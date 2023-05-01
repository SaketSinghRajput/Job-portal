function JM_Bing_Map(){

	var jm_bing_map = jQuery('.noo-mb-job-location');

	if (jm_bing_map.length > 0) {

		jm_bing_map.each(function (index, el) {

			var $$ = jQuery(this);
				id = $$.data('id'),
				zoom = parseInt(JM_Bing_Value.zoom),
				lat = parseFloat(JM_Bing_Value.lat),
                lng = parseFloat(JM_Bing_Value.lng);
                // bmarkers=[];

                // var lat_current = jQuery('#latitude').val(),
                //     lng_current = jQuery('#longitude').val();
                    
                // if (typeof lat_current !== 'undefined' && lat_current !== '') {
                //     lat = parseFloat(lat_current);
                // }

                // if (typeof lng_current !== 'undefined' && lng_current !== '') {
                //     lng = parseFloat(lng_current);
                // }

                var latitude = jQuery('#noo-mb-lat').val(),
                    longitude = jQuery('#noo-mb-lon').val();

                if (typeof latitude !== 'undefined' && latitude !== '') {
                    lat = parseFloat(latitude);
                }

                if (typeof longitude !== 'undefined' && longitude !== '') {
                    lng = parseFloat(longitude);
                }   

	            var map = new Microsoft.Maps.Map(document.getElementById(id), {
	            	/* No need to set credentials if already passed in URL */
                    center: new Microsoft.Maps.Location(lat, lng),
                    zoom: zoom,
	            });
	            var center   = map.getCenter();
		        var Events   = Microsoft.Maps.Events;
		        var Location = Microsoft.Maps.Location;
		        var Pushpin  = Microsoft.Maps.Pushpin;
		        var pins = [
		                new Pushpin(new Location(center.latitude, center.longitude), {icon: 'https://www.bingmapsportal.com/Content/images/poi_custom.png', draggable: true }),
		            ];

	            // var pushpin = new Microsoft.Maps.Pushpin(map.getCenter(), { icon: 'https://www.bingmapsportal.com/Content/images/poi_custom.png',draggable:true});
	            map.entities.push(pins);
	            Events.addHandler(pins[0], 'dragend', function () {  displayPinCoordinates(pins); });

	            Microsoft.Maps.loadModule('Microsoft.Maps.AutoSuggest', function () {
	                var options = {
	                    maxResults: 4,
	                    map: map
	                };
	                var manager = new Microsoft.Maps.AutosuggestManager(options);
	                manager.attachAutosuggest('#noo-mb-location-address', '.map_type', selectedSuggestion);
	            });

	            function displayPinCoordinates(pins){
	            	var pin_location =pins[0].getLocation();
	            	document.getElementById('noo-mb-lat').value = pin_location.latitude ;
	            	document.getElementById('noo-mb-lon').value = pin_location.longitude;
	            }


	            function selectedSuggestion(suggestionResult) {
	            	var map = new Microsoft.Maps.Map(document.getElementById(id), {
	                    center: new Microsoft.Maps.Location(suggestionResult.location.latitude, suggestionResult.location.latitude),
	                    zoom: zoom,
		            });
	                map.entities.clear();
	                map.setView({ bounds: suggestionResult.bestView });
		            var center   = map.getCenter();
	                var pushpin = [
	                	new Pushpin(new Location(suggestionResult.location.latitude, suggestionResult.location.longitude), {icon: 'https://www.bingmapsportal.com/Content/images/poi_custom.png', draggable: true })
	                ];
	                map.entities.push(pushpin);
	                Events.addHandler(pushpin[0], 'dragend', function () {  displayPinCoordinates(pushpin); });
	                document.getElementById('noo-mb-lat').value = suggestionResult.location.latitude ;
	                document.getElementById('noo-mb-lon').value = suggestionResult.location.longitude;
	            }
		});
	}
}