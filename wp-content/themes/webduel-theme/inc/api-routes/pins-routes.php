<?php
//routes

add_action("rest_api_init", "pins_route");

function pins_route() {
    // get pins with slug 
    register_rest_route("inspiry/v1/", "get-pin", array(
        "methods" => "GET",
        "callback" => "getSinglePin"
    ));

     // get user specific pins
     register_rest_route("inspiry/v1/", "get-user-pins", array(
        "methods" => "POST",
        "callback" => "getUserPins"
    ));
    // get single pin for user with slug 
    register_rest_route("inspiry/v1/", "get-single-user-pin", array(
        "methods" => "POST",
        "callback" => "getSingleUserPin"
    ));
 // get single pin for user with slug 
    register_rest_route("inspiry/v1/", "update-single-user-pin", array(
        "methods" => "POST",
        "callback" => "updateSingleUserPin"
    ));
	 // delete single pin for user with slug 
    register_rest_route("inspiry/v1/", "delete-single-user-pin", array(
        "methods" => "POST",
        "callback" => "deleteSingleUserPin"
    ));

     // get all pins
     register_rest_route("inspiry/v1/", "get-all-pins", array(
        "methods" => "GET",
        "callback" => "getAllPins"
    ));
}

    // get pins with slug 
    function getSinglePin($data) {
        $slug = sanitize_text_field($data["slug"]);
        // get post with slug 
        $post = get_page_by_path( $slug, OBJECT, 'projects' );
        $postID = $post->ID; 
        $authorID = $post->post_author; 
        $user = get_user_by('id', $authorID); 
        $tradeProfessionalID = get_field("trade_professional_id", $postID); 
        $pinResult =  array(); 

        if($tradeProfessionalID){ 
            array_push($pinResult, array(
                'id' => $postID,
                "title"=>get_the_title($postID), 
                "gallery"=> get_field("gallery", $postID), 
                "website"=> get_field("website_link", $postID), 
                "description"=> get_field("description", $postID),
                "tradeProfessionalID"=> get_field("trade_professional_id", $postID), 
                "authorInfo"=> array(
                    "authorID"=> $authorID,
                    "profileImage"=>get_field('logo', $tradeProfessionalID),
                    "slug"=> get_post_field( 'post_name', $tradeProfessionalID ),
                    'username'=> get_the_title($tradeProfessionalID),
                    "userRole"=> $user->roles
                )
            ));   
        }
        else { 
            array_push($pinResult, array(
                'id' => $postID,
                "title"=>get_the_title($postID), 
                "gallery"=> get_field("gallery", $postID), 
                "website"=> get_field("website_link", $postID), 
                "description"=> get_field("description", $postID), 
                "tradeProfessionalID"=> get_field("trade_professional_id", $postID), 
                "authorInfo"=> array(
                    "authorID"=> $authorID,
                    'profileImage'=>$user->profile_image,
                    'firstName'=> $user->first_name,
                    'lastName'=> $user->last_name, 
                    'email'=> $user->user_email, 
                    'website'=> $user->website, 
                    'industry'=> $user->industry,
                    'jobTitle'=> $user->job_title,
                    'company'=> $user->company, 
                    "username"=> $user->user_login, 
                    "userRole"=> $user->roles
                )
            ));   
        }
      
        return $pinResult; 
    }

      // get user specific pins 
      function getUserPins($data) {
        
        if(is_user_logged_in()){
            $projects = new WP_Query(array(
               'post_type' => 'projects',
               'post_parent' => 0, 
               'posts_per_page' => -1, 
               'author' => get_current_user_id()
            )); 

            $projectsResult = array(); 
            while($projects->have_posts()){
               $projects->the_post(); 
            
               array_push($projectsResult, array(
                  'title' => get_the_title(),
                  'description' => get_field('description'), 
                  'id' => get_the_id(), 
                  'status' => get_post_status(), 
                  "pinImage" => get_field('gallery'),
                   "slug"=> get_post_field( 'post_name', get_the_ID() )
               ));       
            }

        return $projectsResult; 
    }  
    else{
    return 'you do not have permission' ;
    }
    }


    // get single user pin with slug 
    function getSingleUserPin($data) {
        $slug = sanitize_text_field($data["slug"]);
        // get post with slug 
        $post = get_page_by_path( $slug, OBJECT, 'projects' );
        $postID = $post->ID; 
        $authorID = $post->post_author; 
      

        if(is_user_logged_in() && get_current_user_id() === (int)$authorID){
            $projects = new WP_Query(array(
               'post_type' => 'projects',
               'p' => $postID, 
               'posts_per_page' => -1, 
               'author' => get_current_user_id()
            )); 
            
            $projectsResult = array(); 
            while($projects->have_posts()){
               $projects->the_post(); 
               $tradeProfessionalID = get_field("trade_professional_id"); 
               array_push($projectsResult, array(
                  'title' => get_the_title(),
                  'description' => get_field('description'), 
                  'id' => get_the_id(), 
                  'status' => get_post_status(), 
                  "pinImage" => get_field('gallery'),
                   "slug"=> get_post_field( 'post_name', get_the_ID() ), 
                   "websiteLink"=> get_field("website_link"), 
                   "postCategory" => wp_get_post_terms(454333, array('project_categories')), 
                   "tradeProfessional"=> get_the_title($tradeProfessionalID),
                   "tradeProfessionalID" => $tradeProfessionalID
               ));       
            }

        return $projectsResult; 
    }  
    else{
    return 'you do not have permission' ;
    }
    }

		function updateSingleUserPin($data){
			 $postID = sanitize_text_field($data["id"]);
			 $title = sanitize_text_field($data["title"]);
			 $tradeProfessionalID = sanitize_text_field($data["trade_professional_id"]);
			$websiteLink = sanitize_text_field($data["website_link"]);
			$description = sanitize_text_field($data["description"]);
			$gallery = $data["gallery"];
			$slug = sanitize_text_field($data["slug"]);
			$parentCategory = sanitize_text_field($data["parentCategory"]);
			$subCategory = sanitize_text_field($data["subCategory"]);
            $status=sanitize_text_field($data["status"]);

        // get post with slug 
        $post = get_page_by_path( $slug, OBJECT, 'projects' );
        $authorID = $post->post_author; 
			// check id the logged in user is an author of a post
        if(is_user_logged_in() && get_current_user_id() === (int)$authorID){
			update_field("trade_professional_id", $tradeProfessionalID, $postID); 
			update_field("website_link", $websiteLink, $postID); 
			update_field("description", $description, $postID); 
			update_field("gallery", $gallery, $postID); 
			// 			update taxonomies 
		   wp_set_object_terms( $postID, array($parentCategory, $subCategory), "project_categories" );
          wp_insert_post(array(
			 "ID" => $postID, 
			 "post_type" => "projects",
			 "post_title" => $title, 
			  "post_status"=> $status
			 )); 
			
        return 200; 
    }  
    else{
   return 'you do not have permission' ;
	
    }
		}



		function deleteSingleUserPin($data){
			 $postID = sanitize_text_field($data["id"]);
			$slug = sanitize_text_field($data["slug"]);
        // get post with slug 
        $post = get_page_by_path( $slug, OBJECT, 'projects' );
        $authorID = $post->post_author; 
			// check id the logged in user is an author of a post
        if(is_user_logged_in() && get_current_user_id() === (int)$authorID){
			wp_delete_post($postID, true);
        $response = new WP_REST_Response("Project is deleted!!!", 200);
			return $response;
			
    }  
    else{
		return new WP_Error( 'you do not have permission', 'you do not have permission', array( 'status' => 404 ) );
	
    }
		}

    function getAllPins() {
        $projects = new WP_Query(array(
            'post_type' => 'projects',
            'posts_per_page' => -1
         )); 
         
         $projectsResult = array(); 
         while($projects->have_posts()){
            $projects->the_post(); 
            $tradeProfessionalID = get_field("trade_professional_id"); 
            array_push($projectsResult, array(
               'title' => get_the_title(),
               'id' => get_the_id(), 
               'status' => get_post_status(), 
               "pinImage" => get_field('gallery'),
                "slug"=> get_post_field( 'post_name', get_the_ID() ), 
                "postCategory" => wp_get_post_terms(454333, array('project_categories')), 
                "tradeProfessional"=> get_the_title($tradeProfessionalID),
                "tradeProfessionalID" => $tradeProfessionalID
            ));       
         }

     return $projectsResult; 
    }
?>