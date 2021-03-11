<?php

/**
 * Plugin Name: Article Recommendations Widget
 * Plugin URI: https://github.com/LocalAtBrown/article-recommendations
 * Description: AMP-compatible article recommendations widget
 * Author: LocalAtBrown
 * Version: 1.0.0
 *
 * @package ArticleRecommendationsWidget
 */

function article_register_widget()
{

	register_widget('article_widget');
}

add_action('widgets_init', 'article_register_widget');

class article_widget extends WP_Widget
{

	function __construct()
	{
		parent::__construct(

			// Base ID of your widget
			'article_widget',

			// Widget name will appear in UI
			__('Article Recommendations', ' article_widget_domain'),

			// Widget description
			array('description' => __('Add a list of reader personalized article recommendations', 'article_widget_domain'),)

		);
	}

	// Creating widget front-end view
	public function widget($args, $instance)
	{

		$title = apply_filters('widget_title', $instance['title']);

		// before and after widget arguments are defined by themes
		echo $args['before_widget'];

		// if title is present
		if (!empty($title))

			echo $args['before_title'] . $title . $args['after_title'];

		// TODO: This could belong to an Class API

		$postID = '321805'; //get_the_ID()
		$author = get_the_author();

		// live model type
		$model_type = '&model_type=article';
		// dev model id
		$model_id = '&model_id=8';
		$sort_by = '&sort_by=score';

		// This is where you run the code and display the output
		$dev_url = "https://dev-article-rec-api.localnewslab.io/recs";
		$query = "?source_entity_id=" . $postID . $model_id . $sort_by;
		$request_url = $dev_url . $query;
		// echo 'The author is: ' . $author . $postID;

		// Initialize curl session
		$curl = curl_init();

		// Will return the response, if false it print the response

		// Use this option is making a request to https
		// curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false)

		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, [
			'Content-Type: application/json'
		]);

		// Set the request_url
		curl_setopt($curl, CURLOPT_URL, $request_url);

		// Execute and save JSON response
		$response = curl_exec($curl);

		$err = curl_error($curl);

		// Closing
		curl_close($curl);

		if ($err) {

			//Only show errors while testing
			echo "cURL Error #:" . $err;
		} else {


			//The API returns data in JSON format, so first convert that to an associative array of data objects
			$articleRecommendations = json_decode($response, true);

			// Pull first article directly
			// $recommended_article = $articleRecommendations["results"]["0"]["recommended_article"];

			// $title = $recommended_article["title"];
			// $title = str_replace(" - Washington City Paper", "", $title);
			// $external_id = $recommended_article["external_id"];
			// $post_url = 'https://www.washingtoncitypaper.com/article/' . $external_id;
			// echo "<a class='widget widget_recent_entries' data-var-position=1 data-var-article-id=$external_id href=$post_url>" . $title . "</a> <br><br>";

			// Recommendation schema needs to send the position and score of the article
			// Send them in the data-var to be pulled in through AMP analytics
			/**
			 * Parse JSON response for 'title'. Recursive function to extract nested JSON values
			 *
			 * @param [type] $json_rec
			 * @return void
			 */
			function recursiveParse($json_rec, $count = 0)
			{
				if ($json_rec) {
					foreach ($json_rec as $key => $value) {
						if (is_array($value)) {
							// echo 'array: ' . $key . '<br>';

							// check if key is the iterator
							if (is_int($key)) {
								// start position from 1 rather than 0
								$count = $key + 1;
							}
							recursiveParse($value, $count);
						} else {
							// echo '<br>key: ' . $json_rec . '<br>';
							// echo  '<br>key: ' . $key . '<br>value: ' . $value;
							if ($key == 'title') {
								// TODO: use a regex and loop instead?
								$BASE_URL = 'https://www.washingtoncitypaper.com/article/';

								// Remove " - Washington City Paper" suffix from title string
								$title = str_replace(" - Washington City Paper", "", $value);

								$article_id = $value["external_id"];
								$post_url = $BASE_URL . $article_id;

								echo "<a class='widget widget_recent_entries' data-var-article-id=$article_id data-var-position=$count href=$post_url> " . $title . "</a> <br><br>";
							}
						}
					}
				}
			}

			recursiveParse($articleRecommendations);
		}

		echo $args['after_widget'];
	} // end public function widget





	/**
	 * Widget Backend - this controls what you see in the Widget UI
	 * For this example we are just allowing the widget title to be entered
	 * */
	public function form($instance)
	{
		if (isset($instance['title'])) $title = $instance['title'];
		else $title = __('New Title', 'article_widget_domain');

		// Widget admin form
?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>
<?php
	}

	// Updating widget replacing old instances with new
	public function update($new_instance, $old_instance)
	{

		$instance = array();

		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';

		return $instance;
	}
} // Class article_recommendations ends here
?>
