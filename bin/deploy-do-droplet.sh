#!/usr/bin/env bash
# =============================================================================
# Deploy Saltelli WP staging on DigitalOcean droplet
# =============================================================================
# Crea droplet, configura Docker + nginx + Let's Encrypt, deploya il sito.
# Idempotente: rilanciabile in caso di failure parziale.
#
# Pre-requisiti:
#   - doctl autenticato (doctl auth init)
#   - SSH key Adsolut già caricata su DO account
#   - DNS staging.studiolegalesaltelli.it deve essere pronto a puntare al droplet
#     dopo la creazione (gestione DNS lato Adsolut)
#
# Usage:
#   ./bin/deploy-do-droplet.sh             # full deploy
#   ./bin/deploy-do-droplet.sh --dry-run   # mostra cosa farebbe senza eseguire
#   ./bin/deploy-do-droplet.sh --destroy   # distrugge il droplet (cleanup)
# =============================================================================

set -euo pipefail

# ===== CONFIG =====
DROPLET_NAME="saltelli-staging"
REGION="fra1"                      # Frankfurt — best latency Italia
SIZE="s-2vcpu-4gb"                 # $24/mese, sufficiente per staging WP
IMAGE="docker-20-04"               # Ubuntu 22.04 con Docker preinstallato
SSH_KEY_NAME="adsolut-do-deploy"   # Nome della SSH key registrata su DO
TAG="saltelli,staging,wordpress"
DOMAIN="staging.studiolegalesaltelli.it"
EMAIL_LE="info@adsolut.it"         # Per Let's Encrypt
REPO_URL="git@github.com:Adsolut-Ai-Agency/saltelli-wp.git"
REPO_BRANCH="main"

# ===== UTILITIES =====
log()    { echo -e "\033[1;34m[$(date +%H:%M:%S)]\033[0m $*"; }
ok()     { echo -e "\033[1;32m✓\033[0m $*"; }
warn()   { echo -e "\033[1;33m⚠\033[0m $*"; }
err()    { echo -e "\033[1;31m✗\033[0m $*" >&2; }

require() {
    if ! command -v "$1" &>/dev/null; then
        err "Missing required command: $1"
        exit 1
    fi
}

# ===== PRECHECK =====
log "Pre-flight checks..."
require doctl
require git
require ssh

if ! doctl auth list 2>/dev/null | grep -q "current"; then
    err "doctl non autenticato. Esegui: doctl auth init"
    exit 1
fi
ok "doctl autenticato"

# ===== HANDLE FLAGS =====
DRY_RUN=false
DESTROY=false
case "${1:-}" in
    --dry-run) DRY_RUN=true ;;
    --destroy) DESTROY=true ;;
esac

if [[ "$DESTROY" == "true" ]]; then
    log "DESTROY MODE — sto cercando droplet '$DROPLET_NAME'..."
    DROPLET_ID=$(doctl compute droplet list --tag-name saltelli --format ID,Name --no-header | grep "$DROPLET_NAME" | awk '{print $1}' || true)
    if [[ -n "$DROPLET_ID" ]]; then
        warn "Sto per distruggere il droplet ID $DROPLET_ID. Conferma con [y/N]:"
        read -r confirm
        if [[ "$confirm" == "y" ]]; then
            doctl compute droplet delete "$DROPLET_ID" -f
            ok "Droplet $DROPLET_ID distrutto"
        else
            log "Aborted."
        fi
    else
        warn "Nessun droplet con nome '$DROPLET_NAME' trovato"
    fi
    exit 0
fi

# ===== STEP 1 — Verify SSH key esiste su DO =====
log "Step 1/8 — Verifica SSH key su DO..."
SSH_KEY_ID=$(doctl compute ssh-key list --format ID,Name --no-header | grep "$SSH_KEY_NAME" | awk '{print $1}' || true)
if [[ -z "$SSH_KEY_ID" ]]; then
    err "SSH key '$SSH_KEY_NAME' non trovata su DO."
    err "Aggiungila prima con: doctl compute ssh-key import $SSH_KEY_NAME --public-key-file ~/.ssh/id_ed25519.pub"
    exit 1
fi
ok "SSH key trovata: ID $SSH_KEY_ID"

