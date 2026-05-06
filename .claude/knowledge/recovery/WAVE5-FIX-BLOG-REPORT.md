# Wave 5 Fix — Blog redirect chain (BLOCKER A)

**Branch**: `fix/wave5-blog-rewrites` (parent: `feat/wave5-ia-refactor` @ `dcf65c8`)
**Date**: 2026-05-06
**Bug**: BLOCKER A audit Wave 5 — `08-smoke-blog.txt` mostrava 0/33 PASS (chain `/blog/{slug}/` → 301 → `/risorse/blog/{slug}/` → 404)
**Fix**: FIX A (filter `request` priority 5) come da `prompts/PROMPT_FIX_WAVE5_BLOG_REDIRECT.md`

---

## Root cause

Il file `inc/seo/wave5-blog-rewrites.php` (Wave 5 Phase 8) registra rewrite rules custom `^risorse/blog/([^/]+)/?$ → index.php?name=$matches[1]&post_type=post` con flag `top` priority 11. La diagnosi orchestratore osservava che WP può ombrare il rule custom risolvendo `/risorse/blog/{slug}/` come nested page (`pagename=risorse/blog/{slug}`) e ritornare 404.

In pratica, durante l'execution di questo fix è emerso che la condizione `0/33 FAIL` originaria del Phase 8 audit era dovuta principalmente a **flush rewrite mancante** (`wp rewrite flush --hard` non eseguito o non persistito al termine di Phase 8): un primo `wp rewrite flush --hard` su branch `fix/wave5-blog-rewrites` PRIMA del fix ha già portato il sistema a 33/33 PASS (cfr. `09-smoke-blog-prefix.txt`).

Il FIX A è stato comunque applicato secondo specifica orchestratore per:

1. **Defense-in-depth**: il rewrite rule da solo è fragile sotto modifiche future della struttura page (es. se mai un editor crea una page child di `blog`).
2. **Esplicitazione query_vars**: il filter `request` priority 5 intercetta `pagename` PRIMA del page resolution standard di WP, sostituendolo con `name` + `post_type=post` per forzare la single-post resolution.
3. **Compliance**: il prompt fix esplicita FIX A come pattern raccomandato.

## Fix applicato

File modificato: `wp-content/themes/saltelli/inc/seo/wave5-blog-rewrites.php`

Aggiunte:
- Header docblock esteso con nota FIX BLOCKER A
- `add_filter('request', 'saltelli_resolve_blog_post_request', 5)`
- Helper `saltelli_resolve_blog_post_request($query_vars)`:
  - Skip se `pagename` empty
  - Match regex `^risorse/blog/([^/]+)/?$`
  - Skip su sub-archive (`category|tag|author`) — lascia rewrite rule risolvere
  - `get_page_by_path($slug, OBJECT, 'post')` → se post esiste, rimuove `pagename` e setta `name` + `post_type=post`
  - Se nessun post trovato → lascia 404 naturale

Mantenute (invariate):
- 4 rewrite rules `add_rewrite_rule(...)` per single post + sub-archive (utili per `get_permalink` e fallback resolution).
- `add_action('init', ..., 11)`.

## Test results

| Test | Risultato | Atteso |
|---|---|---|
| Pre-fix smoke 33 slug audit (post hard-flush) | 33/33 PASS | (bug auto-risolto da flush) |
| Post-fix smoke 33 random posts | **33/33 PASS** | 33/33 PASS |
| Page hub `/risorse/blog/` | 200 | 200 |
| Sub-archive category `contratti` | 200 | 200 |
| Sub-archive category `diritto-del-lavoro` | 200 | 200 |
| Sub-archive category `diritto-di-famiglia-ed-ereditario` | 200 | 200 |
| Categoria inesistente | 404 | 404 |
| Slug post inesistente `/risorse/blog/xyz/` | 404 | 404 |
| Top-level slug native (es. `/dividere-la-casa-familiare-…/`) | 200 | 200 |
| Parent page `/risorse/` | 200 | 200 |

## File modificato

- `wp-content/themes/saltelli/inc/seo/wave5-blog-rewrites.php`

## Smoke artifacts

- `.claude/knowledge/audits/wave5-ia-refactor/cli-output/09-smoke-blog-prefix.txt`
- `.claude/knowledge/audits/wave5-ia-refactor/cli-output/09-smoke-blog-postfix.txt`
- `.claude/knowledge/audits/wave5-ia-refactor/cli-output/09-smoke-blog-edge-cases.txt`

## Note operative

- Theme version **invariata** a `1.1.0-wave5-ia-refactor` (è patch interno, no bump per ora).
- Nessun impatto su `legacy-redirects.php` (Phase 6 redirect lavora già correttamente, era il leg `301 → target` che falliva).
- Niente impatti su menu primary, footer, page hub `/risorse/blog/` archive.
- Performance: il filter aggiunge una `get_page_by_path` query per request `/risorse/blog/{slug}/` — costo O(1) cached da WP object cache.

## Lessons learned

1. **Flush rewrite va sempre persistito al termine di una phase che registra rules**: nel Phase 8 di Wave 5 il flush evidentemente non è stato persistito (warning `.htaccess` di docker-wp aveva camuffato il fatto). Soluzione: aggiungere check `wp rewrite list | grep risorse/blog` come acceptance gate in qualunque prompt che registri rewrite rules.
2. **Filter `request` priority < 10 è il pattern robusto** per forzare single-post resolution su URL pattern che collidono con la page hierarchy WP. Più affidabile di `parse_request` perché lavora a livello di query_vars già processati.
3. **FIX A è idempotente** rispetto allo stato già funzionante: anche se il rewrite rule funziona da solo, il filter aggiunge solo casistica difensiva senza side-effect.

## Closes

- BLOCKER A audit Wave 5 ✅
