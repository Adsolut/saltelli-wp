<?php
/**
 * Template: Front page (homepage).
 * Markup tradotto da .claude/knowledge/design/sessione-1/homepage-desktop.jsx
 *
 * @package Saltelli
 */
get_header();

$studio = saltelli_studio_data();

// Wave 4.7.fix.3: hero/studio/team/cases ora attaccati a Page WP "Home" (page_on_front).
// Edita da WP-Admin → Pagine → Home. Helper saltelli_page_field auto-resolve homepage_id.
$hero_eyebrow    = saltelli_page_field('hero_eyebrow', 'Studio Legale · Napoli · Chiaia · Dal 1999');
$hero_headline   = saltelli_page_field('hero_headline', 'Diritto, con misura.');
$hero_sub        = saltelli_page_field('hero_subheadline', "Studio Legale Saltelli &amp; Partners. Quattro avvocati a Chiaia, diciassette aree di pratica, vent'anni di lavoro accanto a famiglie e imprese di Napoli.");
$hero_cta_label  = saltelli_page_field('hero_cta_label', 'Prenota una consulenza gratuita');
$hero_cta_url    = saltelli_page_field('hero_cta_url', '/contatti/');

/* Wave 5 design-handoff P3 — hero variant B "cream scrim asimmetrico" (bg image).
   SCF additive: hero_image (return_format=id), hero_image_credit, hero_image_alt.
   hero_image vuoto (stato attuale) → placeholder Picsum (seed 'saltelli-marble');
   foto reale via Media Library = backlog Wave 5.1 (Elena swap). AVIF/WebP defer
   Wave 5.1 (Picsum serve solo JPG; serve plugin Image Optimization). */
$hero_image_id  = (int) saltelli_page_field('hero_image');
$hero_credit    = (string) saltelli_page_field('hero_image_credit', '');
$hero_image_alt = (string) saltelli_page_field('hero_image_alt', '');
$hero_alt       = $hero_image_alt !== '' ? $hero_image_alt : 'Studio Legale Saltelli, hero banner';

$hero_media_html = '';
if ($hero_image_id) {
    // Foto reale dalla Media Library — srcset/sizes auto via WP.
    $hero_media_html = wp_get_attachment_image($hero_image_id, 'full', false, [
        'loading'       => 'eager',
        'fetchpriority' => 'high',
        'decoding'      => 'async',
        'sizes'         => '100vw',
        'alt'           => esc_attr($hero_alt),
    ]);
}
if ($hero_media_html === '') {
    // Placeholder Picsum (hero_image vuoto o attachment rotto) — <picture> 3 source
    // desktop/tablet/mobile + srcset 1x/2x. loading=eager + fetchpriority=high (LCP).
    $picsum_seed = 'saltelli-marble';
    $picsum = static function ($w, $h) use ($picsum_seed) {
        return 'https://picsum.photos/seed/' . $picsum_seed . '/' . (int) $w . '/' . (int) $h;
    };
    $hero_media_html = sprintf(
        '<picture>' .
            '<source media="(min-width: 1024px)" srcset="%1$s 1x, %2$s 2x">' .
            '<source media="(min-width: 640px)" srcset="%3$s 1x, %4$s 2x">' .
            '<img src="%5$s" srcset="%5$s 1x, %6$s 2x" alt="%7$s" width="768" height="600" loading="eager" fetchpriority="high" decoding="async">' .
        '</picture>',
        esc_url($picsum(1920, 1080)),
        esc_url($picsum(3840, 2160)),
        esc_url($picsum(1280, 800)),
        esc_url($picsum(2560, 1600)),
        esc_url($picsum(768, 600)),
        esc_url($picsum(1536, 1200)),
        esc_attr($hero_alt)
    );
}

// Colophon resta in Theme Options (globale: anche footer.php usa colophon_*).
$col_indirizzo = saltelli_option('colophon_indirizzo', "Via Vannella Gaetani, 27\n80121 Napoli — Chiaia");
$col_orari     = saltelli_option('colophon_orari', "Lun – Ven · 10:00 – 19:00\nSolo su appuntamento");
$col_email     = saltelli_option('colophon_email', $studio['email']);
$col_tel       = saltelli_option('colophon_telefono', '+39 081 1813 1119');
$col_tel_e164  = saltelli_studio_phone_e164();

$studio_titolo = saltelli_page_field('studio_titolo_sezione', 'Un atelier, in senso napoletano.');
$studio_body   = saltelli_page_field('studio_body', '');
$studio_foto   = saltelli_page_field('studio_foto_facciata');

