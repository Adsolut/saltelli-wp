# Report Agenti — SHIP MODE 24H · Sessione 1 build

> Tre sub-agent paralleli in tmux, file disjoint, zero collisioni.
> Esecuzione: 2026-04-29 16:46 → 17:08 (~22 min totale, ~13/16/7 min individuali).
> Output complessivo: tema custom Saltelli WordPress popolato e pronto per deploy staging.

---

## 1. Style & Animation Agent — `pane 0` — DONE in 12m 58s

### Test Task 6 — risultati

| # | Test | Esito |
|---|---|---|
| 1 | Tema attivo, no errori PHP | ✅ |
| 2 | CSS files raggiungibili (tokens, base, components, sections) | ✅ tutti 200 |
| 3 | JS files raggiungibili (main.js) | ✅ |
| 4 | CSS variables nel DOM | ✅ |

### File creati/modificati

**Creati:**
- `wp-content/themes/saltelli/assets/css/components.css` — `.sl-mono`, `.sl-btn`, `.sl-link`, `.sl-area`, `.sl-acc` (estratto da tokens Sessione 1)
- `wp-content/themes/saltelli/assets/css/sections.css` — 7 sezioni Frame 1 (header, hero, areas, studio, team, cases, press, contact, footer) mobile-first con breakpoint @768/1024 + compat-shim Theme Architect

**Modificati:**
- `assets/css/tokens.css` — `:root` + reset `.sl-root` (i component spostati in `components.css`); aggiunto blocco alias legacy per non rompere base.css/template scaffold v0
- `assets/css/base.css` — typography setup, container, layout primitives, reduced motion
- `assets/js/main.js` — entrypoint con Lenis (lerp 0.1, disabilitato su `prefers-reduced-motion` e mobile <768), GSAP + ScrollTrigger + SplitText, hero text reveal, sezioni in scroll, list items stagger, header solid-on-scroll
- `inc/enqueue.php` — enqueue GSAP 3.12.5 + ScrollTrigger + SplitText + Lenis 1.1.13 da CDN, defer

**NON toccati (out of scope):** template `*.php`, `inc/schema/*`, `inc/acf-json/*`, `lenis-init.js` e `gsap-init.js` (tenuti come stub legacy).

### Differenze prese rispetto al `tokens.css` di Claude Design

Nessuna modifica ai valori. Solo riorganizzazione strutturale come richiesto:
- `:root { ... }` + reset `.sl-root` in `tokens.css`
- 5 component spostati 1:1 in `components.css`
- Aggiunto blocco alias legacy in `tokens.css` (`--color-bg → --background`, `--space-3 → 12px`) per compat
- Font loading: `@import` Google Fonts come fallback temporaneo (TODO Duccio: scaricare WOFF2 self-hosted in `assets/fonts/` per rimuovere @import e attivare il blocco `@font-face` già scritto e commentato)

### 🔴 BLOCKER segnalato — naming divergence template vs JSX

Il template `front-page.php` / `header.php` usa una naming convention diversa da quella del JSX di Sessione 1. Aggiunto **compat-shim** in fondo a `sections.css` per coprire entrambe.

| Template usa | JSX usa | Decisione consigliata |
|---|---|---|
| `.sl-header__brand-name` | `.sl-header__brand-title` | tenere `__brand-name` |
| `.sl-header__burger` | `.sl-header__menu-btn` | tenere `__burger` (più semantico) |
| `.sl-header__mobile` | `.sl-mobile-menu` | tenere `__mobile` (template) |
| `[data-scrolled="true"]` | `.is-scrolled` | il main.js setta entrambi → convergere su `[data-scrolled]` |
| `.sl-hero__word` + `[data-split-reveal]` | `.sl-word` | tenere `.sl-hero__word` (block-element) |
| `.sl-hero__subheadline` | `.sl-hero__lede` | tenere `__subheadline` |
| `.sl-team__lawyer/__portrait/__name/__specs` | `.sl-lawyer/__portrait/__name/__specs` | tenere `.sl-team__*` |
| `.sl-cases__list/__row/__id/__desc/__outcome` | `.sl-case/__id/__desc/__outcome` | tenere `.sl-cases__row` |
| `.sl-press__outlets/__outlet` | `.sl-press__list/__item` | tenere `.sl-press__outlets` |
| `.sl-contact__big`, `.sl-contact__cta` | `.sl-contact__item-value`, `.sl-contact__cta-wrap` | tenere `__big/__cta` |
| `.sl-section-head` + `.sl-section-title` | inline | tenere — buona astrazione |
| `footer.php` usa `.site-footer` + `.footer-col` legacy | `.sl-footer__*` mai progettato | aggiunto styling per `.site-footer` legacy |

