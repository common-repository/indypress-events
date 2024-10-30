<?php
/* ******
This class provides different views when the archive for events is requested
(actually, a filter can be used to say that even a category is a page)

DOES:
* filter using events metadata
* no template required
* url parameters (ie ?order=desc) are used

SHOULD DO (but still doesn't):
* support templates (so that different views for the same period can be used:
agenda-style, linear, etc.)
*/

function is_event_page() {
	// Utility: checks if we're querying about events
	$res = false;
	//if you want to say that, for example, a category page is an event page...
	//just put true in this filter
	$res = apply_filters('indypressevent_iseventpage', $res);
	return $res;
}

class indypressevent_page {

	function indypressevent_page() {

		// Allow to append ?event_period=today
//    add_filter('query_vars', array($this, 'add_event_query_vars') );

		// Allows to select based on event-metadata
		add_filter('posts_join', array($this, 'event_post_join'));
		add_filter('posts_where', array($this, 'event_post_where'));
		add_filter('posts_distinct', array($this, 'event_post_distinct'));
		add_filter('posts_orderby', array($this, 'event_post_orderby'));
		add_filter('request', array($this, 'event_post_type'));
	}

	function add_event_query_vars($qvars) {
		//NOTE: "event_period" arg is added to every page, but should be used only on
		//events
		$qvars[] = 'event_period';
		return $qvars;
	}

	function event_post_join($join) {
		global $wpdb, $wp_query;
		if(is_event_page()) {
			$join .= "
				LEFT JOIN $wpdb->postmeta wpostmeta ON " . $wpdb->posts . ".ID = wpostmeta.post_id 
				LEFT JOIN $wpdb->postmeta wpostmeta2 ON " . $wpdb->posts . ".ID = wpostmeta2.post_id ";
			return $join;
		}
		return $join;
	}

	function event_post_where($where) {
		global $wpdb, $wp_query;
		if(is_event_page()) {
			$where .= " AND wpostmeta.meta_key = 'event_end'
				AND wpostmeta.meta_value+'0' >= CURDATE()
				AND wpostmeta2.meta_key = 'event_start'";
			if(isset($wp_query->query['event_period'])) {
				$period = $wp_query->query['event_period'];
				if(strtolower($period) == 'today')
					$where .= " AND wpostmeta2.meta_value < " . mktime(0, 0, 0, date("m")  , date("d")+1, date("Y"));
				if(strtolower($period) == 'tomorrow')
					$where .= " AND wpostmeta2.meta_value < " . mktime(0, 0, 0, date("m")  , date("d")+2, date("Y"));
				if(strtolower($period) == 'seven')
					$where .= " AND wpostmeta2.meta_value < " . mktime(0, 0, 0, date("m")  , date("d")+7, date("Y"));
				if(strtolower($period) == 'week')
					$where .= " AND wpostmeta2.meta_value < " . mktime(0, 0, 0, date("m")  , date("d")+7, date("Y"));
			}

			return $where;
		}
		return $where;
	}

	function event_post_distinct($distinct) {
		if(is_event_page()) {
			return " DISTINCT ";
		}
		return $distinct;
	}

	function event_post_orderby($orderby) {
		global $wp_query;
		if(is_event_page()) {
			$order = 'DESC';
			if(isset($wp_query->query['order']) && 
				strtolower($wp_query->query['order']) == 'asc')
				$order = 'ASC';
			return "wpostmeta2.meta_value $order";
		}
		return $orderby;
	}
	function event_post_type($request) {
		//Filtering by category implies filtering by post_type
		if( is_event_page() ) {
			$request['post_type'] = 'indypress_event';
			return $request;
		}
		return $request;

	}

} ?>
