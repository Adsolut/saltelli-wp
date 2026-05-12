# PROMPT AGENT — Design Handoff Wave P3 · HOME hero variant B + SCF additive

> **Scope**: aggiungere hero bg image (variant B "cream scrim asimmetrico") alla homepage. Verify altre 6 sezioni (atteso 0 drift). 3 SCF field additive a `group_homepage_v1` per swap Media Library via Elena. Picsum placeholder ora, foto reale Wave 5.1.
>
> **Branch**: `feat/design-handoff-home`
> **Stima**: 1.5–2h (severity 🟢 LIGHT, pre-flight orchestratore completo)
> **Modalità**: lean, no version bump per CSS-only changes. Bump SE SCF additive committato (1.3.14).
> **Sessione**: una sola Claude Code, no parallelismo.

---

## CONTESTO

Wave 3/12 sequenza Design Handoff. P1 chrome + P2 footer dovrebbero essere già mergeate quando lanci questa.

**Decisioni orchestratore acquisite** (NON rinegoziare):
1. SoT design tokens = `tokens.css` corrente vince (KEEP CURRENT)
2. SCF data contract immutabile: 14 field text esistenti in `group_homepage_v1` Elena-approved → **NON toccare**. Solo **additive** consentito.
3. **Hero variant B** "cream scrim asimmetrico" (vedi `design-handoff/home/hero-banner.html` righe 115-137)
4. **Picsum placeholder ora**, foto reale Wave 5.1 (Elena swap Media Library)
5. Pattern responsive `<picture>` 3 source (≥1024 / ≥640 / mobile), srcset 1x/2x, `loading="eager"` + `fetchpriority="high"` (LCP candidate)
6. AVIF + WebP = **defer Wave 5.1** (Picsum non supporta, serve plugin). **Ora solo JPG via Picsum**

**Pre-flight orchestratore già fatto** (Explore agent output):

| Categoria | Esito |
|---|---|
| 7 sezioni JSX desktop | tutte mappate WP (hero + areas + studio + team + cases + press + contact) |
| Drift hero | 🔴 **1 CRITICAL** — crea `.sl-hero__media` + `<picture>` + scrim cream + photo-credit + filter grayscale |
| Drift non-hero (6 sezioni) | 🟢 **0** — già allineate (Wave 5 STEP 3 ha gestito) |
| SCF additive | 3 field nuovi (`hero_image`, `hero_image_credit`, `hero_image_alt`) |
| Severity | 🟢 LIGHT, ETA 1.5-2h |
| Righe CSS nuove | ~43-58 |
| Righe PHP nuove | ~12-15 (wrapper conditional hero_image) |

---

## ⚠️ HARD INVARIANT — SCF DATA CONTRACT PRESERVATION

`group_homepage_v1` ha 14 field text/textarea/select + 1 repeater (press_outlets) Wave 5 STEP 3 P1, Elena-approved.

- **NON ACCETTABILE**: rinominare/rimuovere/refactor field esistenti, cambiare location rule (`page=17`), cambiare disable-Gutenberg pattern (Home è SCF-only).
- **ACCETTABILE (additive)**: 3 nuovi field per hero image.

---

## PRE-FLIGHT (5 min)

1. Leggi nell'ordine:
   - `CLAUDE.md` (Hard constraints, Design system, "Design → Code handoff rule golden", Lessons learned)
   - `.claude/knowledge/audits/design-handoff/RECOMMENDATION.md` (§A KEEP CURRENT, §B prioritization, §C Risk Analysis perf/LCP)
   - **JSX source**:
     - `design-handoff/home/index.jsx` (441 righe, 7 sezioni)
     - `design-handoff/home/mobile.jsx` (mobile variant)
     - `design-handoff/home/hero-banner.html` (proposta 4 varianti, focus su variant B righe 115-137 + responsive `<picture>` pattern righe 270-289)
   - **WP target**:
     - `wp-content/themes/saltelli/front-page.php` (homepage template)
     - `wp-content/themes/saltelli/acf-json/group_homepage_v1.json` (14 field esistenti — NON rinominare/rimuovere)
     - Blocco `.sl-hero*` in `wp-content/themes/saltelli/assets/css/sections.css`
   - `wp-content/themes/saltelli/assets/css/tokens.css` (SoT)

