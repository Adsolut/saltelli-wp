# 🔧 MINI-FIX Wave 5 — Consolidamento `diritto-di-famiglia` (BLOCKER B)

> **Audience**: Claude Code agent in sessione corrente (NON nuova sessione).
> **Branch**: continua su `fix/wave5-blog-rewrites` (NON nuovo branch).
> **Scope**: mini-fix DELETE post + aggiunta redirect 301. Stima 15-20 min.
> **Trigger**: orchestratore ha approvato FIX A per BLOCKER A. Decisione cliente sere 2026-05-06: CONSOLIDARE.

---

## 🎯 Tu sei

Claude Code agent che continua a lavorare sullo stesso branch `fix/wave5-blog-rewrites` per chiudere il secondo blocker dell'audit Wave 5: discrepanza count aree (18 effettivo vs 17 firmato cliente).

**Decisione cliente cristallizzata** (sere 2026-05-06): la competenza extra `diritto-di-famiglia` (ID 2669, generale NO LGBTQ+) trovata da DISCOVERY-01 va **consolidata con la versione LGBTQ+** (ID 2666). In pratica: DELETE del post extra + redirect 301 verso il sibling LGBTQ+, in modo che eventuali backlink interni o link da menu/contenuti finiscano sulla pagina giusta.

Risultato atteso post mini-fix: 17 aree finali allineate a DEC-021 (14 privati + 2 imprese + 1 contenzioso-amministrativo).

---

## 📚 Letture obbligatorie

1. **`CLAUDE.md`**
2. **`prompts/PROMPT_FIX_WAVE5_BLOG_REDIRECT.md`** — il prompt fix appena completato (riferimento pattern di lavoro)
3. **`.claude/knowledge/audits/wave5-ia-refactor/blockers.md`** § DISCOVERY-01 — contesto del bug B
4. **`prompts/cluster-mapping-17-areas.csv`** — deliverable cliente-firmato (atteso 17 aree)
5. **`wp-content/themes/saltelli/inc/seo/legacy-redirects.php`** — il file da estendere

---

## 🔍 Stato pre-mini-fix (cristallizzazione)

Nel DB MVP corrente sul branch `fix/wave5-blog-rewrites`:

| Post | ID | Slug | Cluster term assegnato | Status |
|---|---|---|---|---|
| Diritto di famiglia | **2669** | `diritto-di-famiglia` | `privati` | publish — **DA ELIMINARE** |
| Diritto di famiglia LGBTQ+ | **2666** | `diritto-di-famiglia-lgbtq` | `privati` | publish — **DA MANTENERE** |

URL attuali:
- ❌ `/aree-di-pratica/privati/diritto-di-famiglia/` (publish, da DELETE)
- ✅ `/aree-di-pratica/privati/diritto-di-famiglia-lgbtq/` (publish, target del redirect)

Distribuzione cluster pre-mini-fix: 15 privati + 2 imprese + 1 contenzioso-amministrativo = **18 aree**
Distribuzione cluster post-mini-fix: 14 privati + 2 imprese + 1 contenzioso-amministrativo = **17 aree** ✅ DEC-021

---

## 📋 Plan di esecuzione (5 step)

### Step 1 — Verifica pre-fix stato + backup post extra

```bash
cd ~/Desktop/DEV/saltelli-wp/
# Sei già su branch fix/wave5-blog-rewrites — verifica
git branch --show-current   # atteso: fix/wave5-blog-rewrites

# Pre-fix snapshot dello stato post-Wave5
docker-compose exec -T wp wp post list \
    --post_type=competenza --post_status=publish \
    --format=csv --fields=ID,post_name,post_title \
    > .claude/knowledge/audits/wave5-ia-refactor/10-pre-consolidate-competenze.csv

cat .claude/knowledge/audits/wave5-ia-refactor/10-pre-consolidate-competenze.csv

# Verifica conteggio (atteso: 18 righe + header)
wc -l .claude/knowledge/audits/wave5-ia-refactor/10-pre-consolidate-competenze.csv

# Salva backup del post 2669 prima di eliminarlo (per safety + audit trail)
docker-compose exec -T wp wp post get 2669 --format=json \
    > .claude/knowledge/audits/wave5-ia-refactor/10-backup-post-2669.json

# Salva anche meta + ACF fields associati
docker-compose exec -T wp wp post meta list 2669 --format=json \
    > .claude/knowledge/audits/wave5-ia-refactor/10-backup-post-2669-meta.json
```

