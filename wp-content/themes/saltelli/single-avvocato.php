<?php
/**
 * Template: Single CPT avvocato.
 *
 * @package Saltelli
 */
get_header();

while (have_posts()) :
    the_post();
    $post_id   = get_the_ID();
    $ruolo     = saltelli_field('ruolo_breve', $post_id, '');
    $bio_breve = saltelli_field('bio_breve', $post_id, '');
    $bio_est   = saltelli_field('bio_estesa', $post_id, '');
    $email     = saltelli_field('email_pubblica', $post_id, '');
    $tel       = saltelli_field('telefono_pubblico', $post_id, '');
    $linkedin  = saltelli_field('same_as_linkedin', $post_id, '');
    $specs     = saltelli_get_attorney_specializations($post_id);
    $aree      = saltelli_field('aree_competenza_correlate', $post_id, []);
    ?>
    <article <?php post_class('avvocato'); ?>>

        <!-- TODO Style & Animation agent: hero avvocato (foto verticale + nome + ruolo) -->
        <header class="avvocato__hero container">
            <h1><?php the_title(); ?></h1>
            <?php if ($ruolo) : ?>
                <p class="avvocato__ruolo"><?php echo esc_html($ruolo); ?></p>
            <?php endif; ?>
            <?php if ($bio_breve) : ?>
                <p class="avvocato__bio-breve"><?php echo esc_html($bio_breve); ?></p>
            <?php endif; ?>
            <!-- TODO: foto Saltelli (ACF foto_ritratto / featured image) -->
        </header>

        <!-- TODO Style & Animation agent: bio estesa -->
        <?php if ($bio_est) : ?>
            <section class="avvocato__bio container">
                <?php echo wp_kses_post($bio_est); ?>
            </section>
        <?php endif; ?>

        <!-- TODO Style & Animation agent: specializzazioni come tag pills -->
        <?php if (!empty($specs)) : ?>
            <section class="avvocato__specializzazioni container">
                <h2><?php esc_html_e('Specializzazioni', 'saltelli'); ?></h2>
                <ul>
                    <?php foreach ($specs as $s) : ?>
                        <li><?php echo esc_html($s); ?></li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>

        <!-- TODO Style & Animation agent: aree di competenza correlate (cards) -->
        <?php if (!empty($aree)) : ?>
            <section class="avvocato__aree container">
                <h2><?php esc_html_e('Aree di competenza', 'saltelli'); ?></h2>
                <ul>
                    <?php
                    $ids = is_array($aree) ? $aree : [$aree];
                    foreach ($ids as $aid) :
                        $aid = is_object($aid) ? (int) $aid->ID : (int) $aid;
                        if (!$aid) {
                            continue;
                        }
                        ?>
                        <li><a href="<?php echo esc_url(get_permalink($aid)); ?>"><?php echo esc_html(get_the_title($aid)); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </section>
        <?php endif; ?>

        <!-- TODO Style & Animation agent: contatti diretti dell'avvocato -->
        <section class="avvocato__contatti container">
            <h2><?php esc_html_e('Contatti', 'saltelli'); ?></h2>
            <ul>
                <?php if ($tel) : ?>
                    <li><a href="tel:<?php echo esc_attr(preg_replace('/[^0-9+]/', '', $tel)); ?>"><?php echo esc_html($tel); ?></a></li>
                <?php endif; ?>
                <?php if ($email) : ?>
                    <li><a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></li>
                <?php endif; ?>
                <?php if ($linkedin) : ?>
                    <li><a href="<?php echo esc_url($linkedin); ?>" rel="noopener" target="_blank">LinkedIn</a></li>
                <?php endif; ?>
            </ul>
        </section>

    </article>
    <?php
endwhile;

get_footer();
