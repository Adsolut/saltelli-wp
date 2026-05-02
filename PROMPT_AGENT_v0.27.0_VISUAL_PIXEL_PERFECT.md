# PROMPT v0.27.0 — Visual CSS Layout Refactor (Pixel-Perfect Visivo)

> **Per Claude Code in nuova sessione.** Apri `saltelli-wp/`, leggi questo file, eseguilo. Tempo: ~75-90 min sequential.
> **PRECEDENZA:** v0.26.0 schema dignity completato.

---

## 🎯 PROBLEMA REALE IDENTIFICATO

Audit Code v0.26.0 ha confermato: **markup template OK al 91%** (Person schema, FAQPage, sezioni, content). MA Duccio segnala visualmente: *"Tutto: il mood visivo non è quello pixel-perfect del JSX"*.

**Diagnosi profonda:** il GAP è 100% **CSS visivo/layout**, NON markup/structure.

```
ESEMPIO /avvocati/emiliano-saltelli/:

✅ Markup: hero, sticky, bio, competenze (Sei aree), formazione/timeline, casi, articoli, cta
✅ Schema: Person + Attorney + jobTitle + alumniOf
✅ Content: tutto presente

❌ HERO LAYOUT: il JSX prevede grid 2-col 1fr/1fr
   (foto SX 1:1 ritratto B/N | testo DX nome + ruolo + spec tags)
   LIVE: NON ha .sl-attorney__hero-grid CSS rule applicata
   → renderizza probabilmente stack 1-col con foto piccola sopra
   
❌ STICKY POSITIONING: JSX prevede aside sticky 240px sidebar SX
   (CTA mono "Contatto diretto" + Tel/Email/WhatsApp)
   LIVE: position:fixed left:8px translateY(-50%) → FLOATING TOOLBAR
   verticale appiccicata al bordo schermo (NO sidebar pulita)
   
❌ BODY LAYOUT: JSX grid 240px/1fr (sticky | content)
   LIVE: 1-col stack, content full-width
```

**Prova oggettiva:** Duccio ha guardato JSX e live SIDE-BY-SIDE e ha constatato gap visivo. Audit Code dice "91% pixel match" ma è metric sui MARKUP/CONTENT, non sul VISUAL RENDERING.

---

## 🔧 STRATEGIA — Refactor CSS attorney + tier-1 + chi-siamo per match JSX

Tre template critical hanno layout visivo divergente. Refactor CSS **mirato**, mantenendo class esistenti `.sl-attorney__*` etc. (no rinaming).

```
TASK 1 — Attorney CSS layout pixel-perfect (~30 min)
TASK 2 — Tier-1 CSS layout adjustments (~20 min)
TASK 3 — Chi-siamo CSS layout (~15 min)
TASK 4 — Cross-page CSS regression check + bump deploy (~10 min)
```

---

## 📚 Letture obbligatorie

```
.claude/knowledge/design/sessione-2/saltelli-s2-attorney-single.jsx     (riferimento)
.claude/knowledge/design/sessione-2/saltelli-s2-practice-tier1.jsx       (riferimento)
.claude/knowledge/design/sessione-2/saltelli-s2-chi-siamo.jsx            (riferimento)

CLAUDE.md
.claude/knowledge/design/sessione-1/tokens.css (locked — NON modificare valori)

wp-content/themes/saltelli/single-avvocato.php   (verifica markup)
wp-content/themes/saltelli/single-competenza.php (verifica markup)
wp-content/themes/saltelli/page.php              (blocco is_page('chi-siamo'))
wp-content/themes/saltelli/assets/css/sections.css (target di refactor)
```

---

## 🔒 Hard rules

| Rule | Spiegazione |
|---|---|
| **NESSUN refactor markup PHP** — solo CSS | Markup è OK, problema è visual layout |
| **NESSUNA modifica tokens.css** valori | Locked palette/font/spacing |
| **Class esistenti** `.sl-attorney__*` / `.sl-tier1__*` / `.sl-chi-siamo__*` MANTIENI | No rinaming |
| **CSS scope marker** `/* === v0.27.0 [task] === */` per ogni rule nuova | Audit trail |
| **JSX = ground truth visivo**: replica ESATTI gridTemplateColumns, gap, padding, aspectRatio | Tu hai fonte verità lì |
| Cache flush + smoke test 3 URL chiave dopo OGNI task | Verify no regression |
| Bump version + git commit dopo OGNI task | Atomicity |

