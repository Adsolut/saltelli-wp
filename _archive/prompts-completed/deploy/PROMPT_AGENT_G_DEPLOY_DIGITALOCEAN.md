# Prompt — Step G: Deploy DigitalOcean (`staging.studiolegalesaltelli.it`)

> **Stato 2026-04-30 (post-aggiornamento 3):**
> ✅ Fase 0 — droplet attivo (`saltelli-staging-ams3-01` / `178.62.207.50`)
> ✅ Fase 1 — hardening (deploy user, UFW, fail2ban, SSH no-root, swap)
> ✅ Fase 2 — LEMP (nginx 1.24, PHP 8.2.30, MySQL 8.0.45, WP-CLI, DB `saltelli_wp` vuoto)
> ✅ Fase 5 — nginx vhost + holding page + SSL Let's Encrypt valido fino al 2026-07-29
> ✅ Fase 6 — DNS `staging.studiolegalesaltelli.it → 178.62.207.50`
> ⏸ Fase 3 — WP install (in attesa GO orchestratore)
> ⏸ Fase 4 — Migrazione DB + uploads dal locale (in attesa GO orchestratore)
> ⏸ Fase 7 — Smoke test post-deploy
> ⏸ Fase 8 — Hand-off + monitoring
>
> **URL pubblico ora:** https://staging.studiolegalesaltelli.it → holding page `noindex,nofollow` con design system Saltelli (palette navy/cream/bronze, Playfair + DM Sans).
> **Tempo residuo stimato dal GO:** ~30-45 minuti (Fasi 3+4+7+8).
> **Secrets locali:** `~/Desktop/DEV/saltelli-wp/.saltelli-staging-secrets` (gitignored, 600).
> **Secrets droplet:** `/root/.saltelli-secrets` + `/home/deploy/.saltelli-secrets` (600).
> **Pending kernel reboot:** kernel 6.8.0-110 installato, in esecuzione 6.8.0-71 — reboot consigliato dopo Fase 4 al GO.

---

## Stato di partenza atteso

- Branch `main`, tag annunciato dall'orchestratore (al momento siamo `0.13.0` IA Unification — il tag prod sarà `1.0.0` o successivo a seguire `PROMPT_AGENT_F_PRODUCTION_READINESS.md`).
- `wp-content/themes/saltelli/` presente, design tokens locked.
- DB locale `saltelli_wp` su `saltelli-db` (MySQL 8) — 85 MB, 326 post, 19 competenze, 4 avvocati, foto Emiliano `_thumbnail_id=2683`.
- Iubenda attivo (banner + privacy/cookie/TOS) — vedi nota su DOMDocument round-trip nei JSON-LD.
- `config.local.json` (gitignored) contiene credenziali interne — **NON va sul droplet**.
- doctl autenticato come `info@adsolut.it` (team `My Team`).

---

## Decisioni di sizing (locked)

| Parametro | Valore | Motivo |
|---|---|---|
| Slug | `s-1vcpu-2gb` (Basic Regular) | RAM corretta per MySQL 8 + PHP-FPM + nginx con ~1GB content; allineato a `adsolut-apps-ams3-01` |
| Region | `ams3` | Adsolut infra già in ams3, latenza IT ~25 ms |
| Image | `ubuntu-24-04-x64` | LTS più recente |
| SSH key | `aldosantoro-macbook` (51341481) + `Adsolut` (27542203) | accesso operativo + backup team |
| Project | `Adsolut Web Agency` (7435d1b3-530a-4848-8117-293c4e7d3349) | progetto canonico |
| Nome droplet | `saltelli-staging-ams3-01` | naming convention Adsolut |
| Tag DO | `saltelli`,`staging`,`wordpress` | filtri/billing |
| Backup automatici DO | ON ($2.40/mo) | rete di sicurezza pre-produzione |
| Monitoring | ON (gratuito) | metriche CPU/disk/net |

Costo mensile staging: **~$14.40** (12$ droplet + 2.40$ backup).
Resize a `s-2vcpu-4gb` (24$) quando si va in produzione e si vuole margine per AI crawlers.

---

## Fase 0 — Provisioning droplet — ✅ COMPLETATA 2026-04-30

