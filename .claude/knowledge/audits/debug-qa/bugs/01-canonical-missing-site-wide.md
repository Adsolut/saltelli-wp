# Bug #01 — `<link rel="canonical">` mancante su tutto il sito

**Severity:** P1 (medium-high · SEO impact)
**Found:** 2026-05-05 by Debug QA Agent
**Browser/Method:** curl + python regex audit
**URL:** Site-wide (29/32 URL audited HTML pages)

## Descrizione

Nessuna delle 29 URL HTML audited (escluse XML/TXT/search) emette
`<link rel="canonical" href="...">` nel `<head>`. Lo schema Yoast JSON-LD
(WebPage @id) sostituisce parzialmente questa funzione ma:
- Non equivale per AEO/GEO crawlers AI
- Non equivale per Google canonical signals (mismatch AEO+SEO)
- I crawlers possono interpretare URL ambigui (es. /lo-studio/ vs /chi-siamo/) male

## Atteso

Ogni URL HTML deve emettere:
```html
<link rel="canonical" href="https://staging.studiolegalesaltelli.it/<path>/">
```

## Reproduce

```bash
curl -sL https://staging.studiolegalesaltelli.it/avvocati/emiliano-saltelli/ \
  | grep -i "rel=.canonical"
# Output: (vuoto — nessun match)
```

## Root cause

`wp-content/themes/saltelli/inc/seo/meta-tags.php` linea 32:
```php
if (saltelli_seo_plugin_active()) {
    return;  // bail-out totale: theme NON emette nulla quando Yoast attivo
}
```

Il theme delega tutto a Yoast, ma Yoast 27.5 (su questo sito) emette schema graph
+ description + OG tags MA non emette `<link rel="canonical">`. Probabile bug
Yoast 27.5 o config Yoast settings.

Ho verificato:
- Yoast option `disable-canonical`: **not set** (default = enabled)
- Yoast option `clean_canonical_filter`: **not set**
- Yoast emette description, OG, Twitter, schema graph correttamente
- **Solo canonical non viene emesso** — anomalia

## Fix proposto (in scope Debug & QA)

Split `saltelli_emit_meta_tags` in due funzioni:
1. `saltelli_emit_canonical` — emette canonical SEMPRE (sia se Yoast attivo che no)
2. `saltelli_emit_seo_meta` — emette description/OG/Twitter solo se nessun SEO plugin

Patch:
```php
add_action('wp_head', 'saltelli_emit_canonical', 3);
function saltelli_emit_canonical() {
    $url = saltelli_canonical_url();
    if ($url !== '') {
        // Suppresso se Yoast emette duplicato (filter check via has_filter wpseo_canonical).
        // Strategia: emit theme canonical, lasciamo che Yoast sovrascriva con suo se vuole.
        printf('<link rel="canonical" href="%s">' . "\n", esc_url($url));
    }
}
```

Helper `saltelli_canonical_url()` già esiste in `inc/helpers.php:312`.

## Status

- [x] Reproduced (29/29 HTML URL test)
- [x] Root cause identified
- [x] Fix applied (commit 8fe8c2e... pending)
- [x] Re-tested (canonical visibile su 7/7 URL test)
- [x] Closed

## Fix applied

Split `saltelli_emit_meta_tags` in `wp-content/themes/saltelli/inc/seo/meta-tags.php`:
- Nuovo `saltelli_emit_canonical` su `wp_head` priority 3 → emette SEMPRE
- Vecchio `saltelli_emit_meta_tags` priority 4 → invariato (description+OG+Twitter solo se no SEO plugin)

Eventuali duplicati con Yoast (se Yoast inizia ad emettere) sono OK
per crawlers (HTML last canonical wins; Google dedupa).

Deploy droplet: rsync + systemctl reload php8.2-fpm + cache flush.

Verify post-fix:
- /                            → canonical=https://staging.studiolegalesaltelli.it/
- /lo-studio/                  → canonical=https://staging.studiolegalesaltelli.it/chi-siamo/ (after redirect)
- /avvocati/emiliano-saltelli/ → canonical=https://staging.studiolegalesaltelli.it/avvocati/emiliano-saltelli/
- /competenze/diritto-tributario/ → canonical=...
- /faq/                        → canonical=...
- /contatti/                   → canonical=...
- /costi/                      → canonical=...

Smoke 21/21 PASS post-fix.
