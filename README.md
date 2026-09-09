# License For Envato

<p align="center">
  <img src="https://img.shields.io/badge/version-1.4.0-green?style=flat-square" alt="Version">
  <img src="https://img.shields.io/badge/WordPress-%3E%3D6.0-blue?style=flat-square&logo=wordpress" alt="WordPress">
  <img src="https://img.shields.io/badge/PHP-%3E%3D7.2-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/license-GPLv2-orange?style=flat-square" alt="License">
  <a href="https://wordpress.org/plugins/license-envato/"><img src="https://img.shields.io/badge/WordPress.org-Plugin-21759B?style=flat-square&logo=wordpress" alt="WordPress.org"></a>
</p>

<p align="center">
  A WordPress plugin that acts as a <strong>license server</strong> for your Envato themes and plugins.<br>
  Validate purchase codes via the official Envato API and manage per-domain activations — all from your own WordPress site.
</p>

<p align="center">
  <a href="https://ashrafulsarkar.github.io/license-envato/">📖 Documentation</a> &nbsp;·&nbsp;
  <a href="https://wordpress.org/plugins/license-envato/">🔌 WordPress.org</a> &nbsp;·&nbsp;
  <a href="https://codeholt.com/products/license-envato-pro/">🚀 Get Pro</a> &nbsp;·&nbsp;
  <a href="https://github.com/ashrafulsarkar/license-envato/issues">🐛 Report a Bug</a>
</p>

---

## How It Works

```
Customer site  ──POST──►  Your WordPress site  ──►  Envato API
(client plugin)           (license-envato)           (purchase code validation)
```

1. **Server Setup** — Install this plugin on your WordPress site and add your Envato personal token.
2. **Client Integration** — Drop the provided PHP class into your theme or plugin and point it at your server URL.
3. **Display the Form** — Call `new licenseCodeVerifyForm();` on your settings page to render activate/deactivate controls.

---

## Features

