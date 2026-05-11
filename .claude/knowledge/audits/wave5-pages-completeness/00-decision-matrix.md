# Wave 5 — Pages Completeness · Decision Matrix (piano STEP 3)

**Data:** 2026-05-11 · **Branch:** `audit/wave5-pages-completeness` · **Modalità:** read-only audit (zero modifiche a codice/SCF/template).
**Obiettivo:** portare TUTTE le Pages al pattern "eccellente" già raggiunto da lavora-con-noi / come-lavoriamo / prima-consulenza / richiedi-preventivo / guide-gratuite / domande-frequenti (`group_info_shared_v1` + `group_faq_v1`) = ogni testo editoriale, ogni immagine, ogni sezione ripetuta = field SCF; struttura/decorativo/dynamic = hardcoded.

**Reference "obiettivo completezza" (NON ri-auditate):** 372 lavora-con-noi · 2708 domande-frequenti · 2709 guide-gratuite · 2711 prima-consulenza · 2712 come-lavoriamo · 2713 richiedi-preventivo → 16 field SCF "Page Servizi" + FAQ group. Questo è il livello a cui portare le altre.

---

## Matrice aggregata

| # | Page / Archive | URL | Field SCF da aggiungere | di cui image | di cui repeater | Group SCF target | Template da refactor | Stima | Priorità |
|---|---|---|---|---|---|---|---|---|---|
| 06 | **Costi e Consulenze HUB** (2695) | `/costi-e-consulenze/` | **~16** (13 text + 3 textarea) | 0 | 0 | **NUOVO** `group_costi_e_consulenze_hub_v1` | `page-costi-e-consulenze-hub.php` | ~35 min | **P1 ALTA** — 0 field oggi, tutto hardcoded |
| 03 | **Lo Studio** (2811) | `/chi-siamo/lo-studio/` | **~20** (14 text + 4 textarea + 1 wysiwyg + 1 url) | **1** ⚠️ `lo_studio_plate_image` (facciata Via Vannella Gaetani 27) | 0 | `group_lo_studio_v1` espandi | `page-lo-studio.php` | ~50 min | P1 — molto editoriale, lede prosa hardcoded, plate placeholder vuoto |
| 01 | **Home** (17) | `/` | **~12** (11 text + 1 textarea); 3 "core" + ~7 eyebrow sezione bassa-prio + 2 `home_areas_h2_*` | 0 | 0 | `group_homepage_v1` espandi | `front-page.php` | ~40 min | P1 — traffico massimo (ma già 50% SCF) |
| 02 | **Chi Siamo HUB** (2822) | `/chi-siamo/` | **~13** (Opt A: 6 text + 4 textarea + 1 url + 2 text card... ≈ 7 text + 4 textarea + 1 url) | 0 | 0 (Opt B: 1 cards repeater) | `group_chi_siamo_v1` espandi | `page-chi-siamo-hub.php` | ~30 min | P1 — 3 child-card + CTA finale hardcoded |
| 05 | **Risorse HUB** (2813) | `/risorse/` | **~13** (Opt A: 9 text + 4 textarea) | 0 | 0 (Opt B: 1 cards repeater) | `group_risorse_v1` espandi | `page-risorse-hub.php` | ~30 min | P1 — 4 resource-card hardcoded |
| 07 | **Contatti** (23) | `/contatti/` | **~11** (9 text + 1 textarea + 1 repeater) | 0 | **1** ⚠️ `contatti_come_items` (3 voci Metro/Auto/Treno) | `group_contatti_v1` espandi | `page-contatti.php` | ~35 min | P1 — sezione "Come arrivare" + success message hardcoded |
| 04 | **Aree di Pratica HUB** (2812) | `/aree-di-pratica/` | **~5** (3 text + 1 textarea + 1 url) | 0 | 0 | `group_aree_di_pratica_v1` espandi | `page-aree-di-pratica-hub.php` | ~20 min | P1 — **quick win** (già ~85% SCF, manca solo CTA finale + cta-label card) |
| 13 | **Blog archivio** (Page 1413) | `/risorse/blog/` | **~7 core** (3 text + 3 textarea + 1 text) (+ ~5 bassa-prio) | 0 | 0 | **NUOVO** `group_blog_archive_v1` (su Page 1413) | `home.php` | ~25 min | P2 — rende **finalmente sensata** l'editing della Page 1413 |
| 14 | **Archive CPT avvocato** | `/chi-siamo/team/` | **~10** (6 text + 3 textarea + 1 url) + riuso CPT `saltelli_principio` | 0 | 0 (o 1 `archive_avvocato_principles` se non si riusa il CPT) | Theme Options "Archive Headers" espandi | `archive-avvocato.php` | ~30 min | P2 — aside trust + § Come lavoriamo + CTA finale hardcoded |
| 08 | **Glossario legale** (2710) — *Fase 1* | `/risorse/glossario-legale/` | **~8** (4 text + 2 textarea + 1 url + 1 text) — solo hero + CTA | 0 | 0 | **NUOVO** `group_glossario_v1` (su Page 2710) | `inc/wave3-glossario.php` | ~20 min | P2 |
| 08b | **Glossario legale** (2710) — *Fase 2* (wave separata) | idem | **NUOVO CPT `glossary_term`** (~5 CPT-field: term, definition wysiwyg, letter, category, related_areas) + FAQ glossario → CPT `saltelli_faq` o repeater | 0 | (CPT) | NUOVO CPT `glossary_term` | `inc/wave3-glossario.php` (+ JSON-LD `DefinedTermSet`) | ~3-4 ore | P3 — i 60 termini sono content editoriale ma è un lift grande (richiede anche rifare il JSON-LD) |
| 15 | **Archive CPT saltelli_caso** | `/chi-siamo/casi-rappresentativi/` | **0-1** (1 text empty-state opzionale) | 0 | 0 | — (già completa) | — | ~0-5 min | **DONE** — header SCF + casi via CPT |
| 09 | **Prenota Appuntamento** (2714) | `/prenota-appuntamento/` | **0** | 0 | 0 | — | — | 0 | **DONE** — fallback Gutenberg (title + content) |
| 10 | **Privacy Policy** (2741) | `/privacy-policy/` | **0** | 0 | 0 | — | — | 0 | **DONE** — contenuto Iubenda, NON migrare |
| 11 | **Cookie Policy** (2742) | `/cookie-policy/` | **0** | 0 | 0 | — | — | 0 | **DONE** — contenuto Iubenda, NON migrare |
| 12 | **Note legali** (2743) | `/note-legali/` | **0** | 0 | 0 | — | — | 0 | **DONE** — fallback Gutenberg, NON migrare |
| | **6 Pages reference già eccellenti** | (info-shared + faq) | — (non auditate) | — | — | `group_info_shared_v1` + `group_faq_v1` | — | — | **DONE** |
| | **TOTALE Fase 1** (escl. Glossario Fase 2) | | **~116 field** (~100 se si saltano gli eyebrow sezione bassa-prio) | **1** image (+ opportunità design) | **1** repeater mandatory (+ 3 opzionali "cards") | **3 nuovi group** + **6 group da espandere** + Theme Options Archive Headers | **~10 template** | **~5h 15min** | |
| | **TOTALE con Glossario Fase 2** | | + ~5 CPT-field + 1 nuovo CPT | | | + 1 nuovo CPT | + JSON-LD refactor | **~8-9.5 ore** | |

