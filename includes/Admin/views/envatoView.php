<h3><?php _e( 'Envato Licenser Settings', 'envatolicenser' ); ?></h3>
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
    <form action="" method="post" class="envato_licenser">
        <div class="token_box">
            <div class="label">
                <h4>
                    <label for="token"><?php _e( 'Your Personal Token Here', 'envatolicenser' ); ?></label>
                </h4>
            </div>
            <div class="input_box">
                <input type="text" name="envato_token" id="envato_token" class="regular-text" value="<?php echo $get_envato_licenser_envato_token;?>">
            </div>
            <p class="description"><?php echo _e( 'You need a “personal token” before you can validate purchase codes for your items. This is similar to a password that grants limited access to your account, but it’s exclusively for the API.', 'envatolicenser' ); ?>  <a href="https://build.envato.com/create-token" target="_blank"><?php echo _e( 'Create a token.', 'envatolicenser' ); ?></a>
            </p>
        </div>
        
        <?php wp_nonce_field( 'envato_licenser_envato_token' ); ?>
        <?php submit_button( __( 'Save Envato Token', 'envatolicenser' ), 'primary', 'submit_envato_token' ); ?>
    </form>
    <h4>Minimum Permission</h4>
    <ul>
        <li>View and search Envato sites</li>
        <li>View your Envato Account username</li>
        <li>View your email address</li>
        <li>View your account profile details</li>
        <li>View your account financial history</li>
        <li>Download your purchased items</li>
        <li>View your items' sales history</li>
        <li>Verify purchases of your items</li>
        <li>List purchases you've made</li>
        <li>Verify purchases you've made</li>
        <li>View your purchases of the app creator's items</li>
    </ul>
<?php }else{ ?>
    <form action="" method="post">
        <?php wp_nonce_field( 'envato_licenser_unlink' ); ?>
        <?php submit_button( __( 'Deactive Envato Account', 'envatolicenser' ), 'danger', 'unlink_envato_token' ); ?>
    </form>
    
<?php } ?>