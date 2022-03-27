const $ = jQuery
import 'owl.carousel/dist/assets/owl.carousel.css';
import 'owl.carousel';

class WooGallery {
    constructor() {
        this.events()
    }
    events() {
        // owl  carousel for single product page
        this.slideShow()

    }
    slideShow() {
        var x = window.matchMedia("(max-width: 800px)")
        if (x.matches) {
            $('.woocommerce-product-gallery__wrapper').addClass('owl-carousel')

            $('.owl-carousel').owlCarousel({
                loop: true,
                margin: 10,
                nav: true,
                responsive: {
                    0: {
                        items: 1
                    },
                    600: {
                        items: 1
                    },
                    1000: {
                        items: 1
                    }
                }
            })
        }
    }

}
export default WooGallery