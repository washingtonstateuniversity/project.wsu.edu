(function($, window){
	/**
	 * Handle the click action on the form submission button.
	 */
	function handle_click( e ) {
		e.preventDefault();

		var project_name = $('#project-name').val(),
			project_path = $('#project-path').val(),
			nonce        = $('#project-create-nonce').val();

		// Build the data for our ajax call
		var data = {
			action:       'submit_project_create_request',
			project_name: project_name,
			project_path: project_path,
			_ajax_nonce:  nonce
		};

		$('.project-create-form' ).hide();
		$('.project-loading' ).show();
		// Make the ajax call
		$.post( window.project_create_data.ajax_url, data, function( response ) {
			response = $.parseJSON( response );

			if ( response.success ) {
				$( '.project-create-form').html('');
				$( '.project-create-form' ).addClass('project-create-success');
				$( '.project-create-form').append( response.success );
				$( '.project-loading' ).hide();
				$( '.project-create-form' ).show();
			} else {
				console.log( response.error );
			}
		});
	}

	$( '#submit-project-create' ).on( 'click', handle_click );
}(jQuery,window));