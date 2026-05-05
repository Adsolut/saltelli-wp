# WYSIWYG Diagnosis — Test Plan per Elena

> **Scope:** diagnosi user-research del modello CMS attuale del sito Saltelli, eseguita con Elena Cappabianca come tester reale.
> **Obiettivo:** identificare i gap tra "modello mentale dell'editor" e "modello dati che abbiamo costruito", per poi pianificare un Wave 5 di realignment.
> **Status:** Fase 1 (Test Plan creato) · 2026-05-05

---

## Cosa c'è in questa cartella

| File | Cosa è |
|---|---|
| `test-plan-template.xlsx` | Template spreadsheet (4 fogli) per Elena. **Da caricare su Google Drive** e condividere con Elena. |
| `build_test_plan.py` | Generatore Python del file `.xlsx`. Eseguire per rigenerare se servono modifiche. |
| `elena-instructions.md` | Email/messaggio pronto da inviare a Elena con il link al Google Sheets. |
| `findings.md` | (verrà creato in Fase 3) Aggregazione del feedback di Elena + categorizzazione per severity. |
| `README.md` | Sei qui. |

## Le 4 fasi della diagnosi

```
FASE 1 — Test Plan creato                               ← stato attuale
            ↓
FASE 2 — Elena esegue i 10 scenari (~2-3 ore)
            ↓
FASE 3 — Aggregazione findings + analisi gap
            ↓
FASE 4 — Prompt Wave 5 WYSIWYG-realignment
```

## Come usare il template

### Step 1 — Carica il file su Google Drive

1. Apri [Google Drive](https://drive.google.com/) (account Adsolut)
2. Crea cartella `Saltelli/QA/WYSIWYG-Diagnosis-2026-05/`
3. Carica `test-plan-template.xlsx`
4. Click destro sul file → **Apri con → Fogli Google**
5. Google Sheets crea automaticamente una copia in formato `.gsheet`. Tieni questo, cancella l'`.xlsx` originale dal Drive (per evitare confusione)

### Step 2 — Condividi con Elena

1. Sul Google Sheet → **Condividi** (in alto a destra)
2. Aggiungi email di Elena come **Editor**
3. Copia il link condivisibile
4. Manda email a Elena (template in `elena-instructions.md`)

### Step 3 — Aspetta che Elena compili

Tempo realistico: 1-3 giorni dopo il primo annuncio (Elena avrà altre priorità).

### Step 4 — Aggregazione (Fase 3)

Quando Elena ha compilato:
1. Esporta il Google Sheets come `.xlsx` o `.csv`
2. Mandami il link in chat
3. Faccio analisi → genero `findings.md` in questa cartella
4. Discutiamo i fix in chat → genero il prompt Wave 5

## Struttura del template

### Foglio 1: "Inizia da qui"
Istruzioni per Elena: perché stai facendo questo, regole del test, step pre-test, contatto per dubbi.

### Foglio 2: "Test Plan" — il cuore
10 righe (una per scenario), 10 colonne:

| Colonna | Pre-compilata da te? | Compilata da Elena? |
|---|---|---|
| # | ✅ | — |
| Cosa devi fare | ✅ (descrizione concreta) | — |
| Tempo target (min) | ✅ | — |
| Tempo impiegato (min) | — | ✅ |
| Frustrazione (1-5) | — | ✅ (dropdown) |
| Riuscita? | — | ✅ (Sì / Sì con difficoltà / No / Non provata — dropdown) |
| Cosa cercavi e non trovavi | — | ✅ |
| Cosa ti aspettavi | — | ✅ |
| Cosa hai fatto alla fine | — | ✅ |
| Note libere | — | ✅ |

In fondo: riga totali con somma `Tempo target` (=50 min) e `Tempo impiegato` (formula auto, evidenziato giallo).

### Foglio 3: "Open feedback"
6 domande aperte sul rapporto generale di Elena con il sito + WordPress in generale. Più "etnografiche", meno task-oriented.

### Foglio 4: "Riferimenti rapidi"
URL WP-Admin staging, link manuale, contatti. **Niente credenziali nel foglio** — quelle vanno mandate separatamente.

## I 10 scenari (sintesi per orchestratore)

| # | Scenario | Gap testato (NON visibile a Elena) | Tempo target |
|---|---|---|---|
| 1 | Cambia numero telefono | Theme Options + propagazione cross-page | 2 min |
| 2 | Aggiungi FAQ | CPT modulare + topic + visualizzazione | 5 min |
| 3 | Modifica brand statement /lo-studio/ | **Gap 2** — `/lo-studio/` body hardcoded | 5 min |
| 4 | Aggiorna bio Emiliano | **Gap 3** — bio_estesa post_content vs ACF | 5 min |
| 5 | Modifica "Modalità di consulenza" | **Gap 1** — CPT in pagina singola | 3 min |
| 6 | Aggiungi caso vinto | CPT casi + anonimizzazione | 8 min |
| 7 | Modifica eyebrow /faq/ | Page editing standard (sanity check) | 3 min |
| 8 | Aggiungi 20a competenza | Estensione modello dati | 10 min |
| 9 | Cambia payoff logo | **Gap 4** — sidebar Theme Options | 2 min |
| 10 | Carica guida PDF | CPT guida + workflow upload | 8 min |

**Tempo totale target:** ~50 min ideali. Range realistico: 60-100 min.

## Cosa NON fare (per non contaminare il test)

- ❌ NON dare a Elena il manuale `EDITOR-HANDOFF.md` PRIMA del test (lo può consultare DURANTE se si blocca, ma non a tavolino)
- ❌ NON mandare a Elena la lista "Gap testato" della tabella sopra — diventerebbe una caccia al tesoro guidata
- ❌ NON guidarla in tempo reale durante l'esecuzione (a meno che non sia un blocker assoluto, tipo non riesce a entrare in WP-Admin)
- ❌ NON incolpare Elena per i fallimenti — il test misura il sistema, non lei

## Cosa FARE

- ✅ Mandare credenziali WP-Admin separatamente (non nel foglio)
- ✅ Garantire che Elena abbia un blocco di 2-3 ore dedicate (NON spalmare il test su 5 giorni — il flusso conta)
- ✅ Essere disponibile per dubbi sul test stesso (NON sull'esecuzione del WP-Admin)
- ✅ Quando ha finito, ringraziarla e farle vedere che il suo feedback ha generato cambi concreti

---

## Aggiornare il template

Se durante la review (prima di mandarlo a Elena) ti viene voglia di modificare un scenario:

```bash
cd /Users/aldosantoro/Desktop/DEV/saltelli-wp
# Modifica build_test_plan.py
python3 docs/qa/wysiwyg-diagnosis/build_test_plan.py
```

Il file `.xlsx` viene rigenerato. Il `.py` è la source of truth — il `.xlsx` è generato.

---

*Maintained by orchestrator (Claude in chat). Last updated: 2026-05-05.*
