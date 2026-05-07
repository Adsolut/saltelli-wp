# 🔧 WAVE 4 — CALIBRATION NOTES (read FIRST before the v1.0 prompt)

> **Audience**: Claude Code agent dedicato Wave 4 (Production Readiness — WOFF2 self-host + SRI + Critical CSS + Lighthouse ≥ 92).
> **Funzione**: calibra il prompt esistente `PROMPT_AGENT_v1.0_WAVE4_PRODUCTION_READINESS.md` (1072 righe, scritto pre-Wave5/6) rispetto a:
> 1. La realtà del codice MVP confermata da Wave 5 + Wave 6 in produzione (tag `v1.2.0-wave6-geo-cro-blocks`)
> 2. Le decisioni post-Wave 5/6 (DEC-024, DEC-025-COMPLETED)
> 3. Le 5 lessons learned cristallizzate in DEC-024 + 5 in DEC-025-COMPLETED
> **Origine**: lettura puntuale codice MVP post-Wave6 + report wave + audit findings.
> **Stato attuale**: ✅ POPOLATA — pronta per Wave 4 launch (sere 2026-05-06).

---

## 🎯 Cosa fare con questo file

1. Leggi prima `CLAUDE.md`.
2. **Leggi questo file** prima del prompt Wave 4.
3. Le calibrazioni qui hanno la **precedenza** sul prompt v1.0 dove c'è conflitto (probabile: il prompt v1.0 è stato scritto pre-Wave 5/6, quindi assume stato MVP precedente).
4. Tieni questo file e `mvp-state-snapshot.md` v3 a portata durante l'esecuzione.

---

## 📍 CAL-W4-01 — Theme version baseline aggiornata

Il prompt v1.0 Wave 4 fu scritto assumendo theme version `1.0.0-recovery-wave3-debug`. **Realtà attuale**: `1.2.0-wave6-geo-cro-blocks`.

**Action**: ovunque nel prompt Wave 4 vedi reference a "1.0.0" o "Wave 3 baseline", interpretalo come "1.2.0-wave6-geo-cro-blocks" baseline. Theme version target **dopo Wave 4**: `1.3.0-wave4-production-readiness` (decide orchestratore al lancio).

---

## 📍 CAL-W4-02 — File CSS bundle aggiornati post-Wave 6

Il prompt v1.0 Wave 4 elenca i CSS bundle da processare per Critical CSS extraction. Post-Wave 6 c'è un **CSS bundle nuovo da includere**:

```
wp-content/themes/saltelli/assets/css/
├── tokens.css                          (DS originale, design tokens locked)
├── components.css                      (atoms + molecules)
├── sections.css                        (hub blocks Wave 5 inclusi, ~141 righe Wave 5)
└── components/
    └── cro.css                         (NEW Wave 6, 453 righe, 10 pattern adaptation)
```

**Action**: il critical CSS extraction Wave 4 deve **includere `cro.css`** nel calcolo critical (specialmente i selettori per trust-bar, mobile-sticky-bar, mini-form, related-services che sono above-the-fold su molte pages).

---

## 📍 CAL-W4-03 — URL pattern smoke test post-Wave 5

Il prompt v1.0 Wave 4 fu scritto assumendo URL pattern legacy o pre-Wave5. **Realtà attuale**: pattern audit-aligned post-Wave 5:
- `/aree-di-pratica/{cluster}/{slug-reale}/`
- `/chi-siamo/team/{slug}/`
- `/risorse/blog/{slug}/`

**Action**: il smoke test Wave 4 deve usare gli URL audit-aligned. Per riferimento, vedi `migration-matrix-v3.csv` (slug REALI delle 17 competenze) e `WAVE5-IA-REFACTOR-REPORT.md` (32 URL audit-aligned).

Lighthouse target URL principali per Wave 4 (campione rappresentativo):
- `/` (homepage, max content above-the-fold)
- `/aree-di-pratica/privati/diritto-tributario/` (Tier-1 deep, max struttura)
- `/aree-di-pratica/privati/cartelle-esattoriali-e-multe/` (Tier-2, validate post Wave 6 FAQ fix)
- `/chi-siamo/team/antonia-battista/` (single-avvocato, asset principale)
- `/risorse/blog/{recent-slug}/` (single blog post, validate Wave 6 author byline ricca)
- `/contatti/` (form completo + GDPR)

---

## 📍 CAL-W4-04 — `wave5-blog-rewrites.php` filter request priority 5 NON tocca

Il file `inc/seo/wave5-blog-rewrites.php` contiene il pattern critico:
```php
add_filter('request', 'saltelli_resolve_blog_post_request', 5);
```

**Action**: Wave 4 (Production Readiness) **NON deve modificare** questo file. È critical per i 326 blog post historical accessibili via `/risorse/blog/{slug}/`.

---

## 📍 CAL-W4-05 — Lighthouse target ≥ 92 mobile + desktop, baseline da Wave 5

Pre-Wave5 baseline esiste: `.claude/knowledge/audits/wave5/lh-*.html` (storico).

**Realtà**: Wave 5 e Wave 6 hanno aggiunto codice CSS + JS senza performance ottimizzazione. È plausibile che Lighthouse mobile sia sotto 92 prima di Wave 4. Il purpose di Wave 4 è proprio portarlo a target.

**Action**:
1. Esegui Lighthouse PRE-Wave 4 (subito dopo branch creation) per misurare baseline corrente
2. Esegui Lighthouse POST-Wave 4 per misurare delta
3. Target: ≥ 92 mobile + desktop su 6 URL campione (vedi CAL-W4-03)
4. Se delta < 5 punti, valutare se serve seconda iterazione (decide orchestratore)

