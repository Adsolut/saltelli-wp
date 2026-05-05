# PROMPT v0.23.0 — Sessione 2 Pixel-Perfect Final (Tier-1 + Costi + .sl-input)

> **Per Claude Code in nuova sessione (oppure stessa).** Apri `saltelli-wp/`, leggi questo file, eseguilo. Tempo: 60-75 min sequential / 35-40 min parallel multi-agent.
> **PRECEDENZA:** v0.22.1 deve essere completato (animation residue chiusi).

---

## 🎯 Tu sei

L'**Agente Sessione 2 Pixel-Perfect Final**. Audit Duccio post-v0.22.1 ha rilevato 3 gap reali tra JSX Sessione 2 approvati e implementation live:

```
1. /competenze/{tier1}/ — solo 40% match JSX (manca deep cluster + FAQ accordion)
   3 page tier-1: diritto-tributario, diritto-del-lavoro, diritto-di-famiglia-lgbtq
   
2. /costi/ — su template legacy generico, mai implementato Sessione 2
   JSX saltelli-s2-costi.jsx (577 righe, 27.8KB) NON applicato
   Il sito mostra .sl-page__prose generico
   
3. CSS .sl-input + .sl-link styling editoriale missing/incompleto
   Form fields underline-only style + link bronze hover
```

**LAYOUT JSX = SACRO.** I 2 JSX sono stati approvati dall'orchestrator. Implementare al pixel come scritto, non re-design.

---

## 📚 Letture obbligatorie

```
1. CLAUDE.md — hard constraints
2. .claude/knowledge/design/sessione-2/saltelli-s2-practice-tier1.jsx (300+ righe)
3. .claude/knowledge/design/sessione-2/saltelli-s2-costi.jsx (577 righe)
4. .claude/knowledge/design/sessione-1/tokens.css (locked, NON modificare)
5. wp-content/themes/saltelli/single-competenza.php (template tier-1 da refactorare)
6. wp-content/themes/saltelli/page.php (blocco is_page('costi') da aggiungere)
7. wp-content/themes/saltelli/assets/css/components.css (.sl-btn rules)
8. wp-content/themes/saltelli/assets/css/sections.css (scope CSS pagine)
```

---

## 🔒 Hard rules

| Rule | Reason |
|---|---|
| **LAYOUT JSX = SACRO** — riproduci esatto markup + classi + ordine | JSX approvato Duccio, no re-design |
| **NESSUNA modifica tokens.css** valori | Locked palette/font/spacing |
| **NON sovrascrivere** `_thumbnail_id` Emiliano + `bio_estesa` Step D + `post_content` CPT competenza/avvocato | Content Step D protetto |
| **CSS scope marker** `/* === v0.23.0 [task] === */` per ogni rule nuova | Audit trail |
| **Mantieni live H2 extra** `/chi-siamo/` ("I nostri quattro" + "Come lavoriamo") | Decisione orchestrator SEO/GEO |
| **NON applicare** "Sei aree di competenza" agli avvocati (decisione Q2: skip) | ACF vuoto, render box vuoti |
| Cache flush + smoke test 5 URL chiave dopo OGNI task | Lezione comprehensive |
| Backup post_content WP page id 2695 (`/costi/`) prima di refactor | Recovery se rollback necessario |
| Mobile-first responsive @media query (mobile compress) | UX safety |

---

## TASK A — Tier-1 deep cluster pixel-perfect (35 min · CRITICO)

### A.1 — Riferimento JSX

Source: `.claude/knowledge/design/sessione-2/saltelli-s2-practice-tier1.jsx`

Struttura JSX da replicare ESATTA:

```
<S2PracticeTier1>
  <S2Header />
  <article class="sl-tier1">
    
    <!-- 1. HERO -->
    <header class="sl-tier1__hero">
      <div class="sl-mono sl-tier1__eyebrow">TIER 1 · Approfondimento · Per le imprese</div>
      <h1 class="sl-tier1__h1" data-split-reveal>Diritto tributario.</h1>
      <p class="sl-tier1__sub">Cartelle, accertamenti, contenzioso.</p>
    </header>
    
    <!-- 2. ANSWER CAPSULE (audit GEO §4.3 first 100 words) -->
    <section class="sl-tier1__capsule">
      <p class="sl-tier1__capsule-text">[50-60 parole answer-style first-paragraph]</p>
    </section>
    
    <!-- 3. AVVOCATO DI RIFERIMENTO -->
    <aside class="sl-tier1__lawyer">
      <div class="sl-mono">§ Avvocato di riferimento</div>
      [card avvocato: foto 80×80 + nome + ruolo + link single-avvocato]
    </aside>
    
    <!-- 4. BODY EDITORIAL (drop-cap + sub-section deep cluster) -->
    <section class="sl-tier1__body">
      <h2 class="sl-tier1__cluster-title">Cartelle esattoriali.</h2>
      <p>[200-300 parole content GEO-optimized]</p>
      
      <h2 class="sl-tier1__cluster-title">Accertamenti sintetici.</h2>
      <p>[200-300 parole]</p>
      
      <h2 class="sl-tier1__cluster-title">Reati tributari.</h2>
      <p>[200-300 parole]</p>
    </section>
    
    <!-- 5. CASI RAPPRESENTATIVI tier-1 -->
    <section class="sl-tier1__cases">
      <header><div class="sl-mono">§ Tre vittorie recenti</div><h2>Tre vittorie recenti.</h2></header>
      <ol>
        <li>[id mono · desc italic · outcome bronze]</li>
        × 3
      </ol>
    </section>
    
    <!-- 6. FAQ ACCORDION editoriale +/− -->
    <section class="sl-tier1__faq">
      <h2>Cinque domande frequenti.</h2>
      <div class="sl-acc">
        <div class="sl-acc__item">
          <button class="sl-acc__btn" aria-expanded="false">
            <span>[Q]</span>
            <span class="sl-acc__icon" aria-hidden="true">+</span>
          </button>
          <div class="sl-acc__panel">
            <div class="sl-acc__inner">[A]</div>
          </div>
        </div>
        × 5
      </div>
    </section>
    
    <!-- 7. ARTICOLI CORRELATI -->
    <section class="sl-tier1__related">
      <header><div class="sl-mono">§ Articoli correlati</div><h2>Tre articoli recenti.</h2></header>
      <div class="sl-tier1__related-grid">
        <article>[date mono + h3 + meta]</article>
        × 3
      </div>
    </section>
    
    <!-- 8. CTA FINALE -->
    <section class="sl-tier1__cta-final">
      <div class="sl-mono">§ Pronto?</div>
      <h2>Hai una pratica simile?</h2>
      <p>[invito 30 parole]</p>
      <a class="sl-btn sl-btn--primary">Prenota un incontro →</a>
    </section>
    
  </article>
  <S2Footer />
</S2PracticeTier1>
```

### A.2 — Implementation in `single-competenza.php`

Refactor il template con scope `is_tier_1=true` (i 3 tier-1: tributario, lavoro, LGBTQ+).

Passaggi:

1. **Backup** del template attuale:
   ```bash
   cp wp-content/themes/saltelli/single-competenza.php wp-content/themes/saltelli/single-competenza.php.v0.22.1.backup
   ```

2. **Verifica `is_tier_1` source** — l'attuale tier-1 è settato come?
   ```bash
   grep -n "is_tier_1\|tier_1\|is_tier1" wp-content/themes/saltelli/single-competenza.php | head -5
   ```
   Probabile via meta o ACF. Se mancante: hardcoda 3 slug tier-1 in helper:
   ```php
   function saltelli_is_tier1_competenza($post_id) {
       $tier1_slugs = ['diritto-tributario', 'diritto-del-lavoro', 'diritto-di-famiglia-lgbtq'];
       $slug = get_post_field('post_name', $post_id);
       return in_array($slug, $tier1_slugs);
   }
   ```

3. **Refactor template tier-1** con structure JSX:
   ```php
   <?php if (saltelli_is_tier1_competenza(get_the_ID())) : ?>
       <article class="sl-tier1">
           <?php /* Hero */ ?>
           <header class="sl-tier1__hero">
               <?php saltelli_render_breadcrumb('competenza'); ?>
               <div class="sl-mono sl-tier1__eyebrow">TIER 1 · Approfondimento · <?php echo esc_html($tier_label); ?></div>
               <h1 class="sl-tier1__h1" data-split-reveal>
                   <?php echo saltelli_split_h1_words(get_the_title(), 'sl-tier1__h1-word'); ?>
               </h1>
               <p class="sl-tier1__sub"><?php echo esc_html(saltelli_get_meta('subtitle', 'Cartelle, accertamenti, contenzioso.')); ?></p>
           </header>
           
           <?php /* Answer Capsule */ ?>
           <section class="sl-tier1__capsule">
               <p class="sl-tier1__capsule-text"><?php echo esc_html(saltelli_get_meta('answer_capsule', '[50-60 parole answer]')); ?></p>
           </section>
           
           <?php /* Lawyer ref */ ?>
           [...]
           
           <?php /* Body deep cluster — content da post_content WP */ ?>
           <section class="sl-tier1__body">
               <?php the_content(); ?>
               <?php /* I H2 nel post_content sono auto-renderizzati con classe .sl-tier1__cluster-title via CSS scope */ ?>
           </section>
           
           [Casi · FAQ · Correlati · CTA come da JSX]
       </article>
   <?php else : ?>
       [Layout tier-2 esistente, intatto]
   <?php endif; ?>
   ```

