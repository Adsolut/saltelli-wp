# BRIEF: Studio Legale Saltelli — WordPress Custom Theme (AI-Ready)

## Progetto

Sito WordPress custom per Studio Legale Emiliano Saltelli & Partners, Napoli. Parte della Fase 1 di un programma GEO (Generative Engine Optimization) da €14.000/7 mesi. Il sito deve essere **AI-ready dalla prima riga di codice** — progettato per essere visibile e citabile dai motori AI generativi (ChatGPT, Perplexity, Gemini, Google AI Mode).

## Cliente

**Studio Legale Emiliano Saltelli & Partners**Via Vannella Gaetani, 27 — 80121 Napoli (quartiere Chiaia) Tel: 081 1813 1119 | WhatsApp: +39 351 713 8006 Email: [info@studiolegalesaltelli.it](mailto:info@studiolegalesaltelli.it)PEC: [emilianosaltelli@avvocatinapoli.legalmail.it](mailto:emilianosaltelli@avvocatinapoli.legalmail.it)P.IVA: 06685101211 Orari: 10:00–19:00 Lun–Ven, solo su appuntamento

## Team dello Studio (4 avvocati)

1. **Avv. Emiliano Saltelli** — Fondatore. Tributarista e civilista. 20+ anni esperienza. Federico II.
2. **Avv. Fabiana Saltelli** — Diritto civile, tributario, del lavoro (giuslavorista).
3. **Avv. Antonia Battista** — Diritto di famiglia, divorzi, unioni civili, famiglie LGBTQ+.
4. **Avv. Stefano Gaetano Tedesco** — Diritto condominiale e immobiliare. Rete esterna: commercialisti, consulenti del lavoro, medici legali, psicologi.

## 19 Aree di Pratica

Tributario, Cartelle esattoriali/multe, Recupero crediti, Diritto del lavoro, Famiglia/divorzi, Responsabilità medica (malasanità), Bancario, Condominiale/immobiliare, Immigrazione, Penale, Previdenziale, Assicurazioni, Successioni, Risarcimento danni, Responsabilità civile, Domiciliazione impresa, Consulenze online, Diritto amministrativo, Diritto commerciale.

## Sito Attuale — Problemi Identificati (GEO Audit: 2.3/10)

- WordPress + Elementor Pro (dipendenza JS, pesante)
- Zero schema markup JSON-LD
- H1 duplicati su più pagine
- Meta description assenti
- Open Graph tags assenti
- Nessun file llms.txt
- Robots.txt non ottimizzato per crawler AI
- Nessun Google Business Profile
- Contenuti narrativi, non estraibili (no FAQ, no answer capsules)
- Zero earned media esterno

---

## ARCHITETTURA SITO NUOVO

### Pagine principali

```
/ (Homepage)
/lo-studio/ (Chi siamo, storia, valori, team overview)
/avvocati/ (Pagina team)
/avvocati/emiliano-saltelli/
/avvocati/fabiana-saltelli/
/avvocati/antonia-battista/
/avvocati/stefano-gaetano-tedesco/
/competenze/ (Overview 19 aree con link)
/competenze/[slug-area]/ (Una pagina per ciascuna delle 19 aree)
/blog/ (Archivio articoli con categorie)
/blog/[slug-articolo]/ (Singolo articolo)
/contatti/ (Mappa, form, info, orari, candidature)
/llms.txt (File per crawler AI)
/privacy-policy/
/cookie-policy/
```

### Struttura ogni pagina di competenza

1. **Answer capsule** (40-60 parole) — risposta diretta alla query target
2. **H1 unico** — keyword-rich, specifico per l'area
3. **Contenuto principale** — strutturato con H2/H3 logici
4. **Sezione FAQ** (3-5 domande) — schema FAQPage
5. **CTA** — contatto/consulenza
6. **Link correlati** — altre competenze rilevanti

### Struttura ogni pagina avvocato

1. Foto professionale
2. Nome, ruolo, specializzazioni
3. Bio professionale strutturata
4. Aree di competenza (link alle pagine competenze)
5. Schema Attorney/Person JSON-LD

### Implementazione tecnica delle pagine (chiarimento)

Le pagine **avvocato** (4) e **competenza** (19) sono implementate come **Custom Post Types** WordPress, NON come pagine statiche:

- CPT `avvocato` → registrato in `functions.php`, slug pubblico `/avvocati/{slug}/`, archive `/avvocati/`, supporto title/editor/thumbnail/custom-fields
- CPT `competenza` → registrato in `functions.php`, slug pubblico `/competenze/{slug}/`, archive `/competenze/`, supporto title/editor/thumbnail/custom-fields

**Vantaggi del CPT vs pagine:**

- Schema markup JSON-LD generato automaticamente per ogni entry tramite template `single-{cpt}.php`
- Scalabilità: aggiungere il 20° avvocato o un'area numero 20 è zero refactor
- Tassonomie associate (es. tassonomia `tipo-area` per categorizzare le 19 aree in macro-gruppi: civile, penale, tributario, lavoro, famiglia)
- Pulizia dell'admin: l'editor non confonde "pagine istituzionali" con "schede prodotto"
- Loop standardizzati per archive, related, sidebar

Le altre URL della struttura informativa (`/lo-studio/`, `/contatti/`, `/blog/`, `/privacy-policy/`, `/cookie-policy/`) restano come pagine WordPress standard o archive (per `/blog/`).

---

## DESIGN DIRECTION

### Estetica

**Direzione: Legal Luxury Minimal** — uno studio legale napoletano nel quartiere Chiaia (zona alta). Il design deve comunicare: competenza, affidabilità, prestigio, modernità. Non il solito sito legale scuro con bilancia della giustizia.

### Principi

- **Tipografia dominante** — font serif elegante per headings (Playfair Display, Cormorant, o equivalente), sans-serif raffinato per body (DM Sans, Satoshi, o equivalente)
- **Palette sobria ma non banale** — base chiara con accenti navy/blu scuro + oro/bronzo come accent. NO nero pieno, NO rosso, NO viola
- **Spazio bianco generoso** — la pulizia comunica professionalità
- **Foto reali** — dei 4 avvocati, dello studio, del quartiere Chiaia/Napoli
- **Micro-animazioni** — subtle fade-in on scroll, niente di vistoso
- **Mobile-first** — almeno 60% del traffico sarà mobile

### NO (da evitare)

- Icone generiche di bilancia/martelletto
- Stock photo di strette di mano
- Slideshow in homepage
- Colori aggressivi
- Font generici (Inter, Roboto, Arial)
- Elementor o page builder pesanti

---

## REQUISITI TECNICI GEO (AI-READY)

### Schema Markup JSON-LD (obbligatorio su ogni pagina)

```json
// Header globale
Organization + LocalBusiness/LegalService
{
  "@type": ["Organization", "LegalService"],
  "name": "Studio Legale Emiliano Saltelli & Partners",
  "url": "https://studiolegalesaltelli.it",
  "address": { Via Vannella Gaetani 27, 80121 Napoli },
  "telephone": "+390811813119",
  "openingHours": "Mo-Fr 10:00-19:00",
  "areaServed": "Napoli, Campania, Italia",
  "priceRange": "$$"
}

// Per ogni avvocato
Attorney/Person con sameAs, knowsAbout, worksFor

// Per ogni pagina competenza
FAQPage con domande e risposte

// Per ogni articolo blog
Article con author, datePublished, dateModified

// Navigazione
BreadcrumbList su tutte le pagine
```

### File llms.txt

```
# Studio Legale Emiliano Saltelli & Partners
> Studio legale a Napoli fondato dall'Avv. Emiliano Saltelli.
> 4 avvocati, 19 aree di pratica. Sede: Via Vannella Gaetani 27, Chiaia.
> Specializzati in: diritto tributario, cartelle esattoriali, diritto del lavoro,
> diritto di famiglia, malasanità, immigrazione.

## Contatti
- Telefono: 081 1813 1119
- WhatsApp: +39 351 713 8006
- Email: info@studiolegalesaltelli.it
- Indirizzo: Via Vannella Gaetani 27, 80121 Napoli

## Aree di pratica
[elenco delle 19 aree con URL]

## Team
[elenco dei 4 avvocati con specializzazione e URL]
```

### Robots.txt

```
User-agent: *
Allow: /
Sitemap: https://studiolegalesaltelli.it/sitemap.xml

User-agent: GPTBot
Allow: /

User-agent: ClaudeBot
Allow: /

User-agent: PerplexityBot
Allow: /

User-agent: Google-Extended
Allow: /
```

