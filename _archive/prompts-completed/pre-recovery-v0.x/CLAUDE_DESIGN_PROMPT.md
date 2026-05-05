# Claude Design Prompt — Studio Legale Saltelli (v2.1)

> **v2.1 — 2026-04-28** · Riscrittura pulita di v2.0. Stessi contenuti (10 siti, 5 internazionali tra cui Pedersoli Gattai + DeepJudge, tutti i 5 frame), formattazione fixata, prompt completo end-to-end.
>
> **Cosa è cambiato vs v1**: il prompt non parte più da brief astratto, parte da **dati comparativi reali** con conteggi di prevalenza N/8 (vedi `ux-research/SALTELLI_UX_REFERENCE_REPORT.md`). Le decisioni di design sono **difensive** — supportate da "X competitor su 8 fanno Y" — non opinioni soggettive. La Bad list è esplicita.

---

## Come usare questo prompt

### Su `claude.ai/design`

1. Apri `claude.ai/design` in una nuova sessione.
2. **Allega come reference visiva** (drag & drop) i seguenti screenshot dalla cartella `ux-research/screenshots/`, in quest'ordine:
   - `01-difiorenunziato-desktop.png` — competitor diretto tributario stesso indirizzo (riferimento "anti-mood")
   - `05-bicklaw-desktop.png` — international: editorial illustration boutique
   - `06-stowe-desktop.png` — international: serif display + warmth + ritmo
   - `07-seddons-mobile.png` — international: asimmetria + bordeaux + drop cap
   - `09-pedersoligattai-desktop.png` — international: boutique premium italiano (DNA culturale)
   - `10-deepjudge-desktop.png` — legaltech: UI components moderni + minimalismo
3. **Copia il blocco prompt qui sotto** (tra `--- BEGIN PROMPT ---` e `--- END PROMPT ---`).
4. Incolla come primo messaggio in `claude.ai/design`.
5. Avanza una sessione alla volta:
   - **Sessione 1**: Design System + Frame 1 (Homepage)
   - tra una sessione e l'altra: review interna Adsolut → eventuale cliente review
   - **Sessione 2**: Frame 2 (Avvocato) + Frame 3 (Competenza tier-1 + tier-2)
   - **Sessione 3**: Frame 4 (Archivio competenze) + Frame 5 (Articolo blog)

### Note operative per Duccio (NON incollare in Claude Design)

Vedi sezione finale dopo `--- END PROMPT ---`.

---

## --- BEGIN PROMPT ---

Sto progettando il sito web di **Studio Legale Emiliano Saltelli & Partners**, uno studio boutique a Napoli (quartiere Chiaia, Via Vannella Gaetani 27). Voglio un design system completo e i prototipi delle 5 pagine chiave del sito, con un solo obiettivo: **far apparire il sito attuale del cliente — e quelli dei suoi competitor napoletani diretti — vecchi di 10 anni**.

Il prompt è denso. Leggilo tutto prima di iniziare.

---

## 1. Identità del cliente

**Studio Legale Emiliano Saltelli & Partners**

- 4 avvocati: **Emiliano Saltelli** (fondatore, tributarista 20+ anni, Università Federico II) · **Fabiana Saltelli** (giuslavorista) · **Antonia Battista** (famiglia, divorzi, **tutela LGBTQ+** — nicchia poco presidiata a Napoli) · **Stefano Gaetano Tedesco** (condominiale, immobiliare).
- 19 aree di pratica totali, ma con **strategia tier-1 confermata** sui 3 cluster verticali profondi: **Diritto tributario / cartelle esattoriali**, **Diritto del lavoro**, **Diritto di famiglia LGBTQ+**.
- Quartiere Chiaia: zona alta di Napoli, eleganza borghese, palazzi nobiliari. È un dato culturale, non solo un indirizzo.

**Posizionamento desiderato**: né "studio scuola classica romana" austero, né "boutique milanese minimal". Uno **studio napoletano contemporaneo** che porta nel digitale la stessa cura sartoriale dei migliori palazzi nobiliari di Chiaia: pulito, raffinato, riconoscibile, mai ostentato. **AI-ready** (sarà il primo studio legale in Italia con `llms.txt` + schema GEO completo).

