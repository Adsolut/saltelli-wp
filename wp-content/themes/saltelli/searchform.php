<?php
/**
 * Template: search form.
 *
 * @package Saltelli
 */
$sl_id = 'sl-search-' . esc_attr(uniqid());
?>
<form role="search" method="get" class="sl-search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <label for="<?php echo esc_attr($sl_id); ?>" class="screen-reader-text"><?php esc_html_e('Cerca:', 'saltelli'); ?></label>
    <input type="search" id="<?php echo esc_attr($sl_id); ?>" class="sl-search-form__input" placeholder="<?php esc_attr_e('Cerca…', 'saltelli'); ?>" value="<?php echo esc_attr(get_search_query()); ?>" name="s">
    <button type="submit" class="sl-search-form__submit sl-mono">
        <span><?php esc_html_e('Cerca', 'saltelli'); ?></span>
        <span class="arrow" aria-hidden="true">→</span>
    </button>
</form>
