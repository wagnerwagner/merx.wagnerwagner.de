<?php

use Kirby\Cms\App;
use Wagnerwagner\Site\Markdown;

return [
	/**
	 * Use our custom Marsdown parser
	 */
	'markdown' => function (
		App $kirby,
		string|null $text = null,
		array $options = []
	) {
		static $parser;
		static $config;

		// if the config options have changed or the component is called for the first time,
		// (re-)initialize the parser object
		if ($config !== $options) {
			$parser = new Markdown($options);
			$config = $options;
		}

		if (($options['inline'] ?? false) === true) {
			return @$parser->line($text);
		}

		return @$parser->text($text);
	},
];
