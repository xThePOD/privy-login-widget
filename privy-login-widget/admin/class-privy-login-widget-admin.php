<?php
/**
 * Admin settings page for Privy Login Widget
 */
class Privy_Login_Widget_Admin {
    /**
     * Initialize the class
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_plugin_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    /**
     * Add options page to the admin menu
     */
    public function add_plugin_admin_menu() {
        add_options_page(
            __('Privy Login Widget Settings', 'privy-login-widget'),
            __('Privy Login', 'privy-login-widget'),
            'manage_options',
            'privy-login-widget-settings',
            array($this, 'display_plugin_admin_page')
        );
    }

    /**
     * Register plugin settings
     */
    public function register_settings() {
        register_setting('privy_login_widget_settings', 'privy_app_id');
    }

    /**
     * Render the settings page
     */
    public function display_plugin_admin_page() {
        ?>
        <div class="wrap">
            <h2><?php _e('Privy Login Widget Settings', 'privy-login-widget'); ?></h2>
            <form method="post" action="options.php">
                <?php
                settings_fields('privy_login_widget_settings');
                do_settings_sections('privy_login_widget_settings');
                ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php _e('Privy App ID', 'privy-login-widget'); ?></th>
                        <td>
                            <input type="text" name="privy_app_id" value="<?php echo esc_attr(get_option('privy_app_id')); ?>" class="regular-text" />
                            <p class="description">
                                <?php _e('Enter your Privy App ID. You can find this in your Privy dashboard.', 'privy-login-widget'); ?>
                            </p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}

// Initialize the admin class
new Privy_Login_Widget_Admin(); 