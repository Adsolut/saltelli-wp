# PROMPT v0.24.0 — Pixel-Perfect Final Implementation (8 Tasks Sequential)

> **Per Claude Code in nuova sessione (oppure stessa).** Apri `saltelli-wp/`, leggi questo file, eseguilo. Tempo: ~120 min sequential.
> **PRECEDENZA:** v0.23.0 deve essere completato (tier-1 + costi base done).

---

## 🎯 Tu sei

L'**Agente Pixel-Perfect Final**. Audit Duccio post-v0.23.0 ha rilevato **8 gap critici** + 1 gap medio tra JSX Sessione 2 approvati e implementation live (>70% delivery non riprodotto pixel-perfect):

```
🔴 GAP CRITICI (match <70%):
  /avvocati/emiliano-saltelli/                 44%
  /avvocati/fabiana-saltelli/                  44%
  /avvocati/antonia-battista/                  44%
  /avvocati/stefano-gaetano-tedesco/           (mancante "Sei aree" idem)
  /competenze/diritto-tributario/              40%
  /competenze/diritto-del-lavoro/              40%
  /competenze/diritto-di-famiglia-lgbtq/       40%
  /contatti/                                   66%
  /costi/                                      43%

🟡 GAP MEDIO:
  /chi-siamo/                                  80% (già OK strategicamente, manteniamo live)

🟢 OK PIXEL-PERFECT (≥90%):
  /, /casi/, /blog/, /tipo-area/* (4), /glossario-legale/, footer, chrome
```

**LAYOUT JSX = SACRO.** I 12 JSX in `.claude/knowledge/design/sessione-2/` sono stati approvati dall'orchestrator. Implementare al pixel come scritto, non re-design.

---

## 📚 Letture obbligatorie

```
.claude/knowledge/design/sessione-2/
  ├── saltelli-s2-attorney-single.jsx       (12.7KB · 4 single-avvocato)
  ├── saltelli-s2-practice-tier1.jsx        (14KB · 3 tier-1 deep)
  ├── saltelli-s2-contatti.jsx              (13.2KB · success state mancante)
  └── saltelli-s2-costi.jsx                 (27.8KB · 5 sezioni mancanti)

CLAUDE.md
.claude/knowledge/design/sessione-1/tokens.css (locked)
.claude/knowledge/design/sessione-2/PIXEL-AUDIT-FULL.json (audit completo)

wp-content/themes/saltelli/
  ├── single-avvocato.php
  ├── single-competenza.php
  ├── page.php (blocchi is_page custom)
  ├── inc/helpers.php
  └── assets/css/sections.css
```

---

## 🔒 Hard rules (vincoli orchestrator confermati)

| Rule | Decisione |
|---|---|
| **LAYOUT JSX = SACRO** — riproduci esatto markup + classi + ordine | Confermato |
| **NESSUNA modifica tokens.css** valori | Locked |
| **NON sovrascrivere** `_thumbnail_id` Emiliano + `bio_estesa` Step D | Content protetto |
| **Q1 ACF "Sei aree"**: implementa su TUTTI 4 lawyer + popola ACF dove vuoto | Conf. Duccio |
| **Q2 /chi-siamo/**: MANTIENI live (skip refactor, già SEO/GEO compliant) | Conf. Duccio |
| **Q3 Avvocato di riferimento tier-1**: mapping fisso | Conf. Duccio |
| **Q4 Esecuzione**: SEQUENZIALE single agent | Conf. Duccio |
| **CSS scope marker** `/* === v0.24.0 [task] === */` per ogni rule nuova | Audit trail |
| Cache flush + smoke test 5 URL chiave dopo OGNI task | Lezione |
| Bump version + git commit dopo OGNI task major (no big-bang) | Atomicity |

---

## 🗺 Ordine sequenziale (ottimizzato per dipendenze)

```
Task 1 → ACF lawyer aree_competenza_correlate (DB writes, indipendente)        ~15 min
Task 2 → single-avvocato.php "Sei aree di competenza" sezione (4 lawyer)        ~25 min
Task 3 → single-competenza.php tier-1 FAQ accordion (.sl-acc__*)                ~20 min
Task 4 → single-competenza.php tier-1 "Avvocato riferimento" card mapping       ~15 min
Task 5 → page.php is_page('costi') 5 sezioni mancanti                           ~25 min
Task 6 → /contatti/ form success state H3 + .sl-input class injection           ~10 min
Task 7 → CSS cleanup + drop-cap residui + sl-spinner footer                      ~10 min
Task 8 → Bump + smoke + deploy + report finale                                   ~10 min
```

---

## TASK 1 — ACF Lawyer aree_competenza_correlate (~15 min · CRITICO)

### 1.1 — Discovery ACF current state

```bash
docker compose run --rm wpcli post list --post_type=avvocato --fields=ID,post_name --format=csv
```

ID lawyer (verifica nel tuo DB):
```
2660 emiliano-saltelli         ← ACF popolato (verify)
2661 fabiana-saltelli          ← ACF da popolare
2662 antonia-battista          ← ACF da popolare
2663 stefano-gaetano-tedesco   ← ACF da popolare
```

Verifica meta key effettiva:
```bash
docker compose run --rm wpcli post meta list 2660 --format=csv | grep -iE "aree|competenza|correlate"
```

Probabile meta key: `aree_competenza_correlate` (ACF Repeater) oppure `_aree_correlate`.

### 1.2 — Mapping competenze per lawyer (Duccio approved Q3 mapping)

Avvocato → 6 aree di competenza (post_name slug):

```
EMILIANO SALTELLI (founding · tributarista · cassazionista):
  - diritto-tributario           (tier-1 primario)
  - cartelle-esattoriali-e-multe
  - recupero-crediti
  - diritto-bancario
  - responsabilita-civile
  - diritto-penale (reati tributari)

FABIANA SALTELLI (giuslavorista · separazioni):
  - diritto-del-lavoro           (tier-1 primario)
  - diritto-di-famiglia
  - diritto-previdenziale
  - responsabilita-medica
  - risarcimento-danni
  - diritto-delle-successioni

ANTONIA BATTISTA (LGBTQ+ famiglia · COA):
  - diritto-di-famiglia-lgbtq    (tier-1 primario)
  - diritto-di-famiglia
  - diritto-delle-successioni
  - diritto-dellimmigrazione
  - responsabilita-civile
  - diritto-penale

STEFANO GAETANO TEDESCO (immobiliare · condominiale):
  - diritto-condominiale
  - recupero-crediti
  - diritto-bancario
  - responsabilita-civile
  - risarcimento-danni
  - diritto-delle-assicurazioni
```

### 1.3 — Popola ACF via WP-CLI

```bash
# Funzione helper PHP per ottenere CPT competenza ID da slug
docker compose run --rm wpcli eval '
function get_comp_id($slug) {
    $p = get_page_by_path($slug, OBJECT, "competenza");
    return $p ? $p->ID : null;
}

