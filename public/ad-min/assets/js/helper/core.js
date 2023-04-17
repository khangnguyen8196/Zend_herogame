$(function () {
    pages.core.init();
});

if (!pages) {
    var pages = {};
}
pages = $.extend(pages, {
	core: {
        init: function () {
        	$(document.body).on('hide.bs.modal', function () {
        	    $('body').css('padding-right', '0px');
        	});
        },
        isDefined: function(obj) {
    		return typeof obj !== 'undefined' && obj !== null && obj !== undefined;
    	}
    }
});