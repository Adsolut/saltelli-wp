<?php
/**
 * Wave 2 Phase 2 — Page WP custom fields migration.
 *
 * Source: page.php blocks is_page() per /costi/ /casi/ /contatti/ /faq/ + 5 info-shared pages.
 * Target: ACF Field Groups Wave 1 → 5 page (16+10+10+10+16) + info-shared (×5 page = 5 × 16 = 80).
 *
 * Run:
 *   docker compose run --rm -v $PWD/scripts:/scripts wpcli eval-file /scripts/wave2-phase2-pages.php
 */

defined('ABSPATH') || exit;

$total_ok = 0;
$total_attempt = 0;

function w2_update($name, $value, $post_id, &$total_ok, &$total_attempt) {
    $total_attempt++;
    $result = update_field($name, $value, $post_id);
    if ($result) $total_ok++;
    $preview = is_string($value) ? substr($value, 0, 50) : (is_bool($value) ? var_export($value, true) : (string) $value);
    printf("  [%s] post=%-5d %-25s = %s\n", $result ? 'OK' : 'NC', $post_id, $name, $preview);
    return $result;
}

// =====================================================================
// 1. /costi/ (page 2695, 16 fields)
// =====================================================================
echo "\n═══ /costi/ (2695) ═══\n";
$pid = 2695;
w2_update('hero_eyebrow',     '§ Trasparenza · Costi e tariffe', $pid, $total_ok, $total_attempt);
w2_update('hero_h1_pre',      'Costi e prima',                    $pid, $total_ok, $total_attempt);
w2_update('hero_h1_em',       'consulenza.',                      $pid, $total_ok, $total_attempt);
w2_update('hero_lede',        'Trenta minuti gratuiti per ascoltarci, valutare insieme, decidere se procedere. Solo dopo, un preventivo personalizzato basato su complessità, tempi e probabilità di esito.', $pid, $total_ok, $total_attempt);
w2_update('aside_eyebrow',    '§ Prima consulenza',               $pid, $total_ok, $total_attempt);
w2_update('aside_h3',         'GRATUITA · 30 MINUTI · IN STUDIO O ONLINE', $pid, $total_ok, $total_attempt);
w2_update('aside_p',          'Nessun obbligo · Nessun costo nascosto · Riservatezza assoluta', $pid, $total_ok, $total_attempt);
w2_update('aside_cta_label',  'Prenota un incontro →',            $pid, $total_ok, $total_attempt);
w2_update('aside_cta_url',    '/contatti/',                       $pid, $total_ok, $total_attempt);
w2_update('calc_body',
    "<p>Trasparenza è la nostra prima regola. I nostri preventivi considerano tre fattori: complessità della pratica (analisi atti, ricerca giurisprudenza, perizie tecniche), tempo stimato (ore di lavoro su atti, udienze, comunicazioni), probabilità di esito favorevole (incide sulla strategia consigliata).</p>\n<p>Quando possibile, lavoriamo a tariffa forfettaria: ti diamo un numero finale al primo incontro e quello rimane. Quando la complessità non lo permette, lavoriamo a tariffa oraria con budget cap concordato in anticipo. <em>Niente fatturazione a sorpresa, mai.</em></p>",
    $pid, $total_ok, $total_attempt);
w2_update('cta_eyebrow',      '§ Pronto?',                        $pid, $total_ok, $total_attempt);
w2_update('cta_h2',           'La prima consulenza è gratuita. Sempre.', $pid, $total_ok, $total_attempt);
w2_update('cta_p',            'Trenta minuti per ascoltarci, valutare insieme, capire se possiamo esserti utili. Senza obblighi e senza costi nascosti.', $pid, $total_ok, $total_attempt);
w2_update('cta_label',        'Prenota un incontro →',            $pid, $total_ok, $total_attempt);
w2_update('cta_url',          '/contatti/',                       $pid, $total_ok, $total_attempt);
w2_update('cta_trust',        'Risposta entro 24 ore · Riservatezza assoluta', $pid, $total_ok, $total_attempt);

