<?php

defined( 'ABSPATH' ) or die( 'Keep Silent' );

if ( ! class_exists( 'Woo_Variation_Clone' ) ):

	class Woo_Variation_Clone {

		public static function init() {

			add_action( 'woocommerce_variation_header', array( __CLASS__, 'add_checkbox' ) );
			add_action( 'woocommerce_variable_product_bulk_edit_actions', array( __CLASS__, 'add_dropdown' ) );
			add_action( 'woo_variation_duplicator_load_variations', array( __CLASS__, 'notice' ) );
			add_action( 'woocommerce_bulk_edit_variations', array( __CLASS__, 'clone' ), 10, 4 );
			add_action( 'woocommerce_variable_product_before_variations', array( __CLASS__, 'instruction' ) );

			remove_action( 'wp_ajax_woocommerce_load_variations', array( 'WC_AJAX', 'load_variations' ) );
			add_action( 'wp_ajax_woocommerce_load_variations', array( __CLASS__, 'load_variations' ) );
		}

		public static function notice() {

			$results      = get_transient( 'woo_variation_duplicator_cloned_ids' );
			$limit_exceed = get_transient( 'woo_variation_duplicator_exceed_clone_limit' );

			if ( $results ) {
				$message = sprintf( esc_html__( 'Variation: #%s cloned.', 'variation-duplicator-for-woocommerce' ), implode( ', #', $results ) );
				printf( '<div class="inline notice variation-duplicator-for-woocommerce-notice"><p>%s</p></div>', $message );
				delete_transient( 'woo_variation_duplicator_cloned_ids' );
			}
			if ( $limit_exceed ) {
				printf( '<div class="inline notice variation-duplicator-for-woocommerce-notice error"><p>%s</p></div>', esc_html__( 'Variation clone limit exceed.', 'variation-duplicator-for-woocommerce' ) );
				delete_transient( 'woo_variation_duplicator_exceed_clone_limit' );
			}
		}

		public static function instruction() {
			printf( '<a target="_blank" href="https://getwooplugins.com/documentation/variation-duplicator-for-woocommerce/#duplicate-variation-in-a-click" class="variation-duplicator-for-woocommerce-works">%s</a>', esc_html__( 'How variation duplicator works', 'variation-duplicator-for-woocommerce' ) );
		}

		public static function load_variations() {
			ob_start();

			check_ajax_referer( 'load-variations', 'security' );

			if ( ! current_user_can( 'edit_products' ) || empty( $_POST['product_id'] ) ) {
				wp_die( - 1 );
			}

			// Set $post global so its available, like within the admin screens.
			global $post;

			$loop           = 0;
			$product_id     = absint( $_POST['product_id'] );
			$post           = get_post( $product_id ); // phpcs:ignore
			$product_object = wc_get_product( $product_id );
			$per_page       = ! empty( $_POST['per_page'] ) ? absint( $_POST['per_page'] ) : 10;
			$page           = ! empty( $_POST['page'] ) ? absint( $_POST['page'] ) : 1;
			$variations     = wc_get_products(
				array(
					'status'  => array( 'private', 'publish' ),
					'type'    => 'variation',
					'parent'  => $product_id,
					'limit'   => $per_page,
					'page'    => $page,
					'orderby' => array(
						'menu_order' => 'ASC',
						'ID'         => 'DESC',
					),
					'return'  => 'objects',
				)
			);

			if ( $variations ) {
				do_action( 'woo_variation_duplicator_load_variations', $product_object, $variations );
				wc_render_invalid_variation_notice( $product_object );

				foreach ( $variations as $variation_object ) {
					$variation_id   = $variation_object->get_id();
					$variation      = get_post( $variation_id );
					$variation_data = array_merge( get_post_custom( $variation_id ), wc_get_product_variation_attributes( $variation_id ) ); // kept for BW compatibility.
					include WC_ABSPATH . 'includes/admin/meta-boxes/views/html-variation-admin.php';
					$loop ++;
				}
			}
			wp_die();
		}

		public static function add_checkbox( $variation ) {
			printf( '<label class="clone-checkbox">
<input class="no-track-change variation_is_cloneable" type="checkbox" name="variation_is_cloneable[]" value="%d"><span class="clone-text" data-clone-text="%s" data-clone-save-text="%s"></span>
%s </label> ',
				esc_attr( absint( $variation->ID ) ),
				esc_attr__( 'Duplicate', 'variation-duplicator-for-woocommerce' ),
				esc_attr__( 'Save before duplicate', 'variation-duplicator-for-woocommerce' ),
				wc_help_tip( esc_attr__( 'If you want to duplicate this variation you have to save this variation first.', 'variation-duplicator-for-woocommerce' ) ) );
		}

		public static function add_dropdown() {
			printf( '<option class="woo_variation_duplicate_option" value="woo_variation_duplicate">%s</option>', esc_html__( 'Duplicate variations', 'variation-duplicator-for-woocommerce' ) );
		}

		public static function clone( $bulk_action, $data, $product_id, $variations ) {

			if ( 'woo_variation_duplicate' === $bulk_action && isset( $data['items'] ) && ! empty( $data['items'] ) ) {

				$get_limit   = absint( $data['times'] );
				$exceed      = wc_string_to_bool( $data['exceed'] );
				$clone_limit = absint( apply_filters( 'woo_variation_duplicator_clone_limit', 9 ) );
				$times       = ( $get_limit > $clone_limit ) ? 1 : $get_limit;

				if ( $exceed ) {
					set_transient( 'woo_variation_duplicator_exceed_clone_limit', 1 );
				}

				$variation_ids = array_map( 'absint', $data['items'] );

				$cloned_ids = [];

				foreach ( $variation_ids as $variation_id ) {

					for ( $i = 1; $i <= $times; $i ++ ) {

						// Main Variation
						$variation_object = wc_get_product_object( 'variation', $variation_id );

						$cloned_variation_object = wc_get_product_object( 'variation', $variation_id );
						$cloned_variation_object->set_props( [ 'id' => 0 ] );
						$cloned_variation_object->set_parent_id( $product_id );
						$cloned_variation_id = $cloned_variation_object->save();

						array_push( $cloned_ids, $cloned_variation_id );

						do_action( 'woo_variation_duplicator_variation_save', $cloned_variation_id, $variation_id, $cloned_variation_object, $variation_object, $variation_ids, $i );
					}
				}

				do_action( 'woo_variation_duplicator_variation_saved', $variation_ids, $cloned_ids );

				set_transient( 'woo_variation_duplicator_cloned_ids', $cloned_ids );

			}
		}
	}

	Woo_Variation_Clone::init();
endif;