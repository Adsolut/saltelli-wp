# CLAUDE.md — Studio Legale Saltelli WordPress Theme

> **Single source of truth for Claude Code agents working on this project.**
> Read this FIRST. Then read only the prompt assigned to you (in `prompts/`).
> Project context machine-readable: [`.claude/knowledge/project-context.json`](./.claude/knowledge/project-context.json).
>
> **Repo layout** (post housekeeping 2026-05-05):
> - `/docs/` — documentazione operativa viva (BRIEF, PRODUCT, DESIGN, ARCHITECTURE, DEPLOY, EDITOR-HANDOFF)
> - `/prompts/` — prompt ATTIVI per Claude Code (uno alla volta)
> - `/_archive/prompts-completed/` — prompt completati (storia, 5 sub-cartelle cronologiche)
> - `/.claude/knowledge/` — working knowledge (recovery, audits, reference, _history)

## Identity

Building a deliberately differentiated, AI-ready, performance-obsessed custom WordPress theme for **Studio Legale Emiliano Saltelli & Partners** — a premium law firm in Naples (Chiaia). The vendor is **Adsolut SRLS**, an AI Agency. The theme should make the existing Naples legal market look dated.

**Strategy:** "Legal Luxury Minimal" — boutique editoriale italiano, tipografia dominante, palette navy/crema/bronzo. Tier-1 deep clusters: Tributario · Lavoro · Famiglia LGBTQ+. The other 16 practice areas get tier-2 lighter pages.

## Current state — v1.3.6-wave4-7-fix-scf-migration

**Last updated:** 2026-05-08 (Wave 4.7.fix mergeata: SCF migration + Theme Options activation)
**Branch:** `main` · feature `fix/wave4-7-fix-scf-migration` ⏳ in audit
**Demo:** ✅ presentata al cliente · feedback iteration assorbita
**Live staging:** https://staging.studiolegalesaltelli.it allineato a `1.3.6-wave4-7-fix-scf-migration` · ACF→SCF switched, 50/50 options popolati
**Active phase:** Acceptance test editoriale (Elena/Ludovica) — ora con menu **Saltelli — Settings** funzionale (slot 60)
**Next:** Onboarding Elena 30 min · valutare se Wave 4.9 Gutenberg migration ancora in scope · cut produzione

**Infra staging (consolidata 2026-04-30):**
- Droplet DO `saltelli-staging-ams3-01` · IPv4 `178.62.207.50` · ams3 · s-1vcpu-2gb · Ubuntu 24.04 LTS
- DNS `staging.studiolegalesaltelli.it` → propagato · HTTPS Let's Encrypt notAfter `2026-07-29` (auto-renew certbot.timer)
- Stack: nginx 1.24 + PHP 8.2.30 + MySQL 8.0.45 + WP-CLI · DB `saltelli_wp` popolato
- WP installato in `/var/www/saltelli` · theme su `wp-content/themes/saltelli/` (rsync da locale, NON git clone)
- Hardening: UFW (22/80/443), fail2ban, SSH no-root no-password, swap 2GB, unattended-upgrades
- Secrets locali: `.saltelli-staging-secrets` (gitignored) · droplet: `/home/deploy/.saltelli-secrets`
- Runbook deploy: `PROMPT_AGENT_G_DEPLOY_DIGITALOCEAN.md` (Fasi 0-2, 5-6 originarie completate; 3-4 fatte ad-hoc fuori runbook; 7-8 ancora aperte)
- Pending: reboot droplet per kernel 6.8.0-110

**WP admin access (locale + staging, password allineate 2026-05-04):**
- UID 1 `Emiliano Saltelli` (info@studiolegalesaltelli.it) → `WP_EMILIANO_PWD` in `.saltelli-staging-secrets`
- UID 8 `Adsolut Staff` (tech@adsolut.it) → `WP_ADSOLUT_PWD` in `.saltelli-staging-secrets`
- Reset via `wp_set_password()` locale e `wp user update` su droplet (phpass nativo, no MD5 fallback)
- Stesso password locale↔staging per comodità — ruotare se serve isolamento env

### What's done