---

## TASK 1 — Attorney CSS pixel-perfect (~30 min)

### 1.1 — Specifiche estratte dal JSX

**HERO** (saltelli-s2-attorney-single.jsx, righe 35-100):

```jsx
{/* Hero section wrapper */}
<section style={{
  maxWidth: 1440, margin: "0 auto",
  padding: "80px clamp(24px, 5vw, 96px) 64px",
  display: "grid", 
  gridTemplateColumns: "1fr 1fr",     // ← 2-COL EQUAL
  gap: 64,
  alignItems: "stretch",
}}>
  
  {/* Ritratto — col SX 1:1 */}
  <div style={{
    aspectRatio: "1 / 1",
    background: "linear-gradient(135deg, #4a4540 0%, #1a1815 100%)",
    border: "1px solid var(--border)",
    position: "relative", 
    overflow: "hidden",
    filter: "grayscale(1) contrast(1.05)",
    transition: "filter 600ms var(--ease-editorial)",
  }} 
  onMouseEnter={e => filter: "grayscale(0)"}>
    {/* contenuto foto/placeholder */}
  </div>
  
  {/* Testo — col DX */}
  <div style={{
    display: "flex", 
    flexDirection: "column", 
    justifyContent: "space-between"   // ← spazia top/bottom
  }}>
    <div>
      <div className="sl-mono">§ Avvocato · Founding Partner</div>
      <h1 style={{
        fontSize: "clamp(56px, 6vw, 88px)",
        lineHeight: 0.98, 
        letterSpacing: "-0.025em",
        marginBottom: 32,
      }}>
        Emiliano<br/>
        <em>Saltelli.</em>      // ← italic + color text-muted
      </h1>
      <div style={{
        fontFamily: "var(--font-display)", 
        fontSize: 22, 
        fontStyle: "italic",
        color: "var(--text)",
      }}>
        {role}                  // "Founding Partner · Tributarista"
      </div>
    </div>
    
    {/* Spec tags row */}
    <div style={{ display: "flex", flexWrap: "wrap", gap: 8, marginTop: 32 }}>
      {/* tag mono uppercase 11px border 1px */}
    </div>
  </div>
</section>
```

**BODY 240px/1fr** (righe 105-170):

```jsx
<section style={{
  maxWidth: 1440, margin: "0 auto",
  padding: "96px clamp(24px, 5vw, 96px)",
  display: "grid", 
  gridTemplateColumns: "240px 1fr",   // ← STICKY SX 240px | CONTENT 1fr
  gap: 96,
}}>
  
  {/* Sticky aside SX */}
  <aside style={{
    position: "sticky",
    top: 120,
    alignSelf: "start",
    height: "fit-content",
  }}>
    <div className="sl-mono">Contatto diretto</div>
    {/* Tel/Email/WhatsApp links mono editoriale */}
  </aside>
  
  {/* Content DX */}
  <div>
    {/* Bio + Sei aree + Formazione + Casi + CTA */}
  </div>
</section>
```

**FORMAZIONE rows** (righe ~230):

```jsx
<div style={{
  display: "grid", 
  gridTemplateColumns: "100px 1fr",   // ← anno mono | titolo+ente
  gap: 32, 
  paddingBottom: 32, 
  marginBottom: 32,
  borderBottom: "1px solid var(--border)",
}}>
```

**CASI rows** (righe ~270):

```jsx
<div style={{
  display: "grid",
  gridTemplateColumns: "200px 1fr 160px",  // ← id mono | desc | outcome
  gap: 32, 
  padding: "28px 0",
  borderBottom: "1px solid var(--border)",
  alignItems: "baseline",
}}>
```

### 1.2 — CSS scope da AGGIUNGERE/SOSTITUIRE in sections.css

