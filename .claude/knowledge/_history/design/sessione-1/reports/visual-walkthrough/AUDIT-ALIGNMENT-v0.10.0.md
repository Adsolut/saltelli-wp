# Audit Allineamento — v0.10.0 (durante Final Polish v0.11.0 in flight)

> **Scope:** audit cliccando ogni voce del menu come fa un cliente comune.
> **Pattern segnalato da Duccio:** "ogni pagina ha allineamento header/contenuti diverso. Homepage parte tutta a sinistra mentre solo alcune sono perfettamente allineate. Tutte vanno riviste nello spacing verticale tra contenuti e nella distanza tra head e hero."
> **Conferma orchestrator:** problema reale e quantificato. **Tre sistemi di indentazione + cinque diverse distanze header→hero coesistono.**

---

## Misure DOM esatte (header bottom = 78px ovunque)

| Pagina | Eyebrow/Breadcrumb top | H1 top | Gap header→hero | Padding-left H1 | Pattern |
|---|---:|---:|---:|---:|---|
| `/` Homepage | 198 | 302 | **120px** | 144 | Hero asimmetrico |
| `/chi-siamo/` | 193 (breadcrumb) | 235 | **115px** | 72 | Page generic |
| `/avvocati/` archive | 261 | 206 | **128px (sopra eyebrow)** | **861 (destra!)** | Archive 2-col asimm |
| `/competenze/` archive | 206 | 206 | **128px** | **448 (centro-destra)** | Archive 2-col asimm |
| `/blog/` archive | 280 (eyebrow) | n/a | **202px** | 200 | Archive standard |
| `/contatti/` | 218 (breadcrumb) | 308 | **140px** | 72 | Page generic |
| `/costi/` | 218 (breadcrumb) | 308 | **140px** | 72 (h1) → 393 (body) | Page con eyebrow sticky |
| `/tipo-area/privati/` | 251 (**OVERLAP!**) | 206 | **128px** | 485 | Taxonomy 2-col asimm |
| `/competenze/diritto-tributario/` | 100 | 165 | **22px** | 72 | Single content (compatto) |
| `/avvocati/emiliano-saltelli/` | 100 | (foto first) | **22px** | 72 | Single avvocato (foto) |
| `/intimazione-tari-...` (post) | 35 (?) | 131 | **53px** | 72 | Single blog post |

---

## 🔴 Cinque problemi quantificati

### Problema 1 — **Gap header→hero** ha 5 valori diversi (22, 53, 115-128, 140, 202px)

**Atteso:** ritmo costante editoriale (es. 96-120px su tutte le pagine).
**Attuale:** alcune pagine sono "schiacciate" sotto l'header (single post 53px, single competenza 22px), altre "perdute nello spazio" (blog archive 202px). **Inconsistenza visiva grave navigando il menu.**

### Problema 2 — **Padding-left** ha 5 sistemi (72, 80, 144, 200, 448, 861px)

**Atteso:** padding container unificato (es. 96px desktop standard).
**Attuale:**
- Homepage: `144px` (template `front-page.php` con `clamp(24, 5vw, 96)` + extra 48px da grid)
- Page generic: `72px` (template `page.php` con `clamp(24, 5vw, 96)` riducendo a 72 a 1440)
- Archive avvocati/competenze: H1 a `448-861` (asimmetrico 2-col, eyebrow stretta + headline destra)
- Single post/competenza/avvocato: `72px` (single template)

L'utente che naviga **vede un sito diverso ad ogni click**.

### Problema 3 — **Homepage parte "troppo a sinistra"**

Effetto descritto da Duccio: l'eyebrow "STUDIO LEGALE · NAPOLI · CHIAIA · DAL 1999" inizia a `left:144` ma h1 "Diritto, con misura." è in colonna 8fr → finisce a `left:144`, lasciando i 4fr di destra (~520px) **completamente vuoti** finché non scrolli (allora appare colophon).

L'effetto first impression è "sito sbilanciato a sinistra" perché above-the-fold il colophon non è visibile.

### Problema 4 — **`/casi/` voce nel menu ma pagina NON esiste** (404)

Click su "Casi" del menu primary → HTTP 404 "Pagina non trovata."

**Causa:** menu item ha url `/casi/` ma:
- Nessuna page WP con slug `casi`
- Nessun CPT `caso` (mai creato)
- Nessun template `taxonomy-casi.php` o `archive-caso.php`

**Bug critico** non rilevato in walkthrough precedenti perché /casi/ ritornava HTTP 404 sempre, smoke test ignorava (atteso per /non-esiste/, ma /casi/ è cliccabile dal menu!).

### Problema 5 — **`/tipo-area/privati/` overlap testo** (eyebrow + breadcrumb sovrapposti)

