jQuery(function ($) {
	$(document).ready(function () {
		contact.initEventHandlers();
	});

	var contact = {
		initEventHandlers: function () {

			$('#final_button').bind('click', function (event) {

				contact.ContactFormSubmit();

			});

		},
		ContactFormSubmit: function () {

			var ajaxJSON = {
				'email': $('#final_email').val()
			};

			$.ajax({
				type: 'POST',
				url: ajaxquote.ajax_url,
				data: {
					action: 'omsgc_ajax',
					jason: ajaxJSON
				},
				success: function (response) {

					if (response.fragments) {
						$.each(response.fragments, function (key) {
							$(key)
								.addClass('updating')
								.fadeTo('400', '0.6')
								.block({
									message: null,
									overlayCSS: {
										opacity: 0.6
									}
								});
						});

						$.each(response.fragments, function (key, value) {
							$(key).replaceWith(value);
							$(key).stop(true).css('opacity', '1').unblock();
						});

						$(document.body).trigger('wc_fragments_loaded');
					}

					// $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash]);
					// $(document.body).trigger('wc_cart_button_updated');
					$(document.body).trigger('wc_fragment_refresh');
					// $("[name='update_cart']").trigger("click");



					//$('#ajax_return').text(response).val(response.fragments);
					// $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash]);

					// if (response.fragments) {
					// 	$.each(response.fragments, function (key) {
					// 		$(key)
					// 			.addClass('updating')
					// 			.fadeTo('400', '0.6')
					// 			.block({
					// 				message: null,
					// 				overlayCSS: {
					// 					opacity: 0.6
					// 				}
					// 			});
					// 	});

					// 	$.each(response.fragments, function (key, value) {
					// 		$(key).replaceWith(value);
					// 		$(key).stop(true).css('opacity', '1').unblock();
					// 	});

					// 	$(document.body).trigger('wc_fragments_loaded');
					// }

					//$("[name='update_cart']").trigger("click");
					//$(document.body).trigger('wc_cart_button_updated');
					//$(document.body).trigger('wc_fragment_refresh');
					//last resort hard resfresh works  :/
					//window.location.href=window.location.href;
				}
			})
		}
	}
});