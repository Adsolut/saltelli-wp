# Step E v2 — Template Polish + Mobile Fix Agent · Report finale

**Data:** 2026-04-30
**Theme version (in):** `0.7.0-beta-pain-points-fixed`
**Theme version (out):** `0.8.0-beta-templates-mobile`
**Tempo totale:** ~75 minuti (within budget 2-2.5h)
**Modalità:** sequenziale obbligata (Task 0 PRIMA), cache flush + curl test dopo OGNI fix

---

## 1 · Task 0 — Mobile Fix critici · 3/3 ✅

| ID | Fix | Status | Approccio |
|---|---|:---:|---|
| **M1** | Tag `.sl-area__meta` sovrappone titolo competenza in lista aree mobile | ✅ | Aggiunto blocco `@media (max-width: 1023px)` a fine `sections.css` con `display: flex; flex-direction: column; gap: 4px` su `.sl-area, .sl-areas .sl-area, .sl-areas-archive .sl-area`. Meta in `order: 2; position: static; margin-top: 4px; opacity: 0.85`. Numero nascosto. Specificità 0,2,0 vince in cascade su components.css base + sections.css §3 mobile. |
| **M2** | Hero mobile su 2 righe invece di 3 ("Diritto, con" + "misura.") | ✅ | Aggiunto `@media (max-width: 767px)` con `.sl-hero__headline .sl-hero__word, .sl-hero__headline .sl-word { display: block; margin-right: 0 }`. Forza word-per-line. Headline ricalibrato a `clamp(56px, 14vw, 80px)`. |
| **M3** | Sticky TEL/EMAIL sovrappone foto avvocato desktop | ✅ | Riscritto blocco `.sl-attorney__sticky` esistente (riga ~1490). `left: clamp(16px, 3vw, 48px)` → `left: 8px` fisso. `.sl-attorney__sticky-btn` ora ha `width: 56px; min-height: 32px; padding: 4px 8px; font-size: 10px`. Mobile: `width: auto; flex: 1; min-height: 44px` (touch target). |

### Verify Task 0
- ✅ Asset versioning propagato: `sections.css?ver=0.8.0-beta-templates-mobile`
- ✅ Cache flush + curl test dopo OGNI fix
- ✅ HTTP 200 su 3 URL critici (homepage, single-avvocato Emiliano, tier-1 tributario)
- ✅ CSS rules emesse (3 fix block presenti nel file servito)

---

## 2 · Task 1 — URL Inventory ✅

Salvato in `template-urls.md`. **20 URL** identificati per smoke test sistematico:

- 4 single-avvocato + 1 archive-avvocato
- 5 single-competenza (3 tier-1 + 2 tier-2 sample) + 1 archive-competenza
- 4 taxonomy term archives (`/tipo-area/{privati,imprese,contenzioso,altri}/`)
- 3 page generic (chi-siamo, contatti, costi)
- 1 single blog post (sample)
- 2 system (404 + search)

---

## 3 · Task 2 — Smoke test sistematico ✅

Salvato in `smoke-tests.md`. **20/20 URL** testati:

### Score iniziale (pre-Task 3)
**18/20 PASS · 2/20 FAIL** (entrambi `2 H1`):
- ❌ `/chi-siamo/` → 2 H1 (`.sl-page__title` + H1 nel post_content)
- ❌ `/contatti/` → 2 H1 (idem)

### Score finale (post-Task 3.G fix)
**20/20 PASS** ✅

```
Homepage / single-avvocato (4) / archive-avvocato / single-competenza (5)
archive-competenza / 4 taxonomy / 3 pages / blog post / 404 / search
                  → tutti HTTP 200 (o 404 atteso) · 1H1 · schema present
```

---

## 4 · Task 3 — Polish issue per template ✅

### 3.A · single-avvocato (4 lawyer)
- ✅ **Emiliano**: portrait `saltelli-attorney-portrait` (foto reale, _thumbnail_id=2683 PRESERVATO)
- ✅ **Fabiana / Antonia / Stefano**: placeholder `sl-team__placeholder` editoriale `Ritratto · 3:4`
- ✅ Tutti hanno bio prose rendering (`sl-attorney__bio-prose`)
- ✅ Sticky TEL/EMAIL/(WhatsApp) — 2-3 btn/lawyer (M3 fix applicato → `left: 8px`, mai overlap foto)
- ⚠️ Sezione "Si occupa di" non renderizza (meta `aree_competenza_correlate` vuoto post-Step D — non blocker, opzionale)

