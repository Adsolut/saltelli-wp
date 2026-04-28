# Security Hardening Checklist - {{SITE_NAME}}

## WordPress Core Security

- \[x\] WordPress updated to latest version (6.8.3)
- \[x\] PHP updated to supported version (8.2.29)
- \[x\] Unused themes deleted
- \[x\] File permissions set correctly (755/644)
- \[x\] wp-config.php secured (640 permissions)
- \[x\] Database prefix not default (wp\_)
- \[x\] Debug mode enabled but log hidden from public
- \[x\] File editing disabled in admin

### wp-config.php Security

```php
// Disable file editing
define('DISALLOW_FILE_EDIT', true);

// Security keys (all 8 defined)
define('AUTH_KEY', '...');
define('SECURE_AUTH_KEY', '...');
// ... (6 more)

// Database prefix (custom, not wp_)
$table_prefix = 'wp_';
```

## User & Access Security

- \[x\] Strong admin passwords
- \[x\] No admin username = "admin"
- \[x\] Limited admin accounts
- \[x\] SSH key-based authentication
- \[x\] No FTP/cPanel access (SSH only)

### SSH Security

```bash
# SSH key location
/Users/aldosantoro/Desktop/torres-wp/torres_ssh_key

# Connection
ssh -i torres_ssh_key torrest1@86.105.14.11
```

## Plugin & Theme Security

- \[x\] All plugins from trusted sources
- \[x\] Unused plugins removed (47 → 37)
- \[x\] Premium plugins updated manually
- \[x\] Plugin checksums verified
- \[x\] No nulled/pirated plugins

## Database Security

- \[x\] Regular backups (before all changes)
- \[x\] Database credentials in wp-config.php (not hardcoded)
- \[x\] MySQL user has minimal permissions
- \[x\] Database optimization regular

### Backup Commands

```bash
# Create backup
wp db export ~/backup-$(date +%Y%m%d).sql --allow-root

# Verify backup
ls -lh ~/backup-*.sql
```

## SSL/HTTPS Security

- \[x\] Valid SSL certificate
- \[x\] HTTPS enforced
- \[x\] Mixed content resolved
- \[x\] HSTS enabled

### Verify SSL

```bash
curl -I https://{{SITE_DOMAIN}}/
```

## File Security

- \[x\] No PHP files in uploads/ directory
- \[x\] .htaccess configured
- \[x\] Directory listing disabled
- \[x\] Sensitive files not accessible

### File Permission Check

```bash
# Find files with 777 permissions (should be none)
find {{WP_PATH}} -type f -perm 0777

# Find directories with 777 (should be none)
find {{WP_PATH}} -type d -perm 0777
```

## Login Security

- \[x\] Strong password policy
- \[x\] No user enumeration
- \[x\] Admin area not exposed

## Content Security

- \[x\] User-submitted content sanitized
- \[x\] Output escaped properly
- \[x\] CSRF tokens used (nonces)

## GDPR & Privacy

- \[x\] Privacy policy (4 languages)
- \[x\] Cookie consent (Iubenda)
- \[x\] Data export capability
- \[x\] Data erasure capability
- \[x\] SSL for all forms

## Monitoring

- \[x\] Error logging enabled
- \[x\] Debug log monitored
- \[ \] Automated security scans (optional)

### Check Logs

```bash
tail -100 {{WP_PATH}}/wp-content/debug.log
```

## Backup & Recovery

- \[x\] Database backups before changes
- \[x\] Full site backups monthly
- \[x\] Backups stored offsite
- \[x\] Recovery procedure tested

## Server Security (Hosting Provider)

- \[x\] Firewall active
- \[x\] DDoS protection
- \[x\] Server software updated
- \[x\] LiteSpeed server optimized

## Pending Hardening (Optional)

- \[ \] Two-factor authentication (2FA)
- \[ \] Security plugin (Wordfence/Sucuri)
- \[ \] Automated malware scanning
- \[ \] Web Application Firewall (WAF)
- \[ \] Rate limiting for login attempts

## References

- Access control skill: `.claude/skills/security-hardening/access-control.md`
- Data protection skill: `.claude/skills/security-hardening/data-protection.md`
- Vulnerability assessment: `.claude/knowledge/security/known-vulnerabilities.md`
