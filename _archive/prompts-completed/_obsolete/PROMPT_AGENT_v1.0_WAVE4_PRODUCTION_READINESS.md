# 🚀 Claude Code Agent — Wave 4 Production Readiness (v1.0, post-Wave5+6)

> **Versione**: 1.0 (ex novo, scritto post-Wave 5 + Wave 6 completate)
> **Audience**: Claude Code agent in sessione terminale dedicata.
> **Branch parent**: `main` (post-Wave 6 mergeata, tag `v1.2.0-wave6-geo-cro-blocks`)
> **Branch nuovo**: `feat/wave4-production-readiness`
> **Theme version target**: `1.3.0-wave4-production-readiness`
> **Scope**: WOFF2 self-host + SRI + Critical CSS inline + JS defer/async + image optimization + Lighthouse ≥ 92 mobile/desktop
> **Tempo stimato**: ~5h (7 phases)
> **Riferimento decisione**: DEC-020 (pipeline 5→6→4→7 — Wave 4 prima di cut produzione Wave 7)

---

## 📚 Letture obbligatorie (nell'ordine)

1. **`CLAUDE.md`** — single source of truth
2. **`prompts/WAVE4_CALIBRATION_NOTES.md`** — 8 calibrazioni preventive specifiche per Wave 4 (LEGGI PRIMA del prompt)
3. **`prompts/WAVE4_RUNBOOK.md`** — istruzioni operative complete
4. Questo prompt (`PROMPT_AGENT_v1.0_WAVE4_PRODUCTION_READINESS.md`)
5. **Riferimenti consultivi**:
   - `.claude/knowledge/recovery/WAVE5-IA-REFACTOR-REPORT.md` (smoke 32 URL audit-aligned)
   - `.claude/knowledge/recovery/WAVE6-GEO-CRO-REPORT.md` (smoke 21 URL + render checks)
   - `wp-content/themes/saltelli/inc/enqueue.php` (CSS bundle attuali post-Wave 6)
   - `wp-content/themes/saltelli/style.css` (theme version current `1.2.0-wave6-geo-cro-blocks`)

---

## 🎯 Tu sei

Claude Code agent dedicato a portare il MVP `saltelli-wp` in stato **production-ready**: Lighthouse ≥ 92 mobile + desktop, font self-hosted, JS ottimizzato, immagini con lazy loading e dimensioni esplicite, SRI per CDN, security headers compatibili.

Wave 4 è **performance + security**. NON tocca features funzionali (sono finite Wave 5 + 6). NON deploya in produzione (è scope Wave 7). Tutto il lavoro vive nel branch fino al merge orchestratore.

---

## 🔒 Hard rules (non negotiabili)

1. **NO commit su main**. Sempre su `feat/wave4-production-readiness`.
2. **NO regression smoke Wave 5**: 32/32 audit-aligned + 18/18 redirect legacy + 33/33 blog redirect chain devono ancora PASS.
3. **NO regression smoke Wave 6**: 21/21 URL + render checks (trust-bar visibile, mobile-bar conditional, FAQPage Tier-2 schema emesso) devono ancora PASS.
4. **NO modifica `wave5-blog-rewrites.php`**. È il filter `request` priority 5 che fa funzionare i 326 blog post historical. CRITICAL.
5. **NO modifica template-parts Wave 6** (trust-bar, mobile-sticky-bar, mini-form, testimonials-block). Solo CSS/font/JS optimization.
6. **NO new JS bundle inutili**. Wave 4 idealmente RIDUCE JS footprint (defer + async), non lo aumenta.
7. **NO Lenis re-enable** (default decisione orchestratore — vedi CAL-W4-06).
8. **NO Iubenda / Brevo / hosting switch** (scope Wave 7, NOT Wave 4).
9. **NO nuovi font** oltre Playfair Display + DM Sans + JetBrains Mono (DS originale locked).
10. **NO breaking change a CSS bundle existing**: tokens.css, components.css, sections.css, components/cro.css restano enqueued, anche se Critical CSS ne inlinea estratti nel <head>.

---

## 📋 PHASE 1 — Backup + branch + Lighthouse baseline (~30 min)

### 1.1 Backup pre-Wave 4

```bash
mkdir -p ~/backups
docker-compose exec -T db mysqldump -u root -proot saltelli > ~/backups/saltelli-pre-wave4-$(date +%Y%m%d-%H%M).sql
tar czf ~/backups/saltelli-pre-wave4-theme-$(date +%Y%m%d-%H%M).tar.gz wp-content/themes/saltelli/
ls -lh ~/backups/saltelli-pre-wave4-*
```

