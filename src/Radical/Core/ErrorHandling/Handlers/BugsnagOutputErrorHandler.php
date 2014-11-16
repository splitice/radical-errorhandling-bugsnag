<?php
namespace Radical\Core\ErrorHandling\Handlers;

use Radical\Core\ErrorHandling\Errors\Internal\ErrorBase;
use Radical\Core\ErrorHandling\Errors\Internal\ErrorException;

class BugsnagOutputErrorHandler extends ErrorHandlerBase {
	const CLI_START = "[%s]%s\n";
	const CLI_HANDLER = '\Radical\Core\ErrorHandling\Handlers\BugsnagCLIOutputErrorHandler';
	const WEB_HANDLER = '\Radical\Core\ErrorHandling\Handlers\BugsnagWebOutputErrorHandler';
	private $handler;
	
	function __construct($is_cli = null){
		if($is_cli === null){
			$is_cli = \Radical\Core\Server::isCLI();
		}
		
		if($is_cli)
			$c = self::CLI_HANDLER;
		else
			$c = self::WEB_HANDLER;
		
		$this->handler = new $c;
	}
	
	function error(ErrorBase $error) {
		return $this->handler->error($error);
	}
	function exception(ErrorException $error){
		return $this->handler->exception($error);
	}
}