# Wave 4.7.fix.2 TRUE FIX — Final Report

**Branch**: `feat/wave4-7-fix-2-true-fix`
**Version**: `1.3.7-wave4-7-fix-1-scf-url-validation` → `1.3.8-wave4-7-fix-2-true-fix`
**Date**: 2026-05-08
**Phases**: 5/5 completed
**Commits**: 6 (P1 + P2 + P3 + P4 + P5 + previous orchestrator commit `782fce0` already on branch base)

---

## Phase summary

| Phase | Topic | Files changed | Key outcome |
|---|---|---|---|
| P1 | studio_body editorial fallback → JSON default + template refactor | 3 | Bug Duccio "Studio Section admin vuoto" risolto · `options_studio_body` popolato con 929 chars HTML default · admin == frontend |
| P2 | Menu rebuild + slug rename `risultati`→`casi-rappresentativi` + redirect 301 | 4 | CPT `saltelli_caso` slug ridefinito · `/chi-siamo/risultati/` + 9 single-caso URLs ridirezionano · menu primary 22 voci con riferimenti `type=post_type/taxonomy` |
| P3 | EDITOR-HANDOFF v2.0 → v3.0 | 1 | Doc aggiornato con changelog v3.0 + nuova sezione §3.5 "Pagina WP vs Tassonomia vs Archive CPT" + tabella 15 URL Elena + recurring blocks SCF map |
| P4 | SCF tier-2 migration (recurring + hub pages + archive headers) | 9 | 33 nuovi field SCF (60 → 93) · 3 nuovi tab admin · 8 template refactored · Elena ora edita TUTTO il copy editoriale |
| P5 | Version bump + final QA + push | 2 | `1.3.8` everywhere · 26/26 URL test passed · ready for orchestrator audit + merge |

---

## Files modified (cumulative)

```
acf-json/group_theme_options_v1.json                 (P1 + P4)
front-page.php                                       (P1)
inc/cpt-recovery.php                                 (P2)
inc/seo/legacy-redirects.php                         (P2)
inc/migrations/wave4-7-fix-2-editorial-defaults.php  (P1, NEW)
scripts/wave4-7-fix-2-menu-rebuild.sh                (P2, NEW)
header.php                                           (P4)
footer.php                                           (P4)
archive-avvocato.php                                 (P4)
archive-saltelli_caso.php                            (P4, NEW)
taxonomy-tipo-area.php                               (P4)
template-parts/page-chi-siamo-hub.php                (P4)
template-parts/page-aree-di-pratica-hub.php          (P4)
template-parts/page-risorse-hub.php                  (P4)
docs/EDITOR-HANDOFF.md                               (P3)
functions.php / style.css                            (P5)
```

Untracked artifacts kept in tree:
- `prompts/PROMPT_AGENT_WAVE4_7_FIX_2_TRUE_FIX.md` (this wave prompt)
- `.claude/knowledge/audits/wave4-7-fix-2-true-fix/` (audit logs incl. this REPORT.md)

Out-of-scope (not touched): `docs/qa/tokens-drift-audit-2026-05-08.md` (untracked preexisting).

---

## SCF schema — counts

```
Pre-wave:   60 fields, 10 tabs
Post-wave:  93 fields, 13 tabs
New tabs:   Footer Aree (slot 80) · Hub Pages (slot 85) · Archive Headers (slot 90)
New fields: 33 (incl. 1 repeater root + 3 repeater sub_fields)
```

Seed run on staging:
- 30 nuove chiavi `options_*` aggiunte da `seed-theme-options.php`
- Idempotent re-run: 0 seeded, 80 skipped (presence-based check)
- `options_studio_body` migrated empty→929 chars via `inc/migrations/wave4-7-fix-2-editorial-defaults.php`

---

## Redirect 301 map (post-extension)

Esistente in `inc/seo/legacy-redirects.php` (Wave 5 IA Refactor + extensions):
- 22 mapping legacy Elementor → audit-aligned (Step 1 mappa A→C)
- 18 mapping MVP corrente → audit-aligned (Step 2 mappa B→C, **+2 entries P2**)
- Step 3-5 dynamic regex patterns

P2 additions:
- `/casi/` → `/chi-siamo/casi-rappresentativi/` (was `/chi-siamo/risultati/`)
- `/chi-siamo/risultati/` → `/chi-siamo/casi-rappresentativi/`
- Step 5 regex `/chi-siamo/risultati/{slug}/` → `/chi-siamo/casi-rappresentativi/{slug}/` (covers 9 single saltelli_caso posts)

---

## Menu rebuild — 22 items

