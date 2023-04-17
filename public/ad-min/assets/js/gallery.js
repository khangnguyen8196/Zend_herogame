$(function () {
    pages.gallery.init();
});

if (!pages) {
    var pages = {};
}
pages = $.extend(pages, {
	gallery: {
		isUpdate: -1,
        init: function () {
        	var me = this;
        	pages.common.setupDatePicker();
        	$('.select-search').select2();
        	$('[data-popup="lightbox"]').fancybox({});
        	if( currController == 'gallery' && currAction == 'index'){
        		me.initDatatable();
        		$(document).on('click', '.seach-form', {}, function ( ) {
        			pages.common.executeSearchForm('searchGallery','galleryTable');
        		});
        	}
        	if( currController == 'gallery' && currAction == 'detail'){
        		me.initValidation();
        		$(document).on('click', '.submit-btn', {}, function ( ) {
	                if ( pages.validation.validator['#galleryDetailForm'].form() == false ) {
	                    return false;
	                }
	                $(".submit-btn").submit();
	            });
        		//
        		$(document).on('change', '#url_name_vi', {}, function ( ) {
        			var value = $(this).val();
        			value = pages.common.string_to_slug(value);
        			$(this).val(value);
        		});
        		$(document).on('change', '#url_name_en', {}, function ( ) {
        			var value = $(this).val();
        			value = pages.common.string_to_slug(value);
        			$(this).val(value);
        		});
        		//
        		$(document).on('click', '#select-media', {}, function ( ) {
                    me.showMedia();
                });
        		$(document).on('click', '.edit-item', {}, function ( ) {
        			me.isUpdate = $(this).attr('data-update');
                    me.showMedia();
                });
        		$(document).on('click', '.remove-item', {}, function ( ) {
                    var removeClass = $(this).attr('data-remove');
                    if( $('.'+removeClass).length > 0 ){
                    	$('.'+removeClass).remove();
                    }
                });
        		
        		$(document).on('click', '.media-select-image', {}, function ( ) {
        			var id = $(this).attr('data_id');
        			
        			if( me.isUpdate == -1 ){
        				if( $('.img-item-'+id).length > 0) {
            				$('.close-media-dialog').click();
            				bootbox.alert(translate('this-image-is-already-exists'));
            			} else {
            				var tpl ='';
                            tpl +='<div class="col-lg-2 col-sm-4 parent img-item-'+id+'">';
                            tpl +='<div class="thumbnail">';
                            tpl +='<div class="thumb">';
                            tpl +='<img class="img-media img-thumbnail-item" src="'+$(this).attr('data-src-thumb')+'" alt="">';
                            tpl +='<div class="caption-overflow">';
                            tpl +='<span>';
                            tpl +='<a class="btn border-white text-white btn-flat btn-icon btn-rounded ml-5 container-image" href="'+$(this).attr('data-src')+'" data-popup="lightbox">';
                            tpl +='<i class="icon-plus3"></i>';
                            tpl +='</a>';
                            tpl +='<a href="#" class="btn border-white text-white btn-flat btn-icon btn-rounded ml-5">';
                            tpl +='<i class="icon-cross2 remove-item" data-remove="img-item-'+id+'"></i></a>';
                            tpl +='<a href="#" class="btn border-white text-white btn-flat btn-icon btn-rounded ml-5">';
                            tpl +='<i class="icon-pencil7 edit-item" data-update="'+id+'"></i></a>';
                            tpl +='</span>';
                            tpl +='</div>';
                            tpl +='</div>';
                            tpl +='</div>';
                            tpl +='<input type="hidden" class="hiden-item" name="media_id[]" value="'+id+'">';
                            tpl +='</div>';
                            $('.list-image').append(tpl);
                            
            			}
        			} else {
        				// update
        				var oldId = me.isUpdate;
        				if( $('.img-item-'+id).length > 0 && id != oldId) {
            				$('.close-media-dialog').click();
            				bootbox.alert(translate('this-image-is-already-exists'));
            			} else {
            				$('.img-item-'+oldId+' .img-media').attr('src',$(this).attr('data-src-thumb'));
            				$('.img-item-'+oldId+' .container-image').attr('href',$(this).attr('data-src'));
            				$('.img-item-'+oldId+' .remove-item').attr('data-remove',"img-item-"+id);
            				$('.img-item-'+oldId+' .edit-item').attr('data-update',id);
            				$('.img-item-'+oldId+' .hiden-item').val(id);
            				$('.img-item-'+oldId).addClass("img-item-"+id);
            				$('.img-item-'+oldId).removeClass('.img-item-'+oldId);
            			}
        			}
        			me.isUpdate = -1;
        			$('.close-media-dialog').click();
                    
                });
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
                    	title_en: {
                            required: true
                        },
                        url_name_en: {
			                required: true
			            }
                    },
                    messages: {
                    	title_en: {
                            required: $("#title_en").attr('data-msg')
                        },
                        url_name_en: {
                            required: $("#url_name_en").attr('data-msg')
                        },
                    }
                };
        	 pages.validation.setupValidation("#galleryDetailForm", loptions);
             
        },
        initDatatable: function(){
        	var aoColumns = [
	    	                 {"data": "gallery_id"},
	    	                 { "data": "title" },
	    	                 { "data": "url_name" },
	    	                 { "data": "category_id" },
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
                    		  if( pages.core.isDefined(row['category_name'])){
                    			  return row['category_name'];
                    		  } else {
                    			  return '';
                    		  }
                    	  },
                    	  "targets": 3,
                    	  "orderable": true,
                    	  "data": "category_id"
                      },
                      {
                    	  "render": function ( data, type, row ) {
                    		  if( pages.core.isDefined(data) && data !='0000-00-00 00:00:00'){
                    			  return pages.datetime.parseIsoDatetimeUTC(data,true,'dd/mm/yyyy');
                    		  } else {
                    			  return '-';
                    		  }
                    	  },
                    	  "targets": 4,
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
                        	"targets": 6,
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
	  		                    targets: 8
	  					},
	  					 {
	                    	 "render": function (data, type, row) {
	                             var action = '<ul class="icons-list" >' +
	                                     '<li class="dropdown" >' +
	                                     '<a href="#" class="dropdown-toggle" data-toggle="dropdown">' +
	                                     '<i class="icon-menu9"> </i></a>' +
	                                     '<ul class="dropdown-menu dropdown-menu-right">';
	                             action += '<li> <a href="/gallery/detail/id/'+row.gallery_id+'" > <i class="icon-pencil3"></i> '+ translate('edit')+'</a > </li>';
	                             action += '<li> <a onclick="pages.gallery.deleteGallery('+  row.gallery_id +')" > <i class="icon-bin"></i> '+ translate('delete')+'</a > </li></ul></li></ul>';
	                             return 	action;
	                         },
	                         "className": "text-center",
	                         "targets": 9,
	                         "orderable": false,
	                         "data": "Action_Table"
	  					 }
	  	    ];
	        pages.common.setupDataTable( "#galleryTable", "/admin/gallery/list", aoColumns, columnDefs, {order:[[ 4, "desc" ]]});
        },
        deleteGallery: function( id ){
        	bootbox.confirm(translate('are-you-sure-want-to-delete-this-gallery'), function(result) {
	            if( result){
	            	$.ajax({
	                    'url': '/admin/gallery/delete',
	                    'type': 'GET',
	                    'data': {id: id},
	                    beforeSend: function () {
	                    	
	                    },
	                    success: function (data) {
	                        if( data.Code > 0 ){
	                            if ($("#galleryTable").length > 0) {
	                            	bootbox.alert(translate('delete-gallery-success'));
	                                var t = $("#galleryTable").DataTable();
	                                t.draw();
	                            }
	                        }else{
	                        	bootbox.alert(translate('delete-gallery-fail'));
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