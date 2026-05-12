# PROMPT AGENT — Design Handoff Wave P8 · blog-archive verify & minimal drift

> **Scope**: verificare `home.php` (blog archive `/risorse/blog/`) + `.sl-blog2*` di sections.css vs `design-handoff/blog-archive/index.jsx`. **98% già allineato**. 2-3 fix CSS computed-neutral. Niente SCF. QA pagination su 326 post.
>
> **Branch**: `feat/design-handoff-blog-archive`
> **Stima**: 4-5h totali (1h fix CSS + 3-4h QA pagination + visual breakpoint test)
> **Modalità**: lean, no version bump
> **Sessione**: una sola Claude Code.

---

## CONTESTO

Wave 8/12 sequenza Design Handoff. Sequenza C→B→A: P10 + P9 mergeate prima di questa.

**Decisioni orchestratore acquisite** (NON rinegoziare):
1. SoT design tokens = `tokens.css` corrente vince
2. SCF data contract: blog usa **WP-native** (post + category + tag, niente SCF custom per i 326 post). 🟢 COMPLIANT 0 additive.
3. **2 typography fix**: `-0.015em` letter-spacing + `1.55` line-height — entrambi phantom catalogati Wave 5 STEP 4. **Resolve per-selector now**.
4. Altri 4 phantom **defer Wave 5 STEP 5**: 17px form, 28px h2-floor, transition durations, hover states.
5. **Featured image 16:9 + card 4:3 + zoom hover 1.03**: già allineati in CSS (sections.css L4928, L5040, L5063).

**Pre-flight orchestratore già fatto**:

| Voce | Valore |
|---|---|
| JSX ↔ WP parity | **98%** già allineato |
| Drift CSS | 6 totali (2 fix oggi + 4 defer) |
| Drift principali oggi | 2 typography: `-0.015em` (featured + card title), `1.55` (card excerpt) |
| Severity | 🟢 LIGHT-MEDIUM |
| ETA | 4-5h (1h fix + 3-4h QA pagination 326 post + visual breakpoint) |
| SCF | 🟢 WP-native, zero touch |
| Righe CSS attese | 3-6 |

---

## ⚠️ HARD INVARIANT

1. **NO SCF additive**: blog è WP-native (post + category + tag), niente custom field per i 326 post.
2. **NO refactor `home.php` template**: solo CSS drift cleanup.
3. **NO touch CPT registration** post, tassonomie category/tag, Page 1413 Blog hub.
4. **Pagination preserved**: 12 posts/page default WP_Query, 326 posts → 28 pagine totali.

---

## PRE-FLIGHT (5 min)

1. Leggi:
   - `CLAUDE.md` (Hard constraints, Design system, "Design → Code handoff rule golden")
   - `.claude/knowledge/audits/design-handoff/RECOMMENDATION.md` (§J blog-archive, §B P8)
   - **JSX source**: `design-handoff/blog-archive/index.jsx`
   - **WP target**:
     - `wp-content/themes/saltelli/home.php`
     - Blocchi `.sl-blog2*` in sections.css (~L4800-5141)
   - `.claude/knowledge/audits/wave5-step4-sections-cleanup/02-phantom-values-remaining.md` (phantom §4 + §5 cross-ref)

2. Verifica stato:
   ```sh
   git fetch origin
   git status
   git log --oneline -3   # atteso HEAD post-P9 merge
   git checkout -b feat/design-handoff-blog-archive
   ```

3. Conferma in chat + prosegui.

---

## PHASE 1 — VERIFY (15 min)

### 1.A — Drift CSS fix (2 oggi)

| Selettore | Property | Current | Fix | Reason |
|---|---|---|---|---|
| `.sl-blog2__featured-title` | letter-spacing | `-0.015em` literal o `var(--ls-h1)` | **decidi per-selector** (vedi 1.B) | Phantom §4 |
| `.sl-blog2__card-title` | letter-spacing | `-0.015em` literal | **decidi per-selector** | Phantom §4 |
| `.sl-blog2__card-excerpt` | line-height | `1.55` literal | **decidi per-selector** | Phantom §5 |

### 1.B — Decisione letter-spacing `-0.015em`

Phantom doc §4 propone 2 path:
- **Path A**: normalize a `var(--ls-h1)` (-0.02em) — più tight, allinea featured/card title agli H1 standard
- **Path B**: expose `--ls-h2-tight: -0.015em` in tokens.css — nuovo token

**Decisione orchestratore**: **Path A** (normalize a `var(--ls-h1)`). Motivo: lean, evita token-creep, computed -0.02em vs -0.015em ≈ 0.5px diff su 28-48px font (invisibile). Se Design contesta visivamente in QA → revert literal `-0.015em` per-selector.

