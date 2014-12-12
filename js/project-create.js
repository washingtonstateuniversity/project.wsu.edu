(function($, window){
	/**
	 * Cache the project creation form element for use throughout.
	 *
	 * @type {*|HTMLElement}
	 */
	var project_create_form = $('.project-create-form');

	/**
	 * Cache the spinner element for use throughout.
	 *
	 * @type {*|HTMLElement}
	 */
	var project_loading = $('.project-loading');

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

		project_create_form.hide();
		project_loading.show();

		// Make the ajax call
		$.post( window.project_create_data.ajax_url, data, function( response ) {
			response = $.parseJSON( response );

			if ( response.success ) {
				project_create_form.html('').addClass('project-create-success').append( response.success ).show();
				project_loading.hide();
			} else {
				$( '.project-create-error' ).remove();
				project_create_form.prepend('<p class="project-create-error">' + response.error + '</p>' ).show();
				project_loading.hide();
			}
		});
	}

	$( '#submit-project-create' ).on( 'click', handle_click );
}(jQuery,window));