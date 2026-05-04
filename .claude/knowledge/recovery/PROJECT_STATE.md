# PROJECT STATE — Saltelli WP CMS Recovery (consolidated)

> Documento di state condivisibile tra sessioni Claude.
> Aggiornato: post v1.0.0 Wave 1 (parziale completion).

---

## 🎯 PROGETTO

**Cliente**: Studio Legale Saltelli & Partners (Napoli, Chiaia, dal 1999)
**Agency**: Adsolut (Duccio Santoro)
**Brief**: Sito WordPress editoriale, 17 pagine, 4 lawyer, 19 aree pratica, GEO-optimized

**URL**:
- Staging: https://staging.studiolegalesaltelli.it
- Production: studiolegalesaltelli.it (DNS switch pending)

**Ambiente**:
- Local Docker: `cd /Users/aldosantoro/Desktop/DEV/saltelli-wp && docker compose ...`
- WP-CLI local: `docker compose run --rm wpcli ...`
- Droplet: `ssh deploy@178.62.207.50`, path `/var/www/saltelli/` (NO /htdocs)
- Repo: github.com/Adsolut-Ai-Agency/saltelli-wp
- Branch: main

---

## 📊 PERCORSO PROGETTO (35+ versioni)

```
v0.7 → v0.18  →  Initial design + content migration + CSS iterations
v0.19 → v0.30 →  Sessione 2 design implementation (12 JSX templates)
v0.31 → v0.35 →  Polish + Foundation Layer (refactor strutturale)
v1.0.0 Wave 0 → Foundation CMS (ACF Free + 8 CPT fake repeater)
v1.0.0 Wave 1 → ACF Field Groups setup (PARZIALE: 10/16)
v1.0.0 Wave 2 → Content migration (PROSSIMA)
v1.0.0 Wave 3 → Refactor template + editor handoff doc
```

---

## 🚨 PROBLEMA STRUTTURALE SCOPERTO (sessione corrente)

