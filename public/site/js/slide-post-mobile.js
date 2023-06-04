$(document).ready(function() {
	var $sliderPC = $('.pc .post-menu-list');
	
	$sliderPC.on('beforeChange', function(event, slick, currentSlide, nextSlide) {
	  // Xóa CSS của chấm tròn active trước đó
	  var $dots = $(slick.$dots[0]).find('.slick-active');
	  $dots.removeClass('active');
	});
  
	$sliderPC.on('afterChange', function(event, slick, currentSlide) {
	  // Áp dụng CSS cho chấm tròn active hiện tại
	  var $dots = $(slick.$dots[0]).find('.slick-active');
	  $dots.addClass('active');
	});
  
	$sliderPC.slick({
	  dots: true,
	  infinite: true,
	  speed: 300,
	  touchMove: true,
	  slidesToShow: 5,
	  slidesToScroll: 5,
	  arrows: false,
  })
});


$(document).ready(function() {
	var $slider = $('.mobile .post-menu-content .post-menu-list');
	
	$slider.on('beforeChange', function(event, slick, currentSlide, nextSlide) {
	  // Xóa CSS của chấm tròn active trước đó
	  var $dots = $(slick.$dots[0]).find('.slick-active');
	  $dots.removeClass('active');
	});
  
	$slider.on('afterChange', function(event, slick, currentSlide) {
	  // Áp dụng CSS cho chấm tròn active hiện tại
	  var $dots = $(slick.$dots[0]).find('.slick-active');
	  $dots.addClass('active');
	});
  
	$slider.slick({
	  dots: true,
	  infinite: false,
	  speed: 300,
	  touchMove: true,
	  slidesToShow: 4,
	  slidesToScroll: 4,
	  arrows: false,
	  responsive: [
		{
		  breakpoint: 767,
		  settings: {
			touchMove: true,
			slidesToShow: 2,
			slidesToScroll: 2,
			infinite: true,
			dots: true
		  }
		},
		{
		  breakpoint: 480,
		  settings: {
			touchMove: true,
			slidesToShow: 2,
			slidesToScroll: 2,
			infinite: true,
			dots: true
		  }
		}
	  ]
	});
});

$(document).ready(function() {
	var slidesShow = 6;
	// $('.banner-yt-pc').on('init reInit beforeChange', function(event, slick, currentSlide, nextSlide) {
	// 	var $clonedSlides = $(slick.$slider).find('.slick-cloned .banner-video');
		
	// 	$clonedSlides.each(function(index) {
	// 	  var $originalSlide = $(slick.$slides).eq((index - slidesShow));
	// 	  var $originalImage = $originalSlide.find('.imgh.r0x0');
	// 	  var dataLazyUrl = $originalImage.data('lazy');
	// 	  $(this).find('.imgh.r0x0').css('background-image', 'url("' + dataLazyUrl + '")');
	// 	});
	// });
	$('.banner-yt-pc').slick({
		dots: false,
		arrows: true,
		slidesToShow: slidesShow,
		slidesToScroll: 2,
		infinite: true,
		swipeToSlide: true,
		appendDots: $('.custom-dots-pc'),
		prevArrow: '<button class="slick-prev slick-arrow" aria-label="Previous" type="button"></button>',
  		nextArrow: '<button class="slick-next slick-arrow" aria-label="Next" type="button"></button>'

	}); 
});

$(document).ready(function() {
	var slidesShow = 2;
	// $('.banner-yt').on('init reInit beforeChange', function(event, slick, currentSlide, nextSlide) {
	// 	var $clonedSlides = $(slick.$slider).find('.slick-cloned .banner-video');
		
	// 	$clonedSlides.each(function(index) {
	// 	  var $originalSlide = $(slick.$slides).eq((index - slidesShow));
	// 	  var $originalImage = $originalSlide.find('.imgh.r0x0');
	// 	  var dataLazyUrl = $originalImage.data('lazy');
	// 	  $(this).find('.imgh.r0x0').css('background-image', 'url("' + dataLazyUrl + '")');
	// 	});
	// });
  	$('.banner-yt').slick({
    dots: false,
    touchMove: true,
    slidesToShow: slidesShow,
    slidesToScroll: 2,
    infinite: true,
    arrows: true,
	swipe: true,
	speed:300,
	easing: 'easeInOutCubic',
    appendDots: $('.custom-dots'),
	prevArrow: '<button class="slick-prev slick-arrow" aria-label="Previous" type="button"></button>',
  	nextArrow: '<button class="slick-next slick-arrow" aria-label="Next" type="button"></button>',
  });
});










  


