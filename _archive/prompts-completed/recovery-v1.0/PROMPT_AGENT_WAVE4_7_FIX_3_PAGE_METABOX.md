# PROMPT AGENT — Wave 4.7.fix.3 PAGE METABOX MIGRATION

> **Scope**: trasferire il contenuto delle Pages WP dalla Theme Options page (Saltelli Settings) a Page metabox attaccate alla rispettiva Page WP. Risolve il feedback Elena: "non è modificabile al pari di altre pagine simili. Il CMS non è usabile in questo modo."
>
> **Branch**: `feat/wave4-7-fix-3-page-metabox` (creare da `main` aggiornato).
> **Version target**: `1.3.9-wave4-7-fix-3-page-metabox`.
> **Sessione**: una sola Claude Code, no parallelismo. Orchestratore (chat Claude.ai) **fermo** sui commit del repo finché questa wave non è mergeata.
> **Stima totale**: 180–240 min.
> **Riferimenti**:
> - `WAVE4-7-FIX-2-INVESTIGATION-REPORT.md` (uploads orchestratore — pattern già verificati)
> - Feedback Elena 2026-05-08: "https://staging.studiolegalesaltelli.it/chi-siamo/ non è modificabile al pari di altre pagine simili. Il Cms non è usabile in questo modo"
> - CLAUDE.md § "Workflow rules" + § "Hard constraints"

---

## Premise (perché questa wave esiste)

La Wave 4.7.fix.2 ha risolto il bug `studio_body` ma ha introdotto un'asimmetria UX: per editare il contenuto di una Page WP (es. `/chi-siamo/`), Elena deve aprire un pannello globale separato (Saltelli Settings → Hub Pages) invece di modificare la Page direttamente. Questo rompe il modello mentale WordPress standard ("modifica pagina = modifica contenuto") e Elena ha esplicitamente respinto il workflow.

Il fix è **ribaltare il pattern**: i field SCF che descrivono il contenuto di una Page WP devono essere agganciati a quella Page tramite location rules `page == <ID>` (metabox post-attached), non `options_page == saltelli-settings` (Theme Options globali).

Cosa **resta in Theme Options** (legittimo perché è configurazione globale, non contenuto di pagina): Brand, Studio Info, Mappa, Footer, Social, CTA Defaults, Footer Aree (tier-1), Archive Headers (perché archive CPT non hanno una Page WP corrispondente).

Cosa **migra a Page metabox**: tutti i field che descrivono il contenuto di una specifica pagina del sito.

---

## 0. Pre-flight (15 min, OBBLIGATORIO)

1. Leggi nell'ordine:
   - `CLAUDE.md` (single source of truth, sezioni "Hard constraints", "Workflow rules", "Versioning policy")
   - `.claude/knowledge/project-context.json`
   - `docs/ARCHITECTURE.md` (theme + ACF schema mapping)
   - `docs/EDITOR-HANDOFF.md` v3.0 (manuale editoriale corrente)
   - Investigation report (uploads): `WAVE4-7-FIX-2-INVESTIGATION-REPORT.md`
   - `inc/seed-theme-options.php` (idempotency pattern)
   - `inc/helpers.php` (`saltelli_option`, `saltelli_field`, helper helper helper)
   - `acf-json/group_theme_options_v1.json` (80 field, 13 tab)

2. Verifica stato:
   ```sh
   git fetch origin main
   git status                    # working tree pulito
   git log --oneline -3          # atteso: 2bbab24 (orchestrator update) → 7f5c25f (merge fix.2) → c5392f2 (P5)
   grep "SALTELLI_THEME_VERSION" wp-content/themes/saltelli/functions.php
   # atteso: 1.3.8-wave4-7-fix-2-true-fix
   ```

3. Crea branch dedicato:
   ```sh
   git checkout -b feat/wave4-7-fix-3-page-metabox
   mkdir -p .claude/knowledge/audits/wave4-7-fix-3-page-metabox
   ```

4. **Backup DB staging PRIMA di qualsiasi migration script** (Phase 3 fa migration di wp_options → wp_postmeta, irreversibile senza backup):
   ```sh
   ssh deploy@178.62.207.50 "cd ~/backups && \
     mkdir -p wave4-7-fix-3-pre-migration && \
     cd wave4-7-fix-3-pre-migration && \
     sudo -u www-data wp db export db-pre-migration-$(date +%Y%m%d-%H%M).sql --path=/var/www/saltelli && \
     ls -lh"
   ```

5. Conferma in chat: branch creato, audit dir creata, DB backup confermato, version corrente, prosegui Phase 1.

