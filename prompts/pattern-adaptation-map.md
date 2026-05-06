# Pattern Adaptation Map — Studio Legale Saltelli

**Versione**: v1
**Riferimento**: DEC-019 (Wave 6 lean — pattern adaptation senza Sessione 3 Claude Design)
**Audience**: Claude Code (per implementazione Wave 6) + orchestratore (per audit)
**Data**: 06/05/2026

---

## Premessa

L'audit firmato Aprile 2026 + il Friction Points & CRO Patterns analysis identificano **17 acceptance criteria** per Fase 3. Di questi, una parte è già coperta dal DS MVP esistente (breadcrumb, accordion, attorney sticky CTA, eyebrow caption, drop-cap, lede italic). Altri 8-10 pattern friction-points-driven NON sono presenti né nel DS originale Sessione 1+2 di Claude Design né nel MVP corrente.

DEC-019 stabilisce che **non si commissiona una Sessione 3 di Claude Design**. La Wave 6 viene eseguita adattando componenti esistenti del DS Sessione 1+2, accettando la perdita di precisione visiva su pattern di nicchia.

Questo documento è il **mapping operativo** che:
1. Per ogni pattern friction-points-driven mancante, identifica il/i componente/i del DS esistente da adattare
2. Specifica ACF Field (esistenti o da estendere), template-part PHP (esistenti o nuovi), CSS (estensione `assets/css/components/cro.css`)
3. Annota i trade-off accettati per ogni pattern
4. Serve come input diretto per il prompt strutturato di Claude Code Wave 6 (in `_shared/prompt-library/claude-code/wave6-extension-blocks.md`)

---

## Componenti DS già esistenti (riferimento per adaptation)

| Componente | Selettore CSS | Source | Note |
|---|---|---|---|
| Button primary filled | `.sl-btn--primary` | MVP `tokens.css` + `components.css` | Drift accettato vs DS originale (DEC-018), navy bg + cream text + bronze hover |
| Button ghost | `.sl-btn--ghost` | MVP | Transparent + hairline border-bottom |
| Eyebrow / mono caption | `.sl-mono` | DS Sessione 1 + MVP | JetBrains Mono +0.08em uppercase |
| Breadcrumb | `.sl-page__breadcrumb` | MVP | Mono caption + bronze hover |
| Drop-cap §02 | `.sl-studio__prose p:first-of-type::first-letter` | MVP | Playfair 84px bronze, solo desktop |
| Lede italic | `.sl-hero__subheadline`, `.sl-post__lede`, `.sl-competenza__lede` | MVP | Playfair italic 22px navy |
| Accordion FAQ | `.sl-acc` + `details > summary` | DS Sessione 1 + MVP | Hairline border + chevron `+` rotate 45deg = `×` |
| Tag pill | `.sl-tag` | MVP | 1px hairline, transparent, no fill |
| Card case | `.sl-cases__row` | MVP | Grid 200/1fr/200 (id mono / desc Playfair italic / outcome bronze) |
| Area row | `.sl-area` | DS Sessione 1 + MVP | Grid 64/1fr/auto (numero / titolo / meta), hover translateX 8px + bordo bronzo |
| Area row tier-1 modifier | `.sl-area--tier1` | DS Sessione 1 | First-letter title bronze, numero stella `★ ` bronze |
| Sticky attorney CTA | `.sl-attorney__sticky` | MVP | Solo single-avvocato.php, bottom 32px right 32px desktop |
| WhatsApp sticky mobile | `.sl-whatsapp-sticky` | MVP | Brand fidelity #25D366 + box-shadow brand-tinted |
| Pill filter | `.sl-pill` | DS Sessione 1 (in `styles/tokens.css`) | NON ancora implementato in MVP |
| Hairline rule | `.sl-rule` | DS Sessione 1 (in `styles/tokens.css`) | Ornament hairline 1px |
| Placeholder striped | `.sl-placeholder` | DS Sessione 1 (in `styles/tokens.css`) | Striped grayscale per immagini mancanti |
| Hero word reveal animation | `.sl-word` + `@keyframes sl-rise` | DS Sessione 1 (in `styles/tokens.css`) | NON ancora implementato in MVP, da aggiungere in Wave 6 |
| Section fade-in | `.sl-reveal` | DS Sessione 1 (in `styles/tokens.css`) | NON ancora implementato in MVP |

