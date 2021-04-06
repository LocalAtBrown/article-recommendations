<?php

/**
 * Article Recommendations Widget
 *
 * @package ArticleRecommendationsWidget
 * @author Erick Martinez Jr <em3591@columbia.edu>
 *
 * @wordpress-plugin
 * Plugin Name: Article Recommendations Widget
 * Plugin URI: https://github.com/LocalAtBrown/article-recommendations
 * Description: AMP-compatible article recommendations widget
 * Author: LocalAtBrown
 * Version: 1.0.0
 * Github Branch: main
 *
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
	die;
}

add_action('widgets_init', 'article_register_widget');

function article_register_widget() {
	register_widget('article_widget');
}

class article_widget extends WP_Widget {

	function __construct() {
		parent::__construct(

			// Base ID of your widget
			'article_recommendations',

			// Widget name will appear in UI
			__('Article Recommendations', ' article_recommendations_domain'),

			// Widget description
			array('description' => __('Add a list of reader personalized article recommendations', 'article_recommendations_domain'),)

		);
	}

	public function widget($args, $instance) {
		extract($args);

		$title = apply_filters('widget_title', $instance['title']);

		// add widget_recent_entries class to article_recommendations widget
		$widget_recent_entries = !empty($instance['widget_recent_entries']) ? $instance['widget_recent_entries'] : "widget_recent_entries";

		// no 'class' attribute - add one with the recent_entries
		if( strpos($before_widget, 'class') === false ) {
			// include closing tag in replace string
			$before_widget = str_replace('>', 'class="'. $widget_recent_entries . '">', $before_widget);
		}
		// there is 'class' attribute - append width value to it
		else {
			$before_widget = str_replace('class="', 'class="'. $widget_recent_entries . ' ', $before_widget);
		}


		/* Before widget */
		echo $before_widget;

		// if title is present
		if (!empty($title)) echo $args['before_title'] . $title . $args['after_title'];

		$postID = '321805'; //get_the_ID()
		$model_type = '&model_type=article';
		$sort_by = '&sort_by=score';

		// This is where you run the code and display the output
		$dev_url = "https://dev-article-rec-api.localnewslab.io/recs";
		$query = "?source_entity_id=" . $postID . $model_type . $sort_by;

		$request_url = $dev_url . $query;

		$response = wp_remote_retrieve_body ( wp_remote_get( $request_url ) );

		if( is_wp_error( $response ) ) {
			lnl_get_recent_posts();
		}

		$data = json_decode($response, true, JSON_PRETTY_PRINT);
		echo $data->results;
		$results = $data["results"];

		/**
		 * Parse JSON response recommended articles and construct links to be rendered
		 * Send data in the data-vars to be pulled in through AMP analytics
		 *
		 * @param [type] $results
		 * @return void
		 */
		function lnl_get_recommendations($results) {
			$count = 1;
			$articleIds = [];
			foreach ($results as $result) {
				$score = $result["score"];

				// Storing model object from response
				$model =  $result["model"];

				$model_id =  $model["id"];
				$model_status = $model["status"];
				$model_type = $model["type"];

				// Store recommendation_article object from response
				$recommended_article = $result["recommended_article"];

				//$BASE_URL = 'https://www.washingtoncitypaper.com/article/';
				$rec_id = $recommended_article["id"];
				$article_title = esc_html($recommended_article["title"]);
				$article_path = $recommended_article["path"];
				$article_id = $recommended_article["external_id"];
				$post_url = $BASE_URL . $article_path;

				// Construct the link
				$str = "data-vars-model-id=$model_id ";
				$str .= "data-vars-model-type='$model_type' ";
				$str .= "data-vars-model-status='$model_status' ";
				$str .= "data-vars-article-id=$article_id ";
				$str .= "data-vars-rec-id=$rec_id ";
				$str .= "data-vars-position=$count ";
				$str .= "data-vars-article-title='$article_title' ";
				$str .= "data-vars-score=$score ";
				$str .= "href=$post_url>";
				$str .= $article_title;

				$recId = 'rec_' . $count;
				echo "<li><a id=$recId class='rec_article' " . $str . "</a></li>";

				$count++;
				$articleIds[] = $article_id;
			}
			return $articleIds;
		}

		/**
		 * Output recent posts
		 *
		 * @return void
		 */
		function lnl_get_recent_posts() {
			$args = array( 'numberposts' => '5' );
			$recent_posts = wp_get_recent_posts($args);
			foreach ($recent_posts as $recent) {
				printf(
					'<li><a href="%1$s">%2$s</a></li>',
					esc_url(get_permalink($recent['ID'])),
					apply_filters('the_title', $recent['post_title'], $recent['ID'])
				);
			}
		}

		$articleIds = [];
		foreach ($results as $result) {
			$articleIds[] = $result["recommended_article"]["external_id"];
		}

		$model =  $results[0]["model"];
		$model_id =  $model["id"];
		$model_status = $model["status"];
		$model_type = $model["type"];

		$str = "data-vars-model-id=$model_id ";
		$str .= "data-vars-model-type='$model_type' ";
		$str .= "data-vars-model-status='$model_status' ";

		echo "<ul data-vars-one=$articleIds[0] data-vars-two=$articleIds[1] data-vars-three=$articleIds[2] data-vars-four=$articleIds[3] data-vars-five=$articleIds[4] " . $str . ">";
		lnl_get_recommendations($results);
		echo "</ul>";

		echo $args['after_widget'];
	} // end public function widget

	/**
	 * Widget Backend - this controls what you see in the Widget UI
	 * For this example we are just allowing the widget title to be entered
	 * */
	public function form($instance) {
		if (isset($instance['title'])) $title = $instance['title'];
		else $title = __('New Title', 'article_widget_domain');

		// Widget admin form
	?>
<p>
  <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
  <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
    name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
</p>
<?php
	}

	// Updating widget replacing old instances with new
	public function update($new_instance, $old_instance) {

		$instance = array();

		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';

		return $instance;
	}
} // Class article_recommendations ends here


?>
