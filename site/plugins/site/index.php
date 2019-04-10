<?php
use Merx\Merx;
use Kirby\Toolkit\Str;
use Kirby\Toolkit\Collection;
use Kirby\Toolkit\Html;

@include_once __DIR__ . '/vendor/autoload.php';
require_once 'country-list.php';
$licenseBase = file_get_contents('.license-base', FILE_USE_INCLUDE_PATH);

function niceExcerpt($string, $query) {
  $start = stripos($string, $query) - 100;
  $start = $start < 0 ? 0 : $start;
  if ($start > 0) {
    $string = Str::substr($string, $start);
    $string = '… ' . Str::after($string, ' ');
  } else {
    $string = Str::substr($string, 0);
  }
  $excerpt = Str::excerpt($string, 200 + Str::length($query));
  return $excerpt;
}


function sendConfirmationMail(OrderPage $orderPage) {
  kirby()->email([
    'from' => option('merx-email'),
    'to' => (string)$orderPage->email(),
    'subject' => 'Your Merx Order',
    'template' => 'confirmation',
    'data' => [
      'page' => $orderPage,
    ],
  ]);
}


Kirby::plugin('wagnerwagner/site', [
  'options' => [
    'countryList' => $countryList,
  ],
  'tags' => [
    'fileContents' => [
      'html' => function($tag) {
        try {
          return file_get_contents(kirby()->root('site') . DS . $tag->value);
        } catch (Exception $ex) {
          return 'No such file or directory. (' . $tag->value . ')';
          // return $ex->getMessage();
        }
      },
    ],
    'github' => [
      'attr' => [
        'line',
        'text',
        'repository',
      ],
      'html' => function($tag) {
        $url = option('github-repository');
        if ($tag->url) {
          $url = $tag->url;
        }
        $url .= '/blob/master' . $tag->value;
        $text = 'Source Code';
        if ($tag->line) {
          $url .= '#L' . $tag->line;
        }
        if ($tag->text) {
          $text = $tag->text;
        }
        return '<a class="link-secondary" href="'. $url . '">' . $text . '</a>';
      }
    ],
    'filename' => [
      'html' => function($tag) {
        return '<h3 class="filename">' . $tag->value . '</h3>';
      }
    ],
  ],
  'pageMethods' => [
    'seoTitle' => function (): string
    {
      if ($this->isHomePage()) {
        return $this->title();
      } else if ((string)$this->intendedTemplate() === 'order') {
        return 'Invoice ' . $this->invoiceNumber() . ' – ' . $this->site()->title();
      }
      return $this->title() . ' – ' . $this->site()->title();
    }
  ],
  'siteMethods' => [
    'prettySearch' => function ($query = ''): Kirby\Cms\Pages
    {
      $sitesToSearch = $this->find('docs', 'cookbooks')->index()->listed()->filterBy('intendedTemplate', '!=', 'order');
      $result = $sitesToSearch->search($query);
      $result->map(function ($page) use ($query) {
        $contentCollection = new Kirby\Toolkit\Collection($page->content()->data());
        $pageResults = $contentCollection->filter(function ($page) use ($query) {
          return Str::contains(strtolower($page), strtolower($query));
        });
        $excerpt = $pageResults->first();
        if ($pageResults->findByKey('excerpt')) {
          $excerpt = $pageResults->findByKey('excerpt');
        } else if ($pageResults->findByKey('text')) {
          $excerpt = $pageResults->findByKey('text');
        }
        $excerpt = niceExcerpt(HTML::decode(kirbytext($excerpt)), $query);
        if ($excerpt !== (string)$page->title()) {
          $page->content()->update([
            'excerpt' => preg_replace('/'.$query.'/i', '<b>${0}</b>', $excerpt),
          ]);
        }
        return $page;
      });
      return $result;
    }
  ],
  'hooks' => [
    'ww.merx.completePayment:after' => function($orderPage) use ($licenseBase) {
      $quantity = $orderPage->cart()->findBy('id', 'merx-license')['quantity'];
      $licenses = [];
      while (count($licenses) < $quantity) {
        $licenses[] = 'MERX-' . strtoupper(dechex(A::join(A::shuffle(str_split((int)$licenseBase)), '')) . '-' . dechex(A::join(A::shuffle(str_split((int)$licenseBase)), '')));
      }
      $orderPage = $orderPage->update([
        'licenses' => A::join($licenses),
      ]);
      sendConfirmationMail($orderPage);
    },
  ],
  'routes' => [
    [
      'method' => 'post',
      'pattern' => 'merx-api/buy',
      'action' => function() {
        $data = $_POST;
        try {
          $redirect = merx()->initializePayment($data);
          return [
            'status' => 201,
            'redirect' => $redirect,
          ];
        } catch (Kirby\Exception\Exception $ex) {
          return [
            'status' => $ex->getHttpCode(),
            'code' => $ex->getCode(),
            'message' => $ex->getMessage(),
            'details' => $ex->getDetails(),
          ];
        };
      },
    ],
    [
      'method' => 'post',
      'pattern' => 'merx-api/update-cart',
      'action' => function() {
        setlocale(LC_ALL, 'en_US');
        $product = page('merx-license');
        $quantity = (int)$_POST['quantity'];
        if ($quantity > 10) {
          throw Exception('Quantity can’t be more than 10.');
        }
        $cart = merx()->cart();
        $cart->updateItem([
          'id' => $product->id(),
          'quantity' => $quantity,
        ]);
        $data = $cart->first();
        return [
          'quantity' => $data['quantity'],
          'sumNet' => formatPrice($data['sum'] - $data['sumTax']),
          'sumTax' => '+ Vat (19%) ' . formatPrice($data['sumTax']),
          'sum' => formatPrice($data['sum'], 'before'),
        ];
      }
    ],
    [
      'method' => 'get',
      'pattern' => 'search-api',
      'action' => function() {
        $query = get('q');
        $results = site()->prettySearch($query)->limit(6);
        $results->map(function ($page) {
          return [
            'title' => (string)$page->title(),
            'id' => $page->id(),
            'url' => $page->url(),
            'excerpt' => (string)$page->excerpt(),
          ];
        });
        return new Response(json_encode(array_values($results->data()), JSON_PRETTY_PRINT), 'application/json');
      }
    ]
  ],
]);