| Phase | Version | Status |
|---|---|---|
| Scaffolding → Multi-agent → Polish → Impeccable | 0.1.0 → 0.4.0 | ✅ |
| Content Migration + Audit Alignment + Pain Points | 0.5.0 → 0.7.0 | ✅ |
| Template Polish + Mobile Fix | 0.8.0 | ✅ (storico) |
| Pre-presentation polish (homepage, hero, eyebrow, atelier, ToV "tu") | 0.16.0 → 0.16.3 | ✅ |
| Logo system v1.1 + sitemap audit + favicon fix + home fix | 0.17.0 → 0.17.1 | ✅ |
| Contatti rework + rhythm + sede no-iframe + pills + wp_site_icon unhook | 0.17.2 → 0.17.3 | ✅ |
| Version consolidation + numbering policy | 0.17.4-beta-consolidation | ✅ |
| Wave 0 — Foundation CMS (ACF Free + 8 CPT fake repeater) | 1.0.0-recovery-wave0 | ✅ |
| Wave 1 — 16/16 ACF Field Groups (Agent A + B + C consolidato) | 1.0.0-recovery-wave1 | ✅ |
| Wave 2 — Content Migration (273 fields + 63 CPT items) | 1.0.0-recovery-wave2 | ✅ |
| **Wave 3 — Template Refactor (page.php 1274→79 + 6 template-parts + ACF reads)** | **1.0.0-recovery-wave3** | **✅** |
| EDITOR-HANDOFF v1.0 (manuale editoriale Elena/Ludovica/esterni) | docs · `60cea61` | ✅ |
| EDITOR-HANDOFF v1.1 (workflow estesi + nota bio_estesa + fase debug) | docs | ✅ |
| **Debug & QA — stress test pre-production (4 bugs, 1 P0 architectural fix)** | **1.0.0-recovery-wave3-debug** | **✅** |
| Wave 4–4.7.1 (font WOFF2, critical CSS, CMS editability, ACF default_value hotfix) | 1.3.0–1.3.4 | ✅ |
| Wave 4.8 — Cleanup + Migrations + UX Polish FINAL | 1.3.5-wave4-8-cleanup-final | ✅ |
| **Wave 4.7.fix — SCF Migration + Theme Options Activation (50/50 fields seedabili popolati, write-side pipeline funzionale)** | **1.3.6-wave4-7-fix-scf-migration** | **✅** |
| Acceptance test editoriale (Elena/Ludovica console+CF7+cross-browser+copy) | parallel | 🔍 ACTIVE |
| Cut produzione (DNS switch staging→prod) | 1.0.0 | ⏸ |

### 0.17.x — consolidation log (4 collisioni di numbering risolte)

Fra `Codencore` (istanza esterna) e `me`/Aldo c'è stato un parallel work che ha generato 8 commit con numerazione duplicata `0.17.0 / .1 / .2 / .3` (2 commit ciascuno, file disgiunti, nessun conflict tecnico). Storia git lasciata immutabile, version interna `SALTELLI_THEME_VERSION` riflette sempre l'ultimo arrivato.

| SHA | Author | Tag interno | Cosa porta |
|---|---|---|---|
| `e63d989` | me | v0.17.0 | Logo system v1.1 (header/footer + favicon SVG monogramma) |
| `0426aa3` | Codencore | v0.17.0 | Sitemap audit: 3 nuove competenze + 8 page top-level + menu rebuild gerarchico |
| `8a7b36b` | me | v0.17.1 | Favicon fix (SVG corrotti 7B → ricostruiti dal brief) |
| `5fbca6e` | Codencore | v0.17.1 | Home fix: areas list opacity stuck + hero white-space + tassonomia 3 nuove |
| `ccb0ed8` | me | v0.17.2 | /contatti/ rework: form sopra contatti classici + rename + aria submit |
| `e02a254` | Codencore | v0.17.2 | Hero white-space cleanup + section rhythm armonia 80/80 |
| `2e9189f` | me | v0.17.3 | Sede text-only (iframe rimosso) + wp_site_icon legacy unhooked |
| `7df2bb3` | Codencore | v0.17.3 | Tag pills text centered + padding symmetric |

### Versioning policy (da v0.17.4 in poi)

Per evitare future collisioni quando più agent committano in parallelo:

1. **Prima di scegliere la version**, controllare l'ultimo `SALTELLI_THEME_VERSION` su `origin/main`:
   ```sh
   git fetch origin main && git show origin/main:wp-content/themes/saltelli/functions.php | grep SALTELLI_THEME_VERSION
   ```
2. **Bump monotonic**: se sull'origin c'è `0.X.Y`, il nuovo commit usa `0.X.(Y+1)` — mai lo stesso `Y`.
3. **Suffix sempre presente** (`-beta-<topic>`) per leggibilità human nel `git log`.
4. **Se push fallisce** per non-fast-forward, `git pull --rebase`, ribumpa e ripusha — non risolvere a mano i conflitti su `style.css` / `functions.php` mantenendo la propria version.

### What's where

**Source of truth files (always read first):**
- `CLAUDE.md` (this file)
- `docs/BRIEF.md` — original brief
- `docs/PRODUCT.md` — brand voice + anti-references
- `docs/DESIGN.md` — design tokens
- `docs/ARCHITECTURE.md` — theme + ACF schema mapping (chiave per WYSIWYG gaps)
- `.claude/knowledge/project-context.json` — machine-readable context
- `.claude/knowledge/_history/design/sessione-1/homepage-desktop.jsx` — JSX reference for Frame 1 (storia)

**Agent prompts** (in `prompts/` se attivo, in `_archive/prompts-completed/{categoria}/` se completato):
- `recovery-v1.0/PROMPT_AGENT_v1.0_WAVE0_FOUNDATION.md` — ✅ done
- `recovery-v1.0/PROMPT_AGENT_v1.0_WAVE1_FIELD_GROUPS.md` + `_RECOVERY.md` — ✅ done (Agent A+B+C consolidato)
- `recovery-v1.0/PROMPT_AGENT_v1.0_WAVE2_CONTENT_MIGRATION.md` — ✅ done
- `recovery-v1.0/PROMPT_AGENT_v1.0_WAVE3_TEMPLATE_REFACTOR.md` — ✅ done
- `recovery-v1.0/PROMPT_AGENT_v1.0_DEBUG_QA.md` — ✅ done (4 bugs found, 3 closed + 1 deferred, P0 architectural fix `page_slug ==`)
- **`prompts/PROMPT_AGENT_v1.0_WAVE4_PRODUCTION_READINESS.md`** — ⏸ ready to launch (5 phases, branch dedicato `feat/wave4-production-readiness`)
- `deploy/PROMPT_AGENT_G_DEPLOY_DIGITALOCEAN.md` — runbook deploy archiviato (Fase 0+infra completata, deploy delta via rsync ad-hoc, Fasi 7-8 ancora aperte; sostituito de facto da `docs/DEPLOY.md`)
- `_archive/prompts-completed/orchestration-original/` — prompt iniziali sessione 1
- `_archive/prompts-completed/pre-recovery-v0.x/` — 17 prompt iterazioni v0.* (sessione 1+2 design)
- `_archive/prompts-completed/recovery-v0.9/` — recovery preliminare

**Operational docs** (`/docs/`):
- `docs/BRIEF.md` — brief originale del progetto (cliente, team, 19 aree, AI-readiness)
- `docs/PRODUCT.md` — brand identity, voice, anti-references, principi strategici
- `docs/DESIGN.md` — design tokens (colori, typography, spacing)
- `docs/ARCHITECTURE.md` — mappa theme + ACF schema + WP-Admin↔frontend coupling + WYSIWYG gaps
- `docs/DEPLOY.md` — runbook deploy droplet + lessons learned
- `docs/EDITOR-HANDOFF.md` v1.1 — manuale editoriale italiano per Elena/Ludovica/esterni

**Working knowledge** (`.claude/knowledge/`):
- `recovery/` — release notes wave (5 file, vivi)
- `audits/debug-qa/` — 4 bug ticket + 1 report + audit raw (vivi)
- `reference/{security,wordpress,database}/` — reference docs (10 file, dormi-vita)
- `_history/design/` — storia 2 sessioni design pre-recovery (26 file post-cleanup `0ee9789`, informativo non operativo — vedi `_history/README.md`. I 90 report v0.x dettagliati rimossi sono consultabili in git history)

## Hard constraints (non-negotiable)

