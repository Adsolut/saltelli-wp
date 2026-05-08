# Wave 4.7.fix.1 — SCF URL Validation Hotfix · Report

**Branch**: `fix/wave4-7-fix-1-scf-url-validation`
**Theme version**: `1.3.7-wave4-7-fix-1-scf-url-validation`
**Generated**: 2026-05-08
**Phases**: 5/5 completate
**Pattern**: sequenziale (scope chirurgico, ~30 min)

---

## Executive summary

Wave 4.7.fix.1 risolve bug **SCF URL validation strict** che bloccava il save dei
campi CTA con URL relativi (es. `/contatti/`) introdotto come side effect del
switch ACF Free → SCF di Wave 4.7.fix.

**Sintomo (bug Duccio 2026-05-08 mattina)**: WP Admin → Saltelli — Settings →
Hero Homepage / CTA Defaults · modifica + "Aggiorna" → banner rosso
"Validazione fallita. 2 campi necessitano attenzione" su `hero_cta_url` e
`cta_default_url` (default `/contatti/`).

**Causa root**: SCF è più strict di ACF Free su `type: url` — il validator
`acf_validate_value` per i field URL richiede absolute URL `https://...` e
rifiuta path relativi. ACF Free era permissivo, SCF no.

**Fix applicato**: Strategy B — type `url` → `text` per field URL **interni**
(2 CTA), mantenuto `type: url` per field URL **esterni** (4 social + footer
credit Adsolut + press_outlets sub-field).

**Razionale architetturale**: URL CTA interni del proprio sito DEVONO essere
relativi per portabilità staging↔produzione (al cut Wave 7, URL assoluti
hardcoded a staging punterebbero ancora a staging — bug critico). URL
esterni (social, partner press, agency credit) sono assoluti per natura
→ `type: url` correttamente strict.

---

## Phase summary

| Phase | Outcome |
|---|---|
| 1 Investigation | 8 field type=url identificati, 2 INTERNAL (hero_cta_url, cta_default_url) + 6 EXTERNAL (footer_credit_url, 4× social_*, press_outlets.url), 0 UNKNOWN |
| 2 JSON fix | 2 field cambiati type:url→text, instructions aggiornate con "Accetta URL relativi (es. /contatti/) o assoluti (https://...).", timestamp `modified` bumped (1778168741→1778238231) |
| 3 SCF sync staging | scp + sudo cp + chown www-data, `wp acf json sync` Success: 2 item(s) synced (anche group_lo_studio_v1 pre-pending), cache flush + 8 transients deleted, field type=text confermato post-sync |
| 4 Smoke save E2E | acf_validate_value("/contatti/") = TRUE (was FALSE pre-fix) su entrambi field INTERNAL; counter-check social_facebook respinge correttamente "/relative/"; frontend renders 4× href="/contatti/" relative; 21/21 regression PASS |
| 5 Bump 1.3.7 + report + push | theme version bumped (style.css + functions.php), report generato, branch ready for orchestrator merge |

---

## Acceptance criteria

- [x] Branch `fix/wave4-7-fix-1-scf-url-validation` da main post-Wave 4.7.fix
- [x] 5 phases eseguite, 5 commit phase-by-phase + 1 commit Phase 5 finale
- [x] Field type=url **INTERNAL** identificati e cambiati a `type: text`
- [x] Field type=url **EXTERNAL** invariati (verifica esplicita)
- [x] `acf_validate_value('/contatti/', hero_cta_url)`: **TRUE** (era FALSE)
- [x] `acf_validate_value('/contatti/', cta_default_url)`: **TRUE** (era FALSE)
- [x] `acf_validate_value('/relative/', social_facebook)`: **FALSE** (correttamente strict)
- [x] `update_field` value-change save: TRUE su entrambi field INTERNAL
- [x] Frontend rendering relative URL: 4× href="/contatti/" su homepage
- [x] 21/21 smoke regression PASS · 0 FAIL
- [x] NO regression Wave 5+6+4+4.5+4.6+4.7+4.7.1+4.8+4.7.fix
- [x] Theme version `1.3.7-wave4-7-fix-1-scf-url-validation` (style.css + functions.php SALTELLI_THEME_VERSION)
- [x] Report generato (this file)
- [ ] Branch pushed (NO merge automatico) — pending orchestrator audit