**Diagnosi**: WP-Admin "vuoto" per Elena/Ludovica.
- 80% content è HARDCODED in template PHP (page.php = 1274 righe, 94KB!)
- ACF plugin mai installato pre-recovery (theme aspettava ACF ma non c'era)
- Cliente NON può autonomamente editare contenuti

**Recovery in corso**: CMS Real Migration v1.0.

---

## ✅ COSA È STATO FATTO

### Wave 0 (completata)
- ACF Free 6.8.0 installato (LOCAL + DROPLET)
- 8 CPT "fake repeater" registrati in `inc/cpt.php`:
  - `saltelli_faq` + tax `faq_topic`
  - `saltelli_caso` + tax `caso_categoria`
  - `saltelli_modalita`, `saltelli_scenario`
  - `saltelli_principio`, `saltelli_trust`
  - `saltelli_formazione`, `saltelli_guida` (public)
- ACF JSON sync setup (`wp-content/themes/saltelli/acf-json/`)
- Theme version: 1.0.0-recovery-wave0
- Commit: bcad185 + 6265426

### Wave 1 (PARZIALE: 1/3 agent completati)

**Agent B ✅ COMPLETED** — 10 Field Groups CPT
- group_avvocato_v1
- group_competenza_v1
- group_faq_item_v1
- group_caso_item_v1
- group_modalita_item_v1
- group_scenario_item_v1
- group_principio_item_v1
- group_trust_item_v1
- group_formazione_item_v1
- group_guida_item_v1
- Commit: f1c1051

**Agent A ❌ FAILED/MISSING** — Field Groups page WP NON creati
- Atteso: group_costi, group_casi, group_contatti, group_faq, group_info_shared
- Status: 0/5 file presenti in acf-json/

**Agent C ❌ FAILED/MISSING** — Theme Options NON creato
- Atteso: group_theme_options
- Status: 0/1 file presente

**Causa probabile**: saturazione sistema durante esecuzione 3 agent paralleli
(load avg superato 68 durante Wave 1, MCP timeout, alcuni agent killati o stalled).

---

## 🔄 PROSSIMO STEP — Wave 1 Recovery (Agent A + Agent C)

```
TASK 1 — Agent A retry: 5 Field Groups page WP custom (~1.5h)
  group_costi (page id 2695)
  group_casi (page id 2699)  
  group_contatti
  group_faq (page id 2705)
  group_info_shared (5 page: guide-gratuite, come-lavoriamo, prima-consulenza,
                     lavora-con-noi, richiedi-preventivo)

TASK 2 — Agent C retry: 1 Field Group Theme Options (~1h)
  group_theme_options con 6 tabs:
  1. Studio Info (NAP)
  2. Mappa (lat/lng)
  3. Brand (payoff, statement)
  4. Footer (credit, newsletter)
  5. Social (Instagram, LinkedIn, Twitter, Facebook)
  6. CTA Defaults

ESECUZIONE:
  Sequenziale 1 agent (più safe)
  Tempo totale: ~2.5h
  
  OPPURE 2 agent paralleli tmux (~1.5h elapsed)
  Riusare scripts/wave1-launch.sh ridotto a 2 panes
```

Riferimento prompt completo: `PROMPT_AGENT_v1.0_WAVE1_FIELD_GROUPS.md`
(sezione Agent A + Agent C).

---

## 📚 ROADMAP COMPLETA RECOVERY

```
✅ Wave 0           — Foundation CMS (ACF + 8 CPT)            DONE
🟡 Wave 1            — ACF Field Groups (10/16)                 PARZIALE
   └─ Recovery       — Agent A + Agent C retry (~2.5h)          NEXT

⏳ Wave 2            — Content migration (~2-3h)
   - /costi/ hardcoded → ACF + CPT items
   - /casi/, /contatti/, /faq/ hardcoded → ACF + CPT items
   - 4 lawyer (formazione + casi + bio) → ACF
   - 3 tier-1 (cluster + FAQ) → ACF
   - 5 info pages → ACF
   - Theme Options → values

⏳ Wave 3            — Refactor template + handoff (~2h)
   - page.php 1274 → ~350 righe + 6 template-parts riusabili
   - single-avvocato/competenza usa get_field()
   - Documentazione editor-handoff.md per Elena/Ludovica
   - Sessione formazione (30 min)

⏳ v1.0.0 Production cut — DNS switch + WOFF2 + SRI + Lighthouse (~1.5h)
```

**Tempo recovery totale rimanente**: ~7-8h elapsed.

---

## 🔧 PATTERN OPERATIVI APPRESI (lessons learned)

### Anti-pattern da NON ripetere

```
1. ❌ JSX content-rich → Code hardcoded nel template PHP
   Soluzione: JSX deve essere SCHEMA, content via CMS
   
2. ❌ ACF/CMS plugin install scoperto a fine progetto
   Soluzione: Day 0 audit CMS architecture, install plugin upfront
   
3. ❌ 35 iterazioni patch invece di refactor strutturale
   Soluzione: Foundation Layer (sl-page-*) DOPO 35 versioni MA che doveva
   esistere da v0.1
   
4. ❌ 3 agent paralleli + tmux → load avg 68, MCP timeout
   Soluzione: max 2 agent paralleli su sistema standard, 3 solo se
   memoria libera >8GB
```

### Pattern OK da mantenere

```
✓ tmux load-buffer + paste-buffer + send-keys Enter (binary-safe)
✓ Lock-file coordination tra agent paralleli
✓ ACF JSON sync git versionato (acf-json/)
✓ CPT "fake repeater" workaround per ACF Free
✓ saltelli_field() helper per fallback grazioso
✓ CSS scope marker /* === v0.X.Y === */ per audit trail
```

---

## 🛠 TOOLS & RESOURCES

```
Git: 
  Branch main, 35+ commits sessione corrente
  Working tree clean post Wave 1 Agent B

Local dev:
  http://localhost:8080 (Docker WP)
  http://localhost:8081 (phpMyAdmin)
  
Droplet production:
  ssh deploy@178.62.207.50
  path: /var/www/saltelli/
  Plugin attivi: ACF Free, CF7, Honeypot, Yoast
  
Files chiave repo:
  PROMPT_AGENT_v1.0_WAVE0_FOUNDATION.md       (DONE)
  PROMPT_AGENT_v1.0_WAVE1_FIELD_GROUPS.md     (DONE B, RETRY A+C)
  PIANO_EMERGENZA_v1.0_CMS_MIGRATION.md       (master plan)
  EMAIL_CLIENTE_RECOVERY.md                    (template comm.)
  scripts/wave1-launch.sh                      (tmux 3 panes)
  
  inc/cpt.php          (8 CPT registrati Wave 0)
  inc/acf-fields.php   (ACF JSON sync setup)
  acf-json/*.json      (10/16 Field Groups)
```

---

## 🎯 OBIETTIVO FINALE PROGETTO

**Cliente autonomo** sul WP-Admin per gestire:
- Tutte le 17 page (hero, sezioni, CTA, contenuti)
- 4 lawyer (bio, formazione, casi, foto)
- 3 tier-1 competenze (FAQ, cluster, casi)
- Settings globali (NAP, brand, footer, social, CTA defaults)
- Add new FAQ, casi, guide via CPT separati

**Editor handoff documentato**: `docs/EDITOR-HANDOFF.md` (Wave 3).

**Production cut quando**: cliente confermato autonomous + Lighthouse OK + DNS switch.

---

## 📞 COMUNICAZIONE CLIENTE

Email inviata (template `EMAIL_CLIENTE_RECOVERY.md`):
- Inquadrato come "completamento naturale handoff editoriale"
- Promessa formazione 30 min editor pre-launch
- Sito staging visibile durante recovery
- Tono control-conscious, no panic

---

## ⚠️ NOTE IMPORTANTI PER CONTINUAZIONE

1. **MCP saturation risk**: load avg sistema può superare 60+ con 3 agent paralleli.
   Soluzione: max 2 agent paralleli, oppure sequential.

2. **Path droplet**: SEMPRE `/var/www/saltelli/` (NO `/htdocs`).
   Tutti i prompts che usavano `/htdocs` erano sbagliati ma rsync funzionava
   via path relative.

3. **ACF Free limitations**:
   - NO Repeater (workaround: CPT "fake repeater")
   - NO Flexible Content
   - NO Gallery
   - NO Clone field
   
4. **Theme Options menu**: `acf_add_options_page` registra `saltelli-settings`
   (visibile solo se Field Group con location options_page=saltelli-settings esiste).

5. **Drop-cap status pre-recovery**: 26 first-letter rules attive
   (target era 14, mai raggiunto). Non bloccante per CMS recovery.

6. **Foundation CSS Layer**: applicato su 13/16 page.
   Y-distance hero range 80-192px (tre design intent: compact 80, normal 96-120, extended 192).
