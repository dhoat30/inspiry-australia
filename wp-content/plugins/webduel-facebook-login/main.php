<?php
/**
 * Plugin Name: Webduel facebook Login
 * Description: This plugin will add a Login with Facebook
 * Plugin URI: https://webduel.co.nz
 * Author: Gurpreet Singh Dhoat
 * Version: 0.0.1
**/

// Don't access this file directly
defined( 'ABSPATH' ) or die();

// call sdk library
require_once 'Facebook/autoload.php';

session_start();
  
if (strstr($_SERVER['SERVER_NAME'], 'inspiry.com.au')) {
	$FBObject = new \Facebook\Facebook([
		'app_id' => '1078711392706961',
		'app_secret' => '594de74331c3ecc1c46e65257a16d9ba',
		'default_graph_version' => 'v2.10'
	]);
	
  } else {
	$FBObject = new \Facebook\Facebook([
		'app_id' => '662817335050339',
		'app_secret' => '104d512983d90a1e79d6f2d8b6ffea24',
		'default_graph_version' => 'v2.10'
	]);
  }

$handler = $FBObject -> getRedirectLoginHelper();



// facebook login
add_action("wp_ajax_webduel_facebook_login", "webduel_facebook_login");

function webduel_facebook_login(){
	
	global $handler, $FBObject;

	// if ( !wp_verify_nonce( $_REQUEST['nonce'], "webduel_facebook_login_nonce")) {
	// 	exit("No naughty business please");
	// }

	try {
		$accessToken = $handler->getAccessToken();
	}catch(\Facebook\Exceptions\FacebookResponseException $e){
		echo "Response Exception: " . $e->getMessage();
		exit();
	}catch(\Facebook\Exceptions\FacebookSDKException $e){
		echo "SDK Exception: " . $e->getMessage();
		exit();
	}

	if(!$accessToken){
		wp_redirect( home_url() );
		exit;
	}

	$oAuth2Client = $FBObject->getOAuth2Client();
	if(!$accessToken->isLongLived())
    	$accessToken = $oAuth2Client->getLongLivedAccesToken($accessToken);

    $response = $FBObject->get("/me?fields=id, first_name, last_name, email, picture.type(large)", $accessToken);
	$userData = $response->getGraphNode()->asArray();
 
	$user_email = $userData['email'];
	// check if user email already registered
	if(!email_exists($user_email)) {

		// generate password
		$bytes = openssl_random_pseudo_bytes(2);
		$password = md5(bin2hex($bytes));
		$user_login = strtolower($userData['first_name'].$userData['last_name']);
		

		$new_user_id = wp_insert_user(array(
			'user_login'		=> $user_login,
			'user_pass'	 		=> $password,
			'user_email'		=> $user_email,
			'first_name'		=> $userData['first_name'],
			'last_name'			=> $userData['last_name'],
			'user_registered'	=> date('Y-m-d H:i:s'),
			'role'				=> 'subscriber'
			)
		);
		 // get jwt token
		 jwtTokenFbLogin($userData['email'], $password); 
		if($new_user_id) {
			// send an email to the admin
			wp_new_user_notification($new_user_id);
			
			// log the new user in
			do_action('wp_login', $user_login, $user_email);
			wp_set_current_user($new_user_id);
			wp_set_auth_cookie($new_user_id, true);
			
			// send the newly created user to the home page after login
			wp_redirect(home_url()); exit;
		}
	}else{
		//if user already registered than we are just loggin in the user
		$user = get_user_by( 'email', $user_email );

			// need it for jwt token 
			$bytes = openssl_random_pseudo_bytes(2);
			$password = md5(bin2hex($bytes));
			wp_set_password( $password, $user->ID);
		  // get jwt token
		  jwtTokenFbLogin($userData['email'], $password); 
		do_action('wp_login', $user->user_login, $user->user_email);
		wp_set_current_user($user->ID);
		wp_set_auth_cookie($user->ID, true);
		// wp_redirect($_GET['redirect-link']); 

		wp_redirect(home_url()); 
		exit;
	}
}

// get jwt auth token 
function jwtTokenFbLogin($username, $password){
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

// allow logged out users to access admin-ajax.php action
function add_ajax_actions() {
    add_action( 'wp_ajax_nopriv_webduel_facebook_login', 'webduel_facebook_login' );
}

add_action( 'admin_init', 'add_ajax_actions' );


// button action hook on sign in and create account page
 add_action('webduel_social_login', function(){ 
	$btnContent = '
	<style>
	.fbBtn {
		text-decoration: none;
		display: flex;
		align-items: center;
		justify-content: center;
		padding: 7px;
		width: 100%;
		margin: 5px 0;
		background: #385499; 
		color: white; 
		font-family: var(--openSans); 
	  }
	  .fbBtn svg {
		margin-right: 5px;
		color: white; 
	  }
	  .fbBtn path {
		fill: white; 
	  }
	</style>
	';
// https://inspiry.co.nz/wp-admin/admin-ajax.php
	if(!is_user_logged_in()){
		if(!get_option('users_can_register')){
			echo ('The registrations are closed!');
		}else{
			global $handler;
			$nonce = wp_create_nonce("webduel_facebook_login_nonce");
			// $link = admin_url('admin-ajax.php?action=webduel_facebook_login&nonce='.$nonce);
			$link = get_site_url().'/wp-admin/admin-ajax.php?action=webduel_facebook_login';
			// $link = "https://".$_SERVER['SERVER_NAME']."/wp-admin/admin-ajax.php?action=webduel_facebook_login";

			$redirect_to = $link;
			$data = ["email"];
			$fullURL = $handler->getLoginURL($redirect_to, $data);
			echo $btnContent.'
				<a class="fbBtn" id="fbBtn" href="'. $fullURL .'"><svg aria-hidden="true" class="svg-icon iconFacebook" width="18" height="18" viewBox="0 0 18 18"><path d="M3 1a2 2 0 0 0-2 2v12c0 1.1.9 2 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2H3Zm6.55 16v-6.2H7.46V8.4h2.09V6.61c0-2.07 1.26-3.2 3.1-3.2.88 0 1.64.07 1.87.1v2.16h-1.29c-1 0-1.19.48-1.19 1.18V8.4h2.39l-.31 2.42h-2.08V17h-2.5Z" fill="#4167B2"></path></svg> Login With Facebook</a>
			';
		}
	}else{
		$current_user = wp_get_current_user();
		echo  'Hi ' . $current_user->first_name . '! - <a href="/wp-login.php?action=logout">Log Out</a>';
	}
  }, 20); 