```
ID droplet:       568158213
Nome:             saltelli-staging-ams3-01
IPv4:             178.62.207.50
IPv6:             2a03:b0c0:2:f0:0:1:926d:7001
Region:           ams3
Size:             s-1vcpu-2gb (2GB RAM, 1 vCPU, 50GB SSD)
Image:            ubuntu-24.04.3-lts-x64
SSH keys:         aldosantoro-macbook (51341481), Adsolut (27542203)
Backup DO:        ON
Monitoring:       ON
IPv6:             ON
Tag DO:           saltelli, staging, wordpress
Project:          Adsolut Web Agency (7435d1b3-530a-4848-8117-293c4e7d3349)
Costo:            $14.40/mo ($0.0179/h droplet + ~$2.40/mo backup)
Smoke test SSH:   OK (root@178.62.207.50 risponde)
RAM disponibile:  1.5Gi (394Mi usati out-of-box)
Disco libero:     46Gi su 48Gi
Swap:             0 (verrà aggiunto in Fase 1)
```

Comando eseguito:

```bash
doctl compute droplet create saltelli-staging-ams3-01 \
  --image ubuntu-24-04-x64 --size s-1vcpu-2gb --region ams3 \
  --ssh-keys 51341481,27542203 \
  --enable-backups --enable-monitoring --enable-ipv6 \
  --tag-names saltelli,staging,wordpress --wait
doctl projects resources assign 7435d1b3-530a-4848-8117-293c4e7d3349 \
  --resource=do:droplet:568158213
```

**Stato attuale:** droplet attivo e raggiungibile via SSH, vuoto. In attesa di GO orchestratore per Fase 1.
Per fermare il billing prima del GO: `doctl compute droplet delete 568158213` (power-off NON azzera il billing su DO).

---

## Fase 1 — Hardening base (15 min)

Connesso come root via SSH:

```bash
# Aggiorna sistema
apt update && apt upgrade -y

# Crea utente deploy con sudo (no root SSH dopo)
adduser --gecos "" --disabled-password deploy
usermod -aG sudo deploy
mkdir -p /home/deploy/.ssh
cp ~/.ssh/authorized_keys /home/deploy/.ssh/
chown -R deploy:deploy /home/deploy/.ssh
chmod 700 /home/deploy/.ssh
chmod 600 /home/deploy/.ssh/authorized_keys

# Sudo NOPASSWD per deploy (operativo, niente prompt durante runbook)
echo "deploy ALL=(ALL) NOPASSWD:ALL" > /etc/sudoers.d/deploy
chmod 440 /etc/sudoers.d/deploy

# SSH hardening: niente root login, niente password, porta 22
sed -i 's/^#*PermitRootLogin.*/PermitRootLogin no/' /etc/ssh/sshd_config
sed -i 's/^#*PasswordAuthentication.*/PasswordAuthentication no/' /etc/ssh/sshd_config
systemctl reload ssh

# UFW
ufw default deny incoming
ufw default allow outgoing
ufw allow OpenSSH
ufw allow 'Nginx Full'
ufw --force enable

# fail2ban + unattended-upgrades
apt install -y fail2ban unattended-upgrades
dpkg-reconfigure -plow unattended-upgrades  # interattivo: rispondere Yes

# Swap 2GB (utile per PHP build/composer occasionali)
fallocate -l 2G /swapfile
chmod 600 /swapfile
mkswap /swapfile && swapon /swapfile
echo '/swapfile none swap sw 0 0' >> /etc/fstab

# Timezone Italia
timedatectl set-timezone Europe/Rome
```

Verifica: `ssh deploy@<IP>` deve funzionare; `ssh root@<IP>` deve essere rifiutato.

---

## Fase 2 — Stack LEMP (20 min)

Come `deploy`:

```bash
# nginx
sudo apt install -y nginx
sudo systemctl enable --now nginx

# PHP 8.2 + estensioni richieste da WP
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update
sudo apt install -y php8.2-fpm php8.2-cli php8.2-mysql php8.2-curl \
  php8.2-gd php8.2-mbstring php8.2-xml php8.2-zip php8.2-imagick \
  php8.2-intl php8.2-bcmath php8.2-soap php8.2-opcache

# Tuning php.ini
sudo sed -i 's/^upload_max_filesize.*/upload_max_filesize = 128M/' /etc/php/8.2/fpm/php.ini
sudo sed -i 's/^post_max_size.*/post_max_size = 128M/'           /etc/php/8.2/fpm/php.ini
sudo sed -i 's/^memory_limit.*/memory_limit = 256M/'             /etc/php/8.2/fpm/php.ini
sudo sed -i 's/^max_execution_time.*/max_execution_time = 120/'  /etc/php/8.2/fpm/php.ini
sudo sed -i 's/^expose_php.*/expose_php = Off/'                  /etc/php/8.2/fpm/php.ini

# OPcache produzione
sudo tee /etc/php/8.2/fpm/conf.d/99-opcache.ini >/dev/null <<'EOF'
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.validate_timestamps=0
opcache.revalidate_freq=0
EOF
sudo systemctl restart php8.2-fpm

# MySQL 8
sudo apt install -y mysql-server
sudo mysql_secure_installation  # interattivo: scegliere policy LOW (le password vengono iniettate da noi)

# DB + utente WP (sostituire <STRONG_PWD> con password generata)
DB_PWD=$(openssl rand -base64 24 | tr -d '/+=' | head -c 32)
sudo mysql -uroot <<EOF
CREATE DATABASE saltelli_wp DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'saltelli'@'localhost' IDENTIFIED WITH mysql_native_password BY '$DB_PWD';
GRANT ALL PRIVILEGES ON saltelli_wp.* TO 'saltelli'@'localhost';
FLUSH PRIVILEGES;
EOF
echo "DB_PWD=$DB_PWD" >> ~/.saltelli-secrets  # salvare anche in 1Password Adsolut

# WP-CLI
sudo curl -sL https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar -o /usr/local/bin/wp
sudo chmod +x /usr/local/bin/wp
```

