<?php

use Kirby\Cms\Pagination;
use Kirby\Toolkit\A;

/** @var Pagination $pagination */

$range = 6;
$currentPage = $pagination->page();
$pagesCount = $pagination->pages();
$paginationRange = $pagination->range($range);
$paginationRangeFirst = A::first($paginationRange);
$paginationRangeLast = A::last($paginationRange);
?>
<ul class="a-pagination">
	<?php if ($pagesCount > $range && $paginationRangeFirst !== 1): ?>
		<li>
			<a <?= attr([
				'href' => $pagination->pageUrl(1),
				'class' => 'a-button',
				'data-kind' => 'primary',
				'data-size' => 'small',
				'data-shape' => 'squared',
				'data-variant' => 'ghost',
				'aria-label' => 'First page',
			]) ?>>
				<?= 1 ?>
			</a>
		</li>
		<li class="a-pagination__seperator">…</li>
	<?php endif ?>
	<?php foreach ($paginationRange as $r): ?>
	<li>
		<a <?= attr([
			'href' => $pagination->pageUrl($r),
			'class' => 'a-button',
			'data-kind' => 'primary',
			'data-size' => 'small',
			'data-shape' => 'squared',
			'data-variant' => 'ghost',
			'data-selected' => $currentPage === $r,
			'aria-current' => $currentPage === $r ? 'page' : null,
		]) ?>>
			<?= $r ?>
		</a>
	</li>
	<?php endforeach ?>
	<?php if ($pagesCount > $range && $paginationRangeLast !== $pagesCount): ?>
		<li class="a-pagination__seperator">…</li>
		<li>
			<a <?= attr([
				'href' => $pagination->pageUrl($pagesCount),
				'class' => 'a-button',
				'data-kind' => 'primary',
				'data-size' => 'small',
				'data-shape' => 'squared',
				'data-variant' => 'ghost',
				'aria-label' => 'Last page',
			]) ?>>
				<?= $pagesCount ?>
			</a>
		</li>
	<?php endif ?>
</ul>
