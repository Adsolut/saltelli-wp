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

## Current state — v1.3.13-wave5-step3-coverage (CUT-READY)

**Last updated:** 2026-05-11 (5 wave + 5 chore consecutivi: Wave 4.7.fix.5 → Wave 5 STEP 1 audit + STEP 2 design realign + STEP 3 Pages SCF + STEP 3 coverage → 4 chore frontend polish → Wave 5 STEP 4 sections.css drift cleanup (328 token swaps) · Elena OK definitivo su tutte le 19 Pages + 2 archive CPT + 3 term tipo-area · EDITOR-HANDOFF v6.0)
**Branch:** `main` · last merge `5309876` (Wave 5 STEP 4 — sections.css drift cleanup, 328 token swaps) · tag `v1.3.13-wave5-step3-coverage` (Wave 5 STEP 4 = no version bump, chore frontend cleanup) · 49 commits totali dal v1.3.10
**Demo:** ✅ presentata al cliente · feedback iteration assorbita
**Live staging:** https://staging.studiolegalesaltelli.it allineato a `1.3.13-wave5-step3-coverage` + Wave 5 STEP 4 CSS (328 token swaps, computed-neutral) · **13 Page WP Gutenberg-disabled** (12 + Prenota appuntamento 2714) · **19 Pages canoniche** (35 → 19 post cleanup fix.5) · **7 Pages alto-traffico con SCF metabox completa** (Home/Chi Siamo/Lo Studio/Aree di Pratica/Risorse/Costi e Consulenze/Contatti, 107 field text/textarea) · **2 archive CPT** con SCF "Archive Headers" expanded (Team + Casi rappresentativi) · **3 term tipo-area** con SCF group dedicato (privati/imprese/contenzioso-amministrativo, 23 field per content per-term) · tokens.css rebuilt da DESIGN.md (12 token allineati + 4 mancanti aggiunti + top-15 violazioni hardcoded fixed) · 4 chore frontend polish (Tutte le aree href + 404 count dinamico + breadcrumb cluster + 2 duplicate body sections) · sections.css drift cleanup STEP 4 (328 hardcoded typography literals → var(--token), ~460 phantom residui documentati) · cross-page smoke test PASS · **Elena OK definitivo 2026-05-11**
**Active phase:** Front-end debug visivo — STEP 4 ha risolto 328/788 hardcoded typography in sections.css (~42%); restano ~460 phantom residui (valori senza token in tokens.css: 13px×27, 10px×21, 17px×19, -0.015em×21, 0.06em×14, 1.1×21, 1.55×18, 1.6×16, ~70 ad-hoc clamp() curves… · `font-size: 22px`×23 AMBIGUO --fs-h3-floor vs --fs-lede · `line-height: 1.65`×5 = vecchio --lh-body deprecato) — vedi `.claude/knowledge/audits/wave5-step4-sections-cleanup/` · pre-cut produzione checklist
**Next:** "Phantom resolution" wave backlog candidate (STEP 4 §"Recommended order": 1° `font-size: 22px`×23 per-selector resolve + esporre `--fs-h2-floor: 28px` — entrambi computed-neutral; 2° promuovere valori ricorrenti a token espliciti — neutral; 3° clamp() consolidation + caption-nebula normalization — CAMBIA computed values, serve design sign-off + Playwright pixel-diff) · Wave 6.0 backlog candidate (CPT competenza Strategy A migration: disable Gutenberg + post_content → body_extended unification, post-cut) · Wave 5.1 backlog candidate (Image Expansion per Pages: hero + featured + cluster cards) · Cut produzione (DNS switch staging → prod) · valutare ripristino Yoast `twitter:label1` reading-time meta (-102 byte side effect benigno su 2 pagine)

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
| **Wave 4.7.fix.1 — SCF URL Validation Fix (CTA interni type:url→text)** | **1.3.7-wave4-7-fix-1-scf-url-validation** | **✅** |
| **Wave 4.7.fix.2 — TRUE FIX (studio_body editorial JSON default + menu primary slug-based rebuild + 14 redirect 301 legacy + SCF tier-2 60→93 fields, 13 tabs + EDITOR-HANDOFF v3.0 + slug rename `risultati`→`casi-rappresentativi`)** | **1.3.8-wave4-7-fix-2-true-fix** | **✅** |
| **Wave 4.7.fix.3 — PAGE METABOX MIGRATION (30 SCF field da Theme Options globali → Page metabox delle 4 Page WP: Home 17, Chi Siamo 2822, Aree 2812, Risorse 2813. Theme Options 13/14 → 9 tab. Helper `saltelli_page_field()` introdotto. EDITOR-HANDOFF v4.0. Risolve feedback Elena "il CMS non è usabile in questo modo")** | **1.3.9-wave4-7-fix-3-page-metabox** | **✅** |
| **Wave 4.7.fix.4 — STRATEGY A FULL SCF MIGRATION (Gutenberg disabled per 12 Page WP target: 4 hub + 7 dual-source bonificate + 1 child legacy lo-studio. Discovery empirica ha rivelato 6/7 post_content zombie + 1/7 live (Page 2713 → SCF body_content). Admin shortcuts per archive CPT. EDITOR-HANDOFF v5.0. Modello mentale editor definitivo: una sola sorgente di verità per Page = SCF metabox)** | **1.3.10-wave4-7-fix-4-strategy-a-full-scf** | **✅** |
| **Wave 4.7.fix.5 — PAGES CLEANUP + BLOG DOC + CUSTOMIZER LOCK (35 Pages → 19 KEEP, 16 cestinate: 13 draft orfani 2019-2025 + 3 publish duplicate. Blog editing audit 02-blog-editing-map.md + admin sidebar notices. WP Customizer lock-down per ruolo editor con filter user_has_cap. Incident OOM ricorsione is_super_admin risolto. EDITOR-HANDOFF v6.0)** | **1.3.11-wave4-7-fix-5-cleanup** | **✅** |
| **Wave 5 STEP 1 — Pages Completeness Audit (read-only, 16 deliverable: 13 audit Pages + 2 archive CPT + decision matrix con field SCF da aggiungere per Page con type incluso image + repeater, stima ordine implementation STEP 3)** | audit/wave5-pages-completeness | **✅** |
| **Wave 5 STEP 2 — Design Realignment (tokens.css rebuilt da docs/DESIGN.md come SoT: 12 token disallineati + 4 mancanti corretti, letter-spacing ottico 4 valori per hierarchy, line-height ottico 5 valori, top-15 violazioni hardcoded fixed in components.css + sections.css. Remaining ~590 hardcoded values deferred a wave dedicata)** | feat/wave5-design-realign | **✅** |
| **Wave 5 STEP 3 — Pages SCF expansion completa (7 Pages alto-traffico: Home 17 + Chi Siamo 2822 + Aree di Pratica 2812 + Risorse 2813 + Costi e Consulenze 2695 + Lo Studio 2811 + Contatti 23 con 107 field text/textarea conservative pattern Elena-approved. Default_value byte-per-byte = hardcoded → frontend invariato pre/post. Image/repeater rinviati a backlog Wave 5.1)** | **1.3.12-wave5-step3-pages-scf** | **✅** |
| **Wave 5 STEP 3 coverage completion — chiusura gap Elena 6 elementi mancanti (2 archive CPT Team + Casi rappresentativi con tab "Archive Headers" espansa + riuso CPT saltelli_principio · 3 term tipo-area privati/imprese/contenzioso-amministrativo con nuovo group_tipo_area_term_v1 attached a taxonomy 23 field per-term · 1 Page Prenota appuntamento 2714 con nuovo group_prenota_appuntamento_v1 + Gutenberg disabled SCF-only 13 IDs)** | **1.3.13-wave5-step3-coverage** | **✅** |
| **Chore fix single-competenza frontend regression** (helper saltelli_aree_hub_url() risolve hub via get_page_by_path post Wave 5 IA refactor has_archive=>false. Swap in 4 call sites: single-competenza.php back link + 404.php CTA + 2 breadcrumb nodes in helpers.php) | chore | **✅** |
| **Chore pre-cut polish** (404.php count aree dinamico via wp_count_posts + breadcrumb cluster intermediate node + docs/DEPLOY.md §2 rsync command completato) | chore | **✅** |
| **Chore fix single-competenza duplicate body** (logica template "uno o l'altro" tra sl-competenza__intro/post_content e sl-competenza__body/body_extended SCF, mai entrambi simultanei) | chore | **✅** |
| **Chore fix single-competenza tier-1 clusters duplicate body_extended** (rimossa duplicazione tra helper saltelli_tier1_clusters hardcoded e sl-competenza__body SCF, polish Duccio) | chore | **✅** |
| Acceptance test editoriale Elena | **OK DEFINITIVO 2026-05-11** | **✅** |
| **Wave 5 STEP 4 — sections.css drift cleanup** (328 hardcoded typography literals → var(--token) reference, solo dove il valore matcha esattamente un token in tokens.css. Conservative: exact match only, ZERO cambio computed CSS values, frontend pixel-perfect invariato pre/post. font-size 129 swaps (11px→--fs-caption 44×, 12px→--fs-micro 28×, 14px→--fs-small, 16px→--fs-body, 18px→--fs-body-marketing, 32px→--fs-h3-max, 96px→--fs-h1-max, clamp(48,6vw,96)→--fs-h1, clamp(28,3.5vw,44)→--fs-h2) + letter-spacing 91 (-0.02em→--ls-h1 37×, 0.08em→--ls-mono 24×, -0.01em→--ls-h2, -0.035em→--ls-display, -0.005em→--ls-h3) + line-height 108 (1.5→--lh-lede 29×, 1.7→--lh-body 17×, 0.98→--lh-display, 1.05→--lh-h1, 1.15→--lh-heading, 1.2→--lh-h3, 1.4→--lh-mono). ~460 phantom residui documentati in audits/wave5-step4-sections-cleanup/02-phantom-values-remaining.md con file:line + rationale + ranked plan. Drift typography sections.css ~42% risolto (788 literal sul file attuale → ~460; "605" era la stima baseline 2026-05-08, il file è cresciuto via STEP 2/3 + chore). 21/21 token mappings verificati byte-equal al literale che sostituiscono. Smoke test 5 URL: HTML markup invariato. No version bump) | chore | **✅** |
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
- `recovery-v1.0/PROMPT_AGENT_WAVE4_7_FIX_2_TRUE_FIX.md` — ✅ done (5 phases · 21 file · +1688/-57 · 26/26 URL smoke pass · SCF 60→93 fields)
- `recovery-v1.0/PROMPT_AGENT_WAVE4_7_FIX_3_PAGE_METABOX.md` — ✅ done (5 phases · 20 file · +1849/-643 · 30 field migrati · 4 Pages WP affected · Theme Options 13/14→9 tab · 4/4 smoke pass)
- `recovery-v1.0/PROMPT_AGENT_WAVE4_7_FIX_4_STRATEGY_A_FULL_SCF.md` — ✅ done (6 phases · 26 file · +2755/-38 · pivot empirico 6/7 zombie + 1/7 live · 12 Pages Gutenberg-disabled · admin shortcuts CPT · 12/12 smoke pass)
- `recovery-v1.0/PROMPT_AGENT_WAVE4_7_FIX_5_PAGES_CLEANUP_BLOG_DOC.md` — ✅ done (5 phases · 10 file · +686/-15 · 35→19 Pages cleanup · Customizer lock per role editor · blog audit + admin notices · incident OOM is_super_admin risolto)
- Wave 5 STEP 1/2/3/4 prompt — inline in chat orchestratore (non file): STEP 1 audit + STEP 2 design realign + STEP 3 7+6 Pages SCF expansion + STEP 4 328 sections.css token swaps · 8 commit P1→P8 STEP 3 + 4 commit STEP 3 coverage + 4 chore frontend fixes + STEP 4 (1 feat commit `9fbfe61` + merge `5309876`) · output knowledge: `audits/wave5-pages-completeness/` (STEP 1) + `audits/wave5-step4-sections-cleanup/` (STEP 4)
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
- `docs/EDITOR-HANDOFF.md` v6.0 — manuale editoriale italiano per Elena/Ludovica/esterni (post-Wave 4.7.fix.5 + Wave 5 STEP 3 coverage: §3.7 lista canonica 19 Pages KEEP, §9 blog editing chiarezza completa, §3.6 archive CPT + admin shortcuts, modello mentale "una sola sorgente di verità per Page = SCF metabox" applicato a 13 Pages SCF-only)

