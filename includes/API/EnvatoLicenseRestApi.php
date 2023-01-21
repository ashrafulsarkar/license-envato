<?php

namespace EnvatoLicenser\API;

use WP_REST_Controller;
use WP_REST_Server;
use WP_Error;

/**
 * EnvatoLicense Class
 */
class EnvatoLicense extends WP_REST_Controller {

    /**
     * Initialize the class
     */
    function __construct() {
        $this->namespace = 'envatolicenser/v1';
        $this->rest_base = 'license';

        //envatoverify
    }

    /**
     * Registers the routes for the objects of the controller.
     *
     * @return void
     */
    public function register_routes() {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            [
                [
                    'methods'             => WP_REST_Server::READABLE,
                    'callback'            => [ $this, 'get_items' ],
                    // 'permission_callback' => [ $this, 'get_items_permissions_check' ],
                    'args'                => $this->get_collection_params(),
                ],
                'schema' => [ $this, 'get_item_schema' ],
            ]
        );

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
     * Retrieves a list of address items.
     *
     * @param  \WP_Rest_Request $request
     *
     * @return \WP_Rest_Response|WP_Error
     */
    public function get_items( $request ) {
        $args = [];
        $params = $this->get_collection_params();

        foreach ( $params as $key => $value ) {
            if ( isset( $request[ $key ] ) ) {
                $args[ $key ] = $request[ $key ];
            }
        }


        $data     = [];
        $envatolicense_verify = envatolicense_verify( $args );

        // foreach ( $envatolicense_verify as $envatolicense_verify ) {
        //     $response = $this->prepare_item_for_response( $contact, $request );
        //     $data[]   = $this->prepare_response_for_collection( $response );
        // }

        

        $response = rest_ensure_response( $envatolicense_verify );
        // $response = rest_ensure_response( $request[ 'code' ] );

        // $response->header( 'X-WP-Total', (int) $total );
        // $response->header( 'X-WP-TotalPages', (int) $max_pages );

        return $envatolicense_verify;
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
			),
			'username' => array(
				'description'       => __( 'Maximum number of items to be returned in result set.' ),
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'validate_callback' => 'rest_validate_request_arg',
			),
			'time'   => array(
				'description'       => __( 'Limit results to those matching a string.' ),
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'validate_callback' => 'rest_validate_request_arg',
			),
		);
    }

    /**
     * Get the address, if the ID is valid.
     *
     * @param int $id Supplied ID.
     *
     * @return Object|\WP_Error
     */
    protected function get_contact( $id ) {
        // $contact = wd_ac_get_address( $id );

        // if ( ! $contact ) {
        //     return new WP_Error(
        //         'rest_contact_invalid_id',
        //         __( 'Invalid contact ID.' ),
        //         [ 'status' => 404 ]
        //     );
        // }

        // return $contact;
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

        $contact = $this->get_contact( $request['id'] );

        if ( is_wp_error( $contact ) ) {
            return $contact;
        }

        return true;
    }

    /**
     * Retrieves one item from the collection.
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_Error|\WP_REST_Response
     */
    public function get_item( $request ) {
        $contact = $this->get_contact( $request['id'] );

        $response = $this->prepare_item_for_response( $contact, $request );
        $response = rest_ensure_response( $response );

        return $response;
    }



    /**
     * Prepares one item for create or update operation.
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_Error|object
     */
    protected function prepare_item_for_database( $request ) {
        $prepared = [];

        if ( isset( $request['name'] ) ) {
            $prepared['name'] = $request['name'];
        }

        if ( isset( $request['address'] ) ) {
            $prepared['address'] = $request['address'];
        }

        if ( isset( $request['phone'] ) ) {
            $prepared['phone'] = $request['phone'];
        }

        return $prepared;
    }

    /**
     * Prepares the item for the REST response.
     *
     * @param mixed           $item    WordPress representation of the item.
     * @param \WP_REST_Request $request Request object.
     *
     * @return \WP_Error|WP_REST_Response
     */
    public function prepare_item_for_response( $item, $request ) {
        $data   = [];
        $fields = $this->get_fields_for_response( $request );

        if ( in_array( 'id', $fields, true ) ) {
            $data['id'] = (int) $item->id;
        }

        if ( in_array( 'name', $fields, true ) ) {
            $data['name'] = $item->name;
        }

        if ( in_array( 'address', $fields, true ) ) {
            $data['address'] = $item->address;
        }

        if ( in_array( 'phone', $fields, true ) ) {
            $data['phone'] = $item->phone;
        }

        if ( in_array( 'date', $fields, true ) ) {
            $data['date'] = mysql_to_rfc3339( $item->created_at );
        }

        $context = ! empty( $request['context'] ) ? $request['context'] : 'view';
        $data    = $this->filter_response_by_context( $data, $context );

        $response = rest_ensure_response( $data );
        $response->add_links( $this->prepare_links( $item ) );

        return $response;
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