---

## Hard rules rispettate

| Rule | Stato |
|---|---|
| NO commit su main | ✅ tutti su `fix/wave4-7-fix-1-scf-url-validation` |
| Backup JSON pre-modifica | ✅ `.claude/knowledge/audits/wave4-7-fix-1/group_theme_options_v1.BACKUP.json` |
| NO modifiche template PHP | ✅ helper `saltelli_option()` invariato, get_field() ritorna string sia url che text |
| NO modifiche field URL esterni | ✅ verifica esplicita: footer_credit_url + social_* + press_outlets.url type=url |
| Identificazione field INTERNAL rigorosa | ✅ classificazione automatica (heuristic name + default_value) + 0 UNKNOWN |
| Smoke test save E2E mandatory | ✅ acf_validate_value path testato (replica esatta del path admin form-submit) |
| NO regression Wave precedenti | ✅ 21/21 smoke PASS |
| Pattern HARD RULE STOP | ✅ niente blocker, niente shortcut |

---

## File modificati

| File | Op | Note |
|---|---|---|
| `wp-content/themes/saltelli/acf-json/group_theme_options_v1.json` | MOD | hero_cta_url + cta_default_url type:url→text + instructions + modified timestamp |
| `wp-content/themes/saltelli/style.css` | MOD | Version bump 1.3.6→1.3.7 |
| `wp-content/themes/saltelli/functions.php` | MOD | SALTELLI_THEME_VERSION bump |
| `prompts/PROMPT_AGENT_WAVE4_7_FIX_1_SCF_URL.md` | NEW | prompt this wave (commit Phase 1) |
| `.claude/knowledge/audits/wave4-7-fix-1/group_theme_options_v1.BACKUP.json` | NEW | backup pre-modifica |
| `.claude/knowledge/audits/wave4-7-fix-1/phase1-investigation.txt` | NEW | audit Phase 1 (8 field URL, 2 INTERNAL, 6 EXTERNAL, 0 UNKNOWN) |
| `.claude/knowledge/audits/wave4-7-fix-1/phase2-fix.txt` | NEW | git diff JSON modifica |
| `.claude/knowledge/audits/wave4-7-fix-1/phase3-sync.txt` | NEW | sync staging trace |
| `.claude/knowledge/audits/wave4-7-fix-1/phase4-smoke.txt` | NEW | smoke test save E2E |
| `.claude/knowledge/audits/wave4-7-fix-1/regression/smoke.txt` | NEW | 21/21 URL regression |
| `.claude/knowledge/recovery/WAVE4-7-FIX-1-SCF-URL-REPORT.md` | NEW | this report |

**DB ops** (già su staging via SSH durante Phase 3, non in repo):
- 2 field group synced (group_theme_options_v1 + group_lo_studio_v1 pre-pending)
- field type post-sync: hero_cta_url=text, cta_default_url=text, EXTERNAL=url

---

## Field modificati

**INTERNAL → text**:
- `hero_cta_url` (default: `/contatti/`) — label "CTA hero — URL"
- `cta_default_url` (default: `/contatti/`) — label "CTA default URL"

**EXTERNAL invariati (type:url)**:
- `social_facebook`, `social_instagram`, `social_linkedin`, `social_twitter`
- `press_outlets.url` (sub-field repeater)
- `footer_credit_url` (default `https://adsolut.it`, sempre absolute → strict OK)

---

## Lessons learned

