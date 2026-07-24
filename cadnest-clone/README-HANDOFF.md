# CADnest Clone — WordPress Theme

A pixel-perfect WordPress replica of **cadnestdesign.com**. Every one of the 11
original pages is reproduced exactly (layout, colours, typography, images) and
recreated in WordPress with its **original slug**, so all indexed URLs keep
working.

Everything the pages need — CSS, web fonts and images — is bundled inside the
theme's `/assets` folder, so the site is fully self-contained and does **not**
depend on GoDaddy or any external host to render.

---

## 1. Install & activate (2 minutes)

1. Log in to WordPress → **Appearance → Themes → Add New → Upload Theme**.
2. Upload **`cadnest-clone.zip`** and click **Install Now**, then **Activate**.
3. On activation the theme automatically:
   - creates all 11 pages with their exact original slugs,
   - sets the **Home** page as the static front page,
   - enables pretty permalinks (`/%postname%/`) if they weren't already on.

You'll see a green "CADnest Clone activated" notice. That's it — visit the site.

### If you ever need to re-run the installer
Go to **Appearance → CADnest Clone** and click **"Install / repair pages"**.
It's safe to run any time — existing pages are never overwritten.

Prefer the command line? With WP-CLI:

```bash
wp theme activate cadnest-clone
wp cadnest install
```

---

## 2. The pages & URLs it creates

| Page | URL |
|------|-----|
| Home | `/` |
| Sample Plans | `/sample-plans/` |
| Quote Request | `/quote-request/` |
| Contact Us | `/contact-us/` |
| Drafting Services for Contractors Los Angeles | `/drafting-services-for-contractors-los-angeles/` |
| Drafting Services Pasadena CA | `/drafting-services-pasadena-ca/` |
| ADU Plans Los Angeles | `/adu-plans-los-angeles/` |
| As-Built Plans Pasadena Los Angeles | `/as-built-plans-pasadena-los-angeles/` |
| Room Addition & Remodel Drafting Los Angeles | `/room-addition-remodel-drafting-los-angeles/` |
| Outsourced CAD Drafting Los Angeles | `/outsourced-cad-drafting-los-angeles/` |
| Privacy Policy | `/privacy-policy/` |

> **URL note:** WordPress serves pretty permalinks with a trailing slash, e.g.
> `/contact-us/`. Your old links without the slash (`/contact-us`) automatically
> 301-redirect to the slash version, so every indexed URL still resolves. If you
> want the URLs to match with *no* trailing slash instead, tell me and I'll flip
> a one-line setting.

---

## 3. How it works (for future editing)

Each page's appearance comes from a captured HTML snapshot in
`/snapshots/<slug>.html`. When a visitor opens a page, the theme loads that
snapshot, points its asset links at `/assets`, and outputs it — a byte-for-byte
copy of the original design.

**To edit page content:** open the matching file in `/snapshots/` (e.g.
`/snapshots/contact-us.html`) and edit the text directly, then re-upload the
theme (or edit via **Appearance → Theme File Editor**). The markup is plain
HTML, so changing wording, phone numbers, etc. is straightforward. Leave the
`__ASSETBASE__` placeholders untouched — they're how images/fonts stay linked.

**To add a brand-new page** that isn't a clone, just create it normally in
WordPress; the theme only takes over the 11 cloned slugs and lets WordPress
handle everything else.

---

## 4. Third-party pieces carried over

- **Google Tag Manager / Analytics / Ads** tags are preserved as-is.
- **Crisp chat** widget is preserved.
- **Quote Request** links out to your existing Google Form — still works.
- **Contact form:** the original form was powered by GoDaddy's own form service.
  Off GoDaddy it will display identically but won't deliver submissions until
  it's wired to a WordPress form handler. I'm happy to connect it to a simple
  mailer or a plugin like WPForms/Contact Form 7 — just say the word.

---

## 5. Requirements

- WordPress 5.5+ and PHP 7.2+ (no plugins required).
- Pretty permalinks enabled (the installer turns them on automatically).

Questions or tweaks — send them over and I'll take care of it.
