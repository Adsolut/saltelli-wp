<?php
/**
 * Helper functions — small utilities used across templates and partials.
 *
 * @package Saltelli
 */

defined('ABSPATH') || exit;

/**
 * Wrapper attorno a get_field() che funziona anche se ACF non è installato.
 * Restituisce $default se ACF non c'è o il campo non esiste.
 *
 * @param string $name
 * @param int|null $post_id
 * @param mixed $default
 * @return mixed
 */
function saltelli_field($name, $post_id = null, $default = null) {
    $post_id = $post_id ?: get_the_ID();

    // Try ACF first
    if (function_exists('get_field')) {
        $value = get_field($name, $post_id);
        if ($value !== null && $value !== '' && $value !== false) {
            return $value;
        }
    }

    // Fallback: post_meta diretto. Supporta sia valori scalari sia repeater
    // serializzati (modalità Step D Content Migration: update_post_meta(name, $array)).
    if ($post_id) {
        $raw = get_post_meta($post_id, $name, true);
        if ($raw !== '' && $raw !== null && $raw !== false) {
            // Già array (repeater seriale) o scalare → ritorna così
            if (is_array($raw)) return $raw;
            // Numerico = repeater style ACF (es. faq=5, faq_0_domanda=...).
            // Rimonta righe.
            if (is_numeric($raw) && (int) $raw > 0 && (int) $raw < 100) {
                $rows = saltelli_field_repeater_rows($name, (int) $raw, $post_id);
                if (!empty($rows)) return $rows;
            }
            return $raw;
        }
    }
    return $default;
}

/**
 * Rimonta le righe di un repeater ACF-style (faq_0_domanda, faq_0_risposta, ...).
 *
 * @param string $name
 * @param int $count
 * @param int $post_id
 * @return array<int, array<string, mixed>>
 */
function saltelli_field_repeater_rows($name, $count, $post_id) {
    global $wpdb;
    $like = $wpdb->esc_like($name . '_') . '%';
    $results = $wpdb->get_results($wpdb->prepare(
        "SELECT meta_key, meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key LIKE %s",
        $post_id, $like
    ));
    $rows = [];
    foreach ($results as $row) {
        if (preg_match('/^' . preg_quote($name, '/') . '_(\d+)_(.+)$/', $row->meta_key, $m)) {
            $idx = (int) $m[1];
            $sub = $m[2];
            if (!isset($rows[$idx])) $rows[$idx] = [];
            $rows[$idx][$sub] = maybe_unserialize($row->meta_value);
        }
    }
    ksort($rows);
    return array_values($rows);
}

/**
 * Restituisce le specializzazioni di un avvocato come array di stringhe.
 * Supporta sia repeater ACF Pro (sub-field "label") sia textarea (split su \n).
 *
 * @param int $post_id
 * @return array<string>
 */
function saltelli_get_attorney_specializations($post_id) {
    $raw = saltelli_field('specializzazioni', $post_id, []);

    if (is_string($raw)) {
        $lines = preg_split('/\r\n|\r|\n/', $raw);
        return array_values(array_filter(array_map('trim', $lines)));
    }

    if (is_array($raw)) {
        $out = [];
        foreach ($raw as $row) {
            if (is_array($row) && isset($row['label'])) {
                $out[] = trim((string) $row['label']);
            } elseif (is_string($row)) {
                $out[] = trim($row);
            }
        }
        return array_values(array_filter($out));
    }

    return [];
}

/**
 * Per una pagina competenza, ritorna gli avvocati referenti (post objects).
 * Restituisce un array di WP_Post.
 *
 * @param int $competenza_id
 * @return WP_Post[]
 */
function saltelli_get_attorneys_for_competenza($competenza_id) {
    $ids = saltelli_field('lead_attorneys', $competenza_id, []);
    if (empty($ids)) {
        return [];
    }
    $ids = is_array($ids) ? $ids : [$ids];
    $ids = array_filter(array_map('intval', $ids));
    if (empty($ids)) {
        return [];
    }

    $q = get_posts([
        'post_type'        => 'avvocato',
        'post__in'         => $ids,
        'orderby'          => 'post__in',
        'numberposts'      => -1,
        'suppress_filters' => false,
    ]);
    return $q ?: [];
}

/**
 * Genera la catena breadcrumb come array di nodi:
 *   [ ['name' => 'Home', 'url' => 'https://...'], ['name' => 'Competenze', 'url' => '...'], ['name' => 'Diritto tributario'] ]
 * L'ultimo nodo NON ha 'url' (è la pagina corrente).
 *
 * @param int|null $post_id
 * @return array<int, array{name:string, url?:string}>
 */
