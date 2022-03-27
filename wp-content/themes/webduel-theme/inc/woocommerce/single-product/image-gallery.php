<?php 
// remove sale badge
add_filter('woocommerce_sale_flash', 'webduel_hide_sale_flash');
function webduel_hide_sale_flash(){
return false;
}
// vertical gallery 

// remove default product images
// remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20); 

// change breadcrumb location
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0);
add_action('woocommerce_before_single_product_summary', function(){ 
      echo '<div class="product-images">';
      echo woocommerce_breadcrumb();
  }, 5);
// add images 
add_action('woocommerce_before_single_product_summary', function(){ 
      echo '</div>';
}, 20);