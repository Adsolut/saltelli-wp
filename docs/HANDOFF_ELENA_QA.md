# Handoff QA continuativo — Elena Cappabianca

> **Destinataria:** Elena Cappabianca (Studio Legale Saltelli & Partners)
> **In copia:** Ludovica Casa, Avv. Emiliano Saltelli
> **Da:** Adsolut SRLS · tech@adsolut.it
> **Data:** 2026-05-13
> **Versione tema:** v1.3.24-wave-6-0-partial-stabilized (CUT-READY)
> **Staging:** https://staging.studiolegalesaltelli.it
> **Manuale CMS completo:** `docs/EDITOR-HANDOFF.md` v6.0 (resta il riferimento quotidiano per "come edito X")

Questo documento è il tuo **handoff operativo continuativo per la fase di QA pre-cut + post-cut**. Sostituisce `HANDOFF_ELENA_PRE_CUT.md` (snapshot del 2026-05-12, ora superseded). Lo scopo: darti gli strumenti per fare QA visivo del sito, segnalare bug, lavorare quotidianamente sul CMS senza dipendere dal team tecnico per ogni cosa.

---

## 0. Stato attuale del sito (2026-05-13)

- **18 fix tuoi** consegnati + verificati + Elena-approved (vedi `HANDOFF_ELENA_PRE_CUT.md` § 1 per la lista dettagliata)
- **12/12 template Design Handoff** allineati al design system
- **19 competenze** ora tutte editabili da WP-Admin (Wave 6.0 partial stabilizzata: post 2670 e simili sbloccati post sanitize)
- **18 Pages canoniche** (post consolidamento P7 chi-siamo=lo-studio)
- **13 Pages SCF-only** (Gutenberg disabilitato — vedi tabella canonica in EDITOR-HANDOFF § 3.7)
- **Tag release tecnico** `v1.3.24-wave-6-0-partial-stabilized` (per audit Adsolut)
- **CUT-READY**: aspettiamo finestra concordata con Avv. Saltelli per DNS switch staging → produzione

---

## 1. Il tuo workflow quotidiano (3 modalità)

### Modalità A — Content editor (95% del tuo tempo)

Stai editando contenuto editoriale: copy, FAQ, casi rappresentativi, scenari per term, bio avvocati (campi safe), articoli del blog.

**Cosa fare:**
1. WP-Admin → trova la cosa da editare (mappa "voglio editare X" in `EDITOR-HANDOFF.md` § 3 TL;DR)
2. Modifica via metabox SCF dedicato (sotto il title della pagina/CPT)
3. Salva. Verifica frontend in browser.
4. Se non è quello che pensavi → torna a editare. È iterativo.

**Quando contattare tech**: mai per questa modalità (è il tuo terreno).

### Modalità B — QA bug hunter (1-2 sessioni a settimana di review visivo)

Stai navigando il sito come utente reale (browser desktop + mobile DevTools 375px), cerchi cose che non quadrano: layout rotti, copy strani, link rotti, pulsanti che non fanno nulla, contenuto che non si aggiorna dopo aver salvato in WP-Admin.

**Cosa fare:**
1. Apri staging in browser pulito (no cache, magari incognito)
2. Naviga gli URL critici (lista in § 4)
3. Quando trovi qualcosa che non quadra → documenta (vedi § 2 bug report template)
4. Invia a tech@adsolut.it con email strutturata
5. Tech triage e risolve, ti aggiorna

**Quando contattare tech**: ogni bug, ma sempre con template compilato. Niente "il sito è rotto" senza dettagli.

### Modalità C — Crisis (raro, ma serve workflow)

Sito giù, errore 500, pagina bianca su una pagina critica, link rotto che porta a 404 da home.

**Cosa fare:**
1. **Email + chiamata** simultanea a tech@adsolut.it / numero diretto
2. Subject email: `URGENTE — [cosa è giù] su [URL]`
3. Allegare screenshot
4. **Non tentare fix** dal CMS (potresti peggiorare)
5. Attendi conferma tech che è preso in carico

---

## 2. Bug report template (formato standard)

Quando trovi un bug visivo/UX, copia questo template e compila. Invialo a `tech@adsolut.it` come email standalone.

