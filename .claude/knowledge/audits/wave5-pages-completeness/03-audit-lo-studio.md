# Audit completezza — Lo Studio (ID 2811) · `/chi-siamo/lo-studio/` · `template-parts/page-lo-studio.php`

**Render:** `page.php` → `get_template_part('template-parts/page', 'lo-studio')` (`is_page('lo-studio')`). Page in `SALTELLI_SCF_ONLY_PAGES` (Gutenberg disabled — ma vedi nota §02 sotto: il template usa ancora `the_content()` come prima priorità per il body founding!).
**Group SCF attuale:** `group_lo_studio_v1` (attached a Page 2811). Già SCF: `timeline_year_range`, `timeline_events` (repeater), `founding_paragraphs` (wysiwyg). Resto: molto hardcoded.
**Priorità:** P1 — alto traffico + è la pagina "storica" dello studio, molto editoriale, oggi quasi tutto hardcoded.

## Elementi frontend → sorgente

| Elemento frontend | File:linea | Sorgente attuale | Tipo | Decisione |
|---|---|---|---|---|
| Breadcrumb | :77 | `saltelli_render_breadcrumb()` | — | ⏸ auto |
| Hero aside eyebrow "§ Lo studio · Chi siamo" | :78 | hardcoded | text | ⚠️ → `lo_studio_hero_eyebrow` (text) |
| Hero aside meta "Un atelier / di quattro avvocati / in Via Vannella Gaetani 27 / Chiaia · Napoli / Dal 1999" | :79-85 | hardcoded (5 `esc_html_e`) | textarea | ⚠️ → `lo_studio_hero_meta` (textarea, 1 riga = 1 linea) |
| Hero H1 "Un atelier / di quattro / professionisti." | :87-99 | hardcoded (3 `esc_html__`) | textarea + text | ⚠️ → `lo_studio_h1_main` (textarea "Un atelier / di quattro") + `lo_studio_h1_em` (text "professionisti.") |
| §01 eyebrow "§ 01 — Lede" | :104 | hardcoded | layout | ⏸ struttura (sezione numerata) |
| §01 lede prosa — paragrafo 1 "Un atelier di quattro professionisti che da oltre vent'anni…" | :106-108 | **HARDCODED letterale** (nemmeno tradotto) | wysiwyg | ⚠️ DA MIGRARE → `lo_studio_lede_body` (wysiwyg) — **gap evidente: prosa editoriale completamente hardcoded** |
| §01 lede prosa — paragrafo 2 "Crediamo che il diritto sia…" | :109-111 | hardcoded `esc_html_e` | (idem) | ⚠️ (incluso in `lo_studio_lede_body`) |
| §01.5 Plate (placeholder foto facciata) "Plate I · Facciata studio / Via Vannella Gaetani, 27 / Palazzo nobiliare · Chiaia · Napoli" | :116-127 | hardcoded — **nessuna immagine reale, solo placeholder testuale** | image | ⚠️ **DA MIGRARE → `lo_studio_plate_image` (image, Media Library)** — CRITICO: oggi è un riquadro vuoto, serve la foto reale della facciata di Via Vannella Gaetani 27 |
| §02 eyebrow "§ 02 — 1999" + "1999." | :132-133 | hardcoded | text | ⚠️ → `lo_studio_founding_year` (text "1999.") (l'eyebrow "§ 02 — 1999" può restare struttura) |
| §02 H2 "Un atelier, in senso napoletano." | :136-138 | hardcoded `esc_html_e` | text | ⚠️ → `lo_studio_founding_h2` (text) |
| §02 body prosa founding | :140-156 | priorità: `the_content()` → `founding_paragraphs` (wysiwyg, ✅ SCF) → hardcoded fallback | wysiwyg | ✅ già editabile (post_content o `founding_paragraphs`) — *ma nota: la Page è Gutenberg-disabled, quindi `the_content()` è di fatto vuoto → di fatto si usa `founding_paragraphs`. OK.* |
| §03 team-mini eyebrow "§ 03 — I nostri quattro" | :166 | hardcoded | text | ⚠️ → `lo_studio_team_eyebrow` (text) |
| §03 team-mini H2 "Quattro avvocati, / diciassette aree." | :167-170 | hardcoded (2 `esc_html_e`) | text + text | ⚠️ → `lo_studio_team_h2_main` (text) + `lo_studio_team_h2_em` (text) |
| §03 grid 4 avvocati (ritratto, ruolo, nome, bio breve) | :172-218 | `get_posts('avvocato')` + thumbnail + `saltelli_field` (CPT) | — | ⏸ dynamic (CPT avvocato) |
| §04 principi eyebrow "§ 04 — Come lavoriamo" | :225 | hardcoded | text | ⚠️ → `lo_studio_principi_eyebrow` (text) |
| §04 principi H2 "Tre principi." | :227-229 | hardcoded (2 `esc_html_e`) | text | ⚠️ → `lo_studio_principi_h2_main` (text "Tre") + `_h2_em` (text "principi.") — *o 1 textarea* |
| §04 lista 3 principi (num, title, desc) | :230-266 | `get_posts('saltelli_principio')` (CPT, Wave 2 popolato) + fallback hardcoded | — | ⏸ dynamic (CPT saltelli_principio) — *nota: il fallback hardcoded è duplicato anche in archive-avvocato.php; vedi 14-audit-archive-team* |
| §05 cronologia eyebrow "§ 05 — Cronologia" | :274 | hardcoded | text | ⚠️ → `lo_studio_timeline_eyebrow` (text) |
| §05 cronologia H2 (year range) "1999 → 2026." | :37,275 | `saltelli_field('timeline_year_range')` | text | ✅ già SCF |
| §05 cronologia lista eventi (year, title, desc) | :39-60,277-288 | `saltelli_field('timeline_events')` repeater + fallback hardcoded | repeater | ✅ già SCF |
| §06 CTA eyebrow "§ 06 — Primo incontro" | :294 | hardcoded | text | ⚠️ → `lo_studio_cta_eyebrow` (text) |
| §06 CTA H2 "Prenota / una consulenza / gratuita." | :296-299 | hardcoded (3 `esc_html_e`) | textarea | ⚠️ → `lo_studio_cta_h2` (textarea) |
| §06 CTA lede "Il primo incontro è gratuito…" | :300-302 | hardcoded | textarea | ⚠️ → `lo_studio_cta_lede` (textarea) |
| §06 CTA bottone "Prenota un primo incontro" + `/contatti/` | :303-306 | hardcoded label + `home_url` | text + url | ⚠️ → `lo_studio_cta_btn_label` (text) + `lo_studio_cta_url` (url) |

## Field SCF da aggiungere

| Field | Type |
|---|---|
| `lo_studio_hero_eyebrow` | text |
| `lo_studio_hero_meta` | textarea |
| `lo_studio_h1_main` | textarea |
| `lo_studio_h1_em` | text |
| `lo_studio_lede_body` | **wysiwyg** (prosa editoriale, 2 paragrafi) |
| `lo_studio_plate_image` | **image** (Media Library — facciata Via Vannella Gaetani 27) ← CRITICO |
| `lo_studio_founding_year` | text |
| `lo_studio_founding_h2` | text |
| `lo_studio_team_eyebrow` | text |
| `lo_studio_team_h2_main` | text |
| `lo_studio_team_h2_em` | text |
| `lo_studio_principi_eyebrow` | text |
| `lo_studio_principi_h2_main` | text |
| `lo_studio_principi_h2_em` | text |
| `lo_studio_timeline_eyebrow` | text |
| `lo_studio_cta_eyebrow` | text |
| `lo_studio_cta_h2` | textarea |
| `lo_studio_cta_lede` | textarea |
| `lo_studio_cta_btn_label` | text |
| `lo_studio_cta_url` | url |

**Totale DA MIGRARE: ~20 field** — di cui **1 image** (`lo_studio_plate_image`, CRITICO) + **1 wysiwyg** (`lo_studio_lede_body`) + 4 textarea + ~14 text + 1 url. **0 repeater nuovi** (timeline già repeater; principi già CPT).
**Group target:** `group_lo_studio_v1` espandi. **Template refactor:** `page-lo-studio.php` (sostituire le ~30 `esc_html_e`/letterali con `saltelli_field(..., $pid, default)` + wire dell'`<img>` plate).
**Stima implementation:** ~50 min. **Punto di attenzione:** il paragrafo 1 del lede (:106-108) è testo letterale nel PHP (non `esc_html_e`) — va estratto con cura nel default del wysiwyg.
