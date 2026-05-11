# PROMPT AGENT — Wave 4.7.fix.4 STRATEGY A: FULL SCF MIGRATION

> **Scope**: eliminare la dualità `post_content` ↔ SCF metabox per 7 Pages WP rimanenti, raggiungere "una sola sorgente di verità per pagina" (= SCF metabox), bloccare Gutenberg sulle Pages con metabox attached, aggiungere admin shortcuts per archive CPT senza Page WP.
>
> **Branch**: `feat/wave4-7-fix-4-strategy-a-full-scf`
> **Version target**: `1.3.10-wave4-7-fix-4-strategy-a-full-scf`
> **Sessione**: una sola Claude Code, no parallelismo. Orchestratore (chat Claude.ai) **fermo** sui commit del repo finché questa wave non è mergeata.
> **Stima totale**: 240–300 min.
> **Riferimenti**:
> - Feedback Elena 2026-05-08 (chat orchestratore, 2 round): "il CMS non è usabile in questo modo" + "queste sezioni non sono ancora editabili e per esempio la pagina in editor è un'altra sebbene abbia i metabox corretti"
> - Wave 4.7.fix.3 REPORT.md (.claude/knowledge/audits/wave4-7-fix-3-page-metabox/)
> - Decisione orchestratore Strategia A: "tutto SCF, no più post_content" — eliminare la sorgente di verità duplicata, una metabox per Page

---

## Premise (perché questa wave esiste)

La Wave 4.7.fix.3 ha migrato 30 SCF field da Theme Options globali a Page metabox per 4 Pages (Home 17, Chi Siamo 2822, Aree di Pratica 2812, Risorse 2813). Bonifica del `post_content` di queste 4 hub fatta manualmente post-merge dall'orchestratore via WP-CLI (template `page-{slug}-hub.php` non chiama `the_content()`, quindi sicuro).

Ma il sito ha **altre 7 Pages WP con SCF metabox attached + post_content non vuoto** (audit orchestratore 2026-05-08):

| Page | ID | post_content len | SCF group | Template |
|---|---|---|---|---|
| `contatti` | 23 | 900 chars | `group_contatti_v1` | `page.php` (default fallback) |
| `domande-frequenti` | 2708 | 1118 chars | `group_faq_v1` | `template-parts/page-info-shared.php` (probable) |
| `guide-gratuite` | 2709 | 678 chars | `group_info_shared_v1` | `page-info-shared.php` |
| `come-lavoriamo` | 2712 | 1433 chars | `group_info_shared_v1` | `page-info-shared.php` |
| `prima-consulenza` | 2711 | 910 chars | `group_info_shared_v1` | `page-info-shared.php` |
| `lavora-con-noi` | 372 | 1252 chars | `group_info_shared_v1` | `page-info-shared.php` |
| `richiedi-preventivo` | 2713 | 880 chars | `group_info_shared_v1` | `page-info-shared.php` |

Queste 7 Pages hanno `the_content()` chiamata sul frontend (verificato in `page.php:88` + `page-info-shared.php:94` priority-2 fallback). **Bonifica brutale del `post_content` rompe il frontend** — non è zombie, è content live.

Per Elena questa è dualità indistinguibile: vede l'editor Gutenberg pieno + vede metabox SCF popolata, non sa quale dei due controlla cosa. Ha esplicitamente dichiarato "il CMS non è usabile in questo modo".

**Strategia A**: migrare il contenuto del `post_content` di queste 7 Pages dentro field SCF strutturati (creare nuovi field se necessario), refactor del template per leggere SOLO da SCF, bonifica `post_content`, disable Gutenberg per queste 7 Pages + le 4 hub (totale 11 Pages). Una sola sorgente di verità per Page, niente più dualità.

**In aggiunta**: archive CPT (`/chi-siamo/team/`, `/chi-siamo/casi-rappresentativi/`) restano non-editabili come Pages (per design WP), ma serve UX bridge in admin per non lasciare Elena impatatire — admin bar shortcuts dal frontend + admin notices nella tab Archive Headers con link diretti ai CPT items.

---

## 0. Pre-flight (15 min, OBBLIGATORIO)