$lawyers = [
    2660 => ["diritto-tributario", "cartelle-esattoriali-e-multe", "recupero-crediti", "diritto-bancario", "responsabilita-civile", "diritto-penale"],
    2661 => ["diritto-del-lavoro", "diritto-di-famiglia", "diritto-previdenziale", "responsabilita-medica", "risarcimento-danni", "diritto-delle-successioni"],
    2662 => ["diritto-di-famiglia-lgbtq", "diritto-di-famiglia", "diritto-delle-successioni", "diritto-dellimmigrazione", "responsabilita-civile", "diritto-penale"],
    2663 => ["diritto-condominiale", "recupero-crediti", "diritto-bancario", "responsabilita-civile", "risarcimento-danni", "diritto-delle-assicurazioni"],
];

foreach ($lawyers as $lawyer_id => $slugs) {
    $ids = array_filter(array_map("get_comp_id", $slugs));
    update_field("aree_competenza_correlate", $ids, $lawyer_id);
    // Fallback: se non ACF
    update_post_meta($lawyer_id, "aree_competenza_correlate", $ids);
    echo "Lawyer $lawyer_id: " . count($ids) . " aree assegnate\n";
}
'
```

NB: se ACF non è plugin attivo, usa `update_post_meta` come fallback.

### 1.4 — Smoke verify

```bash
docker compose run --rm wpcli cache flush
for ID in 2660 2661 2662 2663; do
    echo "─── Lawyer $ID ───"
    docker compose run --rm wpcli post meta get $ID aree_competenza_correlate
done
```

Atteso: array di 6 ID per ognuno.

---

## TASK 2 — single-avvocato.php "Sei aree di competenza" (~25 min)

### 2.1 — Riferimento JSX

In `saltelli-s2-attorney-single.jsx` la sezione "Sei aree di competenza":

```jsx
<section className="sl-attorney__competenze">
  <header>
    <div className="sl-mono">§ Competenze</div>
    <h2>Sei aree di competenza.</h2>
  </header>
  <ol className="sl-attorney__areas-list">
    {lawyer.areas.map((a, i) => (
      <li key={i} className="sl-area">
        <span className="sl-area__num">{String(i+1).padStart(2,'0')}</span>
        <h3 className="sl-area__title">{a.name}</h3>
        <p className="sl-area__meta">{a.tier} · {a.cluster}</p>
      </li>
    ))}
  </ol>
</section>
```

Pattern: lista numerata 6 aree, ogni area = num mono + h3 titolo + meta (tier + cluster).

### 2.2 — Implementation in single-avvocato.php

Inserire dopo sezione "Bio" (o dopo formazione, se più logico):

```php
<?php
// Get aree correlate
$aree_ids = get_field('aree_competenza_correlate', get_the_ID());
if (empty($aree_ids)) {
    $aree_ids = get_post_meta(get_the_ID(), 'aree_competenza_correlate', true);
}

if (!empty($aree_ids) && is_array($aree_ids)) :
?>
<section class="sl-attorney__competenze" data-reveal>
    <header class="sl-attorney__competenze-head">
        <div class="sl-mono">§ Competenze</div>
        <h2 class="sl-attorney__competenze-h2">Sei aree di competenza.</h2>
    </header>
    <ol class="sl-attorney__areas-list">
        <?php foreach ($aree_ids as $i => $area_id) :
            $area = get_post($area_id);
            if (!$area) continue;
            
            // Tier label e cluster
            $is_tier1 = function_exists('saltelli_is_tier1_competenza') 
                ? saltelli_is_tier1_competenza($area_id)
                : false;
            $tier_label = $is_tier1 ? 'Tier 1 · Approfondimento' : 'Tier 2';
            
            // Cluster da taxonomy tipo-area
            $terms = get_the_terms($area_id, 'tipo-area');
            $cluster = $terms && !is_wp_error($terms) ? $terms[0]->name : '';
            
            $num = str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT);
        ?>
            <li class="sl-area">
                <span class="sl-area__num sl-mono"><?php echo esc_html($num); ?></span>
                <h3 class="sl-area__title">
                    <a href="<?php echo esc_url(get_permalink($area_id)); ?>" class="sl-link sl-link--clean">
                        <?php echo esc_html($area->post_title); ?>
                    </a>
                </h3>
                <p class="sl-area__meta sl-mono">
                    <?php echo esc_html($tier_label); ?>
                    <?php if ($cluster) : ?> · <?php echo esc_html($cluster); ?><?php endif; ?>
                </p>
            </li>
        <?php endforeach; ?>
    </ol>
</section>
<?php endif; ?>
```

### 2.3 — CSS scope `.sl-attorney__competenze`

```css
/* === v0.24.0 TASK 2 — Avvocato Sei aree di competenza === */
.sl-attorney__competenze {
    padding-block: clamp(64px, 8vw, 120px);
    border-top: 1px solid var(--border);
}

.sl-attorney__competenze-head {
    margin-bottom: 56px;
}

.sl-attorney__competenze-head .sl-mono {
    margin-bottom: 16px;
}

.sl-attorney__competenze-h2 {
    font-family: var(--font-display);
    font-size: clamp(36px, 4vw, 56px);
    line-height: 1.1;
    font-weight: 400;
    color: var(--primary);
    margin: 0;
    max-width: 24ch;
}

.sl-attorney__areas-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: grid;
    gap: 0;
}

.sl-attorney__areas-list .sl-area {
    display: grid;
    grid-template-columns: 64px 1fr auto;
    gap: 24px;
    align-items: baseline;
    padding-block: 32px;
    border-bottom: 1px solid var(--border);
    transition: transform var(--dur-base, 400ms) var(--ease-quart-out, cubic-bezier(0.25, 1, 0.5, 1)),
                background var(--dur-base) var(--ease-quart-out);
}

@media (hover: hover) {
    .sl-attorney__areas-list .sl-area:hover {
        transform: translateX(8px);
    }
    .sl-attorney__areas-list .sl-area:hover .sl-area__num {
        color: var(--accent);
    }
}

.sl-attorney__areas-list .sl-area__num {
    color: var(--text-muted);
    font-size: 13px;
    transition: color var(--dur-fast) var(--ease-quart-out);
}

.sl-attorney__areas-list .sl-area__title {
    font-family: var(--font-display);
    font-size: clamp(22px, 2vw, 28px);
    line-height: 1.2;
    font-weight: 400;
    margin: 0;
    color: var(--primary);
}

.sl-attorney__areas-list .sl-area__title a {
    color: inherit;
    text-decoration: none;
}

.sl-attorney__areas-list .sl-area__title a:hover {
    color: var(--accent);
}

.sl-attorney__areas-list .sl-area__meta {
    font-size: 11px;
    color: var(--text-muted);
    text-align: right;
    white-space: nowrap;
}

