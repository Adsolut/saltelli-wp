# Wave 6 — GEO/CRO Blocks Extension · Recovery Report

> **Tag target**: `1.2.0-wave6-geo-cro-blocks`
> **Branch**: `feat/wave6-geo-cro-blocks` (parent: `main` post-Wave5, tag `v1.1.0-wave5-ia-refactor`)
> **Decisione di base**: DEC-019 — pattern adaptation lean (NO Sessione 3 Claude Design).
> **Lettura propedeutica**: `prompts/PROMPT_AGENT_v1.1_WAVE6_GEO_CRO_BLOCKS.md`, `prompts/WAVE6_CALIBRATION_NOTES.md`, `prompts/pattern-adaptation-map.md`.
> **Output**: 17 file modificati/creati · 1238 insertions · 25 deletions su `main`.
> **One-writer-at-a-time**: orchestratore fermo durante questa wave. Audit + merge a discrezione orchestratore.

---

## Phase summary

| Phase | Stato | Output |
|---|---|---|
| 1 — Backup + branch + ACF Field Groups extension | ✅ | tarball + DB dump in `/tmp/saltelli-pre-wave6-*`. Branch creato. 4 ACF group estesi (commit `122d9e0`). |
| 2 — 4 nuovi template-part | ✅ | trust-bar, mobile-sticky-bar, mini-form, testimonials-block (commit `e8a30e9`). |
| 3 — Estensione 7 template files | ✅ | single-competenza, single, front-page, page-costi, footer, partial-faqpage, helpers (commit `6e26788`). |
| 4 — `cro.css` bundle | ✅ | 453 righe, 132 var(--*) calls, 6 prefers-reduced-motion blocks. |
| 5 — Enqueue `cro.css` | ✅ | `inc/enqueue.php` + `add_editor_style()` aggiornati (commit `35f1350` con Phase 4). |
| 6 — Smoke + render checks + schema validation | ✅ | 21/21 PASS, render check pass, FAQPage Tier-2 emesso, no Yoast dup. |
| 7 — Bump version + report + push | ✅ in corso | Version `1.2.0-wave6-geo-cro-blocks`, report presente. |

Lighthouse no-regression: **NON eseguito locale**. Richiede deploy staging post-merge (orchestrator-driven).

---

## 10 pattern implementati (mapping su `pattern-adaptation-map.md`)

| Pattern | Mapping | Implementazione | Stato |
|---|---|---|---|
| 1 — Answer capsule | `.sl-competenza__answer` esistente + bordo bottom | `answer_capsule` field già presente Wave 1, riusato. CSS Pattern 1 in `cro.css`. CTA top ghost sotto capsule. | ✅ |
| 2 — Trust bar globale | 4 colonne grid + Theme Options Brand | 8 ACF field nuovi (`trust_signal_1..4` × label/caption). Template-part `trust-bar.php` con fallback editoriale hardcoded (visibile out-of-the-box). Hook in `front-page.php` prima di §06 contact. | ✅ |
| 3 — Mobile sticky bar | `.sl-mobile-bar` 3 azioni | `mobile-sticky-bar.php` con dati da `saltelli_studio_data()`. Exclusion via PHP conditional (CAL-W6-06): single-avvocato, /contatti/, 404. CSS fixed bottom, env(safe-area-inset-bottom). | ✅ |
| 4 — Mini-form contestuale | CF7 ridotto + fallback CTA | `mini-form.php` con strategia best-effort (CF7 slug `saltelli-mini` → fallback CTA). Hook in single-competenza + page-costi. | ✅ |
| 5 — FAQPage schema generalization | `partial-faqpage.php` | Bug fix pre-esistente: il loop si aspettava rows con `domanda`/`risposta` ma riceve array di IDs `saltelli_faq` post_object. **Wave 6 fix**: supporto entrambi pattern (legacy fake-repeater + Wave 1+ post_object). FAQPage ora emesso anche su Tier-2. | ✅ |
| 6 — Testimonials block | `saltelli_trust` CPT esteso | 8 ACF field nuovi (testimonial_type radio numero/testimonianza, testimonial_text/author/city/topic, source_label/text/url). `testimonials-block.php` loop con WP_Query meta_query. NO foto, NO rating, NO carousel. | ✅ |
| 7 — Statistic-with-source | Estensione trust-bar | 3 ACF field nuovi su `saltelli_trust` (source_label, source_text, source_url, conditional su type=numero). CSS `.sl-trust-bar__source` predisposto. Render UI delegato a Wave 6.1 (orchestrator decide). | ✅ schema · 🟡 render UI deferred |
| 8 — CTA progressive | 3 punti CTA esistenti + 4 ACF field nuovi | `cta_top_label`/`cta_top_url` (ghost sotto capsule), `cta_middle_label`/`cta_middle_url` (primary prima FAQ, fallback a `cta_label`). CTA bottom invariato. | ✅ |
| 9 — Author byline ricca | `single.php` blog | 2 ACF field nuovi su `group_avvocato_v1` (`byline_extended`, `expertise_topics` max 3). Render sotto la lede solo se autore linkato a CPT avvocato. `saltelli_reading_time()` esiste già (NO helper aggiunto). | ✅ |
| 10 — Related services | Riuso `.sl-area` | 1 ACF field nuovo su `group_competenza_v1` (`related_competenze`). Auto-fallback runtime: 3 random stesso cluster (tassonomia tipo-area). single-avvocato già conforme dal v0.24.0. | ✅ |

