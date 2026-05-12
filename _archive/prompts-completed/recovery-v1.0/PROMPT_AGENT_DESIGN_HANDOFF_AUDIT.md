# PROMPT AGENT — DESIGN HANDOFF AUDIT & RECOMMENDATION

> **Scope**: ricevere handoff strutturato Claude Design (15 JSX template + 2 CSS reference + 4 screenshots in `/design-handoff/`) e produrre un piano operativo strategico di applicazione al tema WordPress. **READ-ONLY audit + planning. NO implementation.**
>
> **Branch**: `audit/design-handoff-strategy`
> **Stima**: 3-4h
> **Modalità**: read-only audit, push branch con deliverable, NO theme changes.

---

## CONTESTO

Sei Claude Code in una sessione dedicata audit/strategy. Il progetto è il custom WordPress theme di Studio Legale Saltelli (vedi `CLAUDE.md` per contesto completo).

**Stato corrente repo** (post-Elena OK definitivo 2026-05-11):
- v1.3.13-wave5-step3-coverage CUT-READY
- 19 Pages canoniche (35 → 19 post Wave 4.7.fix.5 cleanup)
- 13 Pages Gutenberg-disabled SCF-only (4 hub + 7 dual-source + Prenota appuntamento + Lo Studio)
- 107 field SCF added in Wave 5 STEP 3 (text/textarea conservative pattern Elena-approved)
- 23 field SCF per term tipo-area (privati/imprese/contenzioso-amministrativo)
- 2 archive CPT con tab "Archive Headers" expanded (Team + Casi rappresentativi)
- tokens.css allineato a DESIGN.md (Wave 5 STEP 2: 12 token + 4 mancanti corretti, letter-spacing + line-height ottici)
- sections.css 55% drift risolto (Wave 5 STEP 4: 328 token swaps, ~460 phantom documentati)

**Handoff ricevuto da Claude Design** (in `/design-handoff/`):
- 15 JSX template (vedi `design-handoff/README.md` per mapping)
- `_reference/tokens-design-bundle.css` (298 righe — NON SoT, cross-check)
- `_reference/saltelli-design-bundle.css` (164 righe — utility CSS Design)
- `_reference/screenshots/` 4 PNG hero stack concept variants

**Obiettivo sessione**: audit completo + RECOMMENDATION.md su come applicare. NON implementare. NON committare al theme.

---

## ⚠️ HARD INVARIANT — SCF DATA CONTRACT PRESERVATION

**INTERIORIZZA QUESTA REGOLA PRIMA DI INIZIARE.**

La struttura SCF + il flow editoriale (Wave 4.7.fix.4 + 4.7.fix.5 + Wave 5 STEP 3, approvati da Elena) è il **data contract immutabile** del CMS. I JSX Design sono **prototipi visuali** che si DEVONO adattare al data contract, NON viceversa.

### NON ACCETTABILE (FLAGGA E BLOCCA durante audit)

- Refactor struttura SCF field group esistenti (rinominare, rimuovere, spostare field tra group)
- Cambio location rules SCF (es. spostare field da `group_homepage_v1` a un altro group)
- Disable Gutenberg pattern modificato sui 13 Pages SCF-only (vedi `inc/admin/disable-gutenberg-for-scf-pages.php`)
- Rimozione field SCF popolati da Elena (107 field Wave 5 STEP 3 + 23 field term tipo-area + Archive Headers expanded)
- Cambio admin path (URL admin) di nessuna Page WP
- Refactor template PHP che rompe la lettura SCF esistente (es. cambiare nome variabile PHP che ora corrisponde a field SCF name)
- Cambio CPT registration (post_type `avvocato`/`competenza`/`saltelli_caso`/ecc.) — labels OK, slug NO, capabilities NO
- Cambio taxonomy registration (`tipo-area`, `caso_categoria`)

### ACCETTABILE (modalità additive)

- Aggiungere NUOVI field SCF a group esistenti (es. nuovo field `image` `hero_bg` in `group_homepage_v1`)
- Aggiungere NUOVI tab a Saltelli Settings se servono per content globale nuovo del JSX
- Aggiungere NUOVI group SCF se Design introduce contenuti per Pages senza group attached
- Aggiornare `default_value` di field esistenti se Design richiede valore diverso (additive, no schema change)
- Refactor CSS (sections.css, components.css) per matchare visual Design
- Aggiungere asset alla Media Library (background image, foto, SVG)

### Regola di interpretazione durante PHASE 2 (mapping)

