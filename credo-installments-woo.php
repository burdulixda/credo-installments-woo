<?php

/**
 * Plugin Name: Credo Installments for WooCoommerce
 * Plugin URI: https://credobank.ge/
 * Author: George Burduli
 * Author URI: https://github.com/burdulixda
 * Description: A custom WooCommerce payment gateway for processing installments in Credo bank.
 * Version: 1.0.5
 * License: 1.0
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * text-domain: credo-woo
 * 
 * Class WC_Gateway_Credo file.
 *
 * @package WooCommerce\Credo
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) return;

add_action( 'plugins_loaded', 'credo_payment_init', 11 );
add_filter( 'woocommerce_currencies', 'credo_add_gel_currencies' );
add_filter( 'woocommerce_currency_symbol', 'credo_add_gel_currencies_symbol', 10, 2 );
add_filter( 'woocommerce_payment_gateways', 'add_to_woo_credo_installment_gateway' );

add_action("wp_ajax_credo", "credo_route");
add_action("wp_ajax_nopriv_credo", "credo_route");

function credo_route() {
	if($_SERVER["REQUEST_METHOD"] === "GET") {
		$data = $_GET["data"];
		$data = base64_decode($data);
		$html = '<form action="https://ganvadeba.credo.ge/widget/" method="post" style="display : none"><input type="hidden" name="credoinstallment" value='. $data .' /><input type="submit" value="go" />	</form>';
		$script = "
			<script>
				const form = document.querySelector('form');
				window.onload = () => {
					form.submit();
				}
			</script>
		";
		echo $html . $script;
	}
	wp_die();
}

function credo_payment_init() {
	if ( class_exists( 'WC_Payment_Gateway' ) ) {
		require_once plugin_dir_path( __FILE__ ) . '/includes/class-wc-payment-gateway-credo.php';
		// require_once plugin_dir_path( __FILE__ ) . '/includes/credo-order-statuses.php';
	}
}

function add_to_woo_credo_installment_gateway( $gateways ) {
	$gateways[] = 'WC_Gateway_Credo';
	return $gateways;
}

function credo_add_gel_currencies( $currencies ) {
	$currencies['GEL'] = __( 'Georgian lari', 'credo-woo' );

	return $currencies;
}

function credo_add_gel_currencies_symbol( $currency_symbol, $currency ) {
	switch ( $currency ) {
		case 'GEL':
			$currency_symbol = '₾';
		break;
	}
	return $currency_symbol;
}