### 1.2 Branch dedicato

```bash
cd ~/Desktop/DEV/saltelli-wp/
git fetch origin
git checkout main
git pull --ff-only origin main
git checkout -b feat/wave4-production-readiness

# Verifica HEAD
git log --oneline -1   # → merge commit Wave 6
```

### 1.3 Lighthouse baseline pre-Wave 4

Eseguire su 6 URL campione rappresentativi (richiede `npx lighthouse` o Chrome DevTools — alternativa: PageSpeed Insights API se Docker locale non ha headless Chrome):

```bash
mkdir -p .claude/knowledge/audits/wave4/lh-baseline-pre/

URLS=(
  "http://localhost:8080/"
  "http://localhost:8080/aree-di-pratica/privati/diritto-tributario/"
  "http://localhost:8080/aree-di-pratica/privati/cartelle-esattoriali-e-multe/"
  "http://localhost:8080/chi-siamo/team/antonia-battista/"
  "http://localhost:8080/contatti/"
  "http://localhost:8080/risorse/blog/"
)

for url in "${URLS[@]}"; do
  slug=$(echo "$url" | sed 's|http://localhost:8080||' | tr '/' '_' | sed 's/_$//' | sed 's/^_//')
  [ -z "$slug" ] && slug="home"
  
  for strategy in mobile desktop; do
    npx lighthouse "$url" \
      --preset=$strategy \
      --output=json --output=html \
      --output-path=".claude/knowledge/audits/wave4/lh-baseline-pre/${slug}-${strategy}" \
      --chrome-flags="--headless --no-sandbox" \
      --quiet 2>&1 | tail -3
  done
done
```

Estrai score baseline:
```bash
for f in .claude/knowledge/audits/wave4/lh-baseline-pre/*.json; do
  perf=$(jq -r '.categories.performance.score' "$f")
  echo "$(basename $f .json): perf=$(printf '%.0f' $(echo "$perf * 100" | bc -l))"
done | tee .claude/knowledge/audits/wave4/lh-baseline-summary.txt
```

**Cristallizza il baseline pre-Wave 4** in `lh-baseline-summary.txt`. Sarà il riferimento per misurare il delta post-Wave 4.

### 1.4 Commit Phase 1

```bash
git add .claude/knowledge/audits/wave4/
git commit -m "wave4: phase 1 — backup + branch setup + Lighthouse baseline pre-Wave 4"
```

---

## 📋 PHASE 2 — WOFF2 self-host (~60 min)

### 2.1 Audit font caricamento attuale

Verifica come i 3 font (Playfair Display, DM Sans, JetBrains Mono) sono caricati attualmente:

```bash
grep -rn "fonts.googleapis\|fonts.gstatic\|@font-face\|fonts.bunny" wp-content/themes/saltelli/ --include="*.php" --include="*.css" | head -20
```

Probabili scenari:
- A: caricati via Google Fonts CDN nel `<head>` (`<link rel="preconnect">`+ `<link rel="stylesheet">`)
- B: già self-hosted via CSS `@font-face` ma caricamento non ottimizzato
- C: misto

### 2.2 Download WOFF2 (Latin + Latin-Ext)

Per ognuno dei 3 font, scarica i WOFF2 ufficiali:
- **Playfair Display** weight 400 + 400 italic (Latin + Latin-Ext)
- **DM Sans** weight 400, 500, 700 (Latin + Latin-Ext)
- **JetBrains Mono** weight 400 (Latin + Latin-Ext)

Fonti raccomandate: google-webfonts-helper (gwfh.mranftl.com) per WOFF2 self-host pronti.

Salva in:
```
wp-content/themes/saltelli/assets/fonts/
├── playfair-display/
│   ├── playfair-display-v30-latin-400.woff2
│   ├── playfair-display-v30-latin-400italic.woff2
│   ├── playfair-display-v30-latin-ext-400.woff2
│   └── playfair-display-v30-latin-ext-400italic.woff2
├── dm-sans/
│   ├── dm-sans-v15-latin-400.woff2
│   ├── dm-sans-v15-latin-500.woff2
│   ├── dm-sans-v15-latin-700.woff2
│   └── dm-sans-v15-latin-ext-{400,500,700}.woff2
└── jetbrains-mono/
    ├── jetbrains-mono-v18-latin-400.woff2
    └── jetbrains-mono-v18-latin-ext-400.woff2
```

