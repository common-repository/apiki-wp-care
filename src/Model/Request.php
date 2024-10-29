<?php
namespace Apiki\Care\Model;

if ( ! function_exists( 'add_action' ) ) {
	exit( 0 );
}

use Exception;
use WP_Error;
use Apiki\Care\Core;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Request
{
	const RABBIT_GATEWAY_URL = 'https://rb-queue.apiki.com';
	const AUTH_TOKEN         = '519A873B9CC3EC01426CB6F1C4C1D2948BC97AF7';

	public function send( $data )
	{
		$args = array(
			'headers' => array( 'apiki-auth-token' => self::AUTH_TOKEN ),
			'body'    => wp_json_encode( $data )
		);

		$response = wp_remote_post( self::RABBIT_GATEWAY_URL, $args );

		if ( is_wp_error( $response ) ) {
			return array(
				'success' => false,
				'error'   => array(
					'type'    => 'wp_error',
					'message' => $response->get_error_message(),
				)
			);
		}

		return json_decode( wp_remote_retrieve_body( $response ), true );
	}
}
