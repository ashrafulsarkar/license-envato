<?php
// Exit if accessed directly
defined('ABSPATH') || exit;

// Define allowed tab values to prevent LFI
$license_envato_allowed_tabs = array('general', 'envato');
// The Free vs Pro tab is only relevant while the Pro add-on is not active
if ( ! license_envato_is_pro_active() ) {
    $license_envato_allowed_tabs[] = 'get-pro';
}
// Apply filter to allow extensions to add their own tabs
$license_envato_allowed_tabs = apply_filters('license_envato_allowed_tabs', $license_envato_allowed_tabs);

// Verify nonce if tab parameter is set
$action = 'general';
if (isset($_GET['tab'])) {
    // Verify nonce for tab switching if provided
    if (isset($_GET['_wpnonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), 'license_envato_switch_tab')) {
        $tab = sanitize_text_field(wp_unslash($_GET['tab']));
        // Only allow values from the whitelist
        $action = in_array($tab, $license_envato_allowed_tabs) ? $tab : 'general';
    } elseif (!isset($_GET['_wpnonce'])) {
        // If no nonce is provided, still allow tab switching but sanitize input
        $tab = sanitize_text_field(wp_unslash($_GET['tab']));
        // Only allow values from the whitelist
        $action = in_array($tab, $license_envato_allowed_tabs) ? $tab : 'general';
    }
}
?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Settings', 'license-envato' ); ?></h1>
    <a href="https://ashrafulsarkar.github.io/license-envato/" target="_blank" rel="noopener noreferrer" class="page-title-action" style="float:right;"><?php esc_html_e( 'Documentation', 'license-envato' ); ?></a>
    <hr class="wp-header-end">
    <nav class="nav-tab-wrapper">
        <?php $licenseEnvato_nav = [
            'general' => esc_html__('General', 'license-envato'),
            'envato' => esc_html__('Envato', 'license-envato'),
            ];

            if ( ! license_envato_is_pro_active() ) {
                $licenseEnvato_nav['get-pro'] = esc_html__( 'Get Pro', 'license-envato' );
            }

            $licenseEnvato_nav_array =  apply_filters( 'license_envato_settings_nav', $licenseEnvato_nav );
            $license_envato_nav_html = '';
            if ($licenseEnvato_nav_array) {
                foreach ( $licenseEnvato_nav_array as $license_envato_tab_key => $license_envato_tab_label ) {
                    $license_envato_tab_class = ( $action == $license_envato_tab_key ) ? 'nav-tab-active' : '';
                    // Add nonce to tab links
                    $license_envato_tab_nonce = wp_create_nonce('license_envato_switch_tab');
                    $link = admin_url( 'admin.php?page=licenseenvato-settings&tab=' . $license_envato_tab_key . '&_wpnonce=' . $license_envato_tab_nonce );
                    $license_envato_nav_html .= '<a href="' . esc_url($link) . '" class="nav-tab ' . esc_attr($license_envato_tab_class) . '">' . esc_html($license_envato_tab_label) . '</a>';
                }
            }
            echo wp_kses_post($license_envato_nav_html);
        ?>
    </nav>

    <?php
    $license_envato_view_dir = __DIR__;
    $licenseEnvato_nav_view =  apply_filters( 'license_envato_settings_view', $license_envato_view_dir, $action );

    if ($licenseEnvato_nav_view) {
        // Ensure we only include files within the plugin directory structure
        $license_envato_template = realpath("{$licenseEnvato_nav_view}/{$action}.php");
        $license_envato_nav_view_dir = realpath($licenseEnvato_nav_view);

        // Verify the template is a child of the nav view directory to prevent path traversal
        if ($license_envato_template && $license_envato_nav_view_dir && strpos($license_envato_template, $license_envato_nav_view_dir) === 0 && file_exists($license_envato_template)) {
            include $license_envato_template;
        } else {
            // Fallback to general.php with the same security checks
            $license_envato_general_template = realpath("{$licenseEnvato_nav_view}/general.php");
            if ($license_envato_general_template && strpos($license_envato_general_template, $license_envato_nav_view_dir) === 0) {
                include $license_envato_general_template;
            }
        }
    }
    ?>
</div>