/**
 * v0.13.8 — Centralizza il label visualizzato nel breadcrumb per post-type
 * archive node. Risolve incoerenza CPT label "Competenze" vs menu nav
 * "Aree di Pratica" vs H1 "Diciannove aree." → unico label "Aree di pratica"
 * sul frontend (admin WP labels restano "Competenze" per Duccio UX).
 *
 * @param string $post_type
 * @return string Friendly label
 */
function saltelli_breadcrumb_pt_label($post_type) {
    $map = [
        'competenza' => __('Aree di pratica', 'saltelli'),
        'avvocato'   => __('Avvocati', 'saltelli'),
        'post'       => __('Editoriale', 'saltelli'),
    ];
    if (isset($map[$post_type])) {
        return $map[$post_type];
    }
    $obj = get_post_type_object($post_type);
    return $obj ? (string) $obj->labels->name : (string) $post_type;
}

function saltelli_get_breadcrumb_chain($post_id = null) {
    $post_id = $post_id ?: get_queried_object_id();
    $chain = [];

    // Home — sempre primo
    $chain[] = ['name' => __('Home', 'saltelli'), 'url' => home_url('/')];

    if (is_404() || is_search()) {
        $chain[] = ['name' => is_404() ? __('Non trovato', 'saltelli') : __('Risultati ricerca', 'saltelli')];
        return $chain;
    }

    // Archive
    if (is_post_type_archive()) {
        // get_post_type() può essere vuoto su archive senza post.
        // get_queried_object() ritorna il WP_Post_Type su archive CPT.
        $obj = get_queried_object();
        $pt  = $obj instanceof WP_Post_Type ? $obj->name : (string) get_query_var('post_type');
        $chain[] = ['name' => saltelli_breadcrumb_pt_label($pt)];
        return $chain;
    }

    if (is_tax() || is_category() || is_tag()) {
        $term = get_queried_object();
        if ($term && !is_wp_error($term)) {
            // v0.13.8 — aggiungi parent CPT archive node per taxonomy 'tipo-area'
            // (collegata al CPT 'competenza'). Risolve breadcrumb truncation
            // 'Home / Per i Privati' → 'Home / Aree di pratica / Per i Privati'.
            if (is_tax('tipo-area')) {
                $chain[] = [
                    'name' => saltelli_breadcrumb_pt_label('competenza'),
                    'url'  => get_post_type_archive_link('competenza'),
                ];
            }
            $chain[] = ['name' => $term->name];
        }
        return $chain;
    }

    if (!$post_id) {
        return $chain;
    }

    $post = get_post($post_id);
    if (!$post) {
        return $chain;
    }

    // CPT singolo: aggiungi link archive
    if ($post->post_type === 'avvocato') {
        $chain[] = ['name' => saltelli_breadcrumb_pt_label('avvocato'), 'url' => get_post_type_archive_link('avvocato')];
    } elseif ($post->post_type === 'competenza') {
        $chain[] = ['name' => saltelli_breadcrumb_pt_label('competenza'), 'url' => get_post_type_archive_link('competenza')];
    } elseif ($post->post_type === 'post') {
        $blog_page_id = (int) get_option('page_for_posts');
        if ($blog_page_id) {
            $chain[] = ['name' => get_the_title($blog_page_id), 'url' => get_permalink($blog_page_id)];
        } else {
            $chain[] = ['name' => __('Blog', 'saltelli'), 'url' => home_url('/blog/')];
        }
        // Categoria primaria
        $cats = get_the_category($post_id);
        if (!empty($cats)) {
            $cat = $cats[0];
            $chain[] = ['name' => $cat->name, 'url' => get_category_link($cat->term_id)];
        }
    } elseif ($post->post_type === 'page') {
        // Pagine gerarchiche: ricostruisci catena ancestrale
        $ancestors = array_reverse(get_post_ancestors($post_id));
        foreach ($ancestors as $aid) {
            $chain[] = [
                'name' => get_the_title($aid),
                'url'  => get_permalink($aid),
            ];
        }
    }

    // Pagina corrente — ultimo nodo, no URL
    $chain[] = ['name' => get_the_title($post_id)];

    return $chain;
}

/**
 * IA Unification v0.13.0 — Render breadcrumb HTML uniforme.
 *
 * Emit del solo markup HTML (`<nav class="sl-page__breadcrumb">`).
 * Lo schema BreadcrumbList JSON-LD è già emesso a livello sito da
 * `inc/schema/schema-loader.php` (active su 12/12 pagine, verificato
 * in DOM audit). Questo helper unifica solo la presentazione visiva
 * cross-template (audit CRO §1.3.1 + Audit GEO requirement).
 *
 * Skip rendering quando chain ha < 2 nodi (homepage o catena vuota).
 *
 * @param int|null $post_id Post id override; default = queried object.
 * @return void
 */
