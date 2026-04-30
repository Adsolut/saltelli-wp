# Prompt — Audit Alignment Agent (PRE-DEMO · 30-45 min)

> **Per Claude Code in nuova sessione.** Apri `saltelli-wp/`, leggi questo file, eseguilo. Tempo previsto: 30-45 minuti.
> **PRECEDENZA:** Step D (Content Migration) completato. v0.5.1-beta-content-fix o successiva.
> **CRITICITÀ:** la presentazione cliente è **OGGI**. Lavoro chirurgico, niente refactor strutturali, ZERO rischio di regressione.

---

## Tu sei

L'**Audit Alignment Agent**. L'orchestratore (Claude in chat) ha riletto i due audit consegnati al cliente in fase contrattuale (`AUDIT-COMPLETO-STUDIO-LEGALE-SALTELLI.md` e `audit-cro-studiolegalesaltelli.md`) e ha individuato **3 gap critici** dove la build attuale non riflette ancora le raccomandazioni che il cliente si aspetta di vedere implementate.

Il tuo lavoro: chiudere questi 3 gap **prima della presentazione**. Tutto il resto (testimonianze, booking calendar reale, Brevo automation, lead magnet) resta in roadmap successiva — NON è scope oggi.

---

## Letture obbligatorie (in ordine, prima di scrivere codice)

