# PROMPT v1.0.0 WAVE 0 — Setup Foundation CMS Migration

> **Per Claude Code in nuova sessione (single agent, sequenziale).** Tempo: ~30-45 min.
> **CRITICAL**: questo è il setup foundation. NESSUN content migration ancora. Solo plugin install + CPT register + ACF Field Groups stub + verify. Wave 1+2+3 saranno multi-agent dopo.

---

## 🎯 Tu sei

L'**Agente Foundation Wave 0**. Decisione orchestratore Duccio:
- ACF Free + workaround CPT "fake repeater" (no licenza Pro)
- Skip backup (git è sufficiente)
- Sito staging resta funzionante (refactor backend-only, frontend invariato)

OBIETTIVO Wave 0: preparare la base per Wave 1+2+3 multi-agent successivi.

```
WAVE 0 (questa sessione, ~30 min):
  ├─ Step 1: Install ACF Free (LOCAL + DROPLET)
  ├─ Step 2: Register 8 nuovi CPT "fake repeater"
  ├─ Step 3: ACF JSON sync setup (git versioning)
  ├─ Step 4: Smoke test: WP-Admin mostra nuovi menu CPT
  └─ Step 5: Report Wave 0 + handoff Wave 1
```

---

## 📚 Letture obbligatorie

```
PIANO_EMERGENZA_v1.0_CMS_MIGRATION.md (root project)
CLAUDE.md (hard constraints)

wp-content/themes/saltelli/
  ├── inc/acf-fields.php (architettura prevista, mai attivata)
  ├── inc/cpt.php (CPT esistenti: avvocato, competenza)
  └── functions.php (theme bootstrap)
```

---

## 🔒 Hard rules

| Rule | Decisione |
|---|---|
| **NESSUN content migration** in Wave 0 (solo schema setup) | Wave 1+2 dopo |
| **NO modifiche frontend** (template PHP, sections.css) | refactor backend-only |
| **CPT register reversibili** (functions.php hookable) | rollback safe |
| **ACF Free**, no Pro features | scelta orchestrator |
| **JSON sync auto** in `acf-json/` (git) | versioning |
| **Smoke test** WP-Admin dopo OGNI step | safety |
| Cache flush + transient delete dopo plugin install | obbligatorio WP |

---

## STEP 1 — Install ACF Free LOCAL + DROPLET (10 min)

### 1.1 — LOCAL Docker

```bash
cd /Users/aldosantoro/Desktop/DEV/saltelli-wp

# Install ACF Free (advanced-custom-fields)
docker compose run --rm wpcli plugin install advanced-custom-fields --activate

# Verify
docker compose run --rm wpcli plugin status advanced-custom-fields 2>&1 | tail -3
# Atteso: Active

docker compose run --rm wpcli eval "echo function_exists('get_field') ? 'ACF OK' : 'FAIL';"
# Atteso: ACF OK

# Verify version
docker compose run --rm wpcli plugin get advanced-custom-fields --field=version
# Atteso: 6.x.x (Free)
```

### 1.2 — DROPLET production

```bash
ssh deploy@178.62.207.50 << 'SSH'
cd /var/www/saltelli/htdocs
sudo -u www-data wp plugin install advanced-custom-fields --activate

sudo -u www-data wp plugin status advanced-custom-fields 2>&1 | tail -3
sudo -u www-data wp eval "echo function_exists('get_field') ? 'ACF OK' : 'FAIL';"
SSH
```

### 1.3 — Smoke verify

Apri WP-Admin local:
```bash
echo "Apri: http://localhost:8080/wp-admin"
echo "Verifica menu sidebar: 'Custom Fields' (ACF) deve essere presente"
```

---

## STEP 2 — Register 8 nuovi CPT "fake repeater" (10 min)

### 2.1 — Modifica `inc/cpt.php`

Aggiungi al file esistente (mantenendo CPT 'avvocato' e 'competenza' che ci sono già):

