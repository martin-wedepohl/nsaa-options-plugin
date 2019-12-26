<?php

namespace NSAAOptions;

use NSAAOptions\NSAADebug;
use NSAAOptions\NSAAConfig;

defined('ABSPATH') or die('');

class NSAALegend {
    private static $META_DATA_KEY = '_meta_legends_data';
    private static $META_BOX_DATA = 'nsaa_legends_save_meta_box_data';
    private static $META_BOX_NONCE = 'nsaa_legends_meta_box_nonce';
    private static $POST_TYPE = 'nsaa_legend';

    /**
     * Get all the legend meta data for the post id
     * 
     * All the data will be sanitized.
     *  
     * @param int $post_id        - The ID of the post
     * 
     * @return array - Associative array with all the meta data
     */
    public static function get_legend_data($post_id) {

        $legend_data = get_post_meta($post_id, self::$META_DATA_KEY);
        if (count($legend_data) > 0) {
            $legend_data = $legend_data[0];
        } else {
            $legend_data = [];
        }
        $legend_data = shortcode_atts(
                [
                    'code' => '',
                    'additional' => '',
                ],
                $legend_data,
                'get_legend_data'
        );
        $legend_data['code'] = sanitize_text_field($legend_data['code']);
        $legend_data['additional'] = sanitize_text_field($legend_data['additional']);

        return $legend_data;

    }

    public static function getLegends() {

        $args = [
            'post_type' => self::$POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => 9999,
            'orderby' => 'title',
            'order' => 'ASC',
        ];
        $legend_posts = \get_posts($args);

        $legends = [];
        foreach($legend_posts as $id => $post) {
            $meta = \get_post_meta($post->ID, '_meta_legends_data');
            $legends[$meta[0]['code']] = 
                [
                'name' => $post->post_title,
                'additional' => $meta[0]['additional']
                ];
        }

        return $legends;

    }

    public static function getServiceLegend( $service_name = 'Service Meeting' ) {

        $legendId = false;
        $legends = NSAALegend::getLegends();

        foreach($legends as $id => $value) {
            if($value['name'] === $service_name) {
                return $id;
            }
        }

        return $legendId;
    }

    /**
     * Class constructor 
     * 
     * Performs all the initialization for the class
     */
    public function __construct() {
        add_action('init', [$this, 'register_cpt']);
        add_action('save_post', [$this, 'save_meta'], 1, 2);
        add_filter('manage_edit-' . self::$POST_TYPE . '_columns', [$this, 'table_head']);
        add_action('manage_' . self::$POST_TYPE . '_posts_custom_column', [$this, 'table_content'], 10, 2);
    }
    public static function getPostType() {
        return self::$POST_TYPE;
    }
    public static function getMetaKey() {
        return self::$META_DATA_KEY;
    }
    /**
     * Register the custom post type for the class
     */
    public function register_cpt() {
        global $wp_version;
        $labels = [
            'name' => __('Legends', NSAAConfig::TEXT_DOMAIN),
            'singular_name' => __('Legend', NSAAConfig::TEXT_DOMAIN),
            'menu_name' => __('Legends', NSAAConfig::TEXT_DOMAIN),
            'name_admin_bar' => __('Legends', NSAAConfig::TEXT_DOMAIN),
            'add_new' => __('Add New', NSAAConfig::TEXT_DOMAIN),
            'add_new_item' => __("Add New ", NSAAConfig::TEXT_DOMAIN),
            'new_item' => __('New Legend', NSAAConfig::TEXT_DOMAIN),
            'edit_item' => __('Edit Legend', NSAAConfig::TEXT_DOMAIN),
            'view_item' => __('View Legends', NSAAConfig::TEXT_DOMAIN),
            'all_items' => __('All Legends', NSAAConfig::TEXT_DOMAIN),
            'search_items' => __('Search Legends', NSAAConfig::TEXT_DOMAIN),
            'parent_item_colon' => __('Parent Legend:', NSAAConfig::TEXT_DOMAIN),
            'not_found' => __('No Legends found.', NSAAConfig::TEXT_DOMAIN),
            'not_found_in_trash' => __('No Legends found in Trash.', NSAAConfig::TEXT_DOMAIN),
            'archives' => __('Legend archives', NSAAConfig::TEXT_DOMAIN),
            'insert_into_item' => __('Insert into Legend', NSAAConfig::TEXT_DOMAIN),
            'uploaded_to_this_item' => __('Uploaded to this Legend', NSAAConfig::TEXT_DOMAIN),
            'filter_items_list' => __('Filter Legends list', NSAAConfig::TEXT_DOMAIN),
            'items_list_navigation' => __('Legend list navigation', NSAAConfig::TEXT_DOMAIN),
            'items_list' => __('Legend list', NSAAConfig::TEXT_DOMAIN),
        ];
        if (version_compare($wp_version, '5.0', '>=')) {
            $labels['item_published'] = __('Legend published', NSAAConfig::TEXT_DOMAIN);
            $labels['item_published_privately'] = __('Legend published privately', NSAAConfig::TEXT_DOMAIN);
            $labels['item_reverted_to_draft'] = __('Legend reverted to draft', NSAAConfig::TEXT_DOMAIN);
            $labels['item_scheduled'] = __('Legend scheduled', NSAAConfig::TEXT_DOMAIN);
            $labels['item_updated'] = __('Legend updated', NSAAConfig::TEXT_DOMAIN);
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
            'register_meta_box_cb' => [$this, 'register_meta_box'],
        ];
        register_post_type(self::$POST_TYPE, $args);
    }

