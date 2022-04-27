<?php
/**
 * ICS Feed for Apple Calendar Class.
 *
 * Handles generating the ICS feed.
 *
 * @since 0.1
 *
 * @package Event_Organiser_Apple_Cal
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * ICS Feed for Apple Calendar Class.
 *
 * A class that encapsulates generating the ICS feed.
 *
 * @since 0.1
 */
class Event_Organiser_Apple_Cal_Feed {

	/**
	 * The name of our feed.
	 *
	 * @since 0.1
	 * @access public
	 * @var str $feed_name The name of the feed.
	 */
	public $feed_name = 'eo-apple';

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

		// Add a new feed.
		add_action( 'init', [ $this, 'feed_register' ] );

		// Filter the query later than Event Organiser.
		add_action( 'pre_get_posts', [ $this, 'pre_get_posts' ], 20 );

		// Filter whether the query is an event query.
		add_filter( 'eventorganiser_is_event_query', [ $this, 'is_event_query' ], 10, 3 );

		// Filter Event Organiser's template stack.
		add_filter( 'eventorganiser_template_stack', [ $this, 'template_stack' ] );

	}

	/**
	 * Register a new feed.
	 *
	 * @since 0.1
	 *
	 * @param bool $flush Flushes rewrite rules when set.
	 */
	public function feed_register( $flush = false ) {

		// Bail if EO doesn't allow feeds.
		if ( ! eventorganiser_get_option( 'feed' ) ) {
			return;
		}

		// Add our custom feed and its callback.
		add_feed( $this->feed_name, [ $this, 'feed_export' ] );

		// Maybe flush rules.
		if ( $flush ) {
			flush_rewrite_rules();
		}

	}

	/**
	 * Export an ICS feed that is compatible with Apple Calendar.
	 *
	 * @since 0.1
	 */
	public function feed_export() {

		// Bail if EO doesn't allow feeds.
		if ( ! eventorganiser_get_option( 'feed' ) ) {
			return;
		}

		// Bail if it's not our feed.
		if ( ! is_feed( $this->feed_name ) ) {
			return;
		}

		// Generate a sensible filename.
		$filename = urlencode( 'event-organiser-apple-cal_' . gmdate( 'Y-m-d' ) . '.ics' );

		// Collect output.
		ob_start();

		// File header.
		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename=' . $filename );
		header( 'Content-type: text/calendar; charset=' . get_option( 'blog_charset' ) . ';' );
		header( 'Pragma: 0' );
		header( 'Expires: 0' );

		// We filter Event Organiser's stack function to include our file.
		eo_locate_template( 'ical-apple.php', true, false );

		// Collect output and echo.
		$eventsical = ob_get_contents();
		ob_end_clean();
		echo $eventsical;
		exit();

	}

	/**
	 * Filter the query.
	 *
	 * @since 0.1
	 *
	 * @param WP_Query $query The query object.
	 */
	public function pre_get_posts( $query ) {

		// Bail if the query is for our feed.
		if ( ! $query->is_feed( $this->feed_name ) ) {
			return;
		}

		// Set post type.
		$query->set( 'post_type', 'event' );

		/*
		 * Handle posts per page for feeds bug.
		 *
		 * @see https://core.trac.wordpress.org/ticket/17853
		 */
		add_filter( 'post_limits', 'wp17853_eventorganiser_workaround' );
		$query->set( 'posts_per_page', -1 );

		// Grouping by needs to be by 'occurrence'.
		if ( $query->is_main_query() ) {
			$query->set( 'group_events_by', 'occurrence' );
		}

	}

	/**
	 * Filters whether the query is an event query.
	 *
	 * This should be `true` if the query is for events, `false` otherwise. The
	 * third parameter, `$exclusive` qualifies if this means 'query exclusively
	 * for events' or not. If `true` then this filter should return `true` only
	 * if the query is exclusively for events.
	 *
	 * @since 0.1
	 *
	 * @param bool $bool True if the query is an event query.
	 * @param WP_Query $query The WP_Query instance to check.
	 * @param bool $exclusive Whether the check if for queries exclusively for events.
	 * @return bool $bool True if the query is an event query.
	 */
	public function is_event_query( $bool, $query, $exclusive ) {

		// Test lifted from Event Organiser.
		if ( ( $query && $query->is_feed( $this->feed_name ) ) || is_feed( $this->feed_name ) ) {
			$bool = true;
		}

		// --<
		return $bool;

	}

	/**
	 * Filters the Event Organiser template stack.
	 *
	 * The directories are checked in the order in which they appear in this array.
	 * By default the array includes (in order)
	 *
	 *  - child theme directory
	 *  - parent theme directory
	 *  - `event-organiser/templates`
	 *
	 * @since 0.1
	 *
	 * @param array $stack Existing array of directories (absolute path).
	 * @return array $stack Modified array of directories (absolute path).
	 */
	public function template_stack( $stack ) {

		// Define path to our templates directory.
		$templates_dir = EVENT_ORGANISER_APPLE_CAL_PATH . 'templates';

		// Add it if not already present.
		if ( is_array( $stack ) && ! in_array( $templates_dir, $stack ) ) {
			$stack[] = $templates_dir;
		}

		// --<
		return $stack;

	}

}
