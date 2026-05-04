# PROMPT v1.0.0 WAVE 1 RECOVERY — Agent A + Agent C (sequenziale single agent)

> **Per Claude Code in nuova sessione (single agent).** Tempo: ~2.5h.
> **PRECEDENZA**: Wave 0 + Wave 1 Agent B completati (10 Field Groups CPT).
> **MISSIONE**: completare Wave 1 con i 2 agent falliti (Agent A + Agent C) in modalità sequenziale safe.

---

## 🎯 Tu sei

L'**Agente Recovery Wave 1**. Wave 1 paralleli del lancio precedente ha avuto un problema: 1 agent su 3 ha completato, gli altri 2 sono falliti per saturazione sistema (load avg 68 con 3 agent + tmux paralleli).

```
✅ Agent B  → 10 Field Groups CPT     [DONE — commit f1c1051]
❌ Agent A  → 5 Field Groups page WP  [MISSING — DA RECUPERARE]
❌ Agent C  → 1 Field Group Theme Opt [MISSING — DA RECUPERARE]
```

Ora il sistema è libero (load 13). Esegui sequenziale: prima Agent A (5 task), poi Agent C (1 task), poi smoke + commit + push.

---

## 📚 Letture obbligatorie

```
.claude/knowledge/recovery/PROJECT_STATE.md          (state consolidato)
PROMPT_AGENT_v1.0_WAVE1_FIELD_GROUPS.md              (specifiche complete)
CLAUDE.md                                            (hard constraints)

Verify Wave 1 stato attuale:
  ls wp-content/themes/saltelli/acf-json/*.json
  → Atteso: 10 file (tutti group_*_v1.json di Agent B)
  → Mancanti: group_costi, group_casi, group_contatti, group_faq, 
              group_info_shared, group_theme_options
```

---

## 🔒 Hard rules

| Rule | Decisione |
|---|---|
| **Sequenziale**: prima Agent A completa tutti 5, poi Agent C | No più parallelismo |
| **Output**: ACF JSON files in `wp-content/themes/saltelli/acf-json/` | Git versionato |
| **Method**: WP-CLI eval `acf_add_local_field_group()` | ACF auto-export JSON |
| **NO content migration** in Wave 1 (solo schema) | Wave 2 dopo |
| **NO modifiche frontend** template | refactor backend-only |
| **Field naming**: snake_case, prefix `field_<group>_<name>` | ACF requirement |
| **Smoke test** dopo OGNI Field Group creato | safety |
| **Path droplet**: `/var/www/saltelli/` (NO /htdocs) | Lesson learned |
| **Commit incrementale**: 1 commit per ogni Field Group | Atomicity |

---

## 📋 PHASE 1 — Agent A retry (5 Field Groups, ~1.5h)

### 1.1 — Setup workspace

```bash
cd /Users/aldosantoro/Desktop/DEV/saltelli-wp

# Verify Wave 1 Agent B done
ls wp-content/themes/saltelli/acf-json/group_*_v1.json | wc -l
# Atteso: 10

# Verify ACF working
docker compose run --rm wpcli eval "echo function_exists('acf_add_local_field_group') ? 'OK' : 'FAIL';"
# Atteso: OK

# Verify CPT registrati Wave 0
docker compose run --rm wpcli post-type list --format=csv | grep -E 'saltelli_'
# Atteso: 8 CPT
```

### 1.2 — Task A1: Field Group COSTI (~20 min)

Page WP target: `costi` (verifica id):
```bash
COSTI_ID=$(docker compose run --rm wpcli post list --post_type=page --name=costi --field=ID 2>&1 | grep -oE '^[0-9]+' | head -1)
echo "Costi ID: $COSTI_ID"
# Atteso: 2695 (oppure verifica)
```

Crea Field Group via WP-CLI eval:

```bash
docker compose run --rm wpcli eval '
$page_id = get_page_by_path("costi")->ID;
acf_add_local_field_group([
    "key" => "group_costi_v1",
    "title" => "Costi — Sezioni",
    "fields" => [
        // HERO
        ["key"=>"field_costi_hero_eyebrow", "label"=>"Hero · Eyebrow", "name"=>"hero_eyebrow", "type"=>"text", "default_value"=>"§ Servizio · Costi"],
        ["key"=>"field_costi_hero_h1_pre", "label"=>"Hero · H1 prefix", "name"=>"hero_h1_pre", "type"=>"text", "default_value"=>"Costi e prima"],
        ["key"=>"field_costi_hero_h1_em", "label"=>"Hero · H1 italic", "name"=>"hero_h1_em", "type"=>"text", "default_value"=>"consulenza."],
        ["key"=>"field_costi_hero_lede", "label"=>"Hero · Lede italic", "name"=>"hero_lede", "type"=>"textarea", "rows"=>3],
        // ASIDE TRUST BOX
        ["key"=>"field_costi_aside_eyebrow", "label"=>"Aside · Eyebrow", "name"=>"aside_eyebrow", "type"=>"text"],
        ["key"=>"field_costi_aside_h3", "label"=>"Aside · H3", "name"=>"aside_h3", "type"=>"text"],
        ["key"=>"field_costi_aside_p", "label"=>"Aside · Paragrafo", "name"=>"aside_p", "type"=>"textarea"],
        ["key"=>"field_costi_aside_cta_label", "label"=>"Aside · CTA label", "name"=>"aside_cta_label", "type"=>"text"],
        ["key"=>"field_costi_aside_cta_url", "label"=>"Aside · CTA URL", "name"=>"aside_cta_url", "type"=>"url"],
        // § 03 Body editorial
        ["key"=>"field_costi_calc_body", "label"=>"§ 03 Body editorial", "name"=>"calc_body", "type"=>"wysiwyg", "tabs"=>"all"],
        // CTA finale
        ["key"=>"field_costi_cta_eyebrow", "label"=>"CTA · Eyebrow", "name"=>"cta_eyebrow", "type"=>"text", "default_value"=>"§ Pronto?"],
        ["key"=>"field_costi_cta_h2", "label"=>"CTA · H2", "name"=>"cta_h2", "type"=>"text", "default_value"=>"La prima consulenza è gratuita. Sempre."],
        ["key"=>"field_costi_cta_p", "label"=>"CTA · Paragrafo", "name"=>"cta_p", "type"=>"textarea"],
        ["key"=>"field_costi_cta_label", "label"=>"CTA · Bottone label", "name"=>"cta_label", "type"=>"text", "default_value"=>"Prenota un incontro →"],
        ["key"=>"field_costi_cta_url", "label"=>"CTA · Bottone URL", "name"=>"cta_url", "type"=>"url", "default_value"=>"/contatti/"],
        ["key"=>"field_costi_cta_trust", "label"=>"CTA · Trust line", "name"=>"cta_trust", "type"=>"text", "default_value"=>"Risposta entro 24 ore · Riservatezza assoluta"],
    ],
    "location" => [
        [["param"=>"page", "operator"=>"==", "value"=>$page_id]]
    ],
    "menu_order" => 0,
    "position" => "normal",
    "style" => "default",
    "label_placement" => "top",
    "instruction_placement" => "label",
    "active" => true,
]);
echo "OK group_costi_v1 created";
'
```

Verify creation:
```bash
ls -la wp-content/themes/saltelli/acf-json/group_costi*.json
docker compose run --rm wpcli eval 'var_dump(acf_get_field_group("group_costi_v1") !== false);'
```

Commit:
```bash
git add wp-content/themes/saltelli/acf-json/group_costi*.json
git commit -m "feat(s2-v1.0.0-wave1-recovery): Agent A.1 — Field Group costi (16 fields page WP)"
git push origin main
```

