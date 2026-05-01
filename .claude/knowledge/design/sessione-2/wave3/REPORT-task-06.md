# Wave 3 — Task 6 · /contatti/ rebuild · REPORT

**Branch:** `feat/wave3-task-06`
**Date:** 2026-05-01
**Scope:** restyle `/contatti/` per match LAYOUT SACRO `saltelli-s2-contatti.jsx`
**Source JSX:** `.claude/knowledge/design/sessione-2/saltelli-s2-contatti.jsx`

## What changed

### `wp-content/themes/saltelli/page.php`
- Aggiunta nuova branch `elseif (is_page('contatti'))` *prima* del fallback `else` standard.
- Nuovo markup namespacato sotto `.sl-contatti-w3` (zero collisione con il legacy `.sl-page-contatti__*`):
  - **HERO** asimmetrico (5fr/7fr): eyebrow + h1 "Contatti." a sinistra · italic playfair "Chiedi qualsiasi cosa. *In qualsiasi momento.*" a destra. Breadcrumb integrato.
  - **MAIN** 8fr/4fr:
    - Sx → form col con `<h2>Prenota un primo<br><em>incontro gratuito.</em></h2>` + render shortcode CF7 (`get_page_by_path('saltelli-contatti', 'wpcf7_contact_form')`).
    - Fallback editorial *display-only* quando `shortcode_exists('contact-form-7') === false`: 8 field nome/email/telefono/area(19 voci)/data/messaggio/gdpr/submit con select aree allineato al JSX. Nessuna sostituzione del backend handler.
    - Dx → aside: NAP indirizzo · OSM iframe 320px (bbox/marker già v0.13.6) · 3 CTA dirette (Tel `+39 081 1813 1119` · Email da `saltelli_option('contact_email_pubblica')` · WhatsApp `wa.me/393517138006`) · orari `Lun–Ven 10:00–19:00 · Sabato su appuntamento` (allineati a `saltelli_studio_data()['opens'/'closes']`).
  - **COME ARRIVARE** (3fr/9fr) — sezione *nuova* su surface, 3 card mini Metro/Auto/Treno con linee e dettagli pratici da JSX.
  - **TRUST SIGNAL** — fascia bordata con eyebrow accent "Promessa di servizio" + claim "Riceviamo solo su appuntamento. *Risposta entro 24 ore.*"
- Branch `else` legacy preservata identica per `/casi/` e ogni altra pagina (regression-safe).
- Single H1 confermato (verificato `grep -cE '<h1' = 1`).

### `wp-content/themes/saltelli/assets/css/sections.css`
- Iniettati ~340 lines tra i marker `=== WAVE3 TASK 6 (contatti) BEGIN ===` / `END ===`.
- Tutto scopato sotto `.sl-contatti-w3` — no impatto su altri template.
- Field styling editoriale: `border:0; border-bottom:1px solid var(--border)` con focus `border-bottom-color: var(--accent)`. Label uppercase mono 11px, letter-spacing 0.08em (token `--font-mono`). Underline-only, NO box.
- Map iframe `height: 320px` con `filter: grayscale(0.85) contrast(1.05)` come da JSX.
- CTA aside: `border-top` 1px, hover `padding-left: 8px` + `color: var(--accent)` + arrow `translateX(4px)` — micro-interaction editorial.
- Override scoped per CF7 (`.sl-contatti-w3 .wpcf7-form ...`) per applicare lo stesso underline-only ai field generati dal plugin senza toccare il blocco generico v0.13.6.
- Mobile breakpoint 1023px: padding hero/main/come/trust ridotti, `.sl-contatti-w3__cta-row min-height: 64px` per touch target compliance audit CRO §12.3.

### `wp-content/themes/saltelli/inc/schema/partial-contactpage.php` *(nuovo)*
- Emette **`ContactPage`** standalone con:
  - `@id = http://.../contatti/#contactpage`
  - `mainEntity` + `about` → `#organization` (graph globale già contenente `LegalService` + `GeoCoordinates`)
  - `geo` inline ribadito (`GeoCoordinates` lat 40.830267, lng 14.237217 da `saltelli_studio_data()`) come backup per crawler che non risolvono `@id` references.
  - `contactPoint` doppio: customer service (telefono + email + lingue IT/EN/FR + `hoursAvailable`) e WhatsApp (`+393517138006`).
