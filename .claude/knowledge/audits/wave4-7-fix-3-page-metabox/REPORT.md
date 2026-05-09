# Wave 4.7.fix.3 PAGE METABOX MIGRATION — Final Report

**Branch**: `feat/wave4-7-fix-3-page-metabox`
**Version**: `1.3.9-wave4-7-fix-3-page-metabox`
**Data**: 2026-05-08
**Tempo effettivo**: ~150 min
**Commits**: 5 (P1 → P5)

---

## TL;DR

Wave 4.7.fix.3 ribalta il pattern UX SCF: il content delle 4 Page WP principali (Home, Chi Siamo, Aree di Pratica, Risorse) si modifica ora **dalla Page WP stessa** (WP-Admin → Pagine → seleziona pagina → metabox dedicato) anziché da un pannello globale separato (Saltelli — Settings → tab "Hub Pages" + 4 tab homepage).

Risolve feedback Elena 2026-05-08:
> "https://staging.studiolegalesaltelli.it/chi-siamo/ non è modificabile al pari di altre pagine simili. Il Cms non è usabile in questo modo."

Modello mentale ripristinato: "modifica pagina = modifica contenuto pagina".

---

## Cosa è stato fatto

### Phases completed (5/5)

| Phase | Cosa | Commits | File modificati |
|---|---|---|---|
| P1 | Discovery — Pages WP × SCF reads decision matrix | 1824a5d | 3 file audit (CSV + 2 MD) |
| P2 | SCF field group split + location rules | 920c807 | 4 nuovi JSON + 1 modificato + 5 PHP |
| P3 | Data migration wp_options → wp_postmeta | 80eb1b4 | 1 nuovo PHP + 1 audit log |
| P4 | Theme Options cleanup + helper simplification | a5b2b07 | 2 PHP modificati |
| P5 | Documentation + version bump + final QA | (in this commit) | 4 file (EDITOR-HANDOFF, CLAUDE.md TODO, functions.php, style.css) |

**Total file modified**: 22 file (5 audit + 5 SCF JSON + 5 PHP themed + 1 migration + 1 EDITOR-HANDOFF + 2 version bump + 3 audit md + 1 report)

### Migration scope

**MIGRATI** a Page metabox (30 SCF field):
- **Page 17 "Home"** (slug=`home`, page_on_front) → 12 field divisi in 4 tab (Hero / Studio Section / Team & Casi / Press)
- **Page 2822 "Chi Siamo"** (slug=`chi-siamo`) → 4 field (Eyebrow / H1 main / H1 emphasis / Intro)
- **Page 2812 "Aree di Pratica"** (slug=`aree-di-pratica`) → 10 field (4 hero + 6 cluster cards in 2 tab)
- **Page 2813 "Risorse"** (slug=`risorse`) → 4 field (Eyebrow / H1 main / H1 emphasis / Intro)

**RESTANO** in Theme Options (Saltelli — Settings) — 9 tab globali:
- Studio Info, Mappa, Brand, Footer, Social, CTA Defaults, Footer Aree, Taxonomy Tipo Area, Archive Headers

**Page WP NON toccate**: Costi e Consulenze (2695), Lo Studio (2811), Contatti (23), e tutte le altre 18+ Page WP — out of scope Wave 4.7.fix.3.

### File creati (4 SCF group + 1 migration script)

- `wp-content/themes/saltelli/acf-json/group_homepage_v1.json` — 17 elementi (1 message + 4 tab + 12 field)
- `wp-content/themes/saltelli/acf-json/group_chi_siamo_v1.json` — 5 elementi (1 message + 4 field)
- `wp-content/themes/saltelli/acf-json/group_aree_di_pratica_v1.json` — 13 elementi (1 message + 2 tab + 10 field)
- `wp-content/themes/saltelli/acf-json/group_risorse_v1.json` — 5 elementi (1 message + 4 field)
- `wp-content/themes/saltelli/inc/migrations/wave4-7-fix-3-options-to-postmeta.php` — script idempotente migration

### File modificati (5 PHP + 1 SCF JSON)

- `wp-content/themes/saltelli/acf-json/group_theme_options_v1.json` — rimosso 5 tab + 30 field migrati. Pre: 80 elementi / 14 tab. Post: 60 elementi / 9 tab + 1 intro message.
- `wp-content/themes/saltelli/inc/helpers.php` — nuovo helper `saltelli_page_field()` + aggiornati saltelli_homepage_cases(), saltelli_press_outlets(), saltelli_press_outlets_full()
- `wp-content/themes/saltelli/inc/seed-theme-options.php` — docblock aggiornato (no code change)
- `wp-content/themes/saltelli/front-page.php` — refactor `saltelli_option('hero_*'|'studio_*'|'team_*'|'cases_*')` → `saltelli_page_field(...)`
- `wp-content/themes/saltelli/template-parts/page-chi-siamo-hub.php` — refactor `hub_chisiamo_*`
- `wp-content/themes/saltelli/template-parts/page-aree-di-pratica-hub.php` — refactor `hub_aree_*` + 3 cluster
- `wp-content/themes/saltelli/template-parts/page-risorse-hub.php` — refactor `hub_risorse_*`

