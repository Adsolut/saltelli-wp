---
name: Studio Legale Emiliano Saltelli & Partners
description: Boutique law firm in Naples (Chiaia), founded 1999 — premium AI-ready WordPress site, "Diritto, con misura."
register: brand
---

## Users

### Primary — *Privati*
Clienti privati di Napoli e Campania che cercano un avvocato di fiducia per pratiche di diritto della famiglia (incluse coppie e famiglie LGBTQ+), diritto del lavoro, separazioni, contenzioso ordinario. Età 35-65, ricerca su Google + raccomandazione personale, valutano competenza percepita prima di prezzo. Si aspettano riservatezza, ascolto, profondità.

### Secondary — *Imprese*
Imprenditori PMI Campania (settore commercio, servizi, edilizia) per consulenza fiscale, contrattualistica, contenzioso tributario. Decisione di engagement spesso B2B referral.

### Tertiary — *Famiglie LGBTQ+ (presidio nazionale)*
Tier-1 strategico differenziante: l'unico studio in Campania con riconoscimento giuridico in giurisprudenza locale (sentenza 2023 Tribunale Napoli, primo riconoscimento campano di trascrizione integrale di atto di nascita con due madri). Bacino nazionale via consulenza video.

## Brand

**Strategic principle:** *"Diritto, con misura."* Calma, autorevolezza che non urla, ascolto, profondità tier-1 in poche aree presidiate.

**Heritage:** L'avv. Emiliano Saltelli, Federico II Napoli, 20+ anni di esperienza dal 1999 (lineage: 2008 ex socio → 2019 fondazione attuale). Bottega di 4 avvocati a Chiaia, indirizzo Via Vannella Gaetani 27.

**Voice:** Editorial italiano. Sobrio, denso, mai colloquiale. Frasi piene, mai promozionali. Termini giuridici precisi senza barocchi. Riservatezza implicita. Linguaggio inclusivo per area LGBTQ+ senza retorica.

**Tone references (positive):**
- BonelliErede — struttura editoriale, disciplina visiva
- Pentagram (lawfirm work) — typography dominante, nessun ornamento
- Stripe Press — interazioni misurate, attesa tipografica
- Wise — chiarezza nel vocabolario tecnico per non-specialisti
- Cravath Swaine & Moore — silhouette ritratti, cromia desaturata

## Anti-references (cose da NON fare, mai)

- **Stock photo legali generici** — bilance, martelletti, codici aperti, strette di mano. Vietato.
- **Color schemes da website "studio professionale"** — gold gradients, navy lucido vetroso, "professional blue".
- **Aggressive red CTA o purple/magenta** — purple/magenta è brand Adsolut (vendor), non Saltelli.
- **Page builder bloat** — Elementor, Bricks, Divi, WPBakery. Solo PHP puro.
- **Jargon vuoto** — "soluzioni legali su misura", "team altamente qualificato", "passione per il diritto". Sostituito da fatti specifici (sentenza 2023, area presidiata, anno fondazione).
- **Decorazioni** — ornament, divider fancy, drop-shadow, glassmorphism. Vietato. Solo righe sottili (`--border #E5E0D5`).
- **Multiple weights per stesso role** — non mescolare 400+500+700 nella stessa gerarchia.
- **Marketing-speak inclusività LGBTQ+** — la pagina è competenza tecnica, non manifesto. Esempio brutto: "Inclusività al cuore." Esempio buono: "Trascrizione integrale di atto di nascita con due madri (Tribunale di Napoli, 2023)."

## Strategic principles

1. **Differenziazione tier-1** — 3 aree presidiate in profondità (Tributario, Lavoro, Famiglia LGBTQ+) con autori, casi reali, FAQ dedicate. Le altre 16 aree restano tier-2 leggere.
2. **AI-readiness** — Schema JSON-LD inline (Organization, LegalService, Attorney, FAQPage, Article, BreadcrumbList) + llms.txt + robots ottimizzato. Target: AI Overviews trigger 81% mobile.
3. **Mobile-first content** — 60%+ traffico mobile. LCP < 2.5s priorità. Touch target 48×48 minimo.
4. **GEO local presidio Napoli/Chiaia** — schema PostalAddress, GeoCoordinates, openingHours preserve l'asse "studio fisico in Chiaia" senza minare il bacino nazionale LGBTQ+.
5. **Coabitazione Yoast** — schema delegato a Yoast quando attivo (Organization, Article, Breadcrumb), tema emette solo Person/Attorney/FAQPage non duplicati.

## Constraints (dal brief)

- WordPress 6.x + PHP 8.2+
- ACF Pro non installato — fallback editorial hardcoded
- Iubenda non in uso (no DOMDocument round-trip risk)
- GSAP 3.12.5 + ScrollTrigger CDN, Lenis 1.1.13 (currently disabled)
- NO nuove librerie senza istruzione esplicita
- Single H1 per pagina (audit ha trovato duplicati su sito legacy)
- `_thumbnail_id=2683` Emiliano + `bio_estesa` avvocati — preserve cross-run
