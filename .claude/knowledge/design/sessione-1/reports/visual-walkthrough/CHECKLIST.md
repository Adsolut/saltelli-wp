# Visual Walkthrough — Checklist 12-Point

> **Test plan riusabile** per validare visivamente che la build implementata corrisponda alle premesse del prompt design Claude Sessione 1 + audit GEO/CRO + brief originale.
> Eseguito da: **Claude (orchestrator)** via Claude in Chrome, dopo ogni release significativa.
> Output: report markdown con PASS/WARN/FAIL per ciascuno dei 12 punti.

---

## Quando eseguirlo

Eseguire dopo ogni "milestone agent" che ha modificato CSS o template visibili al cliente:

| Versione | Quando | Owner |
|---|---|---|
| v0.7.x — pain points fixed | Dopo Pain Points Refinement Agent | Orchestrator |
| v0.8.x — template polish | Dopo Template Polish Agent (Step E) | Orchestrator |
| v1.0.0-rc1 — production ready | Dopo Production Readiness Agent (Step F) | Orchestrator |
| Post-deploy DigitalOcean | Sul droplet con DNS attivo | Orchestrator + Duccio |

---

## Setup pre-test

```
1. Browser: Chrome (preferito) via Claude in Chrome MCP
2. Viewport desktop: 1440 × 900 (resize_window)
3. Cache: hard reload con query string ?_=v07test (per bypassare cache CDN/browser)
4. DevTools: chiusi durante screenshot, aperti per ispezione DOM se serve
5. Mobile test: viewport 375 × 844 (resize_window) — viene fatto solo nei punti 12+
```

---

## Hard rules per il test

| Rule | Reason |
|---|---|
| Mai accettare PASS basandosi solo su HTTP 200 | Il sito può rispondere ma essere visivamente rotto |
| Ogni screenshot deve essere allegato come evidence al report | Audit trail riproducibile |
| WARN ammissibile per discrepanze minori (es. spacing 4px diverso) | Non blocca demo |
| FAIL per discrepanze visibili al cliente in 5 secondi di occhio | Bloccante |
| Comparison contro JSX Sessione 1 sempre la fonte di verità di intent | Locked design |

---

## I 12 punti del walkthrough

### 1 — Hero homepage 100vh, 3 righe, zero overlap

**Premessa Claude Design:** *Hero a piena altezza viewport (3/8 best-in-class). Headline display large clamp 60-120px che si rivela parola per parola in scroll-in (SplitText). Sotto la headline, sotto-headline 18-20 parole. A destra: spazio negativo / colophon orari/contatti. Una sola CTA primaria stile editoriale. Indicatore di scroll discreto in basso.*

**Test:**
- Naviga a `http://localhost:8080/?_=test1`
- Wait 3 secondi (animazioni complete)
- Screenshot 1 (above the fold)
- Scroll 500px down
- Screenshot 2 (verifica nessun overlap)

**Criteri di PASS:**
- ✅ Hero occupa l'intera viewport altezza (≥ 800px su 1440×900)
- ✅ Headline "Diritto, con misura." su 3 righe, font Playfair, color navy
- ✅ Sotto-headline serif italic visibile sotto la h1
- ✅ CTA "Prenota una consulenza gratuita" stile editoriale (testo + linea, no button filled)
- ✅ Subline italic serif "Prima consulenza conoscitiva — risposta entro 24 ore" (post fix P1.4)
- ✅ Colophon a destra con INDIRIZZO/ORARI/CONTATTI tipografato (mono uppercase eyebrow + body)
- ✅ Indicator "SCORRI ↓" in basso a destra
- ❌ FAIL se: headline tagliata, colophon sovrapposto al main content, subline in mono caps aggressivo

**Verifica DOM (opzionale):**
```js
({
  heroHeight: document.querySelector('.sl-hero')?.offsetHeight,
  viewportHeight: window.innerHeight,
  ratio: document.querySelector('.sl-hero')?.offsetHeight / window.innerHeight,
  hasMain: !!document.querySelector('.sl-hero__main'),
  hasColophon: !!document.querySelector('.sl-hero__colophon'),
  ctaText: document.querySelector('.sl-hero__main .sl-btn')?.textContent.trim()
})
```

`ratio` atteso ≥ 0.95 (≈ 100vh).

---

### 2 — Lista 19 aree tipografica (NON cards) con tier-1 evidenziato