```
SUBJECT: Bug Saltelli [breve descrizione] — [URL pagina]

PRIORITÀ: [BLOCKER / ALTA / MEDIA / BASSA]
  - BLOCKER: utente non può completare azione critica (form non invia, link rotto navigazione)
  - ALTA: layout rotto su pagina importante (home, aree di pratica, contatti)
  - MEDIA: copy errato, immagine sbagliata, hover che non funziona
  - BASSA: spacing leggermente off, micro-glitch

URL: https://staging.studiolegalesaltelli.it/[percorso-esatto]
DEVICE: [Desktop 1440px / Tablet 768px / Mobile 375px]
BROWSER: [Chrome / Safari / Firefox / versione]

COSA HO FATTO:
1. Sono andata su [URL]
2. Ho cliccato/scrollato/hover su [elemento]
3. Mi aspettavo [comportamento atteso]
4. Invece è successo [comportamento reale]

SCREENSHOT:
[Allega 1-3 screenshot del problema. Usa frecce/cerchi per evidenziare se utile]

CONTENT CHE STAVO EDITANDO (se applicabile):
- Pagina/CPT/Term: [es. Pagina "Contatti" ID 23]
- Campo SCF: [es. "Aree di interesse" dropdown nel form]
- Cosa ho cambiato: [es. ho aggiunto 2 nuove voci, ho salvato]

NOTE EXTRA:
[Qualsiasi cosa pensi sia utile: succede ogni volta o random? Solo a te o anche ad altri? È regressione recente o era già così?]
```

### Esempio bug report compilato

```
SUBJECT: Bug Saltelli — Filtro casi rappresentativi non aggiorna risultati su mobile — /chi-siamo/casi-rappresentativi/

PRIORITÀ: MEDIA

URL: https://staging.studiolegalesaltelli.it/chi-siamo/casi-rappresentativi/
DEVICE: Mobile 375px (Chrome DevTools)
BROWSER: Chrome 127

COSA HO FATTO:
1. Apro /chi-siamo/casi-rappresentativi/ su mobile
2. Tap sul filtro "Per i privati"
3. Mi aspettavo che la lista mostrasse solo casi privati
4. Invece la lista non cambia, restano tutti i casi

SCREENSHOT:
[screenshot allegato]

NOTE EXTRA:
Su desktop 1440 il filtro funziona. Quindi sembra un bug solo mobile.
```

---

## 3. Quando NON contattare tech (puoi fare da sola)