1. Leggi nell'ordine:
   - `CLAUDE.md` (single source of truth, sezioni "Hard constraints", "Workflow rules", "Versioning policy", "Lesson learned OPcache")
   - `.claude/knowledge/project-context.json`
   - `docs/ARCHITECTURE.md`
   - `docs/EDITOR-HANDOFF.md` v4.0
   - `inc/seed-theme-options.php` (idempotency pattern)
   - `inc/helpers.php` (`saltelli_option`, `saltelli_field`, `saltelli_page_field`)
   - `acf-json/group_contatti_v1.json`, `acf-json/group_info_shared_v1.json`, `acf-json/group_faq_v1.json` (i 3 group da espandere)
   - `template-parts/page-info-shared.php` (template chiave da refactor)
   - `page.php` (template default da refactor o sostituire con template specifici)
   - Wave 4.7.fix.3 REPORT.md (`.claude/knowledge/audits/wave4-7-fix-3-page-metabox/`)
   - Wave 4.7.fix.3 decision matrix (`.claude/knowledge/audits/wave4-7-fix-3-page-metabox/03-decision-matrix.md`)
   - Wave 4.7.fix.3 migration script (`inc/migrations/wave4-7-fix-3-options-to-postmeta.php`) — pattern di riferimento

2. Verifica stato:
   ```sh
   git fetch origin main
   git status                       # working tree pulito
   git log --oneline -5              # atteso HEAD = orchestrator post-merge Wave 4.7.fix.3
   grep "SALTELLI_THEME_VERSION" wp-content/themes/saltelli/functions.php
   # atteso: 1.3.9-wave4-7-fix-3-page-metabox
   ```

3. Crea branch + audit dir:
   ```sh
   git checkout -b feat/wave4-7-fix-4-strategy-a-full-scf
   mkdir -p .claude/knowledge/audits/wave4-7-fix-4-strategy-a-full-scf
   ```

4. **DB backup OBBLIGATORIO** (Phase 3 modifica wp_posts.post_content + wp_postmeta, irreversibile senza backup):
   ```sh
   ssh deploy@178.62.207.50 "cd ~/backups && \
     mkdir -p wave4-7-fix-4-pre-migration && \
     cd wave4-7-fix-4-pre-migration && \
     sudo -u www-data wp db export db-pre-fix4-$(date +%Y%m%d-%H%M).sql --path=/var/www/saltelli && \
     sudo -u www-data wp post list --post_type=page --post_status=publish --format=json --path=/var/www/saltelli > pages-snapshot.json && \
     ls -lh"
   ```

5. Conferma in chat: branch creato, audit dir creata, DB backup confermato, version corrente, prosegui Phase 1.

---

## Phase 1 — Discovery: post_content content audit + SCF mapping (45–60 min)

### 1.A — Estrai post_content delle 7 Pages target

```sh
for ID in 23 2708 2709 2712 2711 372 2713; do
  ssh deploy@178.62.207.50 "sudo -u www-data wp post get $ID --field=post_content --path=/var/www/saltelli" \
    > .claude/knowledge/audits/wave4-7-fix-4-strategy-a-full-scf/raw-page-$ID.html
done
```

Per ognuna: leggi attentamente il content. Identifica:
- Sezioni semantiche (intro, contatti, contattaci, candidatura, faq list, modalità, ecc.)
- Heading structure (H1/H2/H3)
- Liste (ul/ol)
- Link interni/esterni
- Paragrafi prosa
- Block Gutenberg vs HTML legacy

### 1.B — Mappa post_content → SCF field target (CRITICA)

Per ogni Page, decidi mapping concreto. Esempio per `contatti` (ID 23, 900 chars):

| Sezione `post_content` | Tipo | Field SCF target | Esistente o nuovo? |
|---|---|---|---|
| "Hai bisogno di aiuto?" eyebrow | text | `contatti_eyebrow` | NUOVO (verifica `group_contatti_v1`) |
| H1 "Contattaci" | text | `contatti_h1_main` | NUOVO o esistente? |
| H2 "Chiedi qualsiasi cosa. In qualsiasi momento" | text | `contatti_subtitle` | NUOVO o esistente |
| Intro "Siamo situati a Napoli..." | textarea | `contatti_intro` | NUOVO |
| Lista contatti (indirizzo, telefono, ...) | n/a | **già coperto da Studio Info Theme Options** | non duplicare — refactor template per leggere da Studio Info |
| H2 "Si riceve solo su appuntamento" | text | `contatti_appointment_note` | NUOVO |
| H1 "Siamo sempre alla ricerca di nuovi Legali" | text | `contatti_recruiting_h1` | NUOVO |
| Paragrafo + link "Invia candidatura" | text/url | `contatti_recruiting_intro`, `contatti_recruiting_cta_label`, `contatti_recruiting_cta_url` | NUOVO |

**Decisione architetturale chiave**: i dati che sono già in Theme Options (Studio Info: telefono, email, orari, indirizzo) NON vanno duplicati nei field SCF della Page. Il template deve leggerli da Studio Info via `saltelli_option('studio_*')`. Solo il content editoriale (intro, sezioni testuali, recruiting) va in SCF metabox della Page.

