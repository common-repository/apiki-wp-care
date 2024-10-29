<?php
namespace Apiki\Care\Model;

if ( ! function_exists( 'add_action' ) ) {
	exit( 0 );
}

use Plasticbrain\FlashMessages\FlashMessages;
use Apiki\Care\Helper\Utils;

class Messages extends FlashMessages
{
	protected $msgWrapper = "<div class='%s'><p>%s</p></div>\n";

	protected $closeBtn = '';

	protected $msgCssClass = 'awpc-alert notice is-dismissible';

	protected $cssClassMap = [
		self::INFO    => 'notice-info',
		self::SUCCESS => 'notice-success',
		self::WARNING => 'notice-warning',
		self::ERROR   => 'notice-error',
	];
}
