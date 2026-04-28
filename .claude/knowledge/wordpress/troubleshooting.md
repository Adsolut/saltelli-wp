# WordPress Troubleshooting Guide - {{SITE_NAME}}

## Common Issues and Solutions

### White Screen of Death (WSOD)

**Symptoms**: Blank white page, no error message

**Diagnosis**:
```bash
# Check PHP error log
./ssh_cmd.exp "tail -50 {{WP_PATH}}/wp-content/debug.log"

# Enable debug mode (if not already)
./ssh_cmd.exp "grep 'WP_DEBUG' {{WP_PATH}}/wp-config.php"
```

**Solutions**:
1. **Plugin conflict**: Disable all plugins
```bash
./ssh_cmd.exp "cd {{WP_PATH}} && wp plugin deactivate --all --allow-root"
```

2. **Theme issue**: Switch to default theme
```bash
./ssh_cmd.exp "cd {{WP_PATH}} && wp theme activate twentytwentythree --allow-root"
```

3. **Memory limit**: Increase PHP memory
```php
// In wp-config.php
define('WP_MEMORY_LIMIT', '256M');
```

### Homepage Sections Missing/Blank

**Symptoms**: Homepage sections show no content

**Cause**: {{CUSTOM_FIELDS_PLUGIN}} plugin deactivated or {{CUSTOM_FIELDS_PREFIX}}* fields missing

**Diagnosis**:
```bash
# Check {{CUSTOM_FIELDS_PLUGIN}} plugin
./ssh_cmd.exp "cd {{WP_PATH}} && wp plugin status meta-box --allow-root"

# Count {{CUSTOM_FIELDS_PREFIX}}* entries
./ssh_cmd.exp "cd {{WP_PATH}} && wp db query 'SELECT COUNT(*) FROM wp_postmeta WHERE meta_key LIKE \"{{CUSTOM_FIELDS_PREFIX}}%\"' --allow-root"
```

**Solutions**:
1. **Reactivate {{CUSTOM_FIELDS_PLUGIN}}**:
```bash
./ssh_cmd.exp "cd {{WP_PATH}} && wp plugin activate meta-box --allow-root"
```

2. **Restore from backup** (if {{CUSTOM_FIELDS_PREFIX}}* < 919):
```bash
./ssh_cmd.exp "cd {{WP_PATH}} && wp db import ~/backup-YYYYMMDD.sql --allow-root"
```

### 404 Errors on Shop/Product Pages

**Symptoms**: Shop or product pages return 404

**Cause**: Permalink structure issue or WPML slug conflict

**Solutions**:
1. **Flush permalinks**:
```bash
./ssh_cmd.exp "cd {{WP_PATH}} && wp rewrite flush --allow-root"
```

2. **Check WooCommerce permalinks**:
```bash
./ssh_cmd.exp "cd {{WP_PATH}} && wp option get woocommerce_permalink_structure --allow-root"
```

3. **Verify WPML shop slugs**:
   - Italian: /negozio/
   - English: /shop/

### Language Switcher Not Working

**Symptoms**: WPML language switcher not showing or not switching

**Solutions**:
1. **Check WPML status**:
```bash
./ssh_cmd.exp "cd {{WP_PATH}} && wp plugin status sitepress-multilingual-cms --allow-root"
```

2. **Verify languages**:
```bash
./ssh_cmd.exp "cd {{WP_PATH}} && wp db query 'SELECT * FROM wp_icl_languages WHERE active=1' --allow-root"
```

3. **Clear WPML cache**:
```bash
./ssh_cmd.exp "cd {{WP_PATH}} && wp cache flush --allow-root"
```

### Slow Loading / Performance Issues

**Diagnosis**:
```bash
# Check database size
./ssh_cmd.exp "cd {{WP_PATH}} && wp db size --allow-root"

# Count autoloaded options
./ssh_cmd.exp "cd {{WP_PATH}} && wp db query 'SELECT COUNT(*), SUM(LENGTH(option_value)) FROM wp_options WHERE autoload=\"yes\"' --allow-root"

# Check transients
./ssh_cmd.exp "cd {{WP_PATH}} && wp transient list --allow-root | wc -l"
```

**Solutions**:
1. **Delete expired transients**:
```bash
./ssh_cmd.exp "cd {{WP_PATH}} && wp transient delete --expired --allow-root"
```

