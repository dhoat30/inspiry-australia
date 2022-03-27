<?php 
function webduelSocialShareFunction() {
    $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    return '
    <div class="social-share">
        <a target="_blank" href="https://www.pinterest.com/pin/create/button?url='.$actual_link.'">
            <i class="fa-brands fa-pinterest-p"></i>
        </a>
        <a target="_blank" href="https://www.facebook.com/sharer.php?u='.$actual_link.'">
            <i class="fa-brands fa-facebook-f"></i>
        </a>
      
        <a target="_blank" href="mailto:?subject=I wanted you to see this product&body=Check out this product '.$actual_link.'">
            <i class="fa-regular fa-envelope"></i>
         </a>
    </div>';
}

add_shortcode('webduelSocialShare', 'webduelSocialShareFunction'); 