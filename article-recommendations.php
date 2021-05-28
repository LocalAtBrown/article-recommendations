<?php

/**
 * Article Recommendations
 *
 * @package ArticleRecommendations
 * @author Erick Martinez Jr <em3591@columbia.edu>
 *
 * @wordpress-plugin
 * Plugin Name: Article Recommendations
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

// ----------------------------------------------------------------

add_action('wp_head', 'buffer_start');

/**
 * Manipulate the page after it has been constructed, right before it is sent to visitor's browser.
 * https://www.php.net/manual/en/function.ob-start.php
 *
 * Filters the entire page HTML and using regex and preg_replace(), copy each link href
 * into a data-vars-click-url attribute.
 * @return void
 */
function buffer_start()
{
    if (!wp_doing_ajax()) {
	// Hold constructed HTML to give time to manipulate it.
        ob_start('callback');
    }
}

add_action('wp_footer', 'buffer_end');


function buffer_end()
{
    if (!wp_doing_ajax()) {
        ob_end_flush();
    }
}

/**
 * Adds data-vars-click-url attributes to anchor tags using the href value
 * ex. <a href="www.example.com"> to
 * <a href="www.example.com" data-vars-click-url="www.example.com">
 *
 * @param array|string $buffer
 * @return array|string
 */
function callback($buffer)
{
    $pattern     = '/<a([^>]*?)href=["|\'](.*?)["|\']/i';
    $replacement = '<a${1}href="${2}" data-vars-click-url="${2}"';

    $buffer = preg_replace($pattern, $replacement, $buffer);

    return $buffer;
}

// ----------------------------------------------------------------

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
		$widget_recent_entries = ! empty( $instance['widget_recent_entries'] ) ? $instance['widget_recent_entries'] : "widget_recent_entries";

		// if no 'class' attribute - add one with the recent_entries for child theme css
		if( strpos( $before_widget, 'class' ) === false ) {
			// include closing tag in replace string
			$before_widget = str_replace( '>', 'class="'. $widget_recent_entries . '">', $before_widget );
		}
		// there is 'class' attribute - append width value to it
		else {
			$before_widget = str_replace( 'class="', 'class="' .$widget_recent_entries. ' ', $before_widget );
		}

		/* Before widget */
		echo $before_widget;

		// if title is present
		if (!empty($title)) echo $args['before_title'] . $title . $args['after_title'];

		$postID = get_the_ID();
		$model_type = "&model_type=article";
		$sort_by = "&sort_by=score";
		$exclude_param = "&exclude=$postID";

		// TODO: Fix these article recommendations
		#$exclude_param .= ",321838,321866,244547,242808,229925,261530,244483";

		// This is where you run the code and display the output
		$URL = "https://article-rec-api.localnewslab.io/recs";
		$query = "?source_entity_id={$postID}{$model_type}{$sort_by}{$exclude_param}";
		$request_url = $URL . $query;

		$response = wp_remote_retrieve_body ( wp_remote_get( $request_url ) );
		$data = json_decode($response, true, JSON_PRETTY_PRINT);
		$results = $data["results"];

		if ( is_array( $results ) && ! is_wp_error( $results ) ) {
    		// Work with the $result data
    		echo $this->get_recommendations($results);
			$this->get_recent_posts();
			echo "</ul>";
		} else {
			// Work with the error
			$this->get_recent_posts();
			echo "</ul>";
			echo $args['after_widget'];
			return;
		}

		echo $args['after_widget'];
	} // end public function widget

	/**
	 * Parse JSON response recommended articles and construct links to be rendered
	 * Send data in the data-vars to be pulled in through AMP analytics
	 *
	 * @param [array] $results - array of article recommendations from rec-api
	 * @return string
	 */
	function get_recommendations($results) {
		if( ! is_array( $results ) ) {
			return;
		}

		$count = 1;
		$articles = "";
		$list_items = "";
		foreach ( $results as $result ) {
			// Limit 5 posts
			if ( $count > 5 ) {
				return;
			}

			$score = $result["score"];

			// If score is NaN then continue
			if( is_nan( $score ) ) {
				continue;
			}

			// Get model and recommended_article object from response
			$model =  $result["model"];
			$rec = $result["recommended_article"];

			// Create the attributes for model and article recommendation
			$model_attributes = "data-vars-model-id={$model['id']} data-vars-model-status={$model['status']} data-vars-model-type={$model['type']}";
			$article_attributes = "data-vars-article-id={$rec['external_id']} data-vars-rec-id={$rec['id']} data-vars-position={$count} data-vars-score={$score}";

			$articles .= "data-vars-{$count}={$rec['external_id']}";

			// Create the href
			$class = "class='rec_article'"; // List item class
			$href = "href=https://www.washingtoncitypaper.com/{$rec['path']} >";
			$list_items .= "<li><a id='rec_{$count}' {$class} {$article_attributes} {$href} </a></li>";

			$count++; // increase count
		}

		return "<ul id='recommendations' {$model_attributes} {$articles}>{$list_items}</ul>";
	} // end of function

	/**
	 * Output only 5 published posts with data attributes for AMP config variable substitution
	 *
	 * @return void
	 */
	function get_recent_posts() {
		$recent_posts = wp_get_recent_posts( array( 'numberposts' => '5', 'post_status' => 'publish' ) );

		// Set the post IDs attribute
		$str = "data-vars-one=$recent_posts[0]['ID'] ";
		$str .= "data-vars-two=$recent_posts[1]['ID'] ";
		$str .= "data-vars-three=$recent_posts[2]['ID'] ";
		$str .= "data-vars-four=$recent_posts[3]['ID'] ";
		$str .= "data-vars-five=$recent_posts[4]['ID'] ";
		// Set the model type attribute
		$model_type = 'recent_posts'; // model context
		$str .= "data-vars-model-type=$model_type ";

		echo "<ul id='recentposts' " . $str . ">"; // Create the list

		$count = 1;
		foreach ( $recent_posts as $recent_post ) {

			$recent_post_id = 'recent_' . $count; // Set the IDs for links

			// Output the link
			printf(
				'<li><a id="%3$s" data-vars-position=%6$s data-vars-article-id=%5$s data-vars-model-type=%4$s href="%1$s">%2$s</a></li>',
				esc_url(get_permalink($recent_post['ID'])),
				apply_filters('the_title', $recent_post['post_title'], $recent_post['ID']),
				$recent_post_id,
				$model_type,
				$recent_post['ID'],
				$count
			);

			$count++;
		}
	}


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