---

## Fase 3 — WordPress core + theme deploy (15 min)

```bash
# Doc root
sudo mkdir -p /var/www/saltelli
sudo chown deploy:www-data /var/www/saltelli
cd /var/www/saltelli

# WP core (stessa major del local: 6.x)
wp core download --version=6.7 --locale=it_IT
wp config create --dbname=saltelli_wp --dbuser=saltelli --dbpass="$DB_PWD" --dbhost=localhost --dbprefix=wp_

# Aggiungi config produzione (debug off, https forzato dopo certbot)
wp config set WP_DEBUG false --raw
wp config set WP_DEBUG_LOG false --raw
wp config set WP_DEBUG_DISPLAY false --raw
wp config set DISALLOW_FILE_EDIT true --raw
wp config set WP_AUTO_UPDATE_CORE minor
wp config set WP_MEMORY_LIMIT 256M
wp config set FORCE_SSL_ADMIN true --raw

# Salt keys freschi
wp config shuffle-salts

# Install (la URL definitiva diventerà https://staging.studiolegalesaltelli.it dopo certbot)
wp core install \
  --url="http://staging.studiolegalesaltelli.it" \
  --title="Studio Legale Emiliano Saltelli & Partners" \
  --admin_user="saltelli_admin" \
  --admin_password="$(openssl rand -base64 24 | tr -d '/+=' | head -c 24)" \
  --admin_email="info@adsolut.it" \
  --skip-email

# Theme — clone da repo privato Adsolut-Ai-Agency/saltelli-wp
cd wp-content/themes
gh auth status || gh auth login  # se necessario, su deploy user
git clone git@github.com:Adsolut-Ai-Agency/saltelli-wp.git _src
ln -s _src/wp-content/themes/saltelli ./saltelli

# Plugins: Yoast, Iubenda, eventuali altri presenti in local
# Strategia: rsync da locale o reinstall via wp-cli
wp plugin install wordpress-seo --activate
wp plugin install iubenda-cookie-law-solution --activate
# (altri plugin esistenti vanno listati con `wp plugin list` su locale e replicati)

wp theme activate saltelli
```

---

## Fase 4 — Migrazione contenuti (DB + uploads) (20 min)

**Dal locale (laptop Aldo):**

```bash
# Export DB (escludi transient, sessioni)
docker exec saltelli-db mysqldump -uroot -proot saltelli_wp \
  --single-transaction --skip-lock-tables \
  --ignore-table=saltelli_wp.wp_options \
  > /tmp/saltelli_db_main.sql

# Export wp_options separato senza siteurl/home (li riscrive WP installato)
docker exec saltelli-db mysqldump -uroot -proot saltelli_wp wp_options \
  --where="option_name NOT IN ('siteurl','home','_transient%')" \
  > /tmp/saltelli_db_options.sql

# Upload sul droplet
scp /tmp/saltelli_db_*.sql deploy@$DROPLET_IP:/tmp/
rsync -avz --progress \
  /Users/aldosantoro/Desktop/DEV/saltelli-wp/wp-content/uploads/ \
  deploy@$DROPLET_IP:/var/www/saltelli/wp-content/uploads/
```

**Sul droplet:**

