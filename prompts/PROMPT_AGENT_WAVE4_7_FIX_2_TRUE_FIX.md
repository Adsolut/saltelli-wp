# PROMPT AGENT — Wave 4.7.fix.2 TRUE FIX

> **Scope**: bug Duccio "Studio Section admin vuoto frontend pieno" + caos URL (menu obsoleto + slug rename) + manuale editoriale v3.0 + recurring blocks SCF + hub/archive CPT SCF migration tier-2.
>
> **Branch**: `feat/wave4-7-fix-2-true-fix` (creare da `main` aggiornato).
> **Version target**: `1.3.8-wave4-7-fix-2-true-fix`.
> **Sessione**: una sola Claude Code, no parallelismo. Orchestratore (chat Claude.ai) **fermo** sui commit del repo finché questa wave non è mergeata.
> **Stima totale**: 250–300 min.
> **Riferimento root**: `WAVE4-7-FIX-2-INVESTIGATION-REPORT.md` (uploads orchestratore) + `.claude/knowledge/audits/wave4-7-fix-2-investigation/`.

---

## 0. Pre-flight (15 min, OBBLIGATORIO)

1. Leggi nell'ordine:
   - `CLAUDE.md` (single source of truth, sezioni "Hard constraints", "Workflow rules", "Versioning policy", "Design → Code handoff rule", "Convention summary")
   - `.claude/knowledge/project-context.json`
   - `docs/ARCHITECTURE.md` (theme + ACF schema + WP-Admin↔frontend coupling)
   - `docs/EDITOR-HANDOFF.md` v1.1 (workflow Elena + nota bio_estesa + fase debug)
   - `docs/PRODUCT.md` (ToV "tu", brand voice, anti-references)
   - `inc/seed-theme-options.php` (idempotency pattern Wave 4.7.fix)
   - `inc/helpers.php` (`saltelli_option`, `saltelli_field`, `saltelli_attorney_*`)
   - `acf-json/group_theme_options_v1.json` (53 field, 50 seedabili)
   - File investigation (uploads): `WAVE4-7-FIX-2-INVESTIGATION-REPORT.md`

2. Verifica stato:
   ```sh
   git fetch origin main
   git status                                       # working tree DEVE essere pulito
   git log --oneline -5                             # ultimo commit atteso: 782fce0 (investigation v2)
   grep "SALTELLI_THEME_VERSION" wp-content/themes/saltelli/functions.php
   # atteso: 1.3.7-wave4-7-fix-1-scf-url-validation
   ```

3. Crea branch dedicato:
   ```sh
   git checkout -b feat/wave4-7-fix-2-true-fix
   ```

4. Crea cartella audit logs:
   ```sh
   mkdir -p .claude/knowledge/audits/wave4-7-fix-2-true-fix
   ```

5. Conferma in chat: branch creato, audit dir creata, version corrente, prosegui Phase 1.

---

## Phase 1 — Bug Duccio "Studio Section" + bio_breve validation (30–45 min)

### 1.A — Validazione manuale dei candidati Phase 3b

Per ognuno dei 5 candidati identificati nell'investigation, leggi il file completo e classifica come:
- **EDITORIAL** (bug pattern: fallback è contenuto editoriale che Elena dovrebbe modificare)
- **UX_PLACEHOLDER** (legittimo: fallback decorativo/skip rendering)

Candidati (file:line, var, field):
1. `front-page.php:199` `$studio_body` `studio_body` — atteso EDITORIAL
2. `single-avvocato.php:14` `$ruolo` `ruolo_breve` — atteso UX_PLACEHOLDER
3. `single-avvocato.php:25` `$linkedin` `same_as_linkedin` — atteso UX_PLACEHOLDER
4. `template-parts/page-lo-studio.php:182` `$bio_breve_av` `bio_breve` — **DA VALIDARE** (discrepanza tra investigation report e re-check orchestratore)
5. `archive-avvocato.php:87` `$ruolo` `ruolo_breve` — atteso UX_PLACEHOLDER

