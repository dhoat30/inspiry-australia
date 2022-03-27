<?php
//routes

add_action("rest_api_init", "social_login_route");

function social_login_route() {
		// social login
	register_rest_route("inspiry/v1/", "social-login", array(
			"methods" => "POST",
			"callback" => "socialLogin"
		));
        	// login with facebook 
	register_rest_route("inspiry/v1/", "facebook-login", array(
        "methods" => "POST",
        "callback" => "facebookLogin"
    ));
}

// social login
function socialLogin($data){
    $userLogin = sanitize_text_field($data["userLogin"]);
    $email = sanitize_text_field($data["email"]);
    $givenName = sanitize_text_field($data["givenName"]);
    $familyName=sanitize_text_field($data["familyName"]);
    // $imageUrl=sanitize_text_field($data["imageUrl"]);

    $bytes = openssl_random_pseudo_bytes(2);
    $password = md5(bin2hex($bytes));
    if(!email_exists($email)){ 
        $new_user_id = wp_insert_user(array(
            'user_login'		=> $userLogin,
            'user_pass'	 		=> $password,
            'user_email'		=> $email,
            'first_name'		=> $givenName,
            'last_name'			=> $familyName,
            'user_registered'	=> date('Y-m-d H:i:s'),
            'role'				=> 'customer'
            )
        );

        // $user = get_user_by( 'email', $email );

        // update profile image
        // update_user_meta($user->ID, 'profile_image', $imageUrl);

        return array(
            "status"=> 201, 
            "userID"=> $new_user_id, 
            "email"=> $email, 
            "password"=> $password
        ); 
    }
    else{ 
        $user = get_user_by( 'email', $email );
         // need it for jwt token 
         $bytes = openssl_random_pseudo_bytes(2);
         $password = md5(bin2hex($bytes));
         wp_set_password( $password, $user->ID);

         // update profile image
        //  update_user_meta($user->ID, 'profile_image', $imageUrl);

         return array(
             "status"=> 200, 
             "email"=> $email, 
             "password"=> $password 
         ); 
    }
}


// social login
function facebookLogin($data){
    $userLogin = sanitize_text_field($data["userLogin"]);
    $email = sanitize_text_field($data["email"]);
    $givenName = sanitize_text_field($data["name"]);
    // $imageUrl=sanitize_text_field($data["imageUrl"]);
    
    $bytes = openssl_random_pseudo_bytes(2);
    $password = md5(bin2hex($bytes));
    if(!username_exists($userLogin)){ 
        $new_user_id = wp_insert_user(array(
            'user_login'		=> $userLogin,
            'user_pass'	 		=> $password,
            'first_name'		=> $givenName,
            'user_email'        => $email, 
            'user_registered'	=> date('Y-m-d H:i:s'),
            'role'				=> 'customer'
            )
        );

        // $user = get_user_by( 'email', $email );

        // update profile image
        // update_user_meta($user->ID, 'profile_image', $imageUrl);

        return array(
            "status"=> 201, 
            "username"=> $new_user_id, 
            "password"=> $password
        ); 
    }
    else{ 
        $user = get_user_by( 'login', $userLogin );
         // need it for jwt token 
         $bytes = openssl_random_pseudo_bytes(2);
         $password = md5(bin2hex($bytes));
         wp_set_password( $password, $user->ID);

         // update profile image
        //  update_user_meta($user->ID, 'profile_image', $imageUrl);

         return array(
             "status"=> 200, 
             "username"=> $userLogin, 
             "password"=> $password 
         ); 
    }
}