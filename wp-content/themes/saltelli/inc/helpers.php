<?php
/**
 * Helper functions — small utilities used across templates and partials.
 *
 * @package Saltelli
 */

defined('ABSPATH') || exit;

/**
 * Wrapper attorno a get_field() che funziona anche se ACF non è installato.
 * Restituisce $default se ACF non c'è o il campo non esiste.
 *
 * @param string $name
 * @param int|null $post_id
 * @param mixed $default
 * @return mixed
 */
function saltelli_field($name, $post_id = null, $default = null) {
    if (!function_exists('get_field')) {
        return $default;
    }
    $value = get_field($name, $post_id);
    if ($value === null || $value === '' || $value === false) {
        return $default;
    }
    return $value;
}

/**
 * Restituisce le specializzazioni di un avvocato come array di stringhe.
 * Supporta sia repeater ACF Pro (sub-field "label") sia textarea (split su \n).
 *
 * @param int $post_id
 * @return array<string>
 */
function saltelli_get_attorney_specializations($post_id) {
    $raw = saltelli_field('specializzazioni', $post_id, []);

    if (is_string($raw)) {
        $lines = preg_split('/\r\n|\r|\n/', $raw);
        return array_values(array_filter(array_map('trim', $lines)));
    }

    if (is_array($raw)) {
        $out = [];
        foreach ($raw as $row) {
            if (is_array($row) && isset($row['label'])) {
                $out[] = trim((string) $row['label']);
            } elseif (is_string($row)) {
                $out[] = trim($row);
            }
        }
        return array_values(array_filter($out));
    }

    return [];
}

/**
 * Per una pagina competenza, ritorna gli avvocati referenti (post objects).
 * Restituisce un array di WP_Post.
 *
 * @param int $competenza_id
 * @return WP_Post[]
 */
function saltelli_get_attorneys_for_competenza($competenza_id) {
    $ids = saltelli_field('lead_attorneys', $competenza_id, []);
    if (empty($ids)) {
        return [];
    }
    $ids = is_array($ids) ? $ids : [$ids];
    $ids = array_filter(array_map('intval', $ids));
    if (empty($ids)) {
        return [];
    }

    $q = get_posts([
        'post_type'        => 'avvocato',
        'post__in'         => $ids,
        'orderby'          => 'post__in',
        'numberposts'      => -1,
        'suppress_filters' => false,
    ]);
    return $q ?: [];
}

/**
 * Genera la catena breadcrumb come array di nodi:
 *   [ ['name' => 'Home', 'url' => 'https://...'], ['name' => 'Competenze', 'url' => '...'], ['name' => 'Diritto tributario'] ]
 * L'ultimo nodo NON ha 'url' (è la pagina corrente).
 *
 * @param int|null $post_id
 * @return array<int, array{name:string, url?:string}>
 */
function saltelli_get_breadcrumb_chain($post_id = null) {
    $post_id = $post_id ?: get_queried_object_id();
    $chain = [];

    // Home — sempre primo
    $chain[] = ['name' => __('Home', 'saltelli'), 'url' => home_url('/')];

    if (is_404() || is_search()) {
        $chain[] = ['name' => is_404() ? __('Non trovato', 'saltelli') : __('Risultati ricerca', 'saltelli')];
        return $chain;
    }

    // Archive
    if (is_post_type_archive()) {
        // get_post_type() può essere vuoto su archive senza post.
        // get_queried_object() ritorna il WP_Post_Type su archive CPT.
        $obj = get_queried_object();
        if ($obj instanceof WP_Post_Type) {
            $chain[] = ['name' => $obj->labels->name];
        } else {
            $pt  = get_query_var('post_type');
            $pto = $pt ? get_post_type_object(is_array($pt) ? $pt[0] : $pt) : null;
            if ($pto) {
                $chain[] = ['name' => $pto->labels->name];
            }
        }
        return $chain;
    }

    if (is_tax() || is_category() || is_tag()) {
        $term = get_queried_object();
        if ($term && !is_wp_error($term)) {
            $chain[] = ['name' => $term->name];
        }
        return $chain;
    }

    if (!$post_id) {
        return $chain;
    }

    $post = get_post($post_id);
    if (!$post) {
        return $chain;
    }

    // CPT singolo: aggiungi link archive
    if ($post->post_type === 'avvocato') {
        $chain[] = ['name' => __('Avvocati', 'saltelli'), 'url' => get_post_type_archive_link('avvocato')];
    } elseif ($post->post_type === 'competenza') {
        $chain[] = ['name' => __('Competenze', 'saltelli'), 'url' => get_post_type_archive_link('competenza')];
    } elseif ($post->post_type === 'post') {
        $blog_page_id = (int) get_option('page_for_posts');
        if ($blog_page_id) {
            $chain[] = ['name' => get_the_title($blog_page_id), 'url' => get_permalink($blog_page_id)];
        } else {
            $chain[] = ['name' => __('Blog', 'saltelli'), 'url' => home_url('/blog/')];
        }
        // Categoria primaria
        $cats = get_the_category($post_id);
        if (!empty($cats)) {
            $cat = $cats[0];
            $chain[] = ['name' => $cat->name, 'url' => get_category_link($cat->term_id)];
        }
    } elseif ($post->post_type === 'page') {
        // Pagine gerarchiche: ricostruisci catena ancestrale
        $ancestors = array_reverse(get_post_ancestors($post_id));
        foreach ($ancestors as $aid) {
            $chain[] = [
                'name' => get_the_title($aid),
                'url'  => get_permalink($aid),
            ];
        }
    }

    // Pagina corrente — ultimo nodo, no URL
    $chain[] = ['name' => get_the_title($post_id)];

    return $chain;
}