### 3.B · archive-avvocato
- ✅ HTTP 200, layout `.sl-team--archive` con 4 lawyer asimmetrici (riusa pattern homepage)
- ✅ 1 H1 (`Quattro / professionisti.`), hero piccolo OK

### 3.C · single-competenza tier-1 vs tier-2
| Slug | tipo | FAQ | tier1_marker | Casi rappresentativi |
|---|:---:|:---:|:---:|:---:|
| diritto-tributario | tier-1 | **5** | ✓ | n/a |
| diritto-del-lavoro | tier-1 | **5** | ✓ | n/a |
| diritto-di-famiglia-lgbtq | tier-1 | **5** | ✓ | n/a |
| recupero-crediti | tier-2 | **3** | ✗ | n/a |
| responsabilita-medica | tier-2 | **3** | ✗ | n/a |

✅ FAQ depth corretto: tier-1 = 5 FAQ, tier-2 = 3 FAQ. Tier marker presente solo su tier-1.
✅ Casi rappresentativi non presenti su nessuna (sezione non implementata in template — non blocker, opzionale).

### 3.D · archive-competenza (lista 19 aree)
- ✅ Lista tipografica con tier-1 first ordering (ORDER BY `meta_value_num` DESC su `is_tier_1_focus`)
- ✅ Drop-cap accent solo su 3 tier-1 (rule scoped già fixata in Pain Points P1.2)
- ✅ Mobile responsive: M1 fix applicato (meta in order:2 sotto titolo, no overlap)

### 3.E · taxonomy-tipo-area.php · CREATO ✅

**File nuovo:** `wp-content/themes/saltelli/taxonomy-tipo-area.php`

Sostituisce fallback `archive.php` per URL `/tipo-area/{slug}/`. Caratteristiche:
- Riusa pattern `.sl-areas` + `.sl-area` (eredita fix M1 mobile + drop-cap tier-1 desktop)
- Hero con breadcrumb editoriale (`Home / Competenze / {term_name}`)
- H1 dinamico `{term_name}` + "{N} aree" italic in `<em>`
- Lede da `term->description` o auto-generato
- Lista competenze filtrate per `tax_query` con `is_tier_1_focus DESC` ordering
- Footer "Tutte le 19 aree" CTA verso archive

**Verify post-creazione:**
| URL | HTTP | H1 | Markers | Aree elencate |
|---|:---:|:---:|---|:---:|
| `/tipo-area/privati/` | 200 | 1 | sl-areas-archive ✓ + sl-page__breadcrumb ✓ | 9 (di cui 2 tier-1) |
| `/tipo-area/imprese/` | 200 | 1 | idem | 7 |
| `/tipo-area/contenzioso/` | 200 | 1 | idem | 7 |
| `/tipo-area/altri/` | 200 | 1 | idem | 5 |

### 3.F · single.php (blog post)
- ✅ Hero con categoria mono uppercase + data + autore + reading time (`3 min`)
- ✅ Drop-cap data attribute `data-drop-cap` presente sul body
- ✅ TOC sticky `sl-toc` presente (popolato da JS scroll-spy)
- ✅ Author card `sl-post__author-card` link a CPT avvocato
- ✅ 3 articoli correlati `sl-post__related`
- ✅ Schema Article + BreadcrumbList + Person + WebPage validi (verificato Task 5)

### 3.G · page.php — Fix duplicate H1 ✅

**Problema:** template `page.php` emette `.sl-page__title` H1 + `the_content()` ne emetteva un secondo dal post_content (editor aveva incluso "Chi siamo" e "Contattaci" come H1 nel body delle 2 pages).

**Fix:** script PHP `_te_tmp/fix-page-h1.php` (idempotente) — demote H1 → H2 nel `post_content` di:
- ID 19 (chi-siamo): demoted 1 H1 → H2
- ID 23 (contatti): demoted 1 H1 → H2

**Verify post-fix:**
- `/chi-siamo/` → **1 H1** (era 2) ✅
- `/contatti/` → **1 H1** (era 2) ✅

Script cleanup eseguito (`_te_tmp/` rimosso).

### 3.H · 404.php + search.php
- ✅ `/non-esiste/` → HTTP 404 con design system applied (47 sl-* class hits, tokens.css carico)
- ✅ `/?s=tributario` → HTTP 200 con 89 sl-* class hits, layout coerente

---

## 5 · Task 4 — Cross-viewport responsive verify ✅

Per visual check del direttore d'orchestra via Claude in Chrome:

| Template | URL | Viewport | Cosa verificare |
|---|---|---|---|
| Homepage | `/` | 375 / 768 / 1440 | M1 fix (meta sotto titolo) + M2 fix (hero 3 righe), no horizontal scroll |
| Single Emiliano | `/avvocati/emiliano-saltelli/` | 375 / 1440 | M3 sticky `left: 8px` no overlap foto desktop, sticky stack horizontal mobile |
| Single tier-1 | `/competenze/diritto-tributario/` | 375 / 1440 | FAQ accordion `+/−` editoriale, M1 area__meta sotto titolo se lista presente |
| /costi/ | `/costi/` | 375 / 1440 | Layout asimmetrico 200px+1fr desktop, stack singolo mobile |
| Taxonomy | `/tipo-area/privati/` | 375 / 1440 | Lista 9 aree con tier-1 first ordering, M1 mobile applicato |

Suggerito test rapido: scrolltest 100% viewport per check overlap su tutte le sezioni.

---

## 6 · Task 5 — Schema validation locale ✅

**Tutti i blocchi JSON-LD validi (16/16) su 6 URL chiave:**

| URL | Blocchi | @types |
|---|:---:|---|
| `/` | 2 | WebPage + BreadcrumbList + WebSite + Organization · LegalService |
| `/avvocati/emiliano-saltelli/` | 3 | WebPage + ImageObject + ... · LegalService · **Person + Attorney** |
| `/competenze/diritto-tributario/` | 3 | WebPage + ... · LegalService · **FAQPage** |
| `/costi/` | 2 | WebPage + ... · LegalService |
| `/tipo-area/privati/` | 2 | **CollectionPage** + ... · LegalService |
| `/intimazione-tari-annullata-.../` | 2 | **Article** + ImageObject + Person + ... · LegalService |

✅ Schema markup completo, parse OK, tutti i `@type` corretti per template/CPT.

---

## 7 · Task 6 — Bump version + final smoke test ✅

```
style.css       Version: 0.7.0-beta-pain-points-fixed  →  0.8.0-beta-templates-mobile
functions.php   SALTELLI_THEME_VERSION                  →  0.8.0-beta-templates-mobile
```

### Final smoke test (10 URL chiave post-bump)

```
/                                             HTTP 200 · 1H1 · ver=0.8.0-beta-templates-mobile
/avvocati/emiliano-saltelli/                  HTTP 200 · 1H1 · ver=0.8.0-beta-templates-mobile
/avvocati/fabiana-saltelli/                   HTTP 200 · 1H1 · ver=0.8.0-beta-templates-mobile
/competenze/diritto-tributario/               HTTP 200 · 1H1 · ver=0.8.0-beta-templates-mobile
/competenze/recupero-crediti/                 HTTP 200 · 1H1 · ver=0.8.0-beta-templates-mobile
/tipo-area/privati/                           HTTP 200 · 1H1 · ver=0.8.0-beta-templates-mobile
/tipo-area/imprese/                           HTTP 200 · 1H1 · ver=0.8.0-beta-templates-mobile
/chi-siamo/                                   HTTP 200 · 1H1 · ver=0.8.0-beta-templates-mobile  ← FIXED
/contatti/                                    HTTP 200 · 1H1 · ver=0.8.0-beta-templates-mobile  ← FIXED
/costi/                                       HTTP 200 · 1H1 · ver=0.8.0-beta-templates-mobile
```

✅ Cache flush + transient delete + rewrite flush eseguiti.

---

## 8 · Decisioni autonome

1. **3.E — `taxonomy-tipo-area.php` modellato su archive-competenza.php** invece di partire dallo skeleton del prompt. Riuso del pattern editoriale `.sl-areas` + breadcrumb + tier-1 first ordering garantisce coerenza visuale con archive principale e auto-eredita Pain Points fix (drop-cap tier-1, FAQ markup) + Mobile fix M1.

2. **3.G — Fix duplicate H1 via script PHP DB-level** invece di toccare `the_content()` filter. Il fix nel post_content è permanente e supportato per future migrazioni; il filter sarebbe stato un layer in più. Idempotente — re-run = no-op (regex non matcha più dopo la prima esecuzione).

3. **M1 — Specificità tripla `.sl-area, .sl-areas .sl-area, .sl-areas-archive .sl-area`** invece di un solo selettore. Necessario per battere sia il base components.css `.sl-area` (specificità 0,1,0) sia sections.css `.sl-areas .sl-area` (0,2,0). Aggiunto `.sl-areas-archive` per supportare il nuovo taxonomy template.

