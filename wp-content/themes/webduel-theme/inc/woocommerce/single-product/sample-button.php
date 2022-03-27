<?php
//add sample functonality 
add_action( 'woocommerce_single_product_summary', 'bbloomer_add_free_sample_add_cart', 35 );
  
function bbloomer_add_free_sample_add_cart() {
  global $product; 
    global $post;
    $terms = wp_get_post_terms( $post->ID, 'product_cat' );
    foreach ( $terms as $term ) $categories[] = $term->slug;
    $brandName = $product->get_attribute( 'pa_brands' );
    if ( in_array( 'fabric', $categories ) || in_array( 'wallpaper', $categories ) && (str_contains($brandName, 'Khroma') || str_contains($brandName, 'Arte'))) {
   ?>
      <form class="cart" method="post" enctype='multipart/form-data'>
      <?php 
        if (strstr($_SERVER['SERVER_NAME'], 'localhost')) {
      ?>
      
      <button type="submit" name="add-to-cart" value="456280" class="button btn-dk-green-border btn-full-width margin-top">ORDER FREE SAMPLE</button>
      <?php
        }

        else{
            ?>
              <button type="submit" name="add-to-cart" value="999471569" class="button btn-dk-green-border btn-full-width margin-top">ORDER FREE SAMPLE</button>

            <?php
        }
      ?>
      <input type="hidden" name="free_sample" value="<?php the_ID(); ?>">
      </form>
   <?php
    }
}
// -------------------------
// 2. Add the custom field to $cart_item
  
add_filter( 'woocommerce_add_cart_item_data', 'bbloomer_store_free_sample_id', 9999, 2 );
  
function bbloomer_store_free_sample_id( $cart_item, $product_id ) {
   if ( isset( $_POST['free_sample'] ) ) {
         $cart_item['free_sample'] = $_POST['free_sample'];
   }
   return $cart_item; 
}
  
// -------------------------
// 3. Concatenate "Free Sample" with product name (CART & CHECKOUT)
// Note: rename "Free Sample" to your free sample product name
  
add_filter( 'woocommerce_cart_item_name', 'bbloomer_alter_cart_item_name', 9999, 3 );
  
function bbloomer_alter_cart_item_name( $product_name, $cart_item, $cart_item_key ) {
   if ( $product_name == "Free Sample" ) {
      $product = wc_get_product( $cart_item["free_sample"] );
      $product_name .=  " (" . $product->get_name() . ")";
   }
   return $product_name;
}


// define the woocommerce_cart_item_thumbnail callback 
function filter_woocommerce_cart_item_thumbnail( $product_get_image, $cart_item, $cart_item_key ) { 
   // make filter magic happen here... 
   if($cart_item["free_sample"]){
     $product = wc_get_product( $cart_item["free_sample"] );
     $product_get_image = '<img';
     $product_get_image .= ' src="' .  wp_get_attachment_image_url( $product->image_id, 'woocommerce_thumbnail' ). '"';
     $product_get_image .= ' class="' . $class . '"';
     $product_get_image .= ' />';
     return $product_get_image; 
   }
   else{
     return $product_get_image; 
   }
  
 }; 
        
 // filter for sample images on a cart page
 add_filter( 'woocommerce_cart_item_thumbnail', 'filter_woocommerce_cart_item_thumbnail', 10, 3 ); 
   
 
 
 
 // -------------------------
 // 4. Add "Free Sample" product name to order meta
 // Note: this will show on thank you page, emails and orders
   
 add_action( 'woocommerce_add_order_item_meta', 'bbloomer_save_posted_field_into_order', 9999, 2 );
   
 function bbloomer_save_posted_field_into_order( $itemID, $values ) {
     if ( ! empty( $values['free_sample'] ) ) {
       $product = wc_get_product( $values['free_sample'] );
       $product_name = $product->get_name();
       wc_add_order_item_meta( $itemID, 'Free sample for', $product_name );
       wc_add_order_item_meta( $itemID, 'SKU', $product->get_sku() );
     }
 }
 