# Metodo Adsolut — Ricerca Reference UX/UI

> Metodo formalizzato della web agency Adsolut per la fase di **ricerca reference** che precede la progettazione UX/UI di ogni nuovo cliente. Affinato su 20+ anni di esperienza. Codificato qui per la prima volta in forma scritta, con tre upgrade specifici resi possibili dall'AI assistance.

---

## Sintesi del metodo in una frase

> *Si parte dal cliente (dentro), si esce nei competitor diretti (vicino) e nei best-in-class del settore a livello globale (lontano), si esce dal settore per estetica e tecnica (cross-domain), si consolida tutto in un report comparativo a colonne con conteggio di prevalenza, e si distilla un brief sintetico per lo UX designer interno.*

---

## I 5 step

### Step 1 — Inside (cliente attuale)

**Cosa raccogli:**
- Vecchio brand (logo, palette, identità visiva)
- Foto di archivio aziendali (se esistono)
- Tutti i contenuti del sito attuale → questi diventano la base contenutistica del nuovo (mai buttare, sempre delta)
- Sitemap esistente — se SEO-aware, è il ponte verso la nuova IA
- Storico aziendale: anno di fondazione, milestone, evoluzione del brand

**Cosa produci da questo step:**
- Inventario "c'è / manca": cosa c'è nel sito attuale, cosa manca rispetto a un sito moderno
- Inventario asset riusabili (foto, contenuti testuali da rinfrescare, vittorie/casi documentati)
- Sketch di sitemap nuova (se la vecchia non è SEO-aware)

---

### Step 2 — Local (competitor diretti di zona)

**Cosa cerchi:** i 3-5 migliori competitor diretti del cliente, in zona geografica.

**Lente analitica (la stessa per tutti, applicata sistematicamente):**
- Piattaforma tecnologica (CMS, framework, builder)
- Plugin / feature funzionali (chat, lead capture, landing, calendar booking)
- Tipografia (font, pairing, scale)
- Palette colori (background, primary, accent)
- Spazio bianco e leggibilità
- Organizzazione contenuti (gerarchia, lunghezza, formato)
- **Hero / sezione principale** ← priorità assoluta, è dove si gioca conversione
- Animazioni e script
- UX dei bottoni / micro-interazioni
- Performance (Core Web Vitals, Lighthouse a colpo d'occhio)
- Mobile adaptation

**Doppia polarità — nuovo nel metodo formalizzato:**
- I migliori (da emulare in parte)
- I peggiori → **worst case del settore** (oggi raccolti come bad list mentale, da formalizzare → vedi Upgrade 1)

---

### Step 3 — International (best-in-class internazionali stesso settore)

**Cosa cerchi:** i 3 migliori siti del settore a livello globale, anche in altre lingue.

**Lente analitica prevalente:**
- **Cura del design** (priorità su tutto)
- Feature innovative non presenti nel mercato locale
- Tipografia, micro-interazioni, asset originali

**Source consigliate:**
- Awwwards filter "[settore] + Sites of the Day"
- Land-book.com tag settore
- Behance "rebrand" + settore
- Liste editoriali "Best [settore] websites 2026" su Google

---

### Step 4 — Cross-domain (estetica e tecnica fuori settore)

**Cosa cerchi:** ispirazione visiva e tecnica indipendentemente dal settore.

**Source per tipo di output:**
| Source | Cosa estrai |
|---|---|
| **Awwwards / SiteInspire** | Mood, art direction, layout completo |
| **Dribbble** | Frame singolo, pattern visivo, micro-interazione |
| **Codepen** | Effetto puntuale tecnico (text reveal, hover, transition) |
| **Google "best 3D websites"** | Wow factor, ambizioni tecniche |

**Regola d'oro:** dal cross-domain non si copia mai un layout intero. Si rubano *singoli elementi* (un'animazione, una scelta tipografica, un ritmo di scroll).

---

### Step 5 — Comparative report (formula segreta interna)

Documento **interno**, mai cliente-facing. È la sintesi che alimenta lo UX designer.

**Struttura:**

