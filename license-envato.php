<?php
/**
 * Plugin Name: License For Envato
 * Plugin URI: https://github.com/ashrafulsarkar/envato-licenser
 * Description: Manage your envato market items theme & plugin license.
 * Version: 1.1.0
 * Author: Ashraful Sarkar Naiem
 * Author URI: https://github.com/ashrafulsarkar
 * Requires at least: 6.0
 * Requires PHP:      7.2
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: license-envato
 * Domain Path: /languages/
 */

/**
 * Copyright (c) 2023 Ashraful Sarkar Naiem (email: ashrafulsarkar47@gmail.com). All rights reserved.
 *
 * Released under the GPL license
 * http://www.opensource.org/licenses/gpl-license.php
 *
 * This is an add-on for WordPress
 * http://wordpress.org/
 *
 * **********************************************************************
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * **********************************************************************
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

require_once __DIR__ . '/vendor/autoload.php';

// Early handler for deactivation requests before any output is generated
function license_envato_process_deactivation_early() {
    if (!is_admin()) {
        return;
    }
    
    if (isset($_REQUEST['page']) && $_REQUEST['page'] === 'licenseenvato' &&
        isset($_REQUEST['action']) && $_REQUEST['action'] === 'deactivate') {

        // Prevent any output before our redirect
        while (ob_get_level()) {
            ob_end_clean();
        }

        $token_value = isset( $_REQUEST['token'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['token'] ) ) : '';
        $nonce_value = isset( $_REQUEST['_wpnonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ) : '';

        if ( ! wp_verify_nonce( $nonce_value, 'license_envato_deactivate_action_' . $token_value ) ) {
            wp_die( esc_html__( 'Security check failed. Please try again.', 'license-envato' ) );
        }
        
        $code = [];
        $code['token'] = $token_value;
        
        // Need to manually include the API class if autoload hasn't run yet
        if (!class_exists('\LicenseEnvato\API\EnvatoLicenseApiCall')) {
            require_once __DIR__ . '/includes/API/EnvatoLicenseApiCall.php';
        }
        
        $EnvatoLicenseApiCall = new \LicenseEnvato\API\EnvatoLicenseApiCall;
        $licenseenvato_deactive = $EnvatoLicenseApiCall->envatolicense_deactive( $code );
        
        $redirect_url = add_query_arg(
            array(
                'page' => 'licenseenvato',
            ),
            admin_url('admin.php')
        );
        
        if ( is_wp_error( $licenseenvato_deactive ) ) {
            $error_message = $licenseenvato_deactive->get_error_message();
            $redirect_url = add_query_arg( 'error', urlencode(esc_html($error_message)), $redirect_url );
        } elseif ( isset($licenseenvato_deactive['deactive']) ) {
            $success_message = $licenseenvato_deactive['deactive'];
            $redirect_url = add_query_arg( 'success', urlencode(esc_html($success_message)), $redirect_url );
        } else {
            $redirect_url = add_query_arg( 'error', urlencode(esc_html__('Something wrong!', 'license-envato')), $redirect_url );
        }
        
        // Disable any error output to prevent "headers already sent"
        @error_reporting(0);
        @ini_set('display_errors', 0);
        
        // Force the redirect without using wp_redirect (which can check headers sent)
        header("Location: " . $redirect_url);
        exit;
    }
}
// Hook with very high priority (1) to run early
add_action('plugins_loaded', 'license_envato_process_deactivation_early', 1);

/**
 * The main plugin class
 */
final class License_Envato {

    /**
     * Plugin version
     *
     * @var string
     */
    const version = '1.0.0';

    /**
     * Class construcotr
     */
    private function __construct() {
        $this->define_constants();

        register_activation_hook( __FILE__, [$this, 'activate'] );

        add_action( 'plugins_loaded', [$this, 'init_plugin'] );
        add_action( 'plugins_loaded', [$this, 'load_textdomain'] );
    }

    /**
     * Initializes a singleton instance
     *
     * @return \License_Envato
     */
    public static function init() {
        /**
         * @var mixed
         */
        static $instance = false;

        if ( !$instance ) {
            $instance = new self();
        }

        return $instance;
    }

    public function load_textdomain(){
        load_plugin_textdomain('license-envato', false, dirname(__FILE__) . "/languages");
    }

    /**
     * Define the required plugin constants
     *
     * @return void
     */
    public function define_constants() {
        define( 'LICENSE_ENVATO_VERSION', self::version );
        define( 'LICENSE_ENVATO_FILE_URL', __FILE__ );
        define( 'LICENSE_ENVATO_FILE_PATH', __DIR__ );
        define( 'LICENSE_ENVATO_BASE_URL', plugin_basename( LICENSE_ENVATO_FILE_URL ) );
        define( 'LICENSE_ENVATO_URL', plugins_url( '', LICENSE_ENVATO_FILE_URL ) );
        define( 'LICENSE_ENVATO_ASSETS', LICENSE_ENVATO_URL . '/assets' );
    }

    /**
     * Initialize the plugin
     *
     * @return void
     */
    public function init_plugin() {

        new LicenseEnvato\Assets();

        if ( is_admin() ) {
            new LicenseEnvato\Admin();
        }

        new LicenseEnvato\API();
    }

    /**
     * Do stuff upon plugin activation
     *
     * @return void
     */
    public function activate() {
        $activation = new LicenseEnvato\Activation();
        $activation->run();
    }
}

/**
 * Initializes the main plugin
 *
 * @return \License_Envato
 */
function licence_envato() {
    return License_Envato::init();
}
licence_envato();