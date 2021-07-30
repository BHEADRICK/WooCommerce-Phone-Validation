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
		add_action(get_class($this->plugin).'_send_status_email', [$this, 'send_status_email'], 10, 2);

		//wc_format_phone_number()
	}

	public function validate_phone(){
		$fields = ['billing_phone', 'shipping_phone'];

		foreach($fields as $field){
			if(isset($_POST[$field])){
				$value = $res = preg_replace("/[^0-9]/", "",  $_POST[$field] );
				if( substr($value, 0, 1) === '1'){
					$value = ltrim($value, '1');
				}
				$type = $field === 'billing_phone'?'Billing':'Shipping';
				if(!preg_match('/^^([0-9]( |-)?)?(\(?[0-9]{3}\)?|[0-9]{3})( |-)?([0-9]{3}( |-)?[0-9]{4}|[a-zA-Z0-9]{7})$$/',$value)){

					wc_add_notice( __( "The $type Phone number you entered ($value) is invalid." ), 'error' );
					error_log('invalid phone number: ' . $value);
				}elseif(WCPV_Basic_Options::is_active() && $key = get_option('numverify_key')){


					$validate = $this->numverify($value, $key);

					if(!$validate->valid){
						error_log('invalid phone number: ' . $value);
					}
					if(!isset($validate->error) && !$validate->valid){
						wc_add_notice( __( "The $type Phone number you entered ($value) is invalid." ), 'error' );
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

									$response =  curl_exec($curl);
									$result = json_decode($response);
									if($response ===false){

										 $response = curl_error($curl);
									}

			curl_close($curl);
		$was_down = get_transient('numverify_down');
		if(is_object($result)){

			if($was_down){
				delete_transient('numverify_down');

				wp_schedule_single_event(time(), get_class($this->plugin).'_send_status_email', ['', false]);
			}

			return $result;
		}else{

			if(!$was_down){

				set_transient('numverify_down', true);

				wp_schedule_single_event(time(), get_class($this->plugin).'_send_status_email', [$response, true]);
			}

			$result = new stdClass();
			$result->valid = true;
			return $result;
		}




	}


	public function send_status_email($response, $down = true){

		$to = get_option('numverify_admin_email');
		if(!$to || !filter_var($to, FILTER_VALIDATE_EMAIL)){
			$to = get_option('admin_email');
		}


		if($down){
			$subject = 'NumVerify is Down';
			$body = 'Numverify has stopped responding and is currently not validating phone numbers.<br>
			 Orders will continue processing without phone validation until this is resolved<br>
			 Response: ' . $response;
		}else{
			$subject = "NumVerify is back up";
			$body = "Phone numbers will continue to be validated.";
		}

		$headers = array('Content-Type: text/html; charset=UTF-8');

		wp_mail($to, $subject,$body, $headers);

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
