# CADnest — WordPress theme (hand-off notes)

A custom WordPress theme that reproduces the CADnest website (cadnestdesign.com)
with **real, editable WordPress content**, clean license-free code, and a
**working contact form**. No code from your previous host's platform ships with
this theme.

- Theme version: **2.0.0**
- Requires: WordPress 5.9+ and PHP 7.4+

---

## 1. Install

1. Zip the `cadnest-clone` folder (or use the release zip provided).
2. WordPress admin → **Appearance → Themes → Add New → Upload Theme** → choose the
   zip → **Install** → **Activate**.
3. On activation the theme **automatically creates all 11 pages** at their exact
   original URLs and sets the Home page as the front page. You'll see a success
   notice linking to the settings screen.
4. Go to **Settings → Permalinks** and click **Save** once (this makes sure the
   pretty `/slug/` URLs are flushed — WordPress sometimes needs this nudge).

If for any reason the pages aren't created, go to **Appearance → CADnest** and
click **Install / repair pages**. It's safe to run any time and never overwrites
your edits. (There's also a CLI command: `wp cadnest install`.)

---

## 2. Editing content — this is now real WordPress content

Every page's text and images live inside WordPress, so you edit them the normal
way:

- **Pages → (any page) → Edit.**
- Each section of the page is a **Custom HTML block** you can open and edit
  directly. Change the wording, swap a link, etc., and **Update**.
- Because the content is real `post_content`, **Yoast SEO (and any other SEO
  plugin) reads and manages it normally** — titles, meta descriptions, analysis,
  sitemaps all work.

> Tip: to replace an image, upload it to the Media Library, copy its URL, and
> paste it into the relevant `<img src="...">` inside the Custom HTML block. The
> bundled original images live in the theme's `/assets` folder.

If you ever want a page reset back to the original design, tick the reset box on
**Appearance → CADnest** before clicking Install / repair (this discards edits to
those pages only).

---

## 3. Contact form (now functional)

The Home and Contact pages have a real working form.

1. Go to **Appearance → CADnest** and set **"Send enquiries to"** to the email
   address that should receive messages (defaults to your admin email).
2. Submissions are validated (with spam honeypot + nonce) and emailed to that
   address via WordPress mail. The visitor sees a confirmation banner.

**Deliverability:** WordPress's default `wp_mail()` uses the server's PHP mail,
which many hosts treat as spam. For reliable delivery I recommend a free SMTP
plugin (e.g. *WP Mail SMTP*) pointed at your email provider — 5-minute setup. Happy
to configure this for you if you'd like.

---

## 4. URLs / SEO parity

The installer creates every page with its original slug, so all indexed URLs keep
resolving:

| Page | URL |
|------|-----|
| Home | `/` |
| Sample Plans | `/sample-plans/` |
| Quote Request | `/quote-request/` |
| Contact Us | `/contact-us/` |
| Contractors | `/drafting-services-for-contractors-los-angeles/` |
| Drafting Services | `/drafting-services-pasadena-ca/` |
| Los Angeles ADU | `/adu-plans-los-angeles/` |
| As-Built Plans | `/as-built-plans-pasadena-los-angeles/` |
| Room Addition & Remodel | `/room-addition-remodel-drafting-los-angeles/` |
| Outsourced CAD Drafting | `/outsourced-cad-drafting-los-angeles/` |
| Privacy Policy | `/privacy-policy/` |

**Trailing slash:** WordPress serves pretty permalinks with a trailing slash
(`/contact-us/`). Your old non-slash URLs (`/contact-us`) automatically
**301-redirect** to the slash version, so nothing breaks. If you'd prefer the
exact non-slash form, say the word and I'll switch the permalink structure.

---

## 5. What was removed / replaced (clean-up)

- **Your old platform's JavaScript runtime** (`consent.js`, `forms.js`,
  `customLightbox.js`, `accessNavigationList.js`, `downloadWarning.js`) — removed
  entirely. None of it ships with this theme.
- **Cookie-consent / privacy-settings banners and the "consent to load map"
  overlay** — removed. On the Contact page the map is now a **real, live Google
  Maps embed** of your address (967 E Colorado Blvd, Pasadena) with no consent gate.
- **Third-party marketing tags** that were baked into the old export (Google Tag
  Manager / Analytics / Crisp chat) are **not** shipped in the theme — that keeps
  it clean and avoids hard-coding tracking IDs. If you want analytics or live chat
  back, add them with a standard plugin or the Header snippet and I'll help wire it.

The navigation (desktop "more" dropdown and the mobile menu) is now driven purely
by CSS + a tiny bit of my own vanilla JS, so it works with no third-party code.

---

## 6. Theme file map

```
cadnest-clone/
  style.css            theme header
  functions.php        enqueues, content filters, wiring
  header.php / footer.php   shared site chrome
  page.php / index.php / 404.php   templates
  parts/header.html, parts/footer.html   the shared header & footer markup
  inc/installer.php    creates the pages (+ wp cadnest install)
  inc/contact.php      contact-form handler
  inc/admin.php        Appearance → CADnest screen (install + email)
  inc/pages/*.html     each page's editable content (seeded into WP on install)
  assets/              bundled CSS, fonts and images
  js/theme.js          small nav enhancements (no vendor code)
```

Any questions, just message me. — Anirudha
