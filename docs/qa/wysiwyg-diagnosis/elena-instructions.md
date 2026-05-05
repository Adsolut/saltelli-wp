# Messaggio pronto per Elena

> **Da copiare e incollare** quando le mandi il link al Google Sheets condiviso.
> **Tono**: caldo, diretto, onesto. Niente corporate-speak.
> **Canale**: email Adsolut (per tracciabilità) + ping su Slack ("ti ho mandato un'email su un test sul sito Saltelli").

---

## Versione email (formale ma calda)

**Oggetto:** Saltelli WP — ti chiedo 2 ore per un test che ci serve davvero

---

Ciao Elena,

prima che il sito Saltelli vada in produzione vorrei che facessi un giro completo del WP-Admin facendo cose vere — non guardandolo, ma usandolo. Ti spiego in 30 secondi perché.

Quando abbiamo riscritto l'architettura CMS del sito (le ultime 4 settimane), abbiamo costruito un modello di campi pensato per darti autonomia editoriale. Sulla carta è tutto pulito. Sul campo, sospetto che ci siano dei punti in cui il sistema **non è ovvio** — magari trovi una pagina sul sito ma non capisci da dove modificarla in WP-Admin, oppure devi fare 4 click dove te ne aspettavi 1.

Vorrei capire **dove** non è ovvio. Perché solo se mi dici dove inciampi posso sistemarlo prima del go-live, invece che dopo (quando sarebbe più costoso e più imbarazzante con il cliente).

Ho preparato un test di 10 scenari concreti — cose tipo *"il cliente ti chiede di cambiare il numero di telefono"*, *"aggiungi una FAQ nuova"*, *"modifica la bio di un avvocato"*. Sono task realistici, non trabocchetti. Tempo stimato totale: **circa 60-90 minuti** se il sistema funziona bene; di più se ci sono problemi (e quello che impieghi in più è esattamente l'informazione che mi serve).

Ti chiedo **due cose importanti**:

1. **Non studiare il manuale prima.** Voglio vedere quanto è ovvio il sistema senza preparazione. Il manuale lo puoi aprire SOLO se ti blocchi davvero (e in quel caso me lo segni).
2. **Sii onesta sui tempi e sulla frustrazione.** Se uno scenario ti fa innervosire, segna "5/5". Non è un giudizio su di te — è il dato che mi serve per capire dove intervenire.

**Link al Google Sheets per il test**: [INCOLLA QUI IL LINK CONDIVISO]

Sul foglio "Inizia da qui" trovi tutte le istruzioni. Sul foglio "Test Plan" i 10 scenari. Sul foglio "Open feedback" 6 domande più libere quando hai finito.

**Credenziali WP-Admin** (NON metterle nel foglio):
- URL: https://staging.studiolegalesaltelli.it/wp-admin/
- Username: [INSERIRE]
- Password: [INSERIRE]

Quando hai finito, fammi un fischio su Slack o rispondi a questa mail — aggregherò i risultati e capiremo come sistemare le cose. Idealmente entro la prossima settimana, se riesci.

Grazie davvero. Il tuo feedback qui vale più di qualsiasi audit tecnico — sei tu che dovrai usare questo CMS ogni giorno, e voglio che funzioni per te.

Duccio

---

## Versione Slack (più informale)

```
Ehi Elena, ti scrivo qui ma ti ho mandato anche email.

Riassunto: ti chiedo 1-2 ore per un test sul WP-Admin del sito Saltelli su staging. Non è un test che fai te — è un test del SISTEMA che abbiamo costruito. Devo capire dove ti facciamo perdere tempo prima che il sito vada in produzione.

Trovi tutto nel Google Sheet che ti ho condiviso: [LINK].

Tre cose da non dimenticare:
1. NON studiare il manuale prima
2. Tieni il timer
3. Sii brutalmente onesta sulla frustrazione

Quando hai finito, ping. Grazie 🙏
```

---

## Note interne (NON mandare a Elena)

- **Quando inviare**: idealmente lunedì mattina, così Elena ha la settimana per dedicarsi senza fretta
- **Follow-up**: se dopo 3 giorni non ha confermato di aver iniziato, ping su Slack ("hai visto la mail?")
- **Se Elena chiede chiarimenti sul test**: rispondere ai dubbi sul TEST, non sull'esecuzione del WP-Admin (es. "non capisco se devo segnare il tempo solo della modifica o anche del cercare dove andare?" → rispondi "tutto, anche il tempo di ricerca")
- **Se Elena si blocca davvero su uno scenario**: ok consultare il manuale ma annotarlo nella colonna "Cosa hai fatto alla fine"
- **Tempo realistico aggregazione (Fase 3) post-completamento**: ~1 ora per leggere + categorizzare + scrivere `findings.md`

---

*Last updated: 2026-05-05.*