---

## Phase 1 — Discovery: mappa Pages WP ↔ SCF field reads (30–40 min)

Obiettivo: produrre una matrice precisa di **quale Page WP contiene quale field di Theme Options**, distinguendo:
- Field che descrivono contenuto di una Page WP specifica → **MIGRARE** a Page metabox.
- Field che sono configurazione globale o usati su pagine senza Page WP corrispondente → **RESTANO** in Theme Options.

### 1.A — Inventario Pages WP

```sh
wp post list --post_type=page --post_status=publish --fields=ID,post_title,post_name,post_parent --format=csv \
  --path=/var/www/saltelli > .claude/knowledge/audits/wave4-7-fix-3-page-metabox/01-pages-inventory.csv
```

Aggiungi colonna manuale: `template` (leggi `_wp_page_template` post_meta) e `static_homepage` flag (controlla `wp option get page_on_front --path=...` e `wp option get show_on_front --path=...`).

### 1.B — Mappa template → SCF reads

Per ogni template `*.php` in `wp-content/themes/saltelli/` e `template-parts/`, fai grep di:
- `saltelli_option('` — ogni hit è un field di Theme Options letto
- `get_field('` — ogni hit è un field SCF letto (potrebbe essere già post-attached)

Output: `.claude/knowledge/audits/wave4-7-fix-3-page-metabox/02-template-scf-reads.md` con tabella:

| Template | SCF reads (saltelli_option) | SCF reads (get_field) | Pagina servita |
|---|---|---|---|
| front-page.php | hero_*, studio_*, team_*, casi_*, press_* | (legge da CPT loop) | Homepage `/` |
| page-chi-siamo-hub.php | hub_chisiamo_* | – | `/chi-siamo/` (2822) |
| page-aree-di-pratica-hub.php | hub_aree_*, hub_aree_cluster_* | – | `/aree-di-pratica/` (2812) |
| page-risorse-hub.php | hub_risorse_*, hub_risorse_card_* | – | `/risorse/` (2813) |
| archive-avvocato.php | archive_avvocato_* | – | `/chi-siamo/team/` (NO Page WP) |
| archive-saltelli_caso.php | archive_caso_* | – | `/chi-siamo/casi-rappresentativi/` (NO Page WP) |
| header.php | brand_payoff, whatsapp_message_default, studio_telefono_pubblico | – | (globale) |
| footer.php | colophon_*, footer_*, social_*, footer_tier1_aree | – | (globale) |
| template-parts/trust-bar.php | trust_signal_* | – | (globale) |
| taxonomy-tipo-area.php | taxonomy_tipoarea_* | – | term (NO Page WP) |
| ... | ... | ... | ... |

### 1.C — Decision matrix

Per ogni gruppo di field di Theme Options, classifica:

| Tab Theme Options | Field group | Letto da | Pagina servita | Decisione |
|---|---|---|---|---|
| Hero Homepage | hero_* | front-page.php | `/` Homepage | **MIGRA** → metabox Page Homepage |
| Studio Section | studio_body, studio_* | front-page.php | `/` Homepage | **MIGRA** → metabox Page Homepage |
| Team & Casi | team_*, casi_* | front-page.php | `/` Homepage | **MIGRA** → metabox Page Homepage |
| Press Homepage | press_* | front-page.php | `/` Homepage | **MIGRA** → metabox Page Homepage |
| Hub Pages (chi-siamo) | hub_chisiamo_* | page-chi-siamo-hub.php | `/chi-siamo/` (2822) | **MIGRA** → metabox Page 2822 |
| Hub Pages (aree-di-pratica) | hub_aree_*, hub_aree_cluster_* | page-aree-di-pratica-hub.php | `/aree-di-pratica/` (2812) | **MIGRA** → metabox Page 2812 |
| Hub Pages (risorse) | hub_risorse_*, hub_risorse_card_* | page-risorse-hub.php | `/risorse/` (2813) | **MIGRA** → metabox Page 2813 |
| Archive Headers (avvocato) | archive_avvocato_* | archive-avvocato.php | `/chi-siamo/team/` (NO Page) | **RESTA** in Theme Options + UX polish |
| Archive Headers (caso) | archive_caso_* | archive-saltelli_caso.php | `/chi-siamo/casi-rappresentativi/` (NO Page) | **RESTA** in Theme Options + UX polish |
| Brand | brand_*, whatsapp_message_default | header.php, multiple | (globale) | **RESTA** |
| Studio Info | studio_telefono_*, studio_email, studio_indirizzo, ... | multiple | (globale) | **RESTA** |
| Mappa | mappa_* | template-parts | (globale) | **RESTA** |
| Footer | colophon_*, footer_newsletter_* | footer.php | (globale) | **RESTA** |
| Social | social_* | footer.php | (globale) | **RESTA** |
| CTA Defaults | cta_default_* | 19 templates | (globale) | **RESTA** |
| Footer Aree | footer_tier1_aree (repeater 3) | footer.php | (globale) | **RESTA** |