NB: le sezioni con MULTIPLE items (Modalità × 3, Scenari × 3, FAQ × 5+, Trust × 4) NON sono in questo Field Group: usano i CPT separati (`saltelli_modalita`, `saltelli_scenario`, ecc.) gestiti da Agent B (già done).

### 1.3 — Task A2: Field Group CASI (~15 min)

Stessa modalità WP-CLI eval. Page WP `casi`:

```bash
docker compose run --rm wpcli eval '
$page_id = get_page_by_path("casi")->ID;
acf_add_local_field_group([
    "key" => "group_casi_v1",
    "title" => "Casi — Sezioni",
    "fields" => [
        // HERO
        ["key"=>"field_casi_hero_eyebrow", "label"=>"Hero · Eyebrow", "name"=>"hero_eyebrow", "type"=>"text", "default_value"=>"§ Risorse · Casi rappresentativi"],
        ["key"=>"field_casi_hero_h1_pre", "label"=>"Hero · H1 prefix", "name"=>"hero_h1_pre", "type"=>"text", "default_value"=>"Casi"],
        ["key"=>"field_casi_hero_h1_em", "label"=>"Hero · H1 italic", "name"=>"hero_h1_em", "type"=>"text", "default_value"=>"rappresentativi."],
        ["key"=>"field_casi_hero_lede", "label"=>"Hero · Lede italic", "name"=>"hero_lede", "type"=>"textarea", "rows"=>3],
        // INTRO body editorial (drop-cap target)
        ["key"=>"field_casi_intro_body", "label"=>"Intro · Body editoriale", "name"=>"intro_body", "type"=>"wysiwyg", "tabs"=>"all"],
        // CTA finale
        ["key"=>"field_casi_cta_eyebrow", "label"=>"CTA · Eyebrow", "name"=>"cta_eyebrow", "type"=>"text", "default_value"=>"§ Vuoi raccontarci la tua pratica?"],
        ["key"=>"field_casi_cta_h2", "label"=>"CTA · H2", "name"=>"cta_h2", "type"=>"text"],
        ["key"=>"field_casi_cta_p", "label"=>"CTA · Paragrafo", "name"=>"cta_p", "type"=>"textarea"],
        ["key"=>"field_casi_cta_label", "label"=>"CTA · Bottone label", "name"=>"cta_label", "type"=>"text", "default_value"=>"Prenota un incontro →"],
        ["key"=>"field_casi_cta_url", "label"=>"CTA · Bottone URL", "name"=>"cta_url", "type"=>"url", "default_value"=>"/contatti/"],
    ],
    "location" => [[["param"=>"page", "operator"=>"==", "value"=>$page_id]]],
    "active" => true,
]);
echo "OK group_casi_v1";
'
```

Verify + commit pattern stesso Task A1.

### 1.4 — Task A3: Field Group CONTATTI (~15 min)

