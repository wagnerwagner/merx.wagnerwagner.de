<?php

namespace Wagnerwagner\Site;

use Closure;
use Kirby\Api\Api;
use Kirby\Cms\App;
use Kirby\Cms\ModelWithContent;
use Throwable;
use Wagnerwagner\Merx\Cart;
use Wagnerwagner\Merx\ListItem;
use Wagnerwagner\Merx\Price;

/**
 * Example requests and responses for the API reference
 *
 * The examples are not written by hand. They come from the real Merx API,
 * running against a small fixture shop: a few virtual product pages and a
 * cart. The fixture shop runs in a clone of the Kirby instance with its own
 * in-memory session, so nothing is written to disk and the visitor’s own
 * session and cart stay untouched.
 */
class ApiExamples
{
	public const BASE_URL = 'https://shop.test';

	/**
	 * Request bodies for routes that have an example, by method and route
	 * pattern (without the endpoint). Routes missing here have no example,
	 * because they talk to a payment provider or redirect the visitor.
	 *
	 * The checkout body holds the fields this site’s order blueprint requires.
	 */
	public const REQUESTS = [
		'GET cart' => null,
		'POST cart' => [
			'page' => 'products/canvas-bag',
			'quantity' => 1,
		],
		'PATCH cart' => [
			'key' => 'products/cozy-socks',
			'quantity' => 3,
		],
		'DELETE cart' => [
			'key' => 'products/cozy-socks',
		],
		'POST checkout' => [
			'paymentGateway' => 'invoice',
			'email' => 'jane@example.com',
			'company' => 'Example Inc.',
			'street' => 'Example Street 1',
			'postal_code' => '12345',
			'city' => 'Example City',
			'country' => 'DE',
		],
	];

	/**
	 * Requests that fail, by method and route pattern like `REQUESTS`,
	 * then by title. `cart: false` sends the request with an empty cart.
	 */
	public const ERRORS = [
		'POST checkout' => [
			'Invalid or missing fields' => [
				'body' => [
					'paymentGateway' => 'invoice',
					'email' => 'jane@example',
					'street' => 'Example Street 1',
					'city' => 'Example City',
					'country' => 'DE',
				],
			],
			'No payment gateway' => [
				'body' => [
					'email' => 'jane@example.com',
					'company' => 'Example Inc.',
					'street' => 'Example Street 1',
					'postal_code' => '12345',
					'city' => 'Example City',
					'country' => 'DE',
				],
			],
			'Empty cart' => [
				'body' => self::REQUESTS['POST checkout'],
				'cart' => false,
			],
		],
	];

	/**
	 * Fixture products, with page slug as key
	 */
	protected const PRODUCTS = [
		'nice-shoes' => [
			'title' => 'Nice Shoes',
			'price' => 89.9,
			'stock' => 12,
			'uuid' => 'Yk4fR7tNq2Lm9VxC',
		],
		'cozy-socks' => [
			'title' => 'Cozy Socks',
			'price' => 12.5,
			'uuid' => 'bP3wZs8HdE6uJa1Q',
		],
		'canvas-bag' => [
			'title' => 'Canvas Bag',
			'price' => 24,
			'uuid' => 'Gm7cTq2XvN5rKe9W',
		],
	];

	/**
	 * Runs `$callback` with the Kirby API of the fixture shop
	 *
	 * The fixture cart is in the session already, unless `$cart` is false.
	 * The original Kirby instance is restored afterwards, also when
	 * `$callback` throws.
	 *
	 * @template T
	 * @param Closure(Api): T $callback
	 * @return T
	 */
	public static function run(Closure $callback, bool $cart = true): mixed
	{
		$kirby = App::instance();
		$fixture = static::app($kirby);

		try {
			// Sets the locale for prices, as an API request would
			$fixture->setCurrentLanguage();

			// Created in manual mode before anything else asks for a session,
			// so the session never sends a cookie.
			$fixture->sessions()->get(['createMode' => 'manual']);
			$fixture->session()->set('region', 'DE');
			if ($cart === true) {
				static::fillCart(new Cart());
			}

			return $callback($fixture->api());
		} finally {
			// The clone replaced both, so pages loaded later would get its urls
			App::instance($kirby);
			ModelWithContent::$kirby = $kirby;
		}
	}

	/**
	 * Example response of a route, or null when the route has no example
	 *
	 * @param $pattern Route pattern including the endpoint, e.g. `shop/cart`
	 * @param $query Query parameters, e.g. `['view' => 'compact']`
	 */
	public static function request(string $pattern, string $method = 'GET', array $query = []): array|null
	{
		$key = static::requestKey($pattern, $method);
		if ($key === null) {
			return null;
		}

		return static::call($pattern, $method, static::REQUESTS[$key] ?? [], $query);
	}

	/**
	 * Failing example requests of a route, with their error responses
	 *
	 * @return array<string, array{body: array, cart: bool, response: array}> By title
	 */
	public static function errors(string $pattern, string $method = 'GET'): array
	{
		$key = static::requestKey($pattern, $method, static::ERRORS);
		if ($key === null) {
			return [];
		}

		$errors = [];
		foreach (static::ERRORS[$key] as $title => $error) {
			$cart = $error['cart'] ?? true;
			$errors[$title] = [
				'body' => $error['body'],
				'cart' => $cart,
				'response' => static::call($pattern, $method, $error['body'], cart: $cart),
			];
		}
		return $errors;
	}

