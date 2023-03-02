<div class="wrap">
    <h1 class="wp-heading-inline"><?php _e( 'Documentation', 'licenseenvato' ); ?></h1>
</div>
<h2><?php _e('Step 1 (Your Site)', 'licenseenvato');?></h2>
<ul>
    <li><?php _e('1. Install this plugin on your site.', 'licenseenvato');?></li>
    <li><?php _e('2. Goto plugin settings > Activate Envato Token.', 'licenseenvato');?></li>
</ul>
<p><?php _e('Alright, the Plugin settings are done. If you want more unique license tokens add a Token secret key in the General Setting area. (Any letter/word)', 'licenseenvato');?></p>
<h2><?php _e('Step 2 (Your Theme/Plugin)', 'licenseenvato');?></h2>
<ul>
    <li><?php _e('1. Goto your theme or plugin.', 'licenseenvato');?></li>
    <li><?php _e('2. Copy this code.', 'licenseenvato');?></li>
    <li><?php _e('3. Add this code to your theme or plugin.', 'licenseenvato');?></li>
</ul>
<p class="display_code" readonly><?php show_source(LICENSE_ENVATO_FILE_PATH . '/includes/Admin/doc/class.license.php');?></p>
<ul>
    <li><?php _e('4. Replace <b>YOUR_SITE_URL</b> >', 'licenseenvato');?> <b><?php echo get_option( 'siteurl' );?></b></li>
    <li><?php _e('5. Replace <b>TEXT_DOMAIN</b>', 'licenseenvato');?></li>
</ul>
<h2><?php _e('Step 3 (Your Theme/Plugin)', 'licenseenvato');?></h2>
<p><?php _e('Now call this Class, where you want to add your theme/plugin License Box.', 'licenseenvato');?></p>
<p class="display_code small_box" readonly><?php show_source(LICENSE_ENVATO_FILE_PATH . '/includes/Admin/doc/function-call.php');?></p>
<p><?php _e('Congratulations! All Setup is done. Enjoy and conditionally manage what you want.', 'licenseenvato');?></p>
