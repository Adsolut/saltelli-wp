# .claude/knowledge/_history/ — Storia design pre-recovery

> ⚠️ **Questa cartella è informativa, NON operativa.**
> Per le decisioni di design correnti vai a [`/docs/DESIGN.md`](../../../docs/DESIGN.md) e [`/docs/PRODUCT.md`](../../../docs/PRODUCT.md).

## Cosa c'è qui

Documenta **26 file** sopravvissuti al cleanup (commit `0ee9789`, 2026-05-05) — JSX prototype, design system reference e i report-chiave delle due sessioni di design condotte in chat (Claude.ai), prima del recovery v1.0 che ha riscritto l'architettura CMS.

I 90 file rimossi (reports v0.x dettagliati, audit pixel-perfect, prompt wave 3 design, screenshot reference) restano consultabili in **git history**: `git log --all --follow -- .claude/knowledge/_history/design/sessione-X/...`.

### `design/sessione-1/` (7 file)

Prima sessione: **scaffolding del design system**.

- `Saltelli Partners - Sessione 1.html` — export chat originale
- `design-canvas.jsx` — canvas iniziale
- `design-system.jsx` — JSX reference design system (palette, typography, components)
- `homepage-desktop.jsx` — prototype Frame 1 desktop
- `homepage-mobile.jsx` — prototype Frame 1 mobile
- `tokens.css` — token CSS variables (storia)
- `README.md` — questo file

### `design/sessione-2/` (19 file)

Seconda sessione: **iterazioni pixel-perfect + foundation**.

JSX template prototype (10):
- `saltelli-s2-{404,attorney-single,blog-archive,casi,chi-siamo,chrome,contatti,footer,glossario-legale,practice-tier1,taxonomy-tipo-area}.jsx`

Report milestone (8):
- `IMPLEMENTATION-REPORT-v0.19.0.md`
- `IMPECCABLE-v0.20.0-REPORT.md`
- `v0.20.1-CARRYOVER-REPORT.md`
- `v0.20.2-FOOTER-V2-REPORT.md`
- `v0.21.0-PERFORMANCE-REPORT.md`
- `v0.21.23-TYPO-BREATHING-REPORT.md`
- `v0.22.0-IMPECCABLE-ANIMATIONS.md`
- `Saltelli Partners - Sessione 2.html` — export chat originale

## Perché questa cartella esiste

Le due sessioni di design hanno prodotto **decisioni di brand identity, design tokens, IA, copy editoriale** che sono ancora attivi nel sito attuale, anche se il recovery v1.0 (Maggio 2026) ha riscritto l'**architettura CMS** (template + ACF + content model).

Le decisioni *visuali* sono ancora valide. Le decisioni *strutturali* sono state superate dal recovery (vedi `.claude/knowledge/recovery/` per il modello dati corrente).

## Decisioni chiave da queste sessioni (ancora attive)

Tutte queste decisioni vivono ora in `/docs/DESIGN.md`, `/docs/PRODUCT.md`, `/docs/ARCHITECTURE.md` e nei file CSS/PHP del tema. La colonna "Origine storica" è solo per traccia genealogica — per riferimento operativo guarda i `/docs/`.

| Decisione | Live in | Origine storica |
|---|---|---|
| Palette navy/cream/bronze (`#1B2B4B`, `#FAFAF8`, `#B8860B`) | `docs/DESIGN.md` + `assets/css/tokens.css` | s1 v0.5–v0.8 (`sessione-1/design-system.jsx`) |
| Typography: Playfair Display + DM Sans + JetBrains Mono | `docs/DESIGN.md` + `assets/css/tokens.css` | s2 v0.21.23 (`sessione-2/v0.21.23-TYPO-BREATHING-REPORT.md`) |
| Layout containers `--sl-w-text` 720 / `--sl-w-content` 1100 | `assets/css/base.css` | s2 v0.27.2 (git history) |
| Drop-cap automatico primo paragrafo | `assets/css/components.css` | s2 v0.34.0 (git history) |
| Eyebrow format `§ Topic · Subtopic` | template-parts + `docs/PRODUCT.md` | s1 v0.10.0 (git history) |
| Footer V3 newsletter-ready | `template-parts/footer.php` | s2 v0.28.0 (git history) |
| Chi-siamo template alignment | `page-templates/lo-studio.php` | s2 v0.27.3 (git history) |
| Cross-template H2 spacing | `assets/css/sections.css` | s2 v0.27.7 (git history) |
| Pre-CTA grid alignment | `template-parts/cta.php` | s2 v0.27.8 (git history) |
| Foundation layer (CSS variables system) | `assets/css/tokens.css` + `base.css` | s2 v0.35.0 (git history) |
| Schema dignity (Yoast coabitazione) | `inc/schema/` + `docs/ARCHITECTURE.md` | s2 v0.26.0 (git history) |

## Decisioni che sono state SUPERATE dal recovery v1.0

| Cosa è cambiato | Pre-recovery | Post-recovery (v1.0) |
|---|---|---|
| Modello dati | Hardcoded nei template | ACF Field Groups (16 group) |
| `page.php` | 1274 righe monolitiche | 79 righe + 6 template-parts |
| Editing cliente | Modificare codice | Editare campi ACF in WP-Admin |
| CPT modulari (FAQ, Casi, ecc.) | Inesistenti o hardcoded | 8 CPT con fields strutturati |
| Theme Options | Hardcoded constants | ACF Theme Options (6 tab, 26 field) |

## Cosa fare se vuoi consultare un file qui

1. **Apri il file** col tuo editor markdown
2. **Tieni presente la data** del file: lo stato del sito a quel momento è diverso da oggi
3. **Verifica con il sito attuale** se la decisione documentata è ancora attiva (es. design tokens sì, struttura template no)
4. **Se cerchi un report v0.x rimosso**, è in git history: `git log --all --diff-filter=D -- .claude/knowledge/_history/design/`

---

*Maintained by orchestrator (Claude in chat). Last updated: 2026-05-05 post-cleanup `0ee9789`.*