```bash
cd /var/www/saltelli
mysql -usaltelli -p"$DB_PWD" saltelli_wp < /tmp/saltelli_db_main.sql
mysql -usaltelli -p"$DB_PWD" saltelli_wp < /tmp/saltelli_db_options.sql

# Search-replace URL: localhost:8080 → staging.studiolegalesaltelli.it
wp search-replace 'http://localhost:8080' 'https://staging.studiolegalesaltelli.it' --all-tables --report-changed-only
wp search-replace 'localhost:8080' 'staging.studiolegalesaltelli.it' --all-tables --report-changed-only

# Permessi
sudo chown -R deploy:www-data /var/www/saltelli
sudo find /var/www/saltelli -type d -exec chmod 755 {} \;
sudo find /var/www/saltelli -type f -exec chmod 644 {} \;
sudo chmod 640 /var/www/saltelli/wp-config.php

# Cache flush + permalink rebuild
wp cache flush
wp rewrite flush --hard
wp transient delete --all
```

---

## Fase 5 — nginx vhost + SSL — ✅ COMPLETATA 2026-04-30

```
✅ Doc root /var/www/saltelli (deploy:www-data, 755)
✅ Holding page index.html (design system Saltelli, ~2.7KB, noindex+nofollow)
✅ Vhost /etc/nginx/sites-available/saltelli con HTTPS auto-redirect
✅ Cert Let's Encrypt:
   issuer:     C=US, O=Let's Encrypt, CN=E8
   subject:    CN=staging.studiolegalesaltelli.it
   notBefore:  2026-04-30 09:57:49 GMT
   notAfter:   2026-07-29 09:57:48 GMT
   path:       /etc/letsencrypt/live/staging.studiolegalesaltelli.it/
✅ TLS 1.2 + TLS 1.3 supportati (cipher: AES256-GCM-SHA384)
✅ Auto-renew via certbot.timer (next: 2026-05-01 01:29 CEST)
✅ HTTP → HTTPS 301 Moved Permanently
✅ wp-config.php / xmlrpc.php / dotfile → 404/deny
```

**Note operative per Fase 3 al GO:**
- Il vhost ha già `location ~ \.php$` configurato per PHP-FPM 8.2 socket → quando arriva WP basta sostituire `try_files $uri $uri/ /index.html` con `try_files $uri $uri/ /index.php?$args`.
- Holding page in `/var/www/saltelli/index.html` va rimossa (o rinominata in `_holding.html`) prima del `wp core install`, altrimenti WP non risponde sulla `/`.

Vhost finale (post-certbot) è già produttivo. Snippet originale conservato qui per riferimento:

```bash
# Vhost
sudo tee /etc/nginx/sites-available/saltelli >/dev/null <<'EOF'
server {
    listen 80;
    listen [::]:80;
    server_name staging.studiolegalesaltelli.it;
    root /var/www/saltelli;
    index index.php index.html;

    client_max_body_size 128M;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Permissions-Policy "geolocation=(), microphone=(), camera=()" always;

    # Static caching (theme assets)
    location ~* \.(css|js|woff2?|jpg|jpeg|png|webp|avif|svg|ico)$ {
        expires 30d;
        access_log off;
        add_header Cache-Control "public, immutable";
    }

    # llms.txt + robots.txt serviti dal tema
    location = /llms.txt   { try_files $uri /index.php?$args; }
    location = /robots.txt { try_files $uri /index.php?$args; }

    # WP standard
    location / { try_files $uri $uri/ /index.php?$args; }

    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_read_timeout 120s;
    }

    # Hardening
    location ~ /\.(?!well-known).* { deny all; }
    location = /xmlrpc.php { deny all; }
    location ~* /(?:wp-config\.php|readme\.html|license\.txt) { deny all; }
}
EOF
sudo ln -s /etc/nginx/sites-available/saltelli /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t && sudo systemctl reload nginx

# Certbot (Let's Encrypt) — DOPO che il DNS punta al droplet
sudo apt install -y certbot python3-certbot-nginx
sudo certbot --nginx -d staging.studiolegalesaltelli.it --email info@adsolut.it --agree-tos --no-eff-email --redirect
```

**Cron rinnovo:** già installato da pacchetto certbot, verifica con `systemctl list-timers | grep certbot`.

---

## Fase 6 — DNS — ✅ COMPLETATA 2026-04-30

Record A `staging.studiolegalesaltelli.it → 178.62.207.50` propagato e verificato su tutti i resolver pubblici:

```
$ dig staging.studiolegalesaltelli.it @1.1.1.1 +short  → 178.62.207.50
$ dig staging.studiolegalesaltelli.it @8.8.8.8 +short  → 178.62.207.50
$ dig staging.studiolegalesaltelli.it @9.9.9.9 +short  → 178.62.207.50
TTL osservato: ~300s (basso, rollback rapido garantito)
```

