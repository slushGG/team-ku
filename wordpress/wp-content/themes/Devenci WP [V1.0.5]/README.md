# Dev Venci — WordPress FSE Block Theme

A full-site-editing conversion of the original TeamKU-4A HTML portfolio. The theme keeps the original dark/orange Web3 visual language, particle background, glitch headings, typing effects, music toggle, filterable portfolio, timeline resume, terminal footer, and member selection stage — while making all five pages editable with native Gutenberg blocks.

## What is included

- `devenci/` — the complete installable block theme.
- `devenci.zip` — installable theme ZIP (theme folder at the root of the archive).
- `backup/original-html-portfolio.zip` — untouched backup of the original HTML project.
- Original source folders (`Homepage/`, `Group/`, `libs/`) are preserved and were not modified.

## Completed features

- Genuine FSE block theme with `theme.json`, block templates, template parts, and patterns.
- Five pages created on activation: group homepage plus four independent member portfolios.
- Group homepage with four linked Member Card blocks and the original character-stage hover interactions.
- Editable headers and footers in the Site Editor, including logo, brand text, navigation, wallet button, terminal, and copyright.
- Editable page sections: hero, about, skills, portfolio, resume, blog, and contact.
- Eight reusable patterns in two theme pattern categories.
- Five custom blocks: Member Card, Skill Bar, Filterable Portfolio, Portfolio Item, Contact Form.
- Native Image blocks for every content image slot, plus attachment-ID-based custom block images, all managed through the Media Library.
- One-time Media Library import of the Ethereum logo only; content images are empty Image blocks with placeholder text and no hardcoded image files.
- Preserved particle background, glitch headings, TypeIt typing, GSAP member entrance, portfolio filtering, animated skill bars, music toggle, toasts, and scroll reveal.
- Responsive layout at desktop, laptop, tablet, and mobile widths with no horizontal overflow.
- Local copies of Particles.js, GSAP, TypeIt, Font Awesome, and background audio; no CDN dependencies.
- AJAX contact form with nonce, honeypot, validation, sanitization, and `wp_mail()` delivery.
- Cache-busted asset enqueueing with `wp_enqueue_style()`, `wp_enqueue_script()`, `get_theme_file_uri()`, and `get_theme_file_path()`.
- Theme setup screen for re-running the idempotent demo import.

## Requirements

- WordPress 6.6 or newer (tested on WordPress 7.1.4).
- PHP 8.0 or newer (tested on PHP 8.4.16).
- MySQL/MariaDB (tested on MySQL 8.0.30).
- GD or Imagick enabled in PHP and a writable `wp-content/uploads` directory for the one-time logo import.
- `wp_mail()` / SMTP configured if you want the contact form to deliver email.

## Installation

1. In WordPress go to **Appearance → Themes → Add New → Upload Theme**.
2. Upload `devenci.zip` and activate **Dev Venci**.
3. On activation the theme automatically:
   - imports the Ethereum logo into the Media Library,
   - creates the five pages listed below,
   - sets the group homepage as the static front page,
   - sets a pretty permalink structure if none exists,
   - sets the Ethereum logo as the site logo and site icon,
   - leaves every content image slot as an empty Image block with placeholder text,
   - assigns the **Member Portfolio** template to member 1 and **Member Portfolio (Generic)** to members 2–4.

If automatic import does not run, open **Appearance → Dev Venci Setup** and click **Import Missing Pages and Images**. The import is safe to run again: existing pages are never overwritten.

### Page map

| Page                      | Slug       | Template                   |
| ------------------------- | ---------- | -------------------------- |
| Group homepage            | `home`     | `front-page.html`          |
| Member 1 — Tyrone Parreño | `member-1` | Member Portfolio           |
| Member 2                  | `member-2` | Member Portfolio (Generic) |
| Member 3                  | `member-3` | Member Portfolio (Generic) |
| Member 4                  | `member-4` | Member Portfolio (Generic) |

## Editing the site

### Group homepage

