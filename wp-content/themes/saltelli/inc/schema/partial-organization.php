<?php
/**
 * Schema partial — Organization + LegalService + WebSite.
 *
 * Replica geo-assets/schema/01-organization-legalservice.json.
 * Iniettato su OGNI pagina dal schema-loader.
 *
 * @package Saltelli
 */

defined('ABSPATH') || exit;

$studio = saltelli_studio_data();
$home   = home_url('/');

// hasOfferCatalog — riflette le 19 aree di pratica come da brief.
$practice_areas = [
    ['name' => 'Diritto tributario',                   'slug' => 'diritto-tributario'],
    ['name' => 'Cartelle esattoriali e multe',         'slug' => 'cartelle-esattoriali'],
    ['name' => 'Recupero crediti',                     'slug' => 'recupero-crediti'],
    ['name' => 'Diritto del lavoro',                   'slug' => 'diritto-del-lavoro'],
    ['name' => 'Diritto di famiglia e divorzi',        'slug' => 'diritto-di-famiglia'],
    ['name' => 'Responsabilità medica (malasanità)',   'slug' => 'malasanita'],
    ['name' => 'Diritto bancario',                     'slug' => 'diritto-bancario'],
    ['name' => 'Diritto condominiale e immobiliare',   'slug' => 'diritto-condominiale'],
    ['name' => 'Diritto dell\'immigrazione',           'slug' => 'diritto-immigrazione'],
    ['name' => 'Diritto penale',                       'slug' => 'diritto-penale'],
    ['name' => 'Diritto previdenziale',                'slug' => 'diritto-previdenziale'],
    ['name' => 'Diritto delle assicurazioni',          'slug' => 'diritto-assicurazioni'],
    ['name' => 'Diritto delle successioni',            'slug' => 'successioni'],
    ['name' => 'Risarcimento danni',                   'slug' => 'risarcimento-danni'],
    ['name' => 'Responsabilità civile',                'slug' => 'responsabilita-civile'],
    ['name' => 'Domiciliazione d\'impresa',            'slug' => 'domiciliazione-impresa'],
    ['name' => 'Consulenze online',                    'slug' => 'consulenze-online'],
    ['name' => 'Diritto amministrativo',               'slug' => 'diritto-amministrativo'],
    ['name' => 'Diritto commerciale',                  'slug' => 'diritto-commerciale'],
];
$offer_items = [];
foreach ($practice_areas as $a) {
    $offer_items[] = [
        '@type' => 'OfferCatalog',
        'name'  => $a['name'],
        'url'   => home_url('/competenze/' . $a['slug'] . '/'),
    ];
}

// Riferimenti incrociati al @id dei 4 avvocati (CPT).
$attorney_slugs = ['emiliano-saltelli', 'fabiana-saltelli', 'antonia-battista', 'stefano-gaetano-tedesco'];
$employee_refs  = [];
foreach ($attorney_slugs as $slug) {
    $employee_refs[] = ['@id' => home_url('/avvocati/' . $slug . '/#person')];
}

// sameAs — confermati 2026-04-28 da Ludovica.
// LinkedIn omesso perché è profilo personale di Emiliano (non Company Page);
// è agganciato a Person Emiliano in partial-attorney.php.
$same_as = [];
if (!empty($studio['social']['facebook'])) {
    $same_as[] = $studio['social']['facebook'];
}
if (!empty($studio['social']['instagram'])) {
    $same_as[] = $studio['social']['instagram'];
}
// TODO: Google Business Profile URL — da creare in Fase 1.

// Logo — usa custom_logo se impostato, altrimenti placeholder uploads.
$logo_id = get_theme_mod('custom_logo');
$logo_url = $logo_id ? wp_get_attachment_image_url($logo_id, 'full') : home_url('/wp-content/uploads/logo.png');
$logo_meta = $logo_id ? wp_get_attachment_metadata($logo_id) : null;

