---
title: Design Handoff — Strategy Recommendation (per orchestratore)
date: 2026-05-12
author: Claude Code (branch audit/design-handoff-strategy)
inputs: 01-jsx-inventory.md · 02-jsx-to-wp-mapping.md · 03-tokens-reconciliation.md · CLAUDE.md · docs/DESIGN.md · docs/ARCHITECTURE.md · docs/EDITOR-HANDOFF.md v6.0 · ../wave5-step4-sections-cleanup/02-phantom-values-remaining.md
status: read-only audit · NESSUNA modifica a tema/JSX/CSS/SCF · raccomandazione operativa
---

# RECOMMENDATION — come applicare il Design Handoff

> **TL;DR.** I 15 JSX sono le _design source_ di pagine **già costruite** (Sessione 1+2+Logo v1.1).
> Il bundle è un **re-handoff** → il lavoro è **verifica + drift cleanup CSS** (tipografia/spacing/
> letter-spacing — stesso problema dei ~460 phantom di Wave 5 STEP 4), **1 ADDITIVE** (pull-quote
> "caso simbolo" in archive-casi, ~4 field), e **2 decisioni di prodotto** da prendere con Duccio
> (mappa iframe sì/no in /contatti/; chi-siamo JSX → quale template). **Zero refactor SCF.**
> SoT design tokens = **`tokens.css` corrente (Wave 5 STEP 2) vince**; il bundle CSS è obsoleto.
> Quick win: wave `chrome` (header). 12 mini-wave, ~13–17h dev totali.

---

## J. SCF Data Contract Compliance Audit (hard invariant — PRIMA)

Classificazione complessiva per ognuno dei 15 JSX (dettaglio per-elemento in `02-jsx-to-wp-mapping.md`):

| # | JSX | → template WP | Esito SCF | Cosa serve |
|---|---|---|---|---|
| 1 | `home/index.jsx` | `front-page.php` | 🟢 **COMPLIANT** | nulla di schema. `group_homepage_v1` copre tutto. Solo riconciliazione `default_value` ("19" vs "17 aree" — additive). |
| 2 | `home/mobile.jsx` | `front-page.php` (responsive CSS) | 🟢 **COMPLIANT** | nulla — è la variante mobile dello stesso template/data contract. |
| 3 | `chrome/index.jsx` (header) | `header.php` + `logo.css` | 🟢 **COMPLIANT** | nulla. Nav = `wp_nav_menu('primary')`, telefono = `saltelli_option`. |
| 4 | `footer/index.jsx` | `footer.php` | 🟢 **COMPLIANT** | nulla obbligatorio. 🟡 opzionale bassa-prio: rendere editabili i copy hardcoded di newsletter/precta (nuovi field). |
| 5 | `logo/index.jsx` | `header.php` + `footer.php` + `logo.css` + favicon | 🟢 **COMPLIANT** | nulla (logo = markup statico + CSS). |
| 6 | `design-system/index.jsx` | — (showcase, non un template) | **n/a** | nulla. Informativo. |
| 7 | `chi-siamo/index.jsx` | **`template-parts/page-lo-studio.php`** (Page 2811) — vedi §"⚠️ MAPPING DECISION" sotto | 🟢 **COMPLIANT** se mappato a lo-studio | `group_lo_studio_v1` (539 righe) copre hero/lede/founding/team/principi/timeline/CTA. 🟡 forse 1 `image` field per "Plate I" facciata se assente (coperto da Wave 5.1 comunque). |
| 8 | `single-avvocato/index.jsx` | `single-avvocato.php` | 🟢 **COMPLIANT** | nulla. `group_avvocato_v1` copre tutto. `bio_estesa` resta HARD-PROTECTED (mai scrivere). |
| 9 | `blog-archive/index.jsx` | `home.php` | 🟢 **COMPLIANT** | nulla (blog = WP-native). 🟡 opzionale: copy newsletter inline editabile. |
| 10 | `archive-casi/index.jsx` | `archive-saltelli_caso.php` | 🟡 **ADDITIVE** | ~4 field nuovi nel tab "Archive Headers" per la sezione pull-quote "caso simbolo" (`archive_caso_simbolo_eyebrow/number/quote/attr`) — oppure 1 `post_object`→`saltelli_caso` + 2 override testuali. Il `outcome_label` per-caso **esiste già** (`group_caso_item_v1`). Filtri tab `caso_categoria` = template+JS, no SCF. |
| 11 | `glossario-legale/index.jsx` | `inc/wave3-glossario.php` (Page 2710) | 🟢 **COMPLIANT** | nulla. Termini hardcoded in PHP **per scelta** (stesso sorgente del JSX) — stato pre-esistente, non un break. Migrazione termini→CPT = backlog separato. |
| 12 | `taxonomy-tipo-area/index.jsx` | `taxonomy-tipo-area.php` + `group_tipo_area_term_v1` | 🟢 **COMPLIANT** | nulla. Il group per-term (Wave 5 STEP 3 coverage) ha già i 3 scenari + tutte le sezioni. |
| 13 | `single-competenza-tier1/index.jsx` | `single-competenza.php` (branch tier-1) + `group_competenza_v1` | 🟢 **COMPLIANT** | nulla. Tutti i blocchi (capsule/body/avvocato-card/casi/faq/correlati/CTA) hanno il loro field. |
| 14 | `contatti/index.jsx` | `template-parts/page-contatti.php` (Page 23) + `group_contatti_v1` | 🔴 **RE-INTERPRETATION** (solo mappa iframe) — resto 🟢 COMPLIANT | il JSX **re-introduce un `<iframe>` OpenStreetMap** che era stato rimosso volutamente (v0.17.3 "sede no-iframe"). Vedi §"DESIGN RE-INTERPRETATION" sotto. Niente schema SCF cambia. Refinement non-bloccante: select "Area di interesse" → renderlo dinamico (term `tipo-area`/CPT `competenza`) invece di 19 hardcoded. |
| 15 | `404/index.jsx` | `404.php` | 🟢 **COMPLIANT** | nulla. Copy hardcoded (è un 404), conteggio aree già dinamico. |