4. **CSS scope `.sl-tier1__*`** in sections.css:
   ```css
   /* === v0.23.0 TASK A — Tier-1 deep cluster === */
   .sl-tier1 { 
       max-width: 1440px;
       margin: 0 auto;
       padding-inline: clamp(24px, 5vw, 96px);
   }
   
   .sl-tier1__hero {
       padding-block: clamp(64px, 8vw, 120px) clamp(32px, 4vw, 48px);
   }
   
   .sl-tier1__eyebrow { margin-bottom: 24px; }
   
   .sl-tier1__h1 {
       font-family: var(--font-display);
       font-size: clamp(48px, 6vw, 96px);
       line-height: 0.98;
       font-weight: 400;
       letter-spacing: -0.02em;
       margin-block: 0 24px;
       color: var(--primary);
   }
   
   .sl-tier1__sub {
       font-family: var(--font-display);
       font-style: italic;
       font-size: clamp(20px, 1.6vw, 24px);
       line-height: 1.4;
       color: var(--text);
       max-width: 56ch;
       margin: 0;
   }
   
   .sl-tier1__capsule {
       padding-block: 48px;
       border-block: 1px solid var(--border);
       margin-block: 56px;
   }
   
   .sl-tier1__capsule-text {
       font-family: var(--font-display);
       font-style: italic;
       font-size: clamp(22px, 2vw, 28px);
       line-height: 1.5;
       color: var(--primary);
       max-width: 60ch;
       margin: 0;
   }
   
   .sl-tier1__lawyer {
       display: grid;
       grid-template-columns: 80px 1fr;
       gap: 20px;
       align-items: start;
       padding-block: 48px;
       border-bottom: 1px solid var(--border);
   }
   
   .sl-tier1__body {
       padding-block: 64px;
       max-width: 720px;
       margin: 0 auto;
   }
   
   .sl-tier1__body p {
       max-width: 60ch;
       line-height: 1.75;
       margin-block: 0 24px;
   }
   
   .sl-tier1__cluster-title,
   .sl-tier1__body h2 {
       font-family: var(--font-display);
       font-size: clamp(28px, 3vw, 44px);
       line-height: 1.15;
       font-weight: 400;
       margin-block: 80px 32px;
       color: var(--primary);
       max-width: 24ch;
   }
   
   /* Drop-cap on first paragraph body (allinea v0.22.1 pattern) */
   .sl-tier1__body > p:first-of-type::first-letter {
       font-family: var(--font-display);
       font-size: 84px;
       line-height: 0.85;
       float: left;
       margin: 8px 16px 0 0;
       color: var(--primary);
   }
   
   .sl-tier1__cases,
   .sl-tier1__faq,
   .sl-tier1__related,
   .sl-tier1__cta-final {
       padding-block: clamp(64px, 8vw, 128px);
   }
   
   /* FAQ ACCORDION editoriale */
   .sl-tier1__faq .sl-acc {
       max-width: 720px;
       margin: 0 auto;
   }
   
   .sl-acc__item {
       border-bottom: 1px solid var(--border);
   }
   
   .sl-acc__btn {
       display: flex;
       align-items: baseline;
       justify-content: space-between;
       width: 100%;
       padding: 24px 0;
       background: transparent;
       border: 0;
       font-family: var(--font-display);
       font-size: clamp(18px, 2vw, 22px);
       text-align: left;
       cursor: pointer;
       color: var(--primary);
       transition: color var(--dur-fast) var(--ease-quart-out);
   }
   
   .sl-acc__btn:hover { color: var(--accent); }
   .sl-acc__btn:focus-visible { outline: 2px solid var(--accent); outline-offset: 4px; }
   
   .sl-acc__icon {
       font-family: var(--font-mono);
       font-size: 18px;
       color: var(--accent);
       transition: transform var(--dur-fast) var(--ease-quart-out);
   }
   
   .sl-acc__btn[aria-expanded="true"] .sl-acc__icon {
       transform: rotate(45deg);
   }
   
   .sl-acc__panel {
       overflow: hidden;
       max-height: 0;
       transition: max-height var(--dur-base) var(--ease-quart-out);
   }
   
   .sl-acc__panel[aria-hidden="false"] {
       max-height: 500px;
   }
   
   .sl-acc__inner {
       padding-bottom: 24px;
       max-width: 60ch;
       line-height: 1.7;
       color: var(--text);
   }
   
   /* Mobile compress */
   @media (max-width: 767px) {
       .sl-tier1__h1 { font-size: clamp(36px, 8vw, 56px); }
       .sl-tier1__sub { font-size: 18px; }
       .sl-tier1__cluster-title { margin-block: 56px 24px; font-size: clamp(24px, 5vw, 32px); }
       .sl-tier1__body > p:first-of-type::first-letter { font-size: 60px; margin: 4px 12px 0 0; }
   }
   ```

