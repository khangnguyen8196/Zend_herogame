$(function () {
    pages.order.init();
});

if (!pages) {
    var pages = {};
}
pages = $.extend(pages, {
    order: {
    	selectItem: {},
        init: function () {
            var me = this;
            pages.common.setupCheckbox();
            pages.common.setupDatePicker();
            if (currController == 'order' && currAction == 'index') {
                me.initDatatable();
                $(document).on('click', '.seach-form', {}, function ( ) {
                    pages.common.executeSearchForm('searchOrder', 'orderTable');
                });
            }
            if (currController == 'order' && currAction == 'detail') {
                me.initValidation();
                $(document).on('click', '.submit-btn', {}, function ( ) {
                    if (pages.validation.validator['#orderForm'].form() == false) {
                        return false;
                    }
                    $(".submit-btn").submit();
                });
            }
            $("#admin_discount").keyup(function(){
                var vl = $(this).val();
                var total = $("#total_hide_down").val();
                var new_total = total-vl;
                $("#total_show_down").text(Number(new_total).toLocaleString());
            });
            $(".addpromotion").click(function(){
            	var code = $("#code").val();
            	var admin_discount = parseInt($("#admin_discount").val());
            	$.ajax({
                    'url': '/admin/promotion-code/check-and-get-promotion',
                    'type': 'GET',
                    'data': {code: code},
                    success: function (data) {
                        if (data.Code > 0) {
                        	var infoPromo = data.Data;
                        	var total = $("#total_hide").val();
                        	var desc = total;
                        	var cacl = (total* infoPromo.percent)/100;
                        	if( cacl < infoPromo.max_price ){
                        		desc = total - cacl;
                        	} else {
                        		desc = total - infoPromo.max_price;
                        	}
                        	$("#total_hide_down").val(desc);
                        	$("#total_show_down").text(Number(desc-admin_discount).toLocaleString());
                        	//
                        	$("#apdungma").val(true);
                        	$("#apdungma").attr('data-percent',infoPromo.percent);
                        	$("#apdungma").attr('data-max',infoPromo.max_price);
                        } else {
                        	var total = $("#total_hide").val();
                        	$("#apdungma").val(false);
                        	$("#total_hide_down").val(total);
                        	$("#total_show_down").text(Number(total-admin_discount).toLocaleString());
                            bootbox.alert('Mã Khuyến mãi không hợp lệ');
                        }
                    },
                    error: function (data) {
                    	var total = $("#total_hide").val();
                    	$("#apdungma").val(false);
                    	$("#total_hide_down").val(total);
                    	$("#total_show_down").text(Number(total-admin_discount).toLocaleString());
                        bootbox.alert('Mã Khuyến mãi không hợp lệ');
                    }
                });
            });
            $(".addproduct").click(function(){
            	var value = $("#product").val();
            	var priceTotal = $("#total_hide").val();
            	var color = $("#product_color").val();
            	var admin_discount = 0;
            	if( $("#admin_discount").val() != ""){
            		admin_discount = parseInt($("#admin_discount").val());
            	}
            	if( pages.core.isDefined(priceTotal) && priceTotal != ''){
            		priceTotal = parseInt(priceTotal);
            	} else {
            		priceTotal = 0;
            	}
            	if( value != null ){
            		// price 
        			var product_price = me.selectItem.price;
        			if( pages.core.isDefined(me.selectItem.price_sales) &&  me.selectItem.price_sales > 0 ){
        				product_price = me.selectItem.price_sales;
        			}
            		if( $('.itemproduct[data-idx="'+value+'_'+color+'"]').length > 0 ){
            			var sl = $('.itemproduct[data-idx="'+value+'_'+color+'"] .sl').text();
            			sl = parseInt(sl);
            			
            			$('.itemproduct[data-idx="'+value+'_'+color+'"] .sl').text(sl+parseInt($("#sluong").val()));
            			$('.itemproduct[data-idx="'+value+'_'+color+'"] .sl_'+value).val(sl+parseInt($("#sluong").val()));
            			
            		} else {
            			var html = '<tr class="itemproduct" data-idx="'+value+'_'+color+'">'+
        				'<td><img width="40px" height="40px" src="/upload/images/'+me.selectItem.img +'" /></td>'+
        				'<td>'+me.selectItem.title+'</td>'+
        				'<td>'+$("#product_color :selected").text()+'</td>'+
        				'<td>'+Number(me.selectItem.price).toLocaleString()+'</td>'+
        				'<td>'+Number(me.selectItem.price_sales).toLocaleString()+'</td>'+
        				'<td class="sl">'+$("#sluong").val()+'</td>'+
        				'<td><input type="hidden" name="pro_id[]" value="'+value+'"><input type="hidden" name="color_pro[]" value="'+color+'"><input type="hidden" name="sl_pro[]" class="sl_'+value+'" value="'+$("#sluong").val()+'"><input type="hidden" name="price_pro[]" class="price_'+value+'" value="'+product_price+'"><a onclick="pages.order.deleteProduct('+value+'_'+color+')"> <i class="icon-bin"></i> Xóa</a></td>'+
        				'</tr>';
            			$("#listproductbody").append(html);
            		}
        			var price = parseInt($("#sluong").val())*product_price;
        			priceTotal+=price;
        			$("#total_show").text(Number(priceTotal).toLocaleString());
        			$("#total_hide").val(priceTotal);
        			//
        			if( $("#code").val().trim() != '' ){
        				 $(".addpromotion").click();
        			} else {
        				$("#total_hide_down").val(priceTotal);
                    	$("#total_show_down").text(Number(priceTotal-admin_discount).toLocaleString());
        			}
            	}
            });
			$(document).on('change', '.choose', function() {
                var action = $(this).attr('id');
				console.log(action);
                var ma_id = $(this).val();
                var result = '';
                if(action =='province'){
                    result ='district';
                }else if(action =='district'){
                    result = 'wards';
                }
                $.ajax({
                    url: '/admin/order/select',
                    type: 'POST',
                    data: {action: action,ma_id:ma_id},
                    success: function (data) {
                        $('#'+result).html(data);
                    },
                    error: function (data) {
                    }
                });
            });
            function formatRepoUser (r) {
	        	if (r.loading) return r.text;
	        	var m = '<div>'+r.title+' ( '+r.first_name+' '+ r.last_name+' email:'+r.email+')</div>';	
	            return m;
	        }
		    function formatRepoSelectionUser (r) {
		    	if( r.id == "" ){
	        		return r.text;
	        	} else {
	        		if( r.selected == true ){
	        			return r.text;
	        		}
	        	}
		    	
		    	$("#hiddenId").val(r.id);
		    	if($("#name").val().trim() == ''){
		    		$("#name").val(r.first_name+' '+ r.last_name);
		    	}
		    	if($("#email").val().trim() == ''){
		    		$("#email").val(r.email);
		    	}
		    	return r.title;
		    }
            function formatRepo (repo) {
                if (repo.loading) return repo.text;
                var price = Number(repo.price).toLocaleString();
                var sale_price = '';
                if( pages.core.isDefined(repo.price_sales) && repo.price_sales != '' && repo.price_sales > 0 ){
                	sale_price = Number(repo.price_sales).toLocaleString();
                	price = price +' - ' + sale_price;
                }
                 
                var markup = '<div><img width="20px" height="20px" src="/upload/images/'+repo.image+'" /> <b>'+repo.title+' </b>( <span style="color:red;">'+ price +' VNĐ</span>)</div>';	
                return markup;
            }

	          function formatRepoSelection (repo) {
	        	if( repo.id == "" ){
	        		return repo.text;
	        	}
	        	var sale_price = '';
	        	var price = Number(repo.price).toLocaleString();
                if( pages.core.isDefined(repo.price_sales) && repo.price_sales != '' && repo.price_sales > 0 ){
                	sale_price = Number(repo.price_sales).toLocaleString();
                	price = price +' - ' + sale_price;
                }
                me.selectItem = { title: repo.title, price: repo.price, price_sales: repo.price_sales, img: repo.image, url: repo.url_product};
	            return repo.title+' ('+  price +' VNĐ)';
	          }
	          $("#userId").select2({
	        	  placeholder: 'Chọn User',
	            	ajax: {
	    		    url: "/admin/order/list-user",
	    		    dataType: 'json',
	    		    delay: 150,
	    		    data: function (p) {
	    		      return {
	    		        q: p.term, // search term
	    		        page: p.pages
	    		      };
	    		    },
	    		    processResults: function (data, p) {
	    		      p.pages = p.pages || 1;

	    		      return {
	    		        results: data.list,
	    		        pagination: {
	    		          more: (p.pages * 30) < data.total_count
	    		        }
	    		      };
	    		    },
	    		    cache: false
	    		  },
	    		  escapeMarkup: function (m) { return m; }, // let our custom formatter work
	    		  minimumInputLength: 2,
	    		  templateResult: formatRepoUser, // omitted for brevity, see the source of this page
	    		  templateSelection: formatRepoSelectionUser // omitted for brevity, see the source of this page
	    	});
	          $("#product").select2({
	        	  	placeholder: 'Chọn Sản Phẩm',
	            	ajax: {
	    		    url: "/admin/order/list-product",
	    		    dataType: 'json',
	    		    delay: 250,
	    		    data: function (params) {
	    		      return {
	    		        q: params.term, // search term
	    		        page: params.page
	    		      };
	    		    },
	    		    processResults: function (data, params) {
	    		      // parse the results into the format expected by Select2
	    		      // since we are using custom formatting functions we do not need to
	    		      // alter the remote JSON data, except to indicate that infinite
	    		      // scrolling can be used
	    		      params.page = params.page || 1;

	    		      return {
	    		        results: data.items,
	    		        pagination: {
	    		          more: (params.page * 30) < data.total_count
	    		        }
	    		      };
	    		    },
	    		    cache: true
	    		  },
	    		  escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
	    		  minimumInputLength: 2,
	    		  templateResult: formatRepo, // omitted for brevity, see the source of this page
	    		  templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
	    		});
	          $('#product').on('select2:select', function (e) {
	        	  var data = e.params.data;
	        	  $("#product_color option").hide();
	        	  color = data.product_color
	        	  if( color !== undefined  && color != ''){
	        		  color = color.split(",");
	        		  $.each(color,function(key, item){
	        			  $("#product_color option[value='"+item+"']").show();
	        			  if( key == 0 ){
	        				  $("#product_color").val(item);
	        			  }
	        		  });
	        		  
	        	  } else {
	        		  $("#product_color option[value='1']").show();
	        		  $("#product_color").val(1);
	        	  }
	          });
	          $("#save-status").click(function(){
	        	  $("#modalStatus").modal('hide');
	        	  id = $(this).attr('data-id');
	        	  user_id = $(this).attr('data-user');
	        	  $.ajax({
	                  'url': '/admin/order/update-status',
	                  'type': 'GET',
	                  'data': {
                              id: id, status: $('#status-update :selected').val(), 
                              user_id: user_id,
                              reason: $("#reject_reason").val(),
                              admin_discount: $("#admin_discount").val()
                          },
	                  beforeSend: function () {

	                  },
	                  success: function (data) {
	                      if (data.Code > 0) {
	                          if ($("#orderTable").length > 0) {
	                              bootbox.alert('Update thành công');
	                              var t = $("#orderTable").DataTable();
	                              t.draw();
	                          }
	                      } else {
	                          bootbox.alert('Update thất bại');
	                      }
	                  },
	                  error: function (data) {

	                  }
	              });
	          });
            
        },
        deleteProduct : function( id ){
        	var price = parseInt($(".price_"+id).val());
        	var sl = parseInt($(".sl_"+id).val());
        	var total = sl*price;
        	var total_hide = parseInt($("#total_hide").val());
        	var admin_discount = parseInt($("#admin_discount").val());
        	var div = total_hide - total;
        	$("#total_show").text(Number(div).toLocaleString());
        	$("#total_hide").val(div);
        	$('.itemproduct[data-idx="'+id+'"]').remove();
			//
			if( $("#code").val().trim() != '' ){
				 $(".addpromotion").click();
			} else {
				$("#total_hide_down").val(div);
            	$("#total_show_down").text(Number(div-admin_discount).toLocaleString());
			}
        },
        initValidation: function () {
            var loptions = {
                rules: {
                    name: {
                        required: true
                    },
                    address: {
                        required: true
                    },
                    place: {
                        required: true
                    },
                    phone: {
                    	required: true
                    }
                },
                messages: {
                    name: {
                        required: $("#name").attr('data-msg')
                    },
                    address: {
                        required: $("#address").attr('data-msg')
                    },
                    place: {
                        required: $("#place").attr('data-msg')
                    },

                    phone: {
                        required: $("#phone").attr('data-msg')
                    },
                }
            };
            pages.validation.setupValidation("#orderForm", loptions);

        },
        initDatatable: function () {
            var aoColumns = [
                {"data": "order_code"},
                {"data": "user_id"},
                {"data": "phone"},
                {"data": "email"},
                {"data": "total"},
                {"data": "is_pay"},
                {"data": "status"},
                {"data": "created_date"},
                {"data": "Action_Table"}
            ];
            var columnDefs = [
				{
				    "render": function (data, type, row) {
                                        var t = row['name'];
                                        if( t.length > 10 ){
                                            t = t.substring(0,10)+'...';
                                            return '<span title="'+row['name']+'">'+t+'</span>';
                                        } else {
                                            return row['name'];
                                        }
				        
				    },
				    "className": "text-center",
				    "targets": 1,
				    "orderable": false,
				    "data": "user_id"
				},
                                {
				    "render": function (data, type, row) {
                                        var t = row['email'];
                                        if( t.length > 15 ){
                                            t = t.substring(0,15)+'...';
                                            return '<span title="'+row['email']+'">'+t+'</span>';
                                        } else {
                                            return row['email'];
                                        }
				        
				    },
				    "className": "text-center",
				    "targets": 3,
				    "orderable": false,
				    "data": "email"
				},
				{
				    "render": function (data, type, row) {
						var total = row['total'];
				    	if( row['admin_discount'] != undefined && row['admin_discount'] > 0 ){
				    		total = total - row['admin_discount'];
				    	}
				        return 	Number(total).toLocaleString();
				    },
				    "className": "text-center",
				    "targets": 4,
				    "orderable": false,
				    "data": "total"
				},
				{
				    "render": function (data, type, row) {
				    	var pay = 'No';
				    	if( row['is_pay'] != '' && row['is_pay'] != '' && row['is_pay'] == 1){
				    		pay = 'Yes';
				    	}
				        return 	pay;
				    },
				    "className": "text-center",
				    "targets": 5,
				    "orderable": false,
				    "data": "is_pay"
				},
				{
				    "render": function (data, type, row) {
				    	var st = 'Đang Xử Lý';
				    	if( row['status'] == 2 ){
				    		st = '<span style="color:blue;">Xác Nhận</span>';
				    	} else if( row['status'] ==3 ){
				    		st = '<span style="color:darkblue;">Đang Vận Chuyển</span>';
				    	} else if( row['status'] == 4 ){
				    		st = '<span style="color:green;font-size: 15px;">Giao Thành Công</span>';
				    	} else if( row['status'] == 5){
				    		st = '<span style="color:red;">Hủy</span>';
				    	}
				    	return st;
				    },
				    "className": "text-center",
				    "targets": 6,
				    "orderable": false,
				    "data": "status"
				},
                                {
				    "render": function (data, type, row) {
				    	var d = row['created_date'];
                                        d = new Date(d);
                                        var date = d.getDate();
                                        if( date < 10) {  date = '0'+date;}
                                        var month = d.getMonth()+1;
                                        if( month < 10) {  month = '0'+month;}
				    	return date+'/'+month+'-'+d.getFullYear();
				    },
				    "className": "text-center",
				    "targets": 7,
				    "orderable": true,
				    "data": "created_date"
				},
                {
                    "render": function (data, type, row) {
                        var action = '<ul class="icons-list" >' +
                                '<li class="dropdown" >' +
                                '<a href="#" class="dropdown-toggle" data-toggle="dropdown">' +
                                '<i class="icon-menu9"> </i></a>' +
                                '<ul class="dropdown-menu dropdown-menu-right">';
                        action += '<li> <a href="/admin/order/detail/id/' + row.id + '" > <i class="icon-pencil3"></i> Sửa</a > </li>';
                        action += '<li> <a onclick="pages.order.deleteOrder(' + row.id + ')" > <i class="icon-bin"></i> Xóa</a > </li>';
                        if( row['is_pay'] == 0 ){
                        	action += '<li> <a onclick="pages.order.updateIsPay(' + row.id + ',1)" > <i class="fa fa-money"></i> Thanh Toán</a > </li>';
                        } else {
                        	action += '<li> <a onclick="pages.order.updateIsPay(' + row.id + ',0)" > <i class="fa fa-money"></i>Hủy Thanh Toán</a > </li>';
                        }
                        if( row['status'] != 5 ){
                            if(row['reject_reason'] === undefined || row['reject_reason'] == null){
                                row['reject_reason'] = '';
                            }
                            if(row['admin_discount'] === undefined || row['admin_discount'] == null){
                                row['admin_discount'] = 0;
                            }
                            action += '<li> <a onclick="pages.order.updateStatus(' + row.id + ','+row['status']+','+row['user_id']+',\''+row['reject_reason']+'\','+row['admin_discount']+')" > <i class="fa fa-shield"></i> Cập Nhật Trạng Thái</a > </li>';
                        }
                        action += '</ul>';
                        return 	action;
                    },
                    "className": "text-center",
                    "targets": 8,
                    "orderable": false,
                    "data": "Action_Table"
                }
            ];
            pages.common.setupDataTable("#orderTable", "/admin/order/list", aoColumns, columnDefs, {order: [[0, "desc"]]});
        },
        updateIsPay: function(id, is_pay){
        	$.ajax({
                'url': '/admin/order/update-ispay',
                'type': 'GET',
                'data': {id: id, is_pay: is_pay},
                beforeSend: function () {

                },
                success: function (data) {
                    if (data.Code > 0) {
                        if ($("#orderTable").length > 0) {
                            bootbox.alert('Update thành công');
                            var t = $("#orderTable").DataTable();
                            t.draw();
                        }
                    } else {
                        bootbox.alert('Update thất bại');
                    }
                },
                error: function (data) {

                }
            });
        },
        updateStatus: function(id, currentStauts, user_id, note, discount){
        	console.log(discount);
        	$("#status-update").val(currentStauts);
        	$("#status-update").change();
        	$("#reject_reason").val(note);
        	$("#admin_discount").val(discount);
        	$("#save-status").attr('data-id',id);
        	$("#save-status").attr('data-user',user_id);
        	$("#modalStatus").modal('show');
        },
        deleteOrder: function (id) {
            bootbox.confirm('Bạn có muốn xóa khuyến mãi này?', function (result) {
                if (result) {
                    $.ajax({
                        'url': '/admin/order/delete',
                        'type': 'GET',
                        'data': {id: id},
                        beforeSend: function () {

                        },
                        success: function (data) {
                            if (data.Code > 0) {
                                if ($("#orderTable").length > 0) {
                                    bootbox.alert('Xóa thành công');
                                    var t = $("#orderTable").DataTable();
                                    t.draw();
                                }
                            } else {
                                bootbox.alert('Xóa thất bại');
                            }
                        },
                        error: function (data) {

                        }
                    });
                }
            });
        },
        approveAll: function () {
            bootbox.confirm('Bạn có muốn xóa duyệt tất cả đơn hàng?', function (result) {
                if (result) {
                    $.ajax({
                        'url': '/admin/order/approve-all',
                        'type': 'POST',
                        'data': {},
                        beforeSend: function () {

                        },
                        success: function (data) {
                            if (data.Code > 0) {
                                if ($("#orderTable").length > 0) {
                                    bootbox.alert('Duyệt tất cả đơn hàng thành công');
                                    var t = $("#orderTable").DataTable();
                                    t.draw();
                                }
                            } else {
                                bootbox.alert('Duyệt tất cả đơn hàng thất bại');
                            }
                        },
                        error: function (data) {

                        }
                    });
                }
            });
        },
        deleteAll: function(){
            bootbox.confirm('Bạn có muốn xóa tất cả order bị hủy?', function(result) {
                    if (result) {
                          $.ajax({
        	                  'url': '/admin/order/delete-all',
        	                  'type': 'GET',
        	                  'data': { },
        	                  beforeSend: function () {
        
        	                  },
        	                  success: function (data) {
        	                      if (data.Code > 0) {
        	                          if ($("#orderTable").length > 0) {
        	                              bootbox.alert('Xóa thành công');
        	                              var t = $("#orderTable").DataTable();
        	                              t.draw();
        	                          }
        	                      } else {
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