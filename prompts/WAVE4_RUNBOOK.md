# 📘 WAVE 4 — RUNBOOK operativo

> **Audience**: Duccio (orchestrator umano) — istruzioni passo-passo per lanciare Wave 4 in autonomia.
> **Scope**: Production Readiness (WOFF2 + Critical CSS + JS optimization + Image optimization + Security headers + SRI).
> **Tempo stimato**: ~5h Claude Code (7 phases).
> **Branch target**: `feat/wave4-production-readiness` da `main` aggiornato (post-Wave6 merge).
> **Theme version target**: `1.3.0-wave4-production-readiness`.

---

## 0. Pre-flight check (5 min)

Prima di lanciare Wave 4, conferma che:

```bash
cd ~/Desktop/DEV/saltelli-wp/

# 1. Sei su main pulita post-Wave6
git checkout main
git pull --ff-only origin main
git status   # → "nothing to commit, working tree clean"

# 2. Tag Wave 5 + 6 presenti
git tag --list | grep -E "wave[56]"
# → v1.1.0-wave5-ia-refactor + v1.2.0-wave6-geo-cro-blocks

# 3. Theme version corrente
grep "Version:" wp-content/themes/saltelli/style.css
# → "Version: 1.2.0-wave6-geo-cro-blocks"

# 4. Branch zombie eliminati post-Wave 5/6
git branch -a
# → solo main + remotes/origin/main + remotes/origin/HEAD

# 5. Docker WP funzionante
docker-compose ps   # → wp + db + redis "Up"

# 6. Node.js + npx disponibili (per Lighthouse + penthouse/critters)
node --version    # → 18+ raccomandato
npx --version
```

Se uno qualunque di questi check fallisce, **NON proseguire**. Apri ticket con orchestratore.

---

## 1. Setup file Wave 4 in repo (~2 min)

```bash
chmod +x ~/Downloads/wave4-setup.sh && ~/Downloads/wave4-setup.sh
```

Lo script copia:
- `prompts/PROMPT_AGENT_v1.0_WAVE4_PRODUCTION_READINESS.md` (~600 righe — il prompt principale)
- `prompts/WAVE4_CALIBRATION_NOTES.md` (8 calibrazioni preventive specifiche)
- `prompts/WAVE4_RUNBOOK.md` (questo file, copia per Claude Code reference)

Verifica copia avvenuta:

```bash
ls -la prompts/PROMPT_AGENT_v1.0_WAVE4_PRODUCTION_READINESS.md \
       prompts/WAVE4_CALIBRATION_NOTES.md
```

---

## 2. Lancio Claude Code in NUOVA sessione (~5h)

**Importante**: NUOVA sessione, non riusare la sessione Wave 6.

```bash
cd ~/Desktop/DEV/saltelli-wp
claude
```

**Primo messaggio** da incollare in Claude Code:

```
Wave 4 launch — Production Readiness (WOFF2 + Critical CSS + JS opt + Lighthouse ≥ 92).

Branch parent: main (post-Wave6 mergeata, tag v1.2.0-wave6-geo-cro-blocks)
Branch nuovo: feat/wave4-production-readiness

Letture obbligatorie nell'ordine:
1. CLAUDE.md
2. prompts/WAVE4_CALIBRATION_NOTES.md (LEGGI PRIMA del prompt — 8 calibrazioni preventive)
3. prompts/PROMPT_AGENT_v1.0_WAVE4_PRODUCTION_READINESS.md (prompt principale)

Esegui Phase 1 → Phase 7 come da prompt v1.0.
Acceptance: Lighthouse ≥ 92 mobile/desktop su 6 URL + NO regression Wave 5/6 smoke.

NO commit su main. NO merge. Push branch feat/wave4-production-readiness + report.
Theme version target: 1.3.0-wave4-production-readiness.

Procedi.
```

Tempo per phase (stima):
- Phase 1: backup + branch + Lighthouse baseline pre (30 min)
- Phase 2: WOFF2 self-host (60 min)
- Phase 3: Critical CSS extraction + inline (90 min)
- Phase 4: JS optimization (defer + async + cleanup) (30 min)
- Phase 5: Image optimization (lazy + width/height + preload hero) (45 min)
- Phase 6: Security headers + SRI (30 min)
- Phase 7: Smoke + Lighthouse post + bump version + report (30 min)

---

## 3. Cosa fare durante l'esecuzione Wave 4

