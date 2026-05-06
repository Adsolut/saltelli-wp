# PROMPT v1.1 WAVE 6 — Extension blocchi GEO/CRO (pattern adaptation lean, post-Wave5)

## 📝 Changelog v1.0 → v1.1 (2026-05-06 sere — post-Wave5 merge)

Modifiche dal prompt v1.0 al v1.1, sulla base di DEC-024 finale Wave 5:

1. **Phase 0 RIMOSSA**: `saltelli_option()` helper NON va aggiunto. Esiste già in `inc/helpers.php` line 503 (assunzione orchestratore precedente errata).
2. **URL pattern aggiornati a slug brevi DEC-022**: `/privati/`, `/imprese/`, `/contenzioso-amministrativo/` (NO `per-i-privati`).
3. **Slug REALI delle 17 competenze incorporati**: `diritto-tributario`, `diritto-del-lavoro`, `diritto-di-famiglia-lgbtq` (NO più `diritto-di-famiglia` eliminato in DISCOVERY-01), ecc. Vedi `migration-matrix-v3.csv`.
4. **Acceptance gate aggiunto**: dopo ogni `add_rewrite_rule + wp rewrite flush`, eseguire `wp rewrite list | grep <pattern>` per verificare attivazione (lesson learned Wave 5 BLOCKER A).
5. **Filter `request` priority 5 pattern**: pattern Wave 5 FIX A è ora documentato in `WAVE6_CALIBRATION_NOTES.md` come pattern di riferimento per future URL pattern collision con page hierarchy.
6. **Coabitazione Yoast**: nota aggiunta su FAQPage schema generalization (CAL-W6-07).
7. **Tier-1 deep slug aggiornati**: `diritto-di-famiglia-lgbtq` invece di `diritto-di-famiglia`. NB: `infortunistica-stradale` e `aste-immobiliari` sono Tier-2 (NEW Wave 5), NON Tier-1.

**Contesto pre-launch**: Wave 5 mergeata. Sito staging deployabile via rsync delta. Theme version `1.1.0-wave5-ia-refactor`. 17 aree finali attive.

---


> **Audience**: Claude Code agent in sessione terminale dedicata.
> **Read this FIRST**: `CLAUDE.md` + this prompt + **`pattern-adaptation-map.md`** (input principale).
> **Branch dedicato**: `feat/wave6-geo-cro-blocks` da `main` post-Wave5.
> **Prerequisito**: Wave 5 mergeata su main. Theme version `1.1.0-wave5-ia-refactor`.
> **One-writer-at-a-time HARD RULE**: orchestratore non committa durante questa wave.

---

## 🎯 Tu sei

Claude Code agent dedicato alla **Wave 6 — Extension blocchi GEO/CRO** del progetto Saltelli. La sitemap firmata è già implementata (Wave 5). I 17 acceptance criteria del Friction Points & CRO Patterns analysis identificano 10 pattern GEO/CRO da aggiungere al MVP. DEC-019 ha stabilito che questi pattern vengono **adattati da componenti esistenti del DS** (NO Sessione 3 di Claude Design), per velocità + economia.

Il deliverable centrale di questa Wave è il file **`pattern-adaptation-map.md`** che mappa 1:1 ognuno dei 10 pattern sui componenti DS esistenti, con specifica esatta di: ACF Field da estendere, template-part da creare/modificare, CSS preciso, trade-off accettati, schema impact.

Tuo compito: **implementare i 10 pattern come specificato in pattern-adaptation-map.md**, senza inventare design né mockup intermedi. Ogni decisione di design è già stata presa dall'orchestratore + cliente.

**NON tocchi**: stack tecnologico (DEC-013), DS tokens (DEC-018), schema JSON-LD partials esistenti (sono OK), URL/CPT/tassonomie (Wave 5 ha già fatto), font/CSS optimization (è Wave 4 — DEC-020).

---

## 📚 Letture obbligatorie

