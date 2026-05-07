# 🏁 Claude Code Agent — Wave 4.8 Cleanup + Migrations + UX Polish FINAL (v1.0)

> **Audience**: Claude Code agent in nuova sessione terminale dedicata.
> **Branch parent**: `main` (post-Wave 4.7.1 mergeata, tag `v1.3.4-wave4-7-1-acf-fix`)
> **Branch nuovo**: `feat/wave4-8-cleanup-migrations-ux-polish-final`
> **Theme version target**: `1.3.5-wave4-8-cleanup-final`
> **Scope**: chiusura definitiva di TUTTI i 14 items pending Wave 4.7 + 7 visual P2 polish + term rename. NESSUNO scope creep.
> **Tempo stimato**: ~3-4h (7 phases)
> **Riferimento**: DEC-035-COMPLETED (Wave 4.7), DEC-036 (Wave 4.7.1), Visual Audit + CMS Diagnosis 2026-05-07

---

## 🎯 Tu sei

Claude Code agent dedicato a **chiudere definitivamente** il gap CMS + visual residuo prima dell'handoff Elena. Wave 4.8 deve essere la **wave finale** prima del Wave 7 cut produzione (bloccato 6 decisioni cliente).

**Critical context**: Duccio (Project Lead) richiede esecuzione "definitiva" — nessun item pending residuo, nessun deferred a sessioni future. Dopo questa wave, il sito staging è **handoff-ready Elena al 100%**.

**Onestà tecnica obbligatoria**: se trovi blocker, riporta orchestratore. NON forzare fix. NON improvvisare. Pattern Wave 4.7 (HARD RULE STOP) è il modello da seguire — meglio safety di forced completion.

---

## 🔒 Hard rules (non negotiabili)

1. **NO commit su main**. Sempre su `feat/wave4-8-cleanup-migrations-ux-polish-final`.
2. **Phase 1 è READ-ONLY** (investigation finale). NO modifiche prima di Phase 2.
3. **Trash != force delete**. Le pages vanno in trash WP nativo (recoverable 30gg).
4. **Migration page → CPT**: NUOVI CPT competenza con `wp_insert_post` + ACF field copy + tassonomia term assign + page legacy → trash. ATOMICO per ogni page (rollback se step fallisce).
5. **Term slug rename**: usa `wp term update` o `$wpdb->update` su `wp_terms` + flush rewrite. NO modifica diretta SQL senza WP-CLI wrapper.
6. **NO regression Wave 5+6+4+4.5+4.6+4.7+4.7.1 smoke**. Ogni phase ha smoke gate.
7. **rsync `--checksum` mandatory** per deploy delta (DEC-036 lesson learned).
8. **NO modifiche a**: `wave5-blog-rewrites.php`, `inc/perf.php`, `inc/security.php`, `inc/critical-css.php`, `inc/wave4-6-migration.php`, CPT registration files.
9. **Idempotenza**: tutti gli script DB devono essere safely re-eseguibili.
10. **Acceptance criteria stringenti**: Phase 6 smoke deve mostrare 32/32 audit-aligned PASS (eliminando i 5 FAIL pre-existing) + 18/18 legacy redirects PASS.

---

## 📚 Letture obbligatorie (nell'ordine)

1. **`CLAUDE.md`** — single source of truth
2. **`prompts/PROMPT_AGENT_WAVE4_8_CLEANUP_FINAL.md`** (questo file) end-to-end
3. **`.claude/knowledge/recovery/WAVE4-7-CMS-HOTFIX-REPORT.md`** — Phase 1 investigation findings + 14 items STOP
4. **`.claude/knowledge/audits/wave4-7/q3-page-draft-content.txt`** — content size delle 11 page draft
5. **`.claude/knowledge/audits/wave4-7/q3b-cpt-competenza-publish-content.txt`** — cross-ref duplicate vs unique
6. **File da modificare** (lettura preliminare):
   - `wp-content/themes/saltelli/inc/cpt-competenza.php` (NON modificare, riferimento ACF group + tassonomia)
   - `wp-content/themes/saltelli/template-parts/page-aree-di-pratica-hub.php` (riferimento card "Per i privati")
   - `wp-content/themes/saltelli/page-templates/page-contatti.php` o `page.php` con template contatti (typo "Te qualsiasi" + "PRIMA INCONTRO")
   - `wp-content/themes/saltelli/page-templates/page-risorse.php` o template risorse-hub (counter "0 GUIDE")
   - `wp-content/themes/saltelli/single-competenza.php` (sub-header "napoli")
   - `wp-content/themes/saltelli/assets/css/sections.css` (TOC mobile collapse)
   - `wp-content/themes/saltelli/page-templates/page-glossario.php` (alphabetical jump nav)

---

## 📋 PHASE 1 — Investigation finale READ-ONLY (~30 min)

### 1.1 Backup completo + branch

```bash
mkdir -p ~/backups
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp db export ~/backups/saltelli-staging-pre-wave48-$(date +%Y%m%d-%H%M).sql --add-drop-table --path=/var/www/saltelli && \
  sudo tar czf ~/backups/saltelli-staging-theme-pre-wave48-$(date +%Y%m%d-%H%M).tar.gz -C /var/www/saltelli wp-content/themes/saltelli/"

cd ~/Desktop/DEV/saltelli-wp/
git fetch origin --prune
git checkout main
git pull --ff-only origin main   # → tag v1.3.4-wave4-7-1-acf-fix

# Verifica baseline
grep "^Version:" wp-content/themes/saltelli/style.css
# Atteso: Version: 1.3.4-wave4-7-1-acf-fix

git checkout -b feat/wave4-8-cleanup-migrations-ux-polish-final
mkdir -p .claude/knowledge/audits/wave4-8/
```

### 1.2 Re-investigation (sanity check pre-fix)

Esegui queste 6 query su staging:

```bash
mkdir -p .claude/knowledge/audits/wave4-8/

# Q1 — Verifica presenza 11 page draft confliggenti CPT (riconferma post-Wave 4.7)
echo "=== Q1: Page draft confliggenti (atteso: 11 publish-status=draft) ===" > .claude/knowledge/audits/wave4-8/investigation-pre.txt
ssh deploy@178.62.207.50 "sudo -u www-data wp post list --post_type=page \
  --post__in=202,208,170,223,232,260,279,288,297,2246,2251,19 \
  --post_status=any --fields=ID,post_status,post_name,post_title,post_modified \
  --format=table --path=/var/www/saltelli" \
  >> .claude/knowledge/audits/wave4-8/investigation-pre.txt

# Q2 — Content sizing per migration decision
echo "" >> .claude/knowledge/audits/wave4-8/investigation-pre.txt
echo "=== Q2: Content size detail (decisione migration vs trash) ===" >> .claude/knowledge/audits/wave4-8/investigation-pre.txt
for ID in 202 208 170 223 232 260 279 288 297 2246 2251 19; do
  ssh deploy@178.62.207.50 "sudo -u www-data wp eval '
    \$p = get_post($ID);
    if (\$p) {
      \$content_len = strlen(\$p->post_content);
      \$excerpt_len = strlen(\$p->post_excerpt);
      \$meta = get_post_meta($ID);
      \$meta_keys = array_keys(\$meta);
      \$acf_keys = array_filter(\$meta_keys, function(\$k) { return strpos(\$k, \"_\") !== 0; });
      echo \"ID $ID [\".\$p->post_status.\"] {$p->post_name} — content_len=\".\$content_len.\" excerpt_len=\".\$excerpt_len.\" acf_field_count=\".count(\$acf_keys).\"\\n\";
      if ($ID == 232 || $ID == 260) {
        echo \"  -- ACF FIELD KEYS (per migration mapping) --\\n\";
        foreach (\$acf_keys as \$k) echo \"  - \$k\\n\";
      }
    }
  ' --path=/var/www/saltelli" \
    >> .claude/knowledge/audits/wave4-8/investigation-pre.txt
done

# Q3 — Term tipo-area attuali (per rename)
echo "" >> .claude/knowledge/audits/wave4-8/investigation-pre.txt
echo "=== Q3: Term tipo-area state (atteso: contenzioso slug → contenzioso-amministrativo target) ===" >> .claude/knowledge/audits/wave4-8/investigation-pre.txt
ssh deploy@178.62.207.50 "sudo -u www-data wp term list tipo-area \
  --fields=term_id,name,slug,count --format=table --path=/var/www/saltelli" \
  >> .claude/knowledge/audits/wave4-8/investigation-pre.txt

# Q4 — CPT competenza Tier-2 esempio (per migration template)
echo "" >> .claude/knowledge/audits/wave4-8/investigation-pre.txt
echo "=== Q4: CPT competenza Tier-2 publish (template per migration) ===" >> .claude/knowledge/audits/wave4-8/investigation-pre.txt
ssh deploy@178.62.207.50 "sudo -u www-data wp post list --post_type=competenza \
  --post_status=publish --meta_key=is_tier_1 --meta_value=0 \
  --fields=ID,post_name,post_title --format=table --path=/var/www/saltelli" \
  >> .claude/knowledge/audits/wave4-8/investigation-pre.txt

# Q5 — Verifica file template visual P2 polish (esistenza)
echo "" >> .claude/knowledge/audits/wave4-8/investigation-pre.txt
echo "=== Q5: File template visual P2 polish ===" >> .claude/knowledge/audits/wave4-8/investigation-pre.txt
for f in wp-content/themes/saltelli/template-parts/page-contatti.php wp-content/themes/saltelli/template-parts/page-risorse.php wp-content/themes/saltelli/template-parts/page-aree-di-pratica-hub.php wp-content/themes/saltelli/single-competenza.php wp-content/themes/saltelli/template-parts/archive-blog.php wp-content/themes/saltelli/template-parts/page-glossario.php; do
  if [ -f "$f" ]; then
    echo "  ✓ $f" >> .claude/knowledge/audits/wave4-8/investigation-pre.txt
  else
    echo "  ✗ $f NON ESISTE" >> .claude/knowledge/audits/wave4-8/investigation-pre.txt
  fi
done

# Q6 — Localizza copy issues visual P2 (grep)
echo "" >> .claude/knowledge/audits/wave4-8/investigation-pre.txt
echo "=== Q6: Localizza visual P2 copy issues ===" >> .claude/knowledge/audits/wave4-8/investigation-pre.txt
echo "Te qualsiasi:" >> .claude/knowledge/audits/wave4-8/investigation-pre.txt
grep -rn "Te qualsiasi" wp-content/themes/saltelli/ --include="*.php" >> .claude/knowledge/audits/wave4-8/investigation-pre.txt 2>&1 || echo "  (nessun match — verificare ACF DB)" >> .claude/knowledge/audits/wave4-8/investigation-pre.txt

echo "" >> .claude/knowledge/audits/wave4-8/investigation-pre.txt
echo "PRIMA INCONTRO:" >> .claude/knowledge/audits/wave4-8/investigation-pre.txt
grep -rn "PRIMA INCONTRO\|prima incontro" wp-content/themes/saltelli/ --include="*.php" >> .claude/knowledge/audits/wave4-8/investigation-pre.txt 2>&1 || echo "  (nessun match — verificare ACF DB)" >> .claude/knowledge/audits/wave4-8/investigation-pre.txt

echo "" >> .claude/knowledge/audits/wave4-8/investigation-pre.txt
echo "0 GUIDE counter:" >> .claude/knowledge/audits/wave4-8/investigation-pre.txt
grep -rn "guide_pdf_counter\|guides_count\|0 GUIDE" wp-content/themes/saltelli/ --include="*.php" >> .claude/knowledge/audits/wave4-8/investigation-pre.txt 2>&1

echo "" >> .claude/knowledge/audits/wave4-8/investigation-pre.txt
echo "lowercase napoli amministrativo:" >> .claude/knowledge/audits/wave4-8/investigation-pre.txt
grep -rn "diritto-amministrativo\|amministrativo a napoli\|amministrativo a Napoli" wp-content/themes/saltelli/ --include="*.php" >> .claude/knowledge/audits/wave4-8/investigation-pre.txt 2>&1

cat .claude/knowledge/audits/wave4-8/investigation-pre.txt | tail -100
```

### 1.3 Decision tree post-investigation

Sulla base di Q1-Q2:
- Se 11 page draft sono ancora tutte presenti e content > 0: **OK, procedi Phase 2 trash**
- Se qualcuna è stata trashata da Wave 4.7: **skip quella in Phase 2**

Sulla base di Q3:
- Se term `contenzioso` esiste con slug `contenzioso`: **OK, procedi Phase 4 rename**
- Se term `contenzioso-amministrativo` esiste già: **skip rename, c'è altro problema**

Sulla base di Q4:
- Identifica un CPT competenza Tier-2 publish completo (es. "Cartelle esattoriali e multe") come **template** per migration page 232/260 → CPT

Sulla base di Q5-Q6:
- Verifica esistenza file template per Phase 5
- Identifica righe esatte per copy fix (oppure conferma che il fix è in ACF DB se grep non trova)

### 1.4 Commit Phase 1

```bash
git add .claude/knowledge/audits/wave4-8/
git commit -m "wave4-8: phase 1 — investigation read-only pre-cleanup-migration

Findings query 1-6:
- 12 page IDs verificate (11 draft confliggenti + page 19)
- Content size + ACF field count per migration decision (page 232 + 260)
- Term tipo-area state (rename target identificato)
- CPT competenza Tier-2 template (migration model)
- File template visual P2 polish identificati
- Copy issues localizzati (PHP + ACF DB)

Decision tree per Phase 2-5 documentato in investigation-pre.txt."
```

---

## 📋 PHASE 2 — CMS cleanup definitivo (~30 min)

### 2.1 Trash 9 page draft duplicate CPT (content duplicato verificato Q3b Wave 4.7)

