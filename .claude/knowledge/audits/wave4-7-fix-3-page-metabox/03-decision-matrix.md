# Decision Matrix Theme Options → Page Metabox — Wave 4.7.fix.3

**Data**: 2026-05-08
**Decisione root**: ribaltare il pattern UX. Quello che descrive il contenuto di una Page WP migra a Page metabox; resta in Theme Options (Saltelli — Settings) solo configurazione globale del sito.

---

## Tabella decisione completa per tab Theme Options

### MIGRA → Page metabox (4 group SCF nuovi)

| Tab Theme Options corrente | Field group | Letto da | Page WP target | SCF group destination | Field migrati |
|---|---|---|---|---|---|
| Hero Homepage | hero_eyebrow, hero_headline, hero_subheadline, hero_cta_label, hero_cta_url | front-page.php | Page 17 (slug `home`) | `group_homepage_v1.json` (location `page_slug == home`) tab "Hero" | 5 |
| Studio Section | studio_titolo_sezione, studio_body, studio_foto_facciata | front-page.php | Page 17 | `group_homepage_v1.json` tab "Studio" | 3 |
| Team & Casi | team_titolo, cases_titolo, casi_rappresentativi_home | front-page.php | Page 17 | `group_homepage_v1.json` tab "Team & Casi" | 3 |
| Press Homepage | press_outlets (repeater + 3 sub) | front-page.php helper saltelli_press_outlets() | Page 17 | `group_homepage_v1.json` tab "Press" | 1 (repeater) |
| Hub Pages — Chi Siamo | hub_chisiamo_eyebrow, hub_chisiamo_h1_main, hub_chisiamo_h1_emphasis, hub_chisiamo_intro | page-chi-siamo-hub.php | Page 2822 (slug `chi-siamo`) | `group_chi_siamo_v1.json` (location `page_slug == chi-siamo`) | 4 |
| Hub Pages — Aree di Pratica (hero) | hub_aree_eyebrow, hub_aree_h1_main, hub_aree_h1_emphasis, hub_aree_intro | page-aree-di-pratica-hub.php | Page 2812 (slug `aree-di-pratica`) | `group_aree_di_pratica_v1.json` tab "Hero" | 4 |
| Hub Pages — Aree di Pratica (cluster cards) | hub_aree_cluster_privati_label, hub_aree_cluster_privati_desc, hub_aree_cluster_imprese_label, hub_aree_cluster_imprese_desc, hub_aree_cluster_contenzioso_label, hub_aree_cluster_contenzioso_desc | page-aree-di-pratica-hub.php | Page 2812 | `group_aree_di_pratica_v1.json` tab "Cluster Cards" | 6 |
| Hub Pages — Risorse | hub_risorse_eyebrow, hub_risorse_h1_main, hub_risorse_h1_emphasis, hub_risorse_intro | page-risorse-hub.php | Page 2813 (slug `risorse`) | `group_risorse_v1.json` (location `page_slug == risorse`) | 4 |

**Totale field migrati**: 30 (incluso 1 repeater press_outlets con sub-fields name/logo/url)

### RESTA in Theme Options (Saltelli — Settings)

| Tab Theme Options | Field | Reason |
|---|---|---|
| Brand | brand_payoff, brand_statement_short, trust_signal_1-4_label/caption (8 field), whatsapp_message_default | Read da header/footer/trust-bar = globali |
| Studio Info | studio_indirizzo_via, studio_cap_citta, studio_quartiere, studio_orari_settimana, studio_orari_sabato, studio_telefono_pubblico, studio_email, studio_pec, studio_piva, studio_ordine_avvocati | NAP studio = read da footer + page-contatti + 404 + multipla schema JSON-LD = globale per definizione |
| Mappa | studio_coordinate_lat, studio_coordinate_lng | Schema LocalBusiness, footer mappa = globale |
| Footer | colophon_indirizzo, colophon_orari, colophon_email, colophon_telefono, footer_credit_text, footer_credit_url, footer_newsletter_enabled, footer_newsletter_provider | Read da footer.php + colophon su front-page.php hero (dual-use ma globale) |
| Social | social_instagram, social_linkedin, social_facebook, social_twitter | Footer social links = globale |
| CTA Defaults | cta_default_label, cta_default_url, cta_trust_signal, cta_subline_italic | Read da 19 template (footer CTA + multiple) = globale |
| Footer Aree | footer_tier1_aree (repeater) | Read da footer.php = globale |
| Archive Headers | archive_avvocato_eyebrow, archive_avvocato_h1_main, archive_avvocato_h1_emphasis, archive_avvocato_intro, archive_caso_eyebrow, archive_caso_h1_main, archive_caso_h1_emphasis, archive_caso_intro | Archive CPT URLs (`/chi-siamo/team/`, `/chi-siamo/casi-rappresentativi/`) NON hanno una Page WP corrispondente = no target metabox possibile |
| Taxonomy Tipo Area | taxonomy_tipoarea_eyebrow, taxonomy_tipoarea_subtitle_template | Read da taxonomy-tipo-area.php (term page) — niente Page WP = no metabox |