**Azione consigliata:** dopo questa pass, una rapida unificazione (1 commit) per adottare la naming dei template come canonica e rimuovere blocchi duplicati. Compat-shim attuale rende tutto funzionante ma è ridondante.

### Note per gli agent successivi

**Per GEO Engineer:**
- Animazioni JS defer non bloccano rendering iniziale → buono per LCP
- Lenis disabilitato su `prefers-reduced-motion` e mobile (<768px) → no impact su INP/CLS
- Google Fonts via `@import` è temporaneo e penalizza Lighthouse (~10-20 punti)

**Per tutti:**
- Hero reveal richiede `<h1 class="sl-hero__headline" data-split-reveal>` con parole pre-segmentate in `<span class="sl-hero__word">` — template attuale è già conforme ✅
- Per qualunque elemento da rivelare in scroll: aggiungere `class="sl-revealable"` o `data-reveal` — main.js fa pickup automatico via ScrollTrigger
- Filtri aree (`.sl-areas__filter`) hanno styling pronto per `[aria-pressed="true"]` e `.is-active` — JS filtraggio non ancora scritto (TODO post-demo)
- Hover preview area pratica (`.sl-area__preview`) ha styling base; binding `mouseenter`/`mouseleave` + popolamento da `data-area-lead` è TODO

---

## 2. Theme Architect Agent — `pane 1` — DONE in 16m 33s

### Test Task 9 — risultati

| # | Test | Esito |
|---|---|---|
| 1 | Tema rendering (sl-hero, sl-areas, sl-team) | ✅ markup presente |
| 2 | Single H1 per pagina | ✅ 1 H1 su homepage (verificato) |
| 3 | ACF field group | ⚠️ ACF non installato (atteso, fallback hardcoded attivi) |
| 4 | CPT funzionanti | ✅ avvocato + competenza con archive |
| 5 | Menu posizioni | ✅ primary, footer-studio, footer-aree, footer-legal |
| 6 | PHP error log pulito | ✅ |

### File creati/modificati

**ACF JSON (2):**
- `group_competenza.json` — aggiunto `lead_breve` (textarea, 60-160 char)
- `group_settings.json` — riscritto con 19 campi (hero, colophon, studio, team, cases, press, contact); 2 repeater flaggati `// REQUIRES ACF PRO` con fallback hardcoded

**Template PHP (15):**
- `header.php` — sticky con `[data-scrolled]`, click-to-call, hamburger mobile, body class `sl-root`
- `front-page.php` — 7 sezioni Frame 1 (hero, aree con sticky preview, lo studio, avvocati asimmetrici, casi, press, contatti)
- `single-avvocato.php` — hero ritratto + bio + "Si occupa di" + formazione timeline + sticky margine sinistro
- `single-competenza.php` — branch `is_tier_1_focus` (depth tier-1 vs essenziale tier-2), answer capsule, FAQ accordion, casi solo tier-1
- `archive-avvocato.php` — griglia asimmetrica 4 lawyer
- `archive-competenza.php` — lista tipografica 19 aree con filtro pillole tassonomia, tier-1 emergono in cima
- `single.php` blog — hero meta + drop-cap + TOC sticky placeholder + autore in fondo + 3 correlati
- `footer.php` — 3 colonne dark navy
- Più: `index.php`, `page.php`, `archive.php`, `search.php`, `searchform.php`, `404.php`