|  | Sito attuale cliente | Local 1 | Local 2 | Local 3 | International 1 | International 2 | International 3 |
|---|---|---|---|---|---|---|---|
| Piattaforma / stack | | | | | | | |
| Feature funzionali | | | | | | | |
| Hero / sezione principale | | | | | | | |
| Tipografia (font + scale) | | | | | | | |
| Palette colori | | | | | | | |
| Spazio bianco / leggibilità | | | | | | | |
| Organizzazione contenuti | | | | | | | |
| Animazioni / script | | | | | | | |
| UX bottoni / micro-interazioni | | | | | | | |
| Performance | | | | | | | |
| Mobile UX | | | | | | | |
| Lead capture / CTA | | | | | | | |

**Riga di sintesi finale: "Design vincente per [cliente]"** — bullet list testuale che il designer riceve come brief.

---

## I 3 upgrade del metodo (introdotti con AI assistance)

### Upgrade 1 — Worst case scritti, non solo mentali

**Problema risolto:** oggi i worst case del settore vivono come "lo so quando lo vedo" del designer/team. Non viaggiano. Quando il designer cambia o entra qualcuno nuovo (Elena, Ludovica, freelance), il sapere si perde.

**Come si fa adesso:**
Nel report comparativo, sotto la riga di sintesi positiva, si aggiunge una sezione **"Bad list — cose da non fare"**:

```markdown
## Bad list — pattern da evitare

- ❌ Hero con foto stock di stretta di mano in giacca grigia (tutti i competitor lo fanno)
- ❌ Icone bilancia/martelletto/colonne classiche → cliché del settore legale
- ❌ Slideshow rotante in homepage → appartiene al 2014
- ❌ Cards con bordi arrotondati e shadow blur → estetica "AI app SaaS 2024"
- ❌ Footer con icone social colorate → rompe la sobrietà del brand
- ...
```

**Beneficio:** il designer ha sia il "sì" che il "no" formalizzati. Più veloce, più replicabile, più difensivo davanti al cliente quando obietta.

---

### Upgrade 2 — Conteggio prevalenza X/N (AI-assisted)

**Problema risolto:** in passato Adsolut compilava il report con conteggi tipo "8/10 competitor usano hero a piena altezza". Pratica abbandonata per il **costo manuale di compilazione** (richiede di guardare 10 siti, prendere note rigorose, contare). Senza il conteggio, le decisioni di design si prendono "a sensazione" — meno difensive, meno taglienti.

**Come si fa adesso:**
L'AI compila il conteggio in 5 minuti scarsi. In ogni cella del report, oltre al dato qualitativo, si annota il conteggio di prevalenza:

```markdown
| Hero a piena altezza | ✓ (5/7 — pattern dominante) |
| Carousel di servizi | ✗ (1/7 — pattern raro, evitare se non motivato) |
| Chat live in basso a destra | ✓ (4/7 — diviso, non determinante) |
```

**Tre soglie interpretative:**
- **≥7/10 → standard di settore** → emularlo o avere ragione precisa per evitarlo
- **3-6/10 → diviso** → scelta libera, dipende dal posizionamento
- **≤2/10 → pattern raro** → emularlo solo se è una feature distintiva del best-in-class

**Beneficio:** decisioni di design supportate da dato. Davanti al cliente che chiede "perché non hai messo X?" la risposta è "9 competitor su 10 lo fanno e per questo NON lo facciamo, vogliamo differenziare. Oppure: 9 competitor su 10 lo fanno e quindi lo facciamo anche noi — è uno standard utente che il visitatore si aspetta."

---

### Upgrade 3 — Negative brief esplicito dal cliente

**Problema risolto:** raccolto implicitamente, mai messo nero su bianco. Il cliente dice "non voglio sembrare X" e quella frase contiene info preziose: un competitor con cui ha conflitto, un posizionamento opposto, un'estetica traumatica, un'esperienza pregressa fallita.

**Come si fa adesso:**
In kickoff cliente, una domanda esplicita: **"Quali sono 1-3 siti / studi / brand a cui NON vuoi assolutamente assomigliare? Perché?"**

La risposta entra nel report come box dedicato:

```markdown
## Negative brief — il cliente NON vuole sembrare:

1. **[Competitor X]** — perché lo trova "vecchio, generico, troppo pomposo"
2. **[Brand Y]** — perché ha avuto cattiva esperienza, associazione negativa
3. **[Pattern estetico Z]** — perché percepito come "scontato"
```

**Beneficio:** la lista alimenta direttamente la **Bad list** (Upgrade 1). Inoltre dà al designer un orizzonte di legittimazione: se nel review interno si propende verso un pattern simile a [Competitor X], scatta il segnale rosso prima di arrivare al cliente con un concept che andrà rigettato.

---

### Upgrade 4 — Anti-pattern automated detection (CLI)

**Problema risolto:** anche con la Bad list scritta (Upgrade 1) e il negative brief (Upgrade 3), un designer (umano o AI) può inavvertitamente reintrodurre un anti-pattern nelle iterazioni successive del codice. Esempio: lo Style Agent applica una palette corretta, ma in un component minore aggiunge `font-family: Inter` o usa `cubic-bezier(0.68, -0.55, 0.265, 1.55)` (bounce easing). Senza un check automatico, scopri il problema solo davanti al cliente.

**Cosa si introduce:**

Un **detector regex-based** che scansiona il frontend buildato e segnala 24+ pattern AI-slop e issue di design quality, senza intervento AI.

**Anti-pattern catturati (lista non esaustiva):**

| Categoria | Esempi |
|---|---|
| AI slop visivo | Side-tab borders, purple gradients, bounce easing, dark glows neon, cards nestate in cards |
| Tipografia generica | Font generici imposti (Inter, Roboto, Arial, system-default), uso massivo di font weight 400 senza varianti |
| Colore | Pure `#000` o `#FFF` senza tinta, gray text su colored backgrounds, palette troppo desaturate |
| Spazio | Cramped padding (< 8px), line-length > 80ch, touch target < 44px |
| Gerarchia | Skipped headings (h1 → h3 senza h2), multipli h1 |
| Motion | Bounce / elastic easing, durate animazioni > 600ms, mancanza di `prefers-reduced-motion` fallback |
| Accessibilità | Contrast ratio < 4.5, focus state mancanti, alt text generico |

**Implementazione:**

Il metodo Adsolut adotta `pbakaus/impeccable` come standard di detection (19k★, Apache 2.0, mantenuto). Il detector è uno **script CLI standalone** — non richiede AI, gira in locale via `npx`, output JSON o human-readable.

```bash
# Detector run su un progetto (esempio Saltelli)
npx impeccable detect wp-content/themes/saltelli/ --json > audit.json

# Detector su una URL live
npx impeccable detect https://staging.studiolegalesaltelli.it --fast --json

# Detector veloce (solo regex, no Puppeteer)
npx impeccable detect --fast .
```

**Quando va eseguito nel processo Adsolut:**

1. **Pre-commit hook (opzionale)** — gira il detector ad ogni commit del frontend. Se >0 anti-pattern critici, blocca il commit. Adatto a progetti maturi, eccessivo per prototipi.

2. **Post-build sanity check (consigliato)** — gira il detector dopo ogni build/deploy. Output finisce in `audit.json` committato come evidenza, e il diff con la run precedente mostra regressioni.

3. **Pre-cliente review (obbligatorio)** — gira il detector prima di mandare un design al cliente. Zero anti-pattern critici è un Definition of Done implicito.

4. **Pre-deploy produzione (obbligatorio)** — ultimo gate prima del go-live. Diff vs baseline iniziale come evidenza di qualità raggiunta.

**Rapporto con i 3 upgrade precedenti:**

- L'**Upgrade 1 (Bad list scritta)** è la lista *qualitativa*, scritta dal team Adsolut, contiene scelte di posizionamento (es. "no bilance/martelletto"). **L'Upgrade 4 NON la sostituisce** — è il *complemento automatico* per gli anti-pattern *generici trasversali* (purple gradient, Inter, ecc.).
- L'**Upgrade 2 (Conteggio prevalenza)** alimenta decisioni *strategiche*. L'Upgrade 4 cattura *errori di esecuzione*.
- L'**Upgrade 3 (Negative brief)** è specifico cliente. L'Upgrade 4 è universale.