5. **JS handler accordion** in main.js (idempotente):
   ```javascript
   if (!window.slAccBound) {
       window.slAccBound = true;
       document.querySelectorAll('.sl-acc__btn').forEach(btn => {
           btn.addEventListener('click', () => {
               const expanded = btn.getAttribute('aria-expanded') === 'true';
               btn.setAttribute('aria-expanded', !expanded);
               const panel = btn.nextElementSibling;
               if (panel) panel.setAttribute('aria-hidden', expanded);
           });
       });
   }
   ```

### A.3 — ACF / meta opzionali (fallback)

Se ACF disponibili per tier-1 (subtitle + answer_capsule), usa quelli. Altrimenti default hardcoded da JSX.

NB: il tier-1 è 3 page (tributario, lavoro, LGBTQ+). Il JSX usa solo "tributario" come esempio. **Riusa template per i 3, content da WP `the_content()`**.

### A.4 — Smoke verify

```bash
docker compose run --rm wpcli cache flush

# 3 tier-1 verify
for SLUG in diritto-tributario diritto-del-lavoro diritto-di-famiglia-lgbtq; do
    HTML=$(curl -s "http://localhost:8080/competenze/$SLUG/?_=tier1" -m 10)
    echo "─── /competenze/$SLUG/ ───"
    echo "  H1 found:        $(echo "$HTML" | grep -c '<h1')"
    echo "  sl-tier1 wrapper: $(echo "$HTML" | grep -c 'sl-tier1')"
    echo "  Answer capsule:   $(echo "$HTML" | grep -c 'sl-tier1__capsule')"
    echo "  Cluster H2 count: $(echo "$HTML" | grep -c 'sl-tier1__cluster-title')"
    echo "  FAQ accordion:    $(echo "$HTML" | grep -c 'sl-acc__btn')"
    echo "  Articoli corr.:   $(echo "$HTML" | grep -c 'sl-tier1__related')"
    echo "  CTA finale:       $(echo "$HTML" | grep -c 'sl-tier1__cta-final')"
done
```

---

## TASK B — /costi/ Sessione 2 implementation pixel-perfect (25 min)

### B.1 — Riferimento JSX

Source: `.claude/knowledge/design/sessione-2/saltelli-s2-costi.jsx` (577 righe, 27.8KB)

5 sezioni richieste:

```
1. HERO ASIMMETRICO 8fr/4fr
   Sx: breadcrumb + h1 "Costi e prima consulenza" + lede italic
   Dx: trust signal sticky-style box "GRATUITA · 30 MINUTI" + 3 mini-bullet + CTA

2. § 01 · COME FUNZIONA (3-col scenari)
   Card 1 "Vieni a Chiaia" (in studio)
   Card 2 "Videocall riservata" (online)
   Card 3 "Per casi semplici" (telefonica)

3. § 02 · COSA SUCCEDE DOPO I 30 MIN (asimmetrico 4fr/8fr)
   Sx: H2 "Tre scenari possibili"
   Dx: 3 scenari verticali con num mono
     01 NON PROCEDIAMO
     02 PRATICA SEMPLICE — TARIFFA FORFETTARIA
     03 PRATICA COMPLESSA — TARIFFA ORARIA

4. § 03 · COME CALCOLIAMO I PREVENTIVI (2-col 6fr/6fr)
   Sx: drop-cap "T" + body editoriale 200 parole
   Dx: 3 cards stacked (Complessità · Tempo · Esito)

5. § 04 · SUI COSTI IN CHIARO (FAQ 5 Q accordion)
   Q1: "Quanto costa una pratica di diritto tributario?"
   Q2: "Pagamento dilazionato?"
   Q3: "Se non vinco?"
   Q4: "Primo incontro gratuito davvero?"
   Q5: "Recupero crediti success fee?"

6. § 05 · TRUST SIGNALS (4-col grid)
   Plate 1: Iscritti Ordine Avvocati Napoli
   Plate 2: P.IVA 06685101211
   Plate 3: Codice deontologico forense
   Plate 4: Riservatezza assoluta

7. CTA FINALE editoriale
   "§ Pronto?"
   H2 "La prima consulenza è gratuita. Sempre."
   Sub-text + CTA primary
```

### B.2 — Implementation page.php scope

Aggiungi blocco `is_page('costi')`:

```php
<?php elseif (is_page('costi')) : ?>
    <article class="sl-costi-w4">
        
        <?php /* 1. Hero asimmetrico */ ?>
        <header class="sl-costi-w4__hero">
            <div class="sl-costi-w4__hero-grid">
                <div class="sl-costi-w4__hero-text">
                    <?php saltelli_render_breadcrumb('page'); ?>
                    <h1 class="sl-costi-w4__h1" data-split-reveal>
                        <?php echo saltelli_split_h1_words('Costi e prima consulenza', 'sl-costi-w4__h1-word'); ?>
                    </h1>
                    <p class="sl-costi-w4__lede">
                        Trenta minuti gratuiti per ascoltarci, valutare insieme, decidere se procedere.
                        Solo dopo, un preventivo personalizzato basato su complessità, tempi e probabilità di esito.
                    </p>
                </div>
                <aside class="sl-costi-w4__hero-trust">
                    <div class="sl-mono sl-costi-w4__hero-trust-eyebrow">§ Prima consulenza</div>
                    <div class="sl-costi-w4__hero-trust-box">
                        <div class="sl-costi-w4__hero-trust-headline">GRATUITA · 30 MINUTI · IN STUDIO O ONLINE</div>
                        <ul class="sl-costi-w4__hero-trust-list">
                            <li>✓ Nessun obbligo</li>
                            <li>✓ Nessun costo nascosto</li>
                            <li>✓ Riservatezza assoluta</li>
                        </ul>
                    </div>
                    <a class="sl-btn sl-btn--primary" href="/contatti/">Prenota un incontro →</a>
                </aside>
            </div>
        </header>
        
        <?php /* 2. Come funziona — 3 col */ ?>
        <section class="sl-costi-w4__come">
            <header class="sl-costi-w4__section-head">
                <div class="sl-mono">§ 01 · Come funziona la prima consulenza</div>
                <h2>Tre modalità.</h2>
            </header>
            <div class="sl-costi-w4__come-grid">
                [3 card: Studio · Online · Telefonica con eyebrow + h3 + body + trust]
            </div>
        </section>
        
        [...sezioni 3-7 da JSX completo...]
        
    </article>
<?php endif; ?>
```

NB: i contenuti precisi sono nel JSX `saltelli-s2-costi.jsx` — riproduci ESATTI testi delle 5 FAQ, dei 3 scenari, dei 4 trust plates.

### B.3 — CSS scope `.sl-costi-w4__*`

Aggiungi marker e regole complete in sections.css. Nota:
- Usa `@media (min-width: 1024px)` per 8fr/4fr e 6fr/6fr layouts
- Mobile: stack 1-col tutto
- Drop-cap "T" su § 03 body editoriale
- FAQ accordion riusa pattern `.sl-acc__btn` + `__panel` + `__icon` definito in Task A

### B.4 — IMPORTANTE: post_content WP page id 2695

Il post_content attuale di `/costi/` (page id 2695) verrà **bypassato** dal blocco `is_page('costi')`. Backup pre-update:

```bash
docker compose run --rm wpcli post get 2695 --field=post_content > /tmp/costi-post-content.v0.22.1.backup.txt
```

