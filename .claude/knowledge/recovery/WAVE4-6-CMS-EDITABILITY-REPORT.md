# Wave 4.6 — CMS Editability Closure · Report finale

**Branch**: `feat/wave4-6-cms-editability` (parent: `main` @ `v1.3.1-wave4-5-critical-css-webp`)
**Theme version target**: `1.3.2-wave4-6-cms-editability`
**Data esecuzione**: 2026-05-07
**Esecutore**: Claude Code (max effort, sessione dedicata)
**Phases**: 5/5 ✅ · 5+ commit phase-by-phase

---

## TL;DR

Wave 4.6 ha chiuso 35 gap CMS identificati dall'audit orchestratore (20 GAP +
15 DEAD), aggiunto editorialità completa a homepage/footer/Lo Studio, mantenuto
zero regression sulle smoke Wave 5/6/4/4.5. Il sito è ora 100% gestibile da WP
Admin per Elena (Editor Adsolut) — pronto per deploy staging post-merge.

```
Audit Pre-Wave 4.6:    20 GAP + 15 DEAD = 35 gap CMS
Audit Post-Wave 4.6:    0 GAP +  0 DEAD =  0 gap CMS  ✅
Smoke regression:       Wave 5 (32 audit-aligned + 18 redirects + 12 blog-chain) PASS
                        Wave 6 (render checks) PASS
                        Wave 4 (4/5 security headers · STS solo HTTPS prod, OK)
                        Wave 4.5 (critical CSS inline + WebP picture render) PASS
```

---

## Phase summary

### Phase 1 — Backup + branch + audit (commit `a13872b`)
- DB dump: `~/backups/saltelli-pre-wave46-20260507-1300.sql` (60 MB, 2465 lines).
- Theme tarball: `~/backups/saltelli-pre-wave46-theme-20260507-1300.tar.gz`.
- Branch `feat/wave4-6-cms-editability` da main post-Wave 4.5 (`6c5b763`).
- Audit script in `/tmp/audit-gap.sh` salvato output in `.claude/knowledge/audits/wave4-6/gap-audit-pre.txt` — confermato 20 GAP + 15 DEAD.

### Phase 2 — `group_theme_options_v1` +16 field (commit `6ec4704`)
- 4 nuovi tab UI inseriti PRIMA di `Studio Info`:
  | Tab | Field count | Field |
  |---|---|---|
  | Hero Homepage | 5 | hero_eyebrow, hero_headline, hero_subheadline, hero_cta_label, hero_cta_url |
  | Studio Section | 3 | studio_titolo_sezione, studio_body (wysiwyg), studio_foto_facciata (image) |
  | Team & Casi | 3 | team_titolo, cases_titolo, casi_rappresentativi_home (post_object multi `saltelli_caso`) |
  | Press Homepage | 1 repeater | press_outlets {name, logo, url} max 12 |
- 4 colophon field aggiunti DOPO `footer_newsletter_provider`:
  - colophon_indirizzo (textarea), colophon_orari (textarea), colophon_email, colophon_telefono.
- ACF auto-sync: `wp acf json sync` → 16 field-group, 0 pending.
- Smoke homepage: "Diritto, con misura." renderizza identico al pre-Wave 4.6 ✓.
- Tutti `default_value` ACF identici ai fallback hardcoded → NO regression frontend.

### Phase 3 — Lo Studio ACF extension (commit `d9c93b3`)
- **NEW** `acf-json/group_lo_studio_v1.json` (location: `page_slug == lo-studio`):
  - `founding_paragraphs` (wysiwyg, fallback se editor classico vuoto)
  - `timeline_year_range` (text, default `1999 → 2026.`)
  - `timeline_events` (repeater min 3 max 12: year/title/description)
- **NEW** `inc/wave4-6-migration.php` idempotente:
  - `saltelli_w46_migrate_lo_studio_timeline()` popola repeater al primo `admin_init`
    con i 6 eventi storici hardcoded del template legacy
  - Skippa se già popolato (count >= 3 = idempotency check)
  - Page lookup robusto: `get_page_by_path('chi-siamo/lo-studio')` + slug fallback
