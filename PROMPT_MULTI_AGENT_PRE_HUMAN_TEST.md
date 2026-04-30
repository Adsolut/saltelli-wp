# Prompt — Multi-Agent UX/UI Pre-Human-Test Audit

> **Per Claude Code in 3 sessioni parallele (o sequenziali se preferito).** Ogni agente lavora indipendentemente sul sito live `https://staging.studiolegalesaltelli.it` con focus diverso. L'orchestrator (Claude in chat) fonderà i 3 report in un single dossier prima del feedback umano.
> **Tempo stimato:** 30-40 min per agent in parallelo · ~50 min in sequenza.

---

## Contesto

Il sito Saltelli è **live in staging** all'URL https://staging.studiolegalesaltelli.it. Tutti gli URL ritornano HTTP 200, schema markup valido, breadcrumb consistent.

Prima di passare al **feedback umano del team interno** (Duccio + Elena + Ludovica) per il sign-off finale, vogliamo un **audit AI parallelo** che identifichi gap UX/UI residui che un occhio umano potrebbe notare ma che noi vogliamo fissare prima.

3 agent → 3 punti di vista → 1 dossier consolidato.

---

## Setup orchestrazione

Lancia 3 sessioni di Claude Code separate (consigliato: 3 pane tmux o 3 finestre Claude Code). Ad ognuna passa il prompt corrispondente sotto.

```
┌─────────────────┬─────────────────┬─────────────────┐
│  AGENT A        │  AGENT B        │  AGENT C        │
│  UX Critic      │  UI Designer    │  Conversion+SEO │
│                 │                 │                 │
│  Focus:         │  Focus:         │  Focus:         │
│  - User flow    │  - Typography   │  - CTA          │
│  - Friction     │  - Spacing      │  - Forms        │
│  - Navigation   │  - Hierarchy    │  - Schema       │
│  - Content IA   │  - Components   │  - A11y/WCAG    │
│  - Dead-ends    │  - Consistency  │  - Performance  │
└─────────────────┴─────────────────┴─────────────────┘
                         ↓
            Orchestrator merge → REPORT-PRE-HUMAN.md
```

Ognuno scrive il report in path diverso per evitare conflict:
- `.claude/knowledge/design/sessione-1/reports/pre-human-test/REPORT-A-UX.md`
- `.claude/knowledge/design/sessione-1/reports/pre-human-test/REPORT-B-UI.md`
- `.claude/knowledge/design/sessione-1/reports/pre-human-test/REPORT-C-CONV-SEO.md`

---

## Hard rules (per tutti e 3 gli agent)

| Rule | Reason |
|---|---|
| **NESSUNA modifica al codice.** Solo lettura + report | Audit puro, fix decide orchestrator |
| Lavora su **sito live** `https://staging.studiolegalesaltelli.it` | Ambiente reale + WAF/cache attivi |
| Cita **URL specifici** + screenshot path se possibile | Riproducibilità |
| Severità issue: **P0/P1/P2/P3** chiara | Decisione fix prioritizzazione |
| Max **20 issue per agent** (no infinite list) | Quality > quantity |
| Riferiscati a `audit-cro-studiolegalesaltelli.md` (project knowledge) | Cross-check con audit precedente |
| Niente PHP/CSS edit, niente smoke test, niente git push | Read-only |
| Non eseguire `wp` o `docker compose` localmente | Sito è live, non locale |

---

## ═══════════════════════════════════════════════════════
## AGENT A — UX Critic
## ═══════════════════════════════════════════════════════

> Tu sei un **UX critic indipendente**. Esamini il sito come farebbe un utente reale (un cliente potenziale di uno studio legale, NON un addetto ai lavori) e annoti tutto quello che sembra "fuori posto" funzionalmente.

### Letture obbligatorie

1. `audit-cro-studiolegalesaltelli.md` (project knowledge) — sezioni 9 (Information Architecture) + 3 (CRO Strategy)
2. `BRIEF_Saltelli_WordPress.md` — il brief originale del cliente

### Walkthrough da eseguire

Naviga il sito **come un utente comune** seguendo questi 5 user journey. Per ogni journey annota friction points, click sprecati, dead-ends, content non chiaro.

