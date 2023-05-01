(function ($) {
    "use strict";
    var NooCarousel = function( $scope, $ ) {
        $scope.find('.owl-carousel').each(function () {
            var slide = $(this).attr('data-slide');
            slide = JSON.parse(slide);
            $(this).owlCarousel({
                margin: typeof slide.margin !== "undefined" ? parseInt(slide.margin) : 0,
                loop: typeof slide.loop !== "undefined" ? slide.loop : false,
                center:typeof slide.center !== "undefined" ? slide.center : false,
                autoplay: typeof slide.autoplay !== "undefined" ? slide.autoplay : false,
                autoHeight : typeof slide.auto_height !== "undefined" ? slide.auto_height : false,
                autoplaySpeed:typeof slide.speed !== "undefined" ? slide.speed : false,
                nav: typeof slide.show_nav !== "undefined" ? slide.show_nav : false,
                dots: typeof slide.dot !== "undefined" ? slide.dot : false,
                navText: typeof slide.prev !== "undefined" && typeof slide.next !== "undefined" ? [slide.prev,slide.next] : ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
                responsive:{
                    0: {
                        items: 1
                    },
                    500: {
                        items: typeof slide.mobilecol !== "undefined" ? parseInt(slide.mobilecol) : 2,
                    },
                    991: {
                        items: typeof slide.tabletcol !== "undefined" ? parseInt(slide.tabletcol) : 3,
                    },
                    1300: {
                        items: typeof slide.items !== "undefined" ? parseInt(slide.items) : 4,
                    }
                }
            });
        });
    };
     var NooSwiper = function($scope ,$){
         $scope.find('.noo-swiper').each(function () {
            var slide = $(this).attr('data-slide');
            slide = JSON.parse(slide);
            var mySwiper = new Swiper($(this),{
                speed: typeof slide.speed !== "undefined" ? slide.speed : false,
                slidesPerView:typeof slide.items !== "undefined" ? parseInt(slide.items) : 2,
                slidesPerColumn: slide.rows !== "undefined" ? slide.rows : 2,
                autoplay : slide.autoplay !== "undefined" ? slide.autoplay : true,
                pagination: slide.show_nav !== "undefined" ? slide.show_nav : true,
                preloadImages: false,
                lazy: true,
                navigation: {
                    nextEl: '.swiper-next',
                    prevEl: '.swiper-prev',
                },
                breakpoints:{
                    0: {
                        slidesPerView: 1,
                    },
                    500: {
                        slidesPerView: typeof slide.mobilecol !== "undefined" ? parseInt(slide.mobilecol) : 2,
                    },
                    991: {
                        slidesPerView: typeof slide.tabletcol !== "undefined" ? parseInt(slide.tabletcol) : 3,
                    },
                    1300: {
                        slidesPerView: typeof slide.items !== "undefined" ? parseInt(slide.items) : 4,
                    }
                }
            });
             if (mySwiper) {
                 mySwiper.update();
                 $('.vc_tta-tab').click(function () {
                     mySwiper.update();
                 });
             }
         });
     };
    var ImagesSlider = function($scope,$){
        $scope.find('.noo-job-search-wrapper .sliders').each(function(){
            var slide = $(this).attr('data-slide');
            slide = JSON.parse(slide);
            var _this = $(this);
            imagesLoaded(_this,function(){
                _this.owlCarousel({
                    items:1,
                    loop:true,
                    autoplay:true,
                    autoplayTimeout:typeof slide.time !== "undefined" ? slide.time : 5000,
                    autoplayHoverPause:true,
                    autoplaySpeed:typeof slide.speed !== "undefined" ? slide.speed : false,
                });
            });
        });
    };
    var NooClient = function($scope,$){
      $scope.find('.noo-client-elementor-widget').each(function () {
        var slide = $(this).find('.owl-carousel').attr('data-slide');
        slide = JSON.parse(slide);
        $(this).find('.owl-carousel').owlCarousel({
              items: typeof slide.items !== "undefined" ? parseInt(slide.items) : 4,
              itemsDesktop : false,
              itemsDesktopSmall : false,
              itemsTablet: false,
              itemsTabletSmall : false,
              itemsMobile : false,
              dots: typeof slide.dot !== "undefined" ? slide.dot : false,
              nav: typeof slide.show_nav !== "undefined" ? slide.show_nav : false,
              autoPlay : typeof slide.autoplay !== "undefined" ? slide.autoplay : false,
              slideSpeed : typeof slide.speed !== "undefined" ? slide.speed : false,
        });
      });
    };
    var NooFAQ = function($scope,$){
      $scope.find('.noo-faq-elementor-widget').each(function () {
         $(this).find('.hide_faq .noo_faq_content').slideUp(0);
          $(this).find('.noo_faq_item').click(function() {
              $(this).find('.noo_faq_content').slideToggle(500);
              $(this).toggleClass('open_faq');
          });
      });
    };
    $( window ).on( 'elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction( 'frontend/element_ready/noo_job.default', NooCarousel );
        elementorFrontend.hooks.addAction( 'frontend/element_ready/noo_resume.default', NooSwiper );
        elementorFrontend.hooks.addAction( 'frontend/element_ready/noo_company.default', NooSwiper );
        elementorFrontend.hooks.addAction( 'frontend/element_ready/noo_company.default', NooCarousel );
        elementorFrontend.hooks.addAction( 'frontend/element_ready/noo_testimonial.default', NooCarousel );
        elementorFrontend.hooks.addAction( 'frontend/element_ready/noo_package.default', NooCarousel );
        elementorFrontend.hooks.addAction( 'frontend/element_ready/job_category.default', NooCarousel );
        elementorFrontend.hooks.addAction( 'frontend/element_ready/job_location.default', NooCarousel );
        elementorFrontend.hooks.addAction( 'frontend/element_ready/noo-recent-news.default', NooCarousel );
        elementorFrontend.hooks.addAction( 'frontend/element_ready/noo_company_feature.default', NooCarousel );
        elementorFrontend.hooks.addAction( 'frontend/element_ready/noo_advanced_search.default', ImagesSlider );
        elementorFrontend.hooks.addAction( 'frontend/element_ready/noo-client.default', NooCarousel );
        elementorFrontend.hooks.addAction( 'frontend/element_ready/noo-faq.default', NooFAQ );
    } );
})(jQuery);