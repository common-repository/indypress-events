<?php

function load_next_event_widget() {
		register_widget( 'IndyPress_NextEventWidget' );
}

class IndyPress_NextEventWidget extends WP_Widget
{

	function IndyPress_NextEventWidget() {
		parent::WP_Widget( false, $name = 'IndyPress Next Event' );
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

		if( $instance['active_categories'] && is_category() && !is_front_page() )
			$categories_filter = implode( ',', array_intersect( explode( ',', get_query_var( 'cat' ) ), explode( ',', $instance['active_categories'] ) ) );
		else
			$categories_filter = false;

		// Select next events
		$query = "SELECT * FROM " . $wpdb->postmeta . " AS meta2 INNER JOIN " . $wpdb->posts . " AS posts2 ON meta2.post_id=posts2.id ";
		if( $categories_filter )
			$query .= "
								LEFT JOIN $wpdb->term_relationships rel ON ( posts2.id = rel.object_id )
								LEFT JOIN $wpdb->term_taxonomy taxonomy ON ( rel.term_taxonomy_id = taxonomy.term_taxonomy_id )";
		$query .= "
								WHERE posts2.post_status='publish'
								AND meta_key='event_start'
								AND id IN (SELECT id FROM " . $wpdb->posts . " AS posts INNER JOIN " . $wpdb->postmeta . " AS meta ON posts.id=meta.post_id 
										WHERE meta.meta_key='event_end'
										AND meta.meta_value>='" . time() . "')";
		if ( $categories_filter )
			$query .= "
								AND taxonomy.taxonomy = 'category'
								AND taxonomy.term_id IN ( $categories_filter )";

		$query .= "
							 	ORDER BY posts2.post_status ASC, meta_value";
		$events = $wpdb->get_results($query);

		echo '<ul class="vcalendar">';

		// Writing next events
		if( $events ) {
			$mytime = "";
			foreach( $events as $post ) {
				setup_postdata( $post );

				$mytimeold = $mytime;
//                $mytime = strftime( "%e %h %Y", $post->meta_value );
				$mytime = date_i18n( "j F", $post->meta_value );
				if( empty( $mytimeold ) || $mytimeold !== $mytime ) {
					echo "<li class=\"nostyle\"><strong>" . $mytime . "</strong></li>";
				}
				$start = get_post_meta( $post->ID, 'event_start', true );
				$start_iso = date( 'c', $start );
				$location = get_post_meta( $post->ID, 'event_location', true );
			?>
				<li class="cat-post-item vevent">
				<a class="post-title url" href="<?php the_permalink(); ?>" rel="bookmark" title="Permanent Link to <?php the_title_attribute(); ?>">
				<abbr class="dtstart" title="<?php echo $start_iso; ?>"></abbr>
					<span class="summary"><?php the_title(); ?></span>
				<?php if( $location ): ?>
					&nbsp;@ <span class="location"><?php echo $location; ?></span>
				<?php endif; ?>
				</a>
				</li>
			<?php
			}
		} else 
			if( !$instance["no_event"] )
				echo "There arent events";
			else
				echo $instance['no_event'];


		echo "</ul>";

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

			<p>
				<label for="<?php echo $this->get_field_id("no_event"); ?>">
					<?php _e( 'Alert (when there aren\' next event)' , 'indypress'); ?>:
					<input class="widefat" id="<?php echo $this->get_field_id("no_event"); ?>" name="<?php echo $this->get_field_name("no_event"); ?>" type="text" value="<?php echo esc_attr($instance["no_event"]); ?>" />
				</label>
				<label for="<?php echo $this->get_field_id("active_categories"); ?>">
					<?php _e( 'Filter by category when viewing one of these categories (comma separated IDs)' , 'indypress'); ?>:
					<input class="widefat" id="<?php echo $this->get_field_id("active_categories"); ?>" name="<?php echo $this->get_field_name("active_categories"); ?>" type="text" value="<?php echo isset( $instance['active_categories'] ) ? esc_attr($instance["active_categories"]) : ''; ?>" />
				</label>
			</p>
	<?php
	}
}

?>
