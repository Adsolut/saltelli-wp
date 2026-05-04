# PROMPT v0.33.0 — Recupero Pagine Informative + Archive Editorial Refactor

> **Per Claude Code in nuova sessione.** Apri `saltelli-wp/`, leggi questo file, eseguilo. Tempo: ~90 min sequential / ~50 min se parallelizzato in 3 agenti.
> **PRECEDENZA:** v0.32.0 drop-cap + FAQ identity completato.

---

## 🎯 Tu sei

L'**Agente Pagine Informative + Archive Refactor**. Audit Duccio post-v0.32.0 ha rilevato 5 pagine "secondarie" che non hanno design Sessione 2 e renderizzano "anonimo" rispetto al brand editorial:

```
🔴 GAP REALI v0.33.0:

1. /avvocati/ archive (4 lawyer overview)
   archive-avvocato.php attuale: design "team grid" minimale,
   manca hero asimmetrico + drop-cap + § principi
   
2. /guide-gratuite/ — page WP su .sl-page__prose generic
3. /come-lavoriamo/ — idem
4. /prima-consulenza/ — idem  
5. /lavora-con-noi/ — idem

Tutte queste 4 pagine usano page.php template generico con .sl-page__prose:
markup body OK ma drop-cap NON visibile + zero identità visiva editoriale
rispetto a /casi/, /chi-siamo/, /costi/ che hanno template Sessione 2 dedicati.

PROBLEMA: drop-cap CSS rule v0.32.0 esistono ma SU WRAPPER GENERIC
.sl-page__prose la specificity è troppo bassa, qualche override generic la sovrascrive.
```

**STRATEGIA:** Quick refactor riusando pattern Sessione 2 esistenti (no nuovi JSX da Design). Tempo controllato 90 min.

---

## 📚 Letture obbligatorie

```
.claude/knowledge/design/sessione-2/saltelli-s2-attorney-single.jsx (riferimento hero asym)
.claude/knowledge/design/sessione-2/saltelli-s2-chi-siamo.jsx (riferimento drop-cap + principi)
.claude/knowledge/design/sessione-2/saltelli-s2-costi.jsx (riferimento info page hero + CTA)

CLAUDE.md (hard constraints + Design→Code handoff golden rule)
.claude/knowledge/design/sessione-1/tokens.css (locked)

wp-content/themes/saltelli/
  ├── archive-avvocato.php (87 righe — refactor)
  ├── page.php (66KB — aggiungi 4 blocchi is_page custom)
  └── assets/css/sections.css (target CSS scope refactor)
```

---

## 🔒 Hard rules

| Rule | Decisione |
|---|---|
| **Riusa pattern Sessione 2 esistenti** — NO nuovo design Sessione 3 | Scelta operativa Duccio |
| **Class scope** `.sl-info-page__*` per info shared, `.sl-attorney-archive__*` per /avvocati/ | Naming dedicato |
| **Drop-cap !important strategico** dove la specificity non basta | Fix gap visibility |
| **NESSUNA modifica tokens.css** valori | Locked |
| **NON sovrascrivere** `_thumbnail_id` Emiliano + `bio_estesa` Step D + `post_content` CPT | Content protetto |
| **CSS scope marker** `/* === v0.33.0 [task] === */` per ogni rule nuova | Audit trail |
| Cache flush + smoke test 5 URL chiave dopo OGNI task | Lezione |
| Bump version + git commit dopo OGNI task major | Atomicity |

---

## 🗺 Strategia esecuzione: SEQUENZIALE single agent

**Decisione orchestrator**: SEQUENZIALE per 3 ragioni:
1. Task 1 (template info shared) → Task 2 (apply ai 4 page) hanno dipendenza diretta
2. Task 3 (/avvocati/ archive) tocca file diverso, parallelizzabile MA tempo extra coordinazione tmux > savings
3. Task 4 (drop-cap forced) è cross-template, deve venire DOPO Task 1+3

```
Task 1 → CSS template "info-page" shared           ~25 min
Task 2 → page.php blocchi 4 page (apply template)  ~15 min
Task 3 → /avvocati/ archive refactor               ~20 min
Task 4 → Drop-cap !important + visibility fix      ~10 min
Task 5 → Bump + smoke + deploy                       ~10 min
```

---

## TASK 1 — CSS template "info-page" shared (~25 min)

### 1.1 — Pattern condiviso

In `sections.css` aggiungi blocco scoped marker `/* === v0.33.0 INFO-PAGE === */`:

```css
/* ═══════════════════════════════════════════════════════════════
   v0.33.0 — Template "info-page" shared
   For: /guide-gratuite/, /come-lavoriamo/, /prima-consulenza/, /lavora-con-noi/
   Pattern: hero asimmetrico 8fr/4fr + drop-cap forte + body editorial + CTA
   ═══════════════════════════════════════════════════════════════ */

/* Hero asimmetrico */
.sl-info-page {
    max-width: 1440px;
    margin-inline: auto;
    padding: clamp(96px, 10vw, 120px) clamp(24px, 5vw, 96px) clamp(64px, 8vw, 80px);
}

.sl-info-page__hero {
    display: grid;
    gap: 64px;
    margin-bottom: 96px;
}

@media (min-width: 1024px) {
    .sl-info-page__hero {
        grid-template-columns: 8fr 4fr;
        align-items: end;
    }
}

.sl-info-page__hero-text {
    display: grid;
    gap: 24px;
}

.sl-info-page__eyebrow {
    margin-bottom: 8px;
}

.sl-info-page__h1 {
    font-family: var(--font-display);
    font-size: clamp(48px, 7vw, 96px);
    line-height: 0.98;
    letter-spacing: -0.025em;
    font-weight: 400;
    color: var(--primary);
    margin: 0;
    max-width: 14ch;
}

.sl-info-page__h1 em {
    font-style: italic;
    color: var(--text-muted);
}

.sl-info-page__lede {
    font-family: var(--font-display);
    font-size: clamp(20px, 1.8vw, 24px);
    font-style: italic;
    line-height: 1.5;
    color: var(--text);
    max-width: 56ch;
    margin: 0;
}

/* Trust card DX (4fr) */
.sl-info-page__hero-aside {
    border: 1px solid var(--border);
    padding: 32px;
    background: var(--surface);
    align-self: end;
}

.sl-info-page__hero-aside-eyebrow {
    margin-bottom: 16px;
}

.sl-info-page__hero-aside h3 {
    font-family: var(--font-display);
    font-size: 22px;
    line-height: 1.2;
    font-weight: 400;
    color: var(--primary);
    margin: 0 0 16px;
}

.sl-info-page__hero-aside p {
    font-size: 14px;
    line-height: 1.6;
    color: var(--text);
    margin: 0 0 20px;
}

/* Body editorial */
.sl-info-page__body {
    max-width: 720px;
    margin: 0 auto;
}

.sl-info-page__body > p {
    font-size: 19px;
    line-height: 1.75;
    color: var(--text);
    margin: 0 0 24px;
    max-width: 60ch;
}

/* DROP-CAP FORTE prima parola — !important per override eventuali generic */
.sl-info-page__body > p:first-of-type::first-letter {
    font-family: var(--font-display) !important;
    font-size: 84px !important;
    line-height: 0.85 !important;
    float: left !important;
    margin: 8px 16px 0 0 !important;
    color: var(--primary) !important;
    font-weight: 400 !important;
}

/* H2 cluster con respiro */
.sl-info-page__body h2 {
    font-family: var(--font-display);
    font-size: clamp(28px, 3vw, 40px);
    line-height: 1.15;
    letter-spacing: -0.015em;
    font-weight: 400;
    color: var(--primary);
    margin: 80px 0 32px;
    max-width: 24ch;
    padding-top: 24px;
    border-top: 1px solid var(--accent);
}

.sl-info-page__body h2:first-child {
    margin-top: 0;
    border-top: 0;
    padding-top: 0;
}

.sl-info-page__body h3 {
    font-family: var(--font-display);
    font-size: clamp(22px, 2vw, 28px);
    line-height: 1.2;
    font-weight: 400;
    color: var(--primary);
    margin: 56px 0 24px;
}

.sl-info-page__body ul,
.sl-info-page__body ol {
    margin: 24px 0 32px;
    padding-left: 24px;
    list-style: none;
}

.sl-info-page__body ul li,
.sl-info-page__body ol li {
    font-size: 18px;
    line-height: 1.65;
    color: var(--text);
    margin-bottom: 12px;
    padding-left: 24px;
    position: relative;
}

.sl-info-page__body ul li::before {
    content: "—";
    position: absolute;
    left: 0;
    color: var(--accent);
    font-family: var(--font-mono);
}

.sl-info-page__body ol {
    counter-reset: info-list;
}

.sl-info-page__body ol li {
    counter-increment: info-list;
}

.sl-info-page__body ol li::before {
    content: counter(info-list, decimal-leading-zero);
    position: absolute;
    left: 0;
    color: var(--accent);
    font-family: var(--font-mono);
    font-size: 11px;
    letter-spacing: 0.06em;
    top: 4px;
}

.sl-info-page__body blockquote {
    margin: 48px 0;
    padding: 24px 0 24px 32px;
    border-left: 1px solid var(--accent);
    font-family: var(--font-display);
    font-style: italic;
    font-size: 22px;
    line-height: 1.5;
    color: var(--primary);
}

/* CTA finale */
.sl-info-page__cta-final {
    max-width: 1440px;
    margin: clamp(96px, 12vw, 160px) auto 0;
    padding: clamp(64px, 8vw, 96px) clamp(24px, 5vw, 96px);
    background: var(--primary);
    color: var(--background);
    text-align: center;
}

.sl-info-page__cta-final-eyebrow {
    color: var(--accent);
    margin-bottom: 24px;
}

.sl-info-page__cta-final h2 {
    font-family: var(--font-display);
    font-size: clamp(36px, 5vw, 64px);
    font-style: italic;
    font-weight: 400;
    line-height: 1.1;
    color: var(--background);
    margin: 0 0 24px;
    max-width: 20ch;
    margin-inline: auto;
}

.sl-info-page__cta-final p {
    font-size: 18px;
    line-height: 1.6;
    color: rgba(250, 250, 248, 0.85);
    margin: 0 0 40px;
    max-width: 50ch;
    margin-inline: auto;
}

.sl-info-page__cta-final .sl-btn--primary {
    background: var(--accent);
    color: var(--primary);
    border-color: var(--accent);
}

@media (hover: hover) {
    .sl-info-page__cta-final .sl-btn--primary:hover {
        background: var(--background);
        color: var(--primary);
    }
}

.sl-info-page__cta-final-trust {
    margin-top: 24px;
    color: rgba(250, 250, 248, 0.55);
}
```

