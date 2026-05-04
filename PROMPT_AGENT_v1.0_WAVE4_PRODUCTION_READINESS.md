# PROMPT v1.0.0 WAVE 4 — Production Readiness (sequenziale single agent)

> **Per Claude Code in nuova sessione (single agent).** Tempo: ~2.5-3h.
> **PRECEDENZA**: Wave 0+1+2+3 completati. Theme `1.0.0-recovery-wave3` su staging.studiolegalesaltelli.it · 21/21 PASS · ACF popolato · cliente CMS-autonomous.
> **MISSIONE**: portare il sito da "demo presentabile" a "production-ready" — fonts self-hosted (WOFF2), SRI sui CDN, Critical CSS inline, Lighthouse ≥92, schema validato. Frontend deve restare INVARIATO ma le metriche di performance/sicurezza/SEO devono essere production-grade. Bump finale → `1.0.0-rc1`.

---

## 🎯 Tu sei

L'**Agente Production Readiness**. Wave 3 ha unlockato il CMS per Elena/Ludovica. Adesso devi:

1. **Self-hostare i font** (Playfair Display, DM Sans, JetBrains Mono) come WOFF2 — toglie dipendenza Google Fonts CDN
2. **Aggiungere SRI hash** sui CDN restanti (GSAP + ScrollTrigger) — protezione tampering
3. **Inline-are il Critical CSS** sui 5 template chiave — migliora LCP
4. **Audit Lighthouse** mobile + desktop con target ≥92 Performance / ≥95 Accessibility / 100 SEO / ≥95 Best Practices
5. **Validare schema JSON-LD** su 5 URL con Google Rich Results Test
6. **Cleanup pendenze infra** (reboot droplet kernel, certbot status)
7. **Bump 1.0.0-rc1** + report finale

```
WAVE 4 — 5 PHASES sequenziali

Phase 1 (~40 min): WOFF2 self-hosted (3 famiglie, 7 weights)
Phase 2 (~20 min): SRI hash su CDN GSAP + ScrollTrigger
Phase 3 (~30 min): Critical CSS inline (5 template above-the-fold)
Phase 4 (~40 min): Lighthouse audit + fix iterativo
Phase 5 (~20 min): Schema validation + cleanup infra + bump rc1 + report
```

---

## 📚 Letture obbligatorie

```
CLAUDE.md                                                       (hard constraints + design tokens)
.claude/knowledge/recovery/PROJECT_STATE.md                     (stato progetto)
.claude/knowledge/recovery/v1.0-WAVE3-TEMPLATE-REFACTOR.md      (cosa è stato fatto in Wave 3)
docs/EDITOR-HANDOFF.md                                          (cosa promette al cliente)

wp-content/themes/saltelli/
  ├── assets/css/tokens.css                  (LOCKED — solo lettura per riferimento font names)
  ├── assets/css/base.css                    (typography setup attuale — reference, NON modificare se eviti)
  ├── inc/enqueue.php                        (target: aggiungere enqueue fonts.css + rimuovere Google Fonts CDN)
  ├── header.php                             (target: SRI su <script> CDN + Critical CSS inline <head>)
  ├── functions.php                          (target: bump SALTELLI_THEME_VERSION + script_loader_tag filter)
  └── style.css                              (target: bump Version)

PROMPT_AGENT_F_PRODUCTION_READINESS.md       (eventuali note storiche se esiste)
PROMPT_AGENT_G_DEPLOY_DIGITALOCEAN.md        (path droplet + lessons learned)
```

---

## 🔒 Hard rules

| Rule | Decisione |
|---|---|
| **Frontend INVARIATO post-Wave 4** (stessa identica resa visiva su Chrome/Safari/mobile) | Test critical |
| **NESSUNA modifica `tokens.css`** (design tokens locked) | Locked |
| **NESSUNA modifica template PHP** (page-*.php, single-*.php, footer.php, header.php content body) | Out of scope — Wave 3 chiuso |
| **NESSUNA modifica copy editoriale**, ACF fields, CPT items | Out of scope |
| **NON aggiungere plugin** (Autoptimize, WP Rocket, ecc.) — fix manuale | Lesson learned: dipendenze plugin = debt |
| **NON aggiornare WordPress core o plugin esistenti** durante run | Stabilità |
| **Backup pre-Wave 4** obbligatorio (theme tar.gz + DB dump) prima di toccare | Rollback safety |
| **Smoke test 21 URL** dopo OGNI Phase su staging | Catch regression early |
| **Commit incrementale 1 per Phase** (5 commit + 1 finale bump) | Audit trail |
| **Path droplet**: `/var/www/saltelli/` — NO `/htdocs` | Lesson learned |
| **Idempotency**: re-run di una phase non duplica file/config | Safety |
| **WOFF2 weights coerenti con `tokens.css`**: Playfair 400 + 700 · DM Sans 400/500/700 · JetBrains Mono 400 | Match design system |
| **SRI hash valido**: ogni script CDN deve avere `integrity` calcolata sul file servito | Security real |
| **Lighthouse run su staging URL** (NON localhost) — production-like network | Misurazione affidabile |
| **Target Lighthouse**: Performance ≥92 (mobile+desktop), Accessibility ≥95, SEO 100, Best Practices ≥95 | Production-grade |
| **Schema validation Google Rich Results Test**: zero errori critici (warnings tollerati con motivazione) | GEO-ready |
| **Reboot droplet** SOLO con conferma esplicita orchestratore — non in autonomia | Downtime 30s |
| **Branch isolato**: `feat/wave4-production-readiness` parte da `main` (`2761f7a` o successivo) | Coordinazione coi prossimi commit |

---

## 📋 PHASE 1 — WOFF2 self-hosted (~40 min)

### 1.1 — Backup pre-Wave 4

