<?php

use Kirby\Cms\Page;
use Kirby\Cms\Pages;
use Kirby\Content\Field;
use Kirby\Data\Data;
use Kirby\Toolkit\Str;

class ReferenceApiTypePage extends Page
{
	public function title(): Field
	{
		$type = $this->type();
		return $this->type()->value(ucfirst($type));
	}


	/**
	 * Creates children collection by parsing the `src/` folder
	 */
	public function children(): Pages
	{
		if ($this->children !== null) {
			return $this->children;
		}

		$children = $this->childrenFactory();

		return $this->children = Pages::factory($children, $this);
	}

	/**
	 * Creates an array of page properties for all blueprint folders in the
	 * provided root.
	 */
	protected function childrenFactory(): array {
			$children = [];

			/** e.g. "routes" or "models" */
			$apiType = $this->slug();
			$items = [];
			$model = '';
			$template = 'reference-doc';
			if ($apiType === 'routes') {
				$items = $this->kirby()->plugin('ww/merx')->extends()['api']['routes']($this->kirby());
				$model = 'reference-api-route';
			}
			if ($apiType === 'models') {
				$items = $this->kirby()->plugin('ww/merx')->extends()['api']['models'];
				$model = 'reference-api-model';
			}
			foreach ($items as $key => $item) {
				if ($apiType === 'routes') {
					$method = Str::upper($item['method'] ?? 'GET');
					$slug  = Str::slug($item['pattern'] . '-' . $method);
					$root  = $this->root() . '//0_' . $slug;
					$content = [
						'pattern' => $item['pattern'],
						'auth' => $item['auth'],
						'method' => $method,
						'reflection' => new ReflectionFunction($item['action']),
					];
				} else if ($apiType === 'models') {
					$slug  = Str::slug($key);
					$root  = $this->root() . '//0_' . $slug;
					$content = [
						'class' => $key,
						'fields' => $item['fields'],
						'type' => $item['type'],
						'views' => $item['views'],
					];
				}

				try {
					/** Add content from .txt content files */
					$content = [...Data::read($root . '/' . $template . '.txt'), $content];
				} catch (Throwable) {
				}

				$children[] = [
					'slug' => $slug,
					'root' => $root,
					'model' => $model,
					'template' => $template,
					'num' => 0,
					'content'  => $content
				];
			}

			return $children;
		}
}
