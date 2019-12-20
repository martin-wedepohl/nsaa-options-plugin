<?php
namespace NSAAOptions;
use NSAAOptions\NSAASettings;
defined( 'ABSPATH' ) or die( '' );
class NSAADebug {
    
    static public function write_log( $log ) {
        $settings = new NSAASettings();
        $wanttodebug = $settings->get_options('debugging');
        if('1' === $wanttodebug) {
            if( is_array( $log ) || is_object( $log ) ) {
                error_log( print_r( $log, true ) );
            } else {
                error_log( $log );
            }
        }
    }
    
    public function __construct() {
        $settings = new NSAASettings();
        $wanttodebug = $settings->get_options('debugging');
        if('1' === $wanttodebug) {
            add_action('wp_footer', [$this, 'show_template']);
        }        
    }
    
    public function show_template() {
        if(is_super_admin()) {
            global $template;
            print_r($template);
        }
    }
}