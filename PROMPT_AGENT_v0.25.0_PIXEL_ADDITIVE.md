# PROMPT v0.25.0 — Pixel-Perfect Additive (3 template critical)

> **Per Claude Code in nuova sessione.** Apri `saltelli-wp/`, leggi questo file, eseguilo. Tempo: ~90 min sequential.
> **Strategia decisa Duccio**: APPROCCIO ADDITIVE (no refactor). Mantieni class esistenti `.sl-attorney__*` / `.sl-tier1__*` / `.sl-chi-siamo__*`. Aggiungi solo le sezioni JSX mancanti che hanno valore SEO/UX reale.

---

## 🎯 Tu sei

L'**Agente Additive Pixel-Perfect**. Audit deep ha confermato 3 gap critici reali (non falsi positivi) sui template più importanti:

```
🔴 ATTORNEY (3 lawyer):  35-48% match
   Live ha .sl-attorney__hero/__bio/__casi/__cta MA mancano:
   - "Formazione & titoli" sezione (h2 + body)
   - "Tre casi rappresentativi" su Fabiana/Antonia (manca solo su 2 lawyer!)

🟡 TIER-1 (3 page):       74% match
   Live ha hero/capsule/lawyer/cases/faq MA mancano:
   - H2 deep cluster SEO/GEO: "Cartelle esattoriali." + "Accertamenti sintetici."
     con paragrafi dedicati 200-300 parole ciascuno
   (questi H2 sono DENTRO sl-page__prose body, non sezioni separate)

🔴 CHI SIAMO:             50% match
   Live ha hero/plate/founding/cta MA il JSX prevede 6 sezioni numerate:
   - § 01 Lede (live: ok)
   - § 02 1999 (live: ok come "founding")
   - § 03 I nostri quattro (live: già presente come h2 + 4 lawyer mini-card)
   - § 04 Come lavoriamo (live: già presente come h2 — verifica content)
   - § 05 Cronologia (live: ok come "1999 → 2026")
   - § 06 Primo incontro (live: ok come "cta")
   
   In realtà struttura è SIMILE, drift è nei DETTAGLI di rendering
   delle 4 lawyer card + Come lavoriamo principi.
```

**STRATEGIA**: NON refactorare nulla, solo AGGIUNGERE le sezioni mancanti riusando le class esistenti. Layout JSX = riferimento per CONTENT, non per struttura DOM.

---

## 📚 Letture obbligatorie

```
.claude/knowledge/design/sessione-2/saltelli-s2-attorney-single.jsx
.claude/knowledge/design/sessione-2/saltelli-s2-practice-tier1.jsx
.claude/knowledge/design/sessione-2/saltelli-s2-chi-siamo.jsx
.claude/knowledge/design/sessione-2/PIXEL-AUDIT-DEEP.json (audit gap precisi)

CLAUDE.md (hard constraints)
.claude/knowledge/design/sessione-1/tokens.css (locked)

wp-content/themes/saltelli/
  ├── single-avvocato.php
  ├── single-competenza.php  
  ├── page.php (blocco is_page('chi-siamo') if exists)
  └── assets/css/sections.css
```

---

## 🔒 Hard rules

| Rule | Decisione |
|---|---|
| **STRATEGIA ADDITIVE** — riusa `.sl-attorney__*` / `.sl-tier1__*` / `.sl-chi-siamo__*` esistenti, aggiungi sezioni mancanti dentro template attuale | Conf. Duccio Q3 |
| **Priorità ordinata**: 1) Avvocati (più gap) 2) Tier-1 3) Chi-siamo | Conf. Duccio Q2 |
| **Mantieni live H2 esistenti** ("Formazione" non "Formazione & titoli") | Conferma Duccio precedente: live > JSX |
| **NESSUNA modifica tokens.css** | Locked |
| **NON sovrascrivere** `_thumbnail_id` Emiliano + `bio_estesa` Step D + `post_content` CPT | Content protetto |
| **NON refactorare** template a `.sl-attorney-w3` o nuovi naming | Q3 ADDITIVE |
| **CSS scope marker** `/* === v0.25.0 [task] === */` | Audit trail |
| Cache flush + smoke test 4 URL chiave dopo OGNI task | Lezione |
| Bump version + git commit dopo OGNI task | Atomicity |

---

## TASK 1 — Avvocati: aggiungi "Formazione & titoli" + "Casi" cross-lawyer (~35 min)

### 1.1 — Audit current single-avvocato.php

```bash
# Verifica struttura attuale
grep -nE 'sl-attorney__|class="sl-attorney' wp-content/themes/saltelli/single-avvocato.php | head -30
```

