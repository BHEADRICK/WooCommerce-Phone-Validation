<?php
/**
 * WooCommerce_Phone_Validation.
 *
 * @since   0.0.1
 * @package WooCommerce_Phone_Validation
 */
class WooCommerce_Phone_Validation_Test extends WP_UnitTestCase {

	/**
	 * Test if our class exists.
	 *
	 * @since  0.0.1
	 */
	function test_class_exists() {
		$this->assertTrue( class_exists( 'WooCommerce_Phone_Validation') );
	}

	/**
	 * Test that our main helper function is an instance of our class.
	 *
	 * @since  0.0.1
	 */
	function test_get_instance() {
		$this->assertInstanceOf(  'WooCommerce_Phone_Validation', woocommerce_phone_validation() );
	}

	/**
	 * Replace this with some actual testing code.
	 *
	 * @since  0.0.1
	 */
	function test_sample() {
		$this->assertTrue( true );
	}
}