### 1.C — Stessa decisione mapping per le altre 6 Pages

Per ognuna di `domande-frequenti` (2708), `guide-gratuite` (2709), `come-lavoriamo` (2712), `prima-consulenza` (2711), `lavora-con-noi` (372), `richiedi-preventivo` (2713):

1. Estrai content
2. Identifica sezioni semantiche
3. Verifica quali field SCF esistono già nei group (`group_info_shared_v1`, `group_faq_v1`)
4. Decidi mapping (esistente vs nuovo)
5. Documenta in `.claude/knowledge/audits/wave4-7-fix-4-strategy-a-full-scf/01-mapping-decisions.md`

**ATTENZIONE — `group_info_shared_v1` ha location rules su 5 page slug**: significa che gli stessi field condividono key/name tra 5 Pages diverse. Per Strategia A serve **splittare** se i field hanno semantiche diverse per Page (es. CTA text per `richiedi-preventivo` ≠ CTA text per `prima-consulenza`), oppure condividere se sono primitivi davvero generici.

Decisione raccomandata: split in 5 group dedicati (`group_page_guide_gratuite_v1`, `group_page_come_lavoriamo_v1`, `group_page_prima_consulenza_v1`, `group_page_lavora_con_noi_v1`, `group_page_richiedi_preventivo_v1`) con field key dedicati per Page. Più verboso ma elimina ambiguità.

### 1.D — Audit template usage

Per ognuna delle 7 Pages, identifica template esatto. WP segue gerarchia:
1. `page-{slug}.php` (es. `page-contatti.php`)
2. `page-{ID}.php` (es. `page-23.php`)
3. `_wp_page_template` post_meta (custom template)
4. `page.php` (default)

Verifica con `wp post meta get $ID _wp_page_template --path=...` per ognuna. Documenta in `02-template-usage.md`.

### 1.E — Audit Page legacy `lo-studio` (ID 2811)

`lo-studio` ha 1 char post_content (whitespace) ma esiste come Page. Slug `lo-studio` è stato rinominato `chi-siamo` in Wave 5 IA refactor. Verifica:
- `lo-studio` Page è ancora linkata da qualche parte?
- C'è redirect 301 da `/lo-studio/` a `/chi-siamo/`?
- È sicuro cancellarla (move to trash) o serve mantenerla per altri reason?

Documenta in `03-orphan-pages.md`. Decisione cancellazione = Phase 5 cleanup opzionale (non blocking).

### 1.F — Commit Phase 1

```sh
git add .claude/knowledge/audits/wave4-7-fix-4-strategy-a-full-scf/
git commit -m "wave4-7-fix-4 P1: discovery — post_content extraction + SCF mapping decisions

- raw-page-{ID}.html × 7: post_content estratti per analisi
- 01-mapping-decisions.md: post_content sezione → SCF field target per ognuna delle 7 Pages
- 02-template-usage.md: template effettivo servente ogni Page (page-{slug}, page-{ID}, custom, fallback)
- 03-orphan-pages.md: status Page lo-studio (2811) post slug rename Wave 5"
```

---

## Phase 2 — SCF field group expansion + new groups (60–75 min)

### 2.A — Espandi `group_contatti_v1.json`

Aggiungi i field nuovi identificati in Phase 1.B per `/contatti/`. Mantieni location rule esistente (`page_slug == contatti`).

Tab structure raccomandata:
- Tab "Header pagina" (eyebrow + H1 + subtitle + intro)
- Tab "Recruiting" (recruiting H1 + intro + CTA label + CTA URL)
- (i field esistenti nel group si riorganizzano in tab semantiche se non lo sono già)

### 2.B — Crea 5 nuovi group splittati da `group_info_shared_v1`

Per ogni Page servita da info-shared:
- `group_page_guide_gratuite_v1.json` (location: `page_slug == guide-gratuite`)
- `group_page_come_lavoriamo_v1.json` (location: `page_slug == come-lavoriamo`)
- `group_page_prima_consulenza_v1.json` (location: `page_slug == prima-consulenza`)
- `group_page_lavora_con_noi_v1.json` (location: `page_slug == lavora-con-noi`)
- `group_page_richiedi_preventivo_v1.json` (location: `page_slug == richiedi-preventivo`)

Field key dedicati per Page (es. `prima_consulenza_intro` invece di `info_shared_intro` generico). Mappa post_content sezioni come da Phase 1.

### 2.C — `group_faq_v1.json` espansione

