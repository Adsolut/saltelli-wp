<?php
/**
 * Debug-QA bug-04 fix — re-migration ACF data per slug-based lookup.
 *
 * Risolve il page-id mismatch tra Docker locale e droplet staging:
 *  - I dati ACF Wave 2 sono stati popolati su page IDs LOCALI (Docker WP)
 *  - Su droplet questi IDs mappano a pagine DIVERSE
 *  - Conseguenza: 5 page WP hanno dati ACF SBAGLIATI o VUOTI
 *
 * Strategia:
 *  1. Re-popola le pagine CORRECT (slug lookup) con i valori canonici
 *     da wave2-phase2-pages.php
 *  2. Cleanup: rimuove i fields ACF Wave 1 dalle pagine WRONG (CPT competenza
 *     "Diritto societario" / "Contrattualistica" / page "Glossario legale")
 *
 * NB: NON tocca post_content, _thumbnail_id, o tassonomie. Solo ACF fields
 * Wave 1 (hero_*, aside_*, cta_*, body_content, map_*, come_*, trust_*).
 *
 * Idempotente: rerunnable senza side-effects.
 *
 * Run on droplet:
 *   ssh deploy@178.62.207.50 "sudo -u www-data wp --path=/var/www/saltelli eval-file -" < scripts/debug-qa-fix-page-id-mismatch.php
 */

defined('ABSPATH') || exit;

$total_ok = 0;
$total_attempt = 0;
$total_clean = 0;

function fix_set($name, $value, $post_id, &$ok, &$attempt) {
    $attempt++;
    $r = update_field($name, $value, $post_id);
    if ($r) $ok++;
    return $r;
}

function fix_clean($name, $post_id, &$cleaned) {
    if (delete_field($name, $post_id)) {
        $cleaned++;
    }
}

// ============================================================
// /faq/ — repopola via slug lookup (10 fields)
// ============================================================
$faq_page = get_page_by_path('faq');
if (!$faq_page) { echo "ERROR: /faq/ page not found\n"; exit(1); }
$pid = $faq_page->ID;
echo "═══ /faq/ (id=$pid) ═══\n";
fix_set('hero_eyebrow', '§ Risorse · Domande frequenti', $pid, $total_ok, $total_attempt);
fix_set('hero_h1_pre', 'Domande', $pid, $total_ok, $total_attempt);
fix_set('hero_h1_em', 'frequenti.', $pid, $total_ok, $total_attempt);
fix_set('hero_lede', "Le domande più ricorrenti che ci pongono privati e imprese. Sei aree tematiche, oltre 28 risposte, raccolte in un'unica pagina.", $pid, $total_ok, $total_attempt);
fix_set('toc_title', 'Indice', $pid, $total_ok, $total_attempt);
fix_set('cta_eyebrow', '§ Domanda specifica?', $pid, $total_ok, $total_attempt);
fix_set('cta_h2', 'La tua domanda non è qui?', $pid, $total_ok, $total_attempt);
fix_set('cta_p', 'Trenta minuti di prima consulenza gratuita per la tua pratica specifica. In studio o online. Risposta entro 24 ore.', $pid, $total_ok, $total_attempt);
fix_set('cta_label', 'Prenota un incontro →', $pid, $total_ok, $total_attempt);
fix_set('cta_url', '/contatti/', $pid, $total_ok, $total_attempt);

