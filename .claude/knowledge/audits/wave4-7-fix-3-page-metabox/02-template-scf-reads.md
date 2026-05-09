# Template × SCF reads inventory — Wave 4.7.fix.3 P1 Discovery

**Data**: 2026-05-08
**Source**: grep saltelli_option/get_field nei template + template-parts.

---

## Tabella sintetica

| Template | SCF reads (saltelli_option) | Pagina servita | Migration verdict |
|---|---|---|---|
| `front-page.php` | hero_eyebrow, hero_headline, hero_subheadline, hero_cta_label, hero_cta_url, colophon_indirizzo, colophon_orari, colophon_email, colophon_telefono, studio_titolo_sezione, studio_body, studio_foto_facciata, team_titolo, cases_titolo, cta_default_url, cta_default_label, cta_subline_italic + helper saltelli_homepage_cases() reads `casi_rappresentativi_home` + helper saltelli_press_outlets() reads `press_outlets` | `/` Homepage (Page WP 17 "Home" + is_front_page() router) | **MIGRA hero_*, studio_*, team_*, cases_titolo, casi_rappresentativi_home, press_outlets** → Page 17 metabox. **RESTA colophon_*, cta_default_*** (globali, usati anche in footer/19 templates) |
| `template-parts/page-chi-siamo-hub.php` | hub_chisiamo_eyebrow, hub_chisiamo_h1_main, hub_chisiamo_h1_emphasis, hub_chisiamo_intro | `/chi-siamo/` (Page WP 2822) | **MIGRA tutti** → Page 2822 metabox |
| `template-parts/page-aree-di-pratica-hub.php` | hub_aree_eyebrow, hub_aree_h1_main, hub_aree_h1_emphasis, hub_aree_intro, hub_aree_cluster_privati_label, hub_aree_cluster_privati_desc, hub_aree_cluster_imprese_label, hub_aree_cluster_imprese_desc, hub_aree_cluster_contenzioso_label, hub_aree_cluster_contenzioso_desc | `/aree-di-pratica/` (Page WP 2812) | **MIGRA tutti** → Page 2812 metabox (4 fields hero + 6 cluster fields = 10) |
| `template-parts/page-risorse-hub.php` | hub_risorse_eyebrow, hub_risorse_h1_main, hub_risorse_h1_emphasis, hub_risorse_intro | `/risorse/` (Page WP 2813) | **MIGRA tutti** → Page 2813 metabox |
| `template-parts/page-costi-e-consulenze-hub.php` | (nessun saltelli_option specifico — verificare) | `/costi-e-consulenze/` (Page WP 2695) | **VERIFY**: nessun field SCF migrato verso Page 2695 — Wave 4.7.fix.3 scope NON include questa pagina |
| `archive-avvocato.php` | archive_avvocato_eyebrow, archive_avvocato_h1_main, archive_avvocato_h1_emphasis, archive_avvocato_intro | `/chi-siamo/team/` (CPT archive — NESSUNA Page WP corrispondente) | **RESTA in Theme Options** (no Page WP target possibile) |
| `archive-saltelli_caso.php` | archive_caso_eyebrow, archive_caso_h1_main, archive_caso_h1_emphasis, archive_caso_intro | `/chi-siamo/casi-rappresentativi/` (CPT archive — NESSUNA Page WP) | **RESTA in Theme Options** |
| `taxonomy-tipo-area.php` | taxonomy_tipoarea_eyebrow, taxonomy_tipoarea_subtitle_template | `/aree-di-pratica/{cluster}/` (term — NESSUNA Page WP) | **RESTA in Theme Options** (term, non page) |
| `header.php` | brand_payoff, studio_telefono_pubblico, whatsapp_message_default | (globale, ogni pagina) | **RESTA** (globale) |
| `footer.php` | studio_indirizzo_via, studio_cap_citta, studio_quartiere, colophon_indirizzo, colophon_orari, studio_telefono_pubblico, studio_email, studio_pec, studio_piva, studio_ordine_avvocati, brand_statement_short, footer_credit_text, footer_credit_url, footer_newsletter_enabled, footer_newsletter_provider, social_instagram, social_linkedin, social_facebook, social_twitter, footer_tier1_aree | (globale, ogni pagina) | **RESTA** (globale) |
| `template-parts/trust-bar.php` | trust_signal_1-4_label, trust_signal_1-4_caption (8 field) | (globale, multipla) | **RESTA** (globale) |
| `template-parts/page-contatti.php` | studio_indirizzo_via, studio_cap_citta, studio_quartiere, studio_orari_settimana, studio_orari_sabato, studio_telefono_pubblico, studio_email | `/contatti/` (Page WP 23) | **RESTA** (Studio Info globale; il content `/contatti/` è già editabile via group_contatti_v1) |
| `template-parts/mini-form.php` | cta_default_label | (globale CTA) | **RESTA** |
| `404.php` | studio_telefono_pubblico | (globale) | **RESTA** |
| `template-parts/page-lo-studio.php` | (usa `get_field()` direct su page 2811) | `/chi-siamo/lo-studio/` (Page WP 2811) | **GIÀ post-attached** via group_lo_studio_v1 (page_slug == lo-studio) — fuori scope Wave 4.7.fix.3 |

