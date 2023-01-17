<h3><?php _e( 'Envato Licenser Settings', 'envatolicenser' ); ?></h3>
<form action="" method="post" class="envato_licenser">
    <div class="token_box">
        <div class="label">
            <h4>
                <label for="token"><?php _e( 'Your Personal Token Here', 'envatolicenser' ); ?></label>
            </h4>
        </div>
        <div class="input_box">
            <input type="password" name="token" id="token" class="regular-text" value="">
            <?php if ( $this->has_error( 'token' ) ) { ?>
                <p class="description error"><?php echo $this->get_error( 'token' ); ?></p>
            <?php } ?>
        </div>
        <p class="description"><?php echo _e( 'You need a “personal token” before you can validate purchase codes for your items. This is similar to a password that grants limited access to your account, but it’s exclusively for the API.', 'envatolicenser' ); ?>  <a href="https://build.envato.com/create-token" target="_blank"><?php echo _e( 'Create a token.', 'envatolicenser' ); ?></a>
        </p>
    </div>
    
    <?php wp_nonce_field( 'envato_licenser' ); ?>
    <?php submit_button( __( 'Save Envato Token', 'envatolicenser' ), 'primary', 'submit_envato_token' ); ?>
</form>