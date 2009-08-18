<?php defined('SYSPATH') OR die('No direct access allowed.');
return array
(
	/**
	 * Title for the Kwalbum section of your website
	 */
	'title' => 'Kwalbum',
	/**
	 * String to put between subsections in the title
	 */
	'title_separator' => ' - ',
	/**
	 * Item upload directory.
	 */
	'item_path' => APPPATH.'items',

	/**
	 * Root path in the URL to get to Kwalbum
	 */
	'url' => '/kwalbum/',

	/**
	 * Secret key for remote sites to get data about your items
	 * Random characters are best
	 * No more than 45 characters
	 */
	'key' => 'change me',
);
