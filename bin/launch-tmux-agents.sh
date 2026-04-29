#!/usr/bin/env zsh
# =============================================================================
# Saltelli WP — Multi-agent tmux launcher
# =============================================================================
# Apre una sessione tmux 'saltelli' con 4 pane:
#
#   ┌─────────────────────────┬─────────────────────────┐
#   │  Pane 0: AGENT 1        │  Pane 1: AGENT 2        │
#   │  Style & Animation      │  Theme Architect        │
#   ├─────────────────────────┼─────────────────────────┤
#   │  Pane 2: AGENT 3        │  Pane 3: CONTROL        │
#   │  GEO Engineer           │  (logs, git, test)      │
#   └─────────────────────────┴─────────────────────────┘
#
# In ogni pane apre Claude Code dentro il progetto e gli passa il prompt.
#
# Usage:
#   zsh bin/launch-tmux-agents.sh         # full launch
#   zsh bin/launch-tmux-agents.sh --kill  # kill sessione esistente
# =============================================================================

set -euo pipefail

PROJECT_DIR="/Users/aldosantoro/Desktop/DEV/saltelli-wp"
SESSION="saltelli"

# Comando Claude Code — adatta se il binary è diverso sul tuo Mac
# Possibili: claude / claude-code / cc / npx claude
CLAUDE_CMD="${CLAUDE_CMD:-claude}"

# ===== FLAGS =====
if [[ "${1:-}" == "--kill" ]]; then
    tmux kill-session -t "$SESSION" 2>/dev/null && echo "✓ Sessione '$SESSION' killed" || echo "Nessuna sessione '$SESSION' attiva"
    exit 0
fi

# ===== PRE-CHECK =====
if ! command -v tmux &>/dev/null; then
    echo "✗ tmux non installato. Esegui: brew install tmux"
    exit 1
fi

if ! command -v "$CLAUDE_CMD" &>/dev/null; then
    echo "⚠ Comando '$CLAUDE_CMD' non trovato in PATH."
    echo "  Verifica con: which claude-code, which cc, alias | grep claude"
    echo "  Poi rilancia con: CLAUDE_CMD=<binary> zsh $0"
    echo ""
    echo "Per ora apro la sessione tmux comunque, lancia tu manualmente Claude Code"
    echo "in ogni pane usando il comando giusto + paste del prompt indicato."
    CLAUDE_CMD=""
fi

if [[ ! -d "$PROJECT_DIR" ]]; then
    echo "✗ Project dir non trovata: $PROJECT_DIR"
    exit 1
fi

# Se sessione esiste già, attacca senza ricrearla
if tmux has-session -t "$SESSION" 2>/dev/null; then
    echo "⚠ Sessione '$SESSION' già esistente. Attacco."
    tmux attach-session -t "$SESSION"
    exit 0
fi

# ===== CREA SESSIONE 4-PANE =====
echo "Creating tmux session '$SESSION' with 4 panes..."

# Crea sessione con prima pane (Agent 1 — Style & Animation)
tmux new-session -d -s "$SESSION" -n "build" -c "$PROJECT_DIR"

# Split orizzontale → 2 pane affiancate (0 e 1)
tmux split-window -h -t "$SESSION:0" -c "$PROJECT_DIR"

# Split verticale del pane 0 → ora 0 sopra, 2 sotto
tmux split-window -v -t "$SESSION:0.0" -c "$PROJECT_DIR"

# Split verticale del pane 1 (ora pane 2 con la nuova numerazione) → 1 sopra, 3 sotto
tmux split-window -v -t "$SESSION:0.1" -c "$PROJECT_DIR"

# Layout uniforme
tmux select-layout -t "$SESSION:0" tiled

# ===== ETICHETTE PANE =====
tmux select-pane -t "$SESSION:0.0" -T "AGENT 1 — Style & Animation"
tmux select-pane -t "$SESSION:0.1" -T "AGENT 2 — Theme Architect"
tmux select-pane -t "$SESSION:0.2" -T "AGENT 3 — GEO Engineer"
tmux select-pane -t "$SESSION:0.3" -T "CONTROL — git/logs/test"

# Pane border title visibili
tmux set-option -t "$SESSION" pane-border-status top
tmux set-option -t "$SESSION" pane-border-format "#{?pane_active,#[reverse],}#{pane_index} · #{pane_title}#[default]"