$team_titolo   = saltelli_page_field('team_titolo', "Quattro\nprofessionisti.");
$cases_titolo  = saltelli_page_field('cases_titolo', 'Casi rappresentativi.');

/* Wave 4.6: CTA default — usata in sl-contact final CTA section.
   Default ACF identici a quelli legacy hero_* per backward compat. */
$cta_default_url     = saltelli_option('cta_default_url', $hero_cta_url);
$cta_default_label   = saltelli_option('cta_default_label', $hero_cta_label);
$cta_subline_italic  = (string) saltelli_option('cta_subline_italic', '');

// Aree CPT competenza — Elena fix 2026-05-13: query "pulita" senza meta_key per
// evitare INNER JOIN su postmeta (WP gotcha: meta_key + orderby=meta_value_num
// esclude i post che non hanno quel meta salvato → competenze create da admin con
// checkbox is_tier_1 mai toccato sparivano dalla home). Sort tier-1-first PHP-side.
$competenze = get_posts([
    'post_type'   => 'competenza',
    'numberposts' => -1,
    'orderby'     => ['menu_order' => 'ASC', 'title' => 'ASC'],
]);
if (!empty($competenze)) {
    usort($competenze, function ($a, $b) {
        $at = (int) get_post_meta($a->ID, 'is_tier_1', true) ? 1 : 0;
        $bt = (int) get_post_meta($b->ID, 'is_tier_1', true) ? 1 : 0;
        if ($at !== $bt) return $bt - $at;
        $am = (int) $a->menu_order;
        $bm = (int) $b->menu_order;
        if ($am !== $bm) return $am - $bm;
        return strcmp((string) $a->post_title, (string) $b->post_title);
    });
}
// fallback: se nessuna competenza creata, lista dummy mostra solo CTA.

// 4 lawyers
$avvocati = get_posts([
    'post_type'   => 'avvocato',
    'numberposts' => 4,
    'orderby'     => ['menu_order' => 'ASC', 'date' => 'ASC'],
]);
$layout_team = saltelli_team_grid_layout();

// Filter pillole (tassonomia)
// Wave-Q fix #4 (feedback Elena): tab "Tutte" + "Altri servizi" rimossi → solo
// 3 cluster canonici (privati / imprese / contenzioso-amministrativo). Exclude
// eventuali term `altri-servizi` / `altri` / `altro` creati in admin (non canonici IA Wave 5).
$tipo_terms = get_terms([
    'taxonomy'   => 'tipo-area',
    'hide_empty' => false,
    'orderby'    => 'count',
    'order'      => 'DESC',
]);
if (is_array($tipo_terms) && !is_wp_error($tipo_terms)) {
    $tipo_terms = array_values(array_filter($tipo_terms, function ($t) {
        return !in_array($t->slug, ['altri-servizi', 'altri', 'altro'], true);
    }));
} else {
    $tipo_terms = [];
}
$default_filter_slug = (!empty($tipo_terms) && isset($tipo_terms[0]->slug))
    ? $tipo_terms[0]->slug
    : 'privati';

// Elena fix 2026-05-13: numerazione per-cluster (evita "buca" 02, …, 06, 08…).
// Pre-fix: $i globale 1..N nel loop render; il filtro JS nascondeva item non-matching →
// numeri saltati nel tab attivo. Post-fix: ogni cluster ha indice locale 01..N e total
// proprio. Mappa pre-calcolata qui per usarla 1:1 nel render.
//
// Elena fix 2026-05-14: supporto multi-cluster. Una competenza con più term
// tipo-area (es. Contrattualistica = Privati + Imprese) deve apparire sotto
// ogni tab a cui appartiene. Mappa diventa [post_id][cluster] e nel render si
// emette un <a> per ogni cluster del post (numerazione locale al cluster).
$canonical_clusters_h = ['privati', 'imprese', 'contenzioso-amministrativo'];
$cluster_counts_h     = array_fill_keys($canonical_clusters_h, 0);
$cluster_running_h    = array_fill_keys($canonical_clusters_h, 0);
$cluster_index_map_h  = []; // post_id => [cluster_slug => ['num' => '01', 'total' => '12']]
$post_clusters_map_h  = []; // post_id => [cluster_slug, …] (cluster reali del post, post fallback)

// Slug→name map per label tab (es. 'privati' => 'Per i Privati').
$cluster_name_map_h = [];
foreach ($tipo_terms as $term) {
    $cluster_name_map_h[$term->slug] = $term->name;
}

