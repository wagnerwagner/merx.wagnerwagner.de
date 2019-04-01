<?php
namespace Wagnerwagner\Site;

use Kirby\Toolkit\Str;
use Kirby\Toolkit\c;
use phpDocumentor\Reflection\DocBlockFactory;


class MyReflectionMethod extends \ReflectionMethod
{
  private $docBlock = null;
  public $className = '';

  public function __construct($class, string $name) {
    parent::__construct($class, $name);
    $this->className = $class;
    if ($comment = parent::getDocComment()) {
      $this->docBlock = DocBlockFactory::createInstance()->create($comment);
    }
  }

  public function getSummary(): string
  {
    if ($this->docBlock) {
      return $this->docBlock->getSummary();
    }
    return '';
  }

  public function hasParameters(): bool
  {
    return count($this->getParameters()) > 0;
  }

  public function getParameters(): array
  {
    if ($this->docBlock) {
      return $this->docBlock->getTagsByName('param');
    }
    return [];
  }

  public function getFileName(): string
  {
    $fileName = parent::getFileName();
    if (Str::contains($fileName, kirby()->root('kirby'))) {
      $fileName = Str::after($fileName, kirby()->root('kirby'));
    } else if (Str::contains($fileName, \c::get('merxPluginDirectory'))) {
      $fileName = Str::after($fileName, \c::get('merxPluginDirectory'));
    }
    return $fileName;
  }

  public function getFileContents(): string
  {
    return file_get_contents(parent::getFileName()); //read the file
  }

  public function getLines(): array
  {
    return explode("\n", $this->getFileContents());
  }

  public function getCode(): string
  {
    $code = '';
    $className = $this->className;
    if (Str::contains($className, 'Wagnerwagner\\Merx\\')) {
      $className = Str::after($className, 'Wagnerwagner\\Merx\\');
    }

    $startLine = $this->getStartLine();
    $lines = $this->getLines();
    if ($this->isStatic()) {
      $code = $className . '::' . Str::from($lines[$startLine - 1], $this->getName());
    } else {
      $variable = '$' . lcfirst($className);
      if ($this->isConstructor()) {
        $code = $variable . ' = new ' . $className . Str::after($lines[$startLine - 1], $this->getName()) . ';';
      } else {
        $code = $variable . '->' . Str::from($lines[$startLine - 1], $this->getName());
      }
    }
    return $code;
  }

  public function hasReturnType(): bool
  {
    if ($this->docBlock && count($this->docBlock->getTagsByName('return')) > 0) {
      return true;
    }
    return parent::hasReturnType();
  }

  public function getReturnType(): string
  {
    $returnType = '';
    $returnType = parent::getReturnType();
    if ($returnType === null && $this->docBlock && count($this->docBlock->getTagsByName('return')) > 0) {
      $returnType = $this->docBlock->getTagsByName('return')[0]->getType();
    }
    if ((string)$returnType === 'self') {
      $returnType = $this->className;
    } else if ((string)$returnType === 'parent') {
      $returnType = $this->getDeclaringClass()->getParentClass()->name;
    }
    return (string)$returnType;
  }

  public function getReturnDescription(): string
  {
    if ($this->docBlock && count($this->docBlock->getTagsByName('return')) > 0) {
      return $this->docBlock->getTagsByName('return')[0]->getDescription();
    }
    return '';
  }

  public function getInheritsFromLink(): string
  {
    $url = '';
    if (Str::startsWith($this->getDeclaringClass()->name, 'Wagnerwagner\\Merx\\')) {
      $slug = Str::after($this->getDeclaringClass()->name, 'Wagnerwagner\\Merx\\');
      $slug = Str::lower($slug);
      $slug = Str::replace($slug, '\\', '/');
      if ($page = page('docs/classes/' . $slug)) {
        $url = $page->url();
      }
    } else if (Str::startsWith($this->getDeclaringClass()->name, 'Kirby\\')) {
      // $slug = Str::after($this->getDeclaringClass()->name, 'Kirby\\');
      // $slug = Str::lower($slug);
      // $slug = Str::replace($slug, '\\', '/');
      // $slug = Str::replace($slug, 'toolkit/', 'cms/'); // because toolkit is not (yet) documented on getkirby.com
      // $url = 'https://getkirby.com/docs/reference/@/' . $slug;
    }
    return $url;
  }
}
