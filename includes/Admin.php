<?php

namespace EnvatoLicenser;

/**
 * The admin class
 */
class Admin {

    /**
     * Initialize the class
     */
    function __construct() {
        // $addressbook = new Admin\Addressbook();

        // $this->dispatch_actions( $addressbook );

        new Admin\Menu();

        $this->custom_function();
        

    }

    public function custom_function(){
        add_filter( 'plugin_action_links_' . ENVATO_LICENSER_FILE_URL, [$this, 'plugin_menu_links'] );
    }
    
    public function plugin_menu_links ( $actions ) {
        $mylinks = array(
            '<a href="' . admin_url( 'admin.php?page=envatolicenser' ) . '">Settings</a>',
        );
        $actions = array_merge( $mylinks, $actions );
        return $actions;
    }

    /**
     * Dispatch and bind actions
     *
     * @return void
     */
    // public function dispatch_actions( $addressbook ) {
    //     add_action( 'admin_init', [ $addressbook, 'form_handler' ] );
    //     // add_action( 'admin_post_wd-ac-delete-address', [ $addressbook, 'delete_address' ] );
    // }
}