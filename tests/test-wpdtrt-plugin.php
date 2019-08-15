<?php
/**
 * Unit tests, using PHPUnit, wp-cli, WP_UnitTestCase.
 *  These tests are run from child plugins.
 *
 * @package DTRT Test
 */

/**
 * WP_UnitTestCase unit tests for wpdtrt_plugin_boilerplate
 */
class PluginTest extends WP_UnitTestCase {

	/**
	 * Compare two HTML fragments.
	 *
	 * @param string $expected Expected value.
	 * @param string $actual Actual value.
	 * @param string $error_message Message to show when strings don't match.
	 * @uses https://stackoverflow.com/a/26727310/6850747
	 */
	protected function assertEqualHtml( $expected, $actual, $error_message ) {
		$from = [ '/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s', '/> </s' ];
		$to   = [ '>', '<', '\\1', '><' ];
		$this->assertEquals(
			preg_replace( $from, $to, $expected ),
			preg_replace( $from, $to, $actual ),
			$error_message
		);
	}

	/**
	 * SetUp.
	 * Automatically called by PHPUnit before each test method is run.
	 */
	public function setUp() {
		// Make the factory objects available.
		parent::setUp();

		$this->mock_data();
	}

	/**
	 * TearDown.
	 * Automatically called by PHPUnit after each test method is run.
	 */
	public function tearDown() {

		global $wpdtrt_test_plugin;

		parent::tearDown();

		wp_delete_post( $this->post_id_1, true );

		// remove any previously saved options.
		$wpdtrt_test_plugin->unset_options();
	}

	/**
	 * Create post
	 *
	 * @param array $options Options [$post_title, $post_date, $post_content].
	 * @return number $post_id
	 * @see https://developer.wordpress.org/reference/functions/wp_insert_post/
	 * @see https://wordpress.stackexchange.com/questions/37163/proper-formatting-of-post-date-for-wp-insert-post
	 * @see https://codex.wordpress.org/Function_Reference/wp_update_post
	 */
	public function create_post( $options ) {

		$post_title   = null;
		$post_date    = null;
		$post_content = null;

		extract( $options, EXTR_IF_EXISTS );

		$post_id = $this->factory->post->create([
			'post_title'   => $post_title,
			'post_date'    => $post_date,
			'post_content' => $post_content,
			'post_type'    => 'post',
			'post_status'  => 'publish',
		]);

		return $post_id;
	}

