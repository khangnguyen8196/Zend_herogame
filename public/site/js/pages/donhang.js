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
            $("#btnPromotion").click(function(){
                var value = $("#promotionCode").val();
                me.checkAndGetPromotion(value);
            });
            $("#btnDisCount").click(function(){
                var value = $("#discount").val();
                me.checkAndGetDiscount(value);
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
        checkAndGetDiscount: function( percent ){
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
                    data: { percent: percent, t: token},
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
                            $("#totalPrice").text(data.Data.aTotalText);
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
        checkAndGetPromotion: function( code ){
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
                    data: {code: code, t: token},
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
                            $("#totalPrice").text(data.Data.aTotalText);
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
        }
    }
});
