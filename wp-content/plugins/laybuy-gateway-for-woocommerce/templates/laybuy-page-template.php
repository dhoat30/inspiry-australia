<?php if ( ! defined( 'ABSPATH' ) ) {
exit; // Exit if accessed directly
}

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta name="viewport" content="width=device-width" />
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta name="robots" content="noindex, nofollow" />
        <title><?php _e( 'What is Laybuy?', 'woo_laybuy' ); ?></title>
        <link rel="stylesheet" href="<?php echo esc_url( str_replace( array( 'http:', 'https:' ), '', WC_LAYBUY_PLUGIN_URL ) . '/assets/css/laybuy-iframe.css' ); ?>" type="text/css" />
    </head>
    <body>
        <div class="laybuy-content-container">
            <iframe src="<?php echo do_shortcode('[laybuy_iframe_src]') ?>"></iframe>
        </div>
    </body>
</html>