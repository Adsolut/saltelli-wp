# Wave 3 · Task 8 · `/tipo-area/{slug}/` taxonomy — REPORT

**Branch:** `feat/wave3-task-08`
**Date:** 2026-05-01
**Scope:** rewrite `taxonomy-tipo-area.php` to match LAYOUT SACRO `saltelli-s2-taxonomy-tipo-area.jsx`, scoped CSS in `sections.css` between WAVE3 TASK 8 markers.
**Files touched:**
- `wp-content/themes/saltelli/taxonomy-tipo-area.php` (rewrite)
- `wp-content/themes/saltelli/assets/css/sections.css` (scoped block only)

NO-TOUCH respected: `tokens.css`, `functions.php`, `style.css`, `page.php`, `home.php`, `single-*.php`.

## Layout sezioni implementate (5/5)

1. **Hero asimmetrico 8/4** — sx breadcrumb mono + h1 Playfair `clamp(56px, 8vw, 132px)` + lede italic `clamp(18, 2vw, 24)` + counter mono "N aree di pratica". Dx aside con eyebrow `Avvocati di riferimento` + 1-2 mini-card (foto 80x80 grayscale → color on hover + nome Playfair + role mono "→"). Mobile: collapse single column.
2. **Quando rivolgersi** — eyebrow `§ 01 — Quando rivolgersi` + h2 "Tre scenari / *tipici.*" + 3-col grid scenari (sym Playfair italic accent + h3 + p), border-top accent.
3. **Lista aree** — eyebrow `§ 02 — Aree di pratica` + h2 "N aree." + lista `.sl-area` riusata da pattern homepage/archive. Tier-1 first ordering (meta_value_num DESC) + ★ prefix accent.
4. **Casi rappresentativi cluster** — eyebrow `§ 03 — Casi rappresentativi` + h2 "N vittorie per {cluster}." + lista 3 col (id mono · desc Playfair italic · outcome accent right). Filtro dinamico su `saltelli_all_cases()` per `cat = Privati|Imprese|Contenzioso|Altri`.
5. **CTA finale** — eyebrow `§ 04 — Primo incontro` + h2 "Hai una pratica / *simile?*" 96px + lede italic 22px + `.sl-btn--primary` "Prenota gratuita" → `/contatti/`.

## Avvocati di riferimento — derivazione dinamica

Aggrega `lead_attorneys` di tutte le competenze del cluster, frequenza-ordina, prendi top 2. Fallback editoriale per slug se vuoto:

| cluster | top 2 ottenuti |
|---|---|
| privati | Antonia Battista, Fabiana Saltelli |
| imprese | (auto da lead_attorneys delle 4 competenze) |
| contenzioso | (auto da lead_attorneys delle 4 competenze) |
| altri | Emiliano Saltelli (fallback editoriale, 1 solo) |

## Scenari "Quando rivolgersi"

Dataset hardcoded per cluster (`$scenari_map` nel template), 3 voci per cluster. Sym glyph: `§ ¶ †` Playfair italic accent.

## Schema JSON-LD

Yoast SEO v27.4 emette già `CollectionPage` + `BreadcrumbList` su term archive (verificato su `/wp-json/wp/v2/tipo-area`). Per rispettare la regola CLAUDE.md "no duplicati con Yoast" emettiamo **solo** `ItemList` di `LegalService` — additivo non duplicativo. Schema chunk:

```json
{
  "@context": "https://schema.org",
  "@type": "ItemList",
  "@id": "<term_url>#itemlist",
  "name": "Aree di pratica — <term name>",
  "numberOfItems": <count>,
  "itemListOrder": "https://schema.org/ItemListOrderAscending",
  "itemListElement": [
    { "@type": "ListItem", "position": N,
      "item": { "@type": "LegalService", "@id": "<perm>#legalservice", "name": "...", "url": "...",
                 "provider": { "@type": "Organization", "@id": "<home>#organization", "name": "..." },
                 "areaServed": { "@type": "Place", "name": "Italia" } } },
    ...
  ]
}
```

ASCII-safe via `saltelli_emit_jsonld()` (Iubenda DOMDocument round-trip safe — vedi memory `feedback_jsonld_iubenda`).

## Smoke test 4/4 termini

Eseguito locale `http://localhost:8080`:

| URL | HTTP | sl-tipoarea-- | aree | casi | attorneys | ItemList |
|---|---|---|---|---|---|---|
| `/tipo-area/privati/` | 200 | privati | 9 | 3 | 2 | ✓ |
| `/tipo-area/imprese/` | 200 | imprese | 4 | 4 | 2 | ✓ |
| `/tipo-area/contenzioso/` | 200 | contenzioso | 4 | 2 | 2 | ✓ |
| `/tipo-area/altri/` | 200 | altri | 2 | 1 | 1 | ✓ |

Breadcrumb verificato: `Home / Aree di pratica / <Term name>` su tutti 4.
PHP lint: `php -l` no syntax errors.

## CSS scope

Tutto namespaced sotto `.sl-tipoarea` + BEM modifier `.sl-tipoarea--{slug}` per zero overlap. Reuse:
- `.sl-container` (layout wrapper)
- `.sl-page__breadcrumb` (helper centralizzato)
- `.sl-mono` (typography utility)
- `.sl-btn` / `.sl-btn--primary` (button system)

Mobile-first: hero 1col → 8/4 desktop @1024, scenari 1col → 3col @768, caso 1col → 240/1fr/200 @1024.

## Hard rules respect

- LAYOUT JSX SACRO ✓ (5/5 sezioni nell'ordine, copy match dataset privati, generalized per altri 3)
- NO-TOUCH lock files ✓
- CSS scope marker BEGIN/END ✓
- Yoast coabitation ✓ (no CollectionPage/Breadcrumb duplicate)
- `_thumbnail_id`, `bio_estesa`, tokens.css, config.local.json non toccati ✓

## Decisioni autonome

1. **Schema scope**: Yoast già emette CollectionPage → emessa solo ItemList come additivo (non in spec ma in linea con CLAUDE.md).
2. **Avvocati derivati dinamicamente** dai `lead_attorneys` invece di hardcode per cluster: scala se cambiano team. Fallback per cluster se nessuna match.
3. **Dataset scenari** generalizzato 3 voci anche per `imprese`, `contenzioso`, `altri` (JSX aveva solo `privati`).
4. **Casi cluster source**: riuso `saltelli_all_cases()` con filtro `cat`, no nuova tabella.
5. **Foto avvocati**: usa `wp_get_attachment_image(120x120)` con fallback gradient quando `_thumbnail_id` mancante (privati attualmente solo Emiliano ha foto).

## STOP.
