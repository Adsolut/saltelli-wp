<?php
/**
 * Footer — chiusura main + footer site + scripts.
 *
 * @package Saltelli
 */
?>
</main><!-- /main#site-main -->

<footer class="site-footer" role="contentinfo">
    <div class="container">

        <!-- TODO Style & Animation agent: layout footer in 3-4 colonne (Studio / Aree / Contatti / Legali) -->

        <div class="footer-col footer-col--studio">
            <h3><?php esc_html_e('Lo Studio', 'saltelli'); ?></h3>
            <?php
            wp_nav_menu([
                'theme_location' => 'footer-studio',
                'container'      => false,
                'menu_class'     => 'menu menu--footer',
                'fallback_cb'    => '__return_false',
                'depth'          => 1,
            ]);
            ?>
        </div>

        <div class="footer-col footer-col--aree">
            <h3><?php esc_html_e('Aree di pratica', 'saltelli'); ?></h3>
            <?php
            wp_nav_menu([
                'theme_location' => 'footer-aree',
                'container'      => false,
                'menu_class'     => 'menu menu--footer',
                'fallback_cb'    => '__return_false',
                'depth'          => 1,
            ]);
            ?>
        </div>

        <div class="footer-col footer-col--contatti">
            <h3><?php esc_html_e('Contatti', 'saltelli'); ?></h3>
            <!-- TODO Style & Animation agent: dati di contatto + mappa mini + CTA WhatsApp -->
            <p>
                Via Vannella Gaetani, 27<br>
                80121 Napoli (Chiaia)<br>
                <a href="tel:+390811813119">+39 081 1813 1119</a><br>
                <a href="mailto:info@studiolegalesaltelli.it">info@studiolegalesaltelli.it</a>
            </p>
        </div>

        <div class="footer-col footer-col--legal">
            <h3><?php esc_html_e('Informazioni legali', 'saltelli'); ?></h3>
            <?php
            wp_nav_menu([
                'theme_location' => 'footer-legal',
                'container'      => false,
                'menu_class'     => 'menu menu--footer',
                'fallback_cb'    => '__return_false',
                'depth'          => 1,
            ]);
            ?>
            <p class="copyright">© <?php echo esc_html(date('Y')); ?> Studio Legale Emiliano Saltelli &amp; Partners — P.IVA 06685101211</p>
        </div>

    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
