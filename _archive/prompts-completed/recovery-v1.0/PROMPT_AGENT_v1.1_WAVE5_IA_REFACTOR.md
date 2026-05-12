# PROMPT v1.1.0 WAVE 5 — IA Refactor (sequenziale single agent)

> **Per Claude Code in nuova sessione (single agent).** Tempo: ~3-4h.
> **PRECEDENZA**: Wave 0+1+2+3 + Debug QA completati. Theme `1.0.0-recovery-wave3-debug` su `staging.studiolegalesaltelli.it` · 21/21 PASS · ACF popolato · cliente CMS-autonomous · acceptance test editoriale Elena/Ludovica in corso.
> **MISSIONE**: portare l'Information Architecture del MVP dall'attuale schema flat top-level alla **sitemap firmata audit Apr 2026** — 5 sezioni primarie (Chi Siamo · Aree di Pratica · Risorse · Costi e Consulenze · Contatti) con cluster Privati/Imprese/Contenzioso amministrativo. Bump finale → `1.1.0-ia-refactor`.

---

## 🎯 Tu sei

L'**Agente IA Refactor**. Wave 4 è in stand-by per ordine DEC-020 (pipeline 5→6→4→7). Adesso devi:

1. **Aggiungere il 3° termine tassonomia** `tipo-area` "contenzioso-amministrativo" + ri-tassonomizzare le 19 (o 16, post riconciliazione cliente) competenze
2. **Modificare `register_post_type` rewrite slug** di `competenza` (slug dinamico per cluster), `avvocato` (`/chi-siamo/team/`), `saltelli_caso` (da private → public con `/chi-siamo/risultati/`)
3. **Creare 4 nuove pagine madre WP** (Chi Siamo hub, Aree di Pratica hub, Risorse hub, Costi e Consulenze hub) + 4 nuovi `template-parts/page-*-hub.php`
4. **Spostare le pagine top-level** sotto le sezioni hub via `parent_id` update
5. **Estendere `inc/seo/legacy-redirects.php`** con redirect 301 da URL MVP corrente verso URL audit-aligned (mappa unificata 3 stati: legacy → audit / mvp → audit / legacy → mvp passthrough)
6. **Aggiornare menu navigation primario** + footer menu con nuova gerarchia
7. **Yoast sitemap rebuild + flush rewrite rules**
8. **Smoke test 21 URL audit-aligned** + smoke test redirect 301 su 11 URL MVP legacy
9. **Bump 1.1.0-ia-refactor** + report finale

```
WAVE 5 — 8 PHASES sequenziali

Phase 1 (~30 min): Backup pre-Wave 5 + branch dedicato + lettura state
Phase 2 (~20 min): Tassonomia tipo-area — 3° termine + retag competenze
Phase 3 (~30 min): register_post_type rewrite — competenza, avvocato, saltelli_caso
Phase 4 (~40 min): Nuove pagine madre + 4 template-parts hub
Phase 5 (~30 min): Page hierarchy moves (parent_id) + slug rinominazioni
Phase 6 (~30 min): Estensione legacy-redirects.php (mappa unificata 3 stati)
Phase 7 (~20 min): Menu navigation primario + footer
Phase 8 (~30 min): Flush rewrite + Yoast sitemap rebuild + smoke test 21+11 URL + bump 1.1.0 + report
```

---

## 📚 Letture obbligatorie

```
CLAUDE.md                                                       (hard constraints + design tokens + workflow rules)
docs/BRIEF.md                                                   (4 avvocati identificati + 19 aree pratica)
docs/ARCHITECTURE.md                                            (mappa CPT + ACF + schema esistente)
docs/EDITOR-HANDOFF.md                                          (cosa Elena/Ludovica si aspetta — non rompere flussi editoriali)

wp-content/themes/saltelli/
  ├── inc/cpt-avvocato.php                   (TARGET PHASE 3 — register_post_type rewrite)
  ├── inc/cpt-competenza.php                 (TARGET PHASE 3 — register_post_type rewrite + filter post_type_link)
  ├── inc/cpt-recovery.php                   (TARGET PHASE 3 — saltelli_caso da private a public)
  ├── inc/seo/legacy-redirects.php           (TARGET PHASE 6 — estensione)
  ├── inc/acf-fields.php                     (READ — pattern location rule page_slug ==)
  ├── template-parts/                        (TARGET PHASE 4 — 4 nuovi *-hub.php)
  ├── header.php / footer.php                (TARGET PHASE 7 — menu navigation)
  ├── functions.php                          (TARGET PHASE 8 — bump SALTELLI_THEME_VERSION)
  └── style.css                              (TARGET PHASE 8 — bump Version)

.claude/knowledge/recovery/PROJECT_STATE.md  (stato corrente + cosa è già stato fatto Wave 0-3)

DELIVERABLE EXTERNAL (orchestratore chat ha prodotto):
  saltelli-refactor/02-architettura/sitemap-blueprint-v2.md
  saltelli-refactor/02-architettura/taxonomy-cpt-plan-v2.md
  saltelli-refactor/01-discovery/migration-matrix-v2.csv
```

---

## 🔒 Hard rules

| Rule | Decisione |
|---|---|
| **Frontend visivo INVARIATO post-Wave 5** (stessa identica resa visiva, solo URL diversi) | Test critical |
| **NESSUNA modifica `tokens.css`** (design tokens locked) | Locked |
| **NESSUNA modifica al rendering single-* template body** (single-avvocato.php, single-competenza.php) — solo template-parts hub nuovi | Out of scope — Wave 6 farà extension blocchi |
| **NESSUNA modifica copy editoriale**, ACF fields esistenti, CPT items esistenti | Out of scope |
| **NESSUNA modifica al DS originale `_history/design/sessione-1/` o `sessione-2/`** | Read-only |
| **NON rinominare il `post_type` name** (`competenza`, `avvocato`, `saltelli_caso`) — solo `rewrite['slug']` | DB consistency |
| **NON aggiungere plugin** (multilingua, redirect manager, ecc.) — fix manuale via PHP | Lesson learned: dipendenze plugin = debt |
| **NON aggiornare WordPress core o plugin esistenti** durante run | Stabilità |
| **Backup pre-Wave 5** obbligatorio (theme tar.gz + DB dump) prima di toccare CPT registration | Rollback safety critical (CPT change rompe URL se mal gestito) |
| **Smoke test 21 URL audit-aligned + 11 URL redirect** dopo OGNI Phase su staging | Catch regression early |
| **Commit incrementale 1 per Phase** (8 commit + 1 finale bump) | Audit trail |
| **Path droplet**: `/var/www/saltelli/` — NO `/htdocs` | Lesson learned |
| **Idempotency**: re-run di una phase non duplica termini, pagine, redirect | Safety |
| **ACF Free constraint**: niente Repeater, niente Flexible Content (Pro-only) — solo field flat o relationship | Stack constraint DEC-013 |
| **Yoast coabitation enforced** — verificare sitemap.xml emessa correttamente post-rewrite | Schema integrity |
| **One-writer-at-a-time HARD RULE**: durante Wave 5 NESSUN commit dall'orchestratore in chat. Acceptance test editoriale Elena/Ludovica resta in ascolto, eventuali bug critici annotati ma non committati | Workflow |
| **Branch isolato**: `feat/wave5-ia-refactor` parte da `main` aggiornato | Coordinazione |
| **Decisioni cliente bloccanti CHIUSE prima del lancio**: vedere § "Pre-flight checklist" | Pre-requisite |