@media (max-width: 767px) {
    .sl-attorney__areas-list .sl-area {
        grid-template-columns: 48px 1fr;
        gap: 16px;
        padding-block: 24px;
    }
    .sl-attorney__areas-list .sl-area__meta {
        grid-column: 1 / -1;
        text-align: left;
        margin-top: 4px;
    }
}
```

### 2.4 — Smoke verify

```bash
for SLUG in emiliano-saltelli fabiana-saltelli antonia-battista stefano-gaetano-tedesco; do
    HTML=$(curl -s "http://localhost:8080/avvocati/$SLUG/?_=task2" -m 8)
    AREAS=$(echo "$HTML" | grep -c 'sl-area__title')
    H2=$(echo "$HTML" | grep -c 'Sei aree di competenza')
    printf "  /avvocati/%-30s aree:%s 'Sei aree':%s\n" "$SLUG/" "$AREAS" "$H2"
done
```

Atteso: 6 aree per ognuno + H2 "Sei aree di competenza" = 1.

### 2.5 — Commit incrementale

```bash
git add -A
git commit -m "feat(v0.24.0 task2): single-avvocato 'Sei aree di competenza' (4 lawyer)"
```

---

## TASK 3 — Tier-1 FAQ accordion `.sl-acc__*` (~20 min)

### 3.1 — Riferimento JSX

In `saltelli-s2-practice-tier1.jsx`:

```jsx
<section className="sl-tier1__faq">
  <h2>Cinque domande frequenti.</h2>
  <div className="sl-acc">
    {faq.map((q, i) => (
      <div key={i} className={`sl-acc__item ${openFaq === i ? 'is-open' : ''}`}>
        <button
          className="sl-acc__btn"
          aria-expanded={openFaq === i}
          onClick={() => setOpenFaq(openFaq === i ? -1 : i)}
        >
          <span>{q.q}</span>
          <span className="sl-acc__icon">{openFaq === i ? '−' : '+'}</span>
        </button>
        <div className="sl-acc__panel" aria-hidden={openFaq !== i}>
          <div className="sl-acc__inner">{q.a}</div>
        </div>
      </div>
    ))}
  </div>
</section>
```

5 FAQ tematiche per tier-1 (vedere JSX per testo esatto).

### 3.2 — Implementation in single-competenza.php

Verifica se v0.23.0 ha implementato l'accordion. Se l'audit mostra ancora `sl-acc__btn=0`, l'agent v0.23.0 ha saltato questa parte.

Aggiungere dopo sezione "Casi rappresentativi":

```php
<?php if (saltelli_is_tier1_competenza(get_the_ID())) : 
    // FAQ specifiche per ogni tier-1 (hardcoded da JSX o ACF)
    $slug = get_post_field('post_name', get_the_ID());
    $faqs = saltelli_get_tier1_faqs($slug); // helper
    
    if (!empty($faqs)) :
?>
<section class="sl-tier1__faq" data-reveal>
    <header class="sl-tier1__faq-head">
        <div class="sl-mono">§ FAQ</div>
        <h2 class="sl-tier1__faq-h2">Cinque domande frequenti.</h2>
    </header>
    <div class="sl-acc">
        <?php foreach ($faqs as $i => $faq) : 
            $aria_id = 'faq-' . $i;
        ?>
            <div class="sl-acc__item">
                <button 
                    class="sl-acc__btn"
                    aria-expanded="false"
                    aria-controls="<?php echo esc_attr($aria_id); ?>"
                >
                    <span><?php echo esc_html($faq['q']); ?></span>
                    <span class="sl-acc__icon" aria-hidden="true">+</span>
                </button>
                <div 
                    class="sl-acc__panel" 
                    id="<?php echo esc_attr($aria_id); ?>"
                    aria-hidden="true"
                >
                    <div class="sl-acc__inner">
                        <?php echo wp_kses_post($faq['a']); ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php /* Schema FAQPage */ ?>
<script type="application/ld+json">
<?php echo wp_json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'FAQPage',
    'mainEntity' => array_map(function($faq) {
        return [
            '@type' => 'Question',
            'name' => $faq['q'],
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text' => wp_strip_all_tags($faq['a']),
            ],
        ];
    }, $faqs),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>
</script>
<?php endif; endif; ?>
```

### 3.3 — Helper FAQ tier-1 in inc/helpers.php

```php
if (!function_exists('saltelli_get_tier1_faqs')) {
    function saltelli_get_tier1_faqs($slug) {
        $faqs = [
            'diritto-tributario' => [
                ['q' => 'Quando conviene impugnare una cartella esattoriale?',
                 'a' => 'Le cartelle vanno impugnate entro 60 giorni dalla notifica davanti alla Commissione Tributaria competente. Lo Studio valuta gratuitamente la fondatezza dell\'impugnazione nel primo incontro.'],
                ['q' => 'Cosa fare se l\'Agenzia delle Entrate avvia un accertamento sintetico?',
                 'a' => 'Prima dell\'accertamento si apre un contraddittorio preventivo: è la fase più delicata. Documentare correttamente la propria posizione in questa sede può evitare il contenzioso.'],
                ['q' => 'Quali sono i tempi medi di un contenzioso tributario?',
                 'a' => 'Primo grado in CTP: 12-18 mesi. Appello in CTR: ulteriori 18-24 mesi. Cassazione: 24-36 mesi. La sospensione cautelare è quasi sempre concedibile.'],
                ['q' => 'Si possono rateizzare le somme dovute?',
                 'a' => 'Sì, fino a 72 rate mensili (120 in casi di grave difficoltà). Lo Studio assiste anche nella negoziazione dei piani di rateizzazione con AGE Riscossione.'],
                ['q' => 'Quanto costa un contenzioso tributario?',
                 'a' => 'Il primo incontro è gratuito. Il preventivo è scritto, fisso o a percentuale del beneficio. Le parcelle seguono i parametri ministeriali, sempre concordate prima del mandato.'],
            ],
            'diritto-del-lavoro' => [
                ['q' => 'Il licenziamento è impugnabile? Entro quando?',
                 'a' => 'Sì, entro 60 giorni dalla comunicazione (180 giorni se per contestazione discriminatoria). Lo Studio valuta gratuitamente la fondatezza nel primo incontro.'],
                ['q' => 'Cos\'è il mobbing e come si dimostra?',
                 'a' => 'Il mobbing richiede prova documentale di vessazioni reiterate. Servono messaggi, testimoni, note mediche. Lo Studio coordina la raccolta probatoria.'],
                ['q' => 'Cosa cambia tra contestazione disciplinare e licenziamento?',
                 'a' => 'La contestazione è il primo step: se non si difende correttamente entro 5 giorni si arriva al provvedimento. Difesa scritta entro 5gg è critica.'],
                ['q' => 'Sono lavoratore autonomo: che tutele ho?',
                 'a' => 'Anche il lavoro autonomo gode di tutele crescenti (legge 81/2017). Equo compenso, recesso illegittimo, dipendenza economica.'],
                ['q' => 'INPS contestato: cosa fare?',
                 'a' => 'Ricorso amministrativo entro 90 giorni, poi giudiziale entro un anno. Lo Studio assiste in entrambe le sedi.'],
            ],
            'diritto-di-famiglia-lgbtq' => [
                ['q' => 'L\'unione civile dà gli stessi diritti del matrimonio?',
                 'a' => 'L\'unione civile (legge 76/2016, "DDL Cirinnà") dà la maggior parte dei diritti del matrimonio salvo adozione e fecondazione assistita. Trascrizione, eredità, pensione di reversibilità: sì.'],
                ['q' => 'Trascrizione di nascita di figlio nato all\'estero (PMA o GPA): è possibile?',
                 'a' => 'Dipende dalla giurisdizione di nascita. La Cassazione 38162/2022 e successive aprono spiragli per trascrizione integrale (entrambi genitori). Lo Studio ha ottenuto il primo riconoscimento in Campania nel 2023.'],
                ['q' => 'Stepchild adoption: in quali casi è possibile?',
                 'a' => 'L\'adozione coparentale (art. 44 lettera d L.184/1983) è possibile su minore già genitore biologico del partner. Procedura giudiziale, esito favorevole consolidato post-Cassazione 2014.'],
                ['q' => 'Cosa succede in caso di separazione tra coppie LGBTQ+?',
                 'a' => 'Per unioni civili: scioglimento giudiziale come divorzio. Per coppie di fatto: tutela patrimoniale separata, contratti di convivenza. Affido figli: principio del "miglior interesse del minore" prevale su orientamento.'],
                ['q' => 'L\'identità di genere è riconosciuta legalmente?',
                 'a' => 'Sì, legge 164/1982 (rettifica anagrafica). Procedura giudiziale o amministrativa (post-Cassazione 15138/2015). Lo Studio assiste in tutti i passaggi.'],
            ],
        ];
        return $faqs[$slug] ?? [];
    }
}
```

### 3.4 — CSS `.sl-acc__*` (riusa da v0.23.0 se già presente, altrimenti aggiungi)

Verifica con: `grep -c 'sl-acc__btn' wp-content/themes/saltelli/assets/css/sections.css`

Se = 0:

```css
/* === v0.24.0 TASK 3 — FAQ accordion editorial === */
.sl-tier1__faq {
    padding-block: clamp(64px, 8vw, 128px);
}

