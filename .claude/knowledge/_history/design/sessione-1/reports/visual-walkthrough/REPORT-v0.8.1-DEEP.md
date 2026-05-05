# Visual Walkthrough Approfondito v0.8.1 — Bug typography + Wall of Text

> **Walkthrough condotto durante esecuzione Recovery Agent v0.9.0.** Focus richiesto da Duccio:
> - Testo poco arioso (titoli attaccati a sottotitoli e contenuti)
> - Immagini sparate a sinistra senza impaginazione
> - Effetto "wall of text" che rovina l'effetto tipografico
> - Bisogno di armonia testo/immagini

**Scope:** navigazione sistematica oltre i 12-point classici. Ho controllato blog post (5 sample), `/blog/` archive, `/contatti/`, `/lo-studio/`, search results, 404. Risultato: trovati **8 nuovi bug di severità alta** che il Recovery Agent v0.9.0 NON sta toccando perché il suo prompt era focused solo sui 6 FAIL precedenti.

---

## 🔴 GRUPPO A — BUG TYPOGRAPHY (wall of text)

### A1. Spazio NULLO tra titolo blog post e sotto-headline (lede)

**Sample testato:** `/intimazione-tari-annullata-vittoria-dello-studio-legale-saltelli/`, `/buoni-fruttiferi-postali-ottieni-il-rimborso/`, `/cartelle-prescritte-se-ignori-lintimazione-di-pagamento-rischi-il-pignoramento-cassazione-2025/`

**Sintomo:**
```
Cartelle prescritte: se ignori l'intimazione di
pagamento rischi il pignoramento (Cassazione 2025)        ← H1 Playfair gigante
Ti arriva una intimazione di pagamento dell'Agenzia       ← LEDE attaccata, ZERO margin
delle Entrate-Riscossione...
```

L'H1 finisce e il primo paragrafo del lede inizia **alla stessa riga successiva**, senza margin-bottom sull'h1 (oppure h1 ha `margin-bottom: 0` e il `<p>` lede ha `margin-top: 0`). Visivamente sembrano un unico blocco.

**Atteso:** spazio editoriale 32-48px tra H1 e primo paragrafo lede + lede in serif italic dimensione media (per stacco gerarchico).

**Affligge:** **TUTTI i 326 blog post** + tutti i CPT competenza.

### A2. H2 sub-section attaccati al testo precedente E al successivo

**Sample testato:** stesso `/cartelle-prescritte-...`

**Sintomo:**
```
...ad esempio contro un pignoramento.
Oggi non è più così.
Secondo la Cassazione, l'intimazione è un atto che deve essere
contestato subito, altrimenti:
                                                          ← ZERO spazio
L'intimazione non è più un atto "da ignorare"             ← H2 incollato sopra
Fino a ieri molti pensavano che l'intimazione...          ← Testo attaccato sotto
```

H2 in Playfair grande "L'intimazione non è più un atto..." appare con **margin-top minimo** rispetto al paragrafo precedente, e con margin-bottom minimo rispetto al successivo. Effetto: **non sembra una sezione**, sembra un "titolo decorativo" buttato dentro.

**Atteso:** `margin-block: 80px 24px` sugli h2 dentro `.entry-content` (80 sopra per stacco da prosa precedente, 24 sotto per attaccare alla prosa di sezione).

### A3. Wall of text senza pull-quote, senza drop-cap, senza letteringure

**Sample:** stesso post, parte centrale

Vedo paragrafi consecutivi senza alcuna pausa visiva:
```
il debito si "cristallizza"
diventa inattaccabile
l'Agenzia può procedere con pignoramenti...
```

3 bullet con icone emoji "✅" e "👉" + paragrafi corti. Funzionalmente ok ma:
- **Manca il drop-cap** sul primo paragrafo (presente solo su /lo-studio/ homepage e /costi/)
- **Manca pull-quote** editoriale per citazioni Cassazione importanti
- **Manca varietà ritmica** tra blocchi (tutti uguali in size + line-height)

### A4. List `<li>` in `<ul>` non stilizzati come pattern editoriale

Le liste bullet (sia dentro post che su `/contatti/` e `/lo-studio/`) usano **bullet di default browser** (•) in nero/text-color, senza il trattamento editoriale del resto del sito (mono o accent).

---

## 🔴 GRUPPO B — BUG IMMAGINI

### B1. Immagini sentenze/documenti sparate a sinistra **senza container**

**Sample testato:** `/intimazione-tari-annullata-...`, `/cartelle-prescritte-...`, `/lo-studio-legale-saltelli-fa-annullare-fermo-amministrativo-...` (path di /lo-studio/ rotto, vedi C1)

