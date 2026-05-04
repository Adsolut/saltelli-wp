<?php
/**
 * Wave 2 Phase 4 — Lawyer + Tier-1 fields migration.
 *
 * Source: existing post_meta + helpers.php data + saltelli_attorney_cases / formazione.
 * Target: ACF Field Groups Wave 1 (group_avvocato_v1, group_competenza_v1).
 *
 * Hard rules applied:
 *  - NO sovrascrivere _thumbnail_id Emiliano (2683)
 *  - NO sovrascrivere bio_estesa esistente (Step D content protected)
 *  - NO sovrascrivere foto_ritratto se presente
 *
 * Run:
 *   docker compose run --rm -v $PWD/scripts:/scripts wpcli eval-file /scripts/wave2-phase4-lawyers-tier1.php
 */

defined('ABSPATH') || exit;

$total_ok = 0;
$total_attempt = 0;
$total_skip = 0;

function w2_set($name, $value, $post_id, &$ok, &$attempt) {
    $attempt++;
    $r = update_field($name, $value, $post_id);
    if ($r) $ok++;
    return $r;
}

function w2_set_if_empty($name, $value, $post_id, $reason, &$ok, &$attempt, &$skip) {
    $attempt++;
    $existing = get_field($name, $post_id);
    if (!empty($existing) && (is_string($existing) ? trim($existing) !== '' : true)) {
        $skip++;
        echo "    [SKIP-PROTECTED] $name (existing $reason preserved)\n";
        return false;
    }
    $r = update_field($name, $value, $post_id);
    if ($r) $ok++;
    return $r;
}

// =====================================================================
// 4 LAWYER PROFILES
// =====================================================================

$lawyers = [
    'emiliano-saltelli' => [
        'id'            => 2660,
        'hero_role'     => 'Founding Partner · Tributarista',
        'specializzazioni' => "Diritto tributario\nCartelle esattoriali\nAccertamenti fiscali\nContenzioso fiscale\nCassazione tributaria",
        'bio_breve'     => 'Fondatore dello Studio. Si occupa di diritto tributario e contenzioso fiscale, dal contraddittorio preventivo al ricorso in Cassazione.',
        // bio_estesa SKIP (potrebbe essere Step D protected)
        'email_pubblica' => 'info@studiolegalesaltelli.it',
        'telefono_pubblico' => '+39 081 1813 1119',
        'whatsapp'      => '+393517138006',
        'same_as_linkedin' => 'https://www.linkedin.com/in/emilianosaltelli/',
    ],
    'fabiana-saltelli' => [
        'id'            => 2661,
        'hero_role'     => 'Partner · Giuslavorista',
        'specializzazioni' => "Diritto del lavoro\nLicenziamenti\nMobbing e demansionamento\nContenzioso INPS\nContrattualistica lavoro",
        'bio_breve'     => 'Partner dello Studio, giuslavorista. Segue licenziamenti, mobbing, contenziosi INPS dalle imprese ai lavoratori.',
        'email_pubblica' => 'info@studiolegalesaltelli.it',
        'telefono_pubblico' => '+39 081 1813 1119',
        'whatsapp'      => '+393517138006',
        'same_as_linkedin' => '',
    ],
    'antonia-battista' => [
        'id'            => 2662,
        'hero_role'     => 'Of Counsel · Famiglia LGBTQ+',
        'specializzazioni' => "Diritto di famiglia\nUnioni civili\nPMA e trascrizione nascita\nStepchild adoption\nIdentità di genere",
        'bio_breve'     => 'Of-counsel dello Studio dedicata al diritto di famiglia e alla tutela LGBTQ+. Componente Commissione Famiglia COA Napoli.',
        'email_pubblica' => 'info@studiolegalesaltelli.it',
        'telefono_pubblico' => '+39 081 1813 1119',
        'whatsapp'      => '+393517138006',
        'same_as_linkedin' => '',
    ],
    'stefano-gaetano-tedesco' => [
        'id'            => 2663,
        'hero_role'     => 'Associate · Condominiale',
        'specializzazioni' => "Diritto condominiale\nLocazioni e immobiliare\nRecupero crediti\nResponsabilità civile\nDecreti ingiuntivi",
        'bio_breve'     => "Associate dello Studio. Coordina l'area del diritto condominiale e immobiliare con esperienza in recupero crediti e responsabilità civile.",
        'email_pubblica' => 'info@studiolegalesaltelli.it',
        'telefono_pubblico' => '+39 081 1813 1119',
        'whatsapp'      => '+393517138006',
        'same_as_linkedin' => '',
    ],
];

