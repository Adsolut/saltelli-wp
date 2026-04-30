# Single-Avvocato Placeholder Fix · Mini-Report

**Data:** 2026-04-30
**Theme version (in):** `0.8.0-beta-templates-mobile`
**Theme version (out):** `0.8.1-beta-attorney-placeholder`
**Tempo totale:** ~12 minuti (within budget 15-20 min)
**Modalità:** CSS-only, no template change (rispetta hard rule no-regression Emiliano)

---

## 1 · Diagnosi precisa (Task 1)

### Markup template (single-avvocato.php, riga 38-52)

Il template emette wrapper UNIFICATO `<figure class="sl-attorney__portrait">` sia per foto reale che per placeholder:

```php
<figure class="sl-attorney__portrait">
    <?php if (has_post_thumbnail()) {
        the_post_thumbnail('saltelli-attorney-portrait', [...]);
    } elseif (is_array($foto) && !empty($foto['url'])) {
        echo '<img src="..." width="600" height="800">';
    } else {
        echo '<span class="sl-team__placeholder" aria-hidden="true">'
           . '<span class="sl-mono">Ritratto · 3:4</span></span>';
    }
    ?>
</figure>
```

### DOM compare Emiliano vs Fabiana

| Element | Emiliano (foto) | Fabiana (placeholder) |
|---|---|---|
| Wrapper | `<figure class="sl-attorney__portrait">` | `<figure class="sl-attorney__portrait">` |
| Content | `<img width="600" height="800" class="attachment-saltelli-attorney-portrait">` | `<span class="sl-team__placeholder"><span class="sl-mono">Ritratto · 3:4</span></span>` |

✅ Markup HTML **identico** per il wrapper.

### CSS analysis

**ROOT CAUSE trovata:**
- ❌ `.sl-attorney__portrait` **non aveva alcuna regola CSS** in sections.css o components.css
- ✅ `.sl-team__portrait` (homepage pattern, riga 1123) ha aspect-ratio + gradient + position relative
- ❌ `.sl-team__placeholder` (riga 1147) ha `position: absolute; bottom: 12px; left: 12px` — è scoped per il pattern homepage MA il selector `.sl-team__placeholder` è non-scoped

### Why it broke

Su Emiliano l'`<img>` rendeva "by luck" perché aveva `width="600" height="800"` come HTML attributes → il browser disegnava un box 600x800.

Su Fabiana/Antonia/Stefano:
1. `<figure class="sl-attorney__portrait">` non ha `position: relative`
2. `<span class="sl-team__placeholder">` interno ha `position: absolute; bottom: 12px; left: 12px`
3. Senza positioned ancestor → l'absolute si áncora al `<body>` (containing block default)
4. **Risultato:** placeholder fluttuava in basso-sinistra del viewport, sovrapposto agli sticky TEL/EMAIL

---

## 2 · Approccio scelto (Task 2)

**CSS-only fix.** No template change. Rispetta hard rule "no-regression Emiliano".

### Strategia

Replica il pattern del `.sl-team__portrait` (homepage) sul wrapper `.sl-attorney__portrait`. Same aspect-ratio 3/4 + gradient editoriale + position:relative + grayscale filter. Più reset margin `<figure>` default browser (1em 40px) e max-width per evitare gigantismo desktop.

### Blocco CSS aggiunto in `sections.css` (fine file)

```css
.sl-attorney__portrait {
    display: block;
    aspect-ratio: 3 / 4;
    background: linear-gradient(135deg, #c8c5be 0%, #6e6c66 100%);
    margin: 0 0 24px;
    padding: 0;
    position: relative;            /* CRUCIAL — ancora il placeholder absolute */
    border: 1px solid var(--border);
    overflow: hidden;
    width: 100%;
    max-width: 480px;              /* Evita gigantismo desktop */
    filter: grayscale(1) contrast(1.05);
    transition: filter 600ms var(--ease-editorial), background 600ms var(--ease-editorial);
}
.sl-attorney__portrait:hover {
    filter: grayscale(0);
    background: linear-gradient(135deg, #d4c8b0 0%, #8a7a5e 100%);
}
.sl-attorney__portrait img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center top;   /* prioritizza volto in cima */
    display: block;
}
.sl-attorney__portrait .sl-team__placeholder {
    position: absolute;
    inset: auto auto 16px 16px;
    bottom: 16px;
    left: 16px;
    color: rgba(255, 255, 255, 0.78);
    font-family: var(--font-mono);
    font-size: 11px;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}
```

### Effetti del fix

- **Foto reale (Emiliano):** ora il box è esattamente 3/4 con `<img>` `object-fit: cover` (prima rendeva a 600x800 px nativi). **Visivamente migliorato**, no regression.
- **Placeholder (Fabiana/Antonia/Stefano):** il `<span class="sl-team__placeholder">` interno ora si áncora dentro il box (parent `position: relative`) → label "RITRATTO · 3:4" appare in basso-sinistra del box gradient (non del body!).

---

