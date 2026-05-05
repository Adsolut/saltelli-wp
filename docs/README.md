# docs/ — Documentazione operativa

Documentazione **viva** del progetto Saltelli WP. Tutto qui è riferimento corrente, non storia.

## Cosa c'è qui

| File | Scope | Audience |
|---|---|---|
| [`BRIEF.md`](./BRIEF.md) | Brief originale del progetto: cliente, team, 19 aree, problemi sito attuale, architettura sito nuovo, AI-readiness | Tecnici + business |
| [`PRODUCT.md`](./PRODUCT.md) | Brand identity, voice, anti-references, principi strategici, persona utenti | Tecnici + editor + agency |
| [`DESIGN.md`](./DESIGN.md) | Design tokens (colori, typography, spacing), heuristics visuali | Designer + frontend dev |
| [`ARCHITECTURE.md`](./ARCHITECTURE.md) | Mappa theme files + ACF schema + WP-Admin↔frontend coupling | Sviluppatori + Claude Code |
| [`DEPLOY.md`](./DEPLOY.md) | Runbook deploy droplet (rsync, WP-CLI, nginx) + lessons learned | DevOps + agency |
| [`EDITOR-HANDOFF.md`](./EDITOR-HANDOFF.md) | Manuale editoriale italiano per Elena/Ludovica/esterni | Editor (NON tecnici) |

## Convenzioni di mantenimento

- Ogni file ha uno **scope chiaro** e **NON cresce** se non per ragioni operative.
- Aggiornamenti vanno **datati** in fondo al file ("*Last updated: YYYY-MM-DD*").
- Se un documento diventa obsoleto, **NON cancellare**: spostare in `_archive/docs-deprecated/{YYYY-MM-DD}/`.
- Il `EDITOR-HANDOFF.md` ha versioning esplicito (v1.0, v1.1, ecc.) perché viene distribuito al cliente.

## Cosa NON va qui

- Prompt per agent (Claude Code): vanno in `/prompts/`
- Bug ticket / audit report: vanno in `.claude/knowledge/audits/`
- Release notes wave: vanno in `.claude/knowledge/recovery/`
- Storia design pre-recovery: vedi `.claude/knowledge/_history/design/`

---

*Maintained by orchestrator (Claude in chat). Last updated: 2026-05-05.*
