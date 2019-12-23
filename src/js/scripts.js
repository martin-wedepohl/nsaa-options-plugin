jQuery(document).ready(function($) {	
	$('.time_picker').timepicker({
		timeFormat: 'h:mm TT',
		stepMinute: 15,
		controlType: 'select'
	});
	$('.date_picker').datepicker({
		changeMonth: true,
		changeYear: true,
		showButtonPanel: true,
		dateFormat : 'DD MM d, yy',
		minDate : 0
	});
	$('.month_picker').datepicker({
		changeMonth: true,
		changeYear: true,
		showButtonPanel: true,
		dateFormat : 'MM yy',
		minDate: 0,
		onChangeMonthYear: function( year, month, inst ) {
			$(this).val($.datepicker.formatDate('MM yy', new Date(year, month - 1, 1)));
		},
		onClose: function( datetext, inst) {
			var month = $(".ui-datepicker-month :selected").val();
			var year = $(".ui-datepicker-year :selected").val();
			$(this).val($.datepicker.formatDate('MM yy', new Date(year, month, 1)));
	
		}
	}).focus(function() {
		$(".ui-datepicker-calendar").hide();
	});
});
