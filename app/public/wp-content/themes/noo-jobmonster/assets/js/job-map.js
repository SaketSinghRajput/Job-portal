var infoBox;
(function($){
	"use strict";
	function isTouch(){
		return !!('ontouchstart' in window) || ( !! ('onmsgesturechange' in window) && !! window.navigator.maxTouchPoints);
	}
	var map_style_dark=[{featureType:"all",elementType:"labels.text.fill",stylers:[{saturation:36},{color:"#000000"},{lightness:40}]},{featureType:"all",elementType:"labels.text.stroke",stylers:[{visibility:"on"},{color:"#000000"},{lightness:16}]},{featureType:"all",elementType:"labels.icon",stylers:[{visibility:"off"}]},{featureType:"administrative",elementType:"all",stylers:[{lightness:"-1"}]},{featureType:"administrative",elementType:"geometry.fill",stylers:[{color:"#000000"},{lightness:20}]},{featureType:"administrative",elementType:"geometry.stroke",stylers:[{color:"#000000"},{lightness:17},{weight:1.2}]},{featureType:"administrative.country",elementType:"all",stylers:[{lightness:"20"}]},{featureType:"administrative.country",elementType:"geometry.stroke",stylers:[{visibility:"on"},{color:nooJobGmapL10n.primary_color}]},{featureType:"administrative.country",elementType:"labels.text",stylers:[{color:nooJobGmapL10n.primary_color},{visibility:"simplified"}]},{featureType:"administrative.country",elementType:"labels.icon",stylers:[{visibility:"off"}]},{featureType:"administrative.province",elementType:"all",stylers:[{lightness:"20"}]},{featureType:"administrative.province",elementType:"labels.text",stylers:[{color:nooJobGmapL10n.primary_color},{visibility:"off"}]},{featureType:"administrative.locality",elementType:"all",stylers:[{lightness:"0"},{color:nooJobGmapL10n.primary_color},{saturation:"9"},{visibility:"simplified"}]},{featureType:"landscape",elementType:"geometry",stylers:[{color:"#000000"},{lightness:20}]},{featureType:"poi",elementType:"geometry",stylers:[{color:"#000000"},{lightness:21}]},{featureType:"poi",elementType:"geometry.fill",stylers:[{color:"#3e3e3e"}]},{featureType:"road.highway",elementType:"geometry.fill",stylers:[{color:"#000000"},{lightness:17}]},{featureType:"road.highway",elementType:"geometry.stroke",stylers:[{color:nooJobGmapL10n.primary_color},{lightness:29},{weight:.2}]},{featureType:"road.arterial",elementType:"geometry",stylers:[{color:"#000000"},{lightness:18}]},{featureType:"road.local",elementType:"geometry",stylers:[{color:"#000000"},{lightness:16}]},{featureType:"transit",elementType:"geometry",stylers:[{color:"#000000"},{lightness:19}]},{featureType:"water",elementType:"all",stylers:[{visibility:"simplified"},{lightness:"-62"}]},{featureType:"water",elementType:"geometry",stylers:[{color:"#13232a"},{lightness:17}]}];
	var map_style_light=[{featureType:"administrative",elementType:"labels.text.fill",stylers:[{color:"#444444"}]},{featureType:"landscape",elementType:"all",stylers:[{color:"#f2f2f2"}]},{featureType:"poi",elementType:"all",stylers:[{visibility:"off"}]},{featureType:"poi.park",elementType:"all",stylers:[{visibility:"on"},{color:"#bcd9c3"}]},{featureType:"road",elementType:"all",stylers:[{saturation:-100},{lightness:45}]},{featureType:"road.highway",elementType:"all",stylers:[{visibility:"simplified"}]},{featureType:"road.arterial",elementType:"labels.icon",stylers:[{visibility:"off"}]},{featureType:"transit",elementType:"all",stylers:[{visibility:"off"}]},{featureType:"transit.station",elementType:"all",stylers:[{visibility:"off"},{weight:"0.28"}]},{featureType:"transit.station",elementType:"labels.text",stylers:[{color:"#555555"}]},{featureType:"transit.station",elementType:"labels.icon",stylers:[{saturation:"-66"}]},{featureType:"transit.station.rail",elementType:"all",stylers:[{visibility:"on"}]},{featureType:"water",elementType:"all",stylers:[{color:"#d5def2"},{visibility:"on"}]},{featureType:"water",elementType:"labels.text.fill",stylers:[{color:"#ffffff"}]},{featureType:"water",elementType:"labels.text.stroke",stylers:[{visibility:"on"}]},{featureType:"administrative.country",elementType:"geometry.stroke",stylers:[{visibility:"on"},{color:nooJobGmapL10n.primary_color}]},{featureType:"administrative.country",elementType:"labels.text",stylers:[{color:nooJobGmapL10n.primary_color},{visibility:"simplified"}]},{featureType:"administrative.province",elementType:"labels.text",stylers:[{color:nooJobGmapL10n.primary_color},{visibility:"off"}]},{featureType:"administrative.locality",elementType:"all",stylers:[{lightness:"0"},{color:nooJobGmapL10n.primary_color},{saturation:"9"},{visibility:"simplified"}]}];
	var map_style_apple=[{featureType:"landscape.man_made",elementType:"geometry",stylers:[{color:"#f7f1df"}]},{featureType:"landscape.natural",elementType:"geometry",stylers:[{color:"#d0e3b4"}]},{featureType:"landscape.natural.terrain",elementType:"geometry",stylers:[{visibility:"off"}]},{featureType:"poi",elementType:"labels",stylers:[{visibility:"off"}]},{featureType:"poi.business",elementType:"all",stylers:[{visibility:"off"}]},{featureType:"poi.medical",elementType:"geometry",stylers:[{color:"#fbd3da"}]},{featureType:"poi.park",elementType:"geometry",stylers:[{color:"#bde6ab"}]},{featureType:"road",elementType:"geometry.stroke",stylers:[{visibility:"off"}]},{featureType:"road",elementType:"labels",stylers:[{visibility:"off"}]},{featureType:"road.highway",elementType:"geometry.fill",stylers:[{color:"#ffe15f"}]},{featureType:"road.highway",elementType:"geometry.stroke",stylers:[{color:"#efd151"}]},{featureType:"road.arterial",elementType:"geometry.fill",stylers:[{color:"#ffffff"}]},{featureType:"road.local",elementType:"geometry.fill",stylers:[{color:"black"}]},{featureType:"transit.station.airport",elementType:"geometry.fill",stylers:[{color:"#cfb2db"}]},{featureType:"water",elementType:"geometry",stylers:[{color:"#a2daf2"}]}];
	var map_style_nature=[{featureType:"landscape",stylers:[{hue:"#FFA800"},{saturation:0},{lightness:0},{gamma:1}]},{featureType:"road.highway",stylers:[{hue:"#53FF00"},{saturation:-73},{lightness:40},{gamma:1}]},{featureType:"road.arterial",stylers:[{hue:"#FBFF00"},{saturation:0},{lightness:0},{gamma:1}]},{featureType:"road.local",stylers:[{hue:"#00FFFD"},{saturation:0},{lightness:30},{gamma:1}]},{featureType:"water",stylers:[{hue:"#00BFFF"},{saturation:6},{lightness:8},{gamma:1}]},{featureType:"poi",stylers:[{hue:"#679714"},{saturation:33.4},{lightness:-25.4},{gamma:1}]}];
	function noo_job_map_initialize(){
		var mapSearchBox = $('.noo-job-map');
		var mapBox = mapSearchBox.find('#gmap'),
			latitude = mapBox.attr('data-latitude') ? mapBox.attr('data-latitude') : nooJobGmapL10n.latitude,
			longitude = mapBox.attr('data-longitude') ? mapBox.attr('data-longitude') : nooJobGmapL10n.longitude,
			zoom = mapBox.attr('data-zoom') ? mapBox.attr('data-zoom') : nooJobGmapL10n.zoom,
			fit_bounds = mapBox.attr('data-fit_bounds') ? ( mapBox.attr('data-fit_bounds') == 'yes' ) : true;
		var myPlace    = new google.maps.LatLng( parseFloat( latitude ), parseFloat( longitude ) );
		var style = mapBox.attr('data-map_style') ? mapBox.attr('data-map_style') : '';
		var map_style = map_style_dark;
		switch (style) {
			case "none":
				map_style = [];
				break;
			case "light":
				map_style = map_style_light;
				break;
			case "apple":
				map_style = map_style_apple;
				break;
			case "nature":
				map_style = map_style_nature;
				break;
		}
		var myOptions = {
			    flat:false,
			    noClear:false,
			    zoom: parseInt(zoom),
			    scrollwheel: false,
			    streetViewControl:false,
			    disableDefaultUI: false,
			    scaleControl:false,
			    navigationControl:false,
			    mapTypeControl:false,
			    maxZoom: parseInt(zoom),
			    // draggable: !isTouch(),
			    center: myPlace,
			    mapTypeId: google.maps.MapTypeId.ROADMAP,
			    styles : map_style
		};
		var gmarkers = [],
			map = new google.maps.Map(mapBox.get(0),myOptions );
			google.maps.visualRefresh = true;
			
		google.maps.event.addListener(map, 'tilesloaded', function() {
			mapSearchBox.find('.gmap-loading').hide();
		});

		var input = new google.maps.places.Autocomplete($("#map-location-search")[0]);
		input.bindTo("bounds", map);
		google.maps.event.addListener(input, "place_changed", function() {
			var place = input.getPlace();
			if( place.geometry ) {
				if( place.geometry.viewport )
					map.fitBounds(place.geometry.viewport);
				else 
					map.setCenter(place.geometry.location);
			}
		});

		var infoboxOptions = {
                content: document.createElement("div"),
                disableAutoPan: true,
                maxWidth: 500,
                boxClass:"myinfobox",
                zIndex: null,			
                closeBoxMargin: "-13px 0px 0px 0px",
                closeBoxURL: "",
                infoBoxClearance: new google.maps.Size(1, 1),
                isHidden: false,
                pane: "floatPane",
                enableEventPropagation: false                   
        };               
		infoBox = new InfoBox(infoboxOptions);
		
		var clickMarkerListener = function(marker){
			var infoContent = '<div class="gmap-infobox"><a class="info-close" onclick="return infoBox.close();" href="javascript:void(0)">x</a>\
				<div class="loop-item-wrap"> \
					<div class="item-featured"><a href="' + marker.company_url + '"><img src="'+ marker.image +'"></a></div> \
					<div class="loop-item-content"> \
					 	<h4 class="loop-item-title"><a href="' + marker.url + '">' + marker.title + '</a></h4>';
					 	

			if( marker.company_url != '' || marker.type != '' ) {
				infoContent += '<p class="content-meta">';
					 		
				if( marker.company_url != '' ) {
					infoContent += '<span class="job-company"> <a href="' + marker.company_url + '">' + marker.company + '</a></span>';
				}
				if( marker.type != '' ) {
					infoContent += '<span class="job-type"> <a href="' + marker.type_url + '" style="color: ' + marker.type_color + '"><i class="fa fa-bookmark"></i>' + marker.type + '</a></span>';
				}

				infoContent += '</p>';
			}
			infoContent += '</div></div>';

			infoBox.setContent(infoContent);
			infoBox.open(map,marker);

			map.setCenter(marker.position); 
			map.panBy(50,-120);
		};
        var clickCompanyMarkerListener = function(company_marker){
            var infoContent = '<div class="gmap-infobox"><a class="info-close" onclick="return infoBox.close();" href="javascript:void(0)">x</a>\
				<div class="loop-item-wrap"> \
					<div class="item-featured"><a href="' + company_marker.company_url + '">' + company_marker.image + '</a></div> \
					<div class="loop-item-content"> \
					 	<h4 class="loop-item-title"><a href="' + company_marker.company_url + '">' + company_marker.title + '</a></h4>';
            if( company_marker.company_url !== '' ) {
                infoContent += '<p class="content-meta">';

                if( company_marker.slogan !== '' ) {
                    infoContent += '<span class="job-company"> ' + company_marker.slogan + '</span>';
                }
                if(company_marker.total_job > 0){
                	infoContent +='<strong> '+ nooJobGmapL10n.total_job + '</strong><span class="total-job">'+ company_marker.total_job +'</span> ';
				}
                infoContent += '</p>';
            }
            infoContent += '</div></div>';

            infoBox.setContent(infoContent);
            infoBox.open(map,company_marker);

            map.setCenter(company_marker.position);
            map.panBy(50,-120);
        };
		var company_markers =$.parseJSON(nooJobGmapL10n.marker_company_data);
			if(company_markers.length){
                var b = new google.maps.LatLngBounds();
                for(var i = 0; i <company_markers.length ; i ++){
                    var company_marker = company_markers[i];
                    var company_markerPlace = new google.maps.LatLng(company_marker.latitude,company_marker.longitude);
                    var company_gmarker = new google.maps.Marker({
                        position: company_markerPlace,
                        map: map,
						post:company_marker.post_type,
                        title:company_marker.title,
                        image:company_marker.image,
                        company: company_marker.company,
                        company_url:company_marker.company_url,
                        total_job:company_marker.total_job,
                        slogan  :company_marker.slogan,
                        icon: nooJobGmapL10n.theme_uri + '/assets/images/map-marker-icon.png'
                    });
                    gmarkers.push(company_gmarker);
                    b.extend(company_gmarker.getPosition() );
                    google.maps.event.addListener(company_gmarker, 'click', function(e) {
                        clickCompanyMarkerListener(this);
                    });
                }

                if( gmarkers.length > 0 && fit_bounds ) {
                    map.fitBounds(b);
                }
			}
		var markers = $.parseJSON(nooJobGmapL10n.marker_data);
		if(markers.length){
			var bounds = new google.maps.LatLngBounds();
			for(var i = 0; i < markers.length ; i ++){
				var marker = markers[i];
				var markerPlace = new google.maps.LatLng(marker.latitude,marker.longitude);
				var gmarker = new google.maps.Marker({
					position: markerPlace,
					map: map,
					post:marker.post_type,
					title: marker.title,
					url: marker.url,
					image: marker.image,
					type: marker.type,
					type_url: marker.type_url,
					type_color: marker.type_color,
					company: marker.company,
					company_url: marker.company_url,
					term_url: marker.term_url,
					icon: nooJobGmapL10n.theme_uri + '/assets/images/map-marker.png'
				});
				gmarkers.push(gmarker);
				bounds.extend( gmarker.getPosition() );
				google.maps.event.addListener(gmarker, 'click', function(e) {
					clickMarkerListener(this);
				});
			}

			if( gmarkers.length > 0 && fit_bounds ) {
				map.fitBounds(bounds);
			}
		 }
		
		var clusterStyles = [{
				textColor: '#ffffff',    
				opt_textColor: '#ffffff',
				url: nooJobGmapL10n.theme_uri + '/assets/images/cloud.png',
				height: 62,
				width: 60,
				textSize:14
			}
		];
		var mcluster = new MarkerClusterer(map, gmarkers,{
			gridSize: 50,
			ignoreHidden:true, 
			styles: clusterStyles
		});
		mcluster.setIgnoreHidden(true);
        var search_filter_map = function () {
            mcluster.removeMarkers(gmarkers);
            gmarkers = []; gmarkers.length = 0;
            var container = $(".noo-main > .jobs");
            var bounds_filter = new google.maps.LatLngBounds();
            container.find("article").each(function(e){
                if($(this).data('marker') != false){
                    marker = $(this).attr('data-marker');
                    marker =JSON.parse(marker);
                    var markerPlace = new google.maps.LatLng(marker.latitude, marker.longitude);
                    gmarker = new google.maps.Marker({
                        position: markerPlace,
                        map: map,
                        post:marker.post_type,
                        title: marker.title,
                        url: marker.url,
                        image: marker.image,
                        type: marker.type,
                        type_url: marker.type_url,
                        type_color: marker.type_color,
                        company: marker.company,
                        company_url: marker.company_url,
                        icon: nooJobGmapL10n.theme_uri + '/assets/images/map-marker.png'
                    });
                    gmarkers.push(gmarker);

                    bounds_filter.extend( gmarker.getPosition() );
                    google.maps.event.addListener(gmarker, 'click', function(e) {
                        clickMarkerListener(this);
                    });
                }
            });
            map.setZoom(10);
            if(gmarkers.length > 0 && fit_bounds ) {
                map.fitBounds(bounds_filter);
            }
            mcluster = new MarkerClusterer(map, gmarkers,{
                maxZoom:20,
                gridSize: 50,
                ignoreHidden:true,
                styles: clusterStyles
            });
            mcluster.setIgnoreHidden(true);
        }
        var job_ajax = function() {
            var container = $(".noo-main > .jobs");
            var map = $('.map-info');
            if (container.length && map.length) {
                var id = $(".widget-advanced-search").attr('id');
                $('#' + id).on('change','select, input:not([type="checkbox"]):not([type="radio"]):not(.noo-mb-lat-filter):not(.noo-mb-lon-filter):not(.noo-mb-location-address-filter):not(.noo-mb-location-address)', function (event) {
                    // event.preventDefault();

                    /* Filter and search canvas on Mobile */
                    $(this).closest('.widget_noo_advanced_job_search_widget').removeClass('on-filter');
                    /* End*/

                    $('.noo-main').addClass('noo-loading').append('<div class="noo-loader loadmore-loading"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>');
                    $('.page-archive-job').animate({ scrollTop: 0 }, 500);
                    var $form = $('#' + id + " .form-control");
                    var data = $(this).parents('form').serialize();
                    window.history.pushState(null, null, "?" + $form.serialize());
                    $.ajax({
                        url: nooJobGmapL10n.ajax_url ,
                        data: data
                    })
                        .done(function (data) {
                            if (data !== "-1") {
                                var $newElems = $(data).css({
                                    opacity: 0
                                });
                                $(".noo-main").html($newElems);
                                $newElems.animate({
                                    opacity: 1
                                },1000);
                                $('.noo-main').removeClass('noo-loading');

                                if ($('[data-paginate="loadmore"]').find(".loadmore-action").length) {
                                    $('[data-paginate="loadmore"]').each(function () {
                                        var $this = $(this);
                                        var maxPage = $this.find('.btn-loadmore').data('maxpage');
                                        var itemSelector = 'article.loadmore-item';
                                        if($this.find('.noo-grid').length){
                                            itemSelector = 'div.loadmore-item';
                                        }
                                        $this.nooLoadmore({
                                            navSelector: $this.find("div.pagination"),
                                            nextSelector: $this.find("div.pagination a.next"),
                                            itemSelector: itemSelector,
                                            maxPage: maxPage,
                                            finishedMsg: " All jobs displayed"
                                        });
                                        $(".btn-loadmore").on('click', function (e) {
                                            e.preventDefault();
                                            $( document ).ajaxComplete(function() {
                                                search_filter_map();
                                            });
                                        })

                                    });
                                }
                                search_filter_map();
                            } else {
                                location.reload();
                            }
                        })
                        .fail(function () {

                        })
                });
            }
        }
        job_ajax();
        var jobLiveFilter = function(){

            var form_id = $('.widget-csf-live-filter').attr('id');
            var is_tax = $('#' + form_id).data('is-tax');
            if(!is_tax){
                $('#' + form_id + ' .reset-search').on('click',function (e) {
                    e.preventDefault();

                    var $form = $(this).parent('form');
                    $form.find('option:selected').removeAttr('selected');

                    $form.get(0).reset(); /* Reset form value*/

                    /* Get any input tag and run the function change() --> callback ajax*/
                    $form.find('.form-group').first().find(':input').not(':button, :submit, :reset, :hidden').change();

                    $('select.form-control', $form).multiselect('refresh');

                    return false;
                });
            }
            /* Ajax Live Search */
            var container = $(".noo-main > .jobs");
            var off_livesearch = $('#' + form_id).data('off-livesearch');

            if($('.map-info').length && container.length > 0 && !off_livesearch){
                $('#' + form_id).on('change','select,input:not(.filter-search-option):not(.noo-mb-lat-filter):not(.noo-mb-lon-filter):not(.noo-mb-location-address-filter):not(.noo-mb-location-address)', function (event) {
                    event.preventDefault();
                    // Filter button when run on Mobile
                    $(this).closest('.widget_noo_advanced_job_search_widget').removeClass('on-filter');

                    $('.noo-main').addClass('noo-loading').append('<div class="noo-loader loadmore-loading"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>');
                    $('html,body').animate({ scrollTop: $('.noo-main').offset().top -200}, 500);
                    var $form = $('#' + form_id + ' .form-control');
                    var data = $(this).parents('form').serialize();
                    history.pushState(null, null, "?" + $form.serialize());
                    $.ajax({
                        url: nooJobGmapL10n.ajax_url,
                        data: data
                    })
                        .done(function (data) {
                            if (data !== "-1") {
                                var $newElems = $(data).find(".filter-post-content").html()
                                var $newSidebar = $(data).find('.filter-sidebar').contents();
                                var $resultsFilter = $(data).find('.filter-selected').contents();
                                $(".noo-main").html($newElems);
                                $('.noo-main').removeClass('noo-loading');
                                $('.widget-fields-live-filter').html($newSidebar);
                                $('.results-filter').html($resultsFilter);
                                if ($('[data-paginate="loadmore"]').find(".loadmore-action").length) {
                                    $('[data-paginate="loadmore"]').each(function () {
                                        var $this = $(this);
                                        var maxPage = $this.find('.btn-loadmore').data('maxpage');
                                        $this.nooLoadmore({
                                            navSelector: $this.find("div.pagination"),
                                            nextSelector: $this.find("div.pagination a.next"),
                                            itemSelector: "article.loadmore-item",
                                            maxPage: maxPage,
                                            finishedMsg: " All jobs displayed"
                                        });
                                        $(".btn-loadmore").on('click', function (e) {
                                            e.preventDefault();
                                            $( document ).ajaxComplete(function() {
                                                search_filter_map();
                                            });
                                        })
                                    });
                                }
                                search_filter_map();
                                FilterFunc.jobQuickSearch();
                                FilterFunc.NooDatetimepicker();
                                FilterFunc.hideClearAllFilter();
                                FilterFunc.collaspe_expand_field_filter();
                                FilterFunc.GetGeoLocation();
                                FilterFunc.ProximityRange();
                                if($('.noo-mb-job-location-filter').length >=1){
                                    FilterFunc.noo_mb_map_field_filter();
                                }else if($('.noo-mb-job').length >=1){
                                    JM_Bing_Map();
                                }
                            } else {
                                location.reload();
                            }
                        })
                        .fail(function () {

                        })
                });
            }
            $('#' + form_id).submit(function () {
                $(this).find("input[name='action']").remove();
                $(this).find("input[name='_wp_http_referer']").remove();
                $(this).find("input[name='live-filter-nonce']").remove();

                return true;
            });
        }
        jobLiveFilter();
        var loadmore = function(){
            $(".btn-loadmore").on('click', function (e) {
                var $this = $(this);
                var max_page = $this.find('.btn-loadmore').data('maxpage');
                var itemSelector = 'article.loadmore-item';
                if($this.find('.noo-grid').length){
                    itemSelector = 'div.loadmore-item';
                }
                $this.nooLoadmore({
                    navSelector: $this.find('div.pagination'),
                    nextSelector: $this.find('div.pagination a.next'),
                    itemSelector: itemSelector,
                    maxPage: max_page,
                    finishedMsg: " All jobs displayed"
                });
                $( document ).ajaxComplete(function() {
                    search_filter_map();
                });
            })
        }
        loadmore();
	 }
	google.maps.event.addDomListener(window, 'load', noo_job_map_initialize);
})(jQuery);