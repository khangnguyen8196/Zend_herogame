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

            // Phân loại sản phẩm
            $(document).on("click", ".variant-items", {}, function (e) {
                e.preventDefault();
                $(".variant-items a").removeClass("active");
                var variant_id = $(this).attr("data-id");
                var variant0Id = $("input[data-var0-id]").data("var0-id");
                var vcId = $('.combo-product').data('vc-id');
                console.log(variant_id);
                if (isNaN(variant_id) == false) {
                    $(this).children("a").addClass("active");
                    $("#selected_variant").text($(this).text());
                    $("#variant").val(variant_id);
            
                    // Lấy giá tiền tương ứng với variant-item được chọn
                    var variant_price = $(this).attr("data-price");
                    var variant_price_sales = $(this).attr("data-price-sales");
                    var variant_name = $(this).find("a").text();
                    $("#selected_price_sales").text(formatNumber(variant_price_sales) + '₫');
                    $("#selected_price").text(formatNumber(variant_price) + '₫');
                    $("#selected_variant").text(variant_name);
                    $("#variant_price_sales").val(variant_price_sales);
                    $("#variant_price").val(variant_price);
                    $("#variant_name").val(variant_name);
                    if(variant_price!=variant_price_sales){
                        $('strong.price-b.hidden').css('opacity','1');
                    }else {   
                            $('.hidden').css('opacity','0');
                    }
                    if(variant_price==variant_price_sales){
                        $('strong.price-b').addClass('hidden');
                    }

                    if(variant_id !=vcId){
                        $('.combo-product').hide();
                    }else {
                        $('.combo-product').show();
                    }

            
                    if ($("#productMobilePhotos.mobile").is(':visible') == true) {
                        pages.sanpham.processGalleryMobile(variant_id);
                    } else {

                        $(".image_thumbs").removeClass("active");
                        $(".image_thumbs").hide();
                        if (variant0Id == variant_id) {
                            $("a.variant_" + variant_id).first().trigger("click");
                            $('.image_thumbs').hide();
                            $('.variant_' + variant_id).show();
                            $('.variant_0_id, .image_thumbs:not([class*=variant_])').show(); 
                        } else {
                            $('.variant_' + variant_id).show();
                            var variantImage = $('.variant_' + variant_id).find('.imgh.r1x1.photo').eq(0);
                            variantImage.trigger("click");
                        }
                        
                    }
                }
            });
            function formatNumber(number) {
                return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."); // định dạng số với dấu phẩy phân cách hàng nghìn
            }
            //
            // check is mobile
            if($("#productMobilePhotos.mobile").is(':visible') == true){
                var variant_id = $(".variant-items a.active").attr('data-id');
                if(isNaN(variant_id)){
                    variant_id = $(this).attr("data-id");
                }
                pages.sanpham.processGalleryMobile(variant_id);
            }


            $(document).on('click', '.cart_btn_combo', {}, function (e) {
                e.preventDefault();
                var token = $.cookie("token");
                var cid = $(this).attr("cid");
                var qty = $(this).attr("qty");
                $.ajax({
                  url: "/don-hang/them-vao-gio-hang-combo",
                  type: 'POST',
                  data: {t:token,cid:cid,qty:qty},
                  beforeSend: function () {
                  },
                  success: function (data) {
                    if (data.Code > 0) {
                      $("#cart_item_count").text(data.Data.item_count);
                      $("#cart_item_count_mobile").text('('+data.Data.item_count+')');
                      $("#cartModal #product_name").text(data.Data.combo_title);
                      $("#cartModal").modal("show");
                      
                      if (parseInt(data.Data.item_count) > 0) {
                        if ($("#cart_item_count").hasClass("active") == false) {
                          $("#cart_item_count").addClass("active");
                        }
                      } else {
                        $("#cart_item_count").removeClass("active");
                      }
                    } else {
                      alert(data.Message);
                    }
                  },
                  error: function () {
                  }
                });
            });

            $(document).ready(function() {
                $(".more-expand").click(function() {
                  var comboId = $(this).data('combo-id');
                  $(this).hide();
                  $(".more-collapse[data-combo-id='" + comboId + "']").show();
                  $(".info-detail[data-combo-id='" + comboId + "']").show();
                });
              
                $(".more-collapse").click(function() {
                  var comboId = $(this).data('combo-id');
                  $(this).hide();
                  $(".more-expand[data-combo-id='" + comboId + "']").show();
                  $(".info-detail[data-combo-id='" + comboId + "']").hide();
                });
              });     
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
                        if(data.Data.variant_name != ""){
                            title = title +" Phân Loại: " + data.Data.variant_name;
                        }else {
                            title = title +" Phân Loại: " + 'Mặc Định';
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
         * @param  {[type]} variant_id [description]
         * @return {[type]}          [description]
         */
        processGalleryMobile: function(variant_id) {
            var variant0Id = $("input[data-var0-id]").data("var0-id");
            if( (variant_id) ==(variant0Id)){
                var merged_images = [];
                var listColorImage = $('.color-image[data-id="1"]').val();
                if(typeof listColorImage !== 'undefined') {
                    var color_image = JSON.parse(listColorImage);
                }
                var listImage = $('.variant-image[data-id="'+variant_id+'"]').val();
                if(typeof listImage !== 'undefined'){
                    var variant_image = JSON.parse(listImage);
                }
                if (typeof variant_image !== 'undefined' && typeof color_image !== 'undefined') {
                    merged_images = variant_image.concat(color_image);
                } else if (typeof variant_image !== 'undefined') {
                    merged_images = variant_image;
                } else if ( typeof color_image !== 'undefined') {
                    merged_images = color_image;
                }
                if ($('#productMobilePhotos').hasClass('slick-initialized')) {
                    $('#productMobilePhotos').slick('unslick');
                }
                $("#productMobilePhotos").empty();
                merged_images.forEach(function(image, index) {
                    var $imageThumb = $('<a>', {
                      'class': 'image_thumbs variant_' + variant_id,
                      'data-image': '/upload/images/' + image,
                      'data-index': index,
                      'data-zoom-image': '/upload/images/' + image,
                    });
                    var $imageDiv = $('<div>', {
                      'class': 'imgh r6x4 photo',
                      'data-lazy': "/upload/images/" + image,
                      'style': 'display: block; background-image: url("/upload/images/' + image + '");'
                    });
                    $imageThumb.css({
                      'z-index': index + 1 // Sử dụng giá trị z-index tăng dần cho từng ảnh
                    });
                  
                    $imageDiv.appendTo($imageThumb);
                    $imageThumb.appendTo("#productMobilePhotos");
                });
                $('#productMobilePhotos').slick({
                    dots: true,
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    infinite: false,
                    arrows: false,
                    appendDots: $('.custom-dots'),
                });       
            }else{
                if ($('#productMobilePhotos').hasClass('slick-initialized')) {
                    $('#productMobilePhotos').slick('unslick');
                }
                var listImage2 = $('.variants-image[data-id="'+variant_id+'"]').val();
                if(typeof listImage2 !== 'undefined') {
                    var variant_image2 = JSON.parse(listImage2);
                }
                $("#productMobilePhotos").empty();
                $("a.variant_" + variant_id).trigger("click");
                console.log(variant_image2);
                variant_image2.forEach(function(image, index) {
                    var $imageThumb = $('<a>', {
                      'class': 'image_thumbs variant_' + variant_id,
                      'data-image': '/upload/images/' + image,
                      'data-index': index,
                      'data-zoom-image': '/upload/images/' + image,
                    });
                    var $imageDiv = $('<div>', {
                      'class': 'imgh r6x4 photo',
                      'data-lazy': "/upload/images/" + image,
                      'style': 'display: block; background-image: url("/upload/images/' + image + '");'
                    });
                    $imageThumb.css({
                      'z-index': index + 1 // Sử dụng giá trị z-index tăng dần cho từng ảnh
                    });
                  
                    $imageDiv.appendTo($imageThumb);
                    $imageThumb.appendTo("#productMobilePhotos");
                });
                $('#productMobilePhotos').slick({
                    dots: true,
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    infinite: false,
                    arrows: false,
                    appendDots: $('.custom-dots'),
                });
            }
        }       
     }
});