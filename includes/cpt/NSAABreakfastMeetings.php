<?php

namespace NSAAOptions;

use NSAAOptions\NSAADebug;
use NSAAOptions\NSAAConfig;

defined('ABSPATH') or die('');

class NSAABreakfastMeetings {
    private static $META_DATA_KEY = '_meta_breakfast_data';
    private static $META_BOX_DATA = 'nsaa_breakfast_save_meta_box_data';
    private static $META_BOX_NONCE = 'nsaa_breakfast_meta_box_nonce';
    private static $POST_TYPE = 'nsaa_breakfast';

    /**
     * Get all the breakfast meeting meta data for the post id
     * 
     * All the data will be sanitized.
     *  
     * @param int $post_id        - The ID of the post
     * 
     * @return array - Associative array with all the meta data
     */
    public static function get_breakfast_data($post_id) {
        $key = self::$META_DATA_KEY;
        $data = get_post_meta($post_id, $key);
        if (count($data) > 0) {
            $data = $data[0];
        } else {
            $data = [];
        }
        $data = shortcode_atts(
            [
                'bdate' => '',
                'group' => '',
            ],
            $data,
            'get_breakfast_data'
        );
        $data['bdate'] = sanitize_text_field($data['bdate']);
        $data['group'] = sanitize_text_field($data['group']);

        return $data;
    }

    public static function getBreakfasts() {
        $args = [
            'post_type' => self::$POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => 9999,
            'order' => 'ASC',
        ];
        $posts = \get_posts($args);

        $breakfast = [];
        foreach($posts as $post) {
            $meta = self::get_breakfast_data($post->ID);
            $breakfast[$post->ID] = $meta;
            $breakfast[$post->ID]['name'] = get_the_title($post->ID);
        }

        usort( $breakfast, [NSAABreakfastMeetings::class, 'sortByTime']);

        return $breakfast;
    }

