# PROMPT AGENT — Design Handoff Wave P11 · contatti verify + select aree dinamico

> **Scope**: verificare `page-contatti.php` + `.sl-contatti-w3*` di sections.css vs `design-handoff/contatti/index.jsx`. Refactor select "Area di interesse" hardcoded → dinamico da CPT competenza. NO mappa iframe (decisione orchestratore preserved v0.17.3).
>
> **Branch**: `feat/design-handoff-contatti`
> **Stima**: 1-1.5h (severity 🟡 MEDIUM)
> **Modalità**: lean, no version bump (chore frontend)
> **Sessione**: una sola Claude Code.

---

## CONTESTO

Wave 11/12 sequenza Design Handoff. P10 glossario + P9 archive-casi + P8 blog-archive in sequenza prima di questa (C→B→A→P11).

**Decisioni orchestratore acquisite** (NON rinegoziare):
1. SoT design tokens = `tokens.css` corrente vince (KEEP CURRENT)
2. **NO mappa OpenStreetMap iframe** (decisione v0.17.3 preserved, ACF field `map_iframe` resta vuoto → no render)
3. **Select aree dinamico**: query CPT `competenza` (19 items match 1:1 con JSX hardcoded). NO term `tipo-area` (sono solo 4 cluster, non aree)
4. **Font 22px address**: `var(--fs-lede)` (lede stilistico)
5. **Font 22px come-t**: `var(--fs-h3-floor)` (heading h3)
6. **Phantom 17px form / 28px hero-lede**: defer Wave 5 STEP 5
7. SCF data contract immutabile: 19 field `group_contatti_v1` Elena-approved → 🟢 COMPLIANT (0 additive)

**Pre-flight orchestratore già fatto**:

| Voce | Valore |
|---|---|
| JSX sezioni mappate | 9/9 (hero + form + NAP + mappa BLOCCATA + CTA + orari + come-arrivare + trust) |
| Drift CSS | 6 phantom (2 fix oggi, 4 defer) |
| SCF | 🟢 100% match, 0 additive |
| Select aree refactor | 19 hardcoded → CPT competenza query (8-12 righe PHP) |
| Mappa iframe | BLOCCATA — NO impl |
| Severity | 🟡 MEDIUM, ETA 1-1.5h |
| Righe totali | ~23 (15 CSS + 8 PHP) |

---

## ⚠️ HARD INVARIANT

1. **NO mappa iframe**: decisione v0.17.3 preserved. ACF `map_iframe` lascia vuoto, template skip render conditional.
2. **`group_contatti_v1` 19 field invariati** (Wave 4.7.fix.4 + Wave 5 STEP 3 P7 Elena-approved).
3. **CPT competenza registration**: invariato. Solo READ query, no schema touch.
4. **Phantom defer Wave 5 STEP 5**: 17px form inputs, 28px hero-lede.

---

## PRE-FLIGHT (5 min)

