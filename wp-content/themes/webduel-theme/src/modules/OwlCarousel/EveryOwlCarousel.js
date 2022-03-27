import OwlCarousel from './OwlCarousel';
import 'owl.carousel/dist/assets/owl.carousel.css';
import 'owl.carousel';
let $ = jQuery;
class EveryOwlCarousel {
    constructor() {
        this.events();
    }
    events() {
        //trending section carousel 
        this.trendingCarousel();

        // this.brandLogoHomePageCarousel();
        // product gallery on single product page
        this.productGallery();

        // // banner carousel 
        this.banner();

        // recently viewed carousel 
        this.recentlyViewedCarousel()

        // home page category cards 
        this.homeCategoryCards()
        // be inspired home page
        this.beInspiredHome()
    }

    // banner carousel 
    banner() {
        // // owl carousel 

        let className = '.banner-container .owl-carousel';
        let args = {
            lazyLoad: true,
            autoplay: true,
            autoplayTimeout: 5000,
            autoplayHoverPause: true,
            responsiveBaseElement: ".row-container",
            responsiveClass: true,
            rewind: true,
            dots: false,
            loop: true,
            responsive: {
                0: {
                    items: 1,
                    dots: false
                }

            }
        }
        const banner = new OwlCarousel(args, className);
    }

    productGallery() {
        // // owl carousel 
        // $('.single-product .flex-control-thumbs').addClass('splide');
        let className = '.woocommerce-product-gallery .owl-carousel';
        let args = {
            margin: 20,
            autoplay: true,
            autoplayTimeout: 2000,
            autoplayHoverPause: true,
            responsiveBaseElement: ".row-container",
            responsiveClass: true,
            rewind: true,
            responsive: {
                0: {
                    items: 4,

                    dots: true
                },
                600: {
                    items: 4,
                    dots: true
                }

            }
        }
        // const trendingNow = new OwlCarousel(args, className);
    };


    brandLogoHomePageCarousel() {

        // owl carousel 
        let className = '.brand-logo-section .owl-carousel';
        let args = {
            loop: true,
            navText: "G",
            margin: 20,
            lazyLoad: true,
            autoplay: true,
            autoplayTimeout: 2000,
            autoplayHoverPause: true,
            responsiveBaseElement: ".row-container",
            responsiveClass: true,
            rewind: true,
            responsive: {
                0: {
                    items: 1,
                    dots: true
                },
                600: {
                    items: 2,

                    dots: true
                },
                900: {
                    items: 3,

                    dots: true
                },
                1200: {
                    items: 3,
                    dots: true
                },
                1500: {
                    items: 4,
                    dots: true
                }
            }
        }
        const trendingNow = new OwlCarousel(args, className);
    }
    trendingCarousel() {

        // owl carousel 
        let className = '.trending-section .owl-carousel';
        let args = {
            loop: true,
            navText: ['<i class="fa-thin fa-arrow-left-long"></i>', '<i class="fa-thin fa-arrow-right-long"></i>'],
            margin: 20,
            center: true,
            lazyLoad: true,
            responsiveBaseElement: ".row-container",
            responsiveClass: true,
            rewind: true,
            mouseDrag: true,
            touchDrag: true,
            nav: true,
            responsive: {
                0: {
                    items: 1,
                    dots: false
                },
                600: {
                    items: 2,
                    dots: false
                },
                700: {
                    items: 3,
                    dots: false
                },
                1440: {
                    items: 3,
                    dots: false
                }
            }
        }
        const trendingNow = new OwlCarousel(args, className);
    }

    recentlyViewedCarousel() {

        // owl carousel 
        let className = '.recently-viewed-section .owl-carousel';
        let args = {
            loop: true,
            navText: ['<i class="fa-thin fa-arrow-left-long"></i>', '<i class="fa-thin fa-arrow-right-long"></i>'],
            margin: 20,
            center: true,
            lazyLoad: true,
            responsiveBaseElement: ".row-container",
            responsiveClass: true,
            rewind: true,
            mouseDrag: true,
            touchDrag: true,
            nav: true,
            responsive: {
                0: {
                    navText: ['<i class="fa-thin fa-arrow-left-long"></i>', '<i class="fa-thin fa-arrow-right-long"></i>'],
                    items: 1,
                    dots: false
                },
                600: {
                    navText: ['<i class="fa-thin fa-arrow-left-long"></i>', '<i class="fa-thin fa-arrow-right-long"></i>'],
                    items: 2,
                    dots: false
                },
                900: {
                    items: 3,
                    dots: false
                },
                1440: {
                    items: 3,
                    dots: false
                }
            }
        }
        const recentlyViewed = new OwlCarousel(args, className);
    }

    // home page category cards
    homeCategoryCards() {

        // owl carousel 
        let className = '.home .category-cards-section .owl-carousel';
        let args = {
            mouseDrag: true,
            touchDrag: true,
            nav: true,
            lazyLoad: true,
            loop: true,
            autoplay: true,
            autoplayTimeout: 5000,
            autoplayHoverPause: true,
            navText: ['<i class="fa-thin fa-arrow-left-long"></i>', '<i class="fa-thin fa-arrow-right-long"></i>'],
            margin: 20,
            responsive: {
                0: {
                    items: 1,
                    dots: false
                },
                600: {
                    items: 2,
                    dots: false
                },
                900: {
                    items: 3,
                    dots: false
                },
                1350: {
                    loop: false,
                    autoplay: false,
                    items: 4,
                    dots: false
                }
            }
        }
        const homeCategoryCards = new OwlCarousel(args, className);
    }
    // home page be inspired

    beInspiredHome() {

        // owl carousel 
        let className = '.home .be-inspired-section .owl-carousel';
        let args = {
            mouseDrag: true,
            touchDrag: true,
            nav: true,
            lazyLoad: true,
            loop: true,
            autoplay: true,
            autoplayTimeout: 5000,
            autoplayHoverPause: true,
            navText: ['<i class="fa-thin fa-arrow-left-long"></i>', '<i class="fa-thin fa-arrow-right-long"></i>'],
            responsive: {
                0: {
                    items: 1,
                    dots: false
                },
                600: {
                    items: 2,
                    dots: false
                },
                900: {
                    loop: false,
                    autoplay: false,
                    items: 3,
                    dots: false
                }
            }
        }
        const homeCategoryCards = new OwlCarousel(args, className);
    }

}
export default EveryOwlCarousel;