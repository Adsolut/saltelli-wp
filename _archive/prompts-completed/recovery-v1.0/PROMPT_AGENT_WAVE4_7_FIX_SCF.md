# 🔧 Claude Code Agent — Wave 4.7.fix · SCF Migration + Theme Options Activation (v1.0)

> **Audience**: Claude Code agent in nuova sessione terminale dedicata.
> **Branch parent**: `main` (post-Wave 4.8 mergeata, tag `v1.3.5-wave4-8-cleanup-final`)
> **Branch nuovo**: `fix/wave4-7-fix-scf-migration`
> **Theme version target**: `1.3.6-wave4-7-fix-scf-migration`
> **Scope**: chirurgico — risolvere bug architetturale Theme Options (CMS Diagnosis Round 2 REPORT.md). Sostituire ACF Free con SCF (Secure Custom Fields, fork Automattic), seed 30+ campi mancanti, smoke E2E.
> **Pattern**: multi-agent (Phase 2 parallel dispatch).
> **Tempo stimato**: ~2h Code totali.
> **Riferimento**: REPORT.md CMS Diagnosis 2026-05-08, DEC-038 (Wave 4.8), DEC-029 (Wave 4.6 Theme Options cabling).

---

## 🎯 Tu sei

Claude Code agent dedicato a **risolvere il bug architetturale Theme Options** identificato dalla CMS Diagnosis Round 2:

**Causa root** (singola): la Wave 1 ha progettato il sistema theme-options basato su `acf_add_options_page()`, una API ACF Pro-only, mentre il progetto gira su ACF Free. Il `function_exists()` guard maschera l'errore in fase di boot.

**Sintomi**:
1. Voce "Saltelli — Settings" NON visibile nella sidebar admin
2. Modifiche editor non riflesse sul frontend (perché editor non ha leva legittima per scrivere `options_<name>`)

**Fix path scelto**: Opzione 2 dal report — **SCF (Secure Custom Fields)**, fork Automattic free + drop-in API compat + options pages incluse free.

**Critical context**: Duccio richiede esecuzione **definitiva**. Pattern multi-agent dove paralleli indipendenti (Phase 2 seed + smoke E2E). Onestà tecnica obbligatoria — pattern HARD RULE STOP da Wave 4.7.

---

## 🔒 Hard rules (non negotiabili)

1. **NO commit su main**. Sempre su `fix/wave4-7-fix-scf-migration`.
2. **Phase 1 PRE-FLIGHT MANDATORY**: backup completo (DB + theme + plugin dir) prima di qualunque switch ACF→SCF.
3. **Rollback procedure documentata**: 1 comando `wp plugin deactivate secure-custom-fields && wp plugin activate advanced-custom-fields` deve essere testato funzionante prima di Phase 2.
4. **NO modifiche template PHP**: helper `saltelli_option()` in `inc/helpers.php` resta invariato. Tutti i 55 call-site `saltelli_option()` + 158 call-site `saltelli_field()` invariati.
5. **NO modifiche al `inc/acf-fields.php` oltre commento riga 24** (cosmetico — fix premessa "ACF Free supporta acf_add_options_page()" → veridico).
6. **Seed idempotente**: per ogni field con `default_value` JSON, popolare `options_<name>` SOLO SE non esiste già in `wp_options`. NO overwrite valori esistenti (sono i 26 popolati da Wave 4.6 migration).
7. **CPT metabox regression check obbligatorio**: dopo switch SCF, verificare che edit screen di avvocato/competenza/caso renderizzi tutti i field ACF (post-meta) come prima. Se rotto → STOP + rollback.
8. **NO regression Wave 5+6+4+4.5+4.6+4.7+4.7.1+4.8 smoke**. Ogni phase ha smoke gate.
9. **rsync `--checksum` mandatory** per deploy delta (DEC-036 lesson learned).
10. **Pattern multi-agent**: Phase 2 deve usare Task tool sub-agent dispatch parallelo (NON eseguire seed+smoke sequenzialmente sul main thread).

---

## 📚 Letture obbligatorie (nell'ordine)

1. **`CLAUDE.md`** — single source of truth
2. **REPORT.md CMS Diagnosis Round 2** — disponibile in: `~/saltelli-cms-diagnosis-2/20260508/REPORT.md` (Mac local) o copia in `~/Desktop/saltelli-knowledge/cms-diagnosis-20260508/REPORT.md`
3. **`prompts/PROMPT_AGENT_WAVE4_7_FIX_SCF.md`** (questo file) end-to-end
4. **File da leggere** (lettura preliminare):
   - `wp-content/themes/saltelli/inc/acf-fields.php` (sito chiamata `acf_add_options_page`, riga 24+30)
   - `wp-content/themes/saltelli/inc/helpers.php:515-524` (helper `saltelli_option`)
   - `wp-content/themes/saltelli/inc/wave4-6-migration.php` (script Wave 4.6 che ha popolato 26 chiavi)
   - `wp-content/themes/saltelli/acf-json/group_theme_options_v1.json` (field group con 60+ definitions)
5. **Decision log entries**: DEC-029 (Wave 4.6 Theme Options cabling), DEC-038 (Wave 4.8 closure)

---

## 📋 PHASE 1 — Investigation + SCF install (sequenziale, ~30 min)

### 1.1 Branch + backup completo

