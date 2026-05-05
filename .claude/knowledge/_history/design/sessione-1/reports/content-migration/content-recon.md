# Content Recon — sito originale Saltelli (DB importato locale)

**Run date:** 2026-04-29
**DB:** `saltelli_wp` (Docker container `saltelli-db`)
**Scope:** discovery contenuti reali per migration verso CPT custom theme

---

## 1 · Pagine originali — 31 totali, ordinate per ID

| ID | Slug | Titolo | Char post_content | Note |
|---:|---|---|---:|---|
| 17 | home | Home | 9 687 | Hero + intro + grid pratica (probabilmente Elementor pesante) |
| 19 | chi-siamo | Chi siamo | 5 395 | **Lorem ipsum filler** — bio avvocati fake, NON usabile |
| 21 | servizi-legali | Servizi legali | 4 431 | Container area-pratica |
| 23 | contatti | Contatti | 897 | Form + map shortcode |
| 170 | recupero-crediti | Recupero Crediti | 4 179 | ✅ source CPT `recupero-crediti` |
| 202 | diritto-tributario | Diritto Tributario | 3 812 | ✅ source CPT `diritto-tributario` (Tier-1) |
| 208 | cartelle-esattoriali-e-multe | Cartelle esattoriali e multe | 2 846 | ✅ source CPT `cartelle-esattoriali-e-multe` |
| 223 | risarcimento-del-danno | Risarcimento del danno | 2 911 | ✅ source CPT `risarcimento-danni` |
| 232 | infortunistica-stradale | Infortunistica stradale | 2 510 | Source potenziale `responsabilita-civile` |
| 254 | immigrazione | Immigrazione | 2 438 | ✅ source CPT `diritto-dellimmigrazione` |
| 260 | aste-immobiliari | Aste immobiliari | 1 708 | NO CPT match (area niche, no scaffolded) |
| 273 | contrattualistica | Contrattualistica | 709 | NO CPT match diretto |
| 279 | responsabilita-medica | Responsabilità medica | 2 078 | ✅ source CPT `responsabilita-medica` |
| 285 | condominio-e-locazioni | Condominio e locazioni | 2 634 | ✅ source CPT `diritto-condominiale` |
| 288 | eredita-e-successioni | Eredità e successioni | 1 772 | ✅ source CPT `diritto-delle-successioni` |
| 292 | lavoro | Lavoro | 1 374 | ✅ source CPT `diritto-del-lavoro` (Tier-1) |
| 297 | diritto-bancario | Diritto bancario | 1 949 | ✅ source CPT `diritto-bancario` |
| 300 | diritto-societario | Diritto societario | 1 153 | NO CPT match (area abbandonata? non scaffolded) |
| 305 | domicilia-la-tua-azienda | Domicilia la tua azienda | 1 985 | ✅ source CPT `domiciliazione-dimpresa` |
| 321 | competenze | Competenze | 2 897 | Hub page archivio |
| 356 | conferma | Conferma | 760 | Thank-you page form |
| 361 | prenota-un-appuntamento | Prenota un appuntamento | 820 | Form pre-call |
| 372 | lavora-con-noi | Lavora con noi | 53 | Stub (53 char) |
| 947 | avvocato-divorzista | Avvocato Divorzista | 5 106 | ✅ source CPT `diritto-di-famiglia` |
| 996 | ricorsi-napoli-obiettivo-valore | Napoli Obiettivo Valore | 1 155 | Landing page localizzata |
| 1413 | blog | Blog | 648 | Hub blog |
| 1540 | infortunistica-stradale-italia | Infortunistica stradale italia | 2 230 | Variante landing nazionale |
| 1558 | avvocato-divorzista-italia | Avvocato Divorzista Italia | 4 994 | Variante landing nazionale (duplicato 947) |
| 2241 | invalidita-civile-diritto-previdenziale | Invalidità Civile e Diritto Previdenziale | 4 449 | ✅ source CPT `diritto-previdenziale` |
| 2246 | diritto-amministrativo | Diritto Amministrativo | 3 879 | ✅ source CPT `diritto-amministrativo` |
| 2251 | diritto-penale | Diritto Penale | 6 455 | ✅ source CPT `diritto-penale` |