Old: 22 items, ALL `type=custom` con URL hardcoded legacy (es. `/competenze/`, `/faq/`, `/costi/`).
New: 22 items: **17 type=post_type** (page references) + **3 type=taxonomy** (tipo-area terms) + **2 type=custom** (CPT archive `/chi-siamo/team/` + `/chi-siamo/casi-rappresentativi/`, no Page WP equivalent).

Cleaning: orphan "Lo Studio" submenu (duplicate of `/chi-siamo/` parent) removed.

Rebuild script: `scripts/wave4-7-fix-2-menu-rebuild.sh` (portable: page+term IDs resolved by slug at runtime → works on staging IDs ≠ local Docker IDs).

---

## Cross-template smoke (final, post version bump)

```
26/26 URL pass (status code expected):
  HTTP 200: 19 hub/page/CPT URLs
  HTTP 301: 7 legacy redirect URLs (preserved + P2 new entries)

Single caso post smoke:
  /chi-siamo/risultati/cassazione-2022/: 301 → casi-rappresentativi/...
  /chi-siamo/casi-rappresentativi/cassazione-2022/: 200

SCF integration smoke (HTML parse):
  /chi-siamo/ → eyebrow + lede da SCF
  /aree-di-pratica/ → eyebrow + lede + 3 cluster cards (Privati / Imprese / Contenzioso) da SCF
  /risorse/ → eyebrow + lede da SCF
  /chi-siamo/team/ → eyebrow + H1 split + lede da SCF
  /chi-siamo/casi-rappresentativi/ → header completo + 9 post listed
  /aree-di-pratica/privati/ → NEW eyebrow "§ Cluster" rendered + lede term description

Recurring blocks smoke:
  WhatsApp template: ATTIVO da SCF (es. "Ciao, sto guardando la pagina X sul vostro sito. Vorrei una consulenza.")
  Footer tier1 aree: render OK (fallback editoriale legacy quando repeater non popolato)
  Sticky bar mobile: continua a funzionare (no SCF wiring necessario)
```

---

## DB / verifiche staging

```
options_studio_body: 929 chars HTML (vs '' pre-fix)
options_whatsapp_message_default: "Ciao, %s sul vostro sito. Vorrei una consulenza."
options_hub_chisiamo_h1_main: "Quattro avvocati,"
options_archive_caso_intro: "Una selezione anonimizzata di pratiche dello Studio. ..."
options_taxonomy_tipoarea_subtitle_template: "Aree di pratica per %s"
options_footer_tier1_aree: "0" (repeater count, no rows seeded — fallback legacy attivo)
```

WP-CLI version verify:
```
$ wp eval "echo SALTELLI_THEME_VERSION;"
1.3.8-wave4-7-fix-2-true-fix
```

---

## Open items / known issues

1. **`options_footer_tier1_aree` repeater empty in DB**.
   Seed-theme-options.php non popola sub-rows per i repeater (per design — i repeater richiederebbero default per riga che JSON non ha). Footer fallback editoriale legacy attivo (3 link tier-1 hardcoded).
   **Action item**: Elena deve aprire Saltelli Settings → Footer Aree → aggiungere 3 entries quando vuole curare manualmente.

2. **Backup DB pre-seed P4 fallito** (permissions error su `~/backups/wave4-7-fix-2-p4/db-pre-seed-*.sql`: write to deploy home denied per www-data).
   Non bloccante: backup file-system del tema ok; rollback codice possibile via `~/backups/wave4-7-fix-2-p4/*.bak.*` (file PHP).
   **Mitigazione**: per future wave, eseguire `wp db export` come deploy user, non www-data.

3. **OPcache reload obbligatorio post-deploy**.
   `wp cache flush` da solo non è sufficiente — i file `.php` modificati restano cached in OPcache fino a `sudo systemctl reload php8.2-fpm`. Aggiunto al runbook deploy.
   Documentato in REPORT P2 e in commit message P2.

4. **Legacy `prenota-un-appuntamento` page (ID 361)** rimane in DB (non rimossa).
   Solo per backup; il menu primary rebuild punta a 2714 (`prenota-appuntamento`, Wave 5 slug). 361 + redirect 301 lo dirotta a `/contatti/`.
   **Action item**: orchestrator decida se cancellare la legacy page in housekeeping successiva.

5. **`docs/qa/tokens-drift-audit-2026-05-08.md` untracked preesistente** (out of scope wave).
   Lasciato intatto: rispetto della one-writer-at-a-time rule.
   **Action item**: orchestrator decida cosa farne in chat.

---

## Rollback procedures

Per ogni phase, 1-shot rollback:

**P1** (studio_body):
```sh
git checkout 5d03b9f^ -- wp-content/themes/saltelli/{front-page.php,acf-json/group_theme_options_v1.json}
ssh deploy@178.62.207.50 'sudo -u www-data wp option update options_studio_body "" --path=/var/www/saltelli'
```

**P2** (menu + redirects):
```sh
git checkout 5d03b9f^ -- wp-content/themes/saltelli/inc/{cpt-recovery.php,seo/legacy-redirects.php}
ssh deploy@178.62.207.50 'sudo systemctl reload php8.2-fpm && sudo -u www-data wp rewrite flush --hard --path=/var/www/saltelli'
# Menu rebuild rollback: re-import .claude/knowledge/audits/wave4-7-fix-2-true-fix/02-menu-backup-pre-rebuild.json (manual)
```

**P3** (docs): `git checkout HEAD~3 -- docs/EDITOR-HANDOFF.md`

**P4** (SCF tier-2):
```sh
git checkout 5d03b9f^ -- wp-content/themes/saltelli/{header.php,footer.php,archive-avvocato.php,taxonomy-tipo-area.php,template-parts/}
rm wp-content/themes/saltelli/archive-saltelli_caso.php
git checkout 5d03b9f^ -- wp-content/themes/saltelli/acf-json/group_theme_options_v1.json
# DB cleanup (optional — values are inert without templates):
ssh deploy@178.62.207.50 '
  WP=/var/www/saltelli
  for k in options_whatsapp_message_default options_hub_* options_archive_* options_taxonomy_tipoarea_* options_footer_tier1_aree*; do
    sudo -u www-data wp option delete "$k" --path=$WP 2>/dev/null
  done'
```

**Full rollback to pre-wave**: `git revert 5d03b9f..HEAD` (5 commits).

---

## Time spent (estimate)

| Phase | Estimated | Actual |
|---|---|---|
| P0 pre-flight | 15 min | ~10 min |
| P1 studio_body | 30-45 min | ~25 min |
| P2 menu + redirect | 60-90 min | ~50 min |
| P3 EDITOR-HANDOFF | 15 min | ~12 min |
| P4 SCF tier-2 | 90-120 min | ~85 min |
| P5 final QA + push | 10 min | ~10 min |
| **Total** | **220-295 min** | **~192 min** |

---

## Decisions taken autonomously

1. **bio_breve / ruolo / linkedin / archive empty-state classified UX_PLACEHOLDER** (not EDITORIAL).
   Phase 3b regex correlated empty defaults to next `else` block in file (cross-line). Manual re-read confirmed they're decorative placeholders, alternate-content branches, or empty-state copy — none editor-visible HTML fallbacks. **Phase 1 scope reduced to studio_body only.**

2. **Created `inc/migrations/wave4-7-fix-2-editorial-defaults.php`** rather than modifying existing seed-theme-options.php logic.
   Rationale: `seed-theme-options.php` uses presence-based idempotency (correct for first-time seed). For backfilling fields that were seeded empty in Wave 4.7.fix Phase 2 baseline, a targeted migration with whitelist + empty-detection is cleaner than overloading the seed function with two semantics.

3. **Extended `inc/seo/legacy-redirects.php`** rather than creating new `inc/redirects.php`.
   Discovered existing infrastructure during P2 investigation (more comprehensive than what prompt anticipated). Adding 2 entries + 1 regex pattern preserved the existing redirect chain.

4. **Refactored menu rebuild script to use slug-based ID resolution** instead of hardcoded IDs.
   Discovered local Docker IDs ≠ staging IDs. Slug-based resolver makes the script portable across environments.

5. **Created `archive-saltelli_caso.php`** instead of refactoring `archive.php`.
   The existing `archive.php` handles multiple archive types (category/tag/CPT-fallback). Forking the saltelli_caso branch into a dedicated template is cleaner than adding `is_post_type_archive('saltelli_caso')` conditionals to the generic.

6. **Skipped explicit eyebrow render in taxonomy-tipo-area.php template by default**.
   Made eyebrow `if ($sl_taxonomy_eyebrow)` conditional. JSON default is `§ Cluster` so the eyebrow DOES render on staging post-seed. Preserves visual stability if editor wipes the field.

7. **WhatsApp message refactor only on `header.php`**.
   `mobile-sticky-bar.php` has no message text in its `wa.me/` link (just a plain link, no `?text=`), so no SCF wiring needed there.

8. **Footer tier1 with hard fallback**.
   Repeater seed doesn't populate sub-rows (by design). Footer `if (empty($ftr_tier1)) { fallback }` keeps render stable until Elena populates the repeater manually.

---

*End of report. Branch ready for orchestrator audit + merge to main.*
