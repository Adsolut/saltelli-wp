# PROMPT AGENT — Design Handoff Wave P10 · glossario-legale verify & minimal drift

> **Scope**: verificare `inc/wave3-glossario.php` + `.sl-glossario*` di sections.css vs `design-handoff/glossario-legale/index.jsx`. 1 fix CSS computed-neutral (lede 22px → var(--fs-lede)), altri phantom defer Wave 5 STEP 5.
>
> **Branch**: `feat/design-handoff-glossario-legale`
> **Stima**: 0.5-1h (severity 🟢 LIGHT)
> **Modalità**: lean, no version bump (chore frontend)
> **Sessione**: una sola Claude Code.

---

## CONTESTO

Wave 10/12 sequenza Design Handoff. P1-P5+P7 mergeate, P2/P6 skipped, version 1.3.15.

**Decisioni orchestratore acquisite** (NON rinegoziare):
1. SoT design tokens = `tokens.css` corrente vince (KEEP CURRENT)
2. Glossario: **array PHP hardcoded** (decisione Wave 3, confermata RECOMMENDATION §J). 60 termini + 5 FAQ inline in `inc/wave3-glossario.php`. **NO migrazione CPT** (backlog separato, fuori scope P10).
3. Schema JSON-LD `DefinedTermSet` + `FAQPage` (PHP L484-544) — invariato.
4. **1 fix computed-neutral**: `.sl-glossario__lede` 22px → `var(--fs-lede)` (token esistente)
5. **Altri phantom defer Wave 5 STEP 5**: 17px (.sl-glossario__search/.sl-glossario__def), 13px (counter), 28px (term), 1.55 lh (example), -0.025em ls (cta-h), 1.65 lh (def, bucket C deliberate)

**Pre-flight orchestratore già fatto**:

| Sezione | Selettore | Status |
|---|---|---|
| Hero | `.sl-glossario__hero` | ✓ token-aware (clamp h1, fs-display letter) |
| Sticky nav (search + A-Z) | `.sl-glossario__nav` (sticky z:10) | ✓ |
| Lista termini (dl 30/70) | `.sl-glossario__list` + `__entry` | ⚠️ 1 fix lede |
| FAQ accordion | `.sl-glossario__faq` (.sl-acc__*) | ✓ |
| CTA "non trovi?" | `.sl-glossario__cta` | ✓ |

5 sezioni JSX, tutte mappate 1:1 con PHP. JSX↔PHP confermato (JSX righe 48-172 → PHP righe 321-481).

---

## ⚠️ HARD INVARIANT

1. **Array PHP termini + FAQ**: INVARIATO (60 termini + 5 FAQ, decisione Wave 3 confermata RECOMMENDATION §J).
2. **Schema JSON-LD**: INVARIATO (DefinedTermSet + FAQPage emessi).
3. **Phantom defer Wave 5 STEP 5**: NON tokenize ora 17px/13px/28px/1.55lh/-0.025em — sono scope futuro pool promotion.
4. **`.sl-glossario__def` lh 1.65**: leave deliberate (bucket C deprecated old --lh-body, audit Wave 5 STEP 4 mantenuta esplicita).

---

## PRE-FLIGHT (3 min)

1. Leggi:
   - `CLAUDE.md` (Hard constraints, "Design → Code handoff rule golden")
   - `.claude/knowledge/audits/design-handoff/RECOMMENDATION.md` (§J glossario "stesso sorgente JSX")
   - **JSX source**: `design-handoff/glossario-legale/index.jsx`
   - **WP target**:
     - `wp-content/themes/saltelli/inc/wave3-glossario.php`
     - Blocco `.sl-glossario*` in sections.css (~righe 5590-5811)

2. Verifica stato:
   ```sh
   git fetch origin
   git status
   git log --oneline -3   # atteso HEAD = 34578af (P7 merge)
   git checkout -b feat/design-handoff-glossario-legale
   ```

3. Conferma in chat + prosegui.

---

## PHASE 1 — VERIFY (5 min)

Verifica solo il fix lede (Agent pre-flight ha già confermato tutto il resto):

| Selettore | Property | Current | Target | Fix |
|---|---|---|---|---|
| `.sl-glossario__lede` | font-size | `22px` literal | `var(--fs-lede)` | YES (computed-neutral) |
| `.sl-glossario__def` | line-height | `1.65` | leave deliberate (bucket C) | NO |
| `.sl-glossario__search` | font-size | `17px` | defer Wave 5 STEP 5 | NO |
| `.sl-glossario__term` | font-size | `28px` | defer Wave 5 STEP 5 | NO |
| `.sl-glossario__example` | line-height | `1.55` | defer Wave 5 STEP 5 | NO |
| `.sl-glossario__cta-h` | letter-spacing | `-0.025em` | defer Wave 5 STEP 5 | NO |

**Fix totale: 1 riga CSS**.

---

## PHASE 2 — IMPLEMENT (5 min)

