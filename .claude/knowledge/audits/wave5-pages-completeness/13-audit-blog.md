# Audit completezza — Blog archivio (Page 1413 `blog` = `page_for_posts`) · `/risorse/blog/` · `home.php`

**Render:** `home.php` (template della "pagina degli articoli"). **Il `post_content`/title della Page 1413 NON sono usati** dal frontend (solo il title → segmento breadcrumb "Editoriale"). Dettaglio già documentato in `audits/wave4-7-fix-5-cleanup/02-blog-editing-map.md`.
**Group SCF attuale:** **NESSUNO** attached a Page 1413. L'header dell'archivio (eyebrow / H1 "Editoriale." / lede / blocco newsletter) è hardcoded in `home.php`.
**Priorità:** P2 — non è "rotto", ma aggiungere SCF qui **renderebbe finalmente sensata l'editing della Page 1413** (oggi Elena ci scrive e non cambia nulla — Wave 4.7.fix.5 ci ha messo un notice di avviso).

## Elementi frontend → sorgente

| Elemento frontend | File:linea | Sorgente attuale | Tipo | Decisione |
|---|---|---|---|---|
| Breadcrumb | :58 | `saltelli_render_breadcrumb()` (segmento "Editoriale" = title Page 1413) | — | ⏸ auto |
| Hero eyebrow "§ Editoriale · Saltelli" | :59 | hardcoded | text | ⚠️ → `blog_archive_eyebrow` (text) |
| Hero H1 "Editoriale." | :60 | hardcoded `saltelli_split_h1_words('Editoriale.')` | text | ⚠️ → `blog_archive_h1` (text) |
| Hero lede destra "Articoli, casi vinti, novità giurisprudenziali da Studio Legale Saltelli & Partners. Aggiornato settimanalmente." | :63-65 | hardcoded | textarea | ⚠️ → `blog_archive_lede` (textarea) |
| Hero counter "X articoli · Y categorie · agg. Z" | :66-73 | `$total_posts`, `wp_count_terms`, `get_lastpostmodified` | — | ⏸ dynamic |
| Category tabs (Tutti + 7 categorie top) | :78-91 | `get_categories(['orderby'=>'count','number'=>7])` | — | ⏸ dynamic (tassonomia categorie) |
| Featured card eyebrow "§ In evidenza · {data}" + "Plate · IV" | :107,111 | hardcoded ("Plate · IV" decorativo) | text | ⚠️ → `blog_featured_eyebrow_prefix` (text "§ In evidenza ·") + `blog_featured_plate_label` (text "Plate · IV", *bassa prio decorativo*) |
| Featured card (titolo, lede, meta, "Leggi l'articolo →") | :93-131 | il post più recente (`get_the_title`, `get_the_excerpt`, autore, reading-time) + cta label hardcoded | — | ⏸ dynamic (post) — eventuale `blog_featured_cta_label` (text, *bassa prio*) |
| Grid head "§ Archivio · X di Y" + "Tutti gli articoli." | :136-142 | hardcoded ("§ Archivio ·" + "Tutti gli articoli." `esc_html`) | text | ⚠️ → `blog_grid_eyebrow_prefix` (text) + `blog_grid_title` (text "Tutti gli articoli.") — *bassa prio* |
| Grid 3-col cards (media, categoria, titolo, estratto, meta) | :147-185 | loop post (dynamic) | — | ⏸ dynamic (post) |
| Empty state "Nessun contenuto trovato." | :145 | hardcoded | text | ⚠️ → `blog_empty_text` (text, *bassa prio*) |
| Pagination "← Precedenti / X—Y di Z / Successivi →" | :188-215 | auto | layout | ⏸ struttura |
| Newsletter — eyebrow "§ Newsletter" | :221 | hardcoded | text | ⚠️ → `blog_newsletter_eyebrow` (text) |
| Newsletter — H2 "Un articolo / al mese." | :222-225 | hardcoded | textarea | ⚠️ → `blog_newsletter_h2` (textarea) |
| Newsletter — lede "Una sola mail al mese. Solo casi vinti…" | :228-230 | hardcoded | textarea | ⚠️ → `blog_newsletter_lede` (textarea) |
| Newsletter — form (label "Email", placeholder, bottone "Iscriviti →") | :231-237 | hardcoded; `POST → /contatti/` | — | ⏸ funzionale (form) — eventuale `blog_newsletter_btn_label` (text, *bassa prio*) |
| Schema JSON-LD Blog + ItemList | :245-290 | derivato dai post | — | ⏸ dev |

## Field SCF da aggiungere

**Core (raccomandato):**
| Field | Type |
|---|---|
| `blog_archive_eyebrow` | text |
| `blog_archive_h1` | text |
| `blog_archive_lede` | textarea |
| `blog_newsletter_eyebrow` | text |
| `blog_newsletter_h2` | textarea |
| `blog_newsletter_lede` | textarea |
| `blog_newsletter_btn_label` | text (*bassa prio*) |

**Bassa prio (decorativi/strutturali):** `blog_featured_plate_label`, `blog_featured_cta_label`, `blog_grid_eyebrow_prefix`, `blog_grid_title`, `blog_empty_text` — opzionali.

**Totale DA MIGRARE: ~7 field core (+ ~5 bassa prio).** 0 immagini, 0 repeater. **Group target:** **NUOVO** `group_blog_archive_v1` attached a **Page 1413** (location `page == 1413`) — così la Page "Blog" finalmente serve a qualcosa (+ valutare di aggiungerla a `SALTELLI_SCF_ONLY_PAGES` per nascondere l'editor Gutenberg inutilizzato). **Template refactor:** `home.php` (sostituire le stringhe hero/newsletter con `saltelli_page_field(..., 1413)` o helper dedicato).
**Stima implementation:** ~25 min.
