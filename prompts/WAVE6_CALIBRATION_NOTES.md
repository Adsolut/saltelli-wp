# 🔧 WAVE 6 — CALIBRATION NOTES (read FIRST before the v1.1 prompt)

> **Audience**: Claude Code agent dedicato Wave 6 (Extension blocchi GEO/CRO).
> **Funzione**: calibra 6 punti del prompt `PROMPT_AGENT_v1.1_WAVE6_GEO_CRO_BLOCKS.md` rispetto a:
> 1. La realtà del codice MVP confermata da Wave 5 in produzione (tag `v1.1.0-wave5-ia-refactor`)
> 2. Le 5 lessons learned cristallizzate in DEC-024 finale
> 3. Le decisioni post-Wave 5 (DEC-014/018/019/020/021/022/023/024)
> **Origine**: lettura puntuale codice MVP post-merge + report Wave 5 + audit findings.
> **Stato attuale**: ✅ POPOLATA, **0 placeholder TBD** — pronta per Wave 6 launch.

---

## 🎯 Cosa fare con questo file

1. Leggi prima `CLAUDE.md`.
2. **Leggi questo file** prima del prompt Wave 6.
3. Tieni questo file e `pattern-adaptation-map.md` a portata durante l'esecuzione.
4. Le calibrazioni qui hanno la **precedenza** sul prompt v1.1 dove c'è conflitto (raro, prompt v1.1 già allineato a Wave 5 done).

---

## 📍 CAL-W6-01 — URL pattern slug brevi DEC-022 (confermato Wave 5)

Il prompt v1.0 originale Wave 6 usava pattern URL `/aree-di-pratica/per-i-privati/...`. **Già aggiornato in v1.1**.

**Realtà confermata Wave 5 mergeata**: cluster URL segments sono **slug brevi**:
- `/aree-di-pratica/privati/{competenza-slug}/`
- `/aree-di-pratica/imprese/{competenza-slug}/`
- `/aree-di-pratica/contenzioso-amministrativo/{competenza-slug}/`

Esempi reali post-Wave 5:
- `/aree-di-pratica/privati/diritto-tributario/`
- `/aree-di-pratica/privati/diritto-del-lavoro/`
- `/aree-di-pratica/privati/diritto-di-famiglia-lgbtq/` (Tier-1 deep)
- `/aree-di-pratica/imprese/recupero-crediti/`
- `/aree-di-pratica/contenzioso-amministrativo/diritto-amministrativo/`

Il filter `post_type_link` con cache statica (in `cpt-competenza.php`) risolve dinamicamente il cluster — **NESSUN URL hardcoded nei template-parts**.

---

## 📍 CAL-W6-02 — `redirect_guess_404_permalink()` come safety net (NUOVA, ex-erronea su saltelli_option)

> **Note storica**: la versione precedente di questo file conteneva una calibrazione su `saltelli_option()` da introdurre come helper. **INVALIDATA** dall'audit Wave 5: `saltelli_option()` ESISTE già in `helpers.php` line 503 (assunzione orchestratore errata, vedi DEC-024 finale).

**Nuova calibrazione (lesson learned Wave 5 mini-fix B)**: WordPress core ha una funzione `redirect_guess_404_permalink()` (in `wp-includes/canonical.php`) che fa **fuzzy match su slug condivisi prefix** quando un permalink restituisce 404. Esempio: dopo DELETE di `diritto-di-famiglia` (slug `diritto-di-famiglia`), accessi a `/competenze/diritto-di-famiglia/` rispondono 301 a `diritto-di-famiglia-lgbtq` automaticamente, perché il sibling con slug più simile (start-with prefix) vince.

**Implicazione per Wave 6**: per nuovi slug introdotti (es. nuove competenze, glossario, FAQ), Claude Code può **affidarsi al WP fuzzy guess** come fallback safety net, ma deve essere consapevole che è una dipendenza implicita fragile (rompe se in futuro qualcuno rinomina lo slug target). Pattern raccomandato Wave 6:

```php
// Per i NUOVI URL pattern Wave 6 (es. /risorse/glossario/{lettera}/, /aree-di-pratica/{cluster}/{slug}/),
// preferire redirect 301 ESPLICITI in legacy-redirects.php mappa B
// piuttosto che dipendere dal WP fuzzy guess.

// Esempio nel pattern: NO affidarsi a fuzzy guess per:
'/risorse/glossario/a/' => '/risorse/glossario-legale/a/',  // ESPLICITO meglio

// Eccezione: per consolidazioni di slug post-DELETE post-MVP, il fuzzy guess è un OK fallback
// (non sostituire ma annotare come known dependency).
```

---

## 📍 CAL-W6-03 — Acceptance gate `wp rewrite list | grep <pattern>` post-flush

**Lesson learned Wave 5 BLOCKER A**: il bug `0/33 blog redirect chain FAIL` era probabilmente causato da `wp rewrite flush --hard` non persistito a fine Phase 8. Il primo `wp rewrite flush --hard` sul branch fix risolveva 33/33 senza nemmeno applicare FIX A.

**Action Wave 6**: **dopo OGNI `add_rewrite_rule` + `wp rewrite flush --hard`**, esegui acceptance gate `wp rewrite list | grep <pattern>` per verificare che la rule sia attiva. Esempio:

```bash
# Wave 6 — se introduci nuove rewrite rules (es. /risorse/glossario/lettera/{X}/ /faq/categoria/{cat}/, ecc.):
docker-compose exec -T wp wp rewrite flush --hard
docker-compose exec -T wp wp cache flush

# ACCEPTANCE GATE — verifica che le nuove rules siano attive:
docker-compose exec -T wp wp rewrite list --format=table | grep -E "glossario|faq" | head -10
# Atteso: vedere le pattern just-added nella lista. Se NO → flush non persistito → re-flush.
```

**Bonus**: usa anche **filter `request` priority 5** (pattern Wave 5 FIX A) come defense-in-depth se la rule rischia di essere ombrata da page hierarchy WP.

---

## 📍 CAL-W6-04 — Slug effettivi competenze (post-Wave 5 confermati)

**Realtà confermata** (post-Wave 5, validata da migration-matrix-v3.csv):

| Cluster | Slug REALI delle 17 competenze finali |
|---|---|
| privati (14) | `diritto-tributario`, `cartelle-esattoriali-e-multe`, `diritto-del-lavoro`, `diritto-di-famiglia-lgbtq`, `responsabilita-medica`, `diritto-bancario`, `diritto-condominiale`, `diritto-dellimmigrazione`, `diritto-penale`, `diritto-previdenziale`, `diritto-delle-successioni`, `risarcimento-danni`, `infortunistica-stradale`, `aste-immobiliari` |
| imprese (2) | `recupero-crediti`, `domiciliazione-dimpresa` |
| contenzioso-amministrativo (1) | `diritto-amministrativo` |

**Action**: nessun slug hardcoded nei template-part Wave 6. Tutti i loop dinamici via WP_Query / get_posts. Esempio per Related services (Pattern 10):

```php
// In single-competenza.php — Related services (Pattern 10)
$current_id = get_the_ID();
$current_terms = wp_get_object_terms($current_id, 'tipo-area', ['fields' => 'ids']);

$related = [];
$candidate_ids = saltelli_field('related_competenze', $current_id);  // ACF Wave 6 NEW field

if (!empty($candidate_ids) && is_array($candidate_ids)) {
    // Manual selection (ACF relationship field)
    $related = $candidate_ids;
} elseif (!empty($current_terms)) {
    // Auto: 3 competenze stesso cluster, escluso current
    $related_query = new WP_Query([
        'post_type' => 'competenza',
        'posts_per_page' => 3,
        'post__not_in' => [$current_id],
        'tax_query' => [[
            'taxonomy' => 'tipo-area',
            'field' => 'term_id',
            'terms' => $current_terms,
        ]],
        'orderby' => 'rand',
    ]);
    $related = wp_list_pluck($related_query->posts, 'ID');
}
```

Pattern auto-fallback: se ACF relationship vuota → 3 random stesso cluster. Robusto.

---