Per ogni element del JSX:
1. Identifica il contenuto editoriale (testo, immagine, link, lista)
2. Verifica se esiste già un SCF field che lo copre:
   - ✅ **SCF field esistente** → MATCH (template legge da field esistente, no schema change)
   - ⚠️ **SCF field mancante ma additive su group esistente** → ADD FIELD (additive, safe)
   - ⚠️ **Servono N field nuovi** → ADD GROUP FIELDS (additive, lean)
   - ❌ **Design richiede refactor schema esistente** → DESIGN RE-INTERPRETATION (BLOCCA + flagga in RECOMMENDATION)

---

## PRE-FLIGHT (15 min)

1. Leggi nell'ordine:
   - `design-handoff/README.md` (convention orchestratore + chiarimento SoT design tokens)
   - `CLAUDE.md` root (sezioni: Identity, Current state, Hard constraints, Workflow rules, Design system, **Design → Code handoff rule golden**, Lessons learned)
   - `docs/DESIGN.md` (SoT design tokens canonical)
   - `docs/ARCHITECTURE.md` (theme + ACF schema mapping)
   - `docs/EDITOR-HANDOFF.md` v6.0 (modello mentale editoriale Elena)
   - `wp-content/themes/saltelli/assets/css/tokens.css` (current tokens, post Wave 5 STEP 2)
   - `design-handoff/_reference/tokens-design-bundle.css` (bundle Design — NON SoT)
   - `design-handoff/_reference/saltelli-design-bundle.css` (utility CSS Design)
   - `wp-content/themes/saltelli/front-page.php` (template Home current)
   - `wp-content/themes/saltelli/inc/admin/disable-gutenberg-for-scf-pages.php` (13 Pages SCF-only)
   - `.claude/knowledge/audits/wave5-step4-sections-cleanup/02-phantom-values-remaining.md` (drift residui CSS)
   - `wp-content/themes/saltelli/acf-json/group_homepage_v1.json` (esempio SCF group espanso post Wave 5 STEP 3)

2. Verifica branch + crea audit dir:
   ```sh
   git fetch origin && git status
   git checkout -b audit/design-handoff-strategy
   mkdir -p .claude/knowledge/audits/design-handoff
   ```

3. Conferma in chat:
   - Stato repo (working tree pulito)
   - 22 file presenti in `design-handoff/` (15 JSX + 4 screenshots + 2 CSS + README)
   - Prosegui PHASE 1

---

## WORK — 4 PHASES

### PHASE 1 — JSX Inventory (45 min)

Per ognuno dei 15 JSX in `design-handoff/<page>/index.jsx` (e `home/mobile.jsx`):

1. Leggi integralmente
2. Estrai:
   - **React hooks** usati (useState, useEffect, useRef, useCallback, useMemo)
   - **className BEM** dichiarate (count + lista)
   - **Inline styles** count (`style={{...}}`)
   - **Component custom React** riusati (es. `<Button>`, `<Card>`, `<SectionHeader>`) — count + list
   - **Children dinamici / loop** (es. `{areas.map(...)}`, `{lawyers.map(...)}`)
   - **Dati hardcoded** nel JSX (es. array `areas` con 19 items, array `lawyers` con 4 items)
   - **Reference asset** (background-image, gradient, SVG inline)
   - **Animation / scroll behavior / interactive state**

3. Documenta in `.claude/knowledge/audits/design-handoff/01-jsx-inventory.md` con tabella per page:

   | Page | className count | inline styles | Components | Loops | Hardcoded data | Asset refs |
   |---|---|---|---|---|---|---|

### PHASE 2 — JSX → WP Theme Mapping (60 min)

Per ogni accoppiata JSX → WP template:

1. Identifica match WP target:
   - `design-handoff/home/` → `front-page.php`
   - `design-handoff/chrome/` → `header.php` + `footer.php` (split)
   - `design-handoff/footer/` → `footer.php` (alternative dedicato)
   - `design-handoff/chi-siamo/` → `template-parts/page-chi-siamo-hub.php`
   - `design-handoff/single-avvocato/` → `single-avvocato.php`
   - `design-handoff/blog-archive/` → `home.php`
   - `design-handoff/archive-casi/` → `archive-saltelli_caso.php`
   - `design-handoff/glossario-legale/` → template per Page 2710
   - `design-handoff/taxonomy-tipo-area/` → `taxonomy-tipo-area.php`
   - `design-handoff/single-competenza-tier1/` → `single-competenza.php` (variante Tier-1)
   - `design-handoff/contatti/` → template Page contatti (23)
   - `design-handoff/404/` → `404.php`

2. Per ogni accoppiata, classifica gap visual:
   - ✅ **Già presente**: WP template ha struttura matching JSX (no refactor needed)
   - ⚠️ **Parziale**: simile ma mancano sezioni/element specifici
   - ❌ **Mancante**: JSX introduce nuova feature non presente nel WP attuale
   - 🔄 **Refactor**: JSX cambia struttura/order — VALUTA SCF impact