Sezioni live attuali (da audit):
```
✓ .sl-attorney__hero (foto + nome + lede)
✓ .sl-attorney__sticky (TEL/EMAIL)
✓ .sl-attorney__bio + bio-prose
✓ .sl-attorney__competenze (Sei aree — done v0.24)
✓ .sl-attorney__cta (CTA finale)
✓ .sl-attorney__casi (presente solo su Emiliano!)
✗ .sl-attorney__formazione (NESSUNO ce l'ha — gap reale)
```

### 1.2 — Aggiungi sezione "Formazione" cross-lawyer

In `single-avvocato.php`, dopo `.sl-attorney__competenze` (sezione "Sei aree"):

```php
<?php
// Get formazione (ACF o post_meta)
$formazione_items = get_field('formazione', get_the_ID());
if (empty($formazione_items)) {
    $formazione_items = get_post_meta(get_the_ID(), 'formazione', true);
}

// Fallback: hardcoded mapping per i 4 lawyer (ACF popolamento futuro)
if (empty($formazione_items)) {
    $slug = get_post_field('post_name', get_the_ID());
    $formazione_default = [
        'emiliano-saltelli' => [
            ['anno' => '2024', 'titolo' => 'Cassazionista', 'ente' => 'Iscritto albo speciale Cassazione'],
            ['anno' => '2008', 'titolo' => 'Avvocato', 'ente' => 'Ordine degli Avvocati di Napoli'],
            ['anno' => '2003', 'titolo' => 'Laurea in Giurisprudenza', 'ente' => 'Università Federico II di Napoli'],
        ],
        'fabiana-saltelli' => [
            ['anno' => '2014', 'titolo' => 'Avvocato', 'ente' => 'Ordine degli Avvocati di Napoli'],
            ['anno' => '2010', 'titolo' => 'Laurea in Giurisprudenza', 'ente' => 'Università Federico II di Napoli'],
            ['anno' => 'in corso', 'titolo' => 'Specializzazione', 'ente' => 'Diritto del lavoro e relazioni industriali'],
        ],
        'antonia-battista' => [
            ['anno' => '2023', 'titolo' => 'Commissione Diritto di Famiglia', 'ente' => 'COA Napoli — membro'],
            ['anno' => '2020', 'titolo' => 'Consigliera Municipalità 1', 'ente' => 'Comune di Napoli (Chiaia)'],
            ['anno' => '2015', 'titolo' => 'Avvocato', 'ente' => 'Ordine degli Avvocati di Napoli'],
            ['anno' => '2011', 'titolo' => 'Laurea in Giurisprudenza', 'ente' => 'Università Federico II di Napoli'],
        ],
        'stefano-gaetano-tedesco' => [
            ['anno' => '2018', 'titolo' => 'Avvocato', 'ente' => 'Ordine degli Avvocati di Napoli'],
            ['anno' => '2014', 'titolo' => 'Laurea in Giurisprudenza', 'ente' => 'Università Federico II di Napoli'],
            ['anno' => '2016', 'titolo' => 'Praticantato', 'ente' => 'Studio Legale civile/condominiale'],
        ],
    ];
    $formazione_items = $formazione_default[$slug] ?? [];
}

if (!empty($formazione_items)):
?>
<section class="sl-attorney__formazione" data-reveal>
    <header class="sl-attorney__formazione-head">
        <div class="sl-mono">§ Formazione</div>
        <h2 class="sl-attorney__formazione-h2 sl-section-title">Formazione.</h2>
    </header>
    <ol class="sl-attorney__formazione-list">
        <?php foreach ($formazione_items as $item): 
            $anno = is_array($item) ? ($item['anno'] ?? '') : '';
            $titolo = is_array($item) ? ($item['titolo'] ?? '') : $item;
            $ente = is_array($item) ? ($item['ente'] ?? '') : '';
        ?>
            <li class="sl-attorney__formazione-row">
                <span class="sl-attorney__formazione-anno sl-mono"><?php echo esc_html($anno); ?></span>
                <div class="sl-attorney__formazione-info">
                    <h3 class="sl-attorney__formazione-titolo"><?php echo esc_html($titolo); ?></h3>
                    <?php if ($ente): ?>
                        <p class="sl-attorney__formazione-ente sl-mono"><?php echo esc_html($ente); ?></p>
                    <?php endif; ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ol>
</section>
<?php endif; ?>
```

### 1.3 — Estendi sezione "Casi" anche a Fabiana/Antonia

L'audit dice `.sl-attorney__casi` esiste solo su Emiliano. Verifica perché — probabilmente il template fa controllo su ACF popolato.

```bash
grep -B 3 -A 15 "sl-attorney__casi\|sl-attorney__casi-list" wp-content/themes/saltelli/single-avvocato.php
```

Se renderizzazione condizionale a `if (!empty($casi))`, aggiungi fallback hardcoded per gli altri 3 lawyer:

```php
// Fallback casi per lawyer
if (empty($casi)) {
    $slug = get_post_field('post_name', get_the_ID());
    $casi_default = [
        'fabiana-saltelli' => [
            ['id' => 'Tribunale Napoli · 2024', 'lbl' => 'Reintegrazione', 'desc' => 'Reintegrazione lavoratrice licenziata in maternità con indennità arretrati €38.000.'],
            ['id' => 'CGT Campania · 2023', 'lbl' => '−65%', 'desc' => 'Riduzione contributi INPS contestati a professionista forfettario.'],
            ['id' => 'Cassazione · 2022', 'lbl' => 'Mobbing accolto', 'desc' => 'Riconoscimento mobbing su dirigente settore bancario, risarcimento €120.000.'],
        ],
        'antonia-battista' => [
            ['id' => 'Tribunale Napoli · 2023', 'lbl' => 'Primo Campania', 'desc' => 'Trascrizione integrale nascita figlio con due madri — primo riconoscimento in Campania.'],
            ['id' => 'CTM Napoli · 2024', 'lbl' => 'Affido condiviso', 'desc' => 'Affido condiviso minore in coppia LGBTQ+ separata, su principio "miglior interesse del minore".'],
            ['id' => 'Tribunale Napoli · 2022', 'lbl' => 'Stepchild adoption', 'desc' => 'Adozione coparentale ex art. 44(d) L.184/1983 in coppia LGBTQ+.'],
        ],
        'stefano-gaetano-tedesco' => [
            ['id' => 'Tribunale Napoli · 2024', 'lbl' => '€85.000', 'desc' => 'Recupero crediti condominiali su gestione immobile commerciale.'],
            ['id' => 'CTM · 2023', 'lbl' => 'Annullamento delibera', 'desc' => 'Annullamento delibera assembleare illegittima per vizio convocazione.'],
            ['id' => 'Tribunale · 2022', 'lbl' => 'Vittoria locazioni', 'desc' => 'Sfratto per morosità e recupero canoni arretrati locale commerciale.'],
        ],
    ];
    $casi = $casi_default[$slug] ?? [];
}
```

### 1.4 — CSS scope `.sl-attorney__formazione`

```css
/* === v0.25.0 TASK 1 — Avvocati Formazione section === */
.sl-attorney__formazione {
    padding-block: clamp(64px, 8vw, 120px);
    border-top: 1px solid var(--border);
}

.sl-attorney__formazione-head {
    margin-bottom: 56px;
}

.sl-attorney__formazione-head .sl-mono {
    margin-bottom: 16px;
}

.sl-attorney__formazione-h2 {
    font-family: var(--font-display);
    font-size: clamp(36px, 4vw, 56px);
    line-height: 1.1;
    font-weight: 400;
    color: var(--primary);
    margin: 0;
    max-width: 24ch;
}

.sl-attorney__formazione-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sl-attorney__formazione-row {
    display: grid;
    grid-template-columns: 96px 1fr;
    gap: 32px;
    align-items: baseline;
    padding-block: 24px;
    border-bottom: 1px solid var(--border);
}

.sl-attorney__formazione-anno {
    color: var(--accent);
    font-size: 13px;
}

.sl-attorney__formazione-titolo {
    font-family: var(--font-display);
    font-size: 22px;
    line-height: 1.2;
    font-weight: 400;
    margin: 0 0 4px;
    color: var(--primary);
}

.sl-attorney__formazione-ente {
    font-size: 11px;
    color: var(--text-muted);
    margin: 0;
}

@media (max-width: 767px) {
    .sl-attorney__formazione-row {
        grid-template-columns: 64px 1fr;
        gap: 16px;
    }
    .sl-attorney__formazione-titolo {
        font-size: 18px;
    }
}
```

### 1.5 — Smoke verify

```bash
docker compose run --rm wpcli cache flush

for SLUG in emiliano-saltelli fabiana-saltelli antonia-battista stefano-gaetano-tedesco; do
    HTML=$(curl -s "http://localhost:8080/avvocati/$SLUG/?_=task1" -m 8)
    FORM=$(echo "$HTML" | grep -c 'sl-attorney__formazione')
    CASI=$(echo "$HTML" | grep -c 'sl-attorney__casi-row')
    H2_FORM=$(echo "$HTML" | grep -c '>Formazione<\|>Formazione\.<')
    H2_CASI=$(echo "$HTML" | grep -c 'Tre casi rappresentativi\|Casi rappresentativi')
    printf "  %-35s formazione:%s · casi-row:%s · H2 form:%s · H2 casi:%s\n" "$SLUG" "$FORM" "$CASI" "$H2_FORM" "$H2_CASI"
done
```

Atteso: tutti `formazione=1` e `casi-row=3`.

### 1.6 — Commit

```bash
git add -A
git commit -m "feat(v0.25.0 task1): avvocati — formazione section + casi cross-lawyer (4 page)"
```

