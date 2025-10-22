# 🧩 Shopido Elements

**Shopido Elements** is a lightweight, modular **Elementor extension** that brings new creative widgets — Stories, AJAX Search, Ticker Carousel, and Counter — to WordPress.  
Designed for speed, extendability, and seamless integration with Elementor and WooCommerce.

---

## 📁 Folder Structure

```
shopido-elements/
├─ shopido-elements.php              ← Main plugin file (plugin header + init include)
├─ includes/
│  ├─ init.php                       ← Bootstrap: textdomain, CPT, assets, Elementor category, AJAX, widget registry
│  ├─ stories-cpt.php                ← Custom Post Type: `shopido_story` + media metabox
│  ├─ ajax/
│  │  └─ ajax-search.php             ← AJAX search handler (wp_ajax_* / _nopriv)
│  └─ widgets/
│     ├─ class-stories.php                     ← Stories widget (`SEA\Widgets\Stories`)
│     ├─ class-ajax-search.php                 ← AJAX Search widget (`Shopido_Ajax_Search`)
│     ├─ class-shopido-ticker-carousel.php     ← Infinite ticker widget
│     ├─ class-shopido-counter.php             ← Counter widget
│     ├─ class-shopido-read-more.php           ← Legacy
│     ├─ class-shopido-breadcrumb.php          ← Legacy
│     ├─ class-shopido-product-carousel.php    ← Legacy
│     └─ class-shopido-tabbed-product-carousel.php ← Legacy
└─ assets/
   ├─ css/
   │  ├─ story.css
   │  ├─ ajax-search.css
   │  ├─ ticker-carousel.css
   │  ├─ counter.css
   │  ├─ read-more.css
   │  ├─ breadcrumb.css
   │  ├─ product-carousel.css
   │  └─ tabbed-carousel.css
   └─ js/
      ├─ story.js
      ├─ ajax-search.js
      ├─ ticker-carousel.js
      ├─ counter.js
      ├─ read-more.js
      ├─ product-carousel.js
      └─ tabbed-carousel.js
```

---

## ⚙️ Initialization (`includes/init.php`)

- Loads textdomain `shopido-widgets-pack`
- Registers Swiper **v8.4.5** (only if not already available)
- Registers all plugin CSS/JS files using `filemtime` versioning
- Registers Elementor category: **Shopido**
- Loads and registers all widgets dynamically
- Includes `stories-cpt.php` and `ajax/ajax-search.php`
- Standardized handles naming: `shopido-{module}`

---

## 🧱 Widgets Overview

### 🟣 Stories (`SEA\Widgets\Stories`)
Grid-based story viewer powered by the custom post type `shopido_story`.

**Features**
- Responsive grid with featured images
- Opens popup viewer (image 20s, video duration or fallback 20s)
- Like button (UI only)
- Preload next story
- Fully accessible: keyboard navigation & focus trap

---

### 🔵 AJAX Search (`Shopido_Ajax_Search`)
Instant AJAX search across multiple post types or WooCommerce products.

**Features**
- Post type / taxonomy filters
- Minimum character, limit, order, stock-only (Woo)
- “View all” button (optional)
- Card/List layouts with responsive columns
- JSON output rendered via JS templates

---

### 🟢 Ticker Carousel (`Shopido_Ticker_Carousel`)
Infinite scrolling ticker with smooth CSS animation.

**Features**
- Repeater: text/link, colors, padding, radius
- CSS Variables: `--gap`, `--speed`, `--dir`
- Pause on hover/focus
- Direction RTL/LTR supported

---

### 🟠 Counter (`Shopido_Counter`)
Animated numeric counter with configurable animation and formatting.

**Features**
- Start, end, duration
- Count up/down
- Thousands separator formatting
- Fully stylable in Elementor

---

## 🗂️ Custom Post Type: `shopido_story`

Defined in `includes/stories-cpt.php`

- `register_post_type('shopido_story', …)`
- Media metabox: select image/video from Media Library
- Stores `_story_media_id` & `_story_media_type`

---

## 🧩 AJAX Handler

File: `includes/ajax/ajax-search.php`

**Hooks**
- `wp_ajax_shopido_ajax_search`
- `wp_ajax_nopriv_shopido_ajax_search`

**Parameters**
`q`, `post_types`, `taxonomy`, `term_ids`, `min_chars`, `limit`, `orderby`, `order`, `only_instock`

**Output**
JSON response array → `[ { title, url, thumb, price? } ]`

---

## 🧠 Developer Guide

### Add a New Widget

1. Create `includes/widgets/class-your-widget.php`
2. Extend `\Elementor\Widget_Base`
3. Implement `get_style_depends()` / `get_script_depends()`
4. Add CSS/JS in `/assets/`
5. Register handles inside `includes/init.php`
6. Include & register class

---

## 🔒 Security

- Nonce check (`shopido_ajax_nonce`)
- Sanitization: `sanitize_text_field`, `sanitize_key`, `absint`
- Escaping: `esc_html`, `esc_url`, `wp_kses_post`
- Capability checks for CPT/meta saving

---

## 🌐 Localization

- Textdomain: `shopido-widgets-pack`
- POT file: `languages/shopido-widgets-pack.pot`
- Localized JS strings via `wp_localize_script()`

---

## 🧰 Hooks

**Filters**
- `shopido_swiper_handle`
- `shopido_ajax_search_args`
- `shopido_ajax_search_result_item`
- `shopido_story_cpt_args`
- `shopido_assets_map`

**Actions**
- `shopido_before_enqueue_{handle}`
- `shopido_after_enqueue_{handle}`
- `shopido_story_metabox_render`
- `shopido_story_metabox_save`

---

## 🧾 Requirements

| Component | Minimum | Recommended |
|------------|----------|--------------|
| WordPress  | 5.8 | 6.3+ |
| PHP        | 7.4 | 8.1+ |
| Elementor  | 3.10 | Latest |
| WooCommerce | 6.0 | Latest |

---

## 🧹 Uninstall

`uninstall.php` safely removes meta fields `_story_media_id` and `_story_media_type` (optional).  
No posts are deleted by default.

---

## 🧭 Roadmap

- Story albums & analytics tracking  
- Grouped AJAX results (posts/products/categories)  
- Multi-row ticker layout  
- Countdown support for Counter

---

© 2025 **Shopido Elements** — Crafted with ❤️ for developers.
