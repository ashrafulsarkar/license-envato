<?php
// Exit if accessed directly
defined( 'ABSPATH' ) || exit;
?>
<h3><?php esc_html_e( 'General Settings', 'license-envato' ); ?></h3>
<?php
/**
 * Allow add-ons to append their own sections below the general settings.
 */
do_action( 'license_envato_general_settings_after' );
?>
