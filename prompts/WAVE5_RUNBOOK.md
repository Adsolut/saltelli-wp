# 🚀 WAVE 5 RUNBOOK — IA Refactor (cheat sheet operativo)

> **Tienilo aperto durante l'esecuzione.** Tutti i dettagli sono nel prompt v1.1 → questo è il riassunto sintetico per orientarti rapidamente tra le 8 phases.

---

## 🎯 Identità + setup

| Voce | Valore |
|---|---|
| **Branch** | `feat/wave5-ia-refactor` (da main aggiornato) |
| **Prompt input** | `prompts/PROMPT_AGENT_v1.1_WAVE5_IA_REFACTOR.md` |
| **Theme version finale** | `1.1.0-wave5-ia-refactor` |
| **Deliverable cliente** | `01-discovery/cluster-mapping-17-areas.csv` (DEC-021) |
| **Stato pre-flight** | ✅ Tutti i bloccanti CHIUSI |
| **Tempo stimato totale** | 4-6 ore + smoke test + report |
| **HARD RULE** | one-writer-at-a-time: orchestratore (chat) ferma, NO commit paralleli |

---

## 📋 8 Phases — overview tabellare

| # | Phase | Scope | Tempo | Output chiave |
|---|---|---|---|---|
| 1 | Backup + branch + baseline | snapshot theme + DB dump locale + branch + smoke pre-Wave5 | 30 min | `pre-wave5-smoke.txt` (21/21 PASS atteso), branch `feat/wave5-ia-refactor` |
| 2 | Tassonomia tipo-area | aggiungere 3° termine `contenzioso-amministrativo` | 10 min | `wp term list tipo-area` mostra 3 termini |
| 3 | CPT rewrite + cluster mapping | 5 modifiche register_post_type + filter post_type_link + WP-CLI mapping 17 aree (15 KEEP + 4 DELETE + 2 CREATE) | 60-75 min | 17 competenze totali con cluster term assegnato, distribuzione 14/2/1 |
| 4 | Pagine madre + template-parts | 4 pagine WP create + 4 template-parts hub | 40 min | `/chi-siamo/`, `/aree-di-pratica/`, `/risorse/`, `/costi-e-consulenze/` rendering OK |
| 5 | Page hierarchy moves | sposta 14 pagine top-level sotto sezioni hub (parent_id update) | 30 min | tutti gli URL annidati, slug rinominati (es. `/faq/` → `/risorse/domande-frequenti/`) |
| 6 | legacy-redirects.php | mappa unificata 3 stati (legacy → MVP / MVP → audit / legacy → audit) + redirect 4 DELETE | 30 min | 11 redirect statici + 4 dynamic regex pattern, tutti 301 |
| 7 | Smoke test + Lighthouse | 21 URL post-Wave5 + 11 URL legacy redirect + 10 random blog posts + Lighthouse no-regression | 30 min | `post-wave5-smoke.txt` 21/21 PASS, redirect 11/11 PASS, Lighthouse mobile ≥ baseline |
| 8 | Bump version + report + push | functions.php version bump + WAVE5-IA-REFACTOR-REPORT.md + 6 commits + push | 30 min | branch pushed, NO merge |

---

## ⌨️ Comandi chiave per phase

### Phase 1
```bash
tar -czf /tmp/saltelli-pre-wave5-$(date +%F-%H%M).tar.gz wp-content/themes/saltelli/
wp db export /tmp/saltelli-pre-wave5-db.sql
git fetch origin main && git checkout main && git pull --ff-only && git checkout -b feat/wave5-ia-refactor
```

### Phase 2 — Tassonomia (NUOVO 3° termine)
```bash
wp term create tipo-area "Contenzioso amministrativo" --slug=contenzioso-amministrativo
wp term list tipo-area --format=table  # atteso 3 termini
```

### Phase 3.6 — Cluster mapping 17 aree (script pre-compilato, copia da prompt v1.1)
```bash
# 12 → privati (loop sul prompt)
# 2 → imprese (recupero-crediti, domiciliazione-impresa)
# 1 → contenzioso-amministrativo (diritto-amministrativo)
# 4 DELETE: assicurazioni, responsabilita-civile, consulenze-online, diritto-commerciale
# 2 CREATE: infortunistica-stradale, aste-immobiliari (entrambi cluster privati)
wp rewrite flush
```

### Phase 4 — 4 pagine madre WP
```bash
wp post create --post_type=page --post_title="Chi Siamo" --post_name=chi-siamo-hub --post_status=publish --porcelain
wp post create --post_type=page --post_title="Aree di Pratica" --post_name=aree-di-pratica --post_status=publish --porcelain
wp post create --post_type=page --post_title="Risorse" --post_name=risorse --post_status=publish --porcelain
wp post create --post_type=page --post_title="Costi e Consulenze" --post_name=costi-e-consulenze --post_status=publish --porcelain
```

### Phase 6 — Yoast sitemap + smoke test
```bash
wp yoast index --network --reindex
wp transient delete --all && wp cache flush
curl -s "https://staging.studiolegalesaltelli.it/sitemap_index.xml" | xmllint --noout -
```

### Phase 7 — Lighthouse no-regression
```bash
npx lighthouse https://staging.studiolegalesaltelli.it/ --emulated-form-factor=mobile \
    --output=html --output-path=.claude/knowledge/audits/wave5/lh-mobile-home.html --quiet
```

### Phase 8 — Push branch
```bash
git push origin feat/wave5-ia-refactor
# NO merge automatico su main, orchestratore audisce
```

