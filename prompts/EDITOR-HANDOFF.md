# 📝 EDITOR HANDOFF — Studio Legale Saltelli WordPress

> **Audience**: Elena (Editor Adsolut), Ludovica (QA Adsolut), Avv. Saltelli + team (cliente)
> **Scope**: come gestire e modificare il sito `studiolegalesaltelli.it` via WordPress Admin
> **Theme version**: 1.3.x post-Wave 4
> **Aggiornato**: 2026-05-07
> **Autore**: Adsolut SRLS

---

## Indice

1. [Accesso WordPress Admin](#1-accesso-wordpress-admin)
2. [Panoramica struttura sito](#2-panoramica-struttura-sito)
3. [Gestione Aree di Pratica (Competenze)](#3-gestione-aree-di-pratica-competenze)
4. [Gestione Avvocati (Team)](#4-gestione-avvocati-team)
5. [Gestione Casi Rappresentativi (Risultati)](#5-gestione-casi-rappresentativi-risultati)
6. [Gestione Trust Signals & Testimonianze](#6-gestione-trust-signals--testimonianze)
7. [Gestione FAQ](#7-gestione-faq)
8. [Gestione Glossario Legale](#8-gestione-glossario-legale)
9. [Gestione Blog (Risorse)](#9-gestione-blog-risorse)
10. [Gestione Theme Options (Brand & Contatti)](#10-gestione-theme-options-brand--contatti)
11. [Gestione Form Contatti (Contact Form 7)](#11-gestione-form-contatti-contact-form-7)
12. [Gestione Menu di navigazione](#12-gestione-menu-di-navigazione)
13. [Gestione Utenti & Permessi](#13-gestione-utenti--permessi)
14. [Backup & Restore](#14-backup--restore)
15. [Best practice editoriali](#15-best-practice-editoriali)
16. [Troubleshooting comuni](#16-troubleshooting-comuni)
17. [Contatti supporto](#17-contatti-supporto)

---

## 1. Accesso WordPress Admin

### URL di accesso

- **Staging**: `https://staging.studiolegalesaltelli.it/wp-admin`
- **Produzione (post Wave 7)**: `https://studiolegalesaltelli.it/wp-admin`

### Credenziali iniziali

Verranno fornite separatamente da Adsolut via canale sicuro (1Password / email cifrata). Al primo accesso, **cambia immediatamente la password** in *Utenti → Profilo*.

### Requisiti browser

Browser moderni: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+. Scoraggiati: Internet Explorer (non supportato), browser molto datati.

### Two-Factor Authentication (raccomandato)

Adsolut consiglia di attivare 2FA per tutti gli utenti con ruolo Editor o superiore. Plugin pre-installato: *WP 2FA*. Vai su *Profilo → Two-Factor Auth* e segui setup con app Authenticator (Google Authenticator, Authy, 1Password).

---

## 2. Panoramica struttura sito

Il sito ha 5 sezioni primarie + 326 blog post legacy:

```
/
├── /chi-siamo/                       hub (page)
│   ├── /chi-siamo/lo-studio/         page (era "Chi Siamo")
│   ├── /chi-siamo/team/              archive Avvocati (CPT)
│   │   ├── /chi-siamo/team/emiliano-saltelli/
│   │   ├── /chi-siamo/team/fabiana-saltelli/
│   │   ├── /chi-siamo/team/antonia-battista/
│   │   └── /chi-siamo/team/stefano-gaetano-tedesco/
│   └── /chi-siamo/risultati/         archive Casi (CPT)
├── /aree-di-pratica/                 hub (page)
│   ├── /aree-di-pratica/privati/     14 competenze
│   ├── /aree-di-pratica/imprese/     2 competenze
│   └── /aree-di-pratica/contenzioso-amministrativo/  1 competenza
├── /risorse/                         hub (page)
│   ├── /risorse/blog/                326 blog post historical
│   ├── /risorse/domande-frequenti/   FAQ generali
│   ├── /risorse/glossario-legale/    Glossario
│   └── /risorse/guide-gratuite/
├── /costi-e-consulenze/              hub (page)
│   ├── /costi-e-consulenze/come-lavoriamo/
│   ├── /costi-e-consulenze/prima-consulenza/
│   └── /costi-e-consulenze/richiedi-preventivo/
└── /contatti/                        page (form completo + WhatsApp + telefono)
    ├── /contatti/lavora-con-noi/
    └── /contatti/prenota-appuntamento/  (opzionale)
```

### Custom Post Types (CPT) personalizzati

| CPT | Slug | Descrizione | Public |
|---|---|---|---|
| Competenza | `competenza` | Aree di pratica legale | ✅ |
| Avvocato | `avvocato` | Membri team studio | ✅ |
| Caso | `saltelli_caso` | Casi rappresentativi pubblici | ✅ |
| Trust | `saltelli_trust` | Testimonianze + statistiche | ✅ in template |
| FAQ | `saltelli_faq` | Domande frequenti | ✅ |
| Glossario | `saltelli_glossario` | Termini legali | ✅ |

### Tassonomie custom

- **`tipo-area`** → su `competenza`. Termini: `privati` (14), `imprese` (2), `contenzioso-amministrativo` (1)

---

## 3. Gestione Aree di Pratica (Competenze)

### Le 17 aree finali

Cliente-firmate (DEC-021), distribuite su 3 cluster:

**Privati (14)**: Diritto tributario · Cartelle esattoriali e multe · Diritto del lavoro · Diritto di famiglia LGBTQ+ · Responsabilità medica · Diritto bancario · Diritto condominiale · Diritto dell'immigrazione · Diritto penale · Diritto previdenziale · Eredità e successioni · Risarcimento danni · Infortunistica stradale · Aste immobiliari

**Imprese (2)**: Recupero crediti · Domiciliazione d'impresa

**Contenzioso amministrativo (1)**: Diritto amministrativo

### Modificare una competenza esistente

1. *WP Admin → Competenze* (sidebar) → click sulla competenza desiderata
2. La pagina edit ha **3 sezioni principali**:
   - **Editor classic**: titolo + corpo testo (descrizione lunga competenza)
   - **Excerpt** (lede): 1-2 frasi sintetiche, usato come `<meta description>` SEO + lede italic visibile
   - **ACF Field Groups**: tutti i campi custom (vedi sotto)

### Field ACF importanti per competenza

| Field | Tipo | Obbligatorio? | Note |
|---|---|---|---|
| `tipo_area` (taxonomy) | radio | Sì | Privati / Imprese / Contenzioso amministrativo |
| `answer_capsule` | textarea | Raccomandato | 40-60 parole risposta diretta — pattern GEO critico |
| `tier` | radio | Sì | tier1-deep (3 competenze top) o tier2 (14 altre) |
| `body_completo` | wysiwyg | Sì | Contenuto principale (dopo answer_capsule) |
| `cta_top_label` + `cta_top_url` | text | Opzionale | CTA ghost in alto pagina (default usa Theme Options) |
| `cta_middle_label` + `cta_middle_url` | text | Opzionale | CTA filled in mezzo pagina |
| `faq` | post_object multiple | Raccomandato | Linkare 3-7 FAQ associate (sblocca FAQPage schema) |
| `related_competenze` | post_object multiple | Opzionale | Se vuoto, auto-fallback a 3 random stesso cluster |
| `correlazioni` | repeater | Opzionale | Link a casi rappresentativi correlati |

### Creare una NUOVA competenza

⚠️ **Attenzione**: la lista delle 17 aree è cliente-firmata (DEC-021). Aggiungere una 18ª richiede:
1. Approvazione cliente esplicita
2. Verifica che lo slug non collida con redirect esistenti
3. Aggiornamento `cluster-mapping-17-areas.csv` deliverable Adsolut

Procedura:
1. *WP Admin → Competenze → Aggiungi nuova*
2. Titolo + slug auto-generato (modificabile in *URL slug* sidebar)
3. Compila Excerpt (lede italic) + body_completo
4. Compila answer_capsule (40-60 parole)
5. Seleziona tipo_area (cluster)
6. Seleziona tier (tier1-deep solo se cliente conferma asset principale)
7. Linka 3-7 FAQ (campo `faq`)
8. Pubblica

### URL pattern automatico

Il theme genera automaticamente `/aree-di-pratica/{cluster}/{slug}/`. NON modificare manualmente lo slug per cambiarlo a `/per-i-privati/...` o simili — rompe i redirect 301 chain.

---

## 4. Gestione Avvocati (Team)

### I 4 avvocati

| Slug | Nome | Ruolo principale |
|---|---|---|
| `emiliano-saltelli` | Avv. Emiliano Saltelli | Founder, tributarista (Tier-1) |
| `fabiana-saltelli` | Avv. Fabiana Saltelli | Co-founder, giuslavorista (Tier-1) |
| `antonia-battista` | Avv. Antonia Battista | Asset principale, diritto famiglia LGBTQ+ (Tier-1) |
| `stefano-gaetano-tedesco` | Avv. Stefano Gaetano Tedesco | Condominiale, immobiliare |

### Field ACF importanti per avvocato

| Field | Tipo | Obbligatorio? | Note |
|---|---|---|---|
| `bio_breve` | textarea | Sì | 2-3 frasi sintetiche, mostrato in archive `/chi-siamo/team/` |
| `bio_estesa` | wysiwyg | **Sì** ⚠️ | Bio completa (mostrata in single page). Asset principale Antonia ha bio estesa, gli altri 3 sono **DEBT EDITORIALE** ancora da completare |
| `foto_profilo` | image | Sì | Min 800×1000px, formato verticale, JPG quality 85+ |
| `albo_iscrizione` | text | Sì | Es. "COA Napoli, n. iscrizione XXXX" |
| `aree_competenza_correlate` | post_object (CPT competenza) | Sì | Linkare 3-5 competenze trattate dall'avvocato |
| `byline_extended` | textarea | Opzionale Wave 6 | Per byline ricca su blog post (single.php) |
| `expertise_topics` | text repeater | Opzionale Wave 6 | Tag esperienza per byline |
| `cv_pdf` | file | Opzionale | CV scaricabile |
| `email_contatto` | email | Opzionale | Email diretta avvocato (se differente da contatti studio) |
| `telefono_diretto` | text | Opzionale | Tel diretto (se differente da centralino) |
| `linkedin_url` | url | Opzionale | Profilo LinkedIn |

### ⚠️ DEBT EDITORIALE — bio_estesa mancanti

3 avvocati hanno `bio_estesa` ancora da completare:
- Avv. Emiliano Saltelli
- Avv. Fabiana Saltelli
- Avv. Stefano Gaetano Tedesco

**Riferimento di stile**: la bio_estesa di Avv. Antonia Battista è il template approvato. Lunghezza ~300-500 parole, prima persona, tone editoriale italiano, evidenzia esperienza concreta + valori, no jargon legalese, brand voice "Studio boutique Napoli".

### Modificare un avvocato

1. *WP Admin → Avvocati* → click sull'avvocato
2. Aggiorna foto_profilo, bio_breve, bio_estesa
3. Verifica `aree_competenza_correlate` aggiornato (linka le competenze realmente trattate)
4. Aggiorna `albo_iscrizione` se cambia n. iscrizione
5. Pubblica

---

## 5. Gestione Casi Rappresentativi (Risultati)

### Stato attuale

10 casi pubblicati su `/chi-siamo/risultati/` post-Wave 5. Privacy compliance dichiarata cliente per ognuno (B5.4 cristallizzato in DEC-021).

### Field ACF importanti per caso

| Field | Tipo | Obbligatorio? | Note |
|---|---|---|---|
| `data_caso` | date | Sì | Data conclusione (AAAA-MM) |
| `competenza_associata` | post_object (CPT competenza) | Sì | Quale area ha gestito il caso |
| `avvocato_responsabile` | post_object (CPT avvocato) | Sì | Chi ha seguito il caso |
| `descrizione_breve` | textarea | Sì | 2-3 frasi sintetiche per archive |
| `descrizione_completa` | wysiwyg | Sì | Contenuto pagina single |
| `risultato` | text | Sì | Outcome (es. "€87.000 risarcimento ottenuto") |
| `tribunale` | text | Opzionale | Tribunale competente |
| `durata_procedimento` | text | Opzionale | Es. "8 mesi" |
| `privacy_disclaimer` | textarea | Sì | Disclaimer GDPR + consenso cliente |

### ⚠️ Privacy & GDPR

I casi pubblici DEVONO:
- NON contenere nomi cliente (anonimizzati: "il sig. M.R.", "la cliente A.B.")
- NON contenere dettagli identificativi (residenza esatta, professione specifica se rara, ecc.)
- AVERE consenso scritto cliente per pubblicazione
- INCLUDERE disclaimer privacy in fondo alla pagina single

### Aggiungere un nuovo caso

1. *WP Admin → Casi → Aggiungi nuovo*
2. Verifica consenso cliente per pubblicazione (non procedere senza)
3. Anonimizza ogni dato identificativo
4. Compila tutti i campi obbligatori
5. Aggiungi `privacy_disclaimer` standard (template fornito da Adsolut)
6. Linka `competenza_associata` + `avvocato_responsabile`
7. Pubblica

---

## 6. Gestione Trust Signals & Testimonianze

### Cosa sono

Il CPT `saltelli_trust` ospita 2 tipi di "trust signal":
1. **Statistiche numeriche** (es. "20+ anni di esperienza", "500+ casi vinti")
2. **Testimonianze cliente** (es. "Lo Studio Saltelli mi ha guidato con competenza...")

Wave 6 ha esteso il CPT con 8 nuovi field per supportare entrambi i pattern.

### Field ACF importanti per trust signal

| Field | Tipo | Obbligatorio? | Note |
|---|---|---|---|
| `testimonial_type` | radio | Sì | "statistic" o "testimonianza" |
| `numero` | text | Solo se statistic | Es. "20+", "500+", "100%" |
| `label` | text | Opzionale | Header del numero (es. "Anni di esperienza") |
| `valore` | text | Opzionale | Caption sotto numero (es. "dal 2004 al fianco dei clienti") |
| `testimonial_text` | textarea | Solo se testimonianza | Quote testimonianza (max 200 caratteri raccomandato) |
| `testimonial_author` | text | Solo se testimonianza | Nome anonimizzato (es. "M.R., Napoli") |
| `testimonial_role` | text | Opzionale | Ruolo/contesto (es. "Cliente diritto del lavoro") |
| `testimonial_rating` | number 1-5 | Opzionale | Per future Review schema markup |
| `source_label` + `source_text` + `source_url` | text/url | Opzionale | Fonte verificabile (Pattern 7 statistic-with-source) |

### ⚠️ DEBT EDITORIALE — Testimonianze

**Stato attuale**: zero testimonianze popolate. Il block testimonials in homepage NON renderizza finché non vengono aggiunti almeno 3 trust di tipo `testimonianza`.

### Aggiungere una nuova testimonianza

1. **Verifica consenso scritto cliente** per pubblicazione testimonianza con foto/nome anonimizzato
2. *WP Admin → Trust Signals → Aggiungi nuovo*
3. `testimonial_type` = `testimonianza`
4. `testimonial_text` = quote del cliente (max 200 char raccomandati per leggibilità mobile)
5. `testimonial_author` = nome ANONIMIZZATO (mai nome completo, GDPR)
6. `testimonial_role` = contesto (es. "Cliente diritto del lavoro, sede Napoli")
7. (Opzionale) `testimonial_rating` da 1 a 5 stelle
8. Pubblica

### Aggiornare i 4 trust signal numerici (Theme Options)

I 4 plate del trust-bar in homepage si gestiscono da *Saltelli Settings → Brand* (vedi sezione 10). NON dal CPT `saltelli_trust`.

---

## 7. Gestione FAQ

### Stato attuale

Wave 6 ha sbloccato un bug architettonico: prima del Wave 6, le FAQ associate alle competenze NON erano renderizzate né schema-emesse. Ora **tutte le 17 competenze** possono avere FAQ con schema FAQPage corretto.

### Field ACF importanti per FAQ

| Field | Tipo | Obbligatorio? | Note |
|---|---|---|---|
| `domanda` | text | Sì | Pattern: "Come fare X?" / "Cosa succede se Y?" |
| `risposta` | wysiwyg | Sì | 50-150 parole raccomandato per GEO |
| `competenza_associata` | post_object (CPT competenza) | Opzionale | Se compilato, FAQ appare in single competenza + emette schema |
| `categoria` | taxonomy | Opzionale | Per gruppi (es. "tributario", "famiglia") |

### Aggiungere una nuova FAQ

1. *WP Admin → FAQ → Aggiungi nuova*
2. **Domanda**: formula come la cercherebbe un utente (long-tail SEO + GEO)
3. **Risposta**: 50-150 parole, risposta DIRETTA + esempio + eventualmente link interno
4. Linka `competenza_associata` per attivare schema FAQPage automatico
5. Pubblica

### Linkare FAQ a una competenza

Dopo aver creato la FAQ:
1. *WP Admin → Competenze* → apri la competenza target
2. Scorri al Field Group con campo `faq` (post_object multiple)
3. Aggiungi la FAQ creata + altre correlate (3-7 raccomandato)
4. Aggiorna

**Ora la single competenza renderizza il blocco FAQ + emette schema `FAQPage` JSON-LD automaticamente** (sblocca GEO + Google Rich Results).

---

## 8. Gestione Glossario Legale

### Stato attuale

Pagina archive `/risorse/glossario-legale/` con CPT `saltelli_glossario`.

### Field ACF importanti per glossario

| Field | Tipo | Obbligatorio? |
|---|---|---|
| `termine` | text | Sì (= post_title) |
| `definizione` | wysiwyg | Sì |
| `lettera_iniziale` | text (1 char) | Sì (per archivio alfabetico) |
| `competenza_associata` | post_object (CPT competenza) | Opzionale |

### Aggiungere un nuovo termine

1. *WP Admin → Glossario → Aggiungi nuovo*
2. Titolo = il termine (es. "Cassazione")
3. Compila `definizione` (200-500 parole)
4. Linka `competenza_associata` se pertinente
5. Pubblica

---

## 9. Gestione Blog (Risorse)

### Pattern URL

`/risorse/blog/{slug}/` (post-Wave 5). Tutti i 326 blog post historical sono ancora accessibili con redirect 301 da `/blog/{slug}/`.

### Aggiungere un nuovo blog post

1. *WP Admin → Articoli → Aggiungi nuovo*
2. Titolo + Excerpt (lede italic) + Editor body
3. Imposta **Featured Image** (min 1200×675px, JPG quality 85)
4. Categoria + Tag (importante per archive filter)
5. Imposta Author (l'avvocato che ha scritto)
6. Pubblica

### Best practice editoriale per nuovi blog post

**Pattern Answer Capsule (GEO critico)**:
1. **Header H2 che rispecchia la query utente** (es. "Come funziona il licenziamento per giusta causa?")
2. **Answer capsule** (primo paragrafo, 40-60 parole): risposta diretta + entità chiave + dato rilevante
3. **Evidenza di supporto**: statistiche, citazioni leggi, fonti autorevoli
4. **Link interno** alla competenza correlata (es. `/aree-di-pratica/privati/diritto-del-lavoro/`)
5. **Anchor ID**: usa `#answer`, `#steps`, `#faq` per citazioni a span esatti

**Lunghezza raccomandata**: 1200-2500 parole. Sotto 800 parole = thin content (bad for SEO/GEO). Sopra 3000 = considerare split in serie articoli.

**Tone of voice**: italiano editoriale, no jargon legalese, esempi concreti, mai patronizing. Brand voice "Studio boutique Napoli".

---

## 10. Gestione Theme Options (Brand & Contatti)

### Accesso

*WP Admin → Saltelli Settings* (sidebar dedicata, plugin ACF).

### Tab principali

#### Tab "Brand"
- **Trust signals** (4 plate del trust-bar in homepage):
  - Plate 1: numero + label + caption (default: "20+ / ANNI / dal 2004 al fianco dei clienti")
  - Plate 2: (default: "4 / AVVOCATI / specialisti dedicati")
  - Plate 3: (default: "17 / AREE / di competenza")
  - Plate 4: (default: "COA / FAMIGLIA / Antonia Battista referente")

⚠️ **Importante**: i fallback editoriali hardcoded sono attivi finché NON salvi questa pagina la prima volta. Una volta salvata, ACF prende il sopravvento. Verifica che i numeri reali siano accurati prima di salvare.

#### Tab "Contatti"
- Telefono studio: `+39 081 1813 1119`
- WhatsApp: `+39 351 713 8006`
- Email principale: TBD da cliente
- PEC: `emilianosaltelli@avvocatinapoli.legalmail.it`
- P.IVA: `06685101211`
- Indirizzo studio: Via Vannella Gaetani 27, Chiaia, 80121 Napoli (NA)
- Coordinate Google Maps: lat/lng (per schema Organization)

#### Tab "CTA Default"
- CTA primary label/url (per quando le competenze non sovrascrivono)
- CTA ghost label/url

#### Tab "Footer"
- Copyright text
- Social URLs (LinkedIn, Facebook, Instagram se presenti)
- Crediti (Adsolut)

### Modificare Theme Options

1. *Saltelli Settings → Brand* (o tab desiderato)
2. Modifica i campi
3. **Salva modifiche** (pulsante in alto a destra)

---

## 11. Gestione Form Contatti (Contact Form 7)

### Form principali (esistenti)

- **Form contatti** (`/contatti/`): completo con subject, message, GDPR consent
- **Form preventivo** (`/costi-e-consulenze/richiedi-preventivo/`): con dropdown topics

### ⚠️ Form da CREARE — `saltelli-mini`

Wave 6 ha aggiunto un mini-form contestuale che attualmente usa un fallback CTA. Per attivarlo come form vero, va creato il form CF7 con slug `saltelli-mini`:

#### Procedura

1. *WP Admin → Contact Forms → Aggiungi nuovo*
2. **Titolo**: "Saltelli Mini Form"
3. **Slug** (sidebar URL): `saltelli-mini` (deve essere esattamente questo per attivazione)
4. **Form template**:

```
<label> Il tuo nome (obbligatorio)
    [text* your-name placeholder "Mario Rossi"] </label>

<label> La tua email (obbligatoria)
    [email* your-email placeholder "mario@email.it"] </label>

<label> Il tuo telefono
    [tel your-phone placeholder "+39 333 1234567"] </label>

<label> Argomento (obbligatorio)
    [select* topic include_blank
        "Diritto tributario"
        "Cartelle esattoriali e multe"
        "Diritto del lavoro"
        "Diritto di famiglia LGBTQ+"
        "Responsabilità medica"
        "Diritto bancario"
        "Diritto condominiale"
        "Diritto dell'immigrazione"
        "Diritto penale"
        "Diritto previdenziale"
        "Eredità e successioni"
        "Risarcimento danni"
        "Infortunistica stradale"
        "Aste immobiliari"
        "Recupero crediti"
        "Domiciliazione d'impresa"
        "Diritto amministrativo"] </label>

<label> Messaggio breve (max 500 caratteri)
    [textarea your-message minlength:10 maxlength:500 placeholder "Descrivi brevemente la tua situazione..."] </label>

[acceptance gdpr-consent]
    Ho letto e accetto la <a href="/privacy-policy/" target="_blank">Privacy Policy</a> e autorizzo il trattamento dei miei dati personali per essere ricontattato.
[/acceptance]

[submit "Invia richiesta"]
```

5. **Tab "Mail"**: configura destinatario (Avv. Emiliano + email assistente studio)
6. **Tab "Messages"**: personalizza messaggi (success, validation errors)
7. **Salva**
8. Verifica frontend: `/aree-di-pratica/privati/{slug}/` ora renderizza il mini-form invece del fallback CTA

### Best practice CF7

- **Honeypot 2.3** (plugin pre-installato): aggiungere `[honeypot honeypot-field]` a ogni form
- **Test invio** sempre dopo modifica form
- **GDPR**: il checkbox `[acceptance]` con link Privacy Policy è obbligatorio (UE 2016/679)

---

## 12. Gestione Menu di navigazione

### Menu primary (header)

*WP Admin → Aspetto → Menu*. Selezionare "Menu Primary".

Voci attese (post-Wave 5):
- Chi Siamo (link a `/chi-siamo/`)
- Aree di Pratica (link a `/aree-di-pratica/`)
- Risorse (link a `/risorse/`)
- Costi e Consulenze (link a `/costi-e-consulenze/`)
- Contatti (link a `/contatti/`)

### Menu footer

Footer ha 3 colonne menu (gestite via Theme Options) + 1 colonna social.

### Aggiungere/modificare voce menu

1. Aspetto → Menu → seleziona menu
2. Drag & drop voci, modifica label/URL
3. **Salva menu**

---

## 13. Gestione Utenti & Permessi

### Ruoli WordPress

| Ruolo | Capabilities | Use case Saltelli |
|---|---|---|
| Administrator | Tutti | Solo Adsolut + Avv. Saltelli (1-2 utenti) |
| Editor | Pubblica/modifica tutto, gestisce categorie/tag | Elena (Adsolut) + Ludovica (QA) |
| Author | Pubblica solo propri post | Per i 4 avvocati (autori blog) |
| Contributor | Scrive ma NON pubblica | Per redattori junior |
| Subscriber | Solo legge | (non usato attualmente) |

### Aggiungere un nuovo utente

1. *Utenti → Aggiungi nuovo*
2. Username + email + first name + last name
3. **Genera password** robusta (≥ 16 caratteri)
4. Selezione ruolo (Author per avvocati, Editor per Adsolut)
5. Invia notifica via email
6. **Forzare 2FA** post primo login

### Bonificare utenti inattivi

Periodicamente (ogni 6 mesi):
1. Lista utenti, ordina per "ultimo accesso"
2. Disattiva (cambia ruolo a Subscriber) utenti senza login da 12+ mesi
3. Elimina utenti senza login da 24+ mesi (dopo verifica con cliente)

---

## 14. Backup & Restore

### Backup automatico (Wave 4 + Wave 7)

Strategia (post Wave 7 deploy):
- **Daily**: snapshot automatico DigitalOcean droplet (incluso DB + filesystem)
- **Weekly**: dump DB completo via cron WP-CLI → S3 / Dropbox
- **Pre-update**: backup manuale prima di ogni update tema/plugin/core WP

### Backup manuale (Editor può eseguire)

Plugin: *UpdraftPlus* (pre-installato). Vai su *Settings → UpdraftPlus Backups → Backup ora*. Scegli:
- Database: ✅
- Plugin: ✅
- Themes: ✅
- Uploads: ✅ (se non troppo grande)

### Restore in caso di emergenza

⚠️ **NON tentare restore senza supporto Adsolut**. Contatta `aldo.santoro@adsolut.it` o canale supporto.

---

## 15. Best practice editoriali

### Brand voice Saltelli

- **Tone**: editoriale italiano, professionale ma umano, MAI legalese
- **Person**: prima persona plurale ("Noi") oppure terza neutra ("Lo Studio")
- **Style**: drop-cap iniziale + lede italic + body sans-serif
- **Lunghezza paragrafi**: 50-100 parole, brevi
- **Headers**: H2 per sezioni principali, H3 per sotto-sezioni, MAI H1 nel body (è il titolo pagina)
- **CTA copy**: orientato al beneficio (NO "Invia" / "Click qui"), SÌ "Richiedi consulenza dedicata" / "Parla con un avvocato"

### SEO best practice

- **Meta description**: usare Excerpt (150-160 caratteri max)
- **Slug URL**: brevi, lowercase, kebab-case (es. `cartelle-esattoriali-e-multe` ✅)
- **Internal linking**: ogni pagina dovrebbe linkare 3-5 altre pagine correlate (competenze, FAQ, casi)
- **Featured image**: sempre presente, con `alt` descrittivo (NON "image1.jpg")

### GEO best practice (per AI search engines)

- **Answer capsule** (40-60 parole) sotto ogni H2 strategico
- **Entity-first content**: menziona entità (Studio Saltelli, avvocati, leggi) nei primi 100 parole
- **Citation-friendly anchors**: `<h3 id="risposta">`, `<section id="steps">`
- **Source citation**: linka SEMPRE fonti esterne autorevoli (Cassazione, leggi gazzetta ufficiale, articoli accademici)

### Accessibility

- **Alt text immagini**: SEMPRE compilato, descrittivo (NO "decorative" salvo davvero decorative)
- **Heading hierarchy**: H1 → H2 → H3 sequenziale, no salti (NON H1 → H4)
- **Color contrast**: rispettato dal design system (NON forzare colori in editor)
- **Links**: descrittivi (NO "clicca qui", SÌ "leggi la guida completa al diritto del lavoro")

---

## 16. Troubleshooting comuni

### "Non vedo il blocco FAQ in single competenza"

**Causa**: il field `faq` su quella competenza è vuoto o mal compilato.
**Fix**: vai su *Competenze → la tua competenza → ACF Field Group → faq* (post_object multiple) e linka 3-7 FAQ.

### "Trust bar mostra ancora i fallback hardcoded invece dei numeri reali"

**Causa**: la pagina *Saltelli Settings → Brand* non è mai stata salvata. ACF Theme Options funziona solo dopo primo save.
**Fix**: vai su Saltelli Settings → Brand → modifica/conferma i 4 trust signal → **Salva modifiche**.

### "Mini-form mostra fallback CTA invece del form vero"

**Causa**: il CF7 form `saltelli-mini` non è stato creato.
**Fix**: vedi sezione 11 "Form da CREARE — saltelli-mini".

### "Schema FAQPage non viene emesso (Google Rich Results test fallisce)"

**Causa**: probabilmente il field `faq` su competenza è vuoto, OPPURE le FAQ linkate hanno `domanda` o `risposta` vuoti.
**Fix**: verifica che ogni FAQ linkata abbia entrambi i campi compilati. Test con [Google Rich Results Test](https://search.google.com/test/rich-results).

### "Pagina dà 404 dopo modifica slug"

**Causa**: cache rewrite rules WP non aggiornata.
**Fix**: vai su *Impostazioni → Permalink* → click "Salva modifiche" (anche senza cambiare nulla, forza flush). Se persiste, contatta Adsolut.

### "Form contatti non invia email"

**Causa**: SMTP non configurato (Brevo credentials Wave 7).
**Fix**: post Wave 7. Per ora usa contact form solo come UI test, non in produzione.

### "Lighthouse score è basso"

**Performance**:  questo è normale in Docker locale (simulated 4G). Production reale (Cloudflare + brotli + edge cache) avrà score più alti.
**Accessibility**: se < 95, riferire a Adsolut (audit dedicato disponibile).
**SEO**: se < 100, verificare meta description + Open Graph tags.

---

## 17. Contatti supporto

### Adsolut support

- **Project Lead**: Aldo "Duccio" Santoro
- **Email**: `aldo.santoro@adsolut.it`
- **Editor / Content**: Elena (contattabile via Duccio)
- **QA**: Ludovica (contattabile via Duccio)
- **Tempo risposta**: business hours 9-18, lun-ven (eccezioni emergency)

### Documentazione tecnica

- Repo codice: `https://github.com/adsolut/saltelli-wp` (private, accesso su richiesta)
- Repo deliverable: `saltelli-refactor` (interno Adsolut)
- Decision Log: 36+ decisioni cristallizzate (DEC-001 → DEC-026-COMPLETED)

### Emergenze produzione

Post Wave 7 cut produzione, in caso di sito down:
1. Verifica se è hosting issue (DigitalOcean status / Cloudflare)
2. Ping Adsolut immediato
3. Restore da backup ultima recente (vedi sezione 14)

---

*Documento aggiornato post-Wave 4 (2026-05-07).*
*Per modifiche o aggiunte: ping Adsolut.*
