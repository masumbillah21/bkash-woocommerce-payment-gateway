<?php

namespace BWPG\Inc;
use BWPG\Inc\Traits\Singleton;

class Check_Auth
{
    use Singleton;

    public function __construct()
    {
        $this->setup_hooks();
    }

    private function setup_hooks()
    {
        add_action('wp_ajax_check_bkash_connection', [$this, 'check_bkash_connection']);
    }


    public function check_bkash_connection() {
        $username = get_option('bwpg_username', '');
        $password = get_option('bwpg_password', '');
        $app_key = get_option('bwpg_app_key', '');
        $app_secret = get_option('bwpg_app_secret', '');
        $environment = get_option('bwpg_environment', '');
        $live_link = get_option( 'bwpg_live_url' );
        $sanbox_link = get_option( 'bwpg_sandbox_url' );

        $api_endpoint = ($environment === 'live') ? $live_link : $sanbox_link;


        $request_data = array(
            'app_key' => $app_key,
            'app_secret' => $app_secret
        );
        
        $url = "$api_endpoint/tokenized/checkout/token/grant";
        $request_data_json = json_encode($request_data);
        
        $headers = array(
            'Content-Type' => 'application/json',
            'username' => $username,
            'password' => $password,
        );
        
        $args = array(
            'method'    => 'POST',
            'body'      => $request_data_json,
            'headers'   => $headers,
        );
        
        $response = wp_remote_post($url, $args);
        
        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            $response = array('success' => false, 'message' => $error_message);
        } else {
            $response_body = wp_remote_retrieve_body($response);
            $response = json_decode($response_body, true);
        }

        wp_send_json($response);
    }
}