function saltelli_render_breadcrumb($post_id = null) {
    if (!function_exists('saltelli_get_breadcrumb_chain')) return;
    $chain = saltelli_get_breadcrumb_chain($post_id);
    if (empty($chain) || count($chain) < 2) return;

    $last_idx = count($chain) - 1;
    ?>
    <nav class="sl-mono sl-page__breadcrumb" aria-label="<?php esc_attr_e('Breadcrumb', 'saltelli'); ?>">
        <?php foreach ($chain as $i => $node) :
            $name = isset($node['name']) ? (string) $node['name'] : '';
            $url  = isset($node['url'])  ? (string) $node['url']  : '';
            $is_last = ($i === $last_idx);
            ?>
            <?php if ($i > 0) : ?>
                <span class="sl-page__breadcrumb-sep" aria-hidden="true"> / </span>
            <?php endif; ?>
            <?php if (!$is_last && $url !== '') : ?>
                <a href="<?php echo esc_url($url); ?>"><?php echo esc_html($name); ?></a>
            <?php else : ?>
                <span aria-current="page"><?php echo esc_html($name); ?></span>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>
    <?php
}

/**
 * Ritorna il numero di FAQ compilate per una competenza.
 * Utile per decidere se iniettare schema FAQPage.
 *
 * @param int $post_id
 * @return int
 */
function saltelli_count_faq($post_id) {
    $faq = saltelli_field('faq', $post_id, []);
    if (!is_array($faq)) {
        return 0;
    }
    $valid = 0;
    foreach ($faq as $row) {
        if (!empty($row['domanda']) && !empty($row['risposta'])) {
            $valid++;
        }
    }
    return $valid;
}

/**
 * Helper: URL canonico della pagina corrente.
 */
function saltelli_canonical_url() {
    if (is_singular()) {
        $url = get_permalink();
    } elseif (is_post_type_archive()) {
        $url = get_post_type_archive_link(get_post_type());
    } elseif (is_category() || is_tag() || is_tax()) {
        $url = get_term_link(get_queried_object());
    } elseif (is_home() || is_front_page()) {
        $url = home_url('/');
    } else {
        $url = home_url(add_query_arg([], $GLOBALS['wp']->request));
    }
    return is_wp_error($url) ? home_url('/') : $url;
}

/**
 * Helper: telefono in formato E.164 dello studio (fallback hardcoded).
 */
function saltelli_studio_phone_e164() {
    return '+390811813119';
}

/**
 * Output di un blocco JSON-LD inline.
 *
 * Nota di compatibilità: alcuni plugin (e.g. iubenda Cookie Law Solution)
 * applicano un output buffer che fa passare l'HTML attraverso DOMDocument::loadHTML
 * + saveHTML. Quel passaggio convertirebbe i caratteri UTF-8 (es. "à") in
 * entità numeriche HTML (&#224;) che però sono *invalide* dentro un JSON
 * inline a `<script>`. Per questo NON usiamo JSON_UNESCAPED_UNICODE: emettiamo
 * `\uXXXX` ASCII-safe, che è JSON valido e immune a DOMDocument.
 *
 * @param array $schema  Schema array (incluso @context).
 * @param int   $extra_flags  Flag aggiuntivi opzionali.
 * @return void Emette `<script type="application/ld+json">…</script>`.
 */
function saltelli_emit_jsonld($schema, $extra_flags = 0) {
    $flags = JSON_UNESCAPED_SLASHES | $extra_flags;
    $json = wp_json_encode($schema, $flags);
    if ($json === false) {
        return;
    }
    echo "\n<script type=\"application/ld+json\">" . $json . "</script>\n";
}

/**
 * Helper: configurazione anagrafica studio.
 * Usato dai partial schema per tenere centralizzati i dati.
 * TODO: spostare in ACF Options Page quando ACF Pro disponibile.
 *
 * Dati confermati dal cliente il 2026-04-28 (sessione Ludovica):
 *  - GPS precisi via Google Business 2026-05-02: (40.8332541, 14.2414699)
 *  - Anno fondazione studio attuale: gennaio 2019
 *  - Social: Facebook share-URL, Instagram, LinkedIn (personale di Emiliano usato come profilo studio)
 */