**Conteggio: 🟢 COMPLIANT ×12 · 🟡 ADDITIVE ×1 (archive-casi; +2 opzionali bassa-prio) · 🔴 RE-INTERPRETATION ×1 (contatti, solo mappa) · n/a ×1.**

### ⚠️ MAPPING DECISION — chi-siamo/index.jsx → quale template?

Il JSX "chi-siamo" è una **pagina editoriale completa** (hero asimmetrico drop-cap + lede drop-cap "U"
+ Plate I facciata + sezione "1999." + 4 lawyer card + 3 principi + timeline 1999→2026 + CTA finale).
**Questo NON è l'attuale `/chi-siamo/`** (= `page-chi-siamo-hub.php`, solo 3 card di navigazione).
È invece **esattamente la struttura di `page-lo-studio.php`** + il suo `group_lo_studio_v1` (il group
SCF più ricco del progetto, con hero/lede/founding/team/principi/timeline tutti previsti).

**Raccomandazione**: trattare il nome cartella "chi-siamo" come misnomer del bundle e mappare il JSX
a **`template-parts/page-lo-studio.php`** (Page 2811). Non rompe niente — `group_lo_studio_v1` già
attende quei campi e Elena li ha (probabilmente) popolati. Il "/chi-siamo/" hub-of-cards resta com'è.
*Se* invece Duccio vuole che `/chi-siamo/` diventi la pagina editoriale completa e sparisca il
sotto-livello `/lo-studio/` — quella sì sarebbe una decisione IA da rinegoziare (impatto su redirect,
menu, e sul `group_chi_siamo_v1` che andrebbe espanso). **Default raccomandato: mappa a lo-studio,
nessun cambio IA.**

### DESIGN RE-INTERPRETATION dettagliata

**RE-INTERPRETATION #1 — `/contatti/`: mappa OpenStreetMap iframe re-introdotta**
- **Cosa richiede Design**: un `<iframe src="openstreetmap.org/export/embed.html?bbox=...&marker=40.8316,14.2400">` 320px alto, `filter: grayscale(0.85) contrast(1.05)`, con un badge "Chiaia · Napoli" overlay.
- **Cosa "rompe"**: la decisione deliberata `v0.17.3` — "Sede text-only (iframe rimosso)" / "sede no-iframe" (vedi CLAUDE.md changelog + `0.17.x consolidation log`). Niente di tecnico si rompe, ma si annulla una scelta esplicita di prodotto (probabilmente per privacy/performance/no-3rd-party-embed).
- **Impatto Elena**: nessuno (la mappa non è un field SCF; il "Come arrivare" 3-col Metro/Auto/Treno text che il JSX ha *in aggiunta* alla mappa è già implementato in `page-contatti.php`).
- **Raccomandazione**: **mantenere no-iframe.** Implementare tutto il resto del JSX `/contatti/` (hero, form, NAP, "Come arrivare" 3-col, CTA dirette, orari, trust signal italic) — la mappa è l'unico elemento da NON portare. Rinegoziare con Duccio solo se vuole consapevolmente re-introdurla (in tal caso: valutare un embed lazy + `loading="lazy"` + consent-gating Iubenda).

**RE-INTERPRETATION #2 — `chi-siamo/index.jsx` template mapping** (vedi sopra) — non è un refactor strutturale, è una decisione di routing. **Default: mappa a `page-lo-studio.php`. Nessun cambio IA salvo diversa volontà di Duccio.**

Nessun'altra "re-interpretation". In particolare: **nessun JSX implica** refactor di group SCF, cambio location rule, cambio del disable-Gutenberg pattern sui 13 Pages SCF-only, cambio admin path, cambio CPT/taxonomy registration. Il "data contract" Elena-approved è LOCKED e i JSX lo rispettano (il contenuto dinamico è CPT/taxonomy-driven; gli array hardcoded nei JSX sono placeholder di design).

---

## A. SoT Design Tokens Decision (critica)

**Raccomandazione: `tokens.css` corrente (Wave 5 STEP 2, derivato da `docs/DESIGN.md`) VINCE.
`tokens-design-bundle.css` è informativo/obsoleto. Nessun update a `docs/DESIGN.md`.**

