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
                    'permission_callback' => [$this, 'throttle_active'],
                ],
            ]
        );

        register_rest_route( $this->namespace, '/deactive',
            [
                [
                    'methods'             => WP_REST_Server::CREATABLE,
                    'callback'            => [$this, 'deactive_license'],
                    'args'                => $this->get_collection_params(),
                    'permission_callback' => [$this, 'throttle_deactive'],
                ],
            ]
        );

    }

    /**
     * Rate limit for the public /active endpoint.
     *
     * @return true|\WP_Error
     */
    public function throttle_active() {
        return $this->throttle( 'active', 20 );
    }

    /**
     * Rate limit for the public /deactive endpoint.
     *
     * @return true|\WP_Error
     */
    public function throttle_deactive() {
        return $this->throttle( 'deactive', 30 );
    }

    /**
     * Fixed-window, per-IP request limiter. Public endpoints can otherwise be
     * used to brute-force purchase codes/tokens and burn the Envato API quota.
     *
     * @param string $endpoint Endpoint key.
     * @param int    $limit    Default max requests per window.
     *
     * @return true|\WP_Error
     */
    private function throttle( $endpoint, $limit ) {
        /**
         * Filters the max requests per window for a public endpoint. Return 0 to disable.
         *
         * @param int    $limit    Max requests.
         * @param string $endpoint 'active' or 'deactive'.
         */
        $limit  = (int) apply_filters( 'license_envato_rate_limit', $limit, $endpoint );
        $window = (int) apply_filters( 'license_envato_rate_limit_window', 10 * MINUTE_IN_SECONDS, $endpoint );

        if ( $limit <= 0 ) {
            return true;
        }

        // REMOTE_ADDR only: forwarded headers are client-spoofable. Sites behind a proxy can use the filter.
        $ip  = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
        $ip  = apply_filters( 'license_envato_rate_limit_ip', $ip, $endpoint );
        $key = 'le_rl_' . md5( $endpoint . '|' . $ip );

        $now  = time();
        $data = get_transient( $key );
        if ( ! is_array( $data ) || $data['reset'] <= $now ) {
            $data = [ 'count' => 0, 'reset' => $now + $window ];
        }

        $data['count']++;
        set_transient( $key, $data, max( 1, $data['reset'] - $now ) );

        if ( $data['count'] > $limit ) {
            $retry = max( 1, $data['reset'] - $now );
            return new \WP_Error(
                'rate_limited',
                __( 'Too many requests. Please try again later.', 'license-envato' ),
                [ 'status' => 429, 'headers' => [ 'Retry-After' => $retry ] ]
            );
        }

        return true;
    }

    /**
     * Validates the client-supplied domain. Value is not rewritten (stored
     * activations are compared verbatim), only rejected when it is not a
     * plausible host, optionally prefixed by a scheme and followed by port/path.
     *
     * @param mixed $value Raw param value.
     *
     * @return true|\WP_Error
     */
    public function validate_domain( $value ) {
        $error = new \WP_Error( 'invalid_domain', __( 'Invalid domain.', 'license-envato' ), [ 'status' => 400 ] );

        if ( ! is_string( $value ) ) {
            return $error;
        }

        $value = trim( $value );
        if ( $value === '' || strlen( $value ) > 255 || preg_match( '/[\x00-\x20\x7f\\\\]/', $value ) ) {
            return $error;
        }

        $rest = preg_replace( '#^[a-z][a-z0-9+.-]*://#i', '', $value );
        $host = preg_split( '#[/?\#]#', $rest, 2 )[0];
        if ( strpos( $host, '@' ) !== false ) { // userinfo is never a legit client domain.
            return $error;
        }

        // [ipv6]:port
        if ( preg_match( '/^\[([0-9a-f:.]+)\](?::\d{1,5})?$/i', $host, $m ) ) {
            return filter_var( $m[1], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ? true : $error;
        }

        $host = preg_replace( '/:\d{1,5}$/', '', $host ); // drop port.
        if ( $host === '' || strlen( $host ) > 253 ) {
            return $error;
        }

        if ( filter_var( $host, FILTER_VALIDATE_IP ) ) {
            return true;
        }

        // Hostname labels (also covers localhost, single-label dev hosts, IDN punycode).
        if ( preg_match( '/^(?=.{1,253}$)([a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?)(\.[a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?)*\.?$/i', $host ) ) {
            return true;
        }

        return $error;
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
                'validate_callback' => [$this, 'validate_domain'],
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