.sl-tier1__faq-head { margin-bottom: 48px; }

.sl-tier1__faq-h2 {
    font-family: var(--font-display);
    font-size: clamp(28px, 3vw, 44px);
    line-height: 1.1;
    font-weight: 400;
    color: var(--primary);
    margin: 0;
}

.sl-acc {
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
    gap: 24px;
    width: 100%;
    padding: 24px 0;
    background: transparent;
    border: 0;
    font-family: var(--font-display);
    font-size: clamp(18px, 2vw, 22px);
    line-height: 1.4;
    text-align: left;
    cursor: pointer;
    color: var(--primary);
    transition: color var(--dur-fast, 200ms) var(--ease-quart-out, cubic-bezier(0.25, 1, 0.5, 1));
}

.sl-acc__btn:hover { color: var(--accent); }
.sl-acc__btn:focus-visible {
    outline: 2px solid var(--accent);
    outline-offset: 4px;
}

.sl-acc__icon {
    font-family: var(--font-mono);
    font-size: 20px;
    color: var(--accent);
    flex-shrink: 0;
    transition: transform var(--dur-fast) var(--ease-quart-out);
}

.sl-acc__btn[aria-expanded="true"] .sl-acc__icon {
    transform: rotate(45deg);
}

.sl-acc__panel {
    overflow: hidden;
    max-height: 0;
    transition: max-height var(--dur-base, 400ms) var(--ease-quart-out);
}

.sl-acc__panel[aria-hidden="false"] {
    max-height: 800px;
}

.sl-acc__inner {
    padding-bottom: 24px;
    max-width: 60ch;
    line-height: 1.7;
    color: var(--text);
}
```

### 3.5 — JS handler accordion (idempotente in main.js)

Verifica se già presente. Se mancante:

```javascript
// === v0.24.0 — FAQ accordion idempotent ===
if (!window.slAccBound) {
    window.slAccBound = true;
    document.querySelectorAll('.sl-acc__btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const expanded = btn.getAttribute('aria-expanded') === 'true';
            btn.setAttribute('aria-expanded', String(!expanded));
            const panelId = btn.getAttribute('aria-controls');
            const panel = panelId ? document.getElementById(panelId) : btn.nextElementSibling;
            if (panel) panel.setAttribute('aria-hidden', String(expanded));
        });
    });
}
```

### 3.6 — Smoke

```bash
for SLUG in diritto-tributario diritto-del-lavoro diritto-di-famiglia-lgbtq; do
    HTML=$(curl -s "http://localhost:8080/competenze/$SLUG/?_=task3" -m 10)
    BTN=$(echo "$HTML" | grep -c 'sl-acc__btn')
    PANEL=$(echo "$HTML" | grep -c 'sl-acc__panel')
    SCHEMA=$(echo "$HTML" | grep -c 'FAQPage')
    printf "  /competenze/%-30s sl-acc__btn:%s panel:%s schema:%s\n" "$SLUG/" "$BTN" "$PANEL" "$SCHEMA"
done
```

Atteso: 5 btn + 5 panel + 1 FAQPage schema per ogni tier-1.

### 3.7 — Commit

```bash
git add -A
git commit -m "feat(v0.24.0 task3): tier-1 FAQ accordion .sl-acc__* + 15 FAQ + FAQPage schema"
```

---

## TASK 4 — Tier-1 "Avvocato di riferimento" card mapping (~15 min)

### 4.1 — Mapping (Q3 confermato)

```
diritto-tributario          → Emiliano Saltelli (founding · tributarista · cassazionista)
diritto-del-lavoro          → Fabiana Saltelli (giuslavorista · separazioni)
diritto-di-famiglia-lgbtq   → Antonia Battista (LGBTQ+ famiglia · COA)
```

### 4.2 — Implementation in single-competenza.php

Aggiungere dopo "Answer capsule" (sezione 3 del JSX):

```php
<?php if (saltelli_is_tier1_competenza(get_the_ID())) : 
    $slug = get_post_field('post_name', get_the_ID());
    $lawyer_map = [
        'diritto-tributario' => 'emiliano-saltelli',
        'diritto-del-lavoro' => 'fabiana-saltelli',
        'diritto-di-famiglia-lgbtq' => 'antonia-battista',
    ];
    $lawyer_slug = $lawyer_map[$slug] ?? null;
    if ($lawyer_slug):
        $lawyer = get_page_by_path($lawyer_slug, OBJECT, 'avvocato');
        if ($lawyer):
            $lawyer_id = $lawyer->ID;
            $lawyer_title = get_the_title($lawyer_id);
            $lawyer_role = get_post_meta($lawyer_id, 'ruolo', true) ?: 'Partner';
            $lawyer_photo = get_the_post_thumbnail_url($lawyer_id, 'medium');
