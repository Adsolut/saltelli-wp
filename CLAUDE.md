# CLAUDE.md — Studio Legale Saltelli WordPress Theme

> **Read this file first.** Then read `BRIEF_Saltelli_WordPress.md` and `.claude/PROMPT_LEAD_AGENT.md`.
> Project context machine-readable: `.claude/knowledge/project-context.json`.

## Identity

You are working as part of the build team for **Studio Legale Emiliano Saltelli & Partners** — a premium law firm in Naples (Chiaia district). The vendor is **Adsolut SRLS**, an AI Agency. You are NOT building a generic legal site. You are building a deliberately differentiated, AI-ready, performance-obsessed custom WordPress theme that should make the existing Naples legal market look dated.

## Project state

**Current phase:** Pre-development setup. Awaiting SSH/FTP credentials from client to dump the existing site (`studiolegalesaltelli.it`).

**Active deliverables (Fase 1 — €8.500 of €14.000 program):**

1. WordPress custom theme `.zip`, installable
2. Schema JSON-LD coverage on every page
3. `llms.txt` + AI-optimized `robots.txt`
4. Heading hierarchy fix, meta descriptions, Open Graph
5. Google Business Profile activation
6. Lighthouse > 90 across all metrics

## Hard constraints (non-negotiable)

| Rule | Reason |
|---|---|
| **No page builder** (no Elementor, Bricks, Divi, WPBakery) | Removes JS bloat, gives full control over markup for schema injection |
| **Pure PHP template hierarchy** | Standard WP, predictable, auditable |
| **Custom Post Types** for `avvocato` and `competenza` | Scales schema markup automatically per entity |
| **GSAP 3.15+ + Lenis only** for animations | Industry-standard 2026 stack. NO AOS, WOW.js, ScrollMagic, Locomotive |
| **Schema JSON-LD inline in templates** | NOT plugin-generated. We need full control |
| **1 H1 per page**, ever | Audit found duplicate H1s on the current site. Don't repeat the mistake |
| **Mobile-first**, every breakpoint | 60%+ traffic is mobile, AI Overviews trigger 81% from mobile |
| **No #000000 black**, no aggressive red, no purple/magenta | Purple/magenta is the Adsolut brand. Saltelli is its own brand: cream, navy, bronze |

## Design system (locked)

```
Background:    #FAFAF8 (cream)
Primary dark:  #1B2B4B (navy)
Accent:        #B8860B (bronze/gold)
Text:          #2D2D2D (deep grey, NOT black)

Display font:  Playfair Display OR Cormorant Garamond (serif)
Body font:     DM Sans OR Satoshi (sans-serif)
```

Breakpoints: 375 / 768 / 1024 / 1440.

## Information architecture

```
/                                   Homepage
/lo-studio/                         About
/avvocati/                          Team archive
/avvocati/{slug}/                   CPT: avvocato
/competenze/                        Practice areas archive
/competenze/{slug}/                 CPT: competenza (19 entries)
/blog/                              Blog archive (with categories)
/blog/{slug}/                       Single post
/contatti/                          Contact (form + map + hours)
/llms.txt                           Static AI crawler file
```

## File conventions

- Theme path: `wp-content/themes/saltelli/`
- CSS: NO Tailwind, NO Bootstrap. Custom CSS with CSS variables (design tokens) in `assets/css/tokens.css` + module files
- JS: ES modules where possible, `defer` everywhere, GSAP + Lenis loaded from CDN with SRI hashes
- Fonts: self-hosted WOFF2, `font-display: swap`, preload only the 2 critical weights
- Images: WebP/AVIF with fallback, native `loading="lazy"` except above-the-fold hero
- Schema: one PHP partial per type in `inc/schema/` — see `geo-assets/schema/` for ready-to-inject templates

## Mandatory pre-coding reads

Before writing any code, read in order:

1. `CLAUDE.md` (this file)
2. `BRIEF_Saltelli_WordPress.md`
3. `.claude/PROMPT_LEAD_AGENT.md`
4. `.claude/knowledge/project-context.json`
5. `geo-assets/schema/README.md`
6. Any `SKILL.md` files in `~/.claude/skills/` and `.claude/skills/`

## Multi-agent decomposition

When the lead agent decomposes work for tmux parallel execution:

AgentOwnsReads from**Theme Architect**Template hierarchy, `functions.php`, ACF field groups, CPT registration, menus, widget areasBrief sections "Architettura sito" + "Stack tecnico"**Style & Animation**Design tokens, layout system, typography loading, GSAP+Lenis setup, all WOW effects, micro-interactionsBrief sections "Design direction" + "Stack effetti WOW", `CLAUDE_DESIGN_PROMPT.md` (for visual reference)**GEO Engineer**Schema JSON-LD partials, `llms.txt`, `robots.txt`, meta tag system, OG/Twitter, performance optimization, Lighthouse iterationBrief section "Requisiti tecnici GEO", `geo-assets/` directory

Agents communicate via the lead. They do NOT edit each other's files without coordination.

## Strategic content decision (CONFIRMED 2026-04-28)

GEO Audit recommended focus on **2-3 vertical niches** for topical authority. Decision confirmed: tier-1 deep clusters are:

1. **Diritto tributario / cartelle esattoriali** (Emiliano + Fabiana)
2. **Diritto del lavoro** (Fabiana giuslavorista)
3. **Diritto di famiglia e tutela LGBTQ+** (Antonia)

The other 16 practice areas get tier-2 standard pages: H1 + answer capsule + 400-600 words + 3-FAQ + CTA. Tier-1 areas get 1500-2500 words + 5-FAQ + casi rappresentativi + cluster di articoli blog correlati.

**Implementation:** add ACF boolean field `is_tier_1_focus` on CPT `competenza`. The `single-competenza.php` template branches on this flag to render the appropriate depth. Set `true` for the three slugs above, `false` for the others. Full breakdown in `project-context.json` → `strategic_focus_decision`.

## Quality gates before any deploy

- \[ \] Schema validated on `validator.schema.org` AND `search.google.com/test/rich-results`
- \[ \] Lighthouse Performance/Accessibility/Best-Practices/SEO all &gt; 90 on mobile + desktop
- \[ \] Single H1 per page (verify with HeadingsMap or DevTools)
- \[ \] `llms.txt` and `robots.txt` reachable at root
- \[ \] No console errors, no 404s on assets
- \[ \] Cross-browser pass: Chrome, Safari, Firefox, mobile iOS Safari + Android Chrome
- \[ \] Forms tested with real SMTP (not WP default mail)
- \[ \] Schema markup test on `/`, `/avvocati/emiliano-saltelli/`, `/competenze/diritto-tributario/`, one blog post

## Tone of communication when reporting back to Duccio
Direct. Concrete. Zero filler. He values: precise diagnosis, ranked options with rationale, explicit blockers, no apology padding. Mirror this tone in commit messages and status updates.

## What NOT to do

- Don't invent client data. Phone, email, addresses are in `project-context.json`. Don't make up VAT numbers, social URLs, attorney bios.
- Don't reuse Adsolut brand colors (magenta, purple, galaxy theme) — those are for Adsolut decks, not for Saltelli's site.
- Don't ship "AI slop" — generic legal stock imagery, lorem ipsum, placeholder unsplash photos of handshakes. Mark every visual placeholder with a `<!-- TODO: replace with real Saltelli photo -->` comment.
- Don't treat the 19 practice areas as content blockers. Build the structure now; content depth comes in Fase 3 of the program.
- Don't optimize for desktop hero "wow" at the cost of mobile LCP. Cinematic but lean.

## When in doubt

Re-read the brief. If still in doubt, ask Duccio. Don't guess on:
- Client-facing copy
- Attorney specialization details
- Pricing or commercial terms
- Anything that would appear in schema markup as fact

---
*Last updated: 2026-04-28 by Claude in sync with Duccio.*
