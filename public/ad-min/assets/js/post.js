$(function () {
    pages.post.init();
});

if (!pages) {
    var pages = {};
}
pages = $.extend(pages, {
	post: {
		lang: '',
        init: function () {
        	var me = this;
        	pages.common.setupDatePicker();
        	pages.common.setupCheckbox();
        	$('.select-search').select2();
        	$('[data-popup="lightbox"]').fancybox({});
        	if( currController == 'post' && currAction == 'index'){
        		me.initDatatable();
        		$(document).on('click', '.seach-form', {}, function ( ) {
        			pages.common.executeSearchForm('searchPost','postTable');
        		});
        	}
        	if( currController == 'post' && currAction == 'detail'){
        		me.initValidation();
        		$(document).on('click', '.submit-btn', {}, function ( ) {
	                if ( pages.validation.validator['#postDetailForm'].form() == false ) {
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
   					   filebrowserBrowseUrl : '/ad-min/assets/js/libs/kcfinder/browse.php?opener=ckeditor&type=files',
   					   filebrowserImageBrowseUrl : '/ad-min/assets/js/libs//kcfinder/browse.php?opener=ckeditor&type=images',
   					   filebrowserUploadUrl : '/ad-min/assets/js/libs//kcfinder/upload.php?opener=ckeditor&type=files',
   					   filebrowserImageUploadUrl : '/ad-min/assets/js/libs/kcfinder/upload.php?opener=ckeditor&type=images',
   					   filebrowserFlashUploadUrl : '/ad-min/assets/js/libs/kcfinder/upload.php?opener=ckeditor&type=flash',
          		    });
        		});
        		//
        		$(document).on('change', '#url_name', {}, function ( ) {
        			var value = $(this).val();
        			value = pages.common.string_to_slug(value);
        			$(this).val(value);
        		});
        		
        	}
        	if( currController == 'post' && currAction == 'media'){
        		pages.common.setupMasonry();
        		$(document).on('click', '.choose-img', function() {
                   url = $(this).attr('data-src');
                   var functionNum = $("#CKEditorFuncNum").val();
                   window.opener.CKEDITOR.tools.callFunction(functionNum, url, '');
                   window.close();
                });
        	}
        	$(document).on('click', '.media-select-image', {}, function ( ) {
                var url = $(this).attr('data-src');
                var frontUrl = frontLink;
                $('#og_image').val( frontUrl.substring(0, frontUrl.length-1) + url );
                $('.close-media-dialog').click();
            });
        	$(document).on('click', '#select-media', {}, function ( ) {
                pages.post.showMedia();
            });
        	
        	if (currAction  == "detail") {
                pages.post.getListPost();
				pages.post.getListProduct()
            }
        },
        showMedia: function () {
            $.ajax({
                url: '/admin/media/get-list-media',
                type: 'GET',
                data: {},
                beforeSend: function ( ) {
                },
                success: function (data) {
                    if (data.Code > 0) {
                        $('#modal_media').html(data.Data);
                        pages.common.setupMasonry();
                        $('#modal_media').modal({keyboard: false, show: true, backdrop: 'static'});
                    } else {
                    }
                },
                error: function (error) {
                }
            });
        },
        initValidation: function(){
        	var loptions = {
                    rules: {
                    	title: {
                            required: true
                        },
                        url_name: {
			                required: true
			            }
			       
                        
                    },
                    messages: {
                    	title: {
                            required: $("#title").attr('data-msg')
                        },
                        url_name: {
                            required: $("#url_name").attr('data-msg')
                        }
                    }
                };
        	 pages.validation.setupValidation("#postDetailForm", loptions);
             
        },
        initDatatable: function(){
        	var me = this;
        	var aoColumns = [
	    	                 {"data": "post_id"},
	    	                 { "data": "title" },
	    	                 { "data": "url_name" },
	    	                 { "data": "created_at" },
	    	                 { "data": "created_by" },
	    	                 { "data": "updated_at" },
	    	                 { "data": "updated_by" },
	    	                 { "data": "status" },
	    	                 { "data": "Action_Table"}
	    	];
	        var columnDefs = [
                      {
                    	  "render": function ( data, type, row ) {
                    		  if( pages.core.isDefined(row['title'])){
                    			  return row['title'];
                    		  } else {
                    			  return '';
                    		  }
                    	  },
                    	  "targets": 1,
                    	  "orderable": true,
                    	  "data": "title"
                      },
                      {
                        	"render": function ( data, type, row ) {
                        		if( pages.core.isDefined(data)){
                        			return pages.datetime.parseIsoDatetimeUTC(data,true,'dd/mm/yyyy');
                        		} else {
                        			return '-';
                        		}
                        	},
                        	"targets": 3,
  							"orderable": true,
  							"data": "created_at"
						},
	                     {
	                        	"render": function ( data, type, row ) {
	                        		if( pages.core.isDefined(data)){
	                        			return pages.datetime.parseIsoDatetimeUTC(data,true,'dd/mm/yyyy');
	                        		} else {
	                        			return '-';
	                        		}
	                        	},
	                        	"targets": 5,
	  							"orderable": true,
	  							"data": "updated_at"
	                     },
	                     {
	  							"render": function (data, type, row) {
	  		                        var label = '';
	  		                        if( row["status"] == 1){
	  		                            label = '<span class="label label-success">'+translate('active')+'</span>';
	  		                        } else if( row["status"] == -1 ){
	  		                             label = '<span class="label label-default">'+translate('disabled')+'</span>';
	  		                        }
	  		                        return label;
	  		                    },
	  		                    orderable: true,
	  		                    targets: 7
	  					},
	  					 {
	                    	 "render": function (data, type, row) {
	                             var action = '<ul class="icons-list" >' +
	                                     '<li class="dropdown" >' +
	                                     '<a href="#" class="dropdown-toggle" data-toggle="dropdown">' +
	                                     '<i class="icon-menu9"> </i></a>' +
	                                     '<ul class="dropdown-menu dropdown-menu-right">';
	                             action += '<li> <a href="/admin/post/detail/id/'+row.post_id+'" > <i class="icon-pencil3"></i> '+ translate('edit')+'</a > </li>';
	                             action += '<li> <a onclick="pages.post.deletePost('+  row.post_id +')" > <i class="icon-bin"></i> '+ translate('delete')+'</a > </li></ul></li></ul>';
	                             return 	action;
	                         },
	                         "className": "text-center",
	                         "targets": 8,
	                         "orderable": false,
	                         "data": "Action_Table"
	  					 }
	  	    ];
	        pages.common.setupDataTable( "#postTable", "/admin/post/list/", aoColumns, columnDefs, {order:[[ 6, "desc" ]]});
        },
        deletePost: function( id ){
        	bootbox.confirm(translate('are-you-sure-want-to-delete-this-post'), function(result) {
	            if( result){
	            	$.ajax({
	                    'url': '/admin/post/delete',
	                    'type': 'GET',
	                    'data': {id: id},
	                    beforeSend: function () {
	                    	
	                    },
	                    success: function (data) {
	                        if( data.Code > 0 ){
	                            if ($("#postTable").length > 0) {
	                            	bootbox.alert(translate('delete-post-success'));
	                                var t = $("#postTable").DataTable();
	                                t.draw();
	                            }
	                        }else{
	                        	bootbox.alert(translate('delete-post-fail'));
	                        }
	                    },
	                    error: function (data) {

	                    }
	                });
	            }
            });
        },
        getListPost: function () {
            $.ajax({
                'url': '/admin/post/get-list-post',
                'type': 'GET',
                'data': {id: $("#id").val(), selectRelativePost : $("#selected_relative_post").val()},
                beforeSend: function () {

                },
                success: function (data) {
                    if (data.Code > 0 && data.Data != "") {
                        $("#relative_post").html(data.Data);
                        $('#relative_post').select2({
                            minimumResultsForSearch: "-1",
                            width: '100%',
                        });
                    }
                },
                error: function (data) {

                }
            });
        },
		getListProduct: function () {
            $.ajax({
                'url': '/admin/product/get-list-product',
                'type': 'GET',
                'data': {id: $("#id").val(), selectRelativeProduct : $("#selected_relative_product").val()},
                beforeSend: function () {

                },
                success: function (data) {
                    if (data.Code > 0 && data.Data != "") {
                        $("#relative_product").html(data.Data);
                        $('#relative_product').select2({
                            placeholder: "Những sản phẩm liên quan...",
                            minimumResultsForSearch: "-1",
                            width: '100%',
                        });
                    }
                },
                error: function (data) {

                }
            });
        },
    }
});