```css
/* ═══════════════════════════════════════════════════════════════
   v0.27.0 TASK 1 — ATTORNEY pixel-perfect visual refactor
   Source: saltelli-s2-attorney-single.jsx
   Target: replica esatto layout grid 2-col hero + body 240/1fr sticky
   ═══════════════════════════════════════════════════════════════ */

/* === HERO 2-col grid === */
.sl-attorney__hero,
.sl-attorney__hero-inner {
    max-width: 1440px;
    margin-inline: auto;
    padding: 80px clamp(24px, 5vw, 96px) 64px;
}

.sl-attorney__hero-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;       /* JSX exact */
    gap: 64px;
    align-items: stretch;
}

@media (max-width: 1023px) {
    .sl-attorney__hero-grid {
        grid-template-columns: 1fr;       /* stack mobile */
        gap: 32px;
    }
}

/* === Hero LEFT: portrait 1:1 === */
.sl-attorney__portrait {
    aspect-ratio: 1 / 1;
    border: 1px solid var(--border);
    position: relative;
    overflow: hidden;
    filter: grayscale(1) contrast(1.05);
    transition: filter 600ms var(--ease-editorial, cubic-bezier(0.25, 1, 0.5, 1));
    background: linear-gradient(135deg, color-mix(in srgb, var(--text-muted) 90%, var(--primary)) 0%, var(--primary) 100%);
}

@media (hover: hover) {
    .sl-attorney__portrait:hover {
        filter: grayscale(0) contrast(1);
    }
}

.sl-attorney__portrait img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center top;
}

.sl-attorney__portrait--placeholder {
    background: linear-gradient(135deg, var(--surface) 0%, var(--text-muted) 100%);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 16px;
    color: rgba(255, 255, 255, 0.55);
}

/* === Hero RIGHT: name + role + tags === */
.sl-attorney__hero-text {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 100%;
}

.sl-attorney__role {
    margin-bottom: 32px;
}

.sl-attorney__name {
    font-family: var(--font-display);
    font-size: clamp(56px, 6vw, 88px);
    line-height: 0.98;
    letter-spacing: -0.025em;
    font-weight: 400;
    margin: 0 0 32px;
    color: var(--primary);
}

.sl-attorney__name em {
    font-style: italic;
    color: var(--text-muted);
}

.sl-attorney__lede {
    font-family: var(--font-display);
    font-size: 22px;
    font-style: italic;
    color: var(--text);
    margin: 0 0 32px;
    line-height: 1.5;
}

.sl-attorney__specs {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 32px;
}

.sl-attorney__specs li,
.sl-attorney__specs span {
    list-style: none;
    font-family: var(--font-mono);
    font-size: 11px;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--text-muted);
    border: 1px solid var(--border);
    padding: 6px 12px;
}

/* === BODY 240px/1fr (sticky sx + content dx) === */
.sl-attorney__body,
.sl-attorney__bio {
    max-width: 1440px;
    margin-inline: auto;
    padding: 96px clamp(24px, 5vw, 96px);
}

@media (min-width: 1024px) {
    .sl-attorney__body,
    .sl-attorney__bio-wrapper {
        display: grid;
        grid-template-columns: 240px 1fr;    /* JSX exact */
        gap: 96px;
    }
}

/* === STICKY OVERRIDE — convert from fixed toolbar to sidebar === */
.sl-attorney__sticky {
    /* OVERRIDE pattern fixed toolbar */
    position: sticky !important;
    top: 120px !important;
    left: auto !important;
    transform: none !important;
    align-self: start;
    height: fit-content;
    display: grid;
    gap: 16px;
}

.sl-attorney__sticky-label {
    font-family: var(--font-mono);
    font-size: 11px;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--text-muted);
    margin-bottom: 24px;
}

.sl-attorney__sticky-link {
    display: grid;
    gap: 4px;
    text-decoration: none;
    padding-block: 12px;
    border-bottom: 1px solid var(--border);
    transition: border-color var(--dur-fast, 200ms) var(--ease-editorial, cubic-bezier(0.25, 1, 0.5, 1));
}

.sl-attorney__sticky-link:hover {
    border-bottom-color: var(--accent);
}

.sl-attorney__sticky-link-label {
    font-family: var(--font-mono);
    font-size: 10px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--text-muted);
}

.sl-attorney__sticky-link-value {
    font-family: var(--font-display);
    font-size: 16px;
    color: var(--primary);
}

@media (max-width: 1023px) {
    .sl-attorney__sticky {
        position: static !important;
        flex-direction: row !important;
        flex-wrap: wrap;
        gap: 8px;
        margin-block: 24px;
    }
    
    .sl-attorney__sticky-link {
        border-bottom: 0;
        border: 1px solid var(--border);
        padding: 12px 16px;
    }
}

/* === BIO PROSE drop-cap === */
.sl-attorney__bio-prose {
    font-size: 19px;
    line-height: 1.75;
    color: var(--text);
    max-width: 60ch;
}

.sl-attorney__bio-prose > p:first-of-type::first-letter {
    font-family: var(--font-display);
    font-size: 84px;
    line-height: 0.85;
    float: left;
    margin: 8px 16px 0 0;
    color: var(--primary);
    font-weight: 400;
}

@media (max-width: 767px) {
    .sl-attorney__bio-prose > p:first-of-type::first-letter {
        font-size: 60px;
        margin: 4px 12px 0 0;
    }
}

/* === FORMAZIONE rows 100px / 1fr === */
.sl-attorney__formazione-row,
.sl-attorney__timeline-row {
    display: grid;
    grid-template-columns: 100px 1fr;     /* JSX exact */
    gap: 32px;
    padding-bottom: 32px;
    margin-bottom: 32px;
    border-bottom: 1px solid var(--border);
}

.sl-attorney__formazione-row:last-child,
.sl-attorney__timeline-row:last-child {
    border-bottom: 0;
}

.sl-attorney__formazione-anno,
.sl-attorney__timeline-anno {
    font-family: var(--font-mono);
    font-size: 13px;
    color: var(--accent);
    letter-spacing: 0.08em;
}

@media (max-width: 767px) {
    .sl-attorney__formazione-row,
    .sl-attorney__timeline-row {
        grid-template-columns: 64px 1fr;
        gap: 16px;
    }
}

/* === CASI rows 200px / 1fr / 160px === */
.sl-attorney__casi-row {
    display: grid;
    grid-template-columns: 200px 1fr 160px;   /* JSX exact */
    gap: 32px;
    padding: 28px 0;
    border-bottom: 1px solid var(--border);
    align-items: baseline;
    transition: transform var(--dur-base, 400ms) var(--ease-editorial, cubic-bezier(0.25, 1, 0.5, 1));
}

@media (hover: hover) {
    .sl-attorney__casi-row:hover {
        transform: translateX(8px);
    }
}

.sl-attorney__casi-id {
    font-family: var(--font-mono);
    font-size: 11px;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    color: var(--text-muted);
}

.sl-attorney__casi-desc {
    font-family: var(--font-display);
    font-size: 18px;
    line-height: 1.5;
    font-style: italic;
    color: var(--text);
}

.sl-attorney__casi-outcome {
    text-align: right;
    font-family: var(--font-display);
    font-size: 16px;
    color: var(--accent);
    font-weight: 400;
}

@media (max-width: 1023px) {
    .sl-attorney__casi-row {
        grid-template-columns: 1fr;
        gap: 8px;
    }
    .sl-attorney__casi-outcome {
        text-align: left;
    }
}
```

