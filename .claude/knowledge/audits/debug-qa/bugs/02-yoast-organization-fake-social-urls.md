# Bug #02 — Yoast Organization schema graph contiene URL social fake/sbagliati

**Severity:** P1 (medium-high · brand integrity + SEO trust)
**Found:** 2026-05-05 by Debug QA Agent
**URL:** Site-wide (Yoast schema graph emesso in <head> di ogni pagina)

## Descrizione

Lo schema graph emesso da Yoast SEO contiene un Organization node con
`sameAs` che lista 6 URL social — ma alcuni di questi puntano a profili
**fake o non gestiti dallo studio**:

```json
"Organization": {
  "sameAs": [
    "https://www.facebook.com/studiolegalesaltelli",     // FAKE — real: /share/1D1jCY7BnW/
    "https://x.com/legalesaltelli",                       // TBD/non confermato
    "https://www.instagram.com/studiolegalesaltelli",    // OK ✓ (manca trailing /)
    "https://www.tiktok.com/@studiolegalesaltelli",      // TBD/non confermato
    "https://www.linkedin.com/in/emilianosaltelli/",     // PERSONAL profile (non Organization!)
    "https://www.youtube.com/@studiolegalesaltelli"      // TBD/non confermato
  ]
}
```

Source of truth (saltelli_studio_data() in helpers.php:398):
```php
'social' => [
  'facebook'  => 'https://www.facebook.com/share/1D1jCY7BnW/',
  'instagram' => 'https://www.instagram.com/studiolegalesaltelli/',
],
```

Theme Options ACF (Wave 2 popolato):
- social_facebook = "https://www.facebook.com/share/1D1jCY7BnW/"
- social_instagram = "https://www.instagram.com/studiolegalesaltelli/"
- social_linkedin = "" (vuoto — niente Company Page)
- social_twitter = "" (TBD)

## Atteso

Yoast Organization sameAs deve contenere SOLO gli URL confermati dal cliente:
- Facebook share URL real
- Instagram con trailing /
- (LinkedIn personale Emiliano va su Person.sameAs, NON Organization)
- (X/Twitter, TikTok, YouTube TBD — vuoto se non confermati)

## Root cause

Probabilmente impostati direttamente in Yoast → SEO → Settings → Site features →
Knowledge graph → Other social profiles (con valori legacy).

OPPURE: hook custom in `inc/seo/yoast-schema-extensions.php` aggiunge sameAs.

## Reproduce

```bash
curl -sL https://staging.studiolegalesaltelli.it/ \
  | grep -oE 'sameAs[^]]*\]' | head -5
```

## Fix proposto (Phase 5)

**Opzione A (preferita)**: filter `wpseo_schema_organization` per sovrascrivere `sameAs`
con valori da ACF Theme Options (`social_facebook` etc.) — fallback `saltelli_studio_data()`.

```php
add_filter('wpseo_schema_organization', function ($graph) {
    $links = [];
    foreach (['facebook', 'instagram', 'linkedin', 'twitter'] as $net) {
        $url = saltelli_option('social_' . $net, '');
        if ($url !== '') $links[] = $url;
    }
    if (!empty($links)) $graph['sameAs'] = $links;
    return $graph;
});
```

**Opzione B (delega Elena)**: Elena/Ludovica entrano in Yoast admin →
Settings → Site features → Other social profiles, e correggono i 6 URL
manualmente.

Decisione orchestratore: A è codified+riusabile, B è più flessibile ma
richiede config update post-deploy.

## Status

- [x] Reproduced
- [x] Root cause identified
- [x] Fix applied (Opzione A: filter wpseo_schema_graph)
- [x] Re-tested (Organization sameAs ora 2 URL confermati invece di 6 fake)
- [x] Closed

## Fix applied

Aggiunto `add_filter('wpseo_schema_graph', ..., 13, 2)` in
`wp-content/themes/saltelli/inc/seo/yoast-schema-extensions.php`:

```php
foreach ($graph as &$piece) {
    if (in_array($type, ['Organization','LegalService','LocalBusiness','ProfessionalService'], true)) {
        $piece['sameAs'] = $authoritative_sameas; // da ACF Theme Options
    }
}
```

`$authoritative_sameas` letto da:
1. ACF Theme Options (Wave 1+2): social_facebook, social_instagram, social_linkedin, social_twitter
2. Fallback: saltelli_studio_data() helpers.php (Facebook share URL + Instagram)

Verify post-fix su staging:
```
@type=Organization
sameAs:
  - https://www.facebook.com/share/1D1jCY7BnW/   (confermato)
  - https://www.instagram.com/studiolegalesaltelli/ (confermato)
```

LinkedIn personale Emiliano resta su Person.sameAs (partial-attorney.php),
NON Organization. Twitter/X TBD vuoto.

Files modified:
- `wp-content/themes/saltelli/inc/seo/yoast-schema-extensions.php` (+30 righe)
- Deploy droplet: rsync + systemctl reload php8.2-fpm + cache flush.
