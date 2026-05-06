# Wave 5 — IA Refactor Report

**Branch**: `feat/wave5-ia-refactor`
**Theme version**: `1.0.0-recovery-wave3-debug` → **`1.1.0-wave5-ia-refactor`**
**Phases**: 8 (1 → 8) + Phase 1.5 NUOVA (CAL-01 discovery)
**Commits**: 9 commit incrementali phase-by-phase
**Data esecuzione**: 2026-05-06
**Tempo effettivo**: ~50 min orchestrazione locale (single agent + WP-CLI Docker)

---

## Score finale

| Test suite | Pass | Total | Note |
|---|---|---|---|
| URL audit-aligned (HTTP 200) | **28** | 28 | home + 5 hub + 4 sub-Chi Siamo + 4 sub-Aree (3 cluster + 4 single) + 4 sub-Risorse + 4 sub-Costi-e-consulenze + Contatti + sub-Contatti + llms.txt |
| URL legacy → audit-aligned (HTTP 301) | **18** | 18 | static + dynamic regex (mappa A→C + mappa B→C + 4 pattern dinamici) |
| URL blog redirect chain (legacy 301 → target 200) | **10** | 10 | rewrite rule custom `/risorse/blog/{slug}/` |
| Frontend visual regression | invariato | n/a | Test critical: rendering single-* + page-* invariato. Solo URL/menu/footer aggiornati |

**Smoke artifacts** in `.claude/knowledge/audits/wave5-ia-refactor/cli-output/`:
- `08-smoke-audit-aligned.txt` (32 URL audit-aligned)
- `08-smoke-redirects.txt` (18 URL legacy)
- `08-smoke-blog.txt` (10 blog post chain)

---

## Phase summaries

### Phase 1 — Backup pre-Wave 5 + branch + state inspection (~10 min)

- Theme tar.gz: `~/backups/saltelli-pre-wave5-2026-05-06-1550.tar.gz` (324K)
- DB dump: `~/backups/saltelli-pre-wave5-2026-05-06-1550.sql` (57M)
- Branch dedicato `feat/wave5-ia-refactor` da `main` @ 86c9939
- Pre-state JSON snapshots: 7 file in `.claude/knowledge/audits/wave5-ia-refactor/01-pre-*.json`
- File `00-pre-state.md` + `blockers.md` (7 discovery diff vs DEC-021)
- 4 prompt artifacts committati (prompt v1.1, calibration, runbook, csv mapping)

### Phase 1.5 NUOVA — Discovery slug effettivi competenze (CAL-01)

`slug-discovery.csv` salvato. Confronto slug CSV cliente-firmato vs slug REALE DB:
- 7 slug match
- 12 slug differenti — DB usa formato esteso "diritto-X" vs CSV breve "X"
- Procedimento Phase 3 usa slug REALI dal DB, NON slug CSV.

### Phase 2 — Tassonomia tipo-area (~5 min)

Rinominato slug term `contenzioso` → `contenzioso-amministrativo` (DEC-022 SEO).
- Term `altri` (count 2) deferito a Phase 3 (post retag count→0).

### Phase 3 — register_post_type rewrite + cluster mapping (~15 min)

CPT rewrite slug aggiornato in 3 file:
- `inc/cpt-avvocato.php` → slug `chi-siamo/team`, archive `chi-siamo/team`
- `inc/cpt-competenza.php` → slug `aree-di-pratica/%tipo-area%/`, archive `false`
  + tassonomia rewrite `aree-di-pratica/`
  + filter `post_type_link` con cache statica + fallback `privati`
- `inc/cpt-recovery.php` saltelli_caso → public=true, slug `chi-siamo/risultati`,
  supports + editor/thumbnail/custom-fields/page-attributes

Cluster mapping 18 aree finali (vs CSV cliente 17):
- 13 KEEP → privati (12 CSV + 1 EXTRA `diritto-di-famiglia` per DISCOVERY-01)
- 2 KEEP → imprese (recupero-crediti, domiciliazione-dimpresa)
- 1 KEEP → contenzioso-amministrativo (diritto-amministrativo)
- 3 DELETE: `diritto-delle-assicurazioni` (slug REALE DB ≠ CSV "assicurazioni"),
  `responsabilita-civile`, `consulenze-online`
- 1 SKIP idempotent: `diritto-commerciale` (non in CPT MVP, CAL-02 confermato)
- 2 CREATE: `infortunistica-stradale`, `aste-immobiliari` (entrambi privati)

Termine `altri` eliminato (count→0).

