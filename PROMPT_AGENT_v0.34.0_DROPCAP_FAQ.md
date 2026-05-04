# PROMPT v0.34.0 — Drop-cap Cleanup + FAQ Aggregator + CSS Specificity Reset

> **Per Claude Code in nuova sessione.** Tempo: ~50 min sequential.
> **PRECEDENZA:** v0.33.0 info pages + archive-avvocato refactor completato.

---

## 🎯 Tu sei

L'**Agente CSS Cleanup + FAQ Aggregator**. Audit Duccio post-v0.33.0 ha rilevato **4 problemi reali**:

```
🔴 PROBLEMA 1 — Drop-cap "flasha e sparisce" su alcune pagine
   CAUSA: animation reveal CSS .drop-cap-revealed esiste SOLO per .sl-studio
   homepage. Su altre pagine, drop-cap appare brevemente poi un repaint
   da altri elementi (hero word-stagger, scroll triggers) lo "fa sparire"
   visivamente. NON c'è meccanismo di reveal coordinato cross-template.

🔴 PROBLEMA 2 — Drop-cap DOPPIO su alcune pagine
   CAUSA: 75 first-letter rules nel CSS, di cui:
     .sl-attorney__bio:           10 rules
     .sl-competenza__prose:        14 rules
     .sl-tier1__body:               8 rules
     .sl-page__prose:              11 rules
   
   Specificity battle: regole cumulative attivano rendering 2 volte
   (es. parent + child entrambi pseudo-element first-letter).

🔴 PROBLEMA 3 — Pagina /domande-frequenti/ MANCANTE
   /faq/ esiste (HTTP 200) ma è generic page WP empty.
   Mancanza: FAQ aggregator dedicato con TUTTE le domande dal sito
   organizzate per topic (Costi, Tributario, Lavoro, LGBTQ+, Glossario).
   Audit GEO §4.3 raccomanda FAQPage Schema cumulativo per AI Overviews.

🔴 PROBLEMA 4 — Drop-cap NON visibile su /chi-siamo/
   Markup ha 2 elementi separati:
     a) .sl-chi-siamo__dropcap (span dedicato, statico)
     b) .sl-chi-siamo__lede-text first-letter rule (pseudo-element)
   Risultato: rendering ambiguo o frammentato.
```

---

## 📚 Letture obbligatorie

```
.claude/knowledge/design/sessione-2/saltelli-s2-glossario-legale.jsx (FAQ pattern)
.claude/knowledge/design/sessione-2/saltelli-s2-costi.jsx (FAQ accordion)

CLAUDE.md (handoff rule golden)

wp-content/themes/saltelli/
  ├── page.php (66KB — aggiungi blocco is_page('faq') o /domande-frequenti/)
  ├── single-avvocato.php (markup chi-siamo dropcap)
  └── assets/css/sections.css (target cleanup specificity)
```

---

## 🔒 Hard rules

| Rule | Decisione |
|---|---|
| **CSS cleanup**: rimuovi rules first-letter ridondanti, mantieni 1 per scope con !important | Specificity reset |
| **NO animation reveal** drop-cap cross-template (causa "flash") — solo statico instant render | Stabilità |
| **FAQ aggregator** = page custom is_page('faq' o 'domande-frequenti') | GEO/SEO requirement |
| **NESSUNA modifica tokens.css** | Locked |
| **NON sovrascrivere** _thumbnail_id Emiliano + bio_estesa Step D + post_content CPT | Content protetto |
| **CSS scope marker** `/* === v0.34.0 [task] === */` | Audit trail |
| Cache flush + smoke test post-deploy | Lezione |

---

## 🗺 Strategia esecuzione: SEQUENZIALE

```
Task 1 → CSS first-letter cleanup + 1 rule per scope             ~15 min
Task 2 → /chi-siamo/ drop-cap fix (rimuovi .sl-chi-siamo__dropcap span)  ~10 min
Task 3 → FAQ aggregator page /faq/ con tutte le domande           ~20 min
Task 4 → Bump + smoke + deploy                                     ~10 min
```

---

## TASK 1 — CSS first-letter cleanup + specificity reset (~15 min)