?>
<aside class="sl-tier1__lawyer" data-reveal>
    <div class="sl-mono sl-tier1__lawyer-eyebrow">§ Avvocato di riferimento</div>
    <a href="<?php echo esc_url(get_permalink($lawyer_id)); ?>" class="sl-tier1__lawyer-card">
        <?php if ($lawyer_photo): ?>
            <img 
                src="<?php echo esc_url($lawyer_photo); ?>" 
                alt="<?php echo esc_attr($lawyer_title); ?>" 
                class="sl-tier1__lawyer-photo"
                width="80" height="80"
                loading="lazy"
            >
        <?php else: ?>
            <div class="sl-tier1__lawyer-placeholder" aria-hidden="true">
                <?php echo esc_html(strtoupper(substr($lawyer_title, 0, 1))); ?>
            </div>
        <?php endif; ?>
        <div class="sl-tier1__lawyer-info">
            <h3 class="sl-tier1__lawyer-name"><?php echo esc_html($lawyer_title); ?></h3>
            <p class="sl-tier1__lawyer-role sl-mono"><?php echo esc_html($lawyer_role); ?></p>
            <span class="sl-tier1__lawyer-link sl-link">Vai alla scheda →</span>
        </div>
    </a>
</aside>
<?php endif; endif; endif; ?>
```

### 4.3 — CSS `.sl-tier1__lawyer`

```css
/* === v0.24.0 TASK 4 — Tier-1 Avvocato di riferimento === */
.sl-tier1__lawyer {
    padding-block: 48px;
    border-block: 1px solid var(--border);
    margin-block: 56px;
}

.sl-tier1__lawyer-eyebrow {
    margin-bottom: 24px;
}

.sl-tier1__lawyer-card {
    display: grid;
    grid-template-columns: 80px 1fr;
    gap: 24px;
    align-items: center;
    text-decoration: none;
    color: inherit;
    transition: transform var(--dur-base) var(--ease-quart-out);
}

@media (hover: hover) {
    .sl-tier1__lawyer-card:hover {
        transform: translateX(8px);
    }
    .sl-tier1__lawyer-card:hover .sl-tier1__lawyer-name {
        color: var(--accent);
    }
}

.sl-tier1__lawyer-photo,
.sl-tier1__lawyer-placeholder {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 0;
}

.sl-tier1__lawyer-placeholder {
    background: var(--surface);
    border: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: var(--font-display);
    font-size: 32px;
    color: var(--primary);
}

.sl-tier1__lawyer-name {
    font-family: var(--font-display);
    font-size: 22px;
    line-height: 1.2;
    font-weight: 400;
    margin: 0 0 4px;
    color: var(--primary);
    transition: color var(--dur-fast) var(--ease-quart-out);
}

.sl-tier1__lawyer-role {
    font-size: 11px;
    color: var(--text-muted);
    margin: 0 0 8px;
}

.sl-tier1__lawyer-link {
    font-family: var(--font-body);
    font-size: 13px;
    color: var(--accent);
}
```

### 4.4 — Smoke

```bash
for SLUG in diritto-tributario diritto-del-lavoro diritto-di-famiglia-lgbtq; do
    HTML=$(curl -s "http://localhost:8080/competenze/$SLUG/?_=task4" -m 8)
    LAWYER=$(echo "$HTML" | grep -c 'sl-tier1__lawyer')
    NAME=$(echo "$HTML" | grep -oE 'sl-tier1__lawyer-name[^>]*>[^<]+' | sed 's/.*>//')
    printf "  /competenze/%-30s lawyer block:%s · %s\n" "$SLUG/" "$LAWYER" "$NAME"
