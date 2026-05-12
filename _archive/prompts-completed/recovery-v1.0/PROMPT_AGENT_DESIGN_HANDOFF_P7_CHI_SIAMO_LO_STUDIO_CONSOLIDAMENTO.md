# PROMPT AGENT — Design Handoff Wave P7 · chi-siamo = lo-studio CONSOLIDAMENTO (Opzione A)

> **Scope**: consolidare le 2 pagine `/chi-siamo/` (Page 2822 hub) + `/lo-studio/` (Page 2811 editorial) in UNA sola pagina canonical `/chi-siamo/`. Strategia: **rename slug Page 2811** (Opzione A orchestratore) → preserve content Elena, minimal data migration, max SEO continuity.
>
> **Branch**: `feat/design-handoff-chi-siamo-lo-studio-consolidamento`
> **Stima**: 45-60 min Code
> **Modalità**: chore data + frontend, **NO refactor schema SCF** (group_lo_studio_v1 39 field resta). Version bump candidato (slug change + page delete).
> **Sessione**: una sola Claude Code.

---

## CONTESTO

Wave 7/12 sequenza Design Handoff. Decisione orchestratore acquisita 2026-05-12 (rovescia precedente):

**"chi-siamo = lo-studio"** — le 2 pagine sono di fatto la stessa cosa visivamente (JSX `chi-siamo/index.jsx` Design ha layout identico a `page-lo-studio.php` corrente Wave 5 STEP 3). Consolidamento.

**Opzione A scelta**: rename slug Page 2811 `lo-studio` → `chi-siamo`, cancella Page 2822 hub legacy, redirect 301 `/lo-studio/` ⏵ `/chi-siamo/`. Preserve content Elena (Page 2811 39 field già popolati Wave 5 STEP 3).

---

## ⚠️ HARD INVARIANT

1. **`group_lo_studio_v1` 39 field Elena-approved**: invariato. NON refactor schema.
2. **`group_chi_siamo_v1` 4 field Wave 4.7.fix.3**: andranno **dismessi** (Page 2822 viene cancellata). Acceptable losing schema poiché data migrata via slug change (Elena hub content non sarà più visibile, ma il design canonical sarà ora quello editorial di lo-studio che Elena ha già popolato in Wave 5 STEP 3).
3. **CPT registration**: invariato. Slug Page non tocca CPT registration.
4. **Menu primary**: aggiornare se `/chi-siamo/lo-studio/` era voce sub-menu (rimuovere) + verify `/chi-siamo/` punta al nuovo target.
5. **`SALTELLI_SCF_ONLY_PAGES`**: 13 IDs current. Rimuovere 2822 (cancellata), aggiungere 2811 con nuovo slug. Oppure mantieni 2811 (era già nella lista) e rimuovi 2822. Verifica.
6. **Redirect 301 obbligatorio**: `/lo-studio/` legacy + sub-paths se esistono → `/chi-siamo/`.
7. **Backup DB obbligatorio Phase 0** (page delete è irreversibile senza backup).

---

## PRE-FLIGHT (10 min)

1. Leggi:
   - `CLAUDE.md` (Hard constraints, Workflow rules, Lessons learned, Information architecture sezione redirect)
   - `.claude/knowledge/audits/design-handoff/RECOMMENDATION.md` (§J chi-siamo MAPPING DECISION — ora aggiornato a Opzione A)
   - `wp-content/themes/saltelli/template-parts/page-chi-siamo-hub.php` (current hub 3-card, sarà cancellato di fatto)
   - `wp-content/themes/saltelli/template-parts/page-lo-studio.php` (current editorial, resta canonical post-rename)
   - `wp-content/themes/saltelli/inc/admin/disable-gutenberg-for-scf-pages.php` (SALTELLI_SCF_ONLY_PAGES 13 IDs)
   - `wp-content/themes/saltelli/inc/seo/legacy-redirects.php` (pattern redirect Wave 4.7.fix.2)
   - `wp-content/themes/saltelli/acf-json/group_lo_studio_v1.json` (39 field, resta canonical)
   - `wp-content/themes/saltelli/acf-json/group_chi_siamo_v1.json` (4 field, resta location `page == 2822`, ma Page 2822 viene cancellata)

2. Verifica stato:
   ```sh
   git fetch origin
   git status
   git log --oneline -3   # atteso post-P5 merge
   git checkout -b feat/design-handoff-chi-siamo-lo-studio-consolidamento
   ```

