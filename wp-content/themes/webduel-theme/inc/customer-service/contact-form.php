<?php 
// design board short code
function contact_form_webduel(){ 
   
    // contact page query
    $contactQuery = array(
        'pagename' => 'contact',
        'posts_per_page'=> 1, 
    );
    $contact = new WP_Query( $contactQuery );
    $enquiryTermsArr = array(); 
    while($contact->have_posts()){ 
        $contact->the_post(); 
            $phoneNumber = get_field('phone_number'); 
            $emailAddress = get_field('email_address'); 
            if(have_rows('enquiry_terms')){ 
                while(have_rows('enquiry_terms')){ 
                    the_row(); 
                    array_push($enquiryTermsArr, get_sub_field('term')); 
                }
            }
    }
    wp_reset_postdata();
 
    ob_start(); 
    ?> 

    <div class="contact-form-container"> 
        <form id="contact-form" >
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
                    <label for="Enquiry About">Enquiry About</label> 
                    <select name="enquiry-term" id="enquiry-term">
                        <?php 
                         foreach ($enquiryTermsArr as $term) {
                            ?>
                              <option value=<?php echo $term?>><?php echo $term?></option>
                            <?php
                        }
                          
                        ?> 
                    </select>
                </div>
                <div class="label-container">
                    <label for="Message">Message</label> 
                    <textarea name="message" id="message" ></textarea> 
                </div>
            <button class="primary-button">Send</button>
        </form>
        <div class="vertical-border"></div>
        <div class="contact-card">
          
            <div>
                <a href="tel:<?php echo $phoneNumber;?>">
                    <span class="icon-container">
                        <i class="fas fa-phone-alt"></i> 
                    </span>
                    <span><?php echo $phoneNumber;?></span>
                </a>
            </div>
            <div>
                <a target="_blank" href="mailto:<?php echo $emailAddress;?>">
                    <span class="icon-container">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <span><?php echo $emailAddress;?></span>
                </a>
            </div>
        </div>
    </div>
    <?php return ob_get_clean(); 
}

// design board button shortcode
add_shortcode('contact_form_webduel_code', 'contact_form_webduel'); 