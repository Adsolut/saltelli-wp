<?php
/**
 * v0.26.0 — Yoast schema graph extensions per GEO/AI Overviews.
 *
 * T1 Person/Attorney lawyer page: già emesso da inc/schema/partial-attorney.php
 *    (NO duplicate via Yoast filter — eviterebbe doppio Person nel grafo).
 *
 * T2 FAQPage forced emit su /costi/ — Yoast Yoast Schema Pieces injection.
 * T3 LegalService priceRange "€800–€3500" + Offer "Prima consulenza gratuita".
 *
 * @package Saltelli
 */

defined('ABSPATH') || exit;

if (!class_exists('\Yoast\WP\SEO\Generators\Schema\Abstract_Schema_Piece')) {
    // Yoast non attivo: niente da fare. partial-attorney.php emette comunque
    // Person via saltelli_emit_jsonld (indipendente da Yoast).
    return;
}

/**
 * v0.26.0 T2 — FAQPage piece scoped a /costi/.
 * Yoast Schema Piece pattern (Yoast 14+).
 *
 * Source 5 FAQ: deve combaciare con il markup hardcoded in page.php
 * (is_page('costi') block sl-costi-w4__faq).
 */
class Saltelli_Schema_Costi_FAQPage extends \Yoast\WP\SEO\Generators\Schema\Abstract_Schema_Piece {

    public function is_needed() {
        return is_page('costi');
    }

    public function generate() {
        $faqs = [
            ['Quanto costa una pratica di diritto tributario?',
             "Range orientativo €800-€3500 a seconda di tipologia atto, importo contestato, necessità di periti. Esempio reale: opposizione cartella esattoriale €5000 → forfait €1200 + €200 contributo unificato."],
            ['Pagamento dilazionato è possibile?',
             "Sì per pratiche oltre €1500. Concordiamo rate trimestrali in funzione del flusso atti. Trasparenza totale: nessun interesse, solo dilazione fisica."],
            ['Se non vinco, devo comunque pagare?',
             "Sì. Le tariffe forensi prevedono onorari per il lavoro svolto, indipendentemente dall'esito (regola del Codice deontologico). Quello che possiamo fare: valutare seriamente in prima consulenza se la causa è effettivamente percorribile."],
            ['Il primo incontro è davvero gratuito?',
             "Sì, sempre. Trenta minuti senza costi né obblighi. Se decidi di non procedere, abbiamo solo investito tempo. Il nostro tempo costa solo se decidiamo insieme di procedere."],
            ['Recupero crediti: solo se vinciamo?',
             "Per pratiche specifiche di recupero crediti < €5000 proponiamo success fee (X% sul recuperato + spese vive). Da concordare in prima consulenza in base alla concretezza del credito."],
        ];

        $main_entity = [];
        foreach ($faqs as $i => $faq) {
            $main_entity[] = [
                '@type' => 'Question',
                '@id'   => $this->context->canonical . '#faq-' . $i,
                'name'  => $faq[0],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => $faq[1],
                ],
            ];
        }

        return [
            '@type'      => 'FAQPage',
            '@id'        => $this->context->canonical . '#faq',
            'mainEntity' => $main_entity,
            'inLanguage' => 'it-IT',
        ];
    }
}

add_filter('wpseo_schema_graph_pieces', function ($pieces, $context) {
    if (is_page('costi')) {
        $pieces[] = new Saltelli_Schema_Costi_FAQPage($context);
    }
    return $pieces;
}, 11, 2);

/**
 * v0.26.0 T3 — LegalService priceRange + offers su /costi/.
 *
 * Override del default Yoast "€€" con il range concordato "€800–€3500".
 * Aggiunge anche un Offer "Prima consulenza gratuita" per AI Overview /
 * Knowledge Panel.
 *
 * Hook tardivo (priorità 12) per applicarsi DOPO che Yoast ha costruito il
 * grafo finale; itera sui pieces e modifica LegalService/LocalBusiness.
 */
