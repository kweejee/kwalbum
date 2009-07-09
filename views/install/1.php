<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @version 3.0 Jun 30, 2009
 * @package kwalbum
 * @since 3.0 Jun 30, 2009
 */

?>
<div class="box">
	<h2>setup stage 1</h2>
	form to get first OpenId and name/title to show
	<form action='<?php echo URL; ?>install' method='post'>
	OpenId: <input type='text' name='openid'/><br/>
	Name: <input type='text'name='name'/>
	<input type='submit' name='action' value='Add Kwalbum to Database'/>
	</form>
</div>