### Cose normali (NO intervento)
- Claude Code chiede conferme su decisioni di scope (es. Lenis re-enable) → rispondi NO (default mantieni disabled)
- Bug minori scoperti → annotati per discussione post-merge
- Polish CSS, micro-interactions → variazioni accettabili

### Cose anomale (Stop + ping orchestratore)
- ❌ Lighthouse drop sotto baseline pre-Wave 4 → STOP, identifica regression, fix mirato
- ❌ Regression smoke Wave 5 (32 URL audit-aligned) o Wave 6 (21 URL + render checks) → STOP
- ❌ Frontend rotto su 1+ template → STOP, ripristina backup, ping
- ❌ Font WOFF2 404 (path errato o MIME type Docker) → annotare, fix locale, ping se persiste

### Cose da accettare
- Critical CSS estratto > 14KB inline → riduce parzialmente il benefit, ma comunque migliora vs no-extraction
- Lighthouse 92-94 invece di 95+ → accettabile, target è ≥ 92
- Differenze Lighthouse Docker locale vs PageSpeed Insights produzione → normali, validare entrambi

---

## 4. Audit orchestratore post-Wave 4 push (~15 min)

Quando Claude Code completa Wave 4 e pusha il branch, **NON mergeare ancora**. Prima ping orchestratore:

```
Wave 4 done — branch feat/wave4-production-readiness pushato.
Path report: .claude/knowledge/recovery/WAVE4-PRODUCTION-READINESS-REPORT.md
```

L'orchestratore (Claude in chat):
1. Fetcha il branch dal container
2. Audit end-to-end (analogo Wave 5/6)
3. Verifica Lighthouse delta + smoke regression
4. Verdict GO / NO-GO per merge

Se GO → ti do i comandi merge `feat/wave4-production-readiness → main` (no-ff) + tag `v1.3.0-wave4-production-readiness`.

---

## 5. Post-merge actions orchestratore (~30 min, in autonomia mio lavoro)

Dopo merge Wave 4:
1. DEC-026 popolata (sintesi Wave 4 + Lighthouse delta)
2. `mvp-state-snapshot.md` v4 (`1.3.0-wave4-production-readiness`)
3. WAVE7_CALIBRATION_NOTES.md preparatorio (per cut produzione DNS switch)
4. Quality Checklist Fase 1+2 update con Wave 4 closure
5. Lancio Wave 7 (Cut produzione) ready quando vuoi

---

## 🚨 Bandiere rosse Wave 4 — escalation immediate

| Bandiera rossa | Action |
|---|---|
| Branch zombie su main durante phase | STOP, fix subito (HARD RULE one-writer-at-a-time) |
| Fatal error PHP frontend qualsiasi page | STOP, restore backup, ping |
| WOFF2 404 / MIME error / FOUT visible | Verifica path + MIME nginx Docker, fix locale |
| Critical CSS rompe layout | Rollback Phase 3, valuta strategia alternativa |
| Lighthouse < 85 su 1+ URL post-fix | STOP, identifica top contributor, fix mirato |
| Regression smoke Wave 5 o Wave 6 | STOP immediato, debug isolato |
| GSAP / animation rotti dopo defer | Rimuovi defer da gsap nel filter |

---

## 🎯 Definition of done Wave 4

Wave 4 si considera **completata** quando:
- [ ] 7 phases eseguite, 7+ commit phase-by-phase
- [ ] Lighthouse ≥ 92 mobile + desktop su 6 URL campione
- [ ] NO regression smoke Wave 5 (32 audit-aligned + 18 redirect + 33 blog)
- [ ] NO regression smoke Wave 6 (21 URL + render checks)
- [ ] WOFF2 self-host attivo (NO Google Fonts CDN)
- [ ] Critical CSS inline + main CSS async
- [ ] JS defer/async configurato
- [ ] Security headers + XML-RPC disabled
- [ ] Theme version `1.3.0-wave4-production-readiness`
- [ ] Branch `feat/wave4-production-readiness` pushato (NO merge)
- [ ] Report `.claude/knowledge/recovery/WAVE4-PRODUCTION-READINESS-REPORT.md`

---

## 🔗 Riferimenti incrociati

- `prompts/PROMPT_AGENT_v1.0_WAVE4_PRODUCTION_READINESS.md` — prompt principale
- `prompts/WAVE4_CALIBRATION_NOTES.md` — 8 calibrazioni preventive
- `CLAUDE.md` — single source of truth
- DEC-018 (drift accettato), DEC-020 (pipeline)
