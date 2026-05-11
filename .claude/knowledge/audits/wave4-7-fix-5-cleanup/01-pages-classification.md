# Wave 4.7.fix.5 — Phase 1: Pages discovery + classification + borderline verify

**Data:** 2026-05-11 · **Branch:** `feat/wave4-7-fix-5-pages-cleanup-blog-doc`
**Fonte dati:** `wp post list` su staging (`db-pre-fix5-20260511-0755.sql` backup + `pages-snapshot.json` su droplet `~/backups/wave4-7-fix-5-pre-cleanup/`) + `pages-full-inventory.csv` (orchestratore) + verifica frontend `curl` + grep template tema.

---

## ⚠️ Deviazioni dal prompt (DA AUDITARE — orchestratore)

| # | Prompt dice | Realtà verificata | Decisione presa |
|---|---|---|---|
| 1 | **Page 2811 `lo-studio` → "DELETE — publish duplicate, già rediretto a `/chi-siamo/`"** | Page 2811 è una **Pagina viva**: URL `/chi-siamo/lo-studio/` → HTTP 200 · è il **target** del redirect `/lo-studio/` → `/chi-siamo/lo-studio/` (definito in `inc/seo/legacy-redirects.php:66` + `inc/setup.php:74-82`) · è linkata da `footer.php:270` · è in `SALTELLI_SCF_ONLY_PAGES` (Gutenberg disabled, `inc/admin/disable-gutenberg-for-scf-pages.php:32`) · documentata in `EDITOR-HANDOFF.md` v5.0 §5.0 / §5.6 come una delle 12 Pages SCF v5.0 ("Saltelli — Page Lo Studio: mission + lineage + faq") · `post_content` già bonificato in Wave 4.7.fix.4 (length=1). **Non è un duplicato: `/lo-studio/` (top-level, deprecato) ≠ Page 2811 (`/chi-siamo/lo-studio/`, viva). Il prompt confonde i due.** | **KEEP 2811.** Cancellarla sarebbe una regressione visibile + contraddice l'EDITOR-HANDOFF v5.0 scritto dall'orchestratore 1 giorno fa. |
| 2 | "16 KEEP — non toccare" | La lista KEEP esplicita nel prompt ha **18** elementi, non 16. +2811 (vedi #1) = **19 KEEP**. | Conteggio reale: **35 Pages → 19 KEEP + 16 DELETE** (13 draft + 361 + 356 + 2699). Il "16 Pages visibili" atteso dal prompt è un errore aritmetico. |
| 3 | "verifica redirect in `inc/redirects.php`" | Il file `inc/redirects.php` **non esiste**. I redirect 301 vivono in `wp-content/themes/saltelli/inc/seo/legacy-redirects.php` (mappa `saltelli_legacy_redirect_map()` A→C + `saltelli_mvp_to_audit_redirect_map()` B→C + pattern regex). | Lavoro su `inc/seo/legacy-redirects.php`. |
| 4 | Phase 2.A: "se 361 ha link → aggiungi 301 `/prenota-un-appuntamento/` → `/prenota-appuntamento/`" | `/prenota-un-appuntamento/` **redirige già** a `/contatti/` (`legacy-redirects.php:63`, redirect Elementor-era preservato). Hard rule #3 vieta di toccare entry esistenti. | Nessun nuovo redirect per `/prenota-un-appuntamento/`. Redirect esistente → `/contatti/` preservato. |
| 5 | (non previsto dal prompt) | Page 2699 `risultati` ha `post_parent=2811`, quindi il suo permalink reale è `/chi-siamo/lo-studio/risultati/` (HTTP 200, NON `/chi-siamo/risultati/` che è già rediretto). Cancellandola → 404 su un URL oggi vivo. | **Aggiunto 1 redirect**: `/chi-siamo/lo-studio/risultati/` → `/chi-siamo/casi-rappresentativi/` (estende pattern esistente `/chi-siamo/risultati/` → idem). |
| 6 | (non previsto dal prompt) | Menu item db_id **365** ("PRENOTA APPUNTAMENTO" → object_id 361) è ancora presente nel nav menu `Main` (term_id 3, **nessuna location assegnata → non visualizzato sul frontend**). È ridondante con menu item 3066 (→ 2714, la Pagina appuntamento reale). | Cancellato menu item 365 contestualmente al trash di 361 (cleanup; menu non visualizzato comunque). |
| 7 | (non previsto dal prompt) | `/conferma/` (Page 356) sarà 404 post-cleanup. Nessun target canonico ovvio (era una thank-you page Elementor-era, dati interni stale). | Nessun redirect aggiunto (hard rule #3 minimizzazione). 404 accettabile: URL legacy zero-traffico. Google lo droppa. |

**Note minori per l'orchestratore (non azionate — fuori scope):**
- `EDITOR-HANDOFF.md` §5.2 "Pagina `/casi/` (ID 2699)" è **stale** — verrà rimossa nell'update v6.0 (Phase 5).
- Nav menu `Main` (term_id 3, 26 voci, nessuna location) = cruft orfano Elementor-era. NON usato dal tema (il tema usa `Saltelli Header` term_id 996 su location `primary`). Cleanup completo del menu `Main` = wave futura separata.
- Post 600 `wp_navigation "Main"` (post type FSE Navigation block, status publish) contiene `/prenota-un-appuntamento/` nel serialized content → **inerte**: il tema classico non usa il blocco FSE Navigation. Nessuna azione.

---

## Classificazione finale (35 Pages)

### ✅ KEEP — 19 Pages (non toccare)

| ID | Title | Slug | URL | Parent | Note |
|---|---|---|---|---|---|
| 17 | Home | home | `/` | 0 | `page_on_front` · SCF metabox · Gutenberg disabled |
| 23 | Contatti | contatti | `/contatti/` | 0 | SCF metabox · Gutenberg disabled |
| 372 | Lavora con noi | lavora-con-noi | `/contatti/lavora-con-noi/` | 23 | SCF "Page Servizi" · Gutenberg disabled |
| 1413 | Blog | blog | `/risorse/blog/` | 2813 | `page_for_posts` · standard WP (no SCF) |
| 2695 | Costi e Consulenze | costi-e-consulenze | `/costi-e-consulenze/` | 0 | Hub · SCF nativo CPT-based · Gutenberg attivo |
| 2708 | Domande frequenti | domande-frequenti | `/risorse/domande-frequenti/` | 2813 | SCF metabox · Gutenberg disabled |
| 2709 | Guide gratuite | guide-gratuite | `/risorse/guide-gratuite/` | 2813 | SCF "Page Servizi" · Gutenberg disabled |
| 2710 | Glossario legale | glossario-legale | `/risorse/glossario-legale/` | 2813 | standard WP |
| 2711 | Prima consulenza | prima-consulenza | `/costi-e-consulenze/prima-consulenza/` | 2695 | SCF "Page Servizi" · Gutenberg disabled |
| 2712 | Come lavoriamo | come-lavoriamo | `/costi-e-consulenze/come-lavoriamo/` | 2695 | SCF "Page Servizi" · Gutenberg disabled |
| 2713 | Richiedi un preventivo | richiedi-preventivo | `/costi-e-consulenze/richiedi-preventivo/` | 2695 | SCF "Page Servizi" · Gutenberg disabled · 1 migrazione reale post_content→SCF (Wave 4.7.fix.4) |
| 2714 | Prenota un appuntamento | prenota-appuntamento | `/prenota-appuntamento/` | 0 | la Pagina appuntamento REALE (vs 361 legacy) |
| 2741 | Privacy Policy | privacy-policy | `/privacy-policy/` | 0 | standard WP |
| 2742 | Cookie Policy | cookie-policy | `/cookie-policy/` | 0 | standard WP |
| 2743 | Note legali | note-legali | `/note-legali/` | 0 | standard WP |
| 2811 | Chi Siamo* | lo-studio | `/chi-siamo/lo-studio/` | 2822 | **SCF "Page Lo Studio" · Gutenberg disabled · footer-linked · target redirect `/lo-studio/`** — vedi deviazione #1 |
| 2812 | Aree di Pratica | aree-di-pratica | `/aree-di-pratica/` | 0 | Hub · SCF metabox (2 tab) · Gutenberg disabled |
| 2813 | Risorse | risorse | `/risorse/` | 0 | Hub · SCF metabox (1 tab) · Gutenberg disabled |
| 2822 | Chi Siamo | chi-siamo | `/chi-siamo/` | 0 | Hub · SCF metabox (1 tab) · Gutenberg disabled |

\* Page 2811 ha `post_title` = "Chi Siamo" (quirk legacy: il title non corrisponde allo slug `lo-studio`). Frontend rende con `<title>Chi Siamo - Studio Legale Saltelli</title>`. Non è un bug bloccante; l'orchestratore può valutare di rinominare il title in "Lo Studio" via Pagine → 2811 (cosmetico, fuori scope di questa wave).

### 🗑️ DELETE — 13 draft orfani (cancellazione diretta → trash)

| ID | Title | Slug | post_date | Redirect copertura |
|---|---|---|---|---|
| 2241 | Invalidità Civile e Diritto Previdenziale | invalidita-civile-diritto-previdenziale | 2025-11-30 | `legacy_map`: `/invalidita-civile-diritto-previdenziale/` → `/aree-di-pratica/privati/diritto-previdenziale/` ✓ |
| 1558 | Avvocato Divorzista Italia | avvocato-divorzista-italia | 2025-05-08 | `legacy_map`: `/avvocato-divorzista-italia/` → `/aree-di-pratica/privati/diritto-di-famiglia/` ✓ |
| 1540 | Infortunistica stradale italia | infortunistica-stradale-italia | 2025-05-08 | `legacy_map`: `/infortunistica-stradale-italia/` → `/aree-di-pratica/privati/infortunistica-stradale/` ✓ |
| 996 | Napoli Obiettivo Valore | ricorsi-napoli-obiettivo-valore | 2025-01-10 | `legacy_map`: `/ricorsi-napoli-obiettivo-valore/` → `/aree-di-pratica/privati/cartelle-esattoriali-e-multe/` ✓ |
| 947 | Avvocato Divorzista | avvocato-divorzista | 2024-12-23 | `legacy_map`: `/avvocato-divorzista/` → `/aree-di-pratica/privati/diritto-di-famiglia/` ✓ |
| 321 | [Hub legacy] Competenze | competenze | 2020-01-09 | `mvp_map`: `/competenze/` → `/aree-di-pratica/` ✓ |
| 305 | Domicilia la tua azienda | domicilia-la-tua-azienda | 2020-01-09 | `legacy_map`: `/domicilia-la-tua-azienda/` → `/aree-di-pratica/imprese/domiciliazione-dimpresa/` ✓ |
| 300 | Diritto societario | diritto-societario | 2020-01-09 | `legacy_map`: `/diritto-societario/` → `/aree-di-pratica/` ✓ |
| 292 | Lavoro | lavoro | 2020-01-09 | `legacy_map`: `/lavoro/` → `/aree-di-pratica/privati/diritto-del-lavoro/` ✓ |
| 285 | Condominio e locazioni | condominio-e-locazioni | 2020-01-09 | `legacy_map`: `/condominio-e-locazioni/` → `/aree-di-pratica/privati/diritto-condominiale/` ✓ |
| 273 | Contrattualistica | contrattualistica | 2020-01-09 | `legacy_map`: `/contrattualistica/` → `/aree-di-pratica/` ✓ |
| 254 | Immigrazione | immigrazione | 2020-01-09 | `legacy_map`: `/immigrazione/` → `/aree-di-pratica/privati/diritto-dellimmigrazione/` ✓ |
| 21 | Servizi legali | servizi-legali | 2019-11-30 | `legacy_map`: `/servizi-legali/` → `/aree-di-pratica/` ✓ |

Tutti `draft` (mai pubblicate), tutti con un redirect 301 già attivo che copre il loro slug. Zero rischio SEO.

### 🗑️ DELETE — 3 publish orfani (post-verifica Phase 1.B)

| ID | Title | Slug | URL reale | Verifica | Decisione |
|---|---|---|---|---|---|
| 361 | Prenota un appuntamento | prenota-un-appuntamento | `/prenota-un-appuntamento/` (→ 301 → `/contatti/`) | `post_content` = cruft Elementor-era (`<h1>` duplicato, `tel:08119572180` numero VECCHIO, link `#elementor-action%3A...`). URL già rediretto a `/contatti/`. Referenziato da menu item 365 (nel menu orfano `Main`, non visualizzato). Superato da Page 2714. | **TRASH** + cancella menu item 365. Redirect `/prenota-un-appuntamento/` → `/contatti/` resta attivo (hard rule #3). |
| 356 | Conferma | conferma | `/conferma/` (HTTP 200, oggi vivo) | `post_content` = thank-you page Elementor-era con **dati STALE**: indirizzo "Rampe Brancaccio, 45 - 80132 Napoli" (vecchia sede!), telefono "081 181 31 119", "Ore 9:00–19:30". NON in nessun menu. CF7 forms attivi (2702 "Contact form 1", 2703 "Saltelli Contatti") **NON referenziano `/conferma/`** né in `post_content` né in `_additional_settings`/`_messages` meta (verificato: `wpcf7` option = solo version/bulk_validate; flow attuale usa risposta AJAX inline `mail_sent_ok`, non redirect). | **TRASH** — orphan legacy thank-you page, sostituita dal flow CF7 AJAX inline. Nessun redirect aggiunto (no target canonico). |
| 2699 | Casi rappresentativi | risultati | `/chi-siamo/lo-studio/risultati/` (HTTP 200, oggi vivo — nested sotto 2811) | `post_content` = 1 paragrafo lede ("Una selezione di casi rappresentativi..."). NON in nessun menu. Superato dall'archive CPT `/chi-siamo/casi-rappresentativi/` (template `archive-saltelli_caso.php` + intro SCF "Archive Headers"). NB: redirect `/chi-siamo/risultati/` → `/chi-siamo/casi-rappresentativi/` esiste già ma copre un URL DIVERSO da quello reale di 2699. | **TRASH** + aggiungi redirect `/chi-siamo/lo-studio/risultati/` → `/chi-siamo/casi-rappresentativi/` in `mvp_map`. |

### Riepilogo numerico

```
35 Pages totali
├── 19 KEEP    (17,23,372,1413,2695,2708,2709,2710,2711,2712,2713,2714,2741,2742,2743,2811,2812,2813,2822)
└── 16 DELETE  → trash (recuperabili 30gg + DB backup)
    ├── 13 draft orfani  (21,254,273,285,292,300,305,321,947,996,1540,1558,2241)
    └──  3 publish orfani (356 conferma, 361 prenota-un-appuntamento, 2699 risultati)
```

**Atteso post-Phase 2:** WP Admin → Pagine mostra 19 Pages (anziché 35).

---

## Verifica frontend Phase 1.B (curl, 2026-05-11)

| URL | HTTP | Note |
|---|---|---|
| `/chi-siamo/lo-studio/` | 200 | Page 2811 viva (body class `page-id-2811`) — KEEP |
| `/lo-studio/` | 301 → `/chi-siamo/lo-studio/` | redirect → target 2811 (KEEP) |
| `/chi-siamo/` | 200 | hub 2822 — KEEP |
| `/chi-siamo/risultati/` | 301 → `/chi-siamo/casi-rappresentativi/` | redirect esistente (non è l'URL di 2699) |
| `/chi-siamo/lo-studio/risultati/` | 200 | **= URL reale di Page 2699** — diventerà 404 post-trash → aggiungo redirect |
| `/chi-siamo/casi-rappresentativi/` | 200 | archive CPT `saltelli_caso` — KEEP |
| `/prenota-appuntamento/` | 200 | Page 2714 (la reale) — KEEP |
| `/prenota-un-appuntamento/` | 301 → `/contatti/` | Page 361 URL già rediretto — safe trash |
| `/conferma/` | 200 | **= URL di Page 356** — diventerà 404 post-trash (accettabile) |
| `/risorse/blog/` | 200 | blog archive — KEEP |

---

## Backup Phase 0 (su droplet `~/backups/wave4-7-fix-5-pre-cleanup/`)

- `db-pre-fix5-20260511-0755.sql` — full DB dump (59 MB, via `wp db export -` stdout — il path `~deploy/` non è scrivibile da `www-data`, fix vs prompt template originale)
- `pages-snapshot.json` — `wp post list --post_type=page --post_status=any` (5.1 KB)
- `pages-snapshot.csv` — idem CSV con `post_modified`

Rollback Pages: `wp post untrash <ID>` (entro 30gg) oppure restore DB dump.
