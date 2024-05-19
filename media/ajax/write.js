// Kwalbum 3.0
const loc = {
	lastXhr: null,
	cache: {}
};
const tags = {
	lastXhr: null,
	cache: {}
};
$(document).ready(function(){
	$("#loc").focus().autocomplete({
		minLength: 2,
		source: function( request, response ) {
			let term = request.term;
			if ( term in loc.cache ) {
				response( loc.cache[ term ] );
				return;
			}

			loc.lastXhr = $.getJSON( "KWALBUM_URL/~ajax/getInputLocations", request, function( data, status, xhr ) {
				loc.cache[ term ] = data;
				if ( xhr === loc.lastXhr ) {
					response( data );
				}
			});
		}
	});
	$("#tags").autocomplete({
		minLength: 2,
		source: function( request, response ) {
			let term = request.term;
			if ( term in tags.cache ) {
				response( tags.cache[ term ] );
				return;
			}

			tags.lastXhr = $.getJSON( "KWALBUM_URL/~ajax/getInputTags", request, function( data, status, xhr ) {
				tags.cache[ term ] = data;
				if ( xhr === tags.lastXhr ) {
					response( data );
				}
			});
		}
	});
    $("#date").datepicker({dateFormat: "yy-mm-dd"});
});
