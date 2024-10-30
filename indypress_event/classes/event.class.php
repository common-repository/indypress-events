<?php

class indypressevent {

	function indypressevent() {
		global $indypressevent_path;

		// REQUIRE API

		$this->load_settings();

		// ENABLE post_type "indypress_event"
		add_action( 'init', array( $this, 'event_setup' ) );
	}

	function event_link( $post_link, $post = 0, $leavename = false ) {
		// Code stolen from https://wordpress.org/support/topic/custom-post-type-with-custom-urls?replies=3#post-2039780
		if ( strpos('%post_id%', $post_link) === 'false' ) {
			return $post_link;
		}

		if ( is_object($post) ) {
			$post_id = $post->ID;
		} else {
			$post_id = $post;
			$post = get_post($post_id);
		}

		if ( !is_object($post) || $post->post_type != 'indypress_event' ) {
			return $post_link;
		}

		//put post ID in place of %post_id%
		return str_replace('%post_id%', $post_id, $post_link);

	}

	function load_settings() {
		if(function_exists('get_indy_publish_page_id'))
			$this->publication_page = get_indy_publish_page_id();
		else //indypress is not active
			$this->publication_page = -1;
	}

	function event_setup() {
		global $indypressevent_url;
		global $indypress_relative_path;

		//TODO: move publication.css somewhere else
		wp_register_style( 'indypress_publication', $indypress_relative_path . 'css/publication.css' );
		register_post_type( 'indypress_event',
			array(
				'description' => __( 'Events created with IndyPress' , 'indypress'),
				'labels' => array(
					'name' => _x( 'Events', 'post type general name' , 'indypress'),
					'singular_name' => _x( 'Event', 'post type singular name' , 'indypress'),
					'add_new' => _x( 'Add New', 'event' , 'indypress'),
					'add_new_item' => __( 'Add New Event' , 'indypress'),
					'edit_item' => __( 'Edit Event' , 'indypress'),
					'new_item' => __( 'New Event' , 'indypress'),
					'view_item' => __( 'View Event' , 'indypress'),
					'search_items' => __( 'Search Events' , 'indypress'),
					'not_found' =>  __( 'No events found' , 'indypress'),
					'not_found_in_trash' => __( 'No events found in Trash' , 'indypress'),
					'parent_item_colon' => 'Parent Event',
					'menu_name' => 'Events'
				),
				'public' => true,
				'menu_icon' => $indypressevent_url . 'images/clock.png',
				'rewrite' => array( 'slug' => get_option('indypressevent_permalink', 'event'), 'with_front' => false),
				'capability_type' => 'post',
				'menu_position' => 5, // below posts
				'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'comments', 'custom-fields' ),
				'has_archive' => true,
				'taxonomies' => array_keys(get_taxonomies(array(), 'names')) //every taxonomy
			)	);
		add_filter('post_type_link', array( $this, 'event_link'), 1, 3);
	}

}

?>