// =====================================================================
// 2. /casi/ (page 2699, 10 fields)
// =====================================================================
echo "\n═══ /casi/ (2699) ═══\n";
$pid = 2699;
w2_update('hero_eyebrow',     '§ Risultati · Casi rappresentativi', $pid, $total_ok, $total_attempt);
w2_update('hero_h1_pre',      'Casi',                              $pid, $total_ok, $total_attempt);
w2_update('hero_h1_em',       'rappresentativi.',                  $pid, $total_ok, $total_attempt);
w2_update('hero_lede',        'Una selezione di vittorie. Identificativi anonimizzati per riservatezza, documentati e verificabili in studio.', $pid, $total_ok, $total_attempt);
// intro_body — empty for now (template non lo renderizza nella struttura corrente).
// Wave 3 può popolare se aggiunge sezione intro editoriale dedicata.
w2_update('intro_body',       '',                                  $pid, $total_ok, $total_attempt);
w2_update('cta_eyebrow',      '§ Prossimo caso',                   $pid, $total_ok, $total_attempt);
w2_update('cta_h2',           'Vorresti vincere il tuo?',          $pid, $total_ok, $total_attempt);
w2_update('cta_p',            "Il primo incontro è gratuito. Diciamo la verità anche quando significa sconsigliare un'azione legale.", $pid, $total_ok, $total_attempt);
w2_update('cta_label',        'Prenota gratuita →',                $pid, $total_ok, $total_attempt);
w2_update('cta_url',          '/contatti/',                        $pid, $total_ok, $total_attempt);

// =====================================================================
// 3. /contatti/ (page 23, 10 fields)
// =====================================================================
echo "\n═══ /contatti/ (23) ═══\n";
$pid = 23;
w2_update('hero_eyebrow',     '§ Contatti · Primo incontro gratuito', $pid, $total_ok, $total_attempt);
w2_update('hero_h1_pre',      'Contatti.',                         $pid, $total_ok, $total_attempt);
w2_update('hero_h1_em',       '',                                  $pid, $total_ok, $total_attempt);
w2_update('hero_lede',        'Chiedi qualsiasi cosa. In qualsiasi momento.', $pid, $total_ok, $total_attempt);
// Map: iframe OpenStreetMap con coordinate Google Business 2026-05-02.
$map_iframe = '<iframe title="Studio Saltelli — Via Vannella Gaetani 27" width="100%" height="100%" frameborder="0" scrolling="no" loading="lazy" src="https://www.openstreetmap.org/export/embed.html?bbox=14.236%2C40.830%2C14.246%2C40.837&amp;layer=mapnik&amp;marker=40.8332541%2C14.2414699"></iframe>';
w2_update('map_iframe',       $map_iframe,                         $pid, $total_ok, $total_attempt);
w2_update('map_caption',      'Chiaia · Napoli',                   $pid, $total_ok, $total_attempt);
w2_update('come_arrivare_title', 'Come arrivare.',                 $pid, $total_ok, $total_attempt);
w2_update('come_arrivare_metro',
    "Linea 6 · Mergellina — 8 minuti a piedi lungo la Riviera di Chiaia.\nNapoli Mergellina (FS) — Stazione FS, 10 minuti a piedi.",
    $pid, $total_ok, $total_attempt);
w2_update('come_arrivare_parking',
    "Parcheggio Mergellina — sosta a pagamento, 5 minuti a piedi.",
    $pid, $total_ok, $total_attempt);
w2_update('trust_signal',     'Riceviamo solo su appuntamento. Risposta entro 24 ore.', $pid, $total_ok, $total_attempt);

// =====================================================================
// 4. /faq/ (page 2705, 10 fields)
// =====================================================================
echo "\n═══ /faq/ (2705) ═══\n";
$pid = 2705;
w2_update('hero_eyebrow',     '§ Risorse · Domande frequenti',      $pid, $total_ok, $total_attempt);
w2_update('hero_h1_pre',      'Domande',                            $pid, $total_ok, $total_attempt);
w2_update('hero_h1_em',       'frequenti.',                         $pid, $total_ok, $total_attempt);
w2_update('hero_lede',        "Le domande più ricorrenti che ci pongono privati e imprese. Sei aree tematiche, oltre 28 risposte, raccolte in un'unica pagina.", $pid, $total_ok, $total_attempt);
w2_update('toc_title',        'Indice',                             $pid, $total_ok, $total_attempt);
w2_update('cta_eyebrow',      '§ Domanda specifica?',               $pid, $total_ok, $total_attempt);
w2_update('cta_h2',           'La tua domanda non è qui?',          $pid, $total_ok, $total_attempt);
w2_update('cta_p',            'Trenta minuti di prima consulenza gratuita per la tua pratica specifica. In studio o online. Risposta entro 24 ore.', $pid, $total_ok, $total_attempt);
w2_update('cta_label',        'Prenota un incontro →',              $pid, $total_ok, $total_attempt);
w2_update('cta_url',          '/contatti/',                         $pid, $total_ok, $total_attempt);

