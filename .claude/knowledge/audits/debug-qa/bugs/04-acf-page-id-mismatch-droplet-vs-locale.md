# Bug #04 — ACF page-id mismatch droplet vs locale (P0 critical)

**Severity:** P0 (critical · cliente CMS-autonomy promise broken per 6 page)
**Found:** 2026-05-05 by Debug QA Agent
**Scope:** group_faq_v1 + group_info_shared_v1 Field Groups + 6 page WP

## Descrizione

Il Wave 2 migration (script `scripts/wave2-phase2-pages.php`) ha hardcoded
le page IDs LOCALI (Docker WP locale) per i target ACF. Su droplet le pagine
hanno ID DIVERSI per 6 slug, quindi:

1. I dati ACF Wave 2 sono stati popolati su pagine **SBAGLIATE** su droplet
2. I Field Group ACF (location rule `page == <id>`) si attaccano a pagine
   sbagliate su droplet → Cliente NON può editare via WP-Admin

## Mapping ID locale ↔ droplet (page mismatch)

| Slug | Local ID | Droplet ID | Status |
|---|---|---|---|
| costi | 2695 | 2695 | ✓ MATCH |
| casi | 2699 | 2699 | ✓ MATCH |
| contatti | 23 | 23 | ✓ MATCH |
| **faq** | **2705** | **2708** | ✗ MISMATCH |
| **guide-gratuite** | **2706** | **2709** | ✗ MISMATCH |
| **come-lavoriamo** | **2709** | **2712** | ✗ MISMATCH |
| **prima-consulenza** | **2708** | **2711** | ✗ MISMATCH |
| lavora-con-noi | 372 | 372 | ✓ MATCH |
| **richiedi-preventivo** | **2710** | **2713** | ✗ MISMATCH |

## Conseguenze su droplet

**ACF data mismigrato** (fields applicati a pagine errate):
- droplet ID 2705 (Diritto societario, competenza) ha hero_eyebrow="§ Risorse · Domande frequenti" ← era per /faq/
- droplet ID 2706 (Contrattualistica, competenza) ha guide-gratuite content
- droplet ID 2708 (faq, page) ha prima-consulenza content ← /faq/ "ruba" /prima-consulenza/!
- droplet ID 2709 (guide-gratuite, page) ha come-lavoriamo content
- droplet ID 2710 (Glossario legale, page) ha richiedi-preventivo content

**Pagine VUOTE** che dovrebbero essere popolate:
- /come-lavoriamo/ (id 2712 droplet) — vuota
- /prima-consulenza/ (id 2711 droplet) — vuota
- /richiedi-preventivo/ (id 2713 droplet) — vuota

**Frontend ancora 200** grazie al fallback `saltelli_field('x', $pid, 'hardcoded')`
ma cliente vede contenuto SBAGLIATO o GENERICO su 5 pagine info-shared + /faq/.

## Reproduce

```bash
ssh deploy@178.62.207.50 'sudo -u www-data wp --path=/var/www/saltelli eval "
foreach ([\"faq\",\"guide-gratuite\",\"come-lavoriamo\",\"prima-consulenza\",\"richiedi-preventivo\"] as \$slug) {
    \$p = get_page_by_path(\$slug);
    \$eyebrow = get_field(\"hero_eyebrow\", \$p->ID);
    echo \$slug . \" id=\" . \$p->ID . \" eyebrow=\\\"\" . \$eyebrow . \"\\\"\" . PHP_EOL;
}
"'
```

## Root cause

Wave 2 migration script `scripts/wave2-phase2-pages.php` hardcoded local IDs:
```php
$pid = 2705;  // hardcoded — assumes local Docker page ID
update_field('hero_eyebrow', '...', $pid);
```

Quando lo script gira su droplet, $pid 2705 punta a "Diritto societario"
(competenza) invece che a /faq/.

Field Group JSON (`acf-json/group_info_shared_v1.json`) usa stesse local IDs
nelle location rules:
```json
"location": [
    [{"param": "page", "operator": "==", "value": "2706"}],  // local guide-gratuite, droplet contrattualistica
    ...
]
```

