<?php

namespace Wagnerwagner\Site;

use Kirby\Cms\Page;
use Kirby\Toolkit\Str;
use Reflector;
use ReflectionClass;
use ReflectionMethod;

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
    $root = $this->kirby()->plugin('ww/merx')->root() . '/';
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

    return $this->kirby()->plugin('ww/merx')->root() . '/' . $relativeFilePath;
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
        return 'cursor://file/' . $absoluteFilePath . ':' . $line ?? 1;
        return 'nova://open?path=' . $absoluteFilePath . '&line=' . $line ?? 1;
      }
		}

		if ($namespace === 'Kirby') {
			$gitHubRoot = option('github-repositories.kirby');
			$version = $this->kirby()->version();
		} else if ($namespace === 'Wagnerwagner') {
			$gitHubRoot = option('github-repositories.merx');
			$version = $this->kirby()->plugin('ww/merx')->version();
		} else {
			// Default to merx for models without namespace detection
			$gitHubRoot = option('github-repositories.merx');
			$version = $this->kirby()->plugin('ww/merx')->version();
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
}