| Cosa vuoi fare | Dove vai |
|---|---|
| Editare il copy di una Pagina (es. "Chi siamo") | WP-Admin → Pagine → [nome] → modifica metabox SCF |
| Aggiungere/modificare una FAQ | WP-Admin → Pagine → "Domande frequenti" → metabox FAQ |
| Modificare bio breve di un avvocato | WP-Admin → Avvocati → [nome] → metabox SCF (NON toccare `bio_estesa`) |
| Cambiare contenuto di una competenza (area di pratica) | WP-Admin → Aree di Pratica → [nome] → metabox SCF "Body" |
| Aggiungere un nuovo articolo del blog | WP-Admin → Articoli → Aggiungi nuovo (Gutenberg) |
| Popolare 3 scenari per term "Contenzioso amministrativo" (Bug feedback #15) | WP-Admin → Articoli → Tipo area → "Contenzioso amministrativo" → metabox term |
| Modificare un caso rappresentativo | WP-Admin → Casi → [nome] → metabox SCF |
| Cambiare orari di apertura, telefono, indirizzo | WP-Admin → Saltelli Settings → tab Contatti |

Tutto il resto → tech@adsolut.it.

---

## 4. URL critici per QA visivo (15 URL settimanali)

Quando fai sessione QA settimanale, copri questi URL nell'ordine. Ognuno richiede 1-2 min:

### Public (frontend pubblico)
1. https://staging.studiolegalesaltelli.it/ (homepage)
2. https://staging.studiolegalesaltelli.it/chi-siamo/
3. https://staging.studiolegalesaltelli.it/chi-siamo/team/
4. https://staging.studiolegalesaltelli.it/chi-siamo/team/emiliano-saltelli/
5. https://staging.studiolegalesaltelli.it/chi-siamo/casi-rappresentativi/
6. https://staging.studiolegalesaltelli.it/aree-di-pratica/
7. https://staging.studiolegalesaltelli.it/aree-di-pratica/privati/
8. https://staging.studiolegalesaltelli.it/aree-di-pratica/privati/diritto-tributario/ (tier-1)
9. https://staging.studiolegalesaltelli.it/aree-di-pratica/privati/responsabilita-medica/ (tier-2 recentemente migrata)
10. https://staging.studiolegalesaltelli.it/risorse/
11. https://staging.studiolegalesaltelli.it/risorse/domande-frequenti/
12. https://staging.studiolegalesaltelli.it/risorse/blog/
13. https://staging.studiolegalesaltelli.it/costi-e-consulenze/
14. https://staging.studiolegalesaltelli.it/contatti/
15. https://staging.studiolegalesaltelli.it/prenota-appuntamento/

Per ognuno verifica almeno:
- ✅ Layout corretto (no rotture visive)
- ✅ Titolo H1 sensato + copy editoriale leggibile
- ✅ Link cliccabili e portano dove ti aspetti
- ✅ Immagini caricano (no broken image icon)
- ✅ Form (dove presente) si possono inviare
- ✅ Hover effects funzionano (bottoni, card)
- ✅ Mobile responsive (DevTools 375px)

### Admin (WP-Admin)
16. https://staging.studiolegalesaltelli.it/wp-admin/ (login + dashboard)
17. https://staging.studiolegalesaltelli.it/wp-admin/edit.php?post_type=competenza (lista Aree di Pratica)
18. https://staging.studiolegalesaltelli.it/wp-admin/edit.php?post_type=avvocato (lista Avvocati)

Per ognuno verifica:
- ✅ Lista mostra tutti gli items che ti aspetti
- ✅ Click su un item apre la pagina edit
- ✅ Edit page ha tutti i metabox SCF popolati come ti aspetti
- ✅ Salva funziona, frontend si aggiorna dopo

---

## 5. Cosa NON toccare (hard rule)

Vedi `HANDOFF_ELENA_PRE_CUT.md` § 3 + `EDITOR-HANDOFF.md` § 13. Sintesi:

- ❌ Aspetto → Personalizza, "CSS aggiuntivo" — bloccato per editor (è voluto)
- ❌ Editor Gutenberg sulle 13 Pages SCF-only — modifica via metabox SCF della Pagina stessa
- ❌ Campi `bio_estesa` sulle 4 schede avvocato — solo via tech@adsolut.it
- ❌ Foto Emiliano Saltelli (`_thumbnail_id=2683`) — hard-protected
- ❌ Plugin (attiva/disattiva/install) — solo amministratori tech
- ❌ Eliminazione di Pages canoniche (18 in lista) — rompe routing + menu
- ❌ Slug `/chi-siamo/`, `/aree-di-pratica/`, `/risorse/`, `/costi-e-consulenze/`, `/contatti/`, `/prenota-appuntamento/`
- ❌ Codice del tema (`wp-content/themes/saltelli/`) — è zona Adsolut, gestita via Git

---

## 6. Workflow Wave 6.0 partial (importante per le 16 competenze migrate)

A maggio 2026 abbiamo migrato 16 CPT competenza da `post_content` (Gutenberg classic) a `body_extended` (SCF wysiwyg). Stato finale:

- **3 tier-1** (Diritto tributario, Diritto del lavoro, Diritto di famiglia LGBTQ+) — `body_extended` già popolato da te in Wave 5 STEP 3
- **16 tier-2** (es. Responsabilità medica, Recupero crediti, Aste immobiliari, Infortunistica stradale, Diritto condominiale, etc.) — `body_extended` popolato dal nostro script + sanitizzato
- **5 draft non pubblicate** (Diritto societario, Contrattualistica, Ricorsi, Domiciliazione, Consulenze online) — non ancora live

**Cosa significa per te quando modifichi una competenza**:
1. Apri WP-Admin → Aree di Pratica → [nome competenza]
2. Vedi **Gutenberg editor vuoto** (sopra) — **NON metterci contenuto qui** (verrebbe ignorato, il template renderizza `body_extended`)
3. Vedi **metabox SCF "Competenza"** (sotto/a fianco) → tab **"Body"** → campo `body_extended` (WYSIWYG TinyMCE)
4. **Modifica TUTTO da body_extended** (visual o text editor)
5. Salva. Frontend si aggiorna.

Note tecniche (per tua trasparenza, ma non devi farci niente):
- Se l'editor Gutenberg dovesse ritornare con contenuto strano (es. "blocco classico" con HTML legacy), ignoralo. Stiamo pianificando Wave 6.0 full che disabiliterà Gutenberg per le competenze in modo definitivo (post-cut).
- Il content "vecchio" è preservato come backup `_legacy_post_content_backup` (postmeta nascosto). Se serve rollback per un singolo post, tech@adsolut.it può recuperarlo.

---

## 7. Roadmap cut produzione (cosa accade quando)

| Step | Quando | Chi |
|---|---|---|
| **Tu finisci minor UX fix + popolamento scenari term** | Settimana corrente | Elena |
| **Tu ci scrivi "OK pronto per cut"** | Quando soddisfatta | Elena → tech |
| **Concordiamo finestra con Avv. Saltelli** | Email/chiamata | Adsolut + Cliente |
| **DNS switch staging → prod** | Window 30-60 min | Adsolut tech |
| **SSL produzione + WP URL update** | Subito dopo DNS | Adsolut tech |
| **Smoke test 8 URL critici prod** | Window 30 min | Adsolut tech |
| **Email finale a te + cliente: "siamo live"** | Fine window | Adsolut tech |
| **Onboarding 30 min produzione (opzionale)** | Settimana dopo | Elena + Adsolut |

### Cosa accadrà DOPO il cut (backlog post-cut)

| Wave | Cosa | Timeline | Impatto su te |
|---|---|---|---|
| **6.0 full** | Disabilitare Gutenberg per CPT competenza (chiude UX confusion) | 1-2 settimane post-cut | Editor competenze più chiaro: solo metabox SCF |
| **6.1 SCF orphan cleanup** | Rimuovere field group legacy non più usati | 1-2 settimane post-cut | Page 2714 prenota-appuntamento avrà 1 metabox solo (oggi 2) |
| **P11 Contatti** | Dropdown "Area di interesse" dinamico da CPT competenza | 1-2 settimane post-cut | Form contatti si auto-aggiorna se aggiungi/togli competenze |
| **5.1 Image Expansion** | Foto reali al posto di Picsum placeholders | Quando foto pronte da cliente | Hero homepage + Plate I + ritratti veri |
| **Single-post JSX request** | Template editoriale dedicato per i 326 articoli blog | 2-3 settimane post-cut (richiede Design) | Articoli avranno layout migliore SEO + readability |
| **EDITOR-HANDOFF v6.1** | Manuale CMS aggiornato post tutte le wave recenti | 1 settimana post-cut | Documentazione fresca per te |

---

## 8. Contatti tech (Adsolut)

| Caso | Chi | Come | SLA |
|---|---|---|---|
| Bug visivo / UX (priorità BASSA/MEDIA) | Adsolut tech | tech@adsolut.it con bug report template § 2 | Risposta entro 24h |
| Bug ALTA priorità (layout rotto pagina importante) | Adsolut tech | tech@adsolut.it (subject "ALTA") + Slack se canale attivo | Risposta entro 4h working hours |
| BLOCKER / Crisis (sito giù, form non invia) | Adsolut on-call | tech@adsolut.it + chiamata diretta | Immediato |
| Voglio modificare layout/CSS/colore | Adsolut tech (NON via CMS) | tech@adsolut.it (richiesta cambio design) | Risposta entro 48h |
| Voglio aggiungere/rimuovere una Pagina canonica | Adsolut tech (impatta routing/menu/SEO) | tech@adsolut.it | Risposta entro 48h |
| Domande su come edito X (CMS quotidiano) | Da sola via EDITOR-HANDOFF.md | `docs/EDITOR-HANDOFF.md` § 3 TL;DR | — |

---

## 9. Versione corrente & repo

- **Tema**: v1.3.24-wave-6-0-partial-stabilized
- **Staging**: https://staging.studiolegalesaltelli.it
- **Production** (post-cut): https://studiolegalesaltelli.it
- **Repo GitHub** (read-only per te, gestita Adsolut): https://github.com/Adsolut-Ai-Agency/saltelli-wp/
- **Tag release stabile**: `v1.3.24-wave-6-0-partial-stabilized`
- **Documentazione live**: `docs/` folder nella repo (EDITOR-HANDOFF, ARCHITECTURE, DESIGN, DEPLOY, PRODUCT, BRIEF + questo file)

---

## 10. Tip pratici per QA efficace

1. **Browser pulito**: usa modalità incognito per QA. Evita cache stale che ti fa vedere vecchie versioni.
2. **2 finestre affiancate**: una con frontend, una con WP-Admin. Modifichi e vedi subito.
3. **DevTools mobile**: F12 → Toggle device toolbar (Ctrl+Shift+M Chrome) → 375px per mobile.
4. **Screenshot rapidi**: usa tool nativo OS (macOS: Cmd+Shift+4 area; Windows: Win+Shift+S area).
5. **Naming screenshot**: includi URL + data (es. `bug-casi-filter-mobile-20260513.png`) — facilita riconoscimento per tech.
6. **Test ogni 2-3 modifiche, non 20**: se modifichi 20 campi e qualcosa si rompe, non sai quale modifica ha causato il bug.
7. **Confronto desktop vs mobile**: sempre. Molti bug sono solo mobile.
8. **Test link link**: clicca ogni CTA, ogni breadcrumb. Bug rotto silenziosi sono comuni.
9. **Test form submission**: anche se sembrano funzionare, prova a inviare realmente.
10. **Test hover effects**: passa il mouse su tutto. Hover che non fanno nulla = bug visivo.

---

*Questo handoff è il riferimento operativo continuativo. Sarà aggiornato con `EDITOR-HANDOFF.md v6.1` dopo il cut produzione. Maintained by Adsolut SRLS · tech@adsolut.it.*
