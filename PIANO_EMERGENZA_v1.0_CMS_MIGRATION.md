# PIANO EMERGENZA v1.0 RECOVERY — CMS Real Migration

> **Stato critico**: WP-Admin non gestibile da Elena/Ludovica.
> **Scoperta**: theme già architettato per ACF (26 custom fields previsti) ma plugin mai installato.
> **Tempo recovery**: 6-8h elapsed con 3-wave parallel (era 12-16h stimato).
> **Output**: cliente AUTONOMO sul WP-Admin entro 1.5 giorni.

---

## 🎯 STRATEGIA Multi-Wave Parallel

```
WAVE 0 — Setup ACF Pro (sequenziale, 30 min)
  └─ Foundation: install + license ACF Pro su local + droplet

WAVE 1 — ACF Schema Setup (parallel × 3 agent, 2-3h)
  ├─ Agent A: Field Groups page custom (costi, casi, contatti, faq, info-shared)
  ├─ Agent B: Field Groups CPT (avvocato, competenza)
  └─ Agent C: Theme Options (contatti studio, NAP, social, brand)

WAVE 2 — Migration Content (parallel × 3 agent, 2-3h)
  ├─ Agent D: Migrate /costi/, /casi/, /contatti/ hardcoded → ACF
  ├─ Agent E: Migrate /faq/, /come-lavoriamo/, /prima-consulenza/, /lavora-con-noi/, 
  │           /guide-gratuite/, /richiedi-preventivo/ → ACF
  └─ Agent F: Migrate single-avvocato (4 lawyer) + single-competenza (3 tier-1) → ACF

WAVE 3 — Refactor Template + Docs (sequenziale, 1.5-2h)
  ├─ Refactor page.php (1274 → 350 righe, partials)
  ├─ Refactor single-avvocato.php / single-competenza.php
  ├─ Theme Options panel UI editor-friendly
  └─ Documentazione editor-handoff.md per Elena/Ludovica
```

---

## 🔒 Hard rules CRITICHE

| Rule | Decisione |
|---|---|
| **NESSUNA modifica visiva sul frontend** | refactor backend-only, output identico |
| **Backup pre-Wave** mandatory (DB + theme files) | safety prima recovery |
| **ACF JSON sync** (auto-export field groups) | versioning git-friendly |
| **Helper function `saltelli_field()`** già esistente, RIUSARE | no rewrite |
| **Fallback gracioso**: se ACF empty → default hardcoded (compat) | safety |
| **Frontend smoke test** 17 URL dopo OGNI wave | no regression |
| **NO modifica tokens.css o foundation CSS** | locked |
| **NON sovrascrivere** _thumbnail_id Emiliano + foto upload | content protetto |

---

## WAVE 0 — Setup ACF Pro (30 min, SEQUENZIALE PRIMA DI TUTTO)

### 0.1 — License + Install ACF Pro

ACF Pro €159/anno per 1 sito. Alternative:

```
OPZIONE A) ACF Pro Licenza Adsolut (raccomandato)
  Adsolut acquista licenza ACF Pro 1-site (€159/anno)
  Riusabile per progetti futuri (license multi-site disponibile a €299)
  
OPZIONE B) ACF Free + Custom Fields manuali
  Repeater feature → simulato con post_meta serializzato (workaround)
  Flexible Content → non disponibile, switch a Gutenberg blocks
  
OPZIONE C) Switch totale a Gutenberg Custom Blocks (no ACF)
  Più tempo (rewrite blocchi PHP → React)
  Future-proof WordPress nativi
```

**Raccomandazione**: A (ACF Pro). Più veloce + cliente avrà supporto a vita.

### 0.2 — Install command

```bash
# LOCAL
docker compose run --rm wpcli plugin install advanced-custom-fields-pro --activate
# OPPURE upload manuale ZIP + license key in WP-Admin

# DROPLET
ssh deploy@178.62.207.50 "
  sudo -u www-data wp plugin install advanced-custom-fields-pro --activate \
    --path=/var/www/saltelli/htdocs
"
```

License key: ottenuta da advancedcustomfields.com dopo acquisto.

### 0.3 — Configura ACF JSON sync (versioning git)

In wp-content/themes/saltelli/, crea `acf-json/` directory:
```bash
mkdir -p wp-content/themes/saltelli/acf-json
chmod 755 wp-content/themes/saltelli/acf-json
```

