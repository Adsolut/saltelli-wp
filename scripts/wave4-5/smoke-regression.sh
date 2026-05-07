#!/usr/bin/env bash
# Wave 4.5 — NO regression smoke runner.
#
# Re-curl 4 URL lists used as Wave 4 acceptance:
#   1. Wave 5 audit-aligned IA (33 URLs, expect 200)
#   2. Wave 5 legacy redirects (18 URLs, expect 301)
#   3. Wave 5 blog redirects chain (33 URLs, expect 301→200)
#   4. Wave 6 audit-aligned 21-URL (expect 200)
# Plus extra render checks for trust-bar / mobile-bar / mini-form / FAQ schema.
#
# Output: writes per-test reports + a summary file in $OUT_DIR.
set -u

BASE="http://localhost:8080"
OUT_DIR="${1:-.claude/knowledge/audits/wave4-5/regression}"
mkdir -p "$OUT_DIR"

GLOBAL_FAILS=0

# Helper: re-curl URL, classify status code
status() {
    curl -s -o /dev/null -w '%{http_code}' --max-time 15 "$@"
}
location() {
    curl -s -o /dev/null -w '%{redirect_url}' --max-time 15 "$@"
}

# ----- 1. Wave 5 audit-aligned (33 URLs, expect 200) -----
W5_AA_FILE="$OUT_DIR/wave5-audit-aligned.txt"
fails=0; total=0
{
    while IFS= read -r line; do
        # only "OK 200 /path" or "FAIL 200 /path" lines
        [[ "$line" =~ ^(OK|FAIL)[[:space:]] ]] || continue
        url_path=$(echo "$line" | awk '{print $3}')
        # path must start with /
        [[ "$url_path" =~ ^/ ]] || continue
        total=$((total + 1))
        full="${BASE}${url_path}"
        code=$(status "$full")
        if [ "$code" = "200" ]; then
            echo "PASS 200 ${url_path}"
        else
            echo "FAIL ${code} ${url_path}"
            fails=$((fails + 1))
        fi
    done < .claude/knowledge/audits/wave5-ia-refactor/cli-output/08-smoke-audit-aligned.txt
    echo ""
    echo "Total: ${total} | Fails: ${fails}"
} > "$W5_AA_FILE"
echo "1. Wave 5 audit-aligned: ${total} URLs, ${fails} fails $([ $fails -eq 0 ] && echo '✅' || echo '❌')"
GLOBAL_FAILS=$((GLOBAL_FAILS + fails))

# ----- 2. Wave 5 legacy redirects (18 URLs, expect 301) -----
W5_RD_FILE="$OUT_DIR/wave5-redirects.txt"
fails=0; total=0
{
    while IFS= read -r line; do
        # only "OK 301 /old-path -> ..." or "FAIL 301 ..." lines
        [[ "$line" =~ ^(OK|FAIL)[[:space:]] ]] || continue
        url_path=$(echo "$line" | awk '{print $3}')
        [[ "$url_path" =~ ^/ ]] || continue
        total=$((total + 1))
        full="${BASE}${url_path}"
        code=$(status "$full")
        if [ "$code" = "301" ] || [ "$code" = "302" ]; then
            echo "PASS ${code} ${url_path}"
        else
            echo "FAIL ${code} ${url_path}"
            fails=$((fails + 1))
        fi
    done < .claude/knowledge/audits/wave5-ia-refactor/cli-output/08-smoke-redirects.txt
    echo ""
    echo "Total: ${total} | Fails: ${fails}"
} > "$W5_RD_FILE"
echo "2. Wave 5 legacy redirects: ${total} URLs, ${fails} fails $([ $fails -eq 0 ] && echo '✅' || echo '❌')"
GLOBAL_FAILS=$((GLOBAL_FAILS + fails))

