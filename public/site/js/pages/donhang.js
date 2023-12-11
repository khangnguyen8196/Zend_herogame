/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(function () {
    pages.donhang.init();
});

if (!pages) {
    var pages = {};
}
pages = $.extend(pages, {
    donhang: {
        currentCode: '',
        init: function () {
            var me = this;
            $(document).on('submit','form#checkoutForm',function(){
            	 e.preventDefault();
                $(".loader").attr('data-text','Vui lòng đợi trong giây lát').addClass('is-active');
            });
            $(document).on('click', '.remove_pr', {}, function (e) {
                e.preventDefault();
                pages.donhang.removeProduct($(this).attr("pid"), $(this).attr("variantid"));
            });
            $(document).on('click', '.update_pr', {}, function (e) {
                e.preventDefault();
                pages.donhang.updateProduct();
            });
            $(document).on('click', '.update_cb', {}, function (e) {
                e.preventDefault();
                pages.donhang.updateCombo();
            });
            $(document).on('click', '#payment_btn', {}, function (e) {
                e.preventDefault();
                pages.donhang.checkOrder();
            });

            $(document).ready(function() {
                var total = parseInt($('#currTotalPrice').val(), 10);
                var checkedValue = parseInt($('#checked-value').val(), 10);
                if(total < checkedValue){
                    $('#no-cod').prop('checked', true);
                }else if(total >= checkedValue) {
                    // $('#has-cod').prop('checked', true);
                    $('.has-cod').removeClass('hiddens');
                    $('.message-game').removeClass('hiddens');
                }
            });

            $(document).on('click','#submit-btn-pay',function(e){
                var isChecked = $('input[name="cod"]:checked').length > 0;
                if (!isChecked) {
                    $('#popup').show();
                    e.preventDefault();
                }  
            })

            $('#closePopup').click(function(e) {
                $('#popup').hide();
                e.preventDefault();
            });

            $("#btnPromotion").click(function(){
                var value = $("#promotionCode").val();
                var province = $('.province').val().trim();
                var district = $('.district').val().trim();
                var wards = $('.wards').val().trim();
                var fee_cod = $('input[name="cod"]:checked').val().trim();
                var totalPrice = $('#totalPrice').text();
                me.checkAndGetPromotion(value, province, district, wards, fee_cod, totalPrice);
            });
            $("#btnDisCount").click(function(){
                var value = $("#discount").val();
                // var value = $("#promotionCode").val();
                var province = $('.province').val().trim();
                var district = $('.district').val().trim();
                var wards = $('.wards').val().trim();
                var fee_cod = $('input[name="cod"]:checked').val().trim();
                me.checkAndGetDiscount(value, province, district, wards, fee_cod);
            });
            $("#discount").maskNumber({integer: true});
            $(".cancel-order").click(function(){
                var code = $(this).attr('data-code');
                if( code  != ''){
                    me.currentCode = code;
                    $("#alertModalRejectOrder").modal('show');
                }
            });
            $(".rejectOrder").click(function(){
                $("#alertModalRejectOrder").modal('hide');
                if( me.currentCode != ''){
                    me.rejectOrder(me.currentCode);
                }
            });
            
            $("#checkoutForm #cfa_phone").keydown(function (e) {
                // Allow: backspace, delete, tab, escape, enter and .
                if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                     // Allow: Ctrl+A, Command+A
                    (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) || 
                     // Allow: home, end, left, right, down, up
                    (e.keyCode >= 35 && e.keyCode <= 40)) {
                         // let it happen, don't do anything
                         return;
                }
                // Ensure that it is a number and stop the keypress
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }
            });

            $(document).on('change', '.choose', function() {
                var action = $(this).attr('id');
                var ma_id = $(this).val();
                var result = '';
                if(action =='province'){
                    result ='district';
                }else if(action =='district'){
                    result = 'wards';
                }
                $.ajax({
                    url: '/site/don-hang/select',
                    type: 'POST',
                    data: {action: action,ma_id:ma_id},
                    success: function (data) {
                        $('#'+result).html(data);
                    },
                    error: function (data) {
                    }
                });
            });
            
            $(document).on('change','.province', function(){
                if($('#province').val() != ''){
                    $('select#province').css('color','black')
                }else{
                    $('select#province').css('color','#9090909c')
                }
            });

            $(document).on('change','.district', function(){
                if($('#district').val() != ''){
                    $('select#district').css('color','black')
                }else{
                    $('select#district').css('color','#9090909c')
                }
            });

            $(document).on('change','.wards', function(){
                if($('#wards').val() != ''){
                    $('select#wards').css('color','black')
                }else{
                    $('select#wards').css('color','#9090909c')
                }
                var phi_cod = $('#has-cod').prop('checked');
                var province = $('.province').val().trim();
                var district = $('.district').val().trim();
                var wards = $('.wards').val().trim();
                var token = $.cookie("token");
                var timestamp = new Date().getTime();
                if((province == 1 && phi_cod == true) || (province == 79 && phi_cod == true) ) {
                    $('.fee-cod').css('display','block');
                    $('#fee-cod').html('0đ');
                    $('#fee-cod-last').val(0)
                    var totalPrice = parseFloat($('#currTotalPrice').val());
                    var khuyenmai = $('#priceDiscount').text();
                    var khuyenmai = khuyenmai.replace(/,/g, '');
                    var feeCod = parseFloat($('#fee-cod-last').val());
                    $.ajax({
                        url: '/site/don-hang/fee-ship?timestamp=' + timestamp,
                        type: 'POST',
                        data: {province: province, district:district,wards:wards, token:token, phi_cod:phi_cod },
                        success: function (data) {
                            var total = 0;
                            if(data){
                                $('#fee-ship').html('0đ');
                                var fee = parseFloat(data.data[0]);
                                $('#fee-ship').removeClass('hidden');
                                if(khuyenmai == 0 || khuyenmai ==''){
                                    total = numberFormat(fee +totalPrice+feeCod)+ '&#8363';
                                    $('#totalPrice').html(total);
                                    $('#fee-ship-last').val(fee);
                                }else {
                                    total = numberFormat(totalPrice - khuyenmai + fee +feeCod)+'&#8363';
                                    $('#totalPrice').html(total);
                                    $('#fee-ship-last').val(fee);
                                }

                                $("#fee_shipping_order").val(fee);
                                $('#fee_cod_order').val(feeCod);
                                $('.custom-control-description-check').html('Có máy game ở TP.HCM hoặc Hà nội free phí vận chuyển và phí cod');
                            }
                        },
                        error: function (data) {
                        }
                    });
                }else if((province != 1 && phi_cod == true) || (province != 79 && phi_cod == true) ) {
                    var totalPrice = parseFloat($('#currTotalPrice').val());
                    var khuyenmai = $('#priceDiscount').text();
                    var khuyenmai = khuyenmai.replace(/,/g, '');
                    $.ajax({
                        url: '/site/don-hang/fee-ship?timestamp=' + timestamp,
                        type: 'POST',
                        data: {province: province, district:district,wards:wards, token:token, phi_cod:phi_cod },
                        success: function (data) {
                            if(data){
                                var total = 0;
                                var feeCod = parseFloat(data.data[1]);
                                $('#fee-cod').html(numberFormat(data.data[1])+ '&#8363');
                                $('#fee-cod-last').val(feeCod)
                                if(data.data[0] > 0){
                                    $('#fee-ship').html(numberFormat(data.data[0])+ '&#8363');
                                }else {
                                    $('#fee-ship').html('0đ');
                                }
                                var fee = parseFloat(data.data[0]);
                                $('#fee-ship').removeClass('hidden');
                                if(khuyenmai == 0 || khuyenmai ==''){
                                    total = numberFormat(fee +totalPrice+feeCod)+ '&#8363';
                                    $('#totalPrice').html(total);
                                    $('#fee-ship-last').val(fee);
                                }else {
                                    total = numberFormat(totalPrice - khuyenmai + fee +feeCod)+'&#8363';
                                    $('#totalPrice').html(total);
                                    $('#fee-ship-last').val(fee);
                                }

                                $('#fee_shipping_order').val(fee);
                                $('#fee_cod_order').val(feeCod);
                                $('.custom-control-description-check').html('Có máy game liên tỉnh')
                            }
                        },
                        error: function (data) {
                        }
                    });
                }else {
                    var totalPrice = parseFloat($('#currTotalPrice').val());
                    var khuyenmai = $('#priceDiscount').text();
                    var khuyenmai = khuyenmai.replace(/,/g, '');
                    var feeCod = parseFloat($('#fee-cod-last').val());
                    $.ajax({
                        url: '/site/don-hang/fee-ship?timestamp=' + timestamp,
                        type: 'POST',
                        data: {province: province, district:district,wards:wards, token:token },
                        success: function (data) {
                            var total = 0;
                            if(data){
                                if(data.data[0] > 0){
                                    $('#fee-ship').html(numberFormat(data.data[0])+ '&#8363');
                                }else {
                                    $('#fee-ship').html('0đ');
                                }
                                var fee = parseFloat(data.data[0]);
                                $('#fee-ship').removeClass('hidden');
                                if(khuyenmai == 0 || khuyenmai ==''){
                                    total = numberFormat(fee +totalPrice+feeCod)+ '&#8363';
                                    $('#totalPrice').html(total);
                                    $('#fee-ship-last').val(fee);
                                }else {
                                    total = numberFormat(totalPrice - khuyenmai + fee +feeCod)+'&#8363';
                                    $('#totalPrice').html(total);
                                    $('#fee-ship-last').val(fee);
                                }

                                $("#fee_shipping_order").val(fee);
                            }
                        },
                        error: function (data) {
                        }
                    });
                }
            })

            $(document).on('click','#has-cod',function(){
                var fee_cod = $('#has-cod').val();
                var province = $('#province').val();
                var timestamp = new Date().getTime();
                if(province == 1 || province == 79){
                    $('.fee-cod').css('display','block');
                    $('#fee-cod').html('0đ');
                    $('#fee-cod-last').val(0);
                    $('#fee-ship').html('0đ');
                    $.ajax({
                        url: '/site/don-hang/fee-cod?timestamp=' + timestamp,
                        type: 'POST',
                        data: {fee_cod: fee_cod, province:province},
                        success: function (data) {
                            var khuyenmai = $('#priceDiscount').text();
                            var khuyenmai = khuyenmai.replace(/,/g, '');
                            var total = 0;
                            if(data){
                                var feeShip=  parseFloat(data.data[0]);
                                var totalPrice = parseFloat($('#currTotalPrice').val());
                                var feeCod = parseFloat(data.data[0]);
                                $('#fee-cod-last').val(feeCod)
                                $('.fee-cod').css('display','block');
                                $('#fee-cod').html(numberFormat(feeCod)+ '&#8363');
                                if(khuyenmai == 0 || khuyenmai ==''){
                                        total = numberFormat(totalPrice+feeCod+feeShip)+ '&#8363';
                                        $('#totalPrice').html(total);
                                }
                                else{
                                    total = numberFormat(totalPrice+feeCod+feeShip -khuyenmai)+ '&#8363';
                                    $('#totalPrice').html(total);
                                }
                                $('#fee_cod_order').val(feeCod);
                                $('#fee_shipping_order').val(feeShip)
                                $('#fee-cod').html('0đ');
                                $('.custom-control-description-check').html('Có máy game ở TP.HCM hoặc Hà nội free phí vận chuyển và phí cod')
                            }
                        },
                        error: function (data) {
                        }
                    });
                } else{
                    $.ajax({
                        url: '/site/don-hang/fee-cod?timestamp=' + timestamp,
                        type: 'POST',
                        data: {fee_cod: fee_cod},
                        success: function (data) {
                            var khuyenmai = $('#priceDiscount').text();
                            var khuyenmai = khuyenmai.replace(/,/g, '');
                            var total = 0;
                            if(data){
                                var feeShip=  parseFloat($('#fee-ship-last').val());
                                var totalPrice = parseFloat($('#currTotalPrice').val());
                                var feeCod = parseFloat(data.data[0]);
                                $('#fee-cod-last').val(feeCod)
                                $('.fee-cod').css('display','block');
                                $('#fee-cod').html(numberFormat(feeCod)+ '&#8363');
                                if(khuyenmai == 0 || khuyenmai ==''){
                                        total = numberFormat(totalPrice+feeCod+feeShip)+ '&#8363';
                                        $('#totalPrice').html(total);
                                }
                                else{
                                    total = numberFormat(totalPrice+feeCod+feeShip -khuyenmai)+ '&#8363';
                                    $('#totalPrice').html(total);
                                }
                                $('#fee_cod_order').val(feeCod);
                                $('.custom-control-description-check').html('Có máy game liên tỉnh')
                            }
                        },
                        error: function (data) {
                        }
                    });
                }
            })
            $(document).on('click','#no-cod',function(){
                var province = $('.province').val().trim();
                var district = $('.district').val().trim();
                var wards = $('.wards').val().trim();
                var non_cod = $('#no-cod').prop('checked');
                $('.custom-control-description-check').html('Có máy game')
                if(non_cod && province ==''){
                    $('#fee-cod').html('');
                    // $('.fee-cod').css('display','none');
                    var fee_code = $('#fee-cod-last').val();
                    var totalPresent = $('#totalPrice').text();
                    var totalPresent = totalPresent.replace(/[,đ₫]/g, '');
                    var total = numberFormat(totalPresent - fee_code)+ '&#8363';
                    $('#totalPrice').html(total);
                }
                if(non_cod && province && district && wards){
                    var totalPrice = parseFloat($('#currTotalPrice').val());
                    var khuyenmai = $('#priceDiscount').text();
                    var khuyenmai = khuyenmai.replace(/,/g, '');
                    var timestamp = new Date().getTime();
                    var token = $.cookie("token");
                    $.ajax({
                        url: '/site/don-hang/fee-ship?timestamp=' + timestamp,
                        type: 'POST',
                        data: {province: province, district:district,wards:wards, token:token,non_cod:non_cod },
                        success: function (data) {
                            console.log(data)
                            var total = 0;
                            if(data){
                                if(data.data[0] > 0){
                                    $('#fee-ship').html(numberFormat(data.data[0])+ '&#8363');
                                }else {
                                    $('#fee-ship').html('0đ');
                                }
                                var fee = parseFloat(data.data[0]);
                                var feeCod = 0;
                                $('#fee-cod').html('0đ');
                                $('#fee-ship').removeClass('hidden');
                                $('#fee-cod-last').val(0);
                                $('#fee_cod_order').val(0);
                                if(khuyenmai == 0 || khuyenmai ==''){
                                    total = numberFormat(fee +totalPrice+feeCod)+ '&#8363';
                                    $('#totalPrice').html(total);
                                    $('#fee-ship-last').val(fee);
                                }else {
                                    total = numberFormat(totalPrice - khuyenmai + fee +feeCod)+'&#8363';
                                    $('#totalPrice').html(total);
                                    $('#fee-ship-last').val(fee);
                                }

                                $("#fee_shipping_order").val(fee);
                            }
                        },
                        error: function (data) {
                        }
                    });
                }

            })
            function numberFormat (number, decimals, decPoint, thousandsSep) {
                number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
                var n = !isFinite(+number) ? 0 : +number;
                var prec = !isFinite(+decimals) ? 0 : Math.abs(decimals);
                var sep = (typeof thousandsSep === 'undefined') ? ',' : thousandsSep;
                var dec = (typeof decPoint === 'undefined') ? '.' : decPoint;
                var s = '';
            
                var toFixedFix = function (n, prec) {
                    var k = Math.pow(10, prec);
                    return '' + (Math.round(n * k) / k)
                };
            
                s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
                if (s[0].length > 3) {
                    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
                }
                if ((s[1] || '').length < prec) {
                    s[1] = s[1] || '';
                    s[1] += new Array(prec - s[1].length + 1).join('0');
                }
                return s.join(dec);
            }
            
        },
        rejectOrder: function(code){
            var me = this;
            $.ajax({
                    url: "/don-hang/reject-order",
                    type: 'POST',
                    data: { code: code},
                    beforeSend: function () {
                        $(".loader").attr('data-text','Vui lòng đợi trong giây lát').addClass('is-active');
                    },
                    success: function (data) {
                        $(".loader").removeClass('is-active');
                        if( data.Code == 1 ){
                            $("#alertResult #result-text").text(data.Message);
                            $("#alertResult").modal('show');
                            me.currentCode = '';
                            location.reload();
                        } else {
                            $("#alertResult #result-text").text(data.Message);
                            $("#alertResult").modal('show');
                        }
                    },
                    error: function () {
                       $("#alertResult #result-text").text('Hủy hóa đơn thất bại, vui lòng thử lại');
                       $("#alertResult").modal('show');
                       $(".loader").removeClass('is-active');
                    }
                });
        },
        checkAndGetDiscount: function( percent, province, district, wards, fee_cod ){
            var me = this;
            if( percent != undefined && percent != ""){
                var token = $.cookie("token");
                $(".error-discount").removeClass('text-success').addClass('error');
                $(".error-discount").hide();
                $("#checkdisCount").val(false);
                $("#priceDiscount").text(0);
                $("#totalPrice").text($("#totalPrice").attr('data-total'));
                $.ajax({
                    url: "/don-hang/check-discount",
                    type: 'POST',
                    data: { percent: percent, t: token, province:province, district:district,wards:wards, fee_cod:fee_cod},
                    beforeSend: function () {
                        $("#btnDisCount").hide();
                        $(".loading_discount").show();
                    },
                    success: function (data) {
                        $("#btnDisCount").show();
                        $(".loading_discount").hide();
                        $(".error-discount").text(data.Message);
                        $(".error-discount").show();
                        if( data.Code == 1 ){
                            me.resetForm(1);
                            $("#priceDiscount").text(data.Data.discountText);
                            $("#checkdisCount").val(true);
                            $(".error-discount").removeClass('error').addClass('text-success');
                            var totalPrice1 = data.Data.aTotalText+'đ'
                            $("#totalPrice").text(totalPrice1);
                        }
                    },
                    error: function () {
                        $(".error-discount").text('Lỗi Xảy Ra Vui Lòng Thực Hiện Lại');
                        $(".error-discount").show();
                        $("#btnDisCount").show();
                        $(".loading_discount").hide();
                    }
                });
            }
        },
        checkAndGetPromotion: function( code, province, district, wards,fee_cod, totalPrice ){
            var me = this;
            if( code != undefined && code != ""){
                var token = $.cookie("token");
                $(".error-promotion").removeClass('text-success').addClass('error');
                $(".error-promotion").hide();
                $("#checkPromotion").val(false);
                $("#priceDiscount").text(0);
                $("#totalPrice").text($("#totalPrice").attr('data-total'));
                $.ajax({
                    url: "/don-hang/check-promotion",
                    type: 'POST',
                    data: {code: code, t: token , province:province, district:district,wards:wards, fee_cod:fee_cod},
                    beforeSend: function () {
                        $("#btnPromotion").hide();
                        $(".loading_promotion").show();
                    },
                    success: function (data) {
                        $("#btnPromotion").show();
                        $(".loading_promotion").hide();
                        $(".error-promotion").text(data.Message);
                        $(".error-promotion").show();
                        if( data.Code == 1 ){
                            me.resetForm(2);
                            $("#priceDiscount").text(data.Data.caclText);
                            $("#checkPromotion").val(true);
                            $(".error-promotion").removeClass('error').addClass('text-success');
                            var totalPrice1 = data.Data.aTotalText +'đ'
                            $("#totalPrice").text(totalPrice1);
                        }else{
                            var totalPrice1 = totalPrice +'đ'
                            $("#totalPrice").html(totalPrice1);
                        }
                    },
                    error: function () {
                        $(".error-promotion").text('Lỗi Xảy Ra Vui Lòng Thực Hiện Lại');
                        $(".error-promotion").show();
                        $("#btnPromotion").show();
                        $(".loading_promotion").hide();
                    }
                });
            }
        },
        
        resetForm: function( type ){
            if( type === 1 ){
                // reset promotion
                $("#promotionCode").val('');
                $("#checkPromotion").val(false);
                $(".error-promotion").hide();
                $("#totalPrice").text($("#totalPrice").attr('data-total'));
            } else {
                $("#discount").val(0);
                $("#checkdisCount").val(false);
                $(".error-discount").hide();
                $("#totalPrice").text($("#totalPrice").attr('data-total'));
            }
        },
        
        /**
         * 
         * @returns {undefined}
         */
        removeProduct: function (id, variant_id) {
            var token = $.cookie("token");
            $.ajax({
                url: "/don-hang/xoa-san-pham",
                type: 'POST',
                data: {pid: id, variantid: variant_id, t: token},
                beforeSend: function () {
                    $(".update_pr").prop("disabled", true);
                    $(".remove_pr").prop("disabled", true);
                },
                success: function (data) {
                    $(".update_pr").prop("disabled", false);
                    $(".remove_pr").prop("disabled", false);
                    if (data.Code > 0) {
                        location.reload();
                    }
                },
                error: function () {
                    $(".update_pr").prop("disabled", false);
                    $(".remove_pr").prop("disabled", false);
                }
            });
        },
        /**
         * 
         */
        updateProduct: function ( ) {
            if ($("input.pr_qty").length > 0) {
                var data = {};
                var t = $.cookie("token");
                
                $.each($("input.pr_qty"), function (k, v) {
                    var pid = $(v).attr("pid");
                    var qty = $(v).val();
                    var variant_id = $(v).attr("variantid");
                    if( isNaN(qty) == true){
                        alert("Số lượng sản phẩm không hợp lệ!");
                        return false;
                    }
                    if( variant_id.length > 0 ){
                        data[ pid+"|"+ variant_id] = qty;
                    }else{
                        data[ pid ] = qty;
                    }
                });
                
                $.ajax({
                    url: "/don-hang/cap-nhat-don-hang",
                    type: 'POST',
                    data: {data: data, t:t},
                    beforeSend: function () {
                        $(".update_pr").prop("disabled", true);
                        $(".remove_pr").prop("disabled", true);
                    },
                    success: function (data) {
                        $(".update_pr").prop("disabled", false);
                        $(".remove_pr").prop("disabled", false);
                        if (data.Code > 0) {
                            window.location = "/don-hang/gio-hang";
                        }
                    },
                    error: function () {
                        $(".update_pr").prop("disabled", false);
                        $(".remove_pr").prop("disabled", false);
                    }
                });
            }
        },
        updateCombo: function ( ) {
            if ($("input.cb_qty").length > 0) {
                var data = {};
                var t = $.cookie("token");
                
                $.each($("input.cb_qty"), function (k, v) {
                    var pid = $(v).attr("pid");
                    var qty = $(v).val();
                    var variant_id = $(v).attr("variantid");
                    if( isNaN(qty) == true){
                        alert("Số lượng sản phẩm không hợp lệ!");
                        return false;
                    }
                    if( variant_id.length > 0 ){
                        data[ pid+"|"+ variant_id] = qty;
                    }else{
                        data[ pid ] = qty;
                    }
                });
                
                $.ajax({
                    url: "/don-hang/cap-nhat-don-hang-combo",
                    type: 'POST',
                    data: {data: data, t:t},
                    beforeSend: function () {
                        $(".update_cb").prop("disabled", true);
                        $(".remove_pr").prop("disabled", true);
                    },
                    success: function (data) {
                        $(".update_cb").prop("disabled", false);
                        $(".remove_pr").prop("disabled", false);
                        if (data.Code > 0) {
                            window.location = "/don-hang/gio-hang";
                        }
                    },
                    error: function () {
                        $(".update_cb").prop("disabled", false);
                        $(".remove_pr").prop("disabled", false);
                    }
                });
            }
        },
        /**
         * 
         */
        checkOrder: function () {
            window.location = "/don-hang/dat-hang";
            // $.ajax({
            //     url: "/auth/kiem-tra-dang-nhap",
            //     type: 'POST',
            //     data: {},
            //     beforeSend: function () {
            //     	$(".loader").attr('data-text','Vui lòng đợi trong giây lát').addClass('is-active');
            //     },
            //     success: function (data) {
            //     	$(".loader").removeClass('is-active');
            //         if(data.Code > 0){
            //             window.location = "/don-hang/dat-hang";
            //         }else{
            //         	$("#signinModal").modal('show');
            //         }
            //     },
            //     error: function () {
            //     	$(".loader").removeClass('is-active');
            //     }
            // });
        },  
    }
});
