# CLAUDE.md — Studio Legale Saltelli WordPress Theme

> **Single source of truth per gli agent. Leggi PRIMA questo file, poi il prompt assegnato.**
> Storia completa wave + 0.17.x + prompt archive → [`docs/CHANGELOG.md`](docs/CHANGELOG.md)
> Lesson learned (OPcache, admin smoke test, recursion, data migration) → [`docs/LESSONS-LEARNED.md`](docs/LESSONS-LEARNED.md)
> Context machine-readable → [`.claude/knowledge/project-context.json`](.claude/knowledge/project-context.json)

**Repo layout:**
- `/docs/` — operativa viva (BRIEF, PRODUCT, DESIGN, ARCHITECTURE, DEPLOY, EDITOR-HANDOFF, CHANGELOG, LESSONS-LEARNED)
- `/prompts/` — prompt ATTIVI per Claude Code (uno alla volta)
- `/_archive/prompts-completed/` — prompt completati (5 sub-cartelle cronologiche)
- `/.claude/knowledge/` — working knowledge (recovery, audits, reference, _history)

## Identity

Custom WordPress theme deliberatamente differenziato, AI-ready, performance-obsessed per **Studio Legale Emiliano Saltelli & Partners** — law firm premium Napoli (Chiaia). Vendor: **Adsolut SRLS** (AI Agency). Il theme deve far sembrare datato il mercato legale napoletano esistente.

**Strategy:** "Legal Luxury Minimal" — boutique editoriale italiano, tipografia dominante, palette navy/crema/bronzo. Tier-1 deep clusters: Tributario · Lavoro · Famiglia LGBTQ+. Le altre 16 practice areas → tier-2 lighter pages.

## Current state — v1.3.24-wave-6-0-partial-stabilized (CUT-READY)

**Updated:** 2026-05-13 · **Branch:** `main` · last merge `d93b50a` (rollback template v1.3.23) · prev merge `9b5221c` (Wave 6.0 partial v1.3.22) · sanitize DB ops eseguito post-rollback (no commit codice, solo DB staging) · ~46 commit post-v1.3.13

**Status:**
- Demo ✅ presentata · feedback Elena 23 punti triage: 18 chiusi + 1 pre-batch + 1 zero-code deferred + 3 defer post-cut
- Staging `https://staging.studiolegalesaltelli.it` allineato v1.3.23 template rollback
- 13 Page WP Gutenberg-disabled · 18 Pages canoniche
- Design Handoff completato 12/12 template · Wave Elena FB Batch 1+2+3 + Wave 6.0 partial COMPLETATI
- tokens.css rebuilt da DESIGN.md (Wave 5 STEP 2) · sections.css drift cleanup STEP 4 (328 token swaps)
- Cross-page smoke test PASS

**Active phase:** Elena pre-cut minor UX fix via WP-Admin · cleanup 15 branch feat/chore orfani (post-cut) · attesa cliente per finestra cut produzione (DNS switch staging → prod)

**Next:** Cut produzione · backlog post-cut: Wave 6.0 full (disable Gutenberg CPT competenza) · Wave 6.1 SCF orphan cleanup (13 field in `docs/SCF_ORPHAN_FIELDS.md`) · P11 contatti · Wave 5.1 Image Expansion · Wave 5 STEP 5 phantom (~460) · single-post JSX Design · EDITOR-HANDOFF v6.1

### Infra staging (consolidata 2026-04-30)
- DO droplet `saltelli-staging-ams3-01` · `178.62.207.50` · ams3 · s-1vcpu-2gb · Ubuntu 24.04 LTS
- DNS `staging.studiolegalesaltelli.it` propagato · HTTPS Let's Encrypt notAfter `2026-07-29` (auto-renew certbot.timer)
- Stack: nginx 1.24 + PHP 8.2.30 + MySQL 8.0.45 + WP-CLI · DB `saltelli_wp`
- WP in `/var/www/saltelli` · theme `wp-content/themes/saltelli/` (rsync da locale, NON git clone)
- Hardening: UFW (22/80/443), fail2ban, SSH no-root no-password, swap 2GB, unattended-upgrades
- Secrets: `.saltelli-staging-secrets` (gitignored) · droplet: `/home/deploy/.saltelli-secrets`
- Pending: reboot droplet per kernel 6.8.0-110