Aggiungi field SCF per la struttura content di `/risorse/domande-frequenti/`:
- `faq_page_intro` (textarea)
- `faq_page_section_*` per le eventuali sezioni introduttive non-FAQ
- (le FAQ vere come item rimangono CPT `saltelli_faq` separati, già editabili)

### 2.D — `group_info_shared_v1.json` cleanup

Una volta splittato in 5 group dedicati, `group_info_shared_v1.json` resta con field condivisi davvero generici (se ce ne sono), oppure va completamente eliminato se ridondante.

Decisione raccomandata: eliminare `group_info_shared_v1` post-split. Documenta in audit log.

### 2.E — Smoke test SCF post-edit

Sync staging:
```sh
rsync -avz --delete wp-content/themes/saltelli/acf-json/ deploy@178.62.207.50:/var/www/saltelli/wp-content/themes/saltelli/acf-json/
ssh deploy@178.62.207.50 "cd /var/www/saltelli && sudo -u www-data wp cache flush --path=/var/www/saltelli"
```

Verifica WP Admin → Pagine → Contatti (e altre 6) → metabox visibili con i nuovi field (vuoti, popolazione in Phase 3).

### 2.F — Commit Phase 2

```sh
git add -A
git commit -m "wave4-7-fix-4 P2: SCF field group expansion + 5 new dedicated groups

- group_contatti_v1.json: +8 field (header pagina + recruiting tab)
- group_faq_v1.json: +3 field (page intro + sezioni)
- 5 NEW: group_page_{guide_gratuite,come_lavoriamo,prima_consulenza,lavora_con_noi,richiedi_preventivo}_v1.json
- group_info_shared_v1.json: REMOVED (sostituito da 5 group dedicati)
- Field key/name preservati dove esistenti per data continuity"
```

---

## Phase 3 — Data migration script + post_content backup (45–60 min)

### 3.A — Migration script `inc/migrations/wave4-7-fix-4-postcontent-to-scf.php`

Script PHP idempotente che, per ognuna delle 7 Pages:

1. **Backup**: salva `post_content` originale in `wp_postmeta` chiave `_legacy_post_content_backup` (mai sovrascritto se già presente).
2. **Parsing**: estrae le sezioni semantiche dal `post_content` secondo il mapping di Phase 1.B. Per ogni sezione:
   - Identifica heading + paragrafi correlati
   - Pulisce HTML (preserva strong/em/links interni, rimuove block markers Gutenberg residui)
   - Convertice in plain text o HTML pulito a seconda del field type SCF target
3. **Update SCF**: per ogni field SCF target, scrive il valore via `update_post_meta($page_id, $field_name, $value)` + scrive shadow `_$field_name = $field_key`.
4. **Idempotency**: skip se SCF field già popolato non-empty (non sovrascrivere modifiche editoriali fatte post-Wave 4.7.fix.3).
5. **NON svuota** `post_content` ancora — quello succede in Phase 5 dopo template refactor confermato.
6. **Logga tutto** in `.claude/knowledge/audits/wave4-7-fix-4-strategy-a-full-scf/04-migration-log.md`.

**Pattern di riferimento**: `inc/migrations/wave4-7-fix-3-options-to-postmeta.php` (struttura idempotency proven).

### 3.B — Run su staging

```sh
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp eval-file wp-content/themes/saltelli/inc/migrations/wave4-7-fix-4-postcontent-to-scf.php --path=/var/www/saltelli"
```

Output atteso: list di field migrati per ogni Page, count successi/skip/errori.

### 3.C — Verify

Spot check su 2 Pages:
```sh
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp post meta get 23 contatti_intro --path=/var/www/saltelli && \
  sudo -u www-data wp post meta get 2712 come_lavoriamo_intro --path=/var/www/saltelli && \
  sudo -u www-data wp post meta get 23 _legacy_post_content_backup --path=/var/www/saltelli | head -c 200"
```

Atteso: ognuno restituisce content non-empty.

### 3.D — Commit Phase 3

```sh
git add -A
git commit -m "wave4-7-fix-4 P3: data migration script post_content → SCF

- inc/migrations/wave4-7-fix-4-postcontent-to-scf.php (idempotent)
- For 7 Pages (contatti 23, faq 2708, guide 2709, lavoriamo 2712, prima 2711, lavora 372, preventivo 2713):
  - Parse post_content sections semantically
  - Write to SCF field per Phase 1 mapping
  - Backup _legacy_post_content_backup post_meta (recoverable)
  - post_content NOT yet emptied (Phase 5)
- Migration log: .claude/knowledge/audits/wave4-7-fix-4-strategy-a-full-scf/04-migration-log.md
- Backup pre-migration: ~/backups/wave4-7-fix-4-pre-migration/ on droplet"
```

---

