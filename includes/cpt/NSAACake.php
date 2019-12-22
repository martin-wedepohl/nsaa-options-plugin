<?php

namespace NSAAOptions;

use NSAAOptions\NSAADebug;
use NSAAOptions\NSAAConfig;

defined('ABSPATH') or die('');

class NSAACake {
    private static $META_DATA_KEY = '_meta_cakes_data';
    private static $META_BOX_DATA = 'nsaa_cakes_save_meta_box_data';
    private static $META_BOX_NONCE = 'nsaa_cakes_meta_box_nonce';
    private static $POST_TYPE = 'nsaa_cakes';

    /**
     * Get all the cake meta data for the post id
     * 
     * All the data will be sanitized.
     *  
     * @param int $post_id        - The ID of the post
     * 
     * @return array - Associative array with all the meta data
     */
    public static function get_cake_data($post_id) {
        $key = self::$META_DATA_KEY;
        $cake_data = get_post_meta($post_id, $key);
        if (count($cake_data) > 0) {
            $cake_data = $cake_data[0];
        } else {
            $cake_data = [];
        }
        $cake_data = shortcode_atts(
            [
                'cdate' => '',
                'milestone' => '',
                'group' => '',
            ],
            $cake_data,
            'get_cake_data'
        );
        $cake_data['cdate'] = sanitize_text_field($cake_data['cdate']);
        $cake_data['milestone'] = sanitize_text_field($cake_data['milestone']);
        $cake_data['group'] = sanitize_text_field($cake_data['group']);

        return $cake_data;
    }

    public static function getCakes() {
        $args = [
            'post_type' => self::$POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => 9999,
            'order' => 'ASC',
        ];
        $posts = \get_posts($args);

        $cakes = [];
        foreach($posts as $post) {
            $meta = self::get_cake_data($post->ID);
            $cakes[$post->ID] = $meta;
            $cakes[$post->ID]['name'] = get_the_title($post->ID);
        }

        usort( $cakes, [NSAACake::class, 'sortByTime']);

        return $cakes;
    }

