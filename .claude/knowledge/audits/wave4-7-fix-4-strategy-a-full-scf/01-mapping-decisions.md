# Phase 1 — Discovery + Mapping decisions

**Wave**: 4.7.fix.4 STRATEGY A FULL SCF MIGRATION
**Branch**: `feat/wave4-7-fix-4-strategy-a-full-scf`
**Date**: 2026-05-10

---

## TL;DR — la situazione reale è più semplice di quanto il prompt assumeva

Audit empirico delle 7 Page WP target ha rivelato:

| Page ID | Slug | post_content len | Template | the_content() chiamata sul frontend? | Stato reale |
|---|---|---|---|---|---|
| 23 | contatti | 900 chars | `template-parts/page-contatti.php` | **NO** | **ZOMBIE** (post_content esiste ma non renderizzato) |
| 2708 | domande-frequenti | 1118 chars | `template-parts/page-faq.php` | **NO** (renderizza CPT `saltelli_faq` items) | **ZOMBIE** |
| 2709 | guide-gratuite | 678 chars | `template-parts/page-info-shared.php` | NO (body_content SCF popolato prio 1) | **ZOMBIE** |
| 2712 | come-lavoriamo | 1433 chars | `page-info-shared.php` | NO (body_content prio 1) | **ZOMBIE** |
| 2711 | prima-consulenza | 910 chars | `page-info-shared.php` | NO (body_content prio 1) | **ZOMBIE** |
| 372 | lavora-con-noi | 1252 chars | `page-info-shared.php` | NO (body_content prio 1) | **ZOMBIE** |
| 2713 | richiedi-preventivo | 880 chars | `page-info-shared.php` | **YES** (body_content SCF è VUOTO) | **LIVE** — necessita migrazione |

**Implicazioni:**
- **6/7 pagine = "zombie content"**: post_content esiste in DB ma frontend NON lo renderizza. Elena vede dualità apparente (Gutenberg pieno + SCF metabox) ma il post_content non controlla nulla.
- **1/7 pagina (richiedi-preventivo 2713) = "live content"**: il post_content è effettivamente l'unica fonte. Richiede migrazione del content nel field SCF `body_content` esistente, poi cleanup.

Il prompt originale assumeva 7 migrazioni complesse con creazione di nuovi field. La realtà richiede:
- 6 cleanup di post_content (pure delete, content è già o non-rendered o duplicato in SCF)
- 1 migrazione + cleanup (richiedi-preventivo → body_content)

---

## Decisione architetturale chiave — Strategia A applicata pragmaticamente

### 1. NESSUN nuovo SCF field necessario

Il prompt suggeriva di aggiungere field SCF nuovi per le sezioni "recruiting" di /contatti/, "modalità" di /prima-consulenza/, etc. La realtà:

- **Sezioni di contatti post_content non sono renderizzate**: il template `page-contatti.php` non chiama `the_content()`. Le sezioni "Hai bisogno di aiuto?", "Contattaci", "Si riceve solo su appuntamento", "Siamo sempre alla ricerca di nuovi Legali" sono **dead content** dal punto di vista frontend. Aggiungere field SCF per loro = aggiungere sezioni mai viste dall'utente finale.
- **Sezioni di FAQ post_content non sono renderizzate**: il template `page-faq.php` renderizza il CPT `saltelli_faq` con tassonomia `faq_topic`, NON il post_content. Le 4 sezioni nel post_content sono dead content.
- **Per le 4 info-shared pages (2709, 2712, 2711, 372)**: il template `page-info-shared.php` HA fallback `the_content()` ma solo se `body_content` SCF è vuoto. In tutte e 4 il `body_content` è popolato (Wave 2 Content Migration). Il post_content non viene mai renderizzato.

### 2. NESSUN split di `group_info_shared_v1` in 5 group dedicati

Il prompt suggeriva di splittare in 5 group separati (`group_page_guide_gratuite_v1`, etc.) per "eliminare ambiguità". Motivazioni per NON farlo:

1. **Storage SCF è per-post**: i field hanno lo stesso `name` ma la postmeta è scritta per `post_id`. Editare la stessa key da pagine diverse produce postmeta diversa. Nessuna ambiguità di dati.
2. **Editor UX post-Gutenberg-disable**: con Phase 5 disable Gutenberg, l'editor apre la pagina e vede solo il metabox SCF. Il titolo "Info Shared" è confondente ma non blocking; possiamo retitolare via `acf/load_field_group` filter o cambiare il `title` JSON.
3. **Costo manutenzione**: 5 file JSON quasi identici da mantenere sincronizzati ad ogni modifica futura.
4. **Field key collision rischiosa**: SCF richiede field key globalmente uniche. Splittando, dovrei rinominare tutte le 16 field key da `field_info_*` a `field_page_<slug>_*` × 5 pagine = 80 nuove field key + migration delle shadow `_<name>` postmeta references. Lavoro non banale + rischio data loss.

**Decisione**: keep `group_info_shared_v1` shared. Retitle JSON `title` da "Info Shared — Layout standard" → "Contenuto pagina" per chiarezza editor. Documentato qui.

### 3. Nessuna espansione di `group_contatti_v1` o `group_faq_v1`

I template `page-contatti.php` e `page-faq.php` leggono già tutti i loro field SCF + Theme Options. Le sezioni del post_content che non corrispondono a un field esistente non sono renderizzate sul frontend. Aggiungere field per loro = aggiungere sezioni invisibili.

**Decisione**: NESSUN nuovo field. I 3 group (`contatti`, `faq`, `info_shared`) restano invariati. Phase 2 si riduce a retitling JSON + opzionale rifinitura.

---

## Mapping per Page (cosa si fa effettivamente)

### Page 23 — `/contatti/`
- **post_content**: 900 chars HTML legacy (eyebrow "Hai bisogno di aiuto?" + h2 Contattaci + h2 Chiedi qualsiasi cosa + intro + ul contatti + h2 riceve appuntamento + h2 ricerca legali + cta candidatura)
- **Template render**: `page-contatti.php` legge `hero_eyebrow`, `hero_h1_pre`, `hero_h1_em`, `hero_lede`, `map_iframe`, `map_caption`, `come_arrivare_title`, `trust_signal` da SCF + Studio Info da Theme Options.
- **Action**: Phase 5 `post_content = ''`. Backup in `_legacy_post_content_backup`. Nessun nuovo field SCF.

### Page 2708 — `/risorse/domande-frequenti/`
- **post_content**: 1118 chars (lede + 4 h2 aree + cta)
- **Template render**: `page-faq.php` legge `hero_*`, `toc_title`, `cta_*` da SCF + query CPT `saltelli_faq` con tax `faq_topic`.
- **Action**: Phase 5 `post_content = ''`. Backup. Nessun nuovo field.

### Page 2709 — `/risorse/guide-gratuite/`
- **post_content**: 678 chars (lede + h2 "In lavorazione" + ul guide + cta)
- **SCF body_content**: ~200+ chars popolato (Wave 2 Content Migration), editorial diverso dal post_content
- **Template render**: `page-info-shared.php` prio 1 `body_content` SCF (popolato).
- **Action**: Phase 5 `post_content = ''`. Backup. Nessun nuovo field (body_content già popolato).

### Page 2712 — `/costi-e-consulenze/come-lavoriamo/`
- **post_content**: 1433 chars (lede + h2 "Il nostro processo" + ol 5 step + h2 "I nostri principi" + ul + cta)
- **SCF body_content**: popolato, editorial diverso
- **Template render**: `page-info-shared.php` prio 1 SCF.
- **Action**: Phase 5 `post_content = ''`. Backup.