---

## ✈️ Pre-flight checklist (status post sere 2026-05-06)

Decisioni cliente che il prompt assume risolte:

- [x] **Riconciliazione 19 vs 16 aree** → ✅ CHIUSA (DEC-021): **17 aree finali** = 15 originali BRIEF (− 4 DELETE_410) + 2 nuove. NO MERGE: ogni area resta autonoma. Le 4 DELETE = Assicurazioni, Responsabilità civile, Consulenze online, Diritto commerciale (eliminate via `wp post delete --force` + redirect 301 a `/aree-di-pratica/` archive in Phase 6). Le 2 NUOVE = Infortunistica stradale + Aste immobiliari (create via `wp post create` in Phase 3.6.c).
- [x] **Cluster mapping** delle 17 competenze → ✅ CHIUSA (DEC-021): mappatura completa in `01-discovery/cluster-mapping-17-areas.csv`. Distribuzione: 14 privati / 2 imprese / 1 contenzioso-amministrativo. Phase 3.6.a/b/c contiene loop WP-CLI pre-compilati.
- [x] **Slug Italian SEO-friendly** → ✅ CHIUSA (DEC-022): cluster URL segments brevi (`privati`, `imprese`, `contenzioso-amministrativo`, NON `per-i-privati` etc). Pattern: `/aree-di-pratica/{cluster_slug}/{competenza_slug}/`.
- [x] **MERGE Risarcimento Danni** → ✅ CHIUSA: cliente RIFIUTA il merge proposto dall'audit. Resp. medica, Infortunistica stradale, Risarcimento Danni restano 3 aree autonome separate, tutte cluster privati.
- [x] **Casi rappresentativi pubblici** → ✅ CHIUSA (B5.4): tutti 9 `saltelli_caso` esistenti vanno go-public in Wave 5 — privacy compliance già verificata cliente. Phase 3.3 imposta `public => true` + URL `/chi-siamo/risultati/{slug}/`.
- [x] **Slug pubblico Famiglia LGBTQ+** → ✅ già nel MVP come `diritto-di-famiglia-lgbtq` (esplicito, SEO-positive per query target Avv. Antonia Battista).
- [x] **B5.5 — Redirect blog 326 posts** → ✅ ASSUNTA OK: pattern 301 globale `/blog/{slug}/` → `/risorse/blog/{slug}/` accettato come default proposto. Implementato in Phase 5 + 6.
- [ ] **Acceptance test Elena/Ludovica pause** → ⏳ NON BLOCCANTE: durante Wave 5 (one-writer-at-a-time) Elena/Ludovica restano in ascolto e annotano bug nel `.claude/knowledge/audits/wave5/elena-bugs.md`, ma non committano editoriale fino al merge Wave 5. Se trovano bug critici, escalation all'orchestratore via chat — Claude Code non interrompe la wave salvo direttiva esplicita.
- [ ] **Multilingua** → ⏳ NON BLOCCANTE per Wave 5: solo IT confermato per il go-live. Niente `theme.json` modifiche, niente WPML/Polylang setup. Decisione futura su EN per `/immigrazione/` resta aperta ma non impatta Wave 5.

**STATO**: ✅ TUTTI I BLOCCANTI CHIUSI. Wave 5 è launch-ready.

I 2 ⏳ rimanenti sono operativi/futuri, non strutturali. Il prompt procede.

---

## 📋 PHASE 1 — Backup pre-Wave 5 + branch dedicato (~30 min)

### 1.1 — Backup theme + DB dump locale

```bash
# Snapshot theme + assets
cd /var/www/saltelli  # o path locale equivalente
tar -czf ~/backups/saltelli-pre-wave5-$(date +%F-%H%M).tar.gz wp-content/themes/saltelli/

# DB dump locale
wp db export ~/backups/saltelli-pre-wave5-$(date +%F-%H%M).sql

# Verifica file size
ls -lh ~/backups/saltelli-pre-wave5-*
```

### 1.2 — Crea branch dedicato + parti da main aggiornato

```bash
cd /home/duccio/saltelli-wp  # repo locale
git fetch origin
git checkout main
git pull --rebase origin main
git checkout -b feat/wave5-ia-refactor

# Verifica versione corrente
grep SALTELLI_THEME_VERSION wp-content/themes/saltelli/functions.php
# Atteso: 1.0.0-recovery-wave3-debug
```

### 1.3 — Lettura state corrente

```bash
# CPT attivi
wp post-type list --format=table

# Tassonomie attive
wp taxonomy list --format=table

# Termini tipo-area corrente
wp term list tipo-area --format=table

# Pagine WP custom attive
wp post list --post_type=page --posts_per_page=20 --format=table --fields=ID,post_name,post_title,post_parent
```

**Annota gli ID delle pagine** (servono in Phase 5).

### 1.4 — Phase 1 commit

```bash
# Niente cambi codice ancora — solo branch checkout
git status
# Atteso: clean working tree on feat/wave5-ia-refactor

# Eventuali file di stato/note
mkdir -p .claude/knowledge/audits/wave5-ia-refactor/
echo "# Wave 5 IA Refactor — Audit log" > .claude/knowledge/audits/wave5-ia-refactor/00-pre-state.md
# (Compilare con state attuale: CPT count, term count, page count)

git add .claude/knowledge/audits/wave5-ia-refactor/
git commit -m "wave5: phase 1 — pre-state audit logged"
```

---

## 📋 PHASE 2 — Tassonomia tipo-area: 3° termine + retag (~20 min)

### 2.1 — Aggiunta 3° termine "contenzioso-amministrativo"

```bash
wp term create tipo-area "Contenzioso amministrativo" \
  --slug="contenzioso-amministrativo" \
  --description="Aree relative a contenziosi con la pubblica amministrazione e ricorsi al TAR/Consiglio di Stato"

# Verifica
wp term list tipo-area --format=table
# Atteso: 3 termini (privati, imprese, contenzioso-amministrativo) — eventualmente "altri" da deprecate
```

### 2.2 — Retag delle 19 (o 16) competenze nei 3 cluster

**Mapping da PRE-FLIGHT cliente** — esempio (sostituire con decisione cliente reale):

```bash
# Lista le 19 competenze attuali con slug + termini correnti
wp post list --post_type=competenza --posts_per_page=20 --format=csv \
  --fields=ID,post_name,post_title,tax_input

# Retag manuale per ogni competenza (esempio per "tributario")
COMP_ID=$(wp post list --post_type=competenza --name=tributario --field=ID)
wp post term set $COMP_ID tipo-area privati  # NB: questo SOSTITUISCE i termini, non aggiunge

# Ripeti per tutte le 19/16 competenze secondo mapping cliente
# Pattern: privati cluster (~10), imprese cluster (~5), contenzioso-amministrativo (~1-2)
```

### 2.3 — (Opzionale) Deprecate termine "altri"

```bash
# Verifica se esiste e se ha post associati
wp term get tipo-area altri 2>/dev/null
wp post list --post_type=competenza --tax_query="tipo-area=altri" --format=count

# Se 0 post associati: rimuovi
wp term delete tipo-area altri

# Se >0 post: NON eliminare. Annota nell'audit log per discussione orchestratore.
```

