# Wave 4.7.fix.4 STRATEGY A FULL SCF MIGRATION — Final Report

**Branch**: `feat/wave4-7-fix-4-strategy-a-full-scf`
**Version**: `1.3.10-wave4-7-fix-4-strategy-a-full-scf`
**Data**: 2026-05-10
**Tempo effettivo**: ~120 min (significativamente sotto i 240-300 min stimati)
**Commits**: 6 (P1 → P6)

---

## TL;DR

Strategy A FULL SCF MIGRATION elimina la dualità `post_content` Gutenberg ↔ SCF metabox per le 12 Pages WP target. Risolve feedback Elena 2026-05-08:

> "il CMS non è usabile in questo modo" + "queste sezioni non sono ancora editabili e per esempio la pagina in editor è un'altra sebbene abbia i metabox corretti"

**Modello mentale post-fix.4**: una sola sorgente di verità per ogni Page WP = il metabox SCF. L'editor di contenuto classico/Gutenberg è disabilitato sulle 12 Pages target. Elena apre la pagina e vede: title + slug + notice + metabox. Zero ambiguità.

## Cosa è stato fatto

### Phases completed (6/6)

| Phase | Cosa | Commits | File modificati |
|---|---|---|---|
| P1 | Discovery — post_content + SCF state audit | 88d9665 | 11 file audit |
| P2 | SCF group retitling (3 group JSON) | e3837cb | 3 JSON |
| P3 | Data migration post_content → SCF (1 page) + backup (7 pages) | 246819e | 1 migration PHP + 1 log |
| P4 | Template refactor — `the_content()` fallback removed | 0ef030b | 1 PHP + 1 audit |
| P5 | post_content cleanup + Gutenberg disable + admin UX | d6e25ef | 2 NEW admin PHP + 1 PHP mod + 1 audit |
| P6 | Docs + version bump + final QA | (this commit) | 2 file version + EDITOR-HANDOFF + REPORT |

**Total file modified**: 20 file (7 audit + 3 SCF JSON + 1 migration PHP + 2 admin PHP + 3 PHP mod + EDITOR-HANDOFF + 2 version + 1 REPORT)

### Migration scope reale

Audit empirico ha rivelato che la situazione era **più semplice** di quanto il prompt assumeva:

**Stato pre-fix.4 per 7 target Pages**:

| Page ID | Slug | post_content | Rendered su frontend? | Status |
|---|---|---|---|---|
| 23 | contatti | 899 chars | NO (template page-contatti.php SCF-only) | ZOMBIE |
| 2708 | domande-frequenti | 1117 chars | NO (template page-faq.php renderizza CPT items) | ZOMBIE |
| 2709 | guide-gratuite | 677 chars | NO (body_content SCF già popolato, prio 1) | ZOMBIE |
| 2712 | come-lavoriamo | 1432 chars | NO (idem) | ZOMBIE |
| 2711 | prima-consulenza | 909 chars | NO (idem) | ZOMBIE |
| 372 | lavora-con-noi | 1251 chars | NO (idem) | ZOMBIE |
| 2713 | richiedi-preventivo | 879 chars | **YES** (body_content SCF vuoto, fallback the_content() attivo) | **LIVE** |

**Implicazioni**:
- 6/7 = pure zombie cleanup (svuotare post_content, nessuna migrazione necessaria)
- 1/7 (Page 2713) = migrazione reale: `post_content` → SCF `body_content` (field già esistente nel group_info_shared_v1, era solo vuoto)

### Decisioni autonome divergenti dal prompt

Documentate dettagliatamente in `01-mapping-decisions.md`:

1. **NESSUN nuovo SCF field aggiunto**: prompt suggeriva di creare field per "recruiting" su contatti, "modalità" su prima-consulenza, etc. Realtà: queste sezioni del post_content non sono renderizzate sul frontend → field nuovi sarebbero stati invisibili.