### 2.3 Crea `assets/css/fonts.css` con `@font-face`

```css
/* wp-content/themes/saltelli/assets/css/fonts.css */
/* Self-hosted fonts — Wave 4 Production Readiness
   font-display: swap → graceful fallback durante load (no FOIT)
   unicode-range → ottimizza scaricamento (browser scarica solo file rilevante per il content)
*/

/* Playfair Display — display + italic */
@font-face {
  font-family: 'Playfair Display';
  font-style: normal;
  font-weight: 400;
  font-display: swap;
  src: url('../fonts/playfair-display/playfair-display-v30-latin-400.woff2') format('woff2');
  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
}
@font-face {
  font-family: 'Playfair Display';
  font-style: italic;
  font-weight: 400;
  font-display: swap;
  src: url('../fonts/playfair-display/playfair-display-v30-latin-400italic.woff2') format('woff2');
  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
}
/* Latin-Ext per accenti italiani (è, à, ò, etc.) */
@font-face {
  font-family: 'Playfair Display';
  font-style: normal;
  font-weight: 400;
  font-display: swap;
  src: url('../fonts/playfair-display/playfair-display-v30-latin-ext-400.woff2') format('woff2');
  unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
}

/* DM Sans 400 / 500 / 700 — body text */
@font-face {
  font-family: 'DM Sans';
  font-style: normal;
  font-weight: 400;
  font-display: swap;
  src: url('../fonts/dm-sans/dm-sans-v15-latin-400.woff2') format('woff2');
  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
}
@font-face {
  font-family: 'DM Sans';
  font-style: normal;
  font-weight: 500;
  font-display: swap;
  src: url('../fonts/dm-sans/dm-sans-v15-latin-500.woff2') format('woff2');
  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
}
@font-face {
  font-family: 'DM Sans';
  font-style: normal;
  font-weight: 700;
  font-display: swap;
  src: url('../fonts/dm-sans/dm-sans-v15-latin-700.woff2') format('woff2');
  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
}
/* DM Sans Latin-Ext per ognuno dei 3 weights — analogamente */

/* JetBrains Mono — caption/eyebrow */
@font-face {
  font-family: 'JetBrains Mono';
  font-style: normal;
  font-weight: 400;
  font-display: swap;
  src: url('../fonts/jetbrains-mono/jetbrains-mono-v18-latin-400.woff2') format('woff2');
  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
}
```

### 2.4 Aggiorna `inc/enqueue.php` per caricare `fonts.css` PRIMA di tokens.css

`fonts.css` deve essere caricato **per primo** (handle dipendenza più alta). Inseriscilo in cima al chain:

```php
// In wp_enqueue_scripts callback, all'inizio:
wp_enqueue_style(
    'saltelli-fonts',
    SALTELLI_THEME_URI . '/assets/css/fonts.css',
    [],  // no dependencies
    $ver
);

// E modifica tokens.css per dipendere da fonts:
wp_enqueue_style(
    'saltelli-tokens',
    SALTELLI_THEME_URI . '/assets/css/tokens.css',
    ['saltelli-fonts'],  // ← aggiungi dependency
    $ver
);
```

### 2.5 Rimuovi caricamento Google Fonts CDN (se presente)

Se trovi `<link rel="preconnect" href="https://fonts.googleapis.com">` o simili in `header.php` o `inc/enqueue.php`, **commentali** (non rimuovere — Wave 4 lascia commento per traceability):

```php
// Wave 4 — Google Fonts CDN rimosso, font self-hosted via fonts.css
// wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?...', [], null);
```

### 2.6 `<link rel="preload">` per font critical (Playfair 400 + DM Sans 400 + 700)

In `header.php`, aggiungi prima di `wp_head()`:

```php
<?php if (!is_admin()) : ?>
<link rel="preload" href="<?php echo SALTELLI_THEME_URI; ?>/assets/fonts/dm-sans/dm-sans-v15-latin-400.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="<?php echo SALTELLI_THEME_URI; ?>/assets/fonts/dm-sans/dm-sans-v15-latin-700.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="<?php echo SALTELLI_THEME_URI; ?>/assets/fonts/playfair-display/playfair-display-v30-latin-400.woff2" as="font" type="font/woff2" crossorigin>
<?php endif; ?>
```

JetBrains Mono NON è critical (caption/eyebrow only) → no preload, viene caricato lazy.

