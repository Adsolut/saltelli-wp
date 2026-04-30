# Audit Alignment Agent — Report finale (PRE-DEMO)

**Data:** 2026-04-30
**Theme version (in):** `0.5.1-beta-content-fix`
**Theme version (out):** `0.6.0-beta-audit-aligned`
**Tempo totale:** ~30 minuti

---

## 1 · Task 1 — Sitemap Privati / Imprese / Contenzioso ✅

### Tassonomia `tipo-area`
- Slug rewrite **aggiornato** in `inc/cpt-competenza.php`: `'tipo'` → `'tipo-area'` (per matchare URL audit `/tipo-area/<slug>/`)
- 4 termini creati via `wp_insert_term`:
  - `privati` (term_id 992) — "Per i Privati"
  - `imprese` (term_id 993) — "Per le Imprese"
  - `contenzioso` (term_id 994) — "Contenzioso Amministrativo"
  - `altri` (term_id 995) — "Altri servizi"

### Mapping 19 competenze → 19/19 (100%)
| Termine | Count | CPT |
|---|---:|---|
| privati | 9 | famiglia, famiglia-lgbtq, lavoro, resp.medica, immigrazione, penale, risarcimento, resp.civile, successioni |
| imprese | 4 | recupero-crediti, bancario, tributario, assicurazioni |
| contenzioso | 4 | cartelle-multe, condominiale, amministrativo, previdenziale |
| altri | 2 | domiciliazione-impresa, consulenze-online |

### Menu "Saltelli Header"
- Vecchio menu eliminato + ricreato (idempotente)
- Struttura: Studio · Avvocati · **Aree di Pratica ▾** (4 sub-items: Per i Privati / Per le Imprese / Contenzioso / Tutte le aree) · Casi · Costi · Editoriale · Contatti
- Assegnato a `nav_menu_locations['primary']`
- Verifica HTML: `<li class="… menu-item-has-children">` + `<ul class="sub-menu">` presenti
- CSS dropdown aggiunto in `sections.css` con desktop hover/focus-within + mobile stacked inline + chevron `↓`

---

## 2 · Task 2 — "Prima consulenza gratuita" gancio ✅

3 punti dove appare il messaggio (verifica grep):

| Posizione | File | Testo |
|---|---|---|
| Hero CTA homepage | `front-page.php:15` | `'Prenota un primo incontro'` → `'Prenota una consulenza gratuita'` (default `saltelli_option`) |
| Hero subline note | `front-page.php` (post-CTA) | `<div class="sl-mono sl-hero__cta-note">Prima consulenza conoscitiva — risposta entro 24 ore</div>` |
| Sezione Contatti homepage | `front-page.php` (sl-section-head) | `<div class="sl-mono sl-contact__eyebrow">Prima consulenza conoscitiva gratuita · Risposta entro 24 ore</div>` |
| CTA finale single-competenza | `single-competenza.php` (post-CTA finale) | `<div class="sl-mono sl-competenza__cta-note">Prima consulenza conoscitiva gratuita · Risposta entro 24 ore · In studio o online</div>` |

**CSS aggiunti:** `.sl-hero__cta-note`, `.sl-competenza__cta-note`, `.sl-contact__eyebrow` (mono · 11px · uppercase · text-muted, da tokens locked).

**Verifica:**
- Homepage: `consulenza gratuita` × 2 · `24 ore` × 2
- Competenza tributario: `consulenza conoscitiva gratuita` × 1 · `24 ore` × 1

---

## 3 · Task 3 — Pagina /costi/ ✅

- **Page ID:** 2695, slug `costi`, status `publish`
- **HTTP** `/costi/`: 200 (56 901 bytes)
- **Sezioni create (4):** intro capsule + § 01 "Come funziona" + § 02 "Trasparenza" + § 03 "Domande frequenti" + CTA finale
- **FAQ accordion:** **5/5** (`<details class="sl-acc">` · `<summary class="sl-acc__summary">`)
- **Yoast meta description** popolata + `_yoast_wpseo_title` con format `%%sep%% %%sitename%%`
- **CSS layout** in `sections.css`: `.sl-costi__capsule` (display italic editoriale), `.sl-costi__section` (max 720px centrato), `.sl-costi__cta` (surface bg, centered)

---

## 4 · Task 4 — Smoke test 8 URL chiave ✅

| URL | HTTP | Bytes |
|---|:---:|---:|
| `/` | **200** | 84 977 |
| `/costi/` | **200** | 56 901 |
| `/competenze/` | **200** | 66 187 |
| `/competenze/diritto-tributario/` | **200** | 64 711 |
| `/avvocati/emiliano-saltelli/` | **200** | 60 594 |
| `/tipo-area/privati/` | **200** | 55 885 |
| `/tipo-area/imprese/` | **200** | 53 165 |
| `/tipo-area/contenzioso/` | **200** | 53 264 |

