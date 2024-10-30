<?php
add_action('indypress_input_init_daterange', 'indypress_input_daterange_init', 10, 2);
function indypress_input_daterange_init ($args, $number) {
	new indypress_input_daterange( $args, $number );
}
class indypress_input_daterange {
		/* Handles a daterange input: this will show some form to let the user pick two dates,
			the first before the second.
			NOTE: it DOESN'T USE $args['name'] but name_prefix
	 */
	function indypress_input_daterange ( $args, $number ) {
		$this->args = $args;
		add_filter( 'indypress_input_form_daterange_' . $number, array( &$this, 'form' ), 10, 2 );
		add_filter( 'indypress_form_post', array( &$this, 'submitted' ) );
		add_filter( 'indypress_form_validation_all', array( &$this, 'validation' ), 10, 2 );
	}
	function form( $previous, $submitted ) {
		$args = $this->args;
		$form = '';
		$month = array(1=>"Gennaio", 2=>"Febbraio", 3=>"Marzo", 4=>"Aprile", 5=>"Maggio", 6=>"Giugno", 7=>"Luglio", 8=>"Agosto", 9=>"Settembre", 10=>"Ottobre", 11=>"Novembre", 12=>"Dicembre");
		$form .= '
		<h2>Inizio evento</h2>
		<input type="text" name="' . $args['name_prefix'] . '_start_day" value="' . $submitted['event_start_day'] . '" size="2" maxlength="2" title="Giorno di inizio" >
		<select name="' . $args['name_prefix'] . '_start_month" title="Mese di inizio" >';
		for( $i=1; $i<=12; $i++ ) {
			if( $submitted['event_start_month'] == $i )
				$form .= '		<option value="' . $i . '" SELECTED >' . $month[$i] . "</option>\n";
			else
				$form .= '		<option value="' . $i . '">' . $month[$i] . "</option>\n";
		}

		$form .= '
		</select>
		<input type="text" name="' . $args['name_prefix'] . '_start_year" value="' . $submitted['event_start_year'] . '" size="4" maxlength="4" title="Anno di inizio">
		-
		<select name="' . $args['name_prefix'] . '_start_hour" title="Ora di inizio">
			';
		for( $i=0; $i<24; $i++ )
			if( $submitted['event_start_hour'] == $i )
				$form .= '		<option value="' . $i . '" SELECTED>' . $i . "</option>\n";
			else
				$form .= '		<option value="' . $i . '">' . $i . "</option>\n";
		$form .= '
		</select>
		<select name="' . $args['name_prefix'] . '_start_minut" title="Minuto di inizio" >
			';
		for( $i=0; $i<60; $i++ )
			if( $submitted['event_start_minut'] == $i )
				$form .= '		<option value="' . $i . '" SELECTED>' . $i . "</option>\n";
			else
				$form .= '		<option value="' . $i . '">' . $i . "</option>\n";
		$form .= '
		</select>

		<h2>Fine evento</h2>
		<input type="text" name="' . $args['name_prefix'] . '_end_day" value="' . $submitted['event_end_day'] . '" size="2" maxlength="2" title="Giorno di fine">
		<select name="' . $args['name_prefix'] . '_end_month" title="Mese di fine" >';
		for( $i=1; $i<=12; $i++ )
			if( $submitted['event_end_month'] == $i )
				$form .= '		<option value="' . $i . '" SELECTED>' . $month[$i] . "</option>\n";
			else
				$form .= '		<option value="' . $i . '">' . $month[$i] . "</option>\n";

		$form .= '
		</select>
		<input type="text" name="' . $args['name_prefix'] . '_end_year" value="' . $submitted['event_end_year'] . '" size="4" maxlength="4" title="Anno di fine">
		-
		<select name="' . $args['name_prefix'] . '_end_hour" title="Ora di fine" >
			';
		for( $i=0; $i<24; $i++ )
			if( $submitted['event_end_hour'] == $i )
				$form .= '		<option value="' . $i . '" SELECTED>' . $i . "</option>\n";
			else
				$form .= '		<option value="' . $i . '">' . $i . "</option>\n";
		$form .= '
		</select>
		<select name="' . $args['name_prefix'] . '_end_minut" title="Minuto di fine" >
			';
		for( $i=0; $i<60; $i++ )
			if( $submitted['event_end_minut'] == $i )
				$form .= '		<option value="' . $i . '" SELECTED>' . $i . "</option>\n";
			else
				$form .= '		<option value="' . $i . '">' . $i . "</option>\n";
		$form .= '
		</select>
		';
		return $previous . $form;
	}
	function submitted( $submitted ) {
		$args = $this->args;
	$start = mktime( $submitted[$args['name_prefix'] . '_start_hour'], $submitted[$args['name_prefix'] . '_start_minut'], 0, $submitted[$args['name_prefix'] . '_start_month'], $submitted[$args['name_prefix'] . '_start_day'], $submitted[$args['name_prefix'] . '_start_year'] );
	$end = mktime( $submitted[$args['name_prefix'] . '_end_hour'], $submitted[$args['name_prefix'] . '_end_minut'], 0, $submitted[$args['name_prefix'] . '_end_month'], $submitted[$args['name_prefix'] . '_end_day'], $submitted[$args['name_prefix'] . '_end_year'] );
	$submitted[ $args['name_prefix'] . '_start' ] = $start;
	$submitted[ $args['name_prefix'] . '_end' ] = $end;
	return $submitted;
	}
	function validation( $errors, $submitted ) {
		$args = $this->args;


		if( empty( $submitted[$args['name_prefix'] . '_start_day'] ) || empty( $submitted[$args['name_prefix'] . '_start_month']) || empty( $submitted[$args['name_prefix'] . '_start_year'] ) ) 
		 	$errors[] = __('Start event date', 'indypress');
		if( !isset( $submitted[$args['name_prefix'] . '_start_hour'] ) || !isset( $submitted[$args['name_prefix'] . '_start_minut'] ) ) 
			$errors[] = __('Start event time', 'indypress');
		if( empty( $submitted[$args['name_prefix'] . '_end_day'] ) || empty( $submitted[$args['name_prefix'] . '_end_month']) || empty( $submitted[$args['name_prefix'] . '_end_year'] ) ) 
			$errors[] = __('End event date', 'indypress'); 
		if( !isset( $submitted[$args['name_prefix'] . '_end_hour'] ) || !isset( $submitted[$args['name_prefix'] . '_end_minut'] ) ) 
			$errors[] = __('End event time', 'indypress'); 

		// CHECK DATES
		if( !checkdate( $submitted[$args['name_prefix'] . '_start_month'], $submitted[$args['name_prefix'] . '_start_day'], $submitted[$args['name_prefix'] . '_start_year'] ) ) 
			$errors[] = __('Invalid start event date', 'indypress'); 
		if( !checkdate( $submitted[$args['name_prefix'] . '_end_month'], $submitted[$args['name_prefix'] . '_end_day'], $submitted[$args['name_prefix'] . '_end_year'] ) ) 
			$errors[] = __('Invalid end event date', 'indypress'); 

		if( !( $submitted[$args['name_prefix'] . '_start_hour']<=23 && (int)$submitted[$args['name_prefix'] . '_start_hour']>=0 && $submitted[$args['name_prefix'] . '_start_minut']<=59 && (int)$submitted[$args['name_prefix'] . '_start_minut']>=0 ) ) 
			$errors[] = __('Invalid start event time', 'indypress'); 
		if( !( $submitted[$args['name_prefix'] . '_end_hour']<=23 && (int)$submitted[$args['name_prefix'] . '_end_hour']>=0 && $submitted[$args['name_prefix'] . '_end_minut']<=59 && (int)$submitted[$args['name_prefix'] . '_end_minut']>=0 ) ) 
			$errors[] = __('Invalid end event time', 'indypress'); 
		

		$start = mktime( $submitted[$args['name_prefix'] . '_start_hour'], $submitted[$args['name_prefix'] . '_start_minut'], 0, $submitted[$args['name_prefix'] . '_start_month'], $submitted[$args['name_prefix'] . '_start_day'], $submitted[$args['name_prefix'] . '_start_year'] );
		$end = mktime( $submitted[$args['name_prefix'] . '_end_hour'], $submitted[$args['name_prefix'] . '_end_minut'], 0, $submitted[$args['name_prefix'] . '_end_month'], $submitted[$args['name_prefix'] . '_end_day'], $submitted[$args['name_prefix'] . '_end_year'] );
		if($start > $end)
			$errors[] = $args['name_prefix'] . ' ends before starting';
		return $errors;
	}
}
