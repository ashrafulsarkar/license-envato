=== License For Envato ===
Contributors: ashrafulsarkar
Tags: license, license manager, envato license, plugin license, license envato
Donate link: https://www.buymeacoffee.com/ashrafulsarkar
Requires at least: 6.0
Tested up to: 7.0
Stable tag: 1.3.0
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

License For Envato is an Envato theme & plugin license management solution for WordPress.

== Description ==

Are you a theme or plugin developer selling your products on the Envato market? Struggling to manage purchase-code licenses across multiple customer domains? **License For Envato** is the solution.

Install this plugin on your WordPress site to turn it into a fully-featured license server. It validates Envato purchase codes through the official Envato API and stores per-domain activation records in your database. Your customers activate their copy directly from your theme or plugin settings page — no third-party service required.

Need activation limits, automatic updates, webhooks, blacklisting or an analytics dashboard? Check out **[License For Envato Pro](https://codeholt.com/products/license-envato-pro/)**.

= 🔑 How It Works =

1. **Server Setup** — Install the plugin on your WordPress site and connect your Envato personal token.
2. **Client Integration** — Copy the provided PHP class into your theme or plugin and point it at your server URL.
3. **Display the Form** — Instantiate the class on your settings page to render an activate/deactivate form for your customers.

= ⚡ Free Features =

* Easy one-time server setup
* Validates purchase codes via the official Envato API
* Per-domain license activation and deactivation
* REST API endpoints for client integration (`/wp-json/licenseenvato/v1/active`, `/wp-json/licenseenvato/v1/deactive`)
* Copy-paste PHP integration class included
* Search and manage verified licenses from the admin panel
* Secure token-based activation flow (AES-256-CBC encrypted storage)
* Lightweight and fast — no external dependencies at runtime

= 🚀 Pro Features =

Need more than the basics? [License For Envato Pro](https://codeholt.com/products/license-envato-pro/) adds:

* Multiple domain activations per license, with configurable limits
* Support-expiry enforcement, with on-demand renewal re-sync from Envato
* Email notifications + a daily expiry-warning digest
* Webhooks with signed payloads for your own integrations
* Purchase code & domain blacklist
* Activity log with CSV export
* Manual & bulk license issuance — no Envato purchase required
* Automatic update delivery for your own themes/plugins (self-hosted, outside WordPress.org)
* Analytics dashboard
* Priority support

Get it at [https://codeholt.com/products/license-envato-pro/](https://codeholt.com/products/license-envato-pro/)

= 📖 Documentation =

Full documentation is available at [https://ashrafulsarkar.github.io/license-envato/](https://ashrafulsarkar.github.io/license-envato/)

= 🐙 GitHub =

[https://github.com/ashrafulsarkar/license-envato](https://github.com/ashrafulsarkar/license-envato)


== Installation ==

1. Deactivate the plugin if you have a previous version installed.
2. Upload the `license-envato` folder to the `/wp-content/plugins/` directory, or install it directly through the WordPress Plugins screen.
3. Activate **License For Envato** via the **Plugins** menu in WordPress admin.
4. Navigate to **License Envato → Settings → Envato** tab and paste your Envato personal token, then click **Save Envato Token**.
5. Follow the [documentation](https://ashrafulsarkar.github.io/license-envato/) to integrate the client PHP class into your theme or plugin.

= Required Envato Token Permissions =

* View and search Envato sites
* View your Envato Account username
* View your email address
* Verify purchases of your items
* List purchases you've made


== Frequently Asked Questions ==

= How do I set up this plugin? =
Go to **wp-admin → License Envato → Settings**, enter your Envato personal token under the **Envato** tab, and click **Save Envato Token**.

= Where is the full documentation? =
Full documentation with step-by-step integration instructions is available at [https://ashrafulsarkar.github.io/license-envato/](https://ashrafulsarkar.github.io/license-envato/).

= How does the client integration work? =
Copy the PHP class from the documentation into your theme's `functions.php` or a dedicated file in your plugin. Set `LICENCE_CALL_URL` to your WordPress site URL and `PREFIX` to a unique prefix for your product. Then call `new licenseCodeVerifyForm();` on your settings page.

= Can I use this for multiple products? =
Yes. Use a unique `PREFIX` constant per product to keep option keys separate in the WordPress database.

= Where can I report bugs or contribute? =
Bugs can be reported on the [GitHub repository](https://github.com/ashrafulsarkar/license-envato). Pull requests are welcome.

= Is there a Pro version? =
Yes. [License For Envato Pro](https://codeholt.com/products/license-envato-pro/) adds activation limits, support-expiry enforcement, email notifications, webhooks, a blacklist, activity logs, manual/bulk issuance, automatic update delivery and an analytics dashboard on top of this free plugin. You can also compare Free vs Pro from **wp-admin → License Envato → Settings → Get Pro**.


== Screenshots ==

1. Connect your Envato personal token under Settings - Envato tab.
2. Account details displayed after a successful token connection.
3. Verified license list with domain and purchase-code records.
4. REST API activation and deactivation in action from a client theme/plugin.


== Changelog ==

= 1.3.0 - 07-08-2026 =
* New: Free vs Pro comparison tab under Settings with an upgrade option
* New: Get Pro link on the Plugins page and in the side menu (hidden when the Pro add-on is active)
* New: dismissible review notice after 7 days of use — dismiss forever, snooze, or rate the plugin
* Admin user list can now show every activated domain of a license, each with its own Deactivate action
* Admin user list actions are now icons: green check for active domains, red cross to deactivate
* Admin Deactivate now runs on `admin_init`, after add-ons have registered their deactivation overrides
* Developer: new `license_envato_activate_override` and `license_envato_deactive_override` filters let add-ons extend license verification and deactivation
* Developer: new `license_envato_license_activated` and `license_envato_license_deactivated` action hooks fire on every activation/deactivation — the deactivated action receives the domain as a third parameter
* Developer: new `license_envato_userlist_items` filter lets add-ons merge extra domain activations into the admin user list
* Developer: new `license_envato_userlist_domain_actions` and `license_envato_userlist_row_actions` filters let add-ons add per-domain and per-license action icons
* License lookups now include `licensetype` and `supported_until`, enabling support-period aware add-ons

= 1.2.1 - 13-07-2026 =
* Fixed fatal error when the Envato API returns an empty response
* Fixed duplicate license rows and stale lookups on sites with persistent object caching (Redis/Memcached) — activation, deactivation and admin list caches are now invalidated correctly
* Fixed license reactivation not verifying the item ID
* Failed activations now return a proper error response instead of an empty success
* Admin license list now paginates in SQL — much faster with large license tables
* Database schema now upgrades automatically on plugin update — no re-activation needed
* Invalid purchase codes are cached for 10 minutes to protect your Envato API quota from repeated attempts
* Clearer, translatable security and error messages
* Added uninstall cleanup — plugin options and transients are removed on uninstall (license records are preserved for safety)

= 1.2.0 - 18-05-2026 =
* Plugin version now read dynamically from file header — single source of truth
* Documentation moved to GitHub Pages (https://ashrafulsarkar.github.io/license-envato/)
* Admin Documentation menu now links directly to the GitHub Pages docs
* Security hardening: AES-256-CBC token encryption, sslverify enabled, improved nonce handling

= 1.1.0 - 11-05-2025 =
* Tested up to WordPress 6.8
* Local File Inclusion vulnerability fixed
* Multiple product verification issue fixed

= 1.0.0 - 04-03-2023 =
* Initial release