### 1.C — Decisione line-height `1.55`

Phantom doc §5 propone:
- **Path A**: fold into `var(--lh-body)` (1.7) — più generoso, legal density
- **Path B**: expose `--lh-prose: 1.55` — nuovo token

**Decisione orchestratore**: **literal `1.55`** (Path C, deferred token). Motivo: card excerpt è prose-band intermedio tra lede (1.5) e body (1.7), 1.55 è valore intentional Design. Tokenize Wave 5 STEP 5 quando si pool-promote prose-band. **Per ora literal, no change**.

### 1.D — Altri 4 phantom (defer)

NON fixare:
- `.sl-blog2__featured-media` hover filter grayscale off (matches JSX, OK)
- `.sl-blog2__card-media` transition `600ms` (matches JSX, OK)
- Spacing `--s-6: 48px` (matches scale, OK)
- Pagination eyebrow spacing (matches mobile→desktop ratio, OK)

### 1.E — SCF reads verify (atteso 100% WP-native)

Grep `home.php` per `saltelli_field`, `get_field`, `saltelli_option`. Atteso: solo letture WP-native (`get_permalink`, `get_the_category`, `get_the_date`, `get_the_author_meta`, `wp_trim_words`, `has_post_thumbnail`, `get_the_post_thumbnail_url`).

Conferma 0 SCF custom in `home.php`. Se trovi reads inattesi (es. `saltelli_field('byline_extended')`), documenta — atteso unico in `single.php` (linked avvocato CPT), NON in home.php.

### 1.F — Output PHASE 1

Posta in chat:
- 2 fix CSS (letter-spacing → token, line-height literal stays)
- 4 phantom defer
- SCF verify 🟢

---

## PHASE 2 — IMPLEMENT (15-20 min)

### 2.A — CSS fix in sections.css

Scope marker `/* === design-handoff blog-archive P8 === */`:

```css
/* === design-handoff blog-archive P8 === */

.sl-blog2__featured-title {
  letter-spacing: var(--ls-h1); /* was -0.015em literal — normalize phantom §4 (computed -0.005em diff invisibile) */
}

.sl-blog2__card-title {
  letter-spacing: var(--ls-h1); /* idem phantom §4 normalize */
}

/* .sl-blog2__card-excerpt line-height 1.55 — literal LEAVE, defer Wave 5 STEP 5 pool promotion --lh-prose */
```

### 2.B — Sync staging

```sh
rsync -avz wp-content/themes/saltelli/assets/css/ deploy@178.62.207.50:/var/www/saltelli/wp-content/themes/saltelli/assets/css/
ssh deploy@178.62.207.50 "cd /var/www/saltelli && sudo -u www-data wp cache flush --path=/var/www/saltelli"
```

No PHP changes = no OPcache reload.

---

## PHASE 3 — SMOKE TEST + QA PAGINATION (3-4h)

### 3.A — Frontend curl smoke (5 URL)

```sh
echo "=== /risorse/blog/ HTTP ===" && curl -sI "https://staging.studiolegalesaltelli.it/risorse/blog/" | head -3

echo "=== featured + 11 cards (pagina 1 atteso 12 post) ==="
curl -s "https://staging.studiolegalesaltelli.it/risorse/blog/" | grep -cE "sl-blog2__featured|sl-blog2__card"
# atteso count >= 12 (1 featured + 11 card su prima pagina)

echo "=== pagination link a /page/2/ ==="
curl -s "https://staging.studiolegalesaltelli.it/risorse/blog/" | grep -c "/page/2/"
# atteso count >= 1
```

### 3.B — QA pagination 326 post (cruciale per P8)

```sh
echo "=== verifica pagina 2 ==="
curl -sI "https://staging.studiolegalesaltelli.it/risorse/blog/page/2/" | head -3

echo "=== pagina middle (es. 14) ==="
curl -sI "https://staging.studiolegalesaltelli.it/risorse/blog/page/14/" | head -3

echo "=== pagina ultima (28) ==="
curl -sI "https://staging.studiolegalesaltelli.it/risorse/blog/page/28/" | head -3

echo "=== pagina out-of-range (29) ==="
curl -sI "https://staging.studiolegalesaltelli.it/risorse/blog/page/29/" | head -3
# atteso: 404
```

### 3.C — Visual breakpoint test (375/768/1024/1440)

Apri browser staging:
- **375 mobile**: featured + card 1 col, hero stack, h1 ≈ 72px
- **768 tablet**: card 2 col, hero side-by-side iniziale
- **1024 desktop**: featured 8fr/4fr, card 3 col, h1 ≈ 92px (9vw)
- **1440 wide**: card 3 col, featured grande, h1 ≈ 130px

