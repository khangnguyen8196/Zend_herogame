$(function() {
	// Adjustments for Safari on Mac
	if (navigator.userAgent.indexOf('Safari') != -1 && navigator.userAgent.indexOf('Mac') != -1 && navigator.userAgent.indexOf('AppleWebkit') == -1) {
        $('html').addClass('safari-mac'); // provide a class for the safari-mac specific css to filter with
    }

	loadAllPages();
	loadHomePage();
	loadProductPage();
});

function loadAllPages() {
	// Lazy load image, optimize performace
    $('[data-lazy]').lazyload({
        event: 'scroll show slide',
        effect: 'fadeIn',
        threshold : 200,
		failure_limit: 9999,
		skip_invisible : false,
        data_attribute: 'lazy'
    });

	// Top header scroll
	var setPageHeaderPos = function() {
		var windowT = $(window).scrollTop();
		var headerH = $('.pheader').height();
		if (windowT > headerH) {
			$('.pheader').css({ top: '-100%', opacity: 0 });
		}
		else if (windowT < headerH) {
			$('.pheader').css({ top: -windowT + 'px', opacity: 1 });
		}
		else {
			$('.pheader').css({ top: 0, opacity: 1 });
		}
	};
	setPageHeaderPos();
	$(window).scroll(function() {
		// clearTimeout($.data(this, 'scrollTimer'));
	    // $.data(this, 'scrollTimer', setTimeout(function() {
	    //     setPageHeaderPos();
	    // }, 250));
		setPageHeaderPos();
	});

	// Banner video / Youtube
	$('.banner-video').fancybox({
		openEffect: 'none',
		closeEffect: 'none',
		helpers : {
			media : {},
			overlay: {
				locked: false
			}
		}
	});

	// Banner rotator
	var setRotatorItv = function(banRot, banLst) {
		var banAtv = '',
			banIdx = 0,
			banNdx = 0,
			banSpd = parseInt(banRot.data('speed')),
			banDur = parseInt(banRot.data('duration'));
		return setInterval(function() {
			banAtv = banRot.find('.active');
			banIdx = banLst.index(banAtv);
			// Infinite rotation
			if (banIdx == banLst.length - 1) {
				banIdx = 0;
			}
			else {
				banIdx++;
			}
			banLst.hide().removeClass('active');
			banLst.eq(banIdx).fadeIn(banSpd, function() {
				$(this).addClass('active');
			})
			.css('display', 'block');
		}, banDur);
	};
	$('.banner-rotator').each(function() {
		var banRot = $(this);
		var banLst = banRot.find('.banner-photo, .banner-video');
		var banHov = parseBool(banRot.data('hover'));
		if (banLst.length > 1) {
			var banItv = setRotatorItv(banRot, banLst);
			if (banHov) {
				banRot.hover(function() {
					banItv = clearInterval(banItv);
				},
				function() {
					banItv = setRotatorItv(banRot, banLst);
				});
			}
		}
	});

	// Banner slider
	$('.banner-slider').each(function() {
		var banSld = $(this);
		var banRnn = banSld.find('.banner-runner');
		var banPgr = banSld.find('.banner-pager');
		var banHov = parseBool(banSld.data('hover'));
		var banIdx = parseInt(banSld.data('index'));
		var banSpd = parseInt(banSld.data('speed'));
		var banDur = parseInt(banSld.data('duration'));
		banSld.waitForImages(function() {
			banRnn.bxSlider({
				auto: true,
				pause: banDur,
				speed: banSpd,
				controls: false,
				captions: false,
				autoHover: banHov,
				startSlide: banIdx,
				pagerCustom: banPgr,
				easing: 'ease-in-out',
				onSliderLoad: function(slider) {
					// Fix cloned slides lazy image
					banRnn.find('.bx-clone .imgh').each(function() {
						var clonedImgh = $(this);
						clonedImgh.css("background-image", "url('" + clonedImgh.attr('data-lazy') + "')");
					});
				},
		        onSlideAfter: function() {
					// Load slide image on slide event
		            banRnn.trigger('slide');
		        }
			});
		});
	});

	// Quickview slider
	var setQvSlider = function(slider) {
		if (slider.length == 0 || slider.hasClass('loaded')) {
			return;
		}
		// Get slides config
		var dSlides = parseInt(slider.data("dcol"));
		var mSlides = parseInt(slider.data("mcol"));
		// Setup slider
		slider.bxSlider({
			pager: false,
			slideMargin: 20,
			slideWidth: 9999,
			moveSlides: mSlides,
			minSlides: isMobilePhone() ? mSlides : dSlides,
			maxSlides: isMobilePhone() ? mSlides : dSlides,
			onSliderLoad: function(slider) {
				// Mark as loaded
				slider.viewport.find('.slider').addClass('loaded');
				// Fix cloned slides lazy image
				slider.viewport.find('.bx-clone .imgh').each(function() {
					var clonedImgh = $(this);
					clonedImgh.css("background-image", "url('" + clonedImgh.attr('data-lazy') + "')");
				});
			},
	        onSlideAfter: function() {
				// Load slide image on slide event
	            slider.trigger('slide');
	        }
		});
	};
	setQvSlider($('.quick-view .tab-pane.active .slider'));
	$('.quick-view a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
		var qvTab = $($(e.target).attr('href'));
		var qvSld = qvTab.find('.slider');
		if (qvSld.length > 0) {
			setQvSlider(qvSld);
		}
		else {
			qvTab.trigger('slide');
		}
	});

	// Range slider
	$('.range-input').each(function() {
		var inputRange = $(this);
		inputRange.slider({
			tooltip: 'always',
			tooltip_split: true,
			tooltip_position: 'bottom',
			value: [
				parseInt(inputRange.data("slider-min")),
				parseInt(inputRange.data("slider-max"))
			],
			formatter: function(value) {
				return isNaN(value)
					? value
					: formatNumber(value) + inputRange.data("formatter-unit");
			}
		});
	});
}