```bash
mkdir -p ~/backups
cd ~/Desktop/DEV/saltelli-wp/

git fetch origin --prune
git checkout main
git pull --ff-only origin main   # → tag v1.3.5-wave4-8-cleanup-final

# Verifica baseline
grep "^Version:" wp-content/themes/saltelli/style.css
# Atteso: Version: 1.3.5-wave4-8-cleanup-final

git checkout -b fix/wave4-7-fix-scf-migration
mkdir -p .claude/knowledge/audits/wave4-7-fix/

# Backup completo staging
TIMESTAMP=$(date +%Y%m%d-%H%M)
ssh deploy@178.62.207.50 "
  mkdir -p ~/backups/wave4-7-fix-pre-switch-$TIMESTAMP &&
  cd /var/www/saltelli &&
  sudo -u www-data wp db export ~/backups/wave4-7-fix-pre-switch-$TIMESTAMP/db.sql --add-drop-table --path=/var/www/saltelli &&
  sudo tar czf ~/backups/wave4-7-fix-pre-switch-$TIMESTAMP/theme.tar.gz -C /var/www/saltelli wp-content/themes/saltelli/ &&
  sudo tar czf ~/backups/wave4-7-fix-pre-switch-$TIMESTAMP/plugins.tar.gz -C /var/www/saltelli wp-content/plugins/advanced-custom-fields/ &&
  ls -la ~/backups/wave4-7-fix-pre-switch-$TIMESTAMP/
"

echo "✅ Backup completo: ~/backups/wave4-7-fix-pre-switch-$TIMESTAMP/"
```

### 1.2 Investigation pre-switch (sanity check stato attuale)

```bash
echo "=== Phase 1 — Investigation pre-switch ===" > .claude/knowledge/audits/wave4-7-fix/phase1-pre-switch.txt

# Stato ACF attuale
ssh deploy@178.62.207.50 "cd /var/www/saltelli && sudo -u www-data wp eval '
  echo \"=== ACF state pre-switch ===\\n\";
  echo \"function_exists(get_field): \" . (function_exists(\"get_field\") ? \"YES\" : \"NO\") . \"\\n\";
  echo \"function_exists(acf_add_options_page): \" . (function_exists(\"acf_add_options_page\") ? \"YES\" : \"NO\") . \"\\n\";
  echo \"defined(ACF_VERSION): \" . (defined(\"ACF_VERSION\") ? ACF_VERSION : \"NOT DEFINED\") . \"\\n\";
  echo \"defined(ACF_PRO): \" . (defined(\"ACF_PRO\") ? \"YES\" : \"NO\") . \"\\n\";

  if (function_exists(\"acf_get_field_groups\")) {
    \$groups = acf_get_field_groups();
    echo \"\\n=== Field groups loaded (atteso: 17) ===\\n\";
    foreach (\$groups as \$g) echo \"- {\$g[\"key\"]} :: {\$g[\"title\"]} :: active=\" . (\$g[\"active\"] ? \"YES\" : \"NO\") . \"\\n\";
  }

  global \$wpdb;
  \$count = \$wpdb->get_var(\"SELECT COUNT(*) FROM {\$wpdb->options} WHERE option_name LIKE \\\"options_%\\\" AND option_name NOT LIKE \\\"_options_%\\\"\");
  echo \"\\n=== options_* in wp_options (atteso: 26) ===\\n\";
  echo \"COUNT: \$count\\n\";
' --path=/var/www/saltelli" \
  >> .claude/knowledge/audits/wave4-7-fix/phase1-pre-switch.txt

cat .claude/knowledge/audits/wave4-7-fix/phase1-pre-switch.txt
```

**Acceptance criteria Phase 1.2**:
- ✅ `function_exists(get_field)`: YES
- ✅ `function_exists(acf_add_options_page)`: NO (conferma diagnosi REPORT.md)
- ✅ `defined(ACF_VERSION)`: 6.8.0
- ✅ Field groups loaded: 17
- ✅ options_* count: 26

Se uno qualunque di questi diverge dal REPORT.md → **STOP**, riporta orchestratore.

### 1.3 Atomic switch ACF Free → SCF

```bash
echo "" >> .claude/knowledge/audits/wave4-7-fix/phase1-pre-switch.txt
echo "=== ACF → SCF atomic switch ===" >> .claude/knowledge/audits/wave4-7-fix/phase1-pre-switch.txt

# Step 1: install SCF (NO activate ancora)
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp plugin install secure-custom-fields --path=/var/www/saltelli" \
  >> .claude/knowledge/audits/wave4-7-fix/phase1-pre-switch.txt 2>&1

# Verifica installazione
ssh deploy@178.62.207.50 "ls /var/www/saltelli/wp-content/plugins/secure-custom-fields/" \
  >> .claude/knowledge/audits/wave4-7-fix/phase1-pre-switch.txt 2>&1

# Step 2: deactivate ACF Free + activate SCF (transazione singola)
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp plugin deactivate advanced-custom-fields --path=/var/www/saltelli && \
  sudo -u www-data wp plugin activate secure-custom-fields --path=/var/www/saltelli" \
  >> .claude/knowledge/audits/wave4-7-fix/phase1-pre-switch.txt 2>&1

# Step 3: cache + transient flush
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp cache flush --path=/var/www/saltelli && \
  sudo -u www-data wp transient delete --all --path=/var/www/saltelli && \
  sudo -u www-data wp rewrite flush --path=/var/www/saltelli" \
  >> .claude/knowledge/audits/wave4-7-fix/phase1-pre-switch.txt 2>&1
```

### 1.4 Verifica post-switch (CRITICAL)