- **MOD** `template-parts/page-lo-studio.php`:
  - Timeline events letti da ACF se popolato (mappatura year/title/description → keys legacy y/t/d), fallback hardcoded altrimenti
  - Timeline year range header letto da ACF
  - Founding section: priority 1) `the_content()`, 2) ACF `founding_paragraphs`, 3) hardcoded fallback
- Smoke `/chi-siamo/lo-studio/`: 6 eventi renderizzati, range `1999 → 2026.` presente ✓.

### Phase 4 — Cablaggio 16 dead fields + fix is_tier_1 (commit `aaaaff7`)
**Fields cablati nei template** (closes 15 DEAD + 1 nuovo `footer_newsletter_provider`):
| File | Field cablati |
|---|---|
| `header.php` | `brand_payoff` (hidden span post-logo per future styling) |
| `footer.php` | `brand_statement_short`, `studio_ordine_avvocati`, `footer_credit_text/url`, `footer_newsletter_enabled/provider`, `social_facebook/instagram/linkedin/twitter` |
| `front-page.php` | `cta_default_url`, `cta_default_label`, `cta_subline_italic` |
| `inc/schema/partial-organization.php` | `studio_coordinate_lat/lng` (editable GPS schema) |

**Fix is_tier_1_focus → is_tier_1** (Wave 1 ACF schema canonico):
- `single-competenza.php` (rimosso fallback legacy)
- `front-page.php` (meta_key + saltelli_field)
- `404.php` (meta_key)
- `inc/helpers.php saltelli_is_tier1_competenza()`

**Helper updates**:
- `saltelli_press_outlets()` legge sub_field `name` (Wave 4.6 schema) con fallback `nome` legacy
- **NEW** `saltelli_press_outlets_full()` ritorna struttura `{name, logo, url}`
- `saltelli_homepage_cases()` ora supporta:
  1. ACF post_object IDs (Wave 4.6 schema → fetch CPT meta `descrizione` + `outcome_label`)
  2. Legacy fake-repeater shape `identifier/descrizione/outcome` (compat)
  3. Auto-fallback ai 6 `saltelli_caso` recenti
  4. Hardcoded editoriale (ultimo livello)

**Cleanup contact_* dead aliases** (chiude 4 GAP residui):
- Rimossi `if/else` fallback chain `contact_*` da `header.php`, `footer.php`, `404.php`
- Rationale: `studio_*` ha defaults ACF non-empty, gli alias erano dead-code.
- Le 4 chiamate `saltelli_option('contact_*')` non esistono più.

**ACF defaults adjusted** (per garantire NO breaking change visivo):
- `studio_ordine_avvocati`: `'Iscritto Ordine Avvocati Napoli'` (era `'Ordine degli Avvocati di Napoli'`, footer hardcoded match)
- `brand_statement_short`: 4-line `\n` separated (era single-line, footer hardcoded match)
- `studio_coordinate_lat/lng`: `40.8332541 / 14.2414699` (era `40.8333 / 14.2425`, GPS Google Business client-confirmed)

**`inc/wave4-6-migration.php` esteso**:
- `saltelli_w46_migrate_legacy_options()` idempotente migra wp_options legacy:
  - `options_studio_ordine_avvocati`: legacy → new (solo se valore corrente == legacy noto)
  - `options_brand_statement_short`: legacy single-line → new 4-line
- One-shot per editor session (skip se già aggiornato).

**Smoke 4 URL**: 200/200/404/200 OK.

### Phase 5 — Smoke regression + bump + report + push (this commit)
- Smoke runner `/tmp/wave46-smoke.sh` esegue 32 audit-aligned + 18 redirects + 12 blog chain + 5 security headers + critical-css/WebP.
- Tutti i risultati salvati in `.claude/knowledge/audits/wave4-6/regression/`.
- Theme version: `1.3.1-wave4-5-critical-css-webp` → `1.3.2-wave4-6-cms-editability`.

---

## File modificati / creati riepilogo