**Working knowledge** (`.claude/knowledge/`):
- `recovery/` — release notes wave (5 file, vivi)
- `audits/debug-qa/` — 4 bug ticket + 1 report + audit raw (vivi)
- `audits/wave5-pages-completeness/` — Wave 5 STEP 1 audit (13 audit Pages + 2 archive CPT + decision matrix, vivo) · `audits/wave5-design-realign/` — Wave 5 STEP 2 (tokens.css realign, vivo) · `audits/wave5-step4-sections-cleanup/` — Wave 5 STEP 4 (`01-inventory-classification.md` + `02-phantom-values-remaining.md`: inventario 788 typography literal, 328 swap, ~460 phantom residui con file:line + ranked plan, vivi)
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

### Lesson learned — OPcache stale dopo edit `inc/helpers.php` (Wave 4.7.fix.3)

PHP-FPM mantiene OPcache su file `inc/helpers.php` e altri `.php` letti hot. Quando si edita un helper su staging via rsync, le modifiche **non sono visibili al frontend immediatamente** finché OPcache non rigenera l'opcode. Sintomo: helper aggiornato sul disco ma frontend continua a usare la versione precedente (smoke test inspiegabilmente fallito post-deploy).

Mitigazione obbligatoria post-edit di file PHP critici (helpers, migrations, hooks):

