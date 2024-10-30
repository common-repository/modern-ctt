import { registerBlockType } from '@wordpress/blocks';
import { __, _x } from '@wordpress/i18n';
import icons from './icons.js';
import './style.scss';

import edit from './edit';
import save from './save';

/**
 * Register new block type definition.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/#registering-a-block
 */
registerBlockType('modernplugins/modern-ctt', {
	apiVersion: 2,
	title: __('Modern CTT', 'modern-ctt'),
	description: __('Adds a Click To Tweet blockquote.', 'modern-ctt'),
	keywords: [ __('tweet', 'modern-ctt'), __('click', 'modern-ctt') , __('click to tweet', 'modern-ctt')],
	category: 'text',
	icon: icons.twitter,
	supports: {
		align: true,
		anchor: true,
		customClassName: true,
		html: false,
		color: {
			gradients: true
		}
	},
	example: {
		attributes: {
			displayText: <p>{__('Code is poetry.', 'modern-ctt')}</p>,
			prompt: <span>{ __('Tweet this !', 'modern-ctt')}</span>,
			count: 25,
			className: 'is-style-default',
			ctaContent: '',
			ctaStyle: 'modern-ctt-link',
			url: '',
			handle: '',
			hashtags: '#wordpress',
			textColorClass: '',
			backgroundColorClass: '', 
			backgroundGradientClass: '', 
			textAlign: '',
		},
	},
	styles: [
		{ name: 'default', label: _x('Default', 'block style', 'modern-ctt'), isDefault: true },
		{ name: 'twitter', label: _x('Twitter blue', 'block style', 'modern-ctt') },
		{ name: 'minimal', label: _x('Minimal', 'block style', 'modern-ctt') },
		{ name: 'plain', label: _x('Plain', 'block style', 'modern-ctt') },
		{ name: 'quote', label: _x('Quote', 'block style', 'modern-ctt') },
		{ name: 'border', label: _x('Bordered', 'block style', 'modern-ctt') },
	],
	edit,
	save,
} );
