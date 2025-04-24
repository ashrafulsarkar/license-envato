<h3><?php esc_html_e( 'General Settings', 'license-envato' ); ?></h3>
<?php
licenseEnvato_general_setting_handler();
$get_token_secret = get_option('license_envato_token_secret');
$license_envato_token_secret = '';
if ($get_token_secret) {
    $license_envato_token_secret = $get_token_secret;
}

?>
<div class="general_settings">
    <form action="" method="post">
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="token_secret"><?php esc_html_e('Token secret key', 'license-envato')?></label>
                    </th>
                    <td>
                        <input name="token_secret" type="text" id="token_secret" value="<?php echo esc_attr($license_envato_token_secret);?>" class="regular-text">
                        <p class="description" id="token_secret-description"><?php esc_html_e('If you want more secure token, use token secret key.', 'license-envato');?></p>
                    </td>
                </tr>
            </tbody>
        </table>
        
        <?php wp_nonce_field( 'submit_general_setting' ); ?>
        <?php submit_button( __( 'Save Changes', 'license-envato' ), 'primary', 'submit_general' ); ?>
    </form>
</div>
