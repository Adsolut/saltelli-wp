# 🎯 Claude Code Agent — Wave 4.6 CMS Editability Closure (v1.0)

> **Audience**: Claude Code agent in nuova sessione terminale.
> **Branch parent**: `main` (post-Wave 4.5 mergeata, tag `v1.3.1-wave4-5-critical-css-webp`)
> **Branch nuovo**: `feat/wave4-6-cms-editability`
> **Theme version target**: `1.3.2-wave4-6-cms-editability`
> **Scope**: chiusura 20+ gap CMS che bloccano editorialità Elena (ACF field mancanti + hardcoded contents da estrarre + dead fields da cablare)
> **Tempo stimato**: ~3-4h (5 phases)
> **Riferimento decisione**: DEC-028 lancio Wave 4.6 (audit gestibilità CMS rivela ~25% sito hardcoded fallback)

---

## 🎯 Tu sei

Claude Code agent dedicato a chiudere i gap di **editorialità CMS** identificati dall'audit orchestratore post-Wave 4.5. Il problema: il codice tema chiama 20 field via `saltelli_option()` con fallback hardcoded, ma quei field NON esistono in ACF Theme Options. Risultato: Elena vede sempre i fallback e non può modificarli da WP Admin.

Wave 4.6 è **chirurgica e ad alto valore editoriale**. Scope:
1. Aggiungere 20 field ACF mancanti in `group_theme_options_v1` (4 nuovi tab UI: Hero, Studio Section, Team/Casi, Press)
2. Estrarre hardcoded contents da `template-parts/page-lo-studio.php` (timeline + founding) in nuovo `group_lo_studio_v1` ACF location-rule sulla page
3. Cablare i 15 field ACF "dead" attualmente non chiamati nei template (footer credit, social URLs, schema Organization lat/lng)
4. Fix discrepanza `is_tier_1_focus` → `is_tier_1` in single-competenza.php (2 caratteri)
5. **NO breaking change**: tutti i nuovi field hanno default ACF identici ai fallback hardcoded attuali. Se Elena non popola, il sito renderizza ESATTAMENTE come prima.

---

## 📚 Letture obbligatorie (nell'ordine)

1. **`CLAUDE.md`** — single source of truth
2. **Questo prompt** end-to-end
3. **File da modificare** (in lettura preliminare):
   - `wp-content/themes/saltelli/acf-json/group_theme_options_v1.json` (esistente, da estendere)
   - `wp-content/themes/saltelli/front-page.php` (chiama 16 saltelli_option)
   - `wp-content/themes/saltelli/footer.php` (chiama 9 saltelli_option in fallback chain)
   - `wp-content/themes/saltelli/header.php` (chiama 2 saltelli_option)
   - `wp-content/themes/saltelli/template-parts/page-lo-studio.php` (timeline hardcoded + founding fallback)
   - `wp-content/themes/saltelli/template-parts/page-contatti.php` (chiama studio_* + contact_* alias)
   - `wp-content/themes/saltelli/single-competenza.php` (mismatch is_tier_1_focus)
   - `wp-content/themes/saltelli/inc/helpers.php` (`saltelli_option()`, `saltelli_studio_data()`, `saltelli_homepage_cases()`, `saltelli_press_outlets()`)

---

## 🔒 Hard rules (non negotiabili)