1. **`CLAUDE.md`** — single source of truth
2. **`pattern-adaptation-map.md`** — INPUT PRINCIPALE: 10 pattern con spec esatta
3. **`docs/ARCHITECTURE.md`** § 2 (ACF Field Groups) + § 6 (helpers) + § 7 (schema JSON-LD)
4. **`docs/DESIGN.md`** — DS tokens locked + componenti DS attuali
5. **Deliverable orchestratore**:
   - `mvp-state-snapshot.md` — stato corrente
   - `friction-points-and-cro-patterns-v2.md` — 17 acceptance criteria con annotazione "stato MVP"
   - `decision-log.md` DEC-018/019/020

---

## 🔒 Hard rules (non negotiabili)

| Regola | Motivo |
|---|---|
| **NO modifica DS tokens** | DEC-018 |
| **NO modifica URL/CPT/tassonomie** | Wave 5 ha già fatto |
| **NO modifica schema partials esistenti** salvo `partial-faqpage.php` per generalizzazione | Wave 6 estende, non riscrive |
| **NO nuove dipendenze JS** | Wave 6 è pure CSS + PHP |
| **NO nuovi font o palette colori** | DS locked |
| **NO carousel JS** per testimonials | Solo grid statico (DEC-019 trade-off) |
| **NO foto cliente / star rating** | Privacy + brand editoriale |
| **MAI commit diretti su `main`** | Branch policy |
| **`prefers-reduced-motion: reduce`** opt-out per ogni nuova transition | A11y hard rule MVP |
| **`aria-label`** per ogni componente interattivo nuovo | A11y |
| **Acceptance criteria operativi** in pattern-adaptation-map vanno tutti spuntati | Quality gate |

---

## 📋 PHASE 1 — Backup + branch + ACF Field Groups extension (~60 min)

### 1.1 — Snapshot

```bash
tar -czf /tmp/saltelli-pre-wave6-$(date +%Y%m%d-%H%M).tar.gz wp-content/themes/saltelli/
docker-compose exec -T db mysqldump -u root -p${MYSQL_ROOT_PASSWORD} saltelli_wp > /tmp/saltelli-pre-wave6-db.sql
```

### 1.2 — Branch

```bash
git fetch origin main && git checkout main && git pull --ff-only
git checkout -b feat/wave6-geo-cro-blocks
```

### 1.3 — ACF Field Groups extension

Estensioni richieste (vedi `pattern-adaptation-map.md` per dettaglio):

**`group_competenza_v1`** (CPT competenza × 19) — aggiungi:
- `answer_capsule` (textarea, max 400 char, helper text in IT)
- `related_competenze` (relationship, post_object competenza, max 3, returnFormat=object)
- `cta_top_label` (text, default "Leggi i casi rappresentativi")
- `cta_top_url` (url, opzionale)
- `cta_middle_label` (text, default Theme Options `cta_label`)
- `cta_middle_url` (url, default Theme Options `cta_url`)

**`group_trust_item_v1`** (CPT saltelli_trust × 4 estendibili) — aggiungi:
- `testimonial_type` (radio: "Numero" / "Testimonianza", default "Numero")
- `testimonial_text` (textarea max 280 char, conditional su type=Testimonianza)
- `testimonial_author` (text, conditional)
- `testimonial_city` (text, default "Napoli", conditional)
- `testimonial_topic` (taxonomy field tipo-area o topic select, conditional)
- `source_label` (text, default "Fonte", opzionale)
- `source_text` (text, opzionale)
- `source_url` (url, opzionale)

**`group_avvocato_v1`** (CPT avvocato × 4) — aggiungi:
- `byline_extended` (textarea max 200 char)
- `expertise_topics` (relationship, post_object competenza, max 3 per byline cross-link)
- `competenze_trattate` (relationship, post_object competenza, max 5 per pagina avvocato)

