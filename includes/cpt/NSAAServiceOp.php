<?php

namespace NSAAOptions;

use NSAAOptions\NSAADebug;
use NSAAOptions\NSAAConfig;

defined('ABSPATH') or die('');

class NSAAServiceOp {
    private static $POST_TYPE = 'nsaa_service_op';

    /**
     * Return the post type
     */
    public static function getPostType() {
        return self::$POST_TYPE;
    }

    /**
     * Return an array of all the cities in alphabetical order
     */
    public static function getServiceOps() {

        $args = [
            'post_type' => self::$POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => 9999,
            'order' => 'ASC',
        ];

        $service_posts = \get_posts($args);

        $services = [];
        foreach($service_posts as $post) {
            $services[$post->ID] = ['title' => $post->post_title, 'content' => get_the_content( null, false, $post->ID )];
        }

        return $services;

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
            'name' => __('Service Ops', NSAAConfig::TEXT_DOMAIN),
            'singular_name' => __('Service Op', NSAAConfig::TEXT_DOMAIN),
            'menu_name' => __('Service Ops', NSAAConfig::TEXT_DOMAIN),
            'name_admin_bar' => __('Service Ops', NSAAConfig::TEXT_DOMAIN),
            'add_new' => __('Add New', NSAAConfig::TEXT_DOMAIN),
            'add_new_item' => __("Add New ", NSAAConfig::TEXT_DOMAIN),
            'new_item' => __('New Service Op', NSAAConfig::TEXT_DOMAIN),
            'edit_item' => __('Edit Service Op', NSAAConfig::TEXT_DOMAIN),
            'view_item' => __('View Service Ops', NSAAConfig::TEXT_DOMAIN),
            'all_items' => __('All Service Ops', NSAAConfig::TEXT_DOMAIN),
            'search_items' => __('Search Service Ops', NSAAConfig::TEXT_DOMAIN),
            'parent_item_colon' => __('Parent Service Op:', NSAAConfig::TEXT_DOMAIN),
            'not_found' => __('No Service Ops found.', NSAAConfig::TEXT_DOMAIN),
            'not_found_in_trash' => __('No Service Ops found in Trash.', NSAAConfig::TEXT_DOMAIN),
            'archives' => __('Service Op archives', NSAAConfig::TEXT_DOMAIN),
            'insert_into_item' => __('Insert into Service Op', NSAAConfig::TEXT_DOMAIN),
            'uploaded_to_this_item' => __('Uploaded to this Service Op', NSAAConfig::TEXT_DOMAIN),
            'filter_items_list' => __('Filter Service Ops list', NSAAConfig::TEXT_DOMAIN),
            'items_list_navigation' => __('Service Op list navigation', NSAAConfig::TEXT_DOMAIN),
            'items_list' => __('Service Op list', NSAAConfig::TEXT_DOMAIN),
        ];
        if (version_compare($wp_version, '5.0', '>=')) {
            $labels['item_published'] = __('Service Op published', NSAAConfig::TEXT_DOMAIN);
            $labels['item_published_privately'] = __('Service Op published privately', NSAAConfig::TEXT_DOMAIN);
            $labels['item_reverted_to_draft'] = __('Service Op reverted to draft', NSAAConfig::TEXT_DOMAIN);
            $labels['item_scheduled'] = __('Service Op scheduled', NSAAConfig::TEXT_DOMAIN);
            $labels['item_updated'] = __('Service Op updated', NSAAConfig::TEXT_DOMAIN);
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
