<?php
/*
Plugin Name:  DTRT Test
Plugin URI:   https://github.com/dotherightthing/wpdtrt-plugin
Description:  Base classes for a WordPress plugin and associated shortcodes and widgets.
Version:      1.1.10
Author:       Dan Smith
Author URI:   https://profiles.wordpress.org/dotherightthingnz
License:      GPLv2 or later
License URI:  http://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  wpdtrt-test
Domain Path:  /languages
*/

require_once plugin_dir_path( __FILE__ ) . "vendor/autoload.php";

/**
 * Constants
 * WordPress makes use of the following constants when determining the path to the content and plugin directories.
 * These should not be used directly by plugins or themes, but are listed here for completeness.
 * WP_CONTENT_DIR  // no trailing slash, full paths only
 * WP_CONTENT_URL  // full url
 * WP_PLUGIN_DIR  // full path, no trailing slash
 * WP_PLUGIN_URL  // full url, no trailing slash
 *
 * WordPress provides several functions for easily determining where a given file or directory lives.
 * Always use these functions in your plugins instead of hard-coding references to the wp-content directory
 * or using the WordPress internal constants.
 * plugins_url()
 * plugin_dir_url()
 * plugin_dir_path()
 * plugin_basename()
 *
 * @link https://codex.wordpress.org/Determining_Plugin_and_Content_Directories#Constants
 * @link https://codex.wordpress.org/Determining_Plugin_and_Content_Directories#Plugins
 */

if( ! defined( 'WPDTRT_TEST_VERSION' ) ) {
/**
 * Plugin version.
 *
 * WP provides get_plugin_data(), but it only works within WP Admin,
 * so we define a constant instead.
 *
 * @example $plugin_data = get_plugin_data( __FILE__ ); $plugin_version = $plugin_data['Version'];
 * @link https://wordpress.stackexchange.com/questions/18268/i-want-to-get-a-plugin-version-number-dynamically
 *
 * @since     1.0.0
 * @version   1.0.0
 */
  define( 'WPDTRT_TEST_VERSION', '1.1.8' );
}

if( ! defined( 'WPDTRT_TEST_PATH' ) ) {
/**
 * Plugin directory filesystem path.
 *
 * @param string $file
 * @return The filesystem directory path (with trailing slash)
 *
 * @link https://developer.wordpress.org/reference/functions/plugin_dir_path/
 * @link https://developer.wordpress.org/plugins/the-basics/best-practices/#prefix-everything
 *
 * @since     1.0.0
 * @version   1.0.0
 */
  define( 'WPDTRT_TEST_PATH', plugin_dir_path( __FILE__ ) );
}

