<?php
/**
 * WooCommerce Order Status Manager
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Order Status Manager to newer
 * versions in the future. If you wish to customize WooCommerce Order Status Manager for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-order-status-manager/ for more information.
 *
 * @author      SkyVerge
 * @copyright   Copyright (c) 2015-2020, SkyVerge, Inc. (info@skyverge.com)
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Default customer order status email template.
 *
 * Note: the .td class used in table is from WooCommerce core (see email-styles.php).
 *
 * @type string $email_heading The email heading.
 * @type string $email_body_text The email body.
 * @type \WC_Order $order The order object.
 * @type bool $sent_to_admin Whether email is sent to admin.
 * @type bool $plain_text Whether email is plain text.
 * @type bool $show_download_links Whether to show download links.
 * @type bool $show_purchase_note Whether to show purchase note.
 * @type \WC_Email $email The email object.
 *
 * @since 1.0.0
 * @version 1.10.0
 */
?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>
<!-- body  -->
<div class="body max-width padding">
        <div class="text-content">
        <?php 
                    
                    $dateString = get_post_meta( $order->id, 'back_order_date', true );
                  
                    $date =  DateTime::createFromFormat('Ymd', $dateString);
                ?>
                
            <p class="paragraph dark-grey left-align">Thank you for notifying us concerning the damaged product that you have received. I am sorry to hear that product did not meet the high standards of our company. We make every effort to ensure that our products are delivered in perfect condition. Occasionally, however, unpredictable events alter the expected outcome.
               <br><br> Please email or call to arrange pickup of your damaged product and we will arrange a replacement. 
               <br><br>Your support and trust in us are much appreciated,
               <br><br>We are here for any inquiry, do not hesitate to contact us.
                <br> <br> Thank you for your patience.
                </p>
        </div>
    </div>


<?php do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );


/*
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/*
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * Show user-defined additional content - this is set in each email's settings.
 */
if ( $additional_content ) {
	echo wp_kses_post( wpautop( wptexturize( $additional_content ) ) );
}

/*
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );

 ?>
