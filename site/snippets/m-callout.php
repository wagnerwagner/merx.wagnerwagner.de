<?php
/**
 * Note block inside of text, written as a GitHub alert
 *
 * @var string $html
 * @var 'note'|'tip'|'important'|'warning'|'caution' $theme The visual theme of the callout box.
 * @see https://base.uber.com/6d2425e9f/p/199f68-banner
 * @see https://docs.github.com/en/get-started/writing-on-github/getting-started-with-writing-and-formatting-on-github/basic-writing-and-formatting-syntax#alerts
 * @see https://component.gallery/components/alert/
 */
$theme ??= 'note';
?>
<div class="m-callout" <?= attr([
	'data-theme' => $theme === 'note' ? null : $theme,
]) ?>>
	<div class="m-text" data-variant="callout">
		<?= $html ?>
	</div>
</div>
