<?php

namespace EnvatoLicenser\Admin;
use EnvatoLicenser\Error\Form_Error;

class Settings {

    use Form_Error;

    /**
     * Plugin page handler
     *
     * @return void
     */
    public function plugin_page() {
        

        $settingsView = __DIR__ . '/views/settingsView.php';
        if ( file_exists( $settingsView ) ) {
            include $settingsView;
        }

    }

    /**
     * Handle the form
     *
     * @return void
     */
    // public function form_handler() {
    //     if ( ! isset( $_POST['submit_address'] ) ) {
    //         return;
    //     }

    //     if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'new-address' ) ) {
    //         wp_die( 'Are you cheating?' );
    //     }

    //     if ( ! current_user_can( 'manage_options' ) ) {
    //         wp_die( 'Are you cheating?' );
    //     }

    //     $id      = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : 0;
    //     $name    = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : '';
    //     $address = isset( $_POST['address'] ) ? sanitize_textarea_field( $_POST['address'] ) : '';
    //     $phone   = isset( $_POST['phone'] ) ? sanitize_text_field( $_POST['phone'] ) : '';

    //     if ( empty( $name ) ) {
    //         $this->errors['name'] = __( 'Please provide a name', 'wedevs-academy' );
    //     }

    //     if ( empty( $phone ) ) {
    //         $this->errors['phone'] = __( 'Please provide a phone number.', 'wedevs-academy' );
    //     }

    //     if ( ! empty( $this->errors ) ) {
    //         return;
    //     }

    //     $args = [
    //         'name'    => $name,
    //         'address' => $address,
    //         'phone'   => $phone
    //     ];

    //     if ( $id ) {
    //         $args['id'] = $id;
    //     }

    //     $insert_id = wd_ac_insert_address( $args );

    //     if ( is_wp_error( $insert_id ) ) {
    //         wp_die( $insert_id->get_error_message() );
    //     }

    //     if ( $id ) {
    //         $redirected_to = admin_url( 'admin.php?page=wedevs-academy&action=edit&address-updated=true&id=' . $id );
    //     } else {
    //         $redirected_to = admin_url( 'admin.php?page=wedevs-academy&inserted=true' );
    //     }

    //     wp_redirect( $redirected_to );
    //     exit;
    // }

    // public function delete_address() {
    //     if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'wd-ac-delete-address' ) ) {
    //         wp_die( 'Are you cheating?' );
    //     }

    //     if ( ! current_user_can( 'manage_options' ) ) {
    //         wp_die( 'Are you cheating?' );
    //     }

    //     $id = isset( $_REQUEST['id'] ) ? intval( $_REQUEST['id'] ) : 0;

    //     if ( wd_ac_delete_address( $id ) ) {
    //         $redirected_to = admin_url( 'admin.php?page=wedevs-academy&address-deleted=true' );
    //     } else {
    //         $redirected_to = admin_url( 'admin.php?page=wedevs-academy&address-deleted=false' );
    //     }

    //     wp_redirect( $redirected_to );
    //     exit;
    // }
}