## Phase 4 — Template refactor (60–75 min)

### 4.A — Refactor `page.php`

`page.php` line 88 chiama `the_content()`. Decisione raccomandata: **mantenere** `page.php` come fallback per Pages che NON hanno SCF metabox + content (esistono?), MA aggiungere logica condizionale che prioritizza SCF se attached.

Pattern raccomandato:
```php
<?php
// page.php — generic Page template
$has_scf_metabox = saltelli_page_has_scf_content($post->ID);  // helper nuovo
if ($has_scf_metabox) {
    // Render via SCF: ogni Page con metabox ha il suo template render dedicato
    get_template_part('template-parts/page-scf-render', $post->post_name);
} else {
    // Fallback legacy
    the_title('<h1>', '</h1>');
    ?><div class="sl-page__prose"><?php the_content(); ?></div><?php
}
```

Helper `saltelli_page_has_scf_content` in `inc/helpers.php`: ritorna true se la Page ha almeno un field SCF popolato non-empty da uno dei group attached.

### 4.B — Crea template-parts dedicati per ognuna delle 7 Pages

Per ognuna: crea `template-parts/page-scf-render-{slug}.php` con il render specifico. Esempi:

- `template-parts/page-scf-render-contatti.php`: legge `contatti_*` field via `saltelli_field()` e renderizza il layout di `/contatti/` come è oggi visibile (header, sezione contattaci con dati Studio Info, recruiting section)
- `template-parts/page-scf-render-prima-consulenza.php`, ecc.

NB: la struttura HTML+CSS classes (`.sl-page__*`) DEVE restare identica a quella che `the_content()` produceva prima — frontend invariato è hard requirement.

### 4.C — Refactor `template-parts/page-info-shared.php`

Rimuovi il fallback `the_content()` di line 94. Il template ora legge solo da SCF (priority 1). Se SCF è vuoto, renderizza placeholder editoriale o semplicemente skip (decisione: skip silenzioso, no contenuto > rendere lorem ipsum).

### 4.D — Smoke test cross-page (CRITICA)

Per ognuna delle 7 Pages, confronta frontend prima vs dopo:

```sh
# BASELINE (prima): salva snapshot HTML
for SLUG in contatti domande-frequenti guide-gratuite come-lavoriamo prima-consulenza lavora-con-noi richiedi-preventivo; do
  curl -s https://staging.studiolegalesaltelli.it/$SLUG/ > /tmp/baseline-$SLUG.html
done

# DEPLOY refactor (rsync template + helpers)
# ...

# AFTER: salva snapshot post-refactor
for SLUG in contatti domande-frequenti guide-gratuite come-lavoriamo prima-consulenza lavora-con-noi richiedi-preventivo; do
  curl -s https://staging.studiolegalesaltelli.it/$SLUG/ > /tmp/after-$SLUG.html
done

# Diff visuale (ignore comments + whitespace)
for SLUG in contatti domande-frequenti guide-gratuite come-lavoriamo prima-consulenza lavora-con-noi richiedi-preventivo; do
  echo "=== /$SLUG/ diff ===" 
  diff -u <(sed 's/<!--.*-->//g' /tmp/baseline-$SLUG.html | tr -s ' \t\n') <(sed 's/<!--.*-->//g' /tmp/after-$SLUG.html | tr -s ' \t\n') | head -30
done
```

**Atteso: diff vuoto o limitato a metadata invisibili (timestamp, request ID, ecc.)**. Se diff mostra perdita di content visibile, refactor del template di quella Page non è completo — fix prima di proseguire.

### 4.E — OPcache reload obbligatorio post-edit helpers

```sh
ssh deploy@178.62.207.50 "sudo systemctl reload php8.2-fpm"
```

(Lesson learned Wave 4.7.fix.3, vedi CLAUDE.md § "Lesson learned OPcache").

### 4.F — Commit Phase 4

```sh
git add -A
git commit -m "wave4-7-fix-4 P4: template refactor — SCF-first render, the_content() removed

- page.php: conditional render — saltelli_page_has_scf_content() helper, SCF prio + legacy fallback
- 7 NEW template-parts: page-scf-render-{slug}.php per ognuna delle 7 Pages migrate
- template-parts/page-info-shared.php: removed the_content() fallback
- inc/helpers.php: NEW saltelli_page_has_scf_content() helper

Smoke test: 7/7 frontend diff invariato pre/post refactor (HTML markup preserved)"
```

---

## Phase 5 — post_content cleanup + Gutenberg disable + UX polish (45–60 min)

### 5.A — Bonifica post_content delle 7 Pages

**SOLO DOPO Phase 4.D ha confermato frontend invariato**.