| Rule | Reason |
|---|---|
| **No page builder** (no Elementor, Bricks, Divi, WPBakery) | Removes JS bloat, full markup control for schema injection |
| **Pure PHP template hierarchy** | Standard WP, predictable, auditable |
| **Custom Post Types** for `avvocato` and `competenza` | Scales schema markup automatically |
| **GSAP 3.12+ + Lenis only** for animations | NO AOS, WOW.js, ScrollMagic, Locomotive |
| **Schema JSON-LD inline in templates** | NOT plugin-generated, full control |
| **Single H1 per page**, ever | Audit found duplicate H1s on the source site. Don't repeat |
| **Mobile-first**, every breakpoint | 60%+ traffic is mobile, AI Overviews trigger 81% from mobile |
| **No `#000000` black**, no aggressive red, no purple/magenta | Purple/magenta is Adsolut brand, not Saltelli's |
| **Design tokens locked** in `tokens.css` — never modify them | The whole system depends on stability |
| **Yoast coabitation respected** — never emit Organization/Article/Breadcrumb if Yoast active | No schema duplicates |
| **Foto Emiliano `_thumbnail_id=2683`** + **bio_estesa avvocati** preserved across all runs | Step D content + Step C.5 photo integration |
| **One-writer-at-a-time** — una sola sessione (chat orchestratore O Claude Code) committa su `main` o branch attivi alla volta | Vedi pattern dettagliato in "Workflow rules" sotto |

## Workflow rules (orchestratore ↔ Claude Code)

### Pattern di lavoro consolidato

Il progetto Saltelli usa un pattern a due ruoli con responsabilità disgiunte:

```
┌─ Orchestratore (Claude in chat, su Claude.ai)
│   └─ ruolo: pianifica, scrive prompt, audita, mergea
│   └─ commits: docs/*, prompts/*, .claude/knowledge/*, CLAUDE.md, README.md
│
└─ Claude Code (sessione dedicata su terminale)
    └─ ruolo: esegue prompt assegnato, lavora in branch dedicato
    └─ commits: wp-content/themes/saltelli/*, scripts/*, .claude/knowledge/audits/{nome}/*
```

### One-writer-at-a-time (HARD RULE)

**Solo una delle due sessioni è attiva sui commit alla volta.**

- Quando l'orchestratore lancia un task per Claude Code (es. Wave 4), l'orchestratore **NON committa nulla** sulla repo finché Claude Code non ha pushato il branch dedicato e l'audit è stato completato.
- Quando l'orchestratore sta lavorando su qualcosa (es. test plan, manuale, refactor doc), Claude Code **è fermo**.
- **Niente committi paralleli sulla stessa repo.**

### Cosa fare se vedi qualcosa di urgente durante l'attesa

Se come orchestratore vedi un fix necessario mentre Claude Code sta lavorando: **annotalo, non committarlo**. Lo discutiamo in chat dopo l'audit del branch di Claude Code. La probabilità che Claude Code abbia già fixato la stessa cosa è alta (è successo già: vedi commit `0ee9789` + `b6bfbf9` del 2026-05-05).

### Mitigazione collisioni se accadono comunque

- File disgiunti modificati da entrambe le sessioni: `git pull --rebase` risolve da solo, push.
- Stesso file modificato da entrambe le sessioni: la sessione che pusha per seconda fa `git pull --rebase`, riconcilia a mano, ripusha. **Non riscrivere mai la storia git pubblica.**
- Stessa modifica fatta da entrambe (improbabile ma capitato): la sessione che pusha per seconda nota il duplicato e abbandona il proprio commit (`git reset --soft HEAD~`).

### Branch policy

- **`main`** — niente commit diretti per cambi al codice tema. Solo merge no-ff da branch dedicati.
- **`feat/{nome}`** — ogni wave/task in branch separato (es. `feat/debug-qa`, `feat/wave4-production-readiness`).
- **`chore/{nome}`** — housekeeping, refactor doc, cleanup (es. `chore/repo-housekeeping`).
- **Documentazione minore** (typo CLAUDE.md, fix link rotto in README) — ammesso commit diretto su `main`, ma sempre quando l'altra sessione è ferma.

### Identità git

Dal 2026-05-05 entrambe le sessioni committano sotto `AdsolutAdv <aldo.santoro@adsolut.it>` (config locale repo). La storia precedente firmata `Codencore <git@adhost.it>` resta immutabile.

