# Prompt — Final Polish Agent v0.11.0 (Mini-Round Pre-Step F)

> **Per Claude Code in nuova sessione (o sessione corrente).** Apri `saltelli-wp/`, leggi questo file, eseguilo. Tempo: 30-40 min. **Mini-fix focused** sui 3 issue residui prima di Step F.
> **PRECEDENZA:** Editorial Refinement v0.10.0 completato.

---

## Tu sei

Il **Final Polish Agent**. Editorial Refinement ha trasformato il sito in rivista editoriale (lede italic, drop-cap, H2 respiro, sentenze in container 720px, author bio 80×80). Walkthrough deep ha trovato **3 issue residui** che vanno chiusi prima di Step F (Production Readiness):

```
R1 — Mappa /contatti/ con coordinate sul mare (BLOCKER reputazione cliente)
R2 — Pagina /chi-siamo/ con Lorem Ipsum (BLOCKER reputazione cliente)
R3 — Lista bullet "•" su /competenze/diritto-tributario/ (incoerenza editoriale)
```

Output atteso: **v0.11.0-beta-final-polish** ready per Step F.

---

## Letture obbligatorie

1. `CLAUDE.md` — hard constraints
2. `.claude/knowledge/design/sessione-1/reports/visual-walkthrough/REPORT-v0.10.0-DEEP.md` — diagnosi 3 issue
3. `.claude/knowledge/project-context.json` — info studio per scrivere /chi-siamo/ content
4. `wp-content/themes/saltelli/page.php` — template page (per R1 map fix)
5. `wp-content/themes/saltelli/assets/css/sections.css` — CSS file (per R3)

---

## Hard rules

| Rule | Reason |
|---|---|
| Mai sovrascrivere `_thumbnail_id` Emiliano (CPT 2660) | Foto Step C.5 |
| Mai sovrascrivere `bio_estesa` o `post_content` CPT competenza/avvocato | Step D content |
| Design tokens NON si toccano | Locked |
| Cache flush + smoke test 6+ URL dopo OGNI fix | Lezione Recovery/Editorial |
| R2 content scritto **in italiano**, **editoriale**, basato su `project-context.json` (no inventato) | Brand fidelity |
| R2 puoi cambiare `post_content` di page id 19 (chi-siamo) **NON** è uno dei content protetti (è Lorem Ipsum dal 2019, da rimpiazzare) | Audit CRO originale flagga questo come bug |

---

## R1 — Mappa coordinate /contatti/ (10 min)

### Sintomo
Mappa OpenStreetMap iframe rende il pin **sul mare di Napoli** (Pista ciclabile), invece che sull'indirizzo Via Vannella Gaetani 27 (Chiaia interna).

### Diagnosi
```bash
grep -A 5 "openstreetmap" wp-content/themes/saltelli/page.php
```

Vedi parametri `bbox=14.235,40.828,14.243,40.832` + `marker=40.830,14.239` — coordinate errate.

### Coordinate corrette (verificate via project-context.json)
- **Indirizzo:** Via Vannella Gaetani 27, 80121 Napoli (Chiaia)
- **Coordinate WGS84:** lat 40.832, lng 14.235 (cuore di Chiaia interna)

### Fix
Edit `page.php` blocco `is_page('contatti')`:

```php
<iframe
    src="https://www.openstreetmap.org/export/embed.html?bbox=14.232,40.829,14.240,40.835&layer=mapnik&marker=40.832,14.235"
    width="100%" height="400"
    style="border: 1px solid var(--border);"
    loading="lazy"
    title="Studio Legale Saltelli - Via Vannella Gaetani 27, Napoli">
</iframe>
```

E aggiorna anche il link "APRI IN OPENSTREETMAP":
```html
href="https://www.openstreetmap.org/?mlat=40.832&mlon=14.235#map=17/40.832/14.235"
```

### Verify
```bash
docker compose run --rm wpcli cache flush
HTML=$(curl -s "http://localhost:8080/contatti/?_=r1verify")
echo "  bbox correct: $(echo "$HTML" | grep -c '14.232,40.829')"
echo "  marker correct: $(echo "$HTML" | grep -c 'marker=40.832,14.235')"
```

