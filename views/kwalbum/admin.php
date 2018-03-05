<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009-2012 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @version 3.0 Jun 30, 2009
 * @package kwalbum
 * @since 3.0 Jun 30, 2009
 */

?>
<div class="box">
	<big><b>Admin Options</b></big>
	<ul>
		<li><?php echo HTML::anchor($kwalbum_url.'/~admin/locations', 'Locations'); ?></li>
		<li><?php echo HTML::anchor($kwalbum_url.'/~admin/tags', 'Tags'); ?></li>
		<li><?php echo HTML::anchor($kwalbum_url.'/~admin/people', 'People'); ?></li>
		<li><?php echo HTML::anchor($kwalbum_url.'/~admin/users', 'User Accounts'); ?></li>
	</ul>
</div>