### 2.4 — Phase 2 commit

```bash
# Niente cambi al codice — solo dati DB
# Snapshot del nuovo state
wp term list tipo-area --format=json > .claude/knowledge/audits/wave5-ia-refactor/02-taxonomy-post.json
wp post list --post_type=competenza --format=json > .claude/knowledge/audits/wave5-ia-refactor/02-competenze-post.json

git add .claude/knowledge/audits/wave5-ia-refactor/
git commit -m "wave5: phase 2 — tipo-area 3rd term + 19 competenze retagged"
```

---

## 📋 PHASE 3 — register_post_type rewrite (~30 min)

### 3.1 — Modifica `inc/cpt-avvocato.php`

```php
// CAMBIO MIRATO — solo 'rewrite' + 'has_archive'
// Linea ~XX (cerca register_post_type('avvocato', ...))

register_post_type('avvocato', [
    'labels' => [/* invariato */],
    'public' => true,
    'show_in_rest' => true,
    'has_archive' => 'chi-siamo/team',          // ✏️ era: 'avvocati' (o true con default)
    'rewrite' => [
        'slug' => 'chi-siamo/team',              // ✏️ era: 'avvocati'
        'with_front' => false,
    ],
    /* resto invariato */
]);
```

### 3.2 — Modifica `inc/cpt-competenza.php`

```php
// CAMBIO PIÙ COMPLESSO — slug dinamico per cluster
register_post_type('competenza', [
    'labels' => [/* invariato */],
    'public' => true,
    'show_in_rest' => true,
    'has_archive' => 'aree-di-pratica',          // ✏️ era: 'competenze'
    'rewrite' => [
        'slug' => 'aree-di-pratica/%tipo-area%', // ✏️ era: 'competenze'
        'with_front' => false,
    ],
    'taxonomies' => ['tipo-area'],
    /* resto invariato */
]);

// AGGIUNGI filter (dopo la registrazione del post type)
add_filter('post_type_link', function($link, $post) {
    if ($post->post_type !== 'competenza') return $link;
    $terms = wp_get_object_terms($post->ID, 'tipo-area');
    if (empty($terms) || is_wp_error($terms)) {
        $cluster = 'altri';  // fallback se untagged
    } else {
        $cluster = $terms[0]->slug;
    }
    return str_replace('%tipo-area%', $cluster, $link);
}, 10, 2);
```

### 3.3 — Modifica `inc/cpt-recovery.php` per `saltelli_caso`

```php
// Trova il blocco register_post_type('saltelli_caso', ...) e sostituisci:
register_post_type('saltelli_caso', [
    'labels' => [/* invariato */],
    'public' => true,                            // ✏️ era: false
    'publicly_queryable' => true,                // ✏️ era: false
    'show_in_rest' => true,                      // ✏️ era: false
    'show_ui' => true,                           // mantenere
    'has_archive' => 'chi-siamo/risultati',      // ✏️ era: false
    'rewrite' => [
        'slug' => 'chi-siamo/risultati',         // ✏️ nuovo
        'with_front' => false,
    ],
    'supports' => ['title', 'editor', 'thumbnail', 'custom-fields'],
    'menu_icon' => 'dashicons-awards',
    'capability_type' => 'post',
]);
```

### 3.4 — Verifica + smoke test parziale

```bash
# Flush rewrite rules subito dopo cambi register_post_type
wp rewrite flush

# Verifica URL generati
wp post list --post_type=avvocato --format=table --fields=ID,post_name,permalink
# Atteso: tutti gli avvocati hanno URL /chi-siamo/team/{slug}/

wp post list --post_type=competenza --format=table --fields=ID,post_name,permalink
# Atteso: tutti hanno URL /aree-di-pratica/{cluster}/{slug}/ — verificare cluster corretto

wp post list --post_type=saltelli_caso --format=table --fields=ID,post_name,post_status,permalink
# Atteso: URL /chi-siamo/risultati/{slug}/ per quelli con post_status=publish
```

### 3.5 — Tassonomia tipo-area: aggiungi 3° termine "contenzioso-amministrativo"

```bash
# Verifica termini esistenti (atteso: privati, imprese)
wp term list tipo-area --format=table

# Aggiungi NUOVO 3° termine
wp term create tipo-area "Contenzioso amministrativo" \
  --slug=contenzioso-amministrativo \
  --description="Aree di contenzioso con la pubblica amministrazione"

# Verifica (atteso: 3 termini)
wp term list tipo-area --format=table
```

### 3.6 — APPLICA MAPPATURA CLIENTE-FIRMATA (DEC-021) — 17 aree finali

**Riferimento autoritativo**: `cluster-mapping-17-areas.csv` (deliverable orchestratore).

**Regole**:
- Le 19 competenze esistenti nel DB del MVP (post Wave 2) vanno ridotte a 17:
  - 15 esistenti **mantenute** con nuovo cluster term assegnato
  - 4 esistenti **eliminate** (DELETE_410): Assicurazioni, Responsabilità civile, Consulenze online, Diritto commerciale
  - 2 **nuove create**: Infortunistica stradale, Aste immobiliari
- L'audit firmato Apr 2026 proponeva MERGE (malasanità + infortunistica + risarcimento → "Risarcimento Danni"). **Cliente RIFIUTA il merge**: ognuna resta autonoma.

**Phase 3.6.a — KEEP_assign_term per le 15 esistenti**:

```bash
# Privati: 12 aree (escludendo le 4 PENDING DELETE)
for slug in tributario cartelle-esattoriali diritto-del-lavoro diritto-di-famiglia-lgbtq responsabilita-medica bancario condominiale-immobiliare immigrazione penale previdenziale successioni risarcimento-danni; do
    POST_ID=$(wp post list --post_type=competenza --name=$slug --field=ID --posts_per_page=1)
    if [ -z "$POST_ID" ]; then
        echo "⚠️  ATTENZIONE: post '$slug' non trovato — verifica manualmente"
        continue
    fi
    wp post term set $POST_ID tipo-area privati
    echo "✅ $slug → privati (post_id=$POST_ID)"
done

# Imprese: 2 aree
for slug in recupero-crediti domiciliazione-impresa; do
    POST_ID=$(wp post list --post_type=competenza --name=$slug --field=ID --posts_per_page=1)
    if [ -z "$POST_ID" ]; then
        echo "⚠️  ATTENZIONE: post '$slug' non trovato — verifica manualmente"
        continue
    fi
    wp post term set $POST_ID tipo-area imprese
    echo "✅ $slug → imprese (post_id=$POST_ID)"
done

# Contenzioso amministrativo: 1 area
POST_ID=$(wp post list --post_type=competenza --name=diritto-amministrativo --field=ID --posts_per_page=1)
wp post term set $POST_ID tipo-area contenzioso-amministrativo
echo "✅ diritto-amministrativo → contenzioso-amministrativo (post_id=$POST_ID)"
```

**Phase 3.6.b — DELETE le 4 PENDING (Assicurazioni, Resp. civile, Consulenze online, Diritto commerciale)**:

```bash
# IMPORTANTE: --force evita trash, rimuove definitivamente. Backup pre-Wave5 contiene già queste righe per safety rollback.
for slug in assicurazioni responsabilita-civile consulenze-online diritto-commerciale; do
    POST_ID=$(wp post list --post_type=competenza --name=$slug --field=ID --posts_per_page=1)
    if [ -z "$POST_ID" ]; then
        echo "⚠️  $slug: già non presente nel DB (skip)"
        continue
    fi
    # Capture URL pre-delete per il redirect 301 successivo
    OLD_URL=$(wp post get $POST_ID --field=url)
    echo "🗑  Eliminando $slug (post_id=$POST_ID, old_url=$OLD_URL)"
    wp post delete $POST_ID --force
done
```

**Phase 3.6.c — CREATE le 2 NUOVE (Infortunistica stradale, Aste immobiliari)**:

```bash
# Infortunistica stradale
INFORTUNISTICA_ID=$(wp post create \
    --post_type=competenza \
    --post_title="Infortunistica stradale" \
    --post_name="infortunistica-stradale" \
    --post_status=publish \
    --post_content="<!-- Body placeholder — Elena fornisce contenuto editoriale post-Wave 5 -->" \
    --porcelain)
wp post term set $INFORTUNISTICA_ID tipo-area privati
echo "✅ NUOVA: infortunistica-stradale → privati (post_id=$INFORTUNISTICA_ID)"

# Aste immobiliari
ASTE_ID=$(wp post create \
    --post_type=competenza \
    --post_title="Aste immobiliari" \
    --post_name="aste-immobiliari" \
    --post_status=publish \
    --post_content="<!-- Body placeholder — Elena fornisce contenuto editoriale post-Wave 5 -->" \
    --porcelain)
wp post term set $ASTE_ID tipo-area privati
echo "✅ NUOVA: aste-immobiliari → privati (post_id=$ASTE_ID)"
```

**Phase 3.6.d — Verifica finale: 17 competenze totali con cluster assegnato**:

```bash
# Atteso: 17 righe (15 + 2 nuove, dopo DELETE delle 4)
wp post list --post_type=competenza --post_status=publish --format=table --fields=ID,post_name | tee /tmp/wave5-competenze-final.txt

# Atteso: ognuna ha 1 termine tipo-area assegnato (escluso "altri" come fallback)
wp term list tipo-area --format=table  # 3 termini
for slug in tributario cartelle-esattoriali recupero-crediti diritto-del-lavoro diritto-di-famiglia-lgbtq responsabilita-medica bancario condominiale-immobiliare immigrazione penale previdenziale successioni risarcimento-danni domiciliazione-impresa diritto-amministrativo infortunistica-stradale aste-immobiliari; do
    POST_ID=$(wp post list --post_type=competenza --name=$slug --field=ID --posts_per_page=1)
    TERMS=$(wp post term list $POST_ID tipo-area --field=slug | tr '\n' ',' | sed 's/,$//')
    echo "$slug → cluster: $TERMS"
done
```

Atteso output:
```
tributario → cluster: privati
cartelle-esattoriali → cluster: privati
recupero-crediti → cluster: imprese
diritto-del-lavoro → cluster: privati
diritto-di-famiglia-lgbtq → cluster: privati
responsabilita-medica → cluster: privati
bancario → cluster: privati
condominiale-immobiliare → cluster: privati
immigrazione → cluster: privati
penale → cluster: privati
previdenziale → cluster: privati
successioni → cluster: privati
risarcimento-danni → cluster: privati
domiciliazione-impresa → cluster: imprese
diritto-amministrativo → cluster: contenzioso-amministrativo
infortunistica-stradale → cluster: privati
aste-immobiliari → cluster: privati
```

**Distribuzione finale verificata**:
- Privati: 14 aree
- Imprese: 2 aree
- Contenzioso-amministrativo: 1 area
- **Totale**: 17 aree ✅

### 3.7 — Phase 3 commit

```bash
git add wp-content/themes/saltelli/inc/cpt-avvocato.php
git add wp-content/themes/saltelli/inc/cpt-competenza.php
git add wp-content/themes/saltelli/inc/cpt-recovery.php
git commit -m "wave5: phase 3 — register_post_type rewrite (avvocato, competenza, saltelli_caso) + tassonomia + cluster mapping 17 aree"
```

---

## 📋 PHASE 4 — Nuove pagine madre + 4 template-parts hub (~40 min)

### 4.1 — Crea le 4 pagine madre WP

```bash
# Pagina madre Chi Siamo (slug "chi-siamo-hub" temporaneo per evitare clash con esistente "chi-siamo")
CHISIAMO_HUB_ID=$(wp post create \
  --post_type=page \
  --post_title="Chi Siamo" \
  --post_name="chi-siamo-hub" \
  --post_status=publish \
  --porcelain)
echo "Chi Siamo Hub ID: $CHISIAMO_HUB_ID"

# Pagina madre Aree di Pratica
AREE_HUB_ID=$(wp post create \
  --post_type=page \
  --post_title="Aree di Pratica" \
  --post_name="aree-di-pratica" \
  --post_status=publish \
  --porcelain)

# Pagina madre Risorse
RISORSE_HUB_ID=$(wp post create \
  --post_type=page \
  --post_title="Risorse" \
  --post_name="risorse" \
  --post_status=publish \
  --porcelain)

# Pagina madre Costi e Consulenze
COSTI_HUB_ID=$(wp post create \
  --post_type=page \
  --post_title="Costi e Consulenze" \
  --post_name="costi-e-consulenze" \
  --post_status=publish \
  --porcelain)

# Salva gli ID per Phase 5
echo "$CHISIAMO_HUB_ID,$AREE_HUB_ID,$RISORSE_HUB_ID,$COSTI_HUB_ID" \
  > .claude/knowledge/audits/wave5-ia-refactor/04-hub-pages.csv
```

### 4.2 — Crea 4 nuovi template-parts hub

```bash
# Pattern minimale — vedi sitemap-blueprint-v2.md § Step 9 per il dettaglio rendering
# Layout: hero + grid 3-4 child links + trust bar (placeholder) + CTA
```

**`template-parts/page-chi-siamo-hub.php`**:
```php
<?php
/**
 * Page hub: Chi Siamo
 * Renderizza: hero + grid 3 child (Lo Studio / Team / Risultati) + trust bar (placeholder Wave 6) + CTA
 */
?>
<article class="sl-page sl-page--chi-siamo-hub">
  <header class="sl-page__header">
    <p class="sl-mono"><?php echo esc_html(saltelli_option('eyebrow_chi_siamo', 'STUDIO LEGALE · NAPOLI · CHIAIA · DAL 1999')); ?></p>
    <h1 class="sl-page__title">Chi siamo</h1>
    <p class="sl-page__lede"><?php the_field('lede') ?: 'Quattro avvocati a Chiaia, diciannove aree di pratica, vent\'anni accanto a famiglie e imprese di Napoli.'; ?></p>
  </header>

  <section class="sl-hub-grid">
    <a href="/chi-siamo/lo-studio/" class="sl-hub-card">
      <p class="sl-mono">01 / 03</p>
      <h2>Lo Studio</h2>
      <p>Storia, valori, sede in Via Vannella Gaetani 27.</p>
    </a>
    <a href="/chi-siamo/team/" class="sl-hub-card">
      <p class="sl-mono">02 / 03</p>
      <h2>Il Team</h2>
      <p>Quattro avvocati, ognuno con specializzazione consolidata.</p>
    </a>
    <a href="/chi-siamo/risultati/" class="sl-hub-card">
      <p class="sl-mono">03 / 03</p>
      <h2>Risultati</h2>
      <p>Casi rappresentativi vinti per i nostri clienti.</p>
    </a>
  </section>

  <!-- Wave 6: trust-bar globale qui -->
  <!-- Wave 6: CTA progressive qui -->
</article>
```

