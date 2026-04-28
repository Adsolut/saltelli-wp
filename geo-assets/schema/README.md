# GEO Assets — Studio Legale Saltelli

Asset pronti da iniettare nel tema WordPress custom. Tutti gli schema JSON-LD sono validati contro `schema.org` 2026 e ottimizzati per estraibilità AI (ChatGPT, Claude, Perplexity, Gemini, Google AI Overviews).

## File inclusi

```
geo-assets/
├── robots.txt                                  → deploy in / del dominio
├── llms.txt                                    → deploy in / del dominio
└── schema/
    ├── 01-organization-legalservice.json       → header globale, ogni pagina
    ├── 02-attorneys.json                       → CPT avvocato (4 entries)
    ├── 03-faqpage-example-tributario.json      → pattern per CPT competenza
    ├── 04-breadcrumblist-template.json         → ogni pagina non-home
    ├── 05-article-template.json                → ogni post blog
    └── README.md                               → questo file
```

## Strategia di integrazione nel tema

### Approccio: PHP partials, NON plugin

Il brief impone schema custom inline, non plugin (Schema Pro / Yoast / Rank Math built-in). Motivo: controllo totale, zero dipendenze, performance ottimale, schema sempre aggiornato con i contenuti reali del sito.

Struttura consigliata nel tema:

```
wp-content/themes/saltelli/
└── inc/
    └── schema/
        ├── schema-loader.php           → router: include il partial corretto in base al template
        ├── partial-organization.php    → wrappa 01-organization-legalservice.json
        ├── partial-attorney.php        → genera schema per CPT avvocato corrente
        ├── partial-faqpage.php         → legge ACF FAQ del CPT competenza
        ├── partial-breadcrumb.php      → genera dinamicamente
        └── partial-article.php         → per single post blog
```

### Hook in `header.php`

```php
<?php
// Schema JSON-LD globale (Organization + WebSite) — su OGNI pagina
include get_template_directory() . '/inc/schema/partial-organization.php';

// Schema specifico per il template corrente
include get_template_directory() . '/inc/schema/schema-loader.php';
?>
```

### `schema-loader.php` — logica di routing

```php
<?php
if (is_singular('avvocato')) {
    include __DIR__ . '/partial-attorney.php';
}
elseif (is_singular('competenza')) {
    include __DIR__ . '/partial-faqpage.php';
}
elseif (is_singular('post')) {
    include __DIR__ . '/partial-article.php';
}

// Breadcrumb su tutto tranne homepage
if (!is_front_page()) {
    include __DIR__ . '/partial-breadcrumb.php';
}
?>
```

## Conversione JSON → PHP

I file `.json` qui sono la **fonte di verità**. Il PHP partial deve replicare la struttura, sostituendo i campi statici con dati reali e i campi dinamici con WordPress functions.

**Esempio — `partial-organization.php`:**

```php
<?php
$schema = [
    "@context" => "https://schema.org",
    "@graph" => [
        [
            "@type" => ["Organization", "LegalService"],
            "@id" => home_url('/#organization'),
            "name" => get_bloginfo('name'),
            "url" => home_url('/'),
            // ... resto del JSON convertito
        ]
    ]
];
?>
<script type="application/ld+json">
<?php echo wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
</script>
```

## TODO da chiudere prima del deploy in produzione

| Campo | File | Cosa serve |
|---|---|---|
| `sameAs` Organization | `01-organization-legalservice.json` | URL Facebook, Instagram, LinkedIn, Google Business Profile dello studio |
| `sameAs` per ogni avvocato | `02-attorneys.json` | URL LinkedIn personale di ciascun avvocato |
| `alumniOf` per Fabiana, Antonia, Stefano | `02-attorneys.json` | Università di laurea (per Emiliano è Federico II, confermato) |
| Foto avvocati | tutti | Foto professionali ad alta risoluzione, 4 avvocati. Path: `/wp-content/uploads/avv-{slug}.jpg` |
| Foto sede studio | `01-organization-legalservice.json` | Foto facciata o interno studio. Path: `/wp-content/uploads/sede-studio-saltelli.jpg` |
| Anno fondazione studio | `01-organization-legalservice.json` (`foundingDate`) | Da chiedere al cliente |
| Coordinate GPS esatte | `01-organization-legalservice.json` (`geo`) | Verificare via Google Maps su Via Vannella Gaetani 27 |
| Google Business Profile URL | `01-organization-legalservice.json` (`sameAs`) | Da creare in Fase 1 del programma |
| FAQ per le altre 18 aree | nuovi file da `03-faqpage-*.json` | Contenuto da produrre (lavoro Elena, Fase 1-3) |

## Validazione obbligatoria pre-deploy

Per ogni schema, validare in QUESTO ORDINE:

1. **Schema.org Validator** → https://validator.schema.org/
   Deve dare zero errori. Warning ammessi solo per campi opzionali raccomandati.

2. **Google Rich Results Test** → https://search.google.com/test/rich-results
   Deve riconoscere il tipo (LocalBusiness, FAQ, Article, Breadcrumb) e mostrarne preview.

3. **Test su pagine reali**:
   - Homepage → Organization + WebSite
   - `/avvocati/emiliano-saltelli/` → Person/Attorney + Breadcrumb
   - `/competenze/diritto-tributario/` → FAQPage + Breadcrumb (e WebPage isPartOf Organization)
   - Un post blog → Article + Breadcrumb

4. **Headers HTTP**: confermare `Content-Type: application/ld+json` non è necessario se inline `<script type="application/ld+json">`. È necessario solo per file `.jsonld` separati.

## Note tecniche

- **Encoding**: tutti i file usano UTF-8 senza BOM. Caratteri italiani (à, è, ò, ecc.) NON devono essere escapati nel JSON. Usare `JSON_UNESCAPED_UNICODE` in PHP.
- **Slash**: usare `JSON_UNESCAPED_SLASHES` in PHP per leggibilità degli URL.
- **Posizione del `<script>`**: meglio in `<head>` ma prima della chiusura `</head>`. Subito dopo i meta tag SEO.
- **Una pagina può avere più blocchi schema**: una pagina `competenza` può legittimamente avere Organization (globale) + LegalService specializzato + FAQPage + BreadcrumbList. Sono indipendenti, ognuno nel proprio `<script type="application/ld+json">`.
- **`@id` come identificatore stabile**: usare URL canonici come `@id`. Questo permette ai crawler AI di costruire un grafo coerente delle entità Saltelli.

---
*Ultimo aggiornamento: 2026-04-28 — Claude × Duccio*
