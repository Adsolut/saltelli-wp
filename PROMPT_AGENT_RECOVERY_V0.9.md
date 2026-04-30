# Prompt — Recovery Agent v0.9.0 (Comprehensive Bug Fix)

> **Per Claude Code in nuova sessione (o sessione corrente).** Apri `saltelli-wp/`, leggi questo file, eseguilo. Tempo previsto: 60-90 min. **Sequenza obbligata, NON parallelo.**
> **PRECEDENZA:** mini-fix avvocato (v0.8.1) completato. Ha risolto markup ma ha lasciato 6 bug visibili (3 pre-esistenti non testati prima + 1 regressione + 1 ancora aperto + 1 content quality).

---

## Tu sei

Il **Recovery Agent**. Il direttore d'orchestra (Claude in chat) ha eseguito Visual Walkthrough approfondito su v0.8.1 cercando attivamente bug e ne ha trovati **6 FAIL** sui template che il cliente vedrà domani:

```
SCORE WALKTHROUGH v0.8.1: 11 PASS · 0 WARN · 6 FAIL
```

Il tuo lavoro: **chiudere ognuno dei 6 FAIL con fix chirurgico**, verificare zero regressioni rispetto al patrimonio funzionante (tutti i 11 PASS preservati), produrre v0.9.0-beta-recovery.

---

## ⚠️ ATTENZIONE — Lezione dal mini-fix v0.8.1

