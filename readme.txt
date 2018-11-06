=== Event Organiser ICS Feed for Apple Calendar ===
Contributors: needle
Donate link: https://www.paypal.me/interactivist
Tags: civicrm, event organiser, events, sync, apple calendar
Requires at least: 4.9
Tested up to: 4.9
Stable tag: 0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Provides an ICS feed for the Event Organiser plugin that can be read by Apple Calendar.



== Description ==

A WordPress plugin which provides an ICS feed for the Event Organiser plugin that can be read by Apple Calendar.

### Requirements

This plugin requires:

* [Event Organiser](http://wordpress.org/plugins/event-organiser/) version 3.0 or greater

### Shortcode

This plugin provides the `[eo_apple_subscribe]` shortcode which can be used to generate a link to the calendar feed. It can be used like this:

```
[eo_apple_subscribe title="Subscribe with Apple Calendar" class="apple_cal_subscribe"]Subscribe with Apple Calendar[/eo_apple_subscribe]
```

The resulting markup will look something like:

```
<a href="webcal://your.domain/feed/eo-apple/" target="_blank" title="Subscribe with Apple Calendar" class="apple_cal_subscribe">Subscribe with Apple Calendar</a>
```

At present the shortcode only supports the full calendar.

### Plugin Development

For feature requests and bug reports (or if you're a plugin author and want to contribute) please visit the plugin's [GitHub repository](https://github.com/christianwach/event-organiser-apple-cal).

### 404 Errors

If you get a 404 Not Found error when visiting the feed, then go to your WordPress *Settings > Permalinks* page and (optionally) hit *Save*.



== Installation ==

1. Extract the plugin archive
1. Upload plugin files to your `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress


== Changelog ==

See https://github.com/christianwach/event-organiser-apple/commits/master