---

## Note pattern

### get_field vs saltelli_option

- `saltelli_option(name, default)` → wrapper di `get_field(name, 'option')` che reade dalla options page
- `saltelli_field(name, post_id, default)` → wrapper di `get_field(name, $post_id)` che reade dal post meta

Quando un field migra da options page → Page metabox, **il template deve cambiare** da `saltelli_option('foo')` a `saltelli_page_field('foo')` (helper nuovo) o a `get_field('foo', $page_id)`.

### Helper saltelli_page_field design

Nuovo helper in `inc/helpers.php`:

```php
function saltelli_page_field($field, $default = '', $page_id = null) {
    if (!function_exists('get_field')) return $default;

    if ($page_id === null) {
        $page_id = (is_front_page() && get_option('show_on_front') === 'page')
            ? (int) get_option('page_on_front')
            : get_queried_object_id();
    }
    if (!$page_id) return $default;

    $val = get_field($field, $page_id);
    if ($val !== '' && $val !== null && $val !== false) return $val;

    // Fallback transitorio Phase 2-3: legge da Theme Options (legacy compat).
    // Rimosso in Phase 4 quando migration data è confermata.
    if (function_exists('saltelli_option')) {
        return saltelli_option($field, $default);
    }
    return $default;
}
```

Pattern decision: il helper espone `is_front_page()` lookup integrato per consentire ai template "front-page.php" di usare `saltelli_page_field('hero_headline')` senza dover passare manualmente l'ID Page Homepage.

### Repeater press_outlets — scope migration

Field SCF repeater attualmente in `options_press_outlets` (count) + sub-keys serializzate. Stato DB staging: empty (0 rows). Migration script gestisce il valore root come scalar; le sub-keys (se ci fossero) sono migrate via pattern `options_press_outlets_<i>_<sub>` → `<page_id>_press_outlets_<i>_<sub>` postmeta. SCF rebuild il repeater alla scrittura successiva.

### casi_rappresentativi_home — scope migration

post_object multi field. Empty su staging (fallback all'helper saltelli_homepage_cases() → 6 most recent CPT). Migra a Page 17 metabox; quando Elena selezionerà casi via UI metabox, viene salvato come post_meta multi-value (probabile serialized array).

### studio_foto_facciata — scope migration

Image field, valore `2211` (attachment ID). Migra a Page 17 metabox. La image-rendering pipeline in front-page.php legge `is_array($studio_foto)` ma con SCF Pro array return format. Verificare che dopo migration, `get_field('studio_foto_facciata', 17)` ritorni array con url/alt come pre-migration.

---

*Phase 1 audit · saltelli-wp · 2026-05-08*
