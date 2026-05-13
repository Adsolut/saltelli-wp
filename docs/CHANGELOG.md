# CHANGELOG — Studio Legale Saltelli WordPress Theme

> Storia completa wave + versioning + prompt archive. Spostato da `CLAUDE.md` 2026-05-13 per performance.
> Per stato corrente vedi `CLAUDE.md` §Current state.

## Wave history

| Phase | Version | Status |
|---|---|---|
| Scaffolding → Multi-agent → Polish → Impeccable | 0.1.0 → 0.4.0 | ✅ |
| Content Migration + Audit Alignment + Pain Points | 0.5.0 → 0.7.0 | ✅ |
| Template Polish + Mobile Fix | 0.8.0 | ✅ |
| Pre-presentation polish (homepage, hero, eyebrow, atelier, ToV "tu") | 0.16.0 → 0.16.3 | ✅ |
| Logo system v1.1 + sitemap audit + favicon fix + home fix | 0.17.0 → 0.17.1 | ✅ |
| Contatti rework + rhythm + sede no-iframe + pills + wp_site_icon unhook | 0.17.2 → 0.17.3 | ✅ |
| Version consolidation + numbering policy | 0.17.4-beta-consolidation | ✅ |
| Wave 0 — Foundation CMS (ACF Free + 8 CPT fake repeater) | 1.0.0-recovery-wave0 | ✅ |
| Wave 1 — 16/16 ACF Field Groups | 1.0.0-recovery-wave1 | ✅ |
| Wave 2 — Content Migration (273 fields + 63 CPT items) | 1.0.0-recovery-wave2 | ✅ |
| Wave 3 — Template Refactor (page.php 1274→79 + 6 template-parts + ACF reads) | 1.0.0-recovery-wave3 | ✅ |
| EDITOR-HANDOFF v1.0 + v1.1 | docs · `60cea61` | ✅ |
| Debug & QA — stress test pre-production (4 bugs, 1 P0 fix `page_slug ==`) | 1.0.0-recovery-wave3-debug | ✅ |
| Wave 4–4.7.1 (font WOFF2, critical CSS, CMS editability, ACF default_value hotfix) | 1.3.0–1.3.4 | ✅ |
| Wave 4.8 — Cleanup + Migrations + UX Polish FINAL | 1.3.5-wave4-8-cleanup-final | ✅ |
| Wave 4.7.fix — SCF Migration + Theme Options Activation (50/50 fields seedabili popolati) | 1.3.6-wave4-7-fix-scf-migration | ✅ |
| Wave 4.7.fix.1 — SCF URL Validation Fix (CTA interni type:url→text) | 1.3.7-wave4-7-fix-1-scf-url-validation | ✅ |
| Wave 4.7.fix.2 — TRUE FIX (studio_body editorial JSON + menu primary slug-based + 14 redirect 301 + SCF tier-2 60→93 fields, 13 tabs + EDITOR-HANDOFF v3.0 + slug `risultati`→`casi-rappresentativi`) | 1.3.8-wave4-7-fix-2-true-fix | ✅ |
| Wave 4.7.fix.3 — PAGE METABOX MIGRATION (30 SCF field da Theme Options → Page metabox 4 Page WP. Theme Options 13/14→9 tab. Helper `saltelli_page_field()`. EDITOR-HANDOFF v4.0) | 1.3.9-wave4-7-fix-3-page-metabox | ✅ |
| Wave 4.7.fix.4 — STRATEGY A FULL SCF MIGRATION (Gutenberg disabled 12 Page WP target. 6/7 post_content zombie + 1/7 live. Admin shortcuts. EDITOR-HANDOFF v5.0) | 1.3.10-wave4-7-fix-4-strategy-a-full-scf | ✅ |
| Wave 4.7.fix.5 — PAGES CLEANUP + BLOG DOC + CUSTOMIZER LOCK (35→19 Pages. Customizer lock-down editor. Incident OOM `is_super_admin` risolto. EDITOR-HANDOFF v6.0) | 1.3.11-wave4-7-fix-5-cleanup | ✅ |
| Wave 5 STEP 1 — Pages Completeness Audit (read-only, 16 deliverable + decision matrix) | audit/wave5-pages-completeness | ✅ |
| Wave 5 STEP 2 — Design Realignment (tokens.css rebuilt da docs/DESIGN.md SoT: 12 token disallineati + 4 mancanti corretti, letter-spacing + line-height ottici, top-15 violazioni hardcoded fixed. ~590 hardcoded residui deferred) | feat/wave5-design-realign | ✅ |
| Wave 5 STEP 3 — Pages SCF expansion (7 Pages alto-traffico, 107 field text/textarea conservative pattern Elena-approved. Default_value byte-per-byte = hardcoded. Image/repeater backlog Wave 5.1) | 1.3.12-wave5-step3-pages-scf | ✅ |
| Wave 5 STEP 3 coverage — Elena gap (2 archive CPT tab Archive Headers · 3 term tipo-area group_tipo_area_term_v1 23 field per-term · Page Prenota appuntamento + Gutenberg disabled 13 IDs) | 1.3.13-wave5-step3-coverage | ✅ |
| Chore fix single-competenza frontend regression (helper `saltelli_aree_hub_url()` via `get_page_by_path` post Wave 5 IA `has_archive=>false`. Swap 4 call sites) | chore | ✅ |
| Chore pre-cut polish (404.php count aree dinamico + breadcrumb cluster + docs/DEPLOY.md §2 rsync) | chore | ✅ |
| Chore fix single-competenza duplicate body (template "uno o l'altro" intro/post_content vs body/body_extended SCF) | chore | ✅ |
| Chore fix single-competenza tier-1 clusters duplicate body_extended | chore | ✅ |
| Acceptance test editoriale Elena | OK DEFINITIVO 2026-05-11 | ✅ |
| Wave 5 STEP 4 — sections.css drift cleanup (328 hardcoded typography → var(--token) exact match, ZERO cambio computed. font-size 129 swaps + letter-spacing 91 + line-height 108. ~460 phantom residui catalogati in `audits/wave5-step4-sections-cleanup/02-phantom-values-remaining.md`) | chore | ✅ |
| DESIGN HANDOFF AUDIT — RECOMMENDATION strategy (audit branch `audit/design-handoff-strategy`, 4 deliverable + RECOMMENDATION 402 righe. SoT: KEEP CURRENT tokens.css. 15 JSX: 12 COMPLIANT + 1 ADDITIVE + 1 RE-INTERPRETATION blocked + 1 n/a) | audit/design-handoff-strategy | ✅ |
| Design Handoff P1 chrome (header verify, 9 drift, 5 fix + 2 defer + 2 kept) | chore | ✅ |
| Design Handoff P2 footer SKIPPED (0 drift fixabili) | skip | ✅ |
| Design Handoff P3 home — hero variant B + 3 SCF additive (cream scrim asimmetrico 90deg, .sl-hero overflow hidden + isolation, filter grayscale(0.7) contrast(1.05), photo-credit mono 10px. SCF: hero_image image return id + hero_image_credit + hero_image_alt) | 1.3.14-wave5-design-handoff-p3-home | ✅ |
| Design Handoff P4 single-competenza-tier1 (H1 display-band clamp(72,10vw,160) + var(--ls-display) + lh 0.95. Answer capsule margin-left 20% @≥1024. Avvocato lead photo 1/1. 5 fix CSS) | chore | ✅ |
| Design Handoff P5 single-avvocato (1 fix CSS spec pill padding. Template 95% allineato. foto_ritratto `_thumbnail_id=2683` + bio_estesa HARD-PROTECTED) | chore | ✅ |
| Design Handoff P6 taxonomy-tipo-area SKIPPED (0 drift) | skip | ✅ |
| Design Handoff P7 chi-siamo = lo-studio CONSOLIDAMENTO Opzione A (Page 2811 lo-studio rinominato slug → chi-siamo + Page 2822 hub legacy DELETED + redirect 301 /lo-studio/ + /chi-siamo/lo-studio/ → /chi-siamo/. group_lo_studio_v1 location page_slug→chi-siamo. group_chi_siamo_v1.json DELETED. 34 field Elena content preserved. SALTELLI_SCF_ONLY_PAGES 13→12) | 1.3.15-wave-design-handoff-p7-chi-siamo-lo-studio-consolidamento | ✅ |
| Design Handoff P8 blog-archive (1 fix CSS card-title letter-spacing → var(--ls-h1). QA pagination 1-37 OK, /page/50/ → 404) | chore | ✅ |
| Design Handoff P9 archive-casi — pull-quote ADDITIVE + filtri JS (+4 SCF in tab Archive Headers: archive_caso_simbolo_eyebrow/number/quote/attr. Filtri tabs hardcode 5 + JS vanilla client-side `assets/js/archive-casi-filter.js`. data-category=term_slug. wp_enqueue_script conditional) | 1.3.16-wave-design-handoff-p9-archive-casi | ✅ |
| Design Handoff P10 glossario-legale (1 fix CSS lede 22px → var(--fs-lede). Schema JSON-LD DefinedTermSet + FAQPage invariato) | chore | ✅ |
| Design Handoff P11 contatti SKIP orchestrator decision (Elena-approved, backlog post-cut) | skip-backlog | ⏸ |
| Design Handoff P12 404 (FINAL 12/12, 2 fix CSS computed-neutral: lede-prose 22px → var(--fs-lede) + article-title 22px → var(--fs-h3-floor)) | chore | ✅ |
| Wave Elena Feedback Batch 1 — 13 fix Q+S multi-agentic parallel (vedi §Elena Feedback Batches sotto) | 1.3.17-wave-elena-fb-batch-1 | ✅ |
| Wave Elena Feedback Batch 2 — 3 fix M+H+C multi-agentic parallel (vedi §Elena Feedback Batches sotto) | 1.3.18-wave-elena-fb-batch-2 | ✅ |
| Wave Elena Feedback Batch 3 — 1 fix J template alignment (vedi §Elena Feedback Batches sotto) | 1.3.19-wave-elena-fb-batch-3 | ✅ |
| Wave 6.0 partial — CPT competenza content migration + 2 incident WSOD + sanitize chirurgico (vedi §Wave 6.0 partial sotto) | 1.3.24-wave-6-0-partial-stabilized | ✅ |
| Cut produzione (DNS switch staging→prod) | 1.0.0 | ⏸ |

