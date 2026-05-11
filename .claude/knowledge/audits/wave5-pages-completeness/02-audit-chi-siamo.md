# Audit completezza — Chi Siamo HUB (ID 2822) · `/chi-siamo/` · `template-parts/page-chi-siamo-hub.php`

**Render:** `page.php` → `get_template_part('template-parts/page', 'chi-siamo-hub')`. Lettura: `saltelli_page_field()` (page_slug=chi-siamo).
**Group SCF attuale:** `group_chi_siamo_v1` (attached a Page 2822). 4 field già SCF (hero). Le 3 child-cards + la CTA finale sono hardcoded.
**Priorità:** P1 — hub alto traffico.

## Elementi frontend → sorgente

| Elemento frontend | File:linea | Sorgente attuale | Tipo | Decisione |
|---|---|---|---|---|
| Breadcrumb | :29 | `saltelli_render_breadcrumb()` | — | ⏸ auto |
| Hero eyebrow "§ Chi siamo" | :21,30 | `saltelli_page_field('hub_chisiamo_eyebrow')` | text | ✅ già SCF |
| Hero H1 "Quattro avvocati, / un atelier." | :22-23,31-40 | `saltelli_page_field('hub_chisiamo_h1_main' / 'hub_chisiamo_h1_emphasis')` | text + text | ✅ già SCF |
| Hero lede/intro | :24,41 | `saltelli_page_field('hub_chisiamo_intro')` | wysiwyg/textarea | ✅ già SCF |
| Card 01 "Lo Studio" — num "01 / 03" | :50 | hardcoded | layout | ⏸ struttura (numerazione) |
| Card 01 — titolo "Lo Studio" | :51 | hardcoded | text | ⚠️ DA MIGRARE → `hub_chisiamo_card1_title` (text) |
| Card 01 — desc "Storia dal 1999, valori, sede…" | :52-54 | hardcoded | textarea | ⚠️ DA MIGRARE → `hub_chisiamo_card1_desc` (textarea) |
| Card 01 — cta "Scopri →" | :55 | hardcoded | text | ⚠️ DA MIGRARE → `hub_chisiamo_card1_cta` (text) |
| Card 01 — URL `/chi-siamo/lo-studio/` | :49 | `home_url()` hardcoded | url | ⏸ routing fisso (struttura IA) |
| Card 02 "Il Team" — titolo | :61 | hardcoded | text | ⚠️ DA MIGRARE → `hub_chisiamo_card2_title` |
| Card 02 — desc "%s avvocati, ognuno con…" (`printf` con count) | :62-69 | hardcoded + `$sl_lawyers_count` (`wp_count_posts`) | textarea | ⚠️ DA MIGRARE → `hub_chisiamo_card2_desc` (textarea; il `%s` count può restare interpolato dal template o essere droppato) |
| Card 02 — cta "Conosci il team →" | :70 | hardcoded | text | ⚠️ DA MIGRARE → `hub_chisiamo_card2_cta` |
| Card 03 "Risultati" — titolo | :76 | hardcoded | text | ⚠️ DA MIGRARE → `hub_chisiamo_card3_title` |
| Card 03 — desc "%s casi rappresentativi vinti…" (`printf` con count) | :77-84 | hardcoded + `$sl_casi_count` | textarea | ⚠️ DA MIGRARE → `hub_chisiamo_card3_desc` (textarea) |
| Card 03 — cta "Vedi i casi →" | :85 | hardcoded | text | ⚠️ DA MIGRARE → `hub_chisiamo_card3_cta` |
| CTA section eyebrow "§ Iniziamo a parlare" | :94 | hardcoded | text | ⚠️ DA MIGRARE → `hub_chisiamo_cta_eyebrow` (text) |
| CTA section H2 "Prenota una prima consulenza…" | :95-97 | hardcoded | textarea | ⚠️ DA MIGRARE → `hub_chisiamo_cta_title` (textarea) |
| CTA section bottone "Contattaci" | :98-100 | hardcoded label + `home_url('/contatti/')` | text + url | ⚠️ DA MIGRARE → `hub_chisiamo_cta_btn_label` (text) + `hub_chisiamo_cta_url` (url) |

## Field SCF da aggiungere

**Opzione A — per-card (più esplicito per Elena):** `hub_chisiamo_card{1,2,3}_title` (text ×3), `_desc` (textarea ×3), `_cta` (text ×3) = 9 field + `hub_chisiamo_cta_eyebrow` (text), `_cta_title` (textarea), `_cta_btn_label` (text), `_cta_url` (url) = 4 field. **Totale 13.**
**Opzione B — repeater:** `hub_chisiamo_cards` (repeater, 3 rows, sub: title:text + desc:textarea + cta:text) + 4 field CTA. **Totale ~5 "field" + 1 repeater.** (Le URL delle card restano hardcoded — sono routing IA, non content.)

**Totale DA MIGRARE: ~13 field (Opzione A) / 1 repeater + 4 field (Opzione B).** 0 immagini (la hub chi-siamo non ha immagini — la foto facciata è nella pagina figlia Lo Studio). **Raccomando Opzione A** (le card sono esattamente 3 e fisse, niente add/remove → per-card è più chiaro in admin del repeater).
**Group target:** `group_chi_siamo_v1` espandi. **Template refactor:** `page-chi-siamo-hub.php`.
**Stima implementation:** ~30 min.
