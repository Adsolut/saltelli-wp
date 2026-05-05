# Architecture — Studio Legale Saltelli WordPress Theme

> **Scope:** mappa tecnica del theme custom e del modello ACF. Risponde alla domanda *"se voglio cambiare X sul sito, dove devo andare nel codice e nel WP-Admin?"*.
> **Versione coperta:** `1.0.0-recovery-wave3-debug` (post Wave 3 + Debug & QA, pre Wave 4).
> **Audience:** sviluppatori, Claude Code agent, agency tech lead.

---

## 1. Mappa frontend → template files

| URL | Template PHP | Template-part | Quando è usato |
|---|---|---|---|
| `/` (home) | `front-page.php` | — | Homepage |
| `/lo-studio/` (slug `chi-siamo`) | `page.php` | `template-parts/page-chi-siamo.php` | Chi siamo / brand statement |
| `/avvocati/` | `archive-avvocato.php` | — | Archivio team |
| `/avvocati/{slug}/` | `single-avvocato.php` | — | Scheda singolo avvocato |
| `/competenze/` | `archive-competenza.php` | — | Archivio aree di pratica |
| `/competenze/{slug}/` | `single-competenza.php` | — | Pagina singola area di pratica |
| `/casi/` | `page.php` | `template-parts/page-casi.php` | Hub casi rappresentativi |
| `/costi/` | `page.php` | `template-parts/page-costi.php` | Costi e prima consulenza |
| `/contatti/` | `page.php` | `template-parts/page-contatti.php` | Contatti + mappa + form CF7 |
| `/faq/` | `page.php` | `template-parts/page-faq.php` | FAQ hub completo |
| `/come-lavoriamo/` | `page.php` | `template-parts/page-info-shared.php` | Info shared layout |
| `/prima-consulenza/` | `page.php` | `template-parts/page-info-shared.php` | Info shared layout |
| `/lavora-con-noi/` | `page.php` | `template-parts/page-info-shared.php` | Info shared layout |
| `/richiedi-preventivo/` | `page.php` | `template-parts/page-info-shared.php` | Info shared layout |
| `/guide-gratuite/` | `page.php` | `template-parts/page-info-shared.php` | Info shared layout |
| `/blog/` (e `/categoria/{slug}/`) | `home.php` | — | Archivio blog |
| `/blog/{slug-articolo}/` | `single.php` | — | Articolo blog |
| `/tipo-area/{slug}/` | `taxonomy-tipo-area.php` | — | Tax tipo-area (privati/imprese) |
| `/glossario-legale/` | `inc/wave3-glossario.php` (custom rendering) | — | Glossario A-Z |
| `/llms.txt`, `/robots.txt` | `inc/seo/ai-files.php` | — | File AI/SEO generati dinamicamente |
| `/?s=...` (search) | `search.php` | — | Risultati ricerca |
| Errore 404 | `404.php` | — | Pagina non trovata |

### Page router (`page.php` 79 righe)

`page.php` è solo un **router**: legge lo slug della pagina e include il template-part corrispondente.

```php
$slug = get_post_field('post_name', get_the_ID());
$template_map = [
    'costi'    => 'template-parts/page-costi.php',
    'casi'     => 'template-parts/page-casi.php',
    'contatti' => 'template-parts/page-contatti.php',
    'faq'      => 'template-parts/page-faq.php',
    'chi-siamo' => 'template-parts/page-chi-siamo.php',
    // 5 info-shared pages
    'come-lavoriamo', 'prima-consulenza', 'lavora-con-noi',
    'richiedi-preventivo', 'guide-gratuite' => 'template-parts/page-info-shared.php',
];
```

**Conseguenza**: ogni pagina custom in WP-Admin sotto **Pagine** → ha un template-part che la renderizza. Pagine fuori dalla mappa cadono nel default WordPress (raramente usato).

---

## 2. ACF Field Groups — il modello dati

