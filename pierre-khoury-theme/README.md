# Pierre Khoury — WordPress Theme

A custom WordPress **block theme** (Full Site Editing) converted from the approved
Claude Design prototype (`Pierre Khoury Website v3.dc.html`). All copy, structure,
colors and typography are ported 1:1 from that design.

## What this is

- **Theme type:** block theme (`theme.json` + block templates in `templates/`
  and `parts/`), editable through Appearance → Editor / Site Editor.
- **Pages:** Home, About, Services (landing), 5 service-pillar pages, Track
  Record, Blog (posts index) and Contact are created automatically the first
  time the theme is activated, with the exact copy from the design.
- **Blog:** uses native WordPress posts + categories. The real archive from
  pierrekhoury.com (225 published posts — mostly Arabic-language media
  commentary on economics, finance, the Lebanese lira, blockchain, etc.) is
  bundled as a WordPress export at `inc/data/legacy-posts.xml` and imported
  in batches from **Settings → Pierre Khoury** (see below) — titles, content,
  original publish dates, categories and tags are all preserved as-is.
- **Contact form:** styled for [Contact Form 7](https://wordpress.org/plugins/contact-form-7/).
  Install the plugin, create a form with the fields below, and enter its ID
  under **Settings → Pierre Khoury**.
- **Colors/fonts:** defined in `theme.json` using the same `oklch()` values as
  the source design, editable via the Site Editor's Styles panel. Fonts are
  Space Grotesk (UI/body) and Newsreader (editorial pull-quotes), loaded from
  Google Fonts.

## Install

1. Zip the `pierre-khoury-theme` folder and upload it under
   **Appearance → Themes → Add New → Upload Theme**, or copy it directly into
   `wp-content/themes/`.
2. Activate it. This automatically:
   - creates the Home/About/Services/Track Record/Blog/Contact pages and the
     5 service pages, with the approved copy;
   - sets Home as the static front page and Blog as the posts page;
   - creates the primary navigation menu (About / Services ▾ / Track Record /
     Blog / Contact) and two footer menus.
   - Nothing is ever overwritten on a later reactivation — each page/menu is
     only created if it doesn't already exist.
   - It does **not** import the blog archive automatically (225 posts can
     easily exceed a shared host's execution time limit in one request) —
     that's a deliberate, separate step below.
3. Go to **Settings → Pierre Khoury** and set:
   - **WhatsApp number** (digits + country code, e.g. `96170000000`) — powers
     every "Message on WhatsApp" button.
   - **Contact Form 7 form ID**, once you've installed CF7 and created the
     form (see below).
4. Install & activate the **Contact Form 7** plugin, then create a form under
   **Contact → Contact Forms** with these fields (matching the design brief):
   - Full Name (text, required)
   - Organization / Institution (text, required)
   - Email (email, required)
   - Phone / WhatsApp (tel, required)
   - Country / City (text, required)
   - Service of Interest (select, required): Gen Z Workplace Expertise /
     Financial Consulting & Training / Career Advisory & Lifelong Learning /
     Blockchain Training / Training Center Launch & Advisory / Other — Not
     Sure Yet
   - Message (textarea, required)
   - Submit button labeled "Get My Proposal"

   Copy the form's numeric ID (shown in the forms list, or in its shortcode
   `[contact-form-7 id="123" ...]`) into Settings → Pierre Khoury.
5. Replace the gray placeholder photo blocks (hero, "Get to Know Pierre",
   About portrait, blog thumbnails) with real photography — they're plain
   image placeholders you can swap directly in the block editor.
6. At **Settings → Pierre Khoury**, under "Legacy blog archive", click
   **Import next batch of posts** repeatedly (40 posts per click, ~6 clicks
   for all 225) until it reports everything imported. Each click is safe to
   repeat — already-imported posts are skipped, so there's no risk of
   duplicates if you click it again or a request times out partway through.
   Featured images aren't part of this import (the original export's image
   attachments are intentionally skipped) — add those directly from the
   Media Library on whichever posts need one.

## Notes for whoever maintains this next

- **Internal links** ("Book a Consultation", pillar cards, footer menus,
  etc.) resolve to the seeded pages' real permalinks at the time the content
  is created — this is intentionally simpler than shipping a plugin
  dependency for link resolution. If you ever change one of the seeded
  pages' slugs, re-check pages that link to it, or use a search/replace
  plugin if you rename several at once.
- **Re-running content setup:** Settings → Pierre Khoury has a "Re-run
  content setup" button. It only fills in anything missing — it never
  touches a page that already exists, so it's safe to click if a page or
  menu didn't get created (e.g. theme was active before this seeder shipped).
- **RTL/Arabic:** the CSS includes baseline RTL rules (`body.rtl`) per the
  original brief's requirement to anticipate an Arabic version. The imported
  legacy archive is itself almost entirely Arabic-language content, but it's
  imported as plain LTR posts for now (no `dir="rtl"` per-post handling and
  no multilingual plugin wired up) — if/when a proper Arabic version of the
  new site is built, connect a plugin like WPML or Polylang and revisit
  per-post text direction at that point.
- **Legacy archive categories:** the real pierrekhoury.com posts use their
  own categories (Economics, Finance, Lira, Business, Career & Education,
  Blockchain, Cryptocurrencies, China Updates, Russia, Guest Writers,
  Uncategorized) rather than the 5 categories described in the original
  design brief — those are just what the old site actually used, preserved
  as-is. `pk_category_to_pillar_map()` in `inc/blocks.php` maps the relevant
  ones to a service pillar so those posts show a "related service" card;
  categories with no obvious match (China Updates, Russia, Guest Writers,
  Uncategorized) simply don't show that card.
- **Legacy import internals:** `inc/import-legacy-posts.php` parses
  `inc/data/legacy-posts.xml` (the original WXR export) directly rather than
  going through the core WordPress Importer plugin, specifically so it can
  skip the 64 media attachments and per-post builder/SEO metadata that came
  with the export and only bring in title/content/date/categories/tags. Each
  imported post is tagged with its original WXR `<guid>` as post meta
  (`_pk_legacy_guid`) so re-running the batch importer never creates
  duplicates. The XML file itself can be deleted from the theme once the
  import is complete, if you'd rather not ship it long-term.
- **Newsletter signup** in the footer is a static form with no backend yet —
  wire it up to whatever email provider you choose (Mailchimp, Brevo, etc.).
- Section markup for repeated components (hero, pillar grid, stats,
  credentials, packages, etc.) lives in `inc/seed-content.php` as PHP
  builder functions fed by the data in `inc/seed-data.php` — the same
  structure the original design's data arrays used, so copy stays easy to
  audit against the source `.dc.html` file.
