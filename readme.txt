=== ScrapeAZon ===

Contributors:      jhanbackjr,w33zy
Plugin Name:       ScrapeAZon
Plugin URI:        http://www.timetides.com/scrapeazon-plugin-wordpress
Tags:              amazon.com,amazon,customer,reviews,asin,isbn
Author URI:        http://www.timetides.com
Author:            James R. Hanback, Jr.
Requires at least: 3.6
Tested up to:      5.2
Stable tag:        2.3.0
License:           GPL3

Display Amazon.com customer reviews for products you specify in WordPress pages or posts, or as a widget.

== Description ==
!! IMPORTANT NOTICE !!

On March 9, 2020, Amazon will remove access to the Amazon Production Advertising API versions 4.0 and earlier. The new version of the API, 5.0, does NOT support the retrieval of customer reviews for products. Therefore, ScrapeAZon can no longer pull and display reviews from Amazon’s site. These changes make the ScrapeAZon plugin obsolete, since it will no longer display reviews. You should seek an alterate reviews solution.


The ScrapeAZon plugin displays Amazon.com customer reviews of specific products that you choose. You must be a participant in both the Amazon.com Affiliate Program and the Amazon.com Product Advertising API in order to use this plugin. You can join the Amazon.com Affiliate Program by following the instructions at the program page. You can join the Product Advertising API by following the instructions at the Product Advertising API page.

Features:

* Uses a shortcode to display customer reviews for a specific Amazon product.
* Reviews iframe can be styled manually from the shortcode, via custom CSS, or via a built-in responsive style sheet.
* Includes an "Amazon Reviews" widget that can be used in place of or in addition to the shortcode.
* Supports WordPress localization (i18n)
* Can pull reviews from Amazon International sites, not just Amazon.com (US). 
* Implements the latest version of the Amazon.com API.

== Installation ==

This section describes how to install the plugin and get it working.

1. Obtain an AWS Access Key ID, an AWS Secret Key, and an Amazon.com Affiliate ID, if you don't already have them
2. If you have a previous version of ScrapeAZon installed, deactivate and delete it from the '/wp-content/plugins/' directory
3. Upload the ScrapeAZon folder to the '/wp-content/plugins/' directory
4. Activate ScrapeAZon by using the 'Plugins' menu
5. Click ScrapeAZon from the Wordpress Settings menu and configure the appropriate settings
6. Add the <code>[scrapeazon asin="<amazon.com product number>"]</code> shortcode, where <amazon.com product number> is the ASIN or ISBN-10 of the product that contains the reviews you want to display, to the pages or posts you want. For example, if you wanted to display reviews for a product with the ASIN of 012345689, you would issue the shortcode <code>[scrapeazon asin="0123456789"]</code>

== Frequently Asked Questions ==

= Why doesn’t this plugin work anymore =

On March 9, 2020, Amazon migrated the Product Advertising API to a new version (5.0). This version of the API removed the ability to retrieve the Amazon customer reviews iframe for products on their site. This action rendered this plugin unusable and therefore obsolete.

= Why would I want to use this plugin? =

ScrapeAZon serves a very specific requirement. It was primarily developed to enable an Amazon vendor to display Amazon.com customer reviews on a product page that is styled independently of other item and product information that is available by using the Amazon.com API.

If you don't want to insert an entire Amazon.com product entry into your site, you can use this plugin to simply incorporate Amazon.com customer reviews onto your existing product page.

= What makes version 2.x different from the 1.x version of ScrapeAZon? =

ScrapeAZon has been rewritten from the ground up to more closely integrate with Wordpress. Additionally, this version of the plugin include several new features, such as:

* An option to style the plugin output for sites that use a responsive design.
* A widget that can be used in place of or in addition to the shortcode.
* An exponential backoff mechanism that attempts to mitigate throttling of high-traffic sites by the Amazon API. 

= What is an ASIN? =

An ASIN is an Amazon.com product identification number. ScrapeAZon uses this identifier to download the correct customer reviews that are associated with a product. An ASIN can be assigned by Amazon.com or, in case of a book, the 10-character version of the ISBN.

= Can I use an identifier other than an ASIN to retrieve reviews? =

Yes, as of ScrapeAZon 2.0.1, you can replace the asin parameter in a shortcode with any of the following parameters:

* <code>isbn</code>
* <code>upc</code>
* <code>ean</code>
* <code>sku</code>