```sh
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  for ID in 23 2708 2709 2712 2711 372 2713; do \
    sudo -u www-data wp post update \$ID --post_content='' --path=/var/www/saltelli; \
  done && \
  sudo -u www-data wp cache flush --path=/var/www/saltelli"
```

Backup `_legacy_post_content_backup` post_meta è già stato salvato in Phase 3.A (recoverable).

### 5.B — Disable Gutenberg per 11 Pages target

In `inc/admin/disable-gutenberg-for-scf-pages.php` (nuovo file):

```php
<?php
defined('ABSPATH') || exit;

/**
 * Disable Gutenberg block editor for Pages WP that have SCF metabox attached.
 * Wave 4.7.fix.4 — eliminates the dual-source confusion (post_content vs SCF metabox)
 * by hiding the Gutenberg editor entirely. Editors see only the SCF metabox + a notice.
 */

// Page IDs target (4 hub from Wave 4.7.fix.3 + 7 dual-source Wave 4.7.fix.4)
const SALTELLI_SCF_ONLY_PAGES = [
    17,    // Homepage
    2822,  // Chi Siamo
    2812,  // Aree di Pratica
    2813,  // Risorse
    23,    // Contatti
    2708,  // Domande Frequenti
    2709,  // Guide Gratuite
    2712,  // Come Lavoriamo
    2711,  // Prima Consulenza
    372,   // Lavora con Noi
    2713,  // Richiedi Preventivo
];

add_filter('use_block_editor_for_post', function($use_block_editor, $post) {
    if ($post && in_array($post->ID, SALTELLI_SCF_ONLY_PAGES, true)) {
        return false;  // disable Gutenberg
    }
    return $use_block_editor;
}, 10, 2);

// Hide classic editor visual area for SCF-only Pages — keep only metabox + title
add_action('edit_form_after_title', function($post) {
    if (!in_array($post->ID, SALTELLI_SCF_ONLY_PAGES, true)) return;
    ?>
    <style>
        #postdivrich, #wp-content-editor-container, #ed_toolbar, .wp-editor-tools { display: none !important; }
    </style>
    <div class="notice notice-info inline" style="margin: 20px 0; padding: 15px; border-left: 4px solid #2271b1;">
        <p style="font-size: 14px; margin: 0;">
            <strong>📝 Modifica i field qui sotto</strong> — il contenuto di questa pagina si modifica esclusivamente dai campi SCF qui sotto. L'editor di contenuto classico non è in uso per questa pagina.
        </p>
    </div>
    <?php
});
```

Include in `functions.php`:
```php
require_once get_template_directory() . '/inc/admin/disable-gutenberg-for-scf-pages.php';
```

### 5.C — Admin notices per archive CPT

In `inc/admin/scf-archive-headers-shortcuts.php` (nuovo file):

```php
<?php
defined('ABSPATH') || exit;

/**
 * Add admin shortcuts for archive CPT (avvocato, saltelli_caso) since they
 * have no Page WP corresponding — Elena needs explicit guidance to find
 * the editing entry points.
 */

// Notice in Saltelli Settings → Archive Headers tab
add_action('acf/render_field_settings/type=tab', function($field) {
    if ($field['name'] !== 'tab_archive_headers') return;
    ?>
    <div class="notice notice-info inline" style="margin: 10px 0; padding: 12px;">
        <p><strong>Header per le pagine archivio CPT</strong></p>
        <p>Modifica qui il titolo + intro delle pagine archivio. Per modificare i singoli avvocati o casi rappresentativi che appaiono nella lista, vai a:</p>
        <ul style="margin-left: 20px;">
            <li><a href="<?php echo admin_url('edit.php?post_type=avvocato'); ?>">→ Avvocato</a> (singoli profili visibili in <code>/chi-siamo/team/</code>)</li>
            <li><a href="<?php echo admin_url('edit.php?post_type=saltelli_caso'); ?>">→ Casi rappresentativi</a> (singoli casi visibili in <code>/chi-siamo/casi-rappresentativi/</code>)</li>
        </ul>
    </div>
    <?php
});

// Admin bar "Modifica" su archive CPT frontend → porta a Saltelli Settings tab
add_action('admin_bar_menu', function($wp_admin_bar) {
    if (!is_admin_bar_showing()) return;
    if (is_post_type_archive('avvocato') || is_post_type_archive('saltelli_caso')) {
        $wp_admin_bar->add_node([
            'id' => 'edit',
            'title' => 'Modifica header pagina',
            'href' => admin_url('admin.php?page=saltelli-settings#tab_archive_headers'),
        ]);
    }
}, 100);
```

Include in `functions.php`.

