<?php
/**
 * Footer — denso 3 colonne, tema scuro (--primary navy + cream text).
 * Markup tradotto da homepage-desktop.jsx + homepage-mobile.jsx
 *
 * @package Saltelli
 */
$studio = saltelli_studio_data();
$ftr_indirizzo = saltelli_option('colophon_indirizzo', "Via Vannella Gaetani, 27\n80121 Napoli — Chiaia");
$ftr_tel       = saltelli_option('contact_telefono_pubblico', '+39 081 1813 1119');
$ftr_email     = saltelli_option('contact_email_pubblica', $studio['email']);
$ftr_pec       = saltelli_option('contact_pec', $studio['pec']);
$ftr_piva      = saltelli_option('contact_piva', '06685101211');
$ftr_tel_e164  = saltelli_studio_phone_e164();
?>
</main><!-- /main#site-main -->

<footer class="sl-footer" role="contentinfo">
    <div class="sl-container sl-footer__inner">

        <div class="sl-footer__cols">
            <div class="sl-footer__col sl-footer__col--brand">
                <a class="sl-footer__brand sl-logo--stack" href="<?php echo esc_url(home_url('/')); ?>" rel="home" aria-label="Studio Legale Saltelli — Home">
                    <span class="sl-logo__s-row1">Studio Legale</span>
                    <span class="sl-logo__s-row2"><span class="sl-logo__swash">S</span>altelli</span>
                    <span class="sl-logo__s-row3">Napoli · Dal 1999</span>
                </a>
                <?php
                wp_nav_menu([
                    'theme_location' => 'footer-studio',
                    'container'      => false,
                    'menu_class'     => 'sl-footer__menu',
                    'fallback_cb'    => 'saltelli_footer_studio_fallback',
                    'depth'          => 1,
                ]);
                ?>
            </div>

            <div class="sl-footer__col sl-footer__col--aree">
                <div class="sl-mono sl-footer__col-label"><?php esc_html_e('Diciannove aree', 'saltelli'); ?></div>
                <?php
                wp_nav_menu([
                    'theme_location' => 'footer-aree',
                    'container'      => false,
                    'menu_class'     => 'sl-footer__menu sl-footer__menu--aree',
                    'fallback_cb'    => 'saltelli_footer_aree_fallback',
                    'depth'          => 1,
                ]);
                ?>
            </div>

            <div class="sl-footer__col sl-footer__col--contatti">
                <div class="sl-mono sl-footer__col-label"><?php esc_html_e('Contatti', 'saltelli'); ?></div>
                <address class="sl-footer__contact">
                    <?php echo wp_kses_post(nl2br(esc_html($ftr_indirizzo))); ?><br><br>
                    <a href="tel:<?php echo esc_attr($ftr_tel_e164); ?>"><?php echo esc_html($ftr_tel); ?></a><br>
                    <a href="mailto:<?php echo esc_attr($ftr_email); ?>"><?php echo esc_html($ftr_email); ?></a><br>
                    <span class="sl-footer__pec"><?php echo esc_html($ftr_pec); ?></span><br><br>
                    <?php esc_html_e('Ordine Avv. Napoli', 'saltelli'); ?><br>
                    <?php esc_html_e('P.IVA', 'saltelli'); ?> <?php echo esc_html($ftr_piva); ?>
                </address>
            </div>
        </div>

        <div class="sl-footer__bottom">
            <div class="sl-mono sl-footer__copy">
                © <?php echo esc_html(date('Y')); ?> Studio Legale Emiliano Saltelli &amp; Partners
            </div>
            <?php
            wp_nav_menu([
                'theme_location' => 'footer-legal',
                'container'      => false,
                'menu_class'     => 'sl-footer__menu sl-footer__menu--legal',
                'fallback_cb'    => 'saltelli_footer_legal_fallback',
                'depth'          => 1,
            ]);
            ?>
            <div class="sl-footer__social sl-mono">
                <?php if (!empty($studio['social']['instagram'])) : ?>
                    <a href="<?php echo esc_url($studio['social']['instagram']); ?>" rel="noopener" target="_blank">Instagram</a>
                <?php endif; ?>
                <?php
                $em_li = saltelli_attorney_linkedin('emiliano-saltelli');
                if ($em_li) :
                    ?>
                    <a href="<?php echo esc_url($em_li); ?>" rel="noopener" target="_blank">LinkedIn</a>
                <?php endif; ?>
            </div>
        </div>

    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
