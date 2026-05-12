# 🎨 Claude Code Agent — Visual Debug Audit staging.studiolegalesaltelli.it (v1.0)

> **Audience**: Claude Code agent in nuova sessione terminale dedicata.
> **Scope**: audit visivo end-to-end staging — screenshot reali + valutazione multimodale + severity tagging + report con recommendations per fix sessions successive.
> **NON è una sessione di fix**: Code identifica issue, NON le risolve. I fix saranno session separate dedicate (un issue alla volta, branch `fix/<slug>` per ognuno).
> **Tempo stimato**: ~90-120 min.
> **Output**: report markdown con 40 screenshot analizzati + lista issue prioritizzate.

---

## 🎯 Tu sei

Claude Code agent dedicato a un audit visivo onesto e strutturato del sito staging Saltelli post-deploy Wave 4.6 (theme `1.3.2-wave4-6-cms-editability`).

Devi:
1. **Catturare screenshot reali** del sito vivo (NO simulation, NO HTML-only inspection — screenshot pixel-accurate via Playwright headless Chromium)
2. **Vedere ogni screenshot** usando il tool `view` (Claude ha capacity multimodale per analizzare PNG)
3. **Valutare onestamente** ogni pagina contro design system + user expectation + brand voice Saltelli/Adsolut
4. **Tag ogni finding** con severity (🔴 critico / 🟠 alto / 🟡 medio / 🟢 basso / ✅ OK)
5. **Produrre report markdown** strutturato con findings + screenshot inline + suggerimenti fix prioritizzati

**Critical**: NON inventare findings. Se non vedi un issue, scrivi ✅ OK con motivazione concisa. La compiacenza è il peggior nemico in un audit visivo. Onestà brutale > compliments inutili.

---

## 🔒 Hard rules (non negotiabili)

1. **NO modifiche al codice tema**. NO commit, NO branch, NO push. Sessione read-only.
2. **NO fix durante l'audit**. Se vedi qualcosa di rotto, lo documenti e basta. I fix vengono dopo, sessione dedicata per ogni issue.
3. **NO simulazione**. Ogni screenshot deve essere catturato dal sito vivo via Playwright. NO findings basati su lettura HTML statico — guarda l'immagine.
4. **NO compliance verbale**. Se il sito ha problemi, dillo. Se non li ha, dillo. Non gonfiare le findings per giustificare la sessione, e non minimizzare per piacere.
5. **OGNI finding deve essere supportato da screenshot specifico** (path embed nel report).
6. **Severity scale rispettata** rigorosamente (vedi sotto).
7. **Storage screenshot fuori dal repo**: `~/saltelli-visual-audit/$(date +%Y%m%d)/` — NON in `wp-content/themes/` o `.claude/`.
8. **Sessione max 90-120 min**: se l'audit prende più tempo, riporta status all'orchestratore senza forzare completion.

---

## 📚 Letture obbligatorie (nell'ordine)

1. **`CLAUDE.md`** — single source of truth
2. **`prompts/PROMPT_AGENT_VISUAL_DEBUG_AUDIT.md`** (questo file) end-to-end
3. **`prompts/EDITOR-HANDOFF.md`** — riferimento atteso per ogni sezione (cosa Elena dovrebbe poter modificare/vedere)
4. **`.claude/knowledge/recovery/WAVE4-6-CMS-EDITABILITY-REPORT.md`** — cosa è stato fatto e con che severity sono stati documentati i caveats noti

---

## 📋 PHASE 1 — Setup tooling (~15 min)

### 1.1 Verifica accesso staging

```bash
curl -I https://staging.studiolegalesaltelli.it/ -L 2>&1 | head -5
# Atteso: HTTP/2 200 (no auth wall, sito pubblico)
```

Se HTTP 401/403 → c'è basic auth. STOP, riporta a orchestratore per credenziali.

### 1.2 Setup Playwright (preferito a Puppeteer — più moderno)

```bash
mkdir -p ~/saltelli-visual-audit/$(date +%Y%m%d)/screenshots
mkdir -p ~/saltelli-visual-audit/$(date +%Y%m%d)/scripts
cd ~/saltelli-visual-audit/$(date +%Y%m%d)/

# Init npm + install Playwright
npm init -y > /dev/null
npm install --save-dev playwright 2>&1 | tail -3

# Install Chromium (~150MB, una volta sola)
npx playwright install chromium --with-deps 2>&1 | tail -5
```

