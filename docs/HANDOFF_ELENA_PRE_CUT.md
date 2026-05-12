# Handoff pre-cut — Elena Cappabianca

> **Destinataria:** Elena Cappabianca (Studio Legale Saltelli & Partners)
> **In copia:** Ludovica Casa, Avv. Emiliano Saltelli
> **Da:** Adsolut SRLS · tech@adsolut.it
> **Data:** 2026-05-12
> **Versione tema:** v1.3.21-chore-batch3-followups (CUT-READY)
> **Staging:** https://staging.studiolegalesaltelli.it
> **Manuale operativo CMS completo:** `docs/EDITOR-HANDOFF.md` v6.0 (resta sempre il riferimento per "come edito X")

Questo documento è un **bridge operativo** tra la chiusura del feedback (17 fix consegnati oggi) e il cut produzione. Ti dice cosa è cambiato in modo concreto, cosa devi fare ancora tu prima del go-live, e cosa NON devi toccare.

---

## 1. Cosa è stato consegnato oggi

A partire dal documento "Saltelli bug fix" che ci hai mandato, abbiamo lavorato in 3 batch consecutivi (multi-agentic parallel, ~75 min totali). **17 dei tuoi 23 punti sono chiusi**, 1 era già stato risolto la settimana scorsa, 1 dipende da una tua azione editoriale (zero code), 4 sono rimandati post-cut perché non bloccanti o richiedono decisioni di prodotto da rivedere insieme.

### 13 fix Batch 1 (Quick + Structural)

| # | Cosa vedevi | Cosa vedrai ora |
|---|---|---|
| 3 | Eyebrow "SCORRI" sotto il CTA hero in home | Rimosso |
| 4 | Filtri home § 01 Aree di pratica con "Tutti" + "Altri servizi" | Solo 3 cluster: Per i privati · Per le imprese · Contenzioso amministrativo |
| 5 | Click sulla row competenza in home non navigava | Tutta la row è cliccabile (markup `<a>` wrapping difensivo) |
| 6 | Prima lettera "D" si colorava solo su alcune voci | Bronze coerente su TUTTE le voci, hover/focus |
| 14 | Pagina 2 di /casi-rappresentativi/ con 1 solo caso | Ppp alzato a 24 → 1 sola pagina, no paginazione |
| 18 | Badge "TIER 1 · APPROFONDIMENTO" su alcune voci e CTA diverso sulle altre | Label uniforme "Approfondimento" via helper `saltelli_tier_badge_label()` |
| 20 | FAQ mostrava `<p>...</p>` come testo letterale | `wp_kses_post()` → HTML renderizzato correttamente |
| 21 | Tutti gli accordion FAQ restavano aperti contemporaneamente | Single-open: apri 2 → 1 si chiude |
| 9 | Box "§ Non trovi la materia? Scrivici una nota: in 24h..." in /aree-di-pratica/ | Sezione rimossa (ridondante con Ultima chiamata footer) |
| 11 | Immagine "Plate I — Facciata Studio" in mezzo al testo di /chi-siamo/ | Spostata come banner subito sotto l'hero |
| 12 | Sezione § 02 — 1999 ridondante in /chi-siamo/ | Rimossa (anno 1999 preservato in timeline § 05) |
| 16 | Sezione § 04 — Primo incontro nei 3 term pages | Rimossa (resta § Ultima chiamata footer globale) |
| 17 | Stelline ★ accanto alle aree tier-1 nei 3 term pages | Sostituite con l'effetto hover-D bronze coerente con la home |

### 3 fix Batch 2 (Mobile + Hero + Competenze)

| # | Cosa vedevi | Cosa vedrai ora |
|---|---|---|
| 2 | Menu mobile/tablet: voci di secondo livello non cliccabili + nessun back button | Tap su "Aree di Pratica" → submenu si espande inline (NON naviga) + tap su voce figlia → naviga · Back button "Chiudi" (icona X) in top-right del drawer · ESC key, click backdrop, body scroll lock |
| 13 | /casi-rappresentativi/ hero diverso da Team archive | Hero ora pattern identico Team: H1 grande sinistra + capsule destra `§ Anonimizzati` con conteggio dinamico + 3 righe trust |
| 23 | Pagine competenze: layout diverso tra tier-1 e tier-2, alcune NON modificabili (body_extended invisibile per tier-2) | Tutte le 19 competenze stesso template render. Tier-2 con `body_extended` SCF popolato ora visibile (era invisibile pre-refactor). Distinzione tier-1 = solo CSS modifier (display-band H1 + capsule indent + photo 1:1) |

### 1 fix Batch 3 (Prenota appuntamento)

| # | Cosa vedevi | Cosa vedrai ora |
|---|---|---|
| 22 | /prenota-appuntamento/ layout default minimale, diverso da Richiedi preventivo | Layout identico al pattern Richiedi preventivo: hero 8/4 + trust card + body editorial 60ch + CTA finale navy |

