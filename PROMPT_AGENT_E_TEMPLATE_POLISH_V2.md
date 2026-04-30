# Prompt — Template Polish + Mobile Fix Agent (Step E v2)

> **Per Claude Code in nuova sessione (o sessione corrente).** Apri `saltelli-wp/`, leggi questo file, eseguilo. Tempo previsto: 2-2.5 ore.
> **PRECEDENZA:** Pain Points Refinement (Step Pain Points) completato. v0.7.0-beta-pain-points-fixed o successiva.

---

## Tu sei

Il **Template Polish + Mobile Fix Agent**. La build attuale (v0.7.0) è production-grade su desktop. Il direttore d'orchestra (Claude in chat) ha eseguito un visual walkthrough 12-point e ha trovato:

- ✅ **10/12 PASS** sui template principali (Homepage, /costi/, single-competenza tier-1)
- 🟡 **1 WARN**: sticky bottoni avvocato sovrappongono foto desktop
- ❌ **1 FAIL** mobile: hero headline 2 righe invece di 3 + tag tipo-area sovrappone titolo competenza

Il tuo lavoro:
1. **Mobile Fix critici (M1, M2, M3)** in apertura — bloccanti per la qualità
2. **Walkthrough sistematico 9 template secondari** che il direttore d'orchestra non ha testato a fondo (single-avvocato Fabiana/Antonia/Stefano, single-competenza tier-2, archive-avvocato, single blog post, page generic, 404, search)
3. **Polish issue per priorità** trovati durante il walkthrough sistematico
4. **Taxonomy template dedicato** (sostituisce fallback archive.php)

---

## Letture obbligatorie

1. `CLAUDE.md` — hard constraints
2. `.claude/knowledge/design/sessione-1/reports/visual-walkthrough/REPORT-v0.7.0.md` — issues identificati
3. `.claude/knowledge/design/sessione-1/reports/pain-points-refinement/REPORT.md` — contesto fix recenti
4. `wp-content/themes/saltelli/assets/css/sections.css` + `components.css` — file CSS principali
5. Tutti i template root del tema da revisionare (single-*, archive-*, page.php, single.php, 404.php, search.php)

---

## Hard rules

| Rule | Reason |
|---|---|
| Mobile fix PRIMA di qualunque altra cosa | Critici per qualità |
| Design tokens NON si toccano | Locked |
| Mai sovrascrivere bio_estesa, post_content, _thumbnail_id | Step D content + foto Emiliano preservati |
| Mai disattivare plugin | Predicibilità |
| Idempotenza: re-run = stesso output | Stabilità |
| Cache flush + curl test dopo OGNI fix | Catch regression early |
| Mobile testing via curl + verify markup, NON solo desktop | 60% traffico legale è mobile |

---

## TASK 0 — Mobile Fix critici (15-20 min · PRIMA DI TUTTO)

### M1 — Overlap tag tipo-area + titolo competenza in lista aree mobile

**Problema:** Su `<= 1023px`, il tag `.sl-area__meta` (es. "TIER 1 · APPROFONDIMENTO →") si sovrappone al `.sl-area__title` serif gigante creando illeggibilità.

**Diagnosi:**
```bash
grep -A 20 '\.sl-area__meta' wp-content/themes/saltelli/assets/css/sections.css | head -30
```

Verifica come è layout-ato `.sl-area` su mobile (probabilmente è grid o flex desktop senza fallback mobile).

**Fix in `sections.css`:**
```css
/* ═══════════════════════════════════════════════════════════════
   FIX M1 — Mobile (≤1023): tag .sl-area__meta sotto titolo
   ═══════════════════════════════════════════════════════════════ */

@media (max-width: 1023px) {
    .sl-area {
        display: flex;
        flex-direction: column;
        gap: 4px;
        padding-block: 24px;
    }

    .sl-area__num {
        display: none;
    }

    .sl-area__title {
        order: 1;
        font-size: clamp(28px, 6vw, 36px);
        line-height: 1.2;
    }

    .sl-area__meta {
        order: 2;
        position: static;
        margin-top: 4px;
        font-size: 11px;
        color: var(--text-muted);
        letter-spacing: 0.06em;
        opacity: 0.8;
    }
}
```

### M2 — Hero mobile su 2 righe invece di 3

**Problema:** "Diritto, con misura." rende su mobile come "Diritto, con" + "misura." (2 righe) invece delle 3 righe del design ("Diritto," / "con" / "misura.").