Verifica:
- `getComputedStyle('.sl-blog2__featured-title')` letter-spacing → `-0.02em` (era `-0.015em`)
- `getComputedStyle('.sl-blog2__card-title')` letter-spacing → `-0.02em`
- `getComputedStyle('.sl-blog2__card-excerpt')` line-height → `1.55` (LEAVE)

### 3.D — Category tabs (7 hardcode in JSX)

JSX hardcoda 7 tab categoria. WP `home.php` probabilmente query top-7 by count. Verifica:
- 7 tab visibili in nav blog
- Click tab "Diritto del lavoro" → query string `?cat=N` → solo post categoria

Se WP usa query dinamica e categorie NON match JSX hardcode → documenta in commit, NO refactor (decisione defer post-cut, è feature blog dynamic).

### 3.E — Admin smoke (lesson Wave 4.7.fix.4)

WP Admin → Articoli → lista 326 post:
- Edit 1 post a caso → metadata visibili (categoria, tag, featured image, autore)
- Page 1413 Blog hub: visibile in Pagine, post_content vuoto, Gutenberg standard (NON SCF-only)
- Save un post di test → frontend blog si aggiorna

---

## PHASE 4 — COMMIT + PUSH

```sh
git add -A
git diff --cached --stat

git commit -m "feat(design-handoff): Wave P8 blog-archive — typography token alignment (computed-neutral)

Wave 8/12 sequenza Design Handoff. Template home.php (blog archive /risorse/blog/) 98% già allineato a JSX design-handoff/blog-archive/index.jsx (verify confermato 326 post WP-native, featured 16:9 + card 4:3 + zoom hover, eyebrow + meta mono).

2 fix CSS computed-neutral (sections.css scope /* === design-handoff blog-archive P8 === */):
- .sl-blog2__featured-title letter-spacing: -0.015em → var(--ls-h1) [phantom §4 normalize, computed -0.005em diff invisible]
- .sl-blog2__card-title letter-spacing: -0.015em → var(--ls-h1) [idem phantom §4]

Defer Wave 5 STEP 5 pool promotion:
- .sl-blog2__card-excerpt line-height 1.55 literal (candidate --lh-prose, prose-band intermedio)
- Altri 4 phantom (17px form, 28px h2-floor, transition 600ms, spacing scale): out of scope P8

SCF: 🟢 WP-native (zero touch). 326 post + tassonomie category/tag invariate.
Pagination QA: 28 pagine totali (326 post / 12 per page), out-of-range 29 → 404 corretto.

Smoke test:
- Frontend curl: featured + 11 card pagina 1, pagination 1-28 OK
- 4 breakpoint visual: featured/card grid responsive corretto
- getComputedStyle: featured-title + card-title letter-spacing -0.02em
- Admin: 326 post WP-native invariati

No version bump (chore frontend computed-neutral).
Branch: feat/design-handoff-blog-archive · 1 file changed · 2 righe CSS"

git push origin feat/design-handoff-blog-archive
```

---

## OUTPUT FINALE in chat

- Tabella PHASE 1 (2 fix + defer documentati)
- 2 fix applicati
- QA pagination 28 pages + out-of-range 404
- Visual breakpoint 4 OK
- SHA commit pushato
- ETA proposto P11 contatti (già scritto, ready)

---

## HARD RULES

1. **2 fix letter-spacing only**: NIENTE altri phantom tokenize. Defer Wave 5 STEP 5.
2. **`.sl-blog2__card-excerpt` line-height 1.55**: LEAVE literal (defer pool promotion).
3. **NO SCF additive**: blog WP-native.
4. **NO touch home.php template**: solo CSS.
5. **Token alignment §A**: KEEP CURRENT, mai toccare `:root`.
6. **One-writer-at-a-time**.

---

## DECISIONE AUTONOMA AUTORIZZATA

- Path A letter-spacing (normalize a `var(--ls-h1)`): confermato. Se QA visivo dimostra drift visibile, fai revert literal `-0.015em` per-selector + documenta in commit.
- Scope marker CSS: `/* === design-handoff blog-archive P8 === */`
- Category tabs hardcode vs dynamic: out of scope P8 (defer post-cut).
- Eventuale phantom collateral catch: segnala, NO fixare (defer Wave 5 STEP 5).

---

## TONO

Direct, concrete, zero filler. Stile commit progetto.

---

*Wave P8/12 sequenza Design Handoff. Sequenza C→B→A: P10 ✅ + P9 (in execution) + P8 (questo) + P11 + P12. Pattern lean = 1 wave alla volta.*
