<!-- newsletter section -->
<div class="footer-newsletter-section">
  <div class="flex-container  row-container">
    <div class="content">
      <h6 class="title">
        Sign Up for Inspiry Emails
      </h6>
      <p class="subtitle">
        Plus hear about the latest and greatest from our family of brands!
      </p>
    </div>
    <div class="form">
      <div class="label">
        Email Address*
      </div>
      <?php
      if (get_site_url() === "https://localhost" or get_site_url() === "http://localhost") {
        echo do_shortcode('[mc4wp_form id="13731"]');
      } else {
        echo do_shortcode('[mc4wp_form id="999535444"]');
      }
      ?>
    </div>
  </div>
  <div class="privacy-policy row-container">
    By signing up, you will receive Inspiry offers, promotions and other commercial messages. You are also agreeing to Inspiry's
    <a href="<?php echo get_privacy_policy_url() ?>">Privacy Policy</a>.
    You may unsubscribe at any time.
  </div>
</div>

<!-- footer section -->
<footer>
  <div class="flex-container row-container">
    <div class="columns">
      <div class="help-nav flex-item">
        <div class="title">
          Help
        </div>
        <div class="nav">
          <?php
          wp_nav_menu(array(
            'theme_location' => 'footer-help-info'
          ))
          ?>
        </div>
      </div>
      <div class="services-nav flex-item">
        <div class="title">
          Services
        </div>
        <div class="nav">
          <?php
          wp_nav_menu(array(
            'theme_location' => 'footer-services'
          ))
          ?>
        </div>
      </div>
      <div class="help-nav flex-item">
        <div class="title">
          Store
        </div>
        <div class="nav">
          <?php
          wp_nav_menu(array(
            'theme_location' => 'footer-store'
          ))
          ?>
        </div>
      </div>
      <div class="social flex-item">
        <div class="title">
          Get Social
        </div>
        <div class="social-icons">
          <?php
          $argsContact = array(
            'pagename' => 'contact'
          );
          $queryContact = new WP_Query($argsContact);
          while ($queryContact->have_posts()) {
            $queryContact->the_post();
          ?>
            <a href="<?php echo get_field("facebook"); ?>">
              <i class="fa-brands fa-facebook-square"></i>
            </a>
            <a href="<?php echo get_field("instagram"); ?>">
              <i class="fa-brands fa-instagram-square"></i>
            </a>
            <a href="<?php echo get_field("pintrest_"); ?>">
              <i class="fa-brands fa-pinterest-square"></i>
            </a>
            <a href="<?php echo get_field("youtube"); ?>">
              <i class="fa-brands fa-youtube"></i>
            </a>
          <?php
          }
          wp_reset_postdata();
          ?>
        </div>
      </div>
    </div>
  </div>
  <div class="banner row-container">
    <?php
    $argsBanner = array(
      'post_type' => 'banners',
      'posts_per_page' => 1,
      'tax_query' => array(
        array(
          'taxonomy' => 'banners_categories',
          'field'    => 'slug',
          'terms'    => array('footer'),
        )
      ),
    );
    $banner = new WP_Query($argsBanner);

    while ($banner->have_posts()) {
      $banner->the_post();
      $image = get_field('banner_image');
      $mobileImageUrl = get_field('banner_mobile_image')['url'];
      $imgUrl = $image['url'];
    ?>
      <a href="<?php echo get_field('banner_link'); ?>">
        <picture>
          <source media="(min-width:900px)" srcset="<?php echo $imgUrl; ?>">
          <img loading="lazy" src="<?php echo $mobileImageUrl; ?>" alt="<?php echo get_the_title(); ?>" width="100%">
        </picture>
      </a>
    <?php
    }
    wp_reset_postdata();
    ?>
  </div>

  <!-- copyright section -->
  <div class="copyright-section row-container">
    <div class="columns">
      <div class="links">
        <a class="first-child" href="<?php echo get_privacy_policy_url(); ?>">Privacy Policy</a>
        <a href="<?php echo get_site_url(); ?>/customer-service/terms-conditions/">Terms & Conditions</a>
      </div>
      <div class="copyright">
        © Copyright 2019 Inspiry NZ. All rights reserved.
      </div>
    </div>
  </div>
</footer>

<div class="go-to-header hide" id='go-to-header'>
  <a href="#header"><i class="fal fa-angle-up"></i></a>
</div>


<!-- overlay without loader  -->
<div class="dark-overlay">
</div>
<!-- overlay  -->
<div class="overlay">
  <i class="fa-duotone fa-loader fa-spin"></i>
</div>
<!-- foreground overlay  -->
<div class="foreground-loader">
  <i class="fa-duotone fa-loader fa-spin"></i>
</div>
<!-- error pop up  -->
<div class="error-modal">

  <div class="container">
    <i class="fa-solid fa-triangle-exclamation"></i>
    <div class="content"></div>
    <button>Dismiss</button>
  </div>
</div>
<!-- design board modal  -->
<?php
do_action('design_board_modal_hook'); ?>
<?php wp_footer(); ?>

</body>

</html>