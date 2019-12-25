<?php

namespace NSAAOptions;

use NSAAOptions\NSAACity;
use NSAAOptions\NSAALegend;
use NSAAOptions\NSAAMeeting;
use NSAAOptions\NSAASettings;

defined('ABSPATH') or die('');

/**
 * Class to handle all the shortcodes for the plugin
 */
class NSAAShortcodes {

    /**
     * Initialize the shortcodes used in the plugin
     */
    public static function initShortcodes() {

        add_shortcode( 'nsaa_get_meeting',          [new NSAAShortcodes, 'get_meeting'] );
        add_shortcode( 'nsaa_get_meetings',         [new NSAAShortcodes, 'get_meetings'] );
        add_shortcode( 'nsaa_get_calendar',         [new NSAAShortcodes, 'get_calendar'] );
        add_shortcode( 'nsaa_get_service_meetings', [new NSAAShortcodes, 'get_service_meetings'] );
        add_shortcode( 'nsaa_get_legend',           [new NSAAShortcodes, 'get_legend'] );
        add_shortcode( 'nsaa_get_events',           [new NSAAShortcodes, 'get_events'] );
        add_shortcode( 'nsaa_front_page_sections',  [new NSAAShortcodes, 'front_page_sections'] );

    }

    /**
     * Display the instructions for all the shortcodes
     */
    public static function getShortcodeInstructions() {
?>

<p>[nsaa_get_meeting id="POST_ID"]</p>
<ul>
    <li><?php _e('Display the meeting information for a meeting with a specific POST_ID (required)', NSAAConfig::TEXT_DOMAIN) ?></li>
</ul>

 <?php
    }

