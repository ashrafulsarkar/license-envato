<?php
/**
 * Uninstall handler for License For Envato.
 *
 * Removes plugin options and transients. The {prefix}license_envato_userlist
 * table is intentionally preserved: it holds customers' license activation
 * records, and dropping it on an accidental uninstall would invalidate every
 * activated site with no way to recover. Reinstalling the plugin picks the
 * table back up as-is.
 *
 * @package LicenseEnvato
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

delete_option( 'license_envato_installed' );
delete_option( 'LICENSE_ENVATO_VERSION' );
delete_option( 'license_envato_token_valid' );
delete_option( 'license_envato_token_secret' );
delete_option( 'license_envato_review_dismissed' );
delete_option( 'license_envato_review_later' );

$license_envato_option_base = hash( 'crc32b', 'license_envato_envato' );
delete_option( $license_envato_option_base . '_token' );
delete_option( $license_envato_option_base . '_user' );

// Remove any negative-cache transients for invalid purchase codes.
global $wpdb;
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
$wpdb->query(
    "DELETE FROM {$wpdb->options}
     WHERE option_name LIKE '\_transient\_license\_envato\_bad\_%'
        OR option_name LIKE '\_transient\_timeout\_license\_envato\_bad\_%'"
);
