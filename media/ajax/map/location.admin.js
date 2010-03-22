var map;
var proj = new OpenLayers.Projection("EPSG:4326");
var id;

function initMap(lat,lon,loc_id){
	id = loc_id;
	var loc = new OpenLayers.LonLat(lon, lat);
	var zoom = 4;

	map = new OpenLayers.Map('mapCanvas',{
		projection: "EPSG:41001",
		displayProjection: new OpenLayers.Projection("EPSG:4326"),
		units: "m",
		maxResolution:156543.0339,
		maxExtent:new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34),
		controls:[new OpenLayers.Control.Navigation(),new OpenLayers.Control.PanZoomBar(),new OpenLayers.Control.ScaleLine(),	new OpenLayers.Control.MousePosition()]
	});
	var tah = new OpenLayers.Layer.OSM.Osmarender("Tiles@Home",{transitionEffect:'resize'});
	var mapnik = new OpenLayers.Layer.OSM.Mapnik("Mapnik",{transitionEffect:'resize'});
	var cycle = new OpenLayers.Layer.OSM.CycleMap("Cycle Map",{transitionEffect:'resize'});

	map.addLayers([mapnik,tah,cycle]);
	map.addControl(new OpenLayers.Control.LayerSwitcher());
	
	loc.transform(proj,map.getProjectionObject());

	var vector = new OpenLayers.Layer.Vector("Marker",{styleMap:new OpenLayers.StyleMap()});
	vector.addFeatures([new OpenLayers.Feature.Vector(new OpenLayers.Geometry.Point(loc.lon,loc.lat))]);

	var dragfeature = new OpenLayers.Control.DragFeature(vector,{'onComplete': onCompleteMove});
	map.addLayer(vector);
	map.setCenter(new OpenLayers.LonLat(loc.lon,loc.lat),zoom);
	map.addControl(dragfeature);
	dragfeature.activate();
}

function onCompleteMove(feature){
	if(feature){
		var point = new OpenLayers.LonLat(feature.geometry.x, feature.geometry.y);
		point.transform(map.getProjectionObject(), proj);
		$.post("KWALBUM_URL/~ajaxAdmin/SaveMapLocation", {id:id,lon:point.lon,lat:point.lat},
			function(){opener.document.getElementById('coord'+id).innerHTML = point.lon+','+point.lat;}
		);
	}
}