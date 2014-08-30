<?php defined('SYSPATH') or die('No direct script access.');
$reserved_words = '(?!(tags|people|page|created))';

Route::set('kwalbum_media', 'kwalbum/media/<file>', array(
		'file' => '.+',
	))
	->defaults(array(
		'controller' => 'kwalbum',
		'action'     => 'media',
	));

Route::set('Kwalbum_Item', 'kwalbum/~<id>(/<year>(/<month>(/<day>))(/to/<year2>(/<month2>(/<day2>))))(/<location>)(/tags/<tags>)(/people/<people>)(/created/<created_date>(/<created_time>))(/~item(/<action>(<ext>)))(/page/<page>)', array(
		'id' => '[0-9]+',
		'action' => '[a-zA-Z0-9]+?',
		'created_date' => '[0-9-]{10}',
		'created_time' => '[0-9:]{8}',
		'ext' => '\..+',
		'year' => '[0-9]{4}',
		'month' => '[0-9]{1,2}',
		'day' => '[0-9]{1,2}',
		'year2' => '[0-9]{4}',
		'month2' => '[0-9]{1,2}',
		'day2' => '[0-9]{1,2}',
		'location' => $reserved_words.'[^~]+?',
		'tags' => '.+?',
		'people' => '.+?',
		'page' => '[0-9]+'
	))
	->defaults(array(
		'controller' => 'item',
		'action'     => 'index',
	));

Route::set('kwalbum_browse', 'kwalbum(/<year>(/<month>(/<day>))(/to/<year2>(/<month2>(/<day2>))))(/<location>)(/tags/<tags>)(/people/<people>)(/created/<created_date>(/<created_time>))(/~<controller>(/<action>(<ext>)))(/page/<page>)', array(
		'controller'   => '[a-zA-Z]+?',
		'action'   => '[a-zA-Z0-9]+?',
		'created_date' => '[0-9-]{10}',
		'created_time' => '[0-9:]{8}',
		'ext' => '\..+',
		'year' => '[0-9]{4}',
		'month' => '[0-9]{1,2}',
		'day' => '[0-9]{1,2}',
		'year2' => '[0-9]{4}',
		'month2' => '[0-9]{1,2}',
		'day2' => '[0-9]{1,2}',
		'location' => $reserved_words.'[^~]+?',
		'tags' => '.+?',
		'people' => '.+?',
		'page' => '[0-9]+'
	))
	->defaults(array(
		'controller' => 'browse',
		'action'     => 'index',
	));
