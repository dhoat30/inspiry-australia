<?php
// send product impression data to google analytics 
add_action('woocommerce_after_shop_loop', 'webduel_product_impressions', 20); 
function webduel_product_impressions(){ 
   ?>
    <!-- send all impression data -->
         <script>
         dataLayer.push({
         'event': 'productImpressions',
         'ecommerce': {
            'currencyCode': 'NZD',                       // Local currency is optional.
            'impressions': [
               <?php 

                  if ( wc_get_loop_prop( 'total' ) ) {
                     $i = 0;
                     while ( have_posts() ) {
                        $i++;
                        the_post();
                        global $product; 
                        $term_list = get_the_terms( $product->get_id(), 'product_cat' );
                        $term = $term_list[0];
                        $variation_id = "No Variation";
                        $list = "Unknown List";
                        $page_title = get_the_title();

                        if( $product->is_type('variable') ) {
                           foreach($product->get_available_variations() as $variation_values ){

                                    foreach($variation_values['attributes'] as $key => $attribute_value ){
                                       $attribute_name = str_replace( 'attribute_', '', $key );
                                       $default_value = $product->get_variation_default_attribute($attribute_name);
                                       if( $default_value == $attribute_value ){
                                          $is_default_variation = true;
                                       } else {
                                          $is_default_variation = false;
                                             break; // Stop this loop to start next main lopp
                                             }
                                       }
                                       if( $is_default_variation ){
                                          $variation_id = $variation_values['variation_id'];
                                       break; // Stop the main loop
                                    }
                              }
                        } //end of variable product type condition
                     
                     
                  ?>
            {
               'name': '<?php echo $product -> get_name()?>',       // Name or ID is required.
               'id': '<?php echo $product -> get_id()?>',
               'price': '<?php echo $product -> get_price()?>',
               'brand': 'My Custom Brand',
               'category': '<?php echo $term -> name ?>',
               'variant': '<?php echo $variation_id ?>',
               'list': '<?php woocommerce_page_title(); ?>',
               'position': '<?php echo $i ?>'
            },

            <?php }
               }
               ?>

            ]
         }
         });

         </script>

         <!-- capture product click on product archive page -->
         <script type="text/javascript">
            var productObj = {};

            <?php

            if ( wc_get_loop_prop( 'total' ) ) {

                        $i = 0;
                        while ( have_posts() ) {
                           $i++;
                           the_post();
                           global $product; 

                           $term_list = get_the_terms( $product->get_id(), 'product_cat' );
                           $term = $term_list[0];
                           $variation_id = "No Variation";
                           $list = "Unknown List";
                           $page_title = get_the_title();

                           if( $product->is_type('variable') ) {

                                    foreach($product->get_available_variations() as $variation_values ){
                                       foreach($variation_values['attributes'] as $key => $attribute_value ){
                                          $attribute_name = str_replace( 'attribute_', '', $key );
                                          $default_value = $product->get_variation_default_attribute($attribute_name);
                                          if( $default_value == $attribute_value ){
                                             $is_default_variation = true;
                                          } else {
                                             $is_default_variation = false;
                                                break; // Stop this loop to start next main lopp
                                                }
                                          }
                                          if( $is_default_variation ){
                                             $variation_id = $variation_values['variation_id'];
                                          break; // Stop the main loop
                                       }
                                 }
                           } //end of variable product type condition

                           ?>

                           var thisProduct = {
                                 'name': '<?php echo $product -> get_name()?>',   
                                    'id': '<?php echo $product -> get_id()?>',
                                    'price': '<?php echo $product -> get_price()?>',
                                    'brand': 'My Custom Brand',
                                       'category': '<?php echo $term -> name ?>',
                                    'variant': '<?php echo $variation_id ?>',
                                    'list': '<?php woocommerce_page_title(); ?>',
                                    'position': '<?php echo $i ?>'
                           }

                           productObj['<?php echo get_permalink( $product->get_id()); ?>'] = thisProduct;
                           <?php

                     }
                  }



            ?>
               

               var products = document.getElementsByClassName("woocommerce-loop-product__link");

               for(var i = 0; i < products.length; i++) {

                  products[i].addEventListener("click", function(event) {

                        var clickedProductURL = event.currentTarget.href;
                     var clickedProduct = productObj[clickedProductURL];

               dataLayer.push({
                     'event': 'productClick',
                     'ecommerce': {
                        'click': {
                        'actionField': {'list': clickedProduct.list},      // Optional list property.
                        'products': [{
                           'name': clickedProduct.name,                      // Name or ID is required.
                           'id': clickedProduct.id,
                           'price': clickedProduct.price,
                           'brand': clickedProduct.brand,
                           'category': clickedProduct.category,
                           'variant': clickedProduct.variant,
                           'position': clickedProduct.position
                           }]
                        }
                        }
                  });

                  localStorage.setItem(clickedProduct.id, clickedProduct.list);

                  });

               }

            </script>
         <!--  -->
   <?php 
}
?>
