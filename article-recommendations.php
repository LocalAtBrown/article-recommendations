<!-- This file contains metadata about the WP plugin written as a PHP comment  -->
<?php
/**
 * Plugin Name: Article Recommendations
 * Plugin URI: https://github.com/LocalAtBrown/article-recommendations
 * Description: AMP-compatible article recommendations module
 * Author: LocalAtBrown
 * Version: 1.0.0
 *
 * @package Article_Recommendations
 */

/**
 * Retrieves a URL to a file in the plugin's directory.
 *
 * @param  string $path Relative path of the desired file.
 *
 * @return string       Fully qualified URL pointing to the desired file.
 *
 * @since 1.0.0
 */
function article_recommendations( $path ) {
	return plugins_url( $path, __FILE__ );
}

/**
 * Registers the plugin's block.
 *
 * @since 1.0.0
 */
function article_recommendations_register_block() {
	if (!function_exists('register_block_type')) {
		return;
	}

	wp_register_script(
		'article-recommendations',
		article_recommendations( 'dist/index.js' ),
		array('wp-blocks','wp-element'), '1.0.0'
	);
		register_block_type( 'article-recommendations/hello-world', array(
		'editor_script' => 'article-recommendations',
	) );
}

/**
 * Trigger the block registration on init.
 */
add_action( 'init', 'article_recommendations_register_block' );