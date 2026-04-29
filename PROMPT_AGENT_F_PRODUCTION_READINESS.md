# Prompt — Production Readiness Agent (Step F — Pre-deploy hardening)

> **Per Claude Code in nuova sessione.** Apri `saltelli-wp/`, leggi questo file, eseguilo. Lavoro previsto: 1-2 ore.
> **PRECEDENZA:** Template Polish Agent (Step E) deve essere completato. v0.6.0-beta-templates o successiva.

---

## Tu sei

Il **Production Readiness Agent**. Il tema è funzionante, popolato, polished. Il tuo lavoro è **l'ultima passata di hardening** prima di metterlo in produzione su DigitalOcean droplet (Step G eseguito da Duccio + Claude in chat).

Vuoi un sito che superi questi gate:
- Lighthouse Performance mobile + desktop ≥ 92
- Lighthouse Accessibility ≥ 95
- Schema JSON-LD validato zero errori esterni
- Cross-browser pulito: Chrome / Safari / Firefox / Mobile iOS+Android
- Zero console warnings/errors
- WOFF2 self-hosted (no Google Fonts @import)
- SRI hash su CDN GSAP/Lenis
- Yoast meta description su tutte 19+4+31+5+ pagine pubbliche
- /llms.txt pubblicamente raggiungibile
- /robots.txt AI crawlers OK
- HTTPS-ready

---

## Letture obbligatorie

1. `CLAUDE.md` — hard constraints
2. `geo-assets/schema/*.json` — riferimento schema
3. `inc/seo/meta-tags.php` — meta tag system (con coabitazione Yoast)
4. `inc/seo/ai-files.php` — /llms.txt + robots filter
5. `inc/enqueue.php` — script + style enqueue (per SRI)
6. `assets/css/base.css` — Google Fonts @import da rimuovere

---

## Hard rules

| Rule | Reason |
|---|---|
| Nessun cambio di design system o copy | Non è scope hardening |
| Tutti gli script CDN devono avere SRI hash | Security |
| Yoast deve restare la fonte primaria di meta description | Coabitazione |
| `<a>` esterni devono avere `rel="noopener noreferrer"` | Security |
| Image dimensions esplicite per evitare CLS | Performance |
| Tutto il fallback mobile-first | A11y + Perf |

---

## Task 1 — WOFF2 self-hosted (30 min)

Sostituire Google Fonts `@import` in `base.css` con file WOFF2 locali.

Font necessari:
- **Playfair Display** weights 400 + 700 (o Cormorant Garamond come fallback secondario)
- **DM Sans** weights 400, 500, 700
- **JetBrains Mono** weight 400 (per `.sl-mono`)

Strategia:
1. Scarica i WOFF2 ufficiali (Google Fonts API + UnicodeRange Latin):
   ```bash
   mkdir -p wp-content/themes/saltelli/assets/fonts
   
   # Esempio per Playfair Display 400
   # URL Google Fonts CSS API restituisce src: url(...woff2)
   curl -s "https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" \
       -A "Mozilla/5.0 (Macintosh; Intel Mac OS X) AppleWebKit/537.36" \
       | grep -oE 'https://[^)]+\.woff2' \
       | head -2
   # Poi scarica ciascuno e rinomina
   ```
   
   Oppure usa il tool `google-webfonts-helper` (online) per ottenere ZIP pronto:
   ```
   https://gwfh.mranftl.com/fonts → search font → download → estrai WOFF2
   ```

2. Salva i file in `assets/fonts/`:
   - `playfair-display-400.woff2`
   - `playfair-display-700.woff2`
   - `dm-sans-400.woff2`
   - `dm-sans-500.woff2`
   - `dm-sans-700.woff2`
   - `jetbrains-mono-400.woff2`

3. Sostituisci in `base.css`:
   ```css
   /* RIMUOVI: @import url('https://fonts.googleapis.com/css2?...'); */
   
   /* AGGIUNGI: */
   @font-face {
       font-family: 'Playfair Display';
       font-style: normal;
       font-weight: 400;
       font-display: swap;
       src: url('../fonts/playfair-display-400.woff2') format('woff2');
       unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
   }
   /* ... ripeti per ogni weight */
   ```

