# 🚀 Claude Code Agent — Wave 4.5 Critical CSS per-template + WebP/AVIF (v1.0)

> **Audience**: Claude Code agent in nuova sessione terminale.
> **Branch parent**: `main` (post-Wave 4 mergeata, tag `v1.3.0-wave4-production-readiness`)
> **Branch nuovo**: `feat/wave4-5-critical-css-webp`
> **Theme version target**: `1.3.1-wave4-5-critical-css-webp`
> **Scope**: per-template Critical CSS extraction + WebP/AVIF conversion blog thumb (chiudere gap Lighthouse residuo Wave 4)
> **Tempo stimato**: ~3-4h (5 phases)
> **Riferimento**: DEC-026-COMPLETED Wave 4 caveats + open items 4.5

---

## 🎯 Tu sei

Claude Code agent dedicato a chiudere il gap Lighthouse residuo Wave 4: 5/12 score sotto 92 su single-run measurement (mobile Tier1 89, Tier2 90, Avvocato 90, Blog-archive 86; desktop Blog-archive 88).

Wave 4.5 è scope **piccolo e focalizzato**: solo 2 ottimizzazioni mirate. NON aggiungere altro scope.

**Target**: portare mobile da 86-90 verso 92-95 con per-template critical CSS extraction + WebP blog thumbs. Desktop blog-archive verso 92.

---

## 📚 Letture obbligatorie (nell'ordine)

1. **`CLAUDE.md`** — single source of truth
2. **`prompts/PROMPT_AGENT_v1.0_WAVE4_5_CRITICAL_CSS_WEBP.md`** (questo file)
3. **`.claude/knowledge/recovery/WAVE4-PRODUCTION-READINESS-REPORT.md`** — gap Lighthouse residuo + lessons learned Wave 4
4. **`wp-content/themes/saltelli/inc/critical-css.php`** — pattern critical CSS attuale (sections-only deferral, già funzionante)
5. **`wp-content/themes/saltelli/inc/perf.php`** — JS optimization Wave 4

---

## 🔒 Hard rules (non negotiabili)

