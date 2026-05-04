#!/usr/bin/env bash
# ═══════════════════════════════════════════════════════════════
# Saltelli — Wave 1 Parallel Launcher (TMUX 3 panes)
# ═══════════════════════════════════════════════════════════════
# v1.0 — Wave 1 ACF Field Groups setup parallel × 3 agent
#
# Pattern testato in v0.34/wave3-launch.sh:
#   1. tmux load-buffer (binary-safe, no quote escape)
#   2. paste-buffer + send-keys Enter ESPLICITO con sleep
#   3. Path completo claude CLI (no PATH issues subshell)
#   4. Indexed array (bash 3.2 compat)
# ═══════════════════════════════════════════════════════════════

set -euo pipefail

SESSION="saltelli-wave1"
REPO="/Users/aldosantoro/Desktop/DEV/saltelli-wp"
PROMPTS_DIR="$REPO/.claude/knowledge/recovery/wave1-prompts"
LOCK_DIR="/tmp/saltelli-agents-w1"

# ───── Pre-checks ─────
[[ -d "$REPO" ]] || { echo "❌ Repo not found: $REPO"; exit 1; }
command -v tmux >/dev/null || { echo "❌ tmux not installed (brew install tmux)"; exit 1; }

# Detecta claude CLI
CLAUDE_BIN=""
for p in "$HOME/.local/bin/claude" "/opt/homebrew/bin/claude" "/usr/local/bin/claude" "$HOME/.npm-global/bin/claude"; do
    if [[ -x "$p" ]]; then CLAUDE_BIN="$p"; break; fi
done
if command -v claude >/dev/null 2>&1; then CLAUDE_BIN="$(command -v claude)"; fi
[[ -n "$CLAUDE_BIN" ]] || { echo "❌ 'claude' CLI not found"; exit 1; }
echo "✓ claude CLI: $CLAUDE_BIN"

# ───── Cleanup previous session ─────
if tmux has-session -t "$SESSION" 2>/dev/null; then
    echo "⚠️  Session '$SESSION' esiste già. Killing..."
    tmux kill-session -t "$SESSION"
    sleep 1
fi

