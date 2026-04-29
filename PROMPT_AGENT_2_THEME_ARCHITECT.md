# Prompt — Theme Architect Agent (SHIP MODE 24H)

> **Per Claude Code in tmux pane 2.** Apri questa cartella (`saltelli-wp/`), leggi questo file, eseguilo. Non improvvisare. Non comunicare con gli altri 2 agent (Style & Animation, GEO Engineer): lavorate su file disjoint.

---

## Tu sei

Il **Theme Architect Agent** del build Saltelli. Il tuo lavoro:

1. Riempire i **template PHP scaffolded** (oggi vuoti con TODO) con il markup tradotto **direttamente dal `homepage-desktop.jsx` di Claude Design**
2. Aggiornare gli **ACF field group JSON** se serve per supportare i contenuti del JSX
3. Configurare il **menu navigation** (header/footer) e i **widget areas**
4. Garantire che ogni template renderizzi correttamente con classi CSS `.sl-*` (le scrive Style & Animation Agent in parallelo, tu le usi)

**Non tocchi**:
- File CSS/JS in `assets/` (li gestisce Style & Animation Agent)
- File schema in `inc/schema/` (li gestisce GEO Engineer)
- `inc/enqueue.php` (lo gestisce Style & Animation Agent — solo per i suoi enqueue script/style)
- Contenuti reali (li popola Elena dopo)

---

## Letture obbligatorie (in quest'ordine, prima di scrivere codice)

1. `CLAUDE.md` — hard constraints
2. `BRIEF_Saltelli_WordPress.md` — sezione "Architettura sito" + "Stack tecnico"
3. `.claude/knowledge/project-context.json` — `client_data`, `team_members`, `practice_areas`, `strategic_focus_decision`
4. `CLAUDE_DESIGN_PROMPT.md` v2.1 — sezione 5 (i 5 frame)
5. **`.claude/knowledge/design/sessione-1/homepage-desktop.jsx`** ← FONTE DI VERITÀ del markup desktop
6. **`.claude/knowledge/design/sessione-1/homepage-mobile.jsx`** ← markup mobile (varia su menu hamburger + WhatsApp etichetta)
7. `wp-content/themes/saltelli/inc/cpt-avvocato.php` + `cpt-competenza.php` — già scaffolded
8. `wp-content/themes/saltelli/inc/acf-json/group_*.json` — field group da arricchire se servono campi nuovi
9. Tutti i file `.php` nella root del tema (front-page.php, single-*.php, archive-*.php) — sono scheletri vuoti da riempire

**NON leggere**: `design-canvas.jsx`, `Saltelli Partners - Sessione 1.html` — meta-visualization, non source-of-truth per il build.

---

## Hard rules (non negoziabili)

| Rule | Reason |
|---|---|
| `<body class="sl-root">` su ogni template (via `body_class` filter) | Attiva il reset CSS dello Style Agent |
| Pure PHP, **nessun JS inline** se non strettamente necessario (es. menu mobile toggle) | Performance + leggibilità |
| **Una sola `<h1>` per pagina, sempre** | Audit GEO ha trovato H1 duplicati nel sito attuale |
| `esc_html()`, `esc_attr()`, `esc_url()` su ogni output dinamico | Security baseline |
| Markup semantico HTML5: `<article>`, `<section>`, `<header>`, `<nav>`, `<footer>` | Accessibilità + SEO |
| **Niente Elementor, niente shortcode complessi.** Pure PHP template tags WP | CLAUDE.md decision |
| Le classi CSS le decide lo Style Agent: tu **usi** `.sl-hero`, `.sl-areas`, `.sl-team`, `.sl-cases`, `.sl-press`, `.sl-contact`, `.sl-footer` come scritto nel JSX (vedi sotto la mappa) | Coordinamento |
| ACF field name **immutabile** dopo il primo populate (Elena dipende da questi nomi) | Stabilità contenuti |

---

## Mappa JSX → PHP class

Quando estrai dal JSX e scrivi PHP, mantieni questa mappatura di classi (Style Agent le sta scrivendo in parallelo in `assets/css/sections.css`):

| Sezione del JSX | Classe principale PHP |
|---|---|
| Header sticky | `.sl-header` |
| Hero (100vh, headline + colophon) | `.sl-hero` |
| Aree di pratica (lista + sticky preview) | `.sl-areas` |
| Lo studio (prose + drop-cap) | `.sl-studio` |
| Avvocati (asimmetrico) | `.sl-team` |
| Casi rappresentativi | `.sl-cases` |
| Earned media strip | `.sl-press` |
| Contatti tipografici | `.sl-contact` |
| Footer denso 3 colonne | `.sl-footer` |

