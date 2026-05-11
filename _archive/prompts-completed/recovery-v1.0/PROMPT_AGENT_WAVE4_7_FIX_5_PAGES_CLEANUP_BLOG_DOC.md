# PROMPT AGENT — Wave 4.7.fix.5 PAGES CLEANUP + BLOG DOC + CUSTOMIZER LOCK

> **Scope**: ultima wave di pulizia pre-cut produzione. 3 assi:
> - **A** — Pages legacy cleanup (cancellare 18+ Pages orfane/duplicate che confondono Elena in admin)
> - **B** — Blog editing chiarezza (audit template blog + EDITOR-HANDOFF v6.0 §Blog dedicato — niente refactor codice, solo documentazione)
> - **C** — WP Customizer lock-down per ruolo `editor` (Elena vede pannello con CSS aggiuntivo e si confonde)
>
> **Branch**: `feat/wave4-7-fix-5-pages-cleanup-blog-doc`
> **Version target**: `1.3.11-wave4-7-fix-5-cleanup`
> **Sessione**: una sola Claude Code, no parallelismo. Orchestratore (chat Claude.ai) **fermo** sui commit del repo finché questa wave non è mergeata.
> **Stima totale**: 120–150 min.
> **Riferimenti**:
> - Inventario Pages WP completo (orchestratore 2026-05-08 + 09): 35 Pages totali, ~18 da cancellare
> - Inventario WP users: Elena UID 9 role `editor`, Adsolut Staff UID 8 admin, info@studiolegalesaltelli UID 1 admin
> - Audit template blog (single.php, home.php, archive.php): tutto standard WP + 1 helper custom (saltelli_reading_time)

---

## Premise

Le Wave 4.7.fix.2/3/4 hanno risolto i bug architettturali (studio_body fallback, menu obsoleto, SCF location rules, Gutenberg disable sulle 12 Pages target). Restano 3 attriti residui che impediscono il cut produzione:

1. WP Admin → Pagine mostra 35 Pages, di cui ~18 sono garbage (draft 2019-2025 mai pubblicate, duplicate del slug rename Wave 5 IA refactor) — Elena le vede e si chiede "cosa modifico, cosa cancello, posso toccare?". Confusione + paura di rompere.

2. Il blog editorial è 100% standard WP (Gutenberg + meta sidebar). Elena lo percepisce come "fantasma" perché abituata al pattern SCF metabox delle Pages target, non capisce il flow nativo. Risolvibile con documentazione mirata, niente refactor.

3. Il Customizer (Aspetto → Personalizza) include "CSS aggiuntivo" — Elena lo vede, ha il sospetto di doverci scrivere, e con un colpo accidentale potrebbe rompere il design. Da disabilitare per ruolo `editor`.

---

## 0. Pre-flight (10 min, OBBLIGATORIO)

1. Leggi nell'ordine:
   - `CLAUDE.md` (sezioni "Hard constraints", "Workflow rules", "Lesson learned" 2 punti)
   - `.claude/knowledge/project-context.json`
   - `docs/EDITOR-HANDOFF.md` v5.0 (struttura del manuale, sezioni esistenti, tono)
   - `docs/ARCHITECTURE.md` (Information Architecture aggiornata)
   - `wp-content/themes/saltelli/single.php` (template blog post)
   - `wp-content/themes/saltelli/home.php` (blog archive)
   - `wp-content/themes/saltelli/archive.php` (CPT/category archive)
   - `wp-content/themes/saltelli/inc/helpers.php` (helper `saltelli_reading_time` se esiste — confermare formula word-count / 200)
   - `inc/redirects.php` (per verificare che redirect 301 di slug legacy siano già attivi prima di cancellare le Pages duplicate)

2. Verifica stato:
   ```sh
   git fetch origin main
   git status                       # working tree pulito
   git log --oneline -5
   grep "SALTELLI_THEME_VERSION" wp-content/themes/saltelli/functions.php
   # atteso: 1.3.10-wave4-7-fix-4-strategy-a-full-scf
   ```

3. Crea branch + audit dir:
   ```sh
   git checkout -b feat/wave4-7-fix-5-pages-cleanup-blog-doc
   mkdir -p .claude/knowledge/audits/wave4-7-fix-5-cleanup
   ```