**Premessa Claude Design:** *NON una grid di cards. Una lista tipografica gigante, diciannove righe, ognuna con kerning generoso. Hover su una riga: traslazione 8px + linea bronzo 1px sotto. Le 3 aree tier-1 visivamente evidenziate (drop-cap accent bronzo) per segnalare la specializzazione di punta.*

**Test:**
- Naviga `/`
- Scroll a sezione "Diciannove aree" (§ 01)
- Screenshot intera lista (potrebbe richiedere 2-3 screenshot per coprire 19 voci)
- Verifica che le 3 tier-1 siano visivamente distinte

**Criteri di PASS:**
- ✅ Lista tipografica verticale (NO grid 3-col, NO card con border)
- ✅ Tier-1 (Diritto Tributario · Diritto del Lavoro · Diritto di Famiglia LGBTQ+) hanno drop-cap iniziale color bronzo (`var(--accent)`)
- ✅ Tier-2 (16 aree) hanno prima lettera color primary normale (NO accent)
- ✅ Numerazione "01/19" sequenziale rimossa o ridotta a opacity 0.4 (post fix P1.1)
- ✅ Separator line tra ogni area
- ❌ FAIL se: tutte 19 aree hanno drop-cap bronzo, lista in card grid

**Verifica DOM:**
```js
({
  totalAreas: document.querySelectorAll('.sl-area').length,
  tier1Count: document.querySelectorAll('.sl-area--tier1').length,
  tier1FirstLetterColor: getComputedStyle(document.querySelector('.sl-area--tier1 .sl-area__title'), '::first-letter').color,
  tier2FirstLetterColor: getComputedStyle(document.querySelector('.sl-area:not(.sl-area--tier1) .sl-area__title'), '::first-letter').color
})
```

Atteso: `totalAreas: 19, tier1Count: 3, tier1FirstLetterColor: rgb(184, 134, 11)` (bronze).

---

### 3 — Layout asimmetrico (NON grid 12-col centrata)

**Premessa Claude Design:** *Asimmetria deliberata, niente paura del vuoto. Ritmo respiratorio. Allineamenti a sinistra forti, blocchi che rompono la griglia.*

**Test:**
- Scroll lentamente dalla home dall'inizio alla fine
- Screenshot 3-5 sezioni significative
- Verifica che ciascuna sezione abbia gerarchia spaziale, non centrata banalmente

**Criteri di PASS:**
- ✅ Eyebrow "§ NN — NOME SEZIONE" sempre allineato a sinistra in colonna stretta
- ✅ Headline sezione (h2) allineato a sinistra in colonna larga
- ✅ Body content (prose, lista, ecc.) flow naturale
- ✅ Sezioni respirano: padding-block ≥ 64px tra sezioni
- ❌ FAIL se: tutto centrato, nessuna gerarchia visiva, padding tight

---

### 4 — Drop-cap "L" su § 02 Lo studio

**Premessa Claude Design:** *Drop-cap sulla prima lettera, prose editoriale, tono editoriale italiano. Layout a colonna stretta come un articolo di rivista.*

**Test:**
- Scroll alla sezione "Lo studio" (§ 02)
- Screenshot

**Criteri di PASS:**
- ✅ Lettera "L" iniziale in drop-cap (font display Playfair, dimensione 3-4× il body, float-left)
- ✅ Prose 3-4 paragrafi
- ✅ Riferimenti reali ("Federico II", "Vannella Gaetani 27", "bottega di quattro professionisti")
- ✅ Foto facciata placeholder "Plate I · Facciata" + "Via Vannella Gaetani, 27" italic + "Fotografia in B/N · 1440 × 480 · placeholder"
- ❌ FAIL se: nessun drop-cap, prose generica/lorem, foto stock random

---

### 5 — § 03 Avvocati: 4 lawyer asimmetrici, foto Emiliano reale

**Premessa Claude Design:** *4 lawyer asimmetrici (NON grid 2x2 simmetrica), placeholder con gradient + filter grayscale che rimuove al hover. Eco Bick Law animation mono→colore.*

**Test:**
- Scroll alla sezione "Quattro professionisti." (§ 03)
- Screenshot

**Criteri di PASS:**
- ✅ Layout asimmetrico (offset numerici diversi tra i 4 lawyer)
- ✅ **Emiliano Saltelli ha foto reale** (DSLR ritratto, abito blu navy)
- ✅ Gli altri 3 hanno placeholder gradient editoriale "RITRATTO · 3:4"
- ✅ Nome avvocato in serif Playfair grande sotto il ritratto
- ✅ Ruolo in mono small ("FOUNDING PARTNER · TRIBUTARISTA")
- ❌ FAIL se: grid 2x2 simmetrica, foto stock dei 3 placeholder, nome dell'avvocato in mono