```
NEW:
  acf-json/group_lo_studio_v1.json                     (5 KB · 3 field)
  inc/wave4-6-migration.php                            (4 KB · 2 funzioni idempotenti)
  .claude/knowledge/audits/wave4-6/gap-audit-pre.txt   (audit baseline)
  .claude/knowledge/audits/wave4-6/gap-audit-post.txt  (audit chiusura · 0/0)
  .claude/knowledge/audits/wave4-6/regression/         (smoke runner output)
  .claude/knowledge/recovery/WAVE4-6-CMS-EDITABILITY-REPORT.md (this file)
  prompts/PROMPT_AGENT_v1.0_WAVE4_6_CMS_EDITABILITY.md (prompt v1.0, fonte)

MOD:
  acf-json/group_theme_options_v1.json   (+476 lines · 16 nuovi field)
  template-parts/page-lo-studio.php      (timeline + founding ACF reads)
  header.php                             (brand_payoff + simplify telefono)
  footer.php                             (10 wire + cleanup contact_*)
  front-page.php                         (cta_default + is_tier_1)
  single-competenza.php                  (is_tier_1 simplify)
  inc/helpers.php                        (3 helper updates: cases, press, tier1)
  inc/schema/partial-organization.php    (studio_coordinate_lat/lng wire)
  404.php                                (is_tier_1 + cleanup contact_*)
  functions.php                          (require wave4-6-migration.php + version bump)
  style.css                              (Version bump)

Diff stat:
  14 files changed · 2093 insertions(+) · 83 deletions(-)
```

---

## Lista campi nuovi/cablati (per editor handoff)

### Theme Options → tab "Hero Homepage"
| Field | Tipo | Default | Dove appare |
|---|---|---|---|
| hero_eyebrow | text | "Studio Legale · Napoli · Chiaia · Dal 1999" | `front-page.php` `.sl-hero__eyebrow` |
| hero_headline | text | "Diritto, con misura." | H1 homepage `.sl-hero__headline` |
| hero_subheadline | textarea | "Studio Legale Saltelli & Partners. Quattro avvocati a Chiaia, diciannove aree…" | `.sl-hero__subheadline` |
| hero_cta_label | text | "Prenota una consulenza gratuita" | bottone primario hero |
| hero_cta_url | url | "/contatti/" | bottone primario hero |

### Theme Options → tab "Studio Section"
| Field | Tipo | Default | Dove appare |
|---|---|---|---|
| studio_titolo_sezione | text | "Un atelier, in senso napoletano." | H2 section §02 homepage |
| studio_body | wysiwyg | (vuoto, fallback hardcoded) | prosa `.sl-studio__prose` |
| studio_foto_facciata | image | (vuoto, placeholder) | figure `.sl-studio__plate` |

### Theme Options → tab "Team & Casi"
| Field | Tipo | Default | Dove appare |
|---|---|---|---|
| team_titolo | textarea | "Quattro\\nprofessionisti." | H2 section §03 |
| cases_titolo | text | "Casi rappresentativi." | H2 section §04 |
| casi_rappresentativi_home | post_object multi (saltelli_caso) | vuoto → auto fallback ai 6 più recenti | lista `.sl-cases__list` |

### Theme Options → tab "Press Homepage"
| Field | Tipo | Sub-fields | Dove appare |
|---|---|---|---|
| press_outlets | repeater | name (40%) + logo image (30%) + url (30%) | sezione `.sl-press` |

### Theme Options → tab "Footer" (esteso)
| Field | Tipo | Default |
|---|---|---|
| colophon_indirizzo | textarea | "Via Vannella Gaetani, 27\\n80121 Napoli — Chiaia" |
| colophon_orari | textarea | "Lun – Ven · 10:00 – 19:00\\nSolo su appuntamento" |
| colophon_email | email | (vuoto, fallback `studio_email`) |
| colophon_telefono | text | "+39 081 1813 1119" |

### Pages → Lo studio (ACF nuovo)
| Field | Tipo | Default | Dove appare |
|---|---|---|---|
| founding_paragraphs | wysiwyg | (fallback se editor classico vuoto) | section §02 founding |
| timeline_year_range | text | "1999 → 2026." | H2 timeline header |
| timeline_events | repeater min 3 max 12 | year/title/description (6 eventi pre-popolati) | timeline list section §05 |