In `inc/acf-fields.php` aggiungi (se non già):
```php
// ACF JSON auto-save in theme (git-friendly)
add_filter('acf/settings/save_json', function($path) {
    return get_stylesheet_directory() . '/acf-json';
});

add_filter('acf/settings/load_json', function($paths) {
    unset($paths[0]);
    $paths[] = get_stylesheet_directory() . '/acf-json';
    return $paths;
});
```

### 0.4 — Verify ACF active

```bash
docker compose run --rm wpcli plugin status advanced-custom-fields-pro 2>&1 | tail -3
# Atteso: Active

docker compose run --rm wpcli eval "echo function_exists('get_field') ? 'OK' : 'FAIL';"
# Atteso: OK
```

### 0.5 — Backup pre-recovery

```bash
# DB backup
docker compose exec saltelli-db mysqldump -u root -p saltelli > /tmp/saltelli-pre-v1-recovery.sql

# Theme backup
tar -czf /tmp/saltelli-theme-pre-v1.tar.gz wp-content/themes/saltelli/

# Droplet
ssh deploy@178.62.207.50 "
  cd /var/www/saltelli
  sudo mysqldump saltelli > /tmp/saltelli-droplet-pre-v1.sql
  sudo tar -czf /tmp/saltelli-theme-droplet-pre-v1.tar.gz htdocs/wp-content/themes/saltelli/
"
```

---

## WAVE 1 — ACF SCHEMA SETUP (parallel × 3 agent, 2-3h)

Tre agenti lavorano in PARALLELO via tmux su Field Groups separati. Output: ACF JSON files in `acf-json/` (git-tracked).

### 🤖 Agent A — Field Groups page WP custom (~2h)

**Scope**: register Field Groups per le page WP che hanno hardcoded content nel template.

```
PAGE TARGET (5 page WP):
  costi (id 2695)         — 7 sezioni Sessione 2
  casi (id 2699)          — filter + 8-10 casi list
  contatti (id ?)         — form fields config + map coords
  faq (id 2705)           — 6 topic groups + 28 Q/A
  + info-shared (location_rule per 5 page):
    guide-gratuite, come-lavoriamo, prima-consulenza,
    lavora-con-noi, richiedi-preventivo
```

