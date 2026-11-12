<?php /** @var ReferenceClassPage $page */ ?>
<h2>Properties</h2>
<div class="m-table">
	<table>
		<thead>
			<th>Name</th>
			<th>Type</th>
			<th>Description</th>
		</thead>
		<?php foreach ($page->properties() as $property): ?>
			<tr>
				<td><code class="a-type"><?= $property['name'] ?></code></td>
				<td><?= $property['types']->toHtml(short: true) ?></td>
				<td><?= kti($property['description']) ?></td>
			</tr>
		<?php endforeach ?>
	</table>
</div>