The isbn parameter enables you to retrieve reviews for a book or an ebook by using an International Standard Book Number (ISBN). For example, you could use an ISBN retrieve reviews for Stephen King's 11/22/63 by using the following shortcode:

<code>[scrapeazon isbn="9781451627299"]</code>

The upc parameter enables you to retrieve reviews for a product based on that product's Universal Product Code (UPC).

The ean parameter enables you to retrieve reviews for a product based on that product's European Article Number (EAN).

The sku parameter enables you to retrieve reviews for a product based that product’s stock keeping unit (SKU).

= Why do I need to sign up for an Amazon Affiliate account? =

Amazon's API requires an affiliate account id in order to correctly process requests and download information about item lookups.

= Why do I need to sign up for Amazon Product Advertising API? =

Amazon's API requires an AWS Access Key ID and an AWS Secret Key in order to correctly process requests and download information about item lookups. You cannot obtain this information unless you sign up for an account.

= Can I use a user-specific (IAM) access key id and secret key with this plugin? =

No. As of this writing, Amazon's Product Advertising API does not support the use of IAM credentials. Therefore, you must use your root Access Key ID and Secret Key in order to successfully obtain product reviews. If you have already created root credentials and are no longer able to access your Secret Key, you might need to generate a new root key in the Amazon AWS Security Console.

= Why is there a 0 at the end of my reviews? =

For an unknown reason, the Amazon Product Advertising API returns a 0 at the end of review text. If you have configured review truncation, you won’t see the 0 value at the end of truncated reviews. However, any review that is displayed in its entirety currently also displays a 0 at the end. This is an issue with the API. Therefore, the plugin cannot fix this issue beyond allowing you to truncate reviews.

= How do I truncate reviews to a specific character length? =

If you want all ScrapeAZon-retrieved reviews throughout your site to be truncated at a specific character length, you can specify that length on the Settings > ScrapeAZon page. You can override this global setting at the shortcode level by specifying a positive integer value using the `truncate` parameter. A value of 0 either globally or on the shortcode level causes the API to return the full text of every review.

= Can I disable the reviews summary so that readers do not see how many ratings each level has? =

Yes. Although the reviews summary is on by default, you can disable it by specifying `summary="false"` in the shortcode.

= Can I retrieve reviews from sites other than Amazon by using this plugin? =

No. This plugin currently only accesses reviews for products that are available through the Amazon.com Product Advertising API.

= Can I retrieve reviews from Amazon's international sites? =

Yes. Configure the shortcode's <code>country</code> parameter with the appropriate two-character country code to change the Amazon site. For example, to retrieve reviews for ISBN 0123456789 from Amazon UK, you could issue the following shortcode:

<code>[scrapeazon asin="0123456789" country="UK"]</code>

The country codes are as follows:

* AT: Austria (uses the German site)
* AU: Australia
* CA: Canada
* CN: China
* DE: Germany
* ES: Spain
* FR: France
* IN: India
* IT: Italy
* JP: Japan
* UK: United Kingdom
* US: United States (default)

You can also globally configure a country code on the ScrapeAZon Settings page instead of specifying one for each shortcode used on your site. If you globally configure a country code and specify a country code in your shortcode, the country code in the shortcode will take precedence.

= How do I use the ScrapeAZon Widget? =

Similar to most WordPress widgets, first click Appearance > Widgets from the Admin menu. Next, drag the widget named "Amazon Reviews" to the location in which you want it to display. Once you have placed the widget, you must fill in the "ASIN" field with the ASIN of the product that contains the reviews you want to display. You can optionally fill in the Height, Width, and Border fields. You can also retitle the widget if you like. Note that whatever global settings you have configured on the ScrapeAZon Settings pages also apply to the widget. Therefore, if you have selected Responsive mode, the widget will attempt to use a responsive style.

= ScrapeAZon isn't displaying *anything* on my page. What's up with that? =

Some common reasons you might see an error or nothing at all are:

* Your AWS Access Key ID has not been set or is incorrect.
* Your AWS Secret Key has not been set or is incorrect.
* Your Amazon.com Associate ID has not been set or is incorrect.
* You have not allowed enough time for your keys or IDs to propagate at Amazon.com.
* Your AWS Access Key ID and Secret Key are associated with an incorrect Amazon.com Product Advertising API account.
* Your AWS Access Key ID and Secret Key are not root keys.
* Your site's HTTP retrieval client was not able to connect to the Amazon API.
* Your site has sent too many requests per second to the Amazon Product Advertising API and Amazon has throttled your access.
* Your site caches the pages that display reviews for an extended period of time (longer than 24 hours).
* Your site's server date, time, or time zone are not properly configured.