**Fix in `sections.css`:**
```css
/* ═══════════════════════════════════════════════════════════════
   FIX M2 — Mobile (≤767): hero headline forza wrap su 3 righe
   ═══════════════════════════════════════════════════════════════ */

@media (max-width: 767px) {
    .sl-hero__headline {
        max-width: 8ch;        /* forza wrap parola-per-parola */
        font-size: clamp(56px, 14vw, 80px);
        line-height: 1.05;
        letter-spacing: -0.02em;
    }
}
```

**Verifica:** dopo fix M2 con `?_=mobiletest` e viewport 375px, l'h1 deve essere 3 line breaks (Diritto, → con → misura.).

### M3 — Sticky TEL/EMAIL sovrappone foto avvocato desktop

**Problema:** `.sl-attorney__sticky-btn` su desktop ≥ 1024 ha `position: fixed; left: clamp(16px, 3vw, 48px)` che a 1440px va sopra il bordo sinistro della foto.

**Fix in `sections.css`:**
```css
/* ═══════════════════════════════════════════════════════════════
   FIX M3 — Sticky avvocato: posizione safe, mai overlap
   ═══════════════════════════════════════════════════════════════ */

.sl-attorney__sticky {
    position: fixed;
    top: 50%;
    left: 8px;                  /* SEMPRE 8px, no clamp variabile */
    transform: translateY(-50%);
    display: flex;
    flex-direction: column;
    gap: 6px;
    z-index: 50;
    pointer-events: auto;
}

@media (max-width: 1023px) {
    .sl-attorney__sticky {
        position: static;
        flex-direction: row;
        gap: 8px;
        margin-block: 24px;
        transform: none;
        padding-inline: 24px;
    }
}

.sl-attorney__sticky-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 56px;
    min-height: 32px;
    padding: 4px 8px;
    background: var(--surface);
    border: 1px solid var(--border);
    color: var(--text);
    text-decoration: none;
    font-family: var(--font-mono);
    font-size: 10px;
    letter-spacing: 0.06em;
    text-transform: uppercase;
    transition: all var(--dur-fast) var(--ease-editorial);
    border-radius: 0;
}

.sl-attorney__sticky-btn:hover,
.sl-attorney__sticky-btn:focus-visible {
    background: var(--primary);
    color: var(--background);
    border-color: var(--primary);
    transform: translateX(2px);
}

@media (max-width: 1023px) {
    .sl-attorney__sticky-btn {
        width: auto;
        flex: 1;
        min-height: 44px;       /* mobile touch target */
    }
}
```

### Verify Task 0
```bash
docker compose run --rm wpcli cache flush

# Test mobile via curl + verify CSS rules emesse
HTML_HOME=$(curl -s "http://localhost:8080/?_=task0verify")
HTML_AVV=$(curl -s "http://localhost:8080/avvocati/emiliano-saltelli/?_=task0verify")

echo "  CSS hero mobile rule: $(echo "$HTML_HOME" | grep -c '8ch')"
echo "  CSS sticky 8px rule: $(echo "$HTML_AVV" | grep -c 'sl-attorney__sticky')"
```

Visual check finale del direttore d'orchestra dopo Task 0 prima di proseguire a Task 1+.

---

## TASK 1 — Inventory URL da testare (5 min)

```bash
# 4 avvocati
docker compose run --rm wpcli post list --post_type=avvocato \
    --fields=ID,post_name --format=csv

# 19 competenze (di cui 3 tier-1 e 16 tier-2)
docker compose run --rm wpcli post list --post_type=competenza \
    --fields=ID,post_name --format=csv

# Page standard
docker compose run --rm wpcli post list --post_type=page \
    --fields=ID,post_name --format=csv | head -10

# Sample blog post
docker compose run --rm wpcli post list --post_type=post \
    --fields=ID,post_name --post_status=publish --format=csv | head -3
```

Salva URL representative in `.claude/knowledge/design/sessione-1/reports/template-polish/template-urls.md`.

---

## TASK 2 — Smoke test sistematico 9 template (15 min)