## Elena Feedback Batches (dettaglio)

### Batch 1 (v1.3.17) — 13 fix Q+S multi-agentic parallel
~60 min orchestrator + 2 Code agent parallel su branch DISGIUNTI `feat/elena-fb-wave-{q,s}` + 1 Explore agent verify.

**Wave Q (9 file +120/-42):**
- #3 home hero "Scorri" eyebrow rimosso
- #4 home areas filtri "Tutti/Altri" rimossi (3 cluster only, JS apply initial filter + orphan fallback `default_filter_slug`)
- #5 area row clickable wrap (CSS defensive `text-decoration:none + color:inherit` su `<a>` wrapping)
- #6 hover-D consistency TUTTE voci first-letter idle inherit + hover/focus `var(--accent)` (rimosso color:accent permanente su tier-1, distinzione tier-1 resta su `.sl-area__num` oro)
- #14 casi archive `pre_get_posts ppp 24` (no paginazione)
- #18 badge "TIER 1 · APPROFONDIMENTO" → "Approfondimento" via helper `saltelli_tier_badge_label()` in `helpers.php` (front-page.php + archive-competenza.php + single-avvocato.php; single-competenza.php hero eyebrow DEFERRED)
- #20 FAQ HTML literal `esc_html` → `wp_kses_post` in template-parts/page-faq.php
- #21 FAQ accordion single-open behavior con aria-expanded sync

