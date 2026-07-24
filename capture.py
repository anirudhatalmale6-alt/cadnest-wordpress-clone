#!/usr/bin/env python3
"""Render each page of cadnestdesign.com fully and mirror every asset locally."""
import os, re, hashlib, json, pathlib
from urllib.parse import urlparse, urljoin
from playwright.sync_api import sync_playwright

BASE = "https://www.cadnestdesign.com"
PAGES = {
    "home": "/",
    "sample-plans": "/sample-plans",
    "quote-request": "/quote-request",
    "contact-us": "/contact-us",
    "drafting-services-for-contractors-los-angeles": "/drafting-services-for-contractors-los-angeles",
    "drafting-services-pasadena-ca": "/drafting-services-pasadena-ca",
    "adu-plans-los-angeles": "/adu-plans-los-angeles",
    "as-built-plans-pasadena-los-angeles": "/as-built-plans-pasadena-los-angeles",
    "room-addition-remodel-drafting-los-angeles": "/room-addition-remodel-drafting-los-angeles",
    "outsourced-cad-drafting-los-angeles": "/outsourced-cad-drafting-los-angeles",
    "privacy-policy": "/privacy-policy",
}

ROOT = pathlib.Path("/var/lib/freelancer/projects/40601864")
MIRROR = ROOT / "mirror"          # local copies of assets
HTML = ROOT / "rendered"          # rendered html per page
MIRROR.mkdir(exist_ok=True)
HTML.mkdir(exist_ok=True)

# hosts whose assets we localise
ASSET_HOSTS = {"www.cadnestdesign.com", "cadnestdesign.com", "img1.wsimg.com", "img.wsimg.com"}

manifest = {}  # url -> local relative path under mirror/

def local_path_for(url):
    p = urlparse(url)
    host = p.netloc
    path = p.path
    if not path or path.endswith("/"):
        path += "index.html"
    # incorporate query into a hash to disambiguate
    qh = ""
    if p.query:
        qh = "_" + hashlib.md5(p.query.encode()).hexdigest()[:8]
    ext = os.path.splitext(path)[1]
    if qh:
        path = path[:len(path)-len(ext)] + qh + ext if ext else path + qh
    rel = os.path.join(host, path.lstrip("/"))
    return rel

def save_response(resp):
    try:
        url = resp.url
        p = urlparse(url)
        if p.netloc not in ASSET_HOSTS:
            return
        ct = resp.headers.get("content-type", "")
        # skip the page documents themselves (we save rendered dom separately)
        rel = local_path_for(url)
        dest = MIRROR / rel
        if dest.exists():
            manifest[url] = rel
            return
        body = resp.body()
        dest.parent.mkdir(parents=True, exist_ok=True)
        dest.write_bytes(body)
        manifest[url] = rel
    except Exception as e:
        pass

with sync_playwright() as pw:
    browser = pw.chromium.launch()
    ctx = browser.new_context(viewport={"width": 1440, "height": 900},
                              user_agent="Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120 Safari/537.36")
    page = ctx.new_page()
    page.on("response", save_response)
    for name, slug in PAGES.items():
        url = BASE + slug
        print("Rendering", url)
        try:
            page.goto(url, wait_until="networkidle", timeout=60000)
        except Exception as e:
            print("  goto warn:", e)
        # scroll to trigger lazy loads
        for y in range(0, 6000, 800):
            page.evaluate(f"window.scrollTo(0,{y})")
            page.wait_for_timeout(200)
        page.wait_for_timeout(1500)
        html = page.content()
        (HTML / f"{name}.html").write_text(html, encoding="utf-8")
        print("  saved html", len(html), "bytes")
    browser.close()

(ROOT / "manifest.json").write_text(json.dumps(manifest, indent=2))
print("Assets mirrored:", len(manifest))