Dentro ognuna usi sub-classi BEM-like dove necessario (es. `.sl-hero__headline`, `.sl-team__lawyer`, `.sl-area--tier1`).

---

## Task 1 — Aggiornare ACF field group se necessario (15 min)

Il `homepage-desktop.jsx` mostra dati che servono al template `front-page.php`. Verifica se il `acf-json/group_competenza.json` esistente ha tutti i campi necessari per popolare la lista 19 aree del JSX. In particolare servono:

- `lead_breve` (textarea, 60-80 char) — è il "lead" mostrato nello sticky preview hover (campo NUOVO se non c'è già)
- `categoria_pillola` (text o taxonomy term reference) — per il filtro "Tutte/Civile/Penale/Tributario/Lavoro/Famiglia" (probabilmente già coperto dalla tassonomia `tipo-area`)
- `is_tier_1_focus` — già definito nel CLAUDE.md, verifica che sia presente
- `numero_ordinamento` (number) — per l'ordine "01-19" della lista (può essere il `menu_order` WP, ma se preferisci campo esplicito lo aggiungi)

**Aggiungi un ACF Options Page** "Saltelli Settings" (se ACF Pro disponibile) con:
- `hero_eyebrow` (text, default: "Studio Legale · Napoli · Chiaia · Dal 1999")
- `hero_headline` (textarea, default: "Diritto, con misura.")
- `hero_subheadline` (wysiwyg)
- `hero_cta_label` (text, default: "Prenota un primo incontro")
- `hero_cta_url` (url, default: "/contatti/")
- `colophon_indirizzo` (textarea)
- `colophon_orari` (textarea)
- `colophon_email` (email)
- `colophon_telefono` (text)
- `studio_titolo_sezione` (text, default: "Una bottega, in senso napoletano.")
- `studio_body` (wysiwyg)
- `studio_foto_facciata` (image)
- `team_titolo` (text, default: "Quattro professionisti.")
- `cases_titolo` (text, default: "Casi rappresentativi.")
- `press_outlets` (repeater) — sub: `nome` (text)
- `contact_email_pubblica`, `contact_telefono_pubblico`, `contact_pec`, `contact_piva` (text/email)

Se ACF Pro non è installato, scrivi i field group ma flagga in commento `// REQUIRES ACF PRO` per i repeater — il sito userà i defaults hardcoded.

**Aggiorna i 3 file `inc/acf-json/group_*.json` di conseguenza.**

---

## Task 2 — `header.php` (15 min)

Replica lo `<header>` del JSX (sticky, transparent → solid dopo 80px scroll). Markup PHP:

- `<!DOCTYPE html><html <?php language_attributes(); ?>>`
- `<head>`: `<?php wp_head(); ?>` (qui il GEO Engineer aggancia gli schema, lo Style Agent gli enqueue)
- `<body <?php body_class('sl-root'); ?>>`
- `<header class="sl-header" data-scrolled="false">`:
  - Logo testuale "Saltelli & Partners" (recupera da `get_bloginfo('name')`)
  - Mono subtitle "Studio Legale · Napoli"
  - Nav 6 voci: Studio, Avvocati, Competenze, Casi, Editoriale, Contatti — usa `wp_nav_menu(['theme_location' => 'primary', 'container' => 'nav', 'menu_class' => 'sl-header__nav'])`
  - Click-to-call header con telefono da ACF Options (fallback `+39 081 1813 1119`)
  - **Mobile**: hamburger button (vedi `homepage-mobile.jsx`)

Aggiungi piccolo JS inline (massimo 10 righe) per toggle `data-scrolled` su `<header>` ogni N px di scroll. Lo Style Agent gestisce le transizioni in CSS.

---

## Task 3 — `front-page.php` (60 min — il task più grosso)

Replica le 7 sezioni della Homepage da `homepage-desktop.jsx`:

### Sezione 1 — Hero
```php
<section class="sl-hero">
  <div class="sl-hero__inner sl-container">
    <div class="sl-hero__main">
      <div class="sl-mono"><?php the_field('hero_eyebrow', 'option'); ?></div>
      <h1 class="sl-hero__headline" data-split-reveal>
        <?php echo esc_html(get_field('hero_headline', 'option') ?: 'Diritto, con misura.'); ?>
      </h1>
      <p class="sl-hero__subheadline"><?php echo wp_kses_post(get_field('hero_subheadline', 'option')); ?></p>
      <a class="sl-btn sl-btn--primary" href="<?php the_field('hero_cta_url', 'option'); ?>">
        <?php the_field('hero_cta_label', 'option'); ?>
        <span class="arrow">→</span>
      </a>
    </div>
    <aside class="sl-hero__colophon">
      <!-- Indirizzo, orari, contatti — come da JSX -->
    </aside>
  </div>
  <div class="sl-hero__scroll"><div></div><span class="sl-mono">Scorri</span></div>
</section>
```

### Sezione 2 — Aree di pratica
Loop WP_Query su CPT `competenza`, ordinato per `menu_order` o `numero_ordinamento`. Per ogni post output:
```php
<div class="sl-area <?php echo get_field('is_tier_1_focus') ? 'sl-area--tier1' : ''; ?>"
     data-area-num="<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>"
     data-area-cat="<?php echo esc_attr(/* termine tassonomia tipo-area */); ?>"
     data-area-lead="<?php echo esc_attr(get_field('lead_breve')); ?>">
  <div class="sl-area__num"><?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?> / 19</div>
  <a class="sl-area__title" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
  <div class="sl-area__meta">
    <?php echo get_field('is_tier_1_focus') ? 'Tier 1 · approfondimento' : esc_html(/* nome categoria */); ?> →
  </div>
</div>
```

Filter pillole sopra: usa termini tassonomia `tipo-area`. JS inline minimale per il filter (vedi JSX). Sticky preview: lascia un `<aside class="sl-area__preview">` vuoto, popolato da JS (riprende il `data-area-lead` al hover).

### Sezione 3 — Lo studio
Drop-cap della prima lettera + prose editoriale da ACF. Foto facciata placeholder su `--surface` se l'immagine non c'è.

### Sezione 4 — Avvocati
WP_Query su CPT `avvocato`, limit 4, ordinato per `menu_order`. Per ogni avvocato:
```php
<article class="sl-team__lawyer" style="grid-column: <?php echo $col; ?> / span <?php echo $span; ?>; margin-top: <?php echo $offset; ?>px;">
  <div class="sl-team__portrait">
    <?php if (has_post_thumbnail()) the_post_thumbnail('saltelli-attorney-portrait');
    else echo '<div class="sl-team__placeholder"></div>'; ?>
  </div>
  <div class="sl-mono"><?php echo esc_html(get_field('ruolo_breve')); ?></div>
  <h3 class="sl-team__name"><?php the_title(); ?></h3>
  <div class="sl-team__specs">
    <?php foreach ((array) get_field('specializzazioni') as $spec): ?>
      <span class="sl-tag"><?php echo esc_html($spec); ?></span>
    <?php endforeach; ?>
  </div>
</article>
```

I valori `col`, `span`, `offset` per i 4 avvocati replicano quelli del JSX (Emiliano col 1 span 5 offset 0, Fabiana col 7 span 5 offset 96, Antonia col 2 span 5 offset 64, Stefano col 8 span 4 offset 32).

### Sezione 5 — Casi rappresentativi
ACF repeater `casi_rappresentativi` su Options o WP_Query su un CPT `caso` (se preferisci, registralo). Per ogni caso: identifier + descrizione editoriale + outcome bronzo.

### Sezione 6 — Earned media
Loop su ACF repeater `press_outlets`. Render strip flexbox.

### Sezione 7 — Contatti + Footer
Tre colonne tipografiche + CTA finale. Footer denso replica esattamente il JSX.

---

## Task 4 — `single-avvocato.php` (15 min)

Markup minimale che lo Style Agent può stylare dopo:
- Hero ritratto + nome serif gigante
- Bio estesa
- Lista "Si occupa di" (link a CPT competenza correlate)
- Formazione (timeline)
- Articoli del blog (loop WP_Query con `meta_query` o `tax_query` se associabili)
- CTA "Prenota un incontro con l'Avv. {nome}"
- Sticky margine sinistro: bottoni telefono/email/whatsapp

---

## Task 5 — `single-competenza.php` (20 min)

Branch `is_tier_1_focus`:
- Hero + answer capsule sempre
- Body extended SOLO tier-1
- Lead attorneys (post_object) sempre
- Casi rappresentativi SOLO tier-1
- FAQ accordion sempre (`<details><summary>` semantico, lo Style Agent gli mette le classi)
- Articoli correlati (loop blog)
- CTA finale

---

## Task 6 — `archive-competenza.php` + `archive-avvocato.php` (15 min)

**Archive competenza**: lista tipografica 19 aree (riusa il pattern `.sl-area` di `front-page.php`). Hero piccolo con titolo + intro 30 parole. Filtro pillole basato su tassonomia `tipo-area`.

**Archive avvocato**: griglia 4 lawyer asimmetrici (riusa il pattern `.sl-team` di `front-page.php`). Hero piccolo.

---

## Task 7 — `single.php` (blog post) (15 min)

Replica il Frame 5 dal `CLAUDE_DESIGN_PROMPT.md` v2.1:
- Hero: categoria mono uppercase + data + autore + reading time + titolo serif gigante + lead 2 righe
- Body: drop-cap, max-width stretta, pull-quote
- Sidebar destra desktop: TOC sticky scroll-spy (lascia hook `<aside class="sl-toc">` vuoto, lo Style Agent popola con JS)
- Profilo autore in fondo (avvocato linked)
- 3 articoli correlati
- CTA "Hai un caso simile?"

---

## Task 8 — `footer.php` (10 min)

Replica esattamente il footer del JSX:
- 3 colonne: Saltelli & Partners (links), Diciannove aree (multi-column), Contatti (NAP + PEC + ordine + P.IVA)
- Bottom row: copyright + privacy + cookie + Instagram + LinkedIn (testuali)
- Tema scuro: background `--primary`, text crema

Usa `wp_nav_menu` con theme_location `footer-studio`, `footer-aree`, `footer-legal`.

---

## Task 9 — Verifica finale (15 min)

```bash
# 1. Tema rendering correttamente
curl -s http://localhost:8080/ | grep -E "<h1|sl-hero|sl-areas|sl-team" | head -10

# 2. Single H1 per pagina
curl -s http://localhost:8080/ | grep -c "<h1"  # atteso: 1
curl -s http://localhost:8080/avvocati/emiliano-saltelli/ | grep -c "<h1" 2>/dev/null || true
curl -s http://localhost:8080/competenze/diritto-tributario/ | grep -c "<h1" 2>/dev/null || true

# 3. ACF field group importato (se ACF è attivo)
docker compose run --rm wpcli option get options_hero_headline 2>&1 | head -3

# 4. CPT funzionanti
docker compose run --rm wpcli post-type list --fields=name,public,has_archive 2>&1 | grep -E "avvocato|competenza"

# 5. Menu posizioni
docker compose run --rm wpcli menu location list 2>&1

# 6. PHP error log pulito
docker exec saltelli-wp tail -30 /var/www/html/wp-content/debug.log 2>/dev/null
```

Tutti devono passare. Se un test ACF fallisce perché il plugin non è attivo, NON è bloccante — segnala nel report.

---

## Coordinamento con gli altri agent

**File scope tuoi (esclusivi):**
- Tutti i template PHP root del tema (`header.php`, `footer.php`, `front-page.php`, `index.php`, `single*.php`, `archive*.php`, `page.php`, `404.php`, `searchform.php`, `search.php`)
- `inc/setup.php`
- `inc/cpt-avvocato.php`, `inc/cpt-competenza.php` (eventuale arricchimento)
- `inc/acf-fields.php` + `inc/acf-json/group_*.json`
- `inc/helpers.php`

**File NON tuoi:**
- `inc/enqueue.php` → Style Agent
- `inc/schema/` → GEO Engineer
- `inc/seo/` → GEO Engineer
- `assets/` → Style Agent

Se trovi che ti serve un campo ACF già usato dal GEO Engineer (es. `email_pubblica` per schema attorney), **mantieni lo stesso nome** già usato in `inc/schema/partial-attorney.php`.

---

## Report finale a Duccio

Quando hai finito:

1. ✅/❌ stato dei test del Task 9
2. Lista file creati/modificati
3. ACF Pro presente o no? (impatta Options Page e repeater)
4. Eventuali decisioni autonome prese (es. "Ho usato `menu_order` invece di campo custom `numero_ordinamento` perché più semplice")
5. Note per Style Agent (es. "Ho aggiunto data-attribute X sul `<h1>` per il SplitText")
6. Note per GEO Engineer (es. "Il telefono pubblico è in ACF field `contact_telefono_pubblico` su Options page")

Poi **fermati**. Aspetta istruzioni.

---

*v1.0 — 2026-04-29 SHIP MODE 24H — Direttore d'orchestra: Claude (chat).*
