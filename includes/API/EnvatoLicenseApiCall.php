<?php

namespace EnvatoLicenser\API;

/**
 * EnvatoLicenseApiCall trait
 */
class EnvatoLicenseApiCall { 

    /**
     * Handle the form
     *
     * @return void
     */
    function envato_token_handler() {
        if ( ! isset( $_POST['submit_envato_token'] ) ) {
            return;
        }

        if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'envato_licenser_envato_token' ) ) {
            wp_die( 'Are you cheating?' );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Are you cheating?' );
        }

        $envato_token    = isset( $_POST['envato_token'] ) ? sanitize_text_field( $_POST['envato_token'] ) : '';

        $user_option_key = hash( 'crc32b', 'envato_licenser_envato' )."_user";
        $profile = get_option($user_option_key);
        if ($profile) {
            
            $profile->account = '';
            update_option($user_option_key, $profile);
        }
    
        $option_key = hash( 'crc32b', 'envato_licenser_envato' )."_token";
        update_option( $option_key , $envato_token );
    }

    function getAPIUserHtmlDetails(){
        $EnvatoUserInfo=$this->get_envato_userdata();
        ob_start();
        ?>
        <?php if(empty($EnvatoUserInfo)){ ?>
            <div class="alert alert-danger" role="alert">
                <?php _e("API Information is not valid or not set."); ?>
            </div>
        <?php }elseif(!empty($EnvatoUserInfo->error)){
            ?>
            <div class="alert alert-danger" role="alert">
                <?php echo wp_kses_post($EnvatoUserInfo->error); ?>
            </div>
            <?php
        }elseif(!empty($EnvatoUserInfo->error_msg)){
            ?>
            <div class="alert alert-danger" role="alert">
                <?php echo wp_kses_post($EnvatoUserInfo->error_msg); ?>
                
            </div>
            <?php
        }else{
            ?>
            <div class="card">
                <h2><?php _e('Envato Account Details', 'envatolicenser');?></h2>
                <div class="envato_account_details">
                    <div class="account_img">
                        <img src="<?php echo wp_kses_post($EnvatoUserInfo->account->image); ?>" class="card-img img-fluid" alt="<?php echo wp_kses_post($EnvatoUserInfo->account->surname); ?>">
                    </div>
                    <div class="account_details_info">
                        <div class="card-body">
                            <h3 class="card-title"><?php echo wp_kses_post($EnvatoUserInfo->account->username); ?></h3>
                            <div class="card-text">
                                <div><?php echo wp_kses_post($EnvatoUserInfo->account->firstname." ".$EnvatoUserInfo->account->surname); ?></div>
                                <div><?php echo wp_kses_post($EnvatoUserInfo->account->email); ?></div>
                                <div><?php echo wp_kses_post($EnvatoUserInfo->account->country); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        } ?>
        <?php
        return ob_get_clean();
    }

    function get_envato_userdata(){
        $option_key = hash( 'crc32b', 'envato_licenser_envato' )."_user";

        $profile = get_option($option_key);
        if(!empty($profile->account->username)){
            return $profile;
        }
        $url="https://api.envato.com/v1/market/private/user/account.json";
        $json=$this->apicall($url);
        if(!empty($json)){
            $json=json_decode($json);
        }
        if(!empty($json->account)) {
            $json->account->email="";
            $json->account->username="";
            
            $eurl   = "https://api.envato.com/v1/market/private/user/email.json";
            $ejson = $this->apicall( $eurl );
            if ( ! empty( $ejson ) ) {
                $ejson = json_decode( $ejson );
                if(!empty($ejson->email)){
                    $json->account->email=$ejson->email;
                }
                
            }
            $uurl   = "https://api.envato.com/v1/market/private/user/username.json";
            $ejson = $this->apicall( $uurl );
            if ( ! empty( $ejson ) ) {
                $ejson = json_decode( $ejson );
                if(!empty($ejson->username)){
                    update_option('envato_licenser_token_valid', true);
                    $json->account->username=$ejson->username;
                }
                
            }
        }
        
        update_option($option_key,$json) OR add_option($option_key,$json);
        return $json;
    }
    
    /**
     * API Call 
     */
    private function apicall($url,$postarray=array()){

        $envato_token = $this->envato_licenser_get_option( '_token' );

        if(empty($envato_token)){
            return NULL;
        }
        $headers=['Authorization'=>' Bearer '.$envato_token];
        $arguments = array(
            'timeout' => 120,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => $headers,
            'sslverify' => false,
            'cookies' => array()
        );
        if(is_array($postarray) && count($postarray)>0){
            $arguments['body']=$postarray;
            $response = wp_remote_post($url, $arguments);
        }else{
            $response = wp_remote_get($url, $arguments);
        }

        if (is_wp_error($response) || empty($response['body'])) {
            $obj=new \stdClass();
            $obj->status=false;
            $obj->type="curl_error";
            $obj->error_msg=$response->get_error_message();
            $obj->curl_errno=$response->get_error_code();
            return  json_encode($obj);
        } else {
            return $response['body'];
        }
    }

    /**
     * get option data
     */
    public function envato_licenser_get_option($key){
        $option_key = hash( 'crc32b', 'envato_licenser_envato' ).$key;
        return get_option($option_key, null);
    }


    public function deactive_envato_token() {
        if ( ! isset( $_POST['unlink_envato_token'] ) ) {
            return;
        }

        if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'envato_licenser_unlink' ) ) {
            wp_die( 'Are you cheating?' );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Are you cheating?' );
        }

        update_option('envato_licenser_token_valid', false);
        $option_key = hash( 'crc32b', 'envato_licenser_envato' )."_token";
        update_option($option_key, '');
    }

}