```php
<?php
// CPT esistenti (avvocato, competenza) — mantieni invariati

// === v1.0.0 CMS Migration — CPT "fake repeater" ===
add_action('init', function () {
    
    // FAQ items (riusabili cross-page: /faq/, /costi/, /tier-1)
    register_post_type('saltelli_faq', [
        'label' => __('FAQ', 'saltelli'),
        'labels' => [
            'name' => 'Domande frequenti',
            'singular_name' => 'FAQ',
            'add_new_item' => 'Aggiungi nuova FAQ',
            'edit_item' => 'Modifica FAQ',
            'all_items' => 'Tutte le FAQ',
        ],
        'public' => false,
        'publicly_queryable' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 25,
        'menu_icon' => 'dashicons-format-status',
        'capability_type' => 'post',
        'has_archive' => false,
        'rewrite' => false,
        'supports' => ['title', 'page-attributes'], // page-attributes = menu_order per ordering
        'show_in_rest' => true,
    ]);
    
    // Caso rappresentativo (per /casi/, single-avvocato, single-competenza)
    register_post_type('saltelli_caso', [
        'label' => __('Caso rappresentativo', 'saltelli'),
        'labels' => [
            'name' => 'Casi rappresentativi',
            'singular_name' => 'Caso',
            'add_new_item' => 'Aggiungi nuovo caso',
            'edit_item' => 'Modifica caso',
            'all_items' => 'Tutti i casi',
        ],
        'public' => false,
        'publicly_queryable' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 26,
        'menu_icon' => 'dashicons-awards',
        'capability_type' => 'post',
        'has_archive' => false,
        'rewrite' => false,
        'supports' => ['title', 'page-attributes'],
        'show_in_rest' => true,
    ]);
    
    // Modalità consulenza (per /costi/ § 01 — 3 modalità)
    register_post_type('saltelli_modalita', [
        'label' => __('Modalità consulenza', 'saltelli'),
        'labels' => [
            'name' => 'Modalità consulenza',
            'singular_name' => 'Modalità',
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => 'edit.php?post_type=page', // sub-menu pages
        'menu_position' => 27,
        'capability_type' => 'post',
        'has_archive' => false,
        'rewrite' => false,
        'supports' => ['title', 'page-attributes'],
        'show_in_rest' => false,
    ]);
    
    // Scenario costi (per /costi/ § 02 — 3 scenari post-30min)
    register_post_type('saltelli_scenario', [
        'label' => __('Scenario costi', 'saltelli'),
        'labels' => [
            'name' => 'Scenari costi',
            'singular_name' => 'Scenario',
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => 'edit.php?post_type=page',
        'capability_type' => 'post',
        'has_archive' => false,
        'rewrite' => false,
        'supports' => ['title', 'page-attributes'],
    ]);
    
    // Principio studio (per /chi-siamo/, /avvocati/, /come-lavoriamo/)
    register_post_type('saltelli_principio', [
        'label' => __('Principio studio', 'saltelli'),
        'labels' => [
            'name' => 'Principi studio',
            'singular_name' => 'Principio',
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => 'edit.php?post_type=page',
        'capability_type' => 'post',
        'has_archive' => false,
        'rewrite' => false,
        'supports' => ['title', 'page-attributes'],
    ]);
    
    // Trust signal (per /costi/ § 05 — 4 plates)
    register_post_type('saltelli_trust', [
        'label' => __('Trust signal', 'saltelli'),
        'labels' => [
            'name' => 'Trust signals',
            'singular_name' => 'Trust signal',
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => 'edit.php?post_type=page',
        'capability_type' => 'post',
        'has_archive' => false,
        'rewrite' => false,
        'supports' => ['title', 'page-attributes'],
    ]);
    
    // Formazione (per single-avvocato — formazione history)
    register_post_type('saltelli_formazione', [
        'label' => __('Formazione', 'saltelli'),
        'labels' => [
            'name' => 'Formazione & Titoli',
            'singular_name' => 'Formazione',
        ],
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => 'edit.php?post_type=avvocato', // sub-menu avvocato
        'capability_type' => 'post',
        'has_archive' => false,
        'rewrite' => false,
        'supports' => ['title', 'page-attributes'],
    ]);
    
    // Guide gratuita (per /guide-gratuite/ list)
    register_post_type('saltelli_guida', [
        'label' => __('Guida gratuita', 'saltelli'),
        'labels' => [
            'name' => 'Guide gratuite',
            'singular_name' => 'Guida',
        ],
        'public' => true,  // public per consentire URL singolo download
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 28,
        'menu_icon' => 'dashicons-download',
        'capability_type' => 'post',
        'has_archive' => false,
        'rewrite' => ['slug' => 'guida'],
        'supports' => ['title', 'editor', 'page-attributes', 'thumbnail'],
        'show_in_rest' => true,
    ]);
    
    // Tassonomie associate
    
    // FAQ topic taxonomy (Tributario / Lavoro / LGBTQ+ / Costi / Metodo / Prima consulenza)
    register_taxonomy('faq_topic', 'saltelli_faq', [
        'label' => 'FAQ Topic',
        'public' => false,
        'show_ui' => true,
        'show_admin_column' => true,
        'hierarchical' => true,
        'rewrite' => false,
    ]);
    
    // Casi categoria (collega a competenza)
    register_taxonomy('caso_categoria', 'saltelli_caso', [
        'label' => 'Casi categoria',
        'public' => false,
        'show_ui' => true,
        'show_admin_column' => true,
        'hierarchical' => true,
        'rewrite' => false,
    ]);
});
```

