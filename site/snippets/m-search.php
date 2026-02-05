<form class="m-search" action="<?= url('search') ?>">
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
	<!-- <button type="submit" tabindex="-1" title="Toggle Search"></button> -->
	<div class="m-search__info"></div>
	<ul
		class="m-search__results"
		id="results"
		role="listbox"
		aria-label="Search results"
	>
	</ul>
</form>