### Step 2 — Verifica nessun link interno da menu/footer/contenuti punta a ID 2669

⚠️ **Important check**: prima di eliminare, verifica che nessun menu item, hardcoded URL, o ACF relationship punti a 2669.

```bash
# Check menu items che linkano a 2669
docker-compose exec -T wp wp db query "
SELECT post_id, meta_key, meta_value 
FROM wp_postmeta 
WHERE meta_value LIKE '%2669%' 
AND meta_key IN ('_menu_item_object_id', '_menu_item_url')
LIMIT 20;
" 2>&1 | tee .claude/knowledge/audits/wave5-ia-refactor/10-menu-refs-to-2669.txt

# Check ACF relationships che includono 2669 (es. related_competenze su altre competenze)
docker-compose exec -T wp wp db query "
SELECT post_id, meta_key, meta_value 
FROM wp_postmeta 
WHERE meta_value LIKE '%2669%' 
AND meta_key NOT LIKE '\\_%'
LIMIT 20;
" 2>&1 | tee .claude/knowledge/audits/wave5-ia-refactor/10-acf-refs-to-2669.txt

# Check hardcoded /diritto-di-famiglia/ in template files (escluso slug LGBTQ+)
grep -rn "/diritto-di-famiglia/" wp-content/themes/saltelli/ \
    --include="*.php" --include="*.css" --include="*.js" 2>/dev/null \
    | grep -v "diritto-di-famiglia-lgbtq" \
    | tee .claude/knowledge/audits/wave5-ia-refactor/10-hardcoded-refs-to-famiglia.txt
```

**Stop conditions**:
- Se trovi menu items con `_menu_item_object_id = 2669` o ACF relationships che lo includono → **annota in blockers**, decidi se aggiornare prima di eliminare (raccomandato: sostituirli con riferimento a 2666 LGBTQ+).
- Se trovi hardcoded `/diritto-di-famiglia/` (non LGBTQ+) in template → **annota** (probabilmente necessita update, ma non bloccante perché il redirect lo copre).

### Step 3 — DELETE post 2669 + cleanup

```bash
# DELETE forced (no trash, rimozione definitiva)
docker-compose exec -T wp wp post delete 2669 --force

# Verifica eliminazione
docker-compose exec -T wp wp post get 2669 --field=ID 2>&1 | head -3
# Atteso: error message "Could not find the post with ID 2669"

# Conteggio competenze post-delete (atteso 17)
docker-compose exec -T wp wp post list --post_type=competenza --post_status=publish --format=count
# Atteso: 17

# Distribuzione cluster post-delete
echo "=== Distribuzione cluster post-delete ==="
for cluster in privati imprese contenzioso-amministrativo; do
    count=$(docker-compose exec -T wp wp post list \
        --post_type=competenza --post_status=publish \
        --tax_query="taxonomy=tipo-area;field=slug;terms=$cluster" \
        --format=count)
    echo "$cluster: $count"
done
# Atteso: privati=14, imprese=2, contenzioso-amministrativo=1, totale 17
```

### Step 4 — Aggiungi redirect 301 in `legacy-redirects.php`

Edita `wp-content/themes/saltelli/inc/seo/legacy-redirects.php` — aggiungi UNA riga al `$mvp_to_audit_redirects` array (mappa B → C):

```php
// Aggiungi dentro saltelli_mvp_to_audit_redirect_map() return array, nella sezione MVP→audit:
'/aree-di-pratica/privati/diritto-di-famiglia/'       => '/aree-di-pratica/privati/diritto-di-famiglia-lgbtq/',
```

⚠️ **Importante**: questo è un redirect **MVP→audit** (non legacy Elementor → MVP), quindi va aggiunto specificamente nella mappa B. Posizione raccomandata: **dopo le altre mappe MVP→audit, prima del closing `)`**. Stile coerente con il file esistente (allineamento colonne).

⚠️ **Note**: questo redirect è solo per gli URL post-Wave5 (`/aree-di-pratica/privati/diritto-di-famiglia/` ). Il vecchio URL legacy `/avvocato-divorzista/` ed equivalenti già redirezionano a `diritto-di-famiglia-lgbtq` via mappa A — **NON modificare** quelli, sono coerenti.

### Step 5 — Smoke + commit + push

