// Kwalbum 3.0
var kwalbum = {
    resize_pop: null,
    images: {},
    current_id: null
};
$(document).ready(function(){
    kwalbum.resize_pop = $("#kwalbumResizePopup");
    $(window).on('resize', kwalbum.centerResizePopup);
	$(".kwalbumThumbnailLink").on('click', function(event) {
        if (event.which === 1) { // left click only
            var item_id = event.currentTarget.id;
            kwalbum.loadResizedImage(item_id);
            event.preventDefault();
        }
    });
    $(document).keyup(function(e) {
        if (e.keyCode == 27) { // esc
            $("#kwalbumResizePopup").hide();
        } else if (e.keyCode == 39 || e.keyCode == 37) { // right & left
            if (kwalbum.current_id && $("#kwalbumResizePopup").css('display') === 'block') {
                if (e.keyCode == 39) {
                    kwalbum.goToNextImage();
                } else {
                    kwalbum.goToNextImage(true);
                }
            }
        }
    });
});

kwalbum.goToNextImage = function (backward) {
    var current_item = kwalbum.images[kwalbum.current_id];
    if (!current_item) {
        return;
    }
    var next_id = current_item.next_id;
    if (backward) {
        next_id = current_item.prev_id;
    }
    if (!next_id) {
        return;
    }
    kwalbum.loadResizedImage('kwalbumItem_'+next_id);
};

kwalbum.centerResizePopup = function (hide_resize_message) {
    if (hide_resize_message) {
        $("#kwalbumResizeMessage").hide();
    }
    $("#kwalbumResizeBox div+div").hide();
    $("#kwalbumResized"+kwalbum.current_id).show();
    var left = (document.documentElement.clientWidth - $("#kwalbumResizeBox").width()) / 2;
    left = Math.max(0, left);
    var top = (document.documentElement.clientHeight - $("#kwalbumResizeBox").height()) / 2;
    top = Math.max(0, top);
    kwalbum.resize_pop
        .css('left', left+'px')
        .css('top', top+'px');
};
kwalbum.setResizedImageHTML = function (data) {
    $("#kwalbumResizeBox").append("<div id='kwalbumResized"+data.id+"' style='display:none;'>"
        +data.img_html);
    if (data.type !== "description only") {
        $("#kwalbumResizeBox").append("<div class='kwalbumResizedDescription'>"+data.description+"</div>");
    }
    $("#kwalbumResizeBox").append("</div>");
    if (data.type === "description only" || data.type === "unknown") {
        kwalbum.centerResizePopup(true);
    }
    $("#kwalbumResized"+data.id+" img")
        .on('load', function() {
            kwalbum.centerResizePopup(true);
        })
        .on('click', function(event) {
            kwalbum.centerResizePopup(true);
            if (event.which === 1) { // left click only
                kwalbum.goToNextImage();
                event.preventDefault();
            }
        });
};

kwalbum.loadResizedImage = function (item_elem_id) {
    if (kwalbum.resize_pop.css('display') === 'none') {
        kwalbum.resize_pop.show();
    }

    var item_id = item_elem_id.split('_')[1]
    if (!item_id) {
        return;
    }
    kwalbum.current_id = item_id;
    if (kwalbum.images[item_id]) {
        kwalbum.centerResizePopup(true);
        return;
    }

    $("#kwalbumResizeMessage").show();
    kwalbum.centerResizePopup();
    var resize_url = "/kwalbum/~ajax/GetResizedImage?id="+item_id; // TODO: dates, location, tags, people
    $.ajax({
        url: resize_url,
        dataType: 'json',
        success: function (data) {
            kwalbum.images[item_id] = data;
            kwalbum.setResizedImageHTML(data);
        }
    });
};