## Fix proposto (Phase 5 — IN SCOPE Debug & QA)

### Sub-fix A — ACF custom location rule `page_slug` (architectural, env-portable)

`inc/acf-fields.php` — aggiungere:
```php
add_filter('acf/location/rule_types', function ($choices) {
    $choices['Page']['page_slug'] = __('Page Slug', 'saltelli');
    return $choices;
});
add_filter('acf/location/rule_match/page_slug', function ($match, $rule, $screen) {
    if (empty($screen['post_id'])) return false;
    $page = get_post($screen['post_id']);
    if (!$page || $page->post_type !== 'page') return false;
    $value = (string) ($rule['value'] ?? '');
    if ($rule['operator'] === '==') return $page->post_name === $value;
    if ($rule['operator'] === '!=') return $page->post_name !== $value;
    return false;
}, 10, 3);
```

Update Field Group JSON (`acf-json/group_*.json`) location rules:
```json
// group_faq_v1.json
"location": [[{"param":"page_slug","operator":"==","value":"faq"}]]

// group_info_shared_v1.json
"location": [
    [{"param":"page_slug","operator":"==","value":"guide-gratuite"}],
    [{"param":"page_slug","operator":"==","value":"come-lavoriamo"}],
    [{"param":"page_slug","operator":"==","value":"prima-consulenza"}],
    [{"param":"page_slug","operator":"==","value":"lavora-con-noi"}],
    [{"param":"page_slug","operator":"==","value":"richiedi-preventivo"}]
]
```

Inoltre per consistency, anche group_costi_v1, group_casi_v1, group_contatti_v1
dovrebbero usare slug (anche se i loro ID matchano per ora).

### Sub-fix B — Re-migrate ACF data su droplet con slug lookup

Run `scripts/wave3-phase5-droplet-acf-fix.php` su droplet che:
1. Per ogni slug info-shared: legge ACF data dalla page WRONG (`get_field('x', wrong_id)`)
2. Scrive ACF data sulla page CORRETTA (slug lookup): `update_field('x', $value, get_page_by_path($slug)->ID)`
3. Cleanup wrong pages: `delete_field('x', wrong_id)` per fields ACF Wave 1 (NON tocca post_content / _thumbnail_id)

### Sub-fix C — verify post-fix

Re-run smoke + ACF audit per confermare:
- /faq/ (2708) ha dati FAQ corretti
- /guide-gratuite/, /come-lavoriamo/, /prima-consulenza/, /richiedi-preventivo/ popolati
- Pagine "wrong" (Diritto societario, Contrattualistica, Glossario legale) NON hanno più ACF data Wave 1

## Status

- [x] Reproduced
- [x] Root cause identified
- [ ] Fix A applied (ACF custom location rule)
- [ ] Fix B applied (data re-migration su droplet)
- [ ] Fix C verified (smoke + audit)
- [ ] Closed

## Files coinvolti (Phase 5)

- `wp-content/themes/saltelli/inc/acf-fields.php` (custom location rule)
- `wp-content/themes/saltelli/acf-json/group_costi_v1.json` (slug-based)
- `wp-content/themes/saltelli/acf-json/group_casi_v1.json` (slug-based)
- `wp-content/themes/saltelli/acf-json/group_contatti_v1.json` (slug-based)
- `wp-content/themes/saltelli/acf-json/group_faq_v1.json` (slug-based)
- `wp-content/themes/saltelli/acf-json/group_info_shared_v1.json` (slug-based)
- NEW: `scripts/wave3-phase5-droplet-acf-fix.php` (data re-migration)
- droplet: `/var/www/saltelli/wp-content/themes/saltelli/` (rsync post-fix)

## Note sui rischi

Il sub-fix A modifica i Field Group JSON files (ACF caches them). Dopo deploy
serve `wp cache flush` su droplet.

Sub-fix B richiede esecuzione script su droplet — è destructive (delete_field
sulle pagine wrong) ma idempotente.

Test atteso post-fix:
- WP-Admin /post.php?post=2708 (faq droplet) mostra Field Group group_faq_v1
- Cliente Elena può editare /faq/ via UI
- Frontend smoke 32/32 PASS invariato
