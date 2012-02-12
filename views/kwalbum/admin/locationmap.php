<?php
/**
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2010 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kwalbum
 * @since March 19, 2010
 */

// TODO: put default latitude and longitude in config file
if ( ! $location->latitude)
	$location->latitude = 40;
if ( ! $location->longitude)
	$location->longitude = -80
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<title>Mapping : <?php echo $location->name; ?></title>
	<style>.mapCanvas {left:0px;top:0px;width:100%;height:100%;border:0px;padding:0px;position:absolute;}</style>
</head>
<body>
<div id="mapCanvas" class="mapCanvas"></div>
<script src="http://www.openlayers.org/api/OpenLayers.js" type="text/javascript"></script>
<script src="http://www.openstreetmap.org/openlayers/OpenStreetMap.js" type="text/javascript"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script src="<?php echo $kwalbum_url; ?>/media/ajax/map/location.admin.js" type="text/javascript"></script>
<script type="text/javascript">
	initMap(<?php echo $location->latitude.','.$location->longitude.','.$location->id; ?>);
</script>
</body>
</html>