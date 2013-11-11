// Kwalbum 3.0
$(document).ready(function(){
    kwalbumSetAutocomplete("#kwalbum_me_location", "GetInputLocations");
    kwalbumSetAutocomplete("#kwalbum_me_add_tags", "GetInputTags");
    kwalbumSetAutocomplete("#kwalbum_me_rem_tags", "GetInputTags");
    kwalbumSetAutocomplete("#kwalbum_me_add_people", "GetInputPersons");
    kwalbumSetAutocomplete("#kwalbum_me_rem_people", "GetInputPersons");
    $(".kwalbuMassInclude input").on("change", function(){
        $(".kwalbumMassEditSave").attr("disabled", false);
    });
});

var kwalbumSetAutocomplete = function (input_selector, autocomplete_action) {
    var $input = $(input_selector);
    var cache = $input.data("kwalbum_cache");
    if (typeof cache !== "object") {
        cache = {};
    }
    var data = {
        minLength: 2,
        source: function(request, response) {
            var term = request.term;
            if (term in cache) {
                response(cache[term]);
                return;
            }

            $input.data("kwalbum_last_xhr", $.getJSON(
                "KWALBUM_URL/~ajax/"+autocomplete_action,
                request,
                function(returned_data, status, xhr) {
                    cache[term] = returned_data;
                    console.log(cache);
                    if (xhr === $input.data("kwalbum_last_xhr")) {
                        response(returned_data);
                    }
                }
            ));
        }
    }
    $input.autocomplete(data);
};