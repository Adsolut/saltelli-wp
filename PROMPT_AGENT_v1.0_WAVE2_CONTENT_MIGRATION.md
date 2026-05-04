# PROMPT v1.0.0 WAVE 2 — Content Migration (sequenziale single agent)

> **Per Claude Code in nuova sessione (single agent).** Tempo: ~3h.
> **PRECEDENZA**: Wave 0 + Wave 1 + Wave 1 Recovery completati (16 Field Groups, 143 fields).
> **MISSIONE**: popolare i 16 Field Groups con valori reali estratti dal content hardcoded di `page.php`, `single-avvocato.php`, `single-competenza.php`. Frontend resta INVARIATO durante migration (refactor backend-only, Wave 3 farà il template refactor).

---

## 🎯 Tu sei

L'**Agente Content Migration**. Wave 1 ha creato 16 ACF Field Groups (143 fields totali, 0 popolati). Devi popolarli con i valori reali leggendoli da:

- `page.php` (1274 righe, blocchi `is_page()` con content hardcoded)
- `single-avvocato.php` (lawyer fields fallback)
- `single-competenza.php` (tier-1 fields fallback)
- Codice helper esistente (es. `inc/cases.php`, `inc/faq.php` se esistono)

```
WAVE 2 — 5 PHASES sequenziali

Phase 1: Theme Options globali (~30 min)         → 32 fields
Phase 2: Page WP fields (~45 min)                → 5 page × ~12 fields
Phase 3: CPT items (~60 min)                     → ~80 items totali
Phase 4: 4 lawyer + 3 tier-1 fields (~30 min)    → 4 lawyer × 12 + 3 × 11
Phase 5: Smoke + Bump + Deploy (~15 min)
```

---

## 📚 Letture obbligatorie

```
.claude/knowledge/recovery/PROJECT_STATE.md  (state consolidato)
PROMPT_AGENT_v1.0_WAVE1_RECOVERY.md          (riferimento field schema)
CLAUDE.md                                    (hard constraints)

wp-content/themes/saltelli/
  ├── page.php                          (SOURCE content hardcoded)
  ├── single-avvocato.php               (SOURCE lawyer fallback)
  ├── single-competenza.php             (SOURCE tier-1 fallback)
  ├── inc/                              (helper functions, cerca cases/faq/principi)
  └── acf-json/*.json                   (schema target Field Groups, 16 file)
```

---

## 🔒 Hard rules

| Rule | Decisione |
|---|---|
| **NO modifiche frontend** template (sito invariato durante migration) | Wave 3 dopo |
| **NO modifiche tokens.css** | locked |
| **NON sovrascrivere** `_thumbnail_id` Emiliano + bio_estesa Step D + post_content CPT esistenti | content protetto |
| **WP-CLI eval** o script PHP per migration via update_field() | atomic safe |
| **Backup pre-migration**: `git stash` o snapshot DB | safety (anche se git è backup principale) |
| **Verify**: `get_field()` ritorna valore atteso dopo update | regression check |
| **Smoke test** frontend dopo OGNI Phase (atteso invariato) | safety |
| **Commit incrementale**: 1 commit per Phase | audit trail |
| **Path droplet**: `/var/www/saltelli/` | lesson learned |

---

## 📋 PHASE 1 — Theme Options globali (~30 min)

Single source of truth per NAP, brand, footer, social, CTA defaults — riusato cross-template.

### 1.1 — Backup snapshot DB

```bash
cd /Users/aldosantoro/Desktop/DEV/saltelli-wp
docker compose exec saltelli-db mysqldump -u root -psaltelli saltelli > /tmp/saltelli-pre-wave2.sql 2>&1 | tail -3
```

### 1.2 — Migration via WP-CLI eval

Estrai valori da `inc/` o `header.php`/`footer.php` esistenti per coerenza. Esempio template:

```bash
docker compose run --rm wpcli eval '
// === TAB 1: Studio Info ===
update_field("studio_indirizzo_via", "Via Vannella Gaetani 27", "options");
update_field("studio_cap_citta", "80121 Napoli", "options");
update_field("studio_quartiere", "Chiaia", "options");
update_field("studio_orari_settimana", "Lun – Ven · 09:30 – 18:30", "options");
update_field("studio_orari_sabato", "Sabato su appuntamento", "options");
update_field("studio_telefono_pubblico", "+39 081 1813 1119", "options");
update_field("studio_email", "info@studiolegalesaltelli.it", "options");
update_field("studio_pec", "studiolegalesaltelli@pec.it", "options");
update_field("studio_piva", "06685101211", "options");
update_field("studio_ordine_avvocati", "Ordine degli Avvocati di Napoli", "options");

// === TAB 2: Mappa ===
update_field("studio_coordinate_lat", "40.83257", "options");
update_field("studio_coordinate_lng", "14.24227", "options");

// === TAB 3: Brand ===
update_field("brand_payoff", "Diritto, con misura", "options");
update_field("brand_statement_short", "Un atelier legale italiano. Quattro avvocati a Chiaia. Vent\\'anni di pratica accanto a famiglie e imprese.", "options");

// === TAB 4: Footer ===
update_field("footer_credit_text", "Realizzato da Adsolut Web Agency", "options");
update_field("footer_credit_url", "https://adsolut.it", "options");
update_field("footer_newsletter_enabled", true, "options");
update_field("footer_newsletter_provider", "static", "options");

// === TAB 5: Social (vuoti per ora, Duccio popola dopo) ===
// update_field("social_instagram", "", "options");
// update_field("social_linkedin", "", "options");

// === TAB 6: CTA Defaults ===
update_field("cta_default_label", "Prenota un incontro →", "options");
update_field("cta_default_url", "/contatti/", "options");
update_field("cta_trust_signal", "Risposta entro 24 ore · Riservatezza assoluta", "options");
update_field("cta_subline_italic", "Prima consulenza conoscitiva gratuita", "options");

echo "OK Phase 1 — Theme Options populated";
'
```

NB: rivedi i valori reali dal codice esistente (es. `get_studio_phone()` o costanti definite). Se trovi divergenze tra hardcoded valori, scegli quello **più presente** o chiedi a Duccio.

### 1.3 — Verify

```bash
docker compose run --rm wpcli eval '
$keys = ["studio_indirizzo_via", "studio_telefono_pubblico", "studio_email", "brand_payoff", "footer_credit_text", "cta_default_label"];
foreach ($keys as $k) {
    $v = get_field($k, "options");
    echo str_pad($k, 30) . " = " . substr($v, 0, 50) . "\n";
}
'
```

### 1.4 — Commit

```bash
git add -A
git commit -m "feat(s2-v1.0.0-wave2): Phase 1 — Theme Options populated (32 fields globali)"
git push origin main
```

---

## 📋 PHASE 2 — Page WP custom fields (~45 min)

Per ogni page WP custom, popola i fields ACF con valori estratti da `page.php` blocco `is_page()`.

### 2.1 — /costi/ (page id 2695, 17 fields)

Estrai da `page.php` blocco `is_page('costi')`:

```bash
# Identifica il blocco costi
grep -n "is_page('costi')\|is_page(\"costi\")" wp-content/themes/saltelli/page.php
```

Esempio template script (adatta valori):

```bash
docker compose run --rm wpcli eval '
$page_id = get_page_by_path("costi")->ID;

// HERO
update_field("hero_eyebrow", "§ Servizio · Costi", $page_id);
update_field("hero_h1_pre", "Costi e prima", $page_id);
update_field("hero_h1_em", "consulenza.", $page_id);
update_field("hero_lede", "Trenta minuti di prima consulenza conoscitiva gratuita. Solo dopo, un preventivo trasparente basato sulla complessità reale della tua pratica.", $page_id);

// ASIDE TRUST BOX (estrai da hero side card hardcoded)
update_field("aside_eyebrow", "§ Prima consulenza", $page_id);
update_field("aside_h3", "Trenta minuti, gratuiti.", $page_id);
update_field("aside_p", "In studio o online. Senza obblighi né costi nascosti. Riservatezza assoluta.", $page_id);
update_field("aside_cta_label", "Prenota un incontro →", $page_id);
update_field("aside_cta_url", "/contatti/", $page_id);

// § 03 Body editorial (estrai sezione "Come calcoliamo i preventivi")
update_field("calc_body", "<p>Il preventivo è il primo atto di trasparenza...</p>", $page_id);

// CTA finale
update_field("cta_eyebrow", "§ Pronto?", $page_id);
update_field("cta_h2", "La prima consulenza è gratuita. Sempre.", $page_id);
update_field("cta_p", "Trenta minuti per inquadrare insieme la tua pratica e capire come possiamo esserti utili.", $page_id);
update_field("cta_label", "Prenota un incontro →", $page_id);
update_field("cta_url", "/contatti/", $page_id);
update_field("cta_trust", "Risposta entro 24 ore · Riservatezza assoluta", $page_id);

echo "OK costi populated";
'
```

