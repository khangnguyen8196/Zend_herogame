/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$(function () {
    pages.sanpham.init();
});

if (!pages) {
    var pages = {};
}
pages = $.extend(pages, {
    sanpham: {
        init: function () {
            $(document).on('click', '.page-link', {}, function (e) {
                e.preventDefault();
                $("#filter_frm #page_size").val($("#item_limit").val());
                $("#filter_frm #sorted").val($("#sort_type").val());
                $("#filter_frm #page").val($(this).attr("page"));
                $("#filter_frm").submit();
            });

            $(document).on('change', '#item_limit, #sort_type', {}, function (e) {
                e.preventDefault();
                $("#filter_frm #page_size").val($("#item_limit").val());
                $("#filter_frm #sorted").val($("#sort_type").val());
                $("#filter_frm").submit();
            });

            $(document).on("click", "a.grid-view", {}, function (e) {
                e.preventDefault();
                $("a.list-view").removeClass("active");
                $("a.grid-view").addClass("active");
                $("div.product-view ul").removeClass("list");
                $("div.product-view ul").addClass("grid").addClass("grid-md-4").addClass("grid-2");

                var productViewType = "grid";
                $.cookie('product_view_type', productViewType, {expires: 7, path: '/'});
            });

            $(document).on("click", "a.list-view", {}, function (e) {
                e.preventDefault();
                $("a.grid-view").removeClass("active");
                $("a.list-view").addClass("active");
                $("div.product-view ul").removeClass("grid").removeClass("grid-md-4").removeClass("grid-2");
                $("div.product-view ul").addClass("list");

                var productViewType = "list";
                $.cookie('product_view_type', productViewType, {expires: 7, path: '/'});
            });
            
            if ($('.range-input').length > 0) {
                $('.range-input').slider().on('slideStop', function (data) {
                    var minRange = data.value[0];
                    var maxRange = data.value[1];
                    $("#filter_frm #minRange").val(minRange);
                    $("#filter_frm #maxRange").val(maxRange);
                    $("#filter_frm").submit();
                });
            }

            if ($("#filter_frm").length > 0 && $('.range-input').length > 0) {
                var min = 0;
                if ($('#filter_frm #minRange').length > 0 && $('#filter_frm #minRange').val() != undefined && $('#filter_frm #minRange').val() != '') {
                    min = parseInt($('#filter_frm #minRange').val());
                }
                var max = parseInt($('.range-input').attr('data-slider-max'));
                if ($('#filter_frm #maxRange').length > 0 && $('#filter_frm #maxRange').val() != undefined && $('#filter_frm #maxRange').val() != '') {
                    max = parseInt($('#filter_frm #maxRange').val());
                }
                $('.range-input').slider('setValue', [min, max]);
            }
            
            
            $(document).on("click", "#minus", {}, function (e) {
                var numberOfItem = $("#number").val();
                if (isNaN(numberOfItem) == true) {
                    numberOfItem = 1;
                }
                numberOfItem = (parseInt(numberOfItem) - 1);
                if (parseInt(numberOfItem) <= 1) {
                    numberOfItem = 1;
                }
                $("#number").val(numberOfItem);
            });
            
            $(document).on("click", "#plus", {}, function (e) {
                var numberOfItem = $("#number").val();
                if(isNaN(numberOfItem) == true ){
                    numberOfItem = 100;
                }
                numberOfItem = (parseInt(numberOfItem) + 1);
                if(parseInt(numberOfItem) >= 100){
                    numberOfItem = 100;
                }
                $("#number").val(numberOfItem);
            });
            
            $(document).on("click", "#add_to_cart", {}, function (e) {
                e.preventDefault();
                var qty = $("#number").val();
                if(isNaN(qty) == true || parseInt(qty)  < 1){
                    qty = 1;
                }
                $("#qty").val(qty);
                pages.sanpham.addToCart();
            });

            $(document).on("click", ".color-items", {}, function (e) {
                e.preventDefault();
                $(".color-items a").removeClass("active");
                var color_id = $(this).attr("data-id");
                if( isNaN(color_id) == false ){
                    $(this).children("a").addClass("active");
                    $("#selected_color").text($(this).text());
                    $("#color").val(color_id);
                    if($("#productMobilePhotos.mobile").is(':visible') == true){
                        pages.sanpham.processGalleryMobile(color_id);
                    }else{
                        $(".image_thumbs").removeClass("active");
                        $(".image_thumbs").hide();
                        $("a.color_"+color_id).show();
                        $("a.color_"+color_id).first().addClass("active");
                        $("a.color_"+color_id).first().trigger("click");
                    }
                }
            });
            // check is mobile
            if($("#productMobilePhotos.mobile").is(':visible') == true){
                var color_id = $(".color-items a.active").attr('data-id');
                if(isNaN(color_id)){
                    color_id = '1';
                }

                pages.sanpham.processGalleryMobile(color_id);
            }
        },
        /**
         * 
         */
        addToCart: function () {
            var token = $.cookie("token");
            if (token != "") {
                $("#purc_frm #t").val(token);
            }
            var options = {
                url: "/don-hang/them-vao-gio-hang",
                type: "POST",
                beforeSubmit: function(formData, jqForm, options) { 
                },
                success: function (data) {
                    if (data.Code > 0) {
                        $("#cart_item_count").text(data.Data.item_count);
                        $("#cart_item_count_mobile").text('('+data.Data.item_count+')');
                        var title = data.Data.product_title;
                        if(data.Data.color_name != ""){
                            title = title +" Màu: " + data.Data.color_name;
                        }
                        $("#cartModal #product_name").text(title);
                        $("#cartModal").modal("show");
                        
                        if (parseInt(data.Data.item_count) > 0) {
                            if ($("#cart_item_count").hasClass("active") == false) {
                                $("#cart_item_count").addClass("active");
                            }
                        } else {
                            $("#cart_item_count").removeClass("active");
                        }
                    } else {
                        alert( data.Message );
                    }
                },
                error: function () {
                }
            };
            $('#purc_frm').ajaxSubmit(options);
        },
        /**
         * [processGalleryMobile description]
         * @param  {[type]} color_id [description]
         * @return {[type]}          [description]
         */
        processGalleryMobile : function(color_id){
            $(".image_thumbs").hide();
            $(".bx-pager .bx-pager-item a.bx-pager-link").hide(); 
            $(".bx-pager .bx-pager-item a.bx-pager-link").removeClass("active");
            
            $.each($("a.color_"+color_id), function (key, value){
                var index = $(value).attr("data-index");
                $("a.bx-pager-link[data-slide-index='"+index+"']").show();
            });
            $("a.bx-pager-link:visible").first().addClass("active");
            $("a.bx-pager-link:visible").first().trigger("click");
            var first_show = $("a.bx-pager-link:visible").first().attr("data-slide-index");
            $(".image_thumbs[data-index='"+first_show+"']").show();
        }
    }
});