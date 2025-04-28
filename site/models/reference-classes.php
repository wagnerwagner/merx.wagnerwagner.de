<?php

use Kirby\Cms\Page;
use Kirby\Cms\Pages;
use Kirby\Data\Data;
use Kirby\Filesystem\Dir;
use Kirby\Toolkit\Str;

class ReferenceClassesPage extends Page
{
  /**
   * Creates children collection by parsing the `src/` folder of
   * the Kirby core
   */
  public function children(): Pages
  {
    if ($this->children !== null) {
      return $this->children;
    }

    $children = [];

    // Add unlisted pages for all classes in namespace:
    // Loop through filesystem as proxy for namespace structure
    $root = $this->kirby()->plugin('ww/merx')->root() . '/src';

    $children = $this->childrenForClasses(
      'Wagnerwagner\\Merx',
      $root,
    );

    return $this->children = Pages::factory($children, $this);
  }

  /**
   * Creates an array of page properties for all class files in the
   * provided root, assigning them to a provided namespace
   */
  protected function childrenForClasses(
    string $namespace,
    string $root
  ): array {
    $children = [];

    // Loop through each class PHP file and
    // create as child page
    foreach (Dir::files($root) as $class) {
      $name  = ucfirst(basename($class, '.php'));
      $class = $namespace . '\\' . $name;
      $slug  = Str::kebab($name);
      $root  = $this->root() . '/' . Str::kebab($namespace) . '/0_' . $slug;

      try {
        $content = Data::read($root . '/reference-class.txt');
      } catch (Throwable) {
        $content = [];
      }

      $children[] = [
        'slug'     => $slug,
        'root'     => $root,
        'model'    => 'reference-class',
        'content'  => [
          ...$content,
          'class' => $class
        ]
      ];
    }

    // we need to create a Pages collection to properly filter
    // pages (e.g. as non-internal); however, we need to pass the
    // data on as array again to be consumable by the upper
    // Pages::factory() call
    return Pages::factory($children)
      ->filterBy('isInternal', false)
      ->toArray(fn ($page) => [
        'slug'     => $page->slug(),
        'root'     => $page->root(),
        'model'    => 'reference-class',
        'template' => 'reference-class',
        'num'      => 0,
        'content'  => $page->content()->toArray()
      ]);
  }

  public static function isFeatured(string $page): bool
  {
    $ids = [
      ...page('docs/reference/objects')->menu()->yaml(),
      ...page('docs/reference/tools')->menu()->yaml()
    ];

    foreach ($ids as $id) {
      if (Str::endsWith($page, $id)) {
        return true;
      }
    }

    return false;
  }
}