```bash
test_template() {
    local url=$1
    local label=$2
    HTML=$(curl -s "http://localhost:8080$url")
    HTTP=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:8080$url")
    SIZE=$(echo "$HTML" | wc -c | tr -d ' ')
    H1_COUNT=$(echo "$HTML" | grep -c "<h1")
    SCHEMA=$(echo "$HTML" | grep -c "application/ld+json")
    SL_HITS=$(echo "$HTML" | grep -oE "sl-[a-z]+" | wc -l | tr -d ' ')

    printf "  %-50s HTTP %s · %sb · %sH1 · %sschema · %ssl-*\n" \
           "$label ($url)" "$HTTP" "$SIZE" "$H1_COUNT" "$SCHEMA" "$SL_HITS"
}

test_template "/" "Homepage"
test_template "/avvocati/emiliano-saltelli/" "Avv. Emiliano (foto reale)"
test_template "/avvocati/fabiana-saltelli/" "Avv. Fabiana (placeholder)"
test_template "/avvocati/antonia-battista/" "Avv. Antonia (placeholder)"
test_template "/avvocati/stefano-gaetano-tedesco/" "Avv. Stefano (placeholder)"
test_template "/avvocati/" "Archive Avvocato"
test_template "/competenze/diritto-tributario/" "Competenza Tier-1 (Tributario)"
test_template "/competenze/diritto-del-lavoro/" "Competenza Tier-1 (Lavoro)"
test_template "/competenze/diritto-di-famiglia-lgbtq/" "Competenza Tier-1 (Famiglia LGBTQ)"
test_template "/competenze/recupero-crediti/" "Competenza Tier-2 (Recupero)"
test_template "/competenze/responsabilita-medica/" "Competenza Tier-2 (Malasanità)"
test_template "/competenze/" "Archive Competenza"
test_template "/tipo-area/privati/" "Taxonomy Privati"
test_template "/tipo-area/imprese/" "Taxonomy Imprese"
test_template "/tipo-area/contenzioso/" "Taxonomy Contenzioso"
test_template "/lo-studio/" "Page generic Lo Studio"
test_template "/contatti/" "Page generic Contatti"
test_template "/costi/" "Page Costi"
test_template "/non-esiste-404/" "404 Not Found"
test_template "/?s=tributario" "Search results"

# Sample blog post: prendi il primo da WP-CLI
BLOG_SLUG=$(docker compose run --rm wpcli post list --post_type=post --fields=name --format=csv --posts_per_page=1 2>&1 | tail -1 | tr -d '\r')
test_template "/$BLOG_SLUG/" "Single blog post"
```

**Tutti devono dare HTTP 200 (eccetto /non-esiste-404/ che deve dare 404), 1 H1, > 30 sl-* hits.**

Output table salvato in `.claude/knowledge/design/sessione-1/reports/template-polish/smoke-tests.md`.

---

## TASK 3 — Polish issue per template (60-90 min)

### 3.A — single-avvocato (Fabiana/Antonia/Stefano)

I 3 lawyer senza foto dovrebbero mostrare placeholder editoriale. Verifica:
- Layout asimmetrico foto sx + nome dx come da Emiliano
- Placeholder gradient editoriale "RITRATTO · 3:4" se foto mancante
- Sticky TEL/EMAIL/WhatsApp posizionato correttamente (post fix M3)
- Bio estesa con caratteri italiani corretti (è, à)
- Sezione "Si occupa di" + "Formazione" rendono in modo coerente

### 3.B — archive-avvocato

Verifica `/avvocati/`: 4 lawyer asimmetrici, hero piccolo "I quattro professionisti", layout coerente con sezione team della homepage.

### 3.C — single-competenza tier-2 (16 entries)

Verifica:
- NO sezione "Casi rappresentativi" su tier-2 (è solo tier-1)
- NO body extended (solo answer capsule + body normale)
- 3 FAQ invece di 5
- Layout più "minimal" di tier-1
- Tag `.sl-area__meta` corretto sul mobile (post fix M1)

### 3.D — archive-competenza (lista 19 aree)

Verifica:
- Lista tipografica con tier-1 first ordering
- Drop-cap accent solo su 3 tier-1
- Mobile responsive (post fix M1)

### 3.E — taxonomy template dedicato (15 min — IMPORTANTE)

Attualmente `/tipo-area/{slug}/` usa fallback `archive.php`. Crea `taxonomy-tipo-area.php` dedicato che:

```php
<?php
/**
 * Template: Taxonomy archive for tipo-area
 * Mostra le competenze taggate con un termine di tipo-area
 * Layout coerente con archive-competenza.php
 */

get_header();

$term = get_queried_object();
?>

<section class="sl-areas-archive">
    <header class="sl-section-head sl-areas__archive-head">
        <div class="sl-mono">Archivio</div>
        <h1 class="sl-areas__archive-title"><?php echo esc_html($term->name); ?></h1>
        <?php if ($term->description) : ?>
            <p class="sl-areas__archive-intro"><?php echo esc_html($term->description); ?></p>
        <?php endif; ?>
    </header>

    <div class="sl-areas__list">
        <?php
        if (have_posts()) :
            while (have_posts()) : the_post();
                $is_tier_1 = function_exists('get_field') ? (bool) get_field('is_tier_1_focus') : false;
                $lead = function_exists('get_field') ? (string) get_field('lead_breve') : '';
                ?>
                <article class="sl-area <?php echo $is_tier_1 ? 'sl-area--tier1' : ''; ?>">
                    <a class="sl-area__title" href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    <?php if ($lead) : ?>
                        <p class="sl-area__lead"><?php echo esc_html($lead); ?></p>
                    <?php endif; ?>
                    <div class="sl-area__meta">
                        <?php echo $is_tier_1 ? 'Tier 1 · Approfondimento' : 'Approfondisci'; ?> →
                    </div>
                </article>
                <?php
            endwhile;
        else :
            ?>
            <p class="sl-areas__empty">Nessuna competenza attualmente in questa categoria.</p>
            <?php
        endif;
        ?>
    </div>
</section>

<?php get_footer(); ?>
```

