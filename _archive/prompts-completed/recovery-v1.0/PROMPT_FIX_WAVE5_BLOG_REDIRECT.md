# 🔧 FIX Wave 5 — Blog redirect chain rotto (BLOCKER A)

> **Audience**: Claude Code agent in nuova sessione terminale.
> **Branch**: `fix/wave5-blog-rewrites` da `feat/wave5-ia-refactor` (NON da main).
> **Scope**: bug fix isolato, **non** redo Wave 5. Tutto il resto di Wave 5 è OK.
> **Tempo stimato**: 30-60 min.

---

## 🎯 Tu sei

Claude Code agent dedicato al fix di un singolo bug critico scoperto durante l'audit orchestratore di Wave 5: **tutti i 326 blog post historical sono inaccessibili dopo redirect 301**.

Il pattern del bug:
- Browser richiede `/blog/{slug}/` → riceve **301** ✅ (redirect funziona)
- Browser segue redirect → richiede `/risorse/blog/{slug}/` → riceve **404** ❌ (target inesistente)

Severity: **alta**. Tutti i backlink esterni storici al sito Elementor finiscono in 404. SEO equity persa.

---

## 📚 Letture obbligatorie

1. **`CLAUDE.md`** — single source of truth
2. **`prompts/PROMPT_AGENT_v1.1_WAVE5_IA_REFACTOR.md`** Phase 6 + Phase 8 — contesto blog redirect originale
3. **`.claude/knowledge/recovery/WAVE5-IA-REFACTOR-REPORT.md`** Phase 8 — dichiara "10/10 blog redirect chain PASS" (ma in realtà artifact mostra 0/33)
4. **`.claude/knowledge/audits/wave5-ia-refactor/cli-output/08-smoke-blog.txt`** — l'evidenza del fallimento (33 FAIL)
5. **`wp-content/themes/saltelli/inc/seo/wave5-blog-rewrites.php`** — il file bugged
6. **`wp-content/themes/saltelli/inc/seo/legacy-redirects.php`** — sezione blog regex Step 4

---

## 🔍 Root cause (diagnosi orchestratore)

### Stato attuale del sistema dopo Wave 5

- **Page WP `blog`**: ID 1413, slug `blog`, parent ID 2802 (page `risorse`). URL native WP: `/risorse/blog/`. Funziona ✅ (è nel smoke 32/32).
- **Permalink struct globale**: `/%postname%/` (i blog post vivono al top-level, es. `/dividere-la-casa-familiare/`)
- **Rewrite rule custom Wave 5** in `wave5-blog-rewrites.php`:
  ```php
  add_rewrite_rule('^risorse/blog/([^/]+)/?$', 'index.php?name=$matches[1]&post_type=post', 'top');
  ```
  con `add_action('init', ..., 11)`.

### Perché il rewrite rule NON viene applicato

Quando WP processa `/risorse/blog/dividere-la-casa-familiare/`:
1. Interpreta come potenziale **nested page**: cerca `pagename=risorse/blog/dividere-la-casa-familiare`
2. Il page resolution standard di WP avviene **prima** che vengano valutati i rewrite rules custom anche con flag `'top'` (page slug routing è hardcoded prima)
3. Cerca un page con slug "dividere-la-casa-familiare" che sia child di "blog" (parent 1413). Non lo trova → 404.

Il rewrite rule custom esiste ma è "ombrato" dal page resolution.

---

## 🛠 Fix candidati (in ordine di preferenza)

### ✅ FIX A — Filter `request` (raccomandato)

Intercetta query var **prima** del page resolution. Pattern pulito, manutenibile, no impatto su page hub `/risorse/blog/` archive.

