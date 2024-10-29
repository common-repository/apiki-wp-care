<?php
namespace Apiki\Care\Controller;

if ( ! function_exists( 'add_action' ) ) {
	exit( 0 );
}

if ( ! session_id() ) {
	@session_start();
}

use Apiki\Care\Core;
use Apiki\Care\Model;
use Apiki\Care\Helper\Utils;
use Apiki\Care\Model\Report;

class Security
{
	public $model;
	public $request;

	const MENU_POSITION = '98.35358';

	public function __construct()
	{
		$this->model   = new Model\Security();
		$this->request = new Model\Request();

		add_action( 'admin_init', array( &$this, 'verify_vulnerabilities' ) );
		add_action( 'activated_plugin', array( &$this, 'verify_all' ) );
		add_action( 'deactivated_plugin', array( &$this, 'verify_deactivated_plugin' ) );
		add_action( 'after_switch_theme', array( &$this, 'verify_all' ) );
		add_action( 'deleted_plugin', array( &$this, 'verify_deleted_plugin' ), 10, 2 );
		add_action( 'admin_notices', array( &$this, 'show_notices' ) );
		add_action( 'admin_menu', array( &$this, 'add_menu_controls' ) );
		add_action( 'admin_menu', array( &$this, 'add_menu_bubble' ) );
		add_action( 'admin_bar_menu', array( &$this, 'admin_bar_menu' ), 95 );
	}

	public function request_send( $data, $show_success = false )
	{
		$messages = new Model\Messages();
		$status   = $this->request->send( $data );

		if ( ! $status['success'] ) {
			$messages->error( $status['error']['message'] );
			return;
		}

		if ( $show_success ) {
			$messages->success( esc_html__( 'A new report was requested, soon we will have its result.', Core::SLUG ) );
		}
	}

	public function verify_deleted_plugin( $plugin, $deleted )
	{
		if ( $deleted ) {
			$this->request_send( $this->model->get_data_removed_plugin( $plugin ) );
		}
	}

	public function verify_deactivated_plugin( $plugin )
	{
		$this->request_send( $this->model->get_data_deactivated_plugin( $plugin ) );
	}

	public function verify_vulnerabilities()
	{
		if ( ! Utils::get( 'care-verify' ) ) {
			return;
		}

		$this->request_send( $this->model->get_data(), true );
	}

	public function verify_plugin( $plugin, $network_activation )
	{
		$info = $this->model->get_single_plugin( $plugin );

		if ( $info && strpos( $plugin, Core::SLUG ) === false ) {
			$this->request_send( $info );
		}
	}

	public function verify_theme()
	{
		$info = $this->model->get_single_theme( get_stylesheet() );

		if ( $info ) {
			$this->request_send( $info );
		}
	}

	public function verify_all()
	{
		$this->request_send( $this->model->get_data() );
	}

