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