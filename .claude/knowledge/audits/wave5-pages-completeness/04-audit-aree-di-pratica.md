# Audit completezza — Aree di Pratica HUB (ID 2812) · `/aree-di-pratica/` · `template-parts/page-aree-di-pratica-hub.php`

**Render:** `page.php` → `get_template_part('template-parts/page', 'aree-di-pratica-hub')` (`is_page('aree-di-pratica')`). Lettura: `saltelli_page_field()` (page_slug=aree-di-pratica).
**Group SCF attuale:** `group_aree_di_pratica_v1` (attached a Page 2812). **Già 10 field SCF** — questa pagina è la più vicina all'obiettivo.
**Priorità:** P1 hub, ma quasi finita → quick win.

## Elementi frontend → sorgente

| Elemento frontend | File:linea | Sorgente attuale | Tipo | Decisione |
|---|---|---|---|---|
| Breadcrumb | :47 | `saltelli_render_breadcrumb()` | — | ⏸ auto |
| Hero eyebrow "§ Aree di pratica" | :18,48 | `saltelli_page_field('hub_aree_eyebrow')` | text | ✅ già SCF |
| Hero H1 "Diciassette aree, / tre cluster." | :19-20,49-58 | `saltelli_page_field('hub_aree_h1_main' / 'hub_aree_h1_emphasis')` | text + text | ✅ già SCF |
| Hero lede/intro | :21,59 | `saltelli_page_field('hub_aree_intro')` | textarea/wysiwyg | ✅ già SCF |
| Cluster card "Per i privati" — titolo | :27,74 | `saltelli_page_field('hub_aree_cluster_privati_label')` | text | ✅ già SCF |
| Cluster card "Per i privati" — desc | :28,75 | `saltelli_page_field('hub_aree_cluster_privati_desc')` | textarea | ✅ già SCF |
| Cluster card "Per le imprese" — titolo + desc | :33-34,74-75 | `saltelli_page_field('hub_aree_cluster_imprese_label' / '_desc')` | text + textarea | ✅ già SCF |
| Cluster card "Contenzioso amministrativo" — titolo + desc | :39-40,74-75 | `saltelli_page_field('hub_aree_cluster_contenzioso_label' / '_desc')` | text + textarea | ✅ già SCF |
| Cluster card num "01 / 03" etc. | :24,32,38,73 | hardcoded | layout | ⏸ struttura |
| Cluster card "X aree" count | :77-83 | `$term->count` (`get_term_by`) | — | ⏸ dynamic (tassonomia) |
| Cluster card cta "Esplora →" | :84 | hardcoded | text | ⚠️ DA MIGRARE → `hub_aree_card_cta` (text, condiviso — è uguale per tutte e 3) |
| Cluster card URL | :69,72 | `get_term_link($term)` | — | ⏸ dynamic (tassonomia) |
| CTA section eyebrow "§ Non trovi la materia?" | :94 | hardcoded | text | ⚠️ → `hub_aree_cta_eyebrow` (text) |
| CTA section H2 "Scrivici una nota: in 24 ore valutiamo…" | :95-97 | hardcoded | textarea | ⚠️ → `hub_aree_cta_title` (textarea) |
| CTA section bottone "Contattaci" + `/contatti/` | :98-100 | hardcoded label + `home_url` | text + url | ⚠️ → `hub_aree_cta_btn_label` (text) + `hub_aree_cta_url` (url) |

## Field SCF da aggiungere

| Field | Type |
|---|---|
| `hub_aree_card_cta` | text ("Esplora →" — condiviso tra le 3 card) |
| `hub_aree_cta_eyebrow` | text |
| `hub_aree_cta_title` | textarea |
| `hub_aree_cta_btn_label` | text |
| `hub_aree_cta_url` | url |

**Totale DA MIGRARE: ~5 field** — 3 text + 1 textarea + 1 url. **0 immagini, 0 repeater.** Pagina già al ~85% — solo la riga CTA finale + il cta-label delle card mancano.
**Group target:** `group_aree_di_pratica_v1` espandi. **Template refactor:** `page-aree-di-pratica-hub.php` (minimo).
**Stima implementation:** ~20 min — **quick win, candidato a fare per primo se si vuole "chiudere" una pagina in fretta.**
