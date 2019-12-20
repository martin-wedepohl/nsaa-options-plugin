# nsaa-options-plugin
### North Shore AA Options Plugin

Contributors: martinwedepohl 
Tags: options, wedepohl engineering
Requires at least: 4.7 or higher
Tested up to: 5.3.2
Stable tag: 0.1.0
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

### Description
North Shore AA Options Plugin is used to provide a set of options, custom post types, templates and shortcodes used for the https://northshoreaa.org website.

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

###### 0.1.0 2019-12-19
* Original issue
