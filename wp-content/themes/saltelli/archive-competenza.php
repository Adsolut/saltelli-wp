<?php
/**
 * Template: Archive CPT competenza.
 * Lista 19 aree con flag tier-1 evidenziato.
 *
 * @package Saltelli
 */
get_header();
?>

<div class="container">

    <header class="archive-header">
        <h1><?php esc_html_e('Aree di pratica', 'saltelli'); ?></h1>
        <!-- TODO: copy intro aree da Elena -->
    </header>

    <?php
    // Tier-1 first, tier-2 second.
    $tier_1 = get_posts([
        'post_type'   => 'competenza',
        'numberposts' => -1,
        'orderby'     => 'title',
        'order'       => 'ASC',
        'meta_query'  => [
            ['key' => 'is_tier_1_focus', 'value' => '1', 'compare' => '='],
        ],
    ]);
    $tier_2 = get_posts([
        'post_type'   => 'competenza',
        'numberposts' => -1,
        'orderby'     => 'title',
        'order'       => 'ASC',
        'meta_query'  => [
            'relation' => 'OR',
            ['key' => 'is_tier_1_focus', 'value' => '1', 'compare' => '!='],
            ['key' => 'is_tier_1_focus', 'compare' => 'NOT EXISTS'],
        ],
    ]);
    ?>

    <?php if (!empty($tier_1)) : ?>
        <!-- TODO Style & Animation agent: tier-1 hero cards (3 aree con ritratto avvocato lead + answer capsule) -->
        <section class="competenze--tier-1" aria-labelledby="competenze-tier-1-h">
            <h2 id="competenze-tier-1-h"><?php esc_html_e('Specializzazioni di punta', 'saltelli'); ?></h2>
            <ul class="competenze-grid competenze-grid--featured">
                <?php foreach ($tier_1 as $p) : ?>
                    <li class="competenza-card competenza-card--tier-1">
                        <a href="<?php echo esc_url(get_permalink($p)); ?>">
                            <h3><?php echo esc_html(get_the_title($p)); ?></h3>
                            <?php
                            $ans = saltelli_field('answer_capsule', $p->ID, '');
                            if ($ans) {
                                echo '<p>' . esc_html(wp_trim_words($ans, 28, '…')) . '</p>';
                            }
                            ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php endif; ?>

    <?php if (!empty($tier_2)) : ?>
        <!-- TODO Style & Animation agent: tier-2 grid compatta (16 aree) -->
        <section class="competenze--tier-2" aria-labelledby="competenze-tier-2-h">
            <h2 id="competenze-tier-2-h"><?php esc_html_e('Tutte le aree', 'saltelli'); ?></h2>
            <ul class="competenze-grid">
                <?php foreach ($tier_2 as $p) : ?>
                    <li class="competenza-card">
                        <a href="<?php echo esc_url(get_permalink($p)); ?>">
                            <h3><?php echo esc_html(get_the_title($p)); ?></h3>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php endif; ?>

    <?php if (empty($tier_1) && empty($tier_2)) : ?>
        <p><?php esc_html_e('Nessuna area di competenza pubblicata.', 'saltelli'); ?></p>
    <?php endif; ?>

</div>

<?php
get_footer();