### 1.1 — Rimuovi rules ridondanti

Nel sections.css cerca **TUTTI** i blocchi `::first-letter` (75 totali) e mantieni SOLO 1 rule per scope con `!important`.

Strategia: il blocco "v0.33.0 — Drop-cap visibility fix !important" che hai aggiunto è quello da MANTENERE come fonte unica di verità. Tutte le altre rules first-letter PRECEDENTI (v0.21, v0.22, v0.27, v0.32) vanno commentate come `/* DEPRECATED v0.34.0 — superseded by unified rule */` oppure rimosse.

```bash
# Audit: estrai tutte le rules first-letter con linea
grep -n '::first-letter' wp-content/themes/saltelli/assets/css/sections.css | head -80
```

Per ogni rule precedente che NON è quella v0.33.0 unificata:
- Wrappa in `/* DEPRECATED v0.34.0: superseded by v0.33.0 unified rule below */`
- Oppure cancella se sicuri non causi rotture

UNIFIED RULE FINALE (da tenere come sola sorgente):

```css
/* === v0.34.0 — SINGLE SOURCE OF TRUTH drop-cap cross-template === */

.sl-attorney__bio-prose > p:first-of-type::first-letter,
.sl-attorney__bio > p:first-of-type::first-letter,
.sl-page__prose > p:first-of-type::first-letter,
.sl-competenza__prose > p:first-of-type::first-letter,
.sl-tier1__body > p:first-of-type::first-letter,
.sl-tier1__body > .sl-competenza__prose > p:first-of-type::first-letter,
.sl-costi-w4__calc-text > p:first-of-type::first-letter,
.sl-costi-w4__calc-prose > p:first-of-type::first-letter,
.sl-casi-w4__hero-lede > p:first-of-type::first-letter,
.sl-info-page__body > p:first-of-type::first-letter,
.sl-info-page__lede::first-letter,
.sl-team--archive .sl-team__archive-lede::first-letter,
.sl-attorney-archive__lede::first-letter {
    font-family: var(--font-display) !important;
    font-size: 84px !important;
    line-height: 0.85 !important;
    float: left !important;
    margin: 8px 16px 0 0 !important;
    color: var(--primary) !important;
    font-weight: 400 !important;
    /* No animation - sempre visibile, no flash */
    opacity: 1 !important;
    transform: none !important;
}

@media (max-width: 767px) {
    .sl-attorney__bio-prose > p:first-of-type::first-letter,
    .sl-attorney__bio > p:first-of-type::first-letter,
    .sl-page__prose > p:first-of-type::first-letter,
    .sl-competenza__prose > p:first-of-type::first-letter,
    .sl-tier1__body > p:first-of-type::first-letter,
    .sl-costi-w4__calc-text > p:first-of-type::first-letter,
    .sl-info-page__body > p:first-of-type::first-letter,
    .sl-team--archive .sl-team__archive-lede::first-letter {
        font-size: 60px !important;
        margin: 4px 12px 0 0 !important;
    }
}

/* HOMEPAGE EXCEPTION — animation reveal solo qui */
@media (min-width: 1024px) {
    .sl-studio__prose[data-drop-cap] > p:first-of-type::first-letter {
        opacity: 0;
        transform: scale(0.8);
        transition: opacity 600ms cubic-bezier(0.25, 1, 0.5, 1),
                    transform 600ms cubic-bezier(0.25, 1, 0.5, 1);
    }
    .sl-studio.drop-cap-revealed .sl-studio__prose[data-drop-cap] > p:first-of-type::first-letter,
    html:not(.js-reveal-ready) .sl-studio__prose[data-drop-cap] > p:first-of-type::first-letter {
        opacity: 1;
        transform: scale(1);
    }
}

@media (prefers-reduced-motion: reduce) {
    .sl-studio__prose[data-drop-cap] > p:first-of-type::first-letter {
        transition: opacity 200ms;
        transform: none;
    }
}
```

### 1.2 — Cleanup deprecated

Strategia approccio safe:
1. SEARCH globale `::first-letter` nel sections.css
2. Per OGNI block trovato (escluso il nuovo v0.34.0 unified + .sl-studio):
   - Sostituisci con `/* DEPRECATED v0.34.0: superseded by unified rule */`
