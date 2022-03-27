<?php 
add_action("rest_api_init", "inspiry_orders_route");

function inspiry_orders_route(){ 
    //get user
   register_rest_route("inspiry/v1/", "get-orders", array(
      "methods" => "POST",
      "callback" => "getOrders"
   ));

    //get order with order number 
	register_rest_route("inspiry/v1/", "get-order-with-number", array(
		"methods" => "POST",
		"callback" => "getOrderWithOrderNumber"
	 ));
	

}
	// get board - new
	function getOrders($data){
					   $userID = sanitize_text_field($data["userID"] ); 

						if(is_user_logged_in()){
							$customer_orders = get_posts( array(
								'numberposts' => -1,
								'meta_key'    => '_customer_user',
								'meta_value'  => get_current_user_id(),
								'post_type'   => wc_get_order_types(),
								'post_status' => array_keys( wc_get_order_statuses() ),
							) );

							$orderResult = []; 
							foreach($customer_orders as $customer_order){
								$orderID = $customer_order->ID;
								$order = wc_get_order($orderID );
								
								// 	loop over the product list
								$productResult = []; 
								foreach ( $order->get_items() as $item_id => $item ) {
								$image_url = wp_get_attachment_image_src( get_post_thumbnail_id( $item->get_product_id() ), 'single-post-thumbnail' );

									 array_push($productResult, array(
								   "productID" => $item->get_product_id(),							
								  "product"=> $item->get_product(),
								   "productName" => $item->get_name(), 
									"total" => $item->get_total(),
									"productImage"=> $image_url,
									"productLink"=> get_the_permalink($item->get_product_id()),
								   "quantity" => $item->get_quantity(),
										 ));
								}
 											$orderDate = $order->get_date_created(); 
									// order meta
									 $shippingData = $order->get_meta('_wc_shipment_tracking_items'); 
 										$final_array = array_values($shippingData);
										  array_push($orderResult, array(
											"orderDate"=>    $orderDate->date("d/m/y"), 
											  "postStatus"=>  $order->get_status(), 
											  "orderNumber"=> $orderID, 
											  "orderSubtotal"=> $order->get_subtotal(), 
											  "taxTotal"=>$order->get_tax_totals(),
											  "taxes"=> $order->get_taxes(),
											  "totalDiscount"=> $order->get_total_discount(),
											  "orderTotal"=> $order->get_total(),
											   "estimatedDelivery"=> get_field('estimated_delivery_date', $orderID),
											  	"products"=> $productResult, 
											  "customerFirstName"=> $order->get_shipping_first_name(), 
											  "customerLastName"=> $order->get_shipping_last_name(),
											  "address1"=> $order->get_shipping_address_1(),
											  "address2"=> $order->get_shipping_address_2(),
											  "city"=> $order->get_shipping_city(), 
											  "state"=> $order->get_shipping_state(), 
											  "postCode"=> $order->get_shipping_postcode(), 
											  "country"=> $order->get_shipping_country(), 
											  "email"=> $order->get_billing_email(), 
											  "phone"=> $order->get_billing_phone(), 
											  "paymentMethod"=> $order->get_payment_method(),
											  "paymentTitle"=> $order->get_payment_method_title(),
											  "shippingTotal"=> $order->get_shipping_total(),
											  "shippingInfo"=> $final_array, 
											  "orderCustomerNote"=> wc_get_order_notes([
												'order_id' => $order->get_id(),
												'type' => 'customer',
											 ])
										  ));      										
							}
							
					   
   				return $orderResult; 
			   }  
			   else{
			   return 'you do not have permission' ;
			   }
	}

	function getOrderWithOrderNumber($data){
		$orderNumber = sanitize_text_field($data["orderNumber"]);

			$customer_orders = get_posts( array(
								'numberposts' => -1,
								'include'  => $orderNumber,
								'post_type'   => wc_get_order_types(),
								'post_status' => array_keys( wc_get_order_statuses() ),
							) );

							$orderResult = []; 
							foreach($customer_orders as $customer_order){
								$orderID = $customer_order->ID;
								$order = wc_get_order($orderID );
							
 											
									// order meta
									 $shippingData = $order->get_meta('_wc_shipment_tracking_items'); 
 										$final_array = array_values($shippingData);
										  array_push($orderResult, array(
											  "postStatus"=>  $order->get_status(), 
										  ));      										
							}
							
		return $orderResult; 
	}
