# Wave 4.7.fix.2 Investigation v2 — Admin↔Frontend + Page Discovery + URL/Menu + Recurring Blocks · Report

**Data**: 2026-05-08
**Scope**: READ-ONLY diagnostic scan, 4 categorie A+B+C+D
**Pattern**: sequenziale 8 phases (Phase 5 saltata, riposizionata come Phase 9)
**Branch**: main (no branch nuovo, sessione read-only)
**Tema corrente**: 1.3.7-wave4-7-fix-1-scf-url-validation
**Riferimento**: bug Duccio 2026-05-08 + feedback Elena (15 URL "non trovo dove modificare" + blocchi ricorrenti)

---

## TL;DR (4 frasi)

1. **Categoria A — Mismatch admin/frontend**: 0 CRITICAL su `saltelli_option(name, 'fallback')` con JSON empty + 5 DIVERGING (3 falsi positivi escape `\n`, 2 reali: `footer_newsletter_provider`, `studio_orari_settimana`). Il VERO bug Duccio è un altro pattern: `saltelli_option(name, '')` con fallback empty MA inline `if/else` template echoes HTML hardcoded — confermato su `studio_body` (front-page.php:199-207, 3 paragrafi hardcoded), 4-5 pattern simili identificati negli altri template.
2. **Categoria B — Page discoverability**: 11/15 URL Elena sono Pages WP (admin standard accessibile), 3/15 Term tipo-area (admin path tassonomia che Elena non sa cercare), 2/15 CPT archive (`/chi-siamo/team/` `=archive-avvocato.php`, `/chi-siamo/risultati/` `=archive saltelli_caso` — NESSUNA Page WP, H1 e contenuti hardcoded nei template). 0/15 404.
3. **Categoria C — URL/Menu**: 4 Page con slug ≠ title (incluso `risultati`/Casi rappresentativi → decision rename già presa); **MENU "Saltelli Header" (primary) ha 17/22 URL OBSOLETI pre-Wave 5 IA refactor** (`/competenze/` invece di `/aree-di-pratica/`, `/faq/` invece di `/risorse/domande-frequenti/`, `/costi/` invece di `/costi-e-consulenze/`, ...) — è la causa root del caos URL, non un singolo URL.
4. **Categoria D — Recurring blocks**: 4/6 SCF-covered (CTA pre-footer ✓, Banda Newsletter ✓ con piccola divergenza, Trust signals ✓, Footer colophon ✓); 2/6 fuori SCF (Sticky WhatsApp messaggio hardcoded ma telefono via studio_telefono_pubblico OK; Header navigation via `wp_nav_menu` con menu URL obsoleti); +1 sub-block NON-SCF da considerare: `$ftr_tier1` (3 aree tier-1 hardcoded in footer.php).

---

## 📊 Numeri chiave

| Metrica | Valore |
|---|---|
| Total call-site `saltelli_option` | 53 |
| Con fallback string literal | 29 |
| Unique field name con fallback | 23 |
| Field nel JSON (excl tabs) | 53 |
| JSON con default_value | 40 |
| JSON senza default_value | 12 |
| Mismatch CRITICAL (PHP fb + JSON empty) | **0** |
| Mismatch DIVERGING (PHP != JSON) | **5** (di cui 3 falsi positivi escape) |
| Mismatch reali DIVERGING | **2** (`footer_newsletter_provider`, `studio_orari_settimana`) |
| OK aligned | 18 |
| Field PHP custom (no JSON) | 0 |
| **Pattern bug Duccio "saltelli_option vuoto + if/else HTML hardcoded"** | **1 confermato (`studio_body`) + 4-5 simili da validare** |
| Wp_options.options_* totali staging | 50 |
| Options DB len=0 | 6 (`studio_body`, `casi_rappresentativi_home`, `press_outlets`, `social_linkedin`, `social_twitter`, `colophon_email`) |
| 15 URL Elena: Pages WP | **11** |
| 15 URL Elena: Term tipo-area | **3** |
| 15 URL Elena: CPT archive (NO Page) | **2** |
| 15 URL Elena: 404 | 0 |
| Page slug ≠ title sanitized | **4** |
| Menu Saltelli Header URL obsoleti pre-Wave 5 | **17/22** (≈77%) |
| Recurring blocks SCF-covered | 4/6 |
| Recurring blocks NOT SCF | 2/6 |
| Hardcoded sub-block in footer da decidere | 1 (`$ftr_tier1` 3 aree) |

---

## 🅰️ Sezione A — Mismatch admin↔frontend

### A.1 Mismatch CRITICAL (PHP fallback string + JSON empty): **0**