4. **DB backup OBBLIGATORIO** (Asse A trasha + cancella Pages, recuperabili solo da DB backup):
   ```sh
   ssh deploy@178.62.207.50 "cd ~/backups && \
     mkdir -p wave4-7-fix-5-pre-cleanup && \
     cd wave4-7-fix-5-pre-cleanup && \
     sudo -u www-data wp db export db-pre-fix5-$(date +%Y%m%d-%H%M).sql --path=/var/www/saltelli && \
     sudo -u www-data wp post list --post_type=page --post_status=any --format=json --path=/var/www/saltelli > pages-snapshot.json && \
     ls -lh"
   ```

5. Conferma in chat: branch creato, audit dir creata, DB backup confermato, version corrente, prosegui Phase 1.

---

## Phase 1 — Pages discovery + verifica borderline (20 min)

### 1.A — Inventario Pages classificato

Classifica le 35 Pages in 3 categorie esatte. Documenta in `.claude/knowledge/audits/wave4-7-fix-5-cleanup/01-pages-classification.md`:

**KEEP (16 Pages — non toccare):**
17 Home · 2822 Chi Siamo · 2812 Aree di Pratica · 2813 Risorse · 2695 Costi e Consulenze · 23 Contatti · 372 Lavora con noi · 2708 Domande frequenti · 2709 Guide gratuite · 2710 Glossario legale · 2711 Prima consulenza · 2712 Come lavoriamo · 2713 Richiedi un preventivo · 2714 Prenota un appuntamento · 2741 Privacy Policy · 2742 Cookie Policy · 2743 Note legali · 1413 Blog

**DELETE — Publish duplicate (3 Pages, cancellare DOPO verifica redirect):**
- 2811 lo-studio → already rediretto da `inc/redirects.php` a `/chi-siamo/`. Cancellare.
- 2699 risultati → already rediretto a `/chi-siamo/casi-rappresentativi/` (Wave 4.7.fix.2). Cancellare.
- 361 prenota-un-appuntamento → duplicate di 2714 (`prenota-appuntamento`). **VERIFICA** in 1.B prima di cancellare.

**DELETE — Draft orfani (14 Pages, cancellare diretto):**
2241, 1558, 1540, 996, 947 (draft 2024-2025 SEO landing mai pubblicate)
321, 305, 300, 292, 285, 273, 254, 21 (draft 2019-2020 legacy "competenze" pre-CPT)

**VERIFY (1 Page):**
- 356 Conferma → probabile redirect post-submit Contact Form 7. **VERIFICA** in 1.B.

### 1.B — Verifica borderline (361 + 356)

**Per 361 (prenota-un-appuntamento):**
```sh
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  echo '=== menu items che linkano 361 ===' && \
  sudo -u www-data wp db query 'SELECT meta_value FROM wp_postmeta WHERE meta_key=\"_menu_item_object_id\" AND meta_value=361' --path=/var/www/saltelli && \
  echo '=== _menu_item_url contains prenota-un-appuntamento ===' && \
  sudo -u www-data wp db query 'SELECT post_id, meta_value FROM wp_postmeta WHERE meta_key=\"_menu_item_url\" AND meta_value LIKE \"%prenota-un-appuntamento%\"' --path=/var/www/saltelli && \
  echo '=== post_content references ===' && \
  sudo -u www-data wp db query 'SELECT ID, post_title FROM wp_posts WHERE post_content LIKE \"%/prenota-un-appuntamento/%\" AND post_status=\"publish\"' --path=/var/www/saltelli"
```

Decision tree:
- 0 menu items + 0 link in content → safe TRASH
- Menu items o link → mantenere come redirect-only (aggiungi 301 in `inc/redirects.php` `/prenota-un-appuntamento/` → `/prenota-appuntamento/`) poi TRASH

**Per 356 (conferma):**
```sh
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  echo '=== CF7 settings reference 356? ===' && \
  sudo -u www-data wp option get cf7_redirect --format=json --path=/var/www/saltelli 2>/dev/null && \
  sudo -u www-data wp option list --search='*cf7*' --format=json --path=/var/www/saltelli 2>/dev/null && \
  echo '=== referenze post_content ===' && \
  sudo -u www-data wp db query 'SELECT ID, post_title FROM wp_posts WHERE post_content LIKE \"%/conferma/%\" AND post_status=\"publish\"' --path=/var/www/saltelli && \
  echo '=== ultima modifica ===' && \
  sudo -u www-data wp post get 356 --field=post_modified --path=/var/www/saltelli"
```

