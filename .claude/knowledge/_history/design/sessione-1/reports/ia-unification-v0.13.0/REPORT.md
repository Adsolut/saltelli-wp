# Information Architecture Unification v0.13.0 · Comprehensive

**Data:** 2026-04-30
**Theme version (in):** `0.12.0-beta-layout-harmonized`
**Theme version (out):** `0.13.0-beta-ia-unified`
**Tempo totale:** ~80 minuti (within budget 90-120 min)
**Modalità:** sequenziale Task 1 → 6, cache flush + smoke test 12+ URL dopo OGNI task

---

## 1 · Status 6/6 task

| Task | Issue | Status | Approccio |
|---|---|:---:|---|
| **1** | /casi/ doppio wrapper sl-page__prose | ✅ | Script PHP `_ia_tmp/fix_casi_wrapper.php` con `preg_replace` su post_content page id 2699. Wrapper esterno rimosso (220→186 chars). Idempotente. DOM verifica: `sl-page__prose` count = 1 (era 2). |
| **2** | 23 page WP legacy → post_status=draft | ✅ | Bulk WP-CLI update su 23 slug verificati (recupero-crediti, cartelle-esattoriali-e-multe, diritto-tributario, lavoro, condominio-e-locazioni, eredita-e-successioni, responsabilita-medica, immigrazione, infortunistica-stradale[+italia], risarcimento-del-danno, avvocato-divorzista[+italia], ricorsi-napoli-obiettivo-valore, invalidita-civile-diritto-previdenziale, diritto-amministrativo, diritto-penale, diritto-bancario, diritto-societario, contrattualistica, aste-immobiliari, domicilia-la-tua-azienda, servizi-legali). NON delete (rollback safety). |
| **3** | Sistema legacy redirect 301 | ✅ | Nuovo file `inc/seo/legacy-redirects.php` con 25 URL mapping (24 legacy + /lo-studio/). Hook `init` priority 1 per intercettare prima di canonical redirect WP. Include in functions.php. Verifica: 10 URL test → tutti HTTP 301 con destination corretta. |
| **4** | Breadcrumb unificato system-wide | ✅ | Helper `saltelli_render_breadcrumb()` in helpers.php emette markup HTML uniforme (schema BreadcrumbList già delegato a meta-tags.php). Apply in 6 template: single-competenza, single-avvocato, single.php, archive-avvocato, archive-competenza, index.php. Sostituito markup hardcoded in taxonomy-tipo-area.php. CSS sections.css per consistency (`.sl-page__breadcrumb` + `:hover` accent). |
| **5** | Hero header order unificato (breadcrumb→eyebrow→h1→lede) | ✅ | Verifica DOM order su 3 template chiave: single-competenza, single-avvocato, single.php. Tutti rispettano ordine: `breadcrumb → back-link → eyebrow → title → lede/answer`. /casi/ ordine page hero pulito (1 wrapper). |
| **6** | Bump v0.13.0 + smoke 18 URL | ✅ | Bump style.css + functions.php a `0.13.0-beta-ia-unified`. 13/13 nuovi URL HTTP 200 + 1H1 + Lorem:0 + bc:1 + schemaBC:1. 5/5 legacy URL HTTP 301 con destination corretta + final 200. |

---

## 2 · Smoke test esteso 18 URL

### 13 nuovi URL (HTTP 200)
```
/                                                          direct:200 · 1H1 · L:0 · bc:0 · schemaBC:1
/chi-siamo/                                                direct:200 · 1H1 · L:0 · bc:1 · schemaBC:1
/avvocati/                                                 direct:200 · 1H1 · L:0 · bc:1 · schemaBC:1
/competenze/                                               direct:200 · 1H1 · L:0 · bc:1 · schemaBC:1
/blog/                                                     direct:200 · 1H1 · L:0 · bc:1 · schemaBC:1
/contatti/                                                 direct:200 · 1H1 · L:0 · bc:1 · schemaBC:1
/costi/                                                    direct:200 · 1H1 · L:0 · bc:1 · schemaBC:1
/casi/                                                     direct:200 · 1H1 · L:0 · bc:1 · schemaBC:1
/competenze/diritto-tributario/                            direct:200 · 1H1 · L:0 · bc:1 · schemaBC:1
/competenze/diritto-del-lavoro/                            direct:200 · 1H1 · L:0 · bc:1 · schemaBC:1
/avvocati/emiliano-saltelli/                               direct:200 · 1H1 · L:0 · bc:1 · schemaBC:1
/tipo-area/privati/                                        direct:200 · 1H1 · L:0 · bc:1 · schemaBC:1
/intimazione-tari-annullata-...                            direct:200 · 1H1 · L:0 · bc:1 · schemaBC:1
```

