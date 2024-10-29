<?php
namespace Apiki\Care\Model;

if ( ! function_exists( 'add_action' ) ) {
	exit( 0 );
}

use Apiki\Care\Helper\Utils;

class Security
{
	public $plugins = array();
	public $themes  = array();

	public function __construct()
	{
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$this->plugins = get_plugins();
		$this->themes  = wp_get_themes( array( 'allowed' => true ) );
	}

	public function get_data()
	{
		$data['plugins'] = $this->_prepare_plugins( $this->plugins );
		$data['themes']  = $this->get_themes();
		$data['system']  = $this->get_system_info();

		return $data;
	}

	public function get_data_deactivated_plugin( $plugin_disabled )
	{
		$data['plugins'] = $this->_prepare_plugins( $this->plugins, $plugin_disabled );
		$data['themes']  = $this->get_themes();
		$data['system']  = $this->get_system_info();

		return $data;
	}

	public function get_data_removed_plugin( $plugin_removed )
	{
		$data['plugins'] = $this->_prepare_plugins( $this->plugins, false, $plugin_removed );
		$data['themes']  = $this->get_themes();
		$data['system']  = $this->get_system_info();

		return $data;
	}

	public function get_single_plugin( $plugin_slug )
	{
		$path = WP_PLUGIN_DIR . '/' . $plugin_slug;
		$data = get_plugin_data( $path, false, false );

		if ( empty( $data ) ) {
			return;
		}

		return array(
			'plugins' => $this->_prepare_plugins( [ $data ] ),
			'system'  => $this->get_system_info(),
		);
	}

	public function get_single_theme( $theme_slug )
	{
		$theme_object = wp_get_theme( $theme_slug );

		return array(
			'themes' => $this->_prepare_themes( [ $theme_object ] ),
			'system' => $this->get_system_info(),
		);
	}

	public function get_themes()
	{
		if ( empty( $this->themes ) && is_multisite() ) {
			$this->themes = wp_get_themes( array( 'allowed' => true ) );
		}

		return $this->_prepare_themes( $this->themes );
	}

	public function get_system_info()
	{
		global $wpdb, $wp_version;

		return array(
			'wp_version'       => $wp_version,
			'site_url'         => get_site_url( null, '/' ),
			'multisite'        => ( is_multisite() ) ? 'yes' : 'no',
			'database_version' => $wpdb->get_var( "SELECT VERSION();" ),
			'os'               => php_uname(),
			'php_version'      => phpversion(),
			'language'         => get_locale(),
			'ssl'              => is_ssl() ? 'yes' : 'no',
			'token'            => Utils::get_token_request(),
		);
	}

	private function _prepare_plugins( $plugins, $plugin_disabled = false, $plugin_removed = false )
	{
		if ( empty( $plugins ) ) {
			return array();
		}

		foreach ( $plugins as $slug => $plugin ) {
			if ( ! (bool) $plugin['TextDomain'] ) {
				continue;
			}

			$prepared['name']              = $plugin['Name'];
			$prepared['plugin_uri']        = Utils::sanitize_html( $plugin['PluginURI'] );
			$prepared['version']           = $plugin['Version'];
			$prepared['description']       = Utils::sanitize_html( $plugin['Description'] );
			$prepared['author']            = Utils::sanitize_html( $plugin['Author'] );
			$prepared['author_uri']        = Utils::sanitize_html( $plugin['AuthorURI'] );
			$prepared['textdomain']        = $plugin['TextDomain'];
			$prepared['network']           = $plugin['Network'];
			$prepared['active']            = $this->_is_active_plugin( $slug, $plugin_disabled );
			$prepared['remove']            = $this->_is_remove_plugin( $slug, $plugin_removed );
			$data[ $plugin['TextDomain'] ] = $prepared;
		}

		return $data;
	}

	private function _prepare_themes( $themes )
	{
		if ( empty( $themes ) ) {
			return array();
		}

		foreach ( $themes as $key => $theme ) {
			$data[ $theme->template ] = array(
				'name'        => $theme->Name,
				'theme_uri'   => Utils::sanitize_html( $theme->ThemeURI ),
				'version'     => $theme->Version,
				'description' => Utils::sanitize_html( $theme->Description ),
				'author'      => Utils::sanitize_html( $theme->Author ),
				'author_uri'  => Utils::sanitize_html( $theme->AuthorURI ),
				'template'    => $theme->Template,
				'textdomain'  => $theme->TextDomain,
				'active'      => $this->_is_theme_active( $key ),
			);
		}

		return $data;
	}

	private function _is_theme_active( $slug )
	{
		return ( $slug == get_stylesheet() ) ? true : false;
	}

	private function _is_remove_plugin( $plugin_file, $plugin_removed )
	{
		return ( $plugin_file == $plugin_removed );
	}

	private function _is_active_plugin( $plugin_file, $plugin_disabled = false )
	{
		if ( $plugin_file == $plugin_disabled ) {
			return false;
		}

		if ( is_multisite() && is_plugin_active_for_network( $plugin_file ) ) {
			return true;
		}

		return is_plugin_active( $plugin_file ) ? true : false;
	}
}