### 1.2 — Verify

```bash
docker compose run --rm wpcli cache flush
curl -s http://localhost:8080/wp-content/themes/saltelli/assets/css/sections.css -m 5 | grep -c 'sl-info-page'
```

Atteso: ≥ 30 hit.

---

## TASK 2 — page.php blocchi 4 info pages (~15 min)

### 2.1 — Aggiungi blocchi `is_page()` in page.php

In `page.php` cerca l'ultimo `<?php elseif (is_page('costi')) :` o equivalente, e aggiungi PRIMA del default `else` (o `endif`):

```php
<?php elseif (is_page(['guide-gratuite', 'come-lavoriamo', 'prima-consulenza', 'lavora-con-noi'])) :
    
    // Hero info per ogni pagina
    $info_hero_map = [
        'guide-gratuite' => [
            'eyebrow' => '§ Risorse · Guide gratuite',
            'h1_pre' => 'Guide',
            'h1_em' => 'gratuite.',
            'lede' => 'Schede sintetiche scaricabili in PDF — primo orientamento giuridico prima di una consulenza professionale.',
            'aside_eyebrow' => '§ Disponibili',
            'aside_h3' => '12 guide editoriali',
            'aside_p' => 'Curate dal team Saltelli & Partners. Aggiornate con la giurisprudenza più recente.',
            'aside_cta' => 'Sfoglia tutte →',
            'aside_cta_url' => '#guide-list',
        ],
        'come-lavoriamo' => [
            'eyebrow' => '§ Studio · Metodo',
            'h1_pre' => 'Come',
            'h1_em' => 'lavoriamo.',
            'lede' => 'Ascolto prima, carte dopo. Un metodo costruito su vent\'anni di pratica accanto a famiglie e imprese di Napoli.',
            'aside_eyebrow' => '§ Tre principi',
            'aside_h3' => 'Trasparenza, ascolto, profondità',
            'aside_p' => 'Tre principi non negoziabili. Stessa cura per pratica privata e mandato corporate.',
            'aside_cta' => 'Prenota un incontro →',
            'aside_cta_url' => '/contatti/',
        ],
        'prima-consulenza' => [
            'eyebrow' => '§ Servizio · Prima consulenza',
            'h1_pre' => 'Prima',
            'h1_em' => 'consulenza.',
            'lede' => 'Trenta minuti, conoscitivi e gratuiti, per ascoltare la pratica e capire se ha solidi presupposti.',
            'aside_eyebrow' => '§ Modalità',
            'aside_h3' => 'In studio, online o telefonica',
            'aside_p' => 'Trenta minuti senza obblighi né costi nascosti. Riservatezza assoluta.',
            'aside_cta' => 'Prenota un incontro →',
            'aside_cta_url' => '/contatti/',
        ],
        'lavora-con-noi' => [
            'eyebrow' => '§ Studio · Carriera',
            'h1_pre' => 'Lavora',
            'h1_em' => 'con noi.',
            'lede' => 'Cerchiamo praticanti, associate e of-counsel con percorso editoriale: ricerca approfondita, scrittura accurata, ascolto del cliente.',
            'aside_eyebrow' => '§ Posizioni aperte',
            'aside_h3' => 'Praticanti & Associate',
            'aside_p' => 'Diritto tributario, lavoro, famiglia. Curriculum + lettera motivazionale.',
            'aside_cta' => 'Invia candidatura →',
            'aside_cta_url' => 'mailto:info@studiolegalesaltelli.it',
        ],
    ];
    
    $current_slug = get_post_field('post_name', get_the_ID());
    $info = $info_hero_map[$current_slug] ?? null;
    
    if ($info):
?>
    <article class="sl-info-page">
        
        <header class="sl-info-page__hero">
            <div class="sl-info-page__hero-text">
                <?php saltelli_render_breadcrumb('page'); ?>
                <div class="sl-mono sl-info-page__eyebrow"><?php echo esc_html($info['eyebrow']); ?></div>
                <h1 class="sl-info-page__h1" data-split-reveal>
                    <?php echo esc_html($info['h1_pre']); ?> <em><?php echo esc_html($info['h1_em']); ?></em>
                </h1>
                <p class="sl-info-page__lede"><?php echo esc_html($info['lede']); ?></p>
            </div>
            
            <aside class="sl-info-page__hero-aside">
                <div class="sl-mono sl-info-page__hero-aside-eyebrow"><?php echo esc_html($info['aside_eyebrow']); ?></div>
                <h3><?php echo esc_html($info['aside_h3']); ?></h3>
                <p><?php echo esc_html($info['aside_p']); ?></p>
                <a href="<?php echo esc_url($info['aside_cta_url']); ?>" class="sl-link sl-link--accent">
                    <?php echo esc_html($info['aside_cta']); ?>
                </a>
            </aside>
        </header>
        
        <div class="sl-info-page__body">
            <?php the_content(); ?>
        </div>
        
        <section class="sl-info-page__cta-final">
            <div class="sl-mono sl-info-page__cta-final-eyebrow">§ Pronto?</div>
            <h2>La prima consulenza è sempre gratuita.</h2>
            <p>Trenta minuti per ascoltarci, valutare insieme, decidere se procedere.</p>
            <a href="/contatti/" class="sl-btn sl-btn--primary">Prenota un incontro →</a>
            <p class="sl-mono sl-info-page__cta-final-trust">Risposta entro 24 ore · Riservatezza assoluta</p>
        </section>
        
    </article>
<?php
    endif;
?>
```