Sono **16 Field Group** (`wp-content/themes/saltelli/acf-json/group_*.json`), ognuno legato a un target specifico (Page, CPT, Theme Options) tramite **location rule slug-based** (post Debug & QA fix).

### 2.1 — Field Group per Page WP custom (5 group)

| Field Group | Location rule | Page target | Fields | Renderizzato da |
|---|---|---|---|---|
| `group_costi_v1` | `page_slug == costi` | `/costi/` | 16 | `template-parts/page-costi.php` |
| `group_casi_v1` | `page_slug == casi` | `/casi/` | 10 | `template-parts/page-casi.php` |
| `group_contatti_v1` | `page_slug == contatti` | `/contatti/` | 10 | `template-parts/page-contatti.php` |
| `group_faq_v1` | `page_slug == faq` | `/faq/` | 10 | `template-parts/page-faq.php` |
| `group_info_shared_v1` | `page_slug == {come-lavoriamo \| prima-consulenza \| lavora-con-noi \| richiedi-preventivo \| guide-gratuite}` | 5 pagine | 16 | `template-parts/page-info-shared.php` |

⚠️ **Lezione del bug-04 Debug QA**: la location rule `page_slug ==` è custom (definita in `inc/acf-fields.php`), non standard ACF. Garantisce env-portability (locale Docker ID ≠ droplet ID, ma slug è stabile).

### 2.2 — Field Group per CPT (10 group)

| Field Group | Location rule | CPT | Fields | Cosa rappresenta |
|---|---|---|---|---|
| `group_avvocato_v1` | `post_type == avvocato` | avvocato (4 items) | 12 | Scheda profilo avvocato |
| `group_competenza_v1` | `post_type == competenza` | competenza (19 items) | 11 | Area di pratica (Tier-1 deep / Tier-2 lighter) |
| `group_caso_item_v1` | `post_type == saltelli_caso` | saltelli_caso (9) | 3 | Caso vinto rappresentativo |
| `group_faq_item_v1` | `post_type == saltelli_faq` | saltelli_faq (28) | 2 | FAQ singola |
| `group_modalita_item_v1` | `post_type == saltelli_modalita` | saltelli_modalita (3) | 4 | Modalità di consulenza (in pres./video/parere) |
| `group_scenario_item_v1` | `post_type == saltelli_scenario` | saltelli_scenario (3) | 4 | Scenario costi tipo |
| `group_principio_item_v1` | `post_type == saltelli_principio` | saltelli_principio (3) | 3 | Principio dello studio |
| `group_trust_item_v1` | `post_type == saltelli_trust` | saltelli_trust (4) | 2 | Trust signal (numero/credibilità) |
| `group_formazione_item_v1` | `post_type == saltelli_formazione` | saltelli_formazione (12) | 3 | Voce formazione/titolo avvocato |
| `group_guida_item_v1` | `post_type == saltelli_guida` | saltelli_guida (0) | 4 | Guida PDF scaricabile |

### 2.3 — Field Group Theme Options (1 group)

| Field Group | Location rule | Target | Fields | Cosa rappresenta |
|---|---|---|---|---|
| `group_theme_options_v1` | `options_page == saltelli-settings` | Saltelli — Settings | 32 (in 6 tab) | Brand, NAP, social, footer, mappa, CTA defaults |

I 6 tab del pannello Saltelli — Settings:

```
Studio Info  (10 field)  → indirizzo, telefono, email, P.IVA, ordine
Mappa        (2 field)   → latitudine, longitudine
Brand        (2 field)   → payoff, brand statement
Footer       (4 field)   → credit text, credit URL, newsletter toggle, provider
Social       (4 field)   → facebook, instagram, linkedin, twitter
CTA Defaults (4 field)   → CTA label, URL, trust signal, subline italic
```

---

## 3. Mappa WP-Admin sidebar → URL frontend

