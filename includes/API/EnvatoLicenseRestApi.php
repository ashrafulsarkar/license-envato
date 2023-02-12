<?php

namespace EnvatoLicenser\API;

use WP_REST_Controller;
use WP_REST_Server;
// use WP_Error;

/**
 * EnvatoLicenseRestApi Class
 */
class EnvatoLicenseRestApi extends WP_REST_Controller {

    /**
     * Initialize the class
     */
    function __construct() {
        $this->namespace = 'envatolicenser/v1';
        $this->rest_base = 'active';

        //envatoverify
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
                    'callback'            => [ $this, 'active_product' ],
                    'args'                => $this->get_collection_params(),
                ],
            ]
        );

    }

    /**
     * Retrieves a list of address items.
     *
     * @param  \WP_Rest_Request $request
     *
     * @return json
     */
    public function active_product( $request ) {
        $EnvatoLicenseApiCall = new EnvatoLicenseApiCall;
        $envatolicense_verify = $EnvatoLicenseApiCall->envatolicense_verify( $request );
        $response = rest_ensure_response( $envatolicense_verify );
        return $response;
    }

    /**
     * Retrieves the query params for collections.
     *
     * @return array
     */
    public function get_collection_params() {

        return array(
			'context'  => $this->get_context_param(),
			'code'     => array(
				'description'       => __( 'Envato purchase code.' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => 'rest_validate_request_arg',
                'required'          => true,
			)
		);
    }
}