2. Verifica stato:
   ```sh
   git fetch origin
   git status                  # working tree pulito
   git log --oneline -3        # HEAD post-P2 footer merge atteso
   git checkout -b feat/design-handoff-home
   ```

3. Conferma in chat: branch creato + JSX home + hero-banner.html letti + prosegui.

---

## PHASE 1 — VERIFY (10-15 min, lean perché pre-flight già fatto)

### 1.A — Hero variant B target (dal hero-banner.html righe 115-137)

CSS atteso:
```css
.sl-hero {
  position: relative;
  overflow: hidden;
  isolation: isolate;
}
.sl-hero__media {
  position: absolute;
  inset: 0;
  z-index: 0;
}
.sl-hero__media img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  filter: grayscale(0.7) contrast(1.05);
  display: block;
}
.sl-hero__media::after {
  content: "";
  position: absolute;
  inset: 0;
  background: linear-gradient(90deg,
    var(--background) 0%,
    var(--background) 38%,
    rgba(250, 250, 248, 0.92) 50%,
    rgba(250, 250, 248, 0.4) 72%,
    rgba(250, 250, 248, 0) 100%);
}
.sl-hero__inner {
  position: relative;
  z-index: 2;
  /* ... existing styles, no change ... */
}
/* Variant B testo navy su crema → già il default current. NO CHANGE colori. */

.sl-hero__photo-credit {
  position: absolute;
  bottom: 16px;
  right: 24px;
  z-index: 3;
  font-family: var(--font-mono);
  font-size: 10px;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--text-muted);
  opacity: 0.55;
}
```

### 1.B — Responsive `<picture>` pattern (dal hero-banner.html righe 270-289)

Markup PHP atteso in `front-page.php`:
```php
<?php
$hero_image_id = get_field('hero_image', 17); // Page 17 = Home
$hero_credit = get_field('hero_image_credit', 17);
$hero_alt = get_field('hero_image_alt', 17);

// Fallback Picsum se hero_image vuoto (Wave P3 dev)
$picsum_seed = 'saltelli-marble';

if ($hero_image_id) {
  // Foto reale Media Library — use wp_get_attachment_image() con srcset auto
  $hero_html = wp_get_attachment_image($hero_image_id, 'full', false, [
    'loading' => 'eager',
    'fetchpriority' => 'high',
    'decoding' => 'async',
    'alt' => esc_attr($hero_alt ?: 'Studio Legale Saltelli, hero banner'),
  ]);
} else {
  // Picsum placeholder con responsive picture manual srcset
  $picsum_url = fn($w, $h) => "https://picsum.photos/seed/{$picsum_seed}/{$w}/{$h}";
  $hero_html = sprintf(
    '<picture>
      <source media="(min-width: 1024px)" srcset="%s 1x, %s 2x">
      <source media="(min-width: 640px)" srcset="%s 1x, %s 2x">
      <img src="%s" srcset="%s 1x, %s 2x" alt="%s" loading="eager" fetchpriority="high" decoding="async">
    </picture>',
    $picsum_url(1920, 1080), $picsum_url(3840, 2160),
    $picsum_url(1280, 800), $picsum_url(2560, 1600),
    $picsum_url(768, 600), $picsum_url(768, 600), $picsum_url(1536, 1200),
    esc_attr($hero_alt ?: 'Studio Legale Saltelli, hero banner (placeholder)')
  );
}
?>
<section class="sl-hero">
  <div class="sl-hero__media" aria-hidden="true">
    <?php echo $hero_html; ?>
  </div>
  <div class="sl-hero__inner">
    <!-- existing kicker + h1 + lede + meta — NO CHANGE -->
  </div>
  <?php if ($hero_credit) : ?>
    <div class="sl-hero__photo-credit">Photo · <?php echo esc_html($hero_credit); ?></div>
  <?php endif; ?>
</section>
```

