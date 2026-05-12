# PROMPT AGENT — Design Handoff Wave P2 · FOOTER verify & drift cleanup

> **Scope**: allineare `footer.php` + blocco `.sl-foot-*` di sections.css al design source `design-handoff/footer/index.jsx`. Verifica fedeltà visual + drift CSS cleanup. **NESSUN cambio SCF.**
>
> **Branch**: `feat/design-handoff-footer`
> **Stima**: 30-60 min (severity 🟢 LIGHT-MEDIUM, pre-flight orchestratore ha già mappato il drift)
> **Modalità**: lean, no version bump (chore frontend cleanup).
> **Sessione**: una sola Claude Code, no parallelismo.

---

## CONTESTO

Wave 2/12 della sequenza Design Handoff. Wave P1 chrome dovrebbe essere già mergeata su `main` quando lanci questa (oppure puoi partire in parallelo se P1 non ha toccato sections.css blocco `.sl-foot-*`).

**Decisioni orchestratore acquisite** (NON rinegoziare):
1. SoT design tokens = **`tokens.css` corrente vince** (KEEP CURRENT, ignore bundle CSS obsoleto)
2. SCF data contract immutabile (footer legge da SCF Footer + Footer Aree + Studio Info + Social + CTA Defaults — tutti compliant, zero additive)
3. **WP wins per divergenze documentate da changelog** (Duccio review v0.21.x ha già applicato decisioni di prodotto: order fasce, tier-2 drop, margin h2 precta)

**Pre-flight orchestratore già fatto** (Explore agent output):

| Drift type | Count | Note |
|---|---|---|
| 🟢 Match computed-neutral | 18 | OK, no fix |
| 🟡 Layout identity (flex↔grid, BEM nesting) | 4 | Markup divergente ma rendering identico, no fix |
| 🔴 Drift sinceri | 3 | F1 h2 margin, F2 grid col2, F3 input padding |
| 🔴 Removal strutturale | 1 | JSX ha tier-2 list 2-col, WP non più (v0.21.3 drop) |
| Order divergence | 1 | JSX: pre-CTA→main→newsletter→bottom · WP: pre-CTA→**newsletter**→main→bottom (v0.21.6) |

**Pattern**: WP è già allineato a decisioni Duccio post-v0.21.x. JSX è il prototipo "first iteration" che NON ha tenuto traccia delle decisioni successive. **Per ogni divergenza → WP wins** salvo eccezioni esplicite.

---

## ⚠️ HARD INVARIANT — SCF DATA CONTRACT PRESERVATION

Footer ha 8 `saltelli_option()` reads + 1 repeater `footer_tier1_aree`. Tutti mappati a SCF field esistenti (Footer / Footer Aree / Studio Info / Social / CTA Defaults / Brand). **0 additive fields necessari** (Explore agent verified).

**NON ACCETTABILE**: refactor SCF group, cambio location rules, rinominare field, rimuovere field esistenti, cambio CTA Defaults (riusata in 19 file template).

---

## PRE-FLIGHT (5 min)

1. Leggi nell'ordine:
   - `CLAUDE.md` (sezioni: Hard constraints, Design system, "Design → Code handoff rule golden", Lessons learned)
   - `.claude/knowledge/audits/design-handoff/RECOMMENDATION.md` (§A KEEP CURRENT, §B prioritization, §C Risk Analysis, §G Elena Impact)
   - `.claude/knowledge/audits/design-handoff/02-jsx-to-wp-mapping.md` (riga footer)
   - **JSX source**: `design-handoff/footer/index.jsx` (S2Footer, 4 fasce editoriali)
   - **WP target**:
     - `wp-content/themes/saltelli/footer.php` (verifica ordine fasce attuale)
     - Blocco `.sl-foot-*` in `wp-content/themes/saltelli/assets/css/sections.css` (probabilmente righe ~6380-6920 in base al pre-flight)
   - **Token reference**: `wp-content/themes/saltelli/assets/css/tokens.css`
   - **Phantom doc cross-ref**: `.claude/knowledge/audits/wave5-step4-sections-cleanup/02-phantom-values-remaining.md`

