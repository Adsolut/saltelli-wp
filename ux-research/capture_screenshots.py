"""
Saltelli UX Research — screenshot batch
Cattura screenshot desktop + mobile dei 7 competitor + Saltelli.
Output: /Users/aldosantoro/Desktop/DEV/saltelli-wp/ux-research/screenshots/
"""

import asyncio
import os
from pathlib import Path
from playwright.async_api import async_playwright

OUTPUT_DIR = Path("/Users/aldosantoro/Desktop/DEV/saltelli-wp/ux-research/screenshots")
OUTPUT_DIR.mkdir(parents=True, exist_ok=True)

SITES = [
    # (nome_file, url, etichetta)
    ("00-saltelli",         "https://www.studiolegalesaltelli.it",      "CLIENTE Saltelli"),
    ("01-difiorenunziato",  "https://www.difiorenunziato.com",           "LOCAL Di Fiore Nunziato"),
    ("02-marcopiccolo",     "https://www.avvocatomarcopiccolo.it",       "LOCAL Marco Piccolo"),
    ("03-legalilavoro",     "https://www.legalilavoro.it/napoli",        "LOCAL Legalilavoro Napoli"),
    ("04-lombardini",       "https://www.avvocatofrancescolombardini.it","LOCAL Lombardini"),
    ("05-bicklaw",          "https://bicklawllp.com",                    "INT Bick Law"),
    ("06-stowe",            "https://www.stowefamilylaw.co.uk",          "INT Stowe Family Law"),
    ("07-seddons",          "https://seddons.co.uk",                     "INT Seddons"),
]

DESKTOP_VIEWPORT = {"width": 1440, "height": 900}
MOBILE_VIEWPORT  = {"width": 390, "height": 844}  # iPhone 14 Pro

# user-agent meno bot-detection-prone
DESKTOP_UA = ("Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) "
              "AppleWebKit/537.36 (KHTML, like Gecko) "
              "Chrome/131.0.0.0 Safari/537.36")
MOBILE_UA  = ("Mozilla/5.0 (iPhone; CPU iPhone OS 17_5 like Mac OS X) "
              "AppleWebKit/605.1.15 (KHTML, like Gecko) "
              "Version/17.5 Mobile/15E148 Safari/604.1")


async def dismiss_cookie_banners(page):
    """Tenta di dismiss dei cookie banner comuni — best effort, non bloccante."""
    selectors_text = [
        "Accetta tutti", "Accetta", "Accept all", "Accept", "I accept",
        "Continua senza accettare", "OK", "Got it", "Agree",
        "Solo necessari", "Reject all", "Rifiuta",
    ]
    for txt in selectors_text:
        try:
            btn = page.get_by_role("button", name=txt, exact=False).first
            if await btn.is_visible(timeout=500):
                await btn.click(timeout=1000)
                await page.wait_for_timeout(300)
                return
        except Exception:
            pass
    # Fallback: ID/class comuni
    selectors_css = [
        "#onetrust-accept-btn-handler",
        ".cc-accept", ".cookie-accept", ".accept-cookies",
        "[data-cy='accept-all']",
    ]
    for css in selectors_css:
        try:
            await page.locator(css).first.click(timeout=500)
            await page.wait_for_timeout(300)
            return
        except Exception:
            pass


async def capture_site(browser, slug, url, label):
    """Cattura desktop above-fold + mobile above-fold per un singolo sito."""
    print(f"\n→ [{label}] {url}")
    results = {"desktop": False, "mobile": False, "error": None}

    # ====== DESKTOP ======
    try:
        ctx = await browser.new_context(
            viewport=DESKTOP_VIEWPORT,
            user_agent=DESKTOP_UA,
            locale="it-IT",
        )
        page = await ctx.new_page()
        await page.goto(url, wait_until="domcontentloaded", timeout=30000)
        # Wait per rendering animazioni hero
        await page.wait_for_timeout(2500)
        await dismiss_cookie_banners(page)
        await page.wait_for_timeout(800)
        # Screenshot above-the-fold
        path = OUTPUT_DIR / f"{slug}-desktop.png"
        await page.screenshot(path=str(path), full_page=False)
        results["desktop"] = path.exists()
        print(f"    ✓ desktop  → {path.name} ({path.stat().st_size // 1024} KB)")
        await ctx.close()
    except Exception as e:
        results["error"] = f"desktop: {type(e).__name__}: {str(e)[:120]}"
        print(f"    ✗ desktop  FAILED — {results['error']}")
        try: await ctx.close()
        except Exception: pass

    # ====== MOBILE ======
    try:
        ctx = await browser.new_context(
            viewport=MOBILE_VIEWPORT,
            user_agent=MOBILE_UA,
            locale="it-IT",
            is_mobile=True,
            device_scale_factor=3,
        )
        page = await ctx.new_page()
        await page.goto(url, wait_until="domcontentloaded", timeout=30000)
        await page.wait_for_timeout(2500)
        await dismiss_cookie_banners(page)
        await page.wait_for_timeout(800)
        path = OUTPUT_DIR / f"{slug}-mobile.png"
        await page.screenshot(path=str(path), full_page=False)
        results["mobile"] = path.exists()
        print(f"    ✓ mobile   → {path.name} ({path.stat().st_size // 1024} KB)")
        await ctx.close()
    except Exception as e:
        prev = results.get("error")
        msg = f"mobile: {type(e).__name__}: {str(e)[:120]}"
        results["error"] = f"{prev}; {msg}" if prev else msg
        print(f"    ✗ mobile   FAILED — {msg}")
        try: await ctx.close()
        except Exception: pass

    return slug, results


async def main():
    print(f"Output dir: {OUTPUT_DIR}")
    async with async_playwright() as p:
        browser = await p.chromium.launch(headless=True)
        all_results = {}
        for slug, url, label in SITES:
            slug_, res = await capture_site(browser, slug, url, label)
            all_results[slug_] = res
        await browser.close()

    print("\n" + "="*60)
    print("RIEPILOGO")
    print("="*60)
    for slug, res in all_results.items():
        d = "✓" if res["desktop"] else "✗"
        m = "✓" if res["mobile"]  else "✗"
        err = f"  ({res['error']})" if res.get("error") else ""
        print(f"  {slug}:  desktop {d}  mobile {m}{err}")

    print(f"\nTotali: {len(list(OUTPUT_DIR.glob('*.png')))} PNG in {OUTPUT_DIR}")


if __name__ == "__main__":
    asyncio.run(main())