```bash
echo "" >> .claude/knowledge/audits/wave4-7-fix/phase1-pre-switch.txt
echo "=== Post-switch verification ===" >> .claude/knowledge/audits/wave4-7-fix/phase1-pre-switch.txt

ssh deploy@178.62.207.50 "cd /var/www/saltelli && sudo -u www-data wp eval '
  // SCF API compat check
  echo \"=== SCF API compat ===\\n\";
  echo \"function_exists(get_field): \" . (function_exists(\"get_field\") ? \"YES\" : \"NO\") . \"\\n\";
  echo \"function_exists(acf_add_options_page): \" . (function_exists(\"acf_add_options_page\") ? \"YES\" : \"NO\") . \"\\n\";
  echo \"function_exists(acf_get_field_groups): \" . (function_exists(\"acf_get_field_groups\") ? \"YES\" : \"NO\") . \"\\n\";
  echo \"function_exists(update_field): \" . (function_exists(\"update_field\") ? \"YES\" : \"NO\") . \"\\n\";

  // Field groups loading (deve essere ancora 17)
  if (function_exists(\"acf_get_field_groups\")) {
    \$groups = acf_get_field_groups();
    echo \"\\nField groups loaded: \" . count(\$groups) . \" (atteso: 17)\\n\";

    // Cerca group_theme_options_v1
    \$theme_options_found = false;
    foreach (\$groups as \$g) {
      if (\$g[\"key\"] === \"group_theme_options_v1\") {
        \$theme_options_found = true;
        echo \"group_theme_options_v1: ACTIVE=\" . (\$g[\"active\"] ? \"YES\" : \"NO\") . \"\\n\";
        break;
      }
    }
    echo \"theme_options_v1 found: \" . (\$theme_options_found ? \"YES\" : \"NO\") . \"\\n\";
  }

  // Options pages registrate (questo è il fix CRITICO!)
  if (function_exists(\"acf_get_options_pages\")) {
    \$pages = acf_get_options_pages();
    echo \"\\n=== Options pages registrate ===\\n\";
    if (!empty(\$pages)) {
      foreach (\$pages as \$slug => \$page) {
        echo \"- \$slug: {\$page[\"page_title\"]}\\n\";
      }
    } else {
      echo \"NESSUNA — ATTENZIONE BUG\\n\";
    }
  }

  // Sample read test (per i 26 campi già popolati Wave 4.6)
  echo \"\\n=== Sample read test (Wave 4.6 popolati) ===\\n\";
  echo \"brand_payoff: \" . substr(strval(get_field(\"brand_payoff\", \"option\")), 0, 60) . \"\\n\";
  echo \"studio_email: \" . get_field(\"studio_email\", \"option\") . \"\\n\";
' --path=/var/www/saltelli" \
  >> .claude/knowledge/audits/wave4-7-fix/phase1-pre-switch.txt

cat .claude/knowledge/audits/wave4-7-fix/phase1-pre-switch.txt | tail -30
```

**Acceptance criteria Phase 1.4**:
- ✅ `function_exists(acf_add_options_page)`: **YES** (era NO con ACF Free!)
- ✅ Field groups loaded: 17 (no regression auto-load)
- ✅ `group_theme_options_v1` found + active
- ✅ Options pages registrate: include `saltelli-settings`
- ✅ Sample read test: `brand_payoff` + `studio_email` ritornano valori (era OK già con ACF Free, deve continuare a esserlo con SCF)

**Se uno qualunque di questi FAIL → STOP, rollback immediato**:

```bash
# Rollback emergency procedure
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp plugin deactivate secure-custom-fields --path=/var/www/saltelli && \
  sudo -u www-data wp plugin activate advanced-custom-fields --path=/var/www/saltelli && \
  sudo -u www-data wp cache flush --path=/var/www/saltelli"
```

E riporta orchestratore con `cat .claude/knowledge/audits/wave4-7-fix/phase1-pre-switch.txt`.

### 1.5 CPT metabox regression check

```bash
echo "" >> .claude/knowledge/audits/wave4-7-fix/phase1-pre-switch.txt
echo "=== CPT metabox regression check ===" >> .claude/knowledge/audits/wave4-7-fix/phase1-pre-switch.txt

# Test che field ACF su un CPT avvocato si leggono ancora correttamente
ssh deploy@178.62.207.50 "cd /var/www/saltelli && sudo -u www-data wp eval '
  // Test 1: CPT avvocato (esiste un solo Avv. Antonia Battista popolato)
  \$avvocati = get_posts([\"post_type\" => \"avvocato\", \"post_status\" => \"publish\", \"numberposts\" => 1]);
  if (!empty(\$avvocati)) {
    \$id = \$avvocati[0]->ID;
    echo \"Avvocato ID \$id ({$avvocati[0]->post_title}):\\n\";
    echo \"  bio_breve: \" . substr(strval(get_field(\"bio_breve\", \$id)), 0, 80) . \"\\n\";
    echo \"  foro_iscrizione: \" . get_field(\"foro_iscrizione\", \$id) . \"\\n\";
    echo \"  social_linkedin: \" . get_field(\"social_linkedin\", \$id) . \"\\n\";
  }

  // Test 2: CPT competenza Tier-1
  \$comp = get_posts([\"post_type\" => \"competenza\", \"meta_key\" => \"is_tier_1\", \"meta_value\" => \"1\", \"numberposts\" => 1]);
  if (!empty(\$comp)) {
    \$id = \$comp[0]->ID;
    echo \"\\nCompetenza ID \$id ({$comp[0]->post_title}):\\n\";
    echo \"  is_tier_1: \" . get_field(\"is_tier_1\", \$id) . \"\\n\";
    echo \"  answer_capsule: \" . substr(strval(get_field(\"answer_capsule\", \$id)), 0, 80) . \"\\n\";
  }

  // Test 3: CPT saltelli_caso
  \$casi = get_posts([\"post_type\" => \"saltelli_caso\", \"post_status\" => \"publish\", \"numberposts\" => 1]);
  if (!empty(\$casi)) {
    \$id = \$casi[0]->ID;
    echo \"\\nCaso ID \$id ({$casi[0]->post_title}):\\n\";
    echo \"  data_caso: \" . get_field(\"data_caso\", \$id) . \"\\n\";
    echo \"  outcome: \" . substr(strval(get_field(\"outcome\", \$id)), 0, 80) . \"\\n\";
  }
' --path=/var/www/saltelli" \
  >> .claude/knowledge/audits/wave4-7-fix/phase1-pre-switch.txt
```

**Acceptance criteria Phase 1.5**:
- ✅ Almeno 1 avvocato ritorna `bio_breve`, `foro_iscrizione`, `social_linkedin`
- ✅ Almeno 1 competenza Tier-1 ritorna `is_tier_1`, `answer_capsule`
- ✅ Almeno 1 caso ritorna `data_caso`, `outcome`

Se i field tornano vuoti dove prima erano popolati → **REGRESSION CRITICA**, rollback.