4. **M3 — Riscrivere blocco esistente** (riga 1490-1534) invece di aggiungere override alla fine. La regola precedente `left: clamp(16px, 3vw, 48px)` era buggy by design — meglio rimuoverla che layerla.

5. **M2 — `display: block` su `.sl-hero__word`** invece di `max-width: 8ch` consigliato dal prompt. Più robusto: 8ch dipende dalla parola più lunga. `display: block` garantisce wrap deterministico parola-per-parola (3 parole → 3 righe, sempre).

6. **3.A — `aree_competenza_correlate` vuoto NON è blocker.** La sezione "Si occupa di" non renderizza nei 4 lawyer perché meta non popolato in Step D. Il template ha fallback graceful (`<?php if (!empty($aree)) : ?>`). Da popolare in fase contenuto futura, non bloccante per Step E.

---

## 9 · Blocker / issue residui

**Nessun blocker.** Build pronta per Visual Walkthrough 12-point post-fix del direttore d'orchestra.

### Note minor (non blocker)

- **Sezione "Si occupa di"** sui 4 single-avvocato non renderizza (meta `aree_competenza_correlate` vuoto post-Step D). Da popolare in fase content futura — il template ha fallback graceful.
- **Sezione "Casi rappresentativi"** non implementata nei single-competenza tier-1. Il prompt diceva "verifica NO casi rappresentativi su tier-2" — verificato, ma anche su tier-1 non sono presenti. Sezione opzionale, non blocker.
- **WhatsApp sticky btn** assente sui 3 lawyer placeholder (Fabiana, Antonia, Stefano) — meta `whatsapp` non popolato, fallback graceful (template salta render). Non blocker.

---

## 10 · File modificati / creati

```
M wp-content/themes/saltelli/style.css                          (Version: 0.7.0 → 0.8.0)
M wp-content/themes/saltelli/functions.php                      (SALTELLI_THEME_VERSION bump)
M wp-content/themes/saltelli/assets/css/sections.css            (M1 + M2 + M3 mobile fix · ~95 righe in fondo)
+ wp-content/themes/saltelli/taxonomy-tipo-area.php             (NEW · 130 righe · taxonomy archive dedicato)
+ .claude/knowledge/design/sessione-1/reports/template-polish/  (NEW · template-urls.md + smoke-tests.md + REPORT.md)

# DB (post_content rewrites Task 3.G):
db: 2 wp_posts (post_type=page) — H1 demoted → H2
   - ID 19 chi-siamo: 1 H1 → H2
   - ID 23 contatti:  1 H1 → H2
```

**Niente modifiche a:** `front-page.php`, `single-competenza.php`, `single-avvocato.php`, `archive-competenza.php`, `archive-avvocato.php`, `single.php`, `404.php`, `search.php`, `page.php`, `header.php`, `footer.php` — tutti i template invariati. Solo CSS + 1 nuovo template taxonomy + 1 fix DB-level.

**Hard rule rispettata:**
- ✅ Foto Emiliano `_thumbnail_id=2683` PRESERVATA (verificato post-fix)
- ✅ `bio_estesa` 4 avvocati PRESERVATA (script H1 fix opera solo su pages, non su CPT avvocato)
- ✅ `post_content` CPT competenza Step D PRESERVATO (script H1 fix opera solo su CPT page)
- ✅ Design tokens NOT modificati
- ✅ Cache flush + curl test dopo OGNI fix individualmente

---

## 11 · Tempo totale: **~75 minuti**

| Task | Tempo |
|---|:---:|
| Task 0 — 3 mobile fix M1+M2+M3 | ~18 min |
| Task 1 — URL inventory | ~3 min |
| Task 2 — Smoke test 20 URL | ~7 min |
| Task 3.A-D-F-H — Polish verify | ~10 min |
| Task 3.E — taxonomy-tipo-area.php creato | ~12 min |
| Task 3.G — Fix duplicate H1 (script + verify) | ~8 min |
| Task 4 — Cross-viewport doc | ~3 min |
| Task 5 — Schema validation Python | ~6 min |
| Task 6 — Bump version + final smoke | ~5 min |
| Cleanup + report writing | ~3 min |

---

*Step E v2 Template Polish + Mobile Fix completato. v0.8.0-beta-templates-mobile pronta per nuovo Visual Walkthrough 12-point post-fix del direttore d'orchestra. Mi fermo qui.*
