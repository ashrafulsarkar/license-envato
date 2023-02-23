<?php
/**
 * EnvatoLicenseApiCall()
 * EnvatoLicense Api Call handler class
 *
 * @author: Ashraful Sarkar Naiem
 * @since 1.0.0
 */

namespace EnvatoLicenser\API;

use WP_Error;

class EnvatoLicenseApiCall {

    /**
     * envato_token_handler()
     * 
     * @return null
     */
    public function envato_token_handler() {
        if ( !isset( $_POST['submit_envato_token'] ) ) {
            return;
        }

        if ( !wp_verify_nonce( $_POST['_wpnonce'], 'envato_licenser_envato_token' ) ) {
            wp_die( 'Are you cheating?' );
        }

        if ( !current_user_can( 'manage_options' ) ) {
            wp_die( 'Are you cheating?' );
        }

        $envato_token = isset( $_POST['envato_token'] ) ? sanitize_text_field( $_POST['envato_token'] ) : '';

        $user_option_key = hash( 'crc32b', 'envato_licenser_envato' ) . "_user";
        $profile = get_option( $user_option_key );
        if ( $profile ) {

            $profile->account = '';
            update_option( $user_option_key, $profile );
        }

        $option_key = hash( 'crc32b', 'envato_licenser_envato' ) . "_token";
        update_option( $option_key, $envato_token );
    }

    /**
     * getAPIUserHtmlDetails()
     *
     * @return string|false
     */
    public function getAPIUserHtmlDetails() {
        $EnvatoUserInfo = $this->get_envato_userdata();
        ob_start();
        ?>
        <?php if ( empty( $EnvatoUserInfo ) ) {?>
            <div class="alert alert-danger" role="alert">
                <?php _e( "API Information is not valid or not set." );?>
            </div>
        <?php } elseif ( !empty( $EnvatoUserInfo->error ) ) {
            ?>
            <div class="alert alert-danger" role="alert">
                <?php echo wp_kses_post( $EnvatoUserInfo->error ); ?>
            </div>
            <?php
        } elseif ( !empty( $EnvatoUserInfo->error_msg ) ) {
            ?>
            <div class="alert alert-danger" role="alert">
                <?php echo wp_kses_post( $EnvatoUserInfo->error_msg ); ?>
            </div>
            <?php
        } else {
            ?>
            <div class="card">
                <h2><?php _e( 'Envato Account Details', 'envatolicenser' );?></h2>
                <div class="envato_account_details">
                    <div class="account_img">
                        <img src="<?php echo wp_kses_post( $EnvatoUserInfo->account->image ); ?>" class="card-img img-fluid" alt="<?php echo wp_kses_post( $EnvatoUserInfo->account->surname ); ?>">
                    </div>
                    <div class="account_details_info">
                        <div class="card-body">
                            <h3 class="card-title"><?php echo wp_kses_post( $EnvatoUserInfo->account->username ); ?></h3>
                            <div class="card-text">
                                <div><?php echo wp_kses_post( $EnvatoUserInfo->account->firstname . " " . $EnvatoUserInfo->account->surname ); ?></div>
                                <div><?php echo wp_kses_post( $EnvatoUserInfo->account->email ); ?></div>
                                <div><?php echo wp_kses_post( $EnvatoUserInfo->account->country ); ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            }?>
        <?php
        return ob_get_clean();
    }

    /**
     * get_envato_userdata()
     * 
     * @return mixed
     */
    public function get_envato_userdata() {
        $option_key = hash( 'crc32b', 'envato_licenser_envato' ) . "_user";

        $profile = get_option( $option_key );
        if ( !empty( $profile->account->username ) ) {
            return $profile;
        }
        $url = "https://api.envato.com/v1/market/private/user/account.json";
        $json = $this->apicall( $url );
        if ( !empty( $json ) ) {
            $json = json_decode( $json );
        }
        if ( !empty( $json->account ) ) {
            $json->account->email = "";
            $json->account->username = "";

            $eurl = "https://api.envato.com/v1/market/private/user/email.json";
            $ejson = $this->apicall( $eurl );
            if ( !empty( $ejson ) ) {
                $ejson = json_decode( $ejson );
                if ( !empty( $ejson->email ) ) {
                    $json->account->email = $ejson->email;
                }

            }
            $uurl = "https://api.envato.com/v1/market/private/user/username.json";
            $ejson = $this->apicall( $uurl );
            if ( !empty( $ejson ) ) {
                $ejson = json_decode( $ejson );
                if ( !empty( $ejson->username ) ) {
                    update_option( 'envato_licenser_token_valid', true );
                    $json->account->username = $ejson->username;
                }

            }
        }

        update_option( $option_key, $json ) OR add_option( $option_key, $json );
        return $json;
    }

