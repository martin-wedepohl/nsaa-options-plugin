<?php
/*
  Plugin Name: North Shore AA Options Plugin
  Plugin URI:
  Description: Optional information used in North Shore AA website
  Version: 0.1.3
  Author: Martin Wedepohl
  Author URI: https://wedepohlengineering.com
  License: GPLv3 or later
  License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
  Text Domain: nsaa-options
 */

namespace NSAAOptions;

use NSAAOptions\NSAAMeeting;
use NSAAOptions\NSAADebug;
use NSAAOptions\NSAAConfig;
use NSAAOptions\NSAASettings;
use NSAAOptions\NSAAShortocdes;
use NSAAOptions\NSAAServiceOp;

defined( 'ABSPATH' ) or die( '' );

require __DIR__ . '/vendor/autoload.php';

class NSAAOptions {
    private $_settings = null;
    private $_meeting = null;
    private $_extensions = null;
    private $_added = null;
    private $_breakfast = null;
    private $_cakes = null;
    private $_cancelled = null;
    private $_events = null;
    private $_gratitude = null;
    /**
     * Display error message if Wordpress is not the minimum version
     */
    static function wordpress_version() {
        $class = 'notice notice-error';
        $message = __( 'To use this plugin, Wordpress Version MUST be at least: ', NSAAConfig::TEXT_DOMAIN ) . NSAAConfig::MINIMUM_WORDPRESS_VERSION;
        printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
    }
    /**
     * Display error message if PHP is not the minimum version
     */
    static function php_version() {
        $class = 'notice notice-error';
        $message = __( 'To use this plugin, PHP Version MUST be at least: ', NSAAConfig::TEXT_DOMAIN ) . NSAAConfig::MINIMUM_PHP_VERSION;
        printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
    }
    /**
     * Check that the prerequisites are set for this plugin displaying messages
     * if not set
     *
     * @global type $wp_version
     *
     * @return boolean - True prerequisites passed, False prerequisites failed
     */
    static function check_prerequisites() {
        global $wp_version;
        $prerequisites_passed = true;
        // Check for required Wordprss version
        if ( !version_compare( $wp_version, NSAAConfig::MINIMUM_WORDPRESS_VERSION, '>=' ) ) {
            add_action( 'admin_notices', [ NSAAOptions::class, 'wordpress_version' ] );
            $prerequisites_passed = false;
        }
        // Check for required PHP version
        if ( !version_compare( PHP_VERSION, NSAAConfig::MINIMUM_PHP_VERSION, '>=' ) ) {
            add_action( 'admin_notices', [ NSAAOptions::class, 'php_version' ] );
            $prerequisites_passed = false;
        }
        // Did all the prerequisites, if not deactivate the plugin
        if ( !$prerequisites_passed ) {
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
            \deactivate_plugins( plugin_basename( __FILE__ ) );
            if ( isset( $_GET[ 'activate' ] ) ) {
                unset( $_GET[ 'activate' ] );
            }
        }
        return $prerequisites_passed;
    }
    /**
     * Class constructor
     *
     * @return null
     */
    public function __construct() {
        if ( !self::check_prerequisites() ) {
            return;
        }
        register_activation_hook( __FILE__, [ $this, 'activate' ] );
        register_deactivation_hook( __FILE__, [ $this, 'deactivate' ] );
        // Add Javascript and CSS for admin screens
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueueAdmin' ] );
        // Add Javascript and CSS for front-end display
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue' ] );
        // Add a settings link on the plugin page
        add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), [ $this, 'plugin_action_link' ] );
        // Add Google Analytics to head