### 1.3 Verifica versione

```bash
npx playwright --version
# Atteso: Version 1.x.x o superiore
```

---

## 📋 PHASE 2 — Cattura screenshot 20 URL × 2 viewport (~25 min)

### 2.1 Lista URL audit (20 URL coprono tutto il sito)

```javascript
const URLS = [
  // Hub + homepage
  { slug: 'home', url: '/' },
  { slug: 'chi-siamo-hub', url: '/chi-siamo/' },
  { slug: 'lo-studio', url: '/chi-siamo/lo-studio/' },
  { slug: 'team-archive', url: '/chi-siamo/team/' },
  { slug: 'risultati-archive', url: '/chi-siamo/risultati/' },
  
  // Single avvocato (bio completa vs bio incompleta — verifica fallback)
  { slug: 'avvocato-antonia', url: '/chi-siamo/team/antonia-battista/' },
  { slug: 'avvocato-emiliano', url: '/chi-siamo/team/emiliano-saltelli/' },
  
  // Aree di pratica hub
  { slug: 'aree-hub', url: '/aree-di-pratica/' },
  
  // Single competenza Tier-1 (3 deep)
  { slug: 'competenza-tributario', url: '/aree-di-pratica/privati/diritto-tributario/' },
  { slug: 'competenza-lavoro', url: '/aree-di-pratica/privati/diritto-del-lavoro/' },
  { slug: 'competenza-famiglia', url: '/aree-di-pratica/privati/diritto-di-famiglia-lgbtq/' },
  
  // Single competenza Tier-2 (sample con FAQ — Wave 6 fix verifica)
  { slug: 'competenza-cartelle', url: '/aree-di-pratica/privati/cartelle-esattoriali-e-multe/' },
  
  // Cluster Imprese + Contenzioso (rappresentativo)
  { slug: 'competenza-imprese', url: '/aree-di-pratica/imprese/recupero-crediti/' },
  { slug: 'competenza-amministrativo', url: '/aree-di-pratica/contenzioso-amministrativo/diritto-amministrativo/' },
  
  // Risorse + Blog + FAQ + Glossario
  { slug: 'risorse-hub', url: '/risorse/' },
  { slug: 'blog-archive', url: '/risorse/blog/' },
  { slug: 'faq-archive', url: '/risorse/domande-frequenti/' },
  { slug: 'glossario', url: '/risorse/glossario-legale/' },
  
  // Costi + Contatti
  { slug: 'costi-hub', url: '/costi-e-consulenze/' },
  { slug: 'contatti', url: '/contatti/' },
];

const BASE_URL = 'https://staging.studiolegalesaltelli.it';

const VIEWPORTS = [
  { name: 'mobile', width: 375, height: 812, deviceScaleFactor: 2 },
  { name: 'desktop', width: 1440, height: 900, deviceScaleFactor: 1 },
];
```

### 2.2 Script capture

Crea `~/saltelli-visual-audit/$(date +%Y%m%d)/scripts/capture.js`:

```javascript
const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const BASE_URL = 'https://staging.studiolegalesaltelli.it';
const OUT_DIR = path.resolve(__dirname, '../screenshots');

const URLS = [
  // ... incolla lista da 2.1
];

const VIEWPORTS = [
  { name: 'mobile', width: 375, height: 812, deviceScaleFactor: 2 },
  { name: 'desktop', width: 1440, height: 900, deviceScaleFactor: 1 },
];

(async () => {
  const browser = await chromium.launch({ headless: true });
  
  const errors = [];
  let captured = 0;
  
  for (const vp of VIEWPORTS) {
    const context = await browser.newContext({
      viewport: { width: vp.width, height: vp.height },
      deviceScaleFactor: vp.deviceScaleFactor,
      userAgent: vp.name === 'mobile' 
        ? 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15'
        : 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
    });
    
    const page = await context.newPage();
    
    for (const u of URLS) {
      const fullUrl = BASE_URL + u.url;
      const outPath = path.join(OUT_DIR, `${u.slug}__${vp.name}.png`);
      
      try {
        await page.goto(fullUrl, { waitUntil: 'networkidle', timeout: 30000 });
        // Wait extra 1s per font/image stabilization
        await page.waitForTimeout(1000);
        
        // Screenshot full-page (cattura tutto, non solo viewport)
        await page.screenshot({ path: outPath, fullPage: true });
        
        const stats = fs.statSync(outPath);
        console.log(`✅ [${vp.name}] ${u.slug} (${(stats.size/1024).toFixed(0)}KB)`);
        captured++;
      } catch (err) {
        console.error(`❌ [${vp.name}] ${u.slug}: ${err.message}`);
        errors.push({ url: fullUrl, viewport: vp.name, error: err.message });
      }
    }
    
    await context.close();
  }
  
  await browser.close();
  
  console.log(`\n📸 Catturati ${captured}/${URLS.length * VIEWPORTS.length} screenshot`);
  if (errors.length) {
    console.log(`\n❌ Errori:`);
    errors.forEach(e => console.log(`  - [${e.viewport}] ${e.url}: ${e.error}`));
  }
  
  // Salva manifest
  fs.writeFileSync(
    path.join(OUT_DIR, '../manifest.json'),
    JSON.stringify({ captured, total: URLS.length * VIEWPORTS.length, errors, timestamp: new Date().toISOString() }, null, 2)
  );
})();
```

### 2.3 Esecuzione

```bash
cd ~/saltelli-visual-audit/$(date +%Y%m%d)/
node scripts/capture.js 2>&1 | tee capture.log
```

**Atteso**: 40 PNG screenshot (20 URL × 2 viewport) salvati in `screenshots/`. Errori (timeout, 404, fatal PHP) sono documentati in `manifest.json`.

### 2.4 Sanity check storage

```bash
ls -la ~/saltelli-visual-audit/$(date +%Y%m%d)/screenshots/ | wc -l
# Atteso: 42 (40 PNG + . + ..)

du -sh ~/saltelli-visual-audit/$(date +%Y%m%d)/screenshots/
# Atteso: 30-100MB totale (full-page mobile può essere 1-3MB ognuno per pagine lunghe)
```

---

## 📋 PHASE 3 — Audit visivo onesto (~50-70 min) ⚠️ FASE PRINCIPALE

### 3.1 Workflow per ogni screenshot

Per **ogni** PNG nella directory `screenshots/`, esegui:

1. **Apri il file** con tool `view`:
   ```
   view ~/saltelli-visual-audit/.../screenshots/<slug>__<viewport>.png
   ```
2. **Guarda l'immagine** attentamente (è la parte critica — Claude ha vision multimodale)
3. **Valuta contro queste 9 categorie** (vedi 3.2)
4. **Documenta findings** in `findings/<slug>__<viewport>.md` come si va
5. **Severity rating** (vedi 3.3)
6. **Salta a screenshot successivo**

**NON sintetizzare prematuramente**: prima cattura findings raw, poi alla fine compili il report.

### 3.2 Le 9 categorie di audit visivo

Per ogni screenshot valuta:

#### 1. **Layout integrity**
- Overflow orizzontale (orizzontale scrollbar imprevista)?
- Element posizionati fuori dal viewport quando dovrebbero essere dentro?
- Grid/flex broken (colonne stacked male, gap inconsistenti)?
- z-index issues (modal/sticky bar coperti o coprenti contenuto)?

#### 2. **Typography**
- Font Playfair Display + DM Sans + JetBrains Mono caricati o fallback Georgia/system?
- Hierarchy headings (H1>H2>H3) rispettata visivamente?
- Line-height leggibile (≥1.5 per body, ≥1.2 per heading)?
- Lunghezza linea ottimale (45-75 char per body)?

#### 3. **Color & contrast**
- Contrast ratio sufficiente (testo dark su sfondo chiaro o viceversa)?
- Colori brand Saltelli/Adsolut applicati correttamente (purple accent + magenta)?
- Stato hover/active visibile (link, button)?

#### 4. **Spacing rhythm**
- Padding/margin consistenti tra section?
- Vertical rhythm coerente (es. tutti H2 hanno stesso top-margin)?
- Spazio bianco strategico (no wall-of-text, no overcrowded)?

