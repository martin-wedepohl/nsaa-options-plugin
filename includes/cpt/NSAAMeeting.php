<?php

namespace NSAAOptions;

use NSAAOptions\NSAACity;
use NSAAOptions\NSAADebug;
use NSAAOptions\NSAAConfig;
use NSAAOptions\NSAALegend;

defined('ABSPATH') or die('');

class NSAAMeeting {
    private static $META_DATA_KEY = '_meta_meetings_data';
    private static $META_BOX_DATA = 'nsaa_meetings_save_meta_box_data';
    private static $META_BOX_NONCE = 'nsaa_meetings_meta_box_nonce';
    private static $POST_TYPE = 'nsaa_meetings';

    private static $_cities;
    private static $_legends;
    
    public static function findIdInArray($needle, $haystack) {

        foreach ($haystack as $value) {
            if ($needle === $value) {
                return true;
            }
        }

        return false;

    }

    /**
     * Get all the meeting meta data for the post id
     * 
     * All the data will be sanitized.
     *  
     * @param int $post_id The ID of the post
     * 
     * @return array - Associative array with all the meta data
     */
    public static function get_meeting_data($post_id) {
        $meeting_data = get_post_meta($post_id, self::$META_DATA_KEY);
        if (count($meeting_data) > 0) {
            $meeting_data = $meeting_data[0];
        } else {
            $meeting_data = [];
        }
        $meeting_data = shortcode_atts(
            [
                'address' => '',
                'location' => '',
                'city' => '',
                'time' => '',
                'monthly' => '',
                'zoom' => '',
                'zoompw' => '',
                'zoomlink' => '',
                'zoomandinperson' => '',
                'additional' => '',
                'dow' => [],
                'legend' => [],
                'notheld' => [],
            ],
            $meeting_data,
            'get_meeting_data'
        );
        $meeting_data['address'] = sanitize_text_field($meeting_data['address']);
        $meeting_data['location'] = sanitize_text_field($meeting_data['location']);
        $meeting_data['city'] = sanitize_text_field($meeting_data['city']);
        $meeting_data['time'] = sanitize_text_field($meeting_data['time']);
        $meeting_data['monthly'] = sanitize_text_field($meeting_data['monthly']);
        $meeting_data['zoom'] = sanitize_text_field($meeting_data['zoom']);
        $meeting_data['zoompw'] = sanitize_text_field($meeting_data['zoompw']);
        $meeting_data['zoomlink'] = sanitize_text_field($meeting_data['zoomlink']);
        $meeting_data['zoomandinperson'] = sanitize_text_field($meeting_data['zoomandinperson']);
        $meeting_data['additional'] = sanitize_textarea_field($meeting_data['additional']);
        if (count($meeting_data['dow']) > 0) {
            foreach ($meeting_data['dow'] as $id => $dow) {
                $meeting_data['dow'][$id] = sanitize_text_field($dow);
            }
        }
        if (count($meeting_data['legend']) > 0) {
            foreach ($meeting_data['legend'] as $id => $legend) {
                $meeting_data['legend'][$id] = sanitize_text_field($legend);
            }
        }
        if (count($meeting_data['notheld']) > 0) {
            foreach ($meeting_data['notheld'] as $id => $notheld) {
                $meeting_data['notheld'][$id] = sanitize_text_field($notheld);
            }
        }

        return $meeting_data;
    }

    public static function getMeetings($dow = '', $includeservice = false, $sortmode = 'city') {
        global $wpdb;
        $post_type = self::$POST_TYPE;
        $query = $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type=%s AND post_status='publish'", $post_type);
        $posts = $wpdb->get_results($query);

        $legendId = NSAALegend::getServiceLegend();

        $meetings = [];
        // Check if there are any posts
        if (isset($posts)) {
            foreach ($posts as $post) {
                $id = $post->ID;
                $meta = self::get_meeting_data($id);
                if ('' === $dow) {
                    if (true === NSAAMeeting::findIdInArray($legendId, $meta['legend']) && false === $includeservice) {
                        continue;
                    }
                    $meetings[$id] = $meta;
                    $meetings[$id]['name'] = get_the_title($id);
                    $meetings[$id]['id'] = $id;
                } else {
                    if (in_array($dow, $meta['dow'])) {
                        if (true === NSAAMeeting::findIdInArray($legendId, $meta['legend']) && false === $includeservice) {
                            continue;
                        }
                        $meetings[$id] = $meta;
                        $meetings[$id]['name'] = get_the_title($id);
                        $meetings[$id]['id'] = $id;
                    }
                }
            }
        }

        switch($sortmode) {
            case 'city':
                $thesort = 'sortByCity';
            break;
            case 'name':
                $thesort = 'sortByName';
            break;
            default:
                $thesort = 'sortByTime';
        }
        usort( $meetings, [NSAAMeeting::class, $thesort]);

        return $meetings;
    }

