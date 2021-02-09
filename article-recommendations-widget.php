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

			// Widget Base ID
			'article_widget',

			// Widget name will appear in UI
			__('Article Recommendations', ' article_widget_domain'),

			// Widget description
			array('description' => __('Add a list of reader personalized article recommendations', 'article_widget_domain'),)

		);
	}

	// Creating widget front-end
	public function widget($args, $instance)
	{

		$title = apply_filters('widget_title', $instance['title']);

		// before and after widget arguments are defined by themes
		echo $args['before_widget'];

		// if title is present
		if (!empty($title))

			echo $args['before_title'] . $title . $args['after_title'];

		// This is where you run the code and display the output
		echo __('This is a dummy article recommendation', 'article_widget_domain');

		echo $args['after_widget'];
	}

	public function form($instance)
	{

		if (isset($instance['title']))

			$title = $instance['title'];

		else

			$title = __('Default Title', 'article_widget_domain');

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
	// Class article_recommendations ends here
}