# ===== STEP 2 — Check se droplet esiste già (idempotenza) =====
log "Step 2/8 — Verifica droplet esistente..."
EXISTING=$(doctl compute droplet list --tag-name saltelli --format ID,Name,PublicIPv4 --no-header | grep "$DROPLET_NAME" || true)
if [[ -n "$EXISTING" ]]; then
    DROPLET_ID=$(echo "$EXISTING" | awk '{print $1}')
    DROPLET_IP=$(echo "$EXISTING" | awk '{print $3}')
    warn "Droplet '$DROPLET_NAME' già esistente: ID $DROPLET_ID, IP $DROPLET_IP"
    log "Skippo creazione, vado avanti col provisioning..."
else
    # ===== STEP 3 — Crea droplet =====
    log "Step 3/8 — Creazione droplet '$DROPLET_NAME' ($SIZE in $REGION)..."
    if [[ "$DRY_RUN" == "true" ]]; then
        warn "DRY RUN — comando che eseguirei:"
        echo "doctl compute droplet create $DROPLET_NAME --region $REGION --size $SIZE --image $IMAGE --ssh-keys $SSH_KEY_ID --tag-names saltelli,staging,wordpress --wait"
        exit 0
    fi

    doctl compute droplet create "$DROPLET_NAME" \
        --region "$REGION" \
        --size "$SIZE" \
        --image "$IMAGE" \
        --ssh-keys "$SSH_KEY_ID" \
        --tag-names "saltelli,staging,wordpress" \
        --wait

    DROPLET_ID=$(doctl compute droplet list --tag-name saltelli --format ID,Name --no-header | grep "$DROPLET_NAME" | awk '{print $1}')
    DROPLET_IP=$(doctl compute droplet get "$DROPLET_ID" --format PublicIPv4 --no-header)
    ok "Droplet creato: ID $DROPLET_ID, IP $DROPLET_IP"
fi

# ===== STEP 4 — Aspetta SSH disponibile =====
log "Step 4/8 — Wait SSH ready su $DROPLET_IP..."
for i in {1..30}; do
    if ssh -o StrictHostKeyChecking=no -o ConnectTimeout=5 -o BatchMode=yes "root@$DROPLET_IP" 'echo OK' 2>/dev/null | grep -q OK; then
        ok "SSH ready"
        break
    fi
    [[ $i -eq 30 ]] && { err "Timeout SSH dopo 5 minuti"; exit 1; }
    sleep 10
done

# ===== STEP 5 — Provisioning del droplet (Docker, nginx, certbot) =====
log "Step 5/8 — Provisioning OS + tools..."
ssh -o StrictHostKeyChecking=no "root@$DROPLET_IP" 'bash -s' <<'PROVISION'
set -e

# Update sistema
apt-get update -qq
apt-get install -y -qq nginx certbot python3-certbot-nginx jq git ufw

# Verifica Docker (presente da image docker-20-04 ma sanity check)
if ! command -v docker &>/dev/null; then
    apt-get install -y docker.io docker-compose-plugin
    systemctl enable --now docker
fi

# Firewall
ufw --force reset
ufw default deny incoming
ufw default allow outgoing
ufw allow 22/tcp
ufw allow 80/tcp
ufw allow 443/tcp
ufw --force enable

# Crea utente deploy (security: niente root login)
if ! id -u deploy &>/dev/null; then
    useradd -m -s /bin/bash deploy
    usermod -aG docker deploy
    mkdir -p /home/deploy/.ssh
    cp /root/.ssh/authorized_keys /home/deploy/.ssh/
    chown -R deploy:deploy /home/deploy/.ssh
    chmod 700 /home/deploy/.ssh
    chmod 600 /home/deploy/.ssh/authorized_keys
fi

echo "✓ Provisioning OS completato"
PROVISION
ok "Provisioning OS completato"

# ===== STEP 6 — Clone repo + start docker stack =====
log "Step 6/8 — Clone repo Saltelli + avvio Docker stack..."
ssh -o StrictHostKeyChecking=no "root@$DROPLET_IP" "bash -s" <<PROVISION_DEPLOY
set -e
cd /home/deploy

# Clone repo come utente deploy
sudo -u deploy bash -c "
    cd ~
    if [ ! -d saltelli-wp ]; then
        git clone --depth 1 --branch $REPO_BRANCH $REPO_URL saltelli-wp
    else
        cd saltelli-wp && git fetch origin && git reset --hard origin/$REPO_BRANCH
    fi
"

cd /home/deploy/saltelli-wp

# Sostituisci porte locali con porte 'pubbliche' staging in docker-compose
# (in locale 8080/8081, in produzione lasciamo 8080 dietro nginx reverse proxy)

# Avvia stack
sudo -u deploy docker compose up -d
sleep 15

# Verifica health
sudo -u deploy docker compose ps
PROVISION_DEPLOY
ok "Stack Docker avviato"

