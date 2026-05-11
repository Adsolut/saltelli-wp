# Deploy — Studio Legale Saltelli WordPress

> **Scope:** runbook deploy della theme su staging droplet + lessons learned.
> **Production cut:** non ancora avvenuto (DNS `studiolegalesaltelli.it` punta al vecchio sito).
> **Audience:** DevOps, agency tech lead, Claude Code agent.

---

## 1. Infrastruttura staging (consolidata)

| Parametro | Valore |
|---|---|
| Provider | DigitalOcean |
| Droplet | `saltelli-staging-ams3-01` |
| IPv4 | `178.62.207.50` |
| Region | ams3 (Amsterdam) |
| Size | s-1vcpu-2gb |
| OS | Ubuntu 24.04 LTS |
| Hostname dominio | `staging.studiolegalesaltelli.it` |
| HTTPS | Let's Encrypt · auto-renew certbot.timer · notAfter `2026-07-29` |
| Stack | nginx 1.24 · PHP 8.2.30 · MySQL 8.0.45 · WP-CLI 2.12.0 |
| Path WP | `/var/www/saltelli/` |
| Path theme | `/var/www/saltelli/wp-content/themes/saltelli/` |
| DB name | `saltelli_wp` |
| User SSH | `deploy@178.62.207.50` (key-based) |
| Web server user | `www-data:www-data` |

### Plugin attivi

```
advanced-custom-fields    6.8.0    (ACF Free)
contact-form-7            6.1.5
honeypot                  2.3.04
wordpress-seo (Yoast)     27.5
```

⚠️ **Aggiornamenti automatici plugin disattivati**. Aggiornamenti manuali coordinati con il team tecnico.

---

## 2. Workflow deploy standard (delta rsync)

Il pattern usato durante recovery v1.0 (Wave 1+2+3 + Debug QA): **rsync delta** dei file theme, niente full re-deploy.

### Step 1 — Pre-deploy (in locale)

```bash
cd /Users/aldosantoro/Desktop/DEV/saltelli-wp
git status   # working tree pulita
git checkout main
git pull --ff-only origin main

# Verifica integrità
grep "SALTELLI_THEME_VERSION" wp-content/themes/saltelli/functions.php | head -1
head -10 wp-content/themes/saltelli/style.css | grep Version
```

Le due versioni devono combaciare (es. entrambi `1.0.0-recovery-wave3-debug`).

### Step 2 — Backup pre-deploy (su droplet)

```bash
ssh deploy@178.62.207.50

# Backup theme tar.gz
sudo -u www-data tar czf /home/deploy/backups/saltelli-theme-$(date +%Y%m%d-%H%M%S).tar.gz \
    /var/www/saltelli/wp-content/themes/saltelli/

# Backup DB dump
sudo -u www-data wp db export - --path=/var/www/saltelli | \
    gzip > /home/deploy/backups/saltelli-db-$(date +%Y%m%d-%H%M%S).sql.gz

ls -lh /home/deploy/backups/ | tail -5
```

⚠️ **Backup OBBLIGATORIO** prima di ogni deploy. Lo abbiamo dimenticato 1 volta in Wave 2 e siamo stati fortunati.

### Step 3 — Rsync delta theme

Da locale:

```bash
rsync -avz --delete \
    --rsync-path="sudo rsync" \
    --exclude '.DS_Store' \
    --exclude '*.bak' \
    --exclude 'node_modules' \
    -e "ssh" \
    wp-content/themes/saltelli/ \
    deploy@178.62.207.50:/var/www/saltelli/wp-content/themes/saltelli/
```

`--rsync-path="sudo rsync"` è **obbligatorio**: dopo il primo deploy lo Step 4 chowna il theme dir a `www-data`, quindi l'utente `deploy` non ha più write diretto su quei file/dir — rsync deve girare via `sudo` lato remoto (l'utente `deploy` ha NOPASSWD sudo, niente `requiretty`). Senza questo flag il rsync fallisce con `rsync: [receiver] mkstemp ... Permission denied (13)` / `failed to set times on ...: Operation not permitted`. Per deploy chirurgici di pochi file, rsync il singolo path esatto (es. `… inc/helpers.php deploy@…:/var/www/saltelli/wp-content/themes/saltelli/inc/helpers.php`) sempre con `--rsync-path="sudo rsync"`.

`--delete` rimuove file lato server che non sono più in locale (idempotente). Usalo solo se sicuro.

### Step 4 — Permission fix + cache flush

```bash
ssh deploy@178.62.207.50

# Fix ownership
sudo chown -R www-data:www-data /var/www/saltelli/wp-content/themes/saltelli/

# WP cache + transient flush
sudo -u www-data wp cache flush --path=/var/www/saltelli
sudo -u www-data wp transient delete --all --path=/var/www/saltelli

# nginx + PHP-FPM reload (se modificate file PHP critici)
sudo systemctl reload nginx
sudo systemctl reload php8.2-fpm
```

