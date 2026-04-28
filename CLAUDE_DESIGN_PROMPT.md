# Claude Design Prompt — Studio Legale Saltelli (v2.0)

> **v2.0 — Aggiornato 2026-04-28** dopo la formalizzazione del Metodo Adsolut UX/UI Reference + report comparativo su 10 siti (1 cliente + 4 competitor locali + 5 internazionali) + 20 screenshot reali catturati con Playwright. \*\***Cosa è cambiato vs v1**: questo prompt non parte più da brief astratto, parte da **dati comparativi reali** con conteggi di prevalenza N/8 (vedi `ux-research/SALTELLI_UX_REFERENCE_REPORT.md`). Le decisioni di design sono **difensive** — supportate da "X competitor su 8 fanno Y" — non opinioni. La Bad list è esplicita.

---

## Come usare questo prompt

### Su `claude.ai/design`

1. Apri `claude.ai/design` in una nuova sessione.
2. **Allega come reference visiva** (drag & drop) i seguenti screenshot dalla cartella `ux-research/screenshots/` del progetto, in quest'ordine:
   - `01-difiorenunziato-desktop.png` — competitor diretto tributario stesso indirizzo
   - `05-bicklaw-desktop.png` — reference internazionale: editorial illustration boutique
   - `06-stowe-desktop.png` — reference internazionale: serif display + warmth + ritmo
   - `07-seddons-mobile.png` — reference internazionale: asimmetria + bordeaux + drop cap
   - `09-pedersoligattai-desktop.png` — reference internazionale: boutique premium italiano (DNA culturale)
   - `10-deepjudge-desktop.png` — reference legaltech: UI components moderni + minimalismo
3. **Copia il blocco prompt qui sotto** tra `--- BEGIN PROMPT ---` e `--- END PROMPT ---`.
4. Incolla come primo messaggio in `claude.ai/design`.
5. Avanza una sessione alla volta: **prima sessione = Design System + Frame 1 (Homepage)**, poi review interna (tu + Elena), poi cliente review #1, poi sessione successiva.

### Workflow successivo (riassunto)

Sessione 1 (oggi/domani) → Design System + Frame 1 Homepage Sessione 2 → Frame 2 (Avvocato) + Frame 3 (Competenza tier-1 + tier-2) Sessione 3 → Frame 4 (archivio competenze) + Frame 5 (articolo blog) Tra una sessione e l'altra: review interna Adsolut + (eventuale) cliente review.

---

## --- BEGIN PROMPT ---

Sto progettando il sito web di **Studio Legale Saltelli & Partners**, uno studio boutique a Napoli (quartiere Chiaia, Via Vannella Gaetani 27). Voglio ottenere un design system completo e i prototipi delle 5 pagine chiave, con un solo obiettivo: **far apparire il sito attuale del cliente — e quelli dei suoi competitor napoletani diretti — vecchi di 10 anni**.

Il prompt è denso. Leggilo tutto prima di iniziare.

## 1 — Identità del cliente

**Studio Legale Emiliano Saltelli & Partners**

- 4 avvocati: Emiliano Saltelli (fondatore, tributarista 20+ anni, Università Federico II) · Fabiana Saltelli (giuslavorista) · Antonia Battista (famiglia, divorzi, **tutela LGBTQ+** — nicchia poco presidiata a Napoli) · Stefano Gaetano Tedesco (condominiale, immobiliare).
- 19 aree di pratica totali, ma con **strategia tier-1 confermata** sui 3 cluster verticali profondi: **Diritto tributario / cartelle esattoriali**, **Diritto del lavoro**, **Diritto di famiglia LGBTQ+**.
- Quartiere Chiaia: zona alta di Napoli, eleganza borghese, palazzi nobiliari. È un dato culturale, non solo un indirizzo.

**Posizionamento desiderato**: né "studio scuola classica romana" austero, né "boutique milanese minimal". Uno **studio napoletano contemporaneo** che porta nel digitale la stessa cura sartoriale dei migliori palazzi nobiliari di Chiaia: pulito, raffinato, riconoscibile, mai ostentato. **AI-ready** (sarà il primo studio legale in Italia con `llms.txt` + schema GEO completo).

## 2 — Cosa abbiamo scoperto analizzando 8 siti

Ho mappato 1 sito cliente + 4 competitor locali napoletani + 3 internazionali best-in-class con una lente analitica sistematica (12 dimensioni × 8 siti = 96 celle). Ecco i dati che muovono il design.

### Pattern dominanti (≥6/8 — standard di settore)