    public static function getServiceMeetings($name = '') {
        global $wpdb;
        $post_type = self::$POST_TYPE;
        $query = $wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE post_type=%s AND post_status='publish'", $post_type);
        $posts = $wpdb->get_results($query);

        $legendId = NSAALegend::getServiceLegend();

        $meetings = [];
        // Check if there are any posts
        if (isset($posts)) {
            foreach ($posts as $post) {
                $id = $post->ID;
                $meta = self::get_meeting_data($id);
                if (true === NSAAMeeting::findIdInArray($legendId, $meta['legend'])) {
                    if ('' !== $name) {
                        if ($name === get_the_title($id)) {
                            $meetings[$id] = $meta;
                            $meetings[$id]['name'] = get_the_title($id);
                            $meetings[$id]['id'] = $id;
                        }
                    } else {
                        $meetings[$id] = $meta;
                        $meetings[$id]['name'] = get_the_title($id);
                        $meetings[$id]['id'] = $id;
                    }
                }
            }
        }

        usort($meetings, [NSAAMeeting::class, 'sortByCity']);

        return $meetings;
    }

    /**
     * Sort by name
     * Then by time
     * Then by city
     */
    private static function sortByname( $a, $b ) {

        if ($a['name'] === $b['name']) {
            // Names are equal sort by time
            $time1 = strtotime($a['time']);
            $time2 = strtotime($b['time']);
            if ($time1 === $time2) {
                // Times are equal sort by city
                return ($a['city'] <= $b['city']) ? -1 : 1;
            } else {
                return ($time1 <= $time2) ? -1 : 1;
            }
        }

        return ($a['name'] <= $b['name']) ? -1 : 1;

    }

    /**
     * Sort by city
     * Then by time
     * Then by name
     */
    private static function sortByCity( $a, $b ) {

        if ($a['city'] === $b['city']) {
            // Cities are equal sort by time
            $time1 = strtotime($a['time']);
            $time2 = strtotime($b['time']);
            if ($time1 === $time2) {
                // Times are equal sort by name
                return ($a['name'] <= $b['name']) ? -1 : 1;
            } else {
                return ($time1 <= $time2) ? -1 : 1;
            }
        }

        return ($a['city'] <= $b['city']) ? -1 : 1;

    }

    /**
     * Sort by time
     * Then by city
     * Then by name
     */
    private static function sortByTime( $a, $b ) {

        $time1 = strtotime($a['time']);
        $time2 = strtotime($b['time']);

        if ($time1 === $time2) {
            // Times are equal sort by city
            if ($a['city'] === $b['city']) {
                // Cities are equal sort by name
                return ($a['name'] <= $b['name']) ? -1 : 1;
            } else {
                return ($a['city'] <= $b['city']) ? -1 : 1;
            }
        }

        return ($time1 <= $time2) ? -1 : 1;

    }

    public static function getDOW($dow, $short = false) {

        $dow_array = [
            0 => ((true === $short) ? 'Sun' : 'Sunday'),
            1 => ((true === $short) ? 'Mon' : 'Monday'),
            2 => ((true === $short) ? 'Tue' : 'Tuesday'),
            3 => ((true === $short) ? 'Wed' : 'Wednesday'),
            4 => ((true === $short) ? 'Thu' : 'Thursday'),
            5 => ((true === $short) ? 'Fri' : 'Friday'),
            6 => ((true === $short) ? 'Sat' : 'Saturday')
        ];

        if(true === $short) {
            $delimiter = ',';
            $spaces = -1;
        } else {
            $delimiter = ', ';
            $spaces = -2;
        }

        if (1 === count($dow)) {
            $day = (isset($dow_array[$dow[0]]) ? $dow_array[$dow[0]] : '');
        } else {
            $day = '';
            foreach ($dow as $id => $the_day) {
                $day .= (isset($dow_array[$the_day]) ? ($dow_array[$the_day] . $delimiter) : '');
            }
            if ('' !== $day) {
                $day = substr($day, 0, $spaces);
            }
        }

        return $day;

    }

