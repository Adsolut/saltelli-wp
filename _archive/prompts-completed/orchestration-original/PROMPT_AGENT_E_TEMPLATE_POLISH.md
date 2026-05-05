# Prompt — Template Polish Agent (Step E — Templates secondari)

> **Per Claude Code in nuova sessione.** Apri `saltelli-wp/`, leggi questo file, eseguilo. Lavoro previsto: 2 ore.
> **PRECEDENZA:** Content Migration Agent (Step D) deve essere completato. v0.5.0-beta-content o successiva.

---

## Tu sei

Il **Template Polish Agent**. Finora è stato testato visivamente solo il template `front-page.php` (Homepage). Il tema custom Saltelli ha **9 template** che il cliente vedrà. Il tuo lavoro è camminare ognuno, identificare polish issues, applicare fix.

**Stato di partenza:** v0.5.0-beta-content. Homepage ottimizzata. CPT popolati con copy reale. Foto ancora placeholder. Tutti i template scaffolded ma non review-tested visivamente.

---

## Letture obbligatorie

1. `CLAUDE.md` — hard constraints
2. Tutti i template root del tema da revisionare:
   - `single-avvocato.php`
   - `archive-avvocato.php`
   - `single-competenza.php` (con branch tier-1/tier-2)
   - `archive-competenza.php`
   - `single.php` (blog post)
   - `page.php` (es. /lo-studio, /contatti)
   - `404.php`
   - `search.php`
3. `assets/css/sections.css` — layout patterns già scritti
4. `.claude/knowledge/design/sessione-1/reports/REPORTS_CONSOLIDATI.md` — context

---

## Hard rules

| Rule | Reason |
|---|---|
| Identifica **tu** gli URL da testare via WP-CLI (non chiedere a Duccio) | Autonomia |
| Per ciascun template: 1 URL representative + smoke test + report | Sistematico |
| **NON cambiare il design system** | Locked |
| Polish = micro-fix, non redesign | Scope |
| Se un template ha bug strutturale, ferma e segnala | Coordinamento |
| Test su Chrome 1440 + mobile 375 (via DevTools) | Multi-viewport |

---

## Task 1 — Inventory URL da testare (10 min)

```bash
# Avvocati: 4 URL
docker compose run --rm wpcli post list --post_type=avvocato \
    --fields=ID,post_name --format=csv

# Competenze: 19 URL (di cui 3 tier-1 + 16 tier-2 — pesa entrambi)
docker compose run --rm wpcli post list --post_type=competenza \
    --fields=ID,post_name --format=csv

# Pagine standard
docker compose run --rm wpcli post list --post_type=page \
    --fields=ID,post_name --format=csv | head -10

# Esempio blog post
docker compose run --rm wpcli post list --post_type=post \
    --fields=ID,post_name --format=csv | head -3
```

Salva tutti gli URL representative in un file `.claude/knowledge/design/sessione-1/reports/template-urls.md`. Per ciascun template, scegli **1 URL representative**:

- `single-avvocato.php` → `/avvocati/emiliano-saltelli/`
- `archive-avvocato.php` → `/avvocati/`
- `single-competenza.php` tier-1 → `/competenze/diritto-tributario/`
- `single-competenza.php` tier-2 → `/competenze/domiciliazione-impresa/`
- `archive-competenza.php` → `/competenze/`
- `single.php` blog → uno dei 326 post (scegli uno con autore associato)
- `page.php` → `/lo-studio/` o `/contatti/`
- `404.php` → `/non-esiste/` (forza 404)
- `search.php` → `/?s=tributario`

---

## Task 2 — Smoke test programmato per ciascun template (15 min)

Per ciascun URL, esegui curl + parsing automatico:

```bash
test_template() {
    local url=$1
    local label=$2
    echo "─── $label ($url) ───"
    
    HTML=$(curl -s "http://localhost:8080$url")
    HTTP=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:8080$url")
    SIZE=$(echo "$HTML" | wc -c | tr -d ' ')
    H1_COUNT=$(echo "$HTML" | grep -c "<h1")
    SCHEMA=$(echo "$HTML" | grep -c "application/ld+json")
    SL_HITS=$(echo "$HTML" | grep -oE "sl-[a-z]+" | wc -l | tr -d ' ')
    
    echo "  HTTP: $HTTP, size: $SIZE bytes"
    echo "  H1 count: $H1_COUNT (atteso: 1)"
    echo "  Schema blocks: $SCHEMA"
    echo "  sl-* class hits: $SL_HITS (atteso > 30)"
    echo ""
}

test_template "/" "Homepage (regression test)"
test_template "/avvocati/emiliano-saltelli/" "Single Avvocato"
test_template "/avvocati/" "Archive Avvocato"
test_template "/competenze/diritto-tributario/" "Single Competenza tier-1"
test_template "/competenze/domiciliazione-impresa/" "Single Competenza tier-2"
test_template "/competenze/" "Archive Competenza"
test_template "/lo-studio/" "Page generic"
test_template "/non-esiste/" "404"
test_template "/?s=tributario" "Search"
```

