<h3><?php _e( 'General Settings', 'envatolicenser' ); ?></h3>
<?php
// $envato_licenser_api->envato_token_handler();

$get_envato_licenser_envato_token = '';

?>
<div class="general_settings">
    <form action="" method="post">
        <table class="form-table" role="presentation">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="blogname">Site Title</label>
                    </th>
                    <td>
                        <input name="blogname" type="text" id="blogname" value="WP Software Licenser" class="regular-text">
                    </td>
                </tr>
            </tbody>
        </table>
        
        <?php wp_nonce_field( 'submit_general_setting' ); ?>
        <?php submit_button( __( 'Save Changes', 'envatolicenser' ), 'primary', 'submit_general' ); ?>
    </form>
</div>