Open **Pages → TeamKU-4A → Edit**. The hero and the four member cards are normal blocks. Each **Member Card** block has an inspector panel for its portrait, alt text, animation delay, and portfolio URL.

### Member portfolios

Open any member page in the Page Editor. Every section is a Group block, so sections can be reordered, duplicated, removed, or replaced:

- Hero (`#home`)
- About (`#about`)
- Skills (`#services`)
- Portfolio (`#portfolio`)
- Resume (`#resume`)
- Blog (`#blog`)
- Contact (`#contact`)

The four member pages share the same section layout but use independent page content. Member 1 keeps the original Web3 copy and terminal footer. Members 2–4 use the **Member Portfolio (Generic)** template with neutral placeholder copy and a generic footer, so they are not presented as Web3 portfolios.

### Header and footer

Go to **Appearance → Editor → Patterns → Template Parts**, or **Appearance → Editor → Templates** and open the header/footer areas. The header includes a Site Logo block (Media Library), editable brand headings, the core Navigation block, and a home button. The footer contains the editable terminal block and copyright paragraph.

### Images

Content images are intentionally not bundled or hardcoded. Every photo slot in the seeded pages is an empty Image block with a dashed placeholder label, for example **“Add your profile image”**. Open the page in the Page Editor and choose **Upload** or **Media Library** on the image block to add the real image. The placeholder label hides automatically once an image is present.

- Core Image blocks: select the empty block → **Upload** or **Media Library**.
- Member Card blocks: **Member Card** panel → **Select image**.
- Portfolio Item blocks: **Portfolio Item** panel → **Select image**.
- Header logo: Site Editor → Site Logo block → **Replace**, or **Site Identity → Logo**.

The only bundled image is `assets/images/eth-icon.png`, used for the site logo and site icon. The original HTML images remain available in the untouched backup archive, but page content never references them.

### Colors, fonts, spacing

Use **Appearance → Editor → Styles** (Global Styles). The palette and font families are defined in `theme.json`, and the theme styles read the generated preset variables. Buttons, links, headings, backgrounds, and spacing can be adjusted globally. Individual blocks can also use the normal color, typography, and spacing controls.

### Reusable patterns

The block inserter contains a **Dev Venci** pattern category:

- Group Hero
- Member Directory
- Portfolio Hero
- About Section
- Skills Section
- Projects Section
- Resume Section
- Contact Section

Patterns contain empty image blocks on purpose: choose an image after inserting the pattern, so every image stays in the Media Library. The seeded pages also use empty Image blocks for you to fill in.

### Custom blocks

The theme adds five blocks under the **Dev Venci** category:

1. **Member Card** — linked portrait with InnerBlocks for the name/role label.
2. **Skill Bar** — inline label plus animated percentage bar.
3. **Filterable Portfolio** — InnerBlocks container. Filter buttons are generated automatically from the **Filter category** field of each Portfolio Item.
4. **Portfolio Item** — Media Library image, optional external link, and InnerBlocks for the title/description overlay.
5. **Contact Form** — accessible AJAX form with nonce, honeypot, and sanitized server-side handling.

### Portfolio filtering

Each Portfolio Item has a **Filter category** text field. Items sharing a category share one filter button. The first item’s category order determines the button order, with **All** always first. Change or add categories in the block sidebar; no code changes are required.

### Music and effects

Particles.js, GSAP, TypeIt, Font Awesome, and the background audio file are bundled locally and enqueued through WordPress. Browsers may block autoplay until the first interaction — the original site had the same limitation. The floating button toggles playback.

## Theme structure

