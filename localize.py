#!/usr/bin/env python3
"""Download every referenced same-origin asset and build self-contained snapshots."""
import re, glob, os, hashlib, pathlib, json
from concurrent.futures import ThreadPoolExecutor
import urllib.request

BASE = "https://www.cadnestdesign.com"
ROOT = pathlib.Path("/var/lib/freelancer/projects/40601864")
RENDERED = ROOT / "rendered"
THEME = ROOT / "cadnest-clone"
ASSETS = THEME / "assets"
SNAPS = THEME / "snapshots"
ASSETS.mkdir(parents=True, exist_ok=True)
SNAPS.mkdir(parents=True, exist_ok=True)

REF_RE = re.compile(r'/-_-/[^\s"\'\)>]+')

def split_ref(ref):
    """Return (download_url, localrel) for a root-relative /-_-/ ref."""
    ref = ref.split('#')[0]                           # drop url fragment (e.g. .svg#Nunito)
    path, _, query = ref.partition('?')
    real_query = bool(query)
    localrel = path.lstrip('/')                      # e.g. -_-/res/.../file
    if real_query:
        h = hashlib.md5(query.encode()).hexdigest()[:8]
        localrel = localrel + '__q' + h
    dl = BASE + path + (('?' + query) if real_query else '')
    return dl, localrel

# 1. collect all distinct refs across rendered html
refs = set()
for f in glob.glob(str(RENDERED / '*.html')):
    t = open(f, encoding='utf-8', errors='ignore').read()
    refs.update(REF_RE.findall(t))
print("distinct html refs:", len(refs))

# map ref -> localrel, and collect download jobs
ref_map = {}
jobs = {}   # localrel -> download_url
for r in refs:
    dl, localrel = split_ref(r)
    ref_map[r] = localrel
    jobs[localrel] = dl

# 2. download everything (skip if already present with size>0)
HDRS = {"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120 Safari/537.36"}
def fetch(item):
    localrel, url = item
    dest = ASSETS / localrel
    if dest.exists() and dest.stat().st_size > 0:
        return (localrel, "cached")
    try:
        req = urllib.request.Request(url, headers=HDRS)
        with urllib.request.urlopen(req, timeout=45) as resp:
            data = resp.read()
        dest.parent.mkdir(parents=True, exist_ok=True)
        dest.write_bytes(data)
        return (localrel, "ok:%d" % len(data))
    except Exception as e:
        return (localrel, "ERR:%s" % e)

errors = []
with ThreadPoolExecutor(max_workers=12) as ex:
    for localrel, status in ex.map(fetch, jobs.items()):
        if status.startswith("ERR"):
            errors.append((localrel, status))
print("downloaded:", len(jobs), "errors:", len(errors))
for e in errors[:20]:
    print("  ", e)

# 3. rewrite the main CSS: /-_-/ refs -> relative path from CSS dir
css_rel = "-_-/common/styles/style.Bdx8pZMZ.css"
css_path = ASSETS / css_rel
if css_path.exists():
    css = css_path.read_text(encoding='utf-8', errors='ignore')
    css_refs = sorted(set(REF_RE.findall(css)), key=len, reverse=True)
    css_dir = css_path.parent
    for r in css_refs:
        _, localrel = split_ref(r)
        target = ASSETS / localrel
        rel = os.path.relpath(target, css_dir)
        css = css.replace(r, rel)
    css_path.write_text(css, encoding='utf-8')
    print("css refs rewritten:", len(css_refs))

# 4. build snapshots: replace /-_-/ refs with __ASSETBASE__/<localrel>
PAGES = {
    "home": "home",
    "sample-plans": "sample-plans",
    "quote-request": "quote-request",
    "contact-us": "contact-us",
    "drafting-services-for-contractors-los-angeles": "drafting-services-for-contractors-los-angeles",
    "drafting-services-pasadena-ca": "drafting-services-pasadena-ca",
    "adu-plans-los-angeles": "adu-plans-los-angeles",
    "as-built-plans-pasadena-los-angeles": "as-built-plans-pasadena-los-angeles",
    "room-addition-remodel-drafting-los-angeles": "room-addition-remodel-drafting-los-angeles",
    "outsourced-cad-drafting-los-angeles": "outsourced-cad-drafting-los-angeles",
    "privacy-policy": "privacy-policy",
}
for name in PAGES:
    src = RENDERED / f"{name}.html"
    if not src.exists():
        print("MISSING", name); continue
    html = src.read_text(encoding='utf-8', errors='ignore')
    page_refs = sorted(set(REF_RE.findall(html)), key=len, reverse=True)
    for r in page_refs:
        localrel = ref_map.get(r) or split_ref(r)[1]
        html = html.replace(r, "__ASSETBASE__/" + localrel)
    (SNAPS / f"{name}.html").write_text(html, encoding='utf-8')
print("snapshots written:", len(PAGES))