Salva output in `.claude/knowledge/design/sessione-1/reports/template-polish/smoke-tests.md`.

---

## Task 3 — Identifica polish issue per ogni template (~10 min × 9 template)

Per ciascun template, fai una breve analisi:

### Per ciascun template:
1. Verifica markup: ci sono `sl-*` classes? Esattamente 1 H1?
2. Verifica content rendering: il copy del CPT è visibile?
3. Verifica responsive: a 375px va in colonna?
4. Verifica empty/loading state: cosa succede se ACF field vuoto?

**Issue tipici da cercare:**
- Sticky elements che si rompono in mobile
- Imagini che non caricano (foto avvocati mancanti)
- Drop-cap su `<p>` vuoto che fa errore visivo
- Form contatti senza submit working
- TOC sticky blog post che non aggancia gli h2/h3 del body
- Gerarchia heading rotta (h1 → h3 senza h2)
- Navigation breadcrumb mancante o disallineato
- Tier-1 vs tier-2 visivamente distinguibili?

---

## Task 4 — Apply fix per priorità (~30-60 min totali)

Ordina issue per impatto:
- **P0** (bloccante): bug strutturale che rompe il template
- **P1** (visibile cliente): polish UX/visivo che è ovvio in demo
- **P2** (refinement): micro-fix tipografici, padding, transitions

**P0 e P1 → applicali tu.** Edit dei template PHP + CSS in `sections.css`.

**P2 → annota nel report e lascia per polishing successivo.**

Per ciascun fix, segna:
- Template/file modificato
- Issue identificato
- Fix applicato
- Test post-fix

---

## Task 5 — Cross-viewport test (15 min)

Per i 3-4 template più critici (single-avvocato, single-competenza tier-1, archive-competenza, single blog), suggerisci a Duccio di aprire DevTools e:

1. Vista 1440px desktop → verifica layout asimmetrico, drop-cap, sticky elementi
2. Vista 768px tablet → verifica wrap colonne, navigation
3. Vista 375px mobile → verifica menu hamburger, touch targets, type scale

Salva gli eventuali fix risultanti in `sections.css` con media queries appropriate.

---

## Task 6 — Verifica completa schema markup (15 min)

Per ogni template **dopo i fix**:

```bash
# Single avvocato deve avere Person/Attorney schema
curl -s http://localhost:8080/avvocati/emiliano-saltelli/ | grep -A 2 "application/ld+json" | head -20

# Single competenza tier-1 deve avere FAQPage + LegalService
curl -s http://localhost:8080/competenze/diritto-tributario/ | grep -A 2 "application/ld+json" | head -20

# Single blog deve avere Article (Yoast wins ma noi emettiamo LegalService)
curl -s http://localhost:8080/blog/<slug>/ | grep -A 2 "application/ld+json" | head -10
```

Suggerisci a Duccio di validare manualmente su https://validator.schema.org/ una volta che il sito è pubblico (post-deploy DigitalOcean).

---

## Task 7 — Bump version + cache flush

```bash
# 0.5.0-beta-content → 0.6.0-beta-templates
sed -i.bak 's/Version: 0.5.0-beta-content/Version: 0.6.0-beta-templates/' wp-content/themes/saltelli/style.css
sed -i.bak "s/define('SALTELLI_THEME_VERSION', '0.5.0-beta-content')/define('SALTELLI_THEME_VERSION', '0.6.0-beta-templates')/" wp-content/themes/saltelli/functions.php
rm -f wp-content/themes/saltelli/{style.css,functions.php}.bak

docker compose run --rm wpcli cache flush
```

---

## Report finale

Scrivi `.claude/knowledge/design/sessione-1/reports/template-polish/REPORT.md`:

1. ✅/❌ ciascuno dei 7 task
2. Smoke test results per 9 template (HTTP, H1 count, schema count, sl-* hits)
3. Lista issue identificati (P0/P1/P2)
4. Lista fix applicati per template
5. Lista issue P2 lasciati come TODO per polish futuro
6. Cross-viewport: liste fix mobile-specifici
7. Schema markup: ✓/✗ per ciascun template

Poi **fermati**. Non procedere a Step F.

---

*v1.0 — Step E post-content v0.5.0*