**`template-parts/page-aree-di-pratica-hub.php`**: simile, grid 3 cluster con conteggio competenze.

**`template-parts/page-risorse-hub.php`**: simile, grid 4 child (Blog / FAQ / Glossario / Guide).

**`template-parts/page-costi-e-consulenze-hub.php`**: simile, grid 3 child (Prima Consulenza / Come Lavoriamo / Richiedi Preventivo).

### 4.3 — Aggiungi route in `page.php` router

```php
// Aggiungi al $template_map array in page.php (linea ~XX):
$template_map = [
    'costi'    => 'template-parts/page-costi.php',
    'casi'     => 'template-parts/page-casi.php',
    'contatti' => 'template-parts/page-contatti.php',
    'faq'      => 'template-parts/page-faq.php',
    'chi-siamo' => 'template-parts/page-chi-siamo.php',
    // NUOVI Wave 5:
    'chi-siamo-hub' => 'template-parts/page-chi-siamo-hub.php',
    'aree-di-pratica' => 'template-parts/page-aree-di-pratica-hub.php',
    'risorse' => 'template-parts/page-risorse-hub.php',
    'costi-e-consulenze' => 'template-parts/page-costi-e-consulenze-hub.php',
    // resto invariato
];
```

### 4.4 — Phase 4 commit

```bash
git add wp-content/themes/saltelli/template-parts/page-chi-siamo-hub.php
git add wp-content/themes/saltelli/template-parts/page-aree-di-pratica-hub.php
git add wp-content/themes/saltelli/template-parts/page-risorse-hub.php
git add wp-content/themes/saltelli/template-parts/page-costi-e-consulenze-hub.php
git add wp-content/themes/saltelli/page.php
git add .claude/knowledge/audits/wave5-ia-refactor/04-hub-pages.csv
git commit -m "wave5: phase 4 — 4 hub pages + 4 template-parts + page.php router updated"
```

---

## 📋 PHASE 5 — Page hierarchy moves + slug rinominazioni (~30 min)

### 5.1 — Rinominazione slug pagina "chi-siamo" → "lo-studio"

**Conflitto da risolvere**: la pagina hub ha slug `chi-siamo-hub` (temporaneo). La pagina figlio "Lo Studio" è quella che oggi ha post_name `chi-siamo` (URL `/lo-studio/` reso da `wp-admin/edit.php` perché il display title è "Lo Studio" ma post_name è `chi-siamo`).

**Step**:

```bash
# 1. Renomina la pagina LoStudio: post_name "chi-siamo" → "lo-studio"
LOSTUDIO_ID=$(wp post list --post_type=page --name=chi-siamo --field=ID --posts_per_page=1)
wp post update $LOSTUDIO_ID --post_name="lo-studio"

# 2. Renomina la pagina hub: post_name "chi-siamo-hub" → "chi-siamo"
CHISIAMO_HUB_ID=$(wp post list --post_type=page --name=chi-siamo-hub --field=ID --posts_per_page=1)
wp post update $CHISIAMO_HUB_ID --post_name="chi-siamo"

# Ora il routing in page.php deve essere riallineato:
# Prima: 'chi-siamo' => 'page-chi-siamo.php' (Lo Studio)
# Ora:   'lo-studio' => 'page-chi-siamo.php' (Lo Studio)
#        'chi-siamo' => 'page-chi-siamo-hub.php' (HUB)
```

### 5.2 — Aggiorna `page.php` router con nuovo mapping

```php
$template_map = [
    'costi'    => 'template-parts/page-costi.php',
    'casi'     => 'template-parts/page-casi.php',
    'contatti' => 'template-parts/page-contatti.php',
    'faq'      => 'template-parts/page-faq.php',
    // ✏️ CAMBIATO:
    'lo-studio' => 'template-parts/page-chi-siamo.php',          // nuovo slug
    'chi-siamo' => 'template-parts/page-chi-siamo-hub.php',      // nuovo hub
    'aree-di-pratica' => 'template-parts/page-aree-di-pratica-hub.php',
    'risorse' => 'template-parts/page-risorse-hub.php',
    'costi-e-consulenze' => 'template-parts/page-costi-e-consulenze-hub.php',
    // info-shared invariato:
    'come-lavoriamo' => 'template-parts/page-info-shared.php',
    'prima-consulenza' => 'template-parts/page-info-shared.php',
    'lavora-con-noi' => 'template-parts/page-info-shared.php',
    'richiedi-preventivo' => 'template-parts/page-info-shared.php',
    'guide-gratuite' => 'template-parts/page-info-shared.php',
    // domande-frequenti dopo rinominazione:
    'domande-frequenti' => 'template-parts/page-faq.php',
    // glossario-legale resta custom rendering
];
```

### 5.3 — Rinominazione slug `/faq/` → `/risorse/domande-frequenti/`

```bash
FAQ_ID=$(wp post list --post_type=page --name=faq --field=ID --posts_per_page=1)
wp post update $FAQ_ID --post_name="domande-frequenti"
```

Aggiornare `inc/acf-fields.php` location rule per `group_faq_v1`:

```php
// Era: 'page_slug == faq'
// Diventa: 'page_slug == domande-frequenti'
```

### 5.4 — Page hierarchy moves (parent_id update)

```bash
# Carica gli ID dei nuovi hub
read CHISIAMO_HUB_ID AREE_HUB_ID RISORSE_HUB_ID COSTI_HUB_ID < .claude/knowledge/audits/wave5-ia-refactor/04-hub-pages.csv

# IDs delle pagine esistenti
LOSTUDIO_ID=$(wp post list --post_type=page --name=lo-studio --field=ID --posts_per_page=1)
COSTI_ID=$(wp post list --post_type=page --name=costi --field=ID --posts_per_page=1)
COMELAVORIAMO_ID=$(wp post list --post_type=page --name=come-lavoriamo --field=ID --posts_per_page=1)
PRIMACONSULENZA_ID=$(wp post list --post_type=page --name=prima-consulenza --field=ID --posts_per_page=1)
RICHIEDIPREVENTIVO_ID=$(wp post list --post_type=page --name=richiedi-preventivo --field=ID --posts_per_page=1)
LAVORACONNOI_ID=$(wp post list --post_type=page --name=lavora-con-noi --field=ID --posts_per_page=1)
GUIDEGRATUITE_ID=$(wp post list --post_type=page --name=guide-gratuite --field=ID --posts_per_page=1)
DOMANDEFREQUENTI_ID=$(wp post list --post_type=page --name=domande-frequenti --field=ID --posts_per_page=1)
CONTATTI_ID=$(wp post list --post_type=page --name=contatti --field=ID --posts_per_page=1)

# Set parent_id per ogni page
wp post update $LOSTUDIO_ID --post_parent=$CHISIAMO_HUB_ID
wp post update $DOMANDEFREQUENTI_ID --post_parent=$RISORSE_HUB_ID
wp post update $GUIDEGRATUITE_ID --post_parent=$RISORSE_HUB_ID
wp post update $COMELAVORIAMO_ID --post_parent=$COSTI_HUB_ID
wp post update $PRIMACONSULENZA_ID --post_parent=$COSTI_HUB_ID
wp post update $RICHIEDIPREVENTIVO_ID --post_parent=$COSTI_HUB_ID
wp post update $LAVORACONNOI_ID --post_parent=$CONTATTI_ID  # sotto contatti, non sotto un hub nuovo
wp post update $COSTI_ID --post_parent=$COSTI_HUB_ID  # /costi/ → child di /costi-e-consulenze/

# Verifica gerarchia
wp post list --post_type=page --posts_per_page=20 --format=table --fields=ID,post_name,post_title,post_parent
```