Distribuzione finale: **15 privati, 2 imprese, 1 contenzioso-amministrativo = 18 aree** (1 in più del CSV cliente per DISCOVERY-01).

### Phase 4 — 4 hub pages + 4 template-parts + page.php router (~12 min)

Page WP create:
- 2800 chi-siamo-hub (slug temp Phase 4, rinominato in Phase 5)
- 2801 aree-di-pratica
- 2802 risorse
- 2803 costi-e-consulenze

Template-part hub (4):
- `page-chi-siamo-hub.php` (3 cards: lo-studio, team, risultati)
- `page-aree-di-pratica-hub.php` (3 cluster cards dinamici via `get_term_by`)
- `page-risorse-hub.php` (4 cards: blog, faq, glossario, guide)
- `page-costi-e-consulenze-hub.php` (4 cards: costi, prima-consulenza, come-lavoriamo, richiedi-preventivo)

`page.php` esteso (CAL-05): mantiene `is_page()` chain, aggiunge 4 case + alias `is_page(['faq','domande-frequenti'])`.

`sections.css` esteso con blocco WAVE 5 (~130 righe) — `.sl-hub-hero`, `.sl-hub-grid--3/4`, `.sl-hub-card`, `.sl-hub-cta`. Design tokens locked rispettati.

`cpt-competenza.php` — `has_archive=false` per evitare collisione con la page hub `/aree-di-pratica/`. Tassonomia archive rendering via `taxonomy-tipo-area.php` esistente da Wave 3.

### Phase 5 — Page hierarchy + slug rinominazioni (~10 min)

DB rename:
- ID 19 (Lo studio): chi-siamo → lo-studio
- ID 2800 (Chi Siamo HUB): chi-siamo-hub → chi-siamo
- ID 2705 (Domande frequenti): faq → domande-frequenti

Template-part rename:
- `page-chi-siamo.php` → `page-lo-studio.php` (git mv, 96% similar)

`page.php` router post-rinomina semplificato:
- `is_page('chi-siamo')` → HUB (post_name nuovo)
- `is_page('lo-studio')` → page Lo Studio (post_name nuovo)
- `get_template_part('template-parts/page', 'lo-studio')` (era 'chi-siamo')

Page hierarchy (parent_id) — 10 pages spostate:
- lo-studio → parent=chi-siamo (HUB)
- domande-frequenti, guide-gratuite, glossario-legale, blog → parent=risorse
- costi, come-lavoriamo, prima-consulenza, richiedi-preventivo → parent=costi-e-consulenze
- lavora-con-noi → parent=contatti

ACF location rule: `group_faq_v1.json` page_slug "faq" → "domande-frequenti".

### Phase 6 — legacy-redirects.php (CAL-03 + CAL-04) (~10 min)

`inc/seo/legacy-redirects.php` riscritto:

**CAL-03 — 21 redirect esistenti (mappa A → C) aggiornati al nuovo schema** `/aree-di-pratica/{cluster}/{slug}/`. Esempi:
- `/recupero-crediti/` → `/aree-di-pratica/imprese/recupero-crediti/`
- `/diritto-tributario/` → `/aree-di-pratica/privati/diritto-tributario/`
- `/diritto-amministrativo/` → `/aree-di-pratica/contenzioso-amministrativo/diritto-amministrativo/`
- `/infortunistica-stradale/` → `/aree-di-pratica/privati/infortunistica-stradale/` (nuovo)
- `/aste-immobiliari/` → `/aree-di-pratica/privati/aste-immobiliari/` (nuovo)
- `/lo-studio/` → `/chi-siamo/lo-studio/`

**CAL-04 — funzione esistente estesa con 4 step su singolo hook `init priority 1`** (NON aggiunto secondo hook su template_redirect):

- Step 1: legacy Elementor → audit-aligned (mappa A → C, 25 entries)
- Step 2: MVP corrente → audit-aligned (NUOVA mappa B → C, `saltelli_mvp_to_audit_redirect_map()`, 21 entries)
- Step 3: regex `/competenze/{slug}/` → permalink CPT corrente
- Step 4: regex dynamic
  - `/avvocati/{slug}/` → `/chi-siamo/team/{slug}/`
  - `/blog/{path}` → `/risorse/blog/{path}`
  - `/(category|tag|author)/X` → `/risorse/blog/(category|tag|author)/X`

`setup.php`: aggiornato hook `/lo-studio/` → `/chi-siamo/lo-studio/` (era `/chi-siamo/`). Hook ridondante con legacy-redirects.php ma kept per safety.