If you know that reviews exist for the product you specified, ensure that the ASIN/ISBN-10 you provided in the shortcode is correct. Also, ensure that you are not viewing a previously cached version of your page that does not contain the shortcode.

= How do I style the iframe? =

There are several ways that you can style the scrapeazon-reviews iframe: by editing your theme's stylesheet, by adding parameters to each shortcode, or by using the plugin's built-in responsive style sheet.

To style the iframe in your theme's stylesheet, add classes named scrapeazon-reviews and scrapeazon-api to your stylesheet, then add the width, height, border, and other parameters you want to style to those classes. For example, copy and paste the following into your stylesheet to make the iframe a 540x540 pixel square with no border:

<code>.scrapeazon-reviews {
   width: 540px;
   height: 540px;
   border: none;
}
.scrapeazon-api {
   width: 540px;
}</code>

To style the iframe by using the shortcode, add width, height, and border as parameters to the shortcode. For example, to accomplish the same formatting as above in shortcode format, use the following shortcode:

<code>[scrapeazon asin="<your asin>" width="540" height="540" border="false"]</code>

Append a percent (%) symbol to the width and height values if you are specifying your those values in percentages rather than pixels. You can optionally append ‘px’ instead of the percent symbol to use pixels. If you specify digits only, ScrapeAZon will default to pixels.

To style the iframe by using the built-in responsive style sheet (if your site has a responsive design/theme), select the "Use Responsive Style" checkbox on the ScrapeAZon Settings page.

= I'm using the responsive stylesheet. Why does the content in the iframe scroll horizontally on very small screens? =

Because the iframe content comes from a different source than the iframe itself, the content does not always scale in a responsive way. On very small screens (such as a vertically held iPhone), the iframe itself will scale to the width of the screen in responsive mode. However, the content inside the iframe might need to be scrolled horizontally as well as vertically in order to read it.

= Can I get rid of that annoying disclaimer at the bottom of the iframe? =

If you know how to edit your theme's CSS, you probably can. However, doing so is not recommended unless you already manually display the disclaimer on your site. As of this writing, Amazon Services requires the disclaimer as part of its Product Advertising API terms.

As of ScrapeAZon 2.2.4, the Settings page now allows you to globally modify the text in the disclaimer.

= Can I at least style the disclaimer differently? =

If you want to use a different font, font size, or otherwise style the disclaimer, add a class named scrape-api to your theme's CSS file and make the changes within that class. For example, if you'd like the disclaimer to be in 9-point Helvetica and 540 pixels wide, you could add the following class to your CSS:

<code>.scrapeazon-api {
   width: 540px;
   font-family: Helvetica;
   font-size: 9pt;
}</code>

= What if I want to use iframe element attributes that are not supported by the shortcode? =

If you want more advanced control over the iframe, you can opt to issue the shortcode with the <code>url="true"</code> parameter. When set to "true," the <code>url</code> parameter prevents the plugin from displaying the iframe and instead simply returns the Amazon URL that should be included in the iframe's SRC attribute.

If you choose to issue the shortcode this way, you should do so between an iframe SRC attributes quotation marks in your page or post, as shown in the following example:

<code><iframe src="[scrapeazon isbn="9781451627299"  url="true"]"></iframe></code>

= Can I prevent the display of the iframe if there are no reviews yet for my products? =

Yes. By default, if the Amazon API returns no reviews for your product, ScrapeAZon will display the iframe that contains Amazon's "Be the first to review this item" page for the product you specified. If you want to prevent the display of that page, issue the shortcode with the <code>noblanks</code> parameter set to <code>true</code>, as shown in the following example:

<code>[scrapeazon isbn="9781451627299" noblanks="true"]</code> 

== Upgrade Notice ==

= 2.3.0 =
Adds sunset notice and end-of-life code for plugin

= 2.2.9 =
Hides values in AWS key fields

= 2.2.8 =
Allows multiple iframes when defer to footer option is selected

= 2.2.7 =
Fixes test page so that it works with countries other than the US

= 2.2.6 =
Adds WooCommerce plugin page type detection

= 2.2.5 =
Enhances transient caching for sites that do not use object caching plugins.