	/**
	 * ===== Mock Data =====
	 */
	public function mock_data() {

		// Post (for testing manually entered, naked shortcode).
		$this->post_id_1 = $this->create_post( array(
			'post_title'   => 'DTRT Test shortcode test',
			'post_content' => '[wpdtrt_test_shortcode]Text to hide[/wpdtrt_test_shortcode]',
		));

		$this->plugin_option_types = array(
			'checkbox_input' => array(
				'type'  => 'checkbox',
				'label' => __( 'Field label', 'text-domain' ),
				'tip'   => __( 'Helper text', 'text-domain' ),
			),
			'file_input'     => array(
				'type'  => 'file',
				'label' => __( 'Field label', 'text-domain' ),
				'tip'   => __( 'Helper text', 'text-domain' ),
			),
			'number_input'   => array(
				'type'  => 'number',
				'label' => __( 'Field label', 'text-domain' ),
				'size'  => 10,
				'tip'   => __( 'Helper text', 'text-domain' ),
			),
			'password_input' => array(
				'type'  => 'password',
				'label' => __( 'Field label', 'text-domain' ),
				'size'  => 10,
				'tip'   => __( 'Helper text', 'text-domain' ),
			),
			'select_input'   => array(
				'type'    => 'select',
				'label'   => __( 'Field label', 'fieldname' ),
				'options' => array(
					'option1value' => array(
						'text' => __( 'Label for option 1', 'text-domain' ),
					),
					'option2value' => array(
						'text' => __( 'Label for option 2', 'text-domain' ),
					),
				),
				'tip'     => __( 'Helper text', 'text-domain' ),
			),
			'text_input'     => array(
				'type'  => 'text',
				'label' => __( 'Field label', 'text-domain' ),
				'size'  => 10,
				'tip'   => __( 'Helper text', 'text-domain' ),
			),
		);

		$this->plugin_options_config_novalues = array(
			'google_static_maps_api_key' => array(
				'type'  => 'text',
				'label' => __( 'Google Static Maps API Key', 'wpdtrt-test' ),
				'size'  => 50,
				'tip'   => __( 'https://developers.google.com/maps/documentation/maps-static/get-api-key', 'wpdtrt-test' ),
			),
		);

		$this->plugin_options_user_values = array(
			'google_static_maps_api_key' => array(
				'type'  => 'text',
				'label' => __( 'Google Static Maps API Key', 'wpdtrt-test' ),
				'size'  => 50,
				'tip'   => __( 'https://developers.google.com/maps/documentation/maps-static/get-api-key', 'wpdtrt-test' ),
				'value' => 'abc12345',
			),
		);

		$this->all_options_fallback = array(
			'plugin_options'      => array(),
			'plugin_data'         => array(),
			'plugin_data_options' => array(),
			'instance_options'    => array(),
			'plugin_dependencies' => array(),
		);

		$this->all_options_config = array(
			'plugin_options'      => array(
				'google_static_maps_api_key' => array(
					'type'  => 'text',
					'label' => 'Google Static Maps API Key',
					'size'  => 50,
					'tip'   => __( 'https://developers.google.com/maps/documentation/maps-static/get-api-key', 'wpdtrt-test' ),
				),
			),
			'plugin_data'         => array(),
			'plugin_data_options' => array(
				'force_refresh' => 1,
			),
			'instance_options'    => array(),
			'plugin_dependencies' => array(),
		);

		$this->all_options_user = array(
			'plugin_options'      => array(
				'google_static_maps_api_key' => array(
					'type'  => 'text',
					'label' => 'Google Static Maps API Key',
					'size'  => 50,
					'tip'   => __( 'https://developers.google.com/maps/documentation/maps-static/get-api-key', 'wpdtrt-test' ),
					'value' => 'abc12345',
				),
			),
			'plugin_data'         => array(),
			'plugin_data_options' => array(
				'force_refresh' => 1,
			),
			'instance_options'    => array(),
			'plugin_dependencies' => array(),
		);

		$this->mock_plugin_dependency_old = array(
			'name'         => 'DTRT Content Sections',
			'slug'         => 'wpdtrt-contentsections',
			'source'       => 'https://github.com/dotherightthing/wpdtrt-contentsections/releases/download/0.0.1/release.zip',
			'version'      => '0.0.1',
			'external_url' => 'https://github.com/dotherightthing/wpdtrt-contentsections',
			'required'     => true,
		);

		$this->mock_plugin_dependency_new = array(
			'name'         => 'DTRT Content Sections',
			'slug'         => 'wpdtrt-contentsections',
			'source'       => 'https://github.com/dotherightthing/wpdtrt-contentsections/releases/download/0.0.2/release.zip',
			'version'      => '0.0.2',
			'external_url' => 'https://github.com/dotherightthing/wpdtrt-contentsections',
			'required'     => true,
		);
	}

	/**
	 * ===== Tests =====
	 */

