<?php
/**
 * Template: 404.
 *
 * @package Saltelli
 */
get_header();
?>

<section class="sl-404">
    <div class="sl-container">
        <div class="sl-mono">§ 404</div>
        <h1 class="sl-section-title">
            <?php esc_html_e('Pagina', 'saltelli'); ?><br>
            <em><?php esc_html_e('non trovata.', 'saltelli'); ?></em>
        </h1>
        <p class="sl-404__lede">
            <?php esc_html_e("La pagina che stai cercando non esiste o è stata spostata. Probabilmente l'URL è cambiato dopo la migrazione del sito.", 'saltelli'); ?>
        </p>

        <div class="sl-404__actions">
            <a class="sl-btn sl-btn--primary" href="<?php echo esc_url(home_url('/')); ?>">
                <span><?php esc_html_e('Torna alla home', 'saltelli'); ?></span>
                <span class="arrow" aria-hidden="true">→</span>
            </a>
            <a class="sl-btn" href="<?php echo esc_url(get_post_type_archive_link('competenza')); ?>">
                <span><?php esc_html_e('Aree di pratica', 'saltelli'); ?></span>
                <span class="arrow" aria-hidden="true">→</span>
            </a>
            <a class="sl-btn" href="<?php echo esc_url(home_url('/contatti/')); ?>">
                <span><?php esc_html_e('Contatti', 'saltelli'); ?></span>
                <span class="arrow" aria-hidden="true">→</span>
            </a>
        </div>

        <div class="sl-404__search">
            <?php get_search_form(); ?>
        </div>
    </div>
</section>

<?php
get_footer();