---

## Pattern Adaptation — 10 pattern (8 friction-points + 2 enrichment)

### Pattern 1 — Answer capsule (40-60 parole)

**Friction-points reference**: FP1.1 — answer capsule come prima risposta GEO sopra il fold di ogni pagina query-driven.

**Componente DS adattato**:
- Lede italic Playfair (`.sl-competenza__lede`) — pattern già esistente per single-competenza
- Mono eyebrow `.sl-mono` per label "RISPOSTA RAPIDA · 2 min lettura"
- Spacing `var(--s-6)` (48px) sotto

**ACF Field (estensione)**:
- `group_competenza_v1` aggiungere field `answer_capsule`:
  - Type: `textarea`
  - Maxlength: 400 char (~ 60 parole italiano)
  - Required: per Tier-1 deep, optional per Tier-2 lighter
  - Helper text: "Risposta diretta alla query target, 40-60 parole. Esempio: 'Lo Studio si occupa di diritto tributario in Napoli da 20+ anni, con focus su contenziosi con l'Agenzia delle Entrate, cartelle esattoriali e ricorsi. Prima consulenza in 3-5 giorni lavorativi.'"

**Template-part**:
- Estendere `single-competenza.php`: dopo `<h1>`, aggiungere `<div class="sl-answer-capsule">`
- Eyebrow `<p class="sl-mono">RISPOSTA RAPIDA · 2 min lettura</p>`
- Body `<p class="sl-competenza__lede">{answer_capsule}</p>` (Playfair italic 22px)

**CSS** (`assets/css/components/cro.css`):
```css
.sl-answer-capsule {
  margin: var(--s-5) 0 var(--s-6) 0;
  padding-bottom: var(--s-5);
  border-bottom: 1px solid var(--border);
  max-width: 60ch;
}
.sl-answer-capsule .sl-mono {
  margin-bottom: var(--s-3);
}
```

**Trade-off**: nessun layout "card" elaborato. Solo lede italic + eyebrow mono. Coerente con register editoriale del MVP.

**Schema impact**: l'answer capsule alimenta il primo `<p>` dopo `<h1>`, che è quello che gli AI engine (Perplexity, ChatGPT, Google AI Overviews) usano come quote-of-page. Massimizza Share of Answer.

---

### Pattern 2 — Trust bar globale (4 segnali credibilità)

**Friction-points reference**: FP3.2 — trust bar inline in homepage + pagine alta-conversion (single-competenza Tier-1, /costi/).

**Componente DS adattato**:
- `.sl-mono` (eyebrow JetBrains Mono) per ciascun segnale
- `.sl-rule` hairline 1px come separator orizzontale
- 4 colonne grid desktop, 2x2 grid tablet, vertical mobile

**ACF Field (riuso)**:
- Theme Options tab "Brand" aggiungere field `trust_bar_signals`:
  - Type: ACF Free non ha Repeater, quindi pattern fake-repeater con CPT modulare oppure semplicemente 4 field flat:
  - `trust_signal_1_label` (text, default "20+ ANNI")
  - `trust_signal_1_caption` (text, default "ESPERIENZA")
  - `trust_signal_2_label` (text, default "4 AVVOCATI")
  - `trust_signal_2_caption` (text, default "TEAM SPECIALIZZATO")
  - `trust_signal_3_label` (text, default "19 AREE")
  - `trust_signal_3_caption` (text, default "DI PRATICA")
  - `trust_signal_4_label` (text, default "COA FAMIGLIA")
  - `trust_signal_4_caption` (text, default "MUNICIPALITÀ 1")

**Template-part**:
- Nuovo `template-parts/trust-bar.php` riusabile
- Include hook in `front-page.php`, `template-parts/page-costi.php`, `single-competenza.php` (solo Tier-1)

**CSS** (`assets/css/components/cro.css`):
```css
.sl-trust-bar {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: var(--s-5);
  padding: var(--s-6) 0;
  border-top: 1px solid var(--border);
  border-bottom: 1px solid var(--border);
}
.sl-trust-bar__item {
  text-align: center;
}
.sl-trust-bar__label {
  font-family: var(--font-display);
  font-size: clamp(28px, 3vw, 40px);
  color: var(--primary);
  margin-bottom: var(--s-2);
}
.sl-trust-bar__caption {
  font-family: var(--font-mono);
  font-size: 11px;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--text-muted);
}
@media (max-width: 768px) {
  .sl-trust-bar { grid-template-columns: 1fr 1fr; }
}
@media (max-width: 480px) {
  .sl-trust-bar { grid-template-columns: 1fr; }
}
```