3. Verifica file integro (no broken syntax)

```bash
# Pre-cleanup: salva backup
cp wp-content/themes/saltelli/assets/css/sections.css /tmp/sections.css.pre-v34-backup

# Cleanup tramite sed o script PHP, attento ai commenti multiriga
grep -c '::first-letter' wp-content/themes/saltelli/assets/css/sections.css
# Atteso pre-cleanup: ~75
# Atteso post-cleanup: ~14 (le 13 unified + 1 .sl-studio)
```

### 1.3 — Smoke verify

```bash
docker compose run --rm wpcli cache flush
for U in /chi-siamo/ /avvocati/emiliano-saltelli/ /competenze/diritto-tributario/ /casi/ /costi/ /come-lavoriamo/ /prima-consulenza/ /guide-gratuite/; do
    echo "  Test $U"
    # Verifica via curl che ci sia 1 first-letter rule scope per ogni page
done
```

L'utente Duccio verifica VISIVAMENTE Cmd+Shift+R che:
- 1 sola lettera grande visibile per pagina
- NO flash (drop-cap presente immediatamente)
- NO doppia lettera

---

## TASK 2 — /chi-siamo/ drop-cap fix (~10 min)

### 2.1 — Diagnosi current state

Markup live ha:
```html
<div class="sl-chi-siamo__lede-text">
    <span class="sl-chi-siamo__dropcap" aria-hidden="true">U</span>
    <p>Un atelier in senso napoletano...</p>
</div>
```

Problema: il `<span>` dedicato sta creando una "U" visiva separata dal `<p>` che ha già il pseudo-element `::first-letter`. Risultato: 2 elementi visivi competing.

### 2.2 — Fix strategy

Nel template che renderizza /chi-siamo/ (probabile page.php blocco is_page('chi-siamo')):

**RIMUOVI** lo `<span class="sl-chi-siamo__dropcap">U</span>` se presente.

Mantieni solo:
```html
<div class="sl-chi-siamo__lede-text">
    <p>Un atelier in senso napoletano...</p>
</div>
```

CSS già coperto in v0.34.0 unified rule. Il pseudo-element `.sl-chi-siamo__lede-text > p:first-of-type::first-letter` o `.sl-page__prose > p:first-of-type::first-letter` farà drop-cap automatico.

### 2.3 — Smoke verify

```bash
HTML=$(curl -s "http://localhost:8080/chi-siamo/?_=v34t2" -m 8)
echo "  span sl-chi-siamo__dropcap: $(echo "$HTML" | grep -c 'sl-chi-siamo__dropcap')"
echo "  Atteso: 0 (rimosso)"
echo ""
echo "  Verifica pseudo-element scope active: lede-text + p"
echo "$HTML" | grep -A 2 'sl-chi-siamo__lede-text\|sl-chi-siamo__lede' | head -10
```

---

## TASK 3 — FAQ Aggregator page /faq/ (~20 min)

### 3.1 — Page WP /faq/ esiste già

```bash
docker compose run --rm wpcli post list --post_type=page --name=faq --fields=ID,post_status --format=csv
```

Se esiste, ottimo. Se NO, crea:
```bash
docker compose run --rm wpcli post create \
    --post_type=page \
    --post_title="Domande frequenti" \
    --post_name=faq \
    --post_status=publish \
    --post_content=""
```

Nota: usa slug `faq` (esistente) NON `domande-frequenti`. URL canonical: `/faq/`.

### 3.2 — Aggiungi blocco is_page('faq') in page.php

Inserisci dopo gli altri blocchi `is_page()`:

```php
<?php elseif (is_page('faq')) : ?>

    <article class="sl-faq-aggregator">
        
        <!-- HERO -->
        <header class="sl-faq-aggregator__hero">
            <?php saltelli_render_breadcrumb('page'); ?>
            <div class="sl-mono sl-faq-aggregator__eyebrow">§ Risorse · Domande frequenti</div>
            <h1 class="sl-faq-aggregator__h1" data-split-reveal>
                Domande <em>frequenti.</em>
            </h1>
            <p class="sl-faq-aggregator__lede">
                Le domande più frequenti raccolte da clienti, ricerca AI e contatti dello Studio. Organizzate per area di pratica e per topic editoriali.
            </p>
        </header>
        
        <!-- TOC -->
        <nav class="sl-faq-aggregator__toc" aria-label="Indice domande">
            <div class="sl-mono sl-faq-aggregator__toc-eyebrow">§ Indice</div>
            <ul class="sl-faq-aggregator__toc-list">
                <li><a href="#tributario">Diritto tributario</a></li>
                <li><a href="#lavoro">Diritto del lavoro</a></li>
                <li><a href="#famiglia-lgbtq">Famiglia LGBTQ+</a></li>
                <li><a href="#costi">Costi e preventivi</a></li>
                <li><a href="#metodo">Metodo dello Studio</a></li>
                <li><a href="#prima-consulenza">Prima consulenza</a></li>
            </ul>
        </nav>
        
        <!-- TOPIC GROUPS — riusa accordion .sl-acc__* -->
        <?php
        // FAQ aggregate cross-site (raccolte dalle altre page)
        // Source: tier-1 page + costi + glossario
        $faq_groups = [
            'tributario' => [
                'title' => 'Diritto tributario',
                'cluster' => 'Per le imprese',
                'link' => '/competenze/diritto-tributario/',
                'items' => [
                    ['Quando conviene impugnare una cartella esattoriale?', 'Le cartelle vanno impugnate entro 60 giorni dalla notifica davanti alla Commissione Tributaria competente. Lo Studio valuta gratuitamente la fondatezza dell\'impugnazione nel primo incontro.'],
                    ['Cosa fare se l\'Agenzia delle Entrate avvia un accertamento sintetico?', 'Prima dell\'accertamento si apre un contraddittorio preventivo: è la fase più delicata. Documentare correttamente la propria posizione in questa sede può evitare il contenzioso.'],
                    ['Quali sono i tempi medi di un contenzioso tributario?', 'Primo grado in CTP: 12-18 mesi. Appello in CTR: ulteriori 18-24 mesi. Cassazione: 24-36 mesi. La sospensione cautelare è quasi sempre concedibile.'],
                    ['Si possono rateizzare le somme dovute?', 'Sì, fino a 72 rate mensili (120 in casi di grave difficoltà). Lo Studio assiste anche nella negoziazione dei piani di rateizzazione con AGE Riscossione.'],
                    ['Quanto costa un contenzioso tributario?', 'Il primo incontro è gratuito. Il preventivo è scritto, fisso o a percentuale del beneficio. Le parcelle seguono i parametri ministeriali, sempre concordate prima del mandato.'],
                ],
            ],
            'lavoro' => [
                'title' => 'Diritto del lavoro',
                'cluster' => 'Per i privati',
                'link' => '/competenze/diritto-del-lavoro/',
                'items' => [
                    ['Il licenziamento è impugnabile? Entro quando?', 'Sì, entro 60 giorni dalla comunicazione (180 giorni se per contestazione discriminatoria). Lo Studio valuta gratuitamente la fondatezza nel primo incontro.'],
                    ['Cos\'è il mobbing e come si dimostra?', 'Il mobbing richiede prova documentale di vessazioni reiterate. Servono messaggi, testimoni, note mediche. Lo Studio coordina la raccolta probatoria.'],
                    ['Cosa cambia tra contestazione disciplinare e licenziamento?', 'La contestazione è il primo step: se non si difende correttamente entro 5 giorni si arriva al provvedimento. Difesa scritta entro 5gg è critica.'],
                    ['Sono lavoratore autonomo: che tutele ho?', 'Anche il lavoro autonomo gode di tutele crescenti (legge 81/2017). Equo compenso, recesso illegittimo, dipendenza economica.'],
                    ['INPS contestato: cosa fare?', 'Ricorso amministrativo entro 90 giorni, poi giudiziale entro un anno. Lo Studio assiste in entrambe le sedi.'],
                ],
            ],
            'famiglia-lgbtq' => [
                'title' => 'Diritto di famiglia LGBTQ+',
                'cluster' => 'Per i privati',
                'link' => '/competenze/diritto-di-famiglia-lgbtq/',
                'items' => [
                    ['L\'unione civile dà gli stessi diritti del matrimonio?', 'L\'unione civile (legge 76/2016, "DDL Cirinnà") dà la maggior parte dei diritti del matrimonio salvo adozione e fecondazione assistita. Trascrizione, eredità, pensione di reversibilità: sì.'],
                    ['Trascrizione di nascita di figlio nato all\'estero (PMA o GPA): è possibile?', 'Dipende dalla giurisdizione di nascita. La Cassazione 38162/2022 e successive aprono spiragli per trascrizione integrale. Lo Studio ha ottenuto il primo riconoscimento in Campania nel 2023.'],
                    ['Stepchild adoption: in quali casi è possibile?', 'L\'adozione coparentale (art. 44 lettera d L.184/1983) è possibile su minore già genitore biologico del partner. Procedura giudiziale, esito favorevole consolidato post-Cassazione 2014.'],
                    ['Cosa succede in caso di separazione tra coppie LGBTQ+?', 'Per unioni civili: scioglimento giudiziale come divorzio. Per coppie di fatto: tutela patrimoniale separata. Affido figli: principio del "miglior interesse del minore".'],
                    ['L\'identità di genere è riconosciuta legalmente?', 'Sì, legge 164/1982 (rettifica anagrafica). Procedura giudiziale o amministrativa (post-Cassazione 15138/2015). Lo Studio assiste in tutti i passaggi.'],
                ],
            ],
            'costi' => [
                'title' => 'Costi e preventivi',
                'cluster' => 'Trasparenza',
                'link' => '/costi/',
                'items' => [
                    ['Quanto costa una pratica di diritto tributario?', 'Range orientativo 800-3500€ a seconda di tipologia atto, importo contestato, necessità di periti. Esempio reale: opposizione cartella esattoriale 5000€ → forfait 1200€ + 200€ contributo unificato.'],
                    ['Pagamento dilazionato è possibile?', 'Sì per pratiche oltre 1500€. Concordiamo rate trimestrali in funzione del flusso atti. Trasparenza totale: nessun interesse, solo dilazione fisica.'],
                    ['Se non vinco, devo comunque pagare?', 'Sì. Le tariffe forensi prevedono onorari per il lavoro svolto, indipendentemente dall\'esito (è regola del Codice deontologico). Quello che possiamo fare: valutare seriamente in prima consulenza se la causa è effettivamente percorribile.'],
                    ['Il primo incontro è davvero gratuito?', 'Sì, sempre. Trenta minuti senza costi né obblighi. Se decidi di non procedere, abbiamo solo investito tempo.'],
                    ['Recupero crediti: solo se vinciamo?', 'Per pratiche specifiche di recupero crediti < 5000€ proponiamo success fee (X% sul recuperato + spese vive).'],
                ],
            ],
            'metodo' => [
                'title' => 'Metodo dello Studio',
                'cluster' => 'Come lavoriamo',
                'link' => '/come-lavoriamo/',
                'items' => [
                    ['Come funziona il primo incontro?', 'Trenta minuti gratuiti, in studio o online. Ascoltiamo la pratica, valutiamo presupposti e probabilità di esito. Solo dopo, eventuale preventivo personalizzato.'],
                    ['Chi seguirà la mia pratica?', 'Personalmente uno dei quattro avvocati. Niente call-center, niente passaggi. Conosciamo i nomi dei clienti, il loro lavoro, la loro storia.'],
                    ['Posso avere aggiornamenti regolari?', 'Sì. Per pratiche complesse, check-in ogni 10 ore lavorate con report scritto. Per forfait, aggiornamenti su richiesta o ad ogni atto rilevante.'],
                    ['Come comunicate? Email, telefono, WhatsApp?', 'Scegli tu. Email per documentazione, telefono per emergenze, WhatsApp per messaggi brevi. Riservatezza assoluta su tutti i canali.'],
                ],
            ],
            'prima-consulenza' => [
                'title' => 'Prima consulenza',
                'cluster' => 'Servizio',
                'link' => '/prima-consulenza/',
                'items' => [
                    ['Quanto dura la prima consulenza?', 'Trenta minuti per inquadrare la pratica. Per situazioni complesse possiamo estenderla, ma resta gratuita.'],
                    ['Devo portare documenti?', 'Sì se possibile: contratti, atti, comunicazioni, qualsiasi cosa pertinente. Anche fotografie da smartphone vanno bene per il primo orientamento.'],
                    ['È vincolante? Posso non procedere dopo?', 'Nessun obbligo. Se la causa non è percorribile o se preferisci un altro avvocato, ti diamo onestamente il nostro parere senza costi.'],
                    ['Posso fare la prima consulenza online?', 'Sì, su Google Meet, Zoom o piattaforma a tua scelta. Stesso valore della consulenza in studio, zero spostamento.'],
                ],
            ],
        ];
        
        // Aggregate FAQPage Schema
        $all_faqs = [];
        foreach ($faq_groups as $group) {
            foreach ($group['items'] as $item) {
                $all_faqs[] = [
                    '@type' => 'Question',
                    'name' => $item[0],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $item[1],
                    ],
                ];
            }
        }
        ?>
        
        <!-- TOPIC SECTIONS -->
        <div class="sl-faq-aggregator__topics">
            <?php foreach ($faq_groups as $slug => $group) : ?>
                <section id="<?php echo esc_attr($slug); ?>" class="sl-faq-aggregator__topic">
                    <header class="sl-faq-aggregator__topic-head">
                        <div class="sl-mono"><?php echo esc_html($group['cluster']); ?></div>
                        <h2><?php echo esc_html($group['title']); ?></h2>
                        <a href="<?php echo esc_url($group['link']); ?>" class="sl-link sl-link--accent">
                            Vai alla sezione →
                        </a>
                    </header>
                    
                    <div class="sl-acc">
                        <?php foreach ($group['items'] as $i => $item) :
                            $aria_id = 'faq-' . $slug . '-' . $i;
                        ?>
                            <div class="sl-acc__item">
                                <button class="sl-acc__btn" aria-expanded="false" aria-controls="<?php echo esc_attr($aria_id); ?>">
                                    <span><?php echo esc_html($item[0]); ?></span>
                                    <span class="sl-acc__icon" aria-hidden="true">+</span>
                                </button>
                                <div class="sl-acc__panel" id="<?php echo esc_attr($aria_id); ?>" aria-hidden="true">
                                    <div class="sl-acc__inner"><?php echo wp_kses_post($item[1]); ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>
        </div>
        
        <!-- AGGREGATE FAQPage Schema (audit GEO §4.3) -->
        <script type="application/ld+json">
        <?php echo wp_json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            '@id' => get_permalink() . '#faq',
            'mainEntity' => $all_faqs,
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>
        </script>
        
        <!-- CTA finale -->
        <section class="sl-faq-aggregator__cta-final">
            <div class="sl-mono">§ Non hai trovato la tua domanda?</div>
            <h2>Prenota un incontro <em>conoscitivo gratuito.</em></h2>
            <p>Trenta minuti per ascoltarci, valutare insieme, capire se possiamo esserti utili.</p>
            <a href="/contatti/" class="sl-btn sl-btn--primary">Prenota un incontro →</a>
        </section>
        
    </article>

<?php endif; ?>
```