    /**
     * Display the instructions for removing\modifying the shortcodes
     */
    public static function getRemovalInstructions() {
?>
<code>
    $shortcode_handler = apply_filter("get_nsaa_shortcode_instance", NULL)<br />
    &nbsp;if( is_a( $shortcode_handler, "\NSAAOptions\NSAAShortcodes" ) {<br />
    &nbsp;&nbsp;&nbsp;&nbsp;// Do something with the instance of the handler<br />
    &nbsp;}
</code>
<?php
    }

    /**
     * Function constructor
     */
    public function __constructor() {

        add_filter( 'get_nsaa_shortcode_instance', [$this, 'get_instance'] );

    }

    /**
     * Return the instance of the class
     */
    public function get_instance() {

        return $this;

    }

    /**
     * Shortcode to return the information on a specific meeting
     *     [nsaa_get_meeting id="POST_ID"]
     *         POST_ID (required) - Post ID of the meeting
     * 
     * @param array $atts Attributes passed to the function
     */
    public function get_meeting( $atts ) {

        do_action( 'nsaa_before_get_meeting' );

        // Process the attributes
        $atts = shortcode_atts(
            [
                'id' => ''
            ], $atts, 'nsaa_get_meeting'
        );

        $timezone = get_option('timezone_string');
        date_default_timezone_set($timezone);

        // Get the title of the post
        $title = get_the_title( $atts['id'] );

        // Get the meta data for the post
        $meeting_data = NSAAMeeting::get_meeting_data( $atts['id'] );
        $time         = $meeting_data['time'];
        $dow          = $meeting_data['dow'];
        $legend       = $meeting_data['legend'];
        $city_id      = $meeting_data['city'];
        $location     = ('' === $meeting_data['location']) ? '' : $meeting_data['location'] . ', ';

        // Get all the legends
        $legends = NSAALegend::getLegends();

        $legend_str = '';
        foreach( $legend as $value ) {
            if( isset( $legends[$value] ) ) {
                $legend_str .= $legends[$value]['name'];
                if( '' !== $legends[$value]['additional'] ) {
                    $legend_str .= ' (' . $legends[$value]['additional'] . ')';
                }
                $legend_str .= ',';
            }
        }
        if( '' !== $legend_str ) {
            $legend_str = substr( $legend_str, 0, -1 );
        }

        // Get all the cities
        $cities = NSAACity::getCities();

        // Get the day(s) of the week that the meetings are held
        $day = NSAAMeeting::getDOW( $dow );

        // Get all the information to display
        $city = (isset( $cities[$city_id] ) ? $cities[$city_id] . ', BC' : '');
        $address = $location . $meeting_data['address'] . (('' === $city) ? '' : ', ' . $city);

        // Create the HTML information
        $html  = "<h2>{$title}</h2>";
        $html .= "{$day} @ {$time}<br />";
        $html .= "$address<br />";
        if( '' !== $meeting_data['additional'] ) {
            $html .= '<strong>' . nl2br($meeting_data['additional']) . '</strong><br />';
        }
        if( '' !== $legend_str ) {
            $html .= $legend_str . '<br />';
        }

        // Only display the link if we allow it
        $settings = new NSAASettings();
        $wantgooglemaps = $settings->get_options('use_google_maps');
        if(1 == $wantgooglemaps) {
            $link = 'https://maps.google.com/?q=' . $address;
            $html .= '<a href="' . $link . '" target="_blank" title="View in Map">View in Map</a>';
        }

        do_action( 'nsaa_after_get_meeting' );

        $html = apply_filters( 'nsaa_get_meeting', $html );

        return $html;

    }

    /**
     * Shortcode to return the information all the meetings for the next number of days
     * which will default to 7 days
     *         
     * @param array $atts Attributes passed to the function
     */
    public function get_calendar( $atts ) {

        do_action( 'nsaa_before_get_calendar' );

        // Process the attributes
        $atts = shortcode_atts(
            [
                'days' => 7,
            ], $atts, 'nsaa_get_calendar'
        );

        $timezone = get_option('timezone_string');
        date_default_timezone_set($timezone);
        $dayofweek = date('w');

        $cities = NSAACity::getCities();
        $legends = NSAALegend::getLegends();
        $cancelled = NSAACancelledMeetings::getCancelled();
        $added = NSAAAddedMeetings::getAdded();

        $html = '';
        for($day = 0; $day < $atts['days']; $day++) {
            $day_ts = strtotime("+ $day day");
            $theday = date('l F jS, Y', $day_ts);
            $day_str = date('l', $day_ts);
            $month_str = date('F', $day_ts); 
            $month_num = date('n', $day_ts);
            $year_str = date('Y', $day_ts);
            $html .= "<h3>$theday</h3>";
            $dayofweek %= 7;
            $meetings = NSAAMeeting::getMeetings($dayofweek, true, 'time');

            $city = '';
            foreach($meetings as $meeting) {
                $occurs = true;

                // Check if the meeting is held in this month
                if(count($meeting['notheld']) > 0) {
                    if(true === in_array($month_num, $meeting['notheld'])) {
                        $occurs = false;
                    }
                }

                // Check if it is a cancelled meeting
                if(true === $occurs) {
                    foreach($cancelled as $cancel) {
                        if(true === in_array($meeting['id'], $cancel)) {
                            $cdate_ts = strtotime($cancel['cdate']);
                            $cdate_str = date('l F jS, Y', $cdate_ts);
                            if($cdate_str === $theday) {
                                $occurs = false;
                            }
                        }
                    }
                }

                // If monthly meeting see if it is occuring on this date
                if(true === $occurs && true === isset($meeting['monthly'])) {
                    $monthly = $meeting['monthly'];
                    switch($monthly) {
                        case 1:
                            $monthly_str = 'first';
                        break;
                        case 2:
                            $monthly_str = 'second';
                        break;
                        case 3:
                            $monthly_str = 'third';
                        break;
                        case 4:
                            $monthly_str = 'fourth';
                        break;
                        case 5:
                            $monthly_str = 'last';
                        break;
                        default:
                            $monthly_str = '';
                    }
                    if('' !== $monthly_str) {
                        // Check for nth day of month year
                        $thismonth_str = strtolower("$monthly_str $day_str of $month_str $year_str");
                        $thismonth_ts = strtotime($thismonth_str);
                        $today_str = date('l F jS, Y', $thismonth_ts);
                        if($today_str !== $theday) {
                            $occurs = false;
                        }
                    }
                }

                // Check if it is an added meeting
                foreach($added as $add) {
                    if(true === in_array($meeting['id'], $add)) {
                        $adate_ts = strtotime($add['adate']);
                        $adate_str = date('l F jS, Y', $adate_ts);
                        if($adate_str === $theday) {
                            $occurs = true;
                        }
                    }
                }
                
                if($occurs) {

                    $city = ((isset( $cities[$meeting['city']] )) ? $cities[$meeting['city']] : '');
                    $legend_str = '';
                    foreach( $meeting['legend'] as $value ) {
                        $legend_str .= $value . ',';
                    }
                    if( '' !== $legend_str ) {
                        $legend_str = substr( $legend_str, 0, -1 );
                        $legend_str = ' (' . $legend_str . ')';
                    }
                    $location = ('' === $meeting['location']) ? '' : $meeting['location'] . ', ';

                    $html .= '<p>' . $meeting['time'] . ' - <a href="' . esc_url( get_the_permalink( $meeting['id'] ) ) . '" title="Visit ' . $meeting['name'] . '">' . strtoupper( $meeting['name'] ) . '</a>' . $legend_str . '<br />';
                    $html .= $location . ' ' . $meeting['address'] . ', ' . $city . '<br />';
                    $html .= (('' === $meeting['additional']) ? '' : '<strong>' . nl2br($meeting['additional']) . '</strong><br />');

                }
            }

            $dayofweek++;

        }
        
        return $html;

    }

    /**
     * Shortcode to return the information all the meetings for a specific day
     *     [nsaa_get_meetings dow="DOW" includeservice=false/true]
     *         DOW (required) - Day of week 0 (Sunday) - 6 (Saturday)
     *         includeservice - Include service meetings - Default false
     *         
     * @param array $atts Attributes passed to the function
     */
    public function get_meetings( $atts ) {

        do_action( 'nsaa_before_get_meetings' );

        // Process the attributes
        $atts = shortcode_atts(
            [
                'dow' => '',
                'includeservice' => 'false',
            ], $atts, 'nsaa_get_meetings'
        );

        $atts['includeservice'] = ('true' === $atts['includeservice']) ? true : false;

        $meetings = NSAAMeeting::getMeetings($atts['dow'], $atts['includeservice']);

        $cities = NSAACity::getCities();
        $legends = NSAALegend::getLegends();

        $html = '';
        $city = '';
        foreach( $meetings as $meeting ) {
            if( $city !== $meeting['city'] ) {
                $city =  $meeting['city'];
                $html .= '<h3>' . ((isset( $cities[$city] )) ? strtoupper( $cities[$city] ) : '') . '</h3>';
            }
            $legend_str = '';
            foreach( $meeting['legend'] as $value ) {
                $legend_str .= $value . ',';
            }
            if( '' !== $legend_str ) {
                $legend_str = substr( $legend_str, 0, -1 );
                $legend_str = ' (' . $legend_str . ')';
            }
            $location     = ('' === $meeting['location']) ? '' : $meeting['location'] . ', ';

            $html .= '<p>' . $meeting['time'] . ' - <a href="' . esc_url( get_the_permalink( $meeting['id'] ) ) . '" title="Visit ' . $meeting['name'] . '">' . strtoupper( $meeting['name'] ) . '</a>' . $legend_str . '<br />';
            $html .= $location . ' ' . $meeting['address'] . '<br />';
            $html .= (('' === $meeting['additional']) ? '' : '<strong>' . nl2br($meeting['additional']) . '</strong><br />');
        }

        return $html;

    }

    /**
     */
    public function get_service_meetings() {

        do_action( 'nsaa_before_get_service_meetings' );

        $meetings = NSAAMeeting::getServiceMeetings();
        $cities = NSAACity::getCities();

        $html = '';
        foreach( $meetings as $meeting ) {
            $dow       = NSAAMeeting::getDOW($meeting['dow']);
            $monthly   = $meeting['monthly'];
            switch($monthly) {
                case '1':
                    $monthly_str = '1st ';
                break;
                case '2':
                    $monthly_str = '2nd ';
                break;
                case '3':
                    $monthly_str = '3rd ';
                break;
                case '4':
                    $monthly_str = '4th ';
                break;
                case '5':
                    $monthly_str = 'Last ';
                break;
                default:
                    $monthly_str = '';
            }
            $location  = ('' === $meeting['location']) ? '' : $meeting['location'] . ', ';
            $html .= '<p><a href="' . esc_url( get_the_permalink( $meeting['id'] ) ) . '" title="Visit ' . $meeting['name'] . '">' . strtoupper( $meeting['name'] ) . '</a> - ' . $monthly_str . $dow . ' of each month @ ' . $meeting['time'] . '<br />';
            $html .= $location . ' ' . $meeting['address'] . ', ' . $cities[$meeting['city']] . '<br />';
            $html .= (('' === $meeting['additional']) ? '' : '<strong>' . nl2br($meeting['additional']) . '</strong><br />');
        }

        return $html;

    }

    public function get_legend() {

        $legends = NSAALegend::getLegends();

        $html = '';
        foreach( $legends as $code => $legend ) {
            $html .= "<strong>{$code}</strong> - {$legend['name']}";
            if( '' !== $legend['additional'] ) {
                $html .= " (<strong><i>{$legend['additional']}</i></strong>)";
            }
            $html .= ', ';
        }
        if( '' !== $html ) {
            $html = substr( $html, 0, -2 );
        }

        return $html;

    }

    public function get_events() {

        $date = '';
        $html = '<ul>';
        $posts = NSAAEvents::getEvents();
        foreach($posts as $id => $post) {
            if($date !== $post['sdate']) {
                $date = date('l F jS', strtotime($post['sdate']));
                if('<ul>' === $html) {
                    $html .= "<li>{$date}<ul>";
                } else {
                    $html .= "</ul></li><li>{$date}<ul>";
                }
                $date = $post['sdate'];
            }

            $html .= "<li><strong>{$post['title']}</strong><br />";

            $msg = "{$post['content']}";
            $html .= nl2br( $msg ) . '</li>';

            if(!empty($post['thumbnail'])) {
                $html .= '<a href="' . esc_url($post['thumbnail_url']) . '" rel="lightbox" title="View Larger Image">' . $post['thumbnail'] . '</a>';
            }
        }
        $html .= '</ul></li></ul>';

        return $html;

    }

    public function front_page_sections( $atts ) {

        // Process the attributes
        $atts = shortcode_atts(
            [
                'id' => ''
            ], $atts, 'nsaa_front_page_sections'
        );

        $timezone = get_option('timezone_string');
        date_default_timezone_set($timezone);
        $month_num = date('n');
        $year = date('Y');

        switch( $atts['id'] ) {
            case 'service-opportunities':
                $html = '<ul>';
                $posts = NSAAServiceOp::getServiceOps();
                foreach( $posts as $id => $post ) {
                    $msg = "<li><div><strong>{$post['title']}</strong></div>";
                    $msg .= "{$post['content']}</li>";
                    $html .= nl2br( $msg );
                }
                $html .= '</ul>';
                if('<ul></ul>' === $html) {
                    $html = '';
                }
            break;

            case 'meeting-changes':
                $html = '<ul>';
                $posts = NSAAMeetingChanges::getMeetingChanges();
                foreach( $posts as $id => $post ) {
                    $msg = "<li><div><strong>{$post['title']}</strong></div>";
                    $msg .= "{$post['content']}</li>";
                    $html .= nl2br( $msg );
                }
                $html .= '</ul>';
                if('<ul></ul>' === $html) {
                    $html = '';
                }
            break;

            case 'cancelled-meetings':
                $date = '';
                $html = '<ul>';
                $posts = NSAACancelledMeetings::getCancelled();
                foreach($posts as $id => $post) {
                    if($date !== $post['cdate']) {
                        $date = date('l F jS', strtotime($post['cdate']));
                        if('<ul>' === $html) {
                            $html .= "<li>{$date}<ul>";
                        } else {
                            $html .= "</ul></li><li>{$date}<ul>";
                        }
                        $date = $post['cdate'];
                    }
                    $group = get_the_title($post['group']);
                    $link = '<a href="' . esc_url( get_the_permalink( $post['group'] ) ) . '" title="Visit ' . $group. '">' . strtoupper( $group ) . '</a>';

                    $html .= "<li>{$post['name']} - {$link}</li>";
                }
                $html .= '</ul></li></ul>';
                if('<ul></ul></li></ul>' === $html) {
                    $html = '';
                }
            break;

            case 'group-cakes':
                $date = '';
                $html = '<ul>';
                $posts = NSAACake::getCakes();
                foreach($posts as $id => $post) {
                    if($date !== $post['cdate']) {
                        $date = date('l F jS', strtotime($post['cdate']));
                        if('<ul>' === $html) {
                            $html .= "<li>{$date}<ul>";
                        } else {
                            $html .= "</ul></li><li>{$date}<ul>";
                        }
                        $date = $post['cdate'];
                    }
                    $milestone = $post['milestone'];
                    switch($milestone % 10) {
                        case 1:
                            $milestone .= ' yr';
                        break;
                        default:
                            $milestone .= ' yrs';
                    }
                    $group = get_the_title($post['group']);
                    $link = '<a href="' . esc_url( get_the_permalink( $post['group'] ) ) . '" title="Visit ' . $group. '">' . strtoupper( $group ) . '</a>';

                    $html .= "<li>{$post['name']} - {$milestone} - {$link}</li>";
                }
                $html .= '</ul></li></ul>';
                if('<ul></ul></li></ul>' === $html) {
                    $html = '';
                }
            break;

            case 'gratitude-nights':
                $date = '';
                $html = '<ul>';
                $posts = NSAAGratitude::getGratitudes();
                foreach($posts as $id => $post) {
                    if($date !== $post['gdate']) {
                        $date = date('l F jS', strtotime($post['gdate']));
                        if('<ul>' === $html) {
                            $html .= "<li>{$date}<ul>";
                        } else {
                            $html .= "</ul></li><li>{$date}<ul>";
                        }
                        $date = $post['gdate'];
                    }
                    $group = get_the_title($post['group']);
                    $link = '<a href="' . esc_url( get_the_permalink( $post['group'] ) ) . '" title="Visit ' . $group. '">' . strtoupper( $group ) . '</a>';

                    $additional = $post['additional'];
                    if('' === $additional) {
                        $additional = "({$post['gtime']})";
                    } else {
                        $additional = "({$post['gtime']} - {$additional})";
                    }   
                    $html .= "<li>{$post['name']} {$additional} - {$link}</li>";
                    if(!empty($post['thumbnail'])) {
                        $html .= '<a href="' . esc_url($post['thumbnail_url']) . '" rel="lightbox" title="View Larger Image">' . $post['thumbnail'] . '</a>';
                    }
                }
                $html .= '</ul></li></ul>';
                if('<ul></ul></li></ul>' === $html) {
                    $html = '';
                }
            break;

            case 'events':
                $date = '';
                $html = '<ul>';
                $posts = NSAAEvents::getEvents();
                foreach($posts as $id => $post) {
                    if($date !== $post['sdate']) {
                        $date = date('l F jS', strtotime($post['sdate']));
                        if('<ul>' === $html) {
                            $html .= "<li>{$date}<ul>";
                        } else {
                            $html .= "</ul></li><li>{$date}<ul>";
                        }
                        $date = $post['sdate'];
                    }

                    $html .= "<li><strong>{$post['title']}</strong><br />";

                    $msg = "{$post['content']}";
                    $html .= nl2br( $msg ) . '</li>';

                    if(!empty($post['thumbnail'])) {
                        $html .= '<a href="' . esc_url($post['thumbnail_url']) . '" rel="lightbox" title="View Larger Image">' . $post['thumbnail'] . '</a>';
                    }
                }
                $html .= '</ul></li></ul>';
                if('<ul></ul></li></ul>' === $html) {
                    $html = '';
                }
            break;

            case 'sunday-morning-breakfast-meeting':
                $posts = NSAABreakfastMeetings::getBreakfasts();
                $html = '<ul>';
                foreach( $posts as $id => $post ) {
                    $bdate_ts = strtotime($post['bdate']);
                    $bdate = date('F Y', $bdate_ts);
                    $group = get_the_title($post['group']);
                    $html .= "<li>{$bdate} - {$group}</li>";
                }
                $html .= '</ul>';
                if('<ul></ul>' === $html) {
                    $html = '';
                }
            break;

            case 'district-meeting':

                $meeting = NSAAMeeting::getServiceMeetings('GSR Meeting');
                $cities = NSAACity::getCities();
                $html = '';
                if(count($meeting) > 0) {
                    $meeting = $meeting[0];
                    while(true === in_array($month_num, $meeting['notheld'])) {
                        $year = (12 == $month_num) ? $year + 1 : $year;
                        $month_num = (12 == $month_num) ? 1 : ($month_num + 1);
                    }

                    switch($month_num) {
                        case '1':  $month = 'January';   break;
                        case '2':  $month = 'February';  break;
                        case '3':  $month = 'March';     break;
                        case '4':  $month = 'April';     break;
                        case '5':  $month = 'May';       break;
                        case '6':  $month = 'June';      break;
                        case '7':  $month = 'July';      break;
                        case '8':  $month = 'August';    break;
                        case '9':  $month = 'September'; break;
                        case '10': $month = 'October';   break;
                        case '11': $month = 'November';  break;
                        case '12': $month = 'December';  break;
                    }

                    $dow       = NSAAMeeting::getDOW($meeting['dow']);
                    $monthly   = $meeting['monthly'];
                    switch($monthly) {
                        case '1': $monthly_str = 'first '; break;
                        case '2': $monthly_str = 'second '; break;
                        case '3': $monthly_str = 'third '; break;
                        case '4': $monthly_str = 'fourth '; break;
                        case '5': $monthly_str = 'Last '; break;
                        default: $monthly_str = '';
                    }

                    $meeting_date_str = strtolower("$monthly_str $dow $month $year");
                    $meeting_date_ts = strtotime($meeting_date_str);
                    $meeting_date = date('l F jS, Y', $meeting_date_ts);

                    $location  = ('' === $meeting['location']) ? '' : $meeting['location'] . ', ';
                    $html .= '<p>Next <a href="' . esc_url( get_the_permalink( $meeting['id'] ) ) . '" title="Visit ' . $meeting['name'] . '">' . strtoupper( $meeting['name'] ) . '</a> - ' . $meeting_date . '<br />';
                    $html .= $location . ' ' . $meeting['address'] . ', ' . $cities[$meeting['city']] . '<br />';
                    $html .= (('' === $meeting['additional']) ? '' : '<strong>' . nl2br($meeting['additional']) . '</strong><br />');
                }
            break;

            default:
                $html = '';
        }

        // If there is no data for the section hide it
        if( '' === $html ) {
            // Ensure that there is a id to hide
            if( '' !== $atts['id'] ) {
                $html = '<span class="hide-nsaa-section" data-id="' . $atts['id'] . '"></span>';
                return $html;
            }
            return $html;
        }

        return $html;

    }


}