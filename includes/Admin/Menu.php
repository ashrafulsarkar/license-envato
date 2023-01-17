<?php

namespace EnvatoLicenser\Admin;

/**
 * The Menu handler class
 */
class Menu {

    /**
     * Initialize the class
     */
    function __construct() {
        add_action( 'admin_menu', [ $this, 'admin_menu' ] );
    }

    /**
     * Register admin menu
     *
     * @return void
     */
    public function admin_menu() {
        $parent_slug = 'envatolicenser';
        $capability = 'manage_options';

        add_menu_page( __( 'Envato Licenser', 'envatolicenser' ), __( 'Envato Licenser', 'envatolicenser' ), $capability, $parent_slug, [ $this, 'plugin_page' ], 'dashicons-admin-network' );

        add_submenu_page( $parent_slug, __( 'Licenser', 'envatolicenser' ), __( 'Licenser', 'envatolicenser' ), $capability, $parent_slug, [ $this, 'plugin_page' ] );

        add_submenu_page( $parent_slug, __( 'Settings', 'envatolicenser' ), __( 'Settings', 'envatolicenser' ), $capability, $parent_slug.'-settings', [ $this, 'settings' ] );

        add_action( 'admin_init', [ $this, 'enqueue_assets' ] );
    }

    /**
     * Handles the settings page
     *
     * @return void
     */
    public function settings() {
        $settings = new Settings();
        $settings-> plugin_page();
    }

    /**
     * Handles the settings page
     *
     * @return void
     */
    public function plugin_page() {
        echo 'Plugin Page';
    }

    /**
     * Enqueue scripts and styles
     *
     * @return void
     */
    public function enqueue_assets() {
        wp_enqueue_style( 'envatolicenser-admin-style' );
        // wp_enqueue_script( 'academy-admin-script' );
    }
}