1. **NO commit su main**. Sempre su `feat/wave4-5-critical-css-webp`.
2. **NO regression smoke Wave 5**: 33/33 audit-aligned + 18/18 redirect legacy + 33/33 blog redirect chain ancora PASS.
3. **NO regression smoke Wave 6**: 21/21 URL + render checks devono PASS.
4. **NO modifica `wave5-blog-rewrites.php`**, `inc/perf.php`, `inc/security.php`. Sono Wave 4 stable.
5. **NO scope creep**: SOLO per-template critical CSS + WebP blog thumb. NO altro.
6. **Critical CSS deferral incrementale + viewport-tested** (lesson learned Wave 4 #1). NO bulk extension.
7. **Trade-off `font-display: optional`** mantenuto da Wave 4. NON cambiare.

---

## 📋 PHASE 1 — Backup + branch + baseline (~20 min)

### 1.1 Backup pre-Wave 4.5

```bash
mkdir -p ~/backups
docker-compose exec -T db mysqldump -u root -proot saltelli > ~/backups/saltelli-pre-wave45-$(date +%Y%m%d-%H%M).sql
tar czf ~/backups/saltelli-pre-wave45-theme-$(date +%Y%m%d-%H%M).tar.gz wp-content/themes/saltelli/
```

### 1.2 Branch dedicato

```bash
cd ~/Desktop/DEV/saltelli-wp/
git fetch origin
git checkout main
git pull --ff-only origin main   # → tag v1.3.0-wave4-production-readiness
git checkout -b feat/wave4-5-critical-css-webp
```

### 1.3 Lighthouse baseline pre-Wave 4.5 (ri-uso script Wave 4)

```bash
mkdir -p .claude/knowledge/audits/wave4-5/lh-baseline-pre/

# Riusa scripts/wave4/lh-run.sh con output path differente
bash scripts/wave4/lh-run.sh .claude/knowledge/audits/wave4-5/lh-baseline-pre/

# Estrai score baseline
for f in .claude/knowledge/audits/wave4-5/lh-baseline-pre/*.json; do
  perf=$(jq -r '.categories.performance.score' "$f")
  echo "$(basename $f .json): perf=$(printf '%.0f' $(echo "$perf * 100" | bc -l))"
done | tee .claude/knowledge/audits/wave4-5/lh-baseline-summary.txt
```

### 1.4 Commit Phase 1

```bash
git add .claude/knowledge/audits/wave4-5/
git commit -m "wave4-5: phase 1 — backup + branch + Lighthouse baseline pre-Wave 4.5"
```

---

## 📋 PHASE 2 — Per-template critical CSS extraction (~120 min)

### 2.1 Setup penthouse

```bash
npm install --save-dev penthouse puppeteer-core
```

### 2.2 Identificare template-pattern

5 pattern principali (riusa elenco da Wave 4 prompt):
1. **front-page** (`/`)
2. **single-competenza Tier-1** (`/aree-di-pratica/privati/diritto-tributario/`)
3. **single-competenza Tier-2** (`/aree-di-pratica/privati/cartelle-esattoriali-e-multe/`)
4. **single-avvocato** (`/chi-siamo/team/antonia-battista/`)
5. **page generic** (`/contatti/`)

### 2.3 Extraction script

Crea `scripts/wave4-5/extract-critical.sh`:

```bash
#!/usr/bin/env bash
set -e

OUT_DIR="wp-content/themes/saltelli/assets/css/critical"
mkdir -p "$OUT_DIR"

declare -A URLS=(
  ["home"]="http://localhost:8080/"
  ["competenza-tier1"]="http://localhost:8080/aree-di-pratica/privati/diritto-tributario/"
  ["competenza-tier2"]="http://localhost:8080/aree-di-pratica/privati/cartelle-esattoriali-e-multe/"
  ["single-avvocato"]="http://localhost:8080/chi-siamo/team/antonia-battista/"
  ["page-generic"]="http://localhost:8080/contatti/"
)

for pattern in "${!URLS[@]}"; do
  url="${URLS[$pattern]}"
  
  # Mobile (375x812)
  npx penthouse \
    --url="$url" \
    --width=375 --height=812 \
    --timeout=120000 \
    --renderWaitTime=2000 \
    --keepLargerMediaQueries=false \
    --output="${OUT_DIR}/${pattern}-mobile.css"
  
  # Desktop (1440x900)
  npx penthouse \
    --url="$url" \
    --width=1440 --height=900 \
    --timeout=120000 \
    --renderWaitTime=2000 \
    --keepLargerMediaQueries=false \
    --output="${OUT_DIR}/${pattern}-desktop.css"
  
  # Verify size < 14KB (HTTP/2 initial congestion window)
  for vp in mobile desktop; do
    size=$(stat -f%z "${OUT_DIR}/${pattern}-${vp}.css" 2>/dev/null || stat -c%s "${OUT_DIR}/${pattern}-${vp}.css")
    if [ $size -gt 14336 ]; then
      echo "⚠️  ${pattern}-${vp}.css = ${size}B (>14KB target)"
    else
      echo "✅ ${pattern}-${vp}.css = ${size}B"
    fi
  done
done
```

Esegui:
```bash
chmod +x scripts/wave4-5/extract-critical.sh
./scripts/wave4-5/extract-critical.sh 2>&1 | tee .claude/knowledge/audits/wave4-5/critical-extraction.log
```

**Target dimensione**: ogni file critical < 14KB (HTTP/2 initial congestion window). Se più grande, è OK ma avviso loggato.

### 2.4 Aggiorna `inc/critical-css.php` per per-template detection

Sostituisci la logica attuale (sections-only deferral) con per-template critical CSS injection:

```php
<?php
/**
 * Wave 4.5 — Per-template critical CSS injection.
 *
 * Inietta inline il CSS critical extracto per il template corrente,
 * così che first paint avvenga senza render-blocking del CSS principale.
 *
 * @package Saltelli
 * @since 1.3.1 Wave 4.5
 */
defined('ABSPATH') || exit;

if (!function_exists('saltelli_detect_template_pattern')) :
function saltelli_detect_template_pattern() {
    if (is_front_page()) return 'home';
    if (is_singular('competenza')) {
        // Tier-1 deep slugs (DEC-021 cliente firmati)
        $tier1_slugs = ['diritto-tributario', 'diritto-del-lavoro', 'diritto-di-famiglia-lgbtq'];
        return in_array(get_post_field('post_name', get_the_ID()), $tier1_slugs, true)
            ? 'competenza-tier1' : 'competenza-tier2';
    }
    if (is_singular('avvocato')) return 'single-avvocato';
    if (is_page() || is_single() || is_archive() || is_404()) return 'page-generic';
    return null;
}
endif;

if (!function_exists('saltelli_inline_critical_css')) :
function saltelli_inline_critical_css() {
    $pattern = saltelli_detect_template_pattern();
    if (!$pattern) return;  // Graceful: no critical, full CSS load
    
    $is_mobile = wp_is_mobile();
    $suffix = $is_mobile ? 'mobile' : 'desktop';
    
    $critical_path = SALTELLI_THEME_DIR . "/assets/css/critical/{$pattern}-{$suffix}.css";
    if (!file_exists($critical_path)) return;
    
    $critical_css = file_get_contents($critical_path);
    if (empty($critical_css)) return;
    
    echo '<style id="saltelli-critical-css" data-pattern="' . esc_attr($pattern) . '-' . $suffix . '">' . $critical_css . '</style>' . "\n";
}
endif;
add_action('wp_head', 'saltelli_inline_critical_css', 1);

// Async load del CSS principale tramite preload + onload swap
if (!function_exists('saltelli_async_main_css')) :
function saltelli_async_main_css($html, $handle) {
    if (is_admin()) return $html;
    
    // Async ai 4 main bundle (NO fonts, NO sections — sections aveva pattern legacy Wave 4)
    $async_handles = ['saltelli-tokens', 'saltelli-base', 'saltelli-components', 'saltelli-cro'];
    if (!in_array($handle, $async_handles, true)) return $html;
    
    return str_replace(
        " rel='stylesheet'",
        " rel='preload' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"",
        $html
    ) . "\n<noscript>" . str_replace(
        " onload=\"this.onload=null;this.rel='stylesheet'\"",
        '',
        str_replace(" rel='preload' as='style'", " rel='stylesheet'", $html)
    ) . "</noscript>";
}
endif;
add_filter('style_loader_tag', 'saltelli_async_main_css', 10, 2);
```

### 2.5 Test Phase 2

```bash
# Critical CSS inlined?
curl -s "http://localhost:8080/" | grep -c 'id="saltelli-critical-css"'   # → 1

# Pattern detection corretto?
curl -s "http://localhost:8080/aree-di-pratica/privati/diritto-tributario/" | grep 'data-pattern='
# → data-pattern="competenza-tier1-desktop" (o mobile se UA mobile)

curl -s "http://localhost:8080/aree-di-pratica/privati/cartelle-esattoriali-e-multe/" | grep 'data-pattern='
# → data-pattern="competenza-tier2-desktop"

# Async main CSS attivo?
curl -s "http://localhost:8080/" | grep -c "rel='preload' as='style'"   # → 4
```

### 2.6 Commit Phase 2

```bash
git add wp-content/themes/saltelli/assets/css/critical/
git add wp-content/themes/saltelli/inc/critical-css.php
git add scripts/wave4-5/
git add .claude/knowledge/audits/wave4-5/critical-extraction.log
git commit -m "wave4-5: phase 2 — per-template critical CSS extraction (5 pattern × 2 viewport = 10 file)

- extract-critical.sh script con penthouse + 14KB target check
- inc/critical-css.php aggiornato: detect pattern (home/competenza-tier1/competenza-tier2/single-avvocato/page-generic) + viewport (mobile/desktop)
- Async main CSS via preload + onload swap + noscript fallback
- Tier-1 deep detection: hardcoded 3 slug Tier-1 (DEC-021)"
```

---

## 📋 PHASE 3 — WebP conversion blog thumbnails (~60 min)

### 3.1 Audit immagini blog

```bash
# Find blog featured images currently served
docker-compose exec -T wp wp post list \
    --post_type=post --post_status=publish \
    --posts_per_page=10 --format=csv \
    --fields=ID,post_name,_thumbnail_id 2>&1 | head
```

### 3.2 Genera WebP via WP-CLI + ImageMagick (Docker)

Verifica che il container WP abbia ImageMagick:
```bash
docker-compose exec -T wp which convert
# Se no: aggiungi a Dockerfile o usa GD library PHP nativo
```

Per ogni immagine in `wp-content/uploads/` (limita a directory blog-archive):

```bash
# Find tutte le JPG/PNG in uploads
find wp-content/uploads/ -type f \( -name "*.jpg" -o -name "*.jpeg" -o -name "*.png" \) | head -20

# Converti in WebP (quality 85, fallback graceful)
for img in $(find wp-content/uploads/ -type f \( -name "*.jpg" -o -name "*.jpeg" \) -newer wp-content/uploads/.webp-marker 2>/dev/null || find wp-content/uploads/ -type f \( -name "*.jpg" -o -name "*.jpeg" \)); do
    webp="${img%.*}.webp"
    if [ ! -f "$webp" ]; then
        cwebp -q 85 -mt "$img" -o "$webp" 2>&1 | tail -1
    fi
done
touch wp-content/uploads/.webp-marker
```

### 3.3 PHP filter per servire WebP via `<picture>` element

In `inc/perf.php` (estendi):

```php
/**
 * Wave 4.5 — Serve WebP via <picture> element con fallback JPG/PNG.
 * Filter wp_get_attachment_image che wrap output in <picture>.
 */
if (!function_exists('saltelli_serve_webp_picture')) :
function saltelli_serve_webp_picture($html, $attachment_id, $size, $icon, $attr) {
    if (is_admin() || is_feed()) return $html;
    
    $src_full = wp_get_attachment_image_url($attachment_id, $size);
    if (!$src_full) return $html;
    
    // Calcola path WebP equivalente
    $webp_url = preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $src_full);
    $webp_path = str_replace(content_url(), WP_CONTENT_DIR, $webp_url);
    
    if (!file_exists($webp_path)) return $html;  // No WebP → fallback originale
    
    // Wrap in <picture>
    return '<picture>'
         . '<source srcset="' . esc_url($webp_url) . '" type="image/webp">'
         . $html
         . '</picture>';
}
endif;
add_filter('wp_get_attachment_image', 'saltelli_serve_webp_picture', 10, 5);
```

### 3.4 Test Phase 3

```bash
# Verifica WebP generati
ls wp-content/uploads/2024/*/*.webp | head -5

# Verifica rendering <picture> element
curl -s "http://localhost:8080/risorse/blog/" | grep -A 3 '<picture>' | head -10
# Atteso: <picture><source srcset="...webp" type="image/webp"><img ...></picture>
```

### 3.5 Commit Phase 3

```bash
git add wp-content/themes/saltelli/inc/perf.php
git add wp-content/uploads/.webp-marker
# WebP files gitignored, ma vengono generati lato server in produzione

git commit -m "wave4-5: phase 3 — WebP conversion + <picture> element per blog thumbnails

- saltelli_serve_webp_picture() filter wrap wp_get_attachment_image in <picture>
- WebP generated via cwebp -q 85 (fallback graceful se WebP non esiste)
- Atteso: blog-archive mobile +3-5 punti Lighthouse (transferred bytes ridotti)"
```

---

## 📋 PHASE 4 — Smoke + Lighthouse post-Wave 4.5 (~30 min)

### 4.1 NO regression smoke (CRITICAL)

Riusa script Wave 5 + Wave 6:

```bash
# Smoke Wave 5 — riusa artifacts esistenti
bash .claude/knowledge/audits/wave5-ia-refactor/cli-output/smoke-runner.sh \
    > .claude/knowledge/audits/wave4-5/regression/wave5-audit-aligned.txt

# Smoke Wave 6
bash .claude/knowledge/audits/wave6/smoke-runner.sh \
    > .claude/knowledge/audits/wave4-5/regression/wave6-smoke.txt

# Verifica PASS
grep -c "PASS" .claude/knowledge/audits/wave4-5/regression/wave5-audit-aligned.txt
# Atteso: 33

grep -c "PASS" .claude/knowledge/audits/wave4-5/regression/wave6-smoke.txt
# Atteso: 21
```

### 4.2 Lighthouse post-Wave 4.5

```bash
mkdir -p .claude/knowledge/audits/wave4-5/lh-post/
bash scripts/wave4/lh-run.sh .claude/knowledge/audits/wave4-5/lh-post/

# Confronto delta vs baseline pre-Wave 4.5
echo "=== Lighthouse delta Wave 4.5 ===" > .claude/knowledge/audits/wave4-5/lh-delta.txt
for f in .claude/knowledge/audits/wave4-5/lh-baseline-pre/*.json; do
    fname=$(basename "$f" .json)
    pre=$(jq -r '.categories.performance.score' "$f" 2>/dev/null)
    post=$(jq -r '.categories.performance.score' ".claude/knowledge/audits/wave4-5/lh-post/${fname}.json" 2>/dev/null)
    pre_pct=$(printf '%.0f' $(echo "$pre * 100" | bc -l))
    post_pct=$(printf '%.0f' $(echo "$post * 100" | bc -l))
    delta=$((post_pct - pre_pct))
    echo "${fname}: ${pre_pct} → ${post_pct} (Δ${delta})" >> .claude/knowledge/audits/wave4-5/lh-delta.txt
done
cat .claude/knowledge/audits/wave4-5/lh-delta.txt
```

**Target Wave 4.5**: portare mobile Tier1/Tier2/Avvocato/Blog-archive a 92+. Desktop blog-archive a 92.

---

## 📋 PHASE 5 — Bump version + report + push (~30 min)

### 5.1 Bump theme version

In `wp-content/themes/saltelli/style.css`:
```
Version: 1.3.1-wave4-5-critical-css-webp
```

In `wp-content/themes/saltelli/functions.php`:
```php
define('SALTELLI_VERSION', '1.3.1-wave4-5-critical-css-webp');
```

### 5.2 Report finale

Crea `.claude/knowledge/recovery/WAVE4-5-CRITICAL-CSS-WEBP-REPORT.md` con:
- Phase summary 1-5
- Lighthouse delta tabella vs Wave 4 baseline
- Smoke regression Wave 5 + 6 + Wave 4 (NO regression)
- File modificati / creati riepilogo
- Decisione GO/NO-GO Wave 7

### 5.3 Commit + push

```bash
git add -A
git commit -m "wave4-5: phase 5 — bump 1.3.1 + Lighthouse post + report

Lighthouse delta vs Wave 4:
- mobile home: XX → YY (Δ+ZZ)
- mobile tier1-tributario: 89 → YY (Δ+ZZ)
- mobile tier2-cartelle: 90 → YY (Δ+ZZ)
- ... (vedi lh-delta.txt)

NO regression smoke Wave 5 (84/84 PASS).
NO regression smoke Wave 6 (21/21 PASS).

Closes Wave 4.5 — gap Lighthouse residuo Wave 4 chiuso."

git push origin feat/wave4-5-critical-css-webp
```

---

## ✅ Acceptance criteria Wave 4.5

- [ ] Branch `feat/wave4-5-critical-css-webp` da `main` post-Wave 4
- [ ] 5 phases eseguite, 5+ commit phase-by-phase
- [ ] Per-template critical CSS extraction: 10 file (5 pattern × 2 viewport) in `assets/css/critical/`
- [ ] Pattern detection corretto: home / competenza-tier1 / competenza-tier2 / single-avvocato / page-generic
- [ ] WebP conversion blog thumbnails attiva (almeno 80% delle JPG/PNG in uploads convertite)
- [ ] `<picture>` element wrap automatico via filter `wp_get_attachment_image`
- [ ] **Mobile Lighthouse**: tutti i 6 URL ≥ 92 (target: 89→92, 90→93, 86→92)
- [ ] **Desktop Lighthouse**: blog-archive 88 → 92+
- [ ] **NO regression smoke Wave 5**: 33/33 + 18/18 + 33/33 PASS
- [ ] **NO regression smoke Wave 6**: 21/21 PASS
- [ ] **NO regression smoke Wave 4**: security headers attivi, perf.php intact
- [ ] Theme version bumpata `1.3.1-wave4-5-critical-css-webp`
- [ ] Branch pushed (NO merge automatico)
- [ ] Report `.claude/knowledge/recovery/WAVE4-5-CRITICAL-CSS-WEBP-REPORT.md`

---

## 🚨 Cosa fare in caso di errore

| Situazione | Action |
|---|---|
| Critical CSS rompe layout (FOUC visibile) | Estendi extraction (penthouse `--keepLargerMediaQueries=true` o aumenta `--renderWaitTime`), oppure aumenta size inline. Se persiste: rollback Phase 2 + valuta strategia ibrida. |
| Mobile Lighthouse NON sale a 92 | Verifica delta vs baseline, ispezione opportunità LH (TBT, LCP, CLS). Possibile cause: `<picture>` non attivo per tutti images, o critical CSS estratto incompleto. Re-extract con renderWaitTime alto. |
| Regression smoke Wave 5 o Wave 6 | STOP immediato, identifica commit causa, revert mirato. NON proseguire. |
| WebP generation fallisce in Docker | Verifica `cwebp` installato. Alternative: GD library PHP (lentissima ma works), oppure plugin WP commerciale (non in scope, sopratutto pre-Wave7). |
| `<picture>` element rotto | Verifica filter priority, MIME type WebP server-served correttamente. Disabilita filter se persiste e usa solo critical CSS. |

---

## 🎯 Output expected

1. Branch `feat/wave4-5-critical-css-webp` con 5 commit phase-by-phase
2. File creati / modificati totali:
   - **NEW**: `assets/css/critical/` (10 file), `scripts/wave4-5/extract-critical.sh`
   - **MOD**: `inc/critical-css.php` (rewrite per pattern detection), `inc/perf.php` (estensione WebP filter), `style.css`, `functions.php` (version bump)
3. Audit trail completo in `.claude/knowledge/audits/wave4-5/`:
   - `lh-baseline-pre/` (12 reports)
   - `lh-post/` (12 reports)
   - `lh-delta.txt`, `critical-extraction.log`
   - `regression/` (smoke Wave 5 + 6 + 4)
4. Report `.claude/knowledge/recovery/WAVE4-5-CRITICAL-CSS-WEBP-REPORT.md`
5. Theme version `1.3.1-wave4-5-critical-css-webp`

L'orchestratore (chat) audisce + se OK ti dice di procedere con merge `feat/wave4-5-critical-css-webp → main` (no-ff) + tag `v1.3.1-wave4-5-critical-css-webp`.

---

## 🔗 Riferimenti

- `WAVE4-PRODUCTION-READINESS-REPORT.md` — gap residuo Wave 4 + lessons learned
- `inc/critical-css.php` — pattern critical CSS pre-Wave 4.5
- `inc/perf.php` — JS optimization Wave 4
- `CLAUDE.md` — single source of truth
- DEC-018, DEC-024, DEC-025-COMPLETED, DEC-026-COMPLETED — decisioni in vigore
