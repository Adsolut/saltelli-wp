# SCF Orphan Fields — Cleanup Target Wave 6.1

> **Status:** documentazione audit · creato `2026-05-12` durante `chore/post-batch3-housekeeping` (post Wave Elena FB Batch 1-3).
>
> **Scope:** elenco field SCF rimasti orfani dopo rimozione delle relative section template-side nelle Wave Elena FB Batch 1-3. **NO eliminazione automatica** — rischio data-loss se Elena/Ludovica hanno popolato content che vogliono recuperare in futuro. Cleanup integrale = wave dedicata Wave 6.1 post-cut produzione, dopo conferma editor che il content non serve.

## Approccio cleanup (Wave 6.1)

Per ogni field group elencato sotto:

1. **Backup DB**: `wp db export ~/backups/wave6.1-pre-scf-cleanup-$(date +%Y%m%d).sql`
2. **Verifica con editor**: chiedere a Elena/Ludovica se valore field salvato è recuperabile o droppable
3. **Se droppable**: rimuovere il field dal JSON del field group + rimuovere le row corrispondenti da `wp_postmeta` con `wp eval "delete_post_meta($pid, '<field_name>')"`
4. **Smoke test admin**: aprire la Page in WP-Admin, verificare metabox SCF non mostri più il field
5. **Smoke test frontend**: nessuna section riappare (deve essere già invisibile per via del template che non legge il field)

---

## A. `group_aree_di_pratica_v1.json` — Page 2812 (aree-di-pratica hub)

**Orphan since:** Wave Elena FB Batch 1, Wave S, fix #9 (section `.sl-hub-cta` "Scrivici nota 24h" rimossa)
**Template che non legge più i field:** `template-parts/page-aree-di-pratica-hub.php`

| Field name | Label esistente in JSON | Type | Rationale orphan |
|---|---|---|---|
| `hub_aree_cta_eyebrow` | (vedi JSON) | text | Section CTA rimossa Wave S |
| `hub_aree_cta_title` | (vedi JSON) | text | Section CTA rimossa Wave S |
| `hub_aree_cta_btn_label` | (vedi JSON) | text | Section CTA rimossa Wave S |
| `hub_aree_cta_url` | (vedi JSON) | text/url | Section CTA rimossa Wave S |

**Count:** 4 field
**Cleanup target:** Wave 6.1 (rimuovere il blocco `hub_aree_cta_*` dal JSON)

---

## B. `group_lo_studio_v1.json` — Page 2811 (chi-siamo, post-P7 consolidamento)

**Orphan since:** Wave Elena FB Batch 1, Wave S, fix #12 (section "§ 02 — 1999" rimossa, anno 1999 preservato in timeline §05)
**Template che non legge più i field:** `template-parts/page-lo-studio.php`

| Field name | Label esistente in JSON | Type | Rationale orphan |
|---|---|---|---|
| `lo_studio_founding_year` | (vedi JSON) | text | Section "1999 founding" rimossa Wave S |
| `lo_studio_founding_h2` | (vedi JSON) | text | Section "1999 founding" rimossa Wave S |

**Count:** 2 field
**Cleanup target:** Wave 6.1

**Nota:** Se nel JSON esistono anche field correlati (es. `lo_studio_founding_lede`, `lo_studio_founding_paragraph`), verificare al cleanup time — tutto il blocco section va droppato.

---

## C. `group_tipo_area_term_v1.json` — Taxonomy `tipo-area` (3 term: privati, imprese, contenzioso-amministrativo)

**Orphan since:** Wave Elena FB Batch 1, Wave S, fix #16 (section `.sl-tipoarea__cta` "§ 04 — Primo incontro" rimossa per 3 term; "Ultima chiamata" resta da footer.php:107 pre-footer globale)
**Template che non legge più i field:** `template-parts/taxonomy-tipo-area.php`

| Field name | Type | Rationale orphan |
|---|---|---|
| `tipo_area_term_cta_label` | text | Section CTA "Primo incontro" rimossa Wave S |
| `tipo_area_term_cta_h2_main` | text | Section CTA rimossa Wave S |
| `tipo_area_term_cta_h2_em` | text | Section CTA rimossa Wave S |
| `tipo_area_term_cta_lede` | textarea | Section CTA rimossa Wave S |
| `tipo_area_term_cta_btn_label` | text | Section CTA rimossa Wave S |
| `tipo_area_term_cta_btn_url` | text/url | Section CTA rimossa Wave S |

**Count:** 6 field × 3 term = 18 row potenziali in `wp_termmeta`
**Cleanup target:** Wave 6.1 (rimuovere il blocco dal JSON + `wp eval` per droppare termmeta su 3 term)

---

## D. `group_prenota_appuntamento_v1.json` — Page 2714 (prenota-appuntamento) — LEGACY DEFENSIVE

**Status:** **NON orphan in senso puro** — è data-compat legacy.
**Wave introduce:** Wave Elena FB Batch 3, Wave J (#22 layout uniforma richiedi-preventivo).
**Template che usa il field:** `template-parts/page-info-shared.php` (Wave J) — fallback condizionale: se `body_content` (info-shared SCF) vuoto E slug == prenota-appuntamento, il template legge `prenota_intro` legacy come content body.

| Field name | Type | Rationale |
|---|---|---|
| `prenota_intro` | textarea | Backward-compat: editor pre-esistente sopravvive senza data migration |

**Count:** 1 field
**Cleanup target:** Wave 6.1 — **prerequisito**: migrazione data `prenota_intro` → `body_content` (info-shared) via `wp eval` script. Dopo migrazione, group `group_prenota_appuntamento_v1` può essere disattivato (location rule rimossa) + JSON cestinato.

**Script migrazione bozza (Wave 6.1):**
```php
$src = get_post_meta(2714, 'prenota_intro', true);
$dst = get_post_meta(2714, 'body_content', true);
if (!empty($src) && empty($dst)) {
    update_post_meta(2714, 'body_content', $src);
    delete_post_meta(2714, 'prenota_intro');
    echo "Migrated.\n";
} else {
    echo "Skipped: src empty OR dst non-vuoto (manual review).\n";
}
```

---

## Totale orphan field (cleanup Wave 6.1)

| Group | Field count | Affected entity |
|---|---|---|
| `group_aree_di_pratica_v1` | 4 | Page 2812 |
| `group_lo_studio_v1` | 2 | Page 2811 |
| `group_tipo_area_term_v1` | 6 | 3 term taxonomy (×3 = 18 row termmeta) |
| `group_prenota_appuntamento_v1` | 1 (LEGACY DEFENSIVE) | Page 2714 |
| **Totale** | **13 field schema** | |

---

## Cross-reference

- Wave Elena FB Batch 1 changelog: `CLAUDE.md` riga "Wave Elena Feedback Batch 1 — 13 fix Q+S" (5 fix S)
- Wave Elena FB Batch 3 changelog: `CLAUDE.md` riga "Wave Elena Feedback Batch 3 — 1 fix J"
- Cleanup chore Wave C BEM rename: vedi commit `chore/post-batch3-housekeeping` (CSS dead-code cleanup `.sl-tier1__*` 71 rule rimosse)