NB: i valori esatti DEVONO essere estratti dal codice attuale `page.php` per garantire frontend invariato. NON inventare nuovi valori.

### 2.2 — /casi/ (page id 2699, 10 fields)

Pattern simile, estrai da `page.php` blocco `is_page('casi')`:
- hero_eyebrow, hero_h1_pre, hero_h1_em, hero_lede
- intro_body (lede dropcap target)
- cta_eyebrow, cta_h2, cta_p, cta_label, cta_url

### 2.3 — /contatti/ (page id 23, 10 fields)

Estrai da `page.php` blocco `is_page('contatti')`:
- hero fields
- map_iframe (iframe HTML embed da template)
- map_caption
- come_arrivare_title, come_arrivare_metro, come_arrivare_parking
- trust_signal

### 2.4 — /faq/ (page id 2705, 10 fields)

Aggregator page, fields semplici (FAQ items vanno in CPT `saltelli_faq` Phase 3):
- hero fields
- toc_title
- cta fields

### 2.5 — /info-shared/ (5 page: 2706, 2709, 2708, 372, 2710 — 16 fields ognuna)

5 page con stesso schema, content diverso:
- guide-gratuite (2706)
- come-lavoriamo (2709)
- prima-consulenza (2708)
- lavora-con-noi (372)
- richiedi-preventivo (2710)

Per ognuna estrai content unico da `page.php` blocco corrispondente.

NB: verifica anche `prenota-appuntamento` (page 2711) — potrebbe richiedere aggiunta al location rule del Field Group `group_info_shared_v1`. Se sì, update Field Group location prima di popolare.

### 2.6 — Verify Phase 2

```bash
docker compose run --rm wpcli eval '
$pages = ["costi" => 2695, "casi" => 2699, "contatti" => 23, "faq" => 2705];
foreach ($pages as $slug => $id) {
    $val = get_field("hero_eyebrow", $id);
    echo str_pad($slug, 15) . " hero_eyebrow = " . $val . "\n";
}
'
```

### 2.7 — Commit Phase 2

```bash
git add -A
git commit -m "feat(s2-v1.0.0-wave2): Phase 2 — Page WP fields populated (5 pages, ~63 fields)"
git push origin main
```

---

## 📋 PHASE 3 — CPT items (~60 min)

Crea ~80 CPT items con WP-CLI `wp post create` + ACF fields.

### 3.1 — saltelli_modalita (3 items)

Estrai da `page.php` blocco `is_page('costi')`, sezione "§ 01 — 3 modalità":

```bash
docker compose run --rm wpcli eval '
// Modalità 1: In Studio
$id = wp_insert_post([
    "post_type" => "saltelli_modalita",
    "post_status" => "publish",
    "post_title" => "In Studio",
    "menu_order" => 1,
]);
update_field("num_label", "01", $id);
update_field("title", "In Studio", $id);
update_field("body", "Trenta minuti seduti al tavolo dello studio, a Chiaia. Documenti alla mano, sguardo negli occhi.", $id);
update_field("trust_mini", "Da preferire per pratiche complesse", $id);

// Modalità 2: Online
$id = wp_insert_post([...]);
// idem

// Modalità 3: Telefonica
$id = wp_insert_post([...]);

echo "OK modalità 3 items";
'
```

### 3.2 — saltelli_scenario (3 items)

Estrai sezione "§ 02 — Tre scenari dopo i 30 min".

### 3.3 — saltelli_principio (3 items)

Estrai sezione "§ Come lavoriamo" (3 principi: Ascoltiamo/Atelier/Verità).

### 3.4 — saltelli_trust (4 items)

Estrai "§ 05 Trust signals" da /costi/.

### 3.5 — saltelli_caso (~12 items)

