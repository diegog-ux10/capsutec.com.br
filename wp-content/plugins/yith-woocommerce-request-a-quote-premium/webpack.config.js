// var webpack = require("webpack");

const path = require( 'path' );

const CustomTemplatedPathPlugin = require( '@wordpress/custom-templated-path-webpack-plugin' );

const externals = {
	'@wordpress/api-fetch'    	    : { this: ['wp', 'apiFetch'] },
	'@wordpress/element'      	    : { this: ['wp', 'element'] },
	'@wordpress/data'         	    : { this: ['wp', 'data'] },
	'@wordpress/hooks'        	    : { this: ['wp', 'hooks'] },
	'@wordpress/url'          	    : { this: ['wp', 'url'] },
	'@wordpress/html-entities'	    : { this: ['wp', 'htmlEntities'] },
	'@wordpress/i18n'         	    : { this: ['wp', 'i18n'] },
	'@wordpress/icons'         	    : { this: ['wp', 'icons'] },
	'@wordpress/date'         	    : { this: ['wp', 'date'] },
	'@wordpress/block-editor'       : { this: ['wp', 'blockEditor'] },
	'@wordpress/block-library'       : { this: ['wp', 'blockLibrary'] },
	'@wordpress/blocks'             : { this: ['wp', 'blocks'] },
	'@wordpress/components'         : { this: ['wp', 'components'] },
	'@wordpress/compose'            : { this: ['wp', 'compose'] },
	'@wordpress/editor'             : { this: ['wp', 'editor'] },
	'@wordpress/plugins'             : { this: ['wp', 'plugins'] },
	'@wordpress/edit-post'             : { this: ['wp', 'edit-post'] },
	'@wordpress/jest-preset-default': { this: ['wp', 'default'] },
	'@wordpress/scripts'            : { this: ['wp', 'scripts'] },
	'@woocommerce/settings'   	    : { this: ['wc', 'wcSettings'] },
	react                           : 'React',
	lodash                          : 'lodash',
	'react-dom'                     : 'ReactDOM'
};

const webpackConfig = {
	entry      : {
		blocks: "./includes/builders/gutenberg/src/index.js",
		templates: "./includes/builders/gutenberg/src/pdf-templates/index.js"
	},
	output     : {
		filename     : "./[name]/index.js",
		libraryTarget: 'this'
	},
	externals,
	module     : {
		rules: [
			{
				parser: {
					amd: false
				}
			},
			{
				exclude: /node_modules/,
				loader : 'babel-loader',
				options: {
					presets: [
						'@babel/preset-env',
						'@babel/react', { 'plugins': ['@babel/plugin-proposal-class-properties'] }
					]
				}
			}
		]
	},
	resolve    : {
		extensions: ['.json', '.js', '.jsx'],
		modules   : [
			path.join( __dirname, 'src' ),
			'node_modules'
		],
		alias     : {
			'gutenberg-components': path.resolve( __dirname, 'node_modules/@wordpress/components/src' ),
			'react-spring'        : 'react-spring/web.cjs'
		}
	},
	plugins    : [
		new CustomTemplatedPathPlugin( {
			modulename( outputPath, data ) {
				const entryName = get( data, ['chunk', 'name'] );
				if ( entryName ) {
					return entryName.replace( /-([a-z])/g, ( match, letter ) => letter.toUpperCase() );
				}
				return outputPath;
			}
		} )
	],
	performance: {
		maxEntrypointSize: 2000000,
		maxAssetSize     : 2000000
	}
};

module.exports = webpackConfig;