### Page 2711 — `/costi-e-consulenze/prima-consulenza/`
- **post_content**: 910 chars (lede + h2 "Cosa ricevi" + ul + h2 "Modalità" + p + cta)
- **SCF body_content**: popolato
- **Template render**: SCF prio 1.
- **Action**: Phase 5 `post_content = ''`. Backup.

### Page 372 — `/lavora-con-noi/`
- **post_content**: 1252 chars (lede + h2 "Profili" + ul + h2 "Cosa offriamo" + ul + h2 "Come candidarti" + p + p)
- **SCF body_content**: popolato (focus su mentorship 18 mesi, CCNL forense)
- **Template render**: SCF prio 1.
- **Action**: Phase 5 `post_content = ''`. Backup.

### Page 2713 — `/costi-e-consulenze/richiedi-preventivo/` ⚠️ UNICO che richiede migrazione
- **post_content**: 880 chars LIVE (lede + h2 "Come funziona" + ol 3 step + p + cta)
- **SCF body_content**: **VUOTO** ← fallback the_content() si attiva
- **Template render**: `page-info-shared.php` line 93 `the_content()` fallback ATTIVO.
- **Action**:
  1. Phase 3: migrare `post_content` → SCF `body_content` (write `update_post_meta(2713, 'body_content', $post_content)` + shadow `_body_content = field_info_body_content`).
  2. Phase 5: `post_content = ''`. Backup.

---

## Phase 2 plan (rivisto post-discovery)

**Cosa NON faccio** (rispetto al prompt originale):
- ❌ Non aggiungo recruiting tab a `group_contatti_v1`
- ❌ Non aggiungo intro field a `group_faq_v1`
- ❌ Non splitto `group_info_shared_v1` in 5 group dedicati
- ❌ Non rimuovo `group_info_shared_v1`

**Cosa faccio**:
- ✅ Retitolo `title` JSON dei 3 group rilevanti per chiarezza editor (Phase 5 deciderà se vale la pena anche cambiare via `acf/load_field_group` filter dinamico)
- ✅ Nessun altro change ai field group

**Cosa migra effettivamente Phase 3**:
- 1 migrazione real: Page 2713 `post_content` → `body_content` SCF
- 7 backup `_legacy_post_content_backup` (anche per zombie, per rollback safety)

---

## Lo-studio (Page 2811) — non in scope ma documentato

`lo-studio` (slug=`lo-studio`, ID 2811) ha:
- post_content empty (0 char)
- URL serving: `/lo-studio/` 301 → `/chi-siamo/lo-studio/` (page sotto chi-siamo hub)
- SCF group attached: `group_lo_studio_v1` (page_slug == lo-studio) — già presente
- Template render: `page-lo-studio.php` (via dispatch in `page.php:48`)

**NON è orphan**: serve come Page WP per il sotto-URL `/chi-siamo/lo-studio/`. Ha SCF metabox dedicato. Rispetto al prompt che chiedeva valutazione cancellazione, **decisione: NON cancellare** — la pagina è funzionale e usata.

Dovrebbe essere aggiunta alla lista delle Page WP con Gutenberg disabled in Phase 5? **SÌ** — è una Page WP con SCF metabox attached, stesso pattern delle altre 11. Aggiungo Page 2811 alla lista `SALTELLI_SCF_ONLY_PAGES` per consistency.

Lista finale Page WP con Gutenberg disabled in Phase 5 (12 totale):
- 4 hub Wave 4.7.fix.3: 17 (home), 2822 (chi-siamo), 2812 (aree-di-pratica), 2813 (risorse)
- 7 target Wave 4.7.fix.4: 23 (contatti), 2708 (faq), 2709 (guide), 2712 (lavoriamo), 2711 (prima-consulenza), 372 (lavora-con-noi), 2713 (preventivo)
- 1 child Page con SCF già esistente: 2811 (lo-studio)
- *Eventualmente* anche 2695 (costi-e-consulenze hub) — già SCF-driven, da verificare in Phase 5

---

*Audit completato 2026-05-10 · prosegue con Phase 2 (minimale)*
