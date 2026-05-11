# Wave 4.7.fix.5 — Phase 4: Customizer lock-down + ⚠️ incident OOM staging

**Data:** 2026-05-11 · **File:** `wp-content/themes/saltelli/inc/admin/customizer-lockdown.php`

---

## ⚠️ INCIDENT — OOM staging (~8 min downtime) — risolto

**Cosa è successo:** la PRIMA versione di `customizer-lockdown.php` deployata aveva, dentro la callback del filtro `user_has_cap`, una chiamata a `is_super_admin($user->ID)` (aggiunta come "belt-and-suspenders" oltre al check `in_array('administrator', $user->roles)`). `is_super_admin()` su single-site fa `$user->has_cap('delete_users')` → `apply_filters('user_has_cap', ...)` → **ri-trigger della stessa callback** → `is_super_admin()` → … **ricorsione infinita**. Eseguendo poi `wp eval 'wp_set_current_user(9); current_user_can("customize")'` per il test (WP-CLI ha `memory_limit = -1`), la ricorsione è cresciuta fino a saturare RAM (2 GB) + swap (2 GB) → thrashing → nginx/sshd/php-fpm non rispondenti per ~8 minuti → frontend `000`, SSH "banner exchange timed out".

**Recovery:** l'OOM killer ha eventualmente terminato il processo `php` runaway → memoria liberata → i servizi (che erano solo in coda, non killati) hanno ripreso. **Nessun reboot necessario** (uptime droplet invariato: `up 10 days`). Tentativo di `doctl ... power-cycle` correttamente bloccato dal safety classifier (azione infra disruptive fuori scope) — si è rivelato non necessario.

**Fix:** rimossa la chiamata `is_super_admin()` dalla callback `user_has_cap`. Si usa SOLO `in_array('administrator', (array) $user->roles, true)` (accesso diretto alla proprietà, non passa dal filtro → no ricorsione). Commento `⚠️` aggiunto nel file. Versione fixed re-deployata immediatamente (md5 `2f21e9c9...`), `php -l` OK, frontend tornato 200, cap check ri-eseguiti (memory-capped: `php -d memory_limit=192M -d max_execution_time=12`) — nessuna ricorsione, box healthy (load 27→1.6).

**Lesson learned (da aggiungere a CLAUDE.md "Workflow rules"):**
- **Mai chiamare `is_super_admin()`, `current_user_can()`, `user_can()`, `current_user_can_for_blog()` dentro una callback del filtro `user_has_cap`** (o `map_meta_cap`): tutte fanno una cap check che ri-trigger il filtro → ricorsione infinita. Usare solo `$user->roles` / `$user->caps` / `$user->allcaps` (accesso diretto a proprietà).
- **`wp eval` su staging ha `memory_limit = -1`** → un bug di ricorsione/loop in un file PHP del tema, eseguito via `wp eval`, può OOM-are l'intera droplet (2 GB). Per i test di capability/eval su staging, wrappare sempre con `sudo -u www-data php -d memory_limit=192M -d max_execution_time=12 "$(command -v wp)" eval '...'` come rete di sicurezza.
- Smoke test post-deploy di file PHP del tema: **`php -l` sul droplet PRIMA di qualsiasi `wp eval`/curl autenticato** (già nel runbook, ma qui il lint passava — la ricorsione non è un errore di sintassi, è logica runtime).

---

## Customizer lock-down — implementazione

`inc/admin/customizer-lockdown.php` — 3 livelli per ruolo non-administrator:

1. **`user_has_cap`** rimuove `customize`, `edit_css`, `edit_theme_options` dai caps primitivi (early-return per chi ha il ruolo `administrator`).
2. **`load-customize.php`** → `wp_die(403, "Non hai i permessi per accedere al Customizer. Per modificare design/layout/CSS contatta l'amministratore: tech@adsolut.it")` se un non-admin GET-ta `/wp-admin/customize.php` (belt-and-suspenders: WP core stesso fa già `wp_die` in `WP_Customize_Manager::setup_theme()` se `! current_user_can('customize')`).
3. **`admin_menu`** rimuove il submenu Aspetto → Personalizza (+ varianti `?return=…`) per i non-admin.