### 2.7 Smoke test font caricamento

```bash
# Verifica nessun request a fonts.googleapis.com
curl -s "http://localhost:8080/" | grep -i "fonts.googleapis\|fonts.gstatic" || echo "✓ NO Google Fonts CDN refs"

# Verifica preload presenti
curl -s "http://localhost:8080/" | grep "rel=\"preload\".*font/woff2"

# Verifica fonts.css enqueued
curl -s "http://localhost:8080/" | grep "fonts.css"
```

### 2.8 Commit Phase 2

```bash
git add wp-content/themes/saltelli/assets/fonts/
git add wp-content/themes/saltelli/assets/css/fonts.css
git add wp-content/themes/saltelli/inc/enqueue.php
git add wp-content/themes/saltelli/header.php
git commit -m "wave4: phase 2 — WOFF2 self-host (Playfair + DM Sans + JetBrains Mono)

- Aggiunto assets/fonts/ con WOFF2 Latin + Latin-Ext per 3 font families
- Aggiunto assets/css/fonts.css con @font-face + font-display: swap + unicode-range
- Aggiornato enqueue.php: saltelli-fonts come prima dipendenza
- Rimosso Google Fonts CDN (commentato per traceability)
- Aggiunto <link rel='preload'> per 3 font critical (Playfair 400 + DM Sans 400/700)

Acceptance: NO request a fonts.googleapis.com, fonts.css presente, preload attivi."
```

---

## 📋 PHASE 3 — Critical CSS extraction + inline (~90 min)

### 3.1 Identificare URL templates principali

5 template-pattern principali in MVP post-Wave 6:
1. **front-page** (`/`) — homepage
2. **single-competenza Tier-1** (es. `/aree-di-pratica/privati/diritto-tributario/`)
3. **single-competenza Tier-2** (es. `/aree-di-pratica/privati/cartelle-esattoriali-e-multe/`)
4. **single-avvocato** (es. `/chi-siamo/team/antonia-battista/`)
5. **page generic** (es. `/contatti/`)

### 3.2 Extract critical CSS via critters / penthouse

Strategia consigliata: **critters** (più moderno, npm package) o **penthouse** (alternativa).

```bash
npm install --save-dev critters
# oppure
npm install --save-dev penthouse

# Esegui per ogni template-pattern (esempio con penthouse):
mkdir -p wp-content/themes/saltelli/assets/css/critical/

for url in / /aree-di-pratica/privati/diritto-tributario/ /aree-di-pratica/privati/cartelle-esattoriali-e-multe/ /chi-siamo/team/antonia-battista/ /contatti/; do
  out=$(echo "$url" | tr '/' '_' | sed 's/^_//;s/_$//')
  [ -z "$out" ] && out="home"
  
  npx penthouse \
    --url="http://localhost:8080${url}" \
    --width=375 --height=812 \
    --output="wp-content/themes/saltelli/assets/css/critical/${out}-mobile.css"
  
  npx penthouse \
    --url="http://localhost:8080${url}" \
    --width=1440 --height=900 \
    --output="wp-content/themes/saltelli/assets/css/critical/${out}-desktop.css"
done
```

**Target dimensione critical CSS**: < 14KB inline per pattern (rispetta `<14KB initial congestion window` HTTP/2).

### 3.3 Helper PHP per inline critical CSS in `<head>`

In `inc/critical-css.php` (NEW):

