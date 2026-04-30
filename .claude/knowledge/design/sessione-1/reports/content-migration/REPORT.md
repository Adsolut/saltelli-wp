# Content Migration Agent Step D — Report finale

**Data:** 2026-04-29
**Theme version (in):** `0.4.0-beta-impeccable` + foto Emiliano (ATT 2683 → CPT 2660)
**Theme version (out):** `0.5.0-beta-content`
**Modalità:** WP-CLI `eval-file` con script PHP single-pass + JSON config

---

## 1 · Task status (8/8)

| # | Task | Status | Note |
|---|---|---|---|
| D1 | Recon DB contenuti originali | ✅ | 31 pagine catalogate + content-recon.md (mapping + length + Lorem ipsum detection su Chi Siamo) |
| D2 | Mapping pagine source → CPT competenza | ✅ | competenza-mapping.json: 16 CPT con source diretto, 3 senza (synth) |
| D3 | Strip Elementor + extract clean content | ✅ | Funzione `saltelli_clean_source_html()` light cleanup (img absolute URLs, srcset, data-elementor-*, empty `<p>`). NB: `post_content` source era già rendered HTML, no Elementor JSON da strippare |
| D4 | answer_capsule + body 19 CPT competenza | ✅ | Tier-1 (3): 5 FAQ + casi rappresentativi + (2 con synth body_extended). Tier-2 (16): 3 FAQ ciascuno |
| D5 | Migrazione 4 profili avvocato | ✅ | bio_breve + bio_estesa + ruolo + specs + formazione + email + telefono per 4/4 avvocati. Foto Emiliano (`_thumbnail_id=2683`) **NON sovrascritta** (verificato post-run) |
| D6 | Mapping autori blog → CPT avvocato | ✅ | `_wp_author_id` meta su 4/4 CPT. 317/326 post mappabili (97.2%) — 9 post di Gabriele Cascone (ex-collaboratore) restano associati al wp_user senza CPT |
| D7 | Verifica visuale via curl | ✅ | Identificato bug `saltelli_field()` (ACF non installato → fallback get_post_meta mancante) → fix in helpers.php → re-verify rendering OK su tier-1 + tier-2 + synth + avvocato |
| D8 | Bump version + cache flush + DB dump + REPORT | ✅ | `0.5.0-beta-content` propagata, cache+transient flush, DB dump 57MB salvato in db-dump/ |

---

## 2 · CPT competenza popolati con content reale: **19/19** (100%)

| Tier | Count | Source | Sintetico |
|---|---:|---:|---:|
| Tier-1 (deep) | 3 | 2 (tributario, lavoro) | 1 (lgbtq) |
| Tier-2 (minimal) | 16 | 14 | 2 (assicurazioni, consulenze-online) |
| **Totale** | **19** | **16** (84%) | **3** (16%) |

**Tier-1 (3/3) con FAQ + casi rappresentativi:**
- `diritto-tributario` — 5 FAQ + 2 casi (annullamento 240k€, riforma accertamento sintetico)
- `diritto-del-lavoro` — 5 FAQ + 1 caso (Cassazione licenziamento)
- `diritto-di-famiglia-lgbtq` — 5 FAQ + 1 caso (primo riconoscimento Campania trascrizione integrale 2023) + body_extended synth (2092 char)

**Tier-2 (16/16) con 3 FAQ ciascuno:**
- 14 con post_content da source clean (range 1768-6333 char post-cleanup)
- 2 synth (assicurazioni 1210 char, consulenze-online 1494 char)

---

## 3 · CPT avvocato popolati con bio reale: **4/4** (100%)