    /**
     * apicall()
     * 
     * @param $url
     * @param array $postarray
     * @return mixed
     */
    private function apicall( $url, $postarray = array() ) {

        $envato_token = $this->envato_licenser_get_option( '_token' );

        if ( empty( $envato_token ) ) {
            return NULL;
        }
        $headers = ['Authorization' => ' Bearer ' . $envato_token];
        $arguments = array(
            'timeout'     => 120,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => true,
            'headers'     => $headers,
            'sslverify'   => false,
            'cookies'     => array(),
        );
        if ( is_array( $postarray ) && count( $postarray ) > 0 ) {
            $arguments['body'] = $postarray;
            $response = wp_remote_post( $url, $arguments );
        } else {
            $response = wp_remote_get( $url, $arguments );
        }

        if ( is_wp_error( $response ) || empty( $response['body'] ) ) {
            $obj = new \stdClass();
            $obj->status = false;
            $obj->type = "curl_error";
            $obj->error_msg = $response->get_error_message();
            $obj->curl_errno = $response->get_error_code();
            return json_encode( $obj );
        } else {
            return $response['body'];
        }
    }

    /**
     * envato_licenser_get_option()
     * 
     * @param mixed $key
     * @return mixed
     */
    public function envato_licenser_get_option( $key ) {
        $option_key = hash( 'crc32b', 'envato_licenser_envato' ) . $key;
        return get_option( $option_key, null );
    }

    /**
     * deactive_envato_token()
     * 
     * @return null
     */
    public function deactive_envato_token() {
        if ( !isset( $_POST['unlink_envato_token'] ) ) {
            return;
        }

        if ( !wp_verify_nonce( $_POST['_wpnonce'], 'envato_licenser_unlink' ) ) {
            wp_die( 'Are you cheating?' );
        }

        if ( !current_user_can( 'manage_options' ) ) {
            wp_die( 'Are you cheating?' );
        }

        update_option( 'envato_licenser_token_valid', false );
        $option_key = hash( 'crc32b', 'envato_licenser_envato' ) . "_token";
        update_option( $option_key, '' );
    }

    /**
     * envatolicense_verify()
     * 
     * @param $args
     * @return mixed
     */
    public function envatolicense_verify( $args ) {
        $purchaseCode = isset( $args['code'] ) ? $args['code'] : '';
        $requestDomain = isset( $args['domain'] ) ? $args['domain'] : '';

        if ( empty( $purchaseCode ) || empty( $requestDomain ) ) {
            return new WP_Error( 'parameter_request', __( "Sent an invalid request, such as lacking required request parameter.", "envatolicenser" ), ["status" => 400] );
        }

        if ( !preg_match( "/^[a-zA-Z0-9\-]+$/", $purchaseCode ) ) {
            return new WP_Error( 'invalid_code', __( "Invalid purchase code.", "envatolicenser" ), ["status" => 404] );
        }

        if ( get_option( 'envato_licenser_token_valid' ) == false ) {
            return new WP_Error( 'envato_connection_error', __( "Envato Auth Error, Contact your theme or plugin author.", "envatolicenser" ), ["status" => 401] );
        }

        $get_license = $this->get_licence_verify_into_db( $purchaseCode );

        if ( !empty( $get_license ) ) {
            if ( $get_license[0]->token && !empty( $get_license[0]->domain ) ) {
                if ( $get_license[0]->domain == $requestDomain ) {
                    $token['token'] = $get_license[0]->token;
                    return $token;
                } else {
                    return new WP_Error( 'already_activated', __( "Already activate another domain.", "envatolicenser" ), ["status" => 406] );
                }
            } else {
                $username = $get_license[0]->username;
                $genarateNewToken = $this->genarateNewToken( $purchaseCode, $username, $requestDomain );
                if ( $genarateNewToken ) {
                    $token['token'] = $genarateNewToken;
                    return $token;
                }
            }
        } else {
            $data = $this->getPurchaseKeyDetails( $purchaseCode );

            if ( !empty( $data ) ) {
                $data = json_decode( $data );

                if ( !empty( $data->type ) && $data->type == "curl_error" ) {
                    return new WP_Error( 'invalid_code', __( "Invalid purchase code.", "envatolicenser" ), ["status" => 404] );
                } elseif ( !empty( $data->message ) && $data->message == "Unauthorized" ) {
                    return new WP_Error( 'invalid_code', __( "Invalid purchase code.", "envatolicenser" ), ["status" => 404] );
                } else {
                    $skip_properties = array( "description", "classification_url", "author_username", "classification", "site", "author_url", "author_image", "summary", "rating_count", "trending", "attributes", "tags", "previews" );
                    if ( !empty( $data->item ) ) {
                        foreach ( $skip_properties as $vl ) {
                            if ( $vl == "previews" ) {
                                if ( !empty( $data->item->$vl->icon_with_landscape_preview->icon_url ) ) {
                                    $data->item->product_icon = $data->item->$vl->icon_with_landscape_preview->icon_url;
                                }
                            }
                            if ( isset( $data->item->$vl ) ) {
                                unset( $data->item->$vl );
                            }
                        }
                    }
                    if ( !empty( $data->buyer ) ) {
                        $save_data_db = $this->savedataIntoDB( $data, $purchaseCode, $requestDomain );
                        if ( $save_data_db ) {
                            $token['token'] = $save_data_db;
                            return $token;
                        }
                    }
                    return new WP_Error( 'invalid_code', __( "Invalid purchase code.", "envatolicenser" ), ["status" => 404] );
                }
            } else {
                return new WP_Error( 'invalid_code', __( "Invalid purchase code.", "envatolicenser" ), ["status" => 404] );
            }
        }
    }

