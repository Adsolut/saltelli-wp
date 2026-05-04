# PROMPT v1.0.0 WAVE 1 — ACF Field Groups Setup (Multi-Agent Parallel)

> **3 agent paralleli in tmux**, ognuno lavora su Field Groups diversi.
> Tempo: ~2-3h elapsed.
> **PRECEDENZA**: Wave 0 completata (ACF Free + 8 CPT registrati).

---

## 🎯 STRATEGIA Multi-Agent

```
═══════════════════════════════════════════════════════════════
                    WAVE 1 — DIVISION OF LABOR
═══════════════════════════════════════════════════════════════

Agent A — Field Groups page WP custom (~2h)
  Output: 5 ACF JSON files
    - costi.json
    - casi.json
    - contatti.json
    - faq.json
    - info-shared.json

Agent B — Field Groups CPT (~2h)
  Output: 9 ACF JSON files
    - avvocato.json (esistente CPT, fields ACF)
    - competenza.json (esistente CPT, fields ACF)
    - faq-item.json (saltelli_faq)
    - caso-item.json (saltelli_caso)
    - modalita-item.json
    - scenario-item.json
    - principio-item.json
    - trust-item.json
    - formazione-item.json
    - guida-item.json

Agent C — Theme Options (~1h)
  Output: 1 ACF JSON file
    - theme-options.json (NAP, brand, footer, social, CTA defaults)

ZERO conflict possibile: i 3 agent scrivono in acf-json/ con file 
NOMI DIVERSI. WP-Admin auto-import dei JSON al refresh.
═══════════════════════════════════════════════════════════════
```

---

## 🔒 Hard rules (TUTTI gli agent)

| Rule | Decisione |
|---|---|
| **Output**: ACF JSON files in `wp-content/themes/saltelli/acf-json/` | Git versionato |
| **Method**: usa ACF UI (`wp-admin/edit.php?post_type=acf-field-group`) per creare Field Groups, ACF auto-export JSON | NO PHP register manuale |
| **Alternative method**: WP-CLI eval per creare Field Group via `acf_add_local_field_group()` | Se UI non disponibile |
| **NO content migration** in Wave 1 (solo schema) | Wave 2 dopo |
| **NO modifiche frontend** template | refactor backend-only |
| **Field naming convention**: snake_case, no spazi, no maiuscole | WP standard |
| **Field keys unique**: prefix `field_<group>_<name>` (es. `field_costi_hero_eyebrow`) | ACF requirement |
| **Smoke test** ACF Field Group registrato dopo OGNI completion | safety |
| **Path droplet**: `/var/www/saltelli/` (NO /htdocs) | Lesson learned |

---

## 🤖 AGENT A — Field Groups page WP custom (~2h)