// Map lawyer slug → competenza slugs (aree_competenza_correlate)
$lawyer_aree_map = [
    'emiliano-saltelli'         => ['diritto-tributario', 'cartelle-esattoriali-e-multe', 'recupero-crediti', 'diritto-bancario', 'diritto-amministrativo'],
    'fabiana-saltelli'          => ['diritto-del-lavoro', 'diritto-previdenziale'],
    'antonia-battista'          => ['diritto-di-famiglia-lgbtq', 'diritto-di-famiglia', 'diritto-delle-successioni'],
    'stefano-gaetano-tedesco'   => ['diritto-condominiale', 'recupero-crediti', 'risarcimento-danni', 'responsabilita-civile', 'diritto-delle-assicurazioni'],
];

echo "═══ 4 Lawyer profiles ═══\n";
foreach ($lawyers as $slug => $L) {
    $pid = $L['id'];
    echo "\n[$pid] $slug ($L[hero_role]):\n";

    // Scalar fields
    foreach (['hero_role', 'specializzazioni', 'bio_breve', 'email_pubblica', 'telefono_pubblico', 'whatsapp', 'same_as_linkedin'] as $key) {
        if (isset($L[$key]) && $L[$key] !== '') {
            $r = w2_set($key, $L[$key], $pid, $total_ok, $total_attempt);
            $existing = get_field($key, $pid);
            $existing_short = is_string($existing) ? substr($existing, 0, 50) : '(non-string)';
            printf("  [%s] %-22s = %s\n", $r ? 'OK' : 'NC', $key, $existing_short);
        }
    }

    // bio_estesa — skip if already populated (Step D protection)
    if (!empty($L['bio_estesa'])) {
        w2_set_if_empty('bio_estesa', $L['bio_estesa'], $pid, 'Step D bio_estesa', $total_ok, $total_attempt, $total_skip);
    }

    // aree_competenza_correlate — relationship
    $aree_ids = [];
    foreach ($lawyer_aree_map[$slug] ?? [] as $area_slug) {
        $a = get_page_by_path($area_slug, OBJECT, 'competenza');
        if ($a) {
            $aree_ids[] = (int) $a->ID;
        }
    }
    if (!empty($aree_ids)) {
        $r = w2_set('aree_competenza_correlate', $aree_ids, $pid, $total_ok, $total_attempt);
        printf("  [%s] %-22s = %d aree (%s)\n", $r ? 'OK' : 'NC', 'aree_competenza_correlate', count($aree_ids), implode(',', $aree_ids));
    }

    // formazione — relationship to saltelli_formazione (filtered by lawyer_slug meta)
    $form_ids = get_posts([
        'post_type'   => 'saltelli_formazione',
        'numberposts' => -1,
        'post_status' => 'publish',
        'meta_key'    => 'lawyer_slug',
        'meta_value'  => $slug,
        'orderby'     => 'menu_order',
        'order'       => 'ASC',
        'fields'      => 'ids',
    ]);
    if (!empty($form_ids)) {
        $form_ids = array_map('intval', $form_ids);
        $r = w2_set('formazione', $form_ids, $pid, $total_ok, $total_attempt);
        printf("  [%s] %-22s = %d items (%s)\n", $r ? 'OK' : 'NC', 'formazione', count($form_ids), implode(',', $form_ids));
    }

    // casi_rappresentativi — relationship via saltelli_attorney_cases match by id_label
    $atty_cases_data = function_exists('saltelli_attorney_cases') ? saltelli_attorney_cases($slug) : [];
    $caso_ids = [];
    foreach ($atty_cases_data as $case_data) {
        // Match on id_label first; fallback to title
        $matched = get_posts([
            'post_type'   => 'saltelli_caso',
            'numberposts' => 1,
            'post_status' => 'publish',
            'meta_query'  => [
                [
                    'key'   => 'id_label',
                    'value' => $case_data['id'],
                ],
            ],
            'fields' => 'ids',
        ]);
        if (empty($matched)) {
            // Fallback: title match
            $matched = get_posts([
                'post_type'   => 'saltelli_caso',
                'numberposts' => 1,
                'title'       => $case_data['id'],
                'post_status' => 'publish',
                'fields'      => 'ids',
            ]);
        }
        if (!empty($matched)) {
            $caso_ids[] = (int) $matched[0];
        }
    }
    if (!empty($caso_ids)) {
        $r = w2_set('casi_rappresentativi', $caso_ids, $pid, $total_ok, $total_attempt);
        printf("  [%s] %-22s = %d items (%s)\n", $r ? 'OK' : 'NC', 'casi_rappresentativi', count($caso_ids), implode(',', $caso_ids));
    }

    // _thumbnail_id PROTECTED — non lo tocchiamo
    $existing_thumb = get_post_thumbnail_id($pid);
    if ($existing_thumb) {
        echo "  [PROTECTED] _thumbnail_id = $existing_thumb (NOT modified)\n";
    } else {
        echo "  [INFO] _thumbnail_id empty (Ludovica fornirà foto)\n";
    }
}