    /**
     * Add the meta box to the custom post type
     */
    public function register_meta_box() {
        add_meta_box('information_section', __('Legend Information', NSAAConfig::TEXT_DOMAIN), [$this, 'meta_box'], self::$POST_TYPE, 'advanced', 'high');
    }
    /**
     * Display the meta box
     * 
     * @global type $post - The current post
     */
    public function meta_box() {
        global $post;
        // Nonce field to validate form request from current site.
        wp_nonce_field(self::$META_BOX_DATA, self::$META_BOX_NONCE);
        // Get all the meta data
        $legend_data = self::get_legend_data($post->ID);
        // Get all the departments
        ?>
        <label for="code">
            Legend Code: <small>(required)</small> 
        </label>
        <input type="text" id="code" name="code" value="<?php echo ($legend_data['code']); ?>" class="widefat" required>
        <label for="code">
            Additional Information:
        </label>
        <textarea id="additional" name="additional" class="widefat", placeholder="Optional Information" rows="5"><?php echo ($legend_data['additional']); ?></textarea>

        <?php
    }
    /**
     * Save the meta box data 
     * 
     * @param int $post_id - The post ID
     * @param array $post - The post
     * 
     * @return int - The post ID
     */
    function save_meta($post_id, $post) {
        // Checks save status
        $is_autosave = wp_is_post_autosave($post_id);
        $is_revision = wp_is_post_revision($post_id);
        $is_valid_nonce = ( isset($_POST[self::$META_BOX_NONCE]) && wp_verify_nonce($_POST[self::$META_BOX_NONCE], self::$META_BOX_DATA) ) ? true : false;
        $can_edit = current_user_can('edit_post', $post_id);
        // Exits script depending on save status
        if ($is_autosave || $is_revision || !$is_valid_nonce || !$can_edit) {
            return;
        }
        // Now that we're authenticated, time to save the data.
        // This sanitizes the data from the field and saves it into an array $events_meta.
        $legend_meta = [];
        $legend_meta['code'] = isset($_POST['code']) ? sanitize_text_field($_POST['code']) : '';
        $legend_meta['additional'] = isset($_POST['additional']) ? sanitize_textarea_field($_POST['additional']) : '';
        update_post_meta($post_id, self::$META_DATA_KEY, $legend_meta);

    }

    /**
     * Display the table headers for custom columns in our order
     * 
     * @param array $columns - Array of headers
     * 
     * @return array - Modified array of headers
     */
    function table_head($columns) {
        $newcols = [];
        // Want the selection box and title (name for our custom post type) first.
        $newcols['cb'] = $columns['cb'];
        unset($columns['cb']);
        $newcols['title'] = 'Name';
        unset($columns['title']);
        // Our custom meta data columns.
        $newcols['code'] = __('Legend Code', NSAAConfig::TEXT_DOMAIN);
        $newcols['additional'] = __('Additional Information', NSAAConfig::TEXT_DOMAIN);
        // Want date last.
        unset($columns['date']);
        // Add all other selected columns.
        foreach ($columns as $col => $title) {
            $newcols[$col] = $title;
        }
        // Add the date back.
        $newcols['date'] = 'Date';
        return $newcols;
    }
    /**
     * Display the meta data associated with a post on the administration table
     * 
     * @param string $column_name - The header of the column
     * @param int $post_id - The ID of the post being displayed
     */
    function table_content($column_name, $post_id) {
        $legend_data = self::get_legend_data($post_id, false);
        
        if ('code' === $column_name) {
            echo $legend_data['code'];
        } else if('additional' === $column_name) {
            echo $legend_data['additional'];
        }
    }
}
