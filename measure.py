from playwright.sync_api import sync_playwright
def m(page,url):
    page.goto(url,wait_until="networkidle",timeout=45000)
    page.wait_for_timeout(1200)
    return page.evaluate("""()=>{
      const h=document.querySelector('header');
      const h1=document.querySelector('h1');
      const r=el=>el?el.getBoundingClientRect():null;
      const hr=r(h), tr=r(h1);
      return {headerTop:hr?Math.round(hr.top):null, headerH:hr?Math.round(hr.height):null,
              headerPos: h?getComputedStyle(h).position:null,
              h1Top: tr?Math.round(tr.top+window.scrollY):null,
              bodyH: document.body.scrollHeight};
    }""")
with sync_playwright() as pw:
    b=pw.chromium.launch(); c=b.new_context(viewport={"width":1280,"height":800})
    p=c.new_page()
    print("LOCAL", m(p,"http://127.0.0.1:8971/"))
    print("LIVE ", m(p,"https://www.cadnestdesign.com/"))
    b.close()
