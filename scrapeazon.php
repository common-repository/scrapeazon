<?php
/*
 * Plugin Name: ScrapeAZon
 * Plugin URI: http://www.timetides.com/scrapeazon-plugin-wordpress
 * Description: Retrieves Amazon.com reviews for products you choose from the Amazon Product Advertising API and displays those reviews in pages, posts, or as a widget on your WordPress blog.
 * Version: 2.3.0
 * Author: James R. Hanback, Jr.
 * Author URI: http://www.timetides.com
 * License: GPL3
 * Text Domain: scrapeazon
 * Domain Path: /lang/
 */

/*
 * Copyright 2011-2020	James R. Hanback, Jr.  (email : james@jameshanback.com)
 * 
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// error_reporting(E_ALL);
$szToday  = date("Y-m-d H:i:s");
$szSunset = "2020-03-09 00:00:00";

// Load plugin files and configuration
$szPlugin = plugin_basename(__FILE__); 
$szPPath = plugin_dir_path(__FILE__);
$szPPath .= '/szclasses.php';
include_once($szPPath);

$szOpts = new szWPOptions;
$szShcd = new szShortcode;

register_activation_hook($szPPath,array(&$szOpts,'szActivate'));
register_activation_hook(  plugin_dir_path( __FILE__ ) . 'scrapeazon.php', array( $szShcd, 'sz_create_custom_option' ) );


// Add widget
add_action('widgets_init',create_function('', 'return register_widget("szWidget");'));

// Register scripts and styles
add_action('wp_enqueue_scripts',array(&$szOpts,'szRequireStyles'));

// Localization support
add_action('plugins_loaded', array(&$szOpts, 'szLoadLocal'));

if (is_admin()) {
    add_action('admin_init', array(&$szOpts, 'szRegisterSettings'));
    add_action('admin_menu', array(&$szOpts, 'szAddAdminPage'));
    add_action('admin_notices', array(&$szOpts, 'szShowNPNotices'));
    add_filter('plugin_row_meta', array(&$szOpts,'szDonateLink'), 10, 2);
}

add_filter("plugin_action_links_$szPlugin", array(&$szOpts, 'szOptionsLink'));
add_action('admin_print_footer_scripts',array(&$szShcd, 'szQuicktag'));
add_action( 'wp_ajax_sz_display_dismissible_admin_notice', array( &$szShcd, 'sz_display_dismissible_admin_notice' ) );

if ($szSunset > $szToday) {
    // Add shortcode functionality
    add_shortcode( 'scrapeazon', array(&$szShcd, 'szParseShortcode') );
    add_action( 'admin_notices', array(&$szShcd,'sz_sunset_notice' ));
    add_action( 'admin_enqueue_scripts', array( &$szShcd, 'sz_enqueue_scripts' ) );
} 
else 
{
    add_shortcode( 'scrapeazon', array(&$szShcd, 'szNoShortcode') );
    add_action( 'admin_notices', array(&$szShcd,'sz_eol_notice' ));
    add_action( 'admin_enqueue_scripts', array( &$szShcd, 'sz_enqueue_scripts' ) );
}

?>