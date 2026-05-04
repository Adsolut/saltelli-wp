# Manuale Editoriale — Studio Legale Saltelli

> **Destinatari:** Elena Cappabianca, Ludovica Casa, eventuali collaboratori editoriali esterni Adsolut.
> **Versione:** 1.0 (post Wave 3 — 2026-05-04)
> **Mantenuto da:** Adsolut SRLS · tech@adsolut.it
> **Ambiente coperto:** staging + production WordPress dello Studio Legale Saltelli.

Questo documento è la **mappa operativa** per gestire i contenuti del sito senza dover chiamare il tecnico ogni volta. Leggi le sezioni 0–2 una volta tutte. Le altre tienile come riferimento da consultare quando ti serve un'azione specifica.

---

## Indice

0. [Cosa devi sapere prima di iniziare](#0-cosa-devi-sapere-prima-di-iniziare)
1. [Accesso al WP-Admin](#1-accesso-al-wp-admin)
2. [TL;DR — Mappa rapida "voglio editare X"](#2-tldr--mappa-rapida-voglio-editare-x)
3. [Saltelli — Settings (impostazioni globali)](#3-saltelli--settings-impostazioni-globali)
4. [Le 9 pagine custom](#4-le-9-pagine-custom)
5. [Le 4 schede avvocato](#5-le-4-schede-avvocato)
6. [Le 19 aree di pratica (Competenze)](#6-le-19-aree-di-pratica-competenze)
7. [I moduli riutilizzabili (FAQ, Casi, Principi…)](#7-i-moduli-riutilizzabili-faq-casi-principi)
8. [Il blog](#8-il-blog)
9. [Convenzioni di scrittura editoriale](#9-convenzioni-di-scrittura-editoriale)
10. [Cosa NON toccare mai](#10-cosa-non-toccare-mai)
11. [Upload immagini e PDF](#11-upload-immagini-e-pdf)
12. [Anteprima, cache, troubleshooting](#12-anteprima-cache-troubleshooting)
13. [Quando scrivere al team tecnico](#13-quando-scrivere-al-team-tecnico)

---

## 0. Cosa devi sapere prima di iniziare

### Il sito è un **tema custom**, non un page builder

Niente Elementor, Divi, WPBakery. Ogni pagina ha una **struttura editoriale fissa** (hero, lede, body, CTA finale, ecc.) e tu **riempi i campi**, non disegni il layout. Questo significa:

- ✅ Cambiare un titolo, un paragrafo, un'immagine, una CTA → **ti basta editare un campo**
- ✅ Aggiungere una FAQ, un caso vinto, una guida gratuita → **crei un nuovo "elemento" e lo si aggancia automaticamente**
- ❌ Spostare i blocchi di una pagina, cambiare la struttura del template, modificare i colori → **tecnico**

### I contenuti sono "in pezzi", non in un unico Word

Una pagina come `/costi/` non è un blob unico: ha un titolo, un sottotitolo, un trust box laterale, un blocco editoriale, una CTA finale. Ognuno è un campo separato, con la sua casellina sulla pagina di edit. Vedrai **tab e gruppi etichettati** (Hero · Aside · Body · CTA finale) che ti aiutano a orientarti.

### Lo "Studio — Settings" è il pannello che governa tutto

Prima di toccare le pagine, vai sempre a vedere se quello che vuoi modificare è in **Saltelli — Settings** (sidebar admin, in basso). Lì stanno: indirizzo, telefono, email, P.IVA, social, payoff, CTA default. Cambiando un valore lì, **si aggiorna ovunque** sul sito (header, footer, pagine multiple). Vedi sezione 3.

### Le modifiche sono visibili dopo qualche secondo

Il sito ha cache abilitata. Dopo un salvataggio, attendi 5–10 secondi e ricarica la pagina pubblica con `Ctrl+Shift+R` (Win) o `Cmd+Shift+R` (Mac) per saltare la cache del browser. Se dopo 30 secondi non vedi la modifica, vedi sezione 12.

---

## 1. Accesso al WP-Admin

### URL

| Ambiente | URL admin | Note |
|---|---|---|
| **Staging** (lavoro corrente) | https://staging.studiolegalesaltelli.it/wp-admin/ | Ambiente di prova, visibile pubblicamente ma non indicizzato |
| **Production** | https://studiolegalesaltelli.it/wp-admin/ | Sito live (DNS switch successivo al sign-off) |

### Account disponibili

| Username | Email | Ruolo | Quando usarlo |
|---|---|---|---|
| `Emiliano Saltelli` | info@studiolegalesaltelli.it | Administrator | Account proprietario — usato da Avv. Saltelli quando entra di persona |
| `Adsolut Staff` | tech@adsolut.it | Administrator | Account agency — uso quotidiano del team Adsolut (incluso editorial) |

**Le password sono allineate locale↔staging**. Per la production verranno ruotate al deploy. Tieni le credenziali nel password manager Adsolut. Non condividerle via email/chat.

### Per il collaboratore esterno

Se entra un freelance editorial, **NON dargli il login Administrator**. Aprire ticket a `tech@adsolut.it` chiedendo creazione utente con ruolo **Editor** (può creare/modificare contenuti ma non installare plugin né cambiare impostazioni globali). L'utente Editor vede tutto quello descritto in questo manuale tranne `Saltelli — Settings` e i settings di sistema.

---

## 2. TL;DR — Mappa rapida "voglio editare X"

| Voglio modificare… | Vai su | Sezione di questo doc |
|---|---|---|
| Indirizzo, telefono, email, P.IVA, ordine | **Saltelli — Settings** → Studio Info | §3 |
| Coordinate mappa (lat/lng) | **Saltelli — Settings** → Mappa | §3 |
| Payoff sotto il logo, brand statement footer | **Saltelli — Settings** → Brand | §3 |
| Link social (Instagram, LinkedIn, Facebook, X) | **Saltelli — Settings** → Social | §3 |
| Testo del bottone "Prenota un incontro" usato di default | **Saltelli — Settings** → CTA Defaults | §3 |
| Hero, body, CTA della pagina `/costi/` | **Pagine** → Costi | §4 |
| Hero, body, CTA della pagina `/casi/` | **Pagine** → Casi | §4 |
| Hero + mappa + come arrivare di `/contatti/` | **Pagine** → Contatti | §4 |
| Hero + intro di `/faq/` | **Pagine** → FAQ | §4 |
| Una delle 5 pagine "info shared" (`/come-lavoriamo/`, `/prima-consulenza/`, `/lavora-con-noi/`, `/richiedi-preventivo/`, `/guide-gratuite/`) | **Pagine** → la pagina che ti serve | §4 |
| Bio, foto, contatti di un avvocato | **Avvocati** → l'avvocato che ti serve | §5 |
| Pagina di un'area di pratica (es. Diritto tributario) | **Aree di pratica** → l'area che ti serve | §6 |
| Aggiungere/modificare una FAQ | **FAQ** → New (o esistente) | §7 |
| Aggiungere un caso vinto | **Casi rappresentativi** → New | §7 |
| Aggiungere una guida gratuita PDF | **Guide gratuite** → New | §7, §11 |
| Aggiungere una formazione/titolo a un avvocato | **Formazione & Titoli** → New, poi associare in scheda avvocato | §7, §5 |
| Articolo del blog | **Articoli** → New (o esistente) | §8 |

---

## 3. Saltelli — Settings (impostazioni globali)

📍 **Dove**: sidebar WP-Admin, voce **Saltelli — Settings** (icona cogwheel, posizione 60).

Sono **6 tab**. Tutto quello che metti qui appare in **tutte le pagine** del sito (header, footer, schede contatti, ecc.).

### Tab 1 — Studio Info

| Campo | Esempio attuale | Note |
|---|---|---|
| Via | Via Vannella Gaetani, 27 | Indirizzo civico |
| CAP + Città | 80121 Napoli | |
| Quartiere | Chiaia | Mostrato in footer + schede contatti |
| Orari settimana | Lun – Ven · 10:00 – 19:00 | Usa il separatore "·" e il trattino lungo "–" |
| Orari sabato | Sabato su appuntamento | |
| Telefono pubblico | +39 081 1813 1119 | **Formato E.164 con spazi** — il click-to-call lo normalizza |
| Email pubblica | info@studiolegalesaltelli.it | |
| PEC | emilianosaltelli@avvocatinapoli.legalmail.it | |
| P.IVA | 06685101211 | |
| Ordine professionale | Ordine degli Avvocati di Napoli | |

⚠️ **Coerenza NAP**. Cambiando un dato qui, cambia ovunque (footer, header click-to-call, schema JSON-LD). Mai duplicare l'indirizzo dentro il body di una pagina specifica: se ti serve scrivere "venite a trovarci in via Vannella Gaetani 27", scrivi solo il riferimento testuale, ma i dati strutturati restano qui.

### Tab 2 — Mappa

| Campo | Esempio | Note |
|---|---|---|
| Latitudine | 40.8332541 | Decimal degrees, max 7 decimali |
| Longitudine | 14.2414699 | Idem |

Servono allo schema JSON-LD `LocalBusiness` (le AI capiscono dove siamo geograficamente). Per ottenere le coordinate: Google Maps → tasto destro sul pin → copia coordinate.

### Tab 3 — Brand

| Campo | Esempio | Note |
|---|---|---|
| Payoff | Diritto, con misura | Riga sotto il logo. Max 25 caratteri. |
| Brand statement | Un atelier legale italiano. Quattro avvocati a Chiaia. Vent'anni di Diritto al servizio di chi sceglie cura. | 1–2 frasi, usato in footer e in `/lo-studio/`. Tono editoriale, niente bullet point. |

### Tab 4 — Footer

| Campo | Esempio | Note |
|---|---|---|
| Credit text bottom | Realizzato da Adsolut Web Agency | Riga in basso a destra del footer |
| Credit URL | https://adsolut.it | Link del credit |
| Newsletter footer attiva? | Sì / No | Toggle per mostrare o nascondere il modulo iscrizione |
| Newsletter provider | Brevo / Mailchimp / Custom | Cambia il provider solo se richiesto da Avv. Saltelli |

### Tab 5 — Social

URL completi degli account ufficiali. Lascia vuoto se l'account non esiste (l'icona scompare automaticamente).

| Campo | Stato attuale |
|---|---|
| Instagram | https://www.instagram.com/studiolegalesaltelli/ ✅ |
| Facebook | https://www.facebook.com/share/1D1jCY7BnW/ ✅ |
| LinkedIn | (vuoto — da popolare quando lo Studio aprirà la company page) |
| X / Twitter | (vuoto — non in uso) |

### Tab 6 — CTA Defaults

I bottoni "Prenota un incontro" che vedi sparsi sul sito hanno **valori di default** che vengono presi da qui. Le singole pagine possono sovrascriverli, ma se non lo fanno usano questi.

| Campo | Esempio attuale |
|---|---|
| CTA default label | Prenota un incontro → |
| CTA default URL | /contatti/ |
| CTA trust signal default | Risposta entro 24 ore · Riservatezza assoluta |
| CTA subline italic | Prima consulenza conoscitiva gratuita |

⚠️ Cambiando questi, cambia il bottone in tante pagine. Verifica navigando 4–5 pagine dopo il salvataggio.

---

## 4. Le 9 pagine custom

📍 **Dove**: sidebar WP-Admin, voce **Pagine**.

Ogni pagina ha la propria struttura editoriale. Ti elenco la mappa pagina → blocchi modificabili. Tutti i campi sono descritti **dentro WP-Admin** con etichette e istruzioni: questa tabella ti serve come "indice" iniziale.

### Pagina `/costi/`

Slogan: trasparenza tariffaria. **6 blocchi modificabili** (ID 2695):

| Blocco | Campi principali |
|---|---|
| **Hero** | Eyebrow, H1 prefix, H1 italic, Lede italic |
| **Aside** (trust box laterale) | Eyebrow, H3, Paragrafo, CTA label, CTA URL |
| **§ 03 — Body editorial** ("Come calcoliamo") | Body editoriale completo (TinyMCE) — è il punto in cui spieghi come si calcola un preventivo |
| **CTA finale** | Eyebrow, H2, Paragrafo, Bottone label, Bottone URL, Trust line |

I blocchi **Modalità di consulenza** (3) e **Scenari costi** (3) e **Trust signals** (4) **non si modificano qui** — sono moduli separati: vedi sezione §7.

### Pagina `/casi/`

Hero + Intro editorial + CTA finale (ID 2699). I 10 casi rappresentativi sono in moduli separati (§7).

### Pagina `/contatti/`

Hero + mappa + come arrivare + trust signal (ID 23):

| Blocco | Campi |
|---|---|
| **Hero** | Eyebrow, H1 prefix, H1 italic, Lede italic |
| **Mappa** | Embed iframe (Google Maps o OpenStreetMap), Caption sotto |
| **Come arrivare** | Titolo, Indicazioni metro/bus, Indicazioni parcheggi |
| **Trust signal** | Riga reassurance sotto il form (privacy / orari risposta) |

💡 Il modulo di contatto è gestito da Contact Form 7 (sidebar **Contatti** → Form). Per modificare i campi del form, scrivi al tecnico (è raro che serva).

### Pagina `/faq/`

Hero + TOC title + CTA finale (ID 2705). **Le 28 FAQ in pagina sono moduli separati** organizzati per topic (§7).

### Le 5 pagine "info-shared" (layout standard)

Stesso layout per tutte e cinque, **stessi 16 campi**:

| Pagina | ID | Tema |
|---|---|---|
| `/come-lavoriamo/` | 2709 | Workflow dello studio |
| `/prima-consulenza/` | 2708 | Cosa aspettarsi alla prima consulenza |
| `/lavora-con-noi/` | 372 | Carriere |
| `/richiedi-preventivo/` | 2710 | Form di preventivo |
| `/guide-gratuite/` | 2706 | Hub delle guide PDF |

I 16 campi sono raggruppati in: **Hero · Aside trust box · Body editorial (TinyMCE con drop-cap automatico sul primo paragrafo) · CTA finale**. Pattern coerente cross-page.

### `/lo-studio/` (chi-siamo)

Pagina importante, **NON ha ACF Field Group**: il contenuto sta in `template-parts/page-chi-siamo.php` ed è disegnato in modo specifico dal team tecnico. I principi mostrati nella sezione "I nostri principi" sono moduli separati (§7 → Principi studio).

> **Per la chi-siamo, il copy editoriale principale è ancora hardcoded nel template.** Se serve modificare il body, scrivi al tecnico e specifica il paragrafo da cambiare. Stiamo valutando in Wave 4 se aprire anche questa via ACF.

---

## 5. Le 4 schede avvocato

📍 **Dove**: sidebar WP-Admin, voce **Avvocati**.

Quattro profili (Emiliano Saltelli, Fabiana Saltelli, Antonia Battista, Stefano Gaetano Tedesco). Ogni profilo è una scheda con i seguenti campi:

| Campo | Tipo | Note |
|---|---|---|
| **Hero · Ruolo** | Testo breve | Es. "Founding Partner · Tributarista". Mostrato sotto il nome nell'hero della scheda. |
| **Specializzazioni (max 5)** | Textarea, **una per riga** | Es.<br>`Diritto tributario`<br>`Cassazione tributaria`<br>`Cartelle e ricorsi NOV` |
| **Bio breve (1 riga lede)** | Testo, max 300 caratteri | Usata nell'archivio avvocati e nello schema description |
| **Bio estesa** | TinyMCE wysiwyg | Bio professionale completa, mostrata nella pagina del profilo |
| **Foto ritratto (3:4)** | Immagine | **Verticale**, minimo 600×800px, JPG ottimizzato. Vedi §11. |
| **Email pubblica** | Email | Lasciare vuoto se l'avvocato non vuole esporre l'email personale |
| **Telefono diretto** | Testo (formato E.164) | Es. `+390811813119` |
| **WhatsApp** | Testo (formato E.164) | Es. `+393517138006` — abilita il bottone WhatsApp |
| **LinkedIn URL** | URL | Profilo personale (alimenta `sameAs` nello schema JSON-LD) |
| **Aree di competenza correlate** | Selezione multipla | Le aree di pratica di cui questo avvocato si occupa. Si selezionano dal CPT Competenze (§6) |
| **Formazione & Titoli** | Selezione multipla | Crea le voci in **Avvocati → Formazione & Titoli** (§7), poi selezionale qui in ordine cronologico |
| **Casi rappresentativi** | Selezione multipla | Crea le voci in **Casi rappresentativi** (§7), poi selezionale qui |

### ⚠️ Tre cose da NON dimenticare per gli avvocati

1. **Ordine specializzazioni conta.** La prima riga è la specializzazione "primaria" (mostrata nell'hero più grande).
2. **Foto Emiliano Saltelli (`_thumbnail_id=2683`).** È stata configurata e validata. **Non sostituirla** senza autorizzazione di Avv. Saltelli.
3. **Bio estesa** delle altre 3 schede avvocato è work-in-progress dopo Wave 2. Se Elena le riscrive, **mantieni il tono editoriale** delle bio già pubblicate (cura, sobrietà, niente "siamo i migliori di Napoli").

---

## 6. Le 19 aree di pratica (Competenze)

📍 **Dove**: sidebar WP-Admin, voce **Aree di pratica**.

Sono **19 aree** divise in:

- **3 aree Tier-1 (deep cluster)**: Diritto tributario · Diritto del lavoro · Diritto di famiglia LGBTQ+
- **16 aree Tier-2 (lighter)**: tutte le altre

Le aree Tier-1 sono **deep**: 1500–2500 parole, FAQ, casi, avvocati referenti, articoli correlati. Le Tier-2 sono **lighter**: titolo + answer capsule + 100–300 parole + lista avvocati.

### Campi per area di pratica

| Campo | Tipo | Note |
|---|---|---|
| **Tier-1 deep cluster** | Toggle on/off | **Attiva SOLO per le 3 aree confermate Tier-1.** Cambia il rendering da pagina lighter a pagina deep |
| **Tier label** | Testo | Es. "Tier 1 · Approfondimento". Eyebrow nell'hero |
| **Sottotitolo H1** | Testo | Sottotitolo editorial sotto l'H1 (es. "Licenziamenti, mobbing, INPS.") |
| **Answer capsule (GEO)** | Textarea, 50–60 parole | Risposta diretta alla query target. **Letta direttamente dalle AI** (ChatGPT, Perplexity, Google AI Overviews). Vedi §9 per le regole di scrittura GEO |
| **Body editorial completo** | TinyMCE wysiwyg | Per Tier-1: 1500–2500 parole strutturate H2/H3. Per Tier-2: testo breve o vuoto |
| **Avvocati referenti** | Selezione multipla | Chi presidia l'area. Si visualizza in fondo alla pagina |
| **Casi rappresentativi** | Selezione multipla | Casi anonimizzati relativi all'area (§7) |
| **Domande frequenti** | Selezione multipla | 3–5 FAQ. Schema FAQPage iniettato automaticamente |
| **Articoli correlati** | Selezione multipla | Post del blog clusterizzati su questa area (rafforza topical authority) |
| **Testo CTA / URL CTA** | Testo / URL | Sovrascrive il CTA default solo se necessario |

### ⚠️ Answer capsule: NON modificare quelle esistenti senza coordinarsi

Le 3 answer capsule delle aree Tier-1 sono state **scritte e ottimizzate manualmente per le query AI**. Sono protette dal sistema di migrazione (Wave 2 le ha skippate per non sovrascriverle). Se devi cambiarle, **fallo direttamente da WP-Admin** e tieni la nuova versione **a 50–60 parole**, in stile Q&A diretto.

### Aggiungere un'area di pratica (raro)

Se Avv. Saltelli decide di aggiungere una 20a area:

1. **Aree di pratica → New**
2. Compila titolo + slug
3. Lascia il toggle Tier-1 **off** (sarà tier-2)
4. Compila answer capsule + body
5. Associa avvocati referenti
6. Pubblica
7. Avverti il tecnico per aggiornare la sitemap del menu (l'aggiunta automatica è in Wave 4)

---

## 7. I moduli riutilizzabili (FAQ, Casi, Principi…)

WordPress ti dà 8 voci nella sidebar che chiamiamo **moduli riutilizzabili**. Sono "elementi atomici" che vengono aggregati nelle pagine. Ti elenco ognuno con il suo uso reale.

### 7.1 — FAQ (28 voci attive)

📍 **Sidebar → FAQ**

Ogni FAQ ha:

| Campo | Note |
|---|---|
| **Titolo** | È la **domanda** (es. "Posso ricorrere contro una cartella già notificata?") |
| **Topic (taxonomy)** | Uno tra: `tributario` · `lavoro` · `lgbtq` · `costi` · `procedurale` · `prima-consulenza` |
| **Risposta** | TinyMCE wysiwyg. 50–150 parole. Tono diretto. |
| **Priorità ordinamento** | Numero (es. 10, 20, 30…). Le FAQ con numero più basso appaiono prima nella lista |

Le FAQ vengono mostrate:
- **Sulla pagina `/faq/`** → tutte e 28, raggruppate per topic
- **Sulle pagine area di pratica Tier-1** → le 3-5 selezionate manualmente nel campo "Domande frequenti" della scheda area

**Schema FAQPage JSON-LD** viene iniettato automaticamente sulle pagine che hanno FAQ associate. Niente da fare a mano.

### 7.2 — Casi rappresentativi (10 voci attive)

📍 **Sidebar → Casi rappresentativi**

Casi vinti **anonimizzati**. Ogni caso:

| Campo | Esempio |
|---|---|
| **Titolo** | "Tribunale Famiglia · 2024" (NO nomi clienti) |
| **Categoria (taxonomy)** | `privati` / `imprese` / `contenzioso` / `altri` |
| **ID label** | Es. "Caso T-04" o "TAR Campania · 2023" |
| **Descrizione anonimizzata** | Textarea, max 250 caratteri. Niente nomi, niente cifre identificative. |
| **Outcome label** | Es. "Vittoria · Risarcimento" o "TARI annullata · 4.500 €" |

⚠️ **Anonimato**. Mai inserire nome cliente, importo esatto identificativo, riferimento a procedimenti pendenti. In dubbio, chiedi a Avv. Saltelli prima di pubblicare.

### 7.3 — Modalità consulenza (3 voci)

📍 **Sidebar → Modalità consulenza** — Mostrate sulla pagina `/costi/` § 01.

Sono i 3 modi in cui lo Studio offre consulenza (es. "In presenza · Chiaia", "Videoconsulto", "Pareri scritti"). Ogni voce: numero, titolo, body, trust mini.

### 7.4 — Scenari costi (3 voci)

📍 **Sidebar → Scenari costi** — Mostrate sulla pagina `/costi/` § 02.

Sono i 3 scenari tipo (es. "Consulenza singola", "Pratica completa", "Assistenza continuativa"). Ogni voce: numero, titolo, body, trust mini.

### 7.5 — Principi studio (3 voci)

📍 **Sidebar → Principi studio** — Mostrate su `/lo-studio/` § 04.

Sono i valori dello studio. Ogni voce: numero, titolo, descrizione.

### 7.6 — Trust signals (4 voci)

📍 **Sidebar → Trust signals** — Mostrate sulla pagina `/costi/` § 05.

Numeri/credibilità (es. "Anni di esperienza", "Casi gestiti", "Settori coperti"). Ogni voce: label + valore.

### 7.7 — Formazione & Titoli (12 voci)

📍 **Sidebar → Avvocati → Formazione & Titoli**

Voci di formazione e titoli professionali degli avvocati. Ogni voce: anno, titolo/qualifica, ente/istituzione.

**Workflow**: prima crei la voce qui, poi la **selezioni nella scheda dell'avvocato** (§5, campo "Formazione & Titoli"). Le voci sono ordinate cronologicamente nella pagina avvocato.

### 7.8 — Guide gratuite (0 voci attualmente)

📍 **Sidebar → Guide gratuite**

Hub delle guide PDF. Pubblico (gli URL `/guide-gratuite/{slug}/` sono indicizzabili). Ogni voce:

| Campo | Note |
|---|---|
| **Titolo** | Titolo della guida (es. "Cartelle esattoriali: come ricorrere senza errori") |
| **Categoria (taxonomy)** | `tributario` / `lavoro` / `lgbtq` / `procedurale` |
| **Intro / Abstract** | Textarea, 80–120 parole. È quello che si vede in `/guide-gratuite/` come anteprima |
| **PDF file** | Caricamento file PDF (vedi §11) |
| **Formato** | Es. "PDF · 12 pagine · 2 MB" |
| **CTA download label** | Es. "Scarica la guida →" |

⚠️ **Le guide saranno create da Elena post-launch.** Nel form il caricamento PDF ha un limite di 8 MB per file (impostazione attuale). Se serve un PDF più pesante, ottimizzalo con Acrobat (Riduci dimensioni file).

---

## 8. Il blog

📍 **Sidebar → Articoli**

326 post storici migrati dal vecchio sito + nuovi articoli editoriali. Workflow standard WordPress:

| Cosa | Come |
|---|---|
| Nuovo articolo | **Articoli → Aggiungi nuovo** |
| Categoria | Tassonomia `categoria`. **Stiamo lavorando alla ricategorizzazione** sui 3 cluster Tier-1 (tributario · lavoro · LGBTQ+ famiglia). Per ora usa la categoria preesistente |
| Immagine in evidenza | **Obbligatoria** (16:9, min 1200×675). Vedi §11 |
| Excerpt (riassunto) | **Obbligatorio** — 150–200 caratteri. Appare nell'archivio blog e nei meta tag social |
| Drop cap | **Automatico** sul primo paragrafo. Non serve fare nulla. |
| Articoli correlati | Si associano nella scheda della **Competenza** (§6, campo "Articoli correlati") |

### Convenzioni post

- **Titolo**: 50–70 caratteri ideali per SEO. Niente titoli sensazionalistici.
- **H1 = titolo**, NON inserire un H1 nel body.
- **H2/H3 nel body**: usa headers strutturati per facilitare l'estrazione AI.
- **Lunghezza**: 800–2000 parole per pillar content; 400–800 per news/aggiornamenti.
- **Citazioni di leggi**: usa il formato standard "Art. 35 c.p.c." (con spazio).

---

## 9. Convenzioni di scrittura editoriale

Lo studio ha una **voce editoriale** precisa. Rispettarla è la differenza tra un sito legale qualsiasi e Saltelli.

### Tono

- **"Tu" diretto al lettore.** ("Hai ricevuto una cartella?" / "Ti aspettiamo per una prima consulenza".) Niente "Lei" formale.
- **Asciutto, non promozionale.** Scrivi "Lo Studio assiste imprese e professionisti nelle controversie tributarie" — NON "Siamo i migliori avvocati tributari di Napoli con anni di esperienza vincente!".
- **Concreto.** Esempi, scenari, casi. Niente generalità.
- **Misurato.** "Atelier legale italiano" è il nostro registro: cura, sobrietà, attenzione.

### Sigle e nomi propri

| Forma corretta | Forma sbagliata |
|---|---|
| INPS, IRPEF, IMU, RC, TARI, NOV | inps, Irpef, Imu, rc, tari, nov |
| LGBTQ+ | lgbtq+, Lgbtq+ |
| Napoli, Cassazione, Federico II | napoli, cassazione, federico ii |

### Punteggiatura editoriale

- Trattino lungo `—` per inserzioni (NON il singolo `-` né `–`)
- Separatore `·` (mediopunto) per sequenze brevi (es. "Diritto tributario · Cassazione · Cartelle")
- Virgolette curve `"..."` o `«...»` (NON `"..."` dritte)

### Eyebrow (riga sopra l'H1)

Format standard: `§ Topic · Subtopic`. Esempi:
- `§ Servizio · Costi`
- `§ Approfondimento · Tier 1`
- `§ FAQ · Diritto del lavoro`

### Answer capsule (GEO)

Per le **3 aree Tier-1**, le answer capsule sono il punto di estraibilità AI. Regole:

1. **50–60 parole** (limite stretto). Le AI tagliano oltre.
2. **Risposta diretta alla query target**, in prima persona dello Studio o terza neutra.
3. **Una sola idea** per capsule. NON liste, NON elenchi.
4. **Apri con l'attività concreta**, NON con introduzioni vaghe.

✅ **Buona**: "Lo Studio Legale Saltelli assiste a Napoli imprese e privati in controversie tributarie: ricorsi contro cartelle esattoriali, accertamenti, IRPEF, IMU, IVA. Operiamo davanti a Commissioni Tributarie Provinciali e Regionali, Cassazione tributaria. Prima consulenza conoscitiva gratuita."

❌ **Cattiva**: "Siamo lo Studio Legale Saltelli, leader a Napoli da oltre vent'anni. Offriamo una vasta gamma di servizi nel diritto tributario, lavoro, famiglia e tanto altro. Contattaci per saperne di più sui nostri eccellenti servizi!"

### Lede italic (sottotitolo serif)

15–35 parole, **complemento all'H1**, NON ripetizione. Stile editoriale: "Quando una cartella arriva e i tempi stringono, conta la mossa giusta — non la fretta."

---

## 10. Cosa NON toccare mai

Questo elenco è **assoluto**. Toccare un elemento qui dentro rompe il sito o le sue performance SEO/AI.

| Elemento | Perché |
|---|---|
| **Sidebar → Aspetto → Editor di temi** | Modifica file PHP del tema. Causa fatal error. |
| **Sidebar → Aspetto → Customizer (la voce "Identità del sito")** | Le impostazioni reali stanno in `Saltelli — Settings`. |
| **Sidebar → Plugin → Disattiva/Elimina qualsiasi plugin** | Rompe ACF, Yoast, Contact Form 7. Sito offline. |
| **Sidebar → Impostazioni → Generali / Lettura / Permalink** | Cambiano gli URL del sito o la struttura permalink. Disastro SEO. |
| **WP-Admin → Custom Fields** (Field Groups) | Sono il modello dati. Toccarli rompe tutte le pagine. |
| **Foto ritratto Avv. Emiliano Saltelli** (`_thumbnail_id=2683`) | Configurata e validata col cliente. Sostituire solo con autorizzazione di Avv. Saltelli. |
| **Codice CSS Custom (Aspetto → Personalizza → CSS aggiuntivo)** | I colori e la tipografia sono nel design system. Override = brand inconsistency. |
| **HTML inline nei body editorial** (a meno che tu non sappia esattamente cosa fai) | Può rompere il drop-cap automatico, il rendering mobile, lo schema JSON-LD. |
| **Yoast SEO → Sitemap XML toggle** | Il sito ha sitemap automatica + AI sitemap. Disattivare la rompe. |
| **Aggiornamenti automatici plugin** | Disattivati di proposito. Aggiornamenti manuali coordinati con il tecnico. |

### Se hai dubbi → ferma le mani

Se durante un edit vedi un'opzione che **non è descritta in questo manuale** e ti sembra "interessante", **NON cliccarla**. Apri ticket a `tech@adsolut.it` e chiedi.

---

## 11. Upload immagini e PDF

### Immagini

**Formati ammessi**: JPG (per foto), PNG (per loghi/grafica), WebP (consigliato per ottimizzazione), SVG (solo loghi/icone, **richiede approvazione tecnica**).

**Dimensioni minime**:

| Uso | Dimensioni | Aspect ratio |
|---|---|---|
| Foto avvocato | 600×800 (preferibile 900×1200) | 3:4 verticale |
| Featured image articolo blog | 1200×675 | 16:9 |
| Hero immagine pagina | 1600×900 | 16:9 |
| Immagine contenuto inline | 800×600 | 4:3 o 16:9 |

**Pre-upload checklist**:

1. ✅ **Comprimi** in JPG quality 80% (non originali RAW da fotografo). Tool: TinyJPG, Squoosh, ImageOptim.
2. ✅ **Rinomina il file** in modo descrittivo prima dell'upload: `emiliano-saltelli-ritratto.jpg`, NON `IMG_4421.jpg`.
3. ✅ **Aggiungi alt text** a ogni upload (sempre obbligatorio per accessibility + SEO/AI). Es. `Avv. Emiliano Saltelli, fondatore dello Studio`.
4. ❌ **Niente foto stock generiche** di "stretta di mano", "bilancia della giustizia", "mazzuolo del giudice". Tutto il mondo legale italiano usa quelle. Saltelli no.

### PDF (per le guide gratuite)

**Limite peso**: 8 MB per file.

**Pre-upload**:

1. ✅ **Comprimi** con Acrobat → File → Riduci dimensioni file → Standard. Target ≤ 5 MB.
2. ✅ **Rinomina** descrittivamente: `guida-cartelle-esattoriali-2026.pdf`.
3. ✅ **Verifica metadati**: nel PDF Properties → Title, Author, Subject. Title è il nome che le AI vedono. Author = "Studio Legale Saltelli".
4. ✅ **Apri il PDF dopo l'upload** dal link pubblico per verificare che si scarichi correttamente.

---

## 12. Anteprima, cache, troubleshooting

### Anteprima vs live

- **Anteprima** (bottone "Anteprima" in alto a destra durante l'edit): mostra come appare la pagina con le tue modifiche in corso, **anche se non hai ancora salvato/pubblicato**. Privata, non indicizzata.
- **Live**: per vederla, clicca **Aggiorna**, attendi 5–10 secondi, ricarica la pagina pubblica con `Cmd+Shift+R` (Mac) o `Ctrl+Shift+R` (Win).

### "Ho modificato ma non vedo il cambiamento"

In ordine, prova:

1. **Hard reload** (`Cmd+Shift+R` / `Ctrl+Shift+R`) sul browser.
2. **Apri in modalità incognito** — esclude cache browser.
3. **Aspetta 60 secondi** — la cache server scade automaticamente.
4. **Logout/Login** in WP-Admin — talvolta la sessione tiene una versione vecchia.
5. Se dopo 2 minuti ancora niente → ticket a `tech@adsolut.it` con: URL pagina, cosa hai cambiato, screenshot del browser.

### "Vedo errore PHP / pagina bianca / 'There has been a critical error on this website'"

🚨 **STOP.** Non riprovare a salvare. Apri ticket immediato a `tech@adsolut.it` con:

- URL della pagina che dà errore
- Cosa stavi facendo (ultima azione prima dell'errore)
- Orario esatto

Il tecnico ha **backup automatici** del sito, può ripristinare in 5 minuti.

### "Il bottone CTA non funziona / link rotto"

1. Vai sulla scheda del field e verifica che il campo URL sia compilato.
2. Verifica formato URL: `/contatti/` (con slash iniziale e finale) oppure `https://...` (URL completo per link esterni).
3. **Mai** mettere `contatti.html` o spazi nell'URL.

### "L'immagine appare distorta o sgranata"

- Controlla le dimensioni minime (§11).
- Caricala in alta risoluzione (almeno 2× la dimensione di rendering).
- Se persiste, mandala al tecnico — potrebbe servire ricreare le miniature WP.

---

## 13. Quando scrivere al team tecnico

📧 **Email**: tech@adsolut.it
🆘 **Per emergenze** (sito down, fatal error, contenuto pubblicato per sbaglio): scrivi anche un messaggio a Duccio Santoro su Slack/WhatsApp.

### Quando scrivere SEMPRE

- Sito offline o pagina bianca
- Errore PHP / "critical error"
- Hai pubblicato per sbaglio contenuto sensibile (nome cliente, importo, segreto professionale): **scrivi entro 5 minuti**, il team rimuove + cancella cache server + Google
- Vuoi cambiare la struttura di una pagina (aggiungere/rimuovere blocchi, NON contenuti)
- Vuoi creare un nuovo template di pagina
- Vuoi modificare il menu principale, footer, header
- Ti serve un nuovo Custom Post Type o un nuovo Field Group
- Aggiornamento WordPress / plugin disponibile
- Richiesta nuovo collaboratore esterno (creazione utente Editor)
- Domande sulla sitemap, schema JSON-LD, llms.txt, robots.txt, AI crawlers

### Cosa includere nell'email

Per velocizzare la risposta:

1. **URL della pagina** interessata
2. **Cosa stavi facendo** (azione concreta, non "non funziona")
3. **Cosa ti aspettavi**
4. **Cosa è successo invece**
5. **Screenshot** se rilevante
6. **Browser + sistema operativo** (es. "Chrome 124 su Mac")

---

## Appendice — Glossario

| Termine | Significato |
|---|---|
| **ACF** (Advanced Custom Fields) | Plugin WP che aggiunge i campi personalizzati che usi nelle pagine. Lo gestisce il tecnico, tu vedi solo i campi. |
| **CPT** (Custom Post Type) | "Tipo di contenuto" custom — Avvocati, Aree di pratica, FAQ, Casi sono tutti CPT. |
| **Field Group** | Gruppo di campi ACF associato a un CPT o pagina. Definisce cosa puoi editare. |
| **Tassonomia** | Sistema di categorizzazione (es. categorie del blog, topic delle FAQ). |
| **Schema JSON-LD** | Codice invisibile che dice alle AI cosa c'è nella pagina. Generato automaticamente. |
| **GEO** (Generative Engine Optimization) | Ottimizzazione del sito per essere citato dalle AI (ChatGPT, Perplexity, Google AI). Le answer capsule servono a questo. |
| **Tier-1 / Tier-2** | Tier-1 = aree di pratica con contenuto deep (1500+ parole, FAQ, casi, articoli). Tier-2 = aree con contenuto leggero (titolo + answer capsule + paragrafo). |
| **NAP** | Name · Address · Phone. I dati di contatto che devono essere coerenti ovunque sul sito. |
| **Drop cap** | La "lettera capolettera" grande all'inizio del primo paragrafo (stile editoriale). Generata automaticamente sui body editorial. |
| **Lede** | Sottotitolo italic sotto l'H1 nella sezione hero. Standard editoriale Saltelli. |

---

## Cambi a questo manuale

Quando il sito evolve (Wave 4: WOFF2, deploy production, Google Business Profile, ecc.), questo documento viene aggiornato. La versione corrente è sempre quella in `docs/EDITOR-HANDOFF.md` del repository GitHub Adsolut-Ai-Agency/saltelli-wp.

**Cronologia versioni**:

- **v1.0 — 2026-05-04** — Prima versione post Wave 3. Copre: 16 ACF Field Group, 9 pagine custom, 4 schede avvocato, 19 aree di pratica, 8 CPT modulari, blog standard.

---

*Manuale mantenuto da Adsolut SRLS · ultima revisione 2026-05-04 · contatto: tech@adsolut.it*
