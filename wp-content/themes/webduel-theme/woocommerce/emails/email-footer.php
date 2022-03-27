<?php
/**
 * Email Footer
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-footer.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;
?>
															</div>
															</div>
    </div>
	 <!-- social media -->
	 <div class="social-container max-width padding">
        <div class="title playfair-fonts">Get social with us</div>
        <div class="icons">
          <?php 
           $argsContact = array(
            'pagename' => 'contact'
          );
          $queryContact = new WP_Query( $argsContact );
          while($queryContact->have_posts()){
            $queryContact->the_post(); 
          ?>
          <a class="social-icon" href="<?php echo get_field("facebook");?>" target="_blank"><img src="https://inspiry.co.nz/wp-content/uploads/2021/05/facebook.png" alt="Facebook Link"></a>
          <a class="social-icon" href="<?php echo get_field("instagram");?>" target="_blank"><img src="https://inspiry.co.nz/wp-content/uploads/2021/05/instagram.png" alt="Instagram Link"></a>
          <a class="social-icon" href="<?php echo get_field("pintrest_");?>" target="_blank"><img src="https://inspiry.co.nz/wp-content/uploads/2021/05/pinterest-social-logo.png" alt="Pinterest Link"></a>
          <a class="social-icon" href="<?php echo get_field("youtube");?>" target="_blank"><img src="https://inspiry.co.nz/wp-content/uploads/2021/05/youtube.png" alt="Youtube Link"></a>
          <?php 
          
          }
          wp_reset_postdata(  );
          ?>
        </div>
    </div>
    <div class="footer max-width padding">
        <div class="playfair-fonts">Need help with your order? Please <a href="https://inspiry.co.nz/contact" target="_blank"> contact us</a>.</div>
    </div>
</section>	
<script src="https://kit.fontawesome.com/f3cb7ab01f.js"></script>
</body>
</html>