Output atteso: `.claude/knowledge/audits/wave4-7-fix-2-true-fix/01-phase3b-validation.md` con tabella file/var/classification/motivazione (≤200 parole).

**Scope concreto P1.B**: solo i field classificati EDITORIAL. Probabile = 1 (`studio_body`); se anche `bio_breve` risulta EDITORIAL, includere in P1.

### 1.B — Migrazione fallback HTML → JSON `default_value`

**Per `studio_body`** (confermato):
1. Leggi `front-page.php:199-207` ed estrai i 3 paragrafi HTML del blocco `else`.
2. Apri `acf-json/group_theme_options_v1.json` (sezione tab "Studio Section"), trova field `studio_body` (type `wysiwyg`), aggiorna `default_value` con i 3 paragrafi (HTML preservato, attributi `class="sl-link"` + `href` preservati).
3. Aggiorna `inc/seed-theme-options.php` per includere `studio_body` nella lista seedable (idempotency: NON sovrascrivere se DB ha valore non-empty diverso da default).
4. Refactor `front-page.php:199-207`:
   ```php
   // Da:
   if ($studio_body) {
       echo wp_kses_post($studio_body);
   } else {
       ?> <p>Lo Studio Saltelli...</p> ... <?php
   }
   // A:
   if ($studio_body) {
       echo wp_kses_post($studio_body);
   }
   // Niente fallback inline. Default JSON garantisce contenuto sempre presente in admin → frontend.
   ```
5. Per ogni altro field EDITORIAL confermato in 1.A: ripeti 1-4.

### 1.C — Reseed staging DB

```sh
wp eval-file inc/seed-theme-options.php --path=/var/www/saltelli  # locale Docker
# oppure su droplet:
ssh deploy@178.62.207.50 "cd /var/www/saltelli && sudo -u www-data wp eval-file wp-content/themes/saltelli/inc/seed-theme-options.php --path=/var/www/saltelli"
```

Verifica:
```sh
wp option get options_studio_body --path=/var/www/saltelli | head -c 200
# atteso: i 3 paragrafi HTML, NON empty
```

### 1.D — Smoke test

1. WP Admin → Saltelli Settings → Studio Section → "Body sezione studio" → field VISIBILE e POPOLATO con i 3 paragrafi.
2. Modifica testo (es. cambia "1999" in "TEST_1999"), salva.
3. `curl -s https://staging.studiolegalesaltelli.it/ | grep TEST_1999` → match.
4. Ripristina testo originale, salva, verifica ripristino.

### 1.E — Commit Phase 1

```sh
git add -A
git commit -m "wave4-7-fix-2 P1: studio_body editorial fallback → JSON default + template refactor

- Migrate hardcoded 3-paragraph fallback from front-page.php else-block to JSON default_value
- Reseed staging DB via inc/seed-theme-options.php
- Refactor front-page.php:199-207: remove inline HTML fallback, rely on JSON default
- Bug fix: Elena ora vede admin field popolato + frontend coerente
- [se applicabile] Same fix for bio_breve

Validation: .claude/knowledge/audits/wave4-7-fix-2-true-fix/01-phase3b-validation.md"
```

---

## Phase 2 — Menu rebuild + slug rename `risultati` + redirect 301 (60–90 min)

### 2.A — Backup menu corrente

```sh
wp menu list --path=/var/www/saltelli --format=json > .claude/knowledge/audits/wave4-7-fix-2-true-fix/02-menu-backup-pre-rebuild.json
wp menu item list "Saltelli Header" --path=/var/www/saltelli --format=json >> .claude/knowledge/audits/wave4-7-fix-2-true-fix/02-menu-backup-pre-rebuild.json
```

### 2.B — Rename slug `risultati` → `casi-rappresentativi` (intent: modificare `has_archive` CPT)

