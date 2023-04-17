$(function () {
    pages.media.init();
});

if (!pages) {
    var pages = {};
}
pages = $.extend(pages, {
	media: {
        init: function () {
        	var me = this;
        	me.initDatatable();
        	me.initValidation();
        	//setup fancybox
        	$('[data-popup="lightbox"]').fancybox({});
        	// setup clean path
        	$(document).on('change', '#mediaDetailForm #url' , {}, function () {
	    		if( $(this).val().length > 0 ){ // if have value
	    			$('.clear_image').removeClass('hidden');
	    		} else {
	    			$('.clear_image').addClass('hidden');
	    		}
		    });
			// set up event for clear button
			$(document).on('click',"#clearPath", {}, function () {
	    		$("#mediaDetailForm input[name=url]").val('');
	    		$('.clear_image').addClass('hidden');
		    });
			
			$(document).on('click', '.seach-form', {}, function ( ) {
    			pages.common.executeSearchForm('searchForm','mediaTable');
    		});
			
        	pages.common.setupDatePicker();
        	$(document).on('click', '.seach-form', {}, function ( ) {
    			pages.common.executeSearchForm('searchForm','mediaTable');
    		});
        	$(document).on('click', '.submit-btn', {}, function ( ) {
                if ( pages.validation.validator['#mediaDetailForm'].form() == false ) {
                    return false;
                }
                $(".submit-btn").submit();
            });
        	
        },
        initValidation: function(){
        	var loptions = {
                    rules: {
                    	name: {
                            required: true
                        }
                    },
                    messages: {
                    	name: {
                            required: $("#name").attr('data-msg')
                        }
                    }
                };
        	 pages.validation.setupValidation("#mediaDetailForm", loptions);
             
        },
        initDatatable: function(){
        	var aoColumns = [
	    	                 { "data": "media_id"},
	    	                 { "data": "url_thumnail"},
	    	                 { "data": "type"},
	    	                 { "data": "created_by" },
	    	                 { "data": "updated_by" },
	    	                 { "data": "created_at" },
	    	                 { "data": "updated_at" },
	    	                 { "data": "status" },
	    	                 { "data": "Action_Table"}
	    	];
	        var columnDefs = [
							{
								"render": function ( data, type, row ) {
									if( pages.core.isDefined( row['url_thumnail'] ) ){
										var img = '<a href="/upload/images'+ row['url']+'" data-popup="lightbox">'
				                        	+'<img style="width:100px;" src="/upload/images'+ row['url']+'" alt="" class="img-rounded img-preview">'
				                        	+'</a>';
									}
									return img;
								},
								"targets": 1,
								"orderable": true,
								"data": "url_thumnail"
							},
							{
								"render": function ( data, type, row ) {
									var disType = '';
									if( pages.core.isDefined( data ) ){
										if( row['type'] == 1 ){
											disType = 'Hình Ảnh';
										} else if( row['type'] == 2 ){
											disType = 'Video';
										} 
									}
									return disType;
								},
								"targets": 2,
								"orderable": true,
								"data": "type"
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
	                        	"targets": 7,
	  							"orderable": true,
	  							"data": "status"
	                     },
	  						{
	                        	"render": function ( data, type, row ) {
	                        		if( pages.core.isDefined(data)){
	                        			return pages.datetime.parseIsoDatetimeUTC(data,true,'dd/mm/yyyy');
	                        		} else {
	                        			return '-';
	                        		}
	                        	},
	                        	"targets": 6,
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
	                             var action = '<ul class="icons-list" >' +
	                                     '<li class="dropdown" >' +
	                                     '<a href="#" class="dropdown-toggle" data-toggle="dropdown">' +
	                                     '<i class="icon-menu9"> </i></a>' +
	                                     '<ul class="dropdown-menu dropdown-menu-right">';
	                             action += '<li> <a href="/admin/media/detail/id/'+row.media_id+'" > <i class="icon-pencil3"></i> '+ translate('edit')+'</a > </li>';
	                             action += '<li> <a onclick="pages.media.deleteMedia('+  row.media_id +')" > <i class="icon-bin"></i> '+ translate('delete')+'</a > </li>';
	                             action += '<li> <a onclick="pages.media.copy(\''+  row.url +'\')" > <i class="icon-copy"></i>Sao Chép Liên Kết</a > </li></ul></li></ul>'
	                             return 	action;
	                         },
	                         "className": "text-center",
	                         "targets": 8,
	                         "orderable": false,
	                         "data": "Action_Table"
	                     }
	  	    ];
	        pages.common.setupDataTable( "#mediaTable", "/admin/media/list", aoColumns, columnDefs, {order:[[ 0, "desc" ]]});
        },
        copy: function(text){
        	 window.prompt("Copy to clipboard: Ctrl+C, Enter", '/upload/images'+text);
        },
        deleteMedia: function( id ){
        	bootbox.confirm(translate('are-you-sure-want-to-delete-this-media'), function(result) {
	            if( result){
	            	$.ajax({
	                    'url': '/admin/media/delete',
	                    'type': 'GET',
	                    'data': {id: id},
	                    beforeSend: function () {
	                    	
	                    },
	                    success: function (data) {
	                        if( data.Code > 0 ){
	                            if ($("#mediaTable").length > 0) {
	                            	bootbox.alert(translate('delete-media-success'));
	                                var t = $("#mediaTable").DataTable();
	                                t.draw();
	                            }
	                        }else{
	                        	bootbox.alert(translate('delete-media-fail'));
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