---

## R2 — Pagina /chi-siamo/ Lorem Ipsum → content reale (15 min)

### Sintomo
Page WP id 19 (slug `chi-siamo`) ha `post_content` placeholder Lorem Ipsum dal 2019. Audit CRO originale aveva flaggato l'issue, mai risolto.

### Diagnosi
```bash
docker compose run --rm wpcli post get 19 --field=post_content 2>&1 | head -10
```

Vedrai: "Lorem ipsum dolor sit amet, conetur adiping elit Lorem ipsum dolor sit amet, cons ectetur adiscing elit..."

### Fix — Content editoriale reale

Scrivi **content editoriale** in italiano basato su:
- `project-context.json` `client_data` (founding 1999, Via Vannella Gaetani 27, Chiaia, "bottega di quattro professionisti")
- `team_members` (Emiliano fondatore tributarista, Fabiana giuslavorista, Antonia famiglia LGBTQ+, Stefano condominiale)
- Tono editoriale stile JSX Sessione 1 sezione "§ 02 Lo studio" (drop-cap "L", "Una bottega in senso napoletano", "diritto è arte di ascolto", ecc.)
- **NO inventare:** date specifiche oltre 1999, premi, riconoscimenti

### Esempio template content

Salva in `/tmp/chi_siamo_content.html`:
```html
<div class="sl-page__prose">

<p class="sl-page__lede">Una bottega in senso napoletano. Quattro avvocati, una sede storica nel cuore di Chiaia, vent'anni di pratica accanto a famiglie e imprese di Napoli.</p>

<p>Lo Studio Legale Saltelli &amp; Partners nasce nel 1999 per iniziativa di Emiliano Saltelli, allora giovane tributarista formatosi all'Università degli Studi di Napoli Federico II. Nel quarto di secolo successivo, lo Studio è cresciuto come si cresce a Napoli &mdash; per accumulazione paziente, una pratica alla volta, un avvocato alla volta &mdash; fino a diventare oggi una bottega di quattro professionisti.</p>

<p>Crediamo che il diritto sia, prima di tutto, un'arte di ascolto. Le carte vengono dopo. Per questo non offriamo pacchetti né formule: ogni cliente è una storia, e ogni storia merita il tempo di essere capita.</p>

<p>Lavoriamo in Via Vannella Gaetani 27, in un palazzo nobiliare a due passi dal lungomare di Chiaia. È qui che riceviamo, è qui che si tengono i nostri primi colloqui, ed è qui &mdash; quando possibile &mdash; che torniamo a vedersi anche per le pratiche più semplici.</p>

<h2>I nostri quattro</h2>

<p><strong>Emiliano Saltelli</strong> &mdash; <em>founding partner, tributarista.</em> Fondatore dello Studio. Si occupa di diritto tributario e contenzioso fiscale dal 2008, con casistica consolidata su cartelle esattoriali, accertamenti, IRPEF, IMU e Commissioni Tributarie. Riceve a Chiaia, Napoli.</p>

<p><strong>Fabiana Saltelli</strong> &mdash; <em>partner, giuslavorista.</em> Si occupa di diritto del lavoro: licenziamenti, mobbing, contestazioni disciplinari, contenzioso INPS, sia lato lavoratore che lato impresa. Coordina anche pratiche tributarie connesse al rapporto di lavoro.</p>

<p><strong>Antonia Battista</strong> &mdash; <em>of-counsel, famiglia e LGBTQ+.</em> Si occupa di diritto di famiglia e tutela giuridica delle famiglie LGBTQ+: unioni civili, genitorialità, riconoscimento di figli, trascrizione di atti di stato civile esteri. Coordina l'omonima area dello Studio.</p>

<p><strong>Stefano Gaetano Tedesco</strong> &mdash; <em>associate, condominiale.</em> Si occupa di diritto condominiale e immobiliare: contenziosi tra condòmini, locazioni, compravendite, espropriazioni.</p>

<h2>Come lavoriamo</h2>

<p>La prima consulenza è <strong>conoscitiva e gratuita</strong>: trenta minuti per ascoltare la pratica, valutare la percorribilità dell'azione e &mdash; solo se ha senso procedere &mdash; formulare un preventivo personalizzato basato su complessità, tempi e probabilità di esito.</p>

<p>Riceviamo solo su appuntamento, in studio o in videocall. Per le pratiche più impegnative valutiamo soluzioni di pagamento dilazionato.</p>

<p><a class="sl-btn sl-btn--primary" href="/contatti/">Prenota una consulenza gratuita <span class="arrow">→</span></a></p>

</div>
```

