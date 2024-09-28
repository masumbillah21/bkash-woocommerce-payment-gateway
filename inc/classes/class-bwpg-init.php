<?php

namespace BWPG\Inc;
use BWPG\Inc\Traits\Singleton;

class BWPG_Init
{
    use Singleton;

    public function __construct() {
        $this->setup_hooks();
    }

    public function setup_hooks() {
        add_action('admin_enqueue_scripts', [$this, 'register_scripts']);
    }

    public function register_styles() {
        // Register styles.

	}

	public function register_scripts() {

		wp_register_script( 'bwpg-admin', BWPG_DIR_URI . '/assets/js/bkash-admin.js', ['jquery'], false, true );
		
        wp_enqueue_script( 'bwpg-admin' );

		wp_localize_script( 'main-js', 'bwpgConfig', [
			'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
			'ajax_nonce' => wp_create_nonce( 'loadmore_post_nonce' ),
		] );
	}

    
    
}