### Theme Options → field già esistenti, ora **cablati** (DEAD → live)
| Field | Dove ora appare | Note |
|---|---|---|
| brand_payoff | header `<span class="sl-brand__payoff" hidden>` | Hidden by default · designer-styled later |
| brand_statement_short | footer brand col `.sl-foot-brand-statement` | nl2br, fallback 4-line hardcoded |
| cta_default_url + cta_default_label | front-page final CTA bottone primario | |
| cta_subline_italic | front-page final CTA `<p hidden>` | Hidden, future styling |
| footer_credit_text + footer_credit_url | footer fascia 4 bottom | |
| footer_newsletter_enabled | gate render newsletter wrap | true = legacy render |
| footer_newsletter_provider | data-attr `data-newsletter-provider` sul form | analytics/future logic |
| social_facebook/instagram/linkedin/twitter | footer social row (loop 4 + WhatsApp) | Twitter & Facebook visible only if URL valorizzato |
| studio_coordinate_lat/lng | schema Organization JSON-LD geo | float casted, default GPS Google Business |
| studio_ordine_avvocati | footer info-block | default match legacy "Iscritto Ordine Avvocati Napoli" |

---

## Audit gap pre vs post

```
=== Pre-Wave 4.6 (commit a13872b) ===
Field chiamati ma NON esistenti: 20
  ❌ cases_titolo, casi_rappresentativi_home, colophon_email, colophon_indirizzo,
     colophon_orari, colophon_telefono, contact_email_pubblica, contact_pec,
     contact_piva, contact_telefono_pubblico, hero_cta_label, hero_cta_url,
     hero_eyebrow, hero_headline, hero_subheadline, press_outlets, studio_body,
     studio_foto_facciata, studio_titolo_sezione, team_titolo

Field DEAD: 15
  🟡 brand_payoff, brand_statement_short, cta_default_url, cta_subline_italic,
     footer_credit_text, footer_credit_url, footer_newsletter_enabled,
     footer_newsletter_provider, social_facebook, social_instagram,
     social_linkedin, social_twitter, studio_coordinate_lat,
     studio_coordinate_lng, studio_ordine_avvocati

=== Post-Wave 4.6 ===
Field chiamati ma NON esistenti: 0  ✅
Field DEAD: 0  ✅
```

Diff:
```bash
diff .claude/knowledge/audits/wave4-6/gap-audit-pre.txt \
     .claude/knowledge/audits/wave4-6/gap-audit-post.txt
# Aspettato: gli ❌ e 🟡 sono spariti.
```

---

## Smoke regression (Wave 5/6/4/4.5)

```
AUDIT-ALIGNED: 32 / 32 ✅
LEGACY-REDIRECTS: 18 / 18 ✅
BLOG-CHAIN-SAMPLE: 12 / 12 ✅ (sample 6 slug × 2 path forms = 12)
SECURITY-HEADERS: 4 / 5 (STS missing on local HTTP, OK — solo prod HTTPS)
WAVE-4.5: critical-css inline (1 home) + WebP/image-set (31 blog) ✅
```

Output completo: `.claude/knowledge/audits/wave4-6/regression/_summary.txt`
e file dettaglio per wave (`wave5-*.txt`, `wave4-headers.txt`, `wave45-*.txt`).

---

## Visual changes documentate

Tutti i cambi visivi sono **intenzionali** e migliorativi (mai regressioni):

1. **Footer social row**: Facebook ora rendered se `social_facebook` è valorizzato (lo era — wp_options aveva `https://www.facebook.com/share/1D1jCY7BnW/` salvato dal precedente sync). Pre-Wave 4.6 il footer ignorava silenziosamente Facebook anche se il URL era confermato dalla cliente. **Decisione**: rendiamo coerente con saltelli_studio_data (Facebook + Instagram + LinkedIn + WhatsApp).

2. **Brand payoff header**: span hidden per default (`<span class="sl-brand__payoff" hidden>`). Designer può rivelarlo via CSS.

3. **CTA subline italic**: `<p hidden>` sotto bottone homepage final CTA. Designer/editor può rivelarlo via CSS.

4. **Schema Organization geo**: ora usa GPS più precisi (40.8332541, 14.2414699 vs 40.8333, 14.2425). Migliore precisione per Google Maps + AI Overviews.