```php
<?php
/**
 * Wave 4 — Critical CSS inliner.
 *
 * Inietta inline il CSS critical extraido per il template corrente,
 * così che il first paint avvenga senza render-blocking del CSS principale.
 *
 * @package Saltelli
 * @since 1.3.0 Wave 4
 */
defined('ABSPATH') || exit;

if (!function_exists('saltelli_inline_critical_css')) :
function saltelli_inline_critical_css() {
    // Determine template pattern
    if (is_front_page()) {
        $pattern = 'home';
    } elseif (is_singular('competenza')) {
        // Tier-1 vs Tier-2 — usa lo stesso critical (struttura simile)
        $pattern = 'aree-di-pratica_privati_diritto-tributario';
    } elseif (is_singular('avvocato')) {
        $pattern = 'chi-siamo_team_antonia-battista';
    } elseif (is_page('contatti')) {
        $pattern = 'contatti';
    } else {
        // No critical CSS for less-common templates → fallback CSS regular
        return;
    }
    
    // Determine viewport (mobile vs desktop) via user-agent — semplice heuristic
    $is_mobile = wp_is_mobile();
    $suffix = $is_mobile ? 'mobile' : 'desktop';
    
    $critical_path = SALTELLI_THEME_DIR . "/assets/css/critical/{$pattern}-{$suffix}.css";
    
    if (!file_exists($critical_path)) {
        return;  // Graceful fallback: load CSS regular
    }
    
    $critical_css = file_get_contents($critical_path);
    if (empty($critical_css)) return;
    
    echo '<style id="saltelli-critical-css">' . $critical_css . '</style>' . "\n";
}
endif;

// Hook a wp_head priority 1 (prima di wp_enqueue_scripts callback)
add_action('wp_head', 'saltelli_inline_critical_css', 1);

// Async load del CSS principale tramite media swap trick
if (!function_exists('saltelli_async_main_css')) :
function saltelli_async_main_css($html, $handle) {
    // Apply async only ai 4 main bundle (NO fonts, NO admin)
    $async_handles = ['saltelli-tokens', 'saltelli-components', 'saltelli-sections', 'saltelli-cro'];
    if (!in_array($handle, $async_handles, true)) return $html;
    
    return str_replace(
        ' rel="stylesheet"',
        ' rel="preload" as="style" onload="this.onload=null;this.rel=\'stylesheet\'"',
        $html
    ) . '<noscript>' . str_replace(
        ' onload="this.onload=null;this.rel=\'stylesheet\'"',
        '',
        str_replace(' rel="preload" as="style"', ' rel="stylesheet"', $html)
    ) . '</noscript>';
}
endif;
add_filter('style_loader_tag', 'saltelli_async_main_css', 10, 2);
```

### 3.4 Include il file in `functions.php`

```php
// Add post Wave 6 includes:
require_once SALTELLI_THEME_DIR . '/inc/critical-css.php';
```

### 3.5 Smoke test

```bash
# Critical CSS inlined in head?
curl -s "http://localhost:8080/" | grep -c "saltelli-critical-css"   # → 1

# Main CSS async (preload + onload swap)?
curl -s "http://localhost:8080/" | grep "rel=\"preload\" as=\"style\""   # → multiple matches

# Page hub vs single-competenza differiscono?
curl -s "http://localhost:8080/aree-di-pratica/privati/diritto-tributario/" | grep -A 3 "saltelli-critical-css" | head -5
```

### 3.6 Commit Phase 3

```bash
git add wp-content/themes/saltelli/assets/css/critical/
git add wp-content/themes/saltelli/inc/critical-css.php
git add wp-content/themes/saltelli/functions.php
git commit -m "wave4: phase 3 — Critical CSS extraction + inline + async main CSS

- 10 file critical CSS (5 pattern × 2 viewport mobile/desktop) in assets/css/critical/
- Helper saltelli_inline_critical_css() inietta <style> in <head> priority 1
- Filter style_loader_tag converte main CSS bundle in async (preload + onload swap + noscript fallback)
- Pattern detection: front-page, single-competenza, single-avvocato, page contatti
- Graceful fallback: se critical file non esiste → CSS regular load

Target: first paint senza render-blocking del CSS main (~14KB inline per pattern)."
```

---

## 📋 PHASE 4 — JS optimization (defer + async + cleanup) (~30 min)

### 4.1 Audit JS attuali

```bash
grep -rn "wp_enqueue_script\|wp_register_script" wp-content/themes/saltelli/inc/ | head -20
```

Probabili JS in MVP:
- GSAP 3.12.5 (animations)
- Lenis 1.1.13 (DISABLED — DEC-018)
- Eventuali handler inline in template-parts

### 4.2 Filter `script_loader_tag` per defer/async

In `inc/critical-css.php` (continuazione), aggiungi:

```php
// Wave 4 — JS defer/async optimization
if (!function_exists('saltelli_optimize_scripts')) :
function saltelli_optimize_scripts($tag, $handle) {
    // Skip admin
    if (is_admin()) return $tag;
    
    // Defer: GSAP + theme custom JS (non-critical, non-render-blocking)
    $defer_handles = ['gsap', 'saltelli-main', 'saltelli-animations'];
    if (in_array($handle, $defer_handles, true)) {
        return str_replace(' src=', ' defer src=', $tag);
    }
    
    // Async: analytics, third-party (se mai presenti)
    $async_handles = ['google-analytics', 'gtm'];
    if (in_array($handle, $async_handles, true)) {
        return str_replace(' src=', ' async src=', $tag);
    }
    
    return $tag;
}
endif;
add_filter('script_loader_tag', 'saltelli_optimize_scripts', 10, 2);
```

