function JM_Bing_Map(){
	
	var jm_location = jQuery('.job-map');
	if (jm_location.length > 0) {
		jm_location.each(function(index, el){
			var form_map   = jQuery(this),
				id_map     = form_map.find('.bmap').data('id'),
				zoom       = parseInt(form_map.find('.bmap').data('zoom')),
				lat        = parseFloat(form_map.find('.bmap').data('latitude')) ,
				lng        = parseFloat(form_map.find('.bmap').data('longitude')) ;
			var map = new Microsoft.Maps.Map(document.getElementById(id_map), {
	        	/* No need to set credentials if already passed in URL */
	            center: new Microsoft.Maps.Location(lat, lng),
	            zoom:zoom,
	            disableScrollWheelZoom: true,
	        });

            form_map.find('.gmap-loading').hide();

            var markers = JSON.parse(nooJobGmapL10n.marker_data);

            var company_markers = JSON.parse(nooJobGmapL10n.marker_company_data);

            var dataLayer = new Microsoft.Maps.EntityCollection();
			map.entities.push(dataLayer);

			var infoboxLayer = new Microsoft.Maps.EntityCollection();
            map.entities.push(infoboxLayer);

            infobox = new Microsoft.Maps.Infobox(new Microsoft.Maps.Location(0, 0), { visible: false, offset: new Microsoft.Maps.Point(0, 20) });
            infoboxLayer.push(infobox);

            // get_list_company_markers(map ,company_markers );

            get_list_markers(map, markers,company_markers );


            function get_list_markers(map, markers ,company_markers){
                Microsoft.Maps.loadModule("Microsoft.Maps.Clustering", function () {
                    var pins = [];
                    var locs =[];
                    var icon_company = nooJobGmapL10n.theme_uri + '/assets/images/map-marker-icon.png'
                    var icon = nooJobGmapL10n.theme_uri + '/assets/images/map-marker.png';
                    for (var i = 0; i < company_markers.length; i++ ) {
                        if ((company_markers[i].latitude === '') && (company_markers[i].longitude === '')) {
                            continue;
                        };
                        var pin_company = new Microsoft.Maps.Pushpin(new Microsoft.Maps.Location(company_markers[i].latitude, company_markers[i].longitude),{icon:icon_company});
                        pin_company.Title = '<h6><a href="' + company_markers[i].company_url + '">' + company_markers[i].title + '</a></h6>';
                        pin_company.Description = '<div class="">\
							<div class="loop-item-wrap"> \
								<div class="item-featured"><a href="' + company_markers[i].company_url + '">' + company_markers[i].image + '</a></div> \
								<div class="loop-item-content"> \
								 	<h5 class="loop-item-title"><a href="' + company_markers[i].company_url + '">' + company_markers[i].title + '</a></h5>\
								 	<p class="content-meta">\
									<span class="job-company"> ' + company_markers[i].slogan + '</span>\
									<strong> '+ nooJobGmapL10n.total_job +' </strong><span class="total-job">'+ company_markers[i].total_job +'</span> \
									</p>\
							</div></div>';
                        Microsoft.Maps.Events.addHandler(pin_company, 'click', displayInfobox);
                        Microsoft.Maps.Events.addHandler(pin_company, 'click',  function (args) {
                            return map.setView({
                                center: args.target.getLocation(),
                                zoom: 15
                            });
                        } );
                        pin_company.metadata = company_markers[i];
                        pins.push(pin_company);
                    }
                    for (var j = 0; j < markers.length; j++ ) {
                        if ((markers[j].latitude === '') && (markers[j].longitude === '')) {
                            continue;
                        };

                        var pin = new Microsoft.Maps.Pushpin(new Microsoft.Maps.Location(markers[j].latitude, markers[j].longitude),{icon:icon});
                        pin.Title = '<h6><a href="' + markers[j].url + '">' + markers[j].title + '</a></h6>';
                        pin.Description = '<div>\
							<div class="loop-item-wrap"> \
								<div class="item-featured"><a href="' + markers[j].url + '"><img src="' + markers[j].image + '"></a></div> \
								<div class="loop-item-content"> \
								 	<h5 class="loop-item-title"><a href="' + markers[j].url + '">' + markers[j].title + '</a></h5>\
								 	<p class="content-meta">';
                        if (markers[j].company_url !== ''  || markers[j].company !== '') {
                            pin.Description += '<span class="job-company"> <a href="' + markers[j].company_url + '">' + markers[j].company + '</a></span>';
                        }
                        if (markers[j].type !== "") {
                            pin.Description += '<span class="job-type"> <a href="' + markers[j].type_url + '" style="color: ' + markers[j].type_color + '"><i class="fa fa-bookmark"></i>' + markers[j].type + '</a></span> ';
                        }
                        pin.Description +='</p>\
							</div></div>';
                        Microsoft.Maps.Events.addHandler(pin, 'click', displayInfobox);
                        Microsoft.Maps.Events.addHandler(pin, 'click',  function (args) {
                            return map.setView({
                                center: args.target.getLocation(),
                                zoom: 15
                            });
                        });
                        pin.metadata = markers[j];
                        pins.push(pin);
                        locs.push(new Microsoft.Maps.Location(markers[j].latitude, markers[j].longitude));
                    }
                    clusterLayer = new Microsoft.Maps.ClusterLayer(pins, {
                        clusteredPinCallback: customizeClusteredPin
                    });
                    viewBoundaries = Microsoft.Maps.LocationRect.fromLocations(locs);
                    if(locs.length === 1){
                        map.setView({center:locs[0],zoom:zoom});
                    }else{
                        map.setView({bounds: viewBoundaries });
                    }
                    map.layers.insert(clusterLayer);

                });
            }
            var search_filter_map = function () {

                markers=[] ;markers.length = 0;
                company_markers=[];
                for (var i = map.entities.getLength() - 1; i >= 0; i--) {
                    var pushpin = map.entities.get(i);
                    if (pushpin instanceof Microsoft.Maps.Pushpin){
                        map.entities.removeAt(i);
                    }
                }
                clusterLayer.clear();
                var container = jQuery(".noo-main > .jobs");
                container.find("article").each(function(e){
                    if(jQuery(this).data('marker') != false){
                        var  marker = jQuery(this).attr('data-marker');
                        marker =JSON.parse(marker);
                        markers.push(marker)
                    }
                });
                if(markers.length ){
                    get_list_markers(map, markers, company_markers);
                }


            };
			function displayInfobox(e) {
			  if (e.targetType == 'pushpin') {
			      infobox.setLocation(e.target.getLocation());
			      infobox.setOptions({ visible: true, description: e.target.Description , maxWidth:350, maxHeight:220 });
			  }
			}

			function customizeClusteredPin(cluster) {
				var minRadius = 25 ;

				var url = nooJobGmapL10n.theme_uri + '/assets/images/cloud.png';
				var clusterSize = cluster.containedPushpins.length;
				var radius = Math.log(clusterSize) / Math.log(10) * 5 + minRadius;
				cluster.setOptions({
			        icon: url,
			        textOffset: new Microsoft.Maps.Point(0, radius -5)
			    });
			    // Add click event to clustered pushpin
			    Microsoft.Maps.Events.addHandler(cluster, 'click', pushpinClicked);
			}

			function pushpinClicked(e) {
			    //Show an infobox when a pushpin is clicked.
			    if (e.target.containedPushpins) {
			    var locs = [];
			    for (var i = 0, len = e.target.containedPushpins.length; i < len; i++) {
			        //Get the location of each pushpin.
			        locs.push(e.target.containedPushpins[i].getLocation());
			    }
			        //Create a bounding box for the pushpins.
			        var bounds = Microsoft.Maps.LocationRect.fromLocations(locs);
			        //Zoom into the bounding box of the cluster. 
			        //Add a padding to compensate for the pixel area of the pushpins.
			        map.setView({ bounds: bounds, padding: 100 });
			        if (bounds.width == 0) {
			        	showInfobox(e.target);
			        }
			    }			    
			}

			function showInfobox(pin) {
			    var description = [];

			    //Check to see if the pushpin is a cluster.
			    if (pin.containedPushpins) {
			        //Create a list of all pushpins that are in the cluster.
			        description.push('<div style="overflow-y:auto;"><ul class="bmap-listCluster">');
			        for (var i = 0; i < pin.containedPushpins.length; i++) {
			        	if (pin.containedPushpins[i].metadata.post_type === 'company') {
			        		description.push('<li><h6><i class="fa fa-suitcase" aria-hidden="true"></i><a href="'+pin.containedPushpins[i].metadata.company_url+'">', pin.containedPushpins[i].metadata.company, '</a></h6><div><a href="'+pin.containedPushpins[i].metadata.company_url+'">'+pin.containedPushpins[i].metadata.image+'</a><span>'+ nooJobGmapL10n.total_job +' '+pin.containedPushpins[i].metadata.total_job+'</span></div></li>');
			        	}
                        if (pin.containedPushpins[i].metadata.post_type === 'job') {
                            description.push('<li><h6><i class="fa fa-black-tie" aria-hidden="true"></i><a href="'+pin.containedPushpins[i].metadata.url+'">', pin.containedPushpins[i].metadata.title, '</a></h6><div><a href="'+pin.containedPushpins[i].metadata.company_url+'"><img src="'+pin.containedPushpins[i].metadata.image+'">'+pin.containedPushpins[i].metadata.company+'</a><span class="job-type" ><a href="'+pin.containedPushpins[i].metadata.type_url+'"style="color:'+pin.containedPushpins[i].metadata.type_color+'"><i class="fa fa-bookmark"></i>'+pin.containedPushpins[i].metadata.type+'</a></span></div></li>');
                        }
			        }
			        description.push('</ul></div>');
			    }

			    //Display an infobox for the pushpin.
			    infobox.setOptions({
			        title: pin.getTitle(),
			        location: pin.getLocation(),
			        description: description.join(''),
			        visible: true,
			        maxWidth:320, 
			        maxHeight:280
			    });
			}
            var job_ajax = function() {
                var container = jQuery(".noo-main > .jobs");
                if (container.length) {
                    var id = jQuery(".widget-advanced-search").attr('id');
                    jQuery('#' + id).on('change','select, input:not([type="checkbox"]):not([type="radio"]):not(.noo-mb-lat-filter):not(.noo-mb-lon-filter):not(.noo-mb-location-address-filter):not(.noo-mb-location-address)', function (event) {
                        event.preventDefault();
                        /*Filter button when run on Mobile*/
                        jQuery(this).closest('.widget_noo_advanced_job_search_widget').removeClass('on-filter');
                        /* End */
                        jQuery('.noo-main').addClass('noo-loading').append('<div class="noo-loader loadmore-loading"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>');
                        jQuery('html,body').animate({ scrollTop: 0 }, 500);

                        var $form = jQuery('#' + id + " .form-control");
                        var data = jQuery(this).parents('form').serialize();
                        window.history.pushState(null, null, "?" + $form.serialize());
                        jQuery.ajax({
                            url: nooJobGmapL10n.ajax_url ,
                            data: data,
                        })
                            .done(function (data) {
                                if (data !== "-1") {
                                    /*Filter and Search canvas on Mobile*/

                                    jQuery(data).on('click','.mobile-job-filter', function(){
                                        var filter_class = jQuery(this).closest('.noo-main').next().find('div[id^="noo_advanced_"]');
                                        if(filter_class.hasClass('on-filter')){
                                            filter_class.removeClass('on-filter');
                                            filter_class.find(".close-mobile-job-filter").empty();
                                            filter_class.find(".close-mobile-job-filter").remove();
                                        }else{
                                            filter_class.addClass('on-filter');
                                            jQuery(this).html('<i class="fas fa-times" aria-hidden="true"></i> ' + nooJobL10n.close);
                                            filter_class.append('<span class="close-mobile-job-filter" style="float: right"><i class="fas fa-times" aria-hidden="true"></i> ' + nooJobL10n.close +'</span>');
                                        }
                                    });
                                    jQuery(data).on('click','.close-mobile-job-filter', function(){
                                        var filter_close = jQuery(this).closest('div[id^="noo_advanced_"]');
                                        var mobile_filter_class = jQuery(this).closest('.main-content').find('.mobile-job-filter');
                                        if(filter_close.hasClass('on-filter')){
                                            filter_close.removeClass('on-filter');
                                            mobile_filter_class.html(filter_text);
                                        }
                                        jQuery(this).empty();
                                        jQuery(this).remove();
                                    });
                                    /* End filter and search*/
                                    var $newElems = jQuery(data).css({
                                        opacity: 0
                                    });
                                    jQuery(".noo-main").html(data);
                                    $newElems.animate({
                                        opacity: 1
                                    },1000);
                                    jQuery('.noo-main').removeClass('noo-loading');

                                    if (jQuery('[data-paginate="loadmore"]').find(".loadmore-action").length) {
                                        jQuery('[data-paginate="loadmore"]').each(function () {
                                            var $this = jQuery(this);
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
                                            jQuery(".btn-loadmore").on('click', function (e) {
                                                e.preventDefault();
                                                jQuery( document ).ajaxComplete(function() {
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
            };
            job_ajax();
            var jobLiveFilter = function(){

                var form_id = jQuery('.widget-csf-live-filter').attr('id');
                var is_tax = jQuery('#' + form_id).data('is-tax');
                if(!is_tax){
                    jQuery('#' + form_id + ' .reset-search').on('click',function (e) {
                        e.preventDefault();

                        var $form = jQuery(this).parent('form');
                        $form.find('option:selected').removeAttr('selected');

                        $form.get(0).reset(); /* Reset form value*/

                        /* Get any input tag and run the function change() --> callback ajax*/
                        $form.find('.form-group').first().find(':input').not(':button, :submit, :reset, :hidden').change();

                        $('select.form-control',$form).multiselect('refresh');

                        return false;
                    });
                }
                /* Ajax Live Search */
                var container = jQuery(".noo-main > .jobs");
                var off_livesearch = jQuery('#' + form_id).data('off-livesearch');

                if(jQuery('.map-info').length && container.length > 0 && !off_livesearch){
                    jQuery('#' + form_id).on('change','select,input:not(.filter-search-option):not(.noo-mb-lat-filter):not(.noo-mb-lon-filter):not(.noo-mb-location-address-filter):not(.noo-mb-location-address)', function (event) {
                        event.preventDefault();
                        // Filter button when run on Mobile
                        jQuery(this).closest('.widget_noo_advanced_job_search_widget').removeClass('on-filter');

                        jQuery('.noo-main').addClass('noo-loading').append('<div class="noo-loader loadmore-loading"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>');
                        jQuery('html,body').animate({ scrollTop: jQuery('.noo-main').offset().top -200}, 500);
                        var $form = jQuery('#' + form_id + ' .form-control');
                        var data = jQuery(this).parents('form').serialize();
                        history.pushState(null, null, "?" + $form.serialize());
                        jQuery.ajax({
                            url: nooJobGmapL10n.ajax_url,
                            data: data
                        })
                            .done(function (data) {
                                if (data !== "-1") {
                                    var $newElems = jQuery(data).find(".filter-post-content").html()
                                    var $newSidebar = jQuery(data).find('.filter-sidebar').contents();
                                    var $resultsFilter = jQuery(data).find('.filter-selected').contents();
                                    jQuery(".noo-main").html($newElems);
                                    jQuery('.noo-main').removeClass('noo-loading');
                                    jQuery('.widget-fields-live-filter').html($newSidebar);
                                    jQuery('.results-filter').html($resultsFilter);
                                    if (jQuery('[data-paginate="loadmore"]').find(".loadmore-action").length) {
                                        jQuery('[data-paginate="loadmore"]').each(function () {
                                            var $this = jQuery(this);
                                            var maxPage = $this.find('.btn-loadmore').data('maxpage');
                                            $this.nooLoadmore({
                                                navSelector: $this.find("div.pagination"),
                                                nextSelector: $this.find("div.pagination a.next"),
                                                itemSelector: "article.loadmore-item",
                                                maxPage: maxPage,
                                                finishedMsg: " All jobs displayed"
                                            });
                                            jQuery(".btn-loadmore").on('click', function (e) {
                                                e.preventDefault();
                                                jQuery( document ).ajaxComplete(function() {
                                                    search_filter_map();
                                                });
                                            })
                                        });
                                    }
                                    search_filter_map();
                                    picker_bing_map();
                                    FilterFunc.jobQuickSearch();
                                    FilterFunc.NooDatetimepicker();
                                    FilterFunc.hideClearAllFilter();
                                    FilterFunc.collaspe_expand_field_filter();
                                    FilterFunc.ProximityRange();
                                } else {
                                    location.reload();
                                }
                            })
                            .fail(function () {

                            })
                    });
                }
                jQuery('#' + form_id).submit(function () {
                    jQuery(this).find("input[name='action']").remove();
                    jQuery(this).find("input[name='_wp_http_referer']").remove();
                    jQuery(this).find("input[name='live-filter-nonce']").remove();

                    return true;
                });
            }
            jobLiveFilter();
            var loadmore = function(){
                jQuery(".btn-loadmore").on('click', function (e) {
                    var $this = jQuery(this);
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
                    jQuery( document ).ajaxComplete(function() {
                        search_filter_map();
                    });
                })
            }
            loadmore();

		});
	}

    var jm_resume_location = jQuery('.resume-map');
    if (jm_resume_location.length > 0) {
        jm_resume_location.each(function(index, el){
            var form_map   = jQuery(this),
                id_map     = form_map.find('.resume_bmap').data('id'),
                zoom       = parseInt(form_map.find('.resume_bmap').data('zoom')),
                lat        = parseFloat(form_map.find('.resume_bmap').data('latitude')) ,
                lng        = parseFloat(form_map.find('.resume_bmap').data('longitude')) ;
            var map = new Microsoft.Maps.Map(document.getElementById(id_map), {
                /* No need to set credentials if already passed in URL */
                supportedMapTypes: [Microsoft.Maps.MapTypeId.road, Microsoft.Maps.MapTypeId.aerial, Microsoft.Maps.MapTypeId.grayscale, Microsoft.Maps.MapTypeId.canvasLight,Microsoft.Maps.MapTypeId.canvasDark,Microsoft.Maps.MapTypeId.birdseye ] ,
                center: new Microsoft.Maps.Location(lat, lng),
                zoom:zoom,
                disableScrollWheelZoom: true,
                mapTypeId: Microsoft.Maps.MapTypeId.grayscale,
            });

            form_map.find('.gmap-loading').hide();

            var markers = JSON.parse(nooResumeMap.marker_data);

            var dataLayer = new Microsoft.Maps.EntityCollection();
            map.entities.push(dataLayer);

            var infoboxLayer = new Microsoft.Maps.EntityCollection();
            map.entities.push(infoboxLayer);

            infobox = new Microsoft.Maps.Infobox(new Microsoft.Maps.Location(0, 0), { visible: false, offset: new Microsoft.Maps.Point(0, 20) });
            infoboxLayer.push(infobox);

            get_list_markers(map, markers);


            function get_list_markers(map, markers){
                Microsoft.Maps.loadModule("Microsoft.Maps.Clustering", function () {
                    var pins = [];
                    var locs =[];
                    for (var j = 0; j < markers.length; j++ ) {
                        if ((markers[j]['latitude']=='') && (markers[j]['longitude']=='')) {
                            continue;
                        };
                        var icon = markers[j].icon;
                        createScaledPushpin(new Microsoft.Maps.Location(markers[j].latitude, markers[j].longitude),icon, 0.45,markers[j], function (pin) {
                            pins.push(pin);
                        });
                        locs.push(new Microsoft.Maps.Location(markers[j].latitude, markers[j].longitude));
                    }
                    clusterLayer = new Microsoft.Maps.ClusterLayer(pins, {
                        clusteredPinCallback: customizeClusteredPin
                    });
                    map.layers.insert(clusterLayer);
                    viewBoundaries = Microsoft.Maps.LocationRect.fromLocations(locs);
                    if(locs.length === 1){
                        map.setView({center:locs[0],zoom:15});
                    }else{
                        map.setView({bounds: viewBoundaries });
                    }


                });
            }
            function createScaledPushpin(location, imgUrl, scale,markers, callback) {
                var img = new Image();
                img.onload = function () {
                    var c = document.createElement('canvas');
                    c.width = img.width * scale;
                    c.height = img.height * scale;

                    var context = c.getContext('2d');

                    //Draw scaled image
                    context.drawImage(img, 0, 0, c.width, c.height);

                    var pin = new Microsoft.Maps.Pushpin(location, {
                        //Generate a base64 image URL from the canvas.
                        icon: c.toDataURL(),

                        //Anchor based on the center of the image.
                        anchor: new Microsoft.Maps.Point(c.width/2, c.height)
                    });
                    pin.Title = '<h6><a href="' + markers.url + '">' + markers.title + '</a></h6>';
                    pin.Description = '<div>\
							<div class="loop-item-wrap" style=""> \
								<div class="item-featured"><a href="' + markers.url + '">' + markers.image + '</a></div> \
								<div class="loop-item-content item-meta"> \
								    <h5 class="loop-item-author">'+ markers.candidate_name +'</h5>\
								 	<h6 class="loop-item-title "><a class="" href="' + markers.url + '">' + markers.title + '</a></h6>\
								 	<p class="content-meta item-meta-value">'
                    if(markers.category !== ''){
                        pin.Description += markers.category;
                    }
                    pin.Description +='</p>\
							</div></div>';
                    Microsoft.Maps.Events.addHandler(pin, 'click', displayInfobox);
                    Microsoft.Maps.Events.addHandler(pin, 'click',  function (args) {
                        return map.setView({
                            center: args.target.getLocation(),
                            zoom: 15
                        });
                    } );
                    pin.metadata = markers;
                    if (callback) {
                        callback(pin);
                    }
                };

                img.src = imgUrl;
            }
            var search_filter_map = function () {

                markers=[] ;markers.length = 0;
                for (var i = map.entities.getLength() - 1; i >= 0; i--) {
                    var pushpin = map.entities.get(i);
                    if (pushpin instanceof Microsoft.Maps.Pushpin){
                        map.entities.removeAt(i);
                    }
                }
                clusterLayer.clear();
                var container = jQuery(".noo-main > .resumes");
                container.find(".noo-resume-item").each(function(e){
                    if(jQuery(this).data('marker') != false){
                        var  marker = jQuery(this).attr('data-marker');
                        marker =JSON.parse(marker);
                        markers.push(marker)
                    }
                });
                if(markers.length ){
                    get_list_markers(map, markers);
                }


            };
            function displayInfobox(e) {
                if (e.targetType == 'pushpin') {
                    infobox.setLocation(e.target.getLocation());
                    infobox.setOptions({ visible: true, description: e.target.Description , maxWidth:350, maxHeight:350 });
                }
            }

            function customizeClusteredPin(cluster) {
                var minRadius = 25 ;

                var url = nooResumeMap.theme_uri + '/assets/images/cloud.png';
                var clusterSize = cluster.containedPushpins.length;
                var radius = Math.log(clusterSize) / Math.log(10) * 5 + minRadius;
                cluster.setOptions({
                    icon: url,
                    textOffset: new Microsoft.Maps.Point(0, radius -5)
                });
                // Add click event to clustered pushpin
                Microsoft.Maps.Events.addHandler(cluster, 'click', pushpinClicked);
            }

            function pushpinClicked(e) {
                //Show an infobox when a pushpin is clicked.
                if (e.target.containedPushpins) {
                    var locs = [];
                    for (var i = 0, len = e.target.containedPushpins.length; i < len; i++) {
                        //Get the location of each pushpin.
                        locs.push(e.target.containedPushpins[i].getLocation());
                    }
                    //Create a bounding box for the pushpins.
                    var bounds = Microsoft.Maps.LocationRect.fromLocations(locs);
                    //Zoom into the bounding box of the cluster.
                    //Add a padding to compensate for the pixel area of the pushpins.
                    map.setView({ bounds: bounds, padding: 100 });
                    if (bounds.width == 0) {
                        showInfobox(e.target);
                    }
                }
            }

            function showInfobox(pin) {
                var description = [];

                //Check to see if the pushpin is a cluster.
                if (pin.containedPushpins) {
                    //Create a list of all pushpins that are in the cluster.
                    description.push('<div style="overflow-y:auto;"><ul class="bmap-listCluster">');
                    for (var i = 0; i < pin.containedPushpins.length; i++) {
                        if (pin.containedPushpins[i].metadata.post_type == 'resume') {
                            description.push('<li class="item-meta" style="background:url(' + pin.containedPushpins[i].metadata.cover_image + ') "><h6><i class="fa fa-black-tie" aria-hidden="true"></i><a href="'+pin.containedPushpins[i].metadata.url+'" class="c-white">', pin.containedPushpins[i].metadata.title, '</a></h6><div class="item-meta-value"><a href="'+pin.containedPushpins[i].metadata.url+'">'+pin.containedPushpins[i].metadata.image+''+pin.containedPushpins[i].metadata.category +'</a></div></li>');
                        }
                    }
                    description.push('</ul></div>');
                }

                //Display an infobox for the pushpin.
                infobox.setOptions({
                    title: pin.getTitle(),
                    location: pin.getLocation(),
                    description: description.join(''),
                    visible: true,
                    maxWidth:500,
                    maxHeight:500
                });
            }
            var resume_ajax = function() {
                var container = jQuery(".noo-main > .resumes");
                if (container.length) {
                    var id = jQuery(".widget-advanced-search").attr('id');
                    jQuery('#' + id).on('change','select, input:not([type="checkbox"]):not([type="radio"]:not(.noo-mb-lat-filter):not(.noo-mb-lon-filter):not(.noo-mb-location-address-filter):not(.noo-mb-location-address))', function (event) {
                        event.preventDefault();
                        /*Filter button when run on Mobile*/
                        jQuery(this).closest('.widget_noo_advanced_resume_search_widget').removeClass('on-filter');
                        /* End */
                        var $form = jQuery('#' + id + " .form-control");
                        var data = jQuery(this).parents('form').serialize();
                        window.history.pushState(null, null, "?" + $form.serialize());
                        jQuery.ajax({
                            url: nooResumeMap.ajax_url ,
                            data: data,
                        })
                            .done(function (data) {
                                if (data !== "-1") {
                                    /*Filter and Search canvas on Mobile*/

                                    jQuery(data).on('click','.mobile-job-filter', function(){
                                        var filter_class = jQuery(this).closest('.noo-main').next().find('div[id^="noo_advanced_"]');
                                        if(filter_class.hasClass('on-filter')){
                                            filter_class.removeClass('on-filter');
                                            filter_class.find(".close-mobile-job-filter").empty();
                                            filter_class.find(".close-mobile-job-filter").remove();
                                        }else{
                                            filter_class.addClass('on-filter');
                                            jQuery(this).html('<i class="fas fa-times" aria-hidden="true"></i> ' + nooJobL10n.close);
                                            filter_class.append('<span class="close-mobile-job-filter" style="float: right"><i class="fas fa-times" aria-hidden="true"></i> ' + nooJobL10n.close +'</span>');
                                        }
                                    });
                                    jQuery(data).on('click','.close-mobile-job-filter', function(){
                                        var filter_close = jQuery(this).closest('div[id^="noo_advanced_"]');
                                        var mobile_filter_class = jQuery(this).closest('.main-content').find('.mobile-job-filter');
                                        if(filter_close.hasClass('on-filter')){
                                            filter_close.removeClass('on-filter');
                                            mobile_filter_class.html(filter_text);
                                        }
                                        jQuery(this).empty();
                                        jQuery(this).remove();
                                    });

                                    /* End filter and search*/
                                    jQuery(".noo-main").html(data);

                                    if (jQuery('[data-paginate="loadmore"]').find(".loadmore-action").length) {
                                        jQuery('[data-paginate="loadmore"]').each(function () {
                                            var $this = jQuery(this);
                                            var max_page = $this.find('.btn-loadmore').data('maxpage');
                                            var itemSelector = 'article.loadmore-item';
                                            if($this.find('.noo-grid').length){
                                                itemSelector = 'div.loadmore-item';
                                            }
                                            $this.nooLoadmore({
                                                navSelector: $this.find("div.pagination"),
                                                nextSelector: $this.find("div.pagination a.next"),
                                                itemSelector: itemSelector,
                                                maxPage: max_page,
                                                finishedMsg: " All resumes displayed"
                                            });
                                            jQuery(".btn-loadmore").on('click', function (e) {
                                                e.preventDefault();
                                                jQuery( document ).ajaxComplete(function() {
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
            };
            resume_ajax();
            var resumeLiveFilter = function(){
                /* Reset search */
                var form_id = jQuery('.widget-csf-resume-live-filter').attr('id');

                jQuery('#' + form_id + ' .reset-search').on('click',function (e) {
                    e.preventDefault();

                    var $form = $(this).parent('form');
                    $form.find('option:selected').removeAttr('selected');

                    $form.get(0).reset(); /* Reset form value*/

                    /* Get any input tag and run the function change() --> callback ajax*/
                    $form.find('.form-group').first().find(':input').not(':button, :submit, :reset, :hidden').change();

                    jQuery('select.form-control',$form).multiselect('refresh');

                    return false;
                });
                /* Ajax Live Search */
                var container = jQuery(".noo-main > .resumes");
                var off_livesearch = jQuery('#' + form_id).data('off-livesearch');
                var can_view_resume = jQuery('#' + form_id).data('can-view');
                if(jQuery('.map-info').length && container.length > 0 && !off_livesearch && can_view_resume){
                    jQuery('#' + form_id).on('change','select,input:not(.filter-search-option):not(.noo-mb-lat-filter):not(.noo-mb-lon-filter):not(.noo-mb-location-address-filter)', function (event) {
                        event.preventDefault();
                        // Filter button when run on Mobile
                        jQuery(this).closest('.widget_noo_advanced_resume_search_widget').removeClass('on-filter');

                        jQuery('.noo-main').addClass('noo-loading').append('<div class="noo-loader loadmore-loading"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>');
                        jQuery('html,body').animate({ scrollTop: jQuery('.noo-main').offset().top -200}, 500);
                        var $form = jQuery('#' + form_id + ' .form-control');
                        var data = jQuery(this).parents('form').serialize();
                        history.pushState(null, null, "?" + $form.serialize());
                        jQuery.ajax({
                            url: nooResumeMap.ajax_url,
                            data: data
                        })
                            .done(function (data) {
                                if (data !== "-1") {

                                    var $newElems = jQuery(data).find(".filter-post-content").html()
                                    var $newSidebar = jQuery(data).find('.filter-sidebar').contents();
                                    var $resultsFilter = jQuery(data).find('.filter-selected').contents();
                                    jQuery(".noo-main").html($newElems);
                                    jQuery('.noo-main').removeClass('noo-loading');
                                    jQuery('.widget-fields-live-filter').html($newSidebar);
                                    jQuery('.results-filter').html($resultsFilter);
                                    if (jQuery('[data-paginate="loadmore"]').find(".loadmore-action").length) {
                                        jQuery('[data-paginate="loadmore"]').each(function () {
                                            var $this = jQuery(this);
                                            var maxPage = $this.find('.btn-loadmore').data('maxpage');
                                            $this.nooLoadmore({
                                                navSelector: $this.find("div.pagination"),
                                                nextSelector: $this.find("div.pagination a.next"),
                                                itemSelector: "article",
                                                maxPage: maxPage,
                                                finishedMsg: " All Resume displayed"
                                            });
                                            jQuery(".btn-loadmore").on('click', function (e) {
                                                e.preventDefault();
                                                jQuery( document ).ajaxComplete(function() {
                                                    search_filter_map();
                                                });
                                            })
                                        });
                                    }
                                    search_filter_map();
                                    picker_bing_map();
                                    FilterFunc.jobQuickSearch();
                                    FilterFunc.NooDatetimepicker();
                                    FilterFunc.hideClearAllFilter();
                                    FilterFunc.collaspe_expand_field_filter();
                                    FilterFunc.GetGeoLocation();
                                    if(jQuery('.noo-mb-job-location-filter').length >=1){
                                        FilterFunc.noo_mb_map_field_filter();
                                    }
                                    FilterFunc.ProximityRange();
                                } else {
                                    location.reload();
                                }
                            })
                            .fail(function () {

                            })

                    });
                }
                jQuery('#' + form_id).submit(function () {
                    jQuery(this).find("input[name='action']").remove();
                    jQuery(this).find("input[name='_wp_http_referer']").remove();
                    jQuery(this).find("input[name='live-filter-nonce']").remove();

                    return true;
                });
            }
            resumeLiveFilter();
            var loadmore = function(){
                jQuery(".btn-loadmore").click(function (e) {
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
                    jQuery( document ).ajaxComplete(function() {
                        search_filter_map();
                    });
                })
            }
            loadmore();
        });
    }
  var picker_bing_map = function () {
      var jm_bing_map = jQuery('.noo-mb-job');
      if (jm_bing_map.length > 0) {

          jm_bing_map.each(function (index, el) {

              var $$ = jQuery(this),
                  id = $$.data('id'),
                  zoom_default = $$.data('zoom'),
                  drag = $$.data('dragged'),
                  draggable = '',
                  zoom = parseInt(JM_Bing_Value.zoom),
                  lat = parseFloat(JM_Bing_Value.lat),
                  lng = parseFloat(JM_Bing_Value.lng);
              var lat_current = jQuery('#latitude').val(),
                  lng_current = jQuery('#longitude').val();

              if (typeof lat_current !== 'undefined' && lat_current !== '') {
                  lat = parseFloat(lat_current);
              }

              if (typeof lng_current !== 'undefined' && lng_current !== '') {
                  lng = parseFloat(lng_current);
              }

              var latitude = jQuery('#noo-mb-lat').val(),
                  longitude = jQuery('#noo-mb-lon').val();

              if (typeof latitude !== 'undefined' && latitude !== '') {
                  lat = parseFloat(latitude);
              }

              if (typeof longitude !== 'undefined' && longitude !== '') {
                  lng = parseFloat(longitude);
              }

              if (zoom == '') {
                  zoom = zoom_default;
              }
              if(drag == 1){
                  draggable = false;
              }else{
                  draggable = true;
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
                  new Pushpin(new Location(center.latitude, center.longitude), {icon: 'https://www.bingmapsportal.com/Content/images/poi_custom.png', draggable: draggable }),
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
                  map.entities.clear();
                  map.setView({ bounds: suggestionResult.bestView });
                  var pushpin = [
                      new Pushpin(new Location(suggestionResult.location.latitude, suggestionResult.location.longitude), {icon: 'https://www.bingmapsportal.com/Content/images/poi_custom.png', draggable: true })
                  ];
                  map.entities.push(pushpin);
                  document.getElementById('noo-mb-lat').value = suggestionResult.location.latitude ;
                  document.getElementById('noo-mb-lon').value = suggestionResult.location.longitude;
              }
          });
      }
  }
    picker_bing_map();

}