// =====================================================================
// 3 TIER-1 COMPETENZA PROFILES
// =====================================================================
echo "\n═══ 3 Tier-1 competenze ═══\n";

$tier1 = [
    'diritto-tributario' => [
        'id'           => 2664,
        'tier_label'   => 'Tier 1 · Approfondimento',
        'subtitle'     => 'Cartelle, accertamenti, contenzioso fiscale.',
        'lead_attorney_slug' => 'emiliano-saltelli',
        'faq_topic'    => 'tributario',
        'caso_cat'     => ['imprese'],
        'cta_label'    => 'Parlane con i nostri avvocati',
        'cta_url'      => '/contatti/',
    ],
    'diritto-del-lavoro' => [
        'id'           => 2665,
        'tier_label'   => 'Tier 1 · Approfondimento',
        'subtitle'     => 'Licenziamenti, mobbing, INPS.',
        'lead_attorney_slug' => 'fabiana-saltelli',
        'faq_topic'    => 'lavoro',
        'caso_cat'     => ['privati', 'contenzioso'],
        'cta_label'    => 'Parlane con i nostri avvocati',
        'cta_url'      => '/contatti/',
    ],
    'diritto-di-famiglia-lgbtq' => [
        'id'           => 2666,
        'tier_label'   => 'Tier 1 · Approfondimento',
        'subtitle'     => 'Unioni civili, PMA, stepchild.',
        'lead_attorney_slug' => 'antonia-battista',
        'faq_topic'    => 'lgbtq',
        'caso_cat'     => ['privati'],
        'cta_label'    => 'Parlane con i nostri avvocati',
        'cta_url'      => '/contatti/',
    ],
];

