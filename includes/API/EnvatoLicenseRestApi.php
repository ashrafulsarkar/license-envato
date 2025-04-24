<?php
/**
 * EnvatoLicenseRestApi()
 * EnvatoLicense Rest Api Call handler class
 *
 * @author: Ashraful Sarkar Naiem
 * @since 1.0.0
 */

namespace LicenseEnvato\API;

use WP_REST_Controller;
use WP_REST_Server;

class EnvatoLicenseRestApi extends WP_REST_Controller {

    /**
     * Initialize the class
     */
    public function __construct() {
        $this->namespace = 'licenseenvato/v1';
    }

    /**
     * Registers the routes for the objects of the controller.
     *
     * @return void
     */
    public function register_routes() {
        register_rest_route( $this->namespace, '/active',
            [
                [
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => [$this, 'active_license'],
                    'args'                => $this->get_active_collection_params(),
                    'permission_callback' => '__return_true',
                ],
            ]
        );

        register_rest_route( $this->namespace, '/deactive',
            [
                [
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => [$this, 'deactive_license'],
                    'args'                => $this->get_collection_params(),
                    'permission_callback' => '__return_true',
                ],
            ]
        );

    }

    /**
     * Retrieves a list of address items.
     *
     * @param  \WP_Rest_Request $request
     *
     * @return \WP_REST_Response
     */
    public function active_license( $request ) {
        $EnvatoLicenseApiCall = new EnvatoLicenseApiCall;
        $envatolicense_verify = $EnvatoLicenseApiCall->envatolicense_verify( $request );
        $response = rest_ensure_response( $envatolicense_verify );
        return $response;
    }

    /**
     * Retrieves a list of address items.
     *
     * @param  \WP_Rest_Request $request
     *
     * @return \WP_REST_Response
     */
    public function deactive_license( $request ) {
        $EnvatoLicenseApiCall = new EnvatoLicenseApiCall;
        $licenseenvato_deactive = $EnvatoLicenseApiCall->envatolicense_deactive( $request );
        $response = rest_ensure_response( $licenseenvato_deactive );
        return $response;
    }

    /**
     * Retrieves the query params for collections.
     *
     * @return array
     */
    public function get_active_collection_params() {

        return array(
            'context' => $this->get_context_param(),
            'code'    => array(
                'description'       => __( 'Envato purchase code.', 'license-envato' ),
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'validate_callback' => 'rest_validate_request_arg',
                'required'          => true,
            ),
            'domain'  => array(
                'description'       => __( 'API Request URL', 'license-envato' ),
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'validate_callback' => 'rest_validate_request_arg',
                'required'          => true,
            ),
            'itemid'  => array(
                'description'       => __( 'Envato Item Id', 'license-envato' ),
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'validate_callback' => 'rest_validate_request_arg',
                'required'          => true,
            ),
        );
    }

    /**
     * Retrieves the query params for collections.
     *
     * @return array
     */
    public function get_collection_params() {

        return array(
            'context' => $this->get_context_param(),
            'token'    => array(
                'description'       => __( 'Token', 'license-envato' ),
                'type'              => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'validate_callback' => 'rest_validate_request_arg',
                'required'          => true,
            ),
        );
    }

}