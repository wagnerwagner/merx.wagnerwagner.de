<form class="m-search" data-variant="combobox" action="<?= url('search') ?>">
	<div class="m-search__field">
		<input
			type="search"
			name="q"
			minlength="3"
			autocomplete="off"
			spellcheck="false"
			aria-label="Search"
			role="combobox"
			aria-controls="results"
			required
		>
		<div class="m-search__submit">
			<button class="a-button" data-variant="icon" data-kind="secondary" type="submit"><?= snippet('a-icon', ['name' => 'search', 'weight' => 700]) ?>
				<span class="a-visually-hidden">Search</span>
			</button>
		</div>
	</div>
	<div class="m-search__info"></div>
	<ul
		class="m-search__results"
		id="results"
		role="listbox"
		aria-label="Search results"
	>
	</ul>
</form>
