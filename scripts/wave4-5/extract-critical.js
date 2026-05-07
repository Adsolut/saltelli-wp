#!/usr/bin/env node
/**
 * Wave 4.5 — Per-template critical CSS extraction.
 *
 * Iterates 5 template patterns × 2 viewports (mobile 375x812 / desktop 1440x900),
 * runs penthouse against the local stack (http://localhost:8080), and writes the
 * extracted critical CSS to wp-content/themes/saltelli/assets/css/critical/.
 *
 * Source CSS: aggregato di tokens.css + base.css + components.css + logo.css +
 * cro.css + sections.css concatenati in /tmp così penthouse vede tutto e può
 * scegliere solo i selettori usati above-fold.
 *
 * Target dimensione: ogni file < 14 KB (TCP slow-start window).
 */
const fs = require('fs');
const path = require('path');
const penthouse = require('penthouse');

const ROOT = path.resolve(__dirname, '..', '..');
const THEME_CSS = path.join(ROOT, 'wp-content', 'themes', 'saltelli', 'assets', 'css');
const OUT_DIR = path.join(THEME_CSS, 'critical');

const PATTERNS = {
    'home': 'http://localhost:8080/',
    'competenza-tier1': 'http://localhost:8080/aree-di-pratica/privati/diritto-tributario/',
    'competenza-tier2': 'http://localhost:8080/aree-di-pratica/privati/cartelle-esattoriali-e-multe/',
    'single-avvocato': 'http://localhost:8080/chi-siamo/team/antonia-battista/',
    'page-generic': 'http://localhost:8080/contatti/',
};

const VIEWPORTS = {
    mobile: { width: 375, height: 812 },
    desktop: { width: 1440, height: 900 },
};

// Source CSS: concateniamo i 6 bundle del tema così penthouse ha tutta la
// regola-base disponibile e puo' filtrare ciò che non serve above-fold.
const SOURCE_CSS_FILES = [
    'tokens.css',
    'base.css',
    'components.css',
    'logo.css',
    'components/cro.css',
    'sections.css',
];

async function buildSourceCss() {
    const tmpFile = path.join(__dirname, '..', '..', '.cache-source.css');
    const parts = [];
    for (const f of SOURCE_CSS_FILES) {
        const p = path.join(THEME_CSS, f);
        if (fs.existsSync(p)) {
            parts.push(`/* ===== ${f} ===== */`);
            parts.push(fs.readFileSync(p, 'utf8'));
        } else {
            console.warn(`⚠️  missing ${p}`);
        }
    }
    fs.writeFileSync(tmpFile, parts.join('\n\n'));
    return tmpFile;
}

async function extract(pattern, url, viewportName, viewport, sourceCssPath) {
    const outFile = path.join(OUT_DIR, `${pattern}-${viewportName}.css`);
    console.log(`\n→ ${pattern}-${viewportName}: ${url}`);

    const result = await penthouse({
        url,
        cssString: fs.readFileSync(sourceCssPath, 'utf8'),
        width: viewport.width,
        height: viewport.height,
        timeout: 90000,
        renderWaitTime: 2000,
        keepLargerMediaQueries: false,
        propertiesToRemove: [],
        forceInclude: [
            // Always include header (sticky, above-fold cross-template)
            '.sl-header', '.sl-header__inner', '.sl-header__nav', '.sl-header__brand',
            '.sl-header__brand-title', '.sl-header__brand-sub', '.sl-header__phone',
            '.sl-header__menu-btn', '.sl-header__cta',
            '.sl-header.is-scrolled',
            // Skip-link a11y (visible only on focus)
            '.skip-link', '.sl-skip-link',
            // Container utility
            '.sl-container', '.sl-saltelli-container',
            // Mono utility (eyebrows ovunque sono above-fold)
            '.sl-mono',
        ],
    });

    // penthouse 2.5.x returns { css } object
    const css = typeof result === 'string' ? result : (result && result.css) || '';
    fs.writeFileSync(outFile, css);
    const size = fs.statSync(outFile).size;
    const sizeKB = (size / 1024).toFixed(1);
    const status = size > 14336 ? `⚠️  ${sizeKB}KB (>14KB)` : `✅ ${sizeKB}KB`;
    console.log(`  ${status}  →  ${path.relative(ROOT, outFile)}`);
    return { pattern, viewport: viewportName, size, file: outFile };
}

async function main() {
    if (!fs.existsSync(OUT_DIR)) fs.mkdirSync(OUT_DIR, { recursive: true });

    console.log('Building source CSS...');
    const sourceCssPath = await buildSourceCss();
    const sourceSize = (fs.statSync(sourceCssPath).size / 1024).toFixed(0);
    console.log(`Source CSS: ${sourceSize} KB → ${path.relative(ROOT, sourceCssPath)}`);

    const results = [];
    for (const [pattern, url] of Object.entries(PATTERNS)) {
        for (const [vpName, vp] of Object.entries(VIEWPORTS)) {
            try {
                const r = await extract(pattern, url, vpName, vp, sourceCssPath);
                results.push(r);
            } catch (err) {
                console.error(`❌ ${pattern}-${vpName} failed:`, err.message);
                results.push({ pattern, viewport: vpName, error: err.message });
            }
        }
    }

    console.log('\n=== Summary ===');
    for (const r of results) {
        if (r.error) {
            console.log(`  ❌ ${r.pattern}-${r.viewport}: ${r.error}`);
        } else {
            console.log(`  ${r.size > 14336 ? '⚠️ ' : '✅'} ${r.pattern}-${r.viewport}: ${(r.size/1024).toFixed(1)} KB`);
        }
    }

    fs.unlinkSync(sourceCssPath);
}

main().then(() => process.exit(0)).catch((e) => {
    console.error(e);
    process.exit(1);
});