- ✅ Validates purchase codes via the **official Envato API**
- ✅ Per-domain license activation and deactivation
- ✅ REST API endpoints ready for client integration
- ✅ Copy-paste PHP integration class included in the docs
- ✅ Admin panel to search and manage all verified licenses
- ✅ **AES-256-CBC** encrypted token storage
- ✅ Nonce-verified, capability-checked form submissions
- ✅ Lightweight — no external runtime dependencies
- ✅ Full [documentation](https://ashrafulsarkar.github.io/license-envato/) on GitHub Pages

---

## 🚀 Pro Features

[**License For Envato Pro**](https://codeholt.com/products/license-envato-pro/) is a paid add-on that builds on top of this free plugin:

- 🔒 Multiple domain activations per license, with configurable limits
- ⏳ Support-expiry enforcement, with on-demand renewal re-sync from Envato
- 📧 Email notifications + a daily expiry-warning digest
- 🔗 Webhooks with signed payloads for your own integrations
- 🚫 Purchase code & domain blacklist
- 📝 Activity log with CSV export
- 🎟️ Manual & bulk license issuance — no Envato purchase required
- 🔄 Automatic update delivery for your own themes/plugins (self-hosted)
- 📊 Analytics dashboard
- 🏆 Priority support

Compare Free vs Pro anytime from **wp-admin → License Envato → Settings → Get Pro**, or see [https://codeholt.com/products/license-envato-pro/](https://codeholt.com/products/license-envato-pro/).

---

## Requirements

| Requirement     | Minimum |
|-----------------|---------|
| WordPress       | 6.0     |
| PHP             | 7.2     |
| Envato Token    | Required |

---

## Installation

### From WordPress Admin

1. Go to **Plugins → Add New** and search for **License For Envato**.
2. Click **Install Now**, then **Activate**.

### Manual

1. Download or clone this repository.
2. Upload the `license-envato` folder to `/wp-content/plugins/`.
3. Activate **License For Envato** via the **Plugins** menu.

### After Activation

1. Navigate to **License Envato → Settings → Envato** tab.
2. Paste your [Envato personal token](https://build.envato.com/create-token/) and click **Save Envato Token**.
3. Follow the [integration guide](https://ashrafulsarkar.github.io/license-envato/) to add the client class to your product.

#### Required Token Permissions

| Permission |
|---|
| View and search Envato sites |
| View your Envato Account username |
| View your email address |
| Verify purchases of your items |
| List purchases you've made |

---

## REST API

The plugin exposes two public endpoints under the `licenseenvato/v1` namespace.

### Activate License

```
POST  https://YOUR_SITE_URL/wp-json/licenseenvato/v1/active
```

| Parameter | Type   | Required | Description                       |
|-----------|--------|----------|-----------------------------------|
| `code`    | string | ✔        | Envato purchase code              |
| `domain`  | string | ✔        | Customer's domain (URL-encoded)   |
| `itemid`  | string | ✔        | Envato item / product ID          |

**Response** — returns a `token` to store for future deactivation.

### Deactivate License

```
POST  https://YOUR_SITE_URL/wp-json/licenseenvato/v1/deactive
```

| Parameter | Type   | Required | Description                              |
|-----------|--------|----------|------------------------------------------|
| `token`   | string | ✔        | License token returned on activation     |

---

## Client Integration (Quick Start)

Copy this class into your theme's `functions.php` or a dedicated file in your plugin:

```php
class licenseCodeVerifyForm {

    const LICENCE_CALL_URL = "https://YOUR_SITE_URL"; // ← your server
    const PREFIX           = "YOUR_PREFIX";           // ← unique per product

    public function __construct() {
        $this->licenceActivate();
        $this->licenceDeactivate();
        $this->LicenceHTMLForm();
    }
    // ... (full class available in the documentation)
}
```

Then render the form anywhere on your settings page:

```php
<?php new licenseCodeVerifyForm(); ?>
```

📖 **Full class code:** [https://ashrafulsarkar.github.io/license-envato/](https://ashrafulsarkar.github.io/license-envato/)

---

## Changelog

### 1.4.0 — 09 September 2026
- New: Dashboard page — stat cards (Total/Active/Deactivated/Blocked licenses) plus the 5 most recent licenses with a quick-view details popup
- New: Item Name is now captured from Envato alongside Item ID and shown on the Dashboard and Users page
- Redesigned admin UI — shared design-system stylesheet applied across every plugin screen (cards, stat grid, badges, buttons, modals)
- Users page simplified to Username, Item Name, Item id, Status and View — purchase code, support-expiry and per-domain actions moved into the View details popup
- Primary buttons and the Free vs Pro "Upgrade to Pro" button now use the plugin's brand color
- Free vs Pro comparison table's Free/Pro column headers are now centered
- Review notice: dismissing (native close or "I already did") now snoozes for 7 days instead of dismissing forever — only "Ok, you deserve it!" stops it for good
- Fixed: dismissing/closing the review notice before actually leaving a review no longer silently disables future prompts

### 1.3.0 — 06 August 2026
- New: Free vs Pro comparison tab under Settings with an upgrade option
- New: Get Pro link on the Plugins page and in the side menu (hidden when the Pro add-on is active)
- New: dismissible review notice after 7 days of use — dismiss forever, snooze, or rate the plugin
- Admin user list can now show every activated domain of a license, each with its own Deactivate action
- Admin user list actions are now icons: green check for active domains, red cross to deactivate
- Admin Deactivate now runs on `admin_init`, after add-ons have registered their deactivation overrides
- Developer: new `license_envato_activate_override` and `license_envato_deactive_override` filters let add-ons extend license verification and deactivation
- Developer: new `license_envato_license_activated` and `license_envato_license_deactivated` action hooks fire on every activation/deactivation — the deactivated action receives the domain as a third parameter
- Developer: new `license_envato_userlist_items` filter lets add-ons merge extra domain activations into the admin user list
- Developer: new `license_envato_userlist_domain_actions` and `license_envato_userlist_row_actions` filters let add-ons add per-domain and per-license action icons
- License lookups now include `licensetype` and `supported_until`, enabling support-period aware add-ons

### 1.2.1 — 13 July 2026
- Fixed fatal error when the Envato API returns an empty response
- Fixed duplicate license rows and stale lookups on sites with persistent object caching (Redis/Memcached) — activation, deactivation and admin list caches are now invalidated correctly
- Fixed license reactivation not verifying the item ID
- Failed activations now return a proper error response instead of an empty success
- Admin license list now paginates in SQL — much faster with large license tables
- Database schema now upgrades automatically on plugin update — no re-activation needed
- Invalid purchase codes are cached for 10 minutes to protect your Envato API quota from repeated attempts
- Clearer, translatable security and error messages
- Added uninstall cleanup — plugin options and transients are removed on uninstall (license records are preserved for safety)

### 1.2.0 — 18 May 2026
- Plugin version now read dynamically from file header (single source of truth)
- Documentation moved to GitHub Pages
- Admin Documentation menu links directly to the GitHub Pages docs
- GitHub Star and WordPress.org links added to the docs page
- Security hardening: AES-256-CBC token encryption, `sslverify` enabled, improved nonce handling

### 1.1.0 — 11 May 2025
- Tested up to WordPress 6.8
- Local File Inclusion vulnerability fixed
- Multiple product verification issue fixed

### 1.0.0 — 04 March 2023
- Initial release

---

## Contributing

Pull requests are welcome. For major changes, please open an issue first to discuss what you'd like to change.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/my-feature`)
3. Commit your changes (`git commit -m 'Add my feature'`)
4. Push to the branch (`git push origin feature/my-feature`)
5. Open a Pull Request

---

## License

Distributed under the **GNU General Public License v2 or later**. See [LICENSE](http://www.gnu.org/licenses/gpl-2.0.html) for more information.

---

<p align="center">Made with ❤️ by <a href="https://github.com/ashrafulsarkar">Ashraful Sarkar Naiem</a></p>