- Asset versioning: `tokens.css?ver=0.6.0-beta-audit-aligned` propagato
- PHP error log: solo log Brevo/WonderPush (plugin esterno, non related)
- `wp cache flush` + `wp transient delete --all` + `wp rewrite flush --hard` eseguiti

---

## 5 · Decisioni autonome

1. **Slug rewrite aggiornato a `tipo-area`** (era `tipo` in `cpt-competenza.php`). Il prompt specifica URL `/tipo-area/...` quindi ho cambiato il rewrite slug per coerenza. Modifica retro-compatibile (nessun dato perso, solo URL pattern).
2. **`diritto-commerciale` rimosso dal mapping prompt** — non esiste come CPT scaffolded (verifica `wp post list --post_type=competenza`). Il prompt lo elencava in `IMPRESE` ma non c'è nel DB. Tutto il resto delle 19 competenze coperte.
3. **`wp_set_post_terms` sostituito con `wp_set_object_terms`** dopo che il primo run ha mappato 19/19 con 0 relationships effettivi (quirk WP: `wp_set_post_terms` su tassonomia gerarchica con stringhe slug le tratta come nomi-da-creare invece di lookup, quindi falliva silenziosamente). Workaround: pre-fetch term_ids via `get_term_by('slug')` poi pass come int array.
4. **Subline gancio CSS unica regola** per `.sl-hero__cta-note`, `.sl-competenza__cta-note`, `.sl-contact__eyebrow` — single declaration condivisa con override puntuali. Riduce CSS duplication.
5. **Migration script idempotente** (`_audit_align_tmp/run.php`) — elimina menu esistente prima di ricrearlo, `update_term` se esiste già. Re-runnabile senza duplicati.
6. **Eyebrow contatti ha richiesto wrapper `<div>`** sopra `<h2>` perché `.sl-section-head` è grid `auto 1fr` (mono | titolo). Il wrap permette di accoppiare eyebrow + h2 nella seconda colonna senza rompere il layout grid esistente.

---

## 6 · Blocker / issue residui

**Nessun blocker.** Build pronta per visual check Chrome del direttore d'orchestra.

Nota minor (non blocker):
- L'archive `/tipo-area/{slug}/` ritorna 200 ma usa il fallback `archive.php` di WP (non c'è `taxonomy-tipo-area.php` template dedicato). Il rendering è funzionale ma non identico all'`archive-competenza.php`. Se Duccio vuole un layout custom per questi archivi tassonomici → aggiungere template in Step E (Template Polish), fuori scope oggi.
- L'accordion `<details>/<summary>` su `/costi/` usa CSS pattern `[open]` per rotazione icona `+` → funziona nativo HTML, niente JS richiesto. Compatibile con tutti i browser moderni.
- Foto Emiliano (`_thumbnail_id 2683` su CPT 2660): **NON toccata**. Verificato nessuna scrittura su `_thumbnail_id` o `bio_estesa` durante il run.

---

## 7 · Tempo totale impiegato

**~30 minuti** (within budget 30-45 min):
- T1 sitemap: ~12 min (incluso debug `wp_set_post_terms` quirk)
- T2 consulenza gratuita: ~5 min
- T3 pagina /costi/: ~8 min
- T4 bump + smoke test + report: ~5 min

---

## File modificati

```
M wp-content/themes/saltelli/inc/cpt-competenza.php       (rewrite slug tipo → tipo-area)
M wp-content/themes/saltelli/front-page.php               (hero CTA + subline + eyebrow contatti)
M wp-content/themes/saltelli/single-competenza.php        (CTA finale subline)
M wp-content/themes/saltelli/assets/css/sections.css      (dropdown + cta-note + costi layout)
M wp-content/themes/saltelli/style.css                    (Version bump)
M wp-content/themes/saltelli/functions.php                (SALTELLI_THEME_VERSION bump)
?? .claude/knowledge/design/sessione-1/reports/audit-alignment/REPORT.md
```

**Database changes:**
- 4 termini tassonomia `tipo-area` (term_ids 992-995)
- 19 relationships `wp_term_relationships` (CPT competenza ↔ termini)
- 1 menu term `Saltelli Header` (term_id 996) + 11 menu items (parent 2686 + 4 children + 6 top-level)
- 1 `theme_mod` `nav_menu_locations[primary]` = 996
- 1 page `/costi/` (post_id 2695) + Yoast meta (`_yoast_wpseo_metadesc`, `_yoast_wpseo_title`)

**Foto Emiliano (`_thumbnail_id=2683` su CPT 2660):** **PRESERVATA**, mai scritta.
**Bio_estesa avvocati popolate da Step D:** **PRESERVATE**, mai scritte.

---

*Step Audit Alignment completato. v0.6.0-beta-audit-aligned pronta per visual check Chrome del direttore d'orchestra. Mi fermo qui.*