3. **DB backup OBBLIGATORIO** (irreversibile senza):
   ```sh
   ssh deploy@178.62.207.50 "cd ~/backups && \
     mkdir -p design-handoff-p7-pre-consolidamento && \
     cd design-handoff-p7-pre-consolidamento && \
     sudo -u www-data wp db export db-pre-p7-$(date +%Y%m%d-%H%M).sql --path=/var/www/saltelli && \
     sudo -u www-data wp post get 2822 --format=json --path=/var/www/saltelli > page-2822-snapshot.json && \
     sudo -u www-data wp post get 2811 --format=json --path=/var/www/saltelli > page-2811-snapshot.json && \
     ls -lh"
   ```

4. Conferma in chat: branch creato + DB backup confermato + prosegui Phase 1.

---

## PHASE 1 — DISCOVERY (10 min)

### 1.A — Verifica stato Page corrente

```sh
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp post get 2811 --fields=ID,post_title,post_name,post_status,post_parent --path=/var/www/saltelli && \
  sudo -u www-data wp post get 2822 --fields=ID,post_title,post_name,post_status,post_parent --path=/var/www/saltelli"
```

Atteso:
- Page 2811: slug `lo-studio`, parent 2822 (era child di chi-siamo)
- Page 2822: slug `chi-siamo`, parent 0

**ATTENZIONE**: Page 2811 è child di Page 2822. Se cancello 2822 senza cambiare parent prima, WP imposta 2811 come orphan. Sequenza corretta:
1. Cambia `post_parent` di 2811 a `0` (top-level)
2. Cambia slug di 2811 da `lo-studio` a `chi-siamo` (Page 2822 deve essere cancellata PRIMA per liberare lo slug)
3. Cancella Page 2822

### 1.B — Verifica sub-paths chi-siamo

```sh
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp post list --post_type=page --post_parent=2822 --fields=ID,post_title,post_name --path=/var/www/saltelli"
```

Atteso:
- 2811 lo-studio (sarà rinominato, parent diventa 0)
- Verifica se ci sono altri child (es. /chi-siamo/sub-page) — atteso: NON dovrebbero (team e casi-rappresentativi sono CPT archive, non Pages)

### 1.C — Verifica menu primary references

```sh
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp menu item list saltelli-header --format=table --path=/var/www/saltelli"
```

Atteso:
- Voce `Chi Siamo` → Page 2822 (sarà sostituita con Page 2811 dopo rename + delete 2822)
- Eventuale sub-voce `Lo Studio` → Page 2811 (rimuovere o redirect)

### 1.D — Verifica SALTELLI_SCF_ONLY_PAGES

```sh
grep -E "^\s+[0-9]+," wp-content/themes/saltelli/inc/admin/disable-gutenberg-for-scf-pages.php
```

Atteso: 13 IDs includono 2811 e 2822 entrambi.

### 1.E — Output PHASE 1 in chat

Tabella consolidata:
- Page 2811 current state
- Page 2822 current state
- Sub-paths child di 2822
- Menu items referencing 2822 o 2811
- SALTELLI_SCF_ONLY_PAGES current

---

## PHASE 2 — DATA OPERATIONS (15-20 min)

Sequenza obbligatoria (ordine importa):

### 2.A — Cambia parent di Page 2811 a top-level

```sh
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp post update 2811 --post_parent=0 --path=/var/www/saltelli"
```

Effetto: Page 2811 non è più child di 2822. URL attuale: `/lo-studio/` (slug `lo-studio` invariato).

### 2.B — Cancella Page 2822 hub

```sh
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp post delete 2822 --force --path=/var/www/saltelli"
```

Effetto: Page 2822 cancellata permanentemente (force = bypass trash). Slug `chi-siamo` ora libero.

### 2.C — Rename slug Page 2811 a chi-siamo

```sh
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp post update 2811 --post_name=chi-siamo --path=/var/www/saltelli && \
  sudo -u www-data wp post update 2811 --post_title='Chi Siamo' --path=/var/www/saltelli"
```

Effetto: Page 2811 ora ha slug `chi-siamo`, title `Chi Siamo`. URL nuovo: `/chi-siamo/`.

### 2.D — Flush rewrite rules + cache

```sh
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp rewrite flush --path=/var/www/saltelli && \
  sudo -u www-data wp cache flush --path=/var/www/saltelli"
```

### 2.E — Verifica frontend

