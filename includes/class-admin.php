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
		add_filter('woocommerce_checkout_fields', [$this, 'fields']);
		add_action('woocommerce_checkout_process', [$this, 'validate_phone']);

		//wc_format_phone_number()
	}

	public function validate_phone(){
		$fields = ['billing_phone', 'shipping_phone'];

		foreach($fields as $field){
			if(isset($_POST[$field])){
				$type = $field === 'billing_phone'?'Billing':'Shipping';
				if(!preg_match('/^^([0-9]( |-)?)?(\(?[0-9]{3}\)?|[0-9]{3})( |-)?([0-9]{3}( |-)?[0-9]{4}|[a-zA-Z0-9]{7})$$/', $_POST[$field])){

					wc_add_notice( __( "The $type Phone number you entered is invalid." ), 'error' );
				}elseif(WCPV_Basic_Options::is_active() && $key = get_option('numverify_key')){


					$validate = $this->numverify($_POST[$field], $key);

					if(!isset($validate->error) && !$validate->valid){
						wc_add_notice( __( "The $type Phone number you entered is invalid." ), 'error' );
					}elseif(isset($validate->error) && $validate->error){
						error_log(print_r($validate->error, true));
					}
				}
			}
		}
	}

	private function numverify($phone, $key){




		$phone = preg_replace("/[^0-9]/", "", $phone);
		$url = "http://apilayer.net/api/validate?access_key=$key&country_code=US&format=1&number=$phone";
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		$result = json_decode( curl_exec($curl));

		curl_close($curl);

		return $result;
	}

	public function fields($fields){
		
		if(isset($fields['billing']['billing_phone'])){

		$fields['billing']['billing_phone']['required'] = true;
}

		if(isset($fields['shipping']['shipping_phone'])){

		$fields['shipping']['shipping_phone']['required'] = true;
}


		return $fields;
}
}
