# Design Handoff — Studio Legale Saltelli

Bundle ricevuto da Claude Design (claude.ai/design) come prototipi JSX. Questi file sono **reference per pixel-perfect implementation**, NON codice di produzione. Il target è il custom WordPress theme `saltelli` (vedi `CLAUDE.md` root).

## Convention naming

Una cartella per template, con `index.jsx` come file canonical:

```
/design-handoff/
├── README.md                              # questo file
├── _reference/                            # design system reference + screenshots
│   ├── tokens-design-bundle.css           # tokens.css del bundle Design (NON SoT — cross-check)
│   ├── saltelli-design-bundle.css         # utility CSS del bundle Design
│   └── screenshots/                       # 4 hero stack variants (concept iterations)
├── home/                                  # → front-page.php
│   ├── index.jsx                          # desktop canonical
│   └── mobile.jsx                         # mobile variant
├── chrome/index.jsx                       # → header.php + footer.php (global chrome)
├── footer/index.jsx                       # → footer.php (specifico, alternative)
├── logo/index.jsx                         # → header.php (logo component)
├── design-system/index.jsx                # reference design tokens applied
├── chi-siamo/index.jsx                    # → template-parts/page-chi-siamo-hub.php
├── single-avvocato/index.jsx              # → single-avvocato.php
├── blog-archive/index.jsx                 # → home.php (blog hub)
├── archive-casi/index.jsx                 # → archive-saltelli_caso.php
├── glossario-legale/index.jsx             # → Page 2710 template
├── taxonomy-tipo-area/index.jsx           # → taxonomy-tipo-area.php
├── single-competenza-tier1/index.jsx      # → single-competenza.php (variante Tier-1)
├── contatti/index.jsx                     # → page-contatti template (Page 23)
└── 404/index.jsx                          # → 404.php
```

## Source of truth — chiarimento

Il bundle Design include `_reference/tokens-design-bundle.css` (298 righe) generato dal prototipo. **NON è il SoT del design system del tema.** Il SoT canonical resta `docs/DESIGN.md` (radice repo) → derivato in `wp-content/themes/saltelli/assets/css/tokens.css` (rebuilt Wave 5 STEP 2).

Discrepanze attese tra bundle e current tema (esempi):

| Token | Bundle Design | Current (Wave 5 STEP 2) |
|---|---|---|
| `--fs-display` | `clamp(48px, 8vw, 120px)` | `clamp(80px, 9vw, 132px)` |
| `--fs-h1` | `clamp(36px, 5vw, 64px)` | `clamp(48px, 6vw, 96px)` |
| Letter-spacing | 1 valore `-0.01em` | 4 ottici differenziati (`--ls-display`, `--ls-h1`, `--ls-h2`, `--ls-h3`) |
| `--lh-body` | `1.65` | `1.7` |
| Spacing scale | `--sp-1..10` | `--s-1..10` (+ alias legacy) |

**Regola applicativa**: il README del bundle Design dice esplicitamente "recreate them pixel-perfectly; don't copy the prototype's internal structure unless it happens to fit." Il **visual output del JSX** è il target. Per arrivarci, la decisione su quale token usare (bundle vs current) è di Code in fase di audit + eventualmente orchestrator review. Non risolvere ciecamente in un senso o nell'altro.

## Cosa LASCIATO FUORI dal copy

- HTML files legacy (Sessione 1/2/Footer/Hero/Logo) — storia precedente, già implementati
- `project/uploads/` (5 competitor reference + 4 screenshot work-in-progress) — inspiration, non operativo
- `project/tokens.css` root (duplicato di `styles/tokens.css`)
- `design-canvas.jsx` (showcase tooling, non operativo)

Tutti questi restano in `/Users/aldosantoro/Desktop/studiolegalesaltelli-ux-ui/` per consultazione manuale Duccio.

## Workflow operativo

Per implementare un template:

1. Orchestratore (Claude in chat) lancia mini-wave per UNA Page (es. Home)
2. Code legge `design-handoff/<page-slug>/index.jsx` integralmente
3. Code mappa ogni inline style a className BEM (`.sl-{template}__{element}`)
4. Code genera CSS rule corrispondente in `sections.css` o `components.css`
5. Code aggiorna template PHP per produrre lo STESSO markup del JSX (semplificato dove i hooks/state React non hanno equivalente PHP)
6. Code aggiunge field SCF dove necessario per content dinamico (text, image, repeater)
7. Smoke test: screenshot frontend WP vs JSX reference → 0 pixel diff su computed CSS critical properties (typography, spacing, color)

Pattern già documentato in `CLAUDE.md` § "Design → Code handoff rule (golden)".

## Asset immagini

I JSX **non referenziano file image specifici** (verificato via grep — zero match `.jpg/.png/.webp/.avif/.svg`). Le immagini servono via:

- **Background CSS** (gradient, color, pattern SVG inline) per hero/decorative
- **Placeholder component React** per ritratti (es. avvocati) — in WP diventerà `wp_get_attachment_image()` da Media Library + SCF field `image`
- **Screenshots in `_reference/screenshots/`** sono concept iterations hero homepage — usare come visual reference, NON come asset da uploadare

Quando Design fornirà asset finali (es. foto reali avvocati, hero bg image), li aggiungeremo in `design-handoff/<page>/assets/` con naming `<page-slug>__<section>__<element>-<size>.<ext>`.

## Pages senza JSX dedicato

Mancano JSX per:
- **Lo Studio** (Page 2811)
- **Aree di Pratica hub** (Page 2812)
- **Risorse hub** (Page 2813)
- **Costi e Consulenze** (Page 2695)
- **Prenota appuntamento** (Page 2714)
- **Privacy/Cookie/Note legali** (utility)
- **single-post articolo** (blog single)
- **archive-avvocato (Team)** — probabilmente coperto da pattern chi-siamo

Per queste Pages: o Design produce JSX dedicato in futuro, o applichiamo pattern condivisi da JSX simili (es. Lo Studio → adattamento di chi-siamo, Risorse → adattamento di chi-siamo, ecc.). Code raccomanderà in audit.

---

*Bundle ricevuto da Claude Design: 2026-05-11. Originale: `/Users/aldosantoro/Desktop/studiolegalesaltelli-ux-ui/`. Copia selettiva orchestratore: 15 JSX + 2 CSS reference + 4 screenshots = 21 file.*
