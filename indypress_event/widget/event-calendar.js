jQuery(function($) {
	$('.indy-event-calendar-multi').click(function() {
		var html = $(this).data('dialog');
		var d = new Date();
		d.setDate($(this).data('day'));
		$(html).dialog( {
			title: 'Events for ' + d.toLocaleDateString()
		});
	});
});