```bash
# Flush rewrite + cache (per essere sicuri)
docker-compose exec -T wp wp rewrite flush --hard
docker-compose exec -T wp wp cache flush
docker-compose exec -T wp wp transient delete --all

# Smoke: nuovo redirect funziona?
echo "=== Smoke consolidamento ===" > .claude/knowledge/audits/wave5-ia-refactor/cli-output/10-smoke-consolidate.txt

# Test 1: vecchio URL ora 301 → LGBTQ+
status=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:8080/aree-di-pratica/privati/diritto-di-famiglia/")
redirect=$(curl -s -o /dev/null -w "%{redirect_url}" "http://localhost:8080/aree-di-pratica/privati/diritto-di-famiglia/")
echo "Test 1 — /aree-di-pratica/privati/diritto-di-famiglia/ → ${status} → ${redirect}" \
    | tee -a .claude/knowledge/audits/wave5-ia-refactor/cli-output/10-smoke-consolidate.txt
# Atteso: 301 → http://localhost:8080/aree-di-pratica/privati/diritto-di-famiglia-lgbtq/

# Test 2: target LGBTQ+ ancora 200
status=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:8080/aree-di-pratica/privati/diritto-di-famiglia-lgbtq/")
echo "Test 2 — /aree-di-pratica/privati/diritto-di-famiglia-lgbtq/ → ${status}" \
    | tee -a .claude/knowledge/audits/wave5-ia-refactor/cli-output/10-smoke-consolidate.txt
# Atteso: 200

# Test 3: smoke 32 audit-aligned (no regression — ne basta un campione di 5)
for url in / /aree-di-pratica/ /aree-di-pratica/privati/ /chi-siamo/team/antonia-battista/ /risorse/blog/; do
    status=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:8080${url}")
    echo "Smoke 32 sample — ${url} → ${status}" \
        | tee -a .claude/knowledge/audits/wave5-ia-refactor/cli-output/10-smoke-consolidate.txt
done
# Atteso: tutti 200

# Test 4: 18 redirect legacy (no regression — campione 3)
for url in /lo-studio/ /avvocati/ /casi/; do
    status=$(curl -s -o /dev/null -w "%{http_code}" "http://localhost:8080${url}")
    echo "Smoke 18 sample — ${url} → ${status}" \
        | tee -a .claude/knowledge/audits/wave5-ia-refactor/cli-output/10-smoke-consolidate.txt
done
# Atteso: tutti 301

cat .claude/knowledge/audits/wave5-ia-refactor/cli-output/10-smoke-consolidate.txt
```

### Step 6 — Commit + push

```bash
git add wp-content/themes/saltelli/inc/seo/legacy-redirects.php
git add .claude/knowledge/audits/wave5-ia-refactor/10-*

git commit -m "fix(wave5): consolidate diritto-di-famiglia ID 2669 with LGBTQ+

DISCOVERY-01 (audit Wave 5 BLOCKER B): post extra trovato in DB MVP non
in CSV cliente-firmato. Decisione cliente: consolidare con sibling LGBTQ+.

- DELETE post 2669 (was: \"Diritto di famiglia\", slug diritto-di-famiglia)
  Backup in 10-backup-post-2669.json + meta in 10-backup-post-2669-meta.json
- Aggiunto redirect 301 in legacy-redirects.php (mappa B→C):
    /aree-di-pratica/privati/diritto-di-famiglia/
      → /aree-di-pratica/privati/diritto-di-famiglia-lgbtq/

Distribuzione cluster post-fix: 14 privati + 2 imprese + 1 contenzioso = 17 aree
(allineata a DEC-021 cliente-firmato)

Closes BLOCKER B audit Wave 5."

git push origin fix/wave5-blog-rewrites
```

### Step 7 — Aggiorna report finale

