<?php
//routes

add_action("rest_api_init", "mailchimp_route");

function mailchimp_route() {
    // send email to trade professional
    register_rest_route("inspiry/v1/", "enquiry-mailchimp", array(
        "methods" => "POST",
        "callback" => "sendDataToMailchimp"
    ));
	


}

// send email to trade professional 
function sendDataToMailchimp($data) {
    $firstName = sanitize_text_field($data["name"]);
    $lastName = sanitize_text_field($data["last-name"]);
    $email = sanitize_text_field($data["email"]);
    $phone = sanitize_text_field($data["phone"]);
    $newsletter = sanitize_text_field($data["newsletter"]); 

    // send mailchimp request
    $curl = curl_init();
    $status = ''; 
    if($newsletter == "Yes"){ 
        $status = "subscribed"; 
    }
    else { 
        $status = 'unsubscribed'; 
    }

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://us20.api.mailchimp.com/3.0/lists/c2a58c3d86',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{
            "members":[
                {
                    "email_address": "'.$email.'", 
                    "status": "'.$status.'", 
                    "merge_fields": { 
                        "FNAME": "'.$firstName.'", 
                        "LNAME": "'.$lastName.'", 
                        "PHONE": "'.$phone.'"
                    }
                }
            ]
        }',
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer bc70e52666c801f5049c6c4d1b19a40f-us20',
        'Content-Type: application/json'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    echo $response;
  
}

