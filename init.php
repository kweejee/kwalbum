<?php defined('SYSPATH') or die('No direct script access.');

Route::set('kwalbum_install', 'kwalbum/install')
	->defaults(array(
		'controller' => 'install',
		'action'     => 'index',
	));

Route::set('kwalbum_item', 'kwalbum/(~<id>)(/<action>)', array(
		'id' => '[0-9]+',
		'action' => '[a-zA-Z0-9]+',
	))
	->defaults(array(
		'controller' => 'item',
		'action'     => 'single',
	));

Route::set('kwalbum_controller', 'kwalbum/~(<controller>)(/<action>)', array(
		'controller'   => '[a-zA-Z0-9]+',
		'action'   => '[a-zA-Z0-9]+',
	))
	->defaults(array(
		'controller' => 'kwalbum',
		'action'     => 'index',
	));

Route::set('kwalbum_media', 'kwalbum/media/<file>', array(
		'file' => '.+',
	))
	->defaults(array(
		'controller' => 'kwalbum',
		'action'     => 'media',
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
		'location' => '.{1,}',
	))
	->defaults(array(
		'controller' => 'browse',
		'action'     => 'location',
	));