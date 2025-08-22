<?php
/**
 * Shortcode for Apple Calendar Class.
 *
 * Handles the shortcode for linking to the ICS feed.
 *
 * @since 0.1
 *
 * @package Event_Organiser_Apple_Cal
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Shortcode for Apple Calendar Class.
 *
 * A class that provides the shortcode for linking to the ICS feed.
 *
 * @since 0.1
 */
class Event_Organiser_Apple_Cal_Shortcode {

	/**
	 * The name of the shortcode.
	 *
	 * @since 0.1
	 * @access public
	 * @var string
	 */
	public $shortcode_name = 'eo_apple_subscribe';

	/**
	 * Initialises this object.
	 *
	 * @since 0.1
	 */
	public function __construct() {

		// Register hooks when plugin is loaded.
		add_action( 'event_organiser_apple_cal_loaded', [ $this, 'register_hooks' ] );

	}

	/**
	 * Register hooks.
	 *
	 * @since 0.1
	 */
	public function register_hooks() {

		// Register shortcode.
		add_action( 'init', [ $this, 'shortcode_register' ] );

	}

	/**
	 * Register our shortcode.
	 *
	 * @since 0.1
	 */
	public function shortcode_register() {

		// Create Data Summary shortcode.
		add_shortcode( $this->shortcode_name, [ $this, 'shortcode_render' ] );

	}

	/**
	 * Add Data Summary to a page/post via a shortcode.
	 *
	 * @since 0.1
	 *
	 * @param array  $atts The saved shortcode attributes.
	 * @param string $content The enclosed content of the shortcode.
	 * @return string $markup The HTML-formatted markup.
	 */
	public function shortcode_render( $atts, $content = null ) {

		// Init markup.
		$markup = '';

		// Bail if this is a feed.
		if ( is_feed() ) {
			return $markup;
		}

		// Declare defaults.
		$defaults = [
			'title' => __( 'Subscribe in Apple Calendar', 'event-organiser-apple-cal' ),
			'class' => '',
			'id'    => '',
			'style' => '',
		];

		// Parse attributes.
		$data = shortcode_atts( $defaults, $atts, $this->shortcode_name );

		// Get reference to plugin.
		$plugin = event_organiser_apple_cal();

		// Get Feed URL.
		$url = get_feed_link( $plugin->ical->feed_name );

		// Format URL for auto-subscription.
		$url = preg_replace( '/^http(s?):/i', 'webcal:', $url );

		// Build anchor attributes.
		$title = ! empty( $data['title'] ) ? 'title="' . esc_attr( $data['title'] ) . '"' : '';
		$id    = ! empty( $data['id'] ) ? 'id="' . esc_attr( $data['id'] ) . '"' : '';
		$class = ! empty( $data['class'] ) ? 'class="' . esc_attr( $data['class'] ) . '"' : '';
		$style = ! empty( $data['style'] ) ? 'style="' . esc_attr( $data['style'] ) . '"' : '';

		// Build anchor.
		$markup = '<a href="' . $url . '" target="_blank" ' . $title . ' ' . $id . ' ' . $class . ' ' . $style . '>' . $content . '</a>';

		// --<
		return $markup;

	}

}
