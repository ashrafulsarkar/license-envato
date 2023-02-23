<?php
/**
 * Admin()
 * The admin class
 *
 * @author: Ashraful Sarkar Naiem
 * @since 1.0.0
 */

namespace EnvatoLicenser;

class Admin {

    /**
     * Initialize the class
     */
    public function __construct() {

        new Admin\Menu();

        $this->custom_function();

    }

    public function custom_function() {
        add_filter( 'plugin_action_links_' . ENVATO_LICENSER_BASE_URL, [$this, 'plugin_menu_links'] );
    }

    /**
     * @param $actions
     * @return mixed
     */
    public function plugin_menu_links( $actions ) {
        $mylinks = array(
            '<a href="' . admin_url( 'admin.php?page=envatolicenser-settings' ) . '">Settings</a>',
        );
        $actions = array_merge( $mylinks, $actions );
        return $actions;
    }
}