function saltelli_studio_data() {
    return [
        'legal_name'    => 'Studio Legale Emiliano Saltelli & Partners',
        'alt_names'     => ['Studio Legale Saltelli', 'Studio Saltelli Napoli'],
        'street'        => 'Via Vannella Gaetani, 27',
        'postal_code'   => '80121',
        'locality'      => 'Napoli',
        'region'        => 'Campania',
        'country'       => 'IT',
        'phone'         => '+390811813119',
        'whatsapp'      => '+393517138006',
        'email'         => 'info@studiolegalesaltelli.it',
        'pec'           => 'emilianosaltelli@avvocatinapoli.legalmail.it',
        'vat'           => 'IT06685101211',
        'tax_id'        => '06685101211',
        // GPS ufficiali Google Business 2026-05-02 (override delle stime precedenti 40.830267/14.237217).
        'lat'           => 40.8332541,
        'lng'           => 14.2414699,
        'opens'         => '10:00',
        'closes'        => '19:00',
        'days'          => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
        'price_range'   => '€€',
        // Studio attuale fondato gennaio 2019. Lineage: studio precedente
        // (studiolegaleavvass.it) dal 2008 con ex socio, abilitazione piena 2013.
        'founding_date' => '2019-01',
        // Social — confermati 2026-04-28. URL ripuliti dai parametri di tracking.
        // NOTA: la "pagina LinkedIn studio" è in realtà il profilo personale di
        // Emiliano (https://www.linkedin.com/in/emilianosaltelli/) — lo agganciamo
        // a Person Emiliano (Person.sameAs), NON a Organization.sameAs, per
        // mantenere coerenza semantica.
        'social' => [
            'facebook'  => 'https://www.facebook.com/share/1D1jCY7BnW/',
            'instagram' => 'https://www.instagram.com/studiolegalesaltelli/',
            // 'linkedin_company' — non disponibile (lo studio non ha Company Page).
            // 'google_business_profile' — TODO: da creare in Fase 1 del programma.
        ],
    ];
}

/**
 * LinkedIn personale per @id slug avvocato. Confermati: solo Emiliano (al 2026-04-28).
 * Per Fabiana, Antonia, Stefano si attendono i link individuali.
 */
function saltelli_attorney_linkedin($slug) {
    $map = [
        'emiliano-saltelli'         => 'https://www.linkedin.com/in/emilianosaltelli/',
        // 'fabiana-saltelli'        => '', // TODO Ludovica
        // 'antonia-battista'        => '', // TODO Ludovica
        // 'stefano-gaetano-tedesco' => '', // TODO Ludovica (scope uncertain — vedi memory)
    ];
    return $map[$slug] ?? '';
}

/**
 * Fallback menu primario quando l'admin non ha ancora assegnato un menu alla location 'primary'.
 * Usato anche dalla mobile menu copy.
 */
function saltelli_header_menu_fallback() {
    $items = [
        ['Studio',     '/chi-siamo/'],
        ['Avvocati',   '/avvocati/'],
        ['Competenze', '/competenze/'],
        ['Casi',       '/casi/'],
        ['Editoriale', '/blog/'],
        ['Contatti',   '/contatti/'],
    ];
    echo '<ul class="sl-header__menu">';
    foreach ($items as $i) {
        echo '<li class="menu-item"><a href="' . esc_url(home_url($i[1])) . '">' . esc_html($i[0]) . '</a></li>';
    }
    echo '</ul>';
}

/**
 * Fallback footer "Lo Studio" quando il menu non è assegnato.
 */
function saltelli_footer_studio_fallback() {
    $items = [
        ['Lo studio', '/chi-siamo/'],
        ['Avvocati',  '/avvocati/'],
        ['Casi',      '/casi/'],
        ['Editoriale','/blog/'],
        ['Contatti',  '/contatti/'],
    ];
    echo '<ul class="sl-footer__menu">';
    foreach ($items as $i) {
        echo '<li><a href="' . esc_url(home_url($i[1])) . '">' . esc_html($i[0]) . '</a></li>';
    }
    echo '</ul>';
}

/**
 * Fallback footer "Aree" — popolato dal CPT competenza ordinato per menu_order.
 */
function saltelli_footer_aree_fallback() {
    $posts = get_posts([
        'post_type'   => 'competenza',
        'numberposts' => 19,
        'orderby'     => ['menu_order' => 'ASC', 'title' => 'ASC'],
    ]);
    if (empty($posts)) {
        echo '<ul class="sl-footer__menu"><li><a href="' . esc_url(home_url('/competenze/')) . '">Tutte le aree</a></li></ul>';
        return;
    }
    echo '<ul class="sl-footer__menu sl-footer__menu--aree">';
    foreach ($posts as $p) {
        echo '<li><a href="' . esc_url(get_permalink($p)) . '">' . esc_html(get_the_title($p)) . '</a></li>';
    }
    echo '</ul>';
}