**Wave S (4 file +51/-79):**
- #9 page-aree-di-pratica-hub.php section `.sl-hub-cta` "Scrivici nota 24h" rimossa (4 SCF orphan `hub_aree_cta_*`)
- #11 page-lo-studio.php Plate I facciata `.sl-chi-siamo__plate` moved to banner #2 (subito sotto hero, prima del lede §01; hardcoded — backlog Wave 5.1)
- #12 page-lo-studio.php sezione `.sl-chi-siamo__founding` "§ 02 — 1999" rimossa (SCF `lo_studio_founding_*` orphan; anno 1999 preservato in timeline §05)
- #16 taxonomy-tipo-area.php sezione `.sl-tipoarea__cta` "§ 04 — Primo incontro" rimossa per 3 term pages (6 SCF `tipo_area_term_cta_*` orphan; Ultima chiamata resta da footer.php:107 pre-footer globale)
- #17 sections.css rule `.sl-tipoarea__areas-list .sl-area--tier1 .sl-area__title::before { content: "★ "; }` rimossa (effetto first-letter bronze già globale via components.css:271)

**Race condition mid-task working tree gestita autonomamente via stash sequence** — lesson learned: per parallel multi-agentic Code execution su file disgiunti, ogni agent deve fare `git checkout -b` IMMEDIATAMENTE e `git stash push -m "..."` se rileva working tree con modifiche di altro agent.

