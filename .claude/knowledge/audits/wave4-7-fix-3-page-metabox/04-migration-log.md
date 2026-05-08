# Wave 4.7.fix.3 P3 Migration Log

**Data**: 2026-05-08
**Script**: `inc/migrations/wave4-7-fix-3-options-to-postmeta.php`
**Run env**: staging (droplet 178.62.207.50, /var/www/saltelli)
**Backup pre-migration**: `~/backups/wave4-7-fix-3-pre-migration/db-pre-migration-20260508-1801.sql` (59MB)

---

## Run #1 — Initial migration

```
=== Wave 4.7.fix.3 Migration Report ===
Migrated: 30
Skipped:  0
Errors:   0
```

### Detail per Page

**Page 17 (Homepage, slug=home) — 12 field**
- ✓ MIG: hero_eyebrow = "Studio Legale · Napoli · Chiaia · Dal 2008"
- ✓ MIG: hero_headline = "Diritto, con misura."
- ✓ MIG: hero_subheadline = "Studio Legale Saltelli &amp; Partners. Quattro avvocati a Chiaia, ..."
- ✓ MIG: hero_cta_label = "Prenota una consulenza gratuita"
- ✓ MIG: hero_cta_url = "/contatti/"
- ✓ MIG: studio_titolo_sezione = "Un atelier, in senso napoletano."
- ✓ MIG: studio_body = "<p>Lo Studio Saltelli &amp; Partners nasce nel 1999..." (WYSIWYG, 3 paragrafi)
- ✓ MIG: studio_foto_facciata = "2211" (attachment ID)
- ✓ MIG: team_titolo = "Quattro\nprofessionisti."
- ✓ MIG: cases_titolo = "Casi rappresentativi."
- ✓ MIG (empty + shadow): casi_rappresentativi_home (no row, fallback CPT recent)
- ✓ MIG (empty + shadow): press_outlets (row_count=0, no rows)

**Page 2822 (Chi Siamo) — 4 field**
- ✓ MIG: hub_chisiamo_eyebrow = "Chi siamo"
- ✓ MIG: hub_chisiamo_h1_main = "Quattro avvocati,"
- ✓ MIG: hub_chisiamo_h1_emphasis = "un atelier."
- ✓ MIG (empty + shadow): hub_chisiamo_intro

**Page 2812 (Aree di Pratica) — 10 field**
- ✓ MIG: hub_aree_eyebrow = "Aree di pratica"
- ✓ MIG: hub_aree_h1_main = "Diciassette aree,"
- ✓ MIG: hub_aree_h1_emphasis = "tre cluster."
- ✓ MIG (empty + shadow): hub_aree_intro
- ✓ MIG: hub_aree_cluster_privati_label = "Per i privati"
- ✓ MIG: hub_aree_cluster_privati_desc = "Famiglie e persone fisiche, lavoratori..."
- ✓ MIG: hub_aree_cluster_imprese_label = "Per le imprese"
- ✓ MIG: hub_aree_cluster_imprese_desc = "Aziende, freelance, partite IVA..."
- ✓ MIG: hub_aree_cluster_contenzioso_label = "Contenzioso amministrativo"
- ✓ MIG: hub_aree_cluster_contenzioso_desc = "TAR, Consiglio di Stato, ricorsi..."

**Page 2813 (Risorse) — 4 field**
- ✓ MIG: hub_risorse_eyebrow = "Risorse"
- ✓ MIG: hub_risorse_h1_main = "Approfondire,"
- ✓ MIG: hub_risorse_h1_emphasis = "senza fretta."
- ✓ MIG (empty + shadow): hub_risorse_intro

### Note

- 25 field con valori migrati (non-empty)
- 5 field "shadow only" (valore source empty/zero, scritto solo `_<key>` per SCF reference)
- press_outlets repeater (row_count=0): nessuna sub-key da migrare
- studio_foto_facciata: scalar attachment ID `2211` migrato; SCF return_format=array deriva url/alt al runtime

---

## Run #2 — Idempotency test