```php
// Sostituisci wave5-blog-rewrites.php con questo pattern

defined('ABSPATH') || exit;

add_action('init', function () {
    // Le rewrite rules restano (per generare URL correttamente in get_permalink)
    add_rewrite_rule('^risorse/blog/category/([^/]+)/?$', 'index.php?category_name=$matches[1]', 'top');
    add_rewrite_rule('^risorse/blog/tag/([^/]+)/?$', 'index.php?tag=$matches[1]', 'top');
    add_rewrite_rule('^risorse/blog/author/([^/]+)/?$', 'index.php?author_name=$matches[1]', 'top');
    add_rewrite_rule('^risorse/blog/([^/]+)/?$', 'index.php?name=$matches[1]&post_type=post', 'top');
}, 11);

// FIX: filter 'request' intercetta PRIMA del page resolution
add_filter('request', 'saltelli_resolve_blog_post_request', 5);
function saltelli_resolve_blog_post_request($query_vars) {
    if (empty($query_vars['pagename'])) return $query_vars;
    
    $pagename = $query_vars['pagename'];
    
    // Match: risorse/blog/{slug}/  (escludendo i sub-archive category/tag/author)
    if (!preg_match('#^risorse/blog/([^/]+)/?$#', $pagename, $matches)) {
        return $query_vars;
    }
    
    $slug = $matches[1];
    
    // Skip se è un sub-archive (lascia il rewrite rule risolvere)
    if (in_array($slug, ['category', 'tag', 'author'], true)) {
        return $query_vars;
    }
    
    // Lookup: esiste un post (post_type=post) con questo slug?
    $post = get_page_by_path($slug, OBJECT, 'post');
    
    if ($post) {
        // Sostituisci pagename con name + post_type → forza single-post resolution
        unset($query_vars['pagename']);
        $query_vars['name'] = $slug;
        $query_vars['post_type'] = 'post';
    }
    // Else: nessun post trovato → lascia che WP faccia 404 naturale
    
    return $query_vars;
}
```

**Pro**: pattern pulito, zero side-effect su altre URL, manutenibile.
**Contro**: aggiunge una `get_page_by_path` query per ogni request `/risorse/blog/{slug}/` — costo O(1) cached da WP object cache.

### FIX B — Custom `parse_request` handler (alternativa)

Più invasivo ma più esplicito:

```php
add_action('parse_request', function ($wp) {
    if (empty($wp->request)) return;
    if (!preg_match('#^risorse/blog/([^/]+)/?$#', $wp->request, $matches)) return;
    
    $slug = $matches[1];
    if (in_array($slug, ['category', 'tag', 'author'], true)) return;
    
    $post = get_page_by_path($slug, OBJECT, 'post');
    if ($post) {
        $wp->query_vars = ['name' => $slug, 'post_type' => 'post'];
        unset($wp->query_vars['pagename']);
    }
});
```

Stesso effetto del FIX A ma usa `parse_request` invece di filter `request`. Sostanzialmente equivalente.

### FIX C — Rinomina page hub `blog` (sconsigliato)

Cambiare lo slug della page ID 1413 da `blog` a `blog-hub` o eliminarla. Funziona ma rompe:
- Menu primary che ora linka `/risorse/blog/` (page hub)
- Footer hardcoded
- Aspettativa cliente

Da NON usare salvo emergency.

### FIX D — Permalink struct globale

Cambiare permalink a `/risorse/blog/%postname%/`. Rompe TUTTO il MVP (CPT competenze, avvocati, casi). Da NON usare.

---

## 🎯 Fix raccomandato dall'orchestratore

**FIX A**. Pattern pulito + zero side-effect + manutenibile.

---

## 📋 Plan di esecuzione (8 step)

### Step 1 — Branch + setup

```bash
cd ~/Desktop/DEV/saltelli-wp/   # o path equivalente
git fetch origin
git checkout feat/wave5-ia-refactor
git pull --ff-only

# Crea branch fix dedicato
git checkout -b fix/wave5-blog-rewrites
```

### Step 2 — Pre-fix verification (riconferma il bug 0/33)

Esegui smoke blog redirect attuale per cristallizzare l'evidenza pre-fix:

```bash
# Sample 5 random posts via WP-CLI Docker, test chain
docker-compose exec -T wp wp post list --post_status=publish --post_type=post \
    --orderby=rand --posts_per_page=5 --format=csv --fields=ID,post_name | tail -n +2 | \
while IFS=, read -r id slug; do
    legacy="http://localhost:8080/blog/${slug}/"
    target="http://localhost:8080/risorse/blog/${slug}/"
    legacy_status=$(curl -s -o /dev/null -w "%{http_code}" "$legacy")
    target_status=$(curl -s -o /dev/null -w "%{http_code}" "$target")
    echo "post_id=${id} legacy=${legacy_status} target=${target_status}"
done > .claude/knowledge/audits/wave5-ia-refactor/cli-output/09-smoke-blog-prefix.txt

cat .claude/knowledge/audits/wave5-ia-refactor/cli-output/09-smoke-blog-prefix.txt
```