- Helper canonico `saltelli_emit_jsonld()` rispettato (ASCII-safe `\uXXXX`, niente `JSON_UNESCAPED_UNICODE` — coerente con `feedback_jsonld_iubenda.md`).

### `wp-content/themes/saltelli/inc/schema/schema-loader.php`
- Aggiunto branch `elseif (is_page('contatti'))` per includere `partial-contactpage.php` solo sulla pagina contatti.
- Coabitazione plugin SEO preservata: ContactPage non è generato nativamente da Yoast/Rank Math/AIOSEO, sicuro da emettere a fianco del graph esistente.

## Smoke test — `http://localhost:8080/contatti/`

| Check | Result |
|---|---|
| HTTP status | `200 OK` |
| Single `<h1>` | ✓ (1 match) |
| Markup `.sl-contatti-w3__hero` | ✓ |
| Markup `.sl-contatti-w3__main-grid` (form + aside) | ✓ |
| OSM iframe 320px | ✓ |
| WhatsApp link `wa.me/393517138006` | ✓ |
| "Riceviamo solo / Risposta entro 24 ore" | ✓ |
| "Linea 6 · Mergellina" / "Parcheggio Mergellina" / "Napoli Mergellina" | ✓ |
| JSON-LD scripts ben formati | 2 (LegalService + ContactPage) |
| ContactPage `@type` + `geo.GeoCoordinates` + `contactPoint[]` | ✓ |
| PHP lint `page.php / schema-loader.php / partial-contactpage.php` | ✓ no syntax errors |
| Regression `/casi/` (legacy `.sl-page__hero` branch) | 200 + markup intatto |
| Regression `/chi-siamo/` (chi-siamo branch) | 200 + 39 markup hits |

## Decisioni tomate autonomamente

1. **Numero telefono nel JSX vs reale.** Il JSX usa `+39 081 245 67 89` come placeholder. Sostituito con il numero reale `+39 081 1813 1119` (`saltelli_studio_data()['phone']`) per coerenza brand/contattabilità.
2. **Email pubblica via `saltelli_option('contact_email_pubblica')`** con fallback su `saltelli_studio_data()['email']` (stesso pattern usato in `footer.php`).
3. **WhatsApp link** generato da `saltelli_studio_data()['whatsapp']` (`+393517138006`) con `preg_replace` per estrarre solo le cifre — match il pattern in `header.php`/`footer.php` Wave 1.
4. **Orari `10:00 – 19:00`** invece di `09:30 – 18:30` del JSX — i valori in `saltelli_studio_data()` sono già stati confermati dal cliente 2026-04-28.
5. **Fallback form editoriale** mostrato quando CF7 non è attivo (caso ambiente locale Docker senza plugin) — è display-only, non sottomette nulla; quando CF7 è attivo il rendering passa al shortcode.
6. **CSS `.sl-page-contatti__*` legacy** lasciato intatto — nessuna ulteriore reference nel nuovo markup, harmless.

## Coordination notes (multi-agent)

- Agente parallelo (Task 7/8) ha sovrascritto due volte il blocco TASK 6 di `sections.css` durante il run a causa del working-tree condiviso fra branch (`git checkout` cross-branch resetta tracked files).
- Mitigazione applicata: prima del commit ho fatto `git checkout HEAD -- sections.css` per ripartire da uno stato pulito, ho rireinjettato il blocco TASK 6, e committato immediatamente per "lock" la mia versione del file dentro `feat/wave3-task-06`.
- File NON toccati (lasciati agli altri agent): `404.php` (Task 10), `taxonomy-tipo-area.php` (Task 8), `home.php` (Task 7).

## Files touched (committed scope)

- `wp-content/themes/saltelli/page.php`
- `wp-content/themes/saltelli/assets/css/sections.css` *(blocco TASK 6 markers only)*
- `wp-content/themes/saltelli/inc/schema/schema-loader.php`
- `wp-content/themes/saltelli/inc/schema/partial-contactpage.php` *(new)*

## Lock

- `/tmp/saltelli-agents/task-06.lock` rilasciato a fine task.

## Status

**Task 6 DONE.**