---

### 6 — § 04 Casi rappresentativi tipografici

**Premessa Claude Design:** *3-4 casi anonimizzati, presentati come "Cause vinte" in uno stile à la Wikipedia entry minimal. Numero (es. "vs. NOV", "Cassazione 2024"), descrizione 2 righe, esito.*

**Test:**
- Scroll a "Casi rappresentativi" (§ 04)
- Screenshot

**Criteri di PASS:**
- ✅ Layout 3-colonne: identifier mono (sx) | descrizione italic (centro) | esito bronze (dx)
- ✅ Almeno 4 casi visibili (es. "VS. AGE RISCOSSIONE · 2024", "CASSAZIONE · 2024")
- ✅ Esito ("Annullamento", "Vittoria", "Riconoscimento") in color bronze
- ✅ Descrizioni anonimizzate (no nomi cognomi reali)
- ❌ FAIL se: layout cards, nessun esito bronze, casi numerici (es. "Caso 1, 2, 3")

---

### 7 — Footer dark navy 3 colonne

**Premessa Claude Design:** *Denso ma elegante. Tre colonne: Studio (link pagine), Aree (19 multi-colonna), Contatti (NAP + PEC + ordine + P.IVA). Sotto: copyright + privacy + cookie + Instagram + LinkedIn testuali.*

**Test:**
- Scroll fino in fondo alla home
- Screenshot

**Criteri di PASS:**
- ✅ Background `var(--primary)` navy
- ✅ Text crema su navy
- ✅ 3 colonne: "Saltelli & Partners" (col 1) · "Diciannove Aree" (col 2) · "Contatti" (col 3)
- ✅ NAP completo (Via Vannella Gaetani 27 · 80121 Napoli — Chiaia · +39 081 1813 1119 · info@studiolegalesaltelli.it · PEC · Ordine · P.IVA)
- ✅ Copyright row in fondo + privacy/cookie/note legali + Instagram/LinkedIn testuali (NON icons colorate)
- ❌ FAIL se: footer chiaro, icons social colorate, no NAP completo

---

### 8 — Pagina /costi/ layout editoriale (NO colonna vuota)

**Premessa post-fix P0.1:** *Layout asimmetrico. Eyebrow mono "§ NN — TITLE" a sinistra in colonna stretta + content a destra in colonna larga. Coerente con tutti gli altri template.*

**Test:**
- Naviga `/costi/?_=test8`
- Screenshot above-fold
- Scroll a § 01, § 02, § 03
- Screenshot ciascuna sezione

**Criteri di PASS:**
- ✅ Breadcrumb "HOME / COSTI E PRIMA CONSULENZA" in mono uppercase
- ✅ H1 "Costi e prima consulenza" Playfair gigante navy
- ✅ Answer capsule editoriale italic Playfair, max-width contenuta, centrato/asimmetrico
- ✅ § 01 / § 02 / § 03 con eyebrow mono a sinistra colonna stretta + content a destra colonna larga (su desktop ≥ 1024px)
- ✅ Mobile (<1024px) stack in colonna unica
- ✅ FAQ accordion `+/-` editoriale a destra (post fix P1.3)
- ✅ CTA finale "Prenota ora →" su surface crema
- ❌ FAIL se: enorme colonna vuota a sinistra (bug pre-fix P0.1), eyebrow allineato a destra, accordion default browser triangle

**Verifica DOM:**
```js
({
  hasPageWrapper: !!document.querySelector('.sl-page'),
  hasContent: !!document.querySelector('.sl-page__content'),
  faqCount: document.querySelectorAll('.sl-acc, details.sl-acc').length,
  sectionsCount: document.querySelectorAll('.sl-costi__section').length,
  asymmetric: getComputedStyle(document.querySelector('.sl-costi__section')).gridTemplateColumns
})
```

Atteso: `faqCount: 5, sectionsCount: ≥3`, `asymmetric: "240px ..."` su desktop.

---

### 9 — Single-competenza tier-1: FAQ accordion + answer capsule + CTA

**Premessa Claude Design:** *Hero gigante + answer capsule sotto H1. Body 1500-2500 parole. Avvocati di riferimento. Casi rappresentativi (solo tier-1). FAQ accordion minimal (no shadow). Articoli correlati. CTA finale.*