### 2.2 — Smoke verify

```bash
for U in /guide-gratuite/ /come-lavoriamo/ /prima-consulenza/ /lavora-con-noi/; do
    HTML=$(curl -s "http://localhost:8080$U?_=v33t2" -m 8)
    INFO=$(echo "$HTML" | grep -c 'sl-info-page\b')
    HERO=$(echo "$HTML" | grep -c 'sl-info-page__hero')
    BODY=$(echo "$HTML" | grep -c 'sl-info-page__body')
    CTA=$(echo "$HTML" | grep -c 'sl-info-page__cta-final')
    H1=$(echo "$HTML" | grep -oE '<h1[^>]*>[^<]+' | head -1 | sed 's/<h1[^>]*>//' | head -c 50)
    printf "  %-25s sl-info-page:%s hero:%s body:%s cta:%s · H1: %s\n" "$U" "$INFO" "$HERO" "$BODY" "$CTA" "$H1"
done
```

Atteso: tutti = 1 per ogni class.

---

## TASK 3 — /avvocati/ archive refactor (~20 min)

### 3.1 — Refactor archive-avvocato.php

`archive-avvocato.php` attuale (87 righe) ha già struttura buona ma manca pattern Sessione 2:
- ✓ HAS hero h1 split-reveal "Quattro professionisti."
- ✓ HAS lede "Un atelier di quattro avvocati"  
- ✗ MANCA hero asimmetrico 8fr/4fr (atteso "trust card" DX)
- ✗ MANCA drop-cap su lede
- ✗ MANCA sezione "Come lavoriamo" 3 principi (riusa pattern chi-siamo)
- ✗ MANCA CTA finale strong

Sostituire archive-avvocato.php con (mantenendo logica $avvocati esistente):

