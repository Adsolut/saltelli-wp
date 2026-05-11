# Audit completezza — Home (ID 17) · `/` · `front-page.php`

**Render:** `front-page.php` (page_on_front = 17). Helper di lettura: `saltelli_page_field()` (auto-resolve homepage_id) + `saltelli_option()` per i dati globali.
**Group SCF attuale:** `group_homepage_v1` (attached a Page 17). Già parzialmente eccellente — hero + studio + team/cases già SCF.
**Priorità:** P1 — pagina a traffico massimo.

## Elementi frontend → sorgente

| Elemento frontend | File:linea | Sorgente attuale | Tipo | Decisione |
|---|---|---|---|---|
| Hero eyebrow "Studio Legale · Napoli · Chiaia · Dal 1999" | front-page.php:14,77 | `saltelli_page_field('hero_eyebrow')` | text | ✅ già SCF |
| Hero H1 "Diritto, con misura." | :15,79-87 | `saltelli_page_field('hero_headline')` | text | ✅ già SCF |
| Hero subheadline | :16,89-91 | `saltelli_page_field('hero_subheadline')` | wysiwyg | ✅ già SCF |
| Hero CTA label + url | :17-18,93-96 | `saltelli_page_field('hero_cta_label' / 'hero_cta_url')` | text + url | ✅ già SCF |
| Hero cta-note "Prima consulenza conoscitiva — risposta entro 24 ore" | :98 | `esc_html_e` HARDCODED | text | ⚠️ DA MIGRARE → `hero_cta_note` (text) |
| Hero colophon (Indirizzo / Orari / Contatti) | :21-25,102-121 | `saltelli_option('colophon_*')` | text/textarea | ⏸ global (Studio Info — anche footer.php) — resta as-is |
| Hero "Coordinate" / "Scorri" labels | :103,125 | hardcoded | layout | ⏸ struttura |
| §01 eyebrow "§ 01 — Aree di pratica" | :132 | hardcoded | text | ⚠️ DA MIGRARE (bassa prio) → `home_areas_eyebrow` (text) |
| §01 H2 "Diciassette aree. / Tre presidiate in profondità." | :134-135 | hardcoded (2 `esc_html_e`) | text | ⚠️ DA MIGRARE → `home_areas_h2_main` (text) + `home_areas_h2_em` (text) |
| §01 hint "Passa il cursore su un'area per leggerne la sintesi." | :187 | hardcoded | text | ⚠️ DA MIGRARE (bassa prio) → `home_areas_preview_hint` (text) |
| §01 filtri tassonomia "Tutte" + term names | :140-143 | `get_terms('tipo-area')` | — | ⏸ dynamic (tassonomia) |
| §01 lista 17 aree (num, titolo, lead, tier) | :146-189 | `get_posts('competenza')` + `saltelli_field` (CPT) | — | ⏸ dynamic (CPT competenza) |
| §02 eyebrow "§ 02 — Lo studio" | :196 | hardcoded | text | ⚠️ DA MIGRARE (bassa prio) → `home_studio_eyebrow` (text) |
| §02 H2 "Un atelier, in senso napoletano." | :27,197 | `saltelli_page_field('studio_titolo_sezione')` | text | ✅ già SCF |
| §02 prosa drop-cap | :28,200-210 | `saltelli_page_field('studio_body')` | wysiwyg | ✅ già SCF |
| §02 foto facciata (img) | :29,212-226 | `saltelli_page_field('studio_foto_facciata')` | image | ✅ già SCF (Media Library) |
| §02 placeholder text (quando foto vuota) | :217-224 | hardcoded | layout | ⏸ placeholder/struttura |
| §03 eyebrow "§ 03 — Avvocati" | :233 | hardcoded | text | ⚠️ DA MIGRARE (bassa prio) → `home_team_eyebrow` (text) |
| §03 H2 "Quattro\nprofessionisti." | :31,234-247 | `saltelli_page_field('team_titolo')` | textarea (linee) | ✅ già SCF |
| §03 grid 4 avvocati (ritratto, ruolo, nome, specs) | :249-294 | `get_posts('avvocato')` + thumbnail + `saltelli_field` | — | ⏸ dynamic (CPT avvocato) |
| §04 eyebrow "§ 04 — Vittorie recenti" | :300 | hardcoded | text | ⚠️ DA MIGRARE (bassa prio) → `home_cases_eyebrow` (text) |
| §04 H2 "Casi rappresentativi." | :32,301 | `saltelli_page_field('cases_titolo')` | text | ✅ già SCF |
| §04 lista casi (id, descrizione, outcome) | :70,304-312 | `saltelli_homepage_cases()` → ACF `casi_rappresentativi_home` post_object (CPT) + fallback | — | ⏸ dynamic (helper + CPT/ACF) |
| Testimonials block | :316-321 | `get_template_part('testimonials-block')` → CPT trust items type=testimonianza | — | ⏸ dynamic (CPT) |
| §05 eyebrow "§ 05 — Parlano di noi" | :326 | hardcoded | text | ⚠️ DA MIGRARE (bassa prio) → `home_press_eyebrow` (text) |
| §05 lista press outlets | :71,327-331 | `saltelli_press_outlets()` → `saltelli_page_field('press_outlets', homepage_id)` repeater | repeater | ✅ già SCF (`press_outlets` su Page 17) |
| Trust bar block | :337-341 | `get_template_part('trust-bar')` → CPT trust items | — | ⏸ dynamic (CPT) |
| §06 eyebrow "§ 06 — Contatti" | :346 | hardcoded | text | ⚠️ DA MIGRARE (bassa prio) → `home_contact_eyebrow` (text) |
| §06 sub "Prima consulenza conoscitiva gratuita · Risposta entro 24 ore" | :349 | hardcoded | text | ⚠️ DA MIGRARE → `home_contact_subline` (text) |
| §06 H2 "Prenota / un primo / incontro." | :351-355 | hardcoded (3 `esc_html_e`) | textarea (3 linee) | ⚠️ DA MIGRARE → `home_contact_h2` (textarea) |
| §06 grid (Indirizzo/Telefono/Email) | :360-372 | `saltelli_option('colophon_*')` | — | ⏸ global Studio Info |
| §06 CTA bottone | :374-384 | `saltelli_option('cta_default_url' / 'cta_default_label' / 'cta_subline_italic')` | — | ✅ già SCF (CTA Defaults globale, Theme Options tab 6) |