```bash
cd /Users/aldosantoro/Desktop/DEV/saltelli-wp

# Snapshot theme + assets
STAMP=$(date +%Y%m%d-%H%M%S)
mkdir -p /tmp/saltelli-backups
tar czf /tmp/saltelli-backups/saltelli-pre-wave4-${STAMP}.tar.gz \
    wp-content/themes/saltelli/

# DB dump locale (per safety, anche se Wave 4 non tocca DB)
docker compose run --rm wpcli db export - 2>/dev/null | \
    gzip > /tmp/saltelli-backups/saltelli-db-pre-wave4-${STAMP}.sql.gz

ls -lh /tmp/saltelli-backups/ | tail -2
echo "✓ Backup pre-Wave4 in /tmp/saltelli-backups/"
```

### 1.2 — Crea branch dedicato + parti da main aggiornato

```bash
git fetch origin main
git checkout main
git pull --ff-only origin main
git checkout -b feat/wave4-production-readiness
echo "✓ branch feat/wave4-production-readiness creato da $(git rev-parse --short HEAD)"
```

### 1.3 — Identifica font references attuali

```bash
# Quali font sono enqueued via Google Fonts CDN?
grep -nE "fonts\.googleapis\.com|fonts\.gstatic\.com" \
    wp-content/themes/saltelli/header.php \
    wp-content/themes/saltelli/inc/enqueue.php \
    wp-content/themes/saltelli/functions.php 2>/dev/null

# Quali font-family sono dichiarati in tokens?
grep -E "^\s*--font-" wp-content/themes/saltelli/assets/css/tokens.css
```

Atteso (da CLAUDE.md):
- `--font-display: "Playfair Display" 400/700`
- `--font-body: "DM Sans" 400/500/700`
- `--font-mono: "JetBrains Mono" 400`

### 1.4 — Download TTF da Google Fonts (source of truth)

Download manuale via `google-webfonts-helper`:

```bash
mkdir -p wp-content/themes/saltelli/assets/fonts
cd /tmp
mkdir -p saltelli-fonts-download && cd saltelli-fonts-download

# Playfair Display 400 + 700 (latin + latin-ext)
curl -L "https://fonts.google.com/download?family=Playfair+Display" -o playfair.zip
curl -L "https://fonts.google.com/download?family=DM+Sans" -o dmsans.zip
curl -L "https://fonts.google.com/download?family=JetBrains+Mono" -o jbmono.zip

# Unzip + tieni solo i weight necessari
for z in playfair.zip dmsans.zip jbmono.zip; do
    unzip -o "$z" -d "${z%.zip}/"
done
```

Se i nomi file non sono esattamente `Regular`, `Bold`, `Medium`: ispeziona `ls` e rinomina coerentemente.

### 1.5 — Convert TTF → WOFF2

Servirà `woff2_compress` (CLI). Su macOS: `brew install woff2`.

```bash
# Verifica binario
which woff2_compress || brew install woff2

# Convert i 7 file target (sources finali)
cd /tmp/saltelli-fonts-download

# Playfair (display)
woff2_compress playfair/static/PlayfairDisplay-Regular.ttf
woff2_compress playfair/static/PlayfairDisplay-Bold.ttf

# DM Sans (body)
woff2_compress dmsans/static/DMSans-Regular.ttf
woff2_compress dmsans/static/DMSans-Medium.ttf
woff2_compress dmsans/static/DMSans-Bold.ttf

# JetBrains Mono (metadata)
woff2_compress jbmono/static/JetBrainsMono-Regular.ttf

ls -lh */**/*.woff2 | awk '{print $5, $NF}'
```

Target dimensioni: ogni WOFF2 ~30-90 KB.

### 1.6 — Copia i WOFF2 nel theme

```bash
TARGET=/Users/aldosantoro/Desktop/DEV/saltelli-wp/wp-content/themes/saltelli/assets/fonts

cp playfair/static/PlayfairDisplay-Regular.woff2 "$TARGET/playfair-display-400.woff2"
cp playfair/static/PlayfairDisplay-Bold.woff2    "$TARGET/playfair-display-700.woff2"
cp dmsans/static/DMSans-Regular.woff2            "$TARGET/dm-sans-400.woff2"
cp dmsans/static/DMSans-Medium.woff2             "$TARGET/dm-sans-500.woff2"
cp dmsans/static/DMSans-Bold.woff2               "$TARGET/dm-sans-700.woff2"
cp jbmono/static/JetBrainsMono-Regular.woff2     "$TARGET/jetbrains-mono-400.woff2"

ls -lh "$TARGET"
```

Naming convention: `{family-slug}-{weight}.woff2`. Lowercase, kebab-case.

### 1.7 — Crea `assets/css/fonts.css`

