from playwright.sync_api import sync_playwright
fails=[]
with sync_playwright() as pw:
    b=pw.chromium.launch(); c=b.new_context(viewport={"width":1280,"height":800}); p=c.new_page()
    def onresp(r):
        if "/assets/" in r.url and r.status>=400:
            fails.append("HTTP%d %s"%(r.status,r.url.split('/assets/')[-1][:70]))
    p.on("requestfailed", lambda r: fails.append("REQFAIL "+r.url.split('/assets/')[-1][:70]) if "/assets/" in r.url else None)
    p.on("response", onresp)
    for slug in ["","contact-us","sample-plans","adu-plans-los-angeles"]:
        p.goto("http://127.0.0.1:8971/"+slug,wait_until="networkidle",timeout=45000)
        for y in range(0,4000,800):
            p.evaluate(f"window.scrollTo(0,{y})"); p.wait_for_timeout(120)
        p.wait_for_timeout(500)
    fam1=p.evaluate("document.fonts.check('700 16px \"Nunito Sans\"')")
    fam2=p.evaluate("document.fonts.check('700 16px \"Nunito\"')")
    print("Nunito Sans 700 loaded:", fam1, "| Nunito 700 loaded:", fam2)
    b.close()
print("asset failures:", len(fails))
for f in sorted(set(fails)): print("  ", f)
