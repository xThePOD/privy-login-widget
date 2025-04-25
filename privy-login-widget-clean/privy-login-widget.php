<?php
/**
 * Plugin Name: Privy Login Widget
 * Plugin URI: https://github.com/yourusername/privy-login-widget
 * Description: Adds Privy authentication to your WordPress site
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: privy-login-widget
 */

// Prevent direct access to this file
if (!defined('ABSPATH')) {
    exit;
}

// Include the widget class
require_once plugin_dir_path(__FILE__) . 'includes/class-privy-login-widget.php';

// Register the widget
function register_privy_login_widget() {
    register_widget('Privy_Login_Widget');
}
add_action('widgets_init', 'register_privy_login_widget');

// Add shortcode support
function privy_login_shortcode($atts) {
    // Merge default attributes with user attributes
    $atts = shortcode_atts(array(
        'button_text' => 'Connect Wallet',
        'theme' => 'dark'
    ), $atts);

    // Generate a unique ID for this instance
    static $instance = 0;
    $instance++;
    $widget_id = 'privy-login-' . $instance;

    // Get the App ID
    $app_id = get_option('privy_app_id', '');
    if (empty($app_id)) {
        return '<p style="color: red;">' . __('Please configure your Privy App ID in the plugin settings.', 'privy-login-widget') . '</p>';
    }

    // Start output buffering
    ob_start();
    ?>
    <div id="<?php echo esc_attr($widget_id); ?>" class="privy-login-container"></div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            new PrivyLoginWidget({
                appId: '<?php echo esc_js($app_id); ?>',
                containerId: '<?php echo esc_js($widget_id); ?>',
                buttonText: '<?php echo esc_js($atts['button_text']); ?>',
                theme: '<?php echo esc_js($atts['theme']); ?>',
                loginMethods: ['email', 'wallet']
            });
        });
    </script>
    <?php
    return ob_get_clean();
}
add_shortcode('privy_login', 'privy_login_shortcode');

// Enqueue scripts
function privy_login_enqueue_scripts() {
    wp_enqueue_script(
        'privy-login-widget',
        plugins_url('js/privy-login-widget.js', __FILE__),
        array(),
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'privy_login_enqueue_scripts');

// Add settings page
function privy_login_add_settings_page() {
    add_options_page(
        __('Privy Login Settings', 'privy-login-widget'),
        __('Privy Login', 'privy-login-widget'),
        'manage_options',
        'privy-login-settings',
        'privy_login_settings_page'
    );
}
add_action('admin_menu', 'privy_login_add_settings_page');

// Create the settings page
function privy_login_settings_page() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_POST['privy_app_id'])) {
        check_admin_referer('privy_login_settings');
        update_option('privy_app_id', sanitize_text_field($_POST['privy_app_id']));
        echo '<div class="updated"><p>' . __('Settings saved.', 'privy-login-widget') . '</p></div>';
    }

    $app_id = get_option('privy_app_id', '');
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form method="post">
            <?php wp_nonce_field('privy_login_settings'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="privy_app_id"><?php _e('Privy App ID', 'privy-login-widget'); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="privy_app_id"
                               name="privy_app_id" 
                               value="<?php echo esc_attr($app_id); ?>" 
                               class="regular-text">
                        <p class="description">
                            <?php _e('Enter your Privy App ID from your Privy dashboard.', 'privy-login-widget'); ?>
                        </p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Add settings link to plugins page
function privy_login_settings_link($links) {
    $settings_link = '<a href="' . esc_url(admin_url('options-general.php?page=privy-login-settings')) . '">' . 
                    esc_html__('Settings', 'privy-login-widget') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'privy_login_settings_link'); 