### Documentazione aggiornata

- `docs/EDITOR-HANDOFF.md` v3.0 → v4.0 — bump frontmatter, changelog v4.0, §3.5 admin path matrix aggiornata, §4 Saltelli Settings ridotto da 10→6 tab dettagli, §5.0 nuova sub-section "I 4 hub pages con metabox dedicato"
- `.claude/knowledge/audits/wave4-7-fix-3-page-metabox/` — 5 file audit completi

---

## Smoke test final

### Frontend HTTP 200

| URL | HTTP | Content rendering |
|---|---|---|
| `/` | 200 | ✓ Hero (Diritto, con misura. + eyebrow Dal 2008), studio_body 3 paragrafi, team grid, casi list, press, footer |
| `/chi-siamo/` | 200 | ✓ Eyebrow "Chi siamo", H1 "Quattro avvocati / un atelier", lede populated, 3 hub cards |
| `/aree-di-pratica/` | 200 | ✓ Eyebrow + H1 split, 3 cluster cards (Privati/Imprese/Contenzioso) |
| `/risorse/` | 200 | ✓ Eyebrow + H1 "Approfondire / senza fretta", 4 resource cards |

### SCF location rules

| Page WP | Title | Field group attaccato |
|---|---|---|
| 17 | Home | group_homepage_v1 ✓ |
| 2822 | Chi Siamo | group_chi_siamo_v1 ✓ |
| 2812 | Aree di Pratica | group_aree_di_pratica_v1 ✓ |
| 2813 | Risorse | group_risorse_v1 ✓ |

### Saltelli — Settings post-cleanup (9 tab)

```
Studio Info, Mappa, Brand, Footer, Social, CTA Defaults, Footer Aree,
Taxonomy Tipo Area, Archive Headers
```

(Pre Wave 4.7.fix.3: 14 tab. Post: 9 tab.)

### Redirect 301 legacy Wave 4.7.fix.2 ancora attivi

| URL legacy | HTTP | Redirect target |
|---|---|---|
| `/chi-siamo/risultati/` | 301 | `/chi-siamo/casi-rappresentativi/` ✓ |
| `/competenze/` | 301 | `/aree-di-pratica/` ✓ |
| `/faq/` | 301 | `/risorse/domande-frequenti/` ✓ |
| `/costi/` | 301 | `/costi-e-consulenze/` ✓ |

### Versioning

- `wp-content/themes/saltelli/functions.php`: `1.3.9-wave4-7-fix-3-page-metabox` ✓
- `wp-content/themes/saltelli/style.css`: `1.3.9-wave4-7-fix-3-page-metabox` ✓

### Migration log (Phase 3 staging run)

```
Run #1: 30 migrated, 0 skipped, 0 errors
Run #2 (idempotency): 5 migrated (empty-shadow re-write no-op), 25 skipped (postmeta già popolato)
```

### Cleanup wp_options (Phase 4 staging)

- 30 chiavi `options_<field>` legacy cancellate
- 30 shadow `_options_<field>` cancellate
- Total: 60 keys removed
- Frontend post-cleanup: HTTP 200 + content invariato (saltelli_page_field legge solo da postmeta)

---

## Open items per orchestratore

### TODO orchestratore post-merge

1. **Bump CLAUDE.md** in chat sessione orchestratore. Aggiornamenti necessari:
   - Header current state: `v1.3.9-wave4-7-fix-3-page-metabox`
   - Last updated: 2026-05-08 (Wave 4.7.fix.3 mergeata: Page Metabox migration)
   - Tabella "What's done": aggiungi riga Wave 4.7.fix.3
   - Footer last updated string

2. **Documentare in CLAUDE.md la lessons learned OPcache stale**: dopo edit di file PHP critici (helpers.php, helper functions usati da template), serve `sudo systemctl reload php8.2-fpm` su staging anche dopo `wp cache flush`. Aggiungere nota deploy procedure.

3. **Verifica admin acceptance test** con Elena via WP-Admin login:
   - WP-Admin → Pagine → Home → Modifica → vede metabox "Saltelli — Page Homepage" con 4 tab popolate
   - WP-Admin → Pagine → Chi Siamo → Modifica → vede metabox "Saltelli — Page Chi Siamo" con 4 field popolati
   - WP-Admin → Pagine → Aree di Pratica → Modifica → vede metabox 2 tab (Hero + Cluster Cards)
   - WP-Admin → Pagine → Risorse → Modifica → vede metabox con 4 field popolati
   - Testa modifica un field, save, verifica frontend riflette

4. **Validare con Elena** che il modello mentale è ripristinato: "modifica pagina = modifica contenuto pagina". Chiedi se il workflow è ora intuitivo.

### Decisioni prese autonomamente (documentate in 03-decision-matrix.md)

