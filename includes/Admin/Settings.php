<?php

namespace EnvatoLicenser\Admin;
use EnvatoLicenser\Error\Form_Error;
use EnvatoLicenser\API\EnvatoLicenseApiCall;

class Settings {

    use Form_Error;

    /**
     * Plugin page handler
     *
     * @return void
     */
    public function plugin_page() {
        $envato_licenser_api = new EnvatoLicenseApiCall();
        $settingsView = __DIR__ . '/views/settingsView.php';
        if ( file_exists( $settingsView ) ) {
            include $settingsView;
        }

    }

}