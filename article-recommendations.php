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
if ( ! defined('WPINC') ) {
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
function buffer_start() {
    if ( ! wp_doing_ajax() ) {
	// Hold constructed HTML to give time to manipulate it.
        ob_start('callback');
    }
}

add_action('wp_footer', 'buffer_end');


function buffer_end() {
    if ( ! wp_doing_ajax() ) {
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
function callback($buffer) {
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
		else {
			// there is 'class' attribute - append width value to it
			$before_widget = str_replace( 'class="', 'class="' .$widget_recent_entries. ' ', $before_widget );
		}

		/* Before widget */
		echo $before_widget;

		// if title is present
		if ( ! empty($title) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$post_id = get_the_ID();
		$model_type = 'article';
		$sort_by = 'score';
		$exclude = "&exclude={$post_id}";

		// TODO: Fix these article recommendations
		#$exclude .= ",321838,321866,244547,242808,229925,261530,244483";

		// This is where you run the code and display the output
		$URL = "https://article-rec-api.localnewslab.io/recs";
		$query = "?source_entity_id='{$post_id}'&model_type={$model_type}&sort_by={$sort_by}{$exclude}";
		$request_url = $URL . $query;

		$response = wp_remote_retrieve_body ( wp_remote_get( $request_url ) );
		$data = json_decode($response, true, JSON_PRETTY_PRINT);
		$results = $data["results"];

		if ( is_array( $results ) && ! is_wp_error( $results ) ) {
			echo $this->get_recommendations( $results );
			echo $this->get_recent_posts();
		} else {
			echo $this->get_recent_posts( 'recommendations' );
			echo $this->get_recent_posts();
		}

		echo $args['after_widget'];
	} // end public function widget

	/**
	 * Parse JSON response recommended articles and construct links to be rendered
	 * Send data in the data-vars to be pulled in through AMP analytics
	 * {
	 * "results": [
     *   {
     *       "id": 36793363,
     *       "created_at": "2021-05-26T20:32:43.997514+00:00",
     *       "updated_at": "2021-05-26T20:32:43.997518+00:00",
     *       "source_entity_id": "321805",
     *       "model": {
     *           "id": 974,
     *           "created_at": "2021-05-26T19:51:27.569465+00:00",
     *           "updated_at": "2021-05-26T20:33:24.491237+00:00",
     *           "type": "article",
     *           "status": "current"
     *       },
     *       "recommended_article": {
     *           "id": 5244,
     *           "created_at": "2021-04-16T21:58:15.521466+00:00",
     *           "updated_at": "2021-04-16T21:58:15.521479+00:00",
     *           "external_id": 244547,
     *           "title": "Savage Love",
     *           "path": "/article/229925/savage-love/",
     *           "published_at": "2009-04-10T04:00:00+00:00"
     *       },
     *       "score": "NaN"
     *   },
	 *	..
	 *	] }
	 * @param [array] $results - array of article recommendations from rec-api
	 * @return string
	 */
	function get_recommendations( $results, $id = 'recommendations' ) {
		// If api request failed, change id to recentposts to still display posts
		if( ! is_array( $results ) ) {
			return $this->get_recent_posts( $id );
		}

		$count = 1;
		$articles = "";
		$list_items = "";
		$model_attributes = "";
		$count_array = ['one','two','three','four','five'];
		foreach ( $results as $result ) {
			// Limit 5 posts
			if ( $count > 5 ) {
				break;
			}

			// Get score from result (Rec API)
			$score = $result["score"];

			// If score is NaN then continue
			if ( is_nan( $score ) || $score === 0 ) {
				continue;
			}

			// Get model and recommended_article object from response
			$model =  $result["model"];
			$post = $result["recommended_article"];

			// If Rec API result, get the 'external_id' else set to WP 'ID'
			$post_id = $post['external_id'] ?? $post['ID'];

			// If Rec API result, get the 'title' else set to WP 'post_title'
			$title = $post['title'] ?? $post['post_title'];

			// Display the post_title
			$post_title = apply_filters('the_title', $title, $post_id);

			// If permalink returns false, no post found. Then use wcp link.
			if( ! get_permalink( $post_id ) ) {
				$url = esc_url (  "https://www.washingtoncitypaper.com{$post['path']}" );
				$href = "href={$url}";
			} else {
				$href = esc_url( get_permalink( $post_id ) );
			}

			// Create the attributes for model and article recommendation
			$model_attributes = "data-vars-model-id={$model['id']} data-vars-model-status={$model['status']} data-vars-model-type={$model['type']} ";
			$article_attributes = "data-vars-article-id={$post['external_id']} data-vars-rec-id={$post['id']} data-vars-position={$count} data-vars-score={$score} ";
			$articles .= "data-vars-{$count_array[ $count - 1 ]}={$post['external_id']} ";

			// Add list item to string builder
			$list_items .= "<li><a id='{$id}_{$count}' {$model_attributes} {$article_attributes} {$href} </a>{$post_title}</li>";

			$count++; // increase count
		}

		return "<ul id={$id} {$model_attributes} {$articles}>{$list_items}</ul>";
	} // end of function get_recommendations

	/**
	 * Output only 5 published posts with data attributes for AMP config variable substitution
	 *
	 * @return string
	 */
	function get_recent_posts( $id = 'recentposts' ) {

		// Get 5 published recent posts
		$args = array(
			'numberposts' => '5',
			'post_status' => 'publish',
		);

		$posts = wp_get_recent_posts( $args );

		$count = 1;
		$articles = "";
		$list_items = "";
		$count_array = ['one','two','three','four','five'];
		foreach ( $posts as $post ) {
			// Limit 5 posts
			if ( $count > 5 ) {
				break;
			}

			// I can probably made a whole default model object and use nullish coal to default the values to null.
			// Get the score
			$score = $post["score"] ?? 0;

			// If score is NaN then continue
			if ( is_nan( $score ) && $score !== 'null') {
				continue;
			}

			$post = $post["recommended_article"] ?? $post;
			$post_id = $post['external_id'] ?? $post['ID'];
			$rec_id = $post['id'] ?? 0;

			$href = esc_url( get_permalink( $post_id ) );

			$title = $post['title'] ?? $post['post_title']; // If Rec API result, get the 'title' else set to WP 'post_title'
			$post_title = apply_filters('the_title', $title, $post_id); // Display the post_title

			// Set the model type attribute
			$model_attributes = "data-vars-model-id='null' data-vars-model-status='null' data-vars-model-type='recent_posts' ";
			$article_attributes = "data-vars-article-id={$post_id} data-vars-rec-id={$rec_id} data-vars-position={$count} data-vars-score={$score} ";

			// Set the post IDs attribute
			$articles .= "data-vars-{$count_array[ $count - 1 ]}={$post_id} ";

			// Create the list items for each post
			$list_items .= "<li><a id='{$id}_{$count}' {$article_attributes} {$model_attributes} href={$href} >{$post_title}</a></li>";

			$count++;
		}

		return "<ul id={$id} {$model_attributes} {$articles} >{$list_items}</ul>";
	} // end of function get_recent_posts

	/**
	 * Widget Backend - this controls what you see in the Widget UI
	 * For this example we are just allowing the widget title to be entered
	 * */
	public function form($instance) {
		if ( isset( $instance['title'] ) ) {
			$title = $instance['title'];
		}
		else {
			$title = __('New Title', 'article_widget_domain');
		}

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
	public function update( $new_instance, $old_instance ) {

		$instance = array();

		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}
} // Class article_recommendations ends here


?>
