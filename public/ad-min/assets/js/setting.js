$(function () {
    pages.setting.init();
});

if (!pages) {
    var pages = {};
}
pages = $.extend(pages, {
	setting: {
        init: function () {
        	var me = this;
        	me.initValidation();
			$(document).on('change', '#type', {}, function ( ) {
    			if( $(this).val() == 1 ){
    				$('.container-value').show();
    				$('.container-hide-show').hide();
    				$('.container-value-web').hide();
    			} else if( $(this).val() == 2 ){
    				$('.container-value').hide();
    				$('.container-hide-show').show();
    				$('.container-value-web').hide();
    			}  else if( $(this).val() == 3 ){
    				$('.container-value').hide();
    				$('.container-hide-show').hide();
    				$('.container-value-web').show();
    			}
    		});
        	$(document).on('click', '.submit-btn', {}, function ( ) {
                if ( pages.validation.validator['#settingDetailForm'].form() == false ) {
                    return false;
                }
                $(".submit-btn").submit();
            });
        	$.each($('.rich-editor'),function(){
    			var id = $(this).attr('id');
    			var h = $(this).attr('data-height');
    			 CKEDITOR.replace( id, {
      		       height: h,
      		       width: '100%',
					   'image_previewText': '  ',
					   'allowedContent': true,
					   'enterMode' : CKEDITOR.ENTER_BR,
					   
					 filebrowserBrowseUrl : '/ad-min/assets/js/libs//kcfinder/browse.php?opener=ckeditor&type=files',
					 filebrowserImageBrowseUrl : '/ad-min/assets/js/libs//kcfinder/browse.php?opener=ckeditor&type=images',
					 filebrowserUploadUrl : '/ad-min/assets/js/libs//kcfinder/upload.php?opener=ckeditor&type=files',
					 filebrowserImageUploadUrl : '/ad-min/assets/js/libs//kcfinder/upload.php?opener=ckeditor&type=images',
					 filebrowserFlashUploadUrl : '/ad-min/assets/js/libs//kcfinder/upload.php?opener=ckeditor&type=flash',
      		    });
    		});
        	$('#type').change();
        	
        	
        	
        },
        initValidation: function(){
        	var loptions = {
                    rules: {
                    	key: {
                            required: true
                        }
                    },
                    messages: {
                    	key: {
                            required: $("#key").attr('data-msg')
                        }
                    }
                };
        	 pages.validation.setupValidation("#settingDetailForm", loptions);
             
        },
        deleteSetting: function( key ){
        	bootbox.confirm(translate('are-you-sure-want-to-delete-this-setting'), function(result) {
	            if( result){
	            	$.ajax({
	                    'url': '/admin/setting/delete',
	                    'type': 'GET',
	                    'data': {key: key},
	                    beforeSend: function () {
	                    	
	                    },
	                    success: function (data) {
	                        if( data.Code > 0 ){
	                            if ($("#settingTable").length > 0) {
	                            	bootbox.alert(translate('delete-setting-success'));
	                                var t = $("#settingTable").DataTable();
	                                t.draw();
	                            }
	                        }else{
	                        	bootbox.alert(translate('delete-setting-fail'));
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