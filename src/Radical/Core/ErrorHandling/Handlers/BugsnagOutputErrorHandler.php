<?php
namespace Radical\Core\ErrorHandling\Handlers;

use Radical\Core\ErrorHandling\Errors\Internal\ErrorBase;
use Radical\Core\ErrorHandling\Errors\Internal\ErrorException;

/**
 * Class BugsnagOutputErrorHandler
 * @package Radical\Core\ErrorHandling\Handlers
 */
class BugsnagOutputErrorHandler extends ErrorHandlerBase {
	const CLI_START = "[%s]%s\n";
	const CLI_HANDLER = '\Radical\Core\ErrorHandling\Handlers\BugsnagCLIOutputErrorHandler';
	const WEB_HANDLER = '\Radical\Core\ErrorHandling\Handlers\BugsnagWebOutputErrorHandler';
	private $handler;

    /**
     * @var \Bugsnag_Client
     */
    private $client;
	
	function __construct(\Bugsnag_Client $client, $is_cli = null){
		if($is_cli === null){
			$is_cli = \Radical\Core\Server::isCLI();
		}
		
		if($is_cli)
			$c = self::CLI_HANDLER;
		else
			$c = self::WEB_HANDLER;

        $this->client = $client;
		$this->handler = new $c($this);
	}

    /**
     * @return \Bugsnag_Client
     */
    function getClient(){
        return $this->client;
    }
	
	function error(ErrorBase $error) {
		return $this->handler->error($error);
	}
	function exception(ErrorException $error){
		return $this->handler->exception($error);
	}
}