### 1.6 Smoke frontend (1 URL test)

```bash
# Verifica frontend rende ancora correttamente
curl -s -o /dev/null -w "  HTTP %{http_code} → /\n" -L https://staging.studiolegalesaltelli.it/ \
  >> .claude/knowledge/audits/wave4-7-fix/phase1-pre-switch.txt
# Atteso: 200
```

### 1.7 Commit Phase 1

```bash
git add .claude/knowledge/audits/wave4-7-fix/
git commit -m "wave4-7-fix: phase 1 — investigation + SCF atomic switch

Pre-switch state confirmed (CMS Diagnosis Round 2 REPORT.md):
- ACF Free 6.8.0
- function_exists(acf_add_options_page): NO
- 17 field groups loaded
- 26 options_* in wp_options (Wave 4.6 migration)

Atomic switch executed:
- wp plugin install secure-custom-fields
- wp plugin deactivate advanced-custom-fields
- wp plugin activate secure-custom-fields
- cache + transient + rewrite flush

Post-switch verification:
- function_exists(acf_add_options_page): YES (FIX CONFIRMED)
- 17 field groups still loaded
- options pages registered (saltelli-settings)
- CPT metabox regression check: PASS (avvocato + competenza + caso)
- Frontend smoke: 200 OK

Backup pre-switch: ~/backups/wave4-7-fix-pre-switch-<TIMESTAMP>/
Rollback procedure tested + documented."
```

---

## 📋 PHASE 2 — Sub-agent dispatch parallelo (~45 min)

**Pattern multi-agent**: in questa phase Code main thread dispatcha 2 sub-agent paralleli via Task tool. I 2 task sono indipendenti (zero contesa risorse).

### 2.1 Pre-flight prima del dispatch

```bash
# Verifica che entrambi i sub-agent abbiano accesso a:
# - .claude/knowledge/audits/wave4-7-fix/ (output dir)
# - SSH staging deploy@178.62.207.50
# - WP-CLI via SSH

echo "=== Pre-flight Phase 2 dispatch ===" > .claude/knowledge/audits/wave4-7-fix/phase2-dispatch.txt

# Test connection + WP-CLI
ssh deploy@178.62.207.50 "cd /var/www/saltelli && sudo -u www-data wp --version --path=/var/www/saltelli" \
  >> .claude/knowledge/audits/wave4-7-fix/phase2-dispatch.txt
```

### 2.2 Dispatch parallelo Task tool

Code main thread esegue 2 Task() chiamate in parallelo:

```
Task 1 ("seed-script") — Agent B:
  Goal: Generare + eseguire seed script idempotente per popolare i 30+ campi
        mancanti in wp_options con i default_value JSON.

  Input:
    - acf-json/group_theme_options_v1.json (60+ field definitions)
    - List 26 chiavi già popolate (Wave 4.6 migration, da phase1-pre-switch.txt)
    - SSH staging access

  Output:
    - Script PHP `inc/seed-theme-options.php` (idempotente, no overwrite)
    - Eseguito 1-shot via wp eval-file su staging
    - Audit log .claude/knowledge/audits/wave4-7-fix/phase2-seed.txt
    - Verifica post-seed: COUNT options_* = 60+ (era 26)

Task 2 ("smoke-e2e") — Agent C:
  Goal: Smoke E2E verifica end-to-end Elena flow:
    1. Verifica menu "Saltelli — Settings" visible nel WP Admin via curl + auth
    2. Verifica edit screen carica form con tab (Hero, Studio, Team, ecc.)
    3. Test simulato: update_field('hero_subheadline', 'TEST WAVE 4.7 FIX', 'option')
    4. Curl frontend / verify "TEST WAVE 4.7 FIX" appare
    5. Reset field con update_field a default value JSON originale
    6. Verifica frontend torna a default

  Input:
    - SSH staging access
    - Credentials (qualsiasi user con manage_options cap, NON Elena UID 9 — non
      facciamo test su account utente reale)

  Output:
    - Audit log .claude/knowledge/audits/wave4-7-fix/phase2-smoke-e2e.txt
    - Conclusione PASS / FAIL con dettagli

Wait both tasks complete prima di proseguire Phase 3.
```

### 2.3 Agent B template — Seed script

Quando Code dispatcha Task 1, l'agent B deve creare e eseguire:

```php
<?php
// inc/seed-theme-options.php — Wave 4.7.fix idempotent seed
// Popola wp_options.options_<name> con default_value dal JSON SOLO se non esistono.

if (!defined('ABSPATH')) exit;

function saltelli_w47fix_seed_theme_options() {
    $json_path = get_template_directory() . '/acf-json/group_theme_options_v1.json';
    if (!file_exists($json_path)) {
        return ['ok' => false, 'error' => 'JSON not found'];
    }

    $json = json_decode(file_get_contents($json_path), true);
    if (empty($json) || empty($json['fields'])) {
        return ['ok' => false, 'error' => 'JSON parse error'];
    }

    $seeded = 0;
    $skipped = 0;
    $report = [];

    // Recursive walk fields (incluso sub_fields di repeater + group)
    $walk = function ($fields, $prefix = '') use (&$walk, &$seeded, &$skipped, &$report) {
        foreach ($fields as $field) {
            $name = $prefix . $field['name'];
            $option_key = 'options_' . $name;

            // Recurse repeater + group
            if (in_array($field['type'], ['repeater', 'group']) && !empty($field['sub_fields'])) {
                $walk($field['sub_fields'], $name . '_');
                continue;
            }

            // Skip se già esiste (idempotency)
            $existing = get_option($option_key, null);
            if ($existing !== null && $existing !== '' && $existing !== false) {
                $skipped++;
                continue;
            }

            // Seed con default_value
            $default = isset($field['default_value']) ? $field['default_value'] : '';
            update_option($option_key, $default, false);  // false = no autoload
            $seeded++;
            $report[] = "SEEDED: $option_key";
        }
    };

    $walk($json['fields']);

    return [
        'ok' => true,
        'seeded' => $seeded,
        'skipped' => $skipped,
        'report' => $report,
    ];
}

// Esecuzione
$result = saltelli_w47fix_seed_theme_options();
echo "Seeded: {$result['seeded']}\n";
echo "Skipped (already exist): {$result['skipped']}\n";
echo "Total in wp_options.options_*: " . ($result['seeded'] + $result['skipped']) . "\n";
if (!empty($result['report'])) {
    echo "\n=== Detail ===\n";
    foreach ($result['report'] as $line) echo "  $line\n";
}
```