### 1.C — SCF additive (3 field nuovi in group_homepage_v1.json)

Aggiungi al tab "Hero Homepage" (esistente) i 3 field:

```json
{
  "key": "field_hero_image",
  "label": "Immagine hero",
  "name": "hero_image",
  "type": "image",
  "instructions": "Immagine di sfondo della hero homepage. Variant B (cream scrim): consigliato 1920×1080 minimo per desktop retina. Picsum placeholder usato se vuoto.",
  "required": 0,
  "return_format": "id",
  "preview_size": "medium",
  "library": "all",
  "min_width": 768,
  "min_height": 600,
  "max_size": 5,
  "mime_types": "jpg,jpeg,png,webp"
},
{
  "key": "field_hero_image_credit",
  "label": "Credit fotografico",
  "name": "hero_image_credit",
  "type": "text",
  "instructions": "Es. \"Tetti di Napoli golden hour\". Appare in piccolo bottom-right hero. Vuoto = no credit visibile.",
  "maxlength": 60,
  "required": 0
},
{
  "key": "field_hero_image_alt",
  "label": "Alt text immagine (SEO/accessibilità)",
  "name": "hero_image_alt",
  "type": "text",
  "instructions": "Descrizione immagine per screen reader e SEO. Vuoto = fallback \"Studio Legale Saltelli, hero banner\".",
  "maxlength": 100,
  "required": 0
}
```

**Posizione field**: dentro `field_hero_tab` (tab "Hero Homepage" esistente), tra gli altri hero_* field. Mantieni l'ordine logico (es. dopo `hero_eyebrow` e prima di `hero_h1_*`).

### 1.D — Spot-check sezioni non-hero (0 drift atteso)

5 selettori spot-check verify (read-only, atteso ✓ già allineato):
- `.sl-areas` grid + filter pillole
- `.sl-studio` drop-cap + prose
- `.sl-team` 12-col asimmetrico
- `.sl-cases` 3-col grid (id/desc/outcome)
- `.sl-contact` 3-col grid

Atteso 5/5 ✓ (Wave 5 STEP 3 P1 Home + Wave 5 STEP 4 hanno già coperto).

---

## PHASE 2 — IMPLEMENT (45-60 min)

### 2.A — CSS hero variant B (sections.css)

Aggiungi blocco con scope marker `/* === design-handoff home P3 — hero variant B === */` in `assets/css/sections.css` (dopo blocco `.sl-hero*` esistente).

Token alignment §A: usa `var(--background)`, `var(--font-mono)`, `var(--text-muted)`, `var(--ls-mono)`. I valori `0.7` grayscale, `1.05` contrast, e i percentages del gradient sono nuovi per-selector (motivati dal JSX hero-banner.html).

### 2.B — SCF additive (group_homepage_v1.json)

Modifica il JSON aggiungendo i 3 field dopo `field_hero_tab`. Mantieni invariati tutti i field esistenti (text/textarea/select 14 field + repeater press_outlets).

Verifica JSON valido:
```sh
python3 -c "import json; json.load(open('wp-content/themes/saltelli/acf-json/group_homepage_v1.json'))"
```

### 2.C — PHP wrapper (front-page.php)

Aggiungi il wrapper conditional `<picture>` (vedi 1.B sopra) nel template hero. Punto critico: NON toccare il resto del template Home (14 field text Wave 5 STEP 3 + 6 sezioni Wave 5 STEP 4 — tutto invariato).

### 2.D — Sync staging + reload