```bash
DRAFT_DUPLICATE_IDS="202 208 170 223 279 288 297 2246 2251"

echo "=== Trash 9 page draft duplicate CPT ===" > .claude/knowledge/audits/wave4-8/phase2-cleanup.txt

for ID in $DRAFT_DUPLICATE_IDS; do
  echo "Trashing page ID $ID..." >> .claude/knowledge/audits/wave4-8/phase2-cleanup.txt
  ssh deploy@178.62.207.50 "sudo -u www-data wp post delete $ID --path=/var/www/saltelli" \
    >> .claude/knowledge/audits/wave4-8/phase2-cleanup.txt 2>&1
done

# Verifica trash
echo "" >> .claude/knowledge/audits/wave4-8/phase2-cleanup.txt
echo "=== Verifica trash status ===" >> .claude/knowledge/audits/wave4-8/phase2-cleanup.txt
for ID in $DRAFT_DUPLICATE_IDS; do
  ssh deploy@178.62.207.50 "sudo -u www-data wp post get $ID --field=post_status --path=/var/www/saltelli" \
    | sed "s/^/  ID $ID: /" >> .claude/knowledge/audits/wave4-8/phase2-cleanup.txt
done
# Atteso: 9 IDs con status=trash
```

### 2.2 Trash page 19 "Lo Studio" duplicate URL DEAD

```bash
echo "" >> .claude/knowledge/audits/wave4-8/phase2-cleanup.txt
echo "=== Trash page 19 (duplicate Lo Studio) ===" >> .claude/knowledge/audits/wave4-8/phase2-cleanup.txt
ssh deploy@178.62.207.50 "sudo -u www-data wp post delete 19 --path=/var/www/saltelli" \
  >> .claude/knowledge/audits/wave4-8/phase2-cleanup.txt 2>&1

# Verifica
ssh deploy@178.62.207.50 "sudo -u www-data wp post get 19 --field=post_status --path=/var/www/saltelli" \
  | sed "s/^/  ID 19: /" >> .claude/knowledge/audits/wave4-8/phase2-cleanup.txt
```

### 2.3 Verifica frontend page 2811 "Lo Studio" intatto post-trash 19

```bash
echo "" >> .claude/knowledge/audits/wave4-8/phase2-cleanup.txt
echo "=== Smoke /chi-siamo/lo-studio/ post-trash 19 ===" >> .claude/knowledge/audits/wave4-8/phase2-cleanup.txt
curl -s -o /dev/null -w "  HTTP %{http_code}\n" -L https://staging.studiolegalesaltelli.it/chi-siamo/lo-studio/ \
  >> .claude/knowledge/audits/wave4-8/phase2-cleanup.txt
# Atteso: 200
```

### 2.4 Aside CPT 2680 + 2681 — DECISION NON TRASH

CPT 2680 "Domiciliazione d'impresa" + 2681 "Consulenze online" sono publish con content reale ma NON sono nelle 17 cliente-firmate DEC-021. **Decisione orchestratore (Duccio): retain con marker `[Servizio extra]` finché cliente decide in Wave 7 review**.

```bash
echo "" >> .claude/knowledge/audits/wave4-8/phase2-cleanup.txt
echo "=== Marker [Servizio extra] su CPT 2680 + 2681 (decisione cliente Wave 7) ===" >> .claude/knowledge/audits/wave4-8/phase2-cleanup.txt
for ID in 2680 2681; do
  current_title=$(ssh deploy@178.62.207.50 "sudo -u www-data wp post get $ID --field=post_title --path=/var/www/saltelli")
  # Skip se già marker
  if echo "$current_title" | grep -q "\[Servizio extra\]"; then
    echo "  ID $ID già marker, skip" >> .claude/knowledge/audits/wave4-8/phase2-cleanup.txt
    continue
  fi
  ssh deploy@178.62.207.50 "sudo -u www-data wp post update $ID \
    --post_title='[Servizio extra] $current_title' \
    --post_status=draft \
    --path=/var/www/saltelli" \
    | sed "s/^/  ID $ID: /" >> .claude/knowledge/audits/wave4-8/phase2-cleanup.txt
done
```

### 2.5 Commit Phase 2

```bash
git add .claude/knowledge/audits/wave4-8/
git commit -m "wave4-8: phase 2 — CMS cleanup definitivo (10 trash + 2 marker)

Trash applicato (recoverable trash WP 30gg):
- 9 page draft duplicate CPT competenza (IDs 202, 208, 170, 223, 279, 288, 297, 2246, 2251)
  Content verificato Q3b Wave 4.7 = duplicato del CPT publish corrispondente, safe trash
- Page ID 19 'Lo Studio' duplicate URL DEAD
  Content era 2856 char ma su URL nested 3-level non risolvibile (/chi-siamo/lo-studio/lo-studio/)
  Page 2811 (URL canonical /chi-siamo/lo-studio/) è la vera Lo Studio, con timeline + founding ACF Wave 4.6

Marker '[Servizio extra]' applicato (decisione cliente Wave 7):
- CPT 2680 'Domiciliazione d'impresa' (servizio extra non tra le 17 cliente-firmate DEC-021)
- CPT 2681 'Consulenze online' (idem)
Strategia: marker visibile + status=draft per evitare frontend leak finché cliente decide.

Verifica frontend post-cleanup: /chi-siamo/lo-studio/ → 200 OK (page 2811 intatto)."
```

---

## 📋 PHASE 3 — Migration content page → CPT competenza (~60 min)

### 3.1 Migration page 232 "Infortunistica stradale" → CPT competenza Tier-2 privati

Pattern atomico (rollback se fallisce):

```bash
echo "=== Migration page 232 → CPT competenza ===" > .claude/knowledge/audits/wave4-8/phase3-migration.txt

ssh deploy@178.62.207.50 "sudo -u www-data wp eval '
  // Step 1: Source data
  \$source_id = 232;
  \$source = get_post(\$source_id);
  if (!\$source || \$source->post_status === \"trash\") {
    echo \"ERROR: source page 232 non disponibile\\n\";
    exit;
  }

  echo \"Source: ID {\$source_id} title=\\\"{\$source->post_title}\\\" content_len=\".strlen(\$source->post_content).\"\\n\";

  // Step 2: Check if CPT already exists (idempotency)
  \$existing = get_posts([\"post_type\" => \"competenza\", \"name\" => \"infortunistica-stradale\", \"post_status\" => \"any\", \"numberposts\" => 1]);
  if (!empty(\$existing)) {
    echo \"WARN: CPT competenza con slug infortunistica-stradale già esiste (ID {\$existing[0]->ID}). Skip migration.\\n\";
    exit;
  }

  // Step 3: Create new CPT
  \$new_id = wp_insert_post([
    \"post_type\" => \"competenza\",
    \"post_status\" => \"publish\",
    \"post_title\" => \"Infortunistica stradale\",
    \"post_name\" => \"infortunistica-stradale\",
    \"post_content\" => \$source->post_content,
    \"post_excerpt\" => \$source->post_excerpt,
  ]);

  if (is_wp_error(\$new_id)) {
    echo \"ERROR creating CPT: \" . \$new_id->get_error_message() . \"\\n\";
    exit;
  }

  echo \"Created CPT competenza ID {\$new_id}\\n\";

  // Step 4: Assign tassonomia tipo-area = privati
  \$result = wp_set_post_terms(\$new_id, [\"privati\"], \"tipo-area\", false);
  if (is_wp_error(\$result)) {
    echo \"ERROR assigning term: \" . \$result->get_error_message() . \"\\n\";
    wp_delete_post(\$new_id, true);
    exit;
  }
  echo \"Assigned term tipo-area=privati\\n\";

  // Step 5: Copy ACF fields (excluding _ prefixed and post_ system meta)
  \$source_meta = get_post_meta(\$source_id);
  \$copied = 0;
  foreach (\$source_meta as \$key => \$values) {
    if (strpos(\$key, \"_\") === 0) continue; // skip ACF reference fields
    if (in_array(\$key, [\"_edit_lock\", \"_edit_last\", \"_wp_page_template\", \"_yoast_wpseo_focuskw\"])) continue;
    foreach (\$values as \$v) {
      add_post_meta(\$new_id, \$key, maybe_unserialize(\$v));
    }
    \$copied++;
  }
  echo \"Copied {\$copied} ACF fields\\n\";

  // Step 6: Set is_tier_1 = 0 (Tier-2)
  update_post_meta(\$new_id, \"is_tier_1\", \"0\");
  echo \"Set is_tier_1=0 (Tier-2)\\n\";

  // Step 7: Trash source page
  \$trashed = wp_delete_post(\$source_id, false);
  if (\$trashed) {
    echo \"Trashed source page {\$source_id}\\n\";
  } else {
    echo \"WARN: trash source failed (manual cleanup needed)\\n\";
  }

  echo \"\\nMIGRATION COMPLETED: page 232 → CPT competenza ID {\$new_id}\\n\";
  echo \"URL atteso: /aree-di-pratica/privati/infortunistica-stradale/\\n\";
' --path=/var/www/saltelli" \
  >> .claude/knowledge/audits/wave4-8/phase3-migration.txt 2>&1

# Verifica URL nuovo
echo "" >> .claude/knowledge/audits/wave4-8/phase3-migration.txt
echo "=== Smoke URL post-migration page 232 ===" >> .claude/knowledge/audits/wave4-8/phase3-migration.txt

# Flush rewrite prima di smoke
ssh deploy@178.62.207.50 "cd /var/www/saltelli && sudo -u www-data wp rewrite flush --path=/var/www/saltelli"

curl -s -o /dev/null -w "  HTTP %{http_code} → /aree-di-pratica/privati/infortunistica-stradale/\n" -L \
  https://staging.studiolegalesaltelli.it/aree-di-pratica/privati/infortunistica-stradale/ \
  >> .claude/knowledge/audits/wave4-8/phase3-migration.txt
# Atteso: 200
```