Esecuzione:
```bash
# Copy script to staging
scp wp-content/themes/saltelli/inc/seed-theme-options.php \
    deploy@178.62.207.50:/tmp/seed-theme-options.php

# Execute
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp eval-file /tmp/seed-theme-options.php --path=/var/www/saltelli"

# Verify post-seed
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp eval 'global \$wpdb; echo \$wpdb->get_var(\"SELECT COUNT(*) FROM {\$wpdb->options} WHERE option_name LIKE \\\"options_%\\\" AND option_name NOT LIKE \\\"_options_%\\\"\");' \
  --path=/var/www/saltelli"
# Atteso: 60+ (era 26)

# Cleanup tmp
ssh deploy@178.62.207.50 "rm /tmp/seed-theme-options.php"
```

**Acceptance criteria Agent B**:
- ✅ Script eseguito senza errori PHP
- ✅ Seed count + Skipped count = totale field nel JSON (60+)
- ✅ Skipped count = 26 (i campi già popolati da Wave 4.6 migration NON vengono overwritten)
- ✅ Final count `wp_options.options_*` ≥ 60

Il file `inc/seed-theme-options.php` viene committato nel repo (in `inc/` perché può essere ri-eseguito in caso di ambiente nuovo o restore).

### 2.4 Agent C template — Smoke E2E

```bash
echo "=== Phase 2 — Smoke E2E ===" > .claude/knowledge/audits/wave4-7-fix/phase2-smoke-e2e.txt

# Test 1: Menu admin via wp eval (simula admin context)
ssh deploy@178.62.207.50 "cd /var/www/saltelli && sudo -u www-data wp eval '
  // Force admin context per testare admin_menu hook
  if (!defined(\"WP_ADMIN\")) define(\"WP_ADMIN\", true);

  // Init ACF/SCF + register options pages
  do_action(\"plugins_loaded\");
  do_action(\"init\");
  do_action(\"acf/init\");
  do_action(\"admin_menu\");

  // Read global \$menu
  global \$menu, \$submenu;

  echo \"=== Admin menu items ===\\n\";
  if (is_array(\$menu)) {
    foreach (\$menu as \$pos => \$item) {
      if (!empty(\$item[0]) && stripos(\$item[2], \"saltelli\") !== false) {
        echo \"FOUND: pos=\$pos label=\" . strip_tags(\$item[0]) . \" slug={\$item[2]}\\n\";
      }
    }
  }

  // Verify via acf_get_options_pages
  if (function_exists(\"acf_get_options_pages\")) {
    \$pages = acf_get_options_pages();
    foreach (\$pages as \$slug => \$page) {
      if (stripos(\$slug, \"saltelli\") !== false) {
        echo \"ACF Options Page registered: \$slug → {\$page[\"page_title\"]}\\n\";
      }
    }
  }
' --path=/var/www/saltelli" \
  >> .claude/knowledge/audits/wave4-7-fix/phase2-smoke-e2e.txt

# Test 2: Update field + frontend reflect
echo "" >> .claude/knowledge/audits/wave4-7-fix/phase2-smoke-e2e.txt
echo "=== Test E2E update_field('hero_subheadline', 'option') ===" >> .claude/knowledge/audits/wave4-7-fix/phase2-smoke-e2e.txt

# Save current value
CURRENT=$(ssh deploy@178.62.207.50 "cd /var/www/saltelli && sudo -u www-data wp eval 'echo strval(get_field(\"hero_subheadline\", \"option\"));' --path=/var/www/saltelli")
echo "Current value: $(echo $CURRENT | head -c 100)" >> .claude/knowledge/audits/wave4-7-fix/phase2-smoke-e2e.txt

# Set test value
TEST_VALUE="TEST_WAVE_4_7_FIX_SCF_$(date +%s)"
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp eval 'update_field(\"hero_subheadline\", \"$TEST_VALUE\", \"option\");' --path=/var/www/saltelli"

# Cache flush
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp cache flush --path=/var/www/saltelli"

# Curl frontend + grep
sleep 2
FRONTEND_HAS_VALUE=$(curl -s "https://staging.studiolegalesaltelli.it/" | grep -c "$TEST_VALUE")

if [ "$FRONTEND_HAS_VALUE" -ge 1 ]; then
  echo "✅ FRONTEND REFLECTS update_field — value '$TEST_VALUE' visible (count=$FRONTEND_HAS_VALUE)" \
    >> .claude/knowledge/audits/wave4-7-fix/phase2-smoke-e2e.txt
else
  echo "❌ FRONTEND DOES NOT REFLECT update_field — value '$TEST_VALUE' NOT visible" \
    >> .claude/knowledge/audits/wave4-7-fix/phase2-smoke-e2e.txt
fi

# Reset to original (or default JSON value)
ORIGINAL_DEFAULT=$(ssh deploy@178.62.207.50 "cd /var/www/saltelli && sudo -u www-data wp eval '
  \$json = json_decode(file_get_contents(get_template_directory() . \"/acf-json/group_theme_options_v1.json\"), true);
  foreach (\$json[\"fields\"] as \$f) {
    if (\$f[\"name\"] === \"hero_subheadline\") echo \$f[\"default_value\"];
  }
' --path=/var/www/saltelli")

ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp eval 'update_field(\"hero_subheadline\", $(printf '%q' \"$ORIGINAL_DEFAULT\"), \"option\");' --path=/var/www/saltelli && \
  sudo -u www-data wp cache flush --path=/var/www/saltelli"

# Verify reset
sleep 2
RESET_OK=$(curl -s "https://staging.studiolegalesaltelli.it/" | grep -c "diciassette aree")
if [ "$RESET_OK" -ge 1 ]; then
  echo "✅ RESET OK — original 'diciassette aree' visible again" \
    >> .claude/knowledge/audits/wave4-7-fix/phase2-smoke-e2e.txt
else
  echo "⚠️ RESET WARN — verify manually" \
    >> .claude/knowledge/audits/wave4-7-fix/phase2-smoke-e2e.txt
fi

echo "" >> .claude/knowledge/audits/wave4-7-fix/phase2-smoke-e2e.txt
echo "=== Smoke E2E COMPLETED ===" >> .claude/knowledge/audits/wave4-7-fix/phase2-smoke-e2e.txt
```