# ----- 3. Wave 5 blog redirects chain (33 URLs, /blog/<slug>/ → /risorse/blog/<slug>/) -----
W5_BL_FILE="$OUT_DIR/wave5-blog-chain.txt"
fails=0; total=0
{
    # Extract the path from FAIL or OK lines
    while IFS= read -r line; do
        # format: "FAIL 301/404 /blog/<slug>/" or "OK 301 /blog/<slug>/ -> ..."
        [[ "$line" =~ ^(OK|FAIL)[[:space:]] ]] || continue
        url_path=$(echo "$line" | awk '{print $3}')
        # Skip header lines / non-paths — must start with /blog/
        [[ "$url_path" =~ ^/blog/ ]] || continue
        total=$((total + 1))
        full="${BASE}${url_path}"
        # Follow redirect chain: original /blog/x/ should 301 to /risorse/blog/x/ which 200s
        code_first=$(status "$full")
        loc=$(location "$full")
        if [ "$code_first" = "301" ] || [ "$code_first" = "302" ]; then
            # Test the redirect target
            code_final=$(status "$loc")
            if [ "$code_final" = "200" ]; then
                echo "PASS 301→200 ${url_path}"
            else
                echo "FAIL chain ${code_first}→${code_final} ${url_path}"
                fails=$((fails + 1))
            fi
        else
            echo "FAIL ${code_first} ${url_path}"
            fails=$((fails + 1))
        fi
    done < .claude/knowledge/audits/wave5-ia-refactor/cli-output/08-smoke-blog.txt
    echo ""
    echo "Total: ${total} | Fails: ${fails}"
} > "$W5_BL_FILE"
echo "3. Wave 5 blog 33-chain: ${total} URLs, ${fails} fails $([ $fails -eq 0 ] && echo '✅' || echo '❌')"
GLOBAL_FAILS=$((GLOBAL_FAILS + fails))

