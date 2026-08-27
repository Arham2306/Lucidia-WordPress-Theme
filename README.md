# Lucidia — Modern Editorial & Magazine WordPress Theme

[![WordPress](https://img.shields.io/badge/WordPress-6.0%2B-blue.svg?logo=wordpress&logoColor=white)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-7.4%20|%208.0%20|%208.1%20|%208.2%20|%208.3-777BB4.svg?logo=php&logoColor=white)](https://php.net)
[![Elementor](https://img.shields.io/badge/Elementor-3.5%2B%20Ready-92003B.svg?logo=elementor&logoColor=white)](https://elementor.com)
[![JavaScript](https://img.shields.io/badge/JavaScript-Vanilla%20ES6%2B%20(No%20jQuery)-F7DF1E.svg?logo=javascript&logoColor=black)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![License](https://img.shields.io/badge/License-GPLv2%20or%20later-green.svg)](http://www.gnu.org/licenses/gpl-2.0.html)

**Lucidia** is a modern, high-performance, lightweight editorial and magazine WordPress theme engineered for publications, independent journalists, digital magazines, essays, and longform blogs. Built from the ground up with clean semantic HTML5, zero jQuery dependencies, self-hosted typography, and complete Elementor page builder integration.

---

## 🌟 Key Highlights

- ⚡ **Core Web Vitals Optimized**: 100/100 performance potential with deferred JavaScript, self-hosted WOFF2 fonts, LCP eager image preloading, and zero external blocking assets.
- 🌓 **Instant Dark Mode & Reader Mode**: Seamless theme switching with OS auto-detection, distraction-free reading mode (Light, Sepia, Dark), and custom font scaling.
- 🔍 **Instant Smart Live Search**: Lightning-fast AJAX live search overlay with debounced queries, search history, and full keyboard navigation (`/`, `Cmd/Ctrl+K`, `Escape`).
- 🧩 **Handcrafted Elementor Widget Suite**: 11 bespoke editorial widgets built natively for Elementor with deep query builders, select2 multi-filtering, line clamps, and responsive styling controls.
- 📑 **Cached Table of Contents**: Automated `h2`/`h3` heading parser with WordPress transient caching and smooth scroll tracking.
- 🎨 **Fluid Typography & Design System**: Responsive CSS custom properties, balanced modular scale, editorial serif/sans pairings, and custom SVG icon system.
- ♿ **Accessibility & Standards Compliant**: WCAG 2.1 AA compliant, ARIA landmarks, visible focus rings, skip-to-content links, and full Schema.org structured microdata.

---

## 🧩 Bespoke Elementor Widget Suite

Lucidia includes a dedicated **Lucidia** category in Elementor with 11 handcrafted editorial widgets:

| Widget | Description | Key Capabilities |
|---|---|---|
| **Hero Featured Story** | Dynamic top-of-page hero presentation | Split 2-column, Full-Bleed Cover Overlay, or Stacked layout; column ratio slider; hover zoom; circular author avatar. |
| **Editorial Posts Grid** | Multi-column responsive magazine grid | 1 to 6 columns; 5 aspect ratios (`16:9`, `16:10`, `4:3`, `3:2`, `1:1`); title/excerpt CSS line clamping; numbered or prev/next pagination. |
| **Horizontal Post List** | Editorial story card list | 1 or 2 columns; Left/Right thumbnail placement; width sliders; presets (`Standard`, `Borderless`, `Divided`); smart full-width `.no-media` text fallback. |
| **Magazine Compact Spotlight** | High-density trending & popular feeds | 4 numbered ranking badge styles (`Bold Number 01`, `Solid Pill`, `Outlined Pill`, `Thumbnail Overlay`); 1 or 2 columns; query by comment popularity. |
| **Classic Editorial Stream** | Full-width longform blog feed stream | Max reading width slider; 4 presets (`Standard`, `Borderless`, `Divided`, `Elevated`); "Continue Reading" CTA button; pagination. |
| **Section Header Bar** | Publication section dividers & heading bars | Lucidia SVG icons / Elementor Icon Library; subtitles; 4 divider styles (`Bold Underline`, `Subtle Line`, `Accent Indicator Bar`, `None`); "View All" action links. |
| **Newsletter Subscription Box** | Email capture callouts & banners | Built-in endpoint, 3rd-party shortcode embed (Mailchimp, MailPoet), or custom HTML/JS embed (Substack, ConvertKit, Beehiiv); ambient corner glow; 4 presets. |
| **Author Spotlight Card** | Rich author biography profile card | Current post author, dropdown registered user, or custom manual profile; circular avatar with ring border; post counter; bio; social links (X, LinkedIn, FB, Website, Email). |
| **Interactive Social Share Bar** | Multi-network viral share bar | X / Twitter, Facebook, LinkedIn, Email; 1-click clipboard copy with animated toast tooltip; Horizontal/Vertical orientations; 5 button presets. |
| **Editorial Navigation Menu** | Standalone responsive nav & menu builder | Horizontal bar, Vertical list, or Scrollable Pill strip; 4 pointer indicators (`Underline`, `Pill`, `Dot`, `Framed`); 3-level dropdown depth with animations; tablet/mobile responsive accordions. |
| **Editorial Smart Search** | In-page real-time search bar & modal trigger | 3 display modes (`Inline Live Search`, `Modal Trigger Button`, `Classic Form`); debounced REST API live dropdown with thumbnails, categories & dates; `⌘K` shortcut chip. |



---

## 🚀 Performance & Architectural Features

### Zero jQuery Dependency
All interactive front-end components (Dark Mode toggle, AJAX Live Search, Reading Mode, Table of Contents, Mobile Off-canvas Navigation, Reading Progress Bar, and Social Copy tooltips) are written in **pure, vanilla ES6+ JavaScript**.

### Self-Hosted Typography
- **Headings**: Playfair Display (Editorial Serif) & Plus Jakarta Sans (Modern Sans-serif)
- **Body**: Plus Jakarta Sans (Highly readable geometric sans)
- **Monospace**: JetBrains Mono (Clean code blocks)
- Fully self-hosted locally in `assets/fonts/` as lightweight `.woff2` files with `font-display: swap` for instant rendering and GDPR compliance.

### Smart Image & Asset Optimization
- Automatic `fetchpriority="high"` and `loading="eager"` applied to top-of-page hero featured images for instant Largest Contentful Paint (LCP).
- Native browser lazy loading (`loading="lazy"`) and asynchronous decoding (`decoding="async"`) on all grid, list, and archive thumbnails.
- Automatic image size generation tailored to editorial card ratios (`custom-theme-hero`, `custom-theme-featured`, `custom-theme-grid`, `custom-theme-compact`, `custom-theme-avatar`).

---

## 🛠️ Installation & Setup

### Requirements
- WordPress 6.0 or higher
- PHP 7.4 or higher (Fully tested up to PHP 8.3+)
- Optional: [Elementor Website Builder](https://wordpress.org/plugins/elementor/) (3.5+ recommended) for visual drag-and-drop page building.

### Quick Start
1. Download or clone this repository into your WordPress themes directory:
   ```bash
   cd wp-content/themes/
   git clone https://github.com/your-username/lucidia.git custom-theme
   ```
2. In your WordPress Admin Dashboard, navigate to **Appearance > Themes**.
3. Locate **Lucidia** and click **Activate**.
4. *(Optional)* Install and activate **Elementor** to use the custom Lucidia widget suite on any page.

---

## 📁 Codebase Structure

```
lucidia/
├── 404.php                            # 404 Error page template with smart search & recent posts
├── archive.php                        # Category, tag, and date archive template
├── author.php                         # Dedicated author profile & article archive
├── footer.php                         # Footer template with multi-column widgets & copyright
├── front-page.php                     # Dynamic magazine homepage template
├── functions.php                      # Theme setup, asset enqueues, and core hooks
├── header.php                         # Header template with smart search & off-canvas drawer
├── home.php                           # Blog index stream template
├── index.php                          # Fallback template
├── page.php                           # Standard page template
├── page-blank-canvas.php              # Full blank canvas template for page builders
├── page-full-width.php                # Full width container template
├── search.php                         # Search results template with query highlights
├── single.php                         # Single article template with layout switchers
├── style.css                          # Theme metadata and baseline definitions
│
├── assets/
│   ├── css/
│   │   ├── admin-options.css          # Dedicated theme options admin styling
│   │   └── main.css                   # Core responsive stylesheet & design system
│   ├── fonts/                         # Self-hosted WOFF2 font files
│   └── js/
│       ├── admin-options.js           # Admin settings panel interactive scripts
│       └── main.js                    # Core vanilla JS interactive suite
│
├── inc/
│   ├── admin-options.php              # Dedicated Theme Options Dashboard page
│   ├── customizer.php                 # WordPress Live Customizer integration
│   ├── helpers.php                    # Reading time calculations, share links & TOC parser
│   ├── template-functions.php         # SVG icon repository, body classes & schema markup
│   ├── template-tags.php              # Category badges, posted-on dates, author avatars
│   ├── thumbnail-regenerator.php      # Custom image size registrations & thumbnail tools
│   ├── widgets.php                    # Legacy WordPress widget areas & sidebars
│   └── elementor/
│       ├── class-elementor-integration.php  # Elementor coordinator & category registrar
│       └── widgets/                         # 9 Bespoke Elementor widgets
│           ├── class-widget-hero-post.php
│           ├── class-widget-post-grid.php
│           ├── class-widget-post-list.php
│           ├── class-widget-compact-spotlight.php
│           ├── class-widget-classic-stream.php
│           ├── class-widget-section-header.php
│           ├── class-widget-newsletter-box.php
│           ├── class-widget-author-box.php
│           └── class-widget-social-share.php
│
└── template-parts/
    ├── author-box.php                 # Single post author bio box
    ├── content-card-classic.php       # Longform stream card
    ├── content-card-compact.php       # High-density compact card
    ├── content-card-grid.php          # Magazine grid card
    ├── content-card-list.php          # Horizontal post list card
    ├── content-card.php               # Standard article card
    ├── content-none.php               # Empty query fallback
    ├── content-single-magazine.php    # Single post magazine layout
    ├── content-single-minimal.php     # Single post minimalist layout
    ├── content-single.php             # Single post standard layout
    ├── newsletter.php                 # Newsletter signup callout
    ├── related-posts.php              # Contextual related stories section
    └── social-share.php               # Social share buttons
```

---

## 🎨 Theme Customization

Lucidia provides two powerful customization workflows:

### 1. WordPress Live Customizer (`Appearance > Customize`)
- **Branding & Identity**: Site logo, dark mode logo variant, favicon, and publication tagline.
- **Color Presets**: Accent colors, surface tints, dark mode defaults, and link colors.
- **Typography Settings**: Serif vs Sans heading toggle, body font sizing, and line height scale.
- **Header & Navigation**: Sticky header toggle, reading progress bar, CTA button URL.
- **Single Post Options**: Reading time display, Table of Contents auto-injection, related posts count, and author bio box toggle.

### 2. Dedicated Lucidia Dashboard (`Appearance > Lucidia Options`)
- Centralized administration panel for site-wide defaults, custom scripts (Header/Footer tracking code injection), social media profiles, and newsletter API endpoints.

---

## 💻 Developer & Hooks API

### CSS Custom Properties
The entire theme styling is driven by semantic CSS variables defined in `:root`:

```css
:root {
  --color-accent: #2563eb;
  --color-accent-hover: #1d4ed8;
  --color-surface: #ffffff;
  --color-surface-subtle: #f8fafc;
  --color-text-main: #0f172a;
  --color-text-secondary: #475569;
  --color-border: #e2e8f0;
  
  --font-heading: 'Playfair Display', Georgia, serif;
  --font-body: 'Plus Jakarta Sans', system-ui, sans-serif;
  --font-mono: 'JetBrains Mono', monospace;
}

[data-theme="dark"] {
  --color-surface: #0f172a;
  --color-surface-subtle: #1e293b;
  --color-text-main: #f8fafc;
  --color-text-secondary: #94a3b8;
  --color-border: #334155;
}
```

### Useful Template Functions & Helpers
- `custom_theme_posted_on()`: Outputs formatted publication date with microdata.
- `custom_theme_posted_by( $show_avatar )`: Outputs author byline with circular avatar.
- `custom_theme_reading_time_badge()`: Calculates estimated read time based on word count.
- `custom_theme_category_badge()`: Renders stylized primary category pill badge.
- `custom_theme_svg_icon( $name, $class )`: Renders clean, inline accessible SVGs.

---

## 📄 License & Copyright

- **License**: Distributed under the terms of the **GNU General Public License v2 or later** ([GPL-2.0-or-later](http://www.gnu.org/licenses/gpl-2.0.html)).
- Copyright &copy; 2026 Lucidia Editorial Theme Developer.

---

<div align="center">
  <sub>Crafted with passion for writers, editors, and modern digital publications.</sub>
</div>