	/**
	 * Test that the raw config is saved as-is, sans values.
	 *  For each option, the 'value' attribute is deliberately omitted,
	 *  this is to aid the checking of this value by helper_get_default_value().
	 *  If the value was set to '' by default,
	 *  it could erase a user value when the new and old options were merged -
	 *  or, if blank values were ignored
	 *  it would prevent the user from erasing values they no longer required
	 *
	 * @see https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/84
	 */
	public function test__set_plugin_options() {

		global $wpdtrt_test_plugin;

		// save the raw config.
		$wpdtrt_test_plugin->set_plugin_options( $this->plugin_options_config_novalues, true );

		// get config + user values (none).
		$plugin_options = $wpdtrt_test_plugin->get_plugin_options();

		// assertions.
		$this->assertArrayHasKey(
			'type',
			$plugin_options['google_static_maps_api_key']
		);

		$this->assertEquals(
			'text',
			$plugin_options['google_static_maps_api_key']['type']
		);

		$this->assertArrayHasKey(
			'label',
			$plugin_options['google_static_maps_api_key']
		);

		$this->assertEquals(
			'Google Static Maps API Key',
			$plugin_options['google_static_maps_api_key']['label']
		);

		$this->assertArrayHasKey(
			'size',
			$plugin_options['google_static_maps_api_key']
		);

		$this->assertEquals(
			'50',
			$plugin_options['google_static_maps_api_key']['size']
		);

		$this->assertArrayHasKey(
			'tip',
			$plugin_options['google_static_maps_api_key']
		);

		$this->assertEquals(
			'https://developers.google.com/maps/documentation/maps-static/get-api-key',
			$plugin_options['google_static_maps_api_key']['tip']
		);

		$this->assertArrayNotHasKey(
			'value',
			$plugin_options['google_static_maps_api_key'],
			'The raw config should exclude user values'
		);
	}

	/**
	 * Test that the user values are correctly merged into the config
	 *
	 * @see https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/84
	 */
	public function test__set_plugin_option_values() {

		global $wpdtrt_test_plugin;

		// save the raw config.
		$wpdtrt_test_plugin->set_plugin_options( $this->plugin_options_config_novalues, true );

		// save the user values.
		$wpdtrt_test_plugin->set_plugin_options( $this->plugin_options_user_values );

		// get config + user values.
		$plugin_options = $wpdtrt_test_plugin->get_plugin_options();

		// assertions.
		$this->assertArrayHasKey(
			'type',
			$plugin_options['google_static_maps_api_key']
		);

		$this->assertEquals(
			'text',
			$plugin_options['google_static_maps_api_key']['type']
		);

		$this->assertArrayHasKey(
			'label',
			$plugin_options['google_static_maps_api_key']
		);

		$this->assertEquals(
			'Google Static Maps API Key',
			$plugin_options['google_static_maps_api_key']['label']
		);

		$this->assertArrayHasKey(
			'size',
			$plugin_options['google_static_maps_api_key']
		);

		$this->assertEquals(
			'50',
			$plugin_options['google_static_maps_api_key']['size']
		);

		$this->assertArrayHasKey(
			'tip',
			$plugin_options['google_static_maps_api_key']
		);

		$this->assertEquals(
			'https://developers.google.com/maps/documentation/maps-static/get-api-key',
			$plugin_options['google_static_maps_api_key']['tip']
		);

		$this->assertArrayHasKey(
			'value',
			$plugin_options['google_static_maps_api_key'],
			'The raw config should exclude user values'
		);

		$this->assertEquals(
			'abc12345',
			$plugin_options['google_static_maps_api_key']['value']
		);
	}

	/**
	 * Test update_option()
	 * This is the WordPress function which adds the merged data to the options table
	 *
	 * @see https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/84
	 */
	public function test__update_option__get_option() {

		global $wpdtrt_test_plugin;

		/**
		* Testing set_options()
		*/

		$options = array_merge( $this->all_options_config, $this->all_options_user );

		update_option( $wpdtrt_test_plugin->get_prefix(), $options, null );

		/**
		* Testing get_options()
		*/

		$options = get_option( $wpdtrt_test_plugin->get_prefix(), $this->all_options_fallback );

		$this->assertArrayHasKey(
			'plugin_options',
			$options
		);

		$this->assertArrayHasKey(
			'google_static_maps_api_key',
			$options['plugin_options']
		);

		$this->assertArrayHasKey(
			'plugin_data',
			$options
		);

		$this->assertArrayHasKey(
			'plugin_data_options',
			$options
		);

		$this->assertArrayHasKey(
			'instance_options',
			$options
		);

		$this->assertArrayHasKey(
			'plugin_dependencies',
			$options
		);

		// passes.
		$this->assertArrayHasKey(
			'value',
			$options['plugin_options']['google_static_maps_api_key'],
			'When the options are saved to the database and then retrieved, new values are lost'
		);
	}