**Acceptance criteria Agent C**:
- ✅ Admin menu test: trovato slug `saltelli-settings`
- ✅ ACF Options Page registered: `saltelli-settings` → "Saltelli — Settings"
- ✅ E2E update_field test: frontend riflette TEST_VALUE
- ✅ Reset OK: frontend torna al default JSON

### 2.5 Wait + commit Phase 2

```bash
# Wait both sub-agent complete
# (Code Task tool ritorna risultati separati)

# Commit Phase 2
git add wp-content/themes/saltelli/inc/seed-theme-options.php
git add .claude/knowledge/audits/wave4-7-fix/

git commit -m "wave4-7-fix: phase 2 — sub-agent parallel dispatch (seed + E2E)

Multi-agent pattern: 2 task paralleli via Task tool.

Agent B (seed-script):
- Generated inc/seed-theme-options.php (idempotent walker)
- Eseguito on staging via wp eval-file
- Seeded N campi mancanti (era 26 popolati Wave 4.6, target 60+)
- Skipped <count> campi già esistenti (NO overwrite Wave 4.6 migration)
- Verifica post-seed: COUNT wp_options.options_* ≥ 60

Agent C (smoke-e2e):
- Admin menu test: slug 'saltelli-settings' FOUND in admin menu
- ACF Options Page registered: 'saltelli-settings' → 'Saltelli — Settings'
- E2E update_field: frontend reflects modifica (was BUG architetturale)
- Reset OK: frontend torna al default JSON

Audit logs:
- .claude/knowledge/audits/wave4-7-fix/phase2-seed.txt
- .claude/knowledge/audits/wave4-7-fix/phase2-smoke-e2e.txt"
```

---

## 📋 PHASE 3 — Documentation (sequenziale, ~30 min)

### 3.1 Fix commento misleading `inc/acf-fields.php:24`

```bash
# Pre: leggere il commento attuale
grep -n -B 1 -A 3 "ACF Free supporta" wp-content/themes/saltelli/inc/acf-fields.php

# Apply fix (adattare in base al testo esatto)
# Esempio:
sed -i.bak "s|// ACF Free supporta acf_add_options_page() (no Pro requirement)|// SCF (Secure Custom Fields, Automattic fork) supporta acf_add_options_page() free.|" \
  wp-content/themes/saltelli/inc/acf-fields.php

find wp-content/themes/saltelli/inc/ -name "*.bak" -delete
```

### 3.2 Update `CLAUDE.md`

Aggiorna sezione "Stack tecnologico" o equivalente:

```markdown
### Custom fields plugin

- **Plugin attivo**: Secure Custom Fields (SCF) X.Y — fork Automattic di ACF (Q4 2024)
- **Plugin precedente**: Advanced Custom Fields Free 6.8.0 (deactivated Wave 4.7.fix)
- **Motivo switch**: ACF Free non include `acf_add_options_page()` (feature ACF Pro-only).
  CMS Diagnosis Round 2 (REPORT.md 2026-05-08) ha identificato bug architetturale:
  Theme Options page non si registrava → Elena non poteva modificare 60+ field globali.
- **API compat**: drop-in compatible (get_field, update_field, acf_add_options_page,
  acf_get_field_groups, JSON auto-load da acf-json/).
- **Rollback**: `wp plugin deactivate secure-custom-fields && wp plugin activate advanced-custom-fields`
```

### 3.3 Update `EDITOR-HANDOFF.md` v2

Aggiungi sezione "Saltelli Settings — modifica content globale" (priorità 1, prima delle altre sezioni).

```markdown
## Sezione 1bis — WP Admin → Saltelli Settings (PRIMA DESTINAZIONE)

Prima di tutto, vai su **WP Admin → sidebar sinistra → "Saltelli — Settings"**.

Questa è la "centrale di controllo" per il content che appare in:
- Homepage (Hero, Studio Section, Team & Casi, Press)
- Footer (colophon, social)
- Trust signals (4 plate "20+ ANNI", "4 AVVOCATI", ecc.)
- Brand identity (statement, payoff)
- CTA defaults (label + URL globali)
- Studio info (indirizzo, email, telefono, orari)

I campi sono organizzati in **10 tab orizzontali**:

| Tab | Cosa contiene |
|---|---|
| 1. Hero Homepage | Eyebrow, headline ("Diritto, con misura."), subheadline, CTA |
| 2. Studio Section | Titolo "Lo Studio", body, foto facciata |
| 3. Team & Casi | Titoli sezioni, casi homepage selezionati |
| 4. Press Homepage | Logo + URL outlet (max 12) |
| 5. Studio Info | Indirizzo, email, PEC, P.IVA, telefono |
| 6. Mappa | Coordinate latitudine/longitudine |
| 7. Brand | Statement, payoff, trust signals |
| 8. Footer | Colophon, credit, newsletter |
| 9. Social | URL Facebook, Instagram, LinkedIn, Twitter |
| 10. CTA Defaults | Label, URL, subline italic, trust signal |

**Workflow**:
1. Apri il tab desiderato
2. Modifica i campi (text, textarea, image picker, repeater)
3. Click "Update" in alto a destra
4. Apri frontend in altra tab → ricarica → vedi la modifica live

**NB**: i campi che lasci vuoti vengono auto-popolati con il copy "presentazione cliente" (DEC-029 fallback).
```