// =====================================================================
// 5. info-shared (5 page · 16 fields each)
// =====================================================================

// guide-gratuite (2706)
echo "\n═══ /guide-gratuite/ (2706) ═══\n";
$pid = 2706;
w2_update('hero_eyebrow',         '§ Risorse · Guide gratuite',     $pid, $total_ok, $total_attempt);
w2_update('hero_h1_pre',          'Schede',                          $pid, $total_ok, $total_attempt);
w2_update('hero_h1_em',           'sintetiche.',                     $pid, $total_ok, $total_attempt);
w2_update('hero_lede',            'Scarica le nostre guide gratuite: dispense pratiche su materie ricorrenti, scritte dai nostri avvocati per privati e imprese.', $pid, $total_ok, $total_attempt);
w2_update('aside_eyebrow',        '§ Disponibili',                   $pid, $total_ok, $total_attempt);
w2_update('aside_h3',             '12 schede in PDF · gratuite · no email obbligatoria', $pid, $total_ok, $total_attempt);
w2_update('aside_p',              'Senza registrazione · Aggiornamento periodico · Lettura ~10 minuti', $pid, $total_ok, $total_attempt);
w2_update('aside_cta_label',      '',                                $pid, $total_ok, $total_attempt);
w2_update('aside_cta_url',        '',                                $pid, $total_ok, $total_attempt);
w2_update('body_content',
    "<p>Schede sintetiche e gratuite, scritte dai nostri avvocati per privati e imprese. Ogni guida copre una materia ricorrente con linguaggio chiaro, esempi reali, e indicazioni pratiche su come muoversi nei primi 7 giorni.</p>\n<p>Sono pensate per chi vuole una prima orientazione prima di decidere se rivolgersi a un avvocato. Le aggiorniamo periodicamente quando cambia la giurisprudenza o la normativa.</p>",
    $pid, $total_ok, $total_attempt);
w2_update('cta_final_eyebrow',    '§ Pronto?',                       $pid, $total_ok, $total_attempt);
w2_update('cta_final_h2',         'Hai bisogno di un caso specifico?', $pid, $total_ok, $total_attempt);
w2_update('cta_final_p',          'Le schede generali non sostituiscono una consulenza personalizzata. Trenta minuti gratuiti per valutare la tua pratica.', $pid, $total_ok, $total_attempt);
w2_update('cta_final_cta_label',  'Prenota un primo incontro →',     $pid, $total_ok, $total_attempt);
w2_update('cta_final_cta_url',    '/contatti/',                      $pid, $total_ok, $total_attempt);
w2_update('cta_final_trust',      'Risposta entro 24 ore · Riservatezza assoluta', $pid, $total_ok, $total_attempt);

// come-lavoriamo (2709)
echo "\n═══ /come-lavoriamo/ (2709) ═══\n";
$pid = 2709;
w2_update('hero_eyebrow',         '§ Lo studio · Come lavoriamo',    $pid, $total_ok, $total_attempt);
w2_update('hero_h1_pre',          'Ascolto prima,',                  $pid, $total_ok, $total_attempt);
w2_update('hero_h1_em',           'carte dopo.',                     $pid, $total_ok, $total_attempt);
w2_update('hero_lede',            "Crediamo che il diritto sia, prima di tutto, un'arte di ascolto. Le carte vengono dopo. Per questo non offriamo pacchetti né formule.", $pid, $total_ok, $total_attempt);
w2_update('aside_eyebrow',        '§ Tre principi',                  $pid, $total_ok, $total_attempt);
w2_update('aside_h3',             'Ascoltiamo · Lavoriamo in atelier · Diciamo la verità', $pid, $total_ok, $total_attempt);
w2_update('aside_p',              'Primo incontro gratuito · Una pratica, un avvocato · Onesta valutazione percorribilità', $pid, $total_ok, $total_attempt);
w2_update('aside_cta_label',      '',                                $pid, $total_ok, $total_attempt);
w2_update('aside_cta_url',        '',                                $pid, $total_ok, $total_attempt);
w2_update('body_content',
    "<p>Ascoltiamo prima delle carte. Il primo incontro dura il tempo necessario, è gratuito e dedicato esclusivamente a capire la tua storia. Le scartoffie le firmiamo solo quando abbiamo capito cosa serve davvero.</p>\n<p>Lavoriamo in atelier: ogni pratica è seguita personalmente da uno dei quattro avvocati, dall'inizio alla fine. Niente call center, niente passaggi di mano, niente \"il collega le richiamerà\".</p>\n<p>Diciamo la verità anche quando significa sconsigliare un'azione legale. La nostra reputazione vale più di un mandato. Se la causa non è percorribile, te lo diciamo subito.</p>",
    $pid, $total_ok, $total_attempt);
