<?php
/*
	Plugin Name: WP Care
	Version: 0.3.4
	Author: Apiki
	Plugin URI: https://apiki.com/produtos/apiki-wp-care/?utm_source=plugin_wp_care&utm_medium=description&utm_campaign=plugin_wp_care
	Author URI: https://apiki.com
	Text Domain: apiki-wp-care
	Domain Path: /languages
	License: GPLv2
	Description: Your ally to keep your WordPress installation healthy, safe and performative.
*/

if ( ! function_exists( 'add_action' ) ) {
	exit( 0 );
}

use Apiki\Care\Core;

require_once __DIR__ . '/vendor/autoload.php';

$core = new Core( __FILE__ );

register_activation_hook( __FILE__, array( $core, 'activate' ) );
