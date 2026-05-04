<?php
/**
 * Wave 2 Phase 3 — CPT items population.
 *
 * Crea ~80 CPT items idempotente (skip se title esiste). Source da page.php blocks
 * + helpers.php (saltelli_all_cases, saltelli_attorney_formazione).
 *
 * Run:
 *   docker compose run --rm -v $PWD/scripts:/scripts wpcli eval-file /scripts/wave2-phase3-cpt-items.php
 */

defined('ABSPATH') || exit;

$total_created = 0;
$total_skipped = 0;

/** Crea CPT item se non esistente (match per post_title). Ritorna ID. */
function w2_get_or_create($post_type, $title, $extra = [], &$created, &$skipped) {
    $existing = get_posts([
        'post_type'      => $post_type,
        'title'          => $title,
        'post_status'    => 'publish',
        'numberposts'    => 1,
        'fields'         => 'ids',
    ]);
    if (!empty($existing)) {
        $skipped++;
        return (int) $existing[0];
    }
    $id = wp_insert_post(array_merge([
        'post_type'   => $post_type,
        'post_status' => 'publish',
        'post_title'  => $title,
    ], $extra), true);
    if (is_wp_error($id)) {
        echo "  [ERR] $post_type · $title → " . $id->get_error_message() . "\n";
        return 0;
    }
    $created++;
    return (int) $id;
}

function w2_set_taxonomy($post_id, $taxonomy, $term_slug, $term_name) {
    $term = get_term_by('slug', $term_slug, $taxonomy);
    if (!$term) {
        $r = wp_insert_term($term_name, $taxonomy, ['slug' => $term_slug]);
        $term_id = is_wp_error($r) ? 0 : (int) $r['term_id'];
    } else {
        $term_id = (int) $term->term_id;
    }
    if ($term_id) {
        wp_set_object_terms($post_id, [$term_id], $taxonomy, false);
    }
    return $term_id;
}

// =====================================================================
// 1. saltelli_modalita (3 items) — da /costi/ § 01
// =====================================================================
echo "═══ saltelli_modalita ═══\n";
$modalita = [
    [
        'title'       => 'Vieni a Chiaia',
        'num_label'   => '01 / Modalità classica',
        'body'        => 'Via Vannella Gaetani 27, sala riunioni del nostro studio. Lunedì-venerdì 09:30-18:30, su appuntamento.',
        'trust_mini'  => 'Caffè incluso',
        'menu_order'  => 1,
    ],
    [
        'title'       => 'Videocall riservata',
        'num_label'   => '02 / Modalità remota',
        'body'        => 'Google Meet, Zoom o piattaforma a tua scelta. Ideale se vivi fuori Napoli o per pratiche urgenti.',
        'trust_mini'  => 'Stesso valore, zero spostamento',
        'menu_order'  => 2,
    ],
    [
        'title'       => 'Per casi semplici',
        'num_label'   => '03 / Modalità rapida',
        'body'        => 'Per situazioni che richiedono solo un primo orientamento o verifica di percorribilità.',
        'trust_mini'  => 'Massimo 30 minuti',
        'menu_order'  => 3,
    ],
];
foreach ($modalita as $m) {
    $id = w2_get_or_create('saltelli_modalita', $m['title'], ['menu_order' => $m['menu_order']], $total_created, $total_skipped);
    if ($id) {
        update_field('num_label',  $m['num_label'],  $id);
        update_field('title',      $m['title'],      $id);
        update_field('body',       $m['body'],       $id);
        update_field('trust_mini', $m['trust_mini'], $id);
        echo "  · #$id $m[title]\n";
    }
}

