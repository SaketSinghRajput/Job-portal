
// jQuery(document).ready(function($) {
	
// 	// $('#noo_form').on('click', '.noo_import', function(event) {
// 	// 	event.stopPropagation();
// 	// 	event.preventDefault();
// 	// 	console.log('sds');
	
// 	// });
// 	$('#noo_form').noo_upload_file({ 
// 		id: 'upload_xml',
// 		notice: '.help-block'
// 	});

// });

// -- function upload file

(function ( $ ) {
	$.fn.noo_upload_file = function( opt ) {

		// -- set default value 
			var defaults = {

				id : 'upload_file',
				notice : 'notice',
				extensions : 'xml'

			}

		// --
			var option = $.extend( defaults, opt );
			
		this.on('change', 'input[type=file]#'+ option.id, function(event) {
			event.stopPropagation();
			event.preventDefault();
			var $this = this;
			// -- get file extensions
				var ext = $('#' + option.id).val().split('.').pop().toLowerCase();
			
			// covert extensions string to array
				var exts = (option.extensions).split(',');

			// -- processing file
				if ( $.inArray( ext, exts) == -1 )
					$(option.notice).html( 'Invalid extensions!' ).css({
						color: 'red',
					});
				else $(option.notice).html( '' );
				// console.log(this.value);

				// var formData = this.files[0];
				//  var reader = new FileReader();
		  //       reader.readAsText(formData);
		  //       reader.onload = function(e) {
		  //           // browser completed reading file - display it
		  //           alert(e.target.result);
		  //       };
		  		console.log(this);
				var data = {
					action: 'noo_processing',
					file: this.value,
					// contentType: false,
					// enctype: 'multipart/form-data',
					// processData: false,
				};

				jQuery.post(noo_ajax.ajax_url, data, function(response) {
					console.log(response);
				});
				// $.ajax({
				// 	url: noo_ajax.ajax_url,
				// 	'action': 'noo_test',
				// 	'data': formData,
				// 	contentType: false,
				//     enctype: 'multipart/form-data',
				//     processData: false,
				// })
				// .done(function( data ) {
				// 	console.log(data);
				// })
				// .fail(function() {
				// 	console.log("error");
				// })
				
		});


	}

}( jQuery ));

