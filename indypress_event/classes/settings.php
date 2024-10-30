<?php

class indypressevent_settings {
	function indypressevent_settings() {
		add_filter('indypress_settings_adminpage', create_function('$v', 'return true;')); //we "plug" into admin page
		add_action( 'admin_menu', array( $this, 'menu' ) );
	}
	function main_page() {
		if ( !current_user_can( 'administrator' ) )
			wp_die( __( 'You do not have sufficient permissions to access this page.' , 'indypress') );
		?>
		<div class="wrap">
			<h2>Indypress Event</h2>
			<form action="options.php" method="post">
			<?php settings_fields( 'indypressevent' ); ?>
			<?php do_settings_sections( 'indypressevent' ); ?>
			<p class="submit">
				<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes', 'indypress'); ?>" />
			</p>
			</form>
			</div>
		<?php
		//TODO: report a summary about the current status of the plugin, with links to documentation
	}
	function register_settings() {
		add_settings_section( 'indypressevent_admin',
			__('Admin', 'indypressevent'),
			create_function('',''),
			'indypressevent' /*group*/);

		register_setting( 'indypressevent' /*group*/, 'indypressevent_add_new_event' );
		add_settings_field( 'indypressevent_add_new_event',
			__('"Add event" button in administration interface (disabling will hide link but keep it still working)', 'indypress'),
			array( $this, 'setting_add_new_event' ),
			'indypressevent', //page
			'indypressevent_admin' /*section*/ );

		add_settings_section( 'indypressevent_visualization',
			__('Visualization', 'indypressevent'),
			create_function('',''),
			'indypressevent' );

//            add_settings_section( 'indypress_visualization_event', __('Event visualization', 'indypress'), array( $this, 'empty_section' ), 'indypress_visualization_settings' );

		register_setting( 'indypressevent', 'indypressevent_info_top' );
		add_settings_field( 'indypressevent_info_top', __('Add information to event (It works whitout theme configuration, must be disabled if you want to configure your theme manually)', 'indypress'), array( $this, 'settings_event_info_top' ), 'indypressevent', 'indypressevent_visualization' );
		register_setting( 'indypressevent', 'indypressevent_permalink' );
		add_settings_field( 'indypressevent_permalink', __('Permalink for indypress_event objects. Example: <code>event/%post_id%</code> or <code>event</code>', 'indypress'), array( $this, 'settings_event_permalink' ), 'indypressevent', 'indypressevent_visualization' );

		// Add this settings to indypress, too!
		//Indypress->admin page
		register_setting( 'indypress_admin', 'indypressevent_add_new_event' );
		add_settings_field( 'indypressevent_add_new_event',
			__('Add event to administration interface (allow to hide link but keep it still working)', 'indypress'),
			array( $this, 'setting_add_new_event' ),
			'indypress_admin_settings',
			'indypress_admin_main' );

		//Indypress->visualization page
		add_settings_section( 'indypress_visualization_event', __('Event visualization', 'indypress'), create_function('',''), 'indypress_visualization_settings' );
		register_setting( 'indypress_visualization', 'indypressevent_info_top' );
		add_settings_field( 'indypressevent_info_top', __('Add information to event (It works whitout theme configuration, must be disabled if you want to configure your theme manually)', 'indypress'), array( $this, 'settings_event_info_top' ), 'indypress_visualization_settings', 'indypress_visualization_event' );
		register_setting( 'indypress_visualization', 'indypressevent_permalink' );
		add_settings_field( 'indypressevent_permalink',
			__('Permalink for indypressevent objects. Example: <code>event/%post_id%</code> or <code>event</code>', 'indypress'),
			array( $this, 'settings_event_permalink' ),
			'indypress_visualization_settings',
			'indypress_visualization_event' );
	}
	function menu() {
		add_menu_page( 'IndypressEvent options', 'IndyEvent', 'administrator', 'indypressevent', array( $this, 'main_page' ), NULL );
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		if( ! get_option( 'indypressevent_add_new_event', true ) ) {
			global $submenu;
			unset( $submenu['edit.php?post_type=indypress_event'][10] ); //hide the "Add New event" button, but if you enter the url it is still accessible
		}
	}

	//callbacks
	function setting_boolean( $option, $default=false ) {
?>
	<input type="checkbox" name="<?php echo $option; ?>" <?php if( get_option( $option, $default ) ) echo ' checked="checked"'; ?>">
<?php
	}

	function setting_add_new_event() {
		$this->setting_boolean( 'indypressevent_add_new_event', true );
	}
	function settings_event_info_top() {
		$this->setting_boolean( 'indypressevent_info_top', true );
	}
	function settings_event_permalink() {
?>
		<input type="text" name="indypressevent_permalink" value="<?php esc_attr_e( stripslashes( get_option( 'indypressevent_permalink', 'event/%post_id%' ) ) ); ?>" />
<?php
	}

}