1. Apri `inc/cpt-saltelli-caso.php`, trova `register_post_type('saltelli_caso', [...])`.
2. Cambia `'has_archive' => 'chi-siamo/risultati'` → `'has_archive' => 'chi-siamo/casi-rappresentativi'`.
3. Verifica che `'rewrite' => [...]` non contenga slug duplicate da aggiornare.
4. Flush rewrite rules:
   ```sh
   wp rewrite flush --path=/var/www/saltelli
   ```
5. Test:
   ```sh
   curl -sI https://staging.studiolegalesaltelli.it/chi-siamo/casi-rappresentativi/ | head -1
   # atteso: HTTP/2 200
   curl -sI https://staging.studiolegalesaltelli.it/chi-siamo/risultati/ | head -1
   # atteso: HTTP/2 404 (poi gestiremo con redirect 301)
   ```

### 2.C — Setup redirect 301 legacy URLs

**Decision tree**:
- Se sito ha già Yoast Premium o Redirection plugin → usa quello (admin GUI o WP-CLI).
- Altrimenti, soluzione tema-side via filter `template_redirect` in `inc/redirects.php` (nuovo file).

Se opzione tema-side, crea `inc/redirects.php`:
```php
<?php
/**
 * Wave 5 IA refactor — legacy URL → new IA 301 redirects
 * Wave 4.7.fix.2 P2 — adds /chi-siamo/risultati/ → /chi-siamo/casi-rappresentativi/
 */
defined('ABSPATH') || exit;

add_action('template_redirect', function() {
    $map = [
        '/chi-siamo/risultati/'                 => '/chi-siamo/casi-rappresentativi/',
        '/competenze/'                          => '/aree-di-pratica/',
        '/competenze/'                          => '/aree-di-pratica/', // (duplicate prevention)
        '/faq/'                                 => '/risorse/domande-frequenti/',
        '/costi/'                               => '/costi-e-consulenze/',
        '/tipo-area/privati/'                   => '/aree-di-pratica/privati/',
        '/tipo-area/imprese/'                   => '/aree-di-pratica/imprese/',
        '/tipo-area/contenzioso/'               => '/aree-di-pratica/contenzioso-amministrativo/',
        '/prima-consulenza/'                    => '/costi-e-consulenze/prima-consulenza/',
        '/come-lavoriamo/'                      => '/costi-e-consulenze/come-lavoriamo/',
        '/richiedi-preventivo/'                 => '/costi-e-consulenze/richiedi-preventivo/',
        '/glossario-legale/'                    => '/risorse/glossario-legale/',
        '/guide-gratuite/'                      => '/risorse/guide-gratuite/',
        '/blog/'                                => '/risorse/blog/',
    ];
    $req = trailingslashit(strtok($_SERVER['REQUEST_URI'], '?'));
    if (isset($map[$req])) {
        wp_redirect(home_url($map[$req]), 301);
        exit;
    }
});
```

Includilo in `functions.php`:
```php
require_once get_template_directory() . '/inc/redirects.php';
```

Test ogni redirect con `curl -sI` (atteso HTTP/2 301 + Location: header corretto).

**ATTENZIONE**: prima di committare i redirect, verifica che gli URL `/blog/`, `/competenze/`, `/faq/`, `/costi/` NON siano serviti da Page WP esistenti (in tal caso il redirect non scatta perché WP risponde 200 prima). Se Page esistono, decidi: cancellare Page legacy o lasciare redirect "dormiente".

### 2.D — Menu rebuild

**Strategia**: ricostruire menu primary `Saltelli Header` da zero. Dove possibile usa `type=page` (object_id reference); per term tipo-area usa `type=taxonomy`; `type=custom` SOLO per CPT archive `/chi-siamo/team/` e `/chi-siamo/casi-rappresentativi/` (no Page WP corrispondente).

