<h3><?php esc_html_e( 'Envato Account Settings', 'license-envato' ); ?></h3>
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
                            <label for="envato_token"><?php esc_html_e( 'Your Personal Token Here', 'license-envato' ); ?></label>
                        </h4>
                    </div>
                    <div class="input_box">
                        <input type="text" name="envato_token" id="envato_token" class="regular-text" value="<?php echo esc_attr( $get_license_envato_envato_token );?>">
                    </div>
                    <p class="description"><?php esc_html_e( 'You need a "personal token" before you can validate purchase codes for your items. This is similar to a password that grants limited access to your account, but it\'s exclusively for the API.', 'license-envato' ); ?>  <a href="https://build.envato.com/create-token" target="_blank"><?php esc_html_e( 'Create a token.', 'license-envato' ); ?></a>
                    </p>
                </div>
                
                <?php wp_nonce_field( 'license_envato_envato_token' ); ?>
                <?php submit_button( esc_html__( 'Save Envato Token', 'license-envato' ), 'primary', 'submit_envato_token' ); ?>
            </form>
        </div>
        <div class="requarement">
            <h4><?php esc_html_e('Minimum Permission','license-envato');?></h4>
            <ul>
                <li><?php esc_html_e('View and search Envato sites','license-envato');?></li>
                <li><?php esc_html_e('View your Envato Account username','license-envato');?></li>
                <li><?php esc_html_e('View your email address','license-envato');?></li>
                <li><?php esc_html_e('View your account profile details','license-envato');?></li>
                <li><?php esc_html_e('View your account financial history','license-envato');?></li>
                <li><?php esc_html_e('Download your purchased items','license-envato');?></li>
                <li><?php esc_html_e('View your items\' sales history','license-envato');?></li>
                <li><?php esc_html_e('Verify purchases of your items','license-envato');?></li>
                <li><?php esc_html_e('List purchases you\'ve made','license-envato');?></li>
                <li><?php esc_html_e('Verify purchases you\'ve made','license-envato');?></li>
                <li><?php esc_html_e('View your purchases of the app creator\'s items','license-envato');?></li>
            </ul>
        </div>
    </div>
<?php }else{ ?>
    <form action="" method="post">
        <?php wp_nonce_field( 'license_envato_unlink' ); ?>
        <?php submit_button( esc_html__( 'Deactivated Envato Account', 'license-envato' ), 'danger', 'unlink_envato_token' ); ?>
    </form>
    
<?php } ?>