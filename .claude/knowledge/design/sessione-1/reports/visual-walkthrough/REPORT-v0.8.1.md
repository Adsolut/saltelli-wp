# Visual Walkthrough — v0.8.1-beta-attorney-placeholder

**Data:** 2026-04-30
**Tester:** Claude (orchestrator)
**Tool:** Claude in Chrome (browser_batch + javascript_tool)
**Build sotto test:** v0.8.1-beta-attorney-placeholder
**Tempo:** ~12 minuti

---

## Risultati aggregati

| # | Punto | v0.7.0 | v0.8.0 | v0.8.1 | Note |
|---|---|:---:|:---:|:---:|---|
| 1 | Hero homepage 100vh, 3 righe | ✅ | ✅ | ✅ | mantenuto |
| 2 | Lista 19 aree tier-1 evidenziato | ✅ | ✅ | ✅ | mantenuto |
| 3 | Layout asimmetrico generico | ✅ | ✅ | ✅ | mantenuto |
| 4 | Drop-cap "L" Lo studio | ✅ | ✅ | ✅ | mantenuto |
| 5 | 4 avvocati homepage asimmetrici | ✅ | ✅ | ✅ | mantenuto |
| 6 | Casi rappresentativi tipografici | ✅ | ✅ | ✅ | mantenuto |
| 7 | Footer dark navy 3 colonne | ✅ | ✅ | ✅ | mantenuto |
| **8** | /costi/ layout editoriale | ✅ | ✅ | 🔴 **REGRESSIONE** | Body a destra fluttua, eyebrow staccato a sinistra, vuoto enorme tra sezioni |
| 9 | Single-competenza tier-1 base | ✅ | ✅ | ✅ | mantenuto (FAQ + answer capsule + CTA) |
| **10** | Single avvocato Emiliano | ✅ | ✅ | ✅ | mantenuto |
| **11** | Archive /tipo-area/* | n/a | ✅ | ✅ | mantenuto |
| 12 | Mobile 375px responsive | ❌ | ✅ | (non testato) | regressione possibile |
| **+13** | Single-avvocato senza foto (3/4 lawyer) | ❌ | ❌ | 🔴 **STILL FAIL** | placeholder gradient gigante in alto, content sotto, sticky sovrappone |
| **+14** | Archive /avvocati/ | n/a | (non testato) | 🔴 **NEW FAIL** | Solo 2/4 lawyer visibili (Emiliano + 1 placeholder vuoto) |
| **+15** | Archive /competenze/ | n/a | (non testato) | 🔴 **NEW FAIL** | Layout rotto a fold, headline scivola fuori, lista 19 aree non visibile sopra fold |
| **+16** | Single-competenza content tier-1 | n/a | (non testato) | 🔴 **NEW FAIL** | H2 sub-section sovrapposti (Avvocato tributarista Napoli + Studio Legale Saltelli a Napoli + Lo Studio Legale Saltelli di Napoli appaiono come blocchi senza respiro) |
| **+17** | Header sticky transition | n/a | (non testato) | 🔴 **NEW FAIL** | Transition non termina, header semi-transparent durante scroll |

---

## Score globale

**11 PASS · 0 WARN · 6 FAIL** (5 nuovi + 1 regressione)

→ **Decisione: NO-GO totale per Step F.** Recovery comprehensive necessario.

---

## Analisi root cause per ogni FAIL

### #8 — /costi/ regressione
**Sintomo:** Su desktop 1440, eyebrow "§ 01 — COME FUNZIONA" sta a sinistra colonna stretta (240px), ma il body content "Trenta minuti, gratuiti..." e "La prima consulenza" h2 sono SCIVOLATI in colonna destra a posizioni anomale (h2 a 470px from left).
**DOM verifica:** `costiSectionGrid: "200px 836px"` (corretto) MA `firstSectionWidth: 1100px` con margine 170px da viewport 1440px → asymmetric layout funzionante a livello regola CSS, ma visivamente il content è sbilanciato perché manca un wrapper che tenga eyebrow + h2 + body insieme.
**Probabile causa:** mini-fix avvocato ha aggiunto regola CSS che impatta `.sl-page` o `.sl-costi`.

### #13 — Avvocati senza foto STILL FAIL
**Sintomo:** Wrapper `.sl-attorney__portrait` ora c'è, ma il box gradient grigio occupa **600x800px nativo** invece dei 480px max-width dichiarati nel mini-fix. Il content "PARTNER · GIUSLAVORISTA / Fabiana Saltelli / bio..." inizia in fondo, sotto il gigantismo.
**Probabile causa:** Una regola CSS successiva con specificità maggiore (probabilmente in components.css o sections.css più giù) **sovrascrive** il `max-width: 480px` del mini-fix. L'agent dovrebbe avere usato `!important` o una specificità più alta come `figure.sl-attorney__portrait`.

### #14 — Archive /avvocati/ solo 2/4 lawyer
**Sintomo:** Headline "Quattro / professionisti." + subline OK, ma sotto vedo solo: foto Emiliano grande sinistra + 1 placeholder grigio piccolo destra, mancano altri 2 lawyer.
**Probabile causa:** Il template `archive-avvocato.php` usa `WP_Query` con `posts_per_page` ridotto, oppure la grid layout 2x2 non sta renderizzando le righe successive, oppure mancano effettivamente CPT (verifica in DB).

### #15 — Archive /competenze/ rotto
**Sintomo:** Headline "Diciannove aree. *Tre presidiate in profondità.*" appare in colonna destra **tutta a riga singola** (non wrappa) e finisce fuori viewport. Lista 19 aree non visibile in viewport iniziale.
**Probabile causa:** CSS della headline ha `white-space: nowrap` o `max-width` errato; oppure il grid layout dell'archive ha `grid-template-columns` che dà alla seconda colonna troppo spazio.

### #16 — Single-competenza content sub-section sovrapposti
**Sintomo:** Nel body migrato del CPT competenza, ci sono **3 h2 ravvicinati senza respiro** ("Avvocato tributarista Napoli", "Lo Studio Legale Saltelli a Napoli si occupa di...", "Lo Studio Legale Saltelli di Napoli si mette dalla tua parte"). Lette di fila sembrano paragrafi continui, non sezioni.
**Probabile causa:** Step D Content Migration ha lasciato 3 H2 + body lunghi nel post_content originale del cliente. Il template li renderizza letteralmente. Issue di **content quality**, non di codice. Va o pulito il content (eliminare h2 ridondanti, accorpare body) o stilato il CSS h2 con `margin-top: 64px` per dare respiro.

### #17 — Header sticky transition lenta
**Sintomo:** Su pagina con scroll, ho visto in più screenshot l'header in stato "transition" (semi-transparent) per troppo tempo (>1 sec), finestra di mezzo tra trasparente e solid.
**Probabile causa:** `transition: all 600ms` su header invece di `transition: background 250ms` mirato. Oppure JS scroll-trigger che riapplica la classe ad ogni scroll-event, causando flicker.

---

## Cosa funziona bene (preservare nel fix)

- Tutti i fix Pain Points (P0.1-P1.4) sono mantenuti
- Mobile fix M1+M2+M3 confermati funzionanti (smoke v0.8.0 walkthrough)
- Taxonomy /tipo-area/* perfetta
- Single-avvocato Emiliano perfetto (foto + sticky + bio)
- Schema 16/16 validi
- Tier-1 first ordering nelle liste
- FAQ accordion `+/-` editoriali
- Drop-cap selettivo solo tier-1

---

## Decisione: prompt comprehensive recovery v0.9.0

Mini-fix è stato troppo focused: ha risolto un sintomo (markup placeholder) ma ha introdotto regressione (#8) e non ha visto 4 bug pre-esistenti pubblici (#14, #15, #16, #17).

Serve un agent **Recovery** comprehensive che:
1. Diagnostica per ognuno dei 6 FAIL la causa CSS specifica
2. Fix CSS chirurgici uno alla volta con curl-test dopo ogni fix
3. Visual walkthrough auto-verifica al termine per confermare nessuna nuova regressione
4. Per il #16 (content quality), decide tra: (a) regola CSS h2 con respiro, (b) script PHP che pulisce post_content del CPT competenza, (c) decisione orchestrator

---

## Screenshot evidenziali

- ss_77507jq7z — Fabiana single-avvocato, placeholder gigante in alto + sticky sovrappone nome (FAIL #13)
- ss_9180scido — Fabiana scrolled, content "PARTNER · GIUSLAVORISTA · Fabiana Saltelli" in basso dopo gigantismo
- ss_18853c8t7 — Archive /avvocati/ solo Emiliano + 1 placeholder vuoto (FAIL #14)
- ss_4466v9m8h — Archive /competenze/ headline scivolata fuori viewport (FAIL #15)
- ss_5564p2m62 — Single-competenza tier-1 body con 3 h2 sovrapposti (FAIL #16)
- ss_69898gkvi — Single-competenza tier-1 lista bullet completa (mostra che il content esiste, è solo malstilato)
- ss_1047v8s5w — /costi/ §01 layout sbilanciato (REGRESSIONE #8)

---

*Walkthrough completato. Procedo con prompt Recovery v0.9.0.*