**`inc/`:**
- `inc/cpt-avvocato.php`, `inc/cpt-competenza.php` — CPT registrati
- `inc/setup.php` — image sizes, menu posizioni, theme support
- `inc/acf-fields.php` — bootstrap ACF + filter path acf-json
- `inc/helpers.php` — `saltelli_option()`, `saltelli_get_breadcrumb_chain()`, `saltelli_count_faq()`, `saltelli_canonical_url()`, `saltelli_reading_time()`, `saltelli_homepage_cases()`, `saltelli_press_outlets()`, `saltelli_studio_phone_e164()`

**File NON toccati (rispettati gli scope altrui):** `inc/enqueue.php` (Style), `inc/schema/` (GEO), `inc/seo/` (GEO), `assets/` (Style)

### ACF Pro presente?

**No, ACF non è installato.** Verificato con `wp plugin list | grep acf` → output vuoto.

**Conseguenze:**
- Options Page "Saltelli Settings" registrata ma visibile solo quando ACF Pro arriva
- I template usano `saltelli_option()` che degrada a default hardcoded → la homepage rende correttamente con i contenuti del JSX (eyebrow, headline, subheadline, colophon, copy "Lo studio", press outlets, casi rappresentativi) anche senza ACF
- I repeater `casi_rappresentativi_home` e `press_outlets` flaggati `// REQUIRES ACF PRO`. Fallback editoriali in helpers (4 casi + 6 outlets)
- Quando ACF Pro arriverà: i field group sono già in `inc/acf-json/`, l'options page si materializza

### Decisioni autonome

1. **`menu_order` invece di `numero_ordinamento`** — uso il `menu_order` nativo del CPT competenza per l'ordine 01-19. Più semplice e standard.
2. **Ordering homepage tier-1 first** — query competenze fa `meta_value_num DESC, menu_order ASC, title ASC` con `meta_key=is_tier_1_focus` → le 3 aree tier-1 emergono in cima.
3. **Filtro pillole basato su tassonomia tipo-area** — invece di hardcodare `["Tutte","Civile",...]` come nel JSX, leggo termini reali e li ordino per count DESC.
4. **Fallback editoriali per casi/press** — non blocco la build se ACF Pro manca. Homepage demo-ready out-of-the-box con 4 casi + 6 outlets.
5. **Lawyer-author matching su blog post** — `single.php` cerca un post avvocato con titolo che match `display_name` dell'autore WP, e linka.
6. **Layout team con `--sl-col`/`--sl-span`/`--sl-offset` come CSS custom property** — passo i valori dal PHP via `style="--sl-col:1; --sl-span:5; --sl-offset:0px;"`. Lo Style Agent può consumarli con `grid-column: var(--sl-col) / span var(--sl-span)`.
7. **Drop-cap delegato al CSS** — niente `<span class="dropcap">` PHP-side; il template applica `data-drop-cap` su `.sl-studio__prose` e `.sl-post__body`.
8. **Sticky bottoni avvocato come `<aside class="sl-attorney__sticky">` dentro `<article>`** — semplifica CSS sticky e coerenza semantica.

### Note per Style & Animation Agent

Tutti gli hook DOM-side sono pronti per consumo CSS/JS:

- **Body class `sl-root`** ✅ (header.php fa `body_class('sl-root')`)
- **Hero SplitText hook**: `<h1 data-split-reveal>` con `<span class="sl-hero__word" data-i="N">` — animazione GSAP `[data-split-reveal] .sl-hero__word` con stagger 80ms
- **Drop-cap hook**: `[data-drop-cap] > p:first-of-type::first-letter`
- **Aree filter**: `<button class="sl-areas__filter" data-filter="*|<slug>">`; `<a class="sl-area" data-area-cat="<slug>" data-area-lead="..." data-area-label="...">`
- **Sticky preview homepage**: `<aside class="sl-area__preview" data-area-preview>` placeholder vuoto. JS: on `mouseenter` leggi `data-area-lead` + `data-area-label` e popola; on `mouseleave` ripristina `<p class="sl-area__preview-empty">`
- **Header sticky**: `.sl-header[data-scrolled="true|false"]`; toggle a 80px nel JS inline di header.php (Style Agent: rimuovere se ridondante con main.js)
- **Mobile menu**: `.sl-header__burger` toggla `aria-expanded` + `[hidden]` su `.sl-header__mobile`. Aggiunge `<html class="sl-menu-open">` quando aperto (utile per `html.sl-menu-open { overflow: hidden; }`)
- **Team asymmetric grid**: `style="--sl-col:N; --sl-span:M; --sl-offset:Xpx"` → `grid-column: var(--sl-col) / span var(--sl-span); margin-top: var(--sl-offset);`. `@media (max-width: 1023px) { grid-column: 1/-1; margin-top: 0; }`
- **Ritratti placeholder**: `<span class="sl-team__placeholder">` — gradient editoriale `linear-gradient(135deg, #c8c5be 0%, #6e6c66 100%)`
- **Blog single TOC**: `<aside class="sl-toc" data-toc>` placeholder. Implementare scroll-spy su h2/h3 in `.sl-post__body`
- **Footer dark theme**: `.sl-footer { background: var(--primary); color: var(--background); }`. Aree in 3-column flow

### Note per GEO Engineer

- **Telefono pubblico**: ACF Options field `contact_telefono_pubblico` (default `+39 081 1813 1119`). Fallback E.164 resta `saltelli_studio_phone_e164()` → `+390811813119`
- **Email & PEC**: `contact_email_pubblica`, `contact_pec`
- **Field name preservati**: tutti i campi esistenti per avvocati (`email_pubblica`, `telefono_pubblico`, `same_as_linkedin`, `foto_ritratto`, `bio_breve`, `ruolo_breve`) e competenze (`is_tier_1_focus`, `answer_capsule`, `faq`, `casi_rappresentativi`, `cta_label`, `cta_url`, `articoli_correlati`, `lead_attorneys`) sono **identici** ai partial schema esistenti. Aggiunto solo `lead_breve` (textarea 160 char).
- **Helpers già pronti**: `saltelli_get_breadcrumb_chain($post_id)` → consumabile da `partial-breadcrumb.php`. `saltelli_count_faq($post_id)` per decidere se emettere FAQPage. `saltelli_canonical_url()` per `<link rel="canonical">`. `saltelli_reading_time($post_id)` per Article schema `wordCount` o `timeRequired`.

### 🔴 BLOCKER segnalato — Elementor Pro Theme Builder attivo

> Il sito locale ha **Elementor Pro Theme Builder** attivo con `location-header` (id 79), `location-footer` (id 336) e archive-template configurati: questi intercettano `get_header()/get_footer()/archives` e iniettano markup Elementor sopra al tema.

**Conseguenze:**
- Homepage rende correttamente le sezioni `sl-*` (front-page.php vince), ma header/footer visivi sono Elementor
- Archive `/avvocati/` e `/competenze/` mostrano widget Elementor invece dei nostri template (HTTP 200 ma `sl-*` assenti)
- `body_class('sl-root')` funziona nel codice ma non appare in output perché Elementor sostituisce l'intero header

**Diagnosi finale Theme Architect:** "Non è bug del tema, è plugin override. Si risolve in fase deploy disattivando Elementor Pro (o le sole template di Theme Builder) sullo staging fresco DigitalOcean. SHIP_PLAN_24H prevede deploy con installazione pulita, quindi il problema non si propaga."

---

## 3. GEO Engineer Agent — `pane 2` — DONE in 7m 17s

### Test Task 9 — risultati

