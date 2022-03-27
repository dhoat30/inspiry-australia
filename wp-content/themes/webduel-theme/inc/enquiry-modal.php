<?php

add_action('woocommerce_single_product_summary', 'add_enquiry_button', 38);

function add_enquiry_button(){
    echo '<a href="#" class="enquire-button" id="enquire-button"><i class="fal fa-info-circle"></i> Enquire Now</a>'; 
}
// add a enquiry modal 
    add_action('woocommerce_after_main_content', 'enquiry_button_modal'); 

    function enquiry_button_modal(){
        global $product; 
        if(is_product()){ 
            echo '<div class="enquiry-form-section">
            <div class="enquiry-modal-container">
                
                <div class="form-container">
                    <i class="fal fa-times"></i>
                    <div class="large-font-size regular center-align upper-case">
                        Interested to know more? 
                    </div>
                    <div class="paragraph-font-size thin center-align poppins-font margin-elements">
                        Please fill in the form and one of our design consultants will respond to your enquiry as quickly as possible.
                    </div>
                    <div class="form">
                        <form id="enquiry-form" data-name="';
                        echo $product->get_name();
                        echo '" data-id="';
                        echo $product->get_id(); 
    
                        echo'"> 
                            <div class="name-container">
                                <input type="text" placeholder="First Name" id="name"  name="name" required>
                                <input type="text" placeholder="Last Name" id="last-name"  name="last-name" required>
                            </div>
                            <input type="email" placeholder="Email" id="email" name="email" required>
                            <input type="phone" placeholder="Phone" id="phone" name="phone" required>
                            <textarea id="enquiry" name="enquiry" placeholder="Enquiry"></textarea> 
                            <div class="checkbox-container">
                                <input class="checkbox" type="checkbox" id="newsletter" name="newsletter" value="No" >
                                <label for="newsletter" class="paragraph-font-size thin"> Receive the latest news, events and special offers from Inspiry.</label>
                            </div>
                            <button class="primary-button">Send</button>
                            
                        </form>
                    </div>
                </div>
    
                <div class="product-container beige-color-bc flex-center flex-column align-center">
                    <img src="';
                    
                    echo get_the_post_thumbnail_url($product->get_id(), 'large');
                    echo  '" alt="';
                    echo  $product->get_name();
                    echo '">';
                    echo '<div class="column-font-size center-align regular dark-grey margin-elements">'; 
                     echo $product->get_name();
                     echo '</div>
                    <div class="section-font-size center-align regular">$';
                     echo $product->get_price();
                     echo '</div>
                </div>
              
            </div>
           
        </div>';
        }
      
  
    }
?>
