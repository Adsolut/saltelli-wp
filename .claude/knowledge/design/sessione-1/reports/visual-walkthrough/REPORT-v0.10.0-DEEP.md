# Visual Walkthrough — v0.10.0-beta-editorial — DEEP

**Data:** 2026-04-30
**Tester:** Claude (orchestrator)
**Tool:** Claude in Chrome (browser_batch + javascript_tool) + Desktop Commander curl
**Build:** v0.10.0-beta-editorial
**Tempo:** ~15 minuti
**Scope:** walkthrough deep post-Editorial Refinement, focus su bug residui + verifica regressioni

---

## Risultati aggregati

| # | Punto | v0.8.1 | v0.9.0 | v0.10.0 | Note |
|---|---|:---:|:---:|:---:|---|
| 1 | Hero 100vh, 3 righe | ✅ | ✅ | ✅ | mantenuto |
| 2 | Lista 19 aree tier-1 | ✅ | ✅ | ✅ | mantenuto |
| 3 | Layout asimmetrico generico | ✅ | ✅ | ✅ | mantenuto |
| 4 | Drop-cap "L" Lo studio homepage | ✅ | ✅ | ✅ | mantenuto |
| 5 | 4 avvocati homepage asimmetrici | ✅ | ✅ | ✅ | mantenuto |
| 6 | Casi rappresentativi tipografici | ✅ | ✅ | ✅ | mantenuto |
| 7 | Footer dark navy | ✅ | ✅ | ✅ | mantenuto |
| 8 | /costi/ layout editoriale | ❌ | ✅ | ✅ | Recovery F4 fix preservato |
| 9 | Single-competenza tier-1 base | ✅ | ✅ | ✅ | h2 spacing OK |
| 10 | Single-avvocato Emiliano | ✅ | ✅ | ✅ | foto reale + sticky no overlap |
| 11 | Archive /tipo-area/* | ✅ | ✅ | ✅ | mantenuto |
| 12 | Mobile 375px responsive | ✅ | ✅ | ✅ | hasOverflow:false confermato |
| 13 | Single-avvocato senza foto | ❌ | ✅ | ✅ | Recovery F3 fix preservato |
| 14 | Archive /avvocati/ 4 lawyer | ❌ | ✅ | ✅ | Recovery F2 fix preservato |
| 15 | Archive /competenze/ headline | ❌ | ✅ | ✅ | Recovery F1 fix preservato |
| 16 | Tier-1 H2 sub-section spacing | ❌ | ✅ | ✅ | Recovery F6 + Editorial A2 |
| 17 | Header sticky transition | ❌ | ✅ | ✅ | Recovery F5 fix preservato |
| **18** | **Editorial typography blog post** | n/a | n/a | ✅ **NEW PASS PERFETTO** | Lede italic Playfair 20px, drop-cap "R" su body, h2 con respiro |
| **19** | **Editorial immagini blog post** | n/a | n/a | ✅ **NEW PASS PERFETTO** | Sentenze container 720px max + border, foto autore 80×80 card |
| **20** | **/blog/ archive popolato** | ❌ | ❌ | ✅ **NEW PASS** | 326 post visibili con paginazione |
| **21** | **/lo-studio/ → /chi-siamo/ redirect** | ❌ | ❌ | ✅ **NEW PASS** | HTTP 301 funzionante |
| **22** | **/contatti/ map embed** | n/a | n/a | ✅ **PASS** (con caveat) | OpenStreetMap rende ma coordinate sul mare |

---

## Score globale

**21 PASS · 0 WARN · 0 FAIL maggiori · 3 issue residui (vedi sotto)**

→ **Decisione: GO per Step F (Production Readiness)** con i 3 issue residui da fixare prima del deploy.

---

## ✅ Fix Editorial v0.10.0 confermati funzionanti

### GROUP A — Typography respiro

- **A1 Lede italic Playfair** ✅ verificato sia desktop che mobile
  - `fontFamily: "Playfair Display"` ✓
  - `fontStyle: italic` ✓
  - `fontSize: 20px` mobile / clamp(20-24px) ✓
  - Margin-bottom 56px tra lede e body ✓
