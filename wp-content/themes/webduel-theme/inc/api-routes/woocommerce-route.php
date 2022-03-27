<?php
//routes

add_action("rest_api_init", "woocommerce_product");

function woocommerce_product(){ 
   
  //update board 
  register_rest_route("inspiry/v1/", "product", array(
   "methods" => "POST",
   "callback" => "getProduct"
));
   
}

function getProduct($data){
   $postID = sanitize_text_field($data["id"] ); 
   if(is_user_logged_in()){
      $boards = new WP_Query(array(
         'post_type' => 'product',
         'p' => $postID
      )); 
      
      $boardsResult = array(); 
   
      while($boards->have_posts()){
         $boards->the_post(); 
         global $product; 
         array_push($boardsResult, array(
            'product' =>  $product->get_attributes('availability')
         )); 
      }
      return $boardsResult; 

   }
   
  
}