**Journey 1 — "Ho ricevuto una cartella esattoriale, mi serve un avvocato a Napoli"**
1. Atterri su homepage
2. Cerchi se lo studio ha questa expertise
3. Trovi info sulle pratiche, costi, contatto

→ Quanti click ti servono? Trovi info chiare? Ci sono dead-end?

**Journey 2 — "Voglio sapere chi è l'avvocato Emiliano Saltelli"**
1. Da homepage → Avvocati → Emiliano
2. Verifica completezza profilo (foto, bio, formazione, contatto diretto)

**Journey 3 — "Mi separo, voglio capire come funziona la prima consulenza"**
1. Cerca info famiglia/divorzi
2. Trova /costi/ → leggi prima consulenza
3. Decide se prenotare

**Journey 4 — "Cerco un articolo blog su una sentenza recente"**
1. Da homepage trova /blog/ (Editoriale)
2. Naviga categorie/post
3. Apri singolo post + leggi

**Journey 5 — "Sono SEO bot / Google AI Overview"**
1. Verifica `/llms.txt`, `/robots.txt`
2. Verifica schema markup su 4 pagine chiave
3. Verifica navigability (breadcrumb, internal linking)

### Issue da cercare attivamente

- ✦ Friction points (campo form non auto-focus, click invisibili, hover state assenti)
- ✦ Navigation che obbliga il back button (dead-ends)
- ✦ CTA che portano a pagine wrong/incomplete (es. "Prenota" → /contatti/ ma /contatti/ non ha form?)
- ✦ Content hierarchy logica vs visiva mismatch
- ✦ Empty states (es. avvocato senza casi, area senza FAQ)
- ✦ Microcopy ambigua (button label generici "Invia" vs specifici "Prenota consulenza gratuita")
- ✦ Loading state assenti
- ✦ Search interno funziona/manca?
- ✦ Mobile journey friction
- ✦ Tab order tastiera
- ✦ Link "morti" (404, redirect chain)

### Output

`REPORT-A-UX.md`:
```
# Agent A · UX Critic — Pre-Human-Test Audit

## Score globale UX: X/10

## Issue trovati (max 20, severità P0-P3)

### P0 — Bloccanti
1. [URL specifico] · descrizione issue · impatto utente · fix suggerito (high-level)

### P1 — Importanti
...

### P2 — Polish
...

### P3 — Nice-to-have
...

## User journey breakdown
- Journey 1: X click necessari (target ≤3) · Y friction points
- Journey 2: ...
- Journey 3: ...
- Journey 4: ...
- Journey 5: ...

## Top 3 raccomandazioni strategiche
1. ...
2. ...
3. ...
```

---

## ═══════════════════════════════════════════════════════
## AGENT B — UI Designer
## ═══════════════════════════════════════════════════════

> Tu sei un **UI/UX designer senior** che valuta visual quality del sito post-build. Confronti con i reference del design system Claude Sessione 1 + Brand Adsolut.

### Letture obbligatorie

1. `.claude/knowledge/design/sessione-1/homepage-desktop.jsx` — riferimento Claude Design
2. `.claude/knowledge/design/sessione-1/tokens.css` — design tokens locked
3. `audit-cro-studiolegalesaltelli.md` — sezione 2 (UI Design Review)

### Walkthrough da eseguire

Esamina **rendering visivo** di queste 12 pagine sul sito live (apri in browser, prendi nota a occhio):

```
Desktop 1440×900:
1. /                                  Homepage
2. /chi-siamo/                        About
3. /avvocati/                         Team archive
4. /avvocati/emiliano-saltelli/       Single con foto reale
5. /avvocati/fabiana-saltelli/        Single con placeholder
6. /competenze/                       Practice areas archive
7. /competenze/diritto-tributario/    Tier-1 deep
8. /competenze/recupero-crediti/      Tier-2 minimal (post-redirect)
9. /casi/                             Cases page
10. /costi/                           Pricing
11. /tipo-area/privati/               Taxonomy
12. /intimazione-tari-annullata-...   Single blog post

Mobile 375×812 (DevTools mobile view):
- Homepage + 3 random
```

### Issue da cercare attivamente