// ============================================================
// info-shared 5 page — repopola via slug lookup (16 fields each)
// ============================================================
$info_data = [
    'guide-gratuite' => [
        'hero_eyebrow'         => '§ Risorse · Guide gratuite',
        'hero_h1_pre'          => 'Schede',
        'hero_h1_em'           => 'sintetiche.',
        'hero_lede'            => 'Scarica le nostre guide gratuite: dispense pratiche su materie ricorrenti, scritte dai nostri avvocati per privati e imprese.',
        'aside_eyebrow'        => '§ Disponibili',
        'aside_h3'             => '12 schede in PDF · gratuite · no email obbligatoria',
        'aside_p'              => 'Senza registrazione · Aggiornamento periodico · Lettura ~10 minuti',
        'aside_cta_label'      => '',
        'aside_cta_url'        => '',
        'body_content'         => "<p>Schede sintetiche e gratuite, scritte dai nostri avvocati per privati e imprese. Ogni guida copre una materia ricorrente con linguaggio chiaro, esempi reali, e indicazioni pratiche su come muoversi nei primi 7 giorni.</p>\n<p>Sono pensate per chi vuole una prima orientazione prima di decidere se rivolgersi a un avvocato. Le aggiorniamo periodicamente quando cambia la giurisprudenza o la normativa.</p>",
        'cta_final_eyebrow'    => '§ Pronto?',
        'cta_final_h2'         => 'Hai bisogno di un caso specifico?',
        'cta_final_p'          => 'Le schede generali non sostituiscono una consulenza personalizzata. Trenta minuti gratuiti per valutare la tua pratica.',
        'cta_final_cta_label'  => 'Prenota un primo incontro →',
        'cta_final_cta_url'    => '/contatti/',
        'cta_final_trust'      => 'Risposta entro 24 ore · Riservatezza assoluta',
    ],
    'come-lavoriamo' => [
        'hero_eyebrow'         => '§ Lo studio · Come lavoriamo',
        'hero_h1_pre'          => 'Ascolto prima,',
        'hero_h1_em'           => 'carte dopo.',
        'hero_lede'            => "Crediamo che il diritto sia, prima di tutto, un'arte di ascolto. Le carte vengono dopo. Per questo non offriamo pacchetti né formule.",
        'aside_eyebrow'        => '§ Tre principi',
        'aside_h3'             => 'Ascoltiamo · Lavoriamo in atelier · Diciamo la verità',
        'aside_p'              => 'Primo incontro gratuito · Una pratica, un avvocato · Onesta valutazione percorribilità',
        'aside_cta_label'      => '',
        'aside_cta_url'        => '',
        'body_content'         => "<p>Ascoltiamo prima delle carte. Il primo incontro dura il tempo necessario, è gratuito e dedicato esclusivamente a capire la tua storia. Le scartoffie le firmiamo solo quando abbiamo capito cosa serve davvero.</p>\n<p>Lavoriamo in atelier: ogni pratica è seguita personalmente da uno dei quattro avvocati, dall'inizio alla fine. Niente call center, niente passaggi di mano, niente \"il collega le richiamerà\".</p>\n<p>Diciamo la verità anche quando significa sconsigliare un'azione legale. La nostra reputazione vale più di un mandato. Se la causa non è percorribile, te lo diciamo subito.</p>",
        'cta_final_eyebrow'    => '§ Pronto?',
        'cta_final_h2'         => 'Vorresti raccontarci la tua pratica?',
        'cta_final_p'          => 'Trenta minuti di prima consulenza gratuita. In studio o online. Risposta entro 24 ore.',
        'cta_final_cta_label'  => 'Prenota un incontro →',
        'cta_final_cta_url'    => '/contatti/',
        'cta_final_trust'      => 'Risposta entro 24 ore · Riservatezza assoluta',
    ],
    'prima-consulenza' => [
        'hero_eyebrow'         => '§ Servizio · Prima consulenza',
        'hero_h1_pre'          => 'Trenta minuti',
        'hero_h1_em'           => 'gratuiti.',
        'hero_lede'            => 'Trenta minuti di prima consulenza conoscitiva, gratuita. In studio a Chiaia o in videocall. Senza obblighi, senza costi nascosti.',
        'aside_eyebrow'        => '§ Modalità',
        'aside_h3'             => 'GRATUITA · 30 MIN · IN STUDIO O ONLINE',
        'aside_p'              => 'Nessun obbligo · Nessun costo nascosto · Riservatezza assoluta',
        'aside_cta_label'      => '',
        'aside_cta_url'        => '',
        'body_content'         => "<p>Trenta minuti gratuiti, in studio a Chiaia o in videocall. Sufficienti per ascoltare la pratica, valutare la percorribilità e decidere insieme se procedere. Senza obblighi, senza costi nascosti.</p>\n<p>Solo dopo il primo incontro formuliamo un preventivo personalizzato basato su complessità, tempi e probabilità di esito. Il preventivo è scritto, fisso o a percentuale del beneficio. Lo concordiamo prima del mandato.</p>",
        'cta_final_eyebrow'    => '§ Pronto?',
        'cta_final_h2'         => 'Iniziamo.',
        'cta_final_p'          => 'Risposta entro 24 ore. Riservatezza assoluta. Cancellazione 1 click.',
        'cta_final_cta_label'  => 'Prenota un incontro →',
        'cta_final_cta_url'    => '/contatti/',
        'cta_final_trust'      => 'Risposta entro 24 ore · Riservatezza assoluta',
    ],
    'lavora-con-noi' => [
        'hero_eyebrow'         => '§ Studio · Carriera',
        'hero_h1_pre'          => 'Cerchiamo',
        'hero_h1_em'           => 'praticanti.',
        'hero_lede'            => 'Cerchiamo praticanti motivati e curiosi, disponibili a un percorso strutturato in tutte le materie dello studio. Nessuna formula stage-mascherato.',
        'aside_eyebrow'        => '§ Cosa offriamo',
        'aside_h3'             => 'Mentorship · 18 mesi · Compenso adeguato',
        'aside_p'              => 'Mentorship 1-1 con i quattro avvocati · Rotazione su tutte le materie · Compenso conforme al CCNL',
        'aside_cta_label'      => '',
        'aside_cta_url'        => '',
        'body_content'         => "<p>Cerchiamo praticanti motivati e curiosi, con voglia di studiare in profondità. Offriamo un percorso strutturato di 18 mesi su tutte le materie dello studio: tributario, lavoro, famiglia LGBTQ+, immobiliare, condominiale, contenzioso.</p>\n<p>Niente formula stage-mascherato. Compenso conforme al CCNL forense, mentorship 1-1 con i quattro avvocati, casi reali fin dalla prima settimana. Cerchiamo persone che vogliano fare l'avvocato in maniera seria.</p>",
        'cta_final_eyebrow'    => '§ Pronto?',
        'cta_final_h2'         => 'Inviaci il tuo curriculum.',
        'cta_final_p'          => 'Solo CV reali, no autocandidature standardizzate. Risposta entro 7 giorni lavorativi.',
        'cta_final_cta_label'  => 'Scrivici →',
        'cta_final_cta_url'    => '/contatti/',
        'cta_final_trust'      => 'Risposta entro 24 ore · Riservatezza assoluta',
    ],
    'richiedi-preventivo' => [
        'hero_eyebrow'         => '§ Servizio · Richiedi un preventivo',
        'hero_h1_pre'          => 'Richiedi un',
        'hero_h1_em'           => 'preventivo.',
        'hero_lede'            => 'Compila un breve modulo per ricevere un preventivo personalizzato. Trasparente, vincolato alla complessità reale della pratica.',
        'aside_eyebrow'        => '§ Come funziona',
        'aside_h3'             => 'Preventivo scritto in 48h',
        'aside_p'              => 'Risposta entro 24h · Preventivo dettagliato scritto · Onorario, contributo unificato, spese vive separati',
        'aside_cta_label'      => '',
        'aside_cta_url'        => '',
        'body_content'         => '',
        'cta_final_eyebrow'    => '§ Pronto?',
        'cta_final_h2'         => 'Pronto a richiedere un preventivo?',
        'cta_final_p'          => 'Trenta minuti di prima consulenza gratuita prima del preventivo. Niente sorprese, niente costi nascosti.',
        'cta_final_cta_label'  => 'Apri modulo →',
        'cta_final_cta_url'    => '/contatti/',
        'cta_final_trust'      => 'Risposta entro 24 ore · Riservatezza assoluta',
    ],
];

