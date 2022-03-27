<?php
//routes

add_action("rest_api_init", "auth_route");

function auth_route() {
    // send email to trade professional
    register_rest_route("inspiry/v1/", "create-user", array(
        "methods" => "POST",
        "callback" => "createUser"
    ));


	
}

// send email to trade professional 
function createUser($data) {
    $new_user_name = stripcslashes($_POST['username']);
	  $new_user_email = stripcslashes($_POST['email']);
	  $new_user_password = $_POST['password'];
	  $userFirstName = sanitize_text_field($_POST['firstName']);
      $userLastName = sanitize_text_field($_POST['lastName']);
      $newsletter = sanitize_text_field($_POST['subscribeNewsletter']); 


        
	  	 // check if user email already registered
           if(!email_exists($new_user_email)){
            // generate password
           

            $new_user_id = wp_insert_user(array(
                'user_login' => $new_user_name,
                'user_email' => $new_user_email,
                'user_pass' => $new_user_password,
                'user_nicename' => strtolower($userFirstName),
                'display_name' => $userFirstName,
                'first_name'=> $userFirstName, 
                'last_name'=> $userLastName,
                'user_registered'	=> date('Y-m-d H:i:s'),
                'role' => 'customer'
                )
            );

            // get jwt token
            // jwtTokenGoogleLogin($userData['email'], $password); 

            if($new_user_id) {
                // send an email to the admin
                wp_new_user_notification($new_user_id);
                
                // log the new user in
                do_action('wp_login', $$new_user_name, $new_user_email);
                wp_set_current_user($new_user_id);
                wp_set_auth_cookie($new_user_id, true);
                
                // send the newly created user to the home page after login
                
                wp_redirect(home_url());
				exit;
            }
        }else{
            //if user already registered than we are just loggin in the user
           return "This email has already been used."; 
			exit;
        }
}


?>