### Performance e Tecnica

- WordPress senza Elementor — tema custom PHP/HTML/CSS/JS
- No page builder, no bloat
- Core Web Vitals ottimizzati (LCP &lt; 2.5s, CLS &lt; 0.1, INP &lt; 200ms)
- Lazy loading immagini native
- CSS minificato inline per above-the-fold
- Font preload per web fonts
- Sitemap XML automatica
- Breadcrumb navigazione
- Meta description unica per ogni pagina
- Open Graph tags per social sharing
- Heading hierarchy corretta (un solo H1 per pagina)

---

## STACK TECNICO CONSIGLIATO

- **WordPress 6.x** (ultima versione stabile)
- **Tema custom** — no child theme, no starter theme. PHP puro con template hierarchy standard
- **ACF Pro** — per campi personalizzati (avvocati, competenze, FAQ)
- **Yoast SEO** o **Rank Math** — per meta, sitemap, breadcrumb
- **Schema Pro** o **custom JSON-LD** inline nei template
- **WP Rocket** o **LiteSpeed Cache** — per performance
- **Contact Form 7** o **WPForms** — per form contatti
- **SMTP configurato** per email affidabile

---

## DELIVERABLE ATTESI

1. Tema WordPress custom installabile (.zip)
2. Template per tutte le pagine (home, studio, avvocati, competenze, blog, contatti)
3. Schema JSON-LD implementato su tutte le pagine
4. File llms.txt
5. Robots.txt ottimizzato
6. ACF field groups configurati
7. Contenuti migrati dal sito attuale
8. Performance test Lighthouse &gt; 90 su tutte le metriche

---

## NOTE OPERATIVE

- Il progetto vive in: `/Users/aldosantoro/Desktop/DEV/saltelli-wp/`
- Template base disponibile in: `/Users/aldosantoro/Desktop/DEV/wordpress-claude-template/`
- Documentazione audit: `/Users/aldosantoro/Desktop/CLIENTI/Attivi/Studio Legale Saltelli/`
- Sviluppo locale prima, deploy su hosting dopo
- Elena ha i materiali del sito attuale come riferimento
- Fornitore: Adsolut SRLS ([info@adsolut.it](mailto:info@adsolut.it))

---

## WORKFLOW OPERATIVO — DUMP → DEVELOP → APPROVE → DEPLOY

### Fase 0: Site Dump (pre-sviluppo)

Scaricare l'intero sito WordPress attuale di Saltelli per lavorarci in locale.

**Cosa serve dal cliente:**

- Accesso SSH o FTP al server attuale
- Credenziali WordPress admin (o Application Password)
- Credenziali database MySQL (host, user, pass, db name)

**Procedura dump (via WP-CLI o SSH):**

```bash
# 1. Dump database
wp db export saltelli_dump_$(date +%Y%m%d).sql --ssh=user@host

# 2. Download files
rsync -avz --progress user@host:/path/to/wordpress/ ./saltelli-dump/
# oppure
scp -r user@host:/path/to/wordpress/ ./saltelli-dump/

# 3. Alternativa senza SSH: plugin All-in-One WP Migration
# Installare il plugin, esportare, scaricare il .wpress file
```

### Fase 1: Setup ambiente locale

Sviluppo su macchina locale con Docker o Local by Flywheel.

**Opzione A — Docker (consigliata per Claude Code):**

```yaml
# docker-compose.yml nella cartella saltelli-wp/
services:
  wordpress:
    image: wordpress:6-php8.2-apache
    ports: ["8080:80"]
    volumes:
      - ./wp-content/themes/saltelli:/var/www/html/wp-content/themes/saltelli
      - ./wp-data:/var/www/html
    environment:
      WORDPRESS_DB_HOST: db
      WORDPRESS_DB_USER: saltelli
      WORDPRESS_DB_PASSWORD: saltelli_dev
      WORDPRESS_DB_NAME: saltelli_wp
  db:
    image: mysql:8.0
    volumes: ["./db-data:/var/lib/mysql"]
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: saltelli_wp
      MYSQL_USER: saltelli
      MYSQL_PASSWORD: saltelli_dev
  phpmyadmin:
    image: phpmyadmin
    ports: ["8081:80"]
    environment:
      PMA_HOST: db
```

