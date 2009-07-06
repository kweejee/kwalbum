<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Routes only for Kwalbum
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @version 3.0 Jul 6, 2009
 * @package kwalbum
 * @since 3.0 Jul 6, 2009
 */

$config['kwalbum/admin'] = 'admin';
//$config['ajax'] = 'ajax';
$config['kwalbum/item/([0-9]+)(/.*)?'] = 'item/single/$1$2';
$config['kwalbum/tag/([a-zA-Z0-9]+)'] = 'browse/tag/$1';
$config['kwalbum/([0-9]{4})(/[0-9]{1,2})?(/[0-9]{1,2})?'] = 'browse/date/$1$2$3';
$config['kwalbum/([a-zA-Z0-9]+)'] = 'browse/location/$1';