/**
 * Fallback footer "Legal".
 */
function saltelli_footer_legal_fallback() {
    $items = [
        ['Privacy',   '/privacy/'],
        ['Cookie',    '/cookie/'],
        ['Note legali', '/note-legali/'],
    ];
    echo '<ul class="sl-footer__menu">';
    foreach ($items as $i) {
        echo '<li><a href="' . esc_url(home_url($i[1])) . '">' . esc_html($i[0]) . '</a></li>';
    }
    echo '</ul>';
}

/**
 * Wrapper per leggere campi ACF Options.
 * Funziona sia con ACF Pro (true) che con ACF Free (degrada a default).
 *
 * @param string $name Nome campo.
 * @param mixed $default Default se ACF/options non disponibile.
 * @return mixed
 */
function saltelli_option($name, $default = null) {
    if (!function_exists('get_field')) {
        return $default;
    }
    $value = get_field($name, 'option');
    if ($value === null || $value === '' || $value === false) {
        return $default;
    }
    return $value;
}

/**
 * Layout asimmetrico avvocati homepage (4 ritratti). Indicizzato per posizione (0-3).
 * Replica esattamente i valori del homepage-desktop.jsx:
 *   #0 Emiliano   col 1  span 5  offset 0
 *   #1 Fabiana    col 7  span 5  offset 96
 *   #2 Antonia    col 2  span 5  offset 64
 *   #3 Stefano    col 8  span 4  offset 32
 *
 * @return array<int, array{col:int, span:int, offset:int}>
 */
function saltelli_team_grid_layout() {
    return [
        ['col' => 1, 'span' => 5, 'offset' => 0],
        ['col' => 7, 'span' => 5, 'offset' => 96],
        ['col' => 2, 'span' => 5, 'offset' => 64],
        ['col' => 8, 'span' => 4, 'offset' => 32],
    ];
}

/**
 * Categoria tassonomia "tipo-area" leggibile (etichetta filtro pillole) di una competenza.
 * Ritorna la prima term name oppure stringa vuota.
 *
 * @param int $post_id
 * @return string
 */
function saltelli_competenza_category_label($post_id) {
    $terms = get_the_terms($post_id, 'tipo-area');
    if (is_wp_error($terms) || empty($terms)) {
        return '';
    }
    return (string) $terms[0]->name;
}

/**
 * Slug categoria competenza (utile per data-attribute filtro).
 *
 * @param int $post_id
 * @return string
 */
function saltelli_competenza_category_slug($post_id) {
    $terms = get_the_terms($post_id, 'tipo-area');
    if (is_wp_error($terms) || empty($terms)) {
        return '';
    }
    return (string) $terms[0]->slug;
}

/**
 * Reading time stimato per un blog post (200 parole/min).
 *
 * @param int $post_id
 * @return int Minuti.
 */
function saltelli_reading_time($post_id) {
    $content = get_post_field('post_content', $post_id);
    $words   = str_word_count(wp_strip_all_tags((string) $content));
    return max(1, (int) ceil($words / 200));
}

/**
 * Casi rappresentativi homepage — ritorna repeater ACF, fallback editoriale 4 casi.
 *
 * @return array<int, array{identifier:string, descrizione:string, outcome:string}>
 */
function saltelli_homepage_cases() {
    $casi = saltelli_option('casi_rappresentativi_home', []);
    if (is_array($casi) && !empty($casi)) {
        $out = [];
        foreach ($casi as $row) {
            if (!empty($row['identifier']) && !empty($row['descrizione']) && !empty($row['outcome'])) {
                $out[] = [
                    'identifier'  => (string) $row['identifier'],
                    'descrizione' => (string) $row['descrizione'],
                    'outcome'     => (string) $row['outcome'],
                ];
            }
        }
        if (!empty($out)) {
            return $out;
        }
    }
    return [
        ['identifier' => 'vs. AGE Riscossione · 2024', 'descrizione' => 'Annullamento di cartella esattoriale per importo superiore a 240.000 € a carico di società in liquidazione.', 'outcome' => 'Annullamento'],
        ['identifier' => 'Cassazione · 2024',          'descrizione' => 'Conferma in Cassazione di sentenza favorevole in materia di licenziamento per giusta causa illegittimo.',                          'outcome' => 'Vittoria'],
        ['identifier' => 'Tribunale di Napoli · 2023', 'descrizione' => 'Primo riconoscimento in Campania di trascrizione integrale di nascita di minore con due madri.',                                  'outcome' => 'Riconoscimento'],
        ['identifier' => "Corte d'Appello · 2023",     'descrizione' => "Riforma di sentenza di primo grado in materia di accertamento sintetico, con riduzione dell'80% del dovuto.",                     'outcome' => 'Riforma'],
    ];
}