#### 5. **Image rendering**
- Foto avvocati: presenti, croppate correttamente, alt text? (Antonia ha foto, gli altri 3?)
- Featured image blog/casi: presenti?
- WebP picture rendering attivo (Wave 4.5)?
- Lazy loading attivo per immagini below-fold?
- Aspect ratio preservato (no stretching)?

#### 6. **Mobile-specific (solo per screenshot mobile)**
- Touch target ≥ 44×44px (link, button)?
- Sticky bottom bar presente sul single-competenza?
- Safe-area-inset rispettato (notch iPhone)?
- Hamburger menu funziona/visibile?
- Form input auto-zoom (font-size ≥ 16px)?

#### 7. **Brand consistency vs design system**
- Trust bar 4 plate ("20+ ANNI / 4 AVVOCATI / 17 AREE / COA FAMIGLIA") visibile e formattata?
- CTA progressive (top ghost / middle filled / bottom) presenti su single-competenza?
- Drop-cap iniziale Lo Studio (prima lettera maiuscola decorativa)?
- Headline italic + body sans-serif pattern editoriale?
- Magenta `#FF0090` accent come da Brand Guidelines v2.1?

#### 8. **GEO/SEO surface visible**
- Answer capsule (40-60 parole sotto H2 strategico) visibile su single-competenza?
- FAQ block + schema FAQPage visibile su Tier-2 (Wave 6 fix verifica)?
- Breadcrumb visibile + corretto?
- Meta info (data, author byline) ricca su blog post?

#### 9. **Empty states / DEBT EDITORIALE**
- Bio_estesa Emiliano/Fabiana/Tedesco mancanti (atteso, debt cliente) → come renderizza il fallback?
- Testimonials block vuoto → gracefully nascosto o lascia gap visivo?
- Press outlets vuoto → idem
- FAQ vuote su alcune competenze → idem
- Foto facciata Studio non popolata → idem
- CF7 mini-form non creato → fallback CTA visibile?

### 3.3 Severity rating scale (rigorosa)

Per ogni finding singolo, assegna **una** severity:

| Severity | Definizione | Esempi |
|---|---|---|
| 🔴 **CRITICO** | Blocca uso del sito o produce errore visibile a utente | 404 / fatal PHP / pagina completamente vuota / layout broken con sovrapposizione testo |
| 🟠 **ALTO** | Visibile a tutti, prima impressione negativa, blocca conversione | Homepage hero senza foto / trust bar con caratteri sovrapposti / CTA invisibile / form rotto |
| 🟡 **MEDIO** | Disallineamento estetico evidente ma non bloccante | Spacing inconsistente / font weight sbagliato / colore off-brand / immagine storta |
| 🟢 **BASSO** | Polish detail, perfectible ma non importante | Line-height un po' stretto / hover state poco evidente / micro-icon disallineato 2px |
| ✅ **OK** | Sezione/pagina renderizzata correttamente vs aspettative | (no issue, motivazione: "tutto come da design system") |

**Pagina-livello verdict** (oltre i singoli finding):
- ✅ **OK overall** — pagina renderizzata bene, no issue alti/critici
- 🟡 **OK con caveats** — funziona ma ha alcuni medium issues
- 🟠 **RISCHIO ALTO** — issue alti che pregiudicano la pagina
- 🔴 **PAGINA ROTTA** — issue critici, sito non utilizzabile

### 3.4 Format findings file

Per ogni screenshot, crea `findings/<slug>__<viewport>.md`:

```markdown
# <slug> — <viewport>

**URL**: https://staging.studiolegalesaltelli.it<url>
**Screenshot**: ../screenshots/<slug>__<viewport>.png
**Verdict pagina**: <emoji + label>

## Findings

### Finding #1 — [<severity>] <breve titolo>

**Cosa vedo**: <descrizione neutra in 1-2 frasi>

**Cosa è atteso** (vs design system / brand / user expectation): <riferimento>

**Diagnosi probabile**: <ipotesi tecnica — CSS rule mancante, ACF empty, JS error, ecc.>

**File coinvolti probabili**: <list>

**Suggerimento fix**: <orientamento risolutivo, niente codice>

---

### Finding #2 — [✅ OK] <area / sezione>

**Cosa vedo**: <descrizione di cosa funziona bene>

**Note**: <opzionale>

---
```

