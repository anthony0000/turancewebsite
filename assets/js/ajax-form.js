$(function() {

	// Get the form.
	var form = $('#contact-form');
	if (!form.length) {
		return;
	}

	// Get the messages div.
	var formMessages = $('.ajax-response');

	function setFormMessage(type, text) {
		formMessages
			.removeClass('d-none alert alert-success alert-danger success error')
			.addClass('alert ' + (type === 'success' ? 'alert-success success' : 'alert-danger error'))
			.text(text);
	}

	// Set up an event listener for the contact form.
	$(form).submit(function(e) {
		// Stop the browser from submitting the form.
		e.preventDefault();

		// Serialize the form data.
		var formData = $(form).serialize();

		// Submit the form using AJAX.
		$.ajax({
			type: $(form).attr('method'),
			url: $(form).attr('action'),
			data: formData,
			headers: {
				'Accept': 'application/json'
			}
		})
		.done(function(response) {
			setFormMessage('success', response.message || 'Thanks for reaching out. We have received your message.');
			form.trigger('reset');
			form.find('select').prop('selectedIndex', 0).trigger('change');
		})
		.fail(function(data) {
			var message = 'Oops! An error occured and your message could not be sent.';

			if (data.responseJSON && data.responseJSON.errors) {
				message = Object.values(data.responseJSON.errors)
					.flat()
					.join(' ');
			} else if (data.responseJSON && data.responseJSON.message) {
				message = data.responseJSON.message;
			} else if (data.responseText !== '') {
				message = data.responseText;
			}

			setFormMessage('error', message);
		});
	});

});
