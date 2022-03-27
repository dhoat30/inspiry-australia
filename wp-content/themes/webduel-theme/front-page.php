<?php
get_header();
?>
<!-- home page -->
<section class="video-container ">
    <?php
    $argsVideos = array(
        'post_type' => 'videos',
        'posts_per_page' => 1,
        'tax_query' => array(
            array(
                'taxonomy' => 'video-category',
                'field'    => 'slug',
                'terms'    => array('home-page-hero-video'),
            )
        ),
    );
    $video = new WP_Query($argsVideos);
    while ($video->have_posts()) {
        $video->the_post();
    ?>
        <a href="<?php echo get_field('add_a_landing_page_link'); ?>">
            <?php echo get_the_content(); ?>
        </a>
        <div class="title-container">
            <div class="title"><?php print_r(get_field('section_title')); ?></div>
            <h2 class="subtitle"><?php print_r(get_field('section_subtitle')); ?></h2>
        </div>
    <?php
    }
    wp_reset_postdata();
    ?>
</section>


<!-- second section  -->
<section class="second-section wide-image-section row-container">
    <div class="image-container">
        <?php
        $argsSecondSectionImage = array(
            'post_type' => 'banners',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'banners_categories',
                    'field'    => 'slug',
                    'terms'    => array('second-section'),
                )
            )
        );
        $secondSectionImage = new WP_Query($argsSecondSectionImage);

        while ($secondSectionImage->have_posts()) {
            $secondSectionImage->the_post();
            // get desktop and mobile image 
            $image = get_field('banner_image');
            $mobileImage = get_field('banner_mobile_image');
        ?>
            <h3 class="title row-container"><?php echo get_field('title'); ?></h3>
            <a class="link" href="<?php echo get_field('banner_link'); ?>">
                <picture>
                    <source media="(min-width:1366px)" srcset="<?php echo $image['sizes']['2048x2048']; ?>">
                    <source media="(min-width:600px)" srcset="<?php echo $image['sizes']['large']; ?>">
                    <img loading="lazy" src="<?php echo $mobileImage['sizes']['woocommerce_thumbnail']; ?>" alt="<?php echo get_the_title(); ?>" width="100%">
                </picture>
            </a>
            <a class="row-container link-text" href="<?php echo get_field('banner_link'); ?>">
                <?php echo get_field('link_text'); ?>
                <i class="fa-regular fa-arrow-right-long"></i>
            </a>
        <?php
        }
        wp_reset_postdata();

        ?>
    </div>
</section>

<!-- third section  -->
<section class="third-section wide-image-section row-container">
    <div class="image-container">
        <?php
        $argsThirdSectionImage = array(
            'post_type' => 'banners',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'banners_categories',
                    'field'    => 'slug',
                    'terms'    => array('third-section'),
                )
            )
        );
        $thirdSectionImage = new WP_Query($argsThirdSectionImage);

        while ($thirdSectionImage->have_posts()) {
            $thirdSectionImage->the_post();
            // get desktop and mobile image 
            $image = get_field('banner_image');
            $mobileImage = get_field('banner_mobile_image');
        ?>
            <h3 class="title row-container"><?php echo get_field('title'); ?></h3>
            <a class="link" href="<?php echo get_field('banner_link'); ?>">
                <picture>
                    <source media="(min-width:1366px)" srcset="<?php echo $image['sizes']['2048x2048']; ?>">
                    <source media="(min-width:600px)" srcset="<?php echo $image['sizes']['large']; ?>">
                    <img class="image" loading="lazy" src="<?php echo $mobileImage['sizes']['woocommerce_thumbnail']; ?>" alt="<?php echo get_the_title(); ?>" width="100%">
                </picture>
            </a>
            <a class="row-container link-text" href="<?php echo get_field('banner_link'); ?>">
                <?php echo get_field('link_text'); ?>
                <i class="fa-regular fa-arrow-right-long"></i>
            </a>
        <?php
        }
        wp_reset_postdata();
        ?>
    </div>
</section>