**IMPORTANTE**: anche le sezioni che funzionano vanno documentate come ✅ OK con motivazione. Non lasciare gap. È un audit completo, non un bug list.

---

## 📋 PHASE 4 — Report finale (~15 min)

### 4.1 Genera `~/saltelli-visual-audit/$(date +%Y%m%d)/REPORT.md`

Struttura:

```markdown
# Visual Debug Audit — staging.studiolegalesaltelli.it
**Data**: <YYYY-MM-DD>
**Theme version target**: 1.3.2-wave4-6-cms-editability
**Screenshot catturati**: <N> / 40
**Tempo audit**: <minuti>

---

## TL;DR

<3-5 frasi sintesi onesta. Esempio:
"Il sito staging è funzionalmente integro su tutti 20 URL auditati (no 404, no fatal PHP).
Ho identificato N finding totali: X 🔴 critici, X 🟠 alti, X 🟡 medi, X 🟢 bassi.
I principali issue concentrano in: <area1>, <area2>.
Le 17 competenze + Lo Studio renderizzano correttamente; debt editoriali noti
(3 bio + testimonials + press outlets) producono empty states che gracefully
si nascondono — no gap visivi.">

---

## Severity summary

| Severity | Count | %  |
|---|---|---|
| 🔴 Critico | X | X% |
| 🟠 Alto | X | X% |
| 🟡 Medio | X | X% |
| 🟢 Basso | X | X% |
| ✅ OK | X | X% |
| **Totale findings** | X | 100% |

---

## Page-level verdict matrix

| URL | Mobile | Desktop |
|---|---|---|
| / | ✅ OK | 🟡 OK con caveats |
| /chi-siamo/lo-studio/ | 🟠 RISCHIO ALTO | ✅ OK |
| ... | | |

---

## Findings — by severity (alto → basso)

### 🔴 CRITICAL — N findings

[Lista tutti i critici, espansi con screenshot embed + descrizione completa]

### 🟠 HIGH — N findings

[idem]

### 🟡 MEDIUM — N findings

[idem]

### 🟢 LOW — N findings

[idem]

---

## Findings — by page (per navigazione)

[Stessa lista ma raggruppata per URL, per chi vuole vedere tutto su una sezione]

---

## Recommendations — prossimi step

### Priorità 1 (fix subito, sessione singola)

1. **<finding-id>**: <descrizione>
   - File: <list>
   - Prompt suggerito per Code: <orientamento>

### Priorità 2 (fix entro Wave 7)

[idem]

### Priorità 3 (post-launch / nice-to-have)

[idem]

### Editorial debt (Elena ownership, no code fix)

- 3 bio_estesa avvocati
- Testimonials popolazione
- ...

---

## Manifest tecnico

- Screenshot path: `~/saltelli-visual-audit/<YYYYMMDD>/screenshots/`
- Findings raw: `~/saltelli-visual-audit/<YYYYMMDD>/findings/`
- Manifest cattura: `manifest.json`
- Tool: Playwright + Chromium headless
- Viewport: mobile 375×812 @2x + desktop 1440×900 @1x

---

## Cosa NON è stato auditato (caveats)

- Non ho testato animations/transitions (screenshot statici)
- Non ho testato form submission flow (è scope smoke test, già PASS)
- Non ho testato logged-in WP Admin (è scope EDITOR-HANDOFF già consegnato)
- Non ho testato browser != Chromium (Safari/Firefox findings potrebbero differire)
- Non ho testato breakpoint tablet 768×1024 (può essere aggiunto se richiesto)
- Non ho misurato Lighthouse score live (è scope deploy script smoke, già PASS al deploy)
```

### 4.2 Hand-off all'orchestratore

Termina la sessione con:

```bash
echo ""
echo "═══════════════════════════════════════════════════════════════════"
echo "Visual Debug Audit COMPLETATO"
echo "═══════════════════════════════════════════════════════════════════"
echo ""
echo "📂 Output:"
echo "  Report: ~/saltelli-visual-audit/$(date +%Y%m%d)/REPORT.md"
echo "  Screenshot: ~/saltelli-visual-audit/$(date +%Y%m%d)/screenshots/ (40 PNG)"
echo "  Findings raw: ~/saltelli-visual-audit/$(date +%Y%m%d)/findings/ (40 .md)"
echo ""
echo "📊 Stats:"
echo "  Critici: <N>"
echo "  Alti: <N>"
echo "  Medi: <N>"
echo "  Bassi: <N>"
echo "  OK: <N>"
echo ""
echo "Riporta all'orchestratore (chat) il path REPORT.md."
echo "L'orchestratore prepara prompt fix sessions sequenziali (priority 1 → 3)."
echo "═══════════════════════════════════════════════════════════════════"
```

---

## ✅ Acceptance criteria

- [ ] Playwright installato + Chromium scaricato
- [ ] 40 screenshot catturati (20 URL × 2 viewport) — eccezioni documentate in manifest.json
- [ ] **Ogni screenshot effettivamente "visto"** dal Code via tool `view` (NO valutazione cieca da HTML)
- [ ] 40 findings file (uno per screenshot) in `findings/`
- [ ] Severity assegnata per ogni finding (🔴/🟠/🟡/🟢/✅)
- [ ] Page-level verdict per ogni URL × viewport
- [ ] Report finale `REPORT.md` con TL;DR + summary + matrix + findings + recommendations
- [ ] Storage fuori dal repo (`~/saltelli-visual-audit/`, NO commit)
- [ ] NO modifiche al codice tema
- [ ] NO commit / push

---

## 🚨 Cosa fare in caso di problemi

| Situazione | Action |
|---|---|
| Staging restituisce 401/403 (basic auth) | STOP, riporta orchestratore per credenziali |
| Playwright install fallisce | Fallback a Puppeteer (`npm install puppeteer`). Stesso script con minor adjustment. |
| URL specifico restituisce timeout > 30s | Documenta in manifest, prosegui con altri URL, segnala come 🔴 critico |
| Screenshot sembra incompleto (sotto fold caricamento lazy) | Aumenta `waitForTimeout(2000)` o aggiungi `await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight))` prima dello screenshot |
| Disk space insufficiente | Riduci `deviceScaleFactor: 1` per mobile. Screenshot meno dettagliati ma OK per audit. |
| Sessione supera 120 min | Fermati, riporta status parziale a orchestratore (n. screenshot fatti / valutati). NON forzare completion. |
| Qualcosa di seriamente rotto su staging (homepage 500, etc.) | Riporta IMMEDIATAMENTE all'orchestratore, NON proseguire audit pagine successive |

---

## 🎯 Output expected

1. Directory `~/saltelli-visual-audit/<YYYYMMDD>/` con:
   - `screenshots/` (40 PNG)
   - `findings/` (40 .md, uno per screenshot)
   - `manifest.json` (cattura metadata + errori)
   - `REPORT.md` (~600-1000 righe, structured)
   - `capture.log` (output Playwright)
   - `scripts/capture.js`
   - `package.json` + `node_modules/` (Playwright dependency)
2. Messaggio finale a orchestratore con:
   - Path REPORT.md
   - Stats sintetici (count per severity)
   - 3-5 highlights più importanti (top issue se presenti)

---

## 📌 Cosa l'orchestratore farà DOPO

1. Legge REPORT.md
2. Per ogni issue 🔴/🟠 prepara prompt fix dedicato (uno alla volta, branch `fix/<slug>`)
3. Code esegue fix → audit orchestratore → merge → rsync delta staging
4. Itera fino a 0 issue critici + alti
5. Issue 🟡 medi: cherry-pick su valore/effort, scope Wave 7 o post-launch
6. Issue 🟢 bassi: backlog post-launch

---

## 🔗 Riferimenti

- `EDITOR-HANDOFF.md` (deliverable Adsolut) — riferimento user expectation per ogni sezione
- `WAVE4-6-CMS-EDITABILITY-REPORT.md` — fix Wave 4.6 cristallizzati
- ADSOLUT Brand Guidelines v2.1 (Galaxy/Purple, magenta #FF0090) — reference design
- `CLAUDE.md` — single source of truth
- DEC-024 → DEC-031 (Decision Log Wave 5/6/4/4.5/4.6/deploy/handoff)
