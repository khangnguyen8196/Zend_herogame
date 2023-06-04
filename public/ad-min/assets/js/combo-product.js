$(function () {
    pages.comboProduct.init();
});

if (!pages) {
    var pages = {};
}
pages = $.extend(pages, {
	comboProduct: {
		lang: '',
		isUpdate: -1,
		isSelectOgImg: false,
        init: function () {
            
        	var me = this;
        	pages.common.setupDatePicker();
        	pages.common.setupCheckbox();
        	$('.select-search').select2();
        	$('[data-popup="lightbox"]').fancybox({});
        	if( currController == 'combo-product' && currAction == 'detail'){
        		me.initDatatable();
        		$(document).on('click', '.seach-form', {}, function ( ) {
        			pages.common.executeSearchForm('searchPost','listProductTable');
        		});
				
        	};
			if( currController == 'combo-product' && currAction == 'index'){
        		me.initDatatables();
        		
        	};
			
			$(document).on('click', '.product-name-input', function() {
				var inputId = $(this).data('input-id');
				$('#modalProductList').data('input-id', inputId).modal('show');
			});

			var selectedProducts = [];
			var selectedProductId = [];
			var selectedProductImage = [];
			var selectedProductPrice = [];
			var selectedCheckboxes = {};


			$(document).on('click', '#listProductTable input[type="checkbox"]', function() {
			var inputId = $('#modalProductList').data('input-id');
			var rowData = $("#listProductTable").DataTable().row($(this).parents('tr')).data();
			var productId = rowData.id;
			var productImage = rowData.image;
			var productName = rowData.title;
			var productPrice = rowData.price_sales;
			if (selectedProductId[inputId] === productId) {
				// product already selected for this input
				return;
			}
			if (Object.values(selectedProductId).indexOf(productId) > -1) {
				// product already selected for another input
				alert('Sản phẩm đã tồn tại. Vui lòng chọn sản phẩm khác.');
				return;
			}
			var existingCheckbox = selectedCheckboxes[inputId];
			if (existingCheckbox) {
				existingCheckbox.prop('checked', false);
			}
			selectedCheckboxes[inputId] = $(this);
			selectedProducts[inputId] = productName;
			selectedProductId[inputId] = rowData.id;
			selectedProductImage[inputId] = rowData.image;
			selectedProductPrice[inputId] = rowData.price_sales;
			$('.product-name-input[data-input-id="' + inputId + '"]').val(productName);
			$('.product-id-input[data-input-id="' + inputId + '"]').val(productId);
			$image=$('.product-image').attr('src', '/upload/images' + productImage);
			$('.product-price-input[data-input-id="' + inputId + '"]').val(productPrice);

			var currentTotal = 0;

			$('.product-price-input').each(function() {
				currentTotal += parseFloat($(this).val());
			});

  			$('.total-price').val(currentTotal);
			});

			$("#selectProduct").submit(function(e) {
			e.preventDefault();
			var inputId = $('#modalProductList').data('input-id');
			var productName = selectedProducts[inputId];
			var productId =selectedProductId[inputId];

			$('.product-name-input[data-input-id="' + inputId + '"]').val(productName);
			$('.product-id-input[data-input-id="' + inputId + '"]').val(productId);
			$.ajax({
				type: "POST",
				url: $(this).attr('action'),
				data: $(this).serialize(),
				success: function(data) {
				// Do something with response data
				},
				error: function(jqXHR, textStatus, errorThrown) {
				// Handle error
				}
			});
			$("#modalProductList").modal("hide");
			});

			$(document).on('click', '.remove-product', function() {
				var inputId = $(this).parents('.file-item').find('.product-name-input').data('input-id');
				delete selectedProducts[inputId];
				delete selectedProducts[inputId];
				delete selectedProductId[inputId];
				delete selectedProductImage[inputId];
				delete selectedProductPrice[inputId];
				delete selectedCheckboxes[inputId];
				$(this).parents('.file-item').remove();
				var currentTotal = 0;

				$('.product-price-input').each(function() {
					if ($(this).val() !== '') {
					currentTotal += parseFloat($(this).val());
					}
				});

				$('.total-price').val(currentTotal);
			});

			// $(document).on('click', '.remove-product', function() {
			// 	$(this).parents('.file-item').remove();
			// });
			
			var Index = 0;

			$(document).on('click', '.add-product', function() {
				Index++;
				var comboId = $(this).data('combo-id');
				var html = 	'<div class="form-group file-item">' +
							'<div class="col-lg-6">' +
								'<input type="hidden" name="combo_detail_id[]" value="0" />'+
								'<input type="text" class="form-control product-name-input"  placeholder="Chọn sản phẩm" data-combo-id="' + comboId + '" data-input-id="' + Index + '" />' +
								'<input type="hidden" class="form-control product-id-input" name="product_id[]" placeholder="" data-combo-id="' + comboId + '" data-input-id="' + Index + '" />' +
							'</div>' +
							'<div class="col-lg-2">' +
								'<input type="text" class="form-control product-price-input"  value="" placeholder="Giá sản phẩm"  data-combo-id="' + comboId + '" data-input-id="' + Index + '" disabled />' +
							'</div>' +
							'<div class="col-lg-2">' +
								'<button type="button" class="btn btn-alert remove-product" style="margin-right: 11px;">x</button>' +
							'</div>' +
							'</div>';
				$(this).closest('.form-group').find('.list-product').append(html);
			});

			$(document).on('click', '.remove-product-list', function() {	
				var comboId = $(this).data('combo-id');
        		var val = $(this).attr('data-remove');
				console.log(val);  
        		$('#delete-product_input_'+comboId).append('<input type="hidden" name="combo_id_delete[]" value="'+val+'">');
        		$(this).parents('.file-item').remove();
				var currentTotal = 0;
				$('.product-price-input').each(function() {
					if ($(this).val() !== '') {
					currentTotal += parseFloat($(this).val());
					}
				});

				$('.total-price').val(currentTotal);
        	});

        },
        initDatatable: function(){
        	var me = this;
        	var aoColumns = [
							{ "data": "Action_Table"},
	    	                 {"data": "id"},
	    	                 { "data": "title" },
	    	                 { "data": "image" },
	    	                 { "data": "id_category" },
	    	                 { "data": "price" },
	    	                 { "data": "status" },
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
							"targets": 3,
							"orderable": true,
							"data": "image"
						},
						{
                        	"render": function ( data, type, row ) {
                        		return row['category_name'];
                        	},
                        	"targets": 4,
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
                        	"targets": 5,
  							"orderable": true,
  							"data": "price"
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
	  		                    targets: 6
	  					},
	  					 {
	                    	 "render": function (data, type, row) {
	                             return '<input type="checkbox" value="'+row.id+'" name="rowcheck[]">';
	                         },
	                         "className": "text-center",
	                         "targets": 0,
	                         "orderable": false,
	                         "data": "Action_Table"
	  					 }
	  	    ];
	        pages.common.setupDataTable( "#listProductTable", "/admin/combo-product/list-product/", aoColumns, columnDefs, {order:[[ 0, "desc" ]]});
        },

		initDatatables: function () {
            var aoColumns = [
                {"data": "id"},
                {"data": "title"},
                {"data": "image_cb"},
                {"data": "total_discount"},
                {"data": "status"},
                {"data": "Action_Table"}
            ];
            var columnDefs = [
				{
                    "render": function (data, type, row) {
                        return row["id"];
                    },
                    "orderable": true,
                    targets: 0
                },
                {
                    "render": function (data, type, row) {
                        return row["title"];
                    },
                    "orderable": true,
                    targets: 1
                },
				{
					"render": function ( data, type, row ) {
						var img = '';
						if( pages.core.isDefined( row['image_cb'] ) && row['image_cb'] != null ){
							var img = '<a href="/upload/images'+ row['image_cb']+'" data-popup="lightbox">'
								+'<img src="/upload/images'+ row['image_cb']+'" alt="" class="img-rounded img-preview">'
								+'</a>';
						}
						return img;
					},
					"targets": 2,
					"orderable": true,
					"data": "image_cb"
				},
				{
					"render": function ( data, type, row ) {
						if( pages.core.isDefined(data)){
							return Number(data).toLocaleString();
						} else {
							return '-';
						}
					},
					"targets":3,
					  "orderable": true,
					  "data": "total_discount"
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
					targets: 4
				},
                {
                    "render": function (data, type, row) {
                        var action = '<ul class="icons-list" >' +
                                '<li class="dropdown" >' +
                                '<a href="#" class="dropdown-toggle" data-toggle="dropdown">' +
                                '<i class="icon-menu9"> </i></a>' +
                                '<ul class="dropdown-menu dropdown-menu-right">';
                        action += '<li> <a href="/admin/combo-product/detail/id/' + row.id + '" > <i class="icon-pencil3"></i> ' + translate('edit') + '</a > </li>';
                        action += '<li> <a onclick="pages.comboProduct.deleteComboProduct(' + row.id + ')" > <i class="icon-bin"></i> ' + translate('delete') + '</a > </li></ul></li></ul>';

                        return 	action;
                    },
                    "className": "text-center",
                    "targets": 5,
                    "orderable": false,
                    "data": "Action_Table"
                }
            ];
            pages.common.setupDataTable("#comboProductTable", "/admin/combo-product/list-combo/", aoColumns, columnDefs, {order: [[0, "desc"]]});
        },
		deleteComboProduct: function( id ){
        	bootbox.confirm('Bạn Có Muốn Xóa Combo Này', function(result) {
	            if( result){
	            	$.ajax({
	                    'url': '/admin/combo-product/delete',
	                    'type': 'GET',
	                    'data': {id: id},
	                    beforeSend: function () {
	                    	
	                    },
	                    success: function (data) {
	                        if( data.Code > 0 ){
	                            if ($("#comboProductTable").length > 0) {
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