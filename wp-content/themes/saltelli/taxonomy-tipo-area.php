<?php
/**
 * Template: Taxonomy archive — tipo-area.
 *
 * Sessione 2 · Wave 3 · Task 8.
 * Match LAYOUT SACRO `saltelli-s2-taxonomy-tipo-area.jsx`:
 *   1. Hero asimmetrico 8/4: sx h1 gigante + lede italic + counter aree;
 *      dx 1-2 mini-card avvocati specialisti (80x80 + nome + ruolo).
 *   2. "Quando rivolgersi" — 3 scenari tipici cluster (mono symbols).
 *   3. Lista aree (.sl-area pattern, tier-1 first ordering).
 *   4. Casi rappresentativi cluster (filter su `saltelli_all_cases()` per cat).
 *   5. CTA finale editoriale "Prenota gratuita".
 *
 * Schema JSON-LD: ItemList LegalService.
 *  Yoast emette già CollectionPage + Breadcrumb su questo template
 *  (verificato 2026-05-01 via /wp-json/wp/v2/tipo-area), quindi per
 *  rispettare la regola "no duplicati" emettiamo solo l'ItemList
 *  delle competenze figlie — additivo non duplicativo.
 *
 * @package Saltelli
 */
get_header();

$term       = get_queried_object();
$term_slug  = $term && !empty($term->slug) ? $term->slug : '';
$term_name  = $term && !empty($term->name) ? $term->name : '';
$term_desc  = $term && !empty($term->description) ? $term->description : '';

// Wave 4.7.fix.2 P4: UX strings comuni ai 4 cluster editable da SCF "Hub Pages".
$sl_taxonomy_eyebrow  = (string) saltelli_option('taxonomy_tipoarea_eyebrow', '');
$sl_taxonomy_subtpl   = (string) saltelli_option('taxonomy_tipoarea_subtitle_template', __('Le aree presidiate dallo studio nel cluster %s. Tier-1 sono le aree di profondità.', 'saltelli'));

// Riusa la query archive-competenza ordering: tier-1 first.
$competenze = $term ? get_posts([
    'post_type'   => 'competenza',
    'numberposts' => -1,
    'meta_key'    => 'is_tier_1_focus',
    'orderby'     => [
        'meta_value_num' => 'DESC',
        'menu_order'     => 'ASC',
        'title'          => 'ASC',
    ],
    'tax_query'   => [[
        'taxonomy' => 'tipo-area',
        'field'    => 'term_id',
        'terms'    => [(int) $term->term_id],
    ]],
]) : [];

$total = count($competenze);

/* ─── Avvocati di riferimento (top 2 dynamic) ────────────────────────
   Aggrega lead_attorneys delle competenze del cluster → frequenza →
   prendi i primi 2. Fallback editoriale per cluster se nessuna match. */
$attorney_freq = [];
foreach ($competenze as $cp) {
    foreach (saltelli_get_attorneys_for_competenza($cp->ID) as $av) {
        $aid = (int) $av->ID;
        if (!isset($attorney_freq[$aid])) {
            $attorney_freq[$aid] = ['post' => $av, 'count' => 0];
        }
        $attorney_freq[$aid]['count']++;
    }
}
uasort($attorney_freq, function ($a, $b) {
    return $b['count'] <=> $a['count'];
});
$avvocati_referenti = array_slice(array_values($attorney_freq), 0, 2);

if (empty($avvocati_referenti)) {
    $fallback_slugs = [
        'privati'     => ['antonia-battista', 'fabiana-saltelli'],
        'imprese'     => ['emiliano-saltelli', 'fabiana-saltelli'],
        'contenzioso' => ['emiliano-saltelli', 'stefano-gaetano-tedesco'],
        'altri'       => ['emiliano-saltelli'],
    ];
    $slugs = $fallback_slugs[$term_slug] ?? ['emiliano-saltelli'];
    foreach ($slugs as $sl) {
        $av = get_page_by_path($sl, OBJECT, 'avvocato');
        if ($av) {
            $avvocati_referenti[] = ['post' => $av, 'count' => 0];
        }
    }
}

