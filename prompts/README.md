# prompts/ — Prompt ATTIVI per Claude Code

Cartella che contiene **solo** i prompt non ancora eseguiti, pronti per essere dati a una nuova sessione di Claude Code.

## Pattern di lavoro consolidato

Il progetto Saltelli usa il seguente pattern di orchestrazione:

```
┌─ Orchestratore (Claude in chat su Claude.ai)
│   └─ scrive il prompt qui in prompts/
│
├─ Claude Code (sessione dedicata su terminale)
│   └─ legge CLAUDE.md + il prompt → esegue → ritorna branch + report
│
└─ Orchestratore (chat)
    └─ audita branch + report → merge no-ff → archive prompt completato
```

## Stato attuale

| Prompt | Status | Ultima modifica |
|---|---|---|
| `_BACKLOG_PROMPT_AGENT_DESIGN_HANDOFF_P11_CONTATTI.md` | ⏸ backlog post-cut (page contatti Elena-approved, 2 fix CSS minor + select aree dinamico = nice-to-have non blocker) | 2026-05-12 |

**Convenzione prefix**: `_BACKLOG_` davanti al filename indica prompt non eseguito ma in pausa per decisione orchestrator (es. scope deferred a fase successiva).

**Cleanup 2026-05-12** (`chore/post-batch3-housekeeping`):
- Spostati 18 prompt completati a `_archive/prompts-completed/recovery-v1.0/` (Wave 4.5/4.6/4.7/4.8/5 + visual debug audit + working artifacts).
- Spostati 5 file obsoleti a `_archive/prompts-completed/_obsolete/` (`PROMPT_AGENT_v1.0_WAVE4_PRODUCTION_READINESS.md` superato da Wave 4.5+; `PROMPT_AGENT_v1.1_WAVE6_GEO_CRO_BLOCKS.md` + 2 file Wave 6 mai eseguito, design pivoted to Wave 5 STEP 1-4; `EDITOR-HANDOFF.md` v1 legacy 27 KB superseded by `docs/EDITOR-HANDOFF.md` v6.0 98 KB).
- Rinominato `PROMPT_AGENT_DESIGN_HANDOFF_P11_CONTATTI.md` → `_BACKLOG_` prefix per chiarezza.

## Come usarli

1. Apri una **nuova sessione di Claude Code** nella root del progetto.
2. Dai come istruzione iniziale: *"Leggi e segui `/prompts/{nome-prompt}.md`."*
3. Claude Code legge prima `CLAUDE.md` (single source of truth + hard rules), poi il prompt assegnato, e procede.
4. Al termine, Claude Code:
   - Crea un branch dedicato (es. `feat/wave4-production-readiness`)
   - Pusha N commit Phase 1-N + fix
   - Lascia `.claude/knowledge/audits/{nome}/` con report + tickets
5. **Tu (orchestratore)** auditi in chat, mergei no-ff, e archivi il prompt completato in `_archive/prompts-completed/{categoria}/`.

## Convenzioni

- **Naming**: `PROMPT_AGENT_v{X.Y}_{NOME_FASE}.md` (es. `v1.0_WAVE4`).
- **Scope**: 1 prompt = 1 wave o 1 fase chiusa. NON mescolare scope.
- **Hard rules**: ogni prompt elenca esplicitamente cosa è fuori scope (no-fly zones).
- **Definition of Done**: ogni prompt ha una checklist DoD nelle ultime 20-30 righe.
- **Branch dedicato**: ogni esecuzione apre un branch nuovo da `main` aggiornato.

## Quando un prompt è completato

Una volta auditato e mergeato in `main`, il prompt va in:

```
_archive/prompts-completed/
├── orchestration-original/    ← prompt iniziali del progetto
├── pre-recovery-v0.x/         ← prompt fasi v0.* (sessioni 1+2 design)
├── recovery-v0.9/             ← recovery preliminare
├── recovery-v1.0/             ← Wave 0+1+2+3 + Debug QA + Wave 4.5/4.6/4.7/4.8/5 + Design Handoff P1-P12 (escluso P11 ancora backlog)
├── _obsolete/                 ← prompt mai eseguiti / superseded (Wave 4 production-readiness v1.0, Wave 6 GEO/CRO, EDITOR-HANDOFF v1 legacy)
└── deploy/                    ← runbook deploy
```

Il merge commit conserva i 7+ commit del branch dedicato (no squash). Così `git log --follow` continua a funzionare.

---

*Maintained by orchestrator (Claude in chat). Last updated: 2026-05-12 — chore/post-batch3-housekeeping cleanup.*