// =====================================================================
// 2. saltelli_scenario (3 items) — da /costi/ § 02
// =====================================================================
echo "\n═══ saltelli_scenario ═══\n";
$scenari = [
    [
        'title'       => 'Non procediamo',
        'num_label'   => '01 / NON PROCEDIAMO',
        'body'        => 'Se la pratica non ha solidi presupposti, te lo diciamo subito. Ti suggeriamo un percorso alternativo o ti rimandiamo a un professionista più indicato.',
        'trust_mini'  => 'Risparmio: 100% costi inutili',
        'menu_order'  => 1,
    ],
    [
        'title'       => 'Pratica semplice — tariffa forfettaria',
        'num_label'   => '02 / PRATICA SEMPLICE — TARIFFA FORFETTARIA',
        'body'        => 'Se la complessità è prevedibile, ti proponiamo un preventivo a forfait. Tutto incluso, nessuna sorpresa successiva.',
        'trust_mini'  => 'Trasparenza: tariffa fissa concordata',
        'menu_order'  => 2,
    ],
    [
        'title'       => 'Pratica complessa — tariffa oraria',
        'num_label'   => '03 / PRATICA COMPLESSA — TARIFFA ORARIA',
        'body'        => 'Se richiede analisi approfondita o iter giudiziale lungo, formuliamo preventivo orario con stima totale + check-in ogni 10 ore lavorate.',
        'trust_mini'  => 'Controllo: budget capped + reportistica',
        'menu_order'  => 3,
    ],
];
foreach ($scenari as $s) {
    $id = w2_get_or_create('saltelli_scenario', $s['title'], ['menu_order' => $s['menu_order']], $total_created, $total_skipped);
    if ($id) {
        update_field('num_label',  $s['num_label'],  $id);
        update_field('title',      $s['title'],      $id);
        update_field('body',       $s['body'],       $id);
        update_field('trust_mini', $s['trust_mini'], $id);
        echo "  · #$id $s[title]\n";
    }
}

// =====================================================================
// 3. saltelli_principio (3 items) — da /chi-siamo/ § 04
// =====================================================================
echo "\n═══ saltelli_principio ═══\n";
$principi = [
    ['num' => '01', 'title' => 'Ascoltiamo prima',     'desc' => 'Il primo incontro è gratuito e dura il tempo necessario. Capire la storia viene sempre prima delle carte.'],
    ['num' => '02', 'title' => 'Lavoriamo in atelier', 'desc' => 'Ogni pratica è seguita personalmente da uno dei quattro avvocati. Niente call center, niente passaggi.'],
    ['num' => '03', 'title' => 'Diciamo la verità',    'desc' => "Anche quando significa sconsigliare un'azione legale. La nostra reputazione vale più di un mandato."],
];
foreach ($principi as $i => $p) {
    $id = w2_get_or_create('saltelli_principio', $p['title'], ['menu_order' => $i + 1], $total_created, $total_skipped);
    if ($id) {
        update_field('num',   $p['num'],   $id);
        update_field('title', $p['title'], $id);
        update_field('desc',  $p['desc'],  $id);
        echo "  · #$id $p[title]\n";
    }
}

// =====================================================================
// 4. saltelli_trust (4 items) — da /costi/ § 05
// =====================================================================
echo "\n═══ saltelli_trust ═══\n";
$trust = [
    ['title' => 'Iscritti Ordine Avvocati Napoli',     'label' => 'Ordine',      'valore' => 'Iscritti Ordine Avvocati Napoli'],
    ['title' => 'P.IVA 06685101211',                   'label' => 'P.IVA',       'valore' => 'P.IVA 06685101211'],
    ['title' => 'Codice deontologico forense',         'label' => 'Codice',      'valore' => 'Codice deontologico forense'],
    ['title' => 'Riservatezza assoluta',               'label' => 'Riservatezza','valore' => 'Riservatezza assoluta'],
];
foreach ($trust as $i => $t) {
    $id = w2_get_or_create('saltelli_trust', $t['title'], ['menu_order' => $i + 1], $total_created, $total_skipped);
    if ($id) {
        update_field('label',  $t['label'],  $id);
        update_field('valore', $t['valore'], $id);
        echo "  · #$id $t[title]\n";
    }
}