/* ─── Scenari tipici per cluster (sezione "Quando rivolgersi") ───────
   Tre scenari editoriali per tipo-area. Sym Playfair italic glyph. */
$scenari_map = [
    'privati' => [
        ['sym' => '§', 't' => __('Famiglia', 'saltelli'),     'd' => __("Separazioni, divorzi, affidamenti, unioni civili e tutela LGBTQ+.", 'saltelli'),  'slug' => 'diritto-di-famiglia'],
        ['sym' => '¶', 't' => __('Eredità', 'saltelli'),      'd' => __("Successioni testate e legittime, divisioni, pubblicazione testamenti.", 'saltelli'),  'slug' => 'diritto-delle-successioni'],
        ['sym' => '†', 't' => __('Risarcimento', 'saltelli'), 'd' => __("Danni da circolazione, malasanità, mobbing e responsabilità civile.", 'saltelli'),  'slug' => 'risarcimento-danni'],
    ],
    'imprese' => [
        ['sym' => '§', 't' => __('Tributario', 'saltelli'),  'd' => __("Accertamenti, ricorsi tributari, contenzioso con Agenzia delle Entrate.", 'saltelli'),  'slug' => 'diritto-tributario'],
        ['sym' => '¶', 't' => __('Crediti', 'saltelli'),     'd' => __("Recupero crediti commerciali, decreti ingiuntivi, esecuzioni mobiliari.", 'saltelli'),  'slug' => 'recupero-crediti'],
        ['sym' => '†', 't' => __('Bancario', 'saltelli'),    'd' => __("Anatocismo, usura, contestazione di clausole vessatorie nei contratti bancari.", 'saltelli'),  'slug' => 'diritto-bancario'],
    ],
    'contenzioso' => [
        ['sym' => '§', 't' => __('Cartelle', 'saltelli'),       'd' => __("Opposizione a cartelle esattoriali, prescrizioni, vizi di notifica.", 'saltelli'),  'slug' => 'cartelle-esattoriali-e-multe'],
        ['sym' => '¶', 't' => __('Amministrativo', 'saltelli'), 'd' => __("Ricorsi al TAR, annullamento di provvedimenti P.A., edilizia, urbanistica.", 'saltelli'),  'slug' => 'diritto-amministrativo'],
        ['sym' => '†', 't' => __('Condominiale', 'saltelli'),   'd' => __("Impugnazione delibere, parti comuni, decoro architettonico, mediazioni.", 'saltelli'),  'slug' => 'diritto-condominiale'],
    ],
    'altri' => [
        ['sym' => '§', 't' => __('Domiciliazione', 'saltelli'), 'd' => __("Domiciliazione d'impresa per società extra-Campania con presidio a Napoli.", 'saltelli'),  'slug' => 'domiciliazione-dimpresa'],
        ['sym' => '¶', 't' => __('Online', 'saltelli'),         'd' => __("Consulenze in videocall su tutta Italia, primo orientamento gratuito.", 'saltelli'),  'slug' => 'consulenze-online'],
        ['sym' => '†', 't' => __('Previdenza', 'saltelli'),     'd' => __("Pensioni, invalidità civile, Legge 104, contributi INPS contestati.", 'saltelli'),  'slug' => 'diritto-previdenziale'],
    ],
];
$scenari = $scenari_map[$term_slug] ?? $scenari_map['privati'];

