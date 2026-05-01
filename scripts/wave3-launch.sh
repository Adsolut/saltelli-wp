#!/usr/bin/env bash
# ═══════════════════════════════════════════════════════════════
# Saltelli — Wave 3 Parallel Launcher (TMUX 5 panes)
# ═══════════════════════════════════════════════════════════════
# v2.0 — bash 3.2 compatible (no declare -A)
# Lancia 5 agenti Claude Code in parallelo nei panes tmux.
# Task 10 (404) viene fatto separatamente in Wave 4 dall'orchestrator
# perché è breve e non vale un pane dedicato.
#
# Fix vs script precedente:
#   1. tmux load-buffer (binary-safe, no quote escape)
#   2. paste-buffer + Enter ESPLICITO con sleep 0.5s+
#   3. has-session + cleanup checks
#   4. Path completo claude CLI (no PATH issues nei subshell)
#   5. Indexed array (NO declare -A) — bash 3.2 macOS compatible
# ═══════════════════════════════════════════════════════════════

set -euo pipefail

SESSION="saltelli-wave3"
REPO="/Users/aldosantoro/Desktop/DEV/saltelli-wp"
PROMPTS_DIR="$REPO/.claude/knowledge/design/sessione-2/wave3-prompts"
LOCK_DIR="/tmp/saltelli-agents"

# ───── Pre-checks ─────
[[ -d "$REPO" ]] || { echo "❌ Repo not found: $REPO"; exit 1; }
command -v tmux >/dev/null || { echo "❌ tmux not installed (brew install tmux)"; exit 1; }

# Detecta claude CLI cercando nei path comuni macOS
CLAUDE_BIN=""
for p in "$HOME/.local/bin/claude" "/opt/homebrew/bin/claude" "/usr/local/bin/claude" "$HOME/.npm-global/bin/claude"; do
    if [[ -x "$p" ]]; then CLAUDE_BIN="$p"; break; fi
done
if command -v claude >/dev/null 2>&1; then CLAUDE_BIN="$(command -v claude)"; fi
[[ -n "$CLAUDE_BIN" ]] || { echo "❌ 'claude' CLI not found"; exit 1; }
echo "✓ claude CLI: $CLAUDE_BIN"
echo "✓ bash version: $BASH_VERSION"

# ───── Cleanup previous session ─────
if tmux has-session -t "$SESSION" 2>/dev/null; then
    echo "⚠️  Session '$SESSION' esiste già. Killing..."
    tmux kill-session -t "$SESSION"
    sleep 1
fi

# Cleanup lock-files vecchi (eventuali zombie)
rm -f "$LOCK_DIR"/task-*.lock 2>/dev/null || true

mkdir -p "$LOCK_DIR" "$PROMPTS_DIR"
mkdir -p "$REPO/.claude/knowledge/design/sessione-2/wave3"

echo ""
echo "📝 Generating 5 prompt files in $PROMPTS_DIR..."

# ═══════════════════════════════════════════════════════════════
# PROMPT TASK 5 — /casi/
# ═══════════════════════════════════════════════════════════════
cat > "$PROMPTS_DIR/task-05-casi.txt" <<'EOF_T5'
v0.19.0 Wave 3 PARALLEL AGENT TASK 5 — /casi/

COORDINATION
1. Claim: if [ -f /tmp/saltelli-agents/task-05.lock ]; then echo TAKEN; exit 1; fi
   echo "agent $$ $(date +%H:%M)" > /tmp/saltelli-agents/task-05.lock
2. Files OK: page.php (blocco is_page('casi')), sections.css (markers WAVE3 TASK 5), inc/helpers.php (saltelli_cases_full).
3. NO-TOUCH: tokens.css, functions.php, style.css, header.php, footer.php, single-*, altri lock.
4. CSS scope: /* === WAVE3 TASK 5 (casi) BEGIN === */ ... /* === WAVE3 TASK 5 (casi) END === */
5. Source JSX: .claude/knowledge/design/sessione-2/saltelli-s2-casi.jsx (LAYOUT SACRO)

TASK
Match JSX:
- Hero "Casi rappresentativi" + lede italic editoriale
- Filter tab tipo-area (Privati/Imprese/Contenzioso/Altri) data-filter
- Lista casi tipografica: id mono sx | desc italic centro | outcome bronze dx
- Hover row: traslazione 8px + linea bronze 1px
- 8-10 casi visibili (estendi attuali 4)
- Helper saltelli_cases_full() che estende saltelli_homepage_cases() con 4-6 casi sourcing dai blog post sentenze (TARI, fermi, intimazioni)

Schema markup hint: CollectionPage + ItemList Article entries.

