# Wave 5 IA Refactor — Pre-state audit

**Data snapshot**: 2026-05-06 ~15:50
**Branch**: `feat/wave5-ia-refactor` (da `main` @ `86c9939`)
**Theme version pre**: `1.0.0-recovery-wave3-debug`

---

## Backup completati

| File | Path | Size |
|---|---|---|
| Theme tar.gz | `~/backups/saltelli-pre-wave5-2026-05-06-1550.tar.gz` | 324K |
| DB dump | `~/backups/saltelli-pre-wave5-2026-05-06-1550.sql` | 57M |

---

## CPT count (publish)

| CPT | Publish | Draft | Note |
|---|---|---|---|
| `competenza` | 19 | 3 | 3 draft: `contrattualistica`, `ricorsi`, `diritto-societario` (legacy Wave 2 residui — non toccare) |
| `avvocato` | 4 | 0 | Emiliano, Fabiana, Antonia, Stefano |
| `saltelli_caso` | 10 | 0 | CSV cliente diceva 9 — diff +1, non bloccante (B5.4 vale tutti) |
| `saltelli_faq` | n/a | n/a | non in scope Wave 5 |
| Altri `saltelli_*` | n/a | n/a | non in scope Wave 5 |

---

## Tassonomia `tipo-area` — STATE PRE

| term_id | name | slug | count | Note |
|---|---|---|---|---|
| 992 | Per i Privati | `privati` | 9 | ✅ slug OK |
| 993 | Per le Imprese | `imprese` | 4 | ✅ slug OK |
| 994 | Contenzioso Amministrativo | `contenzioso` | 4 | ⚠️ slug da rinominare in `contenzioso-amministrativo` (DEC-022) |
| 995 | Altri servizi | `altri` | 2 | ⚠️ deprecate dopo retag (count → 0) |

**Action Phase 2/3**: rinomina `contenzioso` → `contenzioso-amministrativo` (slug update), retag tutti i 17 KEEP+CREATE secondo CSV cliente, deprecate `altri` quando count=0.

---

## Slug discovery CSV — Confronto vs DEC-021 cliente-firmato

Vedi `slug-discovery.csv` (output completo) e `blockers.md` (disclosure scoperte).

### Mappatura slug CSV cliente → slug REALE DB

| Riga CSV | Titolo | CSV `slug_proposto` | Slug REALE DB | Match? |
|---|---|---|---|---|
| 01 | Tributario | `tributario` | `diritto-tributario` | ❌ |
| 02 | Cartelle esattoriali | `cartelle-esattoriali` | `cartelle-esattoriali-e-multe` | ❌ |
| 03 | Recupero crediti | `recupero-crediti` | `recupero-crediti` | ✅ |
| 04 | Diritto del lavoro | `diritto-del-lavoro` | `diritto-del-lavoro` | ✅ |
| 05 | Diritto di famiglia LGBTQ+ | `diritto-di-famiglia-lgbtq` | `diritto-di-famiglia-lgbtq` | ✅ |
| 06 | Responsabilità medica | `responsabilita-medica` | `responsabilita-medica` | ✅ |
| 07 | Bancario | `bancario` | `diritto-bancario` | ❌ |
| 08 | Condominiale e immobiliare | `condominiale-immobiliare` | `diritto-condominiale` | ❌ |
| 09 | Immigrazione | `immigrazione` | `diritto-dellimmigrazione` | ❌ |
| 10 | Penale | `penale` | `diritto-penale` | ❌ |
| 11 | Previdenziale | `previdenziale` | `diritto-previdenziale` | ❌ |
| 12 | Successioni | `successioni` | `diritto-delle-successioni` | ❌ |
| 13 | Risarcimento Danni | `risarcimento-danni` | `risarcimento-danni` | ✅ |
| 14 | Domiciliazione impresa | `domiciliazione-impresa` | `domiciliazione-dimpresa` | ❌ |
| 15 | Diritto amministrativo | `diritto-amministrativo` | `diritto-amministrativo` | ✅ |
| DEL01 | Assicurazioni | `assicurazioni` | `diritto-delle-assicurazioni` | ❌ ma stessa entità |
| DEL02 | Responsabilità civile | `responsabilita-civile` | `responsabilita-civile` | ✅ esiste |
| DEL03 | Consulenze online | `consulenze-online` | `consulenze-online` | ✅ esiste |
| DEL04 | Diritto commerciale | `diritto-commerciale` | — non esiste | ❌ skip idempotent |

