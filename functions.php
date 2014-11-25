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
			<style>
				.project-create-form {
					background: none;
					padding: 30px 15px;
					width: 88%;
					height: 210px;
				}

				.project-create-form label, .project-create-form input, .project-pre-input {
					float: left;
				}

				.project-pre-input, .project-create-form label {
					clear: left;
				}

				.project-create-form label {
					font-size: 2.1rem;
					line-height: 2.1rem;
					margin-bottom: 11px;
					color: #fafafa;
					font-weight: 300;
				}

				#project-name {
					clear: left;
					height: 46px;
					line-height: 1;
					font-size: 1.5rem;
					width: 585px;
				}

				.project-path-label {
					margin-top: 20px;
				}

				#project-path, .project-pre-input {
					display: block;
					height: 46px;
					line-height: 1;
					font-size: 1.2rem;
				}

				#project-path {
					width: 280px;
				}
				.project-pre-input {
					line-height: 3rem;
					padding-right: 6px;
					color: #ebebeb;
					font-weight: 300;
					letter-spacing: 1px;
				}

				.project-create {
					height: 46px;
					font-size: 1.2rem;
					margin-left: 10px;
					width: 135px;

				}
			</style>
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