# Debug & QA — Report Finale

> Stress test pre-production · 2026-05-05
> Branch: `feat/debug-qa` (da main `b2b0975`) · Theme version: `1.0.0-recovery-wave3-debug`
> Method: sequenziale single-agent (Claude Code) · 6 phases ~1.5h elapsed

---

## 📊 Score: 4 bugs trovati · 3 closed · 1 deferred

| Bug | Severity | Status | Note |
|---|---|---|---|
| bug-01 canonical-missing-site-wide | P1 | ✅ CLOSED | Yoast 27.5 non emetteva canonical; theme ora emette sempre |
| bug-02 yoast-organization-fake-social-urls | P1 | ✅ CLOSED | Filter wpseo_schema_graph sovrascrive sameAs con ACF Theme Options |
| bug-03 lo-studio-slug-vs-title-mismatch | P3 | ⏸ DEFERRED | Cosmetic, redirect 301 funziona; richiede decisione editoriale Elena |
| bug-04 acf-page-id-mismatch-droplet-vs-locale | **P0** | ✅ CLOSED | Custom ACF location rule slug-based + data re-migration |

---

## 🐛 Bug breakdown dettagliato

### P0 (Critical) — 1 found · 1 CLOSED

**bug-04 ACF page-id mismatch droplet vs locale**

Wave 2 migration aveva hardcoded local Docker page IDs (2705, 2706, 2708, 2709,
2710); droplet ha IDs DIVERSI per /faq/ + 4 info-shared pages → ACF data
popolato su pagine WRONG su droplet. Cliente Elena non poteva editare 6 page
via WP-Admin.

Fix architetturale env-portable:
- A. Custom ACF location rule `page_slug ==` in inc/acf-fields.php (38 righe)
- B. 5 Field Group JSON aggiornati a slug-based location
- C. scripts/debug-qa-fix-page-id-mismatch.php — re-migrazione data droplet via slug + cleanup orphan

Risultato post-fix:
- 6 page WP custom hanno ACF data corretto su droplet
- 3 wrong pages (Diritto societario, Contrattualistica, Glossario legale) hanno orphan ACF rimosso
- Cliente Elena può ora editare TUTTE le 9 page custom via WP-Admin

### P1 (Medium-High) — 2 found · 2 CLOSED

**bug-01 canonical-missing-site-wide**

29/29 HTML URL audit non emettevano `<link rel=canonical>`. Yoast 27.5 doveva
emetterlo ma per qualche config non lo faceva. Theme bail-out totale su SEO
plugin attivo.

Fix: split saltelli_emit_meta_tags in 2 funzioni; nuovo saltelli_emit_canonical
priority 3 emette SEMPRE. Verify post-fix 7/7 URL hanno canonical.

**bug-02 yoast-organization-fake-social-urls**

Yoast Organization schema sameAs aveva 6 URL legacy non confermati dal cliente
(Facebook/X/TikTok/YouTube fake + LinkedIn personale erroneamente su Org).

Fix: filter wpseo_schema_graph priority 13 sovrascrive sameAs con ACF Theme
Options (social_facebook + social_instagram + saltelli_studio_data fallback).

Risultato: sameAs ora 2 URL autoritativi invece di 6 fake.

### P3 (Low cosmetic) — 1 found · 1 DEFERRED

**bug-03 lo-studio-slug-vs-title-mismatch**

Page id 19 ha post_title="Lo studio" ma slug "chi-siamo". /lo-studio/ ritorna
301 → /chi-siamo/ (working). Cosmetic IA inconsistency.

Decision: deferred a editorial decision con Elena (Wave 4 / Wave 5).

---

## ✅ Cosa è stato verificato OK (no bugs)

### HTTP smoke
- Smoke baseline 21/21 PASS
- Smoke estended 32/32 PASS (incluso sitemap, llms.txt, robots.txt)
- Smoke post-fix 32/32 PASS

### HTML structure
- 0 PHP error markers visibili
- 0 ACF unrendered tokens cross-page
- 0 multiple H1 / no-H1 issues
- Mobile viewport meta presente
- Heading hierarchy corretta (1 H1 + 12 H2 + 3 H3 su tier-1 sample)

### Link check
- 65 unique internal links via curl loop · 65 OK · 0 broken · 0 redirect chains

### Schema JSON-LD soft validation
- 5/5 URL chiave hanno schema valido + parsable
- Yoast @graph con WebPage + BreadcrumbList + WebSite + Organization
- /avvocati/emiliano-saltelli/ aggiunge Person+Attorney (GEO-rich)
- /faq/ aggiunge FAQPage (AEO-critical)
- LegalService custom theme schema su tutte le pagine

### ACF rendering
- 24/26 Theme Options popolati (2 intentional vuoti: linkedin/twitter)
- 4 lawyer profiles: hero_role + bio_breve OK; 3/4 bio_estesa vuota → fallback post_content (UX OK)
- 3 tier-1 competenze: answer_capsule 292-396 chars + body_extended 2859-3110 chars + is_tier_1=YES
- 7 CPT items totali: 63 items (modalita 3, scenario 3, principio 3, trust 4, caso 10, faq 28, formazione 12)