### 5.D — Smoke test admin

1. WP Admin → Pagine → Chi Siamo (2822) → editor Gutenberg HIDDEN, solo metabox SCF + notice "Modifica i field qui sotto"
2. Idem per altre 10 Pages (17, 2812, 2813, 23, 2708, 2709, 2712, 2711, 372, 2713)
3. WP Admin → Pagine → qualunque altra Page (es. legacy `lo-studio` 2811) → Gutenberg ATTIVO (filter non scatta)
4. Frontend `/chi-siamo/team/` (loggato come admin) → admin bar mostra "Modifica header pagina" link
5. Saltelli Settings → tab Archive Headers → notice con shortcuts visibili

### 5.E — Commit Phase 5

```sh
git add -A
git commit -m "wave4-7-fix-4 P5: post_content cleanup + Gutenberg disable + archive shortcuts

- 7 Pages post_content emptied (backup in _legacy_post_content_backup post_meta)
- inc/admin/disable-gutenberg-for-scf-pages.php: filter use_block_editor_for_post for 11 Pages target (4 hub + 7 dual-source)
- inc/admin/scf-archive-headers-shortcuts.php: notice in Saltelli Settings + admin bar 'Modifica' on /chi-siamo/team/ and /casi-rappresentativi/

Elena ora vede solo metabox SCF su Pages target. Archive CPT hanno shortcuts admin chiari."
```

---

## Phase 6 — Documentation + version bump + final QA (30–40 min)

### 6.A — EDITOR-HANDOFF v4.0 → v5.0

Update `docs/EDITOR-HANDOFF.md`:

- Bump version → v5.0 in front-matter
- **Riscrivi §1 Modello mentale** definitivo:
  > "Ogni Page WP del sito si modifica DALLA Page stessa, esclusivamente dai field SCF qui sotto (l'editor Gutenberg è disabilitato sulle Pages con metabox attached). Saltelli Settings serve solo per configurazione globale (footer, header, brand, info di contatto, CTA condivise, header degli archive CPT)."
- **§3.6 Archive CPT — come si edita**: spiegazione dedicata. Per `/chi-siamo/team/` modifichi:
  - Header pagina (titolo, eyebrow, intro): Saltelli Settings → Archive Headers
  - Singoli avvocati: Avvocato → seleziona profilo
  Stesso pattern per `/chi-siamo/casi-rappresentativi/` con Casi rappresentativi.
- **§N nuova "Cos'è cambiato dalla v4.0"**:
  - 7 Pages WP migrate da post_content + metabox dualità a SCF-only (Strategia A)
  - Gutenberg disabled su 11 Pages WP con metabox attached
  - Admin shortcuts per archive CPT (notice + admin bar)
  - Modello mentale: una sola sorgente di verità per Page = SCF metabox

### 6.B — Bump version

`functions.php` + `style.css`:
```
1.3.10-wave4-7-fix-4-strategy-a-full-scf
```

### 6.C — Final smoke test

12 punti totali:

1. `/contatti/` frontend → invariato (markup pre/post diff vuoto)
2. `/risorse/domande-frequenti/` → invariato
3. `/risorse/guide-gratuite/` → invariato
4. `/costi-e-consulenze/come-lavoriamo/` → invariato
5. `/costi-e-consulenze/prima-consulenza/` → invariato
6. `/lavora-con-noi/` → invariato
7. `/costi-e-consulenze/richiedi-preventivo/` → invariato
8. WP Admin → Pagine → Contatti → Gutenberg HIDDEN + metabox SCF popolata + notice visibile
9. WP Admin → Pagine → Chi Siamo → idem (Gutenberg HIDDEN su tutte 11 Pages target)
10. WP Admin → Saltelli Settings → tab Archive Headers → notice con shortcuts CPT visibile
11. Frontend `/chi-siamo/team/` (loggato) → admin bar mostra "Modifica header pagina" → click porta a Saltelli Settings tab
12. WP Admin → Pagine → Lo Studio (2811 legacy) → Gutenberg ATTIVO (filter non scatta su Pages non-target)

### 6.D — REPORT.md finale

`.claude/knowledge/audits/wave4-7-fix-4-strategy-a-full-scf/REPORT.md`:
- 6 phases status
- Files changed count + diff stats
- 7 Pages migrated + 4 Pages confirmed clean
- 11 Pages Gutenberg disabled
- New SCF group files count (5 split + 2 expanded)
- Migration log (count migrated/skipped/errors)
- Smoke test passed/failed (12 punti)
- Open items / known issues
- Rollback procedure per phase
- TODO orchestratore: bump CLAUDE.md (version + Wave 4.7.fix.4 row + lesson learned admin-side smoke test)