NON sovrascrivere il post_content (rimane in DB come fallback). Il template `is_page('costi')` lo ignora e renderizza struttura JSX-based.

### B.5 — Smoke verify

```bash
HTML=$(curl -s "http://localhost:8080/costi/?_=v23" -m 10)
echo "  sl-costi-w4 wrapper:  $(echo "$HTML" | grep -c 'sl-costi-w4')"
echo "  Hero asimmetrico:     $(echo "$HTML" | grep -c 'sl-costi-w4__hero-grid')"
echo "  Trust box:            $(echo "$HTML" | grep -c 'GRATUITA.*30 MINUTI')"
echo "  Come funziona 3-col:  $(echo "$HTML" | grep -c 'sl-costi-w4__come-grid')"
echo "  3 scenari dopo 30min: $(echo "$HTML" | grep -c 'sl-costi-w4__scenari')"
echo "  Calcoliamo cards:     $(echo "$HTML" | grep -c 'sl-costi-w4__calc')"
echo "  FAQ 5 Q:              $(echo "$HTML" | grep -c 'sl-acc__btn')"
echo "  Trust grid 4-plate:   $(echo "$HTML" | grep -c 'sl-costi-w4__trust-grid')"
echo "  CTA finale:           $(echo "$HTML" | grep -c 'sl-costi-w4__cta-final')"
echo "  legacy sl-costi__intro presente?: $(echo "$HTML" | grep -c 'sl-costi__intro')  (atteso 0)"
```

---

## TASK C — `.sl-input` + `.sl-link` styling editoriale (5 min)

### C.1 — `.sl-input` cross-form

Cerca in components.css o sections.css se `.sl-input` esiste:

```bash
grep -n '^\.sl-input' wp-content/themes/saltelli/assets/css/*.css
```

Se mancante o incompleto, aggiungi in components.css:

```css
/* === v0.23.0 TASK C — .sl-input editorial underline-only === */
.sl-input,
input[type="text"].sl-input,
input[type="email"].sl-input,
input[type="tel"].sl-input,
textarea.sl-input,
select.sl-input {
    border: 0;
    border-bottom: 1px solid var(--border);
    background: transparent;
    padding: 12px 0;
    font-family: var(--font-body);
    font-size: 16px;
    color: var(--text);
    width: 100%;
    transition: border-color var(--dur-fast) var(--ease-quart-out);
}

.sl-input:focus {
    outline: none;
    border-bottom-color: var(--accent);
}

.sl-input:focus-visible {
    outline: none;
    border-bottom-color: var(--accent);
    box-shadow: 0 1px 0 var(--accent);
}

.sl-input::placeholder {
    color: var(--text-muted);
    opacity: 0.7;
}

.sl-input:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.sl-input[aria-invalid="true"] {
    border-bottom-color: #9B3D2E; /* burgundy non-aggressive error */
}

textarea.sl-input {
    min-height: 96px;
    resize: vertical;
}
```

### C.2 — `.sl-link` cross-template

```css
/* === v0.23.0 TASK C — .sl-link editorial bronze hover === */
.sl-link {
    color: var(--primary);
    text-decoration: none;
    border-bottom: 1px solid currentColor;
    padding-bottom: 1px;
    transition: color var(--dur-fast) var(--ease-quart-out),
                border-color var(--dur-fast) var(--ease-quart-out);
}

.sl-link:hover,
.sl-link:focus-visible {
    color: var(--accent);
    border-color: var(--accent);
}

.sl-link:focus-visible {
    outline: 2px solid var(--accent);
    outline-offset: 4px;
}

/* Variante senza underline */
.sl-link--clean {
    border-bottom: 0;
}

.sl-link--clean:hover,
.sl-link--clean:focus-visible {
    color: var(--accent);
}
```

---

## TASK D — Bump + smoke + deploy (5 min)