### WP admin access (locale + staging, password allineate 2026-05-04)
- UID 1 `Emiliano Saltelli` (info@studiolegalesaltelli.it) → `WP_EMILIANO_PWD` in `.saltelli-staging-secrets`
- UID 8 `Adsolut Staff` (tech@adsolut.it) → `WP_ADSOLUT_PWD` in `.saltelli-staging-secrets`
- Reset via `wp_set_password()` locale e `wp user update` su droplet (phpass nativo, no MD5 fallback)

## Hard constraints (non-negotiable)

| Rule | Reason |
|---|---|
| **No page builder** (Elementor, Bricks, Divi, WPBakery) | No JS bloat, full markup control per schema injection |
| **Pure PHP template hierarchy** | Standard WP, predictable, auditable |
| **CPT** per `avvocato` e `competenza` | Scala schema markup automaticamente |
| **GSAP 3.12+ + Lenis only** per animations | NO AOS, WOW.js, ScrollMagic, Locomotive |
| **Schema JSON-LD inline** in templates | NOT plugin-generated, full control |
| **Single H1 per page** | Audit ha trovato duplicate H1 sul source site |
| **Mobile-first**, ogni breakpoint | 60%+ traffico mobile, AI Overviews 81% mobile |
| **No `#000000`**, no aggressive red, no purple/magenta | Purple/magenta = Adsolut brand, non Saltelli |
| **Design tokens locked** in `tokens.css` | Sistema dipende dalla stabilità |
| **Yoast coabitation** — mai emit Organization/Article/Breadcrumb se Yoast attivo | No schema duplicates |
| **Foto Emiliano `_thumbnail_id=2683`** + **bio_estesa avvocati** preserved | Step D content + Step C.5 photo integration |
| **One-writer-at-a-time** — una sola sessione (orchestratore O Claude Code) committa | Vedi §Workflow rules |

## Workflow rules (orchestratore ↔ Claude Code)

### Pattern
```
┌─ Orchestratore (Claude in chat, su Claude.ai)
│   └─ pianifica, scrive prompt, audita, mergea
│   └─ commits: docs/*, prompts/*, .claude/knowledge/*, CLAUDE.md, README.md
│
└─ Claude Code (sessione dedicata su terminale)
    └─ esegue prompt assegnato, lavora in branch dedicato
    └─ commits: wp-content/themes/saltelli/*, scripts/*, .claude/knowledge/audits/{nome}/*
```

### One-writer-at-a-time (HARD RULE)
Solo una sessione attiva sui commit alla volta. Quando l'orchestratore lancia un task per Code, l'orchestratore **NON committa** finché Code non ha pushato e l'audit è completato. Quando l'orchestratore lavora, Code è fermo. **Niente committi paralleli.**

Se vedi un fix urgente durante l'attesa: **annotalo, non committarlo**. Discutere in chat dopo l'audit (es. commit `0ee9789` + `b6bfbf9` del 2026-05-05).

### Mitigazione collisioni
- File disgiunti: `git pull --rebase` risolve, push.
- Stesso file: chi pusha secondo fa `git pull --rebase`, riconcilia, ripusha. **Mai riscrivere la storia git pubblica.**
- Stessa modifica fatta da entrambe: chi pusha secondo abbandona (`git reset --soft HEAD~`).

### Branch policy
- `main` — niente commit diretti per codice tema. Solo merge no-ff da branch dedicati.
- `feat/{nome}` — ogni wave/task in branch separato.
- `chore/{nome}` — housekeeping, refactor doc, cleanup.
- Documentazione minore (typo CLAUDE.md, link rotto README) — commit diretto su `main` ammesso, ma con l'altra sessione ferma.

### Identità git
Dal 2026-05-05 entrambe le sessioni committano sotto `AdsolutAdv <aldo.santoro@adsolut.it>` (config locale repo). Storia precedente `Codencore <git@adhost.it>` immutabile.

### Lesson learned critiche (dettaglio in `docs/LESSONS-LEARNED.md`)
1. **OPcache stale** post-edit file PHP critici → mandatory `systemctl reload php8.2-fpm` o `wp eval 'opcache_reset();'`. File trigger: `inc/helpers.php`, `inc/cpt-*.php`, `inc/migrations/*.php`, `inc/admin/*.php`, `functions.php`, `inc/redirects.php`.
2. **Admin-side smoke test obbligatorio** per ogni Wave che tocca SCF/Pages/`post_content`. Frontend `curl` da solo NON copre cosa vede Elena in admin.
3. **Mai chiamare cap functions** (`current_user_can`, `is_super_admin`, `user_can`) dentro callback `user_has_cap`/`map_meta_cap` → ricorsione infinita → OOM. Usa property dirette (`$user->roles`, `$user->caps`, `$user->allcaps`).
4. **Multi-agentic pre-flight pattern** — ~25% ETA reduction su sequenze multi-wave file-disgiunti.
5. **Data migration CPT**: `apply_filters('the_content', ...)` su legacy content può causare WSOD frontend. `update_post_meta` raw HTML può crashare TinyMCE admin. Pre-storage sanitize: strip control chars + normalize line endings + `wp_kses_post()`. Backup postmeta mandatory.

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
  --font-mono:    "JetBrains Mono" 400 (metadata: dates, tags, eyebrows)

