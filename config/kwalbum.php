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
 * Database table names
 */
$config['dbtables'] = array
(
	'items' => 'kwalbum_items',
	'users' => 'kwalbum_users',
	'locations' => 'kwalbum_locations',
	'tags' => 'kwalbum_tags',
	'items_tags' => 'kwalbum_items_tags',
	'persons' => 'kwalbum_persons',
	'items_persons' => 'kwalbum_items_persons',
	'comments' => 'kwalbum_comments',
	'favorites' => 'kwalbum_favorites',
	'externals' => 'kwalbum_externals',
	'externals_items' => 'kwalbum_external_items',
);
/**
 * Secret key for remote sites to get data about your items
 * Random characters are best
 * No more than 45 characters
 */
$config['key'] = 'change me';