    /**
     * getPurchaseKeyDetails()
     * 
     * @param $purchase_code
     * @return mixed
     */
    private function getPurchaseKeyDetails( $purchase_code ) {
        $url = "https://api.envato.com/v3/market/author/sale?code=$purchase_code";
        $data = $this->apicall( $url );
        return $data;
    }

    /**
     * get_licence_verify_into_db()
     * 
     * @param mixed $purchase_code
     * @return array|object|null
     */
    public function get_licence_verify_into_db( $purchase_code ) {
        global $wpdb;
        $result = $wpdb->get_results( "SELECT `itemid`,`token`,`username`, `domain` FROM `{$wpdb->prefix}envato_licenser_userlist` WHERE `purchasecode` = '{$purchase_code}'" );
        return $result;
    }

    /**
     * savedataIntoDB()
     * 
     * @param $data
     * @param $purchaseCode
     * @param $domain
     * @return mixed
     */
    private function savedataIntoDB( $data, $purchaseCode, $domain ) {
        $licenseType = $data->license;
        $sold_at = $data->sold_at;
        $support_amount = $data->support_amount;
        $supported_until = $data->supported_until;
        $itemid = $data->item->id;
        $username = $data->buyer;
        $token_secret = get_option( 'envato_licenser_token_secret' );
        $token = hash( 'md5', $username . $purchaseCode . time() . $token_secret );

        global $wpdb;
        $table_name = $wpdb->prefix . "envato_licenser_userlist";

        $sql = $wpdb->prepare( "INSERT INTO " . $table_name . " ( username, itemid, purchasecode, token, domain, licensetype, sold_at, support_amount, supported_until ) VALUES ( %s, %d, %s, %s, %s, %s, %s, %s, %s )", $username, $itemid, $purchaseCode, $token, $domain, $licenseType, $sold_at, $support_amount, $supported_until );
        $wpdb->query( $sql );

        $id = $wpdb->insert_id;
        if ( $id ) {
            return $token;
        }
        return false;
    }

    /**
     * genarateNewToken()
     * 
     * @param $purchaseCode
     * @param $username
     * @param $requestDomain
     * @return mixed
     */
    public function genarateNewToken( $purchaseCode, $username, $requestDomain ) {
        $token_secret = get_option( 'envato_licenser_token_secret' );
        $token = hash( 'md5', $username . $purchaseCode . time() . $token_secret );

        global $wpdb;
        $table_name = $wpdb->prefix . "envato_licenser_userlist";
        $sql = $wpdb->prepare( "UPDATE $table_name SET `token` = %s ,`domain` = %s WHERE `purchasecode` = %s", $token, $requestDomain, $purchaseCode );

        $wpdb->query( $sql );
        $id = $wpdb->rows_affected;
        if ( $id ) {
            return $token;
        }
        return false;
    }

    /**
     * envatolicense_deactive()
     * 
     * @param $args
     * @return mixed
     */
    public function envatolicense_deactive( $args ) {
        $purchaseCode = isset( $args['code'] ) ? $args['code'] : '';

        if ( empty( $purchaseCode ) ) {
            return new WP_Error( 'parameter_request', __( "Sent an invalid request, such as lacking required request parameter.", "envatolicenser" ), ["status" => 400] );
        }

        if ( !preg_match( "/^[a-zA-Z0-9\-]+$/", $purchaseCode ) ) {
            return new WP_Error( 'invalid_code', __( "Invalid purchase code.", "envatolicenser" ), ["status" => 400] );
        }

        $get_license = $this->get_licence_verify_into_db( $purchaseCode );

        if ( !empty( $get_license ) ) {
            if ( $get_license[0]->domain ) {

                global $wpdb;
                $table_name = $wpdb->prefix . "envato_licenser_userlist";

                $sql = $wpdb->prepare( "UPDATE $table_name SET `domain` = '' WHERE `purchasecode` = %s", $purchaseCode );
                $wpdb->query( $sql );

                $id = $wpdb->rows_affected;

                if ( $id ) {
                    $deactive['deactive'] = 'Deactivated successfully';
                    return $deactive;
                }
                return new WP_Error( 'already_deactivated', __( "Already deactivate this license.", "envatolicenser" ), ["status" => 406] );
            } else {
                return new WP_Error( 'already_deactivated', __( "Already deactivate this license.", "envatolicenser" ), ["status" => 406] );
            }
        } else {
            return new WP_Error( 'deactivated_error', __( "This license code not found.", "envatolicenser" ), ["status" => 406] );
        }
    }
}