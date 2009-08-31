<?php defined('SYSPATH') or die('No direct script access.');

Route::set('kwalbum_media', 'kwalbum/media/<file>', array(
		'file' => '.+',
	))
	->defaults(array(
		'controller' => 'kwalbum',
		'action'     => 'media',
	));

Route::set('kwalbum_date', 'kwalbum(/~<id>)(/~<controller>(/<action>(<ext>)))(/<year>(/<month>(/<day>)))(/<location>)(/tag/<tag>)', array(
		'id' => '[0-9]+',
		'controller'   => '[a-zA-Z]+?',
		'action'   => '[a-zA-Z0-9]+?',
		'ext' => '\.\w+',
		'year' => '[0-9]{4}',
		'month' => '[0-9]{1,2}',
		'day' => '[0-9]{1,2}',
		'location' => '.+?',
		'tag' => '.+?',
	))
	->defaults(array(
		'controller' => 'kwalbum',
		'action'     => 'index',
	));