```
🎯 Sei AGENT A di Wave 1. Lavori SOLO su Field Groups per page WP custom.

CONTEXT

Wave 0 ha installato ACF Free + registrato 8 CPT custom. Theme HA helper
saltelli_field() che chiama get_field() con fallback. Devi creare i Field
Groups per le page WP che hanno hardcoded content nel template page.php.

OBIETTIVO

5 ACF Field Groups per le page WP:
- costi (page id 2695, 7 sezioni Sessione 2)
- casi (page id 2699, hero + filter + casi list)
- contatti (page id ?, form + map + come arrivare)
- faq (page id 2705, hero + 6 topic groups + 28+ Q&A)
- info-shared (5 page: guide-gratuite, come-lavoriamo, prima-consulenza, lavora-con-noi, richiedi-preventivo)

FILES TARGET (ACF JSON output):
  wp-content/themes/saltelli/acf-json/group_costi.json
  wp-content/themes/saltelli/acf-json/group_casi.json
  wp-content/themes/saltelli/acf-json/group_contatti.json
  wp-content/themes/saltelli/acf-json/group_faq.json
  wp-content/themes/saltelli/acf-json/group_info_shared.json

═══════════════════════════════════════════════════════════════
TASK 1 — Field Group COSTI (~30 min)
═══════════════════════════════════════════════════════════════

Riferimento: page.php blocco is_page('costi') ha 7 sezioni hardcoded.

Crea Field Group via WP-CLI eval (ACF Free no Repeater nativo, ma usiamo
CPT "fake repeater" per le sezioni con multiple items):

```bash
docker compose run --rm wpcli eval '
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
        
        // § 03 Come calcoliamo - body editorial
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
        [["param"=>"page", "operator"=>"==", "value"=>get_page_by_path("costi")->ID]]
    ],
    "menu_order" => 0,
    "position" => "normal",
    "style" => "default",
    "label_placement" => "top",
    "instruction_placement" => "label",
    "active" => true,
]);
echo "OK group_costi";
'
```

NB Le sezioni con MULTIPLE items (Come funziona × 3, Scenari × 3, Calcoliamo
fattori × 3, FAQ × 5, Trust × 4) NON sono in questo Field Group: usano
i CPT saltelli_modalita / saltelli_scenario / saltelli_faq / saltelli_trust
gestiti da Agent B.

Verifica creazione:
  docker compose run --rm wpcli eval "var_dump(acf_get_field_group('group_costi_v1'));"

Verify file JSON auto-saved:
  ls -la wp-content/themes/saltelli/acf-json/group_costi*.json

═══════════════════════════════════════════════════════════════
TASK 2 — Field Group CASI (~20 min)
═══════════════════════════════════════════════════════════════

Pattern simile, fields:
- hero_eyebrow, hero_h1_pre, hero_h1_em, hero_lede
- intro_body (textarea, lede dropcap)
- cta finale fields

I casi list usano CPT saltelli_caso (gestito da Agent B).

Location: page=casi.

═══════════════════════════════════════════════════════════════
TASK 3 — Field Group CONTATTI (~20 min)
═══════════════════════════════════════════════════════════════

Fields:
- hero_eyebrow, hero_h1_pre, hero_h1_em, hero_lede
- map_iframe (textarea, embed code OpenStreetMap)
- map_caption
- come_arrivare_title
- come_arrivare_metro (textarea)
- come_arrivare_parking (textarea)
- trust_signal (text default "Riceviamo solo su appuntamento. Risposta entro 24 ore.")

Form CF7 NO ACF (gestito da plugin).

Location: page=contatti.

═══════════════════════════════════════════════════════════════
TASK 4 — Field Group FAQ aggregator (~15 min)
═══════════════════════════════════════════════════════════════

Fields:
- hero_eyebrow ("§ Risorse · Domande frequenti")
- hero_h1_pre ("Domande")
- hero_h1_em ("frequenti.")
- hero_lede (textarea)
- toc_title ("§ Indice")
- cta_eyebrow, cta_h2, cta_p, cta_label, cta_url

Le 28+ FAQ items usano CPT saltelli_faq con taxonomy faq_topic
(gestito da Agent B).

Location: page=faq.

═══════════════════════════════════════════════════════════════
TASK 5 — Field Group INFO-SHARED (5 page) (~30 min)
═══════════════════════════════════════════════════════════════

Field Group condiviso per 5 page con stesso layout.template:
- guide-gratuite, come-lavoriamo, prima-consulenza, lavora-con-noi, richiedi-preventivo

Fields:
- hero_eyebrow (text)
- hero_h1_pre (text)
- hero_h1_em (text)
- hero_lede (textarea)
- aside_eyebrow, aside_h3, aside_p, aside_cta_label, aside_cta_url
- body_content (wysiwyg, editor full TinyMCE)
- cta_final_eyebrow, cta_final_h2, cta_final_p, cta_final_cta_label, cta_final_cta_url, cta_final_trust

Location: page=guide-gratuite OR page=come-lavoriamo OR page=prima-consulenza OR page=lavora-con-noi OR page=richiedi-preventivo

(usa OR clauses ACF per multi-page location).

═══════════════════════════════════════════════════════════════
DELIVERABLE Agent A
═══════════════════════════════════════════════════════════════

5 file JSON in acf-json/:
  ✓ group_costi*.json
  ✓ group_casi*.json
  ✓ group_contatti*.json
  ✓ group_faq*.json
  ✓ group_info_shared*.json

Tempo: ~2h.

Quando finito, segnala "Agent A done. 5 Field Groups page WP."
```

---

## 🤖 AGENT B — Field Groups CPT (~2h)