/**
 * Lista estesa casi per pagina /casi/ (10 casi v0.19.0).
 * Match JSX `saltelli-s2-casi.jsx` data set. Categoria filter:
 * Privati / Imprese / Contenzioso / Altri.
 * Featured = primo caso simbolo (€240k AGE Riscossione).
 *
 * @return array<int, array{id:string, cat:string, outcome:string, lbl:string, desc:string, featured?:bool}>
 */
function saltelli_all_cases() {
    return [
        ['id' => 'vs. AGE Riscossione · 2024',     'cat' => 'Imprese',     'outcome' => '€240.000',      'lbl' => 'Annullamento',  'desc' => 'Annullamento integrale di cartella esattoriale a carico di società in liquidazione, eccezione di prescrizione e vizio di notifica.', 'featured' => true],
        ['id' => 'Cassazione · 2024',              'cat' => 'Privati',     'outcome' => 'Vittoria',      'lbl' => 'Conferma',      'desc' => 'Conferma in Cassazione di sentenza favorevole in materia di licenziamento per giusta causa illegittimo.'],
        ['id' => 'Tribunale di Napoli · 2023',     'cat' => 'Privati',     'outcome' => 'Riconoscimento','lbl' => 'Storica',       'desc' => 'Primo riconoscimento in Campania di trascrizione integrale di atto di nascita di minore con due madri.'],
        ['id' => "Corte d'Appello · 2023",         'cat' => 'Imprese',     'outcome' => '−80%',          'lbl' => 'Riforma',       'desc' => 'Riforma di sentenza di primo grado in materia di accertamento sintetico, riduzione dell\'80% del dovuto.'],
        ['id' => 'CTR Campania · 2022',            'cat' => 'Imprese',     'outcome' => '€87.000',       'lbl' => 'Vittoria',      'desc' => 'Riconoscimento di credito IVA contestato dall\'Agenzia delle Entrate per €87.000.'],
        ['id' => 'Tribunale di Napoli · 2023',     'cat' => 'Contenzioso', 'outcome' => 'Vittoria',      'lbl' => 'Risarcimento',  'desc' => 'Risarcimento del danno per condotta antisindacale di azienda metalmeccanica.'],
        ['id' => 'Tribunale Famiglia · 2024',      'cat' => 'Privati',     'outcome' => 'Affido',        'lbl' => 'Condiviso',     'desc' => 'Affidamento condiviso e mantenimento adeguato in separazione complessa con immobili in più province.'],
        ['id' => 'TAR Campania · 2023',            'cat' => 'Altri',       'outcome' => 'Annullamento',  'lbl' => 'Atto P.A.',     'desc' => 'Annullamento di provvedimento amministrativo in materia di edilizia, per difetto di motivazione.'],
        ['id' => 'Tribunale di Napoli · 2022',     'cat' => 'Contenzioso', 'outcome' => '€156.000',      'lbl' => 'Recupero',      'desc' => 'Recupero di credito commerciale per società del settore tessile, con esecuzione mobiliare immediata.'],
        ['id' => 'Cassazione · 2022',              'cat' => 'Imprese',     'outcome' => 'Vittoria',      'lbl' => 'Soc.',          'desc' => 'Conferma di sentenza favorevole in materia di responsabilità solidale di amministratori di S.r.l.'],
    ];
}

/**
 * Lista completa casi per pagina /casi/ (Wave 3 · Task 5).
 *
 * Compone:
 *  1. saltelli_homepage_cases() — 4 casi base (ACF repeater oppure fallback editoriale).
 *     Vengono normalizzati allo schema esteso ['id', 'cat', 'outcome', 'lbl', 'desc', 'featured'].
 *  2. Estensione 4-6 casi sourcing dai blog post tag/cat 'sentenze' o 'sentenza' (se esistono).
 *     Mappa post → caso usando excerpt + ACF/meta `_caso_outcome`/`_caso_categoria`.
 *  3. Fallback editoriale: i restanti casi della baseline JSX (saltelli_all_cases),
 *     per garantire un volume di 8-10 casi anche senza blog content.
 *
 * Dedupe per `id`, normalizza shape, restituisce max 12 casi.
 *
 * @return array<int, array{id:string, cat:string, outcome:string, lbl:string, desc:string, featured?:bool}>
 */