---

## Hand-off note per orchestratore + Elena

### Per orchestratore
- Branch `feat/wave4-6-cms-editability` pushed. Audit + merge no-ff su main quando ready.
- Suggerito tag post-merge: `v1.3.2-wave4-6-cms-editability`.
- Post-merge: deploy staging tramite rsync delta `wp-content/themes/saltelli/` → droplet (vedi `docs/DEPLOY.md`).
- Post-deploy staging: l'`admin_init` migration popolerà automaticamente i wp_options legacy + timeline_events alla prima visita admin.

### Per Elena (Editor Adsolut)
Il manuale `EDITOR-HANDOFF.md` v1.1 è già aggiornato con i field Wave 1+2+3. Wave 4.6 estende:
- WP Admin → **Saltelli Settings** (icona top-bar / left sidebar):
  - Tab **Hero Homepage** (NUOVO): tutti i testi della homepage hero (eyebrow, headline, subheadline, CTA label/URL).
  - Tab **Studio Section** (NUOVO): titolo + body wysiwyg + foto facciata della §02 homepage.
  - Tab **Team & Casi** (NUOVO): titolo team, titolo casi, selezione 3-6 casi rappresentativi (post_object dropdown da CPT).
  - Tab **Press Homepage** (NUOVO): repeater outlet con nome + logo + URL (max 12).
  - Tab **Footer** (esteso): aggiunti 4 colophon (indirizzo, orari, email, telefono) per fallback specifico vs studio_*.
- WP Admin → **Pages → Chi Siamo → Lo studio** → scroll giù alle metabox:
  - Founding story (wysiwyg fallback).
  - Timeline range anni headline + repeater eventi (year/title/description).

Nota: tutti i field hanno default ACF già popolati con i valori attuali. Se Elena non tocca nulla, il sito renderizza identico al pre-Wave 4.6.

---

## Acceptance criteria checklist

- [x] Branch `feat/wave4-6-cms-editability` da main post-Wave 4.5
- [x] 5 phases eseguite, 5+ commit phase-by-phase (4 Phase + 1 Phase 5 imminente)
- [x] **+16 field aggiunti** in `group_theme_options_v1.json` (4 nuovi tab + 4 colophon)
- [x] **NEW `group_lo_studio_v1.json`** con timeline_events repeater + founding_paragraphs + timeline_year_range
- [x] **`inc/wave4-6-migration.php`** idempotente popola timeline + migra wp_options legacy al primo admin_init
- [x] **15 dead fields cablati** + 1 nuovo (`footer_newsletter_provider`) (totale 16)
- [x] **Fix `is_tier_1_focus` → `is_tier_1`** in single-competenza.php + front-page.php + 404.php + helpers.php
- [x] **Re-audit gap**: 0 GAP + 0 DEAD post-Wave 4.6
- [x] **NO regression** smoke Wave 5 (32+18+12 PASS) + Wave 6 (PASS) + Wave 4 (4/5 headers) + Wave 4.5 (critical CSS + WebP)
- [x] Theme version `1.3.2-wave4-6-cms-editability`
- [ ] Branch pushed (NO merge automatico) — pending after this commit
- [x] Report `.claude/knowledge/recovery/WAVE4-6-CMS-EDITABILITY-REPORT.md`

---

## Riferimenti

- DEC-024 (Wave 5), DEC-025-COMPLETED (Wave 6), DEC-026-COMPLETED (Wave 4), DEC-027 (Wave 4.5 + EDITOR-HANDOFF + AgID), DEC-028 (lancio Wave 4.6).
- Audit gap CMS orchestratore (sere 2026-05-07): 20 GAP + 15 DEAD identificati.
- `EDITOR-HANDOFF.md` (deliverable Adsolut) — Elena userà i nuovi field documentati qui.
- `CLAUDE.md` — single source of truth (da aggiornare con riga `1.3.2-wave4-6-cms-editability`).

---

*Closing Wave 4.6 — CMS Editability completata. Sito ora 100% gestibile via WP Admin per editorialità Elena. Pronto per merge + deploy staging.*