```bash
cat > wp-content/themes/saltelli/assets/css/fonts.css <<'CSS'
/**
 * fonts.css — Self-hosted WOFF2 declarations (Wave 4)
 *
 * Caricato PRIMA di tokens.css/base.css per garantire che `--font-display`,
 * `--font-body`, `--font-mono` (in tokens.css) puntino a famiglie già definite.
 *
 * Strategy:
 *   - WOFF2 only (96%+ browser support 2026, no fallback WOFF)
 *   - font-display: swap → text appare immediato con fallback system stack,
 *     poi switch al WOFF2 quando disponibile (no FOIT, accetta micro-FOUT)
 *   - subset latin + latin-ext (Italian copy)
 *   - preload dei 2 weights critici nell'header.php (Phase 3)
 *
 * @package Saltelli
 * @since 1.0.0-rc1 Wave 4
 */

/* ─────────────────────────────────────────────────────
 * Playfair Display (display headers)
 * ───────────────────────────────────────────────────── */
@font-face {
    font-family: 'Playfair Display';
    src: url('../fonts/playfair-display-400.woff2') format('woff2');
    font-weight: 400;
    font-style: normal;
    font-display: swap;
    unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
}

@font-face {
    font-family: 'Playfair Display';
    src: url('../fonts/playfair-display-700.woff2') format('woff2');
    font-weight: 700;
    font-style: normal;
    font-display: swap;
    unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
}

/* ─────────────────────────────────────────────────────
 * DM Sans (body)
 * ───────────────────────────────────────────────────── */
@font-face {
    font-family: 'DM Sans';
    src: url('../fonts/dm-sans-400.woff2') format('woff2');
    font-weight: 400;
    font-style: normal;
    font-display: swap;
}

@font-face {
    font-family: 'DM Sans';
    src: url('../fonts/dm-sans-500.woff2') format('woff2');
    font-weight: 500;
    font-style: normal;
    font-display: swap;
}

@font-face {
    font-family: 'DM Sans';
    src: url('../fonts/dm-sans-700.woff2') format('woff2');
    font-weight: 700;
    font-style: normal;
    font-display: swap;
}

/* ─────────────────────────────────────────────────────
 * JetBrains Mono (metadata: dates, tags, eyebrows)
 * ───────────────────────────────────────────────────── */
@font-face {
    font-family: 'JetBrains Mono';
    src: url('../fonts/jetbrains-mono-400.woff2') format('woff2');
    font-weight: 400;
    font-style: normal;
    font-display: swap;
}
CSS

echo "✓ fonts.css creato"
```

### 1.8 — Aggiorna `inc/enqueue.php`

Aggiungi enqueue di `fonts.css` con dependency precedente a `tokens.css` (deve caricarsi prima). Rimuovi/commenta gli enqueue di Google Fonts CDN.

```bash
grep -n "wp_enqueue_style\|fonts.googleapis" wp-content/themes/saltelli/inc/enqueue.php
```

Pattern di intervento (segui il style esistente del file):

```php
// ════════════════════════════════════════════════════════════
// Wave 4: Self-hosted fonts (replaces Google Fonts CDN)
// ════════════════════════════════════════════════════════════
wp_enqueue_style(
    'saltelli-fonts',
    get_theme_file_uri('assets/css/fonts.css'),
    [],
    SALTELLI_THEME_VERSION,
    'all'
);

// Tokens DEPENDS ON fonts (font-family vars references)
wp_enqueue_style(
    'saltelli-tokens',
    get_theme_file_uri('assets/css/tokens.css'),
    ['saltelli-fonts'],          // ← dependency aggiunta
    SALTELLI_THEME_VERSION,
    'all'
);

// ════════════════════════════════════════════════════════════
// Wave 4: REMOVED Google Fonts CDN enqueues
// (commento i wp_enqueue_style di fonts.googleapis.com / fonts.gstatic.com)
// ════════════════════════════════════════════════════════════
// wp_enqueue_style('saltelli-google-fonts', ...);   // ❌ Wave 4 self-hosted
```

⚠ **Verifica anche `header.php`** se ha `<link rel="preconnect" href="https://fonts.googleapis.com">` o `<link rel="stylesheet" href="https://fonts.googleapis.com/...">` hardcoded — vanno rimossi.

### 1.9 — Verifica fonts caricano

```bash
docker compose run --rm wpcli cache flush

# Controlla HTML output
curl -sL http://localhost:8080/ | grep -E "font-face|googleapis|fonts/.*\.woff2" | head -10

# Atteso:
#   - link a /wp-content/themes/saltelli/assets/css/fonts.css ✅
#   - NESSUN riferimento a fonts.googleapis.com ❌

# Smoke 21 URL
for url in "/" "/chi-siamo/" "/avvocati/" "/avvocati/emiliano-saltelli/" "/avvocati/fabiana-saltelli/" "/avvocati/antonia-battista/" "/avvocati/stefano-gaetano-tedesco/" "/casi/" "/costi/" "/contatti/" "/faq/" "/come-lavoriamo/" "/prima-consulenza/" "/lavora-con-noi/" "/richiedi-preventivo/" "/guide-gratuite/" "/competenze/diritto-tributario/" "/competenze/diritto-del-lavoro/" "/competenze/diritto-di-famiglia-lgbtq/" "/tipo-area/privati/" "/glossario-legale/"; do
    code=$(curl -o /dev/null -s -w "%{http_code}" "http://localhost:8080${url}")
    printf "%s  %s\n" "$code" "$url"
done
```

Verifica visiva manuale (`open http://localhost:8080`):
- Font Playfair Display visibile su h1?
- DM Sans su body text?
- JetBrains Mono su eyebrow `§ Servizio · ...`?
- Nessun FOIT (Flash of Invisible Text) > 200ms

### 1.10 — Phase 1 commit

```bash
git add wp-content/themes/saltelli/assets/fonts/ \
        wp-content/themes/saltelli/assets/css/fonts.css \
        wp-content/themes/saltelli/inc/enqueue.php \
        wp-content/themes/saltelli/header.php

git commit -m "feat(s2-v1.0.0-rc1-wave4): Phase 1 — WOFF2 self-hosted fonts

- 6 WOFF2 files: Playfair Display 400/700, DM Sans 400/500/700, JetBrains Mono 400
- assets/css/fonts.css con @font-face + font-display: swap + unicode-range
- inc/enqueue.php: rimossi Google Fonts CDN, aggiunto saltelli-fonts handle
- header.php: rimossi <link preconnect> Google Fonts hardcoded
- Dipendenza enqueue: tokens.css depends on fonts.css (caricamento ordinato)

Frontend invariato (smoke 21/21 PASS). FOIT eliminato via swap.
Riduce dipendenza esterna + privacy GDPR (no Google Fonts loading)."
```

---

## 📋 PHASE 2 — SRI hash su CDN GSAP + ScrollTrigger (~20 min)

### 2.1 — Identifica script CDN attuali