function saltelli_cases_full() {
    $out  = [];
    $seen = [];

    $push = function ($case) use (&$out, &$seen) {
        if (empty($case['id']) || empty($case['desc']) || empty($case['outcome'])) {
            return;
        }
        $key = strtolower(trim((string) $case['id']));
        if (isset($seen[$key])) {
            // Upgrade pre-esistente con metadati nuovi (cat/lbl/featured/outcome) se più ricchi.
            $idx = $seen[$key];
            if (!$out[$idx]['featured'] && !empty($case['featured'])) {
                $out[$idx]['featured'] = true;
                // Featured ⇒ adotta outcome+lbl JSX-fidelity (es. "€240.000" + "Annullamento").
                if (!empty($case['outcome'])) {
                    $out[$idx]['outcome'] = (string) $case['outcome'];
                }
                if (!empty($case['lbl'])) {
                    $out[$idx]['lbl'] = (string) $case['lbl'];
                }
            }
            if ($out[$idx]['lbl'] === '' && !empty($case['lbl'])) {
                $out[$idx]['lbl'] = (string) $case['lbl'];
            }
            if ($out[$idx]['cat'] === 'Altri' && !empty($case['cat'])) {
                $out[$idx]['cat'] = (string) $case['cat'];
            }
            return;
        }
        $out[] = [
            'id'       => (string) $case['id'],
            'cat'      => isset($case['cat']) ? (string) $case['cat'] : 'Altri',
            'outcome'  => (string) $case['outcome'],
            'lbl'      => isset($case['lbl']) ? (string) $case['lbl'] : '',
            'desc'     => (string) $case['desc'],
            'featured' => !empty($case['featured']),
        ];
        $seen[$key] = count($out) - 1;
    };

    // 1. Casi homepage (ACF o fallback). Normalizziamo lo shape.
    $homepage = function_exists('saltelli_homepage_cases') ? saltelli_homepage_cases() : [];
    foreach ($homepage as $h) {
        $push([
            'id'       => isset($h['identifier']) ? $h['identifier'] : (isset($h['id']) ? $h['id'] : ''),
            'desc'     => isset($h['descrizione']) ? $h['descrizione'] : (isset($h['desc']) ? $h['desc'] : ''),
            'outcome'  => isset($h['outcome']) ? $h['outcome'] : '',
            'cat'      => isset($h['cat']) ? $h['cat'] : 'Privati',
            'lbl'      => isset($h['lbl']) ? $h['lbl'] : '',
            'featured' => !empty($h['featured']),
        ]);
    }

    // 2. Estensione da blog post tag/category 'sentenze'.
    $sentenze_posts = get_posts([
        'post_type'      => 'post',
        'posts_per_page' => 6,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'tax_query'      => [
            'relation' => 'OR',
            ['taxonomy' => 'category', 'field' => 'slug', 'terms' => ['sentenze', 'sentenza', 'casi'], 'operator' => 'IN'],
            ['taxonomy' => 'post_tag', 'field' => 'slug', 'terms' => ['sentenze', 'sentenza', 'casi'], 'operator' => 'IN'],
        ],
        'suppress_filters' => false,
    ]);
    foreach ($sentenze_posts as $sp) {
        $sp_id      = is_object($sp) ? (int) $sp->ID : 0;
        $sp_outcome = (string) saltelli_field('_caso_outcome', $sp_id, '');
        $sp_cat     = (string) saltelli_field('_caso_categoria', $sp_id, '');
        $sp_label   = (string) saltelli_field('_caso_label', $sp_id, 'Sentenza');
        $sp_desc    = wp_strip_all_tags(get_the_excerpt($sp));
        if ($sp_desc === '') {
            $sp_desc = wp_trim_words(wp_strip_all_tags(get_post_field('post_content', $sp)), 28, '…');
        }
        if ($sp_cat === '') {
            $sp_cats = get_the_category($sp_id);
            $sp_cat  = !empty($sp_cats) ? $sp_cats[0]->name : 'Altri';
        }
        if ($sp_outcome === '') {
            $sp_outcome = __('Vittoria', 'saltelli');
        }
        $push([
            'id'      => is_object($sp) ? get_the_title($sp) : '',
            'desc'    => $sp_desc,
            'outcome' => $sp_outcome,
            'cat'     => $sp_cat,
            'lbl'     => $sp_label,
        ]);
    }

    // 3. Fallback editoriale (saltelli_all_cases) per portare il volume a 8-10.
    if (count($out) < 8 && function_exists('saltelli_all_cases')) {
        foreach (saltelli_all_cases() as $ec) {
            $push($ec);
            if (count($out) >= 10) {
                break;
            }
        }
    }

    return array_slice($out, 0, 12);
}