```sh
rsync -avz wp-content/themes/saltelli/acf-json/ deploy@178.62.207.50:/var/www/saltelli/wp-content/themes/saltelli/acf-json/
rsync -avz wp-content/themes/saltelli/assets/css/ deploy@178.62.207.50:/var/www/saltelli/wp-content/themes/saltelli/assets/css/
rsync -avz wp-content/themes/saltelli/front-page.php deploy@178.62.207.50:/var/www/saltelli/wp-content/themes/saltelli/front-page.php
ssh deploy@178.62.207.50 "sudo systemctl reload php8.2-fpm && cd /var/www/saltelli && sudo -u www-data wp cache flush --path=/var/www/saltelli"
```

(OPcache reload obbligatorio post-edit `front-page.php` — lesson Wave 4.7.fix.3).

---

## PHASE 3 — SMOKE TEST (15-20 min)

### 3.A — Frontend curl smoke

```sh
echo "=== homepage HTML ==="
curl -s "https://staging.studiolegalesaltelli.it/" | grep -cE "sl-hero__media|sl-hero__photo-credit|picsum.photos|fetchpriority"
# atteso count >= 4 (media + credit eventually + picsum url + fetchpriority attr)

echo "=== verify <picture> srcset ==="
curl -s "https://staging.studiolegalesaltelli.it/" | grep -oE "srcset=\"[^\"]*\"" | head -5
# atteso: 2-3 srcset attribute con saltelli-marble seed
```

### 3.B — Visual verify breakpoint (375/768/1024/1440)

Apri staging in browser + dev tools:
- **375px mobile**: hero altezza ~720px, scrim crema occupa quasi tutto, immagine appena visibile a destra
- **768px tablet**: scrim asimmetrico più evidente
- **1024px+ desktop**: scrim 0→38% solid crema, 38→72% transizione, 72→100% immagine visibile
- Tutti i breakpoint: H1 navy + lede italic visibili, no overlap con immagine

### 3.C — Admin-side smoke (lesson Wave 4.7.fix.4)

WP Admin → Pagine → Home (17) → Modifica:
- Tab "Hero Homepage" → vedere i 3 nuovi field:
  - "Immagine hero" (campo image picker)
  - "Credit fotografico" (campo text)
  - "Alt text immagine" (campo text)
- I 14 field esistenti restano INTATTI e popolati come prima (eyebrow, h1_main, h1_emphasis, lede, ecc.)
- Salva → frontend invariato (i field nuovi vuoti = Picsum placeholder + no credit)

### 3.D — LCP verify (opzionale ma raccomandato)

Apri staging in Chrome DevTools → Lighthouse → run Performance audit:
- LCP element: deve essere l'hero `<img>` o `<picture>`
- `loading="eager"` + `fetchpriority="high"` attributi presenti
- LCP score atteso: ≤2.5s (verde) su connessione 4G simulata

Se LCP > 2.5s con Picsum: lascia tracciato come noto, foto reale Wave 5.1 con AVIF/WebP migliorerà.

---

## PHASE 4 — COMMIT + PUSH

Se SCF additive committato → version bump 1.3.13 → 1.3.14:

```sh
# Update functions.php + style.css se SCF additive nuovi (sono 3 nuovi field → sì)
# functions.php: SALTELLI_THEME_VERSION → '1.3.14-wave5-design-handoff-p3-home'
# style.css: Version: 1.3.14-wave5-design-handoff-p3-home

git add -A
git diff --cached --stat

git commit -m "feat(design-handoff): Wave P3 home — hero variant B + 3 SCF additive (v1.3.14)

Wave 3/12 sequenza Design Handoff. Hero variant B 'cream scrim asimmetrico' (design-handoff/home/hero-banner.html righe 115-137) implementata via:

CSS additivo (sections.css scope /* === design-handoff home P3 — hero variant B === */):
- .sl-hero { position: relative; overflow: hidden; isolation: isolate }
- .sl-hero__media (absolute inset 0, z-index 0, container <picture>)
- .sl-hero__media img (object-fit cover, filter grayscale(0.7) contrast(1.05))
- .sl-hero__media::after (linear-gradient 90deg cream scrim 0→38→50→72→100% asimmetrico)
- .sl-hero__photo-credit (absolute bottom-right, mono 10px, opacity 0.55)

SCF additive (group_homepage_v1.json, 14 field esistenti INVARIATI + 3 nuovi):
- hero_image (image, return_format id, library all, min 768×600, max 5MB, jpg/jpeg/png/webp)
- hero_image_credit (text 60ch)
- hero_image_alt (text 100ch, SEO/a11y)

PHP wrapper (front-page.php):
- Conditional wp_get_attachment_image() se hero_image popolato (foto reale Media Library)
- Picsum placeholder fallback (seed 'saltelli-marble') se hero_image vuoto — Wave P3 dev
- loading='eager' + fetchpriority='high' + decoding='async' su hero img (LCP optimization)
- <picture> 3 source desktop/tablet/mobile + srcset 1x/2x

Decisioni orchestratore §A KEEP CURRENT tokens.css confermato.
AVIF + WebP defer Wave 5.1 (Picsum supporta solo JPG; foto reale + plugin Image Optimization arriva post-cut).

Smoke test:
- Frontend curl: <picture> srcset + scrim + photo-credit markup presente
- Admin: tab Hero Homepage con 3 nuovi field, 14 field esistenti invariati
- 4 breakpoint: scrim asimmetrico correttamente renderizzato
- LCP: <verde/giallo/rosso da Lighthouse>

Branch: feat/design-handoff-home · 3 file changed · +XX/-YY"

git push origin feat/design-handoff-home
```

---

## OUTPUT FINALE in chat

- Drift verify: 1 CRITICAL (hero) fixato + 0 non-hero (atteso)
- SCF additive: 3 field aggiunti, 14 esistenti invariati ✓
- PHP wrapper: hero image conditional con Picsum fallback ✓
- Smoke test risultati (curl + admin + 4 breakpoint + LCP)
- SHA commit pushato
- ETA proposto P4 single-competenza-tier1

---

## HARD RULES

1. **SCF schema preservato**: 14 field esistenti `group_homepage_v1` INTATTI. Solo 3 additive.
2. **NO refactor template Home** (Wave 5 STEP 3 P1 Home Elena-approved): hero è l'unica sezione che cambia (additive bg image).
3. **Token alignment §A**: KEEP CURRENT, mai toccare `:root`. Valori scrim/filter/percentages per-selector motivati dal JSX.
4. **LCP**: `loading="eager"` + `fetchpriority="high"` su hero img (non rimuovere — è la sola LCP optimization possibile con Picsum).
5. **OPcache reload** post-edit front-page.php (lesson Wave 4.7.fix.3).
6. **Admin-side smoke test obbligatorio** (lesson Wave 4.7.fix.4): verifica 14 field esistenti intatti.
7. **One-writer-at-a-time**: UNICA sessione Code attiva.

---

## DECISIONE AUTONOMA AUTORIZZATA

- Posizione field SCF nel tab "Hero Homepage": orderly (dopo hero_eyebrow, prima di hero_h1).
- Picsum seed `saltelli-marble` confermato (variant B intent — palazzo nobiliare).
- Dimensioni Picsum per breakpoint: desktop 1920×1080 / 2x 3840×2160, tablet 1280×800 / 2x 2560×1600, mobile 768×600 / 2x 1536×1200.
- Min/max image size SCF: min 768×600 (mobile baseline), max 5MB (perf cap).
- Wording fallback alt: "Studio Legale Saltelli, hero banner".
- Eventuale split-reveal animation su H1 "Diritto, con misura.": preservata se già nel template, NON aggiungere se non c'è (out of scope LCP/critical CSS).

---

## TONO

Direct, concrete, zero filler. Stile commit usato dal progetto.

---

*Wave P3/12 sequenza Design Handoff. Prossima: P4 single-competenza-tier1 (verify + drift `clamp(72,10vw,160)` h1). Pattern lean = 1 wave alla volta su main.*