/* ─── P3 (wave5-step3-completion): SCF per-term — group_tipo_area_term_v1 ───
   Ogni elemento editoriale di questo template è ora editabile PER TERMINE da
   WP-Admin → Aree di pratica → <termine> → Modifica (campi "Term Tipo Area").
   I `default_value` nel JSON sono blank: SCF salva i valori in term meta
   (per-term), e i fallback hardcoded qui sotto garantiscono che il frontend
   resti invariato finché Elena non popola i campi. Dati dinamici (avvocati,
   competenze, casi, conteggi, breadcrumb) restano gestiti dal template. */
$sl_term_field = function ($name, $default = '') use ($term) {
    if (!$term instanceof WP_Term || !function_exists('get_field')) {
        return $default;
    }
    $v = get_field($name, $term);
    return ($v !== null && $v !== '' && $v !== false) ? $v : $default;
};

// Scenari "Quando rivolgersi": override per-term di titolo/descrizione
// (il simbolo §¶† e lo slug della competenza linkata restano dal map).
for ($sl_i = 0; $sl_i < 3; $sl_i++) {
    if (!isset($scenari[$sl_i])) {
        continue;
    }
    $sl_sc_t = (string) $sl_term_field('tipo_area_term_scenario' . ($sl_i + 1) . '_title', '');
    $sl_sc_d = (string) $sl_term_field('tipo_area_term_scenario' . ($sl_i + 1) . '_desc', '');
    if ($sl_sc_t !== '') {
        $scenari[$sl_i]['t'] = $sl_sc_t;
    }
    if ($sl_sc_d !== '') {
        $scenari[$sl_i]['d'] = $sl_sc_d;
    }
}

// Hero eyebrow: fallback al valore globale (Theme Options "Hub Pages", oggi "§ Cluster").
$sl_eyebrow = (string) $sl_term_field('tipo_area_term_eyebrow', $sl_taxonomy_eyebrow);

// Hero H1: default = nome del termine + ".". h1_emphasis è opzionale (corsivo, appeso a h1_main).
$sl_h1_main = (string) $sl_term_field('tipo_area_term_h1_main', '');
$sl_h1_em   = (string) $sl_term_field('tipo_area_term_h1_emphasis', '');
if ($sl_h1_main === '' && $sl_h1_em === '') {
    $sl_h1_src = $term_name . '.';
} else {
    $sl_h1_src = $sl_h1_main !== '' ? $sl_h1_main : ($term_name . '.');
    if ($sl_h1_em !== '') {
        $sl_h1_src .= ' <em>' . $sl_h1_em . '</em>';
    }
}

// Hero lede: SCF intro → descrizione del termine (campo nativo WP) → template globale (ramo else nel markup).
$sl_intro = (string) $sl_term_field('tipo_area_term_intro', '');
$sl_lede  = $sl_intro !== '' ? $sl_intro : (string) $term_desc;

// Etichette di sezione + microcopy della CTA finale (default = literal originale del template).
$sl_aside_eyebrow  = (string) $sl_term_field('tipo_area_term_aside_eyebrow', __('Avvocati di riferimento', 'saltelli'));
$sl_quando_label   = (string) $sl_term_field('tipo_area_term_quando_label', __('Quando rivolgersi', 'saltelli'));
$sl_quando_h2_main = (string) $sl_term_field('tipo_area_term_quando_h2_main', __('Tre scenari', 'saltelli'));
$sl_quando_h2_em   = (string) $sl_term_field('tipo_area_term_quando_h2_em', __('tipici.', 'saltelli'));
$sl_lista_label    = (string) $sl_term_field('tipo_area_term_lista_label', __('Aree di pratica', 'saltelli'));
$sl_lista_empty    = (string) $sl_term_field('tipo_area_term_lista_empty', __('Nessuna competenza in questa categoria.', 'saltelli'));
$sl_casi_label     = (string) $sl_term_field('tipo_area_term_casi_label', __('Casi rappresentativi', 'saltelli'));
$sl_cta_label      = (string) $sl_term_field('tipo_area_term_cta_label', __('Primo incontro', 'saltelli'));
$sl_cta_h2_main    = (string) $sl_term_field('tipo_area_term_cta_h2_main', __('Hai una pratica', 'saltelli'));
$sl_cta_h2_em      = (string) $sl_term_field('tipo_area_term_cta_h2_em', __('simile?', 'saltelli'));
$sl_cta_lede       = (string) $sl_term_field('tipo_area_term_cta_lede', __('Il primo incontro è gratuito. Riceviamo solo su appuntamento. Risposta entro 24 ore.', 'saltelli'));
$sl_cta_btn_label  = (string) $sl_term_field('tipo_area_term_cta_btn_label', __('Prenota gratuita', 'saltelli'));
$sl_cta_btn_url_raw = (string) $sl_term_field('tipo_area_term_cta_btn_url', '');
if ($sl_cta_btn_url_raw === '') {
    $sl_cta_btn_url = home_url('/contatti/');
} elseif (preg_match('#^(?:https?:)?//#i', $sl_cta_btn_url_raw)) {
    $sl_cta_btn_url = $sl_cta_btn_url_raw;
} else {
    $sl_cta_btn_url = home_url('/' . ltrim($sl_cta_btn_url_raw, '/'));
}