2. **NESSUN split di `group_info_shared_v1` in 5 group dedicati**: prompt suggeriva 5 group con field key dedicati. Motivazioni per NON splittare:
   - SCF storage è per-post (postmeta), no ambiguità reale
   - 5 file JSON quasi identici da mantenere
   - Field key collision rischiosa (richiede rename + shadow postmeta update migration)
   - Post Phase 5 Gutenberg disable, editor vede solo metabox — non vede il group "title" come confondente

3. **3 SCF group rinominati** invece di splittati: `group_contatti_v1` → "Saltelli — Page Contatti", `group_faq_v1` → "Saltelli — Page Domande Frequenti", `group_info_shared_v1` → "Saltelli — Page Servizi" — naming consistency con i 4 hub group Wave 4.7.fix.3.

4. **Page 2811 (lo-studio) aggiunta a SCF_ONLY list**: il prompt chiedeva di valutare cancellazione. Realtà: serve `/chi-siamo/lo-studio/` come child URL, ha SCF metabox attached (group_lo_studio_v1). Non orfana, va in lista Gutenberg disable per consistency.

5. **Helper `saltelli_page_has_scf_content()` NON implementato**: prompt suggeriva helper dinamico. Realtà: Phase 5 Gutenberg disable usa lista hardcoded `SALTELLI_SCF_ONLY_PAGES = [12 IDs]`. Helper dinamico = complessità senza beneficio attuale.

6. **Page.php default fallback `the_content()` preservato**: per Pages WP non in switch dispatcher (privacy, cookie, ecc.) il render WP standard resta. Solo `template-parts/page-info-shared.php` line 93 modificato.

### File creati (5)

- `wp-content/themes/saltelli/inc/migrations/wave4-7-fix-4-postcontent-to-scf.php` — migration script idempotente
- `wp-content/themes/saltelli/inc/admin/disable-gutenberg-for-scf-pages.php` — Gutenberg disable + notice
- `wp-content/themes/saltelli/inc/admin/scf-archive-headers-shortcuts.php` — admin bar + tab guidance
- `.claude/knowledge/audits/wave4-7-fix-4-strategy-a-full-scf/` — 6 audit MD + 7 raw HTML + 1 REPORT

### File modificati (8 codice + 1 doc)

- `wp-content/themes/saltelli/acf-json/group_contatti_v1.json` — retitle
- `wp-content/themes/saltelli/acf-json/group_faq_v1.json` — retitle
- `wp-content/themes/saltelli/acf-json/group_info_shared_v1.json` — retitle
- `wp-content/themes/saltelli/template-parts/page-info-shared.php` — rimosso `the_content()` fallback
- `wp-content/themes/saltelli/functions.php` — +5 lines (require_once 2 admin files + version bump)
- `wp-content/themes/saltelli/style.css` — version bump
- `docs/EDITOR-HANDOFF.md` v4.0 → v5.0

### DB modifications (staging)

- 7 Pages WP `post_content`: emptied (backup in `_legacy_post_content_backup` postmeta)
- 7 Pages WP `_legacy_post_content_backup` postmeta: written (recoverable)
- 1 Page WP (2713) SCF `body_content` postmeta: written (879 chars)
- 1 Page WP (2713) shadow `_body_content`: written (`field_info_body_content`)

---

## Final smoke test (12 punti)

| # | Test | Result |
|---|---|---|
| 1 | `/contatti/` HTTP 200 | ✓ |
| 2 | `/risorse/domande-frequenti/` HTTP 200 | ✓ |
| 3 | `/risorse/guide-gratuite/` HTTP 200 | ✓ |
| 4 | `/costi-e-consulenze/come-lavoriamo/` HTTP 200 | ✓ |
| 5 | `/costi-e-consulenze/prima-consulenza/` HTTP 200 | ✓ |
| 6 | `/contatti/lavora-con-noi/` HTTP 200 | ✓ |
| 7 | `/costi-e-consulenze/richiedi-preventivo/` HTTP 200 | ✓ |
| 8 | Gutenberg disabled per 12 target Pages | ✓ (12/12) |
| 9 | Control: Page 2695 Gutenberg ENABLED | ✓ |
| 10 | Saltelli Settings → Archive Headers guidance present (in admin context) | ✓ (826 chars instructions) |
| 11 | Migration script idempotency (re-run no side effects) | ✓ |
| 12 | Version bump functions.php + style.css | ✓ |

