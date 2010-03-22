/* Copyright (c) 2006-2007 MetaCarta, Inc., published under a modified BSD license.
 * See http://svn.openlayers.org/trunk/openlayers/repository-license.txt 
 * for the full text of the license.
 *
 * Modified for Kwalbum in 2010.
 *
 * @requires OpenLayers/Tile.js
 * 
 * Class: OpenLayers.Tile.MarkerTile
 * Instances of OpenLayers.Tile.MarkerTile are used to manage the image tiles
 * used by various layers.  Create a new image tile with the
 * <OpenLayers.Tile.MarkerTile> constructor.
 *
 * Inherits from:
 *  - <OpenLayers.Tile>
 */
OpenLayers.Tile.MarkerTile = OpenLayers.Class(OpenLayers.Tile, {
	// {Array(<OpenLayers.Feature>)} list of features in this tile
	features: null,

	// {String}
	url: null,
    
	/**
	 * Constructor: OpenLayers.Tile.MarkerTile
	 * Constructor for a new <OpenLayers.Tile.MarkerTile> instance.
	 *
	 * Parameters:
	 * layer - {<OpenLayers.Layer>} layer that the tile will go in.
	 * position - {<OpenLayers.Pixel>}
	 * bounds - {<OpenLayers.Bounds>}
	 * url - {<String>}
	 * size - {<OpenLayers.Size>}
	 */
	initialize: function(layer, position, bounds, url, size) {
		OpenLayers.Tile.prototype.initialize.apply(this, arguments);
		this.url = url;
		this.features = [];
	},

	/**
	 * nullify references to prevent circular references and memory leaks
	 */
	destroy: function() {
		this.destroyAllFeatures();
		this.features = null;
		this.url = null;
	},

	/**
	 *  Clear the tile of any bounds/position-related data so that it can
	 *   be reused in a new location.
	 */
	clear: function() {
		this.destroyAllFeatures();
	},
    
	/**
	 * Check that a tile should be drawn, and load features for it.
	 *
	 * Returns:
	 * {Boolean} Always returns true.
	 */

	draw: function() {
		if (this.layer != this.layer.map.baseLayer && this.layer.reproject) {
			this.bounds = this.getBoundsFromBaseLayer(this.position);
		}

		if (!OpenLayers.Tile.prototype.draw.apply(this, arguments)) {
			return false;
		}

		if (this.isLoading) {
			//if we're already loading, send 'reload' instead of 'loadstart'.
			this.events.triggerEvent("reload");
		} else {
			this.isLoading = true;
			this.events.triggerEvent("loadstart");
		}

		this.url = this.layer.getURL(this.bounds);

		this.loadFeaturesForRegion(this.requestSuccess, this.requestFailure);

		this.drawn = true;
		return true;
	},
    
	/**
     * Reposition the tile.
     *
     * Parameters:
     * bounds - {<OpenLayers.Bounds>}
     * position - {<OpenLayers.Pixel>}
     * redraw - {Boolean} Call draw method on tile after moving.
     *     Default is true
     */
	moveTo: function (bounds, position, redraw) {

		this.destroyAllFeatures();
            
		OpenLayers.Tile.prototype.moveTo.apply(this, arguments);

		this.url = this.layer.getURL(this.bounds);

		this.loadFeaturesForRegion(this.requestSuccess, this.requestFailure);

	},


	/**
	 * get the full request string from the ds and the tile params
	 *     and call the AJAX loadURL().
	 *
	 * Input are function pointers for what to do on success and failure.
	 *
	 * Parameters:
	 * success - {function}
	 * failure - {function}
	 */
	loadFeaturesForRegion:function(success, failure) {
		OpenLayers.loadURL(this.url, null, this, success,failure);
	},
    
	/**
	 * Parameters:
	 * request - {XMLHttpRequest}
	 */
	requestFailure: function(request) {
	//alert("requestFailure");
	},
	requestSuccess: function(request) {
		this.clear();
    
		var text = request.responseText;
		var lines = text.split('\n');
		var columns;
		var mylocation, title;
		var type;
		
		// length - 1 to allow for trailing new line
		for (var lcv = 0; lcv < (lines.length); lcv++) {
			var currLine = lines[lcv].replace(/^\s*/,'').replace(/\s*$/,'');
            
			if (!columns) {
				//First line is columns
				columns = currLine.split('\t');
			} else {
				mylocation = new OpenLayers.LonLat(0,0);
				var vals = currLine.split('\t');
				title = null;
				type = null;
				description = null;

				var set = false;

				for (var valIndex = 0; valIndex < vals.length; valIndex++) {
					if (vals[valIndex]) {
						if (columns[valIndex] == 'point') {
							var coords = vals[valIndex].split(',');
							mylocation.lon = parseFloat(coords[0]);
							mylocation.lat = parseFloat(coords[1]);
							set = true;
						} else if (columns[valIndex] == 'type'){
							type = vals[valIndex];
						}else if (columns[valIndex] == 'title') {
							title = vals[valIndex];
						} else if (columns[valIndex] == 'description') {
							description = vals[valIndex];
						}
					}
				}
				if (set) {
					var data = {};

					// MERCATORIZE
					mylocation.lon = mylocation.lon * 20037508.34 / 180;
					mylocation.lat = (Math.log(Math.tan( (90 + mylocation.lat) * PI / 360)) / (PI / 180)) * 20037508.34 / 180;

					if (type == 'i'){
						data.icon = new OpenLayers.Icon('KWALBUM_URL/media/mapmarkers/marker1.png',new OpenLayers.Size(12,20), new OpenLayers.Pixel(-6,-20));
						data.popupSize = new OpenLayers.Size(200,200);
						data['popupContentHTML'] = '<div class="kwalbumPopupTitle"><a href="KWALBUM_URL/~'+title+'"><img src="KWALBUM_URL/~'+title+'/~item/thumbnail.jpg"/></a></div><div class="kwalbumPopupDescription">'+description+'</div>';
					}else if(type == 'l'){
						data.icon = new OpenLayers.Icon('KWALBUM_URL/media/mapmarkers/marker0.png',new OpenLayers.Size(12,20), new OpenLayers.Pixel(-6,-20));
						data.popupSize = new OpenLayers.Size(200,200);
						data['popupContentHTML'] = '<div class="kwalbumPopupTitle"><a href="'+description+'">'+title+'</a></div><div class="kwalbumPopupDescription"><a href="'+description+'">browse pictures from this location</a></div>';
					}else if(type == 'g'){
						data.icon = new OpenLayers.Icon('KWALBUM_URL/media/mapmarkers/marker2.png',new OpenLayers.Size(20,20), new OpenLayers.Pixel(-10,-10));
						data.popupSize = new OpenLayers.Size(120,100);
						var str = 'There are '+title+' items in this area making it too crowded to show on the map.';
						if (description != 17)
							str += 'Zoom in more to see them.';
						data['popupContentHTML'] = str;
					}else{
						data.icon = OpenLayers.Marker.defaultIcon();
					}
				}

				// data['overflow'] = overflow || "auto";

				// We must track both features and markers so they can be properly deallocated later
				var feature = new OpenLayers.Feature(this.layer, mylocation, data);
				this.features.push(feature);
				var marker = feature.createMarker();

				if (type)
					marker.events.register('click', feature, this.markerClick);
				this.layer.addMarker(marker);
			}
		}
		if (this.events) {
			this.events.triggerEvent("loadend");
		}
	},

	/**
	 * Parameters:
	 * evt - {Event}
	 */
	markerClick: function(evt) {
		sameMarkerClicked = (this == this.layer.selectedFeature);
		this.layer.selectedFeature = (!sameMarkerClicked) ? this : null;

		for(var i=0; i < this.layer.map.popups.length; i++) {
			this.layer.map.removePopup(this.layer.map.popups[i]);
		}

		var popup = this.createPopup();
		popup.panMapIfOutOfView = true;
		popup.addCloseBox(function(){this.map.removePopup(this);});
		this.layer.map.addPopup(popup);
		popup.addCloseBox(function(){this.map.removePopup(this);});
		popup.setBackgroundColor('#ddffdd');

		OpenLayers.Event.stop(evt);
	},

	/**
	 * Iterate through and call destroy() on each feature, removing it from
	 *   the local array
	 */
	destroyAllFeatures: function() {
		if (this.features)
		{
			while(this.features.length > 0)
			{
				var feature = this.features.shift();
				if( feature.marker != null )
					if( this.layer != null )
						this.layer.removeMarker(feature.marker);
				feature.destroy();
			}
		}
	},

	CLASS_NAME: "OpenLayers.Tile.MarkerTile"
}
);