### 3.3 — CSS scope `.sl-faq-aggregator__*`

```css
/* === v0.34.0 — FAQ Aggregator template === */

.sl-faq-aggregator {
    max-width: 1440px;
    margin-inline: auto;
    padding: clamp(96px, 10vw, 120px) clamp(24px, 5vw, 96px) 0;
}

.sl-faq-aggregator__hero {
    margin-bottom: 80px;
    max-width: 720px;
}

.sl-faq-aggregator__eyebrow {
    margin-bottom: 24px;
}

.sl-faq-aggregator__h1 {
    font-family: var(--font-display);
    font-size: clamp(56px, 7vw, 96px);
    line-height: 0.98;
    letter-spacing: -0.025em;
    font-weight: 400;
    color: var(--primary);
    margin: 0 0 32px;
}

.sl-faq-aggregator__h1 em {
    font-style: italic;
    color: var(--text-muted);
}

.sl-faq-aggregator__lede {
    font-family: var(--font-display);
    font-size: 22px;
    font-style: italic;
    line-height: 1.5;
    color: var(--text);
    margin: 0;
    max-width: 56ch;
}

/* TOC */
.sl-faq-aggregator__toc {
    background: var(--surface);
    padding: 32px;
    margin-bottom: 96px;
    border-left: 3px solid var(--accent);
}

.sl-faq-aggregator__toc-eyebrow {
    margin-bottom: 16px;
}

.sl-faq-aggregator__toc-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: grid;
    gap: 12px;
}

@media (min-width: 768px) {
    .sl-faq-aggregator__toc-list {
        grid-template-columns: repeat(2, 1fr);
        gap: 16px 32px;
    }
}

@media (min-width: 1024px) {
    .sl-faq-aggregator__toc-list {
        grid-template-columns: repeat(3, 1fr);
    }
}

.sl-faq-aggregator__toc-list a {
    font-family: var(--font-mono);
    font-size: 12px;
    letter-spacing: 0.06em;
    color: var(--primary);
    text-decoration: none;
    border-bottom: 1px solid transparent;
    transition: color var(--dur-fast) var(--ease-editorial),
                border-color var(--dur-fast) var(--ease-editorial);
}

.sl-faq-aggregator__toc-list a:hover {
    color: var(--accent);
    border-bottom-color: var(--accent);
}

/* Topic sections */
.sl-faq-aggregator__topics {
    display: grid;
    gap: 96px;
    margin-bottom: 128px;
}

.sl-faq-aggregator__topic {
    scroll-margin-top: 100px;
}

.sl-faq-aggregator__topic-head {
    margin-bottom: 32px;
    padding-bottom: 24px;
    border-bottom: 1px solid var(--accent);
}

.sl-faq-aggregator__topic-head .sl-mono {
    margin-bottom: 8px;
    color: var(--text-muted);
}

.sl-faq-aggregator__topic-head h2 {
    font-family: var(--font-display);
    font-size: clamp(32px, 4vw, 48px);
    line-height: 1.1;
    letter-spacing: -0.02em;
    font-weight: 400;
    color: var(--primary);
    margin: 0 0 16px;
}

.sl-faq-aggregator__topic-head a {
    font-size: 14px;
}

/* CTA Finale dark */
.sl-faq-aggregator__cta-final {
    background: var(--primary);
    color: var(--background);
    text-align: center;
    padding: clamp(64px, 8vw, 128px) clamp(24px, 5vw, 96px);
    margin: 0 calc(-1 * clamp(24px, 5vw, 96px));
}

.sl-faq-aggregator__cta-final .sl-mono {
    color: var(--accent);
    margin-bottom: 24px;
}

.sl-faq-aggregator__cta-final h2 {
    font-family: var(--font-display);
    font-size: clamp(40px, 5vw, 72px);
    line-height: 1.05;
    letter-spacing: -0.02em;
    font-weight: 400;
    color: var(--background);
    margin: 0 0 24px;
    max-width: 18ch;
    margin-inline: auto;
}

.sl-faq-aggregator__cta-final h2 em {
    font-style: italic;
    color: var(--accent);
}

.sl-faq-aggregator__cta-final p {
    font-size: 18px;
    line-height: 1.6;
    color: rgba(250, 250, 248, 0.85);
    margin: 0 0 40px;
    max-width: 50ch;
    margin-inline: auto;
}

.sl-faq-aggregator__cta-final .sl-btn--primary {
    background: var(--accent);
    color: var(--primary);
    border-color: var(--accent);
}
```

