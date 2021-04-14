<?php

namespace NSAAOptions;

use NSAAOptions\NSAAShortcodes;

?>
<div class="wrap">
	<h2><?php esc_html_e( 'North Shore AA Options Plugin Instructions', 'nsaa-options' ); ?></h2>
	<ul class="tabs">

		<li class="tab">
			<input type="radio" name="tabs" checked="checked" id="tab1" />
			<label for="tab1"><?php esc_html_e( 'Themes/Plugins', 'nsaa-options' ); ?></label>
			<div id="tab-content1" class="content">
				<h2><?php esc_html_e( 'Themes/Plugins Required', 'nsaa-options' ); ?></h2>
				<hr/>
				<h3><?php esc_html_e( 'Themes', 'nsaa-options' ); ?></h3>
				<ul>
					<li><?php esc_html_e( 'OceanWP - Base Theme', 'nsaa-options' ); ?></li>
					<li><?php esc_html_e( 'North Shore AA - Child Theme of OceanWP', 'nsaa-options' ); ?></li>
					<li><?php esc_html_e( 'Twenty Twenty - Default WordPress theme just in case there are issues', 'nsaa-options' ); ?></li>
				</ul>
				<hr />
				<h3><?php esc_html_e( 'Plugins', 'nsaa-options' ); ?></h3>
				<ul>
					<li><?php esc_html_e( 'Contact Form 7 - Display contact forms for emailing', 'nsaa-options' ); ?></li>
					<li><?php esc_html_e( 'Duplicator - Website backup and migration', 'nsaa-options' ); ?></li>
					<li><?php esc_html_e( 'Elementor - Page builder for the pages', 'nsaa-options' ); ?></li>
					<li><?php esc_html_e( 'North Shore AA Options Plugin - Required options/custom post types/shortcodes', 'nsaa-options' ); ?></li>
					<li><?php esc_html_e( 'Ocean Extra - Extra features for the OceanWP Theme', 'nsaa-options' ); ?></li>
					<li><?php esc_html_e( 'Ocean Stick Anything - Used to stick the header on the OceanWP Theme', 'nsaa-options' ); ?></li>
					<li><?php esc_html_e( 'Rank Math SEO - SEO/Redirection/404 Logging', 'nsaa-options' ); ?></li>
					<li><?php esc_html_e( 'WP-Optimize - Database optimization/caching/compression', 'nsaa-options' ); ?></li>
				</ul>
			</div>
		</li>

		<li class="tab">
			<input type="radio" name="tabs" id="tab2" />
			<label for="tab2"><?php esc_html_e( 'Settings', 'nsaa-options' ); ?></label>
			<div id="tab-content2" class="content">
				<h2><?php esc_html_e( 'Settings', 'nsaa-options' ); ?></h2>
				<hr/>
				<h3><?php esc_html_e( 'Auto Delete', 'nsaa-options' ); ?></h3>
				<p>
					<?php esc_html_e( 'If checked will auto delete custom post types once the date of the post has passed.', 'nsaa-options' ); ?>
				</p>
				<ul>
					<li><?php esc_html_e( 'Auto Delete Added Meetings - If checked will automatically remove the added meetings after the post date has passed</li>', 'nsaa-options' ); ?>
					<li><?php esc_html_e( 'Auto Delete Breakfast Meetings - If checked will automatically remove the breakfast meeting groups after the post date has passed</li>', 'nsaa-options' ); ?>
					<li><?php esc_html_e( 'Auto Delete Cakes - If checked will automatically remove the cakes after the post date has passed</li>', 'nsaa-options' ); ?>
					<li><?php esc_html_e( 'Auto Delete Cancelled Meetings - If checked will automatically remove the cancelled meetings after the post date has passed</li>', 'nsaa-options' ); ?>
					<li><?php esc_html_e( 'Auto Delete Events - If checked will automatically remove the events after the post date has passed</li>', 'nsaa-options' ); ?>
					<li><?php esc_html_e( 'Auto Delete Gratitude Meetings - If checked will automatically remove the gratitude meetings after the post date has passed</li>', 'nsaa-options' ); ?>
				</ul>
				<hr/>
				<h3><?php esc_html_e( 'Use Google Maps', 'nsaa-options' ); ?></h3>
				<p>
					<?php esc_html_e( 'If checked will display a link to Google Maps for the meeting location.', 'nsaa-options' ); ?>
				</p>
				<hr/>
				<h3><?php esc_html_e( 'Tracking/Analytics Code', 'nsaa-options' ); ?></h3>
				<p>
					<?php
						esc_html_e(
							'This section is used for all the analytics/tracking codes used by the various sites. This will be the unique identifier that is inserted into the script used by the site.',
							'nsaa-options'
						);
					?>
				</p>
				<p>
					<?php esc_html_e( 'Google Analytics Tracking Code - Typically UA-XXXXXXX-X', 'nsaa-options' ); ?>
				</p>
				<hr/>
				<h3><?php esc_html_e( 'Debugging', 'nsaa-options' ); ?></h3>
				<p>
					<?php esc_html_e( 'If checked will display the template used by the page and enable debug printing to the debug log.', 'nsaa-options' ); ?>
				</p>
			</div>
		</li>

		<li class="tab">
			<input type="radio" name="tabs" id="tab3" />
			<label for="tab3"><?php esc_html_e( 'Custom Post Types', 'nsaa-options' ); ?></label>   
			<div id="tab-content3" class="content">
				<h2><?php esc_html_e( 'Meeting Custom Post Types', 'nsaa-options' ); ?></h2>
				<p>
					<?php
						esc_html_e(
							'This custom post type is used to enter the data for the meeting which is used on the meeting page and in various other pages on the site.',
							'nsaa-options'
						);
					?>
				</p>
				<p>
					<?php esc_html_e( 'The following fields are used in the custom post type.', 'nsaa-options' ); ?>
				</p>
				<ul>
					<li><strong><?php esc_html_e( 'Title', 'nsaa-options' ); ?></strong><?php esc_html_e( ' (required) - Name of the meeting', 'nsaa-options' ); ?></li>
					<li><strong><?php esc_html_e( 'Meeting Information', 'nsaa-options' ); ?></strong>
						<ul>
							<li><?php esc_html_e( 'Location - Physical location (church/building/etc)', 'nsaa-options' ); ?></li>
							<li><?php esc_html_e( 'Street Address - (required)', 'nsaa-options' ); ?></li>
							<li><?php esc_html_e( 'City - (required)', 'nsaa-options' ); ?></li>
							<li><?php esc_html_e( 'Meeting Day - (1 or more required)', 'nsaa-options' ); ?></li>
							<li><?php esc_html_e( 'Monthly Repeat - Normally for a service meeting held once a month', 'nsaa-options' ); ?></li>
							<li><?php esc_html_e( 'Meeting Time - (required)', 'nsaa-options' ); ?></li>
							<li><?php esc_html_e( 'Not Held In Months - Normally for service meetings as they are not held all months', 'nsaa-options' ); ?></li>
							<li><?php esc_html_e( 'Meeting Legend - Check as many as ar required', 'nsaa-options' ); ?></li>
							<li><?php esc_html_e( 'Additional Meeting Information - Other information that will be displayed below the meeting', 'nsaa-options' ); ?></li>
						</ul>
					</li>
				</ul>
				<hr/>
				<h2><?php esc_html_e( 'Cities Custom Post Type', 'nsaa-options' ); ?></h2>
				<p>
					<?php esc_html_e( 'This custom post type is used to create the city in the District.', 'nsaa-options' ); ?>
				</p>
				<p>
					<?php esc_html_e( 'The following fields are used in the custom post type.', 'nsaa-options' ); ?>
				</p>
				<ul>
					<li><strong><?php esc_html_e( 'Title', 'nsaa-options' ); ?></strong><?php esc_html_e( ' (required) - Name of the city', 'nsaa-options' ); ?></li>
				</ul>
				<hr/>
				<h2><?php esc_html_e( 'Added Meeting Custom Post Type', 'nsaa-options' ); ?></h2>
				<p>
					<?php esc_html_e( 'This custom post type is used to enter the data for meetings that are added.', 'nsaa-options' ); ?>
				</p>
				<p>
					<strong><?php esc_html_e( 'This post will be automatically deleted after the meeting date.', 'nsaa-options' ); ?></strong>
				</p>
				<p>
					<?php esc_html_e( 'The following fields are used in the custom post type.', 'nsaa-options' ); ?>
				</p>
				<ul>
					<li><strong><?php esc_html_e( 'Title', 'nsaa-options' ); ?></strong><?php esc_html_e( ' (required) - Title of the added meeting', 'nsaa-options' ); ?></li>
					<li><strong><?php esc_html_e( 'Added Meeting Information', 'nsaa-options' ); ?></strong>
						<ul>
							<li><?php esc_html_e( 'Added Meeting Date - (required) - The date of the added meeting', 'nsaa-options' ); ?></li>
							<li><?php esc_html_e( 'Group - (required) - The group that is holding the meeting', 'nsaa-options' ); ?></li>
						</ul>
					</li>
				</ul>
				<hr/>
				<h2><?php esc_html_e( 'Breakfast Meeting Custom Post Type', 'nsaa-options' ); ?></h2>
				<p>
					<?php esc_html_e( 'This custom post type is used to enter the name of the group that is sponsoring the Sunday Morning Breakfast Meeting.', 'nsaa-options' ); ?>
				</p>
				<p>
					<strong><?php esc_html_e( 'This post will be automatically deleted after the meeting date.', 'nsaa-options' ); ?></strong>
				</p>
			<p>
					<?php esc_html_e( 'The following fields are used in the custom post type.', 'nsaa-options' ); ?>
				</p>
				<ul>
					<li><strong><?php esc_html_e( 'Title', 'nsaa-options' ); ?></strong><?php esc_html_e( ' (required) - The Breakfast Meeting', 'nsaa-options' ); ?></li>
					<li><strong><?php esc_html_e( 'Breakfast Meeting Information', 'nsaa-options' ); ?></strong>
						<ul>
							<li><?php esc_html_e( 'Breakfast Meeting Date - (required) - The month and year of the breakfast meeting', 'nsaa-options' ); ?></li>
							<li><?php esc_html_e( 'Group - (required) - The group that is sponsoring the meeting', 'nsaa-options' ); ?></li>
						</ul>
					</li>
				</ul>
				<hr/>
				<h2><?php esc_html_e( 'Cake Custom Post Type', 'nsaa-options' ); ?></h2>
				<p>
					<?php esc_html_e( 'This custom post type is used to enter the name/date/milestone and group of a member taking a cake.', 'nsaa-options' ); ?>
				</p>
				<p>
					<strong><?php esc_html_e( 'This post will be automatically deleted after the cake date.', 'nsaa-options' ); ?></strong>
				</p>
				<p>
					<?php esc_html_e( 'The following fields are used in the custom post type.', 'nsaa-options' ); ?>
				</p>
				<ul>
					<li><strong><?php esc_html_e( 'Title', 'nsaa-options' ); ?></strong><?php esc_html_e( ' (required) - The name of the member taking the cake', 'nsaa-options' ); ?></li>
					<li><strong><?php esc_html_e( 'Cake Information', 'nsaa-options' ); ?></strong>
						<ul>
							<li><?php esc_html_e( 'Cake Date - (required) - The date of the cake', 'nsaa-options' ); ?></li>
							<li><?php esc_html_e( 'Milestone - (required) - The number of years for the cake', 'nsaa-options' ); ?></li>
							<li><?php esc_html_e( 'Group - (required) - The group that cake is happening at', 'nsaa-options' ); ?></li>
						</ul>
					</li>
				</ul>
				<hr/>
				<h2><?php esc_html_e( 'Cancelled Meeting Custom Post Type', 'nsaa-options' ); ?></h2>
				<p>
					<?php esc_html_e( 'This custom post type is used to enter the data for meetings that are cancelled.', 'nsaa-options' ); ?>
				</p>
				<p>
					<strong><?php esc_html_e( 'This post will be automatically deleted after the meeting date.', 'nsaa-options' ); ?></strong>
				</p>
				<p>
					<?php esc_html_e( 'The following fields are used in the custom post type.', 'nsaa-options' ); ?>
				</p>
				<ul>
					<li><strong><?php esc_html_e( 'Title', 'nsaa-options' ); ?></strong><?php esc_html_e( ' (required) - Title of the cancelled meeting', 'nsaa-options' ); ?></li>
					<li><strong><?php esc_html_e( 'Cancelled Meeting Information', 'nsaa-options' ); ?></strong>
						<ul>
							<li><?php esc_html_e( 'Cancelled Meeting Date - (required) - The date of the cancelled meeting', 'nsaa-options' ); ?></li>
							<li><?php esc_html_e( 'Group - (required) - The group that is holding the meeting', 'nsaa-options' ); ?></li>
						</ul>
					</li>
				</ul>
				<hr/>
				<h2><?php esc_html_e( 'Events Custom Post Type', 'nsaa-options' ); ?></h2>
				<p>
					<?php esc_html_e( 'This custom post type is used to enter the data for district events.', 'nsaa-options' ); ?>
				</p>
				<p>
					<strong><?php esc_html_e( 'This post will be automatically deleted after the event.', 'nsaa-options' ); ?></strong>
				</p>
				<p>
					<?php esc_html_e( 'The following fields are used in the custom post type.', 'nsaa-options' ); ?>
				</p>
				<ul>
					<li><strong><?php esc_html_e( 'Title', 'nsaa-options' ); ?></strong><?php esc_html_e( ' (required) - The name of the event', 'nsaa-options' ); ?></li>
					<li><strong><?php esc_html_e( 'Content', 'nsaa-options' ); ?></strong><?php esc_html_e( ' Information about the event', 'nsaa-options' ); ?></li>
					<li><strong><?php esc_html_e( 'Featured Image', 'nsaa-options' ); ?></strong><?php esc_html_e( ' Poster for the event', 'nsaa-options' ); ?></li>
					<li><strong><?php esc_html_e( 'Event Information', 'nsaa-options' ); ?></strong>
						<ul>
							<li><?php esc_html_e( 'Start Date - (required) - The start date of the event', 'nsaa-options' ); ?></li>
							<li><?php esc_html_e( 'End Date - (required) - The end date of the event', 'nsaa-options' ); ?></li>
						</ul>
					</li>
				</ul>
				<hr/>
				<h2><?php esc_html_e( 'Gratitude Night Custom Post Type', 'nsaa-options' ); ?></h2>
				<p>
					<?php esc_html_e( 'This custom post type is used to enter the data for a gratitude night.', 'nsaa-options' ); ?>
				</p>
				<p>
					<strong><?php esc_html_e( 'This post will be automatically deleted after the gratitude meeting date.', 'nsaa-options' ); ?></strong>
				</p>
				<p>
					<?php esc_html_e( 'The following fields are used in the custom post type.', 'nsaa-options' ); ?>
				</p>
				<ul>
					<li><strong><?php esc_html_e( 'Title', 'nsaa-options' ); ?></strong><?php esc_html_e( ' (required) - The name of the gratitude event', 'nsaa-options' ); ?></li>
					<li><strong><?php esc_html_e( 'Featured Image', 'nsaa-options' ); ?></strong><?php esc_html_e( ' Poster for the gratitude event', 'nsaa-options' ); ?></li>
					<li><strong><?php esc_html_e( 'Gratitude Information', 'nsaa-options' ); ?></strong>
						<ul>
							<li><?php esc_html_e( 'Gratitude Date - (required) - The date of the gratitude event', 'nsaa-options' ); ?></li>
							<li><?php esc_html_e( 'Gratitude Time - (required) - The time of the gratitude event', 'nsaa-options' ); ?></li>
							<li><?php esc_html_e( 'Additional Information - Any additional information for the gratitude event', 'nsaa-options' ); ?></li>
							<li><?php esc_html_e( 'Group - (required) - The group holding the gratitude event', 'nsaa-options' ); ?></li>
						</ul>
					</li>
				</ul>
				<hr/>
				<h2><?php esc_html_e( 'Meeting Change Custom Post Type', 'nsaa-options' ); ?></h2>
				<p>
					<?php esc_html_e( 'This custom post type is used to enter the data for a changed meetings.', 'nsaa-options' ); ?>
				</p>
				<p>
					<?php esc_html_e( 'The following fields are used in the custom post type.', 'nsaa-options' ); ?>
				</p>
				<ul>
					<li><strong><?php esc_html_e( 'Title', 'nsaa-options' ); ?></strong><?php esc_html_e( ' (required) - The name of the changed meeting', 'nsaa-options' ); ?></li>
					<li><strong><?php esc_html_e( 'Content', 'nsaa-options' ); ?></strong><?php esc_html_e( ' (required) - The information about the changed meeting', 'nsaa-options' ); ?></li>
					<li><strong><?php esc_html_e( 'Featured Image', 'nsaa-options' ); ?></strong><?php esc_html_e( ' (optional) - Featured Image', 'nsaa-options' ); ?></li>
				</ul>
				<hr/>
				<h2><?php esc_html_e( 'News Custom Post Type', 'nsaa-options' ); ?></h2>
				<p>
					<?php esc_html_e( 'This custom post type is used to enter the data for a news item.', 'nsaa-options' ); ?>
				</p>
				<p>
					<?php esc_html_e( 'The following fields are used in the custom post type.', 'nsaa-options' ); ?>
				</p>
				<ul>
					<li><strong><?php esc_html_e( 'Title', 'nsaa-options' ); ?></strong><?php esc_html_e( ' (required) - The name of the changed meeting', 'nsaa-options' ); ?></li>
					<li><strong><?php esc_html_e( 'Content', 'nsaa-options' ); ?></strong><?php esc_html_e( ' (required) - The information about the changed meeting', 'nsaa-options' ); ?></li>
					<li><strong><?php esc_html_e( 'Featured Image', 'nsaa-options' ); ?></strong><?php esc_html_e( ' (optional) - Featured Image', 'nsaa-options' ); ?></li>
				</ul>
				<hr/>
				<h2><?php esc_html_e( 'Service Op Custom Post Type', 'nsaa-options' ); ?></h2>
				<p>
					<?php esc_html_e( 'This custom post type is used to enter the data for a service opportunity.', 'nsaa-options' ); ?>
				</p>
				<p>
					<?php esc_html_e( 'The following fields are used in the custom post type.', 'nsaa-options' ); ?>
				</p>
				<ul>
					<li><strong><?php esc_html_e( 'Title', 'nsaa-options' ); ?></strong><?php esc_html_e( ' (required) - The name of the service opportunity', 'nsaa-options' ); ?></li>
					<li><strong><?php esc_html_e( 'Content', 'nsaa-options' ); ?></strong><?php esc_html_e( ' (required) - The information about the service opportunity', 'nsaa-options' ); ?></li>
				</ul>
			</div>
		</li>

		<li class="tab">
			<input type="radio" name="tabs" id="tab4" />
			<label for="tab4"><?php esc_html_e( 'Shortcodes', 'nsaa-options' ); ?></label>   
			<div id="tab-content4" class="content">
				<h2><?php esc_html_e( 'Shortcodes', 'nsaa-options' ); ?></h2>
				<?php NSAAShortcodes::getShortcodeInstructions(); ?>
				<hr/>
				<h2><?php esc_html_e( 'Modify\Remove Shortcode', 'nsaa-options' ); ?></h2>
				<?php NSAAShortcodes::getRemovalInstructions(); ?>
			</div>
		</li>

	</ul>


</div><!-- .wrap -->