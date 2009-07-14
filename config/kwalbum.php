<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Title for the Kwalbum section of your website
 */
$config['title'] = 'Kwalbum';
/**
 * String to put between subsections in the title
 */
$config['title_separator'] = ' - ';
/**
 * Item upload directory.
 */
$config['item_path'] = APPPATH.'items';
/**
 * Root path in the URL to get to Kwalbum
 */
$config['url'] = '/index.php/kwalbum';
/**
 * Paths in the URL to css and javascript.
 * Start and end slashes are not required.
 */
$config['css_url'] = 'css';
$config['js_url'] = 'js';
/**
 * Secret key for remote sites to get data about your items
 * Random characters are best
 * No more than 45 characters
 */
$config['key'] = 'change me';
