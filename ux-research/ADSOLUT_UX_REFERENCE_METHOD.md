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

[Internal QA — Aldo (max 2 revisioni interne)]
    └─ Sign-off

[Cliente review #1]
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