    /**
     * Sort by date
     * Then by group
     * Then by milestone
     */
    private static function sortByTime( $a, $b ) {

        $date1 = strtotime($a['cdate']);
        $date2 = strtotime($b['cdate']);

        if($date1 === $date2) {
            // Dates are equal sort by group
            $gname1 = get_the_title($a['group']);
            $gname2 = get_the_title($b['group']);
            if($gname1 === $gname2) {
                // Groups are equal sort by milestone
                return ($a['milestone'] <= $b['milestone']) ? -1 : 1;
            } else {
                return ($gname1 <= $gname2) ? -1 : 1;
            }
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
        if(!wp_next_scheduled( 'delete_cakes_schedule')){
            wp_schedule_event(time(), 'daily', 'delete_cakes_schedule');
        }
        add_action('delete_cakes_schedule', [$this, 'delete_cakes']);
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
            'name' => __('Cakes', NSAAConfig::TEXT_DOMAIN),
            'singular_name' => __('Cake', NSAAConfig::TEXT_DOMAIN),
            'menu_name' => __('Cakes', NSAAConfig::TEXT_DOMAIN),
            'name_admin_bar' => __('Cakes', NSAAConfig::TEXT_DOMAIN),
            'add_new' => __('Add New', NSAAConfig::TEXT_DOMAIN),
            'add_new_item' => __("Add New ", NSAAConfig::TEXT_DOMAIN),
            'new_item' => __('New Cake', NSAAConfig::TEXT_DOMAIN),
            'edit_item' => __('Edit Cake', NSAAConfig::TEXT_DOMAIN),
            'view_item' => __('View Cakes', NSAAConfig::TEXT_DOMAIN),
            'all_items' => __('All Cakes', NSAAConfig::TEXT_DOMAIN),
            'search_items' => __('Search Cakes', NSAAConfig::TEXT_DOMAIN),
            'parent_item_colon' => __('Parent Cake:', NSAAConfig::TEXT_DOMAIN),
            'not_found' => __('No Cakes found.', NSAAConfig::TEXT_DOMAIN),
            'not_found_in_trash' => __('No Cakes found in Trash.', NSAAConfig::TEXT_DOMAIN),
            'archives' => __('Cake archives', NSAAConfig::TEXT_DOMAIN),
            'insert_into_item' => __('Insert into Cake', NSAAConfig::TEXT_DOMAIN),
            'uploaded_to_this_item' => __('Uploaded to this Cake', NSAAConfig::TEXT_DOMAIN),
            'filter_items_list' => __('Filter Cakes list', NSAAConfig::TEXT_DOMAIN),
            'items_list_navigation' => __('Cake list navigation', NSAAConfig::TEXT_DOMAIN),
            'items_list' => __('Cake list', NSAAConfig::TEXT_DOMAIN),
        ];
        if (version_compare($wp_version, '5.0', '>=')) {
            $labels['item_published'] = __('Cake published', NSAAConfig::TEXT_DOMAIN);
            $labels['item_published_privately'] = __('Cake published privately', NSAAConfig::TEXT_DOMAIN);
            $labels['item_reverted_to_draft'] = __('Cake reverted to draft', NSAAConfig::TEXT_DOMAIN);
            $labels['item_scheduled'] = __('Cake scheduled', NSAAConfig::TEXT_DOMAIN);
            $labels['item_updated'] = __('Cake updated', NSAAConfig::TEXT_DOMAIN);
        }
        $args = [
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => NSAAConfig::PLUGIN_PAGE,
            'show_in_rest' => true,
            'query_var' => false,
            'rewrite' => ['slug' => 'cake', 'with_front' => false],
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
        add_meta_box('information_section', __('Cake Information', NSAAConfig::TEXT_DOMAIN), [$this, 'meta_box'], self::$POST_TYPE, 'advanced', 'high');
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
        $cake_data = self::get_cake_data($post->ID);
        // Get all the departments

        $meetings = NSAAMeeting::getMeetings('', false, 'name');

        $select = '<select id="group" name="group" required>';
        foreach($meetings as $meeting) {
            $selected = '';
            if($meeting['id'] == $cake_data['group']) {
                $selected = ' selected';
            }
            $select .= '<option value="' . $meeting['id'] . '"' . $selected . '>' . $meeting['name'] . '</option>';
        }
        $select .= '</select>';

        ?>
        <label for="cdate">Cake Date <small>(required)</small>: </label>
        <input type="text" id="cdate" name="cdate" required value="<?php echo ($cake_data['cdate']); ?>" class="widefat date_picker"><br /><br />

        <label for="milestone">Milestone <small>(required)</small>: </label>
        <input type="number" id="milestone" name="milestone" required min="1" max="100" step="1" value="<?php echo ($cake_data['milestone']); ?>" class="widefat"><br /><br />

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
        // Now that we're authenticated, time to save the data.
        // This sanitizes the data from the field and saves it into an array $events_meta.
        $cake_meta = [];
        $cake_meta['cdate'] = isset($_POST['cdate']) ? sanitize_text_field($_POST['cdate']) : '';
        $cake_meta['milestone'] = isset($_POST['milestone']) ? sanitize_text_field($_POST['milestone']) : '';
        $cake_meta['group'] = isset($_POST['group']) ? sanitize_text_field($_POST['group']) : '';
        update_post_meta($post_id, self::$META_DATA_KEY, $cake_meta);
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
        $newcols['cdate'] = __('Cake Date', NSAAConfig::TEXT_DOMAIN);
        $newcols['milestone'] = __('Milestone', NSAAConfig::TEXT_DOMAIN);
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
        $cake_data = self::get_cake_data($post_id);
        
        if ('cdate' === $column_name) {
            echo $cake_data['cdate'];
        } else if('milestone' === $column_name) {
            echo $cake_data['milestone'];
        } else if('group' === $column_name) {
            echo get_the_title($cake_data['group']);
        }
    }

    public function delete_cakes() {
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
                $meta = self::get_cake_data($id);
                $cdate = $meta['cdate'] . ' 23:59:59';
                $cake_date = \strtotime($cdate);
                $today = \time();
                if($today > $cake_date) {
                    wp_delete_post( $id, true );
                }
                
            }
        }

    }
}
