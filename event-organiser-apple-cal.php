<?php
/**
 * Plugin Name: Event Organiser ICS Feed for Apple Calendar
 * Description: Provides a Feed for Event Organiser Events that is compatible with Apple Calendar.
 * Version: 0.1
 * Author: Christian Wach
 * Author URI: https://haystack.co.uk
 * Plugin URI: https://github.com/christianwach/event-organiser-apple-cal
 * GitHub Plugin URI: https://github.com/christianwach/event-organiser-apple-cal
 * Text Domain: event-organiser-apple-cal
 * Domain Path: /languages
 *
 * @package Event_Organiser_Apple_Cal
 */

// Set our version here.
define( 'EVENT_ORGANISER_APPLE_CAL_VERSION', '0.1' );

// Store reference to this file.
if ( ! defined( 'EVENT_ORGANISER_APPLE_CAL_FILE' ) ) {
	define( 'EVENT_ORGANISER_APPLE_CAL_FILE', __FILE__ );
}

// Store URL to this plugin's directory.
if ( ! defined( 'EVENT_ORGANISER_APPLE_CAL_URL' ) ) {
	define( 'EVENT_ORGANISER_APPLE_CAL_URL', plugin_dir_url( EVENT_ORGANISER_APPLE_CAL_FILE ) );
}
// Store PATH to this plugin's directory.
if ( ! defined( 'EVENT_ORGANISER_APPLE_CAL_PATH' ) ) {
	define( 'EVENT_ORGANISER_APPLE_CAL_PATH', plugin_dir_path( EVENT_ORGANISER_APPLE_CAL_FILE ) );
}

/**
 * Event Organiser ICS Feed for Apple Calendar Class.
 *
 * A class that encapsulates this plugin's functionality.
 *
 * @since 0.1
 */
class Event_Organiser_Apple_Cal {

	/**
	 * Instance.
	 *
	 * @since 0.1
	 * @access private
	 * @var Event_Organiser_Apple_Cal $instance The plugin instance.
	 */
	private static $instance;

	/**
	 * The Apple iCal Feed class.
	 *
	 * @since 0.1
	 * @access public
	 * @var object $ical The Apple iCal Feed class.
	 */
	public $ical;

	/**
	 * The Shortcode class.
	 *
	 * @since 0.1
	 * @access public
	 * @var object $ical The Shortcode class.
	 */
	public $shortcode;

	/**
	 * Return the instance and optionally create one if it doesn't already exist.
	 *
	 * @since 0.1
	 *
	 * @return Event_Organiser_Apple_Cal The plugin instance.
	 */
	public static function instance_get() {

		// If it doesn't already exist.
		if ( ! isset( self::$instance ) ) {

			// Create it.
			self::$instance = new Event_Organiser_Apple_Cal();
			self::$instance->instance_setup();

		}

		// Return instance.
		return self::$instance;

	}

	/**
	 * Sets up this object.
	 *
	 * @since 0.1
	 */
	public function instance_setup() {

		// Always use translation files.
		add_action( 'plugins_loaded', [ $this, 'enable_translation' ] );

		// Initialise.
		add_action( 'plugins_loaded', [ $this, 'initialise' ] );

	}

	/**
	 * Perform tasks on plugin activation.
	 *
	 * @since 0.1
	 */
	public function activate() {
		flush_rewrite_rules();
	}

	/**
	 * Perform tasks on plugin deactivation.
	 *
	 * @since 0.1
	 */
	public function deactivate() {
		flush_rewrite_rules();
	}

	/**
	 * Initialises this object.
	 *
	 * @since 0.1
	 */
	public function initialise() {

		// Bail quietly if Event Organiser plugin is not present.
		if ( ! defined( 'EVENT_ORGANISER_VER' ) ) {
			return;
		}

		// Include files.
		$this->include_files();

		// Set up objects and references.
		$this->setup_objects();

		/**
		 * Broadcast that this plugin is now loaded.
		 *
		 * This action is used internally by this plugin to initialise its objects
		 * and ensures that all includes and setup has occurred beforehand.
		 *
		 * @since 0.1
		 */
		do_action( 'event_organiser_apple_cal_loaded' );

	}

	/**
	 * Include files.
	 *
	 * @since 0.1
	 */
	public function include_files() {

		// Load our class files.
		require EVENT_ORGANISER_APPLE_CAL_PATH . 'includes/class-eo-apple-cal-feed.php';
		require EVENT_ORGANISER_APPLE_CAL_PATH . 'includes/class-eo-apple-cal-shortcode.php';

	}

	/**
	 * Set up this plugin's objects.
	 *
	 * @since 0.1
	 */
	public function setup_objects() {

		// Only ever do this once.
		static $done;
		if ( isset( $done ) && $done === true ) {
			return;
		}

		// Instantiate objects.
		$this->ical = new Event_Organiser_Apple_Cal_Feed();
		$this->shortcode = new Event_Organiser_Apple_Cal_Shortcode();

		// We're done.
		$done = true;

	}

	/**
	 * Load translation files.
	 *
	 * A good reference on how to implement translation in WordPress:
	 *
	 * @see http://ottopress.com/2012/internationalization-youre-probably-doing-it-wrong/
	 *
	 * @since 0.1
	 */
	public function enable_translation() {

		// Load translations.
		// phpcs:ignore WordPress.WP.DeprecatedParameters.Load_plugin_textdomainParam2Found
		load_plugin_textdomain(
			'event-organiser-apple-cal', // Unique name.
			false, // Deprecated argument.
			dirname( plugin_basename( __FILE__ ) ) . '/languages/' // Relative path to translation files.
		);

	}

}

/**
 * Returns the Event_Organiser_Apple_Cal instance.
 *
 * Use this function like you would a global variable, except without needing to
 * declare the global.
 *
 * Example: $eoa = event_organiser_apple_cal();
 *
 * @since 0.1
 *
 * @return Event_Organiser_Apple_Cal The plugin instance.
 */
function event_organiser_apple_cal() {
	return Event_Organiser_Apple_Cal::instance_get();
}

// Boot Event_Organiser_Apple_Cal immediately.
event_organiser_apple_cal();

// Activation.
register_activation_hook( __FILE__, [ event_organiser_apple_cal(), 'activate' ] );

// Deactivation.
register_deactivation_hook( __FILE__, [ event_organiser_apple_cal(), 'deactivate' ] );