```sh
ssh deploy@178.62.207.50 "sudo systemctl reload php8.2-fpm"
# oppure più chirurgico:
ssh deploy@178.62.207.50 "sudo -u www-data wp eval 'opcache_reset();' --path=/var/www/saltelli"
```

**File trigger** (cache bust raccomandato dopo modifica): `inc/helpers.php`, `inc/cpt-*.php`, `inc/migrations/*.php`, `inc/admin/*.php`, `functions.php`, `inc/redirects.php`.

### Lesson learned — Admin-side smoke test per ogni Wave che tocca Pages WP (Wave 4.7.fix.4)

Le Wave 4.7.fix.2 e 4.7.fix.3 hanno fatto smoke test SOLO frontend (`curl` su URL → 200 + content render). Hanno mancato il problema "Elena vede content legacy nel `post_content` che non viene renderizzato sul frontend ma confonde in admin". La Wave 4.7.fix.4 ha richiesto emergency cleanup di una situazione che era già presente post-fix.3 se solo avessimo controllato in admin.

**Step obbligatorio post-deploy per ogni Wave che tocca SCF field group, template Pages, o `post_content`**:

1. **Frontend smoke test** (esistente): `curl -s URL | grep <content>` su ogni Page affetta — verifica content visibile invariato.
2. **Admin-side smoke test** (nuovo): per ogni Page WP affetta, simulare apertura WP-Admin → Pagine → seleziona Page → Modifica. Descrivere COSA VEDE L'EDITOR (Gutenberg attivo o disabled, `post_content` content presente o vuoto, metabox SCF visibili e popolate, notice presenti).