**Totale field che restano**: ~38 (16 globali + 8 archive + 2 taxonomy + 12 globali multi-tab)

---

## Asimmetria UX risolta

### Pre-Wave 4.7.fix.3 (cosa lamenta Elena 2026-05-08)

> "https://staging.studiolegalesaltelli.it/chi-siamo/ non è modificabile al pari di altre pagine simili. Il Cms non è usabile in questo modo"

Per editare il contenuto di `/chi-siamo/` Elena:
1. Apre WP Admin → Pagine → Chi Siamo → Modifica
2. Vede solo title + slug (+ post_content se disponibile, ma vuoto per i 3 hub)
3. Per modificare hub copy → deve aprire **Saltelli — Settings → tab "Hub Pages"** in un pannello globale completamente separato
4. Scopre che alcune cose (le 3 cluster cards di aree-di-pratica) sono lì ma non sono associate a "Pagine"

**Modello mentale rotto**: in WP standard, "modifica pagina X = modifica contenuto pagina X". Saltelli rompe questa aspettativa.

### Post-Wave 4.7.fix.3

Per editare il contenuto di `/chi-siamo/` Elena:
1. Apre WP Admin → Pagine → Chi Siamo → Modifica
2. Vede metabox "Contenuto pagina" sotto/a fianco dell'editor con i 4 field hub_chisiamo_*
3. Salva → frontend riflette

**Modello mentale ripristinato**: "modifica pagina = modifica contenuto pagina".

---

## Decisioni autonome documentate

### 1. Naming convenzione SCF group nuovi: `group_<slug-with-underscores>_v1.json`

Codebase pattern esistente: `group_avvocato_v1`, `group_casi_v1`, `group_lo_studio_v1`, ecc. Le 4 nuove group seguono lo stesso pattern:
- `group_homepage_v1.json`
- `group_chi_siamo_v1.json`
- `group_aree_di_pratica_v1.json`
- `group_risorse_v1.json`

### 2. Location rule: `page_slug ==` (NON `page == <ID>`)

Codebase pattern (Wave 1 Debug-QA bug-04 fix in `inc/acf-fields.php:64-96`): tutte le Page-targeted location rules usano `page_slug ==` per env-portability (locale Docker ID ≠ droplet ID, slug stabile).

Il prompt Wave 4.7.fix.3 § 2.A suggerisce `param=page, value=<ID>` ma sarebbe regression rispetto al codebase. Decisione autonoma: **uso `page_slug ==`** consistency-first.

### 3. Static Homepage = Page 17 esistente (NON ne creo una nuova)

Staging conferma: `wp option get page_on_front` → `17`. Page WP 17 esiste con slug `home` e title "Home". Userò questa Page come target del nuovo `group_homepage_v1.json` (location `page_slug == home`). Non creo Page nuova "Homepage".

Per Elena, l'admin path sarà: WP Admin → Pagine → Home → Modifica. Quando lei ci entra, vede metabox "Hero · Studio · Team & Casi · Press" con 12 field totali divisi in 4 tab.

### 4. Repeater press_outlets — preservare key `field_press_outlets`

SCF salva i valori indicizzati per **field key** (non per group). Migrare il field con `key: "field_press_outlets"` invariato + `name: "press_outlets"` invariato + nuovo `parent` group, garantisce continuità dei dati senza export/reimport.

### 5. casi_rappresentativi_home — non popolato, scope minimo

Empty su staging (helper fallback in saltelli_homepage_cases() prende i 6 CPT più recenti). Migra il field SCF (struttura) ma niente da migrare lato dati. Quando Elena selezionerà casi via UI metabox, partirà popolato.

### 6. studio_foto_facciata — image attachment_id 2211 → migra come scalar

