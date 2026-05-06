# 📘 WAVE 6 — RUNBOOK operativo

> **Audience**: Duccio (orchestrator umano) — istruzioni passo-passo per lanciare Wave 6 in autonomia.
> **Scope**: Extension blocchi GEO/CRO (pattern adaptation lean, DEC-019).
> **Tempo stimato**: ~6h Claude Code (4 phases lavoro denso + 2 phases lighter).
> **Branch target**: `feat/wave6-geo-cro-blocks` da `main` aggiornato (post-Wave5 merge).
> **Theme version target**: `1.2.0-wave6-geo-cro-blocks` (bump Wave 6).

---

## 0. Pre-flight check (5 min)

Prima di lanciare Wave 6, conferma che:

```bash
cd ~/Desktop/DEV/saltelli-wp/

# 1. Sei su main pulita post-Wave5
git checkout main
git pull --ff-only origin main
git status   # → "nothing to commit, working tree clean"

# 2. Tag Wave 5 presente
git tag --list | grep wave5
# → "v1.1.0-wave5-ia-refactor" presente

# 3. Theme version corrente confermata
grep "Version:" wp-content/themes/saltelli/style.css
# → "Version: 1.1.0-wave5-ia-refactor"

# 4. Branch zombie eliminati (cleanup post-Wave5)
git branch -a
# → atteso: solo main + remotes/origin/main + remotes/origin/HEAD

# 5. Docker WP funzionante
docker-compose ps   # → wp + db + redis "Up"
docker-compose exec -T wp wp post list --post_type=competenza --post_status=publish --format=count
# → 17 (allineata DEC-021)
```

Se uno qualunque di questi check fallisce, **NON proseguire**. Apri ticket con orchestratore.

---

## 1. Setup file Wave 6 in repo (~2 min)

I 4 file Wave 6 vivono nella repo deliverable `saltelli-refactor` ma vanno copiati nella repo codice `saltelli-wp/prompts/` perché Claude Code li legga.

```bash
chmod +x ~/Downloads/wave6-setup.sh && ~/Downloads/wave6-setup.sh
```

Lo script copia:
- `prompts/PROMPT_AGENT_v1.1_WAVE6_GEO_CRO_BLOCKS.md` (552 righe — il prompt principale)
- `prompts/WAVE6_CALIBRATION_NOTES.md` (251 righe — 7 calibrazioni preventive)
- `prompts/WAVE6_RUNBOOK.md` (questo file, copia per Claude Code reference)
- `prompts/cluster-mapping-17-areas.csv` (riferimento canonico cluster, già presente da Wave 5)
- `prompts/migration-matrix-v3.csv` (slug REALI con redirect chain status)
- `prompts/pattern-adaptation-map.md` (10 pattern mappati 1:1, INPUT PRINCIPALE Wave 6)

Verifica copia avvenuta:

```bash
ls -la prompts/PROMPT_AGENT_v1.1_WAVE6_GEO_CRO_BLOCKS.md \
       prompts/WAVE6_CALIBRATION_NOTES.md \
       prompts/pattern-adaptation-map.md \
       prompts/migration-matrix-v3.csv
```

---

## 2. Lancio Claude Code in NUOVA sessione (~6h totali)

