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
                $("#filter_frm #status").val($("#sort_status").val());
                $("#filter_frm").submit();
            });

            $(document).on('change', '#item_limit, #sort_type, #sort_status', {}, function (e) {
                e.preventDefault();
                var itemLimitValue = $("#item_limit").val();
                var sortTypeValue = $("#sort_type").val();
                var sortStatusValue = $("#sort_status").val();
                $("#filter_frm #page_size").val(itemLimitValue);
                $("#filter_frm #sorted").val(sortTypeValue);
                $("#filter_frm #status").val(sortStatusValue);
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
                var maxValue = parseInt($("#number").attr("max"), 10);
                if(isNaN(numberOfItem) == true ){
                    numberOfItem = maxValue;
                }
                numberOfItem = (parseInt(numberOfItem) + 1);
                if(parseInt(numberOfItem) > maxValue){
                    alert(`Sản phẩm này chỉ thêm được tối đa là ${maxValue}`);
                    numberOfItem = maxValue;
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

            $(document).ready(function() {
                if ($('li.variant-items a.active').length > 0) {
                    $('#productThumbPhotos a.image_thumbs:first').addClass('active');
                }
            });

            $(document).ready(function(){
                if ($('li[data-status="-1"]').length > 0) {
                    window.location.href = '/';
                }
                let liStatusMinus1 = $('li[data-status="-1"]').length > 0;
                let liIndex0 = $('li[data-index="0"]');
                let liIndex0NotStatus1 = liIndex0.not('[data-status="1"]').length > 0;
                let liIndex0Status1Price0 = $('li[data-index="0"][data-status="1"][data-price-sales="0"]').length > 0;
                let liIndex0Status1NotVariant1 = $('li[data-index="0"][data-status="1"][data-status-variant!="1"]').length > 0;
                let liStatus1BestSell1Variant1 = $('li[data-index="0"][data-status="1"][data-best-sell="1"][data-status-variant="1"]').length > 0;
                let liStatus1BestSell1VariantPrice0 = $('li[data-index="0"][data-status="1"][data-best-sell="1"][data-status-variant="1"][data-price-sales="0"]').length > 0;
                if (liStatusMinus1) {
                    window.location.href = '/';
                }else if (liStatus1BestSell1VariantPrice0) {
                    $(".purchase").css("display", "none");
                    $('.status-a').text('PRE-ORDER');
                    $('#add_to_cart span').text('HÀNG ĐẶT TRƯỚC');
                }else if (liIndex0NotStatus1 || liIndex0Status1NotVariant1) {
                    $(".purchase").css("display", "none");
                    $('.status-a').text('HẾT HÀNG');
                    liIndex0.find('a').addClass('disabled');
                } else if (liIndex0Status1Price0) {
                    $(".purchase").css("display", "none");
                    $('.status-a').text('CÒN HÀNG');
                    liIndex0.find('a').addClass('disabled');
                } else if (liStatus1BestSell1Variant1) {
                    $('.status-a').text('PRE-ORDER');
                    $('#add_to_cart span').text('HÀNG ĐẶT TRƯỚC');
                }
                var best_sell = $('.best-sell').val();
                if(best_sell == 1) {
                    $('.status-a').text('PRE-ORDER');
                    $('#add_to_cart span').text('HÀNG ĐẶT TRƯỚC');
                }
            });

            $(document).ready(function() {
                var $photos = $('.photos');
                var $pivBot = $('.piv-bot');
                var $anh = $('.image-left');
                var photosOffsetTop, pivBotOffsetTop, photosHeight, anhOffsetTop, anhHeight;

                function updateOffsets() {
                    photosOffsetTop = $photos.offset().top;
                    anhOffsetTop = $anh.offset().top;
                }

                function handleScroll() {
                    var scrollTop = $(window).scrollTop();
                    var photosHeight = $photos.outerHeight();
                    var pivBotOffsetTop = $pivBot.offset().top; // Cập nhật vị trí mới của pivBot
                    var maxScrollTop = pivBotOffsetTop - photosHeight - 20;

                    if (scrollTop > photosOffsetTop && scrollTop < maxScrollTop) {
                        let topPosition = scrollTop > 100 ? '37px' : '0'; 
                        $photos.css({
                            'position': 'fixed',
                            'top': topPosition,
                            'left': $anh.offset().left + 'px',
                            'width': $anh.width() + 'px'
                        });
                    } else if (scrollTop >= maxScrollTop) {
                        $photos.css({
                            'position': 'relative',
                            'top': Math.max(0, maxScrollTop - anhOffsetTop - 20) + 'px',
                            'left': '0'
                        });
                    } else {
                        $photos.css({
                            'position': 'relative',
                            'top': '0',
                            'left': '0',
                            'width': '100%'
                        });
                    }
                    // if (scrollTop > photosOffsetTop && scrollTop < maxScrollTop) {
                    //     let topPosition = scrollTop - photosOffsetTop + (scrollTop > 100 ? 37 : 0);
                    //     $photos.css({
                    //         'position': 'relative',
                    //         'top': topPosition + 'px',
                    //         'left': 0,
                    //         'width': $anh.width() + 'px',
                    //         'transition': 'top 0.03s ease',
                    //     });
                    // } else if (scrollTop >= maxScrollTop) {
                    //     $photos.css({
                    //         'position': 'relative',
                    //         'top': Math.max(0, maxScrollTop - anhOffsetTop - 10) + 'px',
                    //         'left': '0',
                    //         'transition': 'top 0.03s ease',
                    //     });
                    // } else {
                    //     $photos.css({
                    //         'position': 'relative',
                    //         'top': '0',
                    //         'left': '0',
                    //         'width': '100%',
                    //         'transition': 'top 0.03s ease',
                    //     });
                    // }
                }
                if ($(window).width() > 767) {
                    updateOffsets();
                    $(window).on('scroll', handleScroll);
                }
                // Phân loại sản phẩm
                var slidesToShow = 5;
                var slidesToScroll = 5;
                var currentSlide = 0; 
                var noVariant = $('.no-variant');
                var indexVariant = 0;
                firstVariant = $('.variant-wrapper').eq(indexVariant);
                noVariant.show().slick({
                    slidesToShow: slidesToShow,
                    slidesToScroll: slidesToScroll,
                    focusOnSelect: false,
                    infinite: false ,
                    prevArrow: '<button class="slick-prev"><i class="fa-solid fa-angle-left"></i></button>',
                    nextArrow: '<button class="slick-next"><i class="fa-solid fa-angle-right"></i></button>'
                });

                if (firstVariant.hasClass('slick-initialized')) {
                    firstVariant.slick('unslick');
                }
                    firstVariant.show().slick({
                    slidesToShow: slidesToShow,
                    slidesToScroll: slidesToScroll,
                    focusOnSelect: false,
                    infinite: false ,
                    prevArrow: '<button class="slick-prev"><i class="fa-solid fa-angle-left"></i></button>',
                    nextArrow: '<button class="slick-next"><i class="fa-solid fa-angle-right"></i></button>'
                }); 
                if (noVariant.find('.slick-slide').length > 0) {
                    firstVariant = noVariant;
                    updateLargeSlider(0);
                    let image = $('.no-variant').find('.image_thumbs').eq(0);
                    let img_url = image.data('image')
                    if (image.length > 0) {
                        image.addClass("active");
                        $('.image_detail .photo').fadeOut(300, function() {
                            $(this).attr('data-lazy', img_url)
                                   .attr('data-zoom-image', img_url)
                                   .css('background-image', `url(${img_url})`).fadeIn(300);
                        });
                    }
                } else {
                    //  console.log("NoVariant không có phần tử con nào có class slick-slide.");
                } 
                var totalSlides = firstVariant.slick('getSlick').slideCount;
                var lastSlide =  totalSlides -1;
                function updateLargeSlider(index) {
                    var $currentSlide = firstVariant.find('.slick-slide[data-slick-index="' + index + '"]').not('.slick-cloned');
                    var img = $currentSlide.data('image');
                    $('.zoomWindow').fadeOut(300, function() {
                        $(this).css('background-image', `url(${img})`).fadeIn(300);
                    });
                    
                    $('.zoomLens img').fadeOut(300, function() {
                        $(this).attr('src', img).fadeIn(300);
                    });
                    
                    $('.zoomWrapper .photo').fadeOut(300, function() {
                        $(this).attr('data-lazy', img)
                               .attr('data-zoom-image', img)
                               .css('background-image', `url(${img})`).fadeIn(300);
                    });
                    
                    $('.image_detail .photo').fadeOut(300, function() {
                        $(this).attr('data-lazy', img)
                                .attr('data-zoom-image', img)
                                .css('background-image', `url(${img})`).fadeIn(300);
                    });
                    firstVariant.find('.slick-slide').removeClass('active');
                    $currentSlide.addClass('active');
                    if (!$currentSlide.hasClass('slick-active')) {
                        var firstVisibleSlideIndex = firstVariant.find('.slick-active').first().data('slick-index');
                        var lastVisibleSlideIndex = firstVariant.find('.slick-active').last().data('slick-index');
                        if(index == 0) {
                            firstVariant.show().slick('slickGoTo', 0);
                        } 
                        else if(index == lastSlide){
                            firstVariant.show().slick('slickGoTo', lastSlide); 
                        } 
                        else if (index < firstVisibleSlideIndex) {
                            firstVariant.slick('slickPrev');
                        } else if (index > lastVisibleSlideIndex) {
                            firstVariant.slick('slickNext'); 
                        } 
                    }
                }
                firstVariant.on('click', '.slick-slide', function() {
                    var index = $(this).data('slick-index'); 
                    currentSlide = index; 
                    updateLargeSlider(index); 
                });
                $(document).on("click", ".image_thumbs", function (e) {
                    var index = $(this).data('slick-index');
                    updateLargeSlider(index); 
                });
                var isTransitioning = false;
                $('#click-left').on('click', function (event) {
                    event.stopPropagation();
                    if (isTransitioning) return; 
                    isTransitioning = true; 

                    if (currentSlide > 0) {
                        currentSlide--;
                    } else {
                        currentSlide = firstVariant.find('.slick-slide').length - 1;
                    }
                    updateLargeSlider(currentSlide);
                    setTimeout(() => { isTransitioning = false; }, 500);
                });

                $('#click-right').on('click', function (event) {
                    event.stopPropagation();
                    if (isTransitioning) return;
                    isTransitioning = true;

                    if (currentSlide < firstVariant.find('.slick-slide').length - 1) {
                        currentSlide++;
                    } else {
                        currentSlide = 0;
                    }
                    updateLargeSlider(currentSlide);
                    setTimeout(() => { isTransitioning = false; }, 500);
                });

                updateLargeSlider(0);
                
                $(document).on("click", ".variant-items", {}, function (e) {
                    e.preventDefault();
                    var timeStart = $(this).attr('data-start-time');
                    var currentTime = Math.round(Date.now() / 1000); 
                    var saleStartTime = new Date(timeStart).getTime() / 1000; 
                    $(".variant-items").removeClass("active");
                    $(".variant-items a").removeClass("active");
                    var variant_id = $(this).attr("data-id");
                    var dataLazyValue = $('.zoomWrapper .photo').attr('data-lazy');
                    var no_image = $('.variant-wrapper.variant-'+variant_id);
                    var variant0Id = $("input[data-var0-id]").data("var0-id");
                    var status = $(this).attr("data-status");
                    var status_variant = $(this).attr("data-status-variant");
                    var best_sell = $(this).attr("data-best-sell");
                    var vcId = $('.combo-product').data('vc-id');
                    if (isNaN(variant_id) == false) {
                        $(this).addClass("active");
                        $(this).children("a").addClass("active");
                        $("#selected_variant").text($(this).text());
                        $("#variant").val(variant_id);
                        var variant_price = $(this).attr("data-price");
                        var index = $(this).attr("data-index");
                        var variant_price_sales = $(this).attr("data-price-sales");
                        var variant_name = $(this).find("a").text();
                        if(saleStartTime - currentTime <= 6 * 3600){
                            var variant_price_flash_sales = $(this).attr("data-price-sales");
                            var first_char = variant_price_flash_sales.substring(0, 1);
                            $("#selected_price_sales").text(first_char+'?????' + '₫');
                        }else{
                            $("#selected_price_sales").text(formatNumber(variant_price_sales) + '₫');
                        }
                        $("#selected_price").text(formatNumber(variant_price) + '₫');
                        $("#selected_variant").text(variant_name);
                        $("#variant_price_sales").val(variant_price_sales);
                        $("#variant_price").val(variant_price);
                        $("#variant_name").val(variant_name);
                        if( index == 0 && status != 1){
                            $(".purchase").css("display", "none");
                            $('.status-a').text('HẾT HÀNG')
                        }else if(status_variant == 2){
                            $(".purchase").css("display", "none");
                            $('.status-a').text('HẾT HÀNG')
                        }else if(index != 0 && variant_price == 0){
                            $(".purchase").css("display", "none");
                            $('.status-a').text('HẾT HÀNG')
                        }else if(index == 0 && status == 1 && variant_price == 0){
                            $(".purchase").css("display", "none");
                            $('.status-a').text('CÒN HÀNG');
                            $('#selected_price_sales').text('Liên hệ');
                        }else{
                            $(".purchase").css("display", "block");
                            $('.status-a').text('CÒN HÀNG')
                        }
                        if(best_sell == 1 && status_variant == 1){
                            $('.status-a').text('PRE-ORDER');
                            $('#add_to_cart span').text('HÀNG ĐẶT TRƯỚC');
                        }
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
                            pages.sanpham.processGalleryMobileSmall(variant_id);
                        } 
                        else {
    
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
                        // if ($(window).width() > 767) {
                        //     updateOffsets();
                        //     handleScroll();
                        // }
                        $('.variant-wrapper').each(function(){
                            if ($(this).hasClass('slick-initialized')) {
                                $(this).slick('unslick');
                            }
                            $(this).hide();
                        });
                    
                        var selectedVariant = $('.variant-' + variant_id);
                        selectedVariant.show().slick({
                            focusOnSelect: false,
                            infinite: false,
                            slidesToShow: slidesToShow,
                            slidesToScroll: slidesToScroll,
                            prevArrow: '<button class="slick-prev"><i class="fa-solid fa-angle-left"></i></button>',
                            nextArrow: '<button class="slick-next"><i class="fa-solid fa-angle-right"></i></button>'
                        });
                        firstVariant = selectedVariant;
                        updateLargeSlider(0);
                    }
                    if ($('.no-variant a').length > 0) {
                        firstVariant = noVariant;
                        updateLargeSlider(0);
                        let image = $('.no-variant').find('.image_thumbs').eq(0);
                        let img_url = image.data('image')
                        if (image.length > 0) {
                            image.addClass("active");
                            $('.image_detail .photo').fadeOut(300, function() {
                                $(this).attr('data-lazy', img_url)
                                       .attr('data-zoom-image', img_url)
                                       .css('background-image', `url(${img_url})`).fadeIn(300);
                            });
                        } else {
                            console.log('Không tìm thấy phần tử hình ảnh.');
                        }
                        if ($(window).width() > 767) {
                            updateOffsets();
                            handleScroll();
                        }
                    } else {
                        console.log('Không tồn tại thẻ <a> bên trong .no-variant');
                    }
                    if(no_image.length > 0) {
                        $('#click-right, #click-left').css({
                            'display': 'block'
                        });                      
                    } else {
                        $('.image_detail .photo').fadeOut(300, function() {
                            $(this).attr('data-lazy', dataLazyValue)
                                    .attr('data-zoom-image', dataLazyValue)
                                    .css('background-image', `url(${dataLazyValue})`).fadeIn(300);
                        });
                        $('#click-right, #click-left').css({
                            'display': 'none'
                        }); 
                    }
                });
            });
            $('.variant-items').first().click();
            var dataId = $('.variant-items').first().attr('data-id');
            var elementimage = $('.variant-wrapper.variant-'+dataId);
            if(elementimage.length <=0) {
                $('#click-right, #click-left').css({
                    'display': 'none'
                }); 
            }
            
            $(document).ready(function() {
                $('.variant-items[data-price-sales="0"] a').click(function(event) {
                    event.preventDefault();
                });
                $('.variant-items[data-status-variant="2"] a').click(function(event) {
                    event.preventDefault();
                });
                $('.variant-items[data-price-sales="0"] a').addClass('disabled');
                $('.variant-items[data-status-variant="2"] a').addClass('disabled');
            });
            $(document).ready(function() {
                var timeStart = $('.count_time_start').attr('attr-start-time');
                var saleStartTime = new Date(timeStart).getTime() / 1000; 
                function checkCountdown() {
                    var currentTime = Math.round(Date.now() / 1000); 
                    // if (saleStartTime - currentTime <= 6 * 3600) { 
                    //     $('.count_time_start').countdown(new Date(saleStartTime * 1000), function(event) {
                    //         $(this).text(
                    //           event.strftime('%H giờ %M phút %S giây')
                    //         );
                    //         if (event.elapsed){
                    //           $('.product-info').hide();
                    //         }  
                    //     });
                    //     clearInterval(interval); 
                    // }
                    if (saleStartTime - currentTime <= 6 * 3600) {
                        $('.count_time_start').countdown(new Date(saleStartTime * 1000), function(event) {
                            $(this).html(
                                '<span class="hour">' + event.strftime('%H') + '</span>' + ' '+
                                '<span class="minute">' + event.strftime('%M') + '</span>' + ' ' +
                                '<span class="second">' + event.strftime('%S') + '</span>'
                            );
                            if (event.elapsed) {
                                $('.product-info').hide();
                            }
                        });
                        clearInterval(interval);
                    }
                }

                var interval = setInterval(checkCountdown, 1000);            
            });

            function formatNumber(number) {
                return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","); 
            }
            //
            // check is mobile
            if($("#productMobilePhotos.mobile").is(':visible') == true){
                var variant_id = $(".variant-items a.active").attr('data-id');
                if(isNaN(variant_id)){
                    variant_id = $(this).attr("data-id");
                }
                pages.sanpham.processGalleryMobile(variant_id);
                pages.sanpham.processGalleryMobileSmall(variant_id);
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
            
            $(document).ready(function () {
                var btnShowMore = $('#show-combo');
                var btnShowLess = $('#hide-combo');
                var hiddenCombos = $('.combo-product>ul:gt(2)');
                hiddenCombos.hide();


                btnShowMore.on('click', function () {
                    hiddenCombos.slideDown();
                    btnShowMore.hide();
                    btnShowLess.show();
                });
                btnShowLess.on('click', function () {
                    hiddenCombos.slideUp();
                    btnShowLess.hide();
                    btnShowMore.show();
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
                    // merged_images = variant_image.concat(color_image);
                    merged_images = [...new Set(variant_image.concat(color_image))];
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
                    dots: false,
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    infinite: false,
                    arrows: false,
                    asNavFor: '#productMobilePhotosSmall',
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
                    dots: false,
                    slidesToShow: 1,
                    slidesToScroll: 1,
                    infinite: false,
                    arrows: false,
                    asNavFor: '#productMobilePhotosSmall',
                    appendDots: $('.custom-dots'),
                });
            }
        },
        
        processGalleryMobileSmall: function(variant_id) {
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
                if ($('#productMobilePhotosSmall').hasClass('slick-initialized')) {
                    $('#productMobilePhotosSmall').slick('unslick');
                }
                $("#productMobilePhotosSmall").empty();
                merged_images.forEach(function(image, index) {
                    var $imageThumb = $('<a>', {
                      'class': 'image_thumbs_small variant_' + variant_id,
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
                    $imageThumb.appendTo("#productMobilePhotosSmall");
                });
                let slideCount = $('#productMobilePhotosSmall .image_thumbs_small').length;
                $('#productMobilePhotosSmall').slick({
                    dots: false,
                    slidesToShow: 5,
                    infinite: false,
                    arrows: false,
                    focusOnSelect: false,
                    asNavFor: slideCount > 5 ? '#productMobilePhotos' : null, 
                });
                $('#productMobilePhotosSmall .slick-slide').on('click', function() {
                    let index = $(this).data('slick-index');
                    $('#productMobilePhotos').slick('slickGoTo', index);
                });
                if (slideCount <= 5) {
                    $('#custom-prev').hide();
                    $('#custom-next').hide();
                } else {
                    $('#custom-prev').show();
                    $('#custom-next').show();
                }
            }else{
                if ($('#productMobilePhotosSmall').hasClass('slick-initialized')) {
                    $('#productMobilePhotosSmall').slick('unslick');
                }
                var listImage2 = $('.variants-image[data-id="'+variant_id+'"]').val();
                if(typeof listImage2 !== 'undefined') {
                    var variant_image2 = JSON.parse(listImage2);
                }
                $("#productMobilePhotosSmall").empty();
                $("a.variant_" + variant_id).trigger("click");
                variant_image2.forEach(function(image, index) {
                    var $imageThumb = $('<a>', {
                      'class': 'image_thumbs_small variant_' + variant_id,
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
                      'z-index': index + 1 
                    });
                  
                    $imageDiv.appendTo($imageThumb);
                    $imageThumb.appendTo("#productMobilePhotosSmall");
                });
                let slideCount = $('#productMobilePhotosSmall .image_thumbs_small').length;
                $('#productMobilePhotosSmall').slick({
                    dots: false,
                    slidesToShow: 5,
                    infinite: false,
                    arrows: false,
                    focusOnSelect: false,
                    asNavFor: slideCount > 5 ? '#productMobilePhotos' : null, 
                });
                $('#productMobilePhotosSmall .slick-slide').on('click', function() {
                    let index = $(this).data('slick-index');
                    $('#productMobilePhotos').slick('slickGoTo', index);
                });
                if (slideCount <= 5) {
                    $('#custom-prev').hide();
                    $('#custom-next').hide();
                } else {
                    $('#custom-prev').show();
                    $('#custom-next').show();
                }
            }
            // $('#custom-prev').on('click', function() {
            //     $('#productMobilePhotos').slick('slickPrev');  
            //     $('#productMobilePhotosSmall').slick('slickPrev');  
            // });
            
            // $('#custom-next').on('click', function() {
            //     $('#productMobilePhotos').slick('slickNext');
            //     $('#productMobilePhotosSmall').slick('slickNext');
            // }); 
            // Số slide hiển thị cùng lúc
            var slidesToShow = 4;
            
            $('#custom-prev').on('click', function() {
                var currentIndex = $('#productMobilePhotos').slick('slickCurrentSlide');
                var newIndex = Math.max(currentIndex - slidesToShow, 0);
                $('#productMobilePhotos').slick('slickGoTo', newIndex);
                $('#productMobilePhotosSmall').slick('slickGoTo', newIndex);
            });
            
            $('#custom-next').on('click', function() {
                var currentIndex = $('#productMobilePhotos').slick('slickCurrentSlide');
                var totalSlides = $('#productMobilePhotos').slick('getSlick').slideCount;
                var newIndex = Math.min(currentIndex + slidesToShow, totalSlides - 1);
                $('#productMobilePhotos').slick('slickGoTo', newIndex);
                $('#productMobilePhotosSmall').slick('slickGoTo', newIndex);
            });


        } 
    }
});