```
🎯 Sei AGENT B di Wave 1. Lavori SOLO su Field Groups per CPT.

OBIETTIVO

10 ACF Field Groups per i CPT (esistenti + Wave 0 nuovi):

CPT esistenti (estendi fields):
  - avvocato (4 lawyer)
  - competenza (3 tier-1 + 16 tier-2)

CPT nuovi (Wave 0):
  - saltelli_faq + faq_topic taxonomy
  - saltelli_caso + caso_categoria taxonomy
  - saltelli_modalita
  - saltelli_scenario
  - saltelli_principio
  - saltelli_trust
  - saltelli_formazione
  - saltelli_guida

═══════════════════════════════════════════════════════════════
TASK 1 — Field Group AVVOCATO (~30 min)
═══════════════════════════════════════════════════════════════

Fields per CPT 'avvocato' (4 lawyer Emiliano/Fabiana/Antonia/Stefano):

```php
acf_add_local_field_group([
    "key" => "group_avvocato_v1",
    "title" => "Avvocato — Profilo completo",
    "fields" => [
        // Hero
        ["key"=>"field_av_hero_role", "label"=>"Hero · Ruolo (es. Founding Partner · Tributarista)", "name"=>"hero_role", "type"=>"text"],
        ["key"=>"field_av_specs", "label"=>"Specializzazioni (max 5)", "name"=>"specializzazioni", "type"=>"textarea", "instructions"=>"Una per riga, es. Diritto tributario / Cassazione / Cartelle"],
        
        // Bio
        ["key"=>"field_av_bio_breve", "label"=>"Bio breve (1 riga lede)", "name"=>"bio_breve", "type"=>"text"],
        ["key"=>"field_av_bio_estesa", "label"=>"Bio estesa", "name"=>"bio_estesa", "type"=>"wysiwyg"],
        
        // Foto
        ["key"=>"field_av_foto", "label"=>"Foto ritratto (3:4)", "name"=>"foto_ritratto", "type"=>"image", "return_format"=>"array", "preview_size"=>"medium"],
        
        // Contatti diretti
        ["key"=>"field_av_email", "label"=>"Email pubblica", "name"=>"email_pubblica", "type"=>"email"],
        ["key"=>"field_av_tel", "label"=>"Telefono diretto", "name"=>"telefono_pubblico", "type"=>"text"],
        ["key"=>"field_av_whatsapp", "label"=>"WhatsApp number (E.164)", "name"=>"whatsapp", "type"=>"text"],
        ["key"=>"field_av_linkedin", "label"=>"LinkedIn URL", "name"=>"same_as_linkedin", "type"=>"url"],
        
        // Aree competenza correlate
        ["key"=>"field_av_aree", "label"=>"Aree di competenza correlate", "name"=>"aree_competenza_correlate", "type"=>"post_object", "post_type"=>["competenza"], "multiple"=>true, "ui"=>1, "return_format"=>"id"],
        
        // Formazione (riferisce CPT saltelli_formazione)
        // NB: gestita via CPT post_object multiple — Agent B Task 8
        ["key"=>"field_av_formazione", "label"=>"Formazione & Titoli", "name"=>"formazione", "type"=>"post_object", "post_type"=>["saltelli_formazione"], "multiple"=>true, "ui"=>1, "return_format"=>"id", "instructions"=>"Crea formazione items in 'Avvocati > Formazione & Titoli', poi seleziona qui"],
        
        // Casi (riferisce CPT saltelli_caso)
        ["key"=>"field_av_casi", "label"=>"Casi rappresentativi", "name"=>"casi_rappresentativi", "type"=>"post_object", "post_type"=>["saltelli_caso"], "multiple"=>true, "ui"=>1, "return_format"=>"id"],
    ],
    "location" => [
        [["param"=>"post_type", "operator"=>"==", "value"=>"avvocato"]]
    ],
    "active" => true,
]);
```

═══════════════════════════════════════════════════════════════
TASK 2 — Field Group COMPETENZA (~30 min)
═══════════════════════════════════════════════════════════════

Fields per CPT 'competenza' (19 aree pratica, 3 tier-1):

- is_tier_1 (true_false)
- tier_label (text, es. "Tier 1 · Approfondimento · Per le imprese")
- subtitle (text, sottotitolo h1)
- answer_capsule (textarea, GEO answer 50-60 parole)
- body_extended (wysiwyg, body editorial completo)
- lead_attorneys (post_object, target avvocato CPT, multiple)
- casi_rappresentativi (post_object, target saltelli_caso, multiple)
- faq (post_object, target saltelli_faq, multiple)
- articoli_correlati (post_object, target post, multiple)
- cta_label, cta_url

Location: post_type=competenza.

═══════════════════════════════════════════════════════════════
TASK 3 — Field Group FAQ Item (saltelli_faq) (~15 min)
═══════════════════════════════════════════════════════════════

Fields per ogni FAQ item:
- domanda (text - alias del title)
- risposta (textarea)
- topic taxonomy (già registrata Wave 0)

Title del CPT = domanda. Body = risposta (custom field).

═══════════════════════════════════════════════════════════════
TASK 4 — Field Group CASO (saltelli_caso) (~15 min)
═══════════════════════════════════════════════════════════════

Fields:
- id_label (text, es. "Cassazione · 2024")
- descrizione (textarea italic editorial)
- outcome_label (text, es. "Annullamento integrale")
- categoria taxonomy (caso_categoria, già registrata Wave 0)

═══════════════════════════════════════════════════════════════
TASK 5 — Field Group MODALITA (saltelli_modalita) (~10 min)
═══════════════════════════════════════════════════════════════

Per /costi/ § 01 (3 modalità: Studio/Online/Telefonica):
- num_label (text, es. "01 / Modalità classica")
- title (text, es. "Vieni a Chiaia")
- body (textarea)
- trust_mini (text)

═══════════════════════════════════════════════════════════════
TASK 6 — Field Group SCENARIO (saltelli_scenario) (~10 min)
═══════════════════════════════════════════════════════════════

Per /costi/ § 02 (3 scenari post-30min):
- num_label
- title (es. "01 / Non procediamo")
- body (textarea)
- trust_mini

═══════════════════════════════════════════════════════════════
TASK 7 — Field Group PRINCIPIO (saltelli_principio) (~10 min)
═══════════════════════════════════════════════════════════════

Per /chi-siamo/, /avvocati/, /come-lavoriamo/ (3 principi):
- num (text, "01")
- title (text, "Ascoltiamo prima")
- desc (textarea)

═══════════════════════════════════════════════════════════════
TASK 8 — Field Group TRUST (saltelli_trust) (~5 min)
═══════════════════════════════════════════════════════════════

Per /costi/ § 05 (4 trust plates):
- label (text, es. "Iscritti")
- valore (text, es. "Ordine Avvocati Napoli")

═══════════════════════════════════════════════════════════════
TASK 9 — Field Group FORMAZIONE (saltelli_formazione) (~5 min)
═══════════════════════════════════════════════════════════════

Per single-avvocato:
- anno (text, es. "2024" o "in corso")
- titolo (text, es. "Cassazionista")
- ente (text, es. "Iscritto albo speciale Cassazione")

═══════════════════════════════════════════════════════════════
TASK 10 — Field Group GUIDA (saltelli_guida) (~10 min)
═══════════════════════════════════════════════════════════════

Per /guide-gratuite/ list:
- intro (textarea, abstract)
- pdf_file (file, upload PDF)
- formato (text, es. "PDF · 12 pagine")
- categoria (taxonomy nuova "guida_categoria"? oppure semplice text)

═══════════════════════════════════════════════════════════════
DELIVERABLE Agent B
═══════════════════════════════════════════════════════════════

10 file JSON in acf-json/.

Tempo: ~2h.

Quando finito, segnala "Agent B done. 10 Field Groups CPT."
```