// =====================================================================
// 5. saltelli_caso (10 items) — da saltelli_all_cases()
// =====================================================================
echo "\n═══ saltelli_caso ═══\n";
$casi = function_exists('saltelli_all_cases') ? saltelli_all_cases() : [];
$cat_slug_map = [
    'Imprese'     => 'imprese',
    'Privati'     => 'privati',
    'Contenzioso' => 'contenzioso',
    'Altri'       => 'altri',
];
foreach ($casi as $i => $c) {
    $title    = (string) $c['id'];
    $cat_name = (string) $c['cat'];
    $cat_slug = $cat_slug_map[$cat_name] ?? sanitize_title($cat_name);
    $outcome_combined = trim((string) $c['outcome'] . ($c['lbl'] !== '' ? ' · ' . $c['lbl'] : ''));
    $extra = ['menu_order' => $i + 1];
    if (!empty($c['featured'])) {
        $extra['post_excerpt'] = 'featured';  // marker per Wave 3 query featured
    }
    $id = w2_get_or_create('saltelli_caso', $title, $extra, $total_created, $total_skipped);
    if ($id) {
        update_field('id_label',       $title,            $id);
        update_field('descrizione',    (string) $c['desc'],$id);
        update_field('outcome_label',  $outcome_combined, $id);
        w2_set_taxonomy($id, 'caso_categoria', $cat_slug, $cat_name);
        echo "  · #$id $title (" . $cat_name . ") $outcome_combined\n";
    }
}

