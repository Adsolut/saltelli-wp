## Copia questo prompt e incollalo in Claude Code nella cartella del progetto.

---

Sei il LEAD AGENT del progetto Studio Legale Saltelli — WordPress Custom Theme. Il tuo compito è coordinare la realizzazione di un tema WordPress custom ultra-professionale per uno studio legale di Napoli.

## CONTESTO PROGETTO

Client: Studio Legale Emiliano Saltelli & Partners, Napoli (quartiere Chiaia) Fornitore: Adsolut SRLS — AI Agency Contratto: €14.000 / 7 mesi — Programma GEO completo Fase attuale: Fase 1 — GEO Audit + Nuovo sito WordPress AI-ready Brief completo: `BRIEF_Saltelli_WordPress.md` (nella root del progetto)

## REGOLE SICUREZZA CREDENZIALI (NON NEGOZIABILI)

Le credenziali del cliente (SSH host, user, chiave privata, passphrase, password DB, application password WP) vivono **solo** in `config.local.json` (gitignored) o in `~/.ssh/` (chiave privata). Mai altrove.

**Cosa NON fai mai:**

- Non stampare valori sensibili a terminale (host, user, password, path SSH key, ecc.)
- Non includere credenziali in commit, log, file di output, dump del progetto, screenshot
- Non incollare credenziali in messaggi a Duccio o ad altri agent
- Non scrivere credenziali in file diversi da `config.local.json`
- Non eseguire comandi che salvano credenziali in shell history (usa `set +o history` se necessario)
- Non leakare credenziali via error message verbose

**Cosa fai sempre:**

- Leggi `config.local.json` con `jq` o equivalente, e usa i valori solo come variabili shell scope-locale (`$SSH_HOST`, `$SSH_USER`, ecc.)
- Verifica che `.gitignore` contenga `config.local.json` PRIMA di ogni `git add` (è già lì, ma riverifica)
- In caso di dubbio, fermati e chiedi a Duccio
- Se identifichi un leak accidentale (commit, log, screenshot), segnala IMMEDIATAMENTE a Duccio prima di proseguire

## PRIMO STEP — PHASE 0: SITE DUMP + SETUP AMBIENTE

Prima di scrivere una sola riga di codice del tema, esegui Phase 0 in questo ordine.

### 0.1 — Verifica le credenziali in `config.local.json`

Il file `config.local.json` (gitignored, fuori dal repo Git) contiene SSH host/user/key_path, WP path, credenziali database remote, application password. Se non esiste, copialo dal template:

```bash
```

```
```

cp config.local.example.json config.local.json

# poi l'umano (Duccio) compila i TODO

```

**REGOLA NON NEGOZIABILE:** non stampare mai a console, non loggare, non committare valori da `config.local.json`. Riferiti ai campi solo per nome (es. "ho letto ssh.host da config.local.json") senza esporli.

### 0.2 — Test connessione SSH

```bash
# Su macOS: assicurati che ssh-agent abbia la chiave
ssh-add --apple-use-keychain ~/.ssh/saltelli_id_ed25519 2>/dev/null || true

# Test base
ssh -i $(jq -r '.ssh.key_path' config.local.json) \
    $(jq -r '.ssh.user' config.local.json)@$(jq -r '.ssh.host' config.local.json) \
    "wp core version --allow-root --path=$(jq -r '.wordpress_remote.wp_path' config.local.json)"
```

Output atteso: numero versione WordPress (es. `6.8.3`). Se errore, fermarsi e segnalare a Duccio.

### 0.3 — Dump del database remoto

```bash
SSH_HOST=$(jq -r '.ssh.host' config.local.json)
SSH_USER=$(jq -r '.ssh.user' config.local.json)
SSH_KEY=$(jq -r '.ssh.key_path' config.local.json)
WP_PATH=$(jq -r '.wordpress_remote.wp_path' config.local.json)
DUMP_FILE="db-dump/saltelli_$(date +%Y%m%d-%H%M%S).sql"

mkdir -p db-dump
ssh -i "$SSH_KEY" "$SSH_USER@$SSH_HOST" \
    "cd $WP_PATH && wp db export - --allow-root" > "$DUMP_FILE"

echo "Dump salvato in: $DUMP_FILE ($(du -h $DUMP_FILE | cut -f1))"
```

### 0.4 — Dump dei file (uploads, plugin attivi, eventuali custom)

```bash
mkdir -p saltelli-dump/wp-content
rsync -avz --progress \
    -e "ssh -i $SSH_KEY" \
    "$SSH_USER@$SSH_HOST:$WP_PATH/wp-content/uploads/" \
    saltelli-dump/wp-content/uploads/

# Plugin: scarichiamo SOLO la lista, NON il codice (i plugin si reinstallano puliti)
ssh -i "$SSH_KEY" "$SSH_USER@$SSH_HOST" \
    "cd $WP_PATH && wp plugin list --format=json --allow-root" \
    > saltelli-dump/plugin-list.json
```

### 0.5 — Avvia ambiente Docker locale

```bash
docker compose up -d
docker compose ps   # tutti i servizi devono essere "running" / "healthy"
```

### 0.6 — Importa il dump nel database locale

```bash
docker exec -i saltelli-db mysql -u saltelli -psaltelli_dev saltelli_wp < "$DUMP_FILE"

# Aggiorna gli URL per il dev locale (search-replace serializzato, NON sed)
docker compose run --rm wpcli search-replace \
    'https://studiolegalesaltelli.it' 'http://localhost:8080' --all-tables
```

### 0.7 — Verifica che il sito locale risponda

Apri `http://localhost:8080` nel browser. Deve apparire il sito attuale di Saltelli (con tema Elementor). Da qui parte lo sviluppo del tema custom.

### 0.8 — Backup snapshot baseline