| Slug | CPT ID | Bio breve | Bio estesa | Specs | Formazione | Email | Foto |
|---|---:|:---:|:---:|---:|:---:|---|:---:|
| emiliano-saltelli | 2660 | ✅ | ✅ (961 char) | 5 | 2 | info@studiolegalesaltelli.it | ✅ ATT 2683 (preservato) |
| fabiana-saltelli | 2661 | ✅ | ✅ (826 char) | 4 | 1 | fabiana@studiolegalesaltelli.it | ⏳ placeholder (Ludovica DOMANI) |
| antonia-battista | 2662 | ✅ | ✅ (1048 char) | 5 | 1 | antoniabattista@studiolegalesaltelli.it | ⏳ placeholder (Ludovica DOMANI) |
| stefano-gaetano-tedesco | 2663 | ✅ | ✅ (592 char) | 3 | 1 | avv.stefanotedesco@studiolegalesaltelli.it | ⏳ placeholder (Ludovica DOMANI) |

**Note bio:**
- Bio NON estratte dal source page "Chi siamo" (ID 19) perché contenente **Lorem ipsum filler** (vedi recon §1).
- Bio ricostruite da `project-context.json` `team_members` (validato da Duccio + Ludovica) + brief + giurisprudenza nota (caso Antonia LGBTQ+ 2023).
- Tono editoriale "premium law firm 2026", coerente con design tokens locked.
- Email pubbliche da `wp_users.user_email`. Telefoni: tutti `+39 081 1813 1119` (centralino studio — fallback CLAUDE.md project-context).

---

## 4 · Mapping autori blog: **317/326 post** (97.2%) associati a CPT avvocato

| WP user | display_name | post count | CPT avvocato | _wp_author_id meta |
|---:|---|---:|---|:---:|
| 1 | Emiliano Saltelli | **166** | CPT 2660 | ✅ |
| 5 | Avv. Fabiana Saltelli | **99** | CPT 2661 | ✅ |
| 4 | Avv. Antonia Battista | **49** | CPT 2662 | ✅ |
| 7 | Avv. Stefano Gaetano Tedesco | **3** | CPT 2663 | ✅ |
| 6 | Gabriele Cascone | 9 | — (ex-collab) | n/a |
| 8 | Adsolut Staff | 0 | — | n/a |
| 3 | Assistenza Tecnica | 0 | — | n/a |

**Implicazione template `single.php` blog:** può ora linkare l'autore al profilo CPT avvocato via `get_post_meta(<author_post>, '_wp_author_id')`. Reverse lookup possibile con query `meta_query` su CPT.

---

## 5 · CPT senza source page (3) — solo content generato

| CPT slug | CPT ID | Tier | Content |
|---|---:|:--:|---|
| `diritto-di-famiglia-lgbtq` | 2666 | **1** | answer_capsule + synth body 2092 char + 5 FAQ + 1 caso (riconoscimento Campania 2023) — basato su strategia LGBTQ+ + competenza Antonia Battista of-counsel |
| `diritto-delle-assicurazioni` | 2676 | 2 | answer_capsule + synth body 1210 char + 3 FAQ — area marginale, contenuto sobrio (RC auto + polizze vita + ATP procedure) |
| `consulenze-online` | 2681 | 2 | answer_capsule + synth body 1494 char + 3 FAQ — servizio nuovo post-Adsolut, descrive flusso videocall + firma digitale |

**Razionale synth:** per LGBTQ+ — strategy decision validato (vedi `BRIEF_Saltelli_WordPress.md` §strategic_focus_decision Tier-1). Per assicurazioni e consulenze online — copy professionale tono editoriale studio, NO inventate casistiche o numeri.

---

## 6 · Elementor data: nessuna pagina richiede review manuale Elena

**Sorpresa positiva:** lo strip Elementor non è stato necessario in pratica.

- `_elementor_data` (JSON Elementor) era presente come post_meta parallelo, ma **`post_content` conteneva già il rendering finale HTML pulito** (h2/p/ul/li/b/em/a). Il flow editoriale del cliente passava da Elementor builder → frontend, ma WordPress salvava sia il source JSON sia il rendered HTML.
- Funzione `saltelli_clean_source_html()` rimuove rumore residuo (`<img>` absolute URL, `srcset`, `sizes`, `data-elementor-*`, `<p>` vuoti) ma non altera struttura semantica.
- **Nessuna pagina source ha richiesto review manuale.**
- **1 source flaggata come "non usabile":** page ID 19 "Chi siamo" — Lorem ipsum integrale, lasciata fuori dal mapping. Le bio avvocato sono state ricostruite da project-context (vedi §3).