Caricato in `functions.php` dopo `post-editor-notices.php`.

### Verifica (2026-05-11, post-fix)

| Check | Risultato |
|---|---|
| `wp eval` cap check — Elena UID 9 (`editor`) | `customize=false` ✓ · `edit_theme_options=false` ✓ · `edit_css=true` (vedi nota) · `edit_posts=true` ✓ (cap editor normali intatte) · `administrator=false` ✓ |
| `wp eval` cap check — Adsolut Staff UID 8 (`administrator`) | `customize=true` ✓ · `edit_theme_options=true` ✓ · `edit_css=true` ✓ · `administrator=true` ✓ |
| `wp eval` cap check — Emiliano UID 1 (`administrator`) | `customize=true` ✓ |
| HTTP — admin UID 8 → `GET /wp-admin/customize.php` | **200**, `<title>Personalizza Caricamento in corso…</title>`, `customize-controls` presente → Customizer carica regolarmente per admin ✓ |
| HTTP — admin UID 8 → `GET /wp-admin/themes.php` | 200 ✓ |
| HTTP — admin UID 8 → `GET /wp-admin/post-new.php?post_type=post` | 200 + notice "Promemoria editoriale" / "diventa il lede italico" presente ✓ (verifica Phase 3 micro-fix) |
| HTTP — admin UID 8 → `GET /wp-admin/post.php?post=1413&action=edit` | 200 + notice "pagina-contenitore del blog" presente ✓ (verifica Phase 3 micro-fix) |
| HTTP — Elena UID 9 → `GET /wp-admin/customize.php` → 403 | ⚠️ **NON testato via HTTP**: il password di Elena (UID 9) **non è in `.saltelli-staging-secrets`** (lì ci sono solo `WP_EMILIANO_PWD` UID 1 e `WP_ADSOLUT_PWD` UID 8). Tentativo di generare una sessione via `wp_generate_auth_cookie` + curl fallito (cookie non autenticato — probabile mangling del `\|` nel valore o flag cookie). **Sostanza coperta dal cap check**: `current_user_can('customize')=false` per Elena → WP core stesso (`WP_Customize_Manager::setup_theme`) la blocca con `wp_die`, indipendentemente dai 3 hook del tema. **Raccomandazione orchestratore**: (a) login manuale come Elena per conferma visiva, oppure (b) aggiungere `WP_ELENA_PWD` a `.saltelli-staging-secrets` per test futuri. |
| Box health post-tutto | load average 1.6 (era 27 durante l'incident), Mem 576/1968 MB used ✓ |

### Note

- **`edit_css` resta `true` per il ruolo `editor`**: `edit_css` è una meta-cap che `map_meta_cap` risolve in `unfiltered_html` (NON `edit_theme_options`), e gli `editor` hanno `unfiltered_html` di default. `unset($allcaps['edit_css'])` nel filtro `user_has_cap` è no-op (lavora sui caps primitivi, `edit_css` non lo è). **Non si striscia `unfiltered_html`** perché serve agli editor per HTML/embed nei post. **Moot**: l'unica UI per usare "CSS aggiuntivo" è il Customizer → irraggiungibile per Elena (`customize=false`).
- **Il ruolo `editor` di Elena NON aveva già `edit_theme_options`/`customize` di default WP** → il Customizer di norma non le appariva. Il lock-down è una **rete di sicurezza difensiva** (se mai un plugin/`add_cap` sbloccasse quel ruolo). Non è una regressione del comportamento attuale — la premessa del prompt ("Elena vede il Customizer") era imprecisa, ma il lock-down resta valido come hardening pre-cut produzione.
