<?php
        
function boc_load_widgets() {

    register_widget('boc_latest');
    register_widget('contact_info_widget');

}   



/**
 * Latest Posts Widget
 */
class boc_latest extends WP_Widget {

        function boc_latest() {
            $widget_ops = array('description' => 'Fortuna Latest Posts');
            $this->__construct('boc_latest', 'Fortuna Latest Posts', $widget_ops);
        }

        function widget($args, $instance) {
            extract($args, EXTR_SKIP);
            echo $before_widget;
            $title = empty($instance['title']) ? '&nbsp;' : '<span>'.apply_filters('widget_title', wp_kses_post($instance['title'])).'</span>';
            $count = wp_kses_post($instance['count']);

            echo boc_removeSpanFromTitle($before_title) . $title . boc_removeSpanFromTitle($after_title);
            wp_reset_query();

            $recent_posts = new WP_Query(
                array(
                    'posts_per_page' => $count,
                    'post_status' => 'publish',
                    'nopaging' => 0,
                    'post__not_in' => get_option('sticky_posts')
                    )
                );

            // Cycle through Posts    
            if ($recent_posts->have_posts()) :while ($recent_posts->have_posts()) : $recent_posts->the_post();
            ?>

            <div class="boc_latest_posts section">
                <a href="<?php the_permalink() ?>"><?php the_post_thumbnail('boc_thumb', array( 'title' => get_the_title() )); ?></a>
                <p class="boc_latest_posts_title"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></p>
                <p class="date"><?php echo get_the_date();?></p>
            </div>
                <?php
                endwhile;
                endif;
                wp_reset_query();

                echo $after_widget;
            }

            function update($new_instance, $old_instance) {
                $instance = $old_instance;
                $instance['title'] = strip_tags($new_instance['title']);

                $instance['count'] = $new_instance['count'];

                return $instance;
            }

            function form($instance) {
                $instance = wp_parse_args((array) $instance, array('title' => ''));
                $title = strip_tags($instance['title']);

                $count = $instance['count'];
                ?>


                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e( 'Widget Title', 'Fortuna' ) ?>:
                        <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
                    </label>
                </p>

                <p>
                    <label for="<?php echo esc_attr($this->get_field_id('count')); ?>"><?php _e( 'How many posts? (Number)', 'Fortuna' ) ?>:
                        <input class="widefat" id="<?php echo esc_attr($this->get_field_id('count')); ?>" name="<?php echo esc_attr($this->get_field_name('count')); ?>" type="text" value="<?php echo esc_attr($count); ?>" />
                    </label>
                </p>

                <?php
            }

}


/**
 * Contact Info Widget
 */
class contact_info_widget extends WP_Widget {
	
	function contact_info_widget()
	{
		$widget_ops = array('classname' => 'contact_info', 'description' => '');
		$this->__construct('contact_info-widget', 'Fortuna: Contact Info', $widget_ops);
	}
	
	function widget($args, $instance)
	{
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);

		echo $before_widget;

		if($title) {
			echo $before_title.$title.$after_title;
		}
		?>
		<?php if($instance['phone']): ?>
		<div class="boc_contact_icon"><span class="icon icon-mobile3"></span> <?php echo wp_kses_post($instance['phone']); ?></div>
		<?php endif; ?>

		<?php if($instance['email']): ?>
		<div class="boc_contact_icon"><span class="icon icon-mail2"></span> <a href="mailto:<?php echo antispambot(esc_html($instance['email'])); ?>"><?php echo antispambot(esc_html($instance['email'])); ?></a></div>
		<?php endif; ?>

		<?php if($instance['address']): ?>
		<div class="boc_contact_icon"><span class="icon icon-location2 bigger"></span> <?php echo wp_kses_post($instance['address']); ?></div>
		<?php endif; ?>
		
		<div class="clear h10"></div>
		
		<?php
		echo $after_widget;
	}
	
	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;

		$instance['title'] = $new_instance['title'];
		$instance['address'] = $new_instance['address'];
		$instance['phone'] = $new_instance['phone'];
		$instance['fax'] = $new_instance['fax'];
		$instance['email'] = $new_instance['email'];
		$instance['web'] = $new_instance['web'];

		return $instance;
	}

	function form($instance)
	{
		$defaults = array('title' => 'Contact Info');
		$instance = wp_parse_args((array) $instance, $defaults); ?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title', 'Fortuna' ) ?>:</label>
			<input class="widefat" style="width: 216px;" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" value="<?php echo wp_kses_post($instance['title']); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('phone'); ?>"><?php _e( 'Phone', 'Fortuna' ) ?>:</label>
			<input class="widefat" style="width: 216px;" id="<?php echo esc_attr($this->get_field_id('phone')); ?>" name="<?php echo esc_attr($this->get_field_name('phone')); ?>" value="<?php echo wp_kses_post($instance['phone']); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('email'); ?>"><?php _e( 'Email', 'Fortuna' ) ?>:</label>
			<input class="widefat" style="width: 216px;" id="<?php echo esc_attr($this->get_field_id('email')); ?>" name="<?php echo esc_attr($this->get_field_name('email')); ?>" value="<?php echo wp_kses_post($instance['email']); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('address'); ?>"><?php _e( 'Address', 'Fortuna' ) ?>:</label>
			<input class="widefat" style="width: 216px;" id="<?php echo esc_attr($this->get_field_id('address')); ?>" name="<?php echo esc_attr($this->get_field_name('address')); ?>" value="<?php echo wp_kses_post($instance['address']); ?>" />
		</p>
	<?php
	}
} 