4. Aggiungi preload per i 2 critical weights (display + body) in `header.php`:
   ```php
   <link rel="preload" href="<?php echo SALTELLI_THEME_URI; ?>/assets/fonts/playfair-display-700.woff2" as="font" type="font/woff2" crossorigin>
   <link rel="preload" href="<?php echo SALTELLI_THEME_URI; ?>/assets/fonts/dm-sans-400.woff2" as="font" type="font/woff2" crossorigin>
   ```

5. Test: `curl -sI http://localhost:8080/wp-content/themes/saltelli/assets/fonts/playfair-display-700.woff2` → 200 OK

---

## Task 2 — SRI hash su CDN scripts (15 min)

In `inc/enqueue.php`, aggiungi `integrity` e `crossorigin` agli script CDN GSAP/Lenis. WordPress `wp_enqueue_script` non supporta SRI nativamente — usa il filter `script_loader_tag`:

```php
add_filter('script_loader_tag', function($tag, $handle, $src) {
    $sri_map = [
        'saltelli-gsap'             => 'sha384-XXX',  // hash SHA384 di gsap.min.js v3.12.5
        'saltelli-gsap-scrolltrigger' => 'sha384-XXX',
        'saltelli-gsap-splittext'   => 'sha384-XXX',
        'saltelli-lenis'            => 'sha384-XXX',
    ];
    if (isset($sri_map[$handle])) {
        $tag = str_replace('<script ', '<script integrity="' . $sri_map[$handle] . '" crossorigin="anonymous" ', $tag);
    }
    return $tag;
}, 10, 3);
```

**Calcola hash SHA-384** per ciascun script:
```bash
curl -s https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js | openssl dgst -sha384 -binary | openssl base64 -A
# Prefissalo con "sha384-"
```

Lista da hashare:
- `https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js`
- `https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js`
- `https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/SplitText.min.js`
- `https://cdnjs.cloudflare.com/ajax/libs/lenis/1.1.13/lenis.min.js`

---

## Task 3 — Image dimensions + lazy loading (15 min)

Per ogni `<img>` nel tema, garantisci:
- `width` e `height` attributi (evita CLS)
- `loading="lazy"` su immagini sotto la fold
- `loading="eager"` su immagini above-the-fold (hero, primo lawyer)
- `decoding="async"` su tutte
- `alt` sempre popolato (no alt vuoto, sostituisci con descrizione editoriale o `alt=""` solo per decorative)

Helper in `inc/helpers.php`:

```php
function saltelli_responsive_image($image_id, $size = 'large', $args = []) {
    if (!$image_id) return '';
    $defaults = [
        'loading' => 'lazy',
        'decoding' => 'async',
        'sizes' => '(max-width: 767px) 100vw, 50vw',
        'class' => '',
    ];
    $args = array_merge($defaults, $args);
    return wp_get_attachment_image($image_id, $size, false, $args);
}
```

---

## Task 4 — Yoast meta description audit (15 min)

Verifica che ogni post type pubblico abbia `_yoast_wpseo_metadesc` popolato:

```bash
# Posts senza Yoast meta
docker compose run --rm wpcli post list --post_type=post --post_status=publish \
    --meta_compare='NOT EXISTS' --meta_key='_yoast_wpseo_metadesc' --format=count

# Pages
docker compose run --rm wpcli post list --post_type=page --post_status=publish \
    --meta_compare='NOT EXISTS' --meta_key='_yoast_wpseo_metadesc' --format=count

# CPT avvocato
docker compose run --rm wpcli post list --post_type=avvocato --post_status=publish \
    --meta_compare='NOT EXISTS' --meta_key='_yoast_wpseo_metadesc' --format=count

# CPT competenza
docker compose run --rm wpcli post list --post_type=competenza --post_status=publish \
    --meta_compare='NOT EXISTS' --meta_key='_yoast_wpseo_metadesc' --format=count
```

Per ciascun CPT senza meta, generala dal `excerpt` o dal primo paragrafo del body:

```bash
# Per ogni CPT competenza, popola Yoast meta dal lead_breve / answer_capsule
for ID in $(docker compose run --rm wpcli post list --post_type=competenza --field=ID); do
    META=$(docker compose run --rm wpcli post meta get $ID lead_breve)
    if [ -n "$META" ]; then
        docker compose run --rm wpcli post meta update $ID _yoast_wpseo_metadesc "$META"
    fi
done
```

---

## Task 5 — Schema validation (10 min)

Esegui validation locale (non posso fare validator esterno):