2. Verifica stato:
   ```sh
   git fetch origin
   git status                                                  # working tree pulito
   git log --oneline -3                                        # HEAD post-P1 merge atteso
   git checkout -b feat/design-handoff-footer
   ```

3. Conferma in chat: stato repo + branch creato + prosegui VERIFY.

---

## PHASE 1 — VERIFY (10-15 min)

Output obbligatorio in chat: tabella drift consolidata + classifica per riga PRIMA di implementation.

### 1.A — Order fasce (struttura)

Verifica ordine attuale footer.php:
- ✅ Atteso: pre-CTA → Newsletter → Main 4-col → Bottom legal
- JSX dice: pre-CTA → Main → Newsletter → Bottom

**Decisione orchestratore**: **WP wins** (Duccio approved v0.21.6, focus redazionale). JSX è outdated. **NO refactor ordine.**

Documenta in tabella: "Order: WP corretto, JSX outdated, no fix".

### 1.B — Drift sinceri (3 + 1 removal)

Per ognuno dei 3 drift identificati nel pre-flight, verifica computed CSS attuale e decidi fix:

| # | Element | JSX value | WP value attuale | Decision |
|---|---|---|---|---|
| 1 | `.sl-foot-precta h2` margin-bottom | 24px | 20px | **WP wins** (v0.21.21 Duccio review). JSX adapt, no fix WP. |
| 2 | `.sl-foot-main` grid-template-columns | `3fr 4fr 3fr 3fr` (13fr) | `3fr 3fr 3fr 4fr` (13fr) | **WP wins** (post-drop tier-2 v0.21.3). JSX outdated, no fix WP. |
| 3 | `.sl-foot-newsletter form input` padding | `16px 0 12px` | `14px 0 12px` | **VERIFICA con DESIGN.md baseline**. Se DESIGN.md prescrive 16px optical → fix WP a 16px. Altrimenti WP wins. |
| 4 | Tier-2 footer aree (2-col list 16 aree) | presente in JSX | rimosso in WP v0.21.3 | **WP wins** (decision Duccio). NO re-introduce. |

**Per drift #3 input padding**:
- Leggi `docs/DESIGN.md` sezione typography/forms baseline
- Se DESIGN.md ha `16px` come baseline form input → 1-line fix in sections.css
- Se non specificato → WP wins (14px Duccio refinement, no fix)

### 1.C — Match computed-neutral (sample verify)

Per spot-check, scegli 5 dei 18 match documentati dal pre-flight e verifica computed CSS via reading file (NO test browser ora — basta lettura):
- F1 background `var(--surface)` → presente in sections.css? ✓/✗
- F2 col1 logo swash font-size `52px` → presente? ✓/✗
- F2 col2 tier-1 row font-size `14px` → presente? ✓/✗
- F3 grid `5fr 7fr` desktop → presente? ✓/✗
- F4 copy font-size `11px` → presente? ✓/✗

Atteso 5/5 ✓.

### 1.D — Phantom cross-ref

Il pre-flight ha identificato 1 phantom catalogato:
- F2 col3 nav link line-height: JSX `2`, WP `var(--lh-body)` = `1.7` (post Wave 5 STEP 4 normalize). **WP wins**, phantom risolto, no fix.

Verifica nessun ALTRO valore in `.sl-foot-*` di sections.css matcha entry del phantom doc `02-phantom-values-remaining.md`. Se trovi altri match: segnala in tabella (non fixare ora, è scope Wave 5 STEP 4 phantom-resolution successiva).

### 1.E — SCF reads verify

Cross-check 8 `saltelli_option()` calls in footer.php:
- `studio_indirizzo_via`, `studio_cap_citta`, `studio_quartiere`, `studio_telefono_pubblico`, `studio_email`, `studio_pec`, `studio_piva`, `studio_ordine_avvocati` (Studio Info tab)
- `brand_statement_short` (Brand tab)
- `footer_credit_text`, `footer_credit_url` (Footer tab)
- `footer_newsletter_enabled`, `footer_newsletter_provider` (Footer tab)
- `social_*` (Social tab, con fallback `saltelli_studio_data()`)
- `footer_tier1_aree` repeater (Footer Aree tab)

