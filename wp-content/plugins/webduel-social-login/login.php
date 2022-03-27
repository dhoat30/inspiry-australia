<?php
/**
 * Plugin Name: Webduel Social Login
 * Description: This plugin will add a Login with Google 
 * Plugin URI: https://webduel.co.nz
 * Author: Gurpreet Singh Dhoat
 * Version: 0.0.1
**/
//* Don't access this file directly
defined( 'ABSPATH' ) or die();


/**
 * Google App Configuration
 **/
// call sdk library
require_once 'google-api/vendor/autoload.php';

$gClient = new Google_Client();
$gClient->setClientId("207300494956-e1ibbehpkc7avmvcak2f095tpaqtpbkk.apps.googleusercontent.com");
$gClient->setClientSecret("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");
$gClient->setApplicationName("Inspiry Australia Local & Live");
$gClient->setState($_GET['redirect-link']);
$gClient->setRedirectUri(get_site_url()."/wp-admin/admin-ajax.php?action=vm_login_google");
 

$gClient->addScope("https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/userinfo.email");

// login URL
$login_url = $gClient->createAuthUrl();


// google login 
// add ajax action

function vm_login_google(){
	global $wp;
    // echo "fffff";
    global $gClient;
    // checking for google code
    if (isset($_GET['code'])) {
        $token = $gClient->fetchAccessTokenWithAuthCode($_GET['code']);
        var_dump($token);
    
        if(!isset($token["error"])){
            // get data from google
            $oAuth = new Google_Service_Oauth2($gClient);
            $userData = $oAuth->userinfo_v2_me->get();
        }

        // check if user email already registered
        if(!email_exists($userData['email'])){
            // generate password
            $bytes = openssl_random_pseudo_bytes(2);
            $password = md5(bin2hex($bytes));
            $user_login = $userData['id'];

            $new_user_id = wp_insert_user(array(
                'user_login'		=> $user_login,
                'user_pass'	 		=> $password,
                'user_email'		=> $userData['email'],
                'first_name'		=> $userData['givenName'],
                'last_name'			=> $userData['familyName'],
                'user_registered'	=> date('Y-m-d H:i:s'),
                'role'				=> 'customer'
                )
            );

            // get jwt token
            jwtGoogleToken($userData['email'], $password); 

            if($new_user_id) {
                // send an email to the admin
                wp_new_user_notification($new_user_id);
                
                // log the new user in
                do_action('wp_login', $user_login, $userData['email']);
                wp_set_current_user($new_user_id);
                wp_set_auth_cookie($new_user_id, true);
                
                // send the newly created user to the home page after login
                // get the parameter from the url 
                wp_redirect($_GET['state']);
				exit;
            }
        }else{
            //if user already registered than we are just loggin in the user
            $user = get_user_by( 'email', $userData['email'] );

            // need it for jwt token 
            $bytes = openssl_random_pseudo_bytes(2);
            $password = md5(bin2hex($bytes));
            wp_set_password( $password, $user->ID);

            // get jwt token
            jwtGoogleToken($userData['email'], $password); 

            do_action('wp_login', $user->user_login, $user->user_email);
            wp_set_current_user($user->ID);
            wp_set_auth_cookie($user->ID, true);
                wp_redirect($_GET['state']);
			exit;
        }
        var_dump($userData);
    }else{
         wp_redirect($_GET['state']);
        exit;
    }
}

// ALLOW LOGGED OUT users to access admin-ajax.php action
function add_google_ajax_actions(){
    add_action('wp_ajax_nopriv_vm_login_google', 'vm_login_google');
}
add_action('admin_init', 'add_google_ajax_actions');


// get jwt auth token 
function jwtGoogleToken($username, $password){
	unset($_COOKIE['inpiryAuthToken']);
	// curl request for jwt token 
	$curl = curl_init();
	$postData = [ "username"=> $username, 
	"password"=> $password
			];
		curl_setopt_array($curl, array(
            CURLOPT_URL => get_site_url().'/wp-json/jwt-auth/v1/token',
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
		print_r($obj->token);
		// sett auth cookie 
		setcookie("inpiryAuthToken", $obj->token, time() + (86400 * 30), "/"); // 86400 = 1 day
}


  // add login button on woocommerce login page
  
add_action('webduel_social_login', function(){ 
    global $login_url;
    $btnContent = '
        <style>
        .googleBtn {
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 7px;
            width: 100%;
            margin: 5px 0;
           border: 1px solid var(--black); 
            font-family: var(--openSans); 

          }
          .googleBtn svg {
            margin-right: 5px;
          }
        </style>
    ';
    if(!is_user_logged_in()){
            // checking to see if the registration is opend
            if(!get_option('users_can_register')){
                echo ($btnContent . 'Registration is closed!');
            }else{
                echo $btnContent . '<a class="googleBtn" href="'.$login_url.'"><svg aria-hidden="true" class="native svg-icon iconGoogle" width="18" height="18" viewBox="0 0 18 18"><path d="M16.51 8H8.98v3h4.3c-.18 1-.74 1.48-1.6 2.04v2.01h2.6a7.8 7.8 0 0 0 2.38-5.88c0-.57-.05-.66-.15-1.18Z" fill="#4285F4"></path><path d="M8.98 17c2.16 0 3.97-.72 5.3-1.94l-2.6-2a4.8 4.8 0 0 1-7.18-2.54H1.83v2.07A8 8 0 0 0 8.98 17Z" fill="#34A853"></path><path d="M4.5 10.52a4.8 4.8 0 0 1 0-3.04V5.41H1.83a8 8 0 0 0 0 7.18l2.67-2.07Z" fill="#FBBC05"></path><path d="M8.98 4.18c1.17 0 2.23.4 3.06 1.2l2.3-2.3A8 8 0 0 0 1.83 5.4L4.5 7.49a4.77 4.77 0 0 1 4.48-3.3Z" fill="#EA4335"></path></svg>Login With Google</a>';
            }

    }else{
        $current_user = wp_get_current_user();
        echo $btnContent . '<div class="googleBtn">Hi, ' . $current_user->first_name . '! - <a href="/wp-login.php?action=logout">Log Out</a></div>';
    }
 
  }, 10); 