    /**
     * Class constructor 
     * 
     * Performs all the initialization for the class
     */
    public function __construct() {
        add_action('init', [$this, 'register_cpt']);
        add_action('save_post', [$this, 'save_meta'], 1, 2);
        add_filter('single_template', [$this, 'load_template']);
        add_filter('archive_template', [$this, 'load_archive']);
        add_filter('manage_edit-' . self::$POST_TYPE . '_columns', [$this, 'table_head']);
        add_action('manage_' . self::$POST_TYPE . '_posts_custom_column', [$this, 'table_content'], 10, 2);
        add_action('admin_notices', [$this, '_meeting_admin_notices']);

        $city = new NSAACity();
        self::$_cities = [];

        $legend = new NSAALegend();
        self::$_legends = [];

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
            'name' => __('Meetings', NSAAConfig::TEXT_DOMAIN),
            'singular_name' => __('Meeting', NSAAConfig::TEXT_DOMAIN),
            'menu_name' => __('Meetings', NSAAConfig::TEXT_DOMAIN),
            'name_admin_bar' => __('Meetings', NSAAConfig::TEXT_DOMAIN),
            'add_new' => __('Add New', NSAAConfig::TEXT_DOMAIN),
            'add_new_item' => __("Add New ", NSAAConfig::TEXT_DOMAIN),
            'new_item' => __('New Meeting', NSAAConfig::TEXT_DOMAIN),
            'edit_item' => __('Edit Meeting', NSAAConfig::TEXT_DOMAIN),
            'view_item' => __('View Meetings', NSAAConfig::TEXT_DOMAIN),
            'all_items' => __('All Meetings', NSAAConfig::TEXT_DOMAIN),
            'search_items' => __('Search Meetings', NSAAConfig::TEXT_DOMAIN),
            'parent_item_colon' => __('Parent Meeting:', NSAAConfig::TEXT_DOMAIN),
            'not_found' => __('No Meetings found.', NSAAConfig::TEXT_DOMAIN),
            'not_found_in_trash' => __('No Meetings found in Trash.', NSAAConfig::TEXT_DOMAIN),
            'archives' => __('Meeting archives', NSAAConfig::TEXT_DOMAIN),
            'insert_into_item' => __('Insert into Meeting', NSAAConfig::TEXT_DOMAIN),
            'uploaded_to_this_item' => __('Uploaded to this Meeting', NSAAConfig::TEXT_DOMAIN),
            'filter_items_list' => __('Filter Meetings list', NSAAConfig::TEXT_DOMAIN),
            'items_list_navigation' => __('Meeting list navigation', NSAAConfig::TEXT_DOMAIN),
            'items_list' => __('Meeting list', NSAAConfig::TEXT_DOMAIN),
        ];
        if (version_compare($wp_version, '5.0', '>=')) {
            $labels['item_published'] = __('Meeting published', NSAAConfig::TEXT_DOMAIN);
            $labels['item_published_privately'] = __('Meeting published privately', NSAAConfig::TEXT_DOMAIN);
            $labels['item_reverted_to_draft'] = __('Meeting reverted to draft', NSAAConfig::TEXT_DOMAIN);
            $labels['item_scheduled'] = __('Meeting scheduled', NSAAConfig::TEXT_DOMAIN);
            $labels['item_updated'] = __('Meeting updated', NSAAConfig::TEXT_DOMAIN);
        }
        $args = [
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => NSAAConfig::PLUGIN_PAGE,
            'show_in_rest' => false,
            'query_var' => false,
            'rewrite' => ['slug' => 'meeting', 'with_front' => false],
            'capability_type' => 'post',
            'has_archive' => true,
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
        add_meta_box('information_section', __('Meeting Information', NSAAConfig::TEXT_DOMAIN), [$this, 'meta_box'], self::$POST_TYPE, 'advanced', 'high');
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
        $meeting_data = self::get_meeting_data($post->ID);
        // Get all the departments
        ?>
        <label for="location">Location: </label>
        <input type="text" id="location" name="location" value="<?php echo ($meeting_data['location']); ?>" class="widefat", placeholder='Church/AlAno Club/Etc.'><br /><br />

        <label for="address">Street Address: </label>
        <input type="text" id="address" name="address" value="<?php echo ($meeting_data['address']); ?>" class="widefat", placeholder='Street Address'><br /><br />

        <label for="city">City: </label>
        <?php
        self::$_cities = NSAACity::getCities();
        $html = '<select name="city" id="city" class="widefat"><option value="-1">Select a City</option>';
        foreach (self::$_cities as $id => $city) {
            $selected = '';
            if ($meeting_data['city'] === $id) {
                $selected = 'selected';
            }
            $html .= '<option value="' . $id . '" ' . $selected .'>' . $city . '</option>';
        }
        $html .= '</select>';
        echo $html;
        ?>
        <br /><br />

        <?php
            $zoom = $meeting_data['zoom'];
            // Format Zoom ID with 3 digits and a space
            $zoom = substr($zoom, 0, 3) . ' ' . substr($zoom, 3, 3) . ' ' . substr($zoom, 6);
        ?>
        <label for="zoom">Zoom Meeting ID: </label>
        <input type="text" id="zoom" name="zoom" value="<?php echo ($zoom); ?>" class="widefat", placeholder='Zoom ID'><br /><br />
        <label for="zoom">Zoom Meeting Password: </label>
        <input type="text" id="zoompw" name="zoompw" value="<?php echo ($meeting_data['zoompw']); ?>" class="widefat", placeholder='Zoom Password'><br /><br />
        <label for="zoom">Zoom Meeting Link: </label>
        <input type="text" id="zoomlink" name="zoomlink" value="<?php echo ($meeting_data['zoomlink']); ?>" class="widefat", placeholder='Zoom Link'><br /><br />
        <label for="zoomandinperson">Both Zoom and In Person Meeting: </label><br />
        <input type="checkbox" id="zoomandinperson" name="zoomandinperson" value="1" <?php echo (('1' === $meeting_data['zoomandinperson']) ? 'checked' : ''); ?>> Both Running?<br /><br />

        <label for="dow">Meeting Day <small>(required)</small>: </label><br />
        <input type="checkbox" id="dow0" name="dow[]" value="0" <?php echo (in_array('0', $meeting_data['dow']) ? 'checked' : ''); ?>> Sun<br />
        <input type="checkbox" id="dow1" name="dow[]" value="1" <?php echo (in_array('1', $meeting_data['dow']) ? 'checked' : ''); ?>> Mon<br />
        <input type="checkbox" id="dow2" name="dow[]" value="2" <?php echo (in_array('2', $meeting_data['dow']) ? 'checked' : ''); ?>> Tue<br />
        <input type="checkbox" id="dow3" name="dow[]" value="3" <?php echo (in_array('3', $meeting_data['dow']) ? 'checked' : ''); ?>> Wed<br />
        <input type="checkbox" id="dow4" name="dow[]" value="4" <?php echo (in_array('4', $meeting_data['dow']) ? 'checked' : ''); ?>> Thu<br />
        <input type="checkbox" id="dow5" name="dow[]" value="5" <?php echo (in_array('5', $meeting_data['dow']) ? 'checked' : ''); ?>> Fri<br />
        <input type="checkbox" id="dow6" name="dow[]" value="6" <?php echo (in_array('6', $meeting_data['dow']) ? 'checked' : ''); ?>> Sat<br />
        <br />

        <label for="monthly">Monthly Repeat: </label><br />
        <input type="radio" name="monthly" value="" <?php echo (('' === $meeting_data['monthly']) ? 'checked' : ''); ?> /> N/A<br />
        <input type="radio" name="monthly" value="1" <?php echo (('1' === $meeting_data['monthly']) ? 'checked' : ''); ?> /> 1st<br />
        <input type="radio" name="monthly" value="2" <?php echo (('2' === $meeting_data['monthly']) ? 'checked' : ''); ?> /> 2nd<br />
        <input type="radio" name="monthly" value="3" <?php echo (('3' === $meeting_data['monthly']) ? 'checked' : ''); ?> /> 3rd<br />
        <input type="radio" name="monthly" value="4" <?php echo (('4' === $meeting_data['monthly']) ? 'checked' : ''); ?> /> 4th<br />
        <input type="radio" name="monthly" value="5" <?php echo (('5' === $meeting_data['monthly']) ? 'checked' : ''); ?> /> Last<br /><br />

        <label for="time">Meeting Time <small>(required)</small>: </label>
        <input type="text" id="time" name="time" required value="<?php echo ($meeting_data['time']); ?>" class="widefat time_picker" placeholder="Enter a meeting time" /><br /><br />

        <label for="notheld">Not Held In Months: </label><br />
        <input type="checkbox" id="notheld1"  name="notheld[]" value="1"  <?php echo (in_array('1',  $meeting_data['notheld']) ? 'checked' : ''); ?>> January<br />
        <input type="checkbox" id="notheld2"  name="notheld[]" value="2"  <?php echo (in_array('2',  $meeting_data['notheld']) ? 'checked' : ''); ?>> February<br />
        <input type="checkbox" id="notheld3"  name="notheld[]" value="3"  <?php echo (in_array('3',  $meeting_data['notheld']) ? 'checked' : ''); ?>> March<br />
        <input type="checkbox" id="notheld4"  name="notheld[]" value="4"  <?php echo (in_array('4',  $meeting_data['notheld']) ? 'checked' : ''); ?>> April<br />
        <input type="checkbox" id="notheld5"  name="notheld[]" value="5"  <?php echo (in_array('5',  $meeting_data['notheld']) ? 'checked' : ''); ?>> May<br />
        <input type="checkbox" id="notheld6"  name="notheld[]" value="6"  <?php echo (in_array('6',  $meeting_data['notheld']) ? 'checked' : ''); ?>> June<br />
        <input type="checkbox" id="notheld7"  name="notheld[]" value="7"  <?php echo (in_array('7',  $meeting_data['notheld']) ? 'checked' : ''); ?>> July<br />
        <input type="checkbox" id="notheld8"  name="notheld[]" value="8"  <?php echo (in_array('8',  $meeting_data['notheld']) ? 'checked' : ''); ?>> August<br />
        <input type="checkbox" id="notheld9"  name="notheld[]" value="9"  <?php echo (in_array('9',  $meeting_data['notheld']) ? 'checked' : ''); ?>> September<br />
        <input type="checkbox" id="notheld10" name="notheld[]" value="10" <?php echo (in_array('10', $meeting_data['notheld']) ? 'checked' : ''); ?>> October<br />
        <input type="checkbox" id="notheld11" name="notheld[]" value="11" <?php echo (in_array('11', $meeting_data['notheld']) ? 'checked' : ''); ?>> November<br />
        <input type="checkbox" id="notheld12" name="notheld[]" value="12" <?php echo (in_array('12', $meeting_data['notheld']) ? 'checked' : ''); ?>> December<br />
        <br />

        <label for="legend">
            Meeting Legend:
        </label><br />
        <?php
        self::$_legends = NSAALegend::getLegends();
        $html = '';
        foreach (self::$_legends as $id => $legend) {
            $html .= '<input type="checkbox" id="legend" name="legend[]" value="' . $id . '" ' . (in_array($id, $meeting_data['legend']) ? 'checked' : '') . '> ' . $legend['name'] . ' (' . $id . ')<br />';
        }
        echo $html;
        ?>
        <br />

        <label for="legend">Additional Meeting Information:</label>

        <textarea id="additional" name="additional" rows="5" class="widefat" placeholder="Enter any additional information"><? echo $meeting_data['additional']; ?></textarea>

        <?php
    }
    /**
     * Save the meta box data 
     * 
     * @param int   $post_id - The post ID
     * @param array $post    - The post
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
        $meeting_meta = [];
        $meeting_meta['location'] = isset($_POST['location']) ? sanitize_text_field($_POST['location']) : '';
        $meeting_meta['address'] = isset($_POST['address']) ? sanitize_text_field($_POST['address']) : '';
        $meeting_meta['city'] = isset($_POST['city']) ? sanitize_text_field($_POST['city']) : '';
        $meeting_meta['time'] = isset($_POST['time']) ? sanitize_text_field($_POST['time']) : '';
        $meeting_meta['monthly'] = isset($_POST['monthly']) ? sanitize_text_field($_POST['monthly']) : '';
        $meeting_meta['zoom'] = isset($_POST['zoom']) ? sanitize_text_field($_POST['zoom']) : '';
        // Remove spaces and dashes from Zoom  id
        $meeting_meta['zoom'] = str_replace(' ', '', $meeting_meta['zoom']);
        $meeting_meta['zoom'] = str_replace('-', '', $meeting_meta['zoom']);
        $meeting_meta['zoompw'] = isset($_POST['zoompw']) ? sanitize_text_field($_POST['zoompw']) : '';
        $meeting_meta['zoomlink'] = isset($_POST['zoomlink']) ? sanitize_text_field($_POST['zoomlink']) : '';
        $meeting_meta['zoomandinperson'] = isset($_POST['zoomandinperson']) ? sanitize_text_field($_POST['zoomandinperson']) : '';
        $meeting_meta['additional'] = isset($_POST['additional']) ? sanitize_textarea_field($_POST['additional']) : '';
        if (isset($_POST['notheld'])) {
            if (count($_POST['notheld']) > 0) {
                $notheld = [];
                foreach ($_POST['notheld'] as $id => $value) {
                    $notheld[$id] = sanitize_text_field($value);
                }
                $meeting_meta['notheld'] = $notheld;
            }
        }
        if (isset($_POST['legend'])) {
            if (count($_POST['legend']) > 0) {
                $legends = [];
                foreach ($_POST['legend'] as $id => $value) {
                    $legends[$id] = sanitize_text_field($value);
                }
                $meeting_meta['legend'] = $legends;
            }
        }
        if (isset($_POST['dow'])) {
            if (count($_POST['dow']) > 0) {
                $meeting_days = [];
                foreach ($_POST['dow'] as $id => $value) {
                    $meeting_days[$id] = sanitize_text_field($value);
                }
                $meeting_meta['dow'] = $meeting_days;
                update_post_meta($post_id, self::$META_DATA_KEY, $meeting_meta);
                return;
            }
        }

        self::set_dow_error();

    }

    public static function set_dow_error() {
        add_settings_error(
            'meeting-dow-error',
            'meeting-dow-error',
            'ERROR: You MUST have at least one meeting date set - Meeting unchanged.',
            'error'
        );

        set_transient('nsaa_settings_errors', get_settings_errors(), 30);
        
    }

    public function _meeting_admin_notices() {
        // If there are no errors, then we'll exit the function
        if (! ( $errors = get_transient('nsaa_settings_errors') ) ) {
            return;
        }
        // Otherwise, build the list of errors that exist in the settings errores
        $message = '<div id="nsaa-error-message" class="error below-h2"><ul>';
        foreach ( $errors as $error ) {
            $message .= '<li>' . $error['message'] . '</li>';
        }
        $message .= '</ul></div><!-- #error -->';
        // Write them out to the screen
        echo $message;
        // Clear and the transient and unhook any other notices so we don't see duplicate messages
        delete_transient('nsaa_settings_errors');
        remove_action('admin_notices', '_location_admin_notices');
    }

    /**
     * Load the single post template with the following order:
     * - Theme single post template (THEME/plugins/nsaa-options-plugin/templates/single-meeting.php)
     * - Plugin single post template (PLUGIN/templates/single-meeting.php)
     * - Default template
     * 
     * @global array $post - The post
     * @param string $template - Default template
     * 
     * @return string Template to use
     */
    function load_template($template) {
        global $post;
        // Check if this is a meeting.
        if (self::$POST_TYPE === $post->post_type) {
            // Plugin/Theme path
            $plugin_path = plugin_dir_path(__FILE__) . '../../templates/';
            $theme_path = get_stylesheet_directory() . '/plugins/nsaa-options-plugin/templates/';
            // The name of custom post type single template.
            $template_name = 'single-meeting.php';
            $pluginfile = $plugin_path . $template_name;
            $themefile = $theme_path . $template_name;
            // Check for templates.
            if (!file_exists($themefile)) {
                if (!file_exists($pluginfile)) {
                    // No theme or plugin template
                    return $template;
                }
                // Have a plugin template.
                return $pluginfile;
            }
            // Have a theme template.
            return $themefile;
        }
        //This is not a meeting, do nothing with $template.
        return $template;
    }