---

## 📍 CAL-W4-06 — Decisione re-enable Lenis smooth scroll

Wave 5/6 ha mantenuto Lenis disabled (DEC-018, drift accettato vs DS originale Sessione 1+2). 

**Decisione orchestratore PER WAVE 4**:
- Opzione A: **mantenere Lenis disabled** (comportamento smooth-scroll nativo CSS `html { scroll-behavior: smooth }` con `prefers-reduced-motion` opt-out già attivo)
- Opzione B: **abilitare Lenis** per smooth scroll più fluido (Web Animations API style) — costa ~9KB JS

Default: **Opzione A** per non aggiungere JS bundle in Wave 4 (purpose performance). Decisione finale orchestratore + cliente al lancio Wave 4.

---

## 📍 CAL-W4-07 — Brevo SMTP transactional + WP Mail SMTP plugin

Il prompt v1.0 Wave 4 potrebbe assumere Brevo già configurato. **Realtà**: Brevo SMTP relay credentials sono ancora pending da cliente (vedi Quality Checklist Fase 1+2 sign-off open items).

**Action**: Wave 4 **NON dipende** da Brevo (Brevo è transactional email, non performance). Se prompt v1.0 menziona Brevo nel scope Wave 4, ignorare — è scope Wave 7 (cut produzione).

---

## 📍 CAL-W4-08 — Acceptance gate post-Wave 4

Per ogni acceptance criterion del prompt v1.0 Wave 4, aggiungi i seguenti gate **NUOVI** post-Wave 5/6:

1. **NO regression smoke Wave 5**: 32/32 audit-aligned + 18/18 redirect legacy + 33/33 blog redirect chain ancora PASS
2. **NO regression smoke Wave 6**: 21/21 URL audit-aligned + render checks (trust-bar visibile, mobile-bar conditional, FAQPage Tier-2 schema emesso)
3. **No nuovi JS bundle inutili**: Wave 4 dovrebbe ridurre o mantenere il JS footprint, NON aumentarlo (a meno di Lenis re-enable, vedi CAL-W4-06)
4. **CSP headers compatibili**: SRI fallback non deve rompere `cro.css` enqueued con dipendenza `saltelli-components`
5. **Critical CSS NON deve duplicare** i tokens già nel `<head>` inline da `tokens.css`

---

## ✅ Acceptance check post-calibrazione (per Claude Code Wave 4)

Prima di iniziare Phase 1 del prompt v1.0 Wave 4, conferma a te stesso:

- [ ] Ho letto questo file (CAL-W4-01 → CAL-W4-08)
- [ ] Theme version baseline corretta: `1.2.0-wave6-geo-cro-blocks` (NO `1.0.0`)
- [ ] CSS bundle includono `cro.css` (Wave 6 nuovo)
- [ ] URL smoke test usano pattern audit-aligned post-Wave 5
- [ ] NON modifico `wave5-blog-rewrites.php` (CRITICAL)
- [ ] Lighthouse baseline misurata pre-Wave 4
- [ ] Decisione Lenis re-enable acquisita (default: NO, mantieni disabled)
- [ ] Brevo NON è scope Wave 4 (lo è Wave 7)
- [ ] Acceptance gate include NO regression Wave 5 + Wave 6

---

## 📌 Lessons learned cristallizzate da Wave 5 + Wave 6 (10 totali)

### Da Wave 5 (DEC-024)
1. Acceptance gate `wp rewrite list | grep <pattern>` dopo `add_rewrite_rule + flush`
2. Filter `request` priority < 10 per URL pattern collision con page hierarchy
3. WP `redirect_guess_404_permalink()` come safety net post-DELETE
4. ACF relationships non si auto-orfanizzano dopo DELETE — pulire manualmente
5. Validazione report contro artifact (mismatch dichiarazione vs realtà)

### Da Wave 6 (DEC-025-COMPLETED)
6. Bug fix architettonici scoperti durante extension wave: cristallizzare come opportunità inaspettate
7. Pattern di lavoro DRY: **leggere il codice esistente** prima di estendere (3 elementi pre-esistenti riusati)
8. Render checks artifact da rieseguire post-fix per coerenza reporting
9. Default editoriali ACF Theme Options con valori coerenti DEC (es. trust signal "17 AREE")
10. Graceful fallback strategy: ogni nuovo template-part con return precoce se empty

### Applicabili a Wave 4
Tutti i 10 punti sono rilevanti, particolarmente:
- **Punto 5**: per Wave 4 valida i Lighthouse score reali contro i numeri dichiarati nel report
- **Punto 7**: leggi il codice CSS esistente PRIMA di Critical CSS extraction (alcuni selettori potrebbero essere già ottimizzati)
- **Punto 10**: ogni modifica Wave 4 deve essere graceful (es. WOFF2 self-host con fallback a Google Fonts CDN se file mancante)

---

## 🔗 Riferimenti

- `prompts/PROMPT_AGENT_v1.0_WAVE4_PRODUCTION_READINESS.md` — il prompt principale (1072 righe, da revisionare alla luce di queste calibrazioni)
- `mvp-state-snapshot.md` v3 — stato MVP post-Wave 6
- `WAVE5_CALIBRATION_NOTES.md` — riferimento storico Wave 5
- `WAVE6_CALIBRATION_NOTES.md` — riferimento storico Wave 6
- `migration-matrix-v3.csv` — slug REALI 17 aree
- `CLAUDE.md` — single source of truth
- DEC-018/019/020/024/025-COMPLETED — decisioni in vigore

---

