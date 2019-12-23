<?php

namespace NSAAOptions;

use NSAAOptions\NSAADebug;
use NSAAOptions\NSAAConfig;

defined('ABSPATH') or die('');

class NSAAMeetingChanges {
    private static $POST_TYPE = 'nsaa_changes';

    /**
     * Return the post type
     */
    public static function getPostType() {
        return self::$POST_TYPE;
    }

    /**
     * Return an array of all the cities in alphabetical order
     */
    public static function getMeetingChanges() {

        $args = [
            'post_type' => self::$POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => 9999,
            'order' => 'ASC',
        ];

        $posts = \get_posts($args);

        $changes = [];
        foreach($posts as $post) {
            $changes[$post->ID] = ['title' => $post->post_title, 'content' => get_the_content( null, false, $post->ID )];
        }

        return $changes;

    }

    /**
     * Class constructor 
     * 
     * Performs all the initialization for the class
     */
    public function __construct() {
        add_action('init', [$this, 'register_cpt']);
    }
    
    /**
     * Register the custom post type for the class
     */
    public function register_cpt() {
        global $wp_version;
        $labels = [
            'name' => __('Meeting Changes', NSAAConfig::TEXT_DOMAIN),
            'singular_name' => __('Meeting Change', NSAAConfig::TEXT_DOMAIN),
            'menu_name' => __('Meeting Changes', NSAAConfig::TEXT_DOMAIN),
            'name_admin_bar' => __('Meeting Changes', NSAAConfig::TEXT_DOMAIN),
            'add_new' => __('Add New', NSAAConfig::TEXT_DOMAIN),
            'add_new_item' => __("Add New ", NSAAConfig::TEXT_DOMAIN),
            'new_item' => __('New Meeting Change', NSAAConfig::TEXT_DOMAIN),
            'edit_item' => __('Edit Meeting Change', NSAAConfig::TEXT_DOMAIN),
            'view_item' => __('View Meeting Changes', NSAAConfig::TEXT_DOMAIN),
            'all_items' => __('All Meeting Changes', NSAAConfig::TEXT_DOMAIN),
            'search_items' => __('Search Meeting Changes', NSAAConfig::TEXT_DOMAIN),
            'parent_item_colon' => __('Parent Meeting Change:', NSAAConfig::TEXT_DOMAIN),
            'not_found' => __('No Meeting Changes found.', NSAAConfig::TEXT_DOMAIN),
            'not_found_in_trash' => __('No Meeting Changes found in Trash.', NSAAConfig::TEXT_DOMAIN),
            'archives' => __('Meeting Change archives', NSAAConfig::TEXT_DOMAIN),
            'insert_into_item' => __('Insert into Meeting Change', NSAAConfig::TEXT_DOMAIN),
            'uploaded_to_this_item' => __('Uploaded to this Meeting Change', NSAAConfig::TEXT_DOMAIN),
            'filter_items_list' => __('Filter Meeting Changes list', NSAAConfig::TEXT_DOMAIN),
            'items_list_navigation' => __('Meeting Change list navigation', NSAAConfig::TEXT_DOMAIN),
            'items_list' => __('Meeting Change list', NSAAConfig::TEXT_DOMAIN),
        ];
        if (version_compare($wp_version, '5.0', '>=')) {
            $labels['item_published'] = __('Meeting Change published', NSAAConfig::TEXT_DOMAIN);
            $labels['item_published_privately'] = __('Meeting Change published privately', NSAAConfig::TEXT_DOMAIN);
            $labels['item_reverted_to_draft'] = __('Meeting Change reverted to draft', NSAAConfig::TEXT_DOMAIN);
            $labels['item_scheduled'] = __('Meeting Change scheduled', NSAAConfig::TEXT_DOMAIN);
            $labels['item_updated'] = __('Meeting Change updated', NSAAConfig::TEXT_DOMAIN);
        }
        $args = [
            'labels' => $labels,
            'public' => false,
            'publicly_queryable' => false,
            'show_ui' => true,
            'show_in_menu' => NSAAConfig::PLUGIN_PAGE,
            'show_in_rest' => false,
            'query_var' => false,
            'capability_type' => 'post',
            'has_archive' => false,
            'hierarchical' => false,
            'menu_position' => 200,
            'menu_icon' => 'dashicons-groups',
            'supports' => ['title', 'editor'],
        ];
        register_post_type(self::$POST_TYPE, $args);
    }
}
