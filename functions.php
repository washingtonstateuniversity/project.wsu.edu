<?php

class WSU_Projects_Theme {
	public function __construct() {
		add_shortcode( 'wsuwp_create_project', array( $this, 'create_project_display' ) );
	}

	public function create_project_display() {
		ob_start();
		if ( is_user_logged_in() ) : ?>
			<div class="project-create-form">
				<label for="project-name">What is your project name?</label>
				<input type="text" name="project_name" id="project-name" value="" />
				<label for="project-path" class="project-path-label">Choose a URL for your project:</label>
				<span class="project-pre-input">project.wsu.edu/</span><input type="text" name="project_path" id="project-path" value="" />
				<input type="submit" class="project-create" value="Create">
			</div>
		<?php else : ?>
			<div class="project-auth-form">
				You must authenticate first.
			</div>
		<?php endif;

		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}
}
new WSU_Projects_Theme();