# ----- 4. Wave 6 audit-aligned 21-URL (expect 200) -----
W6_FILE="$OUT_DIR/wave6-smoke.txt"
fails=0; total=0
{
    while IFS= read -r line; do
        # format: "  PASS 200 http://localhost:8080/path" — only count if 3rd field is a URL
        full=$(echo "$line" | awk '{print $3}')
        [[ "$full" =~ ^https?:// ]] || continue
        total=$((total + 1))
        code=$(status "$full")
        if [ "$code" = "200" ]; then
            echo "PASS 200 ${full}"
        else
            echo "FAIL ${code} ${full}"
            fails=$((fails + 1))
        fi
    done < .claude/knowledge/audits/wave6/smoke-21-urls.txt
    echo ""
    echo "Total: ${total} | Fails: ${fails}"
} > "$W6_FILE"
echo "4. Wave 6 21-URL: ${total} URLs, ${fails} fails $([ $fails -eq 0 ] && echo '✅' || echo '❌')"
GLOBAL_FAILS=$((GLOBAL_FAILS + fails))

# ----- 5. Wave 6 render checks (trust-bar/mobile-bar/mini-form/FAQ) -----
W6_RC_FILE="$OUT_DIR/wave6-render-checks.txt"
fails=0
{
    home_html=$(curl -s "${BASE}/")
    tier1_html=$(curl -s "${BASE}/aree-di-pratica/privati/diritto-tributario/")
    tier2_html=$(curl -s "${BASE}/aree-di-pratica/privati/cartelle-esattoriali-e-multe/")

    # home: sl-trust-bar (target ≥ 1)
    cnt=$(echo "$home_html" | grep -c 'sl-trust-bar')
    if [ "$cnt" -ge 1 ]; then echo "PASS sl-trust-bar: ${cnt}"; else echo "FAIL sl-trust-bar: ${cnt}"; fails=$((fails + 1)); fi

    # home: sl-mobile-bar
    cnt=$(echo "$home_html" | grep -c 'sl-mobile-bar')
    if [ "$cnt" -ge 1 ]; then echo "PASS home sl-mobile-bar: ${cnt}"; else echo "FAIL home sl-mobile-bar: ${cnt}"; fails=$((fails + 1)); fi

    # home: cro.css enqueue
    cnt=$(echo "$home_html" | grep -c 'cro\.css')
    if [ "$cnt" -ge 1 ]; then echo "PASS cro.css enqueue: ${cnt}"; else echo "FAIL cro.css enqueue: ${cnt}"; fails=$((fails + 1)); fi

    # tier1: sl-mobile-bar
    cnt=$(echo "$tier1_html" | grep -c 'sl-mobile-bar')
    if [ "$cnt" -ge 1 ]; then echo "PASS tier1 sl-mobile-bar: ${cnt}"; else echo "FAIL tier1 sl-mobile-bar: ${cnt}"; fails=$((fails + 1)); fi

    # tier1: mini-form (sl-mini-form)
    cnt=$(echo "$tier1_html" | grep -c 'sl-mini-form')
    if [ "$cnt" -ge 1 ]; then echo "PASS tier1 sl-mini-form: ${cnt}"; else echo "FAIL tier1 sl-mini-form: ${cnt}"; fails=$((fails + 1)); fi

    # tier2: FAQPage schema in JSON-LD
    cnt=$(echo "$tier2_html" | grep -c 'FAQPage')
    if [ "$cnt" -ge 1 ]; then echo "PASS tier2 FAQPage schema: ${cnt}"; else echo "FAIL tier2 FAQPage schema: ${cnt}"; fails=$((fails + 1)); fi
} > "$W6_RC_FILE"
echo "5. Wave 6 render checks: ${fails} fails $([ $fails -eq 0 ] && echo '✅' || echo '❌')"
GLOBAL_FAILS=$((GLOBAL_FAILS + fails))

# ----- 6. Wave 4 perf/security headers (post-Wave 4.5 sanity) -----
W4_FILE="$OUT_DIR/wave4-headers.txt"
fails=0
{
    headers=$(curl -sI "${BASE}/")
    for h in "X-Frame-Options" "X-Content-Type-Options" "Referrer-Policy" "Permissions-Policy"; do
        if echo "$headers" | grep -qi "^${h}:"; then
            echo "PASS ${h}"
        else
            echo "FAIL ${h}"
            fails=$((fails + 1))
        fi
    done

    # JS optimizations: jquery deferred + emoji removed
    home_html=$(curl -s "${BASE}/")
    cnt=$(echo "$home_html" | grep -c 'wp-emoji')
    if [ "$cnt" = "0" ]; then echo "PASS no wp-emoji"; else echo "FAIL wp-emoji present: ${cnt}"; fails=$((fails + 1)); fi
} > "$W4_FILE"
echo "6. Wave 4 headers + JS opt: ${fails} fails $([ $fails -eq 0 ] && echo '✅' || echo '❌')"
GLOBAL_FAILS=$((GLOBAL_FAILS + fails))

# ----- Summary -----
SUMMARY_FILE="$OUT_DIR/_summary.md"
{
    echo "# Wave 4.5 — NO regression smoke"
    echo ""
    echo "Run: $(date '+%Y-%m-%d %H:%M')"
    echo "Branch: $(git rev-parse --abbrev-ref HEAD) @ $(git rev-parse --short HEAD)"
    echo ""
    echo "| Smoke                              | Status |"
    echo "|------------------------------------|--------|"
    aa_pass=$(grep -c '^PASS' "$W5_AA_FILE"); aa_fail=$(grep -c '^FAIL' "$W5_AA_FILE")
    rd_pass=$(grep -c '^PASS' "$W5_RD_FILE"); rd_fail=$(grep -c '^FAIL' "$W5_RD_FILE")
    bl_pass=$(grep -c '^PASS' "$W5_BL_FILE"); bl_fail=$(grep -c '^FAIL' "$W5_BL_FILE")
    w6_pass=$(grep -c '^PASS' "$W6_FILE"); w6_fail=$(grep -c '^FAIL' "$W6_FILE")
    echo "| Wave 5 audit-aligned               | ${aa_pass}/$((aa_pass + aa_fail)) $([ $aa_fail -eq 0 ] && echo PASS || echo FAIL) |"
    echo "| Wave 5 legacy redirects            | ${rd_pass}/$((rd_pass + rd_fail)) $([ $rd_fail -eq 0 ] && echo PASS || echo FAIL) |"
    echo "| Wave 5 blog 33-chain               | ${bl_pass}/$((bl_pass + bl_fail)) $([ $bl_fail -eq 0 ] && echo PASS || echo FAIL) |"
    echo "| Wave 6 audit-aligned               | ${w6_pass}/$((w6_pass + w6_fail)) $([ $w6_fail -eq 0 ] && echo PASS || echo FAIL) |"
    echo "| Wave 6 render checks               | $(grep -c '^PASS' "$W6_RC_FILE") PASS / $(grep -c '^FAIL' "$W6_RC_FILE") FAIL |"
    echo "| Wave 4 headers + JS opt            | $(grep -c '^PASS' "$W4_FILE") PASS / $(grep -c '^FAIL' "$W4_FILE") FAIL |"
    echo ""
    echo "Global fails: ${GLOBAL_FAILS}"
} > "$SUMMARY_FILE"
echo ""
cat "$SUMMARY_FILE"
echo ""
echo "Total fails across all smokes: ${GLOBAL_FAILS}"
exit $GLOBAL_FAILS