Argomenti (pro/contro):
1. **Pro KEEP CURRENT** — il bundle CSS è preliminare: usa `--fs-display: clamp(48px,8vw,120px)`,
   `--fs-h1: clamp(36px,5vw,64px)`, `--lh-display: 1.05`, `--lh-body: 1.65`, un solo
   `letter-spacing: -0.01em`. Sono i valori *pre-Wave-5-STEP-2*. DESIGN.md (la SoT documentata) ha
   `clamp(80px,9vw,132px)` / `clamp(48px,6vw,96px)` / `0.98` / `1.7` / 4 letter-spacing ottici.
2. **Pro KEEP CURRENT** — **gli stessi JSX del bundle non usano i token del bundle**: hardcodano
   inline i `clamp()` GRANDI (`clamp(80px,9vw,132px)` hero, `clamp(64px,8vw,132px)`/`clamp(72px,9vw,140px)`
   h1 archive, `lineHeight: 0.98`/`0.95`, `letterSpacing: -0.035em`/`-0.025em`/`-0.015em`). Riflettono
   un modello mentale **più vicino a current che al bundle CSS**. Il bundle CSS ha semplicemente lagged.
3. **Pro KEEP CURRENT** — adottare il bundle = **regressione di DESIGN.md** (riscrivere all'indietro
   le specifiche tipografiche) + invalidare Wave 5 STEP 4 (328 swap fatti verso i token current) +
   rischiare il contenuto Elena-approved sulle 19 Pages.
4. **Contro (a favore del bundle)** — il bundle ha un `--fs-body: clamp(16px,1.1vw,18px)` fluido che
   current non ha; e il phantom-doc §3d chiede proprio di introdurre un `--fs-body-fluid`. → **non è un
   motivo per adottare il bundle**, ma è un input per la phantom-resolution wave: valutare
   `--fs-body-fluid: clamp(16px,1.4vw,18px)` (cambia computed → design sign-off + Playwright diff).
5. **Contro (parziale)** — `--fs-h2: clamp(28px,3.5vw,44px)` è **identico** in bundle e current; le 7
   variabili colore sono identiche; spacing identico (solo nome `--sp-*` vs `--s-*`); `--ease-soft` ===
   `--ease-editorial`. → l'overlap c'è, ma le divergenze (display/h1/h3/body/lh/ls) sono tutte "il
   bundle è la versione vecchia".

