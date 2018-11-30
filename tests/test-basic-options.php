<?php
/**
 * WooCommerce Phone Validation Basic Options Tests.
 *
 * @since   0.0.1
 * @package WooCommerce_Phone_Validation
 */
class WCPV_Basic_Options_Test extends WP_UnitTestCase {

	/**
	 * Test if our class exists.
	 *
	 * @since  0.0.1
	 */
	function test_class_exists() {
		$this->assertTrue( class_exists( 'WCPV_Basic_Options') );
	}

	/**
	 * Test that we can access our class through our helper function.
	 *
	 * @since  0.0.1
	 */
	function test_class_access() {
		$this->assertInstanceOf( 'WCPV_Basic_Options', woocommerce_phone_validation()->basic-options );
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