### 4.3 Cleanup script inutili (jQuery se non usato)

Verifica se jQuery è usato dal tema:
```bash
grep -rn "jQuery\|\\\$(" wp-content/themes/saltelli/ --include="*.php" --include="*.js" | head -10
```

Se NON usato (probabile per tema vanilla PHP), aggiungi a `inc/enqueue.php`:

```php
// Wave 4 — Cleanup: rimuovi script default non usati
if (!is_admin()) {
    add_action('wp_enqueue_scripts', function () {
        wp_dequeue_script('jquery-migrate');
        // wp_deregister_script('jquery'); // Solo se davvero NON serve a NESSUN plugin
    }, 100);
    
    // Rimuovi emoji script (~14KB inutili)
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
}
```

### 4.4 Commit Phase 4

```bash
git add wp-content/themes/saltelli/inc/critical-css.php
git add wp-content/themes/saltelli/inc/enqueue.php
git commit -m "wave4: phase 4 — JS optimization (defer + async + emoji removal)"
```

---

## 📋 PHASE 5 — Image optimization (lazy + width/height + preload hero) (~45 min)

### 5.1 Verifica `loading="lazy"` su immagini below-the-fold

WordPress auto-aggiunge `loading="lazy"` dal 5.5+. Verifica funzioni correttamente:

```bash
curl -s "http://localhost:8080/" | grep -c 'loading="lazy"'   # → multiple matches attesi
```

Se mancano in alcuni template (es. template-parts che usano `<img>` hardcoded), aggiungi manualmente:

```php
<img src="..." alt="..." loading="lazy" width="600" height="400">
```

### 5.2 `<link rel="preload">` per hero image

Identifica l'hero image della homepage. In `header.php` o `front-page.php`, aggiungi:

```php
<?php if (is_front_page()) : ?>
<link rel="preload" as="image" href="<?php echo SALTELLI_THEME_URI; ?>/assets/img/hero-saltelli.jpg" imagesrcset="..." imagesizes="100vw">
<?php endif; ?>
```

### 5.3 Width/height esplicite su tutti i tag `<img>` nei template

Audit:
```bash
grep -rn "<img" wp-content/themes/saltelli/ --include="*.php" | grep -v 'width=' | grep -v 'height=' | head -10
```

Per ogni `<img>` senza dimensioni, aggiungi width + height (anche valori CSS-grandezza, basta che il browser pre-allochi spazio). Riduce CLS (Cumulative Layout Shift).

### 5.4 Commit Phase 5

```bash
git add -A
git commit -m "wave4: phase 5 — Image optimization (lazy + preload hero + width/height)"
```

---

## 📋 PHASE 6 — Security headers + SRI (~30 min)

### 6.1 Security headers via `inc/security.php` (NEW)

```php
<?php
/**
 * Wave 4 — Security headers (delegabili a Cloudflare/nginx in produzione).
 *
 * @package Saltelli
 * @since 1.3.0 Wave 4
 */
defined('ABSPATH') || exit;

add_action('send_headers', function () {
    if (is_admin()) return;
    
    // X-Frame-Options: DENY (clickjacking protection)
    header('X-Frame-Options: DENY');
    
    // X-Content-Type-Options: nosniff
    header('X-Content-Type-Options: nosniff');
    
    // Referrer-Policy: strict-origin-when-cross-origin
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
    // Permissions-Policy: limita feature sensibili
    header('Permissions-Policy: geolocation=(), microphone=(), camera=(), payment=()');
    
    // Strict-Transport-Security: 1 anno (con preload-ready) — solo se HTTPS
    if (!empty($_SERVER['HTTPS'])) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    }
});

// Disabilita XML-RPC (non usato + vettore attacco)
add_filter('xmlrpc_enabled', '__return_false');

// Rimuovi version WordPress da head e RSS (security through obscurity, ma utile)
remove_action('wp_head', 'wp_generator');
add_filter('the_generator', '__return_empty_string');
```

Include in `functions.php`:
```php
require_once SALTELLI_THEME_DIR . '/inc/security.php';
```

### 6.2 SRI per script CDN (se presenti)

Se in qualche file viene caricato uno script da CDN esterno (es. GSAP da cdnjs), aggiungi `integrity` + `crossorigin`:

```html
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"
        integrity="sha384-EXAMPLE-CHECKSUM"
        crossorigin="anonymous"
        defer></script>
```

Nota: probabilmente il tema usa GSAP self-hosted (più sicuro, no SRI necessario). Verifica e procedi di conseguenza.

### 6.3 Commit Phase 6

```bash
git add wp-content/themes/saltelli/inc/security.php
git add wp-content/themes/saltelli/functions.php
git commit -m "wave4: phase 6 — Security headers + SRI + XML-RPC disabled"
```

---

## 📋 PHASE 7 — Smoke + Lighthouse post + bump version + report (~30 min)

### 7.1 NO regression smoke (CRITICAL)

```bash
# Smoke Wave 5 — 32 audit-aligned + 18 redirect legacy + 33 blog redirect chain
# (riusa script di .claude/knowledge/audits/wave5-ia-refactor/cli-output/)
# Atteso: stesso risultato di Wave 5 (no regression)

# Smoke Wave 6 — 21 URL audit-aligned + render checks
# (riusa script di .claude/knowledge/audits/wave6/)
# Atteso: stesso risultato di Wave 6
```

Se UN qualunque smoke regression: **STOP**, debug isolato, eventualmente rollback fix mirato.

### 7.2 Lighthouse post-Wave 4

Stesso script di Phase 1.3 ma output in `.claude/knowledge/audits/wave4/lh-post/`.

```bash
mkdir -p .claude/knowledge/audits/wave4/lh-post/

# (Stesso loop di Phase 1.3 ma --output-path differente)

# Confronto delta
echo "=== Lighthouse delta Wave 4 ===" > .claude/knowledge/audits/wave4/lh-delta.txt
for f in .claude/knowledge/audits/wave4/lh-baseline-pre/*.json; do
    fname=$(basename "$f" .json)
    pre=$(jq -r '.categories.performance.score' "$f" 2>/dev/null)
    post=$(jq -r '.categories.performance.score' ".claude/knowledge/audits/wave4/lh-post/${fname}.json" 2>/dev/null)
    pre_pct=$(printf '%.0f' $(echo "$pre * 100" | bc -l))
    post_pct=$(printf '%.0f' $(echo "$post * 100" | bc -l))
    delta=$((post_pct - pre_pct))
    echo "${fname}: ${pre_pct} → ${post_pct} (Δ${delta})" >> .claude/knowledge/audits/wave4/lh-delta.txt
done
cat .claude/knowledge/audits/wave4/lh-delta.txt
```

**Acceptance**: ≥ 92 mobile + desktop su 6 URL campione.

### 7.3 Bump theme version

In `wp-content/themes/saltelli/style.css`:
```
Version: 1.3.0-wave4-production-readiness
```

In `wp-content/themes/saltelli/functions.php`:
```php
define('SALTELLI_VERSION', '1.3.0-wave4-production-readiness');
```

### 7.4 Report finale

Crea `.claude/knowledge/recovery/WAVE4-PRODUCTION-READINESS-REPORT.md` con:
- Phase summary 1-7
- Lighthouse delta tabella
- Smoke regression check (Wave 5 + 6)
- File modificati / creati riepilogo
- Open items (mobile device test, etc.)

### 7.5 Commit + push

```bash
git add -A
git commit -m "wave4: phase 7 — bump 1.3.0-wave4-production-readiness + Lighthouse post + report

Lighthouse delta (mobile + desktop):
- /                              : XX → YY (Δ+ZZ)
- /aree-di-pratica/.../tributario: XX → YY (Δ+ZZ)
- ... (vedi lh-delta.txt)

NO regression smoke Wave 5 (32+18+33 PASS).
NO regression smoke Wave 6 (21 PASS + render checks).

Closes Wave 4 Production Readiness."

git push origin feat/wave4-production-readiness
```

---

## ✅ Acceptance criteria Wave 4 (orchestrator audit checklist)

