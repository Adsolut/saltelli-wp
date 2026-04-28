# Saltelli — WordPress Custom Theme

Tema custom per **Studio Legale Emiliano Saltelli & Partners**, Napoli (Chiaia).
Versione: `0.1.0-scaffold` — **solo struttura**. Design + animazioni + contenuti = lavoro successivo dei multi-agent.

## Stack

- **WordPress** ≥ 6.5
- **PHP** ≥ 8.2
- **ACF** (Free OK per i campi base; **Pro consigliato** per repeater FAQ + casi rappresentativi + Options Page)
- **GSAP 3.15+ + Lenis** (placeholder enqueue, popolati dallo Style & Animation agent)
- Schema JSON-LD **inline** in PHP partials (NO Schema Pro, NO Yoast Schema), un blocco per pagina + Organization globale.
- **Nessun page builder.** Pure PHP template hierarchy.

## Struttura

```
saltelli/
├── style.css                            # WP header
├── functions.php                        # bootstrap (include moduli)
├── README.md                            # questo file
│
├── inc/
│   ├── setup.php                        # supports, image sizes, menu
│   ├── enqueue.php                      # CSS/JS, GSAP/Lenis/font TODO
│   ├── helpers.php                      # utility (saltelli_field, breadcrumb chain, ...)
│   ├── cpt-avvocato.php                 # CPT avvocato (slug /avvocati/)
│   ├── cpt-competenza.php               # CPT competenza + tassonomia tipo-area
│   ├── acf-fields.php                   # ACF bootstrap (load/save JSON)
│   ├── acf-json/
│   │   ├── group_avvocato.json          # foto, ruolo, specs, bio, formazione, sameAs
│   │   ├── group_competenza.json        # answer_capsule, is_tier_1_focus, body_extended, faq, casi
│   │   └── group_settings.json          # scheletro Options Page
│   ├── schema/
│   │   ├── schema-loader.php            # routing partial in base al template corrente
│   │   ├── partial-organization.php     # Organization + LegalService + WebSite (globale)
│   │   ├── partial-attorney.php         # Person/Attorney (single-avvocato)
│   │   ├── partial-faqpage.php          # FAQPage (single-competenza, se >= 1 FAQ)
│   │   ├── partial-breadcrumb.php       # BreadcrumbList (tutto tranne home)
│   │   └── partial-article.php          # Article (single post)
│   └── seo/
│       ├── meta-tags.php                # OG, Twitter, description (skip se Yoast/RankMath attivi)
│       └── ai-files.php                 # endpoint /llms.txt + filter robots_txt
│
├── geo-assets/                          # copia interna (servita da ai-files.php)
│   ├── llms.txt
│   └── robots.txt
│
├── assets/
│   ├── css/{tokens.css, base.css, components/}
│   ├── js/{main.js, lenis-init.js, gsap-init.js}
│   ├── fonts/                           # vuota — Style & Animation agent metterà WOFF2
│   └── images/                          # vuota — placeholder per logo/icon set
│
└── *.php                                # template files (header, footer, front-page, single-*, archive-*, page, search, 404)
```

## Setup (locale)

Il tema vive in `wp-content/themes/saltelli/` (bind mount Docker, vedi `docker-compose.yml` nella root del repo).

### Attivare il tema

```bash
docker compose run --rm wpcli theme activate saltelli
```

### Flush rewrite (dopo l'attivazione e dopo cambi CPT)

```bash
docker compose run --rm wpcli rewrite flush --hard
```

### ACF — pickup dei field group

I JSON in `inc/acf-json/` sono **picchiati automaticamente** da ACF (filter `acf/settings/load_json`). Quando installi ACF (Free o Pro), apri **Custom Fields → Field Groups** e dovresti vedere già:

- *Avvocato — dati profilo*
- *Competenza — area di pratica*
- *Saltelli — Impostazioni tema* (richiede ACF Pro per Options Page)

Per il repeater `faq` e `casi_rappresentativi` serve **ACF Pro**. Senza Pro, i field group si caricano ma quei campi non saranno editabili.

## Schema JSON-LD — flusso

1. `inc/schema/schema-loader.php` si aggancia a `wp_head` priorità 5.
2. Su **ogni** pagina: `partial-organization.php` (Organization + LegalService + WebSite).
3. Su **single-avvocato**: `partial-attorney.php` (Person/Attorney con `@id` = url + `#person`).
4. Su **single-competenza** con almeno 1 FAQ valida: `partial-faqpage.php`.
5. Su **single post**: `partial-article.php`.
6. Su **tutto tranne home**: `partial-breadcrumb.php`.

I dati anagrafici dello studio sono centralizzati in `helpers.php::saltelli_studio_data()` — quando si sposterà a ACF Options Page, sostituire la chiamata mantenendo la stessa firma.

## /llms.txt e robots.txt

`inc/seo/ai-files.php`:

- Aggiunge rewrite `^llms\.txt$` e serve dinamicamente il file `geo-assets/llms.txt` con `Content-Type: text/plain`.
- Filtra `robots_txt` sostituendo l'output WP standard con `geo-assets/robots.txt` (mantenendo le righe `Sitemap:` reali del runtime).

In **produzione** è preferibile copiare i due file direttamente in webroot per servirli senza bootstrap WP.

## Tier-1 / Tier-2 — strategia GEO

Decisione confermata in `project-context.json` → `strategic_focus_decision`:

- **Tier-1** (deep cluster, 1500-2500 parole, 5 FAQ, casi rappresentativi):
  1. `diritto-tributario` — Emiliano + Fabiana
  2. `diritto-del-lavoro` — Fabiana
  3. `diritto-di-famiglia` — Antonia (+ tutela LGBTQ+)
- **Tier-2** (16 aree restanti): standard CPT con answer capsule + 3 FAQ + CTA.

L'ACF flag `is_tier_1_focus` su CPT `competenza` è il branch del template `single-competenza.php`. Lo `archive-competenza.php` usa lo stesso flag per ordinare i tier-1 in cima.

## Stato

`0.1.0-scaffold`. Nessun design, nessuna animazione, nessun contenuto reale. Tutti i template hanno `<!-- TODO Style & Animation agent: ... -->` nei punti di intervento.

I prossimi step (multi-agent in tmux, prompt: `.claude/PROMPT_LEAD_AGENT.md`):

- **Style & Animation** — design tokens definitivi, GSAP+Lenis, componenti, font.
- **Content** (Elena) — answer capsule, FAQ, body extended tier-1, copy avvocati.
- **GEO Engineer** — completare schema TODO (sameAs, foundingDate, GPS, foto), Lighthouse > 90, GBP attivo.

## Convenzioni

- 1 H1 per pagina (verificato manualmente, mai introdotto in template).
- `wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)` su tutti i partial schema.
- Niente nero pieno, niente magenta. Palette in `assets/css/tokens.css`.
- Nessun fetch a CDN senza SRI hash quando si caricheranno GSAP/Lenis.