1. **SCF validation strict vs ACF Free permissive**: il field `type: url` di SCF
   rifiuta URL relativi (`/path/`). Pattern futuro per tutti i siti WP con SCF:
   - URL **interni** del proprio sito → `type: text` (con instructions chiare)
   - URL **esterni** (social, partner, CDN) → `type: url`

2. **Cross-environment portability** (staging ↔ produzione): URL relativi nel DB
   sono mandatory. URL assoluti hardcoded a un dominio sono anti-pattern. SCF
   `type: url` strict è corretto in principio ma collide con questa best practice
   per CTA interni → workaround `type: text`.

3. **WP-CLI subcommand drift**: il prompt scriveva `wp acf sync` ma il subcommand
   corretto SCF/ACF è `wp acf json sync`. SCF 6.8.4 expone solo subcommand `json`
   con sub-azioni (export/import/status/sync). Aggiornare runbook futuri.

4. **`update_field` no-op return FALSE**: quando il valore non cambia,
   `update_field` ritorna FALSE anche se il save è semanticamente "successful"
   (perché non c'era nulla da salvare). Test E2E devono usare `acf_validate_value`
   o write+restore pattern per discriminare bug vero da no-op.

5. **CPT regression check non basta**: Wave 4.7.fix Phase 1.5 testava field CPT
   ma non field Theme Options con valori relativi. Lezione: regression suite deve
   coprire TUTTI i tipi di field già in DB, non solo CPT. Aggiungere
   `acf_validate_value` checks su seed values nel prossimo wave-template.

6. **PHP Warnings noise SCF/WP-CLI**: `wp acf json sync` emette ripetuti
   `Undefined array key "ID"` su `class-acf-field-group.php:515`. È bug interno
   SCF nel codepath WP-CLI, non blocca la sync ("Success: N item(s) synced").
   Reportabile upstream a Automattic ma fuori scope.

---

## Open items per orchestratore

1. **Audit branch + merge no-ff** `fix/wave4-7-fix-1-scf-url-validation → main`
   + tag `v1.3.7-wave4-7-fix-1-scf-url-validation`.
2. **Deploy delta `--checksum`** dei 3 file modificati (DEC-036 lesson):
   - `wp-content/themes/saltelli/acf-json/group_theme_options_v1.json` (MOD)
   - `wp-content/themes/saltelli/style.css` (version bump)
   - `wp-content/themes/saltelli/functions.php` (version bump)

   Le DB ops (re-sync field type) sono GIÀ applicate su staging via SSH durante
   Phase 3.
3. **Verifica WP Admin manuale** post-deploy: aprire Saltelli Settings → Hero
   Homepage tab → modifica subheadline → Aggiorna. Atteso: NO banner "Validazione
   fallita". Se Duccio conferma OK, chiusura definitiva.
4. **Onboarding Elena** (programmato post-Wave 4.7.fix): nessun update specifico
   richiesto al manuale EDITOR-HANDOFF v2.0 — il workflow è invariato, l'unica
   differenza è che ora i field CTA accettano valori relativi senza errore.

---

## Riferimenti

- **Bug Duccio** (2026-05-08 mattina): screenshot WP Admin Saltelli Settings
  "Validazione fallita 2 campi" su hero_cta_url + cta_default_url
- **CMS Diagnosis Round 2 REPORT.md** (2026-05-08) — origine fix architetturale
  Wave 4.7.fix
- DEC-039-COMPLETED (Wave 4.7.fix SCF Migration) — base wave + side effect non
  identificato in Phase 1.5 CPT regression check
- DEC-036 (lesson rsync `--checksum` mandatory)
- SCF docs: https://wordpress.org/plugins/secure-custom-fields/
- Audit logs: `.claude/knowledge/audits/wave4-7-fix-1/`
- Prompt: `prompts/PROMPT_AGENT_WAVE4_7_FIX_1_SCF_URL.md`

---

*Report Wave 4.7.fix.1 · 5 phases completed · branch ready for orchestrator audit + merge no-ff + tag + deploy delta `--checksum`.*
