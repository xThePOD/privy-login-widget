<?php
// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

class Privy_Login_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'privy_login_widget', // Base ID
            'Privy Login', // Widget name in admin
            array('description' => __('Displays a Privy Login button', 'privy-login-widget'))
        );
    }

    public function widget($args, $instance) {
        $title = !empty($instance['title']) ? apply_filters('widget_title', $instance['title']) : '';
        $button_text = !empty($instance['button_text']) ? $instance['button_text'] : 'Connect Wallet';
        $theme = !empty($instance['theme']) ? $instance['theme'] : 'dark';

        echo $args['before_widget'];
        if ($title) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }
        
        echo '<div id="privy-login" class="privy-login-container"></div>';
        
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $button_text = !empty($instance['button_text']) ? $instance['button_text'] : 'Connect Wallet';
        $theme = !empty($instance['theme']) ? $instance['theme'] : 'dark';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'privy-login-widget'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('button_text')); ?>"><?php _e('Button Text:', 'privy-login-widget'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('button_text')); ?>" name="<?php echo esc_attr($this->get_field_name('button_text')); ?>" type="text" value="<?php echo esc_attr($button_text); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('theme')); ?>"><?php _e('Theme:', 'privy-login-widget'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('theme')); ?>" name="<?php echo esc_attr($this->get_field_name('theme')); ?>">
                <option value="dark" <?php selected($theme, 'dark'); ?>><?php _e('Dark', 'privy-login-widget'); ?></option>
                <option value="light" <?php selected($theme, 'light'); ?>><?php _e('Light', 'privy-login-widget'); ?></option>
            </select>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['button_text'] = (!empty($new_instance['button_text'])) ? strip_tags($new_instance['button_text']) : 'Connect Wallet';
        $instance['theme'] = (!empty($new_instance['theme'])) ? strip_tags($new_instance['theme']) : 'dark';
        return $instance;
    }
} 