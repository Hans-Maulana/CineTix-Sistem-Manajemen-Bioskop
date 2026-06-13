$(function () {

    // Header Scroll Disabled
    /*
    $(window).scroll(function () {
        if ($(window).scrollTop() >= 60) {
            $("header").addClass("fixed-header");
        } else {
            $("header").removeClass("fixed-header");
        }
    });
    */


    // Featured Owl Carousel (only if present on page)
    if ($('.featured-projects-slider .owl-carousel').length) {
    $('.featured-projects-slider .owl-carousel').owlCarousel({
        center: true,
        loop: true,
        margin: 30,
        nav: true,
        navText: [
            "<button class='btn btn-primary rounded-circle p-2 hstack justify-content-center shadow-sm'><iconify-icon icon='lucide:chevron-left' class='fs-6'></iconify-icon></button>",
            "<button class='btn btn-primary rounded-circle p-2 hstack justify-content-center shadow-sm'><iconify-icon icon='lucide:chevron-right' class='fs-6'></iconify-icon></button>"
        ],
        dots: false,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: false,
        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 2
            },
            1000: {
                items: 3
            },
            1200: {
                items: 4
            }
        }
    })
    }

    // Coming Soon Owl Carousel (only if present on page)
    if ($('.coming-soon-slider .owl-carousel').length) {
    $('.coming-soon-slider .owl-carousel').owlCarousel({
        center: false,
        loop: false,
        margin: 30,
        nav: true,
        navText: [
            "<button class='btn btn-primary rounded-circle p-2 hstack justify-content-center shadow-sm'><iconify-icon icon='lucide:chevron-left' class='fs-6'></iconify-icon></button>",
            "<button class='btn btn-primary rounded-circle p-2 hstack justify-content-center shadow-sm'><iconify-icon icon='lucide:chevron-right' class='fs-6'></iconify-icon></button>"
        ],
        dots: false,
        autoplay: false,
        responsive: {
            0: {
                items: 1
            },
            600: {
                items: 2
            },
            1000: {
                items: 3
            },
            1200: {
                items: 4
            }
        }
    })
    }
    $('.count').each(function () {
		$(this).prop('Counter', 0).animate({
			Counter: $(this).text()
		}, {
			duration: 1000,
			easing: 'swing',
			step: function (now) {
				$(this).text(Math.ceil(now));
			}
		});
	});


    // ScrollToTop
    function scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    const btn = document.getElementById("scrollToTopBtn");
    if (btn) {
        btn.addEventListener("click", scrollToTop);
    }

    window.onscroll = function () {
        const btn = document.getElementById("scrollToTopBtn");
        if (btn) {
            if (document.documentElement.scrollTop > 100 || document.body.scrollTop > 100) {
                btn.style.display = "flex";
            } else {
                btn.style.display = "none";
            }
        }
    };


    // Aos
	AOS.init({
		once: true,
	});

});