= 2.2.4 =
Adds support for width and height percentages and hardens URL sanitization.

= 2.2.2 =
Adds a shortcode button to the text editor, the ability to retrieve reviews by SKU, the ability to disable the reviews summary, and the ability to truncate reviews to a custom length.

= 2.2.1 =
Fixes a WP_DEBUG notice that could be displayed on WordPress content types that are not pages or posts.

= 2.2.0 =
Replaces use of cURL and file_get_contents retrieval methods with wp_remote_get (the WordPress way).

= 2.1.8 =
Improves performance by only loading responsive style sheet on pages that use the short code or widget.

= 2.1.6 =
Fixes an undefined function in the widget.

= 2.1.5 =
Adds an option to the Performance tab to defer iframe loads until the page footer.

= 2.1.2 =
Fixes a default global country settings bug.

= 2.1.1 =
Fixes a stylesheet loading issue.

= 2.1.0 =
Adds a caching mechanism for enhanced performance along with other new features and fixes. Optimizes shortcode defaults and fixes some variable and array initialization issues.

= 2.0.6 =
Optimizes instantiation/destruction of the data retrieval function.

= 2.0.5 =
Fixes input sanitization issue that prevents some UPC reviews from being displayed.

= 2.0.4 =
Updated Advertising API version to 2013-08-01.

= 2.0.3 =
Upgrade to 2.0.3 to enable ScrapeAZon shortcodes and the ScrapeAZon widget to retrieve reviews by using ISBN, UPC, EAN, or ASIN parameters.

= 2.0.0 =
Upgrade to 2.0.0 to enable better Wordpress Settings API integration, better API throttling protection, the possibility of using responsive styles, and the ability to use ScrapeAZon as a widget.

= 1.1.0 =
Upgrade to 1.1.0 to enable automatic detection of HTTPS.

= 1.0.9 =
Upgrade to 1.0.9 to enable support for the Amazon API in China and India.

= 1.0.8 =
Upgrade to 1.0.8 to enable basic ScrapeAZon troubleshooting features, such as a sample iframe and PHP environment detection.

= 1.0.7 =
Upgrade to 1.0.7 to enable Amazon international compatibility and to display the Amazon "Be the first to review" page when no reviews are available for a specific product.

= 1.0.6 =
Upgrade to 1.0.6 to enable ScrapeAZon to automatically display the disclaimer required in the Amazon Product Services API Terms.

= 1.0.5 =
Upgrade to 1.0.5 to fix options page so that it does not conflict with other plugins.

= 1.0.4 =
Upgrade to 1.0.4 to be able to use ScrapeAZon in non-standard Wordpress environments.

= 1.0.3 =
Upgrade to version 1.0.3 to get more granular control over the appearance of the reviews iframe on your pages or posts. Additionally, ScrapeAZon is now compatible only with PHP installations that support cURL.

= 1.0.2 =
Upgrade to version 1.0.2 if you use Amazon API keys that contain special characters, such as the "/" character. (Thanks, Bryan.)

= 1.0.1 =
You should upgrade to version 1.0.1 to reduce calls to the Product Advertising API and increase performance.

= 1.0 =
This is the first version of the plugin.

== Screenshots ==

1. The Settings page
2. The plugin in action
3. The shortcode in a post

== Changelog ==

= 2.3.0 =
* Updated code and admin notices to sunset and end-of-life the ScrapeAZon plugin. On March 9, 2020, the Amazon Product Advertising API will stop serving reviews, rendering the plugin obsolete.

= 2.2.9 =
* Modified AWS fields so values are hidden

= 2.2.8 =
* Fixed footer defer option to enable multiple shortcodes
* Tested with WordPress 4.8

= 2.2.7 =
* Fixed test page so that it works with countries other than the US.
* Tested with WordPress 4.7.

= 2.2.6 =
* Added check for WooCommerce to fix an issue in which the responsive styles were not loading properly.

= 2.2.5 =
* Enhanced transient caching routine for sites that do not use object caching plugins.

= 2.2.4 =
* Added support for using width and height percentages in place of pixels.
* Added backward compatibility for versions of WordPress earlier than 3.3 (plugin still requires at least WordPress 3.6 if responsive mode is used).
* Added better error trapping for Amazon connectivity issues.
* Added ability to modify disclaimer text.
* Hardened security by improving URL sanitization.
* Updated readme.
* Updated POT file.