Se 356 non è referenziato → TRASH. Se è il redirect CF7 → KEEP + segnalare in audit log.

Documenta decisioni in `01-pages-classification.md`.

### 1.C — Commit Phase 1

```sh
git add .claude/knowledge/audits/wave4-7-fix-5-cleanup/
git commit -m "wave4-7-fix-5 P1: Pages discovery + classification + borderline verify

- 01-pages-classification.md: 16 KEEP + 17 DELETE + 0-1 VERIFY (final scope post-1.B verify)
- Borderline verify per 361 (prenota-un-appuntamento) e 356 (conferma):
  - 361 = duplicate di 2714, cancellabile + add redirect 301 se serve
  - 356 = TBD post-verify CF7 reference"
```

---

## Phase 2 — Pages cleanup execution (30–40 min)

### 2.A — Eventuali redirect 301 nuovi da aggiungere

Se Phase 1.B ha rivelato che 361 (`prenota-un-appuntamento`) ha link esterni o menu, aggiungere in `inc/redirects.php`:

```php
'/prenota-un-appuntamento/' => '/prenota-appuntamento/',
```

Sync staging + verify redirect con `curl -sI`.

### 2.B — Cancellazione draft orfani (14 Pages, sicure)

WP `wp post delete` con `--force=false` mette in trash (recoverable per 30 giorni). Idempotente.

```sh
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  for ID in 2241 1558 1540 996 947 321 305 300 292 285 273 254 21; do \
    echo \"=== Trash Page \$ID ===\"; \
    sudo -u www-data wp post delete \$ID --path=/var/www/saltelli; \
  done && \
  sudo -u www-data wp cache flush --path=/var/www/saltelli"
```

### 2.C — Cancellazione publish duplicate (2-3 Pages, post-verify)

```sh
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  for ID in 2811 2699 361; do \
    echo \"=== Trash Page \$ID ===\"; \
    sudo -u www-data wp post delete \$ID --path=/var/www/saltelli; \
  done && \
  sudo -u www-data wp cache flush --path=/var/www/saltelli"
```

(Aggiungi 356 a questa lista solo se Phase 1.B ha confermato safe.)

### 2.D — Verify frontend invariato

Per ogni slug di Page cancellata che ha redirect 301:
```sh
for SLUG in lo-studio risultati prenota-un-appuntamento; do
  echo "=== /$SLUG/ ==="
  curl -sI https://staging.studiolegalesaltelli.it/$SLUG/ | head -3
done
```

Atteso: ognuno 301 + Location target corretto.

### 2.E — Verify admin

WP Admin → Pagine → atteso vedere 16 Pages (anziché 35), tutte ACTIVE.

### 2.F — Commit Phase 2

```sh
git add -A
git commit -m "wave4-7-fix-5 P2: Pages cleanup — 17-18 trashed (14 draft orfani + 3-4 publish duplicate)

- 14 draft cancellate: legacy SEO 2024-2025 + 'competenze' 2019-2020 pre-CPT
- 3 publish duplicate cancellate: lo-studio (2811), risultati (2699), prenota-un-appuntamento (361)
- [Se applicabile] inc/redirects.php aggiunto 301 /prenota-un-appuntamento/ → /prenota-appuntamento/
- [Se applicabile] Page 356 conferma: keep / trash (decisione documentata)
- Backup: DB snapshot + pages-snapshot.json on droplet (Phase 0)
- Frontend smoke test: redirect 301 invariati su 3 slug legacy"
```

---

## Phase 3 — Blog editing chiarezza: audit + UX micro-fix (40–50 min)

### 3.A — Audit completo template blog

Mappa esaustiva di TUTTI gli elementi visibili sul frontend di `single.php`, `home.php`, `archive.php` → sorgente. Documenta in `.claude/knowledge/audits/wave4-7-fix-5-cleanup/02-blog-editing-map.md`:

| Elemento frontend visibile | Template file:line | Sorgente | Editabile da admin? | Dove? |
|---|---|---|---|---|
| H1 titolo post | single.php:? | `the_title()` | Sì | Gutenberg titolo |
| Lede italico (excerpt) | single.php:71 | `get_the_excerpt()` | Sì | Sidebar → Estratto |
| Reading time "3 MIN" | single.php:62 | `saltelli_reading_time()` helper | **NO** | Calcolato da word count |
| Data "13 LUGLIO 2025" | single.php:47 | `get_the_date()` | Sì | Sidebar → Pubblica → data |
| Autore "Avv. Antonia Battista" | single.php:18 | `get_the_author_meta('display_name')` | Sì | Sidebar → Autore |
| Categoria "DIRITTO DI FAMIGLIA..." | (cercare nel template + breadcrumb Yoast) | `get_the_terms()` o Yoast | Sì | Sidebar → Categorie |
| Featured image | (header template) | `the_post_thumbnail()` | Sì | Sidebar → Immagine in evidenza |
| Eyebrow "← EDITORIALE" | (cercare nel template-parts/ oppure single.php) | hardcoded? Helper? | **NO** | (verificare se mai cambiabile) |
| Breadcrumb HOME/BLOG/... | (Yoast hook) | Yoast SEO | Sì | Yoast settings → Breadcrumb |
| Content body | single.php:? | `the_content()` | Sì | Gutenberg body |
| Tag list (bottom) | single.php:? | `get_the_tags()` | Sì | Sidebar → Tag |
| Author bio sezione | single.php:113-130 (probabile) | `get_the_author_meta('description')` | Sì | Utenti → seleziona avvocato → bio |

**Output critico**: per ognuno, **link diretto al admin path** che Elena può aprire (es. `/wp-admin/post.php?post=ID&action=edit`).

### 3.B — UX micro-fix amministrativi (opzionale, non blocking)

Se sensato, aggiungere nel WP Admin **sidebar metabox del post type `post`**:
- Notice: "💡 L'estratto qui sotto controlla il LEDE ITALICO sul frontend (paragrafo introduttivo grande sotto il titolo)."
- Notice nel "Estratto" sidebar field con instruzione contestuale.

Implementation: `add_action('post_submitbox_misc_actions')` o hook simile per inserire notice.

File: `inc/admin/post-editor-notices.php` (nuovo, lean).

Include in `functions.php`.

### 3.C — Verifica eyebrow "EDITORIALE"

Cerca dove `EDITORIALE` viene renderizzato:
```sh
grep -rn "EDITORIALE" wp-content/themes/saltelli/ --include="*.php"
```

Identifica se hardcoded come stringa o se viene da una taxonomy/category. Documenta in `02-blog-editing-map.md`. Se è hardcoded e Duccio vuole renderlo editabile (es. "← LONGFORM" per certi articoli), proporre in audit log come Wave futura — NON implementare ora.

### 3.D — Commit Phase 3

```sh
git add -A
git commit -m "wave4-7-fix-5 P3: Blog editing audit + admin sidebar notices

- 02-blog-editing-map.md: ogni elemento frontend → admin path + edit location
- inc/admin/post-editor-notices.php: sidebar contextual notices for post editor
  - Excerpt field: 'controlla il lede italico sul frontend'
- Eyebrow 'EDITORIALE' hardcoded: documented as out-of-scope this wave"
```

---

## Phase 4 — WP Customizer lock-down per ruolo editor (20–30 min)

### 4.A — Filter `user_has_cap`

In `inc/admin/customizer-lockdown.php` (nuovo file):

```php
<?php
defined('ABSPATH') || exit;

/**
 * Wave 4.7.fix.5 — Customizer lock-down per ruolo editor (Elena)
 *
 * Elena (role 'editor') ha capability 'customize' di default WP. Vede pannello
 * Aspetto → Personalizza con CSS aggiuntivo e si confonde. Lock-down: solo
 * administrator può accedere al Customizer + CSS aggiuntivo.
 */

// Remove customize capability for non-administrator users
add_filter('user_has_cap', function($allcaps, $caps, $args, $user) {
    if (!$user || !($user instanceof WP_User)) return $allcaps;
    if (in_array('administrator', $user->roles, true)) return $allcaps;

    // Strip Customizer + CSS access for non-admin
    foreach (['customize', 'edit_css', 'edit_theme_options'] as $cap) {
        unset($allcaps[$cap]);
    }
    return $allcaps;
}, 10, 4);

// Redirect non-admin trying to access customize.php → dashboard
add_action('load-customize.php', function() {
    if (!current_user_can('administrator')) {
        wp_die(
            __('Non hai i permessi per accedere al Customizer. Contatta l\'amministratore (tech@adsolut.it) se serve modificare design o CSS.'),
            __('Accesso non autorizzato'),
            ['response' => 403, 'back_link' => true]
        );
    }
});

// Hide Aspetto → Personalizza menu item per non-admin
add_action('admin_menu', function() {
    if (!current_user_can('administrator')) {
        remove_submenu_page('themes.php', 'customize.php');
    }
}, 999);
```