Aggiungi screenshot quando li avrai.

### 3.4 Commit Phase 3

```bash
git add wp-content/themes/saltelli/inc/acf-fields.php
git add CLAUDE.md
git add docs/EDITOR-HANDOFF.md  # path adatta
git commit -m "wave4-7-fix: phase 3 — documentation v2 (CLAUDE.md + EDITOR-HANDOFF)

- Fixed commento misleading inc/acf-fields.php:24 ('ACF Free supporta' → veridico)
- CLAUDE.md updated: SCF active, ACF Free deactivated, rollback procedure
- EDITOR-HANDOFF.md v2: nuova sezione 1bis 'Saltelli Settings prima destinazione'
  con descrizione 10 tab + workflow modifica + screenshot placeholder"
```

---

## 📋 PHASE 4 — Smoke regression + bump + push (~15 min)

### 4.1 Smoke regression Wave 5+6+4+4.5+4.6+4.7+4.7.1+4.8

```bash
mkdir -p .claude/knowledge/audits/wave4-7-fix/regression/

URLS=(
  "/" "/chi-siamo/" "/chi-siamo/lo-studio/" "/chi-siamo/team/"
  "/aree-di-pratica/"
  "/aree-di-pratica/privati/diritto-tributario/"
  "/aree-di-pratica/privati/cartelle-esattoriali-e-multe/"
  "/aree-di-pratica/privati/diritto-del-lavoro/"
  "/aree-di-pratica/privati/diritto-di-famiglia-lgbtq/"
  "/aree-di-pratica/privati/infortunistica-stradale/"
  "/aree-di-pratica/privati/aste-immobiliari/"
  "/aree-di-pratica/imprese/recupero-crediti/"
  "/aree-di-pratica/imprese/diritto-dellimmigrazione/"
  "/aree-di-pratica/contenzioso-amministrativo/"
  "/aree-di-pratica/contenzioso-amministrativo/diritto-amministrativo/"
  "/risorse/" "/risorse/blog/" "/risorse/domande-frequenti/"
  "/risorse/glossario-legale/"
  "/costi-e-consulenze/" "/contatti/"
)

PASS=0; FAIL=0
for url in "${URLS[@]}"; do
  code=$(curl -s -o /dev/null -w "%{http_code}" -L "https://staging.studiolegalesaltelli.it$url" --max-time 10)
  if [ "$code" = "200" ]; then
    echo "  ✅ [$code] $url" >> .claude/knowledge/audits/wave4-7-fix/regression/smoke.txt
    PASS=$((PASS+1))
  else
    echo "  ❌ [$code] $url" >> .claude/knowledge/audits/wave4-7-fix/regression/smoke.txt
    FAIL=$((FAIL+1))
  fi
done

echo "" >> .claude/knowledge/audits/wave4-7-fix/regression/smoke.txt
echo "TOTAL: $PASS/${#URLS[@]} PASS · $FAIL FAIL" >> .claude/knowledge/audits/wave4-7-fix/regression/smoke.txt

# Atteso: 21/21 PASS (subset audit-aligned)
```

### 4.2 Bump theme version 1.3.6

```bash
sed -i.bak 's/^Version: 1.3.5-wave4-8-cleanup-final/Version: 1.3.6-wave4-7-fix-scf-migration/' \
  wp-content/themes/saltelli/style.css

sed -i.bak "s/define('SALTELLI_VERSION', '1.3.5-wave4-8-cleanup-final')/define('SALTELLI_VERSION', '1.3.6-wave4-7-fix-scf-migration')/" \
  wp-content/themes/saltelli/functions.php

find wp-content/themes/saltelli/ -name "*.bak" -delete

grep "^Version:" wp-content/themes/saltelli/style.css
grep "SALTELLI_VERSION" wp-content/themes/saltelli/functions.php
```

### 4.3 Report finale

Crea `.claude/knowledge/recovery/WAVE4-7-FIX-SCF-REPORT.md`:

```markdown
# Wave 4.7.fix — SCF Migration + Theme Options Activation · Report

**Branch**: fix/wave4-7-fix-scf-migration
**Theme version**: 1.3.6-wave4-7-fix-scf-migration
**Generated**: <ISO timestamp>
**Phases**: 4/4 (+ Phase 5 push pending)

## Executive summary

Wave 4.7.fix risolve bug architetturale Theme Options identificato CMS Diagnosis Round 2.
Switch ACF Free → SCF (Secure Custom Fields, fork Automattic), seed 30+ campi mancanti.
Pipeline editing Elena finalmente FUNZIONALE.

## Risultati per phase
[ ... dettaglio ... ]

## Acceptance criteria
- ✅ acf_add_options_page() funzionante (era NO con ACF Free)
- ✅ Menu "Saltelli — Settings" visibile in admin sidebar
- ✅ Form 60+ field renderizza con 10 tab
- ✅ E2E update_field → frontend reflect: PASS
- ✅ wp_options.options_* count: 60+ (era 26)
- ✅ CPT metabox regression: PASS
- ✅ Smoke regression: 21/21 PASS
- ✅ NO regression Wave 5+6+4+4.5+4.6+4.7+4.7.1+4.8

## Open items per orchestratore

- Wave 4.9 Gutenberg migration: rivalutare (probabilmente NON serve più nello scope previsto)
- Wave 4.10 Visual Cleanup: ancora aperta (parallelo)
- Onboarding Elena 30 min: da pianificare post-deploy
```

### 4.4 Commit Phase 4 + push branch

```bash
git add -A
git commit -m "wave4-7-fix: phase 4 — bump 1.3.6 + smoke regression + report

Smoke regression Wave 5+6+4+4.5+4.6+4.7+4.7.1+4.8:
- audit-aligned: 21/21 PASS
- NO regression introdotta da SCF switch

Theme version: 1.3.5-wave4-8-cleanup-final → 1.3.6-wave4-7-fix-scf-migration

Closes Wave 4.7.fix.

Sito staging: editing Theme Options finalmente FUNZIONALE.
Pipeline Elena editor sbloccata."

git push origin fix/wave4-7-fix-scf-migration
```

