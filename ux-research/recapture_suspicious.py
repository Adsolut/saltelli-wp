"""
Recattura mirata per siti con cookie banner persistenti (Saltelli Iubenda, Seddons).
"""

import asyncio
from pathlib import Path
from playwright.async_api import async_playwright

OUTPUT_DIR = Path("/Users/aldosantoro/Desktop/DEV/saltelli-wp/ux-research/screenshots")

# Solo i due sospetti
SITES = [
    ("00-saltelli", "https://www.studiolegalesaltelli.it",  "Saltelli (Iubenda)"),
    ("07-seddons",  "https://seddons.co.uk",                "Seddons"),
]

UA = ("Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 "
      "(KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36")


async def kill_cookie_banner(page):
    """Strategia aggressiva: rimuove via JS i banner cookie più diffusi."""
    js = r"""
    (() => {
      const sels = [
        '#iubenda-cs-banner', '.iubenda-cs-container', '.iub-content',
        '#onetrust-banner-sdk', '#onetrust-consent-sdk',
        '#cookie-notice', '.cookie-notice', '.cc-banner', '.cc-window',
        '[class*="cookie"]', '[id*="cookie"]', '[class*="consent"]',
        '[class*="gdpr"]', '[id*="gdpr"]'
      ];
      sels.forEach(s => document.querySelectorAll(s).forEach(el => {
        try { el.remove(); } catch (_) {}
      }));
      // Rimuove eventuali overlay scuri sticky
      document.querySelectorAll('div[style*="position: fixed"]').forEach(el => {
        const style = window.getComputedStyle(el);
        const z = parseInt(style.zIndex) || 0;
        if (z > 1000 && el.offsetWidth > window.innerWidth * 0.5) {
          try { el.remove(); } catch (_) {}
        }
      });
      // Disabilita scroll-lock body
      document.body.style.overflow = 'auto';
      document.documentElement.style.overflow = 'auto';
    })();
    """
    try:
        await page.evaluate(js)
    except Exception:
        pass


async def capture(slug, url, label):
    print(f"\n→ {label}: {url}")
    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True)

        # Desktop
        ctx = await browser.new_context(
            viewport={"width": 1440, "height": 900},
            user_agent=UA,
            locale="it-IT",
        )
        page = await ctx.new_page()
        try:
            await page.goto(url, wait_until="domcontentloaded", timeout=30000)
            await page.wait_for_timeout(3000)  # rendering completo
            await kill_cookie_banner(page)
            await page.wait_for_timeout(800)
            # Scroll un pelo poi back to top per triggera lazy-load
            await page.evaluate("window.scrollTo(0, 200)")
            await page.wait_for_timeout(500)
            await page.evaluate("window.scrollTo(0, 0)")
            await page.wait_for_timeout(800)
            path = OUTPUT_DIR / f"{slug}-desktop.png"
            await page.screenshot(path=str(path), full_page=False)
            print(f"   ✓ desktop → {path.name} ({path.stat().st_size // 1024} KB)")
        except Exception as e:
            print(f"   ✗ desktop FAILED: {e}")
        await ctx.close()

        # Mobile
        ctx = await browser.new_context(
            viewport={"width": 390, "height": 844},
            user_agent=("Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) "
                        "AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.5 "
                        "Mobile/15E148 Safari/604.1"),
            locale="it-IT",
            is_mobile=True,
            device_scale_factor=3,
        )
        page = await ctx.new_page()
        try:
            await page.goto(url, wait_until="domcontentloaded", timeout=30000)
            await page.wait_for_timeout(3000)
            await kill_cookie_banner(page)
            await page.wait_for_timeout(800)
            await page.evaluate("window.scrollTo(0, 200)")
            await page.wait_for_timeout(500)
            await page.evaluate("window.scrollTo(0, 0)")
            await page.wait_for_timeout(800)
            path = OUTPUT_DIR / f"{slug}-mobile.png"
            await page.screenshot(path=str(path), full_page=False)
            print(f"   ✓ mobile  → {path.name} ({path.stat().st_size // 1024} KB)")
        except Exception as e:
            print(f"   ✗ mobile FAILED: {e}")
        await ctx.close()
        await browser.close()


async def main():
    for slug, url, label in SITES:
        await capture(slug, url, label)


if __name__ == "__main__":
    asyncio.run(main())