PatternScoreDecisioneClick-to-call header sempre visibile**8/8**OBBLIGATORIOBlog editoriale8/8OBBLIGATORIOCPT/sezioni avvocato dedicate7/8OBBLIGATORIOFAQ strutturate per pratica7/8OBBLIGATORIOCasi vinti / risultati visibili7/8OBBLIGATORIOContainer max-width 1440px7/8OBBLIGATORIOGerarchia tipografica chiara7/8OBBLIGATORIOEarned media / "Parlano di noi"6/8OBBLIGATORIOCTA primaria singola sotto headline6/8OBBLIGATORIO

### Pattern di differenziazione (3/8 — opportunità di leva)

PatternScoreDecisione**Hero a piena altezza viewport (100vh**)3/8 (solo internazionali)**DA APPLICARE** — discontinuità visibile**Headline serif display large**3/8 (solo internazionali)**DA APPLICARE** — leva massima**Pairing serif display + sans body**3/8 (solo internazionali)**DA APPLICARE** — leva massima**Spazio bianco generoso**3/8**DA APPLICARE** — opportunità WOW**Asimmetria deliberata layout**3/8**DA APPLICARE** — distintivo**Smooth scroll Lenis-like**3/8 (best-in-class)**DA APPLICARE** — qualità tecnica**Scroll-triggered animations**3/8**DA APPLICARE** — moderato, mai esibito**Type scale responsive (clamp**)3/8**DA APPLICARE** — qualità tecnica**Mobile-first vs desktop-first**3/8 mobile-first**MOBILE-FIRST**

### Pattern obsoleti / anti-pattern (≤1/8 — vietare)

�� moderato, mai esibito | | **Type scale responsive (clamp)** | 3/8 | **DA APPLICARE** — qualità tecnica | | **Mobile-first vs desktop-first** | 3/8 mobile-first | **MOBILE-FIRST** |

### Pattern obsoleti / anti-pattern (≤1/8 — vietare)

Anti-patternScoreDecisione**Slideshow rotante in hero**1/8 (solo Saltelli oggi)**VIETATO ASSOLUTAMENTEWhatsApp floating button vistoso**1/8 (solo Saltelli)RIDIMENSIONARE — solo mobile, mini**Schema JSON-LD assente**4/8 (Saltelli + 3 locali)**OBBLIGATORIO RISOLVERE** (gap GEO)`#000` **puro come testo**4/8 (tutti i locali italiani)**VIETATO** — usare `#2D2D2D`**Bianco puro** `#FFF` **come background**5/8**PREFERIRE crema** `#FAFAF8`

### Territorio inesplorato (0/8)

PatternScoreDecisione`llms.txt` **esposto0/8FIRST-MOVER ASSOLUTO** nel settore legale italiano**Schema markup completo**3/8OPPORTUNITÀ — implementeremo coverage 100%

## 3 — Direzione design "Legal Luxury Minimal"

