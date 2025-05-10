<?php
/**
 * licenseCodeVerifyForm()
 */

class licenseCodeVerifyForm {

    const LICENCE_CALL_URL = "YOUR_SITE_URL";
    const PREFIX = "YOUR_PREFIX";

    private $licenceActivate_error;
    private $licenceDeactivate_error;

    public function __construct() {

        $this->licenceActivate_error = $this->licenceActivate();
        $this->licenceDeactivate_error = $this->licenceDeactivate();
        
        $this->LicenceHTMLForm();
    }

    public function LicenceHTMLForm(){ ?>
        <h2><?php esc_html_e('License Activation Form', 'TEXT_DOMAIN');?></h2>
        <?php
        $token = get_option(self::PREFIX.'_envato_token');
        $isActivated = get_option(self::PREFIX.'_envato_is_activated');

        if ($token && $isActivated) {
            ?>
            <?php
            if ($this->licenceDeactivate_error) {?>
                <p class="licence_error"><?php echo esc_html( $this->licenceDeactivate_error );?></p>
            <?php }?>
            <p><?php esc_html_e('You can click this button to deactivate your license code from this domain if you are going to transfer your website to some other domain or server.', 'TEXT_DOMAIN');?></p>
            <form method="post">
                <input type="hidden" name="envato_deactivate" value="1">
                <?php wp_nonce_field( 'submit_deactivate' ); ?>
                <?php submit_button( esc_html__( 'Deactivate', 'TEXT_DOMAIN' ), 'danger', 'submit_deactivate' ); ?>
            </form>
            <?php
        }else{ ?>
            <?php
            if ($this->licenceActivate_error) {?>
                <p class="licence_error"><?php echo esc_html( $this->licenceActivate_error );?></p>
            <?php }?>
            <form method="post">
                <label for="purchase_code"><?php esc_html_e('Purchase code', 'TEXT_DOMAIN'); ?> (<a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank"><?php esc_html_e('Where can I get my purchase code?', 'TEXT_DOMAIN');?></a>)</label>
                <input type="text" style="width:100%" id="purchase_code" name="purchase_code" placeholder="Example: 1e71cs5f-13d9-41e8-a140-2cff01d96afb">
                
                <?php wp_nonce_field( 'submit_activate' ); ?>
                <?php submit_button( esc_html__( 'Activate', 'TEXT_DOMAIN' ), 'danger', 'submit_activate' ); ?>
            </form>
            <?php
        }
    }

    private function licenceActivate(){
        if ( ! isset( $_POST['submit_activate'] ) ) {
            return;
        }
        if ( ! isset( $_POST['purchase_code'] ) || empty($_POST['purchase_code']) ) {
            return 'Please Enter Purchase Code.';
        }
        // Unslash and sanitize nonce
        $nonce = isset($_POST['_wpnonce']) ? sanitize_text_field(wp_unslash($_POST['_wpnonce'])) : '';
        if ( ! wp_verify_nonce( $nonce, 'submit_activate' ) ) {
            wp_die( 'Are you cheating?' );
        }
    
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Are you cheating?' );
        }
        // Unslash and sanitize purchase_code
        $purchase_code = isset( $_POST['purchase_code'] ) ? sanitize_text_field( wp_unslash( $_POST['purchase_code'] ) ) : '';

        if ($purchase_code) {
            $url = self::LICENCE_CALL_URL."/wp-json/licenseenvato/v1/active";
            $domain = $this->domain();
            $itemid = get_option(self::PREFIX.'_envato_itemid');
            
            $response = $this->apicall($url, $purchase_code, $domain, $itemid);
            $data = json_decode($response);

            $token = isset( $data->token ) ? $data->token : '';
            $itemid = isset( $data->itemid ) ? $data->itemid : '';
            if ($token) {
                update_option(self::PREFIX.'_envato_is_activated_'.$itemid, true);
                update_option(self::PREFIX.'_envato_token', $token);
                update_option(self::PREFIX.'_envato_purchase_code', $purchase_code);
                update_option(self::PREFIX.'_envato_itemid', $itemid);
            }else{
                $statusCode = isset( $data->code ) ? $data->code : '';
                $statusMessage = isset( $data->message ) ? $data->message : '';
                if ($statusCode) {
                    return $statusMessage;
                }
            }
        }
    }

    private function domain() {
		$domain = get_option( 'siteurl' );
		$domain = str_replace( 'http://', '', $domain );
		$domain = str_replace( 'https://', '', $domain );
		$domain = str_replace( 'www', '', $domain );
		return urlencode( $domain );
	}

    private function licenceDeactivate(){
        $code = get_option(self::PREFIX.'_envato_token');
        if ( ! isset( $_POST['submit_deactivate'] ) ) {
            return;
        }
        // Unslash and sanitize nonce
        $nonce = isset($_POST['_wpnonce']) ? sanitize_text_field(wp_unslash($_POST['_wpnonce'])) : '';
        if ( ! wp_verify_nonce( $nonce, 'submit_deactivate' ) ) {
            wp_die( 'Are you cheating?' );
        }
    
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Are you cheating?' );
        }
        // Unslash and sanitize envato_deactivate
        $envato_deactivate = isset( $_POST['envato_deactivate'] ) ? sanitize_text_field( wp_unslash( $_POST['envato_deactivate'] ) ) : '';

        if ($envato_deactivate) {
            $url = self::LICENCE_CALL_URL."/wp-json/licenseenvato/v1/deactive";
            
            $response = $this->apicall($url, $code);
	        $date = json_decode($response);

            $statusCode = isset( $date->code ) ? $date->code : '';
            $statusMessage = isset( $date->message ) ? $date->message : '';
            if ($statusCode == 'already_deactivated' ) {
                delete_option(self::PREFIX.'_envato_is_activated');
                delete_option(self::PREFIX.'_envato_token');
                delete_option(self::PREFIX.'_envato_purchase_code');
                delete_option(self::PREFIX.'_envato_itemid');
            }elseif ($statusCode) {
                return $statusMessage;
            }else{
                delete_option(self::PREFIX.'_envato_is_activated');
                delete_option(self::PREFIX.'_envato_token');
                delete_option(self::PREFIX.'_envato_purchase_code');
                delete_option(self::PREFIX.'_envato_itemid');
            }
        }
    }

    private function apicall($url, $purchase_code, $domain = null, $itemid = null){

        if ($domain) {
            $body = array(
                'code' => $purchase_code,
                'domain' => $domain,
                'itemid' => $itemid,
            );
        }else{
            $body = array(
                'token' => $purchase_code,
            );
        }
        
        $headers = array(
            'Content-Type' => 'application/json'
        );
        $response = wp_remote_post( $url , array(
            'method' => 'POST',
            'headers' => $headers,
            'body' => json_encode($body)
        ) );

        return $response['body'];
    }
}
?>