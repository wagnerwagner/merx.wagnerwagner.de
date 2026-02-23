<?php $showDescription = count(array_filter($page->params(), fn ($param) => !empty($param['description']))) > 0; ?>
<h2>Parameters</h2>
<div class="m-table">
	<table>
		<thead>
			<th>Name</th>
			<th>Type</th>
			<th>Default</th>
			<?php if ($showDescription): ?>
				<th>Description</th>
			<?php endif ?>
		</thead>
		<?php foreach ($page->params() as $param): ?>
			<tr>
				<td><code class="a-type"><?= $param['name'] ?></code></td>
				<td><?= $param['types']?->toHtml(short: true) ?></td>
				<td><code class="a-type"><?= $param['defaultValue'] === null ? '-' : $param['defaultValue'] ?></code></td>
				<?php if ($showDescription): ?>
					<td><?= kt($param['description']) ?></td>
				<?php endif ?>
			</tr>
		<?php endforeach ?>
	</table>
</div>