### Batch 2 (v1.3.18) — 3 fix M+H+C multi-agentic parallel
~7 min orchestrator + 3 Code agent parallel su branch DISGIUNTI `feat/elena-fb-wave-{m,h,c}`. Zero overlap hunk sections.css verificato pre-merge (linee 1015-1184 Wave M · 4133-7341 Wave H · 9840-10135 Wave C).

**Wave M (3 file +279/-14) — menu mobile/tablet:**
- #2 menu mobile/tablet submenu cliccabile + back drawer
- header.php drawer mobile `wp_nav_menu` depth 1→2 (markup `<ul class="sub-menu">` sotto voci `menu-item-has-children`)
- main.js handler accordion `.menu-item-has-children > a` preventDefault @viewport <1024px + toggle `.is-open` con `aria-expanded`/`aria-haspopup` sync
- back button "Chiudi" + X SVG in `.sl-header__mobile-bar` sticky top
- ESC key handler con guard `dataset.slMenuEscBound` (idempotenza inline+main.js)
- click outside via backdrop `.sl-header__mobile-backdrop` semi-trasparente navy 32%
- ARIA `role="dialog" aria-modal="true"` su drawer
- dataset flag `slMenuBound`/`slSubmenuBound`/`slMenuEscBound`
- sections.css scope `.sl-header__mobile-bar` + submenu accordion `max-height: 0→600px` transition 280ms, chevron rotate, indent 24px, dashed dotted border separators, body lock `html.sl-menu-open { overflow: hidden }`, mobile full-width, tablet (768-1023) drawer da destra `width: min(480px, 100vw)`, desktop (≥1024) drawer + backdrop `display: none !important`
- Decisioni autonomous: multi-open accordion · close button "Chiudi" + icona X · backdrop navy 32%

**Wave H (2 file +116/-22) — archive-saltelli_caso hero:**
- #13 hero pattern uniformato a archive-avvocato Team
- Markup hero ricostruito 2-col: div left (breadcrumb + eyebrow + h1 + lede) + aside `.sl-archive-casi__trust` capsule destra con eyebrow mono `§ Anonimizzati` + headline dinamica `wp_count_posts('saltelli_caso')->publish` casi anonimizzati
- Class scope nuovo `.sl-archive-casi__hero` parallelo (no collision con design-handoff P9)
- sections.css: padding-block clamp(96,10vw,120) clamp(48,6vw,80), grid 8fr/4fr gap 96px @≥1024, H1 clamp(64,8vw,132) line-height 0.95 ls `--ls-display`, trust capsule `background: var(--surface)` padding 32 `border: 1px var(--border)`, split-reveal animation parity selector `.sl-archive-casi__h1`
- Hardcoded trust content con TODO comment Wave 5.1 SCF additive

