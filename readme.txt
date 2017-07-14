=== Plugin Name ===
Contributors: brandontreb
Donate link: https://www.paypal.com/us/cgi-bin/webscr?cmd=_flow&SESSION=K4UfsmA29c2Xq9gCjQ_98OK5byh3UyGg2nom0ygK12e5i2KuRS8iuA4bu58&dispatch=5885d80a13c0db1fca8cb0621aa94a5fc157eca86dc6e6adbec4b69650d8a3ec
Tags: Feedburner, subscriber count, rss, count, custom feedburner
Requires at least: 2.3
Tested up to: 3.0.1
Version: 1.1
Stable Tag: 1.1

Allows you to display your Feedburner subscriber count in a widget or in your posts without having to use Feedburner's not so beautiful icon :)

== Description ==

This plugin allows you to output your feedburner subscriber count anywhere on your blog. All you need to do is put the [feedburner] tag in a post. It also gives you the ability to add a (text) widget that allows you to add the feedburner tag as well.

This plugin integrates with Worpdress' built-in scheduling system to cache the subscriber count. This allows it to be displayed quickly and efficiently.

[http://brandontreb.com](http://brandontreb.com)

The URL below is demonstrating its use in the widget.

**Note** Sometimes the Feedburner API is unreliable and will return 0 results.  If it doesn't work the first time, just wait a while and try again.

== Installation ==

This section describes how to install the plugin and get it working.

1. Download the Feedburner Anywhere plugin for Wordpress
1. Upload it to your wp-content/plugins folder
1. Activate it in your plugin admin page
1. Click on the Feedburner Anywhere link under settings in WP admin
1. Add your feedburner URL and click Save settings
1. Make sure that you have the Feedburner Awareness API enabled [Part 1 of this tutorial](http://brandontreb.com/displaying-you-feedburner-subscriber-count-anywhere-php-coding-tutorial/)
1. Click Get Feedburner Data

Displaying your Feedburner subscriber count using the widget

1. Click on Widgets under the Appearance menu in the WP admin.
1. Drag the Feedburner Count widget to your widget area on the right.
1. Put whatever you want in the text box and type a %d in the areas where you want to display your subscriber count.  For example, you could type "I have %d subscribers".
1. Click Save

Displaying your Feedburner subscriber count in a post

1. Simply type [feedburner] anywhere in a post or page where you want to display your Feedburner subscriber count.

== Screenshots ==

Check out [this page](http://brandontreb.com/) to see the plugin in action.  (Look at the sidebar)

== Changelog ==

= 1.1 =
* If Feedburner returns 0 results (which it will sometimes :( ), the plugin now keeps the previously retrieved value.  Prevents your blog from displaying that you have 0 readers.

= 1.0 =
* Initial Version