/* ─── Casi cluster — filter saltelli_all_cases() ────────────────────
   La cat in saltelli_all_cases() usa label capitalized: Privati / Imprese /
   Contenzioso / Altri. Match case-insensitive su term slug. */
$cat_map = [
    'privati'     => 'Privati',
    'imprese'     => 'Imprese',
    'contenzioso' => 'Contenzioso',
    'altri'       => 'Altri',
];
$cat_label = $cat_map[$term_slug] ?? '';
$casi_cluster = [];
if ($cat_label && function_exists('saltelli_all_cases')) {
    foreach (saltelli_all_cases() as $c) {
        if (isset($c['cat']) && $c['cat'] === $cat_label) {
            $casi_cluster[] = $c;
        }
    }
}

/* ─── Helper inline: avatar circle (foto + fallback gradient) ───────── */
$avatar_html = function ($av_post) {
    $thumb_id = (int) get_post_thumbnail_id($av_post);
    if ($thumb_id) {
        $img = wp_get_attachment_image($thumb_id, [120, 120], false, [
            'class' => 'sl-tipoarea__attorney-photo',
            'alt'   => esc_attr(get_the_title($av_post)),
            'loading' => 'lazy',
        ]);
        if ($img) return $img;
    }
    return '<span class="sl-tipoarea__attorney-photo sl-tipoarea__attorney-photo--fallback" aria-hidden="true"></span>';
};

/* ─── H1 italic em chunk: "Per i Privati." → split su last word ─── */
?>

