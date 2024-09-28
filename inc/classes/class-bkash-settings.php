<?php

namespace BWPG\Inc;
use BWPG\Inc\Traits\Singleton;

class BKash_Settings {
    use Singleton;

    public array $form_fields = [];

    public function __construct() {
        $this->form_fields = $this->get_form_fields();
    }

    public function get_form_fields(): array {
        return array(
            'BWPGenabled' => array(
                'title'   => __( 'Enable/Disable', 'woocommerce' ),
                'type'    => 'checkbox',
                'label'   => __( 'Enable bKash Payment', 'woocommerce' ),
                'default' => 'yes'
            ),
            'title' => array(
                'title'       => __( 'Title', 'woocommerce' ),
                'type'        => 'text',
                'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce' ),
                'default'     => __( 'bKash', 'woocommerce' ),
                'desc_tip'    => true,
            ),
            'bwpg_username' => array(
                'title'       => __( 'Username', 'woocommerce' ),
                'type'        => 'text',
                'description' => __( 'Your bKash API Username.', 'woocommerce' ),
                'default'     => '',
                'desc_tip'    => true,
            ),
            'bwpg_password' => array(
                'title'       => __( 'Password', 'woocommerce' ),
                'type'        => 'password',
                'description' => __( 'Your bKash API Password.', 'woocommerce' ),
                'default'     => '',
                'desc_tip'    => true,
            ),
            'bwpg_app_key' => array(
                'title'       => __( 'App Key', 'woocommerce' ),
                'type'        => 'text',
                'description' => __( 'Your bKash App Key.', 'woocommerce' ),
                'default'     => '',
                'desc_tip'    => true,
            ),
            'bwpg_app_secret' => array(
                'title'       => __( 'App Secret Key', 'woocommerce' ),
                'type'        => 'password',
                'description' => __( 'Your bKash App Secret Key.', 'woocommerce' ),
                'default'     => '',
                'desc_tip'    => true,
            ),
            'bwpg_environment' => array(
                'title'       => __( 'Environment', 'woocommerce' ),
                'type'        => 'select',
                'options'     => array(
                    'sandbox' => __( 'Sandbox', 'woocommerce' ),
                    'live'    => __( 'Live', 'woocommerce' ),
                ),
                'description' => __( 'Select the environment for bKash payments.', 'woocommerce' ),
                'default'     => 'sandbox',
                'desc_tip'    => true,
            ),
            'bwpg_invoice_prefix' => array(
                'title'       => __( 'Invoice Prefix', 'woocommerce' ),
                'type'        => 'text',
                'description' => __( 'Your invoice prefix.', 'woocommerce' ),
                'default'     => 'wc_',
                'desc_tip'    => true,
            ),
            'bwpg_callback_url' => array(
                'title'       => __( 'Success Page', 'woocommerce' ),
                'type'        => 'select',
                'options'     => $this->get_page_options(),
                'description' => __( 'Select the page to redirect on successful payment.', 'woocommerce' ),
                'desc_tip'    => true,
            )
        );
    }

    private function get_page_options() {
        $pages = get_pages();
        $options = array();
        
        foreach ($pages as $page) {
            $options[$page->ID] = $page->post_title;
        }
    
        return $options;
    }
}
