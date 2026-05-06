# Wave 5 — Blockers / Discovery diff vs DEC-021 cliente-firmato

**Audience**: orchestratore in chat (post-merge audit Wave 5).
**Funzione**: registrare le scoperte fatte durante Phase 1.5 (CAL-01 discovery) che divergono dal CSV cliente-firmato `cluster-mapping-17-areas.csv`. Tutte le decisioni qui sono prese in autonomia per non bloccare la wave; vanno riviste post-merge.

---

## DISCOVERY-01 — `diritto-di-famiglia` (ID 2669) NOT IN CSV CLIENTE

**Fatto**: nel DB MVP ci sono 19 competenze publish; il CSV cliente ne nomina 21 (15 KEEP + 4 DELETE + 2 NEW). Una della 19 publish — `diritto-di-famiglia` (ID 2669, post_title "Diritto di famiglia") — NON è in CSV.

CSV ha solo `diritto-di-famiglia-lgbtq` (ID 2666). Il DB ha entrambe come CPT publish separati.

**Probabile origine**: residuo Wave 2 Content Migration. La voce LGBTQ+ è asset principale di Avv. Antonia (DEC-021 riga 05 tier1-deep), la voce non-LGBTQ è probabilmente la versione "generale" mai sostituita o affiancata.

**Decisione autonoma Wave 5**:
- KEEP `diritto-di-famiglia` (ID 2669) come CPT publish
- Cluster: `privati` (affine al sibling LGBTQ+)
- Wave 5 non lo elimina, non lo rinomina, non lo merge

**Conseguenza count finale**:
- CSV diceva 17 aree finali (15 KEEP + 2 NEW)
- DB MVP avrà 18 aree finali (15 KEEP + 1 EXTRA + 2 NEW)
- Distribuzione cluster: 15 privati, 2 imprese, 1 contenzioso-amministrativo

**Per orchestratore**: rivedere col cliente se `diritto-di-famiglia` (NO LGBTQ) va consolidato con LGBTQ+, o eliminato, o tenuto come pratica autonoma. Decisione fuori scope Wave 5.

---

## DISCOVERY-02 — `diritto-delle-assicurazioni` (ID 2676) ≠ CSV slug `assicurazioni`

**Fatto**: CSV cliente DEL01 ha `slug_proposto=assicurazioni`. DB MVP ha la stessa entità con slug `diritto-delle-assicurazioni` e post_title "Diritto delle assicurazioni".

**Decisione autonoma**: applico DELETE_410 al post con slug REALE `diritto-delle-assicurazioni` (ID 2676). È la stessa entità del CSV — naming differente. Idempotenza preservata.

**Per orchestratore**: nessuna escalation richiesta. Slug DB era più formale ("diritto delle X"), CSV cliente più colloquiale.

---

## DISCOVERY-03 — `diritto-commerciale` (CSV DEL04) NON ESISTE come CPT MVP

**Fatto**: CSV DEL04 ha `slug=diritto-commerciale`. Nel DB MVP non c'è alcuna competenza con questo slug.

**Conferma CAL-02**: solo questa delle 4 PENDING DELETE conferma il pattern "non in DB MVP" suggerito da CAL-02. Le altre 3 PENDING DELETE esistono come CPT MVP.

**Decisione autonoma**: skip silenzioso nel loop DELETE (idempotente). Annoto in `delete-log.txt` come "skip — non in CPT MVP".

**Redirect 301**: il file `legacy-redirects.php` mantiene comunque `/competenze/diritto-commerciale/ → /aree-di-pratica/` come redirect Phase 6, per backlink esterni storici al sito Elementor.

---

## DISCOVERY-04 — Tassonomia `tipo-area` ha 4 termini, slug 1 da rinominare + 1 da deprecate

**Fatto**: il DB MVP ha 4 termini in `tipo-area`:

| term_id | name | slug | count | Decisione Wave 5 |
|---|---|---|---|---|
| 992 | Per i Privati | `privati` | 9 | ✅ KEEP |
| 993 | Per le Imprese | `imprese` | 4 | ✅ KEEP |
| 994 | Contenzioso Amministrativo | `contenzioso` | 4 | 🔄 RINOMINA slug → `contenzioso-amministrativo` (DEC-022) |
| 995 | Altri servizi | `altri` | 2 | 🗑 DEPRECATE (dopo retag count→0) |

