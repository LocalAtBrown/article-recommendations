<!-- This file contains metadata about the WP plugin written as a PHP comment  -->
<?php
/**
 * Plugin Name: WP JS Plugin Starter
 * Plugin URI: https://github.com/youknowriad/wp-js-plugin-starter
 * Description: Just another WordPress plugin starter
 * Version: 1.0.0
 * Author: Erick Martinez Jr
 *
 * @package wp-js-plugin-starter
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
function wp_js_plugin_starter_url( $path ) {
	return plugins_url( $path, __FILE__ );
}

/**
 * Registers the plugin's script to be enqueued later.
 *
 * @since 1.0.0
 */
function wp_js_plugin_starter_register_script() {
	wp_register_script(
		'wp-js-plugin-starter',
		wp_js_plugin_starter_url( 'dist/index.js' ),
		array('wp-element')
  );
}

/**
 * Enqueues the plugin's script. (loads it into Wordpress)
 *
 * @since 1.0.0
 */
function wp_js_plugin_starter_enqueue_script() {
	wp_enqueue_script( 'wp-js-plugin-starter' );
}

/**
 * Trigger the script registration on init.
 */
add_action( 'init', 'wp_js_plugin_starter_register_script' );

/**
 * Enqueue the script in all admin pages
 */
add_action( 'admin_enqueue_scripts', 'wp_js_plugin_starter_enqueue_script' ); 