```bash
docker compose run --rm wpcli eval '
$page_id = get_page_by_path("contatti")->ID;
acf_add_local_field_group([
    "key" => "group_contatti_v1",
    "title" => "Contatti — Sezioni",
    "fields" => [
        // HERO
        ["key"=>"field_contatti_hero_eyebrow", "label"=>"Hero · Eyebrow", "name"=>"hero_eyebrow", "type"=>"text", "default_value"=>"§ Servizio · Contatti"],
        ["key"=>"field_contatti_hero_h1_pre", "label"=>"Hero · H1 prefix", "name"=>"hero_h1_pre", "type"=>"text", "default_value"=>"Contatti"],
        ["key"=>"field_contatti_hero_h1_em", "label"=>"Hero · H1 italic", "name"=>"hero_h1_em", "type"=>"text", "default_value"=>"& appuntamenti."],
        ["key"=>"field_contatti_hero_lede", "label"=>"Hero · Lede italic", "name"=>"hero_lede", "type"=>"textarea", "rows"=>3],
        // MAP
        ["key"=>"field_contatti_map_iframe", "label"=>"Map · iframe embed code", "name"=>"map_iframe", "type"=>"textarea", "rows"=>4, "instructions"=>"Embed code OpenStreetMap o Google Maps"],
        ["key"=>"field_contatti_map_caption", "label"=>"Map · Caption sotto", "name"=>"map_caption", "type"=>"text"],
        // COME ARRIVARE
        ["key"=>"field_contatti_come_title", "label"=>"Come arrivare · Titolo", "name"=>"come_arrivare_title", "type"=>"text", "default_value"=>"Come arrivare"],
        ["key"=>"field_contatti_come_metro", "label"=>"Come arrivare · Metro", "name"=>"come_arrivare_metro", "type"=>"textarea", "rows"=>2],
        ["key"=>"field_contatti_come_parking", "label"=>"Come arrivare · Parcheggi", "name"=>"come_arrivare_parking", "type"=>"textarea", "rows"=>2],
        // TRUST signal
        ["key"=>"field_contatti_trust_signal", "label"=>"Trust signal", "name"=>"trust_signal", "type"=>"text", "default_value"=>"Riceviamo solo su appuntamento. Risposta entro 24 ore."],
    ],
    "location" => [[["param"=>"page", "operator"=>"==", "value"=>$page_id]]],
    "active" => true,
]);
echo "OK group_contatti_v1";
'
```

Verify + commit.

### 1.5 — Task A4: Field Group FAQ aggregator (~10 min)

```bash
docker compose run --rm wpcli eval '
$page_id = get_page_by_path("faq")->ID;
acf_add_local_field_group([
    "key" => "group_faq_v1",
    "title" => "FAQ Aggregator — Sezioni",
    "fields" => [
        // HERO
        ["key"=>"field_faq_hero_eyebrow", "label"=>"Hero · Eyebrow", "name"=>"hero_eyebrow", "type"=>"text", "default_value"=>"§ Risorse · Domande frequenti"],
        ["key"=>"field_faq_hero_h1_pre", "label"=>"Hero · H1 prefix", "name"=>"hero_h1_pre", "type"=>"text", "default_value"=>"Domande"],
        ["key"=>"field_faq_hero_h1_em", "label"=>"Hero · H1 italic", "name"=>"hero_h1_em", "type"=>"text", "default_value"=>"frequenti."],
        ["key"=>"field_faq_hero_lede", "label"=>"Hero · Lede italic", "name"=>"hero_lede", "type"=>"textarea", "rows"=>3],
        // TOC config
        ["key"=>"field_faq_toc_title", "label"=>"TOC · Titolo", "name"=>"toc_title", "type"=>"text", "default_value"=>"§ Indice"],
        // CTA
        ["key"=>"field_faq_cta_eyebrow", "label"=>"CTA · Eyebrow", "name"=>"cta_eyebrow", "type"=>"text", "default_value"=>"§ Non hai trovato la tua domanda?"],
        ["key"=>"field_faq_cta_h2", "label"=>"CTA · H2", "name"=>"cta_h2", "type"=>"text"],
        ["key"=>"field_faq_cta_p", "label"=>"CTA · Paragrafo", "name"=>"cta_p", "type"=>"textarea"],
        ["key"=>"field_faq_cta_label", "label"=>"CTA · Bottone label", "name"=>"cta_label", "type"=>"text", "default_value"=>"Prenota un incontro →"],
        ["key"=>"field_faq_cta_url", "label"=>"CTA · Bottone URL", "name"=>"cta_url", "type"=>"url", "default_value"=>"/contatti/"],
    ],
    "location" => [[["param"=>"page", "operator"=>"==", "value"=>$page_id]]],
    "active" => true,
]);
echo "OK group_faq_v1";
'
```

NB: Le 28+ FAQ items usano CPT `saltelli_faq` con taxonomy `faq_topic` (Agent B done).

Verify + commit.

### 1.6 — Task A5: Field Group INFO-SHARED (5 page) (~25 min)

