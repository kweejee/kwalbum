<?php
/**
 *
 *
 * @author Tim Redmond <kweejee@tummycaching.com>
 * @copyright Copyright 2009 Tim Redmond
 * @license GNU General Public License version 3 <http://www.gnu.org/licenses/>
 * @package kwalbum
 * @since Sep 21, 2009
 */

?>

<div id="map" class="smallmap"></div>
<div class='box'>

This page will have the map with links to all the locations and items.
</div>
<style>
	.smallmap {
    width: 512px;
    height: 256px;
    border: 1px solid #ccc;
}
</style>
<script src='http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAAbxLAEd4gMLUT1GGbfclXDhTdxZaz1IVhygKAIp3JPZvDey3PIBT27zU-duqzdH4WvPGG00L1meRNFg'></script>
<script src="http://www.openlayers.org/api/OpenLayers.js"></script>
    <script type="text/javascript">
        var lon = 5;
        var lat = 40;
        var zoom = 5;
        var map, select;

        function init(){
            var options = {
                projection: new OpenLayers.Projection("EPSG:900913"),
                displayProjection: new OpenLayers.Projection("EPSG:4326"),
                units: "m",
                maxResolution: 156543.0339,
                maxExtent: new OpenLayers.Bounds(-20037508.34, -20037508.34,
                                                 20037508.34, 20037508.34)
            };
            map = new OpenLayers.Map('map', options);
            var mapnik = new OpenLayers.Layer.TMS(
                "OpenStreetMap (Mapnik)",
                "http://tile.openstreetmap.org/",
                {
                    type: 'png', getURL: osm_getTileURL,
                    displayOutsideMaxExtent: true,
                    attribution: '<a href="http://www.openstreetmap.org/">OpenStreetMap</a>'
                }
            );
            var gmap = new OpenLayers.Layer.Google("Google", {sphericalMercator:true});
            var sundials = new OpenLayers.Layer.Vector("KML", {
                projection: map.displayProjection,
                strategies: [new OpenLayers.Strategy.Fixed()],
                protocol: new OpenLayers.Protocol.HTTP({
                    url: "kml/sundials.kml",
                    format: new OpenLayers.Format.KML({
                        extractStyles: true,
                        extractAttributes: true
                    })
                })
            });

            map.addLayers([mapnik, gmap, sundials]);

            select = new OpenLayers.Control.SelectFeature(sundials);
            
            sundials.events.on({
                "featureselected": onFeatureSelect,
                "featureunselected": onFeatureUnselect
            });
  
            map.addControl(select);
            select.activate();   

            map.addControl(new OpenLayers.Control.LayerSwitcher());

            map.zoomToExtent(
                new OpenLayers.Bounds(
                    68.774414, 11.381836, 123.662109, 34.628906
                ).transform(map.displayProjection, map.projection)
            );
        }
        function onPopupClose(evt) {
            select.unselectAll();
        }
        function onFeatureSelect(event) {
            var feature = event.feature;
            var selectedFeature = feature;
            var popup = new OpenLayers.Popup.FramedCloud("chicken", 
                feature.geometry.getBounds().getCenterLonLat(),
                new OpenLayers.Size(100,100),
                "<h2>"+feature.attributes.name + "</h2>" + feature.attributes.description,
                null, true, onPopupClose
            );
            feature.popup = popup;
            map.addPopup(popup);
        }
        function onFeatureUnselect(event) {
            var feature = event.feature;
            if(feature.popup) {
                map.removePopup(feature.popup);
                feature.popup.destroy();
                delete feature.popup;
            }
        }
        function osm_getTileURL(bounds) {
            var res = this.map.getResolution();
            var x = Math.round((bounds.left - this.maxExtent.left) / (res * this.tileSize.w));
            var y = Math.round((this.maxExtent.top - bounds.top) / (res * this.tileSize.h));
            var z = this.map.getZoom();
            var limit = Math.pow(2, z);

            if (y < 0 || y >= limit) {
                return OpenLayers.Util.getImagesLocation() + "404.png";
            } else {
                x = ((x % limit) + limit) % limit;
                return this.url + z + "/" + x + "/" + y + "." + this.type;
            }
        }


		 function get_poi_url() {
    // custom get_poi_url() function that uses the the
    // getLatLonFromPixel function to determine the map
    // boudries and calculates the longitude and latitude
    // using the map projection transform functions of
    // Openlayers.

    // the Zoom Level selected
    var zoom = this.map.getZoom();

    // the top left corner
    var tlLonLat = this.map.getLonLatFromPixel(new OpenLayers.Pixel(1,1)).
          transform(this.map.getProjectionObject(),this.map.displayProjection);

    // the bottom right corner
    var mapsize = this.map.getSize();
    var brLonLat = this.map.getLonLatFromPixel(new OpenLayers.Pixel(mapsize.w - 1, mapsize.h - 1)).
          transform(this.map.getProjectionObject(),this.map.displayProjection);

    return url    + "&zoom=" + zoom
          + "&tllon=" + tlLonLat.lon
          + "&tllat=" + tlLonLat.lat
          + "&brlon=" + brLonLat.lon
          + "&brlat=" + brLonLat.lat
          + "&search="    + zoom + ";"
                + tlLonLat.lon + ";"
                + brLonLat.lat + ";"
                + brLonLat.lon + ";"
                + tlLonLat.lat;
	}

	init();
    </script>
