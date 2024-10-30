<?php
class indypressevent_admin {
	function indypressevent_admin() {
		//time metabox
		add_action( 'admin_init', array( &$this, 'add_event_time_box' ) );
		add_action( 'save_post', array( $this, 'event_save_postdata' ) );
		//location metabox
		add_action( 'admin_init', array( &$this, 'add_event_location_box' ) );
		add_action( 'save_post', array( $this, 'event_location_save_postdata' ) );

		//time column
		add_action('manage_posts_custom_column', array( &$this, 'event_column' ), 10, 2);
		add_filter('manage_edit-indypress_event_columns', array( &$this, 'add_columns' ));
	}
	//time metabox
	function add_event_time_box() {
		add_meta_box( 'indypress_event_time', __('Start and stop event time', 'indypress'), array( &$this, 'time_box' ), 'indypress_event', 'normal', 'high' );
	}
	function add_event_location_box() {
		add_meta_box( 'indypress_event_location', __('event location', 'indypress'), array( &$this, 'location_box' ), 'indypress_event', 'normal', 'high' );
	}

	function location_box() {
		global $post;

 		$args = null;
		$location = get_post_meta($post->ID, 'event_location', true);
		if( $location ) {
			$args['event_location'] = $location;
		}

		$form = '<label for="event_location">' . __('event location', 'indypress') . '</label>
			<input type="text" name="event_location" id="event_location" value="' . esc_attr( stripslashes( $args['event_location'] ) ) . '" maxlength="64" title="' . __('event location', 'indypress') . '" >';

		// Use nonce for verification
		wp_nonce_field( plugin_basename(__FILE__), 'indypress_event_admin_box' );

		echo $form;
	}

	function time_box() {
		global $post;

 		$args = null;
    $start = get_post_meta($post->ID, 'event_start', true);
    $end = get_post_meta($post->ID, 'event_end', true);
		if( $start ) {
			$args['event_start_day'] = date( 'd', $start );
			$args['event_start_month'] = date( 'm', $start );
			$args['event_start_year'] = date( 'Y', $start );
			$args['event_start_hour'] = date( 'H', $start );
			$args['event_start_minut'] = date( 'i', $start );
		}
		if( $end ) {
			$args['event_end_day'] = date( 'd', $end );
			$args['event_end_month'] = date( 'm', $end );
			$args['event_end_year'] = date( 'Y', $end );
			$args['event_end_hour'] = date( 'G', $end );
			$args['event_end_minut'] = date( 'i', $end );
		}

		$month = array(1=>__('January', 'indypress'), 2=>__('Febrauty', 'indypress'), 3=>__('March', 'indypress'), 4=>__('April', 'indypress'), 5=>__('May', 'indypress'), 6=>__('June', 'indypress'), 7=>__('July', 'indypress'), 8=>__('August', 'indypress'), 9=>__('September', 'indypress'), 10=>__('October', 'indypress'), 11=>__('November', 'indypress'), 12=>__('Dicember', 'indypress'));

		$form = '<label>' . __('Start event', 'indypress') . '</label>
			<input type="text" name="event_start_day" value="' . esc_attr( stripslashes( $args['event_start_day'] ) ) . '" size="2" maxlength="2" title="' . __('Start day', 'indypress') . '" >
			<select name="event_start_month" title="' . __('Start month', 'indypress') . '" >';
			for( $i=1; $i<=12; $i++ )
				if( $args['event_start_month']==$i )
					$form .= '		<option value="' . $i . '" SELECTED>' . $month[$i] . "</option>\n";
				else
					$form .= '		<option value="' . $i . '">' . $month[$i] . "</option>\n";

			$form .= '
			</select>
			<input type="text" name="event_start_year" value="' . esc_attr( stripslashes( $args['event_start_year'] ) ) . '" size="4" maxlength="4" title="' . __('Start year', 'indypress') . '">
			-
			<select name="event_start_hour" title="' . __('Start hour', 'indypress') . '">
				';
			for( $i=0; $i<24; $i++ )
				if( $args['event_start_hour'] == $i )
					$form .= '		<option value="' . $i . '" SELECTED>' . $i . "</option>\n";
				else
					$form .= '		<option value="' . $i . '">' . $i . "</option>\n";
			$form .= '
			</select>
			<select name="event_start_minut" title="' . __('Start minut', 'indypress') . '" >
				';
			for( $i=0; $i<60; $i++ )
				if( $args['event_start_minut']==$i )
					$form .= '		<option value="' . $i . '" SELECTED>' . $i . "</option>\n";
				else
					$form .= '		<option value="' . $i . '">' . $i . "</option>\n";
			$form .= '
			</select>

			<br/>
			<label>' . __('End event', 'indypress') . '</label>
			<input type="text" name="event_end_day" value="' . esc_attr( stripslashes( $args['event_end_day'] ) ) . '" size="2" maxlength="2" title="' . __('End day', 'indypress') .'">
			<select name="event_end_month" title="' . __('End month', 'indypress') . '" >';
			for( $i=1; $i<=12; $i++ )
				if( $args['event_end_month']==$i )
					$form .= '		<option value="' . $i . '" SELECTED>' . $month[$i] . "</option>\n";
				else
					$form .= '		<option value="' . $i . '">' . $month[$i] . "</option>\n";

			$form .= '
			</select>
			<input type="text" name="event_end_year" value="' . esc_attr( stripslashes( $args['event_end_year'] ) ) . '" size="4" maxlength="4" title="' . __('End year', 'indypress') . '">
			-
			<select name="event_end_hour" title="' . __('End hour', 'indypress') . '" >
				';
			for( $i=0; $i<24; $i++ )
				if( $args['event_end_hour']==$i )
					$form .= '		<option value="' . $i . '" SELECTED>' . $i . "</option>\n";
				else
					$form .= '		<option value="' . $i . '">' . $i . "</option>\n";
			$form .= '
			</select>
			<select name="event_end_minut" title="' . __('End minut', 'indypress') . '" >
				';
			for( $i=0; $i<60; $i++ )
				if( $args['event_end_minut']==$i )
					$form .= '		<option value="' . $i . '" SELECTED>' . $i . "</option>\n";
				else
					$form .= '		<option value="' . $i . '">' . $i . "</option>\n";
			$form .= '
			</select>';

		// Use nonce for verification
		wp_nonce_field( plugin_basename(__FILE__), 'indypress_event_admin_box' );

		echo $form;
	}