12/12 PASS.

---

## Frontend impact post-cleanup

Cambiamenti totali rispetto al baseline pre-fix.4:

| Page | Delta bytes | Tipo cambiamento |
|---|---|---|
| `/contatti/` | 0 | Identical |
| `/risorse/domande-frequenti/` | 0 | Identical |
| `/risorse/guide-gratuite/` | +8 | Solo whitespace indent |
| `/costi-e-consulenze/come-lavoriamo/` | -102 | Yoast `twitter:Est. reading time` rimosso (post_content empty), schema dateModified updated |
| `/costi-e-consulenze/prima-consulenza/` | +9 | Whitespace |
| `/contatti/lavora-con-noi/` | -102 | Stesso Yoast effect |
| `/costi-e-consulenze/richiedi-preventivo/` | +9 | Whitespace |

**Content visibile sulla pagina invariato** per tutti i 7 URL. I -102 byte sono SEO meta tag minori (reading time twitter card) — Yoast li ricalcola dal post_content (ora vuoto) e li skippa. Per fix futuro: estendere Yoast a leggere reading time da `body_content` SCF (out of scope).

---

## Open items per orchestratore

### TODO orchestratore post-merge

1. **Bump CLAUDE.md** in chat sessione orchestratore. Aggiornamenti necessari:
   - Header current state: `v1.3.10-wave4-7-fix-4-strategy-a-full-scf`
   - Last updated: 2026-05-10 (Wave 4.7.fix.4 mergeata: Strategy A FULL SCF)
   - Tabella "What's done": aggiungi riga Wave 4.7.fix.4
   - Footer last updated string

2. **Documentare in CLAUDE.md la lesson learned admin-side smoke test**: WP-CLI eval di hook fires (`use_block_editor_for_post`, `acf/load_field`) richiede simulazione admin context per testare i filtri scoped a `is_admin()`. Pattern usato:
   ```sh
   wp eval '$GLOBALS["current_screen"] = WP_Screen::get("admin.php"); ...'
   ```

3. **Verifica admin acceptance test con Elena** via WP-Admin login:
   - WP-Admin → Pagine → seleziona qualsiasi delle 12 Pages target → verifica: NO editor Gutenberg, SOLO metabox SCF + notice "Modifica il contenuto qui sotto"
   - WP-Admin → Pagine → Costi e Consulenze (2695, NON in lista) → verifica: Gutenberg ATTIVO (control)
   - WP-Admin → Saltelli — Settings → tab Archive Headers → verifica: notice guidance con link a CPT visibili
   - Frontend `/chi-siamo/team/` (loggato come admin) → verifica admin bar: "Modifica header archivio" submenu visibile

4. **Validare con Elena** che il modello mentale è completo: "modifica pagina = SOLO field SCF". Chiedi se il workflow è ora intuitivo e zero confusione.

5. **Yoast reading-time meta su 2 pagine**: side effect (-102 byte/pagina) sui meta tag SEO `twitter:label1` "Est. reading time" — Yoast lo calcola dal post_content (ora vuoto) e lo skippa. Out of scope. Se vogliamo ripristinarlo: estendere Yoast a calcolare da `body_content` SCF tramite filter `Yoast\WP\SEO\...` (richiede dev e plugin Yoast Schema docs).

### Out of scope (NON tocco — Wave futura se servisse)