	/**
	 * Test that the correct field 'type'
	 * is passed to the Plugin's render_form_element()
	 * as it us used to determine the include() name
	 * via options.php
	 *
	 * @see https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/84
	 */
	public function test__render_form_element() {

		foreach ( $this->plugin_options_user_values as $name => $attributes ) {

			$this->assertArrayHasKey(
				'type',
				$attributes
			);

			$this->assertEquals(
				'text',
				$attributes['type']
			);
		}
	}

	/**
	 * Test whether multiple calls to set and get plugin options
	 * result in duplicate keys
	 *
	 * @see https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/84
	 */
	public function test__set_plugin_options__get_plugin_options__multi() {

		global $wpdtrt_test_plugin;

		// when the page is first loaded,
		// we get the plugin options out of the coded config
		//
		// 1.
		$wpdtrt_test_plugin->set_plugin_options( $this->plugin_options_config_novalues, true );
		$plugin_options = $wpdtrt_test_plugin->get_plugin_options();

		// 2.
		$wpdtrt_test_plugin->set_plugin_options( $plugin_options );
		$plugin_options = $wpdtrt_test_plugin->get_plugin_options();

		// 3.
		$wpdtrt_test_plugin->set_plugin_options( $plugin_options );
		$plugin_options = $wpdtrt_test_plugin->get_plugin_options();

		foreach ( $plugin_options as $name => $attributes ) {

			$this->assertArrayHasKey(
				'type',
				$attributes
			);

			$this->assertEquals(
				'text',
				$attributes['type'],
				'A string is expected for the field type'
			);
		}
	}

	/**
	 * Test that form element values are correctly set
	 * when a form element is rendered
	 * for a plugin option which doesn't have a value attribute yet
	 *
	 * @see views/form-element-checkbox.php
	 * @see views/form-element-file.php
	 * @see views/form-element-number.php
	 * @see views/form-element-password.php
	 * @see views/form-element-select.php
	 * @see views/form-element-text.php
	 * @todo test HTML output to ensure that default values translate to semantic HTML
	 */
	public function test__helper_get_default_value() {

		global $wpdtrt_test_plugin;

		$wpdtrt_test_plugin->set_plugin_options( $this->plugin_option_types, true );
		$stored_plugin_options = $wpdtrt_test_plugin->get_plugin_options();

		$this->assertEquals(
			$wpdtrt_test_plugin->helper_get_default_value( $stored_plugin_options['checkbox_input']['type'] ),
			'',
			'When a plugin option does not have a value yet, a checkbox input should output an empty string (not checked)'
		);

		$this->assertNull(
			$wpdtrt_test_plugin->helper_get_default_value( $stored_plugin_options['file_input']['type'] ),
			'When a plugin option does not have a value yet, a file input should output NULL (nothing selected)'
		);

		$this->assertNull(
			$wpdtrt_test_plugin->helper_get_default_value( $stored_plugin_options['number_input']['type'] ),
			'When a plugin option does not have a value yet, a number input should output NULL [to check]'
		);

		$this->assertEquals(
			$wpdtrt_test_plugin->helper_get_default_value( $stored_plugin_options['password_input']['type'] ),
			'',
			'When a plugin option does not have a value yet, a password input should output an empty string'
		);

		$this->assertNull(
			$wpdtrt_test_plugin->helper_get_default_value( $stored_plugin_options['select_input']['type'] ),
			'When a plugin option does not have a value yet, a select input should output NULL (nothing selected)'
		);

		$this->assertEquals(
			$wpdtrt_test_plugin->helper_get_default_value( $stored_plugin_options['text_input']['type'] ),
			'',
			'When a plugin option does not have a value yet, a text input should output an empty string'
		);
	}

