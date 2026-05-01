# REPORT — Wave 3 · Task 5 (RETRY) · /casi/ editoriale

**Branch:** `feat/wave3-task-05`
**Commit:** `068ec55`
**Status:** ✅ DONE — pushed to `origin/feat/wave3-task-05`
**Smoke:** HTTP 200 + 1 H1 + 41 `.sl-casi__*` classes + 10 rows + 0 PHP errors
**Date:** 2026-05-01

## Scope

Riprendere Task 5 dopo fallimento del primo agent (helpers.php conteneva
solo `saltelli_all_cases()` ma page.php non lo usava ancora; nessun commit).
Match completo del JSX `saltelli-s2-casi.jsx`.

## Files modificati (scope ristretto)

| File | Δ | Note |
|---|---|---|
| `wp-content/themes/saltelli/page.php` | +192 / −47 | Nuovo branch `elseif ($sl_casi)` con layout JSX-faithful; vecchio blocco `is_page('casi')` (4 .sl-cases) rimosso. Default ramo (header + content + contatti) intatto. |
| `wp-content/themes/saltelli/inc/helpers.php` | +121 | `saltelli_cases_full()` aggiunto. `saltelli_homepage_cases()` e `saltelli_all_cases()` lasciati invariati per backward-compat. |
| `wp-content/themes/saltelli/assets/css/sections.css` | +285 | Solo blocco `WAVE3 TASK 5 (casi) BEGIN/END`, namespace `.sl-casi__*` (no collisione con `.sl-cases__*` legacy). |

NO-TOUCH rispettati: `tokens.css`, `functions.php`, `style.css`, `header.php`, `footer.php`, `single-*`, altri lock.

## Implementazione — punti chiave

### Layout (page.php)

1. **Hero 5fr/7fr** — breadcrumb mono + eyebrow `§ Risultati · Casi rappresentativi` + H1 Playfair clamp(64-132px) `Casi <em>rappresentativi.</em>`. Lato destro: lede italic 24px + meta mono `10 casi · 2022 → 2024 · aggiornato Apr 2026`. Mobile: stacking 1 colonna.

2. **Pull-quote caso simbolo** — solo se esiste `featured`. Grid 1fr/2fr su `var(--surface)`, border-top/bottom `var(--accent)`. Figure Playfair italic clamp(80-140px) bronze, blockquote Playfair italic clamp(24-32px) primary, cite mono.

3. **Filter bar** — `[Tutti, Privati, Imprese, Contenzioso, Altri]` con count dinamico, `data-filter`, `role="tablist"`, `aria-pressed`. Active state: `is-active` + border-bottom accent.

4. **Lista 10 casi** — grid 240px/1fr/200px: id+cat sx · desc italic 20px centro · figura bronze 28px + label mono dx. Hover/focus: `translateX(8px)` + `border-color: var(--accent)` con `transition var(--ease-editorial)` (fallback `cubic-bezier(.22, 1, .36, 1)`). `.is-hidden` toggled da JS al filter click.

5. **Pagination row** — `Pagina 1 / 1 · X casi visibili` aggiornato dinamicamente; bottone `Carica altri casi` disabilitato (placeholder paginazione futura).

6. **CTA finale 3fr/9fr** — `§ Prossimo caso` + H2 clamp(56-96px) `Vorresti vincere <em>il tuo?</em>` (em accent bronze) + lede italic + btn primary `Prenota gratuita` → `/contatti/`.

7. **JS inline scoped** — listener delegato sul `.sl-casi__filter-bar`, scope ristretto a `.sl-casi-page` per evitare collisioni con altri filter pattern del theme. Aggiorna `aria-pressed`, toggle `.is-hidden` sui row, ricalcola counter visibili.

### Helper (`saltelli_cases_full()`)

Compone tre fonti in priority order, deduplicate per `id` (lowercase trim):

1. **`saltelli_homepage_cases()`** — 4 casi base (ACF repeater oppure fallback editoriale). Normalizzati allo shape esteso.
2. **Blog post** tag/cat `sentenze` / `sentenza` / `casi` (max 6, ordine data DESC). Mappa post → caso usando excerpt + meta `_caso_outcome` / `_caso_categoria` / `_caso_label` (fallback editoriali).
3. **`saltelli_all_cases()`** — fallback editoriale JSX (10 casi) per garantire volume 8-10.

**Upsert intelligente al dedup**: se un nuovo push ha `featured: true`, promuove il caso pre-esistente con la flag e adotta `outcome` + `lbl` più ricchi (così l'AGE Riscossione 2024 esposto come `Annullamento` da homepage_cases viene aggiornato a `€240.000` + `Annullamento` da all_cases).

Output: `array<int, ['id', 'cat', 'outcome', 'lbl', 'desc', 'featured']>` max 12 casi.

## Smoke test

```
$ curl -s -o /dev/null -w "HTTP %{http_code}" http://localhost:8080/casi/
HTTP 200

$ curl -s http://localhost:8080/casi/ | grep -c '<h1'
1

$ curl -s http://localhost:8080/casi/ | grep -c 'class="sl-casi__row"'
10

$ curl -s http://localhost:8080/casi/ | grep -oE 'sl-casi__[a-z-]+' | sort -u | wc -l
41

# filter counts coerenti
Tutti(10) = Privati(5) + Imprese(2) + Contenzioso(1) + Altri(2)

# PHP errors: 0
```

## Note operative — coabitazione con altri agenti

Durante l'implementazione, le branch del wave 3 hanno subito riassegnamenti
da agenti paralleli:
- `feat/wave3-task-05` è stato creato da `main` (eb3b291) e committato direttamente.
- HEAD è stato spostato su `feat/wave3-task-06` (b8aba53) durante il lavoro
  (parallel agent task-6).
- Recovery: `git stash` mirato dei soli 3 file (page.php, helpers.php, sections.css),
  switch su `feat/wave3-task-05`, `git stash pop` con risoluzione conflitto
  via `git checkout --theirs` (versione mia) per page.php e sections.css.
- Verifica finale: `git diff --cached -- file | grep ^-` mostra solo le righe
  del vecchio `is_page('casi')` block legittimamente sostituite.

Lock `/tmp/saltelli-agents/task-05.lock` rimosso a fine task.
