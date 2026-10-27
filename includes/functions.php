<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Store a one-time admin notice for the current user, shown on the next
 * page that renders notices. Messages travel server-side instead of in the
 * redirect URL, so a crafted link can't put arbitrary text in an admin notice.
 *
 * @param string $type    'success' or 'error'
 * @param string $message
 * @return void
 */
if ( ! function_exists( 'license_envato_set_notice' ) ) {
    function license_envato_set_notice( $type, $message ) {
        set_transient(
            'license_envato_notice_' . get_current_user_id(),
            array(
                'type'    => 'error' === $type ? 'error' : 'success',
                'message' => (string) $message,
            ),
            5 * MINUTE_IN_SECONDS
        );
    }
}

/**
 * Fetch and clear the current user's pending notice.
 *
 * @return array{type:string,message:string}|null
 */
if ( ! function_exists( 'license_envato_get_notice' ) ) {
    function license_envato_get_notice() {
        $key    = 'license_envato_notice_' . get_current_user_id();
        $notice = get_transient( $key );
        if ( ! is_array( $notice ) || empty( $notice['message'] ) ) {
            return null;
        }
        delete_transient( $key );
        return $notice;
    }
}

/**
 * @param mixed $type
 * @param mixed $message
 * @return string
 */
if (!function_exists('licenseEnvato__redirect')) {
    function licenseEnvato__redirect($type, $message){
        license_envato_set_notice( $type, $message );
        $url = add_query_arg(
            array(
                'page' => 'licenseenvato',
            ),
            admin_url('admin.php')
        );
        wp_safe_redirect(wp_sanitize_redirect($url));
        exit;
    }
}

/**
 * Whether the "License For Envato Pro" add-on plugin is active.
 *
 * @return bool
 */
if ( ! function_exists( 'license_envato_is_pro_active' ) ) {
    function license_envato_is_pro_active() {
        return class_exists( 'License_Envato_Pro', false );
    }
}

/**
 * Whether the Pro add-on is active AND its license is activated on this site.
 *
 * @return bool
 */
if ( ! function_exists( 'license_envato_is_pro_license_active' ) ) {
    function license_envato_is_pro_license_active() {
        return license_envato_is_pro_active()
            && class_exists( '\LicenseEnvatoPro\ProLicense' )
            && \LicenseEnvatoPro\ProLicense::is_active();
    }
}

/**
 * URL of the Pro purchase / upgrade page shown on the Free vs Pro tab.
 *
 * Can be overridden with the `license_envato_upgrade_url` filter.
 *
 * @return string
 */
if ( ! function_exists( 'license_envato_upgrade_url' ) ) {
    function license_envato_upgrade_url() {
        return apply_filters( 'license_envato_upgrade_url', 'https://codeholt.com/products/license-envato-pro/' );
    }
}

/**
 * License counts shared by the Dashboard and All Users stat cards.
 *
 * The free plugin only knows about its own primary activation slot and has
 * no concept of blacklisting — Pro extends both numbers via the
 * `license_envato_active_count` / `license_envato_blocked_count` filters
 * (see ActivationLimits::active_count() / Blacklist::blocked_count()), and
 * this degrades gracefully to free-only data when Pro isn't active.
 *
 * @return array{total:int,active:int,deactivated:int,blocked:int,active_pct:int}
 */
if ( ! function_exists( 'license_envato_get_stats' ) ) {
    function license_envato_get_stats() {
        global $wpdb;

        $total       = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}license_envato_userlist" );
        $active_base = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}license_envato_userlist WHERE `domain` <> ''" );

        /**
         * Filters the active-license count shown on the stat cards.
         *
         * @param int $count Active licenses counted from the free plugin's own table.
         */
        $active      = (int) apply_filters( 'license_envato_active_count', $active_base );
        $deactivated = max( 0, $total - $active );

        /**
         * Filters the blocked-count shown on the stat cards. Defaults to 0
         * until Pro hooks in — blacklisting is a Pro-only feature.
         *
         * @param int $count
         */
        $blocked = (int) apply_filters( 'license_envato_blocked_count', 0 );

        return array(
            'total'       => $total,
            'active'      => $active,
            'deactivated' => $deactivated,
            'blocked'     => $blocked,
            'active_pct'  => $total > 0 ? (int) round( ( $active / $total ) * 100 ) : 0,
        );
    }
}

/**
 * Encrypts a value for secure storage in wp_options.
 * Uses AES-256-CBC with a site-specific key derived from WordPress secret keys.
 *
 * @param string $value The plaintext value to encrypt.
 * @return string The base64-encoded encrypted value, or the original value if OpenSSL is unavailable.
 */
if ( ! function_exists( 'license_envato_encrypt_option' ) ) {
    function license_envato_encrypt_option( $value ) {
        if ( empty( $value ) || ! function_exists( 'openssl_encrypt' ) ) {
            return $value;
        }
        $key       = substr( hash( 'sha256', AUTH_KEY . SECURE_AUTH_KEY, true ), 0, 32 );
        $iv        = random_bytes( 16 );
        $encrypted = openssl_encrypt( $value, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv );
        if ( false === $encrypted ) {
            return $value;
        }
        return base64_encode( $iv . $encrypted );
    }
}

/**
 * Decrypts a value previously encrypted with license_envato_encrypt_option().
 * Falls back to returning the original value if decryption fails (legacy unencrypted data).
 *
 * @param string $value The encrypted (base64-encoded) value.
 * @return string The decrypted plaintext, or the original value as a fallback.
 */
if ( ! function_exists( 'license_envato_decrypt_option' ) ) {
    function license_envato_decrypt_option( $value ) {
        if ( empty( $value ) || ! function_exists( 'openssl_decrypt' ) ) {
            return $value;
        }
        $decoded = base64_decode( $value, true );
        // A valid encrypted value must contain at least a 16-byte IV plus one byte of data.
        if ( false === $decoded || strlen( $decoded ) < 17 ) {
            return $value; // Legacy unencrypted value — return as-is.
        }
        $key       = substr( hash( 'sha256', AUTH_KEY . SECURE_AUTH_KEY, true ), 0, 32 );
        $iv        = substr( $decoded, 0, 16 );
        $encrypted = substr( $decoded, 16 );
        $decrypted = openssl_decrypt( $encrypted, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv );
        if ( false === $decrypted ) {
            return $value; // Decryption failed — may be a legacy unencrypted value.
        }
        return $decrypted;
    }
}