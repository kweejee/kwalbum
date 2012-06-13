var map;
var proj = new OpenLayers.Projection("EPSG:4326");
var id;

function initMap(lat,lon,loc_id){//TODO: change order to lon,lat
	id = loc_id;
	var loc = new OpenLayers.LonLat(lon, lat);
	if(!lat) var zoom = 2;
	else var zoom = 10;

	map = new OpenLayers.Map('mapCanvas',{
		projection: "EPSG:41001",
		displayProjection: new OpenLayers.Projection("EPSG:4326"),
		units: "m",
		maxResolution:156543.0339,
		maxExtent:new OpenLayers.Bounds(-20037508.34,-20037508.34,20037508.34,20037508.34),
		controls:[new OpenLayers.Control.Navigation(),new OpenLayers.Control.MousePosition()]
	});

//	var tah = new OpenLayers.Layer.OSM.Osmarender("Tiles@Home",{transitionEffect:'resize'});
	var mapnik = new OpenLayers.Layer.OSM.Mapnik("Mapnik",{transitionEffect:'resize'});
	var cycle = new OpenLayers.Layer.OSM.CycleMap("Cycle Map",{transitionEffect:'resize'});

	map.addLayers([mapnik,cycle]);
	map.addControl(new OpenLayers.Control.LayerSwitcher());
	
	loc.transform(proj,map.getProjectionObject());

	var vector = new OpenLayers.Layer.Vector("Marker",{
		styleMap: new OpenLayers.StyleMap({
//			externalGraphic: "KWALBUM_URL/media/mapmarkers/marker1.png",
//			pointRadius: 10
		})
	});
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
		$.post("KWALBUM_URL/~ajax/SetItemMapLocation", {id:id,lon:point.lon,lat:point.lat});
	}
}