### 3.2 Migration page 260 "Aste immobiliari" → CPT competenza (stesso pattern)

```bash
echo "" >> .claude/knowledge/audits/wave4-8/phase3-migration.txt
echo "=== Migration page 260 → CPT competenza ===" >> .claude/knowledge/audits/wave4-8/phase3-migration.txt

ssh deploy@178.62.207.50 "sudo -u www-data wp eval '
  \$source_id = 260;
  \$source = get_post(\$source_id);
  if (!\$source || \$source->post_status === \"trash\") {
    echo \"ERROR: source page 260 non disponibile\\n\";
    exit;
  }

  \$existing = get_posts([\"post_type\" => \"competenza\", \"name\" => \"aste-immobiliari\", \"post_status\" => \"any\", \"numberposts\" => 1]);
  if (!empty(\$existing)) {
    echo \"WARN: CPT competenza con slug aste-immobiliari già esiste (ID {\$existing[0]->ID}). Skip.\\n\";
    exit;
  }

  \$new_id = wp_insert_post([
    \"post_type\" => \"competenza\",
    \"post_status\" => \"publish\",
    \"post_title\" => \"Aste immobiliari\",
    \"post_name\" => \"aste-immobiliari\",
    \"post_content\" => \$source->post_content,
    \"post_excerpt\" => \$source->post_excerpt,
  ]);

  if (is_wp_error(\$new_id)) { echo \"ERROR creating CPT: \" . \$new_id->get_error_message(); exit; }
  echo \"Created CPT competenza ID {\$new_id}\\n\";

  wp_set_post_terms(\$new_id, [\"privati\"], \"tipo-area\", false);

  \$source_meta = get_post_meta(\$source_id);
  \$copied = 0;
  foreach (\$source_meta as \$key => \$values) {
    if (strpos(\$key, \"_\") === 0) continue;
    if (in_array(\$key, [\"_edit_lock\", \"_edit_last\", \"_wp_page_template\", \"_yoast_wpseo_focuskw\"])) continue;
    foreach (\$values as \$v) add_post_meta(\$new_id, \$key, maybe_unserialize(\$v));
    \$copied++;
  }
  echo \"Copied {\$copied} ACF fields\\n\";

  update_post_meta(\$new_id, \"is_tier_1\", \"0\");
  wp_delete_post(\$source_id, false);

  echo \"MIGRATION COMPLETED: page 260 → CPT competenza ID {\$new_id}\\n\";
' --path=/var/www/saltelli" \
  >> .claude/knowledge/audits/wave4-8/phase3-migration.txt 2>&1

# Flush rewrite + smoke
ssh deploy@178.62.207.50 "cd /var/www/saltelli && sudo -u www-data wp rewrite flush --path=/var/www/saltelli"

curl -s -o /dev/null -w "  HTTP %{http_code} → /aree-di-pratica/privati/aste-immobiliari/\n" -L \
  https://staging.studiolegalesaltelli.it/aree-di-pratica/privati/aste-immobiliari/ \
  >> .claude/knowledge/audits/wave4-8/phase3-migration.txt
# Atteso: 200
```

### 3.3 Verifica ACF field popolati su nuovi CPT

```bash
echo "" >> .claude/knowledge/audits/wave4-8/phase3-migration.txt
echo "=== Verifica ACF field nuovi CPT ===" >> .claude/knowledge/audits/wave4-8/phase3-migration.txt

ssh deploy@178.62.207.50 "sudo -u www-data wp post list \
  --post_type=competenza --name=infortunistica-stradale,aste-immobiliari \
  --fields=ID,post_status,post_name,post_title --format=table --path=/var/www/saltelli" \
  >> .claude/knowledge/audits/wave4-8/phase3-migration.txt
```

### 3.4 Commit Phase 3

```bash
git add .claude/knowledge/audits/wave4-8/
git commit -m "wave4-8: phase 3 — migration content page → CPT competenza (Tier-2 privati)

Migration atomica eseguita per 2 page Tier-1 mai migrate a CPT (Wave 5 IA refactor incompleto):

1. Page 232 'Infortunistica stradale' → CPT competenza Tier-2 privati
   - content_len 2531 char preservato
   - ACF fields copied (escluso _system meta)
   - tassonomia tipo-area=privati assigned
   - is_tier_1=0 (Tier-2)
   - source page 232 → trash (recoverable)
   - URL atteso: /aree-di-pratica/privati/infortunistica-stradale/ → 200

2. Page 260 'Aste immobiliari' → CPT competenza Tier-2 privati
   - content_len 1731 char preservato
   - Stesso pattern atomico
   - URL atteso: /aree-di-pratica/privati/aste-immobiliari/ → 200

Pattern idempotente: skip se CPT con stesso slug già esiste.
Rollback automatico: se step term assign fallisce, CPT cancellato.

Closes 2 dei 5 FAIL smoke pre-existing post-Wave 4.7.
Allinea le 17 competenze cliente-firmate DEC-021 a 17 CPT effettivi (era 15 + 2 page draft)."
```

---

## 📋 PHASE 4 — Term tipo-area rename (~15 min)

### 4.1 Rename term `contenzioso` → `contenzioso-amministrativo`