**Field Group esempio /costi/**:

```php
[
  'key' => 'group_costi_v1',
  'title' => 'Costi — sezioni',
  'location' => [
    [['param' => 'page', 'operator' => '==', 'value' => 'costi']]
  ],
  'fields' => [
    // Hero
    ['key'=>'field_costi_hero_eyebrow', 'label'=>'Hero · Eyebrow', 'name'=>'hero_eyebrow', 'type'=>'text', 'default_value'=>'§ Servizio · Costi'],
    ['key'=>'field_costi_hero_h1_pre', 'label'=>'Hero · H1 prefix', 'name'=>'hero_h1_pre', 'type'=>'text', 'default_value'=>'Costi e prima'],
    ['key'=>'field_costi_hero_h1_em', 'label'=>'Hero · H1 italic', 'name'=>'hero_h1_em', 'type'=>'text', 'default_value'=>'consulenza.'],
    ['key'=>'field_costi_hero_lede', 'label'=>'Hero · Lede italic', 'name'=>'hero_lede', 'type'=>'textarea', 'rows'=>2],
    
    // Hero trust box (aside DX)
    ['key'=>'field_costi_aside_eyebrow', 'label'=>'Aside · Eyebrow', 'name'=>'aside_eyebrow', 'type'=>'text'],
    ['key'=>'field_costi_aside_h3', 'label'=>'Aside · H3', 'name'=>'aside_h3', 'type'=>'text'],
    ['key'=>'field_costi_aside_p', 'label'=>'Aside · Paragrafo', 'name'=>'aside_p', 'type'=>'textarea'],
    ['key'=>'field_costi_aside_cta_label', 'label'=>'Aside · CTA label', 'name'=>'aside_cta_label', 'type'=>'text'],
    ['key'=>'field_costi_aside_cta_url', 'label'=>'Aside · CTA URL', 'name'=>'aside_cta_url', 'type'=>'url'],
    
    // § 01 Come funziona — REPEATER 3 modalità
    ['key'=>'field_costi_come', 'label'=>'§ 01 Come funziona', 'name'=>'come_modalita', 'type'=>'repeater', 
      'min'=>3, 'max'=>3,
      'sub_fields'=>[
        ['key'=>'field_costi_come_num', 'label'=>'Numero', 'name'=>'num', 'type'=>'text'],
        ['key'=>'field_costi_come_label', 'label'=>'Label modalità', 'name'=>'label', 'type'=>'text'],
        ['key'=>'field_costi_come_title', 'label'=>'Titolo', 'name'=>'title', 'type'=>'text'],
        ['key'=>'field_costi_come_body', 'label'=>'Body', 'name'=>'body', 'type'=>'textarea'],
        ['key'=>'field_costi_come_trust', 'label'=>'Trust signal', 'name'=>'trust', 'type'=>'text'],
      ],
    ],
    
    // § 02 Tre scenari — REPEATER
    ['key'=>'field_costi_scenari', 'label'=>'§ 02 Tre scenari', 'name'=>'scenari', 'type'=>'repeater',
      'min'=>3, 'max'=>3,
      'sub_fields'=>[
        ['key'=>'field_costi_scenari_num', 'label'=>'Numero', 'name'=>'num', 'type'=>'text'],
        ['key'=>'field_costi_scenari_label', 'label'=>'Label scenario', 'name'=>'label', 'type'=>'text'],
        ['key'=>'field_costi_scenari_body', 'label'=>'Body', 'name'=>'body', 'type'=>'wysiwyg'],
        ['key'=>'field_costi_scenari_trust', 'label'=>'Trust signal', 'name'=>'trust', 'type'=>'text'],
      ],
    ],
    
    // § 03 Come calcoliamo — body editorial + 3 fattori
    ['key'=>'field_costi_calc_body', 'label'=>'§ 03 Body editorial', 'name'=>'calc_body', 'type'=>'wysiwyg'],
    ['key'=>'field_costi_calc_fattori', 'label'=>'§ 03 Fattori', 'name'=>'calc_fattori', 'type'=>'repeater',
      'min'=>3, 'max'=>3,
      'sub_fields'=>[
        ['key'=>'field_costi_calc_label', 'label'=>'Label fattore', 'name'=>'label', 'type'=>'text'],
        ['key'=>'field_costi_calc_title', 'label'=>'Titolo', 'name'=>'title', 'type'=>'text'],
        ['key'=>'field_costi_calc_desc', 'label'=>'Descrizione', 'name'=>'desc', 'type'=>'textarea'],
      ],
    ],
    
    // § 04 FAQ — REPEATER (riusabile cross-page)
    ['key'=>'field_costi_faq', 'label'=>'§ 04 FAQ', 'name'=>'faq', 'type'=>'repeater',
      'min'=>5, 'max'=>10,
      'sub_fields'=>[
        ['key'=>'field_costi_faq_q', 'label'=>'Domanda', 'name'=>'q', 'type'=>'text'],
        ['key'=>'field_costi_faq_a', 'label'=>'Risposta', 'name'=>'a', 'type'=>'textarea'],
      ],
    ],
    
    // § 05 Trust signals — REPEATER 4
    ['key'=>'field_costi_trust', 'label'=>'§ 05 Trust signals', 'name'=>'trust_plates', 'type'=>'repeater',
      'min'=>4, 'max'=>4,
      'sub_fields'=>[
        ['key'=>'field_costi_trust_label', 'label'=>'Label', 'name'=>'label', 'type'=>'text'],
        ['key'=>'field_costi_trust_value', 'label'=>'Valore', 'name'=>'value', 'type'=>'text'],
      ],
    ],
    
    // CTA finale
    ['key'=>'field_costi_cta_eyebrow', 'label'=>'CTA · Eyebrow', 'name'=>'cta_eyebrow', 'type'=>'text'],
    ['key'=>'field_costi_cta_h2', 'label'=>'CTA · H2', 'name'=>'cta_h2', 'type'=>'text'],
    ['key'=>'field_costi_cta_p', 'label'=>'CTA · Paragrafo', 'name'=>'cta_p', 'type'=>'textarea'],
    ['key'=>'field_costi_cta_label', 'label'=>'CTA · Bottone label', 'name'=>'cta_label', 'type'=>'text', 'default_value'=>'Prenota un incontro →'],
    ['key'=>'field_costi_cta_url', 'label'=>'CTA · Bottone URL', 'name'=>'cta_url', 'type'=>'url', 'default_value'=>'/contatti/'],
    ['key'=>'field_costi_cta_trust', 'label'=>'CTA · Trust line', 'name'=>'cta_trust', 'type'=>'text'],
  ],
]
```

Pattern simile per **/casi/, /contatti/, /faq/, /info-shared/**.

Output: 5 ACF JSON files in `acf-json/`.

### 🤖 Agent B — Field Groups CPT (~1.5h)

**Scope**: Field Groups per single CPT (avvocato, competenza, post blog).

```
CPT avvocato (4 lawyer):
  - hero_role (Founding Partner · Tributarista)
  - bio_breve (1 riga)
  - bio_estesa (WYSIWYG)
  - foto_ritratto (image)
  - ruolo_breve, qualifica
  - email_pubblica, telefono_pubblico, whatsapp
  - same_as_linkedin
  - specializzazioni (text repeater)
  - aree_competenza_correlate (post_object multiple, target competenza CPT)
  - formazione (repeater: anno, titolo, ente)
  - casi_rappresentativi (repeater: id_label, descrizione, outcome_label)

CPT competenza (3 tier-1 + 16 tier-2):
  - is_tier_1 (true_false)
  - tier_label (Approfondimento · Per le imprese)
  - subtitle (Cartelle, accertamenti, contenzioso.)
  - answer_capsule (textarea, GEO answer 50-60 words)
  - body_extended (wysiwyg)
  - lead_attorneys (post_object, target avvocato CPT)
  - casi_rappresentativi (repeater)
  - faq (repeater Q&A)
  - articoli_correlati (post_object, target post)
  - cta_label, cta_url

POST blog (riusare 326 esistenti):
  - reading_time (number, minuti)
  - avvocato_autore (post_object)
  - lead_breve (textarea)
```

Output: 3 ACF JSON files (avvocato, competenza, post blog).

### 🤖 Agent C — Theme Options (~30 min)

**Scope**: Options Page con tutti i settings globali.

```
Studio Settings (global, used cross-template):
  - studio_indirizzo (textarea)
  - studio_orari (text repeater)
  - studio_telefono_pubblico
  - studio_email
  - studio_pec
  - studio_piva
  - studio_ordine_avvocati
  - studio_coordinate_lat/lng
  - studio_iubenda_url
  
Brand Settings:
  - brand_payoff (text)
  - brand_statement_short (textarea)
  - brand_atelier_legale_text (textarea)
  
Footer Settings:
  - footer_newsletter_enabled (true_false)
  - footer_newsletter_provider (select: brevo / static / none)
  - footer_credit_text
  
Social Links:
  - social_instagram, linkedin, twitter, facebook
  
CTA Defaults:
  - cta_default_label (default "Prenota un incontro →")
  - cta_default_url (default "/contatti/")
```

Output: ACF JSON `group_theme_options.json`.

---

## WAVE 2 — Content Migration (parallel × 3 agent, 2-3h)

Tre agenti migrano content hardcoded → ACF fields via WP-CLI eval scripts.

### 🤖 Agent D — Migration page WP set 1 (~2h)

Target: `/costi/`, `/casi/`, `/contatti/`.

Pattern script per /costi/:

```bash
docker compose run --rm wpcli eval-file scripts/migrate-costi-acf.php
```

`scripts/migrate-costi-acf.php`:
```php
$page_id = get_page_by_path('costi')->ID;

// Hero
update_field('hero_eyebrow', '§ Servizio · Costi', $page_id);
update_field('hero_h1_pre', 'Costi e prima', $page_id);
update_field('hero_h1_em', 'consulenza.', $page_id);
update_field('hero_lede', 'Trenta minuti gratuiti per ascoltarci, valutare insieme...', $page_id);

// Aside
update_field('aside_eyebrow', '§ Prima consulenza', $page_id);
update_field('aside_h3', 'GRATUITA · 30 MINUTI · IN STUDIO O ONLINE', $page_id);
// ... etc

// § 01 Come funziona — Repeater
update_field('come_modalita', [
    ['num' => '01 / Modalità classica', 'label' => 'IN STUDIO', 'title' => 'Vieni a Chiaia', 'body' => '...', 'trust' => 'Caffè incluso'],
    ['num' => '02 / Modalità remota', 'label' => 'ONLINE', 'title' => 'Videocall riservata', 'body' => '...', 'trust' => 'Stesso valore...'],
    ['num' => '03 / Modalità rapida', 'label' => 'TELEFONICA', 'title' => 'Per casi semplici', 'body' => '...', 'trust' => 'Massimo 30 min'],
], $page_id);

// § 02 Tre scenari, § 03 calcoliamo, § 04 FAQ, § 05 trust, CTA finale
// ... (5 più update_field con repeater)

echo "Costi migrated: " . $page_id;
```

Stesso pattern per /casi/, /contatti/.

### 🤖 Agent E — Migration page WP set 2 (~2h)

Target: `/faq/`, `/come-lavoriamo/`, `/prima-consulenza/`, `/lavora-con-noi/`, `/guide-gratuite/`, `/richiedi-preventivo/`.

5 info-page riusano stesso ACF schema (location_rule multi-page).

### 🤖 Agent F — Migration CPT (~2h)

Target: 4 avvocati + 3 tier-1 competenze.

Esempio Emiliano:
```php
$emiliano_id = 2660;

update_field('hero_role', 'Founding Partner · Cassazionista', $emiliano_id);
update_field('bio_breve', 'Avvocato cassazionista. Diritto tributario, contenzioso fiscale.', $emiliano_id);
update_field('formazione', [
    ['anno' => '2024', 'titolo' => 'Cassazionista', 'ente' => 'Iscritto albo speciale Cassazione'],
    ['anno' => '2008', 'titolo' => 'Avvocato', 'ente' => 'Ordine degli Avvocati di Napoli'],
    ['anno' => '2003', 'titolo' => 'Laurea Giurisprudenza', 'ente' => 'Università Federico II'],
], $emiliano_id);

update_field('casi_rappresentativi', [
    ['id_label' => 'Cassazione · 2024', 'descrizione' => 'Annullamento cartella €240k...', 'outcome_label' => 'Annullamento integrale'],
    // ... 3 casi
], $emiliano_id);

update_field('aree_competenza_correlate', [/* 6 IDs competenza */], $emiliano_id);
update_field('specializzazioni', [
    'Diritto tributario',
    'Cassazione',
    'Cartelle esattoriali',
    // ... 5 tags
], $emiliano_id);
```

Stesso pattern per Fabiana, Antonia, Stefano.

Per competenze tier-1 (tributario, lavoro, LGBTQ+):
```php
update_field('answer_capsule', 'Lo Studio Saltelli & Partners assiste...', $tributario_id);
update_field('lead_attorneys', [2660], $tributario_id);  // Emiliano
update_field('faq', [/* 5 Q&A */], $tributario_id);
update_field('casi_rappresentativi', [/* 3 casi tier-1 */], $tributario_id);
```

---

## WAVE 3 — Refactor Template + Docs (sequenziale, 1.5-2h)

### 3.1 — Refactor page.php (1274 righe → ~350 righe)

**Strategia**: estrai blocchi `is_page()` in template-parts riusabili.

```
wp-content/themes/saltelli/
  template-parts/
    page-costi.php           (~150 righe, usa get_field() + render)
    page-casi.php
    page-contatti.php
    page-faq.php
    page-info-shared.php     (riusato per 5 info pages)
    page-chi-siamo.php       (mantieni hardcoded MA documentato)