### 6.E — Push branch

```sh
git add -A
git commit -m "wave4-7-fix-4 P6: bump version 1.3.10 + EDITOR-HANDOFF v5.0 + final QA report"
git push origin feat/wave4-7-fix-4-strategy-a-full-scf
```

### 6.F — Output finale per orchestratore

In chat:
- Branch pushato
- Commits totali (atteso: 6)
- Version corrente
- Pages WP affected: lista con ID + slug + status (migrate / confirmed clean / Gutenberg disabled)
- SCF group files diff (5 new + 2 expanded + 1 removed)
- Smoke test passati/falliti (12 punti)
- Open items / decisioni che richiedono input orchestratore prima del merge
- Tempo effettivo speso
- TODO orchestratore: bump CLAUDE.md + nota lesson learned admin-side smoke test

---

## Hard rules per questa wave

1. **One-writer-at-a-time**: l'orchestratore (chat Claude.ai) NON committa nulla finché non hai pushato e l'audit è completato.
2. **DB backup OBBLIGATORIO Phase 0** (Phase 5 è irreversibile su post_content senza backup).
3. **Frontend invariato è HARD REQUIREMENT** Phase 4.D — se il diff baseline vs after mostra perdita visibile di content, fix template before continue. NO compromise sul frontend visible to user.
4. **Backup `_legacy_post_content_backup` per ogni Page**: Phase 3.A scrive obbligatorio prima di qualsiasi update.
5. **Idempotency**: tutti gli script Phase 3+5 devono essere ri-eseguibili senza duplicazione/sovrascrittura distruttiva.
6. **Field key + name preservati** dove esistenti per data continuity (Wave 4.7.fix.3 pattern).
7. **No new dependencies**: niente plugin nuovi.
8. **No design tokens edit**: `tokens.css` non si tocca.
9. **Schema markup invariato**: nessuna modifica a `inc/schema/`.
10. **OPcache reload obbligatorio post-edit `inc/helpers.php` o `inc/admin/*`** (lesson learned Wave 4.7.fix.3).
11. **Versioning monotonic**: 1.3.9 → 1.3.10.
12. **Strategia A definitiva**: NO duplicazione field SCF + post_content. Se post_content è ancora referenziato dopo Phase 4, refactor template è incompleto.

## Decisione autonoma autorizzata

- Naming dei nuovi SCF group: `group_page_<slug>_v1.json` (consistency con Wave 4.7.fix.3 naming) o `group_<area>_v1.json` se più semplice — documenta scelta.
- Tab structure in metabox espansi: applica senso editoriale, motiva in audit log.
- Page legacy `lo-studio` (2811): cancellare (`wp post delete`) o lasciare orfana — tua valutazione, documenta in 03-orphan-pages.md.
- Helper `saltelli_page_has_scf_content()`: implementazione concreta (parse field group attached + check valori non-empty) — adatta a quello che serve nei template.
- Mapping post_content → SCF field può richiedere splitting di sezioni che non sono atomiche (es. una sezione complessa con multipli H + lista può diventare 2-3 field SCF). Decidi a livello di Page in Phase 1.B.

## Tone

Direct, concrete, zero filler. Stile commit usato finora dal progetto.

## Riferimenti

- Wave 4.7.fix.3 audit (`.claude/knowledge/audits/wave4-7-fix-3-page-metabox/REPORT.md`)
- Wave 4.7.fix.3 migration script pattern (`inc/migrations/wave4-7-fix-3-options-to-postmeta.php`)
- Feedback Elena 2026-05-08 (chat orchestratore, 2 round)
- Decisione Strategia A orchestratore 2026-05-08
- `inc/seed-theme-options.php` (idempotency reference)
- `inc/helpers.php` (`saltelli_option`, `saltelli_field`, `saltelli_page_field`, NEW `saltelli_page_has_scf_content`)
- `acf-json/group_contatti_v1.json`, `group_info_shared_v1.json`, `group_faq_v1.json`
- `template-parts/page-info-shared.php` (refactor target)
- `page.php` (refactor target)
- `CLAUDE.md` § "Workflow rules" + § "Hard constraints" + § "Lesson learned OPcache"
- `docs/ARCHITECTURE.md`
- `docs/EDITOR-HANDOFF.md` v4.0 (input per v5.0)

---

*Wave 4.7.fix.4 STRATEGY A: FULL SCF MIGRATION · 6 phases · branch `feat/wave4-7-fix-4-strategy-a-full-scf` · stima 240–300 min · output → orchestratore audit + merge in `main` + bump CLAUDE.md + lesson learned admin-side smoke test.*
