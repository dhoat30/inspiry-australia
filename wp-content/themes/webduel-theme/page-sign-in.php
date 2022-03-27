<?php 
get_header(); 
?>

<div class="login-page">
    <div class="background-image" style='background: url("<?php echo get_the_post_thumbnail_url(null,"large"); ?>")'>
        <div class="content">
            <div class="login-container">
                <h1 class="title">Sign In</h1>
                <form id="login" action="login" method="post">
                        <p class="status"></p>
                        <label for="username">Username*</label>
                        <input id="username" type="text" name="username" required>
                        <label for="password">Password*</label>
                        <input id="password" type="password" name="password" required>
                        
                        <div class="flex">
                            <button class="primary-button" type="submit">SIGN IN</button>
                            <?php 
                            if(has_action('webduel_social_login')){ 
                                ?>
                                <div class="divider">Or</div>
                                <?php 
                            }
                            ?>
                            
                            <?php 
                             do_action('webduel_social_login');
                            ?>
                        </div>        
                        <div class="terms-flex">
                            <a class="lost" href="<?php echo wp_lostpassword_url(); ?>">Reset password</a>
                            <div class="terms"><?php echo get_the_content();?></div>
                        </div>    
                       
                        <?php wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>
                </form>
            </div>
            <div class="vertical-border" ></div>
            <div class="create-account-container">
                <h1 class="title" >Create an Account</h1>
                <div class="benefit-list">
                    <?php 
                     if(have_rows('account_benefits')){ 
                        while(have_rows('account_benefits')){ 
                            the_row(); 
 
                            ?>
                            <div class="item">
                                <div class="icon-container">
                                    <i class='<?php echo get_sub_field('benefit_icon');?>'></i>
                                </div>

                                <h2> 
                                    <?php echo get_sub_field('benefit');?> 
                                </h2>
                            </div>
                            <?php 
                        }
                    }
                    ?> 
                </div>
              
                <a class="primary-button" href='<?php echo get_field('create_account_link'); ?>?redirect-link=<?php  print_r($_GET["redirect-link"])?>'> Create Account</a>
                <h3 class="track-order">
                    Don't have an account?
                    <a  href='<?php echo get_field('track_order_link'); ?> '> Track Order</a>
                </h3>
            </div>
        </div>
    </div>
</div>

<?php get_footer();?>