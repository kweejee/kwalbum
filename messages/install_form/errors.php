<?php defined('SYSPATH') or die('No direct access allowed.');
return array
(
	'login_name' => array
	(
		'not_empty' => 'required',
		'default' => 'Invalid Input.',
	),
	'email' => array
	(
		'not_empty' => 'required',
		'email' => 'Invalid email format.',
		'default' => 'Invalid Input.',
	),
	'password' => array
	(
		'not_empty' => 'required',
		'default' => 'Invalid Input.',
	),
	'name' => array
	(
		'not_empty' => 'required',
		'min_length' => 'Your name has to be longer.',
		'max_length' => 'Your name is too long.',
		'default' => 'Invalid Input.',
	),
	'db' => 'There was an error creating the database tables.'
);