### 5.5 — Glossario legale: cambio path nel custom rendering

In `inc/wave3-glossario.php` o nel rewrite rule che intercetta `/glossario-legale/`:

```php
// Cerca add_rewrite_rule( '^glossario-legale/?$', ... )
// Cambia in:
add_rewrite_rule( '^risorse/glossario-legale/?$', 'index.php?glossario_legale=1', 'top' );
```

E `template_redirect` per il rendering custom: nessuna modifica al rendering, solo il path di entrata.

### 5.6 — Phase 5 commit

```bash
git add wp-content/themes/saltelli/page.php
git add wp-content/themes/saltelli/inc/acf-fields.php
git add wp-content/themes/saltelli/inc/wave3-glossario.php
# DB changes via wp-cli — non in git ma annotati nel log:
wp post list --post_type=page --posts_per_page=20 --format=json > .claude/knowledge/audits/wave5-ia-refactor/05-pages-post-hierarchy.json
git add .claude/knowledge/audits/wave5-ia-refactor/
git commit -m "wave5: phase 5 — page hierarchy + slug rinominazioni + glossario rewrite path"
```

---

## 📋 PHASE 6 — Estensione legacy-redirects.php (~30 min)

### 6.1 — Patch `inc/seo/legacy-redirects.php`

Aggiungi un secondo array di redirect dedicato MVP→audit-aligned, gestito dallo stesso `template_redirect` action:

```php
<?php
/**
 * Legacy Redirects — Wave 5 enhanced
 * Mappa unificata 3 stati:
 *   - URL legacy Elementor (sito production attuale) → URL audit-aligned (target Wave 7 cut)
 *   - URL MVP corrente (recovery-wave3-debug) → URL audit-aligned (target Wave 5 implementa)
 */

namespace Saltelli\SEO;

// Mappa esistente legacy Elementor → MVP corrente (Wave 0-3) — INVARIATA
$legacy_to_mvp_redirects = [
    // ... regole esistenti pre-Wave 5
];

// NUOVA mappa MVP corrente → audit-aligned (Wave 5)
$mvp_to_audit_redirects = [
    '/lo-studio/'                  => '/chi-siamo/lo-studio/',
    '/avvocati/'                   => '/chi-siamo/team/',
    '/avvocati/(.*)'               => '/chi-siamo/team/$1',
    '/competenze/'                 => '/aree-di-pratica/',
    // /competenze/{slug}/ → /aree-di-pratica/{cluster}/{slug}/ è gestito automaticamente da rewrite rules WP
    // poiché register_post_type ora ha rewrite slug 'aree-di-pratica/%tipo-area%' (Phase 3)
    // MA: è bene aggiungere un redirect di fallback per i bookmark esistenti
    '/competenze/([^/]+)/?'        => '__dynamic_competenza_redirect',  // gestito da funzione custom
    '/tipo-area/privati/?'         => '/aree-di-pratica/privati/',
    '/tipo-area/imprese/?'         => '/aree-di-pratica/imprese/',
    '/casi/'                       => '/chi-siamo/risultati/',
    '/blog/'                       => '/risorse/blog/',
    '/blog/(.*)'                   => '/risorse/blog/$1',
    '/category/(.*)'               => '/risorse/blog/category/$1',
    '/tag/(.*)'                    => '/risorse/blog/tag/$1',
    '/author/(.*)'                 => '/risorse/blog/author/$1',
    '/faq/'                        => '/risorse/domande-frequenti/',
    '/glossario-legale/'           => '/risorse/glossario-legale/',
    '/guide-gratuite/'             => '/risorse/guide-gratuite/',
    '/come-lavoriamo/'             => '/costi-e-consulenze/come-lavoriamo/',
    '/prima-consulenza/'           => '/costi-e-consulenze/prima-consulenza/',
    '/richiedi-preventivo/'        => '/costi-e-consulenze/richiedi-preventivo/',
    '/lavora-con-noi/'             => '/contatti/lavora-con-noi/',
    '/costi/'                      => '/costi-e-consulenze/',
    
    // 4 aree DELETE_410 (DEC-021) — redirect a /aree-di-pratica/ archive (preservazione ~80% link equity)
    '/competenze/assicurazioni/?'         => '/aree-di-pratica/',
    '/competenze/responsabilita-civile/?' => '/aree-di-pratica/',
    '/competenze/consulenze-online/?'     => '/aree-di-pratica/',
    '/competenze/diritto-commerciale/?'   => '/aree-di-pratica/',
];

add_action('template_redirect', function() use ($mvp_to_audit_redirects) {
    $request_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
    foreach ($mvp_to_audit_redirects as $pattern => $target) {
        // Pattern statico (no regex)
        if (strpos($pattern, '(') === false) {
            if ($request_path === $pattern || $request_path === rtrim($pattern, '/')) {
                wp_redirect(home_url($target), 301);
                exit;
            }
        }
        // Pattern regex
        else {
            $regex = '#^' . $pattern . '$#';
            if (preg_match($regex, $request_path, $matches)) {
                if ($target === '__dynamic_competenza_redirect') {
                    // Caso speciale: lookup cluster da DB e redirect dinamico
                    $slug = $matches[1];
                    $post = get_page_by_path($slug, OBJECT, 'competenza');
                    if ($post) {
                        wp_redirect(get_permalink($post->ID), 301);  // Yoast/WP genera URL aggiornata
                        exit;
                    }
                } else {
                    $new_path = preg_replace($regex, $target, $request_path);
                    wp_redirect(home_url($new_path), 301);
                    exit;
                }
            }
        }
    }
}, 5);  // priority 5: prima di Yoast (priority 10)
```

### 6.2 — Verifica redirect funzionanti

```bash
# Dal locale o dal droplet, test 11 URL MVP legacy
URLS=(
  "/lo-studio/"
  "/avvocati/"
  "/avvocati/emiliano-saltelli/"
  "/competenze/"
  "/competenze/tributario/"
  "/casi/"
  "/blog/"
  "/faq/"
  "/glossario-legale/"
  "/come-lavoriamo/"
  "/prima-consulenza/"
)
for url in "${URLS[@]}"; do
  redirect_to=$(curl -s -o /dev/null -w "%{redirect_url}" -L "https://staging.studiolegalesaltelli.it${url}")
  status=$(curl -s -o /dev/null -w "%{http_code}" "https://staging.studiolegalesaltelli.it${url}")
  echo "${status} ${url} → ${redirect_to}"
done
```

Atteso: tutti **301 → URL audit-aligned**.

### 6.3 — Phase 6 commit

