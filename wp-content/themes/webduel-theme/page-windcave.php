<?php
 $requestPayload = file_get_contents("php://input"); 
 $object = json_decode($requestPayload); 

    $sessionID = $object->sessionID; 

      // setting up environment variables 
       $sessionUrl = "https://sec.windcave.com/api/v1/sessions/"; 
       $authKey = "test";
         if(get_site_url() === "http://localhost/inspiry" || get_site_url()==="https://test.webduel.co.nz"){ 
            $sessionUrl = "https://uat.windcave.com/api/v1/sessions"; 
            $authKey = "Basic SW5zcGlyeV9SZXN0OmI0NGFiMjZmOWFkNzIwNDQ4OTc0MGQ1YWM3NmE5YzE2ZDgzNDJmODUwYTRlYjQ1NTc1NmRiNDgyYjFiYWVjMjk="; 
         }
         else{ 
            $sessionUrl = "https://sec.windcave.com/api/v1/sessions"; 
            $authKey = "Basic SW5zcGlyeUxQOmRkYzdhZDg2ZDQ0NDA3NDk3OTNkZWM1OWU5YTk1MmI4ODU3ODlkM2Q0OGE2MzliODMwZWI0OTJhNjAyYmNhNjM=";
         }

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
   
   
       $newValue = $sessionObj->transactions[0]->authorised; 
       if($newValue){
           echo "true";
       }
       else{ 
            echo $sessionObj->transactions[0]->responseText; ; 
       }
  ?>