| # | Test | Esito |
|---|---|---|
| 1 | Endpoint `/llms.txt` risponde | ✅ contenuto file servito |
| 2 | `robots.txt` include AI crawlers (GPTBot, ClaudeBot, PerplexityBot) | ✅ |
| 3 | Schema globale presente in homepage | ✅ |
| 4 | PHP error log pulito | ✅ |
| 5 | Validazione esterna validator.schema.org | ⏳ rimandata a post-deploy staging |

### File creati/modificati

- `inc/schema/schema-loader.php` — router su `wp_head` priority 5; condiziona inclusione partial in base a `is_singular`/`is_front_page`
- `inc/schema/partial-organization.php` — Organization + LegalService + WebSite con coabitazione Yoast
- `inc/schema/partial-attorney.php` — Person/Attorney dinamico per CPT avvocato; `worksFor → #organization`, `knowsAbout` da `specializzazioni` ACF, `alumniOf` Federico II hardcoded (confermato)
- `inc/schema/partial-faqpage.php` — FAQPage da repeater ACF `faq`; skip se zero FAQ
- `inc/schema/partial-breadcrumb.php` — BreadcrumbList dinamico via `saltelli_get_breadcrumb_chain()`
- `inc/schema/partial-article.php` — Article per blog post; `dateModified` aggiornato, fallback Organization se autore generico
- `inc/seo/meta-tags.php` — `saltelli_seo_plugin_active()` rileva Yoast/RankMath/AIOSEO; early-return totale se attivo
- `inc/seo/ai-files.php` — endpoint `/llms.txt` via WP rewrite + `template_redirect`; `robots_txt` filter

### Stato coabitazione Yoast

Yoast SEO 27.4 attivo e attivamente emette schema graph completo. Detection via `saltelli_seo_plugin_active()`.

| Schema | Yoast | Saltelli | Decisione |
|---|---|---|---|
| Organization | emette | skip | Yoast wins |
| WebSite | emette | skip | Yoast wins |
| WebPage | emette | n/a | Yoast wins |
| BreadcrumbList | emette | skip | Yoast wins |
| Article (single post) | emette | skip | Yoast wins |
| Person (autore post) | emette | n/a | Yoast wins |
| **LegalService** | non emette | **emette** | **Saltelli unique value-add** con `parentOrganization → #organization` |
| **Person/Attorney (CPT avvocato)** | non emette | **emette** | **Saltelli wins** (Yoast non gestisce CPT custom) |
| **FAQPage (da ACF repeater)** | non emette nativamente | **emette** | **Saltelli wins** (sorgente di verità è ACF) |

**Meta tags (description/OG/Twitter):** `saltelli_emit_meta_tags()` early-return totale se SEO plugin presente. Niente sovrapposizione.

### Path risolti per `geo-assets/`

Il bind mount Docker monta solo `wp-content/`, quindi `/saltelli-wp/geo-assets/` non è raggiungibile dal container.

**Strategia implementata** in `inc/seo/ai-files.php` → `saltelli_locate_geo_asset($name)`:

```
candidate path order:
  1. dirname(ABSPATH) . '/geo-assets/' . $name      ← repo layout (host-only)
  2. SALTELLI_THEME_DIR . '/geo-assets/' . $name    ← copia statica nel tema  ✅ HIT
  3. SALTELLI_THEME_DIR . '/' . $name               ← fallback theme root
```

**Risoluzione effettiva runtime:**
- `llms.txt` → `/var/www/html/wp-content/themes/saltelli/geo-assets/llms.txt` (6.515 byte)
- `robots.txt` → `/var/www/html/wp-content/themes/saltelli/geo-assets/robots.txt` (1.707 byte)

I file sono versionati nel tema per funzionare anche su host che non includono la root del progetto nel path web (staging/prod). Le sitemap dell'host vengono mergiate runtime sopra le sitemap statiche del file (Yoast → `/sitemap_index.xml` rilevato e iniettato).

Endpoint `/llms.txt` servito via WP rewrite + `template_redirect` con `redirect_canonical` disabilitato (evita redirect a `/llms.txt/`).

### TODO lasciati (ereditati dallo scaffolding, non nuovi)