HARD RULES
- LAYOUT JSX SACRO
- Cache flush + smoke test SOLO http://localhost:8080/casi/
- Fine task: rm /tmp/saltelli-agents/task-05.lock + segnala "Task 5 DONE"

OUTPUT
1. Branch feat/wave3-task-05 + push origin
2. Report .claude/knowledge/design/sessione-2/wave3/REPORT-task-05.md
3. STOP. Aspetta merge Wave 4.

PARTI ORA. Procedi con il task.
EOF_T5

# ═══════════════════════════════════════════════════════════════
# PROMPT TASK 6 — /contatti/
# ═══════════════════════════════════════════════════════════════
cat > "$PROMPTS_DIR/task-06-contatti.txt" <<'EOF_T6'
v0.19.0 Wave 3 PARALLEL AGENT TASK 6 — /contatti/

COORDINATION
1. Claim: if [ -f /tmp/saltelli-agents/task-06.lock ]; then echo TAKEN; exit 1; fi
   echo "agent $$ $(date +%H:%M)" > /tmp/saltelli-agents/task-06.lock
2. Files OK: page.php (blocco is_page('contatti')), sections.css (markers WAVE3 TASK 6), inc/contact-form.php (preserva backend logic).
3. NO-TOUCH: tokens.css, functions.php, style.css, header.php, footer.php, single-*, altri lock.
4. CSS scope: /* === WAVE3 TASK 6 (contatti) BEGIN === */ ... /* === WAVE3 TASK 6 (contatti) END === */
5. Source JSX: saltelli-s2-contatti.jsx (LAYOUT SACRO)

TASK
Match JSX:
- Layout 2-col 8fr/4fr (form sx | NAP+orari+map dx)
- Form 8 fields: nome*, email*, telefono, area interesse (select 19), data preferita, messaggio*, GDPR consent*, submit "Prenota gratuita →"
- Field styling editoriale underline-only border (NO box), label uppercase mono 11px
- Map OSM 320px height bordered (già v0.13.6, verifica match)
- Sotto map: NAP + orari + 3 click-to-action (Tel · Email · WhatsApp)
- Sezione "Come arrivare" mini con metro/parcheggio (NUOVO)
- Trust signal footer: "Riceviamo solo su appuntamento. Risposta entro 24 ore."

CRITICO: NON sovrascrivere logica handler form esistente. Solo restyle frontend.

Schema: ContactPage + LocalBusiness + GeoCoordinates.

HARD RULES
- LAYOUT JSX SACRO
- Form handler backend PRESERVATO
- Smoke test SOLO http://localhost:8080/contatti/
- Fine: rm lock + segnala "Task 6 DONE"

OUTPUT
1. Branch feat/wave3-task-06 + push
2. Report wave3/REPORT-task-06.md
3. STOP.

PARTI ORA.
EOF_T6

# ═══════════════════════════════════════════════════════════════
# PROMPT TASK 7 — /blog/
# ═══════════════════════════════════════════════════════════════
cat > "$PROMPTS_DIR/task-07-blog.txt" <<'EOF_T7'
v0.19.0 Wave 3 PARALLEL AGENT TASK 7 — /blog/ archive editoriale

COORDINATION
1. Claim: if [ -f /tmp/saltelli-agents/task-07.lock ]; then echo TAKEN; exit 1; fi
   echo "agent $$ $(date +%H:%M)" > /tmp/saltelli-agents/task-07.lock
2. Files OK: home.php OR archive.php (verifica quale WP usa per /blog/), sections.css (markers WAVE3 TASK 7).
3. NO-TOUCH: tokens.css, functions.php, style.css, page.php, single-*, taxonomy-*, altri lock.
4. CSS scope: /* === WAVE3 TASK 7 (blog) BEGIN === */ ... /* === WAVE3 TASK 7 (blog) END === */
5. Source JSX: saltelli-s2-blog-archive.jsx (LAYOUT SACRO)

TASK
Match JSX:
- Hero archive: eyebrow "EDITORIALE · Saltelli" + h1 "Editoriale" Playfair gigante + lede italic + counter mono "X articoli · 12 categorie"
- Category filter tabs (6-7 categorie, mono uppercase, hover bronze underline grow)
- Featured post hero (1 article primo, layout 2-col 8fr/4fr)
- Articles list grid 3-col desktop / 2-col tablet / 1-col mobile
- Card: image 4:3, category mono 11px, titolo Playfair 22px, excerpt 16px, footer card mono
- Card hover: image scale(1.03) 600ms quart-out + category bronze
- Pagination editorial minimal "1 — 12 di 326"
- Sidebar opzionale (verifica nel JSX): "Categorie" + "Autori" + "Newsletter inline"

Schema: Blog + ItemList Article entries.

HARD RULES
- LAYOUT JSX SACRO
- Smoke test SOLO http://localhost:8080/blog/
- Fine: rm lock + segnala "Task 7 DONE"