3. **CRITICAL — per ogni elemento del JSX, applica classificazione SCF Data Contract**:
   - Verifica se className BEM esiste già in `components.css` o `sections.css` del tema
   - Verifica se contenuto editoriale matcha SCF field esistente, richiede ADD field additive, o richiede REFACTOR (=BLOCCA)
   - Se manca BEM className: flagga "DA CREARE" + proporre file:line target
   - Se esiste ma valori diversi: flagga "DRIFT" + valore JSX vs valore CSS attuale

4. Documenta in `02-jsx-to-wp-mapping.md` con tabella per page:

   | JSX inline/element (file:line) | Selettore CSS attuale | SCF field match | Visual gap | SCF impact | Action proposta |
   |---|---|---|---|---|---|

### PHASE 3 — Design Token Reconciliation (30 min)

Confronta `design-handoff/_reference/tokens-design-bundle.css` vs `wp-content/themes/saltelli/assets/css/tokens.css`:

1. Per ogni token in tokens-design-bundle.css:
   - Valore Bundle Design
   - Valore Current (post Wave 5 STEP 2)
   - Drift assoluto (% differenza)
   - Decisione raccomandata: KEEP CURRENT (Wave 5 STEP 2 = DESIGN.md SoT) vs ADOPT BUNDLE (rinegoziare DESIGN.md) vs ADAPT INLINE (lasciare current + adapt JSX values durante implementation)

2. Cross-reference con `02-phantom-values-remaining.md` (Wave 5 STEP 4): forse Bundle Design usa valori che ora sono fantasma documentati. Se sì, è hint che il bundle Design usa la versione PRELIMINARE del design system pre-Wave 5 STEP 2.

3. Per `saltelli-design-bundle.css`: identifica utility che esistono già in components.css/sections.css vs utility NUOVE introdotte da Design.

4. Documenta in `03-tokens-reconciliation.md`

### PHASE 4 — Strategy Recommendation (45 min)

Produci `RECOMMENDATION.md` con le seguenti sezioni nell'ordine:

#### J. SCF Data Contract Compliance Audit (PRIMA, hard invariant)