rm -f "$LOCK_DIR"/*.lock 2>/dev/null || true
mkdir -p "$LOCK_DIR" "$PROMPTS_DIR"
mkdir -p "$REPO/.claude/knowledge/recovery/wave1"

echo ""
echo "📝 Generating 3 prompt files..."

# ═══════════════════════════════════════════════════════════════
# PROMPT AGENT A — Field Groups page WP custom
# ═══════════════════════════════════════════════════════════════
cat > "$PROMPTS_DIR/agent-a-page-fields.txt" <<'EOF_A'
v1.0.0 Wave 1 PARALLEL AGENT A — Field Groups page WP custom

CONTEXT
Wave 0 completata: ACF Free 6.8.0 attivo + 8 CPT fake repeater registrati.
Sei Agent A. Lavori su Field Groups per page WP custom.
Agent B + C lavorano in parallelo su file DIVERSI in acf-json/.

COORDINATION
1. Claim: 
   if [ -f /tmp/saltelli-agents-w1/agent-a.lock ]; then echo TAKEN; exit 1; fi
   echo "agent-a $$ $(date +%H:%M)" > /tmp/saltelli-agents-w1/agent-a.lock

2. Files OK to create:
   wp-content/themes/saltelli/acf-json/group_costi*.json
   wp-content/themes/saltelli/acf-json/group_casi*.json
   wp-content/themes/saltelli/acf-json/group_contatti*.json
   wp-content/themes/saltelli/acf-json/group_faq*.json
   wp-content/themes/saltelli/acf-json/group_info_shared*.json

3. Tools: WP-CLI eval per acf_add_local_field_group()
   ACF auto-saves JSON in acf-json/ dopo creazione.

4. Path droplet (per future deploy): /var/www/saltelli/ (NO /htdocs).

TASK
Riferimento completo: PROMPT_AGENT_v1.0_WAVE1_FIELD_GROUPS.md sezione AGENT A.

Crea 5 Field Groups per:
  T1 — costi (page id 2695): hero + aside + body + CTA finale
  T2 — casi (page id 2699): hero + intro + CTA
  T3 — contatti: hero + map + come arrivare + trust signal
  T4 — faq: hero + TOC config + CTA
  T5 — info-shared (location OR per 5 page: guide-gratuite, come-lavoriamo, prima-consulenza, lavora-con-noi, richiedi-preventivo)

NB: sezioni con multiple items (Modalità × 3, Scenari × 3, FAQ × 5+, Trust × 4)
NON sono in questi Field Groups: usano CPT separati gestiti da Agent B.

Pattern WP-CLI eval esempio costi:
  docker compose run --rm wpcli eval '
    acf_add_local_field_group([
      "key" => "group_costi_v1",
      "title" => "Costi — Sezioni",
      "fields" => [...],
      "location" => [[["param"=>"page", "operator"=>"==", "value"=>get_page_by_path("costi")->ID]]],
      "active" => true,
    ]);
    echo "OK";
  '

Verify creation:
  ls -la wp-content/themes/saltelli/acf-json/group_costi*.json
  docker compose run --rm wpcli eval "var_dump(acf_get_field_group('group_costi_v1'));"

Output: 5 file JSON in acf-json/ + commit + push origin.

DELIVERABLE
1. 5 Field Groups creati
2. ACF JSON files in acf-json/ (auto-export)
3. Branch git: feat/wave1-agent-a-page-fields
4. Push origin (orchestrator merge in Wave 1 final)
5. Cleanup: rm /tmp/saltelli-agents-w1/agent-a.lock
6. Segnala "Agent A done. 5 Field Groups page WP."

HARD RULES
- NO modifiche frontend (template PHP / CSS)
- NO content migration (solo schema)
- NO modifiche tokens.css
- Field naming: snake_case (no spazi, no maiuscole)
- Field keys unique: prefix field_<group>_<name>
- Smoke test: ACF Field Group registrato in WP-Admin

PARTI ORA. Riferimento completo: PROMPT_AGENT_v1.0_WAVE1_FIELD_GROUPS.md
EOF_A

# ═══════════════════════════════════════════════════════════════
# PROMPT AGENT B — Field Groups CPT
# ═══════════════════════════════════════════════════════════════
cat > "$PROMPTS_DIR/agent-b-cpt-fields.txt" <<'EOF_B'
v1.0.0 Wave 1 PARALLEL AGENT B — Field Groups CPT

CONTEXT
Wave 0 completata: 8 CPT registrati. Sei Agent B.
Agent A + C in parallelo su file DIVERSI.

COORDINATION
1. Claim: /tmp/saltelli-agents-w1/agent-b.lock
2. Files OK:
   wp-content/themes/saltelli/acf-json/group_avvocato*.json
   wp-content/themes/saltelli/acf-json/group_competenza*.json
   wp-content/themes/saltelli/acf-json/group_faq_item*.json (saltelli_faq)
   wp-content/themes/saltelli/acf-json/group_caso_item*.json (saltelli_caso)
   wp-content/themes/saltelli/acf-json/group_modalita_item*.json
   wp-content/themes/saltelli/acf-json/group_scenario_item*.json
   wp-content/themes/saltelli/acf-json/group_principio_item*.json
   wp-content/themes/saltelli/acf-json/group_trust_item*.json
   wp-content/themes/saltelli/acf-json/group_formazione_item*.json
   wp-content/themes/saltelli/acf-json/group_guida_item*.json

TASK
Riferimento completo: PROMPT_AGENT_v1.0_WAVE1_FIELD_GROUPS.md sezione AGENT B.

10 Field Groups:
  T1 — avvocato (4 lawyer): bio_breve, bio_estesa, foto_ritratto,
       hero_role, specializzazioni, contatti diretti, aree_competenza_correlate,
       formazione (post_object multiple), casi_rappresentativi (post_object)
  T2 — competenza (19 aree): is_tier_1, tier_label, subtitle, answer_capsule,
       body_extended, lead_attorneys, casi_rappresentativi, faq, articoli_correlati
  T3 — saltelli_faq: domanda (= title), risposta + faq_topic taxonomy
  T4 — saltelli_caso: id_label, descrizione, outcome_label
  T5 — saltelli_modalita: num_label, title, body, trust_mini
  T6 — saltelli_scenario: num_label, title, body, trust_mini
  T7 — saltelli_principio: num, title, desc
  T8 — saltelli_trust: label, valore
  T9 — saltelli_formazione: anno, titolo, ente
  T10 — saltelli_guida: intro, pdf_file, formato, categoria

Pattern WP-CLI eval per ogni Field Group.

DELIVERABLE
1. 10 Field Groups creati
2. ACF JSON files in acf-json/
3. Branch git: feat/wave1-agent-b-cpt-fields
4. Push origin
5. Cleanup lock
6. Segnala "Agent B done. 10 Field Groups CPT."

HARD RULES same as Agent A.

PARTI ORA. Riferimento completo nel file PROMPT_AGENT_v1.0_WAVE1_FIELD_GROUPS.md sezione AGENT B.
EOF_B

# ═══════════════════════════════════════════════════════════════
# PROMPT AGENT C — Theme Options
# ═══════════════════════════════════════════════════════════════
cat > "$PROMPTS_DIR/agent-c-theme-options.txt" <<'EOF_C'
v1.0.0 Wave 1 PARALLEL AGENT C — Theme Options

CONTEXT
Wave 0 completata: acf_add_options_page registrata in inc/acf-fields.php.
Sei Agent C. Lavori su Field Group "Saltelli Settings" globale.
Agent A + B in parallelo.

COORDINATION
1. Claim: /tmp/saltelli-agents-w1/agent-c.lock
2. File OK to create:
   wp-content/themes/saltelli/acf-json/group_theme_options*.json

TASK
Riferimento completo: PROMPT_AGENT_v1.0_WAVE1_FIELD_GROUPS.md sezione AGENT C.

1 Field Group "Saltelli — Settings globali" con 6 TABS:

TAB 1 — Studio Info:
  studio_indirizzo_via, studio_cap_citta, studio_quartiere
  studio_orari_settimana, studio_orari_sabato
  studio_telefono_pubblico, studio_email, studio_pec
  studio_piva, studio_ordine_avvocati

TAB 2 — Mappa:
  studio_coordinate_lat, studio_coordinate_lng

TAB 3 — Brand:
  brand_payoff (default "Diritto, con misura")
  brand_statement_short (textarea, default brand statement)

TAB 4 — Footer:
  footer_credit_text (default "Realizzato da Adsolut Web Agency")
  footer_credit_url (default https://adsolut.it)
  footer_newsletter_enabled (true_false)
  footer_newsletter_provider (select: brevo/static/none)

TAB 5 — Social:
  social_instagram, social_linkedin, social_twitter, social_facebook (url fields)

TAB 6 — CTA Defaults:
  cta_default_label (default "Prenota un incontro →")
  cta_default_url (default "/contatti/")
  cta_trust_signal (default "Risposta entro 24 ore · Riservatezza assoluta")
  cta_subline_italic (default "Prima consulenza conoscitiva gratuita")

Location: options_page = saltelli-settings

Pattern WP-CLI eval acf_add_local_field_group().

DELIVERABLE
1. 1 Field Group con ~30 fields organizzati in 6 tabs
2. ACF JSON file in acf-json/
3. Branch git: feat/wave1-agent-c-theme-options
4. Push origin
5. Cleanup lock
6. Verify WP-Admin: menu "Saltelli — Settings" mostra 6 tab editabili
7. Segnala "Agent C done. Theme Options Field Group."

HARD RULES same as Agent A+B.

PARTI ORA.
EOF_C

echo "  ✓ 3 prompt files created in $PROMPTS_DIR"
ls -la "$PROMPTS_DIR" | tail -4

# ═══════════════════════════════════════════════════════════════
# Crea tmux session con 3 panes
# ═══════════════════════════════════════════════════════════════
echo ""
echo "🪟 Creating tmux session '$SESSION' con 3 panes..."

tmux new-session -d -s "$SESSION" -n "wave1" -c "$REPO"
tmux set-option -t "$SESSION" mouse on
tmux set-option -t "$SESSION" history-limit 10000
tmux set-option -t "$SESSION" pane-border-status top
tmux set-option -t "$SESSION" pane-border-format " #{pane_index} · #{pane_title} "

# Layout 3 panes: 2 colonne, sinistra = 2 panes verticali, destra = 1 pane
tmux split-window -t "$SESSION:0.0" -h -c "$REPO"      # 0 | 1
tmux split-window -t "$SESSION:0.0" -v -c "$REPO"      # 0/2 | 1
tmux select-layout -t "$SESSION" tiled

# ═══════════════════════════════════════════════════════════════
# Mappa pane → task (INDEXED ARRAY, bash 3.2 compatible)
# ═══════════════════════════════════════════════════════════════
PANE_FILES=(
    "agent-a-page-fields.txt"
    "agent-b-cpt-fields.txt"
    "agent-c-theme-options.txt"
)
PANE_LABELS=(
    "agent-a"
    "agent-b"
    "agent-c"
)

# ═══════════════════════════════════════════════════════════════
# Bulletproof prompt sender (lesson learned)
# ═══════════════════════════════════════════════════════════════
send_prompt_to_pane() {
    local pane=$1
    local prompt_file=$2
    local task_label=$3

    tmux select-pane -t "$SESSION:0.$pane" -T "$task_label"

    tmux send-keys -t "$SESSION:0.$pane" "clear" Enter
    tmux send-keys -t "$SESSION:0.$pane" "echo '═══ $task_label ═══'" Enter
    tmux send-keys -t "$SESSION:0.$pane" "echo 'Prompt: $(basename "$prompt_file")'" Enter
    tmux send-keys -t "$SESSION:0.$pane" "echo ''" Enter

    # Avvia 'claude' CLI con path completo
    tmux send-keys -t "$SESSION:0.$pane" "$CLAUDE_BIN" Enter
    sleep 5   # attesa CLI ready

    # Carica prompt nel buffer (binary-safe)
    tmux load-buffer -b "buf-$pane" "$prompt_file"

    # Paste buffer
    tmux paste-buffer -b "buf-$pane" -t "$SESSION:0.$pane"
    sleep 1.5

    # Enter ESPLICITO
    tmux send-keys -t "$SESSION:0.$pane" Enter
    sleep 0.5

    # Cleanup buffer
    tmux delete-buffer -b "buf-$pane" 2>/dev/null || true

    echo "  ✓ Pane $pane → $task_label"
}

# ═══════════════════════════════════════════════════════════════
# Lancio staggered (5s tra panes)
# ═══════════════════════════════════════════════════════════════
echo ""
echo "🚀 Sending prompts to 3 panes (staggered 5s)..."

NUM_PANES=${#PANE_FILES[@]}
for i in $(seq 0 $((NUM_PANES - 1))); do
    PROMPT_FILE="$PROMPTS_DIR/${PANE_FILES[$i]}"
    LABEL="${PANE_LABELS[$i]}"
    send_prompt_to_pane "$i" "$PROMPT_FILE" "$LABEL"
    sleep 5
done

tmux select-layout -t "$SESSION" tiled

echo ""
echo "═══════════════════════════════════════════════════════════════"
echo "✅ 3 agenti lanciati in parallelo nella session '$SESSION'."
echo ""
echo "📺 Attach:           tmux attach -t $SESSION"
echo "🔍 Monitor locks:    watch -n 5 'ls /tmp/saltelli-agents-w1/'"
echo "📊 Quando vuota →    tutti gli agenti hanno finito"
echo "🛑 Kill all:         tmux kill-session -t $SESSION"
echo ""
echo "Output atteso post Wave 1:"
echo "  - 16 Field Groups in wp-content/themes/saltelli/acf-json/"
echo "  - 3 branch git: feat/wave1-agent-{a,b,c}-*"
echo "  - WP-Admin con menu CPT + Saltelli Settings editabili"
echo ""
echo "Tempo stimato: ~2-3h elapsed."
echo "═══════════════════════════════════════════════════════════════"
