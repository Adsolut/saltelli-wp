# PROMPT AGENT — Design Handoff Wave P12 · 404 verify & minimal drift (ULTIMA)

> **Scope**: verificare `404.php` + `.sl-404*` di sections.css vs `design-handoff/404/index.jsx`. 2 fix CSS computed-neutral. Wave **ULTIMA della sequenza Design Handoff** (12/12).
>
> **Branch**: `feat/design-handoff-404`
> **Stima**: 0.5h (severity 🟢 LIGHT)
> **Modalità**: lean, no version bump (chore frontend)
> **Sessione**: una sola Claude Code.

---

## CONTESTO

Wave 12/12 sequenza Design Handoff. P1-P11 mergeate o skipped, version corrente attesa 1.3.16 (post-P9 archive-casi). **Ultima wave**.

**Decisioni orchestratore acquisite** (NON rinegoziare):
1. SoT design tokens = `tokens.css` corrente vince
2. SCF: zero (404 è template fisso WP, niente custom field)
3. **2 fix CSS oggi**:
   - `.sl-404__lede` 22px literal → `var(--fs-lede)` (lede stilistico)
   - `.sl-404__article-title` 22px literal → `var(--fs-h3-floor)` (heading h3)
4. **Phantom defer Wave 5 STEP 5** (NON fixare ora):
   - `.sl-404__title` `clamp(72px, 9vw, 140px)` (display-band one-off)
   - `.sl-404__dropcap` 72px (decorative)
   - `.sl-404__card-title` 28px (candidate `--fs-h2-floor`)
   - `.sl-404__lede-prose` 19px (intermediate prose-band)
   - `.sl-404__cta-title` `clamp(56px, 6.5vw, 96px)` (display-band one-off)
5. 404.php già toccato in 2 chore precedenti (count aree dinamico Wave 4.7.fix.5 + breadcrumb cluster + helper `saltelli_aree_hub_url()`)

**Pre-flight orchestratore già fatto**:

| Voce | Valore |
|---|---|
| JSX sezioni mappate | 5/5 (hero + recovery 3-col + "Forse cercavi" 6 aree + articoli recenti 3 + CTA finale) |
| Drift CSS | 7 phantom totali (2 fix oggi, 5 defer) |
| Severity | 🟢 LIGHT, ETA 0.45h |
| SCF | 🟢 zero (template WP fisso) |
| Righe CSS attese | 2 |

---

## ⚠️ HARD INVARIANT

1. **NO SCF**: 404 è template fisso WP, niente custom field.
2. **NO touch `404.php`**: già toccato in 2 chore Wave 4.7.fix.5 + pre-cut polish + saltelli_aree_hub_url helper. Solo CSS oggi.
3. **Phantom defer Wave 5 STEP 5**: NON tokenize ora.
4. **`saltelli_aree_hub_url()` helper**: già esistente, NO refactor.

---

## PRE-FLIGHT (3 min)

1. Leggi:
   - `CLAUDE.md` (Hard constraints, Lessons learned)
   - `.claude/knowledge/audits/design-handoff/RECOMMENDATION.md` (§J 404, §B P12)
   - **JSX source**: `design-handoff/404/index.jsx`
   - **WP target**:
     - `wp-content/themes/saltelli/404.php`
     - Blocco `.sl-404*` in sections.css (~righe 5857-6110)

2. Verifica stato:
   ```sh
   git fetch origin
   git status
   git log --oneline -3   # atteso HEAD post-P8 merge
   git checkout -b feat/design-handoff-404
   ```

3. Conferma in chat + prosegui.

---

## PHASE 1 — VERIFY (5 min)

### 1.A — 2 fix CSS oggi

| Selector | Line | Property | Current | Fix | Reason |
|---|---|---|---|---|---|
| `.sl-404__lede` | ~5883 | font-size | `22px` literal | `var(--fs-lede)` | Lede stilistico (token esistente 22px) |
| `.sl-404__article-title` | ~6076 | font-size | `22px` literal | `var(--fs-h3-floor)` | Heading h3 dei 3 articoli recenti (token esistente 22px) |

### 1.B — Phantom defer (5 noti, NON fixare)

- `.sl-404__title` `clamp(72px, 9vw, 140px)` — display-band orphan, defer (no token equivalent)
- `.sl-404__dropcap` 72px (×2 occurrences) — decorative, defer
- `.sl-404__card-title` 28px — candidate `--fs-h2-floor` Wave 5 STEP 5
- `.sl-404__lede-prose` 19px — intermediate prose-band, defer
- `.sl-404__cta-title` `clamp(56px, 6.5vw, 96px)` — display-band CTA orphan, defer

### 1.C — Verify chore precedenti integri

```sh
grep -n "saltelli_aree_hub_url\|wp_count_posts.*competenza\|breadcrumb_chain" wp-content/themes/saltelli/404.php
# Atteso: helper saltelli_aree_hub_url() usato, wp_count_posts() per "Tutte le N aree" dinamico, breadcrumb cluster intermediate
```

NO touch a 404.php. Solo CSS.

---

## PHASE 2 — IMPLEMENT (5 min)

Modifica `.sl-404__lede` e `.sl-404__article-title` in sections.css. Scope marker `/* === design-handoff 404 P12 (FINAL) === */`:

```css
/* === design-handoff 404 P12 (FINAL) === */

.sl-404__lede {
  font-size: var(--fs-lede); /* was 22px literal — token alignment lede stilistico */
}

.sl-404__article-title {
  font-size: var(--fs-h3-floor); /* was 22px literal — heading h3 articoli recenti */
}
```