---

## Ordine implementazione raccomandato (orchestratore decide)

1. **Costi e Consulenze HUB** (2695) — `page-costi-e-consulenze-hub.php` — il gap più grande in assoluto (0 field, alto traffico, tutto hardcoded). ~35 min.
2. **Aree di Pratica HUB** (2812) — `page-aree-di-pratica-hub.php` — quick win, già al ~85%, chiudi una pagina in fretta. ~20 min.
3. **Home** (17) — `front-page.php` — traffico massimo. ~40 min.
4. **Chi Siamo HUB** (2822) — `page-chi-siamo-hub.php` — hub alto traffico. ~30 min.
5. **Lo Studio** (2811) — `page-lo-studio.php` — molto editoriale + include l'unica image gap critica (facciata). ~50 min.
6. **Risorse HUB** (2813) — `page-risorse-hub.php`. ~30 min.
7. **Contatti** (23) — `page-contatti.php` — include il repeater "Come arrivare". ~35 min.
8. **Blog** (1413) — `home.php` — rende sensata la Page 1413 (+ valutare di aggiungerla a `SALTELLI_SCF_ONLY_PAGES`). ~25 min.
9. **Archive Team** (`/chi-siamo/team/`) — `archive-avvocato.php` — P2. ~30 min. Bonus: consolida i 3 principi sul CPT `saltelli_principio` (oggi 2 fallback hardcoded duplicati: qui + page-lo-studio.php).
10. **Glossario Fase 1** (2710) — `inc/wave3-glossario.php`, solo hero+CTA. ~20 min.
— **Wave separata futura:** **Glossario Fase 2** (CPT `glossary_term` + 60 termini + FAQ + JSON-LD). ~3-4 ore.
— **Skip (già al pattern, niente da fare):** Archive Casi, Prenota Appuntamento, Privacy, Cookie, Note legali, + le 6 reference.

**Suggerimento operativo:** una mini-wave STEP 3 può fare i punti 1-7 (i 7 P1, ~3h40m) in un'unica passata (1 branch `feat/wave5-step3-pages-scf`, 1 commit per Page, version bump finale), poi una passata leggera per 8-10 (~1h15m), e la Fase 2 glossario come Wave a sé.

---

## Convenzioni field SCF proposte

