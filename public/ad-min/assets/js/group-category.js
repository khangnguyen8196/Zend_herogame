$(function () {
    pages.groupCategory.init();
});

if (!pages) {
    var pages = {};
}
pages = $.extend(pages, {
	groupCategory: {
        init: function () {
        	var me = this;
        	pages.common.setupCheckbox();
        	pages.common.setupDatePicker();
        	//setup fancybox
        	$('[data-popup="lightbox"]').fancybox({});
        	$('.select-search').select2();
        	if( currController == 'group-category' && currAction == 'index'){
        		me.initDatatable();
        		$(document).on('click', '.seach-form', {}, function ( ) {
        			pages.common.executeSearchForm('searchGroupCategory','groupCategoryTable');
        		});
        	}
        	if( currController == 'group-category' && currAction == 'detail'){
	        	me.initValidation();
	        	$(document).on('click', '.submit-btn', {}, function ( ) {
	                if ( pages.validation.validator['#groupCategoryDetailForm'].form() == false ) {
	                    return false;
	                }
	                $(".submit-btn").submit();
	            });
                $(document).on('change', '#url_slug', {}, function ( ) {
        			var value = $(this).val();
        			value = pages.common.string_to_slug(value);
        			$(this).val(value);
        		});
        	}
        	$(document).on('click', '.media-select-image', {}, function ( ) {
                var url = $(this).attr('data-src');
                var frontUrl = frontLink;
                $('#og_image').val( frontUrl.substring(0, frontUrl.length-1) + url );
                $('.close-media-dialog').click();
            });
        	$(document).on('click', '#select-media', {}, function ( ) {
        		pages.groupCategory.showMedia();
            });
        },
        initValidation: function(){
        	var loptions = {
                    rules: {
                    	name: {
                            required: true
                        },
        				url_slug:{
                        	required: true
        				}
                    },
                    messages: {
                    	name: {
                            required: $("#name").attr('data-msg')
                        },
                        url_slug: {
                            required: $("#url_slug").attr('data-msg')
                        }
                    }
                };
        	 pages.validation.setupValidation("#groupCategoryDetailForm", loptions);
             
        },
        initDatatable: function(){
        	var aoColumns = [
	    	                 {"data": "id"},
	    	                 { "data": "name" },
	    	                 { "data": "url_slug" },
	    	                 { "data": "image" },
	    	                 { "data": "Action_Table"}
	    	];
	        var columnDefs = [
	  						{
	  						    "render": function (data, type, row) {
	  						    	return row["name"];
	  						    },
	  						    "orderable": true,
	  						    targets: 1
	  						},
	  						{
	  							"render": function ( data, type, row ) {
	  								var img = '';
									if( pages.core.isDefined( row['image'] ) && row['image'] != null ){
										var img = '<a href="/upload/images'+ row['image']+'" data-popup="lightbox">'
				                        	+'<img src="/upload/images'+ row['image']+'" alt="" class="img-rounded img-preview">'
				                        	+'</a>';
									}
									return img;
								},
								"targets": 3,
								"orderable": true,
								"data": "name"
	  						},
	  						{
	                    	 "render": function (data, type, row) {
	                             var action = '<ul class="icons-list" >' +
	                                     '<li class="dropdown" >' +
	                                     '<a href="#" class="dropdown-toggle" data-toggle="dropdown">' +
	                                     '<i class="icon-menu9"> </i></a>' +
	                                     '<ul class="dropdown-menu dropdown-menu-right">';
	                             action += '<li> <a href="/admin/group-category/detail/id/'+row.id+'" > <i class="icon-pencil3"></i> '+ translate('edit')+'</a > </li>';
	                             action += '<li> <a onclick="pages.groupCategory.deletegroupCategory('+  row.id +')" > <i class="icon-bin"></i> '+ translate('delete')+'</a > </li></ul></li></ul>';
	                             return 	action;
	                         },
	                         "className": "text-center",
	                         "targets": 4,
	                         "orderable": false,
	                         "data": "Action_Table"
	  					 }
	  	    ];
	        pages.common.setupDataTable( "#groupCategoryTable", "/admin/group-category/list", aoColumns, columnDefs, {order:[[ 0, "desc" ]]});
        },
        deletegroupCategory: function( id ){
        	bootbox.confirm('Bạn có muốn xóa group này không', function(result) {
	            if( result){
	            	$.ajax({
	                    'url': '/admin/group-category/delete',
	                    'type': 'GET',
	                    'data': {id: id},
	                    beforeSend: function () {
	                    	
	                    },
	                    success: function (data) {
	                        if( data.Code > 0 ){
	                            if ($("#groupCategoryTable").length > 0) {
	                            	bootbox.alert('Xóa thành công');
	                                var t = $("#groupCategoryTable").DataTable();
	                                t.draw();
	                            }
	                        }else{
	                        	bootbox.alert('Xóa thất bại');
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