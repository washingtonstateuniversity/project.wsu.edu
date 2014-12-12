<?php

class WSU_Projects_Theme {
	public function __construct() {
		add_shortcode( 'wsuwp_create_project', array( $this, 'create_project_display' ) );
		add_action( 'wp_ajax_submit_project_create_request', array( $this, 'handle_project_request' ), 10, 1 );
		add_action( 'wp_ajax_nopriv_submit_project_create_request', array( $this, 'handle_project_request' ), 10, 1 );
		add_filter( 'wsuwp_sso_create_new_user', array( $this, 'wsuwp_sso_create_new_user' ), 10, 1 );
		add_filter( 'wsuwp_sso_new_user_role',   array( $this, 'wsuwp_sso_new_user_role'   ), 10, 1 );
	}

	/**
	 * Enable the automatic creation of a new user if authentication is handled
	 * via WSU Network ID and no user exists.
	 *
	 * @return bool
	 */
	public function wsuwp_sso_create_new_user() {
		return true;
	}

	/**
	 * Set an automatically created user's role as subscriber.
	 *
	 * @return string New role for the new user.
	 */
	public function wsuwp_sso_new_user_role() {
		return 'subscriber';
	}

	/**
	 * Display a form when the shortcode is used on the home page to capture new project
	 * details and handle the processing of new requests.
	 *
	 * This should only be used on the front page.
	 *
	 * @return string HTML output.
	 */
	public function create_project_display() {
		if ( ! is_front_page() ) {
			return '';
		}

		ob_start();
		if ( is_user_logged_in() ) :
			wp_enqueue_script( 'project_create_request', get_stylesheet_directory_uri() . '/js/project-create.js', array( 'jquery' ), spine_get_script_version(), true );
			wp_localize_script( 'project_create_request', 'project_create_data', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
			?>
			<div class="project-loading" style="display: none; background-image: url(<?php echo get_stylesheet_directory_uri() . '/spinner.gif'; ?>);"></div>
			<div class="project-create-form">
				<input type="hidden" id="project-create-nonce" value="<?php echo esc_attr( wp_create_nonce( 'project-create-nonce' ) ); ?>" />
				<label for="project-name">What is your project name?</label>
				<input type="text" name="project_name" id="project-name" value="" />
				<label for="project-path" class="project-path-label">Choose a URL for your project:</label>
				<span class="project-pre-input">project.wsu.edu/</span><input type="text" name="project_path" id="project-path" value="" />
				<input type="submit" class="project-create" id="submit-project-create" value="Create">
			</div>
		<?php else : ?>
			<div class="project-auth-form">
				WSU Project sites can be created by anyone with a current WSU Network ID. Please <a href="<?php echo esc_url( wp_login_url( home_url() ) ); ?>">authenticate</a> to access the project creation form.
			</div>
		<?php endif;

		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	/**
	 * Handle AJAX requests from the home page to create new projects.
	 */
	public function handle_project_request() {
		if ( ! isset( $_POST['_ajax_nonce'] ) || ! wp_verify_nonce( $_POST['_ajax_nonce'], 'project-create-nonce' ) ) {
			echo json_encode( array( 'error' => 'There was a problem submitting your request.' ) );
			die();
		}

		if ( ! isset( $_POST['project_name'] ) || empty( sanitize_text_field( $_POST['project_name'] ) ) ) {
			echo json_encode( array( 'error' => 'Please enter a few words as a title for the project. It is possible the last attempt contained invalid characters.' ) );
			die();
		}

		if ( ! isset( $_POST['project_path'] ) || empty( sanitize_title( $_POST['project_path'] ) ) ) {
			echo json_encode( array( 'error' => 'Please enter a path for your project. This will appear as the second part of the URL and should not contain spaces or invalid characters.' ) );
			die();
		}

		if ( 'project.wsu.dev' === $_SERVER['HTTP_HOST'] ) {
			$project_domain = 'project.wsu.dev';
			$project_scheme = 'http://';
		} else {
			$project_domain = 'project.wsu.edu';
			$project_scheme = 'https://';
		}

		$project_path = sanitize_title( $_POST['project_path'] );
		$project_path = '/' . trailingslashit( $project_path );

		$user_id = get_current_user_id();
		$site_id = get_current_site()->id;

		$blog_id = wpmu_create_blog( $project_domain, $project_path, sanitize_text_field( $_POST['project_name'] ), $user_id, array(), $site_id );

		if ( is_wp_error( $blog_id ) ) {
			echo json_encode( array( 'error' => esc_attr( $blog_id->get_error_message() ) ) );
			die();
		}

		$project_url = esc_url( $project_scheme . $project_domain . $project_path );
		$success_message = '<p class="success">A new WSU Project site has been configured!</p><p class="success">Start communicating at <a href="' . $project_url . '">' . $project_url . '</a>.</p><p>New collaborators can be added to the project through its <a href="' . $project_url . 'wp-admin/">administration interface</a>.</p>';
		$success_message .= '<p class="success"><a href="' . esc_url( home_url() ) . '">Create</a> another one?</p>';
		echo json_encode( array( 'success' => $success_message ) );
		die();
	}
}
new WSU_Projects_Theme();