## Field SCF da aggiungere

| Field | Type | Nota |
|---|---|---|
| `hero_cta_note` | text | sotto la CTA hero |
| `home_contact_subline` | text | eyebrow lungo sopra l'H2 contatti |
| `home_contact_h2` | textarea | "Prenota / un primo / incontro." (1 linea = 1 riga) |
| `home_areas_h2_main` | text | "Diciassette aree." |
| `home_areas_h2_em` | text | "Tre presidiate in profondità." (corsivo) |
| `home_areas_eyebrow` | text | "§ 01 — Aree di pratica" — *bassa prio* |
| `home_areas_preview_hint` | text | "Passa il cursore…" — *bassa prio* |
| `home_studio_eyebrow` | text | "§ 02 — Lo studio" — *bassa prio* |
| `home_team_eyebrow` | text | "§ 03 — Avvocati" — *bassa prio* |
| `home_cases_eyebrow` | text | "§ 04 — Vittorie recenti" — *bassa prio* |
| `home_press_eyebrow` | text | "§ 05 — Parlano di noi" — *bassa prio* |
| `home_contact_eyebrow` | text | "§ 06 — Contatti" — *bassa prio* |

**Totale DA MIGRARE: 12 field** (3 "core editoriale" + 7 eyebrow sezione bassa-prio + 2 `home_areas_h2_*`). Tutti `text` tranne 1 `textarea`. **0 immagini nuove** (la foto facciata è già SCF). **0 repeater nuovi** (press outlets già repeater SCF).
**Group target:** `group_homepage_v1` espandi. **Template refactor:** `front-page.php` (sostituire le `esc_html_e` con `saltelli_page_field(...)` con default = stringa attuale).
**Stima implementation:** ~40 min.
