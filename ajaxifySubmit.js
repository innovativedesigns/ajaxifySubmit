(function($) {

	$.fn.ajaxifySubmit = function(options) {

		var settings = $.extend({
			submitButtonSelector : '#submit',
			waitMessage : 'Please wait...',
		}, options);

		var $this = this;
		var errMsg = $('<div class="err-msg"></div>');
		var errWrap = $('<div class="err-wrap"></div>');
		errWrap.append(errMsg);
		var okMsg = $('<div class="ok-msg"></div>');
		var submitButton = $(settings.submitButtonSelector);

		submitButton.after(okMsg);
		$('body').append(errWrap);


		errWrap.click(function() {
			$(this).removeClass('show');
		});

		$this.submit(function() {

			// change the submit button text
			var submitText = submitButton.html();
			submitButton.attr('disabled','disabled').html(settings.waitMessage);

			$.post($this.attr('action'), $this.serialize(), function(data) {
					submitButton.removeAttr('disabled').html(submitText);

					if ( data.status === "OK" ) {
						$this[0].reset();
						errMsg.html('');
						okMsg.html('Your message has been sent!');
						setTimeout(function() { okMsg.html(''); }, 6000);
					} else {
						errMsg.html(
								'<p>Please fill in the following required fields:</p>'+
								'<ul><li>'+data.messages.join('</li><li>')+'</li></ul>'
							);
						errWrap.addClass('show');
					}
				}, "json")
				.fail(function(jqXHR, textStatus, errorThrown) {
					errMsg.html('<p>There was an error sending the message.  Please try again.</p>');
					errWrap.addClass('show');
					submitButton.removeAttr('disabled').html(submitText);
				});

			return false;
		});

		return this;
	};

}(jQuery));

$(function() {
	$('[data-ajaxify-submit]').each(function() {
		$(this).ajaxifySubmit();
	});
});
