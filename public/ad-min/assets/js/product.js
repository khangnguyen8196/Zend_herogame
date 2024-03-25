$(function () {
    pages.product.init();
});

if (!pages) {
    var pages = {};
}
pages = $.extend(pages, {
	product: {
		lang: '',
		isUpdate: -1,
		isSelectOgImg: false,
        init: function () {
            
        	var me = this;
        	pages.common.setupDatePicker();
        	pages.common.setupCheckbox();
        	$('.select-search').select2();
        	$('#product_color').select2();
        	$('[data-popup="lightbox"]').fancybox({});
        	if( currController == 'product' && currAction == 'index'){
        		me.initDatatable();
        		$(document).on('click', '.seach-form', {}, function ( ) {
        			pages.common.executeSearchForm('searchPost','productTable');
        		});
        	}
        	$("#addMoreGallery").click(function(){
        		var defaultOption = '<option value="1">Màu Mặc Định - Không Màu</option>';
                var option = '';
                $("#product_color option:selected").each(function () {
                    option += '<option value="'+ $(this).val() +'">'+ $(this).text() +'</option>';
                });
                if( option.length == 0 ){
                    option = defaultOption;
                }
        		$(".list-file-gallery").append(
        				'<div class="form-group file-item">'+
							'<div class="col-lg-7">' +
								'<input type="file" class="file-styled form-control" name="gallery[]" accept="image/*"/>' +
							'</div>'+
                    		'<div class="col-lg-2">' +
								'<select class="form-control image_color" name="image_color[]">' +
									option +
								'</select>'+
							'</div>'+
                			'<div class="col-lg-2">' +
								'<button type="button"  class="btn btn-alert remove" style="margin-right: 11px;">x</button>' +
							'</div>' +
						'</div>'
        		);
        	});
            $("#product_color").change( function(){
                var defaultOption = '<option value="1">Màu Mặc Định - Không Màu</option>';
                var option = '';
                $("#product_color option:selected").each(function () {
                    option += '<option value="'+ $(this).val() +'">'+ $(this).text() +'</option>';
                });
                if( option.length == 0 ){
                    option = defaultOption;
                }
                $(".image_color").each(function () {
                    var valueSelect = $(this).val();
                    $(this).html(option);
                    $(this).val(valueSelect);
                    console.log($(this).val());
                    if( $(this).val() == null ){
                        $(this).prop("selectedIndex", 0);
                    }
                });
			});
        	$(document).on('click', '.remove-item-gallery', function() {
        		var val = $(this).attr('data-remove');
        		$(".list-delete-img").append('<input type="hidden" name="gallery_delete[]" value="'+val+'">');
        		$(this).parents('.img-item').remove();
        	});
        	$(document).on('click', '.remove', function() {
        		$(this).parents('.file-item').remove();
        	});
        	if( currController == 'product' && currAction == 'detail'){
                    $(".datetimepicker").datetimepicker({
                            format: "yyyy-mm-dd hh:ii:ss"
                        });
        		$('#color').colorpicker();
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
   					   
   					 filebrowserBrowseUrl : '/ad-min/assets/js/libs//kcfinder/browse.php?opener=ckeditor&type=files',
   					 filebrowserImageBrowseUrl : '/ad-min/assets/js/libs//kcfinder/browse.php?opener=ckeditor&type=images',
   					 filebrowserUploadUrl : '/ad-min/assets/js/libs//kcfinder/upload.php?opener=ckeditor&type=files',
   					 filebrowserImageUploadUrl : '/ad-min/assets/js/libs//kcfinder/upload.php?opener=ckeditor&type=images',
   					 filebrowserFlashUploadUrl : '/ad-min/assets/js/libs//kcfinder/upload.php?opener=ckeditor&type=flash',
          		    });
        		});	
        		//
        		$(document).on('change', '#url', {}, function ( ) {
        			var value = $(this).val();
        			value = pages.common.string_to_slug(value);
        			$(this).val(value);
        		});
        		$(document).on('change', '#url_product', {}, function ( ) {
        			var value = $(this).val();
        			value = pages.common.string_to_slug(value);
        			$(this).val(value);
        		});
        		
        	}
        	if( currController == 'product' && currAction == 'media'){
        		pages.common.setupMasonry();
        		$(document).on('click', '.choose-img', function() {
                   url = $(this).attr('data-src');
                   var functionNum = $("#CKEditorFuncNum").val();
                   window.opener.CKEDITOR.tools.callFunction(functionNum, url, '');
                   window.close();
                });
        	}
        	$(document).on('click', '.select-media', {}, function ( ) {
        		if( $(this).hasClass('og-img') == true ){
        			me.isSelectOgImg = true;
        		}
                pages.product.showMedia();
            });
        	$(document).on('click', '.edit-item', {}, function ( ) {
        		pages.product.isUpdate = $(this).attr('data-update');
    			pages.product.showMedia();
            });
        	$(document).on('click', '.remove-item', {}, function ( ) {
                var removeClass = $(this).attr('data-remove');
                if( $('.'+removeClass).length > 0 ){
                	$('.'+removeClass).remove();
                }
            });
        	$(document).on('click', '.media-select-image', {}, function ( ) {
        		
        		if( me.isSelectOgImg == true ){
        			$("#og_image").val(window.location.origin+$(this).attr('data-src'));
        			me.isSelectOgImg = false;
        		} else {
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
                            tpl +='<a class="btn border-white text-white btn-flat btn-icon btn-rounded ml-5">';
                            tpl +='<i class="icon-cross2 remove-item" data-remove="img-item-'+id+'"></i></a>';
                            tpl +='<a class="btn border-white text-white btn-flat btn-icon btn-rounded ml-5">';
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
        		}
    			$('.close-media-dialog').click();
                
            });

			// add row
			var index = 0;
			$("#addRow").click(function () {
				var html = '';
				html += 	'<div class="inputFormRow" >';
				html += 		'<div class="form-group">';
				html += 			'<label class="control-label col-lg-2">Tên Loại</label>';
				html += 			'<div class="col-lg-2">';
				html += 				'<input type= "text" class="form-control variant-name" name="variant_name[]" data-name="variant_name[]" placeholder="Tên Loại" value="" data-msg="Vui Lòng Nhập Tên Loại" >';
				html += 			'</div>';
				html += 			'<label class="control-label col-lg-1">Giá</label>';
				html += 			'<div class="col-lg-2">';
				html += 				'<input type="tel" class="form-control variant-price" name="variant_price[]" data-name="variant_price[]" placeholder="Giá" value="" data-msg="Vui Lòng Nhập Giá Loại" oninput="this.value = this.value.replace(/[^0-9]/g, \'\');"/>';
				html += 			'</div>';
				html += 			'<label class="control-label col-lg-1">Giá Sales</label>';
				html += 			'<div class="col-lg-2">';
				html += 				'<input type="tel" class="form-control variant-price-sales" name="variant_price_sales[]" data-name="variant_price_sales[]" placeholder="Giá Sales" value="" data-msg="Vui Lòng Nhập Giá Sales Loại" oninput="this.value = this.value.replace(/[^0-9]/g, \'\');"/>';
				html += 			'</div>';
				html += 			'<input type="hidden"  name="variant_id[]" value="0">';
				html += 			'<div class="input-group-append">';
				html += 				'<button id="removeRow" type="button" class="btn btn-danger">Remove</button>';
				html += 			'</div>';
				html += 		'</div>';
				html += 		'<div class="form-group">';
				html += 			'<label class="control-label col-lg-2">Ảnh Loại Sản Phẩm</label>';
				// html += 			'<button type="button"  class="btn btn-primary add-img-variant" data-variant-id="' + index + '" style="margin-right: 11px;"><span aria-hidden="true"></span> + Thêm Ảnh</button>';
				html += 			'<div class="col-lg-8 list-file-variant-img" id="newVariantImg_' + index + '" style="margin-left: -10px;">';
				html += 				'<div class="form-group file-item">';
				html += 					'<div class="col-lg-7">';
				html +=                         '<input type="hidden"  name = "variant_image_id[]" value="0">',
				html += 						'<input type="file" class="file-styled form-control image_variant_error" name="url_image['+ (index + 1) +'][]" data-variant-id="' + index + '" accept="image/*" multiple="multiple"/>';
				html += 					'</div>';
				html += 					'<div class="col-lg-2">';
				html += 						'<button type="button"  class="btn btn-alert remove" style="margin-right: 11px;">x</button>';
				html += 					'</div>';
				html += 				'</div>';
				html += 			'</div>';
				html += 		'</div>';
				html += 	'</div>';
				$('#newRow').append(html);
				index++;
			});
			
			$(document).on("click", ".add-img-variant", function () {
				var variant_id = $(this).data('variant-id');
				$('#newVariantImg_' + variant_id).append(
				'<div class="form-group file-item-variant">' +
				'<div class="col-lg-7">' +
				'<input type="file" class="file-styled form-control" name="url_image[' + variant_id + '][]" data-variant-id="' + variant_id + '" accept="image/*" multiple="multiple" />' +
				'</div>' +
				'<div class="col-lg-2">' +
				'<button type="button"  class="btn btn-alert remove-variant-img" style="margin-right: 11px;">x</button>' +
				'</div>' +
				'</div>'
				);
			});
			// delete img variant
			$(document).on('click', '.remove-item-variant-img', function() {
				var variant_id = $('.list-delete-variant-img').data('variation-id');
        // 		var val = $(this).attr('data-remove-variant-img');
                var val = $(this).attr('data-id');
        		$('#delete-variant-img_'+variant_id).append('<input type="hidden" name="url_image_delete[]" value="'+val+'">');
        		$(this).parents('.img-item-variant').remove();
        	});
        	$(document).on('click', '.remove-variant-img', function() {
        		$(this).parents('.file-item-variant').remove();
        	});
			//end 

		   	$(document).on('click', '#removeRow', function () {
				$(this).closest('.inputFormRow').remove();
			});

	  	 	
			$(document).on('click', '.removeVariant', function() {	
        		var val = $(this).attr('data-id');
				var confirmation = confirm('Bạn có chắc chắn muốn xóa loại này?');
				if(confirmation){
					$('#delete-variant_input_'+val).append('<input type="hidden" name="variant_delete[]" value="'+val+'">');
        			$(this).closest('.inputFormRowVariant').remove();
				}
        	});
			
			$(document).on('change', '.image_variant_error', function() {
				checkFileSizes(this);
			});
		
			function checkFileSizes(input) {
				const files = input.files;
				const maxSize = 1048576;
				let invalidFiles = [];
		
				for (let i = 0; i < files.length; i++) {
					const fileSize = files[i].size;
					if (fileSize > maxSize) {
						invalidFiles.push(files[i].name); 
					}
				}
		
				if (invalidFiles.length > 0) {
					alert("Các tệp tin sau có kích thước vượt quá 1MB:\n" + invalidFiles.join("\n"));
					$(input).val(''); 
				}
			}
			
			
			function checkVariantStatus() {
				$('.inputFormRowVariant').each(function() {
					var status = $(this).find('input[name^="variant_status"]').val();
					if (status == 1) {
						$(this).show();
					} else {
						$(this).hide();
					}
				});
			}
			checkVariantStatus();

        	$(document).on('click', '#saveproduct', {}, function ( ) {
        		var data = {id: $('#idProduct').val(), type: $('#typeProduct').val() };
        		if( $('#nameP').length > 0 ){
        			data['title'] = $('#nameP').val();
        		} else if( $('#priceP').length > 0 ){
        			data['price'] = $('#priceP').val();
        		} else if( $('#salePS').length > 0 ){
        			data['price_sales'] = $('#salePS').val();
        		}
        		$.ajax({
                    url: '/admin/product/update-product',
                    type: 'GET',
                    data: data,
                    beforeSend: function ( ) {
                    },
                    success: function (data) {
                    	if (data.Code > 0) {
                        	$('#modalEdit').modal('hide');
                        	bootbox.alert('Cập nhật thành công');
                            var t = $("#productTable").DataTable();
                            t.draw();
                        } else {
                        	bootbox.alert('Cập nhật thất bại');
                        }
                    },
                    error: function (error) {
                    }
                });
            });
            if (currAction  == "detail") {
                pages.product.getListProduct();
                pages.product.getListCombo();
				pages.product.getListWithProduct();
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
						url_product: {
							required: true
						},
						price:{
							required: true
						},
						'variant_name[]': {
							required: true
						},
						'variant_price[]': {
							required: true
						},
						'variant_price_sales[]': {
							required: true
						}
					},
					messages: {
						title: {
							required: $("#title").attr('data-msg')
						},
						url_product: {
							required: $("#url_product").attr('data-msg')
						},
						price: {
							required: $("#price").attr('data-msg')
						},
						'variant_name[]': {
							required: $(".variant-name").attr('data-msg')
						},
						'variant_price[]': {
							required: $(".variant-price").attr('data-msg')
						},
						'variant_price_sales[]': {
							required: $(".variant-price-sales").attr('data-msg')
						}
					},
				};	
			pages.validation.setupValidation("#postDetailForm", loptions);
		},
        initDatatable: function(){
        	var me = this;
        	var aoColumns = [
	    	                 {"data": "id"},
	    	                 { "data": "title" },
	    	                 { "data": "image" },
	    	                 { "data": "id_category" },
	    	                 { "data": "price" },
	    	                 { "data": "created_date" },
	    	                 { "data": "updated_date" },
	    	                 { "data": "updated_by" },
	    	                 { "data": "status" },
	    	                 { "data": "Action_Table"}
	    	];
	        var columnDefs = [
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
							"targets": 2,
							"orderable": true,
							"data": "image"
						},
						{
                        	"render": function ( data, type, row ) {
                        		return row['category_name'];
                        	},
                        	"targets": 3,
  							"orderable": true,
  							"data": "id_category"
						},
						{
                        	"render": function ( data, type, row ) {
                        		if( pages.core.isDefined(data)){
                        			return Number(data).toLocaleString();
                        		} else {
                        			return '-';
                        		}
                        	},
                        	"targets": 4,
  							"orderable": true,
  							"data": "price"
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
	                             action += '<li> <a href="/admin/product/detail/id/'+row.id+'" > <i class="icon-pencil3"></i> '+ translate('edit')+'</a > </li>';
	                             action += '<li> <a onclick="pages.product.updateProduct('+  row.id +', 1)" > <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Cập nhật tên</a > </li>';
	                             action += '<li> <a onclick="pages.product.updateProduct('+  row.id +', 2)" > <i class="fa fa-money" aria-hidden="true"></i> Cập nhật giá</a > </li>';
	                             action += '<li> <a onclick="pages.product.updateProduct('+  row.id +', 3)" > <i class="fa fa-money" aria-hidden="true"></i> Cập nhật giá KM</a > </li>';
	                             action += '<li> <a onclick="pages.product.deleteProduct('+  row.id +')" > <i class="icon-bin"></i> '+ translate('delete')+'</a > </li></ul></li></ul>';
	                             return 	action;
	                         },
	                         "className": "text-center",
	                         "targets": 9,
	                         "orderable": false,
	                         "data": "Action_Table"
	  					 }
	  	    ];
	        pages.common.setupDataTable( "#productTable", "/admin/product/list/", aoColumns, columnDefs, {order:[[ 0, "desc" ]]});
        },
        deleteProduct: function( id ){
        	bootbox.confirm(translate('are-you-sure-want-to-delete-this-product'), function(result) {
	            if( result){
	            	$.ajax({
	                    'url': '/admin/product/delete',
	                    'type': 'GET',
	                    'data': {id: id},
	                    beforeSend: function () {
	                    	
	                    },
	                    success: function (data) {
	                        if( data.Code > 0 ){
	                            if ($("#productTable").length > 0) {
	                            	bootbox.alert(translate('delete-product-success'));
	                                var t = $("#productTable").DataTable();
	                                t.draw();
	                            }
	                        }else{
	                        	bootbox.alert(translate('delete-product-fail'));
	                        }
	                    },
	                    error: function (data) {

	                    }
	                });
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
                            placeholder: "Sản phẩm có thể bạn thích...",
                            minimumResultsForSearch: "-1",
                            width: '100%',
                        });
                    }
                },
                error: function (data) {

                }
            });
        },
		getListWithProduct: function () {
            $.ajax({
                'url': '/admin/product/get-list-with-product',
                'type': 'GET',
                'data': {id: $("#id").val(), selectOrderWithProduct : $("#selected_order_with_product").val()},
                beforeSend: function () {

                },
                success: function (data) {
                    if (data.Code > 0 && data.Data != "") {
                        $("#order_with_product").html(data.Data);
                        $('#order_with_product').select2({
                            placeholder: "Những sản phẩm thường mua cùng...",
                            minimumResultsForSearch: "-1",
                            width: '100%',
                        });
                    }
                },
                error: function (data) {

                }
            });
        },
		getListCombo: function () {
            $.ajax({
                'url': '/admin/product/get-list-combo',
                'type': 'GET',
                'data': {id: $("#id").val(), selectCombo : $("#selected_combo").val()},
                beforeSend: function () {

                },
                success: function (data) {
                    if (data.Code > 0 && data.Data != "") {
                        $("#combo_id").html(data.Data);
                        $('#combo_id').select2({
                            placeholder: "Danh sách combo sản phẩm",
                            minimumResultsForSearch: "-1",
                            width: '100%',
                        });
                    }
                },
                error: function (data) {

                }
            });
        },
        
        updateProduct: function ( id, type ) {
        	$('#modalEdit').html('');
            $.ajax({
                url: '/admin/product/show-popup-edit-product',
                type: 'GET',
                data: {id: id, type:type},
                beforeSend: function ( ) {
                },
                success: function (data) {
                    if (data.Code > 0) {
                        $('#modalEdit').html(data.Data);
                        $('#modalEdit').modal({keyboard: false, show: true, backdrop: 'static'});
                    } else {
                    }
                },
                error: function (error) {
                }
            });
        },
        inArray: function(needle, haystack){
			var found = 0;
			for (var i=0, len=haystack.length;i<len;i++) {
				if (haystack[i] == needle) return i;
				found++;
			}
			return -1;
		}
    }
});