**`group_theme_options_v1`** Tab "Brand" — aggiungi:
- `trust_signal_1_label` (text, default "20+ ANNI")
- `trust_signal_1_caption` (text, default "ESPERIENZA")
- `trust_signal_2_label` (text, default "4 AVVOCATI")
- `trust_signal_2_caption` (text, default "TEAM SPECIALIZZATO")
- `trust_signal_3_label` (text, default "19 AREE")
- `trust_signal_3_caption` (text, default "DI PRATICA")
- `trust_signal_4_label` (text, default "COA FAMIGLIA")
- `trust_signal_4_caption` (text, default "MUNICIPALITÀ 1")

Ogni Field Group viene esportato come JSON in `acf-json/` (auto-sync ACF Local JSON). Verifica che i file `acf-json/group_*.json` siano aggiornati e committati.

### 1.4 — Smoke ACF post-extension

```bash
docker-compose exec -T wp wp acf clean
docker-compose exec -T wp wp cache flush
```

Verifica WP-Admin → Aree di pratica → Tributario: deve mostrare nuovi field `answer_capsule`, `related_competenze`, ecc.

---

## 📋 PHASE 2 — Nuovi template-part (~90 min)

Tutti i template-part vanno in `wp-content/themes/saltelli/template-parts/` seguendo le specifiche esatte di `pattern-adaptation-map.md`. Ogni template-part:

1. Ha graceful fallback (`saltelli_field` con default + condizioni `if (!empty($field))`)
2. Ha `aria-label` se interattivo
3. Usa solo classi `.sl-*` (esistenti o nuove definite in cro.css)
4. Non duplica markup tra template-part diversi

Lista template-part da creare:

| File | Pattern adattato | Riferimento map |
|---|---|---|
| `template-parts/trust-bar.php` | Trust bar globale | Pattern 2 |
| `template-parts/mobile-sticky-bar.php` | Sticky bottom mobile 3 azioni | Pattern 3 |
| `template-parts/mini-form.php` | Inline mini-form contestuale | Pattern 4 |
| `template-parts/testimonials-block.php` | Testimonials block | Pattern 6 |

Esempi minimal:

**`trust-bar.php`**:

```php
<?php
/**
 * Template part: Trust Bar globale
 * Wave 6 Pattern 2 — adapted from .sl-mono + .sl-rule
 * Usage: <?php get_template_part('template-parts/trust-bar'); ?>
 */
?>
<aside class="sl-trust-bar" aria-label="Trust signals">
    <?php for ($i = 1; $i <= 4; $i++) :
        $label = saltelli_option("trust_signal_{$i}_label");
        $caption = saltelli_option("trust_signal_{$i}_caption");
        if (empty($label)) continue;
    ?>
        <div class="sl-trust-bar__item">
            <div class="sl-trust-bar__label"><?php echo esc_html($label); ?></div>
            <div class="sl-trust-bar__caption"><?php echo esc_html($caption); ?></div>
        </div>
    <?php endfor; ?>
</aside>
```

**`mobile-sticky-bar.php`**:

```php
<?php
/**
 * Template part: Mobile Sticky Bottom Bar (3 azioni)
 * Wave 6 Pattern 3 — adapted from .sl-attorney__sticky + .sl-whatsapp-sticky
 * Hidden via CSS on /contatti/ + single-avvocato + desktop
 */
$studio = saltelli_studio_data();
$phone = saltelli_phone_e164($studio['phone'] ?? '');
$whatsapp = saltelli_phone_e164($studio['whatsapp'] ?? '');
?>
<aside class="sl-mobile-bar" aria-label="Contatti rapidi">
    <?php if (!empty($phone)) : ?>
        <a href="tel:<?php echo esc_attr($phone); ?>" class="sl-mobile-bar__action">
            <span class="sl-mobile-bar__icon" aria-hidden="true">☎</span>
            Chiama
        </a>
    <?php endif; ?>
    <?php if (!empty($whatsapp)) : ?>
        <a href="https://wa.me/<?php echo esc_attr(ltrim($whatsapp, '+')); ?>" class="sl-mobile-bar__action" rel="noopener">
            <span class="sl-mobile-bar__icon" aria-hidden="true">WA</span>
            WhatsApp
        </a>
    <?php endif; ?>
    <a href="/contatti/" class="sl-mobile-bar__action">
        <span class="sl-mobile-bar__icon" aria-hidden="true">✎</span>
        Scrivi
    </a>
</aside>
```