foreach ($competenze as $p) {
    $slugs = saltelli_competenza_category_slugs($p->ID);
    $slugs = array_values(array_intersect($slugs, $canonical_clusters_h));
    if (empty($slugs)) {
        // Orfani / non-canonici → ricadono sul cluster di default per restare visibili.
        $slugs = [$default_filter_slug];
    }
    $post_clusters_map_h[$p->ID] = $slugs;
    foreach ($slugs as $cs) {
        $cluster_counts_h[$cs] = ($cluster_counts_h[$cs] ?? 0) + 1;
    }
}
foreach ($competenze as $p) {
    foreach ($post_clusters_map_h[$p->ID] as $cs) {
        $cluster_running_h[$cs] = ($cluster_running_h[$cs] ?? 0) + 1;
        $cluster_index_map_h[$p->ID][$cs] = [
            'num'   => str_pad((string) $cluster_running_h[$cs], 2, '0', STR_PAD_LEFT),
            'total' => str_pad((string) ($cluster_counts_h[$cs] ?? 0), 2, '0', STR_PAD_LEFT),
        ];
    }
}

$cases = saltelli_homepage_cases();
$press = saltelli_press_outlets();
?>

<section class="sl-hero sl-page-hero sl-page-hero--homepage" id="hero">
    <?php /* design-handoff P3: bg image (variant B cream scrim). aria-hidden = decorativo; alt sull'img per SEO image search. */ ?>
    <div class="sl-hero__media" aria-hidden="true">
        <?php echo $hero_media_html; // markup interno costruito con esc_url()/esc_attr() sopra (Picsum) o wp_get_attachment_image() (foto reale) ?>
    </div>
    <div class="sl-hero__inner sl-container">
        <div class="sl-hero__main">
            <div class="sl-mono sl-hero__eyebrow"><?php echo esc_html($hero_eyebrow); ?></div>

            <h1 class="sl-hero__headline" data-split-reveal>
                <?php
                $words = preg_split('/\s+/', wp_strip_all_tags($hero_headline));
                foreach ($words as $i => $w) :
                    if ($w === '') continue;
                    ?>
                    <span class="sl-word sl-hero__word" data-i="<?php echo (int) $i; ?>"><?php echo esc_html($w); ?></span>
                <?php endforeach; ?>
            </h1>

            <div class="sl-hero__subheadline">
                <?php echo wp_kses_post($hero_sub); ?>
            </div>

            <a class="sl-btn sl-btn--primary" href="<?php echo esc_url($hero_cta_url); ?>">
                <span><?php echo esc_html($hero_cta_label); ?></span>
                <span class="arrow" aria-hidden="true">→</span>
            </a>
            <div class="sl-mono sl-hero__cta-note">
                <?php echo esc_html(saltelli_page_field('hero_cta_note', 'Prima consulenza conoscitiva — risposta entro 24 ore')); ?>
            </div>
        </div>

        <aside class="sl-hero__colophon" aria-label="<?php esc_attr_e('Coordinate studio', 'saltelli'); ?>">
            <div class="sl-mono sl-hero__colophon-label"><?php esc_html_e('Coordinate', 'saltelli'); ?></div>
            <div class="sl-hero__colophon-grid">
                <div class="sl-hero__colophon-item">
                    <div class="sl-mono"><?php esc_html_e('Indirizzo', 'saltelli'); ?></div>
                    <p><?php echo wp_kses_post(nl2br(esc_html($col_indirizzo))); ?></p>
                </div>
                <div class="sl-hero__colophon-item">
                    <div class="sl-mono"><?php esc_html_e('Orari', 'saltelli'); ?></div>
                    <p><?php echo wp_kses_post(nl2br(esc_html($col_orari))); ?></p>
                </div>
                <div class="sl-hero__colophon-item">
                    <div class="sl-mono"><?php esc_html_e('Contatti', 'saltelli'); ?></div>
                    <p>
                        <a class="sl-link" href="mailto:<?php echo esc_attr($col_email); ?>"><?php echo esc_html($col_email); ?></a><br>
                        <a class="sl-mono sl-hero__colophon-tel" href="tel:<?php echo esc_attr($col_tel_e164); ?>"><?php echo esc_html($col_tel); ?></a>
                    </p>
                </div>
            </div>
        </aside>
    </div>
    <?php /* Wave-Q fix #3: rimosso eyebrow "Scorri" (feedback Elena: ridondante con CTA Prenota una consulenza gratuita). */ ?>
    <?php if ($hero_credit !== '') : ?>
        <div class="sl-hero__photo-credit"><?php esc_html_e('Photo', 'saltelli'); ?> · <?php echo esc_html($hero_credit); ?></div>
    <?php endif; ?>
