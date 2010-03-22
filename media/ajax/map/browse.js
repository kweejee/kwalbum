var map;
var PI = 3.14159265358979323846;

function getTop(bound){
	return(Math.atan( Math.exp( (bound.top *180 / 20037508.34) / 180 * PI))/PI *360 -90);
}
function getBottom(bound){
	return(Math.atan( Math.exp( (bound.bottom *180 / 20037508.34) / 180 * PI))/PI *360 -90);
}
function getLeft(bound){
	return( bound.left *180 / 20037508.34);
}
function getRight(bound){
	return( bound.right *180 / 20037508.34);
}

function get_item_url(bounds){
	return "KWALBUM_URL/~ajax/GetMapItems"+"?z=" + this.map.getZoom()
	+ "&l=" + getLeft(bounds)
	+ "&t=" + getTop(bounds)
	+ "&r=" + getRight(bounds)
	+ "&b=" + getBottom(bounds);
}
function get_loc_url(bounds){
	return "KWALBUM_URL/~ajax/GetMapLocations"+"?z=" + this.map.getZoom()
	+ "&l=" + getLeft(bounds)
	+ "&t=" + getTop(bounds)
	+ "&r=" + getRight(bounds)
	+ "&b=" + getBottom(bounds);
}
function initMap(lon,lat,zoom){
	var center = new OpenLayers.LonLat(lon,lat);
	map = new OpenLayers.Map('mapCanvas',{
		projection: "EPSG:41001",
		//projection: new OpenLayers.Projection("EPSG:900913"),
		displayProjection: new OpenLayers.Projection("EPSG:4326"),
		units: "m",
		maxResolution:156543.0339,
		maxExtent:new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34),
		controls:[
			new OpenLayers.Control.Navigation(),
			new OpenLayers.Control.PanZoomBar(),
			new OpenLayers.Control.ScaleLine(),
			new OpenLayers.Control.Permalink('permalink'),
			new OpenLayers.Control.MousePosition(),
			new OpenLayers.Control.OverviewMap(),
			new OpenLayers.Control.KeyboardDefaults()
		]
	});

	// LAYERS
	//var gmap = new OpenLayers.Layer.Google("Google");
	var tah = new OpenLayers.Layer.OSM.Osmarender("Tiles@Home",{
		attribution:'<a href="http://www.openstreetmap.org/">OpenStreetMap</a>',
		transitionEffect:'resize'
	});
	tah.setIsBaseLayer(true);
	var mapnik = new OpenLayers.Layer.OSM.Mapnik("Mapnik",{
		attribution:'<a href="http://www.openstreetmap.org/">OpenStreetMap</a>',
		transitionEffect:'resize'
	});
	var cycle = new OpenLayers.Layer.OSM.CycleMap("Cycle Map",{
		attribution:'<a href="http://www.opencyclemap.org/">OpenCycleMap</a>',
		transitionEffect:'resize'
	});

	map.addLayers([mapnik,tah,cycle]);

	// create POI layer
	itemPOI = new OpenLayers.Layer.MarkerGrid("Items",{
		type:'txt',
		getURL: get_item_url,
		buffer: 0
	});
	itemPOI.setIsBaseLayer(false);
	itemPOI.setVisibility(true);
	locPOI = new OpenLayers.Layer.MarkerGrid("Locations",{
		type:'txt',
		getURL: get_loc_url,
		buffer: 0
	});
	locPOI.setIsBaseLayer(false);
	locPOI.setVisibility(true);
	map.addLayers([itemPOI,locPOI]);


	center.transform(map.displayProjection,map.getProjectionObject());
	map.setCenter(center, zoom);
	map.addControl(new OpenLayers.Control.LayerSwitcher());
}