```

`page.php` finale:
```php
<?php
get_header();

if (is_page('costi')) {
    get_template_part('template-parts/page', 'costi');
} elseif (is_page('casi')) {
    get_template_part('template-parts/page', 'casi');
} elseif (is_page('contatti')) {
    get_template_part('template-parts/page', 'contatti');
} elseif (is_page('faq')) {
    get_template_part('template-parts/page', 'faq');
} elseif (is_page(['guide-gratuite', 'come-lavoriamo', 'prima-consulenza', 'lavora-con-noi', 'richiedi-preventivo'])) {
    get_template_part('template-parts/page', 'info-shared');
} elseif (is_page('chi-siamo')) {
    get_template_part('template-parts/page', 'chi-siamo');
} else {
    // Default fallback
    while (have_posts()) {
        the_post();
        the_content();
    }
}

get_footer();
```

### 3.2 — Refactor single-avvocato.php / single-competenza.php

Stesso pattern: usa `get_field()` invece di hardcoded fallback.

### 3.3 — Documentazione editor-handoff.md

Crea `docs/EDITOR-HANDOFF.md` per Elena/Ludovica:

```markdown
# Saltelli WordPress — Guida editor

Benvenute Elena e Ludovica! Questa guida vi mostra come modificare ogni 
sezione del sito direttamente da WP-Admin senza sviluppatore.

