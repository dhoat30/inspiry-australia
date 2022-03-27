<?php
//routes

add_action("rest_api_init", "trade_route");

function trade_route() {
		// 	get trade post using category slug
		  register_rest_route("inspiry/v1/", "trade-categories", array(
				"methods" => "POST",
				"callback" => "getTradeCategories"
			));
		// get trade professionals 
		register_rest_route("inspiry/v1/", "trade-professionals", array(
			"methods" => "GET",
			"callback" => "getTradeProfessionals"
		));
		// get trade professionals 
		register_rest_route("inspiry/v1/", "user-trade-profile", array(
					"methods" => "POST",
					"callback" => "getUserTradeProfile"
		));
}
// get trade categories 
	function getTradeCategories($data){
		 $categorySlug = $data["categorySlug"];
		 $tagSlug = $data["tagSlug"];
		  $argsCategories = array(
					'post_type' => 'trade-professionals',
					'posts_per_page'=> -1,
						'tax_query' => array(
							'relation' => 'OR',
							array(
								'taxonomy' => 'trade_professional_categories',
								'field'    => 'slug',
								'terms'    => array( $categorySlug)
							),
							array(
								'taxonomy' => 'trade_professional_tags',
								'field'    => 'slug',
								'terms'    => array( $tagSlug)
							),
							), 	
				);
				$trade = new WP_Query( $argsCategories );
				$postResult = array(); 
				while($trade->have_posts()){
					$trade->the_post(); 
					
					array_push($postResult, array(
						"id"=>get_the_id(), 
						"title"=>get_the_title(), 
						"acf"=> array(
							"logo" =>get_field('logo', get_the_id())
						), 
						"slug"=> get_post_field( 'post_name',get_the_id() ),
						"categories"=> get_the_terms(get_the_id(), "trade_professional_categories"), 
						"tags"=> get_the_terms(get_the_id(), "trade_professional_tags"), 
						"content"=> get_the_content()
					));	
				}
		return $postResult;
	}

	// get trade professionals 
	function getTradeProfessionals(){
	
		 $args = array(
				   'post_type' => 'trade-professionals',
				   'posts_per_page'=> -1,
				   'orderby'   => 'title',
        			'order' => 'ASC',
			   );
			   $trade = new WP_Query( $args );
			   $postResult = array(); 
			   while($trade->have_posts()){
				   $trade->the_post(); 
				   array_push($postResult, array(
				   "id"=>get_the_id(), 
				   "title"=>get_the_title(), 
				   "acf"=> array(
					   "logo" =>get_field('logo', get_the_id())
				   ), 
				   "slug"=> get_post_field( 'post_name',get_the_id() ),
				   "categories"=> get_the_terms(get_the_id(), "trade_professional_categories"), 
				   "tags"=> get_the_terms(get_the_id(), "trade_professional_tags"), 
				   "content"=> get_the_content()
				   ));	
			   }
	   return $postResult;
   }
	// get trade professionals 
	function getUserTradeProfile(){

		if(is_user_logged_in()){
			$tradeProfile = new WP_Query(array(
			   'post_type' => 'trade-professionals',
			   'posts_per_page' => -1, 
			   'author' => get_current_user_id()
			)); 
			$tradeResult = array(); 
			while($tradeProfile->have_posts()){
				$tradeProfile->the_post(); 
				array_push($tradeResult, array(
					'title' => get_the_title(),
					'description' => get_the_content(), 
					'id' => get_the_id(), 
					'status' => get_post_status(), 
					 "slug"=> get_post_field( 'post_name', get_the_ID() ), 
					 "logo"=> get_field("logo"), 
					 "heroImage"=> get_field("hero_image"), 
					 "phoneNumber"=> get_field("phone_number"), 
					 "mobile_number"=> get_field("mobile_number"), 
					 "email_address"=> get_field("email_address"), 
					 "website"=> get_field("website"), 
					 "facebook"=> get_field("facebook"), 
					 "instagram"=> get_field("linkedin"), 
					 "pinterest"=> get_field("pinterest"), 
					 "street_address"=> get_field("street_address"), 
					 "suburb"=> get_field("suburb"), 
					 "townCity"=> get_field("town_city"), 
					 "postCode"=> get_field("postcode"), 
					 "services"=> get_field("services")
				 )); 
			}
			return $tradeResult;
		}
		else{ 
			return new WP_Error( 'you do not have permission', 'you do not have permission', array( 'status' => 404 ) );
		} 
  }
?>