**Conclusione**: 7 slug CSV combaciano col DB; 12 differiscono. Phase 3.6.a userà gli slug REALI del DB, non quelli del CSV. CAL-01 confermato + esteso.

---

## Pages publish — STATE PRE

| ID | post_name | post_title | post_parent | post_status | Wave 5 action |
|---|---|---|---|---|---|
| 17 | home | Home | 0 | publish | ✅ invariata |
| 19 | chi-siamo | Lo studio | 0 | publish | 🔄 rename slug → `lo-studio` (CAL-05) |
| 23 | contatti | Contatti | 0 | publish | ✅ invariata + sub-page lavora-con-noi |
| 321 | competenze | Competenze | 0 | publish | ⚠️ verificare se è la archive page o pagina libera |
| 356 | conferma | Conferma | 0 | publish | ✅ invariata (legacy form thank-you) |
| 361 | prenota-un-appuntamento | Prenota un appuntamento | 0 | publish | ⚠️ legacy, non toccare |
| 372 | lavora-con-noi | Lavora con noi | 0 | publish | 🔄 parent_id → contatti |
| 1413 | blog | Blog | 0 | publish | ✅ archive blog |
| 2695 | costi | Costi e prima consulenza | 0 | publish | 🔄 parent_id → costi-e-consulenze hub |
| 2699 | casi | Casi rappresentativi | 0 | publish | ⚠️ collision con archive saltelli_caso |
| 2705 | faq | Domande frequenti | 0 | publish | 🔄 rename slug → `domande-frequenti` + parent_id → risorse hub |
| 2706 | guide-gratuite | Guide gratuite | 0 | publish | 🔄 parent_id → risorse hub |
| 2707 | glossario-legale | Glossario legale | 0 | publish | 🔄 rewrite path → `/risorse/glossario-legale/` |
| 2708 | prima-consulenza | Prima consulenza | 0 | publish | 🔄 parent_id → costi-e-consulenze hub |
| 2709 | come-lavoriamo | Come lavoriamo | 0 | publish | 🔄 parent_id → costi-e-consulenze hub |
| 2710 | richiedi-preventivo | Richiedi un preventivo | 0 | publish | 🔄 parent_id → costi-e-consulenze hub |
| 2711 | prenota-appuntamento | Prenota un appuntamento | 0 | publish | ⚠️ duplicato di 361 — non toccare |

Pages draft (27): legacy Elementor non in scope Wave 5.

---

## Avvocati (4 publish)

| ID | post_name | post_title | URL post-Wave5 |
|---|---|---|---|
| 2660 | emiliano-saltelli | Emiliano Saltelli | `/chi-siamo/team/emiliano-saltelli/` |
| 2661 | fabiana-saltelli | Fabiana Saltelli | `/chi-siamo/team/fabiana-saltelli/` |
| 2662 | antonia-battista | Antonia Battista | `/chi-siamo/team/antonia-battista/` |
| 2663 | stefano-gaetano-tedesco | Stefano Gaetano Tedesco | `/chi-siamo/team/stefano-gaetano-tedesco/` |

---

## Casi (10 publish, B5.4 tutti go-public)

ID range 2748-2756 + 2797. Slug encoding `%c2%b7` per "·" middle dot — gestito nativo WP, non bloccante.

URL post-Wave5: `/chi-siamo/risultati/{slug}/`.

---

## Pre-flight calibrazioni applicate

- **CAL-01** ✅ Discovery slug effettivi → `slug-discovery.csv` (19 publish + 3 draft)
- **CAL-02** ✅ Loop DELETE idempotente per `diritto-commerciale` (non esiste DB MVP); altre 3 esistono
- **CAL-03** ✅ Aggiornamento legacy-redirects.php esistenti previsto Phase 6
- **CAL-04** ✅ Pattern `init priority 1` da estendere, non `template_redirect`
- **CAL-05** ✅ `page.php` mantiene `is_page()` chain, estensione con 4 nuovi case
- **CAL-06** ✅ Wave 5 non tocca `saltelli_option` (non bloccante)

---

## Discrepanze critiche → vedi `blockers.md`

1. `diritto-di-famiglia` (ID 2669) presente nel DB MVP NON in CSV cliente (la 19ª publish in extra)
2. `diritto-delle-assicurazioni` (slug reale DB) ≠ `assicurazioni` (slug CSV)
3. Tassonomia `contenzioso` (slug reale) ≠ `contenzioso-amministrativo` (slug DEC-022 firmato)
4. Tassonomia `altri` (count 2) → da deprecate dopo retag

Decisioni autonome documentate in blockers.md, non bloccanti per la wave.