Sync staging:
```sh
rsync -avz wp-content/themes/saltelli/assets/css/ deploy@178.62.207.50:/var/www/saltelli/wp-content/themes/saltelli/assets/css/
ssh deploy@178.62.207.50 "cd /var/www/saltelli && sudo -u www-data wp cache flush --path=/var/www/saltelli"
```

No PHP changes = no OPcache reload.

---

## PHASE 3 — SMOKE TEST (10 min)

### 3.A — Frontend curl smoke

```sh
echo "=== 404 trigger (URL inesistente) ==="
curl -sI "https://staging.studiolegalesaltelli.it/url-inesistente-abc-xyz/" | head -3
# Atteso: HTTP/2 404

echo "=== 404 markup 5 sezioni ==="
curl -s "https://staging.studiolegalesaltelli.it/url-inesistente-abc-xyz/" | grep -cE "sl-404__title|sl-404__recovery|sl-404__forse|sl-404__article|sl-404__cta"
# Atteso: count >= 5

echo "=== Tutte le N aree dinamico (wp_count_posts) ==="
curl -s "https://staging.studiolegalesaltelli.it/url-inesistente-abc-xyz/" | grep -oE "Tutte le [0-9]+ aree" | head -1
# Atteso: "Tutte le 19 aree" (count dinamico Wave 4.7.fix.5)

echo "=== helper saltelli_aree_hub_url href ==="
curl -s "https://staging.studiolegalesaltelli.it/url-inesistente-abc-xyz/" | grep -oE 'href="/aree-di-pratica/?"' | head -1
# Atteso: href presente (chore fix single-competenza frontend regression)
```

### 3.B — getComputedStyle spot-check

Su URL 404:
- `.sl-404__lede` font-size atteso `22px` (computed da `var(--fs-lede)`)
- `.sl-404__article-title` font-size atteso `22px` (computed da `var(--fs-h3-floor)`)

### 3.C — Visual breakpoint (375/768/1024/1440)

- 5 sezioni renderizzate correttamente su tutti breakpoint
- Drop-cap "L" hero visibile (72px decorative, defer phantom)
- 3-col recovery cards layout (mobile 1col, desktop 3col)
- "Forse cercavi" 6 aree visibili
- Articoli recenti 3 (con featured image fallback se mancante)
- CTA finale

---

## PHASE 4 — COMMIT + PUSH

```sh
git add -A
git diff --cached --stat

git commit -m "feat(design-handoff): Wave P12 404 — typography token alignment (FINAL WAVE 12/12)

Wave 12/12 sequenza Design Handoff. ULTIMA wave. Template 404.php 95% già allineato (chore precedenti: count aree dinamico Wave 4.7.fix.5 + breadcrumb cluster + saltelli_aree_hub_url helper).

2 fix CSS computed-neutral (sections.css scope /* === design-handoff 404 P12 (FINAL) === */):
- .sl-404__lede font-size: 22px → var(--fs-lede) [lede stilistico]
- .sl-404__article-title font-size: 22px → var(--fs-h3-floor) [heading h3 articoli recenti]

Defer Wave 5 STEP 5 pool promotion:
- .sl-404__title clamp(72,9vw,140) [display-band one-off]
- .sl-404__dropcap 72px ×2 [decorative]
- .sl-404__card-title 28px [candidate --fs-h2-floor]
- .sl-404__lede-prose 19px [intermediate prose-band]
- .sl-404__cta-title clamp(56,6.5vw,96) [display-band CTA orphan]

SCF: zero (404 template WP fisso).
PHP: invariato (chore Wave 4.7.fix.5 + pre-cut polish + saltelli_aree_hub_url helper integri).

Smoke test:
- Frontend curl 404 trigger: 5 sezioni markup presente
- 'Tutte le 19 aree' dinamico (wp_count_posts) integro
- href /aree-di-pratica/ via helper integro
- getComputedStyle: lede + article-title 22px (token-resolved)

🎉 SEQUENZA DESIGN HANDOFF COMPLETATA: 12/12 wave (8 merge + 2 skipped 0 drift + 1 consolidamento + 1 final).

No version bump (chore frontend computed-neutral).
Branch: feat/design-handoff-404 · 1 file changed · 2 righe CSS"

git push origin feat/design-handoff-404
```

---

## OUTPUT FINALE in chat

- Tabella PHASE 1 (2 fix + 5 defer documentati)
- 2 fix CSS applicati
- Smoke test risultati
- SHA commit pushato
- **Annuncio sequenza Design Handoff 12/12 COMPLETATA**
- Memo orchestratore: post-cut backlog (Wave 5 STEP 5 phantom resolution + Wave 5.1 Image Expansion + single-post JSX request Design)

---

## HARD RULES

1. **2 fix lede + article-title only**: NIENTE altri phantom tokenize.
2. **NO touch 404.php**: chore precedenti integri.
3. **NO SCF**: template WP fisso.
4. **Token alignment §A**: KEEP CURRENT, mai toccare `:root`.
5. **One-writer-at-a-time**.

---

## DECISIONE AUTONOMA AUTORIZZATA

- Scope marker CSS: `/* === design-handoff 404 P12 (FINAL) === */`
- Se trovi drift inatteso oltre i 7 noti: documenta, NO fixare (defer Wave 5 STEP 5).

---

## TONO

Direct, concrete, zero filler. Stile commit progetto.

---

*Wave P12/12 sequenza Design Handoff — ULTIMA. Post-completamento sequenza: orchestratore farà batch CLAUDE.md update + report finale + decide next backlog (Wave 5 STEP 5 phantom resolution + Wave 5.1 Image Expansion + single-post JSX request).*