### 3.4 — Update menu /faq/ visible

Verifica menu primary contains link a /faq/. Se no:

```bash
docker compose run --rm wpcli menu item add-post primary <FAQ_PAGE_ID>
```

Oppure aggiungi manualmente in WP-Admin → Menu.

### 3.5 — Smoke verify

```bash
HTML=$(curl -s "http://localhost:8080/faq/?_=v34t3" -m 8)
echo "  sl-faq-aggregator wrap:  $(echo "$HTML" | grep -c 'sl-faq-aggregator\b')"
echo "  6 topic sections:         $(echo "$HTML" | grep -c 'sl-faq-aggregator__topic\b')"
echo "  TOC visible:              $(echo "$HTML" | grep -c 'sl-faq-aggregator__toc')"
echo "  Aggregate FAQPage schema: $(echo "$HTML" | grep -c '\"FAQPage\"')"
echo "  Total Question schema:    $(echo "$HTML" | grep -c '\"@type\":\"Question\"')"
```

Atteso: wrap=1, topic=6, TOC=1, FAQPage=1, Question≥28.

---

## TASK 4 — Bump + smoke + deploy + report finale (~10 min)

```bash
sed -i.bak 's/Version: [0-9.]\+.*/Version: 0.34.0-beta-dropcap-cleanup-faq-aggregator/' wp-content/themes/saltelli/style.css
sed -i.bak "s/SALTELLI_THEME_VERSION', '[^']*'/SALTELLI_THEME_VERSION', '0.34.0-beta-dropcap-cleanup-faq-aggregator'/" wp-content/themes/saltelli/functions.php
rm -f wp-content/themes/saltelli/{style.css,functions.php}.bak

docker compose run --rm wpcli cache flush

git add -A
git commit -m "feat(v0.34.0): drop-cap CSS specificity reset + FAQ aggregator + chi-siamo dropcap fix"
git push origin main

rsync -avz wp-content/themes/saltelli/ deploy@178.62.207.50:/var/www/saltelli/htdocs/wp-content/themes/saltelli/
ssh deploy@178.62.207.50 "
    sudo -u www-data wp cache flush --path=/var/www/saltelli/htdocs
    sudo -u www-data wp transient delete --all --path=/var/www/saltelli/htdocs
"

# Smoke test 10 URL
echo "═══ SMOKE LIVE v0.34.0 ═══"
for URL in /chi-siamo/ /avvocati/ /avvocati/emiliano-saltelli/ /casi/ /contatti/ /costi/ /faq/ /come-lavoriamo/ /prima-consulenza/ /competenze/diritto-tributario/; do
    HTTP=$(curl -sL -o /dev/null -w "%{http_code}" "https://staging.studiolegalesaltelli.it$URL?_=v34" -m 10)
    echo "  $URL → HTTP $HTTP"
done
```