**Test:**
- Naviga `/competenze/diritto-tributario/?_=test9`
- Screenshot ciascuna sezione

**Criteri di PASS:**
- ✅ Eyebrow "TIER 1 · APPROFONDIMENTO · PER LE IMPRESE" in mono uppercase
- ✅ H1 "Diritto tributario" Playfair gigante
- ✅ Answer capsule serif italic 50 parole sotto h1 (con keyword: Commissioni Tributarie, IMU, IRPEF, INPS)
- ✅ Body content corposo (1500+ parole) ereditato dal sito originale
- ✅ Sezione "Domande frequenti" con accordion `+/-` editoriale
- ✅ h2 nel content NON in MAIUSCOLO Playfair gigante (post fix P0.2)
- ✅ CTA finale "Hai una pratica simile?" + button "Parlane con i nostri avvocati →"
- ✅ Subline italic "Prima consulenza conoscitiva gratuita · Risposta entro 24 ore · In studio o online" (post fix P1.4, NON mono caps)
- ❌ FAIL se: h2 ALL CAPS visibili, FAQ con triangolo browser default, subline mono uppercase aggressivo

**Test interattivo:**
- Click su prima FAQ "Quanto costa un ricorso contro una cartella esattoriale?"
- Verifica che il pannello si apra
- Verifica che il `+` ruoti/cambi in `−`
- Click di nuovo → si chiude

---

### 10 — Single-avvocato Emiliano: foto + bio + formazione timeline

**Premessa Claude Design:** *Hero ritratto 1:1 a sinistra (B/W desaturato, recupera colore al load), a destra il nome e ruolo in serif gigante. Bio editoriale 200-300 parole. Sezione "Si occupa di" — lista delle 6 aree di competenza. Sezione "Formazione" — timeline tipografica verticale minimal. Sticky margine sinistro: bottoni piccoli "Telefono", "Email", "WhatsApp" tipografati in mono.*

**Test:**
- Naviga `/avvocati/emiliano-saltelli/?_=test10`
- Screenshot above-fold + scroll multiple

