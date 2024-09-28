<?php

namespace BWPG\Inc;
use BWPG\Inc\Traits\Singleton;
use \WC_Payment_Gateway;
use BWPG\Inc\BKash_Settings;
class WC_Gateway_bKash extends WC_Payment_Gateway {
    use Singleton;
    public $settings;

    public function __construct() {
        $this->id = 'bkash';
        $this->icon = '';
        $this->has_fields = false;
        $this->method_title = __('bKash', 'bwpg');
        $this->method_description = __('Accept payments via bKash.', 'bwpg');

        // Load settings
        $settings = BKash_Settings::get_instance();
        $this->form_fields = $settings->form_fields;
        
        // Actions
        add_action('woocommerce_update_options_payment_gateways_' . $this->id, [$this, 'process_admin_options']);
    }

    public function admin_options() {
        echo '<h2>' . esc_html($this->method_title) . '</h2>';
        echo '<p>' . esc_html($this->method_description) . '</p>';
        echo '<table class="form-table">';
        $this->generate_settings_html();
        echo '</table>';
        $this->add_connection_check_button();
    }

    private function add_connection_check_button() {
        echo '<p class="submit">';
        echo '<button id="check-connection" class="button-primary">' . __( 'Check Authentication', 'woocommerce' ) . '</button>';
        echo '</p>';
    }

    public function process_payment( $order_id ) {

        $order = wc_get_order( $order_id );
        $username = $this->get_option('bwpg_username');
        $password = $this->get_option('bwpg_password');
        $app_key = $this->get_option('bwpg_app_key');
        $app_secret = $this->get_option('bwpg_app_secret');
        $environment = $this->get_option('bwpg_environment');
        $invoice_prefix = $this->get_option('bwpg_invoice_prefix');
        $callback_url = $this->get_option('bwpg_callback_url');
        $live_link = get_option('bwpg_live_url');
        $sanbox_link = get_option('bwpg_sandbox_url');
    
        $api_endpoint = ($environment === 'live') ? $live_link : $sanbox_link;
    
        // Step 1: Get Authorization Token
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
            return array('result' => 'fail', 'message' => $error_message);
        } else {
            $response_body = wp_remote_retrieve_body($response);
            $token_response = json_decode($response_body, true);
    
            if (!isset($token_response['id_token'])) {
                return array('result' => 'fail', 'message' => 'Failed to obtain token.');
            }
    
            $auth_token = $token_response['id_token'];
        }
        
        $requestbody = array(
            'mode' => '0011',
            'amount' => $order->get_total(),
            'currency' => 'BDT',
            'intent' => 'sale',
            'payerReference' => $order->get_billing_phone(),
            'merchantInvoiceNumber' => $invoice_prefix . $order->get_order_number(),
            'callbackURL' => get_permalink($callback_url),
        );
    
        $checkout_url = "$api_endpoint/tokenized/checkout/create";
        $requestbody_json = json_encode($requestbody);
    
        $checkout_headers = array(
            'Content-Type' => 'application/json',
            'Authorization' => $auth_token,
            'X-APP-Key' => $app_key,
        );
        
        
    
        $checkout_args = array(
            'method'    => 'POST',
            'body'      => $requestbody_json,
            'headers'   => $checkout_headers,
        );
    
        $checkout_response = wp_remote_post($checkout_url, $checkout_args);
        
        $response_json = json_encode($checkout_response);
    
        if (is_wp_error($checkout_response)) {
            $error_message = $checkout_response->get_error_message();
            return array('result' => 'fail', 'message' => $error_message);
        } else {
            $checkout_response_body = wp_remote_retrieve_body($checkout_response);
            $checkout_data = json_decode($checkout_response_body, true);
    
            if (!isset($checkout_data['bkashURL'])) {
                return array('result' => 'fail', 'message' => 'Checkout URL not found.');
            }
            
            update_post_meta($order_id, "bkash_payment_id", $checkout_data['paymentID']);

            return array(
                'result'   => 'success',
                'redirect' => $checkout_data['bkashURL'],
            );
        }
    }
}