Pseudo-script (può essere bash + WP-CLI o PHP one-shot):
```sh
# 1. Backup ID menu corrente
OLD_MENU_ID=$(wp menu list --field=term_id --format=csv --path=/var/www/saltelli | head -1)

# 2. Nuovo menu
wp menu create "Saltelli Header" --path=/var/www/saltelli  # oppure rename old + suffix _v1_obsolete
NEW_MENU_ID=$(wp menu list --field=term_id --format=csv --path=/var/www/saltelli | tail -1)

# 3. Item parent (Chi Siamo)
wp menu item add-post $NEW_MENU_ID 2822 --title="Chi Siamo" --path=/var/www/saltelli
# (ripeti per ogni voce: aree-di-pratica 2812, risorse 2813, costi-e-consulenze 2695, contatti N)

# 4. Submenu (--parent-id richiede l'ID dell'item padre, non il post_id)
# Esempio Team:
wp menu item add-custom $NEW_MENU_ID "Il Team" "/chi-siamo/team/" --parent-id=<chi_siamo_item_id> --path=/var/www/saltelli
wp menu item add-custom $NEW_MENU_ID "Risultati" "/chi-siamo/casi-rappresentativi/" --parent-id=<chi_siamo_item_id> --path=/var/www/saltelli

# 5. Submenu Aree di Pratica (term tipo-area)
wp menu item add-term $NEW_MENU_ID tipo-area 992 --parent-id=<aree_item_id> --path=/var/www/saltelli  # privati
# ecc.

# 6. Assegna location primary
wp menu location assign $NEW_MENU_ID primary --path=/var/www/saltelli

# 7. Cancella vecchio menu (o tienilo come backup _obsolete)
wp menu delete $OLD_MENU_ID --path=/var/www/saltelli   # SOLO dopo conferma menu nuovo OK
```

**Mappa menu target** (dal report Sezione C.2, già post-Wave 5 IA refactor):
- Chi Siamo → Page 2822 `/chi-siamo/`
  - Il Team → custom `/chi-siamo/team/`
  - Risultati → custom `/chi-siamo/casi-rappresentativi/`
- Aree di Pratica → Page 2812 `/aree-di-pratica/`
  - Per i Privati → term tipo-area 992
  - Per le Imprese → term tipo-area 993
  - Contenzioso Amministrativo → term tipo-area 994
  - Tutte le aree → Page 2812
- Risorse → Page 2813 `/risorse/`
  - Blog → custom `/risorse/blog/` (se esiste pagina blog) oppure Page blog ID se esiste
  - Domande Frequenti → Page 2708
  - Guide Gratuite → Page 2709
  - Glossario Legale → Page 2710
- Costi e Consulenze → Page 2695
  - Prima Consulenza → Page 2711
  - Come Lavoriamo → Page 2712
  - Richiedi Preventivo → Page 2713
- Contatti → (verifica ID Page contatti)
  - Prenota Appuntamento → Page 2714
  - Dove Siamo → custom `/contatti/#sede`
  - Lavora con Noi → (verifica esistenza Page)

### 2.E — Smoke test menu

1. Frontend: ogni voce menu apre URL atteso (no 404).
2. Click "Risorse" → `/risorse/` (NO `/faq/` legacy).
3. WP Admin → Apparenza → Menu → menu visibile e modificabile da Elena.
4. Submenu rendering desktop + mobile.

### 2.F — Commit Phase 2

```sh
git add -A
git commit -m "wave4-7-fix-2 P2: menu rebuild + risultati→casi-rappresentativi + redirect 301

- has_archive CPT saltelli_caso: chi-siamo/risultati → chi-siamo/casi-rappresentativi
- inc/redirects.php: 14 legacy URL → Wave 5 IA paths (301)
- Menu Saltelli Header rebuilt: type=page references where possible (eliminates type=custom hardcoded URLs)
- Removes 17/22 obsolete URL chain from primary menu
- Backup menu pre-rebuild: .claude/knowledge/audits/wave4-7-fix-2-true-fix/02-menu-backup-pre-rebuild.json

SEO: 14 redirect 301 attivi, validazione URL post-deploy con SEMrush/GSC consigliata."
```

