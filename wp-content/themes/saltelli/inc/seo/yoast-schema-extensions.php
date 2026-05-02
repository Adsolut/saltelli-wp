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
