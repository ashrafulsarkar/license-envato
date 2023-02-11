<?php
namespace EnvatoLicenser;

/**
 * Activation class
 */
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
        $installed = get_option( 'envato_licenser_installed' );

        if ( ! $installed ) {
            update_option( 'envato_licenser_installed', time() );
        }

        update_option( 'envato_licenser_version', ENVATO_LICENSER_VERSION );
    }

    /**
     * Create necessary database tables
     *
     * @return void
     */
    public function create_license_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $schema = "CREATE TABLE IF NOT EXISTS `{$wpdb->prefix}envato_user_list` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `username` varchar(100) NOT NULL DEFAULT '',
          `itemid` varchar(30) NOT NULL DEFAULT '',
          `marketname` varchar(100) NOT NULL DEFAULT '',
          `parchasecode` varchar(255) NOT NULL DEFAULT '',
          `token` varchar(255) NOT NULL DEFAULT '',
          `activation` int(11) NOT NULL DEFAULT '',
          PRIMARY KEY (`id`)
        ) $charset_collate";

        if ( ! function_exists( 'dbDelta' ) ) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }

        dbDelta( $schema );
    }
}