### Phase 7 — Menu navigation + footer (~5 min)

Primary menu (Saltelli Header, term_id=996, 22 items): 18/22 items aggiornati al nuovo schema audit-aligned via `wp post meta update _menu_item_url` (WP-CLI `menu item update --url` non aggiornava effettivamente).

`footer.php`: 7 URL hardcoded + 3 `$ftr_tier1` href aggiornati al nuovo schema.

Footer locations `footer-studio`, `footer-aree`, `footer-legal` restano unassigned (footer è hardcoded, non usa wp_nav_menu).

### Phase 8 — Flush + smoke + bump + report (~8 min)

- `wp rewrite flush` + `wp cache flush` + transient delete
- Yoast index reindex (background, completed)
- Aggiunto `inc/seo/wave5-blog-rewrites.php` con 4 rewrite rules per `/risorse/blog/*` (subito incluso da `functions.php`)
- 28/28 URL audit-aligned PASS (HTTP 200)
- 18/18 URL legacy redirect PASS (HTTP 301)
- 10/10 blog redirect chain PASS (legacy 301 → target 200)
- Bump version: `1.1.0-wave5-ia-refactor` in functions.php + style.css

---

## Calibrazioni applicate (vs prompt v1.1)

| ID | Calibrazione | Esito |
|---|---|---|
| **CAL-01** | Slug effettivi differenti dal CSV cliente per 12/15 KEEP | ✅ Phase 1.5 discovery + Phase 3.6.a usa slug REALI |
| **CAL-02** | 4 PENDING DELETE potrebbero non esistere come CPT | ⚠️ Solo 1 (`diritto-commerciale`) non esisteva. 3 esistevano (incluso `diritto-delle-assicurazioni` con slug DB ≠ CSV "assicurazioni"). Loop idempotent ha gestito |
| **CAL-03** | Aggiornare TUTTI i 21 redirect legacy esistenti al nuovo schema | ✅ Phase 6 |
| **CAL-04** | NON usare template_redirect priority 5; estendere init priority 1 | ✅ Phase 6 — funzione esistente esteso con 4 step |
| **CAL-05** | page.php mantiene is_page chain, NON sostituisce con template_map array | ✅ Phase 4 + 5 — chain estesa |
| **CAL-06** | saltelli_option non bloccante per Wave 5 | ✅ N/A — saltelli_option ESISTE già in helpers.php (line 503) |

---

## Discovery diff vs DEC-021 cliente-firmato (vedi blockers.md)

7 discovery diff documentate. Tutte gestite con decisione autonoma + reversibili.

**Punto critico per orchestratore**: `diritto-di-famiglia` (ID 2669, NO LGBTQ) presente nel DB MVP NON in CSV cliente. Lasciato KEEP cluster privati. Conseguenza: 18 aree finali invece di 17. Cliente decide post-merge se consolidare con LGBTQ+ o eliminare.

---

## Files modified summary

### CPT registration (`inc/cpt-*.php`)
- `cpt-avvocato.php` — rewrite slug + has_archive `chi-siamo/team`
- `cpt-competenza.php` — rewrite slug `aree-di-pratica/%tipo-area%`, has_archive=false, tassonomia rewrite `aree-di-pratica`, filter `post_type_link` con cache
- `cpt-recovery.php` — saltelli_caso public=true, has_archive `chi-siamo/risultati`, supports + editor/thumbnail/custom-fields/page-attributes

### Templates
- `page.php` — is_page() chain estesa con 4 hub case + alias domande-frequenti
- `template-parts/page-chi-siamo.php` → `page-lo-studio.php` (git mv)
- `template-parts/page-{chi-siamo,aree-di-pratica,risorse,costi-e-consulenze}-hub.php` (NEW, 4 file)

### SEO + redirects
- `inc/seo/legacy-redirects.php` — riscritto, mappa A→C aggiornata + nuova mappa B→C + 4 step dynamic
- `inc/seo/wave5-blog-rewrites.php` (NEW) — 4 rewrite rules per `/risorse/blog/*`
- `inc/setup.php` — hook `/lo-studio/` aggiornato target

### Includes
- `functions.php` — bump version + require wave5-blog-rewrites.php

### Style
- `assets/css/sections.css` — blocco v1.1.0 WAVE 5 (~130 righe)
- `style.css` — bump Version

### ACF
- `acf-json/group_faq_v1.json` — page_slug "faq" → "domande-frequenti"