// =====================================================================
// 6. saltelli_faq (28 items) — da /faq/ aggregator topics
// =====================================================================
echo "\n═══ saltelli_faq ═══\n";
$faq_topics = [
    'tributario' => [
        'name' => 'Diritto tributario',
        'faqs' => [
            ['Quando conviene impugnare una cartella esattoriale?', 'Le cartelle vanno impugnate entro 60 giorni dalla notifica davanti alla Corte di Giustizia Tributaria competente. Lo Studio valuta gratuitamente la fondatezza dell\'impugnazione nel primo incontro.'],
            ['Cosa fare se l\'Agenzia delle Entrate avvia un accertamento sintetico?', 'Prima dell\'accertamento si apre un contraddittorio preventivo: è la fase più delicata. Documentare correttamente la propria posizione in questa sede può evitare il contenzioso.'],
            ['Quali sono i tempi medi di un contenzioso tributario?', 'Primo grado in CGT: 12-18 mesi. Appello in CGT 2: ulteriori 18-24 mesi. Cassazione: 24-36 mesi. La sospensione cautelare è quasi sempre concedibile.'],
            ['Si possono rateizzare le somme dovute?', 'Sì, fino a 72 rate mensili (120 in casi di grave difficoltà). Lo Studio assiste anche nella negoziazione dei piani di rateizzazione.'],
            ['Quanto costa un contenzioso tributario?', 'Il primo incontro è gratuito. Il preventivo è scritto, fisso o a percentuale del beneficio. Le parcelle seguono i parametri ministeriali, sempre concordate prima del mandato.'],
        ],
    ],
    'lavoro' => [
        'name' => 'Diritto del lavoro',
        'faqs' => [
            ['Il licenziamento è impugnabile? Entro quando?', 'Sì, entro 60 giorni dalla comunicazione (180 giorni per discriminazione). Lo Studio valuta gratuitamente la fondatezza nel primo incontro.'],
            ['Cos\'è il mobbing e come si dimostra?', 'Il mobbing richiede prova documentale di vessazioni reiterate. Servono messaggi, testimoni, note mediche. Lo Studio coordina la raccolta probatoria.'],
            ['Cosa cambia tra contestazione disciplinare e licenziamento?', 'La contestazione è il primo step: difesa scritta entro 5 giorni è critica per evitare il provvedimento.'],
            ['Sono lavoratore autonomo: che tutele ho?', 'Anche il lavoro autonomo gode di tutele crescenti (legge 81/2017): equo compenso, recesso illegittimo, dipendenza economica.'],
            ['INPS contestato: cosa fare?', 'Ricorso amministrativo entro 90 giorni, poi giudiziale entro un anno. Lo Studio assiste in entrambe le sedi.'],
        ],
    ],
    'lgbtq' => [
        'name' => 'Famiglia LGBTQ+',
        'faqs' => [
            ['L\'unione civile dà gli stessi diritti del matrimonio?', 'L\'unione civile (legge 76/2016) dà la maggior parte dei diritti del matrimonio salvo adozione e fecondazione assistita. Trascrizione, eredità, pensione di reversibilità: sì.'],
            ['Trascrizione di nascita all\'estero (PMA o GPA): è possibile?', 'Dipende dalla giurisdizione di nascita. Cassazione 38162/2022 e successive aprono spiragli per trascrizione integrale. Lo Studio ha ottenuto il primo riconoscimento in Campania nel 2023.'],
            ['Stepchild adoption: in quali casi è possibile?', 'Adozione coparentale (art. 44 lett. d L.184/1983) su minore già genitore biologico del partner. Procedura giudiziale, esito favorevole consolidato post-Cassazione 2014.'],
            ['Cosa succede in caso di separazione tra coppie LGBTQ+?', 'Per unioni civili: scioglimento giudiziale come divorzio. Per coppie di fatto: contratti di convivenza. Affido figli: principio del miglior interesse del minore.'],
            ['L\'identità di genere è riconosciuta legalmente?', 'Sì, legge 164/1982. Procedura giudiziale o amministrativa post-Cassazione 15138/2015. Lo Studio assiste in tutti i passaggi.'],
        ],
    ],
    'costi' => [
        'name' => 'Costi e tariffe',
        'faqs' => [
            ['Quanto costa una pratica di diritto tributario?', 'Range orientativo €800-€3500 a seconda di tipologia atto, importo contestato, necessità di periti. Esempio: opposizione cartella €5000 → forfait €1200.'],
            ['Pagamento dilazionato è possibile?', 'Sì per pratiche oltre €1500. Concordiamo rate trimestrali in funzione del flusso atti. Trasparenza totale: nessun interesse, solo dilazione fisica.'],
            ['Se non vinco, devo comunque pagare?', 'Sì. Le tariffe forensi prevedono onorari per il lavoro svolto, indipendentemente dall\'esito (Codice deontologico). Possiamo valutare in prima consulenza la percorribilità della causa.'],
            ['Recupero crediti: solo se vinciamo?', 'Per pratiche specifiche di recupero crediti < €5000 proponiamo success fee (X% sul recuperato + spese vive). Da concordare in prima consulenza.'],
        ],
    ],
    'metodo' => [
        'name' => 'Come lavoriamo',
        'faqs' => [
            ['Chi seguirà la mia pratica?', 'Uno dei quattro avvocati personalmente, dall\'inizio alla fine. Niente call center, niente passaggi. Lavoriamo in atelier.'],
            ['Posso scegliere l\'avvocato?', 'Sì. Nel primo incontro valutiamo insieme chi è più indicato per la tua materia. Ti presentiamo l\'avvocato di riferimento prima del mandato.'],
            ['Quanto è davvero gratuito il primo incontro?', '30 minuti, in studio o videocall, senza obblighi. Se decidi di non procedere, abbiamo solo investito tempo. Costa solo se decidiamo insieme di procedere.'],
            ['Cosa succede se la mia causa non è percorribile?', 'Te lo diciamo subito. Ti suggeriamo un percorso alternativo o ti rimandiamo a un professionista più indicato. La nostra reputazione vale più di un mandato.'],
        ],
    ],
    'prima-consulenza' => [
        'name' => 'Prima consulenza',
        'faqs' => [
            ['Devo portare documenti al primo incontro?', 'Se hai documenti relativi alla pratica (contratti, cartelle, lettere), portali. Altrimenti basta una sintesi orale della situazione.'],
            ['Posso fare videocall invece di venire in studio?', 'Sì. Google Meet, Zoom o piattaforma a tua scelta. Stessa efficacia, zero spostamento.'],
            ['Quanto preavviso serve per fissare l\'appuntamento?', 'Riceviamo solo su appuntamento. Tipicamente entro 3-5 giorni lavorativi. Per urgenze contattaci telefonicamente.'],
            ['Posso portare un familiare o un consulente?', 'Sì, se ritieni utile. Lo Studio si adatta alle tue esigenze comunicative.'],
            ['Il primo incontro è in italiano?', 'Sì. Disponibilità anche in inglese su richiesta (Emiliano e Antonia parlano inglese fluente).'],
        ],
    ],
];