1. ✓ Naming convenzione: `group_<slug-with-underscores>_v1.json` (consistency codebase)
2. ✓ Location rule: `page_slug ==` (NON `page == ID`) per env-portability
3. ✓ Static Homepage = Page 17 esistente (NON creo Page nuova)
4. ✓ Field key + name preserved cross-split (data continuity)
5. ✓ studio_foto_facciata: scalar attachment ID migrato (SCF deriva array al runtime)
6. ✓ NON migrato Page 2695 (Costi e Consulenze) — out of scope (no SCF reads from theme options)
7. ✓ NON migrato cta_default_*, colophon_*, studio_* (NAP) — globali
8. ✓ Tab "Taxonomy Tipo Area" creata in Theme Options per i 2 field taxonomy_tipoarea_* prima sotto "Hub Pages"
9. ✓ Helper saltelli_page_field semplificato in P4 (rimossa fallback transition options)

### Out of scope (NON tocco — Wave futura se servisse)

- Page 2695 (Costi e Consulenze) — già completamente CMS-editable via SCF per il post_content (~4649 chars)
- Page 23 (Contatti) — già SCF metabox via `group_contatti_v1` (page_slug == contatti)
- Page 2811 (Lo Studio) — già SCF metabox via `group_lo_studio_v1` (page_slug == lo-studio)
- Hub `costi-e-consulenze` template — non legge field SCF migrabili
- 4 cards `/risorse/` (Blog/FAQ/Glossario/Guide) — restano hardcoded (links statici, count dinamico via wp_count_posts)
- 4 cards `/chi-siamo/` (Lo Studio/Team/Risultati) — restano hardcoded (links statici)
- Issue pre-esistente "§ Chi siamo" senza § seed Wave 4.7.fix.2 — wp_options seed non aveva il simbolo, post-migration display "Chi siamo" senza §. NON regression introdotta da Wave 4.7.fix.3. Per ripristinare: Elena svuota field e salva, default JSON ha "§ Chi siamo".

---

## Rollback procedures

### Full rollback (DB + theme)

```sh
# 1. DB restore (UNDO migration)
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp db import ~/backups/wave4-7-fix-3-pre-migration/db-pre-migration-20260508-1801.sql --path=/var/www/saltelli && \
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

### Selective rollback (postmeta delete + ripristino wp_options)

Se la migration ha problemi ma il theme code è OK, basta cancellare le postmeta migrate e re-seedare le wp_options:

```sh
# Delete postmeta keys + shadow
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  for pid in 17 2822 2812 2813; do \
    for k in hero_eyebrow hero_headline hero_subheadline hero_cta_label hero_cta_url \
             studio_titolo_sezione studio_body studio_foto_facciata team_titolo cases_titolo \
             casi_rappresentativi_home press_outlets \
             hub_chisiamo_eyebrow hub_chisiamo_h1_main hub_chisiamo_h1_emphasis hub_chisiamo_intro \
             hub_aree_eyebrow hub_aree_h1_main hub_aree_h1_emphasis hub_aree_intro \
             hub_aree_cluster_privati_label hub_aree_cluster_privati_desc \
             hub_aree_cluster_imprese_label hub_aree_cluster_imprese_desc \
             hub_aree_cluster_contenzioso_label hub_aree_cluster_contenzioso_desc \
             hub_risorse_eyebrow hub_risorse_h1_main hub_risorse_h1_emphasis hub_risorse_intro; do \
      sudo -u www-data wp post meta delete \$pid \$k --path=/var/www/saltelli 2>/dev/null; \
      sudo -u www-data wp post meta delete \$pid _\$k --path=/var/www/saltelli 2>/dev/null; \
    done; \
  done; \
  sudo -u www-data wp eval-file wp-content/themes/saltelli/inc/seed-theme-options.php --path=/var/www/saltelli; \
  sudo -u www-data wp cache flush --path=/var/www/saltelli"
```

Helper saltelli_page_field ritorna $default (Phase 4 simplification senza fallback options) → frontend rende il copy hardcoded nel template fallback (DEC-029 pattern). Display invariato per la maggior parte dei field default-popolati nel JSON.

---

## Stats

| Metrica | Valore |
|---|---|
| Phases completate | 5/5 |
| Commits | 5 (1 per phase) |
| File creati | 8 (5 audit + 4 SCF JSON + 1 migration) |
| File modificati | 8 (helpers, seed, front-page, 3 hub templates, theme_options JSON, EDITOR-HANDOFF + 2 version) |
| Lines added | +875 (P2) + +390 (P3) + +261 (P1) + ~+200 (P4+P5) ≈ +1700 |
| Lines deleted | -567 (P2 cleanup theme_options) + -16 (P4) ≈ -580 |
| SCF group nuovi | 4 |
| SCF group modificati | 1 |
| Field migrati | 30 (più 30 shadow `_<key>`) |
| wp_options keys deletate | 60 (30 + 30 shadow) |
| Page WP affected | 4 (17, 2822, 2812, 2813) |
| Smoke test passati | 4/4 (HTTP 200) + content rendering OK |
| Redirect 301 legacy preserved | 14 (Wave 4.7.fix.2) |
| Tempo effettivo | ~150 min |

---

*REPORT.md · Wave 4.7.fix.3 PAGE METABOX MIGRATION · 2026-05-08 · feat/wave4-7-fix-3-page-metabox · ready for orchestrator audit + merge.*
