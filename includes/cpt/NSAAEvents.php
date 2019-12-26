<?php

namespace NSAAOptions;

use NSAAOptions\NSAADebug;
use NSAAOptions\NSAAConfig;

defined('ABSPATH') or die('');

class NSAAEvents {
    private static $META_DATA_KEY = '_meta_event_data';
    private static $META_BOX_DATA = 'nsaa_event_save_meta_box_data';
    private static $META_BOX_NONCE = 'nsaa_event_meta_box_nonce';
    private static $POST_TYPE = 'nsaa_event';

     /**
     * Get all the gratitude meta data for the post id
     * 
     * All the data will be sanitized.
     *  
     * @param int $post_id        - The ID of the post
     * 
     * @return array - Associative array with all the meta data
     */
    public static function get_event_data($post_id) {
        $key = self::$META_DATA_KEY;
        $meta = get_post_meta($post_id, $key);
        if (count($meta) > 0) {
            $meta = $meta[0];
        } else {
            $meta = [];
        }
        $meta = shortcode_atts(
            [
                'sdate' => '',
                'edate' => '',
            ],
            $meta,
            'get_event_data'
        );
        $meta['sdate'] = sanitize_text_field($meta['sdate']);
        $meta['edate'] = sanitize_text_field($meta['edate']);

        return $meta;
    }

    /**
     * Return an array of all the cities in alphabetical order
     */
    public static function getEvents() {

        $args = [
            'post_type' => self::$POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => 9999,
            'order' => 'ASC',
        ];

        $posts = \get_posts($args);

        $events = [];
        foreach($posts as $post) {
            $events[$post->ID] = NSAAEvents::get_event_data($post->ID);
            $events[$post->ID]['title'] = $post->post_title;
            $events[$post->ID]['content'] = get_the_content( null, false, $post->ID );
            $events[$post->ID]['thumbnail'] = get_the_post_thumbnail($post->ID, 'medium', ['class' => 'img.attachment-medium']);
            $events[$post->ID]['thumbnail_url'] = get_the_post_thumbnail_url($post->ID,'full');
        }

        usort( $events, [NSAAEvents::class, 'sortByTime']);

        return $events;

    }

    /**
     * Sort by date
     * Then by group
     * Then by milestone
     */
    private static function sortByTime( $a, $b ) {

        $start1 = strtotime($a['sdate']);
        $start2 = strtotime($b['sdate']);

        if($start1 === $start2) {
            // Start dates are equal sort by end dates
            $end1 = strtotime($a['gtime']);
            $end2 = strtotime($b['gtime']);

            if($end1 === $end2) {
                // End dates are equal sort by name
                $name1 = get_the_title($a['name']);
                $name2 = get_the_title($b['name']);
                return ($name1 <= $name2) ? -1 : 1;
            } else {
                return ($end1 <= $end2) ? -1 : 1;
            }
        }

        return ($start1 <= $start2) ? -1 : 1;

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
        $settings = new NSAASettings();
        $delete_cron = $settings->get_options('auto_delete_events');
        if('1' === $delete_cron) {
            if(!wp_next_scheduled( 'delete_events_schedule')){
                wp_schedule_event(time(), 'daily', 'delete_events_schedule');
            }
            add_action('delete_events_schedule', [$this, 'delete_events']);
        } else {
            wp_clear_scheduled_hook('delete_events_schedule');
            remove_action('delete_events_schedule', [$this, 'delete_events']);
        }
    }
    
    public static function getMetaKey() {
        return self::$META_DATA_KEY;
    }
    public static function getPostType() {
        return self::$POST_TYPE;
    }

