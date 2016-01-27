=== Plugin Name ===

Contributors: shawneggplantstudiosca

Donate link: none

Tags: 301 redirects, redirects

Requires at least: 3.0.1

Tested up to: 4.2.1

Stable tag: 2.3.0

License: GPLv2 or later

License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily manage and create 301 redirects for your Wordpress website. A robust interface allows you create and validate redirects.

== Description ==

**EPS 301 Redirects** helps you manage and create 301 redirects for your Wordpress website to improve your SEO and guest experience. With a user-friendly interface, EPS 301 Redirects is easy to install and configure. This plugin is installed on over 20,000 Wordpress Websites, and the number grows higher every day. This plugin is perfect for new site designs, repairing links after re-organizing your existing Wordpress content, or when your site has content that expires and you wish to avoid sending visitors to 404 pages.


**Features**

1. Choose from Pages, Posts, Custom Post types, Archives, and Term Archives from dropdown menus.
1. Or, set a custom destination URL!
1. Retain query strings across redirects
1. Super-fast redirection
Improved Import/Export feature for bulk additions.
1. A 404 logger (pro version), that will easily convert into new redirects.

**The Pro version of EPS 301 Redirects includes a new 404 logging feature:**

The Pro Version of EPS 301 Redirects will add a new 404 tracking feature. Every 404 error will be logged, and you will have the power and flexibility to redirect them wherever you want them to go.

1. See which Request URLs are causing 404 errors on your site.
1. Discover which 404 errors are receiving the most traffic.
1. Improve SEO by lowering your total number of 404 errors.
1. Easily fix the 404 errors by turning them into redirects.



**What is a 301 Redirect?** 
A redirect is a simple way to re-route traffic coming to a *Requested URL* to different *Destination URL*. 

A 301 redirect indicates that the page requested has been permanently moved to the *Destination URL*, and helps pass on the *Requested URLs* traffic in a search engine friendly manner. Creating a 301 redirect tells search engines that the *Requested URL*  has moved permanently, and that the content can now be found on the *Destination URL*. An important feature is that search engines will pass along any clout the *Requested URL* used to have to the *Destination URL*.


**When Should I use EPS 301 Redirects?** 
1. Replacing an old site design with a new site design
1. Overhauling or re-organizing your existing Wordpress content
1. You have content that expires (or is otherwise no longer available) and you wish to redirect users elsewhere.



