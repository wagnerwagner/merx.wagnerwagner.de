<?php

namespace Wagnerwagner\Site;

use Kirby\Cms\Html;
use Kirby\Toolkit\Collection;
use Kirby\Toolkit\Str;
use Reflector;

/**
 * @extends \Kirby\Toolkit\Collection<\Wagnerwagner\Site\Type>
 */
class Types extends Collection {
	public array $data = [];

	public function __construct(array|string $types, ?Reflector $reflector = null)
	{
		if (is_string($types)) {
			// Every inline code span in the documentation comes through here, and
			// most of them are not types. `?` only means “nullable” inside one —
			// in a query string like `?pretty=true` it means nothing, and
			// expanding it would render the span as the two types `null` and
			// `pretty=true`. Anything that does not read like a type expression
			// stays one piece.
			if (static::isTypeExpression($types) === true) {
				$types = Str::replace($types, '?', 'null|');
				$types = explode('|', $types);
			} else {
				$types = [$types];
			}
		}
		$this->data = array_map(fn ($type) => new Type($type, $reflector), $types);
	}

	/**
	 * Whether a string is shaped like a PHP type, and may therefore be split
	 * on `|` and have a leading `?` expanded.
	 *
	 * Deliberately narrow: type names, namespaces, generics and array brackets
	 * only. A character a type cannot contain — `=`, `(`, `$`, `.` — rules the
	 * whole string out.
	 */
	protected static function isTypeExpression(string $types): bool
	{
		return preg_match('!^[?\\\\A-Za-z0-9_\[\]|<>, ]+$!', $types) === 1;
	}

	public function toHtml(bool $codeBlock = true, ?string $baseUrl = null, ?bool $api = null, $short = false): string
	{
		if ($this->count() === 0) {
			return Html::tag('code', 'mixed');
		}

		$seperator = ' <span class="a-separator" aria-hidden="true">|</span><span class="a-visually-hidden">or</span> ';

		return implode($seperator, array_map(fn (Type $type) => $type->toHtml($codeBlock, $baseUrl, $api, $short), $this->data));
	}

	public function __toString(): string
	{
		return implode('|', $this->data);
	}
}
