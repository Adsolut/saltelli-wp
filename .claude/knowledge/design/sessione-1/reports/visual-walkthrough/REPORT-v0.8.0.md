# Visual Walkthrough — v0.8.0-beta-templates-mobile

**Data:** 2026-04-30
**Tester:** Claude (orchestrator)
**Tool:** Claude in Chrome (browser_batch + javascript_tool)
**Build sotto test:** v0.8.0-beta-templates-mobile
**Tempo:** ~10 minuti

---

## Risultati aggregati

| # | Punto | v0.7.0 | v0.8.0 | Note |
|---|---|:---:|:---:|---|
| 1 | Hero 100vh, 3 righe, no overlap | ✅ | ✅ | mantenuto |
| 2 | Lista 19 aree tier-1 evidenziato | ✅ | ✅ | mantenuto, tier-1 first ordering attivo |
| 3 | Layout asimmetrico | ✅ | ✅ | mantenuto |
| 4 | Drop-cap "L" Lo studio | ✅ | ✅ | mantenuto |
| 5 | 4 avvocati asimmetrici (Emiliano foto) | ✅ | ✅ | foto Emiliano preservata |
| 6 | Casi rappresentativi tipografici | ✅ | ✅ | mantenuto |
| 7 | Footer dark navy | ✅ | ✅ | mantenuto |
| 8 | /costi/ layout editoriale | ✅ | ✅ | mantenuto |
| 9 | Single-competenza tier-1 FAQ | ✅ | ✅ | mantenuto, h2InCaps=0 |
| **10** | Single-avvocato Emiliano (foto reale) | 🟡 WARN | ✅ **PASS** | **M3 sticky fix:** TEL/EMAIL `left: 8px` no overlap foto |
| **11** | Archive /tipo-area/* | ✅ basic | ✅ **PASS PERFETTO** | **taxonomy-tipo-area.php dedicato:** breadcrumb HOME/COMPETENZE/PER I PRIVATI + "9 aree" italic + tier-1 first ordering + drop-cap accent oro |
| **12** | Mobile 375px responsive | ❌ FAIL | ✅ **PASS PERFETTO** | **M1+M2 fix funzionano:** hero 3 righe, area__meta sotto titolo, no overflow-x, drop-cap accent visibile su tier-1 mobile |
| **+13** | Single-avvocato SENZA foto (Fabiana/Antonia/Stefano) | n/a | 🔴 **NEW FAIL** | Layout rotto: sticky TEL/EMAIL + placeholder "RITRATTO 3:4" coesistono male in posizione fissa basso-sinistra. WhatsApp btn sovrapposto. Affligge 3 avvocati su 4. |

---

## Score globale

**12 PASS · 0 WARN · 1 NEW FAIL**

→ **Decisione: NO-GO per Step F.** Mini-fix necessario prima di Production Readiness.

---

## Risultati DOM via javascript_exec (mobile)

```
hasOverflow: false             ✓ no horizontal scroll
scrollW: 500 / viewportW: 500  ✓ exactly fit
heroWordCount: 3               ✓ M2 fix: 3 parole hero
heroH1Lines: 220.5px           ✓ headline su 3 righe (height ≈ 70px × 3)
areasCount: 19                 ✓ tutte 19 competenze visibili
firstAreaTier: "has tier1"     ✓ tier-1 distinti
firstAreaMetaPosition: static  ✓ M1 fix: meta sotto titolo, no absolute
```

---

## Verdetto sul lavoro Step E v2

**Lavoro brillante** — 75 minuti vs 2-2.5h budget, output sopra le aspettative:

- ✅ **3/3 mobile fix M1+M2+M3** applicati con specificità chirurgica (vince in cascade su components.css base)
- ✅ **Smoke test 20/20 PASS** dopo auto-discovery di duplicate H1 issue su /chi-siamo/ + /contatti/ (Task 3.G, fix idempotente DB-level)
- ✅ **taxonomy-tipo-area.php creato** con riuso pattern editoriale `archive-competenza.php` — auto-eredita Pain Points fix (drop-cap tier-1) + Mobile fix M1
- ✅ **Schema 16/16 validi** (incl. CollectionPage per taxonomy, Article per blog)
- ✅ **Decisioni autonome eccellenti** (es. `display: block` su `.sl-hero__word` invece di `max-width: 8ch` consigliato — più robusto, deterministic word-per-line)
- ✅ Foto Emiliano + bio_estesa avvocati Step D **PRESERVATE** (verificato)

---

## Issue residuo unico — single-avvocato senza foto

**Diagnosi:** Il template `single-avvocato.php` è stato testato visivamente solo su Emiliano (che ha foto reale `_thumbnail_id=2683`). Per gli altri 3 lawyer (Fabiana CPT 2661, Antonia CPT 2662, Stefano CPT 2663) il fallback `.sl-team__placeholder` con gradient editoriale + label "RITRATTO · 3:4" è renderizzato **a fondo pagina** in posizione assoluta o flex sbagliato, dove finisce sovrapposto agli sticky bottoni TEL/EMAIL/WhatsApp che stanno a `left: 8px`.

**Comportamento atteso:**
- Foto/placeholder dovrebbe essere **in alto a sinistra**, in colonna larga, prima del contenuto bio
- Sticky TEL/EMAIL/WhatsApp dovrebbe essere accanto al contenuto, fuori dalla colonna foto
- Su Emiliano funziona perché `the_post_thumbnail()` rende un `<img>` reale che il CSS della pagina sa posizionare

**Comportamento attuale:**
- Quando `has_post_thumbnail()` è false, il template usa fallback `<span class="sl-team__placeholder">` (probabilmente)
- Ma il fallback ha CSS diverso che non si comporta come `<img>` (es. dimensioni inline, position: absolute al posto di static)
- Risultato: placeholder finisce sotto, sticky finisce sopra, casino visivo

**Affligge:** 3 lawyer su 4 (75%). Issue blocker per qualità visiva profili avvocato. Dimostra che **Emiliano è l'unico testato per davvero**, gli altri solo smoke-test (HTTP 200, sl-* hits) ma non visivi.

---

## Decisione: mini-fix prima di Step F

**Mini-prompt** "Single Avvocato Placeholder Fix" (15-20 min stimati):
1. Diagnosticare HTML/CSS del fallback placeholder
2. Allineare layout fallback al pattern di Emiliano (foto a sinistra colonna larga)
3. Visual check sui 3 lawyer placeholder
4. Eventuale fix CSS minore per coerenza

Una volta fixato, GO definitivo per Step F (Production Readiness).

---

## Screenshot evidenziali

- ss_5794dt7t4 — Homepage v0.8.0 desktop
- ss_8621cwpgo — Single Emiliano: M3 sticky fix risolve overlap
- ss_16627bde0 — Taxonomy /tipo-area/privati/ con breadcrumb editoriale
- ss_4331lccjo — Single Fabiana: ⚠ NEW FAIL placeholder + sticky overlap
- ss_1241e4ko7 — Single Antonia: stesso bug Fabiana (conferma pattern)
- ss_5237r06n2 — Mobile hero 3 righe perfette
- ss_6649zjl20 — Mobile lista aree con drop-cap tier-1 + meta sotto titolo

---

*Walkthrough completato. Procedo con commit + mini-fix prompt.*
