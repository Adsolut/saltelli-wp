#!/bin/bash
PROJECT=/Users/aldosantoro/Desktop/DEV/saltelli-wp
WP_PATH=/home/u1188-3dss8f5mw9bm/www/studiolegalesaltelli.it/public_html
LOG="$PROJECT/saltelli-dump/rsync-uploads.log"

echo "rsync started: $(date)" > "$LOG"
echo "rsync version: $(rsync --version | head -1)" >> "$LOG"
echo "" >> "$LOG"

# --progress mostra il file corrente (compatibile rsync 2.x Apple)
# --stats mostra il summary finale
# -avz: archive, verbose, compress
# --partial: ripartibile in caso di interruzione
rsync -avz --progress --stats --partial \
  "saltelli-prod:$WP_PATH/wp-content/uploads/" \
  "$PROJECT/wp-content/uploads/" \
  >> "$LOG" 2>&1

EXIT=$?
echo "" >> "$LOG"
echo "rsync finished: $(date) — exit=$EXIT" >> "$LOG"