Field SCF type=image, return_format=array. Su staging il valore wp_options è `2211` (attachment ID). Migrazione: copy del valore scalar nella postmeta. SCF re-derives the array structure quando viene letto via `get_field()`. Frontend front-page.php usa `is_array($studio_foto) && !empty($studio_foto['url'])` — compatibile.

### 7. Tab description Saltelli Settings — orientamento Elena

Aggiungo "page intro" (HTML message field) al top di Saltelli — Settings:
> "Questa sezione raccoglie le impostazioni globali del sito. Per modificare il contenuto delle pagine specifiche, vai a [Pagine] e seleziona la pagina interessata."

Tab description per "Archive Headers":
> "Header per le pagine archivio dei CPT (Team /chi-siamo/team/, Casi rappresentativi /chi-siamo/casi-rappresentativi/) — queste pagine non hanno un editor diretto perché sono archivi automatici."

### 8. NON migro il Page Costi e Consulenze (2695)

`page-costi-e-consulenze-hub.php` non legge field SCF da Theme Options (verificato). Il post_content Page 2695 è già di 4649 chars (editabile via classic editor). Niente da migrare. Wave 4.7.fix.3 NON tocca questa Page.

### 9. NON migro field "borderline" globali usati anche da front-page.php

Field globali letti SIA da front-page.php SIA da footer/header/altri template:
- `colophon_*` (4 field) — letti da front-page.php hero colophon E da footer.php colophon
- `cta_default_*` (4 field) — letti da multiple template (front-page.php contatti CTA + footer + 19 templates)
- `studio_*` (NAP) — schema markup + footer + page-contatti
- `whatsapp_message_default` — header.php

**Decisione**: restano in Theme Options. Nessun "split" — un campo globale resta in un solo posto canonico.

### 10. Slug `home` per Page 17

WP-Admin sidebar mostrerà "Home" come Page editabile. Per consistency con altre Page del sito (es. "Chi Siamo", "Aree di Pratica", "Risorse") in EDITOR-HANDOFF.md v4.0 documenterò: "Modifica pagina Home → Pagine → Home" non "Pagina Homepage" perché lo slug WP è `home`.

---

## Output Phase 2 atteso

### File JSON nuovi (4)

1. **`acf-json/group_homepage_v1.json`** — location `page_slug == home`, 4 tab (Hero / Studio / Team & Casi / Press), 12 field
2. **`acf-json/group_chi_siamo_v1.json`** — location `page_slug == chi-siamo`, 1 tab (Contenuto pagina), 4 field
3. **`acf-json/group_aree_di_pratica_v1.json`** — location `page_slug == aree-di-pratica`, 2 tab (Hero / Cluster Cards), 10 field
4. **`acf-json/group_risorse_v1.json`** — location `page_slug == risorse`, 1 tab (Contenuto pagina), 4 field

### File modificati (5)

1. **`acf-json/group_theme_options_v1.json`** — rimuovo 5 tab (Hero Homepage / Studio Section / Team & Casi / Press Homepage / Hub Pages) + 30 field. Restano 8 tab: Studio Info, Mappa, Brand, Footer, Social, CTA Defaults, Footer Aree, Archive Headers, Taxonomy Tipo Area (in pratica 9 tab post-cleanup, contando Taxonomy come tab dedicato).
2. **`inc/helpers.php`** — aggiungo `saltelli_page_field()` helper.
3. **`front-page.php`** — refactor `saltelli_option('hero_*'|'studio_*'|'team_*'|'cases_*')` → `saltelli_page_field(...)`. Helper `saltelli_homepage_cases()` legge da Page 17 instead of options. Helper `saltelli_press_outlets[_full]()` idem.
4. **`template-parts/page-chi-siamo-hub.php`** — refactor `saltelli_option('hub_chisiamo_*')` → `saltelli_page_field('hub_chisiamo_*')`.
5. **`template-parts/page-aree-di-pratica-hub.php`** — idem `hub_aree_*`.
6. **`template-parts/page-risorse-hub.php`** — idem `hub_risorse_*`.

(I file 5+6 conteggiati = 6 file modificati totale)

### Migration script

`inc/migrations/wave4-7-fix-3-options-to-postmeta.php` — script idempotente migration ~30 chiavi `options_*` → postmeta delle 4 Page WP target (17, 2822, 2812, 2813).

---

*Decision matrix · Wave 4.7.fix.3 · 2026-05-08 · scopo: feedback Elena 2026-05-08 (UX: pagine non modificabili come Page WP standard).*
