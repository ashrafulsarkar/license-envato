<h3><?php _e( 'Envato Account Settings', 'licenseenvato' ); ?></h3>
<?php
$envato_licenser_api->envato_token_handler();
$envato_licenser_api->deactive_envato_token();

$get_envato_licenser_envato_token = $envato_licenser_api->envato_licenser_get_option( '_token' );
if ($get_envato_licenser_envato_token) {
    $envato_licenser_user_data = $envato_licenser_api->getAPIUserHtmlDetails();
    echo $envato_licenser_user_data;
}

if (get_option('envato_licenser_token_valid') == false) { 
    ?>
    <div class="license_activation">
        <div class="envato_licenser_form">
            <form action="" method="post" class="envato_licenser">
                <div class="token_box">
                    <div class="label">
                        <h4>
                            <label for="envato_token"><?php _e( 'Your Personal Token Here', 'licenseenvato' ); ?></label>
                        </h4>
                    </div>
                    <div class="input_box">
                        <input type="text" name="envato_token" id="envato_token" class="regular-text" value="<?php echo $get_envato_licenser_envato_token;?>">
                    </div>
                    <p class="description"><?php echo _e( 'You need a “personal token” before you can validate purchase codes for your items. This is similar to a password that grants limited access to your account, but it’s exclusively for the API.', 'licenseenvato' ); ?>  <a href="https://build.envato.com/create-token" target="_blank"><?php echo _e( 'Create a token.', 'licenseenvato' ); ?></a>
                    </p>
                </div>
                
                <?php wp_nonce_field( 'envato_licenser_envato_token' ); ?>
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
        <?php wp_nonce_field( 'envato_licenser_unlink' ); ?>
        <?php submit_button( __( 'Deactivated Envato Account', 'licenseenvato' ), 'danger', 'unlink_envato_token' ); ?>
    </form>
    
<?php } ?>