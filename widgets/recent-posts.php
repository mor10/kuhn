<?php

/*
 * Enhanced Recent Posts widget.
 * This code adds a new widget that shows the featured image, post title, and publishing date.
 * Gently lifted and reworked from Anders Norén's Lovecraft theme: http://www.andersnoren.se/teman/lovecraft-wordpress-theme/
 */


class kuhn_recent_posts extends WP_Widget {

	function __construct() {
        $widget_ops = array( 'classname' => 'widget_kuhn_recent_posts', 'description' => __('Displays most recent posts with featured image and publishing date.', 'kuhn') );
        parent::__construct( 'widget_kuhn_recent_posts', __('Enhanced Recent Posts','kuhn'), $widget_ops );
    }

	function widget($args, $instance) {

		// Outputs the content of the widget
		extract($args); // Make before_widget, etc available.

		$widget_title = null;
		$number_of_posts = null;

		$widget_title = esc_attr(apply_filters('widget_title', $instance['widget_title']));
		$number_of_posts = esc_attr($instance['number_of_posts']);

		echo $before_widget;


		if (!empty($widget_title)) {
			echo $before_title . $widget_title . $after_title;
		} else {
			echo $before_title . esc_html__( 'Recent Posts', 'kuhn' ) . $after_title;
		}
		?>

			<ul class="kuhn-widget-list">

				<?php
					if ( $number_of_posts == 0 ) $number_of_posts = 5;

					$recent_posts = new WP_Query( apply_filters(
					'kuhn_recent_posts_args', array(
					        'posts_per_page'      => $number_of_posts,
					        'post_status'         => 'publish',
					        'ignore_sticky_posts' => true
					) ) );

					if ($recent_posts->have_posts()) :

						while ( $recent_posts->have_posts() ) : $recent_posts->the_post(); ?>

						<li>
							<?php global $post; ?>
							<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>">
								<div class="post-icon" aria-hidden="true">
									<?php
									if ( has_post_thumbnail() ) :
										the_post_thumbnail('thumbnail');
									else :
										$post_firstletter = substr(the_title_attribute( 'echo=0' ), 0, 1);
										echo $post_firstletter;
									endif;
									?>
								</div>
								<p class="title"><?php the_title(); ?></p>
								<p class="meta"><?php the_time(get_option('date_format')); ?></p>
							</a>
						</li>

					<?php endwhile; ?>

				</ul>

			<?php endif; ?>

		<?php echo $after_widget;
	}


	function update($new_instance, $old_instance) {
		$instance = $old_instance;

		$instance['widget_title'] = strip_tags( $new_instance['widget_title'] );
        // make sure we are getting a number
        $instance['number_of_posts'] = is_int( intval( $new_instance['number_of_posts'] ) ) ? intval( $new_instance['number_of_posts']): 5;

		//update and save the widget
		return $instance;

	}

	function form($instance) {

		// Set defaults
		if(!isset($instance["widget_title"])) { $instance["widget_title"] = ''; }
		if(!isset($instance["number_of_posts"])) { $instance["number_of_posts"] = '5'; }

		// Get the options into variables, escaping html characters on the way
		$widget_title = esc_attr($instance['widget_title']);
		$number_of_posts = esc_attr($instance['number_of_posts']);
		?>

		<p>
			<label for="<?php echo $this->get_field_id('widget_title'); ?>"><?php  _e('Title', 'kuhn'); ?>:
			<input id="<?php echo $this->get_field_id('widget_title'); ?>" name="<?php echo $this->get_field_name('widget_title'); ?>" type="text" class="widefat" value="<?php echo esc_attr($widget_title); ?>" /></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('number_of_posts'); ?>"><?php _e('Number of posts to display', 'kuhn'); ?>:
			<input id="<?php echo $this->get_field_id('number_of_posts'); ?>" name="<?php echo $this->get_field_name('number_of_posts'); ?>" type="text" class="widefat" value="<?php echo esc_attr($number_of_posts); ?>" /></label>
			<small>(<?php _e('Defaults to 5 if empty','kuhn'); ?>)</small>
		</p>

		<?php
	}
}
register_widget('kuhn_recent_posts');

/**
 * Remove the line above and uncomment the lines below to replace the default widget
 */

/*
function kuhn_posts_widget_registration() {
  unregister_widget('WP_Widget_Recent_Posts');
  register_widget('kuhn_recent_posts');
}
add_action('widgets_init', 'kuhn_posts_widget_registration');
*/