Atteso: 5 righe `legacy=301 target=404`. Se invece vedi `target=200`, il bug si è auto-risolto (fai ANCORA un flush e riprova).

### Step 3 — Implementa FIX A in `wave5-blog-rewrites.php`

Edita `wp-content/themes/saltelli/inc/seo/wave5-blog-rewrites.php`. Mantieni le rewrite rules esistenti (sono utili per `get_permalink`), aggiungi il filter `request`. Vedi codice nella sezione FIX A sopra.

Salva file.

### Step 4 — Flush rewrite rules

```bash
docker-compose exec -T wp wp rewrite flush --hard
docker-compose exec -T wp wp cache flush
docker-compose exec -T wp wp transient delete --all
```

### Step 5 — Smoke 33 random posts post-fix

Esegui smoke su 33 random posts (stesso campione + qualche extra per robustezza):

```bash
docker-compose exec -T wp wp post list --post_status=publish --post_type=post \
    --orderby=rand --posts_per_page=33 --format=csv --fields=ID,post_name | tail -n +2 | \
while IFS=, read -r id slug; do
    legacy="http://localhost:8080/blog/${slug}/"
    target="http://localhost:8080/risorse/blog/${slug}/"
    legacy_status=$(curl -s -o /dev/null -w "%{http_code}" "$legacy")
    target_status=$(curl -s -o /dev/null -w "%{http_code}" "$target")
    if [ "$legacy_status" = "301" ] && [ "$target_status" = "200" ]; then
        echo "OK post_id=${id} legacy=301 target=200 (${slug})"
    else
        echo "FAIL post_id=${id} legacy=${legacy_status} target=${target_status} (${slug})"
    fi
done | tee .claude/knowledge/audits/wave5-ia-refactor/cli-output/09-smoke-blog-postfix.txt

# Conta PASS/FAIL
echo ""
echo "PASS: $(grep -c '^OK' .claude/knowledge/audits/wave5-ia-refactor/cli-output/09-smoke-blog-postfix.txt)"
echo "FAIL: $(grep -c '^FAIL' .claude/knowledge/audits/wave5-ia-refactor/cli-output/09-smoke-blog-postfix.txt)"
```

### Step 6 — Verifica edge cases

```bash
# Page hub /risorse/blog/ deve continuare a funzionare (NON rompi archive)
curl -s -o /dev/null -w "Page hub: %{http_code}\n" "http://localhost:8080/risorse/blog/"
# Atteso: 200

# Sub-archive category/tag/author
curl -s -o /dev/null -w "Category archive: %{http_code}\n" "http://localhost:8080/risorse/blog/category/diritto-di-famiglia/"
# Atteso: 200 o 404 (404 OK se quella categoria non esiste, ma non 500)

# Slug inesistente
curl -s -o /dev/null -w "Slug fake: %{http_code}\n" "http://localhost:8080/risorse/blog/questo-slug-non-esiste-xyz/"
# Atteso: 404 (resolution naturale WP)

# Top-level slug ancora funzionante (permalink struct globale invariato)
docker-compose exec -T wp wp post list --post_status=publish --post_type=post \
    --orderby=rand --posts_per_page=1 --format=csv --fields=post_name | tail -n 1 | \
while read -r slug; do
    curl -s -o /dev/null -w "Top-level: %{http_code} (slug=${slug})\n" "http://localhost:8080/${slug}/"
done
# Atteso: 200 — i blog post sono ancora accessibili al loro URL native (questo perché il legacy-redirects redirect avviene dopo)
```

### Step 7 — Commit + push

