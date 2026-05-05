# WordPress API Reference - {{SITE_NAME}}

## REST API

### Base URL
```
https://{{SITE_DOMAIN}}/wp-json/
```

### Authentication
**Application Password**:
- User: l.dellavolpe@adsolut.it
- Password: HQte ZPwo GcKQ LApx admT tXES

### Common Endpoints

**Posts**:
```bash
# List posts
GET /wp/v2/posts

# Single post
GET /wp/v2/posts/{id}
```

**Products** (WooCommerce):
```bash
# List products
GET /wc/v3/products

# Single product
GET /wc/v3/products/{id}
```

**Orders** (WooCommerce):
```bash
# List orders
GET /wc/v3/orders

# Single order
GET /wc/v3/orders/{id}
```

## WP-CLI Commands

### Core Operations
```bash
# Version check
wp core version --allow-root

# Update WordPress
wp core update --allow-root
wp core update-db --allow-root

# Verify checksums
wp core verify-checksums --allow-root
```

### Plugin Management
```bash
# List plugins
wp plugin list --allow-root

# Activate/Deactivate
wp plugin activate <plugin-slug> --allow-root
wp plugin deactivate <plugin-slug> --allow-root

# Update plugins
wp plugin update --all --allow-root
wp plugin update <plugin-slug> --allow-root
```

### Database Operations
```bash
# Export database
wp db export ~/backup-$(date +%Y%m%d).sql --allow-root

# Import database
wp db import ~/backup.sql --allow-root

# Custom query
wp db query 'SELECT COUNT(*) FROM wp_posts' --allow-root

# Optimize database
wp db optimize --allow-root
```

### Cache Management
```bash
# Flush WordPress object cache
wp cache flush --allow-root

# Purge LiteSpeed cache
wp litespeed-purge all --allow-root

# Delete transients
wp transient delete --expired --allow-root
```

### Post/Page Operations
```bash
# List posts
wp post list --post_type=post --allow-root

# List products
wp post list --post_type=product --allow-root

# Create post
wp post create --post_title='Title' --post_status=publish --allow-root

# Get meta
wp post meta get <POST_ID> <META_KEY> --allow-root

# Update meta
wp post meta update <POST_ID> <META_KEY> '<VALUE>' --allow-root
```

### User Management
```bash
# List users
wp user list --allow-root

# Create user
wp user create <username> <email> --role=editor --allow-root

# Update user
wp user update <username> --user_pass='<password>' --allow-root

# Delete user
wp user delete <username> --reassign=<id> --allow-root
```

### Search-Replace
```bash
# Dry run
wp search-replace 'old-url.com' 'new-url.com' --dry-run --allow-root

# Execute
wp search-replace 'old-url.com' '{{SITE_DOMAIN}}' --all-tables --allow-root
```

## {{CUSTOM_FIELDS_PLUGIN}} API

### Get Field Value
```php
// Simple field
$value = rwmb_meta('{{CUSTOM_FIELDS_PREFIX}}field_id');

// Image field
$image = rwmb_meta('{{CUSTOM_FIELDS_PREFIX}}image_field', array('size' => 'full'));
echo '<img src="' . $image['url'] . '">';

// Multiple values (checkbox, select multiple)
$values = rwmb_meta('{{CUSTOM_FIELDS_PREFIX}}multi_field');
foreach ($values as $value) {
    echo $value;
}
```

### Register {{CUSTOM_FIELDS_PLUGIN}}
```php
add_filter('rwmb_meta_boxes', 'jtlb_register_meta_boxes');

function jtlb_register_meta_boxes($meta_boxes) {
    $meta_boxes[] = array(
        'id' => '{{CUSTOM_FIELDS_PREFIX}}hero',
        'title' => 'Hero Section',
        'post_types' => array('page'),
        'fields' => array(
            array(
                'id' => '{{CUSTOM_FIELDS_PREFIX}}hero_title',
                'name' => 'Hero Title',
                'type' => 'text',
            ),
        ),
    );
    return $meta_boxes;
}
```

## WPML API

### Get Current Language
```php
// Current language code
$current_lang = apply_filters('wpml_current_language', NULL);
// Returns: 'it', 'en', 'es', 'fr'
```

### Get Translated URL
```php
// Get home URL in current language
$home_url = apply_filters('wpml_home_url', home_url());

// Get specific page in specific language
$page_id = 123;
$lang = 'en';
$translated_id = apply_filters('wpml_object_id', $page_id, 'page', FALSE, $lang);
```

### Get All Languages
```php
$languages = apply_filters('wpml_active_languages', NULL);
foreach ($languages as $lang) {
    echo $lang['code']; // it, en, es, fr
    echo $lang['native_name']; // Italiano, English, Español, Français
    echo $lang['url']; // Language-specific URL
}
```

## WooCommerce API

### Product Functions
```php
// Get product
$product = wc_get_product($product_id);

// Product data
$product->get_name();
$product->get_price();
$product->get_price_html();
$product->get_stock_status();
$product->is_type('booking'); // YITH Booking check

// Categories
$terms = get_the_terms($product_id, 'product_cat');
```

### Cart Functions
```php
// Add to cart
WC()->cart->add_to_cart($product_id, $quantity);

// Get cart
WC()->cart->get_cart();

// Cart total
WC()->cart->get_cart_total();
```

### Order Functions
```php
// Get order
$order = wc_get_order($order_id);

// Order data
$order->get_status();
$order->get_total();
$order->get_items();
$order->get_billing_email();
```

## Hooks Reference

### Actions
```php
// After theme setup
add_action('after_setup_theme', 'jtlb_setup');

// Enqueue scripts
add_action('wp_enqueue_scripts', 'jtlb_enqueue_styles');

// WooCommerce hooks
add_action('woocommerce_before_main_content', 'jtlb_wrapper_start');
add_action('woocommerce_after_main_content', 'jtlb_wrapper_end');

// Save post
add_action('save_post', 'jtlb_save_meta');
```

### Filters
```php
// Modify excerpt length
add_filter('excerpt_length', 'jtlb_excerpt_length');

// Modify WooCommerce product query
add_filter('woocommerce_product_query', 'jtlb_product_query');

// WPML current language
add_filter('wpml_current_language', function($lang) {
    // Modify if needed
    return $lang;
});
```

## Custom Post Types (YITH Booking)

### Query Bookings
```php
$args = array(
    'post_type' => 'yith_booking',
    'posts_per_page' => -1,
    'post_status' => 'any',
);
$bookings = new WP_Query($args);
```

### Booking Meta
```php
// Get booking data
$booking_id = 123;
$product_id = get_post_meta($booking_id, '_yith_booking_product_id', true);
$user_name = get_post_meta($booking_id, '_yith_booking_user_name', true);
$user_email = get_post_meta($booking_id, '_yith_booking_user_email', true);
```

## References
- WordPress REST API: https://developer.wordpress.org/rest-api/
- WP-CLI: https://wp-cli.org/
- {{CUSTOM_FIELDS_PLUGIN}}: https://docs.metabox.io/
- WPML: https://wpml.org/documentation/
- WooCommerce: https://woocommerce.github.io/code-reference/
