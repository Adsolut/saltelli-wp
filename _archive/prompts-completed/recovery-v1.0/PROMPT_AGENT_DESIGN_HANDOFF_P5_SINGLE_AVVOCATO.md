# PROMPT AGENT — Design Handoff Wave P5 · single-avvocato verify & drift cleanup

> **Scope**: verificare template `single-avvocato.php` + `.sl-attorney*` di sections.css vs `design-handoff/single-avvocato/index.jsx`. Pattern lawyer-card riusato in altre Pages (chi-siamo grid, archive-avvocato). **Atteso 95% già implementato**, ~2-3 righe CSS fix.
>
> **Branch**: `feat/design-handoff-single-avvocato`
> **Stima**: 0.5-1h (severity 🟡 MEDIUM ma fix minimi)
> **Modalità**: lean, no version bump (chore frontend)
> **Sessione**: una sola Claude Code, no parallelismo.

---

## CONTESTO

Wave 5/12 sequenza Design Handoff. P1 chrome mergeata, P2 footer skipped (0 drift), P3 home + P4 single-competenza-tier1 in sequenza prima di questa.

**4 pagine attorney**: Emiliano Saltelli, Fabiana Saltelli, Antonia Battista, Stefano Gaetano Tedesco.

**Decisioni orchestratore acquisite** (NON rinegoziare):
1. SoT design tokens = `tokens.css` corrente vince (KEEP CURRENT)
2. SCF data contract immutabile: `group_avvocato_v1.json` field esistenti, 🟢 COMPLIANT (0 additive)
3. `foto_ritratto` 🔴 **HARD-PROTECTED** (_thumbnail_id=2683 per Emiliano, Step C.5 photo integration Wave 0)
4. `bio_estesa` 🔴 **HARD-PROTECTED** (Step D content Wave 0, mai sovrascrivere)
5. **Aspect-ratio ritratto**: WP wins (3:4 mobile = foto reale verticale, 1:1 desktop). JSX 1:1 rigid è prototype-only — NON forzare 1:1 su mobile.
6. **Sticky CTA layout**: WP wins current (mobile inline sotto hero, desktop sticky left). Bottom-bar mobile = scope futuro, non P5.
7. **Pattern `.sl-area` riuso**: NESSUNA propagazione ad altri template (è scope specifico attorney).

**Pre-flight orchestratore già fatto** (Explore agent output):

| Element | JSX | WP attuale | Status | Action |
|---|---|---|---|---|
| Ritratto aspect-ratio (mobile) | 1:1 | 3:4 critical mobile | 🟢 **WP wins** (HARD-PROTECTED foto reale) | NO FIX |
| Ritratto aspect-ratio (desktop ≥1024) | 1:1 | 1:1 | ✓ match | NO FIX |
| H1 nome font-size | `clamp(56px, 6vw, 88px)` | `clamp(56px, 6vw, 88px)` | ✓ match | NO FIX |
| H1 nome letter-spacing | `-0.025em` | `-0.025em` (phantom §4) | ✓ match | NO FIX |
| H1 nome line-height | `0.98` | `0.98` | ✓ match | NO FIX |
| Role hero italic | `22px` | `var(--fs-lede)` (22px) | ✓ match | NO FIX |
| Bio breve italic lede | `22px` fisso (JSX L88) | `clamp(18px, 2vw, 22px)` | 🟡 PARTIAL | **DECIDI**: WP fluid wins (mobile 18px è migliore) o JSX fisso 22px? |
| Spec tag pill padding mobile | `6px 12px` (JSX L98) | `4px 12px` mobile, `5px 14px` desktop | 🟡 DRIFT minor | FIX: mobile `4px 12px` → `6px 12px` |
| Sticky CTA mobile | fixed top-left (JSX L112) | static inline sotto hero | 🟢 **WP wins** (decisione orchestratore) | NO FIX |
| Timeline year font-size | `28px italic` | `28px` (phantom §1) | ✓ match | NO FIX |
| Casi outcome font-size | `22px` | `22px` (phantom §2) | ✓ match | NO FIX |

