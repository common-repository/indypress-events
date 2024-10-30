<?php

/*A Template tag */
function is_indypress_event( $post_id=NULL ) {
  if( $post_id !== NULL )
	$post = $post_id;
  else
	global $post;

  $type = get_post_type( $post );
  return 'indypress_event' == $type;
}

function the_event_information() {
	global $post;

	$event_start = get_post_meta( $post->ID, 'event_start', TRUE );
	$event_end = get_post_meta( $post->ID, 'event_end', TRUE );
	$event_location = get_post_meta( $post->ID, 'event_location', TRUE );

	$pre_content = '';
	if( $event_start && $event_end ) {
		$start = date( 'j/m/y - G:i', $event_start );
		$start_iso = date( 'c', $event_start );
		$end = date( 'j/m/y - G:i', $event_end );
		$end_iso = date( 'c', $event_end );

		$pre_content .= '<p><strong>' . __('Start event:', 'indypress') . '</strong> <time itemprop="startDate" datetime="' . $start_iso . '">' . $start . '</time><br />';
		$pre_content .= '<strong>' . __('End event:', 'indypress') . '</strong> <time itemprop="endDate" datetime="' . $start_iso . '">' . $end . '</time></p>';
	}
	if( $event_location )
		$pre_content .= '<p><strong>' . __('Location:', 'indypress') . '</strong><span itemprop="location">' . $event_location . '</span></p>';
	return apply_filters( 'the_event_information', $pre_content );
}
/**
 * indypressevent_get_period_events 
 * 
 * @param array $period  An array of two (start,end) timestamps. If null, the current week is retrieved
 * @access public
 * @return list events in a period. Key for event is the start timestamp,
//value is: url, post_content
 */
function indypressevent_get_period_events( $period=null ) {
	global $wpdb;
	if( $period === null ) {
		$ts_start = strtotime('this Monday');
		$ts_end = strtotime('next Sunday');
	}
	else {
		$ts_start = $period[0];
		$ts_end = $period[1];
	}
	$query = 'SELECT meta.meta_value, posts.id, posts.post_title FROM ' . $wpdb->postmeta . ' AS meta INNER JOIN ' . $wpdb->posts . ' AS posts
		ON meta.post_id = posts.id
		WHERE posts.post_status="publish" AND
		posts.post_type="indypress_event" AND
		meta.meta_key = "event_start" AND
		meta.meta_value >= ' . $ts_start . ' AND
		meta.meta_value <= ' . $ts_end . '
		ORDER BY meta.meta_value ASC';
	$events = $wpdb->get_results( $query, OBJECT_K );
	$ret = array();
	foreach($events as $timestamp => $e) {
		$ret[$timestamp] = array();
		$ret[$timestamp]['title'] = $e->post_title;
	}
	return $ret;
}

/**
 * indypressevent_weektable 
 * 
 * Builds a table of the events. Despite the $events can be anything, it's built with
 * weekly agenda in mind
 * @param array $events  the result of indypressevent_get_period_events()
 * @access public
 * @return string html of the table
 */
function indypressevent_weektable( $events ) {
	$table = '<style type="text/css">
.weektable{
	width: 100%;
}
.daycell {
	width:14%;
}
.event_time {
	font-weight: bold;
	margin-right: 1em;
}
		</style>';
	$table .= '<table class="weektable">';
	$oldday = null;
	$eventsbyday = array();
	foreach($events as $ts => $e) {
		$day = date('d', $ts);
		if(!isset($eventsbyday[$day]))
			$eventsbyday[$day] = array();
		$eventsbyday[$day][$ts] = $e;
	}
	$table .= '<thead>';
	foreach($eventsbyday as $day => $ignore) {
		$table .= '<th>' . $day . '</th>';
	}
	$table .= '</thead><tr>';
	foreach($eventsbyday as $day => $dayevents) {
		$table .= '<td class="daycell" data-day="' . $day . '">';
		foreach($dayevents as $ts => $e) {
			$table .= '<span class="event_time">' . date('h:m', $ts) . '</span><span class="event_title" data-ts="' . $ts . '">' . $e['title'] . '</span>';
			$oldday = $day;
		}
		$table .= '</td>';
	}
	$table .= '</tr></table>';

	return $table;
}

?>
