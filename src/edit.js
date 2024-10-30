import Inspector from "./inspector";
import icons from './icons.js';
import './editor.scss';

const { __ } = wp.i18n;
const { useBlockProps, BlockControls, AlignmentToolbar, RichText, getColorClassName } = wp.blockEditor;
const { Icon } = wp.components;


/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function edit( { attributes, setAttributes } ) {
	const blockProps = useBlockProps();
	const { textAlign, displayText, count, prompt, ctaContent, ctaStyle, className, hashtags, handle, url } = attributes;
	
	const updateText = newValue => {
		setAttributes( { displayText: newValue } );
		updateCount()
	};

	const updateCount = () => {
		// const postUrl = wp.data.select('core/editor').getPermalink()
		const temp = document.createElement('div');
		temp.innerHTML = displayText;
		const raw = temp.textContent || '';
		// const hashtagsLength = hashtags ? hashtags.length + 1 : 0;
		// const handleLength = handle ? handle.length + 6 : 0;
		// const urlLength = url ? url.length + 1 : postUrl.length + 1;
		// const updatedCount = raw.length + 1 + hashtagsLength + handleLength + urlLength;
		const updatedCount = raw.length;
		setAttributes( { count: parseInt( updatedCount ) } );
	}
	
	const maxCount = 280;
	const countClass = count > maxCount ? 'modern-ctt-character-counter exceeded' : 'modern-ctt-character-counter';
	const alignClass = `has-text-align-${ textAlign }`;
	const ctaTextClass = 'icon' === ctaContent ? 'screen-reader-text' : '';
	const ctaIconClass = 'text' === ctaContent ? 'hidden' : '';
	const quote = className && className.includes('is-style-quote') ? <div class="modern-ctt-quotemark"><Icon className="icon icon-quote" icon={icons.quote}/></div> : '';
	setAttributes( { textColorClass: getColorClassName('color', attributes['textColor']) } );
	setAttributes( { backgroundColorClass: getColorClassName('background-color', attributes['backgroundColor']) } );
	setAttributes( { backgroundGradientClass: getColorClassName('gradient-background', attributes['gradient']) } );
	if( ! className ){ setAttributes( { className: 'is-style-default' } ); }
	
	return (
		<>
			<Inspector attributes={attributes} setAttributes={setAttributes} updateCount={updateCount} />
			<BlockControls>
				<AlignmentToolbar
					value={ textAlign }
					onChange={ nextAlign => { setAttributes( { textAlign: nextAlign } ); } }
				/>
			</BlockControls>
			<div {...blockProps} >
				<div class="modern-ctt">
					{quote}
					<span class={alignClass}>
						<RichText
							identifier="displayText"
							multiline
							tagName="div"
							value={ displayText }
							onChange={ updateText }
							allowedFormats={ [ 'core/bold', 'core/italic' ] }
							aria-label={ __( 'Quote text', 'modern-ctt' ) }
							placeholder={ __( 'Write text displayed on the site.', 'modern-ctt' ) }
						/>
					</span>
					<div class={countClass}>{count} / {maxCount}</div>
					<footer class="modern-ctt-footer">
						<span class={ `modern-ctt-cta ${ctaStyle}` }>
							<span class={ `modern-ctt-prompt ${ctaTextClass}`}>
								<RichText
									identifier="prompt"
									value={ prompt }
									tagName="span"
									onChange={ ( newValue ) => setAttributes( { prompt: newValue } ) }
									allowedFormats={ [ 'core/bold', 'core/italic' ] }
									aria-label={ __( 'Button prompt', 'modern-ctt' ) }
									default={ __( 'Tweet this', 'modern-ctt' ) }
									/>
							</span>
							<Icon className={ `icon icon-twitter ${ctaIconClass}`} icon={icons.twitter}/>
						</span>
					</footer>
				</div>
			</div>
		</>
	);
}
