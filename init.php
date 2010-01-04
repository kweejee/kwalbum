<?php defined('SYSPATH') or die('No direct script access.');
$reserved_words = '(?!(tags|people|page))';

Route::set('kwalbum_media', 'kwalbum/media/<file>', array(
		'file' => '.+',
	))
	->defaults(array(
		'controller' => 'kwalbum',
		'action'     => 'media',
	));

Route::set('kwalbum_item', 'kwalbum/~<id>(/<year>(/<month>(/<day>)))(/<location>)(/tags/<tags>)(/people/<people>)(/page/<page>)(/~<controller>(/<action>(<ext>)))', array(
		'id' => '[0-9]+',
		'controller'   => '[a-zA-Z]+?',
		'action'   => '[a-zA-Z0-9]+?',
		'ext' => '\..+',
		'year' => '[0-9]{4}',
		'month' => '[0-9]{1,2}',
		'day' => '[0-9]{1,2}',
		'location' => $reserved_words.'[^~]+?',
		'tags' => '.+?',
		'people' => '.+?',
		'page' => '[0-9]+'
	))
	->defaults(array(
		'controller' => 'item',
		'action'     => 'index',
	));

Route::set('kwalbum_browse', 'kwalbum(/<year>(/<month>(/<day>)))(/<location>)(/tags/<tags>)(/people/<people>)(/page/<page>)(/~<controller>(/<action>(<ext>)))', array(
		'controller'   => '[a-zA-Z]+?',
		'action'   => '[a-zA-Z0-9]+?',
		'ext' => '\..+',
		'year' => '[0-9]{4}',
		'month' => '[0-9]{1,2}',
		'day' => '[0-9]{1,2}',
		'location' => $reserved_words.'[^~]+?',
		'tags' => '.+?',
		'people' => '.+?',
		'page' => '[0-9]+'
	))
	->defaults(array(
		'controller' => 'browse',
		'action'     => 'index',
	));