/**
 * Casi rappresentativi per singolo avvocato — fallback editoriale per slug.
 * Ritorna array di max 3 casi: ['id', 'desc', 'outcome', 'lbl'].
 * Per ora popolato solo per Emiliano (slug `emiliano-saltelli`); altri slug
 * tornano array vuoto e la sezione viene skippata nel template.
 *
 * Source data: practice-tier1.jsx data.casi (sessione-2 design ref).
 *
 * @param string $slug
 * @return array<int, array{id:string, desc:string, outcome:string, lbl:string}>
 */
function saltelli_attorney_cases($slug) {
    $map = [
        'emiliano-saltelli' => [
            ['id' => 'vs. AGE Riscossione · 2024', 'lbl' => 'Annullamento', 'outcome' => '€240.000', 'desc' => 'Annullamento integrale di cartella esattoriale a carico di società in liquidazione, eccezione di prescrizione e vizio di notifica.'],
            ['id' => 'Cassazione · 2023',          'lbl' => 'Riforma',      'outcome' => '−80%',     'desc' => 'Riforma di accertamento sintetico in Corte di Cassazione con riduzione del dovuto.'],
            ['id' => 'CTR Campania · 2022',        'lbl' => 'Vittoria',     'outcome' => '€87.000',  'desc' => 'Riconoscimento di credito IVA contestato dall\'Agenzia per la società del settore tessile.'],
        ],
        'fabiana-saltelli' => [
            ['id' => 'Cassazione · 2024',          'lbl' => 'Conferma',     'outcome' => 'Vittoria', 'desc' => 'Conferma in Cassazione di sentenza favorevole in materia di licenziamento per giusta causa illegittimo.'],
            ['id' => 'Tribunale Napoli · 2023',    'lbl' => 'Risarcimento', 'outcome' => 'Antisind.', 'desc' => 'Risarcimento del danno per condotta antisindacale di azienda metalmeccanica con riconoscimento integrale.'],
        ],
        'antonia-battista' => [
            ['id' => 'Tribunale Napoli · 2023',    'lbl' => 'Storica',      'outcome' => 'Riconosc.', 'desc' => 'Primo riconoscimento in Campania di trascrizione integrale di atto di nascita di minore con due madri.'],
        ],
    ];
    return $map[$slug] ?? [];
}

/**
 * Earned media outlets — ritorna repeater ACF, fallback editoriale.
 *
 * @return array<int, string>
 */
function saltelli_press_outlets() {
    $outlets = saltelli_option('press_outlets', []);
    if (is_array($outlets) && !empty($outlets)) {
        $out = [];
        foreach ($outlets as $row) {
            if (!empty($row['nome'])) {
                $out[] = (string) $row['nome'];
            }
        }
        if (!empty($out)) {
            return $out;
        }
    }
    return ['Il Mattino', 'La Repubblica · Napoli', 'Il Sole 24 Ore', 'Diritto.it', 'Altalex', 'Camera Avvocati Napoli'];
}

/**
 * v0.22.0 — Wrappa ogni parola di un titolo h1 in <span class="sl-word">.
 * Preserva i tag inline whitelisted (em, br) per stylized headlines come
 * "Casi <em>rappresentativi.</em>". Output safe-escaped via wp_kses.
 *
 * @param string $title Stringa testo o markup inline minimal.
 * @return string HTML con span.sl-word per ogni parola.
 */
function saltelli_split_h1_words($title) {
    $title = (string) $title;
    if ($title === '') {
        return '';
    }
    // Tokenizer: separa per tag inline e parole. Mantiene <em>, </em>, <br>.
    $parts = preg_split('/(<\/?(?:em|br)\s*\/?>|\s+)/i', $title, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
    if (!is_array($parts)) {
        return esc_html($title);
    }
    $out  = '';
    $idx  = 0;
    foreach ($parts as $token) {
        if (preg_match('/^\s+$/', $token)) {
            $out .= ' ';
        } elseif (preg_match('/^<\/?(?:em|br)\s*\/?>$/i', $token)) {
            $out .= $token;
        } else {
            $out .= '<span class="sl-word" data-i="' . (int) $idx . '">' . esc_html($token) . '</span>';
            $idx++;
        }
    }
    return $out;
}
