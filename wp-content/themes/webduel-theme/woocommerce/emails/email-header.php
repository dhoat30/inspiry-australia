<?php
/**
 * Email Header
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-header.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 4.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<link rel="preconnect" href="https://fonts.gstatic.com">
		<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">

		<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>" />
		<title><?php echo get_bloginfo( 'name', 'display' ); ?></title>
	</head>
	<body <?php echo is_rtl() ? 'rightmargin' : 'leftmargin'; ?>="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
	
	<section class="email-template processing-order">
		<div class="header max-width padding">
			<div class="logo">
				<img src="<?php echo site_url();?>/wp-content/uploads/2020/11/Inspiry_Logo-transparent-1.png" alt="Inspiry Logo">
			</div>
			<div class="store">
				<div>
					<a href="https://inspiry.co.nz/products" target="_blank">Store</a>
				</div>
				<div> <a href="https://inspiry.co.nz/products" target="_blank"><img src="https://inspiry.co.nz/wp-content/uploads/2021/05/shopping-cart.png" alt="Cart Icon"></a> </div>
			</div>
		</div>