<!-- fourth section  -->
<section class="fourth-section category-cards-section row-container">
    <h3 class="title">shop by category</h3>
    <ul class="card-list owl-carousel">
        <?php
        $argsCategoryCards = array(
            'post_type' => 'homepage-cards',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'home-page-card-category',
                    'field'    => 'slug',
                    'terms'    => array('category-section'),
                )
            )
        );
        $categoryCards = new WP_Query($argsCategoryCards);

        while ($categoryCards->have_posts()) {
            $categoryCards->the_post();
        ?>
            <li class="item">
                <a class="link" href="<?php echo get_field('category_link'); ?>">
                    <img loading="lazy" src="<?php echo get_the_post_thumbnail_url(null, "woocommerce_thumbnail"); ?>" alt="<?php echo get_the_title(); ?>" width="100%">
                    <span>
                        <?php echo get_the_title(); ?>
                        <i class="fa-regular fa-arrow-right-long"></i>
                    </span>
                </a>
            </li>
        <?php
        }
        wp_reset_postdata();
        ?>
    </ul>
</section>
<!-- fifth section- wallpaper -->
<section class="wallpaper-section fifth-section">
    <div class="container row-container">
        <?php
        $argsWallpaper = array(
            'posts_per_page' => 1,
            'tag' => 'home-page-wallpaper-section'
        );
        $wallpaper = new WP_Query($argsWallpaper);
        while ($wallpaper->have_posts()) {
            $wallpaper->the_post();
            $largeImage = get_field('large_image');
        ?>
            <a class="primary-button" href="<?php echo get_field('subtitle_link'); ?>"><?php echo get_field('subtitle'); ?></a>
            <h3 class="title"><?php echo get_the_title(); ?></h3>
            <a class="link" href="<?php echo get_field('big_image_link'); ?>">BROWSE NOW</a>

            <div class="grid">
                <!-- large image -->
                <a href="<?php echo get_field('big_image_link'); ?>" class="large-image">
                    <picture>
                        <source media="(min-width:1366px)" srcset="<?php echo $largeImage['url']; ?>">
                        <source media="(min-width:600px)" srcset="<?php echo $largeImage['sizes']['large']; ?>">
                        <img loading="lazy" src="<?php echo $largeImage['sizes']['woocommerce_thumbnail']; ?>" alt="<?php echo get_the_title(); ?>">
                    </picture>
                </a>
                <div class="cards-container">
                    <?php
                    if (have_rows('four_images_column')) {
                        while (have_rows('four_images_column')) {
                            the_row();
                    ?>
                            <a href="<?php echo get_sub_field('link'); ?>" class="small-image-box">
                                <img src="<?php echo get_sub_field('image')['sizes']['woocommerce_thumbnail']; ?>" />
                                <div class="content">
                                    <h4 class="title">
                                        <?php echo get_sub_field('product_title'); ?>
                                    </h4>
                                    <h5 class="excerpt">
                                        <?php echo get_sub_field('product_excerpt'); ?>
                                    </h5>
                                </div>
                            </a>
                    <?php
                        }
                    }
                    ?>
                </div>
            </div>
        <?php
        }
        wp_reset_postdata();
        ?>
    </div>
</section>
<!-- sixth section - fabric -->
<section class="fabric-section sixth-section">
    <div class="container row-container">
        <?php
        $argsWallpaper = array(
            'posts_per_page' => 1,
            'tag' => 'home-page-fabric-section'
        );
        $wallpaper = new WP_Query($argsWallpaper);
        while ($wallpaper->have_posts()) {
            $wallpaper->the_post();
            $largeImage = get_field('large_image');
        ?>
            <a class="primary-button" href="<?php echo get_field('subtitle_link'); ?>"><?php echo get_field('subtitle'); ?></a>
            <h3 class="title"><?php echo get_the_title(); ?></h3>
            <a class="link" href="<?php echo get_field('big_image_link'); ?>">BROWSE NOW</a>

            <div class="grid">
                <!-- large image -->
                <a href="<?php echo get_field('big_image_link'); ?>" class="large-image">
                    <picture>
                        <source media="(min-width:1366px)" srcset="<?php echo $largeImage['url']; ?>">
                        <source media="(min-width:600px)" srcset="<?php echo $largeImage['sizes']['large']; ?>">
                        <img loading="lazy" src="<?php echo $largeImage['sizes']['woocommerce_thumbnail']; ?>" alt="<?php echo get_the_title(); ?>">
                    </picture>
                </a>
                <div class="cards-container">
                    <?php
                    if (have_rows('four_images_column')) {
                        while (have_rows('four_images_column')) {
                            the_row();
                    ?>
                            <a href="<?php echo get_sub_field('link'); ?>" class="small-image-box">
                                <img src="<?php echo get_sub_field('image')['sizes']['woocommerce_thumbnail']; ?>" />
                                <div class="content">
                                    <h4 class="title">
                                        <?php echo get_sub_field('product_title'); ?>
                                    </h4>
                                    <h5 class="excerpt">
                                        <?php echo get_sub_field('product_excerpt'); ?>
                                    </h5>
                                </div>
                            </a>
                    <?php
                        }
                    }
                    ?>
                </div>
            </div>
        <?php
        }
        wp_reset_postdata();
        ?>
    </div>