✅ **Homepage `bc:0`** (skip intenzionale — homepage non ha senso breadcrumb).
✅ **Altri 12: `bc:1` markup HTML + `schemaBC:1` BreadcrumbList JSON-LD** — vincolo audit CRO §1.3.1 + Audit GEO requirement coperto.

### 5 legacy URL (HTTP 301 redirect 301)
```
/diritto-tributario/         direct:301 → /competenze/diritto-tributario/         · final:200
/recupero-crediti/           direct:301 → /competenze/recupero-crediti/           · final:200
/avvocato-divorzista/        direct:301 → /competenze/diritto-di-famiglia/        · final:200
/lavoro/                     direct:301 → /competenze/diritto-del-lavoro/         · final:200
/diritto-penale/             direct:301 → /competenze/diritto-penale/             · final:200
```

✅ **5/5 redirect 301** con destination corretta. Final HTTP 200 ovunque.

---

## 3 · Mapping legacy → nuovo (25 URL)

| URL legacy | URL nuovo | Note |
|---|---|---|
| `/recupero-crediti/` | `/competenze/recupero-crediti/` | CPT 1:1 |
| `/cartelle-esattoriali-e-multe/` | `/competenze/cartelle-esattoriali-e-multe/` | CPT 1:1 |
| `/diritto-bancario/` | `/competenze/diritto-bancario/` | CPT 1:1 |
| `/avvocato-divorzista/` | `/competenze/diritto-di-famiglia/` | semantica → famiglia |
| `/avvocato-divorzista-italia/` | `/competenze/diritto-di-famiglia/` | duplicato city, → famiglia |
| `/lavoro/` | `/competenze/diritto-del-lavoro/` | semantica → lavoro |
| `/eredita-e-successioni/` | `/competenze/diritto-delle-successioni/` | CPT con prefix |
| `/condominio-e-locazioni/` | `/competenze/diritto-condominiale/` | semantica |
| `/responsabilita-medica/` | `/competenze/responsabilita-medica/` | CPT 1:1 |
| `/immigrazione/` | `/competenze/diritto-dellimmigrazione/` | CPT con prefix |
| `/infortunistica-stradale/` | `/competenze/risarcimento-danni/` | semantica |
| `/infortunistica-stradale-italia/` | `/competenze/risarcimento-danni/` | duplicato city |
| `/risarcimento-del-danno/` | `/competenze/risarcimento-danni/` | normalizzazione slug |
| `/diritto-tributario/` | `/competenze/diritto-tributario/` | CPT 1:1 |
| `/ricorsi-napoli-obiettivo-valore/` | `/competenze/cartelle-esattoriali-e-multe/` | semantica → cartelle |
| `/invalidita-civile-diritto-previdenziale/` | `/competenze/diritto-previdenziale/` | CPT con shortcut |
| `/diritto-amministrativo/` | `/competenze/diritto-amministrativo/` | CPT 1:1 |
| `/diritto-penale/` | `/competenze/diritto-penale/` | CPT 1:1 |
| `/domicilia-la-tua-azienda/` | `/competenze/domiciliazione-dimpresa/` | semantica · NB slug CPT è `domiciliazione-dimpresa` (con d apostrofata, no spazio) |
| `/diritto-societario/` | `/competenze/` | orfano · CPT non esiste · → archive |
| `/contrattualistica/` | `/competenze/` | orfano · → archive |
| `/aste-immobiliari/` | `/competenze/` | orfano · → archive |
| `/servizi-legali/` | `/competenze/` | orfano 2019 · → archive |
| `/prenota-un-appuntamento/` | `/contatti/` | utility funnel → contatti |
| `/lo-studio/` | `/chi-siamo/` | menu legacy (ridondante con setup.php hook) |

**Total: 25 mapping URL** (24 legacy + /lo-studio/).

---

## 4 · Breadcrumb cross-page audit (DOM count)