**`mini-form.php`** + **`testimonials-block.php`**: vedi spec dettagliata in `pattern-adaptation-map.md` Pattern 4 + Pattern 6.

---

## 📋 PHASE 3 — Estensione template files (~90 min)

### 3.1 — `single-competenza.php`

Aggiungi sotto `<h1>`:

```php
<?php
$answer_capsule = saltelli_field('answer_capsule', $post->ID);
if (!empty($answer_capsule)) : ?>
    <div class="sl-answer-capsule">
        <p class="sl-mono">RISPOSTA RAPIDA · 2 MIN LETTURA</p>
        <p class="sl-competenza__lede"><?php echo wp_kses_post($answer_capsule); ?></p>
    </div>
<?php endif; ?>
```

Aggiungi CTA progressive (top dopo answer, middle prima FAQ, bottom):

```php
<?php
$cta_top = saltelli_field('cta_top_label', $post->ID);
$cta_top_url = saltelli_field('cta_top_url', $post->ID);
if (!empty($cta_top) && !empty($cta_top_url)) : ?>
    <a href="<?php echo esc_url($cta_top_url); ?>" class="sl-btn sl-btn--ghost"><?php echo esc_html($cta_top); ?> →</a>
<?php endif; ?>
```

Aggiungi mini-form prima della section related:

```php
<?php get_template_part('template-parts/mini-form', null, ['topic_default' => $post->post_name]); ?>
```

Aggiungi related-services (sotto FAQ, sopra mini-form):

```php
<?php
$related = saltelli_field('related_competenze', $post->ID);
if (!empty($related) && is_array($related)) : ?>
    <section class="sl-related-services">
        <p class="sl-mono">AREE CORRELATE</p>
        <h2>Approfondisci</h2>
        <?php foreach ($related as $rel) : ?>
            <a href="<?php echo esc_url(get_permalink($rel->ID)); ?>" class="sl-area sl-area--related">
                <div class="sl-area__num"><?php echo wp_kses_post(saltelli_field('eyebrow', $rel->ID, 'AREA')); ?></div>
                <div class="sl-area__title"><?php echo esc_html(get_the_title($rel)); ?></div>
                <div class="sl-area__meta">→</div>
            </a>
        <?php endforeach; ?>
    </section>
<?php endif; ?>
```

### 3.2 — `single.php` (blog) — Author byline ricca

Aggiungi sotto `<h1>`:

```php
<?php
$author_id = get_post_field('post_author', $post->ID);
// Mappa author WP user → CPT avvocato (assumendo stessa email o via meta)
$avvocato = get_posts([
    'post_type' => 'avvocato',
    'meta_query' => [['key' => 'wp_user_id', 'value' => $author_id]],
    'posts_per_page' => 1,
]);
if (!empty($avvocato)) :
    $avvocato = $avvocato[0];
    $byline = saltelli_field('byline_extended', $avvocato->ID);
    $expertise = saltelli_field('expertise_topics', $avvocato->ID);
?>
    <div class="sl-author-byline">
        <p class="sl-mono">
            <?php echo esc_html(get_the_date('j F Y', $post)); ?> · 
            LETTURA <?php echo saltelli_reading_time($post->post_content); ?> MIN · 
            DI <?php echo esc_html(get_the_title($avvocato)); ?>
        </p>
        <?php if (!empty($byline)) : ?>
            <p class="sl-author-byline__bio"><?php echo wp_kses_post($byline); ?></p>
        <?php endif; ?>
        <?php if (!empty($expertise) && is_array($expertise)) : ?>
            <ul class="sl-author-expertise">
                <?php foreach ($expertise as $topic) : ?>
                    <li><a href="<?php echo esc_url(get_permalink($topic->ID)); ?>" class="sl-tag"><?php echo esc_html(get_the_title($topic)); ?></a></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
<?php endif; ?>
```

