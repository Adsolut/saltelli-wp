# PROMPT AGENT — Design Handoff Wave P9 · archive-casi pull-quote ADDITIVE + filtri

> **Scope**: aggiungere sezione pull-quote "caso simbolo" (4 SCF field additive a tab "Archive Headers" di Theme Options) + JS toggle filtri caso_categoria client-side. CSS già allineato. **Unica wave Design Handoff con vero ADDITIVE SCF.**
>
> **Branch**: `feat/design-handoff-archive-casi`
> **Stima**: 1.5-2h (severity 🟡 MEDIUM, ADDITIVE + JS)
> **Modalità**: lean, version bump 1.3.16 (SCF additive triggers bump)
> **Sessione**: una sola Claude Code.

---

## CONTESTO

Wave 9/12 sequenza Design Handoff. P1-P5 mergeate, P2/P6 skipped (0 drift), P7 consolidamento + P8 blog-archive in sequenza prima di questa.

**Decisioni orchestratore acquisite** (NON rinegoziare):
1. SoT design tokens = `tokens.css` corrente vince (KEEP CURRENT)
2. SCF additive: SOLO 4 field nuovi nel tab "Archive Headers" di `group_theme_options_v1`. **NO refactor** 4 field esistenti (archive_caso_eyebrow/h1_main/h1_emphasis/intro). **NO touch** `group_caso_item_v1` (CPT singolo).
3. **Pull-quote architecture**: **Opzione 1** (4 field testuali separati) — raccomandato Agent, allineato con pattern Theme Options corrente.
4. **Pull-quote conditional**: se 4 field tutti vuoti → no render. Default values `""` (Elena sceglie).
5. **Filtri caso_categoria**: **hardcode 5 tab** ("Tutti / Privati / Imprese / Contenzioso / Altri") + **vanilla JS toggle client-side** (no AJAX, no scope creep, no query string server-side).

**Pre-flight orchestratore già fatto**:

| Voce | Valore |
|---|---|
| CSS drift | 🟢 0 (selettori `.sl-casi__pull*`, `.sl-casi__filter*`, `.sl-casi__row*` già in sections.css L4000-4280) |
| SCF additive | +4 field testuali (Opzione 1) |
| PHP changes | +15-20 righe in `archive-saltelli_caso.php` (binding + enqueue JS) |
| JS new | +40 righe `assets/js/archive-casi-filter.js` (toggle `.is-active`/`.is-hidden` vanilla) |
| Token changes | 🟢 0 (riuso var esistenti) |
| Severity | 🟡 MEDIUM, ETA 1.5-2h |

---

## ⚠️ HARD INVARIANT — SCF DATA CONTRACT PRESERVATION

`group_theme_options_v1.json` tab "Archive Headers" ha:
- 4 field esistenti per **archive avvocato** (Wave 4.7.fix.4): `archive_avvocato_eyebrow/h1_main/h1_emphasis/intro` — INVARIATI
- 4 field esistenti per **archive saltelli_caso** (Wave 4.7.fix.4): `archive_caso_eyebrow/h1_main/h1_emphasis/intro` — INVARIATI
- 3 field per **trust block** archive avvocato (Wave 5 STEP 3 coverage): `archive_avvocato_trust_eyebrow/headline/text` — INVARIATI
- 1 field empty text archive caso (Wave 5 STEP 3 coverage): `archive_caso_empty_text` — INVARIATO

**Aggiungere SOLO 4 field nuovi** per pull-quote caso simbolo: `archive_caso_simbolo_eyebrow/number/quote/attr`.

CPT `saltelli_caso` registration + `group_caso_item_v1` (outcome_label, casi_data, ecc.): INVARIATI.

---

## PRE-FLIGHT (5 min)

1. Leggi:
   - `CLAUDE.md` (Hard constraints, Lessons learned, Workflow rules)
   - `.claude/knowledge/audits/design-handoff/RECOMMENDATION.md` (§J archive-casi ADDITIVE, §B P9 prioritization)
   - **JSX source**: `design-handoff/archive-casi/index.jsx` (sezioni pull-quote righe 58-91 + filtri righe 93-106)
   - **WP target**:
     - `wp-content/themes/saltelli/archive-saltelli_caso.php`
     - Blocchi `.sl-casi__pull*`, `.sl-casi__filter*`, `.sl-casi__row*` in sections.css (L4000-4280)
   - `wp-content/themes/saltelli/acf-json/group_theme_options_v1.json` (tab "Archive Headers" attuale)

