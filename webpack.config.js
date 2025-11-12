const path = require('path');

module.exports = {
	entry: {
		index: './develop/js/index.js',
		// add more js files here.
	},
	mode: 'production',
	devtool: 'source-map',
	output: {
		path: path.resolve(__dirname, 'public/assets/js'),
	},
	module: {
		rules: [
			{
				test: /\.m?js$/,
				exclude: /(node_modules|bower_components)/,
				use: {
					loader: 'babel-loader',
				},
			},
		],
	},
};