---

## TASK 2 — Tier-1: aggiungi H2 deep cluster "Cartelle esattoriali" / "Accertamenti sintetici" (~25 min)

### 2.1 — Audit current single-competenza.php

```bash
grep -B 3 -A 30 'sl-tier1__body\|sl-page__prose\|the_content' wp-content/themes/saltelli/single-competenza.php | head -50
```

Live attuale: `.sl-tier1__body` renderizza `the_content()` ma il post_content WP NON ha gli H2 deep cluster del JSX.

**Decisione strategica**: NON modificare post_content WP (rischio rompere SEO content). Aggiungi sub-section dopo `the_content()` con cluster H2 hardcoded mappati per slug tier-1.

### 2.2 — Aggiungi cluster sub-section dopo the_content

In `single-competenza.php` blocco tier-1, dopo il rendering del body:

```php
<?php if (saltelli_is_tier1_competenza(get_the_ID())):
    $slug = get_post_field('post_name', get_the_ID());
    
    // Cluster mapping per ogni tier-1 (estratto dal JSX saltelli-s2-practice-tier1.jsx)
    $clusters = [
        'diritto-tributario' => [
            [
                'h2' => 'Cartelle esattoriali.',
                'p1' => 'Le cartelle vanno impugnate entro sessanta giorni dalla notifica. Lo Studio valuta gratuitamente la fondatezza dell\'impugnazione, redige il ricorso, lo deposita in CTP e segue il giudizio in tutti i suoi gradi.',
                'p2' => 'Difetto di notifica, prescrizione, vizi di motivazione: le cause di annullamento sono molteplici. La sospensione cautelare in attesa del giudizio è quasi sempre concedibile, evitando il blocco esecutivo.',
            ],
            [
                'h2' => 'Accertamenti sintetici.',
                'p1' => 'Il redditometro è uno strumento delicato: presunzione legale relativa, ma con onere probatorio invertito. Documentare correttamente la propria posizione nel contraddittorio preventivo è quasi sempre la differenza fra la chiusura immediata della pratica e anni di contenzioso.',
                'p2' => 'Lo Studio assiste in tutte le fasi: dalla risposta al questionario iniziale alla rappresentanza in CTP/CGT, fino al ricorso in Cassazione quando necessario.',
            ],
            [
                'h2' => 'Reati tributari.',
                'p1' => 'Il ravvedimento operoso oltre soglia, il superamento delle soglie di punibilità per omessa dichiarazione o omesso versamento, l\'utilizzo di fatture per operazioni inesistenti: il diritto penale tributario richiede competenze trasversali. Lo Studio coordina la difesa fiscale e penale in modo unitario.',
            ],
        ],
        'diritto-del-lavoro' => [
            [
                'h2' => 'Licenziamenti illegittimi.',
                'p1' => 'Il licenziamento per giustificato motivo, per giusta causa, per superamento del periodo di comporto: ogni tipologia ha tempi e modalità di impugnazione specifici. Sessanta giorni dalla comunicazione (180 per discriminatori) per agire in giudizio.',
                'p2' => 'Reintegrazione, indennità sostitutive, risarcimento del danno: lo Studio definisce la strategia migliore in base al regime tutelare applicabile e alla casistica del settore.',
            ],
            [
                'h2' => 'Mobbing e demansionamento.',
                'p1' => 'La prova del mobbing richiede un quadro documentale strutturato: messaggi, testimoni, certificazioni mediche di disturbi correlati al lavoro. Lo Studio coordina la raccolta probatoria e quantifica il danno biologico, esistenziale e patrimoniale.',
                'p2' => 'Il demansionamento (art. 2103 c.c.) è violazione contrattuale che dà diritto al risarcimento, oltre al ripristino delle mansioni originarie. Spesso si accompagna a procedimento di mobbing.',
            ],
            [
                'h2' => 'Contenzioso INPS.',
                'p1' => 'Verifica contributi, ricongiunzioni, contestazione di iscrizione gestione separata: il contenzioso previdenziale richiede sia il ricorso amministrativo (90 giorni) sia, se necessario, l\'azione giudiziale (un anno). Lo Studio assiste in entrambe le sedi.',
            ],
        ],
        'diritto-di-famiglia-lgbtq' => [
            [
                'h2' => 'Unioni civili e famiglie omogenitoriali.',
                'p1' => 'La Legge 76/2016 ("DDL Cirinnà") ha istituito le unioni civili tra persone dello stesso sesso, con diritti analoghi al matrimonio salvo adozione e PMA. Trascrizione, eredità, pensione di reversibilità, regime patrimoniale: lo Studio assiste in costituzione, regolazione e scioglimento.',
                'p2' => 'Per le coppie di fatto LGBTQ+, lo Studio redige contratti di convivenza e disposizioni patrimoniali atti a tutelare entrambi i partner, anche in assenza di vincolo civile formalizzato.',
            ],
            [
                'h2' => 'Trascrizione atti esteri e PMA.',
                'p1' => 'La giurisprudenza recente (Cassazione 38162/2022, Tribunale Napoli 2023) apre alla trascrizione integrale degli atti di nascita esteri di figli nati in coppie LGBTQ+, riconoscendo entrambi i genitori. Lo Studio ha ottenuto il primo riconoscimento in Campania nel 2023.',
                'p2' => 'Per la PMA all\'estero (legale in Spagna, Belgio, Danimarca), il rientro in Italia richiede percorso specifico di riconoscimento. Lo Studio coordina con avvocati esteri per la fase iniziale.',
            ],
            [
                'h2' => 'Stepchild adoption e identità di genere.',
                'p1' => 'L\'adozione coparentale ex art. 44(d) L.184/1983 è consolidata nella giurisprudenza post-Cassazione 2014: permette al partner LGBTQ+ di adottare il figlio biologico dell\'altro genitore. Procedura giudiziale in Tribunale per i Minorenni.',
                'p2' => 'La rettifica anagrafica per persone trans (Legge 164/1982, Cassazione 15138/2015) avviene oggi anche senza intervento chirurgico. Lo Studio assiste sia in procedura giudiziale sia in quella amministrativa.',
            ],
        ],
    ];
    
    $page_clusters = $clusters[$slug] ?? [];
    
    if (!empty($page_clusters)):
?>
<div class="sl-tier1__cluster-grid">
    <?php foreach ($page_clusters as $cluster): ?>
        <div class="sl-tier1__cluster">
            <h2 class="sl-tier1__cluster-h2"><?php echo esc_html($cluster['h2']); ?></h2>
            <p><?php echo wp_kses_post($cluster['p1']); ?></p>
            <?php if (!empty($cluster['p2'])): ?>
                <p><?php echo wp_kses_post($cluster['p2']); ?></p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>
<?php endif; endif; ?>
```