    function load_archive($template) {
        global $post;
        // Check if this is a meeting.
        if (self::$POST_TYPE === $post->post_type) {
            // Plugin/Theme path
            $plugin_path = plugin_dir_path(__FILE__) . '../../templates/';
            $theme_path = get_stylesheet_directory() . '/plugins/nsaa-options-plugin/templates/';
            // The name of custom post type single template.
            $template_name = 'meetings.php';
            $pluginfile = $plugin_path . $template_name;
            $themefile = $theme_path . $template_name;
            // Check for templates.
            if (!file_exists($themefile)) {
                if (!file_exists($pluginfile)) {
                    // No theme or plugin template
                    return $template;
                }
                // Have a plugin template.
                return $pluginfile;
            }
            // Have a theme template.
            return $themefile;
        }
        //This is not a meeting, do nothing with $template.
        return $template;
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
        $newcols['location'] = __('Location', NSAAConfig::TEXT_DOMAIN);
        $newcols['address'] = __('Address', NSAAConfig::TEXT_DOMAIN);
        $newcols['city'] = __('City', NSAAConfig::TEXT_DOMAIN);
        $newcols['zoom'] = __('Zoom ID', NSAAConfig::TEXT_DOMAIN);
        $newcols['zoompw'] = __('Zoom PWD', NSAAConfig::TEXT_DOMAIN);
        $newcols['zoomlink'] = __('Zoom Link', NSAAConfig::TEXT_DOMAIN);
        $newcols['zoomandinperson'] = __('Zoom/In Person', NSAAConfig::TEXT_DOMAIN);
        $newcols['dow'] = __('Meeting Day', NSAAConfig::TEXT_DOMAIN);
        $newcols['time'] = __('Meeting Time', NSAAConfig::TEXT_DOMAIN);
        $newcols['legend'] = __('Meeting Legend', NSAAConfig::TEXT_DOMAIN);
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
     * @param int    $post_id     - The ID of the post being displayed
     */
    function table_content($column_name, $post_id) {
        $meeting_data = self::get_meeting_data($post_id, false);
        
        if ('location' === $column_name) {
            echo $meeting_data['location'];
        } else if ('address' === $column_name) {
            echo $meeting_data['address'];
        } else if ('zoom' === $column_name) {
            $zoom = $meeting_data['zoom'];
            $zoom = substr($zoom, 0, 3) . ' ' . substr($zoom, 3, 3) . ' ' . substr($zoom, 6);
            echo $zoom;
        } else if ('zoompw' === $column_name) {
            $zoompw = $meeting_data['zoompw'];
            echo $zoompw;
        } else if ('zoomlink' === $column_name) {
            $zoomlink = $meeting_data['zoomlink'];
            echo $zoomlink;
        } else if ('zoomandinperson' === $column_name) {
            $zoomandinperson = $meeting_data['zoomandinperson'];
            echo ('1' === $zoomandinperson) ? 'YES' : '';
        } else if ('city' === $column_name) {
            if (0 === count(self::$_cities)) {
                self::$_cities = NSAACity::getCities();
            }
            if ($meeting_data['city']) {
                echo self::$_cities[$meeting_data['city']];
            } else {
                echo '';
            }
        } else if ('dow' === $column_name) {
            $dow = '';
            if (isset($meeting_data['dow'])) {
                if (count($meeting_data['dow']) > 0) {
                    $dow = self::getDOW($meeting_data['dow'], true);
                }
            }
            echo $dow;
        } else if ('time' === $column_name) {
            echo $meeting_data['time'];
        } else if ('legend' === $column_name) {
            $legend = '';
            if (isset($meeting_data['legend'])) {
                if (count($meeting_data['legend']) > 0) {
                    $legend = '';
                    foreach ($meeting_data['legend'] as $the_legend) {
                        $legend .= $the_legend . ',';
                    }
                    $legend = substr($legend, 0, -1);
                }
            }
            echo $legend;
        }
    }
}