Il pattern attesso (`saltelli_option('field', 'PHP fallback hardcoded')` con `default_value` vuoto in JSON) **non è presente** nella codebase. La regex Phase 1+3 ha trovato 23 unique field con fallback hardcoded, e tutti hanno default_value JSON popolato.

### A.2 Mismatch DIVERGING (PHP fallback != JSON default_value): **5 (di cui 2 reali)**

**Falsi positivi della regex** (3 — literal `\n` PHP source vs newline JSON):
- `colophon_indirizzo`: PHP=`Via Vannella Gaetani, 27\n80121 Napoli — Chiaia` vs JSON con newline reale identico
- `colophon_orari`: PHP=`Lun – Ven · 10:00 – 19:00\nSolo su appuntamento` vs JSON identico
- `team_titolo`: PHP=`Quattro\nprofessionisti.` vs JSON identico

**Mismatch reali** (2):
- `footer_newsletter_provider` — PHP=`brevo`, JSON=`static`. **DB staging mostra `brevo`** (Wave 4.6 baseline preserved). Decisione: o JSON allineato a `brevo`, o flag default che scuriffa per PHP.
- `studio_orari_settimana` — PHP=`Lun – Ven · 10:00 – 19:00`, JSON=`Lun – Ven · 09:30 – 18:30`. **DB staging mostra `Lun – Ven · 10:00 – 19:00`** (PHP fallback ha vinto perché Phase 4.6 seed lo aveva sovrascritto). Allineare JSON o reseed.

### A.3 BUG VERO (pattern non rilevato Phase 1/3, identificato Phase 3b)

**Pattern**: `$var = saltelli_option('field', '')` (fallback empty string) **+** template inline `if ($var) { echo wp_kses_post($var); } else { ?> <p>HTML hardcoded</p> <?php }`.

**Esempio confirmato** (= bug Duccio "Studio Section Body sezione studio"):

```php
// front-page.php:25
$studio_body = saltelli_option('studio_body', '');

// front-page.php:199-207
if ($studio_body) {
    echo wp_kses_post($studio_body);
} else {
    ?>
    <p>Lo Studio Saltelli &amp; Partners nasce nel 1999 per iniziativa di Emiliano Saltelli...</p>
    <p>Crediamo che il diritto sia, prima di tutto, un'arte di ascolto...</p>
    <p>Lavoriamo in <a class="sl-link" href="...">Via Vannella Gaetani 27</a>...</p>
    <?php
}
```

**Effetto**: Elena apre WP Admin → Saltelli Settings → Studio Section → Body sezione studio → trova **field vuoto** (DB `options_studio_body len=0`). Pubblica → frontend mostra 3 paragrafi hardcoded del template. Elena non capisce dove modificare (correttamente).

**Pattern simili candidati identificati Phase 3b** (richiedono validazione manuale per distinguere da falsi positivi heuristic):

| File | Var | Field | Tipo helper | Note |
|---|---|---|---|---|
| `front-page.php:199` | `$studio_body` | `studio_body` | saltelli_option | **CONFERMATO bug Duccio** |
| `single-avvocato.php:14` | `$ruolo` | `ruolo_breve` | saltelli_field | post_meta CPT — fallback è span placeholder vuoto (UX, non content) |
| `single-avvocato.php:25` | `$linkedin` | `same_as_linkedin` | saltelli_field | post_meta CPT — fallback nessun link visibile (legittimo) |
| `template-parts/page-lo-studio.php:182` | `$bio_breve_av` | `bio_breve` | saltelli_field | post_meta CPT — fallback span placeholder |
| `archive-avvocato.php:87` | `$ruolo` | `ruolo_breve` | saltelli_field | post_meta CPT — fallback decorativo |

**Distinzione**: il bug Duccio (`studio_body`) è un Theme Options field il cui fallback è **contenuto editoriale 3-paragrafi**. Gli altri 4 sono post_meta CPT con fallback **placeholder UX** (legittimi). La distinzione critica è:
- **Bug**: field = contenuto editoriale, fallback = HTML editoriale → Elena vede admin vuoto e frontend pieno
- **Non-bug**: field = piccolo dato CPT (ruolo, linkedin URL), fallback = placeholder/skip → comportamento atteso

### A.4 DB staging state — chiavi vuote

6 chiavi `options_*` con `len=0`:
- `options_studio_body` — **= bug Duccio** (fallback HTML hardcoded in template)
- `options_casi_rappresentativi_home` — type post_object, fallback `query casi recenti` (probabile UX legittimo)
- `options_press_outlets` — type repeater, vuoto by design (Wave 4 Press Homepage non ancora popolato)
- `options_social_linkedin`, `options_social_twitter` — vuoti by design (no fallback in template, Linkedin Emiliano viene da `saltelli_attorney_linkedin('emiliano-saltelli')` helper)
- `options_colophon_email` — vuoto by design (footer usa `studio_email` populated)