2. Verifica stato:
   ```sh
   git fetch origin
   git status
   git log --oneline -3   # atteso HEAD post-P7 + P8 merge
   git checkout -b feat/design-handoff-archive-casi
   ```

3. Conferma in chat: branch creato + prosegui.

---

## PHASE 1 — VERIFY (5 min)

CSS drift = 0 (Agent pre-flight confermato). Spot-check rapido:

| Selettore | Esistente in sections.css? |
|---|---|
| `.sl-casi__hero`, `.sl-casi__hero-grid` | ✓ |
| `.sl-casi__pull`, `.sl-casi__pull-figure`, `.sl-casi__pull-quote` | ✓ (L4059-4108) |
| `.sl-casi__filter-bar`, `.sl-casi__filter-btn`, `.sl-casi__filter-btn.is-active` | ✓ (L4117-4144) |
| `.sl-casi__row`, `.sl-casi__row:hover` | ✓ (L4160-4178) |
| `.sl-casi__row.is-hidden` | ⚠️ VERIFY (se mancante, ADD 1 rule `display: none`) |
| `.sl-casi__pagination*` | ✓ (L4216-4230) |
| `.sl-casi__cta*` | ✓ (L4258-4266) |

Output tabella in chat.

---

## PHASE 2 — IMPLEMENT (45-60 min)

### 2.A — SCF additive (group_theme_options_v1.json)

Aggiungi 4 field NUOVI nel tab "Archive Headers" (dopo `archive_caso_empty_text` esistente):

```json
{
  "key": "field_archive_caso_simbolo_eyebrow",
  "label": "Caso simbolo — eyebrow",
  "name": "archive_caso_simbolo_eyebrow",
  "type": "text",
  "instructions": "Eyebrow della sezione pull-quote 'caso simbolo' su /chi-siamo/casi-rappresentativi/. Es. 'Caso simbolo · 2024'. Se vuoto, la sezione non viene renderizzata sul frontend.",
  "maxlength": 60,
  "required": 0,
  "default_value": ""
},
{
  "key": "field_archive_caso_simbolo_number",
  "label": "Caso simbolo — numero/importo",
  "name": "archive_caso_simbolo_number",
  "type": "text",
  "instructions": "Numero/importo principale grande nel pull-quote. Es. '€240.000' o '01 / 19'. Display 80-140px Playfair.",
  "maxlength": 20,
  "required": 0,
  "default_value": ""
},
{
  "key": "field_archive_caso_simbolo_quote",
  "label": "Caso simbolo — citazione",
  "name": "archive_caso_simbolo_quote",
  "type": "textarea",
  "instructions": "Citazione editoriale del caso simbolo. Es. 'L'annullamento integrale di una cartella esattoriale da 240.000 € per società in liquidazione'. Visibile 24-32px Playfair italic.",
  "rows": 4,
  "maxlength": 300,
  "required": 0,
  "default_value": ""
},
{
  "key": "field_archive_caso_simbolo_attr",
  "label": "Caso simbolo — attribuzione",
  "name": "archive_caso_simbolo_attr",
  "type": "text",
  "instructions": "Attribuzione fonte del caso. Es. '— Vs. AGE Riscossione · CTP Napoli · 2024'. Mono 11px.",
  "maxlength": 100,
  "required": 0,
  "default_value": ""
}
```

Aggiungi i 4 field dentro `field_tab_archive_headers` esistente, in coda agli altri archive_caso_* o vicino. Mantieni `"modified"` bump del group.

Verifica JSON valido:
```sh
python3 -c "import json; json.load(open('wp-content/themes/saltelli/acf-json/group_theme_options_v1.json'))"
```

### 2.B — PHP wrapper (archive-saltelli_caso.php)

Dopo l'hero (eyebrow + h1 + intro), AGGIUNGI sezione pull-quote conditional:

```php
<?php
$pull_eyebrow = saltelli_option('archive_caso_simbolo_eyebrow', '');
$pull_number  = saltelli_option('archive_caso_simbolo_number', '');
$pull_quote   = saltelli_option('archive_caso_simbolo_quote', '');
$pull_attr    = saltelli_option('archive_caso_simbolo_attr', '');

// Conditional render: solo se almeno un field popolato
if ($pull_eyebrow || $pull_number || $pull_quote || $pull_attr) :
?>
<section class="sl-casi__pull" aria-label="<?php esc_attr_e('Caso simbolo', 'saltelli'); ?>">
  <?php if ($pull_eyebrow) : ?>
    <div class="sl-casi__pull-eyebrow sl-mono"><?php echo esc_html($pull_eyebrow); ?></div>
  <?php endif; ?>
  <?php if ($pull_number) : ?>
    <div class="sl-casi__pull-figure"><?php echo esc_html($pull_number); ?></div>
  <?php endif; ?>
  <?php if ($pull_quote) : ?>
    <blockquote class="sl-casi__pull-quote">
      <p><?php echo wp_kses_post($pull_quote); ?></p>
      <?php if ($pull_attr) : ?>
        <cite class="sl-casi__pull-attr sl-mono"><?php echo esc_html($pull_attr); ?></cite>
      <?php endif; ?>
    </blockquote>
  <?php endif; ?>
</section>
<?php endif; ?>
```

Posizione: dopo `</section>` hero archive e prima della sezione filtri/loop casi.

### 2.C — Filtri caso_categoria (PHP markup + JS toggle)

Prima del loop casi, aggiungi markup filtri tab (5 hardcode):

```php
<nav class="sl-casi__filter-bar" aria-label="<?php esc_attr_e('Filtra casi per categoria', 'saltelli'); ?>">
  <button type="button" class="sl-casi__filter-btn is-active" data-filter="all">Tutti</button>
  <button type="button" class="sl-casi__filter-btn" data-filter="privati">Privati</button>
  <button type="button" class="sl-casi__filter-btn" data-filter="imprese">Imprese</button>
  <button type="button" class="sl-casi__filter-btn" data-filter="contenzioso">Contenzioso</button>
  <button type="button" class="sl-casi__filter-btn" data-filter="altri">Altri</button>
</nav>
```

Nel loop casi, aggiungi `data-category="<term_slug>"` su `.sl-casi__row`:

```php
<?php
$caso_terms = get_the_terms(get_the_ID(), 'caso_categoria');
$cat_slug = ($caso_terms && !is_wp_error($caso_terms)) ? sanitize_html_class($caso_terms[0]->slug) : 'altri';
?>
<article class="sl-casi__row" data-category="<?php echo esc_attr($cat_slug); ?>">
  <!-- existing markup row -->
</article>
```

### 2.D — JS toggle vanilla (assets/js/archive-casi-filter.js)

Crea nuovo file `wp-content/themes/saltelli/assets/js/archive-casi-filter.js`:

```javascript
/**
 * Wave P9 Design Handoff — archive-casi filter toggle (vanilla JS, no AJAX).
 * Hardcode 5 tab (Tutti/Privati/Imprese/Contenzioso/Altri).
 * Toggle .is-active + .is-hidden client-side.
 */
(function () {
  'use strict';

  document.addEventListener('DOMContentLoaded', function () {
    const filterBar = document.querySelector('.sl-casi__filter-bar');
    if (!filterBar) return;

    const buttons = filterBar.querySelectorAll('.sl-casi__filter-btn');
    const rows = document.querySelectorAll('.sl-casi__row');

    if (!buttons.length || !rows.length) return;

    buttons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        const filter = btn.getAttribute('data-filter');
        if (!filter) return;

        // Update active button
        buttons.forEach(function (b) { b.classList.remove('is-active'); });
        btn.classList.add('is-active');

        // Filter rows
        rows.forEach(function (row) {
          const cat = row.getAttribute('data-category');
          if (filter === 'all' || cat === filter) {
            row.classList.remove('is-hidden');
          } else {
            row.classList.add('is-hidden');
          }
        });
      });
    });
  });
})();
```

### 2.E — CSS PHASE 1 verify result

Se `.sl-casi__row.is-hidden` mancante in sections.css, aggiungi 1 rule:

```css
/* === design-handoff archive-casi P9 (filter hidden state) === */
.sl-casi__row.is-hidden {
  display: none;
}
```

Scope marker dedicato.

### 2.F — Enqueue JS in archive-saltelli_caso.php (o functions.php)

Aggiungi enqueue conditional in archive-saltelli_caso.php top (o functions.php se preferisci globale):

```php
<?php
// Enqueue filter script solo su archive saltelli_caso
add_action('wp_enqueue_scripts', function () {
  if (is_post_type_archive('saltelli_caso')) {
    wp_enqueue_script(
      'saltelli-archive-casi-filter',
      get_template_directory_uri() . '/assets/js/archive-casi-filter.js',
      [],
      defined('SALTELLI_THEME_VERSION') ? SALTELLI_THEME_VERSION : '1.3.16',
      true // in footer
    );
  }
});
?>
```