### 2.2 — Activate + flush rewrite

```bash
docker compose run --rm wpcli rewrite flush --hard
docker compose run --rm wpcli cache flush
```

### 2.3 — Verify CPT registered

```bash
docker compose run --rm wpcli post-type list --format=csv 2>&1 | grep -E 'saltelli_' | head -10

# Atteso: 8 CPT custom (faq, caso, modalita, scenario, principio, trust, formazione, guida)
```

Smoke WP-Admin:
```
Apri http://localhost:8080/wp-admin
Sidebar deve mostrare:
  ✓ Domande frequenti
  ✓ Casi rappresentativi
  ✓ Guide gratuite
  
Sotto Pages:
  ✓ Modalità consulenza
  ✓ Scenari costi
  ✓ Principi studio
  ✓ Trust signals
  
Sotto Avvocati:
  ✓ Formazione & Titoli
```

---

## STEP 3 — ACF JSON sync setup (5 min)

### 3.1 — Create acf-json directory

```bash
mkdir -p wp-content/themes/saltelli/acf-json
chmod 755 wp-content/themes/saltelli/acf-json
echo "/.gitkeep" > wp-content/themes/saltelli/acf-json/.gitkeep
```

### 3.2 — Configura ACF JSON sync

In `inc/acf-fields.php`, sostituisci/aggiungi:

```php
<?php
defined('ABSPATH') || exit;

// === v1.0.0 — ACF JSON sync (git-friendly versioning) ===
add_filter('acf/settings/save_json', function($path) {
    return get_stylesheet_directory() . '/acf-json';
});

add_filter('acf/settings/load_json', function($paths) {
    unset($paths[0]);
    $paths[] = get_stylesheet_directory() . '/acf-json';
    return $paths;
});

// Theme Options page (esistente, mantieni)
add_action('init', function () {
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page([
            'page_title' => __('Saltelli — Settings', 'saltelli'),
            'menu_title' => __('Saltelli — Settings', 'saltelli'),
            'menu_slug'  => 'saltelli-settings',
            'capability' => 'manage_options',
            'icon_url'   => 'dashicons-admin-settings',
            'position'   => 60,
        ]);
    }
});
```

### 3.3 — Verify

```bash
ls -la wp-content/themes/saltelli/acf-json/
docker compose run --rm wpcli eval "echo wp_normalize_path(get_stylesheet_directory() . '/acf-json/');"
```

---

## STEP 4 — Smoke test final (5 min)

```bash
# Frontend smoke
echo "═══ FRONTEND SMOKE TEST ═══"
for URL in / /chi-siamo/ /avvocati/ /casi/ /costi/ /faq/; do
    HTTP=$(curl -sL -o /dev/null -w "%{http_code}" "http://localhost:8080$URL" -m 5)
    echo "  $URL → HTTP $HTTP"
done

# Atteso: tutti 200 — frontend invariato

# WP-Admin smoke (verify ACF + nuovi CPT)
echo ""
echo "═══ WP-ADMIN SMOKE ═══"
docker compose run --rm wpcli plugin status advanced-custom-fields 2>&1 | tail -3
echo ""
docker compose run --rm wpcli post-type list --format=csv 2>&1 | grep -E 'saltelli_|avvocato|competenza' | head -10
echo ""

# DROPLET deploy + verify
ssh deploy@178.62.207.50 << 'SSH'
cd /var/www/saltelli/htdocs
sudo -u www-data wp cache flush
sudo -u www-data wp rewrite flush --hard
sudo -u www-data wp plugin status advanced-custom-fields 2>&1 | tail -3
sudo -u www-data wp post-type list --format=csv 2>&1 | grep -E 'saltelli_|avvocato|competenza'
SSH
```