#### Typography
- ✦ Font-size hierarchy chiara H1 > H2 > H3 > body
- ✦ Line-height adeguato (1.5-1.75 body, 1.1-1.2 display)
- ✦ Letter-spacing display fonts professionale
- ✦ Italic vs bold uso editoriale corretto
- ✦ Drop-cap dove serve (single post body)
- ✦ Line-length max 65-75ch leggibilità

#### Spacing
- ✦ Whitespace verticale tra sezioni 80-128px
- ✦ Padding interno content adeguato
- ✦ Gap tra elementi correlati < gap tra elementi distinti (proximity)
- ✦ Margin-bottom heading consistente

#### Color
- ✦ Contrast ratio WCAG AA su body text
- ✦ Accent color (bronze) usato con parsimonia
- ✦ Hover states visibili
- ✦ Link color distinguibile da body

#### Hierarchy
- ✦ Visual weight: primario (h1) >> secondario (h2/h3) >> body
- ✦ CTA primary visible >>> secondary
- ✦ Scanning path naturale (F-pattern o Z-pattern)

#### Components
- ✦ Button consistency (.sl-btn ovunque?)
- ✦ Form fields styling (se presenti)
- ✦ Card/list pattern coerenti
- ✦ Footer consistency cross-page
- ✦ Header sticky behavior

#### Reference Claude Design check
- ✦ Hero h1 "Diritto, con misura." su 3 righe
- ✦ Lede italic Playfair sotto h1
- ✦ Colophon a destra (Indirizzo/Orari/Contatti)
- ✦ § 02 Lo studio drop-cap "L"
- ✦ § 03 Avvocati 4 layout asimmetrico
- ✦ § 04 Casi tipografico (id mono / desc italic / outcome bronze)

### Output

`REPORT-B-UI.md`:
```
# Agent B · UI Designer — Pre-Human-Test Audit

## Score globale UI: X/10

## Issue trovati (max 20, severità P0-P3)

[same structure as Agent A]

## Comparison Claude Design vs Live
- Hero fidelity: X/10
- Sections fidelity: X/10
- Typography fidelity: X/10
- Component consistency: X/10

## Top 3 raccomandazioni strategiche
```

---

## ═══════════════════════════════════════════════════════
## AGENT C — Conversion + SEO + Accessibility
## ═══════════════════════════════════════════════════════

> Tu sei un **CRO + technical SEO specialist + accessibility auditor**. Verifichi che il sito converta, sia indicizzabile, sia accessibile.

### Letture obbligatorie

1. `audit-cro-studiolegalesaltelli.md` — tutto, in particolare 3 (CRO), 8 (Agentic Search), 12 (Mobile UX)
2. `GEO_Audit__Studio_Legale_Saltelli_Napoli...md` — requirement GEO

### Walkthrough da eseguire

#### Conversion
1. Identifica TUTTI i CTA del sito (cross-page)
2. Verifica conversion path: ogni CTA porta a un'azione concreta?
3. Forms presenti? Funzionanti? Field UX? Privacy disclosure?
4. WhatsApp / Phone click-to-call attivi?
5. "Prima consulenza gratuita" gancio: dove visibile? quanto prominente?
6. Trust signals: foto reali, casi, recensioni, badge ordine

#### SEO
1. Schema JSON-LD validation (4 URL chiave: home, single competenza, single avvocato, blog post)
   - Validate via `https://validator.schema.org/`
   - Confirm tipo: Organization + LegalService + Person/Attorney + FAQPage + BreadcrumbList + Article
2. Meta description coverage (Yoast attivo?) — sample 5 pagine random
3. Open Graph tags presenti?
4. Heading hierarchy logica (H1=1, H2/H3 progressivi)
5. Internal linking density (audit CRO 9.8): cross-link tra competenze correlate?
6. `/llms.txt` content rilevante per AI crawlers
7. `/robots.txt` autorizza GPTBot, ClaudeBot, PerplexityBot
8. Sitemap XML accessibile? Indicizzabile?
9. Canonical URL corretti (no duplicate)

#### Performance
1. Lighthouse mobile + desktop su 3 URL chiave (home + single competenza + single avvocato)
   - Performance ≥ 92
   - Accessibility ≥ 95
   - Best Practices ≥ 95
   - SEO ≥ 95