**Sintomo:**
```
[colonna sinistra fino a ~830px width]
  Documento firmato digitalmente
  Il Giudice                          ← TESTO SOTTILE BIANCO
  [scarabocchi rossi anonimizzazione]

  Sentenza n. 20910/2025
  ...
[colonna destra: VUOTA]
```

L'immagine della sentenza occupa solo i primi ~830px da sinistra, lasciando la colonna destra **completamente vuota**. Manca:
- `max-width` adatto (dovrebbe essere `max-width: 720px` come il resto della prosa)
- `margin: 32px auto` per centrare
- `figure` wrapper con caption mono uppercase ("Sentenza · CGT Napoli · Sez. 15 · 2025")
- shadow editoriale sottile o `border: 1px solid var(--border)` per definire il box

### B2. Immagine featured "epica" (illustrations stock/AI) gigantesca e fuori-mood

Vedi `/cartelle-prescritte-...`: l'immagine featured è un'illustrazione cartoon di "uomo terrorizzato che legge intimazione TARI" larga **tutto il viewport** (1562px). È un'immagine stock-AI generata, NON nello stile del resto del sito.

Stessa cosa per `/buoni-fruttiferi-postali-...`: foto storica di un Buono Fruttifero Postale Lire 5.000.000 a tutta larghezza.

**Issue compounded:**
- **Dimensione**: too big, fa "aterizzare" un sito che dovrebbe essere editoriale-minimal
- **Scelta**: stock illustrations tutte diverse stylisticamente, rovinano la **coerenza brand** Legal Luxury Minimal

### B3. Foto avvocato Emiliano in fondo al post (full-width)

Stesso `/intimazione-tari-...` scrollando in fondo: appare la **foto reale di Emiliano** in formato ENORME (1562px width × 800px+). È la stessa foto del CPT avvocato (5MB DSLR), ma usata come **decoration di fine post**.

**Issue:**
- Dimensione: 1562×800 spara visivamente il volto di Emiliano gigante
- Posizione: dovrebbe essere un **piccolo author bio card** (foto 80×80 + nome + titolo + link al profilo)
- Manca contesto: sembra messo lì a caso, non come "scritto da Emiliano Saltelli"

---

## 🔴 GRUPPO C — BUG ROUTING/CONTENT

### C1. `/lo-studio/` redirect a un blog post invece di pagina "Chi siamo"

Apri `/lo-studio/` → vai a `/lo-studio-legale-saltelli-fa-annullare-fermo-amministrativo-napoli-obiettivo-valore-ancora-battuta/`.

Il menu "Studio" punta a `/lo-studio/` ma quella URL **rimanda al primo post che inizia con "lo-studio-..."** (WP rewrite rule).

**Già segnalato dall'audit CRO sezione 9.9 punto #5**: "Correggere link Lo Studio — attualmente porta a un articolo blog, non alla pagina Chi Siamo".

**Non è stato risolto in nessuna fase precedente.** È un blocker CRO.

### C2. `/blog/` archive vuoto

Apri `/blog/` → mostra solo:
- Eyebrow "BLOG"
- H1 "Blog"
- 2 categorie ("BLOG LEGALE" + "Informazioni legali") con descrizione
- 326 post pubblicati nel DB **non appaiono**

`<article>` count nell'HTML = 1 (probabile è solo il container categoria).

**Atteso:** lista paginata dei 326 post con featured image small + meta + excerpt.

### C3. `/contatti/` content quasi vuoto

Apri `/contatti/` → vedo:
- "Hai bisogno di aiuto?"
- "Contattaci"
- "Chiedi qualsiasi cosa. In qualsiasi momento"
- NAP basic
- "Siamo sempre alla ricerca di nuovi Legali" + link "Invia candidatura"

