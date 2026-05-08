#!/usr/bin/env bash
#
# Wave 4.7.fix.2 P2 — Saltelli Header menu rebuild.
#
# Rebuilds the `Saltelli Header` (location=primary) nav menu using
# type=post_type/taxonomy references where possible. Eliminates the
# 17/22 hardcoded type=custom URLs that pointed to legacy paths.
#
# Idempotent: wipes existing items first, recreates them.
# Portable: page IDs resolved by slug at runtime → works across env
# (staging IDs ≠ local Docker IDs).
#
# Usage:
#   STAGING (droplet):
#     bash scripts/wave4-7-fix-2-menu-rebuild.sh
#
#   LOCAL (docker):
#     WP_PATH=/var/www/html \
#     WPCLI_PREFIX="docker exec saltelli-wp" \
#     EXTRA_FLAGS=--allow-root \
#     bash scripts/wave4-7-fix-2-menu-rebuild.sh
#
set -euo pipefail
WP="${WP_PATH:-/var/www/saltelli}"
WPCLI_PREFIX="${WPCLI_PREFIX:-sudo -u www-data}"
EXTRA_FLAGS="${EXTRA_FLAGS:-}"
WPCLI() { $WPCLI_PREFIX wp "$@" $EXTRA_FLAGS --path=$WP; }

# Resolve page ID by slug; abort if not found (env mismatch).
page_id() {
  local slug="$1"
  local id
  id=$(WPCLI post list --post_type=page --post_status=publish --name="$slug" --field=ID 2>/dev/null | tr -d '[:space:]')
  if [[ -z "$id" || "$id" == "0" ]]; then
    echo "[FATAL] page slug '$slug' not found in DB — aborting menu rebuild" >&2
    exit 2
  fi
  echo "$id"
}

# Resolve term ID by tipo-area slug.
term_id() {
  local slug="$1"
  local id
  id=$(WPCLI term list tipo-area --slug="$slug" --field=term_id 2>/dev/null | tr -d '[:space:]')
  if [[ -z "$id" || "$id" == "0" ]]; then
    echo "[FATAL] tipo-area slug '$slug' not found — aborting menu rebuild" >&2
    exit 2
  fi
  echo "$id"
}

echo "=== Step 0: resolve IDs ==="
ID_CHI=$(page_id chi-siamo)
ID_AREE=$(page_id aree-di-pratica)
ID_RIS=$(page_id risorse)
ID_COSTI=$(page_id costi-e-consulenze)
ID_CONT=$(page_id contatti)
ID_BLOG=$(page_id blog)
ID_FAQ=$(page_id domande-frequenti)
ID_GUIDE=$(page_id guide-gratuite)
ID_GLOSS=$(page_id glossario-legale)
ID_PRIMA=$(page_id prima-consulenza)
ID_COMELAV=$(page_id come-lavoriamo)
ID_RICHPREV=$(page_id richiedi-preventivo)
# Wave 5 IA: prenota-appuntamento (NOT legacy prenota-un-appuntamento).
ID_PRENOTA=$(page_id prenota-appuntamento)
ID_LAVORA=$(page_id lavora-con-noi)
T_PRIVATI=$(term_id privati)
T_IMPRESE=$(term_id imprese)
T_CONTENZ=$(term_id contenzioso-amministrativo)
echo "  parents: chi=$ID_CHI aree=$ID_AREE ris=$ID_RIS costi=$ID_COSTI cont=$ID_CONT"

echo "=== Step 1: wipe Saltelli Header items ==="
ITEM_IDS=$(WPCLI menu item list saltelli-header --fields=db_id --format=csv | tail -n +2)
COUNT=0
for ID in $ITEM_IDS; do
  WPCLI menu item delete "$ID" >/dev/null
  COUNT=$((COUNT+1))
done
echo "  wiped: $COUNT items"

echo "=== Step 2: parents ==="
P_CHI=$(WPCLI menu item add-post saltelli-header $ID_CHI --title="Chi Siamo" --porcelain)
P_AREE=$(WPCLI menu item add-post saltelli-header $ID_AREE --title="Aree di Pratica" --porcelain)
P_RIS=$(WPCLI menu item add-post saltelli-header $ID_RIS --title="Risorse" --porcelain)
P_COSTI=$(WPCLI menu item add-post saltelli-header $ID_COSTI --title="Costi e Consulenze" --porcelain)
P_CONT=$(WPCLI menu item add-post saltelli-header $ID_CONT --title="Contatti" --porcelain)

echo "=== Step 3: sub Chi Siamo ==="
WPCLI menu item add-custom saltelli-header "Il Team" "/chi-siamo/team/" --parent-id=$P_CHI >/dev/null
WPCLI menu item add-custom saltelli-header "Risultati" "/chi-siamo/casi-rappresentativi/" --parent-id=$P_CHI >/dev/null

echo "=== Step 4: sub Aree di Pratica ==="
WPCLI menu item add-term saltelli-header tipo-area $T_PRIVATI --title="Per i Privati" --parent-id=$P_AREE >/dev/null
WPCLI menu item add-term saltelli-header tipo-area $T_IMPRESE --title="Per le Imprese" --parent-id=$P_AREE >/dev/null
WPCLI menu item add-term saltelli-header tipo-area $T_CONTENZ --title="Contenzioso Amministrativo" --parent-id=$P_AREE >/dev/null
WPCLI menu item add-post saltelli-header $ID_AREE --title="Tutte le aree" --parent-id=$P_AREE >/dev/null

echo "=== Step 5: sub Risorse ==="
WPCLI menu item add-post saltelli-header $ID_BLOG --title="Blog" --parent-id=$P_RIS >/dev/null
WPCLI menu item add-post saltelli-header $ID_FAQ --title="Domande Frequenti" --parent-id=$P_RIS >/dev/null
WPCLI menu item add-post saltelli-header $ID_GUIDE --title="Guide Gratuite" --parent-id=$P_RIS >/dev/null
WPCLI menu item add-post saltelli-header $ID_GLOSS --title="Glossario Legale" --parent-id=$P_RIS >/dev/null

echo "=== Step 6: sub Costi ==="
WPCLI menu item add-post saltelli-header $ID_PRIMA --title="Prima Consulenza" --parent-id=$P_COSTI >/dev/null
WPCLI menu item add-post saltelli-header $ID_COMELAV --title="Come Lavoriamo" --parent-id=$P_COSTI >/dev/null
WPCLI menu item add-post saltelli-header $ID_RICHPREV --title="Richiedi Preventivo" --parent-id=$P_COSTI >/dev/null

echo "=== Step 7: sub Contatti ==="
WPCLI menu item add-post saltelli-header $ID_PRENOTA --title="Prenota Appuntamento" --parent-id=$P_CONT >/dev/null
WPCLI menu item add-custom saltelli-header "Dove Siamo" "/contatti/#sede" --parent-id=$P_CONT >/dev/null
WPCLI menu item add-post saltelli-header $ID_LAVORA --title="Lavora con Noi" --parent-id=$P_CONT >/dev/null

WPCLI menu location assign saltelli-header primary >/dev/null
WPCLI cache flush >/dev/null
echo "=== Done ==="
WPCLI menu item list saltelli-header --fields=db_id,type,title,link
