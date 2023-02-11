<?php

namespace EnvatoLicenser\API;

use WP_REST_Controller;
use WP_REST_Server;
use WP_Error;

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
                ],
            ]
        );

    }

    /**
     * Retrieves a list of address items.
     *
     * @param  \WP_Rest_Request $request
     *
     * @return \WP_Rest_Response|WP_Error
     */
    public function active_product( $request ) {
        $envatolicense_verify = EnvatoLicenseApiCall::envatolicense_verify( $request );
        $response = rest_ensure_response( $envatolicense_verify );

        // print_r($response);
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
				'description'       => __( 'Current page of the collection.' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => 'rest_validate_request_arg',
                'required'          => true,
			),
			'username' => array(
				'description'       => __( 'Maximum number of items to be returned in result set.' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => 'rest_validate_request_arg',
			)
		);
    }


    /**
     * Checks if a given request has access to get a specific item.
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_Error|bool
     */
    public function get_item_permissions_check( $request ) {
        if ( ! current_user_can( 'manage_options' ) ) {
            return false;
        }

        return true;
    }

    /**
     * Checks if a given request has access to read contacts.
     *
     * @param  \WP_REST_Request $request
     *
     * @return boolean
     */
    public function get_items_permissions_check( $request ) {
        if ( current_user_can( 'manage_options' ) ) {
            return true;
        }

        return false;
    }

    /**
     * Prepares links for the request.
     *
     * @param \WP_Post $post Post object.
     *
     * @return array Links for the given post.
     */
    protected function prepare_links( $item ) {
        $base = sprintf( '%s/%s', $this->namespace, $this->rest_base );

        $links = [
            'self' => [
                'href' => rest_url( trailingslashit( $base ) . $item->id ),
            ],
            'collection' => [
                'href' => rest_url( $base ),
            ],
        ];

        return $links;
    }

    /**
     * Retrieves the contact schema, conforming to JSON Schema.
     *
     * @return array
     */
    public function get_item_schema() {
        if ( $this->schema ) {
            return $this->add_additional_fields_schema( $this->schema );
        }

        $schema = [
            '$schema'    => 'http://json-schema.org/draft-04/schema#',
            'title'      => 'contact',
            'type'       => 'object',
            'properties' => [
                'id' => [
                    'description' => __( 'Unique identifier for the object.' ),
                    'type'        => 'integer',
                    'context'     => [ 'view', 'edit' ],
                    'readonly'    => true,
                ],
                'name' => [
                    'description' => __( 'Name of the contact.' ),
                    'type'        => 'string',
                    'context'     => [ 'view', 'edit' ],
                    'required'    => true,
                    'arg_options' => [
                        'sanitize_callback' => 'sanitize_text_field',
                    ],
                ],
                'address' => [
                    'description' => __( 'Address of the contact.' ),
                    'type'        => 'string',
                    'context'     => [ 'view', 'edit' ],
                    'arg_options' => [
                        'sanitize_callback' => 'sanitize_textarea_field',
                    ],
                ],
                'phone' => [
                    'description' => __( 'Phone number of the contact.' ),
                    'type'        => 'string',
                    'required'    => true,
                    'context'     => [ 'view', 'edit' ],
                    'arg_options' => [
                        'sanitize_callback' => 'sanitize_text_field',
                    ],
                ],
                'date' => [
                    'description' => __( "The date the object was published, in the site's timezone." ),
                    'type'        => 'string',
                    'format'      => 'date-time',
                    'context'     => [ 'view' ],
                    'readonly'    => true,
                ],
            ]
        ];

        $this->schema = $schema;

        return $this->add_additional_fields_schema( $this->schema );
    }


}