---

## Phase 3 — EDITOR-HANDOFF v3.0 (15 min)

### 3.A — Update doc

Apri `docs/EDITOR-HANDOFF.md`, bump version v1.1 → v3.0 (skip v2.0 perché non rilasciata).

Aggiungi sezione **"Pagina vs Tassonomia vs Archive CPT — dove modificare cosa"** con:

1. Tabella dei 15 URL Elena (dal report Sezione B):

   | URL | Tipo | Admin path | Editabile? |
   |---|---|---|---|
   | `/chi-siamo/` | Page WP 2822 | Pagine → Chi Siamo | ✓ post_content + SCF |
   | `/chi-siamo/team/` | Archive CPT avvocato | NESSUNO admin diretto | ⚠️ H1/intro via SCF tab "Archive Headers" (post-Wave 4.7.fix.2) |
   | `/chi-siamo/casi-rappresentativi/` | Archive CPT saltelli_caso | NESSUNO admin diretto | ⚠️ H1/intro via SCF (idem) |
   | `/aree-di-pratica/` | Page WP 2812 (post_content vuoto) | Pagine → Aree di Pratica | ⚠️ Solo via SCF tab "Hub Pages" |
   | `/aree-di-pratica/{term}/` | Term tipo-area | **Articoli → Tipo area** | ✓ description (term meta) |
   | `/risorse/` | Page WP 2813 (post_content vuoto) | Pagine → Risorse | ⚠️ Solo via SCF tab "Hub Pages" |
   | `/risorse/{slug}/` | Page WP | Pagine → {slug} | ✓ post_content |
   | `/costi-e-consulenze/` | Page WP 2695 | Pagine → Costi e Consulenze | ✓ post_content |
   | `/costi-e-consulenze/{slug}/` | Page WP | Pagine → {slug} | ✓ post_content |
   (...completa tutti i 15)

2. Sezione **"Tipo di sorgente"** (Page WP / Term taxonomy / Archive CPT) con 1 paragrafo per tipo + screenshot mockup di dove cliccare in WP Admin.

3. Sezione **"Blocchi globali ricorrenti"** con tabella:
   - CTA pre-footer → Saltelli Settings → CTA Defaults
   - Banda Newsletter → Saltelli Settings → Footer
   - Trust signals → Saltelli Settings → Brand
   - Sticky WhatsApp message → Saltelli Settings → Brand → Messaggio WhatsApp
   - Footer tier-1 aree → Saltelli Settings → Footer Aree
   - Footer colophon → Saltelli Settings → Footer + Studio Info
   - Header navigation → Apparenza → Menu (NON Saltelli Settings)

4. Sezione **"Cosa NON è editabile direttamente"** con i pattern hardcoded che restano:
   - Form Brevo HTML markup (provider switch = dev request)
   - Schema JSON-LD (technical SEO, dev-only)
   - Layout & design tokens (`tokens.css`, dev-only)

5. Sezione **"Cambio storico v3.0"** con bullet:
   - Wave 4.7.fix.2: SCF migration tab "Hub Pages" + "Archive CPT Headers" → ora Elena edita H1/eyebrow/intro hub & archive
   - Wave 4.7.fix.2: rename `/chi-siamo/risultati/` → `/chi-siamo/casi-rappresentativi/` (con redirect 301)
   - Wave 4.7.fix.2: menu primary rebuilt con type=page references → robusto ai rename slug

### 3.B — Commit Phase 3

```sh
git add -A
git commit -m "wave4-7-fix-2 P3: docs/EDITOR-HANDOFF.md v3.0

- Add 'Pagina vs Tassonomia vs Archive CPT' section
- Document admin path for all 15 Elena URLs
- Document recurring blocks SCF coverage post-Wave 4.7.fix.2
- Document v3.0 changelog: SCF migration tier-2 + slug rename + menu rebuild"
```