$faq_count = 0;
foreach ($faq_topics as $topic_slug => $topic) {
    foreach ($topic['faqs'] as $idx => $qa) {
        [$question, $answer] = $qa;
        $faq_count++;
        $id = w2_get_or_create('saltelli_faq', $question, ['menu_order' => $faq_count], $total_created, $total_skipped);
        if ($id) {
            update_field('domanda',  $question, $id);
            update_field('risposta', $answer,   $id);
            w2_set_taxonomy($id, 'faq_topic', $topic_slug, $topic['name']);
        }
    }
}
echo "  → " . $faq_count . " FAQ items processed across " . count($faq_topics) . " topics\n";

// =====================================================================
// 7. saltelli_formazione (12 items) — 4 lawyer × 3 entries from saltelli_attorney_formazione
// =====================================================================
echo "\n═══ saltelli_formazione ═══\n";
$lawyer_slugs = ['emiliano-saltelli', 'fabiana-saltelli', 'antonia-battista', 'stefano-gaetano-tedesco'];
$lawyer_first = [
    'emiliano-saltelli'        => 'Emiliano',
    'fabiana-saltelli'         => 'Fabiana',
    'antonia-battista'         => 'Antonia',
    'stefano-gaetano-tedesco'  => 'Stefano',
];
foreach ($lawyer_slugs as $slug) {
    $rows = function_exists('saltelli_attorney_formazione') ? saltelli_attorney_formazione($slug) : [];
    foreach ($rows as $idx => $row) {
        // Title prefisso "Lawyer · Anno · Titolo" per uniqueness e leggibilità admin.
        $title = $lawyer_first[$slug] . ' · ' . $row['anno'] . ' · ' . $row['titolo'];
        $id = w2_get_or_create('saltelli_formazione', $title, ['menu_order' => $idx + 1], $total_created, $total_skipped);
        if ($id) {
            update_field('anno',   (string) $row['anno'],        $id);
            update_field('titolo', (string) $row['titolo'],      $id);
            update_field('ente',   (string) $row['istituzione'], $id);
            // Custom meta lawyer_slug: usato in Phase 4 per popolare avvocato.formazione
            update_post_meta($id, 'lawyer_slug', $slug);
        }
    }
    echo "  → " . $lawyer_first[$slug] . " (" . count($rows) . " formazione items)\n";
}

// =====================================================================
// 8. saltelli_guida (placeholder for Elena to populate post-launch)
// =====================================================================
echo "\n═══ saltelli_guida ═══\n";
// Per ora nessun PDF reale. /guide-gratuite/ template non lista guide hardcoded
// (è un info-page+ generico). Skip creation: lasciamo Elena creare le guide
// post-launch quando avrà i PDF + abstract reali.
echo "  → SKIP: no hardcoded guide nel template, Elena popolerà post-launch\n";

echo "\n";
echo "═══ Phase 3 SUMMARY ═══\n";
echo "Created: $total_created · Skipped (already existed): $total_skipped\n";
