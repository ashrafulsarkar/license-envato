<div class="wrap">
    <h1><?php _e( 'Settings', 'envatolicenser' ); ?></h1>

    <nav class="nav-tab-wrapper">
        <?php $envatoLicenser_nav = [ 
            'general' => __('General', 'envatolicenser'), 
            'envato' => __('Envato', 'envatolicenser'), 
            ];
        
            $envatoLicenser_nav_array =  apply_filters( 'envato_licenser_settings_nav', $envatoLicenser_nav );
            echo envato_licenser_settings_nav($envatoLicenser_nav_array);
        ?>
    </nav>

    <?php
    $action = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general';
    switch ( $action ) {

        case 'envato':
            $template = __DIR__ . '/envatoView.php';       
            break;

        default:
            $template = __DIR__ . '/generalView.php';
            break;
    }

    if ( file_exists( $template ) ) {
        include $template;
    }
    ?>
</div>