I 4 upgrade insieme coprono **strategia + esecuzione + verifica**, da capo a coda.

**Beneficio:** il metodo Adsolut diventa **misurabile**. Davanti a un cliente che chiede "come garantite la qualità?" si risponde con un detector run + diff. Nessun "fidatevi del nostro gusto".

**Nota di posizionamento metodologico:** Impeccable include anche 18 comandi AI-driven per refining attivo (`/audit`, `/critique`, `/polish`, ecc.) che NON fanno parte di questo Upgrade 4. Quei comandi vivono in una fase separata del workflow Adsolut — vedi `SHIP_PLAN_24H.md` → "Fase 1.E.10 Impeccable polishing pass". Adsolut adotta solo il **detector standalone** come parte del metodo standard di reference research; il polishing AI-driven resta opzionale e si applica progetto-per-progetto.

---

## Workflow completo del metodo (pre + durante + post ricerca)

```
[Kickoff cliente]
    ├─ Brief funzionale (chi è, cosa offre, target, obiettivi)
    └─ Negative brief esplicito ← Upgrade 3

[Step 1 — Inside]
    └─ Audit sito attuale + storico + sitemap

[Step 2 — Local competitor]
    └─ 3-5 competitor diretti, lente sistematica, doppia polarità best/worst

[Step 3 — International]
    └─ 3 best-in-class globali, focus cura design + feature innovative

[Step 4 — Cross-domain]
    └─ Awwwards / Dribbble / Codepen / 3D showcases per estetica + tecnica

[Step 5 — Comparative report]
    ├─ Tabella colonnare (1 cliente + 3-5 local + 3 international)
    ├─ Conteggio prevalenza X/N ← Upgrade 2
    ├─ Riga sintesi "design vincente"
    └─ Bad list ← Upgrade 1 + Upgrade 3

[Brief allo UX designer Adsolut]
    └─ Riceve: bullet list "design vincente" + bad list

[Esecuzione design — Photoshop + Figma]
    └─ v1 design

[Post-build / Post-iterazione frontend code]
    └─ Detector run (npx impeccable detect) ← Upgrade 4
       └─ Se >0 anti-pattern critici → fix prima di proseguire

[Internal QA — Aldo (max 2 revisioni interne)]
    └─ Sign-off

[Pre-cliente review]
    └─ Detector run finale ← Upgrade 4 obbligatorio
       └─ Output committato come evidenza qualità

[Cliente review #1]

[Pre-deploy produzione]
    └─ Detector run + diff vs baseline ← Upgrade 4 obbligatorio
       └─ Diff committato come evidenza miglioramenti
```

---

## Definition of Done — il report è pronto quando:

1. ✅ Ogni cliente ha il **suo** report (nessun riuso, nessun template generico)
2. ✅ Tutte le righe della lente analitica sono compilate (no celle vuote)
3. ✅ Conteggio di prevalenza presente per ogni feature ricorrente
4. ✅ Riga sintesi "design vincente" è una **bullet list azionabile** (verbi + sostantivi specifici, non aggettivi vaghi tipo "pulito" o "moderno")
5. ✅ Bad list scritta esplicitamente
6. ✅ Negative brief del cliente integrato (se raccolto in kickoff)
7. ✅ Documento approvato da Aldo prima di passare al designer

---

## Quando NON si applica questo metodo

- **Iterazione su sito esistente** (refresh, non redesign) → si fa solo Step 1
- **Pagina singola landing tattica** → metodo abbreviato (Step 1 + Step 2 + report ridotto)
- **Cliente che ha già un design system attivo** → si parte da quello, il metodo serve a estenderlo non a riscriverlo

---

## Storia delle revisioni

| Data | Versione | Modifiche |
|---|---|---|
| 2026-04-28 | 1.0 | Prima formalizzazione scritta. Codificati i 5 step + 3 upgrade (worst case scritti, conteggio prevalenza AI-assisted, negative brief esplicito) durante il progetto Saltelli. |

---

*Questo documento è proprietà di Adsolut SRLS. È la formalizzazione di un metodo affinato su oltre 20 anni di lavoro. Da utilizzare internamente come standard operativo.*
