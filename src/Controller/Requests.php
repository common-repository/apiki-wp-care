<?php
namespace Apiki\Care\Controller;

if ( ! function_exists( 'add_action' ) ) {
	exit( 0 );
}

use Apiki\Care\Helper\Utils;

class Requests
{
	public function __construct()
	{
		add_action( 'init', array( &$this, 'handle_server_response' ) );
	}

	public function handle_server_response()
	{
		if ( ! $this->_is_valid_request() ) {
			return;
		}

		$response = str_replace( '\\', '', $_REQUEST['report'] );

		update_option( 'apiki_wp_care_response', $response );
		update_option( 'apiki_wp_care_response_time', current_time( 'timestamp' ) );
		delete_option( 'apiki_wp_care_response_viewed' );
	}

	private function _is_valid_request()
	{
		$token = Utils::get_token_request();

		if ( $_SERVER['REQUEST_METHOD'] != 'POST' || ! isset( $_SERVER['HTTP_WP_SAFE'] ) ) {
			return false;
		}

		if ( $_SERVER['HTTP_WP_SAFE'] != $token ) {
			return false;
		}

		if ( ! isset( $_REQUEST['report'] ) ) {
			return false;
		}

		return true;
	}
}