</section>
<!-- seventh section - kitchen  -->
<section class="sixth-section kitchen-section">
    <div class="image-container">
        <?php
        $argsSecondSectionImage = array(
            'post_type' => 'banners',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'banners_categories',
                    'field'    => 'slug',
                    'terms'    => array('sixth-section'),
                )
            )
        );
        $secondSectionImage = new WP_Query($argsSecondSectionImage);

        while ($secondSectionImage->have_posts()) {
            $secondSectionImage->the_post();
            // get desktop and mobile image 
            $image = get_field('banner_image');
            $mobileImage = get_field('banner_mobile_image');
        ?>
            <a class="link" href="<?php echo get_field('banner_link'); ?>">
                <picture>
                    <source media="(min-width:1366px)" srcset="<?php echo $image['sizes']['2048x2048']; ?>">
                    <source media="(min-width:600px)" srcset="<?php echo $image['sizes']['large']; ?>">
                    <img loading="lazy" src="<?php echo $mobileImage['sizes']['woocommerce_thumbnail']; ?>" alt="<?php echo get_the_title(); ?>" width="100%">
                </picture>
            </a>
            <h3 class="title row-container"><?php echo get_field('title'); ?></h3>

            <a class="primary-button" href="<?php echo get_field('banner_link'); ?>">
                <?php echo get_field('link_text'); ?>
            </a>
        <?php
        }
        wp_reset_postdata();

        ?>
    </div>
</section>
<!-- services section  -->
<section class="seventh-section services-section">
    <div class="container row-container">
        <?php

        $argsServices = array(
            'post_type' => 'loving',
            'tax_query' => array(
                array(
                    'taxonomy' => 'category',
                    'field'    => 'slug',
                    'terms'    => array('our-services'),
                )
            ),
            'orderby' => 'date',
            'order' => 'ASC'
        );
        $services = new WP_Query($argsServices);

        while ($services->have_posts()) {
            $services->the_post();
            $words = str_word_count(get_the_content(), 2);
            $pos   = array_keys($words);
            $excerpt  = substr(get_the_content(), 0, $pos[22]) . '...';
        ?>
            <div class="card">
                <div>
                    <img class="service-image" src="<?php echo get_the_post_thumbnail_url(null, "full"); ?>" alt="<?php echo get_the_title(); ?>">
                    <div class="icon">
                        <img src="<?php echo get_field('icon')['url']; ?>" alt="<?php echo get_the_title(); ?>" />
                    </div>
                    <div class="content">
                        <h5 class="title"><?php echo get_the_title(); ?></h5>
                        <p class="excerpt"><?php echo $excerpt; ?></p>
                        <a href="<?php echo get_field('add_link'); ?>" class="link"><?php echo get_field('add_subtitle_'); ?></a>
                    </div>

                </div>
            </div>
        <?php
        }
        wp_reset_postdata();
        ?>
    </div>
</section>

