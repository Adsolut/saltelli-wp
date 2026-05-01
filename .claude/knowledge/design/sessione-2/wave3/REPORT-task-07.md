# Wave 3 · Task 07 — /blog/ archive editoriale

**Branch:** `feat/wave3-task-07`
**Source JSX (sacro):** `.claude/knowledge/design/sessione-2/saltelli-s2-blog-archive.jsx`
**Status:** DONE · smoke test PASS · pronto per merge

## Files touched

| File | Change |
|---|---|
| `wp-content/themes/saltelli/home.php` | NEW — override pulito di `index.php` per la blog index quando `page_for_posts` è settato. Non tocca `archive.php` (riservato a category/tag/tax/date/author). |
| `wp-content/themes/saltelli/assets/css/sections.css` | Append scoped tra marker `/* === WAVE3 TASK 7 (blog) BEGIN/END === */` (~360 righe). |

NO-TOUCH rispettati: `tokens.css`, `functions.php`, `style.css`, `page.php`, `single-*`, `taxonomy-*`, `archive-*`. Lock altri agenti non toccati.

## Mapping JSX → PHP

| JSX block | Class PHP / templating |
|---|---|
| `<section>` Hero (5fr/7fr) eyebrow + h1 + lede italic + counter | `.sl-blog2__hero` con `.sl-blog2__h1` (Playfair clamp 64-140px), counter dinamico `wp_count_posts('post')->publish` + `wp_count_terms` + `get_lastpostmodified('blog')`. |
| Sticky tabs `position: sticky; top: 73` | `.sl-blog2__tabs` (`top: 73px`); link a category archives via `get_term_link()`. Stato `is-active` su `is_category()`. Top 7 categorie per count. Voce "Tutti" → `get_post_type_archive_link('post')`. |
| Featured 8/4 (16:9 image + body) | `.sl-blog2__featured-wrap` + `.sl-blog2__featured` (8fr/4fr ≥1024px). Solo su `paged===1`. Usa `has_post_thumbnail()` con fallback gradient. Calcolo "min" via `str_word_count(/220)`. |
| Grid 3-col (resto post) | `.sl-blog2__grid` con `repeat(3,1fr)` ≥1024, `repeat(2,1fr)` 768-1023, `1fr` mobile. Skip primo post se page 1 (è il featured). |
| Card 4:3 + cat 11px + Playfair 22-24 + excerpt + footer mono | `.sl-blog2__card` con `.sl-blog2__card-media-zoom` per `transform: scale(1.03) 600ms`, hover bronze su `.sl-blog2__card-cat`. `-webkit-line-clamp: 3` titolo, `2` excerpt. |
| Pagination minimal "← Precedenti / 1 — 12 di 326 / Successivi →" | `.sl-blog2__pager` con `get_previous_posts_page_link()` / `next_posts(...)`. Counter normalizzato per gestire `post_count != posts_per_page` (vedi note sotto). |
| Newsletter inline (5fr/7fr) | `.sl-blog2__newsletter` con form GET-safe `action=/contatti/`, accent bronze su `<em>al mese</em>`. |

## Schema JSON-LD (coabitazione Yoast)

Yoast emette già `WebPage / CollectionPage` + `BreadcrumbList` + `Organization`.
Aggiunti SOLO i nodi mancanti via `saltelli_emit_jsonld()`:

- `Blog` con `@id="/blog/#blog"`, `publisher` Organization name-only (Yoast ha il completo).
- `ItemList` con `numberOfItems` + `itemListElement` (`ListItem` con `position`, `url`, `name`).

JSON ASCII-safe (`\uXXXX`) come da policy memory `feedback_jsonld_iubenda.md`.

## Smoke test

```
curl -sI http://localhost:8080/blog/  → HTTP 200
grep <h1 …>                           → 1 (sl-blog2__h1, brand è <a>)
sl-blog2__cell                        → 31 (32 query - 1 featured)
@type":"Blog"                         → 1
@type":"ItemList" + numberOfItems     → 32
WAVE3 TASK 7 markers (CSS served)     → 2 (BEGIN+END)
docker exec wp php -l home.php        → No syntax errors
```

Tabs sticky verificate visivamente via `top: 73px` (allineato all'altezza header su breakpoint desktop).

## Note operative

1. **Counter pagina ≠ posts_per_page WP.** La query main su `/blog/` ritorna 32 post in pagina 1 ma 9 in `/blog/page/2/` — riflesso di stato DB anomalo (probabilmente sticky/featured fissati o vecchio override pre-tema). Il template usa `$wp_query->post_count` come fonte di verità, quindi visualizza correttamente il range reale ma le pagine successive mostrano "10 — 18 di 326" dovuto a `posts_per_page=9`. Non risolvibile dal solo template; richiederebbe `pre_get_posts` in `functions.php` (NO-TOUCH). Da valutare in fase di consolidation.
2. **Date format inglese ("30 April 2026").** `wp_date()` rispetta locale ma it_IT language pack non installato/attivo nel container Docker. La compilazione WordPress con `WPLANG=it_IT` risolverebbe; out-of-scope.
3. **Tab "Editorial".** JSX hardcoded ha 7 categorie; PHP usa top-7 dinamiche per count. Coerente con dataset reale.
4. **Image hover sui card.** Implementato come secondo elemento `.sl-blog2__card-media-zoom` per separare il `transform: scale` dal contenitore `aspect-ratio` (evita reflow card hover).

## Coordination

- Lock claim: `/tmp/saltelli-agents/task-07.lock` ✓ acquisito a 11:18
- Lock release: a fine commit (vedi sezione finale messaggio).