	/**
	 * Request body of a route’s example, or null when it has none
	 */
	public static function requestBody(string $pattern, string $method = 'GET'): array|null
	{
		$key = static::requestKey($pattern, $method);
		return $key === null ? null : static::REQUESTS[$key];
	}

	/**
	 * Example of an API model in each of its views
	 *
	 * The cart is fetched through its route, with the view as query parameter.
	 * Models without a route of their own are resolved directly from an
	 * object of the fixture cart.
	 *
	 * @return array<string, array{request: string|null, data: array}> By view name, `default` first
	 */
	public static function model(string $name, array $views): array
	{
		$views = array_unique(['default', ...$views]);

		if ($name === 'Cart') {
			$pattern = static::endpoint() . '/cart';
			$examples = [];
			foreach ($views as $view) {
				$query = $view === 'default' ? [] : ['view' => $view];
				$data = static::request($pattern, 'GET', $query)['data'] ?? null;
				if ($data !== null) {
					$examples[$view] = [
						'request' => 'GET /api/' . $pattern . ($query ? '?' . http_build_query($query) : ''),
						'data' => $data,
					];
				}
			}
			return $examples;
		}

		return static::run(function (Api $api) use ($name, $views) {
			$object = static::object($name);
			if ($object === null) {
				return [];
			}

			$examples = [];
			foreach ($views as $view) {
				$examples[$view] = [
					'request' => null,
					'data' => $api->model(strtolower($name), $object)->view($view)->toArray(),
				];
			}
			return $examples;
		});
	}

	/**
	 * Encodes an example the way the API does with `?pretty=true`
	 */
	public static function json(array $data): string
	{
		return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
	}

	/**
	 * Clone of the Kirby instance which serves the fixture shop
	 */
	protected static function app(App $kirby): App
	{
		return $kirby->clone([
			'components' => [
				'session::store' => fn () => new MemorySessionStore(),
			],
			'options' => [
				// Error responses as in production, without file and line
				'debug' => false,
				'session' => [
					// Never find the visitor’s real session
					'cookieName' => 'merx_api_examples',
					'gcInterval' => false,
				],
				'wagnerwagner.merx.pricingRules' => [
					'default' => [
						'currency' => 'EUR',
						'taxIncluded' => true,
					],
				],
				'wagnerwagner.merx.taxRules' => [
					'default' => [
						'rate' => 0.19,
					],
				],
			],
			'site' => [
				'url' => static::BASE_URL,
				'content' => [
					'title' => 'Shop',
				],
				'children' => [
					[
						'slug' => 'products',
						'template' => 'default',
						'content' => ['title' => 'Products', 'uuid' => 'Ns2kQ8vLc4Tw7HbR'],
						'children' => array_map(fn (array $content, string $slug) => [
							'slug' => $slug,
							'template' => 'product',
							'model' => 'product',
							'content' => $content,
						], static::PRODUCTS, array_keys(static::PRODUCTS)),
					],
					[
						'slug' => 'checkout',
						'template' => 'checkout',
						'content' => ['title' => 'Checkout', 'uuid' => 'Hc5vW1mZr8KdQ3sY'],
					],
					[
						'slug' => 'orders',
						'template' => 'orders',
						'model' => 'orders',
						'content' => ['title' => 'Orders', 'uuid' => 'Ue9pB2xFj6NaT4kM'],
					],
				],
			],
			'urls' => [
				'index' => static::BASE_URL,
			],
		]);
	}

	/**
	 * Adds the items every example starts with
	 *
	 * Item by item rather than through the constructor, which would key the
	 * items by their position instead of their key.
	 */
	protected static function fillCart(Cart $cart): void
	{
		$cart->add([
			'page' => 'products/nice-shoes',
			'quantity' => 1,
			'data' => ['size' => '42'],
		]);
		$cart->add([
			'page' => 'products/cozy-socks',
			'quantity' => 2,
		]);
		$cart->add(new ListItem(
			key: 'shipping',
			title: 'Shipping',
			price: new Price(price: 4.9, tax: 0.19, currency: $cart->currency()),
			type: 'shipping',
		));
	}

	/**
	 * Calls a route of the fixture shop like a client that accepts JSON
	 */
	protected static function call(string $pattern, string $method, array $body, array $query = [], bool $cart = true): array
	{
		return static::run(function (Api $api) use ($pattern, $method, $body, $query) {
			$api->kirby()->visitor()->acceptedMimeType('application/json');

			try {
				return $api->call($pattern, $method, [
					'body' => $body,
					'query' => $query,
				]);
			} catch (Throwable $e) {
				return $api->responseForException($e);
			}
		}, $cart);
	}

	protected static function endpoint(): string
	{
		return (string)App::instance()->option('wagnerwagner.merx.api.endpoint', 'shop');
	}

	/**
	 * Object of the fixture cart for a model without a route
	 */
	protected static function object(string $name): object|null
	{
		$cart = new Cart();
		$listItem = $cart->first();

		return match ($name) {
			'ProductList' => $cart,
			'ListItem' => $listItem,
			'Price' => $listItem->price,
			'Tax' => $listItem->price->tax,
			default => null,
		};
	}

	/**
	 * Key of `REQUESTS` (or `$examples`) for a route, or null when the route has no example
	 */
	protected static function requestKey(string $pattern, string $method, array $examples = self::REQUESTS): string|null
	{
		$endpoint = static::endpoint();
		$path = str_starts_with($pattern, $endpoint . '/') ? substr($pattern, strlen($endpoint) + 1) : $pattern;
		$key = strtoupper($method) . ' ' . $path;

		return array_key_exists($key, $examples) ? $key : null;
	}
}