```bash
echo "=== Phase 4: Term tipo-area rename ===" > .claude/knowledge/audits/wave4-8/phase4-term-rename.txt

# Verifica state pre-rename
ssh deploy@178.62.207.50 "sudo -u www-data wp term list tipo-area \
  --fields=term_id,name,slug,count --format=table --path=/var/www/saltelli" \
  >> .claude/knowledge/audits/wave4-8/phase4-term-rename.txt

# Trova term_id di "contenzioso"
TERM_ID=$(ssh deploy@178.62.207.50 "sudo -u www-data wp term list tipo-area \
  --fields=term_id,slug --format=csv --path=/var/www/saltelli" \
  | grep "contenzioso$" | cut -d',' -f1 | head -1)

if [ -z "$TERM_ID" ]; then
  echo "ERROR: term 'contenzioso' non trovato. STOP." >> .claude/knowledge/audits/wave4-8/phase4-term-rename.txt
  exit 1
fi

echo "" >> .claude/knowledge/audits/wave4-8/phase4-term-rename.txt
echo "Term ID 'contenzioso' identificato: $TERM_ID" >> .claude/knowledge/audits/wave4-8/phase4-term-rename.txt

# Rename: slug + name
ssh deploy@178.62.207.50 "sudo -u www-data wp term update tipo-area $TERM_ID \
  --name='Contenzioso amministrativo' \
  --slug='contenzioso-amministrativo' \
  --path=/var/www/saltelli" \
  >> .claude/knowledge/audits/wave4-8/phase4-term-rename.txt 2>&1

# Verifica state post-rename
echo "" >> .claude/knowledge/audits/wave4-8/phase4-term-rename.txt
echo "=== State post-rename ===" >> .claude/knowledge/audits/wave4-8/phase4-term-rename.txt
ssh deploy@178.62.207.50 "sudo -u www-data wp term list tipo-area \
  --fields=term_id,name,slug,count --format=table --path=/var/www/saltelli" \
  >> .claude/knowledge/audits/wave4-8/phase4-term-rename.txt
```

### 4.2 Flush rewrite + smoke verifica

```bash
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp rewrite flush --path=/var/www/saltelli && \
  sudo -u www-data wp cache flush --path=/var/www/saltelli && \
  sudo -u www-data wp transient delete --all --path=/var/www/saltelli"

# Smoke URL post-rename
echo "" >> .claude/knowledge/audits/wave4-8/phase4-term-rename.txt
echo "=== Smoke URL post-rename + flush ===" >> .claude/knowledge/audits/wave4-8/phase4-term-rename.txt

for url in "/aree-di-pratica/contenzioso-amministrativo/" "/aree-di-pratica/contenzioso-amministrativo/diritto-amministrativo/" "/tipo-area/contenzioso/" "/tipo-area/contenzioso-amministrativo/"; do
  code=$(curl -s -o /dev/null -w "%{http_code}" -L "https://staging.studiolegalesaltelli.it$url" --max-time 10)
  echo "  [$code] $url" >> .claude/knowledge/audits/wave4-8/phase4-term-rename.txt
done

# Atteso:
# 200 /aree-di-pratica/contenzioso-amministrativo/  ← era 404 pre-rename
# 200 /aree-di-pratica/contenzioso-amministrativo/diritto-amministrativo/
# 301/404 /tipo-area/contenzioso/  ← old slug, redirect WP automatico se WP gestisce
# 200 /tipo-area/contenzioso-amministrativo/  ← new term canonical
```

### 4.3 Commit Phase 4

```bash
git add .claude/knowledge/audits/wave4-8/
git commit -m "wave4-8: phase 4 — term tipo-area rename 'contenzioso' → 'contenzioso-amministrativo'

Rename surgical via wp term update:
- term_id <ID>
- name: 'Contenzioso' → 'Contenzioso amministrativo'
- slug: 'contenzioso' → 'contenzioso-amministrativo'

Allinea taxonomy slug a hub PHP (template-parts/page-aree-di-pratica-hub.php già linka 'contenzioso-amministrativo').

Closes 1 dei 5 FAIL smoke pre-existing:
- Pre-rename: /aree-di-pratica/contenzioso-amministrativo/ → 404
- Post-rename: /aree-di-pratica/contenzioso-amministrativo/ → 200

Flush rewrite + cache + transient eseguito post-rename.

NOTA: WP gestisce automaticamente redirect old_slug → new_slug via wp_old_slug_redirect()
sui term canonical /tipo-area/{slug}/. Verifica live: smoke test confermerà comportamento."
```

---

## 📋 PHASE 5 — Visual P2 polish raggruppato (~45 min)

### 5.1 Fix MED-04 + MED-05: typo contatti

#### Trova file template contatti

```bash
grep -rn "PRIMA INCONTRO\|prima incontro\|Te qualsiasi" wp-content/themes/saltelli/ --include="*.php" --include="*.json"
```

Atteso: match in `template-parts/page-contatti.php` o equivalente, OR nei default_value ACF JSON.

#### Surgical replace nel/i file individuati

```bash
# Esempio (adatta basato su grep output)
sed -i.bak 's/PRIMA INCONTRO/PRIMO INCONTRO/g' wp-content/themes/saltelli/template-parts/page-contatti.php
sed -i.bak 's/Te qualsiasi momento/In qualsiasi momento/g' wp-content/themes/saltelli/template-parts/page-contatti.php

# Se è in ACF JSON (default_value):
sed -i.bak 's/PRIMA INCONTRO/PRIMO INCONTRO/g' wp-content/themes/saltelli/acf-json/group_*.json
sed -i.bak 's/Te qualsiasi momento/In qualsiasi momento/g' wp-content/themes/saltelli/acf-json/group_*.json

# Cleanup .bak
find wp-content/themes/saltelli/ -name "*.bak" -delete

# Verifica fix
grep -rn "PRIMA INCONTRO\|Te qualsiasi" wp-content/themes/saltelli/ --include="*.php" --include="*.json" || echo "✓ Fix copy contatti OK"
```

### 5.2 Fix MED-09: lowercase "napoli" sub-header amministrativo

```bash
# Identifica file e fix
grep -rn "amministrativo a napoli\|Diritto Amministrativo a napoli" wp-content/themes/saltelli/ --include="*.php" --include="*.json"

# Surgical (adatta path e stringa esatta)
# Esempio in single-competenza.php o ACF default
sed -i.bak 's/amministrativo a napoli/amministrativo a Napoli/g' wp-content/themes/saltelli/single-competenza.php
sed -i.bak 's/amministrativo a napoli/amministrativo a Napoli/g' wp-content/themes/saltelli/acf-json/*.json

find wp-content/themes/saltelli/ -name "*.bak" -delete
```

### 5.3 Fix MED-06: "0 GUIDE" empty state non hidden

Trova template risorse-hub e modifica:

```bash
# Identifica file
grep -rn "guide\|GUIDE" wp-content/themes/saltelli/template-parts/page-risorse.php | head -5
```

Modifica con `if ($count > 0)` (sintassi adattata al template):

```php
// PRIMA:
<span class="sl-card__counter"><?php echo $guide_count; ?> GUIDE</span>

// DOPO:
<?php if ($guide_count > 0): ?>
  <span class="sl-card__counter"><?php echo $guide_count; ?> GUIDE</span>
<?php else: ?>
  <span class="sl-card__counter sl-card__counter--placeholder">In arrivo</span>
<?php endif; ?>
```