**Wave C (2 file +412/-85) — single-competenza layout unify:**
- #23 template-only (NO data migration, defer Wave 6.0 Strategy A)
- Rimosso PHP branching tier-1/tier-2/default → unico render path con conditional graceful:
  - `$render_body_extended = (body_extended SCF !== '')`
  - `$render_post_content = (! body_ext && has_post_content)`
  - `$render_tier1_clusters = (is_tier_1 && ! body && ! post_content && map slug found)`
- Sezione body unica con priorità body_extended SCF → post_content fallback → tier-1 hardcoded clusters helper (preservato GEO per 3 tier-1 senza body popolato)
- Ref-lawyer card universal: deriva da `lead_attorneys[0]` per TUTTE le competenze, fallback legacy map tributario→emiliano/lavoro→fabiana/lgbtq→antonia per backwards-compat tier-1
- Casi sezione universal condizionata su `! empty($valid_casi)` (non più tier-only)
- Class modifier `<article class="sl-competenza sl-competenza--tier-1|--tier-2">`
- CSS tier-1 visual distinctness scoped sotto `.sl-competenza--tier-1`: display-band H1, capsule asym indent desktop `margin-left: 20%`, hero asym grid 8fr/4fr, photo ratio 1/1, padding lift sezioni, casi 3-col
- BEM rename `.sl-tier1__*` → `.sl-competenza__*` (sub/ref-lawyer*/cluster*). Rule storiche `.sl-tier1__*` lasciate dead-code inerte (71 rule, cleanup integrale defer wave dedicata futura)
- **FIX CORE Elena:** tier-2 con body_extended popolato era invisibile pre-refactor → ora reso correttamente. Tier-2 con post_content + body_extended entrambi popolati: prevale body_extended (canonico)

### Batch 3 (v1.3.19) — 1 fix J template alignment
~4 min single-agent orchestrator + 1 Code agent.

**Wave J (4 file +78/-39):**
- #22 Page 2714 prenota-appuntamento layout uniformato a Page 2713 richiedi-preventivo via approccio "extend router info-shared" (Pattern A conservative)
- `template-parts/page-info-shared.php` esteso a 6° Page slot: aggiunta location `prenota-appuntamento` a `group_info_shared_v1.json` (PURO additive — altri 5 Pages info-shared invariati: costi-e-consulenze hub + prima-consulenza + come-lavoriamo + richiedi-preventivo + lavora-con-noi)
- Page 2714 ora rende: hero grid 8/4 + body editorial 60ch + CTA finale navy
- Defaults 12 string editoriali sensati gestiti PHP-side via `saltelli_field($name, $pid, $default)` conditional `if ($is_prenota === true)` per evitare contaminazione cross-page
- `group_prenota_appuntamento_v1` (1 field `prenota_intro`) lasciato ACTIVE come LEGACY DATA-COMPAT — backward-compat in template: se `body_content` (nuovo SCF) vuoto E slug == prenota-appuntamento, leggo `prenota_intro` legacy → editor pre-esistente sopravvive senza data migration
- CSS: zero nuove rule (`.sl-info-page*` esistenti coprono markup). Class modifier `.sl-info-page--prenota-appuntamento` auto-emessa per future override
- Rischi documentati: editor vede 2 metabox finché non migra manualmente content — mitigazione post-cut via `wp eval` script + disattivazione group legacy. Defer

## Wave 6.0 partial (v1.3.24) — CPT competenza content migration + 2 incident WSOD + sanitize chirurgico

~3 ore totali wall-clock, 3 round.

