<?php
/*
Plugin Name: Woocommerce Glass Sheet Quote Calculator
Plugin URI: https://github.com/sinebeef
Description: Sheet glass calculator add to cart function.
Author: sine
Version: 0.1
Author URI: https://github.com/sinebeef
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	wp_die();
}

class OMSGC {
	
	/**
	 * HTML and CSS frontend forms
	 * @var Octave_PGHQ_Markup
	 */  
	public $markup;
	
    public function __construct(){
	/**
	 * Check if woocommerce is available
	 */
		if ( ! $this->is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			return;
		}
		$this->define_constants();
		$this->load_classes();
	}
	
	public function is_plugin_active( $plugin ) {
	/**
	 * Check whether the plugin is active.
	 */
		return in_array( $plugin, (array) get_option( 'active_plugins', array() ) ) || $this->is_plugin_active_for_network( $plugin );
	}

	/**  
	 * Load Classes
	 */
	public function load_classes() {
		require_once( OCTAVE_OMSGC_INC_PATH . 'class-markup.php' );
		$this->markup_class();
	}
	
	public function markup_class() {
		if ( ! $this->markup ) {
			$this->markup = new Octave_OMSGC_Markup;
		}
		return $this->markup;
	}
	
	public function define_constants() {
	/**
	 * Set constants.
	 */
		$this->define( 'OCTAVE_OMSGC_PATH', plugin_dir_path( __FILE__ ) );
		$this->define( 'OCTAVE_OMSGC_URL', plugin_dir_url( __FILE__ ) );
		$this->define( 'OCTAVE_OMSGC_TEMP', plugin_dir_path( __FILE__ ) . 'templates/' );
		$this->define( 'OCTAVE_OMSGC_INC_PATH', OCTAVE_OMSGC_PATH . 'inc/' );
		$this->define( 'OCTAVE_OMSGC_BASENAME', plugin_basename( __FILE__ ) );
    }

	private function define( $name, $value ) {
	/**
	 * Define constant if not already set.
	 *
	 * @param string      $name
	 * @param string|bool $value
	 */
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}
}

$ompghq = new OMSGC();

