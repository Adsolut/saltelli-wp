# Wave 4.7.fix.5 — Phase 3: Blog editing map (audit completo)

**Data:** 2026-05-11 · **Scope:** mappa esaustiva di ogni elemento visibile sul frontend dei template blog (`single.php`, `home.php`, `archive.php`) → sorgente + dove si edita in WP-Admin. **Niente refactor codice** — questa è documentazione + 2 notice admin (vedi `inc/admin/post-editor-notices.php`).

**Esito alto livello:** il blog è **100% WordPress standard** (editor Gutenberg + sidebar "Documento"). Nessun SCF metabox dedicato come per le 12 Pages target. L'header del blog archive (`home.php`) e la CTA in fondo ai post sono **hardcoded nel tema** (non editabili da admin) — questo è by design.

---

## A. Articolo singolo — `single.php` (`/risorse/blog/{slug}/`)

| # | Elemento frontend | File:linea | Sorgente | Editabile? | Dove in WP-Admin |
|---|---|---|---|---|---|
| 1 | **H1 titolo** `<h1 class="sl-post__title">` | `single.php:68` | `get_the_title()` | ✅ Sì | Articoli → [articolo] → campo Titolo (in cima all'editor) |
| 2 | **Lede italico** `<p class="sl-post__lede">` (paragrafo grande sotto il titolo) | `single.php:71-75` | `get_the_excerpt()` | ✅ Sì | Articoli → [articolo] → sidebar "Documento" → pannello **"Riassunto"** (a volte "Estratto"). **Se vuoto → sotto il titolo non appare nulla.** ⚠️ è il campo più dimenticato |
| 3 | **Eyebrow** `← Editoriale` (link "torna al blog") | `single.php:38-40` | stringa hardcoded `__('Editoriale', 'saltelli')` + link `home_url('/blog/')` | ❌ No | — (vedi §D — out of scope) |
| 4 | **Breadcrumb** `Home / Editoriale / {Categoria} / {Titolo}` | `single.php:36` → `saltelli_render_breadcrumb()` → `saltelli_get_breadcrumb_chain()` (`inc/helpers.php:165`) | **theme-generated** (NON Yoast): segmento "Editoriale" = `get_the_title( get_option('page_for_posts') )` cioè il titolo della Page "Blog" (ID 1413); "{Categoria}" = `get_the_category()[0]`; "{Titolo}" = `get_the_title()` | ⚠️ indirettamente | Cambia la **categoria** → cambia il 3° segmento. Cambia il **titolo** → cambia l'ultimo. Per il segmento "Editoriale": Pagine → "Blog" (ID 1413) → titolo. (Lo schema JSON-LD BreadcrumbList è emesso da `inc/schema/schema-loader.php`, non da questo HTML.) |
| 5 | **Categoria** `<a class="sl-post__cat">` (in maiuscolo, nel meta sopra il titolo) | `single.php:43-46` | `get_the_category()[0]->name` (uppercase) | ✅ Sì | Articoli → [articolo] → sidebar → pannello **"Categorie"**. Scegline **una** principale (il template usa solo la prima) |
| 6 | **Data** `13 LUGLIO 2025` | `single.php:47` | `get_the_date()` | ✅ Sì | Articoli → [articolo] → sidebar → pannello "Stato e visibilità" / "Pubblica" → data di pubblicazione |
| 7 | **Autore** `Avv. Antonia Battista` (linkato alla scheda avvocato se il nome combacia) | `single.php:17-19,49-57` | `get_the_author_meta('display_name')`; link al CPT `avvocato` se `get_posts(['post_type'=>'avvocato','s'=>$author_name])` trova match | ✅ Sì | Articoli → [articolo] → sidebar → pannello **"Autore"** → scegli l'utente avvocato (UID 4 Antonia Battista, 5 Fabiana Saltelli, 7 Stefano G. Tedesco, 6 Gabriele Cascone). Il `display_name` dell'utente deve combaciare col titolo del CPT avvocato per il link |
| 8 | **Byline ricca** (bio estesa + tag temi competenza) `<div class="sl-author-byline">` | `single.php:78-100` | se autore→avvocato: `byline_extended` (testo) + `expertise_topics` (post_object multi → CPT competenza) della scheda avvocato | ✅ Sì | Avvocati → [avvocato] → metabox SCF → campi "byline_extended" e "expertise_topics" |
| 9 | **Immagine in evidenza** (hero del post, LCP) `<figure class="sl-post__featured">` | `single.php:104-109` | `the_post_thumbnail('saltelli-hero', ['loading'=>'eager','fetchpriority'=>'high'])` | ✅ Sì | Articoli → [articolo] → sidebar → pannello **"Immagine in evidenza"**. Praticamente obbligatoria: senza, il post non ha hero |
| 10 | **Indice articolo** (TOC) `<aside class="sl-toc" data-toc>` | `single.php:113-116` | popolato da JS (scroll-spy sui `<h2>`/`<h3>` dentro `.sl-post__body`) | ⚠️ indirettamente | I titoli H2/H3 che metti nel **corpo dell'articolo** generano automaticamente l'indice |
| 11 | **Corpo articolo** `<div class="sl-post__body" data-drop-cap>` | `single.php:118-121` | `the_content()` | ✅ Sì | Articoli → [articolo] → area blocchi Gutenberg (il corpo principale) |
| 12 | **Card autore in fondo** (ritratto + nome + ruolo + bio breve) `<section class="sl-post__author-card">` | `single.php:124-157` | se autore→avvocato: thumbnail + titolo CPT + `ruolo_breve` + `bio_breve` della scheda avvocato | ✅ Sì | Avvocati → [avvocato] → Immagine in evidenza + metabox SCF "ruolo_breve" / "bio_breve" |
| 13 | **Continua a leggere** (3 articoli correlati) `<section class="sl-post__related">` | `single.php:159-182` | automatico: `WP_Query` 3 post più recenti nella stessa categoria | ❌ No (auto) | — (controlli la categoria → controlli quali post sono "correlabili") |
| 14 | **CTA "Hai un caso simile?"** + bottone "Prenota un primo incontro" → `/contatti/` | `single.php:184-190` | stringhe hardcoded nel tema | ❌ No | — (vedi §D — out of scope) |
| 15 | **Tag WP del post** | — | — | — | ⚠️ **NON visualizzati**: il template `single.php` non rende i tag (`get_the_tags()`). I tag esistono nel DB ma non appaiono sul frontend del post. (Esistono pagine archivio per tag — `archive.php` — raggiungibili solo via URL diretto `/risorse/blog/tag/{slug}/`.) |
| 16 | **Tempo di lettura** `3 MIN` | `single.php:14,59-65` | `saltelli_reading_time($id)` = `ceil( str_word_count(strip_tags(post_content)) / 200 )`, min 1 | ❌ No (calcolato) | — auto dal numero di parole del corpo. ⚠️ minor: `home.php` (archivio) usa una formula leggermente diversa (`max(2, round($words/220))`, 220 wpm min 2) → stesso post può mostrare "X min" diverso tra archivio e singolo. Out of scope (no refactor). |
| 17 | **Titolo `<title>` + meta description + OG** | (Yoast) | Yoast SEO | ✅ Sì | Articoli → [articolo] → box Yoast SEO sotto l'editor (snippet anteprima Google) |

### Schema JSON-LD su single post
`inc/schema/partial-article.php` emette `Article` (solo se Yoast NON attivo per quel nodo — coabitazione). Yoast attivo → Yoast gestisce Article/BreadcrumbList. Non editabile direttamente; deriva dai campi sopra (titolo, autore, data, featured image, categoria).

---

## B. Archivio blog — `home.php` (`/risorse/blog/`, è la Page "Blog" ID 1413 = `page_for_posts`)

⚠️ **Il contenuto della Page WP "Blog" (ID 1413) NON è usato dal frontend** — `home.php` ignora `post_content`. Solo il **titolo** della Page 1413 è usato (per il segmento breadcrumb "Editoriale"). Per questo è stato aggiunto un notice admin sulla Page 1413 (vedi `inc/admin/post-editor-notices.php`).

| # | Elemento frontend | File:linea | Sorgente | Editabile? | Dove |
|---|---|---|---|---|---|
| 1 | Breadcrumb | `home.php:58` | `saltelli_render_breadcrumb()` | ⚠️ | come §A.4 |
| 2 | Eyebrow `§ Editoriale · Saltelli` | `home.php:59` | hardcoded | ❌ No | — |
| 3 | **H1** `Editoriale.` | `home.php:60` | hardcoded `saltelli_split_h1_words('Editoriale.')` | ❌ No | — (out of scope) |
| 4 | Lede destra `Articoli, casi vinti, novità giurisprudenziali…` | `home.php:63-65` | hardcoded | ❌ No | — |
| 5 | Counter `X articoli · Y categorie · agg. Z` | `home.php:66-73` | automatico (`found_posts`, `wp_count_terms`, `get_lastpostmodified`) | ❌ No (auto) | — (cambia pubblicando articoli) |
| 6 | Tab categorie (sticky) | `home.php:78-91` | automatico: `get_categories(['orderby'=>'count','number'=>7])` | ⚠️ | i nomi/ordine vengono dalle Categorie (Articoli → Categorie). Mostra le 7 con più articoli |
| 7 | Card "In evidenza" (post più recente, grande) | `home.php:93-132` | automatico = 1° post (più recente) | ❌ No (auto) | — (l'articolo più recente diventa il featured. Per cambiarlo: pubblica/aggiorna un articolo) |
| 8 | Griglia 3-col "Tutti gli articoli" | `home.php:135-185` | automatico = post 2…N, paginati | ❌ No (auto) | — |
| 9 | Card griglia: thumb, categoria, titolo, estratto, meta | `home.php:162-180` | per ogni post: `the_post_thumbnail_url('medium_large')`, `get_the_category()[0]`, `get_the_title()`, `get_the_excerpt()`, autore, reading-time | ✅ via il singolo articolo | come §A (titolo, estratto, immagine in evidenza, categoria, autore del rispettivo articolo) |
| 10 | Paginazione `← Precedenti / X—Y di Z / Successivi →` | `home.php:188-215` | automatica | ❌ No (auto) | — |
| 11 | Newsletter inline (`§ Newsletter / Un articolo al mese.` + form) | `home.php:217-240` | copy hardcoded; form `POST → /contatti/` | ❌ No | — (out of scope; nota: il form newsletter posta su `/contatti/`, non c'è integrazione mailing list dedicata nel tema) |
| 12 | Schema `Blog` + `ItemList` JSON-LD | `home.php:245-290` | auto da `saltelli_emit_jsonld()` | ❌ No | — |

---

## C. Archivio per categoria / tag / autore / data — `archive.php`

URL: `/risorse/blog/category/{slug}/`, `/risorse/blog/tag/{slug}/`, `/risorse/blog/author/{slug}/`, `/risorse/blog/{anno}/{mese}/` (rewrite Wave 5; legacy `/category/...` ecc. → 301 → `/risorse/blog/category/...`). NB: usato anche da CPT archive `saltelli_caso` ma con override `archive-saltelli_caso.php`.

| # | Elemento | File:linea | Sorgente | Editabile? | Dove |
|---|---|---|---|---|---|
| 1 | Eyebrow `Editoriale · Categoria/Tag/Autore/Archivio` | `archive.php:14-23` | hardcoded condizionale | ❌ No | — |
| 2 | **H1** = nome termine / autore / data | `archive.php:24-38` | `single_term_title()` / `get_the_author()` / `get_the_date('F Y')` | ✅ via il termine | Categorie/Tag: Articoli → Categorie/Tag → [termine] → Nome. Autore: Utenti → [utente] → Nome |
| 3 | Lede archivio (descrizione termine) | `archive.php:39-44` | `term_description()` | ✅ Sì | Articoli → Categorie/Tag → [termine] → campo Descrizione |
| 4 | Lista post (data, categoria, titolo, estratto) | `archive.php:55-85` | loop standard | ✅ via singolo articolo | come §A |
| 5 | Paginazione | `archive.php:87-93` | `the_posts_pagination()` | ❌ No (auto) | — |

---

## D. Elementi hardcoded nel tema (NON editabili da admin) — out of scope questa wave

Questi sono nel codice del tema; renderli editabili = wave futura (NON implementato ora, niente refactor):

- **Eyebrow "← Editoriale"** sul singolo post (`single.php:39`) — stringa `__('Editoriale', 'saltelli')`, link `/blog/`. Stessa stringa "Editoriale" usata anche in `archive.php`, `single-competenza.php`, `single-avvocato.php`, `404.php`, `footer.php`, `home.php`. Se Duccio vuole renderla variabile (es. "← Longform" per certi articoli) → richiede campo SCF su `post` + condizionale nel template = **wave futura**.
- **H1 "Editoriale."** sull'archivio blog (`home.php:60`) — hardcoded.
- **Lede archivio + copy newsletter** (`home.php:63-65, 217-240`) — hardcoded.
- **CTA "Hai un caso simile? / Prenota un primo incontro"** in fondo ai post (`single.php:184-190`) — stringhe + link `/contatti/` hardcoded.
- **Reading-time formula inconsistente** single (200 wpm, min 1) vs archivio (220 wpm, min 2) — minor, no refactor.

---

## E. Micro-fix admin implementato (Phase 3.B) — `inc/admin/post-editor-notices.php`

PHP-only, niente JS, niente refactor. **Deviazione vs prompt**: il prompt suggeriva `post_submitbox_misc_actions` ma quell'hook NON scatta in Gutenberg (è classic editor) e gli Articoli usano Gutenberg → usato `admin_notices` (banner in cima all'editor, dismissibile).

1. **Banner su editor Articolo** (`post.php`/`post-new.php`, `post_type=post`): promemoria una-riga della mappatura sidebar→frontend per Estratto/lede, Immagine in evidenza, Autore, Categoria + nota reading-time auto-calcolato.
2. **Banner su editor Page "Blog" (ID 1413 = `page_for_posts`)**: avvisa che il contenuto di quella Page non è mostrato sul frontend (il blog è generato dal template) + link diretto a → Articoli.

Caricato in `functions.php` dopo gli admin file di Wave 4.7.fix.4.
