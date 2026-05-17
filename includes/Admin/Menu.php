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

        add_menu_page( __( 'License Envato', 'license-envato' ), __( 'License Envato', 'license-envato' ), $capability, $parent_slug, [ $this, 'allusers' ], 'dashicons-admin-network' );

        add_submenu_page( $parent_slug, __( 'All Users', 'license-envato' ), __( 'All Users', 'license-envato' ), $capability, $parent_slug, [ $this, 'allusers' ] );

        add_submenu_page( $parent_slug, __( 'Settings', 'license-envato' ), __( 'Settings', 'license-envato' ), $capability, $parent_slug.'-settings', [ $this, 'settings' ] );
        add_submenu_page( $parent_slug, __( 'Documentation', 'license-envato' ), __( 'Documentation', 'license-envato' ), $capability, $parent_slug.'-documentation', '__return_null' );

        add_action( 'admin_init', [ $this, 'enqueue_assets' ] );
        add_action( 'admin_init', [ $this, 'docs_redirect' ] );
        add_action( 'admin_footer', [ $this, 'docs_menu_link' ] );
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
        if (
            isset( $_GET['page'] ) &&
            $_GET['page'] === 'licenseenvato-documentation' &&
            current_user_can( 'manage_options' )
        ) {
            wp_safe_redirect( 'https://ashrafulsarkar.github.io/license-envato/' );
            exit;
        }
    }

    /**
     * docs_menu_link()
     * Patch the Documentation sidebar link so it opens in a new tab.
     *
     * @return void
     * @since 1.1.0
     */
    public function docs_menu_link() {
        ?>
        <script>
        (function () {
            var link = document.querySelector('#adminmenu a[href*="licenseenvato-documentation"]');
            if (link) {
                link.href = 'https://ashrafulsarkar.github.io/license-envato/';
                link.target = '_blank';
                link.rel = 'noopener noreferrer';
            }
        }());
        </script>
        <?php
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
     * enqueue_assets()
     * Enqueue scripts and styles
     * 
     * @return void
     * @since 1.0.0 
     */
    public function enqueue_assets() {
        wp_enqueue_style( 'licenseenvato-admin-style' );
    }
}