done
```

### 4.5 — Commit

```bash
git add -A
git commit -m "feat(v0.24.0 task4): tier-1 'Avvocato di riferimento' card mapping (Emi/Fab/Ant)"
```

---

## TASK 5 — page.php is_page('costi') 5 sezioni mancanti (~25 min)

### 5.1 — Audit current state

```bash
HTML=$(curl -s "http://localhost:8080/costi/" -m 10)
echo "  sl-costi-w4: $(echo "$HTML" | grep -c 'sl-costi-w4')"
echo "  Hero asimmetrico: $(echo "$HTML" | grep -c 'sl-costi-w4__hero-grid')"
echo "  Come funziona: $(echo "$HTML" | grep -c 'sl-costi-w4__come')"
echo "  Scenari dopo 30min: $(echo "$HTML" | grep -c 'sl-costi-w4__scenari')"
echo "  Calcoliamo: $(echo "$HTML" | grep -c 'sl-costi-w4__calc')"
echo "  Trust grid: $(echo "$HTML" | grep -c 'sl-costi-w4__trust-grid')"
```

Audit Duccio: tutti = 0. Significa che v0.23.0 NON ha applicato Task B (costi).

### 5.2 — Implementation completa

Apri `saltelli-s2-costi.jsx` e replica markup ESATTO. 7 sezioni in ordine:

1. Hero asimmetrico 8fr/4fr
2. § 01 · Come funziona (3-col modalità Studio/Online/Telefonica)
3. § 02 · Tre scenari dopo i 30 min (4fr/8fr asimmetrico)
4. § 03 · Come calcoliamo i preventivi (drop-cap "T" + 3 cards)
5. § 04 · FAQ 5 Q (riusa `.sl-acc__*` da Task 3)
6. § 05 · Trust signals 4-col grid
7. CTA finale "La prima consulenza è gratuita. Sempre."

In `page.php` aggiungi blocco:

```php
<?php elseif (is_page('costi')) : ?>
    <article class="sl-costi-w4">
        
        <!-- Hero -->
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
                        <div class="sl-costi-w4__hero-trust-headline">
                            GRATUITA · 30 MINUTI · IN STUDIO O ONLINE
                        </div>
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
        
        <!-- § 01 Come funziona -->
        <section class="sl-costi-w4__come" data-reveal>
            <header class="sl-costi-w4__section-head">
                <div class="sl-mono">§ 01 · Come funziona la prima consulenza</div>
                <h2 class="sl-costi-w4__h2">Tre modalità.</h2>
            </header>
            <div class="sl-costi-w4__come-grid">
                <article class="sl-costi-w4__come-card">
                    <div class="sl-mono sl-costi-w4__come-num">01 / Modalità classica</div>
                    <h3 class="sl-costi-w4__come-title">Vieni a Chiaia</h3>
                    <p class="sl-costi-w4__come-body">
                        Via Vannella Gaetani 27, sala riunioni del nostro studio.
                        Lunedì-venerdì 09:30-18:30, su appuntamento.
                    </p>
                    <p class="sl-costi-w4__come-trust sl-mono">Caffè incluso</p>
                </article>
                <article class="sl-costi-w4__come-card">
                    <div class="sl-mono sl-costi-w4__come-num">02 / Modalità remota</div>
                    <h3 class="sl-costi-w4__come-title">Videocall riservata</h3>
                    <p class="sl-costi-w4__come-body">
                        Google Meet, Zoom o piattaforma a tua scelta. Ideale se vivi
                        fuori Napoli o per pratiche urgenti.
                    </p>
                    <p class="sl-costi-w4__come-trust sl-mono">Stesso valore, zero spostamento</p>
                </article>
                <article class="sl-costi-w4__come-card">
                    <div class="sl-mono sl-costi-w4__come-num">03 / Modalità rapida</div>
                    <h3 class="sl-costi-w4__come-title">Per casi semplici</h3>
                    <p class="sl-costi-w4__come-body">
                        Per situazioni che richiedono solo un primo orientamento o
                        verifica di percorribilità.
                    </p>
                    <p class="sl-costi-w4__come-trust sl-mono">Massimo 30 minuti</p>
                </article>
            </div>
        </section>
        
        <!-- § 02 Tre scenari dopo i 30 min (4fr/8fr) -->
        <section class="sl-costi-w4__scenari" data-reveal>
            <div class="sl-costi-w4__scenari-grid">
                <header class="sl-costi-w4__scenari-head">
                    <div class="sl-mono">§ 02 · Cosa succede dopo i 30 minuti</div>
                    <h2 class="sl-costi-w4__h2">Tre scenari possibili.</h2>
                </header>
                <ol class="sl-costi-w4__scenari-list">
                    <li class="sl-costi-w4__scenari-item">
                        <span class="sl-mono sl-costi-w4__scenari-num">01 / Non procediamo</span>
                        <p>Se la pratica non ha solidi presupposti, te lo diciamo subito. Ti suggeriamo un percorso alternativo o ti rimandiamo a un professionista più indicato.</p>
                        <p class="sl-mono sl-costi-w4__scenari-trust">Risparmio: 100% costi inutili</p>
                    </li>
                    <li class="sl-costi-w4__scenari-item">
                        <span class="sl-mono sl-costi-w4__scenari-num">02 / Pratica semplice — Tariffa forfettaria</span>
                        <p>Se la complessità è prevedibile, ti proponiamo un preventivo a forfait. Tutto incluso, nessuna sorpresa successiva.</p>
                        <p class="sl-mono sl-costi-w4__scenari-trust">Trasparenza: tariffa fissa concordata</p>
                    </li>
                    <li class="sl-costi-w4__scenari-item">
                        <span class="sl-mono sl-costi-w4__scenari-num">03 / Pratica complessa — Tariffa oraria</span>
                        <p>Se richiede analisi approfondita o iter giudiziale lungo, formuliamo preventivo orario con stima totale + check-in ogni 10 ore lavorate.</p>
                        <p class="sl-mono sl-costi-w4__scenari-trust">Controllo: budget capped + reportistica</p>
                    </li>
                </ol>
            </div>
        </section>
        
        <!-- § 03 Come calcoliamo i preventivi (6fr/6fr) -->
        <section class="sl-costi-w4__calc" data-reveal>
            <div class="sl-costi-w4__calc-grid">
                <div class="sl-costi-w4__calc-text">
                    <header class="sl-costi-w4__section-head">
                        <div class="sl-mono">§ 03 · Trasparenza preventivi</div>
                        <h2 class="sl-costi-w4__h2">Come calcoliamo i preventivi.</h2>
                    </header>
                    <p class="sl-costi-w4__calc-body">
                        Trasparenza è la nostra prima regola. I nostri preventivi considerano tre fattori: complessità della pratica (analisi atti, ricerca giurisprudenza, perizie tecniche), tempo stimato (ore di lavoro su atti, udienze, comunicazioni), probabilità di esito favorevole (incide sulla strategia consigliata).
                    </p>
                    <p class="sl-costi-w4__calc-body">
                        Quando possibile, lavoriamo a tariffa forfettaria: ti diamo un numero finale al primo incontro e quello rimane. Quando la complessità non lo permette, lavoriamo a tariffa oraria con budget cap concordato in anticipo. Niente fatturazione a sorpresa, mai.
                    </p>
                </div>
                <div class="sl-costi-w4__calc-cards">
                    <article class="sl-costi-w4__calc-card">
                        <div class="sl-mono">Fattore 1</div>
                        <h4>Analisi della pratica</h4>
                        <p>tipologia atti, normativa applicabile, giurisprudenza</p>
                    </article>
                    <article class="sl-costi-w4__calc-card">
                        <div class="sl-mono">Fattore 2</div>
                        <h4>Ore stimate</h4>
                        <p>redazione atti, udienze, comunicazioni, contraddittorio</p>
                    </article>
                    <article class="sl-costi-w4__calc-card">
                        <div class="sl-mono">Fattore 3</div>
                        <h4>Probabilità</h4>
                        <p>incide sulla strategia consigliata e sul timing</p>
                    </article>
                </div>
            </div>
        </section>
        
        <!-- § 04 FAQ 5 Q -->
        <section class="sl-costi-w4__faq" data-reveal>
            <header class="sl-costi-w4__section-head">
                <div class="sl-mono">§ 04 · Sui costi, in chiaro</div>
                <h2 class="sl-costi-w4__h2">Domande frequenti sui costi.</h2>
            </header>
            <div class="sl-acc">
                <?php
                $costi_faqs = [
                    ['q' => 'Quanto costa una pratica di diritto tributario?',
                     'a' => 'Range orientativo 800-3500€ a seconda di: tipologia atto (cartella semplice → ricorso CTP/CGT), importo contestato, necessità di periti. Esempio reale: opposizione cartella esattoriale 5000€ → forfait 1200€ + 200€ contributo unificato.'],
                    ['q' => 'Pagamento dilazionato è possibile?',
                     'a' => 'Sì per pratiche oltre 1500€. Concordiamo rate trimestrali in funzione del flusso atti. Trasparenza totale: nessun interesse, solo dilazione fisica.'],
                    ['q' => 'Se non vinco, devo comunque pagare?',
                     'a' => 'Sì. Le tariffe forensi prevedono onorari per il lavoro svolto, indipendentemente dall\'esito (è regola del Codice deontologico). Quello che possiamo fare: valutare seriamente in prima consulenza se la causa è effettivamente percorribile.'],
                    ['q' => 'Il primo incontro è davvero gratuito?',
                     'a' => 'Sì, sempre. Trenta minuti senza costi né obblighi. Se decidi di non procedere, abbiamo solo investito tempo. Il nostro tempo costa solo se decidiamo insieme di procedere.'],
                    ['q' => 'Recupero crediti: solo se vinciamo?',
                     'a' => 'Per pratiche specifiche di recupero crediti < 5000€ proponiamo success fee (X% sul recuperato + spese vive). Da concordare in prima consulenza in base alla concretezza del credito.'],
                ];
                foreach ($costi_faqs as $i => $faq):
                    $aria_id = 'costi-faq-' . $i;
                ?>
                    <div class="sl-acc__item">
                        <button class="sl-acc__btn" aria-expanded="false" aria-controls="<?php echo esc_attr($aria_id); ?>">
                            <span><?php echo esc_html($faq['q']); ?></span>
                            <span class="sl-acc__icon" aria-hidden="true">+</span>
                        </button>
                        <div class="sl-acc__panel" id="<?php echo esc_attr($aria_id); ?>" aria-hidden="true">
                            <div class="sl-acc__inner"><?php echo esc_html($faq['a']); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Schema FAQPage -->
            <script type="application/ld+json">
            <?php echo wp_json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'FAQPage',
                'mainEntity' => array_map(function($f) {
                    return ['@type' => 'Question', 'name' => $f['q'], 
                            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f['a']]];
                }, $costi_faqs),
            ], JSON_UNESCAPED_UNICODE); ?>
            </script>
        </section>
        
        <!-- § 05 Trust signals 4-col -->
        <section class="sl-costi-w4__trust-grid" data-reveal>
            <header class="sl-costi-w4__section-head">
                <div class="sl-mono">§ 05 · Garanzie professionali</div>
                <h2 class="sl-costi-w4__h2">I nostri valori in chiaro.</h2>
            </header>
            <div class="sl-costi-w4__trust-plates">
                <div class="sl-costi-w4__trust-plate">
                    <div class="sl-mono">Iscritti</div>
                    <p>Ordine Avvocati Napoli</p>
                </div>
                <div class="sl-costi-w4__trust-plate">
                    <div class="sl-mono">Partita IVA</div>
                    <p>06685101211</p>
                </div>
                <div class="sl-costi-w4__trust-plate">
                    <div class="sl-mono">Codice</div>
                    <p>Deontologico forense</p>
                </div>
                <div class="sl-costi-w4__trust-plate">
                    <div class="sl-mono">Riservatezza</div>
                    <p>Assoluta</p>
                </div>
            </div>
        </section>
        
        <!-- CTA finale -->
        <section class="sl-costi-w4__cta-final" data-reveal>
            <div class="sl-mono">§ Pronto?</div>
            <h2 class="sl-costi-w4__cta-h2">La prima consulenza è gratuita. Sempre.</h2>
            <p class="sl-costi-w4__cta-sub">
                Trenta minuti per ascoltarci, valutare insieme, capire se possiamo
                esserti utili. Senza obblighi e senza costi nascosti.
            </p>
            <a class="sl-btn sl-btn--primary" href="/contatti/">Prenota un incontro →</a>
            <p class="sl-mono sl-costi-w4__cta-trust">Risposta entro 24 ore · Riservatezza assoluta</p>
        </section>
        
    </article>