w2_update('cta_final_eyebrow',    '§ Pronto?',                       $pid, $total_ok, $total_attempt);
w2_update('cta_final_h2',         'Vorresti raccontarci la tua pratica?', $pid, $total_ok, $total_attempt);
w2_update('cta_final_p',          'Trenta minuti di prima consulenza gratuita. In studio o online. Risposta entro 24 ore.', $pid, $total_ok, $total_attempt);
w2_update('cta_final_cta_label',  'Prenota un incontro →',           $pid, $total_ok, $total_attempt);
w2_update('cta_final_cta_url',    '/contatti/',                      $pid, $total_ok, $total_attempt);
w2_update('cta_final_trust',      'Risposta entro 24 ore · Riservatezza assoluta', $pid, $total_ok, $total_attempt);

// prima-consulenza (2708)
echo "\n═══ /prima-consulenza/ (2708) ═══\n";
$pid = 2708;
w2_update('hero_eyebrow',         '§ Servizio · Prima consulenza',   $pid, $total_ok, $total_attempt);
w2_update('hero_h1_pre',          'Trenta minuti',                   $pid, $total_ok, $total_attempt);
w2_update('hero_h1_em',           'gratuiti.',                       $pid, $total_ok, $total_attempt);
w2_update('hero_lede',            'Trenta minuti di prima consulenza conoscitiva, gratuita. In studio a Chiaia o in videocall. Senza obblighi, senza costi nascosti.', $pid, $total_ok, $total_attempt);
w2_update('aside_eyebrow',        '§ Modalità',                      $pid, $total_ok, $total_attempt);
w2_update('aside_h3',             'GRATUITA · 30 MIN · IN STUDIO O ONLINE', $pid, $total_ok, $total_attempt);
w2_update('aside_p',              'Nessun obbligo · Nessun costo nascosto · Riservatezza assoluta', $pid, $total_ok, $total_attempt);
w2_update('aside_cta_label',      '',                                $pid, $total_ok, $total_attempt);
w2_update('aside_cta_url',        '',                                $pid, $total_ok, $total_attempt);
w2_update('body_content',
    "<p>Trenta minuti gratuiti, in studio a Chiaia o in videocall. Sufficienti per ascoltare la pratica, valutare la percorribilità e decidere insieme se procedere. Senza obblighi, senza costi nascosti.</p>\n<p>Solo dopo il primo incontro formuliamo un preventivo personalizzato basato su complessità, tempi e probabilità di esito. Il preventivo è scritto, fisso o a percentuale del beneficio. Lo concordiamo prima del mandato.</p>",
    $pid, $total_ok, $total_attempt);
w2_update('cta_final_eyebrow',    '§ Pronto?',                       $pid, $total_ok, $total_attempt);
w2_update('cta_final_h2',         'Iniziamo.',                       $pid, $total_ok, $total_attempt);
w2_update('cta_final_p',          'Risposta entro 24 ore. Riservatezza assoluta. Cancellazione 1 click.', $pid, $total_ok, $total_attempt);
w2_update('cta_final_cta_label',  'Prenota un incontro →',           $pid, $total_ok, $total_attempt);
w2_update('cta_final_cta_url',    '/contatti/',                      $pid, $total_ok, $total_attempt);
w2_update('cta_final_trust',      'Risposta entro 24 ore · Riservatezza assoluta', $pid, $total_ok, $total_attempt);