---

## Phase 4 — Recurring blocks SCF + Tier-2 SCF migration (90–120 min)

### 4.A — SCF field nuovi (`acf-json/group_theme_options_v1.json`)

Aggiungi i seguenti field **dentro tab esistenti dove sensato**, oppure crea tab nuove (es. "Hub Pages", "Archive Headers", "Footer Aree").

#### 4.A.1 — Tab "Brand" — aggiungi:
- `whatsapp_message_default` (text, default `'Ciao, %s sul vostro sito. Vorrei una consulenza.'`, instructions: "Usa `%s` per inserire il contesto della pagina (es. nome avvocato).")

#### 4.A.2 — Tab nuova "Footer Aree" (slot 80):
- `footer_tier1_aree` (repeater, min 3, max 4, default current 3 hardcoded):
  - sub-field `numero` (text, es. "01")
  - sub-field `label` (text, es. "Tributario")
  - sub-field `url` (url, es. "/aree-di-pratica/privati/diritto-tributario/")

#### 4.A.3 — Tab nuova "Hub Pages" (slot 85):
**Hub /chi-siamo/**:
- `hub_chisiamo_eyebrow` (text)
- `hub_chisiamo_h1_main` (text)
- `hub_chisiamo_h1_emphasis` (text, italic span)
- `hub_chisiamo_intro` (textarea / wysiwyg)

**Hub /aree-di-pratica/**:
- `hub_aree_eyebrow` (text)
- `hub_aree_h1_main` (text)
- `hub_aree_h1_emphasis` (text)
- `hub_aree_intro` (textarea / wysiwyg)
- `hub_aree_cluster_privati_label` (text)
- `hub_aree_cluster_privati_desc` (textarea)
- `hub_aree_cluster_imprese_label` (text)
- `hub_aree_cluster_imprese_desc` (textarea)
- `hub_aree_cluster_contenzioso_label` (text)
- `hub_aree_cluster_contenzioso_desc` (textarea)
- (eventualmente cluster_altri se presente)

**Hub /risorse/**:
- `hub_risorse_eyebrow`
- `hub_risorse_h1_main`
- `hub_risorse_h1_emphasis`
- `hub_risorse_intro`
- `hub_risorse_card_*` per le 4 resource cards (label + desc + url ognuna)

#### 4.A.4 — Tab nuova "Archive Headers" (slot 90):
**Archive avvocato (`/chi-siamo/team/`)**:
- `archive_avvocato_eyebrow` (text, default "§ Studio · Avvocati")
- `archive_avvocato_h1_main` (text, default "Quattro")
- `archive_avvocato_h1_emphasis` (text, default "professionisti.")
- `archive_avvocato_intro` (textarea, default "Un atelier di quattro avvocati a Chiaia...")

**Archive saltelli_caso (`/chi-siamo/casi-rappresentativi/`)**:
- `archive_caso_eyebrow`
- `archive_caso_h1_main`
- `archive_caso_h1_emphasis`
- `archive_caso_intro`

#### 4.A.5 — Tab "Hub Pages" (continued) — taxonomy-tipo-area UX strings:
**Taxonomy hub /aree-di-pratica/{term}/**:
- `taxonomy_tipoarea_eyebrow` (text)
- `taxonomy_tipoarea_subtitle_template` (text, instructions "Use `%s` per nome term")

(Nota: la `description` del term resta editabile come term meta; questi field sono per le UX strings hardcoded comuni a tutti i 4 tipo-area).

### 4.B — Update `inc/seed-theme-options.php`

Aggiungi seed per tutti i nuovi field con default editoriali (estratti dai template hardcoded). Idempotency: check `get_option()` empty/non-set prima del `update_option()`.

### 4.C — Refactor template

Per ogni template hardcoded → leggi via `saltelli_option()`:

1. **`header.php` (sticky WhatsApp message)**:
   ```php
   $whatsapp_msg = saltelli_option('whatsapp_message_default', 'Ciao, %s sul vostro sito. Vorrei una consulenza.');
   ```
   Refactor le 2 occorrenze (header.php:160-165 + mobile-sticky-bar).

2. **`footer.php` (tier1 aree)**:
   Sostituisci `$ftr_tier1` array hardcoded con loop `have_rows('footer_tier1_aree')`.

3. **`page-chi-siamo-hub.php`** (se esistente) o **`page.php` con condizionale chi-siamo**:
   Sostituisci eyebrow/H1/intro hardcoded con `saltelli_option('hub_chisiamo_*')`.

4. **`page-aree-di-pratica-hub.php`**:
   Sostituisci 4 cluster cards hardcoded (lines ~18-44) con `saltelli_option('hub_aree_cluster_*')`.

5. **`page-risorse-hub.php`**:
   Sostituisci hub strings + 4 resource cards.

6. **`archive-avvocato.php`**:
   Sostituisci eyebrow + H1 (line 30 `__('Quattro', ...) <em>professionisti.</em>`) + subtitle paragraph con `saltelli_option('archive_avvocato_*')`.

7. **`archive-saltelli_caso.php`** (se non esiste, creare da `archive.php` fallback):
   Stesso pattern, con `saltelli_option('archive_caso_*')`.

8. **`taxonomy-tipo-area.php`**:
   Sostituisci eyebrow + subtitle hardcoded con `saltelli_option('taxonomy_tipoarea_*')`.

### 4.D — Reseed + smoke test

```sh
wp eval-file wp-content/themes/saltelli/inc/seed-theme-options.php --path=/var/www/saltelli
wp cache flush --path=/var/www/saltelli
```

Verifica via curl + WP Admin:
- `/chi-siamo/team/` → H1 viene da SCF (modifica admin → reload pagina → cambio visibile)
- `/aree-di-pratica/` → cluster cards modificabili da admin
- Sticky WhatsApp → tooltip/messaggio configurabile

### 4.E — Commit Phase 4

```sh
git add -A
git commit -m "wave4-7-fix-2 P4: SCF tier-2 migration (recurring blocks + hub pages + archive headers)

NEW SCF FIELDS:
- Tab Brand: whatsapp_message_default
- Tab Footer Aree (new, slot 80): footer_tier1_aree repeater (3 sub)
- Tab Hub Pages (new, slot 85): hub_chisiamo_*, hub_aree_* (incl 4 cluster), hub_risorse_*, taxonomy_tipoarea_*
- Tab Archive Headers (new, slot 90): archive_avvocato_*, archive_caso_*

REFACTOR (8 templates):
- header.php + mobile-sticky-bar.php: WhatsApp message via saltelli_option
- footer.php: tier1 aree via repeater loop (replaces hardcoded \$ftr_tier1 array)
- page-chi-siamo-hub.php / page-aree-di-pratica-hub.php / page-risorse-hub.php: hub strings + cards via SCF
- archive-avvocato.php: eyebrow + H1 + intro via SCF
- archive-saltelli_caso.php: created from archive.php fallback, full SCF coverage
- taxonomy-tipo-area.php: UX strings via SCF

Elena ora edita TUTTO il copy editoriale del sito (incluso hub pages + CPT archives)."
```

---

## Phase 5 — Final QA + version bump (10 min)

### 5.A — Bump version

`wp-content/themes/saltelli/functions.php`:
```php
define('SALTELLI_THEME_VERSION', '1.3.8-wave4-7-fix-2-true-fix');
```

`wp-content/themes/saltelli/style.css`:
```css
Version: 1.3.8-wave4-7-fix-2-true-fix
```

### 5.B — Smoke test cross-template

Verifica con `curl -s` + `grep` o Playwright:
1. Homepage `/` → studio body HTML presente, no più hardcoded fallback
2. `/chi-siamo/` → H1 da SCF
3. `/chi-siamo/team/` → H1 da SCF, no 404
4. `/chi-siamo/casi-rappresentativi/` → 200 OK (rename ok)
5. `/chi-siamo/risultati/` → 301 → `/chi-siamo/casi-rappresentativi/`
6. `/aree-di-pratica/` → 4 cluster cards da SCF
7. `/aree-di-pratica/privati/` → term page OK
8. `/competenze/` → 301 → `/aree-di-pratica/`
9. `/faq/` → 301 → `/risorse/domande-frequenti/`
10. `/costi/` → 301 → `/costi-e-consulenze/`
11. Menu primary → ogni voce 200, no /faq/ /competenze/ /costi/ legacy
12. WhatsApp sticky → messaggio editabile da admin

### 5.C — Audit logs finale

Crea `.claude/knowledge/audits/wave4-7-fix-2-true-fix/REPORT.md` con:
- Phases completed (1-5)
- File modified count
- New SCF fields count
- New tabs count
- Redirect 301 count
- Menu items rebuilt count
- Tests passed/failed
- Open items / known issues
- Rollback procedure (1-shot per ogni phase)

### 5.D — Bump version commit + push

```sh
git add -A
git commit -m "wave4-7-fix-2 P5: bump version to 1.3.8-wave4-7-fix-2-true-fix + final QA report"
git push origin feat/wave4-7-fix-2-true-fix
```

### 5.E — Output finale per orchestratore

In chat, riporta:
- Branch pushato
- Commits totali (atteso: 5)
- Version corrente
- File modificati (lista riassuntiva)
- Smoke test passati / falliti (con eventuale lista failure)
- Open items / decisioni che richiedono input orchestratore prima del merge
- Tempo effettivo speso (per categoria)

---

## Hard rules per questa wave

1. **One-writer-at-a-time**: l'orchestratore (chat Claude.ai) NON committa nulla finché non hai pushato e l'audit è completato.
2. **Idempotency**: ri-eseguire `inc/seed-theme-options.php` non deve duplicare/sovrascrivere DB se già popolato.
3. **No new dependencies**: niente plugin nuovi, niente librerie nuove. Redirect via tema (no plugin Redirection se non già presente).
4. **No design tokens edit**: `tokens.css` non si tocca.
5. **Schema markup invariato**: nessuna modifica ai 5 partial in `inc/schema/`.
6. **Yoast coabitation preserved**.
7. **Menu backup obbligatorio** prima del rebuild (Phase 2.A).
8. **DB backup raccomandato** prima del reseed massivo Phase 4.D (su droplet `mysqldump`).
9. **Cache flush dopo ogni cambio non-triviale** (`wp cache flush`).
10. **Versioning monotonic**: 1.3.7 → 1.3.8 (NON 1.3.7.x).

## Tone in commit messages e report

Direct, concrete, zero filler. Stile commit usato finora dal progetto.

## Riferimenti

- `WAVE4-7-FIX-2-INVESTIGATION-REPORT.md` (uploads orchestratore)
- `.claude/knowledge/audits/wave4-7-fix-2-investigation/`
- DEC-029 (origin fallback pattern Wave 4.6)
- DEC-039-COMPLETED (Wave 4.7.fix SCF migration)
- DEC-040-COMPLETED (Wave 4.7.fix.1 SCF URL validation)
- `CLAUDE.md` § "Workflow rules" + § "Hard constraints" + § "Versioning policy"
- `docs/ARCHITECTURE.md` § "ACF schema mapping"
- `docs/EDITOR-HANDOFF.md` v1.1 (input per v3.0)

---

*Wave 4.7.fix.2 TRUE FIX · 5 phases · branch `feat/wave4-7-fix-2-true-fix` · stima 250–300 min · output → orchestratore audit + merge in `main`.*