<?php endif; ?>
```

### 5.3 — CSS scope `.sl-costi-w4__*` complete

Aggiungi blocco `/* === v0.24.0 TASK 5 — Costi Sessione 2 === */` con tutte le regole responsive (vedi JSX per layout 8fr/4fr, 4fr/8fr, 6fr/6fr).

Pattern essenziali:
- Hero grid 8fr/4fr desktop, stack mobile
- Come funziona 3-col desktop, 1-col mobile
- Scenari 4fr/8fr asimmetrico
- Calcoliamo 6fr/6fr
- Trust grid 4-col desktop, 2-col tablet, 1-col mobile
- Drop-cap "T" su § 03 calc-body

### 5.4 — Smoke

```bash
HTML=$(curl -s "http://localhost:8080/costi/?_=task5" -m 10)
echo "  sl-costi-w4:           $(echo "$HTML" | grep -c 'sl-costi-w4\b')"
echo "  Hero grid:              $(echo "$HTML" | grep -c 'sl-costi-w4__hero-grid')"
echo "  Come funziona 3-col:   $(echo "$HTML" | grep -c 'sl-costi-w4__come-grid')"
echo "  Scenari:                $(echo "$HTML" | grep -c 'sl-costi-w4__scenari')"
echo "  Calcoliamo:             $(echo "$HTML" | grep -c 'sl-costi-w4__calc-grid')"
echo "  FAQ:                    $(echo "$HTML" | grep -c 'sl-costi-w4__faq')"
echo "  Trust grid:             $(echo "$HTML" | grep -c 'sl-costi-w4__trust-plates')"
echo "  CTA final:              $(echo "$HTML" | grep -c 'sl-costi-w4__cta-final')"
echo "  H2 'Tre modalità':      $(echo "$HTML" | grep -c 'Tre modalità')"
echo "  H2 'Tre scenari':       $(echo "$HTML" | grep -c 'Tre scenari')"
echo "  H2 'Come calcoliamo':   $(echo "$HTML" | grep -c 'Come calcoliamo')"
echo "  H2 'Domande sui costi': $(echo "$HTML" | grep -c 'Domande frequenti sui costi')"
echo "  schema FAQPage:         $(echo "$HTML" | grep -c 'FAQPage')"
```

### 5.5 — Commit

```bash
git add -A
git commit -m "feat(v0.24.0 task5): /costi/ Sessione 2 7 sezioni complete (hero+come+scenari+calc+faq+trust+cta)"
```

---

## TASK 6 — Form contatti success state + .sl-input (~10 min)

### 6.1 — Success state H3 "Grazie. Ci sentiamo entro 24 ore."

In `page.php` blocco `is_page('contatti')`, dentro form CF7 wrapper o post-form:

```html
<!-- Success message hidden by default, shown via JS on wpcf7mailsent -->
<div class="sl-contatti-w3__success" hidden>
    <div class="sl-mono">§ Inviato</div>
    <h3 class="sl-contatti-w3__success-h3">Grazie. Ci sentiamo entro 24 ore.</h3>
    <p class="sl-contatti-w3__success-text">
        La tua richiesta è stata inviata correttamente. Riceverai una conferma via email
        e ti ricontatteremo entro 24 ore lavorative.
    </p>
</div>
```

CSS:
```css
/* === v0.24.0 TASK 6 — Form success state === */
.sl-contatti-w3__success {
    padding: 48px;
    background: var(--surface);
    border-left: 3px solid var(--accent);
    text-align: left;
}

.sl-contatti-w3__success-h3 {
    font-family: var(--font-display);
    font-size: clamp(28px, 3vw, 36px);
    line-height: 1.2;
    color: var(--primary);
    margin: 16px 0;
}