Verifica che ognuna abbia SCF field corrispondente in `acf-json/group_theme_options_v1.json`. Atteso 100% match. Se trovi mismatch (improbabile): FLAGGA, non aggiungere field (out of scope, sarebbe nuovo additive).

### 1.F — Decisione end-of-PHASE-1

Posta tabella drift completa in chat:
- ✅ MATCH (no fix needed)
- ⚠️ DRIFT minor (1-line CSS fix)
- ❌ STRUCTURAL CHANGE (skipped per decisione orchestratore — WP wins)

**Stima fix necessari atteso: 0-1 CSS rule** (solo drift #3 input padding se DESIGN.md baseline 16px).

**Se 0 drift fixabili**: la wave si chiude con commit "no drift to fix, footer già allineato a decisioni Duccio v0.21.x".

---

## PHASE 2 — IMPLEMENT (5-15 min, solo se PHASE 1 trova drift fixabili)

Per ogni DRIFT minor:

1. **Token alignment** (decisione §A KEEP CURRENT):
   - Se valore matcha token → `var(--token)`
   - Se phantom catalogato → segui piano `02-phantom-values-remaining.md`
   - Se nuovo → per-selector, MAI toccare `:root`

2. **BEM className mapping** (golden rule):
   - Probabilmente non serve nuovo className (footer è già ben BEM-strutturato)
   - Aggiunte CSS rule nello scope:
     - `assets/css/sections.css` blocco `.sl-foot-*` con scope marker `/* === design-handoff footer === */`

3. **NO PHP refactor** (footer.php non va toccato — order/struttura WP wins per decisioni Duccio v0.21.x)

4. **Sync staging**:
   ```sh
   rsync -avz wp-content/themes/saltelli/assets/css/ deploy@178.62.207.50:/var/www/saltelli/wp-content/themes/saltelli/assets/css/
   ssh deploy@178.62.207.50 "cd /var/www/saltelli && sudo -u www-data wp cache flush --path=/var/www/saltelli"
   ```
   (No PHP changes = no OPcache reload necessario, ma se per qualsiasi motivo tocchi footer.php, allora SI: `sudo systemctl reload php8.2-fpm`)

---

## PHASE 3 — SMOKE TEST (5-10 min)

### 3.A — Frontend curl smoke (3 URL, footer è globale)

```sh
for URL in / /chi-siamo/ /aree-di-pratica/privati/diritto-di-famiglia-lgbtq/; do
  echo "=== $URL ==="
  curl -s "https://staging.studiolegalesaltelli.it$URL" | grep -cE 'sl-foot-precta|sl-foot-main|sl-foot-newsletter|sl-foot-bottom'
  # atteso: count = 4 (tutte le 4 fasce presenti su ogni pagina)
done
```

### 3.B — getComputedStyle spot-check (opzionale, solo se hai fixato qualcosa)

Se hai fixato il drift #3 input padding:
- `.sl-foot-newsletter form input` padding → atteso `16px 0px 12px 0px`

Altri 5 selettori spot-check verifica (no diff atteso):
- `.sl-foot-precta` background
- `.sl-foot-main` grid-template-columns
- `.sl-foot-bottom .sl-mono` font-size

### 3.C — Admin-side smoke (lesson Wave 4.7.fix.4)

Apri WP Admin → Saltelli Settings:
- Tab "Footer": tutti i field popolati visibili
- Tab "Footer Aree": repeater `tier1_aree` con 3 voci popolate
- Tab "Studio Info": telefono/email/indirizzo intatti
- Tab "Social": social handles intatti
- Tab "CTA Defaults": cta_default_* intatti
- Tab "Brand": brand_statement_short intatto

Atteso 100% intatto (footer.php non è stato toccato).

---

## PHASE 4 — COMMIT + PUSH

Se 0 fix:
```sh
git checkout main  # niente da committare, branch cleanup
git branch -d feat/design-handoff-footer
echo "Wave P2 footer: no drift to fix, già allineato a decisioni Duccio v0.21.x. Skipped."
```

Se fix applicati:
```sh
git add -A
git diff --cached --stat

git commit -m "feat(design-handoff): Wave P2 footer — verify + minor drift cleanup

Wave 2/12 della sequenza Design Handoff. Verify completo .sl-foot-*
blocco sections.css vs design-handoff/footer/index.jsx.

VERIFY summary (pre-flight orchestratore + Code verify):
- 🟢 18 match computed-neutral
- 🟡 4 markup/layout identity (flex↔grid, BEM nesting) — rendering identico, no fix
- 🔴 3 drift sinceri — WP wins per 2 (v0.21.21 margin, v0.21.3 grid), 1 fix applicato
- 🔴 1 removal strutturale tier-2 (v0.21.3 Duccio drop) — WP wins
- Order fasce: WP wins (v0.21.6 newsletter-first)

Decisione orchestratore applicata: KEEP CURRENT tokens.css, WP wins per
divergenze documentate in changelog v0.21.x. JSX outdated rispetto a
review Duccio successive.

Fix applicati: <descrivere drift #3 input padding 14→16 SE DESIGN.md baseline 16px, oppure 'nessuno'>

CSS changes:
- assets/css/sections.css: <N> CSS rule modified, scope /* === design-handoff footer === */

PHP changes: nessuno (footer.php invariato — order/struttura WP corretti).

SCF: 🟢 100% compliant. 8 saltelli_option reads tutti mappati a Footer/Footer Aree/Studio Info/Social/CTA Defaults. Zero additive.

Smoke test:
- Frontend curl 3 URL: footer 4-fasce markup presente su ogni pagina
- Admin-side: 6 tab Saltelli Settings intatti (Footer, Footer Aree, Studio Info, Social, CTA Defaults, Brand)

No version bump (chore frontend, no schema/data change).
Branch: feat/design-handoff-footer · <N> file changed · +XX/-YY"

git push origin feat/design-handoff-footer
```

---

## OUTPUT FINALE in chat

- Tabella drift PHASE 1 (consolidata, max 15 righe)
- Fix applicati count (atteso 0-1)
- Smoke test risultati
- SHA commit pushato (se fix) o "no fix needed, branch deleted"
- ETA proposto P3 home (hero variant B + Picsum placeholder)

---

## HARD RULES

1. **Order fasce footer WP wins** (v0.21.6 Duccio approved). NO refactor ordine.
2. **Tier-2 footer drop WP wins** (v0.21.3 Duccio drop). NO re-introduce.
3. **Margin h2 precta WP wins** (v0.21.21 Duccio review). NO sync a JSX.
4. **Token alignment §A**: KEEP CURRENT tokens.css, mai toccare `:root`.
5. **SCF immutabile**: 0 additive previsti, 0 refactor schema.
6. **OPcache reload** SE per qualsiasi motivo tocchi footer.php (lesson Wave 4.7.fix.3) — atteso: nessun touch a footer.php.
7. **Admin-side smoke test obbligatorio** (lesson Wave 4.7.fix.4).
8. **One-writer-at-a-time**: questa è UNICA sessione Code attiva.

---

## DECISIONE AUTONOMA AUTORIZZATA

- Verifica DESIGN.md baseline form input padding (drift #3). Se 16px → fix WP. Se non specificato → WP wins, no fix.
- Skip PHASE 2 se 0 drift fixabili.
- Wording scope marker CSS: `/* === design-handoff footer === */`.
- Eventuale phantom collateral catch (oltre l'1 già noto): segnala, non fixare (fuori scope, Wave 5 STEP 4 follow-up).

---

## TONO

Direct, concrete, zero filler. Stile commit usato dal progetto.

---

*Wave P2/12 sequenza Design Handoff. Prossima: P3 home (hero variant B + Picsum + SCF hero_image image + responsive `<picture>`). Pattern lean = 1 wave alla volta su main, audit orchestratore post-push.*