```bash
sed -i.bak "s/Version: [0-9.]\+.*/Version: 0.23.0-beta-tier1-costi-pixel-perfect/" wp-content/themes/saltelli/style.css
sed -i.bak "s/SALTELLI_THEME_VERSION', '[^']*'/SALTELLI_THEME_VERSION', '0.23.0-beta-tier1-costi-pixel-perfect'/" wp-content/themes/saltelli/functions.php
rm -f wp-content/themes/saltelli/{style.css,functions.php}.bak

git add -A
git commit -m "feat(v0.23.0): tier-1 deep cluster + /costi/ Sessione 2 pixel-perfect + .sl-input/.sl-link"
git push origin main

# Deploy droplet
rsync -avz wp-content/themes/saltelli/ deploy@178.62.207.50:/var/www/saltelli/htdocs/wp-content/themes/saltelli/
ssh deploy@178.62.207.50 "
    sudo -u www-data wp cache flush --path=/var/www/saltelli/htdocs
    sudo -u www-data wp transient delete --all --path=/var/www/saltelli/htdocs
"

# Smoke test 5 URL chiave LIVE
echo ""
echo "═══ SMOKE LIVE v0.23.0 ═══"
for URL in /competenze/diritto-tributario/ /competenze/diritto-del-lavoro/ /competenze/diritto-di-famiglia-lgbtq/ /costi/ /contatti/; do
    HTTP=$(curl -sL -o /dev/null -w "%{http_code}" "https://staging.studiolegalesaltelli.it$URL?_=v23" -m 10)
    echo "  $URL → HTTP $HTTP"
done
```

---

## ⚙️ Strategia esecuzione

### Opzione A — Sequential (~65-75 min)

Single agent: TASK A → B → C → D in ordine.
Più safe, no merge conflict.

### Opzione B — Parallel multi-agent (~35-40 min)

Riusa pattern wave3-launch.sh:
- Agent 1: TASK A (single-competenza.php + CSS scope tier-1)
- Agent 2: TASK B (page.php + CSS scope costi-w4)
- Agent 3: TASK C + D (components.css + bump + deploy)

NB: Agent 1+2 toccano DIFFERENT files (single-competenza vs page.php), quindi parallel-safe.
Agent 3 attende Agent 1+2 (deploy convergente).

CSS scope marker `/* === v0.23.0 [task] === */` evita conflict in sections.css se Agent 1+2 lavorano su scope diversi.

**Raccomandazione**: Sequential per task complesso (tier-1 rewrite + costi rewrite richiedono attenzione + interconnessi via FAQ accordion). Tempo extra non eccessivo (65 vs 35 min).

---

## DELIVERABLE

Report: `.claude/knowledge/design/sessione-2/v0.23.0-PIXEL-PERFECT-FINAL.md`

Format:

```markdown
# v0.23.0 Pixel-Perfect Final
## Score: 4/4 task PASS

## Per task
- A — Tier-1 deep cluster (3 page tributario+lavoro+LGBTQ+):
  ✓ Hero h1 split-reveal
  ✓ Answer capsule editoriale
  ✓ Body deep cluster H2 ("Cartelle", "Accertamenti", ecc.)
  ✓ FAQ accordion 5Q sl-acc__*
  ✓ Casi rappresentativi 3 mini-card
  ✓ Articoli correlati 3 mini
  ✓ CTA finale "Hai una pratica simile?"
  
- B — /costi/ Sessione 2 implementation:
  ✓ Hero asimmetrico 8fr/4fr trust-box
  ✓ § 01 Come funziona 3-col
  ✓ § 02 Cosa succede dopo i 30min
  ✓ § 03 Come calcoliamo (drop-cap "T")
  ✓ § 04 FAQ 5Q (riusa sl-acc)
  ✓ § 05 Trust signals 4-grid
  ✓ CTA finale editoriale
  ✓ legacy .sl-costi__intro NON renderizzato

- C — .sl-input + .sl-link editorial styling:
  ✓ Form fields underline-only cross-form
  ✓ Link bronze hover + underline grow

- D — Bump + smoke + deploy:
  ✓ v0.23.0-beta-tier1-costi-pixel-perfect
  ✓ 5/5 URL HTTP 200 live

## JSX vs Live re-audit (post-fix)
- /competenze/diritto-tributario/  match: 95%+ (era 40%)
- /costi/                          match: 90%+ (era 0% — template legacy)
- /contatti/ form fields          match: 80%+ (sl-input)

## Issue residui
- (eventuali drift content da JSX hardcoded vs ACF dinamico)
- (eventuali dettagli pixel-perfect non coperti)

## Next
GO walkthrough finale orchestrator visuale
o GO v1.0.0 production cut + DNS switch

Tempo: 65-75 min sequential / 35-40 min parallel.
```

Quando finito segnala "v0.23.0 deployed. Tier-1 + Costi + sl-input/link pixel-perfect."

---

*v1.0 — Sessione 2 Pixel-Perfect Final. Direttore d'orchestra: Claude (chat).*
