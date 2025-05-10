<div class="wrap">
    <h1 class="wp-heading-inline"><?php esc_html_e( 'Documentation', 'license-envato' ); ?></h1>
</div>
<h2><?php esc_html_e('Step 1 (Your Site)', 'license-envato');?></h2>
<ul>
    <li><?php esc_html_e('1. Install this plugin on your site.', 'license-envato');?></li>
    <li><?php esc_html_e('2. Goto plugin settings > Activate Envato Token.', 'license-envato');?></li>
</ul>
<p><?php esc_html_e('Alright, the Plugin settings are done. If you want more unique license tokens add a Token secret key in the General Setting area. (Any letter/word)', 'license-envato');?></p>
<h2><?php esc_html_e('Step 2 (Your Theme/Plugin)', 'license-envato');?></h2>
<ul>
    <li><?php esc_html_e('1. Goto your theme or plugin.', 'license-envato');?></li>
    <li><?php esc_html_e('2. Copy this code.', 'license-envato');?></li>
    <li><?php esc_html_e('3. Add this code to your theme or plugin.', 'license-envato');?></li>
</ul>
<p class="display_code" readonly><?php show_source(LICENSE_ENVATO_FILE_PATH . '/includes/Admin/doc/class.license.php');?></p>
<ul>
    <li><?php esc_html_e('4. Replace "YOUR_SITE_URL" >', 'license-envato');?> <b><?php echo esc_html(get_option( 'siteurl' ));?></b></li>
    <li><?php esc_html_e('5. Replace "TEXT_DOMAIN"', 'license-envato');?></li>
    <li><?php esc_html_e('6. Replace "YOUR_PREFIX"', 'license-envato');?></li>
</ul>
<h2><?php esc_html_e('Step 3 (Your Theme/Plugin)', 'license-envato');?></h2>
<p><?php esc_html_e('Now call this Class, where you want to add your theme/plugin License Box.', 'license-envato');?></p>
<p class="display_code small_box" readonly><?php show_source(LICENSE_ENVATO_FILE_PATH . '/includes/Admin/doc/function-call.php');?></p>
<p><?php esc_html_e('Congratulations! All Setup is done. Enjoy and conditionally manage what you want.', 'license-envato');?></p>
