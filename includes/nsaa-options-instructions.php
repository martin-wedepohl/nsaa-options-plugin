<?php

namespace NSAAOptions;

use NSAAOptions\NSAAConfig;
use NSAAOptions\NSAAShortcodes;

?>
<div class="wrap">
    <h2><?php _e('North Shore AA Options Plugin Instructions', NSAAConfig::TEXT_DOMAIN) ?></h2>
    <ul class="tabs">

        <li class="tab">
            <input type="radio" name="tabs" checked="checked" id="tab1" />
            <label for="tab1"><?php _e('Options', NSAAConfig::TEXT_DOMAIN) ?></label>
            <div id="tab-content1" class="content">
                <h2><?php _e('Options', NSAAConfig::TEXT_DOMAIN) ?></h2>
                <hr/>
                <h3><?php _e('Tracking/Analytics Code', NSAAConfig::TEXT_DOMAIN) ?></h3>
                <p>
                    <?php _e('This section is used for all the analytics/tracking codes used by the various sites.
                    This will be the unique identifier that is inserted into the script used by the site.', NSAAConfig::TEXT_DOMAIN) ?>
                <p>
                    <?php _e('Google Analytics Tracking Code - Typically UA-XXXXXXX-X', NSAAConfig::TEXT_DOMAIN) ?>
                </p>
            </div>
        </li>

        <li class="tab">
            <input type="radio" name="tabs" id="tab2" />
            <label for="tab2"><?php _e('Meetings CPT', NSAAConfig::TEXT_DOMAIN) ?></label>   
            <div id="tab-content2" class="content">
                <h2><?php _e('Meeting Custom Post Type', NSAAConfig::TEXT_DOMAIN) ?></h2>
                <hr/>
                <p>
                    <?php _e('This custom post type is used to enter the data for
                             the meeting which is used on the meeting page and in various other pages on the site.', NSAAConfig::TEXT_DOMAIN) ?>
                </p>
                <p>
                    <?php _e('The following fields are used in the custom post type.', NSAAConfig::TEXT_DOMAIN) ?>
                </p>
                <ul>
                    <li><strong><?php _e('Title ', NSAAConfig::TEXT_DOMAIN) ?></strong><?php _e('(required) - Displayed underneath the picture', NSAAConfig::TEXT_DOMAIN) ?></li>
                    <li><strong><?php _e('Content ', NSAAConfig::TEXT_DOMAIN) ?></strong><?php _e('(required) - The full text of the meeting', NSAAConfig::TEXT_DOMAIN) ?></li>
                    <li><strong><?php _e('Meeting Information', NSAAConfig::TEXT_DOMAIN) ?></strong>
                        <ul>
                            <li><?php _e('Address (required)', NSAAConfig::TEXT_DOMAIN) ?></li>
                        </ul>
                    </li>
                    <li><strong><?php _e('Excerpt ', NSAAConfig::TEXT_DOMAIN) ?></strong><?php _e('(required) - Will be shown when the meeting is hovered on with the mouse', NSAAConfig::TEXT_DOMAIN) ?></li>
                </ul>
            </div>
        </li>

        <li class="tab">
            <input type="radio" name="tabs" id="tab3" />
            <label for="tab3"><?php _e('Shortcodes', NSAAConfig::TEXT_DOMAIN) ?></label>   
            <div id="tab-content3" class="content">
                <h2><?php _e('Shortcodes', NSAAConfig::TEXT_DOMAIN) ?></h2>
                <hr/>
                <?php NSAAShortcodes::getShortcodeInstructions(); ?>
                <h2><?php _e('Modify\Remove Shortcode', NSAAConfig::TEXT_DOMAIN) ?></h2>
                <hr/>
                <?php NSAAShortcodes::getRemovalInstructions(); ?>
            </div>
        </li>

    </ul>


</div><!-- .wrap -->