<?php
/**
 * Email Order Items
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-order-items.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 3.7.0
 */

defined( 'ABSPATH' ) || exit;

?>
<?php 
                        
						$count = 1;
						foreach( $order->get_items() as $item_id => $line_item ){
							$item_data = $line_item->get_data();
							$product = $line_item->get_product();
							$product_name = $product->get_name();
							$productID = $product->get_id();
							$item_quantity = $line_item->get_quantity();
							$item_total = $line_item->get_total();
							$metadata['Line Item '.$count] = 'Product name: '.$product_name.' | Quantity: '.$item_quantity.' | Item total: '. number_format( $item_total, 2 );
							$count += 1;
							?>
							<tr class="items">
								<td class="playfair-fonts title"> <a href="<?php echo  get_the_permalink($productID);?>" target="_blank"><span><img src="<?php echo get_the_post_thumbnail_url($productID, 'thumbnail');?>" alt=""></span> <span class="playfair-fonts"><?php echo $product_name; ?></span></a> </td>
								<td class="playfair-fonts"><?php echo $item_quantity;?></td>
								<td class="playfair-fonts">
								   $<?php echo  number_format($item_total); ?>
								</td>
							</tr>


							<?php 
						}
					
					?>