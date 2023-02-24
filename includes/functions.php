<?php

/**
 * @return null
 */
function general_setting_handler() {
    if ( !isset( $_POST['submit_general'] ) ) {
        return;
    }

    if ( !wp_verify_nonce( $_POST['_wpnonce'], 'submit_general_setting' ) ) {
        wp_die( 'Are you cheating?' );
    }

    if ( !current_user_can( 'manage_options' ) ) {
        wp_die( 'Are you cheating?' );
    }

    $token_secret_key = isset( $_POST['token_secret'] ) ? sanitize_text_field( $_POST['token_secret'] ) : 'LicenseEnvato';

    update_option( 'license_envato_token_secret', $token_secret_key );
}