**Trade-off**: niente icone, solo numeri/testo. Coerente con anti-stock-icon hard rule del MVP.

---

### Pattern 3 — Sticky bottom bar mobile a 3 azioni

**Friction-points reference**: FP4.1 — sticky bottom mobile per ridurre attrito conversione su tutte le pagine eccetto /contatti/.

**Componente DS adattato**:
- Estensione di `.sl-attorney__sticky` (esistente) + `.sl-whatsapp-sticky` (esistente)
- Nuovo componente unificato `.sl-mobile-bar` con 3 icone + label

**ACF Field (riuso)**:
- Theme Options tab "Studio Info" già contiene `phone_e164`, `whatsapp_e164`, `email`
- Theme Options tab "CTA Defaults" `cta_label` (default "Prenota un primo incontro") già esistente
- Niente nuovi field richiesti

**Template-part**:
- Nuovo `template-parts/mobile-sticky-bar.php` riusabile
- Include hook in `footer.php` (sotto WP `wp_footer()` per ottimizzare LCP)
- Solo mobile (`@media (max-width: 768px)`)
- Hidden quando `body.single-avvocato` (perché `.sl-attorney__sticky` già attivo) + `body.page-template-page-contatti` (form già presente)

**CSS** (`assets/css/components/cro.css`):
```css
.sl-mobile-bar {
  display: none;
}
@media (max-width: 768px) {
  body:not(.single-avvocato):not(.page-template-page-contatti) .sl-mobile-bar {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: var(--background);
    border-top: 1px solid var(--border);
    z-index: 50;
    box-shadow: 0 -4px 12px rgba(27, 43, 75, 0.06);
  }
}
.sl-mobile-bar__action {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: var(--s-3) var(--s-2);
  text-decoration: none;
  color: var(--primary);
  font-family: var(--font-mono);
  font-size: 10px;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  border-right: 1px solid var(--border);
}
.sl-mobile-bar__action:last-child { border-right: none; }
.sl-mobile-bar__action:active { background: var(--surface); }
.sl-mobile-bar__icon {
  font-size: 18px;
  margin-bottom: 4px;
}
```

**HTML structure**:
```html
<aside class="sl-mobile-bar" aria-label="Contatti rapidi">
  <a href="tel:{phone_e164}" class="sl-mobile-bar__action">
    <span class="sl-mobile-bar__icon">☎</span>
    Chiama
  </a>
  <a href="https://wa.me/{whatsapp_e164}" class="sl-mobile-bar__action">
    <span class="sl-mobile-bar__icon">WA</span>
    WhatsApp
  </a>
  <a href="/contatti/" class="sl-mobile-bar__action">
    <span class="sl-mobile-bar__icon">✎</span>
    Scrivi
  </a>
</aside>
```

**Trade-off**: niente icone SVG custom (UTF-8 glyph come ☎ ✎ + label "WA" testo). Niente expand drawer, niente notification badge. Coerente con minimalismo editoriale.

**Accessibility**: `aria-label="Contatti rapidi"`, `tel:` + `https://wa.me/` link standard, focus-visible outline ereditato da global.

---

### Pattern 4 — Inline mini-form contestuale

**Friction-points reference**: FP2.1 — mini-form inline su single-competenza Tier-1 e /costi/ per ridurre attrito.

**Componente DS adattato**:
- Estrazione del form CF7 esistente in `/contatti/` come template-part `mini-form.php`
- Versione "ridotta": campo nome + email + topic (select da tassonomia `topic` o area pratica) + messaggio (textarea max 200 char) + submit
- Reuse stile form CF7 del MVP

**ACF Field**:
- Niente nuovi field. Il form ID CF7 è hardcoded nel template-part, configurabile via filter se serve

