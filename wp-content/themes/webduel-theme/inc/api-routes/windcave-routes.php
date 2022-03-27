<?php
//routes

add_action("rest_api_init", "windcave_routes");

function windcave_routes() {
  	// 	create windcave session
    register_rest_route("inspiry/v1/", "windcave-session", array(
      "methods" => "POST",
      "callback" => "createWindcaveSession"
    ));
  
		// 	get trade post using category slug
		  register_rest_route("inspiry/v1/", "windcave-session-status", array(
				"methods" => "POST",
				"callback" => "windcaveSessionStatus"
			));
		
}
function createWindcaveSession($data){ 
    $cartTotal = sanitize_text_field($_POST['cartTotal']); 
    $firstName = sanitize_text_field($_POST['firstName']); 
    $lastName = sanitize_text_field($_POST['lastName']); 
    $emailAddress = sanitize_email($_POST['emailAddress']); 
    $phone = preg_replace('/[^0-9]/', '', $_POST['phone']);
    // setting up environment variables 
    $sessionUrl = ""; 
    $authKey = ""; 
    if(get_site_url() === "https://localhost"){ 
      $sessionUrl = "https://uat.windcave.com/api/v1/sessions"; 
      $authKey = "Basic SW5zcGlyeV9SZXN0OmI0NGFiMjZmOWFkNzIwNDQ4OTc0MGQ1YWM3NmE5YzE2ZDgzNDJmODUwYTRlYjQ1NTc1NmRiNDgyYjFiYWVjMjk="; 
    }
    else{ 
      $sessionUrl = "https://sec.windcave.com/api/v1/sessions"; 
      $authKey = "Basic SW5zcGlyeUxQOmRkYzdhZDg2ZDQ0NDA3NDk3OTNkZWM1OWU5YTk1MmI4ODU3ODlkM2Q0OGE2MzliODMwZWI0OTJhNjAyYmNhNjM=";
    }

      
    // https request to windcave to create a session 
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $sessionUrl);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_HEADER, FALSE);

      curl_setopt($ch, CURLOPT_POST, TRUE);

      curl_setopt($ch, CURLOPT_POSTFIELDS, "{
      \"type\": \"purchase\",
      \"methods\": [
          \"card\"
      ],
      \"amount\": \"$cartTotal\",
      \"currency\": \"NZD\",
      \"callbackUrls\": {
          \"approved\": \"https://localhost/success\",
          \"declined\": \"https://localhost/failure\"
      },
      \"customer\": {
        \"firstName\": \"$firstName\",
        \"lastName\": \"$lastName\",
        \"email\": \"$emailAddress\",
        \"phoneNumber\": \"$phone\"
      }
      }");

      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      "Content-Type: application/json",
      "Authorization:".$authKey."" 
      ));

      $response = curl_exec($ch);
      $obj = json_decode($response);
      return $obj; 
}
function windcaveSessionStatus($data){
    $sessionID = $data["sessionID"];
  
      // setting up environment variables 
      $sessionUrl = ""; 
      $authKey = ""; 
      $sessionUrl = "https://uat.windcave.com/api/v1/sessions/"; 
      $authKey = "Basic SW5zcGlyeV9SZXN0OmI0NGFiMjZmOWFkNzIwNDQ4OTc0MGQ1YWM3NmE5YzE2ZDgzNDJmODUwYTRlYjQ1NTc1NmRiNDgyYjFiYWVjMjk="; 
      // if(get_site_url() === "https://inspiryaus.com/"){ 
      //   $sessionUrl = "https://uat.windcave.com/api/v1/sessions/"; 
      //   $authKey = "Basic SW5zcGlyeV9SZXN0OmI0NGFiMjZmOWFkNzIwNDQ4OTc0MGQ1YWM3NmE5YzE2ZDgzNDJmODUwYTRlYjQ1NTc1NmRiNDgyYjFiYWVjMjk="; 
      // }
      // else{ 
      //   $sessionUrl = "https://sec.windcave.com/api/v1/sessions/"; 
      //   $authKey = "Basic SW5zcGlyeUxQOmRkYzdhZDg2ZDQ0NDA3NDk3OTNkZWM1OWU5YTk1MmI4ODU3ODlkM2Q0OGE2MzliODMwZWI0OTJhNjAyYmNhNjM=";
      // }
  
      
   $curl = curl_init();
   
   curl_setopt_array($curl, array(
       CURLOPT_URL =>$sessionUrl.$sessionID,
       CURLOPT_RETURNTRANSFER => true,
       CURLOPT_ENCODING => '',
       CURLOPT_MAXREDIRS => 10,
       CURLOPT_TIMEOUT => 0,
       CURLOPT_FOLLOWLOCATION => true,
       CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
       CURLOPT_CUSTOMREQUEST => 'GET',
       CURLOPT_HTTPHEADER => array(
       'Content-Type: application/json',
       "Authorization:".$authKey."" 
       ),
   ));
   
   $response = curl_exec($curl);
  
   curl_close($curl);
   $sessionObj = json_decode($response);
   return $sessionObj;

}
?>