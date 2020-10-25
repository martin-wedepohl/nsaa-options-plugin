# nsaa-options-plugin
### North Shore AA Options Plugin

* Contributors: [martinwedepohl](https://en.gravatar.com/martinwedepohl) 
* Tags: options, wedepohl engineering
* Requires at least: 4.7 or higher
* Tested up to: 5.3.2
* Stable tag: 0.1.12
* License: [GPLv3 or later](https://www.gnu.org/licenses/gpl-3.0.html)

### Description
North Shore AA Options Plugin is used to provide a set of options, custom post types, templates and shortcodes used for the [North Shore AA Website](https://northshoreaa.org).

### Privacy Notices
This plugin doesn't store any user data

### Installation For Development
- Download/clone the plugin to a development directory on the website at the root level (same level as the /wp-content directory)
- From the command line in the newly created directory issue the following commands
```
npm install
composer install
gulp watch
```
- This will copy any changes made in the development directory to the /wp-content/plugins/nsaa-options-plugin directory
- The plugin can be activated through the plugins page
- If any new classes are added to the development area issue the following command
```
composer dump
```

### Frequently Asked Questions

### Screenshots

### Changelog

###### 0.1.12 2020-10-25
* Enhancement: Ability to have no address (won't display Google Maps Link)

###### 0.1.11 2020-05-26
* Enhancement: Added ability to have both in person and online meetings

###### 0.1.10 2020-04-05
* FIX: Zoom links broke Google Maps
* Enhancement: Added Google Maps to all meeting pages

###### 0.1.9 2020-04-05
* Enhancement: Ability to have passwords and custom links for zoom meetings

###### 2020-03-21
* Enhancement: Ability to have online meetings

###### 0.1.8 2020-01-26
* FIX: Commented out disabling REST API since Contact Form 7 requires it

###### 0.1.7 2019-12-29
* Display gratitude nights on the events page and calendar

###### 0.1.6 2019-12-29
* Added script to put Google Analytics in the head section
* Remove comments from the website
* Remove redundant code in the head section
* Allow the REST API to only be accessed by logged in users

###### 0.1.5 2019-12-28
* Added script to automatically select who to email
* Scripts only loaded on specific pages
* Scripts are now using strict mode
* Bumpted version to 0.1.5

###### 0.1.4 2019-12-28
* FIX: Added missing delete_cron functions, fixed typo
* Improved instructions
* Bumped version to 0.1.4

###### 0.1.3 2019-12-25
* Ability to auto delete meetings/events through options page
* Remove auto delete crons on plugin deactivation
* Correctly select the next service meeting if current month has passed
* Added CPC/PI page
* Added more instructions for the plugin
* Bumped version to 0.1.3

###### 0.1.2 2019-12-22
* Added Breakfast Meetings
* Added Cancelled Meetings
* Added Events
* Added Changed Meetings
* Changed crons from hourly to daily
* Bumped version to 0.1.2

###### 0.1.1 2019-12-19
* Added calendar shortcode

###### 0.1.0 2019-12-19
* Original issue