### Step 5 — Smoke verification

Da locale:

```bash
URLS=("/" "/lo-studio/" "/avvocati/" "/avvocati/emiliano-saltelli/"
      "/avvocati/fabiana-saltelli/" "/avvocati/antonia-battista/"
      "/avvocati/stefano-gaetano-tedesco/" "/casi/" "/costi/" "/contatti/"
      "/faq/" "/come-lavoriamo/" "/prima-consulenza/" "/lavora-con-noi/"
      "/richiedi-preventivo/" "/guide-gratuite/"
      "/competenze/diritto-tributario/" "/competenze/diritto-del-lavoro/"
      "/competenze/diritto-di-famiglia-lgbtq/" "/tipo-area/privati/"
      "/glossario-legale/")

PASS=0; FAIL=0
for U in "${URLS[@]}"; do
    code=$(curl -L -o /dev/null -s -w "%{http_code}" \
        "https://staging.studiolegalesaltelli.it${U}" --max-time 10)
    [ "$code" = "200" ] && PASS=$((PASS+1)) || { FAIL=$((FAIL+1)); echo "FAIL $code  $U"; }
done
echo "Smoke: $PASS PASS · $FAIL FAIL"
```

**Atteso: 21/21 PASS** (con `-L` follow redirect per `/lo-studio/` → `/chi-siamo/`).

### Step 6 — Deploy ACF Field Group (se modificati)

Se hai modificato file `acf-json/*.json`:

```bash
ssh deploy@178.62.207.50

# Sync da local files (ACF rilegge automaticamente i JSON)
sudo -u www-data wp acf sync --path=/var/www/saltelli
```

### Step 7 — Deploy data migration script (se applicabile)

Per script di migrazione/fix DB (es. Wave 2 o Debug QA bug-04 fix):

```bash
# Trasferisci lo script
scp scripts/{nome-script}.php deploy@178.62.207.50:/tmp/

# Esegui via WP-CLI
ssh deploy@178.62.207.50
sudo -u www-data wp eval-file /tmp/{nome-script}.php --path=/var/www/saltelli
```

⚠️ **Script idempotenti** sempre. Se uno script non è idempotente, riscrivilo.

---

## 3. Deploy completo (full re-deploy)

Raramente necessario. Solo se:
- Cambio infra (es. PHP version upgrade)
- Recovery da disastro
- Migrazione produzione

In quel caso vedi `_archive/prompts-completed/deploy/PROMPT_AGENT_G_DEPLOY_DIGITALOCEAN.md` per la procedura completa step-by-step (ma alcune Fasi 7-8 sono ancora aperte/non eseguite formalmente).

---

## 4. Lessons learned (da Wave 1+2+3 + Debug QA)

### Lesson 1 — Page IDs NON sono portabili tra ambienti

Local Docker WordPress crea page IDs sequenziali. Droplet ha IDs diversi per le stesse pagine (perché contiene anche post legacy del vecchio sito).

**Conseguenza**: ogni script di migrazione/fix che usa `update_field('x', $value, HARDCODED_ID)` rompe su un ambiente diverso da dove è stato scritto.

**Pattern corretto**:
```php
$page = get_page_by_path('costi');
if ($page) {
    update_field('hero_eyebrow', '...', $page->ID);
}
```

**ACF location rule corretta**:
```json
{"param": "page_slug", "operator": "==", "value": "costi"}
```

(Il `page_slug ==` è una **custom location rule** definita in `inc/acf-fields.php`, non standard ACF.)

### Lesson 2 — Plugin `acf-pro` NON è installato — usiamo ACF Free

Tutto il modello dati è scritto per ACF Free 6.8 (nessun repeater, nessun flexible content native). I "repeater" del progetto sono **fake repeater via CPT modulari** (es. `saltelli_modalita` invece che ACF repeater field).

Non tentare di usare repeater field nei JSON — fallback ACF Free non funziona.

### Lesson 3 — Yoast SEO emette schema graph proprio

Il theme **NON** deve duplicare schema Organization, WebPage, BreadcrumbList, WebSite — Yoast li emette già.

Il theme emette **solo** schema NON gestiti da Yoast: Person/Attorney, FAQPage (sui Tier-1), LegalService custom.

Coabitazione gestita via filter `wpseo_schema_graph` (priority 13, in `inc/seo/yoast-schema-extensions.php`).

### Lesson 4 — Yoast 27.5 NON sempre emette canonical (bug noto)

Trovato in Debug QA bug-01. Il theme ora emette canonical su `wp_head` priority 3 **sempre**, indipendentemente dal SEO plugin attivo. Eventuali duplicati con Yoast vengono dedupati dai crawler.

### Lesson 5 — Backup pre-deploy SEMPRE

Mai deployare senza backup. Il droplet ha `cron` per backup nightly ma il backup pre-deploy manuale ha salvato Wave 2 (1 volta).

