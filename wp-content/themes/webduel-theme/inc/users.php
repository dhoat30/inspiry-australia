<?php

add_action( 'show_user_profile', 'extra_user_profile_fields' );
add_action( 'edit_user_profile', 'extra_user_profile_fields' );

function extra_user_profile_fields( $user ) { ?>
    <h3><?php _e("Extra profile information", "blank"); ?></h3>

    <table class="form-table">
    <tr>
        <th><label for="profile_image"><?php _e("Profile Image"); ?></label></th>
        <td>
            <input type="text" name="profile_image" id="profile_image" value="<?php echo esc_attr( get_the_author_meta( 'profile_image', $user->ID ) ); ?>" class="regular-text" /><br />
            <span class="description"><?php _e("Please add your profile image url."); ?></span>
        </td>
    </tr>
    <tr>
        <th><label for="age_group"><?php _e("Age Group"); ?></label></th>
        <td>
            <input type="text" name="age_group" id="age_group" value="<?php echo esc_attr( get_the_author_meta( 'age_group', $user->ID ) ); ?>" class="regular-text" /><br />
            <span class="description"><?php _e("Please enter your age_group."); ?></span>
        </td>
    </tr>
    <tr>
    <th><label for="regions_district"><?php _e("Region/District"); ?></label></th>
        <td>
            <input type="text" name="regions_district" id="regions_district" value="<?php echo esc_attr( get_the_author_meta( 'regions_district', $user->ID ) ); ?>" class="regular-text" /><br />
            <span class="description"><?php _e("Please enter your region/district."); ?></span>
        </td>
    </tr>

    <tr>
    <th><label for="industry"><?php _e("Industry"); ?></label></th>
        <td>
            <input type="text" name="industry" id="industry" value="<?php echo esc_attr( get_the_author_meta( 'industry', $user->ID ) ); ?>" class="regular-text" /><br />
            <span class="description"><?php _e("Please enter your industry."); ?></span>
        </td>
    </tr>

    <tr>
    <th><label for="job_title"><?php _e("Job Title"); ?></label></th>
        <td>
            <input type="text" name="job_title" id="job_title" value="<?php echo esc_attr( get_the_author_meta( 'job_title', $user->ID ) ); ?>" class="regular-text" /><br />
            <span class="description"><?php _e("Please enter your Job Title."); ?></span>
        </td>
    </tr>
		
		 <tr>
    <th><label for="phone"><?php _e("Phone"); ?></label></th>
        <td>
            <input type="text" name="phone" id="phone" value="<?php echo esc_attr( get_the_author_meta( 'phone', $user->ID ) ); ?>" class="regular-text" /><br />
            <span class="description"><?php _e("Please enter phone."); ?></span>
        </td>
    </tr>
		
		<tr>
    <th><label for="website"><?php _e("Website"); ?></label></th>
        <td>
            <input type="text" name="website" id="website" value="<?php echo esc_attr( get_the_author_meta( 'phone', $user->ID ) ); ?>" class="regular-text" /><br />
            <span class="description"><?php _e("Please enter website url."); ?></span>
        </td>
    </tr>
    </table>
<?php }


add_action( 'personal_options_update', 'save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'save_extra_user_profile_fields' );

function save_extra_user_profile_fields( $user_id ) {
    if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'update-user_' . $user_id ) ) {
        return;
    }
    
    if ( !current_user_can( 'edit_user', $user_id ) ) { 
        return false; 
    }
    update_user_meta( $user_id, 'profile_image', $_POST['profile_image'] );
    update_user_meta( $user_id, 'age_group', $_POST['age_group'] );
    update_user_meta( $user_id, 'regions_district', $_POST['regions_district'] );
    update_user_meta( $user_id, 'industry', $_POST['industry'] );
    update_user_meta( $user_id, 'job_title', $_POST['job_title'] );
	update_user_meta( $user_id, 'phone', $_POST['phone'] );
	update_user_meta( $user_id, 'website', $_POST['website'] );
}


// user route
$user_id = ""; //<- add this

add_action("rest_api_init", "inspiry_user_route");

function inspiry_user_route(){ 
	    $GLOBALS['user_id'] = get_current_user_id(); //<- add this

    //get user
   register_rest_route("inspiry/v1/", "get-user", array(
      "methods" => "POST",
      "callback" => "getUser"
   ));
	
	 //update user 
   register_rest_route("inspiry/v1/", "update-user", array(
      "methods" => "POST",
      "callback" => "updateUser"
   ));
	
	 //update profile image 
   register_rest_route("inspiry/v1/", "update-image", array(
      "methods" => "POST",
      "callback" => "updateProfileImage"
   ));

   // 	register user 
   register_rest_route('wp/v2', 'users/register', array(
	'methods' => 'POST',
	'callback' => 'wc_rest_user_endpoint_handler',
  ));
}
	// get board - new
	function getUser($data){
// 		  get_user_by( 'id', $GLOBALS['user_id'] ); //<- add this
// 					   $userID = sanitize_text_field($data["userID"] ); 
							
							$user = get_user_by('id',  $GLOBALS['user_id']); 
		   					//  update_user_meta( $postID, 'age_group', "25-45" );

					   if(is_user_logged_in()){
							$userResult = []; 
						  array_push($userResult, array(
								'profileImage'=>$user->profile_image,
							  'firstName'=> $user->first_name,
							  'lastName'=> $user->last_name, 
							  'email'=> $user->user_email, 
							  'phoneNumber'=> $user->phone, 
							  'website'=> $user->website, 
							  'ageGroup'=> $user->age_group,				
							  'regionsDistrict'=> $user->regions_district,
							  'industry'=> $user->industry,
							  'jobTitle'=> $user->job_title,
							  'company'=> $user->company, 
							  "userID"=> $GLOBALS['user_id'], 
							  "username"=> $user->user_login, 
							  "userRole"=> $user->roles
						  ));       
					   
   				return $userResult; 
			   }  
			   else{
			   return 'you do not have permission' ;
			   }
	}

