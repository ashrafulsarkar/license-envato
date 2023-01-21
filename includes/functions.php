<?php

/**
 * envato_licenser_settings_nav()
 * 
 * @param array[key=>value] $nav
 * @return mixed
 */
function envato_licenser_settings_nav( array $nav ) {
    $action = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general';
    $html = '';
    foreach ( $nav as $key => $val ) {
        $class = ( $action == $key ) ? 'nav-tab-active' : '';
        $link = admin_url( 'admin.php?page=envatolicenser-settings&tab=' . $key . '' );
        $html .= '<a href="' . $link . '" class="nav-tab ' . $class . '">' . $val . '</a>';
    }
    return $html;
}

function envatolicense_verify( $args ){
    $envatocode = isset($args['code']) ? base64_decode($args['code']) : '';
    if ( empty($envatocode) ) {
        return 'false';
    }else{
        return $envatocode;
    }
        // print_r($args);
    return $args;
}








/**
 * Handle the form
 *
 * @return void
 */
// function form_handler() {
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

// function delete_address() {
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