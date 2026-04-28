<?php
/**
 * Template: 404.
 *
 * @package Saltelli
 */
get_header();
?>

<div class="container">

    <header class="error-header">
        <h1><?php esc_html_e('Pagina non trovata', 'saltelli'); ?></h1>
        <p><?php esc_html_e('La pagina che stai cercando non esiste o è stata spostata.', 'saltelli'); ?></p>
    </header>

    <!-- TODO Style & Animation agent: shortcuts (avvocati / aree principali / contatti) -->
    <div class="error-actions">
        <a class="btn" href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Torna alla home', 'saltelli'); ?></a>
        <?php get_search_form(); ?>
    </div>

</div>

<?php
get_footer();