**Attesa**: 1-2 fix CSS minor + 1 decisione "WP fluid wins vs JSX fixed" sul bio_breve clamp.

---

## ⚠️ HARD INVARIANT — SCF DATA CONTRACT PRESERVATION

`group_avvocato_v1.json` field esistenti:
- `hero_role` (text), `foto_ritratto` (image HARD-PROTECTED), `bio_breve` (text), `bio_estesa` (wysiwyg HARD-PROTECTED)
- `specializzazioni` (textarea), `aree_competenza_correlate` (post_object[]), `formazione` (post_object[])
- `casi_rappresentativi` (via helper), `email_pubblica`/`telefono_pubblico`/`whatsapp` (text)

**NON ACCETTABILE**: rinominare/rimuovere field, refactor CPT registration, cambiare lettura `foto_ritratto`/`bio_estesa`, sovrascrivere _thumbnail_id=2683.

---

## PRE-FLIGHT (5 min)

1. Leggi:
   - `CLAUDE.md` (Hard constraints incluso "Foto Emiliano `_thumbnail_id=2683` + `bio_estesa` avvocati preserved", Design system, "Design → Code handoff rule golden", Lessons learned)
   - `.claude/knowledge/audits/design-handoff/RECOMMENDATION.md` (§A KEEP CURRENT, §B P5 prioritization, §G Elena Impact, §C Risk)
   - `.claude/knowledge/audits/wave5-step4-sections-cleanup/02-phantom-values-remaining.md` (phantom §1/§2/§3d/§4 cross-ref)
   - **JSX source**: `design-handoff/single-avvocato/index.jsx`
   - **WP target**:
     - `wp-content/themes/saltelli/single-avvocato.php`
     - Blocchi `.sl-attorney*` in `wp-content/themes/saltelli/assets/css/sections.css`
   - `wp-content/themes/saltelli/acf-json/group_avvocato_v1.json` (verifica field invariati)

2. Verifica stato:
   ```sh
   git fetch origin
   git status
   git log --oneline -3   # atteso HEAD post-P4 merge
   git checkout -b feat/design-handoff-single-avvocato
   ```

3. Conferma in chat: branch creato + pre-flight letto + prosegui.

---

## PHASE 1 — VERIFY (10 min, focused)

### 1.A — Spot-check 95% già implementato

Confermare i match dal pre-flight (atteso ✓ tutti):
- `.sl-attorney__hero` grid 2-col desktop / 1-col mobile
- `.sl-attorney__portrait` aspect-ratio: 3:4 mobile, 1:1 desktop @≥1024
- `.sl-attorney__hero h1` (font-size clamp 56-88, ls -0.025em, lh 0.98)
- `.sl-attorney__hero .role` italic 22px
- `.sl-attorney__sticky` static mobile, sticky desktop
- `.sl-attorney__bio-prose` wp_kses_post + tipografia editoriale
- `.sl-attorney__competenze` grid 6 aree (con tier-1 drop-cap)
- `.sl-attorney__timeline` formazione (year 28px italic + titolo + istituzione)
- `.sl-attorney__casi` (id + desc + outcome 22px)
- `.sl-attorney__cta` button + heading

### 1.B — Drift fixabili (2 minori)

**Drift 1 — Spec tag pill padding mobile**:
- Localizza `.sl-attorney__specs li` o `.sl-team__specs li` (verifica selettore esatto)
- Mobile: `padding: 4px 12px` → `padding: 6px 12px` (JSX L98)
- Desktop: `padding: 5px 14px` → resta o sync a 6px 12px? **Verifica JSX desktop value**, applica match

**Drift 2 — Bio breve italic lede**:
- JSX L88: `font-size: 22px` fisso
- WP `.sl-attorney__lede`: `clamp(18px, 2vw, 22px)`
- **Decisione orchestratore**: lascia WP come è (clamp è UX-superiore su mobile, JSX prototype-only). NO FIX.
- Documenta in commit: "WP fluid clamp 18-22 wins, JSX 22px fisso è prototype-only".

