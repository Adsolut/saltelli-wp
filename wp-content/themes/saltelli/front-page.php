<?php
/**
 * Template: Front page (homepage).
 *
 * @package Saltelli
 */
get_header();
?>

<!-- TODO Style & Animation agent: hero section
     - H1 unico (es. "Avvocati a Napoli — diritto tributario, lavoro, famiglia")
     - sottotitolo / answer capsule (40-60 parole)
     - CTA primaria + telefono click-to-call
     - cinematic visual ma lean (no slideshow)
-->
<section class="home-hero">
    <div class="container">
        <h1><?php bloginfo('name'); ?></h1>
        <!-- TODO: copy header da Elena -->
    </div>
</section>

<!-- TODO Style & Animation agent: section "Aree di pratica" (highlight 3 tier-1 + griglia 16 tier-2) -->
<section class="home-aree" aria-labelledby="home-aree-h">
    <div class="container">
        <h2 id="home-aree-h"><?php esc_html_e('Aree di pratica', 'saltelli'); ?></h2>
        <!-- TODO Style & Animation agent: rendering loop competenza (con flag tier-1) -->
    </div>
</section>

<!-- TODO Style & Animation agent: section "Il team" (4 avvocati) -->
<section class="home-team" aria-labelledby="home-team-h">
    <div class="container">
        <h2 id="home-team-h"><?php esc_html_e('Il team', 'saltelli'); ?></h2>
        <!-- TODO Style & Animation agent: grid 2x2 con foto avvocato + ruolo -->
    </div>
</section>

<!-- TODO Style & Animation agent: section "Approccio" (perché Saltelli vs altri) -->
<section class="home-approccio">
    <div class="container">
        <h2><?php esc_html_e('Il nostro approccio', 'saltelli'); ?></h2>
        <!-- TODO: copy da Elena -->
    </div>
</section>

<!-- TODO Style & Animation agent: section "Blog highlights" (ultimi 3 articoli) -->
<section class="home-blog">
    <div class="container">
        <h2><?php esc_html_e('Approfondimenti', 'saltelli'); ?></h2>
        <!-- TODO Style & Animation agent: WP_Query 3 ultimi post -->
    </div>
</section>

<!-- TODO Style & Animation agent: section CTA finale (mappa + form contatti compatto) -->
<section class="home-cta">
    <div class="container">
        <h2><?php esc_html_e('Prenota una consulenza', 'saltelli'); ?></h2>
        <!-- TODO: form contatti reale -->
    </div>
</section>

<?php
get_footer();