    /**
     * Register the custom post type for the class
     */
    public function register_cpt() {
        global $wp_version;
        $labels = [
            'name' => __('Events', NSAAConfig::TEXT_DOMAIN),
            'singular_name' => __('Event', NSAAConfig::TEXT_DOMAIN),
            'menu_name' => __('Events', NSAAConfig::TEXT_DOMAIN),
            'name_admin_bar' => __('Events', NSAAConfig::TEXT_DOMAIN),
            'add_new' => __('Add New', NSAAConfig::TEXT_DOMAIN),
            'add_new_item' => __("Add New ", NSAAConfig::TEXT_DOMAIN),
            'new_item' => __('New Event', NSAAConfig::TEXT_DOMAIN),
            'edit_item' => __('Edit Event', NSAAConfig::TEXT_DOMAIN),
            'view_item' => __('View Events', NSAAConfig::TEXT_DOMAIN),
            'all_items' => __('All Events', NSAAConfig::TEXT_DOMAIN),
            'search_items' => __('Search Events', NSAAConfig::TEXT_DOMAIN),
            'parent_item_colon' => __('Parent Event:', NSAAConfig::TEXT_DOMAIN),
            'not_found' => __('No Events found.', NSAAConfig::TEXT_DOMAIN),
            'not_found_in_trash' => __('No Events found in Trash.', NSAAConfig::TEXT_DOMAIN),
            'archives' => __('Event archives', NSAAConfig::TEXT_DOMAIN),
            'insert_into_item' => __('Insert into Event', NSAAConfig::TEXT_DOMAIN),
            'uploaded_to_this_item' => __('Uploaded to this Event', NSAAConfig::TEXT_DOMAIN),
            'filter_items_list' => __('Filter Events list', NSAAConfig::TEXT_DOMAIN),
            'items_list_navigation' => __('Event list navigation', NSAAConfig::TEXT_DOMAIN),
            'items_list' => __('Event list', NSAAConfig::TEXT_DOMAIN),
        ];
        if (version_compare($wp_version, '5.0', '>=')) {
            $labels['item_published'] = __('Event published', NSAAConfig::TEXT_DOMAIN);
            $labels['item_published_privately'] = __('Event published privately', NSAAConfig::TEXT_DOMAIN);
            $labels['item_reverted_to_draft'] = __('Event reverted to draft', NSAAConfig::TEXT_DOMAIN);
            $labels['item_scheduled'] = __('Event scheduled', NSAAConfig::TEXT_DOMAIN);
            $labels['item_updated'] = __('Event updated', NSAAConfig::TEXT_DOMAIN);
        }
        $args = [
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => NSAAConfig::PLUGIN_PAGE,
            'show_in_rest' => false,
            'query_var' => false,
            'rewrite' => ['slug' => 'event', 'with_front' => false],
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 200,
            'menu_icon' => 'dashicons-groups',
            'supports' => ['title', 'editor', 'thumbnail'],
            'register_meta_box_cb' => [$this, 'register_meta_box'],
        ];
        register_post_type(self::$POST_TYPE, $args);
    }

    /**
     * Add the meta box to the custom post type
     */
    public function register_meta_box() {
        add_meta_box('information_section', __('Event Information', NSAAConfig::TEXT_DOMAIN), [$this, 'meta_box'], self::$POST_TYPE, 'advanced', 'high');
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
        $meta = self::get_event_data($post->ID);

        ?>
        <label for="sdate">Start Date <small>(required)</small>: </label>
        <input type="text" id="sdate" name="sdate" required value="<?php echo ($meta['sdate']); ?>" class="widefat date_picker"><br /><br />

        <label for="edate">End Date <small>(required)</small>: </label>
        <input type="text" id="edate" name="edate" required value="<?php echo ($meta['edate']); ?>" class="widefat date_picker"><br /><br />

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
        $meta = [];
        $meta['sdate'] = isset($_POST['sdate']) ? sanitize_text_field($_POST['sdate']) : '';
        $meta['edate'] = isset($_POST['edate']) ? sanitize_text_field($_POST['edate']) : '';
        update_post_meta($post_id, self::$META_DATA_KEY, $meta);
    }

    /**
     * Display the table headers for custom columns in our order
     * 
     * @param array $columns - Array of headers
     * 
     * @return array - Modified array of headers
     */
    public function table_head($columns) {
        $newcols = [];
        // Want the selection box and title (name for our custom post type) first.
        $newcols['cb'] = $columns['cb'];
        unset($columns['cb']);
        $newcols['title'] = 'Name';
        unset($columns['title']);
        // Our custom meta data columns.
        $newcols['sdate'] = __('Start Date', NSAAConfig::TEXT_DOMAIN);
        $newcols['edate'] = __('End Date', NSAAConfig::TEXT_DOMAIN);
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
    public function table_content($column_name, $post_id) {
        $meta = self::get_event_data($post_id);
        
        if ('sdate' === $column_name) {
            echo $meta['sdate'];
        } else if('edate' === $column_name) {
            echo $meta['edate'];
        }
    }

    public function delete_events() {
        global $wpdb;

        $timezone = get_option('timezone_string');
        date_default_timezone_set($timezone);

        $post_type = self::$POST_TYPE;
        $query = "SELECT ID FROM $wpdb->posts WHERE post_type='$post_type' AND post_status='publish' ORDER BY post_modified DESC";
        $results = $wpdb->get_results($query);

        # Check if there are any results
        if(isset($results)) {
            foreach($results as $post) {
                $id = $post->ID;
                $meta = self::get_event_data($id);
                if(isset($meta['edate'])) {
                    $edate = $meta['edate'] . ' 23:59:59';
                    $end_date = \strtotime($edate);
                    $today = \time();
                    if($today > $end_date) {
                        wp_delete_post( $id, true );
                    }
                }
            }
        }

    }

}