---

## STEP 5 — Bump + commit + report Wave 0 (5 min)

```bash
# Sync inc/cpt.php + inc/acf-fields.php to droplet
rsync -avz wp-content/themes/saltelli/inc/ deploy@178.62.207.50:/var/www/saltelli/htdocs/wp-content/themes/saltelli/inc/

# Bump version
sed -i.bak 's/Version: [0-9.]\+.*/Version: 1.0.0-recovery-wave0/' wp-content/themes/saltelli/style.css
sed -i.bak "s/SALTELLI_THEME_VERSION', '[^']*'/SALTELLI_THEME_VERSION', '1.0.0-recovery-wave0'/" wp-content/themes/saltelli/functions.php
rm -f wp-content/themes/saltelli/{style.css,functions.php}.bak

# Commit
git add -A
git commit -m "feat(v1.0.0-wave0): CMS recovery foundation — ACF Free install + 8 CPT fake repeater + JSON sync setup"
git push origin main
```

### 5.1 — Report Wave 0

`.claude/knowledge/recovery/v1.0-WAVE0-FOUNDATION.md`:

```markdown
# v1.0.0 Wave 0 Foundation Report

## Status: ✓ Foundation pronta per Wave 1+2+3

## Per step
- Step 1 — ACF Free install (local + droplet): ✓
- Step 2 — 8 CPT "fake repeater" registrati: ✓
  - saltelli_faq + taxonomy faq_topic
  - saltelli_caso + taxonomy caso_categoria  
  - saltelli_modalita
  - saltelli_scenario
  - saltelli_principio
  - saltelli_trust
  - saltelli_formazione (sub-menu Avvocati)
  - saltelli_guida (public)
- Step 3 — ACF JSON sync `acf-json/`: ✓
- Step 4 — Smoke frontend (17 URL HTTP 200) + WP-Admin: ✓
- Step 5 — Bump 1.0.0-recovery-wave0 + deploy: ✓

## Frontend invariato
Sito staging.studiolegalesaltelli.it visivamente identico.
Refactor 100% backend per editor handoff.

## CPT scope mapping
| CPT | Page WP che lo userà | Quantità tipica |
|---|---|---|
| saltelli_faq (con topic taxonomy) | /faq/, /costi/, tier-1 | 28+ items totali |
| saltelli_caso | /casi/, single-avvocato, single-competenza | 10-15 |
| saltelli_modalita | /costi/ § 01 | 3 |
| saltelli_scenario | /costi/ § 02 | 3 |
| saltelli_principio | /chi-siamo/, /avvocati/ | 3 |
| saltelli_trust | /costi/ § 05 | 4 |
| saltelli_formazione | single-avvocato (4 lawyer) | ~16 |
| saltelli_guida | /guide-gratuite/ | 12+ |

## Next: Wave 1 (parallel × 3 agent)
Ora si può procedere con Wave 1: ACF Field Groups setup + 
multi-agent tmux (decisione orchestratore post-Wave 0).

Wave 1 task per agente:
- Agent A: ACF Field Groups per page WP (costi, casi, contatti, faq, info-shared)
- Agent B: ACF Field Groups per CPT (avvocato, competenza, faq, caso, ecc.)
- Agent C: ACF Field Groups Theme Options (NAP, brand, footer, social, CTA)

Tempo Wave 1: ~2-3h elapsed.
```

Quando finito segnala: **"Wave 0 deployed. Foundation ready for Wave 1."**

---

## 🆘 Se incontri imprevisti

```
- ACF Free install fail → check WP version compatibility (richiede WP 5.8+)
- CPT register fail → verifica functions.php hooks priority
- ACF JSON dir permission → chmod 755 manuale + verify webserver write access
- Sito frontend rotto post-install → flush cache + transient delete + rewrite flush
- Plugin conflict → disattiva temporaneamente Elementor/Yoast e re-test
```

Tempo realistic Wave 0: **~30-45 min**.

Buon lavoro. Wave 0 è la fondazione: una volta completata, Wave 1+2+3 saranno multi-agent paralleli super veloci.