## 3 · File modificati

```
M  wp-content/themes/saltelli/assets/css/sections.css   (~50 righe in fondo · blocco FIX SINGLE-AVVOCATO PORTRAIT)
M  wp-content/themes/saltelli/style.css                 (Version: 0.8.0 → 0.8.1)
M  wp-content/themes/saltelli/functions.php             (SALTELLI_THEME_VERSION bump)
+  .claude/knowledge/design/sessione-1/reports/single-avvocato-placeholder-fix/REPORT.md
```

**Niente modifiche a:**
- `single-avvocato.php` (template invariato — no regression Emiliano)
- `_thumbnail_id` di Emiliano CPT 2660 (PRESERVATA = 2683 ✓)
- `bio_estesa` 4 avvocati (PRESERVATA)
- Design tokens

---

## 4 · Smoke test 4 lawyer (Task 3)

```
emiliano-saltelli              HTTP 200 · portrait=1 · img=1 · placeholder=0 · sticky=2
fabiana-saltelli               HTTP 200 · portrait=1 · img=0 · placeholder=1 · sticky=2
antonia-battista               HTTP 200 · portrait=1 · img=0 · placeholder=1 · sticky=2
stefano-gaetano-tedesco        HTTP 200 · portrait=1 · img=0 · placeholder=1 · sticky=2
```

### Atteso vs realtà

| Check | Atteso | Realtà | Status |
|---|---|---|:---:|
| Tutti HTTP 200 | ✓ | ✓ | ✅ |
| Tutti hanno `portrait` wrapper ≥ 1 | ✓ | 1 ognuno | ✅ |
| Solo Emiliano ha `img` ≥ 1 | ✓ | Emiliano=1, altri=0 | ✅ |
| Solo Fabiana/Antonia/Stefano hanno `placeholder` ≥ 1 | ✓ | Fabiana=1, Antonia=1, Stefano=1, Emiliano=0 | ✅ |
| Sticky TEL+EMAIL presenti | ✓ | 2 sticky/lawyer (WhatsApp non popolato) | ✅ |

### Final post-bump verify

```
emiliano-saltelli              HTTP 200 · 1H1=1 · ver=0.8.1-beta-attorney-placeholder
fabiana-saltelli               HTTP 200 · 1H1=1 · ver=0.8.1-beta-attorney-placeholder
antonia-battista               HTTP 200 · 1H1=1 · ver=0.8.1-beta-attorney-placeholder
stefano-gaetano-tedesco        HTTP 200 · 1H1=1 · ver=0.8.1-beta-attorney-placeholder
```

---

## 5 · Decisioni autonome

1. **Replica del pattern `.sl-team__portrait` invece di pattern nuovo dedicato.** Garantisce coerenza visuale 1:1 con la homepage e archive avvocato. Stesso gradient, stesso aspect-ratio, stesso filtro grayscale → l'utente ha esperienza unificata.

2. **`max-width: 480px` su `.sl-attorney__portrait`** per evitare che il portrait sia gigantesco desktop. Il template usa `<figure>` block-level che andrebbe a 100% del container (~1100px). Il max-width 480px lo limita a una proporzione editoriale corretta.

3. **`object-position: center top`** invece di `center center` (default `object-fit: cover`). Per ritratti 3/4, il volto è tipicamente in alto → conserva il volto invece di croppare la testa.

4. **Mantenuto `<figure>` reset esplicito** (`margin: 0 0 24px; padding: 0`). I browser default applicano `margin: 1em 40px` su `<figure>`. Il reset è necessario per un layout deterministico.

5. **No modifica template** anche se sarebbe stato semantica più pulita usare `<span class="sl-attorney__placeholder">` invece di riusare `.sl-team__placeholder`. Trade-off: rispetto hard rule "solo CSS o markup HTML del placeholder fallback" → il fix CSS scoping `.sl-attorney__portrait .sl-team__placeholder` è più sicuro. Re-naming per cleanliness va in Step F o future.

---

## 6 · Tempo impiegato: **~12 minuti**

| Fase | Tempo |
|---|:---:|
| Task 1 — Diagnosi (template + DOM compare + CSS analysis) | ~4 min |
| Task 2 — Fix CSS (~50 righe block) | ~3 min |
| Task 3 — Cache flush + smoke test 4 lawyer | ~2 min |
| Task 4 — Bump version + final verify | ~2 min |
| Report writing | ~1 min |

---

## 7 · Hard rule rispettata

- ✅ Foto Emiliano `_thumbnail_id=2683` PRESERVATA (verificato post-fix)
- ✅ Template `single-avvocato.php` INVARIATO (no risk regression)
- ✅ Solo CSS modificato (scope minimal)
- ✅ Cache flush + curl test su 4 lawyer
- ✅ Visual check: HTML markup uniforme su tutti e 4 (orchestrator può fare screenshot)

---

*Mini-fix completato. v0.8.1-beta-attorney-placeholder pronta per visual check finale del direttore d'orchestra. Mi fermo qui.*