## Design system (locked)

```
COLOR
  --background:   #FAFAF8   (cream)
  --surface:      #F2F0EA   (cream darker)
  --primary:      #1B2B4B   (navy)
  --accent:       #B8860B   (bronze, parsimonious use)
  --text:         #2D2D2D   (NOT pure black)
  --text-muted:   #6B6B6B
  --border:       #E5E0D5

TYPOGRAPHY
  --font-display: "Playfair Display" 400/700
  --font-body:    "DM Sans" 400/500/700
  --font-mono:    "JetBrains Mono" 400 (for metadata: dates, tags, eyebrows)

BREAKPOINTS: 375 / 768 / 1024 / 1440 (mobile-first)
```

## Information architecture (current)

```
/                                   Homepage (front-page.php)
/lo-studio/                         About (page.php)
/avvocati/                          Team archive (archive-avvocato.php)
/avvocati/{slug}/                   CPT avvocato (single-avvocato.php) × 4
/competenze/                        Practice areas archive (archive-competenza.php)
/competenze/{slug}/                 CPT competenza × 19 — branched tier-1/tier-2 (single-competenza.php)
/tipo-area/{privati,imprese,contenzioso,altri}/   Taxonomy archive (currently fallback archive.php)
/casi/                              Cases (page or archive — TBD)
/costi/                             Costs page (page.php) — added at Audit Alignment
/blog/                              Blog archive
/{slug}/                            Single post (single.php) × 326 historical posts
/contatti/                          Contact (page.php)
/llms.txt                           Static AI crawler file (served dynamically)
```

## Tech stack

```
WordPress 6.x          (current Docker local)
PHP 8.2+
SCF 6.8.4              (Secure Custom Fields, Automattic fork — Wave 4.7.fix)
ACF Free 6.8.0         (INACTIVE on staging — kept for fast rollback only)
Yoast SEO              (active — coabitation enforced)
Custom theme path:     wp-content/themes/saltelli/
Animation libs:        GSAP 3.12.5 + ScrollTrigger from CDN (deferred)
                       Lenis 1.1.13 (currently disabled by Polish Agent — re-evaluate Step F)
                       SplitText: NOT used (Polish Agent animated <span>s directly)
```

### Custom fields plugin — SCF (Wave 4.7.fix, 2026-05-08)

**Plugin attivo:** Secure Custom Fields 6.8.4 — fork Automattic di ACF (Q4 2024)
**Plugin precedente:** Advanced Custom Fields Free 6.8.0 (deactivated, NOT removed)

**Motivo switch:** ACF Free non include `acf_add_options_page()` (feature ACF Pro-only).
CMS Diagnosis Round 2 (REPORT.md 2026-05-08) ha identificato bug architetturale: la
Theme Options page non si registrava mai (silent no-op del `function_exists()` guard
in `inc/acf-fields.php:30`) → Elena/Ludovica non potevano modificare 50 field globali.

**API compat:** drop-in compatible. `get_field`, `update_field`, `acf_add_options_page`,
`acf_get_field_groups`, `acf_get_options_pages`, location rules custom (es. `page_slug ==`
del Debug-QA bug-04 fix), JSON auto-load da `acf-json/`, tutti funzionanti.

**Stato post-switch:** `function_exists(acf_add_options_page)=YES`, `defined(ACF_PRO)=YES`,
17 field group preserved, options page `saltelli-settings` registrata + visibile in admin
slot 60. 50 chiavi `options_*` popolate (26 baseline Wave 4.6 + 24 seeded da
`inc/seed-theme-options.php`).

**Rollback emergency** (1-shot, già testato su staging Phase 1):
```sh
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp plugin deactivate secure-custom-fields --path=/var/www/saltelli && \
  sudo -u www-data wp plugin activate advanced-custom-fields --path=/var/www/saltelli && \
  sudo -u www-data wp cache flush --path=/var/www/saltelli"
```

**Backup pre-switch su droplet:** `~/backups/wave4-7-fix-pre-switch-20260508-1220/`
(db.sql 59MB · theme.tar.gz 352KB · plugins-acf.tar.gz 6.2MB).

## Convention summary for agents