| TODO | File | Owner |
|---|---|---|
| Google Business Profile URL → `Organization.sameAs` | `partial-organization.php` | Duccio + Ludovica |
| LinkedIn personale Fabiana, Antonia, Stefano | `partial-attorney.php` | Ludovica |
| Foto avvocati `/wp-content/uploads/avv-{slug}.jpg` | `partial-attorney.php` | Ludovica |
| Foto sede `/wp-content/uploads/sede-studio-saltelli.jpg` | `partial-organization.php` | Ludovica |
| `Content-Type: text/plain` su `/robots.txt` | `inc/seo/ai-files.php` | Verificare se Yoast/sg-security override il MIME |
| Decisione scope Stefano Tedesco (5° avvocato Fabrizio D'Onofrio?) | `partial-organization.php` | Duccio |
| ✅ GPS lat/lng confermati 2026-04-28 (40.830267 / 14.237217) | — | chiuso |
| ✅ Anno fondazione 2019-01 confermato | — | chiuso |
| ✅ Università Federico II per i 4 avvocati confermata | — | chiuso |

### Risultato validazione schema

**Validazione automatica eseguita:** sintassi PHP clean, JSON parse strict valido su tutti i blocchi (homepage, contatti, single-post), zero duplicati Organization/WebSite/BreadcrumbList/Article rispetto al graph Yoast, `@id` canonici stabili (`#organization`, `#legalservice`, `#person`, `#faq`, `#article`, `#breadcrumb`).

**Validazione esterna validator.schema.org + Google Rich Results Test:** non eseguibile in locale. Rimandata a post-deploy staging su DigitalOcean droplet.

Target check post-deploy:
1. `/` → Yoast graph + LegalService (zero errori, warning ammessi)
2. `/avvocati/emiliano-saltelli/` → Person/Attorney + Yoast Breadcrumb (test bloccato finché CPT avvocato non popolato)
3. `/competenze/diritto-tributario/` → FAQPage + Breadcrumb (idem, attesa popolamento CPT competenza tier-1)
4. Un post blog → Yoast Article + LegalService

**Quality gate:** ZERO errori, warning solo su campi opzionali. Aspettativa realistica: alcuni warning su `image.width/height` per LegalService (foto sede non caricata) e su `priceRange` (€€ è ammesso ma alcuni validator preferiscono ranges in cifre).

---

## Recap cross-agent — punti aperti

### 🔴 Critici per la demo cliente

1. **Elementor Pro Theme Builder** — risolto sul deploy DO pulito (non installiamo Elementor)
2. **Foto avvocati e sede mancanti** — placeholder editoriali (gradient + mono "Ritratto · 3:4" / "Plate I · Facciata") sufficienti per la demo

### 🟡 Polish post-demo

1. **Naming convention divergence template/JSX** — compat-shim attivo, unificazione canonica in commit di polish (proposto: contestuale a Fase 1.E.10 Impeccable)
2. **Google Fonts via @import vs WOFF2 self-hosted** — penalizza Lighthouse 10-20 punti. 5 min di lavoro per scaricare i 2 critical (Playfair 700, DM Sans 400/500)
3. **ACF Pro non installato** — fallback editoriali attivi, demo OK; ACF Pro va licenziato pre-produzione per Options Page

### 🟢 Buone notizie

- **Yoast coabitazione gestita correttamente** — niente schema duplicati
- **`/llms.txt` raggiungibile** via path-fallback intelligente che funziona anche se il bind mount cambia
- **Helpers PHP completi** (breadcrumb, FAQ count, reading time, canonical, contatti) → riusabili anche nei prossimi template di Fase 2/3/5
- **Tier-1 ordering automatico** in homepage e archive-competenza tramite `meta_value_num` query
- **Nessun fatal error PHP** in nessuno dei 3 agent

---

*Report consolidato: Claude (chat) — orchestratore livello macro.*
*Source raw: `.claude/knowledge/design/sessione-1/reports/agent-{1,2,3}-*-RAW.txt`*