**Template-part**:
- Nuovo `template-parts/mini-form.php`
- Accetta parametro `$args['topic_default']` (slug area pratica corrente per pre-fill select)
- Include hook in `single-competenza.php` (sotto FAQ section, prima di related services)
- Include hook in `template-parts/page-costi.php` (sezione bottom)

**CSS** (`assets/css/components/cro.css`):
```css
.sl-mini-form {
  margin: var(--s-7) 0;
  padding: var(--s-6);
  background: var(--surface);
  border: 1px solid var(--border);
  max-width: 720px;
}
.sl-mini-form__title {
  font-family: var(--font-display);
  font-size: clamp(24px, 2.5vw, 32px);
  color: var(--primary);
  margin-bottom: var(--s-2);
}
.sl-mini-form__lede {
  font-family: var(--font-display);
  font-style: italic;
  font-size: 17px;
  color: var(--text-muted);
  margin-bottom: var(--s-5);
  max-width: 50ch;
}
.sl-mini-form input,
.sl-mini-form select,
.sl-mini-form textarea {
  width: 100%;
  padding: var(--s-3) var(--s-3);
  background: var(--background);
  border: 1px solid var(--border);
  border-radius: 0;
  font-family: var(--font-body);
  font-size: 16px;
  color: var(--text);
  margin-bottom: var(--s-3);
}
.sl-mini-form input:focus,
.sl-mini-form select:focus,
.sl-mini-form textarea:focus {
  outline: 2px solid var(--accent);
  outline-offset: 2px;
  border-color: var(--primary);
}
.sl-mini-form button[type="submit"] {
  /* riuso .sl-btn--primary */
}
```

**Honeypot integration**: usa Honeypot for CF7 2.3 (già nel MVP).

**Brevo SMTP relay**: il form invia notifica al cliente via Brevo SMTP transactional (DEC-009).

**Trade-off**: niente conditional logic (Gravity Forms-style), niente file upload, niente multi-step. Solo form base 4-field.

---

### Pattern 5 — FAQ block con FAQPage schema

**Friction-points reference**: FP1.2 — FAQ block strutturato su ogni single-competenza Tier-1 + Tier-2 (3-5 domande/risposte) + schema FAQPage.

**Componente DS adattato**:
- `.sl-acc` (accordion esistente) + `inc/schema/partial-faqpage.php` (schema esistente)
- Già funzionante su `/faq/` e single-competenza Tier-1

**Cosa cambia in Wave 6**: generalizzazione

**ACF Field (riuso)**:
- `group_competenza_v1` ha già relationship field `faq_associate` (post_object) per linkare 3-5 `saltelli_faq` items
- Niente nuovi field

**Template-part**:
- `single-competenza.php` rendere il blocco FAQ visibile **anche per Tier-2** (oggi solo Tier-1) se `faq_associate` non vuoto
- Include hook a `partial-faqpage.php` se FAQ presenti (Yoast NON emette FAQPage schema, quindi nessun duplicato)

**CSS**: nessun cambio. Riuso `.sl-acc`.

**Schema impact**: estende la copertura FAQPage da ~3 pagine (oggi /faq/ + 3 Tier-1) a potenzialmente 19 (tutte le competenze) — moltiplica le opportunità di featured snippet AI.

**Trade-off**: nessuno. Pattern già implementato, generalizzazione.

---

### Pattern 6 — Testimonials block

**Friction-points reference**: FP3.1 — testimonianze cliente come trust signal su homepage + single-avvocato.

**Componente DS adattato**:
- `saltelli_trust` × 4 (CPT esistente, oggi solo numerici tipo "20+ anni esperienza")
- Estensione del CPT con field per testimonianza testuale
- Visualizzazione con `.sl-mono` per metadata + Playfair italic per testimonianza + em-dash per attribuzione

**ACF Field (estensione)**:
- `group_trust_item_v1` aggiungere field:
  - `testimonial_type` (radio: "Numero" / "Testimonianza")
  - `testimonial_text` (textarea, condizionale su type=Testimonianza, max 280 char)
  - `testimonial_author` (text, condizionale)
  - `testimonial_city` (text, condizionale, default "Napoli")
  - `testimonial_topic` (select tassonomia `topic`)

**Template-part**:
- Nuovo `template-parts/testimonials-block.php`
- Pattern: 3 testimonianze in carousel statico (no JS slider — solo grid 3-col desktop, 1-col mobile)
- Include hook opzionale in `front-page.php` (sezione middle)
- Loop su `saltelli_trust` con `meta_query` `testimonial_type='Testimonianza'`