- [ ] Branch `feat/wave4-production-readiness` da `main` post-Wave 6
- [ ] 7 phases eseguite, 7+ commit phase-by-phase
- [ ] WOFF2 self-host: 3 font families (Playfair + DM Sans + JetBrains Mono), Latin + Latin-Ext
- [ ] `<link rel="preload">` per 3 font critical (Playfair 400 + DM Sans 400/700)
- [ ] NO request a `fonts.googleapis.com` o `fonts.gstatic.com`
- [ ] Critical CSS extraction per 5 template-pattern × 2 viewport (mobile/desktop) = 10 file
- [ ] `<style id="saltelli-critical-css">` inline in `<head>` per template principali
- [ ] Main CSS bundle async via `rel="preload" + onload="this.rel='stylesheet'"`
- [ ] JS defer per `gsap`, `saltelli-main`
- [ ] Emoji + jquery-migrate dequeued
- [ ] XML-RPC disabled
- [ ] Security headers: X-Frame-Options, X-Content-Type-Options, Referrer-Policy, Permissions-Policy, HSTS (HTTPS only)
- [ ] `loading="lazy"` su immagini below-the-fold (verifica WP auto + manual fix template parts)
- [ ] `width` + `height` esplicite su tutti i tag `<img>` (riduce CLS)
- [ ] `<link rel="preload" as="image">` per hero homepage
- [ ] **Lighthouse ≥ 92 mobile + desktop** su 6 URL campione
- [ ] **NO regression smoke Wave 5**: 32/32 audit-aligned + 18/18 redirect + 33/33 blog
- [ ] **NO regression smoke Wave 6**: 21/21 URL + render checks (trust-bar/mobile-bar/FAQPage)
- [ ] Theme version bumpata `1.3.0-wave4-production-readiness`
- [ ] Branch pushed (NO merge automatico)
- [ ] Report `.claude/knowledge/recovery/WAVE4-PRODUCTION-READINESS-REPORT.md`

---

## 🚨 Cosa fare in caso di errore

| Situazione | Action |
|---|---|
| Lighthouse mobile < 92 dopo Phase 7 | Verifica delta vs baseline, identifica top contributors a basso score (TBT, LCP, CLS), fix mirato Phase 6.5 (ripristina commit + intervento puntuale). NON forzare > 92 con hack hidden. |
| Regression smoke Wave 5 o Wave 6 | STOP immediato, identifica commit causa, revert mirato. NON proseguire fino a green. |
| Critical CSS rompe layout (FOUC visibile) | Estendi extraction (penthouse `--width=...`), oppure aumenta size inline. Se persiste: rollback Phase 3 + valuta strategia diversa (es. inline solo above-the-fold critical, no async). |
| Font self-host non carica (404) | Verifica path `assets/fonts/`, permessi file, MIME type WOFF2 nel webserver Docker. Test diretto curl URL del font. |
| SRI hash non match | Rimuovi SRI se script self-hosted (NON serve), o ricalcola hash con `cat script.js \| openssl dgst -sha384 -binary \| base64` |

---

## 🎯 Output expected

1. Branch `feat/wave4-production-readiness` con 7+ commit phase-by-phase
2. File creati / modificati totali:
   - **NEW**: `assets/fonts/` (3 famiglie WOFF2, ~24 file), `assets/css/fonts.css`, `assets/css/critical/` (10 file), `inc/critical-css.php`, `inc/security.php`
   - **MOD**: `inc/enqueue.php`, `header.php`, `functions.php`, `style.css` (version bump)
3. Audit trail completo in `.claude/knowledge/audits/wave4/`:
   - `lh-baseline-pre/` (12 file: 6 URL × 2 viewport)
   - `lh-post/` (12 file)
   - `lh-baseline-summary.txt`, `lh-delta.txt`
4. Report `.claude/knowledge/recovery/WAVE4-PRODUCTION-READINESS-REPORT.md`
5. Theme version `1.3.0-wave4-production-readiness`

L'orchestratore (chat) audisce il branch + se OK ti dice di procedere con merge `feat/wave4-production-readiness → main` (no-ff) + tag `v1.3.0-wave4-production-readiness`.

Una volta mergeato, l'orchestratore popola DEC-026 finale + aggiorna `mvp-state-snapshot.md` v4 + prepara Wave 7 (Cut produzione).

---

## 🔗 Riferimenti incrociati

- `prompts/WAVE4_CALIBRATION_NOTES.md` — 8 calibrazioni preventive specifiche
- `prompts/WAVE4_RUNBOOK.md` — istruzioni operative per Duccio
- `mvp-state-snapshot.md` v3 — stato MVP post-Wave 6
- `WAVE5-IA-REFACTOR-REPORT.md`, `WAVE6-GEO-CRO-REPORT.md` — riferimenti smoke baseline
- `CLAUDE.md` — single source of truth
- DEC-018 (drift accettato), DEC-020 (pipeline 5→6→4→7), DEC-024 (Wave 5), DEC-025-COMPLETED (Wave 6)