### 5.4 Fix MED-08: card "Per i privati" body editorial

In `template-parts/page-aree-di-pratica-hub.php`, riga ~card "Per i privati":

```bash
grep -n "Per i privati\|persone fisiche" wp-content/themes/saltelli/template-parts/page-aree-di-pratica-hub.php
```

Editorial pass: chiarire taxonomy. Esempio:

```php
// PRIMA (concept-mix):
"Famiglia, persone fisiche, lavoratori, Tributario, lavoro, famiglia LGBTQ+, successioni..."

// DOPO (taxonomy chiara):
"Per famiglie e persone: tributario, lavoro, famiglia LGBTQ+, successioni, infortunistica..."
```

(Se è ACF default_value: fix in `acf-json/group_*.json`)

### 5.5 Fix MED-10: titolo blog "Il responsabilità etica" → "La responsabilità etica"

Questo è in DB (post_title di un blog post specifico). Non è codice. Verifica:

```bash
ssh deploy@178.62.207.50 "sudo -u www-data wp post list --post_type=post \
  --post_status=publish --s='Il responsabilità' \
  --fields=ID,post_title --format=table --path=/var/www/saltelli"
```

Se trovato, fix DB:

```bash
ssh deploy@178.62.207.50 "sudo -u www-data wp post update <BLOG_POST_ID> \
  --post_title='La responsabilità etica della professione: come sembrare per il diritto di famiglia' \
  --path=/var/www/saltelli"
```

(NOTA: editorial debt minor — anche "come sembrare per" è strano, ma fuori scope. Solo fix typo articolo.)

### 5.6 Fix MED-02: TOC mobile collapse Tier-1

In `assets/css/sections.css`, aggiungi media query per nascondere/collapse TOC mobile:

```bash
# Identifica selector TOC
grep -n "sl-toc\|toc-nav\|table-of-contents" wp-content/themes/saltelli/assets/css/sections.css | head -5
```

Aggiungi (al fondo del file o in sezione mobile esistente):

```css
/* Wave 4.8 — TOC mobile collapse Tier-1 (visual P2 fix MED-02) */
@media (max-width: 767px) {
  .sl-toc,
  .sl-tier1__toc {
    display: none; /* o usa <details> + CSS per collapse interactive */
  }
}
```

### 5.7 Fix MED-11: Glossario alphabetical jump nav (NEW component)

Aggiungi sticky alphabetical jump nav in `template-parts/page-glossario.php` o equivalente:

```php
<?php
// Wave 4.8 — Alphabetical jump nav (visual P2 fix MED-11)
$letters = range('A', 'Z');
?>
<nav class="sl-glossario__jumpnav" aria-label="Salta a lettera">
  <?php foreach ($letters as $letter): ?>
    <a href="#letter-<?php echo $letter; ?>" class="sl-glossario__jumpnav-letter"><?php echo $letter; ?></a>
  <?php endforeach; ?>
</nav>
```

CSS in `sections.css`:

```css
/* Wave 4.8 — Glossario jump nav sticky (MED-11) */
.sl-glossario__jumpnav {
  position: sticky;
  top: 80px;
  background: var(--sl-color-cream);
  padding: 12px 16px;
  border-bottom: 1px solid var(--sl-color-bronze);
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  z-index: 10;
}
.sl-glossario__jumpnav-letter {
  font-family: var(--sl-font-mono);
  font-size: 13px;
  letter-spacing: 0.05em;
  color: var(--sl-color-navy);
  text-decoration: none;
  padding: 4px 8px;
  border-radius: 4px;
}
.sl-glossario__jumpnav-letter:hover {
  background: var(--sl-color-bronze);
  color: var(--sl-color-cream);
}
```

E add anchor IDs ai blocchi A-Z nel template glossario:

```php
<section id="letter-<?php echo strtoupper($letter); ?>" class="sl-glossario__section">
```

### 5.8 Smoke verify visual P2

```bash
echo "=== Phase 5 smoke verify ===" > .claude/knowledge/audits/wave4-8/phase5-visual-p2.txt

# MED-04 + MED-05: contatti copy
curl -s https://staging.studiolegalesaltelli.it/contatti/ | grep -oE "PRIMO INCONTRO|PRIMA INCONTRO|In qualsiasi momento|Te qualsiasi momento" | sort -u \
  >> .claude/knowledge/audits/wave4-8/phase5-visual-p2.txt
# Atteso: solo "PRIMO INCONTRO" e "In qualsiasi momento"

# MED-09: napoli capitalization
curl -s https://staging.studiolegalesaltelli.it/aree-di-pratica/contenzioso-amministrativo/diritto-amministrativo/ | grep -oE "amministrativo a [Nn]apoli" | sort -u \
  >> .claude/knowledge/audits/wave4-8/phase5-visual-p2.txt
# Atteso: solo "amministrativo a Napoli"

# (MED-06 / MED-08 / MED-10 / MED-02 / MED-11: verifica visiva post-deploy reale)
```

### 5.9 Commit Phase 5

```bash
git add wp-content/themes/saltelli/
git commit -m "wave4-8: phase 5 — visual P2 polish raggruppato (7 medi visual audit)

Closes 7 visual P2 findings da Visual Audit 2026-05-07:

MED-04 contatti: 'Te qualsiasi momento' → 'In qualsiasi momento'
MED-05 contatti: 'PRIMA INCONTRO GRATUITO' → 'PRIMO INCONTRO GRATUITO'
MED-06 risorse-hub: '0 GUIDE' empty state → conditional render 'In arrivo'
MED-08 aree-hub: card 'Per i privati' body editorial pass (chiarezza taxonomy)
MED-09 amministrativo: lowercase 'napoli' → 'Napoli'
MED-10 blog: titolo featured 'Il responsabilità' → 'La responsabilità'
MED-02 Tier-1: TOC mobile collapse <768px (CSS @media display:none)
MED-11 glossario: alphabetical jump nav sticky A-Z (NEW component)

File modificati:
- template-parts/page-contatti.php (typo)
- template-parts/page-risorse.php (empty state)
- template-parts/page-aree-di-pratica-hub.php (card editorial)
- single-competenza.php OR acf-json/ (napoli capitalization)
- template-parts/page-glossario.php (jump nav)
- assets/css/sections.css (TOC mobile + jump nav styling)
- DB: blog post title fix via wp post update (typo articolo)

Pattern surgical: 1 fix per finding, no scope creep."
```

---

## 📋 PHASE 6 — Smoke regression + bump version + report (~30 min)

### 6.1 Smoke regression Wave 5+6+4+4.5+4.6+4.7+4.7.1

