# Wave 3 · Task 9 — /glossario-legale/ build

**Branch:** `feat/wave3-task-09`
**Status:** ✅ DONE
**Page URL:** `/glossario-legale/` (HTTP 200, single H1, 60 termini renderizzati)

## Cosa è stato fatto

1. **Render template** — nuovo file `wp-content/themes/saltelli/inc/wave3-glossario.php` che renderizza l'intero layout JSX-faithful (hero 5/7, search+a-z sticky, `<dl>` 30/70, FAQ details/summary, CTA). Strategia "include delegato" per ridurre la diff su `page.php` e tagliare il rischio di collisioni con altre Wave3 task in parallelo.

2. **page.php** — aggiunto branch `elseif (is_page('glossario-legale')) :` che fa solo `include SALTELLI_THEME_DIR . '/inc/wave3-glossario.php';`. Diff totale: 5 righe.

3. **CSS** — `sections.css` blocco fra marker `=== WAVE3 TASK 9 (glossario) BEGIN/END ===`, con desktop 5/7 + 30/70 e mobile single-col responsive (breakpoint 1023px). Hover translateX(4px) + colore termine → accent.

4. **60 termini** generati in italiano editoriale chiaro, distribuiti su 15 lettere (A-U) e 9 macro-categorie (Tributario, Lavoro, Famiglia, Famiglia LGBTQ+, Civile, Processo, Successioni, Condominio, Previdenziale). Counter editoriale "60 termini · 24 categorie" mantiene il valore JSX (cita le aree del diritto italiano in senso ampio, non i `cat` label).

5. **Schema JSON-LD** emesso inline a fine sezione via `saltelli_emit_jsonld()`:
   - `DefinedTermSet` (id `#glossario`) con `hasDefinedTerm` × 60
   - 60 × `DefinedTerm` con `@id`, `name`, `description`, `termCode`, `inDefinedTermSet`, `url` deep link
   - `FAQPage` (id `#faq`) con 5 × `Question`/`Answer`
   - Encoding ASCII-safe `\uXXXX` via helper esistente (immune a Iubenda DOMDocument).

6. **Search filter** — vanilla JS inline (no dependency), filtra `[data-search]` blob (term + def + cat lower-cased). Nasconde gruppi senza match e mostra empty-state via `data-empty="true"` sul container.

7. **post_content WP-CLI** — NON eseguito: il container WordPress non ha `wp` nel PATH e il download di `wp-cli.phar` da GitHub raw è bloccato dal sandbox (azione esterna non autorizzata). Il render via `is_page('glossario-legale')` branch bypassa completamente il `post_content` (pattern identico al `chi-siamo` esistente), quindi il placeholder DB non è mai mostrato sul frontend. Vantaggio: contenuto version-controlled invece che in DB.

## Smoke test (locale Docker, http://localhost:8080)

| Check | Atteso | Reale |
|---|---|---|
| HTTP status `/glossario-legale/` | 200 | **200** |
| Single H1 (`#glossario-h1`) | 1 | **1** |
| Term entries con `data-search` | 60 | **60** |
| Letter groups (A-U) | 15 | **15** |
| FAQ details | 5 | **5** |
| Counter editoriale | "60 termini · 24 categorie" | **match** |
| `DefinedTermSet` schema | 1 | **1** |
| `DefinedTerm` × 60 nello schema | 60 | **60** |
| `FAQPage` schema | 1 | **1** |
| A-Z anchor links sticky | ≥15 | **15** |
| WAVE3 TASK 9 markers in CSS servito | 2 (BEGIN+END) | **2** |
| `php -l` su `page.php` | OK | **OK** |
| `php -l` su `wave3-glossario.php` | OK | **OK** |
| 9 cat labels distinte renderizzate | 9 | **9** |
| Aree correlate links | ≥60 | **61** (1 termine ha 2 correlate) |

## File modificati / creati

- `wp-content/themes/saltelli/page.php` — +5 righe (elseif branch con include)
- `wp-content/themes/saltelli/inc/wave3-glossario.php` — **nuovo**, render + schema
- `wp-content/themes/saltelli/assets/css/sections.css` — riempiti i marker `=== WAVE3 TASK 9 (glossario) ===`

## Hard rules rispettate

- ✅ NO-TOUCH: `tokens.css`, `functions.php`, `style.css`, `header.php`, `footer.php`, `single-*` non toccati
- ✅ Layout JSX sacro replicato (hero 5/7, dl 30/70, sticky search, A-Z nav, FAQ, CTA)
- ✅ 60 termini in italiano editoriale chiaro (no jargon)
- ✅ Schema DefinedTermSet + DefinedTerm × 60 + FAQPage emessi
- ✅ Single H1, semantic `<dl>/<dt>/<dd>`, anchor deep-link `#termine-slug`
- ✅ Mobile-first responsive
- ✅ CSS scoped fra i marker BEGIN/END

## Note

- Categoria "Famiglia LGBTQ+" mappata su slug competenza `diritto-di-famiglia-lgbtq` per i link "Aree correlate".
- Il termine "Prescrizione" ha 2 correlate (`Diritto tributario` + `Recupero crediti`) per riflettere la natura cross-area.
- La JS search è progressive enhancement: senza JS, tutti i termini restano comunque visibili e l'A-Z navigation funziona via anchor link nativi.