⚠️ **Helper `saltelli_reading_time()`** da aggiungere a `inc/helpers.php`:

```php
function saltelli_reading_time($content) {
    $word_count = str_word_count(strip_tags($content));
    $minutes = ceil($word_count / 200);  // 200 wpm media italiano
    return $minutes;
}
```

### 3.3 — `single-avvocato.php` — Competenze trattate

Sostituisci eventuale lista hardcoded di "competenze trattate" con loop su CPT relationship `competenze_trattate`:

```php
<?php
$competenze_trattate = saltelli_field('competenze_trattate', $post->ID);
if (!empty($competenze_trattate) && is_array($competenze_trattate)) : ?>
    <section class="sl-attorney__expertise">
        <p class="sl-mono">AREE DI COMPETENZA</p>
        <?php foreach ($competenze_trattate as $i => $comp) : ?>
            <a href="<?php echo esc_url(get_permalink($comp->ID)); ?>" class="sl-area">
                <div class="sl-area__num"><?php printf('%02d / %02d', $i + 1, count($competenze_trattate)); ?></div>
                <div class="sl-area__title"><?php echo esc_html(get_the_title($comp)); ?></div>
                <div class="sl-area__meta">→</div>
            </a>
        <?php endforeach; ?>
    </section>
<?php endif; ?>
```

### 3.4 — `front-page.php` — Trust bar + Testimonials block

Aggiungi nelle sezioni opportune (decisione orchestratore se mostrare, basata su quanto popolato):

```php
<?php get_template_part('template-parts/trust-bar'); ?>
<?php get_template_part('template-parts/testimonials-block'); ?>
```

### 3.5 — `template-parts/page-costi.php` — CTA progressive + mini-form

Aggiungi mini-form al fondo:

```php
<?php get_template_part('template-parts/mini-form', null, ['topic_default' => 'costi']); ?>
```

### 3.6 — `footer.php` — Mobile sticky bar

Aggiungi prima di `wp_footer()`:

```php
<?php get_template_part('template-parts/mobile-sticky-bar'); ?>
```

### 3.7 — `partial-faqpage.php` — Generalizzazione FAQ schema a tutte le competenze

Estendi il check del partial: se `is_singular('competenza')` **e** `saltelli_field('faq_associate')` non vuoto, emette schema FAQPage. Oggi è limitato a Tier-1, generalizza a tutte le competenze.

---

## 📋 PHASE 4 — Nuovo CSS bundle `cro.css` (~60 min)

Crea file `wp-content/themes/saltelli/assets/css/components/cro.css` con tutto il CSS dei pattern adattati. Riferisciti a `pattern-adaptation-map.md` per CSS preciso di ogni pattern.

Include (in ordine):

1. `.sl-answer-capsule` (Pattern 1)
2. `.sl-trust-bar`, `.sl-trust-bar__item`, `.sl-trust-bar__label`, `.sl-trust-bar__caption`, `.sl-trust-bar__source` (Pattern 2 + 7)
3. `.sl-mobile-bar`, `.sl-mobile-bar__action`, `.sl-mobile-bar__icon` (Pattern 3) — con responsive `@media (max-width: 768px)` + body class exclusion
4. `.sl-mini-form`, `.sl-mini-form__title`, `.sl-mini-form__lede`, `.sl-mini-form input/select/textarea`, `.sl-mini-form button[type=submit]` (Pattern 4)
5. `.sl-testimonials`, `.sl-testimonial`, `.sl-testimonial__topic`, `.sl-testimonial__quote`, `.sl-testimonial__attribution` (Pattern 6)
6. `.sl-author-byline`, `.sl-author-byline__bio`, `.sl-author-expertise` (Pattern 9)
7. `.sl-related-services`, `.sl-area--related` (Pattern 10)