(Adatta classi CSS al pattern già esistente nel tema. Il template sopra è uno schema base.)

### 3.F — single.php (blog post)

Verifica un blog post esistente:
- Hero con categoria mono uppercase + data + autore + reading time
- Drop-cap su prima lettera body
- TOC sticky destra desktop (se implementato)
- Profilo autore in fondo (se autore associato a CPT avvocato)
- 3 articoli correlati
- Schema Article + BreadcrumbList

### 3.G — page.php generic (per /lo-studio/, /contatti/)

Verifica:
- Breadcrumb funziona (post fix recente .sl-page wrapper)
- H1 + body content stile editoriale coerente con /costi/
- Mobile responsive

### 3.H — 404.php + search.php

Verifica:
- /non-esiste/ ritorna 404 con design system applicato (NON white default)
- /?s=tributario ritorna risultati con layout coerente

---

## TASK 4 — Cross-viewport responsive verify (15 min)

Per i 4 template più critici, suggerisci a Duccio (orchestrator) di fare visual check via Claude in Chrome:

| Template | Viewport | Cosa verificare |
|---|---|---|
| Homepage | 375 / 768 / 1440 | M1 + M2 fix applicati, no horizontal scroll |
| Single avvocato Emiliano | 375 / 1440 | M3 sticky fix applicato |
| Single competenza tier-1 | 375 / 1440 | FAQ accordion `+/-` funzionante |
| /costi/ | 375 / 1440 | Layout asimmetrico desktop, stack mobile |

---

## TASK 5 — Schema validation locale (15 min)

```bash
# Estrai e valida schema su 4 URL chiave
for URL in "/" "/avvocati/emiliano-saltelli/" "/competenze/diritto-tributario/" "/costi/"; do
    echo "─── $URL ───"
    curl -s "http://localhost:8080$URL" \
        | grep -oE '<script type="application/ld\+json">[^<]*</script>' \
        | sed 's/<[^>]*>//g' \
        | head -3 \
        | while IFS= read -r line; do
            echo "$line" | python3 -c "import sys, json; d=json.load(sys.stdin); print(' ✓ Valid:', d.get('@type', d.get('@graph', [{}])[0].get('@type', 'unknown')))" 2>&1 \
                || echo " ✗ Invalid JSON"
          done
    echo ""
done
```

Tutti i blocchi schema devono parsare come JSON valido.

---

## TASK 6 — Bump version + final smoke test (5 min)

```bash
sed -i.bak 's/Version: 0.7.0-beta-pain-points-fixed/Version: 0.8.0-beta-templates-mobile/' wp-content/themes/saltelli/style.css
sed -i.bak "s/define('SALTELLI_THEME_VERSION', '0.7.0-beta-pain-points-fixed')/define('SALTELLI_THEME_VERSION', '0.8.0-beta-templates-mobile')/" wp-content/themes/saltelli/functions.php
rm -f wp-content/themes/saltelli/{style.css,functions.php}.bak

docker compose run --rm wpcli cache flush
docker compose run --rm wpcli transient delete --all
docker compose run --rm wpcli rewrite flush --hard
```

---

## Report finale

`.claude/knowledge/design/sessione-1/reports/template-polish/REPORT.md`:

1. ✅/❌ Task 0 (M1, M2, M3 mobile fix)
2. Smoke test 9 template: tabella HTTP/H1/Schema
3. Polish issue identificati P0/P1/P2 per template
4. Lista fix applicati
5. taxonomy-tipo-area.php template creato? Sì/No
6. Schema validation: ✓/✗ per ciascuna pagina chiave
7. Eventuali blocker o issue residui
8. Tempo totale

Poi **fermati**. Il direttore d'orchestra eseguirà nuovo Visual Walkthrough 12-point post-fix e committerà.

---

*v2 — Step E + Mobile Fix integrati. Direttore d'orchestra: Claude (chat).*