**Discrepanza vs prompt v1.1 Phase 2**: il prompt assume "creare 3° termine `contenzioso-amministrativo`". In realtà il termine già esiste come `contenzioso` (slug breve). Soluzione: rinomina slug, no create.

**Decisione autonoma**:
1. Rinomina `contenzioso` → `contenzioso-amministrativo` (slug update via `wp term update`)
2. Riassegna i cluster di tutte le 18 aree finali via `wp post term set` (Phase 3.6 con slug REALI)
3. Quando `altri` ha count=0, eliminare termine

**Per orchestratore**: nessuna escalation. Pattern coerente con DEC-022.

---

## DISCOVERY-05 — Casi 10 vs CSV "9"

**Fatto**: il CSV pre-flight (B5.4) menziona "9 casi rappresentativi". Il DB MVP ne ha 10 publish.

**Decisione autonoma**: B5.4 dice "tutti i casi vanno go-public Wave 5" — applico a tutti e 10. Non bloccante.

**Per orchestratore**: nessuna escalation.

---

## DISCOVERY-06 — Pages duplicate `prenota-appuntamento` vs `prenota-un-appuntamento`

**Fatto**: due pages publish:
- ID 361 `prenota-un-appuntamento` (legacy Elementor)
- ID 2711 `prenota-appuntamento` (MVP nuova)

**Decisione autonoma Wave 5**: nessuna delle due viene toccata. Out of scope.

**Per orchestratore**: valutare deprecazione ID 361 (legacy) post-Wave 5.

---

## DISCOVERY-07 — Page ID 321 `competenze` collision con archive CPT

**Fatto**: esiste una page WP con slug `competenze` (ID 321). In Phase 3 il CPT competenza viene riconfigurato con `has_archive => 'aree-di-pratica'`. Quindi l'URL `/competenze/` non sarà più mappato dal CPT archive.

**Decisione autonoma Wave 5**: la page `/competenze/` (ID 321) resta accessibile come page WP. Phase 6 redirect 301 da `/competenze/` → `/aree-di-pratica/` la copre per pulizia URL post-cut produzione, ma la pagina rimane in DB.

**Per orchestratore**: valutare se eliminarla o consolidarla con la nuova page hub `/aree-di-pratica/`. Out of scope Wave 5.

---

## Riepilogo decisioni autonome

| Disc. | Tipo | Decisione | Bloccante? |
|---|---|---|---|
| DISCOVERY-01 | Extra `diritto-di-famiglia` | KEEP cluster privati | No |
| DISCOVERY-02 | Slug DEL01 differente | DELETE su slug reale DB | No |
| DISCOVERY-03 | DEL04 non esiste | Skip idempotent | No |
| DISCOVERY-04 | Term `contenzioso` slug | RINOMINA + deprecate altri | No |
| DISCOVERY-05 | Casi 10 vs 9 | Tutti go-public B5.4 | No |
| DISCOVERY-06 | Pages prenota duplicate | Non toccare | No |
| DISCOVERY-07 | Page `/competenze/` collision | Resta in DB, Phase 6 redirect | No |

Totale aree finali post-Wave5 (atteso): **18 aree** (15 privati + 2 imprese + 1 contenzioso-amministrativo) invece di 17 CSV cliente.

Wait — re-conto:
- 15 KEEP CSV (slug reali)
- + 1 EXTRA (`diritto-di-famiglia`) → +1 privati
- + 2 NEW (`infortunistica-stradale`, `aste-immobiliari`) → +2 privati
- − 0 DEL non in DB
- = 18 totali finali

Distribuzione: privati = 12 (CSV) + 1 (extra) + 2 (new) = 15, imprese = 2, contenzioso = 1. Totale 18.

Tutte le decisioni sono reversibili (nessun delete distruttivo non documentato). Backup pre-Wave5 disponibile in `~/backups/`.