2. Core Web Vitals (LCP, INP, CLS)
3. Font loading strategy (Google Fonts vs WOFF2 self-hosted)
4. Image optimization (lazy loading, dimensions)

#### Accessibility (WCAG 2.1 AA)
1. Skip-link funzionante
2. Focus-visible su tutti i interactive elements
3. Alt text immagini sample 5 random
4. Form labels associate
5. ARIA landmarks (`<nav>`, `<main>`, `<aside>`)
6. Touch target ≥ 48×48 mobile
7. Color contrast su text (use Chrome DevTools Lighthouse a11y report)

### Issue da cercare attivamente

- ✦ CTA generic "Invia" / "Contattaci" senza urgency
- ✦ Form senza privacy disclosure GDPR
- ✦ Trust signal mancanti (foto reali tutti gli avvocati, badge Ordine)
- ✦ Schema markup invalid o incompleto
- ✦ Meta description duplicate/mancanti
- ✦ Heading hierarchy salti (H1 → H3 senza H2)
- ✦ Lighthouse score < target su perf/a11y
- ✦ Image senza alt text
- ✦ Touch target mobile < 48px
- ✦ Console errors (verifica Chrome DevTools console)
- ✦ HTTPS mixed-content warnings
- ✦ Cache headers efficienti?

### Output

`REPORT-C-CONV-SEO.md`:
```
# Agent C · Conversion + SEO + A11y — Pre-Human-Test Audit

## Score globale: X/10

## Issue trovati (max 20, severità P0-P3)

[same structure]

## Lighthouse scores (3 URL × 2 device = 6 reports)
- Home desktop: Perf X · A11y X · BP X · SEO X
- Home mobile: ...
- Single competenza desktop: ...
- ...

## Schema validation
- /                           : ✓/✗ tipi: ...
- /competenze/diritto-tributario/ : ...
- /avvocati/emiliano-saltelli/ : ...
- /casi/                       : ...

## Conversion funnel score
- Homepage hero: X/10
- Practice area: X/10
- Single avvocato: X/10
- /costi/: X/10

## Top 3 raccomandazioni strategiche
```

---

## ═══════════════════════════════════════════════════════
## ORCHESTRATOR — Merge dei 3 report
## ═══════════════════════════════════════════════════════

Quando tutti e 3 gli agent hanno consegnato i loro report, l'orchestrator (Claude in chat) eseguirà:

1. Lettura dei 3 report
2. **Deduplica issue** (stesso bug visto da prospettive diverse)
3. **Severity reconciliation** (un agent dice P1, l'altro P0 → escalate)
4. **Categorize** per impatto: Quick-Win / High-Impact-Low-Effort / Strategico-Long-Term
5. Genera `REPORT-PRE-HUMAN-CONSOLIDATED.md` con max 25 issue prioritizzati
6. Confronto con audit CRO originale: quali issue del vecchio sito sono stati replicati per errore? Quali eliminati?

Output finale: dossier che Duccio può usare per:
- Validare/integrare con feedback team interno (Elena, Ludovica)
- Decidere fix immediati (pre-deploy production) vs fix iterativi (post-deploy)
- Comunicare al cliente (Avv. Saltelli) cosa è stato fatto e cosa resta

---

## Note operative

### Se Chrome MCP è offline
Tutti e 3 gli agent fanno **audit programmatico**:
- curl + Python parser per DOM analysis
- Lighthouse via `npx lighthouse <url> --only-categories=performance,accessibility,best-practices,seo --output=html --output-path=...` (richiede node disponibile)
- Schema validation via `https://validator.schema.org/` URL test (manual call con curl POST)

### Se Chrome MCP è online
Visual verification + screenshot per ogni issue P0/P1.

### Sequenza vs parallelo
Se Duccio lancia in parallelo (3 sessioni), ogni agent lavora indipendente. Output merge dopo.
Se sequenziale (1 sessione), agent A → B → C in fila, ognuno consapevole degli output precedenti.

---

*v1.0 — Multi-Agent Pre-Human-Test Audit. Orchestrator: Claude (chat). Output: dossier consolidato 25 issue prioritizzati pre-feedback umano team interno.*