```bash
grep -nE "cdnjs\.cloudflare\.com|unpkg\.com|jsdelivr\.net" \
    wp-content/themes/saltelli/header.php \
    wp-content/themes/saltelli/footer.php \
    wp-content/themes/saltelli/inc/enqueue.php
```

Atteso: GSAP 3.12.5 + ScrollTrigger 3.12.5 (via `cdnjs.cloudflare.com`).

### 2.2 — Calcola SRI hash (sha384)

Per ogni URL CDN attivo:

```bash
# GSAP core
GSAP_URL="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"
GSAP_HASH=$(curl -sL "$GSAP_URL" | openssl dgst -sha384 -binary | openssl base64 -A)
echo "GSAP   integrity=\"sha384-${GSAP_HASH}\""

# ScrollTrigger
ST_URL="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"
ST_HASH=$(curl -sL "$ST_URL" | openssl dgst -sha384 -binary | openssl base64 -A)
echo "STrig  integrity=\"sha384-${ST_HASH}\""
```

Salva i due hash. Sono valori specifici per quella versione + quel CDN — se la versione cambia, il hash cambia.

### 2.3 — Inietta integrity + crossorigin via filter

ll modo WP-corretto è hookare `script_loader_tag` in `functions.php`:

```php
// ════════════════════════════════════════════════════════════
// Wave 4 Phase 2: SRI hash su CDN scripts
// ════════════════════════════════════════════════════════════
add_filter('script_loader_tag', function ($tag, $handle, $src) {
    $sri_map = [
        'gsap'         => 'sha384-{GSAP_HASH_QUI}',
        'scrolltrigger' => 'sha384-{ST_HASH_QUI}',
    ];
    
    if (!isset($sri_map[$handle])) {
        return $tag;
    }
    
    $integrity = $sri_map[$handle];
    
    // Inject integrity + crossorigin attributes
    $tag = str_replace(
        '<script ',
        '<script integrity="' . esc_attr($integrity) . '" crossorigin="anonymous" ',
        $tag
    );
    
    return $tag;
}, 10, 3);
```

Sostituisci `{GSAP_HASH_QUI}` e `{ST_HASH_QUI}` con i valori reali calcolati al 2.2.

### 2.4 — Verifica SRI funzionante

```bash
docker compose run --rm wpcli cache flush

# HTML output deve contenere integrity= + crossorigin="anonymous"
curl -sL http://localhost:8080/ | grep -E "gsap|scrolltrigger" | grep integrity

# Atteso: 2 righe con integrity="sha384-..." crossorigin="anonymous"
```

Test pratico nel browser DevTools (Console):
- Apri http://localhost:8080
- Console NON deve mostrare warning "Failed integrity check"
- Network tab → script gsap.min.js + ScrollTrigger.min.js → status 200

Se il browser bloccca con "integrity check failed": il hash è sbagliato o il file è stato cambiato dal CDN. Ricalcola al 2.2.

### 2.5 — Phase 2 commit

```bash
git add wp-content/themes/saltelli/functions.php
git commit -m "feat(s2-v1.0.0-rc1-wave4): Phase 2 — SRI hash su CDN GSAP + ScrollTrigger

- script_loader_tag filter inietta integrity= + crossorigin=anonymous
- SHA-384 hash calcolato sui file servity da cdnjs.cloudflare.com
- GSAP 3.12.5 + ScrollTrigger 3.12.5 protetti contro tampering CDN

Console browser: zero warning integrity. Network: status 200.
Lighthouse Best Practices: +2 punti attesi."
```

---

## 📋 PHASE 3 — Critical CSS inline (~30 min)

### 3.1 — Identifica i 5 template above-the-fold

I template che ricevono Critical CSS:

| Template | URL test | Note |
|---|---|---|
| Homepage | `/` | front-page.php |
| Tier-1 competenza | `/competenze/diritto-tributario/` | single-competenza.php (deep) |
| Avvocato profile | `/avvocati/emiliano-saltelli/` | single-avvocato.php |
| Page costi (info-shared style) | `/costi/` | template-parts/page-costi.php |
| Default page fallback | `/lo-studio/` | page.php → page-chi-siamo.php |

### 3.2 — Tool: `critical` CLI (npm)

```bash
# Verifica Node + npm
node --version  # ≥18 atteso
npm --version

# Install critical (one-shot, no save)
npx --yes critical@5 --version
```

Se `critical` non gira (problema headless Chrome): fallback a Phase 3.alt (estrazione manuale).

### 3.3 — Genera Critical CSS per ogni template

```bash
mkdir -p wp-content/themes/saltelli/assets/css/critical

declare -A urls=(
    ["home"]="/"
    ["competenza"]="/competenze/diritto-tributario/"
    ["avvocato"]="/avvocati/emiliano-saltelli/"
    ["costi"]="/costi/"
    ["page"]="/lo-studio/"
)

for slug in "${!urls[@]}"; do
    url="http://localhost:8080${urls[$slug]}"
    out="wp-content/themes/saltelli/assets/css/critical/${slug}.css"
    
    npx --yes critical@5 \
        --base "wp-content/themes/saltelli/assets/css/" \
        --src "$url" \
        --target "$out" \
        --width 1440 --height 900 \
        --width 375 --height 812 \
        --inline false \
        --extract false \
        --penthouse.timeout 60000 \
        2>&1 | tail -5
    
    SIZE=$(wc -c < "$out" | tr -d ' ')
    echo "  ${slug}: ${SIZE} bytes"
done
```

Target: ogni file critical ≤ 14 KB (limite TCP slow start window).

### 3.4 — Inline Critical CSS in `header.php`

Modifica `header.php` per includere il CSS critical inline nel `<head>`, scelto in base al template corrente:

```php
<head>
    <?php wp_head(); ?>

    <?php
    // ════════════════════════════════════════════════════════════
    // Wave 4 Phase 3: Critical CSS inline (LCP optimization)
    // ════════════════════════════════════════════════════════════
    $critical_slug = 'page';  // default fallback
    if (is_front_page()) {
        $critical_slug = 'home';
    } elseif (is_singular('competenza')) {
        $critical_slug = 'competenza';
    } elseif (is_singular('avvocato')) {
        $critical_slug = 'avvocato';
    } elseif (is_page('costi')) {
        $critical_slug = 'costi';
    }
    
    $critical_path = get_theme_file_path("assets/css/critical/{$critical_slug}.css");
    if (file_exists($critical_path)) {
        echo '<style id="saltelli-critical">';
        echo file_get_contents($critical_path);
        echo '</style>';
    }
    ?>

    <!-- Preload font critical (Wave 4) -->
    <link rel="preload" href="<?php echo esc_url(get_theme_file_uri('assets/fonts/playfair-display-400.woff2')); ?>" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?php echo esc_url(get_theme_file_uri('assets/fonts/dm-sans-400.woff2')); ?>" as="font" type="font/woff2" crossorigin>
</head>
```

### 3.5 — Defer non-critical CSS

Aggiungi filter su `style_loader_tag` in `functions.php` per defer i CSS non-critical:

```php
// ════════════════════════════════════════════════════════════
// Wave 4 Phase 3: Defer non-critical stylesheets (LCP boost)
// ════════════════════════════════════════════════════════════
add_filter('style_loader_tag', function ($tag, $handle, $href, $media) {
    // Handles CRITICAL — caricati subito, niente defer
    $critical_handles = ['saltelli-fonts', 'saltelli-tokens', 'saltelli-base'];
    
    if (in_array($handle, $critical_handles, true)) {
        return $tag;
    }
    
    // Defer altri stylesheet via media-trick
    return str_replace(
        "rel='stylesheet'",
        "rel='stylesheet' media='print' onload=\"this.media='all'\"",
        $tag
    );
}, 10, 4);
```

⚠ **Test cross-browser**: Safari ha quirks su `onload` di stylesheet — verifica.

### 3.6 — Verifica Phase 3

```bash
docker compose run --rm wpcli cache flush

# HTML output: verifica <style id="saltelli-critical"> presente
curl -sL http://localhost:8080/ | grep -A1 'saltelli-critical' | head -5

# Verifica preload font
curl -sL http://localhost:8080/ | grep 'rel="preload"' | head -5

# Smoke 21 URL
# (riusa il loop di Phase 1.9)
```

Verifica visiva su Chrome DevTools → Network tab → throttle "Slow 3G":
- LCP target: < 2.5s (oggi probabile 4-5s)
- Render blocking resources: 0 critical CSS file (sono tutti deferred)

### 3.7 — Phase 3 commit

```bash
git add wp-content/themes/saltelli/assets/css/critical/ \
        wp-content/themes/saltelli/header.php \
        wp-content/themes/saltelli/functions.php

git commit -m "feat(s2-v1.0.0-rc1-wave4): Phase 3 — Critical CSS inline + defer non-critical

- 5 critical CSS files generati (home, competenza, avvocato, costi, page)
- Inline in <head> via header.php template-aware switch
- Preload 2 font WOFF2 critical (Playfair 400 + DM Sans 400)
- style_loader_tag filter defer non-critical stylesheet (media print + onload)

LCP: ~4.5s → ~1.8s atteso (Slow 3G throttle).
Render blocking resources eliminati per first paint."
```

---

## 📋 PHASE 4 — Lighthouse audit + fix iterativo (~40 min)

### 4.1 — Setup Lighthouse CLI

```bash
npm install -g lighthouse 2>&1 | tail -2
lighthouse --version  # ≥11 atteso
```

Se l'install è stato fatto in passato: `lighthouse --version` ritorna versione esistente.

### 4.2 — Baseline run su staging (mobile + desktop)

```bash
mkdir -p .claude/knowledge/audits/wave4-lighthouse
cd .claude/knowledge/audits/wave4-lighthouse

URLS=(
    "https://staging.studiolegalesaltelli.it/"
    "https://staging.studiolegalesaltelli.it/lo-studio/"
    "https://staging.studiolegalesaltelli.it/avvocati/emiliano-saltelli/"
    "https://staging.studiolegalesaltelli.it/competenze/diritto-tributario/"
    "https://staging.studiolegalesaltelli.it/faq/"
)

for U in "${URLS[@]}"; do
    SLUG=$(echo "$U" | sed 's|.*staging.studiolegalesaltelli.it/||;s|/$||;s|/|-|g')
    SLUG=${SLUG:-home}
    
    # Mobile
    lighthouse "$U" \
        --form-factor=mobile \
        --throttling.cpuSlowdownMultiplier=4 \
        --output=json --output-path="${SLUG}-mobile.json" \
        --quiet --chrome-flags="--headless"
    
    # Desktop
    lighthouse "$U" \
        --form-factor=desktop \
        --throttling.cpuSlowdownMultiplier=1 \
        --output=json --output-path="${SLUG}-desktop.json" \
        --quiet --chrome-flags="--headless"
    
    echo "✓ $SLUG done"
done

cd /Users/aldosantoro/Desktop/DEV/saltelli-wp
```

### 4.3 — Estrai score baseline

```bash
cd .claude/knowledge/audits/wave4-lighthouse

for f in *.json; do
    SLUG=${f%.json}
    PERF=$(python3 -c "import json; print(round(json.load(open('$f'))['categories']['performance']['score']*100))")
    A11Y=$(python3 -c "import json; print(round(json.load(open('$f'))['categories']['accessibility']['score']*100))")
    SEO=$(python3  -c "import json; print(round(json.load(open('$f'))['categories']['seo']['score']*100))")
    BP=$(python3   -c "import json; print(round(json.load(open('$f'))['categories']['best-practices']['score']*100))")
    printf "%-40s P=%3d  A11Y=%3d  SEO=%3d  BP=%3d\n" "$SLUG" "$PERF" "$A11Y" "$SEO" "$BP"
done

cd /Users/aldosantoro/Desktop/DEV/saltelli-wp
```

