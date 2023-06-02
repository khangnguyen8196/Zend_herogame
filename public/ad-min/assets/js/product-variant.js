$(function () {
    pages.productVariant.init();
});

if (!pages) {
    var pages = {};
}
pages = $.extend(pages, {
	productVariant: {
        init: function () {
        	var me = this;
        	me.initValidation();
        	$(document).on('click', '.submit-btn', {}, function ( ) {
                if ( pages.validation.validator['#productVariantDetailForm'].form() == false ) {
                    return false;
                }
                $(".submit-btn").submit();
            });
        },
        initValidation: function(){
        	var loptions = {
                    rules: {
                    	variant_name: {
                            required: true
                        },
						variant_price: {
                            required: true
                        },
						variant_price_sales: {
                            required: true
                        },
						product_id:{
							required:true
						}
                    },
                    messages: {
                    	key: {
                            required: $("#variant_name").attr('data-msg'),
							required: $("#variant_price").attr('data-msg'),
							required: $("#variant_price_sales").attr('data-msg'),
							required: $("#product_id").attr('data-msg')
                        }
                    }
                };
        	 pages.validation.setupValidation("#productVariantDetailForm", loptions);
             
        },
        deleteVariant: function( id ){
        	bootbox.confirm('Bạn Có Muốn Xóa Loại Này', function(result) {
	            if( result){
	            	$.ajax({
	                    'url': '/admin/product-variant/delete',
	                    'type': 'GET',
	                    'data': {id: id},
	                    beforeSend: function () {
	                    	
	                    },
	                    success: function (data) {
	                        if( data.Code > 0 ){
	                            if ($("#productVariantTable").length > 0) {
	                            	bootbox.alert('Xóa Thành Công');
	                                window.location.reload();
	                            }
	                        }else{
	                        	bootbox.alert('Xóa Thất Bại');
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