### Lesson 6 — Cache flush dopo OGNI deploy

WP cache + transient. Se modifichi schema o template, anche reload PHP-FPM. Senza cache flush, alcune modifiche restano invisibili per 24h+ (transient persistono).

### Lesson 7 — Smoke 21 URL come gate-keeper post-deploy

Se anche solo 1 URL non torna 200, **ROLLBACK** il commit appena deployato (rsync indietro dal backup).

---

## 5. Cut produzione (futuro — non ancora eseguito)

Quando Wave 4 chiude e Elena/Ludovica danno il clear sui loro acceptance test:

### Step 1 — Ultima validazione su staging

- Lighthouse audit ≥92 P / ≥95 A11y / 100 SEO / ≥95 BP (Wave 4 deliverable)
- Smoke 21/21 PASS
- Acceptance test editorial signed-off da Elena
- Form CF7 invio email end-to-end OK

### Step 2 — Bump version finale

```bash
# functions.php + style.css → 1.0.0
git commit -m "release: v1.0.0"
git tag v1.0.0
git push origin main --tags
```

### Step 3 — Deploy produzione

**Production cut richiede decisione**: stesso droplet (rinominato `saltelli-prod-ams3-01`) o droplet nuovo?

Opzioni:
- **A**: rinomina droplet staging → prod, sposta DNS, decommissiona vecchio sito
- **B**: nuovo droplet prod, rsync da staging, sposta DNS, mantieni staging come ambiente test

**Raccomandazione**: B (separazione staging/prod). Cost +$6/mese ma sanity infra.

### Step 4 — DNS switch

A record `studiolegalesaltelli.it` → IP droplet prod.

TTL ridotto a 300s 24h prima per minimizzare downtime. Switch durante orario di basso traffico (notte).

### Step 5 — HTTPS

Let's Encrypt deve coprire entrambi `studiolegalesaltelli.it` + `www.studiolegalesaltelli.it`.

```bash
sudo certbot --nginx -d studiolegalesaltelli.it -d www.studiolegalesaltelli.it
```

### Step 6 — Comunicazione cliente

Email a Avv. Saltelli + Elena con:
- URL nuovo sito live
- Accesso WP-Admin
- Manuale `EDITOR-HANDOFF.md`
- Sessione formazione 30 min
- Canali support (tech@adsolut.it)

### Step 7 — Old site decommissioning

Backup completo del vecchio sito Elementor → archive.
Vecchio hosting cancellato a 30 giorni dal switch.

---

## 6. Troubleshooting

### Sito offline post-deploy

```bash
# Check nginx
ssh deploy@178.62.207.50
sudo systemctl status nginx
sudo nginx -t   # syntax check

# Check PHP-FPM
sudo systemctl status php8.2-fpm

# Check error log
sudo tail -50 /var/log/nginx/saltelli.error.log
sudo tail -50 /var/log/php8.2-fpm.log
```

### Errore PHP fatal su una pagina specifica

```bash
ssh deploy@178.62.207.50
sudo -u www-data wp --path=/var/www/saltelli debug ALL --log
```

Aggiungere temporaneamente `WP_DEBUG=true` in `wp-config.php` (rimuovere dopo).

### Rollback rapido (theme files)

```bash
# Ripristina dal backup pre-deploy
ssh deploy@178.62.207.50
sudo tar xzf /home/deploy/backups/saltelli-theme-{TIMESTAMP}.tar.gz -C /
sudo chown -R www-data:www-data /var/www/saltelli/wp-content/themes/saltelli/
sudo -u www-data wp cache flush --path=/var/www/saltelli
sudo systemctl reload php8.2-fpm
```

### Rollback DB

```bash
ssh deploy@178.62.207.50
gunzip -c /home/deploy/backups/saltelli-db-{TIMESTAMP}.sql.gz | \
    sudo -u www-data wp db import - --path=/var/www/saltelli
```

---

## 7. Maintenance routine

| Frequenza | Task |
|---|---|
| Settimanale | Verifica spazio disco droplet · backup nightly OK |
| Mensile | Plugin updates (manual, coordinati) · WordPress core update |
| Trimestrale | Audit Lighthouse · audit schema JSON-LD · review backup retention |
| Annuale | OS upgrade · PHP upgrade · MySQL major upgrade |

---

## 8. Contatti

| Ruolo | Persona | Contatto |
|---|---|---|
| Project lead | Aldo Santoro (Duccio) | duccio@adsolut.it |
| Tech support | Adsolut staff | tech@adsolut.it |
| Cliente | Avv. Emiliano Saltelli | info@studiolegalesaltelli.it |
| Editor primario | Elena Cappabianca | (interno Adsolut) |
| Editor secondario | Ludovica Casa | (interno Adsolut) |

---

*Maintained by orchestrator (Claude in chat). Last updated: 2026-05-05.*
*Lessons learned aggiornate dopo ogni wave/incident.*
