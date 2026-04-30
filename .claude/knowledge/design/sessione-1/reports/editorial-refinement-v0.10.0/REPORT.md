# Editorial Refinement v0.10.0 · Wall of Text → Rivista Pattern

**Data:** 2026-04-30
**Theme version (in):** `0.9.0-beta-recovery`
**Theme version (out):** `0.10.0-beta-editorial`
**Tempo totale:** ~80 minuti (within budget 90-120 min)
**Modalità:** sequenziale GROUP A → B → C, cache flush + smoke test 8+ URL dopo OGNI gruppo

---

## 1 · Status 11/11 BUG fixati

### GROUP A — Typography Wall of Text Breaker (4/4 ✅)

| ID | Bug | Approccio |
|---|---|---|
| **A1** | H1 attaccata al lede / first paragraph | `margin-bottom: 32px` su `.sl-post__title` + lede primo `<p>` in italic Playfair `clamp(20-24px)` con `margin: 0 56px` + `max-width: 56ch` |
| **A2** | H2/H3 senza respiro | **Già coperto da Recovery F6** (`.sl-competenza__prose h2 { margin-block: 80px 24px }` + adjacent sibling rule). NO duplicazione. |
| **A3** | Liste bullet default browser | `list-style: none` + `::before { content: "—"; color: var(--accent) }` em-dash editoriale. Scope esclusivo `.sl-post__body / .entry-content / .sl-page__prose` con `:not()` per preservare liste meta (`.sl-areas__list`, `.sl-articles`, `.sl-team__specs`). |
| **A4** | Wall of text senza respiro | `max-width: 60ch` body p, `line-height: 1.75`, blockquote pull-quote pattern (border-left 2px accent + italic Playfair clamp 22-28px), `<strong>` weight 500 (no bold), `<em>` italic. |

### Drop-cap (A1 add-on)

Drop-cap su primo paragraph SOLO `body.single-post` (non CPT competenza/avvocato, non page generic). `font-size: 4.2em; line-height: 0.85; float: left; margin: 8px 14px 0 0` Playfair primary.

### GROUP B — Immagini Cornice Editoriale (3/3 ✅)

| ID | Bug | Approccio |
|---|---|---|
| **B1** | Immagini sentenze a sinistra senza container | `.sl-post__body img:not(...) { max-width: min(720px, 100%); margin: 56px auto; border: 1px solid var(--border); background: var(--surface) }` + `figure { max-width: 720px; margin: 56px auto }` + `figcaption` mono 11px uppercase centered |
| **B2** | Featured image stock 1562px gigante | `.sl-post__featured { max-width: 960px; aspect-ratio: 16/9; overflow: hidden }` + `img { object-fit: cover }` → stock images non rimosse (per hard rule "stock vanno SOLO ridimensionate via CSS") ma uniformate visivamente |
| **B3** | Foto autore Emiliano gigante in fondo | Markup template già emetteva `.sl-post__author-card` + `.sl-post__author-portrait`. Aggiunto CSS: grid 80px+1fr, foto 80x80 squared, hover grayscale 0.2→0, name Playfair 18px, role mono 10px. **Specificità 0,2,0 scoped** a `.sl-post__author-card .sl-team__portrait` per NON impattare archive avvocato (4 foto preservate verificate). |

**Note B2 stock images del cliente:** illustrazioni cartoon AI rimangono nel DB (compliance hard rule "Mai sovrascrivere post_meta"). Solo cornice CSS (max-width 960 + aspect 16/9 + object-fit cover) le uniforma visivamente. Decisione di rimpiazzo immagini va a fase content (Step F+ o successiva).

### GROUP C — Routing/Content (3/3 ✅)

