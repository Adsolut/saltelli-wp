# Phase 1.D — Template usage matrix

Verifica empirica della gerarchia WP serving ognuna delle 7 Page target + le 4 hub Wave 4.7.fix.3 + lo-studio.

---

## Discovery method

`_wp_page_template` post_meta check + `page.php` dispatcher trace:

```sh
for ID in 23 2708 2709 2712 2711 372 2713; do
  TMPL=$(wp post meta get $ID _wp_page_template --path=...)
  echo "Page $ID: _wp_page_template='$TMPL'"
done
```

Output (2026-05-10):
```
Page 23:   _wp_page_template='default'
Page 2708: _wp_page_template=''
Page 2709: _wp_page_template=''
Page 2712: _wp_page_template=''
Page 2711: _wp_page_template=''
Page 372:  _wp_page_template='default'
Page 2713: _wp_page_template=''
```

Nessuna delle 7 pagine ha custom template tramite `_wp_page_template`. Tutte cadono su `page.php` default WP, che fa dispatch interno via `is_page($slug)` switch (linee 39-93).

---

## Dispatch matrix (page.php)

| `is_page()` match | Template-part servito | File path |
|---|---|---|
| `chi-siamo` | `page-chi-siamo-hub` | `template-parts/page-chi-siamo-hub.php` |
| `aree-di-pratica` | `page-aree-di-pratica-hub` | idem |
| `risorse` | `page-risorse-hub` | idem |
| `costi-e-consulenze` | `page-costi-e-consulenze-hub` | idem |
| `lo-studio` | `page-lo-studio` | `template-parts/page-lo-studio.php` |
| `casi` | `page-casi` | idem |
| `contatti` | `page-contatti` | idem |
| `glossario-legale` | `inc/wave3-glossario.php` (special) | inline |
| `['faq', 'domande-frequenti']` | `page-faq` | idem |
| `['guide-gratuite', 'come-lavoriamo', 'prima-consulenza', 'lavora-con-noi', 'richiedi-preventivo']` | `page-info-shared` | idem |
| `costi` | `page-costi` | idem |
| **default fallback** | `the_content()` inline (page.php:88) | `page.php:64-91` |

---

## Per Page target — chi chiama the_content()?

| Page | Template-part | `the_content()` chiamato? | Sorgente render |
|---|---|---|---|
| 23 contatti | page-contatti.php | **NO** | SCF `hero_*`, `map_*`, `come_arrivare_*`, `trust_signal` + Theme Options Studio Info |
| 2708 domande-frequenti | page-faq.php | **NO** | SCF `hero_*`, `toc_title`, `cta_*` + CPT `saltelli_faq` query con tassonomia `faq_topic` |
| 2709 guide-gratuite | page-info-shared.php | **CONDIZIONALE** (linea 93) — solo se `body_content` SCF vuoto | SCF `hero_*`, `aside_*`, `body_content` (prio 1), `cta_final_*` |
| 2712 come-lavoriamo | page-info-shared.php | idem | idem |
| 2711 prima-consulenza | page-info-shared.php | idem | idem |
| 372 lavora-con-noi | page-info-shared.php | idem | idem |
| 2713 richiedi-preventivo | page-info-shared.php | **SÌ** — `body_content` vuoto, fallback attivo | post_content WP nativo via the_content() |

---

## Verifica diretta — frontend check via curl

```sh
# Cerchiamo nel HTML rendered tracce univoche del post_content vs SCF body_content.

# /contatti/ — verifichiamo "Hai bisogno di aiuto?" (eyebrow zombie dal post_content)
curl -s https://staging.studiolegalesaltelli.it/contatti/ | grep -c "Hai bisogno di aiuto"
# Expected: 0 (non renderizzato)

# /risorse/domande-frequenti/ — verifichiamo "Apertura successione" (h2 zombie post_content)
curl -s https://staging.studiolegalesaltelli.it/risorse/domande-frequenti/ | grep -c "Apertura successione"
# Expected: 0

# /costi-e-consulenze/richiedi-preventivo/ — verifichiamo "Come funziona" (LIVE post_content)
curl -s https://staging.studiolegalesaltelli.it/costi-e-consulenze/richiedi-preventivo/ | grep -c "Come funziona"
# Expected: 1 (renderizzato via the_content() fallback)
```

Vedi Phase 4 smoke test per verifica empirica.

---

## Refactor scope per Phase 4

| Template | Action richiesta |
|---|---|
| `page.php` | Aggiungere check `has_scf_metabox` per Pages target (helper `saltelli_page_has_scf_content`); il render default fallback `the_content()` resta per Page WP non-target (back-compat) |
| `template-parts/page-info-shared.php` | **Rimuovere `the_content()` fallback** (linea 93). Per Page 2713, dopo migrazione, `body_content` SCF è popolato → render via SCF. Pages senza body_content → render vuoto silenzioso (fine, evita zombie display) |
| `template-parts/page-contatti.php` | Nessuna modifica (già SCF-only) |
| `template-parts/page-faq.php` | Nessuna modifica (già SCF-only) |
| `template-parts/page-lo-studio.php` | Da verificare — probabilmente già SCF-only |
| Altri template-parts (hub-*, casi, costi) | Out of scope — già refactored in Wave 4.7.fix.3 |

---

*Audit completato 2026-05-10*