```bash
git add wp-content/themes/saltelli/inc/seo/legacy-redirects.php
git commit -m "wave5: phase 6 — legacy-redirects.php extended (mvp→audit-aligned mappa unificata)"
```

---

## 📋 PHASE 7 — Menu navigation + footer (~20 min)

### 7.1 — Menu primario via WP-CLI

```bash
# Pulisci menu primary esistente (backup prima)
wp menu list
PRIMARY_MENU_ID=$(wp menu list --format=ids | head -1)  # primo menu

# Backup
wp menu item list $PRIMARY_MENU_ID --format=csv > .claude/knowledge/audits/wave5-ia-refactor/07-menu-pre.csv

# Rebuild menu (semplificato — adatta nomi e URL)
# Step 1: rimuovi tutti gli item esistenti
wp menu item list $PRIMARY_MENU_ID --format=ids | xargs -I {} wp menu item delete {}

# Step 2: aggiungi le 5 sezioni primarie
wp menu item add-post $PRIMARY_MENU_ID $CHISIAMO_HUB_ID  # Chi Siamo
wp menu item add-post $PRIMARY_MENU_ID $AREE_HUB_ID  # Aree di Pratica
wp menu item add-post $PRIMARY_MENU_ID $RISORSE_HUB_ID  # Risorse
wp menu item add-post $PRIMARY_MENU_ID $COSTI_HUB_ID  # Costi e Consulenze
wp menu item add-post $PRIMARY_MENU_ID $CONTATTI_ID  # Contatti

# Step 3: sub-items Chi Siamo
LOSTUDIO_ITEM=$(wp menu item add-post $PRIMARY_MENU_ID $LOSTUDIO_ID --parent-id=$(wp menu item list $PRIMARY_MENU_ID --format=json | jq -r ".[] | select(.title==\"Chi Siamo\") | .db_id") --porcelain)
# ... (ripeti per Team, Risultati, e per gli altri hub)

# Verifica struttura finale
wp menu item list $PRIMARY_MENU_ID --format=table
```

**Alternative manuale via WP-Admin → Menu**: più rapido se preferisci. Documentare in `EDITOR-HANDOFF.md`.

### 7.2 — Footer menu (riuso + aggiunte)

Aggiungere voci legali (Privacy / Cookie / Note Legali) come placeholder — saranno popolate al cut Wave 7.

### 7.3 — Phase 7 commit

```bash
# Niente cambi codice (menu è data DB) — solo audit log
wp menu item list $PRIMARY_MENU_ID --format=json > .claude/knowledge/audits/wave5-ia-refactor/07-menu-post.json
git add .claude/knowledge/audits/wave5-ia-refactor/
git commit -m "wave5: phase 7 — primary + footer menu rebuilt with new IA"
```

---

## 📋 PHASE 8 — Flush + Yoast sitemap + smoke test + bump 1.1.0 (~30 min)

### 8.1 — Flush rewrite + Yoast sitemap rebuild

```bash
wp rewrite flush
wp yoast index --reindex 2>/dev/null || wp yoast index 2>/dev/null  # comando varia per versione
wp option update wpseo_sitemap_clear 1 2>/dev/null
wp cache flush
wp transient delete --all
```

### 8.2 — Smoke test 21 URL audit-aligned

```bash
URLS_AUDIT_ALIGNED=(
  "/"
  "/chi-siamo/"
  "/chi-siamo/lo-studio/"
  "/chi-siamo/team/"
  "/chi-siamo/team/emiliano-saltelli/"
  "/chi-siamo/team/fabiana-saltelli/"
  "/chi-siamo/team/antonia-battista/"
  "/chi-siamo/team/stefano-gaetano-tedesco/"
  "/chi-siamo/risultati/"
  "/aree-di-pratica/"
  "/aree-di-pratica/privati/"
  "/aree-di-pratica/privati/tributario/"
  "/aree-di-pratica/privati/lavoro/"
  "/aree-di-pratica/privati/famiglia-lgbtq/"
  "/aree-di-pratica/imprese/"
  "/aree-di-pratica/contenzioso-amministrativo/"
  "/risorse/"
  "/risorse/blog/"
  "/risorse/domande-frequenti/"
  "/risorse/glossario-legale/"
  "/costi-e-consulenze/"
  "/contatti/"
  "/llms.txt"
)

PASS=0
FAIL=0
for url in "${URLS_AUDIT_ALIGNED[@]}"; do
  status=$(curl -s -o /dev/null -w "%{http_code}" "https://staging.studiolegalesaltelli.it${url}")
  if [ "$status" = "200" ]; then
    PASS=$((PASS+1))
    echo "✅ $status $url"
  else
    FAIL=$((FAIL+1))
    echo "❌ $status $url"
  fi
done
echo "---"
echo "PASS: $PASS / $((PASS+FAIL))"
```

**Atteso**: 23/23 PASS (200 OK). Se anche 1 FAIL, non procedere col bump version → debug.

### 8.3 — Smoke test redirect 11 URL MVP legacy → audit-aligned

```bash
URLS_LEGACY=(
  "/lo-studio/"
  "/avvocati/"
  "/avvocati/emiliano-saltelli/"
  "/competenze/"
  "/competenze/tributario/"
  "/casi/"
  "/blog/"
  "/faq/"
  "/glossario-legale/"
  "/come-lavoriamo/"
  "/prima-consulenza/"
)

PASS=0
FAIL=0
for url in "${URLS_LEGACY[@]}"; do
  status=$(curl -s -o /dev/null -w "%{http_code}" "https://staging.studiolegalesaltelli.it${url}")
  redirect=$(curl -s -o /dev/null -w "%{redirect_url}" -L "https://staging.studiolegalesaltelli.it${url}")
  if [ "$status" = "301" ]; then
    PASS=$((PASS+1))
    echo "✅ $status $url → $redirect"
  else
    FAIL=$((FAIL+1))
    echo "❌ $status $url (expected 301)"
  fi
done
echo "---"
echo "REDIRECT PASS: $PASS / $((PASS+FAIL))"
```

**Atteso**: 11/11 PASS (301 redirect verso URL audit-aligned).

### 8.4 — Bump version → 1.1.0-ia-refactor

```bash
# functions.php
sed -i "s/define('SALTELLI_THEME_VERSION', '.*');/define('SALTELLI_THEME_VERSION', '1.1.0-ia-refactor');/" \
  wp-content/themes/saltelli/functions.php

# style.css
sed -i "s/^Version: .*/Version: 1.1.0-ia-refactor/" wp-content/themes/saltelli/style.css

# Verifica
grep SALTELLI_THEME_VERSION wp-content/themes/saltelli/functions.php
grep "^Version:" wp-content/themes/saltelli/style.css
```

### 8.5 — Deploy delta su droplet + smoke finale

```bash
# Rsync delta
rsync -avz --delete \
  wp-content/themes/saltelli/ \
  deploy@178.62.207.50:/var/www/saltelli/wp-content/themes/saltelli/

# Su droplet
ssh deploy@178.62.207.50 << 'EOF'
cd /var/www/saltelli
wp rewrite flush
wp cache flush
wp transient delete --all
wp yoast index --reindex 2>/dev/null || true
EOF

# Re-smoke 21 URL su staging post-deploy
# (riuso loop di 8.2)
```

### 8.6 — Phase 8 commit + report finale

