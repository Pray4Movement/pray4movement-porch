<?php
/**
 * Plugin Name: Pray4Movement Site (Porch)
 * Plugin URI: https://github.com/Pray4Movement/pray4movement-site-porch
 * Description: Pray4Movement Site (Porch) places the starter landing page in front of the Disciple Tools install.
 * Text Domain: pray4movement-site-porch
 * Domain Path: /languages
 * Version:  0.1
 * Author URI: https://github.com/Pray4Movement
 * GitHub Plugin URI: https://github.com/Pray4Movement/pray4movement-site-porch
 * Requires at least: 4.7.0
 * (Requires 4.7+ because of the integration of the REST API at 4.7 and the security requirements of this milestone version.)
 * Tested up to: 5.6
 *
 * @package Disciple_Tools
 * @link    https://github.com/Pray4Movement
 * @license GPL-2.0 or later
 *          https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

/**
 * Gets the instance of the `Pray4Movement_Site_Porch` class.
 *
 * @since  0.1
 * @access public
 * @return object|bool
 */
function pray4movement_site_porch() {
    $pray4movement_site_porch_required_dt_theme_version = '1.0';
    $wp_theme = wp_get_theme();
    $version = $wp_theme->version;

    /*
     * Check if the Disciple.Tools theme is loaded and is the latest required version
     */
    $is_theme_dt = strpos( $wp_theme->get_template(), "disciple-tools-theme" ) !== false || $wp_theme->name === "Disciple Tools";
    if ( $is_theme_dt && version_compare( $version, $pray4movement_site_porch_required_dt_theme_version, "<" ) ) {
        add_action( 'admin_notices', 'pray4movement_site_porch_hook_admin_notice' );
        add_action( 'wp_ajax_dismissed_notice_handler', 'dt_hook_ajax_notice_handler' );
        return false;
    }
    if ( !$is_theme_dt ){
        return false;
    }
    /**
     * Load useful function from the theme
     */
    if ( !defined( 'DT_FUNCTIONS_READY' ) ){
        require_once get_template_directory() . '/dt-core/global-functions.php';
    }

    return Pray4Movement_Site_Porch::instance();

}
add_action( 'after_setup_theme', 'pray4movement_site_porch', 20 );

/**
 * Singleton class for setting up the plugin.
 *
 * @since  0.1
 * @access public
 */
class Pray4Movement_Site_Porch {

    private static $_instance = null;
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    private function __construct() {

        require_once( 'home/home.php' );

        require_once( 'admin/admin.php' );

        $this->i18n();

        if ( is_admin() ) { // adds links to the plugin description area in the plugin admin list.
            add_filter( 'plugin_row_meta', [ $this, 'plugin_description_links' ], 10, 4 );
        }

    }

    /**
     * Filters the array of row meta for each/specific plugin in the Plugins list table.
     * Appends additional links below each/specific plugin on the plugins page.
     */
    public function plugin_description_links( $links_array, $plugin_file_name, $plugin_data, $status ) {
        if ( strpos( $plugin_file_name, basename( __FILE__ ) ) ) {

            $links_array[] = '<a href="https://disciple.tools">Disciple.Tools Community</a>';
        }

        return $links_array;
    }

    /**
     * Method that runs only when the plugin is activated.
     *
     * @since  0.1
     * @access public
     * @return void
     */
    public static function activation() {
        // add elements here that need to fire on activation
    }

    /**
     * Method that runs only when the plugin is deactivated.
     *
     * @since  0.1
     * @access public
     * @return void
     */
    public static function deactivation() {
        // add functions here that need to happen on deactivation
        delete_option( 'dismissed-pray4movement-site-porch' );
    }

    /**
     * Loads the translation files.
     *
     * @since  0.1
     * @access public
     * @return void
     */
    public function i18n() {
        $domain = 'pray4movement-site-porch';
        load_plugin_textdomain( $domain, false, trailingslashit( dirname( plugin_basename( __FILE__ ) ) ). 'languages' );
    }

    /**
     * Magic method to output a string if trying to use the object as a string.
     *
     * @since  0.1
     * @access public
     * @return string
     */
    public function __toString() {
        return 'pray4movement-site-porch';
    }