foreach ($info_data as $slug => $fields) {
    $page = get_page_by_path($slug);
    if (!$page) { echo "WARN: $slug not found, skip\n"; continue; }
    $pid = $page->ID;
    echo "═══ /$slug/ (id=$pid) ═══\n";
    foreach ($fields as $name => $value) {
        fix_set($name, $value, $pid, $total_ok, $total_attempt);
    }
}

// ============================================================
// CLEANUP — wrong droplet pages che hanno dati ACF Wave 1 orfani
// (slug-based, env-safe: no-op su locale, applica su droplet)
// ============================================================
echo "\n═══ CLEANUP wrong-page ACF data orphan (slug-based) ═══\n";
$cleanup_slug_map = [
    // slug => expected post_type — pages NON in info-shared scope ma con
    // dati ACF Wave 1 orfani da migration con local IDs su droplet
    'diritto-societario'  => 'competenza',
    'contrattualistica'   => 'competenza',
    'glossario-legale'    => 'page',
];
$wave1_fields_to_clean = [
    'hero_eyebrow', 'hero_h1_pre', 'hero_h1_em', 'hero_lede',
    'toc_title', 'cta_eyebrow', 'cta_h2', 'cta_p', 'cta_label', 'cta_url',
    'aside_eyebrow', 'aside_h3', 'aside_p', 'aside_cta_label', 'aside_cta_url',
    'body_content',
    'cta_final_eyebrow', 'cta_final_h2', 'cta_final_p',
    'cta_final_cta_label', 'cta_final_cta_url', 'cta_final_trust',
];

foreach ($cleanup_slug_map as $slug => $expected_pt) {
    $p = get_page_by_path($slug, OBJECT, $expected_pt);
    if (!$p || $p->post_type !== $expected_pt) {
        echo "  SKIP $slug (not found or wrong post_type)\n";
        continue;
    }
    // Detect orphan ACF: cleanup only if at least 1 field is populated
    $has_orphan = false;
    foreach ($wave1_fields_to_clean as $f) {
        $v = get_field($f, $p->ID);
        if ($v !== null && $v !== '' && $v !== false) { $has_orphan = true; break; }
    }
    if (!$has_orphan) {
        echo "  SKIP $slug (id=$p->ID, no orphan ACF data — locale-safe)\n";
        continue;
    }
    echo "  Cleanup #$p->ID ($slug, post_type=$expected_pt) — orphan ACF detected:\n";
    foreach ($wave1_fields_to_clean as $name) {
        fix_clean($name, $p->ID, $total_clean);
    }
}

echo "\n";
echo "═══ Bug-04 fix SUMMARY ═══\n";
echo "Updates applied:    $total_ok / $total_attempt fields\n";
echo "Cleanup orphan ACF: $total_clean fields removed from wrong pages\n";
