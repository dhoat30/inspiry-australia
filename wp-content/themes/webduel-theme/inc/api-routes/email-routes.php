<?php
//routes

add_action("rest_api_init", "email_route");

function email_route() {
    // send email to trade professional
    register_rest_route("inspiry/v1/", "professional-email", array(
        "methods" => "POST",
        "callback" => "professionalEmail"
    ));
	
	 // send email for join trade form
    register_rest_route("inspiry/v1/", "join-trade-email", array(
        "methods" => "POST",
        "callback" => "joinTradeEmail"
    ));

		 // send email for join trade form
		 register_rest_route("inspiry/v1/", "enquiry-email", array(
			"methods" => "POST",
			"callback" => "enquiryEmail"
		));

		// contact form email
		register_rest_route("inspiry/v1/", "contact", array(
			"methods" => "POST",
			"callback" => "contactEmail"
		));

		// contact form email
		register_rest_route("inspiry/v1/", "feedback-email", array(
			"methods" => "POST",
			"callback" => "feedbackEmail"
		));

		// contact form email
		register_rest_route("inspiry/v1/", "windcave-success", array(
			"methods" => "GET",
			"callback" => "wincaveSuccess"
		));

		// contact form email
		register_rest_route("inspiry/v1/", "windcave-fail", array(
			"methods" => "GET",
			"callback" => "windcaveFail"
		));
}
function wincaveSuccess($data){ 
		$boardName = 'testing boards';
		$publishStatus =  "private";

		   wp_insert_post(array(
			  "post_type" => "boards", 
			  "post_status" => $publishStatus, 
			  "post_title" => $data['username'],
			  'post_content' => $data['sessionId']
	   )); 
	  
	return 200; 
}

function windcaveFail(){ 
	wp_redirect('https://localhost/product-category/furniture/dining-kitchen-furniture/'); 
	exit;
	return 200; 
}
// send email to trade professional 
function professionalEmail($data) {
    $name = sanitize_text_field($data["name"]);
    $email = sanitize_text_field($data["email"]);
    $phone = sanitize_text_field($data["phone"]);
    $message = sanitize_text_field($data["message"]);
	$emailTo=sanitize_text_field($data["emailTo"]);
    $formName = "Enquiry Form";

		$name = "\n Name: $name";
		$headers = 'From: '.$email;
		$email = "\n Email: $email";
		$message = " \n Message: $message";
		$phone = " \n Phone: $phone";


		$msg = "Inspiry $formName \n\n $name $email $phone $message";

		$to = $emailTo;
		$sub = $formName;

			// send email using mailgun  
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
			curl_setopt($ch, CURLOPT_USERPWD, 'api:c3c540d8a3fa918836a8291fbdbf64d6-dbdfb8ff-66cfb2a5');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_URL, 
				'https://api.mailgun.net/v2/inspiry.co.nz/messages');
			curl_setopt($ch, CURLOPT_POSTFIELDS, 
				  array('from' => $headers,
						'to' => $to,
						'subject' => $sub,
						'text' => $msg));
			$result = curl_exec($ch);
			curl_close($ch);
			return $result;
		}

// send data of join trade form
function joinTradeEmail($data) {
    $name = sanitize_text_field($data["name"]);
    $email = sanitize_text_field($data["email"]);
    $phone = sanitize_text_field($data["phone"]);
	$company= sanitize_text_field($data["company"]);
	$website = sanitize_text_field($data["website"]);
    $message = sanitize_text_field($data["message"]);
	$emailTo=sanitize_text_field($data["emailTo"]);
	
    $formName = "Join Trade Form";

		$name = "\n Name: $name";
		$headers = 'From: '.$email;
		$email = "\n Email: $email";
		$phone = " \n Phone: $phone";
		$company = "\n Company: $company";
		$website = "\n Website: $website";
		$message = " \n Message: $message";
		


		$msg = "Inspiry $formName \n\n $name $email $phone $company $website $message";

		$to = $emailTo;
		$sub = $formName;
			// send email using mailgun  
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, 'api:c3c540d8a3fa918836a8291fbdbf64d6-dbdfb8ff-66cfb2a5');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_URL, 
			'https://api.mailgun.net/v2/inspiry.co.nz/messages');
		curl_setopt($ch, CURLOPT_POSTFIELDS, 
			  array('from' => $headers,
					'to' => $to,
					'subject' => $sub,
					'text' => $msg));
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
		}

	
