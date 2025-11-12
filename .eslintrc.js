module.exports = {
	root: true,
	env: {
		browser: true,
	},
	extends: [
		'airbnb-base',
	],
	parserOptions: {
		ecmaVersion: 'latest',
		sourceType: 'module',
	},
	rules: {
		'import/extensions': 'off',
		'no-tabs': 'off',
		indent: ['error', 'tab'],
	},
};
