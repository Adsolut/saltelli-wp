# Audit completezza — Contatti (ID 23) · `/contatti/` · `template-parts/page-contatti.php`

**Render:** `page.php` → `get_template_part('template-parts/page', 'contatti')` (`is_page('contatti')`). Page in `SALTELLI_SCF_ONLY_PAGES`. Lettura: `saltelli_field('...', $pid, default)` + `saltelli_option('studio_*')` per i dati globali.
**Group SCF attuale:** `group_contatti_v1` (attached a Page 23). 8 field SCF (hero ×4 + map ×2 + come_arrivare_title + trust_signal). Buona base; mancano: eyebrow di sezione, success message, la 3-list "Come arrivare", l'eyebrow trust.
**Priorità:** P1 — pagina di conversione.

## Elementi frontend → sorgente

| Elemento frontend | File:linea | Sorgente attuale | Tipo | Decisione |
|---|---|---|---|---|
| Breadcrumb | :41,49-60 | `saltelli_get_breadcrumb_chain()` | — | ⏸ auto |
| Hero eyebrow "§ Contatti · Primo incontro gratuito" | :17,61 | `saltelli_field('hero_eyebrow')` | text | ✅ già SCF |
| Hero H1 "Contatti." | :18,62 | `saltelli_field('hero_h1_pre')` (+ `hero_h1_em` vuoto) | text + text | ✅ già SCF |
| Hero lede "Chiedi qualsiasi cosa. In qualsiasi momento." | :20,71-74 | `saltelli_field('hero_lede')` | textarea | ✅ già SCF |
| §01 eyebrow "§ 01 — Modulo" | :83 | hardcoded | text | ⚠️ → `contatti_form_eyebrow` (text) |
| §01 H2 "Prenota un primo / incontro gratuito." | :84-87 | hardcoded (2 `esc_html_e`) | text + text | ⚠️ → `contatti_form_h2_main` (text) + `contatti_form_h2_em` (text) |
| §01 form CF7 | :89-143 | `do_shortcode('[contact-form-7 …]')` (form "saltelli-contatti", ID 2703) | — | ⏸ gestito in Contact Form 7 (campi del form non in SCF tema) |
| §01 success message — eyebrow "§ Inviato" + h3 "Grazie. Ci sentiamo entro 24 ore." + text | :145-151 | hardcoded (3 `esc_html_e`) | text + text + textarea | ⚠️ → `contatti_success_eyebrow` (text) + `contatti_success_h3` (text) + `contatti_success_text` (textarea) |
| §02 aside eyebrow "§ 02 — Studio" | :155 | hardcoded | text | ⚠️ → `contatti_aside_eyebrow` (text) |
| §02 aside — Indirizzo (Via / CAP / Quartiere) | :157-171 | `saltelli_option('studio_indirizzo_via' / 'studio_cap_citta' / 'studio_quartiere')` | — | ⏸ global Studio Info |
| §02 aside — Mappa iframe + caption | :22-23,173-189 | `saltelli_field('map_iframe' / 'map_caption')` | textarea + text | ✅ già SCF |
| §02 aside — cta-list Telefono / Email / WhatsApp | :192-205 | `saltelli_option('studio_telefono_pubblico' / 'studio_email')` + `$studio['whatsapp']` | — | ⏸ global Studio Info |
| §02 aside — "Scrivi su WhatsApp" cta text | :203 | hardcoded | text | ⚠️ → `contatti_whatsapp_cta_label` (text) — *bassa prio* |
| §02 aside — Orari (settimana + sabato) | :207-217 | `saltelli_option('studio_orari_settimana' / 'studio_orari_sabato')` | — | ⏸ global Studio Info |
| §03 eyebrow "§ 03 — Come arrivare" | :224 | hardcoded | text | ⚠️ → `contatti_come_eyebrow` (text) |
| §03 H2 "Come arrivare." | :25,226 | `saltelli_field('come_arrivare_title')` | text | ✅ già SCF |
| §03 lista 3 voci (Metro/Auto/Treno + titolo + desc) | :227-243 | **HARDCODED** (9 `esc_html_e`) | repeater | ⚠️ **DA MIGRARE → `contatti_come_items` (repeater, 3 rows, sub: `label`:text + `title`:text + `desc`:textarea)** |
| §04 trust eyebrow "La nostra professionalità" | :250 | hardcoded | text | ⚠️ → `contatti_trust_eyebrow` (text) |
| §04 trust quote | :26,251-267 | `saltelli_field('trust_signal')` (split su ". " per render 3-line) | textarea | ✅ già SCF |

## Field SCF da aggiungere

| Field | Type |
|---|---|
| `contatti_form_eyebrow` | text |
| `contatti_form_h2_main` | text |
| `contatti_form_h2_em` | text |
| `contatti_success_eyebrow` | text |
| `contatti_success_h3` | text |
| `contatti_success_text` | textarea |
| `contatti_aside_eyebrow` | text |
| `contatti_whatsapp_cta_label` | text (*bassa prio*) |
| `contatti_come_eyebrow` | text |
| `contatti_come_items` | **repeater** (3 rows: label:text, title:text, desc:textarea) |
| `contatti_trust_eyebrow` | text |

**Totale DA MIGRARE: ~10 field + 1 repeater** — ~8 text + 1 textarea + 1 repeater. **0 immagini.** Il form CF7 e i dati Studio Info restano fuori SCF tema (gestiti altrove, correttamente).
**Group target:** `group_contatti_v1` espandi. **Template refactor:** `page-contatti.php`.
**Stima implementation:** ~35 min.