**Impatto su `docs/DESIGN.md`**: nessuno. (Eventuale nota nel README del bundle: "tokens-design-bundle.css
= reference informativo, NON SoT — usare `tokens.css`" — lo dice già, basta non perderlo di vista.)

**Impatto su Elena**: nessuno. Token invariati → frontend invariato → il suo contenuto SCF resta valido.

**Regola applicativa durante l'implementazione** (vedi `03-tokens-reconciliation.md §4`): quando un JSX
hardcoda inline un valore non in `tokens.css` → se matcha un token → `var(--token)` (computed-neutral);
se è un phantom catalogato (Wave 5 STEP 4 doc) → segui quel piano; se è genuinamente nuovo (es.
`clamp(72px,10vw,160px)` del competenza-tier1 h1) → decisione per-selector. **Mai toccare `:root`.**

---

## B. Prioritization

Poiché le pagine sono già costruite, "implementation" = **verifica fedeltà + drift cleanup CSS sui
selettori toccati + le ADDITIVE + le 2 decisioni**. Ordine raccomandato (criterio: globale-prima,
alto-traffico, basso-rischio, dipendenze):

| P | Wave | Target | Tipo | Stima dev | Sblocca / dipende |
|---|---|---|---|---|---|
| P0 | **Token SoT sign-off** | (decisione orchestratore + Duccio) | decisione | 0h | conferma "KEEP CURRENT" → sblocca tutto |
| P1 | **chrome (header)** | `header.php` + `logo.css` + `logo/index.jsx` + `chrome/index.jsx` | verifica + drift | 0.5–1h | **globale** — sblocca la QA visiva di tutte le altre 14 |
| P2 | **footer** | `footer.php` + `footer/index.jsx` | verifica + drift + riconcilia ordine fasce/copy | 1–1.5h | **globale** — appare ovunque |
| P3 | **home** | `front-page.php` + `home/index.jsx` + `home/mobile.jsx` | verifica + drift + riconcilia "19 aree" default_value | 1.5–2h | alto traffico; valida il flow su una pagina complessa |
| P4 | **single-competenza-tier1** | `single-competenza.php` (branch tier-1) + `single-competenza-tier1/index.jsx` | verifica + drift + risolvere `clamp(72px,10vw,160px)` | 1–1.5h | alto valore SEO (cluster Tier-1) |
| P5 | **single-avvocato** | `single-avvocato.php` + `single-avvocato/index.jsx` | verifica + drift (ritratto 1:1 vs 3:4, sticky CTA layout) | 1–1.5h | 4 pagine; pattern lawyer-card riusato altrove |
| P6 | **taxonomy-tipo-area** | `taxonomy-tipo-area.php` + `group_tipo_area_term_v1` + `taxonomy-tipo-area/index.jsx` | verifica + drift; verifica coverage group per 4 sezioni | 1–1.5h | hub di cluster; alto traffico |
| P7 | **chi-siamo → lo-studio** | `template-parts/page-lo-studio.php` + `group_lo_studio_v1` + `chi-siamo/index.jsx` | verifica + drift; (forse +1 image field facciata) | 1.5–2h | dopo decisione mapping (J) |
| P8 | **blog-archive** | `home.php` + `blog-archive/index.jsx` | verifica + drift (card 4:3 zoom hover, featured 16:9) | 1–1.5h | 326 post |
| P9 | **archive-casi** | `archive-saltelli_caso.php` + tab "Archive Headers" + `archive-casi/index.jsx` | **ADDITIVE** — pull-quote "caso simbolo" (~4 field) + filtri `caso_categoria` + layout riga nuovo | 1.5–2.5h | l'unica wave con vero net-new |
| P10 | **glossario-legale** | `inc/wave3-glossario.php` + `glossario-legale/index.jsx` | verifica + drift | 0.5–1h | basso rischio (1:1 con sorgente) |
| P11 | **contatti** | `template-parts/page-contatti.php` + `group_contatti_v1` + `contatti/index.jsx` | verifica + drift; **decidere mappa iframe** (raccomando: no); rendere dinamico il select aree | 1–1.5h | dopo decisione mappa (J) |
| P12 | **404** | `404.php` + `404/index.jsx` | verifica + drift | 0.5h | minimo rischio; chiudere per ultimo |
| — | (skip) **design-system** | — | — | 0h | non è un template; eventuale `/design-system/` interna = backlog post-cut |

**Totale stimato: ~13–17h dev** (12 mini-wave). La forchetta dipende da quanto drift CSS si trova
(se i template sono allineati come l'audit suggerisce → fascia bassa).

---

## C. Risk Analysis

- **Refactor che usa SCF** — basso rischio: 11/15 template non toccano SCF. I 2 che toccano (archive-casi
  → +4 field nel tab Archive Headers; lo-studio → forse +1 image field) sono **additive-only**. Mitigazione
  obbligatoria per ogni wave (lesson Wave 4.7.fix.4): **admin-side smoke test** — aprire la Page/CPT in
  WP-Admin e verificare che (a) il metabox SCF sia ancora visibile e popolato, (b) Gutenberg resti
  disabilitato sui 13 SCF-only, (c) nessun field popolato sia sparito. Più frontend `curl` su ogni URL
  affetta. Più `getComputedStyle()` spot-check sulle critical properties (font-size/letter-spacing/
  line-height/grid-template/gap/padding) — il "1 inline style = 1 CSS rule + 1 className" del golden rule.
- **Field SCF mancanti per liste dinamiche** — non c'è il problema: aree (CPT competenza ×19), lawyers
  (CPT avvocato ×4), cases (CPT saltelli_caso), blog posts (WP-native), term tipo-area (taxonomy ×4),
  glossary terms (array PHP per scelta) — tutto già loop-ato dai template. Gli array hardcoded nei JSX
  sono placeholder di design, non requisiti di dati.
- **Performance / LCP** — **i JSX NON hanno un hero background image** (confermato: zero asset, solo
  placeholder CSS gradient). La "hero bg novità" citata nel prompt non esiste nel bundle — gli screenshot
  `_reference/screenshots/` sono concept di **logo-stack** per un eventuale trattamento hero LOGO, che è
  una conversazione di design separata, non in scope di questi 15 JSX. → nessun nuovo peso asset; LCP
  invariato. Le immagini reali (ritratti, facciata, blog featured) restano backlog **Wave 5.1**, dove
  vanno servite WebP/AVIF + `srcset` responsive + `loading="lazy"` (eccetto LCP candidate: ritratto
  avvocato in hero `single-avvocato.php`, già `loading="eager" fetchpriority="high"`). Le animazioni JSX
  (`opacity`/`translateY` transitions, `@keyframes sl-rise`) sono GPU-friendly; GSAP è già `deferred`.
- **SEO** — struttura HTML invariata su 14/15 (verifica fedeltà, non refactor). **Nessun cambio a
  `inc/schema/`** (regola del prompt). Yoast meta invariato. Breadcrumb (`saltelli_render_breadcrumb`)
  invariato. **Single H1 per pagina**: verificato — ogni JSX ha esattamente un `<h1>`. I tab categorie
  blog restano link `?cat=N` server-side (NON convertire a filtering client-side React-style — perderebbe
  crawlability). archive-casi: la sezione pull-quote nuova è un `<blockquote>` semantico → ok per schema
  `Review`/`Quotation` se mai si volesse arricchire (non obbligatorio).
- **Riconciliazione contenuto** (vedi `02-jsx-to-wp-mapping.md §"Drift contenuto"`): "19 vs 17 aree"
  in vari `default_value`, orari colophon, hero CTA label, refuso "Una atelier" → "Un atelier". Tutti
  `default_value` (additive). **Prima di toccare un default, leggere il valore in DB**: se Elena ha
  scritto un override, non si tocca il suo valore — si allinea solo il default per i casi vuoti.

---

## D. Batching Strategy

**Raccomandazione: mini-wave singole, 1 template ciascuna, 0.5–2.5h. Esecuzione sequenziale su `main`
(one-writer-at-a-time). NIENTE worktree parallelo.**

Motivazioni:
1. Il golden rule del progetto ("Design → Code handoff rule") già prescrive una-Page-alla-volta.
2. **`sections.css` è il collo di bottiglia condiviso** — quasi ogni wave tocca uno scope block di
   `sections.css`. Due wave parallele su `sections.css` collidono → la regola one-writer le serializza
   comunque. Header/footer/home non sono parallelizzabili perché header+footer sono globali (toccano
   ogni pagina renderizzata in QA).
3. Un eventuale split worktree "safe" sarebbe: (A) `archive-casi` parte SCF (nuovi field in `acf-json/`
   + `archive-saltelli_caso.php`, file disgiunti) ‖ (B) un'altra wave su `sections.css`. Ma il guadagno
   è marginale e introduce rischio di merge su `sections.css`. **Non vale la pena.**
4. **Sequenziare con la phantom-resolution Wave 5 STEP 4**: le due iniziative toccano lo stesso
   `sections.css` e si sovrappongono semanticamente (i `clamp()` ad-hoc dei JSX = i phantom catalogati).
   Ordine: **prima le 12 wave design-handoff** (chi tocca un selettore per allinearlo al JSX, sui
   selettori che tocca risolve anche i phantom computed-neutral di quei selettori), **poi** un pass
   finale "phantom-resolution completa" (promote-to-token + clamp consolidation con design sign-off).
   Mai le due in parallelo.

Ogni wave = branch `feat/design-handoff-<template>` → merge no-ff su `main` dopo audit. Una alla volta.

---

## E. Pipeline Decision (Vite + Playwright?)

**Raccomandazione: NON installare il harness Vite+Playwright adesso. Fare le prime 2–3 wave (chrome,
footer, home) con verifica MANUALE. Se il drift è maggiore del previsto, allora investire nel harness.
Per ora, opzionale: uno script Playwright minimale che screenshotta le pagine staging a 4 breakpoint
(375/768/1024/1440) per eyeball-comparison contro i render JSX (~15 min setup).**

Motivazioni:
1. **Costo reale > 30-40 min**: per renderizzare i JSX in Vite servirebbe (a) uno shell HTML per ogni
   componente che carica `tokens-design-bundle.css` + `saltelli-design-bundle.css` + React UMD + il
   componente; (b) gestire le dipendenze cross-componente `<S2Header>`/`<S2Footer>` (global window);
   (c) mockare gli `useEffect` che fanno `window.addEventListener("scroll")`; (d) i componenti esportano
   `window.X = X` non `export default`. Realisticamente ~1–2h per un harness funzionante, non 30-40 min.
2. **Le pagine sono già costruite**: se l'audit ha ragione (drift = solo valori CSS), la verifica manuale
   (Read JSX → Read PHP+CSS → diff a occhio + `curl` smoke + `getComputedStyle` spot-check via uno
   script Playwright one-off) copre l'80% del valore a costo ~zero.
3. **Versione minimale che vale subito**: `playwright` (già un dev-dep candidato? — verificare; se no,
   `npx playwright` senza installare) → screenshot di `staging.studiolegalesaltelli.it/` + le 12 URL a
   4 breakpoint → committare in `tests/visual-baseline/`. Duccio renderizza i JSX in claude.ai/design e
   confronta. ~15 min. Lo si fa **dopo P3** se serve, o subito come baseline pre-implementation.
4. Il harness "vero" (Vite render JSX → screenshot → `pixelmatch` diff vs WP) ha senso **post-cut**, come
   strumento di regressione per i FUTURI bundle Design (vedi §I).

---

## F. Pages Senza JSX (6 + Team)

| Page | JSX dedicato? | Approccio raccomandato |
|---|---|---|
| **Lo Studio** (Page 2811) | sì, *de facto* — `chi-siamo/index.jsx` È il design lo-studio (hero+lede+Plate+1999+4-lawyers+3-principi+timeline+CTA) | mappare `chi-siamo/index.jsx` → `page-lo-studio.php`. **Coperto.** (vedi §J MAPPING DECISION) |
| **Aree di Pratica hub** (Page 2812, `page-aree-di-pratica-hub.php`) | no | **tenere current** (4 cluster card). Opzionale: armonizzare l'hero/intro al pattern `taxonomy-tipo-area` (hero h1 italic + lede + count) per coerenza visiva. **Richiedere a Design un JSX dedicato** solo se si vuole un refresh — bassa prio. |
| **Risorse hub** (Page 2813, `page-risorse-hub.php`) | no | **tenere current** (4 resource card). Stesso discorso: armonizzare l'hero al pattern hub esistente. Bassa prio. |
| **Costi e Consulenze** (Page 2695, `page-costi-e-consulenze-hub.php` + `group_costi_e_consulenze_hub_v1`) | no | **tenere current**, MA: è una pagina conversion-relevant — **richiedere a Design un JSX dedicato** post-cut (priorità media: il funnel costi/preventivo merita un design pass). Nel frattempo, riusare il pattern `contatti` (hero + sezioni + CTA dirette) dove serve. |
| **Prenota appuntamento** (Page 2714, `group_prenota_appuntamento_v1` — minimale, 46 righe) | no | **tenere current**. Probabilmente un embed calendly o un form snello. Se cresce, adattare il pattern form di `contatti/index.jsx`. Bassa prio. |
| **single-post articolo** (blog single, `single.php`) | **NO — gap più grande** | **adattare il pattern editoriale di `single-competenza-tier1/index.jsx`** (italic lede sotto h1 + drop-cap + prose 62ch + 3 articoli correlati + CTA). **Richiedere a Design un JSX `single-post` dedicato — priorità ALTA**: sono 326 pagine, prime SEO real estate, e oggi `single.php` non ha un design firmato. DESIGN.md cita già "Italic stress: ... lede sotto h1 (single blog post + competenza tier-1)" e "drop-cap §02" → il pattern esiste, manca solo il JSX completo. |
| **archive-avvocato (Team)** (`archive-avvocato.php` + tab "Archive Headers") | no (ma il pattern c'è) | **estrarre la griglia "4 lawyer card" da `chi-siamo/index.jsx`** (la sezione `§ 03 — I nostri quattro` con le 4 `<a href="/avvocati/{slug}/">` card 3:4 + role mono + h3 nome + spec) → applicarla a `archive-avvocato.php` per coerenza. **Coperto** dal pattern chi-siamo. |
| Utility (privacy/cookie/note-legali) | no | **tenere current** (`page-info-shared.php` + `group_info_shared_v1`) — testo legale boilerplate, non serve un JSX. |

**Sintesi F**: l'unica richiesta a Design ad alta priorità è **`single-post`**. Le altre 4 hub/utility
restano current; eventuali JSX dedicati = backlog post-cut, priorità bassa-media.

---

## G. Elena Impact Final Check

I 13 Pages SCF-only mantengono il metabox. Verifica per-JSX dell'impatto sul workflow Elena-approved:

- **11/15 template**: nessun cambio SCF → Elena del tutto inalterata. Il drift cleanup è puramente CSS;
  i template continuano a leggere gli stessi field. (`home`, `chrome`, `footer`, `logo`, `single-avvocato`,
  `blog-archive`, `glossario`, `taxonomy-tipo-area`, `single-competenza-tier1`, `404`, + `home/mobile`).
- **`archive-casi`**: 🟡 ADDITIVE — 4 nuovi field nel tab "Archive Headers" (Theme Options). Elena vede
  4 campi opzionali in più (eyebrow/numero/quote/attribuzione del "caso simbolo"), **nessun field rimosso**,
  nessun field rinominato. Se restano vuoti, il template non renderizza la sezione (graceful). EDITOR-HANDOFF
  v6.0 §3.6 (archive CPT) andrà aggiornato con i 4 campi nuovi.
- **`chi-siamo` → `lo-studio`**: 🟢 (forse +1 image field per la facciata "Plate I" se assente da
  `group_lo_studio_v1`). Elena vede 1 campo immagine opzionale in più. Nessuna rimozione. (Coperto comunque
  da Wave 5.1.)
- **`contatti`**: nessun cambio SCF (la decisione mappa-iframe è CSS/template, non un field). Refinement
  "select aree dinamico" = render-side, nessun impatto su come Elena edita la Page 23.
- **Liste dinamiche** (aree/lawyers/cases/term/blog): i JSX hanno array hardcoded *di design*, ma il tema
  loop-a i CPT/taxonomy reali → il contenuto di Elena (e dei CPT) è rispettato, non sovrascritto.
- **Rischio da presidiare**: una wave che "refactora" un nome di variabile PHP per matchare una prop JSX e
  rompe per sbaglio un `get_field()`. Mitigazione: ogni wave fa l'admin-side smoke test + frontend `curl`
  + `getComputedStyle` spot-check (vedi §C). Nessun cambio al disable-Gutenberg pattern, alle location
  rules, alla struttura dei group, alla registrazione CPT/taxonomy. **Hard invariant rispettato.**

---

## H. Quick Win — Proposed Prompt Operativo

**Template scelto: `chrome` (header).** Perché: globale (appare su ogni pagina → verificarlo una volta
sblocca la QA visiva di tutte le altre 14), basso rischio (`header.php` = 143 righe, `chrome/index.jsx`
= 122 righe, il pezzo principale è il logo), e testa il flow lean su qualcosa di piccolo.

> **Nota sulla "hero bg novità"**: il prompt orchestratore la cita, ma **nel bundle non c'è** — i JSX
> home non hanno background image, gli screenshot `_reference/screenshots/` sono concept di **logo-stack
> hero** (4 varianti). Se Duccio vuole un refresh dell'hero, sarebbe un trattamento del LOGO
> (`SLLogoPrimary`/`SLLogoStack` in grande), non una foto bg — conversazione di design separata, non in
> scope di questi 15 JSX. La wave `chrome` può, opzionalmente, includere "valutare se l'hero homepage
> adotta un trattamento logo-stack" come spike, ma senza implementarlo finché Design non firma una variante.

### Prompt operativo — wave `feat/design-handoff-chrome`

```
# PROMPT — Design Handoff Wave 1 · CHROME (header) verify & drift cleanup

Scope: allineare header.php + logo.css al design source design-handoff/chrome/index.jsx
+ design-handoff/logo/index.jsx. Verifica fedeltà + drift cleanup CSS. NESSUN cambio SCF.
Branch: feat/design-handoff-chrome · stima 0.5–1h.

## PRE-FLIGHT
1. Leggi: CLAUDE.md (Identity, Hard constraints, Design system, "Design → Code handoff rule golden",
   Lessons learned), design-handoff/README.md, .claude/knowledge/audits/design-handoff/RECOMMENDATION.md
   (§A SoT decision = KEEP CURRENT tokens.css; §H questo prompt).
2. Leggi: design-handoff/chrome/index.jsx (S2Header), design-handoff/logo/index.jsx
   (SLLogoHorizontal — la variante usata in header), wp-content/themes/saltelli/header.php,
   wp-content/themes/saltelli/assets/css/logo.css, e i blocchi di sections.css/components.css
   che matchano .sl-header__* / .sl-logo__h-*.
3. git checkout -b feat/design-handoff-chrome. Working tree pulito.

## VERIFY (read-only diff first)
Confronta, elemento per elemento:
- Header shell: position sticky / top 0 / z-index 50 / background transparent→var(--background) su
  scroll / border-bottom transparent→1px var(--border) su scroll / transition 300ms var(--ease-editorial).
  WP: header.php ha già data-scrolled + inline script. CSS: cerca .sl-header[data-scrolled="true"].
- Container: max-width 1440 / margin 0 auto / padding "20px clamp(24px,5vw,96px)" / grid
  "auto 1fr auto" / gap 48 (JSX dice gap 36 per la nav interna — distinguere il gap del container 48
  dal gap della nav 36) / align-items center.
- Logo SLLogoHorizontal size=md: grid "auto 1px auto" / gap 24 / left-block (Studio Legale 10px
  weight 500 ls 0.32em uppercase + "Napoli · 1999" mono 9px ls 0.24em uppercase muted) / rule 1px×36px /
  name "Saltelli" Playfair italic 32px lh 1 ls -0.02em con swash "S" bronze. → WP header.php usa markup
  inline .sl-logo--horizontal con .sl-logo__h-top/h-bot/h-rule/h-name/swash. Verifica che logo.css
  produca QUESTI valori (size md). Mobile: SLLogoHorizontal size=sm (top 9 / bot 8 / rule 28 / name 24).
- Nav: 6 voci, fontSize 14 weight 500, .sl-link con borderBottomColor transparent (no underline a
  riposo, bronze su hover). WP: wp_nav_menu('primary'). Il markup JSX delle voci è REFERENCE del menu
  atteso (Studio / Avvocati / Competenze / Casi / Editoriale / Contatti) — gli href JSX sono LEGACY
  (/avvocati/, /competenze/, /casi/, /editoriale/), il menu WP usa gli slug correnti. NON hardcodare
  la nav dal JSX. Verifica solo lo styling delle voci (.sl-header__menu a) vs .sl-link spec.
- Phone: .sl-mono color var(--primary) fontSize 11 (md) / 11 (sm). WP: .sl-header__phone con
  saltelli_option('studio_telefono_pubblico'). Verifica styling.
- Mobile burger + .sl-header__mobile menu: il JSX home/mobile.jsx ha 2 linee 24×1px + overlay menu
  full-width con voci Playfair 28px. WP header.php ha .sl-header__burger (2 linee) + .sl-header__mobile.
  Verifica styling.

Produci una tabella "JSX value | CSS attuale | match? | fix" in chat PRIMA di toccare codice.

## IMPLEMENT (solo se ci sono drift)
- Per ogni drift: se il valore JSX matcha un token di tokens.css → var(--token). Se è un phantom
  catalogato in audits/wave5-step4-sections-cleanup/02-phantom-values-remaining.md → segui quel piano.
  Se nuovo → decidi per-selector, MAI toccare :root.
- Scope marker nel CSS: /* === design-handoff chrome === */.
- Mappa OGNI inline style del JSX a una className BEM se non c'è già (golden rule: 1 inline = 1 rule + 1 className).
- Cache flush dopo ogni edit non triviale + curl test (regola CLAUDE.md). Per file PHP critici
  (header.php): ssh deploy@178.62.207.50 "sudo systemctl reload php8.2-fpm" su staging (lesson OPcache).

## SMOKE TEST
- Frontend: curl -s https://staging.studiolegalesaltelli.it/ | grep -E 'sl-header|sl-logo' → markup
  presente. Idem su 2 URL secondarie (es. /chi-siamo/, /contatti/) — header appare ovunque.
- getComputedStyle (script Playwright one-off): su .sl-header__brand .sl-logo__h-name (font-size,
  letter-spacing, line-height, font-style), .sl-header__menu a (font-size, font-weight), .sl-header__phone
  (font-size, color), .sl-header (position, z-index), e dopo scroll → .sl-header[data-scrolled="true"]
  background/border. Diff vs i valori JSX.
- 4 breakpoint (375/768/1024/1440): screenshot, verifica burger appare <1024, nav appare ≥1024.
- Admin-side: N/A (header non ha metabox SCF) — ma verifica che il logo non sia regredito su nessuna Page.

## PUSH
git push origin feat/design-handoff-chrome. Report in chat: tabella drift trovati+fixati, diff righe,
smoke test esito, eventuali decisioni autonome. NON mergere a main — l'orchestratore audita e mergia.
```

---

## I. Next Steps Post-Cut

1. **Visual-regression baseline (Playwright)** — screenshot delle 12 URL principali a 4 breakpoint,
   committare in `tests/visual-baseline/`. Da fare idealmente *prima* della prima wave (baseline
   pre-implementation) + ri-screenshot dopo ogni wave per pixel-diff regression.
2. **Harness Vite → render JSX → screenshot → pixelmatch vs WP** — il "vero" tool di verifica
   automatica del flow Design→Code. ~1–2h setup. Utile soprattutto per i **futuri** bundle Design (non
   strettamente necessario per questo, dove le pagine sono già costruite). Build-on-demand: lo si crea
   se/quando arriva il secondo bundle.
3. **Handoff bundle update workflow** — quando Design ship-a un nuovo bundle: uno script che diffa i
   `clamp()`/`letter-spacing`/`line-height` inline dei nuovi JSX vs `sections.css` + `tokens.css`
   correnti, e sputa fuori un report "drift introdotto da questo bundle" — così l'audit della PHASE 3
   diventa automatico per le iterazioni successive.
4. **Richieste a Design** (priorità decrescente): (a) **`single-post`** JSX — 326 pagine, alta SEO,
   oggi senza design firmato; (b) `costi-e-consulenze` JSX — funnel conversion-relevant; (c) varianti
   hero homepage (logo-stack treatment — se Duccio vuole il refresh accennato dagli screenshot);
   (d) `aree-di-pratica hub` + `risorse hub` JSX dedicati (bassa prio — i pattern hub esistenti vanno bene).
5. **Phantom-resolution wave (Wave 5 STEP 4 follow-up)** — da sequenziare DOPO le 12 wave design-handoff
   (che risolvono naturalmente i phantom dei selettori che toccano). Pass finale: promote-to-token
   (`--fs-eyebrow: 10px`, `--fs-body-sm: 13px`, `--fs-lede-mobile: 17px`, `--ls-h2-tight: -0.015em`,
   `--ls-h1-tight: -0.025em`, `--lh-prose: 1.55`, `--lh-display-tight: 0.95`, `--lh-heading-tight: 1.1`)
   = computed-neutral; poi clamp() consolidation + valutare `--fs-body-fluid` = cambia computed, serve
   design sign-off + Playwright pixel-diff.
6. **Image Expansion (Wave 5.1)** — i placeholder gradient/striped di ogni JSX diventano
   `wp_get_attachment_image()` da foto reali (ritratti avvocati 3:4, facciata Via Vannella Gaetani
   1440×480/560, blog featured 16:9, blog card 4:3, avatar referenti tipo-area 1:1). WebP/AVIF + `srcset`
   + `loading="lazy"` (eccetto LCP candidate). Coordinare brief fotografo. Field `image` SCF: alcuni
   esistono già (`foto_ritratto`, `studio_foto_facciata`); altri additive.
7. **EDITOR-HANDOFF v6.x** — aggiornare con i nuovi field di `archive-casi` (caso simbolo) e
   l'eventuale image field di `lo-studio` (se aggiunto). Aggiornare se la decisione chi-siamo→lo-studio
   cambia qualcosa nel modello mentale (probabilmente no — `page-lo-studio.php` è già documentato).

---

## Appendice — Top 5 findings critici (per il report orchestratore)

1. **Il bundle è un re-handoff, non un build greenfield.** Le 15 JSX = design source di pagine già a
   frontend (Sessione 1+2+Logo v1.1). Lavoro reale = verifica + drift cleanup CSS, non implementazione.
2. **`tokens-design-bundle.css` è obsoleto** (pre-Wave 5 STEP 2): display `clamp(48,8vw,120)` vs
   `clamp(80,9vw,132)`, h1 `clamp(36,5vw,64)` vs `clamp(48,6vw,96)`, lh-body `1.65` vs `1.7`, 1
   letter-spacing vs 4 ottici. **Ma i JSX stessi usano i valori GRANDI/correnti inline** → tenere
   `tokens.css` corrente, DESIGN.md resta SoT, nessun update.
3. **Il drift tipografico del bundle = i ~460 phantom di Wave 5 STEP 4** (stessi `clamp()` ad-hoc).
   Sequenziare le wave design-handoff e la phantom-resolution sullo stesso `sections.css`; mai parallele.
4. **`chi-siamo/index.jsx` è la pagina LO-STUDIO, non l'hub /chi-siamo/.** È la struttura esatta di
   `page-lo-studio.php` + `group_lo_studio_v1`. Decisione di routing (default: mappa a lo-studio, nessun
   cambio IA), non un refactor.
5. **2 cose da decidere con Duccio**: (a) `/contatti/` — il JSX re-introduce la mappa iframe rimossa
   volutamente in v0.17.3 → raccomando NO; (b) `single-post` blog single non ha JSX → richiederlo a
   Design (alta prio, 326 pagine). L'unica vera ADDITIVE: `archive-casi` pull-quote "caso simbolo" (~4
   field nel tab Archive Headers).