```
Success: Migration complete: 5 migrated, 25 skipped, 0 errors.
```

I 25 field con valori sono SKIPPED (postmeta già popolato, presence-based check).
I 5 field empty-shadow vengono "ri-scritti" come no-op (update_post_meta dello stesso valore, no duplication, no corruption). Non è un problema di idempotency vera (il DB resta consistente), ma il count "5 migrated" può essere fuorviante. Per un audit più rigido, l'idempotency check dovrebbe controllare anche la presenza della shadow `_<key>`. Skip per Wave 4.7.fix.3.

---

## Verifica post-migration

### get_field test (postmeta read)

```php
get_field('hub_chisiamo_eyebrow', 2822) → 'Chi siamo'    ✓
get_field('studio_body', 17) → '<p>Lo Studio Saltelli &amp;...'  ✓
get_field('hero_headline', 17) → 'Diritto, con misura.'  ✓
```

### Frontend smoke (4 URL)

| URL | HTTP | Content invariato? |
|---|---|---|
| `/` | 200 | ✓ (hero, studio_body, team, cases, press, footer) |
| `/chi-siamo/` | 200 | ✓ (eyebrow, h1, lede, 3 hub cards) |
| `/aree-di-pratica/` | 200 | ✓ (cluster cards Privati/Imprese/Contenzioso) |
| `/risorse/` | 200 | ✓ (4 resource cards) |

### Comportamento helper

- `saltelli_page_field('hub_chisiamo_eyebrow')` → ora legge da postmeta (2822) primo, value="Chi siamo"
- Pre-migration: ritornava da fallback options ("Chi siamo")
- Post-migration: ritorna da postmeta ("Chi siamo")
- **Stesso risultato visivo, source diversa**

### Metabox admin

A questo punto, su WP-Admin → Pagine → Chi Siamo → Modifica, dovrebbe apparire metabox "Saltelli — Page Chi Siamo" con i 4 field popolati con i valori migrati. Verifica empirica deferred a smoke test orchestratore (parte di Phase 5 final QA).

---

## Open items

1. **wp_options legacy keys**: tuttora popolate (cleanup deferred a Phase 4, dopo conferma stable migration).
2. **Discrepancy "§" eyebrow**: pre-existing — i seed values Wave 4.7.fix.2 P4 NON contengono il simbolo "§" (es. "Chi siamo" anziché "§ Chi siamo"). Il display frontend è "Chi siamo" — atteso, ereditato dai valori salvati. Per ripristinare "§" Elena può svuotare il field e salvare (default_value JSON ha "§").
3. **Press repeater empty**: nessun outlet popolato. Quando Elena aggiungerà outlets dal metabox Page Homepage, partirà da count=0.
4. **casi_rappresentativi_home**: empty. Helper saltelli_homepage_cases() fa fallback ai 6 CPT più recenti (path 3 in helpers.php).

---

## Rollback procedure

In caso di problema post-migration:

```sh
# 1. Ripristino DB completo (UNDO migration + qualsiasi altro change post-backup)
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp db import /home/deploy/backups/wave4-7-fix-3-pre-migration/db-pre-migration-20260508-1801.sql --path=/var/www/saltelli && \
  sudo -u www-data wp cache flush --path=/var/www/saltelli"

# 2. Rollback theme files (se mergeato in main):
git revert <merge_commit_sha> -m 1
git push origin main

# 3. Re-rsync theme files su staging
rsync -avz --rsync-path='sudo rsync' wp-content/themes/saltelli/ deploy@178.62.207.50:/var/www/saltelli/wp-content/themes/saltelli/
```

Selective rollback (solo postmeta migrati, lasciando theme files Wave 4.7.fix.3):

```sh
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
  done"
```

Helper saltelli_page_field fa fallback automatico a saltelli_option, quindi anche dopo selective rollback il frontend continua a renderizzare correttamente.

---

*Migration log · Wave 4.7.fix.3 P3 · 2026-05-08 · staging completata, pre-migration DB backup intatto.*