---

## 7 · Esempio prima/dopo CPT — diritto-tributario

### PRIMA (post-Step C, pre-migration)

```
post_title:    "Diritto tributario"
post_content:  "" (vuoto o stub)
chars:         250 (placeholder)

ACF meta:
  is_tier_1_focus    = 1
  answer_capsule     = "Cartelle esattoriali, contenzioso fiscale, accertamenti." (stub identico a lead_breve, 56 char, tema sbagliato — è cartelle non tributario)
  lead_breve         = "Cartelle esattoriali, contenzioso fiscale, accertamenti."
  body_extended      = (vuoto)
  faq                = (vuoto)
  casi_rappresentativi = (vuoto)
```

### DOPO (post-Step D)

```
post_title:    "Diritto tributario"
post_content:  3 527 char di HTML pulito (estratto da page ID 202, post-strip)
               <h2>AVVOCATO TRIBUTARISTA NAPOLI</h2>
               <p>I nostri avvocati potranno inoltre aiutarti…</p>
               <p>Lo Studio Legale Saltelli a Napoli si occupa di…</p>
               <ul>
                 <li>IMU, TARSU, TOSAP, imposta sulla pubblicità…</li>
                 <li>IRPEF, IRES, IRAP…</li>
                 …
               </ul>

ACF meta:
  is_tier_1_focus    = 1
  answer_capsule     = "Lo Studio Legale Saltelli & Partners assiste privati e imprese a
                        Napoli in tutte le materie del diritto tributario: contenzioso,
                        ricorsi su cartelle esattoriali, accertamenti, sanzioni e pianificazione
                        fiscale. La nostra esperienza pluriennale si concentra in particolare
                        su Commissioni Tributarie, IMU, IRPEF e contenzioso INPS. Contatta
                        lo studio per un primo incontro." (371 char ≈ 50 parole — perfect GEO range)
  lead_breve         = "Cartelle esattoriali, contenzioso fiscale, accertamenti tributari
                        per privati e imprese." (88 char, distinto da answer_capsule)
  faq                = 5 entries (1687 char)
  casi_rappresentativi = 2 entries (664 char) — annullamento 240k€ + riforma accertamento sintetico
```

**Rendering live (`http://localhost:8080/competenze/diritto-tributario/`):**
- ✅ `class="sl-competenza--tier-1"` (correctly classified)
- ✅ `<h1>Diritto tributario</h1>`
- ✅ `<p class="sl-competenza__answer">Lo Studio Legale Saltelli &amp; Partners assiste…</p>`
- ✅ Section "Casi rappresentativi" con 2 casi visibili
- ✅ Section "Domande frequenti" con 5 FAQ visibili

---

## 8 · DB dump filename + altre note Step F

**DB dump filename (Step F deploy):**
```
db-dump/saltelli_post-content-migration_20260429-224430.sql
```
- 57 MB
- Generated via `mysqldump -u saltelli -psaltelli_dev saltelli_wp`
- Contiene: 19 CPT competenza popolati + 4 CPT avvocato popolati + author mappings + plugin meta originali
- Gitignored (pattern `db-dump/*.sql` in .gitignore)
- **Questo è il dump da uploadare in produzione in Step F.**

**Altre note per Step E (Template Polish) / Step F (Deploy):**

### Bug fix di processo (D7) — saltelli_field helper

Durante la verifica curl ho identificato un bug latente: l'helper `saltelli_field()` in `inc/helpers.php` ritornava `null` quando ACF non era installato (caso ambiente locale, e potenzialmente prod se ACF Pro non viene mai attivato). Il template non rendeva quindi i campi popolati via `update_post_meta`.

