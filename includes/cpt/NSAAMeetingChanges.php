<?php

namespace NSAAOptions;

use NSAAOptions\NSAADebug;
use NSAAOptions\NSAAConfig;

defined( 'ABSPATH' ) || die( '' );

/**
 * North Shore AA Meeting Changes class
 */
class NSAAMeetingChanges
{
	private static $POST_TYPE = 'nsaa_changes';

	/**
	 * Return the post type
	 */
	public static function getPostType() {
		return self::$POST_TYPE;
	}

	/**
	 * Return an array of all the meeting changes
	 */
	public static function getMeetingChanges() {

		$args = array(
			'post_type'      => self::$POST_TYPE,
			'post_status'    => 'publish',
			'posts_per_page' => 9999,
			'order'          => 'ASC',
		);

		$posts = \get_posts( $args );

		$changes = array();
		foreach ( $posts as $post ) {
			$changes[ $post->ID ] = array(
				'title'         => $post->post_title,
				'thumbnail'     => get_the_post_thumbnail( $post->ID, array( 300, 300 ) ),
				'thumbnail_url' => get_the_post_thumbnail_url( $post->ID ),
				'content'       => get_the_content( null, false, $post->ID ),
			);
		}

		return $changes;

	}

	/**
	 * Class constructor
	 *
	 * Performs all the initialization for the class
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_cpt' ) );
	}

	/**
	 * Register the custom post type for the class
	 */
	public function register_cpt() {
		global $wp_version;
		$labels = array(
			'name'                  => __( 'Meeting Changes', 'nsaa-options' ),
			'singular_name'         => __( 'Meeting Change', 'nsaa-options' ),
			'menu_name'             => __( 'Meeting Changes', 'nsaa-options' ),
			'name_admin_bar'        => __( 'Meeting Changes', 'nsaa-options' ),
			'add_new'               => __( 'Add New', 'nsaa-options' ),
			'add_new_item'          => __( 'Add New', 'nsaa-options' ),
			'new_item'              => __( 'New Meeting Change', 'nsaa-options' ),
			'edit_item'             => __( 'Edit Meeting Change', 'nsaa-options' ),
			'view_item'             => __( 'View Meeting Changes', 'nsaa-options' ),
			'all_items'             => __( 'All Meeting Changes', 'nsaa-options' ),
			'search_items'          => __( 'Search Meeting Changes', 'nsaa-options' ),
			'parent_item_colon'     => __( 'Parent Meeting Change:', 'nsaa-options' ),
			'not_found'             => __( 'No Meeting Changes found.', 'nsaa-options' ),
			'not_found_in_trash'    => __( 'No Meeting Changes found in Trash.', 'nsaa-options' ),
			'archives'              => __( 'Meeting Change archives', 'nsaa-options' ),
			'insert_into_item'      => __( 'Insert into Meeting Change', 'nsaa-options' ),
			'uploaded_to_this_item' => __( 'Uploaded to this Meeting Change', 'nsaa-options' ),
			'filter_items_list'     => __( 'Filter Meeting Changes list', 'nsaa-options' ),
			'items_list_navigation' => __( 'Meeting Change list navigation', 'nsaa-options' ),
			'items_list'            => __( 'Meeting Change list', 'nsaa-options' ),
		);
		if ( version_compare( $wp_version, '5.0', '>=' ) ) {
			$labels['item_published']           = __( 'Meeting Change published', 'nsaa-options' );
			$labels['item_published_privately'] = __( 'Meeting Change published privately', 'nsaa-options' );
			$labels['item_reverted_to_draft']   = __( 'Meeting Change reverted to draft', 'nsaa-options' );
			$labels['item_scheduled']           = __( 'Meeting Change scheduled', 'nsaa-options' );
			$labels['item_updated']             = __( 'Meeting Change updated', 'nsaa-options' );
		}
		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => NSAAConfig::PLUGIN_PAGE,
			'show_in_rest'       => false,
			'query_var'          => false,
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => 200,
			'menu_icon'          => 'dashicons-groups',
			'supports'           => array( 'title', 'editor', 'thumbnail' ),
		);
		register_post_type( self::$POST_TYPE, $args );
	}
}