Per ognuno dei 15 template JSX, classifica complessivamente:
- ✅ **COMPLIANT** — il JSX rispetta SCF data contract attuale + richiede zero/poco additive
- ⚠️ **ADDITIVE NEEDED** — il JSX richiede field SCF nuovi (lista esatta count + types: text/textarea/image/repeater)
- ❌ **DESIGN RE-INTERPRETATION** — il JSX implica refactor SCF/schema/Gutenberg pattern (LISTA dettagliata + raccomandazione: rinegoziare con Design vs ignorare l'interpretazione + applicare solo visual layer)

Per ogni "DESIGN RE-INTERPRETATION" identificato:
- Cosa richiede Design (descrizione tecnica)
- Cosa rompe (SCF field, template, Gutenberg pattern, admin path, ecc.)
- Impatto su Elena workflow Elena-approved
- Raccomandazione: rinegoziare Design oppure applicare visual senza touchare SCF

#### A. SoT Design Tokens Decision (critica)

Raccomanda quale tokens.css vince: bundle Design, current Wave 5 STEP 2, o hybrid. Motiva con 3-5 argomenti pro/contro. Indica impatto su `docs/DESIGN.md` (eventuale update SoT) e su Elena workflow.

#### B. Prioritization

Ordine raccomandato di implementation per template (criterio: alto traffico + media complessità + dipendenze + risk). Esempio:
- P1: chrome (header+footer global, sblocca tutte le altre)
- P2: home (hero bg novità + alto traffico)
- P3: chi-siamo (hub alto traffico)
- ...

Stima implementation per template (ore Code) con breakdown.

#### C. Risk Analysis

- Refactor template che attualmente usa SCF: come preservare dati Elena-approved (hard invariant)
- Field SCF mancanti per content dinamico JSX (es. liste areas/lawyers/cases): list di nuovi field per template
- Performance: hero bg + nuovi asset → impatto LCP, valutare WebP/AVIF + responsive srcset
- SEO: cambi struttura HTML → schema markup invariato? Yoast meta? Breadcrumb?

#### D. Batching Strategy

Mini-wave singole 30-60 min ciascuna vs mega-wave compatta. Raccomanda 1 opzione motivata. Identifica template parallelizzabili via worktree (file disgiunti vs dipendenze chrome/header).

#### E. Pipeline Decision

Vale la pena installare Vite + Playwright per render automatico + visual diff? (cost setup 30-40 min). Raccomanda 1 opzione con motivazione.

#### F. Pages Senza JSX

Per 6 Pages mancanti (Lo Studio, Aree di Pratica hub, Risorse hub, Costi e Consulenze, Prenota appuntamento, single-post articolo): proporre approccio (adattare JSX simile, oppure produrre Design custom, oppure lasciare current). Per ognuna, decisione raccomandata.

#### G. Elena Impact Final Check

I 13 Pages SCF-only hanno metabox attached. JSX Design rispetta i field SCF esistenti?
- Se JSX richiede content dinamico (es. lista 19 areas): rispetta i CPT competenza esistenti?
- Se JSX introduce nuove sezioni: serve aggiungere field SCF nuovi — list per page
- Rischio: Elena perde pattern Elena-approved se Design refactor template senza preservare SCF read

#### H. Quick Win — Proposed Prompt Operativo

Identifica il template più "safe + impatto immediato" per il primo test del flow Design Handoff. Probabile candidato: `chrome` (header+footer global) oppure `home` (hero bg novità).

Scrivi il prompt operativo concreto che orchestratore userà per quel template (sezioni: pre-flight + JSX reading + mapping verify + implementation + smoke test + push). Pattern lean stabilito.

#### I. Next Steps Post-Cut

Pipeline Vite/Playwright (se non già scelta in E), tools di visual regression, eventual handoff bundle update workflow per future iterazioni Design.

---

## OUTPUT DELIVERABLE

```
.claude/knowledge/audits/design-handoff/
├── 01-jsx-inventory.md
├── 02-jsx-to-wp-mapping.md
├── 03-tokens-reconciliation.md
└── RECOMMENDATION.md
```

Branch: `audit/design-handoff-strategy` (audit-only, no theme changes)

Commit message format:
```
audit: Design Handoff Strategy — 4 deliverable + RECOMMENDATION

15 JSX template auditati. SCF data contract compliance check per ognuno (COMPLIANT/ADDITIVE/RE-INTERPRETATION). Tokens reconciliation Bundle Design vs current Wave 5 STEP 2. Strategy recommendation per orchestratore: SoT decision, prioritization, batching, pipeline, Elena impact, quick win prompt operativo.

Branch: audit/design-handoff-strategy · N file · +XX righe
Output: piano completo per N mini-wave implementation — orchestratore decide ordine + sblocca rinegoziazione Design su eventuali RE-INTERPRETATION.
```

---

## HARD RULES

1. **Read-only audit**. NIENTE modifiche a JSX, template PHP, CSS, SCF field group.
2. **NIENTE asset upload Media Library** finché orchestratore conferma plan.
3. **JSX in `/design-handoff/`** è SoT per design intent visivo. `tokens.css` current è SoT per design system tema. Conflitto → FLAGGA, non risolvere autonomamente.
4. Rispetta **Hard constraints** `CLAUDE.md`: no page builder, no dependencies nuove, design tokens NON modificabili senza decisione orchestratore.
5. Schema JSON-LD invariato a priori (no cambi a `inc/schema/`).
6. **SCF DATA CONTRACT IMMUTABILE**: tutto ciò che è stato approvato da Elena nella sequenza Wave 4.7.fix.4 → Wave 5 STEP 3 coverage è LOCKED. Nessun refactor di schema, location, group, supported types. Solo additive (nuovi field a group esistenti, nuovi group per Pages senza group). Eventuali "Design re-interpretation" che implicano refactor → FLAGGA, NON suggerire applicazione.
7. **Elena OK definitivo** 2026-05-11 — niente refactor che rischia di rompere il workflow editor approvato.

---

## TONO

Direct, concrete, zero filler. Stile commit usato dal progetto.

## OUTPUT FINALE in chat

- Audit phases status (1/4, 2/4, 3/4, 4/4)
- File path dei 4 deliverable
- Top 5 findings critici (drift tokens, gap maggiori, refactor risks)
- **SoT decision** raccomandata + motivazione
- **SCF Data Contract Compliance summary**: count COMPLIANT / ADDITIVE / RE-INTERPRETATION
- **Lista RE-INTERPRETATION** dettagliata (se presenti) con raccomandazione per ognuna
- **Quick win** proposto: nome template + ETA + nota prompt operativo
- Branch pushato

Procedi PHASE 1. Conferma all'inizio + alla fine di ogni phase.

---

*Bundle ricevuto da Claude Design: 2026-05-11. Orchestratore: Aldo Santoro (Duccio). Origine handoff: `/Users/aldosantoro/Desktop/studiolegalesaltelli-ux-ui/`. Copia selettiva nel repo: `/design-handoff/` (15 JSX + 2 CSS reference + 4 screenshots + README).*
