<?php
namespace Apiki\Care\Helper;

if ( ! function_exists( 'add_action' ) ) {
	exit( 0 );
}

class Utils
{
	public static function get( $key, $default = '', $sanitize = 'esc_html' )
	{
		if ( ! isset( $_GET[ $key ] ) || empty( $_GET[ $key ] ) ) {
			return $default;
		}

		if ( is_array( $_GET[ $key ] ) ) {
			return $_GET[ $key ];
		}

		return self::sanitize_type( $_GET[ $key ], $sanitize );
	}

	public static function request( $key, $default = '', $sanitize = 'esc_html' )
	{
		if ( ! isset( $_REQUEST[ $key ] ) || empty( $_REQUEST[ $key ] ) ) {
			return $default;
		}

		return self::sanitize_type( $_REQUEST[ $key ], $sanitize );
	}

	public static function post( $key, $default = '', $sanitize = 'esc_html' )
	{
		if ( ! isset( $_POST[ $key ] ) || empty( $_POST[ $key ] ) ) {
			return $default;
		}

		if ( is_array( $_POST[ $key ] ) ) {
			return $_POST[ $key ];
		}

		return self::sanitize_type( $_POST[ $key ], $sanitize );
	}

	public static function sanitize_type( $value, $name_function )
	{
		if ( ! $name_function ) {
			return $value;
		}

		if ( ! is_callable( $name_function ) ) {
			return esc_html( $value );
		}

		return call_user_func( $name_function, $value );
	}

	public static function sanitize_html( $value )
	{
		return htmlspecialchars( $value, ENT_QUOTES, 'UTF-8' );
	}

	public static function get_default_format()
	{
		return get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
	}

	public static function get_site_reference()
	{
		$name = get_bloginfo( 'name' );

		if ( empty( $name ) ) {
			return get_site_url();
		}

		return $name;
	}

	public static function set_token_request()
	{
		$time = microtime();
		$site = get_site_url();

		update_option( 'apiki_wp_care_token', md5( "{$time}@{$site}" ) );
	}

	public static function get_token_request()
	{
		return get_option( 'apiki_wp_care_token' );
	}
}