### 1.3 — Verifica markup attorney attuale (forse manca .sl-attorney__hero-grid)

```bash
grep -B 2 -A 10 'sl-attorney__hero' wp-content/themes/saltelli/single-avvocato.php | head -30
```

Se il template emette già `<div class="sl-attorney__hero-grid">`, ok. Altrimenti:

```php
<header class="sl-attorney__hero">
    <div class="sl-attorney__hero-inner">
        <div class="sl-attorney__hero-grid">
            
            <div class="sl-attorney__portrait <?php echo has_post_thumbnail() ? '' : 'sl-attorney__portrait--placeholder'; ?>">
                <?php if (has_post_thumbnail()): ?>
                    <?php the_post_thumbnail('large', ['loading' => 'eager']); ?>
                <?php else: ?>
                    <div class="sl-mono" style="position:absolute; top:16px; left:16px;">Plate</div>
                    <div class="sl-mono">Ritratto · 3:4</div>
                    <div style="font-family:var(--font-display); font-style:italic; font-size:80px;">
                        <?php echo esc_html(implode('', array_map(function($w){ return $w[0] ?? ''; }, explode(' ', get_the_title())))); ?>
                    </div>
                    <div class="sl-mono">Placeholder editoriale</div>
                <?php endif; ?>
            </div>
            
            <div class="sl-attorney__hero-text">
                <div>
                    <div class="sl-mono sl-attorney__role">§ Avvocato · <?php echo esc_html(get_post_meta(get_the_ID(), 'ruolo', true) ?: 'Partner'); ?></div>
                    <h1 class="sl-attorney__name">
                        <?php
                        $title = get_the_title();
                        $parts = explode(' ', $title, 2);
                        echo esc_html($parts[0]);
                        if (isset($parts[1])): ?>
                            <br><em><?php echo esc_html($parts[1]); ?>.</em>
                        <?php endif; ?>
                    </h1>
                    <div class="sl-attorney__lede"><?php echo esc_html(get_post_meta(get_the_ID(), 'ruolo', true)); ?></div>
                </div>
                
                <ul class="sl-attorney__specs">
                    <?php
                    $specs = get_field('specializzazioni', get_the_ID()) ?: explode(',', get_post_meta(get_the_ID(), 'specializzazioni', true));
                    if (empty($specs)) {
                        $specs = ['Diritto tributario', 'Cartelle esattoriali', 'Contenzioso fiscale'];
                    }
                    foreach ($specs as $spec):
                        $spec = trim(is_string($spec) ? $spec : '');
                        if ($spec): ?>
                            <li><?php echo esc_html($spec); ?></li>
                        <?php endif;
                    endforeach; ?>
                </ul>
            </div>
            
        </div>
    </div>
</header>
```

