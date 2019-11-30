<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Octave_OMSGC_Markup' ) ) {

    class Octave_OMSGC_Markup {

		public $eval_price;
		public $eval_string;

        public function __construct(){
            $this->action_hooks();
        }

        public function action_hooks(){
			
            // Shortcode to output form/modal/loader html for frontend
            add_shortcode( 'omglasscalc', array( $this, 'do_the_shortcode' ) );
			
            // Enqueue CSS and JS
            add_action( 'wp_enqueue_scripts', array( $this, 'setup_scripts_and_styles' ) );		
			
            // Localise the quote.js for ajax url
            add_action( 'wp_ajax_nopriv_omsgc_ajax',  array( $this, 'omsgc_ajax' ) );
            add_action( 'wp_ajax_omsgc_ajax', array( $this, 'omsgc_ajax' ) );
			
			add_action( 'woocommerce_add_cart_item_data', array( $this, 'omsgc_custom_cart_item_data' ), 10, 2 );	
			//add_filter( 'woocommerce_add_cart_item', array( $this, 'custom_cart_item_prices'), 20, 2 );
			add_action( 'woocommerce_before_calculate_totals', array( $this, 'omsgc_update_cart_item_price' ), 10, 1 );
			add_filter( 'woocommerce_get_item_data', array( $this, 'omsgc_woocommerce_filter_item_data' ), 10, 2 );	
			add_action( 'woocommerce_add_order_item_meta', array( $this, 'omsgc_add_order_item_meta' ), 10, 2 );
			add_filter( 'woocommerce_cart_item_price', array( $this, 'woocommerce_cart_item_price_filter'), 10, 3 );

			//woocommerce_add_order_item_meta function is deprecated since version 3.0. Replace with wc_add_order_item_meta.
			//woocommerce_add_order_item_meta is deprecated since version 3.0.0! Use woocommerce_new_order_item instead.
		}
		
		public function woocommerce_cart_item_price_filter( $price, $cart_item, $cart_item_key ) {

			if( isset( $cart_item['unique_key'] ) ){
				$meep = wc_price( $cart_item['custom_price'] );
				return $meep;
			}
			
			return $price;
		}

		// public function custom_cart_item_prices( $cart_item_data, $cart_item_key ) {
		// 	// Get and set your price calculation
		// 	if( isset( $cart_item_data['unique_key'] ) ) {
		// 		$cart_item_data['custom_price'] = $this->eval_price;
		// 		$cart_item_data['custom_msg'] = $this->eval_string;	
		// 	}
		// 	return $cart_item_data;
		// }

		public function omsgc_custom_cart_item_data( $cart_item_data, $product_id ) {

			// Only force uniq cart item if it matches custom glass
			if( $product_id == '7113' ){
				$unique_cart_item_key = md5( microtime() . rand() );
				$cart_item_data['unique_key'] = $unique_cart_item_key;
				$cart_item_data['custom_price'] = $this->eval_price;
				$cart_item_data['custom_msg'] = $this->eval_string;
			}
			return $cart_item_data;
		}

		public function omsgc_update_cart_item_price( $cart_object ) {
			// if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
				// return;
			// }
			foreach ( $cart_object->get_cart() as $cart_item ) {
				// Bail, if no key
				if ( ! isset( $cart_item['unique_key'] ) ) { 
					//echo 'there is a problem';
					continue;
				}
				if ( isset( $cart_item['custom_price'] ) ) { 
					$cart_item['data']->set_price( floatval( $cart_item['custom_price'] ) );
				}	
			}
		}
		
		public function omsgc_woocommerce_filter_item_data( $other_data, $cart_item )
		{
			if ( ! empty( $cart_item['custom_msg'] ) ) {
				echo '<br/><span><strong>' . $cart_item['custom_msg'] . '</strong></span><br/>'; 
			}
			return $other_data;
		}
	
        public function setup_scripts_and_styles(){
            wp_enqueue_script( 'omsgc-quote-js', OCTAVE_OMSGC_URL . 'js/omgcalc.js', array('jquery'), '1.0', true );
            wp_localize_script( 'omsgc-quote-js', 'ajaxquote', array( 'ajax_url' => admin_url( 'admin-ajax.php' )));
        }
	
		public function omsgc_add_order_item_meta( $item_id, $values ) {

			if ( ! empty( $values['custom_msg'] ) ) {
				woocommerce_add_order_item_meta( $item_id, 'type', $values['custom_msg'] );           
			}
		}		

        /**
         * Output the HTML into the page source
         */
        public function do_the_shortcode(){
            require_once( OCTAVE_OMSGC_TEMP . 'template-form.php' );
        }

        /**
         * This function deals with the AJAX POST data from the form
         * IMPORTANT!!! >>> Some validation is required here before live
         */
        public function omsgc_ajax(){

		$sobj = $_POST['jason'];
	
		$gc_data = array(
			"gc_height" => $sobj['gcheight'],
			"gc_width" => $sobj['gcwidth'],
			"gc_type" => $sobj['gctype'],
			"gc_size" => $sobj['gcsize'],
			"gc_holes" => $sobj['gcholes'],
			"gc_cuts" => $sobj['gccuts']
		);

		$result = $this->omsgc_form_evaluate($gc_data);
	
		echo var_dump($result);
        die();
		}
		
		public function omsgc_form_evaluate( $f ){

			$total = ( $f['gc_height'] + $f['gc_width'] ) / 2;
			$this->eval_price = $total;
			
			$string = "H:".$f['gc_height']."mm W:".$f['gc_width']."mm T:".$f['gc_type']." S:".$f['gc_size']." H:".$f['gc_holes']." C:".$f['gc_cuts'];
			$this->eval_string = $string;

			// Hidden from search and catalog item 'glass panel' ID
			$product_id = 7113;
			WC()->cart->add_to_cart( $product_id );

			return $this->eval_string;
		}

    } // Class ends
}



