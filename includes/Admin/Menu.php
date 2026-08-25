<?php
/**
 * Menu()
 * The Menu handler class
 * 
 * @author: Ashraful Sarkar Naiem
 * @since 1.0.0
 */

namespace LicenseEnvato\Admin;

class Menu {

    /**
     * Hook suffixes of this plugin's own admin pages, used to scope asset loading.
     *
     * @var string[]
     */
    private $page_hooks = [];

    /**
     * __construct()
     * Initialize the class
     *
     * @return void
     * @since 1.0.0
     */
    function __construct() {
        add_action( 'admin_menu', [ $this, 'admin_menu' ] );
    }

    /**
     * admin_menu()
     * Register admin menu
     *
     * @return void
     * @since 1.0.0
     */
    public function admin_menu() {
        $parent_slug = 'licenseenvato';
        $capability = 'manage_options';

        $this->page_hooks[] = add_menu_page( __( 'License Envato', 'license-envato' ), __( 'License Envato', 'license-envato' ), $capability, $parent_slug, [ $this, 'dashboard' ], 'dashicons-admin-network' );

        // Registered first, on the parent's own slug — WordPress auto-inserts
        // a duplicate "back to parent" link as the first submenu item unless
        // the very first add_submenu_page() call reuses the parent slug.
        $this->page_hooks[] = add_submenu_page( $parent_slug, __( 'Dashboard', 'license-envato' ), __( 'Dashboard', 'license-envato' ), $capability, $parent_slug, [ $this, 'dashboard' ] );

        $this->page_hooks[] = add_submenu_page( $parent_slug, __( 'Users', 'license-envato' ), __( 'Users', 'license-envato' ), $capability, $parent_slug.'-users', [ $this, 'allusers' ] );

        $this->page_hooks[] = add_submenu_page( $parent_slug, __( 'Settings', 'license-envato' ), __( 'Settings', 'license-envato' ), $capability, $parent_slug.'-settings', [ $this, 'settings' ] );

        // Upsell / license entry point, after Settings — hidden once the Pro
        // license is active. The slug is a direct URL, so the item is a plain
        // link to the Settings tab. The Pro plugin's submenu reorder keeps
        // unknown slugs at the end, so the position survives it.
        if ( ! license_envato_is_pro_active() ) {
            add_submenu_page( $parent_slug, __( 'Get Pro', 'license-envato' ), __( 'Get Pro', 'license-envato' ), $capability, 'admin.php?page=licenseenvato-settings&tab=get-pro' );
        } elseif ( ! license_envato_is_pro_license_active() ) {
            add_submenu_page( $parent_slug, __( 'Activate License', 'license-envato' ), __( 'Activate License', 'license-envato' ), $capability, 'admin.php?page=licenseenvato-settings&tab=prolicense' );
        }

        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_action( 'admin_init', [ $this, 'docs_redirect' ] );
    }

    /**
     * docs_redirect()
     * Redirect direct page visits to the external GitHub Pages documentation.
     * Runs on admin_init (before any output) so wp_safe_redirect works.
     *
     * @return void
     * @since 1.1.0
     */
    public function docs_redirect() {
        // Read-only: only compared against a literal to decide whether to redirect, no data is processed.
        if (
            isset( $_GET['page'] ) && // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            sanitize_key( wp_unslash( $_GET['page'] ) ) === 'licenseenvato-documentation' && // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            current_user_can( 'manage_options' )
        ) {
            wp_safe_redirect( 'https://ashrafulsarkar.github.io/license-envato/' );
            exit;
        }
    }

    /**
     * settings()
     * Handles the settings page
     * 
     * @return void
     * @since 1.0.0 
     */
    public function settings() {
        $settings = new Settings();
        $settings->plugin_page();
    }

    /**
     * allusers()
     * Handles the All User page
     *
     * @return void
     * @since 1.0.0
     */
    public function allusers() {
        $user = new Allusers();
        $user->plugin_page();
    }

    /**
     * dashboard()
     * Handles the Dashboard page
     *
     * @return void
     * @since 1.5.0
     */
    public function dashboard() {
        $dashboard = new Dashboard();
        $dashboard->plugin_page();
    }

    /**
     * enqueue_assets()
     * Enqueue scripts and styles, only on this plugin's own admin pages
     *
     * @param string $hook_suffix
     * @return void
     * @since 1.0.0
     */
    public function enqueue_assets( $hook_suffix ) {
        if ( ! in_array( $hook_suffix, $this->page_hooks, true ) ) {
            return;
        }

        wp_enqueue_style( 'licenseenvato-admin-style' );
    }
}