### 4.4 — Target & gap analysis

| Metrica | Target | Gap se < target |
|---|---|---|
| Performance | ≥92 (mobile + desktop) | Critical |
| Accessibility | ≥95 | High |
| SEO | 100 | Medium |
| Best Practices | ≥95 | Medium |

Per ogni URL sotto target: estrai gli **opportunity** dal report e applica fix.

```bash
# Lista opportunities per URL specifico
python3 -c "
import json
d = json.load(open('.claude/knowledge/audits/wave4-lighthouse/home-mobile.json'))
for aid, audit in d['audits'].items():
    if audit.get('score') is not None and audit['score'] < 0.9 and audit.get('details', {}).get('overallSavingsMs', 0) > 100:
        print(f\"  - {aid}: {audit.get('title')} (saving {audit['details'].get('overallSavingsMs',0)}ms)\")
"
```

### 4.5 — Fix iterativo

Pattern dei fix più comuni e risolvibili in scope Wave 4:

| Issue tipico | Fix |
|---|---|
| `unused-css-rules` | Già parzialmente risolto Phase 3 (defer). Se persiste: split CSS per template |
| `render-blocking-resources` | Verifica defer Phase 3 funzioni (style_loader_tag) |
| `largest-contentful-paint-element` | Hero immagine: aggiungi `fetchpriority="high"` su `<img>` hero |
| `total-byte-weight` | Verifica WOFF2 served correttamente, non TTF fallback |
| `uses-text-compression` | Server gzip/brotli — già attivo nginx (verifica lato droplet) |
| `image-alt` | A11y: aggiungi `alt=""` decorative o descrittivo |
| `meta-description` | Verifica Yoast genera per ogni page |
| `crawlable-anchors` | Link `href="#"` o `href="javascript:..."` — fix manuale |

⚠ **NON installare plugin** (Autoptimize ecc.). Fix manuale on a need-by-need basis.

### 4.6 — Re-run + verifica target raggiunto

Dopo ogni batch di fix:

```bash
cd .claude/knowledge/audits/wave4-lighthouse
mkdir -p post-fix

for U in "${URLS[@]}"; do
    SLUG=$(...)  # come 4.2
    lighthouse "$U" --form-factor=mobile --output=json --output-path="post-fix/${SLUG}-mobile.json" --quiet --chrome-flags="--headless"
    lighthouse "$U" --form-factor=desktop --output=json --output-path="post-fix/${SLUG}-desktop.json" --quiet --chrome-flags="--headless"
done

# Diff baseline vs post-fix
for f in post-fix/*.json; do
    BASE_F="${f#post-fix/}"
    # estrai scores e confronta
done
```

Stop loop quando: tutti i 10 file (5 URL × mobile/desktop) raggiungono i target. Se dopo 3 iterazioni il target è ancora out: **documenta il gap** in `.claude/knowledge/audits/wave4-lighthouse/REPORT.md` con root cause analysis e proponi fix Wave 5.

### 4.7 — Phase 4 commit

```bash
git add .claude/knowledge/audits/wave4-lighthouse/ \
        wp-content/themes/saltelli/  # any fix applicato

git commit -m "feat(s2-v1.0.0-rc1-wave4): Phase 4 — Lighthouse audit + fix

Baseline + post-fix audit su 5 URL × mobile/desktop:
- Home, Lo Studio, Emiliano Saltelli, Tributario, FAQ

Score finali (post-fix):
  Performance:   mobile XX  desktop XX  (target ≥92)
  Accessibility: mobile XX  desktop XX  (target ≥95)
  SEO:           mobile XX  desktop XX  (target 100)
  Best Practices:mobile XX  desktop XX  (target ≥95)

Fix applicati:
  - [LIST PRECISO DEI FIX REALI]

Audit raw + report: .claude/knowledge/audits/wave4-lighthouse/"
```

(Riempi gli `XX` con i numeri reali post-fix.)

---

## 📋 PHASE 5 — Schema validation + cleanup infra + bump rc1 (~20 min)

### 5.1 — Schema JSON-LD validation

5 URL chiave + tipo schema atteso:

| URL | Schema atteso |
|---|---|
| `/` | Organization + WebSite |
| `/lo-studio/` | LocalBusiness / LegalService |
| `/avvocati/emiliano-saltelli/` | Person (Attorney) |
| `/competenze/diritto-tributario/` | Service / FAQPage (se ha FAQ) |
| `/faq/` | FAQPage |

Validation manuale via Google Rich Results Test:
```
https://search.google.com/test/rich-results
```

Per ogni URL: incolla URL → analizza → screenshot del risultato → salva in `.claude/knowledge/audits/wave4-schema/`.

Atteso: zero error, warning tollerati (es. `image: missing optional property` accettabile su Person).

Alternativa programmatica (verifica solo presenza JSON-LD):

```bash
mkdir -p .claude/knowledge/audits/wave4-schema

URLS=(
    "https://staging.studiolegalesaltelli.it/"
    "https://staging.studiolegalesaltelli.it/lo-studio/"
    "https://staging.studiolegalesaltelli.it/avvocati/emiliano-saltelli/"
    "https://staging.studiolegalesaltelli.it/competenze/diritto-tributario/"
    "https://staging.studiolegalesaltelli.it/faq/"
)

for U in "${URLS[@]}"; do
    SLUG=$(echo "$U" | sed 's|.*staging.studiolegalesaltelli.it/||;s|/$||;s|/|-|g')
    SLUG=${SLUG:-home}
    
    curl -sL "$U" | python3 -c "
import sys, re, json
html = sys.stdin.read()
matches = re.findall(r'<script[^>]*type=[\"\']application/ld\+json[\"\'][^>]*>(.*?)</script>', html, re.DOTALL)
print(f'  {len(matches)} JSON-LD blocks found')
for i, m in enumerate(matches):
    try:
        d = json.loads(m.strip())
        types = d.get('@type', d.get('@graph', [{}])[0].get('@type', '?')) if isinstance(d, dict) else '?'
        print(f'    block {i}: @type = {types}')
    except json.JSONDecodeError as e:
        print(f'    block {i}: INVALID JSON: {e}')
" > ".claude/knowledge/audits/wave4-schema/${SLUG}-schema.txt"
    
    cat ".claude/knowledge/audits/wave4-schema/${SLUG}-schema.txt"
done
```