BREAKPOINTS: 375 / 768 / 1024 / 1440 (mobile-first)
```

## Information architecture (post-Wave 5 IA + Wave 4.7.fix.2 slug rename + Design Handoff P7 consolidamento)

```
/                                              Homepage (front-page.php · hero variant B + 3 SCF additive)
/chi-siamo/                                    Page WP 2811 POST-P7 (template-parts/page-lo-studio.php · group_lo_studio_v1 34 field)
/chi-siamo/team/                               CPT archive avvocato (archive-avvocato.php · tab Archive Headers)
/chi-siamo/team/{slug}/                        CPT avvocato (single-avvocato.php) × 4
/chi-siamo/casi-rappresentativi/               CPT archive saltelli_caso (+4 SCF Wave P9 pull-quote · filtri tabs JS)
/chi-siamo/casi-rappresentativi/{slug}/        CPT saltelli_caso single
/aree-di-pratica/                              Hub Page WP 2812 (page-aree-di-pratica-hub.php · 4 cluster cards SCF)
/aree-di-pratica/{privati,imprese,contenzioso-amministrativo}/   Term tipo-area (group_tipo_area_term_v1 23 field per-term)
/aree-di-pratica/{cluster}/{competenza-slug}/  CPT competenza × 19 — tier-1/tier-2 branched
/risorse/                                      Hub Page WP 2813 (page-risorse-hub.php · 4 resource cards SCF)
/risorse/{domande-frequenti,guide-gratuite,glossario-legale}/   Pages WP figlie (glossario hardcoded 60 termini)
/risorse/blog/                                 Blog archive (home.php) · 326 post historical
/costi-e-consulenze/                           Hub Page WP 2695 (page-costi-e-consulenze-hub.php)
/costi-e-consulenze/{prima-consulenza,come-lavoriamo,richiedi-preventivo}/   Pages WP figlie
/contatti/                                     page.php · group_contatti_v1 19 field · NO mappa iframe v0.17.3
/prenota-appuntamento/                         Page WP 2714
/404                                           404.php (count aree dinamico + breadcrumb cluster)
/llms.txt                                      Static AI crawler file
```

**Redirect 301 legacy** (`inc/redirects.php`):
- `/chi-siamo/risultati/` → `/chi-siamo/casi-rappresentativi/`
- `/lo-studio/` → `/chi-siamo/` · `/chi-siamo/lo-studio/` → `/chi-siamo/` (Design Handoff P7)
- `/competenze/` → `/aree-di-pratica/` · `/tipo-area/{...}/` → `/aree-di-pratica/{...}/`
- `/faq/` → `/risorse/domande-frequenti/` · `/guide-gratuite/` → `/risorse/guide-gratuite/` · `/glossario-legale/` → `/risorse/glossario-legale/` · `/blog/` → `/risorse/blog/`
- `/costi/` → `/costi-e-consulenze/` · `/prima-consulenza,come-lavoriamo,richiedi-preventivo/` → `/costi-e-consulenze/{...}/`

## Tech stack

```
WordPress 6.x          (current Docker local)
PHP 8.2+
SCF 6.8.4              (Secure Custom Fields, Automattic fork — Wave 4.7.fix)
ACF Free 6.8.0         (INACTIVE su staging — kept for fast rollback only)
Yoast SEO              (active — coabitation enforced)
Custom theme path:     wp-content/themes/saltelli/
Animation libs:        GSAP 3.12.5 + ScrollTrigger from CDN (deferred)
                       Lenis 1.1.13 (currently disabled by Polish Agent)
                       SplitText: NOT used (animated <span>s directly)
