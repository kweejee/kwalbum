<?php defined('SYSPATH') or die('No direct script access.');

Route::set('kwalbum_media', 'kwalbum/media/<file>', array(
		'file' => '.+',
	))
	->defaults(array(
		'controller' => 'kwalbum',
		'action'     => 'media',
	));

Route::set('kwalbum_item', 'kwalbum/~<id>(/<year>(/<month>(/<day>)))(/<location>)(/tags/<tags>)(/people/<people>)(/~<controller>(/<action>(<ext>)))(/page/<page>)', array(
		'id' => '[0-9]+',
		'controller'   => '[a-zA-Z]+?',
		'action'   => '[a-zA-Z0-9]+?',
		'ext' => '\.\w+',
		'year' => '[0-9]{4}',
		'month' => '[0-9]{1,2}',
		'day' => '[0-9]{1,2}',
		'location' => '[^~]+?',
		'tags' => '.+?',
		'people' => '[^~]+?',
		'page' => '[0-9]+'
	))
	->defaults(array(
		'controller' => 'item',
		'action'     => 'index',
	));

Route::set('kwalbum_browse', 'kwalbum(/<year>(/<month>(/<day>)))(/<location>)(/tags/<tags>)(/people/<people>)(/~<controller>(/<action>(<ext>)))(/page/<page>)', array(
		'controller'   => '[a-zA-Z]+?',
		'action'   => '[a-zA-Z0-9]+?',
		'ext' => '\.\w+',
		'year' => '[0-9]{4}',
		'month' => '[0-9]{1,2}',
		'day' => '[0-9]{1,2}',
		'location' => '[^~]+?',
		'tags' => '.+?',
		'people' => '[^~]+?',
		'page' => '[0-9]+'
	))
	->defaults(array(
		'controller' => 'browse',
		'action'     => 'index',
	));