2. **Optimize database**:
```bash
./ssh_cmd.exp "cd {{WP_PATH}} && wp db optimize --allow-root"
```

3. **Purge LiteSpeed cache**:
```bash
./ssh_cmd.exp "cd {{WP_PATH}} && wp litespeed-purge all --allow-root"
```

### WooCommerce Checkout Not Working

**Symptoms**: Checkout page errors or payment failure

**Solutions**:
1. **Check payment gateway status**:
```bash
./ssh_cmd.exp "cd {{WP_PATH}} && wp wc payment_gateway list --allow-root"
```

2. **Review payment logs**:
```bash
./ssh_cmd.exp "tail -50 {{WP_PATH}}/wp-content/uploads/wc-logs/*stripe*.log"
```

3. **Verify SSL certificate**:
```bash
curl -I https://{{SITE_DOMAIN}}/
```

4. **Test in all languages**:
   - Italian checkout: https://{{SITE_DOMAIN}}/checkout/
   - English checkout: https://{{SITE_DOMAIN}}/en/checkout/

### Plugin Conflicts

**Diagnosis**:
1. **Disable all plugins**:
```bash
./ssh_cmd.exp "cd {{WP_PATH}} && wp plugin deactivate --all --allow-root"
```

2. **Enable one by one** to find conflict:
```bash
./ssh_cmd.exp "cd {{WP_PATH}} && wp plugin activate <plugin-slug> --allow-root"
```

**Common Conflicts**:
- **Caching plugins** vs LiteSpeed Cache (use only one)
- **SEO plugins** vs WPML (configure properly)
- **Form plugins** vs WooCommerce checkout

### Database Connection Errors

**Symptoms**: "Error establishing database connection"

**Diagnosis**:
```bash
# Check wp-config.php credentials
./ssh_cmd.exp "grep 'DB_' {{WP_PATH}}/wp-config.php | grep -v 'CHARSET\|COLLATE'"

# Test database connection
./ssh_cmd.exp "cd {{WP_PATH}} && wp db check --allow-root"
```

**Solutions**:
1. **Verify database credentials** in wp-config.php
2. **Check database server status**
3. **Restart database** if necessary

### PHP Errors / Warnings

**Common PHP 8.2 Warnings**:
- **Deprecated**: Dynamic properties
- **Deprecated**: Null values in non-nullable parameters

**Solution**:
1. **Update plugins** to latest versions
2. **Ignore cosmetic warnings** (not breaking site)
3. **Report to plugin authors** for fixes

### Session Expired / Login Issues

**Solutions**:
1. **Clear browser cookies**
2. **Check session configuration**:
```bash
./ssh_cmd.exp "php -i | grep session"
```

3. **Clear user sessions**:
```bash
./ssh_cmd.exp "cd {{WP_PATH}} && wp db query 'DELETE FROM wp_usermeta WHERE meta_key LIKE \"session_%\"' --allow-root"
```

## Emergency Procedures

### Restore from Backup
```bash
# 1. List backups
./ssh_cmd.exp "ls -lh ~/backup-*.sql"

# 2. Import backup
./ssh_cmd.exp "cd {{WP_PATH}} && wp db import ~/backup-YYYYMMDD.sql --allow-root"

# 3. Verify restoration
./ssh_cmd.exp "cd {{WP_PATH}} && wp db check --allow-root"

# 4. Clear caches
./ssh_cmd.exp "cd {{WP_PATH}} && wp cache flush --allow-root && wp litespeed-purge all --allow-root"
```

### Enable Debug Mode
```php
// Add to wp-config.php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

### Disable All Plugins (Emergency)
```bash
# Rename plugins folder
./ssh_cmd.exp "mv {{WP_PATH}}/wp-content/plugins {{WP_PATH}}/wp-content/plugins.disabled"
```

## Useful Diagnostic Commands

```bash
# WordPress version
wp core version --allow-root

# PHP version
php -v

# Installed plugins
wp plugin list --allow-root

# Active theme
wp theme list --status=active --allow-root

# Database size
wp db size --allow-root

# Check disk space
df -h /home/torrest1

# Recent errors
tail -100 wp-content/debug.log

# Server load
uptime
```

## References
- WordPress Debugging: https://wordpress.org/documentation/article/debugging-in-wordpress/
- WP-CLI Commands: https://wp-cli.org/commands/
- `.claude/skills/wordpress-diagnostics/`