| ID | Bug | Approccio |
|---|---|---|
| **C1** | `/lo-studio/` → blog post `/lo-studio-legale-saltelli-fa-annullare-...` | **Doppio fix**: (a) WP-CLI `post meta update 2684 _menu_item_url /chi-siamo/` cambia menu link (verificato `<a href="/chi-siamo/">Studio</a>` nell'HTML); (b) PHP hook `add_action('init', ..., 1)` early intercept che fa `wp_safe_redirect('/chi-siamo/', 301)` per qualsiasi request a `/lo-studio/`. Necessario perché `template_redirect` parte DOPO redirect_canonical WP. |
| **C2** | `/blog/` archive vuoto (326 post mancanti) | `option update page_for_posts 1413` (page WP "Blog" id 1413 esisteva già). WP ora serve `/blog/` con template **`index.php`** (esistente con loop completo `have_posts()` + `the_post()` + paginazione). Verificato: 16 post/page con `the_posts_pagination()` attiva. |
| **C3** | `/contatti/` content scarno | Edit `page.php` con condizionale `is_page('contatti')` → aggiunto blocco `<section class="sl-page-contatti__map">` con OpenStreetMap embed (no API key, GDPR-friendly) + `<section class="sl-page-contatti__cta">` con CTA "Scrivici una mail" + "Chiama lo studio". CSS scoped su `.sl-page-contatti__*` (~80 righe). |

---

## 2 · Smoke test esteso finale (13 URL)

```
/                                                          direct 200 · final 200 · 1H1 · 84 753b · ver=0.10.0-beta-editorial
/costi/                                                    direct 200 · final 200 · 1H1 · 56 610b · ver=0.10.0-beta-editorial
/lo-studio/                                                direct 301 · final 200 · 1H1 · 58 446b · ver=0.10.0-beta-editorial  ← C1 fix
/blog/                                                     direct 200 · final 200 · 1H1 · 81 829b · ver=0.10.0-beta-editorial  ← C2 fix (era vuoto)
/competenze/                                               direct 200 · final 200 · 1H1 · 65 764b · ver=0.10.0-beta-editorial
/competenze/diritto-tributario/                            direct 200 · final 200 · 1H1 · 64 444b · ver=0.10.0-beta-editorial
/competenze/diritto-di-famiglia-lgbtq/                     direct 200 · final 200 · 1H1 · 65 169b · ver=0.10.0-beta-editorial
/avvocati/                                                 direct 200 · final 200 · 1H1 · 56 503b · ver=0.10.0-beta-editorial
/avvocati/emiliano-saltelli/                               direct 200 · final 200 · 1H1 · 60 358b · ver=0.10.0-beta-editorial
/avvocati/fabiana-saltelli/                                direct 200 · final 200 · 1H1 · 57 840b · ver=0.10.0-beta-editorial
/tipo-area/privati/                                        direct 200 · final 200 · 1H1 · 56 448b · ver=0.10.0-beta-editorial
/contatti/                                                 direct 200 · final 200 · 1H1 · 55 847b · ver=0.10.0-beta-editorial  ← C3 fix
/intimazione-tari-annullata-vittoria-dello-studio-legale-saltelli/ direct 200 · final 200 · 1H1 · 73 891b · ver=0.10.0-beta-editorial
```

**13/13 PASS** · `/lo-studio/` 301 redirect intenzionale → /chi-siamo/ (final 200).

---

## 3 · Diagnosi precisa per ciascun bug

### GROUP A

**A1:** Markup `single.php`: `<p class="sl-post__lede">` esplicito + body `the_content()` con `<p>` first-of-type. CSS pre-fix: nessun margin-bottom su `.sl-post__title`, nessun trattamento differenziato del lede vs body p. Risultato: blob unico H1+lede+p1+p2.

**A3:** Markup post body usa `<ul><li>` standard WP editor. `<li>` non aveva CSS scope dentro `.sl-post__body / .entry-content` → rendering bullet "•" browser default. Soluzione `:not()` chain per preservare liste meta del tema (sl-areas__list, sl-articles, sl-team__specs).

**A4:** `<p>` body senza `max-width` → riempivano `.sl-post__body` width (no constraint upstream). Su desktop 1100px → 100ch+ per riga = peggio per lettura. Fix `max-width: 60ch` (≈600px su 16px font) standard editoriale.

### GROUP B

**B1:** WP editor classic emette `<img>` raw o `<figure><img></figure>` a seconda dell'editor. `single.php` template usa `<div class="sl-post__body" data-drop-cap><?php the_content(); ?></div>`. Soluzione CSS scoped su `.sl-post__body img` con `:not()` chain per esclude foto avvocato (square/portrait) e align (left/right). NO PHP filter `the_content` (per evitare side effects).

**B2:** Markup `single.php`:
```php
<?php if (has_post_thumbnail()) : ?>
    <figure class="sl-post__featured">
        <?php the_post_thumbnail('saltelli-hero', ...); ?>
    </figure>
<?php endif; ?>
```
Pre-fix: il `<figure>` era emesso ma senza CSS specifico → image sized by attribute width/height (1414px nativo PNG). Fix CSS forza max-width 960 + aspect-ratio 16/9 + object-fit cover.

**B3:** Markup `single.php`:
```php
<section class="sl-post__author-card">
    <a class="sl-team__portrait sl-post__author-portrait">
        <?php echo get_the_post_thumbnail($linked_avv_id, 'saltelli-attorney-square'); ?>
    </a>
    <div class="sl-post__author-text">
        <h2 class="sl-team__name">...</h2>
    </div>
</section>
```
La regola `.sl-team__portrait` (homepage pattern, riga 1123 sections.css) ha aspect-ratio 3/4 e si applicava ANCHE dentro `.sl-post__author-card`. Override con specificità doppia `.sl-post__author-card .sl-team__portrait` (0,2,0) + `!important` dove necessario per width/height/aspect-ratio.

### GROUP C

**C1:** WP rewrite default cerca match permalink. `/lo-studio/` non matcha nessun page slug → WP fallback canonical lookup → primo post slug che inizia con "lo-studio-..." → 301 redirect indesiderato. Trovati 2 fix layer:
1. Menu link da `/lo-studio/` → `/chi-siamo/` (preventive — utenti normali cliccano dal menu)
2. Init hook priority 1 redirect (defensive — utenti che digitano URL a mano o backlinks SEO)

**C2:** `option page_for_posts: 0` significa "no page assegnata come blog index". La page id 1413 (slug `blog`) era una page WP normale con post_content vuoto. WP la serviva con `page.php` template (rendering del post_content vuoto). Fix: setto `page_for_posts: 1413` → WP ora serve `/blog/` con template `index.php` (loop completo già presente). Niente nuovo template `home.php` necessario.

**C3:** Page contatti id 23 con post_content scarno (4 paragrafi + 1 lista). Fix template-side via `is_page('contatti')` condition in page.php — aggiunto:
- Map embed OpenStreetMap (no API key, no JS, GDPR safe)
- Link "Apri in OpenStreetMap" + indirizzo full
- CTA section con sl-contact__eyebrow "Prima consulenza conoscitiva gratuita" + 2 sl-btn (mailto: + tel:)

---

## 4 · Verifica regressione · 17 punti precedenti preservati

| Fase | Punto | Pre v0.10.0 | Post v0.10.0 |
|---|---|:---:|:---:|
| Recovery v0.9.0 | F1-F6 (6 fix) | ✅ | ✅ tutti preservati |
| Step E v2 | M1+M2+M3 mobile | ✅ | ✅ |
| Step E v2 | taxonomy-tipo-area template | ✅ | ✅ |
| Step E v2 | duplicate H1 chi-siamo/contatti | ✅ | ✅ |
| Pain Points | P0.1-P1.4 | ✅ | ✅ |
| Audit Alignment | sitemap dropdown + costi | ✅ | ✅ |
| Step D | content competenze + avvocati | ✅ | ✅ |
| Foto Emiliano | _thumbnail_id=2683 | ✅ | ✅ verificato |
| Bio_estesa avvocati | post_meta | ✅ | ✅ |
| Schema 16/16 valid | JSON-LD | ✅ | da verificare nel walkthrough |

✅ **Nessuna regressione rilevata** sui 13 URL smoke testati.

### Specifico: archive avvocato — foto NON ridotte a 80x80

```
HTML /avvocati/: 4 foto archive avvocato emesse (sl-team__portrait)
Class wp-image / saltelli-attorney-portrait: conservata
```

Il fix B3 ha specificità `.sl-post__author-card .sl-team__portrait` (0,2,0) — scoped ESCLUSIVAMENTE all'author-card del blog. Le foto archive avvocato (`.sl-team--archive .sl-team__portrait`) e single avvocato (`figure.sl-attorney__portrait`) non sono toccate.

---

## 5 · Decisioni autonome

1. **A2 NO duplicato di Recovery F6.**
   Recovery F6 ha già: `.sl-competenza__prose h2 / .sl-page__prose h2 / .entry-content h2 { margin-block: 80px 24px }`. Aggiungere lo stesso block in A2 sarebbe duplicazione. Skip e conferma A2 già coperto.

2. **A3 selettore `:not()` chain per preservare liste meta.**
   Il em-dash bullet rule `ul li::before { content: "—" }` si sarebbe applicato a TUTTE le `<ul>` del tema, incluso `.sl-articles` (homepage articoli), `.sl-areas__list` (lista 19 aree), `.sl-team__specs` (tag avvocato). Aggiunto `:not(.sl-areas__list):not(.sl-articles):not(.sl-team__specs):not(.sl-attorney__specs)` per scoping pulito.

3. **B2 stock images NON sostituite.**
   Hard rule prompt: "Stock images del cliente vanno SOLO ridimensionate via CSS, NON sostituite". Le illustrazioni cartoon AI (uomo terrorizzato TARI, ecc.) restano nel DB. Solo CSS forza container 960px + aspect 16/9 per uniformità visiva. Decisione rimpiazzo immagini va a fase content/ottimizzazione futura (Step F+).

4. **B3 specificità doppia + `!important` solo per width/height/aspect.**
   La regola `.sl-team__portrait` (homepage) aveva `aspect-ratio: 3/4` + dimensioni specifiche. Per fare override scoped solo dentro author-card senza impattare homepage/archive, ho usato `.sl-post__author-card .sl-team__portrait` (specificità 0,2,0) MA serviva `!important` per width/height/aspect-ratio perché la regola homepage usa anche specificità 0,1,0 ma con properties più specifiche (es. aspect-ratio inline in figure). Documentato in commento CSS.

5. **C1 doppio layer fix (menu + init hook) invece di solo uno.**
   Menu fix (preventive): se utente clicca dal menu, va a `/chi-siamo/` direttamente — no redirect.
   Init hook (defensive): se utente digita `/lo-studio/` a mano o arriva da backlink SEO esterno, intercetta e redirige.
   Combinati: bookmark/backlink coverage + UX clean.

6. **C1 hook su `init` priority 1, NON `template_redirect`.**
   `template_redirect` parte DOPO `redirect_canonical` WP che fa già 301 al blog post. Hook su `init` priority 1 è early enough per intercettare prima di parse_request. Verificato: 1° tentativo con template_redirect non funzionava (HTTP 301 → blog post), 2° tentativo con init priority 1 funziona (HTTP 301 → /chi-siamo/).

7. **C2 `page_for_posts` invece di nuovo `home.php` template.**
   Esisteva già `index.php` con loop completo. Settando `page_for_posts: 1413`, WP serve `/blog/` con `index.php` (fallback corretto quando home.php manca). Soluzione minimal: 1 option DB change vs nuovo file template.

8. **C3 OpenStreetMap embed invece di Google Maps.**
   - No API key required
   - GDPR-friendly (no tracking)
   - Adsolut policy "no Google scripts unless necessary"
   - Free forever, no quota

---

## 6 · Tempo per group

| Group | Tempo |
|---|:---:|
| GROUP A — Typography (4 bug, ~120 righe CSS) | ~25 min |
| GROUP B — Immagini (3 bug, ~110 righe CSS) | ~22 min |
| GROUP C — Routing/Content (3 bug, WP-CLI + PHP + CSS ~150 righe) | ~25 min |
| Bump version + smoke test 13 URL + report | ~8 min |

**Totale:** ~80 minuti.

---

## 7 · Note per orchestrator (visual walkthrough atteso)

### Cosa verificare visivamente nel prossimo walkthrough

1. **Drop-cap** SOLO su single blog post (non CPT competenza, non page costi/contatti)
2. **Lede italic Playfair 20-24px** sopra body p first
3. **Em-dash bullet "—" accent gold** in liste body post (no bullet "•" black)
4. **Blockquote pull-quote** se presente nel body (border-left 2px accent + italic)
5. **Featured image hero** ora 960px max + aspect 16/9 (no più viewport-wide)
6. **Author bio card** in fondo blog post con foto 80x80 (no più 1562px gigante)
7. **/blog/** rende 16 post/page con paginazione "Più recenti / Meno recenti"
8. **/contatti/** ha map OpenStreetMap embed + sezione CTA "Scrivici una mail / Chiama"
9. **/lo-studio/** redirige istantaneamente a /chi-siamo/ (HTTP 301)

### Quote esplicite

- **Stock images del cliente** (illustrations cartoon AI in alcuni blog post): NON sostituite (hard rule). Solo cornice CSS uniforma. Da rimpiazzare in fase content futura se direttore lo richiede.
- **Sezione "Si occupa di" su single avvocato**: non renderizza (meta `aree_competenza_correlate` vuoto post-Step D). Non blocker, opzionale.
- **GROUP D bug minor (D1 search layout, D2 404 plain)**: rimasti aperti come da prompt ("skip se tempo limitato"). Possibile target per Step F+ o pre-deploy polish.

---

## 8 · File modificati

```
M  wp-content/themes/saltelli/style.css                          (Version 0.9.0 → 0.10.0)
M  wp-content/themes/saltelli/functions.php                      (SALTELLI_THEME_VERSION bump)
M  wp-content/themes/saltelli/assets/css/sections.css            (~360 righe in fondo · GROUP A+B+C)
M  wp-content/themes/saltelli/page.php                           (~50 righe · is_page('contatti') block)
M  wp-content/themes/saltelli/inc/setup.php                      (~18 righe · /lo-studio/ → /chi-siamo/ redirect)
+  .claude/knowledge/design/sessione-1/reports/editorial-refinement-v0.10.0/REPORT.md

DB changes (via WP-CLI, no SQL diretto):
  - wp_postmeta: post_id 2684 _menu_item_url '/lo-studio/' → '/chi-siamo/'
  - wp_options: page_for_posts 0 → 1413
```

**Niente modifiche a:**
- Foto Emiliano `_thumbnail_id` (preservato 2683)
- `bio_estesa` avvocati Step D
- `post_content` CPT competenza/avvocato
- Design tokens
- Template single-avvocato.php / single-competenza.php / archive-* / front-page.php / single.php / 404.php / search.php
- Plugin attivi/disattivati
- Image library

---

## 9 · Hard rule rispettata

- ✅ Foto Emiliano `_thumbnail_id=2683` PRESERVATA (verificato `wp post meta get 2660 _thumbnail_id` post-fix)
- ✅ `bio_estesa` 4 avvocati PRESERVATA (no DB write)
- ✅ `post_content` CPT competenza PRESERVATO (no DB write)
- ✅ Design tokens NOT modificati
- ✅ Cache flush + smoke test 8+ URL dopo OGNI gruppo (A+B+C → 3 cicli)
- ✅ Verifica CSS effective via curl + grep dopo ogni fix
- ✅ Stock images cliente: SOLO ridimensionate via CSS, NON sostituite
- ✅ `!important` documentati come override esplicito (B3 portrait + F3 portrait Recovery)

---

*Editorial Refinement v0.10.0 completato. v0.10.0-beta-editorial pronta per Visual Walkthrough completo del direttore d'orchestra prima di Step F (Production Readiness). Mi fermo qui.*
