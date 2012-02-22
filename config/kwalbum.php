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
	'title_separator' => ' : ',

	/**
	 * Item upload directory.
	 */
	'item_path' => APPPATH.'items/',

	/**
	 * Thumbnails per page when browsing.
	 */
	'items_per_page' => 20,

	/**
	 * Secret key for remote sites to get data about your items
	 * Random characters are best
	 * No more than 45 characters
	 */
	'key' => 'change me',

	/**
	 * Png image in the item directory to use as a watermark if you want user
	 * levels other than contributer to see one.
	 */
	'watermark_filename' => '',

	/**
	 * Maximum percent of the width of the image the watermark should cover.
	 */
	'watermark_width_percent' => .4,

	/**
	 * Maximum percent of the height of the image the watermark should cover.
	 */
	'watermark_height_percent' => .3,

	/**
	 * Because of memory limits on some servers, files larger than this will
	 * not have a watermark added.
	 */
	'watermark_filesize_limit' => 3000000

	/**
	 * Separate a main location from a sublocation with this.  Spaces are ignored
	 * when saving, but used when displaying.
	 */
	,'location_separator_1' => ': '
	/**
	 * If separator 1 is found more than once, all except the first will be
	 * replaced with this to avoid confusing the system.
	 */
	,'location_separator_2' => ' - '
);