### Round 1 — Migration (v1.3.22)
Commit `9b5221c` merge `feat/cpt-competenza-content-migration`:
- Template patch `single-competenza.php:213` `wp_kses_post($body_ext)` → `apply_filters('the_content', $body_ext)` per allineamento semantic post_content (wpautop + shortcode + oEmbed)
- Script `scripts/migrate-cpt-competenza-content.php` (231 righe, WP-CLI eval-file, dry-run/wet-run mode, idempotent, backup `_legacy_post_content_backup` postmeta)
- `docs/SCF_ORPHAN_FIELDS.md` updated

Wet-run script su staging:
- **16 MIGRATED** (post_content → body_extended)
- **3 SKIP tier-1 already-populated** (2664/2665/2666 Elena-written pre-migration)
- **5 SKIP draft not-publish** (2680/2681/2705/2706/2707)
- Total: 24 CPT scanned
- Audit integrity check PASS (body == backup byte-per-byte sui 16 migrated)

Bonus: script args parsing patched per WP-CLI `eval-file` quirks (accept positional + `--wet-run`/`wet-run`/`wet`).

### Round 2 — WSOD frontend rollback template (v1.3.23)
Dopo deploy v1.3.22, frontend competenze restituiva pagina bianca. Causa probabile: `apply_filters('the_content', $body_ext)` su content legacy migrato → fatal error (shortcode legacy non più registrati OR do_blocks parse fail OR oEmbed timeout sync HTTP).

Commit `d93b50a` `fix(emergency)`: rollback `single-competenza.php:213` → `echo wp_kses_post($body_ext)` (semantica originale Wave C). Migration DATI restano valide: HTML literal h2/p/strong renderizza correttamente con `wp_kses_post`.

### Round 3 — WSOD admin sanitize chirurgico (v1.3.24)
Post-rollback template, frontend OK ma admin edit post 2670 (responsabilita-medica, migrato) ancora bianco mentre 2664 (tributario, Elena-written) OK. Differenza:
- `body_extended` di 2670 = raw copy post_content via `update_post_meta()` script SENZA sanitize
- `body_extended` di 2664 = scritto via SCF wysiwyg ACF moderno + WP escape standard

Ipotesi: TinyMCE admin SCF wysiwyg crash su raw legacy HTML (encoding/control-chars/script/iframe/event-handlers).

**Sanitize chirurgico via WP-CLI DB ops** (NO commit codice, solo dati staging): per i 16 migrated:
- strip control chars `[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]`
- normalize line endings `\r\n|\r` → `\n`
- `wp_kses_post()` (strip script/iframe/object/embed/form/event-handlers, preserve markup semantico)

Test su 2670 → admin sblocco confermato. Apply su tutti 16 → tutti admin-editable. Frontend invariato post-sanitize (`wp_kses_post` idempotent su markup già pulito).

Wave 6.0 partial CHIUSA. Wave 6.0 full (disable Gutenberg per CPT competenza + pre-sanitize pipeline migration v2) resta backlog post-cut.

## 0.17.x — consolidation log (4 collisioni di numbering risolte)

Fra `Codencore` (istanza esterna) e `me`/Aldo c'è stato un parallel work che ha generato 8 commit con numerazione duplicata `0.17.0 / .1 / .2 / .3` (2 commit ciascuno, file disgiunti, nessun conflict tecnico). Storia git lasciata immutabile, version interna `SALTELLI_THEME_VERSION` riflette sempre l'ultimo arrivato.

| SHA | Author | Tag interno | Cosa porta |
|---|---|---|---|
| `e63d989` | me | v0.17.0 | Logo system v1.1 (header/footer + favicon SVG monogramma) |
| `0426aa3` | Codencore | v0.17.0 | Sitemap audit: 3 nuove competenze + 8 page top-level + menu rebuild gerarchico |
| `8a7b36b` | me | v0.17.1 | Favicon fix (SVG corrotti 7B → ricostruiti dal brief) |
| `5fbca6e` | Codencore | v0.17.1 | Home fix: areas list opacity stuck + hero white-space + tassonomia 3 nuove |
| `ccb0ed8` | me | v0.17.2 | /contatti/ rework: form sopra contatti classici + rename + aria submit |
| `e02a254` | Codencore | v0.17.2 | Hero white-space cleanup + section rhythm armonia 80/80 |
| `2e9189f` | me | v0.17.3 | Sede text-only (iframe rimosso) + wp_site_icon legacy unhooked |
| `7df2bb3` | Codencore | v0.17.3 | Tag pills text centered + padding symmetric |

