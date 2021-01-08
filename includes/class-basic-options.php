<?php
/**
 * WooCommerce Phone Validation Basic Options.
 *
 * @since   0.0.1
 * @package WooCommerce_Phone_Validation
 */


/**
 * WooCommerce Phone Validation Basic Options class.
 *
 * @since 0.0.1
 */
class WCPV_Basic_Options {
	/**
	 * Parent plugin class.
	 *
	 * @var    WooCommerce_Phone_Validation
	 * @since  0.0.1
	 */
	protected $plugin = null;

	/**
	 * Option key, and option page slug.
	 *
	 * @var    string
	 * @since  0.0.1
	 */
	protected static $key = 'woocommerce_phone_validation_basic_options';

	/**
	 * Options page metabox ID.
	 *
	 * @var    string
	 * @since  0.0.1
	 */
	protected static $metabox_id = 'woocommerce_phone_validation_basic_options_metabox';

	/**
	 * Options Page title.
	 *
	 * @var    string
	 * @since  0.0.1
	 */
	protected $title = '';

	/**
	 * Options Page hook.
	 *
	 * @var string
	 */
	protected $options_page = '';

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

		// Set our title.
		$this->title = esc_attr__( 'WooCommerce Phone Validation Basic Options', 'woocommerce-phone-validation' );
	}

	/**
	 * Initiate our hooks.
	 *
	 * @since  0.0.1
	 */
	public function hooks() {

		// Hook in our actions to the admin.

		add_action( 'admin_menu', array( $this, 'create_settings' ) );
		add_action( 'admin_init', array( $this, 'setup_sections' ) );
		add_action( 'admin_init', array( $this, 'setup_fields' ) );
		add_filter('plugin_action_links_' . $this->plugin->basename, [$this,'action_links']);

	}

	public function action_links($links){

		$links[] = '<a href="' . admin_url( 'admin.php?page=phone_validation_options' ) . '">Settings</a>';

		return $links;
	}

	public function create_settings() {
		$page_title = 'Phone Validation Options';
		$menu_title = 'Phone Validation';
		$capability = 'manage_options';
		$slug = 'phone_validation_options';
		$callback = array($this, 'settings_content');

		add_submenu_page('woocommerce',$page_title,$menu_title,$capability,$slug,$callback);
	}
	public function settings_content() { ?>
		<div class="wrap">
			<h1>Phone Validation Options</h1>
			<?php settings_errors(); ?>
			<form method="POST" action="options.php">
				<?php
					settings_fields( 'phone_validation_options' );
					do_settings_sections( 'phone_validation_options' );
					submit_button();
				?>
			</form>
		</div> <?php
	}
	public function setup_sections() {
		add_settings_section( 'phone_validation_options_section', 'Phone Validation Options', array(), 'phone_validation_options' );
	}
	public function setup_fields() {
		$fields = array(
			array(
				'label' => 'Enable Numverify',
				'id' => 'numverify',
				'type' => 'checkbox',
				'section' => 'phone_validation_options_section',
				'options' => array(
					'Yes' => 'Yes',
				),
				'desc' => 'Use Numverify service to validate that phone numbers entered are real ',
			),
			[
				'label' =>'Numverify api key',
				'id'=>'numverify_key',
				'type'=>'text',
				'section' => 'phone_validation_options_section'
			]
		);
		foreach( $fields as $field ){
			add_settings_field( $field['id'], $field['label'], array( $this, 'field_callback' ), 'phone_validation_options', $field['section'], $field );
			register_setting( 'phone_validation_options', $field['id'] );
		}
	}

	public static function is_active(){
		$option = get_option('numverify');

		return ($option && is_array($option) && $option[0]==='Yes');
	}
	public function field_callback( $field ) {
		$value = get_option( $field['id'] );
		switch ( $field['type'] ) {
			case 'radio':
			case 'checkbox':
				if( ! empty ( $field['options'] ) && is_array( $field['options'] ) ) {
					$options_markup = '';
					$iterator = 0;
					foreach( $field['options'] as $key => $label ) {
						$iterator++;
						$options_markup.= sprintf('<label for="%1$s_%6$s"><input id="%1$s_%6$s" name="%1$s[]" type="%2$s" value="%3$s" %4$s /> %5$s</label><br/>',
							$field['id'],
							$field['type'],
							$key,
							checked($value[array_search($key, $value, true)], $key, false),
							$label,
							$iterator
						);
					}
					printf( '<fieldset>%s</fieldset>',
						$options_markup
					);
				}
				break;
			default:
				printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />',
					$field['id'],
					$field['type'],
					$field['placeholder'],
					$value
				);
		}
		if( $desc = $field['desc'] ) {
			printf( '<p class="description">%s </p>', $desc );
		}
	}
}
