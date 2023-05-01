var FilterFunc = {}; 
(function ($) {
    "use strict";
    FilterFunc ={
        init:function () {
            FilterFunc.jobQuickSearch();
            FilterFunc.hideClearAllFilter();
            FilterFunc.ProximityRange();
            FilterFunc.collaspe_expand_field_filter();
            FilterFunc.noo_mb_map_field_filter();
            FilterFunc.NooDatetimepicker();
            FilterFunc.GetGeoLocation();
        },
        jobQuickSearch:function() {
            $('.form-group').each(function () {
                var filter = $(this).find('.form-control-flat label');
                var qs =  $(this).find('.filter-search-option').quicksearch(filter );
                qs.cache();
            });
        },
        hideClearAllFilter :function () {
            if($('.widget-fields-live-filter').length > 0){
                var $length_results_filter = $('.results-filter').find('li').length;
                if ($length_results_filter > 0) {
                    $('.result-filter-wraper').css("display", "block");
                }
            }
        },
        ProximityRange: function() {
            $( 'input#min_proximity_range, input#max_proximity_range' ).hide();
            $( '.proximity_slider, .price_label' ).show();
            var current_value = $('.proximity_slider').data('value');
            var unit_type = $('.proximity_slider').data('distance');
            $( ".proximity_slider" ).slider({
                range: "min",
                value: current_value,
                min: 1,
                max: nooJobL10n.distance_slider_max_value, 
                slide: function(event,ui){
                    $( ".text-distance" ).val(nooJobL10n.distance_text + ui.value + ' ' + unit_type );
                },
                stop: function( event, ui ) {
                    $("#filter_distance").val(ui.value);
                    setTimeout(function () {
                        $("#filter_distance").trigger('change');
                    },800)
                }
            });
            $( ".text-distance" ).val(nooJobL10n.distance_text + $( ".proximity_slider" ).slider( "value" ) + ' ' + unit_type);
            $( "#filter_distance" ).val($( ".proximity_slider" ).slider( "value" ));

        },
        collaspe_expand_field_filter:function () {
            $('.widget-fields-live-filter').each(function () {
                $(this).find(".form-group .advance-filter-form-control").hide();
                var limit = 5;
                var x = Number(limit);
                $(this).find('.form-group .advance-filter-form-control:lt('+x+')').show();
                $(this).find('.form-group .expand-field:lt('+x+')').removeClass('fa-angle-up').addClass('fa-angle-down');
            });
            $('.widget-fields-live-filter').find('.form-group').each(function () {
                $(this).find('.show-expand').on('click', function () {
                    $(this).closest('.form-group').find('.advance-filter-form-control').slideToggle(500);
                    if( $(this).find('i').hasClass('fa-angle-down')){
                        $(this).find('i').removeClass('fa-angle-down').addClass('fa-angle-up');
                    }else {
                        $(this).find('i').removeClass('fa-angle-up').addClass('fa-angle-down');
                    }

                });
            })
        },
        showPosition:function(position){
            if(nooJobL10n.map_using ==='google'){
                $.ajax({
                    url: "https://maps.googleapis.com/maps/api/geocode/json?latlng="+ position.coords.latitude +','+ position.coords.longitude +"&key=" + nooJobL10n.google_map_key,
                })
                    .done(function (data) {
                        var formattedAddress = data.results[1].formatted_address;
                        $('.noo-mb-location-address-filter').val(formattedAddress);
                        $('.noo-mb-lat-filter').val(position.coords.longitude);
                        $('.noo-mb-lon-filter').val(position.coords.latitude);
                    });
            }else {
                $.ajax({
                    url: "https://dev.virtualearth.net/REST/v1/Locations/"+ position.coords.latitude +','+ position.coords.longitude + "?key=" + nooJobL10n.bing_map_api,
                })
                    .done(function (data) {
                        var formattedAddress = data.resourceSets[0].resources[3].address.formattedAddress;
                        $('.noo-mb-location-address').val(formattedAddress);
                        $('.noo-mb-lon').val(position.coords.longitude);
                        $('.noo-mb-lat').val(position.coords.latitude);

                    });
            }
        },
        showGeoError:function(err){
            alert(err.message);
        },
        GetGeoLocation: function(){
            $('.geocode-location').on('click',function () {
                if(navigator.geolocation){
                    navigator.geolocation.getCurrentPosition(FilterFunc.showPosition,FilterFunc.showGeoError);
                }else {
                    alert("Geolocation is not supported by this browser");
                }
            });
        },
        noo_mb_map_field_filter: function() {
            var field = $('.noo-mb-job-location-filter');
            var lat =(typeof nooLocationPicker !== "undefined") ? parseFloat(nooLocationPicker.lat) : 0;
            var lon = (typeof nooLocationPicker !== "undefined") ? parseFloat( nooLocationPicker.lng) : 0;
            var zoom = (typeof nooLocationPicker !== "undefined") ? parseInt(nooLocationPicker.zoom) : 10;

            var lat_current = $('#noo-mb-lat-filter').val(),
                lng_current = $('#noo-mb-lon-filter').val();

            if (typeof lat_current !== 'undefined' && lat_current !== '') {
                lat = parseFloat(lat_current);
            }

            if (typeof lng_current !== 'undefined' && lng_current !== '') {
                lon = parseFloat(lng_current);
            }

            if (field.length > 0) {
                var locationpickerParams = {
                    location: {
                        latitude: lat,
                        longitude: lon,
                    },
                    radius: 0,
                    zoom: zoom,
                    inputBinding: {
                        latitudeInput: $('.noo-mb-lat-filter'),
                        longitudeInput: $('.noo-mb-lon-filter'),
                        locationNameInput: $('.noo-mb-location-address-filter')
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
        },
        NooDatetimepicker:function () {
            var date_format = nooJobL10n.date_format ? nooJobL10n.date_format : 'Y/m/d';
            $('.jform-datepicker').datetimepicker({
                format: date_format,
                timepicker: false,
                scrollMonth: false,
                scrollTime: false,
                scrollInput: false,
                step: 15,
                validateOnBlur: false,
                onChangeDateTime: function (dp, $input) {
                    if ($input.next('.jform-datepicker_value').length) {
                        $input.next('.jform-datepicker_value').val(parseInt(dp.getTime() / 1000) - 60 * dp.getTimezoneOffset());
                    }
                }
            });

            $('#closing.jform-datepicker').datetimepicker({
                format: date_format,
                timepicker: false,
                scrollMonth: false,
                scrollTime: false,
                scrollInput: false,
                step: 15,
                minDate: 0,
                validateOnBlur: false,
                onChangeDateTime: function (dp, $input) {
                    if (dp && $input.next('.jform-datepicker_value').length) {
                        $input.next('.jform-datepicker_value').val(parseInt(dp.getTime() / 1000) - 60 * dp.getTimezoneOffset());
                    }
                }
            });

            jQuery('.jform-datepicker_start').datetimepicker({
                format: date_format,
                timepicker: false,
                scrollMonth: false,
                scrollTime: false,
                scrollInput: false,
                step: 15,
                validateOnBlur: false,
                onShow: function (ct, $input) {
                    var $maxDate = $input.siblings('.jform-datepicker_end_value').val() ? $input.siblings('.jform-datepicker_end_value').val() : false;
                    if ($maxDate) {
                        $maxDate = Date.parseDate($maxDate, 'unixtime');
                        if ($maxDate) {
                            this.setOptions({
                                maxDate: $maxDate.format0()
                            });
                        }
                    } else {
                        this.setOptions({
                            maxDate: false
                        });
                    }
                },
                onChangeDateTime: function (dp, $input) {
                    if (dp && $input.next('.jform-datepicker_start_value').length) {
                        $input.next('.jform-datepicker_start_value').val(parseInt(dp.getTime() / 1000) - 60 * dp.getTimezoneOffset());
                    }
                }
            });
            jQuery('.jform-datepicker_end').datetimepicker({
                format: date_format,
                timepicker: false,
                scrollMonth: false,
                scrollTime: false,
                scrollInput: false,
                step: 15,
                validateOnBlur: false,
                onShow: function (ct, $input) {
                    var $minDate = $input.siblings('.jform-datepicker_start_value').val() ? $input.siblings('.jform-datepicker_start_value').val() : false;
                    if ($minDate) {
                        $minDate = Date.parseDate($minDate, 'unixtime');
                        if ($minDate) {
                            this.setOptions({
                                minDate: $minDate.format0()
                            });
                        }
                    } else {
                        this.setOptions({
                            minDate: false
                        });
                    }
                },
                onChangeDateTime: function (dp, $input) {
                    if (dp && $input.next('.jform-datepicker_end_value').length) {
                        $input.next('.jform-datepicker_end_value').val(parseInt(dp.getTime() / 1000) - 60 * dp.getTimezoneOffset());
                    }
                }
            });

            $('.jform-datepicker, .jform-datepicker_start, .jform-datepicker_end').on('change',function () {
                var $this = $(this);
                if ($this.val() == '') {
                    $this.next('input[type="hidden"]').val('');
                }
            });
        },
    };
    $(document).ready(function () {
        FilterFunc.init();
        $('textarea').on('keydown',function(event){
            if(event.keyCode == 13) {
                 $(this).val($(this).val()+'\n');
              event.preventDefault();
               
              return false;
            }
          });
        if ($('[data-paginate="loadmore"]').find('.loadmore-action').length) {
            $('[data-paginate="loadmore"]').each(function () {
                var $this = $(this);
                $this.nooLoadmore({
                    navSelector: $this.find('div.pagination'),
                    nextSelector: $this.find('div.pagination a.next'),
                    itemSelector: 'article.loadmore-item',
                    finishedMsg: nooJobL10n.ajax_finishedMsg
                });
            });
        }
        if ($('[data-paginate="nextajax"]').length) {
            $('[data-paginate="nextajax"]').each(function () {
                var _this = $(this),
                    _pagination = _this.find('div.pagination');

                _pagination.find('.next').on('click', function (e) {
                    e.stopPropagation();
                    e.preventDefault();
                    var _pagination_data = _pagination.data(),
                        _max_page = _pagination_data.max_page,
                        _current_page = _pagination_data.current_page,
                        _action = _pagination_data.action,
                        _scroll = _pagination_data.scroll;

                    if ($(this).hasClass('disabled')) {
                        return;
                    }
                    _current_page++;

                    if (_current_page >= _max_page) {
                        $(this).addClass('disabled');
                    }
                    if (_current_page > 1) {
                        _pagination.find('.prev').removeClass('disabled');
                    }
                    _this.addClass('is-waiting');
                    _pagination_data.action = _action;
                    _pagination_data.page = _current_page;

                    $.post(nooJobL10n.ajax_url,
                        _pagination_data,
                        function (res) {
                            _pagination.data('current_page', _current_page);
                            if (res) {
                                _this.find('.nextajax-wrap').html(res);
                            }
                            if (_this.hasClass('is-waiting')) {
                                _this.removeClass('is-waiting');
                            }
                            // ===== Scroll Top
                            $('html, body').animate({
                                scrollTop: $("#" + _scroll).offset().top
                            }, 2000);
                            // ===== Scroll Top
                        });
                });
                _pagination.find('.prev').on('click', function (e) {
                    e.stopPropagation();
                    e.preventDefault();
                    var _pagination_data = _pagination.data(),
                        _max_page = _pagination_data.max_page,
                        _current_page = _pagination_data.current_page,
                        _action = _pagination_data.action,
                        _scroll = _pagination_data.scroll;

                    if ($(this).hasClass('disabled')) {
                        return;
                    }
                    _current_page--;

                    if (_current_page <= 1) {
                        $(this).addClass('disabled');
                    }
                    if (_current_page <= _max_page) {
                        _pagination.find('.next').removeClass('disabled');
                    }
                    _pagination_data.action = _action;
                    _pagination_data.page = _current_page;
                    $.post(nooJobL10n.ajax_url,
                        _pagination_data,
                        function (res) {
                            _pagination.data('current_page', _current_page);
                            if (res) {
                                _this.find('.nextajax-wrap').html(res);
                            }
                            if (_this.hasClass('is-waiting')) {
                                _this.removeClass('is-waiting');
                            }
                            // ===== Scroll Top
                            $('html, body').animate({
                                scrollTop: $("#" + _scroll).offset().top
                            }, 2000);
                            // ===== Scroll Top
                        });
                });
            });
        }
        /* Advand search (Job and Resume) in Mobile */
        var filter_text = $('.mobile-job-filter').html();
        $(document).on('click','.mobile-job-filter', function(){
            var $this = $(this);
            $this.toggleClass('active');
            var filter_class = $(this).closest('.noo-main').next().find('.widget_noo_advanced_job_search_widget,.widget_noo_advanced_resume_search_widget');
            if(filter_class.hasClass('on-filter')){
                filter_class.removeClass('on-filter');
                $this.html(filter_text);
                filter_class.find(".close-mobile-job-filter").empty().remove();
            }else{
                filter_class.addClass('on-filter');
                var $list_tools = $this.closest('.noo-list-tools');
                filter_class.css('top',$list_tools.parent()[0].offsetTop + $list_tools.parent().outerHeight(true) );
                $this.html('<i class="fas fa-times" aria-hidden="true"></i> ' + nooJobL10n.close);
                filter_class.append('<span class="close-mobile-job-filter" style="float: right"><i class="fas fa-times" aria-hidden="true"></i> ' + nooJobL10n.close +'</span>');
            }
        });
        $(document).on('click','.close-mobile-job-filter', function(){
            var filter_close = $(this).closest('div[id^="noo_advanced_"]');
            var mobile_filter_class = $(this).closest('.main-content').find('.mobile-job-filter');
            if(filter_close.hasClass('on-filter')){
                filter_close.removeClass('on-filter');
                mobile_filter_class.html(filter_text);
            }
            $(this).empty();
            $(this).remove();
        });
 
        function noo_ajax_resume_paging() {

            $('body').on('click', '.resume-resume_nextajax .next', function (e) {
                var wrap = $(this).parents('.resume-resume_nextajax'),
                    pagination = wrap.find('.pagination'),
                    max_page = pagination.data('max-page'),
                    posts_per_page = pagination.data('posts-per-page'),
                    show = pagination.data('show'),
                    display_style = pagination.data('style'),
                    current_page = pagination.data('current-page');

                if ($(this).hasClass('disabled')) {
                    return false;
                }
                current_page++;
                wrap.addClass('is-waiting');

                $.post(nooJobL10n.ajax_url,
                    {
                        action: 'noo_resume_nextajax',
                        page: current_page,
                        posts_per_page: posts_per_page,
                        show: show,
                        display_style: display_style
                    }, function (res) {

                        if (res) {
                            wrap.html(res);
                            // pagination = $('body').find('.resume-resume_nextajax .pagination');
                            pagination.data('current-page', current_page);

                            if (current_page >= max_page) {
                                wrap.find('.next').addClass('disabled');
                            }
                            if (current_page > 1) {
                                wrap.find('.prev').removeClass('disabled');
                            }


                        }
                        if (wrap.hasClass('is-waiting')) {
                            wrap.removeClass('is-waiting');
                        }

                    });

                return false;
            });

            $('body').on('click', '.resume-resume_nextajax .prev', function (e) {
                var wrap = $(this).parents('.resume-resume_nextajax'),
                    pagination = wrap.find('.pagination'),
                    posts_per_page = pagination.data('posts-per-page'),
                    show = pagination.data('show'),
                    display_style = pagination.data('style'),
                    current_page = pagination.data('current-page');

                if ($(this).hasClass('disabled')) {
                    return false;
                }
                current_page--;
                wrap.addClass('is-waiting');

                $.post(nooJobL10n.ajax_url,
                    {
                        action: 'noo_resume_nextajax',
                        page: current_page,
                        posts_per_page: posts_per_page,
                        show: show,
                        display_style: display_style
                    }, function (res) {

                        if (res) {

                            wrap.html(res);
                            pagination.data('current-page', current_page);

                            if (current_page <= 1) {
                                wrap.find('.prev').addClass('disabled');
                            } else {
                                $('body').find('.prev').removeClass('disabled');
                            }

                        }
                        if (wrap.hasClass('is-waiting')) {
                            wrap.removeClass('is-waiting');
                        }

                    });

                return false;
            });
        }

        noo_ajax_resume_paging();

        $('.form-control-file').find('input[type=file]').bind('change', function () {
            $(this).closest('label').find('.form-control').prop('value', $(this).val());
        });

        // -- event job slider

        // if ( typeof max != 'undefined' ) {
        // 	for( var sl = 2; sl <= max; sl++ ) {
        // 		$('.list_slider_' + sl).hide();
        // 	}
        // }
        $('.slider_post').css('display', 'none');
        $('.list_slider_1').css('display', 'block');
        var current = 1;
        $('.slider .next').click(function (e) {
            e.stopPropagation();
            e.preventDefault();
            var max = $(this).closest('.slider').find('.total-slider').data('total-slider');
            $(this).closest('.slider').find('.list_slider_' + current).animate({opacity: 0}, 200);
            $(this).closest('.slider').find('.list_slider_' + current).css('display', 'none');
            current = current + 1;
            if (current > max) {
                current = 1;
            }
            $(this).closest('.slider').find('.list_slider_' + current).css('display', 'block');
            $(this).closest('.slider').find('.list_slider_' + current).animate({opacity: 1.0}, 800);
            // $(this).closest('.slider').find('.posts-loop-title').html( max) ;
        });

        $('.slider .prev').click(function (e) {
            e.stopPropagation();
            e.preventDefault();
            var max = $(this).closest('.slider').find('.total-slider').data('total-slider');
            $(this).closest('.slider').find('.list_slider_' + current).animate({opacity: 0}, 200);
            $(this).closest('.slider').find('.list_slider_' + current).css('display', 'none');
            current = current - 1;
            if (current == 0) {
                current = max;
            }
            $(this).closest('.slider').find('.list_slider_' + current).css('display', 'block');
            $(this).closest('.slider').find('.list_slider_' + current).animate({opacity: 1.0}, 800);
            // $(this).closest('.slider').find('.posts-loop-title').html(current + max) ;
        });


        // check all checkboxes
        var checks, checked, first, last, sliced,
            lastClicked = false;
        $('.noo-datatable tbody').children().children('.check-column').find(':checkbox').click(function (e) {


            if ('undefined' == e.shiftKey) {
                return true;
            }
            if (e.shiftKey) {
                if (!lastClicked) {
                    return true;
                }
                checks = $(lastClicked).closest('form').find(':checkbox');
                first = checks.index(lastClicked);
                last = checks.index(this);
                checked = $(this).prop('checked');
                if (0 < first && 0 < last && first != last) {
                    sliced = (last > first) ? checks.slice(first, last) : checks.slice(last, first);
                    sliced.prop('checked', function () {
                        if ($(this).closest('tr').is(':visible'))
                            return checked;

                        return false;
                    });
                }
            }
            lastClicked = this;

            // toggle "check all" checkboxes
            var unchecked = $(this).closest('tbody').find(':checkbox').filter(':visible').not(':checked');

            $(this).closest('table').children('thead, tfoot').find(':checkbox').prop('checked', function () {
                return (0 === unchecked.length);
            });

            return true;
        });

        $('.noo-datatable thead, .noo-datatable  tfoot').find('.check-column :checkbox').on('click.noo-toggle-checkboxes', function (event) {


            var $this = $(this),
                $table = $this.closest('table'),
                controlChecked = $this.prop('checked'),
                toggle = event.shiftKey || $this.data('wp-toggle');

            $table.children('tbody').filter(':visible')
                .children().children('.check-column').find(':checkbox')
                .prop('checked', function () {
                    if ($(this).is(':hidden')) {
                        return false;
                    }

                    if (toggle) {
                        return !$(this).prop('checked');
                    } else if (controlChecked) {
                        return true;
                    }

                    return false;
                });

            $table.children('thead,  tfoot').filter(':visible')
                .children().children('.check-column').find(':checkbox')
                .prop('checked', function () {
                    if (toggle) {
                        return false;
                    } else if (controlChecked) {
                        return true;
                    }

                    return false;
                });
        });


        $('.form-control-editor').wysihtml5({
            "font-styles": true,
            "blockquote": true,
            "emphasis": true,
            "lists": true,
            "align": false,
            "html": true,
            "link": true,
            "image": true,
            "stylesheets": [wysihtml5L10n.stylesheet_rtl]
        });

        $('.job-package').find('button[data-package]').each(function () {
            var _this = $(this);
            _this.click(function (e) {
                _this.closest('.job-package').find('input#package').val(_this.data('package'));
                _this.closest('form').submit();
            });
        });
        //settup validate
        $.extend($.validator.messages, {
            required: nooJobL10n.validate_messages.required,
            remote: nooJobL10n.validate_messages.remote,
            email: nooJobL10n.validate_messages.email,
            url: nooJobL10n.validate_messages.url,
            date: nooJobL10n.validate_messages.date,
            dateISO: nooJobL10n.validate_messages.dateISO,
            number: nooJobL10n.validate_messages.number,
            digits: nooJobL10n.validate_messages.digits,
            creditcard: nooJobL10n.validate_messages.creditcard,
            equalTo: nooJobL10n.validate_messages.equalTo,
            maxlength: $.validator.format(nooJobL10n.validate_messages.maxlength),
            minlength: $.validator.format(nooJobL10n.validate_messages.minlength),
            rangelength: $.validator.format(nooJobL10n.validate_messages.rangelength),
            range: $.validator.format(nooJobL10n.validate_messages.range),
            max: $.validator.format(nooJobL10n.validate_messages.max),
            min: $.validator.format(nooJobL10n.validate_messages.min)
        });
        $.validator.addMethod("uploadimage", function (value, element, param) {
            param = typeof param === "string" ? param.replace(/,/g, "|") : "png|jpe?g|gif";
            return this.optional(element) || value.match(new RegExp(".(" + param + ")$", "i"));
        }, nooJobL10n.validate_messages.uploadimage);

        $.validator.addMethod("uploadcv", function (value, element, param) {
            param = typeof param === "string" ? param.replace(/,/g, "|") : "pdf|doc|docx";
            return this.optional(element) || value.match(new RegExp(".(" + param + ")$", "i"));
        }, nooJobL10n.validate_messages.extension);

        $.validator.addClassRules({
            'jform-validate': {
                required: true
            },
            'jform-validate-email': {
                email: true
            },
            'jform-chosen-validate': {
                required: true
            },
            'jform-validate-uploadimage': {
                uploadimage: "png|jpe?g|gif"
            },
            'jform-validate-uploadcv': {
                uploadcv: (nooJobL10n.file_exts !== undefined ? nooJobL10n.file_exts : "pdf|doc|docx")
            }
        });
        var post_job_form = $('#post_job_form');
        post_job_form.validate({
            onkeyup: false,
            onfocusout: false,
            onclick: false,
            errorClass: "jform-error",
            validClass: "jform-valid",
            errorElement: "span",
            ignore: ":hidden:not(.ignore-valid)",
            errorPlacement: function (error, element) {
                if (element.is(':radio') || element.is(':checkbox') || element.is(':file'))
                    error.appendTo(element.parent().parent());
                else
                    error.appendTo(element.parent());
            },
            rules: {
                recaptcha_response_field: {
                    required: true,
                    recaptcha: true
                }
            }
        });
        $('form.jform-validate').each(function () {
            $(this).validate({
                onkeyup: false,
                onfocusout: false,
                onclick: false,
                errorClass: "jform-error",
                validClass: "jform-valid",
                errorElement: "span",
                ignore: ":hidden:not(.ignore-valid)",
                errorPlacement: function (error, element) {
                    if (element.is(':radio') || element.is(':checkbox') || element.is(':file'))
                        error.appendTo(element.parent().parent());
                    else
                        error.appendTo(element.parent());
                }
            });
        });

        $('body').on('click', '.noo-btn-job-alert-form', function () {
            $('#modalJobAlertForm').modal('show');
            $('#modalJobAlertForm').on('shown.bs.modal', function () {
                $('.form-control-chosen', $(this)).multiselect();

            })

        });

        $('body').on('click', '.noo-btn-save-job-alert', function () {
            var btn = $(this),
                form = $('.noo-job-alert-form'),
                current_txt = btn.html(),
                notice = form.find('.noo-job-alert-notice');


            form.block({
                message: null, overlayCSS: {
                    backgroundColor: '#fafafa',
                    opacity: 0.5,
                    cursor: 'wait'
                }
            });

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: nooMemberL10n.ajax_url,
                data: form.serialize(),
                beforeSend: function () {
                    btn.append('<i class="fa fa-spinner fa-spin"></i>');
                },
                success: function (data) {
                    btn.html(current_txt);
                    form.unblock();

                    if (data.success == true) {
                        $('.noo-job-alert-form')[0].reset();
                        notice.addClass('success');
                        notice.removeClass('error');
                    } else {
                        notice.removeClass('success');
                        notice.addClass('error');
                    }

                    notice.html(data.message);
                },
                complete: function () {

                },
                error: function () {
                }
            });
        });

        $('body').on('click','.noo-btn-resume-alert-form',function () {
            $('#modalResumeAlertForm').modal('show');
            $('#modalResumeAlertForm').on('show.bs.modal',function () {
                $('.form-control-chosen',$(this)).multiselect();
            })
        });

        $('body').on('click', '.noo-btn-save-resume-alert', function () {
            var btn = $(this),
                form = $('.noo-resume-alert-form'),
                current_txt = btn.html(),
                notice = form.find('.noo-resume-alert-notice');


            form.block({
                message: null, overlayCSS: {
                    backgroundColor: '#fafafa',
                    opacity: 0.5,
                    cursor: 'wait'
                }
            });

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: nooMemberL10n.ajax_url,
                data: form.serialize(),
                beforeSend: function () {
                    btn.append('<i class="fa fa-spinner fa-spin"></i>');
                },
                success: function (data) {
                    btn.html(current_txt);
                    form.unblock();

                    if (data.success == true) {
                        $('.noo-resume-alert-form')[0].reset();
                        notice.addClass('success');
                        notice.removeClass('error');
                    } else {
                        notice.removeClass('success');
                        notice.addClass('error');
                    }

                    notice.html(data.message);
                },
                complete: function () {

                },
                error: function () {
                }
            });
        });

        $('.noo-btn-bookmark').click(function () {

            if (!nooMemberL10n.is_logged) {
                $('.memberModalLogin').modal('show');
                return;
            }

            var btn = $(this);
            var icon_label = $(this).find('.noo-tool-label');
            var bookmarked = btn.hasClass('bookmarked');

            btn.find('.fa').removeClass('fa-heart').addClass('fa-spinner fa-spin');

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: nooMemberL10n.ajax_url,
                data: {
                    action: 'noo_bookmark_job',
                    security: btn.attr('data-security'),
                    job_id: btn.attr('data-job-id')
                },
                success: function (data) {

                    btn.find('.fa').addClass('fa-heart').removeClass('fa-spinner fa-spin');

                    if (data.success == true) {

                        icon_label.text(data.message_text);

                        if (bookmarked) {
                            btn.removeClass('bookmarked');
                        } else {
                            btn.addClass('bookmarked');
                        }
                        if(result.message){
                            $.notify(data.message, {
                                position: "right bottom",
                                className: 'success'
                            });
                        }

                    } else {
                        if(data.message){
                            $.notify(data.message, {
                                position: "right bottom",
                                className: 'error',
                            });
                        }
                    }
                },
                complete: function () {
                },
                error: function () {
                }
            });

        });

        function noo_modal_send_mail_job() {

            $('body').on('click', '.noo-tool-email-job', function () {

                var btn = $(this);
                var job_id = btn.data('id');
                var modal = $('#modalSendEmailJob_' + job_id);
                modal.modal('show');

            });
        }

        // noo_modal_send_mail_job();

        function noo_modal_send_mail_job_new() {

            $('body').on('click', '.noo-tool-email-job', function () {

                var btn = $(this);
                var job_id = btn.data('id');
                var job_title = btn.data('title');
                var job_url = btn.data('url');

                var input_job_id = $('#noo_form_job_id');
                var input_content = $('#noo_form_email_content');

                input_job_id.val(job_id);
                input_content.val(job_title + '\n' + job_url);

                var modal = $('#modalSendEmailJob');
                modal.modal('show');

            });
        }

        noo_modal_send_mail_job_new();

        function noo_ajax_send_mail_job() {

            $('body').on('click', '.noo-btn-send-job-email', function (event) {
                event.preventDefault();
                var btn = $(this),
                    form = btn.closest('.noo-form-email-job-wrap'),
                    job_id = form.find('input[name="job_id"]'),
                    name = form.find('input[name="friend_name"]'),
                    email = form.find('input[name="friend_email"]'),
                    content = form.find('textarea[name="email_content"]'),
                    notice = form.find('.noo-job-mail-notice'),
                    elm_wrap = btn.closest('.noo-form-email-job-wrap').find('.job-send-email');


                notice.removeClass('success error');
                notice.html('');

                form.block({
                    message: null, overlayCSS: {
                        backgroundColor: '#fafafa',
                        opacity: 0.5,
                        cursor: 'wait'
                    }
                });

                $.ajax({
                    url: nooMemberL10n.ajax_url,
                    type: 'POST',
                    dataType: 'json',
                    data: elm_wrap.serializeArray(),
                    success: function (data) {

                        if (data.success == true) {
                            notice.addClass('success');
                            name.val('');
                            email.val('');
                            if(data.message){
                                $.notify(data.message, {
                                    position: "right bottom",
                                    className: 'success'
                                });
                            }
                            btn.closest('#modalSendEmailJob').modal('hide');
                        } else {
                            notice.addClass('error');
                            if(data.message){
                                $.notify(data.message, {
                                    position: "right bottom",
                                    className: 'error'
                                });
                            }
                        }

                        form.unblock();
                    },
                    complete: function () {

                    },
                    error: function () {
                    }
                });

            })
            ;
        }

        noo_ajax_send_mail_job();

        if ($('.page-member').length > 0) {

            var table_job = $('#noo-table-job').DataTable({
                stateSave: true,
                responsive: true,
                lengthMenu: [[25, 50, -1], [25, 50, "All"]],
                columnDefs: [
                    {
                        "orderable": false,
                        "targets": 0
                    }
                ],
                language: {
                    info: nooJobL10n.dataTable.info,
                    lengthMenu: nooJobL10n.dataTable.lengthMenu,
                    search: nooJobL10n.dataTable.search,
                    infoEmpty: nooJobL10n.dataTable.infoEmpty,
                    emptyTable: nooJobL10n.dataTable.emptyTable,
                    paginate: {
                        next: nooJobL10n.dataTable.next,
                        previous: nooJobL10n.dataTable.previous
                    },

                }
            });
            
            var table_application_element = $('#noo-table-app'),
            	table_application_ajax_option
            	
            if(table_application_element.data('use-ajax') === 'yes'){
            	table_application_ajax_option = {
    	                responsive: true,
    	                stateSave: true,
    	                order: [[0, 'desc']],
    	                language: {
    	                    info: nooJobL10n.dataTable.info,
    	                    lengthMenu: nooJobL10n.dataTable.lengthMenu,
    	                    search: nooJobL10n.dataTable.search,
    	                    searchPlaceholder: nooJobL10n.dataTable.searchByCandidate,
    	                    infoEmpty: nooJobL10n.dataTable.infoEmpty,
    	                    emptyTable: nooJobL10n.dataTable.emptyTable,
    	                    paginate: {
    	                        next: nooJobL10n.dataTable.next,
    	                        previous: nooJobL10n.dataTable.previous
    	                    },
    	                },
    	                processing: true,
    	                serverSide: true,
    	                columnDefs: [
    	             		{
    	                		targets: 3,
    	                		createdCell:  function (td, cellData, rowData, row, col) {
    	                   			$(td).attr('data-filter', rowData.job_id); 
    	                		}
    	             		},
    	             		{
    	                		targets: 6,
    	                		createdCell:  function (td, cellData, rowData, row, col) {
    	                   			$(td).attr('data-sort', rowData.date_sort); 
    	                		}
    	             		},
    	             		{
    	                		targets: 7,
    	                		createdCell:  function (td, cellData, rowData, row, col) {
    	                   			$(td).attr('data-filter', rowData.status_class); 
    	                		}
    	             		}
    	          		],
    	                columns: [
    	                	{
    	                        "class":          "check-column",
    	                        "data":           'id',
    	                        "orderable":      false,
    	                    },
    	                    { 
    	                    	'data': 'candidate',
    	                    	"orderable":      false,
    	                    },
    	                    // { 
    	                    // 	'data': 'avatar',
    	                    // 	"orderable":      false,
    	                    // },
    	                    {
    	                        "data":           'job',
    	                        "orderable":      false,
    	                    },
    	                    {
                                "data":           'message',
    	                        "orderable":      false,
    	                    },
    	                    { 
    	                    	'data': 'attachment',
    	                    	"orderable":      false,
    	                    },
    	                    {
    	                        "data":           'date',
    	                        "orderable":      true,
    	                    },
    	                    {
    	                        "class":          "text-center",
    	                        "data":           'status',
    	                        "orderable":      false,
    	                    },
    	                    {
    	                        "class":          "member-manage-actions text-center",
    	                        "data":           'action',
    	                        "orderable":      false,
    	                    }
    	                ],
    	                ajax: {
    	                    url: nooJobL10n.ajax_action.toString().replace('__action__', 'dashboard_ajax_application_datatable'),
    	                    type: "POST",
    	                    data: function ( data ) {
    	                    	data.job_ids = table_application_element.data('job-ids');
    	                    	data.filter_job = $('#application_job').val()
    	                    	data.filter_status = $('#application_status').val()
    	                    },
                            dataSrc: function(d){
                                var $title = $('.member-manage-application-title');
                                if(d.recordsTotal > 0){
                                    $title.html($title.data('title-text').toString().replace('%s',d.recordsTotal))
                                }else{
                                    $title.html($title.data('no-title-text'))
                                }
                                return d.data;    
                            }
    	                }
    	            }
            }else{
            	table_application_ajax_option = {
    	                responsive: true,
    	                stateSave: true,
    	                language: {
    	                    info: nooJobL10n.dataTable.info,
    	                    lengthMenu: nooJobL10n.dataTable.lengthMenu,
    	                    search: nooJobL10n.dataTable.search,
    	                    infoEmpty: nooJobL10n.dataTable.infoEmpty,
    	                    emptyTable: nooJobL10n.dataTable.emptyTable,
    	                    paginate: {
    	                        next: nooJobL10n.dataTable.next,
    	                        previous: nooJobL10n.dataTable.previous
    	                    },
    	                }
    	            }
            }
            var table_application = table_application_element.DataTable(table_application_ajax_option);
            
            table_application.on('draw',function(){
                $('[data-toggle="tooltip"]').tooltip();
            })
            
            var table_viewed_resume = $('#noo-table-viewed-resume').DataTable({
                responsive: true,
                stateSave: true,
                language: {
                    info: nooJobL10n.dataTable.info,
                    lengthMenu: nooJobL10n.dataTable.lengthMenu,
                    search: nooJobL10n.dataTable.search,
                    infoEmpty: nooJobL10n.dataTable.infoEmpty,
                    emptyTable: nooJobL10n.dataTable.emptyTable,
                    paginate: {
                        next: nooJobL10n.dataTable.next,
                        previous: nooJobL10n.dataTable.previous
                    },

                }
            });
            var table_follow = $('#noo-table-follow').DataTable({
                responsive: true,
                stateSave: true,
                language: {
                    info: nooJobL10n.dataTable.info,
                    lengthMenu: nooJobL10n.dataTable.lengthMenu,
                    search: nooJobL10n.dataTable.search,
                    infoEmpty: nooJobL10n.dataTable.infoEmpty,
                    emptyTable: nooJobL10n.dataTable.emptyTable,
                    paginate: {
                        next: nooJobL10n.dataTable.next,
                        previous: nooJobL10n.dataTable.previous
                    },

                }
            });
            var table_job_follow = $('#noo-table-job-follow').DataTable({
                responsive: true,
                stateSave: true,
                language: {
                    info: nooJobL10n.dataTable.info,
                    lengthMenu: nooJobL10n.dataTable.lengthMenu,
                    search: nooJobL10n.dataTable.search,
                    infoEmpty: nooJobL10n.dataTable.infoEmpty,
                    emptyTable: nooJobL10n.dataTable.emptyTable,
                    paginate: {
                        next: nooJobL10n.dataTable.next,
                        previous: nooJobL10n.dataTable.previous
                    },

                }
            });
            var table_shortlist = $('#noo-table-shortlist').DataTable({
                responsive: true,
                stateSave: true,
                language: {
                    info: nooJobL10n.dataTable.info,
                    lengthMenu: nooJobL10n.dataTable.lengthMenu,
                    search: nooJobL10n.dataTable.search,
                    infoEmpty: nooJobL10n.dataTable.infoEmpty,
                    emptyTable: nooJobL10n.dataTable.emptyTable,
                    paginate: {
                        next: nooJobL10n.dataTable.next,
                        previous: nooJobL10n.dataTable.previous
                    },

                }
            });
            var table_resume = $('#noo-table-resume').DataTable({
                responsive: true,
                stateSave: true,
                language: {
                    info: nooJobL10n.dataTable.info,
                    lengthMenu: nooJobL10n.dataTable.lengthMenu,
                    search: nooJobL10n.dataTable.search,
                    infoEmpty: nooJobL10n.dataTable.infoEmpty,
                    emptyTable: nooJobL10n.dataTable.emptyTable,
                    paginate: {
                        next: nooJobL10n.dataTable.next,
                        previous: nooJobL10n.dataTable.previous
                    },

                }
            });

            var table_job_applied = $('#noo-table-job-applied').DataTable({
                responsive: true,
                stateSave: true,
                language: {
                    info: nooJobL10n.dataTable.info,
                    lengthMenu: nooJobL10n.dataTable.lengthMenu,
                    search: nooJobL10n.dataTable.search,
                    infoEmpty: nooJobL10n.dataTable.infoEmpty,
                    emptyTable: nooJobL10n.dataTable.emptyTable,
                    paginate: {
                        next: nooJobL10n.dataTable.next,
                        previous: nooJobL10n.dataTable.previous
                    },

                }
            });
            var table_job_alert = $('#noo-table-job-alert').DataTable({
                responsive: true,
                stateSave: true,
                language: {
                    info: nooJobL10n.dataTable.info,
                    lengthMenu: nooJobL10n.dataTable.lengthMenu,
                    search: nooJobL10n.dataTable.search,
                    infoEmpty: nooJobL10n.dataTable.infoEmpty,
                    emptyTable: nooJobL10n.dataTable.emptyTable,
                    paginate: {
                        next: nooJobL10n.dataTable.next,
                        previous: nooJobL10n.dataTable.previous
                    },

                }
            });
            var table_resume_alert = $('#noo-table-resume-alert').DataTable({
                responsive: true,
                stateSave: true,
                language: {
                    info: nooJobL10n.dataTable.info,
                    lengthMenu: nooJobL10n.dataTable.lengthMenu,
                    search: nooJobL10n.dataTable.search,
                    infoEmpty: nooJobL10n.dataTable.infoEmpty,
                    emptyTable: nooJobL10n.dataTable.emptyTable,
                    paginate: {
                        next: nooJobL10n.dataTable.next,
                        previous: nooJobL10n.dataTable.previous
                    },

                }
            });
            var table_job_bookmark = $('#noo-table-job-bookmark').DataTable({
                responsive: true,
                stateSave: true,
                language: {
                    info: nooJobL10n.dataTable.info,
                    lengthMenu: nooJobL10n.dataTable.lengthMenu,
                    search: nooJobL10n.dataTable.search,
                    infoEmpty: nooJobL10n.dataTable.infoEmpty,
                    emptyTable: nooJobL10n.dataTable.emptyTable,
                    paginate: {
                        next: nooJobL10n.dataTable.next,
                        previous: nooJobL10n.dataTable.previous
                    },

                }
            });

            $("#job_status").on('change',function () {
                $.fn.dataTable.ext.search.push(
                    function (settings, data, dataIndex) {

                        var stt_val = $("#job_status option:selected").val();
                        // alert(stt_val);
                        var col_stt_val = data[7];

                        if (stt_val == 'undefined' || stt_val == '') {
                            return true;
                        }


                        if (col_stt_val == stt_val) {
                            return true;
                        }

                        return false;
                    }
                );

                table_job.draw();

            });

            $("#application_status").on('change',function () {

                $.fn.dataTable.ext.search.push(
                    function (settings, data, dataIndex) {

                        var stt_val = $("#application_status option:selected").val();
                        var col_stt_val = data[7];



                        if (stt_val == 'undefined' || stt_val == '') {
                            return true;
                        }


                        if (col_stt_val == stt_val) {
                            return true;
                        }

                        return false;
                    }
                );

                table_application.draw();

            });
            $("#application_job").on('change',function () {

                $.fn.dataTable.ext.search.push(
                    function (settings, data, dataIndex) {

                        var job_val = $("#application_job option:selected").val();
                        var col_job_val = data[3];

                        if (job_val == 'undefined' || job_val == 0) {
                            return true;
                        }


                        if (col_job_val == job_val) {
                            return true;
                        }

                        return false;
                    }
                );

                table_application.draw();

            });

            $("#job_applied_status").on('change',function () {

                $.fn.dataTable.ext.search.push(
                    function (settings, data, dataIndex) {

                        var stt_val = $("#job_applied_status option:selected").val();
                        var col_stt_val = data[5];

                        if (stt_val == 'undefined' || stt_val == '') {
                            return true;
                        }


                        if (col_stt_val == stt_val) {
                            return true;
                        }

                        return false;
                    }
                );
                table_job_applied.draw();

            });

        }


        $(document).on('click', '.noo-shortlist', function (event) {
            event.preventDefault();

            var current_event = $(this),
                resume_id = current_event.data('resume-id'),
                user_id = current_event.data('user-id'),
                type = current_event.data('type'),
                current_txt = current_event.html();

            if($('#noo-table-shortlist_wrapper').length > 0){
                current_event.closest('tr').addClass('selected');
            }
            

            $.ajax({
                url: nooMemberL10n.ajax_url,
                type: 'POST',
                dataType: 'json',
                data: {
                    'action': 'noo_shortlist',
                    'resume_id': resume_id,
                    'user_id': user_id,
                    'type': type
                },
                beforeSend: function () {
                    if ('text' == type) {
                        current_event.append('<i class="fa fa-spinner fa-spin"></i>');
                    } else {
                        current_event.html('<i class="fa fa-spinner fa-spin"></i>');
                    }
                },
                success: function (res) {
                    if (res.status === 'error') {
                        $.notify(res.message, {
                            position: "right bottom",
                            className: 'error'
                        });
                    }
                    current_event.html(current_txt);
                    try {
                        current_event.html(res.label);
                        if($('#noo-table-shortlist_wrapper').length > 0){
                            table_shortlist.row('.selected').remove().draw(false);
                        }
                    } catch (e) {
                        alert(e);
                    }

                },
            })
        });

        function noo_job_apply_facebook() {
            $('#apply_via_facebook').click(function () {
                if (typeof(FB) != 'undefined') {
                    FB.login(function (result) {
                        if (result.authResponse) {
                            var grantedScopes = result.authResponse.grantedScopes.split(',');
                            if (grantedScopes.indexOf('email') !== -1) {
                                FB.api('/me?fields=name,email', function (response) {

                                    if (!response || response.error) {

                                        alert('msgMissingAppID');
                                    } else if (!response.email) {
                                        alert('msgMissingEmail');
                                    } else {
                                        $('#fb_candidate_name').val(response.name);
                                        $('#fb_candidate_email').val(response.email);
                                        $('#fb_candidate_id').val(response.id);
                                        var modal_form = $('#applyFacebookModal');
                                        modal_form.modal('show');
                                    }
                                });
                            } else {
                                alert('msgFBMissingEmail');
                                FB.api('/me/permissions', 'DELETE');
                            }
                        } else {
                            alert('Cancel');
                            return false;
                        }
                    }, {
                        scope: 'email',
                        return_scopes: true
                    });

                    return false;
                }
            })
        }

        noo_job_apply_facebook();

        function job_refresh() {
            $('.btn-refresh-job').on('click', function () {
                var btn = $(this);
                var job_id = btn.data('id');

                btn.html('<i class="fas fa-spinner fa-pulse"></i>');
                $.ajax({
                    type: 'POST',
                    url: nooL10n.ajax_url,
                    data: {
                        action: 'noo_ajax_refresh_job',
                        job_id: job_id
                    },
                    success: function (result) {
                        btn.html('<i class="fas fa-sync-alt"></i>');


                        $('.btn-refresh-job').attr('title', result.remain).tooltip('fixTitle');
                        btn.tooltip('show');
                        if(result.message){
                            $.notify(result.message, {
                                position: "right bottom",
                                className: result.status
                            });
                        }
                    },
                    error: function (errorThrown) {
                    }
                });
            })
        }

        job_refresh();


        function resume_refresh() {
            $('.btn-refresh-resume').click(function () {
                var btn = $(this);
                var resume_id = btn.data('id');

                btn.find('.fa').addClass('fa-spin');
                $.ajax({
                    type: 'POST',
                    url: nooL10n.ajax_url,
                    data: {
                        action: 'noo_ajax_refresh_resume',
                        resume_id: resume_id
                    },
                    success: function (result) {

                        btn.find('.fa').removeClass('fa-spin');


                        $('.btn-refresh-resume').attr('title', result.remain).tooltip('fixTitle');
                        btn.tooltip('show');
                        if(result.message){
                            $.notify(result.message, {
                                position: "right bottom",
                                className: result.status
                            });
                        }
                    },
                    error: function (errorThrown) {
                    }
                });
            })
        }

        resume_refresh();


        function job_clone() {
            $('.btn-clone-job').click(function () {
                var btn = $(this);
                var job_id = btn.data('id');

                btn.find('.fa').removeClass('fa-clone');
                btn.find('.fa').addClass('fa-spin fa-spinner');
                $.ajax({
                    type: 'POST',
                    url: nooL10n.ajax_url,
                    data: {
                        action: 'noo_ajax_clone_job',
                        job_id: job_id
                    },
                    success: function (result) {

                        btn.find('.fa').removeClass('fa-spin fa-spinner');
                        btn.find('.fa').addClass('fa-clone');
                        if(result.message){
                            $.notify(result.message, {
                                position: "right bottom",
                                className: result.status
                            });
                        }

                        window.location = result.url;
                    },
                    error: function (errorThrown) {
                    }
                });
            })
        }

        job_clone();

        function resume_clone() {
            $('.btn-clone-resume').click(function () {
                var btn = $(this);
                var resume_id = btn.data('id');

                btn.find('.fa').removeClass('fa-clone');
                btn.find('.fa').addClass('fa-spin fa-spinner');
                $.ajax({
                    type: 'POST',
                    url: nooL10n.ajax_url,
                    data: {
                        action: 'noo_ajax_clone_resume',
                        resume_id: resume_id
                    },
                    success: function (result) {

                        btn.find('.fa').removeClass('fa-spin fa-spinner');
                        btn.find('.fa').addClass('fa-clone');
                        if(result.message){
                            $.notify(result.message, {
                                position: "right bottom",
                                className: result.status
                            });
                        }

                        window.location = result.url;
                    },
                    error: function (errorThrown) {
                    }
                });
            })
        }

        resume_clone();

        function noo_mb_map_field() {
            var field = $('.noo-mb-job-location');

            var lat =(typeof nooLocationPicker !== "undefined") ? parseFloat(nooLocationPicker.lat) : 0;
            var lon = (typeof nooLocationPicker !== "undefined") ? parseFloat( nooLocationPicker.lng) : 0;
            var zoom = (typeof nooLocationPicker !== "undefined") ? parseInt(nooLocationPicker.zoom) : 10;

            var lat_current = $('#noo-mb-lat').val(),
                lng_current = $('#noo-mb-lon').val();

            if (typeof lat_current !== 'undefined' && lat_current !== '') {
                lat = parseFloat(lat_current);
            }

            if (typeof lng_current !== 'undefined' && lng_current !== '') {
                lon = parseFloat(lng_current);
            }

            if (field.length > 0) {
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
                };
                if (nooLocationPicker && nooLocationPicker.componentRestrictions) {
                    locationpickerParams.autocompleteOptions = {
                        types: nooLocationPicker.types,
                        componentRestrictions: {'country': nooLocationPicker.componentRestrictions}
                    };
                }
                field.locationpicker(locationpickerParams);
            }

        }

        noo_mb_map_field();

        function show_company_review_info() {
            $('.noo-review-voted').css("display", "none");
            $('.reviewed-box-icon').click(function () {
                $(this).next('.noo-review-voted').toggle(500);
            });
        }

        show_company_review_info();

        function download_remain_count() {
            $('.btn-download-cv').click(function (event) {
                event.preventDefault();
                var btn = $(this);
                var user_id = btn.data('id');
                var resume_id = btn.data('resume-id');
                var link_download = btn.data('link-download');
                $.ajax({
                    type: 'POST',
                    url: nooL10n.ajax_url,
                    data: {
                        action: 'noo_ajax_download_cv_count',
                        user_id: user_id,
                        resume_id: resume_id,
                        link_download: link_download
                    },
                    success: function (result) {
                        $('.btn-download-cv').attr('title', result.remain).tooltip('fixTitle');
                        btn.tooltip('show');
                        if(result.message){
                            $.notify(result.message, {
                                position: "right bottom",
                                className: result.status
                            });
                        }
                        if(result.status ==='success'){
                            window.open(result.link);
                        }
                        // window.location.href = result.link;
                    },
                    error: function (errorThrown) {
                        event.preventDefault();
                    }
                });
            })
        }
        download_remain_count();

        function count_view_candidate_contact_remain() {
            $('.show-candidate-contact').click(function () {
                var btn = $(this);
                var user_id = btn.data('id');
                var resume_id = btn.data('resume-id');
                $.ajax({
                    type: 'POST',
                    url: nooL10n.ajax_url,
                    data: {
                        action:'view_candidate_contact_remain',
                        user_id: user_id,
                        resume_id: resume_id,
                    },
                    beforeSend: function () {
                        btn.append('<i class="fa fa-spinner fa-spin"></i>');

                    },
                    success: function (result) {
                        if(result.status === 'success'){
                            btn.hide();
                            btn.parent().append(result.contact);
                        }
                        if(result.message){
                            $.notify(result.message, {
                                position: "right bottom",
                                className: result.status
                            });
                        }
                    },
                    error: function (errorThrown) {
                    }
                });
            })
        }

        count_view_candidate_contact_remain();

        $('.posts-loop-content').on('click', '.loadmore_job_indeed', function(event) {
            event.preventDefault();
            // === <<< set icon loading
            var $this = $(this);
            $this.hide().siblings('.noo-loader').show();

            // === <<< var
            var public_id = $(this).data( 'public-id' );
            var indeed_query = $(this).data( 'query' );
            var indeed_localtion = $(this).data( 'localtion' );
            var indeed_job_type = $(this).data( 'job-type' );
            var indeed_country = $(this).data( 'country' );
            var limit = $(this).data( 'limit' );
            // var min = $(this).data( 'min' );
            var max = $(this).data( 'max' );

            // === <<< set job per page
            // $(this).data( 'min', max );
            $(this).data( 'max', max + limit );

            // === <<< set url
            var url = "http://api.indeed.com/ads/apisearch?publisher=" + public_id + "&q=" + indeed_query + "&l=" + indeed_localtion + "&sort=&radius=&st=&jt=" + indeed_job_type + "&start=" + max + "&limit=" + limit + "&fromage=&filter=&latlong=1&co=" + indeed_country + "&v=2";

            // === <<< process
            var data = {
                action : 'load_job_item',
                url : url,
                public_id : public_id,
                indeed_query : indeed_query,
                indeed_localtion : indeed_localtion,
                indeed_job_type : indeed_job_type,
                indeed_country : indeed_country,
                start : max,
                limit : limit,
            };
            $.post(nooL10n.ajax_url, data, function( list, status ) {
                if ( status == 'success' && list != "-1" ) {

                    // === <<< remove icon loading
                    $this.siblings('.noo-loader').hide();
                    $this.show();
                    // === <<< Add list
                    $this.closest('.posts-loop-content').find('.list_loadmore_job').append( list );

                } else {
                    window.location.reload();
                }
            });

        });

        function jobLiveFilter() {
            var form_id = $('.widget-csf-live-filter').attr('id');
            var is_tax = $('#' + form_id).data('is-tax');
            if (!is_tax) {
                $('#' + form_id + ' .reset-search').on('click', function (e) {
                    e.preventDefault();

                    var $form = $(this).parent('form');
                    $form.find('option:selected').removeAttr('selected');

                    $form.get(0).reset();
                    /* Reset form value*/

                    /* Get any input tag and run the function change() --> callback ajax*/
                    $form.find('.form-group').first().find(':input').not(':button, :submit, :reset, :hidden').trigger('change');

                    $('select.form-control',$form).multiselect('refresh');

                    return false;
                });
            }
            /* Ajax Live Search */
            var container = $(".noo-main > .jobs");
            var off_livesearch = $('#' + form_id).data('off-livesearch');

            if (!$('.map-info').length && container.length > 0 && !off_livesearch) {
                $('#' + form_id).on('change','select,input:not(.filter-search-option):not(.noo-mb-lat-filter):not(.noo-mb-lon-filter):not(.noo-mb-location-address-filter)', function (event) {
                    event.preventDefault();
                    // Filter button when run on Mobile
                    $(this).closest('.widget_noo_advanced_job_search_widget').removeClass('on-filter');

                    $('.noo-main').addClass('noo-loading').append('<div class="noo-loader loadmore-loading"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>');
                    $('html,body').animate({scrollTop: $('.noo-main').offset().top - 200}, 500);
                    var $form = $('#' + form_id + ' .form-control');
                    var data = $(this).parents('form').serialize();
                    history.pushState(null, null, "?" + $form.serialize());
                    $.ajax({
                        url: nooJobL10n.ajax_url,
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
                                    });
                                }
                                FilterFunc.collaspe_expand_field_filter();
                                FilterFunc.hideClearAllFilter();
                                FilterFunc.jobQuickSearch();
                                FilterFunc.GetGeoLocation();
                                if($('.noo-mb-job-location-filter').length >=1){
                                    FilterFunc.noo_mb_map_field_filter();
                                }else if($('.noo-mb-job').length >=1){
                                    JM_Bing_Map();
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
        }
        jobLiveFilter();
        function resumeLiveFilter() {
            var form_id = $('.widget-csf-resume-live-filter').attr('id');

            $('#' + form_id + ' .reset-search').on('click', function (e) {
                e.preventDefault();

                var $form = $(this).parent('form');
                $form.find('option:selected').removeAttr('selected');

                $form.get(0).reset();
                /* Reset form value*/

                /* Get any input tag and run the function change() --> callback ajax*/
                $form.find('.form-group').first().find(':input').not(':button, :submit, :reset, :hidden').trigger('change');

                $('select.form-control',$form).multiselect('refresh');

                return false;
            });
            /* Ajax Live Search */
            var container = $(".noo-main > .resumes");
            var off_livesearch = $('#' + form_id).data('off-livesearch');
            var can_view_resume = $('#' + form_id).data('can-view');
            if (!$('.map-info').length && container.length > 0 && !off_livesearch && can_view_resume) {
                $('#' + form_id).on('change','select,input:not(.filter-search-option):not(.noo-mb-lat-filter):not(.noo-mb-lon-filter):not(.noo-mb-location-address-filter)', function (event) {
                    event.preventDefault();
                    // Filter button when run on Mobile
                    $(this).closest('.widget_noo_advanced_resume_search_widget').removeClass('on-filter');

                    $('.noo-main').addClass('noo-loading').append('<div class="noo-loader loadmore-loading"><span></span><span></span><span></span><span></span><span></span><span></span><span></span><span></span></div>');
                    $('html,body').animate({scrollTop: $('.noo-main').offset().top - 200}, 500);
                    var $form = $('#' + form_id + ' .form-control');
                    var data = $(this).parents('form').serialize();
                    history.pushState(null, null, "?" + $form.serialize());
                    $.ajax({
                        url: nooJobL10n.ajax_url,
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
                                            itemSelector: "article",
                                            maxPage: maxPage,
                                            finishedMsg: " All Resume displayed"
                                        });
                                    });
                                }
                                FilterFunc.collaspe_expand_field_filter();
                                FilterFunc.hideClearAllFilter();
                                FilterFunc.jobQuickSearch();
                                FilterFunc.GetGeoLocation();
                                if($('.noo-mb-job-location-filter').length >=1){
                                    FilterFunc.noo_mb_map_field_filter();
                                }else if($('.noo-mb-job').length >=1){
                                    JM_Bing_Map();
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
        }
        resumeLiveFilter();
    });
})(jQuery);