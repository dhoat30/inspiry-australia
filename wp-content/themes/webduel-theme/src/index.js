import "../css/style.scss"

// form 
import Form from './modules/Form/Form'
// owl carousel 
import EveryOwlCarousel from './modules/OwlCarousel/EveryOwlCarousel';
// warranty 
import Warranty from './modules/Warranty';
import WallpaperCalc from './modules/WallpaperCalc';
import DesignBoardSaveBtn from './modules/DesignBoardSaveBtn';
import Overlay from './modules/overlay';
import TopNav from './modules/TopNav';
import ShopFav from './modules/ShopFav';
import ToolTip from './modules/ToolTip';

//pop up cart
import PopUpCart from './modules/PopUpCart';

// Enquire Modal 
import EnquiryModal from './modules/EnquiryModal/EnquiryModal'

// cart modal 
import CartModal from './modules/CartModal/CartModal'

// auth
import Login from './modules/Auth/Login'


// search 
import Search from './modules/Search'
import MobileSearch from "./modules/MobileSearch";

// facet filter
import FacetFilter from './modules/FacetFilter/FacetFilter'

// customer service 
import CustomerServiceMenu from './modules/CustomerService/CustomerServiceMenu'
import ContactForm from './modules/CustomerService/ContactForm'
import FeedbackForm from './modules/CustomerService/FeedbackForm'

// woocommerce 
import WooGallery from './modules/Woocommerce/WooGallery'
import SingleProductAccordion from "./modules/Woocommerce/singleProductAccordion";
import ProductArchive from "./modules/Woocommerce/ProductArchive";
import SingleProduct from "./modules/Woocommerce/SingleProduct";
import Cart from './modules/Woocommerce/Cart/Cart'
import Coupon from './modules/Woocommerce/Cart/Coupon'
// import Windcave from "./modules/Woocommerce/Checkout/Windcave";
// modals 
import ErrorModal from "./modules/ErrorModal/ErrorModal";
import Checkout from "./modules/Woocommerce/Checkout/Checkout";

// header 
import Header from './modules/Header'

let $ = jQuery;


// add to cart and remove from cart class 
const popUpCart = new PopUpCart();

// woo Gallery 
const wooGallery = new WooGallery()
// single product page accordion 
const singleProductAccordion = new SingleProductAccordion()
// single product 
const singleProduct = new SingleProduct()

// every owl carousel
const everyOwlCarousel = new EveryOwlCarousel();

// product archive
const productArchive = new ProductArchive()

// cart 
const cart = new Cart()
const coupon = new Coupon()
// modals 
const errorModal = new ErrorModal()
// design board save button 
const designBoardSaveBtn = new DesignBoardSaveBtn();

// header 
const header = new Header();
window.onload = function () {
  // checkout 
  const checkout = new Checkout()

  // enquiry modal 
  const enquiryModal = new EnquiryModal();
  // cart modal 
  const cartModal = new CartModal();
  // form data processing 
  const form = new Form();

  const shopFav = new ShopFav();
  const topnav = new TopNav();
  const overlay = new Overlay();




  //Tool tip 
  const toolTip = new ToolTip();

  // login 
  const login = new Login()

  // search 
  const search = new Search()
  const mobileSearch = new MobileSearch()

  // facet filter 
  const facetFilter = new FacetFilter()

  // customer service 
  const customerServiceMenu = new CustomerServiceMenu()
  const contactForm = new ContactForm()
  const feedbackForm = new FeedbackForm()
  // const windcave = new Windcave()
  //price 
  let pricevalue = document.getElementsByClassName('bc-show-current-price');
  // console.log($('.bc-show-current-price').text);
  //slogan 

  $('.logo-container .slogan').css('opacity', '1');


}




//log in 
//const logIn = new LogIn();



const warranty = new Warranty();
const wallpaperCalc = new WallpaperCalc();


// typewriter effect
document.addEventListener('DOMContentLoaded', function (event) {
  // array with texts to type in typewriter
  // get json array from a title on a web page
  let jsonArray = $('.typewriter-query-container div').attr('data-title');

  if (jsonArray) {
    let dataText = JSON.parse(jsonArray);
    // type one text in the typwriter
    // keeps calling itself until the text is finished
    function typeWriter(text, i, fnCallback) {
      // chekc if text isn't finished yet
      if (i < (text.length)) {
        // add next character to h1
        document.querySelector(".typewriter-title").innerHTML = text.substring(0, i + 1) + '<span aria-hidden="true"></span>';

        // wait for a while and call this function again for next character
        setTimeout(function () {
          typeWriter(text, i + 1, fnCallback)
        }, 100);
      }
      // text finished, call callback if there is a callback function
      else if (typeof fnCallback == 'function') {
        // call callback after timeout
        setTimeout(fnCallback, 700);
      }
    }

    // start a typewriter animation for a text in the dataText array
    function StartTextAnimation(i) {
      if (typeof dataText[i] == 'undefined') {
        setTimeout(function () {
          StartTextAnimation(0);
        }, 1000);
      }
      if (dataText) {
        // check if dataText[i] exists
        if (i < dataText[i].length) {
          // text exists! start typewriter animation
          typeWriter(dataText[i], 0, function () {
            // after callback (and whole text has been animated), start next text
            StartTextAnimation(i + 1);
          });
        }
      }

    }
    // start the text animation
    StartTextAnimation(0);
  }

});

// scroll arrow 

let myID = document.getElementById("go-to-header");

var myScrollFunc = function () {
  var y = window.scrollY;
  if (y >= 1200) {
    myID.classList.add("show");
  } else if (y <= 1200) {
    myID.classList.remove("show");
  }
};

window.addEventListener("scroll", myScrollFunc);

// hide facet if no value 
(function ($) {
  document.addEventListener('facetwp-loaded', function () {
    $.each(FWP.settings.num_choices, function (key, val) {
      var $facet = $('.facetwp-facet-' + key);
      var $parent = $facet.closest('.facet-wrap');
      var $flyout = $facet.closest('.flyout-row');
      if ($parent.length || $flyout.length) {
        var $which = $parent.length ? $parent : $flyout;
        (0 === val) ? $which.hide() : $which.show();
      }
    });
  });
})(jQuery);



