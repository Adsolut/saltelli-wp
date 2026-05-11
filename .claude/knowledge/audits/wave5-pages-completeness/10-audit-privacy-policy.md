# Audit completezza — Privacy Policy (ID 2741) · `/privacy-policy/` · `page.php` (default fallback)

**Render:** `page.php` → ramo `else` (default fallback): hero (breadcrumb + `<h1>` = `get_the_title()`) + `<div class="sl-page__prose">` `the_content()`. Confermato via curl: body class `page-id-2741`, `page-template-default`, `sl-page__prose`. `_wp_page_template` vuoto. `post_content` ≈ 1402 char. **NON in `SALTELLI_SCF_ONLY_PAGES`** → editor Gutenberg attivo.
**Group SCF attuale:** nessuno (e non serve).
**Priorità:** **GIÀ OK — niente da fare. NON proporre migrazione (contenuto legale, gestito da Iubenda).**

## Elementi frontend → sorgente

| Elemento frontend | Sorgente | Tipo | Decisione |
|---|---|---|---|
| Breadcrumb | `saltelli_get_breadcrumb_chain()` | — | ⏸ auto |
| H1 = titolo pagina | `get_the_title()` | core | ✅ editabile (campo Titolo) |
| Corpo (testo dell'informativa privacy) | `the_content()` | core | ⏸ **gestito da Iubenda** (il body è uno shortcode/HTML Iubenda — NON riscriverlo a mano; si aggiorna dal pannello Iubenda) |

## Decisione

**0 field SCF da aggiungere.** Contenuto legale generato e mantenuto da Iubenda — non è "content editoriale" da portare in SCF. Lasciare la pagina così com'è. **Non proporre migrazione.**
**Stima implementation:** ~0 min (skip).
