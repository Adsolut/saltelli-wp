#!/usr/bin/env bash
# Wave 4.5 — Re-run Lighthouse mobile on marginal URLs to confirm score median.
# Single-run LH variance is ±3-5 points (Wave 4 lesson #5).
set -e

OUT_DIR="${1:-.claude/knowledge/audits/wave4-5/lh-rerun-mobile}"
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
RUNS=3

for entry in "${URLS[@]}"; do
  path="${entry%%|*}"
  slug="${entry##*|}"
  url="${BASE}${path}"

  for i in $(seq 1 $RUNS); do
    out_path="${OUT_DIR}/${slug}-mobile-run${i}"
    npx --yes lighthouse "$url" \
      --output=json \
      --output-path="$out_path" \
      --chrome-path="$CHROME_PATH" \
      --chrome-flags="--headless=new --no-sandbox --disable-gpu" \
      --quiet \
      --only-categories=performance \
      --max-wait-for-load=45000 \
      2>&1 | tail -1
  done
done

echo ""
echo "=== Median per URL (mobile, ${RUNS} runs) ==="
for entry in "${URLS[@]}"; do
  slug="${entry##*|}"
  scores=()
  for i in $(seq 1 $RUNS); do
    f="${OUT_DIR}/${slug}-mobile-run${i}.report.json"
    if [ -f "$f" ]; then
      perf=$(jq -r '.categories.performance.score' "$f" 2>/dev/null)
      pct=$(printf '%.0f' "$(echo "$perf * 100" | bc -l)")
      scores+=($pct)
    fi
  done
  # sort scores
  sorted=$(printf "%s\n" "${scores[@]}" | sort -n | tr '\n' ' ')
  median=$(printf "%s\n" "${scores[@]}" | sort -n | awk -v n="${#scores[@]}" 'NR==int((n+1)/2)')
  printf "%-32s  runs=[%s] median=%s\n" "$slug" "$sorted" "$median"
done | tee "${OUT_DIR}/_summary.txt"