### 5.2 — llms.txt + robots.txt verifica

```bash
curl -sI https://staging.studiolegalesaltelli.it/llms.txt | head -3
curl -sL https://staging.studiolegalesaltelli.it/llms.txt | head -20

curl -sL https://staging.studiolegalesaltelli.it/robots.txt
```

Atteso `/llms.txt`:
- HTTP 200
- Content-Type text/plain
- Body con # Studio Legale Saltelli + URL canonical

Atteso `/robots.txt`:
- AI crawlers explicit allow (GPTBot, ClaudeBot, PerplexityBot, GoogleOther, ChatGPT-User)

### 5.3 — Cleanup infra droplet (NON eseguire reboot in autonomia)

Verifica solo lo stato. Eventuale reboot kernel resta a discrezione orchestratore.

```bash
ssh deploy@178.62.207.50 "
echo '=== Kernel running ==='
uname -r
echo ''
echo '=== Kernel installed (max) ==='
ls /lib/modules/ | sort -V | tail -1
echo ''
echo '=== Reboot required? ==='
[ -f /var/run/reboot-required ] && cat /var/run/reboot-required.pkgs || echo '(no reboot file)'
echo ''
echo '=== Certbot status ==='
sudo systemctl is-active certbot.timer
sudo certbot certificates 2>&1 | grep -E 'Expiry|Domain' | head -10
echo ''
echo '=== Disk + memory ==='
df -h /var/www | tail -1
free -h | head -2
echo ''
echo '=== Theme version droplet ==='
grep SALTELLI_THEME_VERSION /var/www/saltelli/wp-content/themes/saltelli/functions.php
"
```

Documenta findings in `.claude/knowledge/audits/wave4-infra/REPORT.md`. Se kernel ≠ installed → flag a orchestratore con suggerimento "reboot 30s downtime, conferma esplicita richiesta".

### 5.4 — Bump version → 1.0.0-rc1

```bash
cd /Users/aldosantoro/Desktop/DEV/saltelli-wp

# functions.php
sed -i.bak "s/define('SALTELLI_THEME_VERSION', '[^']*');/define('SALTELLI_THEME_VERSION', '1.0.0-rc1');/" \
    wp-content/themes/saltelli/functions.php
rm wp-content/themes/saltelli/functions.php.bak

# style.css
sed -i.bak "s/^Version: .*$/Version: 1.0.0-rc1/" \
    wp-content/themes/saltelli/style.css
rm wp-content/themes/saltelli/style.css.bak

# Verifica
grep -E "Version|SALTELLI_THEME_VERSION" \
    wp-content/themes/saltelli/style.css \
    wp-content/themes/saltelli/functions.php | head -5
```

### 5.5 — Smoke finale 21 URL su staging

⚠ Wave 4 ha modificato file theme — ma **non li hai ancora pushati su droplet**. Decisione orchestratore: deploy droplet entro o oltre Wave 4 chiusura?

Se sì (deploy in scope):
```bash
# Rsync delta theme su droplet
rsync -avz \
    wp-content/themes/saltelli/assets/fonts/ \
    deploy@178.62.207.50:/var/www/saltelli/wp-content/themes/saltelli/assets/fonts/

rsync -avz \
    wp-content/themes/saltelli/assets/css/fonts.css \
    wp-content/themes/saltelli/assets/css/critical/ \
    deploy@178.62.207.50:/var/www/saltelli/wp-content/themes/saltelli/assets/css/

rsync -avz \
    wp-content/themes/saltelli/header.php \
    wp-content/themes/saltelli/functions.php \
    wp-content/themes/saltelli/style.css \
    wp-content/themes/saltelli/inc/enqueue.php \
    deploy@178.62.207.50:/var/www/saltelli/wp-content/themes/saltelli/

ssh deploy@178.62.207.50 "wp cache flush --path=/var/www/saltelli && wp transient delete --all --path=/var/www/saltelli"
```

Smoke 21 URL su staging.studiolegalesaltelli.it (riusa loop Phase 1.9 con dominio HTTPS).

Se **no** (deploy fuori scope, decisione orchestratore): chiudi Wave 4 con tutto local + lascia rsync a step separato.

### 5.6 — Phase 5 commit + report finale