</section>

<section class="sl-areas" id="aree" aria-labelledby="aree-h">
    <div class="sl-container">
        <div class="sl-section-head">
            <div class="sl-mono"><?php echo esc_html(saltelli_page_field('home_areas_eyebrow', '§ 01 — Aree di pratica')); ?></div>
            <h2 class="sl-section-title" id="aree-h">
                <?php echo esc_html(saltelli_page_field('home_areas_h2_main', 'Diciassette aree.')); ?><br>
                <em><?php echo esc_html(saltelli_page_field('home_areas_h2_em', 'Tre presidiate in profondità.')); ?></em>
            </h2>
        </div>

        <div class="sl-areas__filters" role="tablist">
            <?php // Wave-Q fix #4: rimosso "Tutte" + "Altri servizi" → 3 cluster canonici, primo (privati) attivo di default. ?>
            <?php foreach ($tipo_terms as $idx => $term) :
                $is_active = ($idx === 0);
                ?>
                <button class="sl-areas__filter sl-mono<?php echo $is_active ? ' is-active' : ''; ?>" type="button" data-filter="<?php echo esc_attr($term->slug); ?>" aria-pressed="<?php echo $is_active ? 'true' : 'false'; ?>"><?php echo esc_html($term->name); ?></button>
            <?php endforeach; ?>
        </div>

        <div class="sl-areas__grid">
            <div class="sl-areas__list">
                <?php
                if (!empty($competenze)) :
                    foreach ($competenze as $p) :
                        // Wave 4.6: use is_tier_1 (Wave 1 ACF schema canonico).
                        $is_tier_1 = (bool) saltelli_field('is_tier_1', $p->ID, false);
                        $lead      = (string) saltelli_field('lead_breve', $p->ID, '');
                        if ($lead === '') {
                            $lead = (string) saltelli_field('answer_capsule', $p->ID, '');
                            if ($lead !== '') {
                                $lead = wp_trim_words($lead, 18, '…');
                            }
                        }
                        // Elena fix 2026-05-14: render un <a> per ogni cluster del post.
                        // Una competenza multi-tab (es. Contrattualistica = Privati+Imprese)
                        // appariva solo sotto la prima tab pre-fix.
                        $post_clusters_h = $post_clusters_map_h[$p->ID] ?? [$default_filter_slug];
                        foreach ($post_clusters_h as $cat_slug) :
                            $cat_label   = $cluster_name_map_h[$cat_slug] ?? '';
                            $num         = $cluster_index_map_h[$p->ID][$cat_slug]['num']   ?? '01';
                            $num_total_h = $cluster_index_map_h[$p->ID][$cat_slug]['total'] ?? '01';
                            ?>
                            <a class="sl-area<?php echo $is_tier_1 ? ' sl-area--tier1' : ''; ?>"
                               href="<?php echo esc_url(get_permalink($p)); ?>"
                               data-area-num="<?php echo esc_attr($num); ?>"
                               data-area-cat="<?php echo esc_attr($cat_slug); ?>"
                               data-area-lead="<?php echo esc_attr($lead); ?>"
                               data-area-label="<?php echo esc_attr($cat_label ?: ($is_tier_1 ? 'Tier 1' : 'Tier 2')); ?>">
                                <span class="sl-area__num sl-mono"><?php echo esc_html($num); ?> / <?php echo esc_html($num_total_h); ?></span>
                                <span class="sl-area__title"><?php echo esc_html(get_the_title($p)); ?></span>
                                <span class="sl-area__meta sl-mono">
                                    <?php // Wave-Q fix #18: label uniforme via helper centralizzato. ?>
                                    <?php echo esc_html(saltelli_tier_badge_label($p->ID, $is_tier_1, $cat_label)); ?>
                                    <span class="arrow" aria-hidden="true">→</span>
                                </span>
                            </a>
                        <?php endforeach;
                    endforeach;
                else :
                    ?>
                    <p class="sl-mono"><?php esc_html_e('Nessuna area di pratica pubblicata.', 'saltelli'); ?></p>
                <?php endif; ?>
            </div>

            <aside class="sl-area__preview" aria-live="polite" data-area-preview>
                <p class="sl-area__preview-empty"><?php echo esc_html(saltelli_page_field('home_areas_preview_hint', "Passa il cursore su un'area per leggerne la sintesi.")); ?></p>
            </aside>
        </div>
    </div>