Estrai casi rappresentativi da template + helper. Verifica se esiste `inc/cases.php` o array in `single-avvocato.php`.

### 3.6 — saltelli_faq (~28 items)

Estrai FAQ da:
- /faq/ aggregator page hardcoded
- /costi/ FAQ
- /tier-1 FAQ (3 page)

Assigna `faq_topic` taxonomy (Tributario / Lavoro / Famiglia / Costi / Metodo / Studio).

```bash
docker compose run --rm wpcli eval '
// Esempio FAQ tributario
$id = wp_insert_post([
    "post_type" => "saltelli_faq",
    "post_status" => "publish",
    "post_title" => "Posso impugnare una cartella esattoriale?",
]);
update_field("risposta", "Sì, entro 60 giorni dalla notifica. Lo Studio valuta gratuitamente la fondatezza dell\\'impugnazione...", $id);

// Assign taxonomy
$term = get_term_by("slug", "tributario", "faq_topic");
if (!$term) {
    $term_data = wp_insert_term("Diritto Tributario", "faq_topic", ["slug" => "tributario"]);
    $term_id = $term_data["term_id"];
} else {
    $term_id = $term->term_id;
}
wp_set_object_terms($id, $term_id, "faq_topic");

echo "OK faq item created";
'
```

### 3.7 — saltelli_formazione (~16 items)

Estrai timeline formazione 4 lawyer (Emiliano + Fabiana + Antonia + Stefano).

### 3.8 — saltelli_guida (8-12 items)

Verifica se esistono guide hardcoded o elenchi in template `/guide-gratuite/`. Se vuoto, crea placeholder editabili da Elena post-launch.

### 3.9 — Verify Phase 3

```bash
for CPT in saltelli_modalita saltelli_scenario saltelli_principio saltelli_trust saltelli_faq saltelli_caso saltelli_formazione saltelli_guida; do
    COUNT=$(docker compose run --rm wpcli post list --post_type=$CPT --post_status=publish --format=count 2>&1 | grep -vE "^Container|^Success" | tail -1)
    printf "  %-30s items: %s\n" "$CPT" "$COUNT"
done
```

### 3.10 — Commit Phase 3

```bash
git add -A
git commit -m "feat(s2-v1.0.0-wave2): Phase 3 — CPT items populated (~80 items)"
git push origin main
```

---

## 📋 PHASE 4 — Lawyer + Tier-1 fields (~30 min)

### 4.1 — 4 Lawyer (avvocato CPT, ~12 fields each)

Per ogni lawyer (Emiliano, Fabiana, Antonia, Stefano), popola fields ACF estraendo da `single-avvocato.php` fallback hardcoded.

```bash
docker compose run --rm wpcli eval '
// Lawyer Emiliano (id da verificare con wp post list --post_type=avvocato)
$emiliano_id = get_page_by_path("emiliano-saltelli", OBJECT, "avvocato")->ID;

update_field("hero_role", "Founding Partner & Avvocato Cassazionista", $emiliano_id);
update_field("specializzazioni", "Diritto Tributario\nContenzioso fiscale\nCartelle esattoriali", $emiliano_id);
update_field("bio_breve", "...", $emiliano_id);
update_field("bio_estesa", "...", $emiliano_id);

// Aree competenza correlate (post_object multiple)
$tributario = get_page_by_path("diritto-tributario", OBJECT, "competenza");
update_field("aree_competenza_correlate", [$tributario->ID, ...], $emiliano_id);

// Formazione (post_object multiple → CPT items Phase 3)
$formazione_ids = get_posts([
    "post_type" => "saltelli_formazione",
    "post_status" => "publish",
    "meta_query" => [["key" => "lawyer_ref", "value" => $emiliano_id]],
    "fields" => "ids",
    "numberposts" => -1
]);
update_field("formazione", $formazione_ids, $emiliano_id);

// Casi (idem)
$casi_ids = get_posts([...]);
update_field("casi_rappresentativi", $casi_ids, $emiliano_id);

echo "OK Emiliano populated";
'
```

NB: foto avvocato (`foto_ritratto`) → SKIP (Ludovica fornirà servizio fotografico, NON sovrascrivere `_thumbnail_id` Emiliano esistente).

