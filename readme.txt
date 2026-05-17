=== License For Envato ===
Contributors: ashrafulsarkar
Tags: license, license manager, envato license, plugin license, license envato
Donate link: https://www.buymeacoffee.com/ashrafulsarkar
Requires at least: 6.0
Tested up to: 6.9
Stable tag: 1.2.0
Requires PHP: 7.2
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

License For Envato is an Envato theme & plugin license management solution for WordPress.

== Description ==

Are you a theme or plugin developer selling your products on the Envato market? Struggling to manage purchase-code licenses across multiple customer domains? **License For Envato** is the solution.

Install this plugin on your WordPress site to turn it into a fully-featured license server. It validates Envato purchase codes through the official Envato API and stores per-domain activation records in your database. Your customers activate their copy directly from your theme or plugin settings page — no third-party service required.

= 🔑 How It Works =

1. **Server Setup** — Install the plugin on your WordPress site and connect your Envato personal token.
2. **Client Integration** — Copy the provided PHP class into your theme or plugin and point it at your server URL.
3. **Display the Form** — Instantiate the class on your settings page to render an activate/deactivate form for your customers.

= ⚡ Features =

* Easy one-time server setup
* Validates purchase codes via the official Envato API
* Per-domain license activation and deactivation
* REST API endpoints for client integration (`/wp-json/licenseenvato/v1/active`, `/wp-json/licenseenvato/v1/deactive`)
* Copy-paste PHP integration class included
* Search and manage verified licenses from the admin panel
* Secure token-based activation flow (AES-256-CBC encrypted storage)
* Lightweight and fast — no external dependencies at runtime

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


== Screenshots ==

1. Connect your Envato personal token under Settings → Envato tab.
2. Account details displayed after a successful token connection.
3. Verified license list with domain and purchase-code records.
4. REST API activation and deactivation in action from a client theme/plugin.


== Changelog ==

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