/**
 * Ritorna il numero di FAQ compilate per una competenza.
 * Utile per decidere se iniettare schema FAQPage.
 *
 * @param int $post_id
 * @return int
 */
function saltelli_count_faq($post_id) {
    $faq = saltelli_field('faq', $post_id, []);
    if (!is_array($faq)) {
        return 0;
    }
    $valid = 0;
    foreach ($faq as $row) {
        if (!empty($row['domanda']) && !empty($row['risposta'])) {
            $valid++;
        }
    }
    return $valid;
}

/**
 * Helper: URL canonico della pagina corrente.
 */
function saltelli_canonical_url() {
    if (is_singular()) {
        $url = get_permalink();
    } elseif (is_post_type_archive()) {
        $url = get_post_type_archive_link(get_post_type());
    } elseif (is_category() || is_tag() || is_tax()) {
        $url = get_term_link(get_queried_object());
    } elseif (is_home() || is_front_page()) {
        $url = home_url('/');
    } else {
        $url = home_url(add_query_arg([], $GLOBALS['wp']->request));
    }
    return is_wp_error($url) ? home_url('/') : $url;
}

/**
 * Helper: telefono in formato E.164 dello studio (fallback hardcoded).
 */
function saltelli_studio_phone_e164() {
    return '+390811813119';
}

/**
 * Output di un blocco JSON-LD inline.
 *
 * Nota di compatibilità: alcuni plugin (e.g. iubenda Cookie Law Solution)
 * applicano un output buffer che fa passare l'HTML attraverso DOMDocument::loadHTML
 * + saveHTML. Quel passaggio convertirebbe i caratteri UTF-8 (es. "à") in
 * entità numeriche HTML (&#224;) che però sono *invalide* dentro un JSON
 * inline a `<script>`. Per questo NON usiamo JSON_UNESCAPED_UNICODE: emettiamo
 * `\uXXXX` ASCII-safe, che è JSON valido e immune a DOMDocument.
 *
 * @param array $schema  Schema array (incluso @context).
 * @param int   $extra_flags  Flag aggiuntivi opzionali.
 * @return void Emette `<script type="application/ld+json">…</script>`.
 */
function saltelli_emit_jsonld($schema, $extra_flags = 0) {
    $flags = JSON_UNESCAPED_SLASHES | $extra_flags;
    $json = wp_json_encode($schema, $flags);
    if ($json === false) {
        return;
    }
    echo "\n<script type=\"application/ld+json\">" . $json . "</script>\n";
}

/**
 * Helper: configurazione anagrafica studio.
 * Usato dai partial schema per tenere centralizzati i dati.
 * TODO: spostare in ACF Options Page quando ACF Pro disponibile.
 *
 * Dati confermati dal cliente il 2026-04-28 (sessione Ludovica):
 *  - GPS precisi via Google Maps (40.830267, 14.237217)
 *  - Anno fondazione studio attuale: gennaio 2019
 *  - Social: Facebook share-URL, Instagram, LinkedIn (personale di Emiliano usato come profilo studio)
 */
function saltelli_studio_data() {
    return [
        'legal_name'    => 'Studio Legale Emiliano Saltelli & Partners',
        'alt_names'     => ['Studio Legale Saltelli', 'Studio Saltelli Napoli'],
        'street'        => 'Via Vannella Gaetani, 27',
        'postal_code'   => '80121',
        'locality'      => 'Napoli',
        'region'        => 'Campania',
        'country'       => 'IT',
        'phone'         => '+390811813119',
        'whatsapp'      => '+393517138006',
        'email'         => 'info@studiolegalesaltelli.it',
        'pec'           => 'emilianosaltelli@avvocatinapoli.legalmail.it',
        'vat'           => 'IT06685101211',
        'tax_id'        => '06685101211',
        // GPS confermati 2026-04-28 (cliente — Google Maps).
        'lat'           => 40.830267,
        'lng'           => 14.237217,
        'opens'         => '10:00',
        'closes'        => '19:00',
        'days'          => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
        'price_range'   => '€€',
        // Studio attuale fondato gennaio 2019. Lineage: studio precedente
        // (studiolegaleavvass.it) dal 2008 con ex socio, abilitazione piena 2013.
        'founding_date' => '2019-01',
        // Social — confermati 2026-04-28. URL ripuliti dai parametri di tracking.
        // NOTA: la "pagina LinkedIn studio" è in realtà il profilo personale di
        // Emiliano (https://www.linkedin.com/in/emilianosaltelli/) — lo agganciamo
        // a Person Emiliano (Person.sameAs), NON a Organization.sameAs, per
        // mantenere coerenza semantica.
        'social' => [
            'facebook'  => 'https://www.facebook.com/share/1D1jCY7BnW/',
            'instagram' => 'https://www.instagram.com/studiolegalesaltelli/',
            // 'linkedin_company' — non disponibile (lo studio non ha Company Page).
            // 'google_business_profile' — TODO: da creare in Fase 1 del programma.
        ],
    ];
}

/**
 * LinkedIn personale per @id slug avvocato. Confermati: solo Emiliano (al 2026-04-28).
 * Per Fabiana, Antonia, Stefano si attendono i link individuali.
 */
function saltelli_attorney_linkedin($slug) {
    $map = [
        'emiliano-saltelli'         => 'https://www.linkedin.com/in/emilianosaltelli/',
        // 'fabiana-saltelli'        => '', // TODO Ludovica
        // 'antonia-battista'        => '', // TODO Ludovica
        // 'stefano-gaetano-tedesco' => '', // TODO Ludovica (scope uncertain — vedi memory)
    ];
    return $map[$slug] ?? '';
}