.sl-contatti-w3__success-text {
    color: var(--text);
    line-height: 1.6;
    max-width: 56ch;
}
```

JS handler (in main.js, idempotente):
```javascript
// === v0.24.0 — CF7 success state ===
document.addEventListener('wpcf7mailsent', function() {
    const form = document.querySelector('.sl-contatti-w3 form');
    const success = document.querySelector('.sl-contatti-w3__success');
    if (form && success) {
        form.style.display = 'none';
        success.removeAttribute('hidden');
        success.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
}, false);
```

### 6.2 — `.sl-input` class injection su CF7 form fields

Hook PHP `wpcf7_form_elements`:

```php
add_filter('wpcf7_form_elements', function($html) {
    // Inject sl-input class su input/textarea CF7
    $html = preg_replace_callback(
        '/<(input|textarea|select)([^>]*?)class="([^"]*?wpcf7-form-control[^"]*)"/',
        function($match) {
            $tag = $match[1];
            $rest = $match[2];
            $existing_classes = $match[3];
            $new_classes = trim($existing_classes . ' sl-input');
            return '<' . $tag . $rest . 'class="' . $new_classes . '"';
        },
        $html
    );
    return $html;
});
```

Add a `inc/contact-form.php` o `functions.php`.

### 6.3 — Smoke

```bash
HTML=$(curl -s "http://localhost:8080/contatti/?_=task6" -m 10)
echo "  sl-input class injected: $(echo "$HTML" | grep -c 'class="[^"]*sl-input')"
echo "  Success state markup:    $(echo "$HTML" | grep -c 'sl-contatti-w3__success')"
```

### 6.4 — Commit

```bash
git add -A
git commit -m "feat(v0.24.0 task6): /contatti/ form success state H3 + .sl-input CF7 injection"
```

---

## TASK 7 — Drop-cap residui + sl-spinner + cleanup (~10 min)

### 7.1 — Drop-cap su tier-1 body content

Verifica che tier-1 prose `.sl-tier1__body > p:first-of-type::first-letter` abbia drop-cap (Task A v0.23.0 lo richiedeva).

Se mancante:
```css
/* === v0.24.0 TASK 7 — Drop-cap tier-1 body === */
.sl-tier1__body > p:first-of-type::first-letter {
    font-family: var(--font-display);
    font-size: 84px;
    line-height: 0.85;
    float: left;
    margin: 8px 16px 0 0;
    color: var(--primary);
    font-weight: 400;
}

@media (max-width: 767px) {
    .sl-tier1__body > p:first-of-type::first-letter {
        font-size: 60px;
        margin: 4px 12px 0 0;
    }
}

/* Drop-cap costi calc body */
.sl-costi-w4__calc-text > .sl-costi-w4__calc-body:first-of-type::first-letter {
    font-family: var(--font-display);
    font-size: 84px;
    line-height: 0.85;
    float: left;
    margin: 8px 16px 0 0;
    color: var(--primary);
    font-weight: 400;
}
```

### 7.2 — sl-spinner footer (newsletter loading)

Audit ha rilevato `.sl-spinner` mancante. Aggiungi in components.css:

```css
/* === v0.24.0 TASK 7 — Spinner mono editorial === */
.sl-spinner {
    display: inline-block;
    width: 12px;
    height: 12px;
    border: 1px solid currentColor;
    border-top-color: transparent;
    border-radius: 50%;
    animation: sl-spin 600ms linear infinite;
    vertical-align: middle;
}

@keyframes sl-spin {
    to { transform: rotate(360deg); }
}

@media (prefers-reduced-motion: reduce) {
    .sl-spinner {
        animation: none;
        border-top-color: currentColor;
        opacity: 0.4;
    }
}
```

### 7.3 — Smoke + commit

```bash
git add -A
git commit -m "feat(v0.24.0 task7): drop-cap tier-1 + costi calc + sl-spinner reduced-motion safe"
```

---

## TASK 8 — Bump + smoke + deploy + report finale (~10 min)

```bash
# Bump version
sed -i.bak "s/Version: [0-9.]\+.*/Version: 0.24.0-beta-pixel-perfect-final/" wp-content/themes/saltelli/style.css
sed -i.bak "s/SALTELLI_THEME_VERSION', '[^']*'/SALTELLI_THEME_VERSION', '0.24.0-beta-pixel-perfect-final'/" wp-content/themes/saltelli/functions.php
rm -f wp-content/themes/saltelli/{style.css,functions.php}.bak

# Cache flush local
docker compose run --rm wpcli cache flush
docker compose run --rm wpcli transient delete --all

# Final commit
git add -A
git commit -m "feat(v0.24.0): pixel-perfect final — 8 task complete (avvocati+tier-1+costi+contatti+polish)"
git push origin main

# Deploy droplet
rsync -avz wp-content/themes/saltelli/ deploy@178.62.207.50:/var/www/saltelli/htdocs/wp-content/themes/saltelli/
ssh deploy@178.62.207.50 "
    sudo -u www-data wp cache flush --path=/var/www/saltelli/htdocs
    sudo -u www-data wp transient delete --all --path=/var/www/saltelli/htdocs
"

# Smoke test 12 URL live
echo ""
echo "═══ SMOKE LIVE v0.24.0 ═══"
for URL in / /chi-siamo/ /avvocati/emiliano-saltelli/ /avvocati/fabiana-saltelli/ /avvocati/antonia-battista/ /avvocati/stefano-gaetano-tedesco/ /competenze/diritto-tributario/ /competenze/diritto-del-lavoro/ /competenze/diritto-di-famiglia-lgbtq/ /casi/ /contatti/ /costi/; do
    HTTP=$(curl -sL -o /dev/null -w "%{http_code}" "https://staging.studiolegalesaltelli.it$URL?_=v24" -m 10)
    echo "  $URL → HTTP $HTTP"
done

# Re-run audit pixel-perfect
python3 /tmp/audit_full_pixel.py 2>&1 | tail -30
```

### 8.1 — Report finale

`.claude/knowledge/design/sessione-2/v0.24.0-PIXEL-PERFECT-FINAL.md`:

```markdown
# v0.24.0 Pixel-Perfect Final
## Score: 8/8 task PASS

## Per task
- T1 ACF aree_competenza_correlate (4 lawyer): ✓ 24 entry assegnate
- T2 single-avvocato 'Sei aree di competenza': ✓ 4 lawyer + responsive
- T3 tier-1 FAQ accordion: ✓ 15 FAQ + FAQPage schema (3 tier-1)
- T4 tier-1 'Avvocato di riferimento' card: ✓ Emi/Fab/Ant mapping
- T5 /costi/ 7 sezioni Sessione 2 implementate: ✓
- T6 /contatti/ success state + .sl-input CF7 injection: ✓
- T7 drop-cap residui + sl-spinner reduced-motion: ✓
- T8 bump + smoke + deploy live: ✓

## JSX vs Live re-audit (post-fix)
- /avvocati/X (4 lawyer):  44% → 95%+
- /competenze/{tier1}/ (3): 40% → 95%+
- /costi/:                  43% → 95%+
- /contatti/:               66% → 90%+
- /chi-siamo/:              80% (mantenuto, decisione SEO)

## Score globale Sessione 2 implementation
- 17/18 page ≥ 90% match (era 9/18)
- 1/18 page strategicamente 80% (chi-siamo, decisione orchestrator)
- 0/18 gap critici (era 8/18)

## Issue residui
- Eventuali drift sub-pixel CSS (richiede review visuale Duccio)
- Newsletter Brevo endpoint ancora optimistic (separato, fix futuro)

## Next
GO walkthrough finale orchestrator visuale via Chrome MCP
o GO v1.0.0 production cut + DNS switch
```

Quando finito segnala "v0.24.0 deployed. Sessione 2 pixel-perfect 95%+ cross-page."

---

## 🆘 Se incontri imprevisti

```
- Plugin ACF non attivo → usa update_post_meta() come fallback (Task 1)
- meta key aree_competenza_correlate diversa → grep cross-template per identificare
- CF7 non standard markup → adatta hook wpcf7_form_elements o usa CSS scope diretto
- post_content WP costi rotto post-bypass → backup file salvato in /tmp
- Drop-cap su scope nested non visibile → aumenta specificità CSS
- Smoke test fail → check WP debug.log + nginx error.log droplet
```

Tempo totale stimato: **~120 min sequential**.

Buon lavoro. Quando finito, l'orchestrator esegue audit re-run + walkthrough finale via Chrome MCP per validazione visuale.