```sh
echo "=== /chi-siamo/ (nuovo URL Page 2811) ==="
curl -sI "https://staging.studiolegalesaltelli.it/chi-siamo/" | head -3
# atteso: HTTP/2 200

echo "=== /lo-studio/ (vecchio URL, deve dare 404 prima del redirect setup) ==="
curl -sI "https://staging.studiolegalesaltelli.it/lo-studio/" | head -3
# atteso: HTTP/2 404 (sarà fixato in Phase 3 con redirect 301)
```

---

## PHASE 3 — REDIRECT 301 + ADMIN UPDATES (10 min)

### 3.A — Add redirect 301 legacy

Edit `wp-content/themes/saltelli/inc/seo/legacy-redirects.php` aggiungendo:

```php
'/lo-studio/' => '/chi-siamo/',
```

Pattern coerente con redirect Wave 4.7.fix.2 (vedi file esistente).

### 3.B — Update SALTELLI_SCF_ONLY_PAGES

Edit `wp-content/themes/saltelli/inc/admin/disable-gutenberg-for-scf-pages.php`:
- Rimuovi `2822` dall'array (Page cancellata)
- Mantieni `2811` (Page conservata, nuovo slug chi-siamo)
- Total IDs: 13 → 12

```php
define('SALTELLI_SCF_ONLY_PAGES', [
    17,    // home
    23,    // contatti
    372,   // lavora-con-noi
    2708,  // domande-frequenti
    2709,  // guide-gratuite
    2711,  // prima-consulenza
    2712,  // come-lavoriamo
    2713,  // richiedi-preventivo
    2714,  // prenota-appuntamento
    2811,  // chi-siamo (ex lo-studio, P7 consolidamento) — rinominato slug
    2812,  // aree-di-pratica (hub)
    2813,  // risorse (hub)
    // 2822 REMOVED — cancellata in P7 consolidamento (era hub chi-siamo)
]);
```

### 3.C — Update menu primary

Verifica menu items referencing 2822 (cancellata) o link a `/lo-studio/`:

```sh
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp menu item list saltelli-header --format=json --path=/var/www/saltelli | \
  python3 -c \"import json, sys; items = json.load(sys.stdin); [print(i['db_id'], i.get('object_id'), i.get('title'), i.get('url')) for i in items if i.get('object_id') == '2822' or 'lo-studio' in (i.get('url') or '')]\""
```

Per ogni match:
- Se voce è 2822 con title "Chi Siamo" → aggiorna `object_id` a `2811`
- Se voce è 2811 con title "Lo Studio" (sub-menu) → rimuovi (è ora la stessa di Chi Siamo top-level)
- Se voce custom URL `/lo-studio/` → cambia a `/chi-siamo/`

WP-CLI menu update:
```sh
# Esempio (adatta in base ai db_id reali):
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp menu item update <DB_ID_chi_siamo_voce> --object-id=2811 --path=/var/www/saltelli && \
  sudo -u www-data wp menu item delete <DB_ID_lo_studio_voce> --path=/var/www/saltelli"
```

### 3.D — Cleanup group_chi_siamo_v1.json

`group_chi_siamo_v1.json` ha 4 field attached a `page == 2822` (Page cancellata). Opzioni:
- **Pulisci**: cancella `group_chi_siamo_v1.json` (i 4 field hub_chisiamo_* sono orphan, nessuno li legge più)
- **Tieni dormiente**: lascia il file ma cambia location rule a `page == 0` (mai match, di fatto disattivato)

**Decisione orchestratore**: cancella il file (clean cut, less moving parts). Aggiungi nota commit message.

```sh
rm wp-content/themes/saltelli/acf-json/group_chi_siamo_v1.json
```

### 3.E — Sync staging + reload

```sh
rsync -avz wp-content/themes/saltelli/inc/admin/ deploy@178.62.207.50:/var/www/saltelli/wp-content/themes/saltelli/inc/admin/
rsync -avz wp-content/themes/saltelli/inc/seo/ deploy@178.62.207.50:/var/www/saltelli/wp-content/themes/saltelli/inc/seo/
rsync -avz wp-content/themes/saltelli/acf-json/ deploy@178.62.207.50:/var/www/saltelli/wp-content/themes/saltelli/acf-json/
ssh deploy@178.62.207.50 "sudo systemctl reload php8.2-fpm && cd /var/www/saltelli && sudo -u www-data wp cache flush --path=/var/www/saltelli"
```

(OPcache reload obbligatorio per edit `inc/admin/*.php` e `inc/seo/*.php` — lesson Wave 4.7.fix.3).

