$(function () {
    pages.common.init();
});

if (!pages) {
    var pages = {};
}
pages = $.extend(pages, {
    common: {
        ajaxCall: '',
        timeout: null,
        init: function () {
            var me = this;
            if( $('iframe').length > 0 ){
                $("iframe").width('100%');
            }
            if( $.cookie('banner') == undefined ){
                if(banner_qc != undefined && 
                        banner_qc.trang_thai!= undefined && 
                        banner_qc.trang_thai == '1' && banner_qc.link_anh !=''){
                    
                    setTimeout(function(){
                        var width = $(window).width();
                        var iw = width - 50;
                        if( width > 1080){
                            iw = width/1.5;
                        }
                        $.fancybox(
                        '<a id="single_image" href="'+banner_qc.link_click+'">\n\
                            <img  width="'+iw+'px" src="'+banner_qc.link_anh+'" alt=""/></a>',
                            {

                                'transitionIn'      : 'none',
                                'transitionOut'     : 'none',
                                'afterClose': function() {
                                    var date = new Date();
                                    var minutes = 30;
                                    if(banner_qc.thoi_gian_off != undefined){
                                        minutes = banner_qc.thoi_gian_off;
                                    }
                                    date.setTime(date.getTime() + (minutes * 60 * 1000));
                                    $.cookie("banner", "1", { expires: date });
                                }
                            },
                        );
                    }, 3000);
                    $(document).on('click','#single_image',function(){
                        var date = new Date();
                        var minutes = 30;
                        if(banner_qc.thoi_gian_off != undefined){
                            minutes = banner_qc.thoi_gian_off;
                        }
                        date.setTime(date.getTime() + (minutes * 60 * 1000));
                        $.cookie("banner", "1", { expires: date });
                    });
                }
                
            }
            $(document).on('keyup', '.searchV', {}, function (e) {
                if (e.keyCode == 13) {
                    $(this).closest('form').submit();
                  }
            });
            $(document).on('click', '#shopping-cart', {}, function (e) {
                e.preventDefault();
                window.location = "/don-hang/gio-hang";
            });
            
            // Cart popover
            $('#shopping-cart').hoverIntent({
                over: function () {
                    var token = $.cookie("token");
                    $.ajax({
                        url: "/don-hang/danh-sach-san-pham",
                        type: 'POST',
                        data: {t: token},
                        beforeSend: function () {
                            
                        },
                        success: function (data) {
                            $("#popupover_container").html(data.Data);
                            pages.common.refreshLazyImage("#shopping-cart");
                            $('#shopping-cart .popover').fadeToggle(200);
                        },
                        error: function () {
                        }
                    });

                },
                timeout: 100,
                interval: 300
            });
            /*
             * 
             */
            $(document).on('click', '#shopping-cart .remove_pr_popup', {}, function (e) {
                e.stopPropagation();
                e.preventDefault();
                var token = $.cookie("token");
                var id = $(this).attr("pid");
                $.ajax({
                    url: "/don-hang/xoa-san-pham",
                    type: 'POST',
                    data: {pid: id, t: token},
                    beforeSend: function () {
                        $(".remove_pr_popup").prop("disabled", true);
                    },
                    success: function (data) {
                        $(".remove_pr_popup").prop("disabled", false);
                        if (data.Code > 0) {
                            if(currController == "don-hang" ){
                                location.reload();
                            }else{
                                var cart_item_count = $("#cart_item_count").text();
                                var qty = $(".item_"+id +" div.qty").text();
                                var finalQty = parseInt(cart_item_count) - parseInt(qty);
                                $("#cart_item_count").text(finalQty);
                                $("#cart_item_count_mobile").text('('+finalQty+')');
                                if(finalQty <= 0 ){
                                    $("#cart_item_count").removeClass("active");
                                }
                                $(".item_"+id).hide();
                            }
                        }
                    },
                    error: function () {
                        $(".remove_pr_popup").prop("disabled", false);
                    }
                });
            });
            
            $(document).on('click', '.cart_btn', {}, function (e) {
                e.preventDefault();
                var token = $.cookie("token");
                var pid = $(this).attr("pid");
                var qty = $(this).attr("qty");
                $.ajax({
                    url: "/don-hang/them-vao-gio-hang",
                    type: 'POST',
                    data: {pid: pid, qty: qty, t: token},
                    beforeSend: function () {
                    },
                    success: function (data) {
                        if (data.Code > 0) {
                            $("#cart_item_count").text(data.Data.item_count);
                            $("#cart_item_count_mobile").text('('+data.Data.item_count+')');
                            $("#cartModal #product_name").text(data.Data.product_title);
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
                });
            });
            
            $(document).on('click', '#userInfoBtn', {}, function (e) {
                e.preventDefault();
                $.ajax({
                    url: "/tai-khoan/thong-tin-tai-khoan",
                    type: 'POST',
                    data: {},
                    beforeSend: function () {
                         $(".loader").attr('data-text','Vui lòng đợi trong giây lát').addClass('is-active');
                    },
                    success: function (data) {
                        $(".loader").removeClass('is-active');
                        if (data.Code > 0) {
                            $("#userInfoModal #userInfoContent").html(data.Data);
                            
                            //load user day of birth
                            var y = $("#uif_byear").val();
                            var m = $("#uif_bmonth").val();
                            if (y.length > 0 && m.length > 0) {
                                var slt_day = $("#uif_bday").attr("slt_dy");
                                pages.taikhoan.getNumberOfDay(y, m, slt_day);
                            }
                            
                            $("#userInfoModal").modal("show");
                        } else {
                            alert(data.Message);
                        }
                    },
                    error: function () {
                        $(".loader").removeClass('is-active');
                    }
                });
            });
            pages.common.createElementCountDown();
          
            
        },
        /**
         * 
         */
        search: function (keyword) {
            if (pages.common.ajaxCall != "") {
                pages.common.ajaxCall.abort();
            }
            //check keyword
            if (keyword.length > 0) {
                keyword = $.trim(keyword);
                pages.common.ajaxCall = $.ajax({
                    url: "/tim-kiem/index",
                    type: "GET",
                    data: {
                        k: keyword
                    },
                    beforeSend: function () {
                        $(".searchResult").removeClass("active");
                        $(".searchResult").html('');
                    },
                    success: function (data) {
                        if (data.Code > 0 && (data.Data).length > 0) {
                            $(".searchResult").addClass("active");
                            $(".searchResult").html(data.Data);
                            pages.common.refreshLazyImage("#searchResult");
                        }
                    },
                    error: function () {

                    }
                });
            } else {
                $(".searchResult").removeClass("active");
                $(".searchResult").html('');
            }
        },
        /**
         * 
         * @returns {undefined}
         */
        refreshLazyImage: function (container) {
            if (typeof container != "undefined") {
                $(container+' [data-lazy]').lazyload({
                    event: 'scroll show slide',
                    effect: 'fadeIn',
                    threshold: 200,
                    failure_limit: 9999,
                    skip_invisible: false,
                    data_attribute: 'lazy'
                });
            } else {
                $('[data-lazy]').lazyload({
                    event: 'scroll show slide',
                    effect: 'fadeIn',
                    threshold: 200,
                    failure_limit: 9999,
                    skip_invisible: false,
                    data_attribute: 'lazy'
                });
            }
        },
        
        createElementCountDown: function(){
        	$.each( $('.product-item'), function( index, value ) { 
       				var pid = $(value).attr('attr-id');
       				var endtime = $(value).attr('attr-count-down');
       				var enable = $(value).attr('attr-enable-promo');
       				if( enable == '1' && endtime != '' ){
       				    endtime = endtime.replace(/-/g,'/');
       					var defaultStyle = 'color:#ffffff;background:#cc2600;font-size: 1.2em;';
       					var style = $(value).find('.label-b').attr('style');
       					if( style != ''&& style != undefined ){
       						defaultStyle = style+';'+'font-size: 1.2em;';
       					}
       					var display = '';
       					var text = $(value).find('.label-b').text();
       					if( text != '' && text != undefined ){
       						display = '<div class="rotator"><span>'+text+'</span></div>';
       					}
   						$(value).find('.label-text').hide();
       					if(  $('.product_'+ pid + ' .count_down_container').length == 0 ){
       						$('<div class="count_down_container label-b" style="'+defaultStyle+'">'+display+'<span class="days"></span><span class="hours"></span>:<span class="minutes"></span>:<span class="seconds"></span></div>').insertAfter(".product_" + pid + " .label-a");
       						var deadline = new Date( endtime );
       						pages.common.initializeClock('product_'+pid, deadline);
       					}
       				}
            });
            if( $('.product-info').length > 0 ){
                var endtime = $('.product-info').attr('attr-count-down');
       			var enable = $('.product-info').attr('attr-enable-promo');
       			if( enable == '1' && endtime != '' ){
       			    endtime = endtime.replace(/-/g,'/');
   			    	if(  $('.product-info .count_down_container').length == 0 ){
   						var html = '<label class="flash-sale">Flash sales:</label><strong class="count_down_container"><span class="days"></span><span class="hours"></span>:<span class="minutes"></span>:<span class="seconds"></span></strong>';
   						$('.product-info').append(html);
   						var deadline = new Date( endtime );
   						pages.common.initClockDetailProduct( deadline);
   					}
       			}
            }
        },
        
        getTimeRemaining: function(endtime) {
        	  var t = Date.parse(endtime) - Date.parse(new Date());
        	  var seconds = Math.floor((t / 1000) % 60);
        	  if( isNaN(seconds) == true){
        	      seconds = 0;
        	  }
        	  var minutes = Math.floor((t / 1000 / 60) % 60);
        	  if( isNaN(minutes) == true){
        	      minutes = 0;
        	  }
        	  var hours = Math.floor((t / (1000 * 60 * 60)) % 24);
        	  if( isNaN(hours) == true){
        	      hours = 0;
        	  }
        	  var days = Math.floor(t / (1000 * 60 * 60 * 24));
        	  if( isNaN(days) == true){
        	      days = 0;
        	  }
        	  return {
        	    'total': t,
        	    'days': days,
        	    'hours': hours,
        	    'minutes': minutes,
        	    'seconds': seconds
        	  };
        },

    	initializeClock: function(id, endtime) {

    	  function updateClock() {
    	    var t = pages.common.getTimeRemaining(endtime);
    	    $('.' + id + ' .days').text(t.days + ' Ngày ');
    	    $('.' + id + ' .hours').text(('0' + t.hours).slice(-2));
    	    $('.' + id + ' .minutes').text(('0' + t.minutes).slice(-2));
    	    $('.' + id + ' .seconds').text(('0' + t.seconds).slice(-2));

    	    if (t.total <= 0) {
    	      clearInterval(timeinterval);
    	      $('.'+ id + ' .count_down_container').remove();
    	      $('.'+ id + ' .label-b').show();
    	    }
    	  }

    	  updateClock();
    	  var timeinterval = setInterval(updateClock, 1000);
    	},
    	initClockDetailProduct: function(endtime){
    	    function updateClockDetail() {
    	        var t = pages.common.getTimeRemaining(endtime);
        	    $('.product-info .days').text(t.days + ' Ngày ');
        	    $('.product-info .hours').text(('0' + t.hours).slice(-2));
        	    $('.product-info .minutes').text(('0' + t.minutes).slice(-2));
        	    $('.product-info .seconds').text(('0' + t.seconds).slice(-2));
    
        	    if (t.total <= 0) {
        	      clearInterval(timeinterval);
        	      $('.product-info .count_down_container').remove();
        	    }
    	    }
    	    updateClockDetail();
    	    var timeinterval = setInterval(updateClockDetail, 1000);
    	}
        



    }
});
