<?php

namespace Wagnerwagner\Site;

use Closure;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Str;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayShapeNode;
use PHPStan\PhpDocParser\Ast\Type\ArrayTypeNode;
use PHPStan\PhpDocParser\Ast\Type\GenericTypeNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Ast\Type\NullableTypeNode;
use PHPStan\PhpDocParser\Ast\Type\TypeNode;
use PHPStan\PhpDocParser\Ast\Type\UnionTypeNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use PHPStan\PhpDocParser\ParserConfig;
use ReflectionFunction;

/**
 * TypeScript interfaces of the API models of Merx, in their default view
 *
 * A field is typed by the `@return` tag of its closure, or by its return
 * type when there is none. The tag is what describes an `array`.
 */
class TypeScript
{
	/**
	 * Fields of Kirby’s own models. Their closures carry no return type.
	 */
	private const KIRBY_FIELDS = [
		'Kirby\Cms\Page' => [
			'id' => 'string',
			'template' => 'string',
			'title' => 'string',
			'url' => 'string',
			'uuid' => 'string|null',
		],
	];
	private const SCALARS = [
		'bool' => 'boolean',
		'false' => 'false',
		'float' => 'number',
		'int' => 'number',
		'mixed' => 'unknown',
		'null' => 'null',
		'string' => 'string',
		'true' => 'true',
	];

	/**
	 * Translates a PHP type into TypeScript. `$select` is the selection of
	 * a view, like `total<price, priceNet>`, and applies to a model only.
	 */
	private static function convert(TypeNode $type, array|null $select, array $uses): string
	{
		if ($type instanceof UnionTypeNode) {
			return implode(' | ', A::map($type->types, fn ($type) => static::convert($type, $select, $uses)));
		}

		if ($type instanceof NullableTypeNode) {
			return static::convert($type->type, $select, $uses) . ' | null';
		}

		if ($type instanceof ArrayShapeNode) {
			$items = A::map($type->items, fn ($item) => $item->keyName . ($item->optional ? '?' : '') . ': ' . static::convert($item->valueType, null, $uses));
			return '{ ' . implode('; ', $items) . ' }';
		}

		if ($type instanceof ArrayTypeNode) {
			return static::list(static::convert($type->type, null, $uses));
		}

		if ($type instanceof GenericTypeNode) {
			$arguments = A::map($type->genericTypes, fn ($type) => static::convert($type, null, $uses));
			return count($arguments) === 2
				? 'Record<' . ($arguments[0] === 'number' ? 'number' : 'string') . ', ' . $arguments[1] . '>'
				: static::list($arguments[0]);
		}

		if ($type instanceof IdentifierTypeNode) {
			return static::SCALARS[Str::lower($type->name)] ?? static::model($type->name, $select, $uses);
		}

		return 'unknown';
	}

	/**
	 * All models in one declaration file
	 */
	public static function declarations(): string
	{
		$version = kirby()->plugin('wagnerwagner/merx')->version();
		$blocks = [
			'/**' . PHP_EOL
			. ' * The models of the Merx API in their default view' . PHP_EOL
			. ' * Merx ' . $version . ', ' . url('reference/api/models') . PHP_EOL
			. ' */',
		];

		foreach (array_keys(static::models()) as $name) {
			$blocks[] = '/** @see ' . url('reference/api/models/' . Str::kebab($name)) . ' */'
				. PHP_EOL . static::interface($name);
		}

		return implode(PHP_EOL . PHP_EOL, $blocks) . PHP_EOL;
	}

	private static function field(Closure $closure, array|null $select): string
	{
		$reflection = new ReflectionFunction($closure);
		$type = static::returnTag($reflection) ?? static::parse((string)($reflection->getReturnType() ?? 'mixed'));

		return static::convert($type, $select, static::uses($reflection->getFileName()));
	}

	/**
	 * The interface of one model, e.g. `Cart`
	 */
	public static function interface(string $name): string
	{
		$model = static::models()[$name];
		$lines = [];

		foreach ($model['views']['default'] ?? array_keys($model['fields']) as $key => $value) {
			[$field, $select] = is_array($value) ? [$key, $value] : [$value, null];
			$lines[] = "\t" . $field . ': ' . static::field($model['fields'][$field], $select) . ';';
		}

		return 'export interface ' . $name . ' {' . PHP_EOL . implode(PHP_EOL, $lines) . PHP_EOL . '}';
	}

	/**
	 * The `array<T>` or `list<T>` of a type
	 */
	private static function list(string $type): string
	{
		return (Str::contains($type, ' ') ? '(' . $type . ')' : $type) . '[]';
	}

	/**
	 * A class, which the API renders as one of its models
	 */
	private static function model(string $name, array|null $select, array $uses): string
	{
		$class = ltrim($uses[$name] ?? $name, '\\');

		foreach (static::models() as $model => $definition) {
			if ($definition['type'] !== $class) {
				continue;
			}

			if ($select === null) {
				return $model;
			}

			// Pick only works on fields of the default view
			$default = array_map(fn ($value, $key) => is_array($value) ? $key : $value, $definition['views']['default'], array_keys($definition['views']['default']));
			if (array_diff($select, $default) === []) {
				return 'Pick<' . $model . ', ' . implode(' | ', A::map($select, fn ($field) => "'" . $field . "'")) . '>';
			}

			$fields = A::map($select, fn ($field) => $field . ': ' . static::field($definition['fields'][$field], null));
			return '{ ' . implode('; ', $fields) . ' }';
		}

		if ($kirby = static::KIRBY_FIELDS[$class] ?? null) {
			if ($select === null) {
				return 'Record<string, unknown>';
			}

			$fields = A::map($select, fn ($field) => $field . ': ' . static::convert(static::parse($kirby[$field] ?? 'mixed'), null, []));
			return '{ ' . implode('; ', $fields) . ' }';
		}

		return 'unknown';
	}

	/**
	 * @return array<string, array{fields: array<string, Closure>, type: string, views: array}>
	 */
	private static function models(): array
	{
		return kirby()->plugin('wagnerwagner/merx')->extends()['api']['models'];
	}

	private static function parse(string $type): TypeNode
	{
		$config = new ParserConfig([]);
		return (new TypeParser($config, new ConstExprParser($config)))->parse(
			new TokenIterator((new Lexer($config))->tokenize($type)),
		);
	}

	private static function returnTag(ReflectionFunction $reflection): TypeNode|null
	{
		$docComment = $reflection->getDocComment();

		if ($docComment === false) {
			return null;
		}

		$config = new ParserConfig([]);
		$constExpr = new ConstExprParser($config);
		$parser = new PhpDocParser($config, new TypeParser($config, $constExpr), $constExpr);
		/** @var PhpDocNode $node */
		$node = $parser->parse(new TokenIterator((new Lexer($config))->tokenize($docComment)));

		return $node->getReturnTagValues()[0]->type ?? null;
	}

	/**
	 * The `use` statements of a file, which resolve the short class names
	 * of a `@return` tag
	 *
	 * @return array<string, string> Short name => class
	 */
	private static function uses(string|false $file): array
	{
		if ($file === false) {
			return [];
		}

		preg_match_all('!^use\s+([\w\\\\]+)(?:\s+as\s+(\w+))?;!m', file_get_contents($file), $matches, PREG_SET_ORDER);

		$uses = [];
		foreach ($matches as $match) {
			$uses[$match[2] ?? A::last(Str::split($match[1], '\\'))] = $match[1];
		}

		return $uses;
	}
}