1. **NO commit su main**. Sempre su `feat/wave4-6-cms-editability`.
2. **NO breaking change**: ogni nuovo field ACF DEVE avere `default_value` identico al fallback hardcoded del template. Se Elena non popola, il sito deve renderizzare uguale a prima.
3. **NO regression smoke** Wave 5 (33+18+33 PASS), Wave 6 (21 + render checks PASS), Wave 4 (5 headers PASS), Wave 4.5 (per-template critical CSS + WebP picture render).
4. **NO modifica `wave5-blog-rewrites.php`**, `inc/perf.php`, `inc/security.php`, `inc/critical-css.php`. Sono Wave 4.x stable.
5. **NO modifica template-parts Wave 6** (`trust-bar.php`, `mobile-sticky-bar.php`, `mini-form.php`, `testimonials-block.php`).
6. **NO modifica CPT registration** o tassonomie — Wave 4.6 è solo Theme Options + page Lo Studio extension + cablaggio template.
7. **DRY**: prima di aggiungere un field, verifica se esiste già con altro nome (vedi DEC-025-COMPLETED lesson #2).
8. **Default ACF coerenti DEC** (lesson #4 Wave 6): "20+ ANNI / 4 AVVOCATI / 17 AREE / COA FAMIGLIA" già presenti — NON sovrascrivere.

---

## 📋 PHASE 1 — Backup + branch + audit verifica gap (~30 min)

### 1.1 Backup pre-Wave 4.6

```bash
mkdir -p ~/backups
docker-compose exec -T db mysqldump -u root -proot saltelli > ~/backups/saltelli-pre-wave46-$(date +%Y%m%d-%H%M).sql
tar czf ~/backups/saltelli-pre-wave46-theme-$(date +%Y%m%d-%H%M).tar.gz wp-content/themes/saltelli/
```

### 1.2 Branch dedicato

```bash
cd ~/Desktop/DEV/saltelli-wp/
git fetch origin
git checkout main
git pull --ff-only origin main   # → tag v1.3.1-wave4-5-critical-css-webp
git checkout -b feat/wave4-6-cms-editability
```

### 1.3 Audit verifica gap (cristallizza orchestratore findings)

Esegui questo audit script e salva l'output in `.claude/knowledge/audits/wave4-6/gap-audit-pre.txt`:

```bash
mkdir -p .claude/knowledge/audits/wave4-6/

cat > /tmp/audit-gap.sh << 'AUDIT'
#!/bin/bash
cd wp-content/themes/saltelli/

echo "=== 1. Tutte saltelli_option() chiamate ==="
grep -rn "saltelli_option(" --include="*.php" | \
  grep -oE "saltelli_option\(['\"][^'\"]+['\"]" | \
  sed "s/saltelli_option(['\"]//g" | sed "s/['\"]//g" | sort -u > /tmp/option_calls.txt
wc -l /tmp/option_calls.txt
cat /tmp/option_calls.txt

echo ""
echo "=== 2. Field ACF Theme Options esistenti ==="
python3 -c "
import json
with open('acf-json/group_theme_options_v1.json') as f:
    data = json.load(f)
for field in data['fields']:
    if field['type'] not in ['tab', 'message']:
        print(field['name'])
" | sort -u > /tmp/option_fields.txt
wc -l /tmp/option_fields.txt

echo ""
echo "=== 3. ❌ GAP: chiamate ma field NON esistente ==="
python3 -c "
with open('/tmp/option_calls.txt') as f:
    calls = set(l.strip() for l in f if l.strip())
with open('/tmp/option_fields.txt') as f:
    fields = set(l.strip() for l in f if l.strip())
calls_real = {c for c in calls if '{\$' not in c and 'social_' != c}
gap = calls_real - fields
print(f'Field chiamati ma NON esistenti: {len(gap)}')
for f in sorted(gap):
    print(f'  ❌ {f}')
"

echo ""
echo "=== 4. 🟡 DEAD: field esistenti ma codice NON chiama ==="
python3 -c "
with open('/tmp/option_calls.txt') as f:
    calls = set(l.strip() for l in f if l.strip())
with open('/tmp/option_fields.txt') as f:
    fields = set(l.strip() for l in f if l.strip())
calls_real = {c for c in calls if '{\$' not in c}
unused = fields - calls_real
unused_real = {f for f in unused if 'trust_signal' not in f}
print(f'Field DEAD: {len(unused_real)}')
for f in sorted(unused_real):
    print(f'  🟡 {f}')
"
AUDIT

chmod +x /tmp/audit-gap.sh
/tmp/audit-gap.sh > .claude/knowledge/audits/wave4-6/gap-audit-pre.txt
cat .claude/knowledge/audits/wave4-6/gap-audit-pre.txt
```

**Verifica**: l'output dovrebbe mostrare ~20 GAP + ~15 DEAD. Se i numeri sono diversi, riporta all'orchestratore PRIMA di proseguire.

### 1.4 Commit Phase 1

```bash
git add .claude/knowledge/audits/wave4-6/
git commit -m "wave4-6: phase 1 — backup + branch + audit gap CMS preliminare"
```

---

## 📋 PHASE 2 — Estensione `group_theme_options_v1` (+25 field) (~90 min)

### 2.1 Strategia

Aggiungere **4 nuovi tab UI** in cima a `group_theme_options_v1.json` (PRIMA del tab "Studio Info" esistente):

1. **Tab "Hero Homepage"** (5 field)
2. **Tab "Studio Section Homepage"** (3 field)
3. **Tab "Team & Casi Homepage"** (3 field)
4. **Tab "Press Homepage"** (1 field repeater)

E aggiungere field nel tab "Footer" esistente per **colophon** (4 field).

I 4 alias `contact_*` (contact_email_pubblica, contact_pec, contact_piva, contact_telefono_pubblico) **NON aggiungere come field separati** — il codice fallback chain già funziona con studio_* (i contact_* sono dead alias, gestione: lascia il fallback in helpers.php ma documenta nei commenti che sono deprecated).

### 2.2 Edit `acf-json/group_theme_options_v1.json`

Strategia di editing: Python script che modifica il JSON in place per aggiungere i nuovi field nelle posizioni corrette. Carica il file, inserisce i nuovi field array, riscrive con indent=4 per leggibilità.

#### Field Tab "Hero Homepage" (NEW — posizione: subito dopo apertura `fields[]`)

```json
{
  "key": "field_hero_tab",
  "label": "Hero Homepage",
  "name": "",
  "type": "tab",
  "placement": "left"
},
{
  "key": "field_hero_eyebrow",
  "label": "Eyebrow (sopra titolo)",
  "name": "hero_eyebrow",
  "type": "text",
  "default_value": "Studio Legale · Napoli · Chiaia · Dal 1999",
  "instructions": "Riga editoriale piccola sopra il titolo principale. Format raccomandato: '§ Categoria · Luogo · Anno fondazione'",
  "maxlength": 80
},
{
  "key": "field_hero_headline",
  "label": "Headline (titolo principale)",
  "name": "hero_headline",
  "type": "text",
  "default_value": "Diritto, con misura.",
  "instructions": "Titolo H1 della homepage. Brand voice: dichiarazione concisa e memorabile.",
  "maxlength": 60,
  "required": 0
},
{
  "key": "field_hero_subheadline",
  "label": "Subheadline (paragrafo lede)",
  "name": "hero_subheadline",
  "type": "textarea",
  "default_value": "Studio Legale Saltelli &amp; Partners. Quattro avvocati a Chiaia, diciannove aree di pratica, vent'anni di lavoro accanto a famiglie e imprese di Napoli.",
  "instructions": "Paragrafo italic sotto il titolo. 1-3 frasi che spiegano lo Studio.",
  "rows": 3
},
{
  "key": "field_hero_cta_label",
  "label": "CTA hero — Label",
  "name": "hero_cta_label",
  "type": "text",
  "default_value": "Prenota una consulenza gratuita",
  "maxlength": 40
},
{
  "key": "field_hero_cta_url",
  "label": "CTA hero — URL",
  "name": "hero_cta_url",
  "type": "url",
  "default_value": "/contatti/"
}
```

#### Field Tab "Studio Section Homepage" (NEW)

```json
{
  "key": "field_studio_section_tab",
  "label": "Studio Section",
  "name": "",
  "type": "tab",
  "placement": "left"
},
{
  "key": "field_studio_titolo_sezione",
  "label": "Titolo sezione studio (homepage)",
  "name": "studio_titolo_sezione",
  "type": "text",
  "default_value": "Un atelier, in senso napoletano.",
  "instructions": "Titolo H2 della sezione 'Lo Studio' in homepage."
},
{
  "key": "field_studio_body",
  "label": "Body sezione studio (homepage)",
  "name": "studio_body",
  "type": "wysiwyg",
  "default_value": "",
  "instructions": "Testo descrittivo Studio in homepage. Brand voice: 1-3 paragrafi editoriali. Se vuoto, usa fallback hardcoded.",
  "tabs": "visual",
  "toolbar": "basic",
  "media_upload": 0
},
{
  "key": "field_studio_foto_facciata",
  "label": "Foto facciata Studio",
  "name": "studio_foto_facciata",
  "type": "image",
  "instructions": "Foto della facciata o ingresso dello Studio. Min 1200×800px, JPG quality 85.",
  "return_format": "array",
  "preview_size": "medium",
  "library": "all",
  "mime_types": "jpg,jpeg,png,webp"
}
```

#### Field Tab "Team & Casi Homepage" (NEW)

```json
{
  "key": "field_team_cases_tab",
  "label": "Team & Casi",
  "name": "",
  "type": "tab",
  "placement": "left"
},
{
  "key": "field_team_titolo",
  "label": "Titolo sezione team (homepage)",
  "name": "team_titolo",
  "type": "textarea",
  "default_value": "Quattro\nprofessionisti.",
  "instructions": "Titolo H2 della sezione 'Team' in homepage. Linebreaks rispettati.",
  "rows": 2
},
{
  "key": "field_cases_titolo",
  "label": "Titolo sezione casi (homepage)",
  "name": "cases_titolo",
  "type": "text",
  "default_value": "Casi rappresentativi."
},
{
  "key": "field_casi_rappresentativi_home",
  "label": "Casi rappresentativi in homepage",
  "name": "casi_rappresentativi_home",
  "type": "post_object",
  "post_type": ["saltelli_caso"],
  "multiple": 1,
  "return_format": "id",
  "instructions": "Seleziona 3-6 casi da mostrare nella sezione 'Casi rappresentativi' della homepage. Se vuoto, fallback automatico ai 6 casi più recenti.",
  "ui": 1,
  "allow_null": 1
}
```

#### Field Tab "Press Homepage" (NEW — repeater)

```json
{
  "key": "field_press_tab",
  "label": "Press Homepage",
  "name": "",
  "type": "tab",
  "placement": "left"
},
{
  "key": "field_press_outlets",
  "label": "Press outlets (loghi homepage)",
  "name": "press_outlets",
  "type": "repeater",
  "instructions": "Loghi di clienti, giornali o publication che hanno citato lo Studio. Mostrato in homepage sezione press.",
  "min": 0,
  "max": 12,
  "layout": "block",
  "button_label": "Aggiungi outlet",
  "sub_fields": [
    {
      "key": "field_press_outlet_name",
      "label": "Nome outlet",
      "name": "name",
      "type": "text",
      "wrapper": {"width": "40"}
    },
    {
      "key": "field_press_outlet_logo",
      "label": "Logo (SVG/PNG)",
      "name": "logo",
      "type": "image",
      "return_format": "url",
      "wrapper": {"width": "30"},
      "mime_types": "svg,png,webp"
    },
    {
      "key": "field_press_outlet_url",
      "label": "URL articolo (opz.)",
      "name": "url",
      "type": "url",
      "wrapper": {"width": "30"}
    }
  ]
}
```

#### Field nel tab "Footer" esistente — Colophon (4 NEW)

Inserire DOPO `footer_newsletter_provider` (ultimo field del tab Footer):

```json
{
  "key": "field_colophon_indirizzo",
  "label": "Colophon — Indirizzo (multilinea)",
  "name": "colophon_indirizzo",
  "type": "textarea",
  "default_value": "Via Vannella Gaetani, 27\n80121 Napoli — Chiaia",
  "instructions": "Indirizzo nel colophon footer. Linebreaks supportati. Se vuoto, fallback chain a studio_indirizzo_via + studio_cap_citta.",
  "rows": 3
},
{
  "key": "field_colophon_orari",
  "label": "Colophon — Orari (multilinea)",
  "name": "colophon_orari",
  "type": "textarea",
  "default_value": "Lun – Ven · 10:00 – 19:00\nSolo su appuntamento",
  "rows": 3
},
{
  "key": "field_colophon_email",
  "label": "Colophon — Email",
  "name": "colophon_email",
  "type": "email",
  "instructions": "Se vuoto, fallback a studio_email."
},
{
  "key": "field_colophon_telefono",
  "label": "Colophon — Telefono",
  "name": "colophon_telefono",
  "type": "text",
  "default_value": "+39 081 1813 1119",
  "instructions": "Se vuoto, fallback a studio_telefono_pubblico."
}
```

### 2.3 Approccio editing del JSON file

Usa Python script per editing strutturato (NON regex/sed sul JSON):

```python
import json
from pathlib import Path

path = Path("wp-content/themes/saltelli/acf-json/group_theme_options_v1.json")
data = json.loads(path.read_text())

# Backup
backup = Path(str(path) + ".pre-wave46.bak")
backup.write_text(json.dumps(data, indent=4, ensure_ascii=False))

# Trova posizione tab "Studio Info" (primo tab esistente)
new_tabs_fields = [
    # Tab Hero Homepage + 5 field
    {"key": "field_hero_tab", "label": "Hero Homepage", ...},
    # ...etc
]

# Inserisci all'inizio di fields[] (PRIMA del tab "Studio Info" esistente)
data['fields'] = new_tabs_fields + data['fields']

# Trova il tab "Footer" e aggiungi i 4 colophon dopo footer_newsletter_provider
for i, field in enumerate(data['fields']):
    if field.get('name') == 'footer_newsletter_provider':
        colophon_fields = [...]
        data['fields'] = data['fields'][:i+1] + colophon_fields + data['fields'][i+1:]
        break

# Aggiorna modified timestamp
import time
data['modified'] = int(time.time())

# Scrivi
path.write_text(json.dumps(data, indent=4, ensure_ascii=False))
print("✅ group_theme_options_v1.json aggiornato")
```

### 2.4 Sync ACF da JSON (importazione automatica)

ACF auto-sync deve essere attivo. Forza re-sync:

```bash
docker-compose exec -T wp wp acf sync --all
# Output atteso: "Sync OK: group_theme_options_v1"
```

### 2.5 Smoke test Phase 2

```bash
# Verifica field aggiunti via WP-CLI
docker-compose exec -T wp wp acf get_field 'hero_headline' --post_id='options' 2>&1
# Atteso: default value "Diritto, con misura."

# Verifica frontend NON regression (default values matchano fallback hardcoded)
curl -s http://localhost:8080/ | grep -A 2 "sl-hero__headline" | head -3
# Atteso: "Diritto, con misura." (uguale a prima Wave 4.6)
```

### 2.6 Commit Phase 2

```bash
git add wp-content/themes/saltelli/acf-json/group_theme_options_v1.json
git commit -m "wave4-6: phase 2 — group_theme_options +20 field (4 nuovi tab + 4 colophon)

Tab nuovi:
- Hero Homepage: 5 field (eyebrow, headline, subheadline, cta_label, cta_url)
- Studio Section: 3 field (titolo_sezione, body wysiwyg, foto_facciata image)
- Team & Casi: 3 field (team_titolo, cases_titolo, casi_rappresentativi_home post_object)
- Press: 1 repeater (name + logo + url, max 12)

Tab Footer esteso:
- 4 colophon field (indirizzo, orari, email, telefono) — fallback chain con studio_*

NO breaking change: tutti default ACF identici ai fallback hardcoded.
Audit gap pre-Wave 4.6 cristallizzato in .claude/knowledge/audits/wave4-6/."
```

---

## 📋 PHASE 3 — Estensione page Lo Studio (timeline + founding) (~60 min)

### 3.1 Crea `group_lo_studio_v1.json` (NEW)

Location rule: `page_template = page-lo-studio.php` OR `post_status = publish AND post_name = lo-studio`.

```json
{
  "key": "group_lo_studio_v1",
  "title": "Saltelli — Page Lo Studio",
  "fields": [
    {
      "key": "field_ls_founding_paragraphs",
      "label": "Founding story (fallback se Editor vuoto)",
      "name": "founding_paragraphs",
      "type": "wysiwyg",
      "default_value": "<p>Lo Studio Saltelli &amp; Partners nasce per iniziativa di Emiliano Saltelli, giovane tributarista formatosi alla Federico II, che apre una stanza al secondo piano di un palazzo nobiliare a Chiaia.</p>\n<p>Nel quarto di secolo successivo, lo Studio è cresciuto come si cresce a Napoli — per accumulazione paziente, una pratica alla volta, un avvocato alla volta — fino a diventare oggi un atelier di quattro professionisti.</p>",
      "instructions": "Story founding usato come fallback se l'editor classico (post_content) è vuoto. Se compili l'editor classico, ha priorità su questo field.",
      "tabs": "visual",
      "toolbar": "basic",
      "media_upload": 0
    },
    {
      "key": "field_ls_timeline_year_range",
      "label": "Timeline — Range anni headline",
      "name": "timeline_year_range",
      "type": "text",
      "default_value": "1999 → 2026.",
      "instructions": "Headline grande sopra timeline. Format: 'AAAA → AAAA.'"
    },
    {
      "key": "field_ls_timeline_events",
      "label": "Timeline — Eventi storici",
      "name": "timeline_events",
      "type": "repeater",
      "instructions": "Eventi cronologici Studio. Min 3, max 12. Ordinati per anno crescente.",
      "min": 3,
      "max": 12,
      "layout": "block",
      "button_label": "Aggiungi evento",
      "sub_fields": [
        {
          "key": "field_ls_timeline_year",
          "label": "Anno",
          "name": "year",
          "type": "text",
          "wrapper": {"width": "15"},
          "maxlength": 4
        },
        {
          "key": "field_ls_timeline_title",
          "label": "Titolo evento",
          "name": "title",
          "type": "text",
          "wrapper": {"width": "35"}
        },
        {
          "key": "field_ls_timeline_desc",
          "label": "Descrizione (1-2 frasi)",
          "name": "description",
          "type": "textarea",
          "rows": 2,
          "wrapper": {"width": "50"}
        }
      ]
    }
  ],
  "location": [
    [
      {
        "param": "page_template",
        "operator": "==",
        "value": "page-lo-studio.php"
      }
    ],
    [
      {
        "param": "post_type",
        "operator": "==",
        "value": "page"
      },
      {
        "param": "post",
        "operator": "==",
        "value": "lo-studio"
      }
    ]
  ],
  "menu_order": 10,
  "position": "normal",
  "style": "default",
  "label_placement": "top",
  "instruction_placement": "label",
  "active": true,
  "modified": <current_timestamp>
}
```

### 3.2 Migra default ACF: popola repeater al primo save (idempotente)

I 6 eventi timeline default andrebbero pre-popolati nel field `timeline_events`. Strategia: helper PHP idempotente che, alla prima visita admin della page Lo Studio, popola il repeater con gli eventi hardcoded esistenti.

In `inc/wave4-6-migration.php` (NEW, idempotente):

```php
<?php
/**
 * Wave 4.6 — Migrazione idempotente field ACF page Lo Studio.
 * Pre-popola timeline_events con i 6 eventi hardcoded esistenti la prima volta che
 * la page Lo Studio viene visitata in admin (one-shot).
 */
defined('ABSPATH') || exit;

if (!function_exists('saltelli_w46_migrate_lo_studio_timeline')) :
function saltelli_w46_migrate_lo_studio_timeline() {
    // Trova page Lo Studio
    $page = get_page_by_path('lo-studio', OBJECT, 'page');
    if (!$page) return;
    
    // Verifica se già popolato (idempotenza)
    $existing = get_field('timeline_events', $page->ID);
    if (!empty($existing) && is_array($existing) && count($existing) >= 3) return;
    
    // Migrate hardcoded → ACF repeater
    $events = [
        ['year' => '1999', 'title' => 'Fondazione', 'description' => 'Emiliano Saltelli apre lo studio in Via Vannella Gaetani, focalizzato sul contenzioso tributario.'],
        ['year' => '2007', 'title' => 'Ingresso di Fabiana', 'description' => 'Si aggiunge la prima associate — area diritto del lavoro.'],
        ['year' => '2014', 'title' => 'Apertura LGBTQ+', 'description' => 'Antonia Battista inaugura una pratica dedicata, prima a Napoli sud.'],
        ['year' => '2019', 'title' => "Vent'anni", 'description' => "Lo studio passa da 2 a 4 professionisti stabili. Atelier a tutti gli effetti."],
        ['year' => '2024', 'title' => 'Cassazione + AGE', 'description' => 'Annullamento cartella €240k. Conferma in Cassazione su licenziamento illegittimo.'],
        ['year' => '2026', 'title' => 'Oggi', 'description' => '17 aree presidiate, 4 professionisti, un solo atelier.'],
    ];
    
    update_field('timeline_events', $events, $page->ID);
    update_field('timeline_year_range', '1999 → 2026.', $page->ID);
}
endif;

// Hook a admin_init priority basso (one-shot per session)
add_action('admin_init', 'saltelli_w46_migrate_lo_studio_timeline', 999);
```

Include in `functions.php`:
```php
require_once SALTELLI_THEME_DIR . '/inc/wave4-6-migration.php';
```

### 3.3 Refactor `template-parts/page-lo-studio.php`

Sostituisci array hardcoded con loop ACF. Il template ora legge da ACF se popolato, altrimenti usa fallback hardcoded (per backward compatibility durante transition):

```php
<?php
$pid = get_queried_object_id();

// Founding paragraphs: prima the_content(), poi ACF founding_paragraphs, poi hardcoded fallback
// (il fallback hardcoded in __() è già presente — NON rimuoverlo per safety)

// Timeline range
$timeline_year_range = (string) saltelli_field('timeline_year_range', $pid, '1999 → 2026.');

// Timeline events: prima ACF, poi hardcoded fallback
$timeline_events = saltelli_field('timeline_events', $pid, []);
if (empty($timeline_events) || !is_array($timeline_events)) {
    // Fallback hardcoded (kept per backward compat — sarà rimosso post-migrazione idempotente run)
    $timeline_events = [
        ['year' => '1999', 'title' => __('Fondazione', 'saltelli'), 'description' => __('Emiliano Saltelli apre lo studio in Via Vannella Gaetani, focalizzato sul contenzioso tributario.', 'saltelli')],
        // ... altri 5 eventi hardcoded come fallback
    ];
}
?>

<!-- Headline timeline range -->
<h2 class="sl-section-title sl-chi-siamo__h2" id="chi-siamo-time-h"><?php echo esc_html($timeline_year_range); ?></h2>

<!-- Timeline loop -->
<?php foreach ($timeline_events as $ev): ?>
    <article>
        <span class="sl-chi-siamo__timeline-y"><?php echo esc_html($ev['year']); ?></span>
        <h3 class="sl-chi-siamo__timeline-t"><?php echo esc_html($ev['title']); ?></h3>
        <p><?php echo esc_html($ev['description']); ?></p>
    </article>
<?php endforeach; ?>
```

### 3.4 Smoke test Phase 3

```bash
# Verifica page Lo Studio renderizza correttamente
curl -s http://localhost:8080/chi-siamo/lo-studio/ | grep -c "sl-chi-siamo__timeline" 
# Atteso: 1+ (sezione timeline presente)

# Verifica anno range presente
curl -s http://localhost:8080/chi-siamo/lo-studio/ | grep "1999 → 2026"
# Atteso: match (default ACF)

# Verifica 6 eventi renderizzati
curl -s http://localhost:8080/chi-siamo/lo-studio/ | grep -c "sl-chi-siamo__timeline-y"
# Atteso: 6
```

### 3.5 Commit Phase 3

```bash
git add wp-content/themes/saltelli/acf-json/group_lo_studio_v1.json
git add wp-content/themes/saltelli/inc/wave4-6-migration.php
git add wp-content/themes/saltelli/template-parts/page-lo-studio.php
git add wp-content/themes/saltelli/functions.php
git commit -m "wave4-6: phase 3 — page Lo Studio extension (timeline + founding ACF)

NEW group_lo_studio_v1.json:
- founding_paragraphs (wysiwyg fallback se editor vuoto)
- timeline_year_range (text, default '1999 → 2026.')
- timeline_events (repeater min 3 max 12: year + title + description)

NEW inc/wave4-6-migration.php (idempotente):
- saltelli_w46_migrate_lo_studio_timeline() popola repeater al primo admin_init con i 6 eventi hardcoded esistenti
- Skip se già popolato (idempotency check)

MOD template-parts/page-lo-studio.php:
- Timeline events da ACF se popolato, fallback hardcoded altrimenti
- Timeline year range da ACF
- Backward compat: fallback hardcoded preservato durante transition

Asset: ora Elena può modificare timeline + founding via WP Admin → Pages → Lo Studio."
```

---

## 📋 PHASE 4 — Cablaggio dead fields + fix is_tier_1 (~45 min)

### 4.1 Cablare i 15 dead fields nei template

**brand_payoff** (sotto logo header):
File: `header.php`
```php
$brand_payoff = saltelli_option('brand_payoff', '');
if ($brand_payoff): ?>
  <span class="sl-brand__payoff"><?php echo esc_html($brand_payoff); ?></span>
<?php endif;
```

**brand_statement_short** (footer/about):
File: `footer.php`
```php
$brand_statement = saltelli_option('brand_statement_short', '');
if ($brand_statement): ?>
  <p class="sl-foot-brand__statement"><?php echo esc_html($brand_statement); ?></p>
<?php endif;
```

**cta_default_url + cta_subline_italic** (homepage CTA finale):
File: `front-page.php` (sezione CTA finale prima footer)
```php
$cta_url = saltelli_option('cta_default_url', '/contatti/');
$cta_subline = saltelli_option('cta_subline_italic', '');
```

**footer_credit_text + footer_credit_url** (footer hairline credit):
File: `footer.php` (in fondo)
```php
$credit_text = saltelli_option('footer_credit_text', 'Realizzato da Adsolut.');
$credit_url = saltelli_option('footer_credit_url', 'https://adsolut.it');
?>
<small class="sl-foot-credit">
  <a href="<?php echo esc_url($credit_url); ?>" target="_blank" rel="noopener"><?php echo esc_html($credit_text); ?></a>
</small>
```

**footer_newsletter_enabled + footer_newsletter_provider** (footer newsletter conditional):
File: `footer.php`
```php
$nl_enabled = (bool) saltelli_option('footer_newsletter_enabled', false);
$nl_provider = saltelli_option('footer_newsletter_provider', 'brevo');

if ($nl_enabled): ?>
  <!-- Newsletter form (esistente, nascosto se !$nl_enabled) -->
<?php endif;
```

**social_facebook/instagram/linkedin/twitter** (footer + header):
File: `footer.php` — sezione social icons. Già presente con loop, verifica che usi i field corretti.
```php
$social_links = [
    'instagram' => saltelli_option('social_instagram', ''),
    'linkedin' => saltelli_option('social_linkedin', ''),
    'twitter' => saltelli_option('social_twitter', ''),
    'facebook' => saltelli_option('social_facebook', ''),
];
foreach ($social_links as $platform => $url) {
    if (!$url) continue;
    echo '<a href="' . esc_url($url) . '" class="sl-foot-social__link sl-foot-social__link--' . $platform . '" target="_blank" rel="noopener" aria-label="' . esc_attr(ucfirst($platform)) . '"><!-- icon SVG --></a>';
}
```

**studio_coordinate_lat/lng** (schema Organization JSON-LD):
File: `inc/schema/organization.php` (esistente, da estendere)
```php
$lat = saltelli_option('studio_coordinate_lat', '');
$lng = saltelli_option('studio_coordinate_lng', '');
if ($lat && $lng) {
    $schema['geo'] = [
        '@type' => 'GeoCoordinates',
        'latitude' => $lat,
        'longitude' => $lng,
    ];
}
```

**studio_ordine_avvocati** (footer + page contatti):
File: `footer.php`
```php
$ordine = saltelli_option('studio_ordine_avvocati', 'Iscritti COA Napoli');
?>
<p class="sl-foot-meta"><?php echo esc_html($ordine); ?></p>
```

### 4.2 Fix `is_tier_1_focus` → `is_tier_1` in single-competenza.php

```bash
# Find tutte le occorrenze
grep -n "is_tier_1_focus" wp-content/themes/saltelli/single-competenza.php
```

Surgical replace (probabilmente 1-2 occorrenze):
```php
// PRIMA:
$is_tier_1 = (bool) saltelli_field('is_tier_1_focus', $p->ID, false);

// DOPO:
$is_tier_1 = (bool) saltelli_field('is_tier_1', $p->ID, false);
```

### 4.3 Smoke test Phase 4

```bash
# Brand statement nel footer
curl -s http://localhost:8080/ | grep "sl-foot-brand__statement"

# Footer credit
curl -s http://localhost:8080/ | grep "sl-foot-credit"

# Schema Organization con geo (se lat/lng popolati)
curl -s http://localhost:8080/ | grep -A 5 '"@type":"Organization"' | head -10

# is_tier_1 fix
docker-compose exec -T wp wp acf get_field 'is_tier_1' --post_id=$(docker-compose exec -T wp wp post list --post_type=competenza --fields=ID --format=csv | tail -n +2 | head -1)
# Atteso: true/false (NO error "field doesn't exist")
```

### 4.4 Commit Phase 4

```bash
git add wp-content/themes/saltelli/header.php
git add wp-content/themes/saltelli/footer.php
git add wp-content/themes/saltelli/front-page.php
git add wp-content/themes/saltelli/single-competenza.php
git add wp-content/themes/saltelli/inc/schema/organization.php
git commit -m "wave4-6: phase 4 — cablaggio 15 dead fields + fix is_tier_1

Cablati nei template:
- brand_payoff (header sotto logo)
- brand_statement_short (footer about)
- cta_default_url + cta_subline_italic (homepage CTA finale)
- footer_credit_text + footer_credit_url (footer hairline)
- footer_newsletter_enabled + footer_newsletter_provider (newsletter conditional)
- social_facebook/instagram/linkedin/twitter (footer social icons loop)
- studio_coordinate_lat/lng (schema Organization JSON-LD geo)
- studio_ordine_avvocati (footer meta)

Fix:
- single-competenza.php: is_tier_1_focus → is_tier_1 (mismatch ACF field name, 2 occorrenze)

Schema markup ora completo: Organization include geo se coordinate popolate.
Footer ora ha credit + brand statement + social + ordine avvocati gestibili da Theme Options."
```

---

## 📋 PHASE 5 — Smoke regression + bump version + report (~30 min)

### 5.1 NO regression smoke (CRITICAL gate)

```bash
mkdir -p .claude/knowledge/audits/wave4-6/regression/

# Wave 5: 33 audit-aligned + 18 redirect + 33 blog chain
bash .claude/knowledge/audits/wave5-ia-refactor/cli-output/smoke-runner.sh \
  > .claude/knowledge/audits/wave4-6/regression/wave5-audit-aligned.txt 2>&1 || true

# Wave 6: 21 URL + render checks
bash .claude/knowledge/audits/wave6/smoke-runner.sh \
  > .claude/knowledge/audits/wave4-6/regression/wave6-smoke.txt 2>&1 || true

# Wave 4: 5 security headers
bash .claude/knowledge/audits/wave4/regression/headers-check.sh \
  > .claude/knowledge/audits/wave4-6/regression/wave4-headers.txt 2>&1 || true

# Wave 4.5: per-template critical CSS + WebP picture render
curl -s http://localhost:8080/ | grep -c 'id="saltelli-critical-css"' \
  > .claude/knowledge/audits/wave4-6/regression/wave45-critical.txt
curl -s http://localhost:8080/risorse/blog/ | grep -c 'image-set' \
  >> .claude/knowledge/audits/wave4-6/regression/wave45-critical.txt
```

**Gate**: tutte 0 fails. Se anche solo 1 regression: STOP, debug, ripristina.

### 5.2 Re-audit gap CMS post-Wave 4.6 (deve essere 0)

```bash
/tmp/audit-gap.sh > .claude/knowledge/audits/wave4-6/gap-audit-post.txt
diff .claude/knowledge/audits/wave4-6/gap-audit-pre.txt .claude/knowledge/audits/wave4-6/gap-audit-post.txt
# Atteso: GAP da 20 → 0, DEAD da 15 → 0
```

### 5.3 Bump theme version

In `wp-content/themes/saltelli/style.css`:
```
Version: 1.3.2-wave4-6-cms-editability
```

In `wp-content/themes/saltelli/functions.php`:
```php
define('SALTELLI_VERSION', '1.3.2-wave4-6-cms-editability');
```

### 5.4 Report finale

Crea `.claude/knowledge/recovery/WAVE4-6-CMS-EDITABILITY-REPORT.md` con:
- Phase summary 1-5
- Gap audit pre vs post (20 GAP → 0, 15 DEAD → 0)
- Smoke regression (Wave 5+6+4+4.5 PASS)
- File modificati / creati riepilogo
- Lista field aggiunti + dove sono nei template
- Verifica WP Admin: screenshot atteso di `Saltelli Settings` con 10 tab visibili
- Hand-off note per orchestratore + Elena

### 5.5 Commit + push

```bash
git add -A
git commit -m "wave4-6: phase 5 — bump 1.3.2 + smoke regression + gap audit closure

Gap audit:
- Pre-Wave 4.6: 20 GAP (saltelli_option chiamate senza ACF) + 15 DEAD (ACF non chiamati)
- Post-Wave 4.6: 0 GAP + 0 DEAD ✅

Smoke regression:
- Wave 5: 33+18+33 PASS
- Wave 6: 21 + render checks PASS
- Wave 4: 5 security headers PASS
- Wave 4.5: critical CSS + WebP picture rendering PASS

Closes Wave 4.6 — CMS Editability completata.
Sito ora 100% gestibile via WP Admin per editorialità Elena."

git push origin feat/wave4-6-cms-editability
```

---

## ✅ Acceptance criteria Wave 4.6

- [ ] Branch `feat/wave4-6-cms-editability` da main post-Wave 4.5
- [ ] 5 phases eseguite, 5+ commit phase-by-phase
- [ ] **+20 field aggiunti** in `group_theme_options_v1.json` (4 nuovi tab UI + 4 colophon nel tab Footer)
- [ ] **NEW `group_lo_studio_v1.json`** con timeline_events repeater + founding_paragraphs + timeline_year_range
- [ ] **`inc/wave4-6-migration.php`** idempotente popola timeline al primo admin_init
- [ ] **15 dead fields cablati** nei template (brand, cta_default, footer credit, social, geo, ordine)
- [ ] **Fix `is_tier_1_focus` → `is_tier_1`** in single-competenza.php
- [ ] **Re-audit gap**: 0 GAP + 0 DEAD post-Wave 4.6
- [ ] **NO regression** smoke Wave 5 (84 PASS) + Wave 6 (21 + render checks) + Wave 4 (5 headers) + Wave 4.5 (critical CSS + WebP)
- [ ] Theme version `1.3.2-wave4-6-cms-editability`
- [ ] Branch pushed (NO merge automatico)
- [ ] Report `.claude/knowledge/recovery/WAVE4-6-CMS-EDITABILITY-REPORT.md`

---

## 🚨 Cosa fare in caso di errore

| Situazione | Action |
|---|---|
| ACF auto-sync non importa il JSON modificato | Forza con `wp acf sync --all`. Se persiste: invalidate `_acf_field_groups` transient |
| Frontend mostra default ACF al posto del fallback hardcoded | Verifica che default_value sia identico al fallback. Differenza più comune: HTML entities (`&amp;` vs `&`) |
| Timeline migration script non popola | Verifica che la page Lo Studio esista (`get_page_by_path('lo-studio')`). Idempotency check skippa se già popolato |
| Smoke regression Wave 5/6/4/4.5 | STOP immediato, ripristina commit, debug isolato |
| Field repeater `casi_rappresentativi_home` rompe homepage | Verifica `saltelli_homepage_cases()` helper: deve fallback su 6 casi recenti se ACF vuoto |
| Schema Organization rotto post lat/lng cablaggio | Wrap il geo block in `if ($lat && $lng)` — NON aggiungere geo se coordinate vuote |

---

## 🎯 Output expected

1. Branch `feat/wave4-6-cms-editability` con 5+ commit phase-by-phase
2. File modificati / creati:
   - **MOD**: `acf-json/group_theme_options_v1.json` (+25 field)
   - **NEW**: `acf-json/group_lo_studio_v1.json` (3 field di cui 1 repeater)
   - **NEW**: `inc/wave4-6-migration.php` (idempotente)
   - **MOD**: `template-parts/page-lo-studio.php` (timeline + founding via ACF)
   - **MOD**: `header.php` (brand_payoff)
   - **MOD**: `footer.php` (credit, social, brand_statement, ordine)
   - **MOD**: `front-page.php` (cta_default cablato)
   - **MOD**: `single-competenza.php` (fix is_tier_1)
   - **MOD**: `inc/schema/organization.php` (geo)
   - **MOD**: `functions.php` + `style.css` (version bump)
3. Audit trail in `.claude/knowledge/audits/wave4-6/`:
   - `gap-audit-pre.txt` (20 GAP + 15 DEAD)
   - `gap-audit-post.txt` (0 GAP + 0 DEAD ✅)
   - `regression/` (Wave 5+6+4+4.5 smoke)
4. Report `.claude/knowledge/recovery/WAVE4-6-CMS-EDITABILITY-REPORT.md`
5. Theme version `1.3.2-wave4-6-cms-editability`

L'orchestratore audisce + procede con merge `feat/wave4-6-cms-editability → main` (no-ff) + tag `v1.3.2-wave4-6-cms-editability`.

Una volta mergeato: prompt deploy staging.studiolegalesaltelli.it.

---

## 🔗 Riferimenti

- DEC-024 (Wave 5), DEC-025-COMPLETED (Wave 6), DEC-026-COMPLETED (Wave 4), DEC-027 (Wave 4.5 + EDITOR-HANDOFF + AgID)
- Audit gap CMS orchestratore (sere 2026-05-07): 20 GAP + 15 DEAD identificati
- `EDITOR-HANDOFF.md` (deliverable Adsolut) — Elena userà i nuovi field documentati qui
- `CLAUDE.md` — single source of truth