## 📍 CAL-W6-05 — `single-competenza.php` template — Tier-1 deep vs Tier-2 lighter branched logic

**Realtà** (post-Wave 5 stabilizzata): il MVP ha già un `single-competenza.php` con branched logic Tier-1 deep vs Tier-2 lighter. Wave 5 NON ha modificato la logica di branching.

**Action Wave 6**: prima di estendere, **leggi il file `single-competenza.php` corrente** e identifica come la branch logic è implementata. Probabili pattern:

```php
// Pattern A: branch su tassonomia tipo-area (NO, perché Wave 5 cluster sono privati/imprese, non tier1/tier2)
// Pattern B: branch su ACF field "tier" (verifica esistenza in group_competenza_v1)
// Pattern C: branch su lista hardcoded di slug Tier-1 (3 competenze deep)

// Pattern probabile (Pattern C) — verifica leggendo file:
$tier1_slugs = ['diritto-tributario', 'diritto-del-lavoro', 'diritto-di-famiglia-lgbtq'];
$is_tier1 = in_array($post->post_name, $tier1_slugs, true);
```

⚠️ **Attenzione post-Wave 5**: il Tier-1 LGBTQ+ ora ha slug `diritto-di-famiglia-lgbtq` (NO `diritto-di-famiglia` che è stato eliminato in DISCOVERY-01 consolidamento).

Wave 6 deve estendere **entrambi i branch**:
- **Tier-1 deep** (3 competenze): aggiunge answer-capsule + FAQ generalizzata + related-services + mini-form (full extension)
- **Tier-2 lighter** (14 competenze): aggiunge answer-capsule (se popolata) + related-services + mini-form (extension light, no FAQ se zero faq_associate)

L'answer-capsule è estensione **opzionale via ACF field**: se `answer_capsule` field vuoto → blocco non renderizzato, fallback a `the_excerpt()` o `the_content()` first paragraph. Graceful fallback come pattern Wave 3.

---

## 📍 CAL-W6-06 — Mobile sticky bar: `body class` exclusion via PHP conditional

**Pattern raccomandato** post-Wave 5: il `pattern-adaptation-map.md` Pattern 3 (mobile-sticky-bar) usa CSS `:not()` chains, ma post-Wave 5 alcune pages hanno cambiato slug (`chi-siamo` → `lo-studio`, `faq` → `domande-frequenti`).

**Action**: nei nuovi template-part `mobile-sticky-bar.php`, usa **PHP-level conditional** invece di CSS `:not()` chains:

```php
<?php
// Determina se mostrare mobile sticky bar
$show_mobile_bar = !is_singular('avvocato')      // single-avvocato ha già sticky CTA dedicato
                && !is_page('contatti')           // contatti ha form completo, no duplicato
                && !is_page('lo-studio')          // Lo Studio ha layout editorial dedicato
                && !is_404();

if ($show_mobile_bar) :
?>
<aside class="sl-mobile-bar" aria-label="Contatti rapidi">
    <a href="tel:+390811813119" aria-label="Chiama">📞</a>
    <a href="https://wa.me/393517138006" aria-label="WhatsApp">💬</a>
    <a href="<?php echo esc_url(home_url('/contatti/')); ?>" aria-label="Contatti">✉️</a>
</aside>
<?php endif; ?>
```

Più robusto + facile da debuggare. Il CSS resta semplice: `.sl-mobile-bar { display: grid; }` solo `@media (max-width: 768px)`.

---

## 📍 CAL-W6-07 — FAQPage schema generalization: verificare coabitazione Yoast

**Pattern**: il `pattern-adaptation-map.md` Pattern 5 dice "estendi `partial-faqpage.php` per generalizzare a tutte le competenze" oggi limitato a Tier-1 (`/faq/` pagina + 3 Tier-1 deep).

**Realtà** (da `inc/schema/partial-faqpage.php` MVP): file gestisce schema FAQPage emesso solo su `is_page('domande-frequenti')` + Tier-1 specifici (post-Wave 5 page slug FAQ è cambiato in `domande-frequenti`).

Generalizzare significa:

```php
// In partial-faqpage.php — generalizzazione Wave 6
if (is_singular('competenza')) {
    $faq_associate = saltelli_field('faq_associate');
    if (!empty($faq_associate) && is_array($faq_associate)) {
        // Emetti schema FAQPage con i faq linkati
        // ...
    }
}
```

**Action**: verifica che modificare `partial-faqpage.php` **non rompi la coabitazione Yoast** (Yoast NON deve emettere FAQPage automaticamente — coabitazione gestita in `inc/seo/yoast-schema-extensions.php`). Dopo modifica, smoke test:

```bash
# Schema validation post-Wave 6
curl -s "https://staging.studiolegalesaltelli.it/aree-di-pratica/privati/cartelle-esattoriali-e-multe/" \
  | grep -A 30 '"@type":"FAQPage"' | head -40

# Atteso: 1 sola FAQPage emessa (no duplicati Yoast vs custom)
```

Test cliente-facing: validazione su Google Rich Results Test (manuale, post-deploy staging Wave 7).

---

## ✅ Acceptance check post-calibrazione (per Claude Code Wave 6)

Prima di iniziare Phase 1 del prompt v1.1 Wave 6, conferma a te stesso:

- [ ] Ho letto questo file (CAL-W6-01 → CAL-W6-07)
- [ ] **NON aggiungo** Phase 0 con `saltelli_option()` helper (esiste già in helpers.php:503)
- [ ] Userò pattern URL slug brevi (`/privati/`, `/imprese/`, `/contenzioso-amministrativo/`) — NO `per-i-privati`
- [ ] Userò acceptance gate `wp rewrite list | grep <pattern>` dopo ogni `add_rewrite_rule + flush` (CAL-W6-03)
- [ ] Tutti i loop dinamici, NO slug competenze hardcoded (CAL-W6-04)
- [ ] Conoscoli slug LGBTQ+ è ora `diritto-di-famiglia-lgbtq` (no `diritto-di-famiglia`, eliminato Wave 5 DISCOVERY-01)
- [ ] Leggerò `single-competenza.php` corrente prima di estenderlo per capire branch logic Tier-1 vs Tier-2 (CAL-W6-05)
- [ ] Mobile sticky bar: PHP conditional, NO CSS `:not()` chains (CAL-W6-06)
- [ ] FAQPage generalization: verifica coabitazione Yoast post-modifica (CAL-W6-07)

---

## 📌 Lessons learned cristallizzate da Wave 5 (DEC-024 finale)

5 lezioni applicabili per Wave 6:

1. **Acceptance gate `wp rewrite list | grep <pattern>`** dopo `add_rewrite_rule + flush` — vedi CAL-W6-03
2. **Filter `request` priority < 10** per URL pattern collision con page hierarchy — pattern robusto per future rewrite Wave 6
3. **WP `redirect_guess_404_permalink()`** come safety net per consolidazioni post-DELETE — vedi CAL-W6-02
4. **ACF relationships non si auto-orfanizzano** dopo DELETE post — pulire manualmente i serialized array. Per Wave 6: se mai elimini un post linkato in ACF relationship altrove, controlla i postmeta `LIKE '%<id>%'`.
5. **Validazione report contro artifact**: il report Wave 5 originale dichiarava "10/10 PASS" ma artifact mostrava "0/33 FAIL". Per Wave 6: confronta sempre dichiarazioni report con contenuto effettivo file artifact prima del push.

---

## 🔗 Riferimenti

- `prompts/PROMPT_AGENT_v1.1_WAVE6_GEO_CRO_BLOCKS.md` — il prompt principale (v1.1 post-Wave5)
- `pattern-adaptation-map.md` — INPUT PRINCIPALE (10 pattern mappati)
- `WAVE5_CALIBRATION_NOTES.md` — calibrazioni Wave 5 (CAL-01→06) per riferimento storico
- `migration-matrix-v3.csv` — distribuzione cluster definitiva post-Wave 5 (slug REALI)
- `mvp-state-snapshot.md` v2 — stato MVP post-Wave 5 (sezione 9 changes summary)
- `CLAUDE.md` — single source of truth
- DEC-018/019/020/021/022/023/024 — decisioni in vigore
