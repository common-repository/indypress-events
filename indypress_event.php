<?php

/* 
Plugin Name: IndyPress Event
Plugin URI: http://code.autistici.org/p/indypress
Description: Manage events post_type with lot of utilities
Author: boyska, paskao
Version: 0.2.1
Author URI: 
License: GPL2
Domain Path: ./languages/
*/

// CONFIG
$indypressevent_url = plugins_url( '', __FILE__ ) . '/indypress_event/';
$indypressevent_relative_path = '/wp-content/plugins/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ )) . 'indypress_event/';
$indypressevent_path = ABSPATH . 'wp-content/plugins/' . str_replace( basename( __FILE__ ), "", plugin_basename( __FILE__ )) . 'indypress_event/';

require_once( $indypressevent_path . 'api/event.php' );


/* --- Modified by Cap --- Start section*/
load_plugin_textdomain('indypress', '', 'indypress/languages');
/* --- Modified by Cap --- End section*/

/* Publication inputs */
	include_once( $indypressevent_path . 'form_inputs/daterange.php');
/* Publication inputs end */

// ADMIN PANEL MENU
if( is_admin() ) {
	include_once( $indypressevent_path . 'widget/event.php' );
	add_action( 'widgets_init', 'load_next_event_widget' );
	include_once( $indypressevent_path . 'widget/event-calendar.php' );
	add_action( 'widgets_init', 'load_event_calendar_widget' );

	// LOAD EVENT POST TYPE
	require_once( $indypressevent_path . 'classes/event.class.php' );
	$indypressevent = new indypressevent();
	require_once( $indypressevent_path . 'classes/event_admin.class.php' );
	$indypressevent = new indypressevent_admin();
	require_once( $indypressevent_path . 'classes/settings.php' );
	$indypressevent_settings = new indypressevent_settings();

	//TODO: some part of it should be migrated
//    require_once( $indypress_path . 'classes/visualization-settings.class.php' );
//    $indypress_visualization_settings = new Indypress_VisualizationSettings();
//    add_action( 'indypress_admin_init', array( $indypress_visualization_settings, 'Indypress_VisualizationSettings' ) );


} else {

	// INDYPRESS HOOK
	do_action( 'indypressevent_init' );

	require_once( $indypressevent_path . 'classes/event.class.php' );
	$indypressevent = new indypressevent();

	include_once( $indypressevent_path . 'widget/event.php' );
	add_action( 'widgets_init', 'load_next_event_widget' );
	include_once( $indypressevent_path . 'widget/event-calendar.php' );
	add_action( 'widgets_init', 'load_event_calendar_widget' );

	//LOAD EVENTS PAGE
	require_once( $indypressevent_path . 'classes/event_page.class.php');
	$indypressevent_page = new indypressevent_page();
	add_action( 'indypressevent_init', array( $indypressevent_page, 'indypressevent_eventpage' ) );
}
//TODO: migrate meta_box from admin.class

?>
