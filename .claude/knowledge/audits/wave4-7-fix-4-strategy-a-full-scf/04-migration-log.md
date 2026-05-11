# Phase 3 — Migration log

## Script

`wp-content/themes/saltelli/inc/migrations/wave4-7-fix-4-postcontent-to-scf.php`

Esecuzione tramite `wp eval-file` su staging droplet.

## Run #1 (2026-05-10)

```
=== Wave 4.7.fix.4 Migration Report ===
Backed up (post_content → _legacy_post_content_backup): 7
Backup skipped (already present or post_content empty):  0
Migrated (post_content → SCF body_content):              1
Migration skipped (SCF already populated or empty):       0
Errors:                                                   0

[Detail log]
  BACKUP: page 23 (contatti) → _legacy_post_content_backup (899 chars saved)
  BACKUP: page 2708 (domande-frequenti) → _legacy_post_content_backup (1117 chars saved)
  BACKUP: page 2709 (guide-gratuite) → _legacy_post_content_backup (677 chars saved)
  BACKUP: page 2712 (come-lavoriamo) → _legacy_post_content_backup (1432 chars saved)
  BACKUP: page 2711 (prima-consulenza) → _legacy_post_content_backup (909 chars saved)
  BACKUP: page 372 (lavora-con-noi) → _legacy_post_content_backup (1251 chars saved)
  BACKUP: page 2713 (richiedi-preventivo) → _legacy_post_content_backup (879 chars saved)
  MIGRATION: page 2713 (richiedi-preventivo) → SCF body_content (879 chars), shadow _body_content = field_info_body_content
Success: Migration complete: 7 backed up, 1 migrated, 0 skipped backup, 0 skipped migration, 0 errors.
```

## Run #2 (idempotency check, 2026-05-10)

```
=== Wave 4.7.fix.4 Migration Report ===
Backed up (post_content → _legacy_post_content_backup): 0
Backup skipped (already present or post_content empty):  7
Migrated (post_content → SCF body_content):              0
Migration skipped (SCF already populated or empty):       1
Errors:                                                   0

Success: Migration complete: 0 backed up, 0 migrated, 7 skipped backup, 1 skipped migration, 0 errors.
```

**Idempotency confirmed**: re-run zero side-effects.

## Verifica post-migration

### Backup _legacy_post_content_backup (Page 23)

```
$ wp post meta get 23 _legacy_post_content_backup
<p>Hai bisogno di aiuto?</p>		
					<h2>Contattaci</h2>				
					<h2>Chiedi qualsiasi cosa.<br> In qualsiasi momento</h2>				
		<p>Siamo situati a Napoli in una zona facile da raggiungere.</p>
		[...]
```

### body_content SCF (Page 2713)

```
$ wp post meta get 2713 body_content
<p class="sl-page__lede">Per pratiche già definite o per fattibilità preliminare, ricevi un preventivo personalizzato entro 48 ore lavorative.</p>
<h2>Come funziona</h2>
<ol><li><strong>Compili il modulo</strong> — area di pratica, sintesi del caso, eventuali documenti</li>...
```

### Shadow reference (Page 2713)

```
$ wp post meta get 2713 _body_content
field_info_body_content
```

## Frontend smoke test post-migration (pre-Phase 5 cleanup)

| URL | HTTP | Note |
|---|---|---|
| `/contatti/` | 200 | OK |
| `/risorse/domande-frequenti/` | 200 | OK |
| `/risorse/guide-gratuite/` | 200 | OK |
| `/costi-e-consulenze/come-lavoriamo/` | 200 | OK |
| `/costi-e-consulenze/prima-consulenza/` | 200 | OK |
| `/lavora-con-noi/` | 301 → `/contatti/lavora-con-noi/` | OK (parent hierarchy redirect, atteso) |
| `/costi-e-consulenze/richiedi-preventivo/` | 200 | OK + content "Come funziona" rendered |

Per `/costi-e-consulenze/richiedi-preventivo/`: pre-migration il content veniva da `the_content()` fallback. Post-migration viene da SCF `body_content`. Markup HTML identico (post_content era già well-formed HTML).

## Backup pre-migration disponibili

- DB snapshot: `~/backups/wave4-7-fix-4-pre-migration/db-pre-fix4-20260510-2016.sql` (59 MB)
- Pages snapshot: `~/backups/wave4-7-fix-4-pre-migration/pages-snapshot.csv` (974 B)
- Per-page postmeta backup: `_legacy_post_content_backup` su ognuna delle 7 Pages (recoverable via wp post meta delete e re-set su post_content campo).

## Rollback per Phase 3

Se serve revertire post-Phase 3:

```sh
# Delete migrated SCF body_content per Page 2713
ssh deploy@178.62.207.50 "sudo -u www-data wp post meta delete 2713 body_content --path=/var/www/saltelli && \
  sudo -u www-data wp post meta delete 2713 _body_content --path=/var/www/saltelli"

# Backups _legacy_post_content_backup possono restare (zero footprint frontend).
# Per restore completo del DB:
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp db import ~/backups/wave4-7-fix-4-pre-migration/db-pre-fix4-20260510-2016.sql --path=/var/www/saltelli && \
  sudo -u www-data wp cache flush --path=/var/www/saltelli"
```

---

*Migration log Phase 3 · Wave 4.7.fix.4 · 2026-05-10 · ready for Phase 4 template refactor.*