Output finale: `.claude/knowledge/audits/wave4-7-fix-3-page-metabox/03-decision-matrix.md`

**Decisione autonoma autorizzata**: se durante discovery emerge che un field "borderline" (es. usato sia nella homepage sia globalmente) richiede splitting, documenta in 03-decision-matrix.md la tua decisione (preferenza: split + duplicate keys evitati = un solo posto canonico, preferibilmente Page metabox; usare helper fallback nel template per leggere prima da page meta poi da option).

### 1.D — Identifica static homepage Page

WordPress static front-page → `wp option get page_on_front --path=...`. Probabile valore: una Page WP con slug tipo `home`, `homepage`, o `front`. Se NON esiste, crearne una nuova in Phase 2 (Page slug `homepage`, status published, template default = front-page.php verrà comunque caricato via `is_front_page()`).

Importante: la creazione di una Page "Homepage" è solo per dare un punto di edit admin a Elena. Il template `front-page.php` continua a essere risolto via WP template hierarchy quando `is_front_page()` è true. Non serve cambiare nulla in `front-page.php` se non lo specifico read SCF.

### 1.E — Commit Phase 1

```sh
git add .claude/knowledge/audits/wave4-7-fix-3-page-metabox/
git commit -m "wave4-7-fix-3 P1: discovery — Pages WP × SCF reads decision matrix

- 01-pages-inventory.csv: lista Pages WP attuali + template
- 02-template-scf-reads.md: mappa template → field SCF letti
- 03-decision-matrix.md: classifica MIGRA / RESTA per ogni tab Theme Options"
```

---

## Phase 2 — SCF field group split + location rules (60–80 min)

### 2.A — Crea nuovi SCF group files (split da group_theme_options_v1.json)

Per ogni gruppo di field MIGRATI in Phase 1.C, crea un file `group_<name>_v1.json` in `wp-content/themes/saltelli/acf-json/`.

**File attesi:**

1. **`group_page_homepage_v1.json`** — location rule: `param=page, value=<homepage_page_id>`
   - Tab "Hero Homepage" + tutti hero_* field
   - Tab "Studio Section" + studio_body, studio_intro, studio_eyebrow, studio_titolo, ...
   - Tab "Team & Casi" + team_titolo, team_eyebrow, casi_titolo, casi_intro, ...
   - Tab "Press Homepage" + press_outlets repeater, press_titolo, ...

2. **`group_page_chi_siamo_v1.json`** — location rule: `param=page, value=2822`
   - Tab "Contenuto pagina" + hub_chisiamo_eyebrow, hub_chisiamo_h1_main, hub_chisiamo_h1_emphasis, hub_chisiamo_intro
   - (più qualsiasi altro field già in Theme Options sotto "Hub Pages" sotto-section "Chi Siamo")

3. **`group_page_aree_di_pratica_v1.json`** — location rule: `param=page, value=2812`
   - Tab "Contenuto pagina" + hub_aree_eyebrow, hub_aree_h1_main, hub_aree_h1_emphasis, hub_aree_intro
   - hub_aree_cluster_privati_label/desc, hub_aree_cluster_imprese_label/desc, hub_aree_cluster_contenzioso_label/desc, hub_aree_cluster_altri_label/desc (4 cluster cards)

4. **`group_page_risorse_v1.json`** — location rule: `param=page, value=2813`
   - Tab "Contenuto pagina" + hub_risorse_eyebrow, hub_risorse_h1_main, hub_risorse_h1_emphasis, hub_risorse_intro
   - hub_risorse_card_1_label/desc/url, _2_, _3_, _4_ (4 resource cards)

**IMPORTANTE — preserva field key**: ogni field deve mantenere la **stessa `key`** (`field_xxx`) della versione in `group_theme_options_v1.json`. Questo permette che il valore già seedato sopravviva indipendentemente dal group dove vive (SCF salva valori indicizzati dalla key, non dal group). Cambia solo `name` (il meta_key effettivo) **NO**: anche `name` deve restare invariato (vedi Phase 3 migration).

