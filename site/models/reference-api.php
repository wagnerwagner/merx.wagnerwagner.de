<?php

use Kirby\Api\Api;
use Kirby\Cms\App;
use Kirby\Cms\Page;
use Kirby\Cms\Pages;
use Kirby\Data\Data;
use Kirby\Filesystem\Dir;
use Kirby\Toolkit\Str;

class ReferenceApiPage extends Page
{
	/**
	 * Builds the children collection from the plugin's `api` directory
	 * and caches it on the page instance.
	 */
	public function children(): Pages
	{
		if ($this->children !== null) {
			return $this->children;
		}

		$children = [];

		// Add unlisted pages for each available API type by mirroring
		// the directory structure inside the plugin's `api` folder.
		$root = $this->kirby()->plugin('wagnerwagner/merx')->root() . '/api';

		$children = $this->childrenFactory(
			$root,
		);

		return $this->children = Pages::factory($children, $this);
	}

	/**
	 * Returns the page property array for every directory inside the
	 * given root, mapping each one to a `reference-api-type` child.
	 */
	protected function childrenFactory(
		string $root
	): array {
		$children = [];

		// Loop through every API type directory and register it
		// as a child page configuration.
		foreach (Dir::dirs($root) as $type) {
			$slug = Str::kebab($type);
			$root = $this->root() . '/0_' . $slug;

			try {
				$content = Data::read($root . '/reference-api-type.txt');
			} catch (Throwable) {
				$content = [];
			}

			/** exclude collections and data */
			$num = in_array($slug, ['collections', 'data']) ? null : 0;

			$children[] = [
				'slug'     => $slug,
				'root'     => $root,
				'model'    => 'reference-api-type',
				'template' => 'reference-doc',
				'num'      => $num,
				'content'  => [
					...$content,
					'type' => $type,
				]
			];
		}

		return $children;
	}

	/**
	 * API for example requests
	 */
	public function api(): Api
	{
		$kirby = $this->kirby();
		$api = new Api([
			'kirby' => new App(),
			'collections' => $kirby->plugin('wagnerwagner/merx')->extends()['api']['collections'],
			'data' => $kirby->plugin('wagnerwagner/merx')->extends()['api']['data'],
			'models' => $kirby->plugin('wagnerwagner/merx')->extends()['api']['models'],
			'routes' => $kirby->plugin('wagnerwagner/merx')->extends()['api']['routes']($kirby),
		]);
		return $api;
	}

	public function apiRequest(
		string $path,
		string $method = 'GET',
		array $requestData = []
	): mixed
	{
		$requestData = array_merge_recursive($requestData, [
			'query' => [
				'pretty' => true,
			],
		]);

		if (in_array($method, ['POST', 'PATCH', 'DELETE'])) {
			$requestData['body']['key'] = 'shoes';
		}

		if ($path === 'shop/cart') {
			return json_encode([
				'code' => 200,
				'data' => $this->resolveApiModel('ProductList'),
				'status' => 'ok',
				'type' => 'model'
			], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		}
		if ($path === 'shop/checkout') {
			$requestData['body']['email'] = 'example@example.com';
			$requestData['body']['street'] = 'Milkyway';
			$requestData['body']['city'] = 'Ducktown';
			$requestData['body']['country'] = 'DE';
			$requestData['body']['legal'] = '1';
			$requestData['body']['paymentMethod'] = 'invoice';
			$requestData['body']['company'] = 'Example Inc.';
		}

		return $this->api()->render(path: $path, method: $method, requestData: $requestData);
	}

	public function resolveApiModel(string $model): ?array
	{
		$object = null;
		$tax = new \Wagnerwagner\Merx\Tax(priceNet: 45, rate: 0.19, currency: 'EUR');
		$price = new \Wagnerwagner\Merx\Price(price: 45, tax: $tax, currency: 'EUR');
		$listItem = new \Wagnerwagner\Merx\ListItem(
			key: 'shoes',
			price: $price,
			title: 'Shoes',
			data: ['color' => 'red'],
			quantity: 2,
		);
		if ($model === 'Tax') {
			$object = $tax;
		}
		if ($model === 'Price') {
			$object = $price;
		}
		if ($model === 'ListItem') {
			$object = $listItem;
		}
		if ($model === 'ProductList') {
			$object = new \Wagnerwagner\Merx\ProductList([$listItem, ['key' => 'table', 'title' => 'Table', 'price' => null]]);
		}
		if ($model === 'Cart') {
			$object = new \Wagnerwagner\Merx\Cart([$listItem]);
		}

		if ($object === null) return null;

		return $this->api()->resolve($object)->toArray();
	}
}
