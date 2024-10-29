<?php
namespace Apiki\Care\Model;

if ( ! function_exists( 'add_action' ) ) {
	exit( 0 );
}

use Apiki\Care\Helper\Utils;

class Report
{
	private $data     = array();
	private $fallback = false;

	public function __construct( $data = false )
	{
		if ( ! $data ) {
			$data = $this->get_option_data();
		}

		$this->data = $this->_get_filter_data( $data );
	}

	private function _get_filter_data( $data )
	{
		return array(
			'plugins' => $data['plugins'],
			'themes'  => $data['themes'],
			'system'  => $data['system'],
		);
	}

	public function get_option_data()
	{
		$option = get_option( 'apiki_wp_care_response' );

		if ( ! $option ) {
			$this->fallback = true;

			return array(
				'plugins' => array(),
				'themes'  => array(),
				'system'  => array(),
			);
		}

		return json_decode( $option, true );
	}

	public function set_viewed()
	{
		update_option( 'apiki_wp_care_response_viewed', 1 );
	}

	public function get_last_time()
	{
		$time = get_option( 'apiki_wp_care_response_time' );

		if ( ! $time ) {
			return false;
		}

		return date_i18n( Utils::get_default_format(), $time );
	}

	public function has_really_items()
	{
		if ( ! $this->fallback ) {
			return (bool) $this->data;
		}

		return false;
	}

	public function get_resume_vulnerabilities()
	{
		$plugins = $this->get_count_vulnerabilities( 'plugins' );
		$themes  = $this->get_count_vulnerabilities( 'themes' );
		$system  = $this->get_system_vulnerabilities() ? 1 : 0;

		return array(
			'plugins' => $plugins,
			'themes'  => $themes,
			'system'  => $system,
			'total'   => $plugins + $themes + $system,
		);
	}

	public function get_system_vulnerabilities()
	{
		$system = $this->data['system'];

		if ( ! isset( $system['vulnerabilities'] ) ) {
			return 0;
		}

		return count( $system['vulnerabilities'] );
	}

	public function get_count_vulnerabilities( $attr )
	{
		$errors = 0;

		if ( ! isset( $this->data[ $attr ] ) ) {
			return $errors;
		}

		foreach ( $this->data[ $attr ] as $key => $item ) {
			$errors += ( isset( $item['vulnerabilities'] ) && count( $item['vulnerabilities'] ) >= 1 ) ? 1 : 0;
		}

		return $errors;
	}

	public function get_plugins()
	{
		return $this->data['plugins'];
	}

	public function get_themes()
	{
		return $this->data['themes'];
	}

	public function get_system()
	{
		return $this->data['system'];
	}
}
