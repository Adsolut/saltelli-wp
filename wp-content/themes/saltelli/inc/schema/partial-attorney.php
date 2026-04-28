<?php
/**
 * Schema partial — Person/Attorney.
 *
 * Replica geo-assets/schema/02-attorneys.json per UN avvocato (il post corrente).
 * @id stabile: home_url('/avvocati/{slug}/#person')
 *
 * @package Saltelli
 */

defined('ABSPATH') || exit;

if (!is_singular('avvocato')) {
    return;
}

$post_id = get_the_ID();
$post    = get_post($post_id);
if (!$post) {
    return;
}

$studio = saltelli_studio_data();
$slug   = $post->post_name;
$url    = get_permalink($post_id);

// Foto: ACF field se presente, altrimenti featured image, altrimenti placeholder.
$image_url = '';
$foto      = saltelli_field('foto_ritratto', $post_id);
if (is_array($foto) && !empty($foto['url'])) {
    $image_url = $foto['url'];
} elseif (is_numeric($foto)) {
    $image_url = wp_get_attachment_image_url((int) $foto, 'saltelli-attorney-portrait') ?: '';
} elseif (has_post_thumbnail($post_id)) {
    $image_url = get_the_post_thumbnail_url($post_id, 'saltelli-attorney-portrait') ?: '';
} else {
    // TODO: foto reale Saltelli — Ludovica.
    $image_url = home_url('/wp-content/uploads/avv-' . $slug . '.jpg');
}

// Description: bio_breve se presente, altrimenti excerpt, altrimenti title fallback.
$description = (string) saltelli_field('bio_breve', $post_id, '');
if ($description === '') {
    $description = wp_strip_all_tags(get_the_excerpt($post_id));
}
if ($description === '') {
    $description = sprintf('Avvocato dello Studio Legale Saltelli & Partners — %s.', get_the_title($post_id));
}

// knowsAbout: specializzazioni dal CPT, fallback array vuoto.
$knows_about = saltelli_get_attorney_specializations($post_id);

// Email/telefono: ACF, fallback studio.
$email     = (string) saltelli_field('email_pubblica', $post_id, $studio['email']);
$telephone = (string) saltelli_field('telefono_pubblico', $post_id, $studio['phone']);

// sameAs — LinkedIn personale.
// Priorità: ACF (se l'admin ha popolato il campo) → fallback hardcoded
// (helpers::saltelli_attorney_linkedin) per i casi confermati dal cliente.
$linkedin = (string) saltelli_field('same_as_linkedin', $post_id, '');
if ($linkedin === '') {
    $linkedin = saltelli_attorney_linkedin($slug);
}
$same_as  = [];
if ($linkedin !== '') {
    $same_as[] = $linkedin;
}
// TODO: LinkedIn personale di fabiana-saltelli, antonia-battista,
// stefano-gaetano-tedesco — in attesa da Ludovica (al 2026-04-28).

// Title parsing: estrai givenName/familyName dal titolo "Avv. Emiliano Saltelli".
$full = trim(get_the_title($post_id));
$clean = trim(preg_replace('/^Avv\.?\s+/i', '', $full));
$parts = preg_split('/\s+/', $clean);
$family = array_pop($parts);
$given  = trim(implode(' ', $parts));

// jobTitle: ACF ruolo_breve, fallback "Avvocato".
$job_title = (string) saltelli_field('ruolo_breve', $post_id, 'Avvocato');

$person = [
    '@context'        => 'https://schema.org',
    '@type'           => ['Person', 'Attorney'],
    '@id'             => $url . '#person',
    'givenName'       => $given,
    'familyName'      => $family,
    'name'            => $full,
    'honorificPrefix' => 'Avv.',
    'jobTitle'        => $job_title,
    'description'     => $description,
    'url'             => $url,
    'image'           => $image_url,
    'telephone'       => $telephone,
    'email'           => $email,
    'worksFor'        => ['@id' => home_url('/#organization')],
    'memberOf'        => [
        '@type' => 'Organization',
        'name'  => 'Ordine degli Avvocati di Napoli',
        'url'   => 'https://www.ordineavvocatinapoli.it',
    ],
    'hasOccupation'   => [
        '@type' => 'Occupation',
        'name'  => $job_title,
        'occupationLocation' => ['@type' => 'City', 'name' => 'Napoli'],
        'skills' => implode(', ', $knows_about),
    ],
    'address' => [
        '@type'           => 'PostalAddress',
        'streetAddress'   => $studio['street'],
        'addressLocality' => $studio['locality'],
        'postalCode'      => $studio['postal_code'],
        'addressCountry'  => $studio['country'],
    ],
    'knowsLanguage' => [
        ['@type' => 'Language', 'name' => 'Italian', 'alternateName' => 'it'],
    ],
];

if (!empty($knows_about)) {
    $person['knowsAbout'] = $knows_about;
}

if (!empty($same_as)) {
    $person['sameAs'] = $same_as;
}

// alumniOf — confermato 2026-04-28 (cliente): tutti e 4 gli avvocati
// (Emiliano, Fabiana, Antonia, Stefano) sono laureati alla Federico II.
$alumni_federico_ii_slugs = [
    'emiliano-saltelli',
    'fabiana-saltelli',
    'antonia-battista',
    'stefano-gaetano-tedesco',
];
if (in_array($slug, $alumni_federico_ii_slugs, true)) {
    $person['alumniOf'] = [
        '@type' => 'CollegeOrUniversity',
        'name'  => 'Università degli Studi di Napoli Federico II',
        'url'   => 'https://www.unina.it',
    ];
}

saltelli_emit_jsonld($person);
