# Prompt — Single-Avvocato Placeholder Fix Agent (mini)

> **Per Claude Code in nuova sessione (o sessione corrente).** Apri `saltelli-wp/`, leggi questo file, eseguilo. Tempo previsto: 15-20 min. Mini-fix focused su 1 bug visivo.
> **PRECEDENZA:** Step E v2 completato. v0.8.0-beta-templates-mobile.

---

## Tu sei

Mini-fix agent. Il direttore d'orchestra (Claude in chat) ha eseguito Visual Walkthrough 12-point post-Step E v2 e ha trovato:

- **12 PASS · 0 WARN · 1 NEW FAIL** — 12/13 punti perfetti
- **Issue residuo unico:** single-avvocato.php rende layout rotto quando manca foto avvocato (3/4 lawyer affetti: Fabiana, Antonia, Stefano).

Il tuo lavoro: fixare quell'unico issue. Niente altro.

---

## Diagnosi del bug (dal walkthrough orchestrator)

Quando un single-avvocato NON ha foto (`has_post_thumbnail()` = false), il template `single-avvocato.php` cade su un fallback placeholder che ha CSS diverso da quello applicato a `<img>` reale:

- Su **Emiliano** (foto reale, `_thumbnail_id=2683`): foto a sinistra, sticky TEL/EMAIL fuori a `left: 8px` ✓
- Su **Fabiana / Antonia / Stefano** (no foto): placeholder gradient editoriale "RITRATTO · 3:4" rendering **a fondo pagina basso-sinistra** sovrapposto agli sticky bottoni → risultato visivo rotto

**Comportamento atteso:** il placeholder dovrebbe essere **in alto a sinistra in colonna larga**, identico al posizionamento della foto di Emiliano. Solo il **contenuto interno** del box (gradient + label "RITRATTO · 3:4") cambia.

---

## Letture obbligatorie

1. `CLAUDE.md` — hard constraints
2. `wp-content/themes/saltelli/single-avvocato.php` — il template
3. `wp-content/themes/saltelli/assets/css/sections.css` — cerca le regole `.sl-attorney__hero`, `.sl-attorney__portrait`, `.sl-team__placeholder`
4. `wp-content/themes/saltelli/assets/css/components.css` — backup ricerca

---

## Hard rules

