<?php
/**
 * Settings()
 * Settings handler class
 * 
 * @author: Ashraful Sarkar Naiem
 * @since 1.0.0
 */

namespace EnvatoLicenser\Admin;

use EnvatoLicenser\Error\Form_Error;
use EnvatoLicenser\API\EnvatoLicenseApiCall;

class Settings {

    use Form_Error;

    /**
     * plugin_page()
     * settingsView page handler
     * 
     * @return void
     * @since 1.0.0 
     */
    public function plugin_page() {
        $envato_licenser_api = new EnvatoLicenseApiCall();
        $settingsView = __DIR__ . '/views/settingsView.php';
        if ( file_exists( $settingsView ) ) {
            include $settingsView;
        }

    }

}