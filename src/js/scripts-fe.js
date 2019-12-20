jQuery(document).ready(function($) {	
	$('.hide-nsaa-section').each(function( i, val ) {
        var $id = $(val).data('id');
        $('#' + $id).hide();
    });
});