**Importante**: NUOVA sessione, non riusare la sessione cleanup di Wave 5. Il context è diverso (Wave 6 è un'altra wave indipendente, non un fix isolato).

```bash
cd ~/Desktop/DEV/saltelli-wp
claude
```

**Primo messaggio** da incollare in Claude Code:

```
Wave 6 launch — Extension blocchi GEO/CRO (pattern adaptation lean, DEC-019).

Branch parent: main (post-Wave5 mergeata, tag v1.1.0-wave5-ia-refactor)
Branch nuovo: feat/wave6-geo-cro-blocks

Letture obbligatorie nell'ordine:
1. CLAUDE.md
2. prompts/WAVE6_CALIBRATION_NOTES.md (LEGGI PRIMA del prompt — 7 calibrazioni preventive)
3. prompts/PROMPT_AGENT_v1.1_WAVE6_GEO_CRO_BLOCKS.md (prompt principale, 552 righe)
4. prompts/pattern-adaptation-map.md (INPUT PRINCIPALE — 10 pattern mappati 1:1)
5. prompts/migration-matrix-v3.csv (slug REALI delle 17 competenze, riferimento canonico)

Esegui Phase 1 → Phase 7 come da prompt v1.1. NO Phase 0 (saltelli_option esiste già).
Acceptance gate: 17 acceptance criteria (vedi prompt § "Acceptance criteria Wave 6").

NO commit su main. NO merge. Push branch feat/wave6-geo-cro-blocks + report.
Theme version target: 1.2.0-wave6-geo-cro-blocks.

Procedi.
```

Claude Code dovrebbe:
1. Leggere CLAUDE.md, WAVE6_CALIBRATION_NOTES, e poi il prompt v1.1
2. Annunciare il plan (Phase 1 → 7)
3. Chiederti conferma prima di iniziare (oppure partire direttamente — dipende dal suo stile)
4. Eseguire phase-by-phase con commit incrementali sul branch

Tempo per phase (stima da prompt v1.1):
- Phase 1: backup + branch + ACF Field Groups extension (60 min)
- Phase 2: 4 nuovi template-part (90 min)
- Phase 3: Estensione 6+ template files (90 min)
- Phase 4: Nuovo `cro.css` bundle (60 min)
- Phase 5: Aggiornamento `inc/enqueue.php` (10 min)
- Phase 6: Smoke + Lighthouse (30 min)
- Phase 7: Audit + commit + push (30 min)

---

## 3. Cosa fare durante l'esecuzione Wave 6

### Cose normali (NO intervento)
- Claude Code ti chiede conferme su decisioni di design ambigue → rispondi
- Bug minori scoperti durante esecuzione → annotati in `blockers.md` per discussione post-merge
- Polish CSS, copy, micro-interactions → variazioni accettabili rispetto al prompt

### Cose anomale (Stop + ping orchestratore)
- ❌ Phase fallisce con errore PHP fatale o frontend rotto → STOP, ripristina da backup, ping
- ❌ Smoke test post-phase fa regression su URL Wave 5 → STOP, ping
- ❌ Lighthouse droppa sotto 80 mobile → annotare ma NON STOP (Wave 4 lo fixerà)
- ❌ Decisione cliente richiesta (es. testimonial copy, trust signal numerici) → annotare in `blockers.md`, continuare con placeholder, ping post-Wave6

### Cose da accettare (anche se diverse dal prompt)
- Estensione del prompt scope (es. micro-fix CRO non previsti ma utili) → OK se Claude Code documenta
- Nuovi file fuori scope iniziale (es. `inc/cro/` directory) → OK se giustificato in commit message

---

## 4. Audit orchestratore post-Wave 6 push (~15 min)

Quando Claude Code completa Wave 6 e pusha il branch, **NON mergeare ancora**. Prima ping orchestratore:

```
Wave 6 done — branch feat/wave6-geo-cro-blocks pushato.
Path report: .claude/knowledge/recovery/WAVE6-GEO-CRO-BLOCKS-REPORT.md
```

L'orchestratore (Claude in chat):
1. Fetcha il branch dal container
2. Audit end-to-end (analogo Wave 5)
3. Verifica 17 acceptance criteria + 7 calibrazioni applicate + smoke artifacts
4. Decisione GO / NO-GO per merge

Se GO → ti do i comandi merge `feat/wave6-geo-cro-blocks → main` (no-ff) + tag `v1.2.0-wave6-geo-cro-blocks`.

---

## 5. Post-merge actions orchestratore (~30 min, in autonomia mio lavoro)

Dopo merge Wave 6:
1. DEC-025 popolata (sintesi Wave 6)
2. `mvp-state-snapshot.md` v3 (`1.2.0-wave6-geo-cro-blocks`)
3. WAVE7_CALIBRATION_NOTES.md scratch (per cut produzione)
4. Quality Checklist Fase 1+2 sign-off update con Wave 6 closure
5. Lancio Wave 4 (Production Readiness) ready quando vuoi

---

## 🚨 Bandiere rosse Wave 6 — escalation immediate

| Bandiera rossa | Action |
|---|---|
| Branch zombie su main durante phase | STOP, fix subito (HARD RULE one-writer-at-a-time, DEC-016) |
| Fatal error PHP frontend qualsiasi page | STOP, restore backup, ping |
| Schema FAQPage validation fallisce su 1+ page | STOP fino a fix (Pattern 5 generalization) |
| Regression smoke Wave 5 (32 URL audit-aligned, 33 blog redirect) | STOP, debug isolato |
| Decisione cliente urgente richiesta (testimonial content, contact info, trust signal numerici) | NON STOP, placeholder + annota in blockers, continua |
| Lighthouse mobile < 60 (drop violento) | NON STOP, annota — Wave 4 lo fissa |
| Design drift dal pattern-adaptation-map | Ping orchestratore (Wave 6 è LEAN, no creatività) |

---

## 🎯 Definition of done Wave 6

Wave 6 si considera **completata** quando:
- [ ] 7 phases eseguite, 7+ commit phase-by-phase
- [ ] 17 acceptance criteria PASS (vedi `PROMPT_AGENT_v1.1` § acceptance)
- [ ] Smoke Wave 5 NO regression (32 audit-aligned + 33 blog + 18 redirect legacy)
- [ ] Schema FAQPage generalizzata + coabitazione Yoast OK
- [ ] Theme version `1.2.0-wave6-geo-cro-blocks` in `functions.php` + `style.css`
- [ ] Branch `feat/wave6-geo-cro-blocks` pushato (NO merge)
- [ ] Report `.claude/knowledge/recovery/WAVE6-GEO-CRO-BLOCKS-REPORT.md` compilato
- [ ] Audit trail in `.claude/knowledge/audits/wave6-geo-cro-blocks/`

---

## 🔗 Riferimenti incrociati

- `prompts/PROMPT_AGENT_v1.1_WAVE6_GEO_CRO_BLOCKS.md` — prompt principale
- `prompts/WAVE6_CALIBRATION_NOTES.md` — 7 calibrazioni preventive
- `prompts/pattern-adaptation-map.md` — 10 pattern adaptation
- `prompts/migration-matrix-v3.csv` — slug REALI 17 aree
- `CLAUDE.md` — single source of truth
- DEC-019 (Wave 6 lean), DEC-024 (Wave 5 closure)