---

## PHASE 4 — SMOKE TEST (10 min)

### 4.A — Frontend curl

```sh
echo "=== /chi-siamo/ — 200 atteso ==="
curl -sI "https://staging.studiolegalesaltelli.it/chi-siamo/" | head -3

echo "=== /lo-studio/ — 301 atteso → /chi-siamo/ ==="
curl -sI "https://staging.studiolegalesaltelli.it/lo-studio/" | head -3

echo "=== /chi-siamo/team/ — 200 atteso (CPT archive avvocato, NON tocca) ==="
curl -sI "https://staging.studiolegalesaltelli.it/chi-siamo/team/" | head -3

echo "=== /chi-siamo/casi-rappresentativi/ — 200 atteso (CPT archive saltelli_caso) ==="
curl -sI "https://staging.studiolegalesaltelli.it/chi-siamo/casi-rappresentativi/" | head -3

echo "=== /chi-siamo/ content check: presenza markup editorial ==="
curl -s "https://staging.studiolegalesaltelli.it/chi-siamo/" | grep -cE "sl-chi-siamo__hero|sl-chi-siamo__plate|sl-chi-siamo__timeline"
# atteso: count >= 3 (markup editorial di page-lo-studio.php template attached)
```

### 4.B — Admin-side smoke (lesson Wave 4.7.fix.4)

WP Admin → Pagine:
- Page 2811 ora visibile come "Chi Siamo" (title aggiornato), slug `chi-siamo`
- Metabox SCF group_lo_studio_v1 (39 field) intatto e popolato con content Elena Wave 5 STEP 3
- Gutenberg disabled ✓
- Page 2822 NON più visibile (cancellata)

### 4.C — Menu visual verify

WP Admin → Aspetto → Menu → Saltelli Header:
- Voce "Chi Siamo" punta a Page 2811 (nuovo) /chi-siamo/
- Voce "Lo Studio" rimossa (era sub-menu, ora consolidata in Chi Siamo)

### 4.D — Verifica nessun broken link

```sh
echo "=== ricerca link interni a /lo-studio/ in DB ==="
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp db query 'SELECT ID, post_title FROM wp_posts WHERE post_content LIKE \"%/lo-studio/%\" AND post_status=\"publish\"' --path=/var/www/saltelli"
# atteso: solo Page 2811 vecchio post_content che ora ha permalink /chi-siamo/ (link auto-funzionante via redirect 301), oppure 0 risultati

echo "=== ricerca menu items referencing 2822 ==="
ssh deploy@178.62.207.50 "cd /var/www/saltelli && \
  sudo -u www-data wp db query 'SELECT post_id, meta_value FROM wp_postmeta WHERE meta_key=\"_menu_item_object_id\" AND meta_value=\"2822\"' --path=/var/www/saltelli"
# atteso: 0 righe (tutti aggiornati a 2811 in Phase 3.C)
```

---

## PHASE 5 — VERSION BUMP + COMMIT + PUSH

Version bump richiesto perché schema change (page delete + slug rename + SCF group removed):

`functions.php` + `style.css`:
```
1.3.15-wave-design-handoff-p7-chi-siamo-lo-studio-consolidamento
```