//        add_action('wp_head', [$this, 'add_analytics_in_header'], 100);
//        add_action( 'elementor/elements/categories_registered', [ $this, 'create_custom_categories' ] );
//        add_filter( 'ocean_post_layout_class', [ $this, 'my_post_layout_class' ], 20 );
        $this->_settings = new NSAASettings();
        $this->_settings->register();
        $this->_meeting = new NSAAMeeting();
        NSAAShortcodes::initShortcodes();
        $this->_added = new NSAAAddedMeetings();
        $this->_breakfast = new NSAABreakfastMeetings();
        $this->_cakes = new NSAACake();
        $this->_cancelled = new NSAACancelledMeetings();
        $this->_events = new NSAAEvents();
        $this->_gratitude = new NSAAGratitude();
        new NSAAMeetingChanges();
        new NSAAServiceOp();
    }

    function my_post_layout_class( $class ) {

        // Alter your layout

        $post_type = get_post_type();
        $meeting_post_type = NSAAMeeting::getPostType();

        if($post_type === $meeting_post_type) {
            $class = 'full-width';
        }
    
        // Return correct class
        return $class;
    
    }
    
    /**
     * Function activation
     */
    function activate() {

        flush_rewrite_rules();

    }
    /**
     * Function deactivation
     */
    function deactivate() {

        // Delete all the auto delete crons if active 
        $this->_added->delete_cron();
        $this->_breakfast->delete_cron();
        $this->_cakes->delete_cron();
        $this->_cancelled->delete_cron();
        $this->_events->delete_cron();
        $this->_gratitude->delete_cron();

        flush_rewrite_rules();

    }
    /**
     * Enqueue all the administration scripts and styles
     */
    public function enqueueAdmin() {
        // These two lines allow you to only load the files on the relevant screen, in this case, the editor for a "books" custom post type
        $screen = get_current_screen();
        // Plugin specific pages
        // 'toplevel_page_' . NSAAConfig::PLUGIN_PAGE                        - The settings page
        // NSAAConfig::PLUGIN_PAGE . '_page_' . NSAAConfig::INSTRUCTIONS_PAGE - The instructions page
        if ( NSAAConfig::PLUGIN_PAGE . '_page_' . NSAAConfig::INSTRUCTIONS_PAGE === $screen->base ) {
//            wp_enqueue_script('instructions', plugins_url('dist/js/instructions.min.js', __FILE__), [], NSAAConfig::VERSION, true);
            wp_enqueue_style( 'instructions', plugins_url( 'dist/css/instructions.min.css', __FILE__ ), null, NSAAConfig::VERSION );
        }
        
        wp_enqueue_style( 'jquery-ui', plugins_url( 'assets/css/jquery-ui.min.css', __FILE__ ), null, NSAAConfig::VERSION );
        wp_enqueue_style( 'timepicker', plugins_url( 'assets/css/jquery-ui-timepicker-addon.min.css', __FILE__ ), null, NSAAConfig::VERSION );
        wp_enqueue_style( 'nsaa-options', plugins_url( 'dist/css/style.min.css', __FILE__ ), null, NSAAConfig::VERSION );

        wp_enqueue_script( 'jquery-ui-datepicker' );
        wp_dequeue_script( 'timepicker' );
        wp_enqueue_script( 'timepicker', plugins_url( 'assets/js/jquery-ui-timepicker-addon.min.js', __FILE__ ), ['jquery'], NSAAConfig::VERSION, true );
        wp_enqueue_script( 'nsaa-options', plugins_url( 'dist/js/scripts.min.js', __FILE__ ), [], NSAAConfig::VERSION, true );

    }
    /**
     * Enqueue the front end scripts and styles
     */
    public function enqueue() {
        // Actual enqueues, note the files are in the js and css folders
        // For scripts, make sure you are including the relevant dependencies (jquery in this case)
    	wp_enqueue_script('scripts-fe', plugins_url( 'dist/js/scripts-fe.min.js', __FILE__ ), array('jquery'), NSAAConfig::VERSION, true);
        wp_enqueue_style( 'nsaa-options-fe', plugins_url( 'dist/css/style-fe.min.css', __FILE__ ), null, NSAAConfig::VERSION );
        // Sometimes you want to have access to data on the front end in your Javascript file
        // Getting that requires this call. Always go ahead and include ajaxurl. Any other variables,
        // add to the array.
        // Then in the Javascript file, you can refer to it like this: externalName.someVariable
//        wp_localize_script( 'descriptive-name', 'externalName', array(
//            'ajaxurl' => admin_url('admin-ajax.php'),
//            'someVariable' => 'These are my socks'
//        ));
    }
    /**
     * Create the additional links underneath the plugin name
     *
     * @param array $links Current links array
     *
     * @return array
     */
    public function plugin_action_link( $links ) {
        $links[] = '<a href="' . admin_url( 'admin.php?page=' . NSAAConfig::SETTINGS_PAGE ) . '">' . __( 'Settings', NSAAConfig::TEXT_DOMAIN ) . '</a>';
        $links[] = '<a href="' . admin_url( 'admin.php?page=' . NSAAConfig::INSTRUCTIONS_PAGE ) . '">' . __( 'Instructions', NSAAConfig::TEXT_DOMAIN ) . '</a>';
        return $links;
    }
}
new NSAAOptions();
new NSAADebug();