## Versioning policy (da v0.17.4 in poi)

Per evitare future collisioni quando più agent committano in parallelo:

1. **Prima di scegliere la version**, controllare l'ultimo `SALTELLI_THEME_VERSION` su `origin/main`:
   ```sh
   git fetch origin main && git show origin/main:wp-content/themes/saltelli/functions.php | grep SALTELLI_THEME_VERSION
   ```
2. **Bump monotonic**: se sull'origin c'è `0.X.Y`, il nuovo commit usa `0.X.(Y+1)` — mai lo stesso `Y`.
3. **Suffix sempre presente** (`-beta-<topic>`) per leggibilità human nel `git log`.
4. **Se push fallisce** per non-fast-forward, `git pull --rebase`, ribumpa e ripusha — non risolvere a mano i conflitti su `style.css` / `functions.php` mantenendo la propria version.

## Prompt archive

**Agent prompts** (in `prompts/` se attivo, in `_archive/prompts-completed/{categoria}/` se completato):

- `recovery-v1.0/PROMPT_AGENT_v1.0_WAVE0_FOUNDATION.md` — ✅ done
- `recovery-v1.0/PROMPT_AGENT_v1.0_WAVE1_FIELD_GROUPS.md` + `_RECOVERY.md` — ✅ done
- `recovery-v1.0/PROMPT_AGENT_v1.0_WAVE2_CONTENT_MIGRATION.md` — ✅ done
- `recovery-v1.0/PROMPT_AGENT_v1.0_WAVE3_TEMPLATE_REFACTOR.md` — ✅ done
- `recovery-v1.0/PROMPT_AGENT_v1.0_DEBUG_QA.md` — ✅ done (4 bugs, 3 closed + 1 deferred, P0 fix `page_slug ==`)
- `recovery-v1.0/PROMPT_AGENT_WAVE4_7_FIX_2_TRUE_FIX.md` — ✅ done (5 phases · 21 file · +1688/-57 · 26/26 URL smoke pass · SCF 60→93 fields)
- `recovery-v1.0/PROMPT_AGENT_WAVE4_7_FIX_3_PAGE_METABOX.md` — ✅ done (5 phases · 20 file · +1849/-643 · 30 field migrati · 4 Pages WP · Theme Options 13/14→9 tab · 4/4 smoke pass)
- `recovery-v1.0/PROMPT_AGENT_WAVE4_7_FIX_4_STRATEGY_A_FULL_SCF.md` — ✅ done (6 phases · 26 file · +2755/-38 · pivot empirico 6/7 zombie + 1/7 live · 12 Pages Gutenberg-disabled · 12/12 smoke pass)
- `recovery-v1.0/PROMPT_AGENT_WAVE4_7_FIX_5_PAGES_CLEANUP_BLOG_DOC.md` — ✅ done (5 phases · 10 file · +686/-15 · 35→19 Pages cleanup · Customizer lock per role editor · incident OOM `is_super_admin` risolto)
- Wave 5 STEP 1/2/3/4 — inline in chat orchestratore. Output: `audits/wave5-pages-completeness/` (STEP 1) + `audits/wave5-step4-sections-cleanup/` (STEP 4)
- **Design Handoff sequenza P1-P12** (2026-05-12, 12 wave consecutive, 11 prompt file):
  - `PROMPT_AGENT_DESIGN_HANDOFF_AUDIT.md` — ✅ done
  - `PROMPT_AGENT_DESIGN_HANDOFF_P1_CHROME.md` — ✅ done
  - `PROMPT_AGENT_DESIGN_HANDOFF_P2_FOOTER.md` — ✅ done (skip)
  - `PROMPT_AGENT_DESIGN_HANDOFF_P3_HOME.md` — ✅ done (v1.3.14)
  - `PROMPT_AGENT_DESIGN_HANDOFF_P4_SINGLE_COMPETENZA_TIER1.md` — ✅ done
  - `PROMPT_AGENT_DESIGN_HANDOFF_P5_SINGLE_AVVOCATO.md` — ✅ done
  - `PROMPT_AGENT_DESIGN_HANDOFF_P6_TAXONOMY_TIPO_AREA.md` — ✅ done (skip)
  - `PROMPT_AGENT_DESIGN_HANDOFF_P7_CHI_SIAMO_LO_STUDIO_CONSOLIDAMENTO.md` — ✅ done (v1.3.15)
  - `PROMPT_AGENT_DESIGN_HANDOFF_P8_BLOG_ARCHIVE.md` — ✅ done
  - `PROMPT_AGENT_DESIGN_HANDOFF_P9_ARCHIVE_CASI.md` — ✅ done (v1.3.16)
  - `PROMPT_AGENT_DESIGN_HANDOFF_P10_GLOSSARIO_LEGALE.md` — ✅ done
  - `PROMPT_AGENT_DESIGN_HANDOFF_P12_404.md` — ✅ done (FINAL 12/12)
