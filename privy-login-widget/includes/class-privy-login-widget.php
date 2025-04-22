<?php
/**
 * Privy Login Widget Class
 */
class Privy_Login_Widget extends WP_Widget {
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct(
            'privy_login_widget',
            __('Privy Login', 'privy-login-widget'),
            array(
                'description' => __('Add Privy authentication to your site', 'privy-login-widget'),
            )
        );
    }

    /**
     * Front-end display of widget
     */
    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        if (!empty($instance['title'])) {
            echo $args['before_title'] . apply_filters('widget_title', $instance['title']) . $args['after_title'];
        }

        // Widget content
        ?>
        <div id="privy-login-container" class="privy-login-widget-container">
            <div id="privy-login-button" class="privy-login-widget-button">
                <?php _e('Login with Privy', 'privy-login-widget'); ?>
            </div>
        </div>
        <?php

        echo $args['after_widget'];
    }

    /**
     * Back-end widget form
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'privy-login-widget'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <?php
    }

    /**
     * Sanitize widget form values as they are saved
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        return $instance;
    }
} 