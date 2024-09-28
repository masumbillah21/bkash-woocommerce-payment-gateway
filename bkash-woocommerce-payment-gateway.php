<?php
/**
 * Plugin Name: bKash Payment Gateway for WooCommerce
 * Plugin URI: http://masum-billah.com
 * Description: bKash payment gateway for WooCommerce.
 * Version: 1.0.0
 * Author: H. M. Masum Billah
 * Author URI: http://masum-billah.com
 * Text Domain: bwpg
 * Requires PHP: 7.0
 * Required WordPress: 4.9
 * Required WC Version: 9.0
 * Required Plugins: woocommerce
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


if(!defined('BWPG_DIR_PATH')) define('BWPG_DIR_PATH', untrailingslashit(plugin_dir_path(__FILE__)));

if ( ! defined( 'BWPG_DIR_URI' ) ) {
	define( 'BWPG_DIR_URI', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
}

require_once __DIR__ . '/autoloader.php';

if(get_option('bwpg_live_url', '') == ''){
    update_option('bwpg_live_url', 'https://checkout.pay.bka.sh/v1.2.0-beta');
};

if(get_option('bwpg_sandbox_url', '') == ''){
    update_option('bwpg_sandbox_url', 'https://checkout.sandbox.bka.sh/v1.2.0-beta');
};

function bwpg_plugin_instance() {
    return BWPG\Inc\BWPG_Init::get_instance();
}
bwpg_plugin_instance();


function bwpg_init_bkash_gateway() {
    if (!class_exists('WC_Payment_Gateway')) return;
    function add_bkash_gateway($methods) {
        $methods[] = 'BWPG\Inc\WC_Gateway_bKash';
        return $methods;
    }

    add_filter('woocommerce_payment_gateways', 'add_bkash_gateway');
}
add_action('plugins_loaded', 'bwpg_init_bkash_gateway');