foreach ($tier1 as $slug => $T) {
    $pid = $T['id'];
    echo "\n[$pid] $slug ($T[subtitle]):\n";

    // is_tier_1 boolean
    $r = w2_set('is_tier_1', true, $pid, $total_ok, $total_attempt);
    printf("  [%s] %-22s = true\n", $r ? 'OK' : 'NC', 'is_tier_1');

    // tier_label, subtitle
    foreach (['tier_label', 'subtitle', 'cta_label', 'cta_url'] as $key) {
        if (!empty($T[$key])) {
            $r = w2_set($key, $T[$key], $pid, $total_ok, $total_attempt);
            printf("  [%s] %-22s = %s\n", $r ? 'OK' : 'NC', $key, substr($T[$key], 0, 50));
        }
    }

    // answer_capsule — already exists, leave (skip if present)
    $existing_answer = get_field('answer_capsule', $pid);
    if (!empty($existing_answer)) {
        echo "  [SKIP-PROTECTED] answer_capsule (existing " . strlen($existing_answer) . " chars preserved)\n";
        $total_skip++;
    }

    // body_extended — populate from saltelli_tier1_clusters() collated as HTML
    $clusters = function_exists('saltelli_tier1_clusters') ? saltelli_tier1_clusters($slug) : [];
    if (!empty($clusters)) {
        $existing_body = get_field('body_extended', $pid);
        if (empty($existing_body)) {
            $body_html = '';
            foreach ($clusters as $cluster) {
                $body_html .= "<h2>" . esc_html($cluster['h2']) . "</h2>\n";
                foreach ($cluster['paragraphs'] as $par) {
                    $body_html .= "<p>" . esc_html($par) . "</p>\n";
                }
            }
            $r = w2_set('body_extended', $body_html, $pid, $total_ok, $total_attempt);
            printf("  [%s] %-22s = %d chars (%d cluster H2)\n", $r ? 'OK' : 'NC', 'body_extended', strlen($body_html), count($clusters));
        } else {
            echo "  [SKIP-PROTECTED] body_extended (existing " . strlen($existing_body) . " chars preserved)\n";
            $total_skip++;
        }
    }

    // lead_attorneys — relationship
    $lead_atty = get_page_by_path($T['lead_attorney_slug'], OBJECT, 'avvocato');
    if ($lead_atty) {
        $r = w2_set('lead_attorneys', [(int) $lead_atty->ID], $pid, $total_ok, $total_attempt);
        printf("  [%s] %-22s = %d (%s)\n", $r ? 'OK' : 'NC', 'lead_attorneys', $lead_atty->ID, $T['lead_attorney_slug']);
    }

    // casi_rappresentativi — relationship by caso_categoria
    if (!empty($T['caso_cat'])) {
        $caso_ids = get_posts([
            'post_type'   => 'saltelli_caso',
            'numberposts' => -1,
            'post_status' => 'publish',
            'tax_query'   => [
                [
                    'taxonomy' => 'caso_categoria',
                    'field'    => 'slug',
                    'terms'    => $T['caso_cat'],
                ],
            ],
            'fields'      => 'ids',
        ]);
        if (!empty($caso_ids)) {
            $caso_ids = array_map('intval', $caso_ids);
            $r = w2_set('casi_rappresentativi', $caso_ids, $pid, $total_ok, $total_attempt);
            printf("  [%s] %-22s = %d items (cat: %s)\n", $r ? 'OK' : 'NC', 'casi_rappresentativi', count($caso_ids), implode(',', $T['caso_cat']));
        }
    }

    // faq — relationship by faq_topic
    if (!empty($T['faq_topic'])) {
        $faq_ids = get_posts([
            'post_type'   => 'saltelli_faq',
            'numberposts' => -1,
            'post_status' => 'publish',
            'tax_query'   => [
                [
                    'taxonomy' => 'faq_topic',
                    'field'    => 'slug',
                    'terms'    => [$T['faq_topic']],
                ],
            ],
            'fields'      => 'ids',
            'orderby'     => 'menu_order',
            'order'       => 'ASC',
        ]);
        if (!empty($faq_ids)) {
            $faq_ids = array_map('intval', $faq_ids);
            $r = w2_set('faq', $faq_ids, $pid, $total_ok, $total_attempt);
            printf("  [%s] %-22s = %d items (topic: %s)\n", $r ? 'OK' : 'NC', 'faq', count($faq_ids), $T['faq_topic']);
        }
    }
}

echo "\n";
echo "═══ Phase 4 SUMMARY ═══\n";
echo "Updates OK: $total_ok / $total_attempt · Skipped (protected): $total_skip\n";
