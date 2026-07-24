# CADnest Design — WordPress Clone

A pixel-perfect WordPress theme replica of [cadnestdesign.com](https://www.cadnestdesign.com/),
with a one-click installer that recreates all 11 original pages using their exact
original slugs for full URL parity.

## Contents
- `cadnest-clone/` — the installable WordPress theme (self-contained: bundled CSS, fonts, images)
- `capture.py` / `localize.py` — the build pipeline that captured the live site and produced the self-contained snapshots
- `router.php` / `compare.py` — local test harness used to verify pixel parity vs the live site

## Install
See [`cadnest-clone/README-HANDOFF.md`](cadnest-clone/README-HANDOFF.md).

Upload `cadnest-clone.zip` via **Appearance → Themes → Add New → Upload Theme**,
activate, and the installer creates every page automatically.
