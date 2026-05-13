# LESSONS LEARNED — Saltelli WordPress Theme

> Pattern operativi cristallizzati dagli incident del progetto. Spostato da `CLAUDE.md` 2026-05-13 per performance.
> Lettura raccomandata prima di: deploy staging, edit helpers PHP critici, Wave con SCF/Pages/post_content, callback su filter `user_has_cap`/`map_meta_cap`, data migration su CPT.

## 1. OPcache stale dopo edit `inc/helpers.php` (Wave 4.7.fix.3)

PHP-FPM mantiene OPcache su file `inc/helpers.php` e altri `.php` letti hot. Quando si edita un helper su staging via rsync, le modifiche **non sono visibili al frontend immediatamente** finché OPcache non rigenera l'opcode. Sintomo: helper aggiornato sul disco ma frontend continua a usare la versione precedente (smoke test inspiegabilmente fallito post-deploy).

**Mitigazione obbligatoria post-edit di file PHP critici** (helpers, migrations, hooks):

```sh
ssh deploy@178.62.207.50 "sudo systemctl reload php8.2-fpm"
# oppure più chirurgico:
ssh deploy@178.62.207.50 "sudo -u www-data wp eval 'opcache_reset();' --path=/var/www/saltelli"
```

**File trigger** (cache bust raccomandato dopo modifica): `inc/helpers.php`, `inc/cpt-*.php`, `inc/migrations/*.php`, `inc/admin/*.php`, `functions.php`, `inc/redirects.php`.

## 2. Admin-side smoke test per ogni Wave che tocca Pages WP (Wave 4.7.fix.4)

Le Wave 4.7.fix.2 e 4.7.fix.3 hanno fatto smoke test SOLO frontend (`curl` su URL → 200 + content render). Hanno mancato il problema "Elena vede content legacy nel `post_content` che non viene renderizzato sul frontend ma confonde in admin". La Wave 4.7.fix.4 ha richiesto emergency cleanup di una situazione già presente post-fix.3 se solo avessimo controllato in admin.

**Step obbligatorio post-deploy per ogni Wave che tocca SCF field group, template Pages, o `post_content`:**

1. **Frontend smoke test**: `curl -s URL | grep <content>` su ogni Page affetta — verifica content visibile invariato.
2. **Admin-side smoke test**: per ogni Page WP affetta, simulare apertura WP-Admin → Pagine → seleziona Page → Modifica. Descrivere COSA VEDE L'EDITOR (Gutenberg attivo o disabled, `post_content` presente o vuoto, metabox SCF visibili e popolate, notice presenti).

Tool consigliato: WP-CLI `wp eval` con `apply_filters('use_block_editor_for_post', true, $post)` + `get_post($id)->post_content` per simulare admin view, oppure manual login admin se SSO disponibile su staging.

**Falso negativo classico:** assumere che "post_content non renderizzato sul frontend" = "post_content invisibile a Elena". È falso: l'editor admin mostra `post_content` sempre, indipendentemente da cosa fa il template. Lei lo vede, lo modifica, salva, frontend non cambia = perde fiducia nel CMS.

## 3. Mai chiamare cap functions dentro `user_has_cap` / `map_meta_cap` filter (Wave 4.7.fix.5)

Durante Wave 4.7.fix.5 Phase 4 (Customizer lock-down per ruolo editor) la prima versione di `inc/admin/customizer-lockdown.php` aveva, dentro la callback del filtro `user_has_cap`, una chiamata a `is_super_admin($user->ID)` come check ulteriore oltre `in_array('administrator', $user->roles)`. **`is_super_admin()` su single-site fa internamente `$user->has_cap('delete_users')`** → `apply_filters('user_has_cap', ...)` → ri-trigger della stessa callback → `is_super_admin()` → **ricorsione infinita**.

Test successivo con `wp eval 'wp_set_current_user(9); current_user_can("customize")'` (WP-CLI ha `memory_limit = -1`) ha saturato RAM 2GB + swap 2GB → thrashing → nginx/sshd/php-fpm non rispondenti per ~8 min (frontend `000`, SSH "banner exchange timed out"). OOM killer ha terminato php runaway → servizi ripristinati. Nessun reboot.

**Regola tassativa**: dentro callback dei filtri `user_has_cap`, `map_meta_cap`, `user_can` **MAI chiamare**:
- `current_user_can()`, `user_can()` → fanno cap check, ricorsione
- `is_super_admin()` → su single-site fa `has_cap('delete_users')`, ricorsione
- Funzioni che internamente fanno cap check