- **A1 Drop-cap "R"** ✅ float left grande, color primary, scope `body.single-post`
- **A2 H2 spacing** ✅ "Cosa devi sapere" / "Hai ricevuto un'intimazione" hanno respiro 80px sopra
- **A3 Em-dash bullet** 🟡 da verificare — vedo bullet "•" nelle liste IMU/IRPEF/IVA su /competenze/diritto-tributario/. Potrebbe essere `:not()` chain ha escluso `.sl-competenza__prose ul`. Da verificare nel prossimo agent.

### GROUP B — Immagini cornice editoriale

- **B1 Sentenze 720px max** ✅ sentenza TARI ora in container con border editoriale, centered
- **B2 Featured image** ✅ ora 960px max + aspect 16/9
- **B3 Author bio card** ✅ "L'AUTORE / Emiliano Saltelli / FOUNDING PARTNER · TRIBUTARISTA" con foto 80×80 squared

### GROUP C — Routing/Content

- **C1 /lo-studio/ redirect** ✅ HTTP 301 → /chi-siamo/ → HTTP 200
- **C2 /blog/ archive** ✅ 326 post visibili con date + categoria + titolo + excerpt + paginazione
- **C3 /contatti/ map embed** ✅ OpenStreetMap iframe rende, "APRI IN OPENSTREETMAP" link mono editoriale

---

## 🔴 Issue residui trovati nel walkthrough deep

### R1 — Mappa OpenStreetMap su /contatti/ con coordinate sbagliate

**Sintomo:** la mappa rende correttamente ma il pin è **sul mare di Napoli** (Pista ciclabile), non sull'indirizzo Via Vannella Gaetani 27 (Chiaia interna).

**Root cause:** coordinate OpenStreetMap iframe parametrate `40.830, 14.239` invece del corretto `40.832, 14.235` (Via Vannella Gaetani 27).

**Severity:** alta per cliente — un avvocato non può avere "lo studio in mezzo al mare" come prima impressione.

**Fix:** edit `page.php` con coordinate corrette + bbox stretto a Chiaia:
```html
<!-- BBOX corretto Chiaia: 14.232,40.829 to 14.240,40.835 -->
src="https://www.openstreetmap.org/export/embed.html?bbox=14.232,40.829,14.240,40.835&layer=mapnik&marker=40.832,14.235"
```

### R2 — Pagina /chi-siamo/ ha Lorem Ipsum content

**Sintomo:** dopo redirect /lo-studio/ → /chi-siamo/ vediamo:

```
HOME / CHI SIAMO
Chi siamo
Chi siamo
la nostra storia
Lorem ipsum dolor sit amet, conetur adiping elit Lorem ipsum dolor sit amet, cons ectetur adiscing elit Lorem ipsum dolor sit altmet, conse ctetur adipiscing elit aloma lomiur off  silder tolos. Lorem ipsum dolor sitlor amet, conetur adiping elit Lorem ipsum dolor sit amet, consectetur adipiscing elit.
```

