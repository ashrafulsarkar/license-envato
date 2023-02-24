<h3><?php _e( 'General Settings', 'licenseenvato' ); ?></h3>
<?php
general_setting_handler();
$get_token_secret = get_option('envato_licenser_token_secret');
if ($get_token_secret) {
    $envato_licenser_token_secret = $get_token_secret;
}

?>
<div class="general_settings">
    <form action="" method="post">
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="token_secret"><?php _e('Token secret key', 'licenseenvato')?></label>
                    </th>
                    <td>
                        <input name="token_secret" type="text" id="token_secret" value="<?php echo $envato_licenser_token_secret;?>" class="regular-text">
                        <p class="description" id="token_secret-description"><?php _e('If you want more secure token, use token secret key.', 'licenseenvato');?></p>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <?php wp_nonce_field( 'submit_general_setting' ); ?>
        <?php submit_button( __( 'Save Changes', 'licenseenvato' ), 'primary', 'submit_general' ); ?>
    </form>
</div>
