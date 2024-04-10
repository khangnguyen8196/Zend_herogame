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
		
			$("#checkAll").change(function() {
				var isChecked = $(this).prop("checked");
				$("#listProductTable tbody input[type='checkbox']").prop("checked", isChecked).trigger("change");
			});
			$(document).ready(function() {
				$(document).on('click', 'a.paginate_button', function() {
					$("#checkAll").removeAttr('checked');
				});
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
				if (rowData && rowData.id) {
					var productId = rowData.id;
					if ($(this).prop('checked')) {
						var existingProductIndex = selectedProducts.findIndex(product => product.productId === productId);
						console.log(existingProductIndex);
						if (existingProductIndex === -1) {
							var inputId = selectedProducts.length;
							selectedProducts.push({
								inputId: inputId,
								productId: productId,
								name: rowData.title,
								image:rowData.image,
								priceSale: rowData.price_sales,
								price: rowData.price
							});
						}
					} else {
						selectedProducts = selectedProducts.filter(product => product.productId !== productId);
						console.log("After removing:", selectedProducts);
					}
				} else {
					console.log("rowData.id is empty or not available.");
				}
			});
			
			
			$(document).on('click', '.add-product', function() {
				var inputId = $(this).data('input-id');
				$('.product-id-input').each(function() {
					var productId = $(this).val(); 
					$('input[type="checkbox"][value="' + productId + '"]').prop('checked', true);
				});
				$('#modalProductList').data('input-id', inputId).modal('show');
			});

			$(document).on('draw.dt', '#listProductTable', function() {
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
						var image = product.image;
						var price = formatNumber(product.price);
						var priceSale = formatNumber(product.priceSale);
			
						var html = '<div style="border:1px solid #ccc; border-radius:20px;padding:10px;" class="form-group file-item-'+ inputId + '">' +
										
											'<div class="col-lg-2">' +
												'<img class="img-media img-thumbnail-item" width="140px" height="92px" src="/upload/images/'+ image +'" alt="">'+
											'</div>'+
											'<div class="col-lg-10">' +
												'<div class="form-group">'+
													'<label class="control-label col-lg-2">Tên sản phẩm</label>'+
													'<div class="col-lg-10">' +
														'<input type="hidden" name="flash_sale_product_id[]" value="0" />'+
														'<input type="text" readonly class="form-control product-name-input" name="product_name[]" value="' + productName + '" placeholder="Chọn sản phẩm" data-input-id="' + inputId + '" />' +
														'<input type="hidden" class="form-control product-id-input" name="product_id[]" value="' + productId + '" placeholder="" data-input-id="' + inputId + '" />' +
														'<span class="errorSanPham" style="color:red"></span>'+
													'</div>' +
												'</div>'+
												'<label class="control-label col-lg-2">Thông tin</label>'+
												'<div class="col-lg-2">' +
													'<span>Price</span>'+
													'<input type="tel" readonly maxlength="10" name="price[]" class="form-control product-price-input" value="' + price + '" placeholder="Giá sản phẩm"  data-input-id="' + inputId + '" oninput="this.value = this.value.replace(/[^0-9]/g, \'\');" />' +
													'<span class="errorPrice" style="color:red"></span>'+
												'</div>' +
												'<div class="col-lg-2">' +
													'<span>Price sale</span>'+
													'<input type="tel" readonly maxlength="10" name="price_sales[]" class="form-control product-price-sale-input"  value="' + priceSale + '" placeholder="Giá sale"  data-input-id="' + inputId + '" oninput="this.value = this.value.replace(/[^0-9]/g, \'\');" />' +
													'<span class="errorPrice" style="color:red"></span>'+
												'</div>' +
												'<div class="col-lg-1">' +
													'<span>%</span>'+
													'<input type="tel" maxlength="2" name="percent_flash_sale[]" class="form-control product-percent-flash-sale"  value="" placeholder="% sale giảm" data-input-id="' + inputId + '" oninput="this.value = this.value.replace(/[^0-9]/g, \'\');" />' +
												'</div>' +
												'<div class="col-lg-2">' +
													'<span>Price discount</span>'+
													'<input type="tel" name="price_discount[]" class="form-control product-price-discount"  value="" placeholder="tiền giảm" data-input-id="' + inputId + '" oninput="this.value = this.value.replace(/[^0-9]/g, \'\');" />' +
												'</div>' +
												'<div class="col-lg-2">' +
													'<span>Price flash sale</span>'+
													'<input type="tel" readonly maxlength="2" name="price_flash_sale[]" class="form-control product-price-flash-sale"  value="" placeholder="giá flash sale" data-input-id="' + inputId + '" oninput="this.value = this.value.replace(/[^0-9]/g, \'\');"/>' +
												'</div>' +
												'<div class="col-lg-1">' +
													'<button type="button" class="btn btn-alert remove-product" style="display: block;margin-top: 20px;" data-input-id="' + inputId + '">x</button>' +
												'</div>' +
											'</div>'+
									'</div>';
						$('.list-product').append(html);
					}
				});
				$('#modalProductList').modal('hide');
				
			});
			
			$(document).on('click', '.submit-btn', function(e) {
				var title = $('#title_flash_sale').val();
				var startDate = $('#startDate').val();
				var endDate = $('#endDate').val(); 
			
				if (title == '') {
					e.preventDefault();
					$('.errorTitle').text('Tiêu đề không được để trống');
				}
				if (startDate > endDate) {
					e.preventDefault();
					$('.errorStartDate').text('Thời gian bắt đầu phải nhỏ hơn thời gian kết thúc');
				}
				if (startDate == '') {
					e.preventDefault();
					$('.errorStartDate').text('Hãy chọn thời gian bắt đầu nhỏ hơn thời gian kết thúc');
				}
				if (endDate == '') {
					e.preventDefault();
					$('.errorEndDate').text('Hãy chọn thời gian kết thúc phải lớn hơn thời gian bắt đầu');
				}
			});
			
	
			$(document).on('focusin', 'input[name="percent_flash_sale[]"]', function() {
				var inputId = $(this).data('input-id');
				var parentDiv = $(this).closest('.file-item-' + inputId + '');
				var priceValue = parseFloat(parentDiv.find('input[name="price[]"]').val().replace(/,/g, ''));
				var total =0;
				$(document).on('input','.product-percent-flash-sale[data-input-id="' + inputId + '"]',function(){
					var percentValue = parentDiv.find('input[name="percent_flash_sale[]"]').val();
					var priceDiscount = parentDiv.find('input[name="price_discount[]"]').val('');
					if((percentValue =='' ||percentValue == 0) && priceDiscount == '' || priceDiscount==0){
						total =0;
					}
					total = priceValue - (priceValue * percentValue)/100;
					console.log(total);
					parentDiv.find('input[name="price_flash_sale[]"]').val(formatNumber(total));
					
				})
			});

			$('#percent_all').on('input', function() {
				var percentAllValue = $(this).val();
				$('input[name="discount_all"]').val('');
				$('input[name="price_discount[]"]').val(percentAllValue !== 0 ? 0 : '');
				$('input[name="percent_flash_sale[]"]').val(percentAllValue);
				var pricesArray = []; 
				$('input[name="price[]"]').each(function() {
					var priceValue = $(this).val();
					priceValue = priceValue.replace(/,/g, ''); 
					pricesArray.push(priceValue); 
				});
				pricesArray.forEach(function(priceValue, index) {
					var percentAllValue = parseFloat($('input[name="percent_all"]').val()); 
					var newPrice = parseFloat(priceValue - (priceValue * percentAllValue / 100));
					console.log(percentAllValue);
					$('input[name="price_flash_sale[]"]').eq(index).val(formatNumber(newPrice)); 
				});
			});

			$('#discount_all').on('input', function() {
				var discountAllValue = $(this).val();
				$('input[name="percent_all"]').val('');
				$('input[name="percent_flash_sale[]"]').val(discountAllValue !== 0 ? 0 : '');
				var pricesArray = []; 
				$('input[name="price[]"]').each(function() {
					var priceValue = $(this).val();
					priceValue = priceValue.replace(/,/g, ''); 
					pricesArray.push(priceValue); 
				});
				pricesArray.forEach(function(priceValue, index) {
					var discountAllValue = parseFloat($('input[name="discount_all"]').val());
					if(discountAllValue > priceValue){
						var newPrice = parseFloat(priceValue - priceValue);
						$('input[name="price_discount[]"]').eq(index).val(formatNumber(priceValue));
					}else{
						var newPrice = parseFloat(priceValue - discountAllValue);
						$('input[name="price_discount[]"]').eq(index).val(formatNumber(discountAllValue));
					}
					$('input[name="price_flash_sale[]"]').eq(index).val(formatNumber(newPrice)); 
				});
			});

			$(document).on('focusin', 'input[name="price_discount[]"]', function() {
				var inputId = $(this).data('input-id');
				var parentDiv = $(this).closest('.file-item-' + inputId + '');
				var priceValue = parseFloat(parentDiv.find('input[name="price[]"]').val().replace(/,/g, ''));
				var total =0;
				$(document).on('input','.product-price-discount[data-input-id="' + inputId + '"]',function(){
					var percentValue = parentDiv.find('input[name="percent_flash_sale[]"]').val('');
					var priceDiscount = parseFloat(parentDiv.find('input[name="price_discount[]"]').val().replace(/,/g, ''));
					if((percentValue =='' ||percentValue == 0) && priceDiscount == '' || priceDiscount==0){
						total =0;
					}
					total = priceValue - priceDiscount;
					parentDiv.find('input[name="price_flash_sale[]"]').val(formatNumber(total));
					
				})
			});
		
			$(document).on('click', '.remove-product', function() {
				var inputId = $(this).data('input-id');
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

			$(document).on('input', 'input[name="discount_all"]', function(){
				var inputVal = $(this).val();
				var numericVal = inputVal.replace(/\D/g,'');
				if (numericVal.length > 2 && numericVal.charAt(0) === '0') {
					numericVal = numericVal.slice(1);
				}
				var formattedVal = addCommasToNumber(numericVal);
				$(this).val(formattedVal);
			});

			$(document).on('input', 'input[name="price_discount[]"]', function(){
				var inputVal = $(this).val();
				var numericVal = inputVal.replace(/\D/g,'');
				if (numericVal.length > 2 && numericVal.charAt(0) === '0') {
					numericVal = numericVal.slice(1);
				}
				var formattedVal = addCommasToNumber(numericVal);
				$(this).val(formattedVal);
			});
			
			$(document).on('input', 'input[name="price_discount[]"]', function(){
				var inputId = $(this).data('input-id');
				var parentDiv = $(this).closest('.file-item-' + inputId + '');
				var priceDiscount = parseFloat(parentDiv.find('input[name="price_discount[]"]').val().replace(/,/g, ''));
				var priceValue = parseFloat(parentDiv.find('input[name="price[]"]').val().replace(/,/g, ''));
				if(priceDiscount > priceValue) {
					parentDiv.find('input[name="price_discount[]"]').val(parentDiv.find('input[name="price[]"]').val());
				}
			});
	
			function formatNumber(number) {
				var parts = number.toString().split(".");
				parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
				return parts.join(".");
			}

			function addCommasToNumber(number) {
				return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
			}

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
								return row['id'];
							},
							"targets": 0,
							"orderable": true,
							"data": "id"
						},
						{
							"render": function ( data, type, row ) {
								return row['title'];
							},
							"targets": 2,
							"orderable": true,
							"data": "id"
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
	  		                    orderable: false,
	  		                    targets: 7
	  					},
	  					 {
	                    	 "render": function (data, type, row) {
	                             return '<input type="checkbox" value="'+row.id+'" name="rowcheck[]" class="row-checkbox">';
	                         },
	                         "className": "text-center",
	                         "targets": 1,
	                         "orderable": false,
	                         "data": "Action_Table"
	  					 }
	  	    ];
	        pages.common.setupDataTable( "#listProductTable", "/admin/flash-sale-product/list-product/", aoColumns, columnDefs, {order:[[ 0, "desc" ]]});
			$('#listProductTable').on('click', '.row-checkbox', function() {
				$(this).prop('checked', $(this).prop('checked'));
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