Include in `functions.php`:
```php
require_once get_template_directory() . '/inc/admin/customizer-lockdown.php';
```

### 4.B — Smoke test

Login con utente Elena (UID 9, password in `.saltelli-staging-secrets`):
1. WP Admin → Aspetto: il submenu "Personalizza" **NON DEVE essere visibile**
2. Manual GET su `/wp-admin/customize.php`: **403 wp_die page** con messaggio italiano
3. Login con admin (UID 8 Adsolut Staff): Customizer **VISIBILE** e funzionante

### 4.C — Commit Phase 4

```sh
git add -A
git commit -m "wave4-7-fix-5 P4: WP Customizer lock-down per ruolo editor

- inc/admin/customizer-lockdown.php: filter user_has_cap rimuove customize/edit_css/edit_theme_options per non-admin
- Redirect customize.php → 403 wp_die con messaggio italiano (contatto Adsolut)
- Hide submenu Aspetto → Personalizza per non-admin
- Admin (Elena UID 9 role editor) ora non vede più CSS aggiuntivo né Customizer panel"
```

---

## Phase 5 — EDITOR-HANDOFF v6.0 + version bump + final smoke test (30 min)

### 5.A — EDITOR-HANDOFF v5.0 → v6.0

Update `docs/EDITOR-HANDOFF.md`:

- Bump version → v6.0 in front-matter
- **§N nuova "Cos'è cambiato dalla v5.0"**:
  - Pages legacy cleanup: 17-18 Pages cancellate (draft orfani + duplicate slug rename). L'admin "Pagine" ora mostra solo le 16 Pages reali del sito.
  - Customizer lock: editor (Elena) non vede più Aspetto → Personalizza e non può modificare CSS aggiuntivo. Per design/CSS changes contatta tech@adsolut.it.
  - Nuovo §Blog dedicato (sotto)