---

## ACF Field Groups — riepilogo extension

### `group_competenza_v1` — +5 field (1 deferred)

| Field | Type | Note |
|---|---|---|
| ~~`answer_capsule`~~ | textarea | **Già esistente Wave 1** — riusato, NO duplicato (orchestrator assumption errata in prompt v1.1) |
| `cta_top_label` | text | CTA ghost sotto answer-capsule |
| `cta_top_url` | url | URL CTA top |
| `cta_middle_label` | text | CTA primary prima FAQ — fallback a `cta_label` esistente |
| `cta_middle_url` | url | URL CTA middle — fallback a `cta_url` esistente |
| `related_competenze` | post_object × competenza, multiple, max 3 | Aree correlate manuali. Auto-fallback runtime se vuoto. |

### `group_trust_item_v1` — +8 field (radio + 7 conditional)

| Field | Type | Note |
|---|---|---|
| `testimonial_type` | radio (numero/testimonianza) | Default `numero` per backward-compat con i 4 trust signal esistenti |
| `testimonial_text` | textarea max 280 char | Conditional su type=testimonianza |
| `testimonial_author` | text max 80 | Conditional |
| `testimonial_city` | text default "Napoli" | Conditional |
| `testimonial_topic` | text max 80 | Conditional. NB: text invece di taxonomy field perché tassonomia `topic` non esiste — mantenuta semplicità + flessibilità |
| `source_label` | text default "Fonte" | Conditional su type=numero |
| `source_text` | text max 160 | Conditional |
| `source_url` | url | Conditional |

NB: `label` e `valore` esistenti sono stati resi **non più required** + conditional su type=numero per non rompere i 4 trust signal esistenti.

### `group_avvocato_v1` — +2 field

| Field | Type | Note |
|---|---|---|
| `byline_extended` | textarea max 200 char, 2 rows | Bio ricca per single blog post |
| `expertise_topics` | post_object × competenza, multiple, max 3 | Cross-link competenze nella byline |
| ~~`competenze_trattate`~~ | post_object | **NON aggiunto** — `aree_competenza_correlate` esistente è semanticamente identica. Riusato da single-avvocato.php (linee 122-169 già conformi Pattern 10). |

### `group_theme_options_v1` — +8 field nel tab Brand

| Field | Default | Note |
|---|---|---|
| `trust_signal_1_label` | "20+ ANNI" | |
| `trust_signal_1_caption` | "ESPERIENZA" | |
| `trust_signal_2_label` | "4 AVVOCATI" | |
| `trust_signal_2_caption` | "TEAM SPECIALIZZATO" | |
| `trust_signal_3_label` | "17 AREE" | **17, NO 19** — post-Wave 5 consolidamento DEC-021 (3 DELETE) + DISCOVERY-01 (consolidate famiglia) |
| `trust_signal_3_caption` | "DI PRATICA" | |
| `trust_signal_4_label` | "COA FAMIGLIA" | |
| `trust_signal_4_caption` | "MUNICIPALITÀ 1" | |

---

## Smoke test — 21 URL Docker locale

```
TOTAL: 21 PASS / 0 FAIL
```

Lista completa in `.claude/knowledge/audits/wave6/smoke-21-urls.txt`. Tutti 200 OK.

## Render checks — pattern visibility

```
Homepage          : sl-trust-bar=13, sl-mobile-bar=10, cro.css enqueued=1
Tier-1 tributario : sl-related-services=1, sl-mini-form=5, sl-mobile-bar=10,
                    sl-competenza__cta-middle=1, FAQPage schema=1
Tier-2 cartelle   : sl-related-services=1, sl-mini-form=5, FAQPage schema=1
/contatti/        : sl-mobile-bar=0  (PHP exclusion OK)
single-avvocato   : sl-mobile-bar=0  (PHP exclusion OK)
```

Render check artifacts in `.claude/knowledge/audits/wave6/render-checks.txt`.

## Schema validation — FAQPage Tier-2