### 2 chore tecnici (zero impatto utente)

- **v1.3.20**: rimozione 71 regole CSS dead-code `.sl-tier1__*` post Wave C (rendering identico, file più snello) + documentazione 13 campi SCF orfani in `docs/SCF_ORPHAN_FIELDS.md` (cleanup target post-cut)
- **v1.3.21**: fix leftover #18 sui 3 term pages (badge ora uniforme anche lì) + aggiornamento `docs/EDITOR-HANDOFF.md` per Page 2714

### 1 fix già risolto la scorsa settimana (per memoria)

- **#10** `/lo-studio/` con layout "buggato" → consolidato con `/chi-siamo/`: il vecchio URL `/lo-studio/` fa 301 redirect a `/chi-siamo/`, contenuto editoriale completo conservato.

---

## 2. Cosa devi fare TU ora (pre-cut)

Mentre concordiamo con il cliente la finestra per il DNS switch, ci sono **azioni editoriali che solo tu puoi fare** (zero codice, da WP-Admin con il tuo account).

### A) Popolare scenari per term `contenzioso-amministrativo` e `imprese` (feedback #15 + #19)

**Problema:** vai su `/aree-di-pratica/contenzioso-amministrativo/` e nella sezione § 01 — Quando rivolgersi vedi le stesse 3 voci di `/aree-di-pratica/privati/` (Diritto condominiale · Eredità · Risarcimento). I 3 scenari sono **campi SCF per-term** ancora vuoti per quel term → il sito mostra un fallback generico.

**Cosa fare:**
1. WP-Admin → **Articoli → Tipo area** (sidebar sinistra)
2. Clicca sul term **Contenzioso amministrativo** → Modifica
3. Trova il metabox **"Term Tipo Area — Saltelli"** → tab "Quando rivolgersi"
4. Popola i 3 scenari con contenuto contestuale al contenzioso amministrativo (suggerimenti pattern editoriale):
   - **Scenario 1** (icona `§`): titolo + descrizione breve + permalink alla competenza interna (es. "Cartelle esattoriali")
   - **Scenario 2** (icona `¶`): ...
   - **Scenario 3** (icona `†`): ...
5. Salva.
6. Ripeti per il term **Per le imprese** se vuoi differenziarlo da Per i privati.

**Quando puoi farlo:** SUBITO. Nessun deploy serve, le modifiche sono live appena salvi.

### B) Migrare contenuto Page 2714 prenota-appuntamento (post Wave J)

**Contesto:** sulla Page 2714 ora vedi **2 metabox** in edit:
- **"Page Servizi — Hero & Lede"** (gruppo info-shared, nuovo, 16 campi) → questo è il metabox CANONICO
- **"Page Prenota Appuntamento"** (legacy, 1 campo `prenota_intro`) → questo è temporaneo, contiene il body dell'editor pre-Wave-J

**Cosa fare:**
1. WP-Admin → Pagine → **Prenota appuntamento** (ID 2714) → Modifica
2. Copia il contenuto da campo `prenota_intro` (metabox "Page Prenota Appuntamento")
3. Incollalo in campo **`body_content`** (metabox "Page Servizi — Hero & Lede" → tab Body)
4. Eventualmente raffina hero/eyebrow/lede/aside trust con copy editoriale tuo
5. Salva.
6. Verifica frontend `/prenota-appuntamento/`.

**Quando il backend tecnico potrà fare il cleanup finale:** dopo che hai migrato il contenuto, scriverci e disattiviamo il metabox legacy `Page Prenota Appuntamento` (Wave 6.1 post-cut).

### C) Minor UX fix che vuoi rifinire

Se durante il review hai trovato altri micro-aggiustamenti (copy, immagini, ordine voci nei moduli SCF), questo è il momento. Tutti i campi editoriali sono ora editabili dai metabox SCF di ogni Pagina / CPT (avvocato, competenza, caso) / Term (tipo-area).

**Riferimento dettagliato:** `docs/EDITOR-HANDOFF.md` § 4-10 per la mappa "voglio editare X → dove vado".

---

## 3. Cosa NON toccare (hard rule)

