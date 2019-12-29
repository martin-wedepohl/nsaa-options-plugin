"use strict";

jQuery(document).ready(function($) {
    let qs = decodeURIComponent(window.location.search);
    qs = qs.substring(1);
    let n = qs.indexOf('email-to=');
    if(n > -1) {
        n += 9;
        let emailto = qs.substring(n);
        let selector = "#email-select option[value='" + emailto + "']";
        $(selector).attr('selected', 'selected');
    }
});