Modifica `.sl-glossario__lede` in sections.css (riga ~5605):

```css
/* === design-handoff glossario-legale P10 === */
.sl-glossario__lede {
  font-size: var(--fs-lede); /* was 22px literal — token-aware, computed-neutral */
}
```

Sync staging:
```sh
rsync -avz wp-content/themes/saltelli/assets/css/ deploy@178.62.207.50:/var/www/saltelli/wp-content/themes/saltelli/assets/css/
ssh deploy@178.62.207.50 "cd /var/www/saltelli && sudo -u www-data wp cache flush --path=/var/www/saltelli"
```

No PHP changes = no OPcache reload.

---

## PHASE 3 — SMOKE TEST (10 min)

### 3.A — Frontend curl
```sh
curl -sI "https://staging.studiolegalesaltelli.it/risorse/glossario-legale/" | head -3
# atteso: HTTP/2 200

curl -s "https://staging.studiolegalesaltelli.it/risorse/glossario-legale/" | grep -cE "sl-glossario__hero|sl-glossario__list|sl-glossario__faq|sl-glossario__cta"
# atteso: count >= 4 (markup 4 sezioni principali)

curl -s "https://staging.studiolegalesaltelli.it/risorse/glossario-legale/" | grep -c "DefinedTerm"
# atteso: count >= 60 (schema JSON-LD per ogni termine)
```

### 3.B — getComputedStyle spot-check
- `.sl-glossario__lede` font-size atteso `22px` (computed da `var(--fs-lede)`)
- `.sl-glossario__def` line-height atteso `1.65` (leave deliberate bucket C)
- 60 termini rendered nella `<dl>`

### 3.C — Admin smoke (lesson Wave 4.7.fix.4)
WP Admin → Pagine → Glossario legale (Page 2710):
- post_content vuoto (handler PHP renderizza tutto)
- Gutenberg attivo (Page 2710 NOT in SALTELLI_SCF_ONLY_PAGES)
- Save senza modifiche → frontend invariato

---

## PHASE 4 — COMMIT + PUSH

```sh
git add -A
git diff --cached --stat

git commit -m "feat(design-handoff): Wave P10 glossario-legale — lede token alignment (computed-neutral)

Wave 10/12 sequenza Design Handoff. Template glossario-legale 1:1 con JSX confermato (JSX righe 48-172 → PHP inc/wave3-glossario.php righe 321-481).

1 fix CSS computed-neutral (sections.css scope /* === design-handoff glossario-legale P10 === */):
- .sl-glossario__lede font-size: 22px literal → var(--fs-lede) [computed-neutral, token esistente]

Altri phantom defer Wave 5 STEP 5 pool promotion:
- .sl-glossario__search, .sl-glossario__def: 17px (candidato --fs-lede-mobile)
- .sl-glossario__counter: 13px (candidato --fs-body-sm)
- .sl-glossario__term: 28px (candidato --fs-h2-floor)
- .sl-glossario__example: lh 1.55 (candidato --lh-prose)
- .sl-glossario__cta-h: ls -0.025em (candidato --ls-h1-tight)
- .sl-glossario__def: lh 1.65 (bucket C deliberate, Wave 5 STEP 4 esplicita)

SCF: zero (Wave 3 decisione: 60 termini + 5 FAQ in array PHP hardcoded, RECOMMENDATION §J confermato).
Schema JSON-LD DefinedTermSet + FAQPage: invariato.

Smoke test:
- Frontend curl: markup 4 sezioni presente, schema 60 DefinedTerm emessi
- Admin: Gutenberg attivo, post_content vuoto, Page 2710 invariata

No version bump (chore frontend computed-neutral, no schema/data change).
Branch: feat/design-handoff-glossario-legale · 1 file changed · 1 riga CSS"

git push origin feat/design-handoff-glossario-legale
```

---

## OUTPUT FINALE in chat

- Tabella PHASE 1 (1 fix + altri defer documentati)
- 1 fix applicato
- Smoke test risultati
- SHA commit pushato
- ETA proposto P11 contatti

---

## HARD RULES

1. **Solo 1 fix lede**: NIENTE altri phantom tokenize ora.
2. **Array PHP glossario INVARIATO**: 60 termini + 5 FAQ in `inc/wave3-glossario.php`.
3. **Schema JSON-LD invariato**.
4. **Token alignment §A**: KEEP CURRENT, mai toccare `:root`.
5. **One-writer-at-a-time**.

---

## DECISIONE AUTONOMA AUTORIZZATA

- Scope marker CSS: `/* === design-handoff glossario-legale P10 === */`
- Se trovi drift inatteso oltre il pre-flight: documenta in chat, NON fixare (defer Wave 5 STEP 5).

---

## TONO

Direct, concrete, zero filler. Stile commit progetto.

---

*Wave P10/12 sequenza Design Handoff. Prossima: P11 contatti (verify + drift + decisione mappa iframe NO + select aree dinamico). Pattern lean = 1 wave alla volta.*