**Fix applicato:** aggiunto fallback `get_post_meta` con rimontaggio righe per repeater ACF-style. Fix retro-compatibile: se ACF è installato, prima usa `get_field`; altrimenti usa post_meta. Rimonta repeater serializzati o style ACF (faq_0_domanda, ecc.).

**File modificato:** `wp-content/themes/saltelli/inc/helpers.php` (saltelli_field + nuova `saltelli_field_repeater_rows`).

### Pulizie residue / decisioni autonome

- **Diritto del lavoro** (tier-1) — source page 292 era piuttosto scarsa (1374 char). Il post_content è stato comunque applicato ma non c'è body_extended synth. **Suggerimento:** in Step E o successiva passata Fabiana, scrivere body_extended 1500+ word per portarla al livello di tributario/lgbtq.
- **Diritto di famiglia (CPT 2669) source = `avvocato-divorzista`** (ID 947, 5106 char). Il source mescola contenuto pratica + landing page. Il content è stato copiato as-is. Eventuale futura review per pulizia.
- **Page IDs 1540 (infortunistica-stradale-italia) e 1558 (avvocato-divorzista-italia)** sono varianti landing page nazionali. Non mappate ai CPT (duplicati delle versioni Napoli). Restano nel DB come pagine standard.
- Gabriele Cascone (ex-collaboratore) ha 9 post ancora associati al suo wp_user ID 6. **Decisione necessaria:** mantenere come autore separato, o riassegnare a un CPT? Lasciato as-is in attesa direttiva.
- **`_thumbnail_id` Emiliano (2683)**: VERIFICATO post-migration — ancora intatto. Lo script `migrate-content.php` non scrive mai `foto_ritratto`/`_thumbnail_id` per nessun avvocato.

### Files migration (NON ship to prod)

```
db-dump/migrate-content.php     — script PHP eval-file (audit trail)
db-dump/migration-data.json     — config data (audit trail, contiene tutto il body synth)
db-dump/saltelli_post-content-migration_20260429-224430.sql — DB snapshot (gitignored)
```

`db-dump/` è gitignored solo per `*.sql*`. I file `.php` e `.json` sono trackable se Duccio decide di committarli come audit trail. Suggerimento: estendere `.gitignore` con `db-dump/migrate-content.php` `db-dump/migration-data.json` se preferisce repo pulito.

### Blocker per Step E (Template Polish)

- **Foto Fabiana, Antonia, Stefano** — dipendenza esterna Ludovica DOMANI. Step E può procedere su template polish senza foto reali, ma il rendering finale beneficia delle foto.
- **Nessun altro blocker introdotto.** Tutti i CPT renderizzano correttamente. Lo bug helper è stato fixato.

### Blocker per Step F (Deploy)

- DB dump prontissimo da uploadare.
- Servono credenziali SSH/FTP cliente per copiare il dump in produzione.
- Yoast SEO è già attivo nel DB importato → coerenza con setup remoto.

---

**Files changed in Step D:**
```
M wp-content/themes/saltelli/inc/helpers.php           (saltelli_field fix)
M wp-content/themes/saltelli/style.css                 (Version bump)
M wp-content/themes/saltelli/functions.php             (SALTELLI_THEME_VERSION bump)
?? .claude/knowledge/design/sessione-1/reports/content-migration/  (recon, mapping, REPORT)
?? db-dump/                                            (migration artifacts + DB dump, gitignored)
```

**Database changes (per CPT):**
- 19 CPT competenza: post_content + 4-6 meta keys per CPT (5-23 entries inclusi serialized repeater rows)
- 4 CPT avvocato: post_content (bio_estesa) + 8-10 meta keys per CPT
- 4 _wp_author_id meta per author mapping
- 1 _thumbnail_id meta (su 2660) **preservato** — NON modificato da migration

---

*Step D completato. v0.5.0-beta-content pronta per ispezione visiva di Duccio + Step E (Template Polish CPT individuali) o Step F (Deploy). Mi fermo qui, in attesa istruzioni.*
