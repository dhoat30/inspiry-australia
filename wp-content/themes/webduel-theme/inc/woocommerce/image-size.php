<?php 
add_theme_support( 'post-thumbnails' );

add_action( 'after_setup_theme', 'mytheme_add_woocommerce_support' );
function mytheme_add_woocommerce_support(){
    add_theme_support( 'wc-product-gallery-zoom' );
    add_theme_support( 'wc-product-gallery-lightbox' );
    add_theme_support( 'wc-product-gallery-slider' );
    add_theme_support( 'woocommerce', array(
        'thumbnail_image_width' => 500,
        'gallery_thumbnail_image_width' => 150,
        'single_image_width' => 900
        ) );
}
