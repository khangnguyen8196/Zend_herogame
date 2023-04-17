/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(function () {
    pages.tintuc.init();
});

if (!pages) {
    var pages = {};
}
pages = $.extend(pages, {
    tintuc: {
        init: function () {
        	$(document).on('click', '.view-more', {}, function () {
                var me = this;
                $.ajax({
                        url: "/pages/load-view-more",
                        type: 'POST',
                        data: { start: $(this).attr('data-start'), danhMuc: $('#danhMuc').val() },
                        beforeSend: function () {
                        	$('.view-more').hide();
                        	$('#loading').show();
                        },
                        success: function (data) {
                            if( data.Code == 1 ){
                            	//
                            	if(  data.Data.html != '' ){
                            		$('#loading').hide();
                            		$('#post-list-container').append( data.Data.html );
                            		pages.common.refreshLazyImage("#post-list-container");
                            	}
                            	if( data.Data.next == true ){
                            		$('.view-more').show();
                            		$('.view-more').attr('data-start', parseInt($('.view-more').attr('data-start')) + parseInt($('#maxPostConfig').val() ));
                            	} else {
                            		$('.view-more').hide();
                            	}
                            } else {
                            	//
                            	$('.view-more').show();
                            	$('#loading').hide();
                            }
                        },
                        error: function () {
                        	$('.view-more').show();
                        	$('#loading').hide();
                        }
                    });
            
        	});
        },
    }
});