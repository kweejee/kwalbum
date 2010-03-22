<?php
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kwalbum
 * @since Mar 21, 2010
 */

// TODO: set defaults in config file
if (isset($_GET['lat']))
	$lat = (float)$_GET['lat'];
else
	$lat = 38;
if (isset($_GET['lon']))
	$lon = (float)$_GET['lon'];
else
	$lon = -95;
if (isset($_GET['zoom']))
	$zoom = (int)$_GET['zoom'];
else
	$zoom = 4;
?>
<style>
	.mapCanvas {
		left:2px;
        width: 99%;
        height: 80%;
        border: 1px solid black;
        padding: 0px;
		position:absolute;
	}
</style>
<div id="mapCanvas" class="mapCanvas"></div>
<?php //<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAbxLAEd4gMLUT1GGbfclXDhTdxZaz1IVhygKAIp3JPZvDey3PIBT27zU-duqzdH4WvPGG00L1meRNFg'></script> ?>
<script src="http://www.openlayers.org/api/OpenLayers.js"></script>
<script src="http://www.openstreetmap.org/openlayers/OpenStreetMap.js"></script>

<script type="text/javascript" src="<?php echo $kwalbum_url; ?>/media/ajax/map/MarkerGrid.js"></script>
<script type="text/javascript" src="<?php echo $kwalbum_url; ?>/media/ajax/map/MarkerTile.js"></script>

<script src="<?php echo $kwalbum_url; ?>/media/ajax/map/browse.js"></script>

<script type="text/javascript">
	initMap(<?php echo "$lon,$lat,$zoom"; ?>);
</script>