Ma **manca**:
- Form di contatto (Quick Win CRO #4 dell'audit)
- Mappa (Google Maps embed di Via Vannella Gaetani 27)
- Foto sede (placeholder editoriale ok, ma NON c'è nessun visual)
- WhatsApp / sticky CTA mobile (Quick Win CRO #5)
- "Prima consulenza gratuita" gancio (FIX ATTUALE è solo su Hero homepage)

---

## 🟡 GRUPPO D — BUG MINORI ma visibili

### D1. Search results header Playfair gigante "Risultati per *cartelle*"

Funziona ma il layout è asimmetrico:
- Eyebrow "RICERCA" stretto a sinistra in alto
- "Risultati per *cartelle*" parte molto a destra (colonna larga)
- Form "cerca" in alto a sinistra ma piccolo, button "CERCA →" appiccicato

Ok funzionalmente, ma è un layout **non ottimizzato**.

### D2. 404 page semplice ma senza personalità

`/non-esiste-404/` mostra:
- "§ 404"
- "Pagina non trovata."
- "La pagina che stai cercando non esiste o è stata spostata. Probabilmente l'URL è cambiato dopo la migrazione del sito."
- 3 link: "Torna alla home / Aree di pratica / Contatti"
- Form ricerca

Funziona ma è **plain**. Manca opportunità per:
- "Forse cerchi una di queste 19 aree?" mini-lista 5 più cliccate
- Foto/illustrazione editoriale (pattern Bick Law 404)
- CTA "Prenota una consulenza gratuita"

---

## 🟢 Cosa funziona EFFETTIVAMENTE bene

- Hero homepage (3 righe, drop-cap tier-1)
- Lista 19 aree con tier-1 first
- Sezione "Lo studio" homepage (drop-cap "L")
- 4 avvocati homepage asimmetrici
- Casi rappresentativi homepage tipografici
- Footer dark navy
- /costi/ layout (post-recovery — DA VERIFICARE post-fix v0.9.0)
- Single avvocato Emiliano (foto reale, sticky no overlap)
- Taxonomy /tipo-area/{privati,imprese,contenzioso,altri}/
- Search functional
- Schema 16/16 validi

---

## 📊 Score COMPLETO finale (v0.8.1)

| Categoria | Issue | Severità |
|---|---|:---:|
| Recovery v0.9.0 in flight | F1-F6 (6 issues, agent sta lavorando) | 🔴 in fix |
| **GRUPPO A** Typography wall of text | A1 H1↔lede, A2 H2 senza respiro, A3 wall of text, A4 list bullet | 🔴 NUOVI |
| **GRUPPO B** Immagini | B1 sentenze a sinistra, B2 stock images out-of-mood, B3 foto autore gigante | 🔴 NUOVI |
| **GRUPPO C** Routing/Content | C1 /lo-studio/ rotto, C2 /blog/ vuoto, C3 /contatti/ scarno | 🔴 NUOVI |
| **GRUPPO D** Minor | D1 search layout, D2 404 plain | 🟡 NUOVI |

**Bug totali nel walkthrough approfondito: 6 (Recovery in flight) + 11 (nuovi gruppi A-D) = 17 issues totali.**

---

## 🎯 Decisione: post-Recovery v0.9.0, prossimo agent affronta GROUP A + B + C + D

Il Recovery Agent v0.9.0 sta lavorando sui 6 FAIL precedenti. Quando finisce, il prossimo prompt deve essere un **Editorial Refinement Agent** che affronta:

1. **GROUP A — Typography respiro** (CSS-only, no DB change)
   - margin-block su h1, h2, h3 dentro `.entry-content` e `.sl-post__body`
   - Drop-cap su primo paragrafo lede di tutti i blog post
   - Lede in serif italic medio sotto h1 con margin top adeguato
   - List `<li>` editoriali con bullet color accent

2. **GROUP B — Immagini con cornice editoriale** (CSS + filter PHP/template)
   - `figure` wrapper auto su tutte le `<img>` nei post body
   - Caption mono uppercase auto-generata (alt + width × height)
   - `max-width: 720px; margin: 48px auto` su tutte le images dentro prose
   - Author bio card piccolo (80×80 foto + nome + titolo + link CPT) sostituisce foto gigante autore in fondo

3. **GROUP C — Routing fix + content arricchito**
   - `/lo-studio/` redirect manuale o slug rinaming
   - `/blog/` template che mostra i 326 post (probabile bug template `archive.php` che non rende il loop)
   - `/contatti/` aggiungere mappa Google + form

4. **GROUP D — Polish minor** (skip se tempo limitato)

---

## Screenshot evidenziali raccolti

- ss_3679vtwrq + ss_5094zx5m2 — Sentenza intimazione TARI a sinistra senza container (B1)
- ss_65118vpia + ss_7945530no — Foto Emiliano gigante a fondo post (B3) + wall of text (A1, A2, A3)
- ss_9629pc99c + ss_104547h4d — Buono fruttifero stock image gigante (B2)
- ss_9341k0idy + ss_0767zjymm + ss_2185gvvc2 — Cartelle prescritte: comic AI image gigante (B2) + pattern wall of text confermato
- ss_81052pqlc — /blog/ archive con solo categorie, 326 post mancanti (C2)
- ss_67167opcl + ss_8145jumf4 — /contatti/ scarno senza form/mappa/foto (C3)
- ss_10007u0kd + ss_2398n5zp1 — /lo-studio/ porta a blog post, NON alla pagina (C1)
- ss_3780w4rho — Search results "cartelle" funzionale ma layout asimmetrico
- ss_6517l422s — 404 plain (D2)

---

*Walkthrough approfondito completato. Aspetto fine Recovery v0.9.0 per produrre prompt Editorial Refinement Agent.*