---

## 2. Cosa abbiamo scoperto analizzando 8 siti

Ho mappato 1 sito cliente + 4 competitor locali napoletani + 3 internazionali best-in-class con una lente analitica sistematica (12 dimensioni × 8 siti = 96 celle). Ecco i dati che muovono il design.

### 2.1 Pattern dominanti (≥6/8 — standard di settore: vanno rispettati)

| Pattern | Score | Decisione |
|---|---|---|
| Click-to-call header sempre visibile | 8/8 | OBBLIGATORIO |
| Blog editoriale | 8/8 | OBBLIGATORIO |
| CPT/sezioni avvocato dedicate | 7/8 | OBBLIGATORIO |
| FAQ strutturate per pratica | 7/8 | OBBLIGATORIO |
| Casi vinti / risultati visibili | 7/8 | OBBLIGATORIO |
| Container max-width 1440px | 7/8 | OBBLIGATORIO |
| Gerarchia tipografica chiara (1 H1, niente duplicati) | 7/8 | OBBLIGATORIO |
| Earned media / "Parlano di noi" | 6/8 | OBBLIGATORIO |
| CTA primaria singola sotto headline | 6/8 | OBBLIGATORIO |

### 2.2 Pattern di differenziazione (3/8 — opportunità di leva)

Sono i pattern che usano gli internazionali best-in-class ma nessun locale italiano. Applicarli = differenziazione visibile dal primo secondo.

| Pattern | Score | Decisione |
|---|---|---|
| Hero a piena altezza viewport (100vh) | 3/8 (solo internazionali) | DA APPLICARE — discontinuità visibile |
| Headline serif display large (60-120px) | 3/8 (solo internazionali) | DA APPLICARE — leva massima |
| Pairing serif display + sans body | 3/8 (solo internazionali) | DA APPLICARE — leva massima |
| Spazio bianco generoso | 3/8 | DA APPLICARE — opportunità WOW |
| Asimmetria deliberata layout | 3/8 | DA APPLICARE — distintivo |
| Smooth scroll Lenis-like | 3/8 (best-in-class) | DA APPLICARE — qualità tecnica |
| Scroll-triggered animations | 3/8 | DA APPLICARE — moderato, mai esibito |
| Type scale responsive (clamp) | 3/8 | DA APPLICARE — qualità tecnica |
| Mobile-first vs desktop-first | 3/8 mobile-first | MOBILE-FIRST |

### 2.3 Anti-pattern (≤1/8 — vietati)

| Anti-pattern | Score | Decisione |
|---|---|---|
| Slideshow rotante in hero | 1/8 (solo Saltelli oggi) | VIETATO ASSOLUTAMENTE |
| WhatsApp floating button vistoso | 1/8 (solo Saltelli) | RIDIMENSIONARE — solo mobile, mini, stile editoriale |
| Schema JSON-LD assente | 4/8 (Saltelli + 3 locali) | OBBLIGATORIO RISOLVERE (gap GEO) |
| `#000` puro come testo | 4/8 (tutti i locali italiani) | VIETATO — usare `#2D2D2D` |
| Bianco puro `#FFF` come background | 5/8 | PREFERIRE crema `#FAFAF8` |

### 2.4 Territorio inesplorato (0/8)

| Pattern | Score | Decisione |
|---|---|---|
| `llms.txt` esposto | 0/8 | FIRST-MOVER ASSOLUTO nel settore legale italiano |
| Schema markup completo | 3/8 | OPPORTUNITÀ — implementeremo coverage 100% |

---

## 3. Direzione design "Legal Luxury Minimal"