**Criteri di PASS:**
- ✅ Foto reale Emiliano (DSLR, ritratto navy abito su crema)
- ✅ Eyebrow "FOUNDING PARTNER · TRIBUTARISTA" mono
- ✅ Nome "Emiliano Saltelli" Playfair gigante navy
- ✅ Bio breve sotto nome
- ✅ 5 specializzazioni come tag mono uppercase ("DIRITTO TRIBUTARIO · CARTELLE ESATTORIALI · CONTENZIOSO FISCALE · DIRITTO BANCARIO · RECUPERO CREDITI")
- ✅ Bio estesa con storia personale ("La pratica autonoma comincia nel 2008", "in via Vannella Gaetani 27, nel cuore di Chiaia", "annullamenti di cartelle superiori a €240.000")
- ✅ Caratteri italiani corretti (è, à, ', non `?` o `Ã¨`)
- ✅ Sezione "§ FORMAZIONE" con timeline ("1. Laurea Giurisprudenza UNIVERSITÀ DEGLI STUDI DI NAPOLI FEDERICO II", "2. 2008 Avvocato — Iscrizione Ordine")
- ✅ Sticky bottoni TEL/EMAIL/WhatsApp visibili nel margine sinistro stilizzati come tag editoriali
- ❌ FAIL se: foto sgranata o assente, bio con caratteri mojibake, sticky bottoni nudi

---

### 11 — Archive /tipo-area/privati/ funzionante

**Premessa post-fix Step Audit Alignment:** *Archive funzionale che lista le competenze taggate "privati" con titolo della categoria, intro, lista tipografica delle 9 competenze.*

**Test:**
- Naviga `/tipo-area/privati/?_=test11`
- Screenshot

**Criteri di PASS:**
- ✅ Eyebrow "ARCHIVIO" mono
- ✅ H1 "Per i Privati" Playfair gigante
- ✅ Subline "Diritto di famiglia, eredità, lavoro, risarcimento danni, immigrazione, penale" (descrizione termine)
- ✅ Lista delle 9 competenze taggate "privati" (Responsabilità civile, Risarcimento danni, Diritto delle successioni, Diritto penale, Diritto dell'immigrazione, Responsabilità medica, Diritto del lavoro, Diritto di famiglia, Diritto di famiglia LGBTQ+)
- ✅ Stile coerente con archive-competenza.php (lista tipografica, NO cards)
- ❌ FAIL se: 404, layout default WP, lista vuota

**Test ripetuti per:**
- `/tipo-area/imprese/` → atteso: 4 competenze (recupero-crediti, bancario, tributario, assicurazioni)
- `/tipo-area/contenzioso/` → atteso: 4 (cartelle, condominiale, amministrativo, previdenziale)
- `/tipo-area/altri/` → atteso: 2 (domiciliazione, consulenze-online)

---

### 12 — Mobile 375px responsive (DevTools mobile view)

**Premessa Claude Design:** *Mobile-first vero. Type scale clamp. Hamburger pulito. Click-to-call sempre visibile. Animazioni semplificate (solo fade, no SplitText, no translate aggressivi) per preservare LCP.*

**Test:**
- `resize_window(375, 812)` (iPhone X-ish)
- Naviga `/?_=test12mobile`
- Screenshot above-fold
- Test apertura hamburger menu
- Screenshot menu aperto
- Scroll a sezione aree pratica → screenshot
- Test apertura/chiusura accordion FAQ su `/competenze/diritto-tributario/`

**Criteri di PASS:**
- ✅ Header con wordmark "Saltelli & Partners" + hamburger (icona pulita, no fronzoli)
- ✅ Hero headline sta in 1 colonna senza overflow orizzontale
- ✅ CTA "Prenota una consulenza gratuita" full-width clickable
- ✅ Subline italic visibile sotto CTA
- ✅ Hamburger menu si apre con animation, mostra 6 voci + sub-menu "Aree di Pratica"
- ✅ Lista 19 aree resta tipografica verticale (NO cards)
- ✅ Tier-1 drop-cap bronze visibile anche su mobile
- ✅ FAQ accordion `+/-` funzionante
- ✅ NO horizontal scroll
- ✅ NO testo tagliato fuori viewport
- ❌ FAIL se: overflow-x, hamburger non si apre, layout colonne side-by-side desktop replicato

**Verifica DOM mobile:**
```js
({
  bodyScrollWidth: document.body.scrollWidth,
  windowWidth: window.innerWidth,
  hasOverflow: document.body.scrollWidth > window.innerWidth,
  heroHeadlineWidth: document.querySelector('.sl-hero__headline')?.offsetWidth,
  hamburgerVisible: getComputedStyle(document.querySelector('.sl-header__burger, [class*="hamburger"]')).display !== 'none'
})
```

`hasOverflow: false` è critico.

---

## Output: report finale

Al termine del walkthrough, produrre `.claude/knowledge/design/sessione-1/reports/visual-walkthrough/REPORT-vX.Y.Z.md` con:

```markdown
# Visual Walkthrough — vX.Y.Z

**Data:** YYYY-MM-DD HH:MM
**Tester:** Claude (orchestrator)
**Tool:** Claude in Chrome (browser_batch + javascript_tool)
**Build sotto test:** v0.7.0-beta-pain-points-fixed (esempio)

## Risultati aggregati

| Punto | Status | Note |
|---|:---:|---|
| 1 — Hero 100vh | ✅ PASS / 🟡 WARN / ❌ FAIL | … |
| 2 — Lista 19 aree | … | … |
| 3 — Layout asimmetrico | … | … |
| 4 — Drop-cap Lo studio | … | … |
| 5 — Avvocati asimmetrici | … | … |
| 6 — Casi tipografici | … | … |
| 7 — Footer dark | … | … |
| 8 — /costi/ editoriale | … | … |
| 9 — Single-competenza | … | … |
| 10 — Single-avvocato | … | … |
| 11 — Archive tipo-area | … | … |
| 12 — Mobile 375px | … | … |

## Score globale

**X / 12 PASS** · **Y WARN** · **Z FAIL**

→ **Decisione:** GO / NO-GO per il prossimo step

## Issue residui (lista per priorità)

…

## Screenshot evidenziali

Allegati in `.claude/knowledge/design/sessione-1/reports/visual-walkthrough/screenshots-vX.Y.Z/`
```

---

## Versioning del walkthrough

Ogni walkthrough produce un file `REPORT-vX.Y.Z.md` versionato. Lo storico permette di tracciare:
- Trend di qualità nel tempo (4 PASS → 8 PASS → 12 PASS)
- Regressioni (un punto era PASS in v0.6, è FAIL in v0.7?)
- Issue persistenti che richiedono attenzione strategica

---

*v1.0 — 2026-04-30 · Metodo riusabile per tutte le release Saltelli e progetti futuri Adsolut.*