Ogni regola CSS:
- Usa `var(--*)` da tokens.css (NO valori hardcoded)
- Include `prefers-reduced-motion: reduce` per ogni transition
- Include responsive breakpoint @ 768px

---

## 📋 PHASE 5 — Aggiornamento `inc/enqueue.php` (~10 min)

Aggiungi enqueue di `cro.css` dopo `components.css`:

```php
wp_enqueue_style(
    'saltelli-cro',
    get_template_directory_uri() . '/assets/css/components/cro.css',
    ['saltelli-components'],
    SALTELLI_THEME_VERSION
);
```

---

## 📋 PHASE 6 — Smoke test + Lighthouse (~30 min)

### 6.1 — Smoke test 21 URL post-Wave 6

Stesso set di URL di Wave 5 (post-refactor IA). Tutti devono restituire 200.

### 6.2 — Test rendering pattern

Apri manualmente in browser staging:

- `https://staging.studiolegalesaltelli.it/aree-di-pratica/privati/diritto-tributario/` — verifica answer-capsule + CTA progressive + FAQ + related-services + mini-form
- `https://staging.studiolegalesaltelli.it/chi-siamo/team/antonia-battista/` — verifica competenze trattate come .sl-area rows
- `https://staging.studiolegalesaltelli.it/risorse/blog/{primo-post}/` — verifica author byline ricca
- `https://staging.studiolegalesaltelli.it/` (mobile, viewport 375px) — verifica mobile sticky bar visible
- `https://staging.studiolegalesaltelli.it/contatti/` (mobile) — verifica mobile sticky bar HIDDEN

### 6.3 — Lighthouse no-regression

```bash
npx lighthouse https://staging.studiolegalesaltelli.it/aree-di-pratica/privati/diritto-tributario/ --emulated-form-factor=mobile --output=html --output-path=.claude/knowledge/audits/wave6/lh-mobile-tributario.html --quiet
```

Atteso: nessuna regression rispetto a Lighthouse Wave 5 baseline.

### 6.4 — Schema validation

Test FAQPage schema su una competenza Tier-2 (oggi esce solo su Tier-1):

```bash
curl -s "https://staging.studiolegalesaltelli.it/aree-di-pratica/privati/cartelle-esattoriali-e-multe/" | grep -A 50 'type":"FAQPage'
```

Atteso: schema FAQPage emesso se la competenza ha `faq_associate` popolate.

---

## 📋 PHASE 7 — Audit + commit + merge prep (~30 min)

### 7.1 — Bump version

```php
define('SALTELLI_THEME_VERSION', '1.2.0-wave6-geo-cro-blocks');
```

### 7.2 — Documentazione

Crea `.claude/knowledge/recovery/WAVE6-GEO-CRO-REPORT.md` con:

- Phase-by-phase summary
- Lista 10 pattern implementati con riferimento a `pattern-adaptation-map.md` Pattern N
- Diff stat (`git diff main...feat/wave6-geo-cro-blocks --stat`)
- Smoke test artifacts
- Acceptance criteria pattern-by-pattern (10 pattern × N criteri = ~50 check)
- Bug noti / pending
- Note per orchestratore

### 7.3 — Commit policy

```bash
git commit -m "wave6: phase 1 — backup + ACF Field Groups extension (4 group estesi)"
git commit -m "wave6: phase 2 — 4 nuovi template-part (trust-bar, mobile-sticky-bar, mini-form, testimonials-block)"
git commit -m "wave6: phase 3 — estensione template files (single-competenza, single, single-avvocato, front-page, page-costi, footer, partial-faqpage)"
git commit -m "wave6: phase 4-5 — nuovo cro.css + enqueue update"
git commit -m "wave6: phase 6 — smoke + Lighthouse no-regression + schema validation"
git commit -m "wave6: bump version to 1.2.0-wave6-geo-cro-blocks + docs"
```