- `prompts/_BACKLOG_PROMPT_AGENT_DESIGN_HANDOFF_P11_CONTATTI.md` — ⏸ SKIP, backlog post-cut
- ~~`prompts/PROMPT_AGENT_v1.0_WAVE4_PRODUCTION_READINESS.md`~~ — ❌ obsolete (path scartato, Wave 4 eseguita via 4.5/4.6/4.7.*/4.8)
- `deploy/PROMPT_AGENT_G_DEPLOY_DIGITALOCEAN.md` — runbook archiviato, sostituito de facto da `docs/DEPLOY.md`
- `_archive/prompts-completed/{orchestration-original,pre-recovery-v0.x,recovery-v0.9,recovery-v1.0,_obsolete}/`

## Custom fields plugin — SCF (Wave 4.7.fix, 2026-05-08)

**Plugin attivo:** Secure Custom Fields 6.8.4 — fork Automattic di ACF (Q4 2024)
**Plugin precedente:** Advanced Custom Fields Free 6.8.0 (deactivated, NOT removed)

**Motivo switch:** ACF Free non include `acf_add_options_page()` (Pro-only). CMS Diagnosis Round 2 (REPORT.md 2026-05-08) ha identificato bug architetturale: Theme Options page non si registrava mai (silent no-op del `function_exists()` guard in `inc/acf-fields.php:30`) → Elena/Ludovica non potevano modificare 50 field globali.

**API compat:** drop-in. `get_field`, `update_field`, `acf_add_options_page`, `acf_get_field_groups`, `acf_get_options_pages`, location rules custom (es. `page_slug ==` del Debug-QA bug-04 fix), JSON auto-load da `acf-json/`, tutti funzionanti.

**Stato post-switch:** `function_exists(acf_add_options_page)=YES`, `defined(ACF_PRO)=YES`, 17 field group preserved, options page `saltelli-settings` registrata + visibile in admin slot 60. 50 chiavi `options_*` popolate (26 baseline Wave 4.6 + 24 seeded da `inc/seed-theme-options.php`).

**Rollback emergency** (1-shot, già testato su staging Phase 1):
```sh
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp plugin deactivate secure-custom-fields --path=/var/www/saltelli && \
  sudo -u www-data wp plugin activate advanced-custom-fields --path=/var/www/saltelli && \
  sudo -u www-data wp cache flush --path=/var/www/saltelli"
```

**Backup pre-switch su droplet:** `~/backups/wave4-7-fix-pre-switch-20260508-1220/` (db.sql 59MB · theme.tar.gz 352KB · plugins-acf.tar.gz 6.2MB).
