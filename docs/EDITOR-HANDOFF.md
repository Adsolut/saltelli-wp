# Manuale Editoriale — Studio Legale Saltelli

> **Destinatari:** Elena Cappabianca, Ludovica Casa, eventuali collaboratori editoriali esterni Adsolut.
> **Versione:** 6.0 — 2026-05-11 (Wave 4.7.fix.5 PAGES CLEANUP + BLOG DOC + CUSTOMIZER LOCK: l'admin "Pagine" mostra ora solo le 19 Pages reali · sezione blog completa · Customizer/CSS bloccato per il ruolo editor)
> **Mantenuto da:** Adsolut SRLS · tech@adsolut.it
> **Repository:** https://github.com/Adsolut-Ai-Agency/saltelli-wp/blob/main/docs/EDITOR-HANDOFF.md
> **Ambiente coperto:** staging WordPress dello Studio Legale Saltelli (production cut successivo).

> **Cosa è cambiato in v6.0** (2026-05-11, Wave 4.7.fix.5 PAGES CLEANUP + BLOG DOC + CUSTOMIZER LOCK):
> - **Pulizia Pagine legacy**: 16 Pages obsolete sono state cestinate — 13 bozze mai pubblicate (vecchie landing SEO 2024-2025 + vecchie "competenze" 2019-2020 pre-CPT) + 3 Pages pubblicate orfane (`conferma` = vecchia thank-you page Elementor, `prenota-un-appuntamento` = doppione legacy che già rimandava a `/contatti/`, `risultati` = vecchio contenitore casi superato dall'archivio CPT). **L'admin "Pagine" ora mostra 19 Pages — le 19 reali del sito**, non più 35 con metà spazzatura. Tutte le 16 cestinate hanno un redirect 301 già attivo (nessun link rotto). Sono in cestino per 30 giorni, recuperabili. Vedi la **lista canonica delle 19 Pages in §3.7**.
> - **NB Page 2811 "Lo Studio" NON è stata toccata**: resta una delle 12 Pages con metabox SCF (Gutenberg disabilitato) — `/chi-siamo/lo-studio/`, linkata dal footer. (Se vedi un'indicazione contraria in vecchi appunti, ignorala.)
> - **Customizer + "CSS aggiuntivo" bloccati per il ruolo editor**: chi ha ruolo `editor` (Elena) non vede più — e non può raggiungere — Aspetto → Personalizza né "CSS aggiuntivo". Se ci provi, vedi un messaggio: "Per modificare design/layout/CSS contatta l'amministratore (tech@adsolut.it)". Questo è **voluto** — il design vive nel design system, non si tocca dal CMS. Gli amministratori (Adsolut, Avv. Saltelli) continuano ad avere accesso normale.
> - **Sezione blog completa (§9 riscritta)**: il blog è 100% WordPress standard (editor Gutenberg + sidebar "Documento") — niente metabox SCF come per le Pages. La §9 ora ha la **mappa completa "campo della sidebar → cosa diventa sul frontend"** (Estratto = lede italico, Immagine in evidenza, Autore = firma + bio avvocato, Categoria = breadcrumb) e l'elenco di cosa **non** è editabile da admin (tempo di lettura, eyebrow "← Editoriale", header dell'archivio blog — tutto generato dal tema).
> - **Notice contestuali nell'editor Articoli**: quando apri un Articolo (o la Page "Blog" ID 1413) vedi in cima un box promemoria che spiega dove finiscono i campi della sidebar.

> **Cosa è cambiato in v5.0** (2026-05-10, Wave 4.7.fix.4 STRATEGY A FULL SCF):
> - **Una sola sorgente di verità per Page WP**: l'editor Gutenberg/classic è **disabilitato** sulle 12 Page WP con metabox SCF attached. Quando apri una di queste Pages vedi: title + slug + sotto solo il metabox "Saltelli — Page X" con i campi. Niente più ambiguità tra editor di contenuto + metabox separato.
> - **Pages affette** (Gutenberg disabled): Home (17), Contatti (23), Lavora con noi (372), Domande Frequenti (2708), Guide Gratuite (2709), Prima Consulenza (2711), Come Lavoriamo (2712), Richiedi Preventivo (2713), Lo Studio (2811), Aree di Pratica (2812), Risorse (2813), Chi Siamo (2822).
> - **Pages NON affette** (Gutenberg attivo come prima): Costi e Consulenze (2695, ha già SCF nativo CPT-based), Privacy / Cookie / Note legali / Glossario / Blog / le pagine senza metabox SCF.
> - **post_content delle 7 Pages bonificato**: il content che vedevi in editor Gutenberg era ZOMBIE (post_content presente in DB ma NON renderizzato sul frontend per via dei template SCF-first). Ora è stato svuotato. Backup `_legacy_post_content_backup` postmeta preservato per rollback safety.
> - **Notice editoriale aggiunto**: sulle 12 Pages target vedi un box informativo "Modifica il contenuto qui sotto" sotto il title, sopra il metabox SCF, per chiarire dove editare.
> - **Admin shortcuts per archive CPT**: nuova guidance sotto Saltelli Settings → tab "Archive Headers" + admin bar shortcut quando visiti `/chi-siamo/team/` o `/chi-siamo/casi-rappresentativi/` su frontend logged-in.
> - **Vedi §3.5** per la mappa admin path aggiornata + §3.6 (nuovo) per la spiegazione archive CPT workflow.

> **Cosa è cambiato in v4.0** (2026-05-08, Wave 4.7.fix.3 Page Metabox migration):
> - **Modello mentale ripristinato**: "modifica pagina = modifica contenuto pagina". Quasi tutto ciò che è specifico di una Page WP si modifica **dalla pagina stessa** (WP Admin → Pagine → seleziona la pagina → Modifica → vedi metabox sotto/a fianco dell'editor).
> - **4 tab Saltelli Settings rimosse** (Hero Homepage, Studio Section, Team & Casi, Press Homepage): il loro content è migrato a **Pagine → Home** (la Page WP "Home" è il punto di edit della homepage `/`).
> - **Tab "Hub Pages" rimossa**: il content di `/chi-siamo/`, `/aree-di-pratica/`, `/risorse/` ora si modifica direttamente dalle relative Page WP.
> - **Saltelli Settings semplificato**: 14 → 9 tab. Ora contiene solo configurazione **globale** (header/footer/brand/contatti/CTA condivise/archive headers/taxonomy).
> - **Helper saltelli_page_field()**: nuovo helper interno che legge i field SCF dalla Page WP corrente. Trasparente per Elena.
> - **Migration data**: 30 chiavi options legacy → postmeta delle 4 Page WP target (Home 17, Chi Siamo 2822, Aree di Pratica 2812, Risorse 2813) — script idempotente.
> - **Vedi §3.5** per la mappa admin path aggiornata.

> **Cosa è cambiato in v3.0** (2026-05-08, Wave 4.7.fix.2):
> - **Bug fix**: il campo "Body sezione studio" appariva vuoto in admin pur essendo popolato in homepage. Default editoriale (3 paragrafi storici) seedato e visibile.
> - **Rinomina URL**: `/chi-siamo/risultati/` → `/chi-siamo/casi-rappresentativi/`. Vecchio URL ridireziona 301.
> - **Menu primary ricostruito**: 22 voci con riferimenti diretti alle pagine (nessun URL hardcoded).
> - **Nuove sezioni editabili in SCF**: hub e archive CPT ora hanno H1/eyebrow/intro modificabili.

> **Cosa è cambiato in v2.0** (2026-05-08): Wave 4.7.fix ha sbloccato il menu **Saltelli — Settings** che prima era invisibile (bug architetturale ACF Free → SCF migration).

Questo documento è il **manuale operativo** del nuovo CMS Saltelli. Lo userai sia come riferimento quotidiano per gestire i contenuti, sia come **strumento di QA** durante la fase di debug attiva: ogni volta che incontri un comportamento strano o un campo che non ti convince, segnala — il team tecnico ha bisogno del tuo occhio editoriale per chiudere il cerchio.

Leggi le sezioni 0–3 una volta tutte. Le altre tienile come riferimento da consultare quando ti serve un'azione specifica.

---

## Indice

0. [Stato del progetto e fase di test](#0-stato-del-progetto-e-fase-di-test)
1. [Cosa devi sapere prima di iniziare](#1-cosa-devi-sapere-prima-di-iniziare)
2. [Accesso al WP-Admin](#2-accesso-al-wp-admin)
3. [TL;DR — Mappa rapida "voglio editare X"](#3-tldr--mappa-rapida-voglio-editare-x)
3.5. [Pagina WP vs Tassonomia vs Archive CPT — mappa per i 15 URL pubblici](#35--pagina-wp-vs-tassonomia-vs-archive-cpt-mappa-per-i-15-url-pubblici)
3.6. [🆕 Archive CPT: come si edita `/chi-siamo/team/` e `/chi-siamo/casi-rappresentativi/`](#36--archive-cpt-come-si-edita-chi-siamoteam-e-chi-siamocasi-rappresentativi)
3.7. [🆕 Pagine WP del sito — lista canonica (19 Pages)](#37--pagine-wp-del-sito-lista-canonica-19-pages)
4. [Saltelli — Settings (impostazioni globali)](#4-saltelli--settings-impostazioni-globali)
5. [Le 9 pagine custom](#5-le-9-pagine-custom)
6. [Le 4 schede avvocato](#6-le-4-schede-avvocato)
7. [Le 19 aree di pratica (Competenze)](#7-le-19-aree-di-pratica-competenze)
8. [I moduli riutilizzabili (FAQ, Casi, Principi…)](#8-i-moduli-riutilizzabili-faq-casi-principi)
9. [Il blog](#9-il-blog)
10. [Workflow comuni — guide passo-passo](#10-workflow-comuni--guide-passo-passo)
11. [Convenzioni di scrittura editoriale](#11-convenzioni-di-scrittura-editoriale)
12. [Cosa è già ottimizzato per le AI (GEO)](#12-cosa-è-già-ottimizzato-per-le-ai-geo)
13. [Cosa NON toccare mai](#13-cosa-non-toccare-mai)
14. [Upload immagini e PDF](#14-upload-immagini-e-pdf)
15. [Errori comuni e come evitarli](#15-errori-comuni-e-come-evitarli)
16. [Anteprima, cache, troubleshooting](#16-anteprima-cache-troubleshooting)
17. [Come segnalare bug e problemi durante il debug](#17-come-segnalare-bug-e-problemi-durante-il-debug)
18. [Quando scrivere al team tecnico](#18-quando-scrivere-al-team-tecnico)
19. [Glossario](#19-glossario)

---

## 0. Stato del progetto e fase di test

### 🔍 Siamo in fase di Debug & QA, NON al lancio

Il sito **NON è ancora live in produzione**. Quello che vedi su `staging.studiolegalesaltelli.it` è la **versione di test del nuovo sito**. Il dominio principale `studiolegalesaltelli.it` punta ancora al **vecchio sito** (quello che il cliente ha online oggi).

Il go-live richiede ancora questi step (in ordine):

1. **Fase di debug attiva** ← siamo qui — Elena, Ludovica e team tecnico Adsolut testano il sito su staging, segnalano bug, popolano contenuti mancanti, validano il copy. **🆕 Sblocco Wave 4.7.fix (2026-05-08)**: ora il menu Saltelli — Settings è visibile e funzionale → puoi finalmente editare i contenuti homepage + footer da WP-Admin senza chiedere al team tecnico.
2. **Onboarding Elena 30 min** — sessione live di walkthrough sul nuovo menu Saltelli Settings (10 tab) e workflow di edit
3. **Cut produzione** — DNS switch + tag release v1.0.0 + comunicazione cliente

### Cosa significa per te in questa fase

✅ **Puoi e DEVI testare aggressivamente.** Qualsiasi comportamento strano, campo che non rispetta le tue aspettative, copy che suona male, immagine sgranata, link rotto: **segnala**. È esattamente quello che stiamo cercando.

✅ **Puoi popolare contenuti reali.** Tutto quello che editi ora resta nel database. Quando passeremo a produzione, i contenuti popolati su staging vengono trasferiti.

⚠️ **Non condividere il link staging con il cliente** finché non chiudiamo il debug. Lo staging è un ambiente di lavoro, non una vetrina presentabile. Ci sono ancora dettagli da rifinire.

⚠️ **Non aspettarti perfezione del 100% subito.** Wave 1+2+3 hanno coperto: schema dati, popolamento contenuti, refactor template. Ma alcuni edge case emergeranno solo testando.

### Cosa è già stato completato (per tua trasparenza)

| Wave | Cosa è stato fatto |
|---|---|
| 0 — Foundation CMS | Plugin ACF Free attivo + 8 tipi di contenuto custom (FAQ, Casi, Modalità, Scenari, Principi, Trust signals, Formazione, Guide) |
| 1 — Field Groups | 17 gruppi di campi ACF creati per pagine, avvocati, aree di pratica, settings globali |
| 2 — Content Migration | 273 campi + 63 elementi CPT popolati con i contenuti del sito (FAQ, casi vinti, body editoriali, ecc.) |
| 3 — Template Refactor | I template PHP del tema ora leggono dai campi ACF: tu modifichi i campi, il sito si aggiorna |
| 4 — 4.8 — CMS editability + cleanup migrations + UX polish | Iterazioni multiple su editability, ACF default values, content migration, visual polish |
| 4.7.fix — SCF migration ⭐ | Sostituito plugin ACF Free → Secure Custom Fields. Ora il menu "Saltelli — Settings" è visibile e tutti i 50 campi globali (10 tab) sono editabili da WP-Admin. |

### Cosa devi aspettarti DI TROVARE durante il debug (lista incompleta)

- **Bio estese di 3/4 avvocati**: vedi sezione §6.1
- **Guide gratuite**: zero attualmente — Elena le caricherà
- **Articoli blog**: 326 storici migrati, ma la categorizzazione sui 3 cluster (tributario / lavoro / LGBTQ+) è in attesa di tuo input
- **Alcuni link interni** potrebbero puntare a URL legacy del vecchio sito — segnalali
- **Alcuni copy** potrebbero suonare "AI-generated" o non in voce Saltelli — segnalali
- **Schema JSON-LD** è generato automaticamente ma non ancora validato finale (fase Wave 4)
- **Immagini placeholder** in alcune aree di pratica Tier-2 — sostituibili a basso impatto

### Frequenza di reporting consigliata

- **Bug critici** (sito offline, errore PHP, content sparito): **subito** via Slack/WhatsApp a Duccio Santoro
- **Bug medi** (link rotti, immagini sbagliate, copy che non torna): **email a tech@adsolut.it ogni 1-2 giorni** in batch
- **Suggerimenti editoriali** (riformulazioni, miglioramenti tono, idee per nuove FAQ): **documento condiviso** o email settimanale

Vedi §17 per come strutturare le segnalazioni.

---

## 1. Cosa devi sapere prima di iniziare

### Il sito è un **tema custom**, non un page builder

Niente Elementor, Divi, WPBakery. Ogni pagina ha una **struttura editoriale fissa** (hero, lede, body, CTA finale, ecc.) e tu **riempi i campi**, non disegni il layout. Questo significa:

- ✅ Cambiare un titolo, un paragrafo, un'immagine, una CTA → **ti basta editare un campo**
- ✅ Aggiungere una FAQ, un caso vinto, una guida gratuita → **crei un nuovo "elemento" e lo si aggancia automaticamente**
- ❌ Spostare i blocchi di una pagina, cambiare la struttura del template, modificare i colori → **tecnico**

### I contenuti sono "in pezzi", non in un unico Word

Una pagina come `/costi/` non è un blob unico: ha un titolo, un sottotitolo, un trust box laterale, un blocco editoriale, una CTA finale. Ognuno è un campo separato, con la sua casellina sulla pagina di edit. Vedrai **tab e gruppi etichettati** (Hero · Aside · Body · CTA finale) che ti aiutano a orientarti.

### Lo "Saltelli — Settings" è il pannello che governa tutto

Prima di toccare le pagine, vai sempre a vedere se quello che vuoi modificare è in **Saltelli — Settings** (sidebar admin, in basso). Lì stanno: indirizzo, telefono, email, P.IVA, social, payoff, CTA default. Cambiando un valore lì, **si aggiorna ovunque** sul sito (header, footer, pagine multiple). Vedi sezione 4.

### Le modifiche sono visibili dopo qualche secondo

Il sito ha cache abilitata. Dopo un salvataggio, attendi 5–10 secondi e ricarica la pagina pubblica con `Ctrl+Shift+R` (Win) o `Cmd+Shift+R` (Mac) per saltare la cache del browser. Se dopo 30 secondi non vedi la modifica, vedi sezione 16.

### "Editor" vs "Administrator"

In WP ci sono ruoli diversi. Tu (Elena/Ludovica) sei **Administrator** durante il debug — accesso completo. Un eventuale terzo collaboratore esterno avrà ruolo **Editor**: può creare/modificare contenuti, ma non installare plugin né cambiare impostazioni globali.

### Il sito è in italiano e sarà letto da AI

Lo Studio Legale Saltelli punta non solo al traffico Google ma anche a essere **citato dalle AI** (ChatGPT, Perplexity, Google AI Overviews, Claude). Le 3 aree Tier-1 (tributario, lavoro, famiglia LGBTQ+) hanno una struttura specifica chiamata **answer capsule** ottimizzata per estrazione AI. Vedi §12.

---

## 2. Accesso al WP-Admin

### URL

| Ambiente | URL admin | Note |
|---|---|---|
| **Staging** (lavoro corrente) | https://staging.studiolegalesaltelli.it/wp-admin/ | Ambiente di prova, visibile pubblicamente ma non indicizzato |
| **Production** | https://studiolegalesaltelli.it/wp-admin/ | Sito live (DNS switch successivo al sign-off) |

⚠️ Durante la fase di debug, **lavora SOLO sull'admin di staging**. La production attualmente è il vecchio sito e non ha la nuova struttura ACF.

### Account disponibili

| Username | Email | Ruolo | Quando usarlo |
|---|---|---|---|
| `Emiliano Saltelli` | info@studiolegalesaltelli.it | Administrator | Account proprietario — usato da Avv. Saltelli |
| `Adsolut Staff` | tech@adsolut.it | Administrator | Account agency — uso quotidiano del team Adsolut |

**Le password sono allineate locale↔staging**. Per la production verranno ruotate al deploy. Tieni le credenziali nel password manager Adsolut. Non condividerle via email/chat.

### Per il collaboratore esterno

Se entra un freelance editorial, **NON dargli il login Administrator**. Aprire ticket a `tech@adsolut.it` chiedendo creazione utente con ruolo **Editor**. L'utente Editor vede tutto quello descritto in questo manuale tranne `Saltelli — Settings` e i settings di sistema.

### Browser consigliato

**Chrome** o **Firefox** ultime versioni. Safari funziona ma ha qualche quirk minore con TinyMCE. Se trovi un comportamento strano, prova a riprodurlo su Chrome prima di segnalarlo (per isolare se è bug del sito o bug del browser).

---

## 3. TL;DR — Mappa rapida "voglio editare X"

| Voglio modificare… | Vai su | Sezione di questo doc |
|---|---|---|
| Indirizzo, telefono, email, P.IVA, ordine | **Saltelli — Settings** → Studio Info | §4 |
| Coordinate mappa (lat/lng) | **Saltelli — Settings** → Mappa | §4 |
| Payoff sotto il logo, brand statement footer | **Saltelli — Settings** → Brand | §4 |
| Link social (Instagram, LinkedIn, Facebook, X) | **Saltelli — Settings** → Social | §4 |
| Testo del bottone "Prenota un incontro" usato di default | **Saltelli — Settings** → CTA Defaults | §4 |
| Hero, body, CTA della pagina `/costi/` | **Pagine** → Costi | §5 |
| Hero, body, CTA della pagina `/casi/` | **Pagine** → Casi | §5 |
| Hero + mappa + come arrivare di `/contatti/` | **Pagine** → Contatti | §5 |
| Hero + intro di `/faq/` | **Pagine** → FAQ | §5 |
| Una delle 5 pagine "info shared" (`/come-lavoriamo/`, `/prima-consulenza/`, `/lavora-con-noi/`, `/richiedi-preventivo/`, `/guide-gratuite/`) | **Pagine** → la pagina che ti serve | §5 |
| Bio, foto, contatti di un avvocato | **Avvocati** → l'avvocato che ti serve | §6 |
| Pagina di un'area di pratica (es. Diritto tributario) | **Aree di pratica** → l'area che ti serve | §7 |
| Aggiungere/modificare una FAQ | **FAQ** → New (o esistente) | §8.1 |
| Aggiungere un caso vinto | **Casi rappresentativi** → New | §8.2 |
| Aggiungere una guida gratuita PDF | **Guide gratuite** → New | §8.8, §14 |
| Aggiungere una formazione/titolo a un avvocato | **Formazione & Titoli** → New, poi associare in scheda avvocato | §8.7, §6 |
| Articolo del blog | **Articoli** → New (o esistente) | §9 |

---

## 3.5 — Pagina WP vs Tassonomia vs Archive CPT (mappa per i 15 URL pubblici)

> **Perché questa sezione esiste.** Nel sito convivono 3 tipi diversi di "pagina": le Pagine WP (familiari), i term taxonomy (es. cluster `/aree-di-pratica/privati/`) e gli archive CPT (es. `/chi-siamo/team/`). Da WP-Admin si modificano in 3 posti diversi. Tieni questa tabella sotto mano la prima settimana.

> **🆕 v5.0 (Wave 4.7.fix.4)**: Per le 12 Pages WP con metabox SCF attached l'editor di contenuto (Gutenberg/classic) è ora **disabilitato**. Apri la Page e vedi: title + slug + sotto direttamente i campi SCF nel metabox "Saltelli — Page X". Niente più editor sopra. Una sola sorgente di verità per pagina.

| URL pubblico | Tipo | Dove la modifichi in WP-Admin | Cosa puoi editare |
|---|---|---|---|
| `/` (homepage) | Pagina WP "Home" (ID 17, page_on_front) | **Pagine** → Home | 🆕 v4.0: Hero (eyebrow/headline/sub/CTA), Studio Section (titolo/body/foto facciata), Team & Casi (titoli + casi rappresentativi), Press (loghi) — tutto da metabox "Saltelli — Page Homepage" |
| `/chi-siamo/` (hub) | Pagina WP (ID 2822) | **Pagine** → Chi Siamo | 🆕 v4.0: Eyebrow / H1 (parte 1 + parte 2 corsivo) / intro paragrafo via metabox "Saltelli — Page Chi Siamo" |
| `/chi-siamo/lo-studio/` | Pagina WP figlia | **Pagine** → Lo Studio | post_content + SCF mission, lineage, faq di studio (group_lo_studio_v1) |
| `/chi-siamo/team/` | **Archive CPT** avvocato | NIENTE pagina admin diretta | Eyebrow / H1 / intro via Saltelli — Settings → tab "Archive Headers". Per il singolo avvocato → §6. |
| `/chi-siamo/casi-rappresentativi/` | **Archive CPT** saltelli_caso | NIENTE pagina admin diretta | Eyebrow / H1 / intro via Saltelli — Settings → tab "Archive Headers". Per il singolo caso → **Casi rappresentativi**. |
| `/aree-di-pratica/` (hub) | Pagina WP (ID 2812) — `post_content` vuoto by design | **Pagine** → Aree di Pratica | 🆕 v4.0: Hero (eyebrow/H1/intro) + 3 Cluster Cards (Privati/Imprese/Contenzioso label+desc) via metabox "Saltelli — Page Aree di Pratica" — 2 tab |
| `/aree-di-pratica/privati/` | **Term taxonomy** (`tipo-area`) | **Articoli** → Tipo area → Per i Privati | Description del term (max ~300 caratteri) |
| `/aree-di-pratica/imprese/` | **Term taxonomy** | **Articoli** → Tipo area → Per le Imprese | Description |
| `/aree-di-pratica/contenzioso-amministrativo/` | **Term taxonomy** | **Articoli** → Tipo area → Contenzioso amministrativo | Description |
| `/aree-di-pratica/{cluster}/{slug}/` | CPT competenza | **Aree di pratica** → l'area che cerchi | Tutto: H1, answer capsule, body, FAQ, casi, CTA — vedi §7 |
| `/risorse/` (hub) | Pagina WP (ID 2813) — `post_content` vuoto | **Pagine** → Risorse | 🆕 v4.0: Eyebrow / H1 (parte 1 + corsivo) / intro paragrafo via metabox "Saltelli — Page Risorse" |
| `/risorse/blog/` | Pagina WP "Blog" (ID 1413) | **Pagine** → Blog | post_content (i singoli articoli sono in **Articoli**, vedi §9) |
| `/risorse/domande-frequenti/` | Pagina WP | **Pagine** → Domande frequenti | post_content + SCF intro |
| `/risorse/guide-gratuite/` | Pagina WP | **Pagine** → Guide gratuite | post_content (le singole guide sono CPT **Guide gratuite**, vedi §8.8) |
| `/risorse/glossario-legale/` | Pagina WP | **Pagine** → Glossario legale | post_content (template legacy specializzato) |
| `/costi-e-consulenze/` | Pagina WP | **Pagine** → Costi e consulenze | Tutto via SCF: hero, modalità, scenari, principi, trust, FAQ — vedi §5.1 |
| `/costi-e-consulenze/{prima-consulenza,come-lavoriamo,richiedi-preventivo}/` | Pagina WP | **Pagine** → la pagina | post_content + SCF intro |
| `/contatti/` | Pagina WP (ID 23) | **Pagine** → Contatti | Form CF7 + mappa + come arrivare via SCF |
| `/contatti/lavora-con-noi/` | Pagina WP figlia | **Pagine** → Lavora con noi | post_content + SCF intro |

### 3.5.1 — Tipo di sorgente: 3 paradigmi

**Pagina WP (`Pagine` nel menu admin)** — la modalità classica WordPress. Trovi le pagine sotto "Pagine" nel menu di sinistra. Hanno un editor Gutenberg/classic + sotto i campi SCF specifici (vedi §5). Sono le 9 pagine custom + ~6 hub pages.

**Term taxonomy (`Articoli` → `Tipo area`)** — i 4 cluster `/aree-di-pratica/{privati,imprese,contenzioso-amministrativo,altri}/`. Tecnicamente sono "categorie" speciali per le aree di pratica. Per modificare il copy del cluster → vai sotto **Articoli** (NON Pagine) → **Tipo area** → clicca il cluster → modifica la "Description" (campo single textarea, max ~300 caratteri).

**Archive CPT** — `/chi-siamo/team/` e `/chi-siamo/casi-rappresentativi/`. Sono pagine generate automaticamente dal sito (NON esiste un'entry corrispondente in "Pagine"). Pre-Wave 4.7.fix.2 il copy di queste pagine (eyebrow, H1, intro) era hardcoded nel codice e NON modificabile. Da v3.0 lo modifichi via **Saltelli — Settings** → tab "Archive Headers" (vedi §4).

### 3.5.2 — Blocchi globali ricorrenti (1 modifica → tutto il sito)

Alcuni blocchi appaiono su **molte pagine** ma vivono in **un'unica fonte**. Modificare lì = aggiornare ovunque.

| Blocco | Dove vive in admin | Dove appare nel sito |
|---|---|---|
| **CTA pre-footer** ("§ Ultima chiamata · Prenota un incontro") | Saltelli Settings → CTA Defaults | Sopra il footer su ~19 pagine |
| **Banda Newsletter** ("L'editoriale del giovedì") | Saltelli Settings → Footer (`footer_newsletter_enabled` toggle) | Footer fascia 3 |
| **Trust signals plate** (4 segnali "20+ ANNI · ESPERIENZA" ecc.) | Saltelli Settings → Brand (campi `trust_signal_*`) | Trust bar globale (1 file, ~3 includes) |
| **Sticky WhatsApp message** (popup messaggio default) | Saltelli Settings → Brand → "Messaggio WhatsApp default" (post v3.0) | Bottone sticky in basso a destra su tutto il sito |
| **Footer aree tier-1** (3 link sotto "Aree di pratica") | Saltelli Settings → Footer Aree (post v3.0) | Footer colonna 2 |
| **Footer colophon** (indirizzo, contatti, P.IVA) | Saltelli Settings → Footer + Studio Info | Footer colonne 3-4 |
| **Header navigation** (menu superiore) | **Apparenza** → **Menu** → Saltelli Header (NON Saltelli Settings) | Header desktop + mobile |

### 3.5.3 — Cosa NON è editabile direttamente da admin

Per scelta tecnica/SEO alcuni elementi restano lato codice:

- **Markup HTML del form Brevo** (newsletter): se cambi provider serve dev request.
- **Schema JSON-LD** (Organization, Attorney, FAQPage, ecc.): generati dai template per garantire validazione Google. Modifica solo via dev.
- **Layout & design tokens** (colori, font, spacing): vivono in `tokens.css`. Volutamente locked per coerenza brand.
- **Slug URL** delle pagine già pubblicate: cambiarli rompe i link esterni — chiedi al team prima.
- **Logo, favicon, immagini brand**: vivono come asset statici (`/assets/`). Scambio via dev.

---

## 3.6 — Archive CPT: come si edita `/chi-siamo/team/` e `/chi-siamo/casi-rappresentativi/`

> **🆕 v5.0**: queste 2 pagine archivio CPT NON hanno una Page WP corrispondente (sono generate automaticamente dal codice). Per modificarle ci sono due punti distinti — uno per l'header pagina, uno per i singoli elementi.

### `/chi-siamo/team/` (archive CPT Avvocato)

**Header pagina** (titolo grande "Quattro avvocati / un atelier", eyebrow "§ Team", intro):
- WP Admin → **Saltelli — Settings** → tab **Archive Headers**
- Modifica i campi relativi a `archive_avvocato_*`
- Salva → frontend `/chi-siamo/team/` aggiornato

**Singolo avvocato** (foto, bio, specializzazioni, formazione, casi):
- WP Admin → **Avvocato** (voce di menu dedicato)
- Seleziona il profilo → Modifica → tutti i campi
- Vedi §6 per dettaglio campi

**Shortcut comodo**: visitando `/chi-siamo/team/` da frontend loggato come admin, l'admin bar in alto mostra "Modifica header archivio" con submenu "Tutti gli avvocati" — click ti porta direttamente al punto giusto.

### `/chi-siamo/casi-rappresentativi/` (archive CPT Caso)

**Header pagina** (titolo "Casi rappresentativi", eyebrow, intro):
- WP Admin → **Saltelli — Settings** → tab **Archive Headers**
- Modifica i campi `archive_caso_*`
- Salva → frontend aggiornato

**Singolo caso rappresentativo** (titolo, descrizione, outcome, area):
- WP Admin → **Casi rappresentativi** (voce di menu dedicato)
- Seleziona il caso → Modifica
- Vedi §8 per dettaglio campi CPT

**Shortcut**: idem — admin bar su frontend logged-in mostra "Modifica header archivio" + "Tutti i casi rappresentativi".

### Perché NON esistono come Page WP

Per design WordPress: gli archive CPT sono pagine generate automaticamente dal sito a partire dalla collezione di elementi (avvocati, casi). Non hanno entry corrispondente in "Pagine". L'header pagina (titolo + intro) vive quindi in Saltelli Settings (configurazione globale), i singoli elementi nella collezione CPT. È un compromesso WP standard. Per Elena: ricorda "header → Settings, singoli elementi → CPT dedicato".

---

## 3.7 — 🆕 Pagine WP del sito — lista canonica (19 Pages)

> **🆕 v6.0 (Wave 4.7.fix.5)**: l'admin **Pagine** ora mostra **esattamente queste 19 Pages** — sono tutte le Pagine reali del sito. Se vedi un cestino con altre voci (vecchie bozze, doppioni), è materiale legacy cestinato — non recuperarlo senza chiedere a tech@adsolut.it.

Legenda colonna "Come si edita":
- **🔒 SCF metabox** = Page con metabox "Saltelli — Page X", editor Gutenberg disabilitato — apri la Page e vedi solo title + slug + i campi SCF (vedi §5.0).
- **SCF nativo CPT** = la pagina `/costi-e-consulenze/` usa un Field Group ACF/SCF classico (vedi §5.1).
- **Gutenberg** = editor a blocchi standard (le pagine legali e il blog).

| ID | Titolo / slug | URL pubblico | Come si edita | Scopo |
|---|---|---|---|---|
| **17** | Home / `home` | `/` (front page) | 🔒 SCF "Saltelli — Page Homepage" (4 tab, 12 field) | Homepage: hero, sezione studio, team & casi, press |
| **2822** | Chi Siamo / `chi-siamo` | `/chi-siamo/` | 🔒 SCF "Saltelli — Page Chi Siamo" (1 tab, 4 field) | Hub Chi Siamo: eyebrow, H1, intro |
| **2811** | Chi Siamo* / `lo-studio` | `/chi-siamo/lo-studio/` | 🔒 SCF "Saltelli — Page Lo Studio" (mission, lineage, faq) | Pagina "Lo Studio" annidata sotto l'hub (linkata dal footer) |
| **2812** | Aree di Pratica / `aree-di-pratica` | `/aree-di-pratica/` | 🔒 SCF "Saltelli — Page Aree di Pratica" (2 tab, 10 field) | Hub Aree: hero + 3 cluster card (Privati/Imprese/Contenzioso) |
| **2813** | Risorse / `risorse` | `/risorse/` | 🔒 SCF "Saltelli — Page Risorse" (1 tab, 4 field) | Hub Risorse: eyebrow, H1, intro |
| **2695** | Costi e Consulenze / `costi-e-consulenze` | `/costi-e-consulenze/` | SCF nativo CPT (vedi §5.1) — Gutenberg attivo | Hub Costi: trasparenza tariffaria, "come calcoliamo", scenari |
| **2711** | Prima consulenza / `prima-consulenza` | `/costi-e-consulenze/prima-consulenza/` | 🔒 SCF "Saltelli — Page Servizi" (16 field: hero/aside/body/CTA) | Cosa aspettarsi alla prima consulenza |
| **2712** | Come lavoriamo / `come-lavoriamo` | `/costi-e-consulenze/come-lavoriamo/` | 🔒 SCF "Saltelli — Page Servizi" (16 field) | Workflow dello studio |
| **2713** | Richiedi un preventivo / `richiedi-preventivo` | `/costi-e-consulenze/richiedi-preventivo/` | 🔒 SCF "Saltelli — Page Servizi" (16 field) | Form/spiegazione richiesta preventivo |
| **2708** | Domande frequenti / `domande-frequenti` | `/risorse/domande-frequenti/` | 🔒 SCF "Saltelli — Page Domande Frequenti" (hero/TOC/CTA, 10 field) | Hero + indice. Le 28 FAQ sono moduli separati (§8.1) |
| **2709** | Guide gratuite / `guide-gratuite` | `/risorse/guide-gratuite/` | 🔒 SCF "Saltelli — Page Servizi" (16 field) | Hub guide PDF (le guide sono moduli, §8.8) |
| **2710** | Glossario legale / `glossario-legale` | `/risorse/glossario-legale/` | Gutenberg (rendering specializzato dal tema) | Glossario termini legali |
| **1413** | Blog / `blog` | `/risorse/blog/` | ⚠️ Pagina-contenitore: il suo contenuto NON è mostrato — il blog è generato dal tema. Per gli articoli vai a **Articoli** (vedi §9) | Slot per `/risorse/blog/` (è la "pagina degli articoli" di WP) |
| **23** | Contatti / `contatti` | `/contatti/` | 🔒 SCF "Saltelli — Page Contatti" (hero/mappa/come arrivare/trust, 10 field) | Pagina contatti + modulo (modulo via Contact Form 7) |
| **372** | Lavora con noi / `lavora-con-noi` | `/contatti/lavora-con-noi/` | 🔒 SCF "Saltelli — Page Servizi" (16 field) | Carriere |
| **2714** | Prenota un appuntamento / `prenota-appuntamento` | `/prenota-appuntamento/` | Gutenberg / standard | Pagina prenotazione appuntamento |
| **2741** | Privacy Policy / `privacy-policy` | `/privacy-policy/` | Gutenberg / Iubenda | Privacy policy (gestita da Iubenda — non riscrivere a mano) |
| **2742** | Cookie Policy / `cookie-policy` | `/cookie-policy/` | Gutenberg / Iubenda | Cookie policy (gestita da Iubenda) |
| **2743** | Note legali / `note-legali` | `/note-legali/` | Gutenberg | Note legali |

\* La Page 2811 ha `post_title` = "Chi Siamo" (lo slug è `lo-studio`): è un residuo storico. Sul frontend il titolo della scheda mostra "Chi Siamo" — se vuoi cambiarlo in "Lo Studio" puoi farlo dal campo Titolo della Page 2811 (cosmetico, non urgente). Vedi anche §5.6.

> **Cosa NON trovi più qui** (cestinate Wave 4.7.fix.5, redirect 301 attivi): `lo-studio` top-level (era 2811? no — era una vecchia Page diversa, già cestinata da prima), `risultati` (2699 → ora `/chi-siamo/casi-rappresentativi/` archivio CPT), `conferma` (356, vecchia thank-you Elementor), `prenota-un-appuntamento` (361, doppione di "Prenota un appuntamento" 2714), + 13 vecchie bozze ("Servizi legali", "Lavoro", "Immigrazione", "Diritto societario", "Avvocato Divorzista", ecc.) — tutte mai usate dal sito attuale.

---

## 4. Saltelli — Settings (impostazioni globali)

📍 **Dove**: sidebar WP-Admin, voce **Saltelli — Settings** (icona cogwheel, posizione 60).

> **🆕 v4.0 (Wave 4.7.fix.3 · 2026-05-08)**: 5 tab sono state **rimosse** da qui (Hero Homepage, Studio Section, Team & Casi, Press Homepage, Hub Pages) — il loro content è migrato alle Page WP corrispondenti. Vai a **Pagine** → seleziona la pagina (Home, Chi Siamo, Aree di Pratica, Risorse) → trovi un metabox dedicato "Saltelli — Page X" sotto l'editor con tutti i field di quella pagina.

Sono **9 tab**. Tutto quello che resta qui è **configurazione globale** del sito (header, footer, brand, contatti studio, CTA condivise tra pagine, header archive CPT, testi cluster taxonomy). Per modificare: clicca sul tab → modifica → click "Update" in alto a destra → ricarica una tab del sito pubblico per verificare.

| Tab | Cosa contiene | Pagine impattate |
|---|---|---|
| 1. Studio Info | Indirizzo, email, PEC, P.IVA, telefono, orari | Footer + schede contatti + schema JSON-LD |
| 2. Mappa | Coordinate latitudine/longitudine | Schema LocalBusiness + iframe mappa |
| 3. Brand | Payoff, statement, 4 trust signals, messaggio WhatsApp default | Header + footer + trust bar + sticky WhatsApp |
| 4. Footer | Credit text, newsletter, colophon (indirizzo/orari/contatti) | Footer |
| 5. Social | URL Instagram, Facebook, LinkedIn, X | Footer + schede contatti |
| 6. CTA Defaults | Label, URL, subline italic, trust signal globali | Tutti i bottoni "Prenota" sparsi su 19 templates |
| 7. Footer Aree | 3 aree tier-1 in colonna footer (repeater) | Footer colonna "Aree di pratica" |
| 8. Taxonomy Tipo Area | Eyebrow + sottotitolo template per `/aree-di-pratica/{cluster}/` | 4 pagine cluster (term taxonomy) |
| 9. Archive Headers | Eyebrow / H1 / intro per `/chi-siamo/team/` e `/chi-siamo/casi-rappresentativi/` | 2 archive CPT (no Page WP) |

> **🆕 v5.0**: il tab "Archive Headers" ora ha un **notice informativo** in cima con link diretti ai CPT Avvocato e Casi rappresentativi (per editare i singoli elementi della lista). Vedi §3.6 per workflow archive.

> **Per modificare il contenuto delle pagine specifiche** (Homepage, Chi Siamo, Aree di Pratica, Risorse) vai a **Pagine** → seleziona la pagina → vedi metabox "Saltelli — Page X" (sotto il title, l'editor classico è disabilitato dal v5.0). Vedi §5.

⚠️ **NB importante**: per i campi globali che lasci vuoti, il frontend continua a mostrare il copy default seedato dal JSON. Per "azzerare visivamente" un campo devi metterci una stringa con uno spazio o usare il toggle se previsto.

### Tab 1 — Studio Info

| Campo | Valore attuale | Note editoriali |
|---|---|---|
| Via | Via Vannella Gaetani, 27 | Indirizzo civico |
| CAP + Città | 80121 Napoli | Senza punteggiatura tra CAP e città |
| Quartiere | Chiaia | Mostrato in footer + schede contatti, è anche un signal "geo" forte per AI |
| Orari settimana | Lun – Ven · 10:00 – 19:00 | Usa il separatore "·" e il trattino lungo "–" |
| Orari sabato | Sabato su appuntamento | |
| Telefono pubblico | +39 081 1813 1119 | **Formato E.164 con spazi** — il click-to-call lo normalizza automaticamente |
| Email pubblica | info@studiolegalesaltelli.it | |
| PEC | emilianosaltelli@avvocatinapoli.legalmail.it | |
| P.IVA | 06685101211 | |
| Ordine professionale | Ordine degli Avvocati di Napoli | Compare nello schema JSON-LD della scheda avvocato (Person → memberOf) |

⚠️ **Coerenza NAP** (Name · Address · Phone). Cambiando un dato qui, cambia ovunque (footer, header click-to-call, schema JSON-LD). Mai duplicare l'indirizzo dentro il body di una pagina specifica: se ti serve scrivere "venite a trovarci in via Vannella Gaetani 27", scrivi solo il riferimento testuale, ma i dati strutturati restano qui.

🔍 **Test debug consigliato**: cambia la P.IVA in modo dummy ("123456"), salva, vai sul footer del sito pubblico e verifica che si aggiorni. Poi rimettila corretta.

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
| Brand statement | Un atelier legale italiano. Quattro avvocati a Chiaia. Vent'anni di pratica accanto a famiglie e imprese. | 1–2 frasi, usato in footer e in `/lo-studio/`. Tono editoriale, niente bullet point. |
| Trust signal 1 (label + caption) | "20+ ANNI" / "ESPERIENZA" | Plate trust homepage. Caps lock voluto. |
| Trust signal 2 (label + caption) | "4 AVVOCATI" / "TEAM SPECIALIZZATO" | |
| Trust signal 3 (label + caption) | "17 AREE" / "DI PRATICA" | |
| Trust signal 4 (label + caption) | "COA FAMIGLIA" / "MUNICIPALITÀ 1" | |

💡 Il brand statement attuale è la versione finale concordata. Se proponi una variante, segnala via email per discussione — **non sostituirla unilateralmente**, è un asset di brand identity.

🆕 I 4 trust signal sono stati seedati con i copy ricavati dal brief. Modificabili ma cambia uniformemente in homepage + about.

### Tab 4 — Footer

| Campo | Esempio | Note |
|---|---|---|
| Credit text bottom | Realizzato da Adsolut Web Agency | Riga in basso a destra del footer |
| Credit URL | https://adsolut.it | Link del credit |
| Newsletter footer attiva? | Sì / No | Toggle per mostrare o nascondere il modulo iscrizione |
| Newsletter provider | Brevo / Mailchimp / Custom | Cambia il provider solo se richiesto da Avv. Saltelli |
| Colophon indirizzo | Via Vannella Gaetani, 27\n80121 Napoli — Chiaia | Riga colophon footer. `\n` = a-capo. 🆕 Wave 4.7.fix |
| Colophon orari | Lun – Ven · 10:00 – 19:00\nSolo su appuntamento | Idem. 🆕 |
| Colophon email | (vuoto — fallback a Studio Info → Email pubblica) | Override colophon. Se vuoto usa Studio Info. 🆕 |
| Colophon telefono | +39 081 1813 1119 | 🆕 |

### Tab 5 — Social

URL completi degli account ufficiali. Lascia vuoto se l'account non esiste (l'icona scompare automaticamente).

| Campo | Stato attuale |
|---|---|
| Instagram | https://www.instagram.com/studiolegalesaltelli/ ✅ |
| Facebook | https://www.facebook.com/share/1D1jCY7BnW/ ✅ |
| LinkedIn | (vuoto — da popolare quando lo Studio aprirà la company page) |
| X / Twitter | (vuoto — non in uso) |

🔍 **Task debug per Elena**: chiedere ad Avv. Saltelli se LinkedIn ufficiale dello Studio è disponibile. Se sì, popolarlo qui.

### Tab 6 — CTA Defaults

(Tab 7 Footer Aree, Tab 8 Taxonomy Tipo Area, Tab 9 Archive Headers — vedi le tabelle riassuntive sopra. Sono 3 tab tecniche con field semplici, edit on-demand quando cambi assetto strategico, archive CPT copy o cluster taxonomy strings.)

#### Tab 6 dettaglio — CTA Defaults

I bottoni "Prenota un incontro" che vedi sparsi sul sito hanno **valori di default** che vengono presi da qui. Le singole pagine possono sovrascriverli, ma se non lo fanno usano questi.

| Campo | Esempio attuale |
|---|---|
| CTA default label | Prenota un incontro → |
| CTA default URL | /contatti/ |
| CTA trust signal default | Risposta entro 24 ore · Riservatezza assoluta |
| CTA subline italic | Prima consulenza conoscitiva gratuita |

⚠️ Cambiando questi, cambia il bottone in tante pagine. Verifica navigando 4–5 pagine dopo il salvataggio. **Buon test debug**: modifica il "trust signal" in qualcosa di evidentemente diverso, ricarica 3 pagine diverse e verifica la propagazione.

---

## 5. Le pagine custom

📍 **Dove**: sidebar WP-Admin, voce **Pagine**.

Ogni pagina ha la propria struttura editoriale. Ti elenco la mappa pagina → blocchi modificabili. Tutti i campi sono descritti **dentro WP-Admin** con etichette e istruzioni: questa tabella ti serve come "indice" iniziale.

### 5.0 — 🆕 v5.0: 12 Pages WP con metabox SCF dedicato + Gutenberg disabled

A partire dal **v5.0** (Wave 4.7.fix.4 STRATEGY A FULL SCF), 12 Page WP hanno un metabox SCF dedicato + l'editor di contenuto classico (Gutenberg) **disabilitato**:

| Page WP | URL pubblico | Admin path | Metabox content | Da quale Wave |
|---|---|---|---|---|
| **Home** (ID 17) | `/` | Pagine → Home | Saltelli — Page Homepage (4 tab) — 12 field | v4.0 |
| **Contatti** (ID 23) | `/contatti/` | Pagine → Contatti | Saltelli — Page Contatti (hero + map + come arrivare + trust) — 10 field | v5.0 |
| **Lavora con noi** (ID 372) | `/contatti/lavora-con-noi/` | Pagine → Lavora con noi | Saltelli — Page Servizi (hero + aside + body + CTA finale) — 16 field | v5.0 |
| **Domande Frequenti** (ID 2708) | `/risorse/domande-frequenti/` | Pagine → Domande frequenti | Saltelli — Page Domande Frequenti (hero + TOC + CTA) — 10 field | v5.0 |
| **Guide Gratuite** (ID 2709) | `/risorse/guide-gratuite/` | Pagine → Guide gratuite | Saltelli — Page Servizi (hero + aside + body + CTA finale) — 16 field | v5.0 |
| **Prima Consulenza** (ID 2711) | `/costi-e-consulenze/prima-consulenza/` | Pagine → Prima consulenza | Saltelli — Page Servizi — 16 field | v5.0 |
| **Come Lavoriamo** (ID 2712) | `/costi-e-consulenze/come-lavoriamo/` | Pagine → Come lavoriamo | Saltelli — Page Servizi — 16 field | v5.0 |
| **Richiedi Preventivo** (ID 2713) | `/costi-e-consulenze/richiedi-preventivo/` | Pagine → Richiedi preventivo | Saltelli — Page Servizi — 16 field | v5.0 |
| **Lo Studio** (ID 2811) | `/chi-siamo/lo-studio/` | Pagine → Lo Studio | Saltelli — Page Lo Studio (mission + lineage + faq) | v5.0 |
| **Aree di Pratica** (ID 2812) | `/aree-di-pratica/` | Pagine → Aree di Pratica | Saltelli — Page Aree di Pratica (2 tab) — 10 field | v4.0 |
| **Risorse** (ID 2813) | `/risorse/` | Pagine → Risorse | Saltelli — Page Risorse (1 tab) — 4 field | v4.0 |
| **Chi Siamo** (ID 2822) | `/chi-siamo/` | Pagine → Chi Siamo | Saltelli — Page Chi Siamo (1 tab) — 4 field | v4.0 |

**Cosa è successo in v5.0**: per le 7 Pages aggiunte (le ultime 7 + Lo Studio), il loro `post_content` Gutenberg era ZOMBIE — presente in DB ma NON renderizzato sul frontend (i template usavano già i campi SCF). Vedevi due "sorgenti di verità" apparenti (l'editor Gutenberg pieno + il metabox SCF popolato) ma in realtà solo il metabox SCF controllava il contenuto. Da v5.0:

1. Il post_content è stato svuotato (backup recoverable in postmeta per safety)
2. L'editor Gutenberg/classic è stato disabilitato su queste 12 Pages
3. Sotto il title vedi solo: un **notice "Modifica il contenuto qui sotto"** + il metabox SCF

**Workflow tipico per editare la pagina /contatti/ (esempio v5.0)**:
1. WP-Admin → Pagine → Contatti
2. Vedi: title "Contatti" + slug "contatti" + notice info "Modifica il contenuto qui sotto" + metabox "Saltelli — Page Contatti"
3. Modifica nei field SCF del metabox (hero, mappa, come arrivare, trust signal)
4. Click "Update" in alto a destra
5. Ricarica `/contatti/` con Ctrl+Shift+R per verificare

**Workflow tipico per modificare l'intro paragrafo di /chi-siamo/ (invariato v4.0+)**:
1. WP-Admin → Pagine → Chi Siamo
2. Sotto title + slug → metabox "Saltelli — Page Chi Siamo" → field "Intro paragrafo"
3. Modifica → "Update"
4. Ricarica `/chi-siamo/`

I metabox hanno **field "instructions"** in italiano per ogni campo. Se non sei sicuro su un field, leggi l'instruction sotto il campo prima di editare.

**Pages NON affette** (mantieni Gutenberg/classic editor):
- Costi e Consulenze (2695) hub — già SCF-driven con CPT children
- Privacy / Cookie / Note legali — pagine puramente testuali
- Blog (1413) — è solo l'index degli articoli
- Glossario legale — template legacy specializzato
- Eventuali pagine future che crei senza metabox SCF attached

### 5.1 — Pagina `/costi/` (ID 2695)

Slogan: trasparenza tariffaria. **6 blocchi modificabili**:

| Blocco | Campi principali |
|---|---|
| **Hero** | Eyebrow, H1 prefix, H1 italic, Lede italic |
| **Aside** (trust box laterale) | Eyebrow, H3, Paragrafo, CTA label, CTA URL |
| **§ 03 — Body editorial** ("Come calcoliamo") | Body editoriale completo (TinyMCE) — è il punto in cui spieghi come si calcola un preventivo |
| **CTA finale** | Eyebrow, H2, Paragrafo, Bottone label, Bottone URL, Trust line |

I blocchi **Modalità di consulenza** (3) e **Scenari costi** (3) e **Trust signals** (4) **non si modificano qui** — sono moduli separati: vedi sezione §8.

### 5.2 — Casi rappresentativi → `/chi-siamo/casi-rappresentativi/` (archivio CPT, NON una Page)

> **🆕 v6.0**: la vecchia Page WP `/casi/` (ID 2699) è stata **cestinata** in Wave 4.7.fix.5 (era un residuo: il suo URL reale `/chi-siamo/lo-studio/risultati/` era orfano, e il contenuto era già superato). I casi rappresentativi vivono ora **solo** nell'archivio CPT `/chi-siamo/casi-rappresentativi/`:
> - **Header pagina** (eyebrow / H1 / intro): Saltelli — Settings → tab "Archive Headers". Vedi §3.6.
> - **I singoli casi**: sidebar **Casi rappresentativi** (CPT). Vedi §8.2.
>
> Il vecchio URL `/casi/` (e `/chi-siamo/risultati/`) ridireziona 301 a `/chi-siamo/casi-rappresentativi/`.

### 5.3 — Pagina `/contatti/` (ID 23)

Hero + mappa + come arrivare + trust signal:

| Blocco | Campi |
|---|---|
| **Hero** | Eyebrow, H1 prefix, H1 italic, Lede italic |
| **Mappa** | Embed iframe (Google Maps o OpenStreetMap), Caption sotto |
| **Come arrivare** | Titolo, Indicazioni metro/bus, Indicazioni parcheggi |
| **Trust signal** | Riga reassurance sotto il form (privacy / orari risposta) |

💡 Il modulo di contatto è gestito da Contact Form 7 (sidebar **Contatti** → Form). Per modificare i campi del form, scrivi al tecnico (è raro che serva).

🔍 **Test debug consigliato**: invia un test al modulo di contatto da una email diversa dalla tua aziendale, verifica che arrivi a info@studiolegalesaltelli.it E che NON finisca in spam.

### 5.4 — Pagina `/faq/` (ID 2705)

Hero + TOC title + CTA finale. **Le 28 FAQ in pagina sono moduli separati** organizzati per topic (§8.1).

### 5.5 — Le 5 pagine "info-shared" (layout standard)

Stesso layout per tutte e cinque, **stessi 16 campi**:

| Pagina | ID | Tema |
|---|---|---|
| `/come-lavoriamo/` | 2709 | Workflow dello studio |
| `/prima-consulenza/` | 2708 | Cosa aspettarsi alla prima consulenza |
| `/lavora-con-noi/` | 372 | Carriere |
| `/richiedi-preventivo/` | 2710 | Form di preventivo |
| `/guide-gratuite/` | 2706 | Hub delle guide PDF |

I 16 campi sono raggruppati in: **Hero · Aside trust box · Body editorial (TinyMCE con drop-cap automatico sul primo paragrafo) · CTA finale**. Pattern coerente cross-page.

### 5.6 — `/lo-studio/` (chi-siamo)

Pagina importante, **NON ha ACF Field Group**: il contenuto sta in `template-parts/page-chi-siamo.php` ed è disegnato in modo specifico dal team tecnico. I principi mostrati nella sezione "I nostri principi" sono moduli separati (§8 → Principi studio).

> **Per la chi-siamo, il copy editoriale principale è ancora hardcoded nel template.** Se serve modificare il body, scrivi al tecnico e specifica il paragrafo da cambiare. Stiamo valutando in Wave 5 se aprire anche questa via ACF.

---

## 6. Le 4 schede avvocato

📍 **Dove**: sidebar WP-Admin, voce **Avvocati**.

Quattro profili (Emiliano Saltelli #2660, Fabiana Saltelli #2661, Antonia Battista #2662, Stefano Gaetano Tedesco #2663). Ogni profilo è una scheda con i seguenti campi:

| Campo | Tipo | Note |
|---|---|---|
| **Hero · Ruolo** | Testo breve | Es. "Founding Partner · Tributarista". Mostrato sotto il nome nell'hero della scheda. |
| **Specializzazioni (max 5)** | Textarea, **una per riga** | Es.<br>`Diritto tributario`<br>`Cassazione tributaria`<br>`Cartelle e ricorsi NOV` |
| **Bio breve (1 riga lede)** | Testo, max 300 caratteri | Usata nell'archivio avvocati e nello schema description |
| **Bio estesa** | TinyMCE wysiwyg | Bio professionale completa, mostrata nella pagina del profilo. **VEDI §6.1 per nota importante** |
| **Foto ritratto (3:4)** | Immagine | **Verticale**, minimo 600×800px, JPG ottimizzato. Vedi §14. |
| **Email pubblica** | Email | Lasciare vuoto se l'avvocato non vuole esporre l'email personale |
| **Telefono diretto** | Testo (formato E.164) | Es. `+390811813119` |
| **WhatsApp** | Testo (formato E.164) | Es. `+393517138006` — abilita il bottone WhatsApp |
| **LinkedIn URL** | URL | Profilo personale (alimenta `sameAs` nello schema JSON-LD) |
| **Aree di competenza correlate** | Selezione multipla | Le aree di pratica di cui questo avvocato si occupa. Si selezionano dal CPT Competenze (§7) |
| **Formazione & Titoli** | Selezione multipla | Crea le voci in **Avvocati → Formazione & Titoli** (§8.7), poi selezionale qui in ordine cronologico |
| **Casi rappresentativi** | Selezione multipla | Crea le voci in **Casi rappresentativi** (§8.2), poi selezionale qui |

### 6.1 — ⚠️ Stato attuale delle bio estese (DEBT EDITORIALE da risolvere)

Durante l'audit del 2026-05-05 abbiamo rilevato che **3 bio su 4 sono in un campo "legacy"** (post_content del vecchio editor classico), non nel nuovo campo ACF "Bio estesa". Ecco lo stato:

| Avvocato | Bio estesa ACF | Bio nel post_content legacy |
|---|---|---|
| Antonia Battista | ✅ 1075 caratteri | 1052 caratteri |
| Emiliano Saltelli | ❌ vuota | 968 caratteri |
| Fabiana Saltelli | ❌ vuota | 831 caratteri |
| Stefano Tedesco | ❌ vuota | 596 caratteri |

**Sul sito pubblico le bio appaiono comunque** perché c'è un fallback automatico nel template: se il campo ACF "Bio estesa" è vuoto, mostra il post_content. Quindi nessun problema visibile.

**Ma c'è un problema operativo per te**: quando proverai a editare la bio di Emiliano, Fabiana o Stefano dal nuovo campo ACF "Bio estesa", troverai il campo **vuoto**, e dovrai capire che la bio attuale è altrove.

**Cosa fare** (scegli uno dei due approcci):

**Opzione A — Migra le bio nel campo ACF (consigliata, allinea modello dati)**

1. Apri la scheda avvocato (es. Emiliano Saltelli)
2. Nell'editor classico (sotto i field ACF) trovi la bio attuale. Selezionala tutta, copia.
3. Vai al campo ACF "Bio estesa" e incollala lì
4. Salva
5. Verifica sulla pagina pubblica `/avvocati/emiliano-saltelli/` che la bio sia ancora visibile e identica
6. Cancella il contenuto dall'editor classico (rimane vuoto)
7. Salva di nuovo

Tempo: ~5 minuti per avvocato, ×3 = 15 minuti totali.

**Opzione B — Edita le bio dall'editor classico (rapida ma temporanea)**

Modifichi direttamente il body legacy. Funziona ma il modello dati resta misto. Sconsigliato — meglio migrare.

🔍 **Task debug consigliato per Elena**: completare l'opzione A per i 3 avvocati durante la fase di test. È un'attività editoriale che ti permette anche di rivedere/migliorare le bio se serve.

### 6.2 — Tre cose da NON dimenticare per gli avvocati

1. **Ordine specializzazioni conta.** La prima riga è la specializzazione "primaria" (mostrata nell'hero più grande).
2. **Foto Emiliano Saltelli** (allegato `_thumbnail_id=2683`). È stata configurata e validata. **Non sostituirla** senza autorizzazione di Avv. Saltelli.
3. **Bio estesa**: mantieni il tono editoriale delle bio già pubblicate (cura, sobrietà, niente "siamo i migliori di Napoli"). Vedi §11.

### 6.3 — Esempio di bio ben fatta (Antonia Battista, attuale)

Per riferimento di tono e struttura, la bio di Antonia Battista è quella più completa e allineata al brand. Quando scriverai/migrerai le altre, prendi quella come modello: 3-4 paragrafi, prima persona dello Studio o terza neutra, focus sulla competenza e sui risultati concreti, niente auto-celebrazione.

---

## 7. Le 19 aree di pratica (Competenze)

📍 **Dove**: sidebar WP-Admin, voce **Aree di pratica**.

Sono **19 aree** divise in:

- **3 aree Tier-1 (deep cluster)**: Diritto tributario · Diritto del lavoro · Diritto di famiglia LGBTQ+
- **16 aree Tier-2 (lighter)**: tutte le altre

Le aree Tier-1 sono **deep**: 1500–2500 parole, FAQ, casi, avvocati referenti, articoli correlati. Le Tier-2 sono **lighter**: titolo + answer capsule + 100–300 parole + lista avvocati.

### 7.1 — Mappa ID delle competenze (per riferimento rapido)

| ID | Slug | Area |
|---|---|---|
| **2664** | diritto-tributario | **Tier-1** |
| **2665** | diritto-del-lavoro | **Tier-1** |
| **2666** | diritto-di-famiglia-lgbtq | **Tier-1** |
| 2667 | cartelle-esattoriali-e-multe | Tier-2 |
| 2668 | recupero-crediti | Tier-2 |
| 2669 | diritto-di-famiglia | Tier-2 |
| 2670 | responsabilita-medica | Tier-2 |
| 2671 | diritto-bancario | Tier-2 |
| 2672 | diritto-condominiale | Tier-2 |
| 2673 | diritto-dellimmigrazione | Tier-2 |
| 2674 | diritto-penale | Tier-2 |
| 2675 | diritto-previdenziale | Tier-2 |
| 2676 | diritto-delle-assicurazioni | Tier-2 |
| 2677 | diritto-delle-successioni | Tier-2 |
| 2678 | risarcimento-danni | Tier-2 |
| 2679 | responsabilita-civile | Tier-2 |
| 2680 | domiciliazione-dimpresa | Tier-2 |
| 2681 | consulenze-online | Tier-2 |
| 2682 | diritto-amministrativo | Tier-2 |
| 2705 | diritto-societario | Tier-2 |
| 2706 | contrattualistica | Tier-2 |
| 2707 | ricorsi | Tier-2 |

### 7.2 — Campi per area di pratica

| Campo | Tipo | Note |
|---|---|---|
| **Tier-1 deep cluster** | Toggle on/off | **Attiva SOLO per le 3 aree confermate Tier-1.** Cambia il rendering da pagina lighter a pagina deep |
| **Tier label** | Testo | Es. "Tier 1 · Approfondimento". Eyebrow nell'hero |
| **Sottotitolo H1** | Testo | Sottotitolo editorial sotto l'H1 (es. "Licenziamenti, mobbing, INPS.") |
| **Answer capsule (GEO)** | Textarea, 50–60 parole | Risposta diretta alla query target. **Letta direttamente dalle AI**. Vedi §11 e §12 per le regole di scrittura GEO |
| **Body editorial completo** | TinyMCE wysiwyg | Per Tier-1: 1500–2500 parole strutturate H2/H3. Per Tier-2: testo breve o vuoto |
| **Avvocati referenti** | Selezione multipla | Chi presidia l'area. Si visualizza in fondo alla pagina |
| **Casi rappresentativi** | Selezione multipla | Casi anonimizzati relativi all'area (§8.2) |
| **Domande frequenti** | Selezione multipla | 3–5 FAQ. Schema FAQPage iniettato automaticamente |
| **Articoli correlati** | Selezione multipla | Post del blog clusterizzati su questa area (rafforza topical authority) |
| **Testo CTA / URL CTA** | Testo / URL | Sovrascrive il CTA default solo se necessario |

### 7.3 — ⚠️ Answer capsule: NON modificare quelle esistenti senza coordinarsi

Le 3 answer capsule delle aree Tier-1 sono state **scritte e ottimizzate manualmente per le query AI**. Sono protette dal sistema di migrazione (Wave 2 le ha skippate per non sovrascriverle). Se devi cambiarle, **fallo direttamente da WP-Admin** e tieni la nuova versione **a 50–60 parole**, in stile Q&A diretto.

### 7.4 — Aggiungere un'area di pratica (raro)

Se Avv. Saltelli decide di aggiungere una 20a area:

1. **Aree di pratica → New**
2. Compila titolo + slug
3. Lascia il toggle Tier-1 **off** (sarà tier-2)
4. Compila answer capsule + body
5. Associa avvocati referenti
6. Pubblica
7. Avverti il tecnico per aggiornare la sitemap del menu (l'aggiunta automatica è in Wave 5)

---

## 8. I moduli riutilizzabili (FAQ, Casi, Principi…)

WordPress ti dà 8 voci nella sidebar che chiamiamo **moduli riutilizzabili**. Sono "elementi atomici" che vengono aggregati nelle pagine. Ti elenco ognuno con il suo uso reale.

### 8.1 — FAQ (28 voci attive)

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

#### Esempio di FAQ ben fatta

> **Titolo (domanda):** Posso ricorrere contro una cartella esattoriale già notificata?
>
> **Risposta:** Sì, hai 60 giorni dalla notifica per presentare ricorso davanti alla Commissione Tributaria Provinciale. Se i 60 giorni sono già scaduti, in alcuni casi è possibile chiedere la rimessione in termini per causa di forza maggiore (es. notifica viziata o irreperibilità giustificata). Lo Studio valuta il caso in prima consulenza gratuita e ti dice subito se il ricorso è ancora possibile.

Tono: diretto, dà la risposta + specifica i casi limite + chiude con il next step. ~80 parole.

#### Esempio di FAQ NON ben fatta

> ❌ **Titolo:** Cosa fare in caso di cartelle esattoriali?
>
> ❌ **Risposta:** Le cartelle esattoriali sono uno strumento utilizzato dall'Agenzia delle Entrate per riscuotere i tributi non pagati. Ogni situazione è diversa e richiede un'analisi approfondita da parte di un professionista esperto. Il nostro studio offre una vasta gamma di servizi per assistervi al meglio. Contattaci per saperne di più.

Perché è male: titolo vago (non corrisponde a una query reale), risposta generica (non risponde alla domanda), chiusura promozionale (no concreto, no next step).

### 8.2 — Casi rappresentativi (9 voci attive)

📍 **Sidebar → Casi rappresentativi**

Casi vinti **anonimizzati**. Ogni caso:

| Campo | Esempio |
|---|---|
| **Titolo** | "Tribunale Famiglia · 2024" (NO nomi clienti) |
| **Categoria (taxonomy)** | `privati` / `imprese` / `contenzioso` / `altri` |
| **ID label** | Es. "Caso T-04" o "TAR Campania · 2023" |
| **Descrizione anonimizzata** | Textarea, max 250 caratteri. Niente nomi, niente cifre identificative. |
| **Outcome label** | Es. "Vittoria · Risarcimento" o "TARI annullata · 4.500 €" |

⚠️ **Anonimato**. Mai inserire:
- Nome cliente
- Importo esatto identificativo (un importo "tondo" come 50.000 € va bene; un importo specifico come 47.832,15 € può essere identificativo)
- Riferimento a procedimenti pendenti
- Nomi di controparti, imprese, persone fisiche

In dubbio, chiedi a Avv. Saltelli prima di pubblicare.

#### Esempio di caso ben fatto

> **Titolo:** Tribunale di Napoli · 2022
> **Categoria:** Contenzioso
> **ID label:** Recupero crediti
> **Descrizione:** Recupero giudiziale di credito commerciale per fornitura non saldata da parte di società in difficoltà. Procedura monitoria seguita da pignoramento presso terzi.
> **Outcome:** €156.000 · Recupero

#### Esempio di caso NON ben fatto

> ❌ **Titolo:** Vittoria contro l'Azienda XYZ S.p.A.
> ❌ **Descrizione:** Abbiamo recuperato 47.832 € per il nostro cliente Mario Rossi grazie alla competenza del nostro studio. Una grande vittoria!

Identifica nome cliente, controparte, importo non rotondo. Tono auto-celebrativo.

### 8.3 — Modalità consulenza (3 voci)

📍 **Sidebar → Modalità consulenza** — Mostrate sulla pagina `/costi/` § 01.

Sono i 3 modi in cui lo Studio offre consulenza (es. "In presenza · Chiaia", "Videoconsulto", "Pareri scritti"). Ogni voce: numero, titolo, body, trust mini.

### 8.4 — Scenari costi (3 voci)

📍 **Sidebar → Scenari costi** — Mostrate sulla pagina `/costi/` § 02.

Sono i 3 scenari tipo (es. "Consulenza singola", "Pratica completa", "Assistenza continuativa"). Ogni voce: numero, titolo, body, trust mini.

### 8.5 — Principi studio (3 voci)

📍 **Sidebar → Principi studio** — Mostrate su `/lo-studio/` § 04.

Sono i valori dello studio. Ogni voce: numero, titolo, descrizione.

### 8.6 — Trust signals (4 voci)

📍 **Sidebar → Trust signals** — Mostrate sulla pagina `/costi/` § 05.

Numeri/credibilità (es. "Anni di esperienza", "Casi gestiti", "Settori coperti"). Ogni voce: label + valore.

### 8.7 — Formazione & Titoli (12 voci)

📍 **Sidebar → Avvocati → Formazione & Titoli**

Voci di formazione e titoli professionali degli avvocati. Ogni voce: anno, titolo/qualifica, ente/istituzione.

**Workflow**: prima crei la voce qui, poi la **selezioni nella scheda dell'avvocato** (§6, campo "Formazione & Titoli"). Le voci sono ordinate cronologicamente nella pagina avvocato.

#### Esempio voce formazione

> **Anno:** 2008
> **Titolo/qualifica:** Abilitazione alla professione forense
> **Ente:** Corte d'Appello di Napoli

### 8.8 — Guide gratuite (0 voci attualmente)

📍 **Sidebar → Guide gratuite**

Hub delle guide PDF. Pubblico (gli URL `/guide-gratuite/{slug}/` sono indicizzabili). Ogni voce:

| Campo | Note |
|---|---|
| **Titolo** | Titolo della guida (es. "Cartelle esattoriali: come ricorrere senza errori") |
| **Categoria (taxonomy)** | `tributario` / `lavoro` / `lgbtq` / `procedurale` |
| **Intro / Abstract** | Textarea, 80–120 parole. È quello che si vede in `/guide-gratuite/` come anteprima |
| **PDF file** | Caricamento file PDF (vedi §14) |
| **Formato** | Es. "PDF · 12 pagine · 2 MB" |
| **CTA download label** | Es. "Scarica la guida →" |

⚠️ **Le guide saranno create da Elena post-launch.** Nel form il caricamento PDF ha un limite di 8 MB per file (impostazione attuale). Se serve un PDF più pesante, ottimizzalo con Acrobat (Riduci dimensioni file).

🔍 **Task debug per Elena**: caricare almeno 1 guida di test (anche un PDF dummy) per validare il flusso end-to-end (creazione → upload → download dal frontend).

---

## 9. Il blog

📍 **Sidebar → Articoli**

326 post storici migrati dal vecchio sito + nuovi articoli editoriali. URL pubblico: `/risorse/blog/` (l'archivio) e `/risorse/blog/{slug-articolo}/` (il singolo).

> **🆕 v6.0 — leggi questo se il blog ti sembra "un fantasma":** il blog è **100% WordPress standard**. NON ha un metabox "Saltelli — Page X" come le altre pagine del sito. Tutto si fa dall'**editor Gutenberg** (il corpo dell'articolo) + la **sidebar destra "Documento"** (titolo, categoria, estratto, immagine in evidenza, autore). Niente di esotico — è il WordPress "classico" di sempre. Quando apri un Articolo vedi anche un **box promemoria in cima** che ricorda dove finiscono i campi della sidebar (e quando apri la Page "Blog" ID 1413 un avviso che ti dice che il suo contenuto non è usato — gli articoli si gestiscono da **Articoli**).

### Creare un nuovo articolo — in breve

1. **Articoli → Aggiungi nuovo**
2. **Titolo** → diventa l'`<h1>` dell'articolo (50–70 caratteri ideali; niente sensazionalismo). Non mettere un altro H1 nel corpo.
3. **Corpo** (area Gutenberg) → scrivi con paragrafi e usa **H2/H3 strutturati** per le sezioni: l'indice laterale dell'articolo ("Indice") si genera in automatico da quei titoli, e le AI li usano per estrarre risposte. Il **drop-cap** sul primo paragrafo è automatico — non fare nulla.
4. **Sidebar destra → "Categorie"** → scegline **una** principale (obbligatoria: appare nel breadcrumb e nel meta sopra il titolo). Per i cluster Tier-1 vedi §9.2.
5. **Sidebar destra → "Riassunto"** (a volte etichettato "Estratto") → scrivi **1–2 frasi**: diventano il **lede italico** grande sotto il titolo dell'articolo (e l'anteprima nell'archivio + nei social). ⚠️ Se lo lasci vuoto, sotto il titolo non appare nulla — è il campo più dimenticato.
6. **Sidebar destra → "Immagine in evidenza"** → carica/scegli un'immagine 16:9, min 1200×675, descrittiva (no stock generiche). Praticamente **obbligatoria**: è l'hero del post e la card nell'archivio.
7. **Sidebar destra → "Autore"** → scegli l'avvocato che firma (Antonia Battista, Fabiana Saltelli, Stefano Gaetano Tedesco, Gabriele Cascone). Se il nome dell'utente combacia con una scheda **Avvocato**, sotto il titolo compare anche la sua bio breve + i suoi temi di competenza, e in fondo al post la sua card.
8. **Anteprima** → controlla → **Pubblica**.
9. Verifica: archivio blog `/risorse/blog/` → il nuovo post c'è? E se è legato a un'area di pratica, valuta se aggiungerlo agli "Articoli correlati" della relativa Competenza (§7).

### Mappa: campo della sidebar → cosa diventa sul frontend dell'articolo

| Dove lo metti (admin) | Cosa diventa sul frontend | Editabile? |
|---|---|---|
| **Titolo** (in cima all'editor) | `<h1>` dell'articolo | ✅ Sì |
| **Sidebar → "Riassunto"/"Estratto"** | **Lede italico** grande sotto il titolo (+ anteprima archivio/social) | ✅ Sì — se vuoto, niente lede |
| **Sidebar → "Categorie"** | Categoria nel meta sopra il titolo (maiuscolo) + 3° segmento del breadcrumb | ✅ Sì — scegline una |
| **Sidebar → "Stato e visibilità" → data** | Data mostrata nel meta ("13 LUGLIO 2025") | ✅ Sì |
| **Sidebar → "Autore"** | Firma dell'articolo (+ bio breve + temi competenza, se l'autore combacia con una scheda Avvocato) | ✅ Sì |
| **Sidebar → "Immagine in evidenza"** | Hero del post + card nell'archivio blog | ✅ Sì — praticamente obbligatoria |
| **Corpo (Gutenberg)** | Testo dell'articolo | ✅ Sì |
| **H2/H3 dentro il corpo** | Voci dell'indice laterale "Indice" (auto-generato) | ✅ indirettamente |
| **Box "Yoast SEO"** (sotto l'editor) | `<title>`, meta description, anteprima Google, Open Graph | ✅ Sì |
| **Scheda Avvocato** (sidebar Avvocati → l'avvocato firmatario) | Bio breve + ruolo + ritratto + temi competenza mostrati nell'articolo | ✅ Sì — si edita da **Avvocati**, non dall'articolo (§6) |

### Cosa NON è editabile dal blog (è generato automaticamente dal tema)

- **Tempo di lettura "X MIN"**: calcolato dal numero di parole del corpo (~200 parole/minuto). Non si imposta a mano.
- **Eyebrow "← Editoriale"** (il link "torna al blog" in cima al post): testo fisso nel tema.
- **Header dell'archivio blog** (`/risorse/blog/`): il titolo grande "Editoriale.", il sottotitolo, il blocco newsletter in fondo — tutto fisso nel tema (la Page "Blog" ID 1413 NON controlla questi testi). L'archivio è generato automaticamente: l'articolo più recente diventa il "in evidenza" grande, gli altri vanno nella griglia.
- **Breadcrumb** (Home / Editoriale / Categoria / Titolo): generato automaticamente — cambia categoria o titolo dell'articolo e cambia di conseguenza.
- **Articoli correlati** in fondo al post ("Continua a leggere"): automatici, i 3 articoli più recenti della stessa categoria.
- **Tag**: puoi assegnarli (sidebar → "Tag") ma il template del singolo post **non li mostra** sul frontend (esistono pagine archivio per tag raggiungibili solo via URL diretto). Se ti serve raggruppare articoli in modo visibile, usa le **Categorie**, non i Tag.

> Se vuoi che uno di questi elementi "fissi" diventi modificabile (es. il titolo dell'archivio blog, o un eyebrow alternativo tipo "← Longform" per certi articoli) → è una modifica al tema, scrivi a tech@adsolut.it. Non si fa dal CMS.

### 9.1 — Convenzioni post

- **Titolo**: 50–70 caratteri ideali per SEO. Niente titoli sensazionalistici.
- **H1 = titolo**, NON inserire un H1 nel body.
- **H2/H3 nel body**: usa headers strutturati per facilitare l'estrazione AI.
- **Lunghezza**: 800–2000 parole per pillar content; 400–800 per news/aggiornamenti.
- **Citazioni di leggi**: usa il formato standard "Art. 35 c.p.c." (con spazio).

### 9.2 — Riassociazione blog ai cluster Tier-1 (task debug per Elena)

I 326 articoli storici hanno categorie del vecchio sito. Per rafforzare la **topical authority** sui 3 cluster Tier-1, sarebbe utile:

1. Filtrare articoli per parole chiave (es. cerca "cartella", "IRPEF", "tributario" nel titolo) → assegnali alla categoria *Diritto tributario*
2. Idem per "licenziamento", "mobbing", "INPS" → categoria *Diritto del lavoro*
3. Idem per "unione civile", "stepchild", "famiglia" → categoria *Diritto di famiglia LGBTQ+*

Tempo stimato: ~3-5 ore per i 326 articoli (a batch di 50-100). Non urgente, ma alta priorità per il valore SEO/GEO.

---

## 10. Workflow comuni — guide passo-passo

Questi sono i 7 workflow che farai più spesso. Tienili come riferimento operativo.

### 10.1 — Cambiare il numero di telefono dello Studio

**Scenario**: lo Studio cambia operatore e quindi numero principale.

1. Login WP-Admin
2. Sidebar → **Saltelli — Settings**
3. Tab **Studio Info**
4. Campo **Telefono pubblico** → modifica nel formato E.164 con spazi (es. `+39 081 9999 9999`)
5. **Salva**
6. Verifica:
   - Apri il sito pubblico in incognito
   - Footer: il numero appare aggiornato? ✅
   - Click sul numero (su mobile) → parte chiamata? ✅
   - Header: se c'è bottone telefono, è aggiornato? ✅
7. Se qualcosa non si aggiorna entro 60 secondi: hard reload (`Cmd+Shift+R`), poi se persiste vedi §16

### 10.2 — Aggiungere una nuova FAQ

**Scenario**: un cliente ti ha chiesto "Posso ricorrere se ho già pagato la cartella?" e vuoi aggiungerla alla FAQ.

1. Sidebar → **FAQ → Aggiungi nuovo**
2. **Titolo** (= la domanda): "Posso ricorrere contro una cartella che ho già pagato?"
3. **Topic** (taxonomy laterale): seleziona `tributario`
4. **Risposta** (TinyMCE):
   ```
   Sì, il pagamento della cartella non preclude il ricorso. Hai 60 giorni dalla notifica per impugnare anche se hai già pagato. Se vinci il ricorso, l'Agenzia delle Entrate è obbligata a restituirti l'importo pagato, con gli interessi. Lo Studio valuta gratuitamente la fondatezza del ricorso prima di procedere.
   ```
5. **Priorità ordinamento**: numero che indica la posizione (es. 35 se vuoi che appaia tra la 3a e la 4a delle tributarie esistenti)
6. **Pubblica**
7. Verifica:
   - Pagina `/faq/` → la nuova FAQ è visibile sotto "Tributario"? ✅
   - Pagina `/competenze/diritto-tributario/` → se la vuoi mostrare anche lì, vai sulla scheda Tributario (§7), campo "Domande frequenti" e selezionala

### 10.3 — Aggiornare la bio di un avvocato

**Scenario**: Avv. Fabiana Saltelli ti manda una bio aggiornata con un nuovo titolo.

1. Sidebar → **Avvocati → Fabiana Saltelli**
2. **Se la sua bio è ancora nel post_content legacy** (vedi §6.1):
   - Copia il contenuto dall'editor classico
   - Incollalo nel campo ACF "Bio estesa"
   - Aggiorna nel campo ACF
   - Cancella dal classico
3. **Se la bio è già nel campo ACF "Bio estesa"**:
   - Modifica direttamente lì
4. Aggiorna anche la **Bio breve** se necessario (è il sottotitolo)
5. **Aggiorna**
6. Verifica:
   - Pagina `/avvocati/fabiana-saltelli/` → bio aggiornata? ✅
   - Hard reload se servono cambi visibili

### 10.4 — Aggiungere un caso vinto rappresentativo

**Scenario**: Avv. Saltelli ti dà un caso vinto da pubblicare.

1. **Anonimizzalo prima**:
   - Niente nomi cliente
   - Importi arrotondati (50.000 € va bene; 47.832,15 € no)
   - Niente nomi controparti specifici
2. Sidebar → **Casi rappresentativi → Aggiungi nuovo**
3. **Titolo**: "Tribunale [Sede] · [Anno]" (es. "Tribunale di Napoli · 2024")
4. **Categoria** (taxonomy laterale): `privati` / `imprese` / `contenzioso` / `altri`
5. **Descrizione anonimizzata**: max 250 caratteri, frase complessa è ok
6. **Outcome label**: outcome chiaro (es. "Vittoria · Cartella annullata")
7. **Pubblica**
8. Verifica:
   - Pagina `/casi/` → il nuovo caso appare? ✅
   - Se l'hai associato a una competenza specifica (es. tributario), va anche su `/competenze/diritto-tributario/` (campo "Casi rappresentativi" della scheda area)

### 10.5 — Pubblicare un articolo blog

**Scenario**: Avv. Battista ha scritto un articolo su unioni civili e successioni.

1. Sidebar → **Articoli → Aggiungi nuovo**
2. **Titolo** (50-70 caratteri): "Successioni nelle unioni civili: cosa cambia con la legge Cirinnà"
3. **Body**:
   - Primo paragrafo (drop-cap automatico): apertura editoriale
   - H2 e H3 strutturati per le sezioni
   - Citazioni di legge in formato "Art. 1 Legge 76/2016"
4. **Categoria** (sidebar destra): `Diritto di famiglia LGBTQ+`
5. **Tag**: parole chiave specifiche (es. `unioni civili`, `successioni`, `legge Cirinnà`)
6. **Immagine in evidenza**: 16:9, min 1200×675, descrittiva (no foto stock generiche)
7. **Excerpt** (sidebar destra): riassunto 150-200 caratteri per archivio + social
8. **Anteprima**
9. **Pubblica**
10. Verifica:
    - Archivio blog → il nuovo post appare? ✅
    - Pagina `/competenze/diritto-di-famiglia-lgbtq/` → se la pagina ha "Articoli correlati" associati, controlla se vuoi aggiungere questo nuovo post

### 10.6 — Modificare l'orario di apertura

**Scenario**: lo Studio cambia orario estivo.

1. Sidebar → **Saltelli — Settings**
2. Tab **Studio Info**
3. Campo **Orari settimana**: modifica (formato corretto `Lun – Ven · 10:00 – 19:00` con trattino lungo `–` e separatore `·`)
4. Campo **Orari sabato**: idem se cambia
5. **Salva**
6. Verifica footer: orari aggiornati ✅

### 10.7 — Caricare una guida gratuita PDF

**Scenario**: hai pronta una guida PDF "Cartelle esattoriali 2026".

1. **Pre-upload**:
   - Comprimi il PDF (Acrobat → Riduci dimensioni file). Target ≤ 5 MB
   - Rinomina: `guida-cartelle-esattoriali-2026.pdf`
   - Verifica metadati PDF: Title = nome guida, Author = "Studio Legale Saltelli"
2. Sidebar → **Guide gratuite → Aggiungi nuovo**
3. **Titolo**: "Cartelle esattoriali: come ricorrere senza errori"
4. **Categoria**: `tributario`
5. **Intro / Abstract**: 80-120 parole che spiegano cosa contiene la guida
6. **PDF file**: carica il file
7. **Formato**: "PDF · 12 pagine · 2 MB"
8. **CTA download label**: "Scarica la guida →"
9. **Pubblica**
10. Verifica:
    - Pagina `/guide-gratuite/` → la guida appare nell'hub? ✅
    - Click sul download → il PDF si scarica? ✅
    - Apri il PDF scaricato → è quello giusto, non corrotto? ✅

---

## 11. Convenzioni di scrittura editoriale

Lo studio ha una **voce editoriale** precisa. Rispettarla è la differenza tra un sito legale qualsiasi e Saltelli.

### 11.1 — Tono

- **"Tu" diretto al lettore.** ("Hai ricevuto una cartella?" / "Ti aspettiamo per una prima consulenza".) Niente "Lei" formale.
- **Asciutto, non promozionale.** Scrivi "Lo Studio assiste imprese e professionisti nelle controversie tributarie" — NON "Siamo i migliori avvocati tributari di Napoli con anni di esperienza vincente!".
- **Concreto.** Esempi, scenari, casi. Niente generalità.
- **Misurato.** "Atelier legale italiano" è il nostro registro: cura, sobrietà, attenzione.

### 11.2 — Sigle e nomi propri

| Forma corretta | Forma sbagliata |
|---|---|
| INPS, IRPEF, IMU, RC, TARI, NOV, IRES, IRAP, IVA | inps, Irpef, Imu, rc, tari, nov |
| LGBTQ+ | lgbtq+, Lgbtq+ |
| Napoli, Cassazione, Federico II | napoli, cassazione, federico ii |
| Art. 35 c.p.c. | art 35 cpc, articolo 35 cpc |
| Legge 76/2016 (Cirinnà) | l. 76/2016, legge n.76 del 2016 |
| Commissione Tributaria Provinciale (CTP) | ctp, c.t.p. |

### 11.3 — Punteggiatura editoriale

- Trattino lungo `—` per inserzioni (NON il singolo `-` né `–`)
- Separatore `·` (mediopunto) per sequenze brevi (es. "Diritto tributario · Cassazione · Cartelle")
- Virgolette curve `"..."` o `«...»` (NON `"..."` dritte)
- Niente puntini di sospensione `...` se non in citazioni — Saltelli scrive frasi complete

### 11.4 — Eyebrow (riga sopra l'H1)

Format standard: `§ Topic · Subtopic`. Esempi:
- `§ Servizio · Costi`
- `§ Approfondimento · Tier 1`
- `§ FAQ · Diritto del lavoro`

### 11.5 — Lede italic (sottotitolo serif)

15–35 parole, **complemento all'H1**, NON ripetizione. Stile editoriale: "Quando una cartella arriva e i tempi stringono, conta la mossa giusta — non la fretta."

### 11.6 — Answer capsule (GEO) — guida operativa

Per le **3 aree Tier-1**, le answer capsule sono il punto di estraibilità AI. Regole:

1. **50–60 parole** (limite stretto). Le AI tagliano oltre.
2. **Risposta diretta alla query target**, in prima persona dello Studio o terza neutra.
3. **Una sola idea** per capsule. NON liste, NON elenchi.
4. **Apri con l'attività concreta**, NON con introduzioni vaghe.
5. **Includi geo-signal**: "a Napoli", "in Campania" se rilevante.
6. **Includi competenza-signal**: nome dell'area, sub-aree, fora di intervento.
7. **Chiudi con un trust signal o next step** (gratuita, conoscitiva, ecc.).

#### Buona vs cattiva — esempio tributario

✅ **Buona** (~58 parole):
> "Lo Studio Legale Saltelli & Partners assiste a Napoli imprese e privati in controversie tributarie: ricorsi contro cartelle esattoriali, accertamenti, IRPEF, IMU, IVA. Operiamo davanti a Commissioni Tributarie Provinciali e Regionali, Cassazione tributaria. Vent'anni di esperienza in contenzioso fiscale, anche di rilievo (cartelle annullate fino a €240.000). Prima consulenza conoscitiva gratuita."

Perché funziona: apre con l'attività concreta + chi serve + chi opera + dove + come + un dato di esperienza + chiusura con trust signal.

❌ **Cattiva** (~62 parole, ma vuota):
> "Siamo lo Studio Legale Saltelli, leader a Napoli da oltre vent'anni. Offriamo una vasta gamma di servizi nel diritto tributario, lavoro, famiglia e tanto altro. La nostra missione è quella di essere al vostro fianco con professionalità e dedizione. Contattaci per saperne di più sui nostri eccellenti servizi e per fissare un appuntamento conoscitivo."

Perché è male: tono auto-celebrativo, "vasta gamma" / "eccellenti servizi" sono parole vuote, niente specificità (quali tributi? quali fora?), niente geo + competenza signal, chiusura promozionale.

---

## 12. Cosa è già ottimizzato per le AI (GEO)

Il sito Saltelli punta a essere **citato** dalle AI come ChatGPT, Perplexity, Google AI Overviews, Claude. Questo si chiama **GEO — Generative Engine Optimization**. Ti elenco cosa è già fatto, così sai dove la tua scrittura ha effetto su questo asse.

### 12.1 — Cosa è già implementato

| Elemento | Cosa fa per le AI |
|---|---|
| **Schema JSON-LD `Organization`** | Le AI sanno che esiste l'entità "Studio Legale Saltelli & Partners", con sede a Napoli, P.IVA, ecc. |
| **Schema JSON-LD `LocalBusiness` + `LegalService`** | Le AI capiscono che siamo un servizio legale, in posizione geografica precisa (lat/lng) |
| **Schema JSON-LD `Person` (Attorney)** su ogni avvocato | Le AI riconoscono i 4 avvocati come professionisti specifici, con competenze, formazione |
| **Schema JSON-LD `FAQPage`** | Sulle pagine FAQ + sulle aree Tier-1 con FAQ associate |
| **Schema JSON-LD `Service`** | Sulle aree di pratica |
| **`/llms.txt`** | File pubblico dedicato alle AI: spiega cosa è il sito e quali contenuti devono leggere |
| **`/robots.txt` con allow esplicito ai bot AI** | GPTBot, ClaudeBot, PerplexityBot, GoogleOther, ChatGPT-User |
| **`/sitemap.xml`** | Indicizzabilità completa per crawler |
| **Eyebrow standardizzati `§ Topic · Subtopic`** | Aiuta le AI a contestualizzare la pagina |
| **Answer capsule sulle 3 aree Tier-1** | Risposta diretta che le AI possono estrarre quando un utente chiede "avvocato tributarista a Napoli" |
| **H1/H2/H3 strutturati nei body** | Permette estrazione semantica delle sezioni |
| **Drop-cap automatico** | Marker editoriale chiaro |

### 12.2 — Cosa puoi fare TU per amplificare l'efficacia GEO

| Cosa | Come |
|---|---|
| **Scrivere FAQ specifiche, non generiche** | Una FAQ "Posso ricorrere contro una cartella esattoriale dopo 60 giorni?" è infinitamente più estraibile di "Cosa fare con le cartelle?" |
| **Mettere in apertura del body editorial la risposta concreta** | Le AI estraggono spesso i primi 200-300 caratteri di un body. Se inizi vago, hai sprecato il primo paragrafo. |
| **Usare nomi propri di leggi, sigle, competenze** | "Legge 76/2016", "INPS", "TAR Campania" sono ancore semantiche forti. Lo "scrivere chiaro" coincide con lo "scrivere AI-friendly". |
| **Citare i numeri concreti** | "Annullamenti di cartelle fino a €240.000" è più estraibile di "casi importanti". |
| **Dire dove operiamo** | "Commissione Tributaria Provinciale di Napoli" è meglio di "fora locali". Le AI capiscono geografia e gerarchia. |

### 12.3 — Come testare se le AI ci trovano

Test pratici (fai una volta a settimana durante il debug):

1. **ChatGPT** (modalità web): "Avvocato tributarista a Napoli specializzato in cartelle esattoriali" → vediamo se ci cita
2. **Perplexity**: "Studio legale di famiglia LGBTQ+ a Napoli" → idem
3. **Google AI Overviews** (cerca su google.com): "ricorso licenziamento Napoli" → vediamo se compariamo nel box AI
4. **Claude** (claude.ai): "Studio Legale Saltelli" → vediamo se ci conosce

Risultati attesi nelle prime settimane post-deploy: zero o pochi pickup. Le AI hanno ritardi di indicizzazione di settimane/mesi. Questo è normale. Quando inizieremo a vedere citazioni, è perché il sito ha **earned trust** dalle AI.

---

## 13. Cosa NON toccare mai

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
| **Slug delle pagine pubblicate** | Cambiare `/avvocati/emiliano-saltelli/` → `/team/emiliano/` rompe i backlink + l'indicizzazione |

> **🆕 v6.0**: per il ruolo `editor` (Elena) il **Customizer** (Aspetto → Personalizza) e **"CSS aggiuntivo"** sono ora **bloccati a livello di sistema** — la voce non appare nel menu, e se ci provi direttamente vedi un messaggio "Non hai i permessi… contatta l'amministratore (tech@adsolut.it)". Non è un bug: è la rete di sicurezza per non rompere accidentalmente il design. Per modifiche di design/CSS scrivi al tecnico. (Gli amministratori — Adsolut, Avv. Saltelli — continuano ad avere accesso normale.)

### Se hai dubbi → ferma le mani

Se durante un edit vedi un'opzione che **non è descritta in questo manuale** e ti sembra "interessante", **NON cliccarla**. Apri ticket a `tech@adsolut.it` e chiedi.

---

## 14. Upload immagini e PDF

### 14.1 — Immagini

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

### 14.2 — PDF (per le guide gratuite)

**Limite peso**: 8 MB per file.

**Pre-upload**:

1. ✅ **Comprimi** con Acrobat → File → Riduci dimensioni file → Standard. Target ≤ 5 MB.
2. ✅ **Rinomina** descrittivamente: `guida-cartelle-esattoriali-2026.pdf`.
3. ✅ **Verifica metadati**: nel PDF Properties → Title, Author, Subject. Title è il nome che le AI vedono. Author = "Studio Legale Saltelli".
4. ✅ **Apri il PDF dopo l'upload** dal link pubblico per verificare che si scarichi correttamente.

---

## 15. Errori comuni e come evitarli

I 10 errori che vediamo più spesso quando il team editoriale è nuovo a un CMS custom.

| # | Errore | Conseguenza | Come evitarlo |
|---|---|---|---|
| 1 | Modificare l'indirizzo nel body di una pagina invece che in Saltelli — Settings | NAP incoerente, schema JSON-LD sbagliato, AI confuse | Indirizzo SOLO in Settings § Studio Info |
| 2 | Caricare immagini con nome file `IMG_4421.jpg` | Penalità SEO, accessibilità ridotta | Rinominare prima dell'upload + aggiungere alt text |
| 3 | Scrivere FAQ generiche ("Cosa fare con le cartelle?") | Inutili per SEO/AI, nessun pickup | Trasformarle in domande specifiche ("Posso ricorrere contro una cartella già pagata?") |
| 4 | Pubblicare un caso con nome cliente/importo specifico | Violazione privacy + segreto professionale | Anonimizzare sempre. Importi rotondi. Niente nomi controparti. |
| 5 | Cancellare un articolo blog che ha backlink | Perdita di link equity SEO | Mai cancellare: nascondere (status `Bozza`) o redirezionare via 301 (chiedere al tecnico) |
| 6 | Usare l'editor classico per inserire HTML custom (`<div>`, `<style>`) | Rompe drop-cap, rendering mobile, schema | Stare nei campi ACF strutturati |
| 7 | Aggiungere un nuovo CTA copiando codice da pagina a pagina | Brand inconsistency | Modificare CTA Defaults in Settings → propaga ovunque |
| 8 | Salvare e non verificare in incognito | Vedere ancora la cache vecchia, panico | Verificare sempre in modalità incognito o hard reload |
| 9 | Pubblicare senza compilare l'excerpt (riassunto) | Snippet social brutti, archivio blog vuoto | Excerpt 150-200 caratteri, sempre |
| 10 | Cambiare lo slug di una pagina già indicizzata | 404 sui backlink esistenti | Slug = MAI cambiare dopo la prima settimana di indicizzazione |

---

## 16. Anteprima, cache, troubleshooting

### 16.1 — Anteprima vs live

- **Anteprima** (bottone "Anteprima" in alto a destra durante l'edit): mostra come appare la pagina con le tue modifiche in corso, **anche se non hai ancora salvato/pubblicato**. Privata, non indicizzata.
- **Live**: per vederla, clicca **Aggiorna**, attendi 5–10 secondi, ricarica la pagina pubblica con `Cmd+Shift+R` (Mac) o `Ctrl+Shift+R` (Win).

### 16.2 — "Ho modificato ma non vedo il cambiamento"

In ordine, prova:

1. **Hard reload** (`Cmd+Shift+R` / `Ctrl+Shift+R`) sul browser.
2. **Apri in modalità incognito** — esclude cache browser.
3. **Aspetta 60 secondi** — la cache server scade automaticamente.
4. **Logout/Login** in WP-Admin — talvolta la sessione tiene una versione vecchia.
5. Se dopo 2 minuti ancora niente → ticket a `tech@adsolut.it` con: URL pagina, cosa hai cambiato, screenshot del browser.

### 16.3 — "Vedo errore PHP / pagina bianca / 'There has been a critical error on this website'"

🚨 **STOP.** Non riprovare a salvare. Apri ticket immediato a `tech@adsolut.it` con:

- URL della pagina che dà errore
- Cosa stavi facendo (ultima azione prima dell'errore)
- Orario esatto

Il tecnico ha **backup automatici** del sito, può ripristinare in 5 minuti.

### 16.4 — "Il bottone CTA non funziona / link rotto"

1. Vai sulla scheda del field e verifica che il campo URL sia compilato.
2. Verifica formato URL: `/contatti/` (con slash iniziale e finale) oppure `https://...` (URL completo per link esterni).
3. **Mai** mettere `contatti.html` o spazi nell'URL.

### 16.5 — "L'immagine appare distorta o sgranata"

- Controlla le dimensioni minime (§14).
- Caricala in alta risoluzione (almeno 2× la dimensione di rendering).
- Se persiste, mandala al tecnico — potrebbe servire ricreare le miniature WP.

### 16.6 — "Il TinyMCE editor è bloccato / mi mangia la formattazione"

Quirk noto di WordPress. Workaround:

- Salva un draft, ricarica la pagina di edit
- Cambia tab "Visuale" / "Testo" e torna su "Visuale"
- Se persiste: usa l'editor "Testo" (HTML grezzo) — ma solo se sei a tuo agio con HTML basic

---

## 17. Come segnalare bug e problemi durante il debug

La fase di test è il momento per scovare problemi. Hai **due canali**:

### 17.1 — Canale Bug critici (sito offline, errore PHP, content sparito)

📱 **Slack/WhatsApp diretto a Duccio Santoro** + email a tech@adsolut.it. **Subito**, anche fuori orario.

Cosa includere:
- URL della pagina rotta
- Screenshot dell'errore
- Cosa stavi facendo prima dell'errore (ultimi 2-3 click)
- Orario esatto

### 17.2 — Canale Bug medi e suggerimenti (link rotti, immagini sbagliate, copy)

📧 **Email batch a tech@adsolut.it ogni 1-2 giorni**. Non urgente.

**Format consigliato del report (template)**:

```
Oggetto: [QA Saltelli] Report 2026-05-XX — N segnalazioni

1. [PAGINA] /competenze/diritto-tributario/
   [TIPO] Link rotto
   [DESCRIZIONE] Il link "Approfondimento ricorsi NOV" alla riga 7 del body porta a 404.
   [PRIORITÀ] Media

2. [PAGINA] /avvocati/fabiana-saltelli/
   [TIPO] Copy / tono
   [DESCRIZIONE] Frase "specializzata in molteplici aree" è generica, non in voce Saltelli.
   [PROPOSTA] "Specializzata in diritto del lavoro, contenzioso INPS e mobbing."
   [PRIORITÀ] Bassa

3. [PAGINA] /faq/
   [TIPO] Renderingbug
   [DESCRIZIONE] Su mobile (iPhone 14 Safari) la FAQ #14 ha il titolo che taglia a metà
   [SCREENSHOT] [allegato: faq-14-mobile.png]
   [PRIORITÀ] Media
```

### 17.3 — Canale Suggerimenti editoriali

Per riformulazioni, idee per nuove FAQ, miglioramenti di tono: **documento Google condiviso** (cartella Adsolut · Saltelli · Editorial Suggestions) o email settimanale.

### 17.4 — Cosa NON segnalare (è atteso)

- 0 guide gratuite — Elena le caricherà
- Bio estese di 3/4 avvocati nel post_content legacy — vedi §6.1
- Categorie blog non riallineate ai 3 cluster Tier-1 — vedi §9.2
- llms.txt + schema validation finale — fase Wave 4 (Adsolut)
- Performance sotto 80 in Lighthouse — fase Wave 4

Tutto questo è **noto** e nel piano di lavoro. Concentrati su ciò che il manuale non prevede.

---

## 18. Quando scrivere al team tecnico

📧 **Email**: tech@adsolut.it
🆘 **Per emergenze** (sito down, fatal error, contenuto pubblicato per sbaglio): scrivi anche un messaggio a Duccio Santoro su Slack/WhatsApp.

### 18.1 — Quando scrivere SEMPRE

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

### 18.2 — Cosa includere nell'email

Per velocizzare la risposta:

1. **URL della pagina** interessata
2. **Cosa stavi facendo** (azione concreta, non "non funziona")
3. **Cosa ti aspettavi**
4. **Cosa è successo invece**
5. **Screenshot** se rilevante
6. **Browser + sistema operativo** (es. "Chrome 124 su Mac")

---

## 19. Glossario

| Termine | Significato |
|---|---|
| **ACF** (Advanced Custom Fields) | Plugin WP che aggiunge i campi personalizzati che usi nelle pagine. Lo gestisce il tecnico, tu vedi solo i campi. |
| **Answer capsule** | Campo testuale 50-60 parole sulle aree Tier-1, ottimizzato per estrazione AI. |
| **CMS** (Content Management System) | Sistema di gestione contenuti — qui è WordPress. |
| **CPT** (Custom Post Type) | "Tipo di contenuto" custom — Avvocati, Aree di pratica, FAQ, Casi sono tutti CPT. |
| **CTA** (Call To Action) | Bottone che invita a un'azione (es. "Prenota un incontro"). |
| **Drop cap** | La "lettera capolettera" grande all'inizio del primo paragrafo (stile editoriale). Generata automaticamente sui body editorial. |
| **Eyebrow** | Riga corta sopra il titolo H1 (es. `§ Servizio · Costi`). |
| **Field Group** | Gruppo di campi ACF associato a un CPT o pagina. Definisce cosa puoi editare. |
| **GEO** (Generative Engine Optimization) | Ottimizzazione del sito per essere citato dalle AI (ChatGPT, Perplexity, Google AI). Le answer capsule servono a questo. |
| **Hero** | Blocco grande in alto su ogni pagina: eyebrow + H1 + lede + (a volte) CTA. |
| **JSON-LD** | Codice strutturato invisibile che dice alle AI cosa c'è nella pagina. Schema.org standard. |
| **Lede** | Sottotitolo italic sotto l'H1 nella sezione hero. Standard editoriale Saltelli. |
| **`llms.txt`** | File pubblico dedicato alle AI — cosa è il sito, cosa contiene di rilevante, dove leggere. |
| **NAP** | Name · Address · Phone. I dati di contatto che devono essere coerenti ovunque sul sito. |
| **Permalink** | URL "permanente" di una pagina (es. `/avvocati/emiliano-saltelli/`). Mai cambiarli post-pubblicazione. |
| **`robots.txt`** | File pubblico che dice ai crawler cosa indicizzare e cosa no. |
| **Schema markup / JSON-LD** | Vedi sopra — è il codice che parla alle AI. |
| **SEO** (Search Engine Optimization) | Ottimizzazione per i motori di ricerca tradizionali (Google). |
| **Sitemap** | Mappa XML del sito per crawler. Generata automaticamente. |
| **Slug** | La parte finale dell'URL (es. `emiliano-saltelli` in `/avvocati/emiliano-saltelli/`). |
| **Tassonomia** | Sistema di categorizzazione (es. categorie del blog, topic delle FAQ). |
| **Tier-1 / Tier-2** | Tier-1 = aree di pratica con contenuto deep (1500+ parole, FAQ, casi, articoli). Tier-2 = aree con contenuto leggero (titolo + answer capsule + paragrafo). |
| **TinyMCE** | L'editor wysiwyg di WordPress (quello con i pulsanti grassetto/corsivo/link). |
| **WYSIWYG** (What You See Is What You Get) | Editor visivo. Vedi il testo formattato come apparirà sul sito. |

---

## Cambi a questo manuale

Quando il sito evolve (Wave 4 production prep, Wave 5 ACF-izzazione `/lo-studio/`, ecc.), questo documento viene aggiornato. La versione corrente è sempre quella in `docs/EDITOR-HANDOFF.md` del repository GitHub Adsolut-Ai-Agency/saltelli-wp.

**Cronologia versioni**:

- **v6.0 — 2026-05-11** — Wave 4.7.fix.5 · Pages cleanup + blog doc + Customizer lock. Cestinate 16 Pages legacy (13 bozze + 3 publish orfane: `conferma`, `prenota-un-appuntamento`, `risultati`) → admin "Pagine" mostra 19 Pages reali; aggiunta §3.7 lista canonica delle 19 Pages. §9 "Il blog" riscritta (mappa campo sidebar → frontend + cosa non è editabile). Customizer + "CSS aggiuntivo" bloccati per ruolo `editor` (3 hook: `user_has_cap` strip + `load-customize.php` wp_die + `admin_menu` remove submenu). Notice contestuali nell'editor Articoli + Page Blog. §5.2 aggiornata (Page `/casi/` 2699 cestinata → archivio CPT). NB: un incident di ricorsione (`is_super_admin()` dentro `user_has_cap`) ha causato ~8 min di downtime staging in fase di sviluppo, risolto.
- **v5.0 — 2026-05-10** — Wave 4.7.fix.4 STRATEGY A FULL SCF. Editor Gutenberg disabilitato sulle 12 Pages con metabox SCF (una sola sorgente di verità per pagina) + notice "Modifica il contenuto qui sotto". `post_content` zombie delle 7 Pages bonificato (backup postmeta preservato). §3.6 nuova (workflow archive CPT) + admin shortcuts.
- **v4.0 — 2026-05-08** — Wave 4.7.fix.3 Page Metabox migration. 30 chiavi options globali → postmeta delle 4 Page WP (Home/Chi Siamo/Aree/Risorse): "modifica pagina = modifica contenuto pagina". Saltelli Settings 14 → 9 tab (rimosse Hero Homepage / Studio Section / Team & Casi / Press Homepage / Hub Pages).
- **v3.0 — 2026-05-08** — Wave 4.7.fix.2. Fix "Body sezione studio" vuoto in admin (default editoriale seedato). Rinomina URL `/chi-siamo/risultati/` → `/chi-siamo/casi-rappresentativi/` (301). Menu primary ricostruito (22 voci slug-based). Hub + archive CPT con H1/eyebrow/intro editabili in SCF.
- **v2.0 — 2026-05-08** — Wave 4.7.fix · SCF migration. Sbloccato menu Saltelli — Settings (era invisibile per bug architetturale ACF Free). §4 riscritta: 10 tab invece di 6, aggiunti Hero Homepage + Studio Section + Team & Casi + Press Homepage (4 tab nuovi editabili), aggiornati Brand (4 trust signals) e Footer (4 colophon fields). 50 chiavi `options_*` ora popolate in DB.
- **v1.1 — 2026-05-05** — Versione estesa post-audit. Aggiunta: §0 fase debug, §6.1 nota bio_estesa avvocati, §10 workflow comuni, §12 sezione GEO, §15 errori comuni, §17 reporting bug. Glossario esteso. Esempi reali buono/cattivo per FAQ + casi + answer capsule.
- **v1.0 — 2026-05-04** — Prima versione post Wave 3. Copre: 16 ACF Field Group, 9 pagine custom, 4 schede avvocato, 19 aree di pratica, 8 CPT modulari, blog standard.

---

*Manuale mantenuto da Adsolut SRLS · ultima revisione 2026-05-11 · contatto: tech@adsolut.it*