if( ! defined( 'WPDTRT_TEST_URL' ) ) {
/**
 * Plugin directory URL path.
 *
 * @param string $file
 * @return The URL (with trailing slash)
 *
 * @link https://codex.wordpress.org/Function_Reference/plugin_dir_url
 * @link https://developer.wordpress.org/plugins/the-basics/best-practices/#prefix-everything
 *
 * @since     1.0.0
 * @version   1.0.0
 */
  define( 'WPDTRT_TEST_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * Include plugin logic
 *
 * @since     1.0.0
 * @version   1.0.0
 */

  // base class
  // redundant, but includes the composer-generated autoload file if not already included
  require_once(WPDTRT_TEST_PATH . 'index.php');

  // sub classes
  require_once(WPDTRT_TEST_PATH . 'src/class-wpdtrt-test-plugin.php');

  // log & trace helpers
  global $debug;
  $debug = new DoTheRightThing\WPDebug\Debug;

  /**
   * Plugin initialisaton
   *
   * We call init before widget_init so that the plugin object properties are available to it.
   * If widget_init is not working when called via init with priority 1, try changing the priority of init to 0.
   * init: Typically used by plugins to initialize. The current user is already authenticated by this time.
   * └─ widgets_init: Used to register sidebars. Fired at 'init' priority 1 (and so before 'init' actions with priority ≥ 1!)
   *
   * @see https://wp-mix.com/wordpress-widget_init-not-working/
   * @see https://codex.wordpress.org/Plugin_API/Action_Reference
   * @todo Add a constructor function to WPDTRT_Blocks_Plugin, to explain the options array
   */
  function wpdtrt_test_init() {
    // pass object reference between classes via global
    // because the object does not exist until the WordPress init action has fired
    global $wpdtrt_test_plugin;

    /**
     * Admin settings
     *
     * Changed to $taxonomy_options and retained for legacy support - may not be reqd
     */
    $plugin_options = array();

    /**
     * All options available to Widgets and Shortcodes
     */
    $instance_options = array(
      'term_id' => array(
        'type' => 'number',
        'label' => esc_html__('Term ID', 'wpdtrt-test'),
      ),
      'text_before' => array(
        'type' => 'text',
        'label' => esc_html__('Text before', 'wpdtrt-test'),
      ),
      'text_after' => array(
        'type' => 'text',
        'label' => esc_html__('Text after', 'wpdtrt-test'),
      ),
      'posttype' => array(
        'type' => 'text',
        'label' => esc_html__('Custom Post Type', 'wpdtrt-test'),
        'tip' => esc_html__('Used for the previous/next navigation bar', 'wpdtrt-test')
      ),
      /*
      'posttype' => array(
        'type' => 'text',
        'label' => esc_html__('Post type', 'wpdtrt-test'),
      ),
      'taxonomy' => array(
        'type' => 'text',
        'label' => esc_html__('Taxonomy', 'wpdtrt-test'),
        'tip' => 'tours'
      )
      */
    );

    $wpdtrt_test_plugin = new WPDTRT_Test_Plugin(
      array(
        'url' => WPDTRT_TEST_URL,
        'prefix' => 'wpdtrt_test',
        'slug' => 'wpdtrt-test',
        'menu_title' => __('Test', 'wpdtrt-test'),
        'developer_prefix' => 'DTRT',
        'path' => WPDTRT_TEST_PATH,
        'messages' => array(
          'loading' => __('Loading latest data...', 'wpdtrt-test'),
          'success' => __('settings successfully updated', 'wpdtrt-test'),
          'insufficient_permissions' => __('Sorry, you do not have sufficient permissions to access this page.', 'wpdtrt-test'),
          'options_form_title' => __('General Settings', 'wpdtrt-test'),
          'options_form_description' => __('Please enter your preferences.', 'wpdtrt-test'),
          'no_options_form_description' => __('There aren\'t currently any options.', 'wpdtrt-test'),
          'options_form_submit' => __('Save Changes', 'wpdtrt-test'),
          'post_terms_missing' => __('Test plugin error: Please assign all three "Tour" levels to $post id = N', 'wpdtrt-test'),
          'noscript_warning' => __('Please enable JavaScript', 'wpdtrt-test'),
        ),
        'plugin_options' => $plugin_options,
        'instance_options' => $instance_options,
        'version' => WPDTRT_TEST_VERSION,
      )
    );
  }

  add_action( 'init', 'wpdtrt_test_init', 0 );

  /**
   * Register functions to be run when the plugin is activated.
   *
   * @see https://codex.wordpress.org/Function_Reference/register_activation_hook
   *
   * @since     0.6.0
   * @version   1.0.0
   */
  function wpdtrt_test_activate() {
    // $this->set_rewrite_rules()
    flush_rewrite_rules();
  }

  register_activation_hook(__FILE__, 'wpdtrt_test_activate');

  /**
   * Register functions to be run when the plugin is deactivated.
   *
   * (WordPress 2.0+)
   *
   * @see https://codex.wordpress.org/Function_Reference/register_deactivation_hook
   *
   * @since     0.6.0
   * @version   1.0.0
   */
  function wpdtrt_test_deactivate() {
    flush_rewrite_rules();
  }

  register_deactivation_hook(__FILE__, 'wpdtrt_test_deactivate');

?>