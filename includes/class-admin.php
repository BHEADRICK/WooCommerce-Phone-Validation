<?php
/**
 * WooCommerce Phone Validation Admin.
 *
 * @since   0.0.1
 * @package WooCommerce_Phone_Validation
 */

/**
 * WooCommerce Phone Validation Admin.
 *
 * @since 0.0.1
 */
class WCPV_Admin {
	/**
	 * Parent plugin class.
	 *
	 * @since 0.0.1
	 *
	 * @var   WooCommerce_Phone_Validation
	 */
	protected $plugin = null;

	/**
	 * Constructor.
	 *
	 * @since  0.0.1
	 *
	 * @param  WooCommerce_Phone_Validation $plugin Main plugin object.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  0.0.1
	 */
	public function hooks() {

	}
}
