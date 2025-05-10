<?php
// Exit if accessed directly
defined('ABSPATH') || exit;

// Define allowed tab values to prevent LFI
$allowed_tabs = array('general', 'envato');
// Apply filter to allow extensions to add their own tabs
$allowed_tabs = apply_filters('license_envato_allowed_tabs', $allowed_tabs);

// Verify nonce if tab parameter is set
$action = 'general';
if (isset($_GET['tab'])) {
    // Verify nonce for tab switching if provided
    if (isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'license_envato_switch_tab')) {
        $tab = sanitize_text_field(wp_unslash($_GET['tab']));
        // Only allow values from the whitelist
        $action = in_array($tab, $allowed_tabs) ? $tab : 'general';
    } elseif (!isset($_GET['_wpnonce'])) {
        // If no nonce is provided, still allow tab switching but sanitize input
        $tab = sanitize_text_field(wp_unslash($_GET['tab']));
        // Only allow values from the whitelist
        $action = in_array($tab, $allowed_tabs) ? $tab : 'general';
    }
}
?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Settings', 'license-envato' ); ?></h1>
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
                    // Add nonce to tab links
                    $nonce = wp_create_nonce('license_envato_switch_tab');
                    $link = admin_url( 'admin.php?page=licenseenvato-settings&tab=' . $key . '&_wpnonce=' . $nonce );
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
        // Ensure we only include files within the plugin directory structure
        $template = realpath("{$licenseEnvato_nav_view}/{$action}.php");
        $nav_view_dir = realpath($licenseEnvato_nav_view);
        
        // Verify the template is a child of the nav view directory to prevent path traversal
        if ($template && $nav_view_dir && strpos($template, $nav_view_dir) === 0 && file_exists($template)) {
            include $template;
        } else {
            // Fallback to general.php with the same security checks
            $general_template = realpath("{$licenseEnvato_nav_view}/general.php");
            if ($general_template && strpos($general_template, $nav_view_dir) === 0) {
                include $general_template;
            }
        }
    }
    ?>
</div>