### 4.2 — 3 Tier-1 (competenza CPT, ~11 fields each)

3 competenze: Tributario, Lavoro, Famiglia LGBTQ+.

```bash
docker compose run --rm wpcli eval '
$tributario_id = get_page_by_path("diritto-tributario", OBJECT, "competenza")->ID;

update_field("is_tier_1", true, $tributario_id);
update_field("tier_label", "§ Tier 1 · Approfondimento", $tributario_id);
update_field("subtitle", "Cartelle, accertamenti, contenzioso fiscale.", $tributario_id);
update_field("answer_capsule", "Lo Studio Saltelli & Partners assiste imprese e privati nel contenzioso tributario...", $tributario_id);
update_field("body_extended", "<wysiwyg content>", $tributario_id);

// Lead attorneys (Emiliano)
update_field("lead_attorneys", [$emiliano_id], $tributario_id);

// Casi tier-1
update_field("casi_rappresentativi", [...], $tributario_id);

// FAQ tier-1
update_field("faq", [...], $tributario_id);

echo "OK tier-1 tributario populated";
'
```

### 4.3 — Verify Phase 4

```bash
docker compose run --rm wpcli eval '
$lawyers = ["emiliano-saltelli", "fabiana-saltelli", "antonia-battista", "stefano-tedesco"];
foreach ($lawyers as $slug) {
    $p = get_page_by_path($slug, OBJECT, "avvocato");
    if ($p) {
        $role = get_field("hero_role", $p->ID);
        echo str_pad($slug, 25) . " role = " . $role . "\n";
    }
}
'
```

### 4.4 — Commit Phase 4

```bash
git add -A
git commit -m "feat(s2-v1.0.0-wave2): Phase 4 — Lawyer (4) + Tier-1 (3) fields populated"
git push origin main
```

---

## 📋 PHASE 5 — Smoke + Bump + Deploy (~15 min)

### 5.1 — Smoke verify globale

```bash
echo "═══ ACF FIELDS POPULATED COUNT ═══"
docker compose run --rm wpcli eval '
$total_pages = 0;
$total_fields_filled = 0;

// Theme Options
$theme_keys = ["studio_indirizzo_via", "brand_payoff", "footer_credit_text", "cta_default_label"];
foreach ($theme_keys as $k) {
    if (get_field($k, "options")) $total_fields_filled++;
}
echo "Theme Options sampled: $total_fields_filled/4 populated\n";

// Pages WP
$pages = [2695, 2699, 23, 2705, 2706, 2709, 2708, 372, 2710];
foreach ($pages as $id) {
    $val = get_field("hero_eyebrow", $id);
    if ($val) $total_pages++;
}
echo "Pages with hero_eyebrow filled: $total_pages/" . count($pages) . "\n";

// CPT items count
$cpts = ["saltelli_modalita", "saltelli_scenario", "saltelli_principio", "saltelli_trust", "saltelli_faq", "saltelli_caso", "saltelli_formazione"];
foreach ($cpts as $cpt) {
    $count = wp_count_posts($cpt)->publish;
    echo str_pad($cpt, 30) . " items: $count\n";
}
'

echo ""
echo "═══ FRONTEND SMOKE TEST (atteso invariato) ═══"
for URL in / /chi-siamo/ /avvocati/ /casi/ /costi/ /contatti/ /faq/ /come-lavoriamo/ /competenze/diritto-tributario/; do
    HTTP=$(curl -sL -o /dev/null -w "%{http_code}" "https://staging.studiolegalesaltelli.it$URL?_=v1w2" -m 5)
    echo "  $URL → HTTP $HTTP"
done
```

### 5.2 — Bump version

```bash
sed -i.bak 's/Version: [0-9.]\+.*/Version: 1.0.0-recovery-wave2/' wp-content/themes/saltelli/style.css
sed -i.bak "s/SALTELLI_THEME_VERSION', '[^']*'/SALTELLI_THEME_VERSION', '1.0.0-recovery-wave2'/" wp-content/themes/saltelli/functions.php
rm -f wp-content/themes/saltelli/{style.css,functions.php}.bak
```

### 5.3 — Deploy droplet

ATTENZIONE: la migration ACF popola valori in DB locale. Per replicare sul droplet, 2 opzioni:

**Opzione A (raccomandato): replica script migration sul droplet**
```bash
ssh deploy@178.62.207.50 "
    cd /var/www/saltelli
    sudo -u www-data wp eval '
        // PHASE 1 stesso script qui
        update_field(...);
    '
"
```

**Opzione B: dump DB local + import su droplet**
```bash
docker compose exec saltelli-db mysqldump -u root -psaltelli saltelli > /tmp/saltelli-wave2-acf.sql

# Solo righe rilevanti (acf options + post_meta)
grep "acf_" /tmp/saltelli-wave2-acf.sql > /tmp/saltelli-wave2-acf-options.sql
# (workflow complesso, preferisci Opzione A)
```

Decisione: **Opzione A** (script idempotente, safe).

```bash
# Sync script migration su droplet + run
scp scripts/wave2-migration.sh deploy@178.62.207.50:/tmp/
ssh deploy@178.62.207.50 "
    bash /tmp/wave2-migration.sh
    sudo -u www-data wp cache flush --path=/var/www/saltelli
"
```

(Se non hai uno script bundle, replica manualmente i comandi WP-CLI eval delle Phase 1-4 su droplet.)

### 5.4 — Final commit

```bash
git add -A
git commit -m "feat(v1.0.0-wave2): Wave 2 complete — Content migrated to ACF (~140 fields populated, ~80 CPT items)"
git push origin main
```

---

## 📊 DELIVERABLE finale Wave 2

Report: `.claude/knowledge/recovery/v1.0-WAVE2-CONTENT-MIGRATION.md`

```markdown
# v1.0.0 Wave 2 COMPLETE — Content Migration

## Score: 5/5 phases PASS

## Per phase
- Phase 1 — Theme Options globali: ✓ 32 fields populated
- Phase 2 — Page WP custom fields: ✓ 5 pages × ~12 = ~60 fields
- Phase 3 — CPT items: ✓ ~80 items totali (8 CPT)
- Phase 4 — Lawyer + Tier-1 fields: ✓ 4×12 + 3×11 = ~80 fields
- Phase 5 — Smoke + bump + deploy: ✓

## Cumulative content migrated

Theme Options:           32 fields (NAP, brand, footer, social, CTA defaults)
Page WP custom:          ~60 fields (5 pages with hero/aside/cta)
Lawyer fields:           ~48 fields (4 × 12)
Tier-1 fields:           ~33 fields (3 × 11)
CPT items count:         ~80 records (modalità, scenari, principi, trust, FAQ, casi, formazione, guide)

TOTAL: ~140 fields populated + ~80 CPT items + Theme Options

## Frontend invariato
Sito staging.studiolegalesaltelli.it visivamente identico.
Refactor 100% backend per editor handoff.

## Editor readiness check (Wave 3 prep)
Adesso WP-Admin mostra:
✓ Saltelli — Settings popolato (Elena può modificare orari, telefono, email)
✓ Pages WP custom hanno fields editabili (Elena modifica hero, lede, CTA)
✓ CPT items list (Elena/Ludovica add/edit FAQ, casi, guide)
✓ Lawyer profiles editabili (bio, formazione, casi via post_object)

NEXT: Wave 3 = Template refactor (page.php usa get_field() invece di hardcoded).
Tempo stimato: ~2-2.5h.
```

Quando finito, segnala "Wave 2 COMPLETE. ~140 fields populated."

---

## 🆘 Se incontri imprevisti

```
- Field Group location ID mismatch: verifica con `wp post list --post_type=page` ID corretti
- update_field() ritorna false: verifica $post_id valido + field key esistente
- Special chars in update_field (italics, em dash): usa escape PHP appropriato
- /prenota-appuntamento/ (id 2711) non incluso info_shared: aggiungi al location rule
- WP-CLI eval timeout su batch grandi: spezza in chunks più piccoli
- Droplet migration replica: usa Opzione A (script idempotente WP-CLI eval)
- Backup pre-Wave 2 in /tmp/saltelli-pre-wave2.sql (rollback se serve)
```

Tempo realistic Wave 2: ~3h sequenziale single agent.

Quando completata Wave 2, frontend ancora invariato (perché template usa SOLO fallback hardcoded).
Wave 3 farà il refactor template a `get_field()` per leggere i nuovi valori.
