# Audit completezza — Risorse HUB (ID 2813) · `/risorse/` · `template-parts/page-risorse-hub.php`

**Render:** `page.php` → `get_template_part('template-parts/page', 'risorse-hub')` (`is_page('risorse')`). Lettura: `saltelli_page_field()` (page_slug=risorse).
**Group SCF attuale:** `group_risorse_v1` (attached a Page 2813). 4 field SCF (hero). Le 4 resource-cards sono hardcoded.
**Priorità:** P1 hub.

## Elementi frontend → sorgente

| Elemento frontend | File:linea | Sorgente attuale | Tipo | Decisione |
|---|---|---|---|---|
| Breadcrumb | :29 | `saltelli_render_breadcrumb()` | — | ⏸ auto |
| Hero eyebrow "§ Risorse" | :21,30 | `saltelli_page_field('hub_risorse_eyebrow')` | text | ✅ già SCF |
| Hero H1 "Approfondire, / senza fretta." | :22-23,31-40 | `saltelli_page_field('hub_risorse_h1_main' / 'hub_risorse_h1_emphasis')` | text + text | ✅ già SCF |
| Hero lede/intro | :24,41 | `saltelli_page_field('hub_risorse_intro')` | textarea/wysiwyg | ✅ già SCF |
| Card 01 "Blog" — num "01 / 04" | :50 | hardcoded | layout | ⏸ struttura |
| Card 01 "Blog" — titolo | :51 | hardcoded | text | ⚠️ → `hub_risorse_card1_title` (text) |
| Card 01 "Blog" — desc "Articoli scritti dai nostri avvocati…" | :52-54 | hardcoded | textarea | ⚠️ → `hub_risorse_card1_desc` (textarea) |
| Card 01 "Blog" — "X articoli" count | :55-62 | `wp_count_posts('post')` | — | ⏸ dynamic |
| Card 01 "Blog" — cta "Leggi →" | :63 | hardcoded | text | ⚠️ → `hub_risorse_card1_cta` (text) |
| Card 02 "Domande frequenti" — titolo + desc + cta "Apri le FAQ →" | :69-81 | hardcoded (+ count `wp_count_posts('saltelli_faq')` dynamic) | text + textarea + text | ⚠️ → `hub_risorse_card2_title/desc/cta` |
| Card 03 "Glossario legale" — titolo + desc + cta "Sfoglia →" | :87-91 | hardcoded (nessun count) | text + textarea + text | ⚠️ → `hub_risorse_card3_title/desc/cta` |
| Card 04 "Guide gratuite" — titolo + desc + cta "Scarica →" | :97-111 | hardcoded (+ count dynamic + fallback "In arrivo") | text + textarea + text + text | ⚠️ → `hub_risorse_card4_title/desc/cta` + `hub_risorse_card4_empty_text` ("In arrivo") |
| Card 01-04 URL | :49,67,85,95 | `home_url()` hardcoded | url | ⏸ routing fisso (struttura IA) |

**Nota:** questa template part **non ha una CTA section finale** (finisce dopo la grid).

## Field SCF da aggiungere

**Opzione A — per-card:** `hub_risorse_card{1,2,3,4}_title` (text ×4), `_desc` (textarea ×4), `_cta` (text ×4) = 12 + `hub_risorse_card4_empty_text` (text) = **13 field**.
**Opzione B — repeater:** `hub_risorse_cards` (repeater, 4 rows, sub: title:text + desc:textarea + cta:text) + `hub_risorse_card4_empty_text` (text) = **~3 field + 1 repeater**. (Le 4 card sono fisse e legate al routing IA — Opzione A più chiara, ma il repeater è accettabile se in futuro si vogliono riordinare.)

**Totale DA MIGRARE: ~13 field (Opt A) / 1 repeater + 1 field (Opt B).** 0 immagini, 0 repeater obbligatori. **Raccomando Opzione A** (4 card fisse).
**Group target:** `group_risorse_v1` espandi. **Template refactor:** `page-risorse-hub.php`.
**Stima implementation:** ~30 min.
