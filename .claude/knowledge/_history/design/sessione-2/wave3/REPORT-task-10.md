# REPORT — Wave 3 · Task 10 · 404 editoriale

**Branch:** `feat/wave3-task-10`
**Commit:** `2677c04`
**Status:** ✅ DONE — pushed to `origin/feat/wave3-task-10`
**Smoke:** HTTP 404 + 43 unique `.sl-404__*` classes + no PHP errors
**Date:** 2026-05-01

## Scope

Sostituire il vecchio `404.php` (3 bottoni essenziali) con il layout editoriale sacro descritto in `saltelli-s2-404.jsx`:

- Hero "errore quietato" 5fr/7fr con eyebrow mono, H1 Playfair clamp(72-140px), drop-cap "L" italic.
- 3-card recovery (Home, Search, Contatto diretto) su `var(--surface)`.
- Sezione "Forse cercavi" con 5-7 aree (tier-1 first) usando il pattern `.sl-area / .sl-area--tier1`.
- 3 articoli recenti con thumbnail + categoria + meta (data · read).
- CTA finale "Prenota una consulenza gratuita."

## Files modificati (scope ristretto)

| File | Δ | Note |
|---|---|---|
| `wp-content/themes/saltelli/404.php` | +229 / −29 | Riscritto integralmente sul layout JSX. `status_header(404)` + `nocache_headers()` preservati. |
| `wp-content/themes/saltelli/assets/css/sections.css` | +289 | Solo blocco `WAVE3 TASK 10 (404) BEGIN/END`, namespace `.sl-404__*`, no collisione con altri task. |

NO-TOUCH rispettati: `tokens.css`, `functions.php`, `style.css`, `page.php`, `home.php`, `single-*`, `taxonomy-*`.

## Implementazione — punti chiave

1. **Hero `errore quietato`** — eyebrow `Errore 404 · Pagina non trovata` in `var(--accent)`, H1 `font-display weight 400 letter-spacing -0.035em`, drop-cap "L" `font-size 96px float left` con tagline italic 22px lato destro. Grid 5fr/7fr collassa a 1fr sotto 1024px.

2. **Recovery 3-col** (`§ 01 — Cosa puoi fare`):
   - Card 1 → CTA primary `Vai alla home`.
   - Card 2 → search form con `<label for="search-404" class="screen-reader-text">` (a11y richiesto dal task) e submit `.sl-btn`.
   - Card 3 → telefono e WhatsApp, `tel:` + `wa.me/` con prefilled message. Stack `.sl-404__contact-row` con border-top divider.

3. **Suggest aree** (`§ 02 — Forse cercavi`) — query `competenza` ordinata `is_tier_1_focus DESC, menu_order ASC, title ASC`, limit 6. Usa `.sl-area` / `.sl-area--tier1` esistenti per riuso component pattern. CTA finale `Tutte le 19 aree` allineato a destra.

4. **Articoli recenti** (`§ 03 — Articoli recenti`) — `wp_query` post 3 ultimi pubblicati con thumbnail (fallback gradient placeholder), categoria primaria, reading time da meta `reading_time` o calcolo fallback (220 wpm).

5. **CTA finale** — H2 clamp(56-96px), em accent bronzo, italic Playfair text 22px, btn primary `Prenota gratuita` → `/contatti/`.

6. **Schema JSON-LD** — `WebPage` con `isPartOf` `WebSite` homepage. Yoast coabitation: emit gated da `!saltelli_seo_plugin_active()` (skip se WPSEO_VERSION attivo). In locale Yoast è attivo ⇒ il WebPage è correttamente soppresso, l'organization `LegalService` resta visibile.

## Smoke test

```
$ curl -s -o /dev/null -w "HTTP %{http_code}" http://localhost:8080/pagina-non-esiste-test-404/
HTTP 404

$ curl -s http://localhost:8080/pagina-non-esiste-test-404/ | grep -oE 'sl-404__[a-z-]+' | sort -u | wc -l
43

# PHP errors: 0
# search-404 label: present (sr-only)
# tier-1 areas: 3 first (Tributario · Lavoro · Famiglia)
```

## Note operative

- Durante l'implementazione ho lavorato in parallelo con altri agenti (Task 6/7/8/9 sui rispettivi lock). Per garantire scope ristretto, prima del commit ho:
  1. `git checkout main -- sections.css` per scartare i blocchi degli altri task presenti nel working tree;
  2. Rieseguito l'awk-injection del solo blocco TASK 10;
  3. Stage selettivo (`git add 404.php sections.css`).
- Branch corretto da una sequenza di switch automatici tramite `git branch -f feat/wave3-task-10 <sha>` per garantire la naming policy del prompt.

## Output

- Branch `feat/wave3-task-10` pushato su `origin` (PR pronta).
- `/tmp/saltelli-agents/task-10.lock` rimosso a fine task.
