$(function () {
    pages.productColor.init();
});

if (!pages) {
    var pages = {};
}
pages = $.extend(pages, {
	productColor: {
        init: function () {
        	var me = this;
        	me.initValidation();
        	$(document).on('click', '.submit-btn', {}, function ( ) {
                if ( pages.validation.validator['#productColorDetailForm'].form() == false ) {
                    return false;
                }
                $(".submit-btn").submit();
            });
        },
        initValidation: function(){
        	var loptions = {
                    rules: {
                    	color_name: {
                            required: true
                        }
                    },
                    messages: {
                    	key: {
                            required: $("#color_name").attr('data-msg')
                        }
                    }
                };
        	 pages.validation.setupValidation("#productColorDetailForm", loptions);
             
        },
        deleteColor: function( id ){
        	bootbox.confirm('Bạn Có Muốn Xóa Màu Này', function(result) {
	            if( result){
	            	$.ajax({
	                    'url': '/admin/product-color/delete',
	                    'type': 'GET',
	                    'data': {id: id},
	                    beforeSend: function () {
	                    	
	                    },
	                    success: function (data) {
	                        if( data.Code > 0 ){
	                            if ($("#productColorTable").length > 0) {
	                            	bootbox.alert('Xóa màu Thành Công');
	                                window.location.reload();
	                            }
	                        }else{
	                        	bootbox.alert('Xóa Màu Thất Bại');
	                        }
	                    },
	                    error: function (data) {

	                    }
	                });
	            }
            });
        }
    }
});