**Dopo il setup:**

```bash
# Importare il dump del database
docker exec -i saltelli-wp-db-1 mysql -u saltelli -psaltelli_dev saltelli_wp < saltelli_dump.sql

# Aggiornare gli URL per locale
wp search-replace 'https://studiolegalesaltelli.it' 'http://localhost:8080' --all-tables
```

### Fase 2: Sviluppo tema custom

- Il tema viene sviluppato in `./wp-content/themes/saltelli/`
- Volume Docker montato = ogni modifica live
- Claude Code lavora direttamente sui file del tema
- Browser preview su `http://localhost:8080`

### Fase 3: Design con Claude Design (parallelo)

- Aprire `claude.ai/design`
- Web capture del sito attuale per catturare stile corrente
- Iterare il design della homepage, pagine avvocati, competenze
- Esportare handoff bundle → usare come reference in Claude Code
- Il design approvato dal cliente PRIMA di implementare

### Fase 4: Approvazione cliente

- Staging su sottodominio temporaneo (es. `dev.studiolegalesaltelli.it`)
- Review con il cliente
- Iterazioni su feedback
- Sign-off formale

### Fase 5: Deploy produzione

- Backup del sito attuale in produzione
- Upload tema + contenuti aggiornati
- Aggiornamento DNS se cambio hosting
- Test post-deploy: Lighthouse, schema validator, robots.txt
- Monitoraggio 48h post-lancio

---

## CLAUDE DESIGN INTEGRATION

Il workflow prevede l'uso di Claude Design ([claude.ai/design](http://claude.ai/design)) come tool di prototipazione visiva PRIMA dello sviluppo con Claude Code.

**Sequenza raccomandata:**

1. Su Claude Design: creare il design system (colori, font, spaziature)
2. Su Claude Design: prototipare homepage, pagina avvocato, pagina competenza
3. Approvazione visiva del cliente sulle 3 pagine chiave
4. Esportare il handoff bundle da Claude Design
5. Su Claude Code: il team multi-agent implementa il tema WordPress basandosi sul design approvato

**Prompt iniziale Claude Design:**

```
Sto creando un sito web per uno studio legale premium a Napoli, quartiere Chiaia. 
Lo studio ha 4 avvocati e 19 aree di pratica.

Design direction: "Legal Luxury Minimal"
- Font serif elegante per headings (Playfair Display / Cormorant)
- Font sans-serif raffinato per body (DM Sans / Satoshi)
- Palette: base chiara crema, navy come accent, oro/bronzo per dettagli
- Spazio bianco generoso, tipografia dominante
- Animazioni scroll eleganti (non vistose)
- Mobile-first

Cattura il sito attuale: https://studiolegalesaltelli.it
Ridisegnalo completamente mantenendo i contenuti ma con il nuovo design system.
Parti dalla homepage.
```

---

## LIBRERIE JS/CSS — STACK EFFETTI WOW 2026

LibreriaVersioneUsoPesoCDN**GSAP**3.15+Timeline animations, scroll triggers, text split\~30KB gzip`cdn.jsdelivr.net/npm/gsap@3.15/dist/gsap.min.js`**GSAP ScrollTrigger**3.15+Scroll-driven animationsincluso`cdn.jsdelivr.net/npm/gsap@3.15/dist/ScrollTrigger.min.js`**GSAP SplitText**3.15+Text reveal word-by-word/line-by-lineincluso`cdn.jsdelivr.net/npm/gsap@3.15/dist/SplitText.min.js`**Lenis**latestSmooth momentum scrolling3KB`cdn.jsdelivr.net/npm/lenis@latest/dist/lenis.min.js`

**NON USARE:** AOS, WOW.js, Locomotive Scroll, ScrollMagic, Anime.js (obsoleti o non performanti per questo stack)

**Combo pattern standard 2026:**

```javascript
// Inizializzazione Lenis + GSAP
const lenis = new Lenis({ lerp: 0.1, smoothWheel: true });
lenis.on('scroll', ScrollTrigger.update);
gsap.ticker.add((time) => lenis.raf(time * 1000));
gsap.ticker.lagSmoothing(0);
```
