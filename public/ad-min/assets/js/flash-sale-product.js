$(function () {
    pages.flashSaleProduct.init();
});

if (!pages) {
    var pages = {};
}
pages = $.extend(pages, {
	flashSaleProduct: {
		lang: '',
		isUpdate: -1,
		isSelectOgImg: false,
        init: function () {
        	var me = this;
        	pages.common.setupDatePicker();
        	pages.common.setupCheckbox();
        	$('.select-search').select2();
        	$('[data-popup="lightbox"]').fancybox({});
        	if( currController == 'flash-sale-product' && currAction == 'detail'){
				$(".datetimepicker").datetimepicker({
					format: "yyyy-mm-dd hh:ii:ss",
					startDate: new Date() 
				});
				
        		me.initDatatable();
        		$(document).on('click', '.seach-category', {}, function ( ) {
        			pages.common.executeSearchForm('searchPost','listProductTable');
        		});
				
        	};
			if( currController == 'flash-sale-product' && currAction == 'index'){
        		me.initDatatables();
        	};

			var selectedProducts = [];
			$(document).ready(function() {
				$("#checkAll").change(function() {
					var isChecked = $(this).prop("checked");
					$("#listProductTable tbody input[type='checkbox']").prop("checked", isChecked).trigger("change");
				});
			
				$(document).on('change', '#listProductTable input[type="checkbox"]', function() {
					var isCheckedAll = true;
					$("#listProductTable tbody input[type='checkbox']").each(function() {
						if (!$(this).prop('checked')) {
							isCheckedAll = false;
							return false; 
						}
					});
					$("#checkAll").prop("checked", isCheckedAll);
					var rowData = $("#listProductTable").DataTable().row($(this).parents('tr')).data();
					var productId = rowData.id;
					if ($(this).prop('checked')) {
						var inputId = selectedProducts.length;
						selectedProducts.push({
							inputId: inputId,
							productId: productId,
							name: rowData.title,
							priceSale: rowData.price_sales,
							price: rowData.price
						});
					} else {
						selectedProducts = selectedProducts.filter(product => product.productId !== productId);
					}
				});
			});
			// $(document).ready(function() {
			// 	$("#checkAll").change(function() {
			// 		var isChecked = $(this).prop("checked"); 
			// 		$("#listProductTable tbody input[type='checkbox']").prop("checked", isChecked);
			// 	});
			// });

			// $(document).on('change', '#listProductTable input[type="checkbox"]', function() {
			// 	var rowData = $("#listProductTable").DataTable().row($(this).parents('tr')).data();
			// 	var productId = rowData.id;

			// 	if ($(this).prop('checked')) {
			// 		var inputId = selectedProducts.length; 

			// 		if (selectedProducts.some(product => product.productId === productId)) {
			// 			alert('Sản phẩm đã tồn tại. Vui lòng chọn sản phẩm khác.');
			// 			$(this).prop('checked', false);
			// 			return;
			// 		}
			// 		selectedProducts.push({
			// 			inputId: inputId,
			// 			productId: productId,
			// 			name: rowData.title,
			// 			priceSale: rowData.price_sales,
			// 			price: rowData.price
			// 		});
			// 	} else {
			// 		selectedProducts = selectedProducts.filter(product => product.productId !== productId);
			// 	}
			// });
			$(document).on('click', '.add-product', function() {
				var inputId = $(this).data('input-id');
				$('.product-id-input').each(function() {
					var productId = $(this).val(); 
					$('input[type="checkbox"][value="' + productId + '"]').prop('checked', true);
				});
				$('#modalProductList').data('input-id', inputId).modal('show');
			});

			$(document).on('draw.dt', '#listProductTable', function() {
				console.log('hello world');
				$('.product-id-input').each(function() {
					var productId = $(this).val(); 
					$('input[type="checkbox"][value="' + productId + '"]').prop('checked', true);
				});
			});
			
			$(document).on('click', '.remove-product', function() {
				var inputId = $(this).data('input-id');
				$('.file-item-' + inputId).remove();
				delete selectedProducts[inputId];
			});

			// $(document).on('input', '.product-percent-flash-sale', function() {
			// 	var inputId = $(this).data('input-id').split('-')[0];
			// 	var productId = $(this).data('input-id').split('-')[1];
			// 	var parentDiv = $(this).closest('.file-item-' + inputId + '-' + productId);
			// 	var priceValue = parseFloat(parentDiv.find('.product-price-input').val());
			// 	var percentValue = parseFloat($(this).val());
			// 	var total = priceValue - (priceValue * percentValue) / 100;
			// 	if (!isNaN(total)) {
			// 		parentDiv.find('.product-price-flash-sale').val(total.toFixed(2));
			// 	}
			// });

			// $("#confirmSelection").click(function(e) {
			// 	selectedProducts.forEach(function(product) {
			// 		e.preventDefault()
			// 		var inputId = product.inputId;
			// 		var productName = product.name;
			// 		var productId = product.productId;
			// 		var price = product.price;
			// 		var priceSale = product.priceSale;
			// 		var html = 	'<div class="form-group file-item-'+ inputId + '">' +
			// 				'<div class="col-lg-3">' +
			// 					'<input type="hidden" name="flash_sale_product_id[]" value="0" />'+
			// 					'<input type="text" readonly class="form-control product-name-input" name = "product_name[]" value="' + productName+ '" placeholder="Chọn sản phẩm" data-input-id="' + inputId + '" />' +
			// 					'<input type="hidden" class="form-control product-id-input" name = "product_id[]" value="' + productId + '" placeholder="" data-input-id="' + inputId + '" />' +
			// 					'<span class="errorSanPham" style="color:red"></span>'+
			// 					'</div>' +
			// 				'<div class="col-lg-2">' +
			// 					'<input type="tel" readonly maxlength="10" name="price[]" class="form-control product-price-input" value="' + price + '" placeholder="Giá sản phẩm"  data-input-id="' + inputId + '" oninput="this.value = this.value.replace(/[^0-9]/g, \'\');" />' +
			// 					'<span class="errorPrice" style="color:red"></span>'+
			// 					'</div>' +
			// 				'<div class="col-lg-2">' +
			// 					'<input type="tel" readonly maxlength="10" name="price_sales[]" class="form-control product-price-sale-input"  value="' + priceSale + '" placeholder="Giá sale"  data-input-id="' + inputId + '" oninput="this.value = this.value.replace(/[^0-9]/g, \'\');" />' +
			// 					'<span class="errorPrice" style="color:red"></span>'+
			// 				'</div>' +
			// 				'<div class="col-lg-2">' +
			// 					'<input type="tel" maxlength="2" name="percent_flash_sale[]" class="form-control product-percent-flash-sale"  value="" placeholder="% sale giảm" data-input-id="' + inputId + '" oninput="this.value = this.value.replace(/[^0-9]/g, \'\');"/>' +
			// 				'</div>' +
			// 				'<div class="col-lg-2">' +
			// 					'<input type="tel" readonly maxlength="2" name="price_flash_sale[]" class="form-control product-price-flash-sale"  value="" placeholder="giá flash sale" data-input-id="' + inputId + '" oninput="this.value = this.value.replace(/[^0-9]/g, \'\');"/>' +
			// 				'</div>' +
			// 				'<div class="col-lg-1">' +
			// 					'<button type="button" class="btn btn-alert remove-product" style="margin-right: 11px;" data-input-id="' + inputId + '">x</button>' +
			// 				'</div>' +
			// 				'</div>';
			// 				$('.list-product').append(html);
			// 	});
			// 	$('#modalProductList').modal('hide');
			// });

			$(document).on('click', '#confirmSelection', function(e) {
				e.preventDefault();
				var addedProductIds = [];
				var productAdded = false;
				$('.list-product .product-id-input').each(function() {
					addedProductIds.push($(this).val());
				});
				selectedProducts.forEach(function(product) {
					var productId = product.productId;
			
					if (addedProductIds.includes(productId)) {
						productAdded = true;
					} else {
						var inputId = product.inputId;
						var productName = product.name;
						var price = product.price;
						var priceSale = product.priceSale;
			
						var html = '<div class="form-group file-item-'+ inputId + '">' +
										'<div class="col-lg-3">' +
											'<input type="hidden" name="flash_sale_product_id[]" value="0" />'+
											'<input type="text" readonly class="form-control product-name-input" name="product_name[]" value="' + productName + '" placeholder="Chọn sản phẩm" data-input-id="' + inputId + '" />' +
											'<input type="hidden" class="form-control product-id-input" name="product_id[]" value="' + productId + '" placeholder="" data-input-id="' + inputId + '" />' +
											'<span class="errorSanPham" style="color:red"></span>'+
										'</div>' +
										'<div class="col-lg-2">' +
											'<input type="tel" readonly maxlength="10" name="price[]" class="form-control product-price-input" value="' + price + '" placeholder="Giá sản phẩm"  data-input-id="' + inputId + '" oninput="this.value = this.value.replace(/[^0-9]/g, \'\');" />' +
											'<span class="errorPrice" style="color:red"></span>'+
										'</div>' +
										'<div class="col-lg-2">' +
											'<input type="tel" readonly maxlength="10" name="price_sales[]" class="form-control product-price-sale-input"  value="' + priceSale + '" placeholder="Giá sale"  data-input-id="' + inputId + '" oninput="this.value = this.value.replace(/[^0-9]/g, \'\');" />' +
											'<span class="errorPrice" style="color:red"></span>'+
										'</div>' +
										'<div class="col-lg-2">' +
											'<input type="tel" maxlength="2" name="percent_flash_sale[]" class="form-control product-percent-flash-sale"  value="" placeholder="% sale giảm" data-input-id="' + inputId + '" oninput="this.value = this.value.replace(/[^0-9]/g, \'\');" />' +
										'</div>' +
										'<div class="col-lg-2">' +
											'<input type="tel" readonly maxlength="2" name="price_flash_sale[]" class="form-control product-price-flash-sale"  value="" placeholder="giá flash sale" data-input-id="' + inputId + '" oninput="this.value = this.value.replace(/[^0-9]/g, \'\');"/>' +
										'</div>' +
										'<div class="col-lg-1">' +
											'<button type="button" class="btn btn-alert remove-product" style="margin-right: 11px;" data-input-id="' + inputId + '">x</button>' +
										'</div>' +
									'</div>';
						$('.list-product').append(html);
					}
				});
				$('#modalProductList').modal('hide');
				
			});
			
			
			
			
			
			
			
			// $(document).on('click','.submit-btn',function(e){
			// 	var product = $('.product-name-input').val();
			// 	var priceSale = $('.product-price-input').val();
			// 	var date = $('#startdate').val();
			// 	if(product=='' || priceSale =='' || priceSale ==0 || date ==''){
			// 		e.preventDefault();
			// 	}
			// 	if(product ==''){
			// 		$('.errorSanPham').text('Hãy chọn sản phẩm');
			// 	} 
			// 	if(priceSale ==''){
			// 		$('.errorPrice').text('Giá sale phải lớn hơn 0')
			// 	}
			// 	if(date ==''){
			// 		$('.errorDate').text('Không được để trống')
			// 	}
			// })

			// $(document).on('click', '.product-name-input', function() {
			// 	var inputId = $(this).data('input-id');
			// 	var product = $('.product-name-input').val();
			// 	if(product==''){
			// 		$('.errorSanPham').text('')
			// 	}
			// 	var priceSale = $('.product-price-input').val();
			// 	if(priceSale ==''){
			// 		$('.errorPrice').text('')
			// 	}
			// 	$('#modalProductList').data('input-id', inputId).modal('show');
			// });

			// var selectedProducts = [];
			// var selectedProductId = [];
			// var selectedProductPriceSale = [];
			// var selectedProductPrice = [];
			// var priceFlashSale = [];
			// var percentFlashSale = [];
			// var selectedCheckboxes = {};
			// $(document).on('click', '#listProductTable input[type="checkbox"]', function() {
			// 	var inputId = $('#modalProductList').data('input-id');
			// 	var rowData = $("#listProductTable").DataTable().row($(this).parents('tr')).data();
			// 	var productId = rowData.id;
			// 	var productName = rowData.title;
			// 	var productPriceSale = rowData.price_sales;
			// 	var productPrice = rowData.price;
			// 	if (selectedProductId[inputId] === productId) {

			// 		return;
			// 	}
			// 	if (Object.values(selectedProductId).indexOf(productId) > -1) {
			// 		alert('Sản phẩm đã tồn tại. Vui lòng chọn sản phẩm khác.');
			// 		return;
			// 	}
			// 	var existingCheckbox = selectedCheckboxes[inputId];
			// 	if (existingCheckbox) {
			// 		existingCheckbox.prop('checked', false);
			// 	}
			// 	selectedCheckboxes[inputId] = $(this);
			// 	selectedProducts[inputId] = productName;
			// 	selectedProductId[inputId] = rowData.id;
			// 	selectedProductPriceSale[inputId] = rowData.price_sales;
			// 	selectedProductPrice[inputId] = rowData.price;
			// 	$('.product-name-input[data-input-id="' + inputId + '"]').val(productName);
			// 	$('.product-id-input[data-input-id="' + inputId + '"]').val(productId);
			// 	$('.product-price-input[data-input-id="' + inputId + '"]').val(productPrice);
			// 	$('.product-price-sale-input[data-input-id="' + inputId + '"]').val(productPriceSale);
			// 	$('.product-percent-flash-sale[data-input-id="' + inputId + '"]').removeAttr('readonly');
			// });
		
			$(document).on('focusin', 'input[name="percent_flash_sale[]"]', function() {
				var inputId = $(this).data('input-id');
				var parentDiv = $(this).closest('.file-item-' + inputId + '');
				var priceValue = parentDiv.find('input[name="price[]"]').val();
				var total =0;
				$(document).on('input','.product-percent-flash-sale[data-input-id="' + inputId + '"]',function(){
					var percentValue = parentDiv.find('input[name="percent_flash_sale[]"]').val();
					total = priceValue - (priceValue * percentValue)/100;
					console.log(total);
					parentDiv.find('input[name="price_flash_sale[]"]').val(total);
					
				})
			});
		
			// $("#selectProduct").submit(function(e) {
			// 	e.preventDefault();
			// 	var inputId = $('#modalProductList').data('input-id');
			// 	var productName = selectedProducts[inputId];
			// 	var productId =selectedProductId[inputId];

			// 	$('.product-name-input[data-input-id="' + inputId + '"]').val(productName);
			// 	$('.product-id-input[data-input-id="' + inputId + '"]').val(productId);
			// 	$.ajax({
			// 		type: "POST",
			// 		url: $(this).attr('action'),
			// 		data: $(this).serialize(),
			// 		success: function(data) {
			// 		},
			// 		error: function(jqXHR, textStatus, errorThrown) {
			// 		}
			// 	});
			// 	$("#modalProductList").modal("hide");
			// });

			$(document).on('click', '.remove-product', function() {
				var inputId = $(this).data('input-id');
				// var inputId = $(this).parents('.file-item').find('.product-name-input').data('input-id');
				delete selectedProducts[inputId];
				delete selectedProductId[inputId];
				delete selectedProductPriceSale[inputId];
				delete selectedCheckboxes[inputId];
				$(this).parents('.file-item-' + inputId + '').remove();
			});

			$(document).on('click', '.remove-product-list', function() {	
				var id = $(this).data('flash-sale-id');		  
        		var val = $(this).attr('data-remove');
        		$('#delete_product_input_'+id).append('<input type="hidden" name="flash_sale_product_delete[]" value="'+val+'">');
        		$(this).parents('.file-item-'+id).remove();
        	});
			// var Index = 0;
			// $(document).on('click', '.add-product', function() {
			// 	Index++;
			// 	var html = 	'<div class="form-group file-item-'+ Index + '">' +
			// 				'<div class="col-lg-3">' +
			// 					'<input type="hidden" name="flash_sale_product_id[]" value="0" />'+
			// 					'<input type="text" class="form-control product-name-input" name = "product_name[]"  placeholder="Chọn sản phẩm" data-input-id="' + Index + '" />' +
			// 					'<input type="hidden" class="form-control product-id-input" name = "product_id[]" placeholder="" data-input-id="' + Index + '" />' +
			// 					'<span class="errorSanPham" style="color:red"></span>'+
			// 					'</div>' +
			// 				'<div class="col-lg-2">' +
			// 					'<input type="tel" readonly maxlength="10" name="price[]" class="form-control product-price-input"  value="" placeholder="Giá sản phẩm"  data-input-id="' + Index + '" oninput="this.value = this.value.replace(/[^0-9]/g, \'\');" />' +
			// 					'<span class="errorPrice" style="color:red"></span>'+
			// 					'</div>' +
			// 				'<div class="col-lg-2">' +
			// 					'<input type="tel" readonly maxlength="10" name="price_sales[]" class="form-control product-price-sale-input"  value="" placeholder="Giá sale"  data-input-id="' + Index + '" oninput="this.value = this.value.replace(/[^0-9]/g, \'\');" />' +
			// 					'<span class="errorPrice" style="color:red"></span>'+
			// 				'</div>' +
			// 				'<div class="col-lg-2">' +
			// 					'<input type="tel" maxlength="2" name="percent_flash_sale[]" class="form-control product-percent-flash-sale"  value="" placeholder="% sale giảm" data-input-id="' + Index + '" oninput="this.value = this.value.replace(/[^0-9]/g, \'\');"/>' +
			// 				'</div>' +
			// 				'<div class="col-lg-2">' +
			// 					'<input type="tel" readonly maxlength="2" name="price_flash_sale[]" class="form-control product-price-flash-sale"  value="" placeholder="giá flash sale" data-input-id="' + Index + '" oninput="this.value = this.value.replace(/[^0-9]/g, \'\');"/>' +
			// 				'</div>' +
			// 				'<div class="col-lg-1">' +
			// 					'<button type="button" class="btn btn-alert remove-product" style="margin-right: 11px;" data-input-id="' + Index + '">x</button>' +
			// 				'</div>' +
			// 				'</div>';
			// 	$(this).closest('.form-group').find('.list-product').append(html);
			// });
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
							 { "data": "price_sales" },
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
                        	"render": function ( data, type, row ) {
                        		if( pages.core.isDefined(data)){
                        			return Number(data).toLocaleString();
                        		} else {
                        			return '-';
                        		}
                        	},
                        	"targets": 6,
  							"orderable": true,
  							"data": "price_sales"
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
	                             return '<input type="checkbox" value="'+row.id+'" name="rowcheck[]" class="row-checkbox"">';
	                         },
	                         "className": "text-center",
	                         "targets": 0,
	                         "orderable": false,
	                         "data": "Action_Table"
	  					 }
	  	    ];
	        pages.common.setupDataTable( "#listProductTable", "/admin/flash-sale-product/list-product/", aoColumns, columnDefs, {order:[[ 0, "desc" ]]});
			$('#listProductTable').on('click', '.row-checkbox', function() {
				if($(this).prop('checked')) {
					$(this).attr('checked', true);
				} else {
					$(this).removeAttr('checked');
				}
			});
        },
		
		initDatatables: function () {
            var aoColumns = [
                {"data": "flash_sale_id"},
                {"data": "title_flash_sale"},
                {"data": "count_time_start"},
                {"data": "count_time_end"},
                {"data": "status"},
                {"data": "Action_Table"}
            ];
            var columnDefs = [
				{
                    "render": function (data, type, row) {
                        return row["flash_sale_id"];
                    },
                    "orderable": true,
                    targets: 0
                },
				{
                    "render": function (data, type, row) {
                        return row["title_flash_sale"];
                    },
                    "orderable": true,
                    targets: 1
                },
                {
                    "render": function (data, type, row) {
                        return row["count_time_start"];
                    },
                    "orderable": true,
                    targets: 2
                },
				{
                    "render": function (data, type, row) {
                        return row["count_time_end"];
                    },
                    "orderable": true,
                    targets: 3
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
                        action += '<li> <a href="/admin/flash-sale-product/detail/id/' + row.flash_sale_id + '" > <i class="icon-pencil3"></i> ' + translate('edit') + '</a > </li>';
                        action += '<li> <a onclick="pages.flashSaleProduct.deleteFlashSale(' + row.flash_sale_id + ')" > <i class="icon-bin"></i> ' + translate('delete') + '</a > </li></ul></li></ul>';

                        return 	action;
                    },
                    "className": "text-center",
                    "targets": 5,
                    "orderable": false,
                    "data": "Action_Table"
                }
            ];
            pages.common.setupDataTable("#flashSaleTable", "/admin/flash-sale-product/list-flash-sale/", aoColumns, columnDefs, {order: [[0, "desc"]]});
        },

		deleteFlashSale: function( id ){
        	bootbox.confirm('Bạn Có Muốn Xóa Combo Này', function(result) {
	            if( result){
	            	$.ajax({
	                    'url': '/admin/flash-sale-product/delete',
	                    'type': 'GET',
	                    'data': {id: id},
	                    beforeSend: function () {
	                    	
	                    },
	                    success: function (data) {
	                        if( data.Code > 0 ){
	                            if ($("#flashSaleTable").length > 0) {
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