</section>

<section class="sl-studio" id="studio" aria-labelledby="studio-h">
    <div class="sl-container">
        <div class="sl-section-head">
            <div class="sl-mono"><?php echo esc_html(saltelli_page_field('home_studio_eyebrow', '§ 02 — Lo studio')); ?></div>
            <h2 class="sl-section-title" id="studio-h"><?php echo esc_html($studio_titolo); ?></h2>
        </div>

        <div class="sl-studio__prose" data-drop-cap>
            <?php
            // Wave 4.7.fix.2: l'inline 3-paragrafi fallback è stato migrato a JSON
            // default_value (acf-json/group_theme_options_v1.json:studio_body) e
            // seedato in DB via inc/seed-theme-options.php → admin sempre coerente
            // con frontend. Empty state significa "Elena ha esplicitamente svuotato".
            if ($studio_body) {
                echo wp_kses_post($studio_body);
            }
            ?>
        </div>

        <figure class="sl-studio__plate">
            <?php if (is_array($studio_foto) && !empty($studio_foto['url'])) : ?>
                <img src="<?php echo esc_url($studio_foto['url']); ?>" alt="<?php echo esc_attr($studio_foto['alt'] ?: __('Facciata Studio Saltelli, Via Vannella Gaetani 27, Napoli', 'saltelli')); ?>" loading="lazy" decoding="async" width="1440" height="480">
            <?php else : ?>
                <div class="sl-studio__plate-placeholder" aria-hidden="true">
                    <span class="sl-mono sl-studio__plate-tl">Plate I · <?php esc_html_e('Facciata', 'saltelli'); ?></span>
                    <span class="sl-studio__plate-center">
                        <span class="sl-studio__plate-line">Via Vannella Gaetani, 27</span>
                        <span class="sl-mono">Fotografia in B/N · 1440 × 480 · placeholder</span>
                    </span>
                    <span class="sl-mono sl-studio__plate-br">Napoli · Chiaia</span>
                </div>
                <!-- TODO: replace with real Saltelli photo (facciata Via Vannella Gaetani, 27) -->
            <?php endif; ?>
        </figure>
    </div>
</section>

<section class="sl-team" id="avvocati" aria-labelledby="team-h">
    <div class="sl-container">
        <div class="sl-section-head">
            <div class="sl-mono"><?php echo esc_html(saltelli_page_field('home_team_eyebrow', '§ 03 — Avvocati')); ?></div>
            <h2 class="sl-section-title" id="team-h">
                <?php
                $title_lines = preg_split('/\r\n|\r|\n/', $team_titolo);
                foreach ($title_lines as $idx => $line) {
                    if ($line === '') continue;
                    if ($idx === 0) {
                        echo esc_html($line);
                    } else {
                        echo '<br><em>' . esc_html($line) . '</em>';
                    }
                }
                ?>
            </h2>
        </div>

        <?php if (!empty($avvocati)) : ?>
            <?php /* Elena fix 2026-05-14: data-sl-team-carousel per JS mobile carousel (stesso pattern §05 Dal blog) */ ?>
            <div class="sl-team__grid" data-sl-team-carousel>
                <?php foreach ($avvocati as $i => $av) :
                    $layout = $layout_team[$i] ?? ['col' => 1, 'span' => 12, 'offset' => 0];
                    $ruolo  = (string) saltelli_field('ruolo_breve', $av->ID, '');
                    $specs  = saltelli_get_attorney_specializations($av->ID);
                    $foto   = saltelli_field('foto_ritratto', $av->ID);
                    ?>
                    <article class="sl-team__lawyer"
                             style="--sl-col:<?php echo (int) $layout['col']; ?>; --sl-span:<?php echo (int) $layout['span']; ?>; --sl-offset:<?php echo (int) $layout['offset']; ?>px;">
                        <a class="sl-team__portrait" href="<?php echo esc_url(get_permalink($av)); ?>" aria-label="<?php echo esc_attr(get_the_title($av)); ?>">
                            <?php
                            if (has_post_thumbnail($av->ID)) {
                                echo get_the_post_thumbnail($av->ID, 'saltelli-attorney-portrait', [
                                    'loading'  => 'lazy',
                                    'decoding' => 'async',
                                    'alt'      => esc_attr(get_the_title($av) . ' · ' . $ruolo),
                                ]);
                            } elseif (is_array($foto) && !empty($foto['url'])) {
                                echo '<img src="' . esc_url($foto['url']) . '" alt="' . esc_attr($foto['alt'] ?: get_the_title($av)) . '" loading="lazy" decoding="async" width="600" height="800">';
                            } else {
                                echo '<span class="sl-team__placeholder" aria-hidden="true"><span class="sl-mono">' . esc_html__('Ritratto · 3:4', 'saltelli') . '</span></span>';
                                echo '<!-- TODO: replace with real Saltelli photo (ritratto avvocato) -->';
                            }
                            ?>
                        </a>
                        <?php if ($ruolo) : ?>
                            <div class="sl-mono sl-team__role"><?php echo esc_html($ruolo); ?></div>
                        <?php endif; ?>
                        <h3 class="sl-team__name">
                            <a href="<?php echo esc_url(get_permalink($av)); ?>"><?php echo esc_html(get_the_title($av)); ?></a>
                        </h3>
                        <?php if (!empty($specs)) : ?>
                            <ul class="sl-team__specs">
                                <?php foreach ($specs as $s) : ?>
                                    <li class="sl-tag"><?php echo esc_html($s); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php /* Dots indicator: visible solo mobile carousel (CSS @media <768) */ ?>
            <div class="sl-team__dots" aria-hidden="true">
                <?php for ($sl_team_dot_i = 0; $sl_team_dot_i < count($avvocati); $sl_team_dot_i++) : ?>
                    <span class="sl-team__dot<?php echo $sl_team_dot_i === 0 ? ' is-active' : ''; ?>"></span>
                <?php endfor; ?>
            </div>
        <?php else : ?>
            <p class="sl-mono"><?php esc_html_e('Nessun avvocato pubblicato.', 'saltelli'); ?></p>
        <?php endif; ?>
    </div>
