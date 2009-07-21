<?php
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kohana
 * @since Jul 21, 2009
 */
?>
<p>
	<?php echo html::anchor(URL,'main page')?> -
	<a href='<?php echo URL?>item/1'>single item</a> -
	<a href='<?php echo URL?>admin'>admin</a>
	<br/>
	browse by:
	<a href='<?php echo URL?>2005'>single year</a> -
	<a href='<?php echo URL?>2009/6/20'>full date</a> -
	<a href='<?php echo URL?>tag/test'>tag</a> -
	<a href='<?php echo URL?>Home'>location</a> -
</p>