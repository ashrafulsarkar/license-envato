<?php
/**
 * Plugin Name: License For Envato
 * Plugin URI: https://github.com/ashrafulsarkar/envato-licenser
 * Description: Manage your envato market items theme & plugin license.
 * Version: 1.0.0
 * Author: Ashraful Sarkar
 * Author URI: https://github.com/ashrafulsarkar
 * Requires at least: 6.0
 * Requires PHP:      7.2
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: envatolicenser
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

/**
 * The main plugin class
 */
final class Envato_Licenser {

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
     * @return \Envato_Licenser
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
        load_plugin_textdomain("envatolicenser", false, dirname(__FILE__) . "/languages");
    }

    /**
     * Define the required plugin constants
     *
     * @return void
     */
    public function define_constants() {
        define( 'ENVATO_LICENSER_VERSION', self::version );
        define( 'ENVATO_LICENSER_FILE_URL', __FILE__ );
        define( 'ENVATO_LICENSER_FILE_PATH', __DIR__ );
        define( 'ENVATO_LICENSER_BASE_URL', plugin_basename( ENVATO_LICENSER_FILE_URL ) );
        define( 'ENVATO_LICENSER_URL', plugins_url( '', ENVATO_LICENSER_FILE_URL ) );
        define( 'ENVATO_LICENSER_ASSETS', ENVATO_LICENSER_URL . '/assets' );
    }

    /**
     * Initialize the plugin
     *
     * @return void
     */
    public function init_plugin() {

        new EnvatoLicenser\Assets();

        if ( is_admin() ) {
            new EnvatoLicenser\Admin();
        }

        new EnvatoLicenser\API();
    }

    /**
     * Do stuff upon plugin activation
     *
     * @return void
     */
    public function activate() {
        $activation = new EnvatoLicenser\Activation();
        $activation->run();
    }
}

/**
 * Initializes the main plugin
 *
 * @return \Envato_Licenser
 */
function envato_licencer() {
    return Envato_Licenser::init();
}
envato_licencer();