```php
<?php
/**
 * Template: Archive CPT avvocato.
 * Sessione 2 enriched layout v0.33.0.
 * Hero asimmetrico 8fr/4fr + drop-cap + 4 lawyer grid + § principi + CTA.
 *
 * @package Saltelli
 */
get_header();

$avvocati = get_posts([
    'post_type'   => 'avvocato',
    'numberposts' => -1,
    'orderby'     => ['menu_order' => 'ASC', 'date' => 'ASC'],
]);
$layout_team = saltelli_team_grid_layout();
?>

<article class="sl-attorney-archive">
    
    <!-- HERO 8fr/4fr -->
    <header class="sl-attorney-archive__hero">
        <div class="sl-attorney-archive__hero-text">
            <?php saltelli_render_breadcrumb('avvocato_archive'); ?>
            <div class="sl-mono sl-attorney-archive__eyebrow">§ 04 Avvocati · Saltelli &amp; Partners</div>
            <h1 class="sl-attorney-archive__h1" data-split-reveal>
                <?php
                $sl_arch_av_title = esc_html__('Quattro', 'saltelli') . '<br><em>' . esc_html__('professionisti.', 'saltelli') . '</em>';
                echo wp_kses(saltelli_split_h1_words($sl_arch_av_title), [
                    'span' => ['class' => true, 'data-i' => true],
                    'em'   => [],
                    'br'   => [],
                ]);
                ?>
            </h1>
            <p class="sl-attorney-archive__lede">
                <?php esc_html_e('Un atelier di quattro avvocati a Chiaia. Ogni cliente è una storia, e ogni storia merita il tempo di essere capita.', 'saltelli'); ?>
            </p>
        </div>
        
        <aside class="sl-attorney-archive__hero-aside">
            <div class="sl-mono sl-attorney-archive__hero-aside-eyebrow">§ Studio storico</div>
            <h3><?php esc_html_e('Dal 1999, Chiaia.', 'saltelli'); ?></h3>
            <p><?php esc_html_e('Vent\'anni di pratica accanto a famiglie e imprese di Napoli. Diciannove aree di pratica, di cui tre presidiate in profondità.', 'saltelli'); ?></p>
            <a href="/chi-siamo/" class="sl-link sl-link--accent">
                <?php esc_html_e('La nostra storia →', 'saltelli'); ?>
            </a>
        </aside>
    </header>

    <!-- LAWYER GRID 4 -->
    <?php if (!empty($avvocati)) : ?>
        <section class="sl-attorney-archive__grid-wrapper">
            <div class="sl-attorney-archive__grid">
                <?php foreach ($avvocati as $i => $av) :
                    $layout = $layout_team[$i] ?? ['col' => 1, 'span' => 12, 'offset' => 0];
                    $ruolo  = (string) saltelli_field('ruolo_breve', $av->ID, '');
                    $specs  = saltelli_get_attorney_specializations($av->ID);
                    $foto   = saltelli_field('foto_ritratto', $av->ID);
                    ?>
                    <article class="sl-attorney-archive__card"
                             style="--sl-col:<?php echo (int) $layout['col']; ?>; --sl-span:<?php echo (int) $layout['span']; ?>; --sl-offset:<?php echo (int) $layout['offset']; ?>px;">
                        <a class="sl-attorney-archive__portrait" href="<?php echo esc_url(get_permalink($av)); ?>" aria-label="<?php echo esc_attr(get_the_title($av)); ?>">
                            <?php
                            if (has_post_thumbnail($av->ID)) {
                                echo get_the_post_thumbnail($av->ID, 'saltelli-attorney-portrait', [
                                    'loading'  => 'lazy',
                                    'decoding' => 'async',
                                    'alt'      => esc_attr(get_the_title($av) . ($ruolo ? ' · ' . $ruolo : '')),
                                ]);
                            } elseif (is_array($foto) && !empty($foto['url'])) {
                                echo '<img src="' . esc_url($foto['url']) . '" alt="' . esc_attr($foto['alt'] ?: get_the_title($av)) . '" loading="lazy" decoding="async" width="600" height="800">';
                            } else {
                                echo '<span class="sl-attorney-archive__placeholder" aria-hidden="true"><span class="sl-mono">' . esc_html__('Ritratto · 3:4', 'saltelli') . '</span></span>';
                            }
                            ?>
                        </a>
                        <?php if ($ruolo) : ?>
                            <div class="sl-mono sl-attorney-archive__role"><?php echo esc_html($ruolo); ?></div>
                        <?php endif; ?>
                        <h2 class="sl-attorney-archive__name">
                            <a href="<?php echo esc_url(get_permalink($av)); ?>"><?php echo esc_html(get_the_title($av)); ?></a>
                        </h2>
                        <?php if (!empty($specs)) : ?>
                            <ul class="sl-attorney-archive__specs">
                                <?php foreach ($specs as $s) : ?>
                                    <li class="sl-tag"><?php echo esc_html($s); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <!-- § COME LAVORIAMO 3 PRINCIPI (riusa pattern chi-siamo) -->
    <section class="sl-attorney-archive__principi">
        <header class="sl-attorney-archive__principi-head">
            <div class="sl-mono">§ Come lavoriamo</div>
            <h2><?php esc_html_e('Tre principi.', 'saltelli'); ?></h2>
        </header>
        <ol class="sl-attorney-archive__principi-list">
            <li class="sl-attorney-archive__principi-item">
                <div class="sl-mono sl-attorney-archive__principi-num">01</div>
                <h3>Ascoltiamo prima</h3>
                <p>Trenta minuti di prima consulenza gratuita servono a capire la pratica, le aspettative, gli ostacoli reali. Solo dopo proponiamo una strategia.</p>
            </li>
            <li class="sl-attorney-archive__principi-item">
                <div class="sl-mono sl-attorney-archive__principi-num">02</div>
                <h3>Lavoriamo in atelier</h3>
                <p>Quattro avvocati in una sede storica a Chiaia. Conosciamo i nomi dei clienti, il loro lavoro, la loro storia. Ogni pratica è seguita personalmente.</p>
            </li>
            <li class="sl-attorney-archive__principi-item">
                <div class="sl-mono sl-attorney-archive__principi-num">03</div>
                <h3>Diciamo la verità</h3>
                <p>Anche quando significa rifiutare un mandato perché la causa non è solida. Anche quando significa proporre una mediazione invece del processo.</p>
            </li>
        </ol>
    </section>

    <!-- CTA FINALE -->
    <section class="sl-attorney-archive__cta-final">
        <div class="sl-mono sl-attorney-archive__cta-final-eyebrow">§ Vorresti raccontarci la tua pratica?</div>
        <h2>Prenota un incontro<br><em>con un nostro avvocato.</em></h2>
        <p>Trenta minuti gratuiti, in studio o online. Risposta entro 24 ore.</p>
        <a href="/contatti/" class="sl-btn sl-btn--primary">Prenota un incontro →</a>
    </section>
    
</article>

<?php
get_footer();
```

### 3.2 — CSS scope `.sl-attorney-archive__*`