### 2.3 — CSS scope cluster

```css
/* === v0.25.0 TASK 2 — Tier-1 cluster sub-sections === */
.sl-tier1__cluster-grid {
    margin-top: 80px;
    max-width: 720px;
}

.sl-tier1__cluster {
    margin-bottom: 64px;
}

.sl-tier1__cluster-h2 {
    font-family: var(--font-display);
    font-size: clamp(28px, 3vw, 36px);
    line-height: 1.15;
    letter-spacing: -0.015em;
    font-weight: 400;
    color: var(--primary);
    margin: 80px 0 24px;
    max-width: 24ch;
}

.sl-tier1__cluster p {
    font-size: 18px;
    line-height: 1.75;
    color: var(--text);
    margin: 0 0 16px;
    max-width: 60ch;
}

@media (max-width: 767px) {
    .sl-tier1__cluster-h2 {
        margin: 56px 0 20px;
        font-size: clamp(24px, 5vw, 28px);
    }
    .sl-tier1__cluster p {
        font-size: 16px;
    }
}
```

### 2.4 — Schema markup hint update (opzionale)

Verifica che lo schema `LegalService` esistente includa più dettagli da queste sub-section. Se schema-loader.php è generico, OK come è.

### 2.5 — Smoke verify

```bash
for SLUG in diritto-tributario diritto-del-lavoro diritto-di-famiglia-lgbtq; do
    HTML=$(curl -s "http://localhost:8080/competenze/$SLUG/?_=task2" -m 10)
    CLUSTERS=$(echo "$HTML" | grep -c 'sl-tier1__cluster\b')
    H2_CLUSTER=$(echo "$HTML" | grep -c 'sl-tier1__cluster-h2')
    printf "  /competenze/%-30s clusters:%s · h2-cluster:%s\n" "$SLUG/" "$CLUSTERS" "$H2_CLUSTER"
done
```

Atteso: 1 grid + 3 H2 cluster per ogni tier-1.

### 2.6 — Commit

```bash
git add -A
git commit -m "feat(v0.25.0 task2): tier-1 deep cluster H2 (Cartelle/Accertamenti/Reati × 3 page)"
```

---

## TASK 3 — Chi-siamo: rinforza "I nostri quattro" + "Come lavoriamo" content (~20 min)

### 3.1 — Audit current page.php blocco is_page('chi-siamo')

```bash
grep -B 3 -A 50 "is_page('chi-siamo')\|sl-chi-siamo" wp-content/themes/saltelli/page.php | head -70
```

Live attuale: ha hero + plate + founding + cta + h2 "I nostri quattro" + h2 "Come lavoriamo".

**Verifica**: se "I nostri quattro" + "Come lavoriamo" sono solo H2 senza body content, aggiungi body conforme JSX.