Field Group condiviso per 5 page con stesso layout. Location: OR multi-page.

```bash
docker compose run --rm wpcli eval '
$pages = ["guide-gratuite", "come-lavoriamo", "prima-consulenza", "lavora-con-noi", "richiedi-preventivo"];
$location_rules = [];
foreach ($pages as $slug) {
    $p = get_page_by_path($slug);
    if ($p) {
        $location_rules[] = [["param"=>"page", "operator"=>"==", "value"=>$p->ID]];
    }
}

acf_add_local_field_group([
    "key" => "group_info_shared_v1",
    "title" => "Info Shared — Layout standard",
    "fields" => [
        // HERO
        ["key"=>"field_info_hero_eyebrow", "label"=>"Hero · Eyebrow", "name"=>"hero_eyebrow", "type"=>"text"],
        ["key"=>"field_info_hero_h1_pre", "label"=>"Hero · H1 prefix", "name"=>"hero_h1_pre", "type"=>"text"],
        ["key"=>"field_info_hero_h1_em", "label"=>"Hero · H1 italic", "name"=>"hero_h1_em", "type"=>"text"],
        ["key"=>"field_info_hero_lede", "label"=>"Hero · Lede italic", "name"=>"hero_lede", "type"=>"textarea", "rows"=>3],
        // ASIDE
        ["key"=>"field_info_aside_eyebrow", "label"=>"Aside · Eyebrow", "name"=>"aside_eyebrow", "type"=>"text"],
        ["key"=>"field_info_aside_h3", "label"=>"Aside · H3", "name"=>"aside_h3", "type"=>"text"],
        ["key"=>"field_info_aside_p", "label"=>"Aside · Paragrafo", "name"=>"aside_p", "type"=>"textarea"],
        ["key"=>"field_info_aside_cta_label", "label"=>"Aside · CTA label", "name"=>"aside_cta_label", "type"=>"text"],
        ["key"=>"field_info_aside_cta_url", "label"=>"Aside · CTA URL", "name"=>"aside_cta_url", "type"=>"url"],
        // BODY editorial
        ["key"=>"field_info_body_content", "label"=>"Body · Editorial content (drop-cap target)", "name"=>"body_content", "type"=>"wysiwyg", "tabs"=>"all"],
        // CTA finale
        ["key"=>"field_info_cta_eyebrow", "label"=>"CTA · Eyebrow", "name"=>"cta_final_eyebrow", "type"=>"text", "default_value"=>"§ Pronto?"],
        ["key"=>"field_info_cta_h2", "label"=>"CTA · H2", "name"=>"cta_final_h2", "type"=>"text"],
        ["key"=>"field_info_cta_p", "label"=>"CTA · Paragrafo", "name"=>"cta_final_p", "type"=>"textarea"],
        ["key"=>"field_info_cta_label", "label"=>"CTA · Bottone label", "name"=>"cta_final_cta_label", "type"=>"text", "default_value"=>"Prenota un incontro →"],
        ["key"=>"field_info_cta_url", "label"=>"CTA · Bottone URL", "name"=>"cta_final_cta_url", "type"=>"url", "default_value"=>"/contatti/"],
        ["key"=>"field_info_cta_trust", "label"=>"CTA · Trust line", "name"=>"cta_final_trust", "type"=>"text", "default_value"=>"Risposta entro 24 ore · Riservatezza assoluta"],
    ],
    "location" => $location_rules,
    "active" => true,
]);
echo "OK group_info_shared_v1 — applies to " . count($location_rules) . " pages";
'
```

Verify + commit.

### 1.7 — Phase 1 verify cumulativo

```bash
ls wp-content/themes/saltelli/acf-json/group_costi*.json
ls wp-content/themes/saltelli/acf-json/group_casi*.json
ls wp-content/themes/saltelli/acf-json/group_contatti*.json
ls wp-content/themes/saltelli/acf-json/group_faq*.json
ls wp-content/themes/saltelli/acf-json/group_info_shared*.json

# Total Wave 1 atteso: 15/16 (10 Agent B + 5 Agent A)
ls wp-content/themes/saltelli/acf-json/*.json | wc -l
# Atteso: 15
```