Aggiungi sezione al `.claude/knowledge/recovery/WAVE5-FIX-BLOG-REPORT.md` (oppure crea nuovo `WAVE5-FIX-DISCOVERY-01-REPORT.md` — preferenza Claude Code, l'orchestratore non vincola):

```markdown
---

## Mini-fix follow-up: BLOCKER B (DISCOVERY-01)

**Date**: 2026-05-06
**Decisione cliente**: CONSOLIDARE con LGBTQ+
**Action**: DELETE post 2669 + aggiunta redirect 301

### Pre-state
- 18 competenze publish (15 privati + 2 imprese + 1 contenzioso-amministrativo)
- ID 2669 `diritto-di-famiglia` (extra non in CSV cliente)

### Operazioni
1. Backup post 2669 + meta in `10-backup-post-2669*.json`
2. `wp post delete 2669 --force` → eliminato
3. Aggiunto redirect 301 in `legacy-redirects.php`:
   `/aree-di-pratica/privati/diritto-di-famiglia/` → `/aree-di-pratica/privati/diritto-di-famiglia-lgbtq/`
4. Flush rewrite + cache

### Post-state
- 17 competenze publish (14 privati + 2 imprese + 1 contenzioso-amministrativo) ✅ allineata a DEC-021
- Smoke consolidamento PASS (vedi `10-smoke-consolidate.txt`)
- No regression sugli altri smoke (32 audit-aligned + 18 redirect campione PASS)

### File modificato
- `wp-content/themes/saltelli/inc/seo/legacy-redirects.php`

### Audits
- `10-pre-consolidate-competenze.csv` (18 righe pre-fix)
- `10-backup-post-2669.json` + `10-backup-post-2669-meta.json` (safety backup)
- `10-menu-refs-to-2669.txt` + `10-acf-refs-to-2669.txt` (zero ref interne attese)
- `10-hardcoded-refs-to-famiglia.txt` (zero hardcoded attese)
- `10-smoke-consolidate.txt` (smoke verification)
```

---

## ✅ Acceptance criteria mini-fix

- [ ] Branch sempre `fix/wave5-blog-rewrites` (NO nuovo branch)
- [ ] Backup post 2669 + meta + ACF fields salvati in audit trail
- [ ] Verifica zero menu items / ACF relationship / hardcoded URL puntano a 2669
- [ ] `wp post delete 2669 --force` eseguito
- [ ] Conteggio competenze post-delete = **17** (14+2+1)
- [ ] Redirect 301 aggiunto in `legacy-redirects.php` mappa B
- [ ] Flush rewrite + cache eseguiti
- [ ] Smoke consolidamento PASS (Test 1: 301, Test 2: 200, Test 3: tutti 200, Test 4: tutti 301)
- [ ] 1 commit aggiuntivo sul branch (totale 2 commits sul branch fix)
- [ ] Branch pushed
- [ ] Report aggiornato (nuovo o append all'esistente)

---

## 🚨 Cosa NON fare

- ❌ NON eliminare post 2666 (`diritto-di-famiglia-lgbtq`) — è il TARGET del consolidamento
- ❌ NON modificare i redirect legacy Elementor esistenti (mappa A → C) per `/avvocato-divorzista/`, `/avvocato-divorzista-italia/` — già coerenti, puntano già a `diritto-di-famiglia-lgbtq`
- ❌ NON creare nuovo branch — continua su `fix/wave5-blog-rewrites`
- ❌ NON fare merge automatico
- ❌ Theme version invariata (`1.1.0-wave5-ia-refactor`)

---

## 🎯 Output expected

1. Branch `fix/wave5-blog-rewrites` con 2 commit totali (FIX A + mini-fix B)
2. File modificati totali sul branch fix:
   - `inc/seo/wave5-blog-rewrites.php` (FIX A)
   - `inc/seo/legacy-redirects.php` (mini-fix B, +1 riga)
3. Audit trail completo in `.claude/knowledge/audits/wave5-ia-refactor/`:
   - `10-pre-consolidate-competenze.csv`
   - `10-backup-post-2669*.json` (safety)
   - `10-menu-refs-to-2669.txt`, `10-acf-refs-to-2669.txt`, `10-hardcoded-refs-to-famiglia.txt`
   - `10-smoke-consolidate.txt`
4. Report finale aggiornato

L'orchestratore (chat) audisce + decide il **merge finale** in 3 step:
1. Merge `fix/wave5-blog-rewrites` → `feat/wave5-ia-refactor` (no-ff)
2. Merge `feat/wave5-ia-refactor` → `main` (no-ff) + tag `v1.1.0-wave5-ia-refactor`
3. Push tags

Una volta mergeato, l'orchestratore popola DEC-024 finale, aggiorna `mvp-state-snapshot.md` a `1.1.0-wave5`, prepara prompt Wave 6 v1.1 corretto (rimuovendo CAL-W6-02 invalido) + lancia Wave 6.
