jQuery( document ).ready( function( $ ) {
	$('button.wpbom-update-cpe-update').click( function( e ) {
		// $('button.wpbom-update-cpe-update .custom-loader').removeClass('hidden');
		e.preventDefault();
		var nonce = $(this).data('nonce');
		alert('Nonce: ' + nonce);
		var data = {
			action: 'wpbom_update_cpe_dict',
			_ajax_nonce: nonce
		};
		$.post(ajaxurl, data, function(response) {
			alert("Response: " + response);
		});
		/*var $this = $(this);
		var $form = $this.closest('form');
		var $spinner = $this.find('.spinner');
		var $message = $form.find('.wpbom-message');
		$spinner.show();
		$message.hide();
		$.post(
			$form.attr('action'),
			$form.serialize(),
			function( response ) {
				$spinner.hide();
				$message.html( response.message ).show();
			}
		);*/
	});
});
