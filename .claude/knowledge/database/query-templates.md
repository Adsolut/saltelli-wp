# Common Database Queries - {{SITE_NAME}}

## WordPress Content

### Count Posts by Type
```sql
SELECT post_type, COUNT(*) as count
FROM wp_posts
WHERE post_status = 'publish'
GROUP BY post_type
ORDER BY count DESC
```

### Find Posts by Date Range
```sql
SELECT ID, post_title, post_date
FROM wp_posts
WHERE post_type = 'post'
AND post_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
ORDER BY post_date DESC
```

## {{CUSTOM_FIELDS_PLUGIN}} Queries

### Verify {{CUSTOM_FIELDS_PLUGIN}} Entry Count
```sql
SELECT COUNT(*) as meta_box_count
FROM wp_postmeta
WHERE meta_key LIKE '{{CUSTOM_FIELDS_PREFIX}}%'
-- Expected: 919
```

### List All {{CUSTOM_FIELDS_PREFIX}}* Fields
```sql
SELECT DISTINCT meta_key, COUNT(*) as usage
FROM wp_postmeta
WHERE meta_key LIKE '{{CUSTOM_FIELDS_PREFIX}}%'
GROUP BY meta_key
ORDER BY usage DESC
```

### Find Posts Using {{CUSTOM_FIELDS_PLUGIN}}
```sql
SELECT DISTINCT post_id
FROM wp_postmeta
WHERE meta_key LIKE '{{CUSTOM_FIELDS_PREFIX}}%'
```

## WooCommerce Queries

### Count Products
```sql
SELECT COUNT(*) as product_count
FROM wp_posts
WHERE post_type = 'product'
AND post_status = 'publish'
```

### Products Without Prices
```sql
SELECT p.ID, p.post_title
FROM wp_posts p
INNER JOIN wp_postmeta pm ON p.ID = pm.post_id
WHERE p.post_type = 'product'
AND pm.meta_key = '_price'
AND (pm.meta_value = '' OR pm.meta_value IS NULL)
```

### Orders by Status
```sql
SELECT post_status, COUNT(*) as count
FROM wp_posts
WHERE post_type = 'shop_order'
GROUP BY post_status
```

### Payment Methods Used
```sql
SELECT meta_value as payment_method, COUNT(*) as count
FROM wp_postmeta
WHERE meta_key = '_payment_method'
GROUP BY meta_value
ORDER BY count DESC
```

## {{BOOKING_PLUGIN}} Queries

### Total Bookings
```sql
SELECT COUNT(*) as booking_count
FROM wp_posts
WHERE post_type = 'yith_booking'
-- Expected: 3649
```

### Bookings by Status
```sql
SELECT post_status, COUNT(*) as count
FROM wp_posts
WHERE post_type = 'yith_booking'
GROUP BY post_status
ORDER BY count DESC
```

### Recent Bookings
```sql
SELECT ID, post_title, post_date, post_status
FROM wp_posts
WHERE post_type = 'yith_booking'
ORDER BY post_date DESC
LIMIT 10
```

### Bookings by Product
```sql
SELECT pm.meta_value as product_id, COUNT(*) as booking_count
FROM wp_postmeta pm
INNER JOIN wp_posts p ON pm.post_id = p.ID
WHERE p.post_type = 'yith_booking'
AND pm.meta_key = '_yith_booking_product_id'
GROUP BY pm.meta_value
ORDER BY booking_count DESC
```

## WPML Queries

### Translations by Language
```sql
SELECT language_code, COUNT(*) as count
FROM wp_icl_translations
GROUP BY language_code
ORDER BY count DESC
```

### Product Translations
```sql
SELECT language_code, COUNT(*) as product_count
FROM wp_icl_translations
WHERE element_type = 'post_product'
GROUP BY language_code
```

### Untranslated Products
```sql
SELECT p.ID, p.post_title
FROM wp_posts p
LEFT JOIN wp_icl_translations t ON p.ID = t.element_id
WHERE p.post_type = 'product'
AND p.post_status = 'publish'
AND t.element_id IS NULL
LIMIT 10
```

### Missing Translation in Language
```sql
SELECT t1.element_id, p.post_title, t1.language_code
FROM wp_icl_translations t1
INNER JOIN wp_posts p ON t1.element_id = p.ID
WHERE t1.element_type = 'post_page'
AND NOT EXISTS (
    SELECT 1 FROM wp_icl_translations t2
    WHERE t2.trid = t1.trid
    AND t2.language_code = 'en'
)
AND t1.language_code = 'it'
LIMIT 10
```

## WordPress Options

### Large Options (Autoloaded)
```sql
SELECT option_name, LENGTH(option_value) as size
FROM wp_options
WHERE autoload = 'yes'
ORDER BY size DESC
LIMIT 20
```

### WPML Settings
```sql
SELECT option_name, option_value
FROM wp_options
WHERE option_name LIKE '%wpml%'
LIMIT 10
```

### WooCommerce Settings
```sql
SELECT option_name
FROM wp_options
WHERE option_name LIKE 'woocommerce_%'
ORDER BY option_name
```

## User Queries

### Administrator Accounts
```sql
SELECT u.user_login, u.user_email
FROM wp_users u
WHERE u.ID IN (
    SELECT user_id FROM wp_usermeta
    WHERE meta_key = 'wp_capabilities'
    AND meta_value LIKE '%administrator%'
)
```

### User Registration Dates
```sql
SELECT user_login, user_email, user_registered
FROM wp_users
ORDER BY user_registered DESC
LIMIT 10
```

## Database Maintenance

### Orphaned Post Meta
```sql
SELECT COUNT(*) as orphaned_count
FROM wp_postmeta pm
LEFT JOIN wp_posts p ON pm.post_id = p.ID
WHERE p.ID IS NULL
```

### Expired Transients
```sql
SELECT COUNT(*) as expired_transients
FROM wp_options
WHERE option_name LIKE '_transient_timeout_%'
AND option_value < UNIX_TIMESTAMP()
```

### Database Size
```sql
SELECT
    table_name AS 'Table',
    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
FROM information_schema.TABLES
WHERE table_schema = DATABASE()
ORDER BY (data_length + index_length) DESC
```

## References
- Schema diagram: `.claude/knowledge/database/schema-diagram.md`
- Query execution skill: `.claude/skills/database-operations/query-execution.md`