```bash
# Estrai schema da homepage e pagine principali, verifica JSON parse
for URL in "/" "/avvocati/emiliano-saltelli/" "/competenze/diritto-tributario/" "/competenze/domiciliazione-impresa/"; do
    echo "─── $URL ───"
    curl -s "http://localhost:8080$URL" \
        | grep -oE '<script type="application/ld\+json">[^<]*</script>' \
        | sed 's/<[^>]*>//g' \
        | while read line; do
            echo "$line" | python3 -c "import sys, json; d=json.load(sys.stdin); print('✓ Valid JSON, type:', d.get('@type', d.get('@graph', [{}])[0].get('@type', 'unknown')))" 2>&1 \
                || echo "✗ Invalid JSON"
          done
done
```

Suggerisci a Duccio di fare validation manuale su https://validator.schema.org/ una volta che il sito è pubblicamente raggiungibile (post-deploy).

---

## Task 6 — Console errors check (15 min)

Suggerisci a Duccio di:
1. Aprire DevTools Console su `http://localhost:8080/`
2. Hard reload (Cmd+Shift+R)
3. Navigare 5-6 pagine principali
4. Annotare ogni errore o warning JS
5. Mandare lista a te

Probabili categorie:
- 404 risorse mancanti (font, immagini)
- GSAP/Lenis non caricati (CDN bloccato?)
- Yoast vs nostro meta-tags conflict warning
- Service Worker che intercetta (poco probabile, ma check)

Per ciascun errore, applica fix.

---

## Task 7 — `rel="noopener"` su link esterni (5 min)

Filter WordPress che aggiunge automaticamente:

```php
add_filter('the_content', function($content) {
    if (is_admin()) return $content;
    return preg_replace_callback(
        '/<a\s+([^>]*)href=["\'](https?:\/\/(?!' . preg_quote(parse_url(home_url(), PHP_URL_HOST), '/') . ')[^"\']+)["\']([^>]*)>/i',
        function($m) {
            $attrs = $m[1] . $m[3];
            if (!preg_match('/\brel=/', $attrs)) {
                return '<a ' . $m[1] . 'href="' . $m[2] . '"' . $m[3] . ' rel="noopener noreferrer" target="_blank">';
            }
            return $m[0];
        },
        $content
    );
});
```

Aggiungi in `inc/setup.php`.

---

## Task 8 — Lighthouse audit + final report (20 min)

Suggerisci a Duccio di lanciare Lighthouse mobile + desktop su:
1. Homepage
2. Single avvocato
3. Single competenza tier-1
4. Single blog post

Target da raggiungere:
- Performance: **≥ 92** (mobile), **≥ 95** (desktop)
- Accessibility: **≥ 95** (entrambi)
- Best Practices: **≥ 95**
- SEO: **100** (Yoast attivo lo garantisce)

Se sotto target, identifica top 3 issues Lighthouse e fixa.

---

## Task 9 — Bump version finale a 1.0.0-rc1

```bash
# 0.6.0-beta-templates → 1.0.0-rc1
sed -i.bak 's/Version: 0.6.0-beta-templates/Version: 1.0.0-rc1/' wp-content/themes/saltelli/style.css
sed -i.bak "s/define('SALTELLI_THEME_VERSION', '0.6.0-beta-templates')/define('SALTELLI_THEME_VERSION', '1.0.0-rc1')/" wp-content/themes/saltelli/functions.php
rm -f wp-content/themes/saltelli/{style.css,functions.php}.bak

docker compose run --rm wpcli cache flush
docker compose run --rm wpcli transient delete --all
```

---

## Report finale

Scrivi `.claude/knowledge/design/sessione-1/reports/production-readiness/REPORT.md`:

1. ✅/❌ ciascuno dei 9 task
2. Lighthouse scores (numeri puntuali, mobile + desktop, 4 URL chiave)
3. Schema validation: ✓/✗ per ciascuna pagina chiave
4. Console errors: lista (idealmente vuota)
5. Yoast meta coverage: X/Y posts pubblici hanno meta
6. WOFF2 self-hosted: confermato? + size totale fonts
7. SRI hash applicati: confermati?
8. Eventuali issue irrisolti che impediscono il go-live
9. **Sì/No: pronto per deploy DigitalOcean Step G?**

Poi **fermati**. Step G (deploy) viene eseguito da Duccio + Claude (chat) in un'altra sessione.

---

*v1.0 — Step F — pronto per deploy*