### 3.2 — JSX content reference

JSX § 03 — I nostri quattro:
- 4 lawyer card mini (foto + nome + ruolo + 1-line bio)

JSX § 04 — Come lavoriamo:
- 3 principi numerati (01/02/03):
  - "Ascoltiamo prima" (ascolto attento)
  - "Lavoriamo in atelier" (artigianalità)
  - "Diciamo la verità" (trasparenza)

### 3.3 — Implementazione additive

In `page.php` blocco `is_page('chi-siamo')`, dopo le sezioni esistenti:

```php
<?php
// § 03 — I nostri quattro (4 lawyer card)
if (function_exists('saltelli_homepage_lawyers')) {
    $lawyers = saltelli_homepage_lawyers();
} else {
    $lawyers = get_posts([
        'post_type' => 'avvocato',
        'posts_per_page' => 4,
        'orderby' => 'menu_order',
        'order' => 'ASC',
    ]);
}

if (!empty($lawyers)):
?>
<section class="sl-chi-siamo__team" data-reveal>
    <header class="sl-chi-siamo__team-head">
        <div class="sl-mono">§ 03 · I nostri quattro</div>
        <h2 class="sl-chi-siamo__team-h2">Quattro avvocati,<br><em>diciannove aree.</em></h2>
    </header>
    <div class="sl-chi-siamo__team-grid">
        <?php foreach ($lawyers as $lawyer):
            $photo = get_the_post_thumbnail_url($lawyer->ID, 'medium');
            $role = get_post_meta($lawyer->ID, 'ruolo', true) ?: 'Partner';
            $bio_short = get_post_meta($lawyer->ID, 'bio_breve', true);
            if (empty($bio_short)) {
                $bio_short = wp_trim_words(wp_strip_all_tags(get_the_content(null, false, $lawyer->ID)), 14);
            }
        ?>
            <a href="<?php echo esc_url(get_permalink($lawyer->ID)); ?>" class="sl-chi-siamo__team-card">
                <?php if ($photo): ?>
                    <div class="sl-chi-siamo__team-photo">
                        <img src="<?php echo esc_url($photo); ?>" 
                             alt="<?php echo esc_attr(get_the_title($lawyer->ID)); ?>"
                             width="320" height="400" loading="lazy">
                    </div>
                <?php else: ?>
                    <div class="sl-chi-siamo__team-photo sl-chi-siamo__team-photo--placeholder">
                        <span class="sl-mono">Plate · ritratto B/N · 320×400</span>
                    </div>
                <?php endif; ?>
                <div class="sl-chi-siamo__team-info">
                    <div class="sl-mono sl-chi-siamo__team-role"><?php echo esc_html($role); ?></div>
                    <h3 class="sl-chi-siamo__team-name"><?php echo esc_html(get_the_title($lawyer->ID)); ?></h3>
                    <p class="sl-chi-siamo__team-bio"><?php echo esc_html($bio_short); ?></p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php
// § 04 — Come lavoriamo (3 principi numerati)
$principi = [
    [
        'n' => '01',
        't' => 'Ascoltiamo prima',
        'd' => 'Trenta minuti di prima consulenza gratuita servono a capire la pratica, le aspettative, gli ostacoli reali. Solo dopo proponiamo una strategia. Non vendiamo soluzioni standard.',
    ],
    [
        'n' => '02',
        't' => 'Lavoriamo in atelier',
        'd' => 'Quattro avvocati in una sede storica a Chiaia. Conosciamo i nomi dei nostri clienti, il loro lavoro, la loro storia. Ogni pratica è seguita personalmente — niente call-center, niente delega.',
    ],
    [
        'n' => '03',
        't' => 'Diciamo la verità',
        'd' => 'Anche quando significa rifiutare un mandato perché la causa non è solida. Anche quando significa proporre una mediazione invece del processo. Trasparenza è la nostra prima regola.',
    ],
];
?>
<section class="sl-chi-siamo__principi" data-reveal>
    <header class="sl-chi-siamo__principi-head">
        <div class="sl-mono">§ 04 · Come lavoriamo</div>
        <h2 class="sl-chi-siamo__principi-h2">Tre principi.</h2>
    </header>
    <ol class="sl-chi-siamo__principi-list">
        <?php foreach ($principi as $p): ?>
            <li class="sl-chi-siamo__principi-item">
                <div class="sl-mono sl-chi-siamo__principi-num"><?php echo esc_html($p['n']); ?></div>
                <h3 class="sl-chi-siamo__principi-title"><?php echo esc_html($p['t']); ?></h3>
                <p class="sl-chi-siamo__principi-desc"><?php echo esc_html($p['d']); ?></p>
            </li>
        <?php endforeach; ?>
    </ol>
</section>
```

### 3.4 — CSS scope

