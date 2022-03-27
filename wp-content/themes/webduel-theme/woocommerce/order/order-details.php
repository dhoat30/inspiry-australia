<?php
/**
 * Order details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/order/order-details.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.6.0
 */

defined( 'ABSPATH' ) || exit;

$order = wc_get_order( $order_id ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

if ( ! $order ) {
	return;
}

$order_items           = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
$show_purchase_note    = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
$show_customer_details = is_user_logged_in() && $order->get_user_id() === get_current_user_id();
$downloads             = $order->get_downloadable_items();
$show_downloads        = $order->has_downloadable_item() && $order->is_download_permitted();

if ( $show_downloads ) {
	wc_get_template(
		'order/order-downloads.php',
		array(
			'downloads'  => $downloads,
			'show_title' => true,
		)
	);
}
?>
<section class="woocommerce-order-details">
	<?php do_action( 'woocommerce_order_details_before_order_table', $order ); ?>

	<h2 class="woocommerce-order-details__title"><?php esc_html_e( 'Order details', 'woocommerce' ); ?></h2>

	<table class="woocommerce-table woocommerce-table--order-details shop_table order_details">

		<thead>
			<tr>
				<th class="woocommerce-table__product-name product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
				<th class="woocommerce-table__product-table product-total"><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
			</tr>
		</thead>

		<tbody>
			<?php
			do_action( 'woocommerce_order_details_before_order_table_items', $order );

			foreach ( $order_items as $item_id => $item ) {
				$product = $item->get_product();

				wc_get_template(
					'order/order-details-item.php',
					array(
						'order'              => $order,
						'item_id'            => $item_id,
						'item'               => $item,
						'show_purchase_note' => $show_purchase_note,
						'purchase_note'      => $product ? $product->get_purchase_note() : '',
						'product'            => $product,
					)
				);
			}

			do_action( 'woocommerce_order_details_after_order_table_items', $order );
			?>
		</tbody>

		<tfoot>
			<?php
			foreach ( $order->get_order_item_totals() as $key => $total ) {
				?>
					<tr>
						<th scope="row"><?php echo esc_html( $total['label'] ); ?></th>
						<td><?php echo ( 'payment_method' === $key ) ? esc_html( $total['value'] ) : wp_kses_post( $total['value'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
					</tr>
					<?php
			}
			?>
			<?php if ( $order->get_customer_note() ) : ?>
				<tr>
					<th><?php esc_html_e( 'Note:', 'woocommerce' ); ?></th>
					<td><?php echo wp_kses_post( nl2br( wptexturize( $order->get_customer_note() ) ) ); ?></td>
				</tr>
			<?php endif; ?>
		</tfoot>
	</table>

	<?php do_action( 'woocommerce_order_details_after_order_table', $order ); ?>
</section>

<?php
/**
 * Action hook fired after the order details.
 *
 * @since 4.4.0
 * @param WC_Order $order Order data.
 */
do_action( 'woocommerce_after_order_details', $order );

if ( $show_customer_details ) {
	wc_get_template( 'order/order-details-customer.php', array( 'order' => $order ) );
}
?>

<script>

dataLayer.push({
'event': 'transactionCompleted',
  'ecommerce': {
    'purchase': {
      'actionField': {
        'id': '<?php echo $order -> get_order_number()?>',                     
        'affiliation': 'Ecommerce shop',
        'revenue': '<?php echo $order->get_subtotal()?>',                     
        'tax': '<?php echo $order -> get_total_tax()?>',
        'shipping': '<?php echo $order -> calculate_shipping()?>',
        'coupon': '<?php echo $order-> get_coupon_codes()? $order-> get_coupon_codes()[0]: 'No Coupon' ?>'
      },
      'products': [          

      		<?php 
      			foreach($order-> get_items() as $item) {
      				$product = $order -> get_product_from_item($item);

      				$term_list = get_the_terms( $product->get_id(), 'product_cat' );
    				$term = $term_list[0];
				$variation_id = "No variation";
    				$variation = wc_get_product($item['variation_id']);
    				

    				if($variation != null) {
    					$variation_id = $variation -> get_id();
    				}



    				$list = $product->get_categories();; 
    				$page_title = get_the_title();
    				$quantity = $item['qty'];

    				?>       {
    							'name': '<?php echo $product -> get_name()?>',   
	       						'id': '<?php echo $product -> get_id()?>',
	       						'price': '<?php echo $product -> get_price()?>',
	     			       			'brand': '<?php echo  $product->get_attribute('pa_brands')?>',
	                        			'category': '<?php echo $term -> name ?>',
	       						'variant': '<?php echo $variation_id ?>',
	       						'quantity': '<?php echo $quantity ?>'
	       					},

    				<?php

      			}
      		?>
       ]
    }
  }
});

</script>

<script>
 let transactionObject = { 
	 currency: "NZD", 
	 value: '<?php echo $order->get_total()?>', 
	 content_type: 'Product', 
	 contents: [
			<?php 
      			foreach($order-> get_items() as $item) {
      				$product = $order -> get_product_from_item($item);
      				$term_list = get_the_terms( $product->get_id(), 'product_cat' );
    				$term = $term_list[0];
				$variation_id = "No variation";
    				$variation = wc_get_product($item['variation_id']);
    				
    				if($variation != null) {
    					$variation_id = $variation -> get_id();
    				}

    				$list = $product->get_categories();; 
    				$page_title = get_the_title();
    				$quantity = $item['qty'];

    				?>       {
	       						'id': '<?php echo $product -> get_id()?>',
	       						'item_price': '<?php echo $product -> get_price()?>',
	       						'quantity': '<?php echo $quantity ?>'
	       					},
    				<?php
      			}
      		?>
	 ]
 }

 fbq('track', 'Purchase', transactionObject);

</script>

<script>
	// conversion tracking pinterest
	pintrk('track', 'checkout', {
	value: '<?php echo $order->get_total()?>',
	currency: 'NZD',
	line_items: [
		<?php 
      			foreach($order-> get_items() as $item) {
      				$product = $order -> get_product_from_item($item);
      				$term_list = get_the_terms( $product->get_id(), 'product_cat' );
    				$term = $term_list[0];
				$variation_id = "No variation";
    				$variation = wc_get_product($item['variation_id']);
    				
    				if($variation != null) {
    					$variation_id = $variation -> get_id();
    				}

    				$list = $product->get_categories();; 
    				$page_title = get_the_title();
    				$quantity = $item['qty'];

    				?>      {
						product_name: '<?php echo $product -> get_name()?>',
						product_id: '<?php echo $product -> get_id()?>',
						product_category: '<?php echo $term -> name ?>',
						product_price: '<?php echo $product -> get_price()?>',
						product_quantity: <?php echo $quantity ?>,
						product_brand: '<?php echo  $product->get_attribute('pa_brands')?>'
						},
    				<?php
      			}
      		?>
	]
	});
</script>