Sintesi: **boutique editoriale italiana**. Reinterpretiamo la pulizia anglosassone (Stowe, Seddons) in chiave editoriale italiana (eco di Domus, Apartamento, Cabana, dei migliori cataloghi d'arte). Tipografia dominante. Spazio bianco aggressivo. Layout asimmetrici. Movimento sottile, mai esibito.

### Mood references (allegate come immagini)

| Reference | Cosa rubargli |
|---|---|
| **Bick Law LLP** (USA) | Editorial illustration approach, animation mono→colore, atmosfera "studio diverso". Energia di **differenziazione**. |
| **Stowe Family Law** (UK) | Serif display + warmth + ritmo respiratorio. **Gentilezza tipografica**. |
| **Seddons** (UK) | Asimmetria + bordeaux + drop cap. **Sofisticazione editoriale**. |
| **PedersoliGattai** (IT, boutique premium 350 professionisti) | Palette navy/avorio italiana, tipografia institutional ma viva. **DNA culturale italiano**. |
| **DeepJudge** (legaltech, Awwwards HM 2025) | UI components puliti, minimalismo deciso. **Dettagli UI moderni**. |
| **Di Fiore Nunziato** (competitor diretto, stesso indirizzo Saltelli) | NON copiare nulla. Loro hanno navy + oro corporate, noi facciamo crema + bronzo per non concorrere sull'asse "tradizione storica". È il riferimento "anti-mood". |

---

## 4. Design tokens (locked)

```
COLOR
  --background:       #FAFAF8   (crema bianco-avorio — rompe con il bianco puro 5/8)
  --surface:          #F2F0EA   (crema scuro per superfici secondarie)
  --primary:          #1B2B4B   (navy profondo, MAI #000)
  --accent:           #B8860B   (oro/bronzo, parsimonia massima)
  --text:             #2D2D2D   (grigio molto scuro, MAI #000 puro)
  --text-muted:       #6B6B6B
  --border:           #E5E0D5

VIETATI: #000 puro, rossi aggressivi, viola/magenta (sono colori dell'agenzia, non del cliente)
```

```
TYPOGRAPHY
  --font-display:     "Playfair Display" 400/700  (titoli, headline)
                      alternativa: "Cormorant Garamond" 300/500/700
  --font-body:        "DM Sans" 400/500/700  (body, UI)
                      alternativa: "Satoshi" 400/500/700
  --font-mono:        "JetBrains Mono"  (solo metadati: data, tag, label tecniche)

VIETATI: Inter, Roboto, Arial, Open Sans (font generici)
```

```
TYPE SCALE (responsive con clamp)
  --fs-display:           clamp(48px, 8vw, 120px)
  --fs-h1:                clamp(36px, 5vw, 64px)
  --fs-h2:                clamp(28px, 3.5vw, 44px)
  --fs-h3:                clamp(20px, 2vw, 28px)
  --fs-body:              clamp(16px, 1.1vw, 18px)
  --fs-small:             14px
  --line-height-display:  1.05
  --line-height-body:     1.65
```

```
SPACING SCALE (8-base)
  4 / 8 / 16 / 24 / 32 / 48 / 64 / 96 / 128 / 192

GRID
  Container max-width: 1440px
  Padding container:   clamp(24px, 5vw, 96px)
  Grid columns:        12 desktop / 6 tablet / 4 mobile
  Gap:                 32px desktop / 24px tablet / 16px mobile

BREAKPOINTS: 375 / 768 / 1024 / 1440  (mobile-first)
```

---

## 5. I 5 frame da prototipare

Procedere in ordine. **Sessione 1 = solo Design System + Frame 1.** Le altre sessioni vengono dopo, separatamente.

### Frame 1 — Homepage

**Sopra la piega (hero 100vh):**

- A sinistra: **headline serif display gigante** (clamp 60-120px) che si rivela parola per parola in scroll-in (SplitText).
  - Copy seed: *"Diritto, con misura."* (alternativa: *"Avvocati a Napoli, dal 1999."*)
- Sotto la headline, **sotto-headline 18-20 parole** in serif regular o sans medium.
  - Copy seed: *"Studio Legale Saltelli & Partners. Quattro avvocati a Chiaia, diciannove aree di pratica, vent'anni di lavoro accanto a famiglie e imprese di Napoli."*
- A destra: **spazio negativo**. NIENTE foto stock. Eventualmente un piccolo modulo orari/contatti tipografato come colophon di rivista.
- **Una sola CTA primaria** stile editoriale: testo + linea sotto, no button filled colorato.
- Indicatore di scroll discreto in basso.
- VIETATO: slideshow rotante (è ciò che ha Saltelli oggi).

**Sezioni successive (in ordine):**

1. **"Aree di pratica"** — NON una grid di cards. Una **lista tipografica gigante**: 19 righe verticali, kerning generoso. Hover: traslazione 8px a destra + linea bronzo 1px sotto + preview di descrizione che appare a destra. Filtro pillola sopra (Tutte / Civile / Penale / Tributario / Lavoro / Famiglia).

2. **"Lo studio"** — blocco di prose editoriale, max 400 parole, layout a colonna stretta come articolo di rivista. Eventualmente foto B/N facciata Via Vannella Gaetani 27 a tutta larghezza dopo il testo. Niente foto stock.

3. **"Avvocati"** — i 4 avvocati, **layout asimmetrico** (mai grid 2x2). Quattro blocchi sfalsati con ritratto desaturato (3:4), nome serif large, ruolo in mono small, 2-3 specializzazioni come tag tipografati. Hover: il ritratto recupera il colore (eco Bick Law).

4. **"Casi e risultati"** — 3-4 casi anonimizzati come "Vittorie recenti" stile Wikipedia entry minimal. Identificatore (es. "vs. NOV", "Cassazione 2024"), descrizione 2 righe, esito.

5. **"Earned media"** — sezione critica (6/8 standard, Saltelli oggi a 0). Strip di logo testuali in mono o badge "Citati da [stampa locale]" + premi. Sobrio, una riga, non vetrina. Quando vuoto, nascondere.

6. **"Contatti / CTA"** — non un form lungo. Blocco editoriale: indirizzo, telefono, email tipografati grandi. Sotto, pulsante singolo "Prenota un primo incontro" stile minimal.

7. **Footer** — denso ma elegante. Tre colonne: Studio (link pagine), Aree (19 multi-colonna), Contatti (NAP + PEC + ordine + P.IVA). Sotto, copyright + privacy + cookie. Niente icone social colorate; eventualmente i nomi scritti per esteso ("Instagram • LinkedIn") tipografati piccoli.

---

### Frame 2 — Pagina avvocato (esempio: Avv. Emiliano Saltelli)

- **Hero**: ritratto 1:1 a sinistra (B/N desaturato, recupera colore al load), a destra il nome e ruolo in serif gigante. Sotto: bio editoriale 200-300 parole, max-width stretta.
- Sezione **"Si occupa di"** — lista delle 6 aree di competenza, tipografia grande, link.
- Sezione **"Formazione"** — Federico II 1999, Ordine di Napoli, ecc., timeline tipografica verticale minimal.
- Sezione **"Articoli e pubblicazioni"** — 3-5 ultimi post blog dell'avvocato.
- **CTA**: "Prenota un incontro con l'Avv. Saltelli" in fondo.
- **Sticky nel margine sinistro**: bottoni piccoli "Telefono", "Email", "WhatsApp" tipografati in mono, mai sovrapposti al contenuto.

---

### Frame 3 — Pagina competenza (con doppia variante: tier-1 vs tier-2)

**Variante tier-1** — esempio: `/competenze/diritto-tributario/`
Le 3 nicchie verticali profonde (tributario, lavoro, famiglia LGBTQ+) ricevono il trattamento depth.

- **Hero**: titolo serif gigante "Diritto tributario", sottotitolo "Cartelle esattoriali, contenzioso fiscale, accertamenti."
- **Answer capsule** in evidenza subito sotto l'H1: paragrafo di 50 parole serif italic, max-width stretta, che risponde alla query target ("Cosa fa lo Studio Saltelli in materia di diritto tributario?"). Critico per il GEO.
- **Body**: 1500-2500 parole strutturati in H2/H3 logici. Ogni H2 può avere un answer capsule subito sotto.
- Sezione **"Avvocati di riferimento per quest'area"** — 1-2 avvocati che la presidiano (Emiliano e Fabiana), ritratto piccolo + nome + link al profilo.
- Sezione **"Casi rappresentativi"** — 3-4 casi anonimizzati (solo tier-1).
- Sezione **"Domande frequenti"** — 5 FAQ in accordion minimal (no shadow, no rounded corner aggressive). Domande in serif, risposte in sans. Schema FAQPage.
- Sezione **"Articoli sul tema"** — 3 post del blog correlati.
- **CTA finale**: "Hai un caso simile? Parlane con i nostri avvocati."

**Variante tier-2** — esempio: `/competenze/domiciliazione-impresa/`
Le altre 16 aree ricevono trattamento standard.

- Stesso hero + answer capsule.
- **Body** ridotto: 400-600 parole, niente sezione "casi rappresentativi", niente body extended.
- FAQ ridotte a 3 domande.
- Stessa CTA finale.

**Mostra entrambe le varianti nel Frame 3** per evidenziare la differenza visiva tra tier-1 (densità contenuto + casi) e tier-2 (essenziale).

---

### Frame 4 — Archivio competenze (`/competenze/`)

- **Hero piccolo**: solo titolo "19 aree di pratica" + introduzione 30 parole.
- **NON una grid di cards.** Una **grande lista tipografica** delle 19 aree, ognuna su una riga. Hover: la riga si espande sottilmente, appare l'avvocato di riferimento e il numero di articoli correlati.
- **Filtro opzionale in alto**: pillola minimal "Tutte / Civile / Penale / Tributario / Lavoro / Famiglia". Tipografica, non boxed.
- Le 3 aree **tier-1** sono visivamente evidenziate (es. accent bronzo sull'iniziale, o leggera baseline bronzo) per segnalare la specializzazione di punta.

---

### Frame 5 — Singolo articolo blog

- **Hero**: categoria in mono uppercase small + data + autore + reading time. Sotto, titolo serif gigante. Sotto, lead di 2 righe in serif regular.
- **Body**: max-width stretta tipo articolo di Pushkin Industries / NYT longform. **Drop cap** sulla prima lettera. Pull quote ben tipografate.
- **Sidebar destra (solo desktop)**: TOC sticky tipografica minimal con scroll-spy.
- **Fine articolo**: profilo autore (foto piccola + bio 50 parole + link al profilo avvocato).
- **"Articoli correlati"**: 3 post stesso topic.
- **CTA**: "Hai un caso simile?"

---

## 6. Comportamenti e animazioni

- **Hero text reveal**: SplitText word-by-word stagger 60ms, ease `cubic-bezier(0.25, 0.46, 0.45, 0.94)`.
- **Sezioni in scroll**: fade-in 400ms + translateY(24px → 0), trigger quando l'elemento entra all'80% del viewport.
- **List items area pratica / avvocati**: stagger 80ms tra le righe.
- **Hover su lista aree**: translateX(8px) + appare linea bronzo 1px sotto, transition 200ms ease-out.
- **Header**: trasparente at top, diventa solido (background `--background`) con 1px border-bottom dopo 80px di scroll. Smooth transition 300ms.
- **Cursor**: standard, no custom cursor effects vistosi.
- **Smooth scroll**: Lenis sull'intero sito (lerp 0.1).
- **Mobile**: animazioni semplificate (solo fade, no SplitText, no translate aggressivi) per preservare LCP.

---

## 7. Output che mi serve

Sessione corrente (Sessione 1):

1. **Design system completo** — tokens (colori, typography scale, spacing) + 4 componenti base:
   - Button minimal (testo + linea sotto, NO filled)
   - Link tipografico (con hover bronzo)
   - Accordion FAQ (no shadow, no rounded aggressive)
   - List item area-pratica (con hover translateX + linea bronzo)

2. **Frame 1 (Homepage)** completa:
   - Desktop 1440px
   - Mobile 375px

3. **Handoff bundle esportabile** (Figma o equivalente) per passaggio a fase successiva (Claude Code).

---

## 8. Tone-check finale

Quando guardi il prototipo finito chiediti: **"Sembra un sito che potrebbe esistere nel 2030, costruito con la cura di un palazzo storico ristrutturato a Chiaia?"**

- Se la risposta è **sì**, abbiamo finito.
- Se sembra un altro sito legale generico fatto con un theme builder, **ricominciamo**.
- Se sembra un sito "AI app SaaS 2024" con cards arrotondate e shadow soft, **ricominciamo**.
- Se sembra un sito anglosassone copiato senza adattamento italiano, **ricominciamo**.

**Procedi.** Iniziamo dal Design System, poi Frame 1

## --- END PROMPT ---

---

## Note operative per Duccio (NON incollare in Claude Design)

### Cosa fare DOPO che la Sessione 1 ha prodotto Design System + Frame 1

1. **Esporta handoff bundle** (Figma file o equivalente) → salvalo in `.claude/knowledge/design/sessione-1/`.
2. **Internal review #1 (Adsolut)**: tu + Elena + Ludovica. Sessione di 30-45 min. Domande di review:
   - Sembra un sito che potrebbe esistere nel 2030?
   - Sembra italiano (Chiaia/napoletano contemporaneo) o template internazionale?
   - Riusciresti a difenderlo davanti al cliente che dice "manca la bilancia"?
   - La gerarchia tipografica regge a 375px?
3. **Cliente review #1** — manda al cliente: link tokens / screenshot Frame 1 desktop+mobile + memo testo 200 parole con la rationale. Raccogli feedback in **un solo round consolidato** (regola di processo: niente ping-pong, max 2 iterazioni).

### Risposte preparate alle obiezioni cliente (da tenere a mano)

- **"Lo trovo troppo minimale"** → ricorda che è il *posizionamento* che differenzia. Più decoriamo, più assomigliamo ai competitor.
- **"Vorrei più colore"** → l'oro/bronzo è già il colore. Aggiungerne altri rompe l'eleganza.
- **"Il blu è poco visibile"** → il navy è scelto deliberatamente sobrio. Se serve emphasis usiamo l'oro.
- **"Manca la bilancia/martelletto"** → discussione di posizionamento, non di estetica. Mostra le references (Cravath, Wachtell, PedersoliGattai).
- **"Vorrei una foto in homepage"** → mostra Stowe / Bick Law: lo spazio negativo è una scelta editoriale che vale, non un mancato.

### Foto da procurare (Ludovica)

Per il rendering finale (Sessione 2 in poi) servono:

- 4 ritratti professionali avvocati (B/W o desaturabili, sfondo neutro, formati 3:4 e 1:1)
- 1 foto facciata sede Via Vannella Gaetani 27
- 1-2 dettagli interni studio (libreria, scrivania, luce naturale)
- 2-3 dettagli architettonici di Chiaia per atmosfera (palazzi, dettagli di pietra, scorci)

Finché non arrivano, Claude Design userà placeholder. È accettabile per Sessione 1 — diventa critico da Sessione 2 (Frame 2 = pagina avvocato).

### Sessioni successive — cosa cambia rispetto a Sessione 1

**Sessione 2** (Frame 2 + Frame 3):
- Riusa lo stesso prompt da `--- BEGIN PROMPT ---` ma sostituisci la sezione 7 ("Output che mi serve") con:
  - Frame 2 (Pagina avvocato) desktop + mobile
  - Frame 3 (Pagina competenza) — entrambe varianti tier-1 e tier-2, desktop + mobile

**Sessione 3** (Frame 4 + Frame 5):
- Stesso pattern: cambia solo la sezione 7 con Frame 4 + Frame 5 desktop + mobile.

### Checklist pre-lancio Sessione 1

- [ ] Aperto `claude.ai/design` in nuova sessione (non riusare sessione vecchia v1)
- [ ] Allegati 6 screenshot in ordine (Di Fiore Nunziato, Bick Law, Stowe, Seddons, Pedersoli, DeepJudge)
- [ ] Copiato il blocco prompt PULITO da `--- BEGIN PROMPT ---` a `--- END PROMPT ---`
- [ ] Incollato come primo messaggio
- [ ] Prima domanda di rilancio dopo l'output: "Mostrami il Design System a 1440px, poi passa al Frame 1."

---

*Ultimo aggiornamento: 2026-04-28 v2.1 — Riscrittura pulita post-formattazione rotta v2.0.*