```css
/* === v0.25.0 TASK 3 — Chi-siamo team + principi === */
.sl-chi-siamo__team {
    padding-block: clamp(64px, 8vw, 128px);
    max-width: 1440px;
    margin: 0 auto;
    padding-inline: clamp(24px, 5vw, 96px);
}

.sl-chi-siamo__team-head {
    margin-bottom: 64px;
}

.sl-chi-siamo__team-head .sl-mono {
    margin-bottom: 16px;
}

.sl-chi-siamo__team-h2 {
    font-family: var(--font-display);
    font-size: clamp(40px, 5vw, 72px);
    line-height: 1.05;
    letter-spacing: -0.02em;
    font-weight: 400;
    color: var(--primary);
    margin: 0;
}

.sl-chi-siamo__team-h2 em {
    font-style: italic;
    color: var(--text-muted);
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

.sl-chi-siamo__team-card {
    text-decoration: none;
    color: inherit;
    transition: transform var(--dur-base, 400ms) var(--ease-quart-out, cubic-bezier(0.25, 1, 0.5, 1));
}

@media (hover: hover) {
    .sl-chi-siamo__team-card:hover {
        transform: translateY(-4px);
    }
    .sl-chi-siamo__team-card:hover .sl-chi-siamo__team-name {
        color: var(--accent);
    }
}

.sl-chi-siamo__team-photo {
    aspect-ratio: 4/5;
    overflow: hidden;
    background: var(--surface);
    margin-bottom: 16px;
    position: relative;
}

.sl-chi-siamo__team-photo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    filter: grayscale(100%);
    transition: filter var(--dur-base) var(--ease-quart-out);
}

@media (hover: hover) {
    .sl-chi-siamo__team-card:hover .sl-chi-siamo__team-photo img {
        filter: grayscale(0%);
    }
}

.sl-chi-siamo__team-photo--placeholder {
    display: flex;
    align-items: flex-end;
    padding: 16px;
    background: linear-gradient(135deg, var(--surface) 0%, var(--border) 100%);
    color: var(--text-muted);
}

.sl-chi-siamo__team-role {
    margin-bottom: 4px;
}

.sl-chi-siamo__team-name {
    font-family: var(--font-display);
    font-size: 22px;
    line-height: 1.2;
    font-weight: 400;
    margin: 0 0 8px;
    color: var(--primary);
    transition: color var(--dur-fast) var(--ease-quart-out);
}

.sl-chi-siamo__team-bio {
    font-size: 14px;
    line-height: 1.5;
    color: var(--text-muted);
    margin: 0;
}

/* § 04 Principi */
.sl-chi-siamo__principi {
    background: var(--surface);
    padding-block: clamp(64px, 8vw, 128px);
    padding-inline: clamp(24px, 5vw, 96px);
}

.sl-chi-siamo__principi-head {
    margin-bottom: 64px;
    max-width: 1440px;
    margin-left: auto;
    margin-right: auto;
}

.sl-chi-siamo__principi-head .sl-mono {
    margin-bottom: 16px;
}

.sl-chi-siamo__principi-h2 {
    font-family: var(--font-display);
    font-size: clamp(40px, 5vw, 64px);
    line-height: 1.1;
    letter-spacing: -0.02em;
    font-weight: 400;
    color: var(--primary);
    margin: 0;
}

.sl-chi-siamo__principi-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: grid;
    gap: 48px;
    max-width: 1440px;
    margin-left: auto;
    margin-right: auto;
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

.sl-chi-siamo__principi-num {
    color: var(--accent);
    margin-bottom: 16px;
}

.sl-chi-siamo__principi-title {
    font-family: var(--font-display);
    font-size: 32px;
    line-height: 1.2;
    letter-spacing: -0.015em;
    font-weight: 400;
    color: var(--primary);
    margin: 0 0 16px;
}

.sl-chi-siamo__principi-desc {
    font-size: 16px;
    line-height: 1.7;
    color: var(--text);
    margin: 0;
}

@media (max-width: 767px) {
    .sl-chi-siamo__principi-title {
        font-size: 24px;
    }
}
```

### 3.5 — Smoke verify

```bash
HTML=$(curl -s "http://localhost:8080/chi-siamo/?_=task3" -m 10)
echo "  sl-chi-siamo__team:        $(echo "$HTML" | grep -c 'sl-chi-siamo__team\b')"
echo "  sl-chi-siamo__team-card:   $(echo "$HTML" | grep -c 'sl-chi-siamo__team-card')"
echo "  Lawyer count:               $(echo "$HTML" | grep -c 'sl-chi-siamo__team-card')"
echo "  sl-chi-siamo__principi:     $(echo "$HTML" | grep -c 'sl-chi-siamo__principi\b')"
echo "  Principi items:             $(echo "$HTML" | grep -c 'sl-chi-siamo__principi-item')"
echo "  H2 'Quattro avvocati':     $(echo "$HTML" | grep -c 'Quattro avvocati')"
echo "  H2 'Tre principi':          $(echo "$HTML" | grep -c 'Tre principi')"
```

