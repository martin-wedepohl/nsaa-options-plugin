<?php
/*
Plugin Name: North Shore AA Options Plugin
Plugin URI:
Description: Optional information used in North Shore AA website
Version: 0.1.15
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
use NSAAOptions\NSAANews;

defined( 'ABSPATH' ) || die( '' );

require __DIR__ . '/vendor/autoload.php';

class NSAAOptions {
	private $_settings   = null;
	private $_meeting    = null;
	private $_extensions = null;
	private $_added      = null;
	private $_breakfast  = null;
	private $_cakes      = null;
	private $_cancelled  = null;
	private $_events     = null;
	private $_gratitude  = null;
	/**
	 * Display error message if WordPress is not the minimum version
	 */
	public static function wordpress_version() {
		$class   = 'notice notice-error';
		$message = __( 'To use this plugin, WordPress Version MUST be at least: ', 'nsaa-options' ) . NSAAConfig::MINIMUM_WORDPRESS_VERSION;
		printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
	}
	/**
	 * Display error message if PHP is not the minimum version
	 */
	public static function php_version() {
		$class   = 'notice notice-error';
		$message = __( 'To use this plugin, PHP Version MUST be at least: ', 'nsaa-options' ) . NSAAConfig::MINIMUM_PHP_VERSION;
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
	public static function check_prerequisites() {
		global $wp_version;
		$prerequisites_passed = true;
		// Check for required Wordprss version.
		if ( ! version_compare( $wp_version, NSAAConfig::MINIMUM_WORDPRESS_VERSION, '>=' ) ) {
			add_action( 'admin_notices', array( self::class, 'wordpress_version' ) );
			$prerequisites_passed = false;
		}
		// Check for required PHP version.
		if ( ! version_compare( PHP_VERSION, NSAAConfig::MINIMUM_PHP_VERSION, '>=' ) ) {
			add_action( 'admin_notices', array( self::class, 'php_version' ) );
			$prerequisites_passed = false;
		}
		// Did all the prerequisites, if not deactivate the plugin.
		if ( ! $prerequisites_passed ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
			\deactivate_plugins( plugin_basename( __FILE__ ) );
			if ( isset( $_GET['activate'] ) ) {
				unset( $_GET['activate'] );
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
		if ( ! self::check_prerequisites() ) {
			return;
		}
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

		// Add Javascript and CSS for admin screens.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueueAdmin' ) );

		// Add Javascript and CSS for front-end display.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );

		// Add a settings link on the plugin page.
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_link' ) );

		// Add Google Analytics to head.
		add_action( 'wp_head', array( $this, 'add_analytics_in_header' ), 100 );

		// Remove comments from website.
		add_action( 'admin_init', array( $this, 'remove_comments' ) );

		// Close comments on the front-end.
		add_filter( 'comments_open', '__return_false', 20, 2 );
		add_filter( 'pings_open', '__return_false', 20, 2 );

		// Hide existing comments.
		add_filter( 'comments_array', '__return_empty_array', 10, 2 );

		// Remove comments page in menu and discussion submenu page.
		add_action(
			'admin_menu',
			function () {
				remove_menu_page( 'edit-comments.php' );
				remove_submenu_page( 'options-general.php', 'options-discussion.php' );
			}
		);

		// Remove comments links from admin bar.
		add_action(
			'init',
			function () {
				if ( is_admin_bar_showing() ) {
					remove_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 );
				}
			}
		);

		// Clean up code in header.
		remove_action( 'wp_head', 'rsd_link' );                                   // Remove "Really Simple Discovery" tag; not needed if XML-RPC is disabled.
		remove_action( 'wp_head', 'wlwmanifest_link' );                           // Remove "Windows Live Writer Manifest" tag.
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );            // Remove emoji JavaScript tags.
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );                 // Remove emoji CSS tags.
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		add_filter( 'emoji_svg_url', '__return_false' );                          // Remove emoji DNS prefetch tag.
		remove_action( 'wp_head', 'wp_shortlink_wp_head' );                       // Remove shortlink tag.
		remove_action( 'wp_head', 'wp_generator' );                               // Remove generator tag.
		remove_action( 'wp_head', 'feed_links', 2 );                              // Remove general RSS feed tags.
		remove_action( 'wp_head', 'feed_links_extra', 3 );                        // Remove specific-post RSS comment feed tags.
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head' );            // Remove prev/next relational tags.
		remove_action( 'wp_head', 'rest_output_link_wp_head' );                   // Remove WordPress API call tag.
		remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );              // Remove oembed discovery tags.

		// All the plugin classes.
		new NSAADebug();
		$this->_settings = new NSAASettings();
		$this->_settings->register();
		$this->_meeting = new NSAAMeeting();
		NSAAShortcodes::initShortcodes();
		$this->_added     = new NSAAAddedMeetings();
		$this->_breakfast = new NSAABreakfastMeetings();
		$this->_cakes     = new NSAACake();
		$this->_cancelled = new NSAACancelledMeetings();
		$this->_events    = new NSAAEvents();
		$this->_gratitude = new NSAAGratitude();
		new NSAAMeetingChanges();
		new NSAANews();
		new NSAAServiceOp();
	}

	/**
	 * Function activation
	 */
	public function activate() {

		flush_rewrite_rules();

	}
	/**
	 * Function deactivation
	 */
	public function deactivate() {

		// Delete all the auto delete crons if active.
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
		$screen = get_current_screen();
		if ( NSAAConfig::PLUGIN_PAGE . '_page_' . NSAAConfig::INSTRUCTIONS_PAGE === $screen->base ) {
			wp_enqueue_style( 'instructions', plugins_url( 'dist/css/instructions.min.css', __FILE__ ), null, NSAAConfig::VERSION );
		}

		wp_enqueue_style( 'jquery-ui', plugins_url( 'assets/css/jquery-ui.min.css', __FILE__ ), null, NSAAConfig::VERSION );
		wp_enqueue_style( 'timepicker', plugins_url( 'assets/css/jquery-ui-timepicker-addon.min.css', __FILE__ ), null, NSAAConfig::VERSION );
		wp_enqueue_style( 'nsaa-options', plugins_url( 'dist/css/style.min.css', __FILE__ ), null, NSAAConfig::VERSION );

		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_dequeue_script( 'timepicker' );
		wp_enqueue_script( 'timepicker', plugins_url( 'assets/js/jquery-ui-timepicker-addon.min.js', __FILE__ ), array( 'jquery' ), NSAAConfig::VERSION, true );
		wp_enqueue_script( 'nsaa-options', plugins_url( 'dist/js/scripts.min.js', __FILE__ ), array(), NSAAConfig::VERSION, true );

	}
	/**
	 * Enqueue the front end scripts and styles
	 */
	public function enqueue() {
		wp_enqueue_style( 'nsaa-options-fe', plugins_url( 'dist/css/style-fe.min.css', __FILE__ ), null, NSAAConfig::VERSION );
		if ( is_front_page() ) {
			wp_enqueue_script( 'scripts-fe', plugins_url( 'dist/js/scripts-fe.min.js', __FILE__ ), array( 'jquery' ), NSAAConfig::VERSION, true );
		}
		if ( is_page( 'contact-us' ) ) {
			wp_enqueue_script( 'nsaa-contact-fe', plugins_url( 'dist/js/scripts-contact-fe.min.js', __FILE__ ), array(), NSAAConfig::VERSION, true );
		}
	}
	/**
	 * Create the additional links underneath the plugin name
	 *
	 * @param array $links Current links array.
	 *
	 * @return array
	 */
	public function plugin_action_link( $links ) {
		$links[] = '<a href="' . admin_url( 'admin.php?page=' . NSAAConfig::SETTINGS_PAGE ) . '">' . __( 'Settings', 'nsaa-options' ) . '</a>';
		$links[] = '<a href="' . admin_url( 'admin.php?page=' . NSAAConfig::INSTRUCTIONS_PAGE ) . '">' . __( 'Instructions', 'nsaa-options' ) . '</a>';
		return $links;
	}

	/**
	 * Add Google Analytics to the head of the pages only if an analytics string is set
	 */
	public function add_analytics_in_header() {

		$analytics_code = $this->_settings->get_options( 'google' );
		if ( '' !== $analytics_code ) {
			$script  = '<!-- Google Analytics -->';
			$script .= '<script>';
			$script .= 'window.ga=window.ga||function(){(ga.q=ga.q||[]).push(arguments)};ga.l=+new Date;';
			$script .= 'ga(\'create\',\'' . $analytics_code . '\',\'auto\');';
			$script .= 'ga(\'send\',\'pageview\');';
			$script .= '</script>';
			$script .= '<script async src=\'https://www.google-analytics.com/analytics.js\'></script>';
			$script .= '<!-- End Google Analytics -->';

			echo $script;
		}

	}

	/**
	 * Remove comments from the website if we want to
	 */
	public function remove_comments() {

		// Redirect any user trying to access comments page.
		global $pagenow;

		if ( 'edit-comments.php' === $pagenow ) {
			wp_safe_redirect( admin_url() );
			exit;
		}

		// Remove comments metabox from dashboard.
		remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );

		// Disable support for comments and trackbacks in post types.
		foreach ( get_post_types() as $post_type ) {
			if ( post_type_supports( $post_type, 'comments' ) ) {
				remove_post_type_support( $post_type, 'comments' );
				remove_post_type_support( $post_type, 'trackbacks' );
			}
		}

	}

}
new NSAAOptions();