```bash
git add wp-content/themes/saltelli/inc/seo/wave5-blog-rewrites.php
git add .claude/knowledge/audits/wave5-ia-refactor/cli-output/09-smoke-blog-prefix.txt
git add .claude/knowledge/audits/wave5-ia-refactor/cli-output/09-smoke-blog-postfix.txt

git commit -m "fix: wave5 blog redirect chain — filter request priority 5

- Aggiunge filter 'request' che intercetta /risorse/blog/{slug}/
  PRIMA del page resolution standard, sostituendo pagename con
  name + post_type=post per forzare single-post resolution.
- Rewrite rules mantenute per supportare get_permalink + sub-archive.
- Smoke: 33/33 PASS (vs 0/33 pre-fix).

Closes BLOCKER A audit Wave 5."

git push origin fix/wave5-blog-rewrites
```

### Step 8 — Report fix

Crea `.claude/knowledge/recovery/WAVE5-FIX-BLOG-REPORT.md` con:

```markdown
# Wave 5 Fix — Blog redirect chain

**Branch**: fix/wave5-blog-rewrites (da feat/wave5-ia-refactor)
**Bug**: BLOCKER A audit Wave 5 — 0/33 blog post accessibili
**Fix**: FIX A (filter 'request' priority 5)

## Root cause
Page WP 'blog' (ID 1413, parent risorse 2802) causa page resolution
prevalente sul rewrite rule custom: WP cerca pagename=risorse/blog/{slug}
come nested page invece di applicare la rule.

## Fix
Filter 'request' priority 5 intercetta PRIMA del page resolution
standard, sostituisce pagename con name + post_type per forzare
single-post resolution.

## Test results
- Pre-fix smoke (5 sample): 5/5 FAIL (legacy=301, target=404)
- Post-fix smoke (33 sample): 33/33 PASS (legacy=301, target=200)
- Page hub /risorse/blog/: 200 (invariato)
- Sub-archive category/tag/author: 200 (invariato)
- Top-level post URL (es. /dividere-la-casa-familiare/): 200 (invariato, legacy redirect agisce dopo)

## File modificato
- `wp-content/themes/saltelli/inc/seo/wave5-blog-rewrites.php`

## Smoke artifacts
- 09-smoke-blog-prefix.txt
- 09-smoke-blog-postfix.txt
```

---

## ✅ Acceptance criteria fix

- [ ] Branch `fix/wave5-blog-rewrites` creato da `feat/wave5-ia-refactor` (NON da main)
- [ ] FIX A applicato in `wave5-blog-rewrites.php` (filter `request` priority 5)
- [ ] `wp rewrite flush --hard` + `wp cache flush` + transient delete eseguiti
- [ ] Pre-fix smoke confermato 0/5 PASS (cristallizza il bug)
- [ ] Post-fix smoke 33/33 PASS (legacy=301, target=200)
- [ ] Edge cases verified: page hub `/risorse/blog/` 200, slug fake 404, top-level slug 200
- [ ] Single commit chiaro con scope ben definito
- [ ] Branch pushed
- [ ] Report `WAVE5-FIX-BLOG-REPORT.md` compilato

---

## 🚨 Cosa NON fare

- ❌ NON modificare `legacy-redirects.php` (Phase 6 redirect funziona già correttamente)
- ❌ NON cambiare permalink struct globale (rompe MVP intero)
- ❌ NON rinominare page hub `blog` (rompe menu primary + footer)
- ❌ NON tornare su main per applicare il fix (deve essere su branch fix dedicato)
- ❌ NON fare merge automatico — orchestratore audisce + decide

---

## 🎯 Output expected

1. Branch `fix/wave5-blog-rewrites` pushato su origin
2. 1 commit con fix (NO commit aggiuntivi)
3. Smoke artifacts pre-fix + post-fix in `.claude/knowledge/audits/wave5-ia-refactor/cli-output/`
4. Report `.claude/knowledge/recovery/WAVE5-FIX-BLOG-REPORT.md`
5. Theme version **invariata** a `1.1.0-wave5-ia-refactor` (è patch, non minor bump)

L'orchestratore (chat) audisce il fix + se OK ti dice di procedere con il **mini-fix BLOCKER B** (consolidamento `diritto-di-famiglia` ID 2669 con LGBTQ+) sullo stesso branch `fix/wave5-blog-rewrites` o su un secondo branch `fix/wave5-discovery-01`. Decisione da te orchestratore dopo audit fix A.
