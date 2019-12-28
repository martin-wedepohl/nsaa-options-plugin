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
            <label for="tab1"><?php _e('Themes/Plugins', NSAAConfig::TEXT_DOMAIN) ?></label>
            <div id="tab-content1" class="content">
                <h2><?php _e('Themes/Plugins Required', NSAAConfig::TEXT_DOMAIN) ?></h2>
                <hr/>
                <h3><?php _e('Themes', NSAAConfig::TEXT_DOMAIN) ?></h3>
                <ul>
                    <li><?php _e('OceanWP - Base Theme', NSAAConfig::TEXT_DOMAIN) ?></li>
                    <li><?php _e('North Shore AA - Child Theme of OceanWP', NSAAConfig::TEXT_DOMAIN) ?></li>
                    <li><?php _e('Twenty Twenty - Default Wordpress theme just in case there are issues', NSAAConfig::TEXT_DOMAIN) ?></li>
                </ul>
                <hr />
                <h3><?php _e('Plugins', NSAAConfig::TEXT_DOMAIN) ?></h3>
                <ul>
                    <li><?php _e('Contact Form 7 - Display contact forms for emailing', NSAAConfig::TEXT_DOMAIN) ?></li>
                    <li><?php _e('Duplicator - Website backup and migration', NSAAConfig::TEXT_DOMAIN) ?></li>
                    <li><?php _e('Elementor - Page builder for the pages', NSAAConfig::TEXT_DOMAIN) ?></li>
                    <li><?php _e('North Shore AA Options Plugin - Required options/custom post types/shortcodes', NSAAConfig::TEXT_DOMAIN) ?></li>
                    <li><?php _e('Ocean Extra - Extra features for the OceanWP Theme', NSAAConfig::TEXT_DOMAIN) ?></li>
                    <li><?php _e('Rank Math SEO - SEO/Redirection/404 Logging', NSAAConfig::TEXT_DOMAIN) ?></li>
                    <li><?php _e('WP-Optimize - Database optimization/caching/compression', NSAAConfig::TEXT_DOMAIN) ?></li>
                </ul>
            </div>
        </li>

        <li class="tab">
            <input type="radio" name="tabs" id="tab2" />
            <label for="tab2"><?php _e('Settings', NSAAConfig::TEXT_DOMAIN) ?></label>
            <div id="tab-content2" class="content">
                <h2><?php _e('Settings', NSAAConfig::TEXT_DOMAIN) ?></h2>
                <hr/>
                <h3><?php _e('Auto Delete', NSAAConfig::TEXT_DOMAIN) ?></h3>
                <p>
                    <?php _e('If checked will auto delete custom post types once the date of the post has passed.', NSAAConfig::TEXT_DOMAIN) ?>
                </p>
                <ul>
                    <li><?php _e('Auto Delete Added Meetings - If checked will automatically remove the added meetings after the post date has passed</li>', NSAAConfig::TEXT_DOMAIN) ?>
                    <li><?php _e('Auto Delete Breakfast Meetings - If checked will automatically remove the breakfast meeting groups after the post date has passed</li>', NSAAConfig::TEXT_DOMAIN) ?>
                    <li><?php _e('Auto Delete Cakes - If checked will automatically remove the cakes after the post date has passed</li>', NSAAConfig::TEXT_DOMAIN) ?>
                    <li><?php _e('Auto Delete Cancelled Meetings - If checked will automatically remove the cancelled meetings after the post date has passed</li>', NSAAConfig::TEXT_DOMAIN) ?>
                    <li><?php _e('Auto Delete Events - If checked will automatically remove the events after the post date has passed</li>', NSAAConfig::TEXT_DOMAIN) ?>
                    <li><?php _e('Auto Delete Gratitude Meetings - If checked will automatically remove the gratitude meetings after the post date has passed</li>', NSAAConfig::TEXT_DOMAIN) ?>
                </ul>
                <hr/>
                <h3><?php _e('Use Google Maps', NSAAConfig::TEXT_DOMAIN) ?></h3>
                <p>
                    <?php _e('If checked will display a link to Google Maps for the meeting location.', NSAAConfig::TEXT_DOMAIN) ?>
                </p>
                <hr/>
                <h3><?php _e('Tracking/Analytics Code', NSAAConfig::TEXT_DOMAIN) ?></h3>
                <p>
                    <?php _e('This section is used for all the analytics/tracking codes used by the various sites.
                    This will be the unique identifier that is inserted into the script used by the site.', NSAAConfig::TEXT_DOMAIN) ?>
                </p>
                <p>
                    <?php _e('Google Analytics Tracking Code - Typically UA-XXXXXXX-X', NSAAConfig::TEXT_DOMAIN) ?>
                </p>
                <hr/>
                <h3><?php _e('Debugging', NSAAConfig::TEXT_DOMAIN) ?></h3>
                <p>
                    <?php _e('If checked will display the template used by the page and enable debug printing to the debug log.', NSAAConfig::TEXT_DOMAIN) ?>
                </p>
            </div>
        </li>

        <li class="tab">
            <input type="radio" name="tabs" id="tab3" />
            <label for="tab3"><?php _e('Custom Post Types', NSAAConfig::TEXT_DOMAIN) ?></label>   
            <div id="tab-content3" class="content">
                <h2><?php _e('Meeting Custom Post Types', NSAAConfig::TEXT_DOMAIN) ?></h2>
                <p>
                    <?php _e('This custom post type is used to enter the data for
                             the meeting which is used on the meeting page and in various other pages on the site.', NSAAConfig::TEXT_DOMAIN) ?>
                </p>
                <p>
                    <?php _e('The following fields are used in the custom post type.', NSAAConfig::TEXT_DOMAIN) ?>
                </p>
                <ul>
                    <li><strong><?php _e('Title', NSAAConfig::TEXT_DOMAIN) ?></strong><?php _e(' (required) - Name of the meeting', NSAAConfig::TEXT_DOMAIN) ?></li>
                    <li><strong><?php _e('Meeting Information', NSAAConfig::TEXT_DOMAIN) ?></strong>
                        <ul>
                            <li><?php _e('Location - Physical location (church/building/etc)', NSAAConfig::TEXT_DOMAIN) ?></li>
                            <li><?php _e('Street Address - (required)', NSAAConfig::TEXT_DOMAIN) ?></li>
                            <li><?php _e('City - (required)', NSAAConfig::TEXT_DOMAIN) ?></li>
                            <li><?php _e('Meeting Day - (1 or more required)', NSAAConfig::TEXT_DOMAIN) ?></li>
                            <li><?php _e('Monthly Repeat - Normally for a service meeting held once a month', NSAAConfig::TEXT_DOMAIN) ?></li>
                            <li><?php _e('Meeting Time - (required)', NSAAConfig::TEXT_DOMAIN) ?></li>
                            <li><?php _e('Not Held In Months - Normally for service meetings as they are not held all months', NSAAConfig::TEXT_DOMAIN) ?></li>
                            <li><?php _e('Meeting Legend - Check as many as ar required', NSAAConfig::TEXT_DOMAIN) ?></li>
                            <li><?php _e('Additional Meeting Information - Other information that will be displayed below the meeting', NSAAConfig::TEXT_DOMAIN) ?></li>
                        </ul>
                    </li>
                </ul>
                <hr/>
                <h2><?php _e('Cities Custom Post Type', NSAAConfig::TEXT_DOMAIN) ?></h2>
                <p>
                    <?php _e('This custom post type is used to create the city in the District.', NSAAConfig::TEXT_DOMAIN) ?>
                </p>
                <p>
                    <?php _e('The following fields are used in the custom post type.', NSAAConfig::TEXT_DOMAIN) ?>
                </p>
                <ul>
                    <li><strong><?php _e('Title', NSAAConfig::TEXT_DOMAIN) ?></strong><?php _e(' (required) - Name of the city', NSAAConfig::TEXT_DOMAIN) ?></li>
                </ul>
                <hr/>
                <h2><?php _e('Added Meeting Custom Post Type', NSAAConfig::TEXT_DOMAIN) ?></h2>
                <p>
                    <?php _e('This custom post type is used to enter the data for meetings that are added.', NSAAConfig::TEXT_DOMAIN) ?>
                </p>
                <p>
                    <strong><?php _e('This post will be automatically deleted after the meeting date.', NSAAConfig::TEXT_DOMAIN) ?></strong>
                </p>
                <p>
                    <?php _e('The following fields are used in the custom post type.', NSAAConfig::TEXT_DOMAIN) ?>
                </p>
                <ul>
                    <li><strong><?php _e('Title', NSAAConfig::TEXT_DOMAIN) ?></strong><?php _e(' (required) - Title of the added meeting', NSAAConfig::TEXT_DOMAIN) ?></li>
                    <li><strong><?php _e('Added Meeting Information', NSAAConfig::TEXT_DOMAIN) ?></strong>
                        <ul>
                            <li><?php _e('Added Meeting Date - (required) - The date of the added meeting', NSAAConfig::TEXT_DOMAIN) ?></li>
                            <li><?php _e('Group - (required) - The group that is holding the meeting', NSAAConfig::TEXT_DOMAIN) ?></li>
                        </ul>
                    </li>
                </ul>
                <hr/>
                <h2><?php _e('Breakfast Meeting Custom Post Type', NSAAConfig::TEXT_DOMAIN) ?></h2>
                <p>
                    <?php _e('This custom post type is used to enter the name of the group that is sponsoring the Sunday Morning Breakfast Meeting.', NSAAConfig::TEXT_DOMAIN) ?>
                </p>
                <p>
                    <strong><?php _e('This post will be automatically deleted after the meeting date.', NSAAConfig::TEXT_DOMAIN) ?></strong>
                </p>
               <p>
                    <?php _e('The following fields are used in the custom post type.', NSAAConfig::TEXT_DOMAIN) ?>
                </p>
                <ul>
                    <li><strong><?php _e('Title', NSAAConfig::TEXT_DOMAIN) ?></strong><?php _e(' (required) - The Breakfast Meeting', NSAAConfig::TEXT_DOMAIN) ?></li>
                    <li><strong><?php _e('Breakfast Meeting Information', NSAAConfig::TEXT_DOMAIN) ?></strong>
                        <ul>
                            <li><?php _e('Breakfast Meeting Date - (required) - The month and year of the breakfast meeting', NSAAConfig::TEXT_DOMAIN) ?></li>
                            <li><?php _e('Group - (required) - The group that is sponsoring the meeting', NSAAConfig::TEXT_DOMAIN) ?></li>
                        </ul>
                    </li>
                </ul>
                <hr/>
                <h2><?php _e('Cake Custom Post Type', NSAAConfig::TEXT_DOMAIN) ?></h2>
                <p>
                    <?php _e('This custom post type is used to enter the name/date/milestone and group of a member taking a cake.', NSAAConfig::TEXT_DOMAIN) ?>
                </p>
                <p>
                    <strong><?php _e('This post will be automatically deleted after the cake date.', NSAAConfig::TEXT_DOMAIN) ?></strong>
                </p>
                <p>
                    <?php _e('The following fields are used in the custom post type.', NSAAConfig::TEXT_DOMAIN) ?>
                </p>
                <ul>
                    <li><strong><?php _e('Title', NSAAConfig::TEXT_DOMAIN) ?></strong><?php _e(' (required) - The name of the member taking the cake', NSAAConfig::TEXT_DOMAIN) ?></li>
                    <li><strong><?php _e('Cake Information', NSAAConfig::TEXT_DOMAIN) ?></strong>
                        <ul>
                            <li><?php _e('Cake Date - (required) - The date of the cake', NSAAConfig::TEXT_DOMAIN) ?></li>
                            <li><?php _e('Milestone - (required) - The number of years for the cake', NSAAConfig::TEXT_DOMAIN) ?></li>
                            <li><?php _e('Group - (required) - The group that cake is happening at', NSAAConfig::TEXT_DOMAIN) ?></li>
                        </ul>
                    </li>
                </ul>
                <hr/>
                <h2><?php _e('Cancelled Meeting Custom Post Type', NSAAConfig::TEXT_DOMAIN) ?></h2>
                <p>
                    <?php _e('This custom post type is used to enter the data for meetings that are cancelled.', NSAAConfig::TEXT_DOMAIN) ?>
                </p>
                <p>
                    <strong><?php _e('This post will be automatically deleted after the meeting date.', NSAAConfig::TEXT_DOMAIN) ?></strong>
                </p>
                <p>
                    <?php _e('The following fields are used in the custom post type.', NSAAConfig::TEXT_DOMAIN) ?>
                </p>
                <ul>
                    <li><strong><?php _e('Title', NSAAConfig::TEXT_DOMAIN) ?></strong><?php _e(' (required) - Title of the cancelled meeting', NSAAConfig::TEXT_DOMAIN) ?></li>
                    <li><strong><?php _e('Cancelled Meeting Information', NSAAConfig::TEXT_DOMAIN) ?></strong>
                        <ul>
                            <li><?php _e('Cancelled Meeting Date - (required) - The date of the cancelled meeting', NSAAConfig::TEXT_DOMAIN) ?></li>
                            <li><?php _e('Group - (required) - The group that is holding the meeting', NSAAConfig::TEXT_DOMAIN) ?></li>
                        </ul>
                    </li>
                </ul>
                <hr/>
                <h2><?php _e('Events Custom Post Type', NSAAConfig::TEXT_DOMAIN) ?></h2>
                <p>
                    <?php _e('This custom post type is used to enter the data for district events.', NSAAConfig::TEXT_DOMAIN) ?>
                </p>
                <p>
                    <strong><?php _e('This post will be automatically deleted after the event.', NSAAConfig::TEXT_DOMAIN) ?></strong>
                </p>
                <p>
                    <?php _e('The following fields are used in the custom post type.', NSAAConfig::TEXT_DOMAIN) ?>
                </p>
                <ul>
                    <li><strong><?php _e('Title', NSAAConfig::TEXT_DOMAIN) ?></strong><?php _e(' (required) - The name of the event', NSAAConfig::TEXT_DOMAIN) ?></li>
                    <li><strong><?php _e('Content', NSAAConfig::TEXT_DOMAIN) ?></strong><?php _e(' Information about the event', NSAAConfig::TEXT_DOMAIN) ?></li>
                    <li><strong><?php _e('Featured Image', NSAAConfig::TEXT_DOMAIN) ?></strong><?php _e(' Poster for the event', NSAAConfig::TEXT_DOMAIN) ?></li>
                    <li><strong><?php _e('Event Information', NSAAConfig::TEXT_DOMAIN) ?></strong>
                        <ul>
                            <li><?php _e('Start Date - (required) - The start date of the event', NSAAConfig::TEXT_DOMAIN) ?></li>
                            <li><?php _e('End Date - (required) - The end date of the event', NSAAConfig::TEXT_DOMAIN) ?></li>
                        </ul>
                    </li>
                </ul>
                <hr/>
                <h2><?php _e('Gratitude Night Custom Post Type', NSAAConfig::TEXT_DOMAIN) ?></h2>
                <p>
                    <?php _e('This custom post type is used to enter the data for a gratitude night.', NSAAConfig::TEXT_DOMAIN) ?>
                </p>
                <p>
                    <strong><?php _e('This post will be automatically deleted after the gratitude meeting date.', NSAAConfig::TEXT_DOMAIN) ?></strong>
                </p>
                <p>
                    <?php _e('The following fields are used in the custom post type.', NSAAConfig::TEXT_DOMAIN) ?>
                </p>
                <ul>
                    <li><strong><?php _e('Title', NSAAConfig::TEXT_DOMAIN) ?></strong><?php _e(' (required) - The name of the gratitude event', NSAAConfig::TEXT_DOMAIN) ?></li>
                    <li><strong><?php _e('Featured Image', NSAAConfig::TEXT_DOMAIN) ?></strong><?php _e(' Poster for the gratitude event', NSAAConfig::TEXT_DOMAIN) ?></li>
                    <li><strong><?php _e('Gratitude Information', NSAAConfig::TEXT_DOMAIN) ?></strong>
                        <ul>
                            <li><?php _e('Gratitude Date - (required) - The date of the gratitude event', NSAAConfig::TEXT_DOMAIN) ?></li>
                            <li><?php _e('Gratitude Time - (required) - The time of the gratitude event', NSAAConfig::TEXT_DOMAIN) ?></li>
                            <li><?php _e('Additional Information - Any additional information for the gratitude event', NSAAConfig::TEXT_DOMAIN) ?></li>
                            <li><?php _e('Group - (required) - The group holding the gratitude event', NSAAConfig::TEXT_DOMAIN) ?></li>
                        </ul>
                    </li>
                </ul>
                <hr/>
                <h2><?php _e('Meeting Change Custom Post Type', NSAAConfig::TEXT_DOMAIN) ?></h2>
                <p>
                    <?php _e('This custom post type is used to enter the data for a changed meetings.', NSAAConfig::TEXT_DOMAIN) ?>
                </p>
                <p>
                    <?php _e('The following fields are used in the custom post type.', NSAAConfig::TEXT_DOMAIN) ?>
                </p>
                <ul>
                    <li><strong><?php _e('Title', NSAAConfig::TEXT_DOMAIN) ?></strong><?php _e(' (required) - The name of the changed meeting', NSAAConfig::TEXT_DOMAIN) ?></li>
                    <li><strong><?php _e('Content', NSAAConfig::TEXT_DOMAIN) ?></strong><?php _e(' (required) - The information about the changed meeting', NSAAConfig::TEXT_DOMAIN) ?></li>
                </ul>
                <hr/>
                <h2><?php _e('Service Op Custom Post Type', NSAAConfig::TEXT_DOMAIN) ?></h2>
                <p>
                    <?php _e('This custom post type is used to enter the data for a service opportunity.', NSAAConfig::TEXT_DOMAIN) ?>
                </p>
                <p>
                    <?php _e('The following fields are used in the custom post type.', NSAAConfig::TEXT_DOMAIN) ?>
                </p>
                <ul>
                    <li><strong><?php _e('Title', NSAAConfig::TEXT_DOMAIN) ?></strong><?php _e(' (required) - The name of the service opportunity', NSAAConfig::TEXT_DOMAIN) ?></li>
                    <li><strong><?php _e('Content', NSAAConfig::TEXT_DOMAIN) ?></strong><?php _e(' (required) - The information about the service opportunity', NSAAConfig::TEXT_DOMAIN) ?></li>
                </ul>
            </div>
        </li>

        <li class="tab">
            <input type="radio" name="tabs" id="tab4" />
            <label for="tab4"><?php _e('Shortcodes', NSAAConfig::TEXT_DOMAIN) ?></label>   
            <div id="tab-content4" class="content">
                <h2><?php _e('Shortcodes', NSAAConfig::TEXT_DOMAIN) ?></h2>
                <?php NSAAShortcodes::getShortcodeInstructions(); ?>
                <hr/>
                <h2><?php _e('Modify\Remove Shortcode', NSAAConfig::TEXT_DOMAIN) ?></h2>
                <?php NSAAShortcodes::getRemovalInstructions(); ?>
            </div>
        </li>

    </ul>


</div><!-- .wrap -->