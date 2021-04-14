<?php

namespace NSAAOptions;

use NSAAOptions\NSAAConfig;

defined( 'ABSPATH' ) || die( '' );

/**
 * North Shore AA News class
 */
class NSAANews
{
	private static $POST_TYPE = 'nsaa_news';

	/**
	 * Return the post type
	 */
	public static function getPostType() {
		return self::$POST_TYPE;
	}

	/**
	 * Return an array of all the news
	 */
	public static function getNews() {

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
			'name'                  => __( 'News', 'nsaa-options' ),
			'singular_name'         => __( 'News', 'nsaa-options' ),
			'menu_name'             => __( 'News', 'nsaa-options' ),
			'name_admin_bar'        => __( 'News', 'nsaa-options' ),
			'add_new'               => __( 'Add News', 'nsaa-options' ),
			'add_new_item'          => __( 'Add News', 'nsaa-options' ),
			'new_item'              => __( 'New News', 'nsaa-options' ),
			'edit_item'             => __( 'Edit News', 'nsaa-options' ),
			'view_item'             => __( 'View News', 'nsaa-options' ),
			'all_items'             => __( 'All News', 'nsaa-options' ),
			'search_items'          => __( 'Search News', 'nsaa-options' ),
			'parent_item_colon'     => __( 'Parent News:', 'nsaa-options' ),
			'not_found'             => __( 'No News found.', 'nsaa-options' ),
			'not_found_in_trash'    => __( 'No News found in Trash.', 'nsaa-options' ),
			'archives'              => __( 'News archives', 'nsaa-options' ),
			'insert_into_item'      => __( 'Insert into News', 'nsaa-options' ),
			'uploaded_to_this_item' => __( 'Uploaded to this News', 'nsaa-options' ),
			'filter_items_list'     => __( 'Filter News list', 'nsaa-options' ),
			'items_list_navigation' => __( 'News list navigation', 'nsaa-options' ),
			'items_list'            => __( 'News list', 'nsaa-options' ),
		);
		if ( version_compare( $wp_version, '5.0', '>=' ) ) {
			$labels['item_published']           = __( 'News published', 'nsaa-options' );
			$labels['item_published_privately'] = __( 'News published privately', 'nsaa-options' );
			$labels['item_reverted_to_draft']   = __( 'News reverted to draft', 'nsaa-options' );
			$labels['item_scheduled']           = __( 'News scheduled', 'nsaa-options' );
			$labels['item_updated']             = __( 'News updated', 'nsaa-options' );
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
