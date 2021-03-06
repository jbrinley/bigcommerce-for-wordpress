<?php


namespace BigCommerce\Container;


use BigCommerce\Rest\Cart_Controller;
use BigCommerce\Rest\Orders_Shortcode_Controller;
use BigCommerce\Rest\Products_Controller;
use BigCommerce\Rest\Reviews_Listing_Controller;
use BigCommerce\Rest\Shortcode_Controller;
use Pimple\Container;

class Rest extends Provider {
	const NAMESPACE_BASE = 'rest.namespace';
	const VERSION        = 'rest.version';

	const CART_BASE = 'rest.cart_base';
	const CART      = 'rest.cart';

	const PRODUCTS_BASE = 'rest.products_base';
	const PRODUCTS      = 'rest.products';

	const SHORTCODE_BASE = 'rest.shortcode_base';
	const SHORTCODE      = 'rest.shortcode';

	const ORDERS_SHORTCODE_BASE = 'rest.orders_shortcode_base';
	const ORDERS_SHORTCODE      = 'rest.orders_shortcode';

	const REVIEW_LIST_BASE = 'rest.review_list_base';
	const REVIEW_LIST      = 'rest.review_list';

	private $version = 1;

	public function register( Container $container ) {
		$container[ self::NAMESPACE_BASE ] = function ( Container $container ) {
			return apply_filters( 'bigcommerce/rest/namespace_base', 'bigcommerce' );
		};

		$container[ self::VERSION ] = function ( Container $container ) {
			return apply_filters( 'bigcommerce/rest/version', $this->version );
		};

		$container[ self::CART_BASE ] = function ( Container $container ) {
			return apply_filters( 'bigcommerce/rest/cart_base', 'cart' );
		};

		$container[ self::CART ] = function ( Container $container ) {
			return new Cart_Controller( $container[ self::NAMESPACE_BASE ], $container[ self::VERSION ], $container[ self::CART_BASE ], $container[ Api::FACTORY ]->cart() );
		};

		$container[ self::PRODUCTS_BASE ] = function ( Container $container ) {
			return apply_filters( 'bigcommerce/rest/products_base', 'products' );
		};

		$container[ self::PRODUCTS ] = function ( Container $container ) {
			return new Products_Controller( $container[ self::NAMESPACE_BASE ], $container[ self::VERSION ], $container[ self::PRODUCTS_BASE ] );
		};

		$container[ self::SHORTCODE_BASE ] = function ( Container $container ) {
			return apply_filters( 'bigcommerce/rest/shortcode_base', 'shortcode' );
		};

		$container[ self::SHORTCODE ] = function ( Container $container ) {
			return new Shortcode_Controller( $container[ self::NAMESPACE_BASE ], $container[ self::VERSION ], $container[ self::SHORTCODE_BASE ] );
		};

		$container[ self::ORDERS_SHORTCODE_BASE ] = function ( Container $container ) {
			return apply_filters( 'bigcommerce/rest/orders_shortcode_base', 'orders-shortcode' );
		};

		$container[ self::ORDERS_SHORTCODE ] = function ( Container $container ) {
			return new Orders_Shortcode_Controller( $container[ self::NAMESPACE_BASE ], $container[ self::VERSION ], $container[ self::ORDERS_SHORTCODE_BASE ] );
		};

		$container[ self::REVIEW_LIST_BASE ] = function ( Container $container ) {
			return apply_filters( 'bigcommerce/rest/review_list_base', 'product-reviews' );
		};

		$container[ self::REVIEW_LIST ] = function ( Container $container ) {
			return new Reviews_Listing_Controller( $container[ self::NAMESPACE_BASE ], $container[ self::VERSION ], $container[ self::REVIEW_LIST_BASE ] );
		};

		add_action( 'rest_api_init', $this->create_callback( 'rest_init', function () use ( $container ) {
			$container[ self::PRODUCTS ]->register_routes();
			$container[ self::SHORTCODE ]->register_routes();
			$container[ self::ORDERS_SHORTCODE ]->register_routes();
			$container[ self::CART ]->register_routes();
			$container[ self::REVIEW_LIST ]->register_routes();
		} ), 10, 0 );

		add_filter( 'bigcommerce/product/reviews/rest_url', $this->create_callback( 'review_list_rest_url', function ( $url, $post_id ) use ( $container ) {
			return $container[ self::REVIEW_LIST ]->product_reviews_url( $post_id );
		} ), 10, 2 );
	}
}