# 🚀 SHIP PLAN 24H — Saltelli Staging Demo

> **Decisione strategica (2026-04-28)**: salto della validazione cliente pre-build.
> Si consegna staging al 99% completo, cliente valida sul vivo, completamento contenuti nei giorni successivi.
> Tagliato: Internal Review #1, Cliente Review #1, Cliente Review #2, sessioni Claude Design separate per Frame 2-5.
> Conservato: scaffold ✅, design system Claude Design ⏳, build tema, schema GEO, deploy DO droplet.

---

## ⚡ Cosa "99% completo" significa per il cliente domani

**Vedrà** (popolato e vero):
- Homepage con copy realistico, design system applicato, animazioni
- 1 pagina avvocato completa (Avv. Emiliano Saltelli, fondatore)
- 3 pagine competenza tier-1 popolate (Tributario, Lavoro, Famiglia LGBTQ+) con answer capsule + 5 FAQ ciascuna
- Le altre 16 competenze: titolo + 100 parole placeholder + answer capsule generica
- Le altre 3 schede avvocato: layout funzionante con bio breve placeholder
- Blog: i 326 post migrati automaticamente dal DB locale (già pronti dal site dump)
- Schema JSON-LD live, llms.txt servito, robots.txt AI-friendly
- Lighthouse > 85 su staging

**NON vedrà** (work-in-progress, da completare nei giorni successivi):
- Foto reali avvocati (placeholder silhouette tipografica stile Cravath)
- Foto reali sede (placeholder neutro o crop di stock-feel decoroso)
- Casi vinti/risultati popolati con copy specifico
- Earned media (sezione nascosta finché vuota)
- Le 16 competenze tier-2 nei dettagli
- I post blog ricategorizzati nei tier-1 (resta tassonomia attuale)
- Google Business Profile (workflow separato post-launch)

---

## ⏱ Timeline operativa

| Slot | Step | Owner | Output |
|---|---|---|---|
| **OGGI 18:00-19:00** | A. Export design da Claude Design → repo | Duccio | `.claude/knowledge/design/sessione-1/` popolata |
| **OGGI 19:00-19:30** | B. Estrazione tokens reali + scrittura prompt agent | Claude (chat) | 3 prompt agent + tokens-extracted.md |
| **OGGI 19:30-23:30** | C. Build tema multi-agent in tmux | Claude Code × 3 | Tema custom con design applicato + schema runtime |
| **OGGI 23:30-00:30** | D. Provisioning DigitalOcean droplet + deploy staging | Duccio + Claude | staging.studiolegalesaltelli.it (su droplet DO) live con WP + tema |
| **DOMANI 8:00-9:00** | E. Smoke test + populate contenuti minimi | Claude Code + Duccio | Homepage + 1 avvocato + 3 tier-1 popolati |
| **DOMANI 9:00-9:30** | F. Final QA + demo prep | Duccio | Sito staging "99%" pronto |
| **DOMANI 10:00+** | G. Demo cliente | Duccio | Sign-off live |

---

## A. Export design da Claude Design (Duccio, ~1h)

Da `claude.ai/design`, sessione attiva, salvare in `.claude/knowledge/design/sessione-1/`:

| File | Cosa contiene | Priorità |
|---|---|---|
| `01-design-system-overview.png` | Screenshot pannello tokens completo | ESSENZIALE |
| `02-frame-1-homepage-desktop.png` | Frame 1 desktop 1440px full | ESSENZIALE |
| `03-frame-1-homepage-mobile.png` | Frame 1 mobile 375px full | ESSENZIALE |
| `04-tokens.json` | Export JSON dei tokens (se Claude Design lo permette) | NICE-TO-HAVE |
| `05-figma-handoff.fig` | Figma file scaricabile (se disponibile) | NICE-TO-HAVE |
| `07-component-details.png` | Screenshot bottoni, accordion, list-item area-pratica, link tipografico | ESSENZIALE |

**Se Claude Design ha generato variazioni rispetto al design system del prompt v2.1** (es. ha proposto un colore leggermente diverso, o una scale tipografica più aggressiva), **SEGNALARE in `00-decisioni-claude-design.md`** così le incorporo nei prompt agent.

---

## B. Tokens extraction (Claude chat, ~30min)

Compilo io `.claude/knowledge/design/sessione-1/05-tokens-extracted.md` leggendo gli screenshot. Output formato:

```markdown
# Tokens estratti dalla Sessione 1 Claude Design

## Colori (hex puntuali)
--background: #FAFAF8
--surface:    ...
[etc]

## Typography
display font: ...
body font:    ...
[etc]

## Type scale (clamp values reali)
[etc]

## Decisioni di design specifiche
- Hero proporzioni: ...
- Lista 19 aree comportamento hover: ...
- Layout asimmetrico avvocati: ...
[etc]
```

Questo file diventa la **fonte di verità per gli agent**.

---

## C. Build tema multi-agent in tmux (Claude Code × 3, ~4h)

3 agent paralleli, 3 sessioni tmux separate, 3 prompt distinti.

### Agent 1 — Style & Animation Agent
File scope:
- `assets/css/tokens.css` ← popolare con tokens estratti
- `assets/css/base.css` ← reset + typography setup + container
- `assets/css/components/*.css` ← bottoni, link, accordion, list-item
- `assets/js/main.js` ← entrypoint
- `assets/js/lenis-init.js` ← smooth scroll Lenis
- `assets/js/gsap-init.js` ← GSAP + ScrollTrigger + SplitText
- `inc/enqueue.php` ← attivare gli enqueue commentati

Reference: `02-frame-1-homepage-desktop.png` + `03-frame-1-homepage-mobile.png`

### Agent 2 — Theme Architect Agent
File scope:
- `front-page.php` ← le 7 sezioni della Homepage
- `single-avvocato.php` ← layout pagina avvocato
- `single-competenza.php` ← branching tier-1/tier-2
- `archive-competenza.php` ← lista tipografica 19 aree
- `archive-avvocato.php` ← griglia asimmetrica 4 avvocati
- `single.php` ← articolo blog con drop cap + TOC sticky
- `header.php`, `footer.php` ← navigation + footer 3 colonne

Reference: `02-frame-1-homepage-desktop.png` + brief sezione 5 del prompt design

### Agent 3 — GEO Engineer Agent
File scope:
- `inc/schema/partial-organization.php` ← convertire JSON in PHP runtime
- `inc/schema/partial-attorney.php` ← idem
- `inc/schema/partial-faqpage.php` ← idem
- `inc/schema/partial-breadcrumb.php` ← idem
- `inc/schema/partial-article.php` ← idem
- `inc/seo/meta-tags.php` ← OG/Twitter coerenti col design
- `inc/seo/ai-files.php` ← /llms.txt rewrite + robots filter

Reference: `geo-assets/schema/*.json` (tutti)

**Tutti e tre lavorano in parallelo, scrivono su file diversi, zero collisioni.**

---

## D. Deploy DigitalOcean (Duccio + Claude, ~1h)

Specifiche droplet target:
- Region: Frankfurt (latency Italia)
- Size: Basic 4GB RAM / 2 vCPU / 80GB SSD ($24/mese — sufficiente)
- OS: Ubuntu 24.04 LTS
- Stack: Docker + docker-compose (replica del setup locale)
- DNS: `staging.studiolegalesaltelli.it` puntare a IP droplet — modifica DNS lato Adsolut

Steps:
1. `doctl compute droplet create saltelli-staging --region fra1 --size s-2vcpu-4gb --image docker-20-04 --ssh-keys <fingerprint>`
2. SSH al droplet → clone repo `Adsolut-Ai-Agency/saltelli-wp`
3. `docker compose up -d`
4. Import DB dump (DB locale già perfetto: contiene tutti i 326 post + 31 pagine + tassonomie)
5. WP-CLI search-replace `localhost:8080` → `staging.studiolegalesaltelli.it`
6. Configurare nginx reverse proxy + Let's Encrypt SSL
7. Test endpoint pubblico

---

## E. Populate contenuti minimi (Claude Code + Duccio, ~1h)

WP-CLI script che importa contenuti precompilati. Cosa popolare:

- **Homepage**: ACF settings con copy hero realistico
- **Avv. Emiliano Saltelli**: bio reale (dall'audit), specializzazioni, formazione (Federico II), orari
- **Competenza Diritto tributario**: answer capsule + 5 FAQ già in `geo-assets/schema/03-faqpage-example-tributario.json`
- **Competenza Diritto del lavoro**: answer capsule + 5 FAQ generate al momento (le scrivo io basandomi sul brief)
- **Competenza Diritto di famiglia LGBTQ+**: answer capsule + 5 FAQ (le scrivo io)
- **Le 16 tier-2**: titolo + 100 parole placeholder + answer capsule generica

Script in `bin/populate-staging-demo.sh` (script + dati JSON).

---

## F. Final QA (Duccio, 30min)

- [ ] Lighthouse mobile + desktop entrambi > 85
- [ ] Schema validation: validator.schema.org + Google Rich Results Test su 3 URL
- [ ] `/llms.txt` raggiungibile pubblicamente
- [ ] `/robots.txt` con AI crawlers
- [ ] Cross-browser quick test: Chrome + Safari + mobile iOS
- [ ] Form contatto invia mail (SMTP via Brevo/SendGrid)
- [ ] HTTPS verde su staging
- [ ] Nessun riferimento a `localhost:8080` rimasto

## G. Demo cliente (Duccio, 30-60min)

Narrativa per la demo:
1. Apri staging.studiolegalesaltelli.it dal vivo davanti al cliente
2. "Avv. Saltelli, questo è il sito al 99%. Lo vede dal vivo, navigabile."
3. Mostra hero, scroll lento, lista 19 aree, asimmetria avvocati
4. Apri pagina Diritto tributario tier-1 vs Domiciliazione tier-2 (mostra differenza profondità)
5. Apri Avv. Emiliano Saltelli compilato
6. "Cosa manca: foto reali, copy delle altre 18 pagine, blog ricategorizzato. È lavoro di Elena nei prossimi giorni. Lei vede già lo scheletro definitivo."
7. "Validi il design? Andiamo avanti col completamento?"

Risposte preparate alle obiezioni standard (vedi `CLAUDE_DESIGN_PROMPT.md` v2.1).

---

## ⚠️ Punti di rottura possibili (con mitigazione)

| Rischio | Probabilità | Mitigazione |
|---|---|---|
| Claude Design output ambiguo o token sbagliati | Media | Esporto io tokens manualmente leggendo gli screenshot, in 30min |
| 3 agent in tmux collidono su file condivisi | Bassa | Compartimentazione del prompt è scope-disjoint, già verificato |
| DigitalOcean droplet provisioning lento | Bassa | Backup plan: usare staging server SiteGround del cliente come fallback |
| DNS propagation > 1h | Media | Usare IP diretto per la demo se necessario |
| SMTP form contatto non configurato | Alta | OK rimandare post-demo, è dettaglio funzionale |
| Lighthouse < 85 | Media | Disattivare temporaneamente animazioni pesanti se serve, il design system base è light |

---

## Definition of Done finale (per il cliente)

- ✅ Staging accessibile pubblicamente da `staging.studiolegalesaltelli.it`
- ✅ HTTPS attivo
- ✅ Homepage perfetta (design system applicato, animazioni, copy realistico)
- ✅ Almeno 1 avvocato + 3 competenze tier-1 popolate al 100%
- ✅ Tema custom attivo, NON Elementor
- ✅ Schema JSON-LD validato
- ✅ /llms.txt pubblicamente raggiungibile
- ✅ Mobile responsive perfetto
- ✅ Lighthouse mobile > 85

---

## Post-demo — pianificazione completamento (giorni successivi)

(Non parte dello SHIP 24H — solo per memoria.)

| Task | Owner | Tempo stimato |
|---|---|---|
| Foto reali avvocati + sede | Cliente / Ludovica | 2-3 giorni |
| Bio complete 3 avvocati restanti | Elena | 1 giorno |
| Popolamento 16 competenze tier-2 | Elena | 3-5 giorni |
| Casi vinti/risultati copy | Elena + avvocati | 2 giorni |
| Ricategorizzazione 326 post blog nelle nicchie tier-1 | Elena + Claude (assistance LLM) | 2-3 giorni |
| Setup Google Business Profile | Adsolut + cliente | 1 giorno |
| Earned media: pitch a Altalex/Diritto.it | Adsolut + avvocati | 2 settimane |
| Deploy produzione (cambio www → staging) | Adsolut | 1 giorno |

---

*SHIP PLAN 24H — versione 1.0 — 2026-04-28 evening.*
*Approvato: Aldo Santoro (Adsolut). Esecuzione: Claude (chat) + Claude Code × 3 + Duccio.*
