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
- **Blog:** uses native WordPress posts + categories (the 5 categories from
  the brief are created automatically). Six placeholder posts are seeded so
  the blog grid isn't empty on day one — replace their body copy with real
  articles before launch.
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
     Blog / Contact) and two footer menus;
   - creates the 5 blog categories and 6 placeholder posts.
   - Nothing is ever overwritten on a later reactivation — each page/menu is
     only created if it doesn't already exist.
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
6. Replace the 6 placeholder blog posts with real articles.

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
  original brief's requirement to anticipate an Arabic version, but the
  Arabic content itself was out of scope for this conversion and still needs
  to be written and connected to a multilingual plugin (e.g. WPML or
  Polylang) if/when that phase happens.
- **Newsletter signup** in the footer is a static form with no backend yet —
  wire it up to whatever email provider you choose (Mailchimp, Brevo, etc.).
- Section markup for repeated components (hero, pillar grid, stats,
  credentials, packages, etc.) lives in `inc/seed-content.php` as PHP
  builder functions fed by the data in `inc/seed-data.php` — the same
  structure the original design's data arrays used, so copy stays easy to
  audit against the source `.dc.html` file.