1. `CLAUDE.md` — hard constraints (sopratutto: 1 H1 per pagina, design system locked, ACF fallback)
2. `.claude/knowledge/project-context.json` — `team_members`, `practice_areas`, `strategic_focus_decision`
3. `.claude/knowledge/design/sessione-1/reports/content-migration/REPORT.md` — sapere cosa è già stato popolato
4. `wp-content/themes/saltelli/header.php` + `footer.php` — per integrare il menu nuovo
5. `wp-content/themes/saltelli/inc/cpt-competenza.php` — verificare che la tassonomia `tipo-area` sia già registrata (Theme Architect Step C l'aveva inclusa)
6. `wp-content/themes/saltelli/page.php` — template che servirà per la nuova pagina /costi/

---

## Hard rules (non negoziabili oggi)

| Rule | Reason |
|---|---|
| Design tokens NON si toccano | Locked dall'inizio, presentazione oggi |
| Niente nuovi CPT, niente refactor template | Rischio regressione, tempo limitato |
| Mai sovrascrivere meta `_thumbnail_id` o `bio_estesa` esistenti | Step D ha popolato bio reali, foto Emiliano |
| Tutti gli edit visibili devono essere reversibili in 1 commit | Se la presentazione dovesse iniziare in 30 min e qualcosa si rompe, dobbiamo poter `git revert` |
| Idempotenza: se rilanci lo script 2 volte, non duplica menu/pagine | Stabilità |
| Mai disattivare plugin durante questo run | Bisogna rimanere prevedibili (Yoast, Honeypot, redirection, Saltelli, SiteGround) |
| Output in italiano. Mai inglese sui contenuti visibili al cliente | Cliente italiano |

---

## TASK 1 — Sitemap Privati / Imprese / Contenzioso (15 min)

### Obiettivo
Cambiare il menu primary da 6 voci flat a una struttura con **dropdown su "Aree di Pratica"** che raggruppa le 19 competenze in 3 macro-categorie come da audit CRO §9.7.

### Step 1.1 — Termini tassonomia `tipo-area`

Verifica/crea i 3 termini della tassonomia `tipo-area` (CPT competenza):

```bash
docker compose run --rm wpcli term list tipo-area --fields=name,slug,count
```

Se mancano, crea:

```bash
docker compose run --rm wpcli term create tipo-area "Per i Privati" --slug=privati --description="Diritto di famiglia, eredità, lavoro, risarcimento danni, immigrazione, penale"
docker compose run --rm wpcli term create tipo-area "Per le Imprese" --slug=imprese --description="Societario, recupero crediti, contrattualistica, bancario, tributario"
docker compose run --rm wpcli term create tipo-area "Contenzioso Amministrativo" --slug=contenzioso --description="Cartelle esattoriali, ricorsi, condominiale, amministrativo"
docker compose run --rm wpcli term create tipo-area "Altri servizi" --slug=altri --description="Domiciliazione d'impresa, consulenze online, infortunistica, previdenza"
```

### Step 1.2 — Mapping 19 competenze → termini

Esegui questo mapping (basato su audit CRO §9.7 + nostro tier-1 strategy):

```bash
# Helper: trova ID CPT competenza per slug, e termine per slug
declare -A MAPPING=(
  # PRIVATI (6)
  ["diritto-di-famiglia"]="privati"
  ["diritto-di-famiglia-lgbtq"]="privati"
  ["diritto-del-lavoro"]="privati"
  ["responsabilita-medica"]="privati"
  ["diritto-dellimmigrazione"]="privati"
  ["diritto-penale"]="privati"
  ["risarcimento-danni"]="privati"
  ["responsabilita-civile"]="privati"
  ["diritto-delle-successioni"]="privati"

  # IMPRESE (5)
  ["recupero-crediti"]="imprese"
  ["diritto-bancario"]="imprese"
  ["diritto-tributario"]="imprese"
  ["diritto-commerciale"]="imprese"
  ["diritto-delle-assicurazioni"]="imprese"

  # CONTENZIOSO (4)
  ["cartelle-esattoriali-e-multe"]="contenzioso"
  ["diritto-condominiale"]="contenzioso"
  ["diritto-amministrativo"]="contenzioso"
  ["diritto-previdenziale"]="contenzioso"

  # ALTRI (per completezza, per ora 4)
  ["domiciliazione-dimpresa"]="altri"
  ["consulenze-online"]="altri"
)

for SLUG in "${!MAPPING[@]}"; do
  TERM="${MAPPING[$SLUG]}"
  CPT_ID=$(docker compose run --rm wpcli post list --post_type=competenza --name="$SLUG" --field=ID 2>&1 | grep -oE '^[0-9]+' | head -1)
  if [ -n "$CPT_ID" ]; then
    docker compose run --rm wpcli post term set "$CPT_ID" tipo-area "$TERM" 2>&1 | tail -1
    echo "  $SLUG → $TERM (CPT $CPT_ID)"
  fi
done
```

**Caveat:** alcuni slug nel DB possono divergere (es. `diritto-dell-immigrazione` vs `diritto-dellimmigrazione`). Verifica con:

```bash
docker compose run --rm wpcli post list --post_type=competenza --fields=name --format=csv | tail -25
```

E adatta il mapping ai veri slug. **Non saltare nessuna delle 19 competenze**: ognuna deve avere almeno un termine.

### Step 1.3 — Menu nuovo "Saltelli Header"

Cancella il menu corrente e ricreane uno nuovo con dropdown:

```bash
# Cancella e ricrea
docker compose run --rm wpcli menu delete saltelli-header 2>&1 | tail -1
docker compose run --rm wpcli menu create "Saltelli Header" 2>&1 | tail -1

# Voci top-level
docker compose run --rm wpcli menu item add-custom saltelli-header "Studio" "/lo-studio/" 2>&1 | tail -1
docker compose run --rm wpcli menu item add-custom saltelli-header "Avvocati" "/avvocati/" 2>&1 | tail -1

# Aree di Pratica (parent — è il dropdown)
PARENT_ID=$(docker compose run --rm wpcli menu item add-custom saltelli-header "Aree di Pratica" "/competenze/" --porcelain 2>&1 | grep -oE '[0-9]+' | tail -1)
echo "PARENT menu item ID: $PARENT_ID"

# Sotto-voci che puntano a archive filtrato per termine tipo-area
docker compose run --rm wpcli menu item add-custom saltelli-header "Per i Privati" "/tipo-area/privati/" --parent-id="$PARENT_ID" 2>&1 | tail -1
docker compose run --rm wpcli menu item add-custom saltelli-header "Per le Imprese" "/tipo-area/imprese/" --parent-id="$PARENT_ID" 2>&1 | tail -1
docker compose run --rm wpcli menu item add-custom saltelli-header "Contenzioso" "/tipo-area/contenzioso/" --parent-id="$PARENT_ID" 2>&1 | tail -1
docker compose run --rm wpcli menu item add-custom saltelli-header "Tutte le aree" "/competenze/" --parent-id="$PARENT_ID" 2>&1 | tail -1

# Resto del menu
docker compose run --rm wpcli menu item add-custom saltelli-header "Casi" "/casi/" 2>&1 | tail -1
docker compose run --rm wpcli menu item add-custom saltelli-header "Costi" "/costi/" 2>&1 | tail -1   # nuova! verrà popolata in Task 3
docker compose run --rm wpcli menu item add-custom saltelli-header "Editoriale" "/blog/" 2>&1 | tail -1
docker compose run --rm wpcli menu item add-custom saltelli-header "Contatti" "/contatti/" 2>&1 | tail -1

# Assegna location
docker compose run --rm wpcli menu location assign saltelli-header primary 2>&1 | tail -1

docker compose run --rm wpcli rewrite flush --hard 2>&1 | tail -1
```

### Step 1.4 — CSS dropdown sul menu (se non esiste)

Verifica che `assets/css/sections.css` abbia regole per `.sl-header__nav .sub-menu` (WordPress emette automaticamente questa classe sui dropdown). Se manca, aggiungi:

```css
/* ═══════════════════════════════════════════════════════════════
   Dropdown menu primary — sub-menu styling
   ═══════════════════════════════════════════════════════════════ */
.sl-header__nav .menu-item-has-children {
    position: relative;
}
.sl-header__nav .sub-menu {
    position: absolute;
    top: 100%;
    left: 0;
    min-width: 240px;
    padding: 16px 20px;
    background: var(--background);
    border: 1px solid var(--border);
    list-style: none;
    margin: 0;
    display: none;
    flex-direction: column;
    gap: 8px;
    z-index: 100;
    box-shadow: 0 8px 32px rgba(27, 43, 75, 0.08);
}
.sl-header__nav .menu-item-has-children:hover > .sub-menu,
.sl-header__nav .menu-item-has-children:focus-within > .sub-menu {
    display: flex;
}
.sl-header__nav .sub-menu a {
    font-size: 13px;
    padding: 4px 0;
    white-space: nowrap;
}
.sl-header__nav .sub-menu a:hover {
    color: var(--accent);
}

/* Sotto-menu mobile: stack inline */
@media (max-width: 1024px) {
    .sl-header__nav .sub-menu {
        position: static;
        border: none;
        box-shadow: none;
        padding: 8px 0 8px 16px;
        background: transparent;
    }
}
```

### Step 1.5 — Verify

```bash
docker compose run --rm wpcli cache flush
HTML=$(curl -s "http://localhost:8080/?_=task1" )
echo "  Sub-menu items presenti: $(echo "$HTML" | grep -c 'sub-menu')"
echo "  'Per i Privati' nel HTML: $(echo "$HTML" | grep -c 'Per i Privati')"
echo "  Voce 'Costi' nel menu: $(echo "$HTML" | grep -c '>Costi<')"

# Test che archive tipo-area funzioni
echo "  /tipo-area/privati/ HTTP: $(curl -s -o /dev/null -w '%{http_code}' http://localhost:8080/tipo-area/privati/)"
```

Tutti i check devono dare valori > 0 e HTTP 200.

---

## TASK 2 — "Prima consulenza gratuita" gancio (10 min)

### Obiettivo
Far apparire il messaggio "consulenza conoscitiva gratuita" nei punti dove l'utente prende decisione (CTA hero, CTA finale competenze, sezione contatti).

### Step 2.1 — Hero CTA homepage

Il copy della Hero CTA è hardcoded in `front-page.php` come `'Prenota un primo incontro'` (sezione 1 del JSX). Sostituiscilo:

```bash
# Cerca riga corrente
grep -n "Prenota un primo incontro" wp-content/themes/saltelli/front-page.php

# Modifica con sed (backup .bak per safety)
sed -i.bak 's/Prenota un primo incontro/Prenota una consulenza gratuita/g' wp-content/themes/saltelli/front-page.php
rm -f wp-content/themes/saltelli/front-page.php.bak
```

Aggiungi anche una **subline mono** sotto la CTA hero. Cerca il blocco `<a class="sl-btn sl-btn--primary"` nella sezione hero di `front-page.php` e dopo `</a>` inserisci:

```php
<div class="sl-mono sl-hero__cta-note">
    Prima consulenza conoscitiva — risposta entro 24 ore
</div>
```

CSS in `sections.css`:

```css
.sl-hero__cta-note {
    margin-top: 16px;
    color: var(--text-muted);
    font-size: 11px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}
```

### Step 2.2 — CTA finale competenze

In `single-competenza.php`, il blocco CTA finale di ciascuna competenza (cerca classe `.sl-competenza__cta` o simile, oppure cerca testo `cta_label` o `Parla con i nostri avvocati`).

Aggiungi dopo il button CTA una subline:

```php
<div class="sl-mono sl-competenza__cta-note">
    Prima consulenza conoscitiva gratuita · Risposta entro 24 ore · In studio o online
</div>
```

CSS:

```css
.sl-competenza__cta-note {
    margin-top: 12px;
    color: var(--text-muted);
    font-size: 11px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}
```

### Step 2.3 — Sezione "Contatti" homepage

Nella section finale della homepage (sezione 6 nel JSX, classe `.sl-contact` in `front-page.php`), aggiungi un'eyebrow line con la rassicurazione. Cerca il blocco `<section class="sl-contact"` e nella sua header section aggiungi prima del titolo:

```php
<div class="sl-mono sl-contact__eyebrow">
    Prima consulenza conoscitiva gratuita · Risposta entro 24 ore
</div>
```

(Se esiste già un eyebrow lì, sostituisci il testo. Non duplicare elementi.)

### Step 2.4 — Verify

```bash
docker compose run --rm wpcli cache flush
HTML_HOME=$(curl -s "http://localhost:8080/?_=task2")
HTML_COMP=$(curl -s "http://localhost:8080/competenze/diritto-tributario/?_=task2")

echo "  Hero CTA aggiornata: $(echo "$HTML_HOME" | grep -c 'consulenza gratuita')"
echo "  '24 ore' presente in home: $(echo "$HTML_HOME" | grep -c '24 ore')"
echo "  '24 ore' presente in competenza: $(echo "$HTML_COMP" | grep -c '24 ore')"
```

Tutti i check ≥ 1.

---

## TASK 3 — Pagina "/costi/" con copy editoriale (15-20 min)

### Obiettivo
Creare la pagina più richiesta dall'audit CRO ("la domanda n.1 di chi cerca avvocato online: quanto costa?"). Pagina semplice, onesta, editoriale, niente prezzi specifici.

### Step 3.1 — Crea WP page /costi/

```bash
# Verifica se esiste già
EXISTING=$(docker compose run --rm wpcli post list --post_type=page --name=costi --field=ID 2>&1 | grep -oE '^[0-9]+' | head -1)

if [ -z "$EXISTING" ]; then
    # Crea page con content HTML
    cat > /tmp/costi_content.html << 'COSTIHTML'
<div class="sl-costi__intro">
<p class="sl-costi__capsule">Lo Studio Legale Saltelli & Partners offre una <strong>prima consulenza conoscitiva gratuita</strong> di trenta minuti, in presenza nella sede di Chiaia oppure online. È il momento in cui ascoltiamo la pratica, valutiamo la percorribilità dell'azione e — solo se ha senso procedere — formuliamo un preventivo personalizzato basato su complessità, tempi e probabilità di esito.</p>
</div>

<section class="sl-costi__section">
<div class="sl-mono">§ 01 — Come funziona</div>
<h2>La prima consulenza</h2>
<p>Trenta minuti, gratuiti, riservati. È il momento per esporre la situazione senza impegno. Disponibile in tre modalità:</p>
<ul>
<li><strong>In studio</strong> — Via Vannella Gaetani 27, Napoli (Chiaia). Lunedì-venerdì, su appuntamento.</li>
<li><strong>Online</strong> — Videocall riservata su Google Meet o piattaforma a Sua scelta. Ideale per chi vive fuori Napoli o per pratiche urgenti.</li>
<li><strong>Telefonica</strong> — Per casi semplici o per una prima valutazione.</li>
</ul>
<p>Al termine della consulenza riceverà una sintesi scritta della valutazione e, se decidiamo di procedere insieme, un preventivo dettagliato. Nessuna pressione, nessun obbligo.</p>
</section>

<section class="sl-costi__section">
<div class="sl-mono">§ 02 — Trasparenza</div>
<h2>Come calcoliamo i costi</h2>
<p>Lo Studio non lavora a "tariffe nascoste" e non fornisce listini standardizzati per pratiche complesse: ogni caso ha un peso diverso. Ogni preventivo che riceverà conterrà:</p>
<ul>
<li><strong>Importo onorario</strong> per fasi di lavoro chiaramente identificate</li>
<li><strong>Costi di documentazione e contributi</strong> separati e dichiarati a parte (contributo unificato, marche da bollo, perizie, eventuali domiciliazioni)</li>
<li><strong>Tempistiche stimate</strong> per ciascuna fase</li>
<li><strong>Valutazione realistica delle probabilità di esito</strong> — niente promesse, niente illusioni</li>
</ul>
<p>Per le cause più impegnative valutiamo soluzioni di <strong>pagamento dilazionato</strong>. Per le aziende e i professionisti partita IVA emettiamo regolare fattura elettronica.</p>
</section>

<section class="sl-costi__section">
<div class="sl-mono">§ 03 — Domande frequenti</div>
<h2>Sui costi, in chiaro</h2>

<details class="sl-acc">
<summary class="sl-acc__summary">Quanto costa un avvocato a Napoli?</summary>
<div class="sl-acc__panel">
<p>Dipende dalla materia, dalla complessità e dal valore della causa. Una pratica di recupero crediti semplice costa molto meno di un contenzioso tributario complesso. Per questo non offriamo listini: la prima consulenza è gratuita proprio per darle un preventivo realistico sul Suo caso specifico.</p>
</div>
</details>

<details class="sl-acc">
<summary class="sl-acc__summary">La prima consulenza è davvero gratuita?</summary>
<div class="sl-acc__panel">
<p>Sì. Trenta minuti, senza obbligo di procedere. Se decideremo di non lavorare insieme, non ci sarà alcun costo. È il nostro modo di rispettare il Suo tempo e i Suoi soldi.</p>
</div>
</details>

<details class="sl-acc">
<summary class="sl-acc__summary">Posso pagare a rate?</summary>
<div class="sl-acc__panel">
<p>Per cause di durata pluriennale (separazioni, contenziosi tributari complessi, recuperi crediti articolati) valutiamo soluzioni di pagamento dilazionato. La discutiamo durante la prima consulenza, in modo trasparente e senza interessi nascosti.</p>
</div>
</details>

<details class="sl-acc">
<summary class="sl-acc__summary">Cosa è incluso nel preventivo?</summary>
<div class="sl-acc__panel">
<p>Onorario professionale per le fasi del lavoro, costi di documentazione (contributo unificato, marche da bollo, perizie, domiciliazioni), tempistiche stimate. Tutto chiaro, niente sorprese in fattura.</p>
</div>
</details>

<details class="sl-acc">
<summary class="sl-acc__summary">Cosa succede se la causa va male?</summary>
<div class="sl-acc__panel">
<p>Durante la prima consulenza Le diamo una valutazione realistica delle probabilità di esito. Se la causa è perdente, glielo diciamo prima di farLa spendere. Se invece accettiamo l'incarico, è perché crediamo che valga la pena combatterla — pur non potendo mai garantire un esito (chi promette esiti certi mente).</p>
</div>
</details>

</section>

<section class="sl-costi__cta">
<h2>Prenoti la prima consulenza</h2>
<p>Trenta minuti gratuiti, in studio o online. Risposta entro 24 ore.</p>
<p><a class="sl-btn sl-btn--primary" href="/contatti/">Prenota ora <span class="arrow">→</span></a></p>
</section>
COSTIHTML

    docker cp /tmp/costi_content.html saltelli-wp:/tmp/costi_content.html

    # Crea page (titolo + content da file)
    PAGE_ID=$(docker compose run --rm wpcli post create \
        --post_type=page \
        --post_title="Costi e prima consulenza" \
        --post_name="costi" \
        --post_status=publish \
        --post_excerpt="Prima consulenza conoscitiva gratuita di 30 minuti, in studio o online. Preventivo personalizzato, trasparente, senza sorprese." \
        --post_content="$(docker exec saltelli-wp cat /tmp/costi_content.html)" \
        --porcelain 2>&1 | grep -oE '^[0-9]+' | head -1)
    echo "  ✓ Pagina /costi/ creata con ID: $PAGE_ID"
    
    # Yoast meta description
    if [ -n "$PAGE_ID" ]; then
        docker compose run --rm wpcli post meta update "$PAGE_ID" _yoast_wpseo_metadesc \
            "Prima consulenza conoscitiva gratuita di 30 minuti, in studio a Chiaia o online. Preventivi trasparenti, niente sorprese in fattura." 2>&1 | tail -1
    fi
else
    echo "  ⚠ Pagina /costi/ già esistente (ID $EXISTING) — skip creazione"
fi
```

### Step 3.2 — Styling pagina /costi/

In `assets/css/sections.css` aggiungi (in fondo):

```css
/* ═══════════════════════════════════════════════════════════════
   Pagina /costi/ — layout editoriale
   ═══════════════════════════════════════════════════════════════ */
.page-template-default.page article {
    padding: clamp(48px, 6vw, 96px) 0;
}
.sl-costi__intro {
    max-width: 720px;
    margin: 0 auto 64px;
    padding-inline: clamp(24px, 5vw, 96px);
}
.sl-costi__capsule {
    font-family: var(--font-display);
    font-size: clamp(20px, 2vw, 26px);
    font-style: italic;
    line-height: 1.5;
    color: var(--primary);
}
.sl-costi__section {
    max-width: 720px;
    margin: 0 auto 64px;
    padding-inline: clamp(24px, 5vw, 96px);
}
.sl-costi__section h2 {
    font-size: clamp(28px, 3vw, 44px);
    margin-block: 16px 24px;
    color: var(--primary);
}
.sl-costi__section ul {
    padding-left: 24px;
    margin-block: 16px;
}
.sl-costi__section li {
    margin-bottom: 8px;
    line-height: 1.7;
}
.sl-costi__cta {
    max-width: 720px;
    margin: 96px auto 0;
    padding: 64px clamp(24px, 5vw, 96px);
    background: var(--surface);
    text-align: center;
}
.sl-costi__cta h2 {
    font-size: clamp(28px, 3vw, 44px);
    margin-bottom: 16px;
}
.sl-costi__cta p {
    color: var(--text-muted);
    margin-bottom: 24px;
}
```

### Step 3.3 — Verify

```bash
docker compose run --rm wpcli cache flush
docker compose run --rm wpcli rewrite flush --hard

HTTP=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:8080/costi/")
HTML=$(curl -s "http://localhost:8080/costi/?_=verify")
echo "  /costi/ HTTP: $HTTP (atteso 200)"
echo "  Capsule presente: $(echo "$HTML" | grep -c 'sl-costi__capsule')"
echo "  5 FAQ presenti: $(echo "$HTML" | grep -c 'sl-acc__summary')"
echo "  CTA finale: $(echo "$HTML" | grep -c 'Prenota ora')"
```

Tutti ≥ 1, HTTP 200.

---

## TASK 4 — Bump version + final smoke test (5 min)

```bash
sed -i.bak 's/Version: 0.5.1-beta-content-fix/Version: 0.6.0-beta-audit-aligned/' wp-content/themes/saltelli/style.css
sed -i.bak "s/define('SALTELLI_THEME_VERSION', '0.5.1-beta-content-fix')/define('SALTELLI_THEME_VERSION', '0.6.0-beta-audit-aligned')/" wp-content/themes/saltelli/functions.php
rm -f wp-content/themes/saltelli/{style.css,functions.php}.bak

docker compose run --rm wpcli cache flush
docker compose run --rm wpcli transient delete --all

# Smoke test finale
echo ""
echo "═══════════ SMOKE TEST AUDIT ALIGNMENT ═══════════"
for URL in "/" "/costi/" "/competenze/" "/competenze/diritto-tributario/" "/avvocati/emiliano-saltelli/" "/tipo-area/privati/" "/tipo-area/imprese/" "/tipo-area/contenzioso/"; do
    HTTP=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:8080$URL")
    SIZE=$(curl -s "http://localhost:8080$URL" | wc -c | tr -d ' ')
    printf "  %-40s HTTP %s · %s bytes\n" "$URL" "$HTTP" "$SIZE"
done

echo ""
echo "═══════════ PHP error log ═══════════"
docker exec saltelli-wp tail -10 /var/www/html/wp-content/debug.log 2>/dev/null || echo "  (vuoto = OK)"
```

**Tutti gli URL devono dare HTTP 200.** Se uno dà 404 (es. `/tipo-area/privati/` se rewrite non è flushato), rilancia `wp rewrite flush --hard` e riprova.

---

## Report finale

Scrivi report breve (50-80 righe) in `.claude/knowledge/design/sessione-1/reports/audit-alignment/REPORT.md`:

1. ✅/❌ Task 1 (sitemap 3-categorie): mapping 19 competenze, menu nuovo, dropdown CSS
2. ✅/❌ Task 2 (consulenza gratuita): 3 punti dove appare il messaggio
3. ✅/❌ Task 3 (pagina /costi/): page ID, sezioni create, FAQ count
4. ✅/❌ Task 4 (smoke test): tabella URL → HTTP code
5. Eventuali decisioni autonome (es. slug competenza diverso da quello atteso → adattamento mapping)
6. Eventuali blocker o issue residui
7. Tempo totale impiegato

Poi **fermati**. Il direttore d'orchestra (Claude in chat) farà visual check via Chrome e committerà.

---

## Cosa fare se qualcosa va storto

| Situazione | Azione |
|---|---|
| Slug CPT divergente nel mapping | Cerca con `wp post list --post_type=competenza --fields=name`, adatta |
| `/tipo-area/privati/` ritorna 404 | `wp rewrite flush --hard` + verifica `register_taxonomy('tipo-area', ...)` in `cpt-competenza.php` ha `'rewrite' => ['slug' => 'tipo-area']` |
| Menu items duplicati | `wp menu delete saltelli-header` poi ricrea |
| `front-page.php` ha già subline → non duplicare | `grep` prima di `sed`, controlla idempotenza |
| `sed` modifica più occorrenze del previsto | Fai `git diff` dopo, rollback se serve |
| `docker cp` fallisce | Alternativa: `docker compose run --rm -v /tmp:/tmp wpcli ...` |

In caso di **dubbio reale**, fermati e segnala a Duccio. La presentazione è oggi: meglio fermarsi che rompere.

---

*v1.0 — 2026-04-29 PRE-DEMO · Audit Alignment · Direttore d'orchestra: Claude (chat).*