<div class="sl-tipoarea sl-tipoarea--<?php echo esc_attr($term_slug); ?>">

    <!-- HERO 8/4 -->
    <section class="sl-tipoarea__hero sl-page-hero">
        <div class="sl-container">
            <div class="sl-tipoarea__hero-grid">

                <div class="sl-tipoarea__hero-main">
                    <?php saltelli_render_breadcrumb(); ?>

                    <?php if ($sl_eyebrow) : ?>
                        <p class="sl-mono sl-tipoarea__eyebrow"><?php echo esc_html($sl_eyebrow); ?></p>
                    <?php endif; ?>

                    <h1 class="sl-tipoarea__h1" data-split-reveal>
                        <?php echo wp_kses(saltelli_split_h1_words($sl_h1_src), ['span' => ['class' => true, 'data-i' => true], 'em' => []]); ?>
                    </h1>

                    <?php if ($sl_lede !== '') : ?>
                        <p class="sl-tipoarea__lede"><?php echo esc_html($sl_lede); ?></p>
                    <?php else : ?>
                        <p class="sl-tipoarea__lede">
                            <?php
                            // sprintf will silently work even if template has no %s.
                            echo esc_html(sprintf($sl_taxonomy_subtpl, $term_name));
                            ?>
                        </p>
                    <?php endif; ?>

                    <div class="sl-mono sl-tipoarea__count">
                        <?php
                        printf(
                            /* translators: %d numero competenze */
                            esc_html(_n('%d area di pratica', '%d aree di pratica', $total, 'saltelli')),
                            (int) $total
                        );
                        ?>
                    </div>
                </div>

                <?php if (!empty($avvocati_referenti)) : ?>
                <aside class="sl-tipoarea__hero-aside" aria-label="<?php echo esc_attr($sl_aside_eyebrow); ?>">
                    <div class="sl-mono sl-tipoarea__aside-eyebrow">
                        <?php echo esc_html($sl_aside_eyebrow); ?>
                    </div>
                    <div class="sl-tipoarea__attorneys">
                        <?php foreach ($avvocati_referenti as $entry) :
                            $av    = $entry['post'];
                            $ruolo = (string) saltelli_field('ruolo_breve', $av->ID, '');
                            ?>
                            <a class="sl-tipoarea__attorney" href="<?php echo esc_url(get_permalink($av)); ?>">
                                <?php echo $avatar_html($av); // already escaped ?>
                                <span class="sl-tipoarea__attorney-text">
                                    <span class="sl-tipoarea__attorney-name"><?php echo esc_html(get_the_title($av)); ?></span>
                                    <?php if ($ruolo) : ?>
                                        <span class="sl-mono sl-tipoarea__attorney-role">
                                            <?php echo esc_html($ruolo); ?> <span class="arrow" aria-hidden="true">→</span>
                                        </span>
                                    <?php endif; ?>
                                </span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </aside>
                <?php endif; ?>

            </div>
        </div>
    </section>

    <!-- QUANDO RIVOLGERSI -->
    <section class="sl-tipoarea__quando">
        <div class="sl-container">
            <header class="sl-tipoarea__section-head">
                <div class="sl-mono sl-tipoarea__section-eyebrow">§ 01 — <?php echo esc_html($sl_quando_label); ?></div>
                <h2 class="sl-tipoarea__section-title">
                    <?php echo esc_html($sl_quando_h2_main); ?><br>
                    <em><?php echo esc_html($sl_quando_h2_em); ?></em>
                </h2>
            </header>
            <div class="sl-tipoarea__quando-grid">
                <?php foreach ($scenari as $s) :
                    /* === FIX v0.19.1 [F1] BEGIN — wrap scenario in <a> + add "Leggi →" link (JSX parity) === */
                    $sl_sc_slug = isset($s['slug']) ? (string) $s['slug'] : '';
                    $sl_sc_href = $sl_sc_slug !== '' ? home_url('/competenze/' . $sl_sc_slug . '/') : '';
                    ?>
                    <?php if ($sl_sc_href !== '') : ?>
                        <a class="sl-tipoarea__scenario" href="<?php echo esc_url($sl_sc_href); ?>">
                            <span class="sl-tipoarea__scenario-sym" aria-hidden="true"><?php echo esc_html($s['sym']); ?></span>
                            <h3 class="sl-tipoarea__scenario-title"><?php echo esc_html($s['t']); ?></h3>
                            <p class="sl-tipoarea__scenario-desc"><?php echo esc_html($s['d']); ?></p>
                            <span class="sl-mono sl-tipoarea__scenario-leggi"><?php esc_html_e('Leggi', 'saltelli'); ?> <span class="arrow" aria-hidden="true">→</span></span>
                        </a>
                    <?php else : ?>
                        <article class="sl-tipoarea__scenario">
                            <span class="sl-tipoarea__scenario-sym" aria-hidden="true"><?php echo esc_html($s['sym']); ?></span>
                            <h3 class="sl-tipoarea__scenario-title"><?php echo esc_html($s['t']); ?></h3>
                            <p class="sl-tipoarea__scenario-desc"><?php echo esc_html($s['d']); ?></p>
                        </article>
                    <?php endif; ?>
                    <?php /* === FIX v0.19.1 [F1] END === */ ?>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- LISTA AREE -->
    <section class="sl-tipoarea__lista">
        <div class="sl-container">
            <header class="sl-tipoarea__section-head">
                <div class="sl-mono sl-tipoarea__section-eyebrow">§ 02 — <?php echo esc_html($sl_lista_label); ?></div>
                <h2 class="sl-tipoarea__section-title">
                    <?php
                    /* v0.30.0 — usa parola italiana invece di digit (JSX-faithful "Nove aree.") */
                    $sl_num_words_ar = ['', 'Una', 'Due', 'Tre', 'Quattro', 'Cinque', 'Sei', 'Sette', 'Otto', 'Nove', 'Dieci'];
                    $sl_total_label = isset($sl_num_words_ar[$total]) ? $sl_num_words_ar[$total] : (string) $total;
                    printf(
                        /* translators: %s numero competenze in parola */
                        esc_html(_n('%s area.', '%s aree.', $total, 'saltelli')),
                        esc_html($sl_total_label)
                    );
                    ?>
                </h2>
            </header>

            <?php if (!empty($competenze)) : ?>
                <div class="sl-areas__list sl-tipoarea__areas-list">
                    <?php
                    $i = 0;
                    foreach ($competenze as $p) :
                        $i++;
                        $num       = str_pad((string) $i, 2, '0', STR_PAD_LEFT);
                        $cat       = saltelli_competenza_category_label($p->ID);
                        $is_tier_1 = (bool) saltelli_field('is_tier_1_focus', $p->ID, false);
                        ?>
                        <a class="sl-area<?php echo $is_tier_1 ? ' sl-area--tier1' : ''; ?>"
                           href="<?php echo esc_url(get_permalink($p)); ?>"
                           data-area-num="<?php echo esc_attr($num); ?>">
                            <span class="sl-area__num sl-mono"><?php echo esc_html($num); ?> / <?php echo esc_html(str_pad((string) $total, 2, '0', STR_PAD_LEFT)); ?></span>
                            <span class="sl-area__title"><?php echo esc_html(get_the_title($p)); ?></span>
                            <span class="sl-area__meta sl-mono">
                                <?php echo esc_html($is_tier_1 ? __('Tier 1 · approfondimento', 'saltelli') : ($cat ?: __('Approfondisci', 'saltelli'))); ?>
                                <span class="arrow" aria-hidden="true">→</span>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <p class="sl-mono sl-tipoarea__empty"><?php echo esc_html($sl_lista_empty); ?></p>
            <?php endif; ?>
        </div>
    </section>

    <!-- CASI RAPPRESENTATIVI CLUSTER -->
    <?php if (!empty($casi_cluster)) : ?>
    <section class="sl-tipoarea__casi">
        <div class="sl-container">
            <header class="sl-tipoarea__section-head">
                <div class="sl-mono sl-tipoarea__section-eyebrow">§ 03 — <?php echo esc_html($sl_casi_label); ?></div>
                <h2 class="sl-tipoarea__section-title">
                    <?php
                    /* v0.30.0 — fix typo "per per i privati":
                       term_name = "Per i Privati" → preserva senza prefisso duplicato.
                       Strip leading "Per " (case-insensitive) per evitare doppia "per".
                       Usa parola italiana invece di digit (JSX "Tre vittorie..."). */
                    $count = count($casi_cluster);
                    $sl_num_words_casi = ['', 'Una', 'Due', 'Tre', 'Quattro', 'Cinque', 'Sei', 'Sette', 'Otto', 'Nove', 'Dieci'];
                    $sl_count_label = isset($sl_num_words_casi[$count]) ? $sl_num_words_casi[$count] : (string) $count;
                    $sl_term_label = strtolower($term_name);
                    /* Strip "per " iniziale (es. "per i privati", "per le imprese") */
                    if (stripos($sl_term_label, 'per ') === 0) {
                        $sl_term_label = substr($sl_term_label, 4);
                    }
                    printf(
                        /* translators: %1$s numero casi (parola), %2$s nome cluster lowercase */
                        esc_html(_n('%1$s vittoria per %2$s.', '%1$s vittorie per %2$s.', $count, 'saltelli')),
                        esc_html($sl_count_label),
                        esc_html($sl_term_label)
                    );
                    ?>
                </h2>
            </header>
            <ul class="sl-tipoarea__casi-list" role="list">
                <?php foreach ($casi_cluster as $c) : ?>
                    <li class="sl-tipoarea__caso">
                        <span class="sl-mono sl-tipoarea__caso-id"><?php echo esc_html($c['id']); ?></span>
                        <p class="sl-tipoarea__caso-desc"><?php echo esc_html($c['desc']); ?></p>
                        <span class="sl-tipoarea__caso-outcome"><?php echo esc_html($c['outcome']); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>
    <?php endif; ?>

    <!-- CTA FINALE -->
    <section class="sl-tipoarea__cta">
        <div class="sl-container">
            <div class="sl-tipoarea__cta-grid">
                <div class="sl-mono sl-tipoarea__section-eyebrow">§ 04 — <?php echo esc_html($sl_cta_label); ?></div>
                <div class="sl-tipoarea__cta-body">
                    <h2 class="sl-tipoarea__cta-title">
                        <?php echo esc_html($sl_cta_h2_main); ?><br>
                        <em><?php echo esc_html($sl_cta_h2_em); ?></em>
                    </h2>
                    <p class="sl-tipoarea__cta-lede">
                        <?php echo esc_html($sl_cta_lede); ?>
                    </p>
                    <a class="sl-btn sl-btn--primary sl-tipoarea__cta-btn" href="<?php echo esc_url($sl_cta_btn_url); ?>">
                        <span><?php echo esc_html($sl_cta_btn_label); ?></span>
                        <span class="arrow" aria-hidden="true">→</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