	/**
	 * Test that the options page fields display the correct attributes
	 *
	 * @see https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/issues/84
	 */
	public function todo__test__render_options_page_field() {
		// .
	}

	/**
	 * Test that get_api_data() returns data,
	 *  and that this is stored and retrievable.
	 *
	 * @see https://github.com/dotherightthing/wpdtrt-plugin-boilerplate/wiki/Data:-Loading-from-an-API#data-is-stored-in-an-associative-array
	 */
	public function test__get_api_data() {
		global $wpdtrt_test_plugin;

		$endpoint = 'http://jsonplaceholder.typicode.com/photos/1';
		$data     = $wpdtrt_test_plugin->get_api_data( $endpoint );

		$this->assertTrue(
			is_array( $data ),
			'Expected JSON data to be converted to an associative array'
		);

		$this->assertArrayHasKey(
			'id',
			$data,
			'Expected demo API data to contain an id key'
		);

		$this->assertEquals(
			$data,
			$wpdtrt_test_plugin->get_plugin_data(),
			'Expected API data to be stored as plugin_data'
		);
	}

	/**
	 * Test that setting a single dependency,
	 *  will supercede an outdated duplicate
	 */
	public function test_set_plugin_dependency() {

		global $wpdtrt_test_plugin;

		$wpdtrt_test_plugin->set_plugin_dependency( $this->mock_plugin_dependency_old );

		$wpdtrt_test_plugin->set_plugin_dependency( $this->mock_plugin_dependency_new );

		$new_plugin_dependencies = $wpdtrt_test_plugin->get_plugin_dependencies();

		// reindex array (only required in this test).
		$new_plugin_dependencies = array_values( $new_plugin_dependencies );

		$this->assertEquals(
			'0.0.1',
			$this->mock_plugin_dependency_old['version'],
			'Expected old plugin dependency version'
		);

		$this->assertEquals(
			'0.0.2',
			$new_plugin_dependencies[0]['version'],
			'Expected old plugin dependency to be replaced with new version'
		);
	}

	/**
	 * Test shortcode
	 */
	public function test_shortcode() {

		$this->go_to(
			get_post_permalink( $this->post_id_1 )
		);

		// https://stackoverflow.com/a/22270259/6850747.
		$content = apply_filters( 'the_content', get_post_field( 'post_content', $this->post_id_1 ) );

		// default value is '' = unchecked = show.
		$this->assertEqualHtml(
			'<span class="wpdtrt-test wpdtrt-test_show">Text to hide</span>',
			trim( do_shortcode( trim( do_shortcode( $content ) ) ) ),
			'wpdtrt_text_shortcode does not wrap text'
		);
	}

	/**
	 * Test that the Settings (options) page loads without errors.
	 */
	public function __test_settings_page() {

		global $wpdtrt_test_plugin;

		$this->go_to(
			get_admin_url() . 'options-general.php?page=' . $wpdtrt_test_plugin->get_slug()
		);
	}

	/**
	 * Test that plugin dependencies are correctly loaded from composer-tgmpa.json
	 */
	public function test_get_wp_composer_dependencies() {
		$composer_json = dirname( __FILE__ ) . '/data/composer-tgmpa.json';

		$this->assertFileExists(
			$composer_json,
			'File does not exist at this location'
		);

		$composer_dependencies = WPDTRT_Test_Plugin::get_wp_composer_dependencies( $composer_json );

		$this->assertNotEmpty(
			$composer_dependencies,
			'Composer dependencies not retrieved'
		);

		$this->assertTrue(
			is_array( $composer_dependencies ),
			'Expected Composer dependencies to be converted to an associative array'
		);

		$this->assertEquals(
			array(
				array(
					'name'         => 'DTRT Content Sections (0.2.2)',
					'slug'         => 'wpdtrt-contentsections',
					'required'     => true,
					'file'         => 'wpdtrt-contentsections.php',
					'source'       => 'https://github.com/dotherightthing/wpdtrt-contentsections/releases/download/0.2.2/release.zip',
					'version'      => '0.2.2',
					'external_url' => 'https://github.com/dotherightthing/wpdtrt-contentsections',
					'vendor'       => 'dotherightthing',
				),
				array(
					'name'     => 'Better Anchor Links (1.7.*)',
					'slug'     => 'better-anchor-links',
					'required' => true,
					'file'     => 'auto-anchor-list.php',
					'version'  => '1.7.*',
					'vendor'   => 'wpackagist-plugin',
				),
			),
			$composer_dependencies
		);
	}