**Cosa cambia:**
- `key` → INVARIATO (es. `field_studio_body`)
- `name` → INVARIATO (es. `studio_body`)
- `parent` → cambia (riferito al nuovo group key)
- `type`, `label`, `instructions`, `default_value`, `wrapper`, `conditional_logic` → INVARIATI
- Se il field era figlio di un tab → assegnalo al nuovo tab nel nuovo group

### 2.B — Cleanup `group_theme_options_v1.json`

Rimuovi dalla `fields` array tutti i field migrati + i tab vuoti corrispondenti.

Atteso post-cleanup:
- Tab restanti: Brand, Studio Info, Mappa, Footer, Social, CTA Defaults, Footer Aree, Archive Headers (8 tab, ~40 field)
- Tab eliminati: Hero Homepage, Studio Section, Team & Casi, Press Homepage, Hub Pages (5 tab)

Verifica struttura JSON valida: `python3 -c "import json; json.load(open('wp-content/themes/saltelli/acf-json/group_theme_options_v1.json'))"`

### 2.C — Update template reads

Per ogni template che legge field migrati, sostituisci `saltelli_option('foo')` con la lettura corretta da page meta.

**Pattern raccomandato — helper aggiornato in `inc/helpers.php`:**

```php
/**
 * Read SCF field from current page (post_meta) or fall back to Theme Options.
 * Use this for fields migrated from Theme Options to Page metabox in Wave 4.7.fix.3.
 *
 * @param string $field Field name (es. 'studio_body')
 * @param mixed $default Fallback if not set anywhere
 * @param int|null $page_id Specific Page ID, default current queried page
 * @return mixed
 */
function saltelli_page_field($field, $default = '', $page_id = null) {
    if (!function_exists('get_field')) return $default;

    if ($page_id === null) {
        $page_id = (is_front_page() && get_option('show_on_front') === 'page')
            ? (int) get_option('page_on_front')
            : get_queried_object_id();
    }

    if (!$page_id) return $default;

    $val = get_field($field, $page_id);
    if ($val !== '' && $val !== null && $val !== false) return $val;

    // Fallback to Theme Options (legacy compat during transition)
    if (function_exists('saltelli_option')) {
        return saltelli_option($field, $default);
    }

    return $default;
}
```

**Refactor template:**
- `front-page.php`: ogni `saltelli_option('hero_*' | 'studio_*' | 'team_*' | 'casi_*' | 'press_*')` → `saltelli_page_field('hero_*' | ...)`
- `page-chi-siamo-hub.php`: `saltelli_option('hub_chisiamo_*')` → `saltelli_page_field('hub_chisiamo_*')`
- `page-aree-di-pratica-hub.php`: idem per `hub_aree_*`
- `page-risorse-hub.php`: idem per `hub_risorse_*`

**NON toccare:**
- `header.php`, `footer.php`, `template-parts/trust-bar.php`, `archive-*.php`, `taxonomy-*.php`: continuano a usare `saltelli_option(...)` perché leggono field globali / archive / taxonomy.

### 2.D — Smoke test pre-migration data

Prima di fare data migration (Phase 3), verifica che la struttura sia valida:

1. Sync staging: rsync template + acf-json a `/var/www/saltelli/wp-content/themes/saltelli/`.
2. WP Admin → Pagine → Chi Siamo → Modifica → **deve apparire metabox "Contenuto pagina"** con i 4 field hub_chisiamo_*.
3. Editor metabox vuoto (perché dati ancora in wp_options): è normale a questo punto.
4. Save (anche senza modifiche): non rompe nulla.
5. WP Admin → Saltelli Settings → tab "Hub Pages" → **NON DEVE PIÙ ESISTERE** (rimossa).
6. Frontend `/chi-siamo/`: il fallback `saltelli_page_field` legge da Theme Options (legacy compat) → contenuto INVARIATO durante migration. ✓

### 2.E — Commit Phase 2

```sh
git add -A
git commit -m "wave4-7-fix-3 P2: SCF field group split — Page metabox location rules

- New: group_page_homepage_v1.json (location: page == \$homepage_id, 4 tab Hero/Studio/Team&Casi/Press)
- New: group_page_chi_siamo_v1.json (location: page == 2822, 1 tab Contenuto pagina)
- New: group_page_aree_di_pratica_v1.json (location: page == 2812, 1 tab + 4 cluster cards)
- New: group_page_risorse_v1.json (location: page == 2813, 1 tab + 4 resource cards)
- Modified: group_theme_options_v1.json (5 tab eliminate: Hero/Studio/Team/Press/HubPages, 8 tab restano: Brand/StudioInfo/Mappa/Footer/Social/CTA/FooterAree/ArchiveHeaders)
- New helper: saltelli_page_field() in inc/helpers.php (page meta read with Theme Options fallback for transition)
- Refactor templates: front-page.php + page-chi-siamo-hub.php + page-aree-di-pratica-hub.php + page-risorse-hub.php to use saltelli_page_field()

Field key + name preserved across split (data continuity)."
```