</div><!-- /.sl-tipoarea -->

<?php
/* ─── Schema JSON-LD: ItemList LegalService ───────────────────────────
   Yoast emette già CollectionPage + Breadcrumb su term archive (Yoast SEO
   v27.4 verificato 2026-05-01). Non duplichiamo. Emettiamo solo l'ItemList
   delle competenze figlie come complemento additivo: questo è ciò che
   Yoast NON copre e che invece serve a Google/AI per capire la lista
   strutturata dei servizi sotto al cluster. */
if (!empty($competenze) && function_exists('saltelli_emit_jsonld')) {
    $items = [];
    $pos   = 0;
    foreach ($competenze as $p) {
        $pos++;
        $items[] = [
            '@type'    => 'ListItem',
            'position' => $pos,
            'item'     => [
                '@type'      => 'LegalService',
                '@id'        => get_permalink($p) . '#legalservice',
                'name'       => get_the_title($p),
                'url'        => get_permalink($p),
                'provider'   => [
                    '@type' => 'Organization',
                    '@id'   => home_url('/#organization'),
                    'name'  => 'Studio Legale Emiliano Saltelli & Partners',
                ],
                'areaServed' => [
                    '@type' => 'Place',
                    'name'  => 'Italia',
                ],
            ],
        ];
    }

    saltelli_emit_jsonld([
        '@context'        => 'https://schema.org',
        '@type'           => 'ItemList',
        '@id'             => get_term_link($term) . '#itemlist',
        'name'            => sprintf(__('Aree di pratica — %s', 'saltelli'), $term_name),
        'numberOfItems'   => count($items),
        'itemListOrder'   => 'https://schema.org/ItemListOrderAscending',
        'itemListElement' => $items,
    ]);
}

get_footer();