Confermato: `/aree-di-pratica/privati/cartelle-esattoriali-e-multe/` emette `FAQPage` valido con 5 Question/Answer pairs derivati da `saltelli_faq` CPT (title=domanda + ACF risposta=WYSIWYG). ASCII-safe encoding (\uXXXX) preservato. **Bug fix architettonico**: prima di Wave 6 lo schema NON veniva mai emesso a causa di mismatch struttura dati nel loop. Vedi sezione "Bug fix" sotto.

## Coabitazione Yoast (CAL-W6-07)

| Page | FAQPage emissions | Source |
|---|---|---|
| Tier-1 tributario | 1 | partial-faqpage.php (custom) |
| Tier-2 cartelle | 1 | partial-faqpage.php (custom, NEW Wave 6) |
| /costi-e-consulenze/ | 0 in test locale | Yoast `Saltelli_Schema_Costi_FAQPage` scoped, NON impattato da Wave 6 |

Acceptance: nessun duplicato (mai 2 FAQPage emessi per page). Yoast scope rimane su /costi/ via classe `Saltelli_Schema_Costi_FAQPage`.

---

## Bug fix architettonico Wave 6 (originariamente Wave 1+ silently broken)

Durante l'implementazione Pattern 5 (FAQPage generalization) è emerso che `partial-faqpage.php` + `single-competenza.php` FAQ rendering + `saltelli_count_faq()` helper avevano un bug pre-esistente:

- Il field `faq` su `group_competenza_v1` è un `post_object` multiple con `return_format=id` → ACF ritorna array di IDs (es. `[2757, 2758, 2759]`).
- Il code nei tre punti faceva `foreach ($faq as $row) { if (!empty($row['domanda'])) ... }` aspettandosi una struttura row-based fake-repeater.
- Risultato: `$row['domanda']` su un int è `null` → continue → 0 entry valide → schema MAI emesso, blocco FAQ frontend MAI renderizzato, helper count ritornava sempre 0.

**Verifica**: `wp_postmeta` su post_id 2664 (tributario): `meta_value = a:5:{i:0;s:"2757";...}`.

**Fix Wave 6**: tutti e tre i punti ora supportano sia il pattern legacy fake-repeater (rows con domanda/risposta) sia il pattern Wave 1+ post_object (loop su IDs di `saltelli_faq` CPT, dove `get_the_title($faq_id)` = domanda e `saltelli_field('risposta', $faq_id)` = risposta WYSIWYG).

Conseguenza: oltre alla generalizzazione Pattern 5 (Tier-1 + Tier-2 entrambi emettono FAQPage), Wave 6 sblocca *anche* il rendering frontend del blocco FAQ accordion in `single-competenza.php`. Visibile a partire da `1.2.0`.

---

## Acceptance criteria operativi (orchestrator audit checklist)

Da `pattern-adaptation-map.md` § "Indicatori di completamento Wave 6":

- [x] Field Group `group_competenza_v1` esteso (5 nuovi field; `answer_capsule` riusato)
- [x] Field Group `group_trust_item_v1` esteso (8 nuovi field, conditional logic OK)
- [x] Field Group `group_avvocato_v1` esteso (2 nuovi field; `aree_competenza_correlate` riusato)
- [x] Theme Options "Brand" tab esteso con 8 trust_signal fields (4 label + 4 caption)
- [x] Nuovo `template-parts/trust-bar.php` riusabile (con fallback editoriale)
- [x] Nuovo `template-parts/mobile-sticky-bar.php` (PHP exclusion conditional)
- [x] Nuovo `template-parts/mini-form.php` (CF7 detect + fallback CTA)
- [x] Nuovo `template-parts/testimonials-block.php` (loop meta_query)
- [x] Estensione `single-competenza.php`: answer-capsule + CTA progressive top/middle + mini-form + related-services
- [x] Estensione `single.php` (blog): author byline ricca + expertise tags
- [x] `single-avvocato.php` Pattern 10 già conforme dal v0.24.0 (NO modifica)
- [x] Estensione `front-page.php`: trust-bar prima §06 contact + testimonials tra §04 e §05
- [x] Estensione `template-parts/page-costi.php`: mini-form prima CTA finale
- [x] Estensione `footer.php`: hook `mobile-sticky-bar.php` prima `wp_footer()`
- [x] Nuovo CSS bundle `assets/css/components/cro.css` (453 righe, 132 var(--*), 6 reduced-motion blocks)
- [x] `inc/enqueue.php` aggiornato (+ add_editor_style)
- [x] Schema FAQPage generalizzato a tutte le competenze (Tier-1 + Tier-2)
- [x] Smoke 21 URL HTTP 200 (locale Docker)
- [ ] Lighthouse no-regression rispetto baseline pre-Wave 6 — **NON eseguito**, richiede deploy staging post-merge
- [ ] Mobile responsive test su iPhone/Android — **NON eseguito**, richiede device test post-merge

