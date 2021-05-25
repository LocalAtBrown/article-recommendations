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

		$postID = get_the_ID();
		$model_type = '&model_type=article';
		$sort_by = '&sort_by=score';

		// This is where you run the code and display the output
		$URL = "https://article-rec-api.localnewslab.io/recs";
		$query = "?source_entity_id=" . $postID . $model_type . $sort_by;
		$request_url = $URL . $query;

		$response = wp_remote_retrieve_body ( wp_remote_get( $request_url ) );

		if( is_wp_error( $response ) ) {
			lnl_get_recent_posts();
			echo "</ul>";
		}

		$data = json_decode($response, true, JSON_PRETTY_PRINT);
		echo $data->results;
		$results = $data["results"];

		/**
		 * Parse JSON response recommended articles and construct links to be rendered
		 * Send data in the data-vars to be pulled in through AMP analytics
		 * {
		 *	"results": [
		 *		{
		 *			"id": 9516554,
		 *			"created_at": "2021-05-07T14:44:54.760391+00:00",
		 *			"updated_at": "2021-05-07T14:44:54.760393+00:00",
		 *			"source_entity_id": "default",
		 *			"model": {
		 *				"id": 625,
		 *				"created_at": "2021-05-07T14:44:54.754762+00:00",
		 *				"updated_at": "2021-05-07T14:44:54.852580+00:00",
		 *				"type": "popularity",
		 *				"status": "current"
		 *			},
		 *			"recommended_article": {
		 *				"id": 4550,
		 *				"created_at": "2021-04-15T00:20:21.728914+00:00",
		 *				"updated_at": "2021-04-15T00:20:21.728927+00:00",
		 *				"external_id": 286326,
		 *				"title": "Lost Highway",
		 *				"path": "/article/286326/lost-highway/",
		 *				"published_at": "1996-09-27T04:00:00+00:00"
		 *			},
		 *			"score": "0.888446"
		 *		}
		 *	]
		 * }
		 * @param [type] $results
		 * @return void
		 */
		function lnl_get_recommendations($results) {
			$id = $results[0]['model']['id'];
			$type = $results[0]['model']['type'];
			$status = $results[0]['model']['status'];
			$one = $results[0]['recommended_article']['external_id'];
			$two=$results[1]['recommended_article']['external_id'];
			$three=$results[2]['recommended_article']['external_id'];
			$four=$results[3]['recommended_article']['external_id'];
			$five=$results[4]['recommended_article']['external_id'];
			$str = "data-vars-model-id=$id ";
			$str .= "data-vars-model-status=$status ";
			$str .= "data-vars-model-type=$type ";
			$str .= "data-vars-one=$one ";
			$str .= "data-vars-two=$two ";
			$str .= "data-vars-three=$three ";
			$str .= "data-vars-four=$four ";
			$str .= "data-vars-five=$five ";

			echo "<ul id='recommendations' " . $str . ">";

			$count = 1;
			foreach ($results as $result) {
				if ($count > 5) {
					return;
				}
				$score = $result["score"];

				// Storing model object from response
				$model =  $result["model"];
				$model_id =  $model["id"];
				$model_status = $model["status"];
				$model_type = $model["type"];

				// Store recommendation_article object from response
				$rec = $result["recommended_article"];

				$BASE_URL = 'https://www.washingtoncitypaper.com';
				$rec_id = $rec["id"];
				$article_title = esc_html($rec["title"]);
				$article_path = $rec["path"];
				$article_id = $rec["external_id"];
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
			}
		}

		/**
		 * Output recent posts
		 *
		 * @return void
		 */
		function lnl_get_recent_posts() {

			$args = array( 'numberposts' => '5' );
			$recent_posts = wp_get_recent_posts($args);
			$model_type = 'recent_posts'; // model context

			$one = $recent_posts[0]['ID'];
			$two=$recent_posts[1]['ID'];
			$three=$recent_posts[2]['ID'];
			$four=$recent_posts[3]['ID'];
			$five=$recent_posts[4]['ID'];
			$str = "data-vars-one=$one ";
			$str .= "data-vars-two=$two ";
			$str .= "data-vars-three=$three ";
			$str .= "data-vars-four=$four ";
			$str .= "data-vars-five=$five ";
			$str .= "data-vars-model-type=$model_type ";

			echo "<ul id='recentposts' " . $str . ">";

			$count = 1;
			foreach ($recent_posts as $recent) {
				$recent_post_id = 'recent_' . $count;

				printf(
					'<li><a id="%3$s" data-vars-position=%6$s data-vars-article-id=%5$s data-vars-model-type=%4$s href="%1$s">%2$s</a></li>',
					esc_url(get_permalink($recent['ID'])),
					apply_filters('the_title', $recent['post_title'], $recent['ID']),
					$recent_post_id,
					$model_type,
					$recent['ID'],
					$count
				);

				$count++;
			}
		}

		lnl_get_recommendations($results);
		echo "</ul>";
		lnl_get_recent_posts();
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
