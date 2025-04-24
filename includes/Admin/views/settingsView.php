<?php
// Exit if accessed directly
defined('ABSPATH') || exit;

?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Settings', 'license-envato' ); ?></h1>
    <?php $action = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'general'; ?>
    <nav class="nav-tab-wrapper">
        <?php $licenseEnvato_nav = [ 
            'general' => esc_html__('General', 'license-envato'), 
            'envato' => esc_html__('Envato', 'license-envato'), 
            ];
        
            $licenseEnvato_nav_array =  apply_filters( 'license_envato_settings_nav', $licenseEnvato_nav );
            if ($licenseEnvato_nav_array) {
                $html = '';
                foreach ( $licenseEnvato_nav_array as $key => $val ) {
                    $class = ( $action == $key ) ? 'nav-tab-active' : '';
                    $link = admin_url( 'admin.php?page=licenseenvato-settings&tab=' . $key . '' );
                    $html .= '<a href="' . esc_url($link) . '" class="nav-tab ' . esc_attr($class) . '">' . esc_html($val) . '</a>';
                }
            }
            echo wp_kses_post($html);
        ?>
    </nav>

    <?php
    $dir = __DIR__;
    $licenseEnvato_nav_view =  apply_filters( 'license_envato_settings_view', $dir, $action );

    if ($licenseEnvato_nav_view) {
        $template = "{$licenseEnvato_nav_view}/{$action}.php";
    }

    if ( file_exists( $template ) ) {
        include $template;
    }else{
        include "{$licenseEnvato_nav_view}/general.php";
    }
    ?>
</div>