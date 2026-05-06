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

---

## Mini-fix follow-up: BLOCKER B (DISCOVERY-01)

**Date**: 2026-05-06 (sere)
**Branch**: stesso `fix/wave5-blog-rewrites` (commit aggiuntivo)
**Decisione cliente**: CONSOLIDARE `diritto-di-famiglia` (ID 2669) con `diritto-di-famiglia-lgbtq` (ID 2666)
**Action**: DELETE post 2669 force + cleanup ACF relationship + 1 redirect 301 in mappa B

### Pre-state

- 18 competenze publish (15 privati + 2 imprese + 1 contenzioso-amministrativo)
- ID 2669 `diritto-di-famiglia` post extra non in CSV cliente-firmato (DEC-021)
- ID 2666 `diritto-di-famiglia-lgbtq` target consolidamento

### Operazioni

1. Backup post 2669 + meta + tassonomia in `10-backup-post-2669*.{json,csv}`
2. Verifica zero ref interne:
   - 0 menu items linkano ID 2669
   - 1 ACF relationship trovato: post 2662 (avv. Antonia Battista)
     `aree_competenza_correlate = [2666, 2669, 2677]` → cleaned a `[2666, 2677]`
     (2666 LGBTQ+ già presente, rimossa duplicata 2669)
   - 5 hardcoded refs a slug `diritto-di-famiglia` in 4 file template — annotati
     in `10-hardcoded-refs-to-famiglia.txt` (out of scope mini-fix; tutti gli URL
     risultanti funzionano via fuzzy 404 guess WP, no azione richiesta)
3. `wp post delete 2669 --force` → eliminato (verifica `wp post get 2669` → "not found")
4. Aggiunto redirect 301 in `inc/seo/legacy-redirects.php` mappa B (saltelli_mvp_to_audit_redirect_map):
   `/aree-di-pratica/privati/diritto-di-famiglia/` → `/aree-di-pratica/privati/diritto-di-famiglia-lgbtq/`
5. Flush rewrite + cache + transient

### Post-state

- **17 competenze publish** ✅ allineata a DEC-021 cliente-firmato
- Distribuzione cluster (via SQL): `privati=14, imprese=2, contenzioso-amministrativo=1` → 17

| Cluster | Pre | Post | Δ |
|---|---|---|---|
| privati | 15 | 14 | -1 |
| imprese | 2 | 2 | 0 |
| contenzioso-amministrativo | 1 | 1 | 0 |
| **TOTALE** | **18** | **17** | **-1** |

### Smoke verification (7 test PASS)

| Test | URL | Risultato | Atteso |
|---|---|---|---|
| 1 — nuovo redirect mappa B | `/aree-di-pratica/privati/diritto-di-famiglia/` | 301 → LGBTQ+ ✅ | 301 → LGBTQ+ |
| 2 — target LGBTQ+ | `/aree-di-pratica/privati/diritto-di-famiglia-lgbtq/` | 200 ✅ | 200 |
| 3 — audit-aligned no regression × 5 URL | `/`, `/aree-di-pratica/`, etc. | 5/5 200 ✅ | 200 |
| 4 — redirect legacy no regression × 3 URL | `/lo-studio/`, `/avvocati/`, `/casi/` | 3/3 301 ✅ | 301 |
| 5 — chain mappa A → B (`/avvocato-divorzista[-italia]/`) | × 2 URL | 2/2 final 200 (2 hops) ⚠️ | final 200 |
| 6 — hardcoded `/competenze/diritto-di-famiglia/` (taxonomy-tipo-area:86) | | 301 → LGBTQ+ ✅ | (atteso 404, ma WP fuzzy guess salva) |
| 7 — hardcoded glossario / partial-organization | | 301 → LGBTQ+ ✅ | (idem) |

**Discovery aggiuntivo**: WP `redirect_guess_404_permalink()` (canonical.php) cattura
auto-magicamente i link hardcoded a `/competenze/diritto-di-famiglia/` perché 2666
ha slug `diritto-di-famiglia-lgbtq` che starts-with il request slug. Quindi
nessuno dei 5 hardcoded refs rompe alcun URL — tutti rendono 200 finale.

### File modificato (oltre a wave5-blog-rewrites.php FIX A)

- `wp-content/themes/saltelli/inc/seo/legacy-redirects.php` (+5 righe, sezione DISCOVERY-01 in mappa B)

### Modifiche DB (non versionate ma documentate)

- DELETE FROM wp_posts WHERE ID = 2669 (+ cascade meta + termrelationships via wp post delete --force)
- UPDATE wp_postmeta SET meta_value='a:2:{...}' WHERE post_id=2662 AND meta_key='aree_competenza_correlate' (rimossa 2669 da array)

### Audit trail

- `10-pre-consolidate-competenze.csv` (18 righe pre-fix, snapshot)
- `10-backup-post-2669.json` + `10-backup-post-2669-meta.json` + `10-backup-post-2669-terms.csv` (safety backup)
- `10-menu-refs-to-2669.txt` (0 menu refs)
- `10-acf-refs-to-2669.txt` (1 ACF ref pulito + decisione)
- `10-hardcoded-refs-to-famiglia.txt` (5 hardcoded refs annotati + WP fuzzy guess discovery)
- `cli-output/10-smoke-consolidate.txt` (7 test PASS)

### Note operative

- Theme version **invariata** a `1.1.0-wave5-ia-refactor` (è patch, non minor bump).
- Niente impatti su menu primary, footer, page hub, blog redirect chain.
- Performance: nessun overhead aggiuntivo (1 entry statica nel mvp_to_audit map).

### Lessons learned

1. **WP redirect_guess_404_permalink() salva da molte regression** quando si elimina un post con slug condiviso prefix: il sibling più simile vince. Pattern utile da ricordare per future consolidazioni di post.
2. **ACF relationships non orfanizzano automaticamente**: dopo DELETE di un post, gli ID rimangono nei serialized array delle relationship. Vanno puliti manualmente — pena dirty data anche se ACF al render skippa silenziosamente i missing.
3. **Hardcoded slug in template** (taxonomy-tipo-area, glossario, schema partial) sopravvivono al delete grazie al WP fuzzy guess — ma è una dipendenza fragile che vale la pena pulire in un follow-up patch quando l'orchestratore decide.

## Closes (aggiornato)

- BLOCKER A audit Wave 5 ✅
- BLOCKER B (DISCOVERY-01) audit Wave 5 ✅