---

## 📋 PHASE 2 — Agent C retry: Theme Options (~1h)

### 2.1 — Field Group Theme Options con 6 tabs

```bash
docker compose run --rm wpcli eval '
acf_add_local_field_group([
    "key" => "group_theme_options_v1",
    "title" => "Saltelli — Settings globali",
    "fields" => [
        
        // === TAB 1: Studio Info ===
        ["key"=>"field_tab_studio", "type"=>"tab", "label"=>"Studio Info"],
        ["key"=>"field_studio_indirizzo_via", "label"=>"Via", "name"=>"studio_indirizzo_via", "type"=>"text", "default_value"=>"Via Vannella Gaetani 27"],
        ["key"=>"field_studio_cap_citta", "label"=>"CAP + Città", "name"=>"studio_cap_citta", "type"=>"text", "default_value"=>"80121 Napoli"],
        ["key"=>"field_studio_quartiere", "label"=>"Quartiere", "name"=>"studio_quartiere", "type"=>"text", "default_value"=>"Chiaia"],
        ["key"=>"field_studio_orari_settimana", "label"=>"Orari settimana", "name"=>"studio_orari_settimana", "type"=>"text", "default_value"=>"Lun – Ven · 09:30 – 18:30"],
        ["key"=>"field_studio_orari_sabato", "label"=>"Orari sabato", "name"=>"studio_orari_sabato", "type"=>"text", "default_value"=>"Sabato su appuntamento"],
        ["key"=>"field_studio_telefono", "label"=>"Telefono pubblico", "name"=>"studio_telefono_pubblico", "type"=>"text", "default_value"=>"+39 081 1813 1119"],
        ["key"=>"field_studio_email", "label"=>"Email pubblica", "name"=>"studio_email", "type"=>"email", "default_value"=>"info@studiolegalesaltelli.it"],
        ["key"=>"field_studio_pec", "label"=>"PEC", "name"=>"studio_pec", "type"=>"email"],
        ["key"=>"field_studio_piva", "label"=>"P.IVA", "name"=>"studio_piva", "type"=>"text", "default_value"=>"06685101211"],
        ["key"=>"field_studio_ordine", "label"=>"Ordine professionale", "name"=>"studio_ordine_avvocati", "type"=>"text", "default_value"=>"Ordine degli Avvocati di Napoli"],
        
        // === TAB 2: Mappa ===
        ["key"=>"field_tab_map", "type"=>"tab", "label"=>"Mappa"],
        ["key"=>"field_studio_lat", "label"=>"Latitudine", "name"=>"studio_coordinate_lat", "type"=>"text", "default_value"=>"40.8333"],
        ["key"=>"field_studio_lng", "label"=>"Longitudine", "name"=>"studio_coordinate_lng", "type"=>"text", "default_value"=>"14.2425"],
        
        // === TAB 3: Brand ===
        ["key"=>"field_tab_brand", "type"=>"tab", "label"=>"Brand"],
        ["key"=>"field_brand_payoff", "label"=>"Payoff (sotto logo)", "name"=>"brand_payoff", "type"=>"text", "default_value"=>"Diritto, con misura"],
        ["key"=>"field_brand_statement", "label"=>"Brand statement (footer/about)", "name"=>"brand_statement_short", "type"=>"textarea", "rows"=>3, "default_value"=>"Un atelier legale italiano. Quattro avvocati a Chiaia. Vent\\'anni di pratica accanto a famiglie e imprese."],
        
        // === TAB 4: Footer ===
        ["key"=>"field_tab_footer", "type"=>"tab", "label"=>"Footer"],
        ["key"=>"field_footer_credit_text", "label"=>"Credit text bottom", "name"=>"footer_credit_text", "type"=>"text", "default_value"=>"Realizzato da Adsolut Web Agency"],
        ["key"=>"field_footer_credit_url", "label"=>"Credit URL", "name"=>"footer_credit_url", "type"=>"url", "default_value"=>"https://adsolut.it"],
        ["key"=>"field_footer_newsletter_enabled", "label"=>"Newsletter footer attiva?", "name"=>"footer_newsletter_enabled", "type"=>"true_false", "default_value"=>1, "ui"=>1],
        ["key"=>"field_footer_newsletter_provider", "label"=>"Newsletter provider", "name"=>"footer_newsletter_provider", "type"=>"select", "choices"=>["brevo"=>"Brevo (legacy)", "static"=>"HTML statico", "none"=>"Nessuno"], "default_value"=>"static"],
        
        // === TAB 5: Social ===
        ["key"=>"field_tab_social", "type"=>"tab", "label"=>"Social"],
        ["key"=>"field_social_instagram", "label"=>"Instagram URL", "name"=>"social_instagram", "type"=>"url"],
        ["key"=>"field_social_linkedin", "label"=>"LinkedIn URL", "name"=>"social_linkedin", "type"=>"url"],
        ["key"=>"field_social_twitter", "label"=>"X / Twitter URL", "name"=>"social_twitter", "type"=>"url"],
        ["key"=>"field_social_facebook", "label"=>"Facebook URL", "name"=>"social_facebook", "type"=>"url"],
        
        // === TAB 6: CTA Defaults ===
        ["key"=>"field_tab_cta", "type"=>"tab", "label"=>"CTA Defaults"],
        ["key"=>"field_cta_default_label", "label"=>"CTA default label", "name"=>"cta_default_label", "type"=>"text", "default_value"=>"Prenota un incontro →"],
        ["key"=>"field_cta_default_url", "label"=>"CTA default URL", "name"=>"cta_default_url", "type"=>"url", "default_value"=>"/contatti/"],
        ["key"=>"field_cta_trust_signal", "label"=>"CTA trust signal default", "name"=>"cta_trust_signal", "type"=>"text", "default_value"=>"Risposta entro 24 ore · Riservatezza assoluta"],
        ["key"=>"field_cta_subline_italic", "label"=>"CTA subline (sotto bottone)", "name"=>"cta_subline_italic", "type"=>"text", "default_value"=>"Prima consulenza conoscitiva gratuita"],
    ],
    "location" => [
        [["param"=>"options_page", "operator"=>"==", "value"=>"saltelli-settings"]]
    ],
    "menu_order" => 0,
    "position" => "normal",
    "active" => true,
]);
echo "OK group_theme_options_v1 — 6 tabs registered";
'
```