<!-- eighth section - be inspired  -->
<section class="be-inspired-section row-container">
    <div class="container">
        <?php

        $argsTrade = array(
            'post_type' => 'post',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'post_tag',
                    'field'    => 'slug',
                    'terms'    => array('be-inspired-section-home-page'),
                )
            )
        );
        $trade = new WP_Query($argsTrade);

        while ($trade->have_posts()) {
            $trade->the_post();
        ?>
        <h2 class="title"><?php echo get_the_title();?></h2>
            <div class="cards owl-carousel">

                <?php
                if (have_rows('images')) {
                    while (have_rows('images')) {
                        the_row();
                ?>
                        <a class="anchor-container" href="<?php echo get_sub_field('link'); ?>">
                            <img src="<?php echo get_sub_field('image')['sizes']['woocommerce_thumbnail']; ?>" alt="<?php echo get_sub_field('title'); ?>" />
                            <span><?php echo get_sub_field('title'); ?></span>
                        </a>

                <?php
                    }
                }
                ?>

            </div>
        <?php
        }
        wp_reset_postdata();
        ?>

    </div>
</section>

<!-- ninth section - Trade Professional -->
<section class="trade-section">
    <div class="container">
        <?php

        $argsTrade = array(
            'post_type' => 'post',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'post_tag',
                    'field'    => 'slug',
                    'terms'    => array('trade-professional-section-home-page'),
                )
            )
        );
        $trade = new WP_Query($argsTrade);

        while ($trade->have_posts()) {
            $trade->the_post();
        ?>
            <div class="first-section">
                <h2 class="title"><?php echo get_the_title(); ?></h2>
                <div class="subtitle"><?php echo get_the_content(); ?></div>
                <div class="buttons">
                    <a href="https://inspiry.co.nz/home/join-trade" class="primary-button">Join Directory</a>
                    <a href="https://inspiry.co.nz/home/professionals" class="secondary-button">View Professionals</a>
                </div>
            </div>
            <div class="middle-section">
                <div class="header">
                    <div class="image-container">
                        <img src="<?php echo get_field('trade_logo')['url']; ?> " alt="<?php echo get_field('trade_title');  ?>" />
                    </div>
                    <div class="title-container">
                        <div class="title">
                            <?php echo get_field('trade_title');  ?>
                        </div>
                        <div class="category">
                            <?php echo get_field('trade_category');  ?>
                        </div>
                    </div>
                </div>
                <div class="name"><?php echo get_field('trade_title'); ?></div>
                <div class="content"><?php echo get_field('trade_content'); ?></div>
                <div class="footer">
                    <div class="social-container">
                        <?php
                        if (have_rows('trade_social')) {
                            while (have_rows('trade_social')) {
                                the_row();
                                if (get_sub_field('social_media_name') === 'Facebook') {
                        ?>
                                    <a href="<?php echo get_sub_field('social_media_link'); ?>">
                                        <i class="fa-brands fa-facebook-square"></i>
                                    </a>
                                <?php
                                } else if (get_sub_field('social_media_name') === 'Instagram') {
                                ?>
                                    <a href="<?php echo get_sub_field('social_media_link'); ?>">
                                        <i class="fa-brands fa-instagram-square"></i>
                                    </a>
                                <?php
                                } else if (get_sub_field('social_media_name') === 'Twitter') {
                                ?>
                                    <a href="<?php echo get_sub_field('social_media_link'); ?>">
                                        <i class="fa-brands fa-twitter-square"></i>
                                    </a>
                        <?php
                                }
                            }
                        }
                        ?>
                    </div>
                    <div class="buttons">
                        <a href="<?php echo get_field('project_link'); ?>" class="secondary-button">View Projects</a>
                        <a href="<?php echo get_field('trade_profile_link'); ?>" class="secondary-button">Contact</a>
                    </div>

                </div>
            </div>
            <div class="last-section">
                <?php
                if (have_rows('side_images')) {
                    while (have_rows('side_images')) {
                        the_row();
                ?>
                        <img src="<?php echo get_sub_field('image')['sizes']['woocommerce_thumbnail']; ?>" alt="<?php echo get_the_title(); ?>">
                <?php
                    }
                }
                ?>
            </div>
        <?php

        }
        wp_reset_postdata();
        ?>
    </div>
</section>
<?php

get_footer();
?>