<?php

//routes

add_action("rest_api_init", "inspiry_board_route");

function inspiry_board_route(){ 
      //get boards
   register_rest_route("inspiry/v1/", "get-boards", array(
      "methods" => "POST",
      "callback" => "getBoard"
   ));
	
	// 	add to board
    register_rest_route("inspiry/v1/", "add-to-board", array(
      "methods" => "POST",
      "callback" => "addProjectToBoard"
      ));
	
// 	create board 
		register_rest_route("inspiry/v1/", "manage-board", array(
		   "methods" => "POST",
		   "callback" => "createBoard"
		));
	
	// 	get pins related to the single board 
		register_rest_route("inspiry/v1/", "get-pins", array(
		   "methods" => "POST",
		   "callback" => "getPins"
		));
	
	  //update board -new
    register_rest_route("inspiry/v1/", "update-board", array(
      "methods" => "POST",
      "callback" => "updateBoard"
  	));

// 	delete board - new
    register_rest_route("inspiry/v1/", "delete-board", array(
        "methods" => "DELETE",
        "callback" => "deleteBoardFunc"
    ));
    
	
    register_rest_route("inspiry/v1/", "manage-board", array(
        "methods" => "DELETE",
        "callback" => "deletePin"
    ));

	// upload image 
	register_rest_route("inspiry/v1/", "upload-image-board", array(
        "methods" => "POST",
        "callback" => "uploadImageBoard"
    ));

}

	// get board - new
	function getBoard($data){
					   $postID = sanitize_text_field($data["id"] ); 
					   if(is_user_logged_in()){
					   $boards = new WP_Query(array(
						  'post_type' => 'boards',
						  'post_parent' => 0, 
						  'posts_per_page' => -1, 
						  'p' => $postID,
						  'author' => get_current_user_id()
					   )); 

					   $boardsResult = array(); 
					   while($boards->have_posts()){
						  $boards->the_post(); 
                            //GET THE CHILD ID
                            //Instead of calling and passing query parameter differently, we're doing it exclusively
                            $all_locations = get_pages( array(
                              'post_type'         => 'boards', //here's my CPT
                              'post_status'       => array( 'private', 'pending', 'publish') //my custom choice
                          ) );

                          //Using the function
                          $parent_id = get_the_id();
                          $inherited_locations = get_page_children( $parent_id, $all_locations );

                          // echo what we get back from WP to the browser (@bhlarsen's part :) )
                          $child_id = $inherited_locations[0]->ID;
                      //get project, trade or product id 
                      $pinImage = ''; 
						   $tradeImage = ''; 
						$productImage = ''; 
                       if(get_field("saved_project_id", $child_id)){ 
								$projectID = get_field("saved_project_id", $child_id); 
								 $gallery = get_field('gallery', $projectID);
								 $pinImage = $gallery;
							   }
							   elseif(get_field("trade_id", $child_id)){ 
									$tradeID = get_field("trade_id", $child_id); 
									$tradeImage = get_field('logo', $tradeID);
							   }
								elseif(get_field("product_id", $child_id)){ 
								$productID = get_field("product_id", $child_id); 
									 $curl = curl_init();

										// get product featured image id 				
										curl_setopt_array($curl, array(
										  CURLOPT_URL => 'https://inspiry.co.nz/wp-json/wp/v2/product/'.$productID.'?_embed',
										  CURLOPT_RETURNTRANSFER => true,
										  CURLOPT_ENCODING => '',
										  CURLOPT_MAXREDIRS => 10,
										  CURLOPT_TIMEOUT => 0,
										  CURLOPT_FOLLOWLOCATION => true,
										  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
										  CURLOPT_CUSTOMREQUEST => 'GET',
										));

										$response = curl_exec($curl);

										curl_close($curl);
										$data = json_decode($response);
										$imageID = $data->featured_media;
						
										// get the product featured image
										$curl = curl_init();

										curl_setopt_array($curl, array(
										  CURLOPT_URL => 'https://inspiry.co.nz/wp-json/wp/v2/media/'.$imageID,
										  CURLOPT_RETURNTRANSFER => true,
										  CURLOPT_ENCODING => '',
										  CURLOPT_MAXREDIRS => 10,
										  CURLOPT_TIMEOUT => 0,
										  CURLOPT_FOLLOWLOCATION => true,
										  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
										  CURLOPT_CUSTOMREQUEST => 'GET',
										));

										$imageResponse = curl_exec($curl);

										curl_close($curl);
										$imageData = json_decode($imageResponse);
										 $productImage = $imageData->source_url;
							   }
						  array_push($boardsResult, array(
							 'title' => get_the_title(),
							 'description' => get_the_content(), 
							 'id' => get_the_id(), 
							 'status' => get_post_status(), 
							 "pinImage" => $pinImage,
							  "tradeImage"=> $tradeImage,
							 "productImage"=> $productImage,
							  "slug"=> get_post_field( 'post_name', get_the_ID() )
						  ));       
					   }

   				return $boardsResult; 
			   }  
			   else{
			   return 'you do not have permission' ;
			   }
	}

		// add project to board 
		function addProjectToBoard($data){ 
		   if(is_user_logged_in()){
			  $boardID = sanitize_text_field($data["boardID"]);
			  $postTitle = sanitize_text_field($data["postTitle"]);
			  $publishStatus = sanitize_text_field($data['status']);
			  $projectID = sanitize_text_field($data['projectID']);
			  $tradeID = sanitize_text_field($data['tradeID']);
			  $productID = sanitize_text_field($data['productID']);

			  if($projectID){
				 return wp_insert_post(array(
					"post_type" => "boards", 
					"post_status" => $publishStatus, 
					"post_parent" => $boardID, 
					"post_title" => get_the_title($projectID),
					"meta_input" => array(
					   "saved_project_id" => $projectID
					)
				 )); 
			  }
			  elseif ($tradeID){
				 return wp_insert_post(array(
					"post_type" => "boards", 
					"post_status" => $publishStatus, 
					"post_title" => $postTitle,
					"post_parent" => $boardID, 
					"meta_input" => array(
					   "trade_id"=> $tradeID
					)
			 )); 
			  }
			  elseif($productID){
				 return wp_insert_post(array(
					"post_type" => "boards", 
					"post_status" => $publishStatus, 
					"post_title" => $postTitle,
					"post_parent" => $boardID, 
					"meta_input" => array(
					   "product_id"=> $productID
					)
			 )); 

			  }
		   }
		   else{
			  die("Only logged in users can create a board");
		   }

		}

	// create board 
	function createBoard($data){ 
	   if(is_user_logged_in()){
		  $boardName = sanitize_text_field($data["boardName"]);
		  $boardDescription = sanitize_text_field($data['boardDescription']); 
		  $publishStatus = sanitize_text_field($data['status']);

		  $existQuery = new WP_Query(array(
			'author' => get_current_user_id(), 
			'post_type' => 'boards', 
			's' => $boardName
		)); 
		 if($existQuery->found_posts == 0){ 
			return wp_insert_post(array(
				"post_type" => "boards", 
				"post_status" => $publishStatus, 
				"post_title" => $boardName,
				'post_content' => $boardDescription
		 )); 
		 }
		 else{ 
			 die('Board already exists');
		 }
	   }
	   else{
		  die("Only logged in users can create a board");
	   }
	}

		// get pins - new
		function getPins($data){
		   $slug = sanitize_text_field($data["slug"] ); 
			// check if the user is logged in 
		   if(is_user_logged_in()){
		   
			   $parentID = 0; 
					if ( $post = get_page_by_path( $slug, OBJECT, 'boards' ) ){
						$parentID = $post->ID;
					}
			   	$parentStatus = get_post_status($parentID); 
				$parentName = get_the_title($parentID);
			   	$pinResult = array(); 
				$boardLoop = new WP_Query(array(
                'post_type' => 'boards', 
                'post_parent' => $parentID,
                'posts_per_page' => -1,
				'author' => get_current_user_id()
            	));
			   	
			     while($boardLoop->have_posts()){
                $boardLoop->the_post(); 
					 
				// get the ids for images 
			   	$projectID = 0; 
			   $tradeID=0; 
			   $productID = 0; 
				$pinImage = ''; 
				$productImage=''; 
				$slug=''; 
				
			   if(get_field("saved_project_id")){ 
			   	$projectID = get_field("saved_project_id"); 
				 $gallery = get_field('gallery', $projectID);
				 $pinImage = $gallery;
				 global $post;
				$slug= $post->post_name; 
			   }
			   elseif(get_field("trade_id")){ 
			   	$tradeID = get_field("trade_id"); 
				    $pinImage = get_field('logo', $tradeID);
				     global $post;
					$slug= $post->post_name; 
			   }
			    elseif(get_field("product_id")){ 
			   	$productID = get_field("product_id"); 
					 $curl = curl_init();
						
						// get product featured image id 				
						curl_setopt_array($curl, array(
						  CURLOPT_URL => 'https://inspiry.co.nz/wp-json/wp/v2/product/'.$productID.'?_embed',
						  CURLOPT_RETURNTRANSFER => true,
						  CURLOPT_ENCODING => '',
						  CURLOPT_MAXREDIRS => 10,
						  CURLOPT_TIMEOUT => 0,
						  CURLOPT_FOLLOWLOCATION => true,
						  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						  CURLOPT_CUSTOMREQUEST => 'GET',
						));

						$response = curl_exec($curl);

						curl_close($curl);
						$data = json_decode($response);
						$imageID = $data->featured_media;
						$slug = $data->link; 

						// get the product featured image
						$curl = curl_init();

						curl_setopt_array($curl, array(
						  CURLOPT_URL => 'https://inspiry.co.nz/wp-json/wp/v2/media/'.$imageID,
						  CURLOPT_RETURNTRANSFER => true,
						  CURLOPT_ENCODING => '',
						  CURLOPT_MAXREDIRS => 10,
						  CURLOPT_TIMEOUT => 0,
						  CURLOPT_FOLLOWLOCATION => true,
						  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
						  CURLOPT_CUSTOMREQUEST => 'GET',
						));

						$imageResponse = curl_exec($curl);

						curl_close($curl);
						$imageData = json_decode($imageResponse);
						 $productImage = $imageData->source_url;
			   }
					 
					 array_push($pinResult, array(
						 	'parentName'=> $parentName,
						 	'status'=> $parentStatus, 
						 	'pinImage' => $pinImage,
						 	'productImage'=> $productImage, 
						  	'slug'=> $slug,
						 	'projectID'=> $projectID,
						 	"tradeID"=> $tradeID,
						  	'productID'=> $productID, 
						 "this"=>"this is des",
						 	'id' => get_the_id(), 
						 	'title'=> get_the_title(),
							 "parentID"=> $parentID, 
							 "pins"=> true,
							 "gallery"=> get_field("gallery", $parentID ),
						 "parentSlug"=> $slug = get_post_field( 'post_name',$parentID ), 
						));   
				 }

			   if($pinResult){ 
			   return $pinResult; 
			   }
			   else{ 
				   array_push($pinResult, array(
						 'parentName'=> $parentName,
						 	'status'=> $parentStatus, 
							"parentID"=> $parentID, 
							"pins"=> false,
							"parentSlug"=> $slug = get_post_field( 'post_name',$parentID ), 
							"gallery"=> get_field("gallery", $parentID )
						));   
				  	return $pinResult; 
			   }
		   }  
		   else{
		   return 'you do not have permission' ;
		   }
		}

	// 	update board - new
	function updateBoard($data){
	   $parentID = sanitize_text_field($data["boardID"] ); 
	   $boardName = sanitize_text_field($data["boardName"] ); 
	   $boardDescription = sanitize_text_field($data["boardDescription"] );
	   $publishStatus  = sanitize_text_field($data["status"] );

		if(get_current_user_id() == get_post_field("post_author", $parentID) AND get_post_type($parentID)=="boards"){

			//Instead of calling and passing query parameter differently, we're doing it exclusively
			$all_locations = get_pages( array(
				'post_type'         => 'boards', //here's my CPT
				'post_status'       => array( 'private', 'pending', 'publish') //my custom choice
			) );

			//Using the function
			$inherited_locations = get_page_children( $parentID, $all_locations );
			// echo what we get back from WP to the browser (@bhlarsen's part :) )
				// Update all the Children of the Parent Page
				foreach($inherited_locations as $post){

					wp_insert_post(array(
					  "ID" => $post->ID, 
					  "post_type" => "boards", 
					  "post_status" => $publishStatus,
					  'post_parent'=> $parentID, 
					  "post_title" =>get_the_title($post->ID)
				   )); 
				}

			// Update the Parent Page
			wp_insert_post(array(
			 "ID" => $parentID, 
			 "post_type" => "boards", 
			 "post_status" => $publishStatus, 
			 "post_title" => $boardName,
			 'post_content' => $boardDescription
			 )); 

			return 'updation worked. congrats'; 
		 }
		 else{ 
			die("You do not have permission to update a board");
		 }
	}


	function deleteBoardFunc($data){ 
		$parentID = sanitize_text_field($data["boardID"] ); 

		// Delete the Parent Page
		if(get_current_user_id() == get_post_field("post_author", $parentID) AND get_post_type($parentID)=="boards"){

			//Instead of calling and passing query parameter differently, we're doing it exclusively
			$all_locations = get_pages( array(
				'post_type'         => 'boards', //here's my CPT
				'post_status'       => array( 'private', 'pending', 'publish') //my custom choice
			) );

			//Using the function
			$inherited_locations = get_page_children( $parentID, $all_locations );
			// echo what we get back from WP to the browser (@bhlarsen's part :) )
				// Delete all the Children of the Parent Page
				foreach($inherited_locations as $post){

					wp_delete_post($post->ID, true);
				}

			// Delete the Parent Page
			wp_delete_post($parentID, true);

			return 'deletion worked. congrats'; 
		 }
		 else{ 
			die("you do not have permission to delete a pin");
		 }
	}

function deletePin($data){ 
   $pinID = sanitize_text_field($data["id"] ); 
	
   if(get_current_user_id() == get_post_field("post_author", $pinID) ){
      wp_delete_post($pinID, true); 
      return 200; 
   }
   else{ 
      die("you do not have permission to delete a pin");
   }
}
// upload image in a board
function uploadImageBoard($data){
	$slug = sanitize_text_field($data["parentSlug"] ); 
	$postID = sanitize_text_field($data["parentBoardID"] ); 
	$uploadedImages = sanitize_text_field($data["uploadedImages"] ); 
	$gallery = $data["gallery"]; 
	 // get post with slug 
	 $post = get_page_by_path( $slug, OBJECT, 'boards' );
	 $authorID = $post->post_author; 
		// check id the logged in user is an author of a post
        if(is_user_logged_in() && get_current_user_id() === (int)$authorID){
			update_field("gallery", $gallery, $postID); 
			update_field("uploaded_images", $uploadedImages, $postID);
        return 200; 
    }  
    else{
    return 'you do not have permission' ;
    }
}