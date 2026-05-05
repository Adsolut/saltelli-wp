# Template Polish — URL Inventory

**Data:** 2026-04-30
**Theme version:** 0.7.0 → 0.8.0-beta-templates-mobile

---

## Avvocati (4 CPT)

| ID | Slug | URL | Note |
|---:|---|---|---|
| 2660 | emiliano-saltelli | `/avvocati/emiliano-saltelli/` | **Foto reale** _thumbnail_id=2683 |
| 2661 | fabiana-saltelli | `/avvocati/fabiana-saltelli/` | Placeholder editoriale |
| 2662 | antonia-battista | `/avvocati/antonia-battista/` | Placeholder editoriale |
| 2663 | stefano-gaetano-tedesco | `/avvocati/stefano-gaetano-tedesco/` | Placeholder editoriale |

## Competenze (19 CPT)

### Tier-1 (3 — deep treatment)
| ID | Slug | URL |
|---:|---|---|
| 2664 | diritto-tributario | `/competenze/diritto-tributario/` |
| 2665 | diritto-del-lavoro | `/competenze/diritto-del-lavoro/` |
| 2666 | diritto-di-famiglia-lgbtq | `/competenze/diritto-di-famiglia-lgbtq/` |

### Tier-2 (16 — standard)
| ID | Slug | URL |
|---:|---|---|
| 2682 | diritto-amministrativo | `/competenze/diritto-amministrativo/` |
| 2681 | consulenze-online | `/competenze/consulenze-online/` |
| 2680 | domiciliazione-dimpresa | `/competenze/domiciliazione-dimpresa/` |
| 2679 | responsabilita-civile | `/competenze/responsabilita-civile/` |
| 2678 | risarcimento-danni | `/competenze/risarcimento-danni/` |
| 2677 | diritto-delle-successioni | `/competenze/diritto-delle-successioni/` |
| 2676 | diritto-delle-assicurazioni | `/competenze/diritto-delle-assicurazioni/` |
| 2675 | diritto-previdenziale | `/competenze/diritto-previdenziale/` |
| 2674 | diritto-penale | `/competenze/diritto-penale/` |
| 2673 | diritto-dellimmigrazione | `/competenze/diritto-dellimmigrazione/` |
| 2672 | diritto-condominiale | `/competenze/diritto-condominiale/` |
| 2671 | diritto-bancario | `/competenze/diritto-bancario/` |
| 2670 | responsabilita-medica | `/competenze/responsabilita-medica/` |
| 2669 | diritto-di-famiglia | `/competenze/diritto-di-famiglia/` |
| 2668 | recupero-crediti | `/competenze/recupero-crediti/` |
| 2667 | cartelle-esattoriali-e-multe | `/competenze/cartelle-esattoriali-e-multe/` |

## Taxonomy `tipo-area` (4 terms)

| Slug | URL |
|---|---|
| privati | `/tipo-area/privati/` |
| imprese | `/tipo-area/imprese/` |
| contenzioso | `/tipo-area/contenzioso/` |
| altri | `/tipo-area/altri/` |

## Pages (CPT page)

| ID | Slug | URL | Note |
|---:|---|---|---|
| 17 | home | `/` | Front page (front-page.php) |
| 19 | chi-siamo | `/chi-siamo/` | About (`/lo-studio/` → redirect 301 → /chi-siamo/) |
| 21 | servizi-legali | `/servizi-legali/` | Legacy, non nel menu |
| 23 | contatti | `/contatti/` | Contact |
| n/a | costi | `/costi/` | Generated audit-alignment |
| n/a | competenze | `/competenze/` | Archive CPT competenza |
| n/a | avvocati | `/avvocati/` | Archive CPT avvocato |
| n/a | blog | `/blog/` | Posts archive (se esistente) |

## Blog sample post (per smoke test)

| ID | Slug | URL |
|---:|---|---|
| 2643 | intimazione-tari-annullata-vittoria-dello-studio-legale-saltelli | `/intimazione-tari-annullata-.../` |
| 2633 | lavoro-straordinario-quando-e-obbligatorio-e-come-deve-essere-pagato | `/lavoro-straordinario-.../` |
| 2611 | giornata-mondiale-della-sicurezza-sul-lavoro-diritti-del-lavoratore-e-obblighi-del-datore | `/giornata-mondiale-.../` |
| 557 | attenzione-alle-truffe-online | `/attenzione-alle-truffe-online/` |
| 554 | buoni-fruttiferi-postali-ottieni-il-rimborso | `/buoni-fruttiferi-postali-ottieni-il-rimborso/` |

## Sistema (404 + search)

| URL | Note |
|---|---|
| `/non-esiste-404/` | 404.php — must HTTP 404 |
| `/?s=tributario` | search.php — must HTTP 200 |

---

## Smoke test plan

20 URL totali:
- 1 homepage
- 4 single-avvocato + 1 archive-avvocato
- 5 single-competenza (3 tier-1 + 2 tier-2 sample) + 1 archive-competenza
- 4 taxonomy term archives (`/tipo-area/*`)
- 3 page generic (chi-siamo, contatti, costi)
- 1 single blog post (sample)
- 2 system (404 + search)