### 1.4 — Smoke verify

```bash
docker compose run --rm wpcli cache flush

for SLUG in emiliano-saltelli fabiana-saltelli antonia-battista stefano-gaetano-tedesco; do
    HTML=$(curl -s "http://localhost:8080/avvocati/$SLUG/?_=v27t1" -m 8)
    echo "─── /avvocati/$SLUG/ ───"
    echo "  hero-grid presente: $(echo "$HTML" | grep -c 'sl-attorney__hero-grid')"
    echo "  portrait wrapper:    $(echo "$HTML" | grep -c 'sl-attorney__portrait\b')"
    echo "  hero-text wrapper:   $(echo "$HTML" | grep -c 'sl-attorney__hero-text')"
    echo "  specs list:          $(echo "$HTML" | grep -c 'sl-attorney__specs')"
done
```

Atteso: tutti i 4 lawyer = 1 per ogni class.

### 1.5 — Commit

```bash
git add -A
git commit -m "feat(v0.27.0 task1): attorney CSS pixel-perfect — hero 1fr/1fr + body 240/1fr sticky + casi 3-col"
```

---

## TASK 2 — Tier-1 CSS adjustments (~20 min)

### 2.1 — Specifiche dal JSX saltelli-s2-practice-tier1.jsx

Il tier-1 attuale ha `.sl-tier1__*` markup OK. Il visual gap è probabilmente nel hero asimmetrico + body editorial.

Verifica:

```bash
grep -B 2 -A 6 'gridTemplateColumns\|maxWidth.*margin' .claude/knowledge/design/sessione-2/saltelli-s2-practice-tier1.jsx | head -50
```

CSS adjustments:

```css
/* ═══════════════════════════════════════════════════════════════
   v0.27.0 TASK 2 — TIER-1 visual refinement
   ═══════════════════════════════════════════════════════════════ */

.sl-tier1__hero {
    max-width: 1440px;
    margin-inline: auto;
    padding: 96px clamp(24px, 5vw, 96px) 64px;
}

@media (min-width: 1024px) {
    .sl-tier1__hero {
        display: grid;
        grid-template-columns: 8fr 4fr;   /* JSX asymmetric */
        gap: 64px;
        align-items: end;
    }
}

.sl-tier1__h1 {
    font-family: var(--font-display);
    font-size: clamp(64px, 7vw, 120px);
    line-height: 0.98;
    letter-spacing: -0.025em;
    font-weight: 400;
    margin: 0 0 32px;
    color: var(--primary);
}

.sl-tier1__capsule {
    max-width: 1440px;
    margin: 0 auto;
    padding: 64px clamp(24px, 5vw, 96px);
    border-block: 1px solid var(--border);
}

.sl-tier1__capsule-text {
    font-family: var(--font-display);
    font-size: clamp(22px, 2vw, 28px);
    font-style: italic;
    line-height: 1.5;
    color: var(--primary);
    max-width: 60ch;
    margin: 0;
}

.sl-tier1__lawyer-card {
    display: grid;
    grid-template-columns: 80px 1fr;
    gap: 24px;
    align-items: center;
    padding: 32px;
    background: var(--surface);
    border: 1px solid var(--border);
    text-decoration: none;
    transition: transform var(--dur-base, 400ms) var(--ease-editorial, cubic-bezier(0.25, 1, 0.5, 1));
}

@media (hover: hover) {
    .sl-tier1__lawyer-card:hover {
        transform: translateY(-2px);
    }
}

/* Body editorial drop-cap */
.sl-tier1__body > p:first-of-type::first-letter,
.sl-page__prose > p:first-of-type::first-letter {
    font-family: var(--font-display);
    font-size: 84px;
    line-height: 0.85;
    float: left;
    margin: 8px 16px 0 0;
    color: var(--primary);
    font-weight: 400;
}

/* Cluster H2 deep cluster (v0.25.0 cluster) */
.sl-tier1__cluster-h2,
.sl-tier1__body h2 {
    font-family: var(--font-display);
    font-size: clamp(28px, 3vw, 36px);
    line-height: 1.15;
    letter-spacing: -0.015em;
    margin-block: 80px 24px;
    max-width: 24ch;
    color: var(--primary);
}

/* CASI 3-col grid */
.sl-tier1__cases-grid {
    display: grid;
    gap: 32px;
}

@media (min-width: 1024px) {
    .sl-tier1__cases-grid {
        grid-template-columns: repeat(3, 1fr);   /* JSX exact */
    }
}

.sl-tier1__case-card {
    border-top: 1px solid var(--accent);
    padding-top: 24px;
}
```

### 2.2 — Smoke verify

```bash
for SLUG in diritto-tributario diritto-del-lavoro diritto-di-famiglia-lgbtq; do
    HTML=$(curl -s "http://localhost:8080/competenze/$SLUG/?_=v27t2" -m 8)
    echo "─── /competenze/$SLUG/ ───"
    echo "  hero asym (live):       (verifica visual)"
    echo "  capsule italic:          $(echo "$HTML" | grep -c 'sl-tier1__capsule-text')"
    echo "  lawyer card:             $(echo "$HTML" | grep -c 'sl-tier1__lawyer-card')"
    echo "  cluster H2 count:        $(echo "$HTML" | grep -c 'sl-tier1__cluster-h2')"
    echo "  cases-grid 3-col:        $(echo "$HTML" | grep -c 'sl-tier1__cases-grid\|sl-tier1__cases')"
done
```

### 2.3 — Commit

```bash
git add -A
git commit -m "feat(v0.27.0 task2): tier-1 CSS pixel-perfect — hero asym 8/4 + capsule italic + cases 3-col"
```

---

## TASK 3 — Chi-siamo CSS adjustments (~15 min)

### 3.1 — Specifiche dal JSX saltelli-s2-chi-siamo.jsx

Chi-siamo JSX ha 6 sezioni numerate. Live ha la struttura ma probabilmente layout 1-col stack invece di asymmetric.

CSS adjustments:

```css
/* ═══════════════════════════════════════════════════════════════
   v0.27.0 TASK 3 — CHI-SIAMO visual refinement
   ═══════════════════════════════════════════════════════════════ */

.sl-chi-siamo__hero {
    max-width: 1440px;
    margin: 0 auto;
    padding: clamp(96px, 12vw, 192px) clamp(24px, 5vw, 96px) 96px;
}

.sl-chi-siamo__h1 {
    font-family: var(--font-display);
    font-size: clamp(64px, 9vw, 144px);
    line-height: 0.92;
    letter-spacing: -0.03em;
    font-weight: 400;
    margin: 0;
    color: var(--primary);
    max-width: 12ch;
}

.sl-chi-siamo__h1 em {
    font-style: italic;
    color: var(--text-muted);
}

/* Lede asymmetric 3fr/9fr */
.sl-chi-siamo__lede-section {
    max-width: 1440px;
    margin: 0 auto;
    padding: 0 clamp(24px, 5vw, 96px) 96px;
}

@media (min-width: 1024px) {
    .sl-chi-siamo__lede-section {
        display: grid;
        grid-template-columns: 3fr 9fr;
        gap: 64px;
    }
}

.sl-chi-siamo__dropcap {
    font-family: var(--font-display);
    font-size: 84px;
    line-height: 0.85;
    float: left;
    margin: 8px 16px 0 0;
    color: var(--primary);
}

.sl-chi-siamo__lede {
    font-size: 19px;
    line-height: 1.75;
    color: var(--text);
    max-width: 60ch;
}

/* Plate I facciata — full width image placeholder */
.sl-chi-siamo__plate {
    max-width: 1440px;
    margin: 0 auto;
    padding: 0 clamp(24px, 5vw, 96px) 128px;
}

.sl-chi-siamo__plate-frame {
    aspect-ratio: 1440 / 560;
    background: linear-gradient(135deg, color-mix(in srgb, var(--text-muted) 75%, var(--primary)) 0%, var(--primary) 100%);
    border: 1px solid var(--border);
    position: relative;
    overflow: hidden;
}

/* § 02 — 1999 founding (asym 3fr / 7fr / 2fr) */
.sl-chi-siamo__founding {
    max-width: 1440px;
    margin: 0 auto;
    padding: 0 clamp(24px, 5vw, 96px) 128px;
}

@media (min-width: 1024px) {
    .sl-chi-siamo__founding {
        display: grid;
        grid-template-columns: 3fr 7fr 2fr;
        gap: 64px;
    }
}

/* § 03 Team — 4 lawyer card grid */
.sl-chi-siamo__team {
    max-width: 1440px;
    margin: 0 auto;
    padding: 0 clamp(24px, 5vw, 96px) 128px;
}

.sl-chi-siamo__team-grid {
    display: grid;
    gap: 32px;
}

@media (min-width: 768px) {
    .sl-chi-siamo__team-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1024px) {
    .sl-chi-siamo__team-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

.sl-chi-siamo__team-photo {
    aspect-ratio: 4 / 5;
    overflow: hidden;
    background: var(--surface);
    margin-bottom: 16px;
    filter: grayscale(1);
    transition: filter 600ms var(--ease-editorial);
}

@media (hover: hover) {
    .sl-chi-siamo__team-card:hover .sl-chi-siamo__team-photo {
        filter: grayscale(0);
    }
}

/* § 04 Principi — surface bg + 3-col */
.sl-chi-siamo__principi {
    background: var(--surface);
    padding: 128px clamp(24px, 5vw, 96px);
}

.sl-chi-siamo__principi-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: grid;
    gap: 48px;
    max-width: 1440px;
    margin-inline: auto;
}

@media (min-width: 1024px) {
    .sl-chi-siamo__principi-list {
        grid-template-columns: repeat(3, 1fr);
        gap: 64px;
    }
}

.sl-chi-siamo__principi-item {
    border-top: 1px solid var(--accent);
    padding-top: 24px;
}

/* § 05 Timeline (cronologia) */
.sl-chi-siamo__timeline {
    max-width: 1440px;
    margin: 0 auto;
    padding: 128px clamp(24px, 5vw, 96px);
}

.sl-chi-siamo__timeline-row {
    display: grid;
    grid-template-columns: 100px 1fr;
    gap: 32px;
    padding-block: 24px;
    border-bottom: 1px solid var(--border);
}

/* § 06 CTA finale */
.sl-chi-siamo__cta {
    max-width: 1440px;
    margin: 0 auto;
    padding: 96px clamp(24px, 5vw, 96px) 160px;
}
```

### 3.2 — Smoke verify

```bash
HTML=$(curl -s "http://localhost:8080/chi-siamo/?_=v27t3" -m 8)
echo "─── /chi-siamo/ ───"
echo "  hero h1:                $(echo "$HTML" | grep -c 'sl-chi-siamo__h1')"
echo "  founding 3-col grid:     $(echo "$HTML" | grep -c 'sl-chi-siamo__founding')"
echo "  team 4-card grid:        $(echo "$HTML" | grep -c 'sl-chi-siamo__team-grid')"
echo "  principi 3-col:          $(echo "$HTML" | grep -c 'sl-chi-siamo__principi-list')"
echo "  timeline:                $(echo "$HTML" | grep -c 'sl-chi-siamo__timeline')"
```

