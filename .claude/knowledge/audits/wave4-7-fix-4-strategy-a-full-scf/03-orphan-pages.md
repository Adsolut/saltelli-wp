# Phase 1.E — Orphan / legacy Page audit

## `lo-studio` (ID 2811)

| Field | Value |
|---|---|
| ID | 2811 |
| post_title | "Chi Siamo" |
| post_name (slug) | `lo-studio` |
| post_status | publish |
| post_content | **EMPTY** (0 char) |
| post_date | 2026-05-06 20:14:07 |
| _wp_page_template | (none / default fallback `page.php`) |

### URL serving check

```
GET https://staging.studiolegalesaltelli.it/lo-studio/
→ 301 → /chi-siamo/lo-studio/
```

`lo-studio` non è orphan: serve la URL `/chi-siamo/lo-studio/` come child page sotto l'hub `/chi-siamo/` (Page 2822). La redirect 301 da `/lo-studio/` standalone porta alla child URL canonical.

### SCF attachment

- File: `acf-json/group_lo_studio_v1.json` (5.2 KB)
- Location rule: `page_slug == lo-studio`
- Status: attivo, popolato (verifica WP Admin)

### Template render

`page.php:27` `$sl_lo_studio = is_page('lo-studio');` → dispatch a `template-parts/page-lo-studio.php` (Wave 3 refactor).

### Conclusione

**Page 2811 NON è orphan**. Funzionale e attiva. NON cancellare.

Decisione Phase 5: aggiungere ID 2811 alla lista `SALTELLI_SCF_ONLY_PAGES` per disable Gutenberg (consistency con le altre 11 Page con SCF metabox attached).

---

## Audit Page WP totali

Lista completa Pages WP publish:

```sh
ssh deploy@178.62.207.50 "sudo -u www-data wp post list --post_type=page --post_status=publish --fields=ID,post_title,post_name --format=csv --path=/var/www/saltelli"
```

Snapshot salvato in `~/backups/wave4-7-fix-4-pre-migration/pages-snapshot.csv` (974 bytes).

| ID | Title | Slug | Note |
|---|---|---|---|
| 17 | Home | home | page_on_front · Wave 4.7.fix.3 SCF metabox |
| 23 | Contatti | contatti | Wave 4.7.fix.4 target · ZOMBIE cleanup |
| 372 | Lavora con noi | lavora-con-noi | Wave 4.7.fix.4 target · ZOMBIE cleanup |
| 2695 | Costi e Consulenze | costi-e-consulenze | hub · già SCF-driven via group_costi_v1 |
| 2708 | Domande frequenti | domande-frequenti | Wave 4.7.fix.4 target · ZOMBIE cleanup |
| 2709 | Guide gratuite | guide-gratuite | Wave 4.7.fix.4 target · ZOMBIE cleanup |
| 2711 | Prima consulenza | prima-consulenza | Wave 4.7.fix.4 target · ZOMBIE cleanup |
| 2712 | Come lavoriamo | come-lavoriamo | Wave 4.7.fix.4 target · ZOMBIE cleanup |
| 2713 | Richiedi un preventivo | richiedi-preventivo | Wave 4.7.fix.4 target · **LIVE migration** |
| 2811 | Chi Siamo | lo-studio | child di chi-siamo · già SCF-driven · add to Gutenberg disable list |
| 2812 | Aree di Pratica | aree-di-pratica | Wave 4.7.fix.3 SCF metabox |
| 2813 | Risorse | risorse | Wave 4.7.fix.3 SCF metabox |
| 2822 | Chi Siamo (hub) | chi-siamo | Wave 4.7.fix.3 SCF metabox |
| (altre) | varie | privacy, cookie, glossario, ecc. | Out of scope Phase 5 (no SCF metabox attached) |

---

## Pages NON in scope (no Gutenberg disable, mantengono editor classico/Gutenberg)

- `/privacy/`, `/cookie/`, `/note-legali/` — pagine puramente testuali con post_content live, no SCF metabox
- `/glossario-legale/` — render speciale via `inc/wave3-glossario.php`, no SCF metabox standard
- altre minor pages senza SCF group attached

Queste mantengono Gutenberg editor classico — il filter `use_block_editor_for_post` di Phase 5 NON scatta su loro.

---

*Audit completato 2026-05-10 · decisione: lo-studio 2811 NON cancellare, aggiungere a SCF_ONLY_PAGES Phase 5*