- **Naming:** `<page_slug>_<section>_<element>` per le pagine custom (es. `hub_costi_card1_title`, `lo_studio_lede_body`, `contatti_come_items`). Per gli hub si è seguito il prefix esistente (`hub_chisiamo_*`, `hub_aree_*`, `hub_risorse_*`, `hub_costi_*`). Per gli archive: `archive_avvocato_*`, `archive_caso_*` (prefix già in uso in Theme Options).
- **Type:** `text` per heading/label brevi · `textarea` per paragrafi multi-linea senza formattazione (es. H2 a 3 righe, lede semplici) · `wysiwyg` per prosa rich con link/bold/liste (es. `lo_studio_lede_body`, `hub_*_intro` se serve formattazione) · `image` per ogni elemento immagine (Media Library picker WP-nativo) · `url` per link CTA/esterni · `repeater` per liste ripetute (es. `contatti_come_items`; opzionalmente le "cards" degli hub).
- **Default value:** ogni nuovo field deve avere `default_value` = la stringa attualmente hardcoded nel template (così il frontend non cambia finché Elena non edita) — coerente con il pattern Wave 4.7.fix.2 (`studio_body` JSON default + seed in DB via `inc/seed-theme-options.php`).
- **Dynamic = resta as-is:** dati globali Studio Info (`saltelli_option('studio_*' / 'colophon_*')`, `saltelli_studio_phone_e164()`), query CPT (`get_posts('competenza'/'avvocato'/'saltelli_caso'/'saltelli_principio'/...)`, taxonomy `get_terms`/`get_term_by`), conteggi (`wp_count_posts`), breadcrumb (`saltelli_get_breadcrumb_chain`), CTA defaults globali (`cta_default_*`, Theme Options tab 6), il form CF7. Documentati nei singoli audit come ⏸.
- **Out of scope:** Yoast meta + breadcrumb schema (plugin) · JSON-LD inline (dev) · numerazione sezioni "§ 01 — …" (struttura — flaggate ⚠️ ma "bassa prio") · placeholder text dei riquadri "Plate" quando l'immagine non è caricata (struttura).

---

## Bug / inconsistenze trovate (fuori scope audit — da segnalare)

1. **`/costi-e-consulenze/costi/` → 404.** La card 01 "Costi" del HUB `page-costi-e-consulenze-hub.php` linka a `/costi-e-consulenze/costi/`, ma non esiste una Page con slug `costi` sotto 2695 (i child sono 2711/2712/2713). Il template `template-parts/page-costi.php` + il group `group_costi_v1` (~13 field "Come calcoliamo"/scenari/CTA) esistono ma **non sono mai invocati** (nessuna Page ha slug `costi`). → o si crea la Page figlia `costi` (e si wira `page-costi.php` + `group_costi_v1`), o si rimuove/ridireziona la card 01. Decisione orchestratore.
2. **`EDITOR-HANDOFF.md` §5.1-5.6 (sezioni pre-v5.0) hanno ID Page stale** — es. §5.1 dice "`/costi/` (ID 2695)" e descrive `group_costi_v1` come attivo su 2695 (falso: 2695 usa il template HUB hardcoded); §5.4 dice "`/faq/` (ID 2705)" ma la FAQ è ID 2708; §5.5 mappa `/come-lavoriamo/` a ID 2709 ma è 2712, `/prima-consulenza/` a 2708 ma è 2711, `/richiedi-preventivo/` a 2710 ma è 2713, `/guide-gratuite/` a 2706 ma è 2709. Superato da §5.0 (v5.0) e §3.7 (v6.0). Cleanup di §5.x = wave doc futura.
3. **Fallback "3 principi" duplicato** in `page-lo-studio.php` (:250-254) e `archive-avvocato.php` (:118-138, lì interamente literale, nemmeno via CPT). Quando si fa lo STEP 3, consolidare entrambi sul CPT `saltelli_principio` (già esistente, Wave 2 popolato).
4. **`page-lo-studio.php` usa ancora `the_content()` come prima priorità** per il body §02 founding (:146), ma la Page 2811 è in `SALTELLI_SCF_ONLY_PAGES` (Gutenberg disabled) → `the_content()` è di fatto vuoto → si usa sempre `founding_paragraphs` (ACF). Funziona, ma il ramo `the_content()` è morto — pulire quando si refactora la pagina (rendere `founding_paragraphs` la fonte unica).

---

## File deliverable

```
.claude/knowledge/audits/wave5-pages-completeness/
├── 00-decision-matrix.md          ← questo file
├── 01-audit-home.md               (~12 field, P1)
├── 02-audit-chi-siamo.md          (~13 field, P1)
├── 03-audit-lo-studio.md          (~20 field + 1 image, P1)
├── 04-audit-aree-di-pratica.md    (~5 field, P1 quick win)
├── 05-audit-risorse.md            (~13 field, P1)
├── 06-audit-costi-e-consulenze.md (~16 field, P1 ALTA)
├── 07-audit-contatti.md           (~11 field + 1 repeater, P1)
├── 08-audit-glossario-legale.md   (~8 field Fase 1 + CPT Fase 2, P2/P3)
├── 09-audit-prenota-appuntamento.md (0 field — DONE)
├── 10-audit-privacy-policy.md     (0 field — DONE, Iubenda)
├── 11-audit-cookie-policy.md      (0 field — DONE, Iubenda)
├── 12-audit-note-legali.md        (0 field — DONE)
├── 13-audit-blog.md               (~7 field core, P2)
├── 14-audit-archive-team.md       (~10 field + riuso CPT principio, P2)
└── 15-audit-archive-casi-rappresentativi.md (0-1 field — DONE)
```