```bash
# Crea report
cat > .claude/knowledge/recovery/v1.0-rc1-WAVE4-PRODUCTION-READINESS.md <<'MD'
# v1.0.0-rc1 Wave 4 COMPLETE — Production Readiness

> Wave 4 sequenziale single-agent · production prep finale.
> Aggiornato: $(date +%Y-%m-%d). Branch: `feat/wave4-production-readiness`.

---

## 🎯 Score: 5/5 phases · target Lighthouse raggiunti · production-grade

[POPOLARE con dati reali post-execution]

## 📦 Phase summaries

### Phase 1 — WOFF2 self-hosted
- 6 WOFF2 files in assets/fonts/
- @font-face declarations in fonts.css (font-display: swap)
- Google Fonts CDN rimosso → 0 dipendenze esterne fonts

### Phase 2 — SRI hash CDN
- GSAP 3.12.5 + ScrollTrigger 3.12.5 con integrity sha384
- script_loader_tag filter in functions.php

### Phase 3 — Critical CSS inline
- 5 template critical files (home, competenza, avvocato, costi, page)
- Inline in <head> + preload 2 font critical
- Defer non-critical stylesheet

### Phase 4 — Lighthouse audit
- Score finali post-fix: [POPOLARE]

### Phase 5 — Schema + infra cleanup
- 5 URL schema validation
- Bump 1.0.0-rc1

## 🚦 Next: production cut

[Documenta cosa serve per cut 1.0.0]
MD

git add wp-content/themes/saltelli/style.css \
        wp-content/themes/saltelli/functions.php \
        .claude/knowledge/recovery/v1.0-rc1-WAVE4-PRODUCTION-READINESS.md \
        .claude/knowledge/audits/

git commit -m "feat(s2-v1.0.0-rc1-wave4): Phase 5 — Schema validation + bump rc1 + report

- 5 URL schema JSON-LD validation (Google Rich Results compatible)
- llms.txt + robots.txt verified (AI crawlers allow)
- Bump SALTELLI_THEME_VERSION + style.css → 1.0.0-rc1
- Recovery report .claude/knowledge/recovery/v1.0-rc1-WAVE4-PRODUCTION-READINESS.md

Audit raw: .claude/knowledge/audits/wave4-{lighthouse,schema,infra}/

Wave 4 complete. Pronto per cut produzione 1.0.0 (DNS switch + tag release)."
```

---

## ✅ Definition of Done (Wave 4)

- [ ] **Phase 1**: 6 WOFF2 self-hosted, Google Fonts CDN rimosso, smoke 21/21 PASS
- [ ] **Phase 2**: SRI hash su 2 CDN scripts (GSAP + ScrollTrigger), console browser zero warning
- [ ] **Phase 3**: Critical CSS inline su 5 template, defer non-critical, preload font critical
- [ ] **Phase 4**: Lighthouse mobile + desktop su 5 URL — Performance ≥92, A11y ≥95, SEO 100, BP ≥95 (deviazioni documentate)
- [ ] **Phase 5**: Schema JSON-LD validato, llms.txt + robots.txt OK, kernel/certbot status verificati, bump 1.0.0-rc1
- [ ] **6 commit incrementali** sul branch `feat/wave4-production-readiness`
- [ ] **Report finale** in `.claude/knowledge/recovery/v1.0-rc1-WAVE4-PRODUCTION-READINESS.md`
- [ ] **Backup pre-Wave 4** salvati in `/tmp/saltelli-backups/` (audit rollback)
- [ ] **Frontend invariato** (verifica visiva manuale + 21/21 smoke)
- [ ] **Branch pushato** su origin (NON merge in main — quello fa l'orchestratore in chat dopo audit)

---

## 🚦 Branch & deploy state finale

**Branch**: `feat/wave4-production-readiness`
- 6 commit Phase 1-5 + bump finale
- Push: `git push -u origin feat/wave4-production-readiness`
- Merge in main: **decisione orchestratore in chat dopo audit Wave 4**

**Droplet staging**:
- Decisione orchestratore: deploy in scope Wave 4 (Phase 5.5) oppure step separato
- Se deploy in scope: rsync delta + cache flush + smoke 21 URL https
- Se deploy out of scope: lascia tutto locale, orchestratore lancia rsync separato

**Backup pre-Wave 4**: `/tmp/saltelli-backups/saltelli-pre-wave4-${STAMP}.tar.gz` + `/tmp/saltelli-backups/saltelli-db-pre-wave4-${STAMP}.sql.gz`

---

## 🚦 Next dopo Wave 4 (out of scope, info per orchestratore)

```
Production cut 1.0.0:
  ☐ Audit Wave 4 da orchestratore (chat)
  ☐ Merge feat/wave4-production-readiness → main
  ☐ Tag release: git tag -a v1.0.0 -m "Production cut · Wave 1+2+3+4 + EDITOR-HANDOFF"
  ☐ DNS switch staging.studiolegalesaltelli.it → studiolegalesaltelli.it
  ☐ Bump SALTELLI_THEME_VERSION → 1.0.0
  ☐ Comunicazione cliente go-live
  
Out of scope Wave 4 (futuri):
  ☐ Sessione formazione 30 min Elena/Ludovica (basata su docs/EDITOR-HANDOFF.md)
  ☐ Eventuale Wave 5 ACF-izzazione page-chi-siamo (oggi hardcoded)
  ☐ Google Business Profile setup
  ☐ Earned media: pitch Altalex/Diritto.it
  ☐ Housekeeping branch orfani Codencore (wave1-agent-a, wave1-agent-c, wave3-task-05..10)
```

---

## ⚠️ Quando STOP e ritorno orchestratore

Ferma esecuzione e ritorna all'orchestratore (chat) se:

1. **Lighthouse Performance < 80** dopo 3 iterazioni di fix → root cause analysis necessaria, fuori scope wave 4
2. **Schema validation errori critici** non risolvibili modificando `inc/schema/*.php` (richiede refactor schema → fuori scope)
3. **WOFF2 conversione fallisce** (woff2_compress non installabile) → orchestratore decide alternative (CDN font alternativo? Self-host con cdnfonts.com?)
4. **Frontend regression** post-Phase X → rollback al backup, segnala root cause
5. **SSH droplet inaccessible** → infra issue, non procedere
6. **Critical CSS tool fails** (npx critical / penthouse non gira su Mac) → fallback Phase 3.alt o richiedi guidance

**Tono comunicazione di ritorno**: come da CLAUDE.md — diretto, concreto, ranked options, no apology padding.

---

*Generato 2026-05-04 by orchestrator (Claude in chat) per Claude Code single-agent execution.*
*Pattern simmetrico a PROMPT_AGENT_v1.0_WAVE3_TEMPLATE_REFACTOR.md.*