(Decisione autonoma Code: archive-saltelli_caso.php top vs functions.php — orchestratore preferisce functions.php se pattern consistency, oppure inline al template archive se isolato.)

### 2.G — Sync staging + reload

```sh
rsync -avz wp-content/themes/saltelli/acf-json/ deploy@178.62.207.50:/var/www/saltelli/wp-content/themes/saltelli/acf-json/
rsync -avz wp-content/themes/saltelli/archive-saltelli_caso.php deploy@178.62.207.50:/var/www/saltelli/wp-content/themes/saltelli/archive-saltelli_caso.php
rsync -avz wp-content/themes/saltelli/assets/css/ deploy@178.62.207.50:/var/www/saltelli/wp-content/themes/saltelli/assets/css/
rsync -avz wp-content/themes/saltelli/assets/js/ deploy@178.62.207.50:/var/www/saltelli/wp-content/themes/saltelli/assets/js/
ssh deploy@178.62.207.50 "sudo systemctl reload php8.2-fpm && cd /var/www/saltelli && sudo -u www-data wp cache flush --path=/var/www/saltelli"
```

OPcache reload obbligatorio per edit `archive-saltelli_caso.php` e `functions.php` (lesson Wave 4.7.fix.3).

---

## PHASE 3 — SMOKE TEST (15 min)

### 3.A — Frontend curl

```sh
URL="https://staging.studiolegalesaltelli.it/chi-siamo/casi-rappresentativi/"
echo "=== HTTP status ===" && curl -sI "$URL" | head -3
echo "=== pull-quote conditional (vuoto = NO render) ==="
curl -s "$URL" | grep -c "sl-casi__pull"
# Atteso: 0 (field default vuoti → no markup). Se Elena popola 1+ field, count >= 1.

echo "=== filtri tabs presenti ==="
curl -s "$URL" | grep -c "sl-casi__filter-btn"
# Atteso: 5 (tab + 4 categorie)

echo "=== JS enqueue ==="
curl -s "$URL" | grep -c "archive-casi-filter.js"
# Atteso: 1 (script enqueued)

echo "=== data-category presente sui row ==="
curl -s "$URL" | grep -c 'data-category='
# Atteso: count >= N (= numero casi pubblicati)
```

### 3.B — Admin smoke test

WP Admin → Saltelli Settings → tab "Archive Headers":
- 4 nuovi field "Caso simbolo" visibili (eyebrow, number, quote, attr) tutti vuoti
- 4 field esistenti archive_caso_* invariati e popolati
- 4 field archive_avvocato_* invariati
- Trust block 3 field invariati
- Empty text 1 field invariato

Test funzionale:
- Popola almeno eyebrow + number con valori (es. "Caso simbolo · 2024" + "€240.000")
- Save → ricarica frontend → atteso sezione pull-quote renderizzata (markup `<section class="sl-casi__pull">`)
- Svuota i 4 field → save → ricarica → no render (conditional funziona)

### 3.C — JS filter toggle live test

Apri frontend `/chi-siamo/casi-rappresentativi/` in browser:
- Click "Privati" → solo row con `data-category="privati"` visibili
- Click "Tutti" → tutti row visibili
- Active state `.is-active` su button cliccato

(Se PHASE 2.E ha aggiunto `.sl-casi__row.is-hidden { display: none }` → funziona.)

---

## PHASE 4 — VERSION BUMP + COMMIT + PUSH

Version bump perché SCF additive (+4 field):

`functions.php` + `style.css`:
```
1.3.16-wave-design-handoff-p9-archive-casi
```

