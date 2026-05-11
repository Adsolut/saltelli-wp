# Audit completezza — Prenota un appuntamento (ID 2714) · `/prenota-appuntamento/` · `page.php` (default fallback)

**Render:** `page.php` → ramo `else` (default fallback): hero (breadcrumb + `<h1>` = `get_the_title()`) + `<div class="sl-page__prose">` `the_content()`. Confermato via curl: body class `page-id-2714`, `page-template-default`, `sl-page__prose`. `_wp_page_template` vuoto. `post_content` ≈ 789 char (contenuto reale presente). **NON è in `SALTELLI_SCF_ONLY_PAGES`** → editor Gutenberg attivo.
**Group SCF attuale:** nessuno (e non serve).
**Priorità:** **GIÀ OK — niente da fare.**

## Elementi frontend → sorgente

| Elemento frontend | File:linea (page.php) | Sorgente | Tipo | Decisione |
|---|---|---|---|---|
| Breadcrumb | page.php:~76-90 | `saltelli_get_breadcrumb_chain()` | — | ⏸ auto |
| H1 = titolo pagina | page.php:~92 | `get_the_title()` | core | ✅ editabile (campo Titolo della Page in Gutenberg) |
| Corpo (testo / eventuale embed di prenotazione) | page.php:~95 | `the_content()` | core | ✅ editabile (editor Gutenberg della Page 2714) |

## Decisione

**0 field SCF da aggiungere.** La pagina usa il fallback WP standard: title + content via Gutenberg. Tutto ciò che è visibile è già editabile da Elena (titolo + corpo). Se in futuro si vuole un layout strutturato (hero split eyebrow/H1/lede + aside + body) come le altre pagine "info-shared", si potrebbe migrarla al template `page-info-shared.php` + `group_info_shared_v1` — ma è una scelta di design, non un gap di completezza. **Non proporre migrazione.**
**Stima implementation:** ~0 min (skip).
