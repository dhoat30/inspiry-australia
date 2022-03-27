<?php get_header(); ?> 

<div class="create-account-page">
    <div class="background-image" style='background: url("<?php echo get_the_post_thumbnail_url(null,"large"); ?>")'>
        <div class="content">
            <div class="create-account-container">
                <h1 class="title" >Create an Account</h1>
                <form id="create-account" action="create-account" method="post">
                        <p class="status"></p>

                        <div class="flex name-container">
                            <div class="input-item">
                                <label for="first-name">First Name*</label>
                                <input id="first-name" type="text" name="first-name" required>
                            </div>
                            <div class="input-item">
                                <label for="last-name">Last Name*</label>
                                <input id="last-name" type="text" name="last-name" required>
                            </div>
                        </div>

                        <label for="email">Email Address*</label>
                        <input id="email" type="text" name="email" required>
                        <label for="username">Username*</label>
                        <input id="username" type="text" name="username" required>
                        <label for="password">Password*</label>
                        <input id="password" type="password" name="password" required>
                        <!-- <div class="checkbox-container" >
                            <input type="checkbox" id="newsletter" name="newsletter" >
                            <label for="newsletter"> Receive the fortnightly newsletter from Inspiry.</label>
                        </div> -->
                        <div class="flex">
                            <button type="submit" class="primary-button" >CREATE ACCOUNT</button>
                            <?php 
                            if(has_action('webduel_social_login')){ 
                                ?>
                                <div class="divider">Or</div>
                                <?php 
                            }
                            ?>
                            <?php 
                            has_action('webduel_social_login');
                             do_action('webduel_social_login');

                            ?>
                        </div>        
                        <div class="terms-flex">
                            <div class="terms"><?php echo get_the_content();?></div>
                        </div>    
                        <?php wp_nonce_field( 'ajax-login-nonce', 'security' ); ?>
                </form>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    let $ = jQuery
    // get redirect link from url parameters 
    const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const redirectLink = urlParams.get('redirect-link')
      
    $('.create-account-page #create-account').on('submit',function(e){
        e.preventDefault();
        $('.create-account-page p.status').show().text("Sending request, please wait...");
        // add loader in a button
        $('.create-account-page #create-account .primary-button').html('<div class="loader-icon loader--visible"></div>')
        let formData = {
            action: "register_user_front_end",
            username: $('#create-account #username').val(),
            email: $('#create-account #email').val(),
            password: $('#create-account #password').val(),
            firstName: $('#create-account #first-name').val(),
            lastName: $('#create-account #last-name').val(),
           
            redirectLink: redirectLink
        }
      
        jQuery.ajax({
          type:"POST",
          url:"<?php echo admin_url('admin-ajax.php'); ?>",
          data: formData,
          success: function(results){
              const resultsObj = JSON.parse(results)
              console.log(resultsObj)
            $('.create-account-page p.status').show().text(resultsObj.message);
            if(resultsObj.created){ 
                $('.create-account-page #create-account .primary-button').html('Created Account')
                if (redirectLink) {
                        window.location.replace(redirectLink);
                    }
                    else {
                        window.location.replace("/");
                    }
            }
            $('.create-account-page #create-account .primary-button').html('Create Account')
          },
          error: function(results) {
              console.log(results)
          }
        });
      });
</script>
<?php get_footer(); ?> 