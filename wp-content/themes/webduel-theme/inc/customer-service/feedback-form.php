<?php 
// design board short code
function feedback_form_webduel(){ 
   
 
 
    ob_start(); 
    ?> 

    <div class="contact-form-container"> 
        <form id="feedback-form" >
            <div class="flex" >
                <div class="label-container">
                    <label for="First Name">First Name*</label> 
                    <input type="text" name="first-name" id="first-name" required/> 
                </div>
                <div class="label-container">
                    <label for="Last Name">Last Name*</label> 
                    <input type="text" name="last-name" id="last-name" required/> 
                </div>
            </div>
                <div class="label-container">
                    <label for="Email">Email*</label> 
                    <input type="email" name="email" id="email" required/> 
                </div>
                <div class="label-container">
                    <label for="Phone Number">Phone Number</label> 
                    <input type="tel" name="phone-number" id="phone-number" /> 
                </div>
               
                <div class="label-container">
                    <label for="Feedback">Feedback</label> 
                    <textarea name="feedback" id="feedback" ></textarea> 
                </div>
            <button class="primary-button">Send</button>
        </form>
      
    </div>
    <?php return ob_get_clean(); 
}

// design board button shortcode
add_shortcode('feedback_form_webduel_code', 'feedback_form_webduel'); 