Questa è la tabella che Elena/Ludovica devono avere sotto gli occhi. È replicata anche in [`EDITOR-HANDOFF.md` §3](./EDITOR-HANDOFF.md#3-tldr--mappa-rapida-voglio-editare-x).

### Pagine modificabili

| Voce sidebar WP-Admin | Edita la pagina con URL frontend | Field Group attivo |
|---|---|---|
| Saltelli — Settings | (header/footer di tutte le pagine) | `group_theme_options_v1` |
| Pagine → Costi | `/costi/` | `group_costi_v1` |
| Pagine → Casi | `/casi/` | `group_casi_v1` |
| Pagine → Contatti | `/contatti/` | `group_contatti_v1` |
| Pagine → FAQ | `/faq/` | `group_faq_v1` |
| Pagine → Come lavoriamo | `/come-lavoriamo/` | `group_info_shared_v1` |
| Pagine → Prima consulenza | `/prima-consulenza/` | `group_info_shared_v1` |
| Pagine → Lavora con noi | `/lavora-con-noi/` | `group_info_shared_v1` |
| Pagine → Richiedi preventivo | `/richiedi-preventivo/` | `group_info_shared_v1` |
| Pagine → Guide gratuite (hub) | `/guide-gratuite/` | `group_info_shared_v1` |
| Pagine → Lo studio | `/lo-studio/` (= `/chi-siamo/`) | ❌ nessuno (body hardcoded in `page-chi-siamo.php`) |
| Avvocati → {Nome} | `/avvocati/{slug}/` | `group_avvocato_v1` |
| Aree di pratica → {Nome} | `/competenze/{slug}/` | `group_competenza_v1` |
| Articoli → {titolo} | `/blog/{slug}/` | (standard WP) |

### Moduli riutilizzabili (non hanno URL frontend dedicato — sono "inserti")

| Voce sidebar WP-Admin | Dove appare sul frontend | Field Group |
|---|---|---|
| FAQ | `/faq/` (tutte) + competenze Tier-1 (selezionate manualmente) | `group_faq_item_v1` |
| Casi rappresentativi | `/casi/` (tutti) + competenze (selezionate) + avvocati (selezionati) | `group_caso_item_v1` |
| Modalità consulenza | `/costi/` § 01 (tutte e 3) | `group_modalita_item_v1` |
| Scenari costi | `/costi/` § 02 (tutti e 3) | `group_scenario_item_v1` |
| Principi studio | `/lo-studio/` § 04 (tutti e 3) | `group_principio_item_v1` |
| Trust signals | `/costi/` § 05 (tutti e 4) | `group_trust_item_v1` |
| Formazione & Titoli | Schede avvocato (selezionate per profilo) | `group_formazione_item_v1` |
| Guide gratuite | `/guide-gratuite/` (tutte) | `group_guida_item_v1` |

---

## 4. WYSIWYG gaps — pain points conosciuti

Questa sezione è onesta sui **disallineamenti tra "quello che Elena vede sul sito" e "dove deve andare per editarlo"**. Materiale per il refactor futuro.

### Gap 1 — Blocchi visibili in pagina che NON sono editabili dalla scheda della stessa pagina

Esempio: Elena apre `/costi/` sul sito, vede 3 blocchi "Modalità di consulenza" (in presenza, video, parere). Poi va in WP-Admin → Pagine → Costi pensando di modificarli. **Non li trova lì.** Sono in **Modalità consulenza** (sidebar separata).

**Pagine impattate** (con voci modulari "esterne"):
- `/costi/` ha 3 modalità + 3 scenari + 4 trust signal in CPT separati
- `/casi/` ha 9 casi in CPT separato
- `/faq/` ha 28 FAQ in CPT separato
- `/lo-studio/` ha 3 principi in CPT separato
- 3 competenze Tier-1 hanno FAQ + casi selezionate da CPT separati

**Conseguenza pratica**: per modificare una sola FAQ visibile su `/faq/`, Elena deve:
1. Capire che le FAQ non sono nella pagina FAQ
2. Andare in **FAQ** sidebar
3. Trovare la FAQ giusta in mezzo a 28
4. Modificarla

In editing classico WP, la FAQ sarebbe stata semplicemente nel body della pagina. Qui è strutturata come dato, ma il costo cognitivo è alto.

### Gap 2 — `/lo-studio/` non ha Field Group ACF

La pagina chi-siamo ha il body **hardcoded** in `template-parts/page-chi-siamo.php`. Elena vede testo bello sul sito, va su WP-Admin → Pagine → Lo studio, **trova solo title + slug**. Niente body editabile.

**Reason**: in Wave 1 si è deciso di aprire ACF su 9 pagine custom, escludendo `/lo-studio/` perché ha layout JSX-style molto specifico. La scelta era "cuore del brand, non lo apriamo all'editing".

**Costo**: Elena chiede "come modifico il testo della pagina chi siamo?" → risposta "scrivere al tecnico". Pessima UX editor.

### Gap 3 — Bio estesa avvocati: doppio campo (ACF + post_content legacy)

Vedi [`EDITOR-HANDOFF.md` §6.1](./EDITOR-HANDOFF.md#61---attenzione-stato-attuale-delle-bio-estese-debt-editoriale-da-risolvere). 3 su 4 avvocati hanno la bio nel campo legacy `post_content`, non nel campo ACF "Bio estesa". C'è fallback graceful (il sito mostra qualunque dei due esista), ma quando Elena edita il campo ACF, lo trova vuoto.

**Reason**: il content migration (Step D pre-recovery) ha popolato `post_content` invece del campo ACF. Wave 1 ha creato il campo ACF, Wave 2 non l'ha popolato.

### Gap 4 — Linguaggio sidebar non sempre allineato

| Voce sidebar | Cosa la voce contiene davvero | Dovrebbe chiamarsi |
|---|---|---|
| **FAQ** | Domande frequenti modulari | OK |
| **Aree di pratica** | Le 19 competenze | OK |
| **Casi rappresentativi** | Casi vinti anonimizzati | OK |
| **Modalità consulenza** | I 3 setup (in presenza, video, parere) — appare solo su `/costi/` | "Modalità (Costi → 3 box)" sarebbe più chiaro |
| **Scenari costi** | I 3 esempi tipo — appare solo su `/costi/` | "Scenari (Costi → § 02)" |
| **Principi studio** | I 3 valori — appare solo su `/lo-studio/` | "Principi (Lo Studio → § 04)" |
| **Trust signals** | I 4 numeri di credibilità — appare solo su `/costi/` | "Trust Signals (Costi → § 05)" |
| **Formazione & Titoli** | Voci formazione — appaiono nelle schede avvocato | OK |
| **Guide gratuite** | PDF scaricabili | OK |

I 4 CPT "modulari" che appaiono **solo in 1 pagina specifica** sono il caso peggiore: l'astrazione "CPT riutilizzabile" non è giustificata se l'uso è singolo.

### Gap 5 — Drop-cap automatico vs edge case

Vedi `inc/setup.php` filter `the_content` per drop-cap automatico sul primo paragrafo. Quirk noti:
- Se il primo paragrafo inizia con un'immagine inline, drop-cap rotto
- Se il primo paragrafo è dentro un blockquote, drop-cap rotto
- Su mobile webkit Safari, drop-cap occasionalmente con baseline shift

---

## 5. CPT — i 8 Custom Post Types

Definiti in:
- `inc/cpt-avvocato.php` (avvocato)
- `inc/cpt-competenza.php` (competenza + tassonomia tipo-area)
- `inc/cpt-recovery.php` (8 CPT modulari Wave 0)

### Dettaglio CPT modulari (creati in Wave 0)

| CPT slug | Singolare | Plurale | Public? | Has archive? | Items |
|---|---|---|---|---|---|
| `avvocato` | Avvocato | Avvocati | ✅ | ✅ (`/avvocati/`) | 4 |
| `competenza` | Area di pratica | Aree di pratica | ✅ | ✅ (`/competenze/`) | 19 |
| `saltelli_caso` | Caso | Casi rappresentativi | ❌ private | ❌ | 9 |
| `saltelli_faq` | FAQ | FAQ | ❌ private | ❌ | 28 |
| `saltelli_modalita` | Modalità | Modalità consulenza | ❌ private | ❌ | 3 |
| `saltelli_scenario` | Scenario | Scenari costi | ❌ private | ❌ | 3 |
| `saltelli_principio` | Principio | Principi studio | ❌ private | ❌ | 3 |
| `saltelli_trust` | Trust signal | Trust signals | ❌ private | ❌ | 4 |
| `saltelli_formazione` | Formazione | Formazione & Titoli | ❌ private | ❌ | 12 |
| `saltelli_guida` | Guida | Guide gratuite | ✅ | ✅ (`/guide-gratuite/{slug}/`) | 0 |

**`public => false`** sui 7 CPT modulari significa: nessun URL pubblico individuale (es. non esiste `/saltelli_faq/{slug}/`). Sono solo "blocchi dati" inseriti dentro pagine madre.

### Tassonomie

| Slug | Plurale | Su quale CPT | Termini | Note |
|---|---|---|---|---|
| `tipo-area` | Tipo aree | `competenza` | privati, imprese | Filtro UX competenze |
| `topic` | Topic | `saltelli_faq` | tributario, lavoro, lgbtq, costi, procedurale, prima-consulenza | Raggruppamento FAQ |
| `categoria_caso` | Categoria casi | `saltelli_caso` | privati, imprese, contenzioso, altri | Filtro casi |
| `categoria_guida` | Categoria guide | `saltelli_guida` | tributario, lavoro, lgbtq, procedurale | Filtro guide |

---

## 6. Helper functions (`inc/helpers.php`)

Le funzioni helper più usate dai template:

| Funzione | Cosa fa |
|---|---|
| `saltelli_field($key, $post_id, $fallback = '')` | Wrapper safe per `get_field` con fallback hardcoded — **chiave del Wave 3 graceful fallback pattern** |
| `saltelli_option($key, $fallback = '')` | Wrapper per Theme Options (`get_field($key, 'option')`) |
| `saltelli_studio_data()` | Aggregato di tutti i dati Studio (NAP, social, brand) come array — fonte canonica |
| `saltelli_phone_e164($input)` | Normalizza telefono a formato E.164 (per click-to-call) |
| `saltelli_canonical_url()` | Compute URL canonica della richiesta corrente |
| `saltelli_seo_plugin_active()` | Detect se Yoast/Rank Math/AIOSEO sono attivi (per evitare duplicati) |

### Pattern di lettura ACF nei template

```php
// In single-avvocato.php
$bio_estesa = (string) saltelli_field('bio_estesa', $post_id, '');

if ($bio_estesa) :
    echo wp_kses_post($bio_estesa);
elseif (get_the_content()) :
    the_content();   // Fallback al post_content legacy (Step D)
endif;
```

Questo pattern di doppio fallback (`saltelli_field` con default + `the_content` come ultima rete) è **la ragione per cui il sito mostra contenuti anche quando ACF è vuoto**.

---

## 7. Schema JSON-LD

I partial schema sono in `inc/schema/`:

| Partial | Tipi schema emessi | Quando viene incluso |
|---|---|---|
| `partial-organization.php` | Organization, LegalService, LocalBusiness | Sempre (footer) |
| `partial-attorney.php` | Person, Attorney | Su `single-avvocato.php` |
| `partial-faqpage.php` | FAQPage | Su `/faq/` e su competenze Tier-1 con FAQ |
| `partial-article.php` | Article, BreadcrumbList | Su `single.php` (blog) |
| `partial-breadcrumb.php` | BreadcrumbList | Sempre (header) |
| `partial-contactpage.php` | ContactPage | Su `/contatti/` |

**Yoast schema graph** (Organization, WebPage, BreadcrumbList, WebSite) coabita con il theme. Il filter `wpseo_schema_graph` priority 13 (in `inc/seo/yoast-schema-extensions.php`) sovrascrive `sameAs` con i valori ACF Theme Options (lezione bug-02 Debug QA).

---

## 8. SEO + AI files

`inc/seo/`:

| File | Cosa fa |
|---|---|
| `meta-tags.php` | Emette canonical (sempre) + description + OG + Twitter (se nessun SEO plugin attivo) |
| `legacy-redirects.php` | 301 dai vecchi URL Elementor ai nuovi (es. `/lo-studio/` → `/chi-siamo/`) |
| `yoast-schema-extensions.php` | Filter su Yoast schema graph per coabitazione + sameAs override |
| `ai-files.php` | Genera dinamicamente `/llms.txt` + `/robots.txt` con allow esplicito ai bot AI |

---

## 9. Pattern di estensione

### Aggiungere una 20a area di pratica (competenza)

1. Crea termine `tipo-area` se nuovo
2. WP-Admin → Aree di pratica → New
3. Compila slug + title
4. Compila i campi ACF di `group_competenza_v1` (eyebrow, answer capsule, body, ecc.)
5. Tier-1 toggle se applicabile
6. Pubblica
7. (manuale) aggiungi al menu navigation se necessario

### Aggiungere una nuova pagina custom

⚠️ **Non banale**. Richiede:
1. Creare la pagina in WP-Admin con uno slug nuovo
2. Decidere quale Field Group riutilizzare (es. `group_info_shared_v1` per layout standard)
3. Aggiungere il nuovo slug alla location rule del Field Group (`acf-json/group_info_shared_v1.json`)
4. Verificare che il template `template-parts/page-info-shared.php` la renderizzi correttamente
5. Aggiungere la voce al menu navigation

Per pagine con layout completamente nuovo, serve creare un nuovo template-part + estendere `page.php` router. Lavoro tecnico, non solo CMS.

### Aggiungere un nuovo CPT modulare

1. Definire CPT in nuovo file `inc/cpt-{nome}.php` (require in `functions.php`)
2. Creare nuovo Field Group ACF JSON in `acf-json/group_{nome}_item_v1.json`
3. Inserire il rendering nel template-part della pagina madre
4. Popolare via WP-Admin (o via script `scripts/wave2-*.php` se bulk)

---

## 10. Flusso di deploy

Vedi `docs/DEPLOY.md` per il runbook completo.

Sintesi:
1. Branch dedicato `feat/{nome}` da `main`
2. Sviluppo + commit + push
3. Audit + merge `main` no-ff
4. Rsync delta theme files su droplet `/var/www/saltelli/wp-content/themes/saltelli/`
5. `wp cache flush` + `wp transient delete --all`
6. Smoke 21 URL HTTP 200

---

## 11. Cosa è cambiato nel Recovery v1.0 (per chi viene da pre-recovery)

| Pre-recovery (v0.x) | Post-recovery (v1.0) |
|---|---|
| `page.php` 1274 righe monolitiche | 79 righe + 6 template-parts |
| Modello dati hardcoded nei template | 16 ACF Field Group |
| Editing cliente impossibile | Cliente CMS-autonomous (WP-Admin) |
| Theme Options come constants PHP | ACF Theme Options (6 tab, 26 field) |
| Schema JSON-LD scattered | Centralizzato in `inc/schema/` (6 partials) |
| Bio estese hardcoded | ACF `bio_estesa` con fallback graceful a `post_content` |
| FAQ/casi/principi inline | 8 CPT modulari + relazioni many-to-one |

---

*Maintained by orchestrator (Claude in chat). Last updated: 2026-05-05.*
*Per il manuale operativo editoriale (audience NON tecnico) vedi [`EDITOR-HANDOFF.md`](./EDITOR-HANDOFF.md).*
