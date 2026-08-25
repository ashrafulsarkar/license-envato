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

        // dbDelta is picky: no IF NOT EXISTS, no backticks, two spaces after PRIMARY KEY
        $schema = "CREATE TABLE {$wpdb->prefix}license_envato_userlist (
          id int(11) unsigned NOT NULL AUTO_INCREMENT,
          username varchar(100) NOT NULL DEFAULT '',
          itemid varchar(30) NOT NULL DEFAULT '',
          itemname varchar(255) NOT NULL DEFAULT '',
          purchasecode varchar(255) NOT NULL DEFAULT '',
          token varchar(255) NOT NULL DEFAULT '',
          domain varchar(255) NOT NULL DEFAULT '',
          licensetype varchar(255) NOT NULL DEFAULT '',
          sold_at varchar(255) NOT NULL DEFAULT '',
          support_amount varchar(255) NOT NULL DEFAULT '',
          supported_until varchar(255) NOT NULL DEFAULT '',
          PRIMARY KEY  (id)
        ) $charset_collate;";

        if ( !function_exists( 'dbDelta' ) ) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }

        dbDelta( $schema );
    }

    /**
     * Adds the `itemname` column for sites whose table was created before
     * this column existed. Runs on every `plugins_loaded` but the option
     * flag makes every call after the first a cheap `get_option()` — no
     * need for a plugin version bump to trigger it like the full dbDelta
     * pass in create_license_tables() does.
     *
     * @return void
     */
    public function maybe_add_itemname_column() {
        if ( get_option( 'license_envato_itemname_column_added' ) ) {
            return;
        }

        global $wpdb;
        $table = $wpdb->prefix . 'license_envato_userlist';

        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- one-time schema check on a custom table, no WP core API; the option flag above keeps this from running more than once.
        $exists = $wpdb->get_var( $wpdb->prepare(
            "SHOW COLUMNS FROM {$table} LIKE %s",
            'itemname'
        ) );

        if ( !$exists ) {
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange -- one-time additive column on a custom table, no WP core API for ALTER TABLE.
            $wpdb->query( "ALTER TABLE {$table} ADD COLUMN itemname varchar(255) NOT NULL DEFAULT '' AFTER itemid" );
        }

        update_option( 'license_envato_itemname_column_added', 1 );
    }
}