	/**
	 * Test that TGMPA dependencies are correctly registered
	 */
	public function test__set_wp_composer_dependencies_tgmpa() {
		global $wpdtrt_test_plugin;

		$composer_json               = dirname( __FILE__ ) . '/data/composer-tgmpa.json';
		$updated_plugin_dependencies = $wpdtrt_test_plugin->set_wp_composer_dependencies_tgmpa( $composer_json );

		$this->assertNotCount(
			0,
			$updated_plugin_dependencies,
			'No plugin dependencies returned, path to composer-tgmpa.json is bad'
		);

		$this->assertEquals(
			array(
				array(
					'name'         => 'DTRT Content Sections (0.2.2)',
					'slug'         => 'wpdtrt-contentsections',
					'required'     => true,
					'version'      => '0.2.2',
					'source'       => 'https://github.com/dotherightthing/wpdtrt-contentsections/releases/download/0.2.2/release.zip',
					'external_url' => 'https://github.com/dotherightthing/wpdtrt-contentsections',
				),
				array(
					'name'     => 'Better Anchor Links (1.7.*)',
					'slug'     => 'better-anchor-links',
					'required' => true,
					'version'  => '1.7.*',
				),
			),
			$updated_plugin_dependencies,
			'TGMPA plugin dependencies not updated correctly'
		);
	}

	/**
	 * Test that no TGMPA dependencies does not cause an error
	 */
	public function test__not_set_wp_composer_dependencies_tgmpa() {
		global $wpdtrt_test_plugin;

		$composer_json               = dirname( __FILE__ ) . '/data/composer-not-tgmpa.json';
		$updated_plugin_dependencies = $wpdtrt_test_plugin->set_wp_composer_dependencies_tgmpa( $composer_json );

		$this->assertEquals(
			array(),
			$updated_plugin_dependencies,
			'TGMPA plugin dependencies not updated correctly'
		);
	}

	/**
	 * Test static method get_wp_composer_dependencies_wpunit
	 */
	public function test__get_wp_composer_dependencies_wpunit() {
		$composer_json                    = dirname( __FILE__ ) . '/data/composer-tgmpa.json';
		$composer_dependencies            = WPDTRT_Test_Plugin::get_wp_composer_dependencies( $composer_json );
		$composer_dependencies_to_require = WPDTRT_Test_Plugin::get_wp_composer_dependencies_wpunit( $composer_dependencies );

		$this->assertEquals(
			array(
				dirname( dirname( __FILE__ ) ) . '/vendor/dotherightthing/wpdtrt-contentsections/wpdtrt-contentsections.php',
				dirname( dirname( __FILE__ ) ) . '/wp-content/plugins/better-anchor-links/auto-anchor-list.php',
			),
			$composer_dependencies_to_require,
			'WP Unit plugin dependencies not correct'
		);
	}

	/**
	 * Test that no TGMPA dependencies does not cause an error
	 */
	public function test__not_get_wp_composer_dependencies_wpunit() {
		$composer_json                    = dirname( __FILE__ ) . '/data/composer-not-tgmpa.json';
		$composer_dependencies            = WPDTRT_Test_Plugin::get_wp_composer_dependencies( $composer_json );
		$composer_dependencies_to_require = WPDTRT_Test_Plugin::get_wp_composer_dependencies_wpunit( $composer_dependencies );

		$this->assertEquals(
			array(),
			$composer_dependencies_to_require,
			'WP Unit plugin dependencies not correct'
		);
	}
}