Commit:
```sh
git add -A
git diff --cached --stat

git commit -m "feat(design-handoff): Wave P9 archive-casi — pull-quote 'caso simbolo' ADDITIVE + filtri tabs (v1.3.16)

Wave 9/12 sequenza Design Handoff. Template archive-saltelli_caso.php arricchito con sezione pull-quote 'caso simbolo' (ADDITIVE editorial) + filtri tabs caso_categoria (JS vanilla client-side toggle).

SCF ADDITIVE (group_theme_options_v1.json tab 'Archive Headers' — 4 field nuovi):
- archive_caso_simbolo_eyebrow (text 60ch, default vuoto)
- archive_caso_simbolo_number (text 20ch, default vuoto)
- archive_caso_simbolo_quote (textarea 300ch, default vuoto)
- archive_caso_simbolo_attr (text 100ch, default vuoto)

Field esistenti (4 archive_caso_* + 4 archive_avvocato_* + 3 trust + 1 empty_text) INVARIATI.

PHP archive-saltelli_caso.php (+~20 righe):
- Sezione pull-quote conditional render (se 4 field tutti vuoti → no markup)
- Filtri tabs markup (hardcode 5: Tutti/Privati/Imprese/Contenzioso/Altri)
- data-category attribute sui row del loop casi (term slug caso_categoria)
- wp_enqueue_script archive-casi-filter.js (conditional is_post_type_archive)

JS NEW (assets/js/archive-casi-filter.js, ~40 righe vanilla):
- DOMContentLoaded → bind click sui .sl-casi__filter-btn
- Toggle .is-active su button cliccato
- Toggle .is-hidden sui row in base a data-filter/data-category match

CSS (sections.css scope /* === design-handoff archive-casi P9 === */):
- .sl-casi__row.is-hidden { display: none } [se mancante]

Decisioni orchestratore applicate:
- Opzione 1 architettura (4 field testuali, NO post_object) — coerente con pattern Theme Options
- Pull-quote conditional render (no fallback default value, Elena sceglie)
- Filtri hardcode 5 tab + vanilla JS (NO AJAX, NO query string server-side)

SCF: 🟡 ADDITIVE (4 field nuovi). group_caso_item_v1 + CPT saltelli_caso registration INVARIATI.

Smoke test:
- Frontend: pull-quote conditional verificato (vuoto → 0 markup, popolato → 1 markup)
- Filtri: 5 tab presenti, data-category sui row
- JS: enqueue su archive only, toggle filter funziona
- Admin: 4 field nuovi visibili in tab Archive Headers, popolazione → render frontend

Version bump: 1.3.15 → 1.3.16-wave-design-handoff-p9-archive-casi (SCF additive).
Branch: feat/design-handoff-archive-casi · ~4 file changed · +60-80 righe"

git tag -a v1.3.16-wave-design-handoff-p9-archive-casi -m "Design Handoff Wave P9 — archive-casi pull-quote ADDITIVE (4 field caso simbolo) + filtri tabs vanilla JS."

git push origin feat/design-handoff-archive-casi
```

---

## OUTPUT FINALE in chat

- Tabella PHASE 1 verify (CSS selectors check)
- 4 field SCF additive applicati
- PHP + JS additive
- Smoke test risultati (frontend conditional render + filtri + admin)
- SHA commit + tag pushato
- ETA proposto P10 glossario-legale

---

## HARD RULES

1. **Pull-quote architecture Opzione 1**: 4 field testuali. NO post_object (decisione orchestratore).
2. **Default values vuoti**: Elena sceglie quando popolare. NO precompilazione con caso fittizio.
3. **Conditional render**: pull-quote NO renderizzata se 4 field tutti vuoti.
4. **Filtri hardcode 5 tab**: NO dynamic query taxonomy (scope creep).
5. **JS vanilla client-side**: NO AJAX, NO query string server-side.
6. **SCF schema preservato**: 4 archive_caso_* + 4 archive_avvocato_* + 3 trust + 1 empty_text + group_caso_item_v1 + CPT registration — TUTTO INVARIATO.
7. **OPcache reload obbligatorio** post-edit `archive-saltelli_caso.php` o `functions.php` (lesson Wave 4.7.fix.3).
8. **Admin-side smoke obbligatorio** (lesson Wave 4.7.fix.4).
9. **One-writer-at-a-time**.

---

## DECISIONE AUTONOMA AUTORIZZATA

- Enqueue JS in `functions.php` (pattern consistency, conditional `is_post_type_archive('saltelli_caso')`) o inline `archive-saltelli_caso.php` top — orchestratore preferisce functions.php se coerente con altri enqueue del progetto.
- Scope marker CSS wording: `/* === design-handoff archive-casi P9 === */`
- Wrapper width SCF field admin: text=full, textarea=full (UX clarity Elena).
- Tab order field "Archive Headers": aggiungi i 4 nuovi DOPO `archive_caso_empty_text` esistente.

---

## TONO

Direct, concrete, zero filler. Stile commit progetto.

---

*Wave P9/12 sequenza Design Handoff. Prossima: P10 glossario-legale (verify + drift). Pattern lean = 1 wave alla volta.*
