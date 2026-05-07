<?php
/**
 * Wave 4 — HTTP security headers (frontend only, wp-admin keeps defaults).
 *
 * In production these are typically delegated to nginx / Cloudflare. The
 * theme adds them via `send_headers` so security baseline is consistent
 * even when origin server config drifts.
 *
 * Headers set:
 *  - X-Frame-Options: SAMEORIGIN  (clickjacking; SAMEORIGIN over DENY because
 *    Yoast preview iframe + WP Customizer still use same-origin embedding)
 *  - X-Content-Type-Options: nosniff
 *  - Referrer-Policy: strict-origin-when-cross-origin
 *  - Permissions-Policy: deny camera/microphone/geolocation/payment by default
 *  - Strict-Transport-Security: 1y + includeSubDomains + preload (HTTPS only)
 *
 * Additional cleanup (perf adjacent):
 *  - SRI sha384 on cdnjs scripts already enforced via inc/enqueue.php
 *  - XML-RPC, RSD link, wlwmanifest already disabled in inc/perf.php
 *  - WP generator meta already empty in inc/perf.php
 *
 * @package Saltelli
 * @since 1.3.0 Wave 4
 */
defined('ABSPATH') || exit;

/* ------------------------------------------------------------------ */
/* HTTP response headers                                               */
/* ------------------------------------------------------------------ */
add_action('send_headers', 'saltelli_security_headers');
function saltelli_security_headers() {
    if (is_admin()) {
        return;
    }
    if (headers_sent()) {
        return;
    }

    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=(), accelerometer=(), gyroscope=(), magnetometer=(), usb=()');
    header('Cross-Origin-Opener-Policy: same-origin-allow-popups');

    /* HSTS: only when actually served over HTTPS to avoid breaking dev. */
    $is_https = (
        (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443)
    );
    if ($is_https) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    }
}
