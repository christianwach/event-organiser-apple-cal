<?php
/**
 * ICS Feed for Apple Calendar.
 *
 * This is a modified clone of Event Organiser's 'ical.php' template file.
 *
 * @package Event_Organiser_Apple_Cal
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

echo "BEGIN:VCALENDAR\r\n";
echo "VERSION:2.0\r\n";
echo 'PRODID:-//' . esc_html( get_bloginfo( 'name' ) ) . "//NONSGML Events//EN\r\n";
echo "CALSCALE:GREGORIAN\r\n";
if ( ! is_single() ) {
	echo 'X-WR-CALNAME:' . esc_html( get_bloginfo( 'name' ) ) . " - Events\r\n";
}
echo 'X-ORIGINAL-URL:' . esc_url( get_post_type_archive_link( 'event' ) ) . "\r\n";
echo 'X-WR-CALDESC:' . esc_html( get_bloginfo( 'name' ) ) . " - Events\r\n";

// Loop through events.
if ( have_posts() ) :

	$now     = new DateTime();
	$dtstamp = eo_format_date( 'now', 'Ymd\THis\Z' );

	// Set $tz if a timezone is specified - this does not include GMT offsets.
	$timezone     = ( get_option( 'timezone_string' ) ? eo_get_blog_timezone() : false );
	$utc_timezone = new DateTimeZone( 'UTC' );

	$earliest_date = false;
	$latest_date   = false;

	while ( have_posts() ) :

		the_post();

		global $post;

		// If event has no corresponding row in events table then skip it.
		if ( ! isset( $post->event_id ) || -1 === (int) $post->event_id ) {
			continue;
		}

		// Get basic data.
		$schedule_data = eo_get_event_schedule();
		$created_date  = get_post_time( 'Ymd\THis\Z', true );
		$modified_date = get_post_modified_time( 'Ymd\THis\Z', true );

		// Set defaults for start and end.
		$start = eo_get_the_start( DATETIMEOBJ, $post->ID, $post->occurrence_id );
		$end   = eo_get_the_end( DATETIMEOBJ, $post->ID, $post->occurrence_id );

		/*
		$start = $schedule_data['start'];
		$end = $schedule_data['end'];

		// Modified recurrences need special handling.
		if ( ! empty( $schedule_data['exclude'] ) OR ! empty( $schedule_data['include'] ) ) :
			$start = eo_get_the_start( DATETIMEOBJ, $post->ID, $post->occurrence_id );
			$end = eo_get_the_end( DATETIMEOBJ, $post->ID, $post->occurrence_id );
		endif;
		*/

		if ( $timezone ) {
			$earliest_date = $earliest_date ? min( eo_get_schedule_start( DATETIMEOBJ ), $earliest_date ) : eo_get_schedule_start( DATETIMEOBJ );
			$latest_date   = $latest_date ? max( eo_get_schedule_last( DATETIMEOBJ ), $latest_date ) : eo_get_schedule_last( DATETIMEOBJ );
		}

		// Generate Event status.
		if ( get_post_status( get_the_ID() ) === 'publish' ) {
			$event_status = 'CONFIRMED';
		} else {
			$event_status = 'TENTATIVE';
		}

		// Init UID.
		$uid = eo_get_event_uid();

		// Maybe inject occurrence ID for a unique UID.
		$oid = eo_get_the_occurrence_id();
		if ( ! empty( $oid ) && $oid > 0 ) :
			$uid .= 'OID-' . $oid;
		endif;

		// Output event.
		echo "BEGIN:VEVENT\r\n";
		echo 'UID:' . esc_html( $uid ) . "\r\n";
		echo 'STATUS:' . esc_html( $event_status ) . "\r\n";
		echo 'DTSTAMP:' . esc_html( $dtstamp ) . "\r\n";
		echo 'CREATED:' . esc_html( $created_date ) . "\r\n";
		echo 'LAST-MODIFIED:' . esc_html( $modified_date ) . "\r\n";

		if ( eo_is_all_day() ) {
			// All day event.
			$end->modify( '+1 minute' );
			echo 'DTSTART;VALUE=DATE:' . esc_html( $start->format( 'Ymd' ) ) . "\r\n";
			echo 'DTEND;VALUE=DATE:' . esc_html( $end->format( 'Ymd' ) ) . "\r\n";
		} elseif ( $timezone ) {
			// Non-all-day event with timezone.
			echo 'DTSTART;TZID=' . esc_html( eo_get_blog_timezone()->getName() ) . ':' . esc_html( $start->format( 'Ymd\THis' ) ) . "\r\n";
			echo 'DTEND;TZID=' . esc_html( eo_get_blog_timezone()->getName() ) . ':' . esc_html( $end->format( 'Ymd\THis' ) ) . "\r\n";
		} else {
			// Non-all-day event without timezone or with GMT offset.
			$start->setTimezone( $utc_timezone );
			$end->setTimezone( $utc_timezone );
			echo 'DTSTART:' . esc_html( $start->format( 'Ymd\THis\Z' ) ) . "\r\n";
			echo 'DTEND:' . esc_html( $end->format( 'Ymd\THis\Z' ) ) . "\r\n";
		}

		/*
		// This feed only supports recurrence without includes or excludes.
		if ( empty( $schedule_data['exclude'] ) AND empty( $schedule_data['include'] ) ) :
			if ( $recurrence_rule = eventorganiser_generate_ics_rrule() ) :
				echo 'RRULE:' . $recurrence_rule . "\r\n";
			endif;
		endif;
		*/

		/*
		// This feed does not support exclusion.
		if ( ! empty( $schedule_data['exclude'] ) ) :
			$exclude_strings = array();
			foreach ( $schedule_data['exclude'] as $exclude ) {
				if ( eo_is_all_day() ) {
					$param = ';VALUE=DATE';
					$exclude_strings[] = $exclude->format( 'Ymd' );
				} elseif ( $timezone ) {
					$param = ';TZID=' . eo_get_blog_timezone()->getName();
					$exclude_strings[] = $exclude->format( 'Ymd\THis' );
				} else {
					$param = '';
					$exclude->setTimezone( $utc_timezone );
					$exclude_strings[] = $exclude->format( 'Ymd\THis\Z' );
				}
			}
			echo 'EXDATE' . $param . ':' . implode( ',',$exclude_strings ) . "\r\n";
		endif;
		*/

		/*
		// This feed does not support inclusion.
		if ( ! empty( $schedule_data['include'] ) ) :
			$include_strings = array();
			foreach ( $schedule_data['include'] as $include ) {
				if ( eo_is_all_day() ) {
					$param = ';VALUE=DATE';
					$include_strings[] = $include->format( 'Ymd' );
				} elseif ( $timezone ) {
					$param = ';TZID=' . eo_get_blog_timezone()->getName();
					$include_strings[] = $include->format( 'Ymd\THis' );
				} else {
					$param = '';
					$include->setTimezone( $utc_timezone );
					$include_strings[] = $include->format( 'Ymd\THis\Z' );
				}
			}
			echo 'RDATE' . $param . ':' . implode( ',',$include_strings ) . "\r\n";
		endif;
		*/

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo eventorganiser_fold_ical_text(
			'SUMMARY: ' . eventorganiser_escape_ical_text( html_entity_decode( get_the_title_rss(), ENT_COMPAT, 'UTF-8' ) )
		) . "\r\n";

		$description = wp_strip_all_tags( html_entity_decode( get_the_excerpt(), ENT_COMPAT, 'UTF-8' ) );

		/**
		 * Filters the description of the event as it appears in the iCal feed.
		 *
		 * @param string $description The event description.
		 */
		$description = apply_filters( 'eventorganiser_ical_description', $description );
		$description = eventorganiser_escape_ical_text( $description );

		if ( ! empty( $description ) ) :
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo eventorganiser_fold_ical_text( "DESCRIPTION: $description" ) . "\r\n";
		endif;

		$description = wpautop( html_entity_decode( get_the_content(), ENT_COMPAT, 'UTF-8' ) );
		// Remove new lines.
		$description = str_replace( "\r\n", '', $description );
		$description = str_replace( "\n", '', $description );
		$description = eventorganiser_escape_ical_text( $description );
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo eventorganiser_fold_ical_text( "X-ALT-DESC;FMTTYPE=text/html: $description" ) . "\r\n";

		$cats = get_the_terms( get_the_ID(), 'event-category' );
		if ( $cats && ! is_wp_error( $cats ) ) :
			$cat_names = wp_list_pluck( $cats, 'name' );
			$cat_names = array_map( 'eventorganiser_escape_ical_text', $cat_names );
			echo 'CATEGORIES:' . esc_html( implode( ',', $cat_names ) ) . "\r\n";
		endif;

		if ( eo_get_venue() ) :
			$venue = eo_get_venue_name( eo_get_venue() );
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo 'LOCATION:' . eventorganiser_fold_ical_text( eventorganiser_escape_ical_text( $venue ) ) . "\r\n";
			echo 'GEO:' . esc_html( implode( ';', eo_get_venue_latlng( $venue ) ) ) . "\r\n";
		endif;

		if ( get_the_author_meta( 'ID' ) ) {
			$author_name = eventorganiser_escape_ical_text( get_the_author() );
			// @see https://github.com/stephenharris/Event-Organiser/issues/362
			$author_name  = str_replace( '"', '', $author_name );
			$author_email = eventorganiser_escape_ical_text( get_the_author_meta( 'user_email' ) );
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo eventorganiser_fold_ical_text( 'ORGANIZER;CN="' . $author_name . '":MAILTO:' . $author_email ) . "\r\n";
		}

		// Maybe append occurrence ID for a unique GUID.
		$guid = get_permalink();
		if ( ! empty( $oid ) && $oid > 0 ) :
			$guid = add_query_arg( 'oid', $oid, $guid );
		endif;
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo eventorganiser_fold_ical_text( 'URL;VALUE=URI:' . $guid ) . "\r\n";

		if ( has_post_thumbnail( get_the_ID() ) ) {
			$thumbnail_id        = get_post_thumbnail_id( get_the_ID() );
			$thumbnail_url       = wp_get_attachment_url( $thumbnail_id );
			$thumbnail_mime_type = get_post_mime_type( $thumbnail_id );
			printf( "ATTACH;FMTTYPE=%s:%s\r\n", esc_html( $thumbnail_mime_type ), esc_url( $thumbnail_url ) );
		}

		echo "END:VEVENT\r\n";

	endwhile;

	if ( $timezone ) {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo eventorganiser_ical_vtimezone( $timezone, $earliest_date->format( 'U' ), $latest_date->format( 'U' ) ) . "\r\n";
	}

endif;

echo "END:VCALENDAR\r\n";
