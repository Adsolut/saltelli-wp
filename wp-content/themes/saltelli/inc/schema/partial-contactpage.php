<?php
/**
 * Schema partial — ContactPage (only on /contatti/).
 *
 * WAVE3 TASK 6 — Aggiunge un nodo ContactPage standalone con mainEntity →
 * Organization graph (#organization), che già contiene LegalService +
 * GeoCoordinates + ContactPoint multipli (telefono · email · WhatsApp).
 *
 * Coabitazione: ContactPage non è generato nativamente dai principali plugin
 * SEO (Yoast/Rank Math/AIOSEO emettono WebPage/CollectionPage). Sicuro da
 * iniettare anche con plugin attivo. La GeoCoordinates è ribadita inline
 * per crawler che non risolvono il @id graph reference.
 *
 * @package Saltelli
 */

defined('ABSPATH') || exit;

$studio = saltelli_studio_data();
$home   = home_url('/');
$page_url = home_url('/contatti/');

$contact_page = [
    '@context'    => 'https://schema.org',
    '@type'       => 'ContactPage',
    '@id'         => $page_url . '#contactpage',
    'url'         => $page_url,
    'name'        => 'Contatti — Studio Legale Saltelli & Partners',
    'description' => 'Prenota un primo incontro gratuito allo Studio Legale Saltelli & Partners di Napoli (Chiaia). Risposta entro 24 ore — telefono, email, WhatsApp.',
    'inLanguage'  => get_bloginfo('language'),
    'isPartOf'    => ['@id' => $home . '#website'],
    'mainEntity'  => ['@id' => $home . '#organization'],
    'about'       => ['@id' => $home . '#organization'],
    // GeoCoordinates inline — backup esplicito per crawler che non risolvono @id refs.
    'geo' => [
        '@type'     => 'GeoCoordinates',
        'latitude'  => $studio['lat'],
        'longitude' => $studio['lng'],
    ],
    'contactPoint' => [
        [
            '@type'             => 'ContactPoint',
            'telephone'         => $studio['phone'],
            'email'             => $studio['email'],
            'contactType'       => 'customer service',
            'availableLanguage' => ['Italian', 'English', 'French'],
            'areaServed'        => 'IT',
            'hoursAvailable'    => [
                '@type'     => 'OpeningHoursSpecification',
                'dayOfWeek' => $studio['days'],
                'opens'     => $studio['opens'],
                'closes'    => $studio['closes'],
            ],
        ],
        [
            '@type'             => 'ContactPoint',
            'telephone'         => $studio['whatsapp'],
            'contactType'       => 'customer service',
            'contactOption'     => 'TollFree',
            'availableLanguage' => 'Italian',
        ],
    ],
];

saltelli_emit_jsonld($contact_page);