- **§4 NUOVA — Modificare il blog editoriale** (sezione completa, ~50 righe italian):

  > Il blog del sito (`/risorse/blog/`) funziona con **WordPress standard** — non c'è SCF metabox dedicato come per le Pages. Tutto si fa dall'editor Gutenberg + sidebar a destra.
  >
  > **Creare un nuovo articolo**:
  > 1. WP Admin → Articoli → Aggiungi nuovo
  > 2. Compila il titolo (sarà l'H1 sul frontend)
  > 3. Scrivi il content con Gutenberg
  > 4. Sidebar destra → "Pubblica" → scegli categoria (obbligatoria, viene mostrata nel breadcrumb e nel meta sopra il titolo)
  > 5. Sidebar destra → "Estratto" → scrivi 1-2 frasi (sarà il lede italico grande sotto il titolo)
  > 6. Sidebar destra → "Immagine in evidenza" → carica/scegli immagine (obbligatoria, mostrata nell'hero del post)
  > 7. Sidebar destra → "Autore" → seleziona l'avvocato che firma l'articolo (Antonia Battista, Fabiana Saltelli, Stefano Tedesco, Gabriele Cascone)
  > 8. Pubblica
  >
  > **Cosa è editabile (tabella admin path → frontend element)**: [usare la tabella di Phase 3.A]
  >
  > **Cosa NON è editabile (calcolato automaticamente)**:
  > - Reading time "3 MIN": calcolato dal numero di parole del content (200 parole/minuto)
  > - Eyebrow "← EDITORIALE": link "torna al blog" hardcoded nel template
  > - Breadcrumb HOME/BLOG/...: generato da Yoast SEO

- **§3.7 NUOVA — Pagine WP del sito (lista canonica)**:
  Lista delle 16 Pages legittime con admin path + scopo. Includere screenshot mockup se utile (no, skip).

### 5.B — Version bump

`functions.php` + `style.css`:
```
1.3.11-wave4-7-fix-5-cleanup
```

### 5.C — Final smoke test (8 punti)

1. WP Admin → Pagine: solo 16 Pages visibili ✓
2. Frontend /chi-siamo/, /aree-di-pratica/, /risorse/, ... (i 16 URL canonici): tutti 200 ✓
3. Redirect 301 legacy ancora attivi: `/lo-studio/`, `/risultati/`, `/competenze/`, `/faq/`, `/costi/`, ecc. ✓
4. (Se aggiunta) Redirect `/prenota-un-appuntamento/` → 301 ✓
5. Login Elena UID 9: Aspetto → Personalizza NON visibile, customize.php → 403 ✓
6. Login admin UID 8: Customizer visibile e funzionante ✓
7. Articoli → Aggiungi nuovo: sidebar contextual notice "lede italico" visibile ✓
8. Frontend blog `/risorse/blog/` + singolo post: invariato ✓

### 5.D — REPORT.md finale

`.claude/knowledge/audits/wave4-7-fix-5-cleanup/REPORT.md`:
- 5 phases status
- Files changed count + diff stats
- Pages classification finale (16 KEEP + 18 deleted)
- Customizer lock effective per editor role
- Blog audit deliverable
- Smoke test passed/failed (8 punti)
- TODO orchestratore: bump CLAUDE.md (version + tabella row Wave 4.7.fix.5 + Information Architecture update con 16 Pages canoniche)
- Rollback procedure per phase

### 5.E — Push branch

```sh
git add -A
git commit -m "wave4-7-fix-5 P5: version 1.3.11 + EDITOR-HANDOFF v6.0 + final QA report"
git push origin feat/wave4-7-fix-5-pages-cleanup-blog-doc
```

### 5.F — Output finale per orchestratore

In chat:
- Branch pushato
- Commits totali (atteso: 5)
- Version corrente
- Pages deleted count + Pages KEEP final list
- Customizer lock status per role
- Blog audit deliverable path
- Smoke test risultati (8 punti)
- TODO orchestratore: CLAUDE.md update
- Tempo effettivo speso

---

## Hard rules

1. **One-writer-at-a-time**: orchestratore fermo finché branch non pushato.
2. **DB backup OBBLIGATORIO Phase 0** (cancellazioni Page recuperabili solo da DB).
3. **Redirect 301 esistenti preservati**: NON toccare `inc/redirects.php` esistente, solo eventuale add per `/prenota-un-appuntamento/`.
4. **Borderline 356 + 361 verify-first**: cancella solo dopo conferma 1.B che non sono linkate.
5. **No new dependencies**.
6. **No design tokens edit**.
7. **No schema markup change**.
8. **Versioning monotonic**: 1.3.10 → 1.3.11.
9. **Admin-side smoke test obbligatorio** (lesson learned Wave 4.7.fix.4 ora in CLAUDE.md): Phase 4 verifica login Elena reale.
10. **OPcache reload** post-edit di `inc/admin/*.php` (lesson learned Wave 4.7.fix.3).

## Decisione autonoma autorizzata

- Page 356 Conferma decision (KEEP / TRASH) basata su Phase 1.B output: documenta motivazione.
- Page 361 prenota-un-appuntamento: se referenziata da menu o link interno, aggiungi redirect 301 prima di trash; se 0 link, trash diretto.
- Wording dei notice sidebar (Phase 3.B) e wp_die Customizer (Phase 4.A): adatta tono Elena-friendly italiano.
- Sezione §Blog EDITOR-HANDOFF v6.0: scrivi in italiano colloquiale (tono manuale "tu" come da §1).

## Tone

Direct, concrete, zero filler.

## Riferimenti

- Wave 4.7.fix.4 audit (`.claude/knowledge/audits/wave4-7-fix-4-strategy-a-full-scf/REPORT.md`)
- Inventario Pages WP completo + WP users (chat orchestratore 2026-05-08/09)
- Feedback Duccio "Vorrei darti un OK ma abbiamo ancora pagine legacy nel CMS che confondono, inoltre anche l'editing del blog è fantasma"
- `inc/redirects.php` (Wave 4.7.fix.2)
- `CLAUDE.md` § "Workflow rules" + 2 Lesson learned
- `docs/EDITOR-HANDOFF.md` v5.0

---

*Wave 4.7.fix.5 PAGES CLEANUP + BLOG DOC + CUSTOMIZER LOCK · 5 phases · branch `feat/wave4-7-fix-5-pages-cleanup-blog-doc` · stima 120–150 min · output → orchestratore audit + merge + bump CLAUDE.md + cut produzione.*