### 1.C — Tabella drift finale in chat

Posta in chat:
- 1 fix CSS minor confermato (spec tag pill padding mobile)
- 1 decisione "WP wins" documentata (bio_breve clamp)
- Eventuali altri drift inattesi: segnala, NON fixare se non in scope.

---

## PHASE 2 — IMPLEMENT (10-15 min)

### 2.A — CSS fix in sections.css

Aggiungi/modifica con scope marker `/* === design-handoff single-avvocato P5 === */`:

```css
/* === design-handoff single-avvocato P5 === */

/* Spec tag pill padding sync to JSX (mobile) */
.sl-attorney__specs li,
.sl-attorney__hero-text .sl-attorney__specs li {
  padding: 6px 12px;  /* JSX L98 — sync da 4px 12px */
}

@media (min-width: 1024px) {
  .sl-attorney__specs li,
  .sl-attorney__hero-text .sl-attorney__specs li {
    padding: 6px 12px;  /* desktop coerenza JSX, se confermato; altrimenti 5px 14px */
  }
}
```

(Adatta selector esatto a quello che trovi in sections.css.)

### 2.B — Sync staging

```sh
rsync -avz wp-content/themes/saltelli/assets/css/ deploy@178.62.207.50:/var/www/saltelli/wp-content/themes/saltelli/assets/css/
ssh deploy@178.62.207.50 "cd /var/www/saltelli && sudo -u www-data wp cache flush --path=/var/www/saltelli"
```

No PHP changes attesi = no OPcache reload.

---

## PHASE 3 — SMOKE TEST (10 min)

### 3.A — Frontend curl smoke (4 URL attorney)

```sh
for SLUG in emiliano-saltelli fabiana-saltelli antonia-battista stefano-gaetano-tedesco; do
  echo "=== $SLUG ==="
  curl -s "https://staging.studiolegalesaltelli.it/chi-siamo/team/$SLUG/" | grep -cE "sl-attorney__hero|sl-attorney__portrait|sl-attorney__competenze"
  # atteso count >= 3
done
```

### 3.B — getComputedStyle spot-check

Su `/chi-siamo/team/emiliano-saltelli/` (Emiliano ha foto reale `_thumbnail_id=2683`):

- `.sl-attorney__portrait` (mobile ≤768): aspect-ratio atteso `3 / 4` (NON forzato a 1:1)
- `.sl-attorney__portrait` (desktop ≥1024): aspect-ratio atteso `1 / 1`
- `.sl-attorney__hero h1` (desktop 1440): font-size atteso ≈ `86px` (6vw), letter-spacing `-0.025em`
- `.sl-attorney__specs li` mobile: padding atteso `6px 12px` (post-fix)

### 3.C — Visual breakpoint test

- **375 mobile**: ritratto 3:4 verticale, foto Emiliano (LCP image), no sticky CTA bottom-bar
- **768 tablet**: ritratto 3:4
- **1024 desktop**: ritratto 1:1 (quadrato), sticky CTA visible left
- **1440 wide**: idem

### 3.D — Admin-side smoke (lesson Wave 4.7.fix.4)

WP Admin → Avvocato → Emiliano Saltelli → Modifica:
- Metabox SCF "Avvocato — Profilo completo (v1)" visibile
- `foto_ritratto` campo image popolato con ID 2683 ✓ HARD-PROTECTED
- `bio_estesa` campo wysiwyg popolato ✓ HARD-PROTECTED
- `hero_role`, `bio_breve`, `specializzazioni`, `aree_competenza_correlate`, `formazione`, `email_pubblica`, `telefono_pubblico` tutti intatti
- Save → frontend invariato

---

## PHASE 4 — COMMIT + PUSH

Se 1 fix applicato:

```sh
git add -A
git diff --cached --stat

git commit -m "feat(design-handoff): Wave P5 single-avvocato — spec tag pill padding sync (mobile)

Wave 5/12 sequenza Design Handoff. Template attorney 95% già implementato in WP (verify confermato 11 element match con design-handoff/single-avvocato/index.jsx).

1 fix CSS minor applicato (sections.css scope /* === design-handoff single-avvocato P5 === */):
- .sl-attorney__specs li padding mobile: 4px 12px → 6px 12px [JSX L98]

Decisioni orchestratore applicate:
- Aspect-ratio ritratto: WP wins (3:4 mobile foto reale HARD-PROTECTED, 1:1 desktop). JSX 1:1 rigid è prototype-only.
- Bio breve italic lede: WP wins clamp(18px,2vw,22px) — fluid è UX-superiore su mobile, JSX 22px fisso è prototype-only.
- Sticky CTA layout mobile: WP wins (static inline sotto hero). Bottom-bar mobile = scope futuro non P5.
- Pattern .sl-area riuso (chi-siamo grid, archive-avvocato): fix P5 NON propaga (scope specifico attorney).

SCF: 🟢 COMPLIANT. group_avvocato_v1 field invariati. foto_ritratto + bio_estesa HARD-PROTECTED preserved.

Smoke test:
- Frontend curl 4 URL attorney (emiliano-saltelli, fabiana-saltelli, antonia-battista, stefano-gaetano-tedesco): markup .sl-attorney__hero presente
- getComputedStyle: aspect-ratio mobile 3:4 + desktop 1:1, h1 font-size ≈86px @1440, spec pill padding 6px 12px
- Admin: metabox SCF intatto, foto_ritratto _thumbnail_id=2683 preserved, bio_estesa preserved

No version bump (chore frontend, no schema/data change).
Branch: feat/design-handoff-single-avvocato · 1 file changed · ~2-3 righe CSS"

git push origin feat/design-handoff-single-avvocato
```

Se 0 fix (template 100% allineato):

```sh
git checkout main
git branch -d feat/design-handoff-single-avvocato
echo "Wave P5 single-avvocato: 0 drift fixabili, template 100% allineato. Branch deleted."
```

---

## OUTPUT FINALE in chat

- Tabella drift PHASE 1 (1-2 fix attesi)
- Decisioni orchestratore applicate
- Smoke test risultati (4 URL + admin + getComputedStyle)
- SHA commit pushato (o "no fix, branch deleted")
- ETA proposto P6 taxonomy-tipo-area

---

## HARD RULES

1. **HARD-PROTECTED**: `foto_ritratto` ID=2683 Emiliano + `bio_estesa` mai sovrascrivere. NO touch CPT registration.
2. **Token alignment §A**: KEEP CURRENT, mai toccare `:root`.
3. **SCF schema immutabile**: 0 additive, 0 refactor.
4. **PHP single-avvocato.php**: invariato (no touch). Solo CSS.
5. **Aspect-ratio decisione**: WP wins per mobile 3:4 (foto reale verticale).
6. **Admin-side smoke obbligatorio** (lesson Wave 4.7.fix.4): verifica metabox SCF + foto + bio_estesa intatti.
7. **One-writer-at-a-time**: UNICA sessione Code attiva.

---

## DECISIONE AUTONOMA AUTORIZZATA

- Selettore esatto `.sl-attorney__specs li` vs `.sl-team__specs li`: usa quello che trovi in sections.css (entrambi probabili).
- Spec pill padding desktop: se JSX specifica `6px 12px` desktop, sync; altrimenti mantieni WP `5px 14px`.
- Eventuale altro drift inatteso scoperto in PHASE 1: segnala in chat, fixa SE è 1-line minor + scope chiaramente attorney; altrimenti DEFER a wave futura.

---

## TONO

Direct, concrete, zero filler. Stile commit usato dal progetto.

---

*Wave P5/12 sequenza Design Handoff. Prossima: P6 taxonomy-tipo-area (verify + coverage group_tipo_area_term_v1 per 4 sezioni). Pattern lean = 1 wave alla volta su main.*
