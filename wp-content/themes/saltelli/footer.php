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

    <?php /* === IMPECCABLE v0.20.1 [T2] BEGIN — Newsletter footer (Brevo legacy fallback HTML statico) ===
       Action URL = endpoint Brevo legacy "link.studiolegalesaltelli.it".
       SE fallisce sul nuovo dominio → upgrade a plugin mailin (Brevo) +
       shortcode [sibwp_form id=1]. Plugin attualmente NON installato. */ ?>
    <section class="sl-footer__newsletter" aria-labelledby="newsletter-h">
        <div class="sl-container">
            <div class="sl-footer__newsletter-grid">
                <div class="sl-footer__newsletter-intro">
                    <h3 class="sl-footer__newsletter-h" id="newsletter-h"><?php esc_html_e('Resta aggiornato', 'saltelli'); ?></h3>
                    <p class="sl-footer__newsletter-p">
                        <?php esc_html_e('Newsletter editoriale: novità giurisprudenziali, casi e guide.', 'saltelli'); ?>
                        <em><?php esc_html_e('Una al mese.', 'saltelli'); ?></em>
                        <?php esc_html_e('No spam.', 'saltelli'); ?>
                    </p>
                </div>
                <form class="sl-footer__newsletter-form form-newsletter"
                      action="https://link.studiolegalesaltelli.it/api/v3/contacts"
                      method="POST"
                      novalidate>
                    <div class="campi-newsletter">
                        <label class="screen-reader-text" for="sl-newsletter-firstname"><?php esc_html_e('Nome', 'saltelli'); ?></label>
                        <input id="sl-newsletter-firstname"
                               type="text"
                               name="FIRSTNAME"
                               placeholder="<?php esc_attr_e('Nome*', 'saltelli'); ?>"
                               class="sl-footer__newsletter-input"
                               autocomplete="given-name"
                               required>
                        <label class="screen-reader-text" for="sl-newsletter-email"><?php esc_html_e('Email', 'saltelli'); ?></label>
                        <input id="sl-newsletter-email"
                               type="email"
                               name="email"
                               placeholder="<?php esc_attr_e('Email*', 'saltelli'); ?>"
                               class="sl-footer__newsletter-input"
                               autocomplete="email"
                               required>
                    </div>
                    <label class="sl-footer__newsletter-gdpr">
                        <input type="checkbox" name="terms" required>
                        <span>
                            <?php
                            printf(
                                /* translators: %s = Privacy Policy link */
                                esc_html__('Ho letto la %s e acconsento al trattamento dei miei dati per ricevere la newsletter.', 'saltelli'),
                                '<a href="' . esc_url(home_url('/privacy/')) . '" class="sl-link">' . esc_html__('Privacy Policy', 'saltelli') . '</a>'
                            );
                            ?>
                        </span>
                    </label>
                    <button type="submit" class="sl-btn sl-btn--primary sl-footer__newsletter-submit">
                        <span><?php esc_html_e('Iscriviti', 'saltelli'); ?></span>
                        <span class="arrow" aria-hidden="true">→</span>
                    </button>
                </form>
            </div>
        </div>
    </section>
    <?php /* === IMPECCABLE v0.20.1 [T2] END === */ ?>

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
