<?php
/**
 * Template: search form.
 *
 * @package Saltelli
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <label for="s-<?php echo esc_attr(uniqid()); ?>" class="screen-reader-text"><?php esc_html_e('Cerca:', 'saltelli'); ?></label>
    <input type="search" id="s-<?php echo esc_attr(uniqid()); ?>" class="search-field" placeholder="<?php esc_attr_e('Cerca…', 'saltelli'); ?>" value="<?php echo esc_attr(get_search_query()); ?>" name="s">
    <button type="submit" class="search-submit"><?php esc_html_e('Cerca', 'saltelli'); ?></button>
</form>