Sintesi: **boutique editoriale italiana**. Reinterpretiamo la pulizia anglosassone (Stowe, Seddons) in chiave editoriale italiana (eco di Domus, Apartamento, Cabana, dei migliori cataloghi d'arte). Tipografia dominante. Spazio bianco aggressivo. Layout asimmetrici. Movimento sottile, mai esibito.

### Mood references (le ho già allegate come immagini)

- **Bick Law LLP** (USA) — editorial illustration approach, animation mono→colore, atmosfera "studio diverso". Ruba l'**energia di differenziazione**.
- **Stowe Family Law** (UK) — serif display + warmth + ritmo respiratorio. Ruba la **gentilezza tipografica**.
- **Seddons** (UK) — asimmetria + bordeaux + drop cap. Ruba la **sofisticazione editoriale**.
- **PedersoliGattai** (IT, boutique premium 350 professionisti) — palette navy/avorio italiana, tipografia institutional ma viva. Ruba il **DNA culturale italiano**.
- **DeepJudge** (legaltech, Awwwards HM 2025) — UI components puliti, minimalismo deciso. Ruba i **dettagli UI moderni**.
- **Di Fiore Nunziato** (competitor diretto stesso indirizzo Saltelli) — è quello da cui **NON copiare nulla**. Loro hanno navy + oro corporate; noi facciamo crema + bronzo per non concorrere sull'asse "tradizione storica".

## 4 — Design tokens (locked)

```
COLOR
  --background:       #FAFAF8   (crema bianco-avorio — rompe con il bianco puro 5/8)
  --surface:          #F2F0EA   (crema scuro per superfici secondarie)
  --primary:          #1B2B4B   (navy profondo, mai #000)
  --accent:           #B8860B   (oro/bronzo, parsimonia massima)
  --text:             #2D2D2D   (grigio molto scuro, MAI #000 puro)
  --text-muted:       #6B6B6B
  --border:           #E5E0D5
  Vietati: #000 puro, rossi aggressivi, viola/magenta

TYPOGRAPHY
  --font-display:     "Playfair Display" 400/700  (titoli, headline)
                      alternativa: "Cormorant Garamond" 300/500/700
  --font-body:        "DM Sans" 400/500/700  (body, UI)
                      alternativa: "Satoshi" 400/500/700
  --font-mono:        "JetBrains Mono"  (solo metadati: data, tag, label tecniche)
  Vietati: Inter, Roboto, Arial, Open Sans

TYPE SCALE responsive con clamp()
  --fs-display:       clamp(48px, 8vw, 120px)
  --fs-h1:            clamp(36px, 5vw, 64px)
  --fs-h2:            clamp(28px, 3.5vw, 44px)
  --fs-h3:            clamp(20px, 2vw, 28px)
  --fs-body:          clamp(16px, 1.1vw, 18px)
  --fs-small:         14px
  --line-height-display: 1.05
  --line-height-body:    1.65
```

```
SPACING SCALE (8-base): 4 / 8 / 16 / 24 / 32 / 48 / 64 / 96 / 128 / 192

GRID
  Container max-width: 1440px · Padding: clamp(24px, 5vw, 96px)
  Grid columns: 12 desktop / 6 tablet / 4 mobile
  Gap: 32px desktop / 24px tablet / 16px mobile

BREAKPOINTS: 375 / 768 / 1024 / 1440  (mobile-first)
```

## 5 — 5 frame da prototipare

Procedere in ordine. Sessione 1 = solo Design System + Frame 1.

### Frame 1 — Homepage

**Sopra la piega (hero 100vh):**

- A sinistra: **headline serif display gigante** (clamp 60-120px) che si rivela parola per parola in scroll-in (SplitText).
  - Copy seed: *"Diritto, con misura."* (alternativa: *"Avvocati a Napoli, dal 1999."*).
- Sotto la headline, **sotto-headline 18-20 parole** in serif regular o sans medium.
  - Copy seed: *"Studio Legale Saltelli & Partners. Quattro avvocati a Chiaia, diciannove aree di pratica, vent'anni di lavoro accanto a famiglie e imprese di Napoli."*
- A destra: **spazio negativo**. NIENTE foto. Eventualmente un piccolo modulo orari/contatti tipografato come colophon di rivista.
- **Una sola CTA primaria** stile editoriale: testo + linea sotto, no button filled colorato.
- Indicatore di scroll discreto in basso.
- **VIETATO**: slideshow rotante (1/8 — anti-pattern).
**Sezioni successive (in ordine):**

1. **"Aree di pratica"** — NON una grid di cards. **Una lista tipografica gigante**: 19 righe verticali, kerning generoso. Hover: trasla 8px a destra + linea bronzo 1px sotto + preview di descrizione a destra. Filtro pillola sopra (tutte / civile / penale / tributario / lavoro / famiglia).
2. **"Lo studio"** — blocco di prose editoriale, max 400 parole, layout a colonna stretta come articolo di rivista. Eventualmente foto B/N facciata Via Vannella Gaetani 27 a tutta larghezza dopo il testo. Niente foto stock.
3. **"Avvocati"** — 4 avvocati, **layout asimmetrico** (mai grid 2x2). Quattro blocchi sfalsati con ritratto desaturato (3:4), nome serif large, ruolo in mono small, 2-3 specializzazioni come tag. Hover: ritratto recupera colore (eco Bick Law).
4. **"Casi e risultati"** — 3-4 casi anonimizzati come "Vittorie recenti" stile Wikipedia entry. Identificatore (es. "vs. NOV", "Cassazione 2024"), descrizione 2 righe, esito.
5. **"Earned media"** (sezione critica: 6/8 standard, Saltelli a 0) — strip logo in mono o badge "Citati da [stampa locale]" + premi. Sobrio, una riga, non vetrina.
6. **"Contatti / CTA"** — non un form lungo. Blocco editoriale: indirizzo, telefono, email tipografati grandi. Sotto, pulsante singolo "Prenota un primo incontro" stile minimal.
7. **Footer** — denso ma elegante. Tre colonne: Studio (link pagine), Aree (19 multi-colonna), Contatti (NAP + PEC + ordine + P.IVA). Sotto, copyright + privacy + cookie. Niente icone social colorate.

### Frame 2 — Pagina avvocato (Avv. Emiliano Saltelli)

- Hero: ritratto 1:1 a sinistra (B/N desaturato, recupera colore al load), a destra nome e ruolo in serif gigante. Sotto: bio editoriale 200-300 parole, max-width stretta.
- Sezione "Si occupa di" — lista delle 6 aree di competenza, tipografia grande, link.
- Sezione "Formazione" — Federico II 1999, Ordine di Napoli, ecc., timeline tipografica verticale minimal.
- CTA: "Prenota un incontro con l'Avv. Saltelli" in fondo.
- Sticky nel margine: bottoni piccoli "Telefono", "Email", "WhatsApp" tipografati in mono.

