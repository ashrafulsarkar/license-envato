<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * @return null
 */
if (!function_exists('licenseEnvato_general_setting_handler')) {
    function licenseEnvato_general_setting_handler() {
        if ( !isset( $_POST['submit_general'] ) ) {
            return;
        }
    
        if ( !isset( $_POST['_wpnonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'submit_general_setting' ) ) {
            wp_die( esc_html__( 'Security check failed. Please try again.', 'license-envato' ) );
        }

        if ( !current_user_can( 'manage_options' ) ) {
            wp_die( esc_html__( 'You do not have permission to perform this action.', 'license-envato' ) );
        }
    
        $token_secret_key = isset( $_POST['token_secret'] ) ? sanitize_text_field( wp_unslash( $_POST['token_secret'] ) ) : 'license-envato';
    
        update_option( 'license_envato_token_secret', $token_secret_key );
    }
}
/**
 * @param mixed $type
 * @param mixed $message
 * @return string
 */
if (!function_exists('licenseEnvato__redirect')) {
    function licenseEnvato__redirect($type, $message){
        $url = add_query_arg(
            array(
                'page' => 'licenseenvato',
                $type => $message
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