Pre-condizione per `certbot --nginx` (Fase 5 SSL): ✅ soddisfatta.
Provider DNS: gestito lato cliente/orchestratore (record già piazzato — nessuna ulteriore azione richiesta sul registrar).
Eventuali nuovi record (es. `www.staging`, MX, SPF) NON sono in scope per questo runbook.

---

## Fase 7 — Smoke test + Lighthouse

```bash
# HTTP code 200 sulle pagine chiave
for URL in \
  "/" \
  "/lo-studio/" \
  "/avvocati/" \
  "/avvocati/emiliano-saltelli/" \
  "/competenze/" \
  "/competenze/diritto-tributario/" \
  "/casi/" \
  "/costi/" \
  "/blog/" \
  "/contatti/" \
  "/llms.txt"; do
    code=$(curl -s -o /dev/null -w "%{http_code}" "https://staging.studiolegalesaltelli.it$URL")
    echo "$code  $URL"
done

# Schema JSON-LD parse-check
curl -s https://staging.studiolegalesaltelli.it/ \
  | grep -oE '<script type="application/ld\+json">[^<]*</script>' \
  | sed 's/<[^>]*>//g' \
  | python3 -c "import sys,json; [json.loads(l) and print('✓ valid:', json.loads(l).get('@type','?')) for l in sys.stdin if l.strip()]"

# Foto Emiliano (regression check Step C.5)
curl -sI "$(wp option get siteurl)/wp-content/uploads/$(wp post meta get 2683 _wp_attached_file)"
# Atteso: HTTP/2 200
```

Lighthouse mobile + desktop su 4 URL canonici. Target da `PROMPT_AGENT_F_PRODUCTION_READINESS.md` (Performance ≥ 92, A11y ≥ 95).

---

## Fase 8 — Hand-off + monitoring

- [ ] Aggiornare `CLAUDE.md` → "Deployed staging v1.0.0 on 2026-XX-XX, droplet `saltelli-staging-ams3-01` (IP X.X.X.X)"
- [ ] Salvare `~/.saltelli-secrets` (DB pwd, wp admin pwd, IP) in 1Password vault Adsolut "Studio Saltelli"
- [ ] Inviare a Emiliano: URL + credenziali admin temporanee + nota "robots.txt blocca crawler indicizzazione finché staging"
- [ ] Su droplet: `wp option update blog_public 0` per evitare indicizzazione staging
- [ ] DigitalOcean monitoring alerts: CPU >80% per 5min, Disk >85%, Bandwidth >80%
- [ ] Backup automatici DO già attivi (Fase 0); aggiungere in `/etc/cron.daily/saltelli-db-backup`:
  ```bash
  #!/bin/bash
  ts=$(date +%Y%m%d-%H%M)
  mysqldump -usaltelli -p"$DB_PWD" saltelli_wp | gzip > /var/backups/saltelli-db-$ts.sql.gz
  find /var/backups -name 'saltelli-db-*.sql.gz' -mtime +14 -delete
  ```

---

## Rollback rapidi

| Scenario | Azione |
|---|---|
| Sito rotto post-deploy, DB ok | `cd /var/www/saltelli/wp-content/themes/_src && git checkout <last-good-tag>` + `wp cache flush` |
| DB corrotto | `gunzip -c /var/backups/saltelli-db-<ts>.sql.gz \| mysql -usaltelli -p saltelli_wp` |
| Droplet completamente compromesso | `doctl compute droplet-action restore <DROPLET_ID> --image-id <DO_BACKUP_ID>` (backup automatici Fase 0) |
| DNS punta storto | abbassa TTL a 60s prima di ogni cambio; tieni IP precedente in nota per back-out |

---

## Open questions per l'orchestratore (Duccio)

1. **Step F (Production Readiness) prima del deploy?** Lighthouse / WOFF2 / SRI vanno fatti prima di pubblicare lo staging, o accettiamo staging "as-is" v0.13.0 e lo passiamo a Emiliano per feedback prima di chiudere F?
2. **DNS provider:** chi gestisce le DNS di `studiolegalesaltelli.it`? Serve credenziali o richiesta a Emiliano.
3. **Email transazionale:** WP manda mail (form Contatti, password reset). Vogliamo SMTP esterno (es. SendGrid/SES) o accettiamo Postfix locale (rischio spam-flag)?
4. **Iubenda site key produzione:** quella locale va bene anche per staging o serve key separata?
5. **HTTP Basic Auth davanti allo staging?** Per evitare visite indesiderate finché Emiliano non revisiona. (Una riga nel vhost.)

---

*v1.0 · runbook deploy DigitalOcean · pronto per esecuzione manuale al GO orchestratore*
