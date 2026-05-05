# Database Backup Strategy - {{SITE_NAME}}

## Backup Schedule

### Before All Changes (REQUIRED)

```bash
wp db export ~/backup-$(date +%Y%m%d-%H%M%S).sql --allow-root
```

**When to backup**:

- Before WordPress core updates
- Before plugin updates
- Before database queries (UPDATE/DELETE)
- Before theme changes
- Before any risky operations

### Regular Backups (Recommended)

- **Daily**: Automated via hosting (if available)
- **Before phases**: Manual backup before each project phase
- **Monthly**: Full site backup (database + files)

## Backup Commands

### Standard Backup

```bash
# Database only
```
```
```

```
```

```

./ssh_cmd.exp "cd /home/torrest1/public_html && wp db export \~/backup-$(date +%Y%m%d).sql --allow-root"
```

### Compressed Backup

```bash
# Export and compress
./ssh_cmd.exp "cd /home/torrest1/public_html && wp db export ~/backup-$(date +%Y%m%d).sql --allow-root && gzip ~/backup-$(date +%Y%m%d).sql"
```

### Specific Tables

```bash
# Backup only specific tables
./ssh_cmd.exp "cd /home/torrest1/public_html && wp db export --tables=wp_posts,wp_postmeta ~/backup-content.sql --allow-root"
```

## Backup Verification

### Check Backup File

```bash
# List recent backups
./ssh_cmd.exp "ls -lh ~/backup-*.sql* | tail -5"

# Verify backup integrity (check first 20 lines)
./ssh_cmd.exp "head -20 ~/backup-YYYYMMDD-HHMMSS.sql"
```

**Expected**: Should show SQL statements like CREATE TABLE

### Backup Size

```bash
./ssh_cmd.exp "ls -lh ~/backup-*.sql"
```

**Expected**: \~400-500MB (uncompressed)

## Backup Storage

### Local Storage (Server)

**Location**: `/home/torrest1/`**Retention**: Keep last 5 backups manually

### Offsite Storage (Local Machine)

```bash
# Download backup
scp -i /Users/aldosantoro/Desktop/torres-wp/torres_ssh_key torrest1@86.105.14.11:~/backup-YYYYMMDD.sql.gz ~/Desktop/torres-wp/backups/
```

**Location**: `~/Desktop/torres-wp/backups/`**Retention**: Keep critical backups (pre-phase, pre-major-change)

## Restoration Procedure

### Restore from Backup

```bash
# 1. Create safety backup first
./ssh_cmd.exp "cd /home/torrest1/public_html && wp db export ~/pre-restore-backup.sql --allow-root"

# 2. Import backup
./ssh_cmd.exp "cd /home/torrest1/public_html && wp db import ~/backup-YYYYMMDD-HHMMSS.sql --allow-root"

# 3. Verify restoration
./ssh_cmd.exp "cd /home/torrest1/public_html && wp db check --allow-root"

# 4. Verify {{CUSTOM_FIELDS_PLUGIN}} entries
./ssh_cmd.exp "cd /home/torrest1/public_html && wp db query 'SELECT COUNT(*) FROM wp_postmeta WHERE meta_key LIKE \"{{CUSTOM_FIELDS_PREFIX}}%\"' --allow-root"
# Must return: 919

# 5. Clear caches
./ssh_cmd.exp "cd /home/torrest1/public_html && wp cache flush --allow-root && wp litespeed-purge all --allow-root"
```

## Backup Encryption (Recommended)

### Encrypt Backup

```bash
# GPG encryption
./ssh_cmd.exp "gpg --symmetric --cipher-algo AES256 ~/backup-YYYYMMDD.sql"
# Creates: backup-YYYYMMDD.sql.gpg

# Delete unencrypted
./ssh_cmd.exp "rm ~/backup-YYYYMMDD.sql"
```

### Decrypt Backup

```bash
gpg --decrypt backup-YYYYMMDD.sql.gpg > backup-YYYYMMDD.sql
```

## Critical Backup Points

### Before Removal of {{CUSTOM_FIELDS_PLUGIN}} (NEVER!)

If accidentally attempted:

```bash
# Immediate backup
wp db export ~/EMERGENCY-backup-$(date +%Y%m%d-%H%M%S).sql --allow-root
```

### Before Database Modifications

```bash
# Before any UPDATE/DELETE queries
wp db export ~/backup-before-query-$(date +%Y%m%d-%H%M%S).sql --allow-root
```

### Before WordPress/Plugin Updates

```bash
# Pre-update backup
wp db export ~/backup-pre-update-$(date +%Y%m%d).sql --allow-root
```

## Backup Checklist

Before major changes, verify:

- \[ \] Database backup created
- \[ \] Backup file size reasonable (\~400-500MB)
- \[ \] Backup file contains SQL statements
- \[ \] Backup downloaded to local machine (for critical changes)
- \[ \] Backup filename includes date/time
- \[ \] Previous backups retained

## Rollback Procedures

### Quick Rollback

```bash
# Import most recent backup
./ssh_cmd.exp "cd /home/torrest1/public_html && wp db import ~/backup-YYYYMMDD-HHMMSS.sql --allow-root"
```

### Verify After Rollback

1. Check {{CUSTOM_FIELDS_PLUGIN}} entries = 919
2. Verify homepage loads
3. Test all 4 language homepages
4. Check WooCommerce products
5. Clear all caches

## Backup Cleanup

### Remove Old Backups

```bash
# List all backups
./ssh_cmd.exp "ls -lh ~/backup-*.sql*"

# Delete specific backup
./ssh_cmd.exp "rm ~/backup-YYYYMMDD.sql"

# Keep only last 5 backups (manual)
```

## References

- Backup-restore skill: `.claude/skills/database-operations/backup-restore.md`
- Emergency procedures: `.claude/knowledge/wordpress/troubleshooting.md`
