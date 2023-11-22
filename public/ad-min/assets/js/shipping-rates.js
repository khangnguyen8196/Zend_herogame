$(function () {
    pages.shippingRates.init();
});

if (!pages) {
    var pages = {};
}
pages = $.extend(pages, {
	shippingRates: {
		lang: '',
		isUpdate: -1,
		isSelectOgImg: false,
        init: function () {
            
        	var me = this;
        	pages.common.setupDatePicker();
        	pages.common.setupCheckbox();
        	$('.select-search').select2();
        	$('[data-popup="lightbox"]').fancybox({});
			if( currController == 'shipping-rates' && currAction == 'index'){
        		me.initDatatable();
        		
        	};

            $(document).on('change', '.choose', function() {
                var action = $(this).attr('id');
				console.log(action);
                var ma_id = $(this).val();
                console.log(ma_id);
                var result = '';
                if(action =='province'){
                    result ='district';
                }
                $.ajax({
                    url: '/admin/shipping-rates/select',
                    type: 'POST',
                    data: {action: action,ma_id:ma_id},
                    success: function (data) {
                        $('#'+result).html(data);
                    },
                    error: function (data) {
                    }
                });
            });

            var dataIsValid = false; 
            $(document).on('click', '#save-shipping', function (e) {
                let id =$('#fee_id').val();
                let checkDataExistErrorSpan = $('#checkDataExistError');
                let province = $('#province').val();
                let district = $('#district').val();
                $.ajax({
                    url: '/admin/shipping-rates/check-data',
                    type: 'POST',
                    data: {id:id, province: province, district:district},
                    success: function (data) {
                        if (data) {
                            checkDataExistErrorSpan.html(data);
                            dataIsValid = false; 
                        } else {
                            dataIsValid = true; 
                            $('#shippingRatesDetailForm').submit(); 
                        }
                    },
                });

                if (!dataIsValid) {
                    e.preventDefault(); 
                }
            });

            $('#shippingRatesDetailForm').submit(function(e) {
                let selectedProvince = $('#province').val();
                let selectedDistrict = $('#district').val();
                let feeShipValue = $('#fee_ship').val();
                let provinceErrorSpan = $('#provinceError');
                let districtErrorSpan = $('#districtError');
                let feeShipErrorSpan = $('#feeShipError');
                let hasError = false;   
                if (selectedProvince === '') {
                    provinceErrorSpan.text('Vui lòng chọn tỉnh thành.');
                    hasError = true;
                } else {
                    provinceErrorSpan.text('');
                }
                if (selectedDistrict === '') {
                    districtErrorSpan.text('Vui lòng chọn quận huyện.');
                    hasError = true;
                } else {
                    districtErrorSpan.text('');
                }
                if (feeShipValue.trim() === '') {
                    feeShipErrorSpan.text('Vui lòng nhập giá ship.');
                    hasError = true;
                } else {
                    feeShipErrorSpan.text('');
                }   
                if (hasError) {
                    e.preventDefault();
                }
            });              
        },
		initDatatable: function () {
            var aoColumns = [
                {"data": "fee_id"},
                {"data": "name_province"},
                {"data": "name_district"},
                {"data": "name_wards"},
                {"data": "fee_ship"},
                {"data": "status"},
                {"data": "Action_Table"}
            ];
            var columnDefs = [
				{
                    "render": function (data, type, row) {
                        return row["fee_id"];
                    },
                    "orderable": true,
                    targets: 0
                },
                {
                    "render": function (data, type, row) {
                        return row["name_province"];
                    },
                    "orderable": false,
                    targets: 1
                },
				{
                    "render": function (data, type, row) {
                        return row["name_district"];
                    },
                    "orderable": false,
                    targets: 2
                },
				{
                    "render": function (data, type, row) {
                        return row["name_wards"];
                    },
                    "orderable": false,
                    targets: 3
                },
				{
                    "render": function (data, type, row) {
                        return row["fee_ship"];
                    },
                    "orderable": false,
                    targets: 4
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
					targets: 5
				},
                {
                    "render": function (data, type, row) {
                        var action = '<ul class="icons-list" >' +
                                '<li class="dropdown" >' +
                                '<a href="#" class="dropdown-toggle" data-toggle="dropdown">' +
                                '<i class="icon-menu9"> </i></a>' +
                                '<ul class="dropdown-menu dropdown-menu-right">';
                        action += '<li> <a href="/admin/shipping-rates/detail/fee_id/' + row.fee_id + '" > <i class="icon-pencil3"></i> ' + translate('edit') + '</a > </li>';
                        action += '<li> <a onclick="pages.shippingRates.deleteShippingRates(' + row.fee_id + ')" > <i class="icon-bin"></i> ' + translate('delete') + '</a > </li></ul></li></ul>';

                        return 	action;
                    },
                    "className": "text-center",
                    "targets": 6,
                    "orderable": false,
                    "data": "Action_Table"
                }
            ];
            pages.common.setupDataTable("#shippingRatesTable", "/admin/shipping-rates/list-shipping-rates/", aoColumns, columnDefs, {order: [[0, "desc"]]});
        },
		deleteShippingRates: function( fee_id ){
        	bootbox.confirm('Bạn Có Muốn Xóa Phí Vận Chuyển  Này', function(result) {
	            if( result){
	            	$.ajax({
	                    'url': '/admin/shipping-rates/delete',
	                    'type': 'GET',
	                    'data': {fee_id: fee_id},
	                    beforeSend: function () {
	                    	
	                    },
	                    success: function (data) {
	                        if( data.Code > 0 ){
	                            if ($("#shippingRatesTable").length > 0) {
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