### 7.4 — Push branch

```bash
git push origin feat/wave6-geo-cro-blocks
```

NO merge automatico. Orchestratore farà audit + merge.

---

## ✅ Acceptance criteria Wave 6 (orchestrator audit checklist)

Vedi sezione "Indicatori di completamento Wave 6" in `pattern-adaptation-map.md` (~30 check).

In sintesi:

- [ ] Backup pre-Wave 6 presente
- [ ] 4 ACF Field Groups estesi correttamente (group_competenza_v1, group_trust_item_v1, group_avvocato_v1, group_theme_options_v1)
- [ ] 4 nuovi template-part creati e funzionanti (trust-bar, mobile-sticky-bar, mini-form, testimonials-block)
- [ ] Estensioni template (single-competenza, single, single-avvocato, front-page, page-costi, footer, partial-faqpage) tutte deployate
- [ ] `cro.css` creato con tutte le regole pattern + responsive + prefers-reduced-motion
- [ ] `enqueue.php` aggiornato
- [ ] Smoke test 21 URL: 200
- [ ] Pattern visibili e funzionali in browser staging (test manuale)
- [ ] Lighthouse no-regression rispetto a Wave 5 baseline
- [ ] Schema FAQPage emesso anche su competenze Tier-2 con FAQ popolate
- [ ] Theme version bumpata a `1.2.0-wave6-geo-cro-blocks`
- [ ] Report `WAVE6-GEO-CRO-REPORT.md` completo
- [ ] Branch `feat/wave6-geo-cro-blocks` pushato, NO merge su main

---

## 🚨 Cosa fare in caso di errore

### ACF Field Groups non visibili in WP-Admin post-extension

```bash
docker-compose exec -T wp wp acf clean
docker-compose exec -T wp wp cache flush
docker-compose exec -T wp wp transient delete --all
```

Verifica file JSON in `acf-json/` aggiornati. Se non si auto-syncano, importa manualmente via WP-Admin → Custom Fields → Tools → Import.

### CSS non applicato

Verifica `enqueue.php` ha aggiunto `cro.css` correttamente + verifica path file. Hard refresh browser (Cmd+Shift+R).

### Mobile sticky bar visibile su pagine sbagliate

Verifica le body class. WP aggiunge automaticamente `single-avvocato` su single-avvocato.php e `page-template-page-contatti` su /contatti/. Se la regola CSS `body:not(...)` non funziona, aggiungi una funzione PHP che gestisce l'esclusione condizionale lato server.

### Template-part errore "fatal: undefined function saltelli_field"

Verifica che `inc/helpers.php` sia incluso in `functions.php` PRIMA dei template-parts. È già così nel MVP, ma ricontrolla.

---

## 🎯 Output expected

1. **Branch pushato**: `origin/feat/wave6-geo-cro-blocks`
2. **Report**: `.claude/knowledge/recovery/WAVE6-GEO-CRO-REPORT.md`
3. **Smoke test artifacts** in `.claude/knowledge/audits/wave6/`
4. **Theme version**: `1.2.0-wave6-geo-cro-blocks`

L'orchestratore audisce + decide il merge. Wave successiva (Wave 4 — Production Readiness) ha già il prompt esistente in `prompts/PROMPT_AGENT_v1.0_WAVE4_PRODUCTION_READINESS.md`.

---

## Riferimenti incrociati

- `CLAUDE.md`
- `pattern-adaptation-map.md` — INPUT PRINCIPALE
- `friction-points-and-cro-patterns-v2.md`
- `mvp-state-snapshot.md`
- `decision-log.md` DEC-018/019/020
- `wave5-ia-refactor.md` — wave precedente
- `prompts/PROMPT_AGENT_v1.0_WAVE4_PRODUCTION_READINESS.md` — wave successiva