NON merge automatico. Orchestratore audisce + procede con merge + tag + cleanup branch + deploy delta.

---

## ✅ Acceptance criteria Wave 4.7.fix

- [ ] Branch `fix/wave4-7-fix-scf-migration` da main post-Wave 4.8
- [ ] Phase 1 sequenziale: investigation + atomic switch ACF→SCF + verifica
- [ ] Phase 2 PARALLEL: Task tool dispatch (seed + E2E) — pattern multi-agent
- [ ] Phase 3 sequenziale: documentation v2
- [ ] Phase 4 sequenziale: smoke regression + bump 1.3.6
- [ ] **`function_exists(acf_add_options_page)`: YES** (era NO)
- [ ] **Menu "Saltelli — Settings" visible** in admin sidebar
- [ ] **wp_options.options_* count: 60+** (era 26)
- [ ] **E2E update_field → frontend reflect: PASS** (vero fix)
- [ ] CPT metabox regression: PASS
- [ ] 21/21 smoke regression PASS
- [ ] Theme version `1.3.6-wave4-7-fix-scf-migration`
- [ ] Branch pushed (NO merge automatico)
- [ ] Report `.claude/knowledge/recovery/WAVE4-7-FIX-SCF-REPORT.md`

---

## 🚨 Cosa fare in caso di problemi

| Situazione | Action |
|---|---|
| Phase 1.4 verifica: `acf_add_options_page` ancora NO post-SCF activate | STOP, possibile SCF non installato correttamente. Riporta orchestratore + dump errors |
| Phase 1.5 CPT metabox regression: field tornano vuoti | STOP, rollback immediato + riporta orchestratore (incompatibilità SCF↔ACF JSON) |
| Phase 2 Agent B seed script: PHP error | STOP, debug script + riporta. Possibile JSON malformato |
| Phase 2 Agent C E2E: frontend NON reflect update_field | STOP, possibile cache ostinata. Verifica nginx/object cache + riporta |
| Phase 4 smoke regression < 21/21 PASS | Investigate URL specifici. Possibile regression introdotta SCF — STOP riporta |

**Rollback emergency** (qualsiasi step):
```bash
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp plugin deactivate secure-custom-fields --path=/var/www/saltelli && \
  sudo -u www-data wp plugin activate advanced-custom-fields --path=/var/www/saltelli && \
  sudo -u www-data wp cache flush --path=/var/www/saltelli"
```

E se serve restore DB completo:
```bash
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp db import ~/backups/wave4-7-fix-pre-switch-<TIMESTAMP>/db.sql --path=/var/www/saltelli"
```

---

## 🎯 Output expected

1. Branch `fix/wave4-7-fix-scf-migration` con 4+ commit (1 per phase)
2. File modificati / creati:
   - **NEW**: `wp-content/themes/saltelli/inc/seed-theme-options.php`
   - **MOD**: `wp-content/themes/saltelli/inc/acf-fields.php` (commento riga 24)
   - **MOD**: `wp-content/themes/saltelli/style.css` + `functions.php` (version bump)
   - **MOD**: `CLAUDE.md` + `EDITOR-HANDOFF.md` (v2 docs)
   - **DB ops**: switch plugin ACF→SCF + seed 30+ wp_options
3. Audit trail in `.claude/knowledge/audits/wave4-7-fix/`:
   - `phase1-pre-switch.txt`
   - `phase2-seed.txt` + `phase2-smoke-e2e.txt`
   - `regression/smoke.txt`
4. Report `.claude/knowledge/recovery/WAVE4-7-FIX-SCF-REPORT.md`
5. Theme version `1.3.6-wave4-7-fix-scf-migration`

L'orchestratore audisce + procede con merge `fix/wave4-7-fix → main` (no-ff) + tag `v1.3.6-wave4-7-fix-scf-migration` + deploy delta `--checksum` su staging.

**Critical**: post-merge le modifiche DB Phase 1-2 erano già su staging via SSH. Solo i 5 file modificati (acf-fields.php + seed-theme-options.php + style.css + functions.php + 2 docs) richiedono rsync delta.

---

## 🔗 Riferimenti

- **CMS Diagnosis Round 2 REPORT.md** (2026-05-08) — bug architetturale identificato
- DEC-029-COMPLETED (Wave 4.6 Theme Options cabling) — origine bug
- DEC-038 (Wave 4.8 closure) — last green state
- DEC-036 (Wave 4.7.1 + lesson rsync --checksum)
- DEC-021 (URL audit-aligned 17 cliente-firmato)
- `EDITOR-HANDOFF.md` v1 — riferimento Elena (verrà sostituito da v2)
- `CLAUDE.md` — single source of truth
- SCF docs: https://wordpress.org/plugins/secure-custom-fields/

---

## 📜 Note multi-agent pattern

Phase 2 richiede esplicitamente l'uso del **Task tool** per dispatch parallelo. Code main thread:

```
# In codice Code:
Task(
  description="Generate + execute idempotent seed script for 30+ missing wp_options",
  prompt="<full agent B context: read JSON, generate seed script, exec on staging, verify count>",
  subagent_type="general-purpose"
)

Task(
  description="Smoke E2E test admin menu visible + update_field → frontend reflect",
  prompt="<full agent C context: admin menu probe, E2E update_field, verify frontend, reset>",
  subagent_type="general-purpose"
)
```

Entrambi i Task() vengono lanciati nella **stessa risposta** (parallel dispatch). Code main attende entrambi i risultati prima di proseguire Phase 3.

Se Code non supporta Task tool per qualche motivo (versione vecchia / config), fallback a esecuzione **sequenziale** ma documentare nell'audit log "multi-agent fallback to sequential".
