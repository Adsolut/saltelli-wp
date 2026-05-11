# Audit completezza — Costi e Consulenze HUB (ID 2695) · `/costi-e-consulenze/` · `template-parts/page-costi-e-consulenze-hub.php`

**Render:** `page.php` → `get_template_part('template-parts/page', 'costi-e-consulenze-hub')` (`is_page('costi-e-consulenze')`). Confermato via curl: body class `page-id-2695`, `sl-hub-hero`.
**Group SCF attuale:** **NESSUNO** attached a questa template part. ⚠️ **Il template è interamente hardcoded** — 0 field editabili. (NB: esiste un `group_costi_v1` + un `template-parts/page-costi.php`, ma sono per uno slug `costi` che **non esiste più** nel DB — vedi "bug" sotto.)
**Priorità:** **P1 ALTA** — alto traffico + 0 field oggi + tutto hardcoded → la pagina con il gap più grande in assoluto.

## Elementi frontend → sorgente

| Elemento frontend | File:linea | Sorgente attuale | Tipo | Decisione |
|---|---|---|---|---|
| Breadcrumb | :18 | `saltelli_render_breadcrumb()` | — | ⏸ auto |
| Hero eyebrow "§ Costi e consulenze" | :19 | hardcoded `esc_html_e` | text | ⚠️ → `hub_costi_eyebrow` (text) |
| Hero H1 "Trasparenza, / non sorprese." | :22-30 | hardcoded (2 `esc_html__`) | text + text | ⚠️ → `hub_costi_h1_main` (text) + `hub_costi_h1_em` (text) |
| Hero lede "Modalità di consulenza, scenari di costo, processo di lavoro. Quello che chiediamo lo scrivi prima." | :31-33 | hardcoded | textarea/wysiwyg | ⚠️ → `hub_costi_intro` (textarea o wysiwyg) |
| Card 01 "Costi" — num "01 / 04" | :42 | hardcoded | layout | ⏸ struttura |
| Card 01 "Costi" — titolo | :43 | hardcoded | text | ⚠️ → `hub_costi_card1_title` (text) |
| Card 01 "Costi" — desc "Tre scenari tipo, range chiari…" | :44-46 | hardcoded | textarea | ⚠️ → `hub_costi_card1_desc` (textarea) |
| Card 01 "Costi" — cta "Scopri i costi →" | :47 | hardcoded | text | ⚠️ → `hub_costi_card1_cta` (text) |
| Card 01 — URL `/costi-e-consulenze/costi/` | :41 | `home_url()` hardcoded | url | ⏸ routing — ⚠️ **MA: questa pagina figlia NON esiste** (vedi bug sotto) |
| Card 02 "Prima consulenza" — titolo + desc + cta "Prenota →" | :53-58 | hardcoded | text + textarea + text | ⚠️ → `hub_costi_card2_title/desc/cta` |
| Card 03 "Come lavoriamo" — titolo + desc + cta "Approfondisci →" | :63-68 | hardcoded | text + textarea + text | ⚠️ → `hub_costi_card3_title/desc/cta` |
| Card 04 "Richiedi preventivo" — titolo + desc + cta "Richiedi →" | :73-78 | hardcoded | text + textarea + text | ⚠️ → `hub_costi_card4_title/desc/cta` |
| Card 02-04 URL | :51,61,71 | `home_url()` hardcoded | url | ⏸ routing fisso (queste pagine figlie esistono: 2711, 2712, 2713) |

**Nota:** nessuna CTA section finale in questo template (finisce dopo la grid).

## ⚠️ Bug fuori scope (da segnalare all'orchestratore)

- La card 01 "Costi" linka a **`/costi-e-consulenze/costi/`** — ma **non esiste una Page con slug `costi`** sotto 2695 (i child di 2695 sono 2711 `prima-consulenza`, 2712 `come-lavoriamo`, 2713 `richiedi-preventivo`). Quindi quel link → **404**. Il template `template-parts/page-costi.php` + il group `group_costi_v1` (hero/aside/body "Come calcoliamo"/CTA, ~13 field) esistono ma **non sono mai invocati** (nessuna Page ha slug `costi`; il vecchio `/costi/` ridireziona a `/costi-e-consulenze/` cioè 2695, che usa il template HUB, non `page-costi.php`). → o si crea una Page figlia `costi` (e si wira `page-costi.php` + `group_costi_v1`), o si rimuove la card 01 dal HUB, o si fa puntare la card 01 altrove. Decisione orchestratore.
- L'`EDITOR-HANDOFF.md` §5.1 ("Pagina `/costi/` (ID 2695)") è stale di conseguenza — descrive `group_costi_v1` come attivo su 2695, ma 2695 usa il template HUB hardcoded.

## Field SCF da aggiungere

| Field | Type |
|---|---|
| `hub_costi_eyebrow` | text |
| `hub_costi_h1_main` | text |
| `hub_costi_h1_em` | text |
| `hub_costi_intro` | textarea (o wysiwyg) |
| `hub_costi_card{1,2,3,4}_title` | text ×4 |
| `hub_costi_card{1,2,3,4}_desc` | textarea ×4 |
| `hub_costi_card{1,2,3,4}_cta` | text ×4 |

**Totale DA MIGRARE: ~16 field** — ~12 text + ~5 textarea. 0 immagini, 0 repeater (4 card fisse). **Group target:** **NUOVO** `group_costi_e_consulenze_hub_v1` attached a Page 2695 (location `page == 2695` o `page_slug == costi-e-consulenze`). **Template refactor:** `page-costi-e-consulenze-hub.php`.
**Stima implementation:** ~35 min. **Priorità: fare per primo** — è la pagina che oggi Elena non può toccare per niente.