Report `.claude/knowledge/design/sessione-2/v0.34.0-DROPCAP-FAQ.md`:

```markdown
# v0.34.0 Drop-cap Cleanup + FAQ Aggregator
## Score: 4/4 task PASS

## Per task
- T1 CSS first-letter cleanup (75 rules → ~14 unified): ✓
- T2 /chi-siamo/ dropcap span removed: ✓
- T3 /faq/ aggregator (6 topic, 28+ Q, FAQPage schema): ✓
- T4 Bump + smoke + deploy: ✓

## Drop-cap cross-template
- Tutte le 13 scope unificate sotto 1 sola rule v0.34.0
- !important strategico
- NO animation cross-template (no flash)
- Eccezione homepage .sl-studio (animation reveal mantenuta)

## /faq/ Aggregator
- 6 topic sections: tributario, lavoro, LGBTQ+, costi, metodo, prima-consulenza
- 28+ domande aggregate (audit GEO §4.3 compliance)
- TOC sticky
- FAQPage Schema cumulativo per AI Overviews
- CTA finale dark

## Next
GO walkthrough finale Duccio → v1.0.0 production cut
```

Quando finito segnala "v0.34.0 deployed".

---

## 🆘 Se incontri imprevisti

```
- 75 first-letter rules trovate → cleanup va fatto con cura, una alla volta
- Page WP /faq/ non esiste → crea via WP-CLI
- /faq/ slug occupato altrove → usa /domande-frequenti/ alternative
- chi-siamo template non trovato → grep is_page('chi-siamo') in page.php
- Schema FAQPage validation fail → strip <p>, <em> da text answer
```

Tempo totale: ~50 min sequential.

Buon lavoro.
