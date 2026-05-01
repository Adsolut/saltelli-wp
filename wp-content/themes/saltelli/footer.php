<?php
/**
 * Footer System v2 — 4 fasce editoriali (replicato da saltelli-s2-footer.jsx).
 *
 *   Fascia 1: § Ultima chiamata · pre-footer CTA (cream surface, asimm 8/4)
 *   Fascia 2: Main 4-col (brand · aree · studio · info istituzionali) on navy
 *   Fascia 3: Newsletter "L'editoriale del giovedì" (Brevo legacy form)
 *   Fascia 4: Bottom legal (copy + privacy/cookie/note + Adsolut credit)
 *
 * @package Saltelli
 * @since 0.20.2
 */
$studio        = saltelli_studio_data();
$ftr_indirizzo = saltelli_option('colophon_indirizzo', "Via Vannella Gaetani, 27\n80121 Napoli — Chiaia");
$ftr_tel       = saltelli_option('contact_telefono_pubblico', '+39 081 1813 1119');
$ftr_email     = saltelli_option('contact_email_pubblica', $studio['email']);
$ftr_pec       = saltelli_option('contact_pec', $studio['pec']);
$ftr_piva      = saltelli_option('contact_piva', '06685101211');
$ftr_tel_e164  = saltelli_studio_phone_e164();

$em_li = function_exists('saltelli_attorney_linkedin') ? saltelli_attorney_linkedin('emiliano-saltelli') : '';

/* === IMPECCABLE v0.20.2 [T1] aree tier-1 / tier-2 hardcoded da JSX (19 totali) === */
$ftr_tier1 = [
    ['n' => '01', 't' => __('Diritto tributario', 'saltelli'),         'href' => '/competenze/diritto-tributario/'],
    ['n' => '02', 't' => __('Diritto del lavoro', 'saltelli'),         'href' => '/competenze/diritto-del-lavoro/'],
    ['n' => '03', 't' => __('Diritto di famiglia LGBTQ+', 'saltelli'), 'href' => '/competenze/diritto-di-famiglia-lgbtq/'],
];
$ftr_tier2 = [
    ['t' => __('Cartelle esattoriali', 'saltelli'),  'href' => '/competenze/cartelle-esattoriali-e-multe/'],
    ['t' => __('Recupero crediti', 'saltelli'),      'href' => '/competenze/recupero-crediti/'],
    ['t' => __('Diritto di famiglia', 'saltelli'),   'href' => '/competenze/diritto-di-famiglia/'],
    ['t' => __('Responsabilità medica', 'saltelli'), 'href' => '/competenze/responsabilita-medica/'],
    ['t' => __('Diritto bancario', 'saltelli'),      'href' => '/competenze/diritto-bancario/'],
    ['t' => __('Diritto condominiale', 'saltelli'),  'href' => '/competenze/diritto-condominiale/'],
    ['t' => __('Diritto immigrazione', 'saltelli'),  'href' => '/competenze/diritto-dellimmigrazione/'],
    ['t' => __('Diritto penale', 'saltelli'),        'href' => '/competenze/diritto-penale/'],
    ['t' => __('Diritto previdenziale', 'saltelli'), 'href' => '/competenze/diritto-previdenziale/'],
    ['t' => __('Assicurazioni', 'saltelli'),         'href' => '/competenze/diritto-delle-assicurazioni/'],
    ['t' => __('Successioni', 'saltelli'),           'href' => '/competenze/diritto-delle-successioni/'],
    ['t' => __('Risarcimento danni', 'saltelli'),    'href' => '/competenze/risarcimento-danni/'],
    ['t' => __('Responsabilità civile', 'saltelli'), 'href' => '/competenze/responsabilita-civile/'],
    ['t' => __('Domiciliazione', 'saltelli'),        'href' => '/competenze/domiciliazione-dimpresa/'],
    ['t' => __('Consulenze online', 'saltelli'),     'href' => '/competenze/consulenze-online/'],
    ['t' => __('Diritto amministrativo', 'saltelli'),'href' => '/competenze/diritto-amministrativo/'],
];
?>
</main><!-- /main#site-main -->

