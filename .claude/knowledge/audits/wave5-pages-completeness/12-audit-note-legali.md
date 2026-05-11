# Audit completezza — Note legali (ID 2743) · `/note-legali/` · `page.php` (default fallback)

**Render:** `page.php` → ramo `else` (default fallback): hero (breadcrumb + `<h1>` = `get_the_title()`) + `<div class="sl-page__prose">` `the_content()`. `page-template-default`, `sl-page__prose`. `_wp_page_template` vuoto. `post_content` ≈ 1319 char. **NON in `SALTELLI_SCF_ONLY_PAGES`** → editor Gutenberg attivo.
**Group SCF attuale:** nessuno (e non serve).
**Priorità:** **GIÀ OK — niente da fare.**

## Elementi frontend → sorgente

| Elemento frontend | Sorgente | Tipo | Decisione |
|---|---|---|---|
| Breadcrumb | `saltelli_get_breadcrumb_chain()` | — | ⏸ auto |
| H1 = titolo pagina | `get_the_title()` | core | ✅ editabile (campo Titolo) |
| Corpo (testo "Note legali" — P.IVA, foro competente, hosting, ecc.) | `the_content()` | core | ✅ editabile (editor Gutenberg della Page 2743) — testo redatto a mano, non da plugin |

## Decisione

**0 field SCF da aggiungere.** Pagina di testo legale interamente editabile dall'editor Gutenberg standard. Nessun pattern strutturato necessario. **Non proporre migrazione.** (Eventualmente: i dati come P.IVA/email potrebbero referenziare i `saltelli_option('studio_*')` globali invece di essere ripetuti a mano nel body — micro-coerenza, fuori scope.)
**Stima implementation:** ~0 min (skip).