---

## ✅ Acceptance criteria (10 check critici)

- [ ] Backup pre-Wave5 + DB dump presenti, leggibili
- [ ] Tassonomia `tipo-area` ha 3 termini (privati / imprese / contenzioso-amministrativo)
- [ ] 17 competenze totali in DB (15 KEEP + 2 CREATE; 4 DELETE confermati assenti)
- [ ] Distribuzione cluster: 14 privati, 2 imprese, 1 contenzioso-amministrativo
- [ ] CPT `saltelli_caso` è `public => true`, archive `/chi-siamo/risultati/`, 9 casi visibili
- [ ] 4 pagine madre hub create (chi-siamo / aree-di-pratica / risorse / costi-e-consulenze)
- [ ] Smoke 21 URL post-Wave5: tutti HTTP 200
- [ ] Smoke 11 redirect MVP-legacy: tutti HTTP 301 con redirect_url corretto
- [ ] Smoke 10 random blog posts (`/blog/{slug}/` → `/risorse/blog/{slug}/`): tutti `legacy=301 target=200`
- [ ] Lighthouse mobile no-regression: Performance ≥ baseline, SEO ≥ 95
- [ ] Theme version bumpata a `1.1.0-wave5-ia-refactor`
- [ ] Report `.claude/knowledge/recovery/WAVE5-IA-REFACTOR-REPORT.md` completo
- [ ] Branch pushed, NO merge su main

---

## 🛑 Stop conditions (quando fermarsi e annotare)

| Condizione | Azione |
|---|---|
| Smoke test pre-Wave5 ha < 21/21 PASS | STOP. Investiga MVP corrente prima di iniziare. |
| Phase 3 cluster mapping fallisce su 1+ aree (post non trovato) | Annota in `wave5/blockers.md`, prosegui con le altre, segnalo a fine wave |
| Phase 4 pagina hub fa clash con slug esistente (`chi-siamo` clash con `Lo Studio`) | Usa Opzione B del prompt: rinomina lo slug di "Lo Studio" da `chi-siamo` a `lo-studio` (con 301 redirect) |
| Phase 6 Yoast sitemap non parsa correttamente | Re-run `wp yoast index --network --reindex` + flush, escalation se persiste |
| Phase 7 Lighthouse REGRESSION (Performance scende >5%) | STOP. Probabilmente è il filter `post_type_link` che fa query addizionale. Aggiungi static cache (vedi prompt Phase 7 errore) |
| Acceptance test Elena/Ludovica trova bug critico durante wave | Annotalo in `wave5/elena-bugs.md`, NON committare immediatamente. L'orchestratore decide post-merge. |
| Discrepanza tra mappatura cliente-firmata (DEC-021) e DB MVP | STOP. Verifica con orchestratore prima di forzare. |

---

## 📤 Output expected (a fine Wave 5)

1. **Branch pushed**: `origin/feat/wave5-ia-refactor` con 6 commit (uno per phase)
2. **Report**: `.claude/knowledge/recovery/WAVE5-IA-REFACTOR-REPORT.md`
3. **Smoke artifacts** in `.claude/knowledge/audits/wave5/`:
   - `pre-wave5-smoke.txt` (baseline)
   - `post-wave5-smoke.txt` (21 URL audit-aligned)
   - `post-wave5-redirects.txt` (11 URL legacy con 301)
   - `post-wave5-blog-sample.txt` (10 random blog posts redirect chain)
   - `lh-mobile-home.html` + `lh-mobile-tributario.html`
   - `cluster-mapping-applied.txt` (output Phase 3.6.d, 17 righe)
4. **Theme version** in `functions.php`: `1.1.0-wave5-ia-refactor`
5. **Decision Log MVP** (in repo `.claude/knowledge/`): annotazione di completamento Wave 5

---

## 🔗 Riferimenti rapidi

- `CLAUDE.md` — single source of truth (HARD constraints, design system locked, workflow rules)
- `docs/ARCHITECTURE.md` § 2-5 — mappa theme + ACF
- `docs/EDITOR-HANDOFF.md` § 3-5 — pattern editing
- `_archive/prompts-completed/recovery-v1.0/PROMPT_AGENT_v1.0_WAVE3_TEMPLATE_REFACTOR.md` — pattern di lavoro Wave 3 di riferimento
- `prompts/PROMPT_AGENT_v1.1_WAVE5_IA_REFACTOR.md` — il prompt corrente (~1100 righe)
- DEC-013 stack mantenuto · DEC-014 sitemap firmata · DEC-021 mappatura 17 aree · DEC-022 slug brevi

---

## 💡 Pattern di lavoro raccomandato

1. **Apri 2 split terminale**: uno per Claude Code (sessione interattiva), uno per WP-CLI manuale (debug + verifiche puntuali)
2. **Apri questo runbook in second monitor** + il prompt v1.1 in editor
3. **Procedi phase-by-phase**: ogni commit = un Phase completato + smoke parziale
4. **Tieni `wp post list` + `wp term list` come query "stato corrente"** ogni volta che hai un dubbio
5. **Se uno step richiede >2x il tempo stimato**: pausa, valuta se sei fuori scope, eventualmente escalation
6. **Smoke test 21 URL spesso, non solo a fine**: se un URL si rompe Phase 4 lo cogli subito invece che a Phase 7
7. **Commit message pattern**: `wave5: phase N — {scope}` (segue convention MVP)