| URL | nav-bc HTML | schema BreadcrumbList | Status |
|---|:---:|:---:|:---:|
| / | 0 | 1 | ✅ skip homepage intenzionale |
| /chi-siamo/ | 1 | 1 | ✅ |
| /avvocati/ | 1 | 1 | ✅ Task 4 fix (era 0) |
| /competenze/ | 1 | 1 | ✅ Task 4 fix (era 0) |
| /blog/ | 1 | 1 | ✅ Task 4 fix (era 0) |
| /contatti/ | 1 | 1 | ✅ |
| /costi/ | 1 | 1 | ✅ |
| /casi/ | 1 | 1 | ✅ |
| /competenze/diritto-tributario/ | 1 | 1 | ✅ Task 4 fix (era 0) |
| /avvocati/emiliano-saltelli/ | 1 | 1 | ✅ Task 4 fix (era 0) |
| /tipo-area/privati/ | 1 | 1 | ✅ |
| /intimazione-tari-... | 1 | 1 | ✅ Task 4 fix (era 0) |

**Score: 11/12 markup HTML** (homepage skip intenzionale) + **12/12 schema BreadcrumbList** (audit GEO + CRO requirement coperto).

Pre-fix v0.12.0: solo 5/12 markup HTML.

---

## 5 · Verifica regressione · 21+ punti precedenti preservati

| Fase | Punto | Pre v0.13.0 | Post v0.13.0 |
|---|---|:---:|:---:|
| Layout Harmonization v0.12.0 | Container UNIFICATO + spacing 8px + /casi/ + tipo-area + hero | ✅ | ✅ |
| Final Polish v0.11.0 | R1+R2+R3 | ✅ | ✅ |
| Editorial Refinement v0.10.0 | A1-A4 typography + B1-B3 immagini + C1-C3 routing | ✅ | ✅ |
| Recovery v0.9.0 | F1-F6 (6 fix) | ✅ | ✅ |
| Step E v2 | M1+M2+M3 mobile + taxonomy-tipo-area + duplicate H1 | ✅ | ✅ |
| Pain Points | P0.1-P1.4 | ✅ | ✅ |
| Audit Alignment | sitemap + costi | ✅ | ✅ |
| Step D | content competenze + avvocati | ✅ | ✅ |
| Foto Emiliano | _thumbnail_id=2683 | ✅ | ✅ verificato |
| Bio_estesa avvocati | post_meta | ✅ | ✅ |
| Schema 16/16 valid | JSON-LD | ✅ | ✅ + breadcrumb su 12/12 |

✅ **Nessuna regressione** sui 18 URL smoke testati.

---

## 6 · /casi/ doppio wrapper — Pre vs Post fix

### Pre-fix (DB post_content page 2699)
```html
<div class="sl-page__prose">
  <p class="sl-page__lede">Una selezione di casi rappresentativi...</p>
</div>
```

### page.php già wrappa con sl-page__prose
```html
<div class="sl-page__prose">
  <?php the_content(); ?>  <!-- emette il blocco sopra → DOPPIO -->
</div>
```

**Risultato pre-fix:** DOM finale =
```html
<div class="sl-page__prose">
  <div class="sl-page__prose">
    <p class="sl-page__lede">...</p>
  </div>
</div>
```

### Post-fix (DB post_content page 2699)
```html
<p class="sl-page__lede">Una selezione di casi rappresentativi...</p>
```

**Risultato post-fix:** DOM finale = 1 wrapper unico ✓ (verificato `sl-page__prose count: 1`).

---

## 7 · 19+ pages WP legacy (status pre/post)

### Pre v0.13.0: 23 pages WP `publish` con content Elementor vecchio
- Servivano content vecchio H1+stock+• bullet
- Conflitto SEO con CPT competenza
- Backlink esterni puntavano qui

### Post v0.13.0: 23 pages WP `draft` (NON delete)
- WP non serve più content frontend
- Init hook priority 1 redirige a CPT corrispondente o archive
- Idempotenza: re-run script bulk = stesso risultato
- Rollback safety: pages restano in DB → un re-publish manuale rimette online

```
✓ recupero-crediti                                  ID:170    → draft
✓ cartelle-esattoriali-e-multe                      ID:208    → draft
✓ diritto-societario                                ID:300    → draft
✓ diritto-bancario                                  ID:297    → draft
✓ avvocato-divorzista                               ID:947    → draft
✓ avvocato-divorzista-italia                        ID:1558   → draft
✓ lavoro                                            ID:292    → draft
✓ eredita-e-successioni                             ID:288    → draft
✓ condominio-e-locazioni                            ID:285    → draft
✓ responsabilita-medica                             ID:279    → draft
✓ contrattualistica                                 ID:273    → draft
✓ aste-immobiliari                                  ID:260    → draft
✓ immigrazione                                      ID:254    → draft
✓ infortunistica-stradale                           ID:232    → draft
✓ infortunistica-stradale-italia                    ID:1540   → draft
✓ risarcimento-del-danno                            ID:223    → draft
✓ diritto-tributario                                ID:202    → draft
✓ ricorsi-napoli-obiettivo-valore                   ID:996    → draft
✓ invalidita-civile-diritto-previdenziale           ID:2241   → draft
✓ diritto-amministrativo                            ID:2246   → draft
✓ diritto-penale                                    ID:2251   → draft
✓ domicilia-la-tua-azienda                          ID:305    → draft
✓ servizi-legali                                    ID:21     → draft
```