**Naming:**
- All custom CSS classes use `.sl-*` prefix
- Sections in homepage: `.sl-hero`, `.sl-areas`, `.sl-studio`, `.sl-team`, `.sl-cases`, `.sl-press`, `.sl-contact`, `.sl-footer`
- BEM-like: `.sl-area__title`, `.sl-area--tier1` (modifier), `.sl-team__lawyer`, ecc.

**Files structure:**
- `assets/css/tokens.css` — variables only, never edit
- `assets/css/base.css` — reset, container, typography setup
- `assets/css/components.css` — buttons, links, accordion, area-list, attorney sticky
- `assets/css/sections.css` — section layouts + page-specific (`.sl-hero`, `.sl-costi__section`, ecc.)
- `assets/js/main.js` — entrypoint, GSAP+ScrollTrigger init, hover bindings
- `inc/schema/` — 5 PHP partials for JSON-LD (organization, attorney, faqpage, breadcrumb, article)
- `inc/seo/` — meta-tags + ai-files (llms.txt + robots filter)
- `inc/cpt-*.php` — CPT registration
- `inc/acf-json/` — field group definitions (works with or without ACF Pro)

## Design → Code handoff rule (golden)

**JSX inline styles handoff:** quando Claude Design genera JSX con `style={{...}}` inline:

1. **Code DEVE mappare ogni inline style a una className BEM** (`.sl-{template}__{element}`)
2. **Code DEVE generare CSS rule corrispondente in `sections.css`** con scope marker `/* === v0.X.0 TEMPLATE === */`
3. **Code NON deve assumere che className mancanti = optional** — tutti gli inline style del JSX devono diventare CSS rule con className
4. **Test pixel-perfect:** 1 inline style = 1 CSS rule + 1 className
5. **Verifica computed styles via Playwright** post-deploy: ogni `gridTemplateColumns`, `gap`, `fontSize`, `padding` JSX → match in `getComputedStyle(el)`

Lezione v0.19→v0.30: l'agent Code aveva tradotto solo 20% dei JSX inline (quelli con className), saltando l'80% inline-only. v0.27.x recovery ha mappato sistematicamente i gap. **Pattern obbligatorio per future sessioni.**

## Working rules for agents

1. **Read this file first.** Then read your assigned prompt + project-context.json. Then read ONLY the files relevant to your scope.
2. **Cache flush + curl test after EVERY non-trivial change.** Don't batch 5 changes and verify at end — catch regression early.
3. **Idempotency:** re-running your script must not duplicate menu/page/term entries.
4. **Touch only what's in your scope.** If you find a bug outside scope, document it in your report — don't fix it.
5. **Decisions autonomously taken must be reported.** Better verbose than mute.
6. **Never write to:** `_thumbnail_id` on CPT avvocato, `bio_estesa` on existing avvocati, `tokens.css` design variables, `config.local.json` (gitignored — credentials).
7. **Never disable plugins** during a run.

## Tone of communication when reporting back to Duccio

Direct. Concrete. Zero filler. He values: precise diagnosis, ranked options with rationale, explicit blockers, no apology padding. Mirror this tone in commit messages and status updates.

## What NOT to do

- Don't invent client data. Real data is in `project-context.json`.
- Don't reuse Adsolut brand colors (magenta, purple, galaxy theme).
- Don't ship "AI slop" — generic legal stock imagery, lorem ipsum, placeholder unsplash photos of handshakes.
- Don't optimize desktop "wow" at the cost of mobile LCP.
- Don't add new dependencies (libraries, plugins, fonts) without explicit instruction.
- Don't refactor template hierarchy without explicit instruction.
- Don't sentence-case Italian sigle (INPS, IRPEF, IMU, RC, LGBTQ+) or proper nouns (Napoli, Cassazione, Federico) when normalizing case.

## When in doubt

Re-read this file. If still in doubt, ask Duccio. Don't guess on:
- Client-facing copy
- Attorney specialization details
- Pricing or commercial terms
- Anything that would appear in schema markup as fact

---
*Last updated: 2026-05-08 · v1.3.6-wave4-7-fix-scf-migration · SCF migration + Theme Options activation · 50/50 options popolati · pipeline write-side Elena finalmente funzionale*
*Maintained by orchestrator (Claude in chat) after each milestone.*
