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

// Define plugin constants
define('PRIVY_LOGIN_WIDGET_VERSION', '1.0.0');
define('PRIVY_LOGIN_WIDGET_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PRIVY_LOGIN_WIDGET_PLUGIN_URL', plugin_dir_url(__FILE__));

// Enqueue scripts and styles
function privy_login_enqueue_scripts() {
    // Enqueue the Privy widget script
    wp_enqueue_script(
        'privy-login-widget',
        PRIVY_LOGIN_WIDGET_PLUGIN_URL . 'js/privy-login-widget.js',
        array(),
        PRIVY_LOGIN_WIDGET_VERSION,
        true
    );

    // Get the App ID from WordPress options
    $app_id = get_option('privy_app_id', '');
    if (empty($app_id)) {
        error_log('Privy Login Widget: App ID is not configured');
        return;
    }
    
    // Initialize the widget with WordPress-specific configuration
    wp_add_inline_script('privy-login-widget', '
        document.addEventListener("DOMContentLoaded", function() {
            if (document.getElementById("privy-login")) {
                new PrivyLoginWidget({
                    appId: "' . esc_js($app_id) . '",
                    containerId: "privy-login",
                    buttonText: "Connect Wallet",
                    theme: "dark",
                    loginMethods: ["email", "wallet"]
                });
            }
        });
    ');
}
add_action('wp_enqueue_scripts', 'privy_login_enqueue_scripts');

// Add shortcode support
function privy_login_shortcode($atts) {
    $atts = shortcode_atts(array(
        'button_text' => 'Connect Wallet',
        'theme' => 'dark',
    ), $atts);

    return sprintf(
        '<div id="privy-login" data-button-text="%s" data-theme="%s"></div>',
        esc_attr($atts['button_text']),
        esc_attr($atts['theme'])
    );
}
add_shortcode('privy_login', 'privy_login_shortcode');

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

// Register plugin settings
function privy_login_register_settings() {
    register_setting('privy_login_settings', 'privy_app_id', array(
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'default' => ''
    ));
}
add_action('admin_init', 'privy_login_register_settings');

// Create the settings page
function privy_login_settings_page() {
    // Check user capabilities
    if (!current_user_can('manage_options')) {
        return;
    }

    // Save settings if posted
    if (isset($_POST['privy_app_id'])) {
        check_admin_referer('privy_login_settings');
        update_option('privy_app_id', sanitize_text_field($_POST['privy_app_id']));
        echo '<div class="updated"><p>' . esc_html__('Settings saved.', 'privy-login-widget') . '</p></div>';
    }

    $app_id = get_option('privy_app_id', '');
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form method="post" action="">
            <?php wp_nonce_field('privy_login_settings'); ?>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row">
                        <label for="privy_app_id"><?php esc_html_e('Privy App ID', 'privy-login-widget'); ?></label>
                    </th>
                    <td>
                        <input type="text" 
                               id="privy_app_id"
                               name="privy_app_id" 
                               value="<?php echo esc_attr($app_id); ?>" 
                               class="regular-text"
                               required>
                        <p class="description">
                            <?php esc_html_e('Enter your Privy App ID from your Privy dashboard.', 'privy-login-widget'); ?>
                            <a href="https://console.privy.io" target="_blank" rel="noopener noreferrer">
                                <?php esc_html_e('Get your App ID', 'privy-login-widget'); ?>
                            </a>
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