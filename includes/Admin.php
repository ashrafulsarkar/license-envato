<?php
/**
 * Admin()
 * The admin class
 *
 * @author: Ashraful Sarkar Naiem
 * @since 1.0.0
 */

namespace LicenseEnvato;

class Admin {

    /**
     * Initialize the class
     */
    public function __construct() {

        new Admin\Menu();
        new Admin\ReviewNotice();

        $this->custom_function();

    }

    public function custom_function() {
        add_filter( 'plugin_action_links_' . LICENSE_ENVATO_BASE_URL, [$this, 'plugin_menu_links'] );
    }

    /**
     * @param $actions
     * @return mixed
     */
    public function plugin_menu_links( $actions ) {
        $mylinks = array();

        // Get Pro | Settings | Deactivate — the Activate License link lives on
        // the Pro plugin's own row, not here.
        if ( ! license_envato_is_pro_active() ) {
            $mylinks[] = '<a href="' . esc_url( admin_url( 'admin.php?page=licenseenvato-settings&tab=get-pro' ) ) . '" class="license-envato-getpro-link" style="color:#d63638;font-weight:700;">' . esc_html__( 'Get Pro', 'license-envato' ) . '</a>';
        }

        $mylinks[] = '<a href="' . esc_url( admin_url( 'admin.php?page=licenseenvato-settings' ) ) . '">' . esc_html__( 'Settings', 'license-envato' ) . '</a>';

        $actions = array_merge( $mylinks, $actions );
        return $actions;
    }
}