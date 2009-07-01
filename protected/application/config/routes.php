<?php defined('SYSPATH') OR die('No direct access allowed.');

$config['_default'] = 'index';
$config['admin'] = 'admin';
//$config['ajax'] = 'ajax';
$config['item/([0-9]+)(/.*)?'] = 'item/single/$1$2';
$config['tag/([a-zA-Z0-9]+)'] = 'browse/tag/$1';
$config['([0-9]{4})(/[0-9]{1,2})?(/[0-9]{1,2})?'] = 'browse/date/$1$2$3';
$config['([a-zA-Z0-9]+)'] = 'browse/location/$1';
