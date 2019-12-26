<?php
namespace NSAAOptions;
use NSAAOptions\NSAAConfig;
defined( 'ABSPATH' ) or die( '' );
class NSAASettings {
    public function register() {
        // Hook into the admin menu.
        add_action( 'admin_menu', [ $this, 'create_options_page' ] );
        // Custom submenu order
        add_filter( 'custom_menu_order', [ $this, 'reorder_submenu' ] );
        // Register the options for the database
        register_setting( NSAAConfig::OPTIONS_GROUP, NSAAConfig::OPTIONS_NAME );
        // Add Settings and Fields.
        add_action( 'admin_init', [ $this, 'setup_settings' ] );
    }
    /**
     * Create and add the plugin options page.
     */
    public function create_options_page() {
        // Add the menu item and page
        $page_title = __( 'North Shore AA Options', NSAAConfig::TEXT_DOMAIN );
        $menu_title = __( 'NSAA Options', NSAAConfig::TEXT_DOMAIN );
        $slug = NSAAConfig::PLUGIN_PAGE;
        $callback = [ $this, 'settings_page_content' ];
        $icon = 'dashicons-admin-generic';
        $position = 500;
        add_menu_page( $page_title, $menu_title, 'edit_others_posts', NSAAConfig::PLUGIN_PAGE, $callback, $icon, $position );
        $page_title = __( 'North Shore AA Settings', NSAAConfig::TEXT_DOMAIN );
        $menu_title = __( 'Settings', NSAAConfig::TEXT_DOMAIN );
        add_submenu_page( $slug, $page_title, $menu_title, 'manage_options', NSAAConfig::SETTINGS_PAGE, $callback );
        $page_title = __( 'NSAA Options Instructions', NSAAConfig::TEXT_DOMAIN );
        $menu_title = __( 'Instructions', NSAAConfig::TEXT_DOMAIN );
        $callback = [ $this, 'instructions_page_content' ];
        add_submenu_page( $slug, $page_title, $menu_title, 'edit_others_posts', NSAAConfig::INSTRUCTIONS_PAGE, $callback );
    }
// create_options_page
    /**
     * Reorder the submenu so that the NSRU Options is the first item in the submenu
     *
     * @global array $submenu
     *
     * @param type $menu_order
     */
    public function reorder_submenu( $menu_order ) {
        global $submenu;
        $custom = null;
        $search_item = __( 'Settings', NSAAConfig::TEXT_DOMAIN );
        if ( !empty( $submenu ) ) {
            foreach ( $submenu[ NSAAConfig::PLUGIN_PAGE ] as $index => $item ) {
                if ( $search_item === $item[ 0 ] ) {
                    $custom = $item;
                    unset( $submenu[ NSAAConfig::PLUGIN_PAGE ][ $index ] );
                    break;
                }
            }
            if ( null !== $custom ) {
                // Push to beginning of array
                array_unshift( $submenu[ NSAAConfig::PLUGIN_PAGE ], $custom );
            }
        }
        return $menu_order;
    }
    public function setup_settings() {
        add_settings_section( 'autodelete_section', __( 'Auto Delete', NSAAConfig::TEXT_DOMAIN ), null, NSAAConfig::PLUGIN_PAGE );
        $fields = [
            [
                'uid' => 'auto_delete_added',
                'label' => __( 'Auto Delete Added Meetings: ', NSAAConfig::TEXT_DOMAIN ),
                'section' => 'autodelete_section',
                'type' => 'checkbox',
                'options' => [ '1' => 'Yes' ],
                'class' => '',
            ],
            [
                'uid' => 'auto_delete_breakfast',
                'label' => __( 'Auto Delete Breakfast Meetings: ', NSAAConfig::TEXT_DOMAIN ),
                'section' => 'autodelete_section',
                'type' => 'checkbox',
                'options' => [ '1' => 'Yes' ],
                'class' => '',
            ],
            [
                'uid' => 'auto_delete_cake',
                'label' => __( 'Auto Delete Cakes: ', NSAAConfig::TEXT_DOMAIN ),
                'section' => 'autodelete_section',
                'type' => 'checkbox',
                'options' => [ '1' => 'Yes' ],
                'class' => '',
            ],
            [
                'uid' => 'auto_delete_cancelled',
                'label' => __( 'Auto Delete Cancelled Meetings: ', NSAAConfig::TEXT_DOMAIN ),
                'section' => 'autodelete_section',
                'type' => 'checkbox',
                'options' => [ '1' => 'Yes' ],
                'class' => '',
            ],
            [
                'uid' => 'auto_delete_events',
                'label' => __( 'Auto Delete Events: ', NSAAConfig::TEXT_DOMAIN ),
                'section' => 'autodelete_section',
                'type' => 'checkbox',
                'options' => [ '1' => 'Yes' ],
                'class' => '',
            ],
            [
                'uid' => 'auto_delete_gratitudes',
                'label' => __( 'Auto Delete Gratitude Meetings: ', NSAAConfig::TEXT_DOMAIN ),
                'section' => 'autodelete_section',
                'type' => 'checkbox',
                'options' => [ '1' => 'Yes' ],
                'class' => '',
            ],
        ];
        foreach ( $fields as $field ) {
            add_settings_field( $field[ 'uid' ], $field[ 'label' ], [ $this, 'field_callback' ], NSAAConfig::PLUGIN_PAGE, $field[ 'section' ], $field );
        }

        add_settings_section( 'features_section', __( 'Features', NSAAConfig::TEXT_DOMAIN ), null, NSAAConfig::PLUGIN_PAGE );
        $fields = [
            [
                'uid' => 'use_google_maps',
                'label' => __( 'Use Google Maps: ', NSAAConfig::TEXT_DOMAIN ),
                'section' => 'features_section',
                'type' => 'checkbox',
                'options' => [ '1' => 'Yes' ],
                'class' => '',
                'supplemental' => __( '(Display link to open meeting location in Google Maps)', NSAAConfig::TEXT_DOMAIN ),
            ]
        ];
        foreach ( $fields as $field ) {
            add_settings_field( $field[ 'uid' ], $field[ 'label' ], [ $this, 'field_callback' ], NSAAConfig::PLUGIN_PAGE, $field[ 'section' ], $field );
        }

        add_settings_section( 'tracking_section', __( 'Tracking/Analytics Codes', NSAAConfig::TEXT_DOMAIN ), null, NSAAConfig::PLUGIN_PAGE );
        $fields = [
            [
                'uid' => 'google',
                'label' => __( 'Google Code: ', NSAAConfig::TEXT_DOMAIN ),
                'section' => 'tracking_section',
                'type' => 'text',
                'class' => '',
                'helper' => __( '(Code for Google Anlytics)', NSAAConfig::TEXT_DOMAIN ),
                'placeholder' => 'UA-XXXXXXX-X'
            ],
        ];
        foreach ( $fields as $field ) {
            add_settings_field( $field[ 'uid' ], $field[ 'label' ], [ $this, 'field_callback' ], NSAAConfig::PLUGIN_PAGE, $field[ 'section' ], $field );
        }

        add_settings_section( 'debug_section', __( 'Debugging', NSAAConfig::TEXT_DOMAIN ), null, NSAAConfig::PLUGIN_PAGE );
        $fields = [
            [
                'uid' => 'debugging',
                'label' => __( 'Enable Debugging: ', NSAAConfig::TEXT_DOMAIN ),
                'section' => 'debug_section',
                'type' => 'checkbox',
                'options' => [ '1' => 'Yes' ],
                'class' => '',
                'supplemental' => __( '(Enable debug printing to the log file)', NSAAConfig::TEXT_DOMAIN ),
            ]
        ];
        foreach ( $fields as $field ) {
            add_settings_field( $field[ 'uid' ], $field[ 'label' ], [ $this, 'field_callback' ], NSAAConfig::PLUGIN_PAGE, $field[ 'section' ], $field );
        }
    }
    /**
     * Display a specific field.
     *
     * Options are stored in serialized format in the database.
     * The value is extracted and used if it exists already in the database.
     *
     * Based on the type of field print the appropriate field.
     *
     * @param array $arguments Arguments for the field.
     */
    public function field_callback( $arguments ) {
        $value = '';
        $options = get_option( NSAAConfig::OPTIONS_NAME );
        if ( is_array( $options ) ) {
            if ( array_key_exists( $arguments[ 'uid' ], $options ) ) {
                $value = $options[ $arguments[ 'uid' ] ];
            }
        }
        $arguments = shortcode_atts(
            [
                'uid' => '',
                'label' => '',
                'section' => '',
                'type' => 'text',
                'min' => 0,
                'max' => 100,
                'step' => 1,
                'class' => '',
                'helper' => '',
                'supplemental' => '',
                'placeholder' => '',
                'options' => []
            ], $arguments
        );
        switch ( $arguments[ 'type' ] ) {
            case 'text':
            case 'password':
                printf( '<input name="' . NSAAConfig::OPTIONS_NAME . '[%1$s]" id="%1$s" class="%5$s" type="%2$s" placeholder="%3$s" value="%4$s" />', $arguments[ 'uid' ], $arguments[ 'type' ], $arguments[ 'placeholder' ], $value, $arguments[ 'class' ] );
                break;
            case 'number':
                printf( '<input name="' . NSAAConfig::OPTIONS_NAME . '[%1$s]" id="%1$s" class="%5$s" min="%6$f" max="%7$f" step="%8$f" type="%2$s" placeholder="%3$s" value="%4$s" />', $arguments[ 'uid' ], $arguments[ 'type' ], $arguments[ 'placeholder' ], $value, $arguments[ 'class' ], $arguments[ 'min' ], $arguments[ 'max' ], $arguments[ 'step' ] );
                break;
            case 'textarea':
                printf( '<textarea name="' . NSAAConfig::OPTIONS_NAME . '[%1$s]" id="%1$s" class="%4$s" placeholder="%2$s" rows="5" cols="150">%3$s</textarea>', $arguments[ 'uid' ], $arguments[ 'placeholder' ], $value, $arguments[ 'class' ] );
                break;
            case 'select':
            case 'multiselect':
                if ( !empty( $arguments[ 'options' ] ) && is_array( $arguments[ 'options' ] ) ) {
                    $attributes = '';
                    $options_markup = '';
                    foreach ( $arguments[ 'options' ] as $key => $label ) {
                        $options_markup .= sprintf( '<option value="%s" %s>%s</option>', $key, selected( $value[ array_search( $key, $value, true ) ], $key, false ), $label );
                    }
                    if ( $arguments[ 'type' ] === 'multiselect' ) {
                        $attributes = ' multiple="multiple" ';
                    }
                    printf( '<select name="' . NSAAConfig::OPTIONS_NAME . '[%1$s]" id="%1$s" class="%4$s" %2$s>%3$s</select>', $arguments[ 'uid' ], $attributes, $options_markup, $arguments[ 'class' ] );
                }
                break;
            case 'radio':
            case 'checkbox':
                if ( !empty( $arguments[ 'options' ] ) && is_array( $arguments[ 'options' ] ) ) {
                    $options_markup = '';
                    $iterator = 0;
                    foreach ( $arguments[ 'options' ] as $key => $label ) {
                        $iterator++;
                        $options_markup .= sprintf( '<label for="%1$s_%6$s"><input id="%1$s_%6$s" name="' . NSAAConfig::OPTIONS_NAME . '[%1$s]" class="%7$s" type="%2$s" value="%3$s" %4$s /> %5$s</label><br/>', $arguments[ 'uid' ], $arguments[ 'type' ], $key, checked( $value, $key, false ), $label, $iterator, $arguments[ 'class' ] );
                    }
                    printf( '<fieldset>%s</fieldset>', $options_markup );
                }
                break;
        }
        if ( $helper = $arguments[ 'helper' ] ) {
            printf( '<span class="helper"> %s</span>', $helper );
        }
        if ( $supplemental = $arguments[ 'supplemental' ] ) {
            printf( '<p class="description">%s</p>', $supplemental );
        }
    }
    /**
     * HTML page used to display the options.
     */
    public function settings_page_content() {
        require_once plugin_dir_path( __FILE__ ) . 'nsaa-options-settings.php';
    }
// settings_page_content
    /**
     * HTML page used to display the instructions.
     */
    public function instructions_page_content() {
        require_once plugin_dir_path( __FILE__ ) . 'nsaa-options-instructions.php';
    }
    /**
     * Callback to display the section.
     *
     * Not used since an HTML page (above is used)
     *
     * @param array $arguments
     */
    public function section_callback( $arguments ) {
        
    }
// section_callback
    public function get_options( $name = '' ) {
        $options = get_option( NSAAConfig::OPTIONS_NAME );
        if ( is_array( $options ) ) {
            if ( count( $options ) > 0 ) {
                if ( '' !== $name ) {
                    if ( array_key_exists( $name, $options ) ) {
                        return $options[ $name ];
                    }
                } else {
                    return $options;
                }
            } else {
                return '';
            }
        } else {
            return '';
        }
    }
}