## 1. Login WP-Admin
URL: studiolegalesaltelli.it/wp-admin
Credenziali: [Duccio fornisce]

## 2. Modificare /costi/
1. Pages → Costi e prima consulenza → Modifica
2. Scroll giù: Vedi sezioni "Hero", "§ 01 Come funziona", ecc.
3. Modifica i campi che vuoi (text, textarea, repeater)
4. Salva → frontend aggiornato in 5 secondi

## 3. Aggiungere una nuova FAQ
1. Pages → Domande frequenti → Modifica
2. Sezione FAQ → trova topic (es. "Tributario")
3. Click "+ Aggiungi domanda" → compila Q + A
4. Salva → schema FAQPage Google aggiornato auto

## 4. Modificare bio Emiliano
1. Avvocati → Emiliano Saltelli → Modifica
2. Bio estesa (editor WYSIWYG con bold/italic/link)
3. Formazione (repeater anno/titolo/ente)
4. Casi rappresentativi (repeater)
5. Salva

## 5. Cambiare CTA "Prenota un incontro" cross-site
1. Saltelli — Settings (menu sidebar)
2. CTA Defaults → CTA Default Label → modifica
3. Salva → applica su TUTTE le page

## 6. Aggiornare orari studio
1. Saltelli — Settings → Studio Settings
2. Orari → modifica
3. Salva → footer e contatti aggiornati cross-site