<?php /* ═══ FASCIA 1 · CTA EDITORIALE pre-footer ═══ */ ?>
<section class="sl-foot-precta" aria-labelledby="precta-h">
    <div class="sl-foot-precta__inner">
        <div class="sl-foot-cta">
            <div class="sl-foot-precta__lede">
                <div class="sl-mono sl-foot-precta__eyebrow"><?php esc_html_e('§ Ultima chiamata', 'saltelli'); ?></div>
                <h2 class="sl-foot-precta__h2" id="precta-h">
                    <?php esc_html_e('Vorresti raccontarci', 'saltelli'); ?><br>
                    <?php esc_html_e('la tua pratica?', 'saltelli'); ?>
                </h2>
                <p class="sl-foot-precta__p">
                    <?php esc_html_e('Trenta minuti di prima consulenza conoscitiva gratuita. In studio o online. Risposta entro 24 ore.', 'saltelli'); ?>
                </p>
            </div>
            <div class="sl-foot-precta__action">
                <a class="sl-btn sl-btn--primary" href="<?php echo esc_url(home_url('/contatti/')); ?>">
                    <span><?php esc_html_e('Prenota un incontro', 'saltelli'); ?></span>
                    <span class="arrow" aria-hidden="true">→</span>
                </a>
                <p class="sl-mono sl-foot-precta__trust">
                    <?php esc_html_e('Nessun obbligo · Nessun costo · Riservatezza assoluta', 'saltelli'); ?>
                </p>
            </div>
        </div>
    </div>
</section>