**Accesso safe ai dati user dentro queste callback**:
- `$user->roles` (array)
- `$user->caps` (array raw)
- `$user->allcaps` (array merged)
- Accesso diretto a property, NON passa dal filtro → no ricorsione

## 4. Design Handoff pre-flight via Agent multi-agentic in parallelo (sequenza Design Handoff 12/12)

Durante la sequenza Design Handoff (12 wave consecutive), l'orchestratore ha applicato un pattern multi-agentic per ridurre dead-time tra wave Code:

```
Wave N (Code esegue, 30 min-3h)
  └─ in parallelo, orchestratore lancia:
     ├─ Agent A — pre-flight Wave N+1 (Explore agent legge JSX + WP template + produce tabella drift in 5-10 min)
     └─ Agent B — audit verify Wave N-1 pushata (read-only check)
```

Quando Code finisce Wave N e pusha:
- Audit Wave N pronto (Agent B output) → orchestratore decide merge/iterate veloce
- Prompt Wave N+1 già pronto (Agent A ha pre-mappato drift, prompt è 80% scritto)

**Risparmio misurato**: ~25% riduzione ETA totale sequenza (~14-19h ridotti a ~12-15h reali). Specialmente effettivo su wave LIGHT (P10 glossario, P12 404) dove pre-flight ha identificato 1-2 fix CSS prima del lancio Code.

**Pattern applicabile**: ogni sequenza multi-wave con scope simile e file disgiunti (es. design refactor multi-template, audit cleanup multi-area). NON applicabile a wave con dipendenze hard tra commit (es. P7 consolidamento data ops che vincolava P8/P11).

**Esempio comando Agent pre-flight standard** (lean prompt template):
```
Sei Explore agent per [path repo]. Pre-flight Wave [N+1] [scope].
READ-ONLY. Output max 700 parole con:
1. JSX structure mapping
2. Drift CSS focus (tabella max 12 righe)
3. SCF reads check
4. Stima severity + ETA
5. Cross-ref phantom (se applicabile)
6. Decisioni open per orchestratore
7. Stima righe CSS/PHP/SCF
```

## 5. Wave 6.0 partial CPT data migration: 3 takeaways critici (2026-05-13)

Migration `post_content` → `body_extended` postmeta su CPT competenza ha rivelato 3 pattern hard:

### 5.1. `apply_filters('the_content', $field_value)` su content legacy pre-Gutenberg può causare WSOD frontend

La filter chain include `do_blocks` + `do_shortcode` + oEmbed handlers che possono fail su:
- HTML literal classic (no `<!-- wp:* -->` markers)
- shortcode legacy non più registrati
- URL embed con timeout sync HTTP

**Mitigazione**: usare `wp_kses_post()` come safer default per content migrato; oppure pre-sanitize pipeline pre-call (strip non-registered shortcode + skip do_blocks per legacy + oEmbed disable temp).

### 5.2. `update_post_meta(ID, 'body_extended', $raw_html)` su SCF wysiwyg field può crashare admin metabox TinyMCE

Quando raw HTML contiene:
- encoding doppio UTF-8
- control chars (`\x00-\x08\x0B\x0C\x0E-\x1F\x7F`)
- script/iframe/object/embed tag
- inline event handlers (`on*=`)

TinyMCE moderno sanitize input ma può fatal su content patologico.

**Mandatory pre-storage**: `wp_kses_post()` + control chars strip + `\r\n|\r` → `\n` normalize.

### 5.3. Migration data ops vs codice ops separate

Data migration via WP-CLI eval (`update_post_meta` + `wp_update_post`) modifica DB staging direct — NO commit codice associato. Quando emerge bug post-migration (es. WSOD), rollback è separato:
- data via `delete_post_meta` + restore from backup postmeta
- codice via `git revert`

Backup `_legacy_post_content_backup` postmeta è MANDATORY per rollback per-post idempotent senza re-deploy codice.

### Pattern raccomandato per future Wave 6.0 v2 (full Strategy A post-cut)

```php
# Sanitize pipeline pre-storage (NON post)
$clean = preg_replace("/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/", "", $post_content);
$clean = str_replace(["\r\n", "\r"], "\n", $clean);
$clean = wp_kses_post($clean);
update_post_meta($id, "body_extended", $clean);

# Template render mantieni wp_kses_post (NO apply_filters('the_content') su content WP-CLI-migrato)
echo wp_kses_post($body_ext);
```
