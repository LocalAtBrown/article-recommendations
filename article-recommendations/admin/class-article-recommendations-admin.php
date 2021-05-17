<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Article_Recommendations
 * @subpackage Article_Recommendations/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Article_Recommendations
 * @subpackage Article_Recommendations/admin
 * @author     Erick Martinez Jr <em3591@columbia.edu>
 */
class Article_Recommendations_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $article_recommendations    The ID of this plugin.
	 */
	private $article_recommendations;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $article_recommendations       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $article_recommendations, $version ) {

		$this->article_recommendations = $article_recommendations;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Article_Recommendations_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Article_Recommendations_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->article_recommendations, plugin_dir_url( __FILE__ ) . 'css/article-recommendations-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Article_Recommendations_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Article_Recommendations_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->article_recommendations, plugin_dir_url( __FILE__ ) . 'js/article-recommendations-admin.js', array( 'jquery' ), $this->version, false );

	}

}