**CSS** (`assets/css/components/cro.css`):
```css
.sl-testimonials {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: var(--s-6);
  padding: var(--s-7) 0;
}
.sl-testimonial {
  display: flex;
  flex-direction: column;
  gap: var(--s-4);
}
.sl-testimonial__topic {
  font-family: var(--font-mono);
  font-size: 11px;
  letter-spacing: 0.08em;
  text-transform: uppercase;
  color: var(--text-muted);
}
.sl-testimonial__quote {
  font-family: var(--font-display);
  font-style: italic;
  font-size: 22px;
  line-height: 1.5;
  color: var(--primary);
  position: relative;
  padding-left: var(--s-3);
  border-left: 2px solid var(--accent);
}
.sl-testimonial__attribution {
  font-family: var(--font-body);
  font-size: 14px;
  color: var(--text-muted);
}
.sl-testimonial__attribution::before {
  content: "— ";
  color: var(--accent);
}
@media (max-width: 768px) {
  .sl-testimonials { grid-template-columns: 1fr; }
}
```

**Trade-off DEC-019**:
- ❌ Nessuna foto cliente (privacy GDPR + brand editoriale)
- ❌ Nessun rating star (anti-stock-photo hard rule)
- ❌ Nessun carousel JS (solo grid statico)
- ✅ Solo testo + nome + città + topic + Playfair italic con bordo bronzo

---

### Pattern 7 — Statistic-with-source

**Friction-points reference**: FP3.3 — statistiche/dati supportati da fonte autorevole su homepage e single-competenza Tier-1.

**Componente DS adattato**:
- Estensione del pattern Trust signal `saltelli_trust` (CPT esistente) con field `source` aggiunto
- Visualizzazione con `.sl-mono` micro-source citation

**ACF Field (estensione)**:
- `group_trust_item_v1` aggiungere field:
  - `source_label` (text, opzionale, default "Fonte")
  - `source_text` (text, es. "Cassa Forense, 2024", "Audit GEO Adsolut, Apr 2026")
  - `source_url` (url, opzionale)

**Template-part**:
- Nessun nuovo template-part dedicato. Riuso del pattern Trust bar (Pattern 2) con varianti
- Estensione di `template-parts/trust-bar.php`: ogni signal accetta opzionalmente `source` field

**CSS** (`assets/css/components/cro.css`):
```css
.sl-trust-bar__source {
  font-family: var(--font-mono);
  font-size: 9px;
  letter-spacing: 0.05em;
  text-transform: uppercase;
  color: var(--text-muted);
  margin-top: var(--s-2);
  opacity: 0.7;
}
.sl-trust-bar__source a {
  border-bottom: 1px solid var(--border);
  transition: border-color var(--dur-base) var(--ease-editorial);
}
.sl-trust-bar__source a:hover {
  border-color: var(--accent);
  color: var(--accent);
}
```

**Trade-off DEC-019**:
- ❌ Nessun infographic chart elaborato (anti-stock-design)
- ❌ Nessuna animazione count-up (rispetto `prefers-reduced-motion`)
- ✅ Solo numero grande Playfair + caption mono + source mono micro

---

### Pattern 8 — CTA progressive

**Friction-points reference**: FP2.2 — CTA progressive lungo il flusso pagina (top + middle + bottom) con varianti tonali per ridurre fatigue.

**Componente DS adattato**:
- `.sl-btn--primary` (filled navy + cream text + bronze hover) — esistente, drift accettato
- `.sl-btn--ghost` (transparent + hairline border-bottom) — esistente
- Pattern: top= `.sl-btn--ghost`, middle= `.sl-btn--primary`, bottom= sticky attorney CTA + WhatsApp mobile

**ACF Field (riuso)**:
- Theme Options "CTA Defaults" già contiene `cta_label`, `cta_url`, `cta_subline_italic` — nessun cambio
- `group_competenza_v1` aggiungere `cta_top_label`, `cta_top_url`, `cta_middle_label`, `cta_middle_url` per single-competenza Tier-1 (override defaults)

