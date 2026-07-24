#!/usr/bin/env python3
"""Screenshot local clone vs live original and report visual diff + console errors."""
import sys
from playwright.sync_api import sync_playwright

LOCAL = "http://127.0.0.1:8971"
LIVE  = "https://www.cadnestdesign.com"
PAGES = ["", "sample-plans", "contact-us", "quote-request",
         "drafting-services-pasadena-ca", "adu-plans-los-angeles",
         "as-built-plans-pasadena-los-angeles",
         "room-addition-remodel-drafting-los-angeles",
         "drafting-services-for-contractors-los-angeles",
         "outsourced-cad-drafting-los-angeles", "privacy-policy"]
W, H = 1280, 800

def grab(page, url, tag):
    errors = []
    page.on("requestfailed", lambda r: errors.append("REQFAIL %s %s" % (r.failure, r.url[:90])) if "cadnestdesign" not in r.url or "127.0.0.1" in url else None)
    try:
        page.goto(url, wait_until="networkidle", timeout=45000)
    except Exception as e:
        print("  goto warn", tag, e)
    for y in range(0, 4000, 800):
        page.evaluate(f"window.scrollTo(0,{y})"); page.wait_for_timeout(150)
    page.evaluate("window.scrollTo(0,0)"); page.wait_for_timeout(400)
    h = page.evaluate("document.body.scrollHeight")
    return h

with sync_playwright() as pw:
    b = pw.chromium.launch()
    for slug in PAGES:
        name = slug or "home"
        ctx = b.new_context(viewport={"width": W, "height": H})
        # local
        lp = ctx.new_page()
        lc_err = []
        lp.on("console", lambda m: lc_err.append(m.text) if m.type == "error" else None)
        lp.on("requestfailed", lambda r: lc_err.append("REQFAIL "+r.url.split('/assets/')[-1][:80]))
        lh = grab(lp, f"{LOCAL}/{slug}", "local")
        lp.set_viewport_size({"width": W, "height": H})
        lp.screenshot(path=f"shots/local_{name}.png")
        # live
        vp = ctx.new_page()
        vh = grab(vp, f"{LIVE}/{slug}", "live")
        vp.set_viewport_size({"width": W, "height": H})
        vp.screenshot(path=f"shots/live_{name}.png")
        asset_fails = [e for e in lc_err if "REQFAIL" in e]
        print(f"{name:48s} localH={lh:5d} liveH={vh:5d} assetFails={len(asset_fails)}")
        for e in asset_fails[:5]:
            print("      ", e)
        ctx.close()
    b.close()
print("done")
