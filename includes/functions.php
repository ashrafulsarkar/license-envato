<?php

/**
 * @return null
 */
if (!function_exists('licenseEnvato_general_setting_handler')) {
    function licenseEnvato_general_setting_handler() {
        if ( !isset( $_POST['submit_general'] ) ) {
            return;
        }
    
        if ( !wp_verify_nonce( $_POST['_wpnonce'], 'submit_general_setting' ) ) {
            wp_die( 'Are you cheating?' );
        }
    
        if ( !current_user_can( 'manage_options' ) ) {
            wp_die( 'Are you cheating?' );
        }
    
        $token_secret_key = isset( $_POST['token_secret'] ) ? sanitize_text_field( $_POST['token_secret'] ) : 'license-envato';
    
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