```

Dettaglio plugin SCF + rollback emergency → `docs/CHANGELOG.md` §Custom fields plugin.

## Convention summary

**Naming:**
- Custom CSS classes con `.sl-*` prefix
- Sezioni homepage: `.sl-hero`, `.sl-areas`, `.sl-studio`, `.sl-team`, `.sl-cases`, `.sl-press`, `.sl-contact`, `.sl-footer`
- BEM-like: `.sl-area__title`, `.sl-area--tier1` (modifier)

**Files structure:**
- `assets/css/tokens.css` — variables only, never edit
- `assets/css/base.css` — reset, container, typography setup
- `assets/css/components.css` — buttons, links, accordion, area-list, attorney sticky
- `assets/css/sections.css` — section layouts + page-specific
- `assets/js/main.js` — entrypoint, GSAP+ScrollTrigger init, hover bindings
- `inc/schema/` — 5 PHP partials JSON-LD (organization, attorney, faqpage, breadcrumb, article)
- `inc/seo/` — meta-tags + ai-files (llms.txt + robots filter)
- `inc/cpt-*.php` — CPT registration
- `inc/acf-json/` — field group definitions

## Design → Code handoff (golden rule)

Quando Claude Design genera JSX con `style={{...}}` inline:

1. **Code DEVE mappare ogni inline style a className BEM** (`.sl-{template}__{element}`)
2. **Code DEVE generare CSS rule in `sections.css`** con scope marker `/* === v0.X.0 TEMPLATE === */`
3. **className mancanti NON sono optional** — tutti gli inline style del JSX → CSS rule con className
4. **Test pixel-perfect:** 1 inline style = 1 CSS rule + 1 className
5. **Verifica computed styles via Playwright** post-deploy

Lezione v0.19→v0.30: Code aveva tradotto solo 20% dei JSX inline (quelli con className), saltando l'80% inline-only. v0.27.x recovery ha mappato sistematicamente i gap. **Pattern obbligatorio.**

## Working rules

1. **Read this file first.** Poi il prompt assegnato + `project-context.json`. Poi SOLO i file in scope.
2. **Cache flush + curl test dopo OGNI cambio non-triviale.** Non batchare 5 changes e verificare a fine.
3. **Idempotency:** re-running script non deve duplicare menu/page/term.
4. **Touch only what's in scope.** Bug fuori scope → documentalo nel report, non fixare.
5. **Decisioni autonome → riportate.** Meglio verbose che mute.
6. **Never write to:** `_thumbnail_id` su CPT avvocato, `bio_estesa` su avvocati esistenti, `tokens.css` design variables, `config.local.json` (gitignored — credentials).
7. **Never disable plugins** durante un run.
8. **Admin-side smoke test obbligatorio** per Wave che tocca SCF/Pages/`post_content`. Vedi `docs/LESSONS-LEARNED.md` §2.

## Source of truth files (read first)

- `CLAUDE.md` (this file)
- `docs/BRIEF.md` — brief originale
- `docs/PRODUCT.md` — brand voice + anti-references
- `docs/DESIGN.md` — design tokens
- `docs/ARCHITECTURE.md` — theme + ACF schema mapping
- `docs/DEPLOY.md` — runbook deploy droplet
- `docs/EDITOR-HANDOFF.md` v6.0 — manuale editoriale Elena/Ludovica
- `docs/CHANGELOG.md` — wave history + 0.17.x + prompt archive
- `docs/LESSONS-LEARNED.md` — pattern operativi consolidati
- `.claude/knowledge/project-context.json` — machine-readable context

## Tone (reporting a Duccio)

Direct. Concrete. Zero filler. Lui vuole: precise diagnosis, ranked options con rationale, explicit blockers, no apology padding. Mirror this tone in commit messages e status updates.

## What NOT to do

- Don't invent client data. Reale in `project-context.json`.
- Don't reuse Adsolut brand colors (magenta, purple, galaxy theme).
- Don't ship "AI slop" — generic legal stock imagery, lorem ipsum, placeholder unsplash handshakes.
- Don't optimize desktop "wow" a spese di mobile LCP.
- Don't add new dependencies (libraries, plugins, fonts) senza explicit instruction.
- Don't refactor template hierarchy senza explicit instruction.
- Don't sentence-case Italian sigle (INPS, IRPEF, IMU, RC, LGBTQ+) o proper nouns (Napoli, Cassazione, Federico) quando normalizzi case.

## When in doubt

Rileggi questo file. Se persiste il dubbio, chiedi a Duccio. Don't guess su:
- Client-facing copy
- Attorney specialization details
- Pricing o commercial terms
- Anything che apparirebbe in schema markup come fact

---
*Updated: 2026-05-13 · v1.3.24-wave-6-0-partial-stabilized CUT-READY*
*Storia dettagliata → `docs/CHANGELOG.md` · Pattern operativi → `docs/LESSONS-LEARNED.md`*
*Maintained by orchestrator dopo ogni milestone.*