Verify:
```bash
ls -la wp-content/themes/saltelli/acf-json/group_theme_options*.json
docker compose run --rm wpcli eval 'var_dump(acf_get_field_group("group_theme_options_v1") !== false);'
```

WP-Admin verify (manuale):
```
Apri http://localhost:8080/wp-admin
Sidebar → "Saltelli — Settings"
Click → vedi 6 tabs (Studio Info, Mappa, Brand, Footer, Social, CTA Defaults)
```

Commit:
```bash
git add wp-content/themes/saltelli/acf-json/group_theme_options*.json
git commit -m "feat(s2-v1.0.0-wave1-recovery): Agent C — Theme Options Field Group (6 tabs)"
git push origin main
```

---

## 📋 PHASE 3 — Smoke + Bump + Deploy (~15 min)

### 3.1 — Smoke verify globale

```bash
echo "═══ ACF FIELD GROUPS SUMMARY ═══"
ls wp-content/themes/saltelli/acf-json/*.json | xargs -n1 basename
echo ""
echo "Total: $(ls wp-content/themes/saltelli/acf-json/*.json | wc -l)"
echo "Atteso: 16"

echo ""
echo "═══ FRONTEND SMOKE TEST ═══"
for URL in / /chi-siamo/ /avvocati/ /casi/ /costi/ /contatti/ /faq/ /come-lavoriamo/; do
    HTTP=$(curl -sL -o /dev/null -w "%{http_code}" "https://staging.studiolegalesaltelli.it$URL?_=v1w1r" -m 5)
    echo "  $URL → HTTP $HTTP"
done
```

