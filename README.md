# Aura Minimal

A minimal WordPress theme built with the **Aura WP Boilerplate**.

Aura WP is a lightweight, modular WordPress theme architecture designed for building custom themes with a consistent structure, reusable components, and a faster development workflow.

---

## Overview

Aura Minimal serves as a showcase implementation of the Aura WP Boilerplate, demonstrating the framework's architecture and development approach.

The boilerplate focuses on:

* Modular theme development
* Reusable theme components
* Centralized asset management
* Flexible template rendering
* JSON-based Customizer configuration
* Structured AJAX handling
* Administrative utilities
* Development logging support

---

## Features

### 🎨 JSON-Based Customizer Builder

Define WordPress Customizer controls using simple JSON configurations.

```php
"controls": {
  "control_site_icon": {
    "type": "image",
    "mime_type": "image",
    "label": "Icon",
    "section": "section_site_common",
    "settings": "site_icon"
  }
}
```

Benefits:

* Cleaner configuration
* Less repetitive code
* Easier maintenance
* Consistent Customizer implementation

---

### 📄 Centralized Template Rendering System

Render theme components through a unified API.

```php
theme_render_header();
theme_render_menu();
theme_render_template('pages/front-page');
theme_render_footer();
```

Alternative layouts can be loaded effortlessly:

```php
theme_render_header('maintenance');
theme_render_menu('minimal');
theme_render_template('pages/maintenance-page');
theme_render_footer();
```

Benefits:

* Consistent template structure
* Reusable layouts
* Cleaner theme organization
* Easier maintenance

---

### ⚡ Centralized AJAX Dispatcher

All AJAX requests are routed through a centralized dispatcher with built-in nonce support.

```php
switch ($action) {

    case 'verify_recaptcha':
        return theme_verify_recaptcha_ajax();
        break;

}
```

Benefits:

* Improved security
* Better code organization
* Easier debugging
* Simplified endpoint management

---

### 📦 Flexible Asset Management

Centralized loading of styles, scripts, packages, and fonts.

```php
theme_enqueue_css('/core/menu-common.css');

theme_enqueue_js('theme.js');

theme_enqueue_js_for('news', 'news_ajax.js');

theme_enqueue_package('daisy-ui');

theme_enqueue_google_font('Roboto', [400,500,700]);
```

Benefits:

* Reduced duplication
* Context-aware asset loading
* Better performance
* Easier dependency management

---

### ⚙️ Admin Dashboard Utilities

Register custom administration pages and manage plugin integrations from a centralized location.

```php
tgmpa($plugins);

theme_tgma_check_supported_plugins();

theme_notice_supported_plugins();

theme_register_admin_page('aura-logs', ...);

theme_enqueue_admin_css_for('aura-logs', 'admin.css');
```

Features include:

* Plugin recommendations
* Plugin compatibility checks
* Custom admin pages
* Admin asset management

---

### 📝 Development Logging

Built-in logging utilities help track issues and monitor theme behavior during development.

```php
log_error("Call to undefined function {$callback}");

log_warn("Template {$template_file} was not found");

log_info("Theme bootstrap initialization completed");
```

Benefits:

* Easier debugging
* Better development visibility
* Faster issue resolution

---

## Architecture Goals

Aura WP was created with the following principles:

* Modular design
* Reusability
* Developer experience
* Maintainability
* Consistent project structure
* Reduced boilerplate code
* Rapid theme development

---

## Use Cases

Aura WP is suitable for:

* Custom client WordPress themes
* Agency development workflows
* Reusable theme foundations
* Rapid project prototyping
* Multi-project theme ecosystems

---

## About This Project

**Aura Minimal** is a showcase theme built to demonstrate the capabilities and architecture of the Aura WP Boilerplate.

The goal is not to provide a feature-rich theme, but to illustrate a scalable and maintainable approach to modern WordPress theme development.

---

## License

MIT License