---

## 🅱️ Sezione B — Page Discovery Map (15 URL Elena)

| # | URL | Source | Admin Edit | Editability | Note |
|---|---|---|---|---|---|
| 1 | `/chi-siamo/` | PAGE 2822 (template default) | `/wp-admin/post.php?post=2822&action=edit` | **High** | Page con post_content (335 chars), template `page.php` → include `page-chi-siamo-hub.php` per layout |
| 2 | `/chi-siamo/team/` | **CPT archive avvocato** (`has_archive='chi-siamo/team'`) → `archive-avvocato.php` | NESSUNO | **NULL** | H1 hardcoded `__('Quattro', ...) <em>professionisti.</em>` (line 30); subtitle hardcoded "Un atelier di quattro avvocati a Chiaia..." |
| 3 | `/chi-siamo/risultati/` | **CPT archive saltelli_caso** (`has_archive='chi-siamo/risultati'`) → `archive.php` (no archive-saltelli_caso.php) | NESSUNO | **NULL** | Title `<title>Casi rappresentativi Archive - Studio Legale Saltelli</title>` viene da `register_post_type` label "Casi rappresentativi"; **decisione Duccio**: rename slug a `/chi-siamo/casi-rappresentativi/` con redirect 301 |
| 4 | `/aree-di-pratica/` | PAGE 2812 (post_content=0) | `/wp-admin/post.php?post=2812&action=edit` | **Low** | Page vuota, template hub `page-aree-di-pratica-hub.php` con cluster cards 100% hardcoded `__('Per i privati', ...)` (lines 18-44) |
| 5 | `/aree-di-pratica/privati/` | TERM tipo-area (term_id=992) | `/wp-admin/term.php?taxonomy=tipo-area&tag_ID=992` | **Medium** | Description editabile (79 chars). UX strings template `taxonomy-tipo-area.php` hardcoded. Elena deve sapere che è una **tassonomia**, non una Page |
| 6 | `/aree-di-pratica/imprese/` | TERM tipo-area (term_id=993) | `/wp-admin/term.php?taxonomy=tipo-area&tag_ID=993` | **Medium** | description=69 chars |
| 7 | `/aree-di-pratica/contenzioso-amministrativo/` | TERM tipo-area (term_id=994) | `/wp-admin/term.php?taxonomy=tipo-area&tag_ID=994` | **Medium** | description=59 chars |
| 8 | `/risorse/` | PAGE 2813 (post_content=0) | `/wp-admin/post.php?post=2813&action=edit` | **Low** | Page vuota, template `page-risorse-hub.php` con H1 + 4 resource cards hardcoded |
| 9 | `/risorse/domande-frequenti/` | PAGE 2708 (post_content=1117) | `/wp-admin/post.php?post=2708&action=edit` | **High** | Probabile usage `page-faq.php` template — verificare |
| 10 | `/risorse/guide-gratuite/` | PAGE 2709 (post_content=677) | `/wp-admin/post.php?post=2709&action=edit` | **High** | |
| 11 | `/risorse/glossario-legale/` | PAGE 2710 (post_content=1200) | `/wp-admin/post.php?post=2710&action=edit` | **High** | |
| 12 | `/costi-e-consulenze/` | PAGE 2695 (post_content=4649) | `/wp-admin/post.php?post=2695&action=edit` | **High** | post_content lungo, template hub `page-costi-e-consulenze-hub.php` |
| 13 | `/costi-e-consulenze/prima-consulenza/` | PAGE 2711 (post_content=909) | `/wp-admin/post.php?post=2711&action=edit` | **High** | |
| 14 | `/costi-e-consulenze/come-lavoriamo/` | PAGE 2712 (post_content=1432) | `/wp-admin/post.php?post=2712&action=edit` | **High** | |
| 15 | `/costi-e-consulenze/richiedi-preventivo/` | PAGE 2713 (post_content=879) | `/wp-admin/post.php?post=2713&action=edit` | **High** | slug=`richiedi-preventivo` ≠ title `Richiedi un preventivo` |

### B.1 Breakdown source

- **11/15 Pages WP** (ID 2695, 2708, 2709, 2710, 2711, 2712, 2713, 2812, 2813, 2822 + via 2811 lo-studio non incluso nei 15 ma anomalo)
- **3/15 Term tipo-area** (privati, imprese, contenzioso-amministrativo) — admin path è `/wp-admin/term.php?taxonomy=tipo-area&tag_ID=N` (Elena cerca in WP-Admin → "Tassonomie" non in "Pagine")
- **2/15 CPT archive URLs** (avvocato, saltelli_caso) — NESSUNA Page WP corrispondente; H1/title/intro tutti hardcoded in `archive-avvocato.php` e `archive.php` fallback
- **0/15 404**