$organization = [
    '@type' => ['Organization', 'LegalService'],
    '@id'   => $home . '#organization',
    'name'  => $studio['legal_name'],
    'alternateName' => $studio['alt_names'],
    'url'   => $home,
    'logo'  => [
        '@type'  => 'ImageObject',
        'url'    => $logo_url,
        'width'  => $logo_meta['width']  ?? 512,
        'height' => $logo_meta['height'] ?? 512,
    ],
    // TODO: foto sede studio in /wp-content/uploads/sede-studio-saltelli.jpg (Ludovica).
    'image' => home_url('/wp-content/uploads/sede-studio-saltelli.jpg'),
    'description' => 'Studio legale a Napoli (Chiaia) fondato dall\'Avv. Emiliano Saltelli. Quattro avvocati, 19 aree di pratica. Specializzati in diritto tributario, del lavoro, di famiglia e cartelle esattoriali.',
    'slogan' => 'Avvocati a Napoli — competenza, trasparenza, risultati.',
    'vatID'  => $studio['vat'],
    'taxID'  => $studio['tax_id'],
    'telephone' => $studio['phone'],
    'email'  => $studio['email'],
    'address' => [
        '@type'           => 'PostalAddress',
        'streetAddress'   => $studio['street'],
        'addressLocality' => $studio['locality'],
        'addressRegion'   => $studio['region'],
        'postalCode'      => $studio['postal_code'],
        'addressCountry'  => $studio['country'],
    ],
    'geo' => [
        '@type'     => 'GeoCoordinates',
        // GPS confermati cliente via Google Maps (2026-04-28).
        'latitude'  => $studio['lat'],
        'longitude' => $studio['lng'],
    ],
    'openingHoursSpecification' => [[
        '@type'     => 'OpeningHoursSpecification',
        'dayOfWeek' => $studio['days'],
        'opens'     => $studio['opens'],
        'closes'    => $studio['closes'],
    ]],
    'priceRange' => $studio['price_range'],
    'areaServed' => [
        ['@type' => 'City',               'name' => 'Napoli'],
        ['@type' => 'AdministrativeArea', 'name' => 'Campania'],
        ['@type' => 'Country',            'name' => 'Italia'],
    ],
    'knowsAbout' => array_column($practice_areas, 'name'),
    'founder' => ['@id' => home_url('/avvocati/emiliano-saltelli/#person')],
    // Fondazione studio attuale: gennaio 2019 (confermato cliente 2026-04-28).
    'foundingDate' => $studio['founding_date'],
    'foundingLocation' => ['@type' => 'Place', 'name' => 'Napoli, Italia'],
    'employee' => $employee_refs,
    'memberOf' => [
        '@type' => 'Organization',
        'name'  => 'Ordine degli Avvocati di Napoli',
        'url'   => 'https://www.ordineavvocatinapoli.it',
    ],
    'contactPoint' => [
        [
            '@type'             => 'ContactPoint',
            'telephone'         => $studio['phone'],
            'contactType'       => 'customer service',
            'email'             => $studio['email'],
            'availableLanguage' => ['Italian', 'English', 'French'],
            'areaServed'        => 'IT',
        ],
        [
            '@type'             => 'ContactPoint',
            'telephone'         => $studio['whatsapp'],
            'contactType'       => 'customer service',
            'contactOption'     => 'TollFree',
            'availableLanguage' => 'Italian',
            // WhatsApp
        ],
    ],
    'hasOfferCatalog' => [
        '@type' => 'OfferCatalog',
        'name'  => 'Aree di pratica legale',
        'itemListElement' => $offer_items,
    ],
];

if (!empty($same_as)) {
    $organization['sameAs'] = $same_as;
}

$website = [
    '@type' => 'WebSite',
    '@id'   => $home . '#website',
    'url'   => $home,
    'name'  => get_bloginfo('name'),
    'publisher' => ['@id' => $home . '#organization'],
    'inLanguage' => get_bloginfo('language'),
    'potentialAction' => [
        '@type'  => 'SearchAction',
        'target' => [
            '@type'       => 'EntryPoint',
            'urlTemplate' => $home . '?s={search_term_string}',
        ],
        'query-input' => 'required name=search_term_string',
    ],
];

$schema = [
    '@context' => 'https://schema.org',
    '@graph'   => [$organization, $website],
];

saltelli_emit_jsonld($schema);
