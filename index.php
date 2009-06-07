<?php
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @version 3.0 Jun 7, 2009
 * @package kwalbum
 * @since 3.0 Jun 7, 2009
 */
$protectedDirectory = dirname(__FILE__).'/protected/';

$yii = $protectedDirectory.'yii/framework/yii.php';
$config = $protectedDirectory.'config/main.php';

// Remove this line in production mode.
// It is set to false in YiiBase.php if not defined here.
defined('YII_DEBUG') or define('YII_DEBUG', true);

require_once($yii);
Yii::createWebApplication($config)->run();
