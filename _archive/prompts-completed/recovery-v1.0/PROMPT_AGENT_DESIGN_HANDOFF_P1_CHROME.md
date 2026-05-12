# PROMPT AGENT — Design Handoff Wave P1 · CHROME (header) verify & drift cleanup

> **Scope**: allineare `header.php` + `logo.css` + sections.css del tema al design source `design-handoff/chrome/index.jsx` + `design-handoff/logo/index.jsx`. Verifica fedeltà visual + drift CSS cleanup. **NESSUN cambio SCF.**
>
> **Branch**: `feat/design-handoff-chrome`
> **Stima**: 0.5–1h
> **Modalità**: lean, no version bump (chore frontend cleanup).
> **Sessione**: una sola Claude Code, no parallelismo.

---

## CONTESTO

Stato repo: v1.3.13-wave5-step3-coverage CUT-READY. Elena OK definitivo. Avvio sequenza 12 mini-wave Design Handoff (audit completo in `.claude/knowledge/audits/design-handoff/RECOMMENDATION.md`).

**Decisioni orchestratore acquisite** (6 punti, NON rinegoziare):
1. SoT design tokens = **`tokens.css` corrente vince** (Wave 5 STEP 2). Bundle CSS Design è obsoleto/informativo.
2. chi-siamo ≠ lo-studio (restano 2 pagine, applicabile a Wave P7, non a P1).
3. NO mappa iframe contatti (applicabile P11, non P1).
4. Single-post → request Design (backlog post-cut).
5. Hero variant **B** (cream scrim asimmetrico) — applicabile Wave P3 Home, non P1.
6. Picsum placeholder per hero ora, foto reale Wave 5.1 — applicabile P3, non P1.

**Questa wave (P1 chrome)** è la prima della sequenza: globale (header appare su ogni pagina → sblocca QA visiva di tutte le altre 14), basso rischio, validation del flow lean.

---

## ⚠️ HARD INVARIANT — SCF DATA CONTRACT PRESERVATION

La struttura SCF + flow editoriale Elena-approved (Wave 4.7.fix.4 + 4.7.fix.5 + Wave 5 STEP 3) è **data contract immutabile**. JSX Design = visual contract che si adatta, NON viceversa.

- **NON ACCETTABILE**: refactor SCF group, cambio location rules, modifiche disable-Gutenberg pattern (13 Pages SCF-only), cambio admin path, cambio CPT/taxonomy registration.
- **Per P1 chrome**: header.php NON ha metabox SCF (è chrome globale). Legge da `wp_nav_menu('primary')`, `saltelli_option('studio_telefono_pubblico')`, `bloginfo` standard. **Niente schema SCF da toccare.**

---

## PRE-FLIGHT (10 min)

1. Leggi nell'ordine:
   - `CLAUDE.md` (sezioni: Identity, Hard constraints, Design system, **"Design → Code handoff rule golden"**, Lessons learned, Workflow rules)
   - `design-handoff/README.md`
   - `.claude/knowledge/audits/design-handoff/RECOMMENDATION.md` (§A SoT KEEP CURRENT confermato, §H prompt operativo originale per riferimento, §C Risk Analysis admin-side smoke test)
   - `.claude/knowledge/audits/design-handoff/02-jsx-to-wp-mapping.md` (riga chrome — drift specifici già mappati)
   - **JSX source**:
     - `design-handoff/chrome/index.jsx` (122 righe, S2Header)
     - `design-handoff/logo/index.jsx` (varianti SLLogoHorizontal — la `md` è usata in header, `sm` su mobile)
   - **WP target**:
     - `wp-content/themes/saltelli/header.php` (143 righe)
     - `wp-content/themes/saltelli/assets/css/logo.css`
     - Blocchi rilevanti di `sections.css` (cerca `.sl-header__*`)
     - Blocchi rilevanti di `components.css` (cerca `.sl-logo__h-*`, `.sl-link`)

2. Verifica stato repo:
   ```sh
   git fetch origin
   git status                          # atteso: working tree pulito su main
   git log --oneline -3                # atteso HEAD = 5309876 merge Wave 5 STEP 4
   ```

3. Crea branch dedicato:
   ```sh
   git checkout -b feat/design-handoff-chrome
   ```

4. Conferma in chat: stato repo + 22 file design-handoff/ presenti + JSX chrome+logo letti + prosegui VERIFY.

---

## PHASE 1 — VERIFY (read-only diff, 15-20 min)

**Output obbligatorio prima di toccare codice**: tabella `JSX value | CSS attuale | match? | fix` postata in chat.

Confronta element-by-element:

### 1.A — Header shell (`.sl-header`)

| Property | JSX value | WP attuale (file:line) | Match? | Fix |
|---|---|---|---|---|
| position | `sticky` | header.php / sections.css | ? | ? |
| top | `0` | ? | ? | ? |
| z-index | `50` | ? | ? | ? |
| background | `transparent` → `var(--background)` su scroll | ? (data-scrolled toggle) | ? | ? |
| border-bottom | `transparent` → `1px var(--border)` su scroll | ? | ? | ? |
| transition | `300ms var(--ease-editorial)` | ? | ? | ? |

### 1.B — Container (`.sl-header__inner` o equivalente)

| Property | JSX value |
|---|---|
| max-width | `var(--container-max)` = 1440px |
| margin | `0 auto` |
| padding | `20px clamp(24px, 5vw, 96px)` |
| display | `grid` |
| grid-template-columns | `auto 1fr auto` |
| gap | `48px` (container), `36px` (nav interna) — DISTINGUERE |
| align-items | `center` |

### 1.C — Logo SLLogoHorizontal `size=md` (`.sl-header__brand .sl-logo--horizontal`)

Markup atteso: `.sl-logo__h-top` (line 1) + `.sl-logo__h-rule` (separator 1px×36px) + `.sl-logo__h-name` (Saltelli) con `.sl-logo__h-name-swash` (S bronze).

| Element | Property | JSX value |
|---|---|---|
| `.sl-logo--horizontal` | display | `grid` |
|   | grid-template-columns | `auto 1px auto` |
|   | gap | `24px` |
|   | align-items | `center` |
| `.sl-logo__h-top` (kicker "Studio Legale") | font-size | `10px` |
|   | font-weight | `500` |
|   | letter-spacing | `0.32em` |
|   | text-transform | `uppercase` |
| `.sl-logo__h-bot` ("Napoli · 1999") | font-size | `9px` |
|   | letter-spacing | `0.24em` |
|   | font-family | `var(--font-mono)` |
|   | color | `var(--text-muted)` |
|   | text-transform | `uppercase` |
| `.sl-logo__h-rule` | width | `1px` |
|   | height | `36px` |
|   | background | `var(--border)` o equivalente |
| `.sl-logo__h-name` (Saltelli) | font-family | `var(--font-display)` |
|   | font-style | `italic` |
|   | font-size | `32px` (md) / `24px` (sm mobile) |
|   | line-height | `1` |
|   | letter-spacing | `-0.02em` |
|   | font-weight | `400` |
| `.sl-logo__h-name-swash` (S iniziale) | color | `var(--accent)` bronze |

**Mobile `size=sm`** (breakpoint <1024):
- top: 9px / bot: 8px / rule: 28px / name: 24px

### 1.D — Nav (`.sl-header__menu`)

6 voci attese. Markup JSX = REFERENCE del menu, **gli href sono LEGACY** (`/avvocati/`, `/competenze/`, `/casi/`, `/editoriale/` → questi NON sono i slug correnti del sito).

| Property | JSX value |
|---|---|
| display | `flex` |
| gap | `36px` |
| font-size | `14px` |
| font-weight | `500` |
| `.sl-header__menu a` | tipo `.sl-link` |
| `.sl-link` | border-bottom-color: `transparent` (default) → `var(--accent)` su hover |
| text-decoration | nessuno (no underline a riposo) |

**WP**: nav generata da `wp_nav_menu('primary')` (slug correnti via menu DB). NON hardcodare gli href dal JSX. Verifica SOLO lo styling delle voci (`.sl-header__menu a`).

### 1.E — Phone (`.sl-header__phone`)

| Property | JSX value |
|---|---|
| classe | `.sl-mono` |
| font-size | `11px` (md), `11px` (sm) |
| color | `var(--primary)` |
| letter-spacing | `var(--ls-mono)` = `0.08em` |
| text-transform | `uppercase` |

**WP**: `<?php echo esc_html(saltelli_option('studio_telefono_pubblico')); ?>` (già in header.php). Verifica solo styling.

### 1.F — Mobile burger + overlay (`.sl-header__burger` + `.sl-header__mobile`)

JSX `home/mobile.jsx`: 2 linee 24px × 1px + overlay full-width voci Playfair 28px.

WP: `.sl-header__burger` (2 linee) + `.sl-header__mobile`. Verifica:
- Visibile <1024px, hidden ≥1024px
- Linee: width 24px, height 1px, background `var(--primary)`, gap tra linee 6px
- Overlay menu: background `var(--background)`, voci `font-family: var(--font-display)`, `font-size: 28px`, `font-style: italic`

