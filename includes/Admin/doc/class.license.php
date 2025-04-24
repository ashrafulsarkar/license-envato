<?php
/**
 * licenseCodeVerifyForm()
 */

class licenseCodeVerifyForm {

    const LICENCE_CALL_URL = "YOUR_SITE_URL";

    private $licenceActivate_error;
    private $licenceDeactivate_error;

    public function __construct() {

        $this->licenceActivate_error = $this->licenceActivate();
        $this->licenceDeactivate_error = $this->licenceDeactivate();
        
        $this->LicenceHTMLForm();
    }

    public function LicenceHTMLForm(){ ?>
        <h2><?php esc_html_e('License Activation Form', 'license-envato');?></h2>
        <?php
        $token = get_option('envato_token');
        $isActivated = get_option('envato_is_activated');

        if ($token && $isActivated) {
            ?>
            <?php
            if ($this->licenceDeactivate_error) {?>
                <p class="licence_error"><?php echo esc_html( $this->licenceDeactivate_error );?></p>
            <?php }?>
            <p><?php esc_html_e('You can click this button to deactivate your license code from this domain if you are going to transfer your website to some other domain or server.', 'license-envato');?></p>
            <form method="post">
                <input type="hidden" name="envato_deactivate" value="1">
                <?php wp_nonce_field( 'submit_deactivate' ); ?>
                <?php submit_button( esc_html__( 'Deactivate', 'license-envato' ), 'danger', 'submit_deactivate' ); ?>
            </form>
            <?php
        }else{ ?>
            <?php
            if ($this->licenceActivate_error) {?>
                <p class="licence_error"><?php echo esc_html( $this->licenceActivate_error );?></p>
            <?php }?>
            <form method="post">
                <label for="purchase_code"><?php esc_html_e('Purchase code', 'license-envato'); ?> (<a href="https://help.market.envato.com/hc/en-us/articles/202822600-Where-Is-My-Purchase-Code-" target="_blank"><?php esc_html_e('Where can I get my purchase code?', 'license-envato');?></a>)</label>
                <input type="text" style="width:100%" id="purchase_code" name="purchase_code" placeholder="Example: 1e71cs5f-13d9-41e8-a140-2cff01d96afb">
                
                <?php wp_nonce_field( 'submit_activate' ); ?>
                <?php submit_button( esc_html__( 'Activate', 'license-envato' ), 'danger', 'submit_activate' ); ?>
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
        if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'submit_activate' ) ) {
            wp_die( 'Are you cheating?' );
        }
    
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Are you cheating?' );
        }
    
        $purchase_code = isset( $_POST['purchase_code'] ) ? sanitize_text_field( $_POST['purchase_code'] ) : '';

        if ($purchase_code) {
            $url = self::LICENCE_CALL_URL."/wp-json/licenseenvato/v1/active";
            $domain = $this->domain();
            $response = $this->apicall($url, $purchase_code, $domain);
            $date = json_decode($response);

            $token = isset( $date->token ) ? $date->token : '';
            $itemid = isset( $date->itemid ) ? $date->itemid : '';
            if ($token) {
                update_option('envato_is_activated_'.$itemid, true);
                update_option('envato_token', $token);
                update_option('envato_purchase_code', $purchase_code);
            }else{
                $statusCode = isset( $date->code ) ? $date->code : '';
                $statusMessage = isset( $date->message ) ? $date->message : '';
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
        $code = get_option('envato_token');
        if ( ! isset( $_POST['submit_deactivate'] ) ) {
            return;
        }
        if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'submit_deactivate' ) ) {
            wp_die( 'Are you cheating?' );
        }
    
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Are you cheating?' );
        }
    
        $envato_deactivate = isset( $_POST['envato_deactivate'] ) ? sanitize_text_field( $_POST['envato_deactivate'] ) : '';

        if ($envato_deactivate) {
            $url = self::LICENCE_CALL_URL."/wp-json/licenseenvato/v1/deactive";
            
            $response = $this->apicall($url, $code);
	        $date = json_decode($response);

            $statusCode = isset( $date->code ) ? $date->code : '';
            $statusMessage = isset( $date->message ) ? $date->message : '';
            if ($statusCode == 'already_deactivated' ) {
                delete_option('envato_is_activated');
                delete_option('envato_token');
                delete_option('envato_purchase_code');
            }elseif ($statusCode) {
                return $statusMessage;
            }else{
                delete_option('envato_is_activated');
                delete_option('envato_token');
                delete_option('envato_purchase_code');
            }
        }
    }

    private function apicall($url, $purchase_code, $domain = null){

        if ($domain) {
            $body = array(
                'code' => $purchase_code,
                'domain' => $domain,
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