Commit:
```sh
git add -A
git diff --cached --stat

git commit -m "feat(design-handoff): Wave P7 chi-siamo = lo-studio CONSOLIDAMENTO (v1.3.15)

Wave 7/12 sequenza Design Handoff. Decisione orchestratore 2026-05-12: chi-siamo = lo-studio (consolidamento 2 pagine in 1).

Opzione A applicata (lean): rename slug Page 2811 lo-studio → chi-siamo + cancella Page 2822 hub legacy + redirect 301 legacy. Preserve content Elena (39 field group_lo_studio_v1 invariati).

DATA OPERATIONS:
- Page 2811 post_parent: 2822 → 0 (top-level)
- Page 2822 hub legacy: DELETED (--force, 4 field group_chi_siamo_v1 schema rimosso)
- Page 2811 post_name: lo-studio → chi-siamo
- Page 2811 post_title: 'Lo Studio' → 'Chi Siamo'

REDIRECT 301:
- inc/seo/legacy-redirects.php: +1 entry /lo-studio/ → /chi-siamo/

SCF schema:
- group_lo_studio_v1.json (39 field, location page=2811): INVARIATO, ora attached a /chi-siamo/ canonical
- group_chi_siamo_v1.json: REMOVED (4 hub field orphan post-delete Page 2822)

CONFIG UPDATES:
- inc/admin/disable-gutenberg-for-scf-pages.php SALTELLI_SCF_ONLY_PAGES: 13 → 12 IDs (rimosso 2822)
- Menu primary: voce 'Chi Siamo' object_id 2822 → 2811, sub-voce 'Lo Studio' rimossa (consolidata)

ELENA IMPACT: minimal. /chi-siamo/ URL canonical mantenuto, content è ora la pagina editoriale completa (Plate + 1999 + 4 lawyers + 3 principi + timeline + CTA) che Elena ha già popolato in Wave 5 STEP 3 su Page 2811. Hub 3-card legacy (Page 2822) cancellato — i sub-link (team, casi-rappresentativi) sono già CPT archive accessibili da menu.

Smoke test:
- /chi-siamo/ → 200, markup editorial completo
- /lo-studio/ → 301 → /chi-siamo/
- /chi-siamo/team/ → 200 (CPT archive avvocato invariato)
- /chi-siamo/casi-rappresentativi/ → 200 (CPT archive saltelli_caso invariato)
- Admin: Page 2811 visibile come 'Chi Siamo' + 39 field SCF intatti, Page 2822 non più visibile

DB backup pre-consolidamento: ~/backups/design-handoff-p7-pre-consolidamento/ on droplet

Version bump: 1.3.14 → 1.3.15-wave-design-handoff-p7-chi-siamo-lo-studio-consolidamento
Branch: feat/design-handoff-chi-siamo-lo-studio-consolidamento · 4 file changed · ~30 righe modificate"

git tag -a v1.3.15-wave-design-handoff-p7-chi-siamo-lo-studio-consolidamento -m "Design Handoff Wave P7 — chi-siamo = lo-studio consolidamento. Page 2811 rinominato slug chi-siamo, Page 2822 hub legacy cancellato, redirect 301 /lo-studio/ → /chi-siamo/. group_lo_studio_v1 39 field Elena preserved."

git push origin feat/design-handoff-chi-siamo-lo-studio-consolidamento
```

---

## OUTPUT FINALE in chat

- Discovery PHASE 1 (Page state + sub-paths + menu items + SCF_ONLY_PAGES)
- Data operations applicate (parent change + delete + rename)
- Redirect 301 + SALTELLI_SCF_ONLY_PAGES + menu updates
- Smoke test risultati (5 URL + admin)
- DB backup confermato
- SHA commit pushato
- ETA proposto P6 taxonomy-tipo-area (se non ancora lanciato) o P8 blog-archive

---

## HARD RULES

1. **DB BACKUP OBBLIGATORIO Phase 0** (cancellazione Page irreversibile).
2. **Sequenza Phase 2 ordine importa**: parent change → delete 2822 → rename 2811. NO short-cuts.
3. **`group_lo_studio_v1` INVARIATO**: 39 field preserved (content Elena Wave 5 STEP 3).
4. **CPT archive (team, casi-rappresentativi)**: INVARIATI. NON tocca CPT registration né archive templates.
5. **Menu primary**: aggiorna voce Chi Siamo object_id, rimuovi sub-voce Lo Studio (se esiste).
6. **OPcache reload obbligatorio** post-edit `inc/admin/*.php` e `inc/seo/*.php` (lesson Wave 4.7.fix.3).
7. **Admin-side smoke obbligatorio** (lesson Wave 4.7.fix.4): verifica Page 2811 visibile come "Chi Siamo" + 39 field SCF intatti.
8. **One-writer-at-a-time**: UNICA sessione Code attiva.

---

## DECISIONE AUTONOMA AUTORIZZATA

- Cleanup `group_chi_siamo_v1.json`: cancella file (clean cut, no orphan schema).
- Menu items update: usa WP-CLI o admin diretto, motiva in commit.
- Redirect file path: usa `inc/seo/legacy-redirects.php` esistente (Wave 4.7.fix.2 pattern).
- Eventuali sub-paths child di 2822 trovati inattesi: documenta + redirect 301 specifico.
- Wording version bump: `1.3.15-wave-design-handoff-p7-chi-siamo-lo-studio-consolidamento`.

---

## TONO

Direct, concrete, zero filler. Stile commit usato dal progetto.

---

*Wave P7/12 sequenza Design Handoff. Prossima: P6 taxonomy-tipo-area (se non ancora lanciato — prompt pronto) o P8 blog-archive. Pattern lean = 1 wave alla volta su main.*