    /**
     * Magic method to keep the object from being cloned.
     *
     * @since  0.1
     * @access public
     * @return void
     */
    public function __clone() {
        _doing_it_wrong( __FUNCTION__, 'Whoah, partner!', '0.1' );
    }

    /**
     * Magic method to keep the object from being unserialized.
     *
     * @since  0.1
     * @access public
     * @return void
     */
    public function __wakeup() {
        _doing_it_wrong( __FUNCTION__, 'Whoah, partner!', '0.1' );
    }

    /**
     * Magic method to prevent a fatal error when calling a method that doesn't exist.
     *
     * @param string $method
     * @param array $args
     * @return null
     * @since  0.1
     * @access public
     */
    public function __call( $method = '', $args = array() ) {
        _doing_it_wrong( "pray4movement_site_porch::" . esc_html( $method ), 'Method does not exist.', '0.1' );
        unset( $method, $args );
        return null;
    }
}


// Register activation hook.
register_activation_hook( __FILE__, [ 'Pray4Movement_Site_Porch', 'activation' ] );
register_deactivation_hook( __FILE__, [ 'Pray4Movement_Site_Porch', 'deactivation' ] );


if ( ! function_exists( 'pray4movement_site_porch_hook_admin_notice' ) ) {
    function pray4movement_site_porch_hook_admin_notice() {
        global $pray4movement_site_porch_required_dt_theme_version;
        $wp_theme = wp_get_theme();
        $current_version = $wp_theme->version;
        $message = "'Pray4Movement Site (Porch)' plugin requires 'Disciple Tools' theme to work. Please activate 'Disciple Tools' theme or make sure it is latest version.";
        if ( $wp_theme->get_template() === "disciple-tools-theme" ){
            $message .= ' ' . sprintf( esc_html( 'Current Disciple Tools version: %1$s, required version: %2$s' ), esc_html( $current_version ), esc_html( $pray4movement_site_porch_required_dt_theme_version ) );
        }
        // Check if it's been dismissed...
        if ( ! get_option( 'dismissed-pray4movement-site-porch', false ) ) { ?>
            <div class="notice notice-error notice-pray4movement-site-porch is-dismissible" data-notice="pray4movement-site-porch">
                <p><?php echo esc_html( $message );?></p>
            </div>
            <script>
                jQuery(function($) {
                    $( document ).on( 'click', '.notice-pray4movement-site-porch .notice-dismiss', function () {
                        $.ajax( ajaxurl, {
                            type: 'POST',
                            data: {
                                action: 'dismissed_notice_handler',
                                type: 'pray4movement-site-porch',
                                security: '<?php echo esc_html( wp_create_nonce( 'wp_rest_dismiss' ) ) ?>'
                            }
                        })
                    });
                });
            </script>
        <?php }
    }
}

/**
 * AJAX handler to store the state of dismissible notices.
 */
if ( ! function_exists( "dt_hook_ajax_notice_handler" )){
    function dt_hook_ajax_notice_handler(){
        check_ajax_referer( 'wp_rest_dismiss', 'security' );
        if ( isset( $_POST["type"] ) ){
            $type = sanitize_text_field( wp_unslash( $_POST["type"] ) );
            update_option( 'dismissed-' . $type, true );
        }
    }
}

add_action( 'plugins_loaded', function (){
    if ( is_admin() ){
        // Check for plugin updates
        if ( ! class_exists( 'Puc_v4_Factory' ) ) {
            if ( file_exists( get_template_directory() . '/dt-core/libraries/plugin-update-checker/plugin-update-checker.php' )){
                require( get_template_directory() . '/dt-core/libraries/plugin-update-checker/plugin-update-checker.php' );
            }
        }
        if ( class_exists( 'Puc_v4_Factory' ) ){
            Puc_v4_Factory::buildUpdateChecker(
                'https://raw.githubusercontent.com/Pray4Movement/pray4movement-porch/master/version-control.json',
                __FILE__,
                'pray4movement-site-porch'
            );

        }
    }
} );