Ho visto nello screenshot: "STUDIO·COMPETENZE·PER·CATEGORIA / PRIVATI" sembra un overlap di 2 stringhe. Il template `taxonomy-tipo-area.php` emette **sia** `.sl-page__breadcrumb` ("HOME / COMPETENZE / PER CATEGORIA / PRIVATI") **sia** un eyebrow `.sl-mono` ("STUDIO · AREE PER CATEGORIA"), nello stesso punto top 251px. Risultato: testi sovrapposti illeggibili.

---

## 🎯 Cosa proporre al prossimo agent (post-Final Polish)

L'agent **Final Polish v0.11.0 in corso** sta lavorando su R1 (mappa) + R2 (Lorem ipsum chi-siamo) + R3 (em-dash bullet competenza). Quando finisce, il prossimo agent deve fare:

### Layout Harmonization Agent v0.12.0

**3 task in sequenza:**

#### Task 1 — Container unificato `.sl-container` system-wide

Definire UN solo container CSS:
```css
.sl-container,
.sl-page,
.sl-post,
.sl-archive,
.sl-section-head,
.sl-hero__inner,
.sl-areas .sl-container,
.sl-team .sl-container,
.sl-cases .sl-container {
    max-width: 1440px;
    margin: 0 auto;
    padding-inline: clamp(24px, 5vw, 96px);
}
```

A 1440px viewport: padding effettivo = 72px sx + 72px dx → contenuto centrato 1296px.
A < 1440px: clamp degrada gracefully fino a 24px mobile.

**Tutti i template usano la stessa formula.** Allineamento h1 = 72px coerente ovunque.

#### Task 2 — Spacing verticale unificato `--space-hero`

```css
:root {
    --space-hero-top: clamp(64px, 8vw, 120px);
    --space-hero-bottom: clamp(48px, 6vw, 80px);
}

.sl-page__title,
.sl-section-head h1,
.sl-section-head h2,
.sl-hero__main,
.sl-post__title {
    margin-top: var(--space-hero-top);
}

.sl-page__breadcrumb,
.sl-page__eyebrow,
.sl-section-head .sl-mono:first-child {
    margin-top: var(--space-hero-top);
    margin-bottom: 24px;
}
```

Risultato:
- Tutte le pagine: gap header→breadcrumb/eyebrow = ~120px (consistent)
- Tutte le pagine: gap eyebrow→h1 = ~24px

#### Task 3 — Bug specifici

**3.A Crea pagina `/casi/`** (o cambia menu URL a `/competenze/?filter=tier-1` o rimuovi voce).

Opzione raccomandata: page WP con slug `casi`, content che mostra i 4 casi della homepage (`saltelli_homepage_cases()` helper) come archive. ~30 min.

**3.B Fix overlap `/tipo-area/{slug}/`** in `taxonomy-tipo-area.php`:
rimuovi `<div class="sl-mono">Studio · Aree per categoria</div>` (duplicato del breadcrumb) → restano solo:
```
HOME / COMPETENZE / PER CATEGORIA / PRIVATI    (breadcrumb mono)
Per i Privati                                   (h1 Playfair)
9 aree                                          (lede italic Playfair)
```

**3.C Homepage hero — colophon più visibile sopra fold**

Riduci `min-height` dell'h1 hero da `clamp(80,10vw,160)` a `clamp(64, 8vw, 132)` su desktop, così che colophon (top:725 attuale) salga a ~600px, sopra il fold a 1440×900.

In alternativa: layout 2-col attivo già da `min-width: 1024px` (non solo dopo) con eyebrow + h1 a sinistra, colophon visibile a destra fin dal primo screenshot.

---

## 🔒 Vincoli per l'agent

1. **NON sovrascrivere il lavoro Final Polish v0.11.0** in flight (R1 mappa, R2 chi-siamo content, R3 bullet). L'orchestrator committa quel lavoro PRIMA di lanciare Layout Harmonization v0.12.0.
2. **Design tokens locked** (palette, font).
3. **Foto Emiliano** + **bio_estesa** + **post_content competenza** intoccabili.
4. Cache flush + smoke test 12+ URL dopo OGNI fix (lezione comprehensive).
5. Verifica DOM positions via `javascript_exec` post-fix per confermare `gap: 120px` consistente e `left: 72px` consistente.

---

## 🟡 Issue minor identificati (rinviabili a Step F+)

- Footer/blog dropdown styling polish minore
- Mobile responsive verifica posizioni post-Layout Harmonization
- Animazioni cross-page (transition fade?) opzionale
- Empty state /casi/ se page non popolata immediatamente

---

*Audit completato durante Final Polish in flight. Salvato per Layout Harmonization Agent v0.12.0 (post-R1+R2+R3 commit).*
