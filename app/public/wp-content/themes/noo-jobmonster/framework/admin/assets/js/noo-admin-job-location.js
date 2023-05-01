jQuery(document).ready(function ($) {

    var lat = $('#map-lat').val();
    var lon = $('#map-lon').val();

    var input_address = $('.term-name-wrap #name');

    if ($('.term-name-wrap #tag-name').length) {
        input_address = $('.term-name-wrap #tag-name');
    }
    var locationpickerParams = {
        location: {
            latitude: lat,
            longitude: lon,
        },
        radius: 0,
        inputBinding: {
            latitudeInput: $('#map-lat'),
            longitudeInput: $('#map-lon'),
            locationNameInput: input_address
        },
        enableAutocomplete: true,
        enableAutocompleteBlur: true,
    }

    if (nooLocationPicker && nooLocationPicker.componentRestrictions) {
        locationpickerParams.autocompleteOptions = {
            types: nooLocationPicker.types,
            componentRestrictions: {'country': nooLocationPicker.componentRestrictions}
        };
    }

    $('#jm_location_term_map').locationpicker(locationpickerParams);

    function noo_mb_map_field() {
        var field = $('.noo-mb-job-location');
        var lat = parseFloat(nooLocationPicker.lat);
        var lon = parseFloat(nooLocationPicker.lng);
        var zoom = parseInt(nooLocationPicker.zoom);
        
        var lat_current = $('#noo-mb-lat').val(),
            lng_current = $('#noo-mb-lon').val();

        if (typeof lat_current !== 'undefined' && lat_current !== '') {
            lat = parseFloat(lat_current);
        }

        if (typeof lng_current !== 'undefined' && lng_current !== '') {
            lon = parseFloat(lng_current);
        }
        var locationpickerParams = {
            location: {
                latitude: lat,
                longitude: lon,
            },
            radius: 0,
            zoom: zoom,
            inputBinding: {
                latitudeInput: $('.noo-mb-lat'),
                longitudeInput: $('.noo-mb-lon'),
                locationNameInput: $('.noo-mb-location-address')
            },
            enableAutocomplete: true,
            enableAutocompleteBlur: true,
        }

        if (nooLocationPicker && nooLocationPicker.componentRestrictions) {
            locationpickerParams.autocompleteOptions = {
                types: nooLocationPicker.types,
                componentRestrictions: {'country': nooLocationPicker.componentRestrictions}
            };
        }

        field.locationpicker(locationpickerParams);
    }

    noo_mb_map_field();

});

