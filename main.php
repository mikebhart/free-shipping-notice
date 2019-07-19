<?php 

/*

Plugin name: Free Shipping Notice
Description: Display a notice on the cart and checkout Woocommerce pages.  The notice indicates how much more the customer needs to spend to enable free shipping.  Needs a Shipping Zone setup for 'United Kingdom'.
Author: Mike Hart
Version: 1.0.0

*/

function get_free_shipping_minimum($zone_name = 'United Kingdom') {

  if ( ! isset( $zone_name ) ) return null;

  $result = null;
  $zone = null;

  $zones = WC_Shipping_Zones::get_zones();
  foreach ( $zones as $z ) {
    if ( $z['zone_name'] == $zone_name ) {
      $zone = $z;
    }
  }

  if ( $zone ) {
    $shipping_methods_nl = $zone['shipping_methods'];
    $free_shipping_method = null;
    foreach ( $shipping_methods_nl as $method ) {
      if ( $method->id == 'free_shipping' ) {
        $free_shipping_method = $method;
        break;
      }
    }

    if ( $free_shipping_method ) {
      $result = $free_shipping_method->min_amount;
    }
  }

  return $result;

}


function show_free_shipping_notice() {
	
	$free_shipping_amount = get_free_shipping_minimum();
    $cart = WC()->cart->subtotal;
    $remaining = $free_shipping_amount - $cart;
   
    if( $free_shipping_amount > $cart ){
   		 $notice = sprintf( "Add %s worth more products to get free shipping", wc_price($remaining));
         wc_print_notice( $notice , 'notice' );
    }

}
 
function free_ship_notice_cart_message() {
	show_free_shipping_notice();
}
add_action( 'woocommerce_after_order_notes', 'free_ship_notice_cart_message' );


function free_ship_notice_checkout_message( ) {
	show_free_shipping_notice();
}
add_action('woocommerce_before_cart_contents', 'free_ship_notice_checkout_message');


