<h2>Translation strings</h2>
<div class="m-table">
	<table>
		<thead>
			<th>Language</th>
			<th>Translation</th>
		</thead>
		<?php foreach ($page->translationStrings() as $langKey => $translationString): ?>
			<tr>
				<td><?= $langKey ?></td>
				<td><?= $translationString ?></td>
			</tr>
		<?php endforeach ?>
	</table>
</div>
