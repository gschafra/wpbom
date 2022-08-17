jQuery( document ).ready( function( $ ) {
	$('button.wpbom-update-cpe-update').click( function( e ) {
		e.preventDefault();
		var nonce = $(this).data('nonce');
		alert(nonce);
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
