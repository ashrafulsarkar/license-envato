<?php
/**
 * API()
 * API Class
 *
 * @author: Ashraful Sarkar Naiem
 * @since 1.0.0
 */

namespace LicenseEnvato;

class API {

    /**
     * Initialize the class
     */
    public function __construct() {
        add_action( 'rest_api_init', [$this, 'register_api'] );
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