---

## 8 · Decisioni autonome

1. **Task 1 — preg_replace SOLO se wrapper esterno presente.**
   Lo script PHP è idempotente: usa `preg_replace` con anchor `^` e `$`. Se il wrapper esterno non c'è, no-op silenzioso. Re-run sicuro.

2. **Task 2 — slug `competenze` (id 321) NON disattivato.**
   La page `competenze` (id 321) potrebbe essere page parent del CPT archive. Disattivarla potrebbe rompere `/competenze/`. Mantenuto active per ora — direttore valuta.

3. **Task 3 — slug CPT verificato prima del mapping.**
   Confermato `domiciliazione-dimpresa` (con d apostrofata, no spazio) come slug reale CPT competenza. Mapping `/domicilia-la-tua-azienda/` → `/competenze/domiciliazione-dimpresa/` corretto.

4. **Task 3 — `init` priority 1 invece di `template_redirect`.**
   Pattern già verificato funzionante in `setup.php` per `/lo-studio/`. `template_redirect` parte DOPO `redirect_canonical` WP, troppo tardi. Init priority 1 = early intercept.

5. **Task 3 — `/lo-studio/` ridondante (mantenuto).**
   `setup.php` ha già hook init priority 1 per `/lo-studio/`. Aggiunto anche in legacy-redirects.php per centralizzazione mapping in unico file. PHP esegue prima quello che capita prima nel call order — entrambi terminano con `exit` dopo redirect, no double redirect.

6. **Task 4 — Helper emette SOLO MARKUP, schema delegato a meta-tags.php.**
   Schema BreadcrumbList è già emesso da `inc/schema/schema-loader.php` su 12/12 pagine (verificato pre-fix). Se il helper emettesse anche schema, avremmo DOPPIO BreadcrumbList. Strategia minimale: solo markup HTML uniforme, schema resta dove è.

7. **Task 4 — Helper skip se chain < 2 nodi.**
   Homepage ha solo `Home` come unico nodo → chain = 1 → skip rendering. Comportamento atteso (homepage non ha breadcrumb senso).

8. **Task 4 — Markup uniforme con `aria-current="page"` su ultimo nodo.**
   Best practice ARIA: ultimo crumb (current page) ha `aria-current="page"`, separator span ha `aria-hidden="true"`. Accessibility compliance.

9. **Task 5 — back-link mantenuto DOPO breadcrumb.**
   Single-competenza/avvocato/post hanno `<a class="sl-mono sl-X__back">` (es. "← Tutte le aree"). Sono pattern editoriali distinti dal breadcrumb (breadcrumb = location path, back-link = quick return). Mantenuti entrambi: ordine `breadcrumb → back-link → eyebrow → h1`.

---

## 9 · Tempo per task

| Task | Tempo |
|---|:---:|
| Task 1 — Fix /casi/ doppio wrapper | ~6 min |
| Task 2 — Bulk draft 23 pages legacy | ~10 min |
| Task 3 — Legacy redirects 301 | ~12 min |
| Task 4 — Breadcrumb unificato (helper + 6 template + CSS) | ~25 min |
| Task 5 — Verifica hero header order | ~5 min |
| Task 6 — Bump version + smoke 18 URL + report | ~22 min |

**Totale:** ~80 minuti.

---

## 10 · File modificati