```
devenci/
├── theme.json
├── style.css
├── functions.php
├── templates/
│   ├── front-page.html
│   ├── member-portfolio.html
│   ├── member-portfolio-generic.html
│   ├── page.html
│   ├── index.html
│   ├── single.html
│   └── 404.html
├── parts/
│   ├── group-header.html
│   ├── member-header.html
│   ├── group-footer.html
│   ├── member-footer.html
│   └── member-footer-generic.html
├── patterns/           # 8 reusable block patterns
├── blocks/             # 5 custom Gutenberg blocks + render.php files
├── inc/
│   ├── setup.php       # supports, assets, chrome
│   ├── blocks.php      # block registration and helpers
│   ├── media.php       # Ethereum logo import
│   ├── content.php     # portfolio content as block markup
│   ├── importer.php    # page/media setup and setup screen
│   └── contact-form.php
└── assets/
    ├── css/            # base, components, group, editor styles
    ├── js/theme.js     # adapted interactions
    ├── vendor/         # GSAP, Particles.js, TypeIt, Font Awesome
    ├── audio/
    └── images/         # eth-icon.png only; content images are placeholders
```

## Testing performed

- Activated the theme on a fresh WordPress 7.1.4 installation with PHP 8.4.16 and MySQL 8.0.30.
- Verified automatic demo import after theme activation: 5 pages, one Media Library attachment (the Ethereum logo), front page assignment, logo and site icon.
- Verified member 1 uses the **Member Portfolio** template and members 2–4 use **Member Portfolio (Generic)**.
- Verified all content image slots are empty Image blocks with placeholder labels and no image URLs.
- Opened all five pages in Gutenberg: zero block-validation warnings and zero editor console errors.
- Verified that assigning an image to an empty Image block hides the placeholder label and renders the photo on the front end.
- Opened the Site Editor and the **Member Header** template part: the canvas loads, the Site Logo and Navigation blocks are editable, and **Save** is available.
- Verified the header/footer template parts render once (no nested `header`/`footer` elements).
- Verified all CSS, JavaScript, Font Awesome, audio, and upload paths return HTTP 200.
- Verified no PHP warnings/fatals on the front end.
- Verified portfolio filtering: selecting **NFT** reduced 4 items to 1; **All** restored 4.
- Verified skill bars animate from 0% to their configured values when scrolled into view.
- Verified responsive layout at 1440, 1024, 768, and 390 px widths: no horizontal overflow; the navigation collapses at the original 992 px breakpoint; mobile menu opens with the dark drawer design.
- Verified the contact form sends a valid AJAX request with nonce and returns a sanitized JSON response. In the test environment `wp_mail()` was not configured, so the form correctly returned a delivery error instead of claiming success.

## Known limitations / manual testing still recommended

- **Email delivery:** the contact form uses `wp_mail()`. Configure SMTP or a mail plugin on the hosting server, then send a real test message. Use the `devenci_contact_recipient` filter to change the recipient.
- **Audio autoplay:** browser autoplay policies block music until the first user interaction; the toggle handles this gracefully.
- **Members 2–4 starter content:** the original HTML project only contained placeholder sections for these members. They now ship with the complete design, neutral non-Web3 placeholder copy, and no invented personal achievements. Replace the copy per member.
- **Member positions:** the group stage is designed for four cards with CSS-defined positions. Adding a fifth Member Card works in the editor but needs a small CSS position rule for a balanced stage layout.
- **Shared member header:** header navigation, brand, and copyright are shared template parts. Member 1 keeps the Web3 terminal footer; members 2–4 use the generic member footer template.
- **Footer copyright** is set to the site brand (`Dev Venci`) so it is not incorrectly attributed to every member page.
- **Adding images** uses the native Media Library controls. Empty image slots show dashed placeholder text until an image is selected.
- **Cross-browser:** automated visual checks used Chromium. Safari/Firefox and very large TV viewports should be spot-checked manually.
- **WordPress 6.6–6.8:** the theme requires 6.6+, but the automated runtime test was performed on 7.1.4 only.
- **Patterns** intentionally use empty image placeholders; select images after inserting them.
- The `backup` folder is a development/archive artifact and is not part of the installable ZIP.

## Original project safety

The original HTML/CSS/JS files remain in place and a ZIP backup is at `backup/original-html-portfolio.zip`. The theme never writes to or deletes those source files.
