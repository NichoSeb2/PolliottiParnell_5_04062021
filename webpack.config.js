const path = require("path");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const RemoveEmptyScriptsPlugin = require("webpack-remove-empty-scripts");

module.exports = {
	context: path.resolve(__dirname, "public/scss"),
	entry: {
		client: ["./client/main.scss"],
		admin: ["./admin/main.scss"],
	},
	module: {
		rules: [
			{
				test: /\.s[ac]ss$/i,
				exclude: /node_modules/,
				use: [
					MiniCssExtractPlugin.loader,
					"css-loader",
					"postcss-loader",
					"sass-loader",
				],
			},
		],
	},
	plugins: [
		new RemoveEmptyScriptsPlugin(),
		new MiniCssExtractPlugin({
			filename: "[name].min.css",
		}),
	],
	output: {
		path: path.resolve(__dirname, "public/css"),
	},
};
