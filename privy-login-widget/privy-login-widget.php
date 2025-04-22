<?php
/**
 * Plugin Name: Privy Login Widget
 * Plugin URI: https://github.com/xThePOD/privy-login-widget
 * Description: A WordPress widget that integrates Privy authentication into your site
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://github.com/xThePOD
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: privy-login-widget
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Define plugin constants
define('PRIVY_LOGIN_WIDGET_VERSION', '1.0.0');
define('PRIVY_LOGIN_WIDGET_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PRIVY_LOGIN_WIDGET_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once PRIVY_LOGIN_WIDGET_PLUGIN_DIR . 'includes/class-privy-login-widget.php';

// Initialize the widget
function privy_login_widget_init() {
    register_widget('Privy_Login_Widget');
}
add_action('widgets_init', 'privy_login_widget_init');

// Enqueue scripts and styles
function privy_login_widget_enqueue_scripts() {
    wp_enqueue_style(
        'privy-login-widget-style',
        PRIVY_LOGIN_WIDGET_PLUGIN_URL . 'public/css/privy-login-widget.css',
        array(),
        PRIVY_LOGIN_WIDGET_VERSION
    );

    wp_enqueue_script(
        'privy-login-widget-script',
        PRIVY_LOGIN_WIDGET_PLUGIN_URL . 'public/js/privy-login-widget.js',
        array('jquery'),
        PRIVY_LOGIN_WIDGET_VERSION,
        true
    );

    // Add Privy configuration
    wp_localize_script('privy-login-widget-script', 'privyConfig', array(
        'appId' => get_option('privy_app_id', ''),
        'loginMethods' => array('email', 'wallet'),
    ));
}
add_action('wp_enqueue_scripts', 'privy_login_widget_enqueue_scripts');

// Add settings link to plugins page
function privy_login_widget_settings_link($links) {
    $settings_link = '<a href="' . admin_url('options-general.php?page=privy-login-widget-settings') . '">' . __('Settings', 'privy-login-widget') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'privy_login_widget_settings_link'); 