# ===== INVIA COMANDI AI PANE =====
# Pane 0 — Agent 1
if [[ -n "$CLAUDE_CMD" ]]; then
    tmux send-keys -t "$SESSION:0.0" "echo '🎨 STYLE & ANIMATION AGENT — apro Claude Code...' && $CLAUDE_CMD" Enter
    sleep 1
    tmux send-keys -t "$SESSION:0.0" "Leggi PROMPT_AGENT_1_STYLE_ANIMATION.md ed eseguilo. Quando hai finito, mostrami il report finale strutturato secondo le 5 voci richieste."
else
    tmux send-keys -t "$SESSION:0.0" "echo 'Lancia manualmente Claude Code qui, poi incolla:' && echo 'Leggi PROMPT_AGENT_1_STYLE_ANIMATION.md ed eseguilo.'" Enter
fi

# Pane 1 — Agent 2
if [[ -n "$CLAUDE_CMD" ]]; then
    tmux send-keys -t "$SESSION:0.1" "echo '🏗  THEME ARCHITECT AGENT — apro Claude Code...' && $CLAUDE_CMD" Enter
    sleep 1
    tmux send-keys -t "$SESSION:0.1" "Leggi PROMPT_AGENT_2_THEME_ARCHITECT.md ed eseguilo. Quando hai finito, mostrami il report finale strutturato secondo le 6 voci richieste."
else
    tmux send-keys -t "$SESSION:0.1" "echo 'Lancia manualmente Claude Code qui, poi incolla:' && echo 'Leggi PROMPT_AGENT_2_THEME_ARCHITECT.md ed eseguilo.'" Enter
fi

# Pane 2 — Agent 3
if [[ -n "$CLAUDE_CMD" ]]; then
    tmux send-keys -t "$SESSION:0.2" "echo '🤖 GEO ENGINEER AGENT — apro Claude Code...' && $CLAUDE_CMD" Enter
    sleep 1
    tmux send-keys -t "$SESSION:0.2" "Leggi PROMPT_AGENT_3_GEO_ENGINEER.md ed eseguilo. Quando hai finito, mostrami il report finale strutturato secondo le 6 voci richieste."
else
    tmux send-keys -t "$SESSION:0.2" "echo 'Lancia manualmente Claude Code qui, poi incolla:' && echo 'Leggi PROMPT_AGENT_3_GEO_ENGINEER.md ed eseguilo.'" Enter
fi

# Pane 3 — Control (git status loop + tail debug.log)
tmux send-keys -t "$SESSION:0.3" "clear && echo '🛠  CONTROL PANE' && echo '' && echo 'Comandi utili durante il build:' && echo '  watch -n 5 \"git status --short\"' && echo '  docker compose logs -f wordpress' && echo '  docker exec saltelli-wp tail -f /var/www/html/wp-content/debug.log'" Enter

# Focus sul pane 0 (Agent 1)
tmux select-pane -t "$SESSION:0.0"

# Attach
echo ""
echo "✓ Session '$SESSION' creata con 4 pane."
echo ""
echo "Layout:"
echo "  ┌──────────────────────────┬──────────────────────────┐"
echo "  │  0 · Style & Animation   │  1 · Theme Architect     │"
echo "  ├──────────────────────────┼──────────────────────────┤"
echo "  │  2 · GEO Engineer        │  3 · CONTROL             │"
echo "  └──────────────────────────┴──────────────────────────┘"
echo ""
echo "Hotkey utili:"
echo "  Ctrl-b 0/1/2/3      → switch tra pane"
echo "  Ctrl-b z            → zoom/unzoom pane corrente"
echo "  Ctrl-b d            → detach (sessione resta viva in background)"
echo "  tmux attach -t $SESSION   → riattach"
echo "  zsh $0 --kill       → kill della sessione"
echo ""

if [[ -z "$CLAUDE_CMD" ]]; then
    echo "⚠ Claude Code non auto-lanciato. In ogni pane:"
    echo "  1. Apri Claude Code manualmente (qualunque sia il comando sul tuo sistema)"
    echo "  2. Incolla il prompt 'Leggi PROMPT_AGENT_X_*.md ed eseguilo.'"
    echo ""
fi

echo "Apro la sessione..."
tmux attach-session -t "$SESSION"