add_filter('wpseo_schema_graph', function ($graph, $context) {
    if (!is_page('costi')) {
        return $graph;
    }
    if (!is_array($graph)) {
        return $graph;
    }

    foreach ($graph as &$piece) {
        if (empty($piece['@type'])) {
            continue;
        }
        $type = is_array($piece['@type']) ? $piece['@type'][0] : $piece['@type'];
        if ($type !== 'LegalService' && $type !== 'LocalBusiness' && $type !== 'Organization') {
            continue;
        }

        // priceRange override "€800–€3500" (en dash U+2013)
        $piece['priceRange'] = '€800–€3500';

        // Offer "Prima consulenza gratuita" — non duplicare se già presente
        $has_free_offer = false;
        if (!empty($piece['makesOffer']) && is_array($piece['makesOffer'])) {
            foreach ($piece['makesOffer'] as $existing) {
                if (!empty($existing['name']) && stripos($existing['name'], 'Prima consulenza') !== false) {
                    $has_free_offer = true;
                    break;
                }
            }
        }
        if (!$has_free_offer) {
            $offer = [
                '@type'         => 'Offer',
                'name'          => 'Prima consulenza conoscitiva',
                'price'         => '0',
                'priceCurrency' => 'EUR',
                'description'   => 'Trenta minuti di prima consulenza gratuita, in studio o online. Nessun obbligo, nessun costo nascosto.',
                'availability'  => 'https://schema.org/InStock',
                'category'      => 'Legal consultation',
            ];
            if (empty($piece['makesOffer'])) {
                $piece['makesOffer'] = [$offer];
            } else {
                $piece['makesOffer'][] = $offer;
            }
        }
    }
    unset($piece);

    return $graph;
}, 12, 2);

/**
 * Debug-QA bug-02 fix — sovrascrivi Yoast Organization.sameAs con i social
 * confermati dal cliente (ACF Theme Options Wave 1).
 *
 * Yoast 27.5 emette un Organization piece con sameAs=[6 fake URLs] (Facebook
 * /studiolegalesaltelli, X/legalesaltelli, TikTok, YouTube, ecc.) impostato
 * via Yoast → Settings legacy. Il cliente NON gestisce quei profili.
 *
 * Source of truth (Wave 1+2):
 *  - ACF Theme Options: social_facebook, social_instagram, social_linkedin, social_twitter
 *  - Fallback: saltelli_studio_data() helpers.php (Facebook share URL + Instagram).
 *
 * Note: LinkedIn personale Emiliano va su Person.sameAs in partial-attorney.php,
 * NON Organization (vedi memory project_linkedin_studio).
 *
 * @since 1.0.0 Debug-QA
 */
add_filter('wpseo_schema_graph', function ($graph, $context) {
    if (!is_array($graph)) return $graph;

    // Build authoritative sameAs from ACF Theme Options + fallback.
    $studio_fallback = function_exists('saltelli_studio_data') ? saltelli_studio_data() : [];
    $authoritative_sameas = [];
    foreach (['facebook', 'instagram', 'linkedin', 'twitter'] as $net) {
        $url = function_exists('saltelli_option') ? (string) saltelli_option('social_' . $net, '') : '';
        if ($url === '' && !empty($studio_fallback['social'][$net])) {
            $url = (string) $studio_fallback['social'][$net];
        }
        if ($url !== '') {
            $authoritative_sameas[] = $url;
        }
    }

    if (empty($authoritative_sameas)) return $graph;

    foreach ($graph as &$piece) {
        if (empty($piece['@type'])) continue;
        $type = is_array($piece['@type']) ? $piece['@type'][0] : $piece['@type'];
        // Apply only to Organization-type pieces (not Person).
        if (!in_array($type, ['Organization', 'LegalService', 'LocalBusiness', 'ProfessionalService'], true)) continue;
        $piece['sameAs'] = $authoritative_sameas;
    }
    unset($piece);
    return $graph;
}, 13, 2);
