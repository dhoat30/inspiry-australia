<?php 
get_header(); 

  while(have_posts()){
    the_post(); 
    ?>
    <div class="body-contaienr contact-page">
        <img class="hero-section-img" alt="Contact us" src="<?php echo  get_site_url();?>/wp-content/uploads/2020/11/CONTACT-PAGE.jpg">
        <div class="row-container contact-us-page">
            <div class="column contact-form">
                <?php the_content();?>
            </div>
            
            <div class="column contact">
                <div class="contact-info">
                    <div class="phone">
                        <a href="tel:08004677479" class="rm-txt-dec"><i class="fas fa-phone-alt"></i> 0800 INSPIRY (467 7479)
                        </a>                   
                    </div>
                    <div class="email">
                        <a href="mailto:hello@inspiry.co.nz" class="rm-txt-dec"><i class="fas fa-envelope"></i> hello@inspiry.co.nz</a>
                    </div>
                    <div class="chat">
                        <a href="#" class="rm-txt-dec"><i class="fas fa-comment-dots"></i> Live Chat</a>
                    </div>
                    <div class="business-hours">
                        <a href="#" class="rm-txt-dec"><i class="fas fa-clock"></i> Monday - Friday: 9:00am - 4:30pm </a>
                    </div>
                </div>

                <div class="social-media">
                    <h4 class="column-font-size regular">Get social with us</h4>
                    <div class="underline-dg"></div>
                    <div class="social-media-container">
                        <a href="https://www.facebook.com/inspiryliveaninspiredlife/" target="_blank"><i class="fab fa-facebook-square"></i></a>
                        <a href="" target="_blank"><i class="fab fa-instagram-square"></i></a>
                        <a href="" target="_blank"><i class="fab fa-pinterest-square"></i></a>
                        <a href="" target="_blank"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
        </div>
    <div>

    <!-- USP --> 
    <section class="trade-usp">
        <div>
            <i class="fas fa-user-hard-hat"></i>
            <div class="poppins-font medium-font-size center-align">
                Inspiry Trade connects you with your target audience
            </div>
        </div>
        <div>
        <i class="fas fa-clipboard-list-check"></i>
                    <div class="poppins-font medium-font-size center-align">
                Be listed on our Trade directory promoting your business and service
            </div>
        </div>
        <div>
            <i class="fab fa-google"></i>
                    <div class="poppins-font medium-font-size center-align">
                Quality traffic from Google to your projects and listing
            </div>
        </div>
        <div>
            <i class="fas fa-chart-network"></i>            
            <div class="poppins-font medium-font-size center-align">
                Become part of our collaborative hub connecting you with our viewers
            </div>
        </div>
    </section>
    
    <section class="row-container">
        <div class="section-font-size center-align">Testimonials</div>
        <div class="testimonials">
            <div>
                    <i class="fas fa-quote-left"></i>
                        <div class="poppins-font paragraph-font-size">
                        The Inspiry Trades Directory is the ideal place for Trades Professionals to come together to grow their business and share inspirational ideas. It's the perfect platform for those wanting to expand their business opportunities and reach potential new clients and customers. The Trades Directory allows registered users to promote their work and different projects that can be explored by other trades people as well as the general public.            
            
                    <br><br>
                    Listed projects will allow users to access information and contact details about the professionals that worked on the project. Users of the Trades directory will be in full control of their profile and uploaded content, which allows users to showcase their services and projects how they choose.


                    </div>
    </div>
            <div>
        
            <i class="fas fa-quote-left"></i>
                        <div class="poppins-font paragraph-font-size">
                        The Inspiry Trades Directory is your doorway to opening up a world of opportunities for your business. With the ability to save and share your favorite projects and ideas with family and friends over social media, users of the Trades Directory will be able reap the benefits of this exposure which can promote the growth of their business and expand their client base. 
                <br><br>
                Trades Professionals will have the ability to showcase their work and services, their way, on a comprehensive and easy to use platform shared by other like minded professionals. 

                    </div>
            </div>
            <div>
            <i class="fas fa-quote-left"></i>
                <div class="poppins-font paragraph-font-size">
                Promote your services, projects, and latest products to a virtual community of Trades Professionals and My Inspiry users. The Trades Directory allows users to browse through a variety of work from a large spectrum of professionals and is open to home builders, architects,  interior and kitchen designers, kitchen and cabinet makers, renovators, landscape contractors & gardeners, electricians, and plumbers.
                <br><br>   
                Expose your services and innovative concepts to a world of loyal customers and clients who are looking for top tier professionals and inspiring ideas to fulfil their design dreams. 
            </div>
            </div>
        </div>
      
    </section>
    <?php
}

get_footer();
?>

