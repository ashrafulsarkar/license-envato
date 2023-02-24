<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e( 'Settings', 'licenseenvato' ); ?></h1>
    <?php $action = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general'; ?>
    <nav class="nav-tab-wrapper">
        <?php $envatoLicenser_nav = [ 
            'general' => __('General', 'licenseenvato'), 
            'envato' => __('Envato', 'licenseenvato'), 
            ];
        
            $envatoLicenser_nav_array =  apply_filters( 'envato_licenser_settings_nav', $envatoLicenser_nav );
            if ($envatoLicenser_nav_array) {
                $html = '';
                foreach ( $envatoLicenser_nav_array as $key => $val ) {
                    $class = ( $action == $key ) ? 'nav-tab-active' : '';
                    $link = admin_url( 'admin.php?page=envatolicenser-settings&tab=' . $key . '' );
                    $html .= '<a href="' . $link . '" class="nav-tab ' . $class . '">' . $val . '</a>';
                }
            }
            echo $html;
        ?>
    </nav>

    <?php
    $dir = __DIR__;
    $envatoLicenser_nav_view =  apply_filters( 'envato_licenser_settings_view', $dir, $action );

    if ($envatoLicenser_nav_view) {
        $template = "{$envatoLicenser_nav_view}/{$action}.php";
    }

    if ( file_exists( $template ) ) {
        include $template;
    }else{
        include "{$envatoLicenser_nav_view}/general.php";
    }
    ?>
</div>