---

## Phase 3 — Data migration script (30–40 min)

### 3.A — Migration script `inc/migrations/wave4-7-fix-3-options-to-postmeta.php`

Crea PHP script idempotente che:
1. Legge la decision matrix di Phase 1.C (lista field da migrare).
2. Per ogni field:
   - Legge `wp_options` chiave `options_<field>`.
   - Scrive `wp_postmeta` chiave `<field>` per il Page ID corretto.
   - Scrive `wp_postmeta` chiave `_<field>` con la SCF field key (richiesto da SCF per field reference).
   - **NON cancella ancora** la chiave wp_options (lasciata per fallback transition; cleanup in Phase 4).
3. Logga tutto in `.claude/knowledge/audits/wave4-7-fix-3-page-metabox/04-migration-log.md`.
4. Idempotency: ri-eseguire lo script non duplica meta + non sovrascrive se postmeta già presente con valore non-empty.
5. Backup prima: `wp option get options_<field>` → log file (rollback source).

**Sample structure:**

```php
<?php
/**
 * Wave 4.7.fix.3 — Migrate SCF data from wp_options to wp_postmeta
 * Idempotent: safe to run multiple times.
 */
defined('ABSPATH') || exit;

if (!defined('WP_CLI') || !WP_CLI) {
    return; // run only via wp eval-file
}

$migrations = [
    // [option_key, page_id, postmeta_key, scf_field_key]
    ['options_studio_body', /* HOMEPAGE_ID */, 'studio_body', 'field_studio_body'],
    ['options_studio_eyebrow', /* HOMEPAGE_ID */, 'studio_eyebrow', 'field_studio_eyebrow'],
    // ... full list from decision matrix Phase 1.C
    ['options_hub_chisiamo_eyebrow', 2822, 'hub_chisiamo_eyebrow', 'field_hub_chisiamo_eyebrow'],
    ['options_hub_chisiamo_h1_main', 2822, 'hub_chisiamo_h1_main', 'field_hub_chisiamo_h1_main'],
    // ... etc
    ['options_hub_aree_cluster_privati_label', 2812, 'hub_aree_cluster_privati_label', 'field_hub_aree_cluster_privati_label'],
    // ... etc
];

$migrated = 0;
$skipped = 0;
$errors = [];

foreach ($migrations as [$opt_key, $page_id, $meta_key, $scf_key]) {
    $opt_value = get_option($opt_key, null);
    if ($opt_value === null || $opt_value === '') {
        $skipped++; continue;
    }
    if (!$page_id) {
        $errors[] = "Missing page_id for $opt_key";
        continue;
    }
    $existing = get_post_meta($page_id, $meta_key, true);
    if ($existing !== '' && $existing !== null && $existing !== false) {
        $skipped++; continue; // idempotency — don't overwrite
    }
    update_post_meta($page_id, $meta_key, $opt_value);
    update_post_meta($page_id, '_' . $meta_key, $scf_key);
    $migrated++;
    WP_CLI::log("✓ Migrated $opt_key → page_id $page_id post_meta $meta_key");
}

WP_CLI::success("Migration complete: $migrated migrated, $skipped skipped (already present or empty), " . count($errors) . " errors");
foreach ($errors as $err) WP_CLI::warning($err);
```

### 3.B — Run migration su staging

```sh
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp eval-file wp-content/themes/saltelli/inc/migrations/wave4-7-fix-3-options-to-postmeta.php --path=/var/www/saltelli"
```

Output atteso: lista field migrati + count successi/skip/errori.

### 3.C — Verify migration

```sh
# Spot check: 1-2 field per ogni Page migrata
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp post meta get 2822 hub_chisiamo_intro --path=/var/www/saltelli | head -c 200 && \
  sudo -u www-data wp post meta get 2812 hub_aree_intro --path=/var/www/saltelli | head -c 200 && \
  sudo -u www-data wp post meta get \$(wp option get page_on_front --path=/var/www/saltelli) studio_body --path=/var/www/saltelli | head -c 200"
```