### 1.G — Decisione end-of-PHASE-1

Posta in chat la tabella drift completa con classifica per riga:
- ✅ MATCH (nessun fix)
- ⚠️ DRIFT (descrivere fix specifico: token swap o phantom catalogato o new value per-selector)
- ❌ MISSING (className/rule da creare ex-novo)

**Se 0 DRIFT/MISSING**: la wave si chiude qui (no implementation). Commit message "no drift found, header già allineato a JSX Design".

**Se DRIFT/MISSING > 0**: prosegui PHASE 2.

---

## PHASE 2 — IMPLEMENT (solo se drift trovati, 15-25 min)

Per ogni riga DRIFT/MISSING della tabella PHASE 1:

1. **Token alignment rule** (decisione orchestratore §A — KEEP CURRENT tokens.css):
   - Se valore JSX matcha un token in `tokens.css` → sostituisci con `var(--token)`
   - Se è un phantom catalogato in `.claude/knowledge/audits/wave5-step4-sections-cleanup/02-phantom-values-remaining.md` → segui il piano lì proposto
   - Se è genuinamente nuovo (non in current né in phantom doc) → decidi per-selector, **MAI toccare `:root` di tokens.css**

2. **BEM className mapping** (golden rule CLAUDE.md):
   - Ogni inline style del JSX = 1 CSS rule + 1 className BEM
   - Se className manca: crearla (`.sl-header__<element>` o `.sl-logo__<element>`)
   - Aggiungere CSS rule nello scope appropriato:
     - `assets/css/logo.css` per `.sl-logo*`
     - `assets/css/sections.css` per `.sl-header*` (con scope marker `/* === design-handoff chrome === */`)
     - `assets/css/components.css` se è una utility riusabile (`.sl-link`, `.sl-mono`)

3. **PHP refactor** (se serve):
   - `header.php` minimal change: se markup atteso dal JSX differisce, allinearlo (senza rompere `wp_nav_menu` e `saltelli_option` calls)
   - **NON cambiare la nav source**: resta `wp_nav_menu('primary')`, gli href JSX sono solo reference visual
   - **NON cambiare** la lettura `studio_telefono_pubblico`

4. **Sync staging** dopo ogni edit non-triviale:
   ```sh
   rsync -avz wp-content/themes/saltelli/assets/css/ deploy@178.62.207.50:/var/www/saltelli/wp-content/themes/saltelli/assets/css/
   rsync -avz wp-content/themes/saltelli/header.php deploy@178.62.207.50:/var/www/saltelli/wp-content/themes/saltelli/header.php
   ssh deploy@178.62.207.50 "sudo systemctl reload php8.2-fpm && cd /var/www/saltelli && sudo -u www-data wp cache flush --path=/var/www/saltelli"
   ```
   (OPcache reload obbligatorio post-edit `header.php` — lesson Wave 4.7.fix.3).

---

## PHASE 3 — SMOKE TEST (10-15 min)

### 3.A — Frontend curl smoke (3 URL)

```sh
for URL in / /chi-siamo/ /contatti/; do
  echo "=== $URL ==="
  curl -s "https://staging.studiolegalesaltelli.it$URL" | grep -cE 'sl-header|sl-logo'
  # atteso: count > 0 (markup header presente su ogni pagina, è chrome globale)
done
```

### 3.B — getComputedStyle spot-check (Playwright one-off)

Crea uno script Node temporaneo `/tmp/p1-chrome-check.mjs` (oppure usa `npx playwright codegen` se preferito), apre staging, estrae computed styles di:

- `.sl-header` (position, z-index, background, border-bottom)
- `.sl-header[data-scrolled="true"]` (dopo scroll() programmatico — background change)
- `.sl-header__brand .sl-logo__h-name` (font-size, letter-spacing, line-height, font-style, font-family)
- `.sl-header__brand .sl-logo__h-top` (font-size, letter-spacing, text-transform)
- `.sl-header__brand .sl-logo__h-bot` (font-size, font-family, color)
- `.sl-header__brand .sl-logo__h-name-swash` (color → atteso `rgb(184, 134, 11)` = `var(--accent)`)
- `.sl-header__menu a` (font-size, font-weight, border-bottom-color)
- `.sl-header__phone` (font-size, color, letter-spacing)

Confronta con tabella JSX di PHASE 1. Diff = 0 atteso.

### 3.C — Cross-breakpoint visual smoke (375 / 768 / 1024 / 1440)