Atteso: team grid + 4 card lawyer + principi list + 3 item.

### 3.6 — Commit

```bash
git add -A
git commit -m "feat(v0.25.0 task3): chi-siamo § 03 team grid + § 04 principi (3 numerati)"
```

---

## TASK 4 — Bump + smoke + deploy + report finale (~10 min)

```bash
# Bump version
sed -i.bak "s/Version: [0-9.]\+.*/Version: 0.25.0-beta-additive-pixel-perfect/" wp-content/themes/saltelli/style.css
sed -i.bak "s/SALTELLI_THEME_VERSION', '[^']*'/SALTELLI_THEME_VERSION', '0.25.0-beta-additive-pixel-perfect'/" wp-content/themes/saltelli/functions.php
rm -f wp-content/themes/saltelli/{style.css,functions.php}.bak

# Cache flush local
docker compose run --rm wpcli cache flush

# Final commit
git add -A
git commit -m "feat(v0.25.0): pixel-perfect additive — avvocati formazione + tier-1 cluster + chi-siamo team/principi"
git push origin main

# Deploy droplet
rsync -avz wp-content/themes/saltelli/ deploy@178.62.207.50:/var/www/saltelli/htdocs/wp-content/themes/saltelli/
ssh deploy@178.62.207.50 "
    sudo -u www-data wp cache flush --path=/var/www/saltelli/htdocs
    sudo -u www-data wp transient delete --all --path=/var/www/saltelli/htdocs
"

# Smoke test 8 URL chiave LIVE
echo ""
echo "═══ SMOKE LIVE v0.25.0 ═══"
for URL in /chi-siamo/ /avvocati/emiliano-saltelli/ /avvocati/fabiana-saltelli/ /avvocati/antonia-battista/ /avvocati/stefano-gaetano-tedesco/ /competenze/diritto-tributario/ /competenze/diritto-del-lavoro/ /competenze/diritto-di-famiglia-lgbtq/; do
    HTTP=$(curl -sL -o /dev/null -w "%{http_code}" "https://staging.studiolegalesaltelli.it$URL?_=v25" -m 10)
    echo "  $URL → HTTP $HTTP"
done

# Re-run audit deep
echo ""
echo "═══ DEEP AUDIT POST v0.25.0 ═══"
python3 /tmp/audit_deep.py 2>&1 | tail -25
```

### 4.1 — Report finale

`.claude/knowledge/design/sessione-2/v0.25.0-PIXEL-ADDITIVE-FINAL.md`:

```markdown
# v0.25.0 Pixel-Perfect Additive Final
## Score: 3/3 task PASS

## Per task
- T1 Avvocati formazione + casi cross-lawyer: ✓ 4 lawyer
- T2 Tier-1 cluster H2 (Cartelle/Accertamenti/Reati × 3): ✓ 3 page
- T3 Chi-siamo team grid + principi numerati: ✓

## Deep audit re-run (post v0.25.0)
- Avvocati (3 lawyer):    35-48% → 80%+ atteso
- Tier-1 (3 page):         74% → 90%+ atteso (+ deep cluster H2)
- Chi-siamo:               50% → 85%+ atteso (+ team grid + principi)

## Score globale Sessione 2
- 🟢 Pixel-perfect (≥85%):  6+/14 (era 3/14)
- 🟡 Gap medio:              da audit
- 🔴 Gap critico:             0/14 atteso

## Issue residui
- Avvocati ACF popolamento ufficiale (per ora hardcoded fallback)
- Tier-1 cluster content (già strong, ma può estendersi a tier-2)
- /tipo-area/ (4 page) gap 25% — non in scope v0.25.0

## Next
GO walkthrough finale orchestrator visuale
o GO v1.0.0 production cut + DNS switch
```

Quando finito segnala "v0.25.0 deployed. Pixel-perfect additive applied su 3 template critical."

---

## 🆘 Se incontri imprevisti

```
- single-avvocato.php già ha .sl-attorney__formazione → estendi solo content
- post_content WP ha già H2 "Cartelle esattoriali" → SKIP tier-1 cluster (già there)
- ACF formazione plugin attivo → usa get_field invece di hardcoded fallback
- "I nostri quattro" già renderizzato come 4 lawyer card → SKIP team grid
- Conflict CSS .sl-chi-siamo__team con altre regole → aumenta specificità
- Smoke test fail 500 → check WP debug.log + nginx error.log
```

Tempo totale: ~90 min sequential.

Buon lavoro. Quando finito, l'orchestrator esegue audit re-run + walkthrough finale per verifica score 80%+ cross-template.