- **Page 2695 Costi e Consulenze**: hub con SCF nativo già funzionale via CPT children, Gutenberg in admin volutamente non disabilitato (può essere zombie ma non in scope di questa wave).
- **Pages senza SCF metabox attached**: privacy, cookie, note-legali, glossario, blog — mantengono Gutenberg standard.
- **Wave 4.9 Gutenberg migration**: il prompt CLAUDE.md menziona valutare Gutenberg migration come futura wave. Strategia A ha chiuso il gap dualità per ora.
- **lo-studio (2811) deletion**: NON cancellata (serve URL `/chi-siamo/lo-studio/`).

---

## Rollback procedures

### Full rollback (DB + theme)

```sh
# 1. DB restore (UNDO post_content empty + migrations)
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp db import ~/backups/wave4-7-fix-4-pre-migration/db-pre-fix4-20260510-2016.sql --path=/var/www/saltelli && \
  sudo -u www-data wp cache flush --path=/var/www/saltelli && \
  sudo systemctl reload php8.2-fpm"

# 2. Git revert (se mergeato in main)
git revert <merge_commit_sha> -m 1
git push origin main

# 3. Re-rsync theme files staging
rsync -avz --rsync-path='sudo rsync' \
  wp-content/themes/saltelli/ \
  deploy@178.62.207.50:/var/www/saltelli/wp-content/themes/saltelli/
```

### Selective rollback (ripristino post_content)

Per ripristinare il post_content originale di una specifica Page senza fare full DB restore:

```sh
# Example: ripristina Page 23 (contatti)
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  BACKUP=\$(sudo -u www-data wp post meta get 23 _legacy_post_content_backup --path=/var/www/saltelli) && \
  sudo -u www-data wp post update 23 --post_content=\"\$BACKUP\" --path=/var/www/saltelli && \
  sudo -u www-data wp cache flush --path=/var/www/saltelli"
```

### Per disable solo Gutenberg disable (mantieni post_content empty)

Rimuovi require_once da functions.php:
```sh
sed -i.bak '/disable-gutenberg-for-scf-pages/d' \
  wp-content/themes/saltelli/functions.php
```

---

## Stats

| Metrica | Valore |
|---|---|
| Phases completate | 6/6 |
| Commits | 6 (1 per phase) |
| File creati | 13 (7 audit + 1 migration + 2 admin + 1 REPORT + 1 raw txt × 7) |
| File modificati | 9 (3 SCF JSON + 1 PHP template + 2 PHP root + EDITOR-HANDOFF + 2 version + 1 functions) |
| Lines added (cumulative) | ~2400 |
| Lines deleted | ~80 |
| Page WP affected (Gutenberg disabled) | 12 |
| Page WP migrate post_content → SCF | 1 (richiedi-preventivo 2713) |
| Page WP backup `_legacy_post_content_backup` | 7 |
| Page WP post_content emptied | 7 |
| Smoke test passati | 12/12 |
| Tempo effettivo | ~120 min |

---

## Closing note

Strategy A FULL SCF chiude in modo definitivo la dualità Gutenberg/SCF metabox per i 12 Pages WP target. Elena ora vede in admin SOLO il metabox SCF — niente più editor di contenuto sopra che confonde sulla sorgente di verità.

La realtà di post_content era più semplice di quanto il prompt assumeva (6/7 zombie, 1/7 live) → wave conclusa in metà tempo. Migration backup `_legacy_post_content_backup` preservato per safety totale.

Lesson learned chiave per future wave admin-side: i filter scoped a `is_admin()` richiedono simulazione admin context in WP-CLI eval per essere testabili. Pattern documentato in §Open items.

---

*REPORT.md · Wave 4.7.fix.4 STRATEGY A FULL SCF MIGRATION · 2026-05-10 · feat/wave4-7-fix-4-strategy-a-full-scf · ready for orchestrator audit + merge.*