### Apply via WP-CLI

```bash
# Salva content in file temporaneo
cat > /tmp/chi_siamo_content.html << 'CONTENT'
[paste HTML sopra]
CONTENT

# Update post_content via wp-cli eval-file (gestisce UTF-8 + multiline correttamente)
docker cp /tmp/chi_siamo_content.html saltelli-wp:/tmp/chi_siamo_content.html

docker compose run --rm wpcli eval "
\$content = file_get_contents('/tmp/chi_siamo_content.html');
\$result = wp_update_post([
    'ID' => 19,
    'post_content' => \$content,
    'post_title' => 'Lo studio',
    'post_excerpt' => 'Una bottega in senso napoletano. Quattro avvocati a Chiaia, vent anni di pratica accanto a famiglie e imprese di Napoli.',
], true);
if (is_wp_error(\$result)) {
    echo 'ERROR: ' . \$result->get_error_message();
} else {
    echo 'OK: post 19 updated, ' . strlen(\$content) . ' chars';
}
"

# Verifica
docker compose run --rm wpcli post get 19 --field=post_content | head -3
```

### Yoast meta description
```bash
docker compose run --rm wpcli post meta update 19 _yoast_wpseo_metadesc \
    "Studio Legale Saltelli e Partners. Quattro avvocati a Napoli (Chiaia) dal 1999, specializzati in tributario, lavoro, famiglia LGBTQ+ e condominiale." 2>&1 | tail -1
```

### Verify
```bash
docker compose run --rm wpcli cache flush
HTML=$(curl -s "http://localhost:8080/chi-siamo/?_=r2verify")
echo "  Lorem Ipsum residual: $(echo "$HTML" | grep -ci 'lorem ipsum' | head -1)"
echo "  H1 'Lo studio' or 'Chi siamo': $(echo "$HTML" | grep -oE '<h1[^>]*>[^<]+' | head -1)"
echo "  Bottega keyword: $(echo "$HTML" | grep -c 'bottega')"
echo "  4 avvocati menzionati: $(echo "$HTML" | grep -cE 'Emiliano|Fabiana|Antonia|Stefano')"
```

Atteso:
- `Lorem Ipsum residual: 0`
- H1 mostra "Lo studio" (o "Chi siamo" se template usa `the_title()`)
- `Bottega: ≥1`
- `Tutti 4 avvocati: ≥4`

---

## R3 — Lista bullet "•" su /competenze/* (10 min)

### Sintomo
Lista "IMU, TARSU, TOSAP / IRPEF, IRES, IRAP / I.V.A. e accise / ..." su `/competenze/diritto-tributario/` mostra bullet "•" classico browser invece di em-dash "—" accent.

### Diagnosi
```bash
# Trova lo scope CSS attuale del fix A3
grep -B 2 -A 5 'content: "—"' wp-content/themes/saltelli/assets/css/sections.css | head -30

# Verifica markup wrapper della lista nel template competenza
grep -B 2 -A 5 "competenza__body\|competenza__prose\|competenza__content" wp-content/themes/saltelli/single-competenza.php | head -20
```

Probabile: scope corrente è `.sl-post__body / .entry-content / .sl-page__prose`. Il template `single-competenza.php` usa wrapper diverso (es. `.sl-competenza__body`).

### Fix
Edit `sections.css` blocco A3 esteso scope:

```css
/* A3 — Lists editoriali (extended scope) */
.sl-post__body ul li,
.entry-content ul li,
.sl-page__prose ul li,
.sl-competenza__body ul li,
.sl-competenza__prose ul li,
.sl-competenza__content ul li {
    list-style: none;
    position: relative;
    margin-bottom: 12px;
    line-height: 1.7;
}

.sl-post__body ul li::before,
.entry-content ul li::before,
.sl-page__prose ul li::before,
.sl-competenza__body ul li::before,
.sl-competenza__prose ul li::before,
.sl-competenza__content ul li::before {
    content: "—";
    position: absolute;
    left: -28px;
    color: var(--accent);
    font-weight: 400;
}

/* :not() chain SCOPED, NON impatta liste meta del tema */
.sl-areas__list,
.sl-articles,
.sl-team__specs,
.sl-attorney__specs,
.sl-blog__list,
.sl-mobile-menu,
.sl-header__nav .menu,
.sl-footer__nav {
    list-style: none;
}

.sl-areas__list li,
.sl-articles li,
.sl-team__specs li,
.sl-attorney__specs li,
.sl-blog__list li,
.sl-mobile-menu li,
.sl-header__nav .menu li,
.sl-footer__nav li {
    list-style: none;
    margin-bottom: 0;
}

.sl-areas__list li::before,
.sl-articles li::before,
.sl-team__specs li::before,
.sl-attorney__specs li::before,
.sl-blog__list li::before,
.sl-mobile-menu li::before,
.sl-header__nav .menu li::before,
.sl-footer__nav li::before {
    content: none;  /* esclude em-dash su liste meta */
}
```

### Verify
```bash
docker compose run --rm wpcli cache flush
HTML=$(curl -s "http://localhost:8080/competenze/diritto-tributario/?_=r3verify")
echo "  CSS rule scope competenza: $(curl -s 'http://localhost:8080/wp-content/themes/saltelli/assets/css/sections.css' | grep -c 'sl-competenza__body ul li::before')"
```

Visual check: l'orchestrator confermerà em-dash gold sostituisce bullet "•" su `/competenze/*`.

---

## TASK FINALE — Bump version + smoke test (5 min)

```bash
sed -i.bak 's/Version: 0.10.0-beta-editorial/Version: 0.11.0-beta-final-polish/' wp-content/themes/saltelli/style.css
sed -i.bak "s/define('SALTELLI_THEME_VERSION', '0.10.0-beta-editorial')/define('SALTELLI_THEME_VERSION', '0.11.0-beta-final-polish')/" wp-content/themes/saltelli/functions.php
rm -f wp-content/themes/saltelli/{style.css,functions.php}.bak

docker compose run --rm wpcli cache flush
docker compose run --rm wpcli transient delete --all

# Smoke test 8 URL chiave
echo "═══════════ FINAL POLISH SMOKE TEST ═══════════"
for URL in "/" "/lo-studio/" "/chi-siamo/" "/contatti/" "/blog/" "/competenze/diritto-tributario/" "/avvocati/emiliano-saltelli/" "/intimazione-tari-annullata-vittoria-dello-studio-legale-saltelli/"; do
    HTTP=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:8080$URL?_=v011final")
    H1=$(curl -s "http://localhost:8080$URL?_=v011final" | grep -c "<h1")
    LOREM=$(curl -s "http://localhost:8080$URL?_=v011final" | grep -ci "lorem ipsum")
    printf "  %-65s HTTP %s · %sH1 · Lorem:%s\n" "$URL" "$HTTP" "$H1" "$LOREM"
done
```

Atteso:
- Tutti HTTP 200 (eccetto /lo-studio/ HTTP 301 → /chi-siamo/ → 200)
- 1 H1 ovunque
- **Lorem: 0 ovunque** (R2 fix)

---

## Report finale

`.claude/knowledge/design/sessione-1/reports/final-polish-v0.11.0/REPORT.md`:

1. ✅/❌ R1, R2, R3
2. Smoke test 8 URL
3. Diagnosi precisa per R1 (vecchie coordinate → nuove)
4. Content scritto per R2 (snippet)
5. Scope CSS esteso per R3
6. Verifica regressione: 21 PASS preservati?
7. Tempo per fix
8. **GO/NO-GO per Step F** dal tuo punto di vista

Poi **fermati**. Direttore d'orchestra confermerà visualmente prima di Step F.

---

*v1.0 — Final Polish before Step F. Direttore d'orchestra: Claude (chat).*