# ===== STEP 7 — Configura nginx reverse proxy + SSL =====
log "Step 7/8 — Setup nginx reverse proxy + Let's Encrypt..."
ssh -o StrictHostKeyChecking=no "root@$DROPLET_IP" "bash -s" <<PROVISION_NGINX
set -e

# Nginx config
cat > /etc/nginx/sites-available/$DOMAIN <<'NGINX_CONF'
server {
    listen 80;
    server_name $DOMAIN;

    # Body size for media uploads
    client_max_body_size 64M;

    location / {
        proxy_pass http://127.0.0.1:8080;
        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto \$scheme;
        proxy_buffering off;
    }

    # phpMyAdmin (proteggi con basic auth in produzione vera)
    location /pma/ {
        proxy_pass http://127.0.0.1:8081/;
        proxy_set_header Host \$host;
        proxy_set_header X-Real-IP \$remote_addr;
        # auth_basic "phpMyAdmin";  # TODO Duccio: aggiungere htpasswd
        # auth_basic_user_file /etc/nginx/.htpasswd;
    }
}
NGINX_CONF

ln -sf /etc/nginx/sites-available/$DOMAIN /etc/nginx/sites-enabled/$DOMAIN
rm -f /etc/nginx/sites-enabled/default
nginx -t
systemctl reload nginx

# Let's Encrypt (richiede DNS già propagato — se fallisce, certbot ritenta dopo)
# Tenta automatico, se DNS non propagato salta SSL e logga
if dig +short $DOMAIN | grep -q "$DROPLET_IP"; then
    certbot --nginx -d $DOMAIN --non-interactive --agree-tos --email $EMAIL_LE --redirect || \
        echo "⚠ Certbot fallito — DNS forse non ancora propagato. Riprova: certbot --nginx -d $DOMAIN"
else
    echo "⚠ DNS $DOMAIN NON punta ancora a $DROPLET_IP — SSL setup manuale necessario dopo propagazione"
fi
PROVISION_NGINX
ok "Nginx + SSL configurati"

# ===== STEP 8 — Import DB + post-deploy hooks =====
log "Step 8/8 — Import DB Saltelli + URL search-replace..."
ssh -o StrictHostKeyChecking=no "root@$DROPLET_IP" "bash -s" <<POST_DEPLOY
cd /home/deploy/saltelli-wp

# TODO Duccio: scp del DB dump dal Mac al droplet, oppure import dal repo se compresso
# Per ora: assume che il dump sia accessibile in db-dump/baseline-*.sql

# WP-CLI search-replace URL locali → staging
DUMP=\$(ls -t db-dump/saltelli_*.sql 2>/dev/null | grep -v baseline | head -1)
if [ -n "\$DUMP" ]; then
    sudo -u deploy docker exec -i saltelli-db mysql -u saltelli -psaltelli_dev saltelli_wp < "\$DUMP"
    sudo -u deploy docker compose run --rm wpcli search-replace 'http://localhost:8080' 'https://$DOMAIN' --all-tables --skip-columns=guid
    sudo -u deploy docker compose run --rm wpcli search-replace 'localhost:8080' '$DOMAIN' --all-tables --skip-columns=guid
    sudo -u deploy docker compose run --rm wpcli rewrite flush --hard
    sudo -u deploy docker compose run --rm wpcli theme activate saltelli || true
    echo "✓ DB importato + URL aggiornati"
else
    echo "⚠ Nessun DB dump trovato in db-dump/. Importa manualmente."
fi
POST_DEPLOY

# ===== Output finale =====
log ""
log "=========================================="
log "  ✓ DEPLOY COMPLETATO"
log "=========================================="
log "Droplet:  $DROPLET_NAME (ID $DROPLET_ID)"
log "IP:       $DROPLET_IP"
log "URL HTTP: http://$DOMAIN"
log "URL HTTPS: https://$DOMAIN (se SSL configurato)"
log "phpMyAdmin: https://$DOMAIN/pma/"
log ""
log "TODO post-deploy:"
log "  1. Verifica DNS: dig +short $DOMAIN  → deve puntare a $DROPLET_IP"
log "  2. Se SSL non è attivo: ssh root@$DROPLET_IP 'certbot --nginx -d $DOMAIN'"
log "  3. Smoke test: curl -I https://$DOMAIN"
log "  4. Lighthouse: lanciare da Chrome DevTools"
log "  5. Schema validation: validator.schema.org → inserisci https://$DOMAIN"
log ""
ok "Done."
