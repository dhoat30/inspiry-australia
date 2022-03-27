<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-WXK26N9');</script>
<!-- End Google Tag Manager -->


    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11" />
    <?php wp_head(); ?>


    <!-- splide -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@latest/dist/css/splide.min.css">
    <!-- font awesome  -->
    <script src="https://kit.fontawesome.com/71827cc3f2.js" crossorigin="anonymous"></script>
    <!-- google fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
</head>

<body id="header" <?php body_class(); ?> >
  <!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-WXK26N9"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
    <section class="header desktop-header">
        <div class="top-banner">
            <div class="banner-container">
                <div class="banner-card owl-carousel">
                    <?php

                    $argsBanner = array(
                        'post_type' => 'banners',
                        'posts_per_page' => -1,
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'banners_categories',
                                'field'    => 'slug',
                                'terms'    => array('top-banner'),
                            )
                        )
                    );
                    $banner = new WP_Query($argsBanner);

                    while ($banner->have_posts()) {
                        $banner->the_post();
                        if (get_field('banner_link')) {
                    ?>
                            <a href="<?php echo get_field('banner_link'); ?>" class="anchor"> <?php echo get_the_title(); ?> LEARN <i class="fal fa-chevron-right white"></i></a>

                        <?php
                        } else {
                        ?>
                            <a href="<?php echo get_field('banner_link'); ?>" class="anchor"> <?php echo get_the_title(); ?></a>
                    <?php
                        }
                    }
                    wp_reset_postdata();
                    ?>
                </div>
            </div>
        </div>
        <div class="middle-section row-container">
            <div class="search-container">
                <div class="search-code">
                    <div class="search-bar">
                        <input autocomplete="off" type="text" class="search-input" placeholder="Search for rugs, furniture, and more" id="search-term" />
                        <i class="fad fa-spinner fa-spin" aria-hidden="true"></i>
                        <i class="far fa-search" aria-hidden="true"></i>
                    </div>
                    <div class="result-div"></div>
                </div>
            </div>
            <div class="logo-container">
                <?php
                $argsLogo = array(
                    'post_type' => 'page',
                    'pagename' => 'contact',
                    'posts_per_page' => 1
                );
                $queryLogo = new WP_Query($argsLogo);
                while ($queryLogo->have_posts()) {
                    $queryLogo->the_post();
                    $image = get_field('logo')['url'];
                ?>
                    <a href="/">
                        <img src="<?php echo $image ?>" alt="Inspiry Logo" />
                    </a>
                <?php
                }
                wp_reset_postdata();
                ?>
            </div>
            <div class="useful-links-container">
                <div class="sign-in-container container">
                    <!-- get logged in user -->
                    <?php
                    if (is_user_logged_in()) {
                        global $current_user;
                    ?>
                        <span>Hi, <?php echo  $current_user->first_name; ?></span>
                    <?php
                    } else {
                    ?>
                        <span>Orders & Sign In</span>
                    <?php

                    } ?>
                    <i class="fa-solid fa-user"></i>
                    <?php
                    // sign in modal 
                    echo do_shortcode('[sign-in-modal]');
                    ?>
                </div>
                <div class="design-board-icon-container container">
                    <i class="fa-solid fa-heart"></i>

                    <?php
                    // sign in modal 
                    echo do_shortcode('[design-board-header-modal]');
                    ?>
                </div>

                <div class="cart-container container shopping-cart ">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-item-count cart-items-header"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                </div>
            </div>
        </div>
        <!-- top navbar -->
        <section class="top-navbar-section row-container">
            <nav class="navbar">
                <?php
                wp_nav_menu(
                    array(
                        'theme_location' => 'top-navbar',
                        'container_id' => 'top-navbar'
                    )
                );
                ?>
            </nav>
        </section>

        <!--  main menu-->
        <section class="main-navbar-section">
            <nav class="navbar row-container">
                <?php
                wp_nav_menu(
                    array(
                        'theme_location' => 'inspiry_main_menu',
                        'container_id' => 'cssmenu'
                    )
                );
                ?>
            </nav>
        </section>
    </section>

    <!-- mobile header  -->
    <section class="mobile-header header">
        <div class="top-banner">
            <div class="banner-container">
                <div class="banner-card owl-carousel">
                    <?php
                    $argsBanner = array(
                        'post_type' => 'banners',
                        'posts_per_page' => -1,
                        'tax_query' => array(
                            array(
                                'taxonomy' => 'banners_categories',
                                'field'    => 'slug',
                                'terms'    => array('top-banner'),
                            )
                        )
                    );
                    $banner = new WP_Query($argsBanner);

                    while ($banner->have_posts()) {
                        $banner->the_post();
                        if (get_field('banner_link')) {
                    ?>
                            <a href="<?php echo get_field('banner_link'); ?>" class="anchor"> <?php echo get_the_title(); ?> LEARN <i class="fal fa-chevron-right white"></i></a>

                        <?php
                        } else {
                        ?>
                            <a href="<?php echo get_field('banner_link'); ?>" class="anchor"> <?php echo get_the_title(); ?></a>
                    <?php
                        }
                    }
                    wp_reset_postdata();
                    ?>
                </div>
            </div>
        </div>



        <!--  main menu-->
        <section class="main-navbar-section row-container">
            <nav class="navbar ">
                <?php
                wp_nav_menu(
                    array(
                        'theme_location' => 'inspiry_main_menu',
                        'container_id' => 'cssmenu'
                    )
                );
                ?>
            </nav>
            <div class="logo-container">
                <?php
                $argsLogo = array(
                    'post_type' => 'page',
                    'pagename' => 'contact',
                    'posts_per_page' => 1
                );
                $queryLogo = new WP_Query($argsLogo);
                while ($queryLogo->have_posts()) {
                    $queryLogo->the_post();
                    $image = get_field('logo')['url'];
                ?>
                    <a href="/">
                        <img src="<?php echo $image ?>" alt="Inspiry Logo" />
                    </a>
                <?php
                }
                wp_reset_postdata();
                ?>
            </div>
            <div class="useful-links-container">
                <div class="cart-container container shopping-cart ">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-item-count cart-items-header"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
                </div>
                <div class="sign-in-container container">
                    <i class="fa-solid fa-user"></i>
                    <?php
                    // sign in modal 
                    echo do_shortcode('[sign-in-modal]');
                    ?>
                </div>



            </div>
        </section>
        <div class="search-container ">
            <div class="search-code row-container">
                <div class="search-bar">
                    <input autocomplete="off" type="text" class="search-input" placeholder="Search for rugs, furniture, and more" id="mobile-search-term" />
                    <i class="fad fa-spinner fa-spin" aria-hidden="true"></i>
                    <i class="far fa-search" aria-hidden="true"></i>
                </div>
                <div class="result-div"></div>
            </div>
        </div>
    </section>
    <!-- <div class="design-boards-icon-container container">
                    <i class="fa-solid fa-heart"></i>
                </div> -->














    <div class="cart-popup-container box-shadow">
        <div class="cart-box">
            <div class="title-section">
                <div class="title">My Cart</div>
                <i class="fa-light fa-xmark"></i>
            </div>
            <div class="flex-card">
                <?php

                foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
                    $product = $cart_item['data'];
                    $product_id = $product_id = $cart_item['product_id'];

                    $quantity = $cart_item['quantity'];
                    $price = WC()->cart->get_product_price($product);
                    $subtotal = WC()->cart->get_product_subtotal($product, $cart_item['quantity']);
                    $link = $product->get_permalink($cart_item);
                    // Anything related to $product, check $product tutorial
                    $meta = wc_get_formatted_cart_item_data($cart_item);

                ?>
                    <div class="product-card">
                        <?php

                        // condition to check if the product is simple
                        if ($product->name == "Free Sample") {
                            // pulling information of an original product in a form of an objec†
                            $originalProduct = wc_get_product($cart_item["free_sample"]);

                            if (!empty($originalProduct)) {
                                $permalink = get_the_permalink($originalProduct->get_id());
                                $imageID = $originalProduct->image_id;
                                $name = $originalProduct->get_name();
                            }
                        ?>
                            <a href="<?php echo $permalink; ?>" class="mini_cart_item <?php echo $cart_item_key; ?>">
                                <div class="img-container">
                                    <img src="<?php echo wp_get_attachment_image_url($imageID, 'woocommerce_thumbnail'); ?>" alt="<?php echo $name; ?>" />
                                </div>

                                <div class="title-container">
                                    <h5 class="title"> <?php echo $quantity; ?> X Free Sample (<?php echo $name; ?> )</h5>
                                </div>

                                <div class="price-container">
                                    <h6 class="cart-price">$<?php echo number_format($product->price * $quantity) ?></h6>
                                </div>

                            </a>
                            <div class="remove-column remove-product">
                                <i class="fa-solid fa-trash" data-product_id="<?php echo $product_id ?>" data-cart_item_key="<?php echo $cart_item_key; ?>"></i>
                            </div>
                        <?php
                        } else {
                        ?>
                            <a href="<?php echo $link ?>" class="mini_cart_item <?php echo $cart_item_key; ?>">
                                <div class="img-container">
                                    <img src="<?php echo get_the_post_thumbnail_url($product_id, 'medium'); ?>" alt="<?php echo $product->name ?>" />
                                </div>
                                <div class="title-container">
                                    <h5 class="title"> <?php echo $quantity; ?> X <?php echo $product->name ?></h5>
                                </div>

                                <div class="price-container">
                                    <h6 class="cart-price">$<?php echo number_format($product->price * $quantity); ?></h6>
                                </div>
                            </a>
                            <div class="remove-column remove-product">
                                <i class="fa-solid fa-trash" data-product_id="<?php echo $product_id ?>" data-cart_item_key="<?php echo $cart_item_key; ?>"></i>
                            </div>
                        <?php
                        } ?>
                    </div>
                <?php
                }
                ?>
            </div>
            <div class="pop-up-footer">
                <!-- <div class="total-container"> -->
                <!-- <div class="total poppins-font">
                        Total: $<?php
                                //  $totalAmount = str_replace(".00", "", (string)number_format(WC()->cart->total, 2, ".", ""));
                                //echo number_format($totalAmount); 
                                ?>
                    </div> -->
                <!-- </div> -->
                <!-- <div class="cont-shopping">
                            <a class="secondary-button" href="#">Continue Shopping</a>
                        </div> -->
                <div class="checkout-btn">
                    <a class="primary-button" href="<?php echo get_site_url(); ?>/cart">Cart</a>
                </div>
            </div>
        </div>
    </div>