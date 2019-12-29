"use strict";

jQuery(document).ready(function($) {	
	$('.hide-nsaa-section').each(function( i, val ) {
        let $id = $(val).data('id');
        $('#' + $id).hide();
    });
});
