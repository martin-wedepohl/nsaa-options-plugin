<?php

namespace NSAAOptions;

use NSAAOptions\NSAADebug;
use NSAAOptions\NSAAConfig;

defined('ABSPATH') or die('');

class NSAAGratitude {
    private static $META_DATA_KEY = '_meta_gratitude_data';
    private static $META_BOX_DATA = 'nsaa_gratitude_save_meta_box_data';
    private static $META_BOX_NONCE = 'nsaa_gratitude_meta_box_nonce';
    private static $POST_TYPE = 'nsaa_gratitude';

    /**
     * Get all the gratitude meta data for the post id
     * 
     * All the data will be sanitized.
     *  
     * @param int $post_id        - The ID of the post
     * 
     * @return array - Associative array with all the meta data
     */
    public static function get_gratitude_data($post_id) {
        $key = self::$META_DATA_KEY;
        $meta = get_post_meta($post_id, $key);
        if (count($meta) > 0) {
            $meta = $meta[0];
        } else {
            $meta = [];
        }
        $meta = shortcode_atts(
            [
                'gdate' => '',
                'gtime' => '',
                'additional' => '',
                'group' => '',
            ],
            $meta,
            'get_gratitude_data'
        );
        $meta['gdate'] = sanitize_text_field($meta['gdate']);
        $meta['gtime'] = sanitize_text_field($meta['gtime']);
        $meta['additional'] = sanitize_text_field($meta['additional']);
        $meta['group'] = sanitize_text_field($meta['group']);

        return $meta;
    }

    public static function getGratitudes() {
        $args = [
            'post_type' => self::$POST_TYPE,
            'post_status' => 'publish',
            'posts_per_page' => 9999,
            'order' => 'ASC',
        ];
        $posts = \get_posts($args);

        $gratitudes = [];
        foreach($posts as $post) {
            $meta = self::get_gratitude_data($post->ID);
            $gratitudes[$post->ID] = $meta;
            $gratitudes[$post->ID]['name'] = get_the_title($post->ID);
            $gratitudes[$post->ID]['thumbnail'] = get_the_post_thumbnail($post->ID, 'medium', ['class' => 'img.attachment-medium']);
            $gratitudes[$post->ID]['thumbnail_url'] = get_the_post_thumbnail_url($post->ID,'full');
        }

        usort( $gratitudes, [NSAAGratitude::class, 'sortByTime']);

        return $gratitudes;
    }