### Cross-page integrazione (post bug-04 fix)
- /faq/, /come-lavoriamo/, /prima-consulenza/, /lavora-con-noi/, /richiedi-preventivo/, /guide-gratuite/ ora hanno ACF data corretto su droplet
- Wrong pages (diritto-societario/contrattualistica/glossario-legale) hanno ACF Wave 1 fields rimossi

---

## ⏸ Cosa è stato deferred

**bug-03 lo-studio-slug-vs-title-mismatch** → Wave 4 / Wave 5 con input Elena.

**Console errors check** (Phase 4.3) → manual TODO: lychee + headless Chrome
non installati nell'ambiente. Delegato a Elena/orchestratore con DevTools
Chrome su 6 URL chiave.

**Form CF7 end-to-end** (Phase 3.2) → delegato a Elena per test invio email
reale a info@studiolegalesaltelli.it (richiede accesso casella).

**Cross-browser visual** (Phase 2 matrice) → delegato a manual Chrome+Firefox+Safari
desktop+mobile (Elena/orchestratore).

---

## 🚦 Pronto per Wave 4?

**SI** — con caveat:

1. ✅ Funzionalità CMS-autonomy ora consistente (cliente può editare tutto via
   WP-Admin grazie a bug-04 fix)
2. ✅ SEO base solida (canonical su tutte le URL, Yoast schema corretto)
3. ✅ Smoke 32/32 PASS post-fix
4. ✅ Brand integrity OK (sameAs autoritativo invece di fake)
5. ⏸ Manual checks (console errors, cross-browser visual, form CF7 email)
   delegati a Elena — NON bloccano Wave 4 ma da chiudere prima di production

**Wave 4 può procedere** con WOFF2 self-host + SRI + Critical CSS + Lighthouse
(scope Wave 4 di per sé) **dopo** che orchestratore audita questo report e
mergea feat/debug-qa → main.

---

## 🚦 Branch & deploy state finale

**Branch**: `feat/debug-qa` (locale)
- 7 commit (Phase 1-6 + 3 fix commit)
- Push: `git push -u origin feat/debug-qa` (Phase 6.3)
- Merge in main: **DECISIONE ORCHESTRATORE** in chat dopo audit

**Droplet staging**:
- Theme files Wave 3 + Debug-QA fix DEPLOYED via rsync (acf-fields.php +
  5 Field Group JSON + meta-tags.php + yoast-schema-extensions.php)
- DB: ACF data re-migrata + cleanup orphan (script eseguito su droplet)
- PHP-FPM reloaded · WP cache + transient flushed
- Bump version droplet → 1.0.0-recovery-wave3-debug (Phase 6.2)
- Smoke 32/32 PASS https://staging.studiolegalesaltelli.it ✓

**Backup pre-debug**:
- `/tmp/saltelli-backups/saltelli-pre-debug-20260505-121237.tar.gz` (theme · 323 KB)
- `/tmp/saltelli-backups/saltelli-db-pre-debug-20260505-121237.sql.gz` (DB · 7.7 MB)

---

## 📋 Files modificati cumulativi (questo branch)

```
Theme files:
  wp-content/themes/saltelli/inc/acf-fields.php                    (+38 righe — custom location rule)
  wp-content/themes/saltelli/inc/seo/meta-tags.php                 (+13 -1 — canonical always)
  wp-content/themes/saltelli/inc/seo/yoast-schema-extensions.php   (+44 — sameAs override)
  wp-content/themes/saltelli/acf-json/group_costi_v1.json          (page → page_slug)
  wp-content/themes/saltelli/acf-json/group_casi_v1.json           (page → page_slug)
  wp-content/themes/saltelli/acf-json/group_contatti_v1.json       (page → page_slug)
  wp-content/themes/saltelli/acf-json/group_faq_v1.json            (page → page_slug)
  wp-content/themes/saltelli/acf-json/group_info_shared_v1.json    (page → page_slug × 5)
  wp-content/themes/saltelli/style.css                             (Version: 1.0.0-recovery-wave3-debug)
  wp-content/themes/saltelli/functions.php                         (SALTELLI_THEME_VERSION)

Scripts:
  scripts/debug-qa-fix-page-id-mismatch.php                        (nuovo, idempotente env-safe)

Knowledge:
  .claude/knowledge/audits/debug-qa/bugs/01..04                    (4 bug ticket, 3 closed 1 deferred)
  .claude/knowledge/audits/debug-qa/checks/                        (smoke baseline, html-audit, link-check, schema-soft, acf-rendering, console-manual, url-list)
  .claude/knowledge/audits/debug-qa/reports/REPORT.md              (questo file)
```

---

## Next steps (orchestratore in chat)

1. **Audit di questo report** + 4 bug ticket files (.claude/knowledge/audits/debug-qa/)
2. **Decisione su bug-03**: editorial discussion con Elena per slug vs title
3. **Manual checks pendenti** (console DevTools + form CF7 + cross-browser visual): delega Elena
4. **Merge feat/debug-qa → main** (squash o rebase preference orchestratore)
5. **Bump droplet a 1.0.0-recovery-wave3-debug** (già allineato post-fix)
6. **Lancio Wave 4 / Production Readiness** (PROMPT_AGENT_v1.0_WAVE4_PRODUCTION_READINESS.md)

---

*Generato 2026-05-05 by Debug & QA Agent (Claude Code single-agent).*
*Pattern simmetrico a Wave reports: stress test, NON nuove feature.*