```bash
# Crea report
cat > .claude/knowledge/audits/wave5-ia-refactor/REPORT.md << 'EOF'
# Wave 5 IA Refactor — Report

## Score
- 23/23 URL audit-aligned PASS
- 11/11 redirect 301 PASS
- 0 regressioni Lighthouse

## Phase summaries

### Phase 1 — Backup pre-Wave 5
- Theme tar.gz + DB dump completati
- Branch dedicato feat/wave5-ia-refactor

### Phase 2 — Tassonomia tipo-area
- 3° termine "contenzioso-amministrativo" creato
- 19/19 (o 16/16) competenze re-tagged
- Termine "altri" deprecato (se 0 post)

### Phase 3 — register_post_type rewrite
- avvocato: rewrite slug → /chi-siamo/team/
- competenza: rewrite slug → /aree-di-pratica/{cluster}/ (filter post_type_link)
- saltelli_caso: public=true + rewrite → /chi-siamo/risultati/

### Phase 4 — 4 nuove pagine madre + 4 template-parts hub
- chi-siamo, aree-di-pratica, risorse, costi-e-consulenze creati
- 4 template-parts hub aggiunti
- page.php router aggiornato

### Phase 5 — Page hierarchy + slug rinominazioni
- "chi-siamo" page → "lo-studio" (post_name)
- "chi-siamo-hub" page → "chi-siamo" (post_name)
- "faq" page → "domande-frequenti"
- 8 pagine spostate sotto i nuovi hub via post_parent

### Phase 6 — legacy-redirects.php
- Mappa unificata 3 stati implementata
- 11 URL MVP legacy → 301 audit-aligned

### Phase 7 — Menu navigation
- Primary menu rebuilt con 5 sezioni primarie + sub-items
- Footer menu aggiornato

### Phase 8 — Flush + sitemap + smoke + bump
- wp rewrite flush + Yoast sitemap rebuild
- Smoke 21 URL PASS
- Smoke 11 redirect PASS
- Bump 1.1.0-ia-refactor
- Deploy droplet OK

## Bloccanti residui (per orchestratore in chat)

- (Eventuale) elenco bug emersi durante smoke test che non sono stati risolvibili in autonomia
- Acceptance test Elena/Ludovica reopened su nuovo schema URL: serve campagna di re-test editoriale

## Next: Wave 6 — Extension blocchi GEO/CRO

EOF

git add .claude/knowledge/audits/wave5-ia-refactor/REPORT.md
git add wp-content/themes/saltelli/functions.php
git add wp-content/themes/saltelli/style.css
git commit -m "wave5: phase 8 — flush+sitemap+smoke pass, bump 1.1.0-ia-refactor"

# Push branch
git push origin feat/wave5-ia-refactor

# NON merge in main automaticamente — l'orchestratore in chat audita prima
```

---

## ✅ Definition of Done (Wave 5)

- [ ] 23/23 URL audit-aligned PASS HTTP 200 (smoke test 8.2)
- [ ] 11/11 URL MVP legacy redirect 301 a URL audit-aligned (smoke test 8.3)
- [ ] CPT `avvocato`, `competenza`, `saltelli_caso` rewrite slug aggiornato + post_type_link filter funzionante
- [ ] Tassonomia `tipo-area` ha 3 termini (privati, imprese, contenzioso-amministrativo) + 19/16 competenze re-tagged
- [ ] 4 pagine hub create (chi-siamo, aree-di-pratica, risorse, costi-e-consulenze) + 4 template-parts renderizzano
- [ ] Page hierarchy aggiornata: tutte le info-shared sotto costi-e-consulenze (eccetto lavora-con-noi sotto contatti); faq sotto risorse + rinominata "domande-frequenti"
- [ ] `inc/seo/legacy-redirects.php` esteso con `$mvp_to_audit_redirects` array
- [ ] Menu primary ricostruito con 5 sezioni primarie + sub-items
- [ ] Yoast sitemap rebuild + flush rewrite rules eseguito
- [ ] Lighthouse no-regression vs baseline pre-Wave 5
- [ ] Schema validation Google Rich Results Test su 5 URL nuovi (FAQPage + Person + Article + LocalBusiness + ContactPage)
- [ ] Frontend visivo INVARIATO (Wave 5 cambia solo URL e gerarchia, NON il rendering)
- [ ] Branch `feat/wave5-ia-refactor` pushato + 8 commit Phase + 1 finale bump
- [ ] Report `.claude/knowledge/audits/wave5-ia-refactor/REPORT.md` compilato
- [ ] Bump `1.1.0-ia-refactor` in functions.php + style.css

---

## 🚦 Branch & deploy state finale

```
Branch:    feat/wave5-ia-refactor (PUSHED, NOT MERGED)
Theme:     1.1.0-ia-refactor (NEW)
Staging:   https://staging.studiolegalesaltelli.it (deployed via rsync)
Production: ancora legacy Elementor (DNS non switchato)
Smoke:     23/23 URL audit-aligned + 11/11 redirect 301
Sitemap:   /sitemap_index.xml rebuilt with new URLs
```

---

## 🚦 Next dopo Wave 5 (out of scope, info per orchestratore)

- **Wave 6** — Extension blocchi GEO/CRO (lean, DEC-019). Prompt in `_shared/prompt-library/claude-code/wave6-extension-blocks.md`. Branch dedicato `feat/wave6-geo-cro-blocks` da `main` aggiornato dopo merge Wave 5.
- **Wave 4** — Production Readiness (WOFF2 + SRI + Critical CSS + Lighthouse ≥92). Prompt `prompts/PROMPT_AGENT_v1.0_WAVE4_PRODUCTION_READINESS.md` esistente, in stand-by fino a fine Wave 6.
- **Wave 7** — Cut produzione (DNS switch + redirect map legacy Elementor → audit-aligned). Prompt da scrivere a fine Wave 6+4.
- **Acceptance test editoriale Elena/Ludovica** — riapertura post-merge Wave 5 per re-test editoriale sul nuovo schema URL. Documentare in `EDITOR-HANDOFF.md` v1.2.

---

## ⚠️ Quando STOP e ritorno orchestratore

Stop immediato + commit clean state + ping orchestratore in chat se:

1. **Smoke test 21 URL** post Phase 3 (rewrite slug) ha ≥3 fallimenti → rollback rewrite changes
2. **Smoke test 11 redirect** post Phase 6 ha ≥2 fallimenti → debug `legacy-redirects.php`
3. **Yoast sitemap rebuild** non genera URL audit-aligned → debug Yoast permalink integration
4. **Cluster mapping**: durante Phase 2 emergono competenze che non hanno cluster assegnabile → escalation cliente decisione
5. **`saltelli_caso` upgrade**: emergono casi che NON dovrebbero essere pubblici per privacy → escalation cliente decisione
6. **Acceptance test editoriale** segnala bug critico durante Wave 5 → annota nell'audit log, NON fixare in Wave 5 (out of scope), comunica orchestratore
7. **Conflitto naming page slug**: Phase 5 fallisce perché slug "chi-siamo" o "lo-studio" già occupato → rinominare con suffix numerico + ping orchestratore
8. **Lighthouse regression** post-Wave 5: score scende >5 punti vs baseline → rollback Phase X identificata + ping orchestratore

In tutti i casi sopra: NON committare half-done changes. Lavorare in branch isolato + rollback locale, push pulito, escalation chat.