```sh
# Screenshot Playwright per 4 breakpoint, salva in /tmp/p1-chrome-bp-{NN}.png
# Verifica visiva:
# - <1024px: burger visibile, .sl-header__menu nascosta
# - ≥1024px: nav inline visibile, burger nascosto
# - mobile: SLLogoHorizontal size=sm (font-size name 24px), padding ridotto
```

### 3.D — Admin-side smoke (lesson Wave 4.7.fix.4)

Nessun metabox SCF su header.php, ma:
- WP Admin → seleziona 1 Page SCF-only random (es. Chi Siamo 2822) → Modifica → verifica che il logo nell'admin bar / header (se renderizzato) sia visibile e non regredito
- Verifica che nessuna Page abbia perso il pattern Gutenberg-disabled per side effect (atteso: 13 IDs in `SALTELLI_SCF_ONLY_PAGES` invariati)

---

## PHASE 4 — COMMIT + PUSH

```sh
git add -A
git diff --cached --stat

git commit -m "feat(design-handoff): Wave P1 chrome — header verify + drift cleanup

Wave 1/12 della sequenza Design Handoff (post-RECOMMENDATION.md). Confronto
elemento-per-elemento header.php + logo.css + sections.css blocco .sl-header*
vs design-handoff/chrome/index.jsx + design-handoff/logo/index.jsx.

VERIFY tabella drift completa in chat orchestratore.
Drift trovati: <N>.
Drift fixati: <N>.

Token alignment rule applicato (KEEP CURRENT tokens.css — decisione orchestratore §A RECOMMENDATION).
Phantom values: <0 oppure lista cross-ref con 02-phantom-values-remaining.md>.
New values per-selector (se presenti): <lista>.

CSS changes:
- assets/css/sections.css: <N> CSS rule modified/added, scope marker /* === design-handoff chrome === */
- assets/css/logo.css: <N> changes
- assets/css/components.css: <N> changes (se utility .sl-link/.sl-mono toccate)

PHP changes:
- header.php: <descrivere se markup classNames added/modified, oppure 'invariato'>

Smoke test:
- Frontend curl 3 URL: markup .sl-header presente
- getComputedStyle 8 selettori: diff 0 vs JSX
- 4 breakpoint: burger/nav toggle corretto
- Admin-side: 13 Pages SCF-only pattern invariato

No version bump (chore frontend, no schema/data change).
Branch: feat/design-handoff-chrome · <N> file changed · +XX/-YY"

git push origin feat/design-handoff-chrome
```

---

## OUTPUT FINALE in chat

- Tabella drift PHASE 1 (compatta, max 30 righe)
- File modified count + lines diff
- Smoke test risultati (3.A frontend, 3.B getComputedStyle, 3.C breakpoint, 3.D admin)
- Eventuali deviazioni dal piano (decisioni autonome con motivazione)
- SHA commit pushato
- ETA proposto P2 footer

---

## HARD RULES

1. **Read-only PHASE 1**. NIENTE modifiche al codice durante verify.
2. **Token alignment regola §A**: KEEP CURRENT, mai toccare `:root` di tokens.css.
3. **SCF data contract immutabile**: per P1 non si tocca SCF (header.php non ha metabox), ma verifica che nessuna regression accidentale impatti `wp_nav_menu` o `saltelli_option` reads.
4. **OPcache reload** post-edit `header.php` (lesson Wave 4.7.fix.3).
5. **Admin-side smoke test** obbligatorio (lesson Wave 4.7.fix.4) — anche se chrome non ha metabox SCF, verifica regression su 1 Page SCF-only random.
6. **NO new dependencies**, NO design tokens edit, NO schema markup changes, NO redirect changes.
7. **Branch policy**: branch dedicato `feat/design-handoff-chrome`, push origin, NO merge a main (orchestratore audita e mergia).
8. **One-writer-at-a-time**: questa è UNICA sessione Code attiva. Orchestratore (chat Claude.ai) fermo sui commit del repo finché non pushi.

---

## DECISIONE AUTONOMA AUTORIZZATA

- Naming nuovi className BEM se servono: `.sl-header__<element>` o `.sl-logo__<element>` (consistency progetto).
- Wording scope marker CSS: `/* === design-handoff chrome === */` o equivalente.
- Decisione fix per-selector dove valore JSX è genuinamente nuovo (non in current né phantom doc): scegli + motiva in commit message.
- Skip PHASE 2 se 0 drift trovati in PHASE 1 (chiusura wave veloce, commit "no drift found").

---

## TONO

Direct, concrete, zero filler. Stile commit usato dal progetto.

---

*Wave P1/12 sequenza Design Handoff. Prossima: P2 footer. Pattern lean = 1 wave alla volta su main, audit orchestratore post-push, no version bump (chore frontend).*
