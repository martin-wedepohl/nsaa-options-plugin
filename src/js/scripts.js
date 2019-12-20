jQuery(document).ready(function($) {	
	$('.time_picker').timepicker({
		timeFormat: 'h:mm TT',
		stepMinute: 15,
		controlType: 'select'
	});
	$('.date_picker').datepicker({
		dateFormat : 'DD MM d, yy',
		minDate : 0
	});
});
