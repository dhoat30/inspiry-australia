<?php
// send product impression data to google analytics 
add_action('woocommerce_after_single_product', 'webduel_product_detail_analytics', 20); 
function webduel_product_detail_analytics(){ 

   global $product;
   $term_list = get_the_terms( $product->get_id(), 'product_cat' );
   $term = $term_list[0];
   $variation_id = "No Variation";

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
    <!-- send all impression data -->
   <script>

      <?php global $product; ?>
      dataLayer.push({
      'event': 'productDetails',
      'ecommerce': {
         'detail': {
         'actionField': {'list': localStorage.getItem('<?php echo $product -> get_id()?>')},          
         'products': [
            <?php 
                     $term_list = get_the_terms( $product->get_id(), 'product_cat' );
                     $term = $term_list[0];
                     $variation_id = "No Variation";

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
         }

         ]
      }
      }
      });

   </script>
   <script type="text/javascript">

      <?php

                  global $product; 
                  $term_list = get_the_terms( $product->get_id(), 'product_cat' );
                  $term = $term_list[0];
                  $variation_id = "No Variation";

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
                           'variant': '<?php echo $variation_id ?>'
      }

      var addToCartBtn = document.getElementsByClassName("single_add_to_cart_button")[0];

         addToCartBtn.addEventListener("click", function(event) {

            dataLayer.push({
                     'event': 'addToCart',
                     'actionField': {'list': localStorage.getItem('<?php echo $product -> get_id()?>')},  
                     'ecommerce': {
                        'currencyCode': 'NZD',
                        'add': {
                        'products': [{
                           'name': thisProduct.name,                  
                           'id': thisProduct.id,
                           'price': thisProduct.price,
                           'brand': thisProduct.brand,
                           'category': thisProduct.category,
                           'variant': thisProduct.variant,
                           'quantity': Number(document.getElementsByClassName("qty")[0].value)   
                           }]
                        }
                        }
                  });

                  });

   </script>


<!-- facebook add to cart button -->
<script type="text/javascript">

<?php 
	global $product; 
?>
	let productObject = {
		currency: "NZD", 
		value: '<?php echo $product->get_price()?>',
		content_ids: '<?php echo $product->get_id()?>', 
		content_name: '<?php echo $product->get_name()?>', 
		content_type: "Product", 

	}
	addToCartBtn.addEventListener('click', function(){
		fbq('track', 'AddToCart', productObject);
	})
</script>


<script>
	// single product page visit record 
	pintrk('track', 'pagevisit', {
	currency: 'NZD',
	line_items: [
	{
	product_name: '<?php echo $product->get_name() ?>',
	product_id: '<?php echo $product->get_id()?>',
	product_category: '<?php echo $term->name ?>',
	product_price: '<?php echo $product->get_price()?>',
	product_brand: '<?php echo  $product->get_attribute('pa_brands')?>'
	}
	]
	});

	// add to cart button Pinterest Event
	addToCartBtn.addEventListener('click', function(){
		pintrk('track', 'addtocart', {
			product_price: '<?php echo $product->get_price()?>',
			order_quantity:  Number(document.getElementsByClassName("qty")[0].value),
			currency: 'NZD',
			product_id: '<?php echo $product->get_id()?>', 
			product_category:'<?php echo $term -> name ?>'
			});
	})
 
</script>
   <?php 
}
?>