## Bisogno di aiuto?
Contatta: info@adsolut.it
```

---

## DELIVERABLE FINALE

Report: `.claude/knowledge/recovery/v1.0-CMS-MIGRATION.md`

```markdown
# v1.0 CMS Real Migration — Recovery Report

## Score: 16/16 task PASS

## Metrics
- ACF Field Groups creati: 12 (5 page + 3 CPT + Theme Options + 3 generic)
- Custom fields totali: ~80
- Template-parts creati: 6 (riusabili)
- page.php: 1274 → 350 righe (-72%)
- Hardcoded content: 80% → 0% (eccetto fallback grazioso)
- Documentazione editor-handoff.md: 1 doc completa

## Cliente autonomo
- ✓ /costi/, /casi/, /contatti/, /faq/, info pages: editabili da WP-Admin
- ✓ Bio + formazione + casi 4 avvocati: editabili
- ✓ Tier-1 competenze (3 page): editabili (FAQ, body, casi)
- ✓ Theme Options: orari, contatti, social, CTA defaults editabili
- ✓ ACF JSON sync: tutto versionato in git

## Tempo totale recovery
WAVE 0 (setup): 30 min
WAVE 1 (schema): ~2h elapsed (parallel × 3)
WAVE 2 (migration): ~2h elapsed (parallel × 3)
WAVE 3 (refactor): ~2h sequenziale
TOTAL: ~6.5h elapsed

## Next
Production cut v1.0 production-ready
Sign-off cliente con guida editor
```

---

## ⚠️ Cosa serve da TE (Duccio) per partire

### Decisione 1 — License ACF Pro

ACF Pro €159/anno. Se OK, procedo con piano.
Se preferisci ACF Free, dimmi (workaround più lungo + Repeater simulato).

### Decisione 2 — Backup safety

Confermo che PRIMA di Wave 0 facciamo backup DB + theme files.
Tu ok?

### Decisione 3 — Comunicazione cliente

Il sito staging.studiolegalesaltelli.it resta funzionante durante recovery.
Comunichiamo Avv. Saltelli che: "lavoriamo backend per editor handoff, 
sito visibile ma non production cut fino a v1.0".

### Decisione 4 — Tmux multi-agent setup

Riusi script wave3-launch.sh adattato per 3 wave o vuoi setup nuovo?

---

Quando confermi le 4 decisioni, lancio i 3 prompt agent paralleli (Wave 0+1) + 
ti consegno gli script tmux dedicati.

Tempo realistic per cliente autonomo: **6.5h elapsed da inizio Wave 0**.