---

## 🤖 AGENT C — Theme Options (~1h)

```
🎯 Sei AGENT C di Wave 1. Lavori SOLO su Theme Options.

OBIETTIVO

1 Field Group per la Saltelli Settings page (acf_add_options_page già 
registrata in Wave 0). Contiene TUTTI i settings globali riusati 
cross-template.

═══════════════════════════════════════════════════════════════
TASK 1 — Field Group THEME OPTIONS (~1h)
═══════════════════════════════════════════════════════════════

```php
acf_add_local_field_group([
    "key" => "group_theme_options_v1",
    "title" => "Saltelli — Settings globali",
    "fields" => [
        
        // === TAB 1: Studio Settings ===
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
        
        // === TAB 2: Map ===
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
```

DELIVERABLE Agent C: 1 file group_theme_options*.json.

Tempo: ~1h.

Quando finito, segnala "Agent C done. Theme Options Field Group."
```

---

## 📋 SCRIPT TMUX wave1-launch.sh (3 panes)

Riadatto wave3-launch.sh per 3 panes. Lo creo a parte come file separato.

---

## ✅ DELIVERABLE Wave 1 totale

```
acf-json/ contiene:
  ✓ group_costi*.json
  ✓ group_casi*.json
  ✓ group_contatti*.json
  ✓ group_faq*.json
  ✓ group_info_shared*.json
  ✓ group_avvocato*.json
  ✓ group_competenza*.json
  ✓ group_faq_item*.json (saltelli_faq)
  ✓ group_caso_item*.json (saltelli_caso)
  ✓ group_modalita_item*.json
  ✓ group_scenario_item*.json
  ✓ group_principio_item*.json
  ✓ group_trust_item*.json
  ✓ group_formazione_item*.json
  ✓ group_guida_item*.json
  ✓ group_theme_options*.json

= 16 Field Groups · ~80+ custom fields · all editable da WP-Admin
```

Tempo elapsed Wave 1: ~2-3h con 3 agenti paralleli.

Wave 2 successivo: content migration (popolare i fields con valori da hardcoded template PHP).