| Cosa | Perché |
|---|---|
| Aspetto → Personalizza, "CSS aggiuntivo" | Bloccato per ruolo editor (Wave 4.7.fix.5). Il design vive nel design system, contattare tech@adsolut.it per modifiche layout/CSS |
| Editor Gutenberg sulle 13 Pages SCF-only | Disabilitato di default. Modifica il content via metabox SCF della Pagina stessa (sotto il titolo) |
| Campi `bio_estesa` sulle 4 schede avvocato | Step D content protected — modifica solo via richiesta a tech@adsolut.it |
| Foto profilo dei 4 avvocati (`_thumbnail_id` = featured image) | Foto Avv. Saltelli è hard-protected (ID 2683). Le altre 3 puoi sostituirle ma comunicalo prima |
| Field SCF orfani documentati in `docs/SCF_ORPHAN_FIELDS.md` | Sono campi le cui sezioni template sono state rimosse Wave S. Non sono visibili sul frontend ma il dato resta nel DB per backwards-compat. Sarà fatto cleanup Wave 6.1 post-cut |
| Customizer Aspetto generale del tema | Bloccato per editor |
| Plugin (attiva/disattiva/install) | Solo amministratori. SCF, Yoast, eventuali altri restano configurati come oggi |
| Eliminazione di Pages canoniche (18 pagine, lista in EDITOR-HANDOFF § 3.7) | Rompe routing e menu. Se devi spostare/rinominare URL contattare tech |
| Slug `/chi-siamo/`, `/aree-di-pratica/`, `/risorse/`, `/costi-e-consulenze/`, `/contatti/`, `/prenota-appuntamento/` | URL canonici, redirect 301 attivi dai vecchi slug |

---

## 4. Roadmap cut produzione

Quando hai finito i tuoi minor UX fix:

1. **Tu ci scrivi**: "Ho finito, possiamo andare live."
2. **Concordiamo finestra con il cliente** (Avv. Saltelli): tipicamente 1h di window per essere sicuri
3. **DNS switch**: noi modifichiamo l'A-record di `studiolegalesaltelli.it` per puntare al nuovo droplet (IP 178.62.207.50)
4. **SSL produzione**: rigenerazione automatica Let's Encrypt sul dominio prod (~30 secondi)
5. **WP URL update**: `siteurl` e `home` passano da staging a prod
6. **Smoke test post-switch**: verifichiamo 8 URL critici + sitemap.xml + Yoast
7. **Email finale a te + cliente**: "Siamo live, ecco le nuove credenziali admin prod (se diverse da staging)"
8. **Onboarding 30 min live (opzionale)** se vuoi un walkthrough finale del nuovo CMS in produzione

**Tempo totale window**: 30-60 min. Comunicheremo lo stato in tempo reale.

### Cosa accadrà DOPO il cut (backlog post-cut documentato)

| Wave | Cosa | Quando |
|---|---|---|
| **P11 Contatti** | Token CSS alignment + dropdown "Area di interesse" reso dinamico da CPT competenza | 1-2 settimane post-cut |
| **6.0 CPT competenza migration** | Disable Gutenberg sulle 19 competenze + unify post_content → body_extended (chiude tech debt template Wave C) | 1-2 settimane post-cut |
| **6.1 SCF orphan cleanup** | Disattivazione field group `group_prenota_appuntamento_v1` legacy + cleanup 13 campi SCF orfani (script in `docs/SCF_ORPHAN_FIELDS.md`) | 2-3 settimane post-cut |
| **5.1 Image Expansion** | Sostituzione placeholder Picsum con foto reali (hero homepage + Plate I facciata + eventuali ritratti) | Quando le foto reali sono pronte |
| **Single-post template** | Template editoriale dedicato per i 326 articoli del blog (alta priorità SEO) | Richiesta Design + sviluppo, 2-3 settimane post-cut |
| **EDITOR-HANDOFF v6.1** | Refresh manuale con tutti gli aggiornamenti Wave 4.7.fix.5 → Batch 1+2+3 | 1 settimana post-cut |

---

## 5. Contatti tech (Adsolut)

| Caso | Chi | Come |
|---|---|---|
| "Vedo un campo che non sembra editabile" | Adsolut tech | tech@adsolut.it · Slack #saltelli-cms |
| "Voglio modificare un layout / CSS / colore" | Adsolut tech | tech@adsolut.it (NON editabile dal CMS, è scelta voluta) |
| "Il sito è giù / errore 500" | Adsolut on-call | tech@adsolut.it + cellulare diretto se urgenza |
| "Voglio aggiungere/rimuovere una pagina canonica" | Adsolut tech | tech@adsolut.it (impatta routing + menu + SEO) |
| "Voglio popolare contenuto su una Pagina/CPT/Term" | Sei tu | WP-Admin diretto |
| "Voglio caricare nuove foto avvocati" | Sei tu (avvisa prima) | WP-Admin → Media + sostituzione thumbnail |
| "Domande sul manuale operativo" | EDITOR-HANDOFF.md v6.0 | `docs/EDITOR-HANDOFF.md` |

---

## 6. Versione corrente & repo

- **Tema**: v1.3.21-chore-batch3-followups
- **Staging**: https://staging.studiolegalesaltelli.it
- **Repo GitHub**: https://github.com/Adsolut-Ai-Agency/saltelli-wp/
- **Tag release**: v1.3.21-chore-batch3-followups (sarà creato pre-cut)
- **Backlog post-cut completo**: `CLAUDE.md` § "Backlog" + tabella "What's done"

---

*Questo handoff è un documento operativo pre-cut. Sarà superato da EDITOR-HANDOFF.md v6.1 dopo il cut produzione. Maintained by Adsolut SRLS · tech@adsolut.it.*