	function event_location_save_postdata( $post_id ) {
		if ( !wp_verify_nonce( $_POST['indypress_event_admin_box'], plugin_basename(__FILE__) )) {
			return $post_id;
		}

		// verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
		// to do anything
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
			return $post_id;

		
		// Check permissions
		if ( 'indypress_event' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}
		else return $post_id;

		$location = $_POST['event_location'];
		$location = strip_tags( $location, '<a><b><strong>' );
		update_post_meta( $post_id, 'event_location' , $location );
	}

	function event_save_postdata( $post_id ) {
		if ( !wp_verify_nonce( $_POST['indypress_event_admin_box'], plugin_basename(__FILE__) )) {
			return $post_id;
		}

		// verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
		// to do anything
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
			return $post_id;

		
		// Check permissions
		if ( 'indypress_event' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}
		else return $post_id;

		//data is in $_POST[$input_field_name]

		if( empty( $_POST['event_start_day'] ) || empty( $_POST['event_start_month']) || empty( $_POST['event_start_year'] ) ) { $error = true; $error_return[] = 'Data di inizio evento'; }
		if( !isset( $_POST['event_start_hour'] ) || !isset( $_POST['event_start_minut'] ) ) { $error = true; $error_return[] = 'Orario di inizio evento'; }
		if( empty( $_POST['event_end_day'] ) || empty( $_POST['event_end_month']) || empty( $_POST['event_end_year'] ) ) { $error = true; $error_return[] = 'Data di fine evento'; }
		if( !isset( $_POST['event_end_hour'] ) || !isset( $_POST['event_end_minut'] ) ) { $error = true; $error_return[] = 'Orario di fine evento'; }

		// CHECK DATES
		if( !$error && !checkdate( $_POST['event_start_month'], $_POST['event_start_day'], $_POST['event_start_year'] ) ) { $error = true; $error_return[] = 'Data di inizio evento non valida'; }
		if( !$error && !checkdate( $_POST['event_end_month'], $_POST['event_end_day'], $_POST['event_end_year'] ) ) { $error = true; $error_return[] = 'Data di fine evento non valida'; }

		if( !$error && !( $_POST['event_start_hour']<=23 && (int)$_POST['event_start_hour']>=0 && $_POST['event_start_minut']<=59 && (int)$_POST['event_start_minut']>=0 ) ) { $error = true; $error_return[] = 'Orario di inizio evento non valido'; }
		if( !$error && !( $_POST['event_end_hour']<=23 && (int)$_POST['event_end_hour']>=0 && $_POST['event_end_minut']<=59 && (int)$_POST['event_end_minut']>=0 ) ) { $error = true; $error_return[] = 'Orario di fine evento non valido'; }
		if( !$error ) {
			$start = mktime( $_POST['event_start_hour'], $_POST['event_start_minut'], 0, $_POST['event_start_month'], $_POST['event_start_day'], $_POST['event_start_year'] );
			$end = mktime( $_POST['event_end_hour'], $_POST['event_end_minut'], 0, $_POST['event_end_month'], $_POST['event_end_day'], $_POST['event_end_year'] );
		}
		if( !$error && $end<=$start ) { $error = true; $error_return[] = 'La fine dell\'evento deve essere futuro all\'inizio'; }
		if( !$error && $end<=time() ) { $error = true; $error_return[] = 'La data di fine evento non può essere passata'; }

		if( true == $error )
			return $post_id;

		$parent = wp_is_post_revision( $post_id );
		if( $parent )
			$post_id = $parent;
		update_post_meta( $post_id, 'event_start' , $start );
		update_post_meta( $post_id, 'event_end' , $end );
	}


	//time column
	function add_columns( $columns ) {
		$columns['eventstart'] = 'Event start';
		return $columns;
	}
	function event_column( $column_name, $post_id ) {
		if( $column_name != 'eventstart' )
			return;
		$timestamp = get_post_meta( $post_id, 'event_start', TRUE );
		if( $timestamp )
			echo date('M j, Y @ G:i', $timestamp);
	}
}

