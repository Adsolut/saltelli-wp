# Phase 4 — Template refactor

## Cosa è stato fatto

Refactor minimale rispetto al prompt originale (che assumeva 7 nuovi template-parts dedicati). La realtà:

- `page-contatti.php` — già SCF-only, no `the_content()`. Nessuna modifica.
- `page-faq.php` — già SCF-only (renderizza CPT). Nessuna modifica.
- `page-info-shared.php` — aveva `the_content()` come priority-2 fallback (linea 93). **RIMOSSO**.

## Diff `page-info-shared.php`

**Pre-refactor** (linee 84-98):
```php
<section class="sl-info-page__body">
    <div class="sl-mono sl-info-page__body-eyebrow"><?php esc_html_e('§ 01 — Approfondimento', 'saltelli'); ?></div>
    <div class="sl-info-page__prose">
        <?php
        // Priority 1: ACF body_content (Wave 2 popolato).
        // Priority 2: get_the_content() (post_content WP nativo).
        // Priority 3: empty (richiedi-preventivo non ha body editorial).
        if ($body_content !== '') {
            echo wp_kses_post($body_content);
        } elseif (get_the_content() !== '') {
            the_content();
        }
        ?>
    </div>
</section>
```

**Post-refactor**:
```php
<?php if ($body_content !== '') : ?>
<section class="sl-info-page__body">
    <div class="sl-mono sl-info-page__body-eyebrow"><?php esc_html_e('§ 01 — Approfondimento', 'saltelli'); ?></div>
    <div class="sl-info-page__prose">
        <?php
        // Wave 4.7.fix.4 STRATEGY A: source unica = SCF body_content.
        // Pre-fix.4: aveva fallback the_content() WP nativo per pagine senza
        // body_content popolato. Post-fix.4: post_content è stato bonificato +
        // Gutenberg disabled, una sola sorgente di verità per pagina.
        // Se body_content è vuoto, l'intera sezione __body è skippata (silent).
        echo wp_kses_post($body_content);
        ?>
    </div>
</section>
<?php endif; ?>
```

## Smoke test pre/post diff

7 pagine, snapshot HTML pre + post refactor, diff con sed cleanup di commenti HTML e nonce variabili:

| URL | Diff lines | Tipo |
|---|---|---|
| `/contatti/` | 1 line | Solo `wpa_field_info` nonce (anti-spam plugin) |
| `/risorse/domande-frequenti/` | 1 line | Solo `wpa_field_info` nonce |
| `/risorse/guide-gratuite/` | 3 lines | Solo indentazione `<section>` (4 spaces diff) + nonce |
| `/costi-e-consulenze/come-lavoriamo/` | 3 lines | idem |
| `/costi-e-consulenze/prima-consulenza/` | 3 lines | idem |
| `/contatti/lavora-con-noi/` | 3 lines | idem |
| `/costi-e-consulenze/richiedi-preventivo/` | 3 lines | idem |

**Tutti i diff sono whitespace + plugin nonce. Zero perdita di contenuto visibile.**

Per `/costi-e-consulenze/richiedi-preventivo/` specificamente: pre-refactor il content veniva da `the_content()` (post_content), post-refactor da `wp_kses_post($body_content)` (SCF migrato in Phase 3). HTML output identico perché body_content è identico al post_content originale (migration verbatim).

## OPcache reload obbligatorio

Lesson learned Wave 4.7.fix.3 (CLAUDE.md § "Lesson learned OPcache stale"): post-edit template PHP, OPcache deve essere ricaricato per evitare frontend stale.

```sh
ssh deploy@178.62.207.50 "sudo systemctl reload php8.2-fpm && sudo -u www-data wp cache flush --path=/var/www/saltelli"
```

Eseguito post-sync rsync. ✓

## Helper `saltelli_page_has_scf_content` — NON implementato

Il prompt suggeriva di implementare un helper dinamico per detect SCF attachment. Decisione:
- Phase 5 disable Gutenberg usa lista hardcoded `SALTELLI_SCF_ONLY_PAGES = [...]` (12 IDs)
- Il dispatcher `page.php` route già correttamente via `is_page($slug)` switch
- Aggiungere helper dinamico = complessità senza beneficio attuale

Decisione: skip. Se in futuro serve un check dinamico (es. per Gutenberg disable condizionale su tutti Pages con SCF metabox attached automaticamente), si può aggiungere allora.

## Page.php default fallback — NON modificato

`page.php` linea 88 chiama `the_content()` come default fallback per Pages WP non in switch (es. privacy, cookie, note-legali, pagine custom future). Questo comportamento è **corretto** — per Pages senza SCF metabox, render standard WP. Nessuna modifica.

---

*Phase 4 refactor minimal · frontend invariato ✓ · 2026-05-10*
