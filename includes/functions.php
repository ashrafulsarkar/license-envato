<?php

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