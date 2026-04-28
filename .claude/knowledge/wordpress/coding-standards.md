# WordPress Coding Standards - {{SITE_NAME}}

## WordPress PHP Standards

### Naming Conventions

**Functions**:

```php
// Prefix all custom functions
function jtlb_custom_function() {
    // ...
}

// WordPress hooks
add_action('wp_enqueue_scripts', 'jtlb_enqueue_styles');
```

**Variables**:

```php
// Snake_case for WordPress
$post_id = get_the_ID();
$meta_value = get_post_meta($post_id, 'key', true);
```

### Template Tags

**Escaping Output**:

```php
// Escape HTML
echo esc_html($variable);

// Escape attributes
echo '<a href="' . esc_url($link) . '">';

// Escape translation
echo esc_html__('Text', 'textdomain');
```

**Translation**:

```php
```

// Simple translation \_\_('Text', '{{THEME_NAME}}');

// Translation with echo \_e('Text', '{{THEME_NAME}}');

// Plural translation \_n('Singular', 'Plural', $count, '{{THEME_NAME}}');

```

### {{CUSTOM_FIELDS_PLUGIN}} Usage

**Retrieve Field Values**:
```php
// Get {{CUSTOM_FIELDS_PLUGIN}} field (WPML-compatible)
$hero_title = rwmb_meta('{{CUSTOM_FIELDS_PREFIX}}hero_title');

// Check if value exists
if (!empty($hero_title)) {
    echo '<h1>' . esc_html($hero_title) . '</h1>';
}

// Get image field
$hero_image = rwmb_meta('{{CUSTOM_FIELDS_PREFIX}}hero_image', array('size' => 'full'));
if ($hero_image) {
    echo '<img src="' . esc_url($hero_image['url']) . '">';
}
```

### WooCommerce Standards

**Product Data**:

```php
// Get product
$product = wc_get_product($product_id);

// Product title
echo $product->get_name();

// Product price
echo $product->get_price_html();

// Check if bookable (YITH)
if ($product->is_type('booking')) {
    // Bookable product logic
}
```

## PHP 8.2 Compatibility

### Dynamic Properties (Deprecated)

**Avoid**:

```php
$obj->new_property = 'value'; // Deprecated in PHP 8.2
```

**Use**:

```php
class MyClass {
    public $new_property;
}
$obj = new MyClass();
$obj->new_property = 'value'; // OK
```

### Null Handling

**Check before use**:

```php
// Good
if (!empty($variable)) {
    echo $variable;
}

// Or with null coalescing
echo $variable ?? 'default';
```

## Child Theme Patterns

### Enqueue Parent & Child Styles

**functions.php**:

```php
function jtlb_enqueue_styles() {
    // Parent theme stylesheet
    wp_enqueue_style(
        'parent-style',
        get_template_directory_uri() . '/style.css'
    );

    // Child theme stylesheet
    wp_enqueue_style(
        'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array('parent-style')
    );

    // Custom shop.css (WooCommerce)
    if (is_product()) {
        wp_enqueue_style(
            'shop-css',
            get_stylesheet_directory_uri() . '/assets/css/shop.css',
            array('child-style'),
            '1.0.4'
        );
    }
}
add_action('wp_enqueue_scripts', 'jtlb_enqueue_styles');
```

### Template Overrides

**Check before output**:

```php
if (have_posts()) :
    while (have_posts()) : the_post();

        // Get {{CUSTOM_FIELDS_PLUGIN}} value
        $custom_field = rwmb_meta('{{CUSTOM_FIELDS_PREFIX}}field_name');

        // Output with escaping
        if (!empty($custom_field)) {
            echo '<div>' . esc_html($custom_field) . '</div>';
        }

    endwhile;
endif;
```

## WPML Compatibility

### Translation-Ready Strings

```php
// Theme strings
echo __('Book Now', '{{THEME_NAME}}');

// Dynamic content (use WPML)
// Products, pages, posts auto-translated via WPML

// URLs
$current_lang = ICL_LANGUAGE_CODE; // 'it', 'en', 'es', 'fr'
$home_url = apply_filters('wpml_home_url', home_url());
```

### Language-Specific Logic

```php
// Get current language
$current_lang = apply_filters('wpml_current_language', NULL);

if ($current_lang === 'it') {
    // Italian-specific logic
} elseif ($current_lang === 'en') {
    // English-specific logic
}
```

## Security Best Practices

### Nonce Verification

```php
// Create nonce
wp_nonce_field('my_action', 'my_nonce');

// Verify nonce
if (!isset($_POST['my_nonce']) || !wp_verify_nonce($_POST['my_nonce'], 'my_action')) {
    wp_die('Security check failed');
}
```

### Sanitize Input

```php
// Text input
$text = sanitize_text_field($_POST['field']);

// Email
$email = sanitize_email($_POST['email']);

// URL
$url = esc_url_raw($_POST['url']);

// Integer
$id = absint($_POST['id']);
```

### Escape Output

```php
// HTML
echo esc_html($variable);

// Attributes
echo '<input value="' . esc_attr($value) . '">';

// URLs
echo '<a href="' . esc_url($link) . '">';

// JavaScript
echo '<script>var data = ' . wp_json_encode($data) . ';</script>';
```

## Performance Optimization

### Database Queries

```php
// Use WP_Query with caching
$args = array(
    'post_type' => 'product',
    'posts_per_page' => 10,
    'no_found_rows' => true, // Skip pagination count
    'update_post_meta_cache' => false, // Skip if not needed
    'update_post_term_cache' => false, // Skip if not needed
);
$query = new WP_Query($args);
```

### Conditional Loading

```php
// Only load on specific pages
if (is_product()) {
    wp_enqueue_style('shop-css', ...);
}

// Dequeue unused scripts
function jtlb_dequeue_scripts() {
    if (!is_woocommerce()) {
        wp_dequeue_style('woocommerce-layout');
    }
}
add_action('wp_print_styles', 'jtlb_dequeue_scripts', 100);
```

## Common Pitfalls

### ❌ Don't Do

```php
// Direct database access
global $wpdb;
$wpdb->query("DELETE FROM wp_posts WHERE ..."); // DANGEROUS

// Hardcoded URLs
echo '<a href="https://{{SITE_DOMAIN}}/page">'; // Breaks on language switch

// Unescaped output
echo $_POST['field']; // Security risk
```

### ✅ Do Instead

```php
// Use WordPress functions
wp_delete_post($post_id);

// Dynamic URLs
echo '<a href="' . esc_url(home_url('/page')) . '">';

// Sanitize and escape
echo esc_html(sanitize_text_field($_POST['field']));
```

## References

- WordPress Coding Standards: <https://developer.wordpress.org/coding-standards/>
- PHP 8 Compatibility: <https://www.php.net/manual/en/migration82.php>
- WPML Documentation: <https://wpml.org/documentation/>