**Template-part**:
- Estendere `single-competenza.php` con 3 punti CTA:
  - Top (sotto answer capsule): `.sl-btn--ghost` con label "Leggi i casi rappresentativi" o "Scopri di più"
  - Middle (dopo principal content, prima FAQ): `.sl-btn--primary` con label "Prenota un primo incontro"
  - Bottom (mobile sticky bar Pattern 3 + desktop sticky attorney CTA `.sl-attorney__sticky` esistente)
- Estendere `template-parts/page-costi.php` con CTA progressive simili

**CSS**: nessun cambio. Riuso pattern esistenti.

**Trade-off**: nessuno. Pure compositing di CTA esistenti.

---

### Pattern 9 — Author byline ricca (single blog + competenza)

**Friction-points reference**: FP1.3 — author byline schema Person + cross-link competenze trattate per single blog post.

**Componente DS adattato**:
- `partial-attorney.php` (schema Person + Attorney) — esistente per single-avvocato
- `.sl-mono` per byline metadata (data + author + reading time)
- Estensione di single.php (blog) per author byline editoriale

**ACF Field (estensione)**:
- `group_avvocato_v1` aggiungere field:
  - `byline_extended` (textarea, ~200 char, "1-frase bio per byline su single blog")
  - `expertise_topics` (relationship field con `competenza` CPT, max 3, per cross-link)

**Template-part**:
- Estendere `single.php` (blog) sotto `<h1>` con `<div class="sl-author-byline">`
- Eyebrow `<p class="sl-mono">{publish_date} · LETTURA {reading_time} MIN · DI {author_name}</p>`
- Bio `<p>{byline_extended}</p>` (Playfair italic 17px navy)
- Cross-link competenze `<ul class="sl-author-expertise">{loop expertise_topics}</ul>`
- Schema Person + Article (già implementato in `partial-article.php`)

**CSS** (`assets/css/components/cro.css`):
```css
.sl-author-byline {
  margin: var(--s-5) 0 var(--s-7) 0;
  padding-bottom: var(--s-5);
  border-bottom: 1px solid var(--border);
}
.sl-author-byline .sl-mono {
  margin-bottom: var(--s-3);
}
.sl-author-byline__bio {
  font-family: var(--font-display);
  font-style: italic;
  font-size: 17px;
  color: var(--primary);
  margin-bottom: var(--s-3);
  max-width: 60ch;
}
.sl-author-expertise {
  display: flex;
  gap: var(--s-3);
  flex-wrap: wrap;
}
.sl-author-expertise li {
  /* riuso .sl-tag esistente */
}
```

**Trade-off DEC-019**:
- ❌ Nessuna author photo dedicata (gallery hard rule editoriale)
- ❌ Nessun social link in byline (rispetto Theme Options `sameAs` solo via schema, no UI esposta)
- ✅ Reading time calcolato runtime via PHP `wordcount`

---

### Pattern 10 — Related services cards (sotto single-competenza + single-avvocato)

**Friction-points reference**: FP4.2 — cross-linking interno per ridurre dead-end pagina + alimentare Internal PageRank.

**Componente DS adattato**:
- `.sl-area` row component (esistente, archive competenze) — riuso 1:1 con variante "Aree correlate"

**ACF Field (estensione)**:
- `group_competenza_v1` aggiungere `related_competenze` (relationship, max 3, post_object competenza)
- `group_avvocato_v1` aggiungere `competenze_trattate` (relationship, max 5, post_object competenza) — Wave 5 lo richiede già per IA refactor

**Template-part**:
- Estendere `single-competenza.php` con sezione "Aree correlate" (sotto FAQ, sopra mini-form)
- Estendere `single-avvocato.php` con sezione "Aree di competenza dell'avvocato" (esistente come testo, da convertire in `.sl-area` rows)

**CSS**: nessun cambio. Riuso `.sl-area`.

**Trade-off**: nessuno. Pure compositing di pattern esistenti.

---

## Indicatori di completamento Wave 6

### Acceptance criteria operativi