### 3.3 — Commit

```bash
git add -A
git commit -m "feat(v0.27.0 task3): chi-siamo CSS pixel-perfect — hero h1 144px + founding 3/7/2 + team 4-col + principi"
```

---

## TASK 4 — Bump + smoke + deploy + report finale (~10 min)

```bash
# Bump version
sed -i.bak 's/Version: [0-9.]\+.*/Version: 0.27.0-beta-visual-pixel-perfect/' wp-content/themes/saltelli/style.css
sed -i.bak "s/SALTELLI_THEME_VERSION', '[^']*'/SALTELLI_THEME_VERSION', '0.27.0-beta-visual-pixel-perfect'/" wp-content/themes/saltelli/functions.php
rm -f wp-content/themes/saltelli/{style.css,functions.php}.bak

# Cache flush local
docker compose run --rm wpcli cache flush

# Final commit
git add -A
git commit -m "feat(v0.27.0): visual pixel-perfect — attorney + tier-1 + chi-siamo CSS layout JSX-faithful"
git push origin main

# Deploy droplet
rsync -avz wp-content/themes/saltelli/ deploy@178.62.207.50:/var/www/saltelli/htdocs/wp-content/themes/saltelli/
ssh deploy@178.62.207.50 "
    sudo -u www-data wp cache flush --path=/var/www/saltelli/htdocs
    sudo -u www-data wp transient delete --all --path=/var/www/saltelli/htdocs
"

# Smoke test 7 URL critical
echo ""
echo "═══ SMOKE LIVE v0.27.0 ═══"
for URL in /chi-siamo/ /avvocati/emiliano-saltelli/ /avvocati/fabiana-saltelli/ /avvocati/antonia-battista/ /avvocati/stefano-gaetano-tedesco/ /competenze/diritto-tributario/ /competenze/diritto-del-lavoro/ /competenze/diritto-di-famiglia-lgbtq/; do
    HTTP=$(curl -sL -o /dev/null -w "%{http_code}" "https://staging.studiolegalesaltelli.it$URL?_=v27" -m 10)
    echo "  $URL → HTTP $HTTP"
done
```

### 4.1 — Report finale

`.claude/knowledge/design/sessione-2/v0.27.0-VISUAL-PIXEL-PERFECT.md`:

```markdown
# v0.27.0 Visual Pixel-Perfect (CSS Refactor)
## Score: 3/3 task PASS

## Per task
- T1 Attorney CSS hero 1fr/1fr + body 240/1fr sticky + casi 3-col: ✓
- T2 Tier-1 CSS hero 8/4 + capsule italic + cases 3-col: ✓
- T3 Chi-siamo CSS hero h1 144px + founding 3/7/2 + team 4-col + principi: ✓

## Visual fidelity post-fix
- Attorney 4 lawyer:    visual gap CHIUSO (hero 2-col, sticky sidebar pulita)
- Tier-1 3 page:        visual asym applicato + capsule italic
- Chi-siamo:            6 sezioni numerate visualmente fedele JSX

## Schema preserved (v0.26.0)
- Person/Attorney × 4 lawyer ✓
- FAQPage /costi/ ✓
- priceRange €800-€3500 ✓

## Next
GO walkthrough visuale finale Duccio
o GO v1.0.0 production cut

Tempo: ~75 min sequential.
```

Quando finito segnala "v0.27.0 deployed. Visual pixel-perfect applied."

---

## 🆘 Se incontri imprevisti

```
- Markup PHP non emette .sl-attorney__hero-grid → aggiungi wrapper PHP
- Class .sl-attorney__sticky già fixed in CSS → !important strategico per override
- Drop-cap su .sl-attorney__bio non visibile → controlla nesting (.sl-attorney__bio-prose > p)
- Layout grid mobile rotto → verifica @media (max-width: 1023px) stack rules
- Schema JSON-LD parse error → controlla inc/seo/person-schema-lawyer.php
```

Tempo totale: ~75-90 min sequential.

Buon lavoro. Quando finito, l'orchestrator esegue Chrome MCP visual side-by-side JSX vs Live per verifica fidelity finale.