function loadHomePage() {
}

function loadProductPage() {
	if (!$(document.body).hasClass('product')) {
		return;
	}

	// Photos zoom + gallery
	$('#productMainPhoto').elevateZoom({
        tint: true,
		easing: true,
		borderSize: 2,
		zoomWindowWidth: 630,
		zoomWindowHeight: 630,
		borderColour: '#aaa',
        tintColour: '#19dd89',
		imageCrossfade: true,
		gallery: 'productThumbPhotos',
		galleryActiveClass: 'active'
    });
	$('#productGalleryButton').click(function(e) {
		var ez = $('#productMainPhoto').data('elevateZoom');
		$.fancybox.open(ez.getGalleryList(), {
			padding: 0,
			type: 'image',
			helpers:  {
				thumbs : {
					width: 72,
					height: 72
				},
                overlay: {
                    locked: false
                }
			}
		});
		e.preventDefault();
	});

	// Toggle detail description
	var btnToggle = $('.product-item-view .detail .btn-toggle'),
		cntToggle = $('.product-item-view .detail').find('.cnt, .fa, .txt');
	btnToggle.click(function(e) {
		cntToggle.toggleClass('active');
		e.preventDefault();
	});
}

function parseBool(b) {
    return !(/^(false|0)$/i).test(b) && !!b;
}

/**
 * @param integer a: number to format
 * @param integer n: length of decimal
 * @param integer x: length of whole part
 * @param mixed   s: sections delimiter
 * @param mixed   c: decimal delimiter
 */
function formatNumber(a, n, x, s, c) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
        num = a.toFixed(Math.max(0, ~~n));

    return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
};

function isMobilePhone() {
  var check = false;
  (function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))check = true})(navigator.userAgent||navigator.vendor||window.opera);
  return check;
}