Created by Shawn Wernig [Eggplant Studios](http://www.eggplantstudios.ca/ "Eggplant Studios")

*This release is compatible with all Wordpress versions since 3.0.1 (Tested. Plugin may not work as intended for older versions of Wordpress).*

*This plugin is English only; though it is somewhat language independent.*

== Installation ==



1. Upload the `eps-301-redirects` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Begin adding redirects in the Settings -> EPS 301 Redirects menu item.



== Frequently Asked Questions ==

=What is a 301 Redirect?=
A redirect is a simple way to re-route traffic coming to a Requested URL to different Destination URL.

A 301 redirect indicates that the page requested has been permanently moved to the Destination URL, and helps pass on the Requested URLs traffic in a search engine friendly manner. Creating a 301 redirect tells search engines that the Requested URL has moved permanently, and that the content can now be found on the Destination URL. An important feature is that search engines will pass along any clout the Requested URL used to have to the Destination URL.

=I'm getting an error about the default permalink structure?=

EPS 301 Redirects requires that you use anything but the default permalink structure.

=My redirects aren't working=

This could be caused by many things, but please ensure that you are supplying valid URLs. Most common are extra spaces, extra slashes, spelling mistakes and invalid characters. If you're sure they're right, chances are your browser has cached the 301 redirect (in an attempt to make the redirection faster for you), but sometimes it doesn't refresh as fast as we would like. Clear your browser cache, or wait a few minutes to fix this problem.
My redirects aren't working - the old .html page still shows
For this plugin to work, the page must be within the Wordpress environment. If you are redirecting older .html or .php files, you must first delete them. The plugin can’t redirect if the file still exists, sorry! You should look into .htaccess redirects if you want to keep these files on your server.

=My redirects aren't getting the 301 status code=

Your Request or Redirect URLS may be incorrect; please ensure that you are supplying valid URLs. Check slashes. Try Viewing the page by clicking the Request URL - does it load correctly?

=How do I delete a redirect?=

Click the small X beside the redirect you wish to remove.

=How do I add wildcards. or folder redirects?=

Unfortunately this is not supported. You should look into .htaccess redirects for these advanced features.


=What about query strings?=

By default, any URL with a query string is considered unique, and will redirect to a unique page (if you so wish). The query string will be added to the Destination URL, which allows you to keep your tracking codes, affiliate codes, and other important data!



== Screenshots ==

1. The administrative area, Wordpress 3.5.1
2. Examples of some of the dropdown options. 
3. The redirect testing feature. 

== Changelog ==

= 2.3.0 =
Added sorting. Fixed a bug when upgrading from V1 to V2, and the infamous "Invalid Destination" url.

= 2.2.7 =
That silly bug with the database tables not being created has been squashed. Improved query performance.

= 2.2.6 =
Added support for custom plugin directories.

= 2.2.4 =
Support for older versions of PHP.

= 2.2.3 =
Fixed an issue where the redirect database tables were not being created, causing 'Invalid Destination URL' errors.

= 2.2.0 =
Minor bug fixes. Greatly improved import feature. Redirects include query strings. Export feature added. Http/Https agnostic. Pro version added with 404 management.

= 2.1.2 =
Minor bug fixes.

= 2.1.1 =
Fixed an issue where users with a lot of redirects were being limited, this fix also changed up the admin area. Redirects are now editable via AJAX, and the ‘add new’ form was moved to the top.


= 2.0.1 =
Fixed an issue where the Automatic Update would not call the import process for pre 2.0 versions.

= 2.0.0 =
Overhauled the entire plugin. Redirects are stored in their own table. Gracefully migrates older versions.

= 1.4.0 =
* Performance updates, added a new 'Settings' page.

= 1.3.5 =
* Fixed a bug with spaces in the url. Added ease of use visual aids.


= 1.3.4 =
* Fixed nonce validation problem which would prevent saving of new redirects. Special Thanks to Bruce Zlotowitz for all his testing!

= 1.3.3 =
* Fixed major problem when switching from 1.2 to 1.3+


= 1.3.1 =
* Added hierarchy to heirarchical post type selects.


= 1.3 =
* Fixed a bug where duplicate urls were being overwritten, fixed a bug where you could not completely remove all redirects.

= 1.2 =
* Fixed some little bugs.

= 1.1 =
* Minor CSS and usability fixes. Also checking out the SVN!

= 1.0 =
* Release.

== Upgrade Notice ==

= 2.3.0 =
Added sorting. Fixed a bug when upgrading from V1 to V2, and the infamous "Invalid Destination" url.


= 2.2.7 =
That silly bug with the database tables not being created has been squashed. Improved query performance.

= 2.2.6 =
Added support for custom plugin directories.

= 2.2.4 =
Support for older versions of PHP.

= 2.2.0 =
Minor bug fixes. Greatly improved import feature, added Export feature plus many other updates. A Pro Version is now available with 404 Management!

= 2.1.2 =
Minor bug fixes.

= 2.1.1 =
Fixed an issue where users with a lot of redirects were being limited, this fix also changed up the admin area. Redirects are now editable via AJAX, and the ‘add new’ form was moved to the top.


= 2.0.1 =
Fixed an issue where the Automatic Update would not call the import process for pre 2.0 versions.

= 2.0.0 =
Overhauled the entire plugin. Redirects are stored in their own table. Gracefully migrates older versions.

= 1.4.0 =
* Performance updates, added a new 'Settings' page.

= 1.3.5 =
Fixed a bug with spaces in urls. Also added a test for both the request and destination urls to verify that they're working as intended.

= 1.3.4 =
Fixed a bug when saving new redirects.

= 1.3.3 =
Compatibility update for users upgrading from 1.2 to 1.3+ - Update ASAP.

= 1.3.1 =
Functionality update, Cosmetic.

= 1.3 =
Bug fixes; Update ASAP.

= 1.2 =
Cosmetic updates.

= 1.1 =
Cosmetic updates.

= 1.0 =
Released May, 2013.