Il mini-fix avvocato precedente **ha funzionato a livello markup** ma ha:
- **Introdotto regressione** su `/costi/` (FAIL #8)
- **Non risolto pienamente** il bug single-avvocato senza foto (`max-width: 480px` viene sovrascritto)
- **Non ha testato** archive avvocati / archive competenze / content tier-1 / header transition (4 bug pre-esistenti pubblici)

**Lezione operativa:** ogni fix CSS richiede:
1. **Verifica regressione** sui template pre-esistenti via curl + visual check
2. **Verifica specificità CSS** post-fix (un mio `max-width: 480px` può essere battuto da un `width: 100%` successivo)
3. **Smoke test esteso** — non solo i template che ho toccato, ma TUTTI i 9 template visibili al cliente

---

## Letture obbligatorie

1. `CLAUDE.md` — hard constraints
2. `.claude/knowledge/design/sessione-1/reports/visual-walkthrough/REPORT-v0.8.1.md` — diagnosi 6 FAIL
3. `wp-content/themes/saltelli/assets/css/sections.css` + `components.css` — CSS principali
4. `wp-content/themes/saltelli/single-avvocato.php` + `archive-avvocato.php` + `archive-competenza.php` + `page.php` + `single-competenza.php`

---

## Hard rules

| Rule | Reason |
|---|---|
| **Fix sequenziali, NON parallel.** Cache flush + curl test dopo OGNI fix | Come per Pain Points/Step E, regression early |
| **Dopo ogni fix, smoke test completo 6+ URL** non solo il template toccato | Mini-fix v0.8.1 ha rotto /costi/ perché non l'ha ritestato |
| **Verifica CSS effective via DevTools/javascript_exec** — non assumere che la regola scritta sia attiva | `max-width: 480px` può essere sovrascritto |
| **Mai sovrascrivere `_thumbnail_id` Emiliano (CPT 2660)** | Foto Step C.5 |
| **Mai sovrascrivere `bio_estesa` Step D** | Content Step D |
| **Design tokens NON si toccano** | Locked |
| **Prima di marcare un fix come "done", screenshot mentale** del comportamento atteso vs DOM | Validazione |

---

## Sequenza FAIL (in ordine di severità)

```
F1 — #15 — Archive /competenze/ headline scivola fuori (rotto pesante)
F2 — #14 — Archive /avvocati/ solo 2/4 lawyer visibili (rotto pesante)
F3 — #13 — Single-avvocato senza foto (still fail post mini-fix v0.8.1)
F4 — #8 — REGRESSIONE /costi/ layout (causata da mini-fix v0.8.1)
F5 — #17 — Header sticky transition lenta/incompleta
F6 — #16 — Content tier-1 H2 sub-section sovrapposti (content quality + CSS)
```

---

## F1 — Archive /competenze/ rotto (15 min)

### Sintomo
Su `/competenze/?_=test` desktop 1440, l'headline "Diciannove aree. *Tre presidiate in profondità.*" appare in colonna destra **molto stretta** e finisce fuori viewport. Lista 19 aree non visibile sopra il fold. Eyebrow "STUDIO · AREE DI PRATICA" abbandonato a sinistra.

### Diagnosi
```bash
curl -s "http://localhost:8080/competenze/?_=f1diag" | grep -oE '<header[^>]*sl-section-head[^>]*>|<div[^>]*sl-section-head[^>]*>' | head -3

# Verifica grid template di .sl-section-head e .sl-areas-archive
grep -B 1 -A 15 '\.sl-section-head\|\.sl-areas-archive' wp-content/themes/saltelli/assets/css/sections.css | head -60
```

### Fix probabile

Il problema è probabilmente: 
- `.sl-section-head` ha `display: grid; grid-template-columns: minmax(auto, 200px) 1fr` (eyebrow stretto + headline larga)
- MA quando l'headline contiene `<em>Tre presidiate in profondità.</em>` con tipo Playfair `clamp(40px, 5vw, 96px)`, il content NON si adatta e overflow

**Fix:**
```css
/* In sections.css, cerca .sl-areas-archive e .sl-section-head e correggi: */

.sl-areas-archive .sl-section-head,
.sl-areas .sl-section-head {
    display: grid;
    grid-template-columns: 1fr;  /* mobile-first single col */
    gap: 24px;
    margin-bottom: 64px;
}

@media (min-width: 1024px) {
    .sl-areas-archive .sl-section-head,
    .sl-areas .sl-section-head {
        grid-template-columns: 240px 1fr;
        gap: 64px;
        align-items: start;
    }
}

/* Headline title dell'archive */
.sl-areas__archive-title,
.sl-section-head h1,
.sl-section-head h2 {
    font-family: var(--font-display);
    font-size: clamp(40px, 5vw, 84px);   /* riduco da 96px max a 84px */
    line-height: 1.05;
    color: var(--primary);
    max-width: 18ch;                       /* forza wrap */
    margin: 0;
}
```

### Verify
```bash
docker compose run --rm wpcli cache flush
HTML=$(curl -s "http://localhost:8080/competenze/?_=f1verify")
echo "  Headline grid present: $(echo "$HTML" | grep -c "sl-section-head")"

# Smoke test che NON ha rotto altri 5 URL
for URL in "/" "/costi/" "/competenze/diritto-tributario/" "/avvocati/emiliano-saltelli/" "/avvocati/" "/tipo-area/privati/"; do
    HTTP=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:8080$URL?_=f1regression")
    printf "  %-40s HTTP %s\n" "$URL" "$HTTP"
done
```

Tutti devono dare 200.

---

## F2 — Archive /avvocati/ solo 2/4 lawyer (15 min)

### Sintomo
Su `/avvocati/` vedo solo **2 ritratti** (Emiliano grande + 1 placeholder grigio piccolo), mentre dovrebbero essere **4** (Emiliano + Fabiana + Antonia + Stefano).

### Diagnosi
```bash
# 1. Verifica WP-CLI che ci siano 4 CPT pubblicati
docker compose run --rm wpcli post list --post_type=avvocato --post_status=publish --format=csv

# 2. Verifica HTML emesso conta gli articoli/figure
HTML=$(curl -s "http://localhost:8080/avvocati/?_=f2")
echo "  <article> count: $(echo "$HTML" | grep -oE '<article[^>]*sl-attorney\|<article[^>]*sl-team' | wc -l)"
echo "  Names visible: $(echo "$HTML" | grep -oE 'Emiliano Saltelli\|Fabiana Saltelli\|Antonia Battista\|Stefano')"

# 3. Verifica layout grid
grep -B 1 -A 20 '\.sl-team--archive\|\.sl-archive-team\|archive-avvocato' wp-content/themes/saltelli/assets/css/sections.css | head -40
```

### Possibili cause
1. **Query WP**: `archive-avvocato.php` ha `posts_per_page` errato (test: dovrebbe essere ≥4 o `-1`)
2. **CSS layout**: grid 2x2 ma le righe successive sono nascoste da overflow o height fissa
3. **Markup wrapper**: il template emette i 4 lawyer ma gli ultimi 2 sono visivamente fuori container

### Fix
Diagnosi rapida via DOM:
```bash
docker compose run --rm wpcli eval "
\$query = new WP_Query(['post_type' => 'avvocato', 'post_status' => 'publish', 'posts_per_page' => -1]);
echo 'Total posts: ' . \$query->found_posts;
foreach (\$query->posts as \$p) echo PHP_EOL . '  - ' . \$p->post_name . ' (ID ' . \$p->ID . ')';
"
```

Se `Total posts: 4` ma in HTML del template ne vedi solo 2 → bug template (forse `posts_per_page` hardcoded a 2 o limit nel loop).

Se `Total posts: 4` e in HTML ne sono renderizzati 4 ma visivamente solo 2 → CSS che nasconde 2 lawyer (probabilmente `display: none` su `:nth-child(n+3)` o grid che pone in row 2 fuori container).

Fix CSS standard se layout asimmetrico è il problema:
```css
.archive .sl-team--archive,
.post-type-archive-avvocato .sl-team {
    display: grid;
    grid-template-columns: 1fr;
    gap: 64px 32px;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 clamp(24px, 5vw, 96px);
}

@media (min-width: 1024px) {
    .archive .sl-team--archive,
    .post-type-archive-avvocato .sl-team {
        grid-template-columns: repeat(2, 1fr);
    }
}

.post-type-archive-avvocato .sl-team__lawyer {
    /* Override eventuale offset/margin asimmetrico ereditato da homepage */
    grid-column: auto !important;
    margin-top: 0 !important;
}
```

---

## F3 — Single-avvocato senza foto STILL FAIL (15 min)

### Sintomo
Mini-fix v0.8.1 ha aggiunto `.sl-attorney__portrait { max-width: 480px }` ma il box gradient nei 3 lawyer senza foto rende ancora **a 600px+ width** sovrappondo lo sticky.

### Diagnosi via DOM
```bash
# Carica /avvocati/fabiana-saltelli/ e verifica computed style
# Da fare via Chrome DevTools manuale: orchestrator può aiutare con javascript_exec
```

Sul fronte agent, fai:
```bash
grep -n '\.sl-attorney__portrait' wp-content/themes/saltelli/assets/css/sections.css wp-content/themes/saltelli/assets/css/components.css | head -10
```

Verifica quante regole `.sl-attorney__portrait` esistono. Se sono >1, l'ultima vince. Se hanno `width: 100%` ovunque, il `max-width: 480px` viene battuto.

### Fix
**Selettore più specifico + `!important` strategicamente** (l'unico caso in cui `!important` è giustificato è override di regole pre-esistenti che non puoi rimuovere):

```css
/* In sections.css, fine file (sovrascrive ogni regola precedente) */
figure.sl-attorney__portrait {
    display: block !important;
    width: 100% !important;
    max-width: 480px !important;        /* 480px desktop */
    aspect-ratio: 3 / 4 !important;
    margin: 0 0 32px 0 !important;
    padding: 0 !important;
    position: relative !important;
    background: linear-gradient(135deg, #c8c5be 0%, #6e6c66 100%);
    overflow: hidden;
    border: 1px solid var(--border);
}

figure.sl-attorney__portrait img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center top;
    display: block;
}

figure.sl-attorney__portrait .sl-team__placeholder,
figure.sl-attorney__portrait .sl-attorney__placeholder {
    position: absolute;
    bottom: 16px;
    left: 16px;
    color: rgba(255, 255, 255, 0.78);
    font-family: var(--font-mono);
    font-size: 11px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

@media (max-width: 1023px) {
    figure.sl-attorney__portrait {
        max-width: 100% !important;
        margin-bottom: 24px !important;
    }
}
```

Tag selettore `figure.sl-attorney__portrait` ha specificità (0,1,1) vs (0,1,0) della classe sola → batte tutte le altre.

---

## F4 — REGRESSIONE /costi/ layout (10 min)

### Sintomo
Su `/costi/?_=f4` desktop 1440:
- Eyebrow "§ 01 — COME FUNZIONA" sta sinistra colonna stretta ✅
- Body content "Trenta minuti..." e h2 "La prima consulenza" sono in colonna destra MA la posizione sembra sbilanciata, con vuoto enorme tra sezioni

### Diagnosi
```bash
# Verifica computed style attuale via curl + grep
HTML=$(curl -s "http://localhost:8080/costi/?_=f4diag")
echo "  CSS rule for .sl-costi__section:"
grep -B 1 -A 20 '\.sl-costi__section' wp-content/themes/saltelli/assets/css/sections.css | head -40

# Confronta con l'asymmetric grid intended
echo ""
echo "  Layout intended: 240px (eyebrow) | 1fr (body)"
echo "  Layout actual:   200px (eyebrow) | 836px (body) — verificato in DOM check orchestrator"
```

Probabile causa: il mini-fix v0.8.1 ha aggiunto regole CSS che impactano `.sl-page` o `.sl-costi__intro/section` con margin/padding extra.

### Fix
Verifica e ripristina layout asimmetrico solido:
```css
/* In sections.css, cerca tutte le regole .sl-costi__* e sostituisci con: */

.sl-costi__intro {
    max-width: 720px;
    margin: 0 auto 96px;
    padding-inline: clamp(24px, 5vw, 96px);
}

.sl-costi__capsule {
    font-family: var(--font-display);
    font-size: clamp(20px, 2vw, 28px);
    font-style: italic;
    line-height: 1.55;
    color: var(--primary);
    margin: 0;
}

.sl-costi__section {
    max-width: 1100px;
    margin: 0 auto 64px;
    padding-inline: clamp(24px, 5vw, 96px);
    display: grid;
    grid-template-columns: 1fr;  /* mobile single col */
    gap: 16px;
}

@media (min-width: 1024px) {
    .sl-costi__section {
        grid-template-columns: 200px 1fr;
        gap: 80px;
        align-items: start;
    }
    .sl-costi__section > .sl-mono {
        grid-column: 1;
        text-align: right;
        position: sticky;
        top: 120px;
        padding-top: 12px;
    }
    .sl-costi__section > h2,
    .sl-costi__section > p,
    .sl-costi__section > ul,
    .sl-costi__section > details,
    .sl-costi__section > .sl-acc {
        grid-column: 2;
    }
}

.sl-costi__cta {
    max-width: 1100px;
    margin: 96px auto 0;
    padding: 64px clamp(24px, 5vw, 96px);
    background: var(--surface);
    text-align: center;
}
```

---

## F5 — Header sticky transition incompleta (10 min)

### Sintomo
Durante scroll su qualsiasi pagina, l'header rimane "semi-transparent" per più di 1 secondo (transition non termina).

### Diagnosi
```bash
grep -B 1 -A 15 '\.sl-header\b' wp-content/themes/saltelli/assets/css/sections.css | head -30

# Verifica JS che controlla state
grep -A 10 "sl-header.*scrolled\|data-scrolled" wp-content/themes/saltelli/assets/js/main.js | head -20
```

### Fix probabile

```css
/* In sections.css, sostituisci la regola .sl-header con: */
.sl-header {
    position: sticky;
    top: 0;
    z-index: 100;
    background: var(--background);
    transition: background 200ms ease, border-color 200ms ease, box-shadow 200ms ease;
    border-bottom: 1px solid transparent;
}

.sl-header[data-scrolled="true"] {
    border-bottom-color: var(--border);
    box-shadow: 0 1px 0 rgba(27, 43, 75, 0.04);
}
```

`transition: background` mirato (200ms invece di 600ms) + niente fade su altre proprietà = no flicker.

---

## F6 — Content tier-1 H2 sub-section sovrapposti (15 min — content + CSS)

### Sintomo
Nel body migrato del CPT competenza tier-1 (es. tributario), 3 h2 ravvicinati senza respiro: "Avvocato tributarista Napoli" + "Lo Studio Legale Saltelli a Napoli si occupa di..." + "Lo Studio Legale Saltelli di Napoli si mette dalla tua parte". Letti di fila sembrano paragrafi continui.

### Diagnosi
```bash
docker compose run --rm wpcli post get $(docker compose run --rm wpcli post list --post_type=competenza --name=diritto-tributario --field=ID 2>&1 | grep -oE '^[0-9]+' | head -1) --field=post_content 2>&1 | head -50
```

Verifica quanti h2 ci sono e con che spaziatura.

### Fix soluzione 1 — CSS (preferita, no DB change)

```css
/* In sections.css, scope su prose competenza */
.sl-competenza__prose h2,
.sl-competenza__body h2,
.entry-content h2,
.competenza__content h2 {
    font-family: var(--font-display);
    font-size: clamp(28px, 3.5vw, 44px);
    line-height: 1.15;
    color: var(--primary);
    margin-block: 80px 24px;       /* ↑ 80px spazio sopra (era ≈40) per dare RESPIRO */
    max-width: 24ch;
    text-transform: none;
}

.sl-competenza__prose h2 + h2,
.sl-competenza__prose h3 + h2 {
    margin-top: 32px;             /* se h2 segue subito un altro h2/h3, riduci spazio */
}
```

### Fix soluzione 2 — Pulizia DB (alternativa più aggressiva)

```bash
# Solo se soluzione CSS non basta. Script PHP che riduce h2 ridondanti.
# Fai prima il fix CSS, valuta visivamente, poi decide se serve anche questo.
```

Inizialmente prova solo soluzione 1. Verifica visivamente. Se non basta, escala a 2.

---

## TASK FINALE — Bump version + smoke test 10+ URL + walkthrough auto

```bash
sed -i.bak 's/Version: 0.8.1-beta-attorney-placeholder/Version: 0.9.0-beta-recovery/' wp-content/themes/saltelli/style.css
sed -i.bak "s/define('SALTELLI_THEME_VERSION', '0.8.1-beta-attorney-placeholder')/define('SALTELLI_THEME_VERSION', '0.9.0-beta-recovery')/" wp-content/themes/saltelli/functions.php
rm -f wp-content/themes/saltelli/{style.css,functions.php}.bak

docker compose run --rm wpcli cache flush
docker compose run --rm wpcli transient delete --all

# Smoke test esteso 10 URL
for URL in "/" "/costi/" "/competenze/" "/competenze/diritto-tributario/" "/competenze/recupero-crediti/" "/avvocati/" "/avvocati/emiliano-saltelli/" "/avvocati/fabiana-saltelli/" "/tipo-area/privati/" "/contatti/"; do
    HTTP=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:8080$URL?_=v090final")
    SIZE=$(curl -s "http://localhost:8080$URL?_=v090final" | wc -c | tr -d ' ')
    H1=$(curl -s "http://localhost:8080$URL?_=v090final" | grep -c "<h1")
    printf "  %-40s HTTP %s · %sb · %sH1\n" "$URL" "$HTTP" "$SIZE" "$H1"
done
```

Tutti devono dare HTTP 200 (eccetto eventuali) + 1 H1 + size > 30KB.

---

## Report finale

`.claude/knowledge/design/sessione-1/reports/recovery-v0.9.0/REPORT.md`:

1. ✅/❌ ciascuno dei 6 FAIL
2. Smoke test 10 URL post-fix (tabella)
3. Diagnosi precisa per ogni FAIL (cosa hai trovato, cosa hai cambiato)
4. Verifica regressione: lista 11 PASS preservati
5. Decisioni autonome
6. Tempo per fix
7. Eventuali blocker o issue residui (se ce ne sono, segnalali — NON committare)

Poi **fermati**. Il direttore d'orchestra eseguirà nuovo Visual Walkthrough 12-point completo prima di passare a Step F.

---

## Cosa fare se incontri un bug imprevisto

| Situazione | Azione |
|---|---|
| Un fix CSS non viene applicato (regola sovrascritta) | Aumenta specificità (tag.class invece di .class) o usa `!important` come ultima risorsa con commento `/* override pre-existing rule line N */` |
| Un fix introduce regressione su altro template | STOP. Documenta. Non procedere agli altri FAIL finché non lo capisci |
| Un fix richiede modifica template PHP | OK ma documenta esplicitamente il rischio |
| Trovi un 7° bug imprevisto | Segnala nel report, NON fixarlo se non blocker |

---

*v1.0 — Comprehensive Recovery dopo walkthrough orchestrator approfondito. Target: v0.9.0-beta-recovery con 17/17 PASS.*
