<?php
/**
 * Activation()
 * Activation class
 *
 * @author: Ashraful Sarkar Naiem
 * @since 1.0.0
 */

namespace LicenseEnvato;

class Activation {

    /**
     * Run the Activation
     *
     * @return void
     */
    public function run() {
        $this->add_version();
        $this->create_license_tables();
    }

    /**
     * Add time and version on DB
     */
    public function add_version() {
        $installed = get_option( 'license_envato_installed' );

        if ( !$installed ) {
            update_option( 'license_envato_installed', time() );
        }

        update_option( 'LICENSE_ENVATO_VERSION', LICENSE_ENVATO_VERSION );
    }

    /**
     * Create necessary database tables
     *
     * @return void
     */
    public function create_license_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $schema = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}license_envato_userlist` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `username` varchar(100) NOT NULL DEFAULT '',
          `itemid` varchar(30) NOT NULL DEFAULT '',
          `purchasecode` varchar(255) NOT NULL DEFAULT '',
          `token` varchar(255) NOT NULL DEFAULT '',
          `domain` varchar(255) NOT NULL DEFAULT '',
          `licensetype` varchar(255) NOT NULL DEFAULT '',
          `sold_at` varchar(255) NOT NULL DEFAULT '',
          `support_amount` varchar(255) NOT NULL DEFAULT '',
          `supported_until` varchar(255) NOT NULL DEFAULT '',
          PRIMARY KEY (`id`)
        ) $charset_collate";

        if ( !function_exists( 'dbDelta' ) ) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }

        dbDelta( $schema );
    }
}