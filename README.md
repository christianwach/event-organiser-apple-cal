Event Organiser ICS Feed for Apple Calendar
===========================================

A *WordPress* plugin which provides an ICS feed for the *Event Organiser* plugin that can be read by *Apple Calendar*.



### Notes ###

#### Requirements ####

This plugin requires:

* [Event Organiser](http://wordpress.org/plugins/event-organiser/) version 3.0 or greater

#### Shortcode ####

This plugin provides the `[eo_apple_subscribe]` shortcode which can be used to generate a link to the calendar feed. It can be used like this:

```
[eo_apple_subscribe title="Subscribe with Apple Calendar" class="apple_cal_subscribe"]Subscribe with Apple Calendar[/eo_apple_subscribe]
```

The resulting markup will look something like:

```
<a href="webcal://your.domain/feed/eo-apple/" target="_blank" title="Subscribe with Apple Calendar" class="apple_cal_subscribe">Subscribe with Apple Calendar</a>
```

At present the shortcode only supports the full calendar.

#### 404 Errors ####

If you get a 404 Not Found error when visiting the feed, then go to your WordPress *Settings > Permalinks* page and (optionally) hit *Save*.

#### Development ####

For feature requests and bug reports (or if you're a plugin author and want to contribute) please open an Issue or a PR.



### Installation ###

There are two ways to install from GitHub:

###### ZIP Download ######

If you have downloaded *Event Organiser ICS Feed for Apple Calendar* as a ZIP file from the GitHub repository, do the following to install and activate the plugin:

1. Unzip the .zip file and rename the enclosing folder so that the plugin's files are located directly inside `/wp-content/plugins/event-organiser-apple`
2. Activate the plugin (if on WP multisite, activate the plugin to match where *Event Organiser* is activated)
3. Go to the plugin's admin page and follow the instructions
4. You are done!

###### git clone ######

If you have cloned the code from GitHub, it is assumed that you know what you're doing.
