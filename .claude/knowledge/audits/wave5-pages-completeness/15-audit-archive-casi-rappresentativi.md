# Audit completezza — Archive CPT saltelli_caso · `/chi-siamo/casi-rappresentativi/` · `archive-saltelli_caso.php`

**Render:** `archive-saltelli_caso.php` (CPT `saltelli_caso` archive — non c'è una Page WP). Header copy via `saltelli_option('archive_caso_*')` (Theme Options tab "Archive Headers").
**Group SCF attuale:** Theme Options `group_theme_options_v1` tab "Archive Headers" — 4 field già SCF: `archive_caso_eyebrow`, `archive_caso_h1_main`, `archive_caso_h1_emphasis`, `archive_caso_intro`.
**Priorità:** **GIÀ COMPLETA** — niente da fare (a parte 1 micro-field opzionale).

## Elementi frontend → sorgente

| Elemento frontend | File:linea | Sorgente attuale | Tipo | Decisione |
|---|---|---|---|---|
| Breadcrumb | :25 | `saltelli_render_breadcrumb()` | — | ⏸ auto |
| Hero eyebrow "§ Studio · Casi rappresentativi" | :15,26 | `saltelli_option('archive_caso_eyebrow')` | text | ✅ già SCF |
| Hero H1 "Casi / rappresentativi." | :16-17,27-40 | `saltelli_option('archive_caso_h1_main' / '_h1_emphasis')` | text + text | ✅ già SCF |
| Hero lede "Una selezione anonimizzata di pratiche dello Studio…" | :18,41 | `saltelli_option('archive_caso_intro')` | textarea | ✅ già SCF |
| Lista casi (data/anno, categoria, titolo, estratto) | :44-69 | loop CPT `saltelli_caso` + `get_field('data_caso')` + `caso_categoria` taxonomy + `get_the_excerpt` | — | ⏸ dynamic (CPT saltelli_caso — i singoli casi si editano dal CPT) |
| Pagination | :71-77 | `the_posts_pagination()` | layout | ⏸ struttura |
| Empty "Nessun caso pubblicato." | :80 | hardcoded | text | ⚠️ → `archive_caso_empty_text` (text) — *opzionale, bassissima prio* |

## Field SCF da aggiungere

| Field | Type |
|---|---|
| `archive_caso_empty_text` | text (*opzionale — bassissima prio*) |

**Totale DA MIGRARE: 0-1 field.** 0 immagini, 0 repeater. **Questa pagina è già al pattern eccellente** — header editoriale via SCF (Theme Options "Archive Headers"), singoli casi via CPT `saltelli_caso` (con featured image, `data_caso`, `caso_categoria`, excerpt — tutto editabile dal CPT). **Nessun template refactor necessario.**
**Stima implementation:** ~0 min (skip) — eventualmente ~5 min per il micro-field empty-state.