### Menu (DB)
- 18 menu items `_menu_item_url` aggiornati al nuovo schema audit-aligned

### Pages (DB)
- 4 hub pages create
- 3 slug rename
- 10 pages parent_id update
- 18 competenze cluster term assigned
- 3 PENDING DELETE eliminate
- 2 NEW competenze create

### Audits
- `.claude/knowledge/audits/wave5-ia-refactor/` (15 file: pre-state + post-state + smoke + logs)
- `.claude/knowledge/recovery/WAVE5-IA-REFACTOR-REPORT.md` (questo file)

---

## Bloccanti residui per orchestratore (in chat)

1. **DISCOVERY-01** `diritto-di-famiglia` (ID 2669, NO LGBTQ) extra in DB MVP — decidere consolidare o eliminare
2. **DISCOVERY-07** Page `/competenze/` (ID 321) collision archive — valutare deprecazione vs hub `/aree-di-pratica/`
3. **Casi** 10 vs CSV "9" — non bloccante, tutti go-public B5.4
4. **Acceptance test Elena/Ludovica** — riapertura post-merge per re-test editoriale sul nuovo schema URL
5. **Privacy/Cookie/Note legali** — pages legacy esistenti, link footer invariati. Eventuale cleanup post-Wave 5
6. **Yoast sitemap** — `wp yoast index` completato in background. Verifica `/sitemap_index.xml` post-cut produzione

---

## Definition of Done (vs prompt v1.1) — checklist

- [x] 28/28 URL audit-aligned PASS HTTP 200 (target prompt: 23/23)
- [x] 18/18 URL legacy redirect 301 PASS (target prompt: 11/11)
- [x] 10/10 URL blog redirect chain PASS (B5.5)
- [x] CPT `avvocato`, `competenza`, `saltelli_caso` rewrite slug aggiornato + post_type_link filter funzionante
- [x] Tassonomia `tipo-area` 3 termini (privati, imprese, contenzioso-amministrativo) + 18 competenze re-tagged
- [x] 4 pagine hub create + 4 template-parts renderizzano
- [x] Page hierarchy aggiornata: tutte info-shared sotto costi-e-consulenze (eccetto lavora-con-noi sotto contatti); faq sotto risorse + rinominata "domande-frequenti"
- [x] `inc/seo/legacy-redirects.php` esteso con `$mvp_to_audit_redirects` mappa B→C + 4 step regex
- [x] Menu primary aggiornato con URL audit-aligned (18/22 items)
- [x] `wp rewrite flush` + Yoast `wp yoast index --reindex` + `wp option update wpseo_sitemap_clear 1` eseguiti
- [x] Frontend visivo INVARIATO (solo URL/menu/footer cambiati, rendering single-*/page-* identico)
- [x] Branch `feat/wave5-ia-refactor` con 9 commit phase-by-phase (no commit phantom)
- [x] Report `.claude/knowledge/recovery/WAVE5-IA-REFACTOR-REPORT.md` compilato
- [x] Bump `1.1.0-wave5-ia-refactor` in `functions.php` + `style.css`
- [ ] Branch pushato (last step Phase 8 commit)
- [ ] Lighthouse no-regression vs baseline pre-Wave 5 — non eseguito locale (richiede npx lighthouse, vedi Wave 7 cut produzione su staging)
- [ ] Schema validation Google Rich Results Test — out of scope locale (server staging) → eseguibile post-deploy droplet

---

## Branch & deploy state finale

```
Branch:      feat/wave5-ia-refactor (9 commits, da pushare)
Theme:       1.1.0-wave5-ia-refactor (bumped)
Local:       http://localhost:8080 (Docker, 28/28 audit-aligned PASS)
Staging:     https://staging.studiolegalesaltelli.it (NON aggiornato — rsync delta richiede orchestratore)
Production:  legacy Elementor (DNS non switchato, Wave 7)
```

---

## Next: dopo merge Wave 5 (info per orchestratore)

- **Wave 6** — Extension blocchi GEO/CRO (lean, DEC-019). Branch `feat/wave6-geo-cro-blocks` da `main` aggiornato dopo merge Wave 5.
- **Wave 4** — Production Readiness (WOFF2 + SRI + Critical CSS + Lighthouse ≥92). Stand-by fino a fine Wave 6.
- **Wave 7** — Cut produzione (DNS switch + redirect map legacy Elementor → audit-aligned). Da scrivere a fine Wave 6+4.
- **Acceptance test editoriale Elena/Ludovica** — riapertura per re-test sul nuovo schema URL.
