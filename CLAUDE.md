# CLAUDE.md — Studio Legale Saltelli WordPress Theme

> **Single source of truth for Claude Code agents working on this project.**
> Read this FIRST. Then read only the prompt assigned to you (`PROMPT_AGENT_*.md`).
> Project context machine-readable: [`.claude/knowledge/project-context.json`](./.claude/knowledge/project-context.json).

## Identity

Building a deliberately differentiated, AI-ready, performance-obsessed custom WordPress theme for **Studio Legale Emiliano Saltelli & Partners** — a premium law firm in Naples (Chiaia). The vendor is **Adsolut SRLS**, an AI Agency. The theme should make the existing Naples legal market look dated.

**Strategy:** "Legal Luxury Minimal" — boutique editoriale italiano, tipografia dominante, palette navy/crema/bronzo. Tier-1 deep clusters: Tributario · Lavoro · Famiglia LGBTQ+. The other 16 practice areas get tier-2 lighter pages.

## Current state — v0.17.4-beta-consolidation

**Last updated:** 2026-04-30 (post-demo iteration)
**Branch:** `main`
**Demo:** ✅ presentata al cliente · in fase di feedback iteration
**Live staging:** https://staging.studiolegalesaltelli.it allineato a v0.17.4 (Fasi 3+4 deploy completate de facto via rsync ad-hoc, runbook G non eseguito formalmente)
**Active phase:** Demo feedback iteration (post-presentation polish)
**Next:** Step F (Production Readiness — WOFF2, SRI, Lighthouse ≥92) → Cut produzione

**Infra staging (consolidata 2026-04-30):**
- Droplet DO `saltelli-staging-ams3-01` · IPv4 `178.62.207.50` · ams3 · s-1vcpu-2gb · Ubuntu 24.04 LTS
- DNS `staging.studiolegalesaltelli.it` → propagato · HTTPS Let's Encrypt notAfter `2026-07-29` (auto-renew certbot.timer)
- Stack: nginx 1.24 + PHP 8.2.30 + MySQL 8.0.45 + WP-CLI · DB `saltelli_wp` popolato
- WP installato in `/var/www/saltelli` · theme su `wp-content/themes/saltelli/` (rsync da locale, NON git clone)
- Hardening: UFW (22/80/443), fail2ban, SSH no-root no-password, swap 2GB, unattended-upgrades
- Secrets locali: `.saltelli-staging-secrets` (gitignored) · droplet: `/home/deploy/.saltelli-secrets`
- Runbook deploy: `PROMPT_AGENT_G_DEPLOY_DIGITALOCEAN.md` (Fasi 0-2, 5-6 originarie completate; 3-4 fatte ad-hoc fuori runbook; 7-8 ancora aperte)
- Pending: reboot droplet per kernel 6.8.0-110

### What's done

| Phase | Version | Status |
|---|---|---|
| Scaffolding → Multi-agent → Polish → Impeccable | 0.1.0 → 0.4.0 | ✅ |
| Content Migration + Audit Alignment + Pain Points | 0.5.0 → 0.7.0 | ✅ |
| Template Polish + Mobile Fix | 0.8.0 | ✅ (storico) |
| Pre-presentation polish (homepage, hero, eyebrow, atelier, ToV "tu") | 0.16.0 → 0.16.3 | ✅ |
| Logo system v1.1 + sitemap audit + favicon fix + home fix | 0.17.0 → 0.17.1 | ✅ |
| Contatti rework + rhythm + sede no-iframe + pills + wp_site_icon unhook | 0.17.2 → 0.17.3 | ✅ |
| **Version consolidation + numbering policy (questo file)** | **0.17.4-beta-consolidation** | **✅ CURRENT** |
| Step F — Production Readiness (WOFF2, SRI, Lighthouse ≥92) | 1.0.0-rc1 | ⏸ |
| Cut produzione | 1.0.0 | ⏸ |

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
- `BRIEF_Saltelli_WordPress.md` — original brief
- `.claude/knowledge/project-context.json` — machine-readable context
- `.claude/knowledge/design/sessione-1/tokens.css` — design tokens locked
- `.claude/knowledge/design/sessione-1/homepage-desktop.jsx` — JSX reference for Frame 1

**Agent prompts:**
- `PROMPT_AGENT_E_TEMPLATE_POLISH_V2.md` — current
- `PROMPT_AGENT_F_PRODUCTION_READINESS.md` — next
- `PROMPT_AGENT_G_DEPLOY_DIGITALOCEAN.md` — runbook deploy (Fase 0 completata, Fasi 1-8 in attesa GO)
- `_archive/prompts-completed/` — past prompts (informational, do NOT execute)

**Reports** (in `.claude/knowledge/design/sessione-1/reports/`):
- `audit-alignment/REPORT.md` — Step Audit Alignment (sitemap + /costi/)
- `content-migration/REPORT.md` — Step D content migration
- `impeccable/REPORT.md` — Step C Impeccable refinement
- `pain-points-refinement/REPORT.md` — Step Pain Points (most recent)
- `visual-walkthrough/CHECKLIST.md` — 12-point checklist (reusable)
- `visual-walkthrough/REPORT-v0.7.0.md` — last walkthrough result (10 PASS · 1 WARN · 1 FAIL)

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
ACF Pro                (NOT installed — all fallbacks editorial hardcoded)
Yoast SEO              (active — coabitation enforced)
Custom theme path:     wp-content/themes/saltelli/
Animation libs:        GSAP 3.12.5 + ScrollTrigger from CDN (deferred)
                       Lenis 1.1.13 (currently disabled by Polish Agent — re-evaluate Step F)
                       SplitText: NOT used (Polish Agent animated <span>s directly)
```

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
*Last updated: 2026-04-30 · v0.7.0-beta-pain-points-fixed*
*Maintained by orchestrator (Claude in chat) after each milestone.*
