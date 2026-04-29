# Prompt — Content Migration Agent (Step D — Migrazione contenuti reali)

> **Per Claude Code in nuova sessione.** Apri `saltelli-wp/`, leggi questo file, eseguilo. Lavoro previsto: 2-4 ore.
> **PRECEDENZA:** Impeccable Agent (Step C) deve essere completato. v0.4.0-beta-impeccable o successiva.

---

## Tu sei

Il **Content Migration Agent**. Il tuo lavoro è arricchire i CPT del tema custom Saltelli (popolati con stub dall'orchestrator) con i **contenuti reali** estratti dal DB importato dal sito originale del cliente.

Il sito originale Saltelli aveva:
- 31 pagine WordPress (di cui ~19 erano le pagine "pratica" — corrispondenti alle tue 19 competenze)
- 326 articoli blog su 10 categorie
- 4-5 profili avvocato (in pagine standard, non CPT)
- Plugin Yoast SEO con meta description e OG image per ogni pagina
- Plugin Elementor che racchiudeva il content in `_elementor_data` post meta

**Il tuo lavoro è mappare → estrarre → arricchire i CPT esistenti.**

---

## Letture obbligatorie

1. `CLAUDE.md` — hard constraints
2. `.claude/knowledge/project-context.json` — `team_members`, `practice_areas`, `current_site` (categorie blog + plugin attivi originali)
3. `BRIEF_Saltelli_WordPress.md` — sezione "Strategic content decision" tier-1
4. Schema attuale dei CPT: `wp-content/themes/saltelli/inc/cpt-avvocato.php`, `cpt-competenza.php`
5. Field group ACF: `inc/acf-json/group_avvocato.json`, `group_competenza.json`

---

## Hard rules

| Rule | Reason |
|---|---|
| MAI eliminare pagine originali del cliente — sono solo source per migrazione | Audit trail, rollback safety |
| MAI committare DB dump nuovi (gitignored) | Non rompere repo |
| MAI inventare bio o casi vinti che non sono nel DB originale | Fidelity al cliente |
| Strip Elementor markup ma preserva semantica testuale (paragrafi, h2, h3, list) | Preservare lavoro originale |
| Tier-1 ottiene contenuti DEEP, tier-2 minimal | Strategy locked |
| `answer_capsule` 40-60 parole — se manca nel source, GENERALA tu basandoti sul body | Critical per GEO |
| FAQ schema solo se ≥ 3 FAQ vere disponibili — meglio nessuna che fake | Quality over quantity |
| Mai sovrascrivere campi ACF già popolati senza diff visibile | Idempotency |

---

## Task 1 — Recon contenuti originali nel DB (20 min)

Esplora cosa c'è nel DB importato dal cliente:

```bash
# Lista pagine originali (post_type=page, ancora pubblicate)
docker compose run --rm wpcli post list --post_type=page --post_status=publish \
    --fields=ID,post_title,post_name --format=csv 2>&1 | head -40

# Lista post originali con loro categoria
docker compose run --rm wpcli post list --post_type=post --post_status=publish \
    --fields=ID,post_title,post_date --format=csv --posts_per_page=10 2>&1

# Lista categorie blog
docker compose run --rm wpcli term list category --fields=name,count,slug --format=csv 2>&1

# Sample del content di una pagina pratica (es. "diritto-tributario" se esiste come slug)
docker compose run --rm wpcli post list --post_type=page --name=diritto-tributario --field=ID 2>&1
# se trovi un ID: docker compose run --rm wpcli post get $ID --field=post_content
```

**Output di questo task:** scrivi `.claude/knowledge/design/sessione-1/reports/content-recon.md` con:
- Lista 31 pagine + slug + ID
- Mapping pagine originali ↔ tuoi 19 CPT competenza
- Mapping autori blog ↔ tuoi 4 CPT avvocato (cerca `display_name` in `wp_users`)
- Categorie blog con count
- Sample del content tipico (1 pagina pratica + 1 articolo blog) — vedi quanto Elementor markup c'è

---

## Task 2 — Mapping pagine originali → CPT competenza (15 min)

Crea un file di mapping `.claude/knowledge/design/sessione-1/reports/competenza-mapping.json`:

```json
{
  "diritto-tributario": {
    "cpt_id": 2664,
    "source_page_slug": "diritto-tributario",
    "source_page_id": 152,
    "tier": "tier1",
    "has_yoast_meta": true,
    "elementor_in_content": true
  },
  ...
}
```

Per i 19 CPT competenza, trova la pagina source corrispondente.

**Caveats:**
- Slug potrebbero differire (es. CPT è `cartelle-esattoriali-e-multe`, source è `multe-cartelle-esattoriali`) → matching fuzzy su titolo
- Se nessuna pagina source trovata, segna `"source_page_id": null` e CPT ottiene solo i contenuti generati ex-novo da te

---

## Task 3 — Estrazione + clean Elementor (30 min)

Per ciascun CPT competenza con source_page:

```bash
# Estrai post_content originale
SOURCE_ID=152
docker compose run --rm wpcli post get $SOURCE_ID --field=post_content > /tmp/source_152.html

# Strip Elementor markup
# - Rimuovi shortcode [elementor-template]
# - Rimuovi div data-elementor-* attributes
# - Preserva <h2>, <h3>, <p>, <ul>, <ol>, <li>, <strong>, <em>, <a>
```

Helper bash che ti propongo:

```bash
strip_elementor() {
    local input_file=$1
    cat "$input_file" \
        | sed -E 's/\[elementor-template[^]]*\]//g' \
        | sed -E 's/data-elementor-[a-z-]+="[^"]*"//g' \
        | python3 -c "import sys, html; from bs4 import BeautifulSoup; soup = BeautifulSoup(sys.stdin.read(), 'html.parser'); print(soup.get_text(separator='\n\n'))"
}
```

(Se BeautifulSoup non disponibile: `pip3 install beautifulsoup4 --break-system-packages`)

**Validation:** dopo lo strip, conta parole. Se < 100 parole è un page mock o errore — segnala invece di committare contenuto fake.

---

## Task 4 — Generazione `answer_capsule` per ogni CPT (30 min)

Per ogni CPT competenza, genera `answer_capsule` di 40-60 parole basandosi sul source_content. Pattern:

```
Lo Studio Legale Saltelli & Partners offre consulenza in [area] a [Napoli/clienti italiani]. Ci occupiamo di [3-5 sotto-aree specifiche dal source]. [Tier-1 only: La nostra esperienza si concentra in particolare su X e Y, con casi gestiti dai nostri [N avvocato/i]]. Contatta lo studio per un primo incontro di valutazione.
```

**Tier-1 (3 aree):** answer_capsule + body extended (1500+ parole) + 5 FAQ + casi rappresentativi
**Tier-2 (16 aree):** answer_capsule + body 400-600 parole + 3 FAQ light

Aggiorna i CPT via WP-CLI:

```bash
docker compose run --rm wpcli post meta update <CPT_ID> answer_capsule "$(cat /tmp/capsule_xyz.txt)"
docker compose run --rm wpcli post meta update <CPT_ID> lead_breve "$(cat /tmp/lead_xyz.txt)"
docker compose run --rm wpcli post update <CPT_ID> --post_content="$(cat /tmp/body_xyz.html)"
```

---

## Task 5 — Migrazione 4 profili avvocato (30 min)

Cerca le pagine originali con `display_name` autori blog OR slug come `emiliano-saltelli`:

```bash
# Esempi di pagine source possibili
docker compose run --rm wpcli post list --post_type=page \
    --search="Saltelli" --field=ID 2>&1
docker compose run --rm wpcli post list --post_type=page \
    --search="Battista" --field=ID 2>&1
docker compose run --rm wpcli post list --post_type=page \
    --search="Tedesco" --field=ID 2>&1
```

Per ciascun avvocato, popola via WP-CLI i campi ACF:
- `bio_breve` (300 char)
- `bio_estesa` (wysiwyg, dal source page strip-Elementor)
- `ruolo_breve` ("Founding Partner · Tributarista" già stub, raffina)
- `specializzazioni` (array di stringhe)
- `formazione` (repeater anno/titolo/istituzione — solo se source ha info)
- `email_pubblica`, `telefono_pubblico` (solo per Emiliano fondatore; per altri lascia fallback studio)

**Foto:** se `wp-content/uploads/avv-{slug}.jpg` non esiste, lascia placeholder. NON inventare paths.

---

## Task 6 — Mapping autori blog → CPT avvocato (15 min)

Per i 326 articoli blog, l'autore originale potrebbe essere uno dei 4 avvocati. Crea mapping:

```bash
docker compose run --rm wpcli user list --fields=ID,display_name,user_login --format=csv

# Per ogni avvocato CPT, trova il WP user corrispondente per nome
# Memorizza in un meta sui post avvocato: _wp_author_id

docker compose run --rm wpcli post meta update <CPT_ID_EMILIANO> _wp_author_id <USER_ID>
```

Questo permette al template `single.php` blog di linkare l'autore al profilo CPT.

---

## Task 7 — Verifica visuale (15 min)

Dopo aver popolato:

```bash
# Verifica via curl che il content reale appaia
curl -s http://localhost:8080/competenze/diritto-tributario/ | grep -oE "<h1[^>]*>[^<]*" | head -1
curl -s http://localhost:8080/competenze/diritto-tributario/ | grep -oE "answer-capsule[^<]*<p[^>]*>[^<]*" | head -1
curl -s http://localhost:8080/avvocati/emiliano-saltelli/ | grep -oE "<p>[^<]+" | head -3
```

Validation: il sito deve servire copy reale (non più "Lo Studio si occupa di X. Per una consulenza...").

Suggerisci a Duccio di aprire `http://localhost:8080/competenze/diritto-tributario/` e `/avvocati/emiliano-saltelli/` nel browser per ispezione visiva. Lui ti dirà se ci sono problemi.

---

## Task 8 — Bump version + cache flush

```bash
# 0.4.0-beta-impeccable → 0.5.0-beta-content
sed -i.bak 's/Version: 0.4.0-beta-impeccable/Version: 0.5.0-beta-content/' wp-content/themes/saltelli/style.css
sed -i.bak "s/define('SALTELLI_THEME_VERSION', '0.4.0-beta-impeccable')/define('SALTELLI_THEME_VERSION', '0.5.0-beta-content')/" wp-content/themes/saltelli/functions.php
rm -f wp-content/themes/saltelli/{style.css,functions.php}.bak

docker compose run --rm wpcli cache flush
docker compose run --rm wpcli transient delete --all
```

**Nota:** il DB è cambiato (CPT ora hanno meta), quindi prima del prossimo commit fai un dump fresco del DB e salvalo in `db-dump/` (gitignored — ma resta sul filesystem locale per Step F deploy).

```bash
mkdir -p db-dump
docker exec saltelli-db mysqldump -u saltelli -psaltelli_dev saltelli_wp \
    > db-dump/saltelli_post-content-migration_$(date +%Y%m%d-%H%M%S).sql
```

---

## Report finale

Scrivi report in `.claude/knowledge/design/sessione-1/reports/content-migration/REPORT.md`:

1. ✅/❌ ciascuno degli 8 task
2. Numero CPT competenza popolati con content reale: **X/19**
3. Numero CPT avvocato popolati con bio reale: **X/4**
4. Mapping autori blog: **X/326 post associati a CPT avvocato**
5. Lista CPT che NON hanno trovato source page (e ne hanno solo content generato)
6. Eventuali Elementor data inestricabile — pagine che richiedono review manuale Elena
7. Esempio prima/dopo di un CPT (snippet copy)
8. DB dump filename per Step F

Poi **fermati**. Non procedere a Step E.

---

*v1.0 — Step D post-impeccable v0.4.0*