function p4m_porch_fields() {
        $defaults = [
            'title' => [
                'label' => 'Title',
                'value' => '',
                'type' => 'text',
            ],
            'description' => [
                'label' => 'Description',
                'value' => '',
                'type' => 'text'
            ],
            'location' => [
                'label' => 'Location',
                'value' => '',
                'type' => 'text'
            ],
            'logo_url' => [
                'label' => 'Logo URL',
                'value' => '',
                'type' => 'text'
            ],
            'background_image_url' => [
                'label' => 'Background Image URL',
                'value' => '',
                'type' => 'text'
            ],
            'facebook_url' => [
                'label' => 'Facebook URL',
                'value' => '',
                'type' => 'text'
            ],
            'facebook_events_url' => [
                'label' => 'Facebook Events URL',
                'value' => '',
                'type' => 'text'
            ],
            'instagram_url' => [
                'label' => 'Instagram URL',
                'value' => '',
                'type' => 'text'
            ],
            'twitter_url' => [
                'label' => 'Twitter URL',
                'value' => '',
                'type' => 'text'
            ],
            'signal_url' => [
                'label' => 'Signal Join Link',
                'value' => '',
                'type' => 'text'
            ],
            'streets_of_prayer_url' => [
                'label' => 'Streets of Prayer Link',
                'value' => '',
                'type' => 'text'
            ],
            'whatsapp_url' => [
                'label' => 'WhatsApp Join Link',
                'value' => '',
                'type' => 'text'
            ],
            'zoom_meeting_time' => [
                'label' => 'Zoom Meeting Time',
                'value' => '',
                'type' => 'text'
            ],
            'zoom_url' => [
                'label' => 'Zoom Join Link',
                'value' => '',
                'type' => 'text'
            ],
            'movement_training' => [
                'label' => 'Offer Movement Training',
                'value' => '',
                'type' => 'select',
                'defaults' => [
                    'no' => 'No',
                    'yes' => 'Yes'
                ]
            ],

            'mailchimp_form_url' => [
                'label' => 'Mailchimp Form URL',
                'value' => '',
                'type' => 'text'
            ],
            'mailchimp_form_hidden_id' => [
                'label' => 'Mailchimp Form Hidden ID',
                'value' => '',
                'type' => 'text'
            ],

            'samples_section' => [
                'label' => 'Samples Section',
                'value' => '',
                'type' => 'select',
                'defaults' => [
                    'no' => 'No',
                    'yes' => 'Yes'
                ]
            ],
            'stats_population' => [
                'label' => 'Population',
                'value' => '',
                'type' => 'text'
            ],
            'stats_cities' => [
                'label' => 'Cities',
                'value' => '',
                'type' => 'text'
            ],
            'stats_trainings' => [
                'label' => 'Trainings',
                'value' => '',
                'type' => 'text'
            ],
            'stats_churches' => [
                'label' => 'Churches',
                'value' => '',
                'type' => 'text'
            ],
            'contact_form' => [
                'label' => 'Contact Form (GDPR)',
                'value' => '',
                'type' => 'textarea'
            ],
            'google_analytics' => [
                'label' => 'Google Analytics',
                'value' => '',
                'type' => 'textarea'
            ],
            'mailchimp_api_key' => [
                'label' => 'Mailchimp Key',
                'value' => '',
                'type' => 'text'
            ],
            'mailchimp_list_id' => [
                'label' => 'Mailchimp List ID',
                'value' => '',
                'type' => 'text'
            ],
            'mailchimp_form_url' => [
                'label' => 'Mailchimp Form URL',
                'value' => '',
                'type' => 'text'
            ],
            'mailchimp_form_hidden_id' => [
                'label' => 'Mailchimp Form Hidden ID',
                'value' => '',
                'type' => 'text'
            ],
            'status_for_subscriptions' => [
                'label' => 'Status for Subscriptions',
                'value' => '',
                'type' => 'text'
            ],
            'source_for_subscriptions' => [
                'label' => 'Source for Subscriptions',
                'value' => '',
                'type' => 'source_for_subscriptions'
            ],
            'assigned_user_for_followup' => [
                'label' => 'Assigned User for Followup',
                'value' => '',
                'type' => 'assigned_user_for_followup'
            ],
        ];

        $defaults_count = count($defaults);

        $saved_fields = get_option('landing_content_v2', [] );
        $saved_count = count($saved_fields);

        $fields = wp_parse_args($saved_fields, $defaults);

        if ( $defaults_count !== $saved_count ) {
            update_option( 'landing_content_v2', $fields );
        }

        return $fields;
}