Tool consigliato: WP-CLI `wp eval` con `apply_filters('use_block_editor_for_post', true, $post)` + `get_post($id)->post_content` per simulare admin view, oppure manual login admin se SSO disponibile su staging.

**Falso negativo classico** (= cosa NON è stato fatto in fix.2/fix.3): assumere che "post_content non renderizzato sul frontend" = "post_content invisibile a Elena". È falso: l'editor admin mostra `post_content` sempre, indipendentemente da cosa fa il template. Lei lo vede, lo modifica, salva, frontend non cambia = perde fiducia nel CMS.

### Lesson learned — Mai chiamare cap functions dentro `user_has_cap` o `map_meta_cap` filter (Wave 4.7.fix.5)

Durante Wave 4.7.fix.5 Phase 4 (Customizer lock-down per ruolo editor) la prima versione di `inc/admin/customizer-lockdown.php` aveva, dentro la callback del filtro `user_has_cap`, una chiamata a `is_super_admin($user->ID)` come check ulteriore oltre `in_array('administrator', $user->roles)`. **`is_super_admin()` su single-site fa internamente `$user->has_cap('delete_users')`** → `apply_filters('user_has_cap', ...)` → ri-trigger della stessa callback → `is_super_admin()` → **ricorsione infinita**.

Test successivo con `wp eval 'wp_set_current_user(9); current_user_can("customize")'` (WP-CLI ha `memory_limit = -1`) ha saturato RAM 2GB + swap 2GB → thrashing → nginx/sshd/php-fpm non rispondenti per ~8 min (frontend `000`, SSH "banner exchange timed out"). OOM killer ha terminato php runaway → servizi ripristinati. Nessun reboot.

**Regola tassativa**: dentro callback dei filtri `user_has_cap`, `map_meta_cap`, `user_can` **MAI chiamare**:
- `current_user_can()`, `user_can()` → fanno cap check, ricorsione
- `is_super_admin()` → su single-site fa `has_cap('delete_users')`, ricorsione
- Funzioni che internamente fanno cap check

