# _archive/ — Materiale completato (zero impatto operativo)

Tutto in questa cartella è **storia**. Mantenuto per:
- Audit trail (capire come si è arrivati allo stato attuale)
- Recupero di pattern utili in futuro (es. struttura di un prompt che ha funzionato bene)
- Compliance / documentazione cliente

**Niente qui dentro è da eseguire o consultare per il lavoro corrente.** Per il lavoro vivo vai a `/docs/` (operativi) o `/prompts/` (prompt attivi).

## Struttura

### `prompts-completed/`

Prompt usati nelle varie fasi del progetto, organizzati cronologicamente:

| Sub-cartella | Contenuto | Periodo |
|---|---|---|
| [`orchestration-original/`](./prompts-completed/orchestration-original/) | Prompt iniziali del progetto: orchestrator, style/animation agent, theme architect, GEO engineer, audit alignment, content migration, template polish, pain points, scaffolding | Aprile 2026 (sessione 1 setup) |
| [`pre-recovery-v0.x/`](./prompts-completed/pre-recovery-v0.x/) | Prompt delle iterazioni v0.* — editorial refinement, IA unification, layout harmonization, pixel-perfect, dropcap+FAQ, info archive recovery, foundation layer, multi-agent pre-human-test, ship plan 24h | Aprile-Maggio 2026 (sessione 1+2 design) |
| [`recovery-v0.9/`](./prompts-completed/recovery-v0.9/) | Recovery preliminare prima di v1.0: prompt agent recovery + email cliente | Aprile 2026 |
| [`recovery-v1.0/`](./prompts-completed/recovery-v1.0/) | **Recovery v1.0 (CMS migration completa)**: piano emergenza, Wave 0 Foundation, Wave 1 Field Groups (+ recovery), Wave 2 Content Migration, Wave 3 Template Refactor, Debug & QA | Maggio 2026 |
| [`deploy/`](./prompts-completed/deploy/) | Runbook deploy DigitalOcean droplet | Aprile 2026 (parzialmente eseguito; rsync ad-hoc per delta successivi) |

### Cosa NON va qui

- Audit / bug report → `.claude/knowledge/audits/`
- Release notes wave → `.claude/knowledge/recovery/`
- Storia decisioni design → `.claude/knowledge/_history/design/`
- Documentazione operativa viva → `/docs/`
- Prompt ATTIVI non ancora eseguiti → `/prompts/`

## Quando ripescare qualcosa da qui

Caso d'uso tipico: stai per scrivere un nuovo prompt per Claude Code, e vuoi vedere come sono stati strutturati i prompt che hanno funzionato bene. Apri `prompts-completed/recovery-v1.0/PROMPT_AGENT_v1.0_WAVE3_TEMPLATE_REFACTOR.md` come pattern template.

Oppure: il cliente ti chiede "perché abbiamo fatto X", e tu cerchi nei prompt completati la motivazione contestuale dell'epoca.

---

*Maintained by orchestrator (Claude in chat). Last updated: 2026-05-05.*