```css
/* ═══════════════════════════════════════════════════════════════
   v0.33.0 — /avvocati/ archive refactor
   Pattern: hero asym 8fr/4fr + 4-card grid + § principi + CTA
   ═══════════════════════════════════════════════════════════════ */

.sl-attorney-archive {
    max-width: 1440px;
    margin-inline: auto;
    padding: clamp(96px, 10vw, 120px) clamp(24px, 5vw, 96px) 0;
}

.sl-attorney-archive__hero {
    display: grid;
    gap: 64px;
    margin-bottom: 96px;
}

@media (min-width: 1024px) {
    .sl-attorney-archive__hero {
        grid-template-columns: 8fr 4fr;
        align-items: end;
    }
}

.sl-attorney-archive__hero-text {
    display: grid;
    gap: 24px;
}

.sl-attorney-archive__eyebrow {
    margin-bottom: 8px;
}

.sl-attorney-archive__h1 {
    font-family: var(--font-display);
    font-size: clamp(56px, 7vw, 96px);
    line-height: 0.98;
    letter-spacing: -0.025em;
    font-weight: 400;
    color: var(--primary);
    margin: 0;
    max-width: 16ch;
}

.sl-attorney-archive__h1 em {
    font-style: italic;
    color: var(--text-muted);
}

.sl-attorney-archive__lede {
    font-family: var(--font-display);
    font-size: clamp(20px, 1.8vw, 24px);
    font-style: italic;
    line-height: 1.5;
    color: var(--text);
    max-width: 56ch;
    margin: 0;
}

/* Drop-cap "U" su lede */
.sl-attorney-archive__lede::first-letter {
    font-family: var(--font-display) !important;
    font-size: 84px !important;
    line-height: 0.85 !important;
    float: left !important;
    margin: 8px 16px 0 0 !important;
    color: var(--primary) !important;
    font-style: italic !important;
}

@media (max-width: 767px) {
    .sl-attorney-archive__lede::first-letter {
        font-size: 60px !important;
        margin: 4px 12px 0 0 !important;
    }
}

/* Trust aside */
.sl-attorney-archive__hero-aside {
    border: 1px solid var(--border);
    padding: 32px;
    background: var(--surface);
    align-self: end;
}

.sl-attorney-archive__hero-aside h3 {
    font-family: var(--font-display);
    font-size: 22px;
    line-height: 1.2;
    font-weight: 400;
    color: var(--primary);
    margin: 16px 0 16px;
}

.sl-attorney-archive__hero-aside p {
    font-size: 14px;
    line-height: 1.6;
    color: var(--text);
    margin: 0 0 20px;
}

/* Lawyer grid 4-col */
.sl-attorney-archive__grid-wrapper {
    margin-bottom: 128px;
}

.sl-attorney-archive__grid {
    display: grid;
    gap: 32px;
}

@media (min-width: 768px) {
    .sl-attorney-archive__grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1024px) {
    .sl-attorney-archive__grid {
        grid-template-columns: repeat(4, 1fr);
        gap: 48px;
    }
}

.sl-attorney-archive__card {
    display: grid;
    gap: 16px;
}

.sl-attorney-archive__portrait {
    aspect-ratio: 3 / 4;
    overflow: hidden;
    background: var(--surface);
    transition: filter 600ms var(--ease-editorial, cubic-bezier(0.25, 1, 0.5, 1));
    filter: grayscale(1) contrast(1.05);
    text-decoration: none;
    display: block;
}

@media (hover: hover) {
    .sl-attorney-archive__portrait:hover {
        filter: grayscale(0) contrast(1);
    }
}

.sl-attorney-archive__portrait img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center top;
}

.sl-attorney-archive__placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, var(--surface) 0%, var(--text-muted) 100%);
    color: rgba(255, 255, 255, 0.5);
}

.sl-attorney-archive__role {
    color: var(--text-muted);
    margin-top: 8px;
}

.sl-attorney-archive__name {
    font-family: var(--font-display);
    font-size: 24px;
    line-height: 1.2;
    font-weight: 400;
    margin: 0;
    color: var(--primary);
}

.sl-attorney-archive__name a {
    color: inherit;
    text-decoration: none;
    transition: color var(--dur-fast, 200ms) var(--ease-editorial);
}

.sl-attorney-archive__name a:hover {
    color: var(--accent);
}

.sl-attorney-archive__specs {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    list-style: none;
    padding: 0;
    margin: 0;
}

.sl-attorney-archive__specs .sl-tag {
    font-family: var(--font-mono);
    font-size: 10px;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--text-muted);
    border: 1px solid var(--border);
    padding: 4px 10px;
}

/* § Principi (riusa pattern chi-siamo) */
.sl-attorney-archive__principi {
    background: var(--surface);
    margin: 0 calc(-1 * clamp(24px, 5vw, 96px));
    padding: clamp(64px, 8vw, 128px) clamp(24px, 5vw, 96px);
    margin-bottom: 96px;
}

.sl-attorney-archive__principi-head {
    margin-bottom: 64px;
    max-width: 1248px;
    margin-inline: auto;
}

.sl-attorney-archive__principi-head h2 {
    font-family: var(--font-display);
    font-size: clamp(40px, 5vw, 64px);
    line-height: 1.1;
    letter-spacing: -0.02em;
    font-weight: 400;
    color: var(--primary);
    margin: 16px 0 0;
}

.sl-attorney-archive__principi-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: grid;
    gap: 48px;
    max-width: 1248px;
    margin-inline: auto;
}

@media (min-width: 1024px) {
    .sl-attorney-archive__principi-list {
        grid-template-columns: repeat(3, 1fr);
        gap: 64px;
    }
}

.sl-attorney-archive__principi-item {
    border-top: 1px solid var(--accent);
    padding-top: 24px;
}

.sl-attorney-archive__principi-num {
    color: var(--accent);
    margin-bottom: 16px;
}

.sl-attorney-archive__principi-item h3 {
    font-family: var(--font-display);
    font-size: 32px;
    line-height: 1.2;
    letter-spacing: -0.015em;
    font-weight: 400;
    color: var(--primary);
    margin: 0 0 16px;
}

.sl-attorney-archive__principi-item p {
    font-size: 16px;
    line-height: 1.7;
    color: var(--text);
    margin: 0;
}

/* CTA Finale (dark) */
.sl-attorney-archive__cta-final {
    background: var(--primary);
    color: var(--background);
    text-align: center;
    padding: clamp(64px, 8vw, 128px) clamp(24px, 5vw, 96px);
    margin: 0 calc(-1 * clamp(24px, 5vw, 96px));
}

.sl-attorney-archive__cta-final-eyebrow {
    color: var(--accent);
    margin-bottom: 24px;
}

.sl-attorney-archive__cta-final h2 {
    font-family: var(--font-display);
    font-size: clamp(40px, 5vw, 72px);
    line-height: 1.05;
    letter-spacing: -0.02em;
    font-weight: 400;
    color: var(--background);
    margin: 0 0 24px;
    max-width: 18ch;
    margin-inline: auto;
}

.sl-attorney-archive__cta-final h2 em {
    font-style: italic;
    color: var(--accent);
}

.sl-attorney-archive__cta-final p {
    font-size: 18px;
    line-height: 1.6;
    color: rgba(250, 250, 248, 0.85);
    margin: 0 0 40px;
    max-width: 50ch;
    margin-inline: auto;
}

.sl-attorney-archive__cta-final .sl-btn--primary {
    background: var(--accent);
    color: var(--primary);
    border-color: var(--accent);
}
```

