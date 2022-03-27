<?php 
add_action('wp_ajax_register_user_front_end', 'register_user_front_end', 0);
add_action('wp_ajax_nopriv_register_user_front_end', 'register_user_front_end');
function register_user_front_end() {
	  $new_user_name = stripcslashes($_POST['username']);
	  $new_user_email = stripcslashes($_POST['email']);
	  $new_user_password = $_POST['password'];
	  $userFirstName = sanitize_text_field($_POST['firstName']);
      $userLastName = sanitize_text_field($_POST['lastName']);
      $newsletter = sanitize_text_field($_POST['subscribeNewsletter']); 


	  $user_data = array(
	      'user_login' => $new_user_name,
	      'user_email' => $new_user_email,
	      'user_pass' => $new_user_password,
	      'user_nicename' => strtolower($userFirstName),
	      'display_name' => $userFirstName,
          'first_name'=> $userFirstName, 
          'last_name'=> $userLastName,
          'user_registered'	=> date('Y-m-d H:i:s'),
	      'role' => 'customer'
	  	);
	  $user_id = wp_insert_user($user_data);
	  	if (!is_wp_error($user_id)) {
	        
              // get jwt token
            jwtTokenLogin($new_user_email, $new_user_password); 
            // send an email to the admin
            wp_new_user_notification($user_id);
              // log the new user in
              do_action('wp_login', $new_user_name, $new_user_email);
              wp_set_current_user($user_id);
              wp_set_auth_cookie($user_id, true);
              
              // send the newly created user to the home page after login
              echo json_encode(array('created'=>true, 'message'=>__('We have Created an account for you.')));
       
              exit;

	  	} else {
	    	if (isset($user_id->errors['empty_user_login'])) {
	          $notice_key = 'User Name and Email are mandatory';
	          echo json_encode(array('created'=>false, 'message'=>__($notice_key)));
	      	} elseif (isset($user_id->errors['existing_user_login'])) {
                echo json_encode(array('created'=>false, 'message'=>__('User name already exists.')));
	      	} else {
               echo json_encode(array('created'=>false, 'message'=>__('Error Occurred please fill up the sign up form carefully.')));
	        
	      	}
	  	}
	die;
}


// // get jwt auth token 
function jwtTokenLogin($username, $password){
    unset($_COOKIE['inpiryAuthToken']); 
    // curl request for jwt token 
    $curl = curl_init();
    $postData = [ "username"=> $username, 
    "password"=> $password
            ];
        curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://localhost/inspirynew/wp-json/jwt-auth/v1/token',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($postData),
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $obj = json_decode($response);
   
        // sett auth cookie 
        setcookie("inpiryAuthToken", $obj->token, time() + (86400 * 30), "/"); // 86400 = 1 day
}