**Accesso safe ai dati user dentro queste callback**:
- `$user->roles` (array)
- `$user->caps` (array raw)
- `$user->allcaps` (array merged)
- Accesso diretto a property, NON passa dal filtro → no ricorsione

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

## Information architecture (current — post-Wave 5 IA refactor + Wave 4.7.fix.2 slug rename)

```
/                                                  Homepage (front-page.php)
/chi-siamo/                                        Hub Page WP 2822 (page-chi-siamo-hub.php · SCF tab "Hub Pages")
/chi-siamo/team/                                   CPT archive avvocato (archive-avvocato.php · SCF tab "Archive Headers")
/chi-siamo/team/{slug}/                            CPT avvocato (single-avvocato.php) × 4
/chi-siamo/casi-rappresentativi/                   CPT archive saltelli_caso (archive-saltelli_caso.php · SCF tab "Archive Headers") — rename Wave 4.7.fix.2 da /chi-siamo/risultati/ + 301
/chi-siamo/casi-rappresentativi/{slug}/            CPT saltelli_caso single
/aree-di-pratica/                                  Hub Page WP 2812 (page-aree-di-pratica-hub.php · SCF tab "Hub Pages" · 4 cluster cards SCF)
/aree-di-pratica/{privati,imprese,contenzioso-amministrativo}/   Term tipo-area (taxonomy-tipo-area.php · SCF UX strings)
/aree-di-pratica/{cluster}/{competenza-slug}/      CPT competenza × 19 — branched tier-1/tier-2 (single-competenza.php)
/risorse/                                          Hub Page WP 2813 (page-risorse-hub.php · SCF tab "Hub Pages" · 4 resource cards SCF)
/risorse/{domande-frequenti,guide-gratuite,glossario-legale}/   Pages WP figlie (page.php)
/risorse/blog/                                     Blog archive (home.php) · post historical × 326
/costi-e-consulenze/                               Hub Page WP 2695 (page-costi-e-consulenze-hub.php)
/costi-e-consulenze/{prima-consulenza,come-lavoriamo,richiedi-preventivo}/   Pages WP figlie
/contatti/                                         Contact (page.php)
/prenota-appuntamento/                             Page WP 2714
/llms.txt                                          Static AI crawler file (served dynamically)
```

**Redirect 301 legacy attivi** (`inc/redirects.php` · Wave 4.7.fix.2 P2):
- `/chi-siamo/risultati/` → `/chi-siamo/casi-rappresentativi/`
- `/competenze/` → `/aree-di-pratica/` · `/tipo-area/{privati,imprese,contenzioso}/` → `/aree-di-pratica/{...}/`
- `/faq/` → `/risorse/domande-frequenti/` · `/guide-gratuite/` → `/risorse/guide-gratuite/` · `/glossario-legale/` → `/risorse/glossario-legale/` · `/blog/` → `/risorse/blog/`
- `/costi/` → `/costi-e-consulenze/` · `/prima-consulenza/` → `/costi-e-consulenze/prima-consulenza/` · `/come-lavoriamo/` → `/costi-e-consulenze/come-lavoriamo/` · `/richiedi-preventivo/` → `/costi-e-consulenze/richiedi-preventivo/`

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
8. **Admin-side smoke test obbligatorio** per ogni Wave che tocca SCF field group, template Pages, o `post_content`. Frontend `curl` da solo NON copre ciò che Elena vede in WP-Admin. Vedi § "Lesson learned — Admin-side smoke test" sopra.

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
*Last updated: 2026-05-11 · v1.3.13-wave5-step3-coverage CUT-READY · 5 wave consecutive (fix.5 + Wave 5 STEP 1 audit + STEP 2 design realign + STEP 3 Pages SCF + STEP 3 coverage) + 5 chore frontend polish (4 + Wave 5 STEP 4 sections.css drift cleanup: 328 typography literal → var(--token), computed-neutral, ~460 phantom residui documentati) · 19 Pages canoniche · 13 Pages Gutenberg-disabled SCF-only · 7 Pages alto-traffico SCF expansion completa (107 field) · 2 archive CPT + 3 term tipo-area SCF metabox · tokens.css rebuilt da DESIGN.md · EDITOR-HANDOFF v6.0 · Elena OK definitivo 2026-05-11 · 3 lesson learned in Workflow rules (OPcache + admin-side smoke test + cap-recursion in user_has_cap) · prossimo: front-end debug visivo (phantom resolution wave: 22px×23 + clamp() consolidation) + cut produzione*
*Maintained by orchestrator (Claude in chat) after each milestone.*