| Rule | Reason |
|---|---|
| **Mai sovrascrivere `_thumbnail_id` di Emiliano (CPT 2660)** | Foto reale Step C.5 |
| **Mai modificare il template per Emiliano** — la sua resa funziona perfettamente | No-regression |
| Solo CSS o markup HTML del placeholder fallback | Scope minimal |
| Dopo fix, rilanciare cache flush + curl test su tutti e 4 lawyer | Idempotency |
| Visual check via curl HTML (l'orchestrator farà screenshot) | Ottimismo controllato |

---

## Task 1 — Diagnosi precisa (5 min)

Identifica esattamente come il template gestisce il fallback senza foto:

```bash
# 1. Cerca nel template come è gestito has_post_thumbnail
grep -n "has_post_thumbnail\|sl-team__placeholder\|sl-attorney__portrait\|sl-attorney__hero" wp-content/themes/saltelli/single-avvocato.php

# 2. Confronta DOM su Emiliano (foto) vs Fabiana (placeholder)
echo "─── Emiliano DOM ───"
curl -s "http://localhost:8080/avvocati/emiliano-saltelli/" | grep -oE '<(div|span|figure|img|aside)[^>]*sl-attorney__portrait[^>]*>|<(div|span|figure)[^>]*sl-team__placeholder[^>]*>|<aside[^>]*sl-attorney__sticky[^>]*>' | head -5

echo "─── Fabiana DOM ───"
curl -s "http://localhost:8080/avvocati/fabiana-saltelli/" | grep -oE '<(div|span|figure|img|aside)[^>]*sl-attorney__portrait[^>]*>|<(div|span|figure)[^>]*sl-team__placeholder[^>]*>|<aside[^>]*sl-attorney__sticky[^>]*>' | head -5

# 3. CSS rules attive
echo "─── CSS .sl-attorney__portrait ───"
grep -B 1 -A 10 '\.sl-attorney__portrait' wp-content/themes/saltelli/assets/css/sections.css | head -30

echo "─── CSS .sl-team__placeholder ───"
grep -B 1 -A 10 '\.sl-team__placeholder' wp-content/themes/saltelli/assets/css/components.css wp-content/themes/saltelli/assets/css/sections.css | head -30
```

Da questa analisi capirai esattamente:
- Quale tag/classe usa il template per la foto (es. `<figure class="sl-attorney__portrait">`)
- Quale tag/classe usa per il placeholder (es. `<span class="sl-team__placeholder">`)
- Perché lo styling è diverso (probabilmente il placeholder ha `display: inline-block` + `position: relative` mentre l'`<img>` real ha CSS che lo posiziona in colonna)

---

## Task 2 — Fix unificato del layout placeholder (10 min)

**Strategia consigliata:** non cambiare la logica PHP del template (rischio regressione), ma allineare CSS del placeholder al layout di `<img>` real.

**Approccio:**

1. **Identifica wrapper comune** che envelope sia foto che placeholder. Probabilmente è `.sl-attorney__portrait` o `.sl-attorney__hero-media` o simile.

2. Se il wrapper è già lo stesso, il fix è solo CSS interno per il placeholder:

```css
/* ═══════════════════════════════════════════════════════════════
   FIX — Single avvocato placeholder layout aligned with foto reale
   Issue: placeholder rendering basso-sinistra invece di alto-larghezza-piena
   ═══════════════════════════════════════════════════════════════ */

/* Il wrapper della media è lo stesso per foto + placeholder */
.sl-attorney__portrait,
.sl-attorney__hero-media,
.sl-attorney__media {
    position: relative;
    width: 100%;
    aspect-ratio: 3 / 4;       /* portrait formato 3:4 come foto Emiliano */
    overflow: hidden;
    background: var(--surface);
    margin: 0;
}

.sl-attorney__portrait img,
.sl-attorney__hero-media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

/* Placeholder fallback DENTRO il wrapper */
.sl-team__placeholder,
.sl-attorney__placeholder {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: flex-end;
    justify-content: flex-start;
    padding: 24px;
    background: linear-gradient(
        135deg,
        #c8c5be 0%,
        #6e6c66 100%
    );
    color: rgba(255, 255, 255, 0.6);
    font-family: var(--font-mono);
    font-size: 11px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

/* Mobile: stack column normal */
@media (max-width: 1023px) {
    .sl-attorney__portrait,
    .sl-attorney__hero-media,
    .sl-attorney__media {
        max-width: 100%;
        margin-bottom: 32px;
    }
}
```

**SE il template usa wrapper diversi** per foto vs placeholder (es. `<figure>` per foto, `<span>` per placeholder), modifica il template PHP per emettere lo **stesso wrapper** sempre, e il placeholder come contenuto:

```php
<figure class="sl-attorney__portrait">
    <?php if (has_post_thumbnail()) : ?>
        <?php the_post_thumbnail('saltelli-attorney-portrait'); ?>
    <?php else : ?>
        <span class="sl-attorney__placeholder">Ritratto · 3:4</span>
    <?php endif; ?>
</figure>
```

**Adatta** ai nomi classi reali che troverai nel codice. Non inventare classi nuove se ce ne sono già.

---

## Task 3 — Cache flush + verify visuale (5 min)

```bash
docker compose run --rm wpcli cache flush
docker compose run --rm wpcli transient delete --all

# Test 4 lawyer
for SLUG in "emiliano-saltelli" "fabiana-saltelli" "antonia-battista" "stefano-gaetano-tedesco"; do
    HTML=$(curl -s "http://localhost:8080/avvocati/$SLUG/?_=fixverify")
    HTTP=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:8080/avvocati/$SLUG/?_=fixverify")
    HAS_PORTRAIT=$(echo "$HTML" | grep -c "sl-attorney__portrait\|sl-attorney__hero-media\|sl-attorney__media")
    HAS_THUMB=$(echo "$HTML" | grep -c "<img.*saltelli-attorney-portrait\|wp-image-2683")
    HAS_PLACEHOLDER=$(echo "$HTML" | grep -c "sl-team__placeholder\|sl-attorney__placeholder")
    
    printf "  %-30s HTTP %s · portrait wrapper: %s · img: %s · placeholder: %s\n" \
           "$SLUG" "$HTTP" "$HAS_PORTRAIT" "$HAS_THUMB" "$HAS_PLACEHOLDER"
done
```

**Atteso:**
- Tutti HTTP 200
- Tutti hanno `portrait wrapper: ≥1` (struttura unificata)
- Solo Emiliano ha `img: ≥1`
- Solo Fabiana/Antonia/Stefano hanno `placeholder: ≥1`

---

## Task 4 — Bump version + commit-ready

```bash
sed -i.bak 's/Version: 0.8.0-beta-templates-mobile/Version: 0.8.1-beta-attorney-placeholder/' wp-content/themes/saltelli/style.css
sed -i.bak "s/define('SALTELLI_THEME_VERSION', '0.8.0-beta-templates-mobile')/define('SALTELLI_THEME_VERSION', '0.8.1-beta-attorney-placeholder')/" wp-content/themes/saltelli/functions.php
rm -f wp-content/themes/saltelli/{style.css,functions.php}.bak

docker compose run --rm wpcli cache flush
```

---

## Report finale

Scrivi `.claude/knowledge/design/sessione-1/reports/single-avvocato-placeholder-fix/REPORT.md`:

1. Diagnosi precisa (cosa ha trovato Task 1)
2. Approccio scelto (CSS-only? markup change? entrambi?)
3. File modificati
4. Smoke test 4 lawyer (output Task 3)
5. Eventuali decisioni autonome
6. Tempo impiegato

Poi **fermati**. Il direttore d'orchestra farà visual check finale e se OK procederà a Step F.

---

*v1.0 — Mini-fix focused. Direttore d'orchestra: Claude (chat).*
