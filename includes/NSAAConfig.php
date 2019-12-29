<?php
namespace NSAAOptions;
defined( 'ABSPATH' ) or die( '' );
class NSAAConfig {
    const VERSION                   = '0.1.5';
    
	const MINIMUM_WORDPRESS_VERSION = '4.9';
    const MINIMUM_PHP_VERSION       = '5.6';
	const MINIMUM_ELEMENTOR_VERSION = '2.0.0';
    
    const TEXT_DOMAIN               = 'nsaa-options';
    
    const PLUGIN_PAGE               = 'nsaa-options';
    const SETTINGS_PAGE             = 'nsaa-options';
    const INSTRUCTIONS_PAGE         = 'nsaa-instructions';
    
    const OPTIONS_GROUP             = 'nsaa-options';
    const OPTIONS_NAME              = 'nsaa-options';
}