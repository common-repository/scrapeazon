<?php
/* This uninstall.php file is part of the ScrapeAZon plugin for WordPress
 * 
 * This file is distributed as part of the ScrapeAZon plugin for WordPress
 * and is not intended to be used apart from that package. You can download
 * the entire ScrapeAZon plugin from the WordPress plugin repository at
 * http://wordpress.org/plugins/scrapeazon/
 */

/* 
 * Copyright 2011-2014	James R. Hanback, Jr.  (email : james@jameshanback.com)
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

if(!defined('ABSPATH')&& !defined('WP_UNINSTALL_PLUGIN'))
{
    exit();
}

global $wpdb;

$szUser = wp_get_current_user();

// Remove settings stored in database
delete_option('scrape-aws-access-key-id');
delete_option('scrape-aws-secret-key');
delete_option('scrape-amz-assoc-id');
delete_option('scrape-getmethod');
delete_option('scrape-getcountry');
delete_option('scrape-responsive');
delete_option('scrape-disclaimer');

// Remove ScrapeAZon hidden notices
$szMeta_type  = 'user';
$szUser_id    = 0;
$szMeta_value = '';
$szDelete_all = true;

delete_metadata( $szMeta_type, $szUser_id, 'scrapeazon_ignore_FileGetEnabled', $szMeta_value, $szDelete_all );
delete_metadata( $szMeta_type, $szUser_id, 'scrapeazon_ignore_CurlEnabled', $szMeta_value, $szDelete_all );
delete_metadata( $szMeta_type, $szUser_id, 'scrapeazon_ignore_CurlDisabled', $szMeta_value, $szDelete_all );

// Remove ScrapeAZon transients
$dbquery = 'SELECT option_name FROM ' . $wpdb->options . ' WHERE option_name LIKE \'_transient_timeout_szT-%\';';
$cleandb = $wpdb->get_col($dbquery);
foreach ($cleandb as $transient) {
    $key = str_replace('_transient_timeout_','',$transient);
    delete_transient($key);
}
?>