# Pain Points Refinement Agent — Report finale

**Data:** 2026-04-30
**Theme version (in):** `0.6.0-beta-audit-aligned`
**Theme version (out):** `0.7.0-beta-pain-points-fixed`
**Tempo totale:** ~40 minuti (within budget 45-60 min)
**Modalità:** sequenziale obbligata, cache flush + curl test dopo OGNI fix

---

## 1 · Status 7/7 fix

| ID | Fix | Status | Approccio |
|---|---|:---:|---|
| **P0.1** | Layout /costi/ sbilanciato | ✅ | Riscritto blocco `.sl-costi__*` in `sections.css`: rimosso `padding-inline` duplicato (siamo già in `.sl-container`) + grid asimmetrico desktop (200px eyebrow sinistra · 1fr body destra, sticky-top eyebrow). Aggiunto blocco `.sl-page__*` per breadcrumb/title/prose. |
| **P0.2** | h2 ALL-CAPS in tier-1 + duplicate H1 | ✅ | Doppio approccio: (a) script PHP `fix-headings.php` su 19 CPT — convertito **14 `<h1>`** embedded in `<h2>` (fix duplicate H1) e **9 ALL-CAPS h2/h3** sentence-cased preservando sigle (INPS, IVA, IMU, RC ecc) e proper nouns (Napoli, Saltelli, Cassazione). (b) CSS guard `.sl-competenza__prose h2/h3 { text-transform: none, font-size: clamp(28-48px), font-weight: 400 }`. |
| **P0.3** | Hero overlap on scroll | ✅ | `.sl-hero` → `min-height: 100vh; padding-block: clamp(48-120px); overflow: visible`. `.sl-hero__inner` → grid `align-content: space-between` con `min-height: calc(100vh − padding)` per spingere colophon a fondo viewport. Desktop: `grid-template-columns: minmax(0, 8fr) minmax(0, 4fr)` con `align-items: stretch`. Niente più overlap perché eyebrow/main/colophon sono in righe separate del grid. |
| **P1.1** | Numerazione 19 aree | ✅ | `.sl-area__num { display: none }` mobile (la numerazione 01/19 mescola tier-1 e tier-2). Desktop: `display: inline-block` con `opacity: 0.4` per tier-2 e `opacity: 1; color: var(--accent)` per `.sl-area--tier1 .sl-area__num`. |
| **P1.2** | Drop-cap oro solo tier-1 | ✅ | Verifica: la regola `.sl-area--tier1 .sl-area__title::first-letter { color: var(--accent) }` era **già scoped correttamente** dall'inizio. Aggiunto reset esplicito `.sl-area:not(.sl-area--tier1) .sl-area__title::first-letter { color: inherit; font-size: inherit }` come paranoia idempotente. |
| **P1.3** | FAQ accordion +/- editoriale unificato | ✅ | Mismatch detected: `/costi/` usa `.sl-acc/.sl-acc__summary` markup, `/competenze/` usa `.sl-faq__item/.sl-faq__question`. Aggiunto blocco CSS unificato in `components.css` che target entrambi i pattern: `details > summary` con triangle browser nascosto (`-webkit-details-marker`, `::marker`) e icona `+/−` editoriale a destra (`color: var(--accent)`, font-mono 24px). State `[open]` switcha `+` → `−`. |
| **P1.4** | Subline "consulenza gratuita" softer | ✅ | `.sl-hero__cta-note` + `.sl-competenza__cta-note` da mono uppercase 11px → display italic 15px (no transform, no letter-spacing). `.sl-contact__eyebrow` mantiene mono uppercase ma ridotto a `font-size: 11px; letter-spacing: 0.06em` (meno aggressivo, coerenza con § 06 — CONTATTI). |

---

## 2 · Smoke test finale (6 URL)

| URL | HTTP | Bytes |
|---|:---:|---:|
| `/` | **200** | 84 997 |
| `/costi/` | **200** | 56 921 |
| `/competenze/` | **200** | 66 207 |
| `/competenze/diritto-tributario/` | **200** | 64 730 |
| `/avvocati/emiliano-saltelli/` | **200** | 60 614 |
| `/tipo-area/privati/` | **200** | 55 905 |

- Asset versioning: `tokens.css?ver=0.7.0-beta-pain-points-fixed` propagato
- `wp cache flush` + `wp transient delete --all` eseguiti
- PHP error log: **clean** (solo log Brevo/WonderPush plugin, non related)
- Cache flush + curl verify eseguiti **dopo OGNI fix individualmente** (rule fondamentale rispettata)

---

## 3 · Decisioni autonome