OUTPUT
1. Branch feat/wave3-task-07 + push
2. Report wave3/REPORT-task-07.md
3. STOP.

PARTI ORA.
EOF_T7

# ═══════════════════════════════════════════════════════════════
# PROMPT TASK 8 — /tipo-area/
# ═══════════════════════════════════════════════════════════════
cat > "$PROMPTS_DIR/task-08-taxonomy.txt" <<'EOF_T8'
v0.19.0 Wave 3 PARALLEL AGENT TASK 8 — /tipo-area/{slug}/ taxonomy

COORDINATION
1. Claim: if [ -f /tmp/saltelli-agents/task-08.lock ]; then echo TAKEN; exit 1; fi
   echo "agent $$ $(date +%H:%M)" > /tmp/saltelli-agents/task-08.lock
2. Files OK: taxonomy-tipo-area.php, sections.css (markers WAVE3 TASK 8).
3. NO-TOUCH: tokens.css, functions.php, style.css, page.php, home.php, single-*, altri lock.
4. CSS scope: /* === WAVE3 TASK 8 (taxonomy) BEGIN === */ ... /* === WAVE3 TASK 8 (taxonomy) END === */
5. Source JSX: saltelli-s2-taxonomy-tipo-area.jsx (LAYOUT SACRO)

TASK
Variabili term: privati / imprese / contenzioso / altri.

Match JSX:
- Hero asimmetrico 8fr/4fr:
  - Sx: breadcrumb mono + h1 Playfair gigante + lede italic + counter "9 aree"
  - Dx: 1-2 mini-card avvocati specialisti (foto 80x80 + nome + ruolo)
- Sezione "Quando rivolgersi" 3-col scenari tipici per cluster
- Sezione lista aree (pattern .sl-area homepage, NO grid card, lista tipografica)
- Sezione "Casi rappresentativi cluster" (filter dinamico tier)
- CTA finale "Prenota gratuita"

Schema: CollectionPage + ItemList LegalService entries.

HARD RULES
- LAYOUT JSX SACRO
- Smoke test su tutti i 4 termini:
  /tipo-area/privati/, /tipo-area/imprese/, /tipo-area/contenzioso/, /tipo-area/altri/
- Fine: rm lock + segnala "Task 8 DONE"

OUTPUT
1. Branch feat/wave3-task-08 + push
2. Report wave3/REPORT-task-08.md
3. STOP.

PARTI ORA.
EOF_T8

# ═══════════════════════════════════════════════════════════════
# PROMPT TASK 9 — /glossario-legale/
# ═══════════════════════════════════════════════════════════════
cat > "$PROMPTS_DIR/task-09-glossario.txt" <<'EOF_T9'
v0.19.0 Wave 3 PARALLEL AGENT TASK 9 — /glossario-legale/ build

COORDINATION
1. Claim: if [ -f /tmp/saltelli-agents/task-09.lock ]; then echo TAKEN; exit 1; fi
   echo "agent $$ $(date +%H:%M)" > /tmp/saltelli-agents/task-09.lock
2. Files OK: page.php (blocco is_page('glossario-legale')), sections.css (markers WAVE3 TASK 9), WP-CLI per popolare post_content.
3. NO-TOUCH: tokens.css, functions.php, style.css, header.php, footer.php, single-*, altri lock.
4. CSS scope: /* === WAVE3 TASK 9 (glossario) BEGIN === */ ... /* === WAVE3 TASK 9 (glossario) END === */
5. Source JSX: saltelli-s2-glossario-legale.jsx (LAYOUT SACRO)

TASK
Page WP esiste (HTTP 200, H1 "Glossario legale") ma con content placeholder vuoto.

Match JSX:
- Hero: eyebrow "RIFERIMENTI" + h1 + lede italic + counter mono "60 termini · 24 categorie"
- Search bar editoriale (underline-only) + a-z navigation A B C D ... Z (sticky)
- Lista termini <dl> semantic HTML5:
  - Term Playfair 24px + categoria mono + pronuncia
  - Definizione 40-60 parole + "Esempio:" italic + "Aree correlate:" link
  - Layout 2-col desktop, 1-col mobile
  - Anchor #termine-slug deep link
- Sezione FAQ glossario bottom (4-5 domande)
- CTA finale

CONTENT 60 TERMINI: genera array PHP con termini comuni del diritto italiano.
Esempi: ricorso, cassazione, atto giudiziale, prescrizione, decadenza, contraddittorio, prima udienza, decreto ingiuntivo, accertamento, intimazione, opposizione, sentenza, appello, riconvenzionale, mediazione, arbitrato, conciliazione, notifica, cartella esattoriale, fermo amministrativo, pignoramento, unione civile, stepchild adoption, separazione, divorzio, eredità, successione, testamento, condominio, usufrutto, ecc.