### 3.2 — Bump version

```bash
sed -i.bak 's/Version: [0-9.]\+.*/Version: 1.0.0-recovery-wave1/' wp-content/themes/saltelli/style.css
sed -i.bak "s/SALTELLI_THEME_VERSION', '[^']*'/SALTELLI_THEME_VERSION', '1.0.0-recovery-wave1'/" wp-content/themes/saltelli/functions.php
rm -f wp-content/themes/saltelli/{style.css,functions.php}.bak
```

### 3.3 — Deploy droplet

```bash
# Path corretto: /var/www/saltelli/ (NO /htdocs)
rsync -avz wp-content/themes/saltelli/acf-json/ deploy@178.62.207.50:/var/www/saltelli/wp-content/themes/saltelli/acf-json/
rsync -avz wp-content/themes/saltelli/style.css wp-content/themes/saltelli/functions.php deploy@178.62.207.50:/var/www/saltelli/wp-content/themes/saltelli/

ssh deploy@178.62.207.50 "
    cd /var/www/saltelli
    sudo -u www-data wp cache flush --path=/var/www/saltelli
    sudo -u www-data wp transient delete --all --path=/var/www/saltelli
"
```

### 3.4 — Final commit

```bash
git add -A
git commit -m "feat(v1.0.0-wave1-recovery): Wave 1 complete — 16/16 ACF Field Groups (Agent A+B+C)"
git push origin main
```

---

## 📊 DELIVERABLE finale Wave 1 Recovery

Report: `.claude/knowledge/recovery/v1.0-WAVE1-COMPLETE.md`

```markdown
# v1.0.0 Wave 1 COMPLETE — ACF Field Groups Setup

## Score: 16/16 Field Groups

## Per phase
- Phase 1 — Agent A retry (5 Field Groups page WP): ✓
  - group_costi_v1 (16 fields)
  - group_casi_v1 (10 fields)
  - group_contatti_v1 (10 fields)
  - group_faq_v1 (10 fields)
  - group_info_shared_v1 (15 fields, location 5 page)
  
- Phase 2 — Agent C retry (1 Field Group Theme Options): ✓
  - group_theme_options_v1 (28 fields in 6 tabs)
  
- Phase 3 — Smoke + bump + deploy: ✓

## Cumulative Wave 1 stato

Field Groups creati totali: 16
- Agent A:  5 (page WP)
- Agent B: 10 (CPT — already done)
- Agent C:  1 (Theme Options)

Custom fields totali: ~100+

## Frontend invariato
Sito staging.studiolegalesaltelli.it visualmente identico.
Refactor 100% backend per editor handoff.

## Next: Wave 2
Content migration: popolare i 100+ fields con valori da hardcoded template PHP.
Tempo stimato: ~2-3h.
```

Quando finito, segnala "Wave 1 Recovery COMPLETE. 16/16 Field Groups."

---

## 🆘 Se incontri imprevisti

```
- WP-CLI eval errore single-quote: usa heredoc o escape \\'
- Field Group already exists: skip e prosegui (idempotente)
- Page slug non trovato: verifica wp post list --post_type=page
- ACF JSON file non auto-saved: verifica permessi acf-json/ (755)
- Droplet rsync fail: usa /var/www/saltelli/ (NO /htdocs)
- Commit conflict: rebase su origin/main prima di push
```

Tempo realistic: ~2.5h sequenziale single agent.

Buon lavoro. Quando finito, l'orchestrator esegue audit completo Wave 1 e procede con Wave 2 (content migration).
