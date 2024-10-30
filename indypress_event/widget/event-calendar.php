<?php
function load_event_calendar_widget() {
		register_widget( 'IndyPress_EventCalendarWidget' );
}
add_action('init', 'check_widget');
function check_widget() {
	global $indypressevent_url;
	wp_register_script('indy-event-calendar-js', $indypressevent_url . 'widget/event-calendar.js',
				 array('jquery',
				 'jquery-ui-core',
				 'jquery-ui-dialog'),
				 false, true );
	wp_enqueue_script('indy-event-calendar-js');
	wp_register_style('jquery-ui-smoothness', $indypressevent_url . 'css/smoothness/jquery-ui-1.8.16.custom.css');
	wp_register_style('indy-event-calendar-style', $indypressevent_url . 'widget/event-calendar.css',
						 array('wp-jquery-ui-dialog', 'jquery-ui-smoothness'));
	wp_enqueue_style('indy-event-calendar-style');
}



class IndyPress_EventCalendarWidget extends WP_Widget
{

	function IndyPress_EventCalendarWidget() {
		parent::WP_Widget( false, $name = 'IndyPress Event Calendar' );
	}

	function widget( $args, $instance ) {
		global $indywp_pages_categories, $table_prefix, $wpdb, $post;
		$post_old = $post; // Save the post object.

		extract( $args );
		if( !isset($instance['title']) || !$instance['title'] )
			$instance['title'] = 'Events';
		if( !isset($instance['active_categories']) || !$instance['active_categories'] )
			$instance['active_categories'] = '';

		/* Before widget (defined by themes). */
		echo $before_widget;

		// Widget title
		echo $before_title;
		if( isset( $instance['title_link'] ) && $instance['title_link'] )
			echo '<a href="' . get_category_link($instance["cat"]) . '">' . $instance["title"] . '</a>';
		else
			echo $instance["title"];
		echo $after_title;

		$starttime = strtotime("00:00", strtotime("first day of this month"));
		$endtime = strtotime("00:00", strtotime("first day of next month"));
		// Select next events
		$query = "SELECT DAYOFMONTH(FROM_UNIXTIME(meta_value)) `day`, posts.*
			FROM " . $wpdb->postmeta . " AS meta
				INNER JOIN " . $wpdb->posts . " AS posts
				ON meta.post_id=posts.id 
			WHERE posts.post_status='publish'
				AND posts.post_type='indypress_event'
				AND meta_key='event_start'
				AND meta.meta_value >='" . $starttime . "'
				AND meta.meta_value <'" . $endtime . "'
			ORDER BY day ASC";
		$events = $wpdb->get_results($query);
		$days = array(); //each day contains a list of posts
		//negative indexes are for "padding"
		for ($i = -(strftime('%w', strtotime('first day of this month')) - 1); $i<date('t'); $i++)
			$days[$i] = array();
		foreach($events as $p)
			$days[$p->day][] = $p;
?>
		<table class="indy-event-cal">
		<thead>
		<tr>
<?php
		$ts = strtotime('next Monday');
		//print days week
		global $wp_locale;
		for($i = 0; $i < 7; $i++) {
			//$weekday = strftime('%a', $ts); //why, oh PHP, do you sucks so much?
			$weekday = $wp_locale->get_weekday_initial( $wp_locale->get_weekday(($i + 1) %7) );
			echo '<th>' . $weekday . '</th>';
			$ts = strtotime('+1 day', $ts);
		}
?>
		</tr>
		</thead>
		<tbody>
<?php
		$weekday=0;
		foreach($days as $d => $posts) {
			if( $weekday % 7 == 1) echo '<tr>';
			echo '<td>';
			if($posts) {
				if( count($posts) > 1 ) {
					$list = '<ul>';
					foreach($posts as $post) {
						setup_postdata($post);
						$list .= '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
					}
					$list .= '</ul>';
					$list = str_replace('"', '&quot;', $list);
					echo '<a data-dialog="' . $list . '" class="indy-event-calendar-multi indy-event-calendar-link" data-day="' . $d . '" title="' . count($posts) . ' eventi">' . $d . '</a>';

				} else {
					$post = $posts[0];
					setup_postdata($post);
					echo '<a class="indy-event-calendar-link" title="' . get_the_title() . '" href="' . get_permalink() . '">' . $d . '</a>';
				}
			}
			elseif($d > 0)
				echo $d;
			echo '</td>';

			if($weekday % 7 == 0) echo '</tr>';
			$weekday++;
		}
?>
		</tbody>
		</table>
<?php
		/* After widget (defined by themes). */
		echo $after_widget;

		// Restore the post object.
		$post = $post_old;

	}

	function form($instance) {
	?>
			<p>
				<label for="<?php echo $this->get_field_id("title"); ?>">
					<?php _e( 'Title' , 'indypress'); ?>:
					<input class="widefat" id="<?php echo $this->get_field_id("title"); ?>" name="<?php echo $this->get_field_name("title"); ?>" type="text" value="<?php echo esc_attr($instance["title"]); ?>" />
				</label>
			</p>
	<?php
	}
}

