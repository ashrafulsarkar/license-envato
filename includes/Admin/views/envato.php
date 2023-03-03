<h3><?php _e( 'Envato Account Settings', 'licenseenvato' ); ?></h3>
<?php
$license_envato_api->envato_token_handler();
$license_envato_api->deactive_envato_token();

$get_license_envato_envato_token = $license_envato_api->license_envato_get_option( '_token' );
if ($get_license_envato_envato_token) {
    $license_envato_user_data = $license_envato_api->getAPIUserHtmlDetails();
    echo wp_kses_post( $license_envato_user_data );
}

if (get_option('license_envato_token_valid') == false) { 
    ?>
    <div class="license_activation">
        <div class="license_envato_form">
            <form action="" method="post" class="license_envato">
                <div class="token_box">
                    <div class="label">
                        <h4>
                            <label for="envato_token"><?php _e( 'Your Personal Token Here', 'licenseenvato' ); ?></label>
                        </h4>
                    </div>
                    <div class="input_box">
                        <input type="text" name="envato_token" id="envato_token" class="regular-text" value="<?php echo esc_html( $get_license_envato_envato_token );?>">
                    </div>
                    <p class="description"><?php echo _e( 'You need a “personal token” before you can validate purchase codes for your items. This is similar to a password that grants limited access to your account, but it’s exclusively for the API.', 'licenseenvato' ); ?>  <a href="https://build.envato.com/create-token" target="_blank"><?php echo _e( 'Create a token.', 'licenseenvato' ); ?></a>
                    </p>
                </div>
                
                <?php wp_nonce_field( 'license_envato_envato_token' ); ?>
                <?php submit_button( __( 'Save Envato Token', 'licenseenvato' ), 'primary', 'submit_envato_token' ); ?>
            </form>
        </div>
        <div class="requarement">
            <h4><?php _e('Minimum Permission','licenseenvato');?></h4>
            <ul>
                <li><?php _e('View and search Envato sites','licenseenvato');?></li>
                <li><?php _e('View your Envato Account username','licenseenvato');?></li>
                <li><?php _e('View your email address','licenseenvato');?></li>
                <li><?php _e('View your account profile details','licenseenvato');?></li>
                <li><?php _e('View your account financial history','licenseenvato');?></li>
                <li><?php _e('Download your purchased items','licenseenvato');?></li>
                <li><?php _e('View your items\' sales history','licenseenvato');?></li>
                <li><?php _e('Verify purchases of your items','licenseenvato');?></li>
                <li><?php _e('List purchases you\'ve made','licenseenvato');?></li>
                <li><?php _e('Verify purchases you\'ve made','licenseenvato');?></li>
                <li><?php _e('View your purchases of the app creator\'s items','licenseenvato');?></li>
            </ul>
        </div>
    </div>
<?php }else{ ?>
    <form action="" method="post">
        <?php wp_nonce_field( 'license_envato_unlink' ); ?>
        <?php submit_button( __( 'Deactivated Envato Account', 'licenseenvato' ), 'danger', 'unlink_envato_token' ); ?>
    </form>
    
<?php } ?>