// send data of enquiry form
function enquiryEmail($data) {
    $firstName = sanitize_text_field($data["name"]);
	$lastName = sanitize_text_field($data["last-name"]);
    $email = sanitize_text_field($data["email"]);
    $phone = sanitize_text_field($data["phone"]);
	$enquiry= sanitize_text_field($data["enquiry"]);
	$productID = sanitize_text_field($data["productID"]);
    $productName = sanitize_text_field($data["productName"]);
	$newsletter = sanitize_text_field($data["newsletter"]); 
	$emailTo=sanitize_text_field($data["emailTo"]);
	
    $formName = "Product Enquiry Form";

		$headers = 'From: '.$email;
		$firstName = "\n First Name: $firstName";
		$lastName = "\n Last Name: $lastName";
		$email = "\n Email: $email";
		$phone = " \n Phone: $phone";
		$enquiry = "\n enquiry: $enquiry";
		$productID = "\n Product ID: $productID";
		$productName = " \n Product Name: $productName";
		$newsletter = "\n Would you like to subscribe mailing list?: $newsletter";

		$msg = "Inspiry $formName \n\n $firstName $lastName $email $phone $enquiry $productID $productName $newsletter";

		$to = $emailTo;
		$sub = $formName;

		// send email using mailgun  
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, 'api:c3c540d8a3fa918836a8291fbdbf64d6-dbdfb8ff-66cfb2a5');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_URL, 
			'https://api.mailgun.net/v2/inspiry.co.nz/messages');
		curl_setopt($ch, CURLOPT_POSTFIELDS, 
			  array('from' => $headers,
					'to' => $to,
					'subject' => $sub,
					'text' => $msg));
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
		
		}


// contact email 
function contactEmail($data) {
    $firstName = sanitize_text_field($data["firstName"]);
	$lastName = sanitize_text_field($data["lastName"]);
	$email = sanitize_text_field($data["email"]);
	$phone = sanitize_text_field($data["phone"]);
	$enquiry= sanitize_text_field($data["enquiry"]);
	$message = sanitize_text_field($data["message"]);

	$emailTo=sanitize_text_field($data["emailTo"]);
	
    $formName = "Contact Us Form";

		$headers = 'From: '.$email;
		$firstName = "\n First Name: $firstName";
		$lastName = "\n Last Name: $lastName";
		$email = "\n Email: $email";
		$phone = " \n Phone: $phone";
		$enquiry = "\n Enquiry: $enquiry";
		$message = "\n Message: $message";

		$msg = "Inspiry $formName \n\n $firstName $lastName $email $phone $enquiry $message";

		$to = $emailTo;
		$sub = $formName;
		
		// send data to mailgun 
		sendDataToMailgun($headers, $to, $sub, $msg); 
		
		}

		// feedback email 
function feedbackEmail($data) {
    $firstName = sanitize_text_field($data["firstName"]);
	$lastName = sanitize_text_field($data["lastName"]);
	$email = sanitize_text_field($data["email"]);
	$phone = sanitize_text_field($data["phone"]);
	$feedback = sanitize_text_field($data["feedback"]);

	$emailTo=sanitize_text_field($data["emailTo"]);
	
    $formName = "Feedback Form";

		$headers = 'From: '.$email;
		$firstName = "\n First Name: $firstName";
		$lastName = "\n Last Name: $lastName";
		$email = "\n Email: $email";
		$phone = " \n Phone: $phone";
		$feedback = "\n Feedback: $feedback";

		$msg = "Inspiry $formName \n\n $firstName $lastName $email $phone $feedback";

		$to = $emailTo;
		$sub = $formName;
		
		// send data to mailgun 
		sendDataToMailgun($headers, $to, $sub, $msg); 
		
		}



		// send data to mailgun 
		function sendDataToMailgun($headers, $to, $sub, $msg){ 
			// send email using mailgun  
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, 'api:c3c540d8a3fa918836a8291fbdbf64d6-dbdfb8ff-66cfb2a5');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_URL, 
			'https://api.mailgun.net/v2/inspiry.co.nz/messages');
		curl_setopt($ch, CURLOPT_POSTFIELDS, 
			  array('from' => $headers,
					'to' => $to,
					'subject' => $sub,
					'text' => $msg));
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
		}