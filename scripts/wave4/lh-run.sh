#!/usr/bin/env bash
# Wave 4 Lighthouse runner — 6 URL × 2 viewport (mobile/desktop)
# Usage: ./scripts/wave4/lh-run.sh <output-dir>
#   e.g. ./scripts/wave4/lh-run.sh .claude/knowledge/audits/wave4/lh-baseline-pre/
set -e

OUT_DIR="${1:-.claude/knowledge/audits/wave4/lh-baseline-pre}"
mkdir -p "$OUT_DIR"

URLS=(
  "/|home"
  "/aree-di-pratica/privati/diritto-tributario/|tier1-tributario"
  "/aree-di-pratica/privati/cartelle-esattoriali-e-multe/|tier2-cartelle"
  "/chi-siamo/team/antonia-battista/|avvocato-battista"
  "/contatti/|contatti"
  "/risorse/blog/|blog-archive"
)

BASE="http://localhost:8080"
CHROME_PATH="/Applications/Google Chrome.app/Contents/MacOS/Google Chrome"

for entry in "${URLS[@]}"; do
  path="${entry%%|*}"
  slug="${entry##*|}"
  url="${BASE}${path}"

  for strategy in mobile desktop; do
    echo "===  ${slug} (${strategy})  ==="
    out_path="${OUT_DIR}/${slug}-${strategy}"
    if [ "$strategy" = "desktop" ]; then
      preset_arg="--preset=desktop"
    else
      preset_arg=""
    fi
    npx --yes lighthouse "$url" \
      $preset_arg \
      --output=json --output=html \
      --output-path="$out_path" \
      --chrome-path="$CHROME_PATH" \
      --chrome-flags="--headless=new --no-sandbox --disable-gpu" \
      --quiet \
      --only-categories=performance,accessibility,best-practices,seo \
      --max-wait-for-load=45000 \
      2>&1 | tail -2
  done
done

echo "=== Summary ==="
for f in "$OUT_DIR"/*.report.json; do
  fname=$(basename "$f" .report.json)
  perf=$(jq -r '.categories.performance.score // 0' "$f")
  a11y=$(jq -r '.categories.accessibility.score // 0' "$f")
  bp=$(jq -r '.categories["best-practices"].score // 0' "$f")
  seo=$(jq -r '.categories.seo.score // 0' "$f")
  perf_pct=$(printf '%.0f' "$(echo "$perf * 100" | bc -l)")
  a11y_pct=$(printf '%.0f' "$(echo "$a11y * 100" | bc -l)")
  bp_pct=$(printf '%.0f' "$(echo "$bp * 100" | bc -l)")
  seo_pct=$(printf '%.0f' "$(echo "$seo * 100" | bc -l)")
  printf "%-32s  perf=%s  a11y=%s  bp=%s  seo=%s\n" "$fname" "$perf_pct" "$a11y_pct" "$bp_pct" "$seo_pct"
done | tee "${OUT_DIR}/_summary.txt"
