<?php 
    $requestPayload = file_get_contents("php://input"); 
    $object = json_decode($requestPayload); 



		$name = test_input($object->name);
		$email = test_input($object->email);
        $phone = test_input($object->phone);
		$enquiry = test_input($object->enquiry);
        $productID = test_input($object->productID); 
        $productName = test_input($object->productName); 
        $newsletter = test_input($object->newsletter); 

        $formName = "Enquiry Form"; 

            $name = "\n Name: $name";
            $headers = 'From: ' . $email;
            $email = "\n Email: $email"; 
            $enquiry = " \n enquiry: $enquiry"; 
            $phone = " \n Phone: $phone";
            if($productID && $productName && $newsletter){
                $productName =  " \n Product Name: $productName";
                $productID =  " \n Product ID: $productID";
                $newsletter =  " \n Opt-in Newsletter: $newsletter";
            }

        function test_input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
          }

        $msg="Inspiry $formName \n\n $name $email $phone $enquiry $newsletter $productID $productName";

        echo($msg);
       
        // $to='hello@inspiry.co.nz';
        $to = "dhoat30@gmail.com";
        $sub=$formName;
        mail($to,$sub,$msg, $headers);

?>