1. Leggi:
   - `CLAUDE.md` (Hard constraints, Design system, Lessons learned)
   - `.claude/knowledge/audits/design-handoff/RECOMMENDATION.md` (§J contatti DESIGN RE-INTERPRETATION #1, §B P11)
   - **JSX source**: `design-handoff/contatti/index.jsx`
   - **WP target**:
     - `wp-content/themes/saltelli/template-parts/page-contatti.php`
     - Blocchi `.sl-contatti-w3*` in sections.css
   - `wp-content/themes/saltelli/acf-json/group_contatti_v1.json` (verifica 19 field invariati)

2. Verifica stato:
   ```sh
   git fetch origin
   git status
   git log --oneline -3   # atteso HEAD post-P8 merge (o P10 se sequenza C→B→A in corso)
   git checkout -b feat/design-handoff-contatti
   ```

3. Conferma in chat + prosegui.

---

## PHASE 1 — VERIFY (10 min)

### 1.A — Drift CSS fix (2 risolti oggi)

| Selector | Property | Current | Fix | Reason |
|---|---|---|---|---|
| `.sl-contatti-w3__address` | font-size | `22px` literal | `var(--fs-lede)` | Lede stilistico (Playfair italic) |
| `.sl-contatti-w3__come-t` | font-size | `22px` literal | `var(--fs-h3-floor)` | Heading h3 della 3-col |

### 1.B — Phantom defer (4 noti, NON fixare)

- `.sl-contatti-w3__hero-lede` 28px (defer Wave 5 STEP 5 — candidate `--fs-lede-lg`)
- `.wpcf7-form input/select/textarea` 17px (defer — candidate `--fs-lede-mobile`)
- `.sl-contatti-w3__hero h1` 0.95 lh, -0.035em ls (matched JSX inline, leave per-selector)

### 1.C — Verifica mappa iframe NOT renderizzata

```sh
grep -n "map_iframe\|openstreetmap" wp-content/themes/saltelli/template-parts/page-contatti.php
# Atteso: ACF read presente ma conditional `if ($map_iframe)` → se field vuoto, skip render
```

Conferma decision NO mappa: lascia ACF field `map_iframe` vuoto (default), template skippa.

### 1.D — SCF reads verify

Verifica 19 field `group_contatti_v1` reads:
- `hero_eyebrow`, `hero_h1_pre`, `hero_h1_em`, `hero_lede`
- `contatti_form_eyebrow`, `contatti_form_h2_main`, `contatti_form_h2_em`
- `contatti_success_eyebrow`, `contatti_success_h3`, `contatti_success_text`
- `contatti_aside_eyebrow`, `contatti_whatsapp_cta_label`
- `contatti_come_eyebrow`, `contatti_come_item{1,2,3}_{label,title,desc}`
- `contatti_trust_eyebrow`
- `map_iframe` (lascia vuoto), `map_caption`

Atteso 100% match, 0 additive.

---

## PHASE 2 — IMPLEMENT (35-45 min)

### 2.A — CSS fix in sections.css

Scope marker `/* === design-handoff contatti P11 === */`:

```css
/* === design-handoff contatti P11 === */

.sl-contatti-w3__address {
  font-size: var(--fs-lede); /* was 22px literal — token alignment lede stilistico */
}

.sl-contatti-w3__come-t {
  font-size: var(--fs-h3-floor); /* was 22px literal — heading h3 della 3-col */
}
```

### 2.B — Select aree dinamico (page-contatti.php)

Sostituisci array hardcoded di 19 aree con query CPT competenza:

```php
<?php
// Wave P11: select "Area di interesse" dinamico da CPT competenza
$aree_competenze = get_posts([
    'post_type'      => 'competenza',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
    'post_status'    => 'publish',
]);
?>

<select name="area" id="contatti-area" class="sl-contatti-w3__form-select" aria-label="<?php esc_attr_e('Area di interesse', 'saltelli'); ?>">
  <option value="">— <?php esc_html_e('Scegli area di interesse', 'saltelli'); ?> —</option>
  <option value="generale"><?php esc_html_e('Consulenza generale', 'saltelli'); ?></option>
  <?php foreach ($aree_competenze as $area) : ?>
    <option value="<?php echo esc_attr($area->post_name); ?>"><?php echo esc_html($area->post_title); ?></option>
  <?php endforeach; ?>
</select>
```

(Adatta naming campo `name="area"` al pattern Contact Form 7 attuale o form custom — verifica handle in `wpcf7` settings se serve.)

### 2.C — Sync staging + reload

```sh
rsync -avz wp-content/themes/saltelli/assets/css/ deploy@178.62.207.50:/var/www/saltelli/wp-content/themes/saltelli/assets/css/
rsync -avz wp-content/themes/saltelli/template-parts/page-contatti.php deploy@178.62.207.50:/var/www/saltelli/wp-content/themes/saltelli/template-parts/page-contatti.php
ssh deploy@178.62.207.50 "sudo systemctl reload php8.2-fpm && cd /var/www/saltelli && sudo -u www-data wp cache flush --path=/var/www/saltelli"
```

(OPcache reload obbligatorio post-edit template — lesson Wave 4.7.fix.3).

---

## PHASE 3 — SMOKE TEST (10-15 min)

### 3.A — Frontend curl

```sh
echo "=== /contatti/ HTTP status ==="
curl -sI "https://staging.studiolegalesaltelli.it/contatti/" | head -3

echo "=== 19 aree competenza nel select ==="
curl -s "https://staging.studiolegalesaltelli.it/contatti/" | grep -cE '<option value="(diritto-|consulenza|generale)"'
# Atteso count >= 20 (1 placeholder + 1 generale + 19 competenze)

echo "=== NO iframe mappa ==="
curl -s "https://staging.studiolegalesaltelli.it/contatti/" | grep -c "openstreetmap"
# Atteso: 0 (mappa BLOCCATA, ACF map_iframe vuoto)

echo "=== altre sezioni markup ==="
curl -s "https://staging.studiolegalesaltelli.it/contatti/" | grep -cE "sl-contatti-w3__hero|sl-contatti-w3__form|sl-contatti-w3__nap|sl-contatti-w3__come|sl-contatti-w3__trust"
# Atteso: count >= 5 (markup 5 sezioni presenti)
```

### 3.B — getComputedStyle spot-check

- `.sl-contatti-w3__address` font-size atteso `22px` (computed da `var(--fs-lede)`)
- `.sl-contatti-w3__come-t` font-size atteso `22px` (computed da `var(--fs-h3-floor)`)

### 3.C — Form functional smoke

Apri `https://staging.studiolegalesaltelli.it/contatti/` in browser:
- Select "Area di interesse" → vedi 19 aree competenza + 1 generale + 1 placeholder
- Aree ordinate alphabetically by post_title
- Form submit (test send): verifica che `area` value sia post_name (es. `diritto-tributario`) per backend tracking

### 3.D — Admin smoke (lesson Wave 4.7.fix.4)

WP Admin → Pagine → Contatti (Page 23):
- Metabox SCF `group_contatti_v1` visibile, 19 field popolati Wave 5 STEP 3 P7 invariati
- Gutenberg disabled ✓ (SALTELLI_SCF_ONLY_PAGES)
- ACF `map_iframe` field VUOTO (decisione preserved)
- Save senza modifiche → frontend invariato

---

## PHASE 4 — COMMIT + PUSH

```sh
git add -A
git diff --cached --stat

git commit -m "feat(design-handoff): Wave P11 contatti — token alignment + select aree dinamico (CPT competenza)

Wave 11/12 sequenza Design Handoff. Template page-contatti.php verify + 2 token alignment + refactor select aree hardcoded → dinamico da CPT competenza.

CSS additivo (sections.css scope /* === design-handoff contatti P11 === */):
- .sl-contatti-w3__address font-size: 22px → var(--fs-lede) [lede stilistico Playfair italic]
- .sl-contatti-w3__come-t font-size: 22px → var(--fs-h3-floor) [heading h3 3-col come arrivare]

PHP refactor (page-contatti.php +8 righe):
- Select 'Area di interesse' hardcoded 19 aree → get_posts('competenza') dinamico
- Naming match 1:1 con JSX, ordered by title ASC, option value = post_name slug

Decisioni orchestratore applicate:
- NO mappa OpenStreetMap iframe (decisione v0.17.3 preserved). ACF map_iframe field vuoto → no render conditional.
- Select aree → CPT competenza (NO term tipo-area che sono solo 4 cluster, non 19 aree)
- Font 22px address → var(--fs-lede); font 22px come-t → var(--fs-h3-floor) (per-selector resolve)
- Phantom 17px form / 28px hero-lede / 0.95 lh / -0.035em ls: defer Wave 5 STEP 5

SCF: 🟢 COMPLIANT. group_contatti_v1 19 field invariati. ACF map_iframe vuoto preserved.

Smoke test:
- Frontend curl: 19 aree CPT competenza in select, 0 iframe mappa, 5 sezioni markup presenti
- getComputedStyle: address 22px (--fs-lede), come-t 22px (--fs-h3-floor)
- Form: select dinamico funzionale, aree ordered alphabetically
- Admin: metabox SCF 19 field intatti, Gutenberg disabled

No version bump (chore frontend + PHP refactor, no schema/data change).
Branch: feat/design-handoff-contatti · 2 file changed · ~23 righe (15 CSS + 8 PHP)"

git push origin feat/design-handoff-contatti
```

---

## OUTPUT FINALE in chat

- Tabella PHASE 1 (2 fix CSS + 4 defer documentati + 0 additive SCF)
- Select aree refactor confermato (19 CPT competenza)
- Smoke test risultati (curl + form + admin)
- SHA commit pushato
- ETA proposto P12 404

---

## HARD RULES

1. **NO mappa iframe**: decisione preserved. `map_iframe` field lascia vuoto.
2. **CPT competenza READ only**: query `get_posts()`, NO touch registration.
3. **SCF schema preservato**: 19 field invariati.
4. **Token alignment §A**: KEEP CURRENT, mai toccare `:root`.
5. **OPcache reload obbligatorio** post-edit page-contatti.php.
6. **Admin-side smoke obbligatorio**.
7. **One-writer-at-a-time**.

---

## DECISIONE AUTONOMA AUTORIZZATA

- Form integration pattern: se Contact Form 7 attivo (`wpcf7-form` markup), adatta select alla syntax `[select area "—" "..."]` CF7 shortcode. Se form custom HTML, usa il `<select>` markup proposto.
- Scope marker CSS: `/* === design-handoff contatti P11 === */`
- Ordering aree: ASC by title (alphabetic). Se preferisci order custom (es. tier-1 first), documenta in commit.
- Option value: `post_name` slug (es. `diritto-tributario`). Per backend tracking + CF7 dropdown compatibility.

---

## TONO

Direct, concrete, zero filler. Stile commit progetto.

---

*Wave P11/12 sequenza Design Handoff. Prossima: P12 404 (verify + drift, ultima della sequenza). Pattern lean = 1 wave alla volta.*
