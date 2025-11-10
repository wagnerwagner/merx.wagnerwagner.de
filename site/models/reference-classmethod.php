<?php

use Kirby\Cms\Page;
use Kirby\Content\Field;
use Kirby\Toolkit\A;
use Kirby\Toolkit\Str;
use PHPStan\PhpDocParser\Ast\Node;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTagNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use PHPStan\PhpDocParser\ParserConfig;
use Wagnerwagner\Site\Type;
use Wagnerwagner\Site\Types;

/**
 * @method \Kirby\Content\Field name()
 */
class ReferenceClassMethodPage extends Page
{
  public function title(): Field
  {
    $className = A::last(Str::split($this->class(), '\\'));

    $title = (string)$this->name() . '()';
    if ($this->reflection()->isConstructor()) {
      $title = 'new ' . $className . '()';
    } else if ($this->reflection()->isStatic()) {
      $title = $className . '::' . $title;
    } else {
      $objectName = lcfirst($className);
      $title = '$' . $objectName . '->' . $title;
    }
    return $this->name()->value($title);
  }

  public function class(): Type
  {
    /** @var ReferenceClassPage */
    $parent = $this->parent();
    return new Type($parent->class()->toString());
  }

  public function declaringClass(): Type
  {
    return new Type($this->reflection()->getDeclaringClass()->getName());
  }

  public function docBlock(): ?PhpDocNode
  {
    if ($this->reflection()->getDocComment() === false) {
      return null;
    }
    $docblock  = $this->reflection()->getDocComment();
    $config    = new ParserConfig(usedAttributes: []);
    $lexer     = new Lexer($config);
    $constExpr = new ConstExprParser($config);
    $type      = new TypeParser($config, $constExpr);
    $phpDoc    = new PhpDocParser($config, $type, $constExpr);
    $tokens    = new TokenIterator($lexer->tokenize($docblock));
    $node      = $phpDoc->parse($tokens);
    return $node;
  }

  public function reflection(): ReflectionMethod
  {
    return new ReflectionMethod((string)$this->class(), $this->name());
  }

  /**
   * Path with line number
   *
   * @return ?string e.g. src/ListItems.php#L32
   */
  public function filePath(): ?string
  {
    if (Str::startsWith($this->declaringClass(), 'Kirby\\')) {
      $root = $this->kirby()->root('kirby') . '/';
    } else if (Str::startsWith($this->declaringClass(), 'Wagnerwagner\\')) {
      $root = $this->kirby()->plugin('ww/merx')->root() . '/';
    } else {
      return null;
    }
    $filePath = Str::replace($this->reflection()->getFileName(), $root, '');
    $line = $this->reflection()->getStartLine();
    return $filePath . '#L' . $line;
  }

  /**
   * @return string e.g. https://github.com/wagnerwagner/merx/blob/2.0.0-alpha.3/src/ListItems.php#L32
   */
  public function gitHubUrl(): ?string
  {
    if (Str::startsWith($this->declaringClass(), 'Kirby\\')) {
      $gitHubRoot = option('github-repositories.kirby');
      $version = $this->kirby()->version();
    } else if (Str::startsWith($this->declaringClass(), 'Wagnerwagner\\')) {
      $gitHubRoot = option('github-repositories.merx');
      $version = $this->kirby()->plugin('ww/merx')->version();

      if (option('nova-links') === true) {
        return 'nova://open?path=' . $this->reflection()->getFileName() . '&line=' . $this->reflection()->getStartLine();
      }
    } else {
      return null;
    }
    return $gitHubRoot . '/blob/' . $version . '/' . $this->filePath();
  }

  public function summary(): ?string
  {
    $nodes = array_filter(
      $this->docBlock()->children ?? [],
      fn (Node $node) => $node instanceof PhpDocTextNode
    );
    $node = $nodes[0] ?? null;

    if ($node instanceof PhpDocTextNode) {
      $text = explode(PHP_EOL . PHP_EOL, $node->text)[0];
      $text = str_replace(PHP_EOL, ' ', $text);
      return trim($text);
    }

    return null;
  }

  public function description(): ?string
  {
    $nodes = array_filter(
      $this->docBlock()->children ?? [],
      fn (Node $node) => $node instanceof PhpDocTextNode
    );
    $node = $nodes[0] ?? null;

    if ($node instanceof PhpDocTextNode) {
      $text = explode(PHP_EOL . PHP_EOL, $node->text)[1] ?? '';
      return empty($text) ? null : trim($text);
    }

    return null;
  }

  /**
   * @return ?array with name, type, defaultValue, description
   */
  public function params(): array
  {
    $reflection = $this->reflection();

    return A::map($reflection->getParameters(), function (ReflectionParameter $param) {
      /** @var ?\PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode */
      $docBlockParam = A::find(
        $this->docBlock()?->getParamTagValues() ?? [],
        fn (ParamTagValueNode $docBlockParam) => $docBlockParam->parameterName === '$' . $param->name);

      $defaultValue = null;
      if ($param->isDefaultValueAvailable()) {
        $defaultValue = $param->getDefaultValue();
        if (gettype($defaultValue) === 'array') {
          $defaultValue = '[]';
        } else if (gettype($defaultValue) === 'string')
          $defaultValue = "'$defaultValue'";
      };
      $name = '$' . $param->getName();
      $types = $docBlockParam?->type->types ?? explode('|', Str::replace($param->getType() ?? 'mixed', '?', 'null|'));
      $types = new Types($types, $this->reflection());
      $name = $param->isVariadic() ? '...' . $name : $name;
      return [
        'name' => $name,
        'types' => $types,
        'defaultValue' => $defaultValue,
        'description' => $docBlockParam?->description,
      ];
    });
  }

  public function returnTypes(): ?Types
  {
    /** @var \PHPStan\PhpDocParser\Ast\PhpDoc\ReturnTagValueNode[] */
    $returnTags = $this->docBlock()?->getReturnTagValues() ?? [];
    if (isset($returnTags[0])) {
      $type = $returnTags[0]->type;
    }

    $type ??= $this->reflection()->getReturnType();
    $types = new Types(explode(',', $type), $this->reflection());
    return $types;
  }

  /**
   * DocBlock description of return type
   */
  public function returnDescription(): ?string
  {
    /** @var \phpDocumentor\Reflection\DocBlock\Tags\Param[] */
    $returnTags = $this->docBlock()?->getTagsByName('return') ?? [];
    if (count($returnTags) === 0) {
      return null;
    }

    return nl2br($returnTags[0]->getDescription());
  }

  public function exceptions(): array
  {
    /** @var ?\PHPStan\PhpDocParser\Ast\PhpDoc\ThrowsTagValueNode[] */
    $throws = $this->docBlock()?->getThrowsTagValues() ?? [];

    return array_map(fn ($tag) => [
      'type' => new Type($tag->type),
      'description' => $tag->description,
    ], $throws);
  }

  public function call(): string
  {
    $parameters = array_map(function ($param) {
      $string = '';
      if (count($param['types']) > 0) {
        $string .= $param['types'] . ' ';
      }
      $string .= $param['name'];
      if ($param['defaultValue']) {
        $string .= ' = ' . $param['defaultValue'];

      }
      return $string;
    }, $this->params() ?? []);
    $call = substr($this->title(), 0, -2) . '(' . implode(', ', $parameters) . ')';

    if ($this->returnTypes()) {
      $call .= ': ' . $this->returnTypes();
    }

    return $call;
  }
}