// lavora-con-noi (372)
echo "\n═══ /lavora-con-noi/ (372) ═══\n";
$pid = 372;
w2_update('hero_eyebrow',         '§ Studio · Carriera',             $pid, $total_ok, $total_attempt);
w2_update('hero_h1_pre',          'Cerchiamo',                       $pid, $total_ok, $total_attempt);
w2_update('hero_h1_em',           'praticanti.',                     $pid, $total_ok, $total_attempt);
w2_update('hero_lede',            'Cerchiamo praticanti motivati e curiosi, disponibili a un percorso strutturato in tutte le materie dello studio. Nessuna formula stage-mascherato.', $pid, $total_ok, $total_attempt);
w2_update('aside_eyebrow',        '§ Cosa offriamo',                 $pid, $total_ok, $total_attempt);
w2_update('aside_h3',             'Mentorship · 18 mesi · Compenso adeguato', $pid, $total_ok, $total_attempt);
w2_update('aside_p',              'Mentorship 1-1 con i quattro avvocati · Rotazione su tutte le materie · Compenso conforme al CCNL', $pid, $total_ok, $total_attempt);
w2_update('aside_cta_label',      '',                                $pid, $total_ok, $total_attempt);
w2_update('aside_cta_url',        '',                                $pid, $total_ok, $total_attempt);
w2_update('body_content',
    "<p>Cerchiamo praticanti motivati e curiosi, con voglia di studiare in profondità. Offriamo un percorso strutturato di 18 mesi su tutte le materie dello studio: tributario, lavoro, famiglia LGBTQ+, immobiliare, condominiale, contenzioso.</p>\n<p>Niente formula stage-mascherato. Compenso conforme al CCNL forense, mentorship 1-1 con i quattro avvocati, casi reali fin dalla prima settimana. Cerchiamo persone che vogliano fare l'avvocato in maniera seria.</p>",
    $pid, $total_ok, $total_attempt);
w2_update('cta_final_eyebrow',    '§ Pronto?',                       $pid, $total_ok, $total_attempt);
w2_update('cta_final_h2',         'Inviaci il tuo curriculum.',      $pid, $total_ok, $total_attempt);
w2_update('cta_final_p',          'Solo CV reali, no autocandidature standardizzate. Risposta entro 7 giorni lavorativi.', $pid, $total_ok, $total_attempt);
w2_update('cta_final_cta_label',  'Scrivici →',                      $pid, $total_ok, $total_attempt);
w2_update('cta_final_cta_url',    '/contatti/',                      $pid, $total_ok, $total_attempt);
w2_update('cta_final_trust',      'Risposta entro 24 ore · Riservatezza assoluta', $pid, $total_ok, $total_attempt);

// richiedi-preventivo (2710)
echo "\n═══ /richiedi-preventivo/ (2710) ═══\n";
$pid = 2710;
w2_update('hero_eyebrow',         '§ Servizio · Richiedi un preventivo', $pid, $total_ok, $total_attempt);
w2_update('hero_h1_pre',          'Richiedi un',                     $pid, $total_ok, $total_attempt);
w2_update('hero_h1_em',           'preventivo.',                     $pid, $total_ok, $total_attempt);
w2_update('hero_lede',            'Compila un breve modulo per ricevere un preventivo personalizzato. Trasparente, vincolato alla complessità reale della pratica.', $pid, $total_ok, $total_attempt);
w2_update('aside_eyebrow',        '§ Come funziona',                 $pid, $total_ok, $total_attempt);
w2_update('aside_h3',             'Preventivo scritto in 48h',       $pid, $total_ok, $total_attempt);
w2_update('aside_p',              'Risposta entro 24h · Preventivo dettagliato scritto · Onorario, contributo unificato, spese vive separati', $pid, $total_ok, $total_attempt);
w2_update('aside_cta_label',      '',                                $pid, $total_ok, $total_attempt);
w2_update('aside_cta_url',        '',                                $pid, $total_ok, $total_attempt);
w2_update('body_content',
    '',  // template non ha switch case fallback per richiedi-preventivo (nessun body editorial). Lascia vuoto.
    $pid, $total_ok, $total_attempt);
w2_update('cta_final_eyebrow',    '§ Pronto?',                       $pid, $total_ok, $total_attempt);
w2_update('cta_final_h2',         'Pronto a richiedere un preventivo?', $pid, $total_ok, $total_attempt);
w2_update('cta_final_p',          'Trenta minuti di prima consulenza gratuita prima del preventivo. Niente sorprese, niente costi nascosti.', $pid, $total_ok, $total_attempt);
w2_update('cta_final_cta_label',  'Apri modulo →',                   $pid, $total_ok, $total_attempt);
w2_update('cta_final_cta_url',    '/contatti/',                      $pid, $total_ok, $total_attempt);
w2_update('cta_final_trust',      'Risposta entro 24 ore · Riservatezza assoluta', $pid, $total_ok, $total_attempt);

echo "\n";
echo "═══ Phase 2 SUMMARY ═══\n";
echo "Updates: $total_ok / $total_attempt fields\n";
