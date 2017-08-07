=== GetGlueAPI ===
Contributors: nzguru
Author URI: http://nzguru.net
Plugin URI: http://nzguru.net/cool-stuff/getglueapi-plugin-for-wordpress
Donate link: http://nzguru.net/cool-stuff/getglue-plugin-for-wordpress
Tags: getglue,checkin,stickers,widget,sidebar,api,social,get glue,social media,,recent,checkins,latest
Requires at least: 2.7
Tested up to: 3.2.1
Stable tag: 1.0.7

The 1st plugin to integrate the GetGlue&reg; API into WordPress to show your checkins and stickers

== Description ==

This plugin uses the GetGlue<sup>&reg;</sup> API to retrieve various information and display it in a widget or directly in posts and pages via a shortcode. Information currently available includes ...

* your checkins
* your likes
* your stickers

To display your selected data in a widget, simply drag the widget to your sidebar and select the optins you want to display. Similarly, you can add the widget styled data to a page or post by using the shortcode features (see FAQ)

Your GetGlue data may be displayed with options to include stickers, likes, and statistics relating to the object. Some data also allows you display a timestamp relating to when the event occured (such as when you checked in or when you received a sticker)

== Installation ==

For an automatic installation through WordPress:

1. Go to the 'Add New' plugins screen in your WordPress admin area
2. Search for 'getglueapi'
3. Click 'Install Now' and activate the plugin
4. Go to the Settings -> GetGlueAPI admin panel and follow the instructions to connect with GetGlue<sup>&reg;</sup>.

For a manual upload installation through WordPress:

1. Download the GetGlueAPI zip file from [wordpress.org](http://wordpress.org/extend/plugins/getglueapi/)
2. Go to the 'Add New' plugins screen in your Wordpress admin area and select the 'Upload' tab
3. Browse to where you download the zip file
4. Click 'Install Now' and activate the plugin
5. Go to the Settings -> GetGlueAPI admin panel and follow the instructions to connect with GetGlue<sup>&reg;</sup>.

For a manual installation via FTP:

1. Download the GetGlueAPI zip file from [wordpress.org](http://wordpress.org/extend/plugins/getglueapi/)
2. Unzip to your local drive
3. Upload the getglueapi folder to the /wp-content/plugins/ directory on your server
4. Activate the plugin through the 'Plugins' screen in your WordPress admin area
5. Go to the Settings -> GetGlueAPI admin panel and follow the instructions to connect with GetGlue<sup>&reg;</sup>.

== Frequently Asked Questions ==

= What is GetGlue<sup>&reg;</sup>? =

GetGlue<sup>&reg;</sup> is a service that helps you find your next favorite movie, book, music album or other every day thing. GetGlue shows you things that you'll like based on your personal tastes, what your friends like, and what's most popular on GetGlue. 

= What is the GetGlue<sup>&reg;</sup> API? =

API stands for "application programming interface." APIs facilitate interaction between different software programs by making it easy for them to share data and resources.

= Can I get this plugin in a different language? =

Language translations are being added as I am able to do them. If you would like to help by completing a translation, please send [gettext PO and MO files](http://codex.wordpress.org/Translating_WordPress) to me via [NZGuru.net](http://nzguru.net/contact-me) and I will include them in the next update. You can download the latest [POT file from here](http://plugins.svn.wordpress.org/getglueapi/trunk/lang/getglueapi.pot).

== Screenshots ==

1. Install process step 1
2. Install process step 2
3. Install process step 3
4. Install process step 4
5. Install process step 5
6. Interactions widget options
7. Interactions widget example

== Changelog ==

= 1.0.7 =
* Sorry, somewhere along the way I broke the stickers and this fixes them

= 1.0.6 =
* Added ability to showcase your site on NZGuru

= 1.0.5 =
* CSS fixes for more consistant display
* Excluded repetative duplicates

= 1.0.4 =
* Added handling of moviestar objects
* Split interactions to seperate checkins, likes, and stickers

= 1.0.3 =
* Added handling of game objects
* Added debug/cache information to the admin page

= 1.0.2 =
* Added handling of music objects

= 1.0.1 =
* SVN error

= 1.0.0 =
* Initial release

== Upgrade Notice ==

= 1.0.0 =
Initial release