**Marcatori sample reali** (verifica):
- p.202 = HTML pulito (`<h2>`, `<p>`, `<ul>`, `<b>`) — Elementor fa rendering server-side, `post_content` è già la versione finale, NO need to strip _elementor_data
- p.208 = stesso pattern — clean HTML
- p.19 (Chi siamo) = **Lorem ipsum** — **non usabile** per bio avvocati

---

## 2 · Mapping pagine source ↔ CPT competenza (preview)

13 di 19 CPT hanno una source page con buon contenuto. 6 CPT non hanno source diretto e otterranno solo content generato.

| CPT slug | CPT ID | Tier | Source page | Source ID | Source chars |
|---|---:|:--:|---|---:|---:|
| diritto-tributario | 2664 | **1** | diritto-tributario | 202 | 3 812 |
| diritto-del-lavoro | 2665 | **1** | lavoro | 292 | 1 374 |
| diritto-di-famiglia-lgbtq | 2666 | **1** | — (NESSUNA source) | — | 0 |
| cartelle-esattoriali-e-multe | 2667 | 2 | cartelle-esattoriali-e-multe | 208 | 2 846 |
| recupero-crediti | 2668 | 2 | recupero-crediti | 170 | 4 179 |
| diritto-di-famiglia | 2669 | 2 | avvocato-divorzista | 947 | 5 106 |
| responsabilita-medica | 2670 | 2 | responsabilita-medica | 279 | 2 078 |
| diritto-bancario | 2671 | 2 | diritto-bancario | 297 | 1 949 |
| diritto-condominiale | 2672 | 2 | condominio-e-locazioni | 285 | 2 634 |
| diritto-dellimmigrazione | 2673 | 2 | immigrazione | 254 | 2 438 |
| diritto-penale | 2674 | 2 | diritto-penale | 2251 | 6 455 |
| diritto-previdenziale | 2675 | 2 | invalidita-civile-diritto-previdenziale | 2241 | 4 449 |
| diritto-delle-assicurazioni | 2676 | 2 | — (NESSUNA source) | — | 0 |
| diritto-delle-successioni | 2677 | 2 | eredita-e-successioni | 288 | 1 772 |
| risarcimento-danni | 2678 | 2 | risarcimento-del-danno | 223 | 2 911 |
| responsabilita-civile | 2679 | 2 | infortunistica-stradale | 232 | 2 510 |
| domiciliazione-dimpresa | 2680 | 2 | domicilia-la-tua-azienda | 305 | 1 985 |
| consulenze-online | 2681 | 2 | — (NESSUNA source) | — | 0 |
| diritto-amministrativo | 2682 | 2 | diritto-amministrativo | 2246 | 3 879 |

**6 CPT senza source diretto:**
- `diritto-di-famiglia-lgbtq` (Tier-1!) — questo è il punto critico: nessuna source, contenuto va generato ex-novo (basato sulla strategia LGBTQ+ tutela validata da Antonia)
- `diritto-delle-assicurazioni` — area marginale, content da generare
- `consulenze-online` — non era pratica nel sito originale, nuovo servizio Adsolut + Saltelli

---

## 3 · Mapping autori blog ↔ CPT avvocato

| WP user | display_name | post count | CPT avvocato | CPT ID |
|---:|---|---:|---|---:|
| 1 | Emiliano Saltelli | **166** | Emiliano Saltelli | 2660 |
| 5 | Avv. Fabiana Saltelli | **99** | Fabiana Saltelli | 2661 |
| 4 | Avv. Antonia Battista | **49** | Antonia Battista | 2662 |
| 7 | Avv. Stefano Gaetano Tedesco | **3** | Stefano Gaetano Tedesco | 2663 |
| 6 | Gabriele Cascone | 9 | — (ex-collaboratore, NO CPT) | — |
| 8 | Adsolut Staff | 0 | — | — |
| 3 | Assistenza Tecnica | 0 | — | — |

