<?php

namespace EnvatoLicenser;

/**
 * API Class
 */
class API {

    /**
     * Initialize the class
     */
    function __construct() {
        add_action( 'rest_api_init', [ $this, 'register_api' ] );
    }

    /**
     * Register the API
     *
     * @return void
     */
    public function register_api() {
        $envatoLicense = new API\EnvatoLicenseRestApi();
        $envatoLicense->register_routes();
    }
}