Atteso: ognuno restituisce il contenuto editoriale (non empty).

### 3.D — Smoke test frontend post-migration

- `/`: studio body + hero + team + press → tutti VISIBILI INVARIATI
- `/chi-siamo/`: hub copy → INVARIATO
- `/aree-di-pratica/`: 4 cluster cards → INVARIATE
- `/risorse/`: 4 resource cards → INVARIATE
- WP Admin → Pagine → Chi Siamo → metabox **POPOLATA** con contenuti migrati ✓
- WP Admin → Pagine → Homepage → metabox 4 tab popolate ✓

### 3.E — Commit Phase 3

```sh
git add -A
git commit -m "wave4-7-fix-3 P3: data migration script wp_options → wp_postmeta

- inc/migrations/wave4-7-fix-3-options-to-postmeta.php (idempotent)
- Migrates ~40 SCF field values from Theme Options to Page metabox
- Migration log: .claude/knowledge/audits/wave4-7-fix-3-page-metabox/04-migration-log.md
- Backup pre-migration: ~/backups/wave4-7-fix-3-pre-migration/ on droplet
- Old wp_options entries preserved (cleanup deferred to Phase 4 after acceptance)

Smoke test: 4 Pages WP have populated metaboxes, frontend INVARIATO durante transizione."
```

---

## Phase 4 — Theme Options cleanup + UX polish (40–50 min)

### 4.A — Theme Options legacy keys cleanup

Dopo conferma stable migration (Phase 3.D smoke test pass), rimuovi le chiavi `options_<field>` migrate via WP-CLI:

```sh
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  for key in options_studio_body options_studio_eyebrow options_hub_chisiamo_intro options_hub_aree_cluster_privati_label /* ... full list ... */; do \
    sudo -u www-data wp option delete \$key --path=/var/www/saltelli; \
  done"
```

Update `inc/seed-theme-options.php`:
- Rimuovere le entry per i field migrati.
- Aggiungere TODO comment: "Field migrated to Page metabox in Wave 4.7.fix.3, no longer seedable from Theme Options."

### 4.B — Helper `saltelli_page_field` cleanup

Una volta confermata migration ok, rimuovi il fallback `saltelli_option(...)` da `saltelli_page_field()` (lascia solo il read da page meta + default). Questo elimina il dual-source confusion.

### 4.C — UX polish metabox

Per ogni field group migrato, aggiungi:
- **Tab description** (testo introduttivo subito sotto il tab name) tipo: "Modifica il contenuto della pagina /chi-siamo/. Per modificare il menu, footer, o altre sezioni globali del sito vai a Saltelli Settings."
- **Field instructions** chiare e in italiano: ogni field deve avere un `instructions` non vuoto che spiega in 1-2 frasi cosa fa quel field.
- **Conditional fields**: se ci sono cluster cards / resource cards in repeater, ogni sub-field deve avere instruction chiara (es. "URL della card — formato: /aree-di-pratica/privati/diritto-tributario/").

### 4.D — Theme Options UX polish (per i field rimasti)

Per gli 8 tab restanti in `Saltelli Settings`:
- Reorder logico: Brand → Studio Info → Mappa → Footer → CTA Defaults → Footer Aree → Archive Headers → Social.
- Per la tab "Archive Headers": aggiungi description prominente "Header per le pagine archivio dei CPT (Team /chi-siamo/team/, Casi rappresentativi /chi-siamo/casi-rappresentativi/) — queste pagine non hanno un editor diretto perché sono archivi automatici".
- Page intro per Saltelli Settings (top-of-page notice): "Questa sezione raccoglie le impostazioni globali del sito. Per modificare il contenuto delle pagine, vai a [Pagine] e seleziona la pagina specifica."

### 4.E — Commit Phase 4

```sh
git add -A
git commit -m "wave4-7-fix-3 P4: Theme Options cleanup + UX polish

- Removed legacy wp_options entries for migrated fields (~40 keys)
- saltelli_page_field() simplified: page meta only, no Theme Options fallback
- Tab descriptions on all migrated metaboxes (Italian, Elena-friendly)
- Field instructions on every field (italian, 1-2 sentences each)
- Theme Options reorder + page intro notice + Archive Headers context"
```

---

## Phase 5 — Documentation + version bump + final QA (20–30 min)

### 5.A — EDITOR-HANDOFF v3.0 → v4.0

Update `docs/EDITOR-HANDOFF.md`:

- Bump version → v4.0 in front-matter
- **§1 Modello mentale rivisto** (riscrivi):
  > "Quasi tutto ciò che è specifico di una pagina si modifica dalla pagina stessa: WP Admin → Pagine → seleziona pagina → Modifica → vedi metabox sotto/a fianco dell'editor. Saltelli Settings serve solo per configurazione globale del sito (footer, header, brand, info di contatto, CTA condivise)."
- **§3.5 Pagina vs Tassonomia vs Archive CPT** — aggiorna admin path matrix per i 15 URL Elena (vedi tabella sotto).
- **§N nuova "Cos'è cambiato dalla v3.0"** — bullet:
  - Hub pages (chi-siamo, aree-di-pratica, risorse) ora editabili dall'editor pagina, non più da Saltelli Settings.
  - Homepage ora editabile da Pagine → Homepage (creata in Wave 4.7.fix.3).
  - Saltelli Settings semplificato: 13 → 8 tab.
  - Modello mentale: "modifica pagina = modifica contenuto pagina" ripristinato.

**Tabella admin path matrix aggiornata** (15 URL Elena):

| URL | Tipo | Admin path | Editabile? |
|---|---|---|---|
| `/` | Page WP "Homepage" | Pagine → Homepage | ✓ tutto via metabox (Hero, Studio, Team & Casi, Press) |
| `/chi-siamo/` | Page WP 2822 | Pagine → Chi Siamo | ✓ post_content + metabox "Contenuto pagina" |
| `/chi-siamo/team/` | Archive CPT avvocato | NESSUNO admin diretto | ⚠️ Solo header H1/intro via Saltelli Settings → Archive Headers |
| `/chi-siamo/casi-rappresentativi/` | Archive CPT saltelli_caso | NESSUNO admin diretto | ⚠️ Solo header H1/intro via Saltelli Settings → Archive Headers |
| `/aree-di-pratica/` | Page WP 2812 | Pagine → Aree di Pratica | ✓ tutto via metabox + 4 cluster cards |
| `/aree-di-pratica/{term}/` | Term tipo-area | Articoli → Tipo area | ✓ description (term meta) |
| `/risorse/` | Page WP 2813 | Pagine → Risorse | ✓ tutto via metabox + 4 resource cards |
| `/risorse/{slug}/` | Page WP | Pagine → {slug} | ✓ post_content |
| `/costi-e-consulenze/` | Page WP 2695 | Pagine → Costi e Consulenze | ✓ post_content |
| `/costi-e-consulenze/{slug}/` | Page WP | Pagine → {slug} | ✓ post_content |
| `/contatti/` | Page WP | Pagine → Contatti | ✓ post_content + metabox (legacy `group_contatti_v1`) |
| `/prenota-appuntamento/` | Page WP 2714 | Pagine → Prenota un appuntamento | ✓ post_content |

### 5.B — CLAUDE.md update

Update sezioni:
- Current state header → `v1.3.9-wave4-7-fix-3-page-metabox`
- Last updated → 2026-05-08 (Wave 4.7.fix.3 mergeata: Page metabox migration)
- Tabella "What's done" → aggiungi riga Wave 4.7.fix.3
- Footer last updated string

**ATTENZIONE**: il commit di CLAUDE.md è di pertinenza orchestratore, NON di Code. Code lascia un TODO note in audit log per dire all'orchestratore di committare CLAUDE.md update post-merge.

### 5.C — Bump version

`wp-content/themes/saltelli/functions.php`:
```php
define('SALTELLI_THEME_VERSION', '1.3.9-wave4-7-fix-3-page-metabox');
```

`wp-content/themes/saltelli/style.css`:
```css
Version: 1.3.9-wave4-7-fix-3-page-metabox
```

### 5.D — Final smoke test (cross-page)

Verifica con `curl -sI` + WP Admin:

1. `/` → 200, contenuto invariato
2. `/chi-siamo/` → 200, contenuto invariato
3. `/aree-di-pratica/` → 200, 4 cluster cards visibili
4. `/risorse/` → 200, 4 resource cards visibili
5. Tutti i redirect 301 di Wave 4.7.fix.2 → ancora attivi
6. WP Admin → Pagine → Chi Siamo → Modifica → metabox **POPOLATA** ed editabile
7. Modifica un campo (es. `hub_chisiamo_intro`) → save → frontend riflette ✓
8. WP Admin → Saltelli Settings → 8 tab visibili (no più Hero/Studio/Team/Press/HubPages)
9. WP Admin → Pagine → Homepage → metabox 4 tab Hero/Studio/Team/Press popolate
10. Modifica `studio_body` da Page Homepage → save → frontend riflette ✓
11. EDITOR-HANDOFF v4.0 visibile
12. functions.php SALTELLI_THEME_VERSION = 1.3.9-wave4-7-fix-3-page-metabox

