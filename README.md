# Studio Legale Saltelli — WordPress Custom Theme

> Sito WordPress custom AI-ready per **Studio Legale Emiliano Saltelli & Partners** (Napoli, Chiaia).
> Costruito da [Adsolut SRLS](https://adsolut.it) come parte del programma GEO (Generative Engine Optimization).
> Tema **boutique editoriale** "Legal Luxury Minimal" — navy/cream/bronze, Playfair Display + DM Sans, schema JSON-LD inline, llms.txt.

---

## Stato del progetto

**Branch corrente:** `main`
**Theme version live:** `1.0.0-recovery-wave3-debug`
**Staging:** [staging.studiolegalesaltelli.it](https://staging.studiolegalesaltelli.it)
**Production:** non ancora cut. DNS `studiolegalesaltelli.it` punta al vecchio sito.
**Phase:** Acceptance test editoriale + Wave 4 (Production Readiness) ready to launch.

Per il quadro tecnico completo vedi [`CLAUDE.md`](./CLAUDE.md) — single source of truth del progetto.

---

## Mappa della repo

```
saltelli-wp/
├── README.md                          ← sei qui
├── CLAUDE.md                          ← stato progetto, hard rules, roadmap
│
├── docs/                              ← documentazione operativa (vivi)
│   ├── BRIEF.md                       ← brief originale del progetto
│   ├── PRODUCT.md                     ← brand identity, voice, anti-references
│   ├── DESIGN.md                      ← design tokens (colori, type, spacing)
│   ├── ARCHITECTURE.md                ← mappa theme + ACF schema + WP-Admin↔frontend
│   ├── DEPLOY.md                      ← runbook deploy droplet + lessons learned
│   └── EDITOR-HANDOFF.md              ← manuale editoriale per Elena/Ludovica
│
├── prompts/                           ← prompt ATTIVI per Claude Code
│   └── PROMPT_AGENT_v1.0_WAVE4_PRODUCTION_READINESS.md
│
├── wp-content/themes/saltelli/        ← il theme custom
│
├── scripts/                           ← script PHP (migration, audit, fix)
├── geo-assets/                        ← schema JSON-LD reference + llms.txt
├── ux-research/                       ← research UX di riferimento
├── bin/, db-dump/, saltelli-dump/     ← utilities + dump DB locali
│
├── .claude/                           ← Claude Code config + working knowledge
│   ├── PROMPT_LEAD_AGENT.md
│   ├── knowledge/
│   │   ├── recovery/                  ← release notes wave 0-3 (vivi)
│   │   ├── audits/debug-qa/           ← bug ticket + report (vivi)
│   │   ├── reference/                 ← reference WP, security, DB
│   │   └── _history/                  ← STORIA design pre-recovery (informativo)
│   └── skills/impeccable/             ← design skill kit (Claude Code)
│
├── _archive/                          ← prompt completati (storia, zero impatto)
│   └── prompts-completed/
│       ├── orchestration-original/    ← prompt iniziali (sessione 1 setup)
│       ├── pre-recovery-v0.x/         ← prompt fasi v0.* (pre-recovery)
│       ├── recovery-v0.9/             ← recovery preliminare
│       ├── recovery-v1.0/             ← Wave 0+1+2+3 + Debug QA
│       └── deploy/                    ← runbook deploy DigitalOcean
│
├── docker-compose.yml                 ← WP locale Docker
├── config.local.json                  ← config locale
└── .gitignore
```

---

## Quick start

### Setup locale

```bash
# WordPress locale via Docker
docker-compose up -d

# WP-Admin: http://localhost:8080/wp-admin/
```

### Deploy su staging

Vedi [`docs/DEPLOY.md`](./docs/DEPLOY.md) per il runbook completo.

Sintesi:
```bash
# rsync delta del theme su droplet
ssh deploy@178.62.207.50
cd /var/www/saltelli/wp-content/themes/saltelli/
# pull + cache flush
```

---

## Stack tecnico

- **CMS:** WordPress 6.x
- **Theme:** Custom PHP (no page builder, mai)
- **Plugin core:** ACF 6.8 · Yoast SEO 27.5 · Contact Form 7 6.1 · Honeypot 2.3
- **Stack server:** nginx 1.24 · PHP 8.2 · MySQL 8.0
- **Hosting staging:** DigitalOcean droplet `178.62.207.50` (Ubuntu 24.04)
- **CDN font:** futuro WOFF2 self-host (Wave 4)

---

## Per chi entra nel progetto

**Sviluppatore tecnico:** parti da [`CLAUDE.md`](./CLAUDE.md), poi leggi [`docs/ARCHITECTURE.md`](./docs/ARCHITECTURE.md) per la mappa theme + ACF.

**Editor (Elena, Ludovica, esterni):** parti da [`docs/EDITOR-HANDOFF.md`](./docs/EDITOR-HANDOFF.md) — manuale completo in italiano per editare il sito senza toccare codice.

**Claude Code agent:** leggi `CLAUDE.md` (hard rules + state) + il prompt assegnato in `prompts/`. Tutto il resto è informativo.

**Project manager / orchestratore:** [`CLAUDE.md`](./CLAUDE.md) ha la roadmap. [`docs/DEPLOY.md`](./docs/DEPLOY.md) per runbook deploy. [`.claude/knowledge/audits/`](./.claude/knowledge/audits/) per i report degli audit fatti.

---

## Maintainer

**Vendor agency:** Adsolut SRLS · [adsolut.it](https://adsolut.it)
**Tech contact:** tech@adsolut.it
**Project lead:** Aldo Santoro (Duccio)
**Cliente:** Studio Legale Emiliano Saltelli & Partners

---

*README maintained by orchestrator (Claude in chat). Last updated: 2026-05-05.*