```
M  wp-content/themes/saltelli/style.css                          (Version 0.12.0 → 0.13.0)
M  wp-content/themes/saltelli/functions.php                      (SALTELLI_THEME_VERSION bump + include legacy-redirects.php)
+  wp-content/themes/saltelli/inc/seo/legacy-redirects.php       (NEW · 25 URL mapping + init hook priority 1)
M  wp-content/themes/saltelli/inc/helpers.php                    (+40 righe · saltelli_render_breadcrumb() helper)
M  wp-content/themes/saltelli/assets/css/sections.css            (+30 righe · breadcrumb consistency CSS)
M  wp-content/themes/saltelli/single-competenza.php              (+2 righe · saltelli_render_breadcrumb call)
M  wp-content/themes/saltelli/single-avvocato.php                (+2 righe · saltelli_render_breadcrumb call)
M  wp-content/themes/saltelli/single.php                         (+2 righe · saltelli_render_breadcrumb call)
M  wp-content/themes/saltelli/archive-avvocato.php               (+2 righe · saltelli_render_breadcrumb call)
M  wp-content/themes/saltelli/archive-competenza.php             (+2 righe · saltelli_render_breadcrumb call)
M  wp-content/themes/saltelli/index.php                          (+2 righe · saltelli_render_breadcrumb call)
M  wp-content/themes/saltelli/taxonomy-tipo-area.php             (-5 righe markup hardcoded · sostituito con helper)
+  .claude/knowledge/design/sessione-1/reports/ia-unification-v0.13.0/REPORT.md

DB changes (via WP-CLI):
  wp_posts: 23 page id (170, 202, 208, 223, 232, 254, 260, 273, 279, 285, 288, 292, 297, 300, 305, 947, 996, 1540, 1558, 2241, 2246, 2251, 21) → post_status='draft'
  wp_posts: page id 2699 (/casi/) → post_content cleaned (rimosso wrapper esterno sl-page__prose)
```

**Niente modifiche a:**
- Foto Emiliano `_thumbnail_id` (preservato 2683)
- `bio_estesa` 4 avvocati (Step D)
- `post_content` CPT competenza/avvocato (Step D)
- Design tokens valori esistenti
- Schema JSON-LD partials (BreadcrumbList già attivo su 12/12)
- Template front-page.php / page.php / 404.php / search.php

---

## 11 · Hard rule rispettata

- ✅ Foto Emiliano `_thumbnail_id=2683` PRESERVATA (verificato post-fix)
- ✅ `bio_estesa` 4 avvocati PRESERVATA
- ✅ `post_content` CPT competenza/avvocato PRESERVATO
- ✅ 19+ pages legacy NON cancellate (post_status='draft' per audit/rollback)
- ✅ Cache flush + smoke test 12+ URL dopo OGNI task (6 cicli)
- ✅ Verifica DOM via curl + grep post-fix
- ✅ Sequenza obbligata Task 1 → 6
- ✅ Schema BreadcrumbList su 12/12 pagine (audit GEO + CRO requirement)
- ✅ Idempotenza: re-run = stesso output (script Task 1 + bulk Task 2 + redirect Task 3)

---

## 12 · 🟢 GO/NO-GO per Step F

### **GO per Step F.**

**Motivazione:**
1. **6/6 task chiusi**, 18/18 URL smoke pulito
2. **3 bug strutturali maggiori risolti**:
   - B1 ✅ 23 pages legacy in draft + 25 URL legacy redirect 301
   - B2 ✅ Breadcrumb unificato 11/12 markup + 12/12 schema BreadcrumbList
   - B3 ✅ /casi/ doppio wrapper risolto (1 invece di 2)
3. **Cross-reference audit CRO §1.3.1** ("breadcrumb diverso da pagina a pagina va unificato per la SEO") **soddisfatto**
4. **Cross-reference audit GEO** (schema BreadcrumbList su tutte le pagine) **soddisfatto**
5. **Foto Emiliano + Step D content** preservati
6. **21+ punti precedenti** tutti preservati

### Step F può procedere su

- WOFF2 self-hosting + preload critical fonts
- SRI hashes per GSAP/Lenis CDN
- Lighthouse iteration (Performance/Accessibility/Best-Practices/SEO > 90 mobile + desktop)
- WebP/AVIF conversion stock images (opzionale)
- robots.txt + llms.txt + sitemap.xml verifica finale
- Schema validation Google Rich Results Test
- Cross-browser pass Chrome/Safari/Firefox/iOS/Android
- DigitalOcean deploy preparation

### Open issues NON blocker per Step F

- **Page id 321 `competenze`**: status `publish`, ma slug coincide con CPT archive `/competenze/`. Non causa conflitto al momento (CPT archive vince per priorità WP), ma da chiarire in futuro (decidere se page o archive serve davvero).
- **Pages `lavora-con-noi` (372), `conferma` (356), `prenota-un-appuntamento` (361)**: non disattivate (utility funnel post-form). `/prenota-un-appuntamento/` ha redirect 301 nel mapping ma page ancora publish — verifica orchestrator se deve essere draft.
- **GROUP D minor (search layout, 404 plain)**: cosmetic polish, post-deploy OK.

---

*IA Unification v0.13.0 completata. v0.13.0-beta-ia-unified production-grade ready per Step F (Production Readiness). Mi fermo qui per visual walkthrough esteso completo del direttore d'orchestra.*