// update user - new
	function updateUser($data){
					$userID = sanitize_text_field($data["userID"]);
					$firstName = sanitize_text_field($data["first_name"]);
					$lastName = sanitize_text_field($data["last_name"]);
					$userEmail = sanitize_text_field($data["user_email"]);
					$phone = sanitize_text_field($data["phone"]);
					$website = sanitize_text_field($data["website"]);
					$ageGroup = sanitize_text_field($data["age_group"]);
					$regionDistrict = sanitize_text_field($data["regions_district"]);
					$industry = sanitize_text_field($data["industry"]);
					$company = sanitize_text_field($data["company"]);
					$jobTitle = sanitize_text_field($data["job_title"]);
                    $user = get_user_by('id', $userID); 
					
					// 		update email address
					$args = array(
						'ID'         => $userID,
						'user_email' => esc_attr( $data["user_email"] )
					);
				 wp_update_user( $args );
				
						
					update_user_meta($userID, 'first_name', $firstName);
					update_user_meta($userID, 'last_name', $lastName);
					update_user_meta($userID, 'user_email', $userEmail);
					update_user_meta($userID, 'phone', $phone);
					update_user_meta($userID, 'website', $website);
					update_user_meta($userID, 'age_group', $ageGroup);
					update_user_meta($userID, 'regions_district', $regionDistrict);
					update_user_meta($userID, 'industry', $industry);
					update_user_meta($userID, 'company', $company);
					update_user_meta($userID, 'job_title', $jobTitle);
					

					   if(is_user_logged_in()){
							$userResult = []; 
						  array_push($userResult, array(
								'profileImage'=>$user->profile_image,
							  'firstName'=> $user->first_name,
							  'lastName'=> $user->last_name, 
							  'email'=> $user->user_email, 
							  'phoneNumber'=> $user->phone, 
							  'website'=> $user->website, 
							  'ageGroup'=> $user->age_group,				
							  'regionsDistrict'=> $user->regions_district,
							  'industry'=> $user->industry,
							  'jobTitle'=> $user->job_title,
							  'company'=> $user->company
						  ));       
					   
   				return $userResult; 
			   }  
			   else{
			   return 'you do not have permission' ;
			   }
	}

// update user - new
	function updateProfileImage($data){
					$userID = sanitize_text_field($data["userID"]);
					
				$profileImage = sanitize_text_field($data["profile_image"]);
					$user = get_user_by('id', $userID); 

					
					update_user_meta($userID, 'profile_image', $profileImage);

					   if(is_user_logged_in()){
							$userResult = []; 
						  array_push($userResult, array(
								'profileImage'=>$user->profile_image,
							  'firstName'=> $user->first_name,
							  'lastName'=> $user->last_name, 
							  'email'=> $user->user_email, 
							  'phoneNumber'=> $user->phone, 
							  'website'=> $user->website, 
							  'ageGroup'=> $user->age_group,				
							  'regionsDistrict'=> $user->regions_district,
							  'industry'=> $user->industry,
							  'jobTitle'=> $user->job_title,
							  'company'=> $user->company, 
							  'userID'=> $userID
						  ));       
					   
   				return $userResult; 
			   }  
			   else{
			   return 'you do not have permission' ;
			   }
	}

// register user endpoint
function wc_rest_user_endpoint_handler($request = null) {
	$response = array();
	$parameters = $request->get_json_params();
	$username = sanitize_text_field($parameters['username']);
	$email = sanitize_text_field($parameters['email']);
	$password = sanitize_text_field($parameters['password']);
	// $role = sanitize_text_field($parameters['role']);
	$error = new WP_Error();
	if (empty($username)) {
	  $error->add(400, __("Username field 'username' is required.", 'wp-rest-user'), array('status' => 400));
	  return $error;
	}
	if (empty($email)) {
	  $error->add(401, __("Email field 'email' is required.", 'wp-rest-user'), array('status' => 400));
	  return $error;
	}
	if (empty($password)) {
	  $error->add(404, __("Password field 'password' is required.", 'wp-rest-user'), array('status' => 400));
	  return $error;
	}
  
	$user_id = username_exists($username);
	if (!$user_id && email_exists($email) == false) {
	  $user_id = wp_create_user($username, $password, $email);
	  if (!is_wp_error($user_id)) {
		// Ger User Meta Data (Sensitive, Password included. DO NOT pass to front end.)
		$user = get_user_by('id', $user_id);
		// $user->set_role($role);
		$user->set_role('subscriber');
		// WooCommerce specific code
		if (class_exists('WooCommerce')) {
		  $user->set_role('customer');
		}
		// Ger User Data (Non-Sensitive, Pass to front end.)
		$response['code'] = 200;
		$response['message'] = __("User '" . $username . "' Registration was Successful", "wp-rest-user");
	  } else {
		return $user_id;
	  }
	} else {
	  $error->add(406, __("Email or username already exists.", 'wp-rest-user'), array('status' => 400));
	  return $error;
	}
	return new WP_REST_Response($response, 123);
  }