1. **P0.2 — Doppio approccio CSS + SQL/PHP** invece di solo CSS. Ragione: il CSS-only proposto dal prompt non risolveva il problema **duplicate H1** (i source content avevano `<h1>` embeddati che duplicano l'H1 del template — viola CLAUDE.md hard rule "1 H1 per page"). Lo script PHP `fix-headings.php` ha convertito 14 H1→H2 + sentence-cased 9 caps. Idempotente, supportato da CSS guard.

2. **P0.2 — `sentence_case_heading()` con preserve list per sigle e proper nouns**. Senza: "INPS" diventa "Inps", "IVA" diventa "Iva", "Napoli" diventa "napoli". Con: preserva ['INPS', 'IRPEF', 'IRES', 'IRAP', 'IMU', 'IVA', 'TARSU', 'GDPR', 'ATP', 'CTU', 'TAR', 'RC', 'LGBTQ+', 'NDA'] e ['Napoli', 'Italia', 'Saltelli', 'Chiaia', 'Cassazione', 'Tribunale', ...]. Trade-off: code più complesso ma output professionale.

3. **P0.3 — Adattato CSS al markup esistente** (`.sl-hero__inner` come grid container) invece di seguire alla lettera il prompt che assumeva `.sl-hero` come grid container con figli flat (eyebrow + main + colophon). Il template ha `.sl-hero > .sl-hero__inner > [eyebrow + main + colophon]` quindi il grid va su `.sl-hero__inner`. Mantiene la struttura PHP intatta = zero rischio regressione.

4. **P1.2 — Trovato che la regola era già corretta** (`.sl-area--tier1 .sl-area__title::first-letter` esisteva). Il "problema" del user (drop-cap oro su tutte) era probabilmente percezione visuale del Playfair Display "D" naturalmente ornato. Aggiunto comunque reset esplicito `:not(.sl-area--tier1)` come safety net + chiarezza intent.

5. **P1.3 — Class mismatch detected: due selettori distinti** (`.sl-acc` vs `.sl-faq`). Soluzione: blocco CSS unificato che group entrambi `.sl-faq__question, details.sl-acc > summary, .sl-acc__summary` per ogni regola (summary, ::after, hover, [open] state). No PHP markup change → zero rischio template.

6. **P1.4 — `.sl-contact__eyebrow` MANTENUTO mono uppercase** (vs italic serif come gli altri sublines). Ragione: l'eyebrow contatti vive accanto a "§ 06 — CONTATTI" mono — rendere italic serif romperebbe la coerenza tipografica del numero di sezione. Compromesso: tenuto mono ma ridotto `font-size: 11px` e `letter-spacing: 0.06em` (era 12px / 0.08em — meno aggressivo, più editoriale).

---

## 4 · Blocker / issue residui

**Nessun blocker.** Build pronta per visual walkthrough end-to-end del direttore d'orchestra.

Note minor (non blocker):

- **`recupero-crediti` post_content** ha un `<h2>Recupero Crediti Giudiziale</h2>` che era già title-case (no caps) ma con majuscole sui termini. Lo script l'ha lasciato com'era (ratio caps < 0.8) — corretto.
- **`/tipo-area/{slug}/` archive layout** ancora usa fallback `archive.php`. Il rendering è funzionale ma non identico a `archive-competenza.php`. Eventuale Step E (Template Polish) lo risolve con `taxonomy-tipo-area.php` dedicato. Fuori scope oggi.
- **Foto Emiliano `_thumbnail_id=2683`** + **bio_estesa avvocati** (Step D): **PRESERVATE**. Verificato — script `fix-headings.php` opera solo su `post_content`, mai su `_thumbnail_id` né su CPT avvocato.

---

## 5 · File modificati

```
M wp-content/themes/saltelli/assets/css/sections.css      (P0.1: .sl-page + .sl-costi grid asimmetrico · P0.2: .sl-competenza__prose h2/h3 size · P0.3: .sl-hero/.sl-hero__inner grid 100vh · P1.4: subline italic serif)
M wp-content/themes/saltelli/assets/css/components.css    (P1.1: .sl-area__num display:none mobile + tier-1 accent · P1.2: :not(.sl-area--tier1) reset first-letter · P1.3: .sl-faq + .sl-acc unified)
M wp-content/themes/saltelli/style.css                    (Version bump 0.6.0 → 0.7.0)
M wp-content/themes/saltelli/functions.php                (SALTELLI_THEME_VERSION bump)
?? .claude/knowledge/design/sessione-1/reports/pain-points-refinement/REPORT.md

// Solo DB (post_content rewrites P0.2):
db: 16/19 wp_posts (post_type=competenza) — post_content updated (14 H1→H2 + 9 ALL-CAPS sentence-cased)
```

**Niente modifiche a:** `front-page.php`, `single-competenza.php`, `single-avvocato.php`, `page.php` (template invariati). Tutti i fix sono CSS o PHP-script post-process.

**Foto e bio Step D preservate:** verificato.

---

## 6 · Tempo totale: **~40 minuti**

| Fix | Tempo |
|---|:---:|
| P0.1 | ~8 min (lettura page.php + replacement blocco costi + verify) |
| P0.2 | ~10 min (analisi DB + scrittura PHP script con sentence_case_heading + run + CSS guard + verify) |
| P0.3 | ~8 min (lettura markup esistente · adattamento CSS a `.sl-hero__inner` · verify) |
| P1.1 | ~3 min |
| P1.2 | ~2 min |
| P1.3 | ~6 min (analisi mismatch markup `.sl-acc` vs `.sl-faq` + scrittura blocco CSS unificato) |
| P1.4 | ~3 min |

---

*Step Pain Points Refinement completato. v0.7.0-beta-pain-points-fixed pronta per visual walkthrough end-to-end (12-point checklist) del direttore d'orchestra. Mi fermo qui.*
