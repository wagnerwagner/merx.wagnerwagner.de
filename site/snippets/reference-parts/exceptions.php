<h2>Exceptions</h2>
<div class="m-table">
	<table>
		<thead>
			<th>Type</th>
			<th>Description</th>
		</thead>
		<?php foreach ($exceptions as $exception): ?>
			<tr>
				<td><?= $exception['type']->toHtml() ?></td>
				<td><?= $exception['description'] ?></td>
			</tr>
		<?php endforeach ?>
	</table>
</div>