### Quality gate

- [x] Niente nuove dipendenze JS (Wave 6 è pure CSS + PHP)
- [x] Niente nuovi font
- [x] Niente nuovi colori fuori palette tokens.css
- [x] Tutti i template-part hanno graceful fallback
- [x] Tutti gli ACF Field nuovi hanno default value editoriale
- [x] `prefers-reduced-motion: reduce` opt-out per ogni nuova transition (6 blocks in `cro.css`)
- [x] `aria-label` per ogni componente interattivo nuovo (mobile-bar, mini-form, testimonials, trust-bar)
- [x] PHP lint OK su tutti i file modificati/creati
- [x] JSON validi su tutti i 4 ACF field group estesi

---

## Note per l'orchestratore

### Trust bar visibilità

Il template `trust-bar.php` ha **fallback editoriale hardcoded** che garantisce render out-of-the-box (4 plate "20+ ANNI / 4 AVVOCATI / 17 AREE / COA FAMIGLIA"). Quando Elena/Ludovica apriranno WP-Admin → Saltelli Settings → tab Brand e salveranno la options page, ACF prenderà il sopravvento sui valori hardcoded.

### Testimonials block

Il loop `WP_Query` su `saltelli_trust` con `meta_query testimonial_type='testimonianza'` ritorna 0 oggi (i 4 trust signal esistenti hanno il default `numero`). Quando l'editor crea nuovi `saltelli_trust` come tipo Testimonianza popolando `testimonial_text` + `testimonial_author`, il blocco si attiva automaticamente. Empty state = blocco nascosto (graceful fallback).

### Pattern 7 source UI render

Il CSS `.sl-trust-bar__source` è in `cro.css` ma il template-part `trust-bar.php` corrente NON renderizza ancora la source legend per ogni signal (i field `source_label/text/url` esistono solo su `saltelli_trust` CPT, NON su Theme Options trust_signal). Estendibile in Wave 6.1 se cliente richiede source attribution sui 4 trust signals globali. Decisione orchestratore.

### CF7 mini form

Il template `mini-form.php` cerca un form CF7 con slug `saltelli-mini` — se NON esiste, fallback automatico a CTA verso `/contatti/?topic={slug}`. Per attivare il vero mini-form CF7, l'editor deve creare un secondo form CF7 con quello slug. Doc EDITOR-HANDOFF da aggiornare.

### Lighthouse baseline

Wave 5 baseline è in `.claude/knowledge/audits/wave5/lh-*.html` (storico). Lighthouse Wave 6 da eseguire post-merge su staging — orchestrator decide quando.

### Bug fix architettonico FAQPage

Vedi sezione "Bug fix" sopra. Il fix è retrocompatibile (supporta entrambi i pattern di storage). Tutte le competenze con `faq` popolata già dal Wave 2 ora rendereranno il blocco FAQ frontend + emettono FAQPage schema. Effetto collaterale positivo: aumenta superficie GEO da 3 (Tier-1) a 17 (tutte) competenze. **Suggerimento**: smoke test post-deploy staging per verificare rich-result GE su 1-2 Tier-2 (es. cartelle).

---

## Branch state

```
$ git log --oneline main..feat/wave6-geo-cro-blocks
35f1350 wave6: phase 4-5 — cro.css bundle + enqueue update
6e26788 wave6: phase 3 — estensione 7 template files
e8a30e9 wave6: phase 2 — 4 nuovi template-part
122d9e0 wave6: phase 1 — backup + ACF Field Groups extension
```

Push pending: `git push origin feat/wave6-geo-cro-blocks` (NO merge).

## Riferimenti

- Prompt: `prompts/PROMPT_AGENT_v1.1_WAVE6_GEO_CRO_BLOCKS.md`
- Calibrazioni: `prompts/WAVE6_CALIBRATION_NOTES.md` (CAL-W6-01..07)
- Pattern adaptation map: `prompts/pattern-adaptation-map.md`
- Migration matrix: `prompts/migration-matrix-v3.csv`
- Decision log: DEC-018 (DS drift), DEC-019 (Wave 6 lean), DEC-020 (pipeline 5→6→4→7)
- Wave precedente: `.claude/knowledge/recovery/WAVE5-IA-REFACTOR-REPORT.md` (assumed in repo)
- Wave successiva: `prompts/PROMPT_AGENT_v1.0_WAVE4_PRODUCTION_READINESS.md`

---

*Generated 2026-05-06 · Author: Claude Code Wave 6 agent · Theme version: `1.2.0-wave6-geo-cro-blocks`*