### 5.E — Final audit + REPORT.md

Crea `.claude/knowledge/audits/wave4-7-fix-3-page-metabox/REPORT.md` con:
- Phases completed (1-5)
- File modified count + lines diff
- New SCF group files (5)
- Theme Options tab count change (13 → 8)
- Field migrated count (~40)
- Pages WP affected (4-5: Homepage + 3 hub + verify costi)
- Smoke test passed/failed con dettaglio
- Open items (es. Page Homepage creata? slug? template assignment)
- Rollback procedure (1-shot per ogni phase)
- TODO orchestratore: bump CLAUDE.md post-merge

### 5.F — Push branch

```sh
git add -A
git commit -m "wave4-7-fix-3 P5: bump version to 1.3.9 + EDITOR-HANDOFF v4.0 + final QA report"
git push origin feat/wave4-7-fix-3-page-metabox
```

### 5.G — Output finale per orchestratore

In chat, riporta:
- Branch pushato
- Commits totali (atteso: 5)
- Version corrente
- Pages WP affected (lista con admin path)
- Field migrati count
- SCF group files diff (5 new + 1 modified)
- Smoke test passati/falliti
- Open items / decisioni che richiedono input orchestratore prima del merge
- Tempo effettivo speso
- TODO orchestratore: aggiornamento CLAUDE.md post-merge

---

## Hard rules per questa wave

1. **One-writer-at-a-time**: l'orchestratore (chat Claude.ai) NON committa nulla finché non hai pushato e l'audit è completato.
2. **DB backup OBBLIGATORIO Phase 0** prima di qualsiasi migration data (Phase 3 è irreversibile senza backup).
3. **Idempotency**: la migration script deve essere ri-eseguibile senza duplicare/sovrascrivere postmeta esistenti non-empty.
4. **Field key + name preservati** durante split SCF group (continuità dei dati).
5. **No new dependencies**: niente plugin nuovi.
6. **No design tokens edit**: `tokens.css` non si tocca.
7. **Schema markup invariato**: nessuna modifica a `inc/schema/`.
8. **Yoast coabitation preserved**.
9. **Cache flush dopo ogni cambio non-triviale** (`wp cache flush`).
10. **Versioning monotonic**: 1.3.8 → 1.3.9 (NON 1.3.8.x).
11. **Field migration "all-or-nothing" per group**: se un tab Theme Options viene migrato, TUTTI i suoi field vanno migrati insieme. Non lasciare field orfani in Theme Options dopo aver eliminato il tab.

## Decisione autonoma autorizzata (con report obbligatorio)

- Naming dei nuovi SCF group: scegli convenzione `group_page_<slug>_v1.json` se rispetta consistency con codebase, altrimenti adatta.
- Tab description copy: scrivi tu in italiano, in tono Elena-friendly (no jargon dev). Salva il copy proposto in audit log per orchestratore review.
- Reorder Theme Options 8 tab: applica il senso editoriale che ritieni migliore, motiva in audit log.
- Se durante Phase 1 emerge un field "borderline" (legge sia da page sia da global): split sicuro = mantieni in Theme Options (RESTA) e segnala in 03-decision-matrix.md.

## Tone

Direct, concrete, zero filler. Stile commit usato finora dal progetto.

## Riferimenti

- Wave 4.7.fix.2 audit + REPORT.md (`.claude/knowledge/audits/wave4-7-fix-2-true-fix/`)
- DEC-029 (origin fallback pattern Wave 4.6)
- DEC-039 (Wave 4.7.fix SCF migration)
- DEC-040 (Wave 4.7.fix.1 SCF URL validation)
- Feedback Elena 2026-05-08 (uploads orchestratore)
- `inc/seed-theme-options.php`
- `inc/helpers.php` (`saltelli_option`, `saltelli_field`, nuovo `saltelli_page_field`)
- `acf-json/group_theme_options_v1.json` (split source)
- `CLAUDE.md` § "Workflow rules" + § "Hard constraints"
- `docs/ARCHITECTURE.md`
- `docs/EDITOR-HANDOFF.md` v3.0 (input per v4.0)

---

*Wave 4.7.fix.3 PAGE METABOX MIGRATION · 5 phases · branch `feat/wave4-7-fix-3-page-metabox` · stima 180–240 min · output → orchestratore audit + merge in `main` + bump CLAUDE.md.*