<footer class="sl-footer sl-footer--v2" role="contentinfo">

    <?php /* ═══ FASCIA 2 · MAIN FOOTER 4-col ═══ */ ?>
    <section class="sl-foot-main-wrap">
        <div class="sl-foot-main-inner">
            <div class="sl-foot-main">

                <?php /* COL 1 — BRAND IDENTITY */ ?>
                <div class="sl-foot-col sl-foot-col--brand">
                    <a class="sl-foot-logo" href="<?php echo esc_url(home_url('/')); ?>" rel="home" aria-label="<?php esc_attr_e('Studio Legale Saltelli — Home', 'saltelli'); ?>">
                        <span class="sl-mono sl-foot-logo__row1"><?php esc_html_e('Studio Legale', 'saltelli'); ?></span>
                        <span class="sl-foot-logo__row2"><span class="sl-foot-logo__swash">S</span>altelli</span>
                        <span class="sl-mono sl-foot-logo__row3"><?php esc_html_e('Napoli · Dal 1999', 'saltelli'); ?></span>
                    </a>

                    <p class="sl-foot-brand-statement">
                        <?php esc_html_e('Un atelier editoriale italiano.', 'saltelli'); ?><br>
                        <?php esc_html_e('Quattro avvocati a Chiaia.', 'saltelli'); ?><br>
                        <?php esc_html_e("Vent'anni di pratica accanto a famiglie e imprese.", 'saltelli'); ?>
                    </p>

                    <div class="sl-mono sl-foot-contact-mini">
                        <a class="sl-foot-link" href="tel:<?php echo esc_attr($ftr_tel_e164); ?>"><?php echo esc_html($ftr_tel); ?></a>
                        <a class="sl-foot-link" href="mailto:<?php echo esc_attr($ftr_email); ?>"><?php echo esc_html($ftr_email); ?></a>
                        <span><?php esc_html_e('Lun–Ven · 09:30–18:30', 'saltelli'); ?></span>
                    </div>

                    <div class="sl-foot-social">
                        <?php if (!empty($studio['social']['instagram'])) : ?>
                            <a class="sl-foot-link sl-mono" href="<?php echo esc_url($studio['social']['instagram']); ?>" rel="noopener" target="_blank">Instagram</a>
                        <?php endif; ?>
                        <?php if ($em_li) : ?>
                            <a class="sl-foot-link sl-mono" href="<?php echo esc_url($em_li); ?>" rel="noopener" target="_blank">LinkedIn</a>
                        <?php endif; ?>
                        <a class="sl-foot-link sl-mono" href="https://wa.me/<?php echo esc_attr(preg_replace('/[^0-9]/', '', (string) $studio['whatsapp'])); ?>" rel="noopener" target="_blank">WhatsApp</a>
                    </div>
                </div>

                <?php /* COL 2 — AREE DI PRATICA (tier-1 + tier-2) */ ?>
                <div class="sl-foot-col sl-foot-col--aree">
                    <div class="sl-mono sl-foot-col__label"><?php esc_html_e('Diciannove aree', 'saltelli'); ?></div>

                    <nav class="sl-foot-tier1" aria-label="<?php esc_attr_e('Aree principali', 'saltelli'); ?>">
                        <?php foreach ($ftr_tier1 as $t1) : ?>
                            <a class="sl-foot-link sl-foot-tier1__row" href="<?php echo esc_url(home_url($t1['href'])); ?>">
                                <span class="sl-mono sl-foot-tier1__num"><?php echo esc_html($t1['n']); ?></span>
                                <span><?php echo esc_html($t1['t']); ?></span>
                            </a>
                        <?php endforeach; ?>
                    </nav>

                    <hr class="sl-foot-hairline" aria-hidden="true">

                    <nav class="sl-foot-tier2" aria-label="<?php esc_attr_e('Altre aree', 'saltelli'); ?>">
                        <?php foreach ($ftr_tier2 as $t2) : ?>
                            <a class="sl-foot-link" href="<?php echo esc_url(home_url($t2['href'])); ?>"><?php echo esc_html($t2['t']); ?></a>
                        <?php endforeach; ?>
                    </nav>

                    <hr class="sl-foot-hairline" aria-hidden="true">

                    <a class="sl-foot-link sl-mono sl-foot-allareas" href="<?php echo esc_url(home_url('/competenze/')); ?>">
                        <?php esc_html_e('Tutte le aree', 'saltelli'); ?> <span class="arrow" aria-hidden="true">→</span>
                    </a>
                </div>

                <?php /* COL 3 — STUDIO + RISORSE + SERVIZIO */ ?>
                <div class="sl-foot-col sl-foot-col--studio">
                    <div class="sl-mono sl-foot-col__label"><?php esc_html_e('Studio', 'saltelli'); ?></div>
                    <nav class="sl-foot-nav-list" aria-label="<?php esc_attr_e('Studio', 'saltelli'); ?>">
                        <a class="sl-foot-link" href="<?php echo esc_url(home_url('/chi-siamo/')); ?>"><?php esc_html_e('Lo studio', 'saltelli'); ?></a>
                        <a class="sl-foot-link" href="<?php echo esc_url(home_url('/avvocati/')); ?>"><?php esc_html_e('Avvocati', 'saltelli'); ?></a>
                        <a class="sl-foot-link" href="<?php echo esc_url(home_url('/casi/')); ?>"><?php esc_html_e('Casi rappresentativi', 'saltelli'); ?></a>
                        <a class="sl-foot-link" href="<?php echo esc_url(home_url('/costi/')); ?>"><?php esc_html_e('Costi e prima consulenza', 'saltelli'); ?></a>
                    </nav>

                    <hr class="sl-foot-hairline" aria-hidden="true">

                    <div class="sl-mono sl-foot-col__sublabel"><?php esc_html_e('Risorse', 'saltelli'); ?></div>
                    <nav class="sl-foot-nav-list" aria-label="<?php esc_attr_e('Risorse', 'saltelli'); ?>">
                        <a class="sl-foot-link" href="<?php echo esc_url(home_url('/blog/')); ?>"><?php esc_html_e('Editoriale / Blog', 'saltelli'); ?></a>
                        <a class="sl-foot-link" href="<?php echo esc_url(home_url('/glossario-legale/')); ?>"><?php esc_html_e('Glossario legale', 'saltelli'); ?></a>
                    </nav>

                    <hr class="sl-foot-hairline" aria-hidden="true">

                    <div class="sl-mono sl-foot-col__sublabel"><?php esc_html_e('Servizio', 'saltelli'); ?></div>
                    <nav class="sl-foot-nav-list" aria-label="<?php esc_attr_e('Servizio', 'saltelli'); ?>">
                        <a class="sl-foot-link" href="<?php echo esc_url(home_url('/contatti/')); ?>"><?php esc_html_e('Contatti', 'saltelli'); ?></a>
                    </nav>
                </div>

                <?php /* COL 4 — INFO ISTITUZIONALI + AI-FRIENDLY */ ?>
                <div class="sl-foot-col sl-foot-col--info">
                    <div class="sl-mono sl-foot-col__label"><?php esc_html_e('Studio professionale', 'saltelli'); ?></div>

                    <div class="sl-mono sl-foot-info-block">
                        <span><?php esc_html_e('Iscritto Ordine Avvocati Napoli', 'saltelli'); ?></span>
                        <span><?php esc_html_e('P.IVA', 'saltelli'); ?> <?php echo esc_html($ftr_piva); ?></span>
                        <a class="sl-foot-link sl-foot-info-block__pec" href="mailto:<?php echo esc_attr($ftr_pec); ?>">
                            <?php esc_html_e('PEC', 'saltelli'); ?> <?php echo esc_html($ftr_pec); ?>
                        </a>
                    </div>

                    <hr class="sl-foot-hairline" aria-hidden="true">

                    <div class="sl-mono sl-foot-col__sublabel"><?php esc_html_e('AI-friendly', 'saltelli'); ?></div>
                    <div class="sl-foot-ai-list sl-mono">
                        <a class="sl-foot-link" href="<?php echo esc_url(home_url('/llms.txt')); ?>">/llms.txt</a>
                        <a class="sl-foot-link" href="<?php echo esc_url(home_url('/sitemap.xml')); ?>">/sitemap.xml</a>
                        <a class="sl-foot-link" href="<?php echo esc_url(home_url('/robots.txt')); ?>">/robots.txt</a>
                        <span class="sl-foot-ai-list__cite"><?php esc_html_e('Citazione consentita', 'saltelli'); ?></span>
                    </div>

                    <hr class="sl-foot-hairline" aria-hidden="true">

                    <div class="sl-mono sl-foot-badge">
                        <?php esc_html_e('Verificabile in studio', 'saltelli'); ?>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <?php /* ═══ FASCIA 3 · NEWSLETTER EDITORIALE — Brevo legacy form ═══ */ ?>
    <section class="sl-foot-newsletter-wrap" aria-labelledby="newsletter-h">
        <div class="sl-foot-newsletter-inner">
            <div class="sl-foot-newsletter">

                <div class="sl-foot-newsletter__lede">
                    <div class="sl-mono sl-foot-newsletter__eyebrow"><?php esc_html_e('§ Newsletter · Dal 2026', 'saltelli'); ?></div>
                    <h3 class="sl-foot-newsletter__h" id="newsletter-h">
                        <?php esc_html_e("L'editoriale del giovedì.", 'saltelli'); ?>
                    </h3>
                    <p class="sl-foot-newsletter__p">
                        <?php esc_html_e('Una mail al mese. Sentenze recenti, novità giurisprudenziali, case study reali dello Studio. Mai promozioni, mai spam.', 'saltelli'); ?>
                    </p>
                    <p class="sl-mono sl-foot-newsletter__trust">
                        <?php esc_html_e('Una al mese · No spam · Cancellazione 1 click', 'saltelli'); ?>
                    </p>
                </div>

                <div class="sl-foot-newsletter__form-wrap">
                    <form class="sl-foot-newsletter__form form-newsletter"
                          id="sib_signup_form_1"
                          action="https://link.studiolegalesaltelli.it/api/v3/contacts"
                          method="POST"
                          data-sl-newsletter
                          novalidate>
                        <div class="sl-foot-fields">
                            <label class="sl-foot-newsletter__field" for="newsletter-firstname">
                                <span class="sl-mono sl-foot-newsletter__field-label"><?php esc_html_e('Nome', 'saltelli'); ?></span>
                                <input id="newsletter-firstname"
                                       type="text"
                                       name="FIRSTNAME"
                                       class="sl-newsletter__input"
                                       placeholder="<?php esc_attr_e('Il tuo nome', 'saltelli'); ?>"
                                       autocomplete="given-name"
                                       required>
                            </label>
                            <label class="sl-foot-newsletter__field" for="newsletter-email">
                                <span class="sl-mono sl-foot-newsletter__field-label"><?php esc_html_e('Email', 'saltelli'); ?></span>
                                <input id="newsletter-email"
                                       type="email"
                                       name="email"
                                       class="sl-newsletter__input"
                                       placeholder="<?php esc_attr_e('indirizzo@email.it', 'saltelli'); ?>"
                                       autocomplete="email"
                                       required>
                            </label>
                        </div>

                        <label class="sl-foot-newsletter__gdpr" for="newsletter-terms">
                            <input id="newsletter-terms"
                                   type="checkbox"
                                   name="terms"
                                   class="sl-newsletter__check"
                                   required>
                            <span>
                                <?php
                                printf(
                                    /* translators: %s = privacy policy link */
                                    esc_html__('Accetto la %s e voglio ricevere l\'editoriale.', 'saltelli'),
                                    '<a href="' . esc_url(home_url('/privacy/')) . '" class="sl-foot-link sl-foot-newsletter__gdpr-link">' . esc_html__('privacy policy', 'saltelli') . '</a>'
                                );
                                ?>
                            </span>
                        </label>

                        <button type="submit" class="sl-btn sl-btn--primary sl-foot-newsletter__submit">
                            <span class="sl-foot-newsletter__submit-label"><?php esc_html_e("Iscriviti all'editoriale", 'saltelli'); ?></span>
                            <span class="arrow" aria-hidden="true">→</span>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </section>

    <?php /* ═══ FASCIA 4 · BOTTOM LEGAL ═══ */ ?>
    <section class="sl-foot-bottom-wrap">
        <div class="sl-foot-bottom-inner">
            <div class="sl-foot-bottom sl-mono">
                <div class="sl-foot-bottom__copy">
                    © <?php echo esc_html(date('Y')); ?> Studio Legale Emiliano Saltelli &amp; Partners
                </div>
                <nav class="sl-foot-bottom__legal" aria-label="<?php esc_attr_e('Legale', 'saltelli'); ?>">
                    <a class="sl-foot-link" href="<?php echo esc_url(home_url('/privacy/')); ?>"><?php esc_html_e('Privacy', 'saltelli'); ?></a>
                    <a class="sl-foot-link" href="<?php echo esc_url(home_url('/cookie/')); ?>"><?php esc_html_e('Cookie', 'saltelli'); ?></a>
                    <a class="sl-foot-link" href="<?php echo esc_url(home_url('/note-legali/')); ?>"><?php esc_html_e('Note legali', 'saltelli'); ?></a>
                </nav>
                <div class="sl-foot-bottom__credit">
                    <?php
                    printf(
                        /* translators: %s = Adsolut link */
                        esc_html__('Sito by %s', 'saltelli'),
                        '<a href="https://adsolut.it" class="sl-foot-link" rel="noopener" target="_blank">Adsolut</a>'
                    );
                    ?>
                </div>
            </div>
        </div>
    </section>
</footer>

<?php wp_footer(); ?>
</body>
</html>
