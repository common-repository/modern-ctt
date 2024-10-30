import { __ } from "@wordpress/i18n";
import { InspectorControls } from "@wordpress/block-editor";
import { PanelBody, TextControl, RadioControl, ToggleControl } from "@wordpress/components";

/**
 * Create an Inspector Controls wrapper Component
 */
const Inspector = ({ attributes, setAttributes, updateCount }) => {
  const {url, handle, hashtags, ctaStyle, ctaContent} = attributes;

  return (
    <InspectorControls>
      <PanelBody title={__( 'Block settings', 'modern-ctt' )}>
        <TextControl
          label={__('URL to share', 'modern-ctt')}
          help={__('If left empty, defaults to post permalink', 'modern-ctt')}
          value={url}
          onChange={value => setAttributes({'url': value})}
        />
        <TextControl
          label={__('Twitter handle', 'modern-ctt')}
          value={handle}
          onChange={value => { setAttributes({'handle': value}); updateCount() } }
        />
        <TextControl
          label={__('Hashtags', 'modern-ctt')}
          value={hashtags}
          help={__('Add in your hastags, just as you would on any tweet.', 'modern-ctt')}
          onChange={value => { setAttributes({'hashtags': value}); updateCount(); } }
        />
        <label class="styledLabel">{__('Call to Action display', 'modern-ctt')}</label>
        <ToggleControl
          label={__('Display link as a button', 'modern-ctt')}
          checked={ 'modern-ctt-button' === ctaStyle }
          onChange={ () => setAttributes({'ctaStyle': 'modern-ctt-button' === ctaStyle ? 'modern-ctt-link' : 'modern-ctt-button'})}
        />
        <RadioControl
          label={__('Call to Action content', 'modern-ctt')}
          selected={ctaContent}
          options={ [
              { label: __('Icon and text', 'modern-ctt'), value: 'icon&text' },
              { label: __('Icon only (text will still be visible to screen readers)', 'modern-ctt'), value: 'icon' },
              { label: __('Text only', 'modern-ctt'), value: 'text' },
          ] }
          onChange={ctaContent => setAttributes({'ctaContent': ctaContent})}
        />
      </PanelBody>
    </InspectorControls>
  );
};

export default Inspector;
