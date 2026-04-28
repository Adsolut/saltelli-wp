# Known Vulnerabilities - {{SITE_NAME}}

## Current Security Status

### WordPress Core 6.8.3
- **Status**: Up to date
- **Known CVEs**: None (latest version)
- **Last Security Update**: 2025

### PHP 8.2.29
- **Status**: Maintained version
- **Known CVEs**: None critical
- **Compatibility**: 6 plugins show deprecation warnings (cosmetic, non-breaking)

## Plugin Vulnerability Status

### Critical Plugins (Verified Secure)
1. **Meta Box**: Latest, no known vulnerabilities
2. **WPML 4.6.14**: Latest, no critical CVEs
3. **WooCommerce 10.3.5**: Latest, no known vulnerabilities
4. **YITH Booking**: Latest premium version
5. **LiteSpeed Cache 6.4.1**: Latest, secure

### Plugins with Deprecation Warnings (Non-Critical)
These plugins show PHP 8.2 deprecation warnings but are NOT security vulnerabilities:

1. **LayerSlider WP** - Dynamic property warnings
2. **Revolution Slider** - Null parameter warnings
3. **WPBakery Page Builder** - Deprecated function calls
4. **Contact Form 7** - Minor warnings
5. **Elementor** - Dynamic property warnings
6. **Wordfence Security** - PHP 8.2 compatibility notes

**Impact**: Cosmetic warnings in debug log, not breaking functionality

## Historical Vulnerabilities (Resolved)

### WordPress 5.3.4 (Previous Version)
- **Multiple CVEs**: Patched by upgrading to 6.8.3 (Phase 1)

### WooCommerce 4.6.0 (Previous Version)
- **Security issues**: Resolved by upgrade to 10.3.5 (Phase 2)

## Server Security

### LiteSpeed Server
- **Version**: Current
- **Security**: Managed by hosting provider
- **Firewall**: Active

### SSH Access
- **Method**: Key-based authentication
- **Key**: `/Users/aldosantoro/Desktop/torres-wp/torres_ssh_key`
- **Passphrase**: Protected
- **User**: torrest1

## File Integrity Checks

### WordPress Core
```bash
wp core verify-checksums --allow-root
```
**Expected**: No issues

### Plugin Checksums
```bash
wp plugin verify-checksums --all --allow-root
```

## Security Scan Results

### Last Scan: 2025-12-03 (Phase 10 completion)

**Findings**:
- ✅ Core files intact
- ✅ No malicious files in uploads/
- ✅ File permissions correct
- ✅ No unknown admin users
- ✅ SSL certificate valid

## Monitoring Recommendations

1. **Weekly**: Check plugin updates
2. **Monthly**: Run full security scan
3. **After updates**: Verify checksums
4. **Continuous**: Monitor error logs

## References
- Security scanner skill: `.claude/skills/security-hardening/vulnerability-assessment.md`
- Monitoring skill: `.claude/skills/security-hardening/monitoring.md`
