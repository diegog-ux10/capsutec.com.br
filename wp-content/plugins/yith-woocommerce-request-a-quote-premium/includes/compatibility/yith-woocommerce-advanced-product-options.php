<?php //phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * Implements the YWRAQ_Avanced_Product_Options class.
 *
 * @class   YWRAQ_Avanced_Product_Options
 * @since   1.0.0
 * @author  YITH
 * @package YITH WooCommerce Request A Quote Premium
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'YWRAQ_Avanced_Product_Options' ) ) {

	/**
	 * Class YWRAQ_Avanced_Product_Options
	 */
	class YWRAQ_Avanced_Product_Options {

		/**
		 * Single instance of the class
		 *
		 * @var YWRAQ_WooCommerce_Product_Addon
		 */

		protected static $instance;

		/**
		 * Session object
		 *
		 * @var WC_Session
		 */
		public $session_class;


		/**
		 * Content of session
		 *
		 * @var array
		 */
		public $raq_content = array();


		/**
		 * Returns single instance of the class
		 *
		 * @return YWRAQ_WooCommerce_Product_Addon
		 * @since 1.0.0
		 */
		public static function get_instance() {
			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new self();
		}

		/**
		 * Constructor
		 *
		 * Initialize plugin and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 */
		public function __construct() {

			add_filter( 'ywraq_ajax_validate_uploaded_files', array( $this, 'validate_uploaded_files' ), 10 );
			add_filter( 'ywraq_ajax_add_item_prepare', array( $this, 'ajax_add_item' ), 10, 2 );
			add_filter( 'ywraq_add_item', array( $this, 'add_item' ), 10, 2 );

			add_filter( 'ywraq_request_quote_view_item_data', array( $this, 'request_quote_view' ), 10, 4 );
			add_action( 'ywraq_order_adjust_price', array( $this, 'adjust_price' ), 10, 2 );
			add_action( 'ywraq_quote_adjust_price', array( $this, 'adjust_price' ), 10, 2 );
			add_action( 'ywraq_from_cart_to_order_item', array( $this, 'add_order_item_meta' ), 10, 3 );
			add_action( 'ywraq_item_data', array( $this, 'add_raq_item_meta' ), 10, 3 );
			add_filter( 'ywraq_add_to_cart', array( $this, 'add_to_cart' ), 10, 2 );

			add_filter( 'ywraq_cart_to_order_args', array( $this, 'cart_to_order_addons' ), 10, 3 );

			// email front end price.
			add_filter( 'ywraq_exists_in_list', array( $this, 'exists_in_list' ), 10, 5 );
			add_filter( 'ywraq_quote_item_id', array( $this, 'quote_item_id' ), 10, 3 );
			add_filter( 'ywraq_order_cart_item_data', array( $this, 'remove_price' ), 90, 3 );
			add_filter( 'ywraq_filter_cart_item_before_add_to_cart', array( $this, 'update_item_product' ), 90, 2 );
			add_filter( 'ywraq_raq_content_before_add_item', array( $this, 'set_up_raq_content_on_sold_individually' ), 10, 3 );
			add_filter( 'yith_ywraq_item_class', array( $this, 'add_data_wapo_parent_info' ), 10, 3 );
			add_filter( 'woocommerce_order_item_name', array( $this, 'hide_name_and_qty_on_sold_individually' ), 10, 2 );
			add_filter( 'woocommerce_order_item_quantity_html', array( $this, 'hide_name_and_qty_on_sold_individually' ), 10, 2 );

			/* Addon image replacement v2 */
			add_filter( 'ywraq_product_image', array( $this, 'get_addon_image' ), 10, 2 );
			add_filter( 'woocommerce_admin_order_item_thumbnail', array( $this, 'change_addon_image' ), 10, 3 );

		}

		/**
		 * Add metadata inside the cart item
		 *
		 * @param   array    $cart_item  Cart item.
		 * @param   WC_Order $order      The order item.
		 *
		 * @return mixed
		 */
		public function update_item_product( $cart_item, $order ) {

			if ( $order && ! WC()->session->get( 'order_awaiting_payment', $order->get_id() ) ) {
				return $cart_item;
			}

			if ( isset( $cart_item['meta'] ) && ! isset( $cart_item['yith_wapo_options'] ) ) {
				$meta = $cart_item['meta'];
				foreach ( $meta as $current_meta ) {
					if ( isset( $current_meta, $current_meta['value'], $current_meta['key'] ) && '_ywraq_wc_ywapo' === $current_meta['key'] ) {
						foreach ( $current_meta['value'] as $value ) {
							foreach ( $value as &$val ) {
								$val = urldecode( $val );
							}
							$cart_item['yith_wapo_options'][] = $value;
						}
					}
				}
			}

			return $cart_item;
		}


		/**
		 * Validate upload files
		 *
		 * @param   array $error_list  Error list.
		 *
		 * @return array|bool|mixed
		 */
		public function validate_uploaded_files( $error_list ) {
			wc_clear_notices();

			if ( ! empty( $_FILES ) ) {

				$upload_data = array();

				foreach ( $_FILES as $group_key => $group_values ) {

					foreach ( $group_values as $prop_key => $prop_values ) {

						foreach ( $prop_values as $field_key => $field_value ) {

							$upload_data[ $group_key ][ $field_key ][ $prop_key ] = $field_value;

						}
					}
				}

				if ( class_exists( 'YITH_WAPO_Type' ) ) {
					$yith_wapo_frontend = YITH_WAPO()->frontend;
					foreach ( $upload_data as $key => $single_data ) {
						$error_list = YITH_WAPO_Type::checkUploadedFilesError(
							$yith_wapo_frontend,
							$single_data,
							true,
							$key
						);
					}
				}
			}

			return $error_list;
		}


		/**
		 * Prepare the item before adding it to list
		 *
		 * @param   array $postdata    Postdada.
		 * @param   int   $product_id  Product id.
		 *
		 * @return array
		 */
		public function ajax_add_item( $postdata, $product_id ) {

			if ( empty( $postdata ) ) {
				$postdata = array();
			}

			$postdata['add-to-cart'] = $product_id;
			$yith_wapo_frontend      = false;

			// 2.x
			if ( class_exists( 'YITH_WAPO_Cart' ) ) {
				$t1 = YITH_WAPO_Cart()->add_cart_item_data( null, $product_id, $postdata );
				// 1.x
			} else {
				$yith_wapo_frontend = YITH_WAPO()->frontend;
				$t1                 = $yith_wapo_frontend ? $yith_wapo_frontend->add_cart_item_data(
					null,
					$product_id,
					$postdata
				) : array();
			}

			if ( defined( 'YITH_WAPO_PREMIUM' ) && $yith_wapo_frontend ) {
				$t2                               = ! function_exists( 'yith_wapo_get_option_info' ) ? $yith_wapo_frontend->add_cart_item_data(
					null,
					$product_id,
					$postdata,
					1
				) : array( 'yith_wapo_options' => array() );
				$t['yith_wapo_options']           = array_merge( $t1['yith_wapo_options'], $t2['yith_wapo_options'] );
				$t['yith_wapo_sold_individually'] = empty( $t2['yith_wapo_options'] ) ? '' : 1;
			} else {
				$t['yith_wapo_options'] = $t1['yith_wapo_options'] ?? array();
			}

			if ( ! empty( $t ) ) {
				$postdata = array_merge( $t, $postdata );
			}

			return $postdata;
		}

		/**
		 * Request a quote view
		 *
		 * @param   array      $item_data   Item data.
		 * @param   array      $raq         Raq content.
		 * @param   WC_Product $_product    Product.
		 * @param   bool       $show_price  Show price or not.
		 *
		 * @return array
		 */
		public function request_quote_view( $item_data, $raq, $_product, $show_price = true ) {

			if ( isset( $raq['yith_wapo_options'] ) ) {

				// 2.x
				if ( function_exists( 'yith_wapo_get_option_info' ) ) {

					foreach ( $raq['yith_wapo_options'] as $key => $addon ) {

						$addon_key       = key( $addon );
						$option_value    = $addon[ $addon_key ];
						$addon_option_id = explode( '-', $addon_key );

						if ( isset( $addon_option_id[1] ) ) {
							$addon_id  = $addon_option_id[0];
							$option_id = $addon_option_id[1];
						} else {
							$addon_id  = $addon_option_id[0];
							$option_id = $option_value;
						}

						if ( apply_filters( 'ywraq_wapo_force_empty_value_show', '' !== $option_value, $addon ) ) {
							$info         = yith_wapo_get_option_info( $addon_id, $option_id );
							$option_price = yith_wapo_get_option_price(
								$_product->get_id(),
								$addon_id,
								$option_id,
								(int) $option_value
							);
							$price        = '' !== $option_price['price_sale'] ? $option_price['price_sale'] : $option_price['price'];

							$price_html = '';
							if ( ! empty( $price ) && get_option( 'ywraq_hide_price' ) !== 'yes' && apply_filters(
								'yith_wapo_show_addons_price_on_raq',
								$show_price,
								$price,
								$addon
							) ) {
								$price_html  = $price > 0 ? ' ( +' : ' (';
								$price_html .= wp_strip_all_tags( wc_price( $price ) ) . ' ) ';
							}

							$key = ! empty( $info['addon_label'] ) ? $info['addon_label'] : $info['label'];
							$key = apply_filters( 'ywraq_addon_item_name_view', $key, $info );

							if ( in_array(
								$info['addon_type'],
								array( 'checkbox', 'color', 'label', 'radio', 'select' ),
								true
							) ) {
								$value = $info['label'];
							} elseif ( 'product' === $info['addon_type'] ) {
								$option_product_info = explode( '-', $option_value );
								$option_product_id   = $option_product_info[1];
								$option_product      = wc_get_product( $option_product_id );
								$value               = $option_product instanceof WC_Product ? $option_product->get_title() : '';

								// product prices.
								$product_price = $option_product instanceof WC_Product ? $option_product->get_price() : 0;
								if ( 'product' === $info['price_method'] ) {
									$option_price = $product_price;
								} elseif ( 'discount' === $info['price_method'] ) {
									$option_discount_value = $option_price;
									if ( is_array( $option_discount_value ) ) {
										$option_discount_value = $option_discount_value['price'];
									}
									$option_price = $product_price - $option_discount_value;
									if ( 'percentage' === $info['price_type'] ) {
										$option_price = $product_price - ( ( $product_price / 100 ) * $option_discount_value );
									}
								}

								if ( ! empty( $price ) && get_option( 'ywraq_hide_price' ) !== 'yes' && apply_filters(
									'yith_wapo_show_addons_price_on_raq',
									$show_price,
									$option_price,
									$addon
								) ) {
									$price_html  = $price > 0 ? ' ( +' : ' (';
									$price_html .= wp_strip_all_tags( wc_price( $option_price ) ) . ' ) ';
								}
							} elseif ( 'file' === $info['addon_type'] ) {
								$value = '<a href="' . $option_value . '" target="_blank">' . _x(
									'Attached file',
									'Integration: product add-ons attachment',
									'yith-woocommerce-request-a-quote'
								) . '<


/a>';

							} else {
								$value = $option_value;
							}

							if ( YITH_WAPO::$is_wpml_installed ) {
								$key   = YITH_WAPO_WPML::string_translate( $key );
								$value = YITH_WAPO_WPML::string_translate( $value );
							}

							$item_data[] = array(
								'key'   => $key . apply_filters( 'yith_wapo_cart_item_addon_price', $price_html ),
								'value' => urldecode( $value ),
							);
						}
					}

					// 1.x
				} else {

					foreach ( $raq['yith_wapo_options'] as $_r ) {
						$price = '';
						if ( get_option( 'ywraq_hide_price' ) !== 'yes' && $show_price && apply_filters(
							'yith_wapo_show_addons_1_price_on_raq',
							$show_price,
							$_r['price'],
							$_r
						) ) {
							$price .= $_r['price'] > 0 ? ' ( +' : ' (';
							$price .= wp_strip_all_tags( wc_price( $_r['price'] ) ) . ' ) ';
						}
						if ( class_exists( 'YITH_WAPO_WPML' ) ) {
							$key = YITH_WAPO_WPML::string_translate( $_r['name'] );
							if ( strpos( $_r['value'], 'Attached file' ) ) {
								$array = new SimpleXMLElement( $_r['value'] );
								$link  = $array['href'];
								$value = '<a href="' . $link . '" target="_blank">' . __(
									'Attached file',
									'yith-woocommerce-product-add-ons'
								) . '</a>';
							} else {
								$value = YITH_WAPO_WPML::string_translate( $_r['value'] );
							}
						} else {
							$key   = $_r['name'];
							$value = $_r['value'];
						}

						$item_data[] = array(
							'key'   => $key . apply_filters( 'yith_wapo_cart_item_addon_price', $price ),
							'value' => urldecode( $value ),
						);

					}
				}
			}

			return $item_data;
		}

		/**
		 * Add item to list.
		 *
		 * @param   array $raq          RAQ list.
		 * @param   array $product_raq  Product to add.
		 *
		 * @return mixed
		 */
		public function add_item( $raq, $product_raq ) {
			if ( isset( $product_raq['yith_wapo_options'] ) ) {
				/* SOLD INDIVIDUALLY SUPPORT */
				if ( YITH_WAPO_VERSION < '3.0.0' ) {
					if ( ! empty( $product_raq['yith_wapo_sold_individually'] ) ) {
						$individual_items = array();
						foreach ( $product_raq['yith_wapo_options'] as $key => $option ) {
							if ( ! empty( $option['sold_individually'] ) && 1 == $option['sold_individually'] ) { //phpcs:ignore
								$individual_items[] = $option;
								unset( $product_raq['yith_wapo_options'][ $key ] );
							}
						}
						$raq['yith_wapo_options']                 = $product_raq['yith_wapo_options'];
						$raq['yith_wapo_individually_sold_items'] = $individual_items;
					} else {
						$raq['yith_wapo_options'] = $product_raq['yith_wapo_options'];
					}
				} else {
					if ( ! empty( $product_raq['yith_wapo_sell_individually'] ) ) {
						$individual_items = array();
						foreach ( $product_raq['yith_wapo_sell_individually'] as $key => $option ) {
							foreach ( $product_raq['yith_wapo'] as $wapo_value ) {
								if ( isset( $wapo_value[ $key ] ) ) {
									$individual_items[ $key ] = $wapo_value[ $key ];
								}
							}
						}
						$raq['yith_wapo_options']                 = $product_raq['yith_wapo_options'];
						$raq['yith_wapo_individually_sold_items'] = $individual_items;
					} else {
						$raq['yith_wapo_options'] = $product_raq['yith_wapo_options'];
					}
				}

				if ( isset( $product_raq['yith_wapo_product_img'] ) && ! empty( $product_raq['yith_wapo_product_img'] ) ) {
					$raq['yith_wapo_product_img'] = $product_raq['yith_wapo_product_img'];
				}

				if ( isset( $product_raq['yith_wapo_product_img'] ) && ! empty( $product_raq['yith_wapo_product_img'] ) ) {
					$raq['yith_wapo_product_img'] = $product_raq['yith_wapo_product_img'];
				}
			}

			return $raq;
		}

		/**
		 * Request a quote item meta data
		 *
		 * @param   array $item_data   Item data.
		 * @param   array $raq         Raq content.
		 * @param   bool  $show_price  Show price or not.
		 *
		 * @return array
		 */
		public function add_raq_item_meta( $item_data, $raq = null, $show_price = true ) {

			if ( isset( $raq['yith_wapo_options'] ) ) {
				// 2.x
				if ( function_exists( 'yith_wapo_get_option_info' ) ) {

					foreach ( $raq['yith_wapo_options'] as $key => $addon ) {
						if ( isset( $addon['original_value'] ) ) {
							$addon = $addon['original_value'];
						}

						$addon_key       = key( $addon );
						$addon_option_id = explode( '-', $addon_key );
						$option_value    = $addon[ $addon_key ];

						if ( ! isset( $option_value ) ) {
							continue;
						}

						if ( isset( $addon_option_id[1] ) ) {
							$addon_id  = $addon_option_id[0];
							$option_id = $addon_option_id[1];
						} else {
							$addon_id  = $addon_option_id[0];
							$option_id = $option_value;
						}

						$info = yith_wapo_get_option_info( $addon_id, $option_id );

						if ( in_array(
							$info['addon_type'],
							array( 'checkbox', 'color', 'label', 'radio', 'select' ),
							true
						) && ! empty( $info['addon_label'] ) ) {
							$name = $info['addon_label'];
						} else {
							$name = $info['label'];
						}

						$name = apply_filters( 'ywraq_addon_item_name', $name, $info );

						if ( in_array(
							$info['addon_type'],
							array( 'checkbox', 'color', 'label', 'radio', 'select' ),
							true
						) ) {
							$value = $info['label'];
						} elseif ( 'product' === $info['addon_type'] ) {
							$option_product_info = explode( '-', $option_value );
							$option_product_id   = $option_product_info[1];
							$option_product      = wc_get_product( $option_product_id );
							$value               = $option_product->get_title();

						} else {
							$value = $option_value;

						}

						$item_data[] = array(
							'key'   => $name,
							'value' => urldecode( $value ),
						);
					}

					// 1.x
				} else {

					foreach ( $raq['yith_wapo_options'] as $_r ) {
						$price = '';
						if ( $show_price && $_r['price'] > 0 ) {
							$price = apply_filters(
								'yith_wapo_order_item_addon_price',
								' ( ' . wp_strip_all_tags( wc_price( $_r['price'] ) ) . ' ) '
							);
						}
						if ( class_exists( 'YITH_WAPO_WPML' ) ) {
							$key   = YITH_WAPO_WPML::string_translate( $_r['name'] );
							$value = YITH_WAPO_WPML::string_translate( $_r['value'] );
						} else {
							$key   = $_r['name'];
							$value = $_r['value'];
						}
						$item_data[] = array(
							'key'   => $key,
							'value' => urldecode( $value ) . $price,
						);
					}
				}
			}

			return $item_data;
		}

		/**
		 * Return the price of the product.
		 *
		 * @param   array      $values    .
		 * @param   WC_Product $_product  .
		 * @param   string     $taxes     .
		 */
		public function adjust_price( $values, $_product, $taxes = 'inc' ) {

			if ( isset( $values['yith_wapo_options'] ) ) {
				$addon_price = 0;

				// 2.x
				if ( function_exists( 'yith_wapo_get_option_info' ) ) {

					foreach ( $values['yith_wapo_options'] as $key => $addon ) {
						if ( isset( $values['yith_wapo_sold_individually'] ) && $values['yith_wapo_sold_individually'] ) {
							$addon = $addon['original_value'];
						}
						$addon_key       = key( $addon );
						$addon_option_id = explode( '-', $addon_key );
						$option_value    = $addon[ $addon_key ];

						if ( '' === $option_value ) {
							continue;
						}

						if ( isset( $addon_option_id[1] ) ) {
							$addon_id  = $addon_option_id[0];
							$option_id = $addon_option_id[1];
						} else {
							$addon_id  = $addon_option_id[0];
							$option_id = $option_value;
						}
						$info         = yith_wapo_get_option_info( $addon_id, $option_id );
						$option_price = yith_wapo_get_option_price(
							$_product->get_id(),
							$addon_id,
							$option_id,
							(int) $option_value
						);
						$price        = '' !== $option_price['price_sale'] ? $option_price['price_sale'] : $option_price['price'];

						if ( 'product' === $info['addon_type'] ) {
							$option_product_info = explode( '-', $option_value );
							$option_product_id   = $option_product_info[1];
							$option_product      = wc_get_product( $option_product_id );

							// product prices.
							$product_price = $option_product instanceof WC_Product ? $option_product->get_price() : 0;

							if ( 'product' === $info['price_method'] ) {
								$price = $product_price;
							} elseif ( 'discount' === $info['price_method'] ) {
								$option_discount_value = $option_price;
								if ( is_array( $option_discount_value ) ) {
									$option_discount_value = $option_discount_value['price'];
								}
								$price = $product_price - $option_discount_value;
								if ( 'percentage' === $info['price_type'] ) {
									$price = $product_price - ( ( $product_price / 100 ) * $option_discount_value );
								}
							}
						}
						if ( isset( $values['yith_wapo_sold_individually'] ) && $values['yith_wapo_sold_individually'] ) {
							$main_product_price = 0;
						} else {
							$main_product_price = ! empty( $_product->get_price() ) ? $_product->get_price() : 0;
						}

						$total_price = floatval( $price ) + $main_product_price;
						if ( function_exists( 'yith_wapo_get_currency_rate' ) ) {
							$currency_rate = yith_wapo_get_currency_rate();
							$total_price   = ( floatval( $price ) + $main_product_price ) / $currency_rate;
						}

						$_product->set_price( $total_price );

					}

					// 1.x
				} else {
					if ( isset( $values['yith_wapo_individual_item'] ) && $values['yith_wapo_individual_item'] ) {
						foreach ( $values['yith_wapo_options'] as $_r ) {
							$addon_price = 'inc' === $taxes ? $addon_price + floatval( $_r['price_original'] ) : $addon_price + floatval( $_r['price'] );
						}
						$_product->set_price( $addon_price );
					} else {
						if ( $_product->get_price() > 0 ) {
							foreach ( $values['yith_wapo_options'] as $_r ) {
								$addon_price = 'inc' === $taxes ? $_r['price_original'] : $_r['price'];
								$_product->set_price( floatval( $addon_price ) + $_product->get_price() );
							}
						}
					}
				}
			}
		}

		/**
		 * Add the item to cart
		 *
		 * @param   array $cart_item_data  Cart item data.
		 * @param   array $item            List item.
		 *
		 * @return mixed
		 */
		public function add_to_cart( $cart_item_data, $item ) {

			if ( isset( $item['item_meta']['_ywraq_wc_ywapo'] ) ) {
				$addons = maybe_unserialize( $item['item_meta']['_ywraq_wc_ywapo'] );
				if ( ! empty( $addons ) ) {
					$ad                                  = maybe_unserialize( $addons[0] );
					$cart_item_data['yith_wapo_options'] = $ad;
					$cart_item_data['add-to-cart']       = $item['product_id'];
				}
			}

			return $cart_item_data;

		}

		/**
		 * Add to cart from request a quote.
		 *
		 * @param   WC_Cart $new_cart           New Cart.
		 * @param   array   $values             .
		 * @param   array   $item               List item.
		 * @param   string  $new_cart_item_key  Cart item key.
		 *
		 * @return mixed
		 */
		public function add_to_cart_from_request( $new_cart, $values, $item, $new_cart_item_key ) {

			$cart = &$new_cart->cart_contents;

			if ( isset( $cart[ $new_cart_item_key ] ) && isset( $values['yith_wapo_options'] ) ) {
				$cart[ $new_cart_item_key ]['yith_wapo_options'] = $values['yith_wapo_options'];
				$ywapo_frontend                                  = YITH_WAPO()->frontend;
				$ywapo_frontend->cart_adjust_price( $cart[ $new_cart_item_key ] );
			}

			return $new_cart;

		}

		/**
		 * Add order item meta when creating the order
		 *
		 * @param   array  $values         .
		 * @param   string $cart_item_key  Cart item key.
		 * @param   int    $item_id        Item id.
		 */
		public function add_order_item_meta( $values, $cart_item_key, $item_id ) {
			if ( ! empty( $values['yith_wapo_options'] ) ) {

				// 2.x
				if ( function_exists( 'yith_wapo_get_option_info' ) ) {
					foreach ( $values['yith_wapo_options'] as $key => $addon ) {
						if ( isset( $values['yith_wapo_sold_individually'] ) && $values['yith_wapo_sold_individually'] ) {
							$addon = $addon['original_value'];
						}
						$addon_key       = key( $addon );
						$addon_option_id = explode( '-', $addon_key );
						$option_value    = $addon[ $addon_key ];

						if ( isset( $addon_option_id[1] ) ) {
							$addon_id  = $addon_option_id[0];
							$option_id = $addon_option_id[1];
						} else {
							$addon_id  = $addon_option_id[0];
							$option_id = $option_value;
						}

						if ( '' === $option_value ) {
							continue;
						}

						$info         = yith_wapo_get_option_info( $addon_id, $option_id );
						$option_price = yith_wapo_get_option_price( $item_id, $addon_id, $option_id );
						$price        = '' !== $option_price['price_sale'] ? $option_price['price_sale'] : $option_price['price'];

						if ( in_array(
							$info['addon_type'],
							array( 'checkbox', 'color', 'label', 'radio', 'select' ),
							true
						) && ! empty( $info['addon_label'] ) ) {
							$name = $info['addon_label'];
						} else {
							$name = $info['label'];
						}

						$name = apply_filters( 'ywraq_addon_item_name_order', $name, $info );

						if ( in_array(
							$info['addon_type'],
							array( 'checkbox', 'color', 'label', 'radio', 'select' ),
							true
						) ) {
							$value = $info['label'];
						} elseif ( 'product' === $info['addon_type'] ) {
							$option_product_info = explode( '-', $option_value );
							$option_product_id   = $option_product_info[1];
							$option_product      = wc_get_product( $option_product_id );
							$value               = $option_product->get_title();

						} else {
							$value = $option_value;
						}

						if ( ! empty( $price ) ) {
							$value .= ' (+' . wc_price( $price ) . ')';
						}

						if ( isset( $values['yith_wapo_sold_individually'] ) && $values['yith_wapo_sold_individually'] && apply_filters(
							'yith_ywraq_wapo_add_sold_individually_tag',
							true,
							$values,
							$addon
						) ) {
							$name = _x(
								'Individually sold add-on',
								'notice on admin order item meta',
								'yith-woocommerce-request-a-quote'
							) . ' - ' . $name;
						}

						wc_add_order_item_meta( $item_id, $name, $value );
					}
					if ( isset( $values['yith_wapo_product_img'] ) ) {
						wc_add_order_item_meta(
							$item_id,
							'_ywraq_wc_ywapo_replaced_image',
							$values['yith_wapo_product_img']
						);
					}
					wc_add_order_item_meta( $item_id, '_ywraq_wc_ywapo', $values['yith_wapo_options'] );
					if ( isset( $values['yith_wapo_product_img'] ) ) {
						wc_add_order_item_meta(
							$item_id,
							'_ywraq_wc_ywapo_replaced_image',
							$values['yith_wapo_product_img']
						);
					}

					// 1.x
				} else {
					foreach ( $values['yith_wapo_options'] as $addon ) {
						$name = class_exists( 'YITH_WAPO_WPML' ) ? YITH_WAPO_WPML::string_translate( $addon['name'] ) : $addon['name'];
						if ( class_exists( 'YITH_WAPO_WPML' ) ) {
							$name  = YITH_WAPO_WPML::string_translate( $addon['name'] );
							$value = YITH_WAPO_WPML::string_translate( $addon['value'] );
						} else {
							$name  = $addon['name'];
							$value = urldecode( $addon['value'] );
						}
						if ( $addon['price'] > 0 ) {
							$name .= apply_filters(
								'yith_wapo_order_item_addon_price',
								' (' . wp_strip_all_tags( wc_price( $addon['price'] ) ) . ')'
							);
							if ( isset( $values['yith_wapo_sold_individually'] ) && $values['yith_wapo_sold_individually'] && apply_filters(
								'yith_ywraq_wapo_add_sold_individually_tag',
								true,
								$values,
								$addon
							) ) {
								$name .= ' ' . _x(
									'* Sold invidually',
									'notice on admin order item meta',
									'yith-woocommerce-request-a-quote'
								);
							}
						}
						wc_add_order_item_meta( $item_id, $name, $value );
					}
					wc_add_order_item_meta( $item_id, '_ywraq_wc_ywapo', $values['yith_wapo_options'] );
				}
			}

		}

		/**
		 * Cart to order
		 *
		 * @param   array   $args           .
		 * @param   string  $cart_item_key  Cart item key.
		 * @param   array   $values         .
		 * @param   WC_Cart $new_cart       .
		 * @depreacted since 4.0
		 */
		public function cart_to_order_args( $args, $cart_item_key, $values, $new_cart ) {

		}

		/**
		 * Check if the product exists in list.
		 *
		 * @param   bool  $return        Value to filter.
		 * @param   int   $product_id    .
		 * @param   int   $variation_id  .
		 * @param   array $postdata      .
		 * @param   array $raqdata       .
		 *
		 * @return bool
		 */
		public function exists_in_list( $return, $product_id, $variation_id, $postdata, $raqdata ) {

			if ( $postdata ) {

				$this->ajax_add_item( $postdata, $product_id );
				if ( isset( $postdata['yith_wapo_options'] ) && ! empty( $postdata['yith_wapo_options'] ) ) {
					$str = '';
					foreach ( $postdata['yith_wapo_options'] as $ad ) {
						$str .= $ad['name'] . $ad['value'];
					}

					$key_to_find = md5( $product_id . $variation_id . $str );

					if ( array_key_exists( $key_to_find, $raqdata ) ) {
						$return = true;
					}
				}
			} else {

				if ( class_exists( 'YITH_WAPO_Type' ) ) {
					$addons = YITH_WAPO_Type::getAllowedGroupTypes( $product_id );
					if ( ! empty( $addons ) ) {
						$return = false;
					}
				}
			}

			return $return;
		}

		/**
		 * Quote item id
		 *
		 * @param   int   $item_id      Item id.
		 * @param   array $product_raq  .
		 * @param   it    $product_id   Product id.
		 *
		 * @return string
		 */
		public function quote_item_id( $item_id, $product_raq, $product_id ) {
			$str = '';

			$product = wc_get_product( $product_id );
			if ( $product->is_type( 'grouped' ) ) {
				return $item_id;
			}
			// 2.x
			if ( function_exists( 'yith_wapo_get_option_info' ) ) {

				foreach ( $product_raq['yith_wapo_options'] as $key => $addon ) {
					$addon_key       = key( $addon );
					$addon_option_id = explode( '-', $addon_key );
					$option_value    = $addon[ $addon_key ];

					if ( isset( $addon_option_id[1] ) ) {
						$addon_id  = $addon_option_id[0];
						$option_id = $addon_option_id[1];
					} else {
						$addon_id  = $addon_option_id[0];
						$option_id = $option_value;
					}

					$info = yith_wapo_get_option_info( $addon_id, $option_id );
					$str .= $info['addon_label'] . $option_value;
				}

				// 1.x
			} else {

				$addons = YITH_WAPO_Type::getAllowedGroupTypes( $product_raq['product_id'] );
				if ( ! empty( $addons ) && isset( $product_raq['yith_wapo_options'] ) && ! empty( $product_raq['yith_wapo_options'] ) ) {

					foreach ( $product_raq['yith_wapo_options'] as $ad ) {
						$str .= $ad['name'] . $ad['value'];
					}
				}
			}

			if ( isset( $product_raq['variation_id'] ) ) {
				$item_id = md5( $product_raq['product_id'] . $product_raq['variation_id'] . $str );
			} else {
				$item_id = md5( $product_id . $str );
			}

			return $item_id;
		}

		/**
		 * Remove action.
		 */
		public function remove_action() {
			$yith_wapo_frontend = YITH_WAPO()->frontend;
			remove_filter( 'woocommerce_add_cart_item_data', array( $yith_wapo_frontend, 'add_cart_item_data' ) );
		}

		/**
		 * Remove price.
		 *
		 * @param   array    $cart_item  .
		 * @param   array    $item       .
		 * @param   WC_Order $order      .
		 *
		 * @return array
		 */
		public function remove_price( $cart_item, $item, $order ) {

			if ( isset( $cart_item['yith_wapo_options'] ) ) {
				$new_cart_item = array();
				foreach ( $cart_item['yith_wapo_options'] as $k => $opt ) {
					if ( isset( $opt['value'] ) ) {
						$opt['value'] = urldecode( $opt['value'] );
					}
					if ( isset( $opt['price_original'] ) ) {
						$opt['price_original'] = 0;
						$new_cart_item[ $k ]   = $opt;
					}
				}

				$cart_item['yith_wapo_options'] = $new_cart_item;
			}

			return $cart_item;
		}

		/**
		 * Add data to parent.
		 *
		 * @param   string $classes      Classes.
		 * @param   array  $raq_content  Raq list.
		 * @param   string $key          .
		 *
		 * @return mixed|string
		 */
		public function add_data_wapo_parent_info( $classes, $raq_content, $key ) {
			if ( isset( $raq_content[ $key ]['yith_wapo_parent'] ) && 1 == $raq_content[ $key ]['yith_wapo_parent'] ) { //phpcs:ignore
				$classes .= ' yith-wapo-parent';
			}

			return $classes;
		}

		/**
		 * Raq content on sold individually
		 *
		 * @param   array $raq_content  List content.
		 *
		 * @return mixed
		 */
		public function set_up_raq_content_on_sold_individually( $raq_content ) {
			if ( YITH_WAPO_VERSION < '3.0.0' ) {
				foreach ( $raq_content as $key => &$raq ) {
					if ( ! empty( $raq['yith_wapo_individually_sold_items'] ) ) {
						$raq['yith_wapo_parent'] = 1;
						foreach ( $raq['yith_wapo_individually_sold_items'] as $individual_item ) {
							$raq_content[ md5( current( $individual_item['original_value'] ) ) ]['product_id']                  = $raq['product_id'];
							$raq_content[ md5( current( $individual_item['original_value'] ) ) ]['quantity']                    = 1;
							$raq_content[ md5( current( $individual_item['original_value'] ) ) ]['yith_wapo_parent_key']        = $key;
							$raq_content[ md5( current( $individual_item['original_value'] ) ) ]['yith_wapo_individual_item']   = 1;
							$raq_content[ md5( current( $individual_item['original_value'] ) ) ]['yith_wapo_sold_individually'] = 1;
							$raq_content[ md5( current( $individual_item['original_value'] ) ) ]['yith_wapo_options'][]         = $individual_item;
						}
						unset( $raq['yith_wapo_individually_sold_items'] );
					}
				}
			} else {
				foreach ( $raq_content as $key => &$raq ) {
					if ( ! empty( $raq['yith_wapo_individually_sold_items'] ) ) {
						$raq['yith_wapo_parent'] = 1;
						foreach ( $raq['yith_wapo_individually_sold_items'] as $addon_key => $addon_value ) {

							$addon_option_id = explode( '-', $addon_key );
							$option_value    = $addon_value;

							if ( '' === $option_value ) {
								continue;
							}

							if ( isset( $addon_option_id[1] ) ) {
								$addon_id  = $addon_option_id[0];
								$option_id = $addon_option_id[1];
							} else {
								$addon_id  = $addon_option_id[0];
								$option_id = $option_value;
							}
							$individual_item                      = yith_wapo_get_option_info( $addon_id, $option_id );
							$individual_item['name']              = yith_wapo_get_option_label( $addon_id, $option_id );
							$individual_item['value']             = $addon_value;
							$individual_item['original_value']    = array( $addon_key => $addon_value );
							$individual_item['sold_individually'] = 1;

							$raq_content[ md5( current( $individual_item['original_value'] ) ) ]['product_id']                  = $raq['product_id'];
							$raq_content[ md5( current( $individual_item['original_value'] ) ) ]['quantity']                    = 1;
							$raq_content[ md5( current( $individual_item['original_value'] ) ) ]['yith_wapo_addons_parent_key'] = $key;
							$raq_content[ md5( current( $individual_item['original_value'] ) ) ]['yith_wapo_individual_addons'] = 1;
							$raq_content[ md5( current( $individual_item['original_value'] ) ) ]['yith_wapo_sold_individually'] = 1;
							$raq_content[ md5( current( $individual_item['original_value'] ) ) ]['yith_wapo_options'][]         = $individual_item;
						}
						unset( $raq['yith_wapo_individually_sold_items'] );
					}
				}
			}

			return $raq_content;
		}

		/**
		 * Hide name and quantity on sold individually
		 *
		 * @param   string $string  .
		 * @param   Object $item    .
		 *
		 * @return mixed|string
		 */
		public function hide_name_and_qty_on_sold_individually( $string, $item ) {
			$yith_wapo_option = $item->get_meta( '_ywraq_wc_ywapo' ) ? current( $item->get_meta( '_ywraq_wc_ywapo' ) ) : '';

			if ( isset( $yith_wapo_option['sold_individually'] ) && $yith_wapo_option['sold_individually'] ) {
				$string = '';
			}

			return $string;
		}

		/**
		 * Show addon image replaced on product page
		 *
		 * @param   string $image  The image URL.
		 * @param   Object $raq    The RAQ content.
		 *
		 * @return mixed|string
		 */
		public function get_addon_image( $image, $raq ) {

			if ( isset( $raq['yith_wapo_product_img'] ) ) {
				$image_id = attachment_url_to_postid( urldecode( $raq['yith_wapo_product_img'] ) );
				$image    = wp_get_attachment_image( $image_id, 'medium' );
			}

			if ( isset( $raq['yith_wapo_sold_individually'] ) ) {
				$image = '';
			}

			return $image;
		}

		/**
		 *    Fee product image on admin order page
		 *
		 * @param   string $product_image  The product image HTML.
		 * @param   int    $item_id        The cart item array.
		 * @param   bool   $item           The cart item key.
		 */
		public function change_addon_image( $product_image, $item_id, $item ) {
			$image_url = urldecode( wc_get_order_item_meta( $item_id, '_ywraq_wc_ywapo_replaced_image' ) );

			if ( $image_url ) {
				$image_id      = attachment_url_to_postid( $image_url );
				$product_image = wp_get_attachment_image( $image_id );
			}

			return $product_image;
		}

		/**
		 * Change price for add-ons sold individually on the order creation.
		 *
		 * @param   array $args           The args.
		 * @param   int   $cart_item_key  The cart item key.
		 * @param   array $values         The values of the cart.
		 */
		public function cart_to_order_addons( $args, $cart_item_key, $values ) {

			if ( isset( $values['yith_wapo_sold_individually'] ) && $values['yith_wapo_sold_individually'] ) {
				$price = 0;
				// 2.x
				if ( function_exists( 'yith_wapo_get_option_info' ) ) {
					$product_id = $values['product_id'] ?? 0;
					foreach ( $values['yith_wapo_options'] as $key => $addon ) {
						if ( isset( $values['yith_wapo_sold_individually'] ) && $values['yith_wapo_sold_individually'] ) {
							$addon = $addon['original_value'];
						}
						$addon_key       = key( $addon );
						$addon_option_id = explode( '-', $addon_key );
						$option_value    = $addon[ $addon_key ];

						if ( isset( $addon_option_id[1] ) ) {
							$addon_id  = $addon_option_id[0];
							$option_id = $addon_option_id[1];
						} else {
							$addon_id  = $addon_option_id[0];
							$option_id = $option_value;
						}

						if ( '' === $option_value ) {
							continue;
						}
						$option_price = yith_wapo_get_option_price( $product_id, $addon_id, $option_id );
						$price       += $option_price['price_sale'] ? $option_price['price_sale'] : $option_price['price'];
					}
				}

				$args['totals']['subtotal'] = $price;
				$args['totals']['total']    = $price;
			}

			return $args;
		}

	}

	/**
	 * Unique access to instance of YWRAQ_WooCommerce_Product_Addon class
	 *
	 * @return YWRAQ_WooCommerce_Product_Addon
	 */
	function YWRAQ_Avanced_Product_Options() { //phpcs:ignore
		return YWRAQ_Avanced_Product_Options::get_instance();
	}

	if ( class_exists( 'YITH_WAPO' ) ) {
		YWRAQ_Avanced_Product_Options();
	}
}