</section>

<section class="sl-cases" id="casi" aria-labelledby="cases-h">
    <div class="sl-container">
        <div class="sl-section-head">
            <div class="sl-mono"><?php echo esc_html(saltelli_page_field('home_cases_eyebrow', '§ 04 — Vittorie recenti')); ?></div>
            <h2 class="sl-section-title" id="cases-h"><?php echo esc_html($cases_titolo); ?></h2>
        </div>

        <ol class="sl-cases__list" role="list">
            <?php foreach ($cases as $c) : ?>
                <li class="sl-cases__row">
                    <span class="sl-mono sl-cases__id"><?php echo esc_html($c['identifier']); ?></span>
                    <p class="sl-cases__desc"><?php echo esc_html($c['descrizione']); ?></p>
                    <span class="sl-cases__outcome"><?php echo esc_html($c['outcome']); ?></span>
                </li>
            <?php endforeach; ?>
        </ol>
    </div>
</section>

<?php
/* Elena fix 2026-05-14: nuova sezione §05 "Dal blog" — preview 3 articoli più
   recenti del CPT 'post'. Auto-aggiornata: l'editor pubblica un nuovo articolo
   da WP-Admin, frontend lo mostra senza intervento dev. */
$sl_blog_posts = get_posts([
    'post_type'      => 'post',
    'posts_per_page' => 3,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'post_status'    => 'publish',
    'no_found_rows'  => true,
]);
?>
<?php if (!empty($sl_blog_posts)) : ?>
<section class="sl-front-blog" id="blog" aria-labelledby="blog-h">
    <div class="sl-container">
        <div class="sl-section-head">
            <div class="sl-mono"><?php echo esc_html(saltelli_page_field('home_blog_eyebrow', '§ 05 — Dal blog')); ?></div>
            <h2 class="sl-section-title" id="blog-h">
                <?php echo esc_html(saltelli_page_field('home_blog_h2_main', 'Ultime letture')); ?><br>
                <em><?php echo esc_html(saltelli_page_field('home_blog_h2_em', 'dal nostro studio.')); ?></em>
            </h2>
        </div>

        <?php /* Elena fix 2026-05-14: pagination dots mobile carousel — visible <768px only,
                  active state via JS IntersectionObserver in main.js */ ?>
        <div class="sl-front-blog__grid" data-sl-blog-carousel>
            <?php foreach ($sl_blog_posts as $bp) :
                $cats     = get_the_category($bp->ID);
                $cat_name = !empty($cats) ? $cats[0]->name : '';
                $author   = get_the_author_meta('display_name', $bp->post_author);
                $thumb    = get_the_post_thumbnail_url($bp->ID, 'medium_large');
                $excerpt  = get_the_excerpt($bp);
                ?>
                <a class="sl-front-blog__card" href="<?php echo esc_url(get_permalink($bp)); ?>">
                    <?php if ($thumb) : ?>
                        <div class="sl-front-blog__card-img">
                            <img src="<?php echo esc_url($thumb); ?>" alt="" loading="lazy" decoding="async">
                        </div>
                    <?php endif; ?>
                    <div class="sl-front-blog__card-body">
                        <?php if ($cat_name !== '') : ?>
                            <div class="sl-mono sl-front-blog__card-cat"><?php echo esc_html($cat_name); ?></div>
                        <?php endif; ?>
                        <h3 class="sl-front-blog__card-title"><?php echo esc_html(get_the_title($bp)); ?></h3>
                        <?php if ($excerpt !== '') : ?>
                            <p class="sl-front-blog__card-excerpt"><?php echo esc_html(wp_trim_words($excerpt, 20, '…')); ?></p>
                        <?php endif; ?>
                        <div class="sl-mono sl-front-blog__card-meta">
                            <time datetime="<?php echo esc_attr(get_the_date('c', $bp)); ?>"><?php echo esc_html(get_the_date('j M Y', $bp)); ?></time>
                            <?php if ($author !== '') : ?>
                                <span aria-hidden="true"> · </span>
                                <span><?php echo esc_html($author); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <?php /* Dots indicator: visible only on mobile (CSS @media <768) */ ?>
        <div class="sl-front-blog__dots" aria-hidden="true">
            <?php for ($sl_dot_i = 0; $sl_dot_i < count($sl_blog_posts); $sl_dot_i++) : ?>
                <span class="sl-front-blog__dot<?php echo $sl_dot_i === 0 ? ' is-active' : ''; ?>"></span>
            <?php endfor; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php /* Wave 6 Pattern 6 — Testimonials block (renderizza solo se ci sono trust items con type=testimonianza). */ ?>
