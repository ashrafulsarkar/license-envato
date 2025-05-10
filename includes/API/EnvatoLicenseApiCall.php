<?php
/**
 * EnvatoLicenseApiCall()
 * EnvatoLicense Api Call handler class
 *
 * @author: Ashraful Sarkar Naiem
 * @since 1.0.0
 */

namespace LicenseEnvato\API;

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

        // Unslash and sanitize nonce
        $nonce = isset($_POST['_wpnonce']) ? sanitize_text_field(wp_unslash($_POST['_wpnonce'])) : '';
        if ( !wp_verify_nonce( $nonce, 'license_envato_envato_token' ) ) {
            wp_die( 'Are you cheating?' );
        }

        if ( !current_user_can( 'manage_options' ) ) {
            wp_die( 'Are you cheating?' );
        }
        // Unslash and sanitize token
        $envato_token = isset( $_POST['envato_token'] ) ? sanitize_text_field( wp_unslash( $_POST['envato_token'] ) ) : '';

        $user_option_key = hash( 'crc32b', 'license_envato_envato' ) . "_user";
        $profile = get_option( $user_option_key );
        if ( $profile ) {

            $profile->account = '';
            update_option( $user_option_key, $profile );
        }

        $option_key = hash( 'crc32b', 'license_envato_envato' ) . "_token";
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
                <?php esc_html_e( "API Information is not valid or not set.", 'license-envato' );?>
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
                <h2><?php esc_html_e( 'Envato Account Details', 'license-envato' );?></h2>
                <div class="envato_account_details">
                    <div class="account_img">
                        <?php
                        $raw_image_url = isset($EnvatoUserInfo->account->image) ? $EnvatoUserInfo->account->image : '';
                        $raw_alt_text  = isset($EnvatoUserInfo->account->surname) ? $EnvatoUserInfo->account->surname : '';

                        $display_image_url = esc_url($raw_image_url);
                        $display_alt_text  = esc_attr($raw_alt_text);

                        $image_html = '';

                        if ($raw_image_url) {
                            $attachment_id = attachment_url_to_postid($raw_image_url);

                            if ($attachment_id) {
                                $image_html = wp_get_attachment_image(
                                    $attachment_id,
                                    'thumbnail',
                                    false,
                                    array('alt' => $display_alt_text, 'class' => 'card-img img-fluid')
                                );
                            }

                            if (empty($image_html)) {
                                $image_html = '<img src="' . $display_image_url . '" class="card-img img-fluid" alt="' . $display_alt_text . '">';
                            }
                        }
                        // Echo the resulting HTML, ensuring it's passed through kses for safety.
                        echo wp_kses_post($image_html);
                        ?>
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
        $option_key = hash( 'crc32b', 'license_envato_envato' ) . "_user";

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
                    update_option( 'license_envato_token_valid', true );
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

        $envato_token = $this->license_envato_get_option( '_token' );

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
     * license_envato_get_option()
     * 
     * @param mixed $key
     * @return mixed
     */
    public function license_envato_get_option( $key ) {
        $option_key = hash( 'crc32b', 'license_envato_envato' ) . $key;
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

        // Unslash and sanitize nonce
        $nonce = isset($_POST['_wpnonce']) ? sanitize_text_field(wp_unslash($_POST['_wpnonce'])) : '';
        if ( !wp_verify_nonce( $nonce, 'license_envato_unlink' ) ) {
            wp_die( 'Are you cheating?' );
        }

        if ( !current_user_can( 'manage_options' ) ) {
            wp_die( 'Are you cheating?' );
        }

        update_option( 'license_envato_token_valid', false );
        $option_key = hash( 'crc32b', 'license_envato_envato' ) . "_token";
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
        $requestItemid = isset( $args['itemid'] ) ? $args['itemid'] : '';

        if ( empty( $purchaseCode ) || empty( $requestDomain ) || empty($requestItemid) ) {
            return new WP_Error( 'parameter_request', __( "Sent an invalid request, such as lacking required request parameter.", 'license-envato' ), ["status" => 400] );
        }

        if ( !preg_match( "/^[a-zA-Z0-9\-]+$/", $purchaseCode ) ) {
            return new WP_Error( 'invalid_code', __( "Invalid purchase code.", 'license-envato' ), ["status" => 404] );
        }

        if ( get_option( 'license_envato_token_valid' ) == false ) {
            return new WP_Error( 'envato_connection_error', __( "Envato Auth Error, Contact your theme or plugin author.", 'license-envato' ), ["status" => 401] );
        }

        $get_license = $this->get_licence_verify_into_db( 'purchasecode', $purchaseCode );

        if ( !empty( $get_license ) ) {
            if ( $get_license[0]->token && !empty( $get_license[0]->domain ) ) {
                if ( $get_license[0]->domain == $requestDomain ) {
                    if ($get_license[0]->itemid == $requestItemid) {
                        $token['token'] = $get_license[0]->token;
                        return $token;
                    }else {
                        return new WP_Error( 'invalid_code', __( "Invalid purchase code for this item.", 'license-envato' ), ["status" => 406] );
                    }
                } else {
                    return new WP_Error( 'already_activated', __( "Already activate another domain.", 'license-envato' ), ["status" => 406] );
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
                    return new WP_Error( 'invalid_code', __( "Invalid purchase code.", 'license-envato' ), ["status" => 404] );
                } elseif ( !empty( $data->message ) && $data->message == "Unauthorized" ) {
                    return new WP_Error( 'invalid_code', __( "Invalid purchase code.", 'license-envato' ), ["status" => 404] );
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
                    return new WP_Error( 'invalid_code', __( "Invalid purchase code.", 'license-envato' ), ["status" => 404] );
                }
            } else {
                return new WP_Error( 'invalid_code', __( "Invalid purchase code.", 'license-envato' ), ["status" => 404] );
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
     * @param mixed $key
     * @param mixed $value
     * @return array|object|null
     */
    public function get_licence_verify_into_db( $key, $value ) {
        global $wpdb;

        $allowed_keys = array('purchasecode', 'token', 'username', 'itemid', 'domain');
        if ( !in_array( $key, $allowed_keys, true ) ) {
            return null; 
        }

        // Cache key based on the lookup key and value
        $cache_group = 'license_envato_db';
        $cache_key_specific = 'license_verify_' . $key . '_' . md5($value);

        $result = wp_cache_get($cache_key_specific, $cache_group);

        if (false === $result) {
            $sql = $wpdb->prepare(
                "SELECT `itemid`, `token`, `username`, `domain` FROM {$wpdb->prefix}license_envato_userlist WHERE `{$key}` = %s",
                $value
            );
            $result = $wpdb->get_results( $sql );
            wp_cache_set($cache_key_specific, $result, $cache_group, HOUR_IN_SECONDS); // Cache for 1 hour
        }
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
        $token_secret = get_option( 'license_envato_token_secret' );
        $token = hash( 'md5', $username . $purchaseCode . time() . $token_secret );

        global $wpdb;
        $table_name = $wpdb->prefix . "license_envato_userlist";

        // Use wpdb->insert instead of direct query
        $inserted = $wpdb->insert(
            $wpdb->prefix . 'license_envato_userlist',
            array(
                'username' => $username,
                'itemid' => $itemid,
                'purchasecode' => $purchaseCode,
                'token' => $token,
                'domain' => $domain,
                'licensetype' => $licenseType,
                'sold_at' => $sold_at,
                'support_amount' => $support_amount,
                'supported_until' => $supported_until
            ),
            array('%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s')
        );

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
        $token_secret = get_option( 'license_envato_token_secret' );
        $token = hash( 'md5', $username . $purchaseCode . time() . $token_secret );

        global $wpdb;
        
        // Use wpdb->update instead of direct query
        $updated = $wpdb->update(
            $wpdb->prefix . 'license_envato_userlist',
            array(
                'token' => $token,
                'domain' => $requestDomain
            ),
            array(
                'purchasecode' => $purchaseCode
            ),
            array('%s', '%s'),
            array('%s')
        );

        if ( $updated ) {
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
        $token = isset( $args['token'] ) ? $args['token'] : '';

        if ( empty( $token ) ) {
            return new WP_Error( 'deactivated_error', __( "Sent an invalid request, such as lacking required request parameter.", 'license-envato' ), ["status" => 400] );
        }

        if ( !preg_match( "/^[a-zA-Z0-9\-]+$/", $token ) ) {
            return new WP_Error( 'deactivated_error', __( "Invalid purchase code.", 'license-envato' ), ["status" => 400] );
        }

        $get_license = $this->get_licence_verify_into_db( 'token', $token );

        if ( !empty( $get_license ) ) {
            if ( $get_license[0]->domain ) {
                global $wpdb;
                
                // Use wpdb->update instead of direct query
                $updated = $wpdb->update(
                    $wpdb->prefix . 'license_envato_userlist',
                    array('domain' => ''),
                    array('token' => $token),
                    array('%s'),
                    array('%s')
                );

                if ( $updated ) {
                    $deactive['deactive'] = 'Deactivated successfully.';
                    return $deactive;
                }
                return new WP_Error( 'already_deactivated', __( "Already deactivate this license.", 'license-envato' ), ["status" => 406] );
            } else {
                return new WP_Error( 'already_deactivated', __( "Already deactivate this license.", 'license-envato' ), ["status" => 406] );
            }
        } else {
            return new WP_Error( 'deactivated_error', __( "This token is not valid.", 'license-envato' ), ["status" => 406] );
        }
    }
}