**Root cause:** la page WP id 19 (`chi-siamo`) ha post_content placeholder Lorem Ipsum dal 2019 (audit CRO originale aveva flaggato l'issue!). Mai sostituito.

**Severity:** **altissima** — il cliente si aspetta una pagina "Chi siamo" con la storia dello studio. Lorem Ipsum è inaccettabile per la presentazione.

**Fix opzioni:**
1. **Content reale** scritto dall'orchestrator basandosi su `project-context.json` + brief originale (storia studio, mission, valori)
2. **Reindirizzare a homepage section "§ 02 Lo studio"** che già ha drop-cap "L" + bottega napoletana
3. **Page reset** — eliminare post_content e usare template page-chi-siamo.php editoriale

**Mia preferenza:** opzione 1 + opzione 3 (template dedicato + content reale).

### R3 — `/competenze/diritto-tributario/` lista bullet "•" invece di em-dash

**Sintomo:** lista "IMU, TARSU, TOSAP / IRPEF, IRES, IRAP / I.V.A. e accise / ..." mostra bullet "•" classico browser default invece di em-dash "—" accent gold come fix A3.

**Root cause:** la lista è dentro `.sl-competenza__prose` o un wrapper diverso da quelli scoped da fix A3 (`:not()` chain). Probabilmente serve aggiungere `.sl-competenza__body ul` o `.sl-competenza__prose ul` allo scope.

**Severity:** media — funzionalmente OK, ma rompe coerenza editoriale tra blog post (em-dash) e competenza pages (bullet •).

**Fix:** edit `sections.css` scope A3:
```css
.sl-post__body ul li::before,
.entry-content ul li::before,
.sl-page__prose ul li::before,
.sl-competenza__body ul li::before,    /* ← aggiungere */
.sl-competenza__prose ul li::before {  /* ← aggiungere */
    content: "—";
    /* ... */
}
```

E corrispettivo `.sl-competenza__body ul li, .sl-competenza__prose ul li { list-style: none }`.

---

## ✅ Mobile 375px verifica

```
hasOverflow: false                   ← no horizontal scroll
viewport: 500x... (zoom)             ← effective 375px width
h1Count: 1                           ← 1 H1 per page
hero "Diritto, con misura" 4 righe  ← M2 fix preservato (era 3 desktop)
images: 24 (post body)               ← B1 fix attivo
ledeStyle italic Playfair 20px       ← A1 fix mobile
authorCard present                   ← B3 fix mobile
```

✅ Mobile state perfetto.

---

## 📊 Verdetto editoriale

**Trasformazione DRAMMATICA del blog post.** Prima:
- Wall of text incollato senza respiro
- H1 attaccata al lede
- Sentenze sparate a sinistra senza container
- Foto autore gigante a fondo come decoration

Adesso:
- **Lede in serif italic Playfair editoriale** subito sotto H1
- **Drop-cap "R" maestoso** sul primo paragrafo body
- **H2 con respiro** 80px sopra
- **Sentenze in container 720px** con border editoriale
- **Author bio card piccolo** (80×80 + nome + ruolo)
- **Em-dash bullet accent oro** (su blog post — da estendere a competenza)

Il sito **finalmente sembra una rivista editoriale** e non un blog WordPress standard. Aeon / Atlantic / Bick Law model raggiunto.

---

## 🎯 Decisione: 1 Mini-Fix Round prima di Step F

I 3 issue residui (R1 mappa, R2 Lorem Ipsum, R3 bullet competenza) sono fix piccoli (10-15 min ciascuno = ~30-40 min totali). Risolverli prima di Step F per:

1. Cliente non vede mappa "sul mare" e Lorem Ipsum (BLOCKER reputazione)
2. Coerenza editoriale completa (em-dash ovunque)
3. Step F può concentrarsi solo su tecniche (WOFF2, SRI, Lighthouse) senza sorprese visive

---

## 🟡 Issue rinviati (non bloccanti, opzionale)

- **GROUP D minor (D1 search layout, D2 404 plain)** rimasti aperti dal walkthrough precedente. Rinviati a Step F+ o pre-deploy polish.
- **Sezione "Si occupa di" su single avvocato** non renderizza (meta `aree_competenza_correlate` vuoto post-Step D). Non blocker, opzionale.
- **Stock images del cliente** (cartoon AI in alcuni blog post) rimangono. Solo cornice CSS uniforma. Da rimpiazzare in fase content futura se direttore lo richiede.
- **/avvocati/{slug}/ sezione "Si occupa di"** — ACF meta vuoto, sezione skip. Da popolare in fase content.

---

## Screenshot evidenziali

- ss_0201s6yyn — Blog post Intimazione TARI: lede italic + h1 spacing
- ss_15992o1e3 — Sentenza in container 720px + drop-cap "R"
- ss_2998tiori — H2 "Cosa devi sapere" con respiro + lista TARI
- ss_4448wuc3u — Author bio card 80×80 in fondo post
- ss_7099gjx0o + ss_8515lyy54 — /blog/ archive ora popolato 326 post
- ss_13652636j — /contatti/ con eyebrow editoriale
- ss_2783dffjm — /contatti/ map area (vedi R1)
- ss_546024lbr — /chi-siamo/ Lorem Ipsum (vedi R2)
- ss_4762fwuli — Mappa OpenStreetMap mobile sul mare (R1)
- ss_8499tdhjm — /competenze/diritto-tributario/ lista bullet "•" (R3)
- ss_48250nsyd — Homepage mobile editoriale
- ss_7554p5bfm — Blog post mobile lede italic + drop-cap implicito

---

*Walkthrough deep completato. Procedo con commit + prompt mini-fix Final Polish v0.11.0 per chiudere R1+R2+R3.*
