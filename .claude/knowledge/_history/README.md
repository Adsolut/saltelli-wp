# .claude/knowledge/_history/ — Storia design pre-recovery

> ⚠️ **Questa cartella è informativa, NON operativa.**
> Per le decisioni di design correnti vai a [`/docs/DESIGN.md`](../../../docs/DESIGN.md) e [`/docs/PRODUCT.md`](../../../docs/PRODUCT.md).

## Cosa c'è qui

Documenta **62 file** che tracciano l'evoluzione del design del sito Saltelli attraverso **due sessioni di design** condotte in chat (Claude.ai), prima del recovery v1.0 che ha riscritto l'architettura CMS.

### `design/sessione-1/` (25 file)

Prima sessione: **scaffolding del design system**. Da v0.0 a v0.13.x circa. Coperti:
- Setup iniziale: design canvas, design system, homepage desktop+mobile (JSX prototype)
- Audit alignment + visual walkthrough deep
- Editorial refinement (v0.10.0)
- Layout harmonization (v0.12.0)
- IA unification (v0.13.0)
- Final polish (v0.11.0)
- Recovery v0.9 (preliminare)
- Pain points refinement
- Single-avvocato placeholder fix
- Content migration (parziale)
- Template polish

### `design/sessione-2/` (37 file)

Seconda sessione: **iterazioni pixel-perfect + foundation**. Da v0.19.0 a v0.35.1 circa. Coperti:
- IMPECCABLE refinement (v0.19.0, v0.20.0)
- Carryover refinement (v0.20.1)
- Footer V2/V3 (v0.20.2, v0.21.3, v0.21.5, v0.28.0)
- Performance optimization (v0.21.0, v0.21.1, v0.21.2)
- Typography breathing (v0.21.23)
- Legal pages (v0.21.4)
- Animations (v0.22.0, v0.22.1)
- Pixel-perfect final (v0.23.0, v0.24.0)
- Audit post-deploy (v0.25.0)
- Content deepening (v0.25.0)
- Schema dignity (v0.26.0)
- Visual pixel-perfect (v0.27.0, v0.27.1, v0.27.2, v0.27.7, v0.27.8)
- Container alignment fix (v0.27.2)
- Chi-siamo JSX align (v0.27.3)
- Unified layout cross-page (v0.28.4)
- Style mining (v0.31.0)
- Foundation layer (v0.35.0, v0.35.1)
- Wave 3 task reports (task 05-10)

### `design/sessione-2/.normalized/`

Schema mapping normalizzato (1 file).

## Perché questa cartella esiste

Le due sessioni di design hanno prodotto **decisioni di brand identity, design tokens, IA, copy editoriale** che sono ancora attivi nel sito attuale, anche se il recovery v1.0 (Maggio 2026) ha riscritto l'**architettura CMS** (template + ACF + content model).

Le decisioni *visuali* sono ancora valide. Le decisioni *strutturali* sono state superate dal recovery (vedi `.claude/knowledge/recovery/` per il modello dati corrente).

## Decisioni chiave da queste sessioni (ancora attive)

Per chi non vuole leggere 62 file, ecco l'essenza:

| Decisione | Sessione | File di riferimento |
|---|---|---|
| Palette navy/cream/bronze (`#1B2B4B`, `#FAFAF8`, `#B8860B`) | s1 v0.5–v0.8 | `sessione-1/design-system.jsx` |
| Typography: Playfair Display (display+H), DM Sans (body), JetBrains Mono (eyebrow) | s1 + s2 v0.21.23 | `sessione-2/v0.21.23-TYPO-BREATHING-REPORT.md` |
| Layout containers `--sl-w-text` 720px / `--sl-w-content` 1100px | s2 v0.27.2 | `sessione-2/v0.27.2-CONTAINER-ALIGN-FIX.md` |
| Drop-cap automatico sul primo paragrafo | s2 v0.34.0 | `sessione-2/v0.34.0-DROPCAP-FAQ.md` |
| Eyebrow format `§ Topic · Subtopic` | s1 v0.10.0 | `sessione-1/reports/editorial-refinement-v0.10.0/REPORT.md` |
| Footer V3 newsletter-ready | s2 v0.28.0 | `sessione-2/v0.28.0-FOOTER-NEWSLETTER-V3.md` |
| Chi-siamo template JSX alignment | s2 v0.27.3 | `sessione-2/v0.27.3-CHI-SIAMO-JSX-ALIGN.md` |
| Cross-template H2 spacing | s2 v0.27.7 | `sessione-2/v0.27.7-H2-SPACING-CROSS-TEMPLATE.md` |
| Pre-CTA grid alignment | s2 v0.27.8 | `sessione-2/v0.27.8-PRECTA-GRID-ALIGN.md` |
| Foundation layer (CSS variables system) | s2 v0.35.0 | `sessione-2/v0.35.0-FOUNDATION-LAYER.md` |
| Schema dignity (Yoast coabitazione) | s2 v0.26.0 | `sessione-2/v0.26.0-SCHEMA-DIGNITY.md` |

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

---

*Maintained by orchestrator (Claude in chat). Last updated: 2026-05-05.*
