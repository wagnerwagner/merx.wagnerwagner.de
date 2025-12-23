<?php

namespace Wagnerwagner\Site;

use Kirby\Cms\Page;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Str;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use Reflector;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use PHPStan\PhpDocParser\ParserConfig;


abstract class ReferencePageAbstract extends Page
{
	/**
	 * Returns the reflection object for this reference, or null if not applicable.
	 */
	public function reflection(): ?Reflector
	{
		return null;
	}

	/**
	 * Returns the framework- or plugin-relative file path with the start line
	 * appended as an anchor (e.g. `src/ListItems.php#L32`).
	 */
  public function relativeFilePath(): ?string
	{
    $root = $this->kirby()->plugin('wagnerwagner/merx')->root() . '/';
    if ($this->detectNamespace() === 'Kirby') {
      $root = $this->kirby()->root('kirby') . '/';
    }
    $relativeFilePath = Str::replace($this->reflection()?->getFileName() ?? '', $root, '');
    return $relativeFilePath;
	}

  public function line(): ?int
  {
    return $this->reflection()?->getStartLine() ?? null;
  }

  private function absoluteFilePath(): ?string
  {
    $relativeFilePath = $this->relativeFilePath();
    if ($relativeFilePath === null) {
      return null;
    }

    if ($this->detectNamespace() === 'Kirby') {
      return $this->kirby()->root('kirby') . '/' . $relativeFilePath;
    }

    return $this->kirby()->plugin('wagnerwagner/merx')->root() . '/' . $relativeFilePath;
  }

	/**
	 * Returns the versioned GitHub URL that points directly to the
	 * reflected definition.
	 * E.g. https://github.com/wagnerwagner/merx/blob/2.0.0-alpha.3/src/ListItems.php#L32
	 */
	public function gitHubUrl(): ?string
	{
		$namespace = $this->detectNamespace();

    $relativeFilePath = $this->relativeFilePath();

		if ($relativeFilePath === null) {
			return null;
		}
		$line = $this->line();

		if (option('nova-links') === true) {
      $absoluteFilePath = $this->absoluteFilePath();
      if ($absoluteFilePath !== null) {
        // return 'cursor://file/' . $absoluteFilePath . ':' . $line ?? 1;
        return 'nova://open?path=' . $absoluteFilePath . '&line=' . $line ?? 1;
      }
		}

		if ($namespace === 'Kirby') {
			$gitHubRoot = option('github-repositories.kirby');
			$version = $this->kirby()->version();
		} else if ($namespace === 'Wagnerwagner') {
			$gitHubRoot = option('github-repositories.merx');
			$version = $this->kirby()->plugin('wagnerwagner/merx')->version();
		} else {
			// Default to merx for models without namespace detection
			$gitHubRoot = option('github-repositories.merx');
			$version = $this->kirby()->plugin('wagnerwagner/merx')->version();
		}

    if ($line !== null) {
      $relativeFilePath = $relativeFilePath . '#L' . $line;
    }

		return $gitHubRoot . '/blob/' . $version . '/' . $relativeFilePath;
	}

	/**
	 * Detects the namespace (Kirby\\ or Wagnerwagner\\) from reflection when possible.
	 * Returns the namespace prefix or empty string if detection is not possible.
	 */
	protected function detectNamespace(): ?string
	{
		$reflection = $this->reflection();

    // For ReflectionMethod, get the declaring class
		if ($reflection instanceof ReflectionMethod) {
			$className = $reflection->getDeclaringClass()->getName();
			if (Str::startsWith($className, 'Kirby\\')) {
				return 'Kirby';
			} else if (Str::startsWith($className, 'Wagnerwagner\\')) {
				return 'Wagnerwagner';
			}
		}

		// For ReflectionClass, get the class name directly
		if ($reflection instanceof ReflectionClass) {
			$className = $reflection->getName();
			if (Str::startsWith($className, 'Kirby\\')) {
				return 'Kirby';
			} else if (Str::startsWith($className, 'Wagnerwagner\\')) {
				return 'Wagnerwagner';
			}
		}

		// For other reflection types or when detection fails, default to empty
		// (which will cause gitHubUrl to default to merx)
		return null;
	}


	public function docBlock(): ?PhpDocNode
	{
		$reflection = $this->reflection();
		if ($reflection->getDocComment() === false) {
			return null;
		}
		$docblock  = $reflection->getDocComment();
		$config    = new ParserConfig(usedAttributes: []);
		$lexer     = new Lexer($config);
		$constExpr = new ConstExprParser($config);
		$type      = new TypeParser($config, $constExpr);
		$phpDoc    = new PhpDocParser($config, $type, $constExpr);
		$tokens    = new TokenIterator($lexer->tokenize($docblock));
		$node      = $phpDoc->parse($tokens);
		return $node;
	}

	/**
	 * Returns parameter metadata (name, types, default value, description)
	 * sourced from reflection and DocBlocks.
	 */
	public function params(): ?array
	{
		$reflection = $this->reflection();

		if ($reflection === null || !method_exists($reflection, 'getParameters')) {
			return null;
		}

		return A::map($reflection->getParameters(), function (ReflectionParameter $param) use ($reflection) {
			/** @var ?\PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode */
			$docBlockParam = A::find(
				$this->docBlock()?->getParamTagValues() ?? [],
				fn (ParamTagValueNode $docBlockParam) => $docBlockParam->parameterName === '$' . $param->name);

			$defaultValue = null;
			if ($param->isDefaultValueAvailable()) {
				$defaultValue = $param->getDefaultValue();
				if (gettype($defaultValue) === 'array') {
					$defaultValue = '[]';
				} else if (gettype($defaultValue) === 'boolean') {
					$defaultValue = $defaultValue ? 'true' : 'false';
				} else if (gettype($defaultValue) === 'NULL') {
					$defaultValue = 'null';
				} else if (gettype($defaultValue) === 'string') {
					$defaultValue = "'$defaultValue'";
				}
			};
			$name = '$' . $param->getName();
			$types = $docBlockParam->type->types ?? null;
			if ($types === null && $param->getType() !== null) {
				$types = $param->getType();
			}
			$types = new Types($types ?? [], $reflection);
			$name = $param->isVariadic() ? '...' . $name : $name;
			return [
				'name' => $name,
				'types' => $types,
				'defaultValue' => $defaultValue,
				'description' => $docBlockParam?->description,
			];
		});
	}
}

