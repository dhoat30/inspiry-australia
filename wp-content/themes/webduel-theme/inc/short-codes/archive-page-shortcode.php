<?php 
// add free shipping if it exist for the given product 
function addFreeShippingTag(){ 
    global $product;
    if($product->get_shipping_class()==="free-shipping"){ 
      return '<p class="free-shipping">FREE SHIPPING</p>'; 
    }
}

add_shortcode('add_free_shipping_tag', 'addFreeShippingTag'); 


// add deal text if it exist for the given product on product loop page
function addDealText(){ 
    global $product;
    $dealText = $product->get_attribute( 'pa_current-deal' );
    if(!empty($dealText)){ 
      return '<p class="loop-deal">'.$dealText.'</p>'; 
    }
}

add_shortcode('add_deal_text', 'addDealText'); 