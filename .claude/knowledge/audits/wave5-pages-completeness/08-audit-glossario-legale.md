# Audit completezza — Glossario legale (ID 2710) · `/risorse/glossario-legale/` · `inc/wave3-glossario.php`

**Render:** `page.php` → `include SALTELLI_THEME_DIR . '/inc/wave3-glossario.php'` (`is_page('glossario-legale')`). Confermato via curl: body class `page-id-2710`, `sl-gloss`. **Il `post_content` della Page 2710 è ignorato** (il template è specializzato).
**Group SCF attuale:** **NESSUNO**. File ~573 righe, **interamente hardcoded**: i ~60 termini sono un array PHP (`$sl_gloss_terms` :41), le ~N FAQ in fondo un array PHP (`$sl_gloss_faq` :284), hero/CTA `esc_html_e`. Anche lo schema JSON-LD `DefinedTermSet`+`DefinedTerm`×60 + `FAQPage` è inline.
**Priorità:** P2/P3 — non alto traffico; ma i 60 termini ARE editorial content (definizioni legali) → migrazione "vera" = lift importante (nuovo CPT).

## Elementi frontend → sorgente

| Elemento frontend | File:linea | Sorgente attuale | Tipo | Decisione |
|---|---|---|---|---|
| Breadcrumb | (hero) | `saltelli_render_breadcrumb()` | — | ⏸ auto |
| Hero eyebrow "§ Riferimenti · Glossario" | :337 | hardcoded | text | ⚠️ → `glossario_hero_eyebrow` (text) |
| Hero H1 | :339-348 | hardcoded | text/textarea | ⚠️ → `glossario_hero_h1` (text/textarea) |
| Hero lede "Sessanta termini essenziali del diritto italiano…" | :351-353 | hardcoded | textarea | ⚠️ → `glossario_hero_lede` (textarea) |
| Hero counter "X termini · Y categorie" | :354-365 | `count($sl_gloss_terms)` | — | ⏸ dynamic (count) |
| Search input + label "Cerca un termine" | :368-375 | hardcoded | layout | ⏸ struttura/JS |
| Nav A-Z | :376-385 | hardcoded (lettere) | layout | ⏸ struttura/JS |
| Empty state "Nessun risultato. Prova un altro termine." | :389-391 | hardcoded | text | ⚠️ → `glossario_empty_text` (text) — *bassa prio* |
| **Lista ~60 termini** (`<dl>`: termine `<dt>` + definizione `<dd>` + categoria + aree correlate) | :41-282, :393+ | **HARDCODED array PHP `$sl_gloss_terms`** | — | ⚠️ **DA MIGRARE — opzione "grande": nuovo CPT `glossary_term`** con campi: `term`:text (titolo) + `definition`:wysiwyg + `letter`:text/select (A-Z) + `category`:select/taxonomy + `related_areas`:post_object multi (CPT competenza). Migration: importare i ~60 termini dall'array nel CPT. **Lift: ~3-4 ore** (CPT + ACF group + migration script + refactor render + refactor JSON-LD DefinedTermSet). |
| **Sezione FAQ in fondo** (`details`/`summary`) | :284-298, (FAQ render) | **HARDCODED array PHP `$sl_gloss_faq`** | — | ⚠️ DA MIGRARE → riusare il CPT `saltelli_faq` filtrato per topic "glossario", OPPURE repeater `glossario_faq` {q:text, a:wysiwyg}. *Medio lift.* |
| CTA finale | (fine file) | hardcoded | text/url | ⚠️ → `glossario_cta_eyebrow` + `glossario_cta_h2` (textarea) + `glossario_cta_btn_label` + `glossario_cta_url` |
| Schema JSON-LD DefinedTermSet + FAQPage | inline | derivato da `$sl_gloss_terms` / `$sl_gloss_faq` | — | ⏸ dev (si aggiornerebbe automaticamente con la migrazione a CPT) |

## Field SCF da aggiungere

**Fase 1 (quick, ~20 min) — solo hero + CTA:**
| Field | Type |
|---|---|
| `glossario_hero_eyebrow` | text |
| `glossario_hero_h1` | text |
| `glossario_hero_lede` | textarea |
| `glossario_empty_text` | text (*bassa prio*) |
| `glossario_cta_eyebrow` | text |
| `glossario_cta_h2` | textarea |
| `glossario_cta_btn_label` | text |
| `glossario_cta_url` | url |

→ ~7-8 field, group NUOVO `group_glossario_v1` attached a Page 2710.

**Fase 2 (lift grande, ~3-4 ore, wave separata) — i 60 termini + le FAQ:**
- Nuovo CPT `glossary_term` (5 ACF field: term, definition wysiwyg, letter, category, related_areas) + migration script (array → CPT) + refactor render `<dl>` + refactor JSON-LD `DefinedTermSet`.
- FAQ glossario → riuso CPT `saltelli_faq` (topic dedicato) o repeater.

**Totale DA MIGRARE: ~8 field (Fase 1) + 1 nuovo CPT + ~5 CPT-field (Fase 2).** 0 immagini. **Group target:** NUOVO `group_glossario_v1` (Fase 1) + NUOVO CPT `glossary_term` (Fase 2). **Template refactor:** `inc/wave3-glossario.php`.
**Stima implementation:** ~20 min (Fase 1) + ~3-4 ore (Fase 2, opzionale/separata). **Raccomandazione: fare Fase 1 nello STEP 3; valutare Fase 2 come Wave a sé** (richiede anche di rifare il JSON-LD).
