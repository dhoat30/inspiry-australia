<?php
/**
 * Order details table shown in emails.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="order-container max-width">
        <div class="order-content"> 
            <div class="text-content">
                <!-- change it depeding on a email template  -->
                <!-- change above -->
                <div class="meta">
               
                    <div class="order-number">Order #: <?php echo $order->get_id();?></div>

                    <div class="order-date">Order Date: <?php 
                    
                    // format date  
                    $justDate = strtotime($order->order_date);
                    echo date('d-m-Y',$justDate);

                    ?></div>
                </div>
            </div>

			<div class="product-cards max-width padding">
                <div class="card">
                    <table>
                        <tr>
                            <th class="playfair-fonts">Item</th>
                            <th class="playfair-fonts">Qty</th>
                            <th class="playfair-fonts">Price</th>
                        </tr>
                        <!-- GET ORDER ITEMS FROM THE email-order-items.php file -->
                        <?php 
						echo wc_get_email_order_items($order);
						?>
                        
                        </tr>
                    </table>
                </div>
                
            </div>

			<!-- total -->
			<div class="total">
                <table>
                    <tr>
                        <td class="playfair-fonts">Subtotal:</td>
                        <td class="playfair-fonts">$<?php
                        $subtotal= $order->get_subtotal();
                        echo $subtotal;?></td>
                    </tr>
                    <tr>
                        <td class="playfair-fonts">Shipping:</td>
                        <td class="playfair-fonts">$<?php echo $order->get_shipping_total();?></td>
                    </tr>
                    <tr>
                        <td class="playfair-fonts">GST:</td>
                        <td class="playfair-fonts">$<?php echo $order->get_total_tax();?></td>
                    </tr>
                    <tr>
                        <td class="playfair-fonts">Payment Method:</td>
                        <td class="playfair-fonts"><?php echo get_post_meta( $order->id, '_payment_method', true );?></td>
                    </tr>
                    <tr>
                        <td class="playfair-fonts">Total:</td>
                        <td class="playfair-fonts">$<?php echo  $order->get_total();?></td>
                    </tr>
                </table>
            </div>

     