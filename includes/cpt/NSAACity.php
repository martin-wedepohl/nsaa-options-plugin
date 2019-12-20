<?php

namespace NSAAOptions;

use NSAAOptions\NSAADebug;
use NSAAOptions\NSAAConfig;

defined('ABSPATH') or die('');

class NSAACity {
    private static $POST_TYPE = 'nsaa_cities';

    /**
     * Return the post type
     */
    public static function getPostType() {
        return self::$POST_TYPE;
    }

    /**
     * Return an array of all the cities in alphabetical order
     */
    public static function getCities() {

        $args = [
            'post_type' => self::$POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => 9999,
            'order' => 'ASC',
        ];

        $city_posts = \get_posts($args);

        $cities = [];
        foreach($city_posts as $post) {
            $cities[$post->post_name] = $post->post_title;
        }

        return $cities;

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
            'name' => __('Cities', NSAAConfig::TEXT_DOMAIN),
            'singular_name' => __('City', NSAAConfig::TEXT_DOMAIN),
            'menu_name' => __('Cities', NSAAConfig::TEXT_DOMAIN),
            'name_admin_bar' => __('Cities', NSAAConfig::TEXT_DOMAIN),
            'add_new' => __('Add New', NSAAConfig::TEXT_DOMAIN),
            'add_new_item' => __("Add New ", NSAAConfig::TEXT_DOMAIN),
            'new_item' => __('New City', NSAAConfig::TEXT_DOMAIN),
            'edit_item' => __('Edit City', NSAAConfig::TEXT_DOMAIN),
            'view_item' => __('View Cities', NSAAConfig::TEXT_DOMAIN),
            'all_items' => __('All Cities', NSAAConfig::TEXT_DOMAIN),
            'search_items' => __('Search Cities', NSAAConfig::TEXT_DOMAIN),
            'parent_item_colon' => __('Parent City:', NSAAConfig::TEXT_DOMAIN),
            'not_found' => __('No Cities found.', NSAAConfig::TEXT_DOMAIN),
            'not_found_in_trash' => __('No Cities found in Trash.', NSAAConfig::TEXT_DOMAIN),
            'archives' => __('City archives', NSAAConfig::TEXT_DOMAIN),
            'insert_into_item' => __('Insert into City', NSAAConfig::TEXT_DOMAIN),
            'uploaded_to_this_item' => __('Uploaded to this City', NSAAConfig::TEXT_DOMAIN),
            'filter_items_list' => __('Filter Cities list', NSAAConfig::TEXT_DOMAIN),
            'items_list_navigation' => __('City list navigation', NSAAConfig::TEXT_DOMAIN),
            'items_list' => __('City list', NSAAConfig::TEXT_DOMAIN),
        ];
        if (version_compare($wp_version, '5.0', '>=')) {
            $labels['item_published'] = __('City published', NSAAConfig::TEXT_DOMAIN);
            $labels['item_published_privately'] = __('City published privately', NSAAConfig::TEXT_DOMAIN);
            $labels['item_reverted_to_draft'] = __('City reverted to draft', NSAAConfig::TEXT_DOMAIN);
            $labels['item_scheduled'] = __('City scheduled', NSAAConfig::TEXT_DOMAIN);
            $labels['item_updated'] = __('City updated', NSAAConfig::TEXT_DOMAIN);
        }
        $args = [
            'labels' => $labels,
            'public' => false,
            'publicly_queryable' => false,
            'show_ui' => true,
            'show_in_menu' => NSAAConfig::PLUGIN_PAGE,
            'show_in_rest' => false,
            'query_var' => false,
            'rewrite' => ['slug' => 'cities', 'with_front' => true],
            'capability_type' => 'post',
            'has_archive' => false,
            'hierarchical' => false,
            'menu_position' => 200,
            'menu_icon' => 'dashicons-groups',
            'supports' => ['title'],
        ];
        register_post_type(self::$POST_TYPE, $args);
    }
}
