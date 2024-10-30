<?php
/**
 * Plugin Name:        Modern CTT
 * Description:        Adds a beautiful (and customizable) Click to Tweet block to maximize content sharing on Twitter.
 * Version:            1.1.0
 * Author:             Modern Plugins
 * Author URI:         https://modernplugins.com/
 * Contributors:       modernplugins, vincentdubroeucq
 * License:            GPL v3 or later
 * License URI:        https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:        modern-ctt
 * Domain Path:        languages/
 * Requires at least:  5.6
 * Requires PHP :      5.6
 * Tested up to:       5.7.1
 *
 * @package  modernplugins/modern-ctt
 */

/*
Modern CTT is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
Modern CTT is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with Modern CTT. If not, see https://www.gnu.org/licenses/gpl-3.0.html.
*/

defined( 'ABSPATH' ) || die();

define( 'MODERN_CTT_PATH', plugin_dir_path( __FILE__ ) );
define( 'MODERN_CTT_URL', plugin_dir_url( __FILE__ ) );
define( 'MODERN_CTT_VERSION', '1.1.0' );


add_action( 'plugins_loaded', 'modern_ctt_load_textdomain' );
/**
 * Load plugin textdomain.
 */
function modern_ctt_load_textdomain() {
  load_plugin_textdomain( 'modern-ctt', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
}

add_action( 'init', 'modern_ctt_block_init' );
/**
 * Registers block assets
 */
function modern_ctt_block_init() {		
	wp_register_style( 'modern-ctt-block', MODERN_CTT_URL . 'build/style-index.css', array(), MODERN_CTT_VERSION );
	wp_register_style( 'modern-ctt-editor', MODERN_CTT_URL . 'build/index.css', array(), MODERN_CTT_VERSION );
	wp_register_script( 'modern-ctt-editor', MODERN_CTT_URL . '/build/index.js', array( 'wp-block-editor', 'wp-blocks', 'wp-element', 'wp-i18n', 'wp-polyfill' ), MODERN_CTT_VERSION );
	wp_set_script_translations( 'modern-ctt-editor', 'modern-ctt', MODERN_CTT_PATH . 'languages' );

	register_block_type(
		'modernplugins/modern-ctt',
		array(
			'editor_script'   => 'modern-ctt-editor',
			'editor_style'    => 'modern-ctt-editor',
			'style'           => 'modern-ctt-block',
			'render_callback' => 'modern_ctt_block_callback',
			'attributes' => array(
				'displayText' => array(
					'type'    => 'string',
					'default' => ''
				),
				'count' => array(
					'type'    => 'number',
					'default' => 0
				),
				'prompt' => array(
					'type'    => 'string',
					'default' => apply_filters( 'modern_ctt_default_prompt', __( 'Click to Tweet', 'modern-ctt' ) )
				),
				'ctaContent' => array(
					'type'    => 'string',
					'default' => 'icon&text'
				),
				'ctaStyle' => array(
					'type'    => 'string',
					'default' => 'modern-ctt-link'
				),
				'url' => array(
					'type'    => 'string',
					'default' => ''
				),
				'handle' => array(
					'type'    => 'string',
					'default' => ''
				),
				'hashtags' => array(
					'type'    => 'string',
					'default' => ''
				),
				'textColorClass' => array(
					'type'    => 'string',
					'default' => ''
				),
				'backgroundColorClass' => array(
					'type'    => 'string',
					'default' => ''
				),
				'backgroundGradientClass' => array(
					'type'    => 'string',
					'default' => ''
				),
				'textAlign' => array(
					'type'    => 'string',
					'default' => ''
				),
			),
		)
	);
}

/**
 * Render callback function for our block
 * 
 * @param   array   $attributes  Block attributes
 * @return  string  $html        Final block html
 */
function modern_ctt_block_callback( $attributes, $content, $block ){
	$post_id = get_the_ID();

	$class   = modern_ctt_get_block_classnames( $attributes );
	$style   = modern_ctt_get_block_styles( $attributes );
	$url     = ! empty( $attributes['url'] ) ? esc_url( $attributes['url'] ) : get_permalink();
	$handle  = ! empty( $attributes['handle'] ) ? $attributes['handle'] : modern_ctt_get_default_handle();

	$params  = array(
		'url'  => urlencode( $url ),
		'text' => urlencode( wp_strip_all_tags( $attributes['displayText'] ) )
	);

	if( $handle ){
		$handle        = str_replace( '@', '', $handle );
		$handle        = apply_filters( 'modern_ctt_twitter_handle', $handle, $post_id );
		$params['via'] = urlencode( $handle );
	}

	if( ! empty( $attributes['hashtags'] ) ){
		$hashtags_array = explode( '#', $attributes['hashtags'] );
		$hashtags_array = array_map( function( $hashtag ){ return trim( trim( sanitize_text_field( $hashtag ), ',' ) ); }, $hashtags_array );
		$hashtags       = join( ',', array_filter( $hashtags_array ) );
		$params['hashtags'] = urlencode( $hashtags );
	}

	$intent_url     = add_query_arg( $params, 'https://twitter.com/intent/tweet' );
	$cta_text_class = 'icon' === $attributes['ctaContent'] ? 'screen-reader-text' : '';
	$cta_icon_class = 'text' === $attributes['ctaContent'] ? 'hidden' : '';
	$align_class    = ! empty( $attributes['textAlign'] ) ? sprintf( 'has-text-align-%s', sanitize_html_class( $attributes['textAlign'] ) ) : '';
	$quote          = ( ! empty( $attributes['className'] ) && false !== strpos( $attributes['className'], 'is-style-quote' ) ) ? '<svg class="icon icon-quote" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16"><rect x="0" fill="none" width="16" height="16"/><g><path d="M2 9h3c0 1.1-.9 2-2 2v2c2.2 0 4-1.8 4-4V4H2v5zm7-5v5h3c0 1.1-.9 2-2 2v2c2.2 0 4-1.8 4-4V4H9z"/></g></svg>' : '';

	ob_start();
	?>
		<div class="<?php echo esc_attr( $class ) ?>" <?php echo $style; ?>>
			<a class="modern-ctt" href="<?php echo esc_url( $intent_url ); ?>" target="_blank" rel="noopener noreferer">
				<?php if( $quote ) : ?><div class="modern-ctt-quotemark"><?php echo $quote; ?></div><?php endif; ?>
				<div class="modern-ctt-content <?php echo esc_attr( $align_class ); ?>"><?php echo wp_kses_post( $attributes['displayText'] ) ?></div>
				<footer class="modern-ctt-footer">
					<span class="modern-ctt-cta <?php echo esc_attr( $attributes['ctaStyle'] ); ?>">
						<span class="modern-ctt-prompt <?php echo esc_attr( $cta_text_class ); ?>"><?php echo wp_kses_post( $attributes['prompt'] ) ?></span>
						<svg class="icon icon-twitter <?php echo esc_attr( $cta_icon_class ); ?>" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><rect x="0" fill="none" width="24" height="24"/><g><path d="M22.23 5.924a8.212 8.212 0 01-2.357.646 4.115 4.115 0 001.804-2.27 8.221 8.221 0 01-2.606.996 4.103 4.103 0 00-6.991 3.742 11.647 11.647 0 01-8.457-4.287 4.087 4.087 0 00-.556 2.063 4.1 4.1 0 001.825 3.415 4.09 4.09 0 01-1.859-.513v.052a4.104 4.104 0 003.292 4.023 4.099 4.099 0 01-1.853.07 4.11 4.11 0 003.833 2.85 8.236 8.236 0 01-5.096 1.756 8.33 8.33 0 01-.979-.057 11.617 11.617 0 006.29 1.843c7.547 0 11.675-6.252 11.675-11.675 0-.178-.004-.355-.012-.531a8.298 8.298 0 002.047-2.123z"/></g></svg>
					</span>	
				</footer>
			</a>
		</div>
	<?php
	$html = ob_get_clean();
	return apply_filters( 'modern_ctt_block_html', $html, $attributes );
}


/**
 * Returns a string with all the block classnames
 * 
 * @param   array   $attributes  Block attributes
 * @return  string  $classes     Block classnames
 */
function modern_ctt_get_block_classnames( $attributes ){
	$classes   = 'wp-block wp-block-modernplugins-modern-ctt';
	$classes  .= ! empty ( $attributes['className'] ) ? ' ' . $attributes['className'] : '';
	$classes  .= ! empty ( $attributes['align'] ) ? sprintf( ' align%s', esc_attr( $attributes['align'] ) ) : '';
	$classes  .= ! empty ( $attributes['backgroundColorClass'] ) ? sprintf( ' has-background %s', esc_attr( $attributes['backgroundColorClass'] ) ) : '';
	$classes  .= ! empty ( $attributes['textColorClass'] ) ? sprintf( ' has-text-color %s', esc_attr( $attributes['textColorClass'] ) ) : '';
	$classes  .= ! empty ( $attributes['backgroundGradientClass'] ) ? sprintf( ' has-background %s', esc_attr( $attributes['backgroundGradientClass'] ) ) : '';
	return apply_filters( 'modern_ctt_block_classnames', $classes, $attributes );
}


/**
 * Returns a string with the block's style attributes
 * 
 * @param   array   $attributes  Block attributes
 * @return  string  $style       Style attribute and its value
 */
function modern_ctt_get_block_styles( $attributes ){
	$style  = '';
	$styles = array();
	$colors = ! empty( $attributes['style']['color'] ) ? $attributes['style']['color'] : false;
	
	if( $colors ){
		if( ! empty( $colors['gradient'] ) ){
			$styles['background-image'] = esc_attr( $colors['gradient'] );
		}
		if( ! empty( $colors['text'] ) ){
			$styles['color'] = sanitize_hex_color( $colors['text'] );
		}
		if( ! empty( $colors['background'] ) ){
			$styles['background-color'] = sanitize_hex_color( $colors['background'] );
		}
	}
	if( ! empty( $styles ) ){
		foreach ( $styles as $property => $value ) {
			$style .= sprintf( '%s:%s;', sanitize_key( $property ), $value );
		}
		$style = sprintf( 'style="%s"', esc_attr( $style ) );
	}

	return apply_filters( 'modern_ctt_block_style_attribute', $style, $attributes );
}

/**
 * Returns the default Twitter handle
 * Gets author Twitter, or site Twitter in Yoast settings
 * 
 * @return  string  $default_handle
 */
function modern_ctt_get_default_handle(){
	$yoast_options  = get_option( 'wpseo_social' );
	$site_twitter   = is_array( $yoast_options ) && ! empty( $yoast_options['twitter_site'] ) ? $yoast_options['twitter_site'] : false;
	$author_twitter = ! empty( get_the_author_meta( 'twitter' ) ) ? get_the_author_meta( 'twitter' ) : false;
	$default_handle = $author_twitter ? $author_twitter : $site_twitter;
	return apply_filters( 'modern_ctt_default_twitter_handle', $default_handle );
}