= 2.2.2 =
* Added the SAz shortcode button to the default WordPress text editor
* Added ability to retrieve reviews by SKU numbers
* Added ability to truncate reviews to a given number of characters globally or via shortcode
* Added ability to disable the display of the reviews summary that by default appear at the top of the reviews
* Updated readme
* Updated POT file

= 2.2.1 =
* Fixed a WP_DEBUG notice that could be displayed on WordPress content types that are not pages or posts
* Added a link to a configuration tutorial on the Settings > ScrapeAZon > Usage tab
* Updated POT file

= 2.2.0 =
* Replaced cURL and file_get_contents calls with wp_remote_get
* Removed settings related to cURL and file_get_contents
* Enhanced transient function to account for variation between HTTP and HTTPS
* Modified documentation/FAQ
* Updated POT file

= 2.1.8 =
* Modified the unique identifiers generated by the caching mechanism
* Modified responsive style sheet enqueues so that styles only load on pages/posts that require them
* Modified widget loading to enable widgets and shortcodes to exist simultaneously
* Added unique identifiers to div containers to enable multiple shortcodes/widgets per page/post
* Updated minimum WordPress version to 3.6
* Updated POT file

= 2.1.6 =
* Fixed an undefined function error in the widget.

= 2.1.5 =
* Added the ability to defer iframe loads until the footer to enhance site performance.
* Tested in WordPress 4.0

= 2.1.2 =
* Bug fix for default global country setting issue.

= 2.1.1 =
* Bug fix for a stylesheet loading issue.

= 2.1.0 =
* Added a caching mechanism and related settings for faster performance.
* Optimized shortcode defaults.
* Fixed some variable and array initialization issues.
* Added a new shortcode parameter (url) that disables display of the iframe and returns only the iframe source URL.
* Added a new shortcode parameter (noblanks) to disable the iframe when no reviews exist.
* Updated context-sensitive help.
* Updated POT file.
* Updated readme and FAQ.

= 2.0.6 =
* Updated data retrieval function to optimize instantiation/destruction.
* Fixed a bug in the HTTP retries code that might hinder performance on some sites.

= 2.0.5 =
* Updated input sanitization to fix an issue that was preventing some UPC code reviews from being displayed.

= 2.0.4 =
* Updated query string to use Product Advertising API version 2013-08-01.

= 2.0.3 =
* Added support for retrieving reviews by ISBN to shortcode and widget.
* Added support for retrieving reviews by UPC to shortcode and widget.
* Added support for retrieving reviews by EAN to shortcode and widget.
* Modified Settings field labels.
* Updated FAQ.
* Updated help.
* Updated POT file.

= 2.0.0 =
* Plugin has been completely rewritten to better integrate with the Wordpress Settings API.
* Added support for styling the iframe in a more responsive way.
* Added support for WordPress localization (i18n).
* Added support for HTTP retries and an exponential backoff method of dealing with throttling problems.
* Added support for context-sensitive help on the Settings page.
* Added support for an uninstall process that removes all settings and plugin files.

= 1.1.0 =
* Added support for the automatic detection of HTTPS sites.

= 1.0.9 =
* Added support for Amazon China and India API calls.

= 1.0.8 =
* Added Settings page cues to better inform new users about their system environments as well as ScrapeAZon's system requirements.
* Added a test iframe on the Settings page so that users can see immediately whether their configurations are working.
* Added a Settings link to ScrapeAZon's settings from the Plugins page.

= 1.0.7 =
* Can now display results from Amazon international sites. Sites are configurable on a per-shortcode basis. Default is the US site.
* Now displays the Amazon "Be the first to review this" page instead of an HTML comment when no reviews exist.

= 1.0.6 =
* Added an Amazon Product Advertising API ToS compliance function that automatically displays Amazon Services LLC's required disclaimer below content retrieved via the API.

= 1.0.5 =
* Modified options page so that it uses a unique name that will not conflict with other plugins.

= 1.0.4 =
* Modified include path so that it works with uniquely named content directories

= 1.0.3 =
* Added shortcode parameters to style the iframe
* Added information to the FAQ about styling the iframe
* Added option that enables you to retrieve reviews via either cURL or file_get_contents
* Strengthened input validation

= 1.0.2 =
* Fixed secret key character validation that prevented some API keys from working

= 1.0.1 =
* Reduced calls to the AWS Product Advertising API to increase performance
* Fixed typo in the FAQ
* Fixed typo in the tags

= 1.0 =
* Initial release