**Totale post associabili a CPT attivi:** 166 + 99 + 49 + 3 = **317 di 326** (97.2%).
9 post by Gabriele Cascone restano associati al WP user, NON mappati a CPT.

---

## 4 · Categorie blog (10 categorie, 326 post pubblicati)

| Slug | Nome | Count |
|---|---|---:|
| informazioni-legali | Informazioni legali | 113 |
| diritto-del-lavoro | Diritto del lavoro | 92 |
| diritto-di-famiglia-ed-ereditario | Diritto di Famiglia ed Ereditario | 52 |
| diritto-tributario | Diritto tributario | 46 |
| contratti | Contratti | 22 |
| immigrazione | Immigrazione | 9 |
| diritto-previdenziale-invalidita | Diritto Previdenziale - Invalidità | 7 |
| diritto-successorio | Diritto successorio | 7 |
| diritto-societario | Diritto societario | 4 |
| diritto-condominiale | Diritto Condominiale | 3 |

NB: somma 355 > 326 → alcuni post in multiple categorie.

---

## 5 · Stato corrente CPT (pre-migration)

### CPT competenza (19 entries, ~225-265 char content stub)
- Tutti hanno `lead_breve` ed `answer_capsule` popolati con stringa identica (stub orchestrator)
- `is_tier_1_focus` settato correttamente solo su 2664 (verifica successiva su 2665, 2666)
- `body_extended`, `faq`, `casi_rappresentativi`, `articoli_correlati` tutti **vuoti**

### CPT avvocato (4 entries, ~70-120 char content stub)
- `_thumbnail_id = 2683` su CPT 2660 (Emiliano) — **gia' integrato dall'orchestrator, NON sovrascrivere**
- Altri 3 CPT (Fabiana, Antonia, Stefano) **senza foto** — placeholder, attesa Ludovica DOMANI
- `bio_breve`, `bio_estesa`, `specializzazioni`, `formazione` tutti **vuoti**

---

## 6 · Plugin attivi originali (non migrati al nuovo theme)

| Plugin | Stato | Note migrazione |
|---|---|---|
| Yoast SEO | Era attivo | Solo 1 record `_yoast_wpseo_title` su 31 pagine sample → meta description scarsa (Yoast spec stato vergine). Theme gestisce schema/OG inline, NO need import |
| Elementor | Era attivo | `_elementor_data` presente ma `post_content` ha già il rendering finale. **NO strip needed** |
| WonderPush / Brevo | Attivo | Non fa parte content migration |
| Duplicator | Attivo | Tools-only, ignorabile |

---

## 7 · Plan di migrazione (input per D2-D8)

| Step | Tipo intervento | CPT count | Output |
|---|---|---:|---|
| Body content | post_content extract da source | 13 CPT | post_content + lead_breve raffinato |
| Body content | generato ex-novo (no source) | 6 CPT | post_content + lead_breve nuovi |
| answer_capsule | rewrite tutti (40-60 word, GEO) | 19 CPT | meta `answer_capsule` |
| body_extended | tier-1 only | 3 CPT | wysiwyg 1500+ word |
| FAQ | tier-1 = 5, tier-2 = 3 | 19 CPT | repeater `faq` |
| casi_rappresentativi | tier-1 only | 3 CPT | repeater `casi_rappresentativi` |
| Avvocato bio | breve + estesa | 4 CPT | meta `bio_breve`, `bio_estesa` |
| Avvocato spec | array stringhe | 4 CPT | repeater `specializzazioni` |
| Author mapping | _wp_author_id | 4 CPT | meta `_wp_author_id` |

---

*Recon completato — pronto per D2 mapping JSON + D3-D4 migration script.*
