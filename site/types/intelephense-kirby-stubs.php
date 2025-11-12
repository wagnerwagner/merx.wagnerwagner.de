<?php

/**
 * Stub classes to help static analysis tools (e.g. Intelephense) when the Kirby CMS
 * source is provided via a git submodule that is not indexed.
 *
 * These definitions are only loaded by analyzers and won't override the real Kirby
 * classes thanks to the class_exists guards.
 */

namespace Kirby\Cms;

if (!class_exists(Page::class)) {
	abstract class Page
	{
	}
}

if (!class_exists(Pages::class)) {
	class Pages implements \IteratorAggregate
	{
		public function getIterator(): \Traversable
		{
			return new \ArrayIterator([]);
		}
	}
}

