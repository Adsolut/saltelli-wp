#!/usr/bin/env bash
# Wave 4.5 — WebP conversion bulk script.
#
# Iterates wp-content/uploads/ and converts each JPG/PNG to WebP using
# cwebp -q 85 (quality 85 = visually-lossless target). Naming pattern:
# APPEND ($img.webp) to match existing 1325 webp generated pre-Wave 4.5.
#
# Idempotent: skips images that already have a .webp sibling.
# Safe: never deletes the source JPG/PNG (they remain as fallback).
#
# Usage:
#   bash scripts/wave4-5/convert-webp.sh                     # local Docker
#   bash scripts/wave4-5/convert-webp.sh /var/www/uploads    # explicit path
#
# Requirements:
#   cwebp (Google libwebp)
#     macOS:    brew install webp
#     Ubuntu:   apt install webp
#     Alpine:   apk add libwebp-tools
set -euo pipefail

UPLOADS="${1:-wp-content/uploads}"

if ! command -v cwebp >/dev/null 2>&1; then
    echo "❌ cwebp non installato. Installa con: brew install webp (macOS) o apt install webp (Ubuntu)" >&2
    exit 1
fi
if [ ! -d "$UPLOADS" ]; then
    echo "❌ Directory non trovata: $UPLOADS" >&2
    exit 1
fi

START=$(date +%s)
converted=0
failed=0
skipped=0
total=0
saved_kb=0

while IFS= read -r img; do
    total=$((total + 1))
    webp="${img}.webp"
    if [ -f "$webp" ]; then
        skipped=$((skipped + 1))
        continue
    fi
    if cwebp -quiet -q 85 -mt "$img" -o "$webp" 2>/dev/null; then
        converted=$((converted + 1))
        if [ -f "$webp" ]; then
            orig=$(stat -f%z "$img" 2>/dev/null || stat -c%s "$img")
            new=$(stat -f%z "$webp" 2>/dev/null || stat -c%s "$webp")
            saved_kb=$((saved_kb + (orig - new) / 1024))
        fi
    else
        failed=$((failed + 1))
        echo "⚠️  failed: $img" >&2
    fi
done < <(find "$UPLOADS" -type f \( -iname "*.jpg" -o -iname "*.jpeg" -o -iname "*.png" \))

END=$(date +%s)

echo "=== WebP conversion summary ==="
echo "Source dir:     $UPLOADS"
echo "Total source:   $total"
echo "Skipped:        $skipped (already converted)"
echo "Converted:      $converted"
echo "Failed:         $failed"
if [ $((saved_kb)) -gt 1024 ]; then
    saved_mb=$(echo "scale=1; $saved_kb / 1024" | bc 2>/dev/null || awk "BEGIN { printf \"%.1f\", $saved_kb / 1024 }")
    echo "Saved bytes:    ${saved_mb} MB"
fi
echo "Time:           $((END - START))s"