<section class="sl-front-testimonials">
    <div class="sl-container">
        <?php get_template_part('template-parts/testimonials-block'); ?>
    </div>
</section>

<?php /* Elena fix 2026-05-14: §05 Recensioni Google — placeholder swappato con widget
       Elfsight Google Reviews. Script elfsightcdn.com/platform.js caricato via
       wp_enqueue_script in inc/enqueue.php (async, footer, solo is_front_page()).
       Il <div class="elfsight-app-{uuid}"> è il mount-point dove la platform.js
       inietta il widget. data-elfsight-app-lazy attiva il lazy-load Elfsight. */ ?>
<section class="sl-front-reviews" aria-labelledby="reviews-h">
    <div class="sl-container">
        <div class="sl-front-reviews__head">
            <div class="sl-mono sl-front-reviews__label" id="reviews-h"><?php echo esc_html(saltelli_page_field('home_press_eyebrow', '§ 05 — Recensioni')); ?></div>
        </div>
        <div class="sl-front-reviews__widget">
            <!-- Elfsight Google Reviews | Untitled Google Reviews -->
            <div class="elfsight-app-ff7f7838-3389-49dd-a9e8-6afb12bdbab3" data-elfsight-app-lazy></div>
        </div>
    </div>
</section>

<?php /* Wave 6 Pattern 2 — Trust bar globale, prima della §06 contact (max pressure conversione). */ ?>
<section class="sl-front-trust">
    <div class="sl-container">
        <?php get_template_part('template-parts/trust-bar'); ?>
    </div>
</section>