	public function show_notices()
	{
		$viewed   = get_option( 'apiki_wp_care_response_viewed' );
		$response = get_option( 'apiki_wp_care_response' );
		$pages    = array(
			Core::SLUG . '-stats-checker',
			Core::SLUG . '-stats-dashboard',
			Core::SLUG . '-stats-credits',
		);

		if ( in_array( Utils::get( 'page' ), $pages, true ) ) {
			return;
		}

		if ( $response && ! $viewed ) :

		?>
		<div class="notice notice-info is-dismissible">
		    <p>
				<?php esc_html_e( '[WP Care] We have checked the Security Alerts about this WordPress.', Core::SLUG ); ?>
		    	<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . Core::SLUG . '-stats-checker' ) ); ?>">
					<?php esc_html_e( 'Check out the results.', Core::SLUG ); ?>
				</a>
		    </p>
		</div>
		<?php

		endif;
	}

	public function add_menu_controls()
	{
		add_menu_page(
			esc_html__( 'WP Care', Core::SLUG ),
			esc_html__( 'WP Care', Core::SLUG ),
			'manage_options',
			Core::SLUG . '-stats-dashboard',
			array( 'Apiki\Care\View\Dashboard', 'render_page' ),
			$this->get_icon_svg(),
			self::MENU_POSITION
		);

		add_submenu_page(
			Core::SLUG . '-stats-dashboard',
			esc_html__( 'Dashboard', Core::SLUG ),
			esc_html__( 'Dashboard', Core::SLUG ),
			'manage_options',
			Core::SLUG . '-stats-dashboard',
			array( 'Apiki\Care\View\Dashboard', 'render_page' )
		);

		add_submenu_page(
			Core::SLUG . '-stats-dashboard',
			esc_html__( 'Security Alerts', Core::SLUG ),
			esc_html__( 'Security Alerts', Core::SLUG ),
			'manage_options',
			Core::SLUG . '-stats-checker',
			array( 'Apiki\Care\View\Security', 'render_stats' )
		);

		add_submenu_page(
			Core::SLUG . '-stats-dashboard',
			esc_html__( 'Credits', Core::SLUG ),
			esc_html__( 'Credits', Core::SLUG ),
			'manage_options',
			Core::SLUG . '-stats-credits',
			array( 'Apiki\Care\View\Credit', 'render_page' )
		);

	}

	public function admin_bar_menu()
	{
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		global $wp_admin_bar;

		$user_is_admin_or_networkadmin = current_user_can( 'manage_options' );

		if ( ! $user_is_admin_or_networkadmin && is_multisite() ) {
			$user_is_admin_or_networkadmin = ( $options['access'] === 'superadmin' && is_super_admin() );
		}

		if ( ! $user_is_admin_or_networkadmin ) {
			return;
		}

		$awpc_url = get_admin_url( null, 'admin.php?page=' . Core::SLUG . '-stats-checker' );

		$report  = new Report();
		$resume  = $report->get_resume_vulnerabilities();
		$counter = $resume['total'];

		$_icon = sprintf( '<div id="awpc-ab-icon" class="ab-item awpc-logo" style="background-image: url(%s) !important;"><span class="screen-reader-text">%s</span></div>',
			$this->get_icon_svg(),
			esc_html__( 'Apiki WP Care', Core::SLUG )
		);

		$_issue_counter = sprintf( '<span aria-hidden="true">%d</span>', $counter );
		$_issue_notes   = sprintf( _n( '<span class="screen-reader-text">%d security issue</span>', '<span class="screen-reader-text">%d security issues</span>', $counter, Core::SLUG ), $counter );
		$_issue         = sprintf( '<div class="wp-core-ui wp-ui-notification awpc-issue-counter">%s%s</div>', $_issue_counter, $_issue_notes );

		$admin_bar_title = ( $counter == 0 ) ? $_icon : $_icon . $_issue;

		$wp_admin_bar->add_menu(
			array(
				'id'    => 'awpc-admin-bar-menu',
				'title' => $admin_bar_title,
				'href'  => $awpc_url,
				'meta'  => array( 'tabindex' => 0 ),
			)
		);
	}

	public function add_menu_bubble()
	{
		global $menu;

		$report  = new Report();
		$resume  = $report->get_resume_vulnerabilities();
		$counter = $resume['total'];

		if ( $counter >= 1 && isset( $menu[ self::MENU_POSITION ] ) ) {
			$menu[ self::MENU_POSITION ][0] .= ' ' . $this->get_html_counter( $counter );
		}
	}

	public function get_html_counter( $counter )
	{
		return sprintf( '<span class="update-plugins count-%1$d"><span class="plugin-count" aria-hidden="true">%1$d</span></span>', $counter );
	}

	public function get_icon_svg()
	{
		return 'data:image/svg+xml;base64,PHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgdmlld0JveD0iMCAwIDEwMjQgMTAyNCI+PHRpdGxlPjwvdGl0bGU+PGcgaWQ9Imljb21vb24taWdub3JlIj48L2c+PHBhdGggZmlsbD0iIzgyODc4YyIgZD0iTTg2Ny45OTMgMTU0LjAyMmMtMzguMzI3LTI5LjQyLTgzLjgxNS00OC4zOTItMTMxLjgxNy01NC41ODYtODIuNDU1LTEwLjgzOC0xNjMuMTczIDE0LjMyMy0yMjQuMTQ1IDY4LjkxLTYwLjk3Mi01NS4xNjctMTQyLjA3NC04MC4zMjgtMjI0LjkxOS02OS4yOTQtNDguMDA0IDYuMzg2LTkzLjY4MiAyNS41NS0xMzIuMjAyIDU1LjM2LTY5LjI5NCA1NC4wMDUtMTA5LjE2NyAxMzQuNzE4LTEwOC45NzYgMjIxLjgyMSAwIDc0LjUyMyAyOS4yMjkgMTQ0Ljc4MyA4Mi4wNzIgMTk3LjYyNmwzMzQuNjY3IDMzNC42NjdjMTIuNzc2IDEyLjc3NiAyOS40MiAxOC45NjcgNDYuMDY4IDE4Ljk2N3MzMy4yOS02LjM4NiA0Ni4wNjgtMTguOTY3bDUxLjg3My01MS44NzNjMTMuNTUxLTEzLjU1MSAxMy41NTEtMzUuNjE1IDAtNDkuMzU3LTEzLjU1MS0xMy41NTEtMzUuNjE1LTEzLjU1MS00OS4zNTcgMGwtNDguNTg0IDQ4LjU4NC0zMzEuMzc4LTMzMS41N2MtMzkuNjgyLTM5LjY4Mi02MS41NTMtOTIuMzI4LTYxLjc0Ni0xNDguNDYgMC02NS4yMzEgMjkuODA5LTEyNi4wMDcgODIuMDcyLTE2Ni42NTkgMjguNjQ4LTIyLjI2MSA2Mi43MTYtMzYuNTg1IDk4LjUyMi00MS4yMjggNjYuMTk3LTguNzExIDEzMC44NDggMTIuOTcgMTc3LjQ5NiA1OS44MWwzOC4zMjcgMzguMzI3IDM3LjkzOC0zNy45MzhjNDYuNDU3LTQ2LjY0OSAxMTEuMTAzLTY4LjMyOSAxNzcuMTA4LTU5LjYxOSAzNS44MDkgNC42NDMgNjkuNjgzIDE4Ljc3NSA5OC4zMzEgNDAuNjQ3IDU0LjU4NiA0Mi4wMDEgODQuNzggMTA1LjI5OCA4Mi44NDUgMTczLjQzLTEuNTQ4IDUzLjYxNi0yNC45NjkgMTA1LjY4Ny02NS42MTYgMTQ2LjUyNWwtMTc2LjUyNyAxNzYuMzM0Yy0xMy41NTEgMTMuNTUxLTEzLjU1MSAzNS42MTUgMCA0OS4zNTcgMTMuNTUxIDEzLjU1MSAzNS42MTUgMTMuNTUxIDQ5LjM1NyAwbDE3Ni41MjctMTc2LjUyN2M1My40MjQtNTMuNDI0IDg0LjAwNi0xMjIuMTM3IDg1Ljk0Mi0xOTMuNzU2IDIuNzA4LTkwLjc4Mi0zNy4zNTctMTc0Ljc4OC0xMDkuOTQ1LTIzMC41MzJ6Ij48L3BhdGg+PC9zdmc+';
	}
}
