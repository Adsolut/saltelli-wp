# Audit completezza — Archive CPT avvocato · `/chi-siamo/team/` · `archive-avvocato.php`

**Render:** `archive-avvocato.php` (CPT `avvocato` archive — non c'è una Page WP corrispondente). Header copy via `saltelli_option('archive_avvocato_*')` (Theme Options tab "Archive Headers").
**Group SCF attuale:** Theme Options `group_theme_options_v1` tab "Archive Headers" — 4 field già SCF: `archive_avvocato_eyebrow`, `archive_avvocato_h1_main`, `archive_avvocato_h1_emphasis`, `archive_avvocato_intro`. Il resto della pagina (aside trust, § Come lavoriamo, CTA finale) è **hardcoded**.
**Priorità:** P2 — non altissimo traffico, ma è una pagina visibile (linkata dall'hub Chi Siamo).

## Elementi frontend → sorgente

| Elemento frontend | File:linea | Sorgente attuale | Tipo | Decisione |
|---|---|---|---|---|
| Breadcrumb | :30 | `saltelli_render_breadcrumb()` | — | ⏸ auto |
| Hero eyebrow "§ Studio · Avvocati" | :20,31-33 | `saltelli_option('archive_avvocato_eyebrow')` | text | ✅ già SCF (Theme Options) |
| Hero H1 "Quattro / professionisti." | :21-22,34-43 | `saltelli_option('archive_avvocato_h1_main' / '_h1_emphasis')` | text + text | ✅ già SCF |
| Hero lede | :23,44 | `saltelli_option('archive_avvocato_intro')` | textarea | ✅ già SCF |
| Hero aside "trust" — eyebrow "§ Dal 1999" | :47-49 | hardcoded | text | ⚠️ → `archive_avvocato_trust_eyebrow` (text) |
| Hero aside "trust" — headline "Vannella Gaetani, 27. / Chiaia · Napoli." | :50-53 | hardcoded (2 `esc_html_e`) | textarea | ⚠️ → `archive_avvocato_trust_headline` (textarea) |
| Hero aside "trust" — text "Quattro avvocati, una pratica alla volta…" | :54-56 | hardcoded | textarea | ⚠️ → `archive_avvocato_trust_text` (textarea) |
| Grid 4 avvocati (ritratto, ruolo, nome, specs) | :60-110 | `get_posts('avvocato')` + thumbnail + `saltelli_field` (CPT) | — | ⏸ dynamic (CPT avvocato) |
| Empty "Nessun avvocato pubblicato." | :109 | hardcoded | text | ⏸ marginale |
| § Come lavoriamo — eyebrow + 3 principi (num, title, desc) | :112-142 | **HARDCODED letterale** (3 voci × num/title/desc, NON via CPT) | repeater | ⚠️ **DA MIGRARE → o riusare il CPT `saltelli_principio`** (come fa `page-lo-studio.php`) — coerenza! — **o repeater** `archive_avvocato_principles` {num:text, title:text, desc:textarea}. *Raccomando: riuso CPT `saltelli_principio`* (il fallback hardcoded è già duplicato qui e in page-lo-studio.php; consolidare). + eyebrow `archive_avvocato_principles_eyebrow` (text "§ Come lavoriamo"). |
| CTA finale — eyebrow "§ Pronto?" | :147 | hardcoded | text | ⚠️ → `archive_avvocato_cta_eyebrow` (text) |
| CTA finale — H2 "Vuoi raccontarci / la tua pratica?" | :149-152 | hardcoded (2 `esc_html_e`) | text + text | ⚠️ → `archive_avvocato_cta_h2_main` (text) + `archive_avvocato_cta_h2_em` (text) |
| CTA finale — p "Trenta minuti di prima consulenza gratuita…" | :153-155 | hardcoded | textarea | ⚠️ → `archive_avvocato_cta_p` (textarea) |
| CTA finale — bottone "Prenota un incontro" + `/contatti/` | :156-159 | hardcoded label + `home_url` | text + url | ⚠️ → `archive_avvocato_cta_btn_label` (text) + `archive_avvocato_cta_url` (url) |

## Field SCF da aggiungere

| Field | Type |
|---|---|
| `archive_avvocato_trust_eyebrow` | text |
| `archive_avvocato_trust_headline` | textarea |
| `archive_avvocato_trust_text` | textarea |
| `archive_avvocato_principles_eyebrow` | text |
| (3 principi → **riuso CPT `saltelli_principio`** — preferito — oppure repeater `archive_avvocato_principles`) | — / repeater |
| `archive_avvocato_cta_eyebrow` | text |
| `archive_avvocato_cta_h2_main` | text |
| `archive_avvocato_cta_h2_em` | text |
| `archive_avvocato_cta_p` | textarea |
| `archive_avvocato_cta_btn_label` | text |
| `archive_avvocato_cta_url` | url |

**Totale DA MIGRARE: ~10 field + (riuso CPT saltelli_principio).** 0 immagini. **Group target:** Theme Options tab "Archive Headers" espandi (o nuovo tab "Archive — Team body"). **Template refactor:** `archive-avvocato.php`. **Bonus consolidamento:** rendere i 3 principi qui = stessa fonte CPT di `page-lo-studio.php` (oggi sono 2 fallback hardcoded distinti).
**Stima implementation:** ~30 min.
