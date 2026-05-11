# Wave 4.7.fix.5 — REPORT finale

**Branch:** `feat/wave4-7-fix-5-pages-cleanup-blog-doc` · **Version:** `1.3.10` → `1.3.11-wave4-7-fix-5-cleanup` · **Data:** 2026-05-11
**Commits:** 5 (P1→P5) — `c8b74c6` P1 · `c0723f7` P2 · `52b82fe` P3 · `b977542` P4 · `<P5 hash>` P5
**Tempo effettivo:** ~3h (di cui ~15 min recovery dell'incident OOM staging — vedi sotto)

---

## TL;DR

| Asse | Stato | Esito |
|---|---|---|
| **A — Pages legacy cleanup** | ✅ | 35 Pages → **19** (16 cestinate: 13 bozze + 3 publish orfane). Tutte coperte da redirect 301. 1 nuovo redirect aggiunto. 1 menu item dangling rimosso. **NB: Page 2811 lo-studio NON cancellata** — il prompt la classificava erroneamente come duplicate (è una Page viva SCF-only, footer-linked). |
| **B — Blog editing chiarezza** | ✅ | Audit completo dei template blog (`02-blog-editing-map.md`) + EDITOR-HANDOFF v6.0 §9 riscritta (mappa campo→frontend) + 2 notice contestuali nell'admin (`inc/admin/post-editor-notices.php`). Niente refactor codice. |
| **C — Customizer lock-down** | ✅ | `inc/admin/customizer-lockdown.php` — 3 hook (`user_has_cap` strip + `load-customize.php` wp_die 403 + `admin_menu` remove submenu) per ruolo non-admin. ⚠️ Un incident di ricorsione in fase di sviluppo (~8 min downtime staging) — risolto. |
| **Docs** | ✅ | EDITOR-HANDOFF v5.0 → **v6.0** (nuovo §3.7 lista canonica 19 Pages, §9 blog riscritta, §5.2 aggiornata, §13 nota Customizer, changelog backfillato v3.0-v6.0). |
| **Version** | ✅ | functions.php + style.css → `1.3.11-wave4-7-fix-5-cleanup`. |

---

## ⚠️ Deviazioni dal prompt (DA AUDITARE — orchestratore)

1. **Page 2811 `lo-studio` → KEEP (il prompt diceva DELETE).** Il prompt la classifica come "DELETE — publish duplicate, già rediretto a `/chi-siamo/`". Verifica empirica: è una **Pagina viva** (`/chi-siamo/lo-studio/` HTTP 200), è il **target** del redirect `/lo-studio/` → `/chi-siamo/lo-studio/` (`inc/seo/legacy-redirects.php:66` + `inc/setup.php:74-82`), è **linkata da `footer.php:270`**, è in **`SALTELLI_SCF_ONLY_PAGES`** (Gutenberg disabled, `inc/admin/disable-gutenberg-for-scf-pages.php:32`), è **documentata in EDITOR-HANDOFF v5.0 §5.0/§5.6** come una delle 12 Pages SCF v5.0. Cancellarla = regressione visibile + contraddizione con la doc che l'orchestratore ha scritto 1 giorno prima. → **decisione: KEEP** (la più conservativa; se davvero la si vuole eliminare serve anche aggiornare `SALTELLI_SCF_ONLY_PAGES`, `footer.php`, i redirect — fuori scope di questa wave "no refactor").
2. **Conteggio: 35 → 19 KEEP, non "16".** La lista KEEP esplicita nel prompt ha **18** voci, non 16 (errore aritmetico del prompt); +2811 = 19. Cestinate: 16 (13 bozze + 361 + 356 + 2699).
3. **`inc/redirects.php` non esiste.** I redirect 301 vivono in `wp-content/themes/saltelli/inc/seo/legacy-redirects.php` (mappe `saltelli_legacy_redirect_map()` + `saltelli_mvp_to_audit_redirect_map()` + pattern regex). Adattato.
4. **`/prenota-un-appuntamento/` redirige GIÀ a `/contatti/`** (`legacy-redirects.php:63`, redirect Elementor-era preservato per hard rule #3). Non aggiunto nessun nuovo redirect per quell'URL (il prompt suggeriva `→ /prenota-appuntamento/`, ma confliggerebbe con l'esistente).
5. **Aggiunto 1 redirect:** `/chi-siamo/lo-studio/risultati/` → `/chi-siamo/casi-rappresentativi/` (`mvp_map`) — era il permalink reale di Page 2699 (`post_parent=2811`, quindi nested sotto lo-studio), che `/chi-siamo/risultati/` (già nel redirect map) NON copriva.
6. **Cancellato menu item db_id 365** ("PRENOTA APPUNTAMENTO" → object_id 361) — era nel nav menu `Main` (term_id 3, senza location → non visualizzato), ridondante con menu item 3066 (→ 2714, la Page appuntamento reale).
7. **Notice editoriali via `admin_notices`, non `post_submitbox_misc_actions`** — quest'ultimo non scatta in Gutenberg (è classic editor) e gli Articoli usano Gutenberg.
8. **`is_dismissible` invece di metabox sidebar** — un metabox sidebar near-Excerpt in Gutenberg richiede JS (PluginDocumentSettingPanel); il prompt richiedeva "niente refactor" → banner `admin_notices` PHP-only.

**Note minori (non azionate — fuori scope):**
- `EDITOR-HANDOFF.md` §5.1-5.6 (sezioni pre-v5.0) ha alcuni **ID Page stale** (es. `/come-lavoriamo/` indicata come ID 2709 ma è 2712; `/faq/` come 2705 ma è 2708; ecc.) — superati dalla tabella corretta in §5.0 (v5.0) e ora dalla §3.7 (v6.0). Cleanup completo di §5.x = wave doc futura.
- Nav menu `Main` (term_id 3, 26 voci, nessuna location) = cruft orfano Elementor-era, non usato dal tema (il tema usa `Saltelli Header` term_id 996 su location `primary`). Cleanup completo = wave futura.
- Post 600 `wp_navigation "Main"` (FSE Navigation block, publish) contiene `/prenota-un-appuntamento/` nel serialized content → **inerte** (il tema classico non usa il blocco FSE Navigation).
- Page 2811 ha `post_title` = "Chi Siamo" (slug `lo-studio`) → `<title>Chi Siamo - ...</title>` sul frontend. Quirk cosmetico pre-esistente; rinominabile via Pagine → 2811.
- `/conferma/` → 404 post-cleanup (Page 356 cestinata, nessun target canonico — era una thank-you page Elementor-era con dati stale). Accettabile (URL legacy zero-traffico).

---

## ⚠️ INCIDENT — OOM staging (~8 min downtime) — RISOLTO

**Causa:** la prima versione di `inc/admin/customizer-lockdown.php` aveva, dentro la callback del filtro `user_has_cap`, una chiamata a `is_super_admin($user->ID)` (aggiunta come ulteriore check oltre a `in_array('administrator', $user->roles)`). `is_super_admin()` su single-site fa `$user->has_cap('delete_users')` → `apply_filters('user_has_cap', ...)` → ri-trigger della stessa callback → `is_super_admin()` → **ricorsione infinita**. Eseguendo poi `wp eval 'wp_set_current_user(9); current_user_can("customize")'` per il test (WP-CLI ha `memory_limit = -1`), la ricorsione ha saturato RAM (2 GB) + swap (2 GB) → thrashing → nginx/sshd/php-fpm non rispondenti per ~8 min (frontend `000`, SSH "banner exchange timed out").

**Recovery:** l'OOM killer ha terminato il processo `php` runaway → memoria liberata → servizi ripristinati. **Nessun reboot** (uptime droplet invariato: `up 10 days`). Tentativo di `doctl ... power-cycle` correttamente bloccato dal safety classifier — si è rivelato non necessario.

**Fix:** rimossa `is_super_admin()` dalla callback `user_has_cap` (si usa solo `$user->roles` — accesso diretto a proprietà, non passa dal filtro → no ricorsione). Re-deployato immediatamente, `php -l` OK, cap check ri-eseguiti memory-capped (`php -d memory_limit=192M -d max_execution_time=12 wp eval ...`) → nessuna ricorsione, box healthy (load 27 → 0.2).

**Lesson learned (TODO orchestratore: aggiungere a CLAUDE.md "Workflow rules"):**
- **Mai chiamare `is_super_admin()` / `current_user_can()` / `user_can()` dentro una callback dei filtri `user_has_cap` o `map_meta_cap`** — fanno cap check → ricorsione infinita. Usare solo `$user->roles` / `$user->caps` / `$user->allcaps`.
- **`wp eval` su staging ha `memory_limit = -1`** → un bug di ricorsione/loop eseguito via `wp eval` può OOM-are l'intera droplet (2 GB). Per cap/eval test su staging wrappare sempre con `sudo -u www-data php -d memory_limit=192M -d max_execution_time=12 "$(command -v wp)" eval '...'`.
- **Deploy rsync:** il workflow `rsync -avz --delete` come user `deploy` di `docs/DEPLOY.md` **è rotto** (theme dir `www-data:www-data`, `deploy` senza write perms → `mkstemp Permission denied`). Workaround: `rsync --rsync-path="sudo rsync" --checksum` (`--checksum` perché i file locali hanno tutti mtime di un `git checkout` recente → senza, rsync ri-trasferisce 133 file e la connessione SSH muore). + `sudo chown -R www-data:www-data` dopo. → aggiornare `docs/DEPLOY.md`.

---

## Asse A — Pages cleanup (dettaglio in `01-pages-classification.md`)

**KEEP — 19 Pages:** `17, 23, 372, 1413, 2695, 2708, 2709, 2710, 2711, 2712, 2713, 2714, 2741, 2742, 2743, 2811, 2812, 2813, 2822`.

**Cestinate — 16 Pages** (`wp post delete <ID>` → trash, recuperabili 30gg):
- 13 bozze orfane: `21 254 273 285 292 300 305 321 947 996 1540 1558 2241` (vecchie landing SEO 2024-2025 + vecchie "competenze" 2019-2020 pre-CPT). Tutte coperte da redirect 301 in `legacy_map`.
- 3 publish orfane: `356` (conferma — thank-you Elementor, dati stale, 0 ref CF7), `361` (prenota-un-appuntamento — cruft Elementor, URL già 301→/contatti/, in menu orfano Main), `2699` (risultati — 1 paragrafo lede, URL reale `/chi-siamo/lo-studio/risultati/`, superato da archivio CPT).

**Trash totale post-wave:** 28 (16 nuovi + 12 già trashed da Wave 5 IA refactor — non toccati).

**Theme change:** `inc/seo/legacy-redirects.php` +1 entry (`/chi-siamo/lo-studio/risultati/` → `/chi-siamo/casi-rappresentativi/`).
**Menu change:** menu item db_id 365 cancellato (`wp menu item delete 365`).
**Backup Phase 0:** `~/backups/wave4-7-fix-5-pre-cleanup/db-pre-fix5-20260511-0755.sql` (59 MB) + `pages-snapshot.json` + `pages-snapshot.csv`.

## Asse B — Blog editing chiarezza (dettaglio in `02-blog-editing-map.md`)

- **Audit:** mappa esaustiva di ogni elemento frontend di `single.php` / `home.php` / `archive.php` → sorgente + admin path. Esito: blog 100% WP standard; header archivio + CTA post hardcoded by design; tag non renderizzati nel single; reading-time inconsistente single (200wpm) vs archivio (220wpm) — minor, no refactor.
- **`inc/admin/post-editor-notices.php`** (nuovo): `admin_notices` su (1) editor Articolo → promemoria mappatura sidebar→frontend, (2) editor Page Blog (1413) → "contenuto non mostrato sul frontend, vai a Articoli". `functions.php` +require.
- **EDITOR-HANDOFF §9 riscritta** (v6.0): step-by-step creazione articolo + tabella "campo sidebar → cosa diventa sul frontend" + elenco "cosa NON è editabile (generato dal tema)".

## Asse C — Customizer lock-down (dettaglio in `03-customizer-lockdown-and-incident.md`)

- **`inc/admin/customizer-lockdown.php`** (nuovo): 3 hook per non-admin — `user_has_cap` strip (`customize`/`edit_css`/`edit_theme_options`) + `load-customize.php` → `wp_die(403, "...contatta tech@adsolut.it")` + `admin_menu` remove submenu Aspetto → Personalizza. `functions.php` +require.
- **Verifica:** Elena UID 9 (`editor`) → `customize=false`, `edit_theme_options=false`, `edit_posts=true` (cap normali intatte). Admin UID 8/1 → `customize=true` (intatti). Admin GET `/wp-admin/customize.php` → 200.
- **NB:** il ruolo `editor` non aveva già `edit_theme_options`/`customize` di default WP → il Customizer non era già accessibile a Elena. Il lock-down è hardening difensivo (se mai un plugin/`add_cap` sbloccasse il ruolo). La premessa del prompt ("Elena vede il Customizer") era imprecisa, ma il lock-down resta valido pre-cut produzione.
- **`edit_css` resta `true` per `editor`**: mappa a `unfiltered_html` (cap default editor, non strippato per non rompere HTML/embed nei post). Moot: l'unica UI per "CSS aggiuntivo" è il Customizer → irraggiungibile.

---

## Final smoke test (8 punti) — TUTTI PASS

| # | Check | Risultato |
|---|---|---|
| 1 | WP Admin → Pagine: 19 publish, 0 draft | ✅ 19 publish / 0 draft / 28 trash · theme `1.3.11-wave4-7-fix-5-cleanup` attivo |
| 2 | Frontend 22 URL canonici (19 Pages + /chi-siamo/team/ + /chi-siamo/casi-rappresentativi/ + /aree-di-pratica/privati/) | ✅ tutti 200 |
| 3 | Redirect 301 legacy ancora attivi | ✅ 12 testati (`/lo-studio/`, `/chi-siamo/risultati/`, `/competenze/`, `/faq/`, `/costi/`, `/blog/`, `/avvocati/`, `/lavora-con-noi/`, `/prima-consulenza/`, `/richiedi-preventivo/`, `/guide-gratuite/`, `/glossario-legale/`) → tutti 301 a target corretto |
| 4 | Nuovo redirect `/chi-siamo/lo-studio/risultati/` + trashed URLs | ✅ `/chi-siamo/lo-studio/risultati/` → 301 → `/chi-siamo/casi-rappresentativi/` · `/prenota-un-appuntamento/` → 301 → `/contatti/` · `/conferma/` → 404 (atteso) |
| 5 | Login Elena UID 9: Customizer/CSS bloccato | ✅ `customize=false`, `edit_theme_options=false` (lockata) · `edit_posts=true` (cap editor intatte). HTTP 403 non testato (no password Elena in `.saltelli-staging-secrets`) — coperto dal cap check + da WP core (`WP_Customize_Manager::setup_theme` fa `wp_die` se `!current_user_can('customize')`). **Raccomandazione: login manuale Elena per conferma visiva, o aggiungere `WP_ELENA_PWD` ai secrets.** |
| 6 | Login admin UID 8: Customizer accessibile | ✅ GET `/wp-admin/customize.php` → 200 (customizer carica) |
| 7 | Articoli → Aggiungi nuovo: notice "lede italico" | ✅ notice "Promemoria editoriale" presente nell'HTML |
| 8 | Frontend blog: archivio + singolo post invariati | ✅ `/risorse/blog/` → 200 · singolo post (`/attenzione-alle-truffe-online/`) → render con `sl-post` + breadcrumb · nessun cambio (Phase 3 = solo admin notice, niente template) |

Box health post-tutto: load average 0.2, Mem 508/1968 MB used — pieno recupero post-incident.

---

## Rollback per phase

- **P2 (Pages cestinate):** `wp post untrash <ID>` su droplet (entro 30gg) per ognuna; oppure restore `db-pre-fix5-20260511-0755.sql`. Menu item 365: re-creare se serve (`wp menu item add-post saltelli-header 361 ...` — ma punta a una Page cestinata, meglio non farlo). Redirect: `git revert` del commit P2.
- **P3 (notice blog):** rimuovere `require .../post-editor-notices.php` da `functions.php` + cancellare il file; rsync; chown; fpm reload.
- **P4 (customizer lock):** rimuovere `require .../customizer-lockdown.php` da `functions.php` + cancellare il file; rsync; chown; fpm reload. (`git revert` del commit P4.)
- **P5 (version + doc):** `git revert` del commit P5. Re-deploy `functions.php`/`style.css` con la version precedente.

---

## TODO orchestratore

1. **Audit del branch** `feat/wave4-7-fix-5-pages-cleanup-blog-doc` (5 commit P1-P5) + merge no-ff su `main` + tag `v1.3.11-wave4-7-fix-5-cleanup`.
2. **Decidere sulla deviazione #1 (Page 2811 lo-studio)**: confermare KEEP (raccomandato) o, se la si vuole davvero eliminare, pianificare una mini-wave dedicata (aggiornare `SALTELLI_SCF_ONLY_PAGES`, `footer.php`, redirect, EDITOR-HANDOFF).
3. **Bump `CLAUDE.md`**: version `1.3.11`, tabella "What's done" +1 riga Wave 4.7.fix.5, sezione "Information architecture" — aggiornare con le 19 Pages canoniche (riferire §3.7 EDITOR-HANDOFF v6.0), aggiornare "Current state" header.
4. **Aggiungere 3 lesson learned a CLAUDE.md "Workflow rules"**: (a) mai `is_super_admin()`/`current_user_can()` dentro `user_has_cap`/`map_meta_cap` callbacks; (b) `wp eval` su staging ha `memory_limit=-1` → wrappare i test con `php -d memory_limit=192M`; (c) deploy rsync richiede `--rsync-path="sudo rsync" --checksum` (il workflow `rsync -avz --delete` come `deploy` è rotto — aggiornare anche `docs/DEPLOY.md`).
5. **Aggiungere `WP_ELENA_PWD` a `.saltelli-staging-secrets`** (UID 9) per test admin-side futuri come ruolo editor.
6. **Acceptance test editoriale Elena** post-cleanup: WP-Admin → Pagine deve mostrare 19 voci pulite; verificare che il blog editing sia chiaro con la §9 v6.0; confermare che non vede più Aspetto → Personalizza.
7. **Archiviare** `prompts/PROMPT_AGENT_WAVE4_7_FIX_5_PAGES_CLEANUP_BLOG_DOC.md` in `_archive/prompts-completed/recovery-v1.0/` + decidere fate di `pages-full-inventory.csv` (untracked in repo root — è l'inventario che hai lasciato; lo snapshot autoritativo è `pages-snapshot.json` sul droplet).