    /**
     * Sort by date
     * Then by group
     */
    private static function sortByTime( $a, $b ) {

        $date1 = strtotime($a['bdate']);
        $date2 = strtotime($b['bdate']);

        if($date1 === $date2) {
            // Dates are equal sort by group
            $gname1 = get_the_title($a['group']);
            $gname2 = get_the_title($b['group']);
            return ($gname1 <= $gname2) ? -1 : 1;
        }

        return ($date1 <= $date2) ? -1 : 1;

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
        if(!wp_next_scheduled( 'delete_breakfast_schedule')){
            wp_schedule_event(time(), 'daily', 'delete_breakfast_schedule');
        }
        add_action('delete_breakfast_schedule', [$this, 'delete_breakfast']);
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
            'name' => __('Breakfast Meetings', NSAAConfig::TEXT_DOMAIN),
            'singular_name' => __('Breakfast Meeting', NSAAConfig::TEXT_DOMAIN),
            'menu_name' => __('Breakfast Meetings', NSAAConfig::TEXT_DOMAIN),
            'name_admin_bar' => __('Breakfast Meeting', NSAAConfig::TEXT_DOMAIN),
            'add_new' => __('Add New', NSAAConfig::TEXT_DOMAIN),
            'add_new_item' => __("Add New ", NSAAConfig::TEXT_DOMAIN),
            'new_item' => __('New Breakfast Meeting', NSAAConfig::TEXT_DOMAIN),
            'edit_item' => __('Edit Breakfast Meeting', NSAAConfig::TEXT_DOMAIN),
            'view_item' => __('View Breakfast Meetings', NSAAConfig::TEXT_DOMAIN),
            'all_items' => __('All Breakfast Meetings', NSAAConfig::TEXT_DOMAIN),
            'search_items' => __('Search Breakfast Meetings', NSAAConfig::TEXT_DOMAIN),
            'parent_item_colon' => __('Parent Breakfast Meeting:', NSAAConfig::TEXT_DOMAIN),
            'not_found' => __('No Breakfast Meetings found.', NSAAConfig::TEXT_DOMAIN),
            'not_found_in_trash' => __('No Breakfast Meetings found in Trash.', NSAAConfig::TEXT_DOMAIN),
            'archives' => __('Breakfast Meeting archives', NSAAConfig::TEXT_DOMAIN),
            'insert_into_item' => __('Insert into Breakfast Meeting', NSAAConfig::TEXT_DOMAIN),
            'uploaded_to_this_item' => __('Uploaded to this Breakfast Meeting', NSAAConfig::TEXT_DOMAIN),
            'filter_items_list' => __('Filter Breakfast Meetings list', NSAAConfig::TEXT_DOMAIN),
            'items_list_navigation' => __('Breakfast Meeting list navigation', NSAAConfig::TEXT_DOMAIN),
            'items_list' => __('Breakfast Meetings list', NSAAConfig::TEXT_DOMAIN),
        ];
        if (version_compare($wp_version, '5.0', '>=')) {
            $labels['item_published'] = __('Breakfast Meeting published', NSAAConfig::TEXT_DOMAIN);
            $labels['item_published_privately'] = __('Breakfast Meeting published privately', NSAAConfig::TEXT_DOMAIN);
            $labels['item_reverted_to_draft'] = __('Breakfast Meeting reverted to draft', NSAAConfig::TEXT_DOMAIN);
            $labels['item_scheduled'] = __('Breakfast Meeting scheduled', NSAAConfig::TEXT_DOMAIN);
            $labels['item_updated'] = __('Breakfast Meeting updated', NSAAConfig::TEXT_DOMAIN);
        }
        $args = [
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => NSAAConfig::PLUGIN_PAGE,
            'show_in_rest' => true,
            'query_var' => false,
            'rewrite' => ['slug' => 'breakfast', 'with_front' => false],
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
        add_meta_box('information_section', __('Breakfast Meeting Information', NSAAConfig::TEXT_DOMAIN), [$this, 'meta_box'], self::$POST_TYPE, 'advanced', 'high');
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
        $data = self::get_breakfast_data($post->ID);
        // Get all the departments

        $meetings = NSAAMeeting::getMeetings('', false, 'name');

        $select = '<select id="group" name="group" required>';
        foreach($meetings as $meeting) {
            $selected = '';
            if($meeting['id'] == $data['group']) {
                $selected = ' selected';
            }
            $select .= '<option value="' . $meeting['id'] . '"' . $selected . '>' . $meeting['name'] . '</option>';
        }
        $select .= '</select>';


        $timezone = get_option('timezone_string');
        date_default_timezone_set($timezone);
        $bdate = ('' === $data['bdate']) ? 'now' : $data['bdate'];
        $bdate_ts = strtotime($bdate);
        $bdate = date('F Y', $bdate_ts);

        ?>
        <label for="bdate">Breakfast Meeting Date <small>(required)</small>: </label>
        <input type="text" id="bdate" name="bdate" required value="<?php echo ($bdate); ?>" class="widefat month_picker"><br /><br />

        <label for="group">Group <small>(required)</small>: </label>
        <?php echo $select; ?>

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

        $timezone = get_option('timezone_string');
        date_default_timezone_set($timezone);

        // Now that we're authenticated, time to save the data.
        // This sanitizes the data from the field and saves it into an array $events_meta.
        $meta = [];
        $meta['bdate'] = isset($_POST['bdate']) ? sanitize_text_field($_POST['bdate']) : '';
        $bdate_str = strtolower('last day of ' . $meta['bdate']);
        $bdate_ts = strtotime($bdate_str);
        $bdate = date('j F Y', $bdate_ts);
        $meta['bdate'] = $bdate;
        $meta['group'] = isset($_POST['group']) ? sanitize_text_field($_POST['group']) : '';
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
        $newcols['bdate'] = __('Breakfast Meeting Date', NSAAConfig::TEXT_DOMAIN);
        $newcols['group'] = __('Group', NSAAConfig::TEXT_DOMAIN);
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
        $data = self::get_breakfast_data($post_id);
        
        if ('bdate' === $column_name) {
            $timezone = get_option('timezone_string');
            date_default_timezone_set($timezone);
    
            $bdate_ts = strtotime($data['bdate']);
            $bdate = date('F Y', $bdate_ts);
            echo $bdate;
        } else if('group' === $column_name) {
            echo get_the_title($data['group']);
        }
    }

    public function delete_breakfast() {
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
                $meta = self::get_breakfast_data($id);
                $bdate = $meta['bdate'] . ' 23:59:59';
                $breakfast_date = \strtotime($bdate);
                $today = \time();
                if($today > $breakfast_date) {
                    wp_delete_post( $id, true );
                }
                
            }
        }

    }
}