```bash
mkdir -p .claude/knowledge/audits/wave4-8/regression/

# Smoke audit-aligned 32 URL (atteso: 32/32 PASS post-Wave 4.8)
echo "=== Wave 4.8 audit-aligned smoke ===" > .claude/knowledge/audits/wave4-8/regression/audit-aligned.txt

URLS_AUDIT=(
  "/" "/chi-siamo/" "/chi-siamo/lo-studio/" "/chi-siamo/team/"
  "/chi-siamo/team/antonia-battista/" "/chi-siamo/team/emiliano-saltelli/"
  "/chi-siamo/risultati/"
  "/aree-di-pratica/"
  "/aree-di-pratica/privati/diritto-tributario/"
  "/aree-di-pratica/privati/cartelle-esattoriali-e-multe/"
  "/aree-di-pratica/privati/diritto-del-lavoro/"
  "/aree-di-pratica/privati/diritto-di-famiglia-lgbtq/"
  "/aree-di-pratica/privati/responsabilita-medica/"
  "/aree-di-pratica/privati/diritto-bancario/"
  "/aree-di-pratica/privati/diritto-condominiale/"
  "/aree-di-pratica/privati/diritto-penale/"
  "/aree-di-pratica/privati/diritto-previdenziale/"
  "/aree-di-pratica/privati/eredita-e-successioni/"
  "/aree-di-pratica/privati/risarcimento-danni/"
  "/aree-di-pratica/privati/cartelle-esattoriali-e-multe/"
  "/aree-di-pratica/privati/infortunistica-stradale/"
  "/aree-di-pratica/privati/aste-immobiliari/"
  "/aree-di-pratica/imprese/recupero-crediti/"
  "/aree-di-pratica/imprese/diritto-dell-immigrazione/"
  "/aree-di-pratica/contenzioso-amministrativo/"
  "/aree-di-pratica/contenzioso-amministrativo/diritto-amministrativo/"
  "/risorse/" "/risorse/blog/" "/risorse/domande-frequenti/"
  "/risorse/glossario-legale/"
  "/costi-e-consulenze/" "/contatti/"
)

PASS=0; FAIL=0
for url in "${URLS_AUDIT[@]}"; do
  code=$(curl -s -o /dev/null -w "%{http_code}" -L "https://staging.studiolegalesaltelli.it$url" --max-time 10)
  if [ "$code" = "200" ]; then
    echo "  ✅ [$code] $url" >> .claude/knowledge/audits/wave4-8/regression/audit-aligned.txt
    PASS=$((PASS+1))
  else
    echo "  ❌ [$code] $url" >> .claude/knowledge/audits/wave4-8/regression/audit-aligned.txt
    FAIL=$((FAIL+1))
  fi
done

echo "" >> .claude/knowledge/audits/wave4-8/regression/audit-aligned.txt
echo "TOTAL: $PASS/${#URLS_AUDIT[@]} PASS · $FAIL FAIL" >> .claude/knowledge/audits/wave4-8/regression/audit-aligned.txt

# Atteso: ~32/32 PASS (i 5 FAIL pre-existing devono essere PASS dopo Wave 4.8)
```

### 6.2 Verifica DB post-Wave 4.8

```bash
ssh deploy@178.62.207.50 "cd /var/www/saltelli && sudo -u www-data wp eval '
  global \$wpdb;

  // Page draft conflict count (atteso: 0 publish/draft, 11 trash)
  \$conflict_publish_draft = \$wpdb->get_var(\"SELECT COUNT(*) FROM {\$wpdb->posts} WHERE post_type=\\\"page\\\" AND post_name IN (\\\"diritto-tributario\\\",\\\"cartelle-esattoriali-e-multe\\\",\\\"recupero-crediti\\\",\\\"risarcimento-del-danno\\\",\\\"infortunistica-stradale\\\",\\\"aste-immobiliari\\\",\\\"responsabilita-medica\\\",\\\"eredita-e-successioni\\\",\\\"diritto-bancario\\\",\\\"diritto-amministrativo\\\",\\\"diritto-penale\\\") AND post_status IN (\\\"publish\\\",\\\"draft\\\")\");
  echo \"Page conflict CPT (publish/draft): \$conflict_publish_draft\\n\";

  // CPT competenza count (atteso: 17 publish + N draft surplus marker)
  \$comp_publish = \$wpdb->get_var(\"SELECT COUNT(*) FROM {\$wpdb->posts} WHERE post_type=\\\"competenza\\\" AND post_status=\\\"publish\\\"\");
  \$comp_draft = \$wpdb->get_var(\"SELECT COUNT(*) FROM {\$wpdb->posts} WHERE post_type=\\\"competenza\\\" AND post_status=\\\"draft\\\"\");
  echo \"CPT competenza: publish=\$comp_publish | draft=\$comp_draft\\n\";

  // Term tipo-area
  \$terms = get_terms([\"taxonomy\" => \"tipo-area\", \"hide_empty\" => false]);
  echo \"Term tipo-area:\\n\";
  foreach (\$terms as \$t) echo \"  - {\$t->slug} (count=\$t->count)\\n\";

  // 'Diciotto/Diciannove' user-facing residue
  \$pc = \$wpdb->get_var(\"SELECT COUNT(*) FROM {\$wpdb->posts} WHERE post_content REGEXP \\\"[Dd]ici(otto|annove)\\\" AND post_type IN (\\\"page\\\",\\\"competenza\\\",\\\"avvocato\\\",\\\"saltelli_caso\\\")\");
  \$pm = \$wpdb->get_var(\"SELECT COUNT(*) FROM {\$wpdb->postmeta} WHERE meta_value REGEXP \\\"[Dd]ici(otto|annove)\\\"\");
  \$opt = \$wpdb->get_var(\"SELECT COUNT(*) FROM {\$wpdb->options} WHERE option_value REGEXP \\\"[Dd]ici(otto|annove)\\\"\");
  echo \"Residue: post_content=\$pc | postmeta=\$pm | options=\$opt\\n\";
' --path=/var/www/saltelli" \
  > .claude/knowledge/audits/wave4-8/regression/db-state.txt

cat .claude/knowledge/audits/wave4-8/regression/db-state.txt

# Atteso:
# Page conflict CPT (publish/draft): 0
# CPT competenza: publish=17 | draft=N (marker surplus + Servizio extra)
# Term tipo-area: privati, imprese, contenzioso-amministrativo (NO 'contenzioso')
# Residue: post_content=0 | postmeta=0 | options=0
```

### 6.3 Security headers verify

```bash
echo "=== Security headers ===" > .claude/knowledge/audits/wave4-8/regression/headers.txt
curl -sI https://staging.studiolegalesaltelli.it/ \
  | grep -iE "^(x-frame|x-content-type|referrer-policy|permissions-policy|strict-transport)" \
  | tee -a .claude/knowledge/audits/wave4-8/regression/headers.txt

# Atteso: 5/5
```

### 6.4 Bump theme version 1.3.5

```bash
sed -i.bak 's/^Version: 1.3.4-wave4-7-1-acf-fix/Version: 1.3.5-wave4-8-cleanup-final/' \
  wp-content/themes/saltelli/style.css

sed -i.bak "s/define('SALTELLI_VERSION', '1.3.4-wave4-7-1-acf-fix')/define('SALTELLI_VERSION', '1.3.5-wave4-8-cleanup-final')/" \
  wp-content/themes/saltelli/functions.php

find wp-content/themes/saltelli/ -name "*.bak" -delete

grep "^Version:" wp-content/themes/saltelli/style.css
grep "SALTELLI_VERSION" wp-content/themes/saltelli/functions.php
```

### 6.5 Report finale

Crea `.claude/knowledge/recovery/WAVE4-8-CLEANUP-FINAL-REPORT.md`:

```markdown
# Wave 4.8 — Cleanup + Migrations + UX Polish FINAL · Report

**Branch**: feat/wave4-8-cleanup-migrations-ux-polish-final
**Theme version**: 1.3.5-wave4-8-cleanup-final
**Generated**: <ISO timestamp>
**Phases**: 6/6 (+ Phase 7 push)

## Executive summary

Wave 4.8 chiusura definitiva 14 items pending Wave 4.7 + 7 visual P2 polish.
Sito staging ora handoff-ready Elena al 100%, gap residui solo editorial debt cliente.

## Risultati per Phase

[ ... dettaglio Phase 1-6 con metriche ... ]

## Acceptance criteria

- ✅ 32/32 audit-aligned smoke PASS
- ✅ 18/18 legacy redirects PASS  
- ✅ 5/5 security headers
- ✅ 0 page draft conflict CPT
- ✅ 17 CPT competenza publish (allineato DEC-021)
- ✅ Term tipo-area slug rinominato
- ✅ DB residue 'diciotto/diciannove' = 0
- ✅ NO regression Wave 5+6+4+4.5+4.6+4.7+4.7.1

## Open items post-Wave 4.8

- Editorial debt cliente (foto avvocati, testimonials, bio, CF7 form)
- 6 decisioni cliente Wave 7 (cut produzione)
- Wave 4.9 placeholder avvocati silhouette (OPZIONALE finché cliente foto)
```

### 6.6 Commit Phase 6

```bash
git add -A
git commit -m "wave4-8: phase 6 — bump 1.3.5 + smoke regression + report

Smoke regression Wave 5+6+4+4.5+4.6+4.7+4.7.1:
- audit-aligned: <PASS>/32 (era 29/32 post-Wave 4.7, ora atteso 32/32)
- legacy redirects: <PASS>/18 (era 16/18, ora atteso 18/18)
- security headers: 5/5 ✓
- DB residue diciotto/diciannove: 0 ✓
- Page draft conflict CPT: 0 ✓
- CPT competenza publish: 17 (allineato DEC-021)
- Term tipo-area: privati/imprese/contenzioso-amministrativo (slug rinominato)

Theme version: 1.3.4-wave4-7-1-acf-fix → 1.3.5-wave4-8-cleanup-final

Closes Wave 4.8 definitiva (14 items + 7 visual P2 polish + term rename).

Sito staging: handoff-ready Elena al 100%."
```

---

## 📋 PHASE 7 — Push branch + chiusura (~10 min)

```bash
git push origin feat/wave4-8-cleanup-migrations-ux-polish-final
```

NON merge automatico. Orchestratore audisce e procede con merge + tag + cleanup branch + deploy delta `--checksum` su staging.

---

## ✅ Acceptance criteria Wave 4.8 (definitiva)

- [ ] 7 phases eseguite, 7 commit phase-by-phase
- [ ] **CMS cleanup totale**: 0 page draft conflict CPT (era 11), 0 duplicate "lo-studio" (era 1), 2 marker `[Servizio extra]` su CPT 2680/2681 (decisione cliente Wave 7)
- [ ] **Migration eseguita**: 17 CPT competenza publish (era 15, +2 da page 232 + 260)
- [ ] **Term renamed**: tipo-area `contenzioso` → `contenzioso-amministrativo`
- [ ] **7 visual P2 polish**: tutti applicati (typo + empty + napoli + editorial + TOC + jump nav)
- [ ] **Smoke 32/32 audit-aligned PASS** (era 29/32)
- [ ] **Smoke 18/18 legacy redirects PASS** (era 16/18)
- [ ] **5/5 security headers** mantenuto
- [ ] **DB residue zero**: post_content=0, postmeta=0, options=0 (legacy "diciotto/diciannove")
- [ ] **NO regression** Wave 5+6+4+4.5+4.6+4.7+4.7.1
- [ ] Theme version `1.3.5-wave4-8-cleanup-final`
- [ ] Branch pushed (NO merge automatico)
- [ ] Report `.claude/knowledge/recovery/WAVE4-8-CLEANUP-FINAL-REPORT.md`

---

## 🚨 Cosa fare in caso di problemi

| Situazione | Action |
|---|---|
| Phase 1 query rivela page già trashate da Wave 4.7 | OK, skip quelle in Phase 2 |
| Phase 3 migration: CPT con stesso slug già esiste | Pattern idempotente skip — log warning |
| Phase 3 migration: term assign fallisce | Auto rollback (CPT cancellato) — STOP, riporta orchestratore |
| Phase 4 term `contenzioso` non esiste | STOP, possibile già rinominato — verifica state |
| Phase 5 visual: file template inatteso/mancante | STOP, riporta orchestratore (no improvise) |
| Phase 5 typo non in PHP ma in ACF DB | Fix DB via wp post update post_type=acf-field |
| Phase 6 smoke FAIL > 0 audit-aligned | Investigate, NO completion forzato |
| Phase 6 DB residue > 0 | Investigate query specifica, NO forced trash |

---

## 🎯 Output expected

1. Branch `feat/wave4-8-cleanup-migrations-ux-polish-final` con 7+ commit
2. File modificati / creati:
   - **DB ops**: 11 trash + 2 marker CPT + 2 NEW CPT competenza + 1 term rename
   - **MOD**: `template-parts/page-contatti.php` (typo)
   - **MOD**: `template-parts/page-risorse.php` (empty state)
   - **MOD**: `template-parts/page-aree-di-pratica-hub.php` (card editorial)
   - **MOD**: `single-competenza.php` OR `acf-json/*.json` (napoli)
   - **MOD**: `template-parts/page-glossario.php` (jump nav)
   - **MOD**: `assets/css/sections.css` (TOC mobile + jump nav)
   - **MOD**: `style.css` + `functions.php` (version bump)
3. Audit trail completo in `.claude/knowledge/audits/wave4-8/`:
   - `investigation-pre.txt`
   - `phase2-cleanup.txt`, `phase3-migration.txt`, `phase4-term-rename.txt`, `phase5-visual-p2.txt`
   - `regression/audit-aligned.txt`, `regression/db-state.txt`, `regression/headers.txt`
4. Report `.claude/knowledge/recovery/WAVE4-8-CLEANUP-FINAL-REPORT.md`
5. Theme version `1.3.5-wave4-8-cleanup-final`

L'orchestratore audisce + procede con merge `feat/wave4-8 → main` (no-ff) + tag `v1.3.5-wave4-8-cleanup-final` + deploy delta `--checksum` su staging.

**Critical**: post-merge serve **rsync delta `--checksum`** + `wp acf sync` + flush + smoke verify (le modifiche DB di Phase 2-4 erano già su staging via SSH).

---

## 🔗 Riferimenti

- DEC-021 (URL audit-aligned 17 cliente-firmato)
- DEC-027 (EDITOR-HANDOFF Elena)
- DEC-029-COMPLETED (Wave 4.6)
- DEC-035-COMPLETED (Wave 4.7) — 14 items pending originanti questa wave
- DEC-036 (Wave 4.7.1 ACF + deploy fix) — `--checksum` mandatory lesson learned
- Visual Audit 2026-05-07 — 7 P2 polish identificati
- CMS Diagnosis 2026-05-07
- `EDITOR-HANDOFF.md` — riferimento Elena
- `CLAUDE.md` — single source of truth