<section class="sl-contact" id="contatti" aria-labelledby="contact-h">
    <div class="sl-container">
        <div class="sl-section-head">
            <div class="sl-mono"><?php echo esc_html(saltelli_page_field('home_contact_eyebrow', '§ 06 — Contatti')); ?></div>
            <div>
                <div class="sl-mono sl-contact__eyebrow">
                    <?php echo esc_html(saltelli_page_field('home_contact_subline', 'Prima consulenza conoscitiva gratuita · Risposta entro 24 ore')); ?>
                </div>
                <?php
                /* Elena fix 2026-05-13: rendering condizionale headline contatti.
                   - Multi-line mode (line1+line2+line3 popolati): pattern editoriale
                     originale, line3 in <em> oro (.sl-contact__title em).
                   - Single-line mode (solo line1, line2/3 vuote): auto-italicize
                     l'ULTIMA PAROLA di line1 in <em> per preservare l'accento oro
                     (feedback Elena 2026-05-13). Es. "Riceviamo solo su
                     appuntamento." → "Riceviamo solo su <em>appuntamento.</em>". */
                $sl_h2_l1 = (string) saltelli_page_field('home_contact_h2_line1', 'Riceviamo solo su appuntamento.');
                $sl_h2_l2 = (string) saltelli_page_field('home_contact_h2_line2', '');
                $sl_h2_l3 = (string) saltelli_page_field('home_contact_h2_line3', '');

                $sl_h2_l1_html = esc_html($sl_h2_l1);
                if ($sl_h2_l2 === '' && $sl_h2_l3 === '' && $sl_h2_l1 !== '') {
                    $parts = preg_split('/\s+/', trim($sl_h2_l1));
                    if (is_array($parts) && count($parts) > 1) {
                        $last = array_pop($parts);
                        $rest = implode(' ', $parts);
                        $sl_h2_l1_html = esc_html($rest) . ' <em>' . esc_html($last) . '</em>';
                    }
                }
                ?>
                <h2 class="sl-section-title sl-contact__title" id="contact-h">
                    <?php echo $sl_h2_l1_html; // esc_html già applicato per ogni segmento, <em> safe ?>
                    <?php if ($sl_h2_l2 !== '') : ?><br><?php echo esc_html($sl_h2_l2); ?><?php endif; ?>
                    <?php if ($sl_h2_l3 !== '') : ?><br><em><?php echo esc_html($sl_h2_l3); ?></em><?php endif; ?>
                </h2>
            </div>
        </div>

        <?php
        /* Elena fix 2026-05-14: aggiunta mini mappa Google nel §07 Contatti home.
           Single source of truth: l'iframe è letto da meta map_iframe della page
           /contatti/ — editor modifica una volta lì, vale per /contatti/ sidebar
           E homepage §07. */
        $sl_home_contatti_page = get_page_by_path('contatti');
        $sl_home_map_iframe = $sl_home_contatti_page
            ? (string) get_post_meta($sl_home_contatti_page->ID, 'map_iframe', true)
            : '';
        $sl_home_map_allowed = ['iframe' => [
            'src' => true, 'width' => true, 'height' => true, 'style' => true,
            'allowfullscreen' => true, 'loading' => true, 'referrerpolicy' => true,
            'frameborder' => true, 'title' => true, 'aria-label' => true,
        ]];
        ?>
        <div class="sl-contact__grid sl-contact__grid--with-map">
            <div class="sl-contact__items-stack">
                <div class="sl-contact__item">
                    <div class="sl-mono"><?php esc_html_e('Indirizzo', 'saltelli'); ?></div>
                    <p class="sl-contact__big"><?php echo wp_kses_post(nl2br(esc_html($col_indirizzo))); ?></p>
                </div>
                <div class="sl-contact__item">
                    <div class="sl-mono"><?php esc_html_e('Telefono', 'saltelli'); ?></div>
                    <p class="sl-contact__big"><a href="tel:<?php echo esc_attr($col_tel_e164); ?>"><?php echo esc_html($col_tel); ?></a></p>
                </div>
                <div class="sl-contact__item">
                    <div class="sl-mono"><?php esc_html_e('Email', 'saltelli'); ?></div>
                    <p class="sl-contact__big"><a href="mailto:<?php echo esc_attr($col_email); ?>"><?php echo esc_html($col_email); ?></a></p>
                </div>
            </div>
            <?php if ($sl_home_map_iframe !== '') : ?>
                <div class="sl-contact__map" aria-label="<?php esc_attr_e('Mappa studio Saltelli — Chiaia, Napoli', 'saltelli'); ?>">
                    <?php echo wp_kses($sl_home_map_iframe, $sl_home_map_allowed); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="sl-contact__cta">
            <?php /* Wave 4.6: cta_default_url + cta_default_label editabili globally.
                    cta_subline_italic emessa hidden per future styling editor (designer can show via CSS). */ ?>
            <a class="sl-btn sl-btn--primary" href="<?php echo esc_url($cta_default_url); ?>">
                <span><?php echo esc_html($cta_default_label); ?></span>
                <span class="arrow" aria-hidden="true">→</span>
            </a>
            <?php if ($cta_subline_italic !== '') : ?>
                <p class="sl-contact__cta-subline" hidden><em><?php echo esc_html($cta_subline_italic); ?></em></p>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php
get_footer();