    /**
     * Sort by date
     * Then by group
     * Then by milestone
     */
    private static function sortByTime( $a, $b ) {

        $date1 = strtotime($a['gdate']);
        $date2 = strtotime($b['gdate']);

        if($date1 === $date2) {
            // Dates are equal sort by time
            $time1 = strtotime($a['gtime']);
            $time2 = strtotime($b['gtime']);

            if($time1 === $time2) {
                // Times are equal sort by group
                $gname1 = get_the_title($a['group']);
                $gname2 = get_the_title($b['group']);
                return ($gname1 <= $gname2) ? -1 : 1;
            } else {
                return ($time1 <= $time2) ? -1 : 1;
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
        if(!wp_next_scheduled( 'delete_gratitudes_schedule')){
            wp_schedule_event(time(), 'hourly', 'delete_gratitudes_schedule');
        }
        add_action('delete_gratitudes_schedule', [$this, 'delete_gratitudes']);
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
            'name' => __('Gratitudes', NSAAConfig::TEXT_DOMAIN),
            'singular_name' => __('Gratitude', NSAAConfig::TEXT_DOMAIN),
            'menu_name' => __('Gratitudes', NSAAConfig::TEXT_DOMAIN),
            'name_admin_bar' => __('Gratitudes', NSAAConfig::TEXT_DOMAIN),
            'add_new' => __('Add New', NSAAConfig::TEXT_DOMAIN),
            'add_new_item' => __("Add New ", NSAAConfig::TEXT_DOMAIN),
            'new_item' => __('New Gratitude', NSAAConfig::TEXT_DOMAIN),
            'edit_item' => __('Edit Gratitude', NSAAConfig::TEXT_DOMAIN),
            'view_item' => __('View Gratitudes', NSAAConfig::TEXT_DOMAIN),
            'all_items' => __('All Gratitudes', NSAAConfig::TEXT_DOMAIN),
            'search_items' => __('Search Gratitudes', NSAAConfig::TEXT_DOMAIN),
            'parent_item_colon' => __('Parent Gratitude:', NSAAConfig::TEXT_DOMAIN),
            'not_found' => __('No Gratitudes found.', NSAAConfig::TEXT_DOMAIN),
            'not_found_in_trash' => __('No Gratitudes found in Trash.', NSAAConfig::TEXT_DOMAIN),
            'archives' => __('Gratitude archives', NSAAConfig::TEXT_DOMAIN),
            'insert_into_item' => __('Insert into Gratitude', NSAAConfig::TEXT_DOMAIN),
            'uploaded_to_this_item' => __('Uploaded to this Gratitude', NSAAConfig::TEXT_DOMAIN),
            'filter_items_list' => __('Filter Gratitudes list', NSAAConfig::TEXT_DOMAIN),
            'items_list_navigation' => __('Gratitude list navigation', NSAAConfig::TEXT_DOMAIN),
            'items_list' => __('Gratitude list', NSAAConfig::TEXT_DOMAIN),
        ];
        if (version_compare($wp_version, '5.0', '>=')) {
            $labels['item_published'] = __('Gratitude published', NSAAConfig::TEXT_DOMAIN);
            $labels['item_published_privately'] = __('Gratitude published privately', NSAAConfig::TEXT_DOMAIN);
            $labels['item_reverted_to_draft'] = __('Gratitude reverted to draft', NSAAConfig::TEXT_DOMAIN);
            $labels['item_scheduled'] = __('Gratitude scheduled', NSAAConfig::TEXT_DOMAIN);
            $labels['item_updated'] = __('Gratitude updated', NSAAConfig::TEXT_DOMAIN);
        }
        $args = [
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => NSAAConfig::PLUGIN_PAGE,
            'show_in_rest' => true,
            'query_var' => false,
            'rewrite' => ['slug' => 'gratitude', 'with_front' => false],
            'capability_type' => 'post',
            'has_archive' => false,
            'hierarchical' => false,
            'menu_position' => 200,
            'menu_icon' => 'dashicons-groups',
            'supports' => ['title', 'thumbnail'],
            'register_meta_box_cb' => [$this, 'register_meta_box'],
        ];
        register_post_type(self::$POST_TYPE, $args);
    }
    /**
     * Add the meta box to the custom post type
     */
    public function register_meta_box() {
        add_meta_box('information_section', __('Gratitude Information', NSAAConfig::TEXT_DOMAIN), [$this, 'meta_box'], self::$POST_TYPE, 'advanced', 'high');
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
        $meta = self::get_gratitude_data($post->ID);
        // Get all the departments

        $meetings = NSAAMeeting::getMeetings();

        $select = '<select id="group" name="group" required>';
        foreach($meetings as $meeting) {
            $selected = '';
            if($meeting['id'] == $meta['group']) {
                $selected = ' selected';
            }
            $select .= '<option value="' . $meeting['id'] . '"' . $selected . '>' . $meeting['name'] . '</option>';
        }
        $select .= '</select>';

        ?>
        <label for="gdate">Gratitude Date <small>(required)</small>: </label>
        <input type="text" id="gdate" name="gdate" required value="<?php echo ($meta['gdate']); ?>" class="widefat date_picker"><br /><br />

        <label for="gtime">Gratitude Time <small>(required)</small>: </label>
        <input type="text" id="gtime" name="gtime" required value="<?php echo ($meta['gtime']); ?>" class="widefat time_picker" placeholder="Enter time for the gratitude meeting" /><br /><br />

        <label for="additional">Additional Information: </label>
        <input type="text" id="additional" name="additional" value="<?php echo ($meta['additional']); ?>" class="widefat" placeholder="Enter additional information for gratitude meeting - Before/During/After Meeting" /><br /><br />

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
        $meta = [];
        $meta['gdate'] = isset($_POST['gdate']) ? sanitize_text_field($_POST['gdate']) : '';
        $meta['gtime'] = isset($_POST['gtime']) ? sanitize_text_field($_POST['gtime']) : '';
        $meta['additional'] = isset($_POST['additional']) ? sanitize_text_field($_POST['additional']) : '';
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
        $newcols['gdate'] = __('Gratitude Date', NSAAConfig::TEXT_DOMAIN);
        $newcols['gtime'] = __('Gratitude Time', NSAAConfig::TEXT_DOMAIN);
        $newcols['additional'] = __('Additional', NSAAConfig::TEXT_DOMAIN);
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
        $meta = self::get_gratitude_data($post_id);
        
        if ('gdate' === $column_name) {
            echo $meta['gdate'];
        } else if('gtime' === $column_name) {
            echo $meta['gtime'];
        } else if('additional' === $column_name) {
            echo $meta['additional'];
        } else if('group' === $column_name) {
            echo get_the_title($meta['group']);
        }
    }

    public function delete_gratitudes() {
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
                $meta = self::get_gratitude_data($id);
                $date = $meta['date'] . ' 23:59:59';
                $gratitude_date = \strtotime($date);
                $today = \time();
                if($today > $gratitude_date) {
                    wp_delete_post( $id, true );
                }
                
            }
        }

    }
}
