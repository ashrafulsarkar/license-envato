<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e( 'Settings', 'licenseenvato' ); ?></h1>
    <?php $action = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general'; ?>
    <nav class="nav-tab-wrapper">
        <?php $licenseEnvato_nav = [ 
            'general' => __('General', 'licenseenvato'), 
            'envato' => __('Envato', 'licenseenvato'), 
            ];
        
            $licenseEnvato_nav_array =  apply_filters( 'license_envato_settings_nav', $licenseEnvato_nav );
            if ($licenseEnvato_nav_array) {
                $html = '';
                foreach ( $licenseEnvato_nav_array as $key => $val ) {
                    $class = ( $action == $key ) ? 'nav-tab-active' : '';
                    $link = admin_url( 'admin.php?page=licenseenvato-settings&tab=' . $key . '' );
                    $html .= '<a href="' . $link . '" class="nav-tab ' . $class . '">' . $val . '</a>';
                }
            }
            echo $html;
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