Aggiorna post_content via WP-CLI eval con HTML completo dei 60 termini.

Schema: DefinedTermSet + DefinedTerm × 60 + FAQPage.

HARD RULES
- LAYOUT JSX SACRO
- 60 termini italiano editoriale chiaro (no jargon)
- Smoke test SOLO http://localhost:8080/glossario-legale/
- Fine: rm lock + segnala "Task 9 DONE"

OUTPUT
1. Branch feat/wave3-task-09 + push
2. Report wave3/REPORT-task-09.md
3. STOP.

PARTI ORA.
EOF_T9

echo "  ✓ 5 prompt files created in $PROMPTS_DIR"
ls -la "$PROMPTS_DIR" | tail -6

# ═══════════════════════════════════════════════════════════════
# Crea tmux session con 5 panes
# ═══════════════════════════════════════════════════════════════
echo ""
echo "🪟 Creating tmux session '$SESSION' con 5 panes..."

tmux new-session -d -s "$SESSION" -n "wave3" -c "$REPO"
tmux set-option -t "$SESSION" mouse on
tmux set-option -t "$SESSION" history-limit 10000
tmux set-option -t "$SESSION" pane-border-status top
tmux set-option -t "$SESSION" pane-border-format " #{pane_index} · #{pane_title} "

# Layout 5 panes: 2 colonne, 3+2 righe (~tiled)
# Pane 0 esiste già di default
tmux split-window -t "$SESSION:0.0" -h -c "$REPO"     # 0 | 1
tmux split-window -t "$SESSION:0.0" -v -c "$REPO"     # 0/2 | 1
tmux split-window -t "$SESSION:0.1" -v -c "$REPO"     # 0/2 | 1/3
tmux split-window -t "$SESSION:0.0" -v -c "$REPO"     # 0/2/4 | 1/3
tmux select-layout -t "$SESSION" tiled

# ═══════════════════════════════════════════════════════════════
# Mappa pane → task (INDEXED ARRAY, bash 3.2 compatible)
# ═══════════════════════════════════════════════════════════════
PANE_FILES=(
    "task-05-casi.txt"
    "task-06-contatti.txt"
    "task-07-blog.txt"
    "task-08-taxonomy.txt"
    "task-09-glossario.txt"
)
PANE_LABELS=(
    "task-05"
    "task-06"
    "task-07"
    "task-08"
    "task-09"
)

# ═══════════════════════════════════════════════════════════════
# Bulletproof prompt sender
# ═══════════════════════════════════════════════════════════════
send_prompt_to_pane() {
    local pane=$1
    local prompt_file=$2
    local task_label=$3

    # Imposta titolo del pane
    tmux select-pane -t "$SESSION:0.$pane" -T "$task_label"

    # Banner introduttivo nel pane
    tmux send-keys -t "$SESSION:0.$pane" "clear" Enter
    tmux send-keys -t "$SESSION:0.$pane" "echo '═══ $task_label ═══'" Enter
    tmux send-keys -t "$SESSION:0.$pane" "echo 'Prompt: $(basename "$prompt_file")'" Enter
    tmux send-keys -t "$SESSION:0.$pane" "echo ''" Enter

    # Avvia 'claude' CLI con path completo (no PATH issues nei subshell)
    tmux send-keys -t "$SESSION:0.$pane" "$CLAUDE_BIN" Enter
    sleep 5   # attesa CLI ready (5s safe vs 4s)

    # Carica prompt nel buffer (binary-safe, NO escape problems)
    tmux load-buffer -b "buf-$pane" "$prompt_file"

    # Paste buffer nel pane
    tmux paste-buffer -b "buf-$pane" -t "$SESSION:0.$pane"
    sleep 1.5   # attesa propagation

    # Enter ESPLICITO (questo era il bug del precedente .sh)
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
echo "🚀 Sending prompts to 5 panes (staggered 5s)..."

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
echo "✅ 5 agenti lanciati in parallelo nella session '$SESSION'."
echo ""
echo "📺 Attach:           tmux attach -t $SESSION"
echo "🔍 Monitor locks:    watch -n 5 'ls /tmp/saltelli-agents/'"
echo "📊 Quando vuota →    tutti gli agenti hanno finito"
echo "🛑 Kill all:         tmux kill-session -t $SESSION"
echo ""
echo "Dopo che tutti sono done:"
echo "  1. tmux kill-session -t $SESSION"
echo "  2. Lancia PROMPT 8 ORCHESTRATOR Wave 4 nell'agent originale"
echo "     (include Task 10 404 sequenziale + merge + bump + deploy)"
echo "═══════════════════════════════════════════════════════════════"
