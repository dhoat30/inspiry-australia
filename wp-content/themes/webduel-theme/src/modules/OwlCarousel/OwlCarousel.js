let $ = jQuery;
import 'owl.carousel/dist/assets/owl.carousel.css';
import 'owl.carousel';

class OwlCarousel {
    constructor(args, className) {
        this.events(args, className);

    }
    events(args, className) {
        $(className).owlCarousel(args);
    }
}
export default OwlCarousel;