### 3.3 — Smoke verify

```bash
HTML=$(curl -s "http://localhost:8080/avvocati/?_=v33t3" -m 8)
echo "  sl-attorney-archive:        $(echo "$HTML" | grep -c 'sl-attorney-archive\b')"
echo "  hero asym 8fr/4fr:           $(echo "$HTML" | grep -c 'sl-attorney-archive__hero\b')"
echo "  4 lawyer card:               $(echo "$HTML" | grep -c 'sl-attorney-archive__card')"
echo "  Principi 3:                  $(echo "$HTML" | grep -c 'sl-attorney-archive__principi-item')"
echo "  CTA finale:                  $(echo "$HTML" | grep -c 'sl-attorney-archive__cta-final')"
```

---

## TASK 4 — Drop-cap !important visibility fix (~10 min)

### 4.1 — Aggiungi !important sui drop-cap esistenti per garantire override

In sections.css cerca il blocco esistente con `.sl-page__prose > p:first-of-type::first-letter` (creato in v0.32.0):

Aggiungi `!important` su tutte le proprietà drop-cap:

```css
/* === v0.33.0 — Drop-cap visibility fix !important === */

.sl-attorney__bio-prose > p:first-of-type::first-letter,
.sl-attorney__bio > p:first-of-type::first-letter,
.sl-page__prose > p:first-of-type::first-letter,
.sl-competenza__prose > p:first-of-type::first-letter,
.sl-tier1__body > p:first-of-type::first-letter,
.sl-tier1__body > .sl-competenza__prose > p:first-of-type::first-letter,
.sl-costi-w4__calc-text > p:first-of-type::first-letter,
.sl-costi-w4__calc-prose > p:first-of-type::first-letter,
.sl-casi-w4__hero-lede > p:first-of-type::first-letter,
.sl-chi-siamo__lede-text > p:first-of-type::first-letter,
.sl-chi-siamo-w3 .sl-page__prose > p:first-of-type::first-letter,
.sl-chi-siamo-w3__story > p:first-of-type::first-letter,
.sl-competenza-w3__body > p:first-of-type::first-letter,
.sl-practice-tier1 .sl-page__prose > p:first-of-type::first-letter {
    font-family: var(--font-display) !important;
    font-size: 84px !important;
    line-height: 0.85 !important;
    float: left !important;
    margin: 8px 16px 0 0 !important;
    color: var(--primary) !important;
    font-weight: 400 !important;
}

@media (max-width: 767px) {
    .sl-attorney__bio-prose > p:first-of-type::first-letter,
    .sl-attorney__bio > p:first-of-type::first-letter,
    .sl-page__prose > p:first-of-type::first-letter,
    .sl-competenza__prose > p:first-of-type::first-letter,
    .sl-tier1__body > p:first-of-type::first-letter,
    .sl-costi-w4__calc-text > p:first-of-type::first-letter,
    .sl-chi-siamo__lede-text > p:first-of-type::first-letter {
        font-size: 60px !important;
        margin: 4px 12px 0 0 !important;
    }
}
```

NB: questo SOSTITUISCE il blocco simile creato in v0.32.0. Mantieni un solo blocco con `!important`.

### 4.2 — Smoke verify visivo via curl + grep

```bash
HTML=$(curl -s "http://localhost:8080/chi-siamo/?_=v33t4" -m 8)
echo "  Drop-cap CSS rule applied: verify visivo necessario"
```

L'utente Duccio verifica visivamente Cmd+Shift+R che il drop-cap sia visibile su:
- /chi-siamo/ → "U"
- /avvocati/emiliano-saltelli/ → "L"
- /competenze/diritto-tributario/ → "L"
- /casi/ → drop-cap su lede
- /costi/ § 03 → "T"

---

## TASK 5 — Bump + smoke + deploy + report finale (~10 min)

```bash
# Bump version
sed -i.bak 's/Version: [0-9.]\+.*/Version: 0.33.0-beta-info-pages-attorney-archive/' wp-content/themes/saltelli/style.css
sed -i.bak "s/SALTELLI_THEME_VERSION', '[^']*'/SALTELLI_THEME_VERSION', '0.33.0-beta-info-pages-attorney-archive'/" wp-content/themes/saltelli/functions.php
rm -f wp-content/themes/saltelli/{style.css,functions.php}.bak

# Cache flush local
docker compose run --rm wpcli cache flush

# Final commit
git add -A
git commit -m "feat(v0.33.0): info pages template + /avvocati/ archive refactor + drop-cap visibility !important"
git push origin main

# Deploy droplet
rsync -avz wp-content/themes/saltelli/ deploy@178.62.207.50:/var/www/saltelli/htdocs/wp-content/themes/saltelli/
ssh deploy@178.62.207.50 "
    sudo -u www-data wp cache flush --path=/var/www/saltelli/htdocs
    sudo -u www-data wp transient delete --all --path=/var/www/saltelli/htdocs
"

# Smoke test 8 URL
echo ""
echo "═══ SMOKE LIVE v0.33.0 ═══"
for URL in /avvocati/ /guide-gratuite/ /come-lavoriamo/ /prima-consulenza/ /lavora-con-noi/ /chi-siamo/ /costi/ /competenze/diritto-tributario/; do
    HTTP=$(curl -sL -o /dev/null -w "%{http_code}" "https://staging.studiolegalesaltelli.it$URL?_=v33" -m 10)
    echo "  $URL → HTTP $HTTP"
done
```

### 5.1 — Report finale

`.claude/knowledge/design/sessione-2/v0.33.0-INFO-ARCHIVE-RECOVERY.md`:

```markdown
# v0.33.0 Info Pages + Archive Recovery
## Score: 5/5 task PASS

## Per task
- T1 CSS template .sl-info-page__* shared: ✓
- T2 page.php blocchi 4 info pages (guide/come/prima/lavora): ✓
- T3 /avvocati/ archive refactor (.sl-attorney-archive__*): ✓
- T4 Drop-cap !important visibility cross-template: ✓
- T5 Bump + smoke + deploy: ✓

## Pages recovered v0.33.0
- /avvocati/ archive: design "minimal team grid" → "Sessione 2 enriched" (hero asym + drop-cap "U" + 4-card + § principi + CTA dark)
- /guide-gratuite/: drop-cap "S" + hero asimmetrico + CTA dark
- /come-lavoriamo/: drop-cap "A" + hero asimmetrico + CTA dark
- /prima-consulenza/: drop-cap "T" + hero asimmetrico + CTA dark
- /lavora-con-noi/: drop-cap "C" + hero asimmetrico + CTA dark

## Drop-cap visibility cross-template (post v0.33.0 !important)
- /chi-siamo/: "U" 84px ✓
- /avvocati/X: "L" 84px ✓
- /competenze/X: "L" 84px ✓
- /costi/ § 03: "T" 84px ✓
- /info-pages/ × 4: drop-cap visibile ✓

## Lesson learned applied
- ✓ Sitemap-first: enumerated 5 page secondarie senza JSX
- ✓ Pattern reuse: NO Sessione 3 design, riusa Sessione 2 (info-page + attorney-archive scope)
- ✓ !important strategico per fix specificity

## Next
GO walkthrough finale Duccio
o GO v1.0.0 production cut

Tempo totale: ~80 min sequential.

Quando finito segnala "v0.33.0 deployed. Info pages + archive editorial complete."
```

---

## 🔧 Strategia parallel multi-agent (OPZIONE alternativa)

Se preferisci parallelizzare per velocizzare a ~50 min, qui pattern wave3-style:

```
Wave 1 (parallel 3 agenti tmux):
  Agent A: TASK 1 + TASK 2 (template info-page + 4 page integration)
  Agent B: TASK 3 (/avvocati/ archive refactor) — file diverso, no conflict
  Agent C: TASK 4 (drop-cap !important fix) — solo CSS scope dedicato

Wave 2 (orchestrator merge):
  TASK 5 (bump + smoke + deploy) — solo dopo merge dei 3 branch
```

NB: Agent A+B+C lavorano su file diversi (page.php vs archive-avvocato.php vs sections.css scope drop-cap), parallel-safe.

Decide tu se sequential o parallel — io raccomando **sequential** dato che task 1+2 hanno dipendenza diretta.

---

## 🆘 Se incontri imprevisti

```
- saltelli_team_grid_layout() helper non esiste → mantieni layout grid 4-col fixed
- saltelli_get_attorney_specializations() non esiste → fallback array empty
- ACF foto_ritratto non popolato → placeholder "Ritratto · 3:4"
- Drop-cap visibility ancora basso → aumenta specificità CSS (.sl-page__prose body p:first-of-type::first-letter)
- /avvocati/ archive non renderizza nuovo template → forse cache plugin attivo
```

Tempo totale: ~80-90 min sequential.

Buon lavoro. Quando finito, l'orchestrator esegue audit visivo finale + side-by-side check con Duccio.
