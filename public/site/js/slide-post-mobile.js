$(document).ready(function() {
	var $sliderPC = $('.pc .post-menu-list');
	
	// $sliderPC.on('beforeChange', function(event, slick, currentSlide, nextSlide) {
	//   // Xóa CSS của chấm tròn active trước đó
	//   var $dots = $(slick.$dots[0]).find('.slick-active');
	//   $dots.removeClass('active');
	// });
  
	// $sliderPC.on('afterChange', function(event, slick, currentSlide) {
	//   // Áp dụng CSS cho chấm tròn active hiện tại
	//   var $dots = $(slick.$dots[0]).find('.slick-active');
	//   $dots.addClass('active');
	// });
  
	$sliderPC.slick({
		dots: true,
		// autoplay: true,
		// autoplaySpeed:3000,
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
	
	// $slider.on('beforeChange', function(event, slick, currentSlide, nextSlide) {
	//   var $dots = $(slick.$dots[0]).find('.slick-active');
	//   $dots.removeClass('active');
	// });
  
	// $slider.on('afterChange', function(event, slick, currentSlide) {
	//   var $dots = $(slick.$dots[0]).find('.slick-active');
	//   $dots.addClass('active');
	// });
  
	$slider.slick({
	  dots: true,
	  infinite: false,
	  speed: 300,
	  touchMove: true,
	//   autoplay: true,
	//   autoplaySpeed:5000,
	  slidesToShow: 4,
	  slidesToScroll: 4,
	  arrows: false,
	  responsive: [
		{
			breakpoint: 931,
			settings: {
			  touchMove: true,
			  slidesToShow: 4,
			  slidesToScroll: 4,
			  infinite: true,
			  dots: true
			}
		  },
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
	$('.banner-yt-pc').slick({
		dots: false,
		arrows: true,
		slidesToShow: slidesShow,
		slidesToScroll: 2,
		infinite: true,
		// autoplay: true,
		// autoplaySpeed:5000,
// 		swipe:true,
		swipeToSlide: true,
		appendDots: $('.custom-dots-pc'),
		prevArrow: '<button class="slick-prev slick-arrow" aria-label="Previous" type="button"></button>',
  		nextArrow: '<button class="slick-next slick-arrow" aria-label="Next" type="button"></button>'

	}); 
});

$(document).ready(function() {
	var slidesShow = 2;
  	$('.banner-yt').slick({
    dots: false,
    touchMove: true,
    slidesToShow: slidesShow,
    slidesToScroll: 2,
    infinite: true,
    arrows: true,
    swipe:true,
	speed:300,
	// autoplay: true,
	// autoplaySpeed:5000,
	easing: 'easeInOutQuad',
    appendDots: $('.custom-dots'),
	prevArrow: '<button class="slick-prev slick-arrow" aria-label="Previous" type="button"></button>',
  	nextArrow: '<button class="slick-next slick-arrow" aria-label="Next" type="button"></button>',
	responsive: [
	{
		breakpoint: 933,
		settings: {
			touchMove: true,
			slidesToShow: 4,
			slidesToScroll: 4,
			infinite: true,
			dots: true
		}
		},
	{
		breakpoint: 767,
		settings: {
		touchMove: true,
		slidesToShow: 4,
		slidesToScroll: 4,
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
	$('.banner-runner-mobile').slick({
		centerMode: true,
		centerPadding: '60px',
		autoplay: true,
		autoplaySpeed:5000,
		slidesToShow: 3,
		responsive: [
			{
			breakpoint: 768,
			settings: {
				arrows: false,
				centerMode: true,
				centerPadding: '20px',
				slidesToShow: 3
			}
			},
			{
			breakpoint: 480,
			settings: {
				arrows: false,
				centerMode: true,
				centerPadding: '20px',
				slidesToShow: 1
			}
			}
		]
	});
	
	var slickSlider = $('.banner-runner-pc').slick({
		centerMode: true,
		centerPadding: '100px',
		autoplay: true,
		pauseOnHover: false,
		autoplaySpeed:6000,
		slidesToShow: 1,
		arrows: true,
		responsive: [
			{
			breakpoint: 768,
			settings: {
				arrows: true,
				centerMode: true,
				centerPadding: '20px',
				slidesToShow: 1
			}
			},

		]
	});
	
	$('.category-banner-runner').slick({
		autoplay: true,
		pauseOnHover: false,
		autoplaySpeed:6000,
		slidesToShow: 1,
		arrows: true,
	});

});
$(document).ready(function(){
    var $postLQ = $('.post-container');
    var slideCount = $postLQ.children().length;
    var slidesToShow = 4;

    $postLQ.slick({
        dots: true,
        infinite: slideCount > slidesToShow,
        speed: 300,
        touchMove: true,
        slidesToShow: slidesToShow,
        slidesToScroll: slidesToShow,
        arrows: false,
		responsive: [
			{
				breakpoint: 933,
				settings: {
					touchMove: true,
					slidesToShow: slidesToShow,
					slidesToScroll: slidesToShow,
					infinite: true,
					dots: true
				}
				},
			{
				breakpoint: 767,
				settings: {
				touchMove: true,
				slidesToShow: 2,
				slidesToScroll: 2,
				infinite: slideCount > 2,
				dots: true
				}
			},
			{
				breakpoint: 480,
				settings: {
				touchMove: true,
				slidesToShow: 2,
				slidesToScroll: 2,
				infinite: slideCount > 2,
				dots: true
				}
			}
		]
    });
	if ($(window).width() < 768) {
        if (slideCount < 2) {
            $postLQ.addClass('slick-center-left');
        }
    } else {
		if(slideCount < slidesToShow){
			$postLQ.addClass('slick-center-left');
		}
	}
});











  


