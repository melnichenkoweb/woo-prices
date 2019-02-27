<?php
/*
Plugin Name: Prices
Plugin URI: http://null
Description: Prices
Version: 1.0
Author: dev/null
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}

register_activation_hook( __FILE__, array( 'Prices', 'install' ) );
register_deactivation_hook( __FILE__, array( 'Prices', 'uninstall' ) );
add_action( 'plugins_loaded', array( Prices::get_instance(), 'setup' ) );

class Prices {

	protected static $instance = null;

	const PLUGIN_SLUG = 'prices';

	/*Construct*/
	function __construct() {}

	/*Get instance*/
	public static function get_instance() {
		null === self::$instance and self::$instance = new self;
		return self::$instance;
	}

	/*Activation hook*/
	static function install () {
		add_option( 'prices_options', array(
			'active'     => false,
			'domain'     => null,
			'user_key'   => null,
			'secure_key' => null,
		));
	}

	/*Deactivation hook*/
	static function uninstall () {
		delete_option( 'prices_options' );
	}

	/*Plugin run*/
	public function setup() {
		add_action( 'admin_menu', array( $this, 'add_menu_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/*Registration menu pages*/
	public  function add_menu_page () {
		add_menu_page( __( 'Prices' ), __( 'Prices' ), 'manage_options', self::PLUGIN_SLUG, array( $this, 'menu_page' ) );
		add_submenu_page( self::PLUGIN_SLUG, __( 'Settings' ), __( 'Settings' ), 'manage_options', self::PLUGIN_SLUG . '_settings', array( $this, 'settings_page' ) );
		add_submenu_page( self::PLUGIN_SLUG, __( 'Logs' ), __( 'Logs' ), 'manage_options', self::PLUGIN_SLUG . '_logs', array( $this, 'logs_page' ) );
	}

	/*Menu page Prices*/
	public function menu_page () {
		include 'templates/menu.php';
	}

	/*Menu page Settings*/
	public function settings_page () {
		include 'templates/settings.php';
	}

	/*Menu page Logs*/
	public function logs_page () {
		include 'templates/logs.php';
	}

	/*Enqueue scripts and styles to plugin pages*/
	public function enqueue_scripts () {
		wp_enqueue_style( 'prices-style', plugins_url('css/style.css', __FILE__ ) );
		wp_enqueue_script( 'prices-script', plugins_url('js/script.js', __FILE__ )  );
		wp_enqueue_script( 'jQuery' );
	}

	/**
	 * @param $prices_options
	 * @return string|WC_API_Client
	 *
	 * Setup REST API Client
	 */
	public static function client_setup ( $prices_options ) {
		require_once 'woocommerce-rest-api-lib/woocommerce-api.php';
		$prices_client = '';
		$woo_api_options = array(
			'debug'      => true,
			'ssl_verify' => false,
		);

		try {
			$prices_client = new WC_API_Client(
				$prices_options['domain'],
				$prices_options['user_key'],
				$prices_options['secure_key'],
				$woo_api_options );
		} catch ( WC_API_Client_Exception $e ) {
			echo $e->getMessage() . PHP_EOL;
			echo $e->getCode() . PHP_EOL;

			if ( $e instanceof WC_API_Client_HTTP_Exception ) {
				print_r( $e->get_request() );
				print_r( $e->get_response() );
			}
		}

		return $prices_client;
	}

	/**
	 * @param $prices_client
	 *
	 * Show products
	 */
	public static function show_products ( $prices_client ) {
		$products = $prices_client->products->get();

		echo <<<table
<table class="prices-products">
	<tr>
		<th class="prices-check prices-head-check">
			Check
		</th>
		<th>
			ID
		</th>
		<th>
			Title
		</th>
		<th>
			Price
		</th>
		<th>
			Quantity
		</th>
		<th>
			Delete
		</th>
	</tr>
table;


		foreach ( $products->products as $product ){
			echo <<<products
	<tr class="prices-row">
		<td class="prices-check">
			<input class="prices-update" type="checkbox" name="id_{$product->id}[active]" value="update">
			<input type="hidden" name="id_{$product->id}[id]" value="{$product->id}">
			<input type="hidden" name="id_{$product->id}[title]" value="{$product->title}">
		</td>
		<td class="prices-id">
			{$product->id}
		</td>
		<td class="prices-title">
			{$product->title}
		</td>
		<td class="prices-price">
			<input class="prices-change" type="number" step="0.01" name="id_{$product->id}[price]" value="{$product->regular_price}">
		</td>
		<td class="prices-quantity">
			<input class="prices-change" type="number" name="id_{$product->id}[quantity]" value="{$product->stock_quantity}">
		</td>
		<td class="prices-check">
			<input class="prices-delete-product" type="checkbox" name="id_{$product->id}[delete]" value="delete">
		</td>
	</tr>
products;
		}
		echo <<<table
</table>
table;
	}

	/**
	 * @param $prices_client
	 * @param $products
	 *
	 * Upload changes to remote host throw REST API
	 */
	public static function update_changes ( $prices_client, $products ) {
		foreach ( $products as $product ){
			if ( is_array( $product ) && isset( $product['active'] ) ) {
				if ( $product['id'] == 'auto' ){
					$prepare = array(
						'title'              => $product['title'],
						'type'               => 'simple',
						'regular_price'      => $product['price'],
						'managing_stock'     => true,
						'stock_quantity'     => $product['quantity'],
						'in_stock'           => true,
						'purchaseable'       => true,
						'visible'            => true,
						'catalog_visibility' => 'visible'
					);
					$prices_client->products->create( $prepare );
					echo '<div class="prices-border"><span class="prices-upload-title">' . $product['title'] . '</span><span class="prices-upload-status"> - created success</span>';
				} else {
					$prepare = array(
						'regular_price'  => $product['price'],
						'stock_quantity' => $product['quantity'],
					);
					if ( $product ['quantity'] > 0 ){
						$prepare['managing_stock'] = true;
					}
					$prices_client->products->update($product['id'], $prepare );
					echo '<div class="prices-border"><span class="prices-upload-title">' . $product['title'] . '</span><span class="prices-upload-status"> - uploaded success</span>';
				}
			} elseif ( is_array( $product ) && isset( $product['delete'] ) ) {
				$prices_client->products->delete( $product['id'], true );
				echo '<div class="prices-border"><span class="prices-upload-title">' . $product['title'] . '</span><span class="prices-upload-status"> - Deleted success</span></div>';
			}
		}
	}

	/*Download logs file from remote domain*/
	public static function download_logs () {
		$options = get_option( 'prices_options' );
		$domain = preg_replace( '/\//', '', preg_replace( '/^http:/', '', $options['domain'] ) );
		$json = file_get_contents( 'http://' . $domain . '/wp-content/plugins/prices-client/logs/send-log.php' );
		$data = json_decode( $json );
		$file_name = plugin_dir_path( __FILE__ ) . "logs\\logs.txt";
		$log_file = fopen( $file_name , 'w+');
		fwrite( $log_file, $data->logs );
		fclose( $log_file );
	}

}   /* End Class Prices*/