- [ ] Field Group `group_competenza_v1` esteso con `answer_capsule` + `related_competenze`
- [ ] Field Group `group_trust_item_v1` esteso con `testimonial_type`/`testimonial_text`/`testimonial_author`/`testimonial_city`/`testimonial_topic` + `source_label`/`source_text`/`source_url`
- [ ] Field Group `group_avvocato_v1` esteso con `byline_extended` + `expertise_topics` + `competenze_trattate`
- [ ] Theme Options "Brand" tab esteso con 8 trust_signal fields (4 label + 4 caption)
- [ ] Theme Options eventuale "Trust Bar Source" sub-tab opzionale
- [ ] Nuovo `template-parts/trust-bar.php` riusabile
- [ ] Nuovo `template-parts/mobile-sticky-bar.php`
- [ ] Nuovo `template-parts/mini-form.php`
- [ ] Nuovo `template-parts/testimonials-block.php`
- [ ] Estensione `single-competenza.php`: answer-capsule + CTA progressive + mini-form + related-services
- [ ] Estensione `single.php` (blog): author byline ricca + author expertise tags
- [ ] Estensione `single-avvocato.php`: competenze trattate come `.sl-area` rows
- [ ] Estensione `front-page.php`: trust-bar + testimonials-block (opzionale, decisione cliente)
- [ ] Estensione `template-parts/page-costi.php`: CTA progressive + mini-form
- [ ] Estensione `footer.php`: hook `.sl-mobile-bar` con condizioni body-class
- [ ] Nuovo CSS bundle `assets/css/components/cro.css` con tutti i pattern
- [ ] `inc/enqueue.php` aggiornato per enqueue di `cro.css`
- [ ] Schema FAQPage generalizzato a tutte le competenze (Tier-1 + Tier-2) se `faq_associate` non vuoto
- [ ] Acceptance test: smoke 21 URL HTTP 200 + Lighthouse no-regression rispetto baseline pre-Wave 6
- [ ] Mobile responsive: test su iPhone 12/13/14 + Pixel 6 + Galaxy S22

### Quality gate

- [ ] Niente nuove dipendenze JS (Wave 6 è pure CSS + PHP)
- [ ] Niente nuovi font (rispetto al MVP locked)
- [ ] Niente nuovi colori fuori palette `#FAFAF8`/`#F2F0EA`/`#1B2B4B`/`#B8860B`/`#2D2D2D`/`#6B6B6B`/`#E5E0D5`
- [ ] Tutti i template-part hanno graceful fallback (`saltelli_field` + `the_content`)
- [ ] Tutti gli ACF Field nuovi hanno default value editoriale (no field vuoti che rompono il rendering)
- [ ] `prefers-reduced-motion: reduce` opt-out per ogni nuova transition CSS
- [ ] `aria-label` per ogni componente interattivo nuovo (mobile-bar, mini-form button)
- [ ] Accordion FAQ rispetta a11y: `<details>/<summary>` semantic + keyboard accessible

---

## Cosa NON viene implementato in Wave 6 (out of scope)

- ❌ **Sessione 3 di Claude Design** (DEC-019)
- ❌ **Mockup high-fi PNG dei pattern** (no design intermedio)
- ❌ **Carousel JS o slider per testimonials** (solo grid statico)
- ❌ **Foto clienti / star rating** (privacy + anti-stock)
- ❌ **Author photo dedicata** (gallery hard rule)
- ❌ **Live chat widget** (WhatsApp basta)
- ❌ **Booking calendar embedded** (TBD, posticipato a fase post-launch)
- ❌ **Animation count-up per statistic** (rispetto reduced-motion)
- ❌ **Conditional logic form** (CF7 base senza Gravity-style logic)
- ❌ **Multi-step form** (form base 4-field)

---

## Riferimenti incrociati

- DEC-014 (sitemap firmata vince — refactor IA in Wave 5, propedeutico)
- DEC-018 (drift accettato — niente recovery)
- DEC-019 (Wave 6 lean — questo documento)
- DEC-020 (pipeline 5→6→4→7)
- `01-discovery/friction-points-and-cro-patterns.md` — 17 acceptance criteria
- `05-development/mvp-state-snapshot.md` — stato corrente del MVP (CPT, ACF, schema, pagine)
- `_shared/tech-stack-rationale-v2.md` — stack tecnologico
- MVP `wp-content/themes/saltelli/docs/DESIGN.md` — DS tokens locked + componenti DS attuali
- MVP `wp-content/themes/saltelli/.claude/knowledge/_history/design/sessione-1/` + `sessione-2/` — DS originale Sessione 1+2 (riferimento storico)
