<?php
/**
 * Plugin Name:       Article Recommendations
 * Plugin URI:        https://github.com/LocalAtBrown/article-recommendations
 * Description:       AMP-compatible article recommendations module
 * Author:            LocalAtBrown
 * Author URI: 		  https://lnl.brown.columbia.edu
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Version:           1.0.0
 *
 * @package Article_Recommendations
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'ARTICLE_RECOMMENDATIONS_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-article-recommendations-activator.php
 */
function activate_article_recommendations() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-article-recommendations-activator.php';
	Article_Recommendations_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-article-recommendations-deactivator.php
 */
function deactivate_article_recommendations() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-article-recommendations-deactivator.php';
	Article_Recommendations_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_article_recommendations' );
register_deactivation_hook( __FILE__, 'deactivate_article_recommendations' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-article-recommendations.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_article_recommendations() {

	$plugin = new Article_Recommendations();
	$plugin->run();

}
run_article_recommendations();