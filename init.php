<?php defined('SYSPATH') or die('No direct script access.');

Route::set('kwalbum_install', 'kwalbum/install')
	->defaults(array(
		'controller' => 'install',
		'action'     => 'index',
	));

Route::set('kwalbum_admin', 'kwalbum/~(<controller>)(/<action>)', array(
		'controller'   => '.+',
		'action'   => '.+',
	))
	->defaults(array(
		'controller' => 'browse',
		'action'     => 'index',
	));

Route::set('kwalbum_item', 'kwalbum/item(/<id>)(/<action>)', array(
		'action' => '[a-zA-Z0-9]+',
		'id' => '[0-9]+',
	))
	->defaults(array(
		'controller' => 'item',
		'action'     => 'single',
	));

Route::set('kwalbum_tag', 'kwalbum/tag/<tag>', array(
		'tag' => '[a-zA-Z0-9]+',
	))
	->defaults(array(
		'controller' => 'browse',
		'action'     => 'tag',
	));

Route::set('kwalbum_date', 'kwalbum/(<year>)(/<month>)(/<day>)', array(
		'year' => '[0-9]{4}',
		'month' => '[0-9]{1,2}',
		'day' => '[0-9]{1,2}',
	))
	->defaults(array(
		'controller' => 'browse',
		'action'     => 'date',
	));

Route::set('kwalbum_location', 'kwalbum/<location>', array(
		'location' => '[a-zA-Z0-9]+',
	))
	->defaults(array(
		'controller' => 'browse',
		'action'     => 'location',
	));

Route::set('kwalbum_media', 'kwalbum/media/<file>', array(
		'file' => '.+',
	))
	->defaults(array(
		'controller' => 'kwalbum',
		'action'     => 'media',
	));