### B.2 Pattern problema discoverability

| Pattern | URL coinvolti | Problema Elena |
|---|---|---|
| Page con post_content vuoto + template hub | /aree-di-pratica/, /risorse/ | Elena apre la Page in admin, vede content empty, modifica content → frontend INVARIATO (perché viene renderizzato dall'hub PHP hardcoded) |
| Term tipo-area | /aree-di-pratica/{privati, imprese, contenzioso-amministrativo}/ | Elena non sa che è una tassonomia: cerca in Pagine, non trova, frustrata |
| CPT archive URL | /chi-siamo/team/, /chi-siamo/risultati/ | Elena non sa cos'è una "archive page" CPT: il content non esiste in WP affatto |
| Page con slug ≠ title (4 casi) | lo-studio/Chi Siamo, risultati/Casi rappresentativi, prenota-appuntamento, richiedi-preventivo | Confusione cognitive nell'admin |

---

## 🇨 Sezione C — URL/Title/Menu incongruence

### C.1 Page slug ≠ title sanitized (4 casi)

| ID | slug | title | sanitize_title(title) | Severity |
|---|---|---|---|---|
| 2811 | `lo-studio` | Chi Siamo | `chi-siamo` | **CRITICAL** — questa Page sembra duplicato/stale; Page 2822 è la "vera" /chi-siamo/. Verificare se 2811 è obsoleta da pulire |
| 2699 | `risultati` | Casi rappresentativi | `casi-rappresentativi` | **DUCCIO DECISION**: rename slug → `casi-rappresentativi` + redirect 301 legacy |
| 2714 | `prenota-appuntamento` | Prenota un appuntamento | `prenota-un-appuntamento` | Minor — l'articolo "un" elided dallo slug |
| 2713 | `richiedi-preventivo` | Richiedi un preventivo | `richiedi-un-preventivo` | Minor — stesso pattern |

### C.2 Menu Saltelli Header (location `primary`, term_id=996) — 17/22 URL OBSOLETI

Tutti `type=custom` con URL hardcoded (NO `type=page` con `obj_id=N`). Confronto post-Wave 5 IA refactor:

| Menu item | URL menu | URL reale Wave 5+ | Status |
|---|---|---|---|
| Chi Siamo | `/chi-siamo/` | `/chi-siamo/` ✓ | OK |
| └─ Lo Studio | `/chi-siamo/` | duplicate del parent | Ridondante |
| └─ Il Team | `/avvocati/` | `/chi-siamo/team/` | **OBSOLETO** |
| └─ Risultati | `/casi/` | `/chi-siamo/risultati/` | **OBSOLETO** |
| Aree di Pratica | `/competenze/` | `/aree-di-pratica/` | **OBSOLETO** |
| └─ Per i Privati | `/tipo-area/privati/` | `/aree-di-pratica/privati/` | **OBSOLETO** |
| └─ Per le Imprese | `/tipo-area/imprese/` | `/aree-di-pratica/imprese/` | **OBSOLETO** |
| └─ Contenzioso Amministrativo | `/tipo-area/contenzioso/` | `/aree-di-pratica/contenzioso-amministrativo/` | **OBSOLETO+TYPO** (manca `-amministrativo`) |
| └─ Tutte le aree | `/competenze/` | `/aree-di-pratica/` | **OBSOLETO** |
| Risorse | `/faq/` | `/risorse/` | **OBSOLETO** (= **causa "clic Risorse mostra domande-frequenti"**) |
| └─ Blog | `/blog/` | `/risorse/blog/` | **OBSOLETO** |
| └─ Domande Frequenti | `/faq/` | `/risorse/domande-frequenti/` | **OBSOLETO** |
| └─ Guide Gratuite | `/guide-gratuite/` | `/risorse/guide-gratuite/` | **OBSOLETO** |
| └─ Glossario Legale | `/glossario-legale/` | `/risorse/glossario-legale/` | **OBSOLETO** |
| Costi e Consulenze | `/costi/` | `/costi-e-consulenze/` | **OBSOLETO** |
| └─ Prima Consulenza | `/prima-consulenza/` | `/costi-e-consulenze/prima-consulenza/` | **OBSOLETO** |
| └─ Come Lavoriamo | `/come-lavoriamo/` | `/costi-e-consulenze/come-lavoriamo/` | **OBSOLETO** |
| └─ Richiedi Preventivo | `/richiedi-preventivo/` | `/costi-e-consulenze/richiedi-preventivo/` | **OBSOLETO** |
| Contatti | `/contatti/` | `/contatti/` ✓ | OK |
| └─ Prenota Appuntamento | `/prenota-appuntamento/` | `/prenota-appuntamento/` (Page 2714 esiste con slug `prenota-appuntamento`) | **OK ma slug ≠ title** |
| └─ Dove Siamo | `/contatti/#sede` | OK ✓ | OK |
| └─ Lavora con Noi | `/lavora-con-noi/` | da verificare | TBD |

**5 OK / 17 obsoleti** = 77% del menu non aggiornato post-Wave 5 IA refactor.

### C.3 Menu Main (term_id=3, count=26) — alternativo

Menu legacy con altri item (HOME → Page 17, COMPETENZE submenu con vecchie page IDs 300/947/292/285/273/254/996/2241, BLOG → /risorse/blog/). Non assegnato a nessuna location ma esiste in DB. Da capire se serve o eliminare.

### C.4 Comportamento /risorse/ click

- HTTP HEAD `/risorse/` → 200, no redirect
- WP: Page id=2813 publish, post_content=0 chars → render via `page-risorse-hub.php`
- Lamentela Elena "clic risorse → domande-frequenti": **non è la Page /risorse/, è il MENU** che linka a `/faq/` (URL obsoleto). `/faq/` redirige (rewrite WP) a Page con slug `domande-frequenti` o ad un sub-path → mostra domande-frequenti

### C.5 CPT archive title hardcoded

- `/chi-siamo/team/` → `<title>Avvocati Archive - Studio Legale Saltelli</title>` (label CPT avvocato)
- `/chi-siamo/risultati/` → `<title>Casi rappresentativi Archive - Studio Legale Saltelli</title>` (label CPT saltelli_caso)
- Modificabile solo via `register_post_type([...,'label'=>'...'])` in `inc/cpt-saltelli-caso.php` (non editabile da Elena)

---

## 🇩 Sezione D — Recurring blocks coverage

| Block | Used in | SCF Tab | Coverage | Action raccomandata |
|---|---|---|---|---|
| **CTA pre-footer "Ultima chiamata"** | `footer.php:87-110` (1 punto centrale, ma referenced in 19 file template) | `CTA Defaults` (4 field: `cta_default_label`, `cta_default_url`, `cta_trust_signal`, `cta_subline_italic`) + `Hero Homepage` (hero_cta_*) | ✅ **COVERED** | Nessuna azione tecnica. Verificare con Elena che il workflow "Saltelli Settings → CTA Defaults" sia chiaro |
| **Banda Newsletter** "L'editoriale del giovedì" Brevo | `footer.php:121+` (1 punto) + `home.php:218` (newsletter blog2 distinta) | `Footer` tab (8 field: 4 colophon_* + 4 footer_credit/newsletter_*) | ✅ **COVERED** con divergenza: `footer_newsletter_provider` PHP=`brevo` JSON=`static` → align JSON o reseed |
| **Trust signals plate** (4 plate) | `template-parts/trust-bar.php:46-289` (1 punto, included via `get_template_part('template-parts/trust-bar')` da `front-page.php:338`; e probably altri) | `Brand` tab (10 field: `trust_signal_{1-4}_label`, `trust_signal_{1-4}_caption`) | ✅ **COVERED**. Hardcoded `$defaults` array (line 254-259) allineato ai JSON default ([20+ ANNI/ESPERIENZA, 4 AVVOCATI/TEAM SPECIALIZZATO, 17 AREE/DI PRATICA, COA FAMIGLIA/MUNICIPALITÀ 1]) | Nessuna azione tecnica. Default editoriale match già OK |
| **Sticky widget WhatsApp** (icon + text) | `header.php:128` (desktop) + `template-parts/mobile-sticky-bar.php` (mobile) | NO tab dedicato; usa `studio_telefono_pubblico` (Studio Info), telefono via helper `saltelli_studio_phone_e164()` | ⚠️ **PARTIAL**: telefono editabile via `studio_telefono_pubblico` ✓; **MA**: messaggio prefilled `'Ciao, %s sul vostro sito. Vorrei una consulenza.'` HARDCODED come stringa traducibile `__()`, NON editabile da Elena | Action: aggiungere SCF field `whatsapp_message_template` in tab `Brand` (es. "Messaggio WhatsApp default — usa `%s` per il contesto della pagina"). Cost: ~10 min |
| **Footer colophon** (Fascia 4 + sl-hero__colophon) | `footer.php` Fascia 4 + `front-page.php:99` (hero colophon stesso pattern) | `Footer` tab (`colophon_indirizzo`, `colophon_orari`, `colophon_email`, `colophon_telefono`) + `Studio Info` tab | ✅ **COVERED**. Architettura corretta: `studio_*` è canonical, `colophon_*` è override opzionale per casi divergenti | Nessuna azione |
| **Header navigation** (logo + menu + CTA WhatsApp) | `header.php:27-176` (1 punto) + 2 inc helpers | `wp_nav_menu` location `primary` (NON in SCF) + `brand_payoff` (Brand tab) | ⚠️ **OUTSIDE SCF, ma**: il menu corrente Saltelli Header ha **17/22 URL obsoleti** (vedi Sezione C.2). Il problema non è coverage SCF, è data correctness del menu | **PRIORITY 1**: rifare il menu da zero con type=page references (NON custom URL hardcoded) — migliora robustezza ai rename slug futuri |

### D.1 Hardcoded sub-blocks da decidere

- `footer.php:397-401` `$ftr_tier1` array (3 aree tier-1: Tributario, Lavoro, Famiglia LGBTQ+) con URL `/aree-di-pratica/privati/diritto-tributario/` etc. — Hardcoded ma giustificato (Tier-1 è strategic positioning fisso, non frequente di cambiare). Decision: mantenere hardcoded oppure SCF tab "Footer Tier-1 Aree" per editabilità completa. Cost SCF migration: ~15 min.
- `taxonomy-tipo-area.php` UX strings hardcoded (es. § Studio · Avvocati eyebrow, "Un atelier di quattro avvocati a Chiaia..." paragraph). Pattern same as `archive-avvocato.php`. Decision: SCF tab "Taxonomy Hub" oppure mantenere hardcoded (è branded copy, raramente cambia).

### D.2 Newsletter Form Brevo

`footer.php` Fascia 3 contiene il form Brevo (`<form>` con action verso server Brevo) — markup HTML hardcoded del form. SCF copre solo il flag enabled + provider name. Per cambiare provider serve refactor template (codice). Decisione architetturale: lasciato hardcoded perché provider switch è raro/dev task.

---

## 🎯 Diagnosi finale

### Categoria A
- Il bug Duccio "Studio Section Body sezione studio admin vuoto frontend pieno" **NON è un mismatch PHP/JSON** (Phase 3 trovato 0 CRITICAL).
- È il pattern **`saltelli_option(name, '')` con default empty + `if/else` template HTML hardcoded**. Confermato su `studio_body` (3 paragrafi). Non rilevato dal regex Phase 1 perché il fallback è dentro il template, non dentro la funzione.
- 4-5 pattern simili candidati (Phase 3b heuristic) richiedono validazione manuale: la maggioranza sono post_meta CPT con fallback **placeholder UX** (legittimi), NON content fallback editoriale (illegittimi).

### Categoria B
- Il problema Elena "non trovo dove modificare" è **architecturale**, non un bug singolo:
  - **Pages con post_content empty + hub PHP hardcoded** (3 casi: aree-di-pratica, risorse, ma anche aree-di-pratica hub completamente hardcoded)
  - **Term tipo-area** mappati come URL hub (3 casi) — Elena cerca tassonomia, non Pagine
  - **CPT archive URL** senza Page WP corrispondente (2 casi) — content e H1 hardcoded nei template archive

### Categoria C
- **Causa root del caos URL**: il menu **Saltelli Header (primary)** ha 77% URL obsoleti pre-Wave 5 IA refactor. Tutti `type=custom` (URL hardcoded), non `type=page` (referenziati).
- Quando uno slug Page cambia, tutti i menu `type=custom` con URL hardcoded si rompono silenziosamente.
- L'URL `/faq/` (menu obsoleto Risorse) sopravvive perché Page con slug `faq` esiste o c'è un rewrite legacy. Da verificare via `wp post list --post_type=page --field=post_name | grep faq`.

### Categoria D
- 4/6 recurring blocks SCF-covered: in scope corretto, Elena può modificare.
- 2/6 fuori SCF:
  - **Sticky WhatsApp message**: hardcoded translatable string. Aggiungere SCF field rapido (~10 min).
  - **Header navigation**: il vero problema è l'errato data del menu, non lack di SCF. Il menu è `wp_nav_menu` standard WP (admin path: WP Admin → Apparenza → Menu).

---

## 🛠️ Raccomandazione fix path (Wave 4.7.fix.2 vero)

### Priority 1 — Categoria A: studio_body fix + altri "saltelli_option(name, '') + else HTML editorial"

**Strategy**: per ogni field con questo pattern, **migrare il fallback HTML editoriale dentro JSON `default_value`**. Reseed DB se popolato a empty.

**Step concreti**:
1. Identificare la lista finale di field affected (validazione manuale dei 5 candidati Phase 3b — escludere placeholder UX legittimi).
2. Per ognuno: estrarre HTML hardcoded del else block, convertire in stringa default_value (probabile WYSIWYG → multi-line OK), update `acf-json/group_theme_options_v1.json`.
3. Sync staging + reseed DB con `inc/seed-theme-options.php` (idempotency-safe).
4. Refactor template: rimuovere il blocco `if/else { hardcoded HTML }`, sostituire con `echo wp_kses_post($studio_body);` + condizionale opzionale per non-render se totalmente vuoto.
5. Smoke test: Elena modifica Saltelli Settings → Studio Section → Body, save → frontend riflette.

**Cost**: ~30-45 min Code (1 phase Wave). Scope: principal su `studio_body`. Altri field: solo se confermati editoriali (probabile 1-2 max).

### Priority 2 — Categoria C: Menu Saltelli Header rebuild (CRITICAL)

**Strategy**: rifare il menu primary da zero con `type=page` references (object_id pointing to actual page IDs), eliminando i custom URL hardcoded. Mantenere lo stesso visible label.

**Decision Duccio già presa**: rename slug `/chi-siamo/risultati/` → `/chi-siamo/casi-rappresentativi/` (via CPT archive `has_archive` reset, non Page slug — perché `risultati` è il CPT archive URL, non una Page; oppure decidere se creare anche una Page con questo slug per editing). **Da verificare se l'intent è cambiare il `has_archive` del CPT saltelli_caso oppure creare una Page WP che si sovrapponga.**

**Step concreti**:
1. Disconnettere il menu attivo `Saltelli Header` (term_id=996) — opzionale: backup completo (export JSON) prima.
2. Creare nuovo menu `Saltelli Header v2` con item `type=page` (object_id riferito a Page ID 2822, 2812, 2813, 2695 etc.) per i tier-1; `type=taxonomy` per term tipo-area; `type=custom` SOLO per CPT archive (`/chi-siamo/team/`, `/chi-siamo/risultati/` o nuovo slug post-decision Duccio).
3. Aggiungere redirect 301 legacy per gli URL obsoleti più SEO-critical (`/competenze/` → `/aree-di-pratica/`, `/faq/` → `/risorse/domande-frequenti/`, `/costi/` → `/costi-e-consulenze/`, etc.) — confermare con Yoast/SEO presenza redirect manager o usare plugin Redirection.
4. Per slug rename `risultati` → `casi-rappresentativi`: 
   - Se architettura attuale è solo `has_archive='chi-siamo/risultati'` per CPT saltelli_caso: cambiare a `has_archive='chi-siamo/casi-rappresentativi'` + flush rewrite
   - Aggiungere redirect 301 legacy
   - Aggiornare label CPT post_type → "Casi rappresentativi" già OK
5. Verifica menu in 3-4 device + admin Apparenza → Menu Elena può vedere

**Cost**: ~60-90 min Code (multi-phase, include test). + redirect manager setup separato.

### Priority 3 — Categoria B: documentation + struttural decisions

Per i 4 problematici architetturali Categoria B (Pages-empty-with-hub, Term tipo-area, CPT archive senza Page):

**Option A — Documentazione minima** (cost: low, 15 min):
- Aggiornare `docs/EDITOR-HANDOFF.md` v3.0 con sezione "Pagina vs Tassonomia vs Archive CPT — dove modificare cosa"
- Per ogni dei 15 URL Elena: documentare admin path esplicito + che cosa è editabile e cosa no
- Aggiungere nota onboarding: alcune sezioni (`/chi-siamo/team/`, `/chi-siamo/risultati/`, copy hub `/aree-di-pratica/` etc.) sono **hardcoded by design** per branded copy stabilità

**Option B — SCF migration tier-2** (cost: high, 90-120 min):
- Aggiungere SCF tab "Hub Pages" con field per H1, eyebrow, intro per ognuno dei 3 hub Page (chi-siamo, aree-di-pratica, risorse). Refactor templates `page-*-hub.php` per usare i field.
- Aggiungere SCF tab "Archive CPT Headers" per archive-avvocato + archive-saltelli_caso. Refactor i 2 template.
- Risultato: Elena può modificare TUTTO (anche copy hub PHP-hardcoded oggi).
- Trade-off: aggiunge ~12 nuovi SCF field, rende Saltelli Settings più affollato (10 → 11/12 tab). Probabile ROI basso se Elena edita raramente queste pagine.

**Recommendation**: Option A (documentazione) per Wave 4.7.fix.2; Option B candidato per Wave futura se Elena lamenta esplicitamente.

### Priority 4 — Categoria D: Sticky WhatsApp + footer tier-1

**Sticky WhatsApp message editor** (cost: ~10 min):
- Aggiungere SCF field `whatsapp_message_default` (text, default `'Ciao, %s sul vostro sito. Vorrei una consulenza.'` con instruction "Usa `%s` per inserire il contesto della pagina").
- Refactor `header.php:160-165` per leggere via `saltelli_option`.

**Footer tier-1 aree** (cost: ~15 min):
- Aggiungere SCF tab "Footer Aree" con repeater `tier1_aree` (3 sub-fields: numero, label, url). Default = current 3 hardcoded.
- Refactor `footer.php:397-401` per leggere repeater.

**Combined cost P4**: ~25 min.

---

## ⏱️ Stima costi totali Wave 4.7.fix.2 vero

| Priority | Categoria | Tempo Code | Multi-agent? | Risk |
|---|---|---|---|---|
| **P1** | A — `studio_body` JSON migration + reseed + template refactor | ~30-45 min | N | Low (DEC-029 pattern già usato) |
| **P2** | C — Menu rebuild + slug rename `risultati` + redirect 301 | ~60-90 min | possible (1 agent menu, 1 agent redirect) | Medium (SEO impact da validare con cliente) |
| **P3** | B — `EDITOR-HANDOFF.md` v3.0 documentazione | ~15 min | N | None |
| **P4** | D — Sticky WhatsApp message + footer_tier1 aree SCF | ~25 min | N | Low |
| | **TOTAL** | **~130-175 min** | | |

**Confronto sequenziale separato** (3-4 sessioni Wave separate): probabile ~210-260 min totali. Wave 4.7.fix.2 unified: -25-30%.

**Scope minimum proposto**: P1 + P2 + P3 (skip P4 oppure come addendum). Tempo: ~105-150 min.
**Scope full proposto**: P1 + P2 + P3 + P4. Tempo: ~130-175 min.

---

## 🚦 Open items per orchestratore

1. **Decision Duccio scope Wave 4.7.fix.2**:
   - **Minimum**: P1 (studio_body) + P2 (menu rebuild + rename risultati) + P3 (EDITOR-HANDOFF v3)
   - **Full**: + P4 (sticky WhatsApp + footer_tier1)
   - **Tier-2 escluso da default**: Option B Sezione B (SCF tab Hub Pages + Archive CPT Headers) — Wave futura se Elena lamenta
2. **Validazione decisione slug rename con cliente** (Duccio già autorizzato per `risultati` → `casi-rappresentativi`):
   - Conferma: l'intent è modificare `has_archive` del CPT saltelli_caso, oppure creare Page WP `/chi-siamo/casi-rappresentativi/` che sovrappone all'archive?
   - Lista altri redirect 301 legacy (es. `/competenze/`, `/faq/`, `/costi/`, `/tipo-area/*/`) da inserire — SEO impact non-trascurabile
3. **Validazione manuale 4-5 candidati Phase 3b** prima di lanciare P1: distinguere placeholder UX legittimi da content fallback editoriali. Senza questo, P1 rischia falsi positivi.
4. **Sub-decision footer hardcoded blocks** (D.1):
   - `$ftr_tier1` (3 aree footer): SCF migration o mantenere hardcoded?
   - `taxonomy-tipo-area.php` UX strings: SCF migration o mantenere hardcoded?

---

## 🔗 Riferimenti

- **DEC-040-COMPLETED** (Wave 4.7.fix.1) — `v1.3.7-wave4-7-fix-1-scf-url-validation`
- **DEC-039-COMPLETED** (Wave 4.7.fix) — `v1.3.6-wave4-7-fix-scf-migration`
- **DEC-029** (Wave 4.6 origin fallback pattern)
- **DEC-021** (URL audit-aligned 17 cliente-firmato)
- **CMS Diagnosis Round 2 REPORT.md** (2026-05-08)
- **Bug Duccio 2026-05-08** (Studio Section editor vuoto)
- **Feedback Elena 2026-05-08** (15 URL "non trovo dove modificare" + blocchi ricorrenti)
- `inc/seed-theme-options.php` (Wave 4.7.fix Phase 2)
- `inc/helpers.php` `saltelli_option()`, `saltelli_field()`
- `acf-json/group_theme_options_v1.json` (53 field, 50 seedabili)
- Audit logs: `.claude/knowledge/audits/wave4-7-fix-2-investigation/`
- Prompt: `prompts/PROMPT_AGENT_WAVE4_7_FIX_2_INVESTIGATION_V2.md`
- `CLAUDE.md` — single source of truth

---

*Wave 4.7.fix.2 Investigation v2 · 8 phases completed · 4 categorie diagnosed · READ-ONLY · output → orchestratore decide scope & launch fix wave.*
