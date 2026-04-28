# Theme Structure - {{THEME_NAME}} (Child) + {{PARENT_THEME}} (Parent)

## Active Themes

### Child Theme: {{THEME_NAME}}
- **Template**: {{PARENT_THEME}} (parent)
- **Version**: Custom
- **Type**: Child theme
- **Bootstrap**: Version 4
- **{{CUSTOM_FIELDS_PLUGIN}} Integration**: 17 rwmb_meta() calls

### Parent Theme: {{PARENT_THEME}}
- **Type**: Parent theme
- **Framework**: Bootstrap 4
- **Responsive**: Yes
- **WooCommerce Support**: Yes

## Directory Structure

```
wp-content/themes/
├── {{THEME_NAME}}/ (CHILD THEME)
│   ├── style.css (Template: {{PARENT_THEME}})
│   ├── functions.php
│   │
│   ├── assets/
│   │   └── css/
│   │       └── shop.css (v1.0.4 - Product page layout)
│   │
│   ├── functions/
│   │   ├── metabox.php ({{CUSTOM_FIELDS_PLUGIN}} field definitions - CRITICAL)
│   │   └── enqueue-scripts.php (CSS/JS loading)
│   │
│   ├── parts/
│   │   └── home/
│   │       ├── hero.php (uses rwmb_meta)
│   │       ├── excursions.php (uses rwmb_meta)
│   │       ├── guided-tours.php (uses rwmb_meta)
│   │       ├── destinations.php (uses rwmb_meta)
│   │       ├── services.php (uses rwmb_meta)
│   │       ├── blog.php (uses rwmb_meta)
│   │       └── other-tours.php (uses rwmb_meta)
│   │
│   ├── woocommerce/
│   │   └── (WooCommerce template overrides)
│   │
│   └── languages/
│       └── (WPML translations)
│
└── {{PARENT_THEME}}/ (PARENT THEME)
    ├── style.css
    ├── functions.php
    ├── header.php
    ├── footer.php
    └── (standard WordPress templates)
```

## {{CUSTOM_FIELDS_PLUGIN}} Dependencies

### Files Using rwmb_meta() (7 files, 17 occurrences)

1. **parts/home/hero.php**
   - Hero slider title, subtitle, images
   - Background images
   - Call-to-action buttons

2. **parts/home/excursions.php**
   - Excursions grid layout
   - Featured excursions
   - Custom imagery

3. **parts/home/guided-tours.php**
   - Tours section content
   - Tour categories
   - Featured tours

4. **parts/home/destinations.php**
   - Destinations carousel
   - Location imagery
   - Destination descriptions

5. **parts/home/services.php**
   - Services grid
   - Service icons/images
   - Service descriptions

6. **parts/home/blog.php**
   - Blog section layout
   - Featured posts
   - Custom post selection

7. **functions/metabox.php**
   - Field definitions (all {{CUSTOM_FIELDS_PREFIX}}* fields)
   - Field groups
   - Location rules

### {{CUSTOM_FIELDS_PLUGIN}} Field Prefix
All {{CUSTOM_FIELDS_PLUGIN}} fields use `{{CUSTOM_FIELDS_PREFIX}}` prefix:
- Total database entries: 919
- Format: `{{CUSTOM_FIELDS_PREFIX}}hero_title`, `{{CUSTOM_FIELDS_PREFIX}}excursion_image`, etc.

## WooCommerce Templates

Located in `{{THEME_NAME}}/woocommerce/`:
- Product page templates
- Shop page layouts
- Checkout customizations

**Note**: shop.css (v1.0.4) provides product page layout without template overrides.

## Enqueue Pattern (functions.php)

```php
// Enqueue parent stylesheet first
wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');

// Then child stylesheet
wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css');

// shop.css for WooCommerce products
wp_enqueue_style('shop-css', get_stylesheet_directory_uri() . '/assets/css/shop.css');
```

## Critical Files

**DO NOT MODIFY** (without backup):
1. `functions/metabox.php` - {{CUSTOM_FIELDS_PLUGIN}} definitions
2. `parts/home/*.php` - Homepage sections ({{CUSTOM_FIELDS_PLUGIN}} dependent)
3. `style.css` - Theme identification

**Safe to Modify**:
1. `assets/css/shop.css` - Product page styling
2. `functions/enqueue-scripts.php` - CSS/JS loading
3. `woocommerce/*` - WooCommerce overrides

## Template Hierarchy

Homepage loads:
1. `front-page.php` (parent or child)
2. `parts/home/hero.php` (child)
3. `parts/home/excursions.php` (child)
4. `parts/home/guided-tours.php` (child)
5. `parts/home/destinations.php` (child)
6. `parts/home/services.php` (child)
7. `parts/home/blog.php` (child)

Product pages load:
1. `woocommerce/single-product.php` (child override or parent)
2. `assets/css/shop.css` (child - v1.0.4)

## Responsive Breakpoints (Bootstrap 4)

- **XS**: < 576px (mobile)
- **SM**: ≥ 576px (mobile landscape)
- **MD**: ≥ 768px (tablet)
- **LG**: ≥ 992px (desktop)
- **XL**: ≥ 1200px (large desktop)

## References
- {{CUSTOM_FIELDS_PLUGIN}} dependencies: `/docs/META_BOX_DEPENDENCY_REPORT.md`
- Phase 11 shop.css: `/docs/PROJECT_PHASES.md`
- Child theme patterns: `.claude/skills/theme-development/child-theme-patterns.md`
