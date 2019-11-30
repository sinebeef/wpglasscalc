jQuery(function ($) {
	$(document).ready(function () {
		contact.initEventHandlers();
	});

	var contact = {
		initEventHandlers: function () {

			$('#final_add').bind('click', function (event) {
				contact.ContactFormSubmit();
			});
		},
		ContactFormSubmit: function () {

			var ajaxJSON = {
				'gcheight': $('#gc_height').val(),
				'gcwidth': $('#gc_width').val(),
				'gcsize': $('#gc_size').val(),
				'gctype': $('#gc_type').val(),
				'gcholes': $('#gc_holes').val(),
				'gccuts': $('#gc_cuts').val()
			};

			$.ajax({
				type: 'POST',
				url: ajaxquote.ajax_url,
				data: {
					action: 'omsgc_ajax',
					jason: ajaxJSON
				},
				success: function (response) {
					console.log(response);
					$('#ajax_return').text(response);
					$(document.body).trigger('wc_fragment_refresh');

				}
			})
		}
	}





});