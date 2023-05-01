(function ($) {

	$.fn.noo_upload = function( options ) {
		// -- set value default
			var defaults = {

				max_file_size: '2mb',
				runtimes : 'html5,flash,html4',
				multipart      : true,
				urlstream_upload : true,
				max_files      : 0,
				upload_enabled : true,
				multi_upload : false,
				url: nooUpload.url,
				delete_url: nooUpload.delete_url,
				flash_swf_url : nooUpload.flash_swf_url,
				resize : {}
				// resize : {
				// 	width : 320, 
				// 	height : 240, 
				// 	quality : 90
				// },

			};
		// -- 
			options = $.extend(defaults, options);

		// -- get value tag
			var flash_swf_url = options.flash_swf_url;
			var tag_thumb = options.tag_thumb;
			var thumb_preview = $('#' + tag_thumb);
			var input_name = options.input_name;
			var multi_upload = options.multi_upload;

		// -- Call wp plupload

			var uploader = new plupload.Uploader({
				browse_button : options.browse_button,
				file_data_name : 'aaiu_upload_file',
				multi_selection : options.multi_upload,
				url: options.url,

				flash_swf_url : flash_swf_url,
				filters : [
					{title : "extensions", extensions : "jpg,jpeg,gif,png"},
				],
				resize: options.resize,
				views: { thumb: true },
				init: {
					PostInit: function() {
						thumb_preview.innerHTML = '';
					},

					FilesAdded: function(up, files) {
						plupload.each(files, function(file) {
							// var co = co + '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
							// thumb_preview.html( co );
							if ( multi_upload === false ) {
								$('#' + options.browse_button).parent().find('.noo_upload-status').get(0).innerHTML = '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
							} else {
								$('#' + options.browse_button).parent().find('.noo_upload-status').get(0).innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>';
							}
						});

						// up.refresh(); // Reposition Flash/Silverlight
						uploader.start();
					},

					UploadProgress: function(up, file) {
						if( $('#' +file.id) ) {
							$('#' +file.id).find('b').get(0).innerHTML = '<span>' + file.percent + "%</span>";
						}
					},

					Error: function(up, err) {
						if ( multi_upload === false ) {
							thumb_preview.html("\nError #" + err.code + ": " + err.message);
						} else {
							thumb_preview.get(0).innerHTML += "\nError #" + err.code + ": " + err.message;
						}
					},

					FileUploaded: function(up, file, response) {
						var result = $.parseJSON(response.response);
						
						$('#' + file.id).remove();
						$('.jform-error',thumb_preview).remove();
						
						if (result.success) {
							 
		                    if ( multi_upload === false ) {
		                    	$('.image-upload-thumb',thumb_preview).remove()
		                    	thumb_preview.append(
		                    		'<div class="image-upload-thumb" data-id="' + result.image_id + '">' +
			                    	'<img width="150" src="' + result.image + '" />' + 
			                    	'<a class="delete-uploaded" data-fileid="' + result.image_id + '" href="#" title="' + nooUpload.remove_txt + '"><i class="fa fa-times-circle"></i></a>' +
			                    	'</div>'
			                    ); 
		                    } else {
			                    thumb_preview.append(
		                    		'<div class="image-upload-thumb" data-id="' + result.image_id + '">' +
			                    	'<img width="150" src="' + result.image + '" />' + 
			                    	'<a class="delete-uploaded" data-fileid="' + result.image_id + '" href="#" title="' + nooUpload.remove_txt + '"><i class="fa fa-times-circle"></i></a>' +
			                    	'</div>'
			                    );
		                	}
		                    
		                    var imageIds = [];
							
		                    thumb_preview.find( '.image-upload-thumb' ).each( function() {
								var imageId = $( this ).data( 'id' );
								imageIds.push(imageId)
							});
							
		                    thumb_preview.find('.noo-upload-value').val(imageIds.join(',')).attr('value',imageIds.join(','));
		                }
		            }
				}
			});

			uploader.init();
			
			thumb_preview.on('click', '.image-upload-thumb .delete-uploaded', function( e ) {
				e.preventDefault();
				var el = $(this);
				
				var data = {
					'attach_id':el.data('fileid')
				};
				
				el.parent('.image-upload-thumb').remove();
				
				$.post(nooUpload.delete_url, data);
				
				var imageIds = [];
				
                thumb_preview.find( '.image-upload-thumb' ).each( function() {
					var imageId = $( this ).data( 'id' );
					imageIds.push(imageId)
				});
                
                thumb_preview.find('.noo-upload-value').val(imageIds.join(',')).attr('value',imageIds.join(','));

                return false;
			});
	};

})(jQuery);