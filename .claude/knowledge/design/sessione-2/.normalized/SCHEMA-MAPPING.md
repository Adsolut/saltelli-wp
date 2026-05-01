# Sessione 2 · JSON-LD Schema Mapping

Reference per Task 1-10. Ogni page emette gli schemi indicati inline via `inc/schema/`.

| Page | Template target | Schemas | Note Yoast coabitation |
|---|---|---|---|
| /chi-siamo/ | page.php (is_page('chi-siamo')) | AboutPage + Organization + LegalService | NO Organization se Yoast attivo |
| /avvocati/{slug}/ | single-avvocato.php | Person + Attorney (subtype) | OK — Yoast non emette Person |
| /competenze/{tier1}/ | single-competenza.php (tier-1) | LegalService + FAQPage + Article | OK — già emesso, verifica |
| /casi/ | page.php (is_page('casi')) | CollectionPage + ItemList con LegalService results | NO BreadcrumbList se Yoast attivo |
| /contatti/ | page.php (is_page('contatti')) | ContactPage + LocalBusiness + GeoCoordinates + ContactPoint | NO Organization se Yoast attivo |
| /blog/ | home.php / archive.php | Blog + ItemList di Article | NO BreadcrumbList se Yoast attivo |
| /tipo-area/{slug}/ | taxonomy-tipo-area.php | CollectionPage + ItemList di LegalService | OK |
| /glossario-legale/ | page.php (is_page('glossario-legale')) | DefinedTermSet + DefinedTerm × N + FAQPage | NO Article duplicato |
| 404 | 404.php | WebPage + isPartOf homepage | OK |

## Rule helpers

- Use `saltelli_emit_jsonld($payload)` helper (gestisce Iubenda DOMDocument round-trip).
- Hook target: `wp_head` action, priority 5.
- Yoast check: `if (defined('WPSEO_VERSION'))` skip Organization/BreadcrumbList/Article.
- Person schema: usa `same_as` con LinkedIn personal Emiliano (NON Company Page — non esiste).

## Studio fixed data (single